<?
/**
* ANATEL
*
* 31/01/2019 - criada por Renato Chaves - CAST
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

  PaginaSEI::getInstance()->prepararSelecao('md_pet_pessoa_juridica');

  SessaoSEI::getInstance()->validarPermissao($_GET['acao']);

	
  switch($_GET['acao']){
   
    case 'md_pet_pessoa_juridica':
      $strTitulo = PaginaSEI::getInstance()->getTituloSelecao('Selecionar Destinatario','Selecionar Destinatario');
      break;

    default:
      throw new InfraException("Ação '".$_GET['acao']."' não reconhecida.");
  }

  $arrComandos = array();
  
  $arrComandos[] = '<button type="submit" accesskey="P" id="sbmPesquisar" value="Pesquisar" class="infraButton"><span class="infraTeclaAtalho">P</span>esquisar</button>';

  if ($_GET['acao'] == 'md_pet_pessoa_juridica'){
    $arrComandos[] = '<button type="button" accesskey="T" id="btnTransportarSelecao" value="Transportar" onclick="infraTransportarSelecao();" class="infraButton"><span class="infraTeclaAtalho">T</span>ransportar</button>';

    $arrComandos[] = '<button type="button" accesskey="C" name="sbmFechar" id="btnFecharSelecao" s  onclick="window.close();" value="Fechar" class="infraButton">Fe<span class="infraTeclaAtalho">c</span>har</button>';

  }


  $objDTOVinculo = new MdPetVincRepresentantDTO();
            
  $objDTOVinculo->retNumIdContatoVinc();
  $objDTOVinculo->retStrIdxContato();

  if(!empty($_POST['txtRazao'])){
    $objDTOVinculo->setStrRazaoSocialNomeVinc('%'.$_POST['txtRazao'].'%', InfraDTO::$OPER_LIKE);
  }
  if(!empty($_POST['txtCnpj'])){
    $objDTOVinculo->setStrCNPJ(InfraUtil::retirarFormatacao($_POST['txtCnpj']));
  }
  $objDTOVinculo->setStrStaEstado(MdPetVincRepresentantRN::$RP_ATIVO);
  $objDTOVinculo->setStrTpVinc(MdPetVincRepresentantRN::$NT_JURIDICA);
  $objDTOVinculo->setDistinct(true);
  $objDTOVinculo->retStrRazaoSocialNomeVinc();
  $objDTOVinculo->retStrCNPJ();
  $objRNVinculo = new MdPetVincRepresentantRN();

  PaginaSEI::getInstance()->prepararOrdenacao($objDTOVinculo, 'RazaoSocialNomeVinc', InfraDTO::$TIPO_ORDENACAO_ASC);

  PaginaSEI::getInstance()->prepararPaginacao($objDTOVinculo);

  $arrJuridicas = $objRNVinculo->listar($objDTOVinculo);

  //Ordenação

  PaginaSEI::getInstance()->processarPaginacao($objDTOVinculo);
  
  $numRegistros = count($arrJuridicas);


  if ($numRegistros > 0){

    $bolCheck = false;

    if ($_GET['acao']=='md_pet_pessoa_juridica'){
      $strCaptionTabela = 'Pessoas Jurídicas';
      $bolCheck = true;
    }
    $strResultado = '';

    $strResultado .= '<table width="100%" class="infraTable" summary="'.$strSumarioTabela.'">'."\n";
    $strResultado .= '<caption class="infraCaption">'.PaginaSEI::getInstance()->gerarCaptionTabela($strCaptionTabela,$numRegistros).'</caption>';
    $strResultado .= '<tr>';
    if ($bolCheck) {
      $strResultado .= '<th class="infraTh"><div style="width:20px">'.PaginaSEI::getInstance()->getThCheck().'</div></th>'."\n";
    }
    $strResultado .= '<th class="infraTh"><div>'.PaginaSEI::getInstance()->getThOrdenacao($objDTOVinculo,'Razão Social','RazaoSocialNomeVinc',$arrJuridicas).'</div></th>'."\n";
    $strResultado .= '<th class="infraTh"><div style="width:150px" class="text-center">'.PaginaSEI::getInstance()->getThOrdenacao($objDTOVinculo,'CNPJ','CNPJ',$arrJuridicas).'</div></th>'."\n";
    $strResultado .= '<th class="infraTh"><div style="width:50px" class="text-center">Ações</div></th>'."\n";
    $strResultado .= '</tr>'."\n";
    $strCssTr='';
    $strResultado .= '<tbody>';
    for($i = 0;$i < $numRegistros; $i++){

      $strCssTr = ($strCssTr=='<tr class="infraTrClara">')?'<tr class="infraTrEscura">':'<tr class="infraTrClara">';
      $strResultado .= $strCssTr;

      if ($bolCheck){
        $strResultado .= '<td valign="top" style="word-break: break-word">'.PaginaSEI::getInstance()->getTrCheck($i,$arrJuridicas[$i]->getNumIdContatoVinc(),$arrJuridicas[$i]->getStrRazaoSocialNomeVinc()).'</td>';
      }
    
      $strResultado .= '<td>'.PaginaSEI::tratarHTML($arrJuridicas[$i]->getStrRazaoSocialNomeVinc()).'</td>';
      $strResultado .= '<td class="text-center">'.InfraUtil::formatarCpfCnpj($arrJuridicas[$i]->getStrCNPJ()).'</td>';
      $strResultado .= '<td class="text-center">';
      
      $strResultado .= PaginaSEI::getInstance()->getAcaoTransportarItem($i,$arrJuridicas[$i]->getNumIdContatoVinc());
      
     
      $strResultado .= '</td></tr>'."\n";
    }
    $strResultado .= '</tbody>';
    $strResultado .= '</table>';
  }
  
  

  
}catch(Exception $e){
  PaginaSEI::getInstance()->processarExcecao($e);
} 

PaginaSEI::getInstance()->montarDocType();
PaginaSEI::getInstance()->abrirHtml();
PaginaSEI::getInstance()->abrirHead();
PaginaSEI::getInstance()->montarMeta();
PaginaSEI::getInstance()->montarTitle(':: '.PaginaSEI::getInstance()->getStrNomeSistema().' - '.$strTitulo.' ::');
PaginaSEI::getInstance()->montarStyle();
PaginaSEI::getInstance()->abrirStyle();
PaginaSEI::getInstance()->fecharStyle();
PaginaSEI::getInstance()->montarJavaScript();
PaginaSEI::getInstance()->abrirJavaScript();
PaginaSEI::getInstance()->fecharJavaScript();
PaginaSEI::getInstance()->fecharHead();
PaginaSEI::getInstance()->abrirBody($strTitulo,'onload="inicializar();"');
?>

<form id="frmSerieLista" method="post" action="<?=PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?acao='.$_GET['acao'].'&filtro='. $_GET['filtro'].'&tipoDoc='.$_GET['tipoDoc'].'&acao_origem='.$_GET['acao']))?>">
  
    <?
        PaginaSEI::getInstance()->montarBarraComandosSuperior($arrComandos);
        PaginaSEI::getInstance()->abrirAreaDados('4.5em');
    ?>
    
    <div class="row">
      <div class="col-xl-4 col-lg-4 col-md-4 col-sm-5 col-6">
          <div class="form-group">
            <label for="txtRazao" class="infraLabelOpcional">Razão Social:</label>
            <input type="text" id="txtRazao" name="txtRazao"  class="infraText form-control" tabindex="<?=PaginaSEI::getInstance()->getProxTabDados()?>"  value="<?php echo array_key_exists('txtRazao', $_POST) ? $_POST['txtRazao'] : '' ?>" />
          </div>
      </div>
      <div class="col-xl-2 col-lg-3 col-md-4 col-sm-5 col-6">
          <div class="form-group">
            <label id="lblCnpj" for="txtCnpj" accesskey="" class="infraLabelOpcional">CNPJ:</label>
            <input type="text" value="<?php echo array_key_exists('txtCnpj', $_POST) ? $_POST['txtCnpj'] : '' ?>" id="txtCnpj" name="txtCnpj" onkeypress="return infraMascaraCnpj(this, event)"  class="infraText form-control" tabindex="<?=PaginaSEI::getInstance()->getProxTabDados()?>" />
        </div>
      </div>
    </div>
  
  

   

  <?
  PaginaSEI::getInstance()->fecharAreaDados();
  echo '<div class="md_pet_pessoa_juridica">';
  PaginaSEI::getInstance()->montarAreaTabela($strResultado,$numRegistros);
  echo '</div>';
  PaginaSEI::getInstance()->montarAreaDebug();
  PaginaSEI::getInstance()->montarBarraComandosInferior($arrComandos);
  ?>
</form>
<script>
function inicializar(){
  if ('<?=$_GET['acao']?>'=='md_pet_pessoa_juridica'){
    infraReceberSelecao();
    document.getElementById('btnFecharSelecao').focus();
  }else{
    document.getElementById('btnFechar').focus();
  }
  
  infraEfeitoTabelas();
}
</script>

<?
PaginaSEI::getInstance()->fecharBody();
PaginaSEI::getInstance()->fecharHtml();
?>