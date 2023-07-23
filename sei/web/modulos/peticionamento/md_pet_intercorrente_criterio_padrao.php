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

    //Preparar Preenchimento Altera��o
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
            $strTitulo = 'Intercorrente Padr�o';

            $arrComandos[] = '<button type="submit" accesskey="s" name="sbmCadastrarTpProcessoPeticionamento" id="sbmCadastrarTpProcessoPeticionamento" value="Salvar" class="infraButton"><span class="infraTeclaAtalho">S</span>alvar</button>';
            $arrComandos[] = '<button type="button" accesskey="c" name="btnCancelar" id="btnCancelar" value="Cancelar" onclick="location.href=\'' . PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . PaginaSEI::getInstance()->getAcaoRetorno() . '&acao_origem=' . $_GET['acao'])) . '\';" class="infraButton"><span class="infraTeclaAtalho">C</span>ancelar</button>';

            // cadastrando o crit�rio intercorrente
            if (isset($_POST['sbmCadastrarTpProcessoPeticionamento'])) {
                $objMdPetCriterioDTO = new MdPetCriterioDTO();
                $objMdPetCriterioDTO->setNumIdTipoProcedimento($_POST['hdnIdTipoProcesso']);
                $objMdPetCriterioDTO->setStrStaNivelAcesso($_POST['rdNivelAcesso'][0]);
                $objMdPetCriterioDTO->setStrSinIntercorrenteSigiloso($_POST['rdSinPeticionarProcessoSigiloso'][0]);
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

                MdPetForcarNivelAcessoDocINT::forcarNivelAcessoDocumento($tipoPeticionamento = 'I');

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
                    $sinIntercorrenteSigilosoSim = $objCriterioIntercorrentePadraoDTO->getStrSinIntercorrenteSigiloso() == 'S' ? 'checked = checked' : '';
                    $sinIntercorrenteSigilosoNao = $objCriterioIntercorrentePadraoDTO->getStrSinIntercorrenteSigiloso() == 'N' ? 'checked = checked' : '';
                    $hipoteseLegal = $objCriterioIntercorrentePadraoDTO->getStrStaTipoNivelAcesso() === 'I' && $valorParametroHipoteseLegal != '0' ? 'style="display:flex"' : 'style="display:none"';
                    $strItensSelTipoProcesso = MdPetTipoProcessoINT::montarSelectTipoProcesso(null, null, $idTipoProcesso);
                    $strItensSelHipoteseLegal = MdPetTipoProcessoINT::montarSelectHipoteseLegal(null, null, $objCriterioIntercorrentePadraoDTO->getNumIdHipoteseLegal());
                    $nivelAcessoTemplate = '<option value="%s" %s>%s</option>';
                    $arrNivelAcesso = array(
                        '' => '',
                        'P' => 'P�blico',
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
            throw new InfraException("A��o '" . $_GET['acao'] . "' n�o reconhecidas.");
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

    <form id="frmCriterioCadastro" method="post" onsubmit="return OnSubmitForm();" action="<?= PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . $_GET['acao'] . '&acao_origem=' . $_GET['acao'])) ?>">

        <? PaginaSEI::getInstance()->montarBarraComandosSuperior($arrComandos); ?>
        <? PaginaSEI::getInstance()->abrirAreaDados('98%'); ?>

        <div class="infraAreaDados" id="divInfraAreaDados">
            <!--  Tipo de Processo  -->
            <div class="row">
                <div class="col-12">
                    <div class="form-group">
                        <label id="lblTipoProcesso" for="txtTipoProcesso" class="infraLabelObrigatorio">Tipo de Processo: </label>
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
                                onmouseover="return infraTooltipMostrar('Apenas ap�s a parametriza��o do Intercorrente Padr�o � que os Usu�rios Externos passar�o a visualizar o menu de Peticionamento Intercorrente. \n \n A abertura de Processo Novo Relacionado ao processo de fato indicado pelo Usu�rio Externo ocorrer� quando o processo indicado corresponder a: 1) Tipo de Processo sem parametriza��o de Crit�rio para Intercorrente; 2) Processo Sobrestado; 3) Processo Bloqueado; ou 4) Quando for processo Sigiloso e tiver marcada a op��o &ldquo;N�o permitir Peticionamento Intercorrente Direto no Processo Indicado&rdquo;  nesta tela de Administra��o. \n \n - Somente no cen�rio do item 1 acima a forma de indica��o de N�vel de Acesso dos Documentos pelo Usu�rio Externo ser� a parametrizada para Intercorrente Padr�o. \n - Em todos os cen�rios indicados acima somente ocorrer� a abertura de Processo Novo Relacionado utilizando o Tipo de Processo parametrizado para Intercorrente Padr�o quando o Tipo de Processo do processo indicado estiver desativado ou quando a unidade na qual ocorrer� o peticionamento n�o tiver permiss�o de uso do Tipo de Processo do processo indicado.', 'Ajuda');"
                                onmouseout="return infraTooltipOcultar();"
                                alt="Ajuda" class="infraImgModulo"/>
                        </div>
                        <div id="divHidden">
                            <input type="hidden" name="hdnParametroHipoteseLegal" id="hdnParametroHipoteseLegal" value="<?= $valorParametroHipoteseLegal ?>"/>
                            <input type="hidden" id="hdnIdTipoProcesso" name="hdnIdTipoProcesso" value="<?= $idTipoProcesso ?>"/>
                            <input type="hidden" id="hdnIdMdPetTipoProcesso" name="hdnIdMdPetTipoProcesso" value="<?= $idMdPetTipoProcesso ?>"/>
                        </div>
                    </div>
                </div>
            </div>
            <!--  Fim do Tipo de Processo -->
            <div class="row mb-3">
                <div class="col-12">
                    <fieldset class="infraFieldset form-control">
                        <legend class="infraLegend">N�vel de Acesso dos Documentos</legend>
                            <div class="form-group mb-0">
                                <div class="infraDivRadio mt-2">
                                    <input <?= $sinNAUsuExt ?> type="radio" name="rdNivelAcesso[]" id="rdUsuExternoIndicarEntrePermitidos" onclick="changeNivelAcesso();" value="1" class="infraRadio">
                                    <label for="rdUsuExternoIndicarEntrePermitidos" id="lblUsuExterno" class="infraLabelRadio">Usu�rio Externo indicar diretamente</label>
                                </div>
                                <div class="infraDivRadio">
                                    <input <?= $sinNAPadrao ?> type="radio" name="rdNivelAcesso[]" id="rdPadrao"onclick="changeNivelAcesso();" value="2" class="infraRadio">
                                    <label name="lblPadrao" id="lblPadrao" for="rdPadrao" class="infraLabelRadio">Padr�o pr�-definido</label>
                                </div>
                            </div>

                        <div class="row" id="divNivelAcesso" <?php echo $sinNAPadrao != '' ? 'style="display: flex;"' : 'style="display: none;"' ?>>
                            <div class="col-sm-12 col-md-12 col-lg-3 col-xl-3">
                                <div class="form-group mb-0">
                                    <label name="lblNivelAcesso" id="lblNivelAcesso" for="selNivelAcesso" class="infraLabelObrigatorio">N�vel de Acesso: </label><br/>
                                    <select id="selNivelAcesso" name="selNivelAcesso" onchange="changeSelectNivelAcesso()" class="infraSelect form-control">
                                        <?= $strItensSelNivelAcesso ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-12 col-lg-9 col-xl-9" id="divHipoteseLegal" <?php echo $hipoteseLegal ?>>
                                <div class="form-group">
                                    <label name="lblHipoteseLegal" id="lblHipoteseLegal" for="selHipoteseLegal" class="infraLabelObrigatorio">Hip�tese Legal:</label>
                                    <select id="selHipoteseLegal" name="selHipoteseLegal" class="infraSelect form-control" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>">
                                        <?= $strItensSelHipoteseLegal ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </fieldset>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-12">
                    <fieldset class="infraFieldset form-control">
                        <legend class="infraLegend">Exibir menu Peticionamento Intercorrente
                            <img id="imgAjuda2"
                                 src="<?= PaginaSEI::getInstance()->getDiretorioImagensGlobal() ?>/ajuda.gif"
                                 name="ajuda"
                                 onmouseover="return infraTooltipMostrar('Op��o para tornar vis�vel ou n�o o menu de Peticionamento > Intercorrente para os Usu�rios Externos no Acesso Externo do SEI.', 'Ajuda');"
                                 onmouseout="return infraTooltipOcultar();"
                                 class="infraImg"/>
                        </legend>
                        <div class="form-group mb-0">
                            <div class="infraDivRadio mt-2">
                                <input <?php echo $sinAtivoSim; ?> type="radio" name="rdSinAtivo[]" id="rdSinAtivoSim" value="S" class="infraRadio">
                                <label for="rdSinAtivoSim" id="lblSinAtivoSim" class="infraLabelRadio">Exibir no Acesso Externo</label>
                            </div>
                            <div class="infraDivRadio">
                                <input <?php echo $sinAtivoNao; ?> type="radio" name="rdSinAtivo[]" id="rdSinAtivoNao" value="N" class="infraRadio">
                                <label name="rdSinAtivoNao" id="lblSinAtivoNao" for="rdSinAtivoNao" class="infraLabelRadio">N�o Exibir no Acesso Externo</label>
                            </div>
                        </div>
                    </fieldset>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-12">
                    <fieldset class="infraFieldset form-control">
                        <legend class="infraLegend">Peticionamento Intercorrente em Processos com N�vel de Acesso Sigiloso</legend>
                        <div class="form-group">
                            <div class="infraDivRadio">
                                <input <?php echo $sinIntercorrenteSigilosoSim; ?> type="radio" name="rdSinPeticionarProcessoSigiloso[]" id="rdSinIntercorrenteSigilosoSim" value="S" class="infraRadio">
                                <label for="rdSinIntercorrenteSigilosoSim" id="lblSinIntercorrenteSigilosoSim" class="infraLabelRadio">Permitir Peticionamento Intercorrente Direto no Processo Indicado</label>
                                <img id="imgAjuda" src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/ajuda.svg"
                                     onmouseover="return infraTooltipMostrar('Selecione esta op��o para permitir que o Usu�rio Externo realize o Peticionamento Intercorrente diretamente no Processo Indicado, mesmo que este tenha o N�vel de Acesso Sigiloso (padr�o).', 'Ajuda');"
                                     onmouseout="return infraTooltipOcultar();"
                                     alt="Ajuda" class="infraImgModulo ml-1"/>
                            </div>

                            <div class="infraDivRadio">
                                <input <?php echo $sinIntercorrenteSigilosoNao; ?> type="radio" name="rdSinPeticionarProcessoSigiloso[]" id="rdSinIntercorrenteSigilosoNao" value="N" class="infraRadio">
                                <label name="rdSinIntercorrenteSigilosoNao" id="lblSinIntercorrenteSigilosoNao" for="rdSinIntercorrenteSigilosoNao" class="infraLabelRadio">N�o permitir Peticionamento Intercorrente Direto no Processo Indicado</label>
                                <img id="imgAjuda" src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/ajuda.svg"
                                     onmouseover="return infraTooltipMostrar('Selecione esta op��o para n�o permitir que o Usu�rio Externo realize o Peticionamento Intercorrente diretamente no Processo Indicado quando este tenha o N�vel de Acesso Sigiloso, for�ando a abertura de Processo Novo Relacionado ao ao Processo Indicado.', 'Ajuda');"
                                     onmouseout="return infraTooltipOcultar();"
                                     alt="Ajuda" class="infraImgModulo ml-1"/>
                            </div>
                        </div>
                    </fieldset>
                </div>
            </div>

            <?php $staTipoPeticinamento='I'; require_once 'md_pet_forcar_nivel_acesso_doc_bloco.php'; ?>

        </div>

        <? PaginaSEI::getInstance()->fecharAreaDados(); ?>
        <? PaginaSEI::getInstance()->montarBarraComandosInferior($arrComandos); ?>
    </form>

    <?php require_once 'md_pet_intercorrente_criterio_padrao_js.php'; ?>
    <?php require_once 'md_pet_forcar_nivel_acesso_doc_bloco_js.php'; ?>

<? PaginaSEI::getInstance()->fecharBody(); ?>
<? PaginaSEI::getInstance()->fecharHtml(); ?>
