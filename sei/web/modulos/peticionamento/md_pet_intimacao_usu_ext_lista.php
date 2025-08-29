<?php
try {
    /**
     * @author André Luiz <andre.luiz@castgroup.com.br>
     * @since  08/03/2017
     */

    require_once dirname(__FILE__) . '/../../SEI.php';

    session_start();
    //====================================================
    //InfraDebug::getInstance()->setBolLigado(false);
    //InfraDebug::getInstance()->setBolDebugInfra(false);
    //InfraDebug::getInstance()->limpar();
    //====================================================

    SessaoSEIExterna::getInstance()->validarLink();
    SessaoSEIExterna::getInstance()->validarPermissao($_GET['acao']);

    //URL's
    $strUrlAcaoForm     = SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=' . $_GET['acao'] . '&acao_origem=' . $_GET['acao']);
    $strUrlFechar       = SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=usuario_externo_controle_acessos');
    $strUrlResponder    = SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=md_pet_responder_intimacao_usu_ext');
	$strUrlAcaoLinhas   = SessaoSEIExterna::getInstance()->assinarLink('modulos/peticionamento/controlador_ajax_externo.php?acao_ajax_externo=get_acoes_intimacao_lista&acao_origem=' . $_GET['acao']);
    $comboSituacao      = MdPetIntimacaoINT::getSituacoesListaExterno();

    //Combo tipo de Intimação
    $selectedTpIntim    = array_key_exists('selTipoIntimacao', $_POST) ? $_POST['selTipoIntimacao'] : '0';
    $selTipoIntimacao   = MdPetIntTipoIntimacaoINT::montarSelectTipoIntimacaoListaExterna($selectedTpIntim);
    $selectedSitIntim   = array_key_exists('selCumprimentoIntimacao', $_POST) ? $_POST['selCumprimentoIntimacao'] : '';
    $selSituacaoIntimacao = MdPetIntimacaoINT::montarSelectSituacaoIntimacao($selectedSitIntim);

    //Init RN
    $objMdPetRelDestRN = new MdPetIntRelDestinatarioRN();

    switch ($_GET['acao']) {

        case 'md_pet_intimacao_usu_ext_listar':

            $strTitulo = "Intimações Eletrônicas";
            break;

        default:
            throw new InfraException("Ação '" . $_GET['acao'] . "' não reconhecida.");
    }

    $arrComandos[] = '<button type="button" accesskey="P" name="btnPesquisar" onclick="pesquisar()" value="Pesquisar" class="infraButton"><span class="infraTeclaAtalho">P</span>esquisar</button>';

    $arrComandos[] = '<button type="button" accesskey="C" id="btnFechar" value="Fechar" class="infraButton" onclick="fechar()">Fe<span class="infraTeclaAtalho">c</span>har</button>';

    $arrPost = $_POST;

    $selTipoDestinatario = isset($_POST['selTipoDestinatario']) ? $_POST['selTipoDestinatario'] : '';
//        echo "<pre>";
//        var_dump($selTipoDestinatario);
////        die;
    $objDTO = $objMdPetRelDestRN->retornaSelectsDto(array(false, $arrPost));

    PaginaSEIExterna::getInstance()->prepararOrdenacao($objDTO, 'DataCadastro', InfraDTO::$TIPO_ORDENACAO_DESC);
    PaginaSEIExterna::getInstance()->prepararPaginacao($objDTO, 100);

    $objDTO->retStrNomeContato();
    $objDTO->retDblCnpjContato();
    $objDTO->retDblCpfContato();
    $objDTO->retStrSinPessoaJuridica();

    $arrObjDTO = $objMdPetRelDestRN->listarDadosUsuExterno(array(false, $arrPost, $objDTO));

    PaginaSEIExterna::getInstance()->processarPaginacao($objDTO);
    $arrTipoDestinatario = array(
        "N" => "Pessoa Física",
        "S" => "Pessoa Jurídica"
    );

    $numRegistros = (is_array($arrObjDTO) ? count($arrObjDTO) : 0);
    $strResultado = '';
    
    if ($numRegistros > 0) {

        $objMdPetIntAceiteRN = new MdPetIntAceiteRN();
        $objMdPetCertidaoRN = new MdPetIntCertidaoRN();
        $objMdPetIntReciboRN = new MdPetIntReciboRN();

        $strResultado .= '<table width="100%" class="infraTable" summary="Intimações Eletrônicas">';
        $strResultado .= '<caption class="infraCaption">';

        $strResultado .= PaginaSEIExterna::getInstance()->gerarCaptionTabela('Intimações Eletrônicas', $numRegistros);
        $strResultado .= '</caption>';

        $strResultado .= '<tr>';
        $strResultado .= '<th class="infraTh"><div style="width: 160px">' . PaginaSEIExterna::getInstance()->getThOrdenacao($objDTO, 'Processo', 'ProtocoloFormatadoProcedimento', $arrObjDTO) . '</div></th>';

        $strResultado .= '<th class="infraTh" width="66px">' . PaginaSEIExterna::getInstance()->getThOrdenacao($objDTO, 'Data de Expedição', 'DataCadastro', $arrObjDTO) . '</th>';

        $strResultado .= '<th class="infraTh" width="15%">' . PaginaSEIExterna::getInstance()->getThOrdenacao($objDTO, 'Documento Principal', 'DocumentoPrincipal', $arrObjDTO) . '</th>';

        $strResultado .= '<th class="infraTh" width="30%" >' . PaginaSEIExterna::getInstance()->getThOrdenacao($objDTO, 'Destinatário', 'NomeContato', $arrObjDTO) . '</th>';

        $strResultado .= '<th class="infraTh" width="66px">' . PaginaSEIExterna::getInstance()->getThOrdenacao($objDTO, 'Tipo de Destinatário', 'SinPessoaJuridica', $arrObjDTO) . '</th>';

        $strResultado .= '<th class="infraTh" width="12%">' . PaginaSEIExterna::getInstance()->getThOrdenacao($objDTO, 'Tipo de Intimação', 'NomeTipoIntimacao', $arrObjDTO) . '</th>';

        $strResultado .= '<th class="infraTh" width="225px">' . PaginaSEIExterna::getInstance()->getThOrdenacao($objDTO, 'Situação', 'StaSituacaoIntimacao', $arrObjDTO) . '</th>';

        $strResultado .= '<th class="infraTh"><div style="width: 90px" class="text-center">Ações</div></th>';
        $strResultado .= '</tr>';

        $strCssTr = '<tr class="infraTrEscura">';


        foreach ($arrObjDTO as $key => $objRet) {

            $idAcExt = $objRet->getNumIdAcessoExterno();
            SessaoSEIExterna::getInstance()->configurarAcessoExterno($idAcExt);

            //vars
            $strId = $objRet->getNumIdMdPetIntimacao();
            $bolRegistroAtivo = $objRet->getStrSinAtivo() == 'S';
            $idIntimacao = $objRet->getNumIdMdPetIntimacao(); //Corrigir
            $nomeTela = 'Intimação Eletrônica';

            $idProcesso = isset($objRet) && !is_null($objRet) ? $objRet->getDblIdProtocoloProcedimento() : null;
            $tpProcesso = $objRet->getStrNomeTipoProcedimento();

            $idMdPetDest = $objRet->getNumIdMdPetIntRelDestinatario();

            $descricao = $objRet->getStrEspecificacaoProcedimento();
            $strCssTr = !$bolRegistroAtivo ? 'trVermelha' : ($strCssTr == 'infraTrClara' ? 'infraTrEscura' : 'infraTrClara');

            $strResultado .= '<tr class="tr-acoes-dinamicas '.$strCssTr.'" data-idMdPetDest="'.$objRet->getNumIdMdPetIntRelDestinatario().'" data-idIntimacao="'.$idIntimacao.'" data-idProcesso="'.$idProcesso.'" data-tpProcesso="'.$tpProcesso.'" data-idAcExt="'.$idAcExt.'" data-descricao="'.$descricao.'" data-idSituacao="'.$objRet->getStrStaSituacaoIntimacao().'" data-docTipo="'.str_replace('(' . $objRet->getStrProtocoloFormatadoDocumento() . ')', '', $objRet->getStrDocumentoPrincipal()).'" data-docPrinc="'.$objRet->getStrProtocoloFormatadoDocumento().'" data-idUsuarioExterno="'.SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno().'">';

            //Linha Número do Processo
            $strResultado .= '<td align="center" >';
            $strResultado .= $objMdPetRelDestRN->addConsultarProcesso($idProcesso, $tpProcesso, $idAcExt, $descricao, $objRet->getStrProtocoloFormatadoProcedimento());
            $strResultado .= '</td>';

            //Linha Data de Expedição
            $arrData = explode(' ', $objRet->getDthDataCadastro());
            $strResultado .= '<td align="center">';
            $strResultado .= $arrData[0];
            $strResultado .= '</td>';

            //Documento Principal
            $strResultado .= '<td>';
            $strResultado .= PaginaSEI::tratarHTML($objRet->getStrDocumentoPrincipal());
            $strResultado .= '</td>';

            //Destinatário
            $strResultado .= '<td>';
            $strResultado .= PaginaSEI::tratarHTML($objRet->getStrNomeContato()) . " (";
            $strResultado .= $objRet->getStrSinPessoaJuridica() == 'S' ? PaginaSEI::tratarHTML(InfraUtil::formatarCnpj($objRet->getDblCnpjContato())) : InfraUtil::formatarCpf(PaginaSEI::tratarHTML($objRet->getDblCpfContato()));
            $strResultado .= ') </td>';

            //Destinatário
            $strResultado .= '<td>';
            $strResultado .= $objRet->getStrSinPessoaJuridica() == 'S' ? "Pessoa Jurídica" : "Pessoa Física";
            $strResultado .= '</td>';


            //Tipo de Intimação
            $strResultado .= '<td>';
            $strResultado .= PaginaSEI::tratarHTML($objRet->getStrNomeTipoIntimacao());
            $strResultado .= '</td>';

            //Situação
            $strResultado .= '<td>';
            $strResultado .= PaginaSEI::tratarHTML($objRet->getStrSituacaoIntimacao());
            $strResultado .= '</td>';

            $strResultado .= '<td align="center" class="td-acoes-dinamicas" style="vertical-align: middle"><h4 class="text-placeholder line"></h4></td>';
            $strResultado .= '</tr>';

        }

        $strResultado .= '</table>';
    }

    SessaoSEIExterna::getInstance()->configurarAcessoExterno(null);

} catch (Exception $e) {
    PaginaSEIExterna::getInstance()->processarExcecao($e);
}

PaginaSEIExterna::getInstance()->montarDocType();
PaginaSEIExterna::getInstance()->abrirHtml();
PaginaSEIExterna::getInstance()->abrirHead();
PaginaSEIExterna::getInstance()->montarMeta();
PaginaSEIExterna::getInstance()->montarTitle(':: ' . PaginaSEIExterna::getInstance()->getStrNomeSistema() . ' - ' . $strTitulo . ' ::');
PaginaSEIExterna::getInstance()->montarStyle();
PaginaSEIExterna::getInstance()->abrirStyle();
require_once('md_pet_intimacao_usu_ext_lista_css.php');
PaginaSEIExterna::getInstance()->fecharStyle();
PaginaSEIExterna::getInstance()->montarJavaScript();
PaginaSEIExterna::getInstance()->abrirJavaScript();

PaginaSEIExterna::getInstance()->fecharJavaScript();
PaginaSEIExterna::getInstance()->fecharHead();
PaginaSEIExterna::getInstance()->abrirBody($strTitulo, 'onload="inicializar();"');
?>
    <form id="frmIntimacaoEletronicaLista" method="POST" action="<?= $strUrlAcaoForm ?>"/>
<?php
PaginaSEIExterna::getInstance()->montarBarraComandosSuperior($arrComandos);
PaginaSEIExterna::getInstance()->abrirAreaDados('auto', 'style="margin-bottom: 25px"');
?>

    <div class="row">
        <div class="col-sm-12 col-md-6 col-lg-3 col-xl-3">
            <div class="form-group">
                <label class="infraLabelOpcional" for="txtNumeroProcesso">Número do Processo:</label>
                <input type="text" name="txtNumeroProcesso" id="txtNumeroProcesso" class="infraText form-control"
                   tabindex="<?= PaginaSEIExterna::getInstance()->getProxTabDados(); ?>"
                   value="<?php echo array_key_exists('txtNumeroProcesso', $_POST) ? PaginaSEIExterna::tratarHTML($_POST['txtNumeroProcesso']) : '' ?>"/>
            </div>
        </div>
        <div class="col-sm-6 col-md-3 col-lg-3 col-xl-3">
            <div class="form-group">
                <label class="infraLabelOpcional" for="txtPeriodoExpedicao">Período de Expedição:</label>
                <!--DATA INICIAL-->
                <div class="input-group mb-3">
                    <input type="text" name="txtDataInicio" id="txtDataInicio" class="infraText form-control"
                        onkeypress="return infraMascaraData(this, event);" maxlength="10"
                        tabindex="<?= PaginaSEIExterna::getInstance()->getProxTabDados(); ?>"
                        value="<?php echo array_key_exists('txtDataInicio', $_POST) ? PaginaSEIExterna::tratarHTML($_POST['txtDataInicio']) : '' ?>"/>

                    <img src="<?= PaginaSEIExterna::getInstance()->getDiretorioSvgGlobal() ?>/calendario.svg?<?= Icone::VERSAO ?>"
                        id="imgDataInicio"
                        title="Selecionar Data Inicial"
                        alt="Selecionar Data Inicial" class="infraImg"
                        onclick="infraCalendario('txtDataInicio',this,false,'<?= InfraData::getStrDataAtual() ?>');"/>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-3 col-lg-3 col-xl-3">
            <div class="form-group">
                <!--FIM DATA INICIAL-->
                <label class="infraLabelOpcional mx-2">até</label>
                <!--DATA FINAL-->
                <div class="input-group mb-3">
                    <input type="text" id="txtDataFim" name="txtDataFim" class="infraText form-control"
                        value="<?php echo array_key_exists('txtDataFim', $_POST) ? PaginaSEIExterna::tratarHTML($_POST['txtDataFim']) : '' ?>"
                        onkeypress="return infraMascaraData(this, event);" maxlength="10"
                        tabindex="<?= PaginaSEIExterna::getInstance()->getProxTabDados(); ?>"/>

                    <img src="<?= PaginaSEIExterna::getInstance()->getDiretorioSvgGlobal() ?>/calendario.svg?<?= Icone::VERSAO ?>"
                        id="imgDataFim"
                        title="Selecionar Data Final"
                        alt="Selecionar Data Final" class="infraImg"
                        onclick="infraCalendario('txtDataFim',this,false,'<?= InfraData::getStrDataAtual() ?>');"/>
                </div>
            </div>
        </div>

        <!--FIM DATA FINAL-->
        <div class="col-sm-12 col-md-3 col-lg-3 col-xl-3">
            <div class="form-group">
                <label class="infraLabelOpcional" for="selTipoDestinatario">Tipo de Destinatário:</label>
                <select onchange="pesquisar();" class="infraSelect form-control" name="selTipoDestinatario"
                        id="selTipoDestinatario"
                        tabindex="<?= PaginaSEIExterna::getInstance()->getProxTabDados(); ?>">
                    <option value=""></option>
                    <?php foreach ($arrTipoDestinatario as $chaveTipoDestinatario => $itemTipoDestinatario) : ?>
                        <option <?php if ($selTipoDestinatario == $chaveTipoDestinatario) echo "selected='selected'"; ?>
                                value="<?php echo $chaveTipoDestinatario; ?>"><?php echo $itemTipoDestinatario; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="col-sm-12 col-md-9 col-lg-7 col-xl-6">
            <div class="form-group">
                <label class="infraLabelOpcional" for="selTipoIntimacao">Tipo de Intimação:</label>
                <select onchange="pesquisar();" class="infraSelect form-control" name="selTipoIntimacao"
                        id="selTipoIntimacao" tabindex="<?= PaginaSEIExterna::getInstance()->getProxTabDados(); ?>">
                    <?php echo $selTipoIntimacao; ?>
                </select>
            </div>
        </div>
        <div class="col-sm-12 col-md-12 col-lg-5 col-xl-6">
            <div class="form-group">
                <label class="infraLabelOpcional" for="selCumprimentoIntimacao">Situação:</label>
                <select onchange="pesquisar();" class="infraSelect form-control" name="selCumprimentoIntimacao"
                        style="width: 13%; min-width: 100%;" id="selCumprimentoIntimacao"
                        tabindex="<?= PaginaSEIExterna::getInstance()->getProxTabDados(); ?>">
                    <?php echo $comboSituacao; ?>
                </select>
            </div>
        </div>
    </div>

<?php
PaginaSEIExterna::getInstance()->fecharAreaDados();
?>
    <div class="row">
        <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
            <?php
            PaginaSEIExterna::getInstance()->montarAreaTabela($strResultado, $numRegistros);
            ?>
        </div>
    </div>
    </form>
<?php
require_once("md_pet_intimacao_usu_ext_lista_js.php");
PaginaSEIExterna::getInstance()->fecharBody();
PaginaSEIExterna::getInstance()->fecharHead();
