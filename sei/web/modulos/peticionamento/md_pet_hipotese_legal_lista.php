<?
/**
* ANATEL
*
* 30/08/2016 - criado por jaqueline.mendes@castgroup.com.br - CAST GROUP
*
*/

try {
  
  require_once dirname(__FILE__).'/../../SEI.php';

  session_start();

  //////////////////////////////////////////////////////////////////////////////
  //InfraDebug::getInstance()->setBolLigado(false);
  //InfraDebug::getInstance()->setBolDebugInfra(true);
  //InfraDebug::getInstance()->limpar();
  //////////////////////////////////////////////////////////////////////////////

  SessaoSEI::getInstance()->validarLink();
  PaginaSEI::getInstance()->prepararSelecao('md_pet_hipotese_legal_selecionar');
  SessaoSEI::getInstance()->validarPermissao($_GET['acao']);

  switch($_GET['acao']){
    case 'hipotese_legal_peticionamento_excluir':
      try{
        $arrStrIds = PaginaSEI::getInstance()->getArrStrItensSelecionados();
        $arrObjHipoteseLegalDTO = array();
        for ($i=0;$i<count($arrStrIds);$i++){
          $objHipoteseLegalDTO = new HipoteseLegalDTO();
          $objHipoteseLegalDTO->setNumIdHipoteseLegal($arrStrIds[$i]);
          $arrObjHipoteseLegalDTO[] = $objHipoteseLegalDTO;
        }
        $objHipoteseLegalRN = new HipoteseLegalRN();
        $objHipoteseLegalRN->excluir($arrObjHipoteseLegalDTO);
        PaginaSEI::getInstance()->adicionarMensagem('Operação realizada com sucesso.');
      }catch(Exception $e){
        PaginaSEI::getInstance()->processarExcecao($e);
      } 
      header('Location: '.SessaoSEI::getInstance()->assinarLink('controlador.php?acao='.$_GET['acao_origem'].'&acao_origem='.$_GET['acao']));
      die;


    case 'hipotese_legal_peticionamento_desativar':
      try{
        $arrStrIds = PaginaSEI::getInstance()->getArrStrItensSelecionados();
        $arrObjHipoteseLegalDTO = array();
        for ($i=0;$i<count($arrStrIds);$i++){
          $objHipoteseLegalDTO = new HipoteseLegalDTO();
          $objHipoteseLegalDTO->setNumIdHipoteseLegal($arrStrIds[$i]);
          $arrObjHipoteseLegalDTO[] = $objHipoteseLegalDTO;
        }
        $objHipoteseLegalRN = new HipoteseLegalRN();
        $objHipoteseLegalRN->desativar($arrObjHipoteseLegalDTO);
        PaginaSEI::getInstance()->adicionarMensagem('Operação realizada com sucesso.');
      }catch(Exception $e){
        PaginaSEI::getInstance()->processarExcecao($e);
      } 
      header('Location: '.SessaoSEI::getInstance()->assinarLink('controlador.php?acao='.$_GET['acao_origem'].'&acao_origem='.$_GET['acao']));
      die;

    case 'hipotese_legal_peticionamento_reativar':
      $strTitulo = 'Reativar Hipóteses Legais';
      if ($_GET['acao_confirmada']=='sim'){
        try{
          $arrStrIds = PaginaSEI::getInstance()->getArrStrItensSelecionados();
          $arrObjHipoteseLegalDTO = array();
          for ($i=0;$i<count($arrStrIds);$i++){
            $objHipoteseLegalDTO = new HipoteseLegalDTO();
            $objHipoteseLegalDTO->setNumIdHipoteseLegal($arrStrIds[$i]);
            $arrObjHipoteseLegalDTO[] = $objHipoteseLegalDTO;
          }
          $objHipoteseLegalRN = new HipoteseLegalRN();
          $objHipoteseLegalRN->reativar($arrObjHipoteseLegalDTO);
          PaginaSEI::getInstance()->adicionarMensagem('Operação realizada com sucesso.');
        }catch(Exception $e){
          PaginaSEI::getInstance()->processarExcecao($e);
        } 
        header('Location: '.SessaoSEI::getInstance()->assinarLink('controlador.php?acao='.$_GET['acao_origem'].'&acao_origem='.$_GET['acao']));
        die;
      } 
      break;


    case 'md_pet_hipotese_legal_selecionar':
      $strTitulo = PaginaSEI::getInstance()->getTituloSelecao('Selecionar Hipótese Legal','Selecionar Hipóteses Legais');

      //Se cadastrou alguem
      if ($_GET['acao_origem']=='hipotese_legal_peticionamento_cadastrar'){
        if (isset($_GET['id_hipotese_legal'])){
          PaginaSEI::getInstance()->adicionarSelecionado($_GET['id_hipotese_legal']);
        }
      }
      break;

    // NÃO ENCONTRADO USO
    //case 'hipotese_legal_peticionamento_listar':
    //  $strTitulo = 'Hipóteses Legais';
    //  break;

    default:
      throw new InfraException("Ação '".$_GET['acao']."' não reconhecida.");
  }

  $arrComandos = array();
  if ($_GET['acao'] == 'md_pet_hipotese_legal_selecionar'){
    $arrComandos[] = '<button type="button" accesskey="T" id="btnTransportarSelecao" value="Transportar" onclick="infraTransportarSelecao();" class="infraButton"><span class="infraTeclaAtalho">T</span>ransportar</button>';
  }

  // NÃO ENCONTRADO USO
  //if ($_GET['acao'] == 'hipotese_legal_peticionamento_listar' || $_GET['acao'] == 'md_pet_hipotese_legal_selecionar'){
    if ($_GET['acao'] == 'md_pet_hipotese_legal_selecionar'){  
    $bolAcaoCadastrar = SessaoSEI::getInstance()->verificarPermissao('hipotese_legal_peticionamento_cadastrar');
    if ($bolAcaoCadastrar){
      $arrComandos[] = '<button type="button" accesskey="N" id="btnNova" value="Nova" onclick="location.href=\''.PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?acao=hipotese_legal_peticionamento_cadastrar&acao_origem='.$_GET['acao'].'&acao_retorno='.$_GET['acao'])).'\'" class="infraButton"><span class="infraTeclaAtalho">N</span>ova</button>';
    }
  }

  $objHipoteseLegalDTO = new HipoteseLegalDTO();
  $objHipoteseLegalDTO->retNumIdHipoteseLegal();
  $objHipoteseLegalDTO->retStrStaNivelAcesso();
  $objHipoteseLegalDTO->retStrNome();
  $objHipoteseLegalDTO->retStrBaseLegal();
  //$objHipoteseLegalDTO->retStrDescricao();
  
  if ($_GET['acao'] == 'md_pet_hipotese_legal_selecionar'){
    $objHipoteseLegalDTO->setStrSinAtivo('S');
  }

  if($_GET['nvl_acesso']!= '' || $_POST['hdnNivelAcesso']!= ''){
  	$nivelAcesso = isset($_GET['nvl_acesso']) && $_GET['nvl_acesso']!= ''  ? $_GET['nvl_acesso'] : $_POST['hdnNivelAcesso'];
  	$objHipoteseLegalDTO->setStrStaNivelAcesso($nivelAcesso);
  }
  
  
  if ($_GET['acao'] == 'hipotese_legal_peticionamento_reativar'){
    //Lista somente inativos
    $objHipoteseLegalDTO->setBolExclusaoLogica(false);
    $objHipoteseLegalDTO->setStrSinAtivo('N');
  }

  $objHipoteseLegalDTO->setOrdStrNome(InfraDTO::$TIPO_ORDENACAO_ASC);
  PaginaSEI::getInstance()->prepararPaginacao($objHipoteseLegalDTO, 200);

  $objHipoteseLegalRN = new HipoteseLegalRN();
  $arrObjHipoteseLegalDTO = $objHipoteseLegalRN->listar($objHipoteseLegalDTO);

  PaginaSEI::getInstance()->processarPaginacao($objHipoteseLegalDTO);
  $numRegistros = count($arrObjHipoteseLegalDTO);

  if ($numRegistros > 0){

    $bolCheck = false;

    if ($_GET['acao']=='md_pet_hipotese_legal_selecionar'){
      $bolAcaoReativar = false;
      $bolAcaoConsultar = SessaoSEI::getInstance()->verificarPermissao('hipotese_legal_peticionamento_consultar');
      $bolAcaoAlterar = SessaoSEI::getInstance()->verificarPermissao('hipotese_legal_peticionamento_alterar');
      $bolAcaoImprimir = false;
      //$bolAcaoGerarPlanilha = false;
      $bolAcaoExcluir = false;
      $bolAcaoDesativar = false;
      $bolCheck = true;
    }else if ($_GET['acao']=='hipotese_legal_peticionamento_reativar'){
      $bolAcaoReativar = SessaoSEI::getInstance()->verificarPermissao('hipotese_legal_peticionamento_reativar');
      $bolAcaoConsultar = SessaoSEI::getInstance()->verificarPermissao('hipotese_legal_peticionamento_consultar');
      $bolAcaoAlterar = false;
      $bolAcaoImprimir = true;
      //$bolAcaoGerarPlanilha = SessaoSEI::getInstance()->verificarPermissao('infra_gerar_planilha_tabela');
      $bolAcaoExcluir = SessaoSEI::getInstance()->verificarPermissao('hipotese_legal_peticionamento_excluir');
      $bolAcaoDesativar = false;
    }else{
      $bolAcaoReativar = false;
      $bolAcaoConsultar = SessaoSEI::getInstance()->verificarPermissao('hipotese_legal_peticionamento_consultar');
      $bolAcaoAlterar = SessaoSEI::getInstance()->verificarPermissao('hipotese_legal_peticionamento_alterar');
      $bolAcaoImprimir = true;
      //$bolAcaoGerarPlanilha = SessaoSEI::getInstance()->verificarPermissao('infra_gerar_planilha_tabela');
      $bolAcaoExcluir = SessaoSEI::getInstance()->verificarPermissao('hipotese_legal_peticionamento_excluir');
      $bolAcaoDesativar = SessaoSEI::getInstance()->verificarPermissao('hipotese_legal_peticionamento_desativar');
    }

    
    if ($bolAcaoDesativar){
      $bolCheck = true;
      $arrComandos[] = '<button type="button" accesskey="t" id="btnDesativar" value="Desativar" onclick="acaoDesativacaoMultipla();" class="infraButton">Desa<span class="infraTeclaAtalho">t</span>ivar</button>';
      $strLinkDesativar = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=hipotese_legal_peticionamento_desativar&acao_origem='.$_GET['acao']);
    }

    if ($bolAcaoReativar){
      $bolCheck = true;
      $arrComandos[] = '<button type="button" accesskey="R" id="btnReativar" value="Reativar" onclick="acaoReativacaoMultipla();" class="infraButton"><span class="infraTeclaAtalho">R</span>eativar</button>';
      $strLinkReativar = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=hipotese_legal_peticionamento_reativar&acao_origem='.$_GET['acao'].'&acao_confirmada=sim');
    }
    

    if ($bolAcaoExcluir){
      $bolCheck = true;
      $arrComandos[] = '<button type="button" accesskey="E" id="btnExcluir" value="Excluir" onclick="acaoExclusaoMultipla();" class="infraButton"><span class="infraTeclaAtalho">E</span>xcluir</button>';
      $strLinkExcluir = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=hipotese_legal_peticionamento_excluir&acao_origem='.$_GET['acao']);
    }

    /*
    if ($bolAcaoGerarPlanilha){
      $bolCheck = true;
      $arrComandos[] = '<button type="button" accesskey="P" id="btnGerarPlanilha" value="Gerar Planilha" onclick="infraGerarPlanilhaTabela(\''.PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?acao=infra_gerar_planilha_tabela')).'\');" class="infraButton">Gerar <span class="infraTeclaAtalho">P</span>lanilha</button>';
    }
    */
    
    $objProtocoloRN = new ProtocoloRN();
    $arrObjNivelAcessoDTO = InfraArray::indexarArrInfraDTO($objProtocoloRN->listarNiveisAcessoRN0878(),'StaNivel');

    $strResultado = '';

    if ($_GET['acao']!='hipotese_legal_peticionamento_reativar'){
      $strSumarioTabela = 'Tabela de Hipóteses Legais.';
      $strCaptionTabela = 'Hipóteses Legais';
    }else{
      $strSumarioTabela = 'Tabela de Hipóteses Legais Inativas.';
      $strCaptionTabela = 'Hipóteses Legais Inativas';
    }

    $strResultado .= '<table width="99%" class="infraTable" summary="'.$strSumarioTabela.'">'."\n";
    $strResultado .= '<caption class="infraCaption">'.PaginaSEI::getInstance()->gerarCaptionTabela($strCaptionTabela,$numRegistros).'</caption>';
    $strResultado .= '<tr>';
    if ($bolCheck) {
      $strResultado .= '<th class="infraTh" width="1%">'.PaginaSEI::getInstance()->getThCheck().'</th>'."\n";
    }
    $strResultado .= '<th class="infraTh" width="20%">Nível de Restrição de Acesso</th>'."\n";
    $strResultado .= '<th class="infraTh" width="20%">Nome</th>'."\n";
    $strResultado .= '<th class="infraTh">Base Legal</th>'."\n";
    //$strResultado .= '<th class="infraTh">Descrição</th>'."\n";
    $strResultado .= '<th class="infraTh" width="15%">Ações</th>'."\n";
    $strResultado .= '</tr>'."\n";
    $strCssTr='';
    for($i = 0;$i < $numRegistros; $i++){

      $strCssTr = ($strCssTr=='<tr class="infraTrClara">')?'<tr class="infraTrEscura">':'<tr class="infraTrClara">';
      $strResultado .= $strCssTr;

      if ($bolCheck){
      	$nomeTransportado = '';
      	$nomeTransportado = $arrObjHipoteseLegalDTO[$i]->getStrNome().' ('.$arrObjHipoteseLegalDTO[$i]->getStrBaseLegal().')';
        $strResultado .= '<td valign="top">'.PaginaSEI::getInstance()->getTrCheck($i,$arrObjHipoteseLegalDTO[$i]->getNumIdHipoteseLegal(),$nomeTransportado).'</td>';
      }
      $strResultado .= '<td>'.$arrObjNivelAcessoDTO[$arrObjHipoteseLegalDTO[$i]->getStrStaNivelAcesso()]->getStrDescricao().'</td>';
      $strResultado .= '<td>'.$arrObjHipoteseLegalDTO[$i]->getStrNome().'</td>';
      $strResultado .= '<td>'.$arrObjHipoteseLegalDTO[$i]->getStrBaseLegal().'</td>';
      //$strResultado .= '<td>'.$arrObjHipoteseLegalDTO[$i]->getStrDescricao().'</td>';
      $strResultado .= '<td align="center">';

      $strResultado .= PaginaSEI::getInstance()->getAcaoTransportarItem($i,$arrObjHipoteseLegalDTO[$i]->getNumIdHipoteseLegal());

      if ($bolAcaoConsultar){
        $strResultado .= '<a href="'.PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?acao=hipotese_legal_peticionamento_consultar&acao_origem='.$_GET['acao'].'&acao_retorno='.$_GET['acao'].'&id_hipotese_legal='.$arrObjHipoteseLegalDTO[$i]->getNumIdHipoteseLegal())).'" tabindex="'.PaginaSEI::getInstance()->getProxTabTabela().'"><img src="'.PaginaSEI::getInstance()->getDiretorioImagensGlobal().'/consultar.gif" title="Consultar Hipótese Legal" alt="Consultar Hipótese Legal" class="infraImg" /></a>&nbsp;';
      }

      if ($bolAcaoAlterar){
        $strResultado .= '<a href="'.PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?acao=hipotese_legal_peticionamento_alterar&acao_origem='.$_GET['acao'].'&acao_retorno='.$_GET['acao'].'&id_hipotese_legal='.$arrObjHipoteseLegalDTO[$i]->getNumIdHipoteseLegal())).'" tabindex="'.PaginaSEI::getInstance()->getProxTabTabela().'"><img src="'.PaginaSEI::getInstance()->getDiretorioImagensGlobal().'/alterar.gif" title="Alterar Hipótese Legal" alt="Alterar Hipótese Legal" class="infraImg" /></a>&nbsp;';
      }

      if ($bolAcaoDesativar || $bolAcaoReativar || $bolAcaoExcluir){
        $strId = $arrObjHipoteseLegalDTO[$i]->getNumIdHipoteseLegal();
        $strDescricao = PaginaSEI::getInstance()->formatarParametrosJavaScript($arrObjHipoteseLegalDTO[$i]->getStrNome());
      }

      if ($bolAcaoDesativar){
        $strResultado .= '<a href="'.PaginaSEI::getInstance()->montarAncora($strId).'" onclick="acaoDesativar(\''.$strId.'\',\''.$strDescricao.'\');" tabindex="'.PaginaSEI::getInstance()->getProxTabTabela().'"><img src="'.PaginaSEI::getInstance()->getDiretorioImagensGlobal().'/desativar.gif" title="Desativar Hipótese Legal" alt="Desativar Hipótese Legal" class="infraImg" /></a>&nbsp;';
      }

      if ($bolAcaoReativar){
        $strResultado .= '<a href="'.PaginaSEI::getInstance()->montarAncora($strId).'" onclick="acaoReativar(\''.$strId.'\',\''.$strDescricao.'\');" tabindex="'.PaginaSEI::getInstance()->getProxTabTabela().'"><img src="'.PaginaSEI::getInstance()->getDiretorioImagensGlobal().'/reativar.gif" title="Reativar Hipótese Legal" alt="Reativar Hipótese Legal" class="infraImg" /></a>&nbsp;';
      }


      if ($bolAcaoExcluir){
        $strResultado .= '<a href="'.PaginaSEI::getInstance()->montarAncora($strId).'" onclick="acaoExcluir(\''.$strId.'\',\''.$strDescricao.'\');" tabindex="'.PaginaSEI::getInstance()->getProxTabTabela().'"><img src="'.PaginaSEI::getInstance()->getDiretorioImagensGlobal().'/excluir.gif" title="Excluir Hipótese Legal" alt="Excluir Hipótese Legal" class="infraImg" /></a>&nbsp;';
      }

      $strResultado .= '</td></tr>'."\n";
    }
    $strResultado .= '</table>';
  }
  if ($_GET['acao'] == 'md_pet_hipotese_legal_selecionar'){
    $arrComandos[] = '<button type="button" accesskey="F" id="btnFecharSelecao" value="Fechar" onclick="window.close();" class="infraButton"><span class="infraTeclaAtalho">F</span>echar</button>';
  }else{
    $arrComandos[] = '<button type="button" accesskey="F" id="btnFechar" value="Fechar" onclick="location.href=\''.PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?acao='.PaginaSEI::getInstance()->getAcaoRetorno().'&acao_origem='.$_GET['acao'])).'\'" class="infraButton"><span class="infraTeclaAtalho">F</span>echar</button>';
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
?>
<?
PaginaSEI::getInstance()->fecharStyle();
PaginaSEI::getInstance()->montarJavaScript();
PaginaSEI::getInstance()->abrirJavaScript();
?>

function inicializar(){
  if ('<?=$_GET['acao']?>'=='md_pet_hipotese_legal_selecionar'){
    infraReceberSelecao();
    document.getElementById('btnFecharSelecao').focus();
  }else{
    document.getElementById('btnFechar').focus();
  }
  infraEfeitoTabelas();
}

<? if ($bolAcaoDesativar){ ?>
function acaoDesativar(id,desc){
  if (confirm("Confirma desativação da Hipótese Legal \""+desc+"\"?")){
    document.getElementById('hdnInfraItemId').value=id;
    document.getElementById('frmHipoteseLegalLista').action='<?=$strLinkDesativar?>';
    document.getElementById('frmHipoteseLegalLista').submit();
  }
}

function acaoDesativacaoMultipla(){
  if (document.getElementById('hdnInfraItensSelecionados').value==''){
    alert('Nenhuma Hipótese Legal selecionada.');
    return;
  }
  if (confirm("Confirma desativação das Hipóteses Legais selecionadas?")){
    document.getElementById('hdnInfraItemId').value='';
    document.getElementById('frmHipoteseLegalLista').action='<?=$strLinkDesativar?>';
    document.getElementById('frmHipoteseLegalLista').submit();
  }
}
<? } ?>

<? if ($bolAcaoReativar){ ?>
function acaoReativar(id,desc){
  if (confirm("Confirma reativação da Hipótese Legal \""+desc+"\"?")){
    document.getElementById('hdnInfraItemId').value=id;
    document.getElementById('frmHipoteseLegalLista').action='<?=$strLinkReativar?>';
    document.getElementById('frmHipoteseLegalLista').submit();
  }
}

function acaoReativacaoMultipla(){
  if (document.getElementById('hdnInfraItensSelecionados').value==''){
    alert('Nenhuma Hipótese Legal selecionada.');
    return;
  }
  if (confirm("Confirma reativação das Hipóteses Legais selecionadas?")){
    document.getElementById('hdnInfraItemId').value='';
    document.getElementById('frmHipoteseLegalLista').action='<?=$strLinkReativar?>';
    document.getElementById('frmHipoteseLegalLista').submit();
  }
}
<? } ?>

<? if ($bolAcaoExcluir){ ?>
function acaoExcluir(id,desc){
  if (confirm("Confirma exclusão da Hipótese Legal \""+desc+"\"?")){
    document.getElementById('hdnInfraItemId').value=id;
    document.getElementById('frmHipoteseLegalLista').action='<?=$strLinkExcluir?>';
    document.getElementById('frmHipoteseLegalLista').submit();
  }
}

function acaoExclusaoMultipla(){
  if (document.getElementById('hdnInfraItensSelecionados').value==''){
    alert('Nenhuma Hipótese Legal selecionada.');
    return;
  }
  if (confirm("Confirma exclusão das Hipóteses Legais selecionadas?")){
    document.getElementById('hdnInfraItemId').value='';
    document.getElementById('frmHipoteseLegalLista').action='<?=$strLinkExcluir?>';
    document.getElementById('frmHipoteseLegalLista').submit();
  }
}
<? } ?>

<?
PaginaSEI::getInstance()->fecharJavaScript();
PaginaSEI::getInstance()->fecharHead();
PaginaSEI::getInstance()->abrirBody($strTitulo,'onload="inicializar();"');
?>
<form id="frmHipoteseLegalLista" method="post" action="<?=PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?acao='.$_GET['acao'].'&acao_origem='.$_GET['acao']))?>">
  
 <input type="hidden" name="hdnNivelAcesso" id="hdnNivelAcesso" value="<?php echo isset($_GET['nvl_acesso']) && $_GET['nvl_acesso'] != '' ? $_GET['nvl_acesso'] : $_POST['hdnNivelAcesso']; ?>" />
  
  <?
  PaginaSEI::getInstance()->montarBarraComandosSuperior($arrComandos);
  PaginaSEI::getInstance()->montarAreaTabela($strResultado,$numRegistros);
  PaginaSEI::getInstance()->montarBarraComandosInferior($arrComandos);
  ?>
</form>
<?
PaginaSEI::getInstance()->fecharBody();
PaginaSEI::getInstance()->fecharHtml();
?>