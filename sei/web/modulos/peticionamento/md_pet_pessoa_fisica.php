<?
/**
* TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
*
* 14/04/2008 - criado por mga
*
* Versão do Gerador de Código: 1.14.0
*
* Versão no CVS: $Id$
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

  PaginaSEI::getInstance()->prepararSelecao('md_pet_pessoa_fisica');

  SessaoSEI::getInstance()->validarPermissao($_GET['acao']);
  
  PaginaSEI::getInstance()->salvarCamposPost(array('selOrgao','txtSiglaUsuario','txtNomeUsuario', 'txtCpfUsuario'));

  switch($_GET['acao']){
    case 'md_pet_pessoa_fisica':
      $strTitulo = 'Selecionar Usuário Externo';
      break;

    default:
      throw new InfraException("Ação '".$_GET['acao']."' não reconhecida.");
  }

  $arrComandos = array();
  
  $arrComandos[] = '<input type="submit" id="btnPesquisar" value="Pesquisar" class="infraButton" />';  
  
  if ($_GET['acao'] == 'md_pet_pessoa_fisica'){
    $arrComandos[] = '<button type="button" accesskey="T" id="btnTransportarSelecao" value="Transportar" onclick="infraTransportarSelecao();" class="infraButton"><span class="infraTeclaAtalho">T</span>ransportar</button>';
  }
  $objInfraParametro = new InfraParametro(BancoSEI::getInstance());

  $objUsuarioDTO = new UsuarioDTO();
  $objUsuarioDTO->retNumIdUsuario();
  $objUsuarioDTO->retNumIdContato();
  $objUsuarioDTO->retStrSigla();
  $objUsuarioDTO->retStrNome();
  $objUsuarioDTO->retStrStaTipo();

  $strSiglaPesquisa = trim(PaginaSEI::getInstance()->recuperarCampo('txtSiglaUsuario'));
  if ($strSiglaPesquisa!==''){
    $objUsuarioDTO->setStrSigla($strSiglaPesquisa);
  }

  $strCpfPesquisa = trim(PaginaSEI::getInstance()->recuperarCampo('txtCpfUsuario'));
  if ($strCpfPesquisa!==''){
    $objUsuarioDTO->setDblCpfContato(InfraUtil::retirarFormatacao($strCpfPesquisa));
  }

  $strNomePesquisa = PaginaSEI::getInstance()->recuperarCampo('txtNomeUsuario');
  if ($strNomePesquisa!==''){
    $objUsuarioDTO->setStrNome($strNomePesquisa);
  }

  
  $objUsuarioDTO->adicionarCriterio(array('StaTipo', 'StaTipo'),
  		array(InfraDTO::$OPER_IGUAL, InfraDTO::$OPER_IGUAL),
  		array(UsuarioRN::$TU_EXTERNO, UsuarioRN::$TU_EXTERNO),
  		array(InfraDTO::$OPER_LOGICO_OR));
  
  
  PaginaSEI::getInstance()->prepararOrdenacao($objUsuarioDTO, 'Sigla', InfraDTO::$TIPO_ORDENACAO_ASC);
  
  PaginaSEI::getInstance()->prepararPaginacao($objUsuarioDTO);

  $objUsuarioRN = new UsuarioRN();
  $arrObjUsuarioDTO = $objUsuarioRN->pesquisar($objUsuarioDTO);

  PaginaSEI::getInstance()->processarPaginacao($objUsuarioDTO);

  $numRegistros = count($arrObjUsuarioDTO);

  if ($numRegistros > 0){

    $bolCheck = false;

    if ($_GET['acao']=='md_pet_pessoa_fisica'){
      $bolAcaoConsultar = SessaoSEI::getInstance()->verificarPermissao('md_pet_pessoa_fisica');
      $bolCheck = true;
    }

    $strResultado = '';

    $strSumarioTabela = 'Tabela de Usuários Externos.';
    $strCaptionTabela = 'Usuários Externos';

    $strResultado .= '<table width="100%" class="infraTable" summary="'.$strSumarioTabela.'">'."\n";
    $strResultado .= '<caption class="infraCaption">'.PaginaSEI::getInstance()->gerarCaptionTabela($strCaptionTabela,$numRegistros).'</caption>';
    $strResultado .= '<tr>';
    if ($bolCheck) {
      $strResultado .= '<th class="infraTh"><div style="width:20px">'.PaginaSEI::getInstance()->getThCheck().'</div></th>'."\n";
    }
    
    $strResultado .= '<th class="infraTh"><div class="text-left">'.PaginaSEI::getInstance()->getThOrdenacao($objUsuarioDTO,'E-mail','Sigla',$arrObjUsuarioDTO,true).'</div></th>'."\n";
    $strResultado .= '<th class="infraTh"><div style="width:150px" class="text-left">'.PaginaSEI::getInstance()->getThOrdenacao($objUsuarioDTO,'Nome','Nome',$arrObjUsuarioDTO,true).'</div></th>'."\n";    
    
    $strResultado .= '<th class="infraTh"><div style="width:50px" class="text-center">Ações</div></th>'."\n";
    $strResultado .= '</tr>'."\n";
    $strCssTr='';
    for($i = 0;$i < $numRegistros; $i++){

      $strCssTr = ($strCssTr=='<tr class="infraTrClara">')?'<tr class="infraTrEscura">':'<tr class="infraTrClara">';
      $strResultado .= $strCssTr;

      if ($bolCheck){
        $strResultado .= '<td valign="top">'.PaginaSEI::getInstance()->getTrCheck($i,$arrObjUsuarioDTO[$i]->getNumIdContato(),$arrObjUsuarioDTO[$i]->getStrSigla()).'</td>';
      }
      //$strResultado .= '<td align="center">'.$arrObjUsuarioDTO[$i]->getNumIdUsuario().'</td>';
      $strResultado .= '<td class="text-left">'.PaginaSEI::tratarHTML($arrObjUsuarioDTO[$i]->getStrSigla()).'</td>';
      $strResultado .= '<td class="text-left">'.PaginaSEI::tratarHTML($arrObjUsuarioDTO[$i]->getStrNome()).'</td>';
      $strResultado .= '<td class="text-center">';

      $strResultado .= PaginaSEI::getInstance()->getAcaoTransportarItem($i,$arrObjUsuarioDTO[$i]->getNumIdContato());

      $strResultado .= '</td></tr>'."\n";
    }
    $strResultado .= '</table>';
  }
  if ($_GET['acao'] == 'md_pet_pessoa_fisica'){
    $arrComandos[] = '<button type="button" accesskey="C" id="btnFecharSelecao" value="Fechar" onclick="window.close();" class="infraButton">Fe<span class="infraTeclaAtalho">c</span>har</button>';
  }else{
    $arrComandos[] = '<button type="button" accesskey="C" id="btnFechar" value="Fechar" onclick="location.href=\''.SessaoSEI::getInstance()->assinarLink('controlador.php?acao='.PaginaSEI::getInstance()->getAcaoRetorno().'&acao_origem='.$_GET['acao']).'\'" class="infraButton">Fe<span class="infraTeclaAtalho">c</span>har</button>';
  }
  
  $strItensSelOrgao = OrgaoINT::montarSelectSiglaRI1358('','Todos',$numIdOrgao);

}catch(Exception $e){
  PaginaSEI::getInstance()->processarExcecao($e);
} 

PaginaSEI::getInstance()->montarDocType();
PaginaSEI::getInstance()->abrirHtml();
PaginaSEI::getInstance()->abrirHead();
PaginaSEI::getInstance()->montarMeta();
PaginaSEI::getInstance()->montarTitle(PaginaSEI::getInstance()->getStrNomeSistema().' - '.$strTitulo);
PaginaSEI::getInstance()->montarStyle();
PaginaSEI::getInstance()->abrirStyle();
PaginaSEI::getInstance()->fecharStyle();
PaginaSEI::getInstance()->montarJavaScript();
PaginaSEI::getInstance()->abrirJavaScript();
?>

function inicializar(){
  if ('<?=$_GET['acao']?>'=='md_pet_pessoa_fisica'){
    infraReceberSelecao();
    document.getElementById('btnFecharSelecao').focus();
  }else{
    document.getElementById('btnFechar').focus();
  }
  
  infraEfeitoTabelas();
}

<?
PaginaSEI::getInstance()->fecharJavaScript();
PaginaSEI::getInstance()->fecharHead();
PaginaSEI::getInstance()->abrirBody($strTitulo,'onload="inicializar();"');
?>
<form id="frmUsuarioLista" method="post" action="<?=SessaoSEI::getInstance()->assinarLink('controlador.php?acao='.$_GET['acao'].'&acao_origem='.$_GET['acao'])?>">
  <?
  //PaginaSEI::getInstance()->montarBarraLocalizacao($strTitulo);
  PaginaSEI::getInstance()->montarBarraComandosSuperior($arrComandos);
  PaginaSEI::getInstance()->abrirAreaDados();
  ?>

  <div class="row">
    <div class="col-xl-3 col-lg-4 col-md-5 col-sm-4 col-6">
      <div class="form-group">
        <label for="txtSiglaUsuario" class="infraLabelOpcional">E-mail:</label>
        <input type="text" id="txtSiglaUsuario" name="txtSiglaUsuario" class="infraText form-control" value="<?=PaginaSEI::tratarHTML($strSiglaPesquisa)?>" maxlength="100" tabindex="<?=PaginaSEI::getInstance()->getProxTabDados()?>" />
      </div>
    </div>
    <div class="col-xl-3 col-lg-4 col-md-5 col-sm-4 col-6">
      <div class="form-group">
        <label for="txtNomeUsuario" accesskey="N" class="infraLabelOpcional"><span class="infraTeclaAtalho">N</span>ome:</label>
        <input type="text" id="txtNomeUsuario" name="txtNomeUsuario" class="infraText form-control" value="<?=PaginaSEI::tratarHTML($strNomePesquisa)?>" maxlength="50" tabindex="<?=PaginaSEI::getInstance()->getProxTabDados()?>" />
      </div>
    </div>
    <div class="col-xl-2 col-lg-3 col-md-4 col-sm-4 col-4">
      <div class="form-group">
        <label for="txtCpfUsuario" class="infraLabelOpcional">CPF:</label>
        <input type="text" id="txtCpfUsuario" name="txtCpfUsuario" onkeypress="return infraMascaraCpf(this, event)" class="infraText form-control" value="<?=PaginaSEI::tratarHTML($strCpfPesquisa);?>" tabindex="<?=PaginaSEI::getInstance()->getProxTabDados()?>" />
      </div>
    </div>
  </div>
  

  <? PaginaSEI::getInstance()->fecharAreaDados(); ?>
  <div class="row">
   <div class="col-12">
     <div class="table-responsive">
       <? PaginaSEI::getInstance()->montarAreaTabela($strResultado,$numRegistros); ?>
     </div>
   </div>
 </div>
  <? 
  PaginaSEI::getInstance()->montarAreaDebug();
  PaginaSEI::getInstance()->montarBarraComandosInferior($arrComandos);
  ?>
</form>
<?
PaginaSEI::getInstance()->fecharBody();
PaginaSEI::getInstance()->fecharHtml();
?>