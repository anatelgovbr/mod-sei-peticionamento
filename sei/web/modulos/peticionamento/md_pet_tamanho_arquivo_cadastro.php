<?
/**
* ANATEL
*
* 15/02/2016 - criado por jaqueline.mendes@cast.com.br - CAST
*
*/

try {
  require_once dirname(__FILE__).'/../../SEI.php';

  session_start();

  SessaoSEI::getInstance()->validarLink();

  PaginaSEI::getInstance()->verificarSelecao('md_pet_tamanho_arquivo_cadastrar');

  $objMdPetTamanhoArquivoDTO = new MdPetTamanhoArquivoDTO();
  $strDesabilitar = '';

  $arrComandos = array();

  switch($_GET['acao']){
    case 'md_pet_tamanho_arquivo_cadastrar':
    	$strTitulo = 'Peticionamento - Tamanho M�ximo de Arquivos';
    	$arrComandos[] = '<button type="submit" accesskey="S" name="sbmCadastrarTamanhoArquivo" id="sbmCadastrarTamanhoArquivo" value="Salvar" class="infraButton"><span class="infraTeclaAtalho">S</span>alvar</button>';
    	
    	$objMdPetTamanhoArquivoRN = new MdPetTamanhoArquivoRN();
    	$objMdPetTamanhoArquivoDTO->setNumIdTamanhoArquivo(MdPetTamanhoArquivoRN::$ID_FIXO_TAMANHO_ARQUIVO);
    	$objMdPetTamanhoArquivoDTO->retTodos();
    	$objMdPetTamanhoArquivoDTO = $objMdPetTamanhoArquivoRN->consultar($objMdPetTamanhoArquivoDTO);
    	if (isset($_POST['sbmCadastrarTamanhoArquivo'])) {
    		try{
    			
    	$cadastrar = is_null($objMdPetTamanhoArquivoDTO) ? true : false;
    	
    	$objMdPetTamanhoArquivoDTO = new MdPetTamanhoArquivoDTO();
    	$objMdPetTamanhoArquivoDTO->retTodos();
    	
    	$objMdPetTamanhoArquivoDTO->setNumValorDocPrincipal($_POST['txtValorDocPrincipal']);
    	$objMdPetTamanhoArquivoDTO->setNumValorDocComplementar($_POST['txtValorDocComplementar']);
    	$objMdPetTamanhoArquivoDTO->setNumIdTamanhoArquivo(MdPetTamanhoArquivoRN::$ID_FIXO_TAMANHO_ARQUIVO);
    	$objMdPetTamanhoArquivoDTO->setStrSinAtivo('S');
    	
    	if($cadastrar)
    	{
    		$objMdPetTamanhoArquivoDTO = $objMdPetTamanhoArquivoRN->cadastrar($objMdPetTamanhoArquivoDTO);
    	}else
    	{
    		$objMdPetTamanhoArquivoDTO = $objMdPetTamanhoArquivoRN->alterar($objMdPetTamanhoArquivoDTO);
    	}
    			PaginaSEI::getInstance()->adicionarMensagem('Os dados cadastrados foram salvos com sucesso.');
    			header('Location: '.SessaoSEI::getInstance()->assinarLink('controlador.php?acao='.$_GET['acao'].PaginaSEI::getInstance()->montarAncora(MdPetTamanhoArquivoRN::$ID_FIXO_TAMANHO_ARQUIVO)));
    			die;
    		}catch(Exception $e){
    			PaginaSEI::getInstance()->processarExcecao($e);
    		}
    	}

	break;
    			   
    default:
      throw new InfraException("A��o '".$_GET['acao']."' n�o reconhecida.");
  }


}catch(Exception $e){
  PaginaSEI::getInstance()->processarExcecao($e);
}

$arrComandos[] = '<button type="button" accesskey="c" name="btnFechar" id="btnFechar" value="Fechar" onclick="location.href=\''.PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?acao=procedimento_controlar&acao_origem='.$_GET['acao'])).'\';" class="infraButton">Fe<span class="infraTeclaAtalho">c</span>har</button>';

PaginaSEI::getInstance()->montarDocType();
PaginaSEI::getInstance()->abrirHtml();
PaginaSEI::getInstance()->abrirHead();
PaginaSEI::getInstance()->montarMeta();
PaginaSEI::getInstance()->montarTitle(':: '.PaginaSEI::getInstance()->getStrNomeSistema().' - '.$strTitulo.' ::');
PaginaSEI::getInstance()->montarStyle();
PaginaSEI::getInstance()->abrirStyle();
?>
#lblValorDocPrincipal {position:absolute;left:0%;top:7%;margin-left:15px;}
#txtValorDocPrincipal {position:absolute;left:0%;top:13%;width:100px;margin-left:15px}
#lblValorDocComplementar {position:absolute;left:0%;top:21%;margin-left:15px}
#txtValorDocComplementar {position:absolute;left:0%;top:27%;width:100px;margin-left:15px}
#fieldsetTamanhoArquivo {width: 96%; height: 110px; margin-left: 0px;}
<?
PaginaSEI::getInstance()->fecharStyle();
PaginaSEI::getInstance()->montarJavaScript();
PaginaSEI::getInstance()->abrirJavaScript();
PaginaSEI::getInstance()->fecharJavaScript();
PaginaSEI::getInstance()->fecharHead();
PaginaSEI::getInstance()->abrirBody($strTitulo,'onload="inicializar();"');
?>
<form id="frmCadastroTamanhoArquivo" method="post" onsubmit="return OnSubmitForm();" 
action="<?=PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?acao='.$_GET['acao']))?>">
<?
PaginaSEI::getInstance()->montarBarraComandosSuperior($arrComandos);
PaginaSEI::getInstance()->abrirAreaDados('30em');
?>

<fieldset id="fieldsetTamanhoArquivo" class="infraFieldset">
 <legend class="infraLegend">&nbsp;Limite em Mb para carregamento de Arquivos&nbsp;</legend>
	<label id="lblValorDocPrincipal" for="txtValorDocPrincipal" class="infraLabelObrigatorio">Documento Principal (Processo Novo): <img align="top" style="height:16px; width:16px;" src="<?= PaginaSEI::getInstance()->getDiretorioImagensGlobal() ?>/ajuda.gif" name="ajuda" <?= PaginaSEI::montarTitleTooltip('Limita o tamanho m�ximo de Arquivos em Mb no Peticionamento de Processo Novo somente do Documento Principal, que geralmente � de tamanho menor que os demais documentos, pois tende a ser Nato Digital.')?> class="infraImg"/></label>
	<input type="text" id="txtValorDocPrincipal" name="txtValorDocPrincipal" class="infraText" value="<?php echo isset($objMdPetTamanhoArquivoDTO) ? PaginaSEI::tratarHTML($objMdPetTamanhoArquivoDTO->getNumValorDocPrincipal()) : '' ?>" onkeypress="return validarCampo(this, event, 11)" maxlength="11" tabindex="<?=PaginaSEI::getInstance()->getProxTabDados()?>" />
	<label id="lblValorDocComplementar" for="txtValorDocComplementar" class="infraLabelObrigatorio">Demais Documentos: <img align="top" style="height:16px; width:16px;" src="<?= PaginaSEI::getInstance()->getDiretorioImagensGlobal() ?>/ajuda.gif" name="ajuda" <?= PaginaSEI::montarTitleTooltip('Limita o tamanho m�ximo de Arquivos em Mb no Peticionamento de Processo Novo especificamente sobre os Documentos Essenciais e Complementares, no Peticionamento Intercorrente, no Peticionamento de Resposta a Intima��o e no Peticionamento de Respons�vel Legal de Pessoa Jur�dica.')?> class="infraImg"/></label>
	<input type="text" id="txtValorDocComplementar" name="txtValorDocComplementar" class="infraText" value="<?php echo isset($objMdPetTamanhoArquivoDTO) ? PaginaSEI::tratarHTML($objMdPetTamanhoArquivoDTO->getNumValorDocComplementar()) : '' ?>" onkeypress="return validarCampo(this, event, 11);"  onkeydown="somenteNumeros(event)" maxlength="11" tabindex="<?=PaginaSEI::getInstance()->getProxTabDados()?>" />
 </fieldset> 
  
  <input type="hidden" id="hdnIdTamanhoArquivoPeticionamento" name="hdnIdTamanhoArquivoPeticionamento" value="<?=$_GET['id_tamanho_arquivo_peticionamento'];?>" />
  <?
  PaginaSEI::getInstance()->fecharAreaDados();
  ?>
</form>
<?
PaginaSEI::getInstance()->fecharBody();
PaginaSEI::getInstance()->fecharHtml();
?>

<script type="text/javascript">

function validarCampo(obj, event, tamanho){
	 if(!somenteNumeros(event)){
		 return somenteNumeros(event)
	 }else{
		 return infraMascaraTexto(obj, event, tamanho);
	 }
	
}

function inicializar(){

  if ('<?=$_GET['acao']?>'=='md_pet_tamanho_arquivo_cadastrar'){
      addEventoEnter();
    document.getElementById('txtValorDocPrincipal').focus();
  }else{
    document.getElementById('btnFechar').focus();
  }
  infraEfeitoTabelas();
}

function addEventoEnter(){
    var form = document.getElementById('frmCadastroTamanhoArquivo');
    document.addEventListener("keypress", function(evt){
        var key_code = evt.keyCode  ? evt.keyCode  :
            evt.charCode ? evt.charCode :
                evt.which    ? evt.which    : void 0;


        if (key_code == 13)
        {
            $('#sbmCadastrarTamanhoArquivo').click();
        }

    });
}

function validarCadastro() {
	
  if (infraTrim(document.getElementById('txtValorDocPrincipal').value)=='') {
    alert('Informe o Valor para Documento Principal.');
    document.getElementById('txtValorDocPrincipal').focus();
    return false;
  }

  if (infraTrim(document.getElementById('txtValorDocComplementar').value)=='') {
	  alert('Informe o Valor para Documento Complementar.');
	    document.getElementById('txtValorDocComplementar').focus();
	    return false;
	  }
  
  return true;
}

function OnSubmitForm() {
  return validarCadastro();
}

function somenteNumeros(e){
    var tecla=(window.event)?event.keyCode:e.which;   
    if((tecla>47 && tecla<58))
         return true;
    else{
    	if (tecla==8 || tecla==0)
        	 return true;
	else  return false;
    }
}

</script>