<?php
    
    try {

        require_once dirname(__FILE__).'/../../SEI.php';
        session_start();
        PaginaSEIExterna::getInstance()->setBolXHTML(false);
        //////////////////////////////////////////////////////////////////////////////
        //InfraDebug::getInstance()->setBolLigado(false);
        //InfraDebug::getInstance()->setBolDebugInfra(true);
        //InfraDebug::getInstance()->limpar();
        //////////////////////////////////////////////////////////////////////////////

        SessaoSEIExterna::getInstance()->validarLink();
        PaginaSEIExterna::getInstance()->setTipoPagina( InfraPagina::$TIPO_PAGINA_SIMPLES );
        SessaoSEIExterna::getInstance()->validarPermissao($_GET['acao']);
	
	    $idSerie = (isset($_GET['id_serie']) && $_GET['id_serie'] != -1) ? $_GET['id_serie'] : $_POST['hdnIdSerie'];
	    $objDocumentoDTO = new DocumentoDTO();
        $arrComandos = [];
  
        switch($_GET['acao']){

            case 'md_pet_formulario_gerar':
    
                $strTitulo = 'Gerar Formulário - Módulo Peticionamento Eletrônico - Acesso Externo';
    
                $arrComandos[] = '<button type="submit" accesskey="S" name="sbmFormularioProcessar" id="sbmFormularioProcessar" value="Salvar" class="infraButton"><span class="infraTeclaAtalho">S</span>alvar</button>';
    
                $objDocumentoDTO->setDblIdProcedimento($_GET['id_procedimento']);
                $objDocumentoDTO->setNumIdSerie($idSerie);
    
                //BUSCA DADOS DA SERIE
                $objSerieDTO = new SerieDTO();
                $objSerieDTO->setBolExclusaoLogica(false);
                $objSerieDTO->retNumIdTipoFormulario();
                $objSerieDTO->retStrNome();
                $objSerieDTO->setNumIdSerie($objDocumentoDTO->getNumIdSerie());
                $objSerieDTO = (new SerieRN())->consultarRN0644($objSerieDTO);
    
                if (empty($objSerieDTO)){
                    throw new InfraException("Registro de Tipo de Documento não encontrado.");
                }
    
                $objDocumentoDTO->setStrNomeSerie($objSerieDTO->getStrNome());
                $objDocumentoDTO->setNumIdTipoFormulario($objSerieDTO->getNumIdTipoFormulario());
    
                $objDocumentoDTO->setNumIdUnidadeResponsavel(SessaoSEI::getInstance()->getNumIdUnidadeAtual());
                $objDocumentoDTO->setStrSinBloqueado('N');
	
	            $objProtocoloDTO = new ProtocoloDTO();
	            $objProtocoloDTO->setDblIdProtocolo(null);
                $objProtocoloDTO->setStrStaNivelAcessoLocal(ProtocoloRN::$NA_PUBLICO);
                $objProtocoloDTO->setNumIdHipoteseLegal(null);
                $objProtocoloDTO->setStrStaGrauSigilo(null);
                $objProtocoloDTO->setStrDescricao(null);
                $objProtocoloDTO->setDtaGeracao(InfraData::getStrDataAtual());
                $objProtocoloDTO->setArrObjRelProtocoloAssuntoDTO(array());
                $objProtocoloDTO->setArrObjParticipanteDTO(array());
                $objProtocoloDTO->setArrObjObservacaoDTO(array());
                $objProtocoloDTO->setArrObjRelProtocoloAtributoDTO(AtributoINT::processar(null, $objDocumentoDTO->getNumIdTipoFormulario()));
    
                //ANEXOS
                $objProtocoloDTO->setArrObjAnexoDTO(array());
    
                $objDocumentoDTO->setObjProtocoloDTO($objProtocoloDTO);
                $objDocumentoDTO->setNumIdTipoConferencia(null);
                $objDocumentoDTO->setStrNumero(null);
                $objDocumentoDTO->setStrStaDocumento(DocumentoRN::$TD_FORMULARIO_GERADO);
                $objDocumentoDTO->setNumIdTextoPadraoInterno(null);
                $objDocumentoDTO->setStrProtocoloDocumentoTextoBase(null);
    
                if (isset($_POST['sbmFormularioProcessar'])) {
	
	                // Trecho que transforma o campo SINALIZADOR de 'on' para 'S' para poder passar na validação do core:
	                $objAtributoDTO = new AtributoDTO();
	                $objAtributoDTO->setBolExclusaoLogica(false);
	                $objAtributoDTO->retNumIdAtributo();
	                $objAtributoDTO->retStrNome();
	                $objAtributoDTO->retStrRotulo();
	                $objAtributoDTO->setStrSinAtivo('S');
	                $objAtributoDTO->setStrStaTipo('SINALIZADOR');
	                $objAtributoDTO->setNumIdTipoFormulario($objDocumentoDTO->getNumIdTipoFormulario());
	                $arrObjAtributoDTO = (new AtributoRN())->listarRN0165($objAtributoDTO);
	
	                if(!empty($arrObjAtributoDTO)){
		
		                // Monta um array com os nomes dos campos que estao salvos no Tipo de Formulario
		                $arrChavesModificadas = [];
		                $arrIdAtributo = InfraArray::converterArrInfraDTO($arrObjAtributoDTO, 'IdAtributo');
		                foreach($arrIdAtributo as $idAtributo){
			                $arrChavesModificadas[] = 'chkAtributo'.$idAtributo;
		                }
		
		                // Filtrar os itens que começam com 'chkAtributo' no $_POST
		                $filteredPostArray = array_filter($_POST, function($key) {
			                return strpos($key, 'chkAtributo') === 0;
		                }, ARRAY_FILTER_USE_KEY);
		
		                $camposNaoEnviadosPOST = array_diff(array_values($arrChavesModificadas), array_keys($filteredPostArray));
		
		                // Injeta os campos do tipo SINALIZADOR nao enviados no $_POST com valor padrao 'N':
		                foreach($camposNaoEnviadosPOST as $strAtributo){
			                $_POST[$strAtributo] = 'N';
		                }
		
		                // Ajusta o valor dos campos do tipo SINALIZADOR enviados:
		                foreach ($_POST as $key => $value) {
			                if (strpos($key, 'chkAtributo') === 0) {
				                $_POST[$key] = ($value === 'on') ? 'S' : 'N';
			                }
		                }
		
	                }
	                
                    try{
                        (new MdPetProcessoRN())->validarCamposFormulario($_POST);
                        //TODO: Possível risco de consumo excessivo de memória do servidor
                        SessaoSEIExterna::getInstance()->setAtributo('docPrincipalConteudoHTML', $_POST);
                        echo "<script>window.close();</script>";
                    }catch(Exception $e){
                        PaginaSEI::getInstance()->processarExcecao($e);
                    }
                }
    
                if( SessaoSEIExterna::getInstance()->isSetAtributo('docPrincipalConteudoHTML') ){
                    $_POST = SessaoSEIExterna::getInstance()->getAtributo('docPrincipalConteudoHTML');
                }
    
            break;

            default:
                throw new InfraException("Ação '".$_GET['acao']."' não reconhecida.");

        }
        
        AtributoINT::montar(null, $objDocumentoDTO->getNumIdTipoFormulario(),$strHtmlAtributos,$strJavascriptAtributos);

    }catch(Exception $e){
        PaginaSEI::getInstance()->processarExcecao($e);
    }

    PaginaSEIExterna::getInstance()->montarDocType();
    PaginaSEIExterna::getInstance()->abrirHtml();
    PaginaSEIExterna::getInstance()->abrirHead();
    PaginaSEIExterna::getInstance()->montarMeta();
    PaginaSEIExterna::getInstance()->montarStyle();
    EditorINT::montarCss();
    PaginaSEIExterna::getInstance()->abrirStyle();
    
?>

#lblNome {position:absolute;left:0%;top:0%;width:30%;}
#txtNome {position:absolute;left:0%;top:14%;width:30%;}
#lblDescricao {position:absolute;left:0%;top:40%;width:95%;}
#txtDescricao {position:absolute;left:0%;top:54%;width:95%;}
#lblConteudo {position:absolute;left:0%;top:25%;width:95%;}
#txaConteudo {height:300px;}
.cke_contents#cke_1_contents {height:300px;}
form {margin: 5px;overflow: hidden}
<?

PaginaSEIExterna::getInstance()->fecharStyle();
PaginaSEIExterna::getInstance()->montarJavaScript();
PaginaSEIExterna::getInstance()->abrirJavaScript();

?>

function inicializar(){
    infraEfeitoTabelas();
}

function OnSubmitForm() {
    <?= $strJavascriptAtributos ?>
    return true;
}

<?

PaginaSEIExterna::getInstance()->fecharJavaScript();
PaginaSEIExterna::getInstance()->fecharHead();

$strLink = SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?id_serie=' . $_GET['id_serie'] . '&acao='.$_GET['acao'].'&acao_origem='.$_GET['acao']);

?>
    
    <form action="<?= PaginaSEIExterna::getInstance()->formatarXHTML($strLink) ?>" id="frmTextoPadraoInternoCadastro" method="post" onsubmit="return OnSubmitForm();">
    <br>
    <? PaginaSEI::getInstance()->montarBarraComandosSuperior($arrComandos); ?>
    
    <div id="divSerieTitulo" class="tituloProcessoDocumento">
        <label id="lblSerieTitulo"><?=PaginaSEI::tratarHTML($objDocumentoDTO->getStrNomeSerie())?></label>
    </div>

    <?= $strHtmlAtributos ?>

    <input type="hidden" id="hdnIdSerie" name="hdnIdSerie" class="infraText" value="<?=$objDocumentoDTO->getNumIdSerie()?>" />
    <input type="hidden" id="hdnIdTipoFormulario" name="hdnIdTipoFormulario" class="infraText" value="<?=$objDocumentoDTO->getNumIdTipoFormulario()?>" />
    <input type="hidden" id="hdnNomeSerie" name="hdnNomeSerie" class="infraText" value="<?=PaginaSEI::tratarHTML($objDocumentoDTO->getStrNomeSerie())?>" />
    
    <? PaginaSEI::getInstance()->montarBarraComandosInferior($arrComandos); ?>
    
    </form>

<?
PaginaSEIExterna::getInstance()->fecharBody();
PaginaSEIExterna::getInstance()->fecharHtml();
?>