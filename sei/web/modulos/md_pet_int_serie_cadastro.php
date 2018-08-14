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
            $arrSeriesEss = $objMdPetIntSerieRN->listar( $objMdPetIntSerieDTO );

            $strItensSelSeriesEss = "";
            for($x = 0;$x<count($arrSeriesEss);$x++){
                $strItensSelSeriesEss .= "<option value='" . $arrSeriesEss[$x]->getNumIdSerie() .  "'>" . $arrSeriesEss[$x]->getStrNomeSerie(). "</option>";
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

                if( is_array( $arrListarMdPetIntSerieDTO ) && count( $arrListarMdPetIntSerieDTO ) > 0 ){
                    $objMdPetIntSerieRN->excluir( $arrListarMdPetIntSerieDTO );
                }

                $arrValuesHdnSerie = PaginaSEI::getInstance()->getArrValuesSelect($_POST['hdnSerie']);

                try {
                    $objMdPetIntSerieRN = new MdPetIntSerieRN();
                    foreach ($arrValuesHdnSerie as $itemHdnSerie) {
                        $objMdPetIntSerieDTO->setNumIdSerie($itemHdnSerie);
                        $objMdPetIntSerieDTO = $objMdPetIntSerieRN->cadastrar($objMdPetIntSerieDTO);
                    }
                    PaginaSEI::getInstance()->adicionarMensagem(' "' . $objMdPetIntSerieDTO->getNumIdSerie() . '" cadastrado com sucesso.');
                    header('Location: ' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . $_GET['acao'] . '&acao_origem=' . $_GET['acao'] . '&id_serie=' . $objMdPetIntSerieDTO->getNumIdSerie() . PaginaSEI::getInstance()->montarAncora($objMdPetIntSerieDTO->getNumIdSerie())));
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
            $arrSeriesEss = $objMdPetIntSerieRN->listar( $objMdPetIntSerieDTO );


            $strItensSelSeriesEss = "";
            for($x = 0;$x<count($arrSeriesEss);$x++){
                $strItensSelSeriesEss .= "<option value='" . $arrSeriesEss[$x]->getNumIdSerie() .  "'>" . $arrSeriesEss[$x]->getStrNomeSerie(). "</option>";
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
?>
<? if (0){ ?>
    <style><?}?>
        #lblSerie { left: 0%; top: 0%; width: 25%; }
        #selSerie { left: 0%; top: 40%; width: 85%; }
        #txtSerie { width: 50%; }
        #lblSerie { width: 50%; }
        #selSerie { width: 75%; }

        #imgLupaTipoDocumento { margin-top: 2px; margin-left: 4px; }
        #imgExcluirTipoDocumento { margin-top: 2px; margin-left: 4px; }
        .fieldNone { border: none !important; }

        #imgAjuda { left:290px;top:190px; }

        <? if (0){ ?></style><? } ?>
<?
PaginaSEI::getInstance()->fecharStyle();
PaginaSEI::getInstance()->montarJavaScript();
PaginaSEI::getInstance()->abrirJavaScript();
?>
<? if (0){ ?>
    <script type="text/javascript"><?}?>

        var objLupaTipoDocumentoEssencial = null
        var objAutoCompletarTipoDocumentoEssencial = null;

        function inicializar() {
            if ('<?=$_GET['acao']?>' == 'md_pet_int_serie_cadastrar') {
            } else if ('<?=$_GET['acao']?>' == 'md_pet_int_serie_consultar') {
                infraDesabilitarCamposAreaDados();
            } else {
                document.getElementById('btnCancelar').focus();
            }
            infraEfeitoTabelas();
            carregarComponenteTipoDocumentoEssencial();
        }

        function validarCadastro() {
            if (!infraSelectSelecionado('selSerie')) {
                alert('Selecione um Tipo de Documento.');
                document.getElementById('selSerie').focus();
                return false;
            }
            return true;
        }

        function OnSubmitForm() {
            return validarCadastro();
        }

        //Carrega o documento
        function carregarComponenteTipoDocumentoEssencial() {

            objAutoCompletarTipoDocumentoEssencial = new infraAjaxAutoCompletar('hdnIdSerie', 'txtSerie', '<?=$strLinkAjaxTipoDocumento?>');
            objAutoCompletarTipoDocumentoEssencial.limparCampo = true;

            objAutoCompletarTipoDocumentoEssencial.prepararExecucao = function () {
                var tipo = 'E';
                return 'palavras_pesquisa=' + document.getElementById('txtSerie').value + '&tipoDoc=' + tipo;
            };

            objAutoCompletarTipoDocumentoEssencial.processarResultado = function (id, nome, complemento) {

                if (id != '') {
                    var options = document.getElementById('selSerie').options;

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
                        var opt = infraSelectAdicionarOption(document.getElementById('selSerie'), nome, id);
                        objLupaTipoDocumentoEssencial.atualizar();
                        opt.selected = true;
                    }
                    document.getElementById('txtSerie').value = '';
                    document.getElementById('txtSerie').focus();
                }
            };

            objLupaTipoDocumentoEssencial = new infraLupaSelect('selSerie', 'hdnSerie', '<?=$strLinkTipoDocumentoEssencialSelecao?>');
        }

        <? if (0){ ?></script><? } ?>
<?
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
            <div>
                <div style="clear:both;">&nbsp;</div>
                <div>
                    <label id="lblSerie" for="selSerie" class="infraLabelOpcional">Tipos dos Documentos:</label>
                        <img src="<?= PaginaSEI::getInstance()->getDiretorioImagensGlobal() ?>/ajuda.gif" name="ajuda" id="imgAjuda" <?= PaginaSEI::montarTitleTooltip('Selecionar Tipos de Documentos com Aplicabilidade Documentos Internos ou Internos e Externos. Por exemplo, Ofício são documentos tradicionalmente de comunicação externa e serve para ser documento principal de Intimação Eletrônica.\n\n\n Em documentos gerados dos Tipos aqui indicados e após assinado aparecerá o botão Gerar Intimação Eletrônica.') ?> class="infraImg"/>
                </div>
                <div>
                    <input type="text" id="txtSerie" name="txtSerie" class="infraText"
                           tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
                </div>
                <div style="margin-top: 5px;">
                    <select style="float: left;" id="selSerie" name="selSerie" size="8" multiple="multiple" class="infraSelect">
                        <?= $strItensSelSeriesEss; ?>
                    </select>
                    <img id="imgLupaTipoDocumento" onclick="objLupaTipoDocumentoEssencial.selecionar(700,500)" src="/infra_css/imagens/lupa.gif" alt="Localizar Tipo de Documento" title="Localizar Tipo de Documento" class="infraImg"/>
                    <br/>
                    <img id="imgExcluirTipoDocumento" onclick="objLupaTipoDocumentoEssencial.remover();" src="/infra_css/imagens/remover.gif" alt="Remover Tipos de Documentos" title="Remover Tipos de Documentos" class="infraImg"/>
                </div>
            </div>
        </fieldset>

        <input type="hidden" id="hdnIdSerie" name="hdnIdSerie" value="<?= $objMdPetIntSerieDTO->getNumIdSerie(); ?>"/>
        <input type="hidden" id="hdnSerie" name="hdnSerie" value=""/>
    </form>
<?
PaginaSEI::getInstance()->fecharBody();
PaginaSEI::getInstance()->fecharHtml();
?>