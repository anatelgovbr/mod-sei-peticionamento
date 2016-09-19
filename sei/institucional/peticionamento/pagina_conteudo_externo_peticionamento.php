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
?>
<?
PaginaSEIExterna::getInstance()->fecharJavaScript();
PaginaSEIExterna::getInstance()->fecharHead();
PaginaSEIExterna::getInstance()->abrirBody( '' ,'onload="inicializar();"');
echo '<iframe id=ifrConteudoHTML name=ifrConteudoHTML style="height:100%;width:100%" frameborder="0" marginheight="0" marginwidth="0" src="' . SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=pagina_conteudo_externo_peticionamento&iframe=S&id_md_pet_usu_externo_menu='. $_GET['id_md_pet_usu_externo_menu']) . '"></iframe>';
PaginaSEIExterna::getInstance()->fecharBody();
?>
<script type="text/javascript">
function inicializar(){
	  //infraEfeitoTabelas();
	  document.getElementsByTagName("BODY")[0].onresize = function() {resizeIFramePorConteudo()};
	}

function resizeIFramePorConteudo(){
	var id = 'ifrConteudoHTML';
	var ifrm = document.getElementById(id);
	ifrm.style.visibility = 'hidden';
	ifrm.style.height = "10px"; 

	var doc = ifrm.contentDocument? ifrm.contentDocument : ifrm.contentWindow.document;
	doc = doc || document;
	var body = doc.body, html = doc.documentElement;

	///console.clear();

	///var fieldset = document.getElementById('field1');
	///console.log('field1');
	///console.log(field1.scrollWidth+'-'+field1.offsetWidth+'-'+field1.clientWidth+'-'+field1.scrollWidth+'-'+field1.offsetWidth);

	///console.log('body.scrollWidth-body.offsetWidth-html.clientWidth-html.scrollWidth-html.offsetWidth');
	///console.log(body.scrollWidth+'-'+body.offsetWidth+'-'+html.clientWidth+'-'+html.scrollWidth+'-'+html.offsetWidth);
	var width = Math.max( body.scrollWidth, body.offsetWidth, 
	                      html.clientWidth, html.scrollWidth, html.offsetWidth );
	///ifrm.style.width=width+'px';
	ifrm.style.width='100%';

	///console.log('body.scrollHeight-body.offsetHeight-html.clientHeight-html.scrollHeight-html.offsetHeight');
	///console.log(body.scrollHeight+'-'+body.offsetHeight+'-'+html.clientHeight+'-'+html.scrollHeight+'-'+html.offsetHeight);
	var height = Math.max( body.scrollHeight, body.offsetHeight, 
	                       html.clientHeight, html.scrollHeight, html.offsetHeight );
	ifrm.style.height=height+'px';

	ifrm.style.visibility = 'visible';
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