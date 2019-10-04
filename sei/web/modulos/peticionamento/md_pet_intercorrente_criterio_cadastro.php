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

    $strUrlAjaxValidarNivelAcesso = SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=md_pet_tipo_processo_nivel_acesso_validar');

	//Tipo Processo
	$strLinkTipoProcessoSelecao = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=tipo_procedimento_selecionar&tipo_selecao=2&id_object=objLupaTipoProcesso');
	$strLinkAjaxTipoProcesso = SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=md_pet_intercorrente_tipo_processo_auto_completar');
    if ($_GET['acao'] == 'md_pet_intercorrente_criterio_alterar') {
        $strLinkTipoProcessoSelecao = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=tipo_procedimento_selecionar&tipo_selecao=1&id_object=objLupaTipoProcesso');
    }

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
	$sinCriterioPadrao = '';
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
    $strTipoProcesso = '';
    $valorParametroHipoteseLegal = '';
	$strItensSelNivelAcesso = '';
	$strItensSelHipoteseLegal = '';
	$strItensSelHipoteseLegal  = MdPetTipoProcessoINT::montarSelectHipoteseLegal(null, null, ProtocoloRN::$NA_RESTRITO );
    $optionTemplate = '<option value="%s" %s>%s</option>';
    $arrNivelAcesso = array(
        'P' => 'Público',
        'I' => 'Restrito'
    );
    foreach($arrNivelAcesso as $i => $nivelAcesso){
        $selected = '';
        $strItensSelNivelAcesso .= sprintf($optionTemplate, $i, $selected, $nivelAcesso);
    }

    $objInfraParametroDTO = new InfraParametroDTO();
    $objInfraParametroDTO->retTodos();
    $objInfraParametroDTO->setStrNome('SEI_HABILITAR_HIPOTESE_LEGAL');
    $objMdPetParametroRN = new MdPetParametroRN();
    $objInfraParametroDTO = $objMdPetParametroRN->consultar($objInfraParametroDTO);
    $valorParametroHipoteseLegal = $objInfraParametroDTO->getStrValor();

	if( in_array($_GET['acao'], array('md_pet_intercorrente_criterio_cadastrar', 'md_pet_intercorrente_criterio_consultar', 'md_pet_intercorrente_criterio_alterar'))){

		if (isset($_REQUEST['id_criterio_intercorrente_peticionamento']) || isset($_POST['hdnIdTipoProcesso'])){
            if (isset($_REQUEST['id_criterio_intercorrente_peticionamento'])){
                $alterar = true;
                $objMdPetCriterioDTO = new MdPetCriterioDTO();
                $objMdPetCriterioDTO->setNumIdCriterioIntercorrentePeticionamento($_GET['id_criterio_intercorrente_peticionamento']);
                $objMdPetCriterioDTO->retStrNomeProcesso();
                $objMdPetCriterioDTO->retTodos(true);

                $objMdPetCriterioRN = new MdPetCriterioRN();
                $objMdPetCriterioDTO = $objMdPetCriterioRN->consultar($objMdPetCriterioDTO);
                $IdCriterioIntercorrentePeticionamento = $_REQUEST['id_criterio_intercorrente_peticionamento'];
                $nomeTipoProcesso = $objMdPetCriterioDTO->getStrNomeProcesso();
                $idTipoProcesso   = $objMdPetCriterioDTO->getNumIdTipoProcedimento();
            } else {
                if (isset($_POST['hdnIdTipoProcesso'])) {

                    $objMdPetCriterioDTO = new MdPetCriterioDTO();
                    $objMdPetCriterioDTO->setStrStaNivelAcesso($_POST['rdNivelAcesso'][0]);
                    $objMdPetCriterioDTO->setNumIdHipoteseLegal($_POST['selHipoteseLegal']);

                    if (isset($_POST['selNivelAcesso']) && !empty($_POST['selNivelAcesso']) && $_POST['rdNivelAcesso'][0] == '2') {
                        $strStaTipoNivelAcesso = $_POST['selNivelAcesso'];
                        if ($_POST['selNivelAcesso'] == 'I') {
                            $objMdPetCriterioDTO->setNumIdHipoteseLegal($_POST['selHipoteseLegal']);
                        }
                        $objMdPetCriterioDTO->setStrStaTipoNivelAcesso($strStaTipoNivelAcesso);
                    }

                    $idTipoProcesso = $_POST['hdnIdTipoProcesso'];
                    $arrHdnIdTipoProcesso = PaginaSEI::getInstance()->getArrItensTabelaDinamica($_POST['hdnIdTipoProcesso']);
                    foreach($arrHdnIdTipoProcesso as $tipoProcesso) {
                        $strTipoProcesso .= sprintf($optionTemplate, $tipoProcesso[0], 'selected="selected"', $tipoProcesso[1]);
                    }
                }
            }

            $sinNAUsuExt         = $objMdPetCriterioDTO->getStrStaNivelAcesso() == 1 ? 'checked = checked' : '';
            $sinNAPadrao         = $objMdPetCriterioDTO->getStrStaNivelAcesso() == 2 ? 'checked = checked' : '';
            if ($objMdPetCriterioDTO->getStrStaNivelAcesso() == 2) {
                $hipoteseLegal       = $objMdPetCriterioDTO->getStrStaTipoNivelAcesso() === 'I' && $valorParametroHipoteseLegal != '0' ? 'style="display:inherit"' : 'style="display:none"';

            }

            $strItensSelHipoteseLegal  = MdPetTipoProcessoINT::montarSelectHipoteseLegal(null, null, $objMdPetCriterioDTO->getNumIdHipoteseLegal() );
            $strItensSelNivelAcesso = '';
            foreach($arrNivelAcesso as $i => $nivelAcesso){
                $selected = '';
                if ($objMdPetCriterioDTO->getStrStaNivelAcesso() == '2') {
                    $selected = ($i == $objMdPetCriterioDTO->getStrStaTipoNivelAcesso()) ? ' selected="selected" ': '';
                }
                $strItensSelNivelAcesso .= sprintf($optionTemplate, $i, $selected, $nivelAcesso);
            }
        }
	}

	switch ($_GET['acao']) {
		case 'md_pet_intercorrente_criterio_consultar':
			$strTitulo = 'Consultar Critério Intercorrente';
			$arrComandos[] = '<button type="button" accesskey="c" name="btnCancelar" id="btnCancelar" value="Fechar" onclick="location.href=\''.PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?acao='.PaginaSEI::getInstance()->getAcaoRetorno().'&acao_origem='.$_GET['acao'].PaginaSEI::getInstance()->montarAncora($_GET['id_criterio_intercorrente_peticionamento']))).'\';" class="infraButton">Fe<span class="infraTeclaAtalho">c</span>har</button>';
			break;
		case 'md_pet_intercorrente_criterio_alterar':
			$strTitulo = 'Alterar Critério para Intercorrente';
			$arrComandos[] = '<button type="submit" accesskey="s" name="sbmAlterarCriterio" id="sbmAlterarCriterio" value="Salvar" class="infraButton"><span class="infraTeclaAtalho">S</span>alvar</button>';
                        $arrComandos[] = '<button type="button" accesskey="c" name="btnCancelar" id="btnCancelar" value="Cancelar" onclick="location.href=\''.PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?acao='.PaginaSEI::getInstance()->getAcaoRetorno().'&acao_origem='.$_GET['acao'].PaginaSEI::getInstance()->montarAncora($_GET['id_criterio_intercorrente_peticionamento']))).'\';" class="infraButton"><span class="infraTeclaAtalho">C</span>ancelar</button>';

                        if (isset($_POST['sbmAlterarCriterio'])) {
				$objMdPetCriterioDTO = new MdPetCriterioDTO();
				$objMdPetCriterioDTO->setStrStaNivelAcesso($_POST['rdNivelAcesso'][0]);
				$objMdPetCriterioDTO->setNumIdHipoteseLegal($_POST['selHipoteseLegal']);
				$objMdPetCriterioDTO->setNumIdTipoProcedimento($_POST['hdnIdTipoProcesso']);
				$objMdPetCriterioDTO->setStrSinCriterioPadrao('N');
				$objMdPetCriterioDTO->setNumIdCriterioIntercorrentePeticionamento($_POST['hdnIdCriterioIntercorrentePeticionamento']);
				if (isset($_POST['selNivelAcesso'])) {
					$objMdPetCriterioDTO->setStrStaTipoNivelAcesso($_POST['selNivelAcesso']);
				}
				$objMdPetCriterioRN = new MdPetCriterioRN();
				$objMdPetCriterioRN->alterar($objMdPetCriterioDTO);
				header('Location: ' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . PaginaSEI::getInstance()->getAcaoRetorno() . '&acao_origem=' . $_GET['acao'] . '&id_criterio_intercorrente_peticionamento=' . $objMdPetCriterioDTO->getNumIdCriterioIntercorrentePeticionamento() . PaginaSEI::getInstance()->montarAncora($objMdPetCriterioDTO->getNumIdCriterioIntercorrentePeticionamento()) ));
			}
			break;
		case 'md_pet_intercorrente_criterio_cadastrar':
		case 'md_pet_intercorrente_criterio_padrao':
			$arrComandos[] = '<button type="submit" accesskey="s" name="sbmCadastrarTpProcessoPeticionamento" id="sbmCadastrarTpProcessoPeticionamento" value="Salvar" class="infraButton"><span class="infraTeclaAtalho">S</span>alvar</button>';
			$arrComandos[] = '<button type="button" accesskey="c" name="btnCancelar" id="btnCancelar" value="Cancelar" onclick="location.href=\'' . PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . PaginaSEI::getInstance()->getAcaoRetorno() . '&acao_origem=' . $_GET['acao'])) . '\';" class="infraButton"><span class="infraTeclaAtalho">C</span>ancelar</button>';

			// cadastrando o critério intercorrente
			$strTitulo = 'Novo Critério para Intercorrente';
			if ($_GET['acao'] == 'md_pet_intercorrente_criterio_padrao') {
				$strTitulo = 'Intercorrente Padrão';
			}

			if (isset($_POST['sbmCadastrarTpProcessoPeticionamento'])) {
				$objMdPetCriterioDTO = new MdPetCriterioDTO();
				$objMdPetCriterioDTO->setStrStaNivelAcesso($_POST['rdNivelAcesso'][0]);
				$objMdPetCriterioDTO->setNumIdHipoteseLegal($_POST['selHipoteseLegal']);
				$objMdPetCriterioDTO->setNumIdCriterioIntercorrentePeticionamento('');
				$objMdPetCriterioDTO->setStrSinAtivo('S');

                        if (isset($_POST['selNivelAcesso']) && !empty($_POST['selNivelAcesso']) && $_POST['rdNivelAcesso'][0] == '2') {
                            $strStaTipoNivelAcesso = $_POST['selNivelAcesso'];
                            if ($_POST['selNivelAcesso'] == 'I') {
                                $objMdPetCriterioDTO->setNumIdHipoteseLegal($_POST['selHipoteseLegal']);
                            }
                            $objMdPetCriterioDTO->setStrStaTipoNivelAcesso($strStaTipoNivelAcesso);
                        }

                $objMdPetCriterioRN = new MdPetCriterioRN();
				if ($_GET['acao'] == 'md_pet_intercorrente_criterio_padrao') {
					$objMdPetCriterioRN->cadastrarPadrao($objMdPetCriterioDTO);
				} else {
                    if(empty($_POST['hdnIdTipoProcesso'])){
                        $objMdPetCriterioDTO->setNumIdTipoProcedimento($_POST['selTipoProcesso']);
                        $objMdPetCriterioRN->cadastrar($objMdPetCriterioDTO);
                    } else {
                        $arrTipoProcesso = PaginaSEI::getInstance()->getArrValuesSelect($_POST['hdnIdTipoProcesso']);
                        foreach($arrTipoProcesso as $numTipoProcesso){
                            $objMdPetCriterioDTO->setNumIdTipoProcedimento($numTipoProcesso);
                            $objMdPetCriterioRN->cadastrar($objMdPetCriterioDTO);
                        }
                    }
				}
                header('Location: ' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . PaginaSEI::getInstance()->getAcaoRetorno() . '&acao_origem=' . $_GET['acao'] ));
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
    #lblTipoProcesso { position: absolute; left: 0%; top: 2px; width: 50%; }
    #txtTipoProcesso { position: absolute; left: 0%; top: 18px; width: 50%; }

    <? if ($_GET['acao'] == 'md_pet_intercorrente_criterio_alterar' || $_GET['acao'] == 'md_pet_intercorrente_criterio_consultar'){ ?>
        #imgLupaTipoProcesso { position: absolute; left: 51%; top: 15%; }
        #imgExcluirTipoProcesso { position: absolute; left: 53.5%; top: 15%; }
        #imgAjuda { display: none; }
        #selTipoProcesso { display: none; }
    <?} else{ ?>
        #imgLupaTipoProcesso { position: absolute; left: 71%; top: 18%; }
        #imgExcluirTipoProcesso { position: absolute; left: 71%; top: 26%; }
        #imgAjuda { position: absolute; left: 74%; top: 18%; }
    <?} ?>
    #lblNivelAcesso { width: 50%; }
    #selNivelAcesso { width: 20%; }

    #lblHipoteseLegal { width: 50%; }
    #selHipoteseLegal { width: 50%; }

    #selTipoProcesso { position: absolute; left: 0%; top: 45px; width: 70.5%; }

    .fieldsetClear { border: none !important; }

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
        <label id="lblTipoProcesso" for="txtTipoProcesso" class="infraLabelObrigatorio">Tipos de Processos: </label>
        <input type="text" onchange="removerProcessoAssociado(0);" id="txtTipoProcesso" name="txtTipoProcesso" class="infraText InfraAutoCompletar" value="<?= PaginaSEI::tratarHTML($nomeTipoProcesso) ?>" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
        <?php if($_GET['acao'] != 'md_pet_intercorrente_criterio_consultar'){ ?>
        <select name="selTipoProcesso" id="selTipoProcesso" size="8" class="infraSelect" multiple="multiple" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>">
            <?= $strTipoProcesso; ?>
        </select>
        <?php } ?>
        <input type="hidden" id="hdnIdTipoProcesso" name="hdnIdTipoProcesso" value="<?php echo $idTipoProcesso ?>"/>
        <input type="hidden" id="hdnIdCriterioIntercorrentePeticionamento" name="hdnIdCriterioIntercorrentePeticionamento" value="<?php echo $IdCriterioIntercorrentePeticionamento ?>"/>
        <img id="imgLupaTipoProcesso" onclick="objLupaTipoProcesso.selecionar(700,500);" src="/infra_css/imagens/lupa.gif" alt="Selecionar Tipo de Processo" title="Selecionar Tipo de Processo" class="infraImg"/>
        <img id="imgAjuda" src="<?= PaginaSEI::getInstance()->getDiretorioImagensGlobal() ?>/ajuda.gif" name="ajuda" <?= PaginaSEI::montarTitleTooltip('A indicação de mais de um Tipo de Processo apenas facilita aplicar o mesmo Critério para os Tipos indicados. Ou seja, em seguida, cada um terá registro próprio de Critério para Intercorrente.') ?> alt="Ajuda" class="infraImg"/>
        <img id="imgExcluirTipoProcesso" onclick="removerProcessoAssociado(0);objLupaTipoProcesso.remover();" src="/infra_css/imagens/remover.gif" alt="Remover Tipo de Processo" title="Remover Tipo de Processo" class="infraImg"/>
    </div>
    <!--  Fim do Tipo de Processo -->

    <div style="clear:both;">&nbsp;</div>
    <? if ($_GET['acao'] == 'md_pet_intercorrente_criterio_alterar' || $_GET['acao'] == 'md_pet_intercorrente_criterio_consultar'){ ?>
        <div style="margin-top: 40px!important;">
    <? } else { ?>
        <div style="margin-top: 166px!important;">
    <? }  ?>
        <fieldset class="infraFieldset" style="width:75%;">
            <legend class="infraLegend">&nbsp;Nível de Acesso dos Documentos&nbsp;</legend>
            <div>
                <input <?php echo $sinNAUsuExt; ?> type="radio" name="rdNivelAcesso[]" id="rdUsuExternoIndicarEntrePermitidos" onclick="changeNivelAcesso();" value="1" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>">
                <label for="rdUsuExternoIndicarEntrePermitidos" id="lblUsuExterno" class="infraLabelRadio">Usuário Externo indica diretamente</label><br/>

                <input <?php echo $sinNAPadrao; ?> type="radio" name="rdNivelAcesso[]" id="rdPadrao" onclick="changeNivelAcesso();" value="2" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>">
                <label name="lblPadrao" id="lblPadrao" for="rdPadrao" class="infraLabelRadio">Padrão pré definido</label>

                <div id="divNivelAcesso" <?php echo $sinNAPadrao != '' ? 'style="display: inherit;"' : 'style="display: none;"' ?>>
                    <div style="clear:both;">&nbsp;</div>
                    <label name="lblNivelAcesso" id="lblNivelAcesso" for="selNivelAcesso" class="infraLabelObrigatorio">Nível de Acesso: </label><br/>
                    <select id="selNivelAcesso" name="selNivelAcesso" onchange="changeSelectNivelAcesso()" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>">
                        <?= $strItensSelNivelAcesso ?>
                    </select>
                </div>

                <div id="divHipoteseLegal" <?php echo $hipoteseLegal; ?>>
                    <div style="clear:both;">&nbsp;</div>
                    <label name="lblHipoteseLegal" id="lblHipoteseLegal" for="selHipoteseLegal" class="infraLabelObrigatorio">Hipótese Legal:</label><br/>
                    <select id="selHipoteseLegal" name="selHipoteseLegal" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>">
                        <?= $strItensSelHipoteseLegal ?>
                    </select>
                </div>
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
    var objAjaxIdNivelAcesso = null;

    function changeNivelAcesso() {
        document.getElementById('divNivelAcesso').style.display = "none";
        var padrao = document.getElementsByName('rdNivelAcesso[]')[1].checked;

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
        if (valorSelectNivelAcesso == 'I' && valorHipoteseLegal != '0') {
            document.getElementById('divHipoteseLegal').style.display = 'inherit';
        } else {
            document.getElementById('divHipoteseLegal').style.display = 'none';
        }
    }

    function inicializar() {
        if ('<?=$_GET['acao']?>' == 'md_pet_intercorrente_criterio_cadastrar') {
            carregarComponenteTipoProcessoNovo();
            document.getElementById('txtTipoProcesso').focus();
        }else if ('<?=$_GET['acao']?>' == 'md_pet_intercorrente_criterio_alterar') {
            carregarComponenteTipoProcessoAlterar();
            document.getElementById('txtTipoProcesso').focus();
        } else if ('<?=$_GET['acao']?>'=='md_pet_intercorrente_criterio_consultar'){
            infraDesabilitarCamposAreaDados();
        }

        infraEfeitoTabelas();
    }

    function carregarComponenteTipoProcessoNovo() {
        objLupaTipoProcesso = new infraLupaSelect('selTipoProcesso', 'hdnIdTipoProcesso', '<?=$strLinkTipoProcessoSelecao?>');

        objLupaTipoProcesso.finalizarSelecao = function () {
            var options = document.getElementById('selTipoProcesso').options;
            if(options.length < 1){
                return;
            }
            for(var i = 0; i < options.length; i++){
                options[i].selected = true;
            }
            objLupaTipoProcesso.atualizar();
        };

        objAutoCompletarTipoProcesso = new infraAjaxAutoCompletar('hdnIdTipoProcesso', 'txtTipoProcesso', '<?=$strLinkAjaxTipoProcesso?>');
        objAutoCompletarTipoProcesso.limparCampo = false;
        objAutoCompletarTipoProcesso.tamanhoMinimo = 3;
        objAutoCompletarTipoProcesso.prepararExecucao = function () {
            var itensSelecionados = '';
            var options = document.getElementById('selTipoProcesso').options;

            if (options.length > 0){
                for(var i = 0; i < options.length; i++){
                    itensSelecionados += '&itens_selecionados[]=' + options[i].value;
                }
            }
            return 'palavras_pesquisa=' + document.getElementById('txtTipoProcesso').value + '&' + itensSelecionados;
        };

        objAutoCompletarTipoProcesso.processarResultado = function (id, descricao, complemento) {
            if (id!=''){
                var options = document.getElementById('selTipoProcesso').options;

                for(var i=0;i < options.length;i++){
                    if (options[i].value == id){
                        self.setTimeout('alert(\'Tipo de Processo [' + descricao + '] já consta na lista.\')',100);
                        break;
                    }
                }

                if (i==options.length){

                    for(i=0;i < options.length;i++){
                        options[i].selected = false;
                    }

                    opt = infraSelectAdicionarOption(document.getElementById('selTipoProcesso'),descricao,id);

                    objLupaTipoProcesso.atualizar();

                    opt.selected = true;
                }

                document.getElementById('txtTipoProcesso').value = '';
                document.getElementById('txtTipoProcesso').focus();
            }
        }
        objAutoCompletarTipoProcesso.selecionar('<?=$strIdTipoProcesso?>', '<?=PaginaSEI::getInstance()->formatarParametrosJavascript(PaginaSEI::tratarHTML($strNomeRemetente));?>');
    }

    function carregarComponenteTipoProcessoAlterar() {
        objLupaTipoProcesso = new infraLupaText('txtTipoProcesso', 'hdnIdTipoProcesso', '<?=$strLinkTipoProcessoSelecao?>');

        objLupaTipoProcesso.finalizarSelecao = function () {
            objAutoCompletarTipoProcesso.selecionar(document.getElementById('hdnIdTipoProcesso').value, document.getElementById('txtTipoProcesso').value);
        }

        objAutoCompletarTipoProcesso = new infraAjaxAutoCompletar('hdnIdTipoProcesso', 'txtTipoProcesso', '<?=$strLinkAjaxTipoProcesso?>');
        objAutoCompletarTipoProcesso.limparCampo = false;
        objAutoCompletarTipoProcesso.tamanhoMinimo = 3;
        objAutoCompletarTipoProcesso.prepararExecucao = function () {
            return 'palavras_pesquisa=' + document.getElementById('txtTipoProcesso').value;
        };

        objAutoCompletarTipoProcesso.processarResultado = function (id, descricao, complemento) {
            if (id != '') {
                document.getElementById('hdnIdTipoProcesso').value = id;
                document.getElementById('txtTipoProcesso').value = descricao;
            }
        }
        objAutoCompletarTipoProcesso.selecionar('<?=$strIdTipoProcesso?>', '<?=PaginaSEI::getInstance()->formatarParametrosJavascript(PaginaSEI::tratarHTML($strNomeRemetente));?>');
    }

    function removerProcessoAssociado(remover) {
        if (remover === '1') {
            objLupaTipoProcesso.remover();
        }
    }

    function validarCadastro() {
        objLupaTipoProcesso.atualizar();

        var valorHipoteseLegal = document.getElementById('hdnParametroHipoteseLegal').value;

        if (document.getElementById('selTipoProcesso').options < 1) {
            alert('Informe o Tipo de Processo.');
            return false;
        }

        //Validar Nível Acesso
        var elemsNA = document.getElementsByName("rdNivelAcesso[]");

        var validoNA = false, valorNA = 0;

        for (var i = 0; i < elemsNA.length; i++) {
            if (elemsNA[i].checked === true) {
                validoNA = true;
                valorNA = parseInt(elemsNA[i].value);
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
        } else if (document.getElementById('selNivelAcesso').value == 'I' && valorHipoteseLegal != '0') {

            //validar hipotese legal
            if (document.getElementById('selHipoteseLegal').value == '') {
                alert('Informe a Hipótese legal padrão.');
                document.getElementById('selHipoteseLegal').focus();
                return false;
            }
        }

        if(valorNA == 2) {
            var validacaoSelNivelAcesso = false;
            $.ajax({
                url: '<?=$strUrlAjaxValidarNivelAcesso?>',
                type: 'POST',
                dataType: 'XML',
                data: $('form#frmCriterioCadastro').serialize(),
                async: false,
                success: function (r) {
                    if ($(r).find('MensagemValidacao').text()) {
                        alert($(r).find('MensagemValidacao').text());
                    } else {
                        validacaoSelNivelAcesso = true;
                    }
                },
                error: function (e) {
                    if ($(e.responseText).find('MensagemValidacao').text()) {
                        alert($(e.responseText).find('MensagemValidacao').text());
                    }
                }
            });

            if(validacaoSelNivelAcesso == false ){
                return validacaoSelNivelAcesso;
            }
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