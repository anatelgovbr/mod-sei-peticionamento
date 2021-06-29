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

    //Recupera o valor do Parâmetro SEI_HABILITAR_HIPOTESE_LEGAL
    $objInfraParametro = new InfraParametro(BancoSEI::getInstance());
    $strValor = $objInfraParametro->getValor('SEI_HABILITAR_HIPOTESE_LEGAL');

    //Preparar Preenchimento Alteração
    if (count($objMdPetVincTpProcessoDTO) > 0 && !isset($_POST['sbmCadastrarTpProcessoVinculacao'])) {

        foreach ($objMdPetVincTpProcessoDTO as $obj) {
            if ($obj->getStrTipoVinculo() == 'F') {
                $idTipoProcessoPF = $obj->getNumIdTipoProcedimento();
                $idUnidadePF = $obj->getNumIdUnidade();
                $nomeTipoProcessoPF = $obj->getStrNomeProcedimento();
                $especificacaoPF = $obj->getStrEspecificacao();
                if ($obj->getStrSiglaUnidade()) {
                    $nomeUnidadePF = $obj->getStrSiglaUnidade() . ' - ' . $obj->getStrDescricaoUnidade();
                }
                $exibirMenuAcessoExternoPF = $obj->getStrSinAtivo();
            } else {
                $idTipoProcesso = $obj->getNumIdTipoProcedimento();
                $orientacoes = $obj->getStrOrientacoes();
                $idUnidade = $obj->getNumIdUnidade();
                $sinNAUsuExt = $obj->getStrSinNaUsuarioExterno() == 'S' ? 'checked = checked' : '';
                $sinNAPadrao = $obj->getStrSinNaPadrao() == 'S' ? 'checked = checked' : '';
                $idhipoteseLegal = $obj->getNumIdHipoteseLegal();
                $nomeTipoProcesso = $obj->getStrNomeProcedimento();
                $especificacaoPJ = $obj->getStrEspecificacao();
                if ($obj->getStrSiglaUnidade()) {
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
            if ($_POST['rdMenuAcessoExternoPF']) {
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

                if ($_POST['selNivelAcesso'] == ProtocoloRN::$NA_RESTRITO && $valorParametroHipoteseLegal != '0' && $strValor > 0) {
                    $objMdPetVincTpProcessoDTO->setNumIdHipoteseLegal($idhipoteseLegal);
                    $hipoteseLegal = 'style="display: inherit;"';
                } else {
                    $objMdPetVincTpProcessoDTO->setNumIdHipoteseLegal(null);
                }
            }

            if (isset($_POST['sbmCadastrarTpProcessoVinculacao'])) {
                try {

                    //Pessoa jurídica
                    if ($_POST['rdMenuAcessoExterno']) {

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
                        if (!$objRelTipoProcedimentoAssuntoDTO) {
                            $msg = "Por favor informe um tipo de processo que na parametrização do SEI tenha indicação de pelo menos uma sugestão de assunto.";
                            PaginaSEI::getInstance()->setStrMensagem($msg, InfraPagina::$TIPO_MSG_AVISO);
                            header('Location: ' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . $_GET['acao_origem'] . '&acao_origem=' . $_GET['acao']));
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
                    if ($_POST['rdMenuAcessoExternoPF']) {
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
                        if (!$objRelTipoProcedimentoAssuntoDTO) {
                            $msg = "Por favor informe um tipo de processo que na parametrização do SEI tenha indicação de pelo menos uma sugestão de assunto.";
                            PaginaSEI::getInstance()->setStrMensagem($msg, InfraPagina::$TIPO_MSG_AVISO);
                            header('Location: ' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . $_GET['acao_origem'] . '&acao_origem=' . $_GET['acao']));
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
                    $arrObjMdPetVincRelSerieDTO = array();
                    //Tipo de Documento Essencial
                    foreach ($arrIdTipoDocumentoEssencial as $numIdTipoDocumentoEss) {
                        $objMdPetVincRelSerieDTO = new MdPetVincRelSerieDTO();

                        $objMdPetVincRelSerieDTO->setNumIdMdPetVincTpProcesso(MdPetVincTpProcessoRN::$ID_FIXO_MD_PET_VINCULO_USU_EXT);
                        $objMdPetVincRelSerieDTO->setNumIdSerie($numIdTipoDocumentoEss);
                        $objMdPetVincRelSerieDTO->setStrSinObrigatorio('S');
                        array_push($arrObjMdPetVincRelSerieDTO, $objMdPetVincRelSerieDTO);
                    }

                    //Tipo de Documento Complementar
                    foreach ($arrIdTipoDocumento as $numIdTipoDocumento) {
                        $objMdPetVincRelSerieDTO = new MdPetVincRelSerieDTO();

                        $objMdPetVincRelSerieDTO->setNumIdMdPetVincTpProcesso(MdPetVincTpProcessoRN::$ID_FIXO_MD_PET_VINCULO_USU_EXT);
                        $objMdPetVincRelSerieDTO->setNumIdSerie($numIdTipoDocumento);
                        $objMdPetVincRelSerieDTO->setStrSinObrigatorio('N');
                        array_push($arrObjMdPetVincRelSerieDTO, $objMdPetVincRelSerieDTO);
                    }
                    $objMdPetVincRelSerieDTO = $objMdPetVincRelSerieRN->cadastrar($arrObjMdPetVincRelSerieDTO);

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
require_once "md_pet_vinc_tp_processo_cadastro_css.php";


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

    <div class="row">
        <div class="col-sm-12 col-md-10 col-lg-10 col-xl-10">
            <fieldset id="fldPeriodoIndisponibilidade" class="infraFieldset sizeFieldset form-control" style="height: auto">
                <legend class="infraLegend">Configurações para Vinculação de Usuário Externo a Pessoa Física
                    <img src="<?= PaginaSEI::getInstance()->getDiretorioImagensGlobal() ?>/ajuda.gif" name="ajuda"
                         id="imgAjuda" <?= PaginaSEI::montarTitleTooltip('Preencha estas configurações para permitir o Usuário Externo logado emitir Procurações Eletrônicas simples para que outro Usuário Externo possa representá-lo como Pessoa Física. \n \n Defina o Tipo de Processo e a Unidade onde cada Processo de controle de representação por Usuário Externo será aberto.', 'Ajuda') ?>
                         class="infraImgFielset"/>
                </legend>
                <!--  Tipo de Processo  -->
                <div class="row">
                    <div class="col-sm-12 col-md-5 col-lg-5 col-xl-6">
                        <label id="lblTipoProcesso" for="txtTipoProcessoPF" class="infraLabelObrigatorio">Tipo de
                            Processo
                            Associado:</label>
                        <div class="input-group mb-3">
                            <input type="text" onchange="removerProcessoAssociadoPF(0);" id="txtTipoProcessoPF"
                                   name="txtTipoProcessoPF"
                                   class="infraText form-control" value="<?php echo $nomeTipoProcessoPF; ?>"
                                   tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
                            <input type="hidden" id="hdnIdTipoProcessoPF" name="hdnIdTipoProcessoPF"
                                   value="<?php echo $idTipoProcessoPF ?>"/>
                            <img id="imgLupaTipoProcesso" onclick="objLupaTipoProcessoPF.selecionar(700,500);"
                                 onkeypress="objLupaTipoProcessoPF.selecionar(700,500);"
                                 src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/pesquisar.svg"
                                 alt="Selecionar Tipo de Processo" title="Selecionar Tipo de Processo" class="infraImg"
                                 tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
                            <img id="imgExcluirTipoProcesso"
                                 onclick="removerProcessoAssociadoPF(0);objLupaTipoProcessoPF.remover();"
                                 onkeypress="removerProcessoAssociadoPF(0);objLupaTipoProcessoPF.remover();"
                                 src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/remover.svg"
                                 alt="Remover Tipo de Processo" title="Remover Tipo de Processo" class="infraImg"
                                 tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
                        </div>
                    </div>
                </div>
                <!--  Fim do Tipo de Processo -->


                <!--Especificação do Processo -->
                <div class="row">
                    <div class="col-sm-12 col-md-5 col-lg-5 col-xl-6">
                        <label id="lblEspecProc" for="txtEspecProc" class="infraLabelObrigatorio">Especificação do
                            Processo:
                            <img
                                    src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/ajuda.svg"
                                    name="ajuda"
                                    id="imgAjuda" <?= PaginaSEI::montarTitleTooltip('O texto aqui configurado será utilizado na Especificação dos processos abertos, sempre limitado a 100 caracteres no momento da abertura do processo. \n \n No texto podem ser utilizadas as variáveis a seguir: @cpf@ - CPF da Pessoa Física Outorgante @nome_completo@ - Nome Completo da Pessoa Física Outorgante. ', 'Ajuda') ?>
                                    class="infraImgModulo"/></label>
                        <input type="text" id="txtEspecProcPF" name="txtEspecProcPF"
                               onkeypress="return infraMascaraTexto(this,event,100);" class="infraText form-control"
                               value="<?php echo $especificacaoPF ?>"
                               tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
                    </div>
                </div>
                <!--Especificação do Processo - FIM -->

                <!--  Unidade -->
                <div class="row">
                    <div class="col-sm-12 col-md-5 col-lg-5 col-xl-6">
                        <label id="lblUnidade" for="txtUnidade" class="infraLabelObrigatorio">Unidade para Abertura do
                            Processo:</label>
                        <div class="input-group mb-3">
                            <input type="text" id="txtUnidadePF" name="txtUnidadePF" class="infraText form-control"
                                   value="<?php echo $nomeUnidadePF; ?>"
                                   tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
                            <input type="hidden" id="hdnIdUnidadePF" name="hdnIdUnidadePF" value="<?= $idUnidadePF ?>"/>
                            <img id="imgLupaUnidade" onclick="objLupaUnidadePF.selecionar(700,500);"
                                 onkeypress="objLupaUnidadePF.selecionar(700,500);"
                                 src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/pesquisar.svg"
                                 alt="Selecionar Unidade"
                                 title="Selecionar Unidade" class="infraImg"
                                 tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
                            <img id="imgExcluirUnidade" onclick="objLupaUnidadePF.remover();"
                                 onkeypress="objLupaUnidadePF.remover();"
                                 src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/remover.svg"
                                 alt="Remover Unidade"
                                 title="Remover Unidade" class="infraImg"
                                 tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
                        </div>
                    </div>
                </div>
                <!--Fim da Unidade -->

                <!--   Exibir menu Procuração Eletrônica -->
                <div class="row">
                    <div class="col-sm-12 col-md-5 col-lg-5 col-xl-6">
                        <label id="lblMenuAcessoExternoPF" for="" class="infraLabelObrigatorio">Exibir menu Procuração
                            Eletrônica: <img
                                    align="top"
                                    src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/ajuda.svg"
                                    name="ajuda" <?= PaginaSEI::montarTitleTooltip('Esta configuração permite exibir o menu "Procurações Eletrônicas" para os Usuários Externos.', 'Ajuda') ?>
                                    class="infraImgModulo"/></label>
                        <br/>
                        <input <?php if ($exibirMenuAcessoExternoPF == 'S') {
                            echo 'checked = checked';
                        } ?> type="radio" name="rdMenuAcessoExternoPF" id="rdExibirMenuAcessoExternoPF" value="S"
                             class="infraRadio"
                             tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>">
                        <label for="rdExibirMenuAcessoExternoPF" id="lblMenuAcessoExternoPF" class="infraLabelRadio">Exibir
                            no
                            Acesso
                            Externo</label>
                        <br/>
                        <input <?php if ($exibirMenuAcessoExternoPF == 'N') {
                            echo 'checked = checked';
                        } ?> type="radio" name="rdMenuAcessoExternoPF" id="rdNaoExibirMenuAcessoExternoPF" value="N"
                             class="infraRadio"
                             tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>">
                        <label name="lblMenuAcessoExternoPF" id="lblPadrao" for="rdNaoExibirMenuAcessoExternoPF"
                               class="infraLabelRadio">Não exibir no Acesso Externo</label>
                    </div>
                </div>
                <!--  Fim Exibir menu Procuração Eletrônica -->
            </fieldset>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12 col-md-10 col-lg-10 col-xl-10">
            <fieldset id="fldPeriodoIndisponibilidade" class="infraFieldset sizeFieldset2 form-control">
                <legend class="infraLegend">Configurações para Vinculação de Usuário Externo a Pessoa Jurídica
                    <img src="<?= PaginaSEI::getInstance()->getDiretorioImagensGlobal() ?>/ajuda.gif" name="ajuda"
                         id="imgAjuda" <?= PaginaSEI::montarTitleTooltip('Preencha estas configurações para permitir o Usuário Externo logado emitir Procurações Eletrônicas simples ou Procurações Eletrônicas Especiais para que outro Usuário Externo possa representar Pessoa Jurídica. \n \n Defina o Tipo de Processo e a Unidade onde cada Processo de controle de representação por Pessoa Jurídica será aberto.', 'Ajuda') ?>
                         class="infraImgFielset"/>
                </legend>
                <!--  Tipo de Processo  -->
                <div class="row">
                    <div class="col-sm-12 col-md-5 col-lg-5 col-xl-6">
                        <label id="lblTipoProcesso" for="txtTipoProcesso" class="infraLabelObrigatorio">Tipo de Processo
                            Associado:</label>
                        <div class="input-group mb-3">
                            <input type="text" onchange="removerProcessoAssociado(0);" id="txtTipoProcesso"
                                   name="txtTipoProcesso"
                                   class="infraText form-control" value="<?php echo $nomeTipoProcesso; ?>"
                                   tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
                            <input type="hidden" id="hdnIdTipoProcesso" name="hdnIdTipoProcesso"
                                   value="<?php echo $idTipoProcesso ?>"/>
                            <img id="imgLupaTipoProcesso" onclick="objLupaTipoProcesso.selecionar(700,500);"
                                 onkeypress="objLupaTipoProcesso.selecionar(700,500);"
                                 src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/pesquisar.svg"
                                 alt="Selecionar Tipo de Processo" title="Selecionar Tipo de Processo" class="infraImg"
                                 tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
                            <img id="imgExcluirTipoProcesso"
                                 onclick="removerProcessoAssociado(0);objLupaTipoProcesso.remover();"
                                 onkeypress="removerProcessoAssociado(0);objLupaTipoProcesso.remover();"
                                 src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/remover.svg"
                                 alt="Remover Tipo de Processo" title="Remover Tipo de Processo" class="infraImg"
                                 tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
                        </div>
                    </div>
                </div>
                <!--  Fim do Tipo de Processo -->
                <!--Especificação do Processo -->
                <div class="row">
                    <div class="col-sm-12 col-md-5 col-lg-5 col-xl-6">
                        <label id="lblEspecProc" for="txtEspecProc" class="infraLabelObrigatorio">Especificação do
                            Processo:
                            <img
                                    src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/ajuda.svg"
                                    name="ajuda"
                                    id="imgAjuda" <?= PaginaSEI::montarTitleTooltip('O texto aqui configurado será utilizado na Especificação dos processos abertos, sempre limitado a 100 caracteres no momento da abertura do processo. \n \n No texto podem ser utilizadas as variáveis a seguir: @cnpj@ - CNPJ da Pessoa Jurídica Outorgante @razao_social@ - Razão Social da Pessoa Jurídica Outorgante.', 'Ajuda') ?>
                                    class="infraImgModulo"/></label>
                        <input type="text" id="txtEspecProcPJ" name="txtEspecProcPJ"
                               onkeypress="return infraMascaraTexto(this,event,100);" class="infraText form-control"
                               value="<?php echo $especificacaoPJ ?>"
                               tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
                    </div>
                </div>
                <!--Especificação do Processo - FIM -->
                <!--  Unidade -->
                <div class="row">
                    <div class="col-sm-12 col-md-5 col-lg-5 col-xl-6">
                        <label id="lblUnidade" for="txtUnidade" class="infraLabelObrigatorio">Unidade para Abertura do
                            Processo:</label>
                        <div class="input-group mb-3">
                            <input type="text" id="txtUnidade" name="txtUnidade" class="infraText form-control"
                                   value="<?php echo $nomeUnidade; ?>"
                                   tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
                            <input type="hidden" id="hdnIdUnidade" name="hdnIdUnidade" value="<?= $idUnidade ?>"/>
                            <img id="imgLupaUnidade" onclick="objLupaUnidade.selecionar(700,500);"
                                 onkeypress="objLupaUnidade.selecionar(700,500);"
                                 src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/pesquisar.svg"
                                 alt="Selecionar Unidade"
                                 title="Selecionar Unidade" class="infraImg"
                                 tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
                            <img id="imgExcluirUnidade" onclick="objLupaUnidade.remover();"
                                 onkeypress="objLupaUnidade.remover();"
                                 src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/remover.svg"
                                 alt="Remover Unidade"
                                 title="Remover Unidade" class="infraImg"
                                 tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
                        </div>
                    </div>
                </div>
                <!--Fim da Unidade -->

                <!--   Exibir menu Procuração Eletrônica -->
                <div class="row">
                    <div class="col-sm-12 col-md-5 col-lg-5 col-xl-6">
                        <label id="lblMenuAcessoExterno" for="" class="infraLabelObrigatorio">Exibir menu Responsável
                            Legal de
                            Pessoa
                            Jurídica:</label>
                        <img src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/ajuda.svg" name="ajuda"
                             id="imgAjuda" <?= PaginaSEI::montarTitleTooltip('Esta configuração permite exibir o menu "Responsável Legal de Pessoa Jurídica" para os Usuários Externos. \n \n Além dessa configuração, para exibir o menu ainda é necessário o Mapeamento da Integração com a Receita Federal para consultar os dados do CNPJ. Se integração ainda não foi mapeada, acesse Administração >> Peticionamento Eletrônico >> Integrações >> Novo >> Funcionalidade: Consultar Dados CNPJ Receita Federal. \n \n Ainda, para exibir o menu nesse caso, necessariamente tem que selecionar acima para Exibir o menu de Procuração Eletrônica.', 'Ajuda') ?>
                             class="infraImgModulo"/>
                        <br/>
                        <input <?php if ($exibirMenuAcessoExterno == 'S') {
                            echo 'checked = checked';
                        } ?> type="radio" name="rdMenuAcessoExterno" id="rdExibirMenuAcessoExterno" value="S"
                             class="infraRadio"
                             tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>">
                        <label for="rdExibirMenuAcessoExterno" id="lblMenuAcessoExternoPF" class="infraLabelRadio">Exibir
                            no
                            Acesso
                            Externo</label>
                        <br/>
                        <input <?php if ($exibirMenuAcessoExterno == 'N') {
                            echo 'checked = checked';
                        } ?> type="radio" name="rdMenuAcessoExterno" id="rdNaoExibirMenuAcessoExterno" value="N"
                             class="infraRadio"
                             tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>">
                        <label name="lblMenuAcessoExternoPF" id="lblPadrao" for="rdNaoExibirMenuAcessoExterno"
                               class="infraLabelRadio">Não
                            exibir no Acesso Externo</label>
                    </div>
                </div>
                <!--  Fim Exibir menu Procuração Eletrônica -->
                <div class="row">
                    <div class="col-sm-12 col-md-7 col-lg-7 col-xl-7">
                        <fieldset class="infraFieldset form-control" style="height: auto">
                            <legend class="infraLegend">Nível de Acesso dos Documentos Peticionados
                                <img src="<?= PaginaSEI::getInstance()->getDiretorioImagensGlobal() ?>/ajuda.gif"
                                     name="ajuda"
                                     id="imgAjuda" <?= PaginaSEI::montarTitleTooltip('Indique o comportamento a ser adotado pelo SEI referente ao Nível de Acesso dos Atos Constitutivos ao Peticionar a Vinculação do Usuário Externo como Responsável Legal a Pessoa Jurídica. \n \n Utilize a opção "Usuário Externo indica diretamente" para permitir ao Usuário Externo selecionar o Nível de Acesso de cada documento adicionado. \n \n Utilize a opção "Padrão pré definido" para que os Atos Constitutivos sejam peticionados com o Nível de Acesso indicado aqui.', 'Ajuda') ?>
                                     class="infraImgFielset"/>
                            </legend>
                            <div class="row">
                                <div class="col-sm-12 col-md-5 col-lg-5 col-xl-6">
                                    <input <?php echo $sinNAUsuExt; ?> type="radio" name="rdNivelAcesso[]"
                                                                       class="infraRadio"
                                                                       id="rdUsuExternoIndicarEntrePermitidos"
                                                                       onclick="changeNivelAcesso();" value="1"
                                                                       tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>">

                                    <label for="rdUsuExternoIndicarEntrePermitidos" id="lblUsuExterno"
                                           class="infraLabelRadio">Usuário
                                        Externo indica diretamente</label>
                                    <br/>
                                    <input <?php echo $sinNAPadrao; ?> type="radio" name="rdNivelAcesso[]" id="rdPadrao"
                                                                       onclick="changeNivelAcesso();" value="2"
                                                                       class="infraRadio"
                                                                       tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>">
                                    <label name="lblPadrao" id="lblPadrao" for="rdPadrao" class="infraLabelRadio">Padrão
                                        pré
                                        definido</label>
                                </div>
                            </div>
                            <div class="row"
                                 id="divNivelAcesso" <?php echo $sinNAPadrao != '' ? 'style="display: inherit;"' : 'style="display: none;"' ?> >
                                <div class="col-sm-12 col-md-4 col-lg-4 col-xl-3">

                                    <label name="lblNivelAcesso" id="lblNivelAcesso" for="selNivelAcesso"
                                           class="infraLabelObrigatorio">Nível
                                        de Acesso:</label>
                                    <select id="selNivelAcesso" name="selNivelAcesso" class="infraSelect form-control"
                                            onchange="changeSelectNivelAcesso()"
                                            tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>">
                                        <?= $strItensSelNivelAcesso ?>
                                    </select>
                                </div>
                            </div>
                            <div class="row" id="divHipoteseLegal" <?php echo $hipoteseLegal ?> >
                                <div class="col-sm-12 col-md-8 col-lg-8 col-xl-8">
                                    <label name="lblHipoteseLegal" id="lblHipoteseLegal" for="selHipoteseLegal"
                                           class="infraLabelObrigatorio">Hipótese Legal:</label>
                                    <select id="selHipoteseLegal" name="selHipoteseLegal"
                                            class="infraSelect form-control"
                                            tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>">
                                        <?= $strItensSelHipoteseLegal ?>
                                    </select>
                                </div>
                            </div>
                        </fieldset>
                    </div>
                </div>
                <!--  Documento Obrigatório -->
                <?
                //$divDocs = $alterar || $gerado || $externo ? 'style="display: inherit;"' : 'style="display: none;"'
                $divDocs = 'style="display: inherit; margin-left: -5px; margin-top: -3px"';
                ?>
                <div class="row">
                    <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                        <fieldset <?php echo $divDocs; ?> id="fldDocObrigatorio" class="fieldsetClear">
                            <div>
                                <div style="clear:both;">&nbsp;</div>
                                <div>
                                    <label id="lblDescricaoEssencial" for="selDescricaoEssencial"
                                           class="infraLabelObrigatorio">Tipos
                                        dos Documentos de Atos Constitutivos Obrigatórios: <img
                                                src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/ajuda.svg"
                                                name="ajuda"
                                                id="imgAjuda" <?= PaginaSEI::montarTitleTooltip('Defina os Atos Constitutivos que obrigatoriamente o Usuário Externo deverá adicionar ao Peticionar a vinculação dele como Responsável Legal a uma Pessoa Jurídica.', 'Ajuda') ?>
                                                class="infraImgModulo"/></label>
                                </div>
                                <div>
                                    <input type="text" id="txtSerieEssencial" name="txtSerieEssencial" class="infraText"
                                           tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
                                </div>
                                <div style="margin-top: 5px;">
                                    <select style="float: left;" id="selDescricaoEssencial" name="selDescricaoEssencial"
                                            size="8"
                                            multiple="multiple" class="infraSelect"
                                            tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>">
                                        <?= $strItensSelSeriesEss; ?>
                                    </select>

                                    <img id="imgLupaTipoDocumentoObrigatorio"
                                         onclick="objLupaTipoDocumentoEssencial.selecionar(700,500)"
                                         onkeypress="objLupaTipoDocumentoEssencial.selecionar(700,500)"
                                         src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/pesquisar.svg"
                                         alt="Selecionar Tipo de Documento" title="Selecionar Tipo de Documento"
                                         class="infraImg"
                                         tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
                                    <br>
                                    <img id="imgExcluirTipoDocumentoObrigatorio"
                                         onclick="objLupaTipoDocumentoEssencial.remover();"
                                         onkeypress="objLupaTipoDocumentoEssencial.remover();"
                                         src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/remover.svg"
                                         alt="Remover Tipos de Documentos Selecionados"
                                         title="Remover Tipos de Documentos Selecionados"
                                         class="infraImg"
                                         tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
                                </div>

                            </div>
                        </fieldset>
                    </div>
                </div>
                <!--  Fim do Documento Obrigatorio -->

                <!--  Documento Não Obrigatorio  -->
                <div class="row">
                    <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                        <fieldset <?php echo $divDocs; ?> id="fldDocComplementar" class="fieldsetClear">
                            <div>
                                <div style="clear:both;">&nbsp;</div>
                                <div>
                                    <label id="lblDescricao" for="txtDescricao" class="infraLabelOpcional">Tipos dos
                                        Documentos
                                        de Atos
                                        Constitutivos não Obrigatórios: <img
                                                src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/ajuda.svg"
                                                name="ajuda"
                                                id="imgAjuda" <?= PaginaSEI::montarTitleTooltip('Defina os Atos Constitutivos que serão listados de forma não obrigatória para o Usuário Externo ao Peticionar a vinculação dele como Responsável Legal a uma Pessoa Jurídica.') ?>
                                                class="infraImgModulo"/></label>
                                </div>
                                <div>
                                    <input type="text" id="txtSerie" name="txtSerie" class="infraText"
                                           tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
                                </div>
                                <div style="margin-top: 5px;">
                                    <select style="float: left;" id="selDescricao" name="selDescricao" size="8"
                                            multiple="multiple"
                                            class="infraSelect"
                                            tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>">
                                        <?= $strItensSelSeries ?>
                                    </select>

                                    <img id="imgLupaTipoDocumento"
                                         onclick="carregarComponenteLupaTpDocComplementar('S');"
                                         onkeypress="carregarComponenteLupaTpDocComplementar('S');"
                                         src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/pesquisar.svg"
                                         alt="Selecionar Tipo de Documento" title="Selecionar Tipo de Documento"
                                         class="infraImg"
                                         tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
                                    <br>
                                    <img id="imgExcluirTipoDocumentoNaoObrigatorio"
                                         onclick="carregarComponenteLupaTpDocComplementar('R');"
                                         onkeypress="carregarComponenteLupaTpDocComplementar('R');"
                                         src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/remover.svg"
                                         alt="Remover Tipos de Documentos Selecionados"
                                         title="Remover Tipos de Documentos Selecionados"
                                         class="infraImg"
                                         tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
                                </div>

                            </div>
                        </fieldset>
                    </div>
                </div>
                <!--  Fim do Documento Complementar -->

                <input type="hidden" id="hdnCorpoTabela" name="hdnCorpoTabela" value=""/>
                <input type="hidden" id="hdnUnidadesSelecionadas" name="hdnUnidadesSelecionadas" value=""/>
                <input type="hidden" id="hdnTodasUnidades" name="hdnTodasUnidades"
                       value='<?= json_encode($arrObjUnidadeDTOFormatado); ?>'/>
                <input type="hidden" id="hdnIdTipoDocumento" name="hdnIdTipoDocumento" value=""/>
                <input type="hidden" id="hdnSerie" name="hdnSerie" value="<?= $_POST['hdnSerie'] ?>"/>
                <input type="hidden" id="hdnIdTipoDocumento" name="hdnIdTipoDocumento"
                       value="<?= $_POST['hdnIdTipoDocumento'] ?>"/>
                <input type="hidden" id="hdnIdIndisponibilidadePeticionamento"
                       name="hdnIdIndisponibilidadePeticionamento"
                       value=""/>
                <input type="hidden" id="hdnIdSerie" name="hdnIdSerie" value="<?= $_POST['hdnIdSerie'] ?>"/>
                <input type="hidden" id="hdnIdSerieEssencial" name="hdnIdSerieEssencial"
                       value="<?= $_POST['hdnIdSerieEssencial'] ?>"/>
                <input type="hidden" id="hdnSerieEssencial" name="hdnSerieEssencial"
                       value="<?= $_POST['hdnSerieEssencial'] ?>"/>
                <!-- Orientações -->
                <div class="row">
                    <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                        <label id="lblOrientacoes" for="txaConteudo" class="infraLabelOpcional">Orientações:
                            <img src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/ajuda.svg" name="ajuda"
                                 id="imgAjuda" <?= PaginaSEI::montarTitleTooltip('Defina as Orientações que devem ser apresentadas para o Usuário Externo na funcionalidade que permite a vinculação dele como Responsável Legal a uma Pessoa Jurídica.', 'Ajuda') ?>
                                 class="infraImgModulo"/>
                        </label>
                        <?php require_once 'md_pet_vinc_cadastro_orientacao.php'; ?>
                    </div>
                </div>
                <!--  Fim das Orientações  -->
            </fieldset>
        </div>
    </div>
    <?
    PaginaSEI::getInstance()->fecharAreaDados();
    PaginaSEI::getInstance()->montarBarraComandosInferior($arrComandos);
    ?>

</form>
<?
require_once "md_pet_vinc_tp_processo_cadastro_js.php";
PaginaSEI::getInstance()->fecharBody();
PaginaSEI::getInstance()->fecharHtml();
?>

