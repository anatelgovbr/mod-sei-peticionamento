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

    //Recupera o valor do Par�metro SEI_HABILITAR_HIPOTESE_LEGAL
    $objInfraParametro = new InfraParametro(BancoSEI::getInstance());
    $strValor = $objInfraParametro->getValor('SEI_HABILITAR_HIPOTESE_LEGAL');

    //Preparar Preenchimento Altera��o
    if (!empty($objMdPetVincTpProcessoDTO) && !isset($_POST['sbmCadastrarTpProcessoVinculacao'])) {

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

        if (is_iterable($arrObjMdPetVincRelSerieDTO)) {
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
            $strTitulo = 'Par�metros para Vincula��o a Usu�rio Externo';
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

                if ($_POST['selNivelAcesso'] == ProtocoloRN::$NA_RESTRITO && $valorParametroHipoteseLegal != '0' && $strValor > 0) {
                    $objMdPetVincTpProcessoDTO->setNumIdHipoteseLegal($idhipoteseLegal);
                    $hipoteseLegal = 'style="display: inherit;"';
                } else{
                    $objMdPetVincTpProcessoDTO->setNumIdHipoteseLegal(null);
                }
            }         

            if (isset($_POST['sbmCadastrarTpProcessoVinculacao'])) {
                try {

                    //Pessoa jur�dica
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
                            $msg = "Por favor informe um tipo de processo que na parametriza��o do SEI tenha indica��o de pelo menos uma sugest�o de assunto.";
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
                    
                    //Pessoa f�sica
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
                            $msg = "Por favor informe um tipo de processo que na parametriza��o do SEI tenha indica��o de pelo menos uma sugest�o de assunto.";
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

                    if (!empty($objMdPetVincRelSerieDTO)) {
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
                    PaginaSEI::getInstance()->adicionarMensagem("Os dados foram salvos com sucesso.", PaginaSEI::$TIPO_MSG_AVISO);
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
        <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
            <fieldset id="fldPeriodoIndisponibilidade" class="infraFieldset sizeFieldset form-control" style="height: auto">
                <legend class="infraLegend">Configura��es para Vincula��o de Usu�rio Externo a Pessoa F�sica
                    <img src="<?= PaginaSEI::getInstance()->getDiretorioImagensGlobal() ?>/ajuda.gif" name="ajuda"
                         id="imgAjuda" <?= PaginaSEI::montarTitleTooltip('Preencha estas configura��es para permitir o Usu�rio Externo logado emitir Procura��es Eletr�nicas simples para que outro Usu�rio Externo possa represent�-lo como Pessoa F�sica. \n \n Defina o Tipo de Processo e a Unidade onde cada Processo de controle de representa��o por Usu�rio Externo ser� aberto.', 'Ajuda') ?>
                         class="infraImgFielset"/>
                </legend>
                <!--  Tipo de Processo  -->
                <div class="row">
                    <div class="col-sm-12 col-md-6 col-lg-6 col-xl-6">
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
                                 src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/pesquisar.svg?<?= Icone::VERSAO ?>"
                                 alt="Selecionar Tipo de Processo" title="Selecionar Tipo de Processo" class="infraImg"
                                 tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
                            <img id="imgExcluirTipoProcesso"
                                 onclick="removerProcessoAssociadoPF(0);objLupaTipoProcessoPF.remover();"
                                 onkeypress="removerProcessoAssociadoPF(0);objLupaTipoProcessoPF.remover();"
                                 src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/remover.svg?<?= Icone::VERSAO ?>"
                                 alt="Remover Tipo de Processo" title="Remover Tipo de Processo" class="infraImg"
                                 tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
                        </div>
                    </div>
                </div>
                <!--  Fim do Tipo de Processo -->

                <!--Especifica��o do Processo -->
                <div class="row">
                    <div class="col-sm-12 col-md-6 col-lg-6 col-xl-6">
                        <label id="lblEspecProc" for="txtEspecProc" class="infraLabelObrigatorio">Especifica��o do
                            Processo:
                            <img
                                    src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/ajuda.svg?<?= Icone::VERSAO ?>"
                                    name="ajuda"
                                    id="imgAjuda" <?= PaginaSEI::montarTitleTooltip('O texto aqui configurado ser� utilizado na Especifica��o dos processos abertos, sempre limitado a 100 caracteres no momento da abertura do processo. \n \n No texto podem ser utilizadas as vari�veis a seguir: @cpf@ - CPF da Pessoa F�sica Outorgante @nome_completo@ - Nome Completo da Pessoa F�sica Outorgante. ', 'Ajuda') ?>
                                    class="infraImgModulo"/></label>
                        <input type="text" id="txtEspecProcPF" name="txtEspecProcPF"
                               onkeypress="return infraMascaraTexto(this,event,100);" class="infraText form-control"
                               value="<?php echo $especificacaoPF ?>"
                               tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
                    </div>
                </div>
                <!--Especifica��o do Processo - FIM -->

                <!--  Unidade -->
                <div class="row">
                    <div class="col-sm-12 col-md-6 col-lg-6 col-xl-6">
                        <label id="lblUnidade" for="txtUnidade" class="infraLabelObrigatorio">Unidade para Abertura do
                            Processo:</label>
                        <div class="input-group mb-3">
                            <input type="text" id="txtUnidadePF" name="txtUnidadePF" class="infraText form-control"
                                   value="<?php echo $nomeUnidadePF; ?>"
                                   tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
                            <input type="hidden" id="hdnIdUnidadePF" name="hdnIdUnidadePF" value="<?= $idUnidadePF ?>"/>
                            <img id="imgLupaUnidade" onclick="objLupaUnidadePF.selecionar(700,500);"
                                 onkeypress="objLupaUnidadePF.selecionar(700,500);"
                                 src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/pesquisar.svg?<?= Icone::VERSAO ?>"
                                 alt="Selecionar Unidade"
                                 title="Selecionar Unidade" class="infraImg"
                                 tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
                            <img id="imgExcluirUnidade" onclick="objLupaUnidadePF.remover();"
                                 onkeypress="objLupaUnidadePF.remover();"
                                 src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/remover.svg?<?= Icone::VERSAO ?>"
                                 alt="Remover Unidade"
                                 title="Remover Unidade" class="infraImg"
                                 tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
                        </div>
                    </div>
                </div>
                <!--Fim da Unidade -->

                <!--   Exibir menu Procura��o Eletr�nica -->
                <div class="row">
                    <div class="col-sm-12 col-md-7 col-lg-6 col-xl-6">
                        <label id="lblMenuAcessoExternoPF" for="" class="infraLabelObrigatorio">Exibir menu Procura��o
                            Eletr�nica: <img
                                    align="top"
                                    src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/ajuda.svg?<?= Icone::VERSAO ?>"
                                    name="ajuda" <?= PaginaSEI::montarTitleTooltip('Esta configura��o permite exibir o menu "Procura��es Eletr�nicas" para os Usu�rios Externos.', 'Ajuda') ?>
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
                               class="infraLabelRadio">N�o exibir no Acesso Externo</label>
                    </div>
                </div>
                <!--  Fim Exibir menu Procura��o Eletr�nica -->
            </fieldset>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
            <fieldset id="fldPeriodoIndisponibilidade" class="infraFieldset form-control" style="height: auto">
                <legend class="infraLegend">Configura��es para Vincula��o de Usu�rio Externo a Pessoa Jur�dica
                    <img src="<?= PaginaSEI::getInstance()->getDiretorioImagensGlobal() ?>/ajuda.gif" name="ajuda"
                         id="imgAjuda" <?= PaginaSEI::montarTitleTooltip('Preencha estas configura��es para permitir o Usu�rio Externo logado emitir Procura��es Eletr�nicas simples ou Procura��es Eletr�nicas Especiais para que outro Usu�rio Externo possa representar Pessoa Jur�dica. \n \n Defina o Tipo de Processo e a Unidade onde cada Processo de controle de representa��o por Pessoa Jur�dica ser� aberto.', 'Ajuda') ?>
                         class="infraImgFielset"/>
                </legend>
                <!--  Tipo de Processo  -->
                <div class="row">
                    <div class="col-sm-12 col-md-6 col-lg-6 col-xl-6">
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
                                 src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/pesquisar.svg?<?= Icone::VERSAO ?>"
                                 alt="Selecionar Tipo de Processo" title="Selecionar Tipo de Processo" class="infraImg"
                                 tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
                            <img id="imgExcluirTipoProcesso"
                                 onclick="removerProcessoAssociado(0);objLupaTipoProcesso.remover();"
                                 onkeypress="removerProcessoAssociado(0);objLupaTipoProcesso.remover();"
                                 src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/remover.svg?<?= Icone::VERSAO ?>"
                                 alt="Remover Tipo de Processo" title="Remover Tipo de Processo" class="infraImg"
                                 tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
                        </div>
                    </div>
                </div>
                <!--  Fim do Tipo de Processo -->
                <!--Especifica��o do Processo -->
                <div class="row">
                    <div class="col-sm-12 col-md-6 col-lg-6 col-xl-6">
                        <label id="lblEspecProc" for="txtEspecProc" class="infraLabelObrigatorio">Especifica��o do
                            Processo:
                            <img
                                    src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/ajuda.svg?<?= Icone::VERSAO ?>"
                                    name="ajuda"
                                    id="imgAjuda" <?= PaginaSEI::montarTitleTooltip('O texto aqui configurado ser� utilizado na Especifica��o dos processos abertos, sempre limitado a 100 caracteres no momento da abertura do processo. \n \n No texto podem ser utilizadas as vari�veis a seguir: @cnpj@ - CNPJ da Pessoa Jur�dica Outorgante @razao_social@ - Raz�o Social da Pessoa Jur�dica Outorgante.', 'Ajuda') ?>
                                    class="infraImgModulo"/></label>
                        <input type="text" id="txtEspecProcPJ" name="txtEspecProcPJ"
                               onkeypress="return infraMascaraTexto(this,event,100);" class="infraText form-control"
                               value="<?php echo $especificacaoPJ ?>"
                               tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
                    </div>
                </div>
                <!--Especifica��o do Processo - FIM -->
                <!--  Unidade -->
                <div class="row">
                    <div class="col-sm-12 col-md-6 col-lg-6 col-xl-6">
                        <label id="lblUnidade" for="txtUnidade" class="infraLabelObrigatorio">Unidade para Abertura do
                            Processo:</label>
                        <div class="input-group mb-3">
                            <input type="text" id="txtUnidade" name="txtUnidade" class="infraText form-control"
                                   value="<?php echo $nomeUnidade; ?>"
                                   tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
                            <input type="hidden" id="hdnIdUnidade" name="hdnIdUnidade" value="<?= $idUnidade ?>"/>
                            <img id="imgLupaUnidade" onclick="objLupaUnidade.selecionar(700,500);"
                                 onkeypress="objLupaUnidade.selecionar(700,500);"
                                 src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/pesquisar.svg?<?= Icone::VERSAO ?>"
                                 alt="Selecionar Unidade"
                                 title="Selecionar Unidade" class="infraImg"
                                 tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
                            <img id="imgExcluirUnidade" onclick="objLupaUnidade.remover();"
                                 onkeypress="objLupaUnidade.remover();"
                                 src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/remover.svg?<?= Icone::VERSAO ?>"
                                 alt="Remover Unidade"
                                 title="Remover Unidade" class="infraImg"
                                 tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
                        </div>
                    </div>
                </div>
                <!--Fim da Unidade -->

                <!--   Exibir menu Procura��o Eletr�nica -->
                <div class="row">
                    <div class="col-sm-12 col-md-9 col-lg-9 col-xl-9">
                        <label id="lblMenuAcessoExterno" for="" class="infraLabelObrigatorio">Exibir menu Respons�vel
                            Legal de
                            Pessoa
                            Jur�dica:</label>
                        <img src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/ajuda.svg?<?= Icone::VERSAO ?>" name="ajuda"
                             id="imgAjuda" <?= PaginaSEI::montarTitleTooltip('Esta configura��o permite exibir o menu "Respons�vel Legal de Pessoa Jur�dica" para os Usu�rios Externos. \n \n Al�m dessa configura��o, para exibir o menu ainda � necess�rio o Mapeamento da Integra��o com a Receita Federal para consultar os dados do CNPJ. Se integra��o ainda n�o foi mapeada, acesse Administra��o >> Peticionamento Eletr�nico >> Integra��es >> Novo >> Funcionalidade: Consultar Dados CNPJ Receita Federal. \n \n Ainda, para exibir o menu nesse caso, necessariamente tem que selecionar acima para Exibir o menu de Procura��o Eletr�nica.', 'Ajuda') ?>
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
                               class="infraLabelRadio">N�o
                            exibir no Acesso Externo</label>
                    </div>
                </div>
                <!--  Fim Exibir menu Procura��o Eletr�nica -->
                <div class="row">
                    <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                        <fieldset class="infraFieldset form-control" style="height: auto">
                            <legend class="infraLegend">N�vel de Acesso dos Documentos Peticionados
                                <img src="<?= PaginaSEI::getInstance()->getDiretorioImagensGlobal() ?>/ajuda.gif"
                                     name="ajuda"
                                     id="imgAjuda" <?= PaginaSEI::montarTitleTooltip('Indique o comportamento a ser adotado pelo SEI referente ao N�vel de Acesso dos Atos Constitutivos ao Peticionar a Vincula��o do Usu�rio Externo como Respons�vel Legal a Pessoa Jur�dica. \n \n Utilize a op��o "Usu�rio Externo indica diretamente" para permitir ao Usu�rio Externo selecionar o N�vel de Acesso de cada documento adicionado. \n \n Utilize a op��o "Padr�o pr� definido" para que os Atos Constitutivos sejam peticionados com o N�vel de Acesso indicado aqui.', 'Ajuda') ?>
                                     class="infraImgFielset"/>
                            </legend>
                            <div class="row">
                                <div class="col-sm-12 col-md-6 col-lg-6 col-xl-6">
                                    <input <?php echo $sinNAUsuExt; ?> type="radio" name="rdNivelAcesso[]"
                                                                       class="infraRadio"
                                                                       id="rdUsuExternoIndicarEntrePermitidos"
                                                                       onclick="changeNivelAcesso();" value="1"
                                                                       tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>">

                                    <label for="rdUsuExternoIndicarEntrePermitidos" id="lblUsuExterno"
                                           class="infraLabelRadio">Usu�rio
                                        Externo indica diretamente</label>
                                    <br/>
                                    <input <?php echo $sinNAPadrao; ?> type="radio" name="rdNivelAcesso[]" id="rdPadrao"
                                                                       onclick="changeNivelAcesso();" value="2"
                                                                       class="infraRadio"
                                                                       tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>">
                                    <label name="lblPadrao" id="lblPadrao" for="rdPadrao" class="infraLabelRadio">Padr�o
                                        pr�
                                        definido</label>
                                </div>
                            </div>
                            <div class="row"
                                 id="divNivelAcesso" <?php echo $sinNAPadrao != '' ? 'style="display: inherit;"' : 'style="display: none;"' ?> >
                                <div class="col-sm-12 col-md-5 col-lg-5 col-xl-5">

                                    <label name="lblNivelAcesso" id="lblNivelAcesso" for="selNivelAcesso"
                                           class="infraLabelObrigatorio">N�vel
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
                                           class="infraLabelObrigatorio">Hip�tese Legal:</label>
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
                <!--  Documento Obrigat�rio -->
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
                                        dos Documentos de Atos Constitutivos Obrigat�rios: <img
                                                src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/ajuda.svg?<?= Icone::VERSAO ?>"
                                                name="ajuda"
                                                id="imgAjuda" <?= PaginaSEI::montarTitleTooltip('Defina os Atos Constitutivos que obrigatoriamente o Usu�rio Externo dever� adicionar ao Peticionar a vincula��o dele como Respons�vel Legal a uma Pessoa Jur�dica.', 'Ajuda') ?>
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
                                         src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/pesquisar.svg?<?= Icone::VERSAO ?>"
                                         alt="Selecionar Tipo de Documento" title="Selecionar Tipo de Documento"
                                         class="infraImg"
                                         tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
                                    <br>
                                    <img id="imgExcluirTipoDocumentoObrigatorio"
                                         onclick="objLupaTipoDocumentoEssencial.remover();"
                                         onkeypress="objLupaTipoDocumentoEssencial.remover();"
                                         src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/remover.svg?<?= Icone::VERSAO ?>"
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

                <!--  Documento N�o Obrigatorio  -->
                <div class="row">
                    <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                        <fieldset <?php echo $divDocs; ?> id="fldDocComplementar" class="fieldsetClear">
                            <div>
                                <div style="clear:both;">&nbsp;</div>
                                <div>
                                    <label id="lblDescricao" for="txtDescricao" class="infraLabelOpcional">Tipos dos
                                        Documentos
                                        de Atos
                                        Constitutivos n�o Obrigat�rios: <img
                                                src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/ajuda.svg?<?= Icone::VERSAO ?>"
                                                name="ajuda"
                                                id="imgAjuda" <?= PaginaSEI::montarTitleTooltip('Defina os Atos Constitutivos que ser�o listados de forma n�o obrigat�ria para o Usu�rio Externo ao Peticionar a vincula��o dele como Respons�vel Legal a uma Pessoa Jur�dica.', 'Ajuda') ?>
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
                                         src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/pesquisar.svg?<?= Icone::VERSAO ?>"
                                         alt="Selecionar Tipo de Documento" title="Selecionar Tipo de Documento"
                                         class="infraImg"
                                         tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
                                    <br>
                                    <img id="imgExcluirTipoDocumentoNaoObrigatorio"
                                         onclick="carregarComponenteLupaTpDocComplementar('R');"
                                         onkeypress="carregarComponenteLupaTpDocComplementar('R');"
                                         src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/remover.svg?<?= Icone::VERSAO ?>"
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
                <!-- Orienta��es -->
                <div class="row">
                    <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                        <label id="lblOrientacoes" for="txaConteudo" class="infraLabelOpcional">Orienta��es:
                            <img src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/ajuda.svg?<?= Icone::VERSAO ?>" name="ajuda"
                                 id="imgAjuda" <?= PaginaSEI::montarTitleTooltip('Defina as Orienta��es que devem ser apresentadas para o Usu�rio Externo na funcionalidade que permite a vincula��o dele como Respons�vel Legal a uma Pessoa Jur�dica.', 'Ajuda') ?>
                                 class="infraImgModulo"/>
                        </label>
                        <?php require_once 'md_pet_vinc_cadastro_orientacao.php'; ?>
                    </div>
                </div>
                <!--  Fim das Orienta��es  -->
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