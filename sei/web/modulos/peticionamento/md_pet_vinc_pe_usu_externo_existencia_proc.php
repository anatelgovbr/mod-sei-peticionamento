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
      $strTitulo = 'Procuração Eletrônica já Existente';

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
$arrComandos[] = '<button tabindex="-1" type="button" accesskey="c" name="btnFechar" value="Fechar" onclick="fecharJanela()" class="infraButton">Fe<span class="infraTeclaAtalho">c</span>har</button>';
?>
<form id="frmConcluir" method="post"
      action="<?= PaginaSEIExterna::getInstance()->formatarXHTML(SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=' . $_GET['acao'] . '&id_represent='.$_GET['id_represent'].'&acao_origem=' . $_GET['acao'])) ?>">
  <?
  PaginaSEIExterna::getInstance()->montarBarraComandosSuperior($arrComandos);
  PaginaSEIExterna::getInstance()->abrirAreaDados('auto');
  ?>
    <label>Não foi possível Peticionar a presente Procuração em razão de conflito de informações com Procurações Eletrônicas já existentes:</label>
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

       $strResultado .= '<table width="99%" class="infraTable" summary="Lista de Procurações Eletrônicas">'."\n";
       $strResultado .= '<caption class="infraCaption">'.PaginaSEI::getInstance()->gerarCaptionTabela("Procurações Eletrônicas",$numRegistros).'</caption>';
       $strResultado .= '<tr>';
       $strResultado .= '<th class="infraTh" style="width: 150px">'.PaginaSEI::getInstance()->getThOrdenacao($objMdPetVincRepresentantDTO,'Processo','TipoRepresentante',$arrObjMdPetVincRepresentantDTO).'</th>'."\n";
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
           if(count($objMdPetVinculoRN) > 0){
           $strResultado .= '<td valign="middle">'.$objMdPetVinculoRN->getStrProtocoloFormatado().'</td>';
           }
           //Fim Recuperando Processo

           //Recuperando Documento
           $objMdPetVincDocumentoDTO = new MdPetVincDocumentoDTO();
           $objMdPetVincDocumentoDTO->setNumIdMdPetVinculoRepresent($arrObjMdPetVincRepresentantDTO[$i]->getNumIdMdPetVinculoRepresent());
           $objMdPetVincDocumentoDTO->retStrProtocoloFormatadoProtocolo();
           $objMdPetVincDocumentoDTO->setStrTipoDocumento("E");
           $objMdPetVincDocumentoRN = new MdPetVincDocumentoRN();
           $arrObjMdPetVincDocumentoRN = $objMdPetVincDocumentoRN->consultar($objMdPetVincDocumentoDTO);
           $strResultado .= '<td valign="middle">'.$arrObjMdPetVincDocumentoRN->getStrProtocoloFormatadoProtocolo().'</td>';
           //Fim Recuperando Documento

           
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
//Fim da Lista

   ?>

    <input type="hidden" id="hdnIdContExternoPai" name="hdnIdContExterno"/>
    <input type="hidden" id="hdnTabelaProc" name="hdnTabelaProc"/>
    
   <?php PaginaSEI::getInstance()->montarAreaTabela($strResultado,$numRegistros); ?>
   <label>Caso seja necessário, para Peticionar a presente Procuração antes o Outorgante deve Revogá-la ou o próprio Outorgado deve Renunciá-la.</label>
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
</script>