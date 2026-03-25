<?

/**
 * ANATEL
 *
 * 26/08/2024 - Atualização por gabrielg.colab - SPASSU
 *
 */
 
try {
  
  require_once dirname(__FILE__).'/../../SEI.php';
  
  session_start();
  SessaoSEIExterna::getInstance()->validarLink();
  SessaoSEIExterna::getInstance()->validarPermissao($_GET['acao']);
  
  //////////////////////////////////////////////////////////////////////////////
  InfraDebug::getInstance()->setBolLigado( false );
  InfraDebug::getInstance()->setBolDebugInfra( false );
  InfraDebug::getInstance()->limpar();
  //////////////////////////////////////////////////////////////////////////////
   
   $objMdPetMenuUsuarioExternoDTO = new MdPetMenuUsuarioExternoDTO();
   $objMdPetMenuUsuarioExternoDTO->retTodos();

   if( isset( $_GET['id_md_pet_usu_externo_menu'] ) && $_GET['id_md_pet_usu_externo_menu'] != ""){
   	$objMdPetMenuUsuarioExternoDTO->setNumIdMenuPeticionamentoUsuarioExterno($_GET['id_md_pet_usu_externo_menu']);
   }
 
   $objMdPetMenuUsuarioExternoRN = new MdPetMenuUsuarioExternoRN();
   $objMenuConsulta = $objMdPetMenuUsuarioExternoRN->consultar( $objMdPetMenuUsuarioExternoDTO );
 
   $objEditorRN = new EditorRN();
   
   if ($_GET['iframe']!=''){
      PaginaSEIExterna::getInstance()->abrirStyle();
      echo $objEditorRN->montarCssEditor($objMenuConsulta->retNumIdConjuntoEstilos);
      PaginaSEIExterna::getInstance()->fecharStyle();
      echo $objMenuConsulta->getStrConteudoHtml();
      die();	
   }
   
   
PaginaSEIExterna::getInstance()->montarDocType();
PaginaSEIExterna::getInstance()->abrirHtml();
PaginaSEIExterna::getInstance()->abrirHead();
PaginaSEIExterna::getInstance()->montarMeta();
PaginaSEIExterna::getInstance()->montarTitle(':: '.PaginaSEIExterna::getInstance()->getStrNomeSistema().' - '. $objMenuConsulta->getStrNome() .' ::');
PaginaSEIExterna::getInstance()->montarStyle();
PaginaSEIExterna::getInstance()->abrirStyle();
echo $objEditorRN->montarCssEditor(null);
PaginaSEIExterna::getInstance()->fecharStyle();
PaginaSEIExterna::getInstance()->montarJavaScript();
PaginaSEIExterna::getInstance()->abrirJavaScript();
PaginaSEIExterna::getInstance()->fecharJavaScript();
require_once 'js/md_pet_global_js.php';
PaginaSEIExterna::getInstance()->fecharHead();
PaginaSEIExterna::getInstance()->abrirBody( '' ,'onload="inicializar();"');
echo '<iframe id=ifrConteudoHTML name=ifrConteudoHTML style="height:100%;width:100%" frameborder="0" marginheight="0" marginwidth="0" src="' . SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=md_pet_pagina_conteudo_externo&iframe=S&id_md_pet_usu_externo_menu='. $_GET['id_md_pet_usu_externo_menu']) . '"></iframe>';
PaginaSEIExterna::getInstance()->fecharBody();
?>
<script type="text/javascript">
function inicializar(){
	  document.getElementsByTagName("BODY")[0].onresize = function() {resizeIFramePorConteudo()};
	}

document.getElementById('ifrConteudoHTML').onload = function() {
	resizeIFramePorConteudo();
}

</script>
<?
PaginaSEIExterna::getInstance()->fecharHtml();

}catch(Exception $e){	
	PaginaSEIExterna::getInstance()->processarExcecao($e);
	try{ LogSEI::getInstance()->gravar(InfraException::inspecionar($e)); }catch(Exception $e2){}
	die('Erro visualizando link de menu.');
}
?>