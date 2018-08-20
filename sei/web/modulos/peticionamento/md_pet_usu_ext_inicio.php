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
  $objMdPetTpProcessoOrientacoesDTO2 = new MdPetTpProcessoOrientacoesDTO();
  $objMdPetTpProcessoOrientacoesDTO2->setNumIdTipoProcessoOrientacoesPeticionamento(MdPetTpProcessoOrientacoesRN::$ID_FIXO_TP_PROCESSO_ORIENTACOES);
  $objMdPetTpProcessoOrientacoesDTO2->retTodos();
  
  $objMdPetTpProcessoOrientacoesRN  = new MdPetTpProcessoOrientacoesRN();
  $objLista = $objMdPetTpProcessoOrientacoesRN->listar($objMdPetTpProcessoOrientacoesDTO2);
  $alterar = count($objLista) > 0;
  
  $txtOrientacoes ='';
  $id_conjunto_estilos = null;
  if($alterar){
  	$txtOrientacoes = $objLista[0]->getStrOrientacoesGerais();
  	$id_conjunto_estilos = $objLista[0]->getNumIdConjuntoEstilos();
  }
  
  
  //preenche a combo tipo de processo
  $objTipoProcessoDTO = new MdPetTipoProcessoDTO();
  $objTipoProcessoDTO->retNumIdTipoProcessoPeticionamento();
  $objTipoProcessoDTO->retStrNomeProcesso();
  $objTipoProcessoDTO->retStrOrientacoes();
  $objTipoProcessoDTO->setStrSinAtivo('S');
  $objTipoProcessoDTO->setOrdStrNomeProcesso(InfraDTO::$TIPO_ORDENACAO_ASC);
   
  $objTipoProcedimentoRN = new MdPetTipoProcessoRN();
  $arrObjTipoProcedimentoDTO = $objTipoProcedimentoRN->listar($objTipoProcessoDTO);
  
   $objEditorRN = new EditorRN();
   
   if ($_GET['iframe']!=''){
      PaginaSEIExterna::getInstance()->abrirStyle();
      echo $objEditorRN->montarCssEditor($id_conjunto_estilos);
      PaginaSEIExterna::getInstance()->fecharStyle();
      echo $txtOrientacoes;
      die();	
   }
     
  
  //=====================================================
  //FIM - VARIAVEIS PRINCIPAIS E LISTAS DA PAGINA
  //=====================================================
  
  switch($_GET['acao']){
    
  	case 'md_pet_usu_ext_iniciar':
  		$strTitulo = 'Peticionamento de Processo Novo';
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
 <fieldset id="field1" class="infraFieldset sizeFieldset" style="width:auto">
 <legend class="infraLegend">&nbsp; Orientações Gerais &nbsp;</legend>
   <? 
   echo '<iframe id=ifrConteudoHTML name=ifrConteudoHTML style="height:100%;width:100%" frameborder="0" marginheight="0" marginwidth="0" src="' . SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=md_pet_usu_ext_iniciar&iframe=S') . '"></iframe>'; 
   ?>
 </fieldset>
 <div id="divInfraAreaDadosDinamica" class="infraAreaDadosDinamica" style="width:95%;">
<br />
<label class="infraLabelObrigatorio" style="font-size:1.7em;">Escolha o Tipo do Processo que deseja iniciar:</label>
<br />
</div>

<div id="divInfraAreaTabela" class="infraAreaTabela" style="width:90%;">

<table class="infraTable" style="background-color:white; width:100%;" summary="Tabela de Tipos de Processo">

<? foreach($arrObjTipoProcedimentoDTO as $itemDTO){ ?>
<tr class="infraTrClara"> 
  <td>
	<? $link = PaginaSEIExterna::getInstance()->formatarXHTML(SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=md_pet_usu_ext_cadastrar&id_tipo_procedimento=' . $itemDTO->getNumIdTipoProcessoPeticionamento() )); ?>
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
  document.getElementsByTagName("BODY")[0].onresize = function() {resizeIFramePorConteudo()};
}

function OnSubmitForm() {
	return true;
}

function resizeIFramePorConteudo(){
	var id = 'ifrConteudoHTML';
	var ifrm = document.getElementById(id);
	ifrm.style.visibility = 'hidden';
	ifrm.style.height = "10px"; 

	var doc = ifrm.contentDocument? ifrm.contentDocument : ifrm.contentWindow.document;
	doc = doc || document;
	var body = doc.body, html = doc.documentElement;

	var width = Math.max( body.scrollWidth, body.offsetWidth, 
	                      html.clientWidth, html.scrollWidth, html.offsetWidth );
	ifrm.style.width='100%';

	var height = Math.max( body.scrollHeight, body.offsetHeight, 
	                       html.clientHeight, html.scrollHeight, html.offsetHeight );
	ifrm.style.height=height+'px';

	ifrm.style.visibility = 'visible';
}

document.getElementById('ifrConteudoHTML').onload = function() {
	resizeIFramePorConteudo();
}

</script>