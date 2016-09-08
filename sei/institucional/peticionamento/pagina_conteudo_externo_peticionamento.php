<?
try {
  
  require_once dirname(__FILE__).'/../../SEI.php';
  //require_once ("ConverteURI.php");
  
  session_start();
  SessaoSEIExterna::getInstance()->validarLink();
  SessaoSEIExterna::getInstance()->validarPermissao($_GET['acao']);
  
  //////////////////////////////////////////////////////////////////////////////
  InfraDebug::getInstance()->setBolLigado( false );
  InfraDebug::getInstance()->setBolDebugInfra( false );
  InfraDebug::getInstance()->limpar();
  //////////////////////////////////////////////////////////////////////////////
  //ConverteURI::converterURI();
  //if (isset($_GET['id_acesso_externo'])){
     //SessaoSEIExterna::getInstance($_GET['id_acesso_externo'])->validarLink();  
   //}else{
     //SessaoSEIExterna::getInstance()->validarLink();
   //}
   
   //echo "página limpa";
   //print_r($_SESSION);
   
   $objMenuPeticionamentoUsuarioExternoDTO = new MenuPeticionamentoUsuarioExternoDTO();
   $objMenuPeticionamentoUsuarioExternoDTO->retTodos();

   if( isset( $_GET['id_md_pet_usu_externo_menu'] ) && $_GET['id_md_pet_usu_externo_menu'] != ""){
   	$objMenuPeticionamentoUsuarioExternoDTO->setNumIdMenuPeticionamentoUsuarioExterno($_GET['id_md_pet_usu_externo_menu']);
   }
 
   $objMenuPeticionamentoUsuarioExternoRN = new MenuPeticionamentoUsuarioExternoRN();
   $objMenuConsulta = $objMenuPeticionamentoUsuarioExternoRN->consultar( $objMenuPeticionamentoUsuarioExternoDTO );
 
   if ($_GET['iframe']!=''){
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

$objEditorRN = new EditorRN();
echo $objEditorRN->montarCssEditor(null);

PaginaSEIExterna::getInstance()->fecharStyle();
PaginaSEIExterna::getInstance()->montarJavaScript();
PaginaSEIExterna::getInstance()->abrirJavaScript();
?>
function inicializar(){
  //document.getElementById('pwdSenhaAtual').focus();
  //infraEfeitoTabelas();
  resizeIFrameToFitContent(document.getElementById('ifrConteudoHMTL'));
}
function resizeIFrameToFitContent( iFrame ) {
    //iFrame.width  = iFrame.contentWindow.document.body.scrollWidth;
    iFrame.width  = document.getElementById("divInfraAreaTelaD").scrollWidth*.98;
    
    //document.getElementById("divInfraAreaTelaD").scrollHeight = 0;
    iFrame.height = iFrame.contentWindow.document.body.scrollHeight*1.02;
    //iFrame.height = document.getElementById("divInfraAreaTelaD").scrollHeight;
}
<?
PaginaSEIExterna::getInstance()->fecharJavaScript();
PaginaSEIExterna::getInstance()->fecharHead();
PaginaSEIExterna::getInstance()->abrirBody( '' ,'onload="inicializar();"');
echo '<iframe id=ifrConteudoHMTL name=ifrConteudoHMTL frameborder="0" marginheight="0" marginwidth="0" src="' . SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=pagina_conteudo_externo_peticionamento&iframe=S&id_md_pet_usu_externo_menu='. $_GET['id_md_pet_usu_externo_menu']) . '"></iframe>';
PaginaSEIExterna::getInstance()->fecharBody();
PaginaSEIExterna::getInstance()->fecharHtml();

}catch(Exception $e){	
	PaginaSEIExterna::getInstance()->processarExcecao($e);
	try{ LogSEI::getInstance()->gravar(InfraException::inspecionar($e)); }catch(Exception $e2){}
	die('Erro visualizando link de menu.');
}
?>