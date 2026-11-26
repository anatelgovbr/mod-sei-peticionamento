<?php
/**
 * ANATEL
 *
 * Cadastro, alteracao e consulta do Tipo de Processo do Peticionamento Interno.
 *
 * 08/09/2026 - criado por emerson_b.goncalves - ANATEL
 */
try {
    require_once dirname(__FILE__) . '/../../SEI.php';

    session_start();

    PaginaSEI::getInstance()->setBolXHTML(false);

    SessaoSEI::getInstance()->validarLink();

    $strAcao = MdPetPiEntradaINT::getStr('acao');

    SessaoSEI::getInstance()->validarPermissao($strAcao);

    require_once 'md_pet_pi_tp_processo_cadastro_inicializacao.php';

    $staTipoEditor = EditorRN::obterTipoEditorSimples();

    $arrIndicacaoInteressado = MdPetPiTpProcessoINT::listarValoresIndicacaoInteressado();
    $arrAjudaIndicacaoInteressado = MdPetPiTpProcessoINT::listarAjudasIndicacaoInteressado();
    $arrDocPrincipal = MdPetPiTpProcessoINT::listarValoresDocPrincipal();
    $arrAjudaDocPrincipal = MdPetPiTpProcessoINT::listarAjudasDocPrincipal();
    $arrAjudaBlocoDocumento = MdPetPiTpProcessoINT::listarAjudasBlocoDocumento();
    $arrTipoNivelAcesso = MdPetPiTpProcessoINT::listarValoresTipoNivelAcesso();
    $arrAjudaTipoNivelAcesso = MdPetPiTpProcessoINT::listarAjudasTipoNivelAcesso();
    $bolHipoteseLegalHabilitada = MdPetPiTpProcessoRN::isHipoteseLegalHabilitada();

    $bolConsultar = $strAcao === 'md_pet_pi_tp_processo_consultar';
    $bolAlterar = $strAcao === 'md_pet_pi_tp_processo_alterar';

    // Carrega o registro existente nas acoes de alteracao e consulta
    if ($bolAlterar || $bolConsultar) {

        $numIdMdPetPiTpProcesso = MdPetPiEntradaINT::getInt('id_md_pet_pi_tp_processo');

        if ($numIdMdPetPiTpProcesso === null) {
            throw new InfraException('Tipo de Processo do Peticionamento Interno não informado.');
        }

        $objMdPetPiTpProcessoDTO = new MdPetPiTpProcessoDTO();
        $objMdPetPiTpProcessoDTO->setNumIdMdPetPiTpProcesso($numIdMdPetPiTpProcesso);
        $objMdPetPiTpProcessoDTO->retTodos();
        $objMdPetPiTpProcessoDTO->retStrNomeTipoProcedimento();
        $objMdPetPiTpProcessoDTO = (new MdPetPiTpProcessoRN())->consultar($objMdPetPiTpProcessoDTO);

        if ($objMdPetPiTpProcessoDTO === null) {
            throw new InfraException('Tipo de Processo do Peticionamento Interno não encontrado.');
        }

        $numIdTipoProcesso = $objMdPetPiTpProcessoDTO->getNumIdTipoProcedimento();
        $strNomeTipoProcesso = $objMdPetPiTpProcessoDTO->getStrNomeTipoProcedimento();
        $strOrientacoes = $objMdPetPiTpProcessoDTO->getStrOrientacoes();
        $strOrientacoesPosPet = $objMdPetPiTpProcessoDTO->getStrOrientacoesPosPet();
        $strStaIndicacaoInteressado = $objMdPetPiTpProcessoDTO->getStrStaIndicacaoInteressado();

        $objDocPrincDTO = new MdPetPiDocPrincDTO();
        $objDocPrincDTO->setNumIdMdPetPiTpProcesso($numIdMdPetPiTpProcesso);
        $objDocPrincDTO->retTodos();
        $objDocPrincDTO->retStrNomeSerie();
        $objDocPrincDTO->retStrNomeHipoteseLegal();
        $objDocPrincDTO->retStrProtocoloFormatadoModelo();
        $objDocPrincDTO = (new MdPetPiDocPrincRN())->consultar($objDocPrincDTO);

        if ($objDocPrincDTO !== null) {
            $strStaAplicabilidadeDocPrinc = $objDocPrincDTO->getStrStaAplicabilidade();

            $arrDocPrinc[] = [
                'idSerie' => $objDocPrincDTO->getNumIdSerie(),
                'nomeSerie' => $objDocPrincDTO->getStrNomeSerie(),
                'idProtocoloModelo' => $objDocPrincDTO->getDblIdProtocoloModelo(),
                'protocoloModelo' => $objDocPrincDTO->getStrProtocoloFormatadoModelo(),
                'tipoNivelAcesso' => $objDocPrincDTO->getStrStaTipoNivelAcesso(),
                'nivelAcesso' => $objDocPrincDTO->getStrStaNivelAcesso(),
                'idHipoteseLegal' => $objDocPrincDTO->getNumIdHipoteseLegal(),
                'nomeHipoteseLegal' => $objDocPrincDTO->getStrNomeHipoteseLegal(),
            ];
        }

        $objAnexoDTO = new MdPetPiAnexoObrigDTO();
        $objAnexoDTO->setNumIdMdPetPiTpProcesso($numIdMdPetPiTpProcesso);
        $objAnexoDTO->retTodos();
        $objAnexoDTO->retStrNomeSerie();
        $objAnexoDTO->retStrNomeHipoteseLegal();
        $objAnexoDTO->setOrdStrNomeSerie(InfraDTO::$TIPO_ORDENACAO_ASC);

        foreach ((new MdPetPiAnexoObrigRN())->listar($objAnexoDTO) as $objDTO) {
            $arrAnexoObrig[] = [
                'idSerie' => $objDTO->getNumIdSerie(),
                'nomeSerie' => $objDTO->getStrNomeSerie(),
                'tipoNivelAcesso' => $objDTO->getStrStaTipoNivelAcesso(),
                'nivelAcesso' => $objDTO->getStrStaNivelAcesso(),
                'idHipoteseLegal' => $objDTO->getNumIdHipoteseLegal(),
                'nomeHipoteseLegal' => $objDTO->getStrNomeHipoteseLegal(),
            ];
        }

        $objAutoDTO = new MdPetPiDocAutoDTO();
        $objAutoDTO->setNumIdMdPetPiTpProcesso($numIdMdPetPiTpProcesso);
        $objAutoDTO->retTodos();
        $objAutoDTO->retStrNomeSerie();
        $objAutoDTO->retStrNomeHipoteseLegal();
        $objAutoDTO->retStrProtocoloFormatadoModelo();
        $objAutoDTO->setOrdStrNomeSerie(InfraDTO::$TIPO_ORDENACAO_ASC);

        foreach ((new MdPetPiDocAutoRN())->listar($objAutoDTO) as $objDTO) {
            $arrDocAuto[] = [
                'idSerie' => $objDTO->getNumIdSerie(),
                'nomeSerie' => $objDTO->getStrNomeSerie(),
                'idProtocoloModelo' => $objDTO->getDblIdProtocoloModelo(),
                'protocoloModelo' => $objDTO->getStrProtocoloFormatadoModelo(),
                'nivelAcesso' => $objDTO->getStrStaNivelAcesso(),
                'idHipoteseLegal' => $objDTO->getNumIdHipoteseLegal(),
                'nomeHipoteseLegal' => $objDTO->getStrNomeHipoteseLegal(),
            ];
        }

        $arrRestricao = MdPetPiTpProcessoINT::listarRestricaoOrgaoUnidade($numIdMdPetPiTpProcesso);
    }

    // Reaplica o que veio do formulario para nao perder o preenchimento em caso de erro
    if (MdPetPiEntradaINT::postStr('hdnSubmetido') === '1') {
        $numIdMdPetPiTpProcesso = MdPetPiEntradaINT::postInt('hdnIdMdPetPiTpProcesso');
        $numIdTipoProcesso = MdPetPiEntradaINT::postInt('hdnIdTipoProcesso');
        $strNomeTipoProcesso = MdPetPiEntradaINT::postStr('txtTipoProcesso');
        $strOrientacoes = MdPetPiEntradaINT::postStr('txaOrientacoes');
        $strOrientacoesPosPet = MdPetPiEntradaINT::postStr('txaOrientacoesPosPet');
        $strStaIndicacaoInteressado = MdPetPiEntradaINT::postEnum('rdIndicacaoInteressado', $arrIndicacaoInteressado);
        $strStaAplicabilidadeDocPrinc = MdPetPiEntradaINT::postEnum('rdAplicabilidade', $arrDocPrincipal);
        $arrDocPrinc = MdPetPiTpProcessoINT::decodificarGridDocumentos(MdPetPiEntradaINT::postStr('hdnGridDocPrinc'), true, true);
        $arrAnexoObrig = MdPetPiTpProcessoINT::decodificarGridDocumentos(MdPetPiEntradaINT::postStr('hdnGridAnexoObrig'), false, true);
        $arrDocAuto = MdPetPiTpProcessoINT::decodificarGridDocumentos(MdPetPiEntradaINT::postStr('hdnGridDocAuto'), true, false);
        $arrRestricao = MdPetPiTpProcessoINT::lerRestricaoOrgaoUnidade();
    }

    switch ($strAcao) {

        case 'md_pet_pi_tp_processo_cadastrar':
            $strTitulo = 'Novo Tipo de Processo para Peticionamento Interno';
            break;

        case 'md_pet_pi_tp_processo_alterar':
            $strTitulo = 'Alterar Tipo de Processo para Peticionamento Interno';
            break;

        case 'md_pet_pi_tp_processo_consultar':
            $strTitulo = 'Consultar Tipo de Processo para Peticionamento Interno';
            $strDesabilitar = 'disabled="disabled"';
            break;

        default:
            throw new InfraException("Ação '" . $strAcao . "' não reconhecida.");
    }

    if ($bolConsultar) {
        $arrComandos[] = '<button type="button" accesskey="c" name="btnCancelar" id="btnCancelar" value="Fechar" onclick="location.href=\'' . PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . PaginaSEI::getInstance()->getAcaoRetorno() . '&acao_origem=' . $strAcao . PaginaSEI::getInstance()->montarAncora($numIdMdPetPiTpProcesso))) . '\';" class="infraButton">Fe<span class="infraTeclaAtalho">c</span>har</button>';
    } else {
        $arrComandos[] = '<button type="submit" accesskey="s" name="sbmSalvar" id="sbmSalvar" value="Salvar" class="infraButton"><span class="infraTeclaAtalho">S</span>alvar</button>';
        $arrComandos[] = '<button type="button" accesskey="c" name="btnCancelar" id="btnCancelar" value="Cancelar" onclick="location.href=\'' . PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . PaginaSEI::getInstance()->getAcaoRetorno() . '&acao_origem=' . $strAcao . PaginaSEI::getInstance()->montarAncora($numIdMdPetPiTpProcesso))) . '\';" class="infraButton"><span class="infraTeclaAtalho">C</span>ancelar</button>';
    }

    // Gravacao
    if (!$bolConsultar && MdPetPiEntradaINT::postStr('sbmSalvar') !== '') {

        try {
            (new EditorRN())->validarTagsCriticas(['jpg', 'png'], $strOrientacoes);

            if ($strOrientacoesPosPet !== '') {
                (new EditorRN())->validarTagsCriticas(['jpg', 'png'], $strOrientacoesPosPet);
            }

            $objConjuntoEstilosDTO = new ConjuntoEstilosDTO();
            $objConjuntoEstilosDTO->retNumIdConjuntoEstilos();
            $objConjuntoEstilosDTO->setStrSinUltimo('S');
            $objConjuntoEstilosDTO = (new ConjuntoEstilosRN())->consultar($objConjuntoEstilosDTO);

            $objMdPetPiTpProcessoDTO = new MdPetPiTpProcessoDTO();
            $objMdPetPiTpProcessoDTO->setNumIdTipoProcedimento($numIdTipoProcesso);
            $objMdPetPiTpProcessoDTO->setStrOrientacoes($strOrientacoes);
            $objMdPetPiTpProcessoDTO->setStrOrientacoesPosPet($strOrientacoesPosPet !== '' ? $strOrientacoesPosPet : null);
            $objMdPetPiTpProcessoDTO->setStrStaIndicacaoInteressado($strStaIndicacaoInteressado);
            $objMdPetPiTpProcessoDTO->setNumIdConjuntoEstilos($objConjuntoEstilosDTO !== null ? $objConjuntoEstilosDTO->getNumIdConjuntoEstilos() : null);
            $objMdPetPiTpProcessoDTO->setStrSinAtivo('S');

            $objMdPetPiTpProcessoDTO->setObjObjMdPetPiDocPrincDTO(
                MdPetPiTpProcessoINT::montarDocPrincDTO($strStaAplicabilidadeDocPrinc, $arrDocPrinc)
            );
            $objMdPetPiTpProcessoDTO->setArrObjMdPetPiAnexoObrigDTO(MdPetPiTpProcessoINT::montarAnexoObrigDTO($arrAnexoObrig));
            $objMdPetPiTpProcessoDTO->setArrObjMdPetPiDocAutoDTO(MdPetPiTpProcessoINT::montarDocAutoDTO($arrDocAuto));
            $objMdPetPiTpProcessoDTO->setArrObjMdPetPiRelOrgaoDTO(MdPetPiTpProcessoINT::montarRelOrgaoDTO($arrRestricao));
            $objMdPetPiTpProcessoDTO->setArrObjMdPetPiRelUnidadeDTO(MdPetPiTpProcessoINT::montarRelUnidadeDTO($arrRestricao));

            if ($bolAlterar) {
                $objMdPetPiTpProcessoDTO->setNumIdMdPetPiTpProcesso($numIdMdPetPiTpProcesso);
                (new MdPetPiTpProcessoRN())->alterar($objMdPetPiTpProcessoDTO);
                $numIdSalvo = $numIdMdPetPiTpProcesso;
            } else {
                $numIdSalvo = (new MdPetPiTpProcessoRN())->cadastrar($objMdPetPiTpProcessoDTO)->getNumIdMdPetPiTpProcesso();
            }

            PaginaSEI::getInstance()->adicionarMensagem('Operação realizada com sucesso.');

            header('Location: ' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . PaginaSEI::getInstance()->getAcaoRetorno() . '&acao_origem=' . $strAcao . '&id_md_pet_pi_tp_processo=' . $numIdSalvo . PaginaSEI::getInstance()->montarAncora($numIdSalvo)));
            die;

        } catch (Exception $e) {
            PaginaSEI::getInstance()->processarExcecao($e);
        }
    }

    $strItensSelNivelAcesso = MdPetPiTpProcessoINT::montarSelectNivelAcesso(null, null, null, $numIdTipoProcesso);
    $strItensSelHipoteseLegal = MdPetPiTpProcessoINT::montarSelectHipoteseLegal('', ' ', null, ProtocoloRN::$NA_RESTRITO);

    // Listas de Tipo de Documento por aplicabilidade, copiadas pelo JavaScript para os selects de cada bloco
    if (!$bolConsultar) {
        $strItensSelSerieGerado = MdPetPiTpProcessoINT::montarSelectTipoDocumento('', ' ', null, MdPetPiTpProcessoRN::$DOC_GERADO);
        $strItensSelSerieExterno = MdPetPiTpProcessoINT::montarSelectTipoDocumento('', ' ', null, MdPetPiTpProcessoRN::$DOC_EXTERNO);
        $strItensSelFormulario = MdPetPiTpProcessoINT::montarSelectTipoDocumento('', ' ', null, MdPetPiTpProcessoRN::$DOC_FORMULARIO);
    }

    // Editores
    $objEditorOrientacoesDTO = new EditorDTO();
    $objEditorOrientacoesDTO->setStrNomeCampo('txaOrientacoes');
    $objEditorOrientacoesDTO->setStrSinSomenteLeitura($bolConsultar ? 'S' : 'N');
    $objEditorOrientacoesDTO->setNumTamanhoEditor(200);
    $objEditorOrientacoesDTO->setStrConteudoInicial($strOrientacoes);

    $objEditorPosPetDTO = new EditorDTO();
    $objEditorPosPetDTO->setStrNomeCampo('txaOrientacoesPosPet');
    $objEditorPosPetDTO->setStrSinSomenteLeitura($bolConsultar ? 'S' : 'N');
    $objEditorPosPetDTO->setNumTamanhoEditor(200);
    $objEditorPosPetDTO->setStrConteudoInicial($strOrientacoesPosPet);

    if ($staTipoEditor == EditorRN::$VE_CK5) {
        EditorCk5RN::montarSimples($objEditorOrientacoesDTO);
        EditorCk5RN::montarSimples($objEditorPosPetDTO);
    } else {
        $objEditorOrientacoesDTO = (new EditorRN())->montarSimples($objEditorOrientacoesDTO);
        $objEditorPosPetDTO = (new EditorRN())->montarSimples($objEditorPosPetDTO);
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
EditorINT::montarCss();
PaginaSEI::getInstance()->abrirStyle();
require_once 'md_pet_pi_tp_processo_cadastro_css.php';
PaginaSEI::getInstance()->fecharStyle();
PaginaSEI::getInstance()->montarJavaScript();
PaginaSEI::getInstance()->abrirJavaScript();
PaginaSEI::getInstance()->fecharJavaScript();

require_once 'md_pet_pi_tp_processo_cadastro_editor.php';

PaginaSEI::getInstance()->fecharHead();
PaginaSEI::getInstance()->abrirBody($strTitulo, 'onload="inicializar();"');
?>
<form id="frmMdPetPiTpProcessoCadastro" method="post" onsubmit="return onSubmitForm();"
      action="<?= PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . $strAcao . '&acao_origem=' . $strAcao . ($numIdMdPetPiTpProcesso !== null ? '&id_md_pet_pi_tp_processo=' . $numIdMdPetPiTpProcesso : ''))) ?>">

    <?php
    PaginaSEI::getInstance()->montarBarraComandosSuperior($arrComandos);
    PaginaSEI::getInstance()->abrirAreaDados('98%');
    ?>

    <div id="divGeral" class="infraAreaDados">

        <div class="row ml-0">
            <div class="col-sm-12 col-md-12 col-lg-12">
                <div class="form-group">
                    <label id="lblTipoProcesso" for="txtTipoProcesso" class="infraLabelObrigatorio">Tipo de Processo:
                        <img align="top" class="infraImg" name="ajuda"
                             src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/ajuda.svg"
                             <?= PaginaSEI::montarTitleTooltip('Indique o Tipo de Processo que será disponibilizado no Peticionamento Interno, digitando parte do nome ou clicando na Lupa.' . "\n\n" . 'Cada Tipo de Processo admite um único cadastro para Peticionamento Interno e precisa ter Classificação por Assuntos vinculada na Administração do SEI.', 'Ajuda') ?> />
                    </label>
                    <div class="input-group mb-3">
                        <input type="text" id="txtTipoProcesso" name="txtTipoProcesso" class="infraText form-control"
                               value="<?= PaginaSEI::tratarHTML($strNomeTipoProcesso) ?>" <?= $strDesabilitar ?>
                               tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
                        <input type="hidden" id="hdnIdTipoProcesso" name="hdnIdTipoProcesso" value="<?= PaginaSEI::tratarHTML($numIdTipoProcesso) ?>"/>
                        <input type="hidden" id="hdnIdMdPetPiTpProcesso" name="hdnIdMdPetPiTpProcesso" value="<?= PaginaSEI::tratarHTML($numIdMdPetPiTpProcesso) ?>"/>
                        <?php if (!$bolConsultar) { ?>
                            <img id="imgLupaTipoProcesso" onclick="objLupaTipoProcesso.selecionar(700, 500);"
                                 onkeypress="objLupaTipoProcesso.selecionar(700, 500);"
                                 src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/pesquisar.svg"
                                 alt="Selecionar Tipo de Processo" title="Selecionar Tipo de Processo"
                                 tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="row ml-0 mb-3">
            <div class="col-sm-12 col-md-12 col-lg-12">
                <div class="form-group">
                    <label id="lblOrientacoes" for="txaOrientacoes" class="infraLabelObrigatorio">Orientações:
                        <img align="top" class="infraImg" name="ajuda"
                             src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/ajuda.svg"
                             <?= PaginaSEI::montarTitleTooltip('Orientações específicas exibidas ao Usuário depois que ele escolhe o Tipo de Processo para abrir.', 'Ajuda') ?> />
                    </label>
                    <?php if ($staTipoEditor == EditorRN::$VE_CK5) { ?>
                        <div id="divEditorOrientacoes" class="infra-editor" style="visibility: visible;">
                            <?= $objEditorOrientacoesDTO->getStrHtml(); ?>
                        </div>
                    <?php } else { ?>
                        <div id="divEditorOrientacoes" class="mb-0">
                            <textarea id="txaOrientacoes" name="txaOrientacoes" rows="8" class="infraTextarea"
                                      tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"><?= PaginaSEI::tratarHTML($strOrientacoes) ?></textarea>
                            <script type="text/javascript"> <?= $objEditorOrientacoesDTO->getStrEditores(); ?> </script>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>

        <?php require 'md_pet_pi_tp_processo_cadastro_disponibilizacao.php'; ?>

        <div class="row mb-3 ml-0">
            <div class="col-sm-12 col-md-12 col-lg-12">
                <fieldset class="infraFieldset">
                    <legend class="infraLegend">&nbsp;Tipo de Indicação de Interessado&nbsp;</legend>
                    <div class="form-group">
                        <?php foreach ($arrIndicacaoInteressado as $strChave => $strDescricao) { ?>
                            <div class="infraDivRadio">
                                <input type="radio" name="rdIndicacaoInteressado" id="rdII<?= PaginaSEI::tratarHTML($strChave) ?>"
                                       value="<?= PaginaSEI::tratarHTML($strChave) ?>" class="infraRadio" <?= $strDesabilitar ?>
                                       <?= $strStaIndicacaoInteressado === $strChave ? 'checked="checked"' : '' ?>
                                       tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
                                <label for="rdII<?= PaginaSEI::tratarHTML($strChave) ?>" class="infraLabelRadio"><?= PaginaSEI::tratarHTML($strDescricao) ?></label>
                                <img align="top" class="infraImg" name="ajuda"
                                     src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/ajuda.svg"
                                     <?= PaginaSEI::montarTitleTooltip($arrAjudaIndicacaoInteressado[$strChave], 'Ajuda') ?> />
                            </div>
                        <?php } ?>
                    </div>
                </fieldset>
            </div>
        </div>

        <?php if (!$bolConsultar) { ?>
            <select id="selModeloSerie<?= MdPetPiTpProcessoRN::$DOC_GERADO ?>" class="infraSelect" style="display: none;" disabled="disabled"><?= $strItensSelSerieGerado ?></select>
            <select id="selModeloSerie<?= MdPetPiTpProcessoRN::$DOC_EXTERNO ?>" class="infraSelect" style="display: none;" disabled="disabled"><?= $strItensSelSerieExterno ?></select>
            <select id="selModeloSerie<?= MdPetPiTpProcessoRN::$DOC_FORMULARIO ?>" class="infraSelect" style="display: none;" disabled="disabled"><?= $strItensSelFormulario ?></select>
        <?php } ?>

        <?php
        $strBlocoId = 'DocPrinc';
        $strBlocoLegenda = 'Documento Principal';
        $bolBlocoComAplicabilidade = true;
        $bolBlocoComModelo = true;
        $bolBlocoComComportamento = true;
        $bolBlocoUnico = true;
        $arrBlocoGrid = $arrDocPrinc;
        require 'md_pet_pi_tp_processo_cadastro_bloco_doc.php';

        $strBlocoId = 'AnexoObrig';
        $strBlocoLegenda = 'Anexos de Arquivos Obrigatórios';
        $bolBlocoComAplicabilidade = false;
        $bolBlocoComModelo = false;
        $bolBlocoComComportamento = true;
        $bolBlocoUnico = false;
        $arrBlocoGrid = $arrAnexoObrig;
        require 'md_pet_pi_tp_processo_cadastro_bloco_doc.php';

        $strBlocoId = 'DocAuto';
        $strBlocoLegenda = 'Documento Adicional Gerado Automático';
        $bolBlocoComAplicabilidade = false;
        $bolBlocoComModelo = true;
        $bolBlocoComComportamento = false;
        $bolBlocoUnico = false;
        $arrBlocoGrid = $arrDocAuto;
        require 'md_pet_pi_tp_processo_cadastro_bloco_doc.php';
        ?>

        <div class="row ml-0">
            <div class="col-sm-12 col-md-12 col-lg-12">
                <div class="form-group">
                    <label id="lblOrientacoesPosPet" for="txaOrientacoesPosPet" class="infraLabelOpcional">Orientações exibidas ao Usuário pós Peticionamento:
                        <img align="top" class="infraImg" name="ajuda"
                             src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/ajuda.svg"
                             <?= PaginaSEI::montarTitleTooltip('Texto exibido na modal de Confirmação do Peticionamento Interno, quando o Usuário clicar em Peticionar.', 'Ajuda') ?> />
                    </label>
                    <?php if ($staTipoEditor == EditorRN::$VE_CK5) { ?>
                        <div id="divEditorPosPet" class="infra-editor" style="visibility: visible;">
                            <?= $objEditorPosPetDTO->getStrHtml(); ?>
                        </div>
                    <?php } else { ?>
                        <div id="divEditorPosPet" class="mb-0">
                            <textarea id="txaOrientacoesPosPet" name="txaOrientacoesPosPet" rows="6" class="infraTextarea"
                                      tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"><?= PaginaSEI::tratarHTML($strOrientacoesPosPet) ?></textarea>
                            <script type="text/javascript"> <?= $objEditorPosPetDTO->getStrEditores(); ?> </script>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>

    </div>

    <input type="hidden" id="hdnSubmetido" name="hdnSubmetido" value="1"/>
    <input type="hidden" id="hdnGridDocPrinc" name="hdnGridDocPrinc" value="<?= PaginaSEI::tratarHTML(MdPetPiTpProcessoINT::codificarGrid($arrDocPrinc)) ?>"/>
    <input type="hidden" id="hdnGridAnexoObrig" name="hdnGridAnexoObrig" value="<?= PaginaSEI::tratarHTML(MdPetPiTpProcessoINT::codificarGrid($arrAnexoObrig)) ?>"/>
    <input type="hidden" id="hdnGridDocAuto" name="hdnGridDocAuto" value="<?= PaginaSEI::tratarHTML(MdPetPiTpProcessoINT::codificarGrid($arrDocAuto)) ?>"/>

    <?php
    PaginaSEI::getInstance()->fecharAreaDados();
    PaginaSEI::getInstance()->montarBarraComandosInferior($arrComandos);
    ?>

</form>
<?php
PaginaSEI::getInstance()->fecharBody();
PaginaSEI::getInstance()->fecharHtml();
require_once 'md_pet_pi_tp_processo_cadastro_js.php';
