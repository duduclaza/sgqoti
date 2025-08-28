<?php
require_once '../config/database.php';

// Parâmetros de exportação
$format = $_GET['format'] ?? 'xlsx';
$includeCalculated = ($_GET['include_calculated'] ?? '1') === '1';
$includeDates = ($_GET['include_dates'] ?? '1') === '1';

try {
    $database = new Database();
    $pdo = $database->getConnection();
    
    // Buscar todos os toners
    $stmt = $pdo->query("SELECT * FROM toners ORDER BY created_at DESC");
    $toners = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($toners)) {
        throw new Exception('Nenhum toner encontrado para exportar');
    }
    
    if ($format === 'xlsx') {
        exportToExcel($toners, $includeCalculated, $includeDates);
    } else {
        exportToCSV($toners, $includeCalculated, $includeDates);
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo "Erro na exportação: " . $e->getMessage();
}

function exportToExcel($toners, $includeCalculated, $includeDates) {
    $filename = 'Toners_SGQ_' . date('Y-m-d_H-i-s') . '.xlsx';
    
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Cache-Control: max-age=0');
    
    // Definir cabeçalhos
    $headers = ['ID', 'Modelo', 'Cor', 'Tipo', 'Capacidade (páginas)', 'Peso Cheio (g)', 'Peso Vazio (g)', 'Preço (R$)'];
    
    if ($includeCalculated) {
        $headers = array_merge($headers, ['Gramatura (g)', 'Gramatura por Folha (g)', 'Preço por Folha (R$)']);
    }
    
    if ($includeDates) {
        $headers = array_merge($headers, ['Data Criação', 'Data Atualização']);
    }
    
    // Criar strings compartilhadas
    $sharedStrings = array_merge($headers, ['Black', 'Cyan', 'Magenta', 'Yellow', 'Compatível', 'Original', 'Remanufaturado']);
    foreach ($toners as $toner) {
        $sharedStrings[] = $toner['modelo'];
    }
    $sharedStrings = array_unique($sharedStrings);
    $sharedStrings = array_values($sharedStrings);
    
    // Criar arquivo ZIP
    $zip = new ZipArchive();
    $tempFile = tempnam(sys_get_temp_dir(), 'excel_export_');
    
    if ($zip->open($tempFile, ZipArchive::CREATE) === TRUE) {
        
        // [Content_Types].xml
        $zip->addFromString('[Content_Types].xml', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">
  <Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>
  <Default Extension="xml" ContentType="application/xml"/>
  <Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>
  <Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>
  <Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>
  <Override PartName="/xl/sharedStrings.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sharedStrings+xml"/>
</Types>');

        // _rels/.rels
        $zip->addFromString('_rels/.rels', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
  <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>
</Relationships>');

        // xl/_rels/workbook.xml.rels
        $zip->addFromString('xl/_rels/workbook.xml.rels', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
  <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>
  <Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>
  <Relationship Id="rId3" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/sharedStrings" Target="sharedStrings.xml"/>
</Relationships>');

        // xl/workbook.xml
        $zip->addFromString('xl/workbook.xml', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">
  <sheets>
    <sheet name="Toners" sheetId="1" r:id="rId1"/>
  </sheets>
</workbook>');

        // xl/styles.xml
        $zip->addFromString('xl/styles.xml', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">
  <fonts count="2">
    <font><sz val="11"/><name val="Calibri"/></font>
    <font><sz val="11"/><name val="Calibri"/><b/><color rgb="FFFFFFFF"/></font>
  </fonts>
  <fills count="3">
    <fill><patternFill patternType="none"/></fill>
    <fill><patternFill patternType="gray125"/></fill>
    <fill><patternFill patternType="solid"><fgColor rgb="FF4472C4"/></patternFill></fill>
  </fills>
  <borders count="2">
    <border><left/><right/><top/><bottom/></border>
    <border><left style="thin"/><right style="thin"/><top style="thin"/><bottom style="thin"/></border>
  </borders>
  <cellXfs count="3">
    <xf numFmtId="0" fontId="0" fillId="0" borderId="0"/>
    <xf numFmtId="0" fontId="1" fillId="2" borderId="1" applyFont="1" applyFill="1" applyBorder="1"/>
    <xf numFmtId="2" fontId="0" fillId="0" borderId="1" applyBorder="1" applyNumberFormat="1"/>
  </cellXfs>
</styleSheet>');

        // xl/sharedStrings.xml
        $sharedStringsXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<sst xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" count="' . count($sharedStrings) . '" uniqueCount="' . count($sharedStrings) . '">';
        
        foreach ($sharedStrings as $string) {
            $sharedStringsXml .= '<si><t>' . htmlspecialchars($string, ENT_XML1) . '</t></si>';
        }
        $sharedStringsXml .= '</sst>';
        
        $zip->addFromString('xl/sharedStrings.xml', $sharedStringsXml);

        // xl/worksheets/sheet1.xml
        $worksheetXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">
  <sheetData>';
        
        // Cabeçalho
        $worksheetXml .= '<row r="1">';
        foreach ($headers as $index => $header) {
            $col = chr(65 + $index);
            $stringIndex = array_search($header, $sharedStrings);
            $worksheetXml .= '<c r="' . $col . '1" s="1" t="s"><v>' . $stringIndex . '</v></c>';
        }
        $worksheetXml .= '</row>';
        
        // Dados
        foreach ($toners as $rowIndex => $toner) {
            $rowNum = $rowIndex + 2;
            $worksheetXml .= '<row r="' . $rowNum . '">';
            
            $colIndex = 0;
            
            // Dados básicos
            $data = [
                $toner['id'],
                $toner['modelo'],
                $toner['cor'],
                $toner['tipo'],
                $toner['capacidade'],
                $toner['peso_cheio'],
                $toner['peso_vazio'],
                $toner['preco']
            ];
            
            if ($includeCalculated) {
                $data = array_merge($data, [
                    $toner['gramatura'],
                    $toner['gramatura_folha'],
                    $toner['preco_folha']
                ]);
            }
            
            if ($includeDates) {
                $data = array_merge($data, [
                    date('d/m/Y H:i', strtotime($toner['created_at'])),
                    date('d/m/Y H:i', strtotime($toner['updated_at']))
                ]);
            }
            
            foreach ($data as $value) {
                $col = chr(65 + $colIndex);
                
                if (is_numeric($value) && $colIndex > 1) {
                    $worksheetXml .= '<c r="' . $col . $rowNum . '" s="2"><v>' . $value . '</v></c>';
                } else {
                    $stringIndex = array_search($value, $sharedStrings);
                    if ($stringIndex !== false) {
                        $worksheetXml .= '<c r="' . $col . $rowNum . '" t="s"><v>' . $stringIndex . '</v></c>';
                    } else {
                        $worksheetXml .= '<c r="' . $col . $rowNum . '" t="inlineStr"><is><t>' . htmlspecialchars($value, ENT_XML1) . '</t></is></c>';
                    }
                }
                $colIndex++;
            }
            
            $worksheetXml .= '</row>';
        }
        
        $worksheetXml .= '</sheetData></worksheet>';
        
        $zip->addFromString('xl/worksheets/sheet1.xml', $worksheetXml);
        
        $zip->close();
        
        // Enviar arquivo
        readfile($tempFile);
        unlink($tempFile);
    } else {
        throw new Exception('Erro ao criar arquivo Excel');
    }
}

function exportToCSV($toners, $includeCalculated, $includeDates) {
    $filename = 'Toners_SGQ_' . date('Y-m-d_H-i-s') . '.csv';
    
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Cache-Control: max-age=0');
    
    // BOM para UTF-8
    echo "\xEF\xBB\xBF";
    
    // Definir cabeçalhos
    $headers = ['ID', 'Modelo', 'Cor', 'Tipo', 'Capacidade (páginas)', 'Peso Cheio (g)', 'Peso Vazio (g)', 'Preço (R$)'];
    
    if ($includeCalculated) {
        $headers = array_merge($headers, ['Gramatura (g)', 'Gramatura por Folha (g)', 'Preço por Folha (R$)']);
    }
    
    if ($includeDates) {
        $headers = array_merge($headers, ['Data Criação', 'Data Atualização']);
    }
    
    // Escrever cabeçalho
    echo implode(';', $headers) . "\r\n";
    
    // Escrever dados
    foreach ($toners as $toner) {
        $row = [
            $toner['id'],
            $toner['modelo'],
            $toner['cor'],
            $toner['tipo'],
            $toner['capacidade'],
            str_replace('.', ',', $toner['peso_cheio']),
            str_replace('.', ',', $toner['peso_vazio']),
            str_replace('.', ',', $toner['preco'])
        ];
        
        if ($includeCalculated) {
            $row = array_merge($row, [
                str_replace('.', ',', $toner['gramatura']),
                str_replace('.', ',', $toner['gramatura_folha']),
                str_replace('.', ',', $toner['preco_folha'])
            ]);
        }
        
        if ($includeDates) {
            $row = array_merge($row, [
                date('d/m/Y H:i', strtotime($toner['created_at'])),
                date('d/m/Y H:i', strtotime($toner['updated_at']))
            ]);
        }
        
        // Escapar campos que contêm separadores
        $escapedRow = array_map(function($field) {
            if (strpos($field, ';') !== false || strpos($field, '"') !== false || strpos($field, "\n") !== false) {
                return '"' . str_replace('"', '""', $field) . '"';
            }
            return $field;
        }, $row);
        
        echo implode(';', $escapedRow) . "\r\n";
    }
}
?>
