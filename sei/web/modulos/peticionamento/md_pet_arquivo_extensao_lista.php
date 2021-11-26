<?
/**
* TRIBUNAL REGIONAL FEDERAL DA 4� REGI�O
*
* 08/02/2012 - criado por bcu
*
* Vers�o do Gerador de C�digo: 1.32.1
*
* Vers�o no CVS: $Id: md_pet_arquivo_extensao_lista.php 8743 2014-04-23 17:40:44Z mga $
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

  PaginaSEI::getInstance()->prepararSelecao('md_pet_arquivo_extensao_selecionar');

  SessaoSEI::getInstance()->validarPermissao($_GET['acao']);

  switch($_GET['acao']){
    case 'arquivo_extensao_excluir':
      try{
        $arrStrIds = PaginaSEI::getInstance()->getArrStrItensSelecionados();
        $arrObjArquivoExtensaoDTO = array();
        for ($i=0;$i<count($arrStrIds);$i++){
          $objArquivoExtensaoDTO = new ArquivoExtensaoDTO();
          $objArquivoExtensaoDTO->setNumIdArquivoExtensao($arrStrIds[$i]);
          $arrObjArquivoExtensaoDTO[] = $objArquivoExtensaoDTO;
        }
        $objArquivoExtensaoRN = new ArquivoExtensaoRN();
        $objArquivoExtensaoRN->excluir($arrObjArquivoExtensaoDTO);
        PaginaSEI::getInstance()->adicionarMensagem('Opera��o realizada com sucesso.');
      }catch(Exception $e){
        PaginaSEI::getInstance()->processarExcecao($e);
      } 
      header('Location: '.SessaoSEI::getInstance()->assinarLink('controlador.php?acao='.$_GET['acao_origem'].'&acao_origem='.$_GET['acao']));
      die;


    case 'arquivo_extensao_desativar':
      try{
        $arrStrIds = PaginaSEI::getInstance()->getArrStrItensSelecionados();
        $arrObjArquivoExtensaoDTO = array();
        for ($i=0;$i<count($arrStrIds);$i++){
          $objArquivoExtensaoDTO = new ArquivoExtensaoDTO();
          $objArquivoExtensaoDTO->setNumIdArquivoExtensao($arrStrIds[$i]);
          $arrObjArquivoExtensaoDTO[] = $objArquivoExtensaoDTO;
        }
        $objArquivoExtensaoRN = new ArquivoExtensaoRN();
        $objArquivoExtensaoRN->desativar($arrObjArquivoExtensaoDTO);
        PaginaSEI::getInstance()->adicionarMensagem('Opera��o realizada com sucesso.');
      }catch(Exception $e){
        PaginaSEI::getInstance()->processarExcecao($e);
      } 
      header('Location: '.SessaoSEI::getInstance()->assinarLink('controlador.php?acao='.$_GET['acao_origem'].'&acao_origem='.$_GET['acao']));
      die;

    case 'arquivo_extensao_reativar':
      $strTitulo = 'Reativar Extens�es de Arquivos';
      if ($_GET['acao_confirmada']=='sim'){
        try{
          $arrStrIds = PaginaSEI::getInstance()->getArrStrItensSelecionados();
          $arrObjArquivoExtensaoDTO = array();
          for ($i=0;$i<count($arrStrIds);$i++){
            $objArquivoExtensaoDTO = new ArquivoExtensaoDTO();
            $objArquivoExtensaoDTO->setNumIdArquivoExtensao($arrStrIds[$i]);
            $arrObjArquivoExtensaoDTO[] = $objArquivoExtensaoDTO;
          }
          $objArquivoExtensaoRN = new ArquivoExtensaoRN();
          $objArquivoExtensaoRN->reativar($arrObjArquivoExtensaoDTO);
          PaginaSEI::getInstance()->adicionarMensagem('Opera��o realizada com sucesso.');
        }catch(Exception $e){
          PaginaSEI::getInstance()->processarExcecao($e);
        } 
        header('Location: '.SessaoSEI::getInstance()->assinarLink('controlador.php?acao='.$_GET['acao_origem'].'&acao_origem='.$_GET['acao']));
        die;
      } 
      break;


    case 'md_pet_arquivo_extensao_selecionar':
      $strTitulo = PaginaSEI::getInstance()->getTituloSelecao('Selecionar Extens�o de Arquivo','Selecionar Extens�es de Arquivos');

      //Se cadastrou alguem
      if ($_GET['acao_origem']=='arquivo_extensao_cadastrar'){
        if (isset($_GET['id_arquivo_extensao'])){
          PaginaSEI::getInstance()->adicionarSelecionado($_GET['id_arquivo_extensao']);
        }
      }
      break;

    case 'arquivo_extensao_listar':
      $strTitulo = 'Extens�es de Arquivos Permitidas';
      break;

    default:
      throw new InfraException("A��o '".$_GET['acao']."' n�o reconhecida.");
  }

  $arrComandos = array();
  if ($_GET['acao'] == 'md_pet_arquivo_extensao_selecionar'){
    $arrComandos[] = '<button type="button" accesskey="T" id="btnTransportarSelecao" value="Transportar" onclick="infraTransportarSelecao();" class="infraButton"><span class="infraTeclaAtalho">T</span>ransportar</button>';
  }

  if ($_GET['acao'] == 'arquivo_extensao_listar' || $_GET['acao'] == 'md_pet_arquivo_extensao_selecionar'){
    $bolAcaoCadastrar = SessaoSEI::getInstance()->verificarPermissao('arquivo_extensao_cadastrar');
    if ($bolAcaoCadastrar){
      $arrComandos[] = '<button type="button" accesskey="N" id="btnNova" value="Nova" onclick="location.href=\''.PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?acao=arquivo_extensao_cadastrar&acao_origem='.$_GET['acao'].'&acao_retorno='.$_GET['acao'])).'\'" class="infraButton"><span class="infraTeclaAtalho">N</span>ova</button>';
    }
  }

  $objArquivoExtensaoDTO = new ArquivoExtensaoDTO();
  $objArquivoExtensaoDTO->retNumIdArquivoExtensao();
  $objArquivoExtensaoDTO->retStrExtensao();
  $objArquivoExtensaoDTO->retStrDescricao();

  if ($_GET['acao'] == 'arquivo_extensao_reativar'){
    //Lista somente inativos
    $objArquivoExtensaoDTO->setBolExclusaoLogica(false);
    $objArquivoExtensaoDTO->setStrSinAtivo('N');
  }

  PaginaSEI::getInstance()->prepararOrdenacao($objArquivoExtensaoDTO, 'Extensao', InfraDTO::$TIPO_ORDENACAO_ASC);

  $objArquivoExtensaoRN = new ArquivoExtensaoRN();
  $arrObjArquivoExtensaoDTO = $objArquivoExtensaoRN->listar($objArquivoExtensaoDTO);

  $numRegistros = count($arrObjArquivoExtensaoDTO);

  if ($numRegistros > 0){

    $bolCheck = false;

    if ($_GET['acao']=='md_pet_arquivo_extensao_selecionar'){
      $bolAcaoReativar = false;
      $bolAcaoConsultar = SessaoSEI::getInstance()->verificarPermissao('arquivo_extensao_consultar');
      $bolAcaoAlterar = SessaoSEI::getInstance()->verificarPermissao('arquivo_extensao_alterar');
      $bolAcaoImprimir = false;
      $bolAcaoExcluir = false;
      $bolAcaoDesativar = false;
      $bolCheck = true;
    }else if ($_GET['acao']=='arquivo_extensao_reativar'){
      $bolAcaoReativar = SessaoSEI::getInstance()->verificarPermissao('arquivo_extensao_reativar');
      $bolAcaoConsultar = SessaoSEI::getInstance()->verificarPermissao('arquivo_extensao_consultar');
      $bolAcaoAlterar = false;
      $bolAcaoImprimir = true;
      $bolAcaoExcluir = SessaoSEI::getInstance()->verificarPermissao('arquivo_extensao_excluir');
      $bolAcaoDesativar = false;
    }else{
      $bolAcaoReativar = false;
      $bolAcaoConsultar = SessaoSEI::getInstance()->verificarPermissao('arquivo_extensao_consultar');
      $bolAcaoAlterar = SessaoSEI::getInstance()->verificarPermissao('arquivo_extensao_alterar');
      $bolAcaoImprimir = true;
      $bolAcaoExcluir = SessaoSEI::getInstance()->verificarPermissao('arquivo_extensao_excluir');
      $bolAcaoDesativar = SessaoSEI::getInstance()->verificarPermissao('arquivo_extensao_desativar');
    }

    
    if ($bolAcaoDesativar){
      $bolCheck = true;
      $arrComandos[] = '<button type="button" accesskey="t" id="btnDesativar" value="Desativar" onclick="acaoDesativacaoMultipla();" class="infraButton">Desa<span class="infraTeclaAtalho">t</span>ivar</button>';
      $strLinkDesativar = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=arquivo_extensao_desativar&acao_origem='.$_GET['acao']);
    }

    if ($bolAcaoReativar){
      $bolCheck = true;
      $arrComandos[] = '<button type="button" accesskey="R" id="btnReativar" value="Reativar" onclick="acaoReativacaoMultipla();" class="infraButton"><span class="infraTeclaAtalho">R</span>eativar</button>';
      $strLinkReativar = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=arquivo_extensao_reativar&acao_origem='.$_GET['acao'].'&acao_confirmada=sim');
    }
    

    if ($bolAcaoExcluir){
      $bolCheck = true;
      $arrComandos[] = '<button type="button" accesskey="E" id="btnExcluir" value="Excluir" onclick="acaoExclusaoMultipla();" class="infraButton"><span class="infraTeclaAtalho">E</span>xcluir</button>';
      $strLinkExcluir = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=arquivo_extensao_excluir&acao_origem='.$_GET['acao']);
    }

    $strResultado = '';

    if ($_GET['acao']!='arquivo_extensao_reativar'){
      $strSumarioTabela = 'Tabela de Extens�es de Arquivos.';
      $strCaptionTabela = 'Extens�es de Arquivos';
    }else{
      $strSumarioTabela = 'Tabela de Extens�es de Arquivos Inativas.';
      $strCaptionTabela = 'Extens�es de Arquivos Inativas';
    }

    $strResultado .= '<table width="99%" class="infraTable" summary="'.$strSumarioTabela.'">'."\n";
    $strResultado .= '<caption class="infraCaption">'.PaginaSEI::getInstance()->gerarCaptionTabela($strCaptionTabela,$numRegistros).'</caption>';
    $strResultado .= '<tr>';
    if ($bolCheck) {
      $strResultado .= '<th class="infraTh" width="1%">'.PaginaSEI::getInstance()->getThCheck().'</th>'."\n";
    }
    $strResultado .= '<th class="infraTh" width="10%">'.PaginaSEI::getInstance()->getThOrdenacao($objArquivoExtensaoDTO,'Extens�o','Extensao',$arrObjArquivoExtensaoDTO).'</th>'."\n";
    $strResultado .= '<th class="infraTh">'.PaginaSEI::getInstance()->getThOrdenacao($objArquivoExtensaoDTO,'Descri��o','Descricao',$arrObjArquivoExtensaoDTO).'</th>'."\n";
    $strResultado .= '<th class="infraTh" width="15%">A��es</th>'."\n";
    $strResultado .= '</tr>'."\n";
    $strCssTr='';
    for($i = 0;$i < $numRegistros; $i++){

      $strCssTr = ($strCssTr=='<tr class="infraTrClara">')?'<tr class="infraTrEscura">':'<tr class="infraTrClara">';
      $strResultado .= $strCssTr;

      if ($bolCheck){
        $strResultado .= '<td valign="top">'.PaginaSEI::getInstance()->getTrCheck($i,$arrObjArquivoExtensaoDTO[$i]->getNumIdArquivoExtensao(),$arrObjArquivoExtensaoDTO[$i]->getStrExtensao()).'</td>';
      }
      $strResultado .= '<td align="center">'.$arrObjArquivoExtensaoDTO[$i]->getStrExtensao().'</td>';
      $strResultado .= '<td>'.$arrObjArquivoExtensaoDTO[$i]->getStrDescricao().'</td>';
      $strResultado .= '<td align="center">';

      $strResultado .= PaginaSEI::getInstance()->getAcaoTransportarItem($i,$arrObjArquivoExtensaoDTO[$i]->getNumIdArquivoExtensao());

      if ($bolAcaoConsultar){
        $strResultado .= '<a href="'.PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?acao=arquivo_extensao_consultar&acao_origem='.$_GET['acao'].'&acao_retorno='.$_GET['acao'].'&id_arquivo_extensao='.$arrObjArquivoExtensaoDTO[$i]->getNumIdArquivoExtensao())).'" tabindex="'.PaginaSEI::getInstance()->getProxTabTabela().'"><img src="'.PaginaSEI::getInstance()->getDiretorioImagensGlobal().'/consultar.gif" title="Consultar Extens�o de Arquivo" alt="Consultar Extens�o de Arquivo" class="infraImg" /></a>&nbsp;';
      }

      if ($bolAcaoAlterar){
        $strResultado .= '<a href="'.PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?acao=arquivo_extensao_alterar&acao_origem='.$_GET['acao'].'&acao_retorno='.$_GET['acao'].'&id_arquivo_extensao='.$arrObjArquivoExtensaoDTO[$i]->getNumIdArquivoExtensao())).'" tabindex="'.PaginaSEI::getInstance()->getProxTabTabela().'"><img src="'.PaginaSEI::getInstance()->getDiretorioImagensGlobal().'/alterar.gif" title="Alterar Extens�o de Arquivo" alt="Alterar Extens�o de Arquivo" class="infraImg" /></a>&nbsp;';
      }

      if ($bolAcaoDesativar || $bolAcaoReativar || $bolAcaoExcluir){
        $strId = $arrObjArquivoExtensaoDTO[$i]->getNumIdArquivoExtensao();
        $strDescricao = PaginaSEI::getInstance()->formatarParametrosJavaScript(PaginaSEI::tratarHTML($arrObjArquivoExtensaoDTO[$i]->getStrExtensao()));
      }

      if ($bolAcaoDesativar){
        $strResultado .= '<a href="'.PaginaSEI::getInstance()->montarAncora($strId).'" onclick="acaoDesativar(\''.$strId.'\',\''.$strDescricao.'\');" tabindex="'.PaginaSEI::getInstance()->getProxTabTabela().'"><img src="'.PaginaSEI::getInstance()->getDiretorioImagensGlobal().'/desativar.gif" title="Desativar Extens�o de Arquivo" alt="Desativar Extens�o de Arquivo" class="infraImg" /></a>&nbsp;';
      }

      if ($bolAcaoReativar){
        $strResultado .= '<a href="'.PaginaSEI::getInstance()->montarAncora($strId).'" onclick="acaoReativar(\''.$strId.'\',\''.$strDescricao.'\');" tabindex="'.PaginaSEI::getInstance()->getProxTabTabela().'"><img src="'.PaginaSEI::getInstance()->getDiretorioImagensGlobal().'/reativar.gif" title="Reativar Extens�o de Arquivo" alt="Reativar Extens�o de Arquivo" class="infraImg" /></a>&nbsp;';
      }


      if ($bolAcaoExcluir){
        $strResultado .= '<a href="'.PaginaSEI::getInstance()->montarAncora($strId).'" onclick="acaoExcluir(\''.$strId.'\',\''.$strDescricao.'\');" tabindex="'.PaginaSEI::getInstance()->getProxTabTabela().'"><img src="'.PaginaSEI::getInstance()->getDiretorioImagensGlobal().'/excluir.gif" title="Excluir Extens�o de Arquivo" alt="Excluir Extens�o de Arquivo" class="infraImg" /></a>&nbsp;';
      }

      $strResultado .= '</td></tr>'."\n";
    }
    $strResultado .= '</table>';
  }
  if ($_GET['acao'] == 'md_pet_arquivo_extensao_selecionar'){
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
  if ('<?=$_GET['acao']?>'=='md_pet_arquivo_extensao_selecionar'){
    infraReceberSelecao();
    document.getElementById('btnFecharSelecao').focus();
  }else{
    document.getElementById('btnFechar').focus();
  }
  infraEfeitoTabelas();
}

<? if ($bolAcaoDesativar){ ?>
function acaoDesativar(id,desc){
  if (confirm("Confirma desativa��o da Extens�o de Arquivo \""+desc+"\"?")){
    document.getElementById('hdnInfraItemId').value=id;
    document.getElementById('frmArquivoExtensaoLista').action='<?=$strLinkDesativar?>';
    document.getElementById('frmArquivoExtensaoLista').submit();
  }
}

function acaoDesativacaoMultipla(){
  if (document.getElementById('hdnInfraItensSelecionados').value==''){
    alert('Nenhuma Extens�o de Arquivo selecionada.');
    return;
  }
  if (confirm("Confirma desativa��o das Extens�es de Arquivos selecionadas?")){
    document.getElementById('hdnInfraItemId').value='';
    document.getElementById('frmArquivoExtensaoLista').action='<?=$strLinkDesativar?>';
    document.getElementById('frmArquivoExtensaoLista').submit();
  }
}
<? } ?>

<? if ($bolAcaoReativar){ ?>
function acaoReativar(id,desc){
  if (confirm("Confirma reativa��o da Extens�o de Arquivo \""+desc+"\"?")){
    document.getElementById('hdnInfraItemId').value=id;
    document.getElementById('frmArquivoExtensaoLista').action='<?=$strLinkReativar?>';
    document.getElementById('frmArquivoExtensaoLista').submit();
  }
}

function acaoReativacaoMultipla(){
  if (document.getElementById('hdnInfraItensSelecionados').value==''){
    alert('Nenhuma Extens�o de Arquivo selecionada.');
    return;
  }
  if (confirm("Confirma reativa��o das Extens�es de Arquivos selecionadas?")){
    document.getElementById('hdnInfraItemId').value='';
    document.getElementById('frmArquivoExtensaoLista').action='<?=$strLinkReativar?>';
    document.getElementById('frmArquivoExtensaoLista').submit();
  }
}
<? } ?>

<? if ($bolAcaoExcluir){ ?>
function acaoExcluir(id,desc){
  if (confirm("Confirma exclus�o da Extens�o de Arquivo \""+desc+"\"?")){
    document.getElementById('hdnInfraItemId').value=id;
    document.getElementById('frmArquivoExtensaoLista').action='<?=$strLinkExcluir?>';
    document.getElementById('frmArquivoExtensaoLista').submit();
  }
}

function acaoExclusaoMultipla(){
  if (document.getElementById('hdnInfraItensSelecionados').value==''){
    alert('Nenhuma Extens�o de Arquivo selecionada.');
    return;
  }
  if (confirm("Confirma exclus�o das Extens�es de Arquivos selecionadas?")){
    document.getElementById('hdnInfraItemId').value='';
    document.getElementById('frmArquivoExtensaoLista').action='<?=$strLinkExcluir?>';
    document.getElementById('frmArquivoExtensaoLista').submit();
  }
}
<? } ?>

<?
PaginaSEI::getInstance()->fecharJavaScript();
PaginaSEI::getInstance()->fecharHead();
PaginaSEI::getInstance()->abrirBody($strTitulo,'onload="inicializar();"');
?>
<form id="frmArquivoExtensaoLista" method="post" action="<?=PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?acao='.$_GET['acao'].'&acao_origem='.$_GET['acao']))?>">
  <?
  PaginaSEI::getInstance()->montarBarraComandosSuperior($arrComandos);
  PaginaSEI::getInstance()->montarAreaTabela($strResultado,$numRegistros);
  PaginaSEI::getInstance()->montarBarraComandosInferior($arrComandos);
  ?>
</form>
<?
PaginaSEI::getInstance()->fecharBody();
PaginaSEI::getInstance()->fecharHtml();
die();
?>