<?
/**
* ANATEL
*
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
  
  //=====================================================
  //INICIO - VARIAVEIS PRINCIPAIS E LISTAS DA PAGINA
  //=====================================================
  
    //Orientações
    $objMdPetVincTpProcessoDTO = new MdPetVincTpProcessoDTO();
    $objMdPetVincTpProcessoRN = new MdPetVincTpProcessoRN();

    $objMdPetVincTpProcessoDTO->retNumIdTipoProcedimento();
    $objMdPetVincTpProcessoDTO->retStrSinNaUsuarioExterno();
    $objMdPetVincTpProcessoDTO->retStrSinNaPadrao();
    $objMdPetVincTpProcessoDTO->retStrStaNivelAcesso();
    $objMdPetVincTpProcessoDTO->setStrTipoVinculo('J');
    $objMdPetVincTpProcessoDTO->retNumIdHipoteseLegal();
    $objMdPetVincTpProcessoDTO->retStrOrientacoes();

    $objMdPetVincUsuExtPj = $objMdPetVincTpProcessoRN->consultar($objMdPetVincTpProcessoDTO);
    $orientacoes = $objMdPetVincUsuExtPj->getStrOrientacoes();

    $txtOrientacoes =''; 
    if($orientacoes){
        $txtOrientacoes = $orientacoes;
    }   
    
   $objEditorRN = new EditorRN();
   
   if ($_GET['iframe']!=''){
      PaginaSEIExterna::getInstance()->abrirStyle();
      echo $objEditorRN->montarCssEditor($id_conjunto_estilos);
      PaginaSEIExterna::getInstance()->fecharStyle();
      echo $txtOrientacoes;
      die();	
   }     

}catch(Exception $e){
  PaginaSEIExterna::getInstance()->processarExcecao($e);
}

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
PaginaSEIExterna::getInstance()->fecharHead();
PaginaSEIExterna::getInstance()->abrirBody($strTitulo,'onload="inicializar();"');
?>