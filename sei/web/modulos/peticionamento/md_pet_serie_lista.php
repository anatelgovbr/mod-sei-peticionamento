<?
/**
* ANATEL
*
* 12/05/2016 - criada por jaqueline.mendes@cast.com.br - CAST
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

  PaginaSEI::getInstance()->prepararSelecao('md_pet_serie_selecionar');

  SessaoSEI::getInstance()->validarPermissao($_GET['acao']);

	if (isset($_GET['id_grupo_serie'])){
	  PaginaSEI::getInstance()->salvarCampo('selGrupoSerie',$_GET['id_grupo_serie']);
	}else{
	  PaginaSEI::getInstance()->salvarCamposPost(array('selGrupoSerie'));
	}
  
  PaginaSEI::getInstance()->salvarCamposPost(array('selModeloPesquisa', 'selModeloEdocPesquisa','txtNomeSeriePesquisa'));

  switch($_GET['acao']){
    case 'serie_peticionamento_excluir':
      try{
        $arrStrIds = PaginaSEI::getInstance()->getArrStrItensSelecionados();
        $arrObjSerieDTO = array();
        for ($i=0;$i<count($arrStrIds);$i++){
          $objSerieDTO = new SerieDTO();
          $objSerieDTO->setNumIdSerie($arrStrIds[$i]);
          $arrObjSerieDTO[] = $objSerieDTO;
        }
        $objSerieRN = new SerieRN();
        $objSerieRN->excluirRN0645($arrObjSerieDTO);
        PaginaSEI::getInstance()->setStrMensagem('Opera��o realizada com sucesso.');
      }catch(Exception $e){
        PaginaSEI::getInstance()->processarExcecao($e);
      } 
      header('Location: '.SessaoSEI::getInstance()->assinarLink('controlador.php?acao='.$_GET['acao_origem'].'&acao_origem='.$_GET['acao']));
      die;


    case 'serie_peticionamento_desativar':
      try{
        $arrStrIds = PaginaSEI::getInstance()->getArrStrItensSelecionados();
        $arrObjSerieDTO = array();
        for ($i=0;$i<count($arrStrIds);$i++){
          $objSerieDTO = new SerieDTO();
          $objSerieDTO->setNumIdSerie($arrStrIds[$i]);
          $arrObjSerieDTO[] = $objSerieDTO;
        }
        $objSerieRN = new SerieRN();
        $objSerieRN->desativarRN0648($arrObjSerieDTO);
        PaginaSEI::getInstance()->setStrMensagem('Opera��o realizada com sucesso.');
      }catch(Exception $e){
        PaginaSEI::getInstance()->processarExcecao($e);
      } 
      header('Location: '.SessaoSEI::getInstance()->assinarLink('controlador.php?acao='.$_GET['acao_origem'].'&acao_origem='.$_GET['acao']));
      die;

    case 'serie_peticionamento_reativar':
      $strTitulo = 'Reativar Tipos de Documento';
      if ($_GET['acao_confirmada']=='sim'){
        try{
          $arrStrIds = PaginaSEI::getInstance()->getArrStrItensSelecionados();
          $arrObjSerieDTO = array();
          for ($i=0;$i<count($arrStrIds);$i++){
            $objSerieDTO = new SerieDTO();
            $objSerieDTO->setNumIdSerie($arrStrIds[$i]);
            $arrObjSerieDTO[] = $objSerieDTO;
          }
          $objSerieRN = new SerieRN();
          $objSerieRN->reativarRN0649($arrObjSerieDTO);
          PaginaSEI::getInstance()->setStrMensagem('Opera��o realizada com sucesso.');
        }catch(Exception $e){
          PaginaSEI::getInstance()->processarExcecao($e);
        } 
        header('Location: '.SessaoSEI::getInstance()->assinarLink('controlador.php?acao='.$_GET['acao_origem'].'&acao_origem='.$_GET['acao']));
        die;
      } 
      break;


    case 'md_pet_serie_selecionar':
      $strTitulo = PaginaSEI::getInstance()->getTituloSelecao('Selecionar Tipo de Documento','Selecionar Tipos de Documento');

      //Se cadastrou alguem
      if ($_GET['acao_origem']=='serie_cadastrar'){
        if (isset($_GET['id_serie'])){
          PaginaSEI::getInstance()->adicionarSelecionado($_GET['id_serie']);
        }
      }
      break;

    case 'serie_peticionamento_listar':
      $strTitulo = 'Tipos de Documento';
      break;

    default:
      throw new InfraException("A��o '".$_GET['acao']."' n�o reconhecida.");
  }

  $arrComandos = array();
  
  $arrComandos[] = '<button type="submit" accesskey="P" id="sbmPesquisar" value="Pesquisar" class="infraButton"><span class="infraTeclaAtalho">P</span>esquisar</button>';

  if ($_GET['acao'] == 'md_pet_serie_selecionar'){
    $arrComandos[] = '<button type="button" accesskey="T" id="btnTransportarSelecao" value="Transportar" onclick="infraTransportarSelecao();" class="infraButton"><span class="infraTeclaAtalho">T</span>ransportar</button>';
  }
  
  $objSerieRN = new SerieRN();  
  $objSerieDTO = new SerieDTO(true);
  $objSerieDTO->retTodos();
  $objSerieDTO->retStrNome();
  $objSerieDTO->retStrNomeGrupoSerie();
  
  $filtro = $_GET['filtro'];
  
  if($filtro == '1'){
  	$tipoDoc = $_GET['tipoDoc'];
  	
  	//Todos DOCS
  	$aplicalidade = $tipoDoc == MdPetTipoProcessoRN::$DOC_GERADO ? SerieRN::$TA_INTERNO : SerieRN::$TA_EXTERNO;
  	
  	//ignorar o chkboxes "SinInterno", considerar apenas o campo Aplicabilidade em si
  	$objSerieDTO->adicionarCriterio(array('StaAplicabilidade'),
  				array(InfraDTO::$OPER_IN),
  			    //alteracoes SEIv3
  			    array(array(SerieRN::$TA_INTERNO_EXTERNO, $aplicalidade)));  	
  }

  if($filtro == '2'){
  //Add filtro para Tipos de Documentos para Intima��o Eletr�nica
    $objSerieDTO->adicionarCriterio(array('StaAplicabilidade'),
        array(InfraDTO::$OPER_IN),
        array(array(SerieRN::$TA_INTERNO, SerieRN::$TA_INTERNO_EXTERNO)));
  }

    if($filtro == '3'){
        //Add filtro para Tipos de Documentos para Intima��o Eletr�nica
        $objSerieDTO->adicionarCriterio(array('StaAplicabilidade'),
            array(InfraDTO::$OPER_IN),
            array(array(SerieRN::$TA_EXTERNO, SerieRN::$TA_INTERNO_EXTERNO)));
    }

  $numIdGrupoSerie = PaginaSEI::getInstance()->recuperarCampo('selGrupoSerie');
  if ($numIdGrupoSerie!==''){
    $objSerieDTO->setNumIdGrupoSerie($numIdGrupoSerie);
  }

  $strNomeSeriePesquisa = PaginaSEI::getInstance()->recuperarCampo('txtNomeSeriePesquisa');
  if (trim($strNomeSeriePesquisa) != ''){
    $objSerieDTO->setStrNome('%'.trim($strNomeSeriePesquisa.'%'),InfraDTO::$OPER_LIKE);
  }

  $numIdModelo = PaginaSEI::getInstance()->recuperarCampo('selModeloPesquisa','null');
  if ($numIdModelo!=='null'){
    $objSerieDTO->setNumIdModelo($numIdModelo);
  }
  
  $numIdModeloEdoc = PaginaSEI::getInstance()->recuperarCampo('selModeloEdocPesquisa','null');
  if ($numIdModeloEdoc!=='null'){
    $objSerieDTO->setNumIdModeloEdoc($numIdModeloEdoc);
  }
  
  if ($_GET['acao'] == 'serie_peticionamento_reativar'){
    //Lista somente inativos
    $objSerieDTO->setBolExclusaoLogica(false);
    $objSerieDTO->setStrSinAtivo('N');
  }

  PaginaSEI::getInstance()->prepararOrdenacao($objSerieDTO, 'Nome', InfraDTO::$TIPO_ORDENACAO_ASC);
  PaginaSEI::getInstance()->prepararPaginacao($objSerieDTO);

  $arrObjSerieDTO = $objSerieRN->listarRN0646($objSerieDTO);
  
  PaginaSEI::getInstance()->processarPaginacao($objSerieDTO);
  $numRegistros = count($arrObjSerieDTO);

  if ($numRegistros > 0){

    $bolCheck = false;

    if ($_GET['acao']=='md_pet_serie_selecionar'){
      $bolAcaoReativar = false;
      $bolAcaoConsultar = SessaoSEI::getInstance()->verificarPermissao('serie_peticionamento_desativar');
      $bolAcaoAlterar = SessaoSEI::getInstance()->verificarPermissao('serie_peticionamento_alterar');
      $bolAcaoImprimir = false;
      $bolAcaoExcluir = false;
      $bolAcaoDesativar = false;
      $bolCheck = true;
    }else if ($_GET['acao']=='serie_peticionamento_reativar'){
      $bolAcaoReativar = SessaoSEI::getInstance()->verificarPermissao('serie_peticionamento_reativar');
      $bolAcaoConsultar = false;
      $bolAcaoAlterar = false;
      $bolAcaoImprimir = true;
      $bolAcaoExcluir = SessaoSEI::getInstance()->verificarPermissao('serie_peticionamento_excluir');
      $bolAcaoDesativar = false;
    }else{
      $bolAcaoReativar = false;
      $bolAcaoConsultar = SessaoSEI::getInstance()->verificarPermissao('serie_peticionamento_desativar');
      $bolAcaoAlterar = SessaoSEI::getInstance()->verificarPermissao('serie_peticionamento_alterar');
      $bolAcaoImprimir = true;
      $bolAcaoExcluir = SessaoSEI::getInstance()->verificarPermissao('serie_peticionamento_excluir');
      $bolAcaoDesativar = SessaoSEI::getInstance()->verificarPermissao('serie_peticionamento_desativar');
    }

    
    if ($bolAcaoDesativar){
      $bolCheck = true;
      $arrComandos[] = '<button type="button" accesskey="t" id="btnDesativar" value="Desativar" onclick="acaoDesativacaoMultipla();" class="infraButton">Desa<span class="infraTeclaAtalho">t</span>ivar</button>';
      $strLinkDesativar = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=serie_peticionamento_desativar&acao_origem='.$_GET['acao']);
    }

    if ($bolAcaoReativar){
      $bolCheck = true;
      $arrComandos[] = '<button type="button" accesskey="R" id="btnReativar" value="Reativar" onclick="acaoReativacaoMultipla();" class="infraButton"><span class="infraTeclaAtalho">R</span>eativar</button>';
      $strLinkReativar = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=serie_peticionamento_reativar&acao_origem='.$_GET['acao'].'&acao_confirmada=sim');
    }
    

    if ($bolAcaoExcluir){
      $bolCheck = true;
      $arrComandos[] = '<button type="button" accesskey="E" id="btnExcluir" value="Excluir" onclick="acaoExclusaoMultipla();" class="infraButton"><span class="infraTeclaAtalho">E</span>xcluir</button>';
      $strLinkExcluir = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=serie_peticionamento_excluir&acao_origem='.$_GET['acao']);
    }

    if ($bolAcaoImprimir){
      $bolCheck = true;
      $arrComandos[] = '<button type="button" accesskey="I" id="btnImprimir" value="Imprimir" onclick="infraImprimirTabela();" class="infraButton"><span class="infraTeclaAtalho">I</span>mprimir</button>';

    }

    $strResultado = '';

    if ($_GET['acao']!='serie_peticionamento_reativar'){
      $strSumarioTabela = 'Tabela de Tipos de Documento.';
      $strCaptionTabela = 'Tipos de Documento';
    }else{
      $strSumarioTabela = 'Tabela de Tipos de Documento Inativos.';
      $strCaptionTabela = 'Tipos de Documento Inativos';
    }

    $strResultado .= '<table width="99%" class="infraTable" summary="'.$strSumarioTabela.'">'."\n";
    $strResultado .= '<caption class="infraCaption">'.PaginaSEI::getInstance()->gerarCaptionTabela($strCaptionTabela,$numRegistros).'</caption>';
    $strResultado .= '<tr>';
    if ($bolCheck) {
      $strResultado .= '<th class="infraTh" width="1%">'.PaginaSEI::getInstance()->getThCheck().'</th>'."\n";
    }
    $strResultado .= '<th class="infraTh" width="10%">ID</th>'."\n";
    $strResultado .= '<th class="infraTh">'.PaginaSEI::getInstance()->getThOrdenacao($objSerieDTO,'Nome','Nome',$arrObjSerieDTO).'</th>'."\n";
    $strResultado .= '<th class="infraTh">'.PaginaSEI::getInstance()->getThOrdenacao($objSerieDTO,'Grupo','NomeGrupoSerie',$arrObjSerieDTO).'</th>'."\n";
    $strResultado .= '<th class="infraTh" width="15%">A��es</th>'."\n";
    $strResultado .= '</tr>'."\n";
    $strCssTr='';
    for($i = 0;$i < $numRegistros; $i++){

      $strCssTr = ($strCssTr=='<tr class="infraTrClara">')?'<tr class="infraTrEscura">':'<tr class="infraTrClara">';
      $strResultado .= $strCssTr;

      if ($bolCheck){
        $strResultado .= '<td valign="top">'.PaginaSEI::getInstance()->getTrCheck($i,$arrObjSerieDTO[$i]->getNumIdSerie(),$arrObjSerieDTO[$i]->getStrNome()).'</td>';
      }
      $strResultado .= '<td align="center">'.$arrObjSerieDTO[$i]->getNumIdSerie().'</td>';
      $strResultado .= '<td>'.PaginaSEI::getInstance()->formatarXHTML($arrObjSerieDTO[$i]->getStrNome()).'</td>';
      $strResultado .= '<td>'.$arrObjSerieDTO[$i]->getStrNomeGrupoSerie().'</td>';
      $strResultado .= '<td align="center">';
      
      $strResultado .= PaginaSEI::getInstance()->getAcaoTransportarItem($i,$arrObjSerieDTO[$i]->getNumIdSerie());
      
      if ($bolAcaoConsultar){
        $strResultado .= '<a href="'.PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?acao=serie_peticionamento_desativar&acao_origem='.$_GET['acao'].'&acao_retorno='.$_GET['acao'].'&id_serie='.$arrObjSerieDTO[$i]->getNumIdSerie())).'" tabindex="'.PaginaSEI::getInstance()->getProxTabTabela().'"><img src="imagens/consultar.gif" title="Consultar Tipo de Documento" alt="Consultar Tipo de Documento" class="infraImg" /></a>&nbsp;';
      }

      if ($bolAcaoAlterar){
        $strResultado .= '<a href="'.PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?acao=serie_peticionamento_alterar&acao_origem='.$_GET['acao'].'&acao_retorno='.$_GET['acao'].'&id_serie='.$arrObjSerieDTO[$i]->getNumIdSerie())).'" tabindex="'.PaginaSEI::getInstance()->getProxTabTabela().'"><img src="imagens/alterar.gif" title="Alterar Tipo de Documento" alt="Alterar Tipo de Documento" class="infraImg" /></a>&nbsp;';
      }

      if ($bolAcaoDesativar || $bolAcaoReativar || $bolAcaoExcluir){
        $strId = $arrObjSerieDTO[$i]->getNumIdSerie();
        $strDescricao = PaginaSEI::getInstance()->formatarParametrosJavaScript(PaginaSEI::tratarHTML($arrObjSerieDTO[$i]->getStrNome()));
      }

      if ($bolAcaoDesativar){
        $strResultado .= '<a href="#ID-'.$strId.'" onclick="acaoDesativar(\''.$strId.'\',\''.$strDescricao.'\');" tabindex="'.PaginaSEI::getInstance()->getProxTabTabela().'"><img src="imagens/desativar.gif" title="Desativar Tipo de Documento" alt="Desativar Tipo de Documento" class="infraImg" /></a>&nbsp;';
      }

      if ($bolAcaoReativar){
        $strResultado .= '<a href="#ID-'.$strId.'" onclick="acaoReativar(\''.$strId.'\',\''.$strDescricao.'\');" tabindex="'.PaginaSEI::getInstance()->getProxTabTabela().'"><img src="imagens/reativar.gif" title="Reativar Tipo de Documento" alt="Reativar Tipo de Documento" class="infraImg" /></a>&nbsp;';
      }

      if ($bolAcaoExcluir){
        $strResultado .= '<a href="#ID-'.$strId.'" onclick="acaoExcluir(\''.$strId.'\',\''.$strDescricao.'\');" tabindex="'.PaginaSEI::getInstance()->getProxTabTabela().'"><img src="imagens/excluir.gif" title="Excluir Tipo de Documento" alt="Excluir Tipo de Documento" class="infraImg" /></a>&nbsp;';
      }

      $strResultado .= '</td></tr>'."\n";
    }
    $strResultado .= '</table>';
  }
  if ($_GET['acao'] == 'md_pet_serie_selecionar'){
    $arrComandos[] = '<button type="button" accesskey="C" id="btnFecharSelecao" value="Fechar" onclick="window.close();" class="infraButton">Fe<span class="infraTeclaAtalho">c</span>har</button>';
  }else{
    $arrComandos[] = '<button type="button" accesskey="C" id="btnFechar" value="Fechar" onclick="location.href=\''.PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?acao='.PaginaSEI::getInstance()->getAcaoRetorno().'&acao_origem='.$_GET['acao'].PaginaSEI::getInstance()->montarAncora($numIdGrupoSerie))).'\'" class="infraButton">Fe<span class="infraTeclaAtalho">c</span>har</button>';
  }

  $strItensSelGrupoSerie = GrupoSerieINT::montarSelectNomeRI0801('','Todos',$numIdGrupoSerie);
  $strItensSelModelo = ModeloINT::montarSelectNome('null','Todos',$numIdModelo);
  
  
  $strDisplaySelModeloEdoc = '';
  
  //alteracoes SEIv3
  $strDisplaySelModeloEdoc = 'display:none;';
  
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
#lblGrupoSerie {position:absolute;left:0%;top:0%;width:20%;}
#selGrupoSerie {position:absolute;left:0%;top:40%;width:20%;}

#lblNomeSeriePesquisa {position:absolute;left:0%;top:0%;width:20%;}
#txtNomeSeriePesquisa {position:absolute;left:0%;top:40%;width:20%;}

#lblModeloPesquisa {position:absolute;left:50%;top:0%;width:24%;}
#selModeloPesquisa {position:absolute;left:50%;top:40%;width:24%;}

#lblModeloEdocPesquisa {position:absolute;left:75%;top:0%;width:24%;<?=$strDisplaySelModeloEdoc?>}
#selModeloEdocPesquisa {position:absolute;left:75%;top:40%;width:24%;<?=$strDisplaySelModeloEdoc?>}

<?
PaginaSEI::getInstance()->fecharStyle();
PaginaSEI::getInstance()->montarJavaScript();
PaginaSEI::getInstance()->abrirJavaScript();
?>

function inicializar(){
  if ('<?=$_GET['acao']?>'=='md_pet_serie_selecionar'){
    infraReceberSelecao();
    document.getElementById('btnFecharSelecao').focus();
  }else{
    document.getElementById('btnFechar').focus();
  }
  
  infraEfeitoTabelas();
}

<? if ($bolAcaoDesativar){ ?>
function acaoDesativar(id,desc){
  if (confirm("Confirma desativa��o do Tipo de Documento \""+desc+"\"?")){
    document.getElementById('hdnInfraItemId').value=id;
    document.getElementById('frmSerieLista').action='<?=$strLinkDesativar?>';
    document.getElementById('frmSerieLista').submit();
  }
}

function acaoDesativacaoMultipla(){
  if (document.getElementById('hdnInfraItensSelecionados').value==''){
    alert('Nenhum Tipo de Documento selecionado.');
    return;
  }
  if (confirm("Confirma desativa��o dos Tipos de Documento selecionados?")){
    document.getElementById('hdnInfraItemId').value='';
    document.getElementById('frmSerieLista').action='<?=$strLinkDesativar?>';
    document.getElementById('frmSerieLista').submit();
  }
}
<? } ?>

<? if ($bolAcaoReativar){ ?>
function acaoReativar(id,desc){
  if (confirm("Confirma reativa��o do Tipo de Documento \""+desc+"\"?")){
    document.getElementById('hdnInfraItemId').value=id;
    document.getElementById('frmSerieLista').action='<?=$strLinkReativar?>';
    document.getElementById('frmSerieLista').submit();
  }
}

function acaoReativacaoMultipla(){
  if (document.getElementById('hdnInfraItensSelecionados').value==''){
    alert('Nenhum Tipo de Documento selecionado.');
    return;
  }
  if (confirm("Confirma reativa��o dos Tipos de Documento selecionados?")){
    document.getElementById('hdnInfraItemId').value='';
    document.getElementById('frmSerieLista').action='<?=$strLinkReativar?>';
    document.getElementById('frmSerieLista').submit();
  }
}
<? } ?>

<? if ($bolAcaoExcluir){ ?>
function acaoExcluir(id,desc){
  if (confirm("Confirma exclus�o do Tipo de Documento \""+desc+"\"?")){
    document.getElementById('hdnInfraItemId').value=id;
    document.getElementById('frmSerieLista').action='<?=$strLinkExcluir?>';
    document.getElementById('frmSerieLista').submit();
  }
}

function acaoExclusaoMultipla(){
  if (document.getElementById('hdnInfraItensSelecionados').value==''){
    alert('Nenhum Tipo de Documento selecionado.');
    return;
  }
  if (confirm("Confirma exclus�o dos Tipos de Documento selecionados?")){
    document.getElementById('hdnInfraItemId').value='';
    document.getElementById('frmSerieLista').action='<?=$strLinkExcluir?>';
    document.getElementById('frmSerieLista').submit();
  }
}
<? } ?>

<?
PaginaSEI::getInstance()->fecharJavaScript();
PaginaSEI::getInstance()->fecharHead();
PaginaSEI::getInstance()->abrirBody($strTitulo,'onload="inicializar();"');
?>
<form id="frmSerieLista" method="post" action="<?=PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?acao='.$_GET['acao'].'&filtro='. $_GET['filtro'].'&tipoDoc='.$_GET['tipoDoc'].'&acao_origem='.$_GET['acao']))?>">
  <?
  PaginaSEI::getInstance()->montarBarraComandosSuperior($arrComandos);
  PaginaSEI::getInstance()->abrirAreaDados('4.5em');
  ?>
  <label id="lblGrupoSerie" for="selGrupoSerie" accesskey="" class="infraLabelOpcional" style="display:none!important">Grupo:</label>
  <select id="selGrupoSerie" name="selGrupoSerie" onchange="this.form.submit();" class="infraSelect" style="display:none!important" tabindex="<?=PaginaSEI::getInstance()->getProxTabDados()?>" >
  <?=$strItensSelGrupoSerie?>
  </select>
 
  <label id="lblNomeSeriePesquisa" for="txtNomeSeriePesquisa" accesskey="" class="infraLabelOpcional">Nome:</label>
  <input type="text" id="txtNomeSeriePesquisa" name="txtNomeSeriePesquisa" value="<?=PaginaSEI::tratarHTML($strNomeSeriePesquisa)?>" class="infraText" tabindex="<?=PaginaSEI::getInstance()->getProxTabDados()?>" />

  <label id="lblModeloPesquisa" for="selModeloPesquisa" accesskey="" class="infraLabelOpcional" style="display:none!important">Modelo:</label>
  <select id="selModeloPesquisa" name="selModeloPesquisa" onchange="this.form.submit();" class="infraSelect" style="display:none!important" tabindex="<?=PaginaSEI::getInstance()->getProxTabDados()?>" >
  <?=$strItensSelModelo?>
  </select>

  <label id="lblModeloEdocPesquisa" for="selModeloEdocPesquisa" accesskey="" class="infraLabelOpcional">Modelo e-Doc:</label>
  <select id="selModeloEdocPesquisa" name="selModeloEdocPesquisa" onchange="this.form.submit();" class="infraSelect" tabindex="<?=PaginaSEI::getInstance()->getProxTabDados()?>" >
  <?=$strItensSelModeloEdoc?>
  </select>
   
  <?
  PaginaSEI::getInstance()->fecharAreaDados();
  PaginaSEI::getInstance()->montarAreaTabela($strResultado,$numRegistros);
  PaginaSEI::getInstance()->montarAreaDebug();
  PaginaSEI::getInstance()->montarBarraComandosInferior($arrComandos);
  ?>
</form>
<?
PaginaSEI::getInstance()->fecharBody();
PaginaSEI::getInstance()->fecharHtml();
?>