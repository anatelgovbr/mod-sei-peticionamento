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
	            LimiteSEI::getInstance()->configurarNivel3();
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
                    $html = '<div style="text-align:center"> <a title="' . $serieNumero . '" style="font-size:12.4px" class="ancoraPadraoAzul" onclick="' . $linkDocumento . '"> ' . $objDTO->getStrNomeDocFormatado() . ' </a></div>';
                    $arrGrid[] = array($objDTO->getNumIdProtPeticionamento(), $objDTO->getDblIdDocumento(), $html, $objDTO->getStrNomeUnidadeFormatada(), $objDTO->getDtaInclusaoDta());
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
                $html = '<div style="text-align:center"> <a title="' . $serieNumero . '" style="font-size:12.4px" class="ancoraPadraoAzul" onclick="' . $linkDocumento . '"> ' . $objDTO->getStrNomeDocFormatado() . ' </a></div>';

                $arrGrid[] = array($objDTO->getNumIdProtPeticionamento(), $objDTO->getDblIdDocumento(), $html, $objDTO->getStrNomeUnidadeFormatada(), $objDTO->getDtaInclusaoDta());
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
PaginaSEI::getInstance()->fecharJavaScript();
require_once("md_pet_indisponibilidade_cadastro_css.php");
?>

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
    <div class="row mb-3">
        <div class="col-sm-12 col-md-10 col-lg-10 col-xl-10">
            <fieldset id="fldPeriodoIndisponibilidade" class="infraFieldset">
                <legend class="infraLegend">Período de Indisponibilidade</legend>
                <div class="row">
                    <div class="col-sm-12 col-md-6 col-lg-3 col-xl-3">
                        <label id="lblDtInicio" for="txtDtInicio" class="infraLabelObrigatorio">Início:</label>
                        <div class="input-group mb-3">
                            <input <?php echo $disabledAlterar ?> type="text" onchange="validDate('I')"
                                                                  onkeypress="return infraMascara(this, event, '##/##/#### ##:##');"
                                                                  name="txtDtInicio" id="txtDtInicio"
                                                                  value="<?= PaginaSEI::tratarHTML($objMdPetIndisponibilidadeDTO->getDthDataInicioFormatada()) ?>"
                                                                  class="infraText"/>
                            <img src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/calendario.svg?<?= Icone::VERSAO ?>"
                                 id="imgDtInicio"
                                 title="Selecionar Data/Hora Inicial" alt="Selecionar Data/Hora Inicial"
                                 class="infraImg"
                                 onclick="infraCalendario('txtDtInicio',this,true,'<?= InfraData::getStrDataAtual() . ' 00:00' ?>');"/>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-6 col-lg-3 col-xl-3">
                        <label id="lblDtFim" for="txtDtFim" class="infraLabelObrigatorio">Fim:</label>
                        <div class="input-group mb-3">
                            <input <?php echo $disabledAlterar ?> type="text" onchange="validDate('F')"
                                                                  onkeypress="return infraMascara(this, event, '##/##/#### ##:##');"
                                                                  name="txtDtFim" id="txtDtFim"
                                                                  value="<?= PaginaSEI::tratarHTML($objMdPetIndisponibilidadeDTO->getDthDataFimFormatada()) ?>"
                                                                  class="infraText"/>
                            <img src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/calendario.svg?<?= Icone::VERSAO ?>"
                                 id="imgDtFim"
                                 title="Selecionar Data/Hora Final"
                                 alt="Selecionar Data/Hora Final" class="infraImg"
                                 onclick="infraCalendario('txtDtFim',this,true,'<?= InfraData::getStrDataAtual() . ' 23:59' ?>');"/>
                        </div>
                    </div>
                </div>
            </fieldset>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-sm-12 col-md-10 col-lg-10 col-xl-10">

            <fieldset class="infraFieldset fieldsetClear p-0">
            <label id="lblResumoIndisponibilidade" for="txtResumoIndisponibilidade"
                   class="infraLabelObrigatorio">Resumo
                da
                Indisponibilidade:</label><br/>
            <textarea type="text" maxlength="500" id="txtResumoIndisponibilidade" rows="3"
                      name="txtResumoIndisponibilidade"
                      class="infraText form-control" onkeypress="return infraMascaraTexto(this,event,500);"
                      tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"><?php echo isset($objMdPetIndisponibilidadeDTO) ? PaginaSEI::tratarHTML($objMdPetIndisponibilidadeDTO->getStrResumoIndisponibilidade()) : '' ?></textarea>


            </fieldset>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-sm-12 col-md-10 col-lg-10 col-xl-10">
            <fieldset id="fldProrrogacao" class="infraFieldset">
                <legend class="infraLegend">Indisponibilidade justifica a prorrogação automática dos prazos</legend>
                <div class="row mb-2">
                    <div class="col-12">
                        <input <?php echo isset($objMdPetIndisponibilidadeDTO) && ($objMdPetIndisponibilidadeDTO->getStrSinProrrogacao() && $objMdPetIndisponibilidadeDTO->getStrSinProrrogacao() == 'S') ? 'checked="checked" ' : '';
                        echo $disabledAlterar; ?> type="radio" class="infraRadio" id="rdProrrogacaoSim" name="rdProrrogacao[]"/>
                        <label id="lblProrrogacaoSim" class="infraLabelCheckbox" for="rdProrrogacaoSim">
                            Sim
                            <img id="imgAjuda" class="infraImgModulo"
                                 src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/ajuda.svg?<?= Icone::VERSAO ?>" name="ajuda"
                                 onmouseout="return infraTooltipOcultar();" onmouseover="return infraTooltipMostrar('<?= $textoTolTipSim; ?>');"/>
                        </label>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <input <?php echo isset($objMdPetIndisponibilidadeDTO) && ($objMdPetIndisponibilidadeDTO->getStrSinProrrogacao() && $objMdPetIndisponibilidadeDTO->getStrSinProrrogacao() == 'N') ? 'checked="checked" ' : '';
                        echo $disabledAlterar; ?> type="radio" class="infraRadio" id="rdProrrogacaoNao" name="rdProrrogacao[]"/>
                        <label id="lblProrrogacaoNao" class="infraLabelCheckbox" for="rdProrrogacaoNao">Não</label>
                    </div>
                </div>
            </fieldset>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12 col-md-10 col-lg-10 col-xl-10">
            <fieldset class="infraFieldset">
                <legend class="infraLegend">Anexar Documento</legend>
                <div class="row">
                    <div class="col-sm-12 col-md-6 col-lg-5 col-xl-4">
                        <label id="lblNumeroSei" for="txtNumeroSei" accesskey="f" class="infraLabelOpcional">
                            Número SEI:
                        </label>
                        <div class="input-group mb-3">
                            <input onchange="removerValidacaoDocumento();" type="text" id="txtNumeroSei"
                                   name="txtNumeroSei"
                                   class="infraText form-control"
                                   onkeyup="infraMascaraNumero(this, event, 100);" maxlength="100"
                                   tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"
                                   value="<?= PaginaSEI::tratarHTML($txtNumeroSei) ?>"/>

                            <button type="button" <?php echo ($isConsultar) ? 'disabled="disabled"' : '' ?>
                                    accesskey="V"
                                    id="btnValidar" onclick="controlarNumeroSEI();" class="infraButton">
                                <span class="infraTeclaAtalho">V</span>alidar
                            </button>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-5 col-lg-5 col-xl-4">
                        <label id="lblTipo" for="txtTipo" accesskey="f" class="infraLabelOpcional">
                            Tipo:
                        </label>
                        <br/>
                        <div class="input-group mb-3">
                            <input type="text" id="txtTipo" name="txtTipo" class="infraText form-control"
                                   readonly="readonly"
                                   tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"
                                   value="<?= PaginaSEI::tratarHTML($txtTipo) ?>"/>

                            <button type="button" id="btnAdicionar" style="display: none" accesskey="A"
                                    onclick="adicionarDocumento()"
                                    class="infraButton">
                                <span class="infraTeclaAtalho">A</span>dicionar
                            </button>
                        </div>
                    </div>
                </div>
                <div class="row" id="divAnexos">
                    <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">

                        <table id="tbDocumento" name="tbDocumento" class="infraTable" summary="Documentos"
                               style="<?php echo count($arrGrid) == 0 ? 'display:none; width:100%;' : 'width:100%;' ?>">

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
                        <br/>
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
                </div>
            </fieldset>

        </div>
    </div>

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
require_once('md_pet_indisponibilidade_cadastro_js.php');
?>