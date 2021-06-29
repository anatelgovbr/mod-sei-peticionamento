<?
/**
 * TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
 *
 * 07/12/2016 - criado por Marcelo Bezerra - CAST
 *
 * Versão do Gerador de Código: 1.39.0
 *
 */

try {

    require_once dirname(__FILE__) . '/../../SEI.php';

    session_start();

    //////////////////////////////////////////////////////////////////////////////
    //InfraDebug::getInstance()->setBolLigado(false);
    //InfraDebug::getInstance()->setBolDebugInfra(true);
    //InfraDebug::getInstance()->limpar();
    //////////////////////////////////////////////////////////////////////////////

    SessaoSEI::getInstance()->validarLink();

    PaginaSEI::getInstance()->verificarSelecao('md_pet_int_serie_selecionar');

    SessaoSEI::getInstance()->validarPermissao($_GET['acao']);

    PaginaSEI::getInstance()->salvarCamposPost(array('selSerie'));

    $strLinkAjaxTipoDocumento = SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=md_pet_int_serie_auto_completar');

    //Tipo de Documento
    $strLinkTipoDocumentoEssencialSelecao = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_pet_serie_selecionar&filtro=2&tipo_selecao=2&id_object=objLupaTipoDocumentoEssencial&tipoDoc=E');

    $objMdPetIntSerieDTO = new MdPetIntSerieDTO();

    $strDesabilitar = '';

    $arrComandos = array();

    switch ($_GET['acao']) {
        case 'md_pet_int_serie_cadastrar':

            $objMdPetIntSerieRN = new MdPetIntSerieRN();
            $objMdPetIntSerieDTO->retTodos(true);
            $arrSeriesEss = $objMdPetIntSerieRN->listar($objMdPetIntSerieDTO);

            $strItensSelSeriesEss = "";
            for ($x = 0; $x < count($arrSeriesEss); $x++) {
                $strItensSelSeriesEss .= "<option value='" . $arrSeriesEss[$x]->getNumIdSerie() . "'>" . $arrSeriesEss[$x]->getStrNomeSerie() . "</option>";
            }

            $strTitulo = 'Tipos de Documentos para Intimação Eletrônica';
            $arrComandos[] = '<button type="submit" accesskey="S" name="sbmCadastrarMdPetIntSerie" value="Salvar" class="infraButton"><span class="infraTeclaAtalho">S</span>alvar</button>';
            $arrComandos[] = '<button type="button" accesskey="C" name="btnCancelar" id="btnCancelar" value="Cancelar" onclick="location.href=\'' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . PaginaSEI::getInstance()->getAcaoRetorno() . '&acao_origem=' . $_GET['acao']) . '\';" class="infraButton"><span class="infraTeclaAtalho">C</span>ancelar</button>';

            $numIdSerie = PaginaSEI::getInstance()->recuperarCampo('selSerie');

            if ($numIdSerie !== '') {
                $objMdPetIntSerieDTO->setNumIdSerie($numIdSerie);
            } else {
                $objMdPetIntSerieDTO->setNumIdSerie(null);
            }

            if (isset($_POST['sbmCadastrarMdPetIntSerie'])) {
                $objMdPetIntSerieRN = new MdPetIntSerieRN();
                $objMdPetIntSerieDTO = new MdPetIntSerieDTO();
                $objMdPetIntSerieDTO->retTodos();
                $arrMdPetIntSerieDTO = array();
                $arrListarMdPetIntSerieDTO = $objMdPetIntSerieRN->listar($objMdPetIntSerieDTO);

                if (is_array($arrListarMdPetIntSerieDTO) && count($arrListarMdPetIntSerieDTO) > 0) {
                    $objMdPetIntSerieRN->excluir($arrListarMdPetIntSerieDTO);
                }

                $arrValuesHdnSerie = PaginaSEI::getInstance()->getArrValuesSelect($_POST['hdnSerie']);

                try {
                    $objMdPetIntSerieRN = new MdPetIntSerieRN();
                    $arrObjMdPetIntSerieDTO = array();
                    foreach ($arrValuesHdnSerie as $itemHdnSerie) {
                        $objMdPetIntSerieDTO = new MdPetIntSerieDTO();
                        $objMdPetIntSerieDTO->setNumIdSerie($itemHdnSerie);
                        array_push($arrObjMdPetIntSerieDTO, $objMdPetIntSerieDTO);
                    }
                    $objMdPetIntSerieDTO = $objMdPetIntSerieRN->cadastrar($arrObjMdPetIntSerieDTO);
                    PaginaSEI::getInstance()->adicionarMensagem(' "' . $objMdPetIntSerieDTO[0]->getNumIdSerie() . '" cadastrado com sucesso.');
                    header('Location: ' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . $_GET['acao'] . '&acao_origem=' . $_GET['acao'] . '&id_serie=' . $objMdPetIntSerieDTO[0]->getNumIdSerie() . PaginaSEI::getInstance()->montarAncora($objMdPetIntSerieDTO[0]->getNumIdSerie())));
                    die;
                } catch (Exception $e) {
                    PaginaSEI::getInstance()->processarExcecao($e);
                }
            }
            break;

        case 'md_pet_int_serie_alterar':
            $strTitulo = 'Alterar ';
            $arrComandos[] = '<button type="submit" accesskey="S" name="sbmAlterarMdPetIntSerie" value="Salvar" class="infraButton"><span class="infraTeclaAtalho">S</span>alvar</button>';
            $strDesabilitar = 'disabled="disabled"';

            if (isset($_GET['id_serie'])) {
                $objMdPetIntSerieDTO->setNumIdSerie($_GET['id_serie']);
                $objMdPetIntSerieDTO->retTodos();
                $objMdPetIntSerieRN = new MdPetIntSerieRN();
                $objMdPetIntSerieDTO = $objMdPetIntSerieRN->consultar($objMdPetIntSerieDTO);
                if ($objMdPetIntSerieDTO == null) {
                    throw new InfraException("Registro não encontrado.");
                }
            } else {
                $objMdPetIntSerieDTO->setNumIdSerie($_POST['hdnIdSerie']);
            }

            $objMdPetIntSerieRN = new MdPetIntSerieRN();
            $objMdPetIntSerieDTO = new MdPetIntSerieDTO();
            $objMdPetIntSerieDTO->retTodos();
            $arrSeriesEss = $objMdPetIntSerieRN->listar($objMdPetIntSerieDTO);


            $strItensSelSeriesEss = "";
            for ($x = 0; $x < count($arrSeriesEss); $x++) {
                $strItensSelSeriesEss .= "<option value='" . $arrSeriesEss[$x]->getNumIdSerie() . "'>" . $arrSeriesEss[$x]->getStrNomeSerie() . "</option>";
            }

            $arrComandos[] = '<button type="button" accesskey="C" name="btnCancelar" id="btnCancelar" value="Cancelar" onclick="location.href=\'' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . PaginaSEI::getInstance()->getAcaoRetorno() . '&acao_origem=' . $_GET['acao']) . '\';" class="infraButton"><span class="infraTeclaAtalho">C</span>ancelar</button>';
            if (isset($_POST['sbmAlterarMdPetIntSerie'])) {
                try {
                    $objMdPetIntSerieRN = new MdPetIntSerieRN();
                    $objMdPetIntSerieRN->alterar($objMdPetIntSerieDTO);
                    PaginaSEI::getInstance()->adicionarMensagem(' "' . $objMdPetIntSerieDTO->getNumIdSerie() . '" alterad com sucesso.');
                    header('Location: ' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . PaginaSEI::getInstance()->getAcaoRetorno() . '&acao_origem=' . $_GET['acao'] . PaginaSEI::getInstance()->montarAncora($objMdPetIntSerieDTO->getNumIdSerie())));
                    die;
                } catch (Exception $e) {
                    PaginaSEI::getInstance()->processarExcecao($e);
                }
            }
            break;

        case 'md_pet_int_serie_consultar':
            $strTitulo = 'Consultar ';
            $arrComandos[] = '<button type="button" accesskey="F" name="btnFechar" value="Fechar" onclick="location.href=\'' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . PaginaSEI::getInstance()->getAcaoRetorno() . '&acao_origem=' . $_GET['acao'] . PaginaSEI::getInstance()->montarAncora($_GET['id_serie'])) . '\';" class="infraButton"><span class="infraTeclaAtalho">F</span>echar</button>';
            $objMdPetIntSerieDTO->setNumIdSerie($_GET['id_serie']);
            $objMdPetIntSerieDTO->setBolExclusaoLogica(false);
            $objMdPetIntSerieDTO->retTodos();
            $objMdPetIntSerieRN = new MdPetIntSerieRN();
            $objMdPetIntSerieDTO = $objMdPetIntSerieRN->consultar($objMdPetIntSerieDTO);
            if ($objMdPetIntSerieDTO === null) {
                throw new InfraException("Registro não encontrado.");
            }
            break;

        default:
            throw new InfraException("Ação '" . $_GET['acao'] . "' não reconhecida.");
    }

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
require_once("md_pet_int_serie_cadastro_css.php");
PaginaSEI::getInstance()->fecharStyle();
PaginaSEI::getInstance()->montarJavaScript();
PaginaSEI::getInstance()->abrirJavaScript();
PaginaSEI::getInstance()->fecharJavaScript();
PaginaSEI::getInstance()->fecharHead();
PaginaSEI::getInstance()->abrirBody($strTitulo, 'onload="inicializar();"');
?>
    <form id="frmMdPetIntSerieCadastro" method="post" onsubmit="return OnSubmitForm();"
          action="<?= SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . $_GET['acao'] . '&acao_origem=' . $_GET['acao']) ?>">
        <?
        PaginaSEI::getInstance()->montarBarraComandosSuperior($arrComandos);
        $divDocs = 'style="display: inherit;"';
        ?>
        <fieldset <?php echo $divDocs; ?> id="fldDocEssenciais" class="sizeFieldset tamanhoFieldset fieldNone">
            <div class="row">
                <div class="col-sm-12 col-md-5 col-lg-5 col-xl-4">
                    <label id="lblSerie" for="selSerie" class="infraLabelObrigatorio">Tipos dos Documentos:
                        <img align="top"
                             src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/ajuda.svg"
                             name="ajuda" <?= PaginaSEI::montarTitleTooltip('Selecione Tipos de Documentos parametrizados na Administração com Aplicabilidade de Documentos Internos ou Internos e Externos. \n \n Por exemplo, Ofício é um documento tradicionalmente de comunicação externa e serve para ser o documento principal de uma Intimação Eletrônica. \n \n Nos Documentos Gerados dos Tipos aqui indicados, após assinados, aparecerá o botão Gerar Intimação Eletrônica.', 'Ajuda') ?>
                             class="infraImgModulo"/>
                    </label>
                    <input type="text" id="txtSerie" name="txtSerie" class="infraText form-control"
                           tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12 col-md-6 col-lg-6 col-xl-6">
                    <div class="input-group mb-3">
                        <select style="float: left; width: 50%" id="selSerie" name="selSerie" size="8"
                                multiple="multiple"
                                class="infraSelect form-control"
                                tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>">
                            <?= $strItensSelSeriesEss; ?>
                        </select>
                        <div class="botoes">
                            <img id="imgLupaTipoDocumento" onclick="objLupaTipoDocumentoEssencial.selecionar(700,500);"
                                 onkeypress="objLupaTipoDocumentoEssencial.selecionar(700,500);"
                                 src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/pesquisar.svg"
                                 alt="Selecionar Tipo de Documento" title="Selecionar Tipo de Documento"
                                 class="infraImg"
                                 tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
                            <br/>
                            <img id="imgExcluirTipoDocumento" onclick="objLupaTipoDocumentoEssencial.remover();"
                                 onkeypress="objLupaTipoDocumentoEssencial.remover();"
                                 src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/remover.svg"
                                 alt="Remover Tipos de Documentos Selecionados"
                                 title="Remover Tipos de Documentos Selecionados"
                                 class="infraImg" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
                        </div>
                    </div>
                </div>
            </div>
        </fieldset>

        <input type="hidden" id="hdnIdSerie" name="hdnIdSerie" value="<?= $objMdPetIntSerieDTO->getNumIdSerie(); ?>"/>
        <input type="hidden" id="hdnSerie" name="hdnSerie" value=""/>
    </form>
<?
require_once("md_pet_int_serie_cadastro_js.php");
PaginaSEI::getInstance()->fecharBody();
PaginaSEI::getInstance()->fecharHtml();
?>