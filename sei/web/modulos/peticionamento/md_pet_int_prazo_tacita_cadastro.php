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
	
	$strLinkAjaxTiposProcesso       = SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=md_pet_tipo_processo_auto_completar');
	$strLinkTiposProcessoSelecao    = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=tipo_procedimento_selecionar&tipo_selecao=2&id_object=objLupaTipoProcesso');
	
	$objMdPetIntPrazoTacitaDTO = new MdPetIntPrazoTacitaDTO();
	
	$strDesabilitar = '';
	
	$arrComandos = array();
	$strTitulo = '';
	
	switch ($_GET['acao']) {
		case 'md_pet_int_prazo_tacita_cadastrar':
			$strTitulo = 'Nov ';
			$arrComandos[] = '<button type="submit" accesskey="S" name="sbmCadastrarMdPetIntPrazoTacita" value="Salvar" class="infraButton"><span class="infraTeclaAtalho">S</span>alvar</button>';
			$arrComandos[] = '<button type="button" accesskey="C" name="btnCancelar" id="btnCancelar" value="Cancelar" onclick="location.href=\'' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . PaginaSEI::getInstance()->getAcaoRetorno() . '&acao_origem=' . $_GET['acao']) . '\';" class="infraButton"><span class="infraTeclaAtalho">C</span>ancelar</button>';
			
			$objMdPetIntPrazoTacitaDTO->setNumIdMdPetIntPrazoTacita($_POST['txtIdMdPetIntPrazoTacita']);
			$objMdPetIntPrazoTacitaDTO->setNumNumPrazo($_POST['txtNumPrazo']);
			$objMdPetIntPrazoTacitaDTO->setStrStaTipoPrazo('G');
			
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
			$arrComandos[] = '<button type="button" accesskey="C" name="btnCancelar" id="btnCancelar" value="Cancelar" onclick="location.href=\'' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . PaginaSEI::getInstance()->getAcaoRetorno() . '&acao_origem=' . $_GET['acao']) . '\';" class="infraButton"><span class="infraTeclaAtalho">C</span>ancelar</button>';
			
			if (isset($_POST['sbmAlterarMdPetIntPrazoTacita'])) {
				
				try {
				 
					$objMdPetIntPrazoTacitaDTO->setNumIdMdPetIntPrazoTacita($_POST['txtIdMdPetIntPrazoTacita']);
					$objMdPetIntPrazoTacitaDTO->setNumNumPrazo($_POST['txtNumPrazo']);
					$objMdPetIntPrazoTacitaDTO->setStrStaTipoPrazo('G');
					(new MdPetIntPrazoTacitaRN())->alterar($objMdPetIntPrazoTacitaDTO);
					
					// Salvar prazo customizado
                    
                    if(isset($_POST['txtNumPrazoCustomizado']) && !empty($_POST['txtNumPrazoCustomizado']) && isset($_POST['hdnTipoProcesso']) && !empty($_POST['hdnTipoProcesso'])){
	
	                    // Extrai os valores dos Tipos de Processos
	                    $idsProcedimentos = array_column(array_map('explode', array_fill(0, count(explode('¥', $_POST['hdnTipoProcesso'])), '±'), explode('¥', $_POST['hdnTipoProcesso'])), 0);
	
	                    $objMdPetIntPrazoTacitaDTO = new MdPetIntPrazoTacitaDTO();
	                    $objMdPetIntPrazoTacitaDTO->retNumIdMdPetIntPrazoTacita();
	                    $objMdPetIntPrazoTacitaDTO->setStrStaTipoPrazo('E');
	                    $objMdPetIntPrazoTacitaDTO->setNumMaxRegistrosRetorno(1);
	                    $objMdPetIntPrazoTacitaDTO = (new MdPetIntPrazoTacitaRN())->consultar($objMdPetIntPrazoTacitaDTO);
	                    
	                    if(!empty($objMdPetIntPrazoTacitaDTO)){
		
		                    $idMdPetIntPrazoTacita = $objMdPetIntPrazoTacitaDTO->getNumIdMdPetIntPrazoTacita();
	                       
                        }else{
		
		                    $objMdPetIntPrazoTacitaDTO = new MdPetIntPrazoTacitaDTO();
		                    $objMdPetIntPrazoTacitaDTO->setNumNumPrazo($_POST['txtNumPrazoCustomizado']);
		                    $objMdPetIntPrazoTacitaDTO->setStrStaTipoPrazo('E');
		                    $objMdPetIntPrazoTacitaDTO = (new MdPetIntPrazoTacitaRN())->cadastrarCustomizado($objMdPetIntPrazoTacitaDTO);
		
		                    $idMdPetIntPrazoTacita = $objMdPetIntPrazoTacitaDTO->getNumIdMdPetIntPrazoTacita();
	                       
                        }
	                    
	                    if($objMdPetIntPrazoTacitaDTO->retNumNumPrazo() != $_POST['txtNumPrazoCustomizado']){
	                     
		                    $objMdPetIntPrazoTacitaDTO = new MdPetIntPrazoTacitaDTO();
		                    $objMdPetIntPrazoTacitaDTO->setNumIdMdPetIntPrazoTacita($idMdPetIntPrazoTacita);
		                    $objMdPetIntPrazoTacitaDTO->setNumNumPrazo($_POST['txtNumPrazoCustomizado']);
		                    $objMdPetIntPrazoTacitaDTO->setStrStaTipoPrazo('E');
		                    $objMdPetIntPrazoTacitaDTO = (new MdPetIntPrazoTacitaRN())->alterar($objMdPetIntPrazoTacitaDTO);
		                    
                        }
	
	                    (new MdPetIntPrazoTacitaRelTipoProcRN())->excluirRelacionamentosExistentes();
	
	                    foreach($idsProcedimentos as $idProcedimento){
		
		                    $objTipoProcedimentoDTO = new MdPetIntPrazoTacitaRelTipoProcDTO();
		                    $objTipoProcedimentoDTO->setNumIdMdPetIntPrazoTacita($idMdPetIntPrazoTacita);
		                    $objTipoProcedimentoDTO->setNumIdTipoProcedimento($idProcedimento);
		
		                    (new MdPetIntPrazoTacitaRelTipoProcRN())->cadastrar($objTipoProcedimentoDTO);
		
	                    }
                    
                    }
                    
                    if(isset($_POST['txtNumPrazoCustomizado']) && empty($_POST['txtNumPrazoCustomizado']) && isset($_POST['hdnTipoProcesso']) && empty($_POST['hdnTipoProcesso'])){
                     
	                    (new MdPetIntPrazoTacitaRelTipoProcRN())->excluirRelacionamentosExistentes();
	
	                    $objMdPetIntPrazoTacitaDTO = new MdPetIntPrazoTacitaDTO();
	                    $objMdPetIntPrazoTacitaDTO->retNumIdMdPetIntPrazoTacita();
	                    $objMdPetIntPrazoTacitaDTO->setStrStaTipoPrazo('E');
	                    $objMdPetIntPrazoTacitaDTO->setNumMaxRegistrosRetorno(1);
	                    $objMdPetIntPrazoTacitaDTO = (new MdPetIntPrazoTacitaRN())->consultar($objMdPetIntPrazoTacitaDTO);
	                    
	                    if(!empty($objMdPetIntPrazoTacitaDTO)){
		                    (new MdPetIntPrazoTacitaRN())->excluir([$objMdPetIntPrazoTacitaDTO]);
                        }
	                    
                    }
					
					PaginaSEI::getInstance()->adicionarMensagem("Os dados foram salvos com sucesso!", PaginaSEI::$TIPO_MSG_INFORMACAO);
					header('Location: ' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . $_GET['acao'] . '&acao_origem=' . $_GET['acao'] . PaginaSEI::getInstance()->montarAncora('')));
					die;
				} catch (Exception $e) {
					PaginaSEI::getInstance()->processarExcecao($e);
				}
			} else {
				$objMdPetIntPrazoTacitaDTO->retTodos();
				$objMdPetIntPrazoTacitaDTO->setStrStaTipoPrazo('G');
				$objMdPetIntPrazoTacitaRN = new MdPetIntPrazoTacitaRN();
				$objMdPetIntPrazoTacitaDTO = $objMdPetIntPrazoTacitaRN->consultar($objMdPetIntPrazoTacitaDTO);
				if ($objMdPetIntPrazoTacitaDTO == null) {
					throw new InfraException("Registro não encontrado.");
				}
			}
			break;
		
		case 'md_pet_int_prazo_tacita_consultar':
			$strTitulo = 'Consultar ';
			$arrComandos[] = '<button type="button" accesskey="C" name="btnCancelar" value="Cancelar" onclick="location.href=\'' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . PaginaSEI::getInstance()->getAcaoRetorno() . '&acao_origem=' . $_GET['acao'] . PaginaSEI::getInstance()->montarAncora()) . '\';" class="infraButton"><span class="infraTeclaAtalho">C</span>ancelar</button>';
			$objMdPetIntPrazoTacitaDTO->setBolExclusaoLogica(false);
			$objMdPetIntPrazoTacitaDTO->retTodos();
			$objMdPetIntPrazoTacitaDTO->setStrStaTipoPrazo('G');
			$objMdPetIntPrazoTacitaDTO = (new MdPetIntPrazoTacitaRN())->consultar($objMdPetIntPrazoTacitaDTO);
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


// Start - Preenche os campos do Prazo Tácito Customizado

$objMdPetIntPrazoTacitaCustomizadoDTO = new MdPetIntPrazoTacitaDTO();
$objMdPetIntPrazoTacitaCustomizadoDTO->setStrStaTipoPrazo('E');
$objMdPetIntPrazoTacitaCustomizadoDTO->retTodos();
$objMdPetIntPrazoTacitaCustomizadoDTO = (new MdPetIntPrazoTacitaRN())->consultar($objMdPetIntPrazoTacitaCustomizadoDTO);

$strItensSelTipoProcesso = $prazoCustomizado = '';
$idPrazoCustomizado = null;

if(!empty($objMdPetIntPrazoTacitaCustomizadoDTO)){
	
    $idPrazoCustomizado = $objMdPetIntPrazoTacitaCustomizadoDTO->getNumIdMdPetIntPrazoTacita();
	$prazoCustomizado = $objMdPetIntPrazoTacitaCustomizadoDTO->getNumNumPrazo();
	
	$arrObjTipoProcedimentoDTO = new MdPetIntPrazoTacitaRelTipoProcDTO();
	$arrObjTipoProcedimentoDTO->retNumIdTipoProcedimento();
	$arrObjTipoProcedimentoDTO->retStrNomeTipoProcedimento();
	$arrObjTipoProcedimentoDTO->setNumIdMdPetIntPrazoTacita($objMdPetIntPrazoTacitaCustomizadoDTO->getNumIdMdPetIntPrazoTacita());
	$arrObjTipoProcedimentoDTO->setOrdStrNomeTipoProcedimento(InfraDTO::$TIPO_ORDENACAO_ASC);
	$arrObjTipoProcedimentoDTO = (new MdPetIntPrazoTacitaRelTipoProcRN())->listar($arrObjTipoProcedimentoDTO);
	
	if(is_iterable($arrObjTipoProcedimentoDTO)){
		foreach($arrObjTipoProcedimentoDTO as $objTipoProcedimentoDTO){
			$strItensSelTipoProcesso .= '<option value="'.$objTipoProcedimentoDTO->getNumIdTipoProcedimento().'">'.$objTipoProcedimentoDTO->getStrNomeTipoProcedimento().'</option>';
		}
	}
	
}

// End - Preenche os campos do Prazo Tácito Customizado

PaginaSEI::getInstance()->montarDocType();
PaginaSEI::getInstance()->abrirHtml();
PaginaSEI::getInstance()->abrirHead();
PaginaSEI::getInstance()->montarMeta();
PaginaSEI::getInstance()->montarTitle(PaginaSEI::getInstance()->getStrNomeSistema() . ' - ' . $strTitulo);
PaginaSEI::getInstance()->montarStyle();
PaginaSEI::getInstance()->abrirStyle();
?>
.infraImgModulo{width:20px;}
#txtNumPrazo{width:40%;}
<?php
PaginaSEI::getInstance()->fecharStyle();
PaginaSEI::getInstance()->montarJavaScript();
PaginaSEI::getInstance()->abrirJavaScript();
?>
<? if (0){ ?>
<script type="text/javascript"><?}?>

    var objLupaTipoProcesso = null;
    var objAutoCompletarTipoProcesso = null;

    function limparTipoProcessoSelect(){
        if(confirm('Deseja realmente limpar todos os campos da seção Prazo customizado por Tipo de Processo Específicos?')) {
            $('#fldPrazoCustomizadoTipoProcessoEspecifico').children(':input').each(function() { $(this).val(''); });
            $('#fldPrazoCustomizadoTipoProcessoEspecifico select>option:eq(0)').prop('selected', true);
            $('#fldPrazoCustomizadoTipoProcessoEspecifico #selTipoProcesso').find('option').remove();
            $('#txtNumPrazoCustomizado').val('');
        }
    }

    function carregarComponenteTipoProcesso(){
        
        objAutoCompletarTipoProcesso = new infraAjaxAutoCompletar('hdnIdTipoProcesso', 'txtTipoProcesso', '<?= $strLinkAjaxTiposProcesso ?>');
        objAutoCompletarTipoProcesso.limparCampo = true;
        objAutoCompletarTipoProcesso.tamanhoMinimo = 2;

        objAutoCompletarTipoProcesso.prepararExecucao = function(){
            return 'palavras_pesquisa='+document.getElementById('txtTipoProcesso').value;
        }

        objAutoCompletarTipoProcesso.processarResultado = function(id, descricao, complemento){

            if (id != ''){
                var options = document.getElementById('selTipoProcesso').options;

                for(var i=0;i < options.length;i++){
                    if (options[i].value == id){
                        var msg = setMensagemPersonalizada(msg10Padrao, ['TipoProcesso']);
                        alert(msg);
                        break;
                    }
                }

                if (i==options.length){
                    for(i=0;i < options.length;i++){
                        options[i].selected = false;
                    }
                    opt = infraSelectAdicionarOption(document.getElementById('selTipoProcesso'), descricao ,id);
                    objLupaTipoProcesso.atualizar();
                    opt.selected = true;
                }
                document.getElementById('txtTipoProcesso').value = '';
                document.getElementById('txtTipoProcesso').focus();
            }
        }

        objLupaTipoProcesso = new infraLupaSelect('selTipoProcesso', 'hdnTipoProcesso', '<?= $strLinkTiposProcessoSelecao ?>');
    }

    function inicializar() {
        if ('<?=$_GET['acao']?>' == 'md_pet_int_prazo_tacita_alterar') {
            document.getElementById('txtNumPrazo').focus();
        } else if ('<?=$_GET['acao']?>' == 'md_pet_int_prazo_tacita_consultar') {
            infraDesabilitarCamposAreaDados();
        } else {
            document.getElementById('btnCancelar').focus();
        }
        infraEfeitoTabelas();
        carregarComponenteTipoProcesso();
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

        if (document.getElementById('selTipoProcesso').options.length == 0 && document.getElementById('txtNumPrazoCustomizado').value != '') {
            alert('Informe os Tipos de Processo.');
            document.getElementById('selTipoProcesso').focus();
            return false;
        }

        if (document.getElementById('selTipoProcesso').options.length > 0 && document.getElementById('txtNumPrazoCustomizado').value == '') {
            alert('Informe o Prazo Customizado.');
            document.getElementById('txtNumPrazoCustomizado').focus();
            return false;
        }

        return confirm('ATENÇÃO: A alteração de prazo afetará apenas novas intimações, geradas depois da alteração. Deseja continuar?') ? true : false;

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
    <div class="row">
        <div class="col-sm-6 col-md-3 col-lg-3 col-xl-2">
            <div class="mb-5">
                <input type="hidden" id="txtIdMdPetIntPrazoTacita" name="txtIdMdPetIntPrazoTacita"
                       onkeypress="return infraMascaraNumero(this, event)" class="infraText"
                       value="<?= PaginaSEI::tratarHTML($objMdPetIntPrazoTacitaDTO->getNumIdMdPetIntPrazoTacita()); ?>"
                       maxlength="11" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>

                <label id="lblNumPrazo" for="txtNumPrazo" accesskey="" class="infraLabelObrigatorio">Prazo em Dias:</label>
                <img src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/ajuda.svg" name="ajuda"
                     id="imgAjuda" <?= PaginaSEI::montarTitleTooltip('O Prazo para Intimação Tácita é aquele que, a partir da data de expedição, caso o Destinatário não consulte os documentos diretamente no sistema, a intimação será considerada automaticamente cumprida por Decurso do Prazo Tácito.\n\n Em geral, recomenda-se utilizar 10 dias, tendo como parâmetro o art. 5º, § 3º, da Lei nº 11.419/2006.\n\n ATENÇÃO: Alteração nesse prazo afetará apenas novas intimações, geradas depois da alteração.', 'Ajuda') ?>
                     class="infraImgModulo"/>
                <input type="text" id="txtNumPrazo" name="txtNumPrazo"
                       onkeypress="return infraMascaraNumero(this, event)"
                       class="infraText form-control"
                       value="<?= PaginaSEI::tratarHTML($objMdPetIntPrazoTacitaDTO->getNumNumPrazo()); ?>"
                       maxlength="2" size="15" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <fieldset id="fldPrazoCustomizadoTipoProcessoEspecifico" class="infraFieldset p-3">
                <legend class="infraLegend px-2"> Prazo customizado por Tipo de Processo Específico </legend>
                <div class="row mb-3">
                    <div class="col-sm-6 col-md-3 col-lg-3 col-xl-2">
                        <div class="form-group">
                            <label id="lbTxtNumPrazoCustomizado" for="txtNumPrazoCustomizado" accesskey="" class="infraLabelObrigatorio">Prazo customizado:</label>
                            <img src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/ajuda.svg" name="ajuda"
                                 id="imgAjuda" <?= PaginaSEI::montarTitleTooltip('O Prazo Customizado será utilizado nos Tipos de Processos específicos que forem listados abaixo. \n\nApenas para órgãos do Poder Executivo que trabalham com processo administrativo fiscal recomenda-se utilizar 15 dias neste campo, em conformidade com o art. 23, § 2º, inciso III, alínea "a", do Decreto nº 70.235/1972. \n\nCom isso, inclua apenas os Tipos de Processos específicos que sejam afetos a processo administrativo fiscal para que somente nestes processos a Intimação Eletrônica seja gerada com o Prazo Tácito Customizado. \n\nATENÇÃO: Alteração nesse prazo afetará apenas novas intimações, geradas depois da alteração.', 'Ajuda') ?>
                                 class="infraImgModulo"/>
                            <input type="text" id="txtNumPrazoCustomizado" name="txtNumPrazoCustomizado"
                                   onkeypress="return infraMascaraNumero(this, event)"
                                   class="infraText form-control"
                                   value="<?= $prazoCustomizado ?>"
                                   maxlength="2" size="15" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="form-group">
                            <div class="row">
                                <div id="divTipoProcesso" class="col-xs-5 col-sm-8 col-md-8 col-lg-6">
                                    <label id="lblTipoProcesso" for="selTipoProcesso" accesskey="" class="infraLabelObrigatorio">Tipos de Processo:</label>
                                    <input type="text" id="txtTipoProcesso" name="txtTipoProcesso" class="infraText form-control" tabindex="<?=PaginaSEI::getInstance()->getProxTabDados()?>" />
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-sm-10 col-md-10 col-lg-9">
                                    <div class="input-group">
                                        <select id="selTipoProcesso" name="selTipoProcesso" size="8" multiple="multiple" class="infraSelect form-control <?= $strDesabilitar != '' ? '' : 'mr-1'?>"  tabindex="<?=PaginaSEI::getInstance()->getProxTabDados()?>">
											<?= $strItensSelTipoProcesso ?>
                                        </select>
                                        <div id="_divOpcoesTipoProcesso" style="<?= $strDesabilitar ?>" class="ml-1">
                                            <img id="imgLupaTipoProcesso" onclick="objLupaTipoProcesso.selecionar(700,500);" src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() . '/pesquisar.svg'?>" alt="Selecionar Tipo de Processo" title="Selecionar Tipo de Processo" class="infraImg" tabindex="<?=PaginaSEI::getInstance()->getProxTabDados()?>" />
                                            <br>
                                            <img id="imgExcluirTipoProcesso" onclick="objLupaTipoProcesso.remover();" src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() . '/remover.svg'?>" alt="Remover Tipo de Documento Selecionado" title="Remover Tipo de Processo Selecionado" class="infraImg mb-4" tabindex="<?=PaginaSEI::getInstance()->getProxTabDados()?>" />
                                            <img onclick="limparTipoProcessoSelect()" title="Limpar definições desta seção" alt="Limpar definições desta seção" src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() . '/excluir.svg' ?>" class="infraImg d-block mt-5"  tabindex="<?=PaginaSEI::getInstance()->getProxTabDados()?>"/>
                                        </div>
                                    </div>
                                    <input type="hidden" class="form-control" id="hdnIdTipoProcesso" name="hdnIdTipoProcesso" value=""/>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <input type="hidden" class="form-control" id="hdnTipoProcesso" name="hdnTipoProcesso" value="<?= $_POST['hdnTipoProcesso'] ?>" />
                <input type="hidden" class="form-control" id="hdnIdTipoPrazoCustomizado" name="hdnIdTipoPrazoCustomizado" value="<?= $idPrazoCustomizado ?>" />
            </fieldset>
        </div>
    </div>
	
	<?
	PaginaSEI::getInstance()->fecharAreaDados();
	?>
</form>

<?
PaginaSEI::getInstance()->fecharBody();
PaginaSEI::getInstance()->fecharHtml();
?>
