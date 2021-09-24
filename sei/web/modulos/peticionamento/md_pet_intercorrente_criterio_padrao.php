<?
/**
 * ANATEL
 *
 * 15/04/2016 - criado por jaqueline.mendes@cast.com.br - CAST
 *
 */

try {
    require_once dirname(__FILE__) . '/../../SEI.php';

    session_start();

    SessaoSEI::getInstance()->validarLink();

    $strDesabilitar = '';

    $arrComandos = array();
    //Tipo Processo - Nivel de Acesso
    $strLinkAjaxNivelAcesso = SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=md_pet_tipo_processo_nivel_acesso_auto_completar');

    //Tipo Processo
    $strLinkTipoProcessoSelecao = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=tipo_procedimento_selecionar&tipo_selecao=1&id_object=objLupaTipoProcesso');
    $strLinkAjaxTipoProcesso = SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=md_pet_intercorrente_tipo_processo_auto_completar');

    //Preparar Preenchimento Alteração
    $idMdPetTipoProcesso = '';
    $nomeTipoProcesso = '';
    $idTipoProcesso = '';
    $orientacoes = '';
    $idUnidade = '';
    $nomeUnidade = '';
    $sinIndIntUsExt = '';
    $sinIndIntIndIndir = '';
    $sinIndIntIndConta = '';
    $sinIndIntIndCpfCn = '';
    $sinNAUsuExt = '';
    $sinNAPadrao = '';
    $hipoteseLegal = 'style="display:none;"';
    $gerado = '';
    $externo = '';
    $nomeSerie = '';
    $idSerie = '';
    $strItensSelSeries = '';
    $unica = false;
    $mutipla = false;
    $arrObjUnidadesMultiplas = array();
    $alterar = false;

    $strItensSelNivelAcesso = '';
    $strItensSelHipoteseLegal = '';

    $objInfraParametroDTO = new InfraParametroDTO();
    $objMdPetParametroRN = new MdPetParametroRN();
    $objInfraParametroDTO->retTodos();
    $objInfraParametroDTO->setStrNome('SEI_HABILITAR_HIPOTESE_LEGAL');
    $objInfraParametroDTO = $objMdPetParametroRN->consultar($objInfraParametroDTO);
    $valorParametroHipoteseLegal = $objInfraParametroDTO->getStrValor();

    switch ($_GET['acao']) {
        case 'md_pet_intercorrente_criterio_padrao':
            $strTitulo = 'Intercorrente Padrão';

            $arrComandos[] = '<button type="submit" accesskey="s" name="sbmCadastrarTpProcessoPeticionamento" id="sbmCadastrarTpProcessoPeticionamento" value="Salvar" class="infraButton"><span class="infraTeclaAtalho">S</span>alvar</button>';
            $arrComandos[] = '<button type="button" accesskey="c" name="btnCancelar" id="btnCancelar" value="Cancelar" onclick="location.href=\'' . PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . PaginaSEI::getInstance()->getAcaoRetorno() . '&acao_origem=' . $_GET['acao'])) . '\';" class="infraButton"><span class="infraTeclaAtalho">C</span>ancelar</button>';

            // cadastrando o critério intercorrente
            if (isset($_POST['sbmCadastrarTpProcessoPeticionamento'])) {
                $objMdPetCriterioDTO = new MdPetCriterioDTO();
                $objMdPetCriterioDTO->setNumIdTipoProcedimento($_POST['hdnIdTipoProcesso']);
                $objMdPetCriterioDTO->setStrStaNivelAcesso($_POST['rdNivelAcesso'][0]);
                $objMdPetCriterioDTO->setStrSinAtivo($_POST['rdSinAtivo'][0]);

                if (isset($_POST['selNivelAcesso']) && !empty($_POST['selNivelAcesso']) && $_POST['rdNivelAcesso'][0] == '2') {
                    $strStaTipoNivelAcesso = $_POST['selNivelAcesso'];
                    if ($_POST['selNivelAcesso'] == 'I') {
                        $objMdPetCriterioDTO->setNumIdHipoteseLegal($_POST['selHipoteseLegal']);
                    }
                    $objMdPetCriterioDTO->setStrStaTipoNivelAcesso($strStaTipoNivelAcesso);
                }

                $objMdPetCriterioRN = new MdPetCriterioRN();
                $objMdPetCriterioRN->cadastrarPadrao($objMdPetCriterioDTO);

                header('Location: ' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . PaginaSEI::getInstance()->getAcaoRetorno() . '&acao_origem=' . $_GET['acao']));
                die;
            } else {
                $objCriterioIntercorrentePadraoConsultaDTO = new MdPetCriterioDTO();
                $objCriterioIntercorrentePadraoConsultaDTO->setStrSinCriterioPadrao('S');
                $objCriterioIntercorrentePadraoConsultaDTO->retTodos(true);
                $objCriterioIntercorrentePadraoConsultaDTO->retStrNomeProcesso();
                $objMdPetCriterioRN = new MdPetCriterioRN();
                $objCriterioIntercorrentePadraoDTO = $objMdPetCriterioRN->consultar($objCriterioIntercorrentePadraoConsultaDTO);
                $strItensSelHipoteseLegal = MdPetTipoProcessoINT::montarSelectHipoteseLegal(null, null, null);

                //Carregando campos select
                $strItensSelTipoProcesso = MdPetTipoProcessoINT::montarSelectTipoProcesso(null, null, $_POST['selTipoProcesso']);

                if ($objCriterioIntercorrentePadraoDTO) {
                    $nomeTipoProcesso = $objCriterioIntercorrentePadraoDTO->getStrNomeProcesso();
                    $idTipoProcesso = $objCriterioIntercorrentePadraoDTO->getNumIdTipoProcedimento();
                    $sinCriterioPadrao = $objCriterioIntercorrentePadraoDTO->getStrSinCriterioPadrao() == 'S' ? 'checked = checked' : '';
                    $sinNAUsuExt = $objCriterioIntercorrentePadraoDTO->getStrStaNivelAcesso() == 1 ? 'checked = checked' : '';
                    $sinNAPadrao = $objCriterioIntercorrentePadraoDTO->getStrStaNivelAcesso() == 2 ? 'checked = checked' : '';
                    $sinAtivoSim = $objCriterioIntercorrentePadraoDTO->getStrSinAtivo() == 'S' ? 'checked = checked' : '';
                    $sinAtivoNao = $objCriterioIntercorrentePadraoDTO->getStrSinAtivo() == 'N' ? 'checked = checked' : '';
                    $hipoteseLegal = $objCriterioIntercorrentePadraoDTO->getStrStaTipoNivelAcesso() === 'I' && $valorParametroHipoteseLegal != '0' ? 'style="display:inherit"' : 'style="display:none"';
                    $strItensSelTipoProcesso = MdPetTipoProcessoINT::montarSelectTipoProcesso(null, null, $idTipoProcesso);
                    $strItensSelHipoteseLegal = MdPetTipoProcessoINT::montarSelectHipoteseLegal(null, null, $objCriterioIntercorrentePadraoDTO->getNumIdHipoteseLegal());
                    $nivelAcessoTemplate = '<option value="%s" %s>%s</option>';
                    $arrNivelAcesso = array(
                        '' => '',
                        'P' => 'Público',
                        'I' => 'Restrito'
                    );
                    $strTipoProcesso = sprintf($nivelAcessoTemplate, $idTipoProcesso, 'selected="selected"', $nomeTipoProcesso);

                    $strItensSelNivelAcesso = '';
                    foreach ($arrNivelAcesso as $i => $nivelAcesso) {
                        $selected = ($i == $objCriterioIntercorrentePadraoDTO->getStrStaTipoNivelAcesso()) ? ' selected="selected" ' : '';
                        $strItensSelNivelAcesso .= sprintf($nivelAcessoTemplate, $i, $selected, $nivelAcesso);
                    }

                }
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
require_once 'md_pet_intercorrente_criterio_padrao_css.php';
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

        <div class="infraAreaDados" id="divInfraAreaDados">
            <!--  Tipo de Processo  -->
            <div class="row">
                <div class="col-sm-12 col-md-10 col-lg-10 col-xl-6">
                    <label id="lblTipoProcesso" for="txtTipoProcesso" class="infraLabelObrigatorio">Tipo de
                        Processo: </label>
                    <div class="input-group mb-3" id="divIcones">
                        <input type="text" onchange="removerProcessoAssociado(0);" id="txtTipoProcesso"
                               name="txtTipoProcesso"
                               class="infraText form-control"
                               value="<?php echo PaginaSEI::tratarHTML($nomeTipoProcesso); ?>"
                               tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>

                        <img id="imgLupaTipoProcesso" onclick="objLupaTipoProcesso.selecionar(700,500);"
                             src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/pesquisar.svg"
                             alt="Selecionar Tipo de Processo"
                             title="Selecionar Tipo de Processo"
                             class="infraImg"/>
                        <img id="imgExcluirTipoProcesso"
                             onclick="removerProcessoAssociado(0);objLupaTipoProcesso.remover();"
                             src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/remover.svg"
                             alt="Remover Tipo de Processo"
                             title="Remover Tipo de Processo"
                             class="infraImginfraImgModulo"/>
                        <img id="imgAjuda" src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/ajuda.svg"
                             onmouseover="return infraTooltipMostrar('Apenas após a parametrização do Intercorrente Padrão é que os Usuários Externos passarão a visualizar o menu de Peticionamento Intercorrente. \n \n A abertura de Processo Novo Relacionado ao processo de fato indicado pelo Usuário Externo ocorrerá quando o processo indicado corresponder a: 1) Tipo de Processo sem parametrização de Critério para Intercorrente; 2) Processo Sobrestado; ou 3) Processo Bloqueado. \n \n - Somente no cenário do item 1 acima a forma de indicação de Nível de Acesso dos Documentos pelo Usuário Externo será a parametrizada para Intercorrente Padrão. - Em todos os cenários indicados acima somente ocorrerá a abertura de Processo Novo Relacionado utilizando o Tipo de Processo parametrizado para Intercorrente Padrão quando o Tipo de Processo do processo indicado estiver desativado ou quando a unidade na qual ocorrerá o peticionamento não tiver permissão de uso do Tipo de Processo do processo indicado.', 'Ajuda');"
                             onmouseout="return infraTooltipOcultar();"
                             alt="Ajuda" class="infraImgModulo"/>
                    </div>
                    <div id="divHidden">
                        <input type="hidden" name="hdnParametroHipoteseLegal" id="hdnParametroHipoteseLegal"
                               value="<?php echo $valorParametroHipoteseLegal; ?>"/>
                        <input type="hidden" id="hdnIdTipoProcesso" name="hdnIdTipoProcesso"
                               value="<?php echo $idTipoProcesso ?>"/>
                        <input type="hidden" id="hdnIdMdPetTipoProcesso" name="hdnIdMdPetTipoProcesso"
                               value="<?php echo $idMdPetTipoProcesso ?>"/>
                    </div>
                </div>
            </div>
            <!--  Fim do Tipo de Processo -->
            <div class="row rowFieldSet1 rowFieldSet">
                <div class="col-sm-12 col-md-10 col-lg-10 col-xl-10">
                    <fieldset class="infraFieldset form-control">

                        <legend class="infraLegend">&nbsp;Nível de Acesso dos Documentos</legend>

                            <input <?php echo $sinNAUsuExt; ?> type="radio" name="rdNivelAcesso[]"
                                                               id="rdUsuExternoIndicarEntrePermitidos"
                                                               onclick="changeNivelAcesso();" value="1" class="infraRadio">
                            <label for="rdUsuExternoIndicarEntrePermitidos" id="lblUsuExterno" class="infraLabelRadio">Usuário
                                Externo indicar diretamente</label><br/>

                            <input <?php echo $sinNAPadrao; ?> type="radio" name="rdNivelAcesso[]" id="rdPadrao"
                                                               onclick="changeNivelAcesso();" value="2" class="infraRadio">
                            <label name="lblPadrao" id="lblPadrao" for="rdPadrao" class="infraLabelRadio">Padrão pré
                                definido</label>

                        <div class="row" id="divNivelAcesso" <?php echo $sinNAPadrao != '' ? 'style="display: inherit;"' : 'style="display: none;"' ?>>
                            <div class="col-sm-5 col-md-5 col-lg-5 col-xl-3">
                                <label name="lblNivelAcesso" id="lblNivelAcesso" for="selNivelAcesso"
                                       class="infraLabelObrigatorio">Nível de Acesso: </label><br/>
                                <select id="selNivelAcesso" name="selNivelAcesso" onchange="changeSelectNivelAcesso()"
                                        class="infraSelect">
                                    <?= $strItensSelNivelAcesso ?>
                                </select>
                            </div>
                        </div>
                        <div class="row" id="divHipoteseLegal" <?php echo $hipoteseLegal ?> >
                            <div class="col-sm-8 col-md-8 col-lg-8 col-xl-5">
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
        </div>
        <div class="row rowFieldSet2 rowFieldSet">
            <div class="col-sm-12 col-md-10 col-lg-10">
                <fieldset class="infraFieldset form-control">
                    <legend class="infraLegend">Exibir menu Peticionamento Intercorrente
                        <img id="imgAjuda2"
                             src="<?= PaginaSEI::getInstance()->getDiretorioImagensGlobal() ?>/ajuda.gif"
                             name="ajuda"
                             onmouseover="return infraTooltipMostrar('Opção para tornar visível ou não o menu de Peticionamento > Intercorrente para os Usuários Externos no Acesso Externo do SEI.', 'Ajuda');"
                             onmouseout="return infraTooltipOcultar();"
                             class="infraImg"/>
                    </legend>
                        <input <?php echo $sinAtivoSim; ?> type="radio" name="rdSinAtivo[]" id="rdSinAtivoSim" value="S" class="infraRadio">
                        <label for="rdSinAtivoSim" id="lblSinAtivoSim" class="infraLabelRadio">Exibir no Acesso
                            Externo</label><br/>
                        <input <?php echo $sinAtivoNao; ?> type="radio" name="rdSinAtivo[]" id="rdSinAtivoNao" value="N" class="infraRadio">
                        <label name="rdSinAtivoNao" id="lblSinAtivoNao" for="rdSinAtivoNao" class="infraLabelRadio">Não
                            Exibir no Acesso Externo</label>


                </fieldset>
            </div>
        </div>
        </div>

        <?
        PaginaSEI::getInstance()->fecharAreaDados();
        ?>
    </form>
<?
PaginaSEI::getInstance()->fecharBody();
PaginaSEI::getInstance()->fecharHtml();
require_once 'md_pet_intercorrente_criterio_padrao_js.php';
?>