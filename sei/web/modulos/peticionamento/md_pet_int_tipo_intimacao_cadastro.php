<?
/**
 * TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
 *
 * 08/12/2016 - criado por Marcelo Bezerra - CAST
 *
 * Versão do Gerador de Código: 1.39.0
 *
 */
try {
    require_once dirname(__FILE__) . '/../../SEI.php';
    session_start();

    //////////////////////////////////////////////////////////////////////////////
//    InfraDebug::getInstance()->setBolLigado(false);
//    InfraDebug::getInstance()->setBolDebugInfra(true);
//    InfraDebug::getInstance()->limpar();
    //////////////////////////////////////////////////////////////////////////////

    SessaoSEI::getInstance()->validarLink();

    PaginaSEI::getInstance()->verificarSelecao('md_pet_int_tipo_intimacao_selecionar');

    SessaoSEI::getInstance()->validarPermissao($_GET['acao']);

    $objMdPetIntTipoIntimacaoDTO = new MdPetIntTipoIntimacaoDTO();
    $objMdPetIntRelIntimRespDTO = new MdPetIntRelIntimRespDTO();
    $objMdPetIntTipoRespDTO = new MdPetIntTipoRespDTO();

    $strDesabilitar = '';
    $arrComandos = array();
    $arrAcoes = array();

    switch ($_GET['acao']) {
        case 'md_pet_int_tipo_intimacao_cadastrar':
            $strTitulo = 'Novo Tipo de Intimação Eletrônica ';
            $arrComandos[] = '<button type="submit" accesskey="S" name="sbmCadastrarMdPetIntTipoIntimacao" value="Salvar" class="infraButton"><span class="infraTeclaAtalho">S</span>alvar</button>';
            $arrComandos[] = '<button type="button" accesskey="C" name="btnCancelar" id="btnCancelar" value="Cancelar" onclick="location.href=\'' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . PaginaSEI::getInstance()->getAcaoRetorno() . '&acao_origem=' . $_GET['acao']) . '\';" class="infraButton"><span class="infraTeclaAtalho">C</span>ancelar</button>';

            $objMdPetIntTipoIntimacaoDTO->setNumIdMdPetIntTipoIntimacao(null);
            $objMdPetIntTipoIntimacaoDTO->setStrNome($_POST['txtNome']);
            $objMdPetIntTipoIntimacaoDTO->setStrTipoRespostaAceita($_POST['rdoResposta']);
            $objMdPetIntTipoIntimacaoDTO->setStrSinAtivo('S');

            $arr = PaginaSEI::getInstance()->getArrValuesSelect($_POST['hdnTipoResposta']);
            $arrTiposResposta = array();

            $objMdPetIntTipoIntimacaoDTO->setArrObjRelIntRespostaDTO($arr);

            $strTipoResposta = $_POST['hdnTipoResposta'];
            $strEmailAcoes = 'false, true';
            if (isset($_POST['sbmCadastrarMdPetIntTipoIntimacao'])) {
                try {
                    $objMdPetIntTipoIntimacaoRN = new MdPetIntTipoIntimacaoRN();
                    $objMdPetIntTipoIntimacaoDTO = $objMdPetIntTipoIntimacaoRN->cadastrar($objMdPetIntTipoIntimacaoDTO);

                    PaginaSEI::getInstance()->adicionarMensagem(' "' . $objMdPetIntTipoIntimacaoDTO->getNumIdMdPetIntTipoIntimacao() . '" cadastrada com sucesso.');
                    header('Location: ' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . PaginaSEI::getInstance()->getAcaoRetorno() . '&acao_origem=' . $_GET['acao'] . '&id_md_pet_int_tipo_intimacao=' . $objMdPetIntTipoIntimacaoDTO->getNumIdMdPetIntTipoIntimacao() . PaginaSEI::getInstance()->montarAncora($objMdPetIntTipoIntimacaoDTO->getNumIdMdPetIntTipoIntimacao())));
                    die;
                } catch (Exception $e) {
                    PaginaSEI::getInstance()->processarExcecao($e);
                }
            }
            break;

        case 'md_pet_int_tipo_intimacao_alterar':
            $strTitulo = 'Alterar Tipo de Intimação Eletrônica';
            $arrComandos[] = '<button type="submit" accesskey="S" name="sbmAlterarMdPetIntTipoIntimacao" value="Salvar" class="infraButton"><span class="infraTeclaAtalho">S</span>alvar</button>';
            $strDesabilitar = 'disabled="disabled"';

            $arrMdPetIntRelIntimRespDTO = array();
            $optionTipoResposta = '';
            $strEmailAcoes = 'false, true';
            if (isset($_GET['id_md_pet_int_tipo_intimacao'])) {
                $objMdPetIntTipoIntimacaoDTO->setNumIdMdPetIntTipoIntimacao($_GET['id_md_pet_int_tipo_intimacao']);
                $objMdPetIntTipoIntimacaoDTO->retTodos();
                $objMdPetIntTipoIntimacaoRN = new MdPetIntTipoIntimacaoRN();
                $objMdPetIntTipoIntimacaoDTO = $objMdPetIntTipoIntimacaoRN->consultar($objMdPetIntTipoIntimacaoDTO);

                if ($objMdPetIntTipoIntimacaoDTO == null) {
                    throw new InfraException("Registro não encontrado.");
                }
            } else {
                $strTipoResposta = $_POST['hdnTipoResposta'];
                $objMdPetIntTipoIntimacaoDTO->setNumIdMdPetIntTipoIntimacao($_POST['hdnIdMdPetIntTipoIntimacao']);
                $objMdPetIntTipoIntimacaoDTO->setStrNome($_POST['txtNome']);
                $objMdPetIntTipoIntimacaoDTO->setStrTipoRespostaAceita($_POST['rdoResposta']);
                $objMdPetIntTipoIntimacaoDTO->setStrSinAtivo('S');

                $arr = PaginaSEI::getInstance()->getArrValuesSelect($_POST['hdnTipoResposta']);
                $arrTiposResposta = array();
                $objMdPetIntTipoIntimacaoDTO->setArrObjRelIntRespostaDTO($arr);

            }

            $arrComandos[] = '<button type="button" accesskey="C" name="btnCancelar" id="btnCancelar" value="Cancelar" onclick="location.href=\'' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . PaginaSEI::getInstance()->getAcaoRetorno() . '&acao_origem=' . $_GET['acao'] . PaginaSEI::getInstance()->montarAncora($objMdPetIntTipoIntimacaoDTO->getNumIdMdPetIntTipoIntimacao())) . '\';" class="infraButton"><span class="infraTeclaAtalho">C</span>ancelar</button>';

            $strTipoResposta = $_POST['hdnTipoResposta'];
            if (isset($_POST['sbmAlterarMdPetIntTipoIntimacao'])) {
                try {
                    $objMdPetIntTipoIntimacaoRN = new MdPetIntTipoIntimacaoRN();
                    $objMdPetIntTipoIntimacaoRN->alterar($objMdPetIntTipoIntimacaoDTO);
                    PaginaSEI::getInstance()->adicionarMensagem(' "' . $objMdPetIntTipoIntimacaoDTO->getNumIdMdPetIntTipoIntimacao() . '" alterad com sucesso.');
                    header('Location: ' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . PaginaSEI::getInstance()->getAcaoRetorno() . '&acao_origem=' . $_GET['acao'] . PaginaSEI::getInstance()->montarAncora($objMdPetIntTipoIntimacaoDTO->getNumIdMdPetIntTipoIntimacao())));
                    die;
                } catch (Exception $e) {
                    PaginaSEI::getInstance()->processarExcecao($e);
                }
            }
            break;

        case 'md_pet_int_tipo_intimacao_consultar':
            $strTitulo = 'Consultar Tipo de Intimação Eletrônica';
            $arrComandos[] = '<button type="button" accesskey="C" name="btnFechar" value="Fechar" onclick="location.href=\'' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . PaginaSEI::getInstance()->getAcaoRetorno() . '&acao_origem=' . $_GET['acao'] . PaginaSEI::getInstance()->montarAncora($_GET['id_md_pet_int_tipo_intimacao'])) . '\';" class="infraButton">Fe<span class="infraTeclaAtalho">c</span>har</button>';
            $objMdPetIntTipoIntimacaoDTO->setNumIdMdPetIntTipoIntimacao($_GET['id_md_pet_int_tipo_intimacao']);
            $objMdPetIntTipoIntimacaoDTO->setBolExclusaoLogica(false);
            $objMdPetIntTipoIntimacaoDTO->retTodos();
            $objMdPetIntTipoIntimacaoRN = new MdPetIntTipoIntimacaoRN();
            $objMdPetIntTipoIntimacaoDTO = $objMdPetIntTipoIntimacaoRN->consultar($objMdPetIntTipoIntimacaoDTO);
            $strEmailAcoes = 'false, false';
            if ($objMdPetIntTipoIntimacaoDTO === null) {
                throw new InfraException("Registro não encontrado.");
            }
            break;

        default:
            throw new InfraException("Ação '" . $_GET['acao'] . "' não reconhecida.");
    }

    if ($_GET['acao'] === 'md_pet_int_tipo_intimacao_alterar' || $_GET['acao'] === 'md_pet_int_tipo_intimacao_consultar') {
        $objMdPetIntRelTipoRespRN = new MdPetIntRelTipoRespRN();
        $objMdPetIntRelIntimRespRN = new MdPetIntRelIntimRespRN();

        $objMdPetIntRelIntimRespDTO->setNumIdMdPetIntTipoIntimacao($_GET['id_md_pet_int_tipo_intimacao']);
        $objMdPetIntRelIntimRespDTO->retTodos(true);
        $objMdPetIntRelIntimRespDTO->setOrdStrNomeMdPetIntTipoResp(InfraDTO::$TIPO_ORDENACAO_ASC);
        $arrMdPetIntRelIntimRespDTO = $objMdPetIntRelIntimRespRN->listar($objMdPetIntRelIntimRespDTO);

        $arrTiposResposta = array();
        foreach ($arrMdPetIntRelIntimRespDTO as $arrDados) {
            if ($arrDados->getStrTipoPrazoExternoMdPetIntTipoResp() == 'N') {
                $prazo = 'Não Possui Prazo Externo';
            } else if ($arrDados->getStrTipoPrazoExternoMdPetIntTipoResp() == 'D') {
                $tipoDia = null;
                if ($arrDados->getStrTipoDia() == 'U') {
                    $tipoDia = 'Útil';
                    if ($arrDados->getNumValorPrazoExternoMdPetIntTipoResp() > 1) {
                        $tipoDia = 'Úteis';
                    }
                }
                $prazo = $arrDados->getNumValorPrazoExternoMdPetIntTipoResp() . ' Dias ' . $tipoDia;
            } else if ($arrDados->getStrTipoPrazoExternoMdPetIntTipoResp() == 'M') {
                $prazo = $arrDados->getNumValorPrazoExternoMdPetIntTipoResp() . ' Meses';
            } else if ($arrDados->getStrTipoPrazoExternoMdPetIntTipoResp() == 'A') {
                $prazo = $arrDados->getNumValorPrazoExternoMdPetIntTipoResp() . ' Anos';
            }

            if ($arrDados->getStrTipoRespostaAceitaMdPetIntTipoResp() == 'E') {
                $resposta = 'Exige Resposta';
            } else {
                $resposta = 'Resposta Facultativa';
            }

            $isVinculado = $objMdPetIntRelTipoRespRN->validarExclusaoTipoResposta($arrDados->getNumIdMdPetIntTipoRespMdPetIntTipoResp());

            $arrTiposResposta[] = array($arrDados->getNumIdMdPetIntTipoRespMdPetIntTipoResp(), $isVinculado, PaginaSEI::getInstance()->formatarParametrosJavaScript(PaginaSEI::tratarHTML($arrDados->getStrNomeMdPetIntTipoResp())), $prazo, $resposta);
        }

        if (isset($_GET['id_md_pet_int_tipo_intimacao'])) {
            $strTipoResposta = PaginaSEI::getInstance()->gerarItensTabelaDinamica($arrTiposResposta);
        }

        $strItenSelPrazoExterno = MdPetIntTipoRespINT::montarSelectTipoRespostaEU8612($objMdPetIntTipoIntimacaoDTO->getStrTipoRespostaAceita());
    }
    $strLinkAjaxTipoResposta = SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=integracao_tipo_resposta');
    $strUrlBuscaTipoResposta = SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=busca_tipo_resposta');

} catch (Exception $e) {
    PaginaSEI::getInstance()->processarExcecao($e);
}

PaginaSEI::getInstance()->montarDocType();
PaginaSEI::getInstance()->abrirHtml();
PaginaSEI::getInstance()->abrirHead();
PaginaSEI::getInstance()->montarMeta();
PaginaSEI::getInstance()->montarTitle(PaginaSEI::getInstance()->getStrNomeSistema() . ' - ' . $strTitulo);
PaginaSEI::getInstance()->montarStyle();
PaginaSEI::getInstance()->abrirStyle();
require_once("md_pet_int_tipo_intimacao_cadastro_css.php");
PaginaSEI::getInstance()->fecharStyle();
PaginaSEI::getInstance()->montarJavaScript();
PaginaSEI::getInstance()->abrirJavaScript();
PaginaSEI::getInstance()->fecharJavaScript();
PaginaSEI::getInstance()->fecharHead();
PaginaSEI::getInstance()->abrirBody($strTitulo, 'onload="inicializar();"');
?>
    <form id="frmMdPetIntTipoIntimacaoCadastro" method="post"
          action="<?= SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . $_GET['acao'] . '&acao_origem=' . $_GET['acao']) ?>"
          onsubmit="return OnSubmitForm();">

        <? PaginaSEI::getInstance()->montarBarraComandosSuperior($arrComandos);
        PaginaSEI::getInstance()->abrirAreaDados('4em'); ?>
        <div class="row">
            <div class="col-sm-12 col-md-8 col-lg-8 col-xl-6">
                <label id="lblNome" for="txtNome" accesskey="" class="infraLabelObrigatorio">Nome:
                    <img align="top" src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/ajuda.svg?<?= Icone::VERSAO ?>"
                         name="ajuda" <?= PaginaSEI::montarTitleTooltip('Escrever nome que reflita o documento ou decisão que motiva a intimação e não a possível resposta do Usuário Externo. \n \n Exemplos: Descisão de 1ª Instância, Decisão de Inadmissibilidade de Recurso, Exigência para Complementação de Informações, Decisão sobre Recurso.', 'Ajuda') ?>
                         class="infraImgModulo"/></label>
                <input type="text" id="txtNome" name="txtNome" class="infraTex form-control"
                       value="<?= PaginaSEI::tratarHTML($objMdPetIntTipoIntimacaoDTO->getStrNome()); ?>"
                       onkeypress="return infraMascaraTexto(this,event,70);" maxlength="70"
                       tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12 col-md-8 col-lg-8 col-xl-6">
                <fieldset id="fldResposta" class="form-control fieldsetTipoResposta" style="height: auto">
                    <legend class="infraLegend"> Tipo de Intimação Aceita Tipo de Resposta</legend>
                    <div id="divOptAno" class="infraDivRadio">
                <span id="spnAno"><label id="lblAno" class="infraLabelRadio">
                    <input type="radio" onclick="esconderTabelaTipoResposta()" name="rdoResposta"
                           id="optTipoRespostaFacultativa"
                           value="F" <?= ($objMdPetIntTipoIntimacaoDTO->getStrTipoRespostaAceita() === 'F' ? 'checked="checked"' : '') ?> class="infraRadio"
                           tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>Facultativa</label>
                </span>
                    </div>
                    <div id="divOptAno" class="infraDivRadio">
                <span id="spnExige"><label id="lblExige" class="infraLabelRadio">
                    <input type="radio" onclick="esconderTabelaTipoResposta()" name="rdoResposta"
                           id="optTipoRespostaExige"
                           value="E" <?= ($objMdPetIntTipoIntimacaoDTO->getStrTipoRespostaAceita() === 'E' ? 'checked="checked"' : '') ?> class="infraRadio"
                           tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>Exige Resposta</label>
                </span> <br>
                    </div>
                    <div id="divOptAno" class="infraDivRadio">
                <span id="spnExige"><label id="lblExige" class="infraLabelRadio">
                    <input type="radio" onclick="esconderTabelaTipoResposta()" name="rdoResposta"
                           id="optTipoSemResposta"
                           value="S" <?= ($objMdPetIntTipoIntimacaoDTO->getStrTipoRespostaAceita() === 'S' ? 'checked="checked"' : '') ?> class="infraRadio"
                           tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>Sem Resposta</label>
                </span> <br>
                    </div>
                </fieldset>
            </div>
        </div>
        <div class="row" id="divInfraAreaDados2">
            <div class="col-sm-12 col-md-8 col-lg-8 col-xl-6">
                <label id="lblTipoResposta" for="txtTipoResposta" accesskey="" class="infraLabelObrigatorio">Tipos de
                    Resposta:
                    <img align="top"
                         src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/ajuda.svg?<?= Icone::VERSAO ?>"
                         name="ajuda" <?= PaginaSEI::montarTitleTooltip('É possível indicar mais de um Tipo de Resposta com Resposta Facultativa pelo Usuário Externo. \n \n Somente é possível indicar um Tipo de Resposta que Exige Resposta pelo Usuário Externo.', 'Ajuda') ?>
                         class="infraImgModulo"/></label>
                <div class="input-group mb-3">
                    <select id="selTipoResposta" name="selTipoResposta" class="infraSelect form-control"
                            tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>">
                        <option id=""></option> <?= $strItenSelPrazoExterno ?> </select>
                    <button type="button" accesskey="A" name="sbmGravarTipoResposta" id="sbmGravarTipoResposta"
                            value="Adicionar Tipo Resposta" onclick="transportarTipoResposta();"
                            class="infraButton"><span
                                class="infraTeclaAtalho">A</span>dicionar
                    </button>
                    <input type="hidden" id="hdnIdTipoResposta" name="hdnIdTipoResposta" value=""/>
                </div>
            </div>
        </div>
        <div class="row" id="divTabelaTipoResposta">
            <div class="col-sm-12 col-md-12 col-lg-12 col-xl-10">
                <table id="tblTipoResposta" width="85%" class="infraTable" summary="Lista de Tipos de Respostas">
                    <caption class="infraCaption"> Lista de Tipos de Respostas</caption>
                    <tr>
                        <th style="display:none;">ID</th>
                        <th style="display:none;">VINCULADO</th>
                        <th class="infraTh" width="60%">Tipo de Resposta</th>
                        <th class="infraTh" width="20px">Prazo Externo</th>
                        <th class="infraTh" width="25px">Resposta do Usuário Externo</th>
                        <th class="infraTh" width="15px">Ações</th>
                    </tr>
                </table>
                <input type="hidden" id="hdnIdTipoResposta" name="hdnIdTipoResposta" value=""/>

                <input type="hidden" id="hdnTipoResposta" name="hdnTipoResposta" value="<?= $strTipoResposta; ?>"/>

            </div>
        </div>

        <? PaginaSEI::getInstance()->fecharAreaDados(); ?>
        <input type="hidden" id="hdnIdMdPetIntTipoIntimacao" name="hdnIdMdPetIntTipoIntimacao"
               value="<?= $objMdPetIntTipoIntimacaoDTO->getNumIdMdPetIntTipoIntimacao(); ?>"/>
    </form>
<?
require_once "md_pet_int_tipo_intimacao_cadastro_js.php";
PaginaSEI::getInstance()->fecharBody();
PaginaSEI::getInstance()->fecharHtml();
?>