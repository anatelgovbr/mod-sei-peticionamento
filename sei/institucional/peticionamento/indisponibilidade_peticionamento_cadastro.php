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
  
  //////////////////////////////////////////////////////////////////////////////
  InfraDebug::getInstance()->setBolLigado(false);
  InfraDebug::getInstance()->setBolDebugInfra(false);
  InfraDebug::getInstance()->limpar();
  //////////////////////////////////////////////////////////////////////////////

  SessaoSEI::getInstance()->validarLink();
  
  if( $_GET['acao'] != "indisponibilidade_peticionamento_download"){
    PaginaSEI::getInstance()->verificarSelecao('indisponibilidade_peticionamento_alterar');
  }
  
  SessaoSEI::getInstance()->validarPermissao($_GET['acao']);

  $objIndisponibilidadePeticionamentoDTO = new IndisponibilidadePeticionamentoDTO();

  if( $_GET['acao'] != "indisponibilidade_peticionamento_download"){
  
  	$strDesabilitar = '';

    $arrComandos = array();
  
      $objArquivoExtensaoDTO = new ArquivoExtensaoDTO();
      $objArquivoExtensaoDTO->retStrExtensao();
      $objArquivoExtensaoDTO->retStrSinAtivo();
      $objArquivoExtensaoDTO->setStrSinAtivo('S');
      //$arrObjArquivoExtensaoDTO[] = $objArquivoExtensaoDTO;
      $objArquivoExtensaoRN     = new ArquivoExtensaoRN();
      $arrObjArquivoExtensaoDTO = $objArquivoExtensaoRN->listar($objArquivoExtensaoDTO);
	  $arrExtPermitidas       = array();
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
  
  switch($_GET['acao']){
  	
  	case 'indisponibilidade_peticionamento_upload_anexo':
  		if (isset($_FILES['filArquivo'])){
  			PaginaSEI::getInstance()->processarUpload('filArquivo', DIR_SEI_TEMP, false);
  		}
  		die;
  	
    case 'indisponibilidade_peticionamento_cadastrar':

      $strTitulo = 'Nova Indisponibilidade do SEI';

      $arrComandos[] = '<button type="submit" accesskey="s" name="sbmCadastrarIndisponibilidadePeticionamento" id="sbmCadastrarIndisponibilidadePeticionamento" value="Salvar" class="infraButton"><span class="infraTeclaAtalho">S</span>alvar</button>';
      $arrComandos[] = '<button type="button" accesskey="c" name="btnCancelar" id="btnCancelar" value="Cancelar" onclick="location.href=\''.PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?acao='.PaginaSEI::getInstance()->getAcaoRetorno().'&acao_origem='.$_GET['acao'])).'\';" class="infraButton"><span class="infraTeclaAtalho">C</span>ancelar</button>';

      $strLinkAnexos = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=indisponibilidade_peticionamento_upload_anexo');
          
      $dateIni = isset($_POST['txtDtInicio']) && $_POST['txtDtInicio']!= ''  ? $_POST['txtDtInicio'].':00' : $_POST['txtDtInicio'];
	  $dateFim = isset($_POST['txtDtFim'])  && $_POST['txtDtFim']!= ''?  $_POST['txtDtFim'].':00' : $_POST['txtDtFim'];
      
      $objIndisponibilidadePeticionamentoDTO = new IndisponibilidadePeticionamentoDTO();
      $objIndisponibilidadePeticionamentoDTO->setNumIdIndisponibilidade(null);
      $objIndisponibilidadePeticionamentoDTO->setDthDataInicio($dateIni);
      $objIndisponibilidadePeticionamentoDTO->setDthDataFim($dateFim);
      $objIndisponibilidadePeticionamentoDTO->setStrResumoIndisponibilidade($_POST['txtResumoIndisponibilidade']);
      $objIndisponibilidadePeticionamentoDTO->setStrSinProrrogacao($_POST['hdnSinProrrogacao']);
      //echo $_POST['hdnAnexos']; die;
      $objIndisponibilidadePeticionamentoDTO->setArrObjAnexoDTO(IndisponibilidadeAnexoPeticionamentoINT::processarAnexo($_POST['hdnAnexos']));
            
      //processarAnexo
      if (isset($_POST['sbmCadastrarIndisponibilidadePeticionamento'])) {
      	      	
        try{
          
          $objIndisponibilidadePeticionamentoRN = new IndisponibilidadePeticionamentoRN();
          $objIndisponibilidadePeticionamentoDTO = $objIndisponibilidadePeticionamentoRN->cadastrar($objIndisponibilidadePeticionamentoDTO);
          PaginaSEI::getInstance()->adicionarMensagem('Os dados cadastrados foram salvos com sucesso.');
          header('Location: '.SessaoSEI::getInstance()->assinarLink('controlador.php?acao='.PaginaSEI::getInstance()->getAcaoRetorno().'&acao_origem='.$_GET['acao'].'&id_indisponibilidade_peticionamento='.$objIndisponibilidadePeticionamentoDTO->getNumIdIndisponibilidade().PaginaSEI::getInstance()->montarAncora($objIndisponibilidadePeticionamentoDTO->getNumIdIndisponibilidade())));
          die;
        }catch(Exception $e){
          PaginaSEI::getInstance()->processarExcecao($e);
        }
      }
      break;

    case 'indisponibilidade_peticionamento_alterar':
      $strTitulo = 'Alterar Indisponibilidade do SEI';
      $arrComandos[] = '<button type="submit" accesskey="s" name="sbmAlterarIndisponibilidadePeticionamento" value="Salvar" class="infraButton"><span class="infraTeclaAtalho">S</span>alvar</button>';
	  $arrComandos[] = '<button type="button" accesskey="c" name="btnCancelar" id="btnCancelar" value="Cancelar" onclick="location.href=\''.PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?acao='.PaginaSEI::getInstance()->getAcaoRetorno().'&acao_origem='.$_GET['acao'].PaginaSEI::getInstance()->montarAncora($_GET['id_indisponibilidade_peticionamento']))).'\';" class="infraButton"><span class="infraTeclaAtalho">C</span>ancelar</button>';
      
      $strDesabilitar = 'disabled="disabled"';
      
      $strLinkAnexos = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=indisponibilidade_peticionamento_upload_anexo');
      
      if (isset($_GET['id_indisponibilidade_peticionamento'])){
      	
        $objIndisponibilidadePeticionamentoDTO->setNumIdIndisponibilidade($_GET['id_indisponibilidade_peticionamento']);
        $objIndisponibilidadePeticionamentoDTO->retTodos();
    	$objIndisponibilidadePeticionamentoRN = new IndisponibilidadePeticionamentoRN();
        $objIndisponibilidadePeticionamentoDTO = $objIndisponibilidadePeticionamentoRN->consultar($objIndisponibilidadePeticionamentoDTO);
                
        $dataInicioShow = substr($objIndisponibilidadePeticionamentoDTO->getDthDataInicio(), 0, -3); 
        $dataFimShow = substr($objIndisponibilidadePeticionamentoDTO->getDthDataFim(), 0, -3);
        
        $objIndisponibilidadePeticionamentoDTO->setDthDataInicio($dataInicioShow);
        $objIndisponibilidadePeticionamentoDTO->setDthDataFim($dataFimShow);
        
        if ($objIndisponibilidadePeticionamentoDTO==null){
          throw new InfraException("Registro não encontrado.");
        }
        
      }
      
      if (isset($_POST['sbmAlterarIndisponibilidadePeticionamento'])) {
        
      	try{
        	
        	$objIndisponibilidadePeticionamentoDTO = new IndisponibilidadePeticionamentoDTO();
        	$objIndisponibilidadePeticionamentoDTO->setNumIdIndisponibilidade($_POST['hdnIdIndisponibilidadePeticionamento']);
        	
        	$dateIni = isset($_POST['txtDtInicio']) && $_POST['txtDtInicio']!= ''  ? $_POST['txtDtInicio'].':00' : $_POST['txtDtInicio'];
        	$dateFim = isset($_POST['txtDtFim'])  && $_POST['txtDtFim']!= ''?  $_POST['txtDtFim'].':00' : $_POST['txtDtFim'];
        	
        	//CASO TENHA HAVIDO ALTERACAO DO ARQUIVO ANEXO 
        	// remove todos os anexos e depois reinsere (se vier algum para reinserir)
        	if( $_POST['hdnAnexos'] != $_POST['hdnAnexosInicial']) {
        	  $anexoDTO = new IndisponibilidadeAnexoPeticionamentoDTO();
        	  $anexoDTO->setNumIdIndisponibilidade( $objIndisponibilidadePeticionamentoDTO->getNumIdIndisponibilidade() );
        	  $anexoDTO->retTodos();
        	  $rnAnexo = new IndisponibilidadeAnexoPeticionamentoRN();
        	  $arrAnexos = $rnAnexo->listar( $anexoDTO );
        	  $rnAnexo->excluir($arrAnexos);
        	}
        	
        	$objIndisponibilidadePeticionamentoDTO->setDthDataInicio($dateIni);
        	$objIndisponibilidadePeticionamentoDTO->setDthDataFim($dateFim);
        	$objIndisponibilidadePeticionamentoDTO->setStrResumoIndisponibilidade($_POST['txtResumoIndisponibilidade']);
        	$objIndisponibilidadePeticionamentoDTO->setStrSinProrrogacao($_POST['hdnSinProrrogacao']);
        	$objIndisponibilidadePeticionamentoDTO->setArrObjAnexoDTO(IndisponibilidadeAnexoPeticionamentoINT::processarAnexo($_POST['hdnAnexos']));
        	
            $objIndisponibilidadePeticionamentoRN = new IndisponibilidadePeticionamentoRN();
            $objIndisponibilidadePeticionamentoRN->alterar($objIndisponibilidadePeticionamentoDTO);
            PaginaSEI::getInstance()->adicionarMensagem('Os dados foram alterados com sucesso.');
            header('Location: '.SessaoSEI::getInstance()->assinarLink('controlador.php?acao='.PaginaSEI::getInstance()->getAcaoRetorno().'&acao_origem='.$_GET['acao'].'&id_indisponibilidade_peticionamento='.$objIndisponibilidadePeticionamentoDTO->getNumIdIndisponibilidade().PaginaSEI::getInstance()->montarAncora($objIndisponibilidadePeticionamentoDTO->getNumIdIndisponibilidade())));
            //header('Location: '.SessaoSEI::getInstance()->assinarLink('controlador.php?id_tipo_processo_litigioso='. $_POST['hdnIdTipoProcessoLitigioso'] . '&acao='.PaginaSEI::getInstance()->getAcaoRetorno().'&acao_origem='.$_GET['acao'].PaginaSEI::getInstance()->montarAncora($objFaseLitigiosoDTO->getNumIdFaseLitigioso())));
          die;
          
        } catch(Exception $e){
        	PaginaSEI::getInstance()->setBolAutoRedimensionar(false);
        	PaginaSEI::getInstance()->setTipoPagina( InfraPagina::$TIPO_PAGINA_COMPLETA );
            PaginaSEI::getInstance()->processarExcecao($e);
        }
        
      }
      
      break;

      case 'indisponibilidade_peticionamento_download':
      	      	
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
			  header("Cache-Control: no-cache, no-store, post-check=0, pre-check=0,  must-revalidate");
			  header('Pragma: no-cache');
			  header('Expires: 0');
			  header('Content-Description: File Transfer');
			  header('Content-Disposition: attachment; filename="' . $_POST['hdnNomeArquivoDownloadReal'] . '"');
			  header('Content-Length: ' . filesize($file));
			  readfile($file, true);
			  exit;
		  }
      	
      	die;
    
    case 'indisponibilidade_peticionamento_consultar':
      $strTitulo = 'Consultar Indisponibilidade do SEI';
      $arrComandos[] = '<button type="button" accesskey="c" name="btnCancelar" id="btnCancelar" value="Cancelar" onclick="location.href=\''.PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?acao='.PaginaSEI::getInstance()->getAcaoRetorno().'&acao_origem='.$_GET['acao'].PaginaSEI::getInstance()->montarAncora($_GET['id_indisponibilidade_peticionamento']))).'\';" class="infraButton">Fe<span class="infraTeclaAtalho">c</span>har</button>';
	  
      $objIndisponibilidadePeticionamentoDTO->setNumIdIndisponibilidade($_GET['id_indisponibilidade_peticionamento']);
      $objIndisponibilidadePeticionamentoDTO->setBolExclusaoLogica(false);
      $objIndisponibilidadePeticionamentoDTO->retTodos();
      
      $objIndisponibilidadePeticionamentoRN = new IndisponibilidadePeticionamentoRN();
      $objIndisponibilidadePeticionamentoDTO = $objIndisponibilidadePeticionamentoRN->consultar($objIndisponibilidadePeticionamentoDTO);
      
      //$dateStr = date_create( $objIndisponibilidadePeticionamentoDTO->getDthDataInicio() );
      //echo date_format( $dateStr ,"m/d/Y H:m"); die();
      
      if ($objIndisponibilidadePeticionamentoDTO===null){
        throw new InfraException("Registro não encontrado.");
      }
      break;

    default:
      throw new InfraException("Ação '".$_GET['acao']."' não reconhecida.");
  }


}catch(Exception $e){
  PaginaSEI::getInstance()->processarExcecao($e);
}

$hashAnexo = "";
$idAnexo = "";

//na primeira vez que entrar na tela de geração de nova versão não deve processar os anexos (a tabela deve ser montada com os anexos do clone)
if (isset($_GET['id_indisponibilidade_peticionamento'])){
		
    $arrAcoesDownload = array();
    $arrAcoesRemover = array();
    
	if ($_GET ['acao'] != 'indisponibilidade_peticionamento_consultar')	{
		$_POST ['hdnAnexos'] = IndisponibilidadeAnexoPeticionamentoINT::montarAnexosIndisponibilidade ( $objIndisponibilidadePeticionamentoDTO->getNumIdIndisponibilidade (), false, $arrAcoesDownload, true, $arrAcoesRemover );
		//echo $_POST ['hdnAnexos']; die();
	} 
	else {
		$_POST ['hdnAnexos'] = IndisponibilidadeAnexoPeticionamentoINT::montarAnexosIndisponibilidade ( $objIndisponibilidadePeticionamentoDTO->getNumIdIndisponibilidade (), false, $arrAcoesDownload, false, $arrAcoesRemover );
	}
	
	$arrDados = split("±", $_POST['hdnAnexos'], 2);
	$anexoDTODownloadRN = new IndisponibilidadeAnexoPeticionamentoRN();
	$anexoDTODownloadDTO = new IndisponibilidadeAnexoPeticionamentoDTO();
	$anexoDTODownloadDTO->retTodos();
	$anexoDTODownloadDTO->setNumIdAnexoPeticionamento( $arrDados[0] );
	$anexoDTODownloadDTO = $anexoDTODownloadRN->listar($anexoDTODownloadDTO);
	
	if(count( $anexoDTODownloadDTO ) > 0){		
		$idAnexo = $anexoDTODownloadDTO[0]->getNumIdAnexoPeticionamento();
		$hashAnexo = $anexoDTODownloadDTO[0]->getStrHash();
	}
	
}


PaginaSEI::getInstance()->montarDocType();
PaginaSEI::getInstance()->abrirHtml();
PaginaSEI::getInstance()->abrirHead();
PaginaSEI::getInstance()->montarMeta();
PaginaSEI::getInstance()->montarTitle(':: '.PaginaSEI::getInstance()->getStrNomeSistema().' - '.$strTitulo.' ::');
PaginaSEI::getInstance()->montarStyle();
PaginaSEI::getInstance()->abrirStyle();

PaginaSEI::getInstance()->fecharStyle();
PaginaSEI::getInstance()->montarJavaScript();
PaginaSEI::getInstance()->abrirJavaScript();
?>


<?
PaginaSEI::getInstance()->fecharJavaScript();
?>
<style type="text/css">

#lblDtInicio {position:absolute;left:2%;top:4.2%;width:12%;}
#txtDtInicio {position:absolute;left:2%;top:7%;width:12%;}
#imgDtInicio {position:absolute;left:15%;top:7%;}

#lblDtFim {position:absolute;left:19.5%;top:4.2%;width:12%;}
#txtDtFim {position:absolute;left:19.5%;top:7%;width:12%;}
#imgDtFim {position:absolute;left:32.5%;top:7%;}

#lblResumoIndisponibilidade {position:absolute;left:0%;top:15.5%;width:25%;}
#txtResumoIndisponibilidade {position:absolute;left:0%;top:18%;width:75%;}

#fldProrrogacao {height: 10%; width: 86%;}

.sizeFieldset {height: 12.5%; width: 86%;}
.fieldsetClear {border:none !important;}

</style>
<?php 
PaginaSEI::getInstance()->fecharHead();
PaginaSEI::getInstance()->abrirBody($strTitulo,'onload="inicializar();"');
?>
<form id="frmIndisponibilidadeCadastro" method="post" onsubmit="return OnSubmitForm();"  action="<?=PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?acao='.$_GET['acao'].'&acao_origem='.$_GET['acao']))?>">
<?
PaginaSEI::getInstance()->montarBarraComandosSuperior($arrComandos);
PaginaSEI::getInstance()->abrirAreaDados('60em');
?>

 <fieldset id="fldPeriodoIndisponibilidade" class="infraFieldset sizeFieldset">
 
 <!--  Data Inicio  -->
  	<legend class="infraLegend">&nbsp;Período de Indisponibilidade&nbsp;</legend>
  	<label id="lblDtInicio" for="txtDtInicio" class="infraLabelObrigatorio">Início:</label>
    <input type="text" onchange="validDate('I')" onkeypress="return infraMascara(this, event, '##/##/#### ##:##');" name="txtDtInicio" id="txtDtInicio" value="<?= $objIndisponibilidadePeticionamentoDTO->getDthDataInicioFormatada() ?>" class="infraText" />
 	<img src="<?=PaginaSEI::getInstance()->getDiretorioImagensGlobal()?>/calendario.gif" id="imgDtInicio" title="Selecionar Data/Hora Inicial" alt="Selecionar Data/Hora Inicial" class="infraImg" onclick="infraCalendario('txtDtInicio',this,true,'<?=InfraData::getStrDataAtual().' 00:00'?>');" />
    
  <!--  Data Fim  -->
  	<label id="lblDtFim" for="txtDtFim" class="infraLabelObrigatorio">Fim:</label>
    <input type="text" onchange="validDate('F')" onkeypress="return infraMascara(this, event, '##/##/#### ##:##');" name="txtDtFim" id="txtDtFim" value="<?= $objIndisponibilidadePeticionamentoDTO->getDthDataFimFormatada() ?>" class="infraText"/>
    <img src="<?=PaginaSEI::getInstance()->getDiretorioImagensGlobal()?>/calendario.gif" id="imgDtFim" title="Selecionar Data/Hora Final" 
         alt="Selecionar Data/Hora Final" class="infraImg" onclick="infraCalendario('txtDtFim',this,true,'<?=InfraData::getStrDataAtual().' 23:59'?>');" />
    
 </fieldset>
        
    <!-- Resumo da Indisponibilidade -->
    
    <fieldset class="sizeFieldset fieldsetClear">
    <label id="lblResumoIndisponibilidade" for="txtResumoIndisponibilidade" class="infraLabelObrigatorio">Resumo da Indisponibilidade:</label>
    <textarea type="text" id="txtResumoIndisponibilidade" rows="3" name="txtResumoIndisponibilidade" class="infraText" onkeypress="return infraMascaraTexto(this,event,250);" maxlength="250" tabindex="<?=PaginaSEI::getInstance()->getProxTabDados()?>"><?php echo isset($objIndisponibilidadePeticionamentoDTO) ? $objIndisponibilidadePeticionamentoDTO->getStrResumoIndisponibilidade() : '' ?></textarea>
    </fieldset>
        
   <fieldset id="fldProrrogacao" class="infraFieldset">
    <legend class="infraLegend">Indisponibilidade justifica a prorrogação automática dos prazos</legend>
    
    <div id="divProrrogacaoSim" style="margin-top:0.3%">
   <input <?php echo isset($objIndisponibilidadePeticionamentoDTO) && ($objIndisponibilidadePeticionamentoDTO->getStrSinProrrogacao() && $objIndisponibilidadePeticionamentoDTO->getStrSinProrrogacao() == 'S') ? 'checked="checked"' : ''  ?> type="radio" id="rdProrrogacaoSim" name="rdProrrogacao[]" /> <label id="lblProrrogacaoSim" class="infraLabelCheckbox" for="rdProrrogacaoSim">Sim</label>
   </div>
   <div id="divProrrogacaoNao" style="margin-top:0.2%">
   <input <?php echo isset($objIndisponibilidadePeticionamentoDTO) && ($objIndisponibilidadePeticionamentoDTO->getStrSinProrrogacao() && $objIndisponibilidadePeticionamentoDTO->getStrSinProrrogacao() == 'N') ? 'checked="checked"' : ''  ?> type="radio" id="rdProrrogacaoNao" name="rdProrrogacao[]" /> <label id="lblProrrogacaoNao" class="infraLabelCheckbox" for="rdProrrogacaoNao">Não</label>
   </div>
   
   <input type="hidden" id="hdnArquivosPermitidos" name="hdnArquivosPermitidos" value='<?php echo isset($jsonExtPermitidas) && (!is_null($jsonExtPermitidas)) ? $jsonExtPermitidas : ''?>'/>
   <input type="hidden" id="hdnAnexos" name="hdnAnexos" value="<?=$_POST['hdnAnexos']?>"/>
   <input type="hidden" id="hdnAnexosInicial" name="hdnAnexosInicial" value="<?=$_POST['hdnAnexos']?>"/>
   
   </fieldset>
   
</form>
    
    <fieldset class="fieldsetClear">
    
<form id="frmAnexos" style="margin:0;border:0;padding:0;">

  <div id="divArquivo" class="infraAreaDados" style="height:6.5em;">
   <br/>
   <label id="lblAnexarArquivo" class="infraLabelOpcional" for="lblAnexarArquivo">Anexar Arquivo:</label>
   <br/>      
    <input style="margin-top:0.3%" type="file" id="filArquivo" name="filArquivo" size="50" onchange="validarQtdArquivos();" tabindex="1000"/><br />
  </div>
  
  <div id="divAnexos" style="height:10em;">    
    
     <table id="tblAnexos" name="tblAnexos" class="infraTable" style="width:90%;">
    
        <caption class="infraCaption"><?=PaginaSEI::getInstance()->gerarCaptionTabela("Anexos",0)?></caption>
       
    		<tr>
    			<th style="display:none;">ID</th>
    			<th width="30%" class="infraTh">Nome</th>
    			<th class="infraTh" align="center">Data</th>
    			<th style="display:none;">Bytes</th>
    			<th width="10%" class="infraTh" align="center">Tamanho</th>
    			<th width="10%" class="infraTh" align="center">Usuário</th>
    			<th width="10%" class="infraTh" align="center">Unidade</th>
    			<th width="10%" class="infraTh">Ações</th>
    		</tr>
      </table>
      <!-- campo hidden correspondente (hdnAnexos) deve ficar no outro form -->
    </div>
</form>

    </fieldset>
  
  <input type="hidden" id="hdnIdIndisponibilidadePeticionamento" name="hdnIdIndisponibilidadePeticionamento" 
         value="<?php echo isset($_GET['id_indisponibilidade_peticionamento']) ? $_GET['id_indisponibilidade_peticionamento'] : '' ?>" />
  
  <input type="hidden" id="hdnSinProrrogacao" name="hdnSinProrrogacao" value="" />
  <input type="hidden" id="hdnNomeArquivoDownload" name="hdnNomeArquivoDownload" value="" />
  <input type="hidden" id="hdnNomeArquivoDownloadReal" name="hdnNomeArquivoDownloadReal" value="" />
  
  <?
  PaginaSEI::getInstance()->fecharAreaDados();
  PaginaSEI::getInstance()->fecharBody();
  PaginaSEI::getInstance()->fecharHtml();
?>
<script type="text/javascript">
var objUpload = null;
var objTabelaAnexos = null;

//funcao para visualizar estruturas complexas (arrays, objetos) em JS
function dump(arr,level) {

	var dumped_text = "";

	if(!level) level = 0;
	
	//The padding given at the beginning of the line.
	var level_padding = "";
	for(var j=0;j<level+1;j++) level_padding += "    ";
	
	if(typeof(arr) == 'object') { //Array/Hashes/Objects 
		for(var item in arr) {
			var value = arr[item];
			
			if(typeof(value) == 'object') { //If it is an array,
				dumped_text += level_padding + "'" + item + "' ...\n";
				dumped_text += dump(value,level+1);
			} else {
				dumped_text += level_padding + "'" + item + "' => \"" + value + "\"\n";
			}
		}
	} else { //Stings/Chars/Numbers etc.
		dumped_text = "===>"+arr+"<===("+typeof(arr)+")";
	}
	return dumped_text;
}

function inicializar(){

  if ('<?=$_GET['acao']?>'=='indisponibilidade_peticionamento_cadastrar' || '<?=$_GET['acao']?>'=='indisponibilidade_peticionamento_alterar'){
    document.getElementById('txtDtInicio').focus();
  } else if ('<?=$_GET['acao']?>'=='indisponibilidade_peticionamento_consultar'){
	document.getElementById("filArquivo").disabled = 'disabled';
    infraDesabilitarCamposAreaDados();
  }else{
    document.getElementById('btnCancelar').focus();
  }

  objUpload = new infraUpload('frmAnexos','<?=$strLinkAnexos?>');
  objUpload.finalizou = function(arr){
  objTabelaAnexos.adicionar([arr['nome_upload'],arr['nome'],arr['data_hora'],arr['tamanho'],infraFormatarTamanhoBytes(arr['tamanho']),'<?=PaginaSEI::getInstance()->formatarParametrosJavaScript(SessaoSEI::getInstance()->getStrSiglaUsuario())?>' ,'<?=PaginaSEI::getInstance()->formatarParametrosJavaScript(SessaoSEI::getInstance()->getStrSiglaUnidadeAtual())?>']);
  
  var strHash = document.getElementById('hdnAnexos').value;
  var arrHash = strHash.split('±');
    
  var urlBase = "<?=PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?acao=indisponibilidade_peticionamento_download'))?>";
  document.getElementById('hdnNomeArquivoDownload').value = arrHash[0];
  document.getElementById('hdnNomeArquivoDownloadReal').value = arrHash[1];
  
  objTabelaAnexos.adicionarAcoes(
	arr['nome_upload'],
	"<a href='#' onclick=\"downloadArquivo( ' "+ urlBase +"')\"><img title='Baixar anexo' alt='Baixar anexo' src='/infra_css/imagens/download.gif' class='infraImg' /></a>",
	false,
	true);
	  
  document.getElementById("filArquivo").value = '';
  //document.getElementById('divArquivo').style.display = 'none';
}

  objUpload.validar = function(arr){

	  var arquivo = document.getElementById('filArquivo').value;
	  var ext = (arquivo.substring(arquivo.lastIndexOf(".")).toLowerCase()).split('.')[1];

	  var extPermitida = false;
	  
	  var obj = JSON.parse($('#hdnArquivosPermitidos').val());

	  for (var index in obj) {

			 if(obj[index] ==  ext){
			    extPermitida = true;
			 }
	  }
	  
    if(!extPermitida){
        document.getElementById('filArquivo').value = '';
		alert('A extensão do arquivo não é permitida.');
    }
    
 	return extPermitida;

  }
  
  //Monta tabela de anexos
  objTabelaAnexos = new infraTabelaDinamica('tblAnexos','hdnAnexos',false,false);
  objTabelaAnexos.gerarEfeitoTabela=true;

  //Monta ações para remover anexos
  <? if (count($arrAcoesRemover)>0 || $_POST['hdnAnexos'] != ""  ){

  	    if( $_GET['acao'] == 'indisponibilidade_peticionamento_consultar' ){
  	       $arrDados = split("±", $_POST['hdnAnexos'], 2);  	
  	       array_push( $arrAcoesRemover , $arrDados[0]);
        }
  
  	    foreach(array_keys($arrAcoesRemover) as $id) { 
          
        $urlBase = PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?acao=indisponibilidade_peticionamento_download'));
        
  ?>        
        var strHash = document.getElementById('hdnAnexos').value;
        var arrHash = strHash.split('±');

        //hash anexo / nome real do arquivo
        //alert('teste 1 <?=$idAnexo?>');
  		document.getElementById('hdnNomeArquivoDownload').value = '<?=$hashAnexo?>';
  		document.getElementById('hdnNomeArquivoDownloadReal').value = arrHash[1];

		<?php if( $_GET['acao'] == 'indisponibilidade_peticionamento_consultar' ){ ?>

  		objTabelaAnexos.adicionarAcoes(
		    '<?=$idAnexo?>',
		 	"<a href='#' onclick=\"downloadArquivo('<?= $urlBase ?>')\"><img title='Baixar anexo' alt='Baixar anexo' src='/infra_css/imagens/download.gif' class='infraImg' /></a>",
		 	false,
		 	false);

  		//alert('teste 2');
  		
  		<?php } else { ?>

  		objTabelaAnexos.adicionarAcoes(
  			    '<?=$id?>',
  			 	"<a href='#' onclick=\"downloadArquivo('<?= $urlBase ?>')\"><img title='Baixar anexo' alt='Baixar anexo' src='/infra_css/imagens/download.gif' class='infraImg' /></a>",
  			 	false,
  			 	true);

  	  	//alert('teste 3');
		
  		<?php } ?>
	 	
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

function validarQtdArquivos(){

	var table = document.getElementById("tblAnexos");
	var linha = table.getElementsByTagName("tr");
	var qtd   = parseInt(linha.length) - 1;
	
	if(qtd == 0){
		objUpload.executar();
	}
	else{
		document.getElementById("filArquivo").value = '';
		alert('Para anexar um novo arquivo remova o arquivo anexado anteriormente, cada indisponibilidade deve possuir somente um anexo.')
	}
}

function validarDataInicialMaiorQueFinal()
{
	var dataInicial = returnDateTime(document.getElementById('txtDtInicio').value);
	var dataFinal   = returnDateTime(document.getElementById('txtDtFim').value);	
	var valido = (dataInicial.getTime() <= dataFinal.getTime());
		
	if(!valido){
		alert('A Data/Hora Inicio deve ser menor  que a Data/Hora Fim');
		return false;
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
	

function validarCadastro(){
	preencherHdnProrrogacao();

	var campoDtIni = document.getElementById('txtDtInicio');
    var tamanhoDataInicio = parseInt((campoDtIni.value).length);
    var campoDtFim = document.getElementById('txtDtFim');
    var tamanhoDataInicio = parseInt((campoDtFim.value).length);
	
	
	 if (infraTrim(document.getElementById('txtDtInicio').value)=='') {
		    alert('Informe o Início.');
		    document.getElementById('txtDtInicio').focus();
		    return false;
	  }

    if (infraTrim(document.getElementById('txtDtFim').value)=='') {
			 alert('Informe o Fim.');
			 document.getElementById('txtDtFim').focus();
			 return false;
	  }

    if(infraTrim(document.getElementById('txtResumoIndisponibilidade').value)=='')
      {
			alert('Informe o Resumo da Indisponibilidade.');
			document.getElementById('txtResumoIndisponibilidade').focus();
			return false;
	  }

    var prorrogacao = document.getElementsByName('rdProrrogacao[]')[0].checked;
		  if(!prorrogacao)
		   {
	        prorrogacao =  document.getElementsByName('rdProrrogacao[]')[1].checked;
		   }

		  if(!prorrogacao)
		  {
			alert('Informe se a Indisponibilidade justifica prorrogação automática dos prazos');
			//alert('Informe a Indisponibilidade justifica prorrogação automática dos prazos.');
			document.getElementById('rdProrrogacaoSim').focus();
			return false;
	       }

	//Validar Datas 
		var dataInicial = returnDateTime(document.getElementById('txtDtInicio').value);
		var dataFinal   = returnDateTime(document.getElementById('txtDtFim').value);
		
		var valido = (dataInicial.getTime() < dataFinal.getTime());
		
		if(!valido){
			alert('A Data/Hora Inicio deve ser menor que a Data/Hora Fim');
			return false;
		}


		 if (tamanhoDataInicio < 16 || tamanhoDataInicio === 18)  {
			    alert('Data/Hora Inválida');
			    document.getElementById('txtDtInicio').focus();
			    document.getElementById('txtDtInicio').value = '';
			    return false;
		 }

		  if (tamanhoDataFim < 16 || tamanhoDataFim === 18)  {
			    alert('Data/Hora Inválida');
			    document.getElementById('txtDtFim').focus();
			    document.getElementById('txtDtFim').value = '';
			    return false;
		}	 
		

		return true;
	
}

function preencherHdnProrrogacao(){
	var rdProrrogacao = document.getElementsByName('rdProrrogacao[]')[0].checked ? 'S' : '';

	if(rdProrrogacao ==  ''){
	rdProrrogacao = document.getElementsByName('rdProrrogacao[]') [1].checked ? 'N' : '';
	}

	document.getElementById('hdnSinProrrogacao').value = rdProrrogacao;
}


function validDate(valor) {
		
	var campo = (valor === 'I') ?  document.getElementById('txtDtInicio') :  document.getElementById('txtDtFim');
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
		var valido = (dataInicial.getTime() <= dataFinal.getTime());

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

function OnSubmitForm() {
	 var prorrogacaoSim = document.getElementsByName('rdProrrogacao[]')[0].checked;
	 if(prorrogacaoSim)
   {
	 var msg =	'Ao marcar que a presente indisponibilidade justifica a prorrogação automática dos prazos, as intimações eletrônicas com prazo para cumprimento ainda pendentes terão seus prazos prorrogados até as 23h59min59s do primeiro dia útil seguinte ao fim da indisponibilidade indicada. Confirma a prorrogação automática dos prazos?';
	 
	if(confirm(msg)){
	  return validarCadastro();
	}else{
	  return false;
	}
   }
	 
   return validarCadastro();
}
	 
</script>