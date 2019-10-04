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

  SessaoSEI::getInstance()->validarLink();
  
  //=====================================================
  //INICIO - VARIAVEIS PRINCIPAIS E LISTAS DA PAGINA
  //=====================================================
  
    //Orientações Tipo de Destinatário
    $objMdPetIntOrientacoesDTO2 = new MdPetIntOrientacoesDTO();
    $objMdPetIntOrientacoesDTO2->setNumIdIntOrientacoesTipoDestinatario(MdPetIntOrientacoesRN::$ID_FIXO_INT_ORIENTACOES);
    $objMdPetIntOrientacoesDTO2->retTodos();
    
    $objMdPetIntOrientacoesRN  = new MdPetIntOrientacoesRN();
    $objLista = $objMdPetIntOrientacoesRN->listar($objMdPetIntOrientacoesDTO2);
    $alterar = count($objLista) > 0;
  		
    $txtOrientacoes =''; 
    if($alterar){
        $txtOrientacoes = $objLista[0]->getStrOrientacoesTipoDestinatario();
    } 
  
   $objEditorRN = new EditorRN();
   
   if ($_GET['iframe']!=''){
      PaginaSEI::getInstance()->abrirStyle();
      echo $objEditorRN->montarCssEditor($id_conjunto_estilos);
      PaginaSEI::getInstance()->fecharStyle();
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