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

  //=========================================
  //INICIO - Fun��es de apoio ao download
  //=========================================
  function obterDiretorio(MdPetIndisponibilidadeAnexoDTO $objAnexoDTO){
  	try{
  		return ConfiguracaoSEI::getInstance()->getValor('SEI','RepositorioArquivos').'/'.substr($objAnexoDTO->getDthInclusao(),6,4).'/'.substr($objAnexoDTO->getDthInclusao(),3,2) .'/' .substr($objAnexoDTO->getDthInclusao(),0,2);
  	}catch(Exception $e){
  		throw new InfraException('Erro obtendo diret�rio do anexo.',$e);
  	}
  }
  
  function obterLocalizacao(MdPetIndisponibilidadeAnexoDTO $objAnexoDTO){
  	try{
  		return obterDiretorio($objAnexoDTO).'/'.$objAnexoDTO->getNumIdAnexoPeticionamento();
  	}catch(Exception $e){
  		throw new InfraException('Erro obtendo localiza��o do anexo.',$e);
  	}
  }
  //=========================================
  //FIM - Fun��es de apoio ao download
  //=========================================
  
  PaginaPeticionamentoExterna::getInstance()->setTipoPagina(PaginaPeticionamentoExterna::$TIPO_PAGINA_SEM_MENU);
  PaginaPeticionamentoExterna::getInstance()->getBolAutoRedimensionar();  
  
  $objMdPetIndisponibilidadeDTO = new MdPetIndisponibilidadeDTO();

	if( $_GET['acao_externa'] != "md_pet_usu_ext_indisponibilidade_download"){
  
  	$strDesabilitar = '';

    $arrComandos = array();
  
      $objArquivoExtensaoDTO = new ArquivoExtensaoDTO();
      $objArquivoExtensaoDTO->retStrExtensao();
      $objArquivoExtensaoDTO->retStrSinAtivo();
      $objArquivoExtensaoDTO->setStrSinAtivo('S');
		
		$objArquivoExtensaoRN     = new MdPetIndisponibilidadeAnexoRN();
		$arrObjArquivoExtensaoDTO = $objArquivoExtensaoRN->listarAnexoPublico($objArquivoExtensaoDTO);
		$arrExtPermitidas = array();
		if(count($arrObjArquivoExtensaoDTO) > 0){
			foreach($arrObjArquivoExtensaoDTO as $key => $objArquivoExtensaoDTO){
				$chave = (string) $key + 1;
				$chave = 'ext_'.$chave;
				$arrExtPermitidas[$chave] = $objArquivoExtensaoDTO->getStrExtensao();
			}
		}
		$jsonExtPermitidas =  count($arrExtPermitidas) > 0 ? json_encode($arrExtPermitidas) : null;
	}
  
switch($_GET['acao_externa']){

	case 'md_pet_usu_ext_indisponibilidade_download':
      	      	
      	if( $_POST['hdnIdIndisponibilidadePeticionamento'] != "" ) {
      	
			$objMdPetIndisponibilidadeAnexoDTO = new MdPetIndisponibilidadeAnexoDTO();
			$objMdPetIndisponibilidadeAnexoDTO->retTodos();
			$objMdPetIndisponibilidadeAnexoDTO->setNumIdIndisponibilidade( $_POST['hdnIdIndisponibilidadePeticionamento'] );
			
			$objMdPetIndisponibilidadeAnexoRN = new MdPetIndisponibilidadeAnexoRN();
			$objMdPetIndisponibilidadeAnexoDTO = $objMdPetIndisponibilidadeAnexoRN->consultar( $objMdPetIndisponibilidadeAnexoDTO );
			$strDiretorio = $objMdPetIndisponibilidadeAnexoRN->obterDiretorio( $objMdPetIndisponibilidadeAnexoDTO );
			$file = $strDiretorio.'/'.$objMdPetIndisponibilidadeAnexoDTO->getNumIdAnexoPeticionamento();
      	
      	} else {
      		
      		$file = DIR_SEI_TEMP . '/' . $_POST['hdnNomeArquivoDownload'];
      		
      	}
      	
      	if (file_exists($file)) {
      		
      		//Implementa�ao baseada na pagina "anexo_download.php"
      		header("Pragma: public");
      		header("Cache-Control: private, no-cache, no-store, post-check=0, pre-check=0");
      		header("Expires: 0");      		      		
      		$strContentDisposition = 'inline';
      		
      		if ((isset($_GET['download']) && $_GET['download']=='1')) {
      			$strContentDisposition = 'attachment';
      		}
      		
      		PaginaSEI::montarHeaderDownload($_POST['hdnNomeArquivoDownloadReal'],$strContentDisposition);
      		
      		ob_implicit_flush();
      		ob_flush();
      		
      		$fp = fopen(obterLocalizacao($objMdPetIndisponibilidadeAnexoDTO), "rb");
      		while (!feof($fp)) {
      			echo fread($fp, TAM_BLOCO_LEITURA_ARQUIVO);
      		}
      		fclose($fp);
      		
      		ob_flush();
      		break;
      	}
    
	case 'md_pet_usu_ext_indisponibilidade_consultar':

		$urlBase = ConfiguracaoSEI::getInstance()->getValor('SEI','URL') . "/modulos/peticionamento/";
		$strLinkFinal = $urlBase . 'md_pet_usu_ext_indisponibilidade_lista.php?acao_externa=md_pet_usu_ext_indisponibilidade_listar';
		$strLinkFinal .= '&id_indisponibilidade_peticionamento='.$_GET['id_indisponibilidade_peticionamento'];

		$strTitulo = 'Indisponibilidade do Sistema';
		$objMdPetIndisponibilidadeDTO->setNumIdIndisponibilidade($_GET['id_indisponibilidade_peticionamento']);
		$objMdPetIndisponibilidadeDTO->setBolExclusaoLogica(false);
		$objMdPetIndisponibilidadeDTO->retTodos();

		$objMdPetIndisponibilidadeRN = new MdPetIndisponibilidadeRN();
		$objMdPetIndisponibilidadeDTO = $objMdPetIndisponibilidadeRN->consultar($objMdPetIndisponibilidadeDTO);
      
		if ($objMdPetIndisponibilidadeDTO == null){
			throw new InfraException("Registro n�o encontrado.");
		}

		break;

		default:
		throw new InfraException("A��o '".$_GET['acao_externa']."' n�o reconhecida.");
}

}catch(Exception $e){
  PaginaPeticionamentoExterna::getInstance()->processarExcecao($e);
}

$hashAnexo = "";
$idAnexo = "";

//Na primeira vez que entrar na tela de gera��o de nova vers�o n�o deve processar os anexos (a tabela deve ser montada com os anexos do clone)
if ( isset($_GET['id_indisponibilidade_peticionamento'])){
		
    $arrAcoesDownload = array();
    $arrAcoesRemover = array();
    
	if ($_GET ['acao'] != 'md_pet_usu_ext_indisponibilidade_consultar')	{
		$_POST ['hdnAnexos'] = MdPetIndisponibilidadeAnexoINT::montarAnexosIndisponibilidadeExterno ( $objMdPetIndisponibilidadeDTO->getNumIdIndisponibilidade (), false, $arrAcoesDownload, true, $arrAcoesRemover );
	}
	else {
		$_POST ['hdnAnexos'] = MdPetIndisponibilidadeAnexoINT::montarAnexosIndisponibilidadeExterno ( $objMdPetIndisponibilidadeDTO->getNumIdIndisponibilidade (), false, $arrAcoesDownload, false, $arrAcoesRemover );
	}
	
	$arrDados = split("�", $_POST['hdnAnexos'], 2);
	
	$anexoDTODownloadRN = new MdPetIndisponibilidadeAnexoRN();
	
	$anexoDTODownloadDTO = new MdPetIndisponibilidadeAnexoDTO();
	$anexoDTODownloadDTO->retTodos();
	$anexoDTODownloadDTO->setNumIdAnexoPeticionamento( $arrDados[0] );
	$anexoDTODownloadDTO = $anexoDTODownloadRN->listar($anexoDTODownloadDTO);
	
	if(count( $anexoDTODownloadDTO ) > 0){		
		$idAnexo = $anexoDTODownloadDTO[0]->getNumIdAnexoPeticionamento();
		$hashAnexo = $anexoDTODownloadDTO[0]->getStrHash();
	}
	
}

PaginaPeticionamentoExterna::getInstance()->montarDocType();
PaginaPeticionamentoExterna::getInstance()->abrirHtml();
PaginaPeticionamentoExterna::getInstance()->abrirHead();
PaginaPeticionamentoExterna::getInstance()->montarMeta();
PaginaPeticionamentoExterna::getInstance()->montarTitle(':: '.PaginaPeticionamentoExterna::getInstance()->getStrNomeSistema().' - '.$strTitulo.' ::');
PaginaPeticionamentoExterna::getInstance()->montarStyle();
PaginaPeticionamentoExterna::getInstance()->abrirStyle();
PaginaPeticionamentoExterna::getInstance()->fecharStyle();
PaginaPeticionamentoExterna::getInstance()->montarJavaScript();
PaginaPeticionamentoExterna::getInstance()->abrirJavaScript();
PaginaPeticionamentoExterna::getInstance()->fecharJavaScript();
?>
<style type="text/css">
#fldProrrogacao {height: 10%; width: 86%;}
.sizeFieldset {height:auto; width: 86%;}
.fieldsetClear {border:none !important;}
#divInfraBarraSistemaD { display:none; }
</style>
<? 
PaginaPeticionamentoExterna::getInstance()->fecharHead();
PaginaPeticionamentoExterna::getInstance()->abrirBody($strTitulo,'onload="inicializar();"');
$urlBaseLink = "";

if (count($arrAcoesRemover)>0 || $_POST['hdnAnexos'] != ""  ){

  	    if( $_GET['acao_externa'] == 'md_pet_usu_ext_indisponibilidade_consultar' ){
  	       $arrDados = split("�", $_POST['hdnAnexos'], 3);  	
  	       array_push( $arrAcoesRemover , $arrDados[0]);
        }
  
		foreach(array_keys($arrAcoesRemover) as $id){
			$urlBase = ConfiguracaoSEI::getInstance()->getValor('SEI','URL') . "/modulos/peticionamento/";
			$urlBaseLink = $urlBase . "md_pet_usu_ext_indisponibilidade_cadastro.php?download=1&acao_externa=md_pet_usu_ext_indisponibilidade_download";
        }
      }
?>
<form id="frmIndisponibilidadeCadastro" method="post" onsubmit="return OnSubmitForm();"  action="">
<?
$arrComandos = array();
$arrComandos[] = '<button type="button" accesskey="i" id="btnImprimir" value="Imprimir" onclick="imprimir();" class="infraButton"><span class="infraTeclaAtalho">I</span>mprimir</button>';
$arrComandos[] = '<button type="button" accesskey="c" id="btnFechar" value="Fechar" onclick="fechar();" class="infraButton">Fe<span class="infraTeclaAtalho">c</span>har</button>';

PaginaPeticionamentoExterna::getInstance()->montarBarraComandosSuperior($arrComandos);
PaginaPeticionamentoExterna::getInstance()->abrirAreaDados('60em');
?>
 <fieldset id="fldPeriodoIndisponibilidade" class="infraFieldset sizeFieldset">
 
 <!--  Data Inicio  -->
  	<legend class="infraLegend">&nbsp;Per�odo de Indisponibilidade&nbsp;</legend>
  	<label class="infraLabel">
  	<span style="font-weight: bold;">In�cio:</span> <?= $objMdPetIndisponibilidadeDTO->getDthDataInicioFormatada() ?>
  	</label>
    
  <!--  Data Fim  -->
  	<label class="infraLabel"> 
  	<span style="font-weight: bold;"> &nbsp; Fim:</span> <?= $objMdPetIndisponibilidadeDTO->getDthDataFimFormatada() ?>
  	</label>
    
 </fieldset>
        
 <!-- Resumo da Indisponibilidade -->
    
 <fieldset class="sizeFieldset infraFieldset" style="margin-top: 11px; margin-bottom: 11px;">
 
    <legend class="infraLegend">&nbsp; Resumo da Indisponibilidade &nbsp;</legend>
    <label class="infraLabel">
    <?php echo isset($objMdPetIndisponibilidadeDTO) ? $objMdPetIndisponibilidadeDTO->getStrResumoIndisponibilidade() : '' ?>
    </label>
    </fieldset>
        
    <label id="fldProrrogacao" class="infraLabelObrigatorio">Indisponibilidade justificou prorroga��o autom�tica dos prazos:</label>

    <label class="infraLabel">
        <?php echo isset($objMdPetIndisponibilidadeDTO) && ($objMdPetIndisponibilidadeDTO->getStrSinProrrogacao() && $objMdPetIndisponibilidadeDTO->getStrSinProrrogacao() == 'S') ? 'Sim' : ''  ?>
        <?php echo isset($objMdPetIndisponibilidadeDTO) && ($objMdPetIndisponibilidadeDTO->getStrSinProrrogacao() && $objMdPetIndisponibilidadeDTO->getStrSinProrrogacao() == 'N') ? 'N�o' : ''  ?>
    </label>

    <div>
        <label id="lblDescricao" class="infraLabelOpcional">
            <ul>Observa��o: Conforme normativo pr�prio, algumas indisponibilidades justificam a prorroga��o autom�tica dos prazos para a realiza��o de atos processuais em meio eletr�nico que venceriam no dia de sua ocorr�ncia, prorrogando-os para o primeiro dia �til seguinte � resolu��o do problema.</ul>
        </label>
    </div>
    
    <div id="lblProrrogacao">
    
      <? 
      if( $urlBaseLink != "" ) {
        $textoLink = "Download";
        
        if( $arrDados[1] != "" ){
        	$textoLink = $arrDados[1];
        }
       ?>
        <br/>
        <label class="infraLabelObrigatorio">
        Arquivo: <a href="javascript:downloadArquivo( '<?= $urlBaseLink ?>' );" class="infraLabel"> <?= $textoLink ?> </a>
        </label>
      <? } ?>
    
    </div>
     
   <input type="hidden" id="hdnArquivosPermitidos" name="hdnArquivosPermitidos" value='<?php echo isset($jsonExtPermitidas) && (!is_null($jsonExtPermitidas)) ? $jsonExtPermitidas : ''?>'/>
   <input type="hidden" id="hdnAnexos" name="hdnAnexos" value="<?=$_POST['hdnAnexos']?>"/>
   <input type="hidden" id="hdnAnexosInicial" name="hdnAnexosInicial" value="<?=$_POST['hdnAnexos']?>"/>
   
</form>
  
  <input type="hidden" id="hdnIdIndisponibilidadePeticionamento" name="hdnIdIndisponibilidadePeticionamento" 
         value="<?php echo isset($_GET['id_indisponibilidade_peticionamento']) ? $_GET['id_indisponibilidade_peticionamento'] : '' ?>" />
  
  <input type="hidden" id="hdnSinProrrogacao" name="hdnSinProrrogacao" value="" />
  <input type="hidden" id="hdnNomeArquivoDownload" name="hdnNomeArquivoDownload" value="" />
  <input type="hidden" id="hdnNomeArquivoDownloadReal" name="hdnNomeArquivoDownloadReal" value="" />
<?
PaginaPeticionamentoExterna::getInstance()->fecharAreaDados();
PaginaPeticionamentoExterna::getInstance()->fecharBody();
PaginaPeticionamentoExterna::getInstance()->fecharHtml();
  
$urlBase = ConfiguracaoSEI::getInstance()->getValor('SEI','URL');
$strLink = $urlBase . '/modulos/peticionamento/md_pet_usu_ext_indisponibilidade_lista.php?acao_externa=md_pet_usu_ext_indisponibilidade_listar&id_orgao_acesso_externo=0&id_indisponibilidade_peticionamento='.$_GET['id_indisponibilidade_peticionamento'].PaginaSEI::getInstance()->montarAncora($_GET['id_indisponibilidade_peticionamento']);
?>
<script type="text/javascript">

function fechar(){
	document.location = '<?= $strLink ?>';
}

function imprimir() {
    document.getElementById('btnFechar').style.display = 'none';
    document.getElementById('btnImprimir').style.display = 'none';
    infraImprimirDiv('divInfraAreaTelaD');

    self.setTimeout(function () {
        document.getElementById('btnFechar').style.display = '';
        document.getElementById('btnImprimir').style.display = '';
    }, 1000);
}

function inicializar(){

  if ('<?=$_GET['acao_externa']?>'=='md_pet_usu_ext_indisponibilidade_consultar'){
    infraDesabilitarCamposAreaDados();
  } else{
     document.getElementById('btnFechar').focus();
  }
	  
  var strHash = document.getElementById('hdnAnexos').value;
  var arrHash = strHash.split('�');

  var urlBase = "<?=PaginaPeticionamentoExterna::getInstance()->formatarXHTML(SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=md_pet_usu_ext_indisponibilidade_download'))?>";
  document.getElementById('hdnNomeArquivoDownload').value = arrHash[0];
  document.getElementById('hdnNomeArquivoDownloadReal').value = arrHash[1];
  
  //Monta a��es para remover anexos
  <? if (count($arrAcoesRemover)>0 || $_POST['hdnAnexos'] != ""  ){

  	    if( $_GET['acao_externa'] == 'md_pet_usu_ext_indisponibilidade_consultar' ){
  	       $arrDados = split("�", $_POST['hdnAnexos'], 2);  	
  	       array_push( $arrAcoesRemover , $arrDados[0]);
        }
  
  	    foreach(array_keys($arrAcoesRemover) as $id) { 
          
        $urlBase = PaginaPeticionamentoExterna::getInstance()->formatarXHTML(SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=md_pet_usu_ext_indisponibilidade_download'));
        
  ?>        
        var strHash = document.getElementById('hdnAnexos').value;
        var arrHash = strHash.split('�');

  		document.getElementById('hdnNomeArquivoDownload').value = '<?=$hashAnexo?>';
  		document.getElementById('hdnNomeArquivoDownloadReal').value = arrHash[1];
	 	
  <?   
       }
     } //Fim loop
  ?>  
  
  infraEfeitoTabelas();
  
}

function downloadArquivo( urlBaseDownload ){

    var actionAnterior = document.getElementById("frmIndisponibilidadeCadastro").action;
    var targetAnterior = document.getElementById("frmIndisponibilidadeCadastro").target;
    
	document.getElementById("frmIndisponibilidadeCadastro").action = urlBaseDownload;
	document.getElementById("frmIndisponibilidadeCadastro").target="_blank";
	document.getElementById("frmIndisponibilidadeCadastro").submit();

	document.getElementById("frmIndisponibilidadeCadastro").action = actionAnterior;
	document.getElementById("frmIndisponibilidadeCadastro").target=targetAnterior;
		
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

function preencherHdnProrrogacao(){
	var rdProrrogacao = document.getElementsByName('rdProrrogacao[]')[0].checked ? 'S' : '';

	if(rdProrrogacao ==  ''){
	rdProrrogacao = document.getElementsByName('rdProrrogacao[]') [1].checked ? 'N' : '';
	}

	document.getElementById('hdnSinProrrogacao').value = rdProrrogacao;
}

function OnSubmitForm() {
	return true;
}	 
</script>