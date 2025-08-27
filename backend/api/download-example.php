<?php
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="exemplo_toners.xlsx"');
header('Cache-Control: max-age=0');

// Criar planilha Excel simples sem bibliotecas externas
// Vamos usar um formato XML que o Excel reconhece

$excelData = '<?xml version="1.0" encoding="UTF-8"?>
<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"
 xmlns:o="urn:schemas-microsoft-com:office:office"
 xmlns:x="urn:schemas-microsoft-com:office:excel"
 xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"
 xmlns:html="http://www.w3.org/TR/REC-html40">
 <DocumentProperties xmlns="urn:schemas-microsoft-com:office:office">
  <Title>Exemplo Toners SGQ OTI</Title>
  <Author>SGQ OTI System</Author>
  <Created>' . date('Y-m-d\TH:i:s\Z') . '</Created>
 </DocumentProperties>
 <Styles>
  <Style ss:ID="header">
   <Font ss:Bold="1" ss:Size="12"/>
   <Interior ss:Color="#4472C4" ss:Pattern="Solid"/>
   <Font ss:Color="#FFFFFF"/>
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1"/>
   </Borders>
  </Style>
  <Style ss:ID="data">
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1"/>
   </Borders>
  </Style>
  <Style ss:ID="example">
   <Interior ss:Color="#E7E6E6" ss:Pattern="Solid"/>
   <Font ss:Italic="1"/>
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1"/>
   </Borders>
  </Style>
 </Styles>
 <Worksheet ss:Name="Toners">
  <Table>
   <Column ss:Width="120"/>
   <Column ss:Width="80"/>
   <Column ss:Width="100"/>
   <Column ss:Width="80"/>
   <Column ss:Width="100"/>
   <Column ss:Width="100"/>
   <Column ss:Width="80"/>
   <Row ss:Height="25">
    <Cell ss:StyleID="header"><Data ss:Type="String">Modelo</Data></Cell>
    <Cell ss:StyleID="header"><Data ss:Type="String">Cor</Data></Cell>
    <Cell ss:StyleID="header"><Data ss:Type="String">Tipo</Data></Cell>
    <Cell ss:StyleID="header"><Data ss:Type="String">Capacidade</Data></Cell>
    <Cell ss:StyleID="header"><Data ss:Type="String">Peso Cheio (g)</Data></Cell>
    <Cell ss:StyleID="header"><Data ss:Type="String">Peso Vazio (g)</Data></Cell>
    <Cell ss:StyleID="header"><Data ss:Type="String">Preço (R$)</Data></Cell>
   </Row>
   <Row ss:Height="20">
    <Cell ss:StyleID="example"><Data ss:Type="String">HP CF280A</Data></Cell>
    <Cell ss:StyleID="example"><Data ss:Type="String">Black</Data></Cell>
    <Cell ss:StyleID="example"><Data ss:Type="String">Original</Data></Cell>
    <Cell ss:StyleID="example"><Data ss:Type="Number">2700</Data></Cell>
    <Cell ss:StyleID="example"><Data ss:Type="Number">1200.5</Data></Cell>
    <Cell ss:StyleID="example"><Data ss:Type="Number">180.2</Data></Cell>
    <Cell ss:StyleID="example"><Data ss:Type="Number">89.90</Data></Cell>
   </Row>
   <Row ss:Height="20">
    <Cell ss:StyleID="example"><Data ss:Type="String">HP CE285A</Data></Cell>
    <Cell ss:StyleID="example"><Data ss:Type="String">Black</Data></Cell>
    <Cell ss:StyleID="example"><Data ss:Type="String">Compativel</Data></Cell>
    <Cell ss:StyleID="example"><Data ss:Type="Number">1600</Data></Cell>
    <Cell ss:StyleID="example"><Data ss:Type="Number">950.0</Data></Cell>
    <Cell ss:StyleID="example"><Data ss:Type="Number">165.5</Data></Cell>
    <Cell ss:StyleID="example"><Data ss:Type="Number">45.50</Data></Cell>
   </Row>
   <Row ss:Height="20">
    <Cell ss:StyleID="example"><Data ss:Type="String">Canon 728</Data></Cell>
    <Cell ss:StyleID="example"><Data ss:Type="String">Black</Data></Cell>
    <Cell ss:StyleID="example"><Data ss:Type="String">Remanufaturado</Data></Cell>
    <Cell ss:StyleID="example"><Data ss:Type="Number">2100</Data></Cell>
    <Cell ss:StyleID="example"><Data ss:Type="Number">1100.8</Data></Cell>
    <Cell ss:StyleID="example"><Data ss:Type="Number">175.3</Data></Cell>
    <Cell ss:StyleID="example"><Data ss:Type="Number">65.00</Data></Cell>
   </Row>
   <Row ss:Height="20">
    <Cell ss:StyleID="example"><Data ss:Type="String">HP CF541A</Data></Cell>
    <Cell ss:StyleID="example"><Data ss:Type="String">Cyan</Data></Cell>
    <Cell ss:StyleID="example"><Data ss:Type="String">Original</Data></Cell>
    <Cell ss:StyleID="example"><Data ss:Type="Number">1300</Data></Cell>
    <Cell ss:StyleID="example"><Data ss:Type="Number">850.2</Data></Cell>
    <Cell ss:StyleID="example"><Data ss:Type="Number">150.1</Data></Cell>
    <Cell ss:StyleID="example"><Data ss:Type="Number">125.90</Data></Cell>
   </Row>';

// Adicionar 10 linhas vazias para preenchimento
for ($i = 0; $i < 10; $i++) {
    $excelData .= '
   <Row ss:Height="20">
    <Cell ss:StyleID="data"><Data ss:Type="String"></Data></Cell>
    <Cell ss:StyleID="data"><Data ss:Type="String"></Data></Cell>
    <Cell ss:StyleID="data"><Data ss:Type="String"></Data></Cell>
    <Cell ss:StyleID="data"><Data ss:Type="Number"></Data></Cell>
    <Cell ss:StyleID="data"><Data ss:Type="Number"></Data></Cell>
    <Cell ss:StyleID="data"><Data ss:Type="Number"></Data></Cell>
    <Cell ss:StyleID="data"><Data ss:Type="Number"></Data></Cell>
   </Row>';
}

$excelData .= '
  </Table>
  <WorksheetOptions xmlns="urn:schemas-microsoft-com:office:excel">
   <Selected/>
   <FreezePanes/>
   <FrozenNoSplit/>
   <SplitHorizontal>1</SplitHorizontal>
   <TopRowBottomPane>1</TopRowBottomPane>
   <ActivePane>2</ActivePane>
  </WorksheetOptions>
 </Worksheet>
 <Worksheet ss:Name="Instruções">
  <Table>
   <Column ss:Width="500"/>
   <Row ss:Height="25">
    <Cell ss:StyleID="header"><Data ss:Type="String">INSTRUÇÕES PARA PREENCHIMENTO</Data></Cell>
   </Row>
   <Row ss:Height="20">
    <Cell ss:StyleID="data"><Data ss:Type="String">1. Preencha os dados na aba "Toners"</Data></Cell>
   </Row>
   <Row ss:Height="20">
    <Cell ss:StyleID="data"><Data ss:Type="String">2. Modelo: Digite o modelo do toner (ex: HP CF280A)</Data></Cell>
   </Row>
   <Row ss:Height="20">
    <Cell ss:StyleID="data"><Data ss:Type="String">3. Cor: Use apenas: Black, Cyan, Magenta, Yellow</Data></Cell>
   </Row>
   <Row ss:Height="20">
    <Cell ss:StyleID="data"><Data ss:Type="String">4. Tipo: Use apenas: Original, Compativel, Remanufaturado</Data></Cell>
   </Row>
   <Row ss:Height="20">
    <Cell ss:StyleID="data"><Data ss:Type="String">5. Capacidade: Número de folhas (ex: 2700)</Data></Cell>
   </Row>
   <Row ss:Height="20">
    <Cell ss:StyleID="data"><Data ss:Type="String">6. Peso Cheio: Peso em gramas com decimal (ex: 1200.5)</Data></Cell>
   </Row>
   <Row ss:Height="20">
    <Cell ss:StyleID="data"><Data ss:Type="String">7. Peso Vazio: Peso em gramas com decimal (ex: 180.2)</Data></Cell>
   </Row>
   <Row ss:Height="20">
    <Cell ss:StyleID="data"><Data ss:Type="String">8. Preço: Valor em reais com decimal (ex: 89.90)</Data></Cell>
   </Row>
   <Row ss:Height="20">
    <Cell ss:StyleID="data"><Data ss:Type="String">9. Salve o arquivo e importe no sistema</Data></Cell>
   </Row>
  </Table>
 </Worksheet>
</Workbook>';

echo $excelData;
?>
