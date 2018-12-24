<?
/**
 * ANATEL
 *
 * 15/02/2016 - criado por jaqueline.mendes@cast.com.br - CAST
 *
 */

try {

    require_once dirname(__FILE__) . '/../../SEI.php';

    session_start();

    //////////////////////////////////////////////////////////////////////////////
    InfraDebug::getInstance()->setBolLigado(false);
    InfraDebug::getInstance()->setBolDebugInfra(false);
    InfraDebug::getInstance()->limpar();
    //////////////////////////////////////////////////////////////////////////////

    SessaoSEI::getInstance()->validarLink();

    $strLinkAjaxValidacoesNumeroSEI = SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=md_pet_indisp_validar_num_sei');
    $strUrlAjaxValidacaoPeridoDta = SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=md_pet_indisp_validar_periodo');

    $arrGrid = array();
    $strGrid = '';

    SessaoSEI::getInstance()->validarPermissao($_GET['acao']);

    $objMdPetIndisponibilidadeDocRN = new MdPetIndisponibilidadeDocRN();
    $objMdPetIndisponibilidadeDTO = new MdPetIndisponibilidadeDTO();
    $idDocumento = '';
    $idIndspDoc = 0;
    $objDTOIndDoc = null;
    $isConsultar = false;
    $isAlterar = false;
    $isDisabled = 0;
    $textoTolTipSim = 'Após salvar a Nova Indisponibilidade do SEI, NÃO será possível alterar o campo de Sim  para Não e também não será possível alterar o Período de Indisponibilidade';

    switch ($_GET['acao']) {

        case 'md_pet_indisponibilidade_upload_anexo':
            if (isset($_FILES['filArquivo'])) {
                PaginaSEI::getInstance()->processarUpload('filArquivo', DIR_SEI_TEMP, false);
            }
            die;

        case 'md_pet_indisponibilidade_cadastrar':

            $strTitulo = 'Nova Indisponibilidade do SEI';

            $arrComandos[] = '<button type="submit" accesskey="s" name="sbmCadastrarIndisponibilidadePeticionamento" id="sbmCadastrarIndisponibilidadePeticionamento" value="Salvar" class="infraButton"><span class="infraTeclaAtalho">S</span>alvar</button>';
            $arrComandos[] = '<button type="button" accesskey="c" name="btnCancelar" id="btnCancelar" value="Cancelar" onclick="location.href=\'' . PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . PaginaSEI::getInstance()->getAcaoRetorno() . '&acao_origem=' . $_GET['acao'])) . '\';" class="infraButton"><span class="infraTeclaAtalho">C</span>ancelar</button>';

            $dateIni = isset($_POST['txtDtInicio']) && $_POST['txtDtInicio'] != '' ? $_POST['txtDtInicio'] . ':00' : $_POST['txtDtInicio'];
            $dateFim = isset($_POST['txtDtFim']) && $_POST['txtDtFim'] != '' ? $_POST['txtDtFim'] . ':00' : $_POST['txtDtFim'];

            $objMdPetIndisponibilidadeDTO = new MdPetIndisponibilidadeDTO();
            $objMdPetIndisponibilidadeDTO->setNumIdIndisponibilidade(null);
            $objMdPetIndisponibilidadeDTO->setDthDataInicio($dateIni);
            $objMdPetIndisponibilidadeDTO->setDthDataFim($dateFim);
            $objMdPetIndisponibilidadeDTO->setStrResumoIndisponibilidade($_POST['txtResumoIndisponibilidade']);
            $objMdPetIndisponibilidadeDTO->setStrSinProrrogacao($_POST['hdnSinProrrogacao']);

            if (isset($_POST['sbmCadastrarIndisponibilidadePeticionamento'])) {

                try {

                    $objMdPetIndisponibilidadeRN = new MdPetIndisponibilidadeRN();
                    $objMdPetIndisponibilidadeDTO = $objMdPetIndisponibilidadeRN->cadastrar($objMdPetIndisponibilidadeDTO);
                    PaginaSEI::getInstance()->adicionarMensagem('Os dados cadastrados foram salvos com sucesso.');
                    header('Location: ' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . PaginaSEI::getInstance()->getAcaoRetorno() . '&acao_origem=' . $_GET['acao'] . '&id_indisponibilidade_peticionamento=' . $objMdPetIndisponibilidadeDTO->getNumIdIndisponibilidade() . PaginaSEI::getInstance()->montarAncora($objMdPetIndisponibilidadeDTO->getNumIdIndisponibilidade())));
                    die;
                } catch (Exception $e) {
                    PaginaSEI::getInstance()->processarExcecao($e);
                }
            }
            break;

        case 'md_pet_indisponibilidade_alterar':
            $strTitulo = 'Alterar Indisponibilidade do SEI';
            $isAlterar = true;
            $arrComandos[] = '<button type="submit" accesskey="s" name="sbmAlterarIndisponibilidadePeticionamento" value="Salvar" class="infraButton"><span class="infraTeclaAtalho">S</span>alvar</button>';
            $arrComandos[] = '<button type="button" accesskey="c" name="btnCancelar" id="btnCancelar" value="Cancelar" onclick="location.href=\'' . PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . PaginaSEI::getInstance()->getAcaoRetorno() . '&acao_origem=' . $_GET['acao'] . PaginaSEI::getInstance()->montarAncora($_GET['id_indisponibilidade_peticionamento']))) . '\';" class="infraButton"><span class="infraTeclaAtalho">C</span>ancelar</button>';

            $strDesabilitar = 'disabled="disabled"';

            $idIndisponibilidade = array_key_exists('id_indisponibilidade_peticionamento', $_GET) ? $_GET['id_indisponibilidade_peticionamento'] : $_POST['hdnIdIndisponibilidadePeticionamento'];
            $objMdPetIndisponibilidadeDTO->setNumIdIndisponibilidade($idIndisponibilidade);
            $objMdPetIndisponibilidadeDTO->retTodos();
            $objMdPetIndisponibilidadeRN = new MdPetIndisponibilidadeRN();
            $objMdPetIndisponibilidadeDTO = $objMdPetIndisponibilidadeRN->consultar($objMdPetIndisponibilidadeDTO);
            $sinProrrogSim = isset($objMdPetIndisponibilidadeDTO) && ($objMdPetIndisponibilidadeDTO->getStrSinProrrogacao() && $objMdPetIndisponibilidadeDTO->getStrSinProrrogacao() == 'S');

            if (isset($_GET['id_indisponibilidade_peticionamento'])) {

                $dataInicioShow = substr($objMdPetIndisponibilidadeDTO->getDthDataInicio(), 0, -3);
                $dataFimShow = substr($objMdPetIndisponibilidadeDTO->getDthDataFim(), 0, -3);

                $objMdPetIndisponibilidadeDTO->setDthDataInicio($dataInicioShow);
                $objMdPetIndisponibilidadeDTO->setDthDataFim($dataFimShow);

                $objDTO = $objMdPetIndisponibilidadeDocRN->consultarIndisponibilidadeDocPorId(array($objMdPetIndisponibilidadeDTO->getNumIdIndisponibilidade()));

                if (!is_null($objDTO)) {
                    $linkDocumento = "window.open('" . $objDTO->getStrUrlDocumento() . "')";
                    $serieNumero = $objDTO->getStrNomeSerie();
                    $serieNumero .= $objDTO->getStrNumero() != '' ? ' ' . $objDTO->getStrNumero() : '';
                    $html = "<div style=\"text-align:center\"> <a title=\"" . $serieNumero . "\" style=\"font-size:12.4px\" class=\"ancoraPadraoAzul\" onclick=\"" . $linkDocumento . "\"> " . $objDTO->getStrNomeDocFormatado() . " </a></div>";
                    $arrGrid[] = array($objDTO->getNumIdProtPeticionamento(), $objDTO->getDblIdDocumento(), htmlentities($html), $objDTO->getStrNomeUnidadeFormatada(), $objDTO->getDtaInclusaoDta());
                    $strGrid = PaginaSEI::getInstance()->gerarItensTabelaDinamica($arrGrid);
                    $idDocumento = $objDTO->getDblIdDocumento();
                    $idIndspDoc = $objDTO->getNumIdProtPeticionamento();
                    $objDTOIndDoc = $objDTO;
                }

                if ($objMdPetIndisponibilidadeDTO == null) {
                    throw new InfraException("Registro não encontrado.");
                }

            }

            if (isset($_POST['sbmAlterarIndisponibilidadePeticionamento'])) {

                try {

                    $objMdPetIndisponibilidadeDTOAlt = new MdPetIndisponibilidadeDTO();
                    $objMdPetIndisponibilidadeDTOAlt->setNumIdIndisponibilidade($_POST['hdnIdIndisponibilidadePeticionamento']);

                    $dateIni = isset($_POST['txtDtInicio']) && $_POST['txtDtInicio'] != '' ? $_POST['txtDtInicio'] . ':00' : $_POST['txtDtInicio'];
                    $dateFim = isset($_POST['txtDtFim']) && $_POST['txtDtFim'] != '' ? $_POST['txtDtFim'] . ':00' : $_POST['txtDtFim'];

                    if ($_POST['hdnDadosAlterados'] == '1') {

                        $objIndispDocDTO = new MdPetIndisponibilidadeDocDTO();
                        $objIndispDocDTO->setNumIdIndisponibilidade($objMdPetIndisponibilidadeDTOAlt->getNumIdIndisponibilidade());
                        $objIndispDocDTO->retTodos();
                        $count = $objMdPetIndisponibilidadeDocRN->contar($objIndispDocDTO);

                        if ($count > 0) {
                            $arrIndispDocDTO = $objMdPetIndisponibilidadeDocRN->listar($objIndispDocDTO);
                            $objMdPetIndisponibilidadeDocRN->excluir($arrIndispDocDTO);
                        }
                    }

                    //Somente se o Sin prorrogação For Não irá settar esses campos com os valores da tela, se não setta os antigos para validações.
                    if ($sinProrrogSim) {
                        $objMdPetIndisponibilidadeDTOAlt->setDthDataInicio($objMdPetIndisponibilidadeDTO->getDthDataInicio());
                        $objMdPetIndisponibilidadeDTOAlt->setDthDataFim($objMdPetIndisponibilidadeDTO->getDthDataFim());
                        $objMdPetIndisponibilidadeDTOAlt->setStrSinProrrogacao($objMdPetIndisponibilidadeDTO->getStrSinProrrogacao());
                    } else {
                        $objMdPetIndisponibilidadeDTOAlt->setDthDataInicio($dateIni);
                        $objMdPetIndisponibilidadeDTOAlt->setDthDataFim($dateFim);
                        $objMdPetIndisponibilidadeDTOAlt->setStrSinProrrogacao($_POST['hdnSinProrrogacao']);
                    }

                    $objMdPetIndisponibilidadeDTOAlt->setStrResumoIndisponibilidade($_POST['txtResumoIndisponibilidade']);

                    $objMdPetIndisponibilidadeRN = new MdPetIndisponibilidadeRN();
                    $objMdPetIndisponibilidadeRN->alterar($objMdPetIndisponibilidadeDTOAlt);

                    PaginaSEI::getInstance()->adicionarMensagem('Os dados foram alterados com sucesso.');
                    header('Location: ' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . PaginaSEI::getInstance()->getAcaoRetorno() . '&acao_origem=' . $_GET['acao'] . '&id_indisponibilidade_peticionamento=' . $objMdPetIndisponibilidadeDTO->getNumIdIndisponibilidade() . PaginaSEI::getInstance()->montarAncora($objMdPetIndisponibilidadeDTO->getNumIdIndisponibilidade())));
                    die;

                } catch (Exception $e) {
                    PaginaSEI::getInstance()->setBolAutoRedimensionar(false);
                    PaginaSEI::getInstance()->setTipoPagina(InfraPagina::$TIPO_PAGINA_COMPLETA);
                    PaginaSEI::getInstance()->processarExcecao($e);
                }

            }

            break;

        case 'md_pet_indisponibilidade_consultar':
            $strTitulo = 'Consultar Indisponibilidade do SEI';
            $isConsultar = true;
            $arrComandos[] = '<button type="button" accesskey="c" name="btnCancelar" id="btnCancelar" value="Cancelar" onclick="location.href=\'' . PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . PaginaSEI::getInstance()->getAcaoRetorno() . '&acao_origem=' . $_GET['acao'] . PaginaSEI::getInstance()->montarAncora($_GET['id_indisponibilidade_peticionamento']))) . '\';" class="infraButton">Fe<span class="infraTeclaAtalho">c</span>har</button>';

            $objMdPetIndisponibilidadeDTO->setNumIdIndisponibilidade($_GET['id_indisponibilidade_peticionamento']);
            $objMdPetIndisponibilidadeDTO->setBolExclusaoLogica(false);
            $objMdPetIndisponibilidadeDTO->retTodos();

            $objMdPetIndisponibilidadeRN = new MdPetIndisponibilidadeRN();
            $objMdPetIndisponibilidadeDTO = $objMdPetIndisponibilidadeRN->consultar($objMdPetIndisponibilidadeDTO);

            $objDTO = $objMdPetIndisponibilidadeDocRN->consultarIndisponibilidadeDocPorId(array($objMdPetIndisponibilidadeDTO->getNumIdIndisponibilidade()));

            if (!is_null($objDTO)) {
                $linkDocumento = "window.open('" . $objDTO->getStrUrlDocumento() . "')";
                $serieNumero = $objDTO->getStrNomeSerie();
                $serieNumero .= $objDTO->getStrNumero() != '' ? ' ' . $objDTO->getStrNumero() : '';
                $html = "<div style=\"text-align:center\"> <a title=\"" . $serieNumero . "\" style=\"font-size:12.4px\" class=\"ancoraPadraoAzul\" onclick=\"" . $linkDocumento . "\"> " . $objDTO->getStrNomeDocFormatado() . " </a></div>";

                $arrGrid[] = array($objDTO->getNumIdProtPeticionamento(), $objDTO->getDblIdDocumento(), htmlentities($html), $objDTO->getStrNomeUnidadeFormatada(), $objDTO->getDtaInclusaoDta());
                $strGrid = PaginaSEI::getInstance()->gerarItensTabelaDinamica($arrGrid);
                $idDocumento = $objDTO->getDblIdDocumento();
                $idIndspDoc = $objDTO->getNumIdProtPeticionamento();
                $objDTOIndDoc = $objDTO;
            }

            if ($objMdPetIndisponibilidadeDTO === null) {
                throw new InfraException("Registro não encontrado.");
            }
            break;

        default:
            throw new InfraException("Ação '" . $_GET['acao'] . "' não reconhecida.");
    }


} catch (Exception $e) {
    PaginaSEI::getInstance()->processarExcecao($e);
}

$hashAnexo = "";
$idAnexo = "";

//na primeira vez que entrar na tela de geração de nova versão não deve processar os anexos (a tabela deve ser montada com os anexos do clone)
if (isset($_GET['id_indisponibilidade_peticionamento'])) {
    $sinProrrogSim = isset($objMdPetIndisponibilidadeDTO) && ($objMdPetIndisponibilidadeDTO->getStrSinProrrogacao() && $objMdPetIndisponibilidadeDTO->getStrSinProrrogacao() == 'S');
}


PaginaSEI::getInstance()->montarDocType();
PaginaSEI::getInstance()->abrirHtml();
PaginaSEI::getInstance()->abrirHead();
PaginaSEI::getInstance()->montarMeta();
PaginaSEI::getInstance()->montarTitle(':: ' . PaginaSEI::getInstance()->getStrNomeSistema() . ' - ' . $strTitulo . ' ::');
PaginaSEI::getInstance()->montarStyle();
PaginaSEI::getInstance()->abrirStyle();

PaginaSEI::getInstance()->fecharStyle();
PaginaSEI::getInstance()->montarJavaScript();
PaginaSEI::getInstance()->abrirJavaScript();
?>


<?
PaginaSEI::getInstance()->fecharJavaScript();
?>
<style type="text/css">

    #lblDtInicio {
        position: absolute;
        left: 2%;
        top: 4.2%;
        width: 12%;
    }

    #txtDtInicio {
        position: absolute;
        left: 2%;
        top: 7%;
        width: 12%;
    }

    #imgDtInicio {
        position: absolute;
        left: 15%;
        top: 7%;
    }

    #lblDtFim {
        position: absolute;
        left: 19.5%;
        top: 4.2%;
        width: 12%;
    }

    #txtDtFim {
        position: absolute;
        left: 19.5%;
        top: 7%;
        width: 12%;
    }

    #imgDtFim {
        position: absolute;
        left: 32.5%;
        top: 7%;
    }

    #lblResumoIndisponibilidade {
        position: absolute;
        left: 0%;
        top: 15.5%;
        width: 25%;
    }

    #txtResumoIndisponibilidade {
        position: absolute;
        left: 0%;
        top: 18%;
        width: 75%;
    }

    #fldProrrogacao {
        height: 10%;
        width: 86%;
    }

    .sizeFieldset {
        height: 12.5%;
        width: 86%;
    }

    .fieldsetClear {
        border: none !important;
    }

    .bloco {
        position: relative;
        float: left;
    }

</style>
<?php
PaginaSEI::getInstance()->fecharHead();
PaginaSEI::getInstance()->abrirBody($strTitulo, 'onload="inicializar();"');
?>
<form id="frmIndisponibilidadeCadastro" method="post" onsubmit="return OnSubmitForm();"
      action="<?= PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . $_GET['acao'] . '&acao_origem=' . $_GET['acao'])) ?>">
    <?
    PaginaSEI::getInstance()->montarBarraComandosSuperior($arrComandos);
    PaginaSEI::getInstance()->abrirAreaDados('60em');
    ?>
    <?php $disabledAlterar = $isAlterar && $sinProrrogSim ? 'disabled = "disabled"' : '';
    $isDisabled = $disabledAlterar != '' ? 1 : 0;
    ?>
    <fieldset id="fldPeriodoIndisponibilidade" class="infraFieldset sizeFieldset">

        <!--  Data Inicio  -->
        <legend class="infraLegend">&nbsp;Período de Indisponibilidade&nbsp;</legend>
        <label id="lblDtInicio" for="txtDtInicio" class="infraLabelObrigatorio">Início:</label>
        <input <?php echo $disabledAlterar ?> type="text" onchange="validDate('I')"
                                              onkeypress="return infraMascara(this, event, '##/##/#### ##:##');"
                                              name="txtDtInicio" id="txtDtInicio"
                                              value="<?= PaginaSEI::tratarHTML($objMdPetIndisponibilidadeDTO->getDthDataInicioFormatada()) ?>"
                                              class="infraText"/>
        <img src="<?= PaginaSEI::getInstance()->getDiretorioImagensGlobal() ?>/calendario.gif" id="imgDtInicio"
             title="Selecionar Data/Hora Inicial" alt="Selecionar Data/Hora Inicial" class="infraImg"
             onclick="infraCalendario('txtDtInicio',this,true,'<?= InfraData::getStrDataAtual() . ' 00:00' ?>');"/>

        <!--  Data Fim  -->
        <label id="lblDtFim" for="txtDtFim" class="infraLabelObrigatorio">Fim:</label>
        <input <?php echo $disabledAlterar ?> type="text" onchange="validDate('F')"
                                              onkeypress="return infraMascara(this, event, '##/##/#### ##:##');"
                                              name="txtDtFim" id="txtDtFim"
                                              value="<?= PaginaSEI::tratarHTML($objMdPetIndisponibilidadeDTO->getDthDataFimFormatada()) ?>"
                                              class="infraText"/>
        <img src="<?= PaginaSEI::getInstance()->getDiretorioImagensGlobal() ?>/calendario.gif" id="imgDtFim"
             title="Selecionar Data/Hora Final"
             alt="Selecionar Data/Hora Final" class="infraImg"
             onclick="infraCalendario('txtDtFim',this,true,'<?= InfraData::getStrDataAtual() . ' 23:59' ?>');"/>

    </fieldset>

    <!-- Resumo da Indisponibilidade -->

    <fieldset class="sizeFieldset fieldsetClear">
        <label id="lblResumoIndisponibilidade" for="txtResumoIndisponibilidade" class="infraLabelObrigatorio">Resumo da
            Indisponibilidade:</label>
        <textarea type="text" maxlength="500" id="txtResumoIndisponibilidade" rows="3" name="txtResumoIndisponibilidade"
                  class="infraText" onkeypress="return infraMascaraTexto(this,event,500);"
                  tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"><?php echo isset($objMdPetIndisponibilidadeDTO) ? PaginaSEI::tratarHTML($objMdPetIndisponibilidadeDTO->getStrResumoIndisponibilidade()) : '' ?></textarea>
    </fieldset>

    <fieldset id="fldProrrogacao" class="infraFieldset">
        <legend class="infraLegend">Indisponibilidade justifica a prorrogação automática dos prazos</legend>

        <div id="divProrrogacaoSim" style="margin-top:0.3%">
            <input <?php echo isset($objMdPetIndisponibilidadeDTO) && ($objMdPetIndisponibilidadeDTO->getStrSinProrrogacao() && $objMdPetIndisponibilidadeDTO->getStrSinProrrogacao() == 'S') ? 'checked="checked" ' : '';
            echo $disabledAlterar; ?> type="radio" id="rdProrrogacaoSim" name="rdProrrogacao[]"/> <label
                id="lblProrrogacaoSim" class="infraLabelCheckbox" for="rdProrrogacaoSim">Sim</label>
            <img style="margin-bottom:-4px;" id="imgAjuda" src="/infra_css/imagens/ajuda.gif" name="ajuda"
                 onmouseout="return infraTooltipOcultar();"
                 onmouseover="return infraTooltipMostrar('<?= $textoTolTipSim; ?>');"/>
        </div>
        <div id="divProrrogacaoNao" style="margin-top:0.2%">
            <input <?php echo isset($objMdPetIndisponibilidadeDTO) && ($objMdPetIndisponibilidadeDTO->getStrSinProrrogacao() && $objMdPetIndisponibilidadeDTO->getStrSinProrrogacao() == 'N') ? 'checked="checked" ' : '';
            echo $disabledAlterar; ?> type="radio" id="rdProrrogacaoNao" name="rdProrrogacao[]"/> <label
                id="lblProrrogacaoNao" class="infraLabelCheckbox" for="rdProrrogacaoNao">Não</label>
        </div>

    </fieldset>

    <div style="clear:both"></div>
    <fieldset style="margin-top: 24px;width: 86%;" id="fldDocumento" class="infraFieldset">
        <legend class="infraLegend"> Anexar Documento</legend>
        <div class="bloco" style="width: 230px; margin-left: 5px;">
            <br/>
            <label id="lblNumeroSei" for="txtNumeroSei" accesskey="f" class="infraLabelOpcional">
                Número SEI:
            </label>

            <input onchange="removerValidacaoDocumento();" type="text" id="txtNumeroSei" name="txtNumeroSei"
                   class="infraText"
                   onkeyup="infraMascaraNumero(this, event, 100);" maxlength="100"
                   style="width:170px;"
                   tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>" value="<?= PaginaSEI::tratarHTML($txtNumeroSei) ?>"/>

            <button type="button" <?php echo ($isConsultar) ? 'disabled="disabled"' : '' ?> accesskey="V"
                    id="btnValidar" onclick="controlarNumeroSEI();" class="infraButton">
                <span class="infraTeclaAtalho">V</span>alidar
            </button>
        </div>

        <div class="bloco" style="width: 220px; margin-left: 5%">
            <br/>
            <label id="lblTipo" for="txtTipo" accesskey="f" class="infraLabelOpcional">
                Tipo:
            </label>
            <br/>
            <input type="text" id="txtTipo" name="txtTipo" class="infraText"
                   readonly="readonly"
                   tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>" value="<?= PaginaSEI::tratarHTML($txtTipo) ?>"/>

            <button type="button" id="btnAdicionar" style="display: none" accesskey="A" onclick="adicionarDocumento()"
                    class="infraButton">
                <span class="infraTeclaAtalho">A</span>dicionar
            </button>

        </div>

        <div id="divAnexos" style="margin-bottom: 1%;margin-top:7%">

            <table id="tbDocumento" name="tbDocumento" class="infraTable" summary="Documentos"
                   style="<?php echo count($arrGrid) == 0 ? 'display:none; width:90%;' : 'width:90%;' ?>">

                <caption
                    class="infraCaption">    <?= PaginaSEI::getInstance()->gerarCaptionTabela("Documentos", 0) ?> </caption>

                <tr>
                    <th style="display:none;">Pk Tabela</th>
                    <th style="display:none;">ID Documento</th>
                    <th width="30%" class="infraTh">Documento</th>
                    <th width="30%" class="infraTh" align="center">Unidade</th>
                    <th width="15%" class="infraTh" align="center">Data</th>
                    <th width="10%" class="infraTh">Ações</th>
                </tr>


            </table>
            <!-- Auxiliares da tabela -->
            <?php $tipoGrid = !is_null($objDTOIndDoc) ? $objDTO->getStrNomeSerie() : '';
            $tipoGrid .= $tipoGrid != '' ? ' ' . $objDTOIndDoc->getStrNumero() : '';
            $nomeUnidade = SessaoSEI::getInstance()->getStrSiglaUnidadeAtual() . ' - ' . SessaoSEI::getInstance()->getStrDescricaoUnidadeAtual();
            ?>

            <input type="hidden" name="hdnDataAtualGrid" id="hdnDataAtualGrid"
                   value="<?php echo InfraData::getStrDataAtual(); ?>"/>
            <input type="hidden" name="hdnUnidadeAtualGrid" id="hdnUnidadeAtualGrid"
                   value="<?php echo $nomeUnidade; ?>"/>
            <input type="hidden" name="hdnTipoDocumentoGrid" id="hdnTipoDocumentoGrid"
                   value="<?php echo $tipoGrid; ?>"/>
            <input type="hidden" name="hdnNumDocumentoGrid" id="hdnNumDocumentoGrid"
                   value="<?php echo !is_null($objDTOIndDoc) ? $objDTOIndDoc->getStrProtocoloFormatadoDocumento() : ''; ?>"/>
            <input type="hidden" name="hdnPkTabela" id="hdnPkTabela" value="<?php echo $idIndspDoc; ?>"/>
            <input type="hidden" name="hdnIdDocumento" id="hdnIdDocumento" value="<?= $idDocumento ?>"/>
            <input type="hidden" name="hdnTbDocumento" id="hdnTbDocumento" value="<?php echo $strGrid ?>"/>
            <input type="hidden" name="hdnIsAlteracaoGrid" id="hdnIsAlteracaoGrid" value="0"/>
        </div>

    </fieldset>

</form>

<fieldset class="fieldsetClear">


</fieldset>

<input type="hidden" id="hdnUrlDocumento" name="hdnUrlDocumento"
       value="<?php echo !is_null($objDTOIndDoc) ? $objDTOIndDoc->getStrUrlDocumento() : ''; ?>"/>
<input type="hidden" id="hdnDadosAlterados" name="hdnDadosAlterados" value="0"/>
<input type="hidden" id="hdnIdIndisponibilidadePeticionamento" name="hdnIdIndisponibilidadePeticionamento"
       value="<?php echo isset($_GET['id_indisponibilidade_peticionamento']) ? $_GET['id_indisponibilidade_peticionamento'] : '' ?>"/>

<input type="hidden" id="hdnSinProrrogacao" name="hdnSinProrrogacao" value=""/>


<?
PaginaSEI::getInstance()->fecharAreaDados();
PaginaSEI::getInstance()->fecharBody();
PaginaSEI::getInstance()->fecharHtml();
?>
<script type="text/javascript">

    var objTabelaDocumento = null;

    //funcao para visualizar estruturas complexas (arrays, objetos) em JS
    function dump(arr, level) {

        var dumped_text = "";

        if (!level) level = 0;

        //The padding given at the beginning of the line.
        var level_padding = "";
        for (var j = 0; j < level + 1; j++) level_padding += "    ";

        if (typeof(arr) == 'object') { //Array/Hashes/Objects
            for (var item in arr) {
                var value = arr[item];

                if (typeof(value) == 'object') { //If it is an array,
                    dumped_text += level_padding + "'" + item + "' ...\n";
                    dumped_text += dump(value, level + 1);
                } else {
                    dumped_text += level_padding + "'" + item + "' => \"" + value + "\"\n";
                }
            }
        } else { //Stings/Chars/Numbers etc.
            dumped_text = "===>" + arr + "<===(" + typeof(arr) + ")";
        }
        return dumped_text;
    }

    function removerValidacaoDocumento() {
        document.getElementById('txtTipo').value = '';
        document.getElementById('btnAdicionar').style.display = 'none';
    }


    function inicializarGrid() {
        objTabelaDocumento = new infraTabelaDinamica('tbDocumento', 'hdnTbDocumento', true, true);
        objTabelaDocumento.gerarEfeitoTabela = true;

        objTabelaDocumento.procuraLinha = function (id) {
            var qtd;
            var linha;
            qtd = document.getElementById('tbDocumento').rows.length;

            for (i = 1; i < qtd; i++) {
                linha = document.getElementById('tbDocumento').rows[i];
                var valorLinha = $.trim(linha.cells[0].innerText);
                if (id) {
                    id = $.trim(id.value);
                    if (valorLinha == id) {
                        return i;
                    }
                } else {
                    return i;
                }
            }
            return null;
        };

        objTabelaDocumento.alterar = function (arr) {
            document.getElementById('hdnPkTabela').value = arr[0];
            document.getElementById('hdnIdDocumento').value = arr[1];
            document.getElementById('txtTipo').value = document.getElementById('hdnTipoDocumentoGrid').value;
            document.getElementById('hdnDataAtualGrid').value = arr[4];
            document.getElementById('hdnUnidadeAtualGrid').value = arr[3];
            document.getElementById('txtNumeroSei').value = document.getElementById('hdnNumDocumentoGrid').value;
            document.getElementById('hdnIsAlteracaoGrid').value = 1;
            document.getElementById('btnAdicionar').style.display = '';
        };

        objTabelaDocumento.remover = function () {
            document.getElementById('hdnIsAlteracaoGrid').value = 0;
            document.getElementById('hdnDadosAlterados').value = 1;
            document.getElementById('hdnIdDocumento').value = '';

            var qtd = document.getElementById('tbDocumento').rows.length;
            if (qtd == 2) {
                document.getElementById('tbDocumento').style.display = 'none';
            }

            return true;
        };

    }

    function controlarNumeroSEI() {
        var numeroSEI = $.trim(document.getElementById('txtNumeroSei').value);
        var qtd = document.getElementById('tbDocumento').rows.length;
        var isAlteracao = document.getElementById('hdnIsAlteracaoGrid').value == 1;

        if (numeroSEI == '') {
            alert('Preencha o Número SEI.');
            return false;
        } else if (qtd == 2 && !isAlteracao) {
            alert('Para adicionar um novo documento remova o que estava vinculado anteriormente, cada indisponibilidade deve possuir somente um documento.');
            return false;
        } else {
            validarNumeroSEI();
        }


    }


    function validarNumeroSEI() {

        objAjax = new infraAjaxComplementar(null, '<?=$strLinkAjaxValidacoesNumeroSEI?>');
        objAjax.limparCampo = false;
        objAjax.mostrarAviso = false;
        objAjax.tempoAviso = 1000;
        objAjax.async = false;

        objAjax.prepararExecucao = function () {
            var numeroSEI = document.getElementById('txtNumeroSei').value
            return 'numeroSEI=' + numeroSEI;
        };

        objAjax.processarResultado = function (arr) {
            //verifica se o documento foi assinado.
            if ('Assinatura' in arr) {
                alert(arr['Assinatura']);
                return false;
            } else {

                document.getElementById('hdnIdDocumento').value = arr['IdProtocolo'];
                document.getElementById('txtTipo').value = arr['Identificacao'];
                document.getElementById('hdnDataAtualGrid').value = arr['DataAtual'];
                document.getElementById('hdnUnidadeAtualGrid').value = arr['UnidadeAtual'];
                document.getElementById('txtNumeroSei').value = arr['ProtocoloFormatado'];
                document.getElementById('hdnTipoDocumentoGrid').value = arr['Identificacao'];
                document.getElementById('hdnNumDocumentoGrid').value = arr['ProtocoloFormatado'];
                document.getElementById('btnAdicionar').style.display = '';
                document.getElementById('hdnUrlDocumento').value = arr['UrlDocumento'];
            }
        };

        objAjax.executar();
    }


    function addLinkExibicaoDocumento(nomeDoc, tituloDoc) {
        var url = document.getElementById('hdnUrlDocumento').value

        var strLink = "window.open('" + url + "')";
        var html = '<a title="' + tituloDoc + '" style="font-size:12.4px" class="ancoraPadraoAzul" onclick ="' + strLink + '"> ' + nomeDoc + ' </a>';

        return html;
    }

    function adicionarDocumento() {

        var isVazioTipo = $.trim(document.getElementById('txtTipo').value) == '';

        if (isVazioTipo) {
            alert('É necessário Validar o número SEI antes de adicioná-lo.');
        } else {
            var nomeDocCompleto = document.getElementById('txtTipo').value + ' (' + document.getElementById('txtNumeroSei').value + ')';
            var htmlDoc = addLinkExibicaoDocumento(nomeDocCompleto, document.getElementById('txtTipo').value);
            var addBranco;

            var arrLinha = [
                document.getElementById('hdnPkTabela').value,
                document.getElementById('hdnIdDocumento').value,
                addBranco,
                document.getElementById('hdnUnidadeAtualGrid').value,
                document.getElementById('hdnDataAtualGrid').value,
            ];

            objTabelaDocumento.recarregar();
            objTabelaDocumento.adicionar(arrLinha);

            document.getElementById('tbDocumento').rows[1].cells[2].innerHTML = '<div id="divTbDocumento" style="text-align:center;">' + htmlDoc + '</div>';
            document.getElementById('hdnDadosAlterados').value = '1';
            document.getElementById('tbDocumento').style.display = '';
            document.getElementById('txtTipo').value = '';
            document.getElementById('txtNumeroSei').value = '';
            document.getElementById('btnAdicionar').style.display = 'none';
            document.getElementById('hdnIsAlteracaoGrid').value = 0;
        }

    }


    function inicializar() {

        inicializarGrid();

        if ('<?=$_GET['acao']?>' == 'md_pet_indisponibilidade_cadastrar' || '<?=$_GET['acao']?>' == 'md_pet_indisponibilidade_alterar') {
            document.getElementById('txtDtInicio').focus();
        } else if ('<?=$_GET['acao']?>' == 'md_pet_indisponibilidade_consultar') {
            infraDesabilitarCamposAreaDados();
        } else {
            document.getElementById('btnCancelar').focus();
        }

        infraEfeitoTabelas();
    }

    function validarPeriodoIndisp(dataInicio, dataFim) {

        msg = '';
        // #EU4864 - INFRAAJAX - não encontrado método que retorna somente dados, sem componentes
        $.ajax({
            type: "POST",
            url: "<?= $strUrlAjaxValidacaoPeridoDta ?>",
            dataType: "xml",
            async: false,
            data: {
                dataInicio: dataInicio,
                dataFim: dataFim
            },
            success: function (result) {
                msg = $(result).find('validacao').text();
            },
            error: function (msgError) {
                msgCommit = "Erro ao processar o XML do SEI: " + msgError.responseText;
            },
            complete: function (result) {

            }
        });

        return msg;
    }


    function validarDataInicialMaiorQueFinal() {
        var dataInicial = returnDateTime(document.getElementById('txtDtInicio').value);
        var dataFinal = returnDateTime(document.getElementById('txtDtFim').value);
        var valido = (dataInicial.getTime() <= dataFinal.getTime());

        if (!valido) {
            alert('A Data/Hora Inicio deve ser menor  que a Data/Hora Fim');
            return false;
        }

        return true;

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


    function validarCadastro() {
        preencherHdnProrrogacao();

        var campoDtIni = document.getElementById('txtDtInicio');
        var tamanhoDataInicio = parseInt((campoDtIni.value).length);
        var campoDtFim = document.getElementById('txtDtFim');
        var tamanhoDataFim = parseInt((campoDtFim.value).length);
        var numeroSei = document.getElementById('txtNumeroSei').value.trim();
        var tipoDocumento = document.getElementById('txtTipo').value.trim();
        var qtdDocAdicionado = document.getElementById('tbDocumento').rows.length;
        var dataFim = campoDtFim.value;

        if (infraTrim(document.getElementById('txtDtInicio').value) == '') {
            alert('Informe o Início.');
            document.getElementById('txtDtInicio').focus();
            return false;
        }

        if (infraTrim(document.getElementById('txtDtFim').value) == '') {
            alert('Informe o Fim.');
            document.getElementById('txtDtFim').focus();
            return false;
        }

        if (infraTrim(document.getElementById('txtResumoIndisponibilidade').value) == '') {
            alert('Informe o Resumo da Indisponibilidade.');
            document.getElementById('txtResumoIndisponibilidade').focus();
            return false;
        }

        var prorrogacao = document.getElementsByName('rdProrrogacao[]')[0].checked;
        if (!prorrogacao) {
            prorrogacao = document.getElementsByName('rdProrrogacao[]')[1].checked;
        }

        if (!prorrogacao) {
            alert('Informe se a Indisponibilidade justifica prorrogação automática dos prazos.');
            document.getElementById('rdProrrogacaoSim').focus();
            return false;
        }

        //Validar Datas
        var dataInicial = returnDateTime(document.getElementById('txtDtInicio').value);
        var dataFinal = returnDateTime(document.getElementById('txtDtFim').value);

        var valido = (dataInicial.getTime() < dataFinal.getTime());

        if (!valido) {
            alert('A Data/Hora Inicio deve ser menor que a Data/Hora Fim');
            return false;
        }


        if (tamanhoDataInicio < 16 || tamanhoDataInicio === 18) {
            alert('Data/Hora Inválida');
            document.getElementById('txtDtInicio').focus();
            document.getElementById('txtDtInicio').value = '';
            return false;
        }

        if (tamanhoDataFim < 16 || tamanhoDataFim === 18) {
            alert('Data/Hora Inválida');
            document.getElementById('txtDtFim').focus();
            document.getElementById('txtDtFim').value = '';
            return false;
        }

        // Validar Documento

        if (qtdDocAdicionado < 2 && tipoDocumento == '') {
            document.getElementById('hdnIdDocumento').value = '';
        }


        if (tipoDocumento != '' && qtdDocAdicionado < 2) {
            alert('O documento informado no número SEI não foi adicionado e não será salvo.Caso seja necessário vincular o documento é preciso antes adicioná-lo.');
            return false;
        }


        var dataAtual = new Date();
        var validoDtFutura = (dataFinal.getTime() < dataAtual.getTime());

        if (!validoDtFutura) {
            alert('Não é permitido o cadastro de Indisponibilidades Programadas.');
            return false;
        }


        return true;

    }

    function preencherHdnProrrogacao() {
        var rdProrrogacao = document.getElementsByName('rdProrrogacao[]')[0].checked ? 'S' : '';

        if (rdProrrogacao == '') {
            rdProrrogacao = document.getElementsByName('rdProrrogacao[]') [1].checked ? 'N' : '';
        }

        document.getElementById('hdnSinProrrogacao').value = rdProrrogacao;
    }


    function validDate(valor) {

        var campo = (valor === 'I') ? document.getElementById('txtDtInicio') : document.getElementById('txtDtFim');
        var tamanhoCampo = parseInt((campo.value).length);

        if (tamanhoCampo < 16 || tamanhoCampo === 18) {
            campo.focus();
            campo.value = "";
            alert('Data/Hora Inválida');
            return false;
        }

        var datetime = (campo.value).split(" ");
        var date = datetime[0];

        var ardt = new Array;
        var ExpReg = new RegExp("(0[1-9]|[12][0-9]|3[01])/(0[1-9]|1[012])/[12][0-9]{3}");
        ardt = date.split("/");
        erro = false;
        if (date.search(ExpReg) == -1) {
            erro = true;
        }
        else if (((ardt[1] == 4) || (ardt[1] == 6) || (ardt[1] == 9) || (ardt[1] == 11)) && (ardt[0] > 30)) {
            erro = true;
        } else if (ardt[1] == 2) {
            if ((ardt[0] > 28) && ((ardt[2] % 4) != 0))
                erro = true;
            if ((ardt[0] > 29) && ((ardt[2] % 4) == 0))
                erro = true;
        }

        if (erro) {
            alert("Data/Hora Inválida");
            campo.focus();
            campo.value = "";
            return false;
        } else {

            var arrayHoras = datetime[1].split(':')
            var horas = arrayHoras[0];
            var minutos = arrayHoras[1];
            var segundos = arrayHoras[2];

            if (horas > 23 || minutos > 59 || segundos > 59) {
                alert('Data/Hora Inválida');
                campo.focus();
                campo.value = "";
                return false
            }

        }

        if (document.getElementById('txtDtInicio').value != '' && document.getElementById('txtDtFim').value != '') {
            var dataInicial = returnDateTime(document.getElementById('txtDtInicio').value);
            var dataFinal = returnDateTime(document.getElementById('txtDtFim').value);
            var valido = (dataInicial.getTime() <= dataFinal.getTime());

            if (!valido) {
                document.getElementById('txtDtInicio').value = '';
                document.getElementById('txtDtFim').value = '';
                alert('A Data/Hora Inicio deve ser menor que a Data/Hora Fim');
                return false;
            }
        }

        return true;
    }

    function OnSubmitForm() {
        var prorrogacaoSim = document.getElementsByName('rdProrrogacao[]')[0].checked;
        var isSalvoAnteriormente =  <?php echo $isDisabled ?> ==
        '1';
        var campoDtIni = document.getElementById('txtDtInicio').value;
        var campoDtFim = document.getElementById('txtDtFim').value;


        if (validarCadastro()) {

            if (prorrogacaoSim && !isSalvoAnteriormente) {
                var msg = 'Ao marcar que a presente indisponibilidade justifica a prorrogação automática dos prazos, as Intimações Eletrônicas que venceriam durante o período da indisponibilidade terão seus Prazos Externos prorrogados para o primeiro dia útil seguinte ao fim da respectiva indisponibilidade. E uma vez salva a indisponibilidade com a opção SIM selecionada no campo citado, NÃO será possível alterar o campo de SIM para NÃO e também não será possível alterar o Período de Indisponibilidade informado.\n\nConfirma a prorrogação automática dos prazos?';
                periodoExistente = validarPeriodoIndisp(campoDtIni, campoDtFim);

                if (periodoExistente == 'N') {
                    if (confirm(msg)) {
                        return true;
                    } else {
                        return false;
                    }
                } else {
                    alert('Já existe uma indisponibilidade gerada nesse período estabelecido.');
                    return false;
                }

            }

            return true;

        } else {
            return false;
        }
    }

</script>