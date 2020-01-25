<?php
/**
 * Created by PhpStorm.
 * User: jhon.carvalho
 * Date: 19/12/2017
 * Time: 13:42
 */
try {
    require_once dirname(__FILE__) . '/../../SEI.php';

    session_start();

    SessaoSEI::getInstance()->validarLink();
    SessaoSEI::getInstance()->validarPermissao($_GET['acao']);

    $strDesabilitar = '';

    $arrComandos = array();

    //Tipo Processo - Nivel de Acesso
    $strLinkAjaxNivelAcesso = SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=md_pet_tipo_processo_nivel_acesso_auto_completar');

    //Tipo Documento Complementar
    $strLinkTipoDocumentoSelecao = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_pet_serie_selecionar&filtro=1&tipo_selecao=2&id_object=objLupaTipoDocumento&tipoDoc=E');
    $strLinkAjaxTipoDocumento = SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=md_pet_serie_auto_completar');

    //Tipo de Documento Essencial
    $strLinkTipoDocumentoEssencialSelecao = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_pet_serie_selecionar&filtro=1&tipo_selecao=2&id_object=objLupaTipoDocumentoEssencial&tipoDoc=E');

    //Tipo Processo
    $strLinkTipoProcessoSelecao = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=tipo_procedimento_selecionar&tipo_selecao=1&id_object=objLupaTipoProcesso');
    $strLinkAjaxTipoProcesso = SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=md_pet_tipo_processo_auto_completar');

    //Tipo Processo PF
    $strLinkTipoProcessoPFSelecao = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=tipo_procedimento_selecionar&tipo_selecao=1&id_object=objLupaTipoProcessoPF');
    
    //Unidade
    $strLinkUnidadeSelecao = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=unidade_selecionar_todas&tipo_selecao=1&id_object=objLupaUnidade');
    $strLinkUnidadeMultiplaSelecao = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=unidade_selecionar_todas&tipo_selecao=1&id_object=objLupaUnidadeMultipla');
    $strLinkAjaxUnidade = SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=md_pet_unidade_auto_completar');

    //Unidade PF
    $strLinkUnidadePFSelecao = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=unidade_selecionar_todas&tipo_selecao=1&id_object=objLupaUnidadePF');
    
    //Tipo Documento Principal
    $strLinkTipoDocPrincExternoSelecao = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_pet_serie_selecionar&filtro=1&tipoDoc=E&tipo_selecao=1&id_object=objLupaTipoDocPrinc');
    $strLinkTipoDocPrincGeradoSelecao = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_pet_serie_selecionar&filtro=1&tipoDoc=G&tipo_selecao=1&id_object=objLupaTipoDocPrinc');
    $strLinkAjaxTipoDocPrinc = SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=md_pet_serie_auto_completar');

    //Verificar webservice 
    $strLinkAjaxWebServiceSalvar = SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=confirmar_webservice_consultarCnpj');
    //Verificar Tipos de PRocessos para Peticionamento
    //$strLinkAjaxTipoProcessoPeticionamento = SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=confirmar_tipo_processo_pet');
    $objMdPetVincTpProcessoDTO = new MdPetVincTpProcessoDTO();
    $objMdPetVincTpProcessoRN = new MdPetVincTpProcessoRN();

    $objMdPetVincTpProcessoDTO->retTodos();
    $objMdPetVincTpProcessoDTO->retStrNomeProcedimento();
    $objMdPetVincTpProcessoDTO->retStrSiglaUnidade();
    $objMdPetVincTpProcessoDTO->retStrDescricaoUnidade();
    $objMdPetVincTpProcessoDTO->retStrTipoVinculo();
    $objMdPetVincTpProcessoDTO->retStrSinAtivo();
    $objMdPetVincTpProcessoDTO = $objMdPetVincTpProcessoRN->listar($objMdPetVincTpProcessoDTO);

    //Preparar Preenchimento Alteração
    if (count($objMdPetVincTpProcessoDTO) > 0 && !isset($_POST['sbmCadastrarTpProcessoVinculacao'])) {

        foreach ($objMdPetVincTpProcessoDTO as $obj) {
            if($obj->getStrTipoVinculo() == 'F'){
                $idTipoProcessoPF = $obj->getNumIdTipoProcedimento();
                $idUnidadePF = $obj->getNumIdUnidade();
                $nomeTipoProcessoPF = $obj->getStrNomeProcedimento();
                $especificacaoPF = $obj->getStrEspecificacao();
                if($obj->getStrSiglaUnidade()){
                    $nomeUnidadePF = $obj->getStrSiglaUnidade() . ' - ' . $obj->getStrDescricaoUnidade();
                }
                $exibirMenuAcessoExternoPF = $obj->getStrSinAtivo();                
            }else{
                $idTipoProcesso = $obj->getNumIdTipoProcedimento();
                $orientacoes = $obj->getStrOrientacoes();
                $idUnidade = $obj->getNumIdUnidade();
                $sinNAUsuExt = $obj->getStrSinNaUsuarioExterno() == 'S' ? 'checked = checked' : '';
                $sinNAPadrao = $obj->getStrSinNaPadrao() == 'S' ? 'checked = checked' : '';
                $idhipoteseLegal = $obj->getNumIdHipoteseLegal();
                $nomeTipoProcesso = $obj->getStrNomeProcedimento();
                $especificacaoPJ = $obj->getStrEspecificacao();
                if($obj->getStrSiglaUnidade()){
                    $nomeUnidade = $obj->getStrSiglaUnidade() . ' - ' . $obj->getStrDescricaoUnidade();
                }
                $staNivelAcesso = $obj->getStrStaNivelAcesso();
                $exibirMenuAcessoExterno = $obj->getStrSinAtivo();
                
                if ($obj->getStrSinNaPadrao() == 'S' && $staNivelAcesso == ProtocoloRN::$NA_RESTRITO) {
                    $valorParametroHipoteseLegal = $idhipoteseLegal;
                }
            }           
        }        
              
        $strItensSelNivelAcesso = '';

        $hipoteseLegal = '';
        $objMdPetVincRelSerieDTO = new MdPetVincRelSerieDTO();
        $objMdPetVincRelSerieRN = new MdPetVincRelSerieRN();

        $objMdPetVincRelSerieDTO->retNumIdSerie();
        $objMdPetVincRelSerieDTO->retStrSinObrigatorio();
        $objMdPetVincRelSerieDTO->retStrNomeSerie();
        $objMdPetVincRelSerieDTO->setNumIdMdPetVincTpProcesso(MdPetVincTpProcessoRN::$ID_FIXO_MD_PET_VINCULO_USU_EXT);
        $arrObjMdPetVincRelSerieDTO = $objMdPetVincRelSerieRN->listar($objMdPetVincRelSerieDTO);

        if (count($arrObjMdPetVincRelSerieDTO) > 0) {
            foreach ($arrObjMdPetVincRelSerieDTO as $objMdPetVincRelSerieDTO) {
                if ($objMdPetVincRelSerieDTO->getStrSinObrigatorio() == 'N')
                    $strItensSelSeries .= '<option value =' . $objMdPetVincRelSerieDTO->getNumIdSerie() . '>' . $objMdPetVincRelSerieDTO->getStrNomeSerie() . '</option>';
                else
                    $strItensSelSeriesEss .= '<option value =' . $objMdPetVincRelSerieDTO->getNumIdSerie() . '>' . $objMdPetVincRelSerieDTO->getStrNomeSerie() . '</option>';
            }
        }

    } else {

        $nomeTipoProcesso = '';
        $idTipoProcesso = $_POST['hdnIdTipoProcesso'];
        $idTipoProcessoPF = $_POST['hdnIdTipoProcessoPF'];
        $staNivelAcesso = $_POST['selNivelAcesso'];
        $orientacoes = $_POST['txtOrientacoes'];
        $idUnidade = '';
        $nomeUnidade = '';
        $sinNAUsuExt = '';
        $sinNAPadrao = '';
        $idhipoteseLegal = $_POST['selHipoteseLegal'];
        $valorParametroHipoteseLegal = $idhipoteseLegal;
        $hipoteseLegal = 'style="display:none;"';
        $gerado = '';
        $externo = '';
        $nomeSerie = '';
        $idSerie = '';
        $strItensSelSeries = '';
        $strItensSelSeriesEss = '';
        $staNivelAcesso = $_POST['selNivelAcesso'];

        $strItensSelNivelAcesso = '';
        $strItensSelHipoteseLegal = '';
    }

    //Carregando campos select
    $strItensSelHipoteseLegal = MdPetVincTpProcessoINT::montarSelectHipoteseLegal(null, null, $idhipoteseLegal);
    $strItensSelUnidades = UnidadeINT::montarSelectSiglaDescricao(null, null, $idUnidade);

    switch ($_GET['acao']) {

        case 'md_pet_vinc_tp_processo_cadastrar':
            $strTitulo = 'Parâmetros para Vinculação a Usuário Externo';
            $arrComandos[] = '<button type="submit" accesskey="S" id="sbmCadastrarTpProcessoVinculacao" name="sbmCadastrarTpProcessoVinculacao" value="Salvar"  class="infraButton"><span class="infraTeclaAtalho">S</span>alvar</button>';
            $arrComandos[] = '<button type="button" accesskey="C" id="btnCancelar" value="Cancelar" onclick="location.href=\'' . PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?acao=procedimento_controlar&acao_origem=' . $_GET['acao'])) . '\';" class="infraButton"><span class="infraTeclaAtalho">C</span>ancelar</button>';


            $objMdPetVincTpProcessoDTO = new MdPetVincTpProcessoDTO();

            $objMdPetVincTpProcessoDTO->setNumIdTipoProcedimento($idTipoProcesso);
            
            // Vinculo do tipo PF
            if($_POST['rdMenuAcessoExternoPF']){
                $objMdPetVincTpProcessoPFDTO = new MdPetVincTpProcessoDTO();
                $objMdPetVincTpProcessoPFDTO->setNumIdTipoProcedimento($idTipoProcessoPF);
            }

            if (isset($_POST['hdnIdTipoProcesso']) || $idTipoProcesso != '') {
                $strItensSelNivelAcesso = MdPetTipoProcessoINT::montarSelectNivelAcesso(null, null, $staNivelAcesso, $idTipoProcesso);
            }
            $objMdPetVincTpProcessoDTO->setStrOrientacoes($orientacoes);
            $objMdPetVincTpProcessoDTO->setStrSinNaUsuarioExterno('N');
            $objMdPetVincTpProcessoDTO->setStrSinNaPadrao('N');


            // nivel de acesso
            if ($_POST['rdNivelAcesso'][0] == 1)
                $objMdPetVincTpProcessoDTO->setStrSinNaUsuarioExterno('S');
            if ($_POST['rdNivelAcesso'][0] == 2)
                $objMdPetVincTpProcessoDTO->setStrSinNaPadrao('S');


            if ($_POST['selNivelAcesso'] != '') {
                $objMdPetVincTpProcessoDTO->setStrStaNivelAcesso($staNivelAcesso);

                if ($_POST['selNivelAcesso'] == ProtocoloRN::$NA_RESTRITO && $valorParametroHipoteseLegal != '0') {
                    $objMdPetVincTpProcessoDTO->setNumIdHipoteseLegal($idhipoteseLegal);
                    $hipoteseLegal = 'style="display: inherit;"';
                }
            }         

            if (isset($_POST['sbmCadastrarTpProcessoVinculacao'])) {
                try {

                    //Pessoa jurídica
                    if($_POST['rdMenuAcessoExterno']){

                        $relTipoProcedimentoAssuntoRN = new RelTipoProcedimentoAssuntoRN();
                        $tipoProcedimentoRN = new TipoProcedimentoRN();
                        $objTipoProcedimentoDTO = new TipoProcedimentoDTO();
                        $objRelTipoProcedimentoAssuntoDTO = new RelTipoProcedimentoAssuntoDTO();
                        $objTipoProcedimentoDTO->setNumIdTipoProcedimento($_POST['hdnIdTipoProcesso']);
                        $objTipoProcedimentoDTO->retNumIdTipoProcedimento();
                        $objTipoProcedimentoDTO = $tipoProcedimentoRN->consultarRN0267($objTipoProcedimentoDTO);
                        $objRelTipoProcedimentoAssuntoDTO->setNumIdTipoProcedimento($objTipoProcedimentoDTO->getNumIdTipoProcedimento());
                        $objRelTipoProcedimentoAssuntoDTO->retTodos();
                        $objRelTipoProcedimentoAssuntoDTO = $relTipoProcedimentoAssuntoRN->listarRN0192($objRelTipoProcedimentoAssuntoDTO);
                        if(!$objRelTipoProcedimentoAssuntoDTO){
                            $msg = "Por favor informe um tipo de processo que na parametrização do SEI tenha indicação de pelo menos uma sugestão de assunto.";
                            PaginaSEI::getInstance()->setStrMensagem($msg,InfraPagina::$TIPO_MSG_AVISO);
                            header('Location: '.SessaoSEI::getInstance()->assinarLink('controlador.php?acao='.$_GET['acao_origem'].'&acao_origem='.$_GET['acao']));
                            die;
                        }

                        $arrIdTipoDocumento = PaginaSEI::getInstance()->getArrValuesSelect($_POST['hdnSerie']);
                        $arrIdTipoDocumentoEssencial = PaginaSEI::getInstance()->getArrValuesSelect($_POST['hdnSerieEssencial']);

                        $nomeTipoProcesso = $_POST['txtTipoProcesso'];
                        $idTipoProcesso = $objMdPetVincTpProcessoDTO->getNumIdTipoProcedimento();
                        $orientacoes = $objMdPetVincTpProcessoDTO->getStrOrientacoes();
                        $nomeUnidade = $_POST['txtUnidade'];
                        $sinNAUsuExt = $objMdPetVincTpProcessoDTO->getStrSinNaUsuarioExterno() == 'S' ? 'checked = checked' : '';
                        $sinNAPadrao = $objMdPetVincTpProcessoDTO->getStrSinNaPadrao() == 'S' ? 'checked = checked' : '';
                        $nomeSerie = $_POST['txtTipoDocPrinc'];

                        $idUnidade = $_POST['hdnIdUnidade'];
                        $especificacaoPJ = $_POST['txtEspecProcPJ'];
                        $objMdPetVincTpProcessoDTO->setStrEspecificacao($especificacaoPJ);
                        $objMdPetVincTpProcessoDTO->setNumIdUnidade($idUnidade);
                        $objMdPetVincTpProcessoDTO->setStrTipoVinculo('J');

                        $objMdPetVincTpProcessoDTO->setStrSinAtivo($_POST['rdMenuAcessoExterno']);
                        $exibirMenuAcessoExterno = $_POST['rdMenuAcessoExterno'];                  
                        $objMdPetVincTpProcessoRN->cadastrar($objMdPetVincTpProcessoDTO);
                    }                   
                    
                    //Pessoa física
                    if($_POST['rdMenuAcessoExternoPF']){
                        $relTipoProcedimentoAssuntoRN = new RelTipoProcedimentoAssuntoRN();
                        $tipoProcedimentoRN = new TipoProcedimentoRN();
                        $objTipoProcedimentoDTO = new TipoProcedimentoDTO();
                        $objRelTipoProcedimentoAssuntoDTO = new RelTipoProcedimentoAssuntoDTO();
                        $objTipoProcedimentoDTO->setNumIdTipoProcedimento($_POST['hdnIdTipoProcessoPF']);
                        $objTipoProcedimentoDTO->retNumIdTipoProcedimento();
                        $objTipoProcedimentoDTO = $tipoProcedimentoRN->consultarRN0267($objTipoProcedimentoDTO);
                        $objRelTipoProcedimentoAssuntoDTO->setNumIdTipoProcedimento($objTipoProcedimentoDTO->getNumIdTipoProcedimento());
                        $objRelTipoProcedimentoAssuntoDTO->retTodos();
                        $objRelTipoProcedimentoAssuntoDTO = $relTipoProcedimentoAssuntoRN->listarRN0192($objRelTipoProcedimentoAssuntoDTO);
                        if(!$objRelTipoProcedimentoAssuntoDTO){
                            $msg = "Por favor informe um tipo de processo que na parametrização do SEI tenha indicação de pelo menos uma sugestão de assunto.";
                            PaginaSEI::getInstance()->setStrMensagem($msg,InfraPagina::$TIPO_MSG_AVISO);
                            header('Location: '.SessaoSEI::getInstance()->assinarLink('controlador.php?acao='.$_GET['acao_origem'].'&acao_origem='.$_GET['acao']));
                            die;
                        }
                            $nomeTipoProcessoPF = $_POST['txtTipoProcessoPF'];
                            $idTipoProcessoPF = $objMdPetVincTpProcessoPFDTO->getNumIdTipoProcedimento();
                            $nomeUnidadePF = $_POST['txtUnidadePF'];
                            $idUnidadePF = $_POST['hdnIdUnidadePF'];
                            $objMdPetVincTpProcessoPFDTO->setNumIdUnidade($idUnidadePF);
                            $especificacaoPF = $_POST['txtEspecProcPF'];
                            $objMdPetVincTpProcessoPFDTO->setStrEspecificacao($especificacaoPF);
                            $objMdPetVincTpProcessoPFDTO->setStrTipoVinculo('F');
                            $objMdPetVincTpProcessoPFDTO->setStrSinAtivo($_POST['rdMenuAcessoExternoPF']);
                            $exibirMenuAcessoExternoPF = $_POST['rdMenuAcessoExternoPF'];
                            $objMdPetVincTpProcessoRN->cadastrar($objMdPetVincTpProcessoPFDTO);

                    }

                    $objMdPetVincRelSerieDTO = new MdPetVincRelSerieDTO();
                    $objMdPetVincRelSerieDTO->setNumIdMdPetVincTpProcesso(MdPetVincTpProcessoRN::$ID_FIXO_MD_PET_VINCULO_USU_EXT);
                    $objMdPetVincRelSerieDTO->retNumIdMdPetVincRelSerie();

                    $objMdPetVincRelSerieRN = new MdPetVincRelSerieRN();
                    $objMdPetVincRelSerieDTO = $objMdPetVincRelSerieRN->listar($objMdPetVincRelSerieDTO);

                    if (count($objMdPetVincRelSerieDTO) > 0) {
                        $objMdPetVincRelSerieRN->excluir($objMdPetVincRelSerieDTO);
                    }

                    //Tipo de Documento Essencial
                    foreach ($arrIdTipoDocumentoEssencial as $numIdTipoDocumentoEss) {
                        $objMdPetVincRelSerieDTO = new MdPetVincRelSerieDTO();

                        $objMdPetVincRelSerieDTO->setNumIdMdPetVincTpProcesso(MdPetVincTpProcessoRN::$ID_FIXO_MD_PET_VINCULO_USU_EXT);
                        $objMdPetVincRelSerieDTO->setNumIdSerie($numIdTipoDocumentoEss);
                        $objMdPetVincRelSerieDTO->setStrSinObrigatorio('S');

                        $objMdPetVincRelSerieDTO = $objMdPetVincRelSerieRN->cadastrar($objMdPetVincRelSerieDTO);
                    }

                    //Tipo de Documento Complementar
                    foreach ($arrIdTipoDocumento as $numIdTipoDocumento) {
                        $objMdPetVincRelSerieDTO = new MdPetVincRelSerieDTO();

                        $objMdPetVincRelSerieDTO->setNumIdMdPetVincTpProcesso(MdPetVincTpProcessoRN::$ID_FIXO_MD_PET_VINCULO_USU_EXT);
                        $objMdPetVincRelSerieDTO->setNumIdSerie($numIdTipoDocumento);
                        $objMdPetVincRelSerieDTO->setStrSinObrigatorio('N');

                        $objMdPetVincRelSerieDTO = $objMdPetVincRelSerieRN->cadastrar($objMdPetVincRelSerieDTO);
                    }


                    //header('Location: ' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . PaginaSEI::getInstance()->getAcaoRetorno() . '&acao_origem=' . $_GET['acao'] . '&id_tipo_processo_peticionamento=' . $objMdPetTipoProcessoDTO->getNumIdTipoProcessoPeticionamento() . PaginaSEI::getInstance()->montarAncora($objMdPetTipoProcessoDTO->getNumIdTipoProcessoPeticionamento())));

                } catch (Exception $e) {
                    PaginaSEI::getInstance()->processarExcecao($e);
                }
            }

            break;

    }


} catch (Exception $e) {
    PaginaSEI::getInstance()->processarExcecao($e);
}

PaginaSEI::getInstance()->montarDocType();
PaginaSEI::getInstance()->abrirHtml();
PaginaSEI::getInstance()->abrirHead();
PaginaSEI::getInstance()->montarMeta();
PaginaSEI::getInstance()->montarTitle(':: ' . PaginaSEI::getInstance()->getStrNomeSistema() . ' - ' . $strTitulo . ' ::');
PaginaSEI::getInstance()->montarStyle();
PaginaSEI::getInstance()->abrirStyle();

PaginaSEI::getInstance()->fecharStyle();
PaginaSEI::getInstance()->montarJavaScript();
PaginaSEI::getInstance()->abrirJavaScript();

PaginaSEI::getInstance()->fecharJavaScript();

?>


<style type="text/css">


#container{
  width: 100%;
}

.clear {
  clear: both;
}

.bloco {
  float: left;
  margin-top: 1%;
  margin-right: 1%;
}

label[for^=txt] {
  display: block;
  white-space: nowrap;
}
label[for^=s] {
  display: block;
  white-space: nowrap;
}
label[for^=file] {
  display: block;
  white-space: nowrap;
}

img[name=ajuda] {
  margin-bottom: -4px;
  width: 16px !important;
  height: 16px !important;
}

#txtTipoProcesso{
  width:66.3%;
}
#txtTipoProcessoPF{
  width:66.3%;
}
#txtUnidade {
  width: 66.3%;
}
#txtUnidadePF {
  width: 66.3%;
}
#txtSerieEssencial {
  width: 46%;
}
#txtSerie {
  width: 46%;
}
#selDescricaoEssencial {
  width: 66.5%;
}
#selDescricao {
  width: 66.5%;
}

.fieldsetClear {
  border: none !important;
  width: 100%
}

.tamanhoFieldset {
  height: auto;
  width: 100%;
}
#txtEspecProc{
    width:30%;
}
#txtEspecProcPF{
    width:30%;
}
#txtEspecProcPJ{
    width:30%;
}


</style>
<?php


PaginaSEI::getInstance()->fecharHead();
PaginaSEI::getInstance()->abrirBody($strTitulo, 'onload="inicializar();"');

?>
<form id="frmTipoProcessoCadastro" method="post" onsubmit="return OnSubmitForm();"
      action="<?= PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . $_GET['acao'] . '&acao_origem=' . $_GET['acao'])) ?>">
    <?
    PaginaSEI::getInstance()->montarBarraComandosSuperior($arrComandos);
    PaginaSEI::getInstance()->abrirAreaDados('98%');
    ?>

    <input type="hidden" name="hdnParametroHipoteseLegal" id="hdnParametroHipoteseLegal"
           value="<?php echo $valorParametroHipoteseLegal; ?>"/>

    <fieldset id="fldPeriodoIndisponibilidade" class="infraFieldset sizeFieldset">
        <legend class="infraLegend">Configurações para Vinculação de Usuário Externo a Pessoa Física
            <img src="<?= PaginaSEI::getInstance()->getDiretorioImagensGlobal() ?>/ajuda.gif" name="ajuda" id="imgAjuda" <?= PaginaSEI::montarTitleTooltip('Preencha estas configurações para permitir o Usuário Externo logado emitir Procurações Eletrônicas para representar determinada Pessoa Física.\n\n\n\n\n Defina o Tipo de Processo centralizador e a Unidade de Abertura para qual o Processo que conterá a Procuração Eletrônica deva ser enviado.') ?> class="infraImg"/>
        </legend>
        <!--  Tipo de Processo  -->
        <label id="lblTipoProcesso" for="txtTipoProcessoPF" class="infraLabelObrigatorio">Tipo de Processo Associado:</label>
        <input type="text" onchange="removerProcessoAssociadoPF(0);" id="txtTipoProcessoPF" name="txtTipoProcessoPF"
               class="infraText" value="<?php echo $nomeTipoProcessoPF; ?>"
               tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
        <input type="hidden" id="hdnIdTipoProcessoPF" name="hdnIdTipoProcessoPF" value="<?php echo $idTipoProcessoPF ?>"/>
        <img id="imgLupaTipoProcesso" onclick="objLupaTipoProcessoPF.selecionar(700,500);"
             src="/infra_css/imagens/lupa.gif" alt="Selecionar Tipo de Processo Associado"
             title="Selecionar Tipo de Processo Associado" class="infraImg"/>
        <img id="imgExcluirTipoProcesso" onclick="removerProcessoAssociadoPF(0);objLupaTipoProcessoPF.remover();"
             src="/infra_css/imagens/remover.gif" alt="Remover Tipo de Processo Associado"
             title="Remover Tipo de Processo Associado" class="infraImg"/>           
        <!--  Fim do Tipo de Processo -->
        <div class="clear">&nbsp;</div>

        <!--Especificação do Processo -->
         
         <label id="lblEspecProc" for="txtEspecProc" class="infraLabelObrigatorio">Especificação do Processo: <img src="<?= PaginaSEI::getInstance()->getDiretorioImagensGlobal() ?>/ajuda.gif" name="ajuda" id="imgAjuda" <?= PaginaSEI::montarTitleTooltip('O texto aqui configurado será utilizado na Especificação dos processos abertos, sempre limitado a 100 caracteres no momento da abertura do processo. \n \n No texto podem ser utilizadas as variáveis a seguir: @cpf@ - CPF da Pessoa Física Outorgante @nome_completo@ - Nome Completo da Pessoa Física Outorgante. ') ?> class="infraImg"/></label>
        <input type="text"  id="txtEspecProcPF" name="txtEspecProcPF" onkeypress="return infraMascaraTexto(this,event,100);"
               class="infraText" value="<?php echo $especificacaoPF ?>"
               tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
        
        <!--Especificação do Processo - FIM -->
        <div class="clear">&nbsp;</div>
        <!--  Unidade --> 
        <label id="lblUnidade" for="txtUnidade" class="infraLabelObrigatorio">Unidade para Abertura do Processo:</label>
        <input type="text"  id="txtUnidadePF" name="txtUnidadePF" class="infraText"
               value="<?php echo $nomeUnidadePF; ?>" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
        <input type="hidden" id="hdnIdUnidadePF" name="hdnIdUnidadePF" value="<?= $idUnidadePF ?>"/>
        <img id="imgLupaUnidade" onclick="objLupaUnidadePF.selecionar(700,500);" src="/infra_css/imagens/lupa.gif"
                 alt="Selecionar Unidade para Abertura do Processo" title="Selecionar Unidade para Abertura do Processo"
                 class="infraImg"/>
        <img id="imgExcluirUnidade" onclick="objLupaUnidadePF.remover();"
                 src="/infra_css/imagens/remover.gif" alt="Remover  Unidade para Abertura do Processo"
                 title="Remover Unidade para Abertura do Processo" class="infraImg"/>
        <!--Fim da Unidade -->
        <div class="clear">&nbsp;</div>
        
        <!--   Exibir menu Procuração Eletrônica --> 
        <label id="lblMenuAcessoExternoPF" for="" class="infraLabelObrigatorio">Exibir menu Procuração Eletrônica :</label>
        <img src="<?= PaginaSEI::getInstance()->getDiretorioImagensGlobal() ?>/ajuda.gif" name="ajuda" id="imgAjuda" <?= PaginaSEI::montarTitleTooltip('Esta configuração permite exibir o menu "Procuração Eletrônica" para os Usuários Externos.') ?> class="infraImg"/>
        <br/>
        <input <?php if($exibirMenuAcessoExternoPF == 'S'){ echo 'checked = checked';} ?> type="radio" name="rdMenuAcessoExternoPF" id="rdExibirMenuAcessoExternoPF" value="S">
        <label for="rdExibirMenuAcessoExternoPF" id="lblMenuAcessoExternoPF" class="infraLabelRadio">Exibir no Acesso Externo</label>
        <br/>
        <input <?php if($exibirMenuAcessoExternoPF == 'N'){ echo 'checked = checked';} ?>  type="radio" name="rdMenuAcessoExternoPF" id="rdNaoExibirMenuAcessoExternoPF" value="N">
        <label name="lblMenuAcessoExternoPF" id="lblPadrao" for="rdNaoExibirMenuAcessoExternoPF" class="infraLabelRadio">Não exibir no Acesso Externo</label>
        <!--  Fim Exibir menu Procuração Eletrônica --> 
    </fieldset>
    <div class="clear">&nbsp;</div>
    <div class="clear">&nbsp;</div>
    <fieldset id="fldPeriodoIndisponibilidade" class="infraFieldset sizeFieldset">
        <legend class="infraLegend">Configurações para Vinculação de Usuário Externo a Pessoa Jurídica
            <img src="<?= PaginaSEI::getInstance()->getDiretorioImagensGlobal() ?>/ajuda.gif" name="ajuda" id="imgAjuda" <?= PaginaSEI::montarTitleTooltip('Preencha estas configurações para permitir o Usuário Externo logado emitir Procurações Eletrônicas ou Procurações Eletrônicas Especiais para representar determinada Pessoa Jurídica.\n\n\n\n\n Defina o  Tipo de Processo centralizador e a Unidade de Abertura para qual o Processo que conterá a Procuração Eletrônica ou a Procuração Eletrônica Especial deva ser enviado.') ?> class="infraImg"/>
        </legend>        <!--  Tipo de Processo  -->
        <label id="lblTipoProcesso" for="txtTipoProcesso" class="infraLabelObrigatorio">Tipo de Processo Associado:</label>
        <input type="text" onchange="removerProcessoAssociado(0);" id="txtTipoProcesso" name="txtTipoProcesso"
               class="infraText" value="<?php echo $nomeTipoProcesso; ?>"
               tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
        <input type="hidden" id="hdnIdTipoProcesso" name="hdnIdTipoProcesso" value="<?php echo $idTipoProcesso ?>"/>
        <img id="imgLupaTipoProcesso" onclick="objLupaTipoProcesso.selecionar(700,500);"
             src="/infra_css/imagens/lupa.gif" alt="Selecionar Tipo de Processo Associado"
             title="Selecionar Tipo de Processo Associado" class="infraImg"/>
        <img id="imgExcluirTipoProcesso" onclick="removerProcessoAssociado(0);objLupaTipoProcesso.remover();"
             src="/infra_css/imagens/remover.gif" alt="Remover Tipo de Processo Associado"
             title="Remover Tipo de Processo Associado" class="infraImg"/>
        <!--  Fim do Tipo de Processo -->
        <div class="clear">&nbsp;</div>

        <!--Especificação do Processo -->
         
        <label id="lblEspecProc" for="txtEspecProc" class="infraLabelObrigatorio">Especificação do Processo: <img src="<?= PaginaSEI::getInstance()->getDiretorioImagensGlobal() ?>/ajuda.gif" name="ajuda" id="imgAjuda" <?= PaginaSEI::montarTitleTooltip('O texto aqui configurado será utilizado na Especificação dos processos abertos, sempre limitado a 100 caracteres no momento da abertura do processo. \n \n No texto podem ser utilizadas as variáveis a seguir: @cnpj@ - CNPJ da Pessoa Jurídica Outorgante @razao_social@ - Razão Social da Pessoa Jurídica Outorgante.') ?> class="infraImg"/></label>
        <input type="text"  id="txtEspecProcPJ" name="txtEspecProcPJ" onkeypress="return infraMascaraTexto(this,event,100);"
               class="infraText" value="<?php echo $especificacaoPJ ?>"
               tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
        
        <!--Especificação do Processo - FIM -->
        <div class="clear">&nbsp;</div>
        <!--  Unidade --> 
        <label id="lblUnidade" for="txtUnidade" class="infraLabelObrigatorio">Unidade para Abertura do Processo:</label>
        <input type="text"  id="txtUnidade" name="txtUnidade" class="infraText"
               value="<?php echo $nomeUnidade; ?>" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
        <input type="hidden" id="hdnIdUnidade" name="hdnIdUnidade" value="<?= $idUnidade ?>"/>
        <img id="imgLupaUnidade" onclick="objLupaUnidade.selecionar(700,500);" src="/infra_css/imagens/lupa.gif"
                 alt="Selecionar  Unidade para Abertura do Processo" title="Selecionar Unidade para Abertura do Processo"
                 class="infraImg"/>
        <img id="imgExcluirUnidade" onclick="objLupaUnidade.remover();"
                 src="/infra_css/imagens/remover.gif" alt="Remover  Unidade para Abertura do Processo"
                 title="Remover  Unidade para Abertura do Processo" class="infraImg"/>
        <!--Fim da Unidade -->  
        <div class="clear">&nbsp;</div>
        
        <!--   Exibir menu Procuração Eletrônica --> 
        <label id="lblMenuAcessoExterno" for="" class="infraLabelObrigatorio">Exibir menu Responsavel Legal de Pessoa Jurídica:</label>
        <img src="<?= PaginaSEI::getInstance()->getDiretorioImagensGlobal() ?>/ajuda.gif" name="ajuda" id="imgAjuda" <?= PaginaSEI::montarTitleTooltip('Esta configuração permite exibir o menu "Responsável Legal de Pessoa Jurídica" para os Usuários Externo. \n \n Além dessa configuração, para exibir o menu ainda é necessário o Mapeamento da Integração com a Receita Federal para consultar os dados do CNPJ. Se integração ainda não foi mapeada, acesse Administração >> Peticionamento Eletrônico >> Integrações >> Novo >> Funcionalidade: Consultar Dados CNPJ Receita Federal. \n \n Ainda, para exibir o menu nesse caso, necessariamente tem que selecionar acima para Exibir o menu de Procuração Eletrônica.   ') ?> class="infraImg"/>
        <br/>
        <input <?php if($exibirMenuAcessoExterno == 'S'){ echo 'checked = checked';}?> type="radio" name="rdMenuAcessoExterno" id="rdExibirMenuAcessoExterno" value="S">
        <label for="rdExibirMenuAcessoExterno" id="lblMenuAcessoExternoPF" class="infraLabelRadio">Exibir no Acesso Externo</label>
        <br/>
        <input <?php if($exibirMenuAcessoExterno == 'N'){ echo 'checked = checked';}?> type="radio" name="rdMenuAcessoExterno" id="rdNaoExibirMenuAcessoExterno" value="N">
        <label name="lblMenuAcessoExternoPF" id="lblPadrao" for="rdNaoExibirMenuAcessoExterno" class="infraLabelRadio">Não exibir no Acesso Externo</label>
        <!--  Fim Exibir menu Procuração Eletrônica --> 

        <div class="clear">&nbsp;</div>
        <fieldset class="infraFieldset" style="width:65%">
            <legend class="infraLegend">Nível de Acesso dos Documentos Peticionados
                <img src="<?= PaginaSEI::getInstance()->getDiretorioImagensGlobal() ?>/ajuda.gif" name="ajuda" id="imgAjuda" <?= PaginaSEI::montarTitleTooltip('Indique o comportamento ser adotado pelo SEI referente ao Nível de acesso dos Atos Constitutivos ao Peticionar a Vinculação do Usuário Externo como Responsável Legal a uma Pessoa Jurídica. \n\n Utilize a opção "Usuário Externo indica diretamente" para permitir ao Usuário Externo selecionar o Nível de Acesso dos Atos Constitutivos.\n\n\n\n\n  Utilize a opção "Padrão pré definido" para que os Atos Constitutivos sejam peticionados com o Nível de Acesso indicado.') ?> class="infraImg"/>
            </legend>
            <div class="bloco"> 
                <input <?php echo $sinNAUsuExt; ?> type="radio" name="rdNivelAcesso[]"
                                                   id="rdUsuExternoIndicarEntrePermitidos"
                                                   onclick="changeNivelAcesso();" value="1">

                <label for="rdUsuExternoIndicarEntrePermitidos" id="lblUsuExterno" class="infraLabelRadio">Usuário Externo indica diretamente</label>
                <br/>
                <input <?php echo $sinNAPadrao; ?> type="radio" name="rdNivelAcesso[]" id="rdPadrao"
                                                   onclick="changeNivelAcesso();" value="2">
                <label name="lblPadrao" id="lblPadrao" for="rdPadrao" class="infraLabelRadio">Padrão pré definido</label>
                            </div>
            <div class="clear">&nbsp;</div>
            <div class=bloco id="divNivelAcesso" <?php echo $sinNAPadrao != '' ? 'style="display: inherit;"' : 'style="display: none;"' ?>>
                    <label name="lblNivelAcesso" id="lblNivelAcesso" for="selNivelAcesso"
                           class="infraLabelObrigatorio">Nível de Acesso:</label>
                    <select id="selNivelAcesso" name="selNivelAcesso" onchange="changeSelectNivelAcesso()">
                        <?= $strItensSelNivelAcesso ?>
                    </select>
            </div>
            <div class="bloco" id="divHipoteseLegal" <?php echo $hipoteseLegal ?> >
                    <label name="lblHipoteseLegal" id="lblHipoteseLegal" for="selHipoteseLegal"
                           class="infraLabelObrigatorio">Hipótese Legal:</label>
                    <select id="selHipoteseLegal" name="selHipoteseLegal">
                        <?= $strItensSelHipoteseLegal ?>
                    </select>
            </div>
        </fieldset>

            <!--  Documento Obrigatório -->
            <?
            //$divDocs = $alterar || $gerado || $externo ? 'style="display: inherit;"' : 'style="display: none;"'
            $divDocs = 'style="display: inherit; margin-left: -5px; margin-top: -3px"';
            ?>
        <fieldset <?php echo $divDocs; ?> id="fldDocObrigatorio" class="fieldsetClear">
            <div>
                <div style="clear:both;">&nbsp;</div>
                <div>
                    <label id="lblDescricaoEssencial" for="selDescricaoEssencial" class="infraLabelObrigatorio">
                        Tipos dos Documentos de Atos Constitutivos Obrigatórios:
                        <img src="<?= PaginaSEI::getInstance()->getDiretorioImagensGlobal() ?>/ajuda.gif" name="ajuda" id="imgAjuda" <?= PaginaSEI::montarTitleTooltip('Defina os Atos Constitutivos que obrigatoriamente o Usuário Externo deverá adicionar ao Peticionar a vinculação dele como Responsável Legal a uma Pessoa Jurídica.') ?> class="infraImg"/>
                    </label>
                </div>
                <div>
                    <input type="text" id="txtSerieEssencial" name="txtSerieEssencial"  class="infraText"
                           tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>

                </div>
                <div style="margin-top: 5px;">
                    <select style="float: left;" id="selDescricaoEssencial" name="selDescricaoEssencial" size="8"
                            multiple="multiple" class="infraSelect">
                        <?= $strItensSelSeriesEss; ?>
                    </select>

                    <img id="imgLupaTipoDocumentoObrigatorio"
                         onclick="objLupaTipoDocumentoEssencial.selecionar(700,500)"
                         src="/infra_css/imagens/lupa.gif"
                         alt="Localizar Tipo de Documento"
                         title="Localizar Tipo de Documento" class="infraImg"/>
                    <br>
                    <img id="imgExcluirTipoDocumentoObrigatorio" onclick="objLupaTipoDocumentoEssencial.remover();"
                         src="/infra_css/imagens/remover.gif"
                         alt="Remover Tipos de Documentos"
                         title="Remover Tipos de Documentos" class="infraImg"/>

                </div>

            </div>
        </fieldset>
                <!--  Fim do Documento Obrigatorio -->

                <!--  Documento Não Obrigatorio  -->
        <fieldset <?php echo $divDocs; ?> id="fldDocComplementar" class="fieldsetClear">
            <div>
                <div style="clear:both;">&nbsp;</div>
                <div>
                    <label id="lblDescricao" for="txtDescricao" class="infraLabelOpcional">
                        Tipos dos Documentos de Atos Constitutivos não Obrigatórios:
                        <img src="<?= PaginaSEI::getInstance()->getDiretorioImagensGlobal() ?>/ajuda.gif" name="ajuda" id="imgAjuda" <?= PaginaSEI::montarTitleTooltip('Defina os Atos Constitutivos que serão listados de forma não obrigatória para o Usuário Externo ao Peticionar a vinculação dele como Responsável Legal a uma Pessoa Jurídica.') ?> class="infraImg"/>
                    </label>
                </div>
                <div>
                    <input type="text" id="txtSerie" name="txtSerie" class="infraText"
                           tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>

                </div>
                <div style="margin-top: 5px;">
                    <select style="float: left;" id="selDescricao" name="selDescricao" size="8" multiple="multiple"
                            class="infraSelect">
                        <?= $strItensSelSeries ?>
                    </select>

                    <img id="imgLupaTipoDocumento" onclick="carregarComponenteLupaTpDocComplementar('S');"
                         src="/infra_css/imagens/lupa.gif"
                         alt="Localizar Tipo de Documento"
                         title="Localizar Tipo de Documento" class="infraImg"/>
                    <br>
                    <img id="imgExcluirTipoDocumentoNaoObrigatorio"
                         onclick="carregarComponenteLupaTpDocComplementar('R');"
                         src="/infra_css/imagens/remover.gif"
                         alt="Remover Tipos de Documentos"
                         title="Remover Tipos de Documentos" class="infraImg"/>

                </div>

            </div>
        </fieldset>
            <!--  Fim do Documento Complementar -->

        <input type="hidden" id="hdnCorpoTabela" name="hdnCorpoTabela" value=""/>
        <input type="hidden" id="hdnUnidadesSelecionadas" name="hdnUnidadesSelecionadas" value=""/>
        <input type="hidden" id="hdnTodasUnidades" name="hdnTodasUnidades" value='<?= json_encode($arrObjUnidadeDTOFormatado); ?>'/>
        <input type="hidden" id="hdnIdTipoDocumento" name="hdnIdTipoDocumento" value=""/>
        <input type="hidden" id="hdnSerie" name="hdnSerie" value="<?= $_POST['hdnSerie'] ?>"/>
        <input type="hidden" id="hdnIdTipoDocumento" name="hdnIdTipoDocumento" value="<?= $_POST['hdnIdTipoDocumento'] ?>"/>
        <input type="hidden" id="hdnIdIndisponibilidadePeticionamento" name="hdnIdIndisponibilidadePeticionamento" value=""/>
        <input type="hidden" id="hdnIdSerie" name="hdnIdSerie" value="<?= $_POST['hdnIdSerie'] ?>"/>
        <input type="hidden" id="hdnIdSerieEssencial" name="hdnIdSerieEssencial" value="<?= $_POST['hdnIdSerieEssencial'] ?>"/>
        <input type="hidden" id="hdnSerieEssencial" name="hdnSerieEssencial" value="<?= $_POST['hdnSerieEssencial'] ?>"/>

        <div class="clear">&nbsp;</div>

        <!-- Orientações -->
        <label id="lblOrientacoes" for="txaConteudo" class="infraLabelOpcional">Orientações:
            <img src="<?= PaginaSEI::getInstance()->getDiretorioImagensGlobal() ?>/ajuda.gif" name="ajuda" id="imgAjuda" <?= PaginaSEI::montarTitleTooltip('Defina as Orientações que devem ser apresentadas para o Usuário Externo na funcionalidade que permite a vinculação dele como Responsável Legal a uma Pessoa Jurídica.') ?> class="infraImg"/>
        </label>
        <?php require_once 'md_pet_vinc_cadastro_orientacao.php'; ?>
        <!--  Fim das Orientações  -->

        <div class="clear">&nbsp;</div>
    </fieldset>
        <?
        PaginaSEI::getInstance()->fecharAreaDados();
        PaginaSEI::getInstance()->montarBarraComandosInferior($arrComandos);
        ?>

</form>
<?

PaginaSEI::getInstance()->fecharBody();
PaginaSEI::getInstance()->fecharHtml();
?>

<script type="text/javascript">
    //Processo
    var objLupaTipoProcesso = null;
    var objAutoCompletarTipoProcesso = null;
    
    //Processo PF
    var objLupaTipoProcessoPF = null;
    var objAutoCompletarTipoProcessoPF = null;

    //Docs
    var objLupaTipoDocumento = null;
    var objAutoCompletarTipoDocumento = null;

    var objLupaTipoDocPrinc = null;
    var objAutoCompletarTipoDocPrinc = null;

    var objLupaTipoDocumentoEssencial = null;
    var objAutoCompletarTipoDocumentoEssencial = null;

    //Unidades
    var objLupaUnidade = null;
    var objAutoCompletarUnidade = null;

    var objLupaUnidadeMultipla = null;
    var objAutoCompletarUnidadeMutipla = null;
    
    //Unidades PF
    var objLupaUnidadePF = null;
    var objAutoCompletarUnidadePF = null;


    function removerUnidade(idObj) {

        document.getElementById(idObj).remove();
        qtdLinhas = document.getElementsByClassName('linhas').length;
        document.getElementById('qtdRegistros').innerHTML = qtdLinhas;

        if (qtdLinhas == 0) {
            document.getElementById('divTableMultiplasUnidades').style.display = "none";
        }

    }

    function registroDuplicado(uf) {
        var todasUfs = document.getElementsByClassName('ufsSelecionadas');
        var ufAdd = (uf.trim()).toUpperCase();

        if (todasUfs.length > 0) {
            for (i = 0; i < todasUfs.length; i++) {
                var ufGrid = ((todasUfs[i].innerHTML).trim()).toUpperCase();
                if (ufGrid == ufAdd) {
                    alert('Não é permitido adicionar mais de uma Unidade de abertura para a mesma UF.');
                    return true;
                }
            }
        }

        return false;
    }


    function removerProcessoAssociado(remover) {

        document.getElementById('selNivelAcesso').innerHTML = '';
        document.getElementById('divHipoteseLegal').style.display = "none";
        console.log(remover);
        if (remover === '1') {
            objLupaTipoProcesso.remover();
        }
    }
    
    function removerProcessoAssociadoPF(remover) {

        document.getElementById('selNivelAcesso').innerHTML = '';
        document.getElementById('divHipoteseLegal').style.display = "none";
        console.log(remover);
        if (remover === '1') {
            objLupaTipoProcessoPF.remover();
        }
    }

    function changeNivelAcesso() {

        document.getElementById('divNivelAcesso').style.display = "none";
        var padrao = document.getElementsByName('rdNivelAcesso[]')[1].checked;

        document.getElementById('selNivelAcesso').value = '';
        document.getElementById('selHipoteseLegal').value = '';
        //document.getElementById('divHipoteseLegal').style.display = 'none';

        if (padrao) {
            document.getElementById('divNivelAcesso').style.display = "inherit";
        }
        else{
            document.getElementById('divHipoteseLegal').style.display = 'none';
        }

    }

    function changeSelectNivelAcesso() {
        document.getElementById('selHipoteseLegal').value = '';

        var valorSelectNivelAcesso = document.getElementById('selNivelAcesso').value;
        var valorHipoteseLegal = document.getElementById('hdnParametroHipoteseLegal').value;

        if (valorSelectNivelAcesso == '<?= ProtocoloRN::$NA_RESTRITO ?>' && valorHipoteseLegal != '0') {
            document.getElementById('divHipoteseLegal').style.display = 'inherit';

        } else {
            document.getElementById('divHipoteseLegal').style.display = 'none';

        }
    }


    function changeDocPrincipal() {

        var gerado = document.getElementsByName('rdDocPrincipal[]')[0].checked;
        var tipo = '';

        document.getElementById('divDocPrincipal').style.display = "inherit";
        //document.getElementById('fldDocObrigatorio').style.display = "inherit";
        document.getElementById('fldDocComplementar').style.display = "inherit";

        if (objLupaTipoDocPrinc != null) {
            objLupaTipoDocPrinc.remover();
        }

        if (gerado) {
            tipo = 'G';
            document.getElementById('txtTipoDocPrinc').value = '';
            document.getElementsByName("rdDocPrincipal[]")[0].focus();
        } else {
            tipo = 'E';
            document.getElementById('txtTipoDocPrinc').value = '';
            document.getElementsByName("rdDocPrincipal[]")[1].focus();
        }

        carregarComponenteAutoCompleteTpDocPrinc(tipo);


        //rdDocPrincipal
    }

    function changeDocPrincipalEdicao() {

        var gerado = document.getElementsByName('rdDocPrincipal[]')[0].checked;
        var tipo = '';

        document.getElementById('divDocPrincipal').style.display = "inherit";

        if (gerado) {
            tipo = 'G';
            document.getElementsByName("rdDocPrincipal[]")[0].focus();
        } else {
            tipo = 'E';
            document.getElementsByName("rdDocPrincipal[]")[1].focus();
        }

        carregarComponenteAutoCompleteTpDocPrinc(tipo);

    }


    function inicializar() {

        carregarComponenteTipoDocumento(); //Doc Complementares - Seleção Múltipla
        carregarComponenteTipoProcesso(); // Seleção Única
        carregarComponenteTipoProcessoPF();
        carregarComponenteUnidade();  // Seleção Única
        carregarComponenteUnidadePF();
        carregarComponenteTipoDocumentoEssencial(); // Seleção Múltipla
        carregarDependenciaNivelAcesso();
        carregarHipoteseLegal();
        infraEfeitoTabelas();


    }

    function carregarHipoteseLegal() {
        var parametroHipoteseLegal = document.getElementById('hdnParametroHipoteseLegal').value;
        if (parametroHipoteseLegal == '' || parametroHipoteseLegal == 0) {
            document.getElementById('divHipoteseLegal').style.display = 'none';
        }
    }

    function carregarDependenciaNivelAcesso() {
        //Ajax para carregar os niveis de acesso após a escolha do tipo de processo
        objAjaxIdNivelAcesso = new infraAjaxMontarSelectDependente('txtTipoProcesso', 'selNivelAcesso', '<?=$strLinkAjaxNivelAcesso?>');
        objAjaxIdNivelAcesso.prepararExecucao = function () {
            document.getElementById('selNivelAcesso').innerHTML = '';
            return infraAjaxMontarPostPadraoSelect('null', '', 'null') + '&idTipoProcesso=' + document.getElementById('hdnIdTipoProcesso').value;
        }
    }


    function carregarComponenteUnidade() {
        objLupaUnidade = new infraLupaText('txtUnidade', 'hdnIdUnidade', '<?=$strLinkUnidadeSelecao?>');

        objLupaUnidade.finalizarSelecao = function () {
            objAutoCompletarUnidade.selecionar(document.getElementById('hdnIdUnidade').value, document.getElementById('txtUnidade').value);
        };


        objAutoCompletarUnidade = new infraAjaxAutoCompletar('hdnIdUnidade', 'txtUnidade', '<?=$strLinkAjaxUnidade?>');
        objAutoCompletarUnidade.limparCampo = false;
        objAutoCompletarUnidade.tamanhoMinimo = 3;
        objAutoCompletarUnidade.prepararExecucao = function () {
            return 'palavras_pesquisa=' + document.getElementById('txtUnidade').value;
        };

        objAutoCompletarUnidade.processarResultado = function (id, descricao, complemento) {
            if (id != '') {
                document.getElementById('hdnIdUnidade').value = id;
                document.getElementById('txtUnidade').value = descricao;
            }
        }
        objAutoCompletarUnidade.selecionar('<?=$strIdUnidade?>', '<?=PaginaSEI::getInstance()->formatarParametrosJavascript($strNomeRemetente);?>');
    }
    
    function carregarComponenteUnidadePF() {
        objLupaUnidadePF = new infraLupaText('txtUnidadePF', 'hdnIdUnidadePF', '<?=$strLinkUnidadePFSelecao?>');

        objLupaUnidadePF.finalizarSelecao = function () {
            objAutoCompletarUnidadePF.selecionar(document.getElementById('hdnIdUnidadePF').value, document.getElementById('txtUnidadePF').value);
        };


        objAutoCompletarUnidadePF = new infraAjaxAutoCompletar('hdnIdUnidadePF', 'txtUnidadePF', '<?=$strLinkAjaxUnidade?>');
        objAutoCompletarUnidadePF.limparCampo = false;
        objAutoCompletarUnidadePF.tamanhoMinimo = 3;
        objAutoCompletarUnidadePF.prepararExecucao = function () {
            return 'palavras_pesquisa=' + document.getElementById('txtUnidadePF').value;
        };

        objAutoCompletarUnidadePF.processarResultado = function (id, descricao, complemento) {
            if (id != '') {
                document.getElementById('hdnIdUnidadePF').value = id;
                document.getElementById('txtUnidadePF').value = descricao;
            }
        }
        objAutoCompletarUnidadePF.selecionar('<?=$strIdUnidade?>', '<?=PaginaSEI::getInstance()->formatarParametrosJavascript($strNomeRemetente);?>');
    }

    function carregarComponenteLupaTpDocPrinc(acaoComponente) {

        var gerado = document.getElementsByName('rdDocPrincipal[]')[0].checked;
        var tipo = gerado ? 'G' : 'E';
        var link = '<?= $strLinkTipoDocPrincExternoSelecao ?>';

        if (gerado) {
            link = '<?= $strLinkTipoDocPrincGeradoSelecao ?>';
        }

        objLupaTipoDocPrinc = new infraLupaText('txtTipoDocPrinc', 'hdnIdTipoDocPrinc', link);

        objLupaTipoDocPrinc.finalizarSelecao = function () {
            objAutoCompletarTipoDocPrinc.selecionar(document.getElementById('hdnIdTipoDocPrinc').value, document.getElementById('txtTipoDocPrinc').value);
        }

        acaoComponente == 'S' ? objLupaTipoDocPrinc.selecionar(700, 500) : objLupaTipoDocPrinc.remover();
    }

    function carregarComponenteLupaTpDocComplementar(acaoComponente) {
        acaoComponente == 'S' ? objLupaTipoDocumento.selecionar(700, 500) : objLupaTipoDocumento.remover();
    }

    function returnLinkModificado(link, tipo) {
        var arrayLink = link.split('&filtro=1');

        var linkFim = '';
        if (arrayLink.length == 2) {
            linkFim = arrayLink[0] + '&filtro=1&tipoDoc=' + tipo + arrayLink[1];
        } else {
            linkFim = link;
        }

        return linkFim;
    }


    function carregarComponenteAutoCompleteTpDocPrinc(tipo) {

        objAutoCompletarTipoDocPrinc = new infraAjaxAutoCompletar('hdnIdTipoDocPrinc', 'txtTipoDocPrinc', '<?=$strLinkAjaxTipoDocPrinc?>');
        objAutoCompletarTipoDocPrinc.limparCampo = true;

        objAutoCompletarTipoDocPrinc.prepararExecucao = function () {
            return 'palavras_pesquisa=' + document.getElementById('txtTipoDocPrinc').value + '&tipoDoc=' + tipo;
        };

        objAutoCompletarTipoDocPrinc.processarResultado = function (id, descricao, complemento) {
            if (id != '') {
                document.getElementById('hdnIdTipoDocPrinc').value = id;
                document.getElementById('txtTipoDocPrinc').value = descricao;
            }
        }
        objAutoCompletarTipoDocPrinc.selecionar('<?=$strIdTipoDocPrinc?>', '<?=PaginaSEI::getInstance()->formatarParametrosJavascript($strNomeRemetente);?>');
    }


    function carregarComponenteTipoProcesso() {

        objLupaTipoProcesso = new infraLupaText('txtTipoProcesso', 'hdnIdTipoProcesso', '<?=$strLinkTipoProcessoSelecao?>');

        objLupaTipoProcesso.finalizarSelecao = function () {
            objAutoCompletarTipoProcesso.selecionar(document.getElementById('hdnIdTipoProcesso').value, document.getElementById('txtTipoProcesso').value);
            objAjaxIdNivelAcesso.executar();
        }
        objAutoCompletarTipoProcesso = new infraAjaxAutoCompletar('hdnIdTipoProcesso', 'txtTipoProcesso', '<?=$strLinkAjaxTipoProcesso?>');
        objAutoCompletarTipoProcesso.tamanhoMinimo = 3;
        objAutoCompletarTipoProcesso.limparCampo = false;

        objAutoCompletarTipoProcesso.prepararExecucao = function () {
            return 'palavras_pesquisa=' + document.getElementById('txtTipoProcesso').value;
        };

        objAutoCompletarTipoProcesso.processarResultado = function (id, descricao, complemento) {
            if (id != '') {
                document.getElementById('hdnIdTipoProcesso').value = id;
                document.getElementById('txtTipoProcesso').value = descricao;
                objAjaxIdNivelAcesso.executar();
            }
        };

        objAutoCompletarTipoProcesso.selecionar('<?=$strIdTipoProcesso?>', '<?=PaginaSEI::getInstance()->formatarParametrosJavascript($strNomeRemetente);?>');

    }
    
    function carregarComponenteTipoProcessoPF() {

        objLupaTipoProcessoPF = new infraLupaText('txtTipoProcessoPF', 'hdnIdTipoProcessoPF', '<?=$strLinkTipoProcessoPFSelecao?>');

        objLupaTipoProcessoPF.finalizarSelecao = function () {
            objAutoCompletarTipoProcessoPF.selecionar(document.getElementById('hdnIdTipoProcessoPF').value, document.getElementById('txtTipoProcessoPF').value);
            objAjaxIdNivelAcesso.executar();
        }
        objAutoCompletarTipoProcessoPF = new infraAjaxAutoCompletar('hdnIdTipoProcessoPF', 'txtTipoProcessoPF', '<?=$strLinkAjaxTipoProcesso?>');
        objAutoCompletarTipoProcessoPF.tamanhoMinimo = 3;
        objAutoCompletarTipoProcessoPF.limparCampo = false;

        objAutoCompletarTipoProcessoPF.prepararExecucao = function () {
            return 'palavras_pesquisa=' + document.getElementById('txtTipoProcessoPF').value;
        };

        objAutoCompletarTipoProcessoPF.processarResultado = function (id, descricao, complemento) {
            if (id != '') {
                document.getElementById('hdnIdTipoProcessoPF').value = id;
                document.getElementById('txtTipoProcessoPF').value = descricao;
                objAjaxIdNivelAcesso.executar();
            }
        };

        objAutoCompletarTipoProcessoPF.selecionar('<?=$strIdTipoProcesso?>', '<?=PaginaSEI::getInstance()->formatarParametrosJavascript($strNomeRemetente);?>');

    }

    //Carrega o documento para o documento complementar
    function carregarComponenteTipoDocumento() {

        objAutoCompletarTipoDocumento = new infraAjaxAutoCompletar('hdnIdSerie', 'txtSerie', '<?=$strLinkAjaxTipoDocumento?>');
        objAutoCompletarTipoDocumento.limparCampo = true;
        objAutoCompletarTipoDocumento.tamanhoMinimo = 3;
        objAutoCompletarTipoDocumento.prepararExecucao = function () {
            //var tipo   = gerado ? 'G' : 'E';
            //20160908 - Essencial e Complementar SEMPRE EXTERNO
            var tipo = 'E';
            return 'palavras_pesquisa=' + document.getElementById('txtSerie').value + '&tipoDoc=' + tipo;
        };

        objAutoCompletarTipoDocumento.processarResultado = function (id, nome, complemento) {

            if (id != '') {
                var options = document.getElementById('selDescricao').options;

                if (options != null) {
                    for (var i = 0; i < options.length; i++) {
                        if (options[i].value == id) {
                            alert('Tipo de Documento já consta na lista.');
                            break;
                        }
                    }
                }

                if (i == options.length) {

                    for (i = 0; i < options.length; i++) {
                        options[i].selected = false;
                    }

                    opt = infraSelectAdicionarOption(document.getElementById('selDescricao'), nome, id);

                    objLupaTipoDocumento.atualizar();

                    opt.selected = true;
                }

                document.getElementById('txtSerie').value = '';
                document.getElementById('txtSerie').focus();

            }
        };

        objLupaTipoDocumento = new infraLupaSelect('selDescricao', 'hdnSerie', '<?=$strLinkTipoDocumentoSelecao?>');
    }

    //Carrega o documento para o documento essencial
    function carregarComponenteTipoDocumentoEssencial() {

        objAutoCompletarTipoDocumentoEssencial = new infraAjaxAutoCompletar('hdnIdSerieEssencial', 'txtSerieEssencial', '<?=$strLinkAjaxTipoDocumento?>');
        objAutoCompletarTipoDocumentoEssencial.limparCampo = true;
        objAutoCompletarTipoDocumentoEssencial.tamanhoMinimo = 3;
        objAutoCompletarTipoDocumentoEssencial.prepararExecucao = function () {
            var tipo = 'E';
            return 'palavras_pesquisa=' + document.getElementById('txtSerieEssencial').value + '&tipoDoc=' + tipo;
        };

        objAutoCompletarTipoDocumentoEssencial.processarResultado = function (id, nome, complemento) {

            if (id != '') {
                var options = document.getElementById('selDescricaoEssencial').options;

                if (options != null) {
                    for (var i = 0; i < options.length; i++) {
                        if (options[i].value == id) {
                            alert('Tipo de Documento já consta na lista.');
                            break;
                        }
                    }
                }

                if (i == options.length) {

                    for (i = 0; i < options.length; i++) {
                        options[i].selected = false;
                    }

                    opt = infraSelectAdicionarOption(document.getElementById('selDescricaoEssencial'), nome, id);

                    objLupaTipoDocumentoEssencial.atualizar();

                    opt.selected = true;
                }

                document.getElementById('txtSerieEssencial').value = '';
                document.getElementById('txtSerieEssencial').focus();

            }
        };

        objLupaTipoDocumentoEssencial = new infraLupaSelect('selDescricaoEssencial', 'hdnSerieEssencial', '<?=$strLinkTipoDocumentoEssencialSelecao?>');
    }


    function validarCadastro() {

        var valorHipoteseLegal = document.getElementById('hdnParametroHipoteseLegal').value;

        //Pessoa Física
        var exibirMenuAcessoExternoPF = document.getElementById('rdExibirMenuAcessoExternoPF').checked;
        var naoExibirMenuAcessoExternoPF = document.getElementById('rdNaoExibirMenuAcessoExternoPF').checked;
        
        //Pessoa Jurídica
        var exibirMenuAcessoExterno = document.getElementById('rdExibirMenuAcessoExterno').checked;
        var naoExibirMenuAcessoExterno = document.getElementById('rdNaoExibirMenuAcessoExterno').checked;
       
        if (exibirMenuAcessoExternoPF == false && naoExibirMenuAcessoExternoPF == false) {
            alert('Informe sobre o menu Procuração Eletrônica da Pessoa Física.');
            document.getElementById('rdExibirMenuAcessoExternoPF').focus();
            return false;
        }
        
        if (exibirMenuAcessoExterno == false && naoExibirMenuAcessoExterno == false) {
            alert('Informe sobre o menu Procuração Eletrônica.');
            document.getElementById('rdNaoExibirMenuAcessoExterno').focus();
            return false;
        }
        
        //tratamento dos campos obrigatórios pessoa física
       /* if(exibirMenuAcessoExternoPF == true){
            if (infraTrim(document.getElementById('txtTipoProcessoPF').value) == '') {
                alert('Informe o Tipo de Processo para abertura do processo para Pessoa Física.');
                document.getElementById('txtTipoProcessoPF').focus();
                return false;
            }
            if(document.getElementById('txtEspecProcPF').value == ""){
                alert('Informe a Especificação do Processo.');
                document.getElementById('txtTipoProcessoPF').focus();
                return false;
            }
            vlUnidadePF = infraTrim(document.getElementById('hdnIdUnidadePF').value);
            if (vlUnidadePF == '' || vlUnidadePF == null) {
                alert('Informe a Unidade para abertura do processo para Pessoa Física.');
                document.getElementById('txtUnidadePF').focus();
                return false;
            }
        }*/
        
        
        //tratamento dos campos obrigatórios pessoa jurídic
        //Validação para verificação no webservice
       
        
           //Validação Pessoa Física
           //var aviso = "Não foi possível habilitar a exibição do menu Responsável Legal no Acesso Externo. \n Para exibir o menu Responsável Legal de Pessoa Jurídica no Acesso Externo é necessário preencher os campos obrigatórios contidos em Configurações para Vinculação de Usuário Externo a Pessoa Jurídica e Configurações para Vinculação de Usuário Externo a Pessoa Física. Ainda, para exibir o menu nesse caso, necessariamente tem que selecionar acima para Exibir o menu de Procuração Eletrônica. ";
                       
            if (infraTrim(document.getElementById('txtTipoProcessoPF').value) == '') {
                alert('Informe o Tipo de Processo para abertura do processo para Pessoa Física.');
                document.getElementById('txtTipoProcessoPF').focus();
                return false;
            }
            if(document.getElementById('txtEspecProcPF').value == ""){
                alert('Informe a Especificação do processo para Pessoa Física.');
                document.getElementById('txtTipoProcessoPF').focus();
                return false;
            }
            vlUnidadePF = infraTrim(document.getElementById('hdnIdUnidadePF').value);
            if (vlUnidadePF == '' || vlUnidadePF == null) {
                alert('Informe a Unidade para abertura do processo para Pessoa Física.');
                document.getElementById('txtUnidadePF').focus();
                return false;
            }

            if (infraTrim(document.getElementById('txtTipoProcesso').value) == '') {
                alert('Informe o Tipo de Processo para abertura do processo para Pessoa Jurídica.');
                document.getElementById('txtTipoProcesso').focus();
                return false;
            }
            if(document.getElementById('txtEspecProcPJ').value == ""){
                alert('Informe a Especificação do processo para Pessoa Jurídica.');
                document.getElementById('txtTipoProcessoPF').focus();
                return false;
            }
            vlUnidade = infraTrim(document.getElementById('hdnIdUnidade').value);
            if (vlUnidade == '' || vlUnidade == null) {
                alert('Informe a Unidade para abertura do processo para Pessoa Jurídica.');
                document.getElementById('txtUnidade').focus();
                return false;
            }

            //Validar Nível Acesso
            var elemsNA = document.getElementsByName("rdNivelAcesso[]");

            validoNA = false;
            for (var i = 0; i < elemsNA.length; i++) {
                if (elemsNA[i].checked === true) {
                    validoNA = true;
                }
            }

            if (((infraTrim(document.getElementById('selNivelAcesso').value) == '') && document.getElementById('rdPadrao').checked) || (!validoNA)) {
                alert('Informe o Nível de Acesso para abertura do processo para Pessoa Jurídica.');
                document.getElementById('rdUsuExternoIndicarEntrePermitidos').focus();
                return false;
            }else {
                if (document.getElementById('selNivelAcesso').value == <?= ProtocoloRN::$NA_RESTRITO ?> && valorHipoteseLegal != '0') {

                    //validar hipotese legal
                    if (document.getElementById('selHipoteseLegal').value == '') {
                        alert('Informe a Hipótese legal padrão para abertura do processo para Pessoa Jurídica.');
                        document.getElementById('selHipoteseLegal').focus();
                        return false;
                    }

                }
            }

            vlDocObrigatorio = document.getElementById('selDescricaoEssencial').options.length;
            if (vlDocObrigatorio == 0  ) {
                alert('Informe os Tipos dos Documentos de Atos Constitutivos Obrigatórios para abertura do processo para Pessoa Jurídica.');
                document.getElementById('selDescricaoEssencial').focus();
                return false;
            }
            
        if(exibirMenuAcessoExterno == true){            
            $.ajax({
            url: '<?=$strLinkAjaxWebServiceSalvar?>',
                    type: 'POST',
                    dataType: 'XML',
                    async: false,
                    success: function (result) {
                         
                        if($(result).find('valor').text() == 'N'){
                            alert('Não foi possível habilitar a exibição do menu Responsável Legal de Pessoa Jurídica no Acesso Externo.\n\n Acesse Administração >> Peticionamento Eletrônico >> Integrações >> Novo >> Funcionalidade: Consultar Dados CNPJ Receita Federal e preencha o Mapeamento da Integração com a Receita Federal para consultar os dados do CNPJ.');
                        }
                    },
                    error: function (e) {
                        console.error('Erro ao processar o XML do SEI: ' + e.responseText);
                    }
            }); 
        }  
       
        return true;
    }

    function OnSubmitForm() {
        return validarCadastro();
    }

    function getPercentTopStyle(element) {
        var parent = element.parentNode,
            computedStyle = getComputedStyle(element),
            value;
        parent.style.display = 'none';
        value = computedStyle.getPropertyValue('top');
        parent.style.removeProperty('display');

        if (value != '') {
            valor = value.replace('%', '');
            return parseInt(valor);
        }

        return false;
    }

</script>