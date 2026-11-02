<?php
// core/ExcelExportService.php — Generador Nativo OpenXML (.xlsx) para Xprod Fit
// Genera archivos .xlsx nativos multihioja, con estilos y cero dependencias externas.
declare(strict_types=1);

class ExcelExportService {

    /**
     * Construye un archivo físico .xlsx válido en $outputPath.
     * 
     * @param string $outputPath Ruta absoluta del archivo a escribir.
     * @param array $sheets Estructura: ['Nombre Hoja' => ['headers' => [...], 'rows' => [...]], ...]
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

        // 1. [Content_Types].xml
        $contentTypes = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n" .
            '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">' .
            '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>' .
            '<Default Extension="xml" ContentType="application/xml"/>' .
            '<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>' .
            '<Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>';

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

        // 5. xl/styles.xml (Estilo Xprod Fit: Cabecera Púrpura #2B124C y texto blanco negrita)
        $styles = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n" .
            '<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">' .
            '<fonts count="2">' .
            '<font><sz val="11"/><name val="Segoe UI"/></font>' .
            '<font><b/><color rgb="FFFFFFFF"/><sz val="11"/><name val="Segoe UI"/></font>' .
            '</fonts>' .
            '<fills count="3">' .
            '<fill><patternFill patternType="none"/></fill>' .
            '<fill><patternFill patternType="gray125"/></fill>' .
            '<fill><patternFill patternType="solid"><fgColor rgb="FF2B124C"/></patternFill></fill>' .
            '</fills>' .
            '<borders count="2">' .
            '<border><left/><right/><top/><bottom/></border>' .
            '<border>' .
            '<left style="thin"><color rgb="FFD1D5DB"/></left>' .
            '<right style="thin"><color rgb="FFD1D5DB"/></right>' .
            '<top style="thin"><color rgb="FFD1D5DB"/></top>' .
            '<bottom style="thin"><color rgb="FFD1D5DB"/></bottom>' .
            '</border>' .
            '</borders>' .
            '<cellStyleXfs count="1">' .
            '<xf numFmtId="0" fontId="0" fillId="0" borderId="0"/>' .
            '</cellStyleXfs>' .
            '<cellXfs count="3">' .
            '<xf numFmtId="0" fontId="0" fillId="0" borderId="1" xfId="0"/>' . // 0: Normal con borde
            '<xf numFmtId="0" fontId="1" fillId="2" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1"/>' . // 1: Header púrpura
            '<xf numFmtId="0" fontId="0" fillId="0" borderId="1" xfId="0" applyBorder="1"/>' . // 2: Datos
            '</cellXfs>' .
            '</styleSheet>';
        $zip->addFromString('xl/styles.xml', $styles);

        // Función auxiliar para letras de columna (0 => A, 27 => AB...)
        $colLetter = function(int $colIndex): string {
            $letter = '';
            while ($colIndex >= 0) {
                $letter = chr($colIndex % 26 + 65) . $letter;
                $colIndex = intdiv($colIndex, 26) - 1;
            }
            return $letter;
        };

        // 6. Generar cada worksheet
        $sheetNum = 1;
        foreach ($sheets as $sName => $sData) {
            $headers = $sData['headers'] ?? [];
            $rows = $sData['rows'] ?? [];

            // Calcular anchos aproximados de columna
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
                    $escaped = htmlspecialchars((string)$hdr, ENT_XML1, 'UTF-8');
                    $sheetXml .= '<c r="' . $cellRef . '" s="1" t="inlineStr"><is><t>' . $escaped . '</t></is></c>';
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
                        $escaped = htmlspecialchars((string)$val, ENT_XML1, 'UTF-8');
                        $sheetXml .= '<c r="' . $cellRef . '" s="2" t="inlineStr"><is><t>' . $escaped . '</t></is></c>';
                    }
                }
                $sheetXml .= '</row>';
            }

            $sheetXml .= '</sheetData></worksheet>';
            $zip->addFromString('xl/worksheets/sheet' . $sheetNum . '.xml', $sheetXml);
            $sheetNum++;
        }

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

