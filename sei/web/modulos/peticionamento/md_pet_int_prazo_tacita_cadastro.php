<?
/**
 * TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
 *
 * 06/12/2016 - criado por Wilton Júnior
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

    PaginaSEI::getInstance()->verificarSelecao('md_pet_int_prazo_tacita_selecionar');

    SessaoSEI::getInstance()->validarPermissao($_GET['acao']);

    $objMdPetIntPrazoTacitaDTO = new MdPetIntPrazoTacitaDTO();

    $strDesabilitar = '';

    $arrComandos = array();
    $strTitulo = '';

    switch ($_GET['acao']) {
        case 'md_pet_int_prazo_tacita_cadastrar':
            $strTitulo = 'Nov ';
            $arrComandos[] = '<button type="submit" accesskey="S" name="sbmCadastrarMdPetIntPrazoTacita" value="Salvar" class="infraButton"><span class="infraTeclaAtalho">S</span>alvar</button>';
            $arrComandos[] = '<button type="button" accesskey="C" name="btnCancelar" id="btnCancelar" value="Fechar" onclick="location.href=\'' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . PaginaSEI::getInstance()->getAcaoRetorno() . '&acao_origem=' . $_GET['acao']) . '\';" class="infraButton">Fe<span class="infraTeclaAtalho">c</span>har</button>';

            $objMdPetIntPrazoTacitaDTO->setNumIdMdPetIntPrazoTacita($_POST['txtIdMdPetIntPrazoTacita']);
            $objMdPetIntPrazoTacitaDTO->setNumNumPrazo($_POST['txtNumPrazo']);

            if (isset($_POST['sbmCadastrarMdPetIntPrazoTacita'])) {
                try {
                    $objMdPetIntPrazoTacitaRN = new MdPetIntPrazoTacitaRN();
                    $objMdPetIntPrazoTacitaDTO = $objMdPetIntPrazoTacitaRN->cadastrar($objMdPetIntPrazoTacitaDTO);
                    PaginaSEI::getInstance()->adicionarMensagem(' "' . $objMdPetIntPrazoTacitaDTO->getNumNumPrazo() . '" cadastrad com sucesso.');
                    header('Location: ' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . PaginaSEI::getInstance()->getAcaoRetorno() . '&acao_origem=' . $_GET['acao'] . PaginaSEI::getInstance()->montarAncora()));
                    die;
                } catch (Exception $e) {
                    PaginaSEI::getInstance()->processarExcecao($e);
                }
            }
            break;

        case 'md_pet_int_prazo_tacita_alterar':
            $strTitulo = 'Prazo para Intimação Tácita';
            $arrComandos[] = '<button type="submit" accesskey="S" name="sbmAlterarMdPetIntPrazoTacita" value="Salvar" class="infraButton"><span class="infraTeclaAtalho">S</span>alvar</button>';
            $strDesabilitar = 'disabled="disabled"';

            $arrComandos[] = '<button type="button" accesskey="C" name="btnCancelar" id="btnCancelar" value="Fechar" onclick="location.href=\'' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . PaginaSEI::getInstance()->getAcaoRetorno() . '&acao_origem=' . $_GET['acao']) . '\';" class="infraButton">Fe<span class="infraTeclaAtalho">c</span>har</button>';

            if (isset($_POST['sbmAlterarMdPetIntPrazoTacita'])) {

                try {
                    $objMdPetIntPrazoTacitaDTO->setNumIdMdPetIntPrazoTacita($_POST['txtIdMdPetIntPrazoTacita']);
                    $objMdPetIntPrazoTacitaDTO->setNumNumPrazo($_POST['txtNumPrazo']);
                    $objMdPetIntPrazoTacitaRN = new MdPetIntPrazoTacitaRN();
                    $objMdPetIntPrazoTacitaRN->alterar($objMdPetIntPrazoTacitaDTO);
                    PaginaSEI::getInstance()->adicionarMensagem(' "' . $objMdPetIntPrazoTacitaDTO->getNumNumPrazo() . '" alterado com sucesso.');
                    header('Location: ' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . $_GET['acao'] . '&acao_origem=' . $_GET['acao'] . PaginaSEI::getInstance()->montarAncora('')));
                    die;
                } catch (Exception $e) {
                    PaginaSEI::getInstance()->processarExcecao($e);
                }
            } else {
                $objMdPetIntPrazoTacitaDTO->retTodos();
                $objMdPetIntPrazoTacitaRN = new MdPetIntPrazoTacitaRN();
                $objMdPetIntPrazoTacitaDTO = $objMdPetIntPrazoTacitaRN->consultar($objMdPetIntPrazoTacitaDTO);
                if ($objMdPetIntPrazoTacitaDTO == null) {
                    throw new InfraException("Registro não encontrado.");
                }
            }
            break;

        case 'md_pet_int_prazo_tacita_consultar':
            $strTitulo = 'Consultar ';
            $arrComandos[] = '<button type="button" accesskey="F" name="btnFechar" value="Fechar" onclick="location.href=\'' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . PaginaSEI::getInstance()->getAcaoRetorno() . '&acao_origem=' . $_GET['acao'] . PaginaSEI::getInstance()->montarAncora()) . '\';" class="infraButton"><span class="infraTeclaAtalho">F</span>echar</button>';
            $objMdPetIntPrazoTacitaDTO->setBolExclusaoLogica(false);
            $objMdPetIntPrazoTacitaDTO->retTodos();
            $objMdPetIntPrazoTacitaRN = new MdPetIntPrazoTacitaRN();
            $objMdPetIntPrazoTacitaDTO = $objMdPetIntPrazoTacitaRN->consultar($objMdPetIntPrazoTacitaDTO);
            if ($objMdPetIntPrazoTacitaDTO === null) {
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
    <style>
<?}?>
        #lblNumPrazo {
            position: absolute;
            left: 0%;
            top: 0%;
            width: 100px;
        }

        #txtNumPrazo {
            position: absolute;
            left: 0%;
            top: 45%;
            width: 50px;
        }
        #imgAjuda { position:absolute;left:88px;top:0%; }

<? if (0){ ?>
    </style>
<? } ?>
<?
PaginaSEI::getInstance()->fecharStyle();
PaginaSEI::getInstance()->montarJavaScript();
PaginaSEI::getInstance()->abrirJavaScript();
?>
<? if (0){ ?>
    <script type="text/javascript"><?}?>

        function inicializar() {
            if ('<?=$_GET['acao']?>' == 'md_pet_int_prazo_tacita_alterar') {
                document.getElementById('txtNumPrazo').focus();
            } else if ('<?=$_GET['acao']?>' == 'md_pet_int_prazo_tacita_consultar') {
                infraDesabilitarCamposAreaDados();
            } else {
                document.getElementById('btnCancelar').focus();
            }
            infraEfeitoTabelas();
        }

        function validarCadastro() {
            if (infraTrim(document.getElementById('txtIdMdPetIntPrazoTacita').value) == '') {
                alert('Informe o Id.');
                document.getElementById('txtIdMdPetIntPrazoTacita').focus();
                return false;
            }

            var prazo = infraTrim(document.getElementById('txtNumPrazo').value);
            if (prazo == '' || prazo <= 0) {
                alert('Informe o Prazo.');
                document.getElementById('txtNumPrazo').focus();
                return false;
            }


            if(confirm('ATENÇÃO: Após iniciado o uso em produção, não alterar o prazo, pois afetará intimações em curso. Deseja continuar?')){
                return true;
            }else{
                return false;
            }


            return true;
        }

        function OnSubmitForm() {
            return validarCadastro();
        }

        <? if (0){ ?></script><? } ?>
<?
PaginaSEI::getInstance()->fecharJavaScript();
PaginaSEI::getInstance()->fecharHead();
PaginaSEI::getInstance()->abrirBody($strTitulo, 'onload="inicializar();"');
?>
    <form id="frmMdPetIntPrazoTacitaCadastro" method="post" onsubmit="return OnSubmitForm();"
          action="<?= SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . $_GET['acao'] . '&acao_origem=' . $_GET['acao']) ?>">
        <?
        PaginaSEI::getInstance()->montarBarraComandosSuperior($arrComandos);
        PaginaSEI::getInstance()->abrirAreaDados('4em');
        ?>
        <input type="hidden" id="txtIdMdPetIntPrazoTacita" name="txtIdMdPetIntPrazoTacita" onkeypress="return infraMascaraNumero(this, event)" class="infraText" value="<?= PaginaSEI::tratarHTML($objMdPetIntPrazoTacitaDTO->getNumIdMdPetIntPrazoTacita()); ?>" maxlength="11" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>

        <label id="lblNumPrazo" for="txtNumPrazo" accesskey="" class="infraLabelObrigatorio">Prazo em Dias:</label>
        <img src="<?= PaginaSEI::getInstance()->getDiretorioImagensGlobal() ?>/ajuda.gif" name="ajuda" id="imgAjuda" <?= PaginaSEI::montarTitleTooltip('O Prazo para Intimação Tácita é aquele que, a partir da data de expedição, caso o Destinatário não consulte os documentos diretamente no sistema, a intimação será considerada automaticamente cumprida por Decurso do Prazo Tácito.\n\n Para órgãos do Poder Executivo recomenda-se 15 dias (art. 23, § 2º, inciso III, alínea "a", do Decreto nº 70.235/1972) e para órgãos do Poder Judiciário recomenda-se 10 dias (art. 5º, § 3º, da Lei nº 11.419/2006).\n\n\n\n\n ATENÇÃO: Após iniciado o uso em produção, não alterar o prazo, pois afetará intimações em curso.') ?> class="infraImg"/>
        <input type="text" id="txtNumPrazo" name="txtNumPrazo" onkeypress="return infraMascaraNumero(this, event)" class="infraText" value="<?= PaginaSEI::tratarHTML($objMdPetIntPrazoTacitaDTO->getNumNumPrazo()); ?>" maxlength="2" size="15" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
        <?
        PaginaSEI::getInstance()->fecharAreaDados();
        ?>
    </form>
<?
PaginaSEI::getInstance()->fecharBody();
PaginaSEI::getInstance()->fecharHtml();
?>