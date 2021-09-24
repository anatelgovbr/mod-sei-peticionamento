<?
/**
* TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
*
* 08/02/2012 - criado por bcu
*
* Versão do Gerador de Código: 1.32.1
*
* Versão no CVS: $Id: md_pet_arquivo_extensao_lista.php 8743 2014-04-23 17:40:44Z mga $
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
        PaginaSEI::getInstance()->adicionarMensagem('Operação realizada com sucesso.');
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
        PaginaSEI::getInstance()->adicionarMensagem('Operação realizada com sucesso.');
      }catch(Exception $e){
        PaginaSEI::getInstance()->processarExcecao($e);
      } 
      header('Location: '.SessaoSEI::getInstance()->assinarLink('controlador.php?acao='.$_GET['acao_origem'].'&acao_origem='.$_GET['acao']));
      die;

    case 'arquivo_extensao_reativar':
      $strTitulo = 'Reativar Extensões de Arquivos';
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
          PaginaSEI::getInstance()->adicionarMensagem('Operação realizada com sucesso.');
        }catch(Exception $e){
          PaginaSEI::getInstance()->processarExcecao($e);
        } 
        header('Location: '.SessaoSEI::getInstance()->assinarLink('controlador.php?acao='.$_GET['acao_origem'].'&acao_origem='.$_GET['acao']));
        die;
      } 
      break;


    case 'md_pet_arquivo_extensao_selecionar':
      $strTitulo = PaginaSEI::getInstance()->getTituloSelecao('Selecionar Extensão de Arquivo','Selecionar Extensões de Arquivos');

      //Se cadastrou alguem
      if ($_GET['acao_origem']=='arquivo_extensao_cadastrar'){
        if (isset($_GET['id_arquivo_extensao'])){
          PaginaSEI::getInstance()->adicionarSelecionado($_GET['id_arquivo_extensao']);
        }
      }
      break;

    case 'arquivo_extensao_listar':
      $strTitulo = 'Extensões de Arquivos Permitidas';
      break;

    default:
      throw new InfraException("Ação '".$_GET['acao']."' não reconhecida.");
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
      $strSumarioTabela = 'Tabela de Extensões de Arquivos.';
      $strCaptionTabela = 'Extensões de Arquivos';
    }else{
      $strSumarioTabela = 'Tabela de Extensões de Arquivos Inativas.';
      $strCaptionTabela = 'Extensões de Arquivos Inativas';
    }

    $strResultado .= '<div class="row">';
    $strResultado .= '<div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">';
    $strResultado .= '<table class="infraTable table" summary="'.$strSumarioTabela.'">'."\n";
    $strResultado .= '<caption class="infraCaption">'.PaginaSEI::getInstance()->gerarCaptionTabela($strCaptionTabela,$numRegistros).'</caption>';
    $strResultado .= '<thead>';
    $strResultado .= '<tr>';
    if ($bolCheck) {
      $strResultado .= '<th scope="col" class="infraTh" width="1%">'.PaginaSEI::getInstance()->getThCheck().'</th>'."\n";
    }
    $strResultado .= '<th scope="col" class="infraTh" width="10%">'.PaginaSEI::getInstance()->getThOrdenacao($objArquivoExtensaoDTO,'Extensão','Extensao',$arrObjArquivoExtensaoDTO).'</th>'."\n";
    $strResultado .= '<th scope="col" class="infraTh">'.PaginaSEI::getInstance()->getThOrdenacao($objArquivoExtensaoDTO,'Descrição','Descricao',$arrObjArquivoExtensaoDTO).'</th>'."\n";
    $strResultado .= '<th scope="col" class="infraTh" width="20%">Ações</th>'."\n";
    $strResultado .= '</tr>'."\n";
    $strResultado .= '</thead>'."\n";
    $strResultado .= '<tbody>'."\n";
    $strCssTr='';
    for($i = 0;$i < $numRegistros; $i++){

      $strCssTr = ($strCssTr=='<tr class="infraTrClara">')?'<tr class="infraTrEscura">':'<tr class="infraTrClara">';
      $strResultado .= $strCssTr;

      if ($bolCheck){
        $strResultado .= '<th scope="row" valign="top">'.PaginaSEI::getInstance()->getTrCheck($i,$arrObjArquivoExtensaoDTO[$i]->getNumIdArquivoExtensao(),$arrObjArquivoExtensaoDTO[$i]->getStrExtensao()).'</th>';
      }
      $strResultado .= '<td align="center">'.$arrObjArquivoExtensaoDTO[$i]->getStrExtensao().'</td>';
      $strResultado .= '<td>'.$arrObjArquivoExtensaoDTO[$i]->getStrDescricao().'</td>';
      $strResultado .= '<td align="center">';

      $strResultado .= PaginaSEI::getInstance()->getAcaoTransportarItem($i,$arrObjArquivoExtensaoDTO[$i]->getNumIdArquivoExtensao());

      if ($bolAcaoConsultar){
        $strResultado .= '<a href="'.PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?acao=arquivo_extensao_consultar&acao_origem='.$_GET['acao'].'&acao_retorno='.$_GET['acao'].'&id_arquivo_extensao='.$arrObjArquivoExtensaoDTO[$i]->getNumIdArquivoExtensao())).'" tabindex="'.PaginaSEI::getInstance()->getProxTabTabela().'"><img src="'.PaginaSEI::getInstance()->getDiretorioSvgGlobal().'/consultar.svg" title="Consultar Extensão de Arquivo" alt="Consultar Extensão de Arquivo" class="infraImg" /></a>&nbsp;';
      }

      if ($bolAcaoAlterar){
        $strResultado .= '<a href="'.PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?acao=arquivo_extensao_alterar&acao_origem='.$_GET['acao'].'&acao_retorno='.$_GET['acao'].'&id_arquivo_extensao='.$arrObjArquivoExtensaoDTO[$i]->getNumIdArquivoExtensao())).'" tabindex="'.PaginaSEI::getInstance()->getProxTabTabela().'"><img src="'.PaginaSEI::getInstance()->getDiretorioSvgGlobal().'/alterar.svg" title="Alterar Extensão de Arquivo" alt="Alterar Extensão de Arquivo" class="infraImg" /></a>&nbsp;';
      }

      if ($bolAcaoDesativar || $bolAcaoReativar || $bolAcaoExcluir){
        $strId = $arrObjArquivoExtensaoDTO[$i]->getNumIdArquivoExtensao();
        $strDescricao = PaginaSEI::getInstance()->formatarParametrosJavaScript(PaginaSEI::tratarHTML($arrObjArquivoExtensaoDTO[$i]->getStrExtensao()));
      }

      if ($bolAcaoDesativar){
        $strResultado .= '<a href="'.PaginaSEI::getInstance()->montarAncora($strId).'" onclick="acaoDesativar(\''.$strId.'\',\''.$strDescricao.'\');" tabindex="'.PaginaSEI::getInstance()->getProxTabTabela().'"><img src="'.PaginaSEI::getInstance()->getDiretorioSvgGlobal().'/desativar.svg" title="Desativar Extensão de Arquivo" alt="Desativar Extensão de Arquivo" class="infraImg" /></a>&nbsp;';
      }

      if ($bolAcaoReativar){
        $strResultado .= '<a href="'.PaginaSEI::getInstance()->montarAncora($strId).'" onclick="acaoReativar(\''.$strId.'\',\''.$strDescricao.'\');" tabindex="'.PaginaSEI::getInstance()->getProxTabTabela().'"><img src="'.PaginaSEI::getInstance()->getDiretorioSvgGlobal().'/reativar.svg" title="Reativar Extensão de Arquivo" alt="Reativar Extensão de Arquivo" class="infraImg" /></a>&nbsp;';
      }


      if ($bolAcaoExcluir){
        $strResultado .= '<a href="'.PaginaSEI::getInstance()->montarAncora($strId).'" onclick="acaoExcluir(\''.$strId.'\',\''.$strDescricao.'\');" tabindex="'.PaginaSEI::getInstance()->getProxTabTabela().'"><img src="'.PaginaSEI::getInstance()->getDiretorioSvgGlobal().'/excluir.svg" title="Excluir Extensão de Arquivo" alt="Excluir Extensão de Arquivo" class="infraImg" /></a>&nbsp;';
      }

      $strResultado .= '</td></tr>'."\n";
    }
    $strResultado .= '</tbody>';
    $strResultado .= '</table>';
    $strResultado .= '</div>';
    $strResultado .= '</div>';
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
PaginaSEI::getInstance()->montarJavaScript();
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
require_once 'md_pet_arquivo_extensao_lista_js.php';
die();
?>