<?
/**
* ANATEL
*
* 23/06/2016 - criado por marcelo.bezerra@cast.com.br - CAST
*
*/

try {
	
  require_once dirname(__FILE__).'/../../SEI.php';

  session_start();
  
  //////////////////////////////////////////////////////////////////////////////
  InfraDebug::getInstance()->setBolLigado(false);
  InfraDebug::getInstance()->setBolDebugInfra(false);
  InfraDebug::getInstance()->limpar();
  //////////////////////////////////////////////////////////////////////////////

  SessaoSEIExterna::getInstance()->validarLink();
  SessaoSEIExterna::getInstance()->validarPermissao($_GET['acao']);
  
  //=====================================================
  //INICIO - VARIAVEIS PRINCIPAIS E LISTAS DA PAGINA
  //=====================================================
  
  //tipo de processo escolhido
  $txtTipoProcessoEscolhido = "nome do tipo de processo escolhido";
  
  //texto de orientacoes
  $objTipoProcessoOrientacoesPeticionamentoDTO2 = new TipoProcessoOrientacoesPeticionamentoDTO();
  $objTipoProcessoOrientacoesPeticionamentoDTO2->setNumIdTipoProcessoOrientacoesPeticionamento(TipoProcessoOrientacoesPeticionamentoRN::$ID_FIXO_TP_PROCESSO_ORIENTACOES);
  $objTipoProcessoOrientacoesPeticionamentoDTO2->retTodos();
  
  $objTipoProcessoOrientacoesPeticionamentoRN  = new TipoProcessoOrientacoesPeticionamentoRN();
  $objLista = $objTipoProcessoOrientacoesPeticionamentoRN->listar($objTipoProcessoOrientacoesPeticionamentoDTO2);
  $alterar = count($objLista) > 0;
  
  $txtOrientacoes ='';
  if($alterar){
  	$txtOrientacoes = $objLista[0]->getStrOrientacoesGerais();
  }
  
  
  //preenche a combo tipo de processo
  $objTipoProcessoDTO = new TipoProcessoPeticionamentoDTO();
  $objTipoProcessoDTO->retNumIdTipoProcessoPeticionamento();
  $objTipoProcessoDTO->retStrNomeProcesso();
  $objTipoProcessoDTO->retStrOrientacoes();
  $objTipoProcessoDTO->setStrSinAtivo('S');
  $objTipoProcessoDTO->setOrdStrNomeProcesso(InfraDTO::$TIPO_ORDENACAO_ASC);
   
  $objTipoProcedimentoRN = new TipoProcessoPeticionamentoRN();
  $arrObjTipoProcedimentoDTO = $objTipoProcedimentoRN->listar($objTipoProcessoDTO);
  //print_r( $arrObjTipoProcedimentoDTO ); die(); 
  
  //foreach($arrObjTipoProcedimentoDTO as $objTipoProcessoDTO){
  	//$arrOpcoes[] = array($objTipoProcessoDTO->getNumIdTipoProcessoPeticionamento(),$objTipoProcessoDTO->getStrNome(),$objTipoProcessoDTO->getStrSinOuvidoria());
  //}
  
  //DTO basico de Processo Peticionamento Novo
  //$objIndisponibilidadePeticionamentoDTO = new IndisponibilidadePeticionamentoDTO();
  
  //=====================================================
  //FIM - VARIAVEIS PRINCIPAIS E LISTAS DA PAGINA
  //=====================================================
  
  switch($_GET['acao']){
    
  	case 'peticionamento_usuario_externo_iniciar':
  		$strTitulo = 'Peticionar Processo Novo';
  		break;
  		
    default:
      throw new InfraException("Ação '".$_GET['acao']."' não reconhecida.");
  }

}catch(Exception $e){
  PaginaSEIExterna::getInstance()->processarExcecao($e);
}

$hashAnexo = "";
$idAnexo = "";

PaginaSEIExterna::getInstance()->montarDocType();
PaginaSEIExterna::getInstance()->abrirHtml();
PaginaSEIExterna::getInstance()->abrirHead();
PaginaSEIExterna::getInstance()->montarMeta();
PaginaSEIExterna::getInstance()->montarTitle(':: '.PaginaSEIExterna::getInstance()->getStrNomeSistema().' - '.$strTitulo.' ::');
PaginaSEIExterna::getInstance()->montarStyle();
PaginaSEIExterna::getInstance()->abrirStyle();
$objEditorRN = new EditorRN();
echo $objEditorRN->montarCssEditor(null);
PaginaSEIExterna::getInstance()->fecharStyle();
PaginaSEIExterna::getInstance()->montarJavaScript();
PaginaSEIExterna::getInstance()->abrirJavaScript();
PaginaSEIExterna::getInstance()->fecharJavaScript();
?>
<style type="text/css">
#field1 {height: auto; width: 96%; margin-bottom: 11px;}
.sizeFieldset {height:auto; width: 86%;}
.fieldsetClear {border:none !important;}
</style>
<?php 
PaginaSEIExterna::getInstance()->fecharHead();
PaginaSEIExterna::getInstance()->abrirBody($strTitulo,'onload="inicializar();"');
?> 
<form id="frmIndisponibilidadeCadastro" method="post" onsubmit="return OnSubmitForm();"  
      action="<?=PaginaSEIExterna::getInstance()->formatarXHTML(SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao='.$_GET['acao'].'&acao_origem='.$_GET['acao']))?>">
<?
PaginaSEIExterna::getInstance()->abrirAreaDados('auto');
?>
 <br />
 <br />
 <fieldset id="field1" class="infraFieldset sizeFieldset">
 <legend class="infraLegend">&nbsp; Orientações Gerais &nbsp;</legend>
   <label> 
   <?= $txtOrientacoes ?>
   </label>
 </fieldset>
 
 <div id="divInfraAreaDadosDinamica" class="infraAreaDadosDinamica" style="width:50%">
<br />
<br />
<label class="infraLabelObrigatorio" style="font-size:1.6em;">Escolha o Tipo do Processo:</label>
<br />
<br />
</div>

<div id="divInfraAreaTabela" class="infraAreaTabela" style="width:51%;">

<table class="infraTable" style="background-color:white;" summary="Tabela de Tipos de Processo.">

<? foreach($arrObjTipoProcedimentoDTO as $itemDTO){ ?>
<tr class="infraTrClara"> 
  <td>
	<? $link = PaginaSEIExterna::getInstance()->formatarXHTML(SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=peticionamento_usuario_externo_cadastrar&id_tipo_procedimento=' . $itemDTO->getNumIdTipoProcessoPeticionamento() )); ?>
	<a href="<?= $link ?>" 
	   title="<?= $itemDTO->getStrOrientacoes() ?>" 
	   class="ancoraOpcao">
	<?= $itemDTO->getStrNomeProcesso() ?>
	</a>
	</td>
</tr>
<? } ?>

</table>

<input type="hidden" id="hdnInfraNroItens" name="hdnInfraNroItens" value="" />
<input type="hidden" id="hdnInfraItemId" name="hdnInfraItemId" value="" />
<input type="hidden" id="hdnInfraItensSelecionados" name="hdnInfraItensSelecionados" value="" />
<input type="hidden" id="hdnInfraSelecoes" name="hdnInfraSelecoes" value="Infra" />

</div>
   
</form>

<? 
PaginaSEIExterna::getInstance()->fecharAreaDados();
PaginaSEIExterna::getInstance()->fecharBody();
PaginaSEIExterna::getInstance()->fecharHtml();
?>
<script type="text/javascript">

function inicializar(){
  
  infraEfeitoTabelas();
  
}

function OnSubmitForm() {
	return true;
}	 
</script>