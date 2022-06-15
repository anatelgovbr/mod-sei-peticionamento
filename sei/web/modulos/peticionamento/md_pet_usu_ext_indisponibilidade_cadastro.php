<?
/**
 * ANATEL
 *
 * 21/06/2016 - criado por marcelo.bezerra@cast.com.br - CAST
 *
 */

try {

    require_once dirname(__FILE__) . '/../../SEI.php';

    session_start();

    SessaoSEIExterna::getInstance()->validarSessao();
    PaginaPeticionamentoExterna::getInstance()->setTipoPagina(PaginaPeticionamentoExterna::$TIPO_PAGINA_SEM_MENU);
    PaginaPeticionamentoExterna::getInstance()->getBolAutoRedimensionar();

    $objMdPetIndisponibilidadeDTO = new MdPetIndisponibilidadeDTO();
    $objDocDTO = null;

    switch ($_GET['acao_externa']) {

        case 'md_pet_usu_ext_indisponibilidade_consultar':

            $urlBase = ConfiguracaoSEI::getInstance()->getValor('SEI', 'URL') . "/modulos/peticionamento/";
            $strLinkFinal = $urlBase . 'md_pet_usu_ext_indisponibilidade_lista.php?acao_externa=md_pet_usu_ext_indisponibilidade_listar';
            $strLinkFinal .= '&id_indisponibilidade_peticionamento=' . $_GET['id_indisponibilidade_peticionamento'];

            $strTitulo = 'Indisponibilidade do Sistema';
            $objMdPetIndisponibilidadeDTO->setNumIdIndisponibilidade($_GET['id_indisponibilidade_peticionamento']);
            $objMdPetIndisponibilidadeDTO->setBolExclusaoLogica(false);
            $objMdPetIndisponibilidadeDTO->retTodos();

            $objMdPetIndisponibilidadeRN = new MdPetIndisponibilidadeRN();
            $objMdPetIndisponibilidadeDTO = $objMdPetIndisponibilidadeRN->consultar($objMdPetIndisponibilidadeDTO);

            if ($objMdPetIndisponibilidadeDTO == null) {
                throw new InfraException("Registro não encontrado.");
            }

            break;

        default:
            throw new InfraException("Ação '" . $_GET['acao_externa'] . "' não reconhecida.");
    }

} catch (Exception $e) {
    PaginaPeticionamentoExterna::getInstance()->processarExcecao($e);
}

$hashAnexo = "";
$idAnexo = "";

//Na primeira vez que entrar na tela de geração de nova versão não deve processar os anexos (a tabela deve ser montada com os anexos do clone)
if (isset($_GET['id_indisponibilidade_peticionamento'])) {

    $objMdPetIndispDocRN = new MdPetIndisponibilidadeDocRN();
    $objMdPetIndispDocDTO = $objMdPetIndispDocRN->consultarIndisponibilidadeDocPorId(array($_GET['id_indisponibilidade_peticionamento'], false));
    $objDocDTO = $objMdPetIndispDocDTO;

}

PaginaPeticionamentoExterna::getInstance()->montarDocType();
PaginaPeticionamentoExterna::getInstance()->abrirHtml();
PaginaPeticionamentoExterna::getInstance()->abrirHead();
PaginaPeticionamentoExterna::getInstance()->montarMeta();
PaginaPeticionamentoExterna::getInstance()->montarTitle(':: ' . PaginaPeticionamentoExterna::getInstance()->getStrNomeSistema() . ' - ' . $strTitulo . ' ::');
PaginaPeticionamentoExterna::getInstance()->montarStyle();
PaginaPeticionamentoExterna::getInstance()->abrirStyle();
PaginaPeticionamentoExterna::getInstance()->fecharStyle();
PaginaPeticionamentoExterna::getInstance()->montarJavaScript();
PaginaPeticionamentoExterna::getInstance()->abrirJavaScript();
PaginaPeticionamentoExterna::getInstance()->fecharJavaScript();
?>
<style type="text/css">
    #fldProrrogacao {
        height: 10%;
        width: 86%;
    }

    .sizeFieldset {
        height: auto;
        width: 100%;
    }

    .fieldsetClear {
        border: none !important;
    }

    #divInfraBarraSistemaD {
        display: none;
    }

    a.ancoraPadraoAzul:hover,
    a.ancoraPadraoPreta:hover {
        text-decoration: underline;
    }

    a.ancoraPadraoAzul {
        padding: 0 .5em 0 .5em;
        text-decoration: none;
        font-size: 1.2em;
        color: #0066CC;
    }
</style>
<?

PaginaPeticionamentoExterna::getInstance()->fecharHead();
PaginaPeticionamentoExterna::getInstance()->abrirBody($strTitulo, 'onload="inicializar();"');
$urlBaseLink = "";
$qtdArrAcoesRemover = (is_array($arrAcoesRemover) ? count($arrAcoesRemover) : 0);
if ($qtdArrAcoesRemover > 0 || $_POST['hdnAnexos'] != "") {

    if ($_GET['acao_externa'] == 'md_pet_usu_ext_indisponibilidade_consultar') {
        $arrDados = explode("±", $_POST['hdnAnexos'], 3);
        array_push($arrAcoesRemover, $arrDados[0]);
    }

    foreach (array_keys($arrAcoesRemover) as $id) {
        $urlBase = ConfiguracaoSEI::getInstance()->getValor('SEI', 'URL') . "/modulos/peticionamento/";
        $urlBaseLink = $urlBase . "md_pet_usu_ext_indisponibilidade_cadastro.php?download=1&acao_externa=md_pet_usu_ext_indisponibilidade_download";
    }
}
?>
<form id="frmIndisponibilidadeCadastro" method="post" onsubmit="return OnSubmitForm();" action="">
    <?

    $arrComandos = array();
    $arrComandos[] = '<button type="button" accesskey="i" id="btnImprimir" value="Imprimir" onclick="imprimir();" class="infraButton"><span class="infraTeclaAtalho">I</span>mprimir</button>';
    $arrComandos[] = '<button type="button" accesskey="c" id="btnFechar" value="Fechar" onclick="fechar();" class="infraButton">Fe<span class="infraTeclaAtalho">c</span>har</button>';

    PaginaPeticionamentoExterna::getInstance()->montarBarraComandosSuperior($arrComandos);
    PaginaPeticionamentoExterna::getInstance()->abrirAreaDados('60em');
    ?>
    <div class="row">
        <div class="col-sm-12 col-md-12 col-lg-10 col-xl-6">
            <fieldset id="fldPeriodoIndisponibilidade" class="infraFieldset sizeFieldset form-control">
                <!--  Data Inicio  -->
                <legend class="infraLegend">&nbsp;Período de Indisponibilidade&nbsp;</legend>
                <label class="infraLabel">
                    <span style="font-weight: bold;">Início:</span> <?= $objMdPetIndisponibilidadeDTO->getDthDataInicioFormatada() ?>
                </label>
                <!--  Data Fim  -->
                <label class="infraLabel">
                    <span style="font-weight: bold;"> &nbsp; Fim:</span> <?= $objMdPetIndisponibilidadeDTO->getDthDataFimFormatada() ?>
                </label>

            </fieldset>
        </div>
    </div>
    <!-- Resumo da Indisponibilidade -->
    <div class="row">
        <div class="col-sm-12 col-md-12 col-lg-10 col-xl-6">
            <fieldset class="sizeFieldset infraFieldset form-control" style="margin-top: 11px; margin-bottom: 11px;">
                <legend class="infraLegend">&nbsp; Resumo da Indisponibilidade &nbsp;</legend>
                <label class="infraLabel">
                    <?php echo isset($objMdPetIndisponibilidadeDTO) ? $objMdPetIndisponibilidadeDTO->getStrResumoIndisponibilidade() : '' ?>
                </label>
            </fieldset>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12 col-md-12 col-lg-10 col-xl-6">
            <label id="fldProrrogacao" class="infraLabelObrigatorio">Indisponibilidade justificou prorrogação automática
                dos
                prazos:</label>
            <label class="infraLabel">
                <?php echo isset($objMdPetIndisponibilidadeDTO) && ($objMdPetIndisponibilidadeDTO->getStrSinProrrogacao() && $objMdPetIndisponibilidadeDTO->getStrSinProrrogacao() == 'S') ? 'Sim' : '' ?>
                <?php echo isset($objMdPetIndisponibilidadeDTO) && ($objMdPetIndisponibilidadeDTO->getStrSinProrrogacao() && $objMdPetIndisponibilidadeDTO->getStrSinProrrogacao() == 'N') ? 'Não' : '' ?>
            </label>
            <div>
                <label id="lblDescricao" class="infraLabelOpcional">
                    <ul>Observação: Conforme normativo próprio, algumas indisponibilidades justificam a prorrogação
                        automática
                        dos prazos externos de Intimações Eletrônicas que venceriam durante o período da
                        indisponibilidade,
                        prorrogando-os para o primeiro dia útil seguinte ao fim da respectiva indisponibilidade.
                    </ul>
                </label>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12 col-md-12 col-lg-10 col-xl-6">
            <div id="lblProrrogacao">

                <?php if (!is_null($objDocDTO)) {

                    $serieNumero = $objDocDTO->getStrNomeSerie();
                    $serieNumero .= $objDocDTO->getStrNumero() != '' ? ' ' . $objDocDTO->getStrNumero() : '';
                    $urlDocumento = $objDocDTO->getStrUrlDocumento();
                    $html = "<a class=\"ancoraPadraoAzul\" title=\"" . $serieNumero . "\" style=\"font-size:12.4px\" class=\"ancoraPadraoAzul\" onclick=\"window.open('" . $urlDocumento . "')\"> " . $objDocDTO->getStrNomeDocFormatado() . " </a>";
                    ?>
                    <label class="infraLabelObrigatorio">
                        Documento:
                    </label>
                    <?php echo $html; ?>
                <?php } ?>


            </div>
        </div>
    </div>

</form>

<input type="hidden" id="hdnIdIndisponibilidadePeticionamento" name="hdnIdIndisponibilidadePeticionamento"
       value="<?php echo isset($_GET['id_indisponibilidade_peticionamento']) ? $_GET['id_indisponibilidade_peticionamento'] : '' ?>"/>

<input type="hidden" id="hdnSinProrrogacao" name="hdnSinProrrogacao" value=""/>
<input type="hidden" id="hdnNomeArquivoDownload" name="hdnNomeArquivoDownload" value=""/>
<input type="hidden" id="hdnNomeArquivoDownloadReal" name="hdnNomeArquivoDownloadReal" value=""/>
<?
PaginaPeticionamentoExterna::getInstance()->fecharAreaDados();
PaginaPeticionamentoExterna::getInstance()->fecharBody();
PaginaPeticionamentoExterna::getInstance()->fecharHtml();

$urlBase = ConfiguracaoSEI::getInstance()->getValor('SEI', 'URL');
$strLink = $urlBase . '/modulos/peticionamento/md_pet_usu_ext_indisponibilidade_lista.php?acao_externa=md_pet_usu_ext_indisponibilidade_listar&id_orgao_acesso_externo=0&id_indisponibilidade_peticionamento=' . $_GET['id_indisponibilidade_peticionamento'] . PaginaSEI::getInstance()->montarAncora($_GET['id_indisponibilidade_peticionamento']);
?>
<script type="text/javascript">

    function fechar() {
        document.location = '<?= $strLink ?>';
    }

    function imprimir() {
        document.getElementById('btnFechar').style.display = 'none';
        document.getElementById('btnImprimir').style.display = 'none';
        infraImprimirDiv('divInfraAreaTelaD');

        self.setTimeout(function () {
            document.getElementById('btnFechar').style.display = '';
            document.getElementById('btnImprimir').style.display = '';
        }, 1000);
    }

    function inicializar() {

        if ('<?=$_GET['acao_externa']?>' == 'md_pet_usu_ext_indisponibilidade_consultar') {
            infraDesabilitarCamposAreaDados();
        } else {
            document.getElementById('btnFechar').focus();
        }

        infraEfeitoTabelas();

    }

    function returnDateTime(valor) {

        valorArray = valor != '' ? valor.split(" ") : '';

        if (Array.isArray(valorArray)) {
            var data = valorArray[0]
            data = data.split('/');
            var mes = parseInt(data[1]) - 1;
            var horas = valorArray[1].split(':');

            var segundos = typeof horas[2] != 'undefined' ? horas[2] : 00;
            var dataCompleta = new Date(data[2], mes, data[0], horas[0], horas[1], segundos);
            return dataCompleta;
        }

        return false;
    }

    function preencherHdnProrrogacao() {
        var rdProrrogacao = document.getElementsByName('rdProrrogacao[]')[0].checked ? 'S' : '';

        if (rdProrrogacao == '') {
            rdProrrogacao = document.getElementsByName('rdProrrogacao[]') [1].checked ? 'N' : '';
        }

        document.getElementById('hdnSinProrrogacao').value = rdProrrogacao;
    }

    function OnSubmitForm() {
        return true;
    }
</script>