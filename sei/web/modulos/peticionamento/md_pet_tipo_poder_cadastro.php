<?
/**
 * ANATEL
 *
 * 15/06/2016 - criado por marcelo.bezerra - CAST
 *
 */

try {

    require_once dirname(__FILE__) . '/../../SEI.php';
    session_start();
    PaginaSEI::getInstance()->setBolXHTML(false);

    //////////////////////////////////////////////////////////////////////////////
    //InfraDebug::getInstance()->setBolLigado(false);
    //InfraDebug::getInstance()->setBolDebugInfra(true);
    //InfraDebug::getInstance()->limpar();
    //////////////////////////////////////////////////////////////////////////////

    SessaoSEI::getInstance()->validarLink();

    SessaoSEI::getInstance()->validarPermissao($_GET['acao']);

    $arrComandos = array();

    $disabled = '';

    ?>
    <script type="text/javascript">
        var valor = "";

        function inicializar() {
            preencherCampo();
            infraEfeitoTabelas();
            addEventoEnter();
        }

    </script>
    <?php
    switch ($_GET['acao']) {

        case 'md_pet_tipo_poder_cadastrar':

            $strTitulo = 'Novo Tipo de Poder Legal';
            $arrComandos[] = '<button type="submit" accesskey="s" id="sbmCadastrarTpPoder" name="sbmCadastrarTpPoder" value="Salvar" class="infraButton"><span class="infraTeclaAtalho">S</span>alvar</button>';
            $arrComandos[] = '<button type="button" accesskey="c" name="btnCancelar" id="btnCancelar" value="Cancelar" onclick="location.href=\'' . PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . PaginaSEI::getInstance()->getAcaoRetorno() . '&acao_origem=' . $_GET['acao'])) . '\';" class="infraButton"><span class="infraTeclaAtalho">C</span>ancelar</button>';

            $objEditorRN = new EditorRN();
            $objEditorDTO = new EditorDTO();

            $objEditorDTO->setStrNomeCampo('txaConteudo');
            $objEditorDTO->setStrSinSomenteLeitura('N');
            $objEditorDTO->setNumTamanhoEditor(220);
            $retEditor = $objEditorRN->montarSimples($objEditorDTO);

            //Cadastrando tipo de poder
            if (isset($_POST['txtNome'])) {
                $objMdPetTipoPoderLegalDTO = new MdPetTipoPoderLegalDTO();
                $objMdPetTipoPoderLegalDTO->retNumIdTipoPoderLegal();
                $objMdPetTipoPoderLegalDTO->setStrNome($_POST['txtNome']);
                $objMdPetTipoPoderLegalDTO->setDtaDtaCadastro(InfraData::getStrDataHoraAtual());
                $objMdPetTipoPoderLegalDTO->setStrStaSistema(null);
                $objMdPetTipoPoderLegalDTO->setStrSinAtivo('S');
                $objMdPetTipoPoderLegalRN = new MdPetTipoPoderLegalRN();
                $arrObjMdPetTipoPoderLegalDTO = $objMdPetTipoPoderLegalRN->cadastrar($objMdPetTipoPoderLegalDTO);
                header('Location: ' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . PaginaSEI::getInstance()->getAcaoRetorno() . '&acao_origem=' . $_GET['acao'] . '&id_md_pet_tipo_poder=' . $arrObjMdPetTipoPoderLegalDTO->getNumIdTipoPoderLegal()));

            }


            break;

        case 'md_pet_tipo_poder_consultar':
            $disabled = "disabled";
            $idPoder = $_GET['IdTipoPoderLegal'];

            //Recuperando o Poder pelo ID e setando no campo de texto 'nome'.
            $objMdPetTipoPoderLegalDTO = new MdPetTipoPoderLegalDTO();
            $objMdPetTipoPoderLegalDTO->retTodos(true);
            $objMdPetTipoPoderLegalDTO->setNumIdTipoPoderLegal($idPoder);
            $objMdPetTipoPoderLegalRN = new MdPetTipoPoderLegalRN();
            $arrObjMdPetTipoPoderLegalDTO = $objMdPetTipoPoderLegalRN->consultar($objMdPetTipoPoderLegalDTO);
            $txtNome = $arrObjMdPetTipoPoderLegalDTO->getStrNome();

            $strTitulo = 'Consultar Tipo de Poder Legal';

            $arrComandos[] = '<button type="button" accesskey="c" name="btnFechar" id="btnFechar" value="Fechar" onclick="location.href=\'' . PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . PaginaSEI::getInstance()->getAcaoRetorno() . '&acao_origem=' . $_GET['acao'] . PaginaSEI::getInstance()->montarAncora($_GET['IdTipoPoderLegal']))) . '\';" class="infraButton">Fe<span class="infraTeclaAtalho">c</span>har</button>';
            $objEditorRN = new EditorRN();
            $objEditorDTO = new EditorDTO();

            $objEditorDTO->setStrNomeCampo('txaConteudo');
            $objEditorDTO->setStrSinSomenteLeitura('N');
            $objEditorDTO->setNumTamanhoEditor(220);
            $retEditor = $objEditorRN->montarSimples($objEditorDTO);


            break;

        case 'md_pet_tipo_poder_alterar':


            $strTitulo = 'Alterar Tipo de Poder Legal';
            $idPoder = $_GET['IdTipoPoderLegal'];

            $arrComandos[] = '<button type="submit" accesskey="s" id="sbmAlterarTpPoder" name="sbmAlterarTpPoder" value="Salvar" class="infraButton"><span class="infraTeclaAtalho">S</span>alvar</button>';
            $arrComandos[] = '<button type="button" accesskey="C" name="btnCancelar" id="btnCancelar" value="Cancelar" onclick="location.href=\'' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . PaginaSEI::getInstance()->getAcaoRetorno() . '&acao_origem=' . $_GET['acao'] . PaginaSEI::getInstance()->montarAncora($_GET['IdTipoPoderLegal'])) . '\';" class="infraButton"><span class="infraTeclaAtalho">C</span>ancelar</button>';

            $objEditorRN = new EditorRN();
            $objEditorDTO = new EditorDTO();

            $objEditorDTO->setStrNomeCampo('txaConteudo');
            $objEditorDTO->setStrSinSomenteLeitura('N');
            $objEditorDTO->setNumTamanhoEditor(220);
            $retEditor = $objEditorRN->montarSimples($objEditorDTO);

            if (isset($idPoder)) {
                //Recuperando o Poder pelo ID e setando no campo de texto 'nome'.
                $objMdPetTipoPoderLegalDTO = new MdPetTipoPoderLegalDTO();
                $objMdPetTipoPoderLegalDTO->retTodos(true);
                $objMdPetTipoPoderLegalDTO->setNumIdTipoPoderLegal($idPoder);
                $objMdPetTipoPoderLegalRN = new MdPetTipoPoderLegalRN();
                $arrObjMdPetTipoPoderLegalDTO = $objMdPetTipoPoderLegalRN->consultar($objMdPetTipoPoderLegalDTO);
                $txtNome = $arrObjMdPetTipoPoderLegalDTO->getStrNome();
            }

            //Cadastrando tipo de poder
            if (isset($_POST['txtNome'])) {

                $objMdPetTipoPoderLegalDTO = new MdPetTipoPoderLegalDTO();
                $objMdPetTipoPoderLegalDTO->retNumIdTipoPoderLegal();
                $objMdPetTipoPoderLegalDTO->setNumIdTipoPoderLegal($_POST['hdnIdTpPoder']);
                $objMdPetTipoPoderLegalDTO->setStrNome($_POST['txtNome']);
                $objMdPetTipoPoderLegalDTO->setDtaDtaCadastro(InfraData::getStrDataHoraAtual());
                $objMdPetTipoPoderLegalDTO->setStrStaSistema(null);
                $objMdPetTipoPoderLegalRN = new MdPetTipoPoderLegalRN();
                $arrObjMdPetTipoPoderLegalDTO = $objMdPetTipoPoderLegalRN->alterar($objMdPetTipoPoderLegalDTO);
                header('Location: ' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . PaginaSEI::getInstance()->getAcaoRetorno() . '&acao_origem=' . $_GET['acao'] . PaginaSEI::getInstance()->montarAncora($_POST['hdnIdTpPoder'])));
            }


            break;

        default:
            throw new InfraException("Ação '" . $_GET['acao'] . "' não reconhecida.");
            break;

    }
} catch (Exception $e) {
    $texto = $_POST['txtNome'];
    echo "<script type='text/javascript' >valor = '{$texto}'; inicializar();</script>";
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
PaginaSEI::getInstance()->fecharStyle();
PaginaSEI::getInstance()->montarJavaScript();
PaginaSEI::getInstance()->abrirJavaScript();
?>
<? if (0){ ?>
    <script><?}?>

        function preencherCampo() {
            if (document.getElementById('txtNome').value == '') {
                document.getElementById('txtNome').value = valor;
            }
        }

        function OnSubmitForm() {
            var txtNome = document.getElementById('txtNome').value;
            if (txtNome == '') {
                alert('Informe o Nome.');
                document.getElementById('txtNome').focus();
                return false;

            }
            return true;
        }

        function addEventoEnter() {
            var form = document.getElementById('frmTextoPadraoInternoCadastro');
            document.addEventListener("keypress", function (evt) {
                var key_code = evt.keyCode ? evt.keyCode :
                    evt.charCode ? evt.charCode :
                        evt.which ? evt.which : void 0;


                if (key_code == 13) {

                    if ('<?=$_GET['acao']?>' == 'md_pet_tipo_poder_alterar') {
                        $('#sbmAlterarTpPoder').click();
                    } else {
                        $('#sbmCadastrarTpPoder').click();
                    }
                }

            });
        }




        <? if (0){ ?></script><? } ?>

<?php
PaginaSEI::getInstance()->fecharJavaScript();
echo $retEditor->getStrInicializacao();
PaginaSEI::getInstance()->fecharHead();
PaginaSEI::getInstance()->abrirBody($strTitulo, 'onload="inicializar();"');
?>

    <form id="frmTextoPadraoInternoCadastro" method="post" onsubmit="return OnSubmitForm();"
          action="<?= PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . $_GET['acao'] . '&acao_origem=' . $_GET['acao'])) ?>">
        <?
        PaginaSEI::getInstance()->montarBarraComandosSuperior($arrComandos);

        if ($tipo == 'E') {
            PaginaSEI::getInstance()->abrirAreaDados('17em');
        } else if ($tipo == 'H') {
            PaginaSEI::getInstance()->abrirAreaDados('14em;overflow:hidden');
        } else {
            PaginaSEI::getInstance()->abrirAreaDados('17em');
        }
        ?>
        <div class="row">
            <div class="col-sm-12 col-md-6 col-lg-6 col-xl-5">
                <label id="lblNome" for="txtNome" class="infraLabelObrigatorio">Nome:
                    <img align="top"
                         style="width:20px;"
                         src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/ajuda.svg"
                         name="ajuda" <?= PaginaSEI::montarTitleTooltip('Indicar Poderes específicos para serem utilizados na emissão de Procuração Eletrônica simples pelos Usuários Externos. \n \n Por exemplo: Participar em Reuniões, Peticionar Processo Novo ou Intercorrente, Operar Sistemas.', 'Ajuda') ?>
                         class="infraImgModulo"/>
                </label>
                <input type="text" id="txtNome" name="txtNome" onkeypress="return infraMascaraTexto(this,event,100);"
                       maxlength="100" class="infraText form-control" maxlength="30" <?= $disabled ?>
                       value="<?= PaginaSEI::tratarHTML($txtNome) ?>">
            </div>
        </div>


        <?php
        PaginaSEI::getInstance()->fecharAreaDados();
        ?>
        <input type="hidden" id="hdnIdTpPoder" name="hdnIdTpPoder"
               value="<?php echo $idPoder ?>"/>
    </form>

<?
PaginaSEI::getInstance()->fecharBody();
PaginaSEI::getInstance()->fecharHtml();
?>