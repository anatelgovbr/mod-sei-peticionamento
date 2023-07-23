<?php
/**
 * Created by PhpStorm.
 * User: Renato Chaves
 * Date: 08/07/2019
 * Time: 14:18
 */

try {

  require_once dirname(__FILE__) . '/../../SEI.php';

  session_start();

  //////////////////////////////////////////////////////////////////////////////
  InfraDebug::getInstance()->setBolLigado(false);
  InfraDebug::getInstance()->setBolDebugInfra(false);
  InfraDebug::getInstance()->limpar();
  //////////////////////////////////////////////////////////////////////////////
  PaginaSEIExterna::getInstance()->setTipoPagina(InfraPagina::$TIPO_PAGINA_SIMPLES);
  SessaoSEIExterna::getInstance()->validarLink();
  SessaoSEIExterna::getInstance()->validarPermissao($_GET['acao']);

  
  switch ($_GET['acao']) {

    case 'peticionamento_usuario_externo_vinc_validacao_procuracao':

      $objMdPetProcessoRN = new MdPetProcessoRN();
      $strTitulo = 'Conflito de Procuração Eletrônica Existente';

      break;

    default:
      throw new InfraException("Ação '" . $_GET['acao'] . "' não reconhecida.");
  }

} catch (Exception $e) {

  //removendo atributos da sessao
  //if( SessaoSEIExterna::getInstance()->isSetAtributo('docPrincipalConteudoHTML') ){
  //SessaoSEIExterna::getInstance()->removerAtributo('docPrincipalConteudoHTML');
  //}

  

  PaginaSEIExterna::getInstance()->processarExcecao($e);
}


PaginaSEIExterna::getInstance()->montarDocType();
PaginaSEIExterna::getInstance()->abrirHtml();
PaginaSEIExterna::getInstance()->abrirHead();
PaginaSEIExterna::getInstance()->montarMeta();
PaginaSEIExterna::getInstance()->montarTitle(':: ' . PaginaSEIExterna::getInstance()->getStrNomeSistema() . ' - ' . $strTitulo . ' ::');
PaginaSEIExterna::getInstance()->montarStyle();
PaginaSEIExterna::getInstance()->abrirStyle();
PaginaSEIExterna::getInstance()->fecharStyle();
PaginaSEIExterna::getInstance()->montarJavaScript();
PaginaSEIExterna::getInstance()->abrirJavaScript();
PaginaSEIExterna::getInstance()->fecharJavaScript();
PaginaSEIExterna::getInstance()->fecharHead();
PaginaSEIExterna::getInstance()->abrirBody($strTitulo);

$arrComandos = array();
?>
<form id="frmConcluir" method="post"
      action="<?= PaginaSEIExterna::getInstance()->formatarXHTML(SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=' . $_GET['acao'] . '&id_represent='.$_GET['id_represent'].'&acao_origem=' . $_GET['acao'])) ?>">
  <?
  PaginaSEIExterna::getInstance()->montarBarraComandosSuperior($arrComandos);
  PaginaSEIExterna::getInstance()->abrirAreaDados('auto');
  ?>
    <label>Conforme listagem abaixo, não foi possível emitir a nova Procuração Eletrônica em razão de conflito com Procuração Eletrônica já existente. Para emitir a nova Procuração deve-se revogar a Procuração existente.</label>
   <?php 
   //Inicio da Lista
   
   $arrIdsRepresent = explode("-",$_GET['id_represent']);
   $objMdPetVincRepresentantDTO = new MdPetVincRepresentantDTO();
   $objMdPetVincRepresentantDTO->retStrTipoRepresentante();
   $objMdPetVincRepresentantDTO->retNumIdMdPetVinculoRepresent();
   $objMdPetVincRepresentantDTO->retStrStaAbrangencia();
   $objMdPetVincRepresentantDTO->retStrTipoRepresentante();
   $objMdPetVincRepresentantDTO->retNumIdContato();
   $objMdPetVincRepresentantDTO->retNumIdMdPetVinculo();
   $objMdPetVincRepresentantDTO->retStrRazaoSocialNomeVinc();
   $objMdPetVincRepresentantDTO->retDthDataLimite();
   $objMdPetVincRepresentantDTO->setNumIdMdPetVinculoRepresent($arrIdsRepresent,InfraDTO::$OPER_IN);

   PaginaSEI::getInstance()->prepararPaginacao($objMdPetVincRepresentantDTO);
   PaginaSEI::getInstance()->prepararOrdenacao($objMdPetVincRepresentantDTO, 'TipoRepresentante', InfraDTO::$TIPO_ORDENACAO_ASC);

   $objMdPetTipoPoderLegalRN = new MdPetVincRepresentantRN();
   $arrObjMdPetVincRepresentantDTO = $objMdPetTipoPoderLegalRN->listar($objMdPetVincRepresentantDTO);

   PaginaSEI::getInstance()->processarPaginacao($objMdPetVincRepresentantDTO);

   $numRegistros = count($arrObjMdPetVincRepresentantDTO);
  
    
   if ($numRegistros > 0){
    
       $strResultado = '';

       $strResultado .= '<table width="100%" class="infraTable" summary="Lista de Procurações Eletrônicas">'."\n";
       $strResultado .= '<caption class="infraCaption">'.PaginaSEI::getInstance()->gerarCaptionTabela("Procurações Eletrônicas",$numRegistros).'</caption>';
       $strResultado .= '<tr>';
       $strResultado .= '<th class="infraTh" style="width: 190px">'.PaginaSEI::getInstance()->getThOrdenacao($objMdPetVincRepresentantDTO,'Processo','TipoRepresentante',$arrObjMdPetVincRepresentantDTO).'</th>'."\n";
       $strResultado .= '<th class="infraTh">'.PaginaSEI::getInstance()->getThOrdenacao($objMdPetVincRepresentantDTO,'Procuração','TipoRepresentante',$arrObjMdPetVincRepresentantDTO).'</th>'."\n";
       $strResultado .= '<th class="infraTh">'.PaginaSEI::getInstance()->getThOrdenacao($objMdPetVincRepresentantDTO,'Tipo','RazaoSocialNomeVinc',$arrObjMdPetVincRepresentantDTO).'</th>'."\n";
       $strResultado .= '<th class="infraTh">'.PaginaSEI::getInstance()->getThOrdenacao($objMdPetVincRepresentantDTO,'Outorgante','RazaoSocialNomeVinc',$arrObjMdPetVincRepresentantDTO).'</th>'."\n";
       $strResultado .= '<th class="infraTh">'.PaginaSEI::getInstance()->getThOrdenacao($objMdPetVincRepresentantDTO,'Outorgado','TipoRepresentante',$arrObjMdPetVincRepresentantDTO).'</th>'."\n";
       $strResultado .= '<th class="infraTh">'.PaginaSEI::getInstance()->getThOrdenacao($objMdPetVincRepresentantDTO,'Poderes Legais','StaAbrangencia',$arrObjMdPetVincRepresentantDTO).'</th>'."\n";
       $strResultado .= '<th class="infraTh">'.PaginaSEI::getInstance()->getThOrdenacao($objMdPetVincRepresentantDTO,'Abrangência','StaAbrangencia',$arrObjMdPetVincRepresentantDTO).'</th>'."\n";
       $strResultado .= '<th class="infraTh">'.PaginaSEI::getInstance()->getThOrdenacao($objMdPetVincRepresentantDTO,'Validade','TipoRepresentante',$arrObjMdPetVincRepresentantDTO).'</th>'."\n";

       $strResultado .= '</tr>'."\n";
       $strCssTr='';
       for($i = 0;$i < $numRegistros; $i++){
           
           $strCssTr = ($strCssTr=='<tr class="infraTrClara">')?'<tr class="infraTrEscura">':'<tr class="infraTrClara">';
           $strResultado .= $strCssTr;

           
           //Recuperando Processo
           $objMdPetVinculoDTO = new MdPetVinculoDTO();
           $objMdPetVinculoDTO->retStrProtocoloFormatado();
           $objMdPetVinculoDTO->setNumIdMdPetVinculo($arrObjMdPetVincRepresentantDTO[$i]->getNumIdMdPetVinculo());
           $objMdPetVinculoRN = new MdPetVinculoRN();
           $objMdPetVinculoRN = $objMdPetVinculoRN->consultar($objMdPetVinculoDTO);
           if(!empty($objMdPetVinculoRN)){
                $strResultado .= '<td valign="middle">'.$objMdPetVinculoRN->getStrProtocoloFormatado().'</td>';
           }

           //Recuperando Documento
           $objMdPetVincDocumentoDTO = new MdPetVincDocumentoDTO();
           $objMdPetVincDocumentoDTO->setNumIdMdPetVinculoRepresent($arrObjMdPetVincRepresentantDTO[$i]->getNumIdMdPetVinculoRepresent());
           $objMdPetVincDocumentoDTO->retStrProtocoloFormatadoProtocolo();
           $objMdPetVincDocumentoDTO->setStrTipoDocumento("E");
           $objMdPetVincDocumentoRN = new MdPetVincDocumentoRN();
           $arrObjMdPetVincDocumentoRN = $objMdPetVincDocumentoRN->consultar($objMdPetVincDocumentoDTO);
           $strResultado .= '<td valign="middle" style="width: 190px">'.$arrObjMdPetVincDocumentoRN->getStrProtocoloFormatadoProtocolo().'</td>';

           
           $strResultado .= '<td valign="middle">'.$arrObjMdPetVincRepresentantDTO[$i]->getStrNomeTipoRepresentante().'</td>';
           $strResultado .= '<td valign="middle">'.$arrObjMdPetVincRepresentantDTO[$i]->getStrRazaoSocialNomeVinc().'</td>';
           $strResultado .= '<td valign="middle">'.$arrObjMdPetVincRepresentantDTO[$i]->getStrNomeOutorgado().'</td>';
           $strResultado .= '<td valign="middle">'.$arrObjMdPetVincRepresentantDTO[$i]->getStrTipoPoderes().'</td>';


           //Detectando Abrangência e Tratando
           if($arrObjMdPetVincRepresentantDTO[$i]->getStrStaAbrangencia() == "Q" || $arrObjMdPetVincRepresentantDTO[$i]->getStrStaAbrangencia() == null){
                $strResultado .= '<td valign="middle">Qualquer Processo em Nome do Outorgante</td>';
           }else{
                $strResultado .= '<td valign="middle">Processos Específicos</td>';
           }

           $strResultado .= '<td valign="middle">'.$arrObjMdPetVincRepresentantDTO[$i]->getDthDataLimiteValidade().'</td>';
           $strResultado .= '</td></tr>'."\n";

       }

       $strResultado .= '</table>';

   }

   ?>

    <input type="hidden" id="hdnIdContExternoPai" name="hdnIdContExterno"/>
    <input type="hidden" id="hdnTabelaProc" name="hdnTabelaProc"/>
    
   <?php PaginaSEI::getInstance()->montarAreaTabela($strResultado,$numRegistros); ?>
</form>

<?
PaginaSEIExterna::getInstance()->fecharAreaDados();
PaginaSEIExterna::getInstance()->fecharBody();
PaginaSEIExterna::getInstance()->fecharHtml();
?>
<script type="text/javascript">

    function inicializar() {
        
        infraEfeitoTabelas();
    }

    function fecharJanela() {

        if (window.opener != null && !window.opener.closed) {
            window.opener.focus();
        }

        window.close();
    }

    $(document).ready(function(){
        if($('div.infraAreaPaginacao').text().trim() == ''){
            $('div.infraAreaPaginacao').height('0px');
        }
    });
</script>