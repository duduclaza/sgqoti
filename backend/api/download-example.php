<?php
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="Planilha_Importacao_Toners_SGQ.xlsx"');
header('Cache-Control: max-age=0');

// Criar arquivo Excel XML nativo (sem bibliotecas externas)
$xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">
  <sheets>
    <sheet name="Dados dos Toners" sheetId="1" r:id="rId1"/>
    <sheet name="Instruções" sheetId="2" r:id="rId2"/>
    <sheet name="Exemplos" sheetId="3" r:id="rId3"/>
  </sheets>
</workbook>';

// Criar estrutura ZIP do Excel
$zip = new ZipArchive();
$tempFile = tempnam(sys_get_temp_dir(), 'excel_');

if ($zip->open($tempFile, ZipArchive::CREATE) === TRUE) {
    
    // [Content_Types].xml
    $zip->addFromString('[Content_Types].xml', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">
  <Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>
  <Default Extension="xml" ContentType="application/xml"/>
  <Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>
  <Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>
  <Override PartName="/xl/worksheets/sheet2.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>
  <Override PartName="/xl/worksheets/sheet3.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>
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
  <Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet2.xml"/>
  <Relationship Id="rId3" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet3.xml"/>
  <Relationship Id="rId4" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>
  <Relationship Id="rId5" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/sharedStrings" Target="sharedStrings.xml"/>
</Relationships>');

    // xl/workbook.xml
    $zip->addFromString('xl/workbook.xml', $xml);

    // xl/styles.xml - Estilos com cores e formatação
    $zip->addFromString('xl/styles.xml', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">
  <fonts count="4">
    <font><sz val="11"/><name val="Calibri"/></font>
    <font><sz val="11"/><name val="Calibri"/><b/><color rgb="FFFFFFFF"/></font>
    <font><sz val="12"/><name val="Calibri"/><b/></font>
    <font><sz val="10"/><name val="Calibri"/><i/><color rgb="FF666666"/></font>
  </fonts>
  <fills count="6">
    <fill><patternFill patternType="none"/></fill>
    <fill><patternFill patternType="gray125"/></fill>
    <fill><patternFill patternType="solid"><fgColor rgb="FF4472C4"/></patternFill></fill>
    <fill><patternFill patternType="solid"><fgColor rgb="FFF2F2F2"/></patternFill></fill>
    <fill><patternFill patternType="solid"><fgColor rgb="FFFCE4D6"/></patternFill></fill>
    <fill><patternFill patternType="solid"><fgColor rgb="FFE2EFDA"/></patternFill></fill>
  </fills>
  <borders count="2">
    <border><left/><right/><top/><bottom/></border>
    <border><left style="thin"/><right style="thin"/><top style="thin"/><bottom style="thin"/></border>
  </borders>
  <cellXfs count="6">
    <xf numFmtId="0" fontId="0" fillId="0" borderId="0"/>
    <xf numFmtId="0" fontId="1" fillId="2" borderId="1" applyFont="1" applyFill="1" applyBorder="1"/>
    <xf numFmtId="0" fontId="2" fillId="0" borderId="0" applyFont="1"/>
    <xf numFmtId="0" fontId="3" fillId="3" borderId="1" applyFont="1" applyFill="1" applyBorder="1"/>
    <xf numFmtId="2" fontId="0" fillId="0" borderId="1" applyBorder="1" applyNumberFormat="1"/>
    <xf numFmtId="0" fontId="0" fillId="0" borderId="1" applyBorder="1"/>
  </cellXfs>
</styleSheet>');

    // xl/sharedStrings.xml
    $sharedStrings = [
        'Modelo do Toner', 'Cor', 'Tipo', 'Capacidade (páginas)', 'Peso Cheio (g)', 'Peso Vazio (g)', 'Preço (R$)',
        'HP CF280A', 'Black', 'Original', 'HP CE285A', 'Compatível', 'Canon 728', 'Remanufaturado', 'Cyan',
        '🔹 INSTRUÇÕES DE PREENCHIMENTO', 
        '1. MODELO: Digite o código exato do toner (ex: HP CF280A, Canon 728)',
        '2. COR: Escolha entre Black, Cyan, Magenta, Yellow',
        '3. TIPO: Escolha entre Original, Compatível, Remanufaturado',
        '4. CAPACIDADE: Número de páginas que o toner imprime',
        '5. PESO CHEIO: Peso do toner novo em gramas (use vírgula para decimais)',
        '6. PESO VAZIO: Peso do toner vazio em gramas (use vírgula para decimais)',
        '7. PREÇO: Valor em reais (use vírgula para decimais)',
        '',
        '⚠️ IMPORTANTE:',
        '• Use vírgula (,) para separar decimais, não ponto (.)',
        '• Não deixe campos obrigatórios vazios',
        '• Certifique-se que Peso Cheio > Peso Vazio',
        '• Valores devem ser números positivos',
        '',
        '✅ APÓS PREENCHER:',
        '• Salve o arquivo no formato Excel (.xlsx)',
        '• Faça o upload no sistema SGQ OTI',
        '• Aguarde a confirmação da importação'
    ];
    
    $sharedStringsXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<sst xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" count="' . count($sharedStrings) . '" uniqueCount="' . count($sharedStrings) . '">';
    
    foreach ($sharedStrings as $string) {
        $sharedStringsXml .= '<si><t>' . htmlspecialchars($string, ENT_XML1) . '</t></si>';
    }
    $sharedStringsXml .= '</sst>';
    
    $zip->addFromString('xl/sharedStrings.xml', $sharedStringsXml);

    // Sheet 1 - Dados dos Toners (Planilha principal para preenchimento)
    $zip->addFromString('xl/worksheets/sheet1.xml', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">
  <sheetData>
    <row r="1">
      <c r="A1" s="1" t="inlineStr"><is><t>Modelo do Toner</t></is></c>
      <c r="B1" s="1" t="inlineStr"><is><t>Cor</t></is></c>
      <c r="C1" s="1" t="inlineStr"><is><t>Tipo</t></is></c>
      <c r="D1" s="1" t="inlineStr"><is><t>Capacidade (páginas)</t></is></c>
      <c r="E1" s="1" t="inlineStr"><is><t>Peso Cheio (g)</t></is></c>
      <c r="F1" s="1" t="inlineStr"><is><t>Peso Vazio (g)</t></is></c>
      <c r="G1" s="1" t="inlineStr"><is><t>Preço (R$)</t></is></c>
    </row>
    <row r="2"><c r="A2" s="5"/><c r="B2" s="5"/><c r="C2" s="5"/><c r="D2" s="5"/><c r="E2" s="4"/><c r="F2" s="4"/><c r="G2" s="4"/></row>
    <row r="3"><c r="A3" s="5"/><c r="B3" s="5"/><c r="C3" s="5"/><c r="D3" s="5"/><c r="E3" s="4"/><c r="F3" s="4"/><c r="G3" s="4"/></row>
    <row r="4"><c r="A4" s="5"/><c r="B4" s="5"/><c r="C4" s="5"/><c r="D4" s="5"/><c r="E4" s="4"/><c r="F4" s="4"/><c r="G4" s="4"/></row>
    <row r="5"><c r="A5" s="5"/><c r="B5" s="5"/><c r="C5" s="5"/><c r="D5" s="5"/><c r="E5" s="4"/><c r="F5" s="4"/><c r="G5" s="4"/></row>
    <row r="6"><c r="A6" s="5"/><c r="B6" s="5"/><c r="C6" s="5"/><c r="D6" s="5"/><c r="E6" s="4"/><c r="F6" s="4"/><c r="G6" s="4"/></row>
    <row r="7"><c r="A7" s="5"/><c r="B7" s="5"/><c r="C7" s="5"/><c r="D7" s="5"/><c r="E7" s="4"/><c r="F7" s="4"/><c r="G7" s="4"/></row>
    <row r="8"><c r="A8" s="5"/><c r="B8" s="5"/><c r="C8" s="5"/><c r="D8" s="5"/><c r="E8" s="4"/><c r="F8" s="4"/><c r="G8" s="4"/></row>
    <row r="9"><c r="A9" s="5"/><c r="B9" s="5"/><c r="C9" s="5"/><c r="D9" s="5"/><c r="E9" s="4"/><c r="F9" s="4"/><c r="G9" s="4"/></row>
    <row r="10"><c r="A10" s="5"/><c r="B10" s="5"/><c r="C10" s="5"/><c r="D10" s="5"/><c r="E10" s="4"/><c r="F10" s="4"/><c r="G10" s="4"/></row>
    <row r="11"><c r="A11" s="5"/><c r="B11" s="5"/><c r="C11" s="5"/><c r="D11" s="5"/><c r="E11" s="4"/><c r="F11" s="4"/><c r="G11" s="4"/></row>
    <row r="12"><c r="A12" s="5"/><c r="B12" s="5"/><c r="C12" s="5"/><c r="D12" s="5"/><c r="E12" s="4"/><c r="F12" s="4"/><c r="G12" s="4"/></row>
    <row r="13"><c r="A13" s="5"/><c r="B13" s="5"/><c r="C13" s="5"/><c r="D13" s="5"/><c r="E13" s="4"/><c r="F13" s="4"/><c r="G13" s="4"/></row>
    <row r="14"><c r="A14" s="5"/><c r="B14" s="5"/><c r="C14" s="5"/><c r="D14" s="5"/><c r="E14" s="4"/><c r="F14" s="4"/><c r="G14" s="4"/></row>
    <row r="15"><c r="A15" s="5"/><c r="B15" s="5"/><c r="C15" s="5"/><c r="D15" s="5"/><c r="E15" s="4"/><c r="F15" s="4"/><c r="G15" s="4"/></row>
    <row r="16"><c r="A16" s="5"/><c r="B16" s="5"/><c r="C16" s="5"/><c r="D16" s="5"/><c r="E16" s="4"/><c r="F16" s="4"/><c r="G16" s="4"/></row>
    <row r="17"><c r="A17" s="5"/><c r="B17" s="5"/><c r="C17" s="5"/><c r="D17" s="5"/><c r="E17" s="4"/><c r="F17" s="4"/><c r="G17" s="4"/></row>
    <row r="18"><c r="A18" s="5"/><c r="B18" s="5"/><c r="C18" s="5"/><c r="D18" s="5"/><c r="E18" s="4"/><c r="F18" s="4"/><c r="G18" s="4"/></row>
    <row r="19"><c r="A19" s="5"/><c r="B19" s="5"/><c r="C19" s="5"/><c r="D19" s="5"/><c r="E19" s="4"/><c r="F19" s="4"/><c r="G19" s="4"/></row>
    <row r="20"><c r="A20" s="5"/><c r="B20" s="5"/><c r="C20" s="5"/><c r="D20" s="5"/><c r="E20" s="4"/><c r="F20" s="4"/><c r="G20" s="4"/></row>
  </sheetData>
  <cols>
    <col min="1" max="1" width="20"/>
    <col min="2" max="2" width="12"/>
    <col min="3" max="3" width="15"/>
    <col min="4" max="4" width="18"/>
    <col min="5" max="5" width="15"/>
    <col min="6" max="6" width="15"/>
    <col min="7" max="7" width="12"/>
  </cols>
</worksheet>');

    // Sheet 2 - Instruções
    $zip->addFromString('xl/worksheets/sheet2.xml', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">
  <sheetData>
    <row r="1"><c r="A1" s="2" t="s"><v>15</v></c></row>
    <row r="2"><c r="A2" s="0" t="s"><v>24</v></c></row>
    <row r="3"><c r="A3" s="0" t="s"><v>16</v></c></row>
    <row r="4"><c r="A4" s="0" t="s"><v>17</v></c></row>
    <row r="5"><c r="A5" s="0" t="s"><v>18</v></c></row>
    <row r="6"><c r="A6" s="0" t="s"><v>19</v></c></row>
    <row r="7"><c r="A7" s="0" t="s"><v>20</v></c></row>
    <row r="8"><c r="A8" s="0" t="s"><v>21</v></c></row>
    <row r="9"><c r="A9" s="0" t="s"><v>22</v></c></row>
    <row r="10"><c r="A10" s="0" t="s"><v>23</v></c></row>
    <row r="11"><c r="A11" s="0" t="s"><v>24</v></c></row>
    <row r="12"><c r="A12" s="2" t="s"><v>25</v></c></row>
    <row r="13"><c r="A13" s="0" t="s"><v>26</v></c></row>
    <row r="14"><c r="A14" s="0" t="s"><v>27</v></c></row>
    <row r="15"><c r="A15" s="0" t="s"><v>28</v></c></row>
    <row r="16"><c r="A16" s="0" t="s"><v>29</v></c></row>
    <row r="17"><c r="A17" s="0" t="s"><v>24</v></c></row>
    <row r="18"><c r="A18" s="2" t="s"><v>30</v></c></row>
    <row r="19"><c r="A19" s="0" t="s"><v>31</v></c></row>
    <row r="20"><c r="A20" s="0" t="s"><v>32</v></c></row>
    <row r="21"><c r="A21" s="0" t="s"><v>33</v></c></row>
  </sheetData>
  <cols>
    <col min="1" max="1" width="60"/>
  </cols>
</worksheet>');

    // Sheet 3 - Exemplos
    $zip->addFromString('xl/worksheets/sheet3.xml', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">
  <sheetData>
    <row r="1">
      <c r="A1" s="1" t="s"><v>0</v></c>
      <c r="B1" s="1" t="s"><v>1</v></c>
      <c r="C1" s="1" t="s"><v>2</v></c>
      <c r="D1" s="1" t="s"><v>3</v></c>
      <c r="E1" s="1" t="s"><v>4</v></c>
      <c r="F1" s="1" t="s"><v>5</v></c>
      <c r="G1" s="1" t="s"><v>6</v></c>
    </row>
    <row r="2">
      <c r="A2" s="3" t="s"><v>7</v></c>
      <c r="B2" s="3" t="s"><v>8</v></c>
      <c r="C2" s="3" t="s"><v>9</v></c>
      <c r="D2" s="3"><v>2700</v></c>
      <c r="E2" s="3"><v>1200.5</v></c>
      <c r="F2" s="3"><v>180.2</v></c>
      <c r="G2" s="3"><v>89.90</v></c>
    </row>
    <row r="3">
      <c r="A3" s="3" t="s"><v>10</v></c>
      <c r="B3" s="3" t="s"><v>8</v></c>
      <c r="C3" s="3" t="s"><v>11</v></c>
      <c r="D3" s="3"><v>1600</v></c>
      <c r="E3" s="3"><v>950.0</v></c>
      <c r="F3" s="3"><v>165.5</v></c>
      <c r="G3" s="3"><v>45.50</v></c>
    </row>
    <row r="4">
      <c r="A4" s="3" t="s"><v>12</v></c>
      <c r="B4" s="3" t="s"><v>8</v></c>
      <c r="C4" s="3" t="s"><v>13</v></c>
      <c r="D4" s="3"><v>2100</v></c>
      <c r="E4" s="3"><v>1100.8</v></c>
      <c r="F4" s="3"><v>175.3</v></c>
      <c r="G4" s="3"><v>65.00</v></c>
    </row>
    <row r="5">
      <c r="A5" s="3" t="inlineStr"><is><t>HP CF541A</t></is></c>
      <c r="B5" s="3" t="s"><v>14</v></c>
      <c r="C5" s="3" t="s"><v>9</v></c>
      <c r="D5" s="3"><v>1300</v></c>
      <c r="E5" s="3"><v>850.2</v></c>
      <c r="F5" s="3"><v>150.1</v></c>
      <c r="G5" s="3"><v>125.90</v></c>
    </row>
  </sheetData>
  <cols>
    <col min="1" max="1" width="20"/>
    <col min="2" max="2" width="12"/>
    <col min="3" max="3" width="15"/>
    <col min="4" max="4" width="18"/>
    <col min="5" max="5" width="15"/>
    <col min="6" max="6" width="15"/>
    <col min="7" max="7" width="12"/>
  </cols>
</worksheet>');

    $zip->close();
    
    // Enviar arquivo
    readfile($tempFile);
    unlink($tempFile);
} else {
    // Fallback para CSV se ZIP falhar
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="planilha_toners_sgq.csv"');
    echo "\xEF\xBB\xBF";
    echo "Modelo do Toner;Cor;Tipo;Capacidade (páginas);Peso Cheio (g);Peso Vazio (g);Preço (R$)\r\n";
    for ($i = 0; $i < 20; $i++) {
        echo ";;;;;;;\r\n";
    }
}
?>
