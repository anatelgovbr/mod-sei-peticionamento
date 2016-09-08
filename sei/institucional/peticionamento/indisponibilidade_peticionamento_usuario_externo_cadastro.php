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
  
  //if( isset( $_GET['acao_externa'] ) && $_GET['acao_externa'] != "indisponibilidade_peticionamento_usuario_externo_download" ){
    //PaginaSEIExterna::getInstance()->verificarSelecao('indisponibilidade_peticionamento_usuario_externo_alterar');
  //}
  
  //SessaoSEIExterna::getInstance()->validarPermissao($_GET['acao_externa']);

  $objIndisponibilidadePeticionamentoDTO = new IndisponibilidadePeticionamentoDTO();

  if( $_GET['acao_externa'] != "indisponibilidade_peticionamento_usuario_externo_download"){
  
  	$strDesabilitar = '';

    $arrComandos = array();
  
      $objArquivoExtensaoDTO = new ArquivoExtensaoDTO();
      $objArquivoExtensaoDTO->retStrExtensao();
      $objArquivoExtensaoDTO->retStrSinAtivo();
      $objArquivoExtensaoDTO->setStrSinAtivo('S');
      //$arrObjArquivoExtensaoDTO[] = $objArquivoExtensaoDTO;
      $objArquivoExtensaoRN     = new IndisponibilidadeAnexoPeticionamentoRN();
      $arrObjArquivoExtensaoDTO = $objArquivoExtensaoRN->listarAnexoPublico($objArquivoExtensaoDTO);
	  $arrExtPermitidas = array();
	  if(count($arrObjArquivoExtensaoDTO) > 0)
	  {
	   foreach($arrObjArquivoExtensaoDTO as $key => $objArquivoExtensaoDTO)
	   {
	   	    $chave = (string) $key + 1;
	   	    $chave = 'ext_'.$chave;
	    	$arrExtPermitidas[$chave] = $objArquivoExtensaoDTO->getStrExtensao();
	   }
	  }
	  
	  $jsonExtPermitidas =  count($arrExtPermitidas) > 0 ? json_encode($arrExtPermitidas) : null;
  }
  
  switch($_GET['acao_externa']){

      case 'indisponibilidade_peticionamento_usuario_externo_download':
      	      	
      	if( $_POST['hdnIdIndisponibilidadePeticionamento'] != "" ) {
      	
      	  $objIndisponibilidadeAnexoPeticionamentoDTO = new IndisponibilidadeAnexoPeticionamentoDTO();
      	  $objIndisponibilidadeAnexoPeticionamentoDTO->retTodos();
      	  $objIndisponibilidadeAnexoPeticionamentoDTO->setNumIdIndisponibilidade( $_POST['hdnIdIndisponibilidadePeticionamento'] );
      	  $objIndisponibilidadePeticionamentoRN = new IndisponibilidadeAnexoPeticionamentoRN(); //hdnIdIndisponibilidadePeticionamento
      	  $objIndisponibilidadeAnexoPeticionamentoDTO = $objIndisponibilidadePeticionamentoRN->consultar( $objIndisponibilidadeAnexoPeticionamentoDTO );
      	  $strDiretorio = $objIndisponibilidadePeticionamentoRN->obterDiretorio( $objIndisponibilidadeAnexoPeticionamentoDTO );
      	  $file = $strDiretorio.'/'.$objIndisponibilidadeAnexoPeticionamentoDTO->getNumIdAnexoPeticionamento();
      	
      	} else {
      		
      		$file = DIR_SEI_TEMP . '/' . $_POST['hdnNomeArquivoDownload'];
      		
      	}
      	
      	if (file_exists($file)) {
      		
      		header('Pragma: public');
      		header("Cache-Control: private, no-cache, no-store, post-check=0, pre-check=0");
      		header('Expires: 0');      		
      		header('Content-Description: File Transfer');
      		header('Content-Type: application/octet-stream');
      		header('Content-Disposition: attachment; filename="'. $_POST['hdnNomeArquivoDownloadReal'] .'"');
      		header('Content-Length: ' . filesize($file));      		
      		readfile($file, true);
      		exit;
      	}
      	
      	die;
    
    case 'indisponibilidade_peticionamento_usuario_externo_consultar':
    	
      //echo "teste 1"; die();	
   	  $urlBase = ConfiguracaoSEI::getInstance()->getValor('SEI','URL') . "/institucional/peticionamento/";
      $strLinkFinal = $urlBase . 'indisponibilidade_peticionamento_usuario_externo_lista.php?acao_externa=indisponibilidade_peticionamento_usuario_externo_listar';
      $strLinkFinal .= '&id_indisponibilidade_peticionamento='.$_GET['id_indisponibilidade_peticionamento'];
    	    	
      $strTitulo = 'Indisponibilidade do Sistema';
      $arrComandos[] = '<button type="button" accesskey="F" name="btnFechar" value="Fechar" onclick="location.href=\''. $strLinkFinal .'\';" class="infraButton"><span class="infraTeclaAtalho">F</span>echar</button>';
      $objIndisponibilidadePeticionamentoDTO->setNumIdIndisponibilidade($_GET['id_indisponibilidade_peticionamento']);
      $objIndisponibilidadePeticionamentoDTO->setBolExclusaoLogica(false);
      $objIndisponibilidadePeticionamentoDTO->retTodos();
      
      $objIndisponibilidadePeticionamentoRN = new IndisponibilidadePeticionamentoRN();
      $objIndisponibilidadePeticionamentoDTO = $objIndisponibilidadePeticionamentoRN->consultar($objIndisponibilidadePeticionamentoDTO);
      //echo "teste 2"; die();
      
      if ($objIndisponibilidadePeticionamentoDTO == null){
        throw new InfraException("Registro não encontrado.");
      }
      
      break;

    default:
      throw new InfraException("Ação '".$_GET['acao_externa']."' não reconhecida.");
  }

}catch(Exception $e){
  PaginaSEIExterna::getInstance()->processarExcecao($e);
}

$hashAnexo = "";
$idAnexo = "";

//na primeira vez que entrar na tela de geração de nova versão não deve processar os anexos (a tabela deve ser montada com os anexos do clone)
if ( isset($_GET['id_indisponibilidade_peticionamento'])){
		
    $arrAcoesDownload = array();
    $arrAcoesRemover = array();
    
    //echo "teste 4"; die();
    
	if ($_GET ['acao'] != 'indisponibilidade_peticionamento_usuario_externo_consultar')	{
		$_POST ['hdnAnexos'] = IndisponibilidadeAnexoPeticionamentoINT::montarAnexosIndisponibilidadeExterno ( $objIndisponibilidadePeticionamentoDTO->getNumIdIndisponibilidade (), false, $arrAcoesDownload, true, $arrAcoesRemover );
		//echo $_POST ['hdnAnexos']; die();
	} 
	else {
		$_POST ['hdnAnexos'] = IndisponibilidadeAnexoPeticionamentoINT::montarAnexosIndisponibilidadeExterno ( $objIndisponibilidadePeticionamentoDTO->getNumIdIndisponibilidade (), false, $arrAcoesDownload, false, $arrAcoesRemover );
	}
	
	//echo "teste 5"; die();
	
	$arrDados = split("±", $_POST['hdnAnexos'], 2);
	$anexoDTODownloadRN = new IndisponibilidadeAnexoPeticionamentoRN();
	$anexoDTODownloadDTO = new IndisponibilidadeAnexoPeticionamentoDTO();
	$anexoDTODownloadDTO->retTodos();
	$anexoDTODownloadDTO->setNumIdAnexoPeticionamento( $arrDados[0] );
	//$anexoDTODownloadDTO = array();
	$anexoDTODownloadDTO = $anexoDTODownloadRN->listar($anexoDTODownloadDTO);
	
	//echo "teste 6"; die();
	
	if(count( $anexoDTODownloadDTO ) > 0){		
		$idAnexo = $anexoDTODownloadDTO[0]->getNumIdAnexoPeticionamento();
		$hashAnexo = $anexoDTODownloadDTO[0]->getStrHash();
	}
	
}

PaginaSEIExterna::getInstance()->montarDocType();
PaginaSEIExterna::getInstance()->abrirHtml();
PaginaSEIExterna::getInstance()->abrirHead();
PaginaSEIExterna::getInstance()->montarMeta();
PaginaSEIExterna::getInstance()->montarTitle(':: '.PaginaSEIExterna::getInstance()->getStrNomeSistema().' - '.$strTitulo.' ::');
PaginaSEIExterna::getInstance()->montarStyle();
PaginaSEIExterna::getInstance()->abrirStyle();
PaginaSEIExterna::getInstance()->fecharStyle();
PaginaSEIExterna::getInstance()->montarJavaScript();
PaginaSEIExterna::getInstance()->abrirJavaScript();
PaginaSEIExterna::getInstance()->fecharJavaScript();
?>
<style type="text/css">
#fldProrrogacao {height: 10%; width: 86%;}
.sizeFieldset {height:auto; width: 86%;}
.fieldsetClear {border:none !important;}
</style>
<? 
PaginaSEIExterna::getInstance()->fecharHead();
PaginaSEIExterna::getInstance()->abrirBody($strTitulo,'onload="inicializar();"');
$urlBaseLink = "";
//echo "teste 3"; die();
if (count($arrAcoesRemover)>0 || $_POST['hdnAnexos'] != ""  ){

  	    if( $_GET['acao_externa'] == 'indisponibilidade_peticionamento_usuario_externo_consultar' ){
  	       $arrDados = split("±", $_POST['hdnAnexos'], 3);  	
  	       array_push( $arrAcoesRemover , $arrDados[0]);
        }
  
  	    foreach(array_keys($arrAcoesRemover) as $id) { 
          //$urlBaseLink = "";          
          $urlBase = ConfiguracaoSEI::getInstance()->getValor('SEI','URL') . "/institucional/peticionamento/";
          $urlBaseLink = $urlBase . "indisponibilidade_peticionamento_usuario_externo_cadastro.php?acao_externa=indisponibilidade_peticionamento_usuario_externo_download";
          //$urlBaseLink = PaginaSEIExterna::getInstance()->formatarXHTML(SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=indisponibilidade_peticionamento_usuario_externo_download'));
        } 
      }  
  ?> 
<form id="frmIndisponibilidadeCadastro" method="post" onsubmit="return OnSubmitForm();"  action="">
<?
$arrComandos = array();
$arrComandos[] = '<button type="button" accesskey="I" id="btnImprimir" value="Imprimir" onclick="imprimir();" class="infraButton"><span class="infraTeclaAtalho">I</span>mprimir</button>';
$arrComandos[] = '<button type="button" accesskey="F" id="btnFechar" value="Fechar" onclick="fechar();" class="infraButton"><span class="infraTeclaAtalho">F</span>echar</button>';

PaginaSEIExterna::getInstance()->montarBarraComandosSuperior($arrComandos);
PaginaSEIExterna::getInstance()->abrirAreaDados('60em');
?>
 <fieldset id="fldPeriodoIndisponibilidade" class="infraFieldset sizeFieldset">
 
 <!--  Data Inicio  -->
  	<legend class="infraLegend">&nbsp;Período de Indisponibilidade&nbsp;</legend>
  	<label class="infraLabel">
  	<span style="font-weight: bold;">Início:</span> <?= $objIndisponibilidadePeticionamentoDTO->getDthDataInicioFormatada() ?>
  	</label>
    
  <!--  Data Fim  -->
  	<label class="infraLabel"> 
  	<span style="font-weight: bold;"> &nbsp; Fim:</span> <?= $objIndisponibilidadePeticionamentoDTO->getDthDataFimFormatada() ?>
  	</label>
    
 </fieldset>
        
 <!-- Resumo da Indisponibilidade -->
    
 <fieldset class="sizeFieldset infraFieldset" style="margin-top: 11px; margin-bottom: 11px;">
 
    <legend class="infraLegend">&nbsp; Resumo da Indisponibilidade &nbsp;</legend>
    <label class="infraLabel">
    <?php echo isset($objIndisponibilidadePeticionamentoDTO) ? $objIndisponibilidadePeticionamentoDTO->getStrResumoIndisponibilidade() : '' ?>
    </label>
    </fieldset>
        
    <label id="fldProrrogacao" class="infraLabelObrigatorio">Indisponibilidade justifica a prorrogação automática dos prazos:</label>

    <label class="infraLabel">
        <?php echo isset($objIndisponibilidadePeticionamentoDTO) && ($objIndisponibilidadePeticionamentoDTO->getStrSinProrrogacao() && $objIndisponibilidadePeticionamentoDTO->getStrSinProrrogacao() == 'S') ? 'Sim' : ''  ?>
        <?php echo isset($objIndisponibilidadePeticionamentoDTO) && ($objIndisponibilidadePeticionamentoDTO->getStrSinProrrogacao() && $objIndisponibilidadePeticionamentoDTO->getStrSinProrrogacao() == 'N') ? 'Não' : ''  ?>
    </label>

    <div>
        <label id="lblDescricao" class="infraLabelOpcional">
            <ul>
            Observação: Conforme normativo, algumas indisponibilidades justificam a prorrogação automática dos prazos para a realização de atos processuais em meio eletrônico que
            venceriam no dia de sua ocorrência, prorrogando-os para o primeiro dia útil seguinte à resolução do problema.
            </ul>
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
PaginaSEIExterna::getInstance()->fecharAreaDados();
PaginaSEIExterna::getInstance()->fecharBody();
PaginaSEIExterna::getInstance()->fecharHtml();
  
$urlBase = ConfiguracaoSEI::getInstance()->getValor('SEI','URL');
$strLink = $urlBase . '/institucional/peticionamento/indisponibilidade_peticionamento_usuario_externo_lista.php?acao_externa=indisponibilidade_peticionamento_usuario_externo_listar&id_orgao_acesso_externo=0&id_indisponibilidade_peticionamento='.$_GET['id_indisponibilidade_peticionamento'].PaginaSEI::getInstance()->montarAncora($_GET['id_indisponibilidade_peticionamento']);
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

  if ('<?=$_GET['acao_externa']?>'=='indisponibilidade_peticionamento_usuario_externo_consultar'){
	//document.getElementById("filArquivo").disabled = 'disabled';
    infraDesabilitarCamposAreaDados();
  } else{
     document.getElementById('btnFechar').focus();
  }
 
  var strHash = document.getElementById('hdnAnexos').value;
  var arrHash = strHash.split('±');
    
  //var urlBase = "":
  var urlBase = "<?=PaginaSEIExterna::getInstance()->formatarXHTML(SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=indisponibilidade_peticionamento_usuario_externo_download'))?>";
  document.getElementById('hdnNomeArquivoDownload').value = arrHash[0];
  document.getElementById('hdnNomeArquivoDownloadReal').value = arrHash[1];
  //document.getElementById('spanDownload').innerHTML = arrHash[0];
  
  //Monta ações para remover anexos
  <? if (count($arrAcoesRemover)>0 || $_POST['hdnAnexos'] != ""  ){

  	    if( $_GET['acao_externa'] == 'indisponibilidade_peticionamento_usuario_externo_consultar' ){
  	       $arrDados = split("±", $_POST['hdnAnexos'], 2);  	
  	       array_push( $arrAcoesRemover , $arrDados[0]);
        }
  
  	    foreach(array_keys($arrAcoesRemover) as $id) { 
          
        $urlBase = PaginaSEIExterna::getInstance()->formatarXHTML(SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=indisponibilidade_peticionamento_usuario_externo_download'));
        
  ?>        
        var strHash = document.getElementById('hdnAnexos').value;
        var arrHash = strHash.split('±');

  		document.getElementById('hdnNomeArquivoDownload').value = '<?=$hashAnexo?>';
  		document.getElementById('hdnNomeArquivoDownloadReal').value = arrHash[1];
	 	
  <?   
       }
     } // fim loop
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