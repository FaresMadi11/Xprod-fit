<?php
// core/ExcelExportService.php — Generador Nativo OpenXML (.xlsx) para Xprod Fit
// Genera archivos .xlsx nativos multi-hoja, con diseño Bento UI, cuadrículas y cero dependencias externas.
declare(strict_types=1);

class ExcelExportService {

    /**
     * Construye un archivo físico .xlsx válido en $outputPath.
     * 
     * Admite dos formatos de hoja:
     * 1. Formato Tabular Clásico: ['headers' => [...], 'rows' => [...]]
     * 2. Formato Cuadrícula Libre (Grid): ['type' => 'grid', 'grid' => [...], 'col_widths' => [...]]
     *
     * @param string $outputPath Ruta absoluta del archivo a escribir.
     * @param array $sheets Estructura asociativa de hojas.
     */
    public static function generarArchivo(string $outputPath, array $sheets): bool {
        if (empty($sheets)) {
            return false;
        }

        $zip = new ZipArchive();
        if ($zip->open($outputPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            return false;
        }

        $sheetNames = array_keys($sheets);
        $sheetCount = count($sheetNames);

        // Tabla unificada de cadenas compartidas (sharedStrings)
        $stringList  = [];
        $stringTable = [];
        $getStringId = function(string $str) use (&$stringList, &$stringTable): int {
            if (!isset($stringTable[$str])) {
                $idx = count($stringList);
                $stringTable[$str] = $idx;
                $stringList[] = $str;
            }
            return $stringTable[$str];
        };

        // Función auxiliar para letras de columna (0 => A, 27 => AB...)
        $colLetter = function(int $colIndex): string {
            $letter = '';
            while ($colIndex >= 0) {
                $letter = chr($colIndex % 26 + 65) . $letter;
                $colIndex = intdiv($colIndex, 26) - 1;
            }
            return $letter;
        };

        // 1. [Content_Types].xml
        $contentTypes = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n" .
            '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">' .
            '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>' .
            '<Default Extension="xml" ContentType="application/xml"/>' .
            '<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>' .
            '<Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>' .
            '<Override PartName="/xl/sharedStrings.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sharedStrings+xml"/>';

        for ($i = 1; $i <= $sheetCount; $i++) {
            $contentTypes .= '<Override PartName="/xl/worksheets/sheet' . $i . '.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>';
        }
        $contentTypes .= '</Types>';
        $zip->addFromString('[Content_Types].xml', $contentTypes);

        // 2. _rels/.rels
        $rels = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n" .
            '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">' .
            '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>' .
            '</Relationships>';
        $zip->addFromString('_rels/.rels', $rels);

        // 3. xl/_rels/workbook.xml.rels
        $wbRels = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n" .
            '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">';
        for ($i = 1; $i <= $sheetCount; $i++) {
            $wbRels .= '<Relationship Id="rId' . $i . '" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet' . $i . '.xml"/>';
        }
        $wbRels .= '<Relationship Id="rId' . ($sheetCount + 1) . '" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>';
        $wbRels .= '<Relationship Id="rId' . ($sheetCount + 2) . '" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/sharedStrings" Target="sharedStrings.xml"/>';
        $wbRels .= '</Relationships>';
        $zip->addFromString('xl/_rels/workbook.xml.rels', $wbRels);

        // 4. xl/workbook.xml
        $workbook = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n" .
            '<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">' .
            '<sheets>';
        $sIdx = 1;
        foreach ($sheetNames as $sName) {
            $safeName = htmlspecialchars(mb_substr($sName, 0, 31), ENT_XML1, 'UTF-8');
            $workbook .= '<sheet name="' . $safeName . '" sheetId="' . $sIdx . '" r:id="rId' . $sIdx . '"/>';
            $sIdx++;
        }
        $workbook .= '</sheets></workbook>';
        $zip->addFromString('xl/workbook.xml', $workbook);

        // 5. xl/styles.xml (Paleta Bento UI Xprod Fit)
        $styles = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n" .
            '<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">' .
            '<fonts count="7">' .
            '<font><sz val="10"/><name val="Segoe UI"/></font>' .                                           // 0: Normal 10pt
            '<font><b/><color rgb="FFFFFFFF"/><sz val="11"/><name val="Segoe UI"/></font>' .                // 1: Bold blanco 11pt
            '<font><b/><color rgb="FF2B124C"/><sz val="11"/><name val="Segoe UI"/></font>' .                // 2: Bold púrpura oscuro 11pt
            '<font><b/><color rgb="FF374151"/><sz val="9"/><name val="Segoe UI"/></font>' .                 // 3: Bold gris oscuro 9pt
            '<font><b/><color rgb="FFFFFFFF"/><sz val="12"/><name val="Segoe UI"/></font>' .                // 4: Bold blanco 12pt (Semana)
            '<font><sz val="9"/><name val="Segoe UI"/></font>' .                                            // 5: Small 9pt
            '<font><b/><sz val="10"/><name val="Segoe UI"/></font>' .                                       // 6: Bold 10pt
            '</fonts>' .
            '<fills count="6">' .
            '<fill><patternFill patternType="none"/></fill>' .                                              // 0: Ninguno
            '<fill><patternFill patternType="gray125"/></fill>' .                                           // 1: Gray125
            '<fill><patternFill patternType="solid"><fgColor rgb="FF2B124C"/></patternFill></fill>' .       // 2: Púrpura Xprod Marca (#2B124C)
            '<fill><patternFill patternType="solid"><fgColor rgb="FFEDE9FE"/></patternFill></fill>' .       // 3: Lavanda Bento Soft (#EDE9FE)
            '<fill><patternFill patternType="solid"><fgColor rgb="FFF3F4F6"/></patternFill></fill>' .       // 4: Gris Bento Subheader (#F3F4F6)
            '<fill><patternFill patternType="solid"><fgColor rgb="FFF9FAFB"/></patternFill></fill>' .       // 5: Gris ultra suave (#F9FAFB)
            '</fills>' .
            '<borders count="3">' .
            '<border><left/><right/><top/><bottom/></border>' .                                             // 0: Sin bordes
            '<border>' .                                                                                    // 1: Borde sutil gris (#E5E7EB)
            '<left style="thin"><color rgb="FFE5E7EB"/></left>' .
            '<right style="thin"><color rgb="FFE5E7EB"/></right>' .
            '<top style="thin"><color rgb="FFE5E7EB"/></top>' .
            '<bottom style="thin"><color rgb="FFE5E7EB"/></bottom>' .
            '</border>' .
            '<border>' .                                                                                    // 2: Borde estructurado (#D1D5DB)
            '<left style="thin"><color rgb="FFD1D5DB"/></left>' .
            '<right style="thin"><color rgb="FFD1D5DB"/></right>' .
            '<top style="thin"><color rgb="FFD1D5DB"/></top>' .
            '<bottom style="thin"><color rgb="FFD1D5DB"/></bottom>' .
            '</border>' .
            '</borders>' .
            '<cellStyleXfs count="1">' .
            '<xf numFmtId="0" fontId="0" fillId="0" borderId="0"/>' .
            '</cellStyleXfs>' .
            '<cellXfs count="9">' .
            '<xf numFmtId="0" fontId="0" fillId="0" borderId="1" xfId="0"/>' .                                                      // 0: Normal
            '<xf numFmtId="0" fontId="1" fillId="2" borderId="2" xfId="0" applyFont="1" applyFill="1" applyBorder="1"><alignment horizontal="center" vertical="center"/></xf>' . // 1: Header púrpura tabla clásica
            '<xf numFmtId="0" fontId="0" fillId="0" borderId="1" xfId="0" applyBorder="1"/>' .                                      // 2: Datos tabla clásica
            '<xf numFmtId="0" fontId="4" fillId="2" borderId="2" xfId="0" applyFont="1" applyFill="1" applyBorder="1"><alignment horizontal="left" vertical="center" indent="1"/></xf>' .   // 3: Banner Semana
            '<xf numFmtId="0" fontId="1" fillId="2" borderId="2" xfId="0" applyFont="1" applyFill="1" applyBorder="1"><alignment horizontal="center" vertical="center"/></xf>' .           // 4: Peso corporal
            '<xf numFmtId="0" fontId="2" fillId="3" borderId="2" xfId="0" applyFont="1" applyFill="1" applyBorder="1"><alignment horizontal="center" vertical="center"/></xf>' .           // 5: Cabecera Día Rutina
            '<xf numFmtId="0" fontId="3" fillId="4" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1"><alignment horizontal="center" vertical="center"/></xf>' .           // 6: Subcabecera (Ejercicios, Series...)
            '<xf numFmtId="0" fontId="0" fillId="0" borderId="1" xfId="0" applyFont="1" applyBorder="1"><alignment horizontal="left" vertical="center"/></xf>' .                            // 7: Nombre de Ejercicio (izq)
            '<xf numFmtId="0" fontId="0" fillId="0" borderId="1" xfId="0" applyFont="1" applyBorder="1"><alignment horizontal="center" vertical="center"/></xf>' .                          // 8: Dato Centrado (Series, Reps, RIR)
            '</cellXfs>' .
            '</styleSheet>';
        $zip->addFromString('xl/styles.xml', $styles);

        // 6. Generar cada worksheet
        $sheetNum = 1;
        foreach ($sheets as $sName => $sData) {
            $isGrid = (($sData['type'] ?? '') === 'grid');

            if ($isGrid) {
                // MODO CUADRÍCULA (GRID)
                $gridRows  = $sData['grid'] ?? [];
                $colWidths = $sData['col_widths'] ?? [];

                $sheetXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n" .
                    '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">' .
                    '<cols>';

                foreach ($colWidths as $cIdx => $w) {
                    $colNum = $cIdx + 1;
                    $sheetXml .= '<col min="' . $colNum . '" max="' . $colNum . '" width="' . $w . '" customWidth="1"/>';
                }
                $sheetXml .= '</cols><sheetData>';

                ksort($gridRows);
                foreach ($gridRows as $rNum => $rData) {
                    $ht = $rData['ht'] ?? 19;
                    $sheetXml .= '<row r="' . $rNum . '" ht="' . $ht . '" customHeight="1">';
                    $cells = $rData['cells'] ?? [];
                    ksort($cells);
                    foreach ($cells as $cIdx => $cell) {
                        $ref  = $colLetter($cIdx) . $rNum;
                        $sId  = $cell['style'] ?? 0;
                        $val  = $cell['val'] ?? '';
                        $type = $cell['type'] ?? 's';

                        if ($type === 'n' && is_numeric($val)) {
                            $sheetXml .= '<c r="' . $ref . '" s="' . $sId . '"><v>' . $val . '</v></c>';
                        } else {
                            $sVal = (string)$val;
                            $strIdx = $getStringId($sVal);
                            $sheetXml .= '<c r="' . $ref . '" s="' . $sId . '" t="s"><v>' . $strIdx . '</v></c>';
                        }
                    }
                    $sheetXml .= '</row>';
                }
                $sheetXml .= '</sheetData></worksheet>';

            } else {
                // MODO TABULAR CLÁSICO (HEADERS + ROWS)
                $headers = $sData['headers'] ?? [];
                $rows    = $sData['rows'] ?? [];

                $colWidths = [];
                foreach ($headers as $cIdx => $hdr) {
                    $colWidths[$cIdx] = max(12, mb_strlen((string)$hdr) + 4);
                }
                foreach ($rows as $row) {
                    foreach (array_values($row) as $cIdx => $val) {
                        $len = mb_strlen((string)$val) + 3;
                        if (!isset($colWidths[$cIdx]) || $len > $colWidths[$cIdx]) {
                            $colWidths[$cIdx] = min(45, max($colWidths[$cIdx] ?? 12, $len));
                        }
                    }
                }

                $sheetXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n" .
                    '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">' .
                    '<cols>';

                foreach ($colWidths as $cIdx => $w) {
                    $colNum = $cIdx + 1;
                    $sheetXml .= '<col min="' . $colNum . '" max="' . $colNum . '" width="' . $w . '" customWidth="1"/>';
                }
                $sheetXml .= '</cols><sheetData>';

                // Fila de encabezados
                $r = 1;
                if (!empty($headers)) {
                    $sheetXml .= '<row r="' . $r . '" ht="24" customHeight="1">';
                    foreach ($headers as $cIdx => $hdr) {
                        $cellRef = $colLetter($cIdx) . $r;
                        $strIdx = $getStringId((string)$hdr);
                        $sheetXml .= '<c r="' . $cellRef . '" s="1" t="s"><v>' . $strIdx . '</v></c>';
                    }
                    $sheetXml .= '</row>';
                }

                // Filas de datos
                foreach ($rows as $row) {
                    $r++;
                    $sheetXml .= '<row r="' . $r . '" ht="19" customHeight="1">';
                    foreach (array_values($row) as $cIdx => $val) {
                        $cellRef = $colLetter($cIdx) . $r;
                        if ($val === null || $val === '') {
                            $sheetXml .= '<c r="' . $cellRef . '" s="2"/>';
                            continue;
                        }
                        if (is_numeric($val) && !str_starts_with((string)$val, '+') && (!str_starts_with((string)$val, '0') || $val === '0' || str_starts_with((string)$val, '0.'))) {
                            $sheetXml .= '<c r="' . $cellRef . '" s="2"><v>' . $val . '</v></c>';
                        } else {
                            $strIdx = $getStringId((string)$val);
                            $sheetXml .= '<c r="' . $cellRef . '" s="2" t="s"><v>' . $strIdx . '</v></c>';
                        }
                    }
                    $sheetXml .= '</row>';
                }
                $sheetXml .= '</sheetData></worksheet>';
            }

            $zip->addFromString('xl/worksheets/sheet' . $sheetNum . '.xml', $sheetXml);
            $sheetNum++;
        }

        // 7. xl/sharedStrings.xml
        $sstXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n" .
            '<sst xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" count="' . count($stringList) . '" uniqueCount="' . count($stringList) . '">';
        foreach ($stringList as $str) {
            $escaped = htmlspecialchars($str, ENT_XML1, 'UTF-8');
            $sstXml .= '<si><t>' . $escaped . '</t></si>';
        }
        $sstXml .= '</sst>';
        $zip->addFromString('xl/sharedStrings.xml', $sstXml);

        return $zip->close();
    }

    /**
     * Transmite el archivo .xlsx generado directamente al navegador.
     */
    public static function descargarXlsx(string $filename, array $sheets): void {
        @set_time_limit(180);
        @ini_set('memory_limit', '128M');

        if (session_status() === PHP_SESSION_ACTIVE) {
            session_write_close();
        }

        $tmpPath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'xp_export_' . bin2hex(random_bytes(8)) . '.xlsx';
        $ok = self::generarArchivo($tmpPath, $sheets);

        if (!$ok || !file_exists($tmpPath)) {
            http_response_code(500);
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['ok' => false, 'msg' => 'No se pudo generar el archivo Excel en el servidor.']);
            exit;
        }

        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . filesize($tmpPath));
        header('Cache-Control: max-age=0, no-cache, no-store, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');

        readfile($tmpPath);
        @unlink($tmpPath);
        exit;
    }
}
