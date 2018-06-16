<?
/**
 *
 * 26/02/2018 - criado por jose.vieira - CAST
 *
 */

require_once dirname(__FILE__) . '/../../../SEI.php';

class MdPetGraficoINT extends InfraINT
{

  private static $_qtdDados;
  private static $_html;
  private static $_scriptImportado = false;

  public static $_tipoBar = 'bar';
  public static $_tipoHorizontalBar = 'horizontalBar';
  public static $_tipoPie = 'pie';
  public static $_tipoLine = 'line';
  public static $_tipoRadar = 'radar';
  public static $_tipoPolarArea = 'polarArea';

  private static function scripts()
  {
    if (!self::$_scriptImportado) {
      self::$_html = '<script src="modulos/peticionamento/js/Chart.js"></script>';
      self::css();
      self::$_scriptImportado = true;
    }
  }

  private static function css()
  {
    self::$_html .= '<style>';
    self::$_html .= '.legendaGrafico {margin-top: 8px;border: 1px black solid; width:270px !important; display: table}';
    self::$_html .= '.legendaGrafico ul{list-style-type: none; padding-left: 3px;}';
    self::$_html .= '.legendaGrafico li{margin-bottom:2px; vertical-align: middle; height: 100%;}';
    self::$_html .= '.legendaGrafico span{ margin-right:10px; padding: 5px; border: 1px black solid; font-size:12pt; display: inline-block; width: 5px}';

    self::$_html .= '</style>';
  }

  private static function jsGerarBar($idGrafico, $arrDados, $tipoBarra)
  {
    $idLegenda = 'legenda_'.$idGrafico;
    self::$_html .= '<script>';
    self::$_html .= 'var ctx = document.getElementById("' . $idGrafico . '");';
    self::$_html .= 'var data = {';
    self::$_html .= 'datasets:[';
    $contador = 1;
    foreach ($arrDados as $dados) {
      self::$_html .= '{';
      self::$_html .= 'data: [';
      self::$_html .= '"' . $dados['valor'] . '"';
      self::$_html .= '],';
      self::$_html .= '"label":["' . $dados['label'] . '"],';
      self::$_html .= 'backgroundColor: [';
      self::$_html .= '"' . $dados['cor'] . '"';
      self::$_html .= ' ]';
      self::$_html .= ' }';

      if ($contador < self::$_qtdDados) {
        self::$_html .= ' ,';
      }
      $contador++;
    }
    self::$_html .= ']';
    self::$_html .= '};';

    self::$_html .= 'var myChart = new Chart(ctx, {';
    self::$_html .= "type: '" . $tipoBarra . "',";
    self::$_html .= "data: data,";
    self::$_html .= " options: {
      legend: false,
        scales: {
          xAxes:[{
            barPercentage : 0.5
          }],
          yAxes: [{
            ticks: {
              beginAtZero: true
            }
          }]
        },
         tooltips: {
          mode: 'single',
          callbacks: {
            label: function(tooltipItem, data) {
              var label = data.datasets[tooltipItem.datasetIndex].label;
              var labelArr =label[0].match(/\b[\w']+(?:[^\w]+[\w']+){0,3}\b/g);
               var value = data.datasets[tooltipItem.datasetIndex].data[tooltipItem.index];
               labelArr[labelArr.length - 1] += ': ' + value;
               if(labelArr.length > 0){
                return labelArr[0];
               }else{
                return labelArr;
               }
            },
            footer: function(tooltipItems, data) {
              var label = data.datasets[tooltipItems[0].datasetIndex].label;
              var labelArr =label[0].match(/\b[\w']+(?:[^\w]+[\w']+){0,3}\b/g);
              var value = data.datasets[tooltipItems[0].datasetIndex].data[tooltipItems[0].index];
              labelArr[labelArr.length - 1] += ': ' + value;
              if(labelArr.length > 0){
                delete labelArr[0];
              }
              return labelArr[1];
            }
         }
}
    }";
    self::$_html .= "});";
    self::$_html .= "document.getElementById('" . $idLegenda . "').innerHTML = myChart.generateLegend();";
    self::$_html .= '</script>';
  }

  private static function jsGerarPie($idGrafico, $arrDados)
  {
    $idLegenda = 'legenda_'.$idGrafico;
    $arrValor = [];
    $arrLabel = [];
    $arrCor = [];
    foreach ($arrDados as $dados){
      $arrValor[] = $dados['valor'];
      $arrLabel[] = $dados['label'];
      $arrCor[] = $dados['cor'];
    }
    self::$_html .= '<script>';
    self::$_html .= 'var ctx = document.getElementById("' . $idGrafico . '");';
    self::$_html .= 'var data = {';
    self::$_html .= 'labels: ["'.implode('","',$arrLabel).'"],';
    self::$_html .= 'datasets: [{';
    self::$_html .= 'data: ['.implode(',',$arrValor).'],';
    self::$_html .= 'backgroundColor: ["'.implode('","',$arrCor).'"]';
    self::$_html .= '}]';
    self::$_html .= '};';

    self::$_html .= 'var myChart = new Chart(ctx, {';
    self::$_html .= "type: '" . self::$_tipoPie . "',";
    self::$_html .= "data: data,";
    self::$_html .= " options: {
      legend: false,";

    self::$_html .= "tooltips: {
          mode: 'label',
          callbacks: {
            label: function(tooltipItem, data) {
              var label = data.labels[tooltipItem.index];
              var labelArr =label.match(/\b[\w']+(?:[^\w]+[\w']+){0,3}\b/g);
              var value = data.datasets[0].data[tooltipItem.index];
              labelArr[labelArr.length - 1] += ': ' + value;
               if(labelArr.length > 0){
                return labelArr[0];
               }else{
                return labelArr;
               }
            },
            footer: function(tooltipItems, data) {
              var label = data.labels[tooltipItems[0].index];
              var labelArr =label.match(/\b[\w']+(?:[^\w]+[\w']+){0,3}\b/g);
              var value = data.datasets[0].data[tooltipItems[0].index];
              
              labelArr[labelArr.length - 1] += ': ' + value;
              if(labelArr.length > 0){
                delete labelArr[0];
              }
              return labelArr[1];
              
            }
         }
		 }
    ";
    self::$_html .="}";
    self::$_html .= "});";
    self::$_html .= "document.getElementById('".$idLegenda."').innerHTML = myChart.generateLegend();";
    self::$_html .= '</script>';
  }

  private static function jsGerarRadar($idGrafico, $arrDados)
  {
    $idLegenda = 'legenda_'.$idGrafico;
    $arrValor = [];
    $arrLabel = [];
    $arrCor = [];
    foreach ($arrDados as $dados){
      $arrValor[] = $dados['valor'];
      $arrLabel[] = $dados['label'];
      $arrCor[] = $dados['cor'];
    }
    self::$_html .= '<script>';
    self::$_html .= 'var ctx = document.getElementById("' . $idGrafico . '");';
    self::$_html .= 'var data = {';
    self::$_html .= 'labels: ["'.implode('","',$arrLabel).'"],';
    self::$_html .= 'datasets: [{';
    self::$_html .= '"fill":true,';
    self::$_html .= '"pointRadius": 8,';
    self::$_html .= 'pointHoverRadius: 10,';
    self::$_html .= 'data: ['.implode(',',$arrValor).'],';
    self::$_html .= '"borderColor":"rgb(255, 99, 132)",';
    self::$_html .= '"pointBackgroundColor":["'.implode('","',$arrCor).'"],';
    self::$_html .= '"backgroundColor":"rgba(255, 99, 132, 0.2)"';
    self::$_html .= '}]';
    self::$_html .= '};';

    self::$_html .= 'var myChart = new Chart(ctx, {';
    self::$_html .= "type: '" . self::$_tipoRadar . "',";
    self::$_html .= "data: data,";
    self::$_html .= " options: {
      legend: false,
      scale: {
       pointLabels: {
            callback: function(pointLabel, index, labels) {
                return ' ';
            } 
        }
      },";
    self::$_html .= "tooltips: {
          mode: 'label',
          callbacks: {
            label: function(tooltipItem, data) {
              var label = data.labels[tooltipItem.index];
              var labelArr =label.match(/\b[\w']+(?:[^\w]+[\w']+){0,3}\b/g);
              var value = data.datasets[0].data[tooltipItem.index];
              labelArr[labelArr.length - 1] += ': ' + value;
               if(labelArr.length > 0){
                return labelArr[0];
               }else{
                return labelArr;
               }
            },
            footer: function(tooltipItems, data) {
              var label = data.labels[tooltipItems[0].index];
              var labelArr =label.match(/\b[\w']+(?:[^\w]+[\w']+){0,3}\b/g);
              var value = data.datasets[0].data[tooltipItems[0].index];
              
              labelArr[labelArr.length - 1] += ': ' + value;
              if(labelArr.length > 0){
                delete labelArr[0];
              }
              return labelArr[1];
              
            }
         }
		 }
    ";
    self::$_html .="},
    plugins: [{
        beforeInit: function(chart, options) {
          var arrColors = chart.data.datasets[0].pointBackgroundColor;
          var arrLabels = chart.data.labels
          var arrValor = chart.data.datasets[0].data
          var html = '<ul class=\'0-legend\'>';
              arrLabels.forEach(function(valor, chave){
                html += '<li>';
                html += '<span style=\'background-color:'+arrColors[chave]+'\'></span>';
                html += arrLabels[chave];              
                html += '</li>';
              })
              html += '</ul>';              
          
          document.getElementById('".$idLegenda."').innerHTML =html
        },
      
    ";
    self::$_html .= "  }]";
    self::$_html .= "});";
    self::$_html .= '</script>';
  }


  private static function jsGerarAreaPolar($idGrafico, $arrDados)
  {
    $idLegenda = 'legenda_'.$idGrafico;
    $arrValor = [];
    $arrLabel = [];
    $arrCor = [];
    foreach ($arrDados as $dados){
      $arrValor[] = $dados['valor'];
      $arrLabel[] = $dados['label'];
      $arrCor[] = $dados['cor'];
    }
    self::$_html .= '<script>';
    self::$_html .= 'var ctx = document.getElementById("' . $idGrafico . '");';
    self::$_html .= 'var data = {';
    self::$_html .= 'labels: ["'.implode('","',$arrLabel).'"],';
    self::$_html .= 'datasets: [{';
    self::$_html .= 'data: ['.implode(',',$arrValor).'],';
    self::$_html .= 'backgroundColor: ["'.implode('","',$arrCor).'"]';
    self::$_html .= '}]';
    self::$_html .= '};';

    self::$_html .= 'var myChart = new Chart(ctx, {';
    self::$_html .= "type: '" . self::$_tipoPolarArea . "',";
    self::$_html .= "data: data,";
    self::$_html .= " options: {
      legend: false,
      startAngle : 0.1,";
    self::$_html .= "tooltips: {
          mode: 'point',
          callbacks: {
            label: function(tooltipItem, data) {
              var label = data.labels[tooltipItem.index];
              var labelArr =label.match(/\b[\w']+(?:[^\w]+[\w']+){0,3}\b/g);
              var value = data.datasets[0].data[tooltipItem.index];
              labelArr[labelArr.length - 1] += ': ' + value;
               if(labelArr.length > 0){
                return labelArr[0];
               }else{
                return labelArr;
               }
            },
            footer: function(tooltipItems, data) {
              var label = data.labels[tooltipItems[0].index];
              var labelArr =label.match(/\b[\w']+(?:[^\w]+[\w']+){0,3}\b/g);
              var value = data.datasets[0].data[tooltipItems[0].index];
              
              labelArr[labelArr.length - 1] += ': ' + value;
              if(labelArr.length > 0){
                delete labelArr[0];
              }
              return labelArr[1];
              
            }
         }
		 }
    ";
    self::$_html.= "}";
    self::$_html .= "});";
    self::$_html .= "document.getElementById('".$idLegenda."').innerHTML = myChart.generateLegend();";
    self::$_html .= '</script>';
  }

  private static function cabecalho($tamanho, $idGrafico, $arrDados)
  {
    $idLegenda = 'legenda_'.$idGrafico;
    self::$_qtdDados = count($arrDados);
    self::scripts();
    self::$_html .= '<div style="width:' . $tamanho . '">';
    self::$_html .= '<canvas id="' . $idGrafico . '" width="30" height="30"></canvas>';
    self::$_html .= '<div class="legendaGrafico" id="'.$idLegenda.'">';
    self::$_html .= '</div>';
    self::$_html .= '</div>';
  }

  public static function gerarBarra($arrDados = [], $idGrafico = 'canvas', $tipoBarra = 'bar', $tamanho = '400px')
  {
    if (count($arrDados) > 0) {
      self::$_html = '';
      self::cabecalho($tamanho, $idGrafico, $arrDados);
      self::jsGerarBar($idGrafico, $arrDados, $tipoBarra);
    }
    return self::$_html;
  }

  public static function gerarPizza($arrDados = [], $idGrafico = 'canvas', $tamanho = '400px')
  {
    if (count($arrDados) > 0) {
      self::$_html = '';
      self::cabecalho($tamanho, $idGrafico, $arrDados);
      self::jsGerarPie($idGrafico, $arrDados);
    }
    return self::$_html;
  }

  public static function gerarRadar($arrDados = [], $idGrafico = 'canvas', $tamanho = '400px')
  {
    if (count($arrDados) > 0) {
      self::$_html = '';
      self::cabecalho($tamanho, $idGrafico, $arrDados);
      self::jsGerarRadar($idGrafico, $arrDados);
    }
    return self::$_html;
  }

  public static function gerarAreaPolar($arrDados = [], $idGrafico = 'canvas',  $tamanho = '400px')
  {
    if (count($arrDados) > 0) {
      self::$_html = '';
      self::cabecalho($tamanho, $idGrafico, $arrDados);
      self::jsGerarAreaPolar($idGrafico, $arrDados);
    }
    return self::$_html;
  }



}