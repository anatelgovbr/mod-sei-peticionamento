<?
/**
* ANATEL
*
* 21/06/2016 - criado por marcelo.bezerra@cast.com.br - CAST
*
*/

try {

  require_once dirname(__FILE__).'/../../SEI.php';

  session_start();

  //////////////////////////////////////////////////////////////////////////////
  //InfraDebug::getInstance()->setBolLigado(false);
  //InfraDebug::getInstance()->setBolDebugInfra(false);
  //InfraDebug::getInstance()->limpar();
  //////////////////////////////////////////////////////////////////////////////
  //PaginaSEIExterna::getInstance()->setBolXHTML(false);
  //SessaoSEIExterna::getInstance()->validarLink();
  
  //PaginaPeticionamentoExterna::getInstance()->prepararSelecao('indisponibilidade_peticionamento_usuario_externo_selecionar');
  //SessaoSEIExterna::getInstance()->validarPermissao($_GET['acao']);

  PaginaPeticionamentoExterna::getInstance()->setTipoPagina(PaginaPeticionamentoExterna::$TIPO_PAGINA_SEM_MENU);
  PaginaPeticionamentoExterna::getInstance()->getBolAutoRedimensionar();
  
  switch($_GET['acao_externa']){

    case 'indisponibilidade_peticionamento_usuario_externo_selecionar':
      
      $strTitulo = PaginaPeticionamentoExterna::getInstance()->getTituloSelecao('Selecionar Indisponibilidades','Selecionar Indisponibilidades');

      //Se cadastrou alguem
      if ($_GET['acao_origem']=='indisponibilidade_peticionamento_usuario_externo_cadastrar'){
        if (isset($_GET['id_indisponibilidade_peticionamento'])){
          PaginaPeticionamentoExterna::getInstance()->adicionarSelecionado($_GET['id_indisponibilidade_peticionamento']);
        }
      }
      break;

    case 'indisponibilidade_peticionamento_usuario_externo_listar':
        
      $strTitulo = 'Indisponibilidades do Sistema';
      break;

    default:
      throw new InfraException("Ação '".$_GET['acao']."' não reconhecida.");
  }

  $arrComandos = array();
    
  $objIndisponibilidadePeticionamentoDTO = new IndisponibilidadePeticionamentoDTO();
  $objIndisponibilidadePeticionamentoDTO->retTodos();

  if($_POST['txtDtInicio']){
  	$objIndisponibilidadePeticionamentoDTO->setDthDataInicio($_POST['txtDtInicio'].':00',InfraDTO::$OPER_MAIOR_IGUAL);
  }
  
  if($_POST['txtDtFim']){
  	$objIndisponibilidadePeticionamentoDTO->setDthDataFim($_POST['txtDtFim'].':00', InfraDTO::$OPER_MENOR_IGUAL);
  }
  
  if($_POST['selSinProrrogacao'] && $_POST['selSinProrrogacao']!= 'null'){
  	$objIndisponibilidadePeticionamentoDTO->setStrSinProrrogacao($_POST['selSinProrrogacao']);
  }
  
  PaginaPeticionamentoExterna::getInstance()->prepararOrdenacao($objIndisponibilidadePeticionamentoDTO, 'DataInicio', InfraDTO::$TIPO_ORDENACAO_DESC);
  PaginaPeticionamentoExterna::getInstance()->prepararPaginacao($objIndisponibilidadePeticionamentoDTO, 200);
    
  $objIndisponibilidadePeticionamentoRN = new IndisponibilidadePeticionamentoRN();
  $arrObjIndisponibilidadePeticionamentoDTO = $objIndisponibilidadePeticionamentoRN->listar($objIndisponibilidadePeticionamentoDTO);

  PaginaPeticionamentoExterna::getInstance()->processarPaginacao($objIndisponibilidadePeticionamentoDTO);
  $numRegistros = count($arrObjIndisponibilidadePeticionamentoDTO);

  $strLinkPesquisar = PaginaPeticionamentoExterna::getInstance()->formatarXHTML(SessaoSEIExterna::getInstance()->assinarLink('indisponibilidade_peticionamento_usuario_externo_lista.php?acao_externa=indisponibilidade_peticionamento_usuario_externo_listar&id_orgao_acesso_externo=0' ) );
  //$strLinkPesquisar = PaginaPeticionamentoExterna::getInstance()->formatarXHTML(SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=' . $_GET['acao'] .'&acao_origem='.$_GET['acao']));
  $arrComandos[] = '<button type="button" accesskey="P" id="btnPesquisar" value="Pesquisar" onclick="pesquisar();" class="infraButton"><span class="infraTeclaAtalho">P</span>esquisar</button>';
  $arrComandos[] = '<button type="button" accesskey="I" id="btnImprimir" value="Fechar" onclick="infraImprimirTabela();" class="infraButton"><span class="infraTeclaAtalho">I</span>mprimir</button>';
  //$arrComandos[] = '<button type="button" accesskey="F" id="btnFechar" value="Fechar" onclick="fechar();" class="infraButton"><span class="infraTeclaAtalho">F</span>echar</button>';
  
  if ($numRegistros > 0){
  	
    $bolCheck = true;
    $bolAcaoConsultar = SessaoSEIExterna::getInstance()->verificarPermissao('indisponibilidade_peticionamento_usuario_externo_consultar');
    $bolAcaoImprimir = true; 

    $strResultado = '';
    $strSumarioTabela = 'Tabela de Indisponibilidades.';
    $strCaptionTabela = 'Indisponibilidades';
    
    $strResultado .= '<table width="99%" id="tbIndisponibilidade" class="infraTable" summary="'.$strSumarioTabela.'">'."\n";
    $strResultado .= '<caption class="infraCaption">'.PaginaPeticionamentoExterna::getInstance()->gerarCaptionTabela($strCaptionTabela,$numRegistros).'</caption>';
    $strResultado .= '<tr>';
    
    if ($bolCheck) {
      $strResultado .= '<th class="infraTh" width="1%">'.PaginaPeticionamentoExterna::getInstance()->getThCheck().'</th>'."\n";
    }
    
    $strResultado .= '<th class="infraTh" width="30%">'.PaginaPeticionamentoExterna::getInstance()->getThOrdenacao($objIndisponibilidadePeticionamentoDTO,'Início','DataInicio',$arrObjIndisponibilidadePeticionamentoDTO).'</th>'."\n";
    $strResultado .= '<th class="infraTh">'.PaginaPeticionamentoExterna::getInstance()->getThOrdenacao($objIndisponibilidadePeticionamentoDTO,'Fim','DataFim',$arrObjIndisponibilidadePeticionamentoDTO).'</th>'."\n";
    $strResultado .= '<th class="infraTh">'.PaginaPeticionamentoExterna::getInstance()->getThOrdenacao($objIndisponibilidadePeticionamentoDTO,'Prorrogação Automática dos Prazos','SinProrrogacao',$arrObjIndisponibilidadePeticionamentoDTO).'</th>'."\n";
    $strResultado .= '<th class="infraTh" width="15%">Ações</th>'."\n";
    $strResultado .= '</tr>'."\n";
    $strCssTr='';
    
    for($i = 0;$i < $numRegistros; $i++){

        if($_GET['id_indisponibilidade_peticionamento'] == $arrObjIndisponibilidadePeticionamentoDTO[$i]->getNumIdIndisponibilidade()){
            $strCssTr = '<tr class="infraTrAcessada">';
        }else {
            if ($arrObjIndisponibilidadePeticionamentoDTO[$i]->getStrSinAtivo() == 'S') {
                $strCssTr = ($strCssTr == '<tr class="infraTrClara">') ? '<tr class="infraTrEscura">' : '<tr class="infraTrClara">';
            } else {
                $strCssTr = '<tr class="trVermelha">';
            }
        }
       
      $strResultado .= $strCssTr;

      if ($bolCheck){
        $strResultado .= '<td valign="top">'.PaginaPeticionamentoExterna::getInstance()->getTrCheck($i,$arrObjIndisponibilidadePeticionamentoDTO[$i]->getNumIdIndisponibilidade(),$arrObjIndisponibilidadePeticionamentoDTO[$i]->getStrSinProrrogacao()).'</td>';
      }
      
      $dataInicio = isset($arrObjIndisponibilidadePeticionamentoDTO[$i]) && $arrObjIndisponibilidadePeticionamentoDTO[$i]->getDthDataInicio() != '' ? str_replace(' ', ' - ',substr($arrObjIndisponibilidadePeticionamentoDTO[$i]->getDthDataInicio(), 0, -3))  :  '';
      $dataFim    = isset($arrObjIndisponibilidadePeticionamentoDTO[$i]) && $arrObjIndisponibilidadePeticionamentoDTO[$i]->getDthDataFim() != '' ? str_replace(' ', ' - ',substr($arrObjIndisponibilidadePeticionamentoDTO[$i]->getDthDataFim(), 0, -3))  :  '';
      
	  $sinProrrogacao =  $arrObjIndisponibilidadePeticionamentoDTO[$i]->getStrSinProrrogacao() === 'S' ? 'Sim' : 'Não';     
      
      $strResultado .= '<td>'.$dataInicio.'</td>';
      $strResultado .= '<td>'.$dataFim.'</td>';
      $strResultado .= '<td>'.$sinProrrogacao.'</td>';
      $strResultado .= '<td align="center">';
	  
      if ($bolAcaoConsultar){
      	$urlBase = ConfiguracaoSEI::getInstance()->getValor('SEI','URL');
        $strResultado .= '<a href="'. $urlBase . '/institucional/peticionamento/indisponibilidade_peticionamento_usuario_externo_cadastro.php?id_orgao_acesso_externo=0&acao_externa=indisponibilidade_peticionamento_usuario_externo_consultar&id_indisponibilidade_peticionamento='.$arrObjIndisponibilidadePeticionamentoDTO[$i]->getNumIdIndisponibilidade().'" tabindex="'.PaginaPeticionamentoExterna::getInstance()->getProxTabTabela().'"><img src="'.PaginaPeticionamentoExterna::getInstance()->getDiretorioImagensGlobal().'/consultar.gif" title="Consultar Indisponibilidade" alt="Consultar Indisponibilidade" class="infraImg" /></a>&nbsp;';
      }        

      $strResultado .= '</td></tr>'."\n";
      
    }
    
    $strResultado .= '</table>';
    
  }
    
  $strDtInicio = '';
  $strDtFim = '';
  
  if( isset( $_POST['txtDtInicio']) ){
  	$strDtInicio = $_POST['txtDtInicio'];
  }
  
  if( isset( $_POST['txtDtFim']) ){
  	$strDtFim = $_POST['txtDtFim'];
  }
  
  $valorComboProrrogacao = '';
  
  if( isset( $_POST['selSinProrrogacao']) ){
  	$valorComboProrrogacao = $_POST['selSinProrrogacao'];
  }
  
  $strItensSelSinProrrogacaoAutomatica = IndisponibilidadePeticionamentoINT::montarSelectProrrogacaoAutomaticaPrazos('','Todos',$valorComboProrrogacao);

}catch(Exception $e){
  PaginaPeticionamentoExterna::getInstance()->processarExcecao($e);
} 

PaginaPeticionamentoExterna::getInstance()->montarDocType();
PaginaPeticionamentoExterna::getInstance()->abrirHtml();
PaginaPeticionamentoExterna::getInstance()->abrirHead();
PaginaPeticionamentoExterna::getInstance()->montarMeta();
PaginaPeticionamentoExterna::getInstance()->montarTitle(':: '.PaginaPeticionamentoExterna::getInstance()->getStrNomeSistema().' - '.$strTitulo.' ::');
PaginaPeticionamentoExterna::getInstance()->montarStyle();
PaginaPeticionamentoExterna::getInstance()->abrirStyle();
?>
<?
PaginaPeticionamentoExterna::getInstance()->fecharStyle();
PaginaPeticionamentoExterna::getInstance()->montarJavaScript();
PaginaPeticionamentoExterna::getInstance()->abrirJavaScript();
?>
function inicializar(){
  if ('<?=$_GET['acao']?>'=='indisponibilidade_peticionamento_usuario_externo_listar'){
    infraReceberSelecao();
    document.getElementById('btnFecharSelecao').focus();
  }
}

function validDate(valor) {
		
	var campo = (valor === 'I') ?  document.getElementById('txtDtInicio') : campo = document.getElementById('txtDtFim');
    var tamanhoCampo = parseInt((campo.value).length);
    
	if(tamanhoCampo < 16 || tamanhoCampo === 18){
		campo.focus();
		campo.value = "";
		alert('Data/Hora Inválida');
		return false;
	}
	
	var datetime = (campo.value).split(" ");
	var date = datetime[0];
	
	var ardt=new Array;
	var ExpReg=new RegExp("(0[1-9]|[12][0-9]|3[01])/(0[1-9]|1[012])/[12][0-9]{3}");
	ardt=date.split("/");
	erro=false;
	if ( date.search(ExpReg)==-1){
		erro = true;
		}
	else if (((ardt[1]==4)||(ardt[1]==6)||(ardt[1]==9)||(ardt[1]==11))&&(ardt[0]>30)){
		erro = true;
	}else if ( ardt[1]==2) {
		if ((ardt[0]>28)&&((ardt[2]%4)!=0))
			erro = true;
		if ((ardt[0]>29)&&((ardt[2]%4)==0))
			erro = true;
	}
	
	if (erro) {
		alert("Data/Hora Inválida");
		campo.focus();
		campo.value = "";
		return false;
	}else{

		var arrayHoras = datetime[1].split(':')
		var horas      = arrayHoras[0];
		var minutos    = arrayHoras[1];
		var segundos   = arrayHoras[2];
		if(horas > 23 || minutos > 59 || segundos > 59){
		alert('Data/Hora Inválida');
		campo.focus();
		campo.value = "";
		return false
		}
		
	}

	if(document.getElementById('txtDtInicio').value != '' && document.getElementById('txtDtFim').value != ''){
		var dataInicial = returnDateTime(document.getElementById('txtDtInicio').value);
		var dataFinal   = returnDateTime(document.getElementById('txtDtFim').value);
		
		var valido = (dataInicial.getTime() < dataFinal.getTime());

		if(!valido)
	    {
    		document.getElementById('txtDtInicio').value = '';
	    	document.getElementById('txtDtFim').value = '';
			alert('A Data/Hora Inicio deve ser menor que a Data/Hora Fim');
			return false;
		 }
	}
	
	return true;
	
}

function returnDateTime(valor){

	valorArray = valor != '' ? valor.split(" ") : '';

	if(Array.isArray(valorArray)){
	var data = valorArray[0]
	data = data.split('/');
	var mes = parseInt(data[1]) - 1; 
    var horas = valorArray[1].split(':');

    var segundos = typeof horas[2] != 'undefined' ?  horas[2] : 00;
	var dataCompleta = new Date(data[2], mes  ,data[0], horas[0] , horas[1] , segundos);
	return dataCompleta;
	}

	return false;
}

function pesquisar(){
    document.getElementById('frmIndisponibilidadePeticionamentoLista').action='<?=$strLinkPesquisar?>';
    document.getElementById('frmIndisponibilidadePeticionamentoLista').submit();
}

function corrigirTela(){
    var tamanhoGrid = document.getElementById('tbIndisponibilidade').offsetHeight;
    var tamanhoLinha = document.getElementById('divInfraAreaTela').offsetHeight;
    var tamanhoTotal = (tamanhoGrid + tamanhoLinha) - 395;
    document.getElementById('divInfraAreaTela').style.height = tamanhoTotal + 'px';
}

function esconderMenu(){
    infraMenuSistemaEsquema();
    corrigirTela();
}

<?
PaginaPeticionamentoExterna::getInstance()->fecharJavaScript();
PaginaPeticionamentoExterna::getInstance()->fecharHead();
?>

<style type="text/css">
#lblDescricaoInvalido {position:absolute;left:0%;top:0%;width:99%;}

#lblDtInicio {position:absolute;left:0%;top:0px;width:10%;}
#txtDtInicio {position:absolute;left:0%;top:20px;width:10%;}
#imgDtInicio {position:absolute;left:11%;top:20px;}

#lblDtFim {position:absolute;left:15%;top:0px;width:10%;}
#txtDtFim {position:absolute;left:15%;top:20px;width:10%;}
#imgDtFim {position:absolute;left:26%;top:20px;}

#lblSinProrrogacao {position:absolute;left:29%;top:0px;width:30%;}
#selSinProrrogacao {position:absolute;left:29%;top:20px;width:20%;}
#divInfraAreaTabela {position:absolute;left:0%;top:50px; }

#divInfraBarraSistemaD { display:none; }
</style>

<?php 
PaginaPeticionamentoExterna::getInstance()->abrirBody($strTitulo,'onload="inicializar();"');
$urlForm = 'institucional/peticionamento/indisponibilidade_peticionamento_usuario_externo_lista.php?acao_externa=indisponibilidade_peticionamento_usuario_externo_consultar&id_orgao_acesso_externo=0';
?>
<form id="frmIndisponibilidadePeticionamentoLista" method="post" action="<?= $strLinkPesquisar ?>">

<?php  PaginaPeticionamentoExterna::getInstance()->montarBarraComandosSuperior($arrComandos); ?>

  <!--  <div style="height:4.5em; margin-top: 11px; overflow: hidden;" class="infraAreaDados" id="divInfraAreaDados">  -->
  <div style="height:auto; width: 98%;" class="infraAreaDados" id="divInfraAreaDados">
  
  <label id="lblDescricao" class="infraLabelOpcional">
  <br/>Conforme normativo próprio, algumas indisponibilidades justificam a prorrogação automática dos prazos para a realização de atos processuais em meio eletrônico que venceriam no dia de sua ocorrência, prorrogando-os para o primeiro dia útil seguinte à resolução do problema. Na coluna "Prorrogação Automática dos Prazos", as indisponibilidades marcadas com "Sim" justificaram a referida prorrogação.<br/><br/><br/>
  </label>
  
  </div>   
  
  <div style="height:auto; overflow: inherit;" class="infraAreaDados" id="divInfraAreaDados">
  
  <!--  Data Inicio  -->
  	<label id="lblDtInicio" for="txtDtInicio" class="infraLabelOpcional">Início:</label>
    <input type="text" name="txtDtInicio" id="txtDtInicio" onchange="validDate('I');" 
    value="<?= $strDtInicio ?>" 
    onkeypress="return infraMascara(this, event, '##/##/#### ##:##');" class="infraText" />
 	<img src="<?=PaginaPeticionamentoExterna::getInstance()->getDiretorioImagensGlobal()?>/calendario.gif" id="imgDtInicio" 
 	     title="Selecionar Data/Hora Inicial" 
 	     alt="Selecionar Data/Hora Inicial" class="infraImg" 
 	     onclick="infraCalendario('txtDtInicio',this,true,'<?=InfraData::getStrDataAtual().' 00:00'?>');" />
    
  <!--  Data Fim  -->
  	<label id="lblDtFim" for="txtDtFim" class="infraLabelOpcional">Fim:</label>
    <input type="text" name="txtDtFim" onchange="validDate('F');" id="txtDtFim" 
    value="<?= $strDtFim ?>"  
    onchange="validDate('F');" onkeypress="return infraMascara(this, event, '##/##/#### ##:##');" maxlength="16" class="infraText"/>
    <img src="<?=PaginaPeticionamentoExterna::getInstance()->getDiretorioImagensGlobal()?>/calendario.gif" id="imgDtFim" 
         title="Selecionar Data/Hora Final" 
         alt="Selecionar Data/Hora Final" 
         class="infraImg" onclick="infraCalendario('txtDtFim',this,true,'<?=InfraData::getStrDataAtual().' 23:59'?>');" />

<!--  Select Prorrogacao -->

 <label id="lblSinProrrogacao" for="selSinProrrogacao" class="infraLabelOpcional">Prorrogação Automática dos Prazos:</label>
  <select onchange="pesquisar()" id="selSinProrrogacao" name="selSinProrrogacao" class="infraSelect" >
  <?=$strItensSelSinProrrogacaoAutomatica?>
  </select> 
  
    <input type="submit" style="visibility: hidden; display:none;" />
    
  <?
  PaginaPeticionamentoExterna::getInstance()->montarAreaTabela($strResultado,$numRegistros);
//  PaginaPeticionamentoExterna::getInstance()->montarBarraComandosInferior($arrComandos);
  ?>

  </div>
  
</form>
<?
PaginaPeticionamentoExterna::getInstance()->fecharBody();
PaginaPeticionamentoExterna::getInstance()->fecharHtml();
?>