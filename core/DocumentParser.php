<?php
// core/DocumentParser.php — Extractor nativo ultraligero para Word, Excel y PDF
declare(strict_types=1);

class DocumentParser {

    /**
     * Extrae el contenido relevante de un archivo según su extensión.
     */
    public static function parse(string $filePath, string $originalName = ''): array {
        if (!file_exists($filePath)) {
            throw new InvalidArgumentException("El archivo no existe en la ruta proporcionada.");
        }

        $ext = strtolower(pathinfo($originalName ?: $filePath, PATHINFO_EXTENSION));

        switch ($ext) {
            case 'docx':
                return [
                    'tipo' => 'texto',
                    'formato' => 'docx',
                    'contenido' => self::parseDocx($filePath),
                    'mime' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                    'tamano' => filesize($filePath)
                ];

            case 'xlsx':
                return [
                    'tipo' => 'texto',
                    'formato' => 'xlsx',
                    'contenido' => self::parseXlsx($filePath),
                    'mime' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    'tamano' => filesize($filePath)
                ];

            case 'xls':
            case 'csv':
            case 'txt':
                return [
                    'tipo' => 'texto',
                    'formato' => $ext,
                    'contenido' => self::parseTextOrCsv($filePath),
                    'mime' => 'text/plain',
                    'tamano' => filesize($filePath)
                ];

            case 'pdf':
                return [
                    'tipo' => 'pdf_base64',
                    'formato' => 'pdf',
                    'contenido' => base64_encode(file_get_contents($filePath)),
                    'mime' => 'application/pdf',
                    'tamano' => filesize($filePath)
                ];

            default:
                throw new InvalidArgumentException("Formato no soportado (.{$ext}). Formatos permitidos: .xlsx, .docx, .pdf, .csv, .txt");
        }
    }

    /**
     * Parsea un archivo Word (.docx) leyendo directamente word/document.xml con ZipArchive
     */
    public static function parseDocx(string $filePath): string {
        $zip = new ZipArchive();
        if ($zip->open($filePath) !== true) {
            throw new RuntimeException("No se pudo abrir el archivo Word (.docx). Puede estar dañado.");
        }

        $xmlIndex = $zip->locateName('word/document.xml');
        if ($xmlIndex === false) {
            $zip->close();
            throw new RuntimeException("Estructura de Word no válida (word/document.xml no encontrado).");
        }

        $xmlContent = $zip->getFromIndex($xmlIndex);
        $zip->close();

        if (empty($xmlContent)) {
            return '';
        }

        $dom = new DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadXML($xmlContent, LIBXML_NOENT | LIBXML_XINCLUDE | LIBXML_NOERROR | LIBXML_NOWARNING);
        libxml_clear_errors();

        $xpath = new DOMXPath($dom);
        $xpath->registerNamespace('w', 'http://schemas.openxmlformats.org/wordprocessingml/2006/main');

        $output = [];
        $body = $xpath->query('//w:body')->item(0);
        if (!$body) {
            return strip_tags(str_replace(['</w:p>', '</w:tr>'], ["\n", "\n"], $xmlContent));
        }

        foreach ($body->childNodes as $node) {
            if ($node->nodeName === 'w:p') {
                $pText = self::getNodeText($node, $xpath);
                if (trim($pText) !== '') {
                    $output[] = trim($pText);
                }
            } elseif ($node->nodeName === 'w:tbl') {
                $tableRows = [];
                $rows = $xpath->query('.//w:tr', $node);
                foreach ($rows as $tr) {
                    $cells = $xpath->query('.//w:tc', $tr);
                    $rowCells = [];
                    foreach ($cells as $tc) {
                        $cellText = self::getNodeText($tc, $xpath);
                        $rowCells[] = trim($cellText);
                    }
                    if (!empty(array_filter($rowCells))) {
                        $tableRows[] = '| ' . implode(' | ', $rowCells) . ' |';
                    }
                }
                if (!empty($tableRows)) {
                    $output[] = "\n" . implode("\n", $tableRows) . "\n";
                }
            }
        }

        return implode("\n", $output);
    }

    private static function getNodeText(DOMNode $node, DOMXPath $xpath): string {
        $textParts = [];
        $runs = $xpath->query('.//w:t', $node);
        foreach ($runs as $t) {
            $textParts[] = $t->nodeValue;
        }
        return implode('', $textParts);
    }

    /**
     * Parsea un archivo Excel (.xlsx) con múltiples hojas, tablas y resolución de fechas seriales
     */
    public static function parseXlsx(string $filePath): string {
        $zip = new ZipArchive();
        if ($zip->open($filePath) !== true) {
            throw new RuntimeException("No se pudo abrir el archivo Excel (.xlsx).");
        }

        // 1. Cargar cadenas compartidas (sharedStrings)
        $sharedStrings = [];
        $ssIndex = $zip->locateName('xl/sharedStrings.xml');
        if ($ssIndex !== false) {
            $ssXml = $zip->getFromIndex($ssIndex);
            $sDom = new DOMDocument();
            libxml_use_internal_errors(true);
            $sDom->loadXML($ssXml, LIBXML_NOENT | LIBXML_XINCLUDE | LIBXML_NOERROR | LIBXML_NOWARNING);
            libxml_clear_errors();
            $sXpath = new DOMXPath($sDom);
            $sXpath->registerNamespace('x', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');
            
            $siNodes = $sXpath->query('//x:si');
            foreach ($siNodes as $si) {
                $tNodes = $sXpath->query('.//x:t', $si);
                $str = '';
                foreach ($tNodes as $t) {
                    $str .= $t->nodeValue;
                }
                $sharedStrings[] = $str;
            }
        }

        // 2. Mapear estilos para identificar celdas con formato de Fecha (styles.xml)
        $dateStyleIndices = [];
        $stylesIndex = $zip->locateName('xl/styles.xml');
        if ($stylesIndex !== false) {
            $stylesXml = $zip->getFromIndex($stylesIndex);
            $stDom = new DOMDocument();
            libxml_use_internal_errors(true);
            $stDom->loadXML($stylesXml, LIBXML_NOENT | LIBXML_XINCLUDE | LIBXML_NOERROR | LIBXML_NOWARNING);
            libxml_clear_errors();
            $stXpath = new DOMXPath($stDom);
            $stXpath->registerNamespace('x', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');

            $customDateFmtIds = [];
            foreach ($stXpath->query('//x:numFmts/x:numFmt') as $nf) {
                $fmtId = (int)$nf->getAttribute('numFmtId');
                $code = strtolower((string)$nf->getAttribute('formatCode'));
                if (preg_match('/[ydm]|fecha|date/', $code)) {
                    $customDateFmtIds[$fmtId] = true;
                }
            }

            // Formatos de fecha predefinidos en OpenXML/Excel (14..22, 45..47)
            $builtinDateFmtIds = [14=>1, 15=>1, 16=>1, 17=>1, 18=>1, 19=>1, 20=>1, 21=>1, 22=>1, 45=>1, 46=>1, 47=>1];

            $xfIndex = 0;
            foreach ($stXpath->query('//x:cellXfs/x:xf') as $xf) {
                $numFmtId = (int)$xf->getAttribute('numFmtId');
                if (isset($builtinDateFmtIds[$numFmtId]) || isset($customDateFmtIds[$numFmtId])) {
                    $dateStyleIndices[$xfIndex] = true;
                }
                $xfIndex++;
            }
        }

        // 3. Mapear hojas desde workbook.xml si existe
        $sheetsMap = [];
        $wbIndex = $zip->locateName('xl/workbook.xml');
        $wbRelsIndex = $zip->locateName('xl/_rels/workbook.xml.rels');

        $relsMap = [];
        if ($wbRelsIndex !== false) {
            $relsXml = $zip->getFromIndex($wbRelsIndex);
            $rDom = new DOMDocument();
            libxml_use_internal_errors(true);
            $rDom->loadXML($relsXml, LIBXML_NOENT | LIBXML_XINCLUDE | LIBXML_NOERROR | LIBXML_NOWARNING);
            libxml_clear_errors();
            foreach ($rDom->getElementsByTagName('Relationship') as $rel) {
                $id = $rel->getAttribute('Id');
                $target = $rel->getAttribute('Target');
                $relsMap[$id] = 'xl/' . ltrim($target, '/');
            }
        }

        if ($wbIndex !== false) {
            $wbXml = $zip->getFromIndex($wbIndex);
            $wbDom = new DOMDocument();
            libxml_use_internal_errors(true);
            $wbDom->loadXML($wbXml, LIBXML_NOENT | LIBXML_XINCLUDE | LIBXML_NOERROR | LIBXML_NOWARNING);
            libxml_clear_errors();
            $wXpath = new DOMXPath($wbDom);
            $wXpath->registerNamespace('x', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');
            $wXpath->registerNamespace('r', 'http://schemas.openxmlformats.org/officeDocument/2006/relationships');

            foreach ($wXpath->query('//x:sheets/x:sheet') as $sNode) {
                $sName = $sNode->getAttribute('name');
                $rId = $sNode->getAttribute('r:id');
                $sPath = $relsMap[$rId] ?? '';
                if ($sPath !== '') {
                    $sheetsMap[] = ['name' => $sName, 'path' => $sPath];
                }
            }
        }

        // Si no se mapearon por workbook.xml, buscar secuencialmente
        if (empty($sheetsMap)) {
            for ($i = 1; $i <= 40; $i++) {
                $p = "xl/worksheets/sheet{$i}.xml";
                if ($zip->locateName($p) !== false) {
                    $sheetsMap[] = ['name' => "Hoja {$i}", 'path' => $p];
                }
            }
        }

        // 4. Cargar contenido de cada hoja (con soporte ampliado hasta 3500 filas por hoja)
        $output = [];
        $totalSheets = count($sheetsMap);
        $sheetIdx = 0;

        foreach ($sheetsMap as $sheetInfo) {
            $sheetIdx++;
            $sheetPath = $sheetInfo['path'];
            $sheetName = $sheetInfo['name'];
            $sIdx = $zip->locateName($sheetPath);
            if ($sIdx === false) continue;

            $sheetXml = $zip->getFromIndex($sIdx);
            $sheetDom = new DOMDocument();
            libxml_use_internal_errors(true);
            $sheetDom->loadXML($sheetXml, LIBXML_NOENT | LIBXML_XINCLUDE | LIBXML_NOERROR | LIBXML_NOWARNING);
            libxml_clear_errors();
            $xpath = new DOMXPath($sheetDom);
            $xpath->registerNamespace('x', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');

            $rows = $xpath->query('//x:row');
            if ($rows->length === 0) continue;

            $output[] = "\n=== HOJA [{$sheetIdx}/{$totalSheets}]: {$sheetName} ===";
            $sheetRowCount = 0;

            foreach ($rows as $row) {
                // Límite generoso para abarcar años enteros de planificaciones deportivas
                if ($sheetRowCount > 3500) {
                    $output[] = "...[Hoja continuaba con más de 3500 filas; omitidas filas excedentes]...";
                    break;
                }

                $cells = $xpath->query('.//x:c', $row);
                $rowVals = [];

                foreach ($cells as $c) {
                    $type = $c->getAttribute('t');
                    $sAttr = $c->getAttribute('s');
                    $rAttr = $c->getAttribute('r'); // Coordenada de celda (ej: A5)
                    $colLetter = preg_replace('/[0-9]/', '', (string)$rAttr) ?: '';

                    $vNode = $xpath->query('.//x:v', $c)->item(0);
                    $val = $vNode ? $vNode->nodeValue : '';

                    if ($type === 's' && isset($sharedStrings[(int)$val])) {
                        $val = $sharedStrings[(int)$val];
                    } elseif ($type === 'inlineStr') {
                        $tNode = $xpath->query('.//x:t', $c)->item(0);
                        $val = $tNode ? $tNode->nodeValue : '';
                    } else {
                        // Procesar posibles fechas seriales de Excel
                        if (is_numeric($val)) {
                            $numVal = (float)$val;
                            $hasDateStyle = ($sAttr !== '' && isset($dateStyleIndices[(int)$sAttr]));
                            
                            // Si tiene estilo de fecha o es un número serial en rango de fechas de entrenamiento (40000 a 53000: 2009 a 2045)
                            if ($hasDateStyle && $numVal >= 35000 && $numVal <= 55000) {
                                $dateStr = self::excelSerialToDate($numVal);
                                if ($dateStr) {
                                    $val = $dateStr;
                                }
                            } elseif ($numVal >= 43831 && $numVal <= 48212 && floor($numVal) == $numVal) {
                                // Número entero en rango de fechas 2020 a 2031 (días de entrenamiento)
                                $dateStr = self::excelSerialToDate($numVal);
                                if ($dateStr) {
                                    $val = $dateStr;
                                }
                            }
                        }
                    }

                    $valClean = trim(str_replace(["\r", "\n", "\t"], ' ', (string)$val));
                    if ($valClean !== '') {
                        $rowVals[] = $valClean;
                    }
                }

                if (!empty($rowVals)) {
                    $output[] = implode(' | ', $rowVals);
                    $sheetRowCount++;
                }
            }
        }

        $zip->close();
        return implode("\n", $output);
    }

    /**
     * Convierte número serial de Excel a formato YYYY-MM-DD
     */
    public static function excelSerialToDate($serial): ?string {
        if (!is_numeric($serial)) return null;
        $val = (float)$serial;
        if ($val < 35000 || $val > 55000) return null;
        $unix = ($val - 25569) * 86400;
        return gmdate('Y-m-d', (int)$unix);
    }

    /**
     * Parsea fechas de semanas en cualquier formato:
     * - "Semana [del] DD/MM/YYYY", "DD-MM-YYYY", "DD.MM.YYYY"
     * - ISO "YYYY-MM-DD"
     * - Meses en texto: "Semana [del] DD de (enero..diciembre) [de] YYYY"
     * - Número serial de Excel (43831 a 51134)
     */
    public static function parsearFechaSemana(string $text, array $rowCells = []): ?string {
        $text = trim($text);
        if ($text === '') return null;

        // 1. Formato estándar: "Semana [del] DD/MM/YYYY" o "DD-MM-YYYY" o "DD.MM.YYYY"
        if (preg_match('/semana\s*(?:del?\s*)?(\d{1,2})\s*[\/\-\.]\s*(\d{1,2})\s*[\/\-\.]\s*(\d{2,4})/i', $text, $m)) {
            $day = (int)$m[1];
            $month = (int)$m[2];
            $year = (int)$m[3];
            if ($year < 100) $year += 2000;
            if (checkdate($month, $day, $year)) {
                return sprintf('%04d-%02d-%02d', $year, $month, $day);
            }
        }

        // 2. Formato ISO: "Semana [del] YYYY-MM-DD"
        if (preg_match('/semana\s*(?:del?\s*)?(\d{4})\s*[\/\-]\s*(\d{1,2})\s*[\/\-]\s*(\d{1,2})/i', $text, $m)) {
            $year = (int)$m[1];
            $month = (int)$m[2];
            $day = (int)$m[3];
            if (checkdate($month, $day, $year)) {
                return sprintf('%04d-%02d-%02d', $year, $month, $day);
            }
        }

        // 3. Formato meses en español: "Semana [del] DD de marzo [de] YYYY" o "17-mar-2025"
        $meses = [
            'ene' => 1, 'enero' => 1, 'feb' => 2, 'febrero' => 2, 'mar' => 3, 'marzo' => 3,
            'abr' => 4, 'abril' => 4, 'may' => 5, 'mayo' => 5, 'jun' => 6, 'junio' => 6,
            'jul' => 7, 'julio' => 7, 'ago' => 8, 'agosto' => 8, 'sep' => 9, 'sept' => 9, 'septiembre' => 9,
            'oct' => 10, 'octubre' => 10, 'nov' => 11, 'noviembre' => 11, 'dic' => 12, 'diciembre' => 12
        ];
        $mesesPattern = implode('|', array_keys($meses));
        if (preg_match('/semana\s*(?:del?\s*)?(\d{1,2})\s*(?:de|-)?\s*(' . $mesesPattern . ')\s*(?:del?|-)?\s*(\d{2,4})?/i', $text, $m)) {
            $day = (int)$m[1];
            $mName = mb_strtolower(trim($m[2]));
            $month = $meses[$mName] ?? 0;
            $year = !empty($m[3]) ? (int)$m[3] : (int)date('Y');
            if ($year < 100) $year += 2000;
            if ($month > 0 && checkdate($month, $day, $year)) {
                return sprintf('%04d-%02d-%02d', $year, $month, $day);
            }
        }

        // 4. Si la fila contiene la palabra "Semana" y alguna celda tiene un número serial de Excel
        if (stripos($text, 'semana') !== false) {
            foreach ($rowCells as $cVal) {
                $cVal = trim((string)$cVal);
                $serialDate = self::excelSerialToDate($cVal);
                if ($serialDate !== null) {
                    return $serialDate;
                }
            }
        }

        // 5. Standalone DD/MM/YYYY si la fila contiene explícitamente "Semana"
        if (stripos($text, 'semana') !== false && preg_match('/\b(\d{1,2})\s*[\/\-\.]\s*(\d{1,2})\s*[\/\-\.]\s*(\d{2,4})\b/', $text, $m)) {
            $day = (int)$m[1];
            $month = (int)$m[2];
            $year = (int)$m[3];
            if ($year < 100) $year += 2000;
            if (checkdate($month, $day, $year)) {
                return sprintf('%04d-%02d-%02d', $year, $month, $day);
            }
        }

        return null;
    }

    /**
     * Parsea encabezados de día de entrenamiento (ej: "Pierna (Lunes)", "Lunes - Pierna", "Martes", "Torso (Mar)", "Full Body A", "Torso")
     */
    public static function parsearEncabezadoDia(string $headerVal, array $dayOffset, int $orderIndex = 0, int $totalInWeek = 1): ?array {
        $headerVal = trim($headerVal);
        if ($headerVal === '') return null;

        $diasRegex = 'lunes|martes|miércoles|miercoles|jueves|viernes|sábado|sabado|domingo|lun|mar|mie|mié|jue|vie|sab|sáb|dom';
        $normalizeMap = [
            'lun' => 'lunes', 'lunes' => 'lunes',
            'mar' => 'martes', 'martes' => 'martes',
            'mie' => 'miercoles', 'mié' => 'miercoles', 'miercoles' => 'miercoles', 'miércoles' => 'miercoles',
            'jue' => 'jueves', 'jueves' => 'jueves',
            'vie' => 'viernes', 'viernes' => 'viernes',
            'sab' => 'sabado', 'sáb' => 'sabado', 'sabado' => 'sabado', 'sábado' => 'sabado',
            'dom' => 'domingo', 'domingo' => 'domingo'
        ];

        // Caso A: "Pierna (Lunes)" o "Pecho (Lun)"
        if (preg_match('/^(.*?)\s*[\(\[]\s*(' . $diasRegex . ')\s*[\)\]]/i', $headerVal, $dm)) {
            $rawDay = mb_strtolower(trim($dm[2]));
            $dayName = $normalizeMap[$rawDay] ?? $rawDay;
            $routName = trim($dm[1]) ?: ucfirst($dayName);
            return [
                'nombre' => $routName,
                'dia_nombre' => ucfirst($dayName),
                'offset' => $dayOffset[$dayName] ?? 0
            ];
        }

        // Caso B: "Lunes: Pierna" o "Lunes - Pierna" o "Lunes (Pierna)"
        if (preg_match('/^(' . $diasRegex . ')\s*[\:\-\(\[]\s*(.*?)(?:[\)\]])?$/i', $headerVal, $dm)) {
            $rawDay = mb_strtolower(trim($dm[1]));
            $dayName = $normalizeMap[$rawDay] ?? $rawDay;
            $routName = trim($dm[2]) ?: ucfirst($dayName);
            return [
                'nombre' => $routName,
                'dia_nombre' => ucfirst($dayName),
                'offset' => $dayOffset[$dayName] ?? 0
            ];
        }

        // Caso C: Standalone "Lunes", "Martes", "Día 1 - Lunes"
        if (preg_match('/\b(' . $diasRegex . ')\b/i', $headerVal, $dm)) {
            $rawDay = mb_strtolower(trim($dm[1]));
            $dayName = $normalizeMap[$rawDay] ?? $rawDay;
            $cleanName = trim(preg_replace('/\b(' . $diasRegex . ')\b/i', '', $headerVal));
            $cleanName = trim($cleanName, " -:–—");
            $routName = $cleanName !== '' ? $cleanName : ucfirst($dayName);
            return [
                'nombre' => $routName,
                'dia_nombre' => ucfirst($dayName),
                'offset' => $dayOffset[$dayName] ?? 0
            ];
        }

        // Caso D: Encabezado sin día explícito (ej: "Full Body A", "Full Body B", "Torso", "Pierna", "Push", "Pull")
        if (preg_match('/^(?:ejercicios|series|repeticiones|reps|peso|rir|descanso|totales|notas|observaciones)/i', $headerVal)) {
            return null;
        }

        $offsetPresets = [
            1 => [0],
            2 => [1, 4],
            3 => [1, 3, 5],
            4 => [1, 2, 4, 5],
            5 => [1, 2, 3, 5, 6],
            6 => [0, 1, 2, 3, 4, 5],
            7 => [0, 1, 2, 3, 4, 5, 6]
        ];

        $diasSemanaNombres = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];
        $presets = $offsetPresets[$totalInWeek] ?? $offsetPresets[7];
        $offset = $presets[$orderIndex] ?? min(6, $orderIndex);
        $diaNombre = $diasSemanaNombres[$offset] ?? ('Día ' . ($orderIndex + 1));

        return [
            'nombre' => $headerVal,
            'dia_nombre' => $diaNombre,
            'offset' => $offset
        ];
    }

    /**
     * Intenta extraer de forma nativa e instantánea la cronología estructurada
     * si el archivo Excel sigue un diseño de Macrociclo o bloques semanales en columnas.
     */
    public static function extraerCronologiaTabular(string $filePath, array $catalogoEjercicios = []): ?array {
        $zip = new ZipArchive();
        if ($zip->open($filePath) !== true) {
            return null;
        }

        // 1. Cargar sharedStrings
        $sharedStrings = [];
        $ssIndex = $zip->locateName('xl/sharedStrings.xml');
        if ($ssIndex !== false) {
            $ssXml = $zip->getFromIndex($ssIndex);
            $sDom = new DOMDocument();
            libxml_use_internal_errors(true);
            $sDom->loadXML($ssXml);
            $sXpath = new DOMXPath($sDom);
            $sXpath->registerNamespace('x', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');
            foreach ($sXpath->query('//x:si') as $si) {
                $tNodes = $sXpath->query('.//x:t', $si);
                $str = '';
                foreach ($tNodes as $t) $str .= $t->nodeValue;
                $sharedStrings[] = $str;
            }
        }

        // 2. Mapear hojas
        $sheetPaths = [];
        $wbIndex = $zip->locateName('xl/workbook.xml');
        $wbRelsIndex = $zip->locateName('xl/_rels/workbook.xml.rels');
        $relsMap = [];
        if ($wbRelsIndex !== false) {
            $rDom = new DOMDocument();
            libxml_use_internal_errors(true);
            $rDom->loadXML($zip->getFromIndex($wbRelsIndex));
            foreach ($rDom->getElementsByTagName('Relationship') as $rel) {
                $relsMap[$rel->getAttribute('Id')] = 'xl/' . ltrim($rel->getAttribute('Target'), '/');
            }
        }
        if ($wbIndex !== false) {
            $wDom = new DOMDocument();
            libxml_use_internal_errors(true);
            $wDom->loadXML($zip->getFromIndex($wbIndex));
            $wXpath = new DOMXPath($wDom);
            $wXpath->registerNamespace('x', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');
            $wXpath->registerNamespace('r', 'http://schemas.openxmlformats.org/officeDocument/2006/relationships');
            foreach ($wXpath->query('//x:sheets/x:sheet') as $sNode) {
                $rId = $sNode->getAttribute('r:id');
                if (isset($relsMap[$rId])) {
                    $sheetPaths[] = $relsMap[$rId];
                }
            }
        }
        if (empty($sheetPaths)) {
            for ($i = 1; $i <= 5; $i++) {
                $p = "xl/worksheets/sheet{$i}.xml";
                if ($zip->locateName($p) !== false) {
                    $sheetPaths[] = $p;
                }
            }
        }

        $allSessions = [];
        $allMedidas = [];

        $dayOffset = [
            'lunes' => 0, 'martes' => 1, 'miércoles' => 2, 'miercoles' => 2,
            'jueves' => 3, 'viernes' => 4, 'sábado' => 5, 'sabado' => 5, 'domingo' => 6
        ];

        // 3. Procesar hojas en busca de bloques de entrenamiento
        foreach ($sheetPaths as $sheetPath) {
            $sIdx = $zip->locateName($sheetPath);
            if ($sIdx === false) continue;

            $sheetXml = $zip->getFromIndex($sIdx);
            $sDom = new DOMDocument();
            libxml_use_internal_errors(true);
            $sDom->loadXML($sheetXml);
            $xpath = new DOMXPath($sDom);
            $xpath->registerNamespace('x', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');

            $rows = $xpath->query('//x:row');
            if ($rows->length === 0) continue;

            $grid = [];
            foreach ($rows as $row) {
                $rNum = (int)$row->getAttribute('r');
                $cells = $xpath->query('.//x:c', $row);
                $rowCells = [];
                foreach ($cells as $c) {
                    $rCoord = $c->getAttribute('r');
                    if (!preg_match('/^([A-Z]+)(\d+)$/', (string)$rCoord, $m)) continue;
                    $colLetters = $m[1];
                    $colIdx = 0;
                    for ($i = 0; $i < strlen($colLetters); $i++) {
                        $colIdx = $colIdx * 26 + (ord($colLetters[$i]) - ord('A') + 1);
                    }
                    $colIdx -= 1;

                    $type = $c->getAttribute('t');
                    $vNode = $xpath->query('.//x:v', $c)->item(0);
                    $val = $vNode ? $vNode->nodeValue : '';
                    if ($type === 's' && isset($sharedStrings[(int)$val])) {
                        $val = $sharedStrings[(int)$val];
                    }
                    $rowCells[$colIdx] = trim((string)$val);
                }
                $grid[$rNum] = $rowCells;
            }

            if (empty($grid)) continue;
            $maxRow = max(array_keys($grid));
            $r = 1;

            while ($r <= $maxRow) {
                if (!isset($grid[$r])) {
                    $r++;
                    continue;
                }

                $rowText = implode(' ', $grid[$r]);
                $weekMonday = self::parsearFechaSemana($rowText, $grid[$r]);
                if ($weekMonday !== null) {
                    // Peso corporal (indexado por fecha para evitar colisiones de clave única, soporta "62+", "62.5 kg", etc.)
                    if (preg_match('/peso\s*(?:corporal)?\s*[:=]?\s*([\d\.]+)\s*\+?\s*(?:kg|kilos?)?/i', $rowText, $mp)) {
                        $allMedidas[$weekMonday] = [
                            'fecha' => $weekMonday,
                            'peso_kg' => (float)$mp[1]
                        ];
                    }

                    // Fila de rutinas por día
                    $rDays = $r + 1;
                    while ($rDays <= $maxRow && !isset($grid[$rDays])) {
                        $rDays++;
                    }
                    $dayHeaders = $grid[$rDays] ?? [];

                    $validHeaders = [];
                    foreach ($dayHeaders as $cIdx => $headerVal) {
                        $hTrim = trim((string)$headerVal);
                        if ($hTrim === '') continue;
                        if (preg_match('/^(?:ejercicios|series|repeticiones|reps|peso|rir|descanso|totales|notas|observaciones)/i', $hTrim)) continue;
                        $validHeaders[$cIdx] = $hTrim;
                    }

                    $totalInWeek = max(1, count($validHeaders));
                    $orderIdx = 0;
                    $routines = [];
                    foreach ($validHeaders as $cIdx => $headerVal) {
                        $parsedDay = self::parsearEncabezadoDia($headerVal, $dayOffset, $orderIdx, $totalInWeek);
                        if ($parsedDay !== null) {
                            $offset = $parsedDay['offset'];
                            $sessionDate = date('Y-m-d', strtotime("{$weekMonday} +{$offset} days"));

                            $routines[] = [
                                'col_start' => $cIdx,
                                'nombre' => $parsedDay['nombre'],
                                'dia_nombre' => $parsedDay['dia_nombre'],
                                'fecha' => $sessionDate,
                                'ejercicios' => []
                            ];
                            $orderIdx++;
                        }
                    }

                    // Fila de inicio de ejercicios
                    $rEx = $rDays + 1;
                    while ($rEx <= $maxRow) {
                        $subText = implode(' ', $grid[$rEx] ?? []);
                        if (stripos($subText, 'Ejercicios') !== false || stripos($subText, 'Series') !== false) {
                            $rEx++;
                            break;
                        }
                        $rEx++;
                    }

                    while ($rEx <= $maxRow) {
                        $curRowText = implode(' ', $grid[$rEx] ?? []);
                        if (stripos($curRowText, 'Semana') !== false) {
                            break;
                        }
                        if (stripos($curRowText, 'Notas:') !== false || stripos($curRowText, 'Totales semanal:') !== false) {
                            $rEx++;
                            continue;
                        }

                        $cells = $grid[$rEx] ?? [];
                        if (!empty($cells)) {
                            foreach ($routines as $rKey => $rt) {
                                $cStart = $rt['col_start'];
                                $ejName = trim($cells[$cStart] ?? '');
                                if ($ejName !== '' && stripos($ejName, 'minuto') === false && stripos($ejName, 'descanso') === false) {
                                    $seriesRaw = trim((string)($cells[$cStart + 2] ?? ''));
                                    $repsRaw   = trim((string)($cells[$cStart + 4] ?? ''));
                                    $pesoRaw   = trim((string)($cells[$cStart + 6] ?? ''));

                                    $tieneDatos = ($repsRaw !== '' || $pesoRaw !== '');

                                    $match = self::matchEjercicioCatalogo($ejName, $catalogoEjercicios);

                                    $seriesCount = max(1, (int)$seriesRaw);
                                    $repsList = preg_split('/[,;\-\s\/]+/', trim($repsRaw, " \t\n\r\0\x0B-"));
                                    $repsList = array_values(array_filter($repsList, fn($v) => is_numeric($v)));
                                    if (empty($repsList)) $repsList = [$tieneDatos ? 10 : 10];

                                    // Detección ultra-precisa de unidades y tipos de carga
                                    $rawLower = mb_strtolower(trim($pesoRaw), 'UTF-8');
                                    $ejLower  = mb_strtolower(trim($ejName), 'UTF-8');

                                    $isLbsExplicit    = (bool)preg_match('/(?:lbs?|libras?)\b/i', $rawLower);
                                    $isKgExplicit     = (bool)preg_match('/(?:kg|kilos?)\b/i', $rawLower);
                                    $isPlacasExplicit = (bool)preg_match('/(?:placas?|placa|pg|pp)\b/i', $rawLower);
                                    $isSinPeso        = (bool)preg_match('/(?:sin peso|peso corporal|calistenia|libre|corporal)\b/i', $rawLower) || (bool)preg_match('/(?:sin peso|peso corporal|calistenia)\b/i', $ejLower);

                                    $isPlacasContext  = (bool)preg_match('/(?:placas?)\b/i', $ejLower);
                                    $isGrandes        = (bool)preg_match('/(?:grande|grandes|pg)\b/i', $rawLower) || (bool)preg_match('/(?:grande|grandes)\b/i', $ejLower);
                                    $isPequenas       = (bool)preg_match('/(?:peque|pequena|pequeña|pequenas|pequeñas|pp)\b/i', $rawLower) || (bool)preg_match('/(?:peque|pequena|pequeña|pequenas|pequeñas)\b/i', $ejLower);
                                    $isPorLado        = (bool)preg_match('/(?:lado|c\/u|por mano)\b/i', $rawLower);

                                    $unidad = 'kg';
                                    if ($isSinPeso) {
                                        $unidad = 'ninguna';
                                    } elseif ($isLbsExplicit) {
                                        $unidad = 'lbs';
                                    } elseif ($isPlacasExplicit) {
                                        $unidad = 'placas';
                                    } elseif ($isKgExplicit) {
                                        $unidad = 'kg';
                                    } elseif ($isPlacasContext) {
                                        $pClean = preg_replace('/[^\d\.]+/', ' ', $pesoRaw);
                                        $numP = (float)trim($pClean);
                                        $unidad = ($numP > 30) ? 'lbs' : 'placas';
                                    }

                                    $isPlacas = ($unidad === 'placas' || $isPlacasExplicit || $isPlacasContext);

                                    $equipamientoDetectado = $match['equipamiento'];
                                    if ($isPlacas) {
                                        if ($isGrandes) {
                                            $equipamientoDetectado = 'Placas Grandes';
                                        } elseif ($isPequenas) {
                                            $equipamientoDetectado = 'Placas Pequeñas';
                                        } elseif (empty($equipamientoDetectado)) {
                                            $equipamientoDetectado = 'Placas';
                                        }
                                    }

                                    $tipoImplementoDetectado = $match['tipo_implemento'];
                                    if ($isSinPeso) {
                                        $tipoImplementoDetectado = 'sin_peso';
                                    } elseif ($isPlacas && $tipoImplementoDetectado !== 'polea_maquina') {
                                        $tipoImplementoDetectado = 'polea_maquina';
                                    } elseif ($isPorLado && $tipoImplementoDetectado === 'barra') {
                                        $tipoImplementoDetectado = 'mancuerna';
                                    }

                                    // Extracción de pesos numéricos
                                    $pesoClean = preg_replace('/[a-zA-Z\/\s°\+]+/', ' ', $pesoRaw);
                                    $pesoParts = preg_split('/[\-\s,;]+/', trim($pesoClean));
                                    $pesoParts = array_values(array_filter($pesoParts, fn($v) => is_numeric($v)));

                                    $seriesObjs = [];
                                    $numSeriesReal = max($seriesCount, count($repsList));
                                    for ($sNum = 1; $sNum <= $numSeriesReal; $sNum++) {
                                        $rVal = (int)($repsList[$sNum - 1] ?? end($repsList) ?? 10);
                                        $pVal = (float)($pesoParts[$sNum - 1] ?? end($pesoParts) ?? 0.0);
                                        
                                        // Cálculo de kg_real para volumen total
                                        $kgReal = $pVal;
                                        if ($unidad === 'lbs') {
                                            $kgReal = round($pVal / 2.20462, 2);
                                            if ($isPorLado) $kgReal = round(($pVal * 2) / 2.20462, 2);
                                        } elseif ($unidad === 'kg') {
                                            if ($isPorLado) $kgReal = round($pVal * 2, 2);
                                        } elseif ($unidad === 'ninguna') {
                                            $pVal = 0.0;
                                            $kgReal = 0.0;
                                        }

                                        $seriesObjs[] = [
                                            'numero_serie' => $sNum,
                                            'reps' => $rVal,
                                            'peso' => $pVal,
                                            'peso_raw' => $pesoRaw,
                                            'unidad' => $unidad,
                                            'kg_real' => $kgReal,
                                            'peso_por_lado' => $isPorLado ? $pVal : null,
                                            'tipo_serie' => 'normal',
                                            'tiene_datos' => $tieneDatos
                                        ];
                                    }

                                    $routines[$rKey]['ejercicios'][] = [
                                        'nombre_detectado' => $ejName,
                                        'catalogo_id' => $match['catalogo_id'],
                                        'nombre_oficial' => $match['nombre_oficial'],
                                        'equipamiento' => $equipamientoDetectado,
                                        'tipo_implemento' => $tipoImplementoDetectado,
                                        'unidad_carga' => $unidad,
                                        'descripcion_peso' => $pesoRaw,
                                        'tiene_datos' => $tieneDatos,
                                        'series' => $seriesObjs
                                    ];
                                }
                            }
                        }
                        $rEx++;
                    }

                    foreach ($routines as $rt) {
                        if (!empty($rt['ejercicios'])) {
                            // Determinar si la sesión tiene datos reales registrados o fue solo planificada
                            $tieneDatosSesion = false;
                            foreach ($rt['ejercicios'] as $ejChk) {
                                if (!empty($ejChk['tiene_datos'])) {
                                    $tieneDatosSesion = true;
                                    break;
                                }
                            }

                            $allSessions[] = [
                                'fecha' => $rt['fecha'],
                                'dia_nombre' => $rt['dia_nombre'],
                                'rutina_nombre' => $rt['nombre'],
                                'tiene_datos' => $tieneDatosSesion,
                                'es_planificada_futura' => !$tieneDatosSesion,
                                'ejercicios' => $rt['ejercicios']
                            ];
                        }
                    }

                    $r = $rEx;
                    continue;
                }

                $r++;
            }
        }

        $zip->close();

        // Validar que se detectó un volumen consistente de sesiones
        if (count($allSessions) < 3) {
            return null;
        }

        // Ordenar cronológicamente
        usort($allSessions, function($a, $b) {
            return strcmp($a['fecha'], $b['fecha']);
        });

        // Contar series totales
        $totalSeries = 0;
        foreach ($allSessions as $s) {
            foreach ($s['ejercicios'] as $ej) {
                $totalSeries += count($ej['series']);
            }
        }

        $fechaIni = $allSessions[0]['fecha'] ?? '--';
        $fechaFin = end($allSessions)['fecha'] ?? '--';

        return [
            'resumen' => [
                'total_sesiones' => count($allSessions),
                'total_series' => $totalSeries,
                'rango_fechas' => "{$fechaIni} al {$fechaFin}",
                'pesos_corporales_encontrados' => count($allMedidas)
            ],
            'medidas_corporales' => array_values($allMedidas),
            'sesiones' => $allSessions
        ];
    }

    /**
     * Empareja un nombre de ejercicio detectado contra el catálogo oficial del sistema
     */
    private static function matchEjercicioCatalogo(string $detected, array $catalogo): array {
        $clean = mb_strtolower(trim(preg_replace('/\(.*?\)/', '', $detected)));
        $clean = trim(preg_replace('/\s+/', ' ', $clean));

        $best = null;
        $bestScore = 0;

        foreach ($catalogo as $cat) {
            $catClean = mb_strtolower(trim($cat['nombre']));
            if ($clean === $catClean) {
                return [
                    'catalogo_id' => (int)$cat['id'],
                    'nombre_oficial' => $cat['nombre'],
                    'equipamiento' => $cat['equipamiento'] ?? '',
                    'tipo_implemento' => $cat['tipo_implemento'] ?? 'barra'
                ];
            }

            similar_text($clean, $catClean, $percent);
            if ($percent > $bestScore && $percent >= 60) {
                $bestScore = $percent;
                $best = [
                    'catalogo_id' => (int)$cat['id'],
                    'nombre_oficial' => $cat['nombre'],
                    'equipamiento' => $cat['equipamiento'] ?? '',
                    'tipo_implemento' => $cat['tipo_implemento'] ?? 'barra'
                ];
            }
        }

        $tipoImp = 'barra';
        if (stripos($detected, 'mancuerna') !== false) {
            $tipoImp = 'mancuerna';
        } elseif (stripos($detected, 'polea') !== false || stripos($detected, 'máquina') !== false || stripos($detected, 'maquina') !== false) {
            $tipoImp = 'polea_maquina';
        }

        return [
            'catalogo_id' => $best['catalogo_id'] ?? null,
            'nombre_oficial' => $best['nombre_oficial'] ?? $detected,
            'equipamiento' => $best['equipamiento'] ?? '',
            'tipo_implemento' => $best['tipo_implemento'] ?? $tipoImp
        ];
    }
}
