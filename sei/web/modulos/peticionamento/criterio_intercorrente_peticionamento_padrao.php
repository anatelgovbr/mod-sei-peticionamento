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

    //PaginaSEI::getInstance()->verificarSelecao('criterio_peticionamento_intercorrente_selecionar');

    //SessaoSEI::getInstance()->validarPermissao($_GET['acao']);

    $strDesabilitar = '';

    $arrComandos = array();
    //Tipo Processo - Nivel de Acesso
    $strLinkAjaxNivelAcesso = SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=nivel_acesso_auto_completar');

    //Tipo Documento Complementar
    $strLinkTipoDocumentoSelecao = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=serie_peticionamento_selecionar&filtro=1&tipo_selecao=2&id_object=objLupaTipoDocumento&tipoDoc=E');
    $strLinkAjaxTipoDocumento = SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=serie_peticionamento_auto_completar');

    //Tipo de Documento Essencial
    $strLinkTipoDocumentoEssencialSelecao = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=serie_peticionamento_selecionar&filtro=1&tipo_selecao=2&id_object=objLupaTipoDocumentoEssencial&tipoDoc=E');

    //Tipo Processo
    $strLinkTipoProcessoSelecao = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=tipo_procedimento_selecionar&tipo_selecao=1&id_object=objLupaTipoProcesso');
    $strLinkAjaxTipoProcesso = SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=tipo_processo_auto_completar');

    //Unidade
    $strLinkUnidadeSelecao = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=unidade_selecionar_todas&tipo_selecao=1&id_object=objLupaUnidade');
    $strLinkUnidadeMultiplaSelecao = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=unidade_selecionar_todas&tipo_selecao=1&id_object=objLupaUnidadeMultipla');
    $strLinkAjaxUnidade = SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=unidade_auto_completar');

    //Tipo Documento Principal
    $strLinkTipoDocPrincSelecao = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=serie_peticionamento_selecionar&filtro=1&tipoDoc=E&tipo_selecao=1&id_object=objLupaTipoDocPrinc');
    $strLinkAjaxTipoDocPrinc = SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=serie_peticionamento_auto_completar');

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
    //$strItensSelHipoteseLegal  = TipoProcessoPeticionamentoINT::montarSelectHipoteseLegal(null, null, ProtocoloRN::$NA_RESTRITO );

    //Preencher Array de Unidades para buscar posteriormente
    $objUnidadeDTO = new UnidadeDTO();
    $objUnidadeDTO->retNumIdContato();
    $objUnidadeDTO->retNumIdUnidade();
    $objUnidadeDTO->retStrSigla();
    $objUnidadeDTO->retStrDescricao();
    
    //seiv2
    //$objUnidadeDTO->retStrSiglaUf();

    $contatoRN = new ContatoRN();
    $objUnidadeRN = new UnidadeRN();

    $arrObjUnidadeDTO = $objUnidadeRN->listarTodasComFiltro($objUnidadeDTO);

    foreach ($arrObjUnidadeDTO as $key => $objUnidadeDTO) {
        $arrObjUnidadeDTOFormatado[$objUnidadeDTO->getNumIdUnidade()]['sigla'] = $objUnidadeDTO->getStrSigla();
        $arrObjUnidadeDTOFormatado[$objUnidadeDTO->getNumIdUnidade()]['descricao'] = utf8_encode($objUnidadeDTO->getStrDescricao());
        
        $contatoDTO = new ContatoDTO();
        $contatoDTO->retNumIdContato();
        $contatoDTO->retStrSiglaUf();
        $contatoDTO->setNumIdContato( $objUnidadeDTO->getNumIdContato() );
        
        $contatoDTO = $contatoRN->consultarRN0324( $contatoDTO );
        
        //seiv2
        //$arrObjUnidadeDTOFormatado[$objUnidadeDTO->getNumIdUnidade()]['uf'] = $objUnidadeDTO->getStrSiglaUf();
        
        //alteracoes seiv3
        $arrObjUnidadeDTOFormatado[$objUnidadeDTO->getNumIdUnidade()]['uf'] = $contatoDTO->getStrSiglaUf();
    }

    $objInfraParametroDTO = new InfraParametroDTO();
    $objInfraParametroRN = new InfraParametroRN();
    $objInfraParametroDTO->retTodos();
    $objInfraParametroDTO->setStrNome('SEI_HABILITAR_HIPOTESE_LEGAL');
    $objInfraParametroDTO = $objInfraParametroRN->consultar($objInfraParametroDTO);
    $valorParametroHipoteseLegal = $objInfraParametroDTO->getStrValor();

    switch ($_GET['acao']) {
        case 'criterio_intercorrente_peticionamento_padrao':

            $strItensSelHipoteseLegal = TipoProcessoPeticionamentoINT::montarSelectHipoteseLegal(null, null, null);

            //Carregando campos select
            $strItensSelTipoProcesso = TipoProcessoPeticionamentoINT::montarSelectTipoProcesso(null, null, $_POST['selTipoProcesso']);
            //$strItensSelUnidades     = UnidadeINT::montarSelectSiglaDescricao(null, null, $_POST['selUnidade']);

            //$strItensSelDoc          = SerieINT::montarSelectNomeRI0802(null, null, $_POST['selDocumento']);

            $strTitulo = 'Intercorrente Padrão';

            $arrComandos[] = '<button type="submit" accesskey="s" name="sbmCadastrarTpProcessoPeticionamento" id="sbmCadastrarTpProcessoPeticionamento" value="Salvar" class="infraButton"><span class="infraTeclaAtalho">S</span>alvar</button>';
            $arrComandos[] = '<button type="button" accesskey="c" name="btnCancelar" id="btnCancelar" value="Cancelar" onclick="location.href=\'' . PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . PaginaSEI::getInstance()->getAcaoRetorno() . '&acao_origem=' . $_GET['acao'])) . '\';" class="infraButton"><span class="infraTeclaAtalho">C</span>ancelar</button>';

            /*
            $objTipoProcessoPeticionamentoRN = new TipoProcessoPeticionamentoRN();
            $objTipoProcessoPeticionamentoDTO = new TipoProcessoPeticionamentoDTO();
            $objTipoProcessoPeticionamentoDTO->setNumIdProcedimento($_POST['hdnIdTipoProcesso']);
            $objTipoProcessoPeticionamentoDTO->setStrSinIIProprioUsuarioExterno('N');
            $objTipoProcessoPeticionamentoDTO->setStrSinIIIndicacaoDireta('N');
            $objTipoProcessoPeticionamentoDTO->setStrSinIIIndicacaoDiretaCpfCnpj('N');
            $objTipoProcessoPeticionamentoDTO->setStrSinIIIndicacaoDiretaContato('N');
            $objTipoProcessoPeticionamentoDTO->setStrSinNaUsuarioExterno('N');
            $objTipoProcessoPeticionamentoDTO->setStrSinNaPadrao('N');
            $objTipoProcessoPeticionamentoDTO->setStrSinDocGerado('N');
            $objTipoProcessoPeticionamentoDTO->setStrSinDocExterno('N');
            */

            // cadastrando o critério intercorrente
            if (isset($_POST['sbmCadastrarTpProcessoPeticionamento'])) {
                $strStaTipoNivelAcesso = 'I';
                if ($_POST['rdNivelAcesso'][0] == 1) {
                    $strStaTipoNivelAcesso = 'P';
                }
                /*
                echo '<pre>';
                var_dump($_POST);
                exit;
                */
                $objCriterioIntercorrentePeticionamentoDTO = new CriterioIntercorrentePeticionamentoDTO();
                //$objCriterioIntercorrentePeticionamentoDTO->setNumIdCriterioIntercorrentePeticionamento();

                $objCriterioIntercorrentePeticionamentoDTO->setStrStaTipoNivelAcesso($strStaTipoNivelAcesso);
                $objCriterioIntercorrentePeticionamentoDTO->setStrStaNivelAcesso($_POST['rdNivelAcesso'][0]);
                $objCriterioIntercorrentePeticionamentoDTO->setNumIdHipoteseLegal($_POST['selHipoteseLegal']);
                $objCriterioIntercorrentePeticionamentoDTO->setNumIdTipoProcedimento($_POST['hdnIdTipoProcesso']);

                $objCriterioIntercorrentePeticionamentoRN = new CriterioIntercorrentePeticionamentoRN();
                if ($_GET['acao'] == 'criterio_intercorrente_peticionamento_padrao') {
                    $objCriterioIntercorrentePeticionamentoRN->cadastrarPadrao($objCriterioIntercorrentePeticionamentoDTO);
                }
            }
            break;
        default:
            throw new InfraException("Ação '" . $_GET['acao'] . "' não reconhecidas.");
    }
}catch(Exception $e){
    PaginaSEI::getInstance()->processarExcecao($e);
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
//javascript
PaginaSEI::getInstance()->fecharJavaScript();
?>


<style type="text/css">
    <?php
    $browser = $_SERVER['HTTP_USER_AGENT'];
    $firefox = strpos($browser, 'Firefox') ? true : false;
    ?>

    #lblTipoProcesso {
        position: absolute;
        left: 0%;
        top: 2px;
        width: 50%;
    }

    #txtTipoProcesso {
        position: absolute;
        left: 0%;
        top: 18px;
        width: 50%;
    }

    #fldProrrogacao {
        height: 20%;
        width: 86%;
    }

    <?php if($firefox): ?>

    .sizeFieldset {
        height: 30%;
        width: 86%;
    }

    .tamanhoFieldset {
        height: auto;
        width: 86%;
    }

    #divIndicacaoInteressado {
    }

    #divUnidade {
        margin-top: 138px !important;
    }

    #imgLupaTipoProcesso {
        position: absolute;
        left: 51%;
        top: 18px;
    }

    #imgExcluirTipoProcesso {
        position: absolute;
        left: 53.6%;
        top: 18px;
    }

    #lblUnidade {
        position: absolute;
        left: 0%;
        width: 50%;
    }

    #txtUnidade {
        left: 12px;
        width: 65%;
        margin-top: 0.5%;
    }

    #imgLupaUnidade {
        position: absolute;
        left: 51%;
        margin-top: 0.5%;
    }

    #imgExcluirUnidade {
        position: absolute;
        left: 52.7%;
        margin-top: 0.5%;
    }

    #txtUnidadeMultipla {
        left: 12px;
        width: 65%;
        margin-top: 0.5%;
    }

    #imgLupaUnidadeMultipla {
        position: absolute;
        left: 51%;
        margin-top: 0.5%;
    }

    #sbmAdicionarUnidade {
        position: absolute;
        left: 53.7%;
        margin-top: 0.5%;
    }

    #lblOrientacoes {
        position: absolute;
        left: 0%;
        top: 50px;
        width: 20%;
    }

    #txtOrientacoes {
        position: absolute;
        left: 0%;
        top: 66px;
        width: 75%;
    }

    #lblNivelAcesso {
        width: 50%;
    }

    #selNivelAcesso {
        width: 20%;
    }

    #lblHipoteseLegal {
        width: 50%;
    }

    #selHipoteseLegal {
        width: 50%;
    }

    #lblModelo {
        width: 50%;
    }

    #selModelo {
        width: 40%;
    }

    #lblTipoDocPrincipal {
        width: 50%;
    }

    #txtTipoDocPrinc {
        width: 39.5%;
    }

    #imgLupaTipoDocPrinc {
        top: 198%
    }

    #imgExcluirTipoDocPrinc {
        top: 198%
    }

    #txtSerie {
        width: 50%;
    }

    #lblDescricao {
        width: 50%;
    }

    #selDescricao {
        width: 75%;
    }

    #imgLupaTipoDocumento {
        margin-top: 2px;
        margin-left: 4px;
    }

    #txtSerieEssencial {
        width: 50%;
    }

    #lblDescricaoEssencial {
        width: 50%;
    }

    #selDescricaoEssencial {
        width: 75%;
    }

    #imgLupaTipoDocumentoEssencial {
        margin-top: 2px;
        margin-left: 4px;
    }

    .fieldNone {
        border: none !important;
    }

    .sizeFieldset#fldDocPrincipal {
        height: 50% !important;
    }

    <?php else: ?>
    .sizeFieldset {
        height: 30%;
        width: 86%;
    }

    .tamanhoFieldset {
        height: auto;
        width: 86%;
    }

    #divIndicacaoInteressado {
    }

    #imgLupaTipoProcesso {
        position: absolute;
        left: 51%;
        top: 18px;
    }

    #imgExcluirTipoProcesso {
        position: absolute;
        left: 53.1%;
        top: 18px;
    }

    #divUnidade {
        margin-top: 111px !important;
    }

    #lblUnidade {
        left: 0%;
        top: 15.7%;
        width: 65%;
    }

    #txtUnidade {
        left: 0%;
        top: 17.6%;
        width: 65%;
        margin-top: 0.5%;
    }

    #imgLupaUnidade {
        position: absolute;
        left: 50.4%;
    }

    #imgExcluirUnidade {
        position: absolute;
        left: 52.1%;
    }

    #txtUnidadeMultipla {
        left: 12px;
        width: 65%;
        margin-top: 0.5%;
    }

    #imgLupaUnidadeMultipla {
        position: absolute;
        left: 50.5%;
        margin-top: 0.5%;
    }

    #sbmAdicionarUnidade {
        position: absolute;
        left: 53.2%;
        margin-top: 0.5%;
    }

    #lblOrientacoes {
        position: absolute;
        left: 0%;
        top: 50px;
        width: 20%;
    }

    #txtOrientacoes {
        position: absolute;
        left: 0%;
        top: 66px;
        width: 75%;
    }

    #lblNivelAcesso {
        width: 50%;
    }

    #selNivelAcesso {
        width: 20%;
    }

    #lblHipoteseLegal {
        width: 50%;
    }

    #selHipoteseLegal {
        width: 50%;
    }

    #lblModelo {
        width: 50%;
    }

    #selModelo {
        width: 40%;
    }

    #lblTipoDocPrincipal {
        width: 50%;
    }

    #txtTipoDocPrinc {
        width: 39.5%;
    }

    #imgLupaTipoDocPrinc {
        top: 198%
    }

    #imgExcluirTipoDocPrinc {
        top: 198%
    }

    #txtSerie {
        width: 50%;
    }

    #lblDescricao {
        width: 50%;
    }

    #selDescricao {
        width: 75%;
    }

    #imgLupaTipoDocumento {
        margin-top: 2px;
        margin-left: 4px;
    }

    #imgExcluirTipoDocumento {
    }

    .fieldNone {
        border: none !important;
    }

    .sizeFieldset#fldDocPrincipal {
        height: 50% !important;
    }

    #txtSerieEssencial {
        width: 50%;
    }

    #lblDescricaoEssencial {
        width: 50%;
    }

    #selDescricaoEssencial {
        width: 75%;
    }

    #imgLupaTipoDocumentoEssencial {
        margin-top: 2px;
        margin-left: 4px;
    }

    <?php endif; ?>

    .fieldsetClear {
        border: none !important;
    }

    .rdIndicacaoIndiretaHide {
        margin-left: 2.8% !important;
    }
</style>
<?php
PaginaSEI::getInstance()->fecharHead();
PaginaSEI::getInstance()->abrirBody($strTitulo, 'onload="inicializar();"');
?>
<form id="frmCriterioCadastro" method="post" onsubmit="return OnSubmitForm();"
      action="<?= PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . $_GET['acao'] . '&acao_origem=' . $_GET['acao'])) ?>">
    <?
    PaginaSEI::getInstance()->montarBarraComandosSuperior($arrComandos);
    PaginaSEI::getInstance()->abrirAreaDados('98%');
    ?>

    <input type="hidden" name="hdnParametroHipoteseLegal" id="hdnParametroHipoteseLegal"
           value="<?php echo $valorParametroHipoteseLegal; ?>"/>
    <!--  Tipo de Processo  -->
    <div class="fieldsetClear">
        <label id="lblTipoProcesso" for="txtTipoProcesso" class="infraLabelObrigatorio">Tipo de Processo: </label>
        <input type="text" onchange="removerProcessoAssociado(0);" id="txtTipoProcesso" name="txtTipoProcesso"
               class="infraText" value="<?php echo $nomeTipoProcesso; ?>"
               tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
        <input type="hidden" id="hdnIdTipoProcesso" name="hdnIdTipoProcesso" value="<?php echo $idTipoProcesso ?>"/>
        <input type="hidden" id="hdnIdMdPetTipoProcesso" name="hdnIdMdPetTipoProcesso"
               value="<?php echo $idMdPetTipoProcesso ?>"/>
        <img id="imgLupaTipoProcesso" onclick="objLupaTipoProcesso.selecionar(700,500);"
             src="/infra_css/imagens/lupa.gif" alt="Selecionar Tipo de Processo" title="Selecionar Tipo de Processo"
             class="infraImg"/>
        <img id="imgExcluirTipoProcesso" onclick="removerProcessoAssociado(0);objLupaTipoProcesso.remover();"
             src="/infra_css/imagens/remover.gif" alt="Remover Tipo de Processo" title="Remover Tipo de Processo"
             class="infraImg"/>

    </div>
    <!--  Fim do Tipo de Processo -->

    <div style="clear:both;">&nbsp;</div>
    <div style="margin-top: 40px!important;">
        <fieldset class="infraFieldset" style="width:75%;">
            <legend class="infraLegend">&nbsp;Nível de Acesso dos Documentos&nbsp;</legend>
            <div>
                <input <?php echo $sinNAUsuExt; ?> type="radio" name="rdNivelAcesso[]"
                                                   id="rdUsuExternoIndicarEntrePermitidos"
                                                   onclick="changeNivelAcesso();" value="1">

                <label for="rdUsuExternoIndicarEntrePermitidos" id="lblUsuExterno" class="infraLabelRadio">Usuário
                    Externo indicar diretamente</label><br/>

                <input <?php echo $sinNAPadrao; ?> type="radio" name="rdNivelAcesso[]" id="rdPadrao"
                                                   onclick="changeNivelAcesso();" value="2">
                <label name="lblPadrao" id="lblPadrao" for="rdPadrao" class="infraLabelRadio">Padrão pré
                    definido</label>

                <div
                    id="divNivelAcesso" <?php echo $sinNAPadrao != '' ? 'style="display: inherit;"' : 'style="display: none;"' ?>>
                    <div style="clear:both;">&nbsp;</div>
                    <label name="lblNivelAcesso" id="lblNivelAcesso" for="selNivelAcesso" class="infraLabelObrigatorio">Nível
                        de Acesso: </label><br/>
                    <select id="selNivelAcesso" name="selNivelAcesso" onchange="changeSelectNivelAcesso()">
                        <?= $strItensSelNivelAcesso ?>
                    </select>
                </div>

                <div id="divHipoteseLegal" <?php echo $hipoteseLegal; ?>>
                    <div style="clear:both;">&nbsp;</div>
                    <label name="lblHipoteseLegal" id="lblHipoteseLegal" for="selHipoteseLegal"
                           class="infraLabelObrigatorio">Hipótese Legal:</label><br/>
                    <select id="selHipoteseLegal" name="selHipoteseLegal">
                        <?= $strItensSelHipoteseLegal ?>
                    </select>
                </div>
            </div>
        </fieldset>
    </div>

    <div style="clear:both;">&nbsp;</div>
    <!--
	<input type="hidden" id="hdnCorpoTabela" name="hdnCorpoTabela" value=""/>
	<input type="hidden" id="hdnUnidadesSelecionadas" name="hdnUnidadesSelecionadas" value=""/>
	<input type="hidden" id="hdnTodasUnidades" name="hdnTodasUnidades" value='<?= json_encode($arrObjUnidadeDTOFormatado); ?>' />
	<input type="hidden" id="hdnIdTipoDocumento" name="hdnIdTipoDocumento" value="" />
	<input type="hidden" id="hdnSerie" name="hdnSerie" value="<?= $_POST['hdnSerie'] ?>" />
	<input type="hidden" id="hdnIdTipoDocumento" name="hdnIdTipoDocumento" value="<?= $_POST['hdnIdTipoDocumento'] ?>" />
	<input type="hidden" id="hdnIdIndisponibilidadePeticionamento" name="hdnIdIndisponibilidadePeticionamento" value="" />
	<input type="hidden" id="hdnIdSerie" name="hdnIdSerie" value="<?= $_POST['hdnIdSerie'] ?>" />
	<input type="hidden" id="hdnIdSerieEssencial" name="hdnIdSerieEssencial" value="<?= $_POST['hdnIdSerieEssencial'] ?>" />
	<input type="hidden" id="hdnSerieEssencial" name="hdnSerieEssencial" value="<?= $_POST['hdnSerieEssencial'] ?>" />
	 -->

    <?
    PaginaSEI::getInstance()->fecharAreaDados();
    ?>
</form>
<?
PaginaSEI::getInstance()->fecharBody();
PaginaSEI::getInstance()->fecharHtml();
?>

<script type="text/javascript">
    //Processo
    var objLupaTipoProcesso = null;
    var objAutoCompletarTipoProcesso = null;

    function changeNivelAcesso() {
        document.getElementById('divNivelAcesso').style.display = "none";
        var padrao = document.getElementsByName('rdNivelAcesso[]')[1].checked;

        document.getElementById('selNivelAcesso').value = '';
        document.getElementById('selNivelAcesso').value = '';
        document.getElementById('selHipoteseLegal').value = '';
        document.getElementById('divHipoteseLegal').style.display = 'none';

        if (padrao) {
            document.getElementById('divNivelAcesso').style.display = "inherit";
        }
    }

    function changeSelectNivelAcesso() {
        document.getElementById('selHipoteseLegal').value = '';

        var valorSelectNivelAcesso = document.getElementById('selNivelAcesso').value;
        var valorHipoteseLegal = document.getElementById('hdnParametroHipoteseLegal').value;

        if (valorSelectNivelAcesso == '<?= ProtocoloRN::$NA_RESTRITO ?>' && valorHipoteseLegal != '0') {
            document.getElementById('divHipoteseLegal').style.display = 'inherit';
        } else {
            document.getElementById('divHipoteseLegal').style.display = 'none';
        }
    }

    function inicializar() {
        inicializarTela();

        if ('<?=$_GET['acao']?>' == 'criterio_intercorrente_peticionamento_padrao') {
            carregarComponenteTipoProcesso();
            carregarDependenciaNivelAcesso();
        }

        if ('<?=$_GET['acao']?>' == 'criterio_intercorrente_peticionamento_padrao') {
            document.getElementById('txtTipoProcesso').focus();
        }
        infraEfeitoTabelas();
    }

    function carregarDependenciaNivelAcesso() {
        //Ajax para carregar os niveis de acesso após a escolha do tipo de processo
        objAjaxIdNivelAcesso = new infraAjaxMontarSelectDependente('txtTipoProcesso', 'selNivelAcesso', '<?=$strLinkAjaxNivelAcesso?>');
        objAjaxIdNivelAcesso.prepararExecucao = function () {
            document.getElementById('selNivelAcesso').innerHTML = '';
            return infraAjaxMontarPostPadraoSelect('null', '', 'null') + '&idTipoProcesso=' + document.getElementById('hdnIdTipoProcesso').value;
        }
    }

    function inicializarTela() {
    }

    function carregarComponenteLupaTpDocComplementar(acaoComponente) {
        acaoComponente == 'S' ? objLupaTipoDocumento.selecionar(700, 500) : objLupaTipoDocumento.remover();
    }

    function returnLinkModificado(link, tipo) {
        var arrayLink = link.split('&filtro=1');

        var linkFim = '';
        if (arrayLink.length == 2) {
            linkFim = arrayLink[0] + '&filtro=1&tipoDoc=' + tipo + arrayLink[1];
        } else {
            linkFim = link;
        }

        return linkFim;
    }

    function carregarComponenteTipoProcesso() {
        objLupaTipoProcesso = new infraLupaText('txtTipoProcesso', 'hdnIdTipoProcesso', '<?=$strLinkTipoProcessoSelecao?>');

        objLupaTipoProcesso.finalizarSelecao = function () {
            objAutoCompletarTipoProcesso.selecionar(document.getElementById('hdnIdTipoProcesso').value, document.getElementById('txtTipoProcesso').value);
            objAjaxIdNivelAcesso.executar();
        }

        objAutoCompletarTipoProcesso = new infraAjaxAutoCompletar('hdnIdTipoProcesso', 'txtTipoProcesso', '<?=$strLinkAjaxTipoProcesso?>');
        objAutoCompletarTipoProcesso.limparCampo = false;

        objAutoCompletarTipoProcesso.prepararExecucao = function () {
            return 'palavras_pesquisa=' + document.getElementById('txtTipoProcesso').value;
        };

        objAutoCompletarTipoProcesso.processarResultado = function (id, descricao, complemento) {
            if (id != '') {
                document.getElementById('hdnIdTipoProcesso').value = id;
                document.getElementById('txtTipoProcesso').value = descricao;
                objAjaxIdNivelAcesso.executar();
            }
        }
        objAutoCompletarTipoProcesso.selecionar('<?=$strIdTipoProcesso?>', '<?=PaginaSEI::getInstance()->formatarParametrosJavascript($strNomeRemetente);?>');
    }

    function removerProcessoAssociado(remover) {
        document.getElementById('selNivelAcesso').innerHTML = '';
        document.getElementById('divHipoteseLegal').style.display = "none";
        console.log(remover);
        if (remover === '1') {
            objLupaTipoProcesso.remover();
        }
    }

    function validarCadastro() {

        var valorHipoteseLegal = document.getElementById('hdnParametroHipoteseLegal').value;

        if (infraTrim(document.getElementById('txtTipoProcesso').value) == '') {
            alert('Informe o Tipo de Processo.');
            document.getElementById('txtTipoProcesso').focus();
            return false;
        }

        //Validar Nível Acesso
        var elemsNA = document.getElementsByName("rdNivelAcesso[]");

        var validoNA = false, valorNA = 0;

        for (var i = 0; i < elemsNA.length; i++) {
            if (elemsNA[i].checked === true) {
                validoNA = true;
                valorNA = parseInt(elemsNA[i].value);
                //console.log(elemsNA[i].value);
            }
        }

        if (validoNA === false) {
            alert('Informe o Nível de Acesso.');
            return false;
        }

        if (infraTrim(document.getElementById('selNivelAcesso').value) == '' && valorNA != 1) {
            alert('Informe o Nível de Acesso.');
            document.getElementById('selNivelAcesso').focus();
            return false;
        } else if (document.getElementById('selNivelAcesso').value == <?= ProtocoloRN::$NA_RESTRITO ?> && valorHipoteseLegal != '0') {

            //validar hipotese legal
            if (document.getElementById('selHipoteseLegal').value == '') {
                alert('Informe a Hipótese legal padrão.');
                document.getElementById('selHipoteseLegal').focus();
                return false;
            }
        }


        //console.log('validação efetuada com sucesso');
        //return false;

        return true;
    }

    function OnSubmitForm() {
        //preencherUnidadesMultiplas();
        return validarCadastro();
    }

    function getPercentTopStyle(element) {
        var parent = element.parentNode,
            computedStyle = getComputedStyle(element),
            value;

        parent.style.display = 'none';
        value = computedStyle.getPropertyValue('top');
        parent.style.removeProperty('display');

        if (value != '') {
            valor = value.replace('%', '');
            return parseInt(valor);
        }

        return false;
    }
</script>
