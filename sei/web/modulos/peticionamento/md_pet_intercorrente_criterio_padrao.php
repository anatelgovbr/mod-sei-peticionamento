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

                header('Location: '.SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . PaginaSEI::getInstance()->getAcaoRetorno() . '&acao_origem=' . $_GET['acao']));
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

                if($objCriterioIntercorrentePadraoDTO){
                    $nomeTipoProcesso    = $objCriterioIntercorrentePadraoDTO->getStrNomeProcesso();
                    $idTipoProcesso      = $objCriterioIntercorrentePadraoDTO->getNumIdTipoProcedimento();
                    $sinCriterioPadrao   = $objCriterioIntercorrentePadraoDTO->getStrSinCriterioPadrao() == 'S' ? 'checked = checked' : '';
                    $sinNAUsuExt         = $objCriterioIntercorrentePadraoDTO->getStrStaNivelAcesso() == 1 ? 'checked = checked' : '';
                    $sinNAPadrao         = $objCriterioIntercorrentePadraoDTO->getStrStaNivelAcesso() == 2 ? 'checked = checked' : '';                    
                    $sinAtivoSim         = $objCriterioIntercorrentePadraoDTO->getStrSinAtivo() == 'S' ? 'checked = checked' : '';
                    $sinAtivoNao         = $objCriterioIntercorrentePadraoDTO->getStrSinAtivo() == 'N' ? 'checked = checked' : '';
                    $hipoteseLegal       = $objCriterioIntercorrentePadraoDTO->getStrStaTipoNivelAcesso() === 'I' && $valorParametroHipoteseLegal != '0' ? 'style="display:inherit"' : 'style="display:none"';
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
                    foreach($arrNivelAcesso as $i => $nivelAcesso){
                        $selected = ($i == $objCriterioIntercorrentePadraoDTO->getStrStaTipoNivelAcesso()) ? ' selected="selected" ': '';
                        $strItensSelNivelAcesso .= sprintf($nivelAcessoTemplate, $i, $selected, $nivelAcesso);
                    }

                }
            }
            break;
        default:
            throw new InfraException("A��o '" . $_GET['acao'] . "' n�o reconhecidas.");
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

    <?php if($firefox): ?>

    #imgLupaTipoProcesso {
        position: absolute;
        left: 51%;
        top: 18px;
    }

    #imgExcluirTipoProcesso {
        position: absolute;
        left: 53%;
        top: 18px;
    }

    #imgAjuda {
        position: absolute;
        left: 55%;
        top: 18px;
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

    <?php else: ?>

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

    #imgAjuda {
        position: absolute;
        left: 55.2%;
        top: 18px;
    }
        
    #imgAjuda2 {
        width: 16px;
        height: 16px;
        vertical-align: middle;
        padding: .1em 0;
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

    <?php endif; ?>

    .fieldsetClear {
        border: none !important;
    }
</style>
<?php
PaginaSEI::getInstance()->fecharHead();
PaginaSEI::getInstance()->abrirBody($strTitulo, 'onload="inicializar();"');
?>
<form id="frmCriterioCadastro" method="post" onsubmit="return OnSubmitForm();" action="<?= PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . $_GET['acao'] . '&acao_origem=' . $_GET['acao'])) ?>">
    <?
    PaginaSEI::getInstance()->montarBarraComandosSuperior($arrComandos);
    PaginaSEI::getInstance()->abrirAreaDados('98%');
    ?>

    <input type="hidden" name="hdnParametroHipoteseLegal" id="hdnParametroHipoteseLegal" value="<?php echo $valorParametroHipoteseLegal; ?>"/>
    <!--  Tipo de Processo  -->
    <div class="fieldsetClear">
        <label id="lblTipoProcesso" for="txtTipoProcesso" class="infraLabelObrigatorio">Tipo de Processo: </label>
        <input type="text" onchange="removerProcessoAssociado(0);" id="txtTipoProcesso" name="txtTipoProcesso" class="infraText" value="<?php echo PaginaSEI::tratarHTML($nomeTipoProcesso); ?>" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
        <input type="hidden" id="hdnIdTipoProcesso" name="hdnIdTipoProcesso" value="<?php echo $idTipoProcesso ?>"/>
        <input type="hidden" id="hdnIdMdPetTipoProcesso" name="hdnIdMdPetTipoProcesso" value="<?php echo $idMdPetTipoProcesso ?>"/>
        <img id="imgLupaTipoProcesso" onclick="objLupaTipoProcesso.selecionar(700,500);" src="/infra_css/imagens/lupa.gif" alt="Selecionar Tipo de Processo" title="Selecionar Tipo de Processo" class="infraImg"/>
        <img id="imgExcluirTipoProcesso" onclick="removerProcessoAssociado(0);objLupaTipoProcesso.remover();" src="/infra_css/imagens/remover.gif" alt="Remover Tipo de Processo" title="Remover Tipo de Processo" class="infraImg"/>
        <img id="imgAjuda" src="<?= PaginaSEI::getInstance()->getDiretorioImagensGlobal() ?>/ajuda.gif" name="ajuda" <?= PaginaSEI::montarTitleTooltip('Apenas ap�s a parametriza��o do Intercorrente Padr�o � que os Usu�rios Externos passar�o a visualizar o menu de Peticionamento Intercorrente. \n \n A abertura de Processo Novo Relacionado ao processo de fato indicado pelo Usu�rio Externo ocorrer� quando o processo indicado corresponder a: 1) Tipo de Processo sem parametriza��o de Crit�rio para Intercorrente; 2) Processo Sobrestado; ou 3) Processo Bloqueado. \n \n - Somente no cen�rio do item 1 acima a forma de indica��o de N�vel de Acesso dos Documentos pelo Usu�rio Externo ser� a parametrizada para Intercorrente Padr�o. - Em todos os cen�rios indicados acima somente ocorrer� a abertura de Processo Novo Relacionado utilizando o Tipo de Processo parametrizado para Intercorrente Padr�o quando o Tipo de Processo do processo indicado estiver desativado ou quando a unidade na qual ocorrer� o peticionamento n�o tiver permiss�o de uso do Tipo de Processo do processo indicado. ') ?> alt="Ajuda" class="infraImg"/>
    </div>
    <!--  Fim do Tipo de Processo -->

    <div style="clear:both;">&nbsp;</div>
    <div style="margin-top: 40px!important;">
        <fieldset class="infraFieldset" style="width:75%;">
            <legend class="infraLegend">&nbsp;N�vel de Acesso dos Documentos&nbsp;</legend>
            <div>
                <input <?php echo $sinNAUsuExt; ?> type="radio" name="rdNivelAcesso[]" id="rdUsuExternoIndicarEntrePermitidos" onclick="changeNivelAcesso();" value="1">
                <label for="rdUsuExternoIndicarEntrePermitidos" id="lblUsuExterno" class="infraLabelRadio">Usu�rio Externo indicar diretamente</label><br/>

                <input <?php echo $sinNAPadrao; ?> type="radio" name="rdNivelAcesso[]" id="rdPadrao" onclick="changeNivelAcesso();" value="2">
                <label name="lblPadrao" id="lblPadrao" for="rdPadrao" class="infraLabelRadio">Padr�o pr� definido</label>

                <div id="divNivelAcesso" <?php echo $sinNAPadrao != '' ? 'style="display: inherit;"' : 'style="display: none;"' ?>>
                    <div style="clear:both;">&nbsp;</div>
                    <label name="lblNivelAcesso" id="lblNivelAcesso" for="selNivelAcesso" class="infraLabelObrigatorio">N�vel de Acesso: </label><br/>
                    <select id="selNivelAcesso" name="selNivelAcesso" onchange="changeSelectNivelAcesso()">
                        <?= $strItensSelNivelAcesso ?>
                    </select>
                </div>

                <div id="divHipoteseLegal" <?php echo $hipoteseLegal; ?>>
                    <div style="clear:both;">&nbsp;</div>
                    <label name="lblHipoteseLegal" id="lblHipoteseLegal" for="selHipoteseLegal" class="infraLabelObrigatorio">Hip�tese Legal:</label><br/>
                    <select id="selHipoteseLegal" name="selHipoteseLegal">
                        <?= $strItensSelHipoteseLegal ?>
                    </select>
                </div>
            </div>
        </fieldset>
    </div>
    <div style="margin-top: 15px!important;">
        <fieldset class="infraFieldset" style="width:75%;">
            <legend class="infraLegend">&nbsp;Exibir menu Peticionamento Intercorrente&nbsp; <img id="imgAjuda2" src="<?= PaginaSEI::getInstance()->getDiretorioImagensGlobal() ?>/ajuda.gif" name="ajuda" <?= PaginaSEI::montarTitleTooltip('Op��o para tornar vis�vel ou n�o o menu de Peticionamento > Intercorrente para os Usu�rios Externos no Acesso Externo do SEI.') ?> alt="Ajuda" class="infraImg"/></legend>
            <div>
                <input <?php echo $sinAtivoSim; ?> type="radio" name="rdSinAtivo[]" id="rdSinAtivoSim" value="S">
                <label for="rdSinAtivoSim" id="lblSinAtivoSim" class="infraLabelRadio">Exibir no Acesso Externo</label><br/>

                <input <?php echo $sinAtivoNao; ?> type="radio" name="rdSinAtivo[]" id="rdSinAtivoNao" value="N">
                <label name="rdSinAtivoNao" id="lblSinAtivoNao" for="rdSinAtivoNao" class="infraLabelRadio">N�o Exibir no Acesso Externo</label>

            </div>
        </fieldset>
    </div>
    <div style="clear:both;">&nbsp;</div>
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

        document.getElementById('selHipoteseLegal').value = '';
        document.getElementById('divHipoteseLegal').style.display = 'none';
        document.getElementById('selNivelAcesso').value = '';

        if (padrao) {
            document.getElementById('divNivelAcesso').style.display = "inherit";
        }
    }

    function changeSelectNivelAcesso() {
        document.getElementById('selHipoteseLegal').value = '';

        var valorSelectNivelAcesso = document.getElementById('selNivelAcesso').value;
        var valorHipoteseLegal = document.getElementById('hdnParametroHipoteseLegal').value;

        if (valorSelectNivelAcesso == 'I' && valorHipoteseLegal != '0') {
            document.getElementById('divHipoteseLegal').style.display = 'inherit';
        } else {
            document.getElementById('divHipoteseLegal').style.display = 'none';
        }
    }

    function inicializar() {
        inicializarTela();

        if ('<?=$_GET['acao']?>' == 'md_pet_intercorrente_criterio_padrao') {
            carregarComponenteTipoProcesso();
            carregarDependenciaNivelAcesso();
        }

        if ('<?=$_GET['acao']?>' == 'md_pet_intercorrente_criterio_padrao') {
            document.getElementById('txtTipoProcesso').focus();
        }
        infraEfeitoTabelas();
    }

    function carregarDependenciaNivelAcesso() {
        //Ajax para carregar os niveis de acesso ap�s a escolha do tipo de processo
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
                document.getElementById('selNivelAcesso').value = '';
                changeSelectNivelAcesso();
            }
        }
        objAutoCompletarTipoProcesso.selecionar('<?=$strIdTipoProcesso?>', '<?=PaginaSEI::getInstance()->formatarParametrosJavascript(PaginaSEI::tratarHTML($strNomeRemetente));?>');
    }

    function removerProcessoAssociado(remover) {
        document.getElementById('selNivelAcesso').innerHTML = '';
        document.getElementById('divHipoteseLegal').style.display = "none";
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

        //Validar N�vel Acesso
        var elemsNA = document.getElementsByName("rdNivelAcesso[]");

        var validoNA = false, valorNA = 0;

        for (var i = 0; i < elemsNA.length; i++) {
            if (elemsNA[i].checked === true) {
                validoNA = true;
                valorNA = parseInt(elemsNA[i].value);
            }
        }

        if (validoNA === false) {
            alert('Informe o N�vel de Acesso.');
            return false;
        }

        if (infraTrim(document.getElementById('selNivelAcesso').value) == '' && valorNA != 1) {
            alert('Informe o N�vel de Acesso.');
            document.getElementById('selNivelAcesso').focus();
            return false;
        } else if (document.getElementById('selNivelAcesso').value == 'I' && valorHipoteseLegal != '0') {

            //validar hipotese legal
            if (document.getElementById('selHipoteseLegal').value == '') {
                alert('Informe a Hip�tese legal padr�o.');
                document.getElementById('selHipoteseLegal').focus();
                return false;
            }
        }
        
        var elemsSA = document.getElementsByName("rdSinAtivo[]");
        var validoSA = false;

        for (var i = 0; i < elemsSA.length; i++) {
            if (elemsSA[i].checked === true) {
                validoSA = true;
            }
        }

        if (validoSA === false) {
            alert('Indique a op��o para exibi��o ou n�o do menu Peticionamento Intercorrente.');
            return false;
        }
        return true;
    }

    function OnSubmitForm() {
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
