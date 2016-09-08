<?
/**
* ANATEL
*
* 23/06/2016 - criado por marcelo.bezerra@cast.com.br - CAST
*
*/

try {
	
  require_once dirname(__FILE__).'/../../SEI.php';

  session_start();
  
  //////////////////////////////////////////////////////////////////////////////
  InfraDebug::getInstance()->setBolLigado(true);
  InfraDebug::getInstance()->setBolDebugInfra(true);
  InfraDebug::getInstance()->limpar();
  //////////////////////////////////////////////////////////////////////////////
	
  PaginaSEIExterna::getInstance()->setTipoPagina( InfraPagina::$TIPO_PAGINA_SIMPLES );  
  SessaoSEIExterna::getInstance()->validarLink();
  
  //SessaoSEI::getInstance(false);
  //SessaoSEI::getInstance()->simularLogin(null, null, SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno() , SessaoSEIExterna::getInstance()->getNumIdUnidadeAtual() );
  //SessaoSEIExterna::getInstance()->validarPermissao($_GET['acao']);
  
  //=====================================================
  //INICIO - VARIAVEIS PRINCIPAIS E LISTAS DA PAGINA
  //=====================================================

  //=====================================================
  //FIM - VARIAVEIS PRINCIPAIS E LISTAS DA PAGINA
  //=====================================================
  
  switch($_GET['acao']){
    
  	case 'peticionamento_interessado_cadastro':
  		$strTitulo = 'Cadastro de Interessado';
  		break;
  		
    default:
      throw new InfraException("Ação '".$_GET['acao']."' não reconhecida.");
  }

}catch(Exception $e){
  PaginaSEIExterna::getInstance()->processarExcecao($e);
}

$hashAnexo = "";
$idAnexo = "";

PaginaSEIExterna::getInstance()->montarDocType();
PaginaSEIExterna::getInstance()->abrirHtml();
PaginaSEIExterna::getInstance()->abrirHead();
PaginaSEIExterna::getInstance()->montarMeta();
PaginaSEIExterna::getInstance()->montarTitle(':: '.PaginaSEIExterna::getInstance()->getStrNomeSistema().' - '.$strTitulo.' ::');
PaginaSEIExterna::getInstance()->montarStyle();
PaginaSEIExterna::getInstance()->abrirStyle();
$objEditorRN = new EditorRN();
echo $objEditorRN->montarCssEditor(null);
PaginaSEIExterna::getInstance()->fecharStyle();
PaginaSEIExterna::getInstance()->montarJavaScript();
PaginaSEIExterna::getInstance()->abrirJavaScript();
PaginaSEIExterna::getInstance()->fecharJavaScript();
?>
<style type="text/css">
#field1 {height: auto; width: 96%; margin-bottom: 11px;}
#field2 {height: auto; width: 96%; margin-bottom: 11px;}
.sizeFieldset {height:auto; width: 86%;}
.fieldsetClear {border:none !important;}
</style>
<?php 
PaginaSEIExterna::getInstance()->fecharHead();
PaginaSEIExterna::getInstance()->abrirBody($strTitulo,'onload="inicializar();"');
$urlBaseLink = "";
$arrComandos = array();
$arrComandos[] = '<button type="button" accesskey="P" name="Peticionar" value="Peticionar" onclick="enviarInteressado()" class="infraButton"><span class="infraTeclaAtalho">P</span>eticionar</button>';
$arrComandos[] = '<button type="button" accesskey="F" name="btnFechar" value="Fechar" onclick="location.href=\''.PaginaSEIExterna::getInstance()->formatarXHTML(SessaoSEIExterna::getInstance()->assinarLink('controlador.php?acao='.PaginaSEIExterna::getInstance()->getAcaoRetorno().'&acao_origem='.$_GET['acao'].PaginaSEIExterna::getInstance()->montarAncora($_GET['id_indisponibilidade_peticionamento']))).'\';" class="infraButton"><span class="infraTeclaAtalho">V</span>oltar</button>';
?> 
<form id="frmIndisponibilidadeCadastro" method="post" onsubmit="return OnSubmitForm();"  action="<?=PaginaSEIExterna::getInstance()->formatarXHTML(SessaoSEIExterna::getInstance()->assinarLink('controlador.php?acao='.$_GET['acao'].'&acao_origem='.$_GET['acao']))?>">
<?
PaginaSEIExterna::getInstance()->montarBarraComandosSuperior($arrComandos);
PaginaSEIExterna::getInstance()->abrirAreaDados('auto');
?>

 <fieldset id="field1" class="infraFieldset sizeFieldset">
 <legend class="infraLegend">&nbsp; Interessado &nbsp;</legend>

 </fieldset>
  
 <fieldset id="field2" class="infraFieldset sizeFieldset">
 <legend class="infraLegend">&nbsp; Formulário de Cadastro &nbsp;</legend>
  </fieldset>  
    
</form>

<?
PaginaSEIExterna::getInstance()->montarBarraComandosInferior($arrComandos);  
PaginaSEIExterna::getInstance()->fecharAreaDados();
PaginaSEIExterna::getInstance()->fecharBody();
PaginaSEIExterna::getInstance()->fecharHtml();
?>
<script type="text/javascript">

function enviarInteressado(){

	alert('Enviar interessados - INICIO');
	
	var arrDados = ["Banana1", "Orange1", "Apple1", "Mango1"];
	arrDados.push("Kiwi1");
	opener.receberInteressado(arrDados, true);

	var arrDados2 = ["Banana2", "Orange2", "Apple2", "Mango2"];
	arrDados2.push("Kiwi2");
	opener.receberInteressado(arrDados2, false);

	alert('Enviar interessados - FIM');
	
}

function validarFormulario(){

	//valida campo especificação
	var textoEspecificacao = document.getElementById("txtEspecificacao").value;

	if( textoEspecificacao == '' ){
      alert('Informe a especificação.');
      document.getElementById("txtEspecificacao").focus();
      return false;      
	}

	return true;
}

function inicializar(){
  
  infraEfeitoTabelas();
  
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

function OnSubmitForm() {
		
	return true;
}
	 
</script>