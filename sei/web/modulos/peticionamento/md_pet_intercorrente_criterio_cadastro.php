<?

try {
    require_once dirname(__FILE__) . '/../../SEI.php';

    session_start();

    SessaoSEI::getInstance()->validarLink();

    require_once 'md_pet_intercorrente_criterio_cadastro_inicializar.php';

    $strItensSelHipoteseLegal = MdPetTipoProcessoINT::montarSelectHipoteseLegal(null, null, ProtocoloRN::$NA_RESTRITO);
    $optionTemplate = '<option value="%s" %s>%s</option>';
    $arrNivelAcesso = array(
        'P' => 'Público',
        'I' => 'Restrito'
    );
    foreach ($arrNivelAcesso as $i => $nivelAcesso) {
        $selected = '';
        $strItensSelNivelAcesso .= sprintf($optionTemplate, $i, $selected, $nivelAcesso);
    }

    $objInfraParametroDTO = new InfraParametroDTO();
    $objInfraParametroDTO->retTodos();
    $objInfraParametroDTO->setStrNome('SEI_HABILITAR_HIPOTESE_LEGAL');
    $objMdPetParametroRN = new MdPetParametroRN();
    $objInfraParametroDTO = $objMdPetParametroRN->consultar($objInfraParametroDTO);
    $valorParametroHipoteseLegal = $objInfraParametroDTO->getStrValor();

    if (in_array($_GET['acao'], array('md_pet_intercorrente_criterio_cadastrar', 'md_pet_intercorrente_criterio_consultar', 'md_pet_intercorrente_criterio_alterar'))) {

        if (isset($_REQUEST['id_criterio_intercorrente_peticionamento']) || isset($_POST['hdnIdTipoProcesso'])) {
            if (isset($_REQUEST['id_criterio_intercorrente_peticionamento'])) {
                $alterar = true;
                $objMdPetCriterioDTO = new MdPetCriterioDTO();
                $objMdPetCriterioDTO->setNumIdCriterioIntercorrentePeticionamento($_GET['id_criterio_intercorrente_peticionamento']);
                $objMdPetCriterioDTO->retStrNomeProcesso();
                $objMdPetCriterioDTO->retTodos(true);

                $objMdPetCriterioRN = new MdPetCriterioRN();
                $objMdPetCriterioDTO = $objMdPetCriterioRN->consultar($objMdPetCriterioDTO);
                $IdCriterioIntercorrentePeticionamento = $_REQUEST['id_criterio_intercorrente_peticionamento'];
                $nomeTipoProcesso = $objMdPetCriterioDTO->getStrNomeProcesso();
                $idTipoProcesso = $objMdPetCriterioDTO->getNumIdTipoProcedimento();
            } else {
                if (isset($_POST['hdnIdTipoProcesso'])) {

                    $objMdPetCriterioDTO = new MdPetCriterioDTO();
                    $objMdPetCriterioDTO->setStrStaNivelAcesso($_POST['rdNivelAcesso'][0]);
                    $objMdPetCriterioDTO->setNumIdHipoteseLegal($_POST['selHipoteseLegal']);

                    if (isset($_POST['selNivelAcesso']) && !empty($_POST['selNivelAcesso']) && $_POST['rdNivelAcesso'][0] == '2') {
                        $strStaTipoNivelAcesso = $_POST['selNivelAcesso'];
                        if ($_POST['selNivelAcesso'] == 'I') {
                            $objMdPetCriterioDTO->setNumIdHipoteseLegal($_POST['selHipoteseLegal']);
                        }
                        $objMdPetCriterioDTO->setStrStaTipoNivelAcesso($strStaTipoNivelAcesso);
                    }

                    $idTipoProcesso = $_POST['hdnIdTipoProcesso'];
                    $arrHdnIdTipoProcesso = PaginaSEI::getInstance()->getArrItensTabelaDinamica($_POST['hdnIdTipoProcesso']);
                    foreach ($arrHdnIdTipoProcesso as $tipoProcesso) {
                        $strTipoProcesso .= sprintf($optionTemplate, $tipoProcesso[0], 'selected="selected"', $tipoProcesso[1]);
                    }
                }
            }

            $sinNAUsuExt = $objMdPetCriterioDTO->getStrStaNivelAcesso() == 1 ? 'checked = checked' : '';
            $sinNAPadrao = $objMdPetCriterioDTO->getStrStaNivelAcesso() == 2 ? 'checked = checked' : '';
            if ($objMdPetCriterioDTO->getStrStaNivelAcesso() == 2) {
                $hipoteseLegal = $objMdPetCriterioDTO->getStrStaTipoNivelAcesso() === 'I' && $valorParametroHipoteseLegal != '0' ? 'style="display:block"' : 'style="display:none"';

            }

            $strItensSelHipoteseLegal = MdPetTipoProcessoINT::montarSelectHipoteseLegal(null, null, $objMdPetCriterioDTO->getNumIdHipoteseLegal());
            $strItensSelNivelAcesso = '';
            foreach ($arrNivelAcesso as $i => $nivelAcesso) {
                $selected = '';
                if ($objMdPetCriterioDTO->getStrStaNivelAcesso() == '2') {
                    $selected = ($i == $objMdPetCriterioDTO->getStrStaTipoNivelAcesso()) ? ' selected="selected" ' : '';
                }
                $strItensSelNivelAcesso .= sprintf($optionTemplate, $i, $selected, $nivelAcesso);
            }
        }
    }

    switch ($_GET['acao']) {
        case 'md_pet_intercorrente_criterio_consultar':
            $strTitulo = 'Consultar Critério Intercorrente';
            $arrComandos[] = '<button type="button" accesskey="c" name="btnCancelar" id="btnCancelar" value="Fechar" onclick="location.href=\'' . PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . PaginaSEI::getInstance()->getAcaoRetorno() . '&acao_origem=' . $_GET['acao'] . PaginaSEI::getInstance()->montarAncora($_GET['id_criterio_intercorrente_peticionamento']))) . '\';" class="infraButton">Fe<span class="infraTeclaAtalho">c</span>har</button>';
            break;
        case 'md_pet_intercorrente_criterio_alterar':
            $strTitulo = 'Alterar Critério para Intercorrente';
            $arrComandos[] = '<button type="submit" accesskey="s" name="sbmAlterarCriterio" id="sbmAlterarCriterio" value="Salvar" class="infraButton"><span class="infraTeclaAtalho">S</span>alvar</button>';
            $arrComandos[] = '<button type="button" accesskey="c" name="btnCancelar" id="btnCancelar" value="Cancelar" onclick="location.href=\'' . PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . PaginaSEI::getInstance()->getAcaoRetorno() . '&acao_origem=' . $_GET['acao'] . PaginaSEI::getInstance()->montarAncora($_GET['id_criterio_intercorrente_peticionamento']))) . '\';" class="infraButton"><span class="infraTeclaAtalho">C</span>ancelar</button>';

            if (isset($_POST['sbmAlterarCriterio'])) {
                $objMdPetCriterioDTO = new MdPetCriterioDTO();
                $objMdPetCriterioDTO->setStrStaNivelAcesso($_POST['rdNivelAcesso'][0]);
                $objMdPetCriterioDTO->setNumIdHipoteseLegal($_POST['selHipoteseLegal']);
                $objMdPetCriterioDTO->setNumIdTipoProcedimento($_POST['hdnIdTipoProcesso']);
                $objMdPetCriterioDTO->setStrSinCriterioPadrao('N');
                $objMdPetCriterioDTO->setNumIdCriterioIntercorrentePeticionamento($_POST['hdnIdCriterioIntercorrentePeticionamento']);
                if (isset($_POST['selNivelAcesso'])) {
                    $objMdPetCriterioDTO->setStrStaTipoNivelAcesso($_POST['selNivelAcesso']);
                }
                $objMdPetCriterioRN = new MdPetCriterioRN();
                $objMdPetCriterioRN->alterar($objMdPetCriterioDTO);
                header('Location: ' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . PaginaSEI::getInstance()->getAcaoRetorno() . '&acao_origem=' . $_GET['acao'] . '&id_criterio_intercorrente_peticionamento=' . $objMdPetCriterioDTO->getNumIdCriterioIntercorrentePeticionamento() . PaginaSEI::getInstance()->montarAncora($objMdPetCriterioDTO->getNumIdCriterioIntercorrentePeticionamento())));
            }
            break;
        case 'md_pet_intercorrente_criterio_cadastrar':
        case 'md_pet_intercorrente_criterio_padrao':
            $arrComandos[] = '<button type="submit" accesskey="s" name="sbmCadastrarTpProcessoPeticionamento" id="sbmCadastrarTpProcessoPeticionamento" value="Salvar" class="infraButton"><span class="infraTeclaAtalho">S</span>alvar</button>';
            $arrComandos[] = '<button type="button" accesskey="c" name="btnCancelar" id="btnCancelar" value="Cancelar" onclick="location.href=\'' . PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . PaginaSEI::getInstance()->getAcaoRetorno() . '&acao_origem=' . $_GET['acao'])) . '\';" class="infraButton"><span class="infraTeclaAtalho">C</span>ancelar</button>';

            // cadastrando o critério intercorrente
            $strTitulo = 'Novo Critério para Intercorrente';
            if ($_GET['acao'] == 'md_pet_intercorrente_criterio_padrao') {
                $strTitulo = 'Intercorrente Padrão';
            }

            if (isset($_POST['sbmCadastrarTpProcessoPeticionamento'])) {
                $objMdPetCriterioDTO = new MdPetCriterioDTO();
                $objMdPetCriterioDTO->setStrStaNivelAcesso($_POST['rdNivelAcesso'][0]);
                $objMdPetCriterioDTO->setNumIdHipoteseLegal($_POST['selHipoteseLegal']);
                $objMdPetCriterioDTO->setNumIdCriterioIntercorrentePeticionamento('');
                $objMdPetCriterioDTO->setStrSinAtivo('S');

                if (isset($_POST['selNivelAcesso']) && !empty($_POST['selNivelAcesso']) && $_POST['rdNivelAcesso'][0] == '2') {
                    $strStaTipoNivelAcesso = $_POST['selNivelAcesso'];
                    if ($_POST['selNivelAcesso'] == 'I') {
                        $objMdPetCriterioDTO->setNumIdHipoteseLegal($_POST['selHipoteseLegal']);
                    }
                    $objMdPetCriterioDTO->setStrStaTipoNivelAcesso($strStaTipoNivelAcesso);
                }

                $objMdPetCriterioRN = new MdPetCriterioRN();
                if ($_GET['acao'] == 'md_pet_intercorrente_criterio_padrao') {
                    $objMdPetCriterioRN->cadastrarPadrao($objMdPetCriterioDTO);
                } else {
                    if (empty($_POST['hdnIdTipoProcesso'])) {
                        $objMdPetCriterioDTO->setNumIdTipoProcedimento($_POST['txtTipoProcesso']);
                        $objMdPetCriterioRN->cadastrar($objMdPetCriterioDTO);
                    } else {
                        $arrTipoProcesso = PaginaSEI::getInstance()->getArrValuesSelect($_POST['hdnIdTipoProcesso']);
                        foreach ($arrTipoProcesso as $numTipoProcesso) {
                            $objMdPetCriterioDTO->setNumIdTipoProcedimento($numTipoProcesso);
                            $objMdPetCriterioRN->cadastrar($objMdPetCriterioDTO);
                        }
                    }
                }
                header('Location: ' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . PaginaSEI::getInstance()->getAcaoRetorno() . '&acao_origem=' . $_GET['acao']));
            }
            break;
        default:
            throw new InfraException("Ação '" . $_GET['acao'] . "' não reconhecidas.");
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
require_once 'md_pet_intercorrente_criterio_cadastro_css.php';
PaginaSEI::getInstance()->fecharStyle();
PaginaSEI::getInstance()->montarJavaScript();
PaginaSEI::getInstance()->abrirJavaScript();
//javascript
PaginaSEI::getInstance()->fecharJavaScript();
PaginaSEI::getInstance()->fecharHead();
PaginaSEI::getInstance()->abrirBody($strTitulo, 'onload="inicializar();"');
?>
<form id="frmCriterioCadastro" method="post" onsubmit="return OnSubmitForm();"
      action="<?= PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . $_GET['acao'] . '&acao_origem=' . $_GET['acao'])) ?>">
    <?
    PaginaSEI::getInstance()->montarBarraComandosSuperior($arrComandos);
    PaginaSEI::getInstance()->abrirAreaDados('98%');
    ?>
    <!--  Tipo de Processo  -->
    <div class="infraAreaDados" id="divInfraAreaDados">
    <div class="row">
            <div class="col-sm-12 col-md-10 col-lg-8">
                <div class="form-group">
                    <label id="lblTipoProcesso" for="txtTipoProcesso" class="infraLabelObrigatorio">
                        Tipo de Processos:
                    </label>
                    <div class="input-group mb-3">
                        <input type="text" onchange="removerProcessoAssociado(0);" id="txtTipoProcesso"
                            name="txtTipoProcesso"
                            class="infraText form-control"
                            value="<?php echo PaginaSEI::tratarHTML($nomeTipoProcesso); ?>"
                            tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
                        <input type="hidden" id="hdnIdTipoProcesso" name="hdnIdTipoProcesso"
                            value="<?php echo $idTipoProcesso ?>"/>
                        <input type="hidden" id="hdnIdMdPetTipoProcesso" name="hdnIdMdPetTipoProcesso"
                            value="<?php echo $idMdPetTipoProcesso ?>"/>
                        <img id="imgLupaTipoProcesso" onclick="objLupaTipoProcesso.selecionar(700, 500);"
                            onkeypress="objLupaTipoProcesso.selecionar(700, 500);"
                            src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/pesquisar.svg"
                            alt="Selecionar Tipo de Processo" title="Selecionar Tipo de Processo"
                            tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
                        <img id="imgExcluirTipoProcesso"
                            onclick="removerProcessoAssociado(0);objLupaTipoProcesso.remover();"
                            onkeypress="removerProcessoAssociado(0);objLupaTipoProcesso.remover();"
                            src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/remover.svg"
                            alt="Remover Tipo de Processo" title="Remover Tipo de Processo"
                            tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
                    </div>
                </div>
            </div>
        </div>
        <!--  Fim do Tipo de Processo -->
        <div class="row rowFieldSet2 rowFieldSet">
            <div class="col-sm-12 col-md-10 col-lg-8 col-xl-8">
                <fieldset class="infraFieldset form-control">
                    <legend class="infraLegend">&nbsp;Nível de Acesso dos Documentos&nbsp;</legend>
                    <div class="form-group">
                        <input <?php echo $sinNAUsuExt; ?> type="radio" name="rdNivelAcesso[]"
                                                        id="rdUsuExternoIndicarEntrePermitidos" class="infraRadio"
                                                        onclick="changeNivelAcesso();" value="1"
                                                        tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>">
                        <label for="rdUsuExternoIndicarEntrePermitidos" id="lblUsuExterno" class="infraLabelRadio">Usuário
                            Externo indica diretamente</label><br/>

                        <input <?php echo $sinNAPadrao; ?> type="radio" name="rdNivelAcesso[]" id="rdPadrao"
                                                        onclick="changeNivelAcesso();" value="2" class="infraRadio"
                                                        tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>">
                        <label name="lblPadrao" id="lblPadrao" for="rdPadrao" class="infraLabelRadio">Padrão pré
                            definido</label>
                    </div>
                    <div class="row" id="divNivelAcesso" <?php echo $sinNAPadrao != '' ? 'style="display: flex;"' : 'style="display: none;"' ?>>
                        <div class="col-sm-12 col-md-12 col-lg-3 col-xl-3">
                            <div class="form-group">
                                <label name="lblNivelAcesso" id="lblNivelAcesso" for="selNivelAcesso"
                                    class="infraLabelObrigatorio">Nível de Acesso: </label><br/>
                                <select id="selNivelAcesso" name="selNivelAcesso" onchange="changeSelectNivelAcesso()"
                                        class="infraSelect form-control"
                                        tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>">
                                    <?= $strItensSelNivelAcesso ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-12 col-lg-9 col-xl-9"  id="divHipoteseLegal" <?php echo $hipoteseLegal ?>>
                            <div class="form-group">
                                <label name="lblHipoteseLegal" id="lblHipoteseLegal" for="selHipoteseLegal"
                                    class="infraLabelObrigatorio">Hipótese Legal:</label><br/>
                                <select id="selHipoteseLegal" name="selHipoteseLegal" class="infraSelect form-control"
                                        tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>">
                                    <?= $strItensSelHipoteseLegal ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </fieldset>
            </div>
        </div>
        <div id="dovHidden">
            <input type="hidden" name="hdnParametroHipoteseLegal" id="hdnParametroHipoteseLegal"
                   value="<?php echo $valorParametroHipoteseLegal; ?>"/>
            <input type="hidden" id="hdnIdCriterioIntercorrentePeticionamento"
                   name="hdnIdCriterioIntercorrentePeticionamento"
                   value="<?php echo $IdCriterioIntercorrentePeticionamento ?>"/>
        </div>
    </div>

<?
PaginaSEI::getInstance()->fecharAreaDados();
?>
</form>
<?
PaginaSEI::getInstance()->fecharBody();
PaginaSEI::getInstance()->fecharHtml();
require_once 'md_pet_intercorrente_criterio_cadastro_js.php';
?>
