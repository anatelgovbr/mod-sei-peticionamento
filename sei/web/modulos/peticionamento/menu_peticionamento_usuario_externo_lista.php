<?
/**
* ANATEL
*
* 15/06/2016 - criado por marcelo.bezerra - CAST
*
*/

try {
	require_once dirname(__FILE__).'/../../SEI.php';

	session_start();

	SessaoSEI::getInstance()->validarLink();

	PaginaSEI::getInstance()->prepararSelecao('menu_peticionamento_usuario_externo_selecionar');

	SessaoSEI::getInstance()->validarPermissao($_GET['acao']);

	switch($_GET['acao']){
		case 'menu_peticionamento_usuario_externo_excluir':
			
			try{
				$arrStrIds = PaginaSEI::getInstance()->getArrStrItensSelecionados();
				$arrObjMenuPeticionamentoUsuarioExternoDTO = array();
				
				for ($i=0;$i<count($arrStrIds);$i++){
					$objMenuPeticionamentoUsuarioExternoDTO = new MenuPeticionamentoUsuarioExternoDTO();
					$objMenuPeticionamentoUsuarioExternoDTO->setNumIdMenuPeticionamentoUsuarioExterno($arrStrIds[$i]);
					$arrObjMenuPeticionamentoUsuarioExternoDTO[] = $objMenuPeticionamentoUsuarioExternoDTO;
				}

				$objMenuPeticionamentoUsuarioExternoRN = new MenuPeticionamentoUsuarioExternoRN();
				$objMenuPeticionamentoUsuarioExternoRN->excluir($arrObjMenuPeticionamentoUsuarioExternoDTO);
				PaginaSEI::getInstance()->adicionarMensagem('Operação realizada com sucesso.');

			}catch(Exception $e){
				PaginaSEI::getInstance()->processarExcecao($e);
			}
			header('Location: '.SessaoSEI::getInstance()->assinarLink('controlador.php?id_menu_peticionamento_usuario_externo='. $_GET['id_menu_peticionamento_usuario_externo'] .'&acao='.$_GET['acao_origem'].'&acao_origem='.$_GET['acao']));
			die;

		case 'menu_peticionamento_usuario_externo_desativar':
			try{
				$arrStrIds = PaginaSEI::getInstance()->getArrStrItensSelecionados();
				$arrObjMenuPeticionamentoUsuarioExternoDTO = array();
				for ($i=0;$i<count($arrStrIds);$i++){
					$objMenuPeticionamentoUsuarioExternoDTO = new MenuPeticionamentoUsuarioExternoDTO();
					$objMenuPeticionamentoUsuarioExternoDTO->setNumIdMenuPeticionamentoUsuarioExterno($arrStrIds[$i]);
					//$objMenuPeticionamentoUsuarioExternoDTO->setNumIdMenuPeticionamentoUsuarioExterno($_GET['id_menu_peticionamento_usuario_externo']);
					$arrObjMenuPeticionamentoUsuarioExternoDTO[] = $objMenuPeticionamentoUsuarioExternoDTO;
				}
				$objMenuPeticionamentoUsuarioExternoRN = new MenuPeticionamentoUsuarioExternoRN();
				$objMenuPeticionamentoUsuarioExternoRN->desativar($arrObjMenuPeticionamentoUsuarioExternoDTO);
			}catch(Exception $e){
				PaginaSEI::getInstance()->processarExcecao($e);
			}
			header('Location: '.SessaoSEI::getInstance()->assinarLink('controlador.php?id_menu_peticionamento_usuario_externo='. $_GET['id_menu_peticionamento_usuario_externo'] .'&acao='.$_GET['acao_origem'].'&acao_origem='.$_GET['acao']));
			die;

		case 'menu_peticionamento_usuario_externo_reativar':
			 
			$strTitulo = 'Reativar Menus';

			if ($_GET['acao_confirmada']=='sim'){

				try{
					$arrStrIds = PaginaSEI::getInstance()->getArrStrItensSelecionados();
					$arrObjMenuPeticionamentoUsuarioExternoDTO = array();
					for ($i=0;$i<count($arrStrIds);$i++){
						$objMenuPeticionamentoUsuarioExternoDTO = new MenuPeticionamentoUsuarioExternoDTO();
						$objMenuPeticionamentoUsuarioExternoDTO->setNumIdMenuPeticionamentoUsuarioExterno($arrStrIds[$i]);
						//$objMenuPeticionamentoUsuarioExternoDTO->setNumIdTipoControleLitigioso($_GET['id_menu_peticionamento_usuario_externo']);
						$arrObjMenuPeticionamentoUsuarioExternoDTO[] = $objMenuPeticionamentoUsuarioExternoDTO;
					}
					$objMenuPeticionamentoUsuarioExternoRN = new MenuPeticionamentoUsuarioExternoRN();
					$objMenuPeticionamentoUsuarioExternoRN->reativar($arrObjMenuPeticionamentoUsuarioExternoDTO);
					PaginaSEI::getInstance()->adicionarMensagem('Operação realizada com sucesso.');
				}catch(Exception $e){
					PaginaSEI::getInstance()->processarExcecao($e);
				}
				header('Location: '.SessaoSEI::getInstance()->assinarLink('controlador.php?acao='.$_GET['acao_origem'].'&acao_origem='.$_GET['acao'].'&id_menu_peticionamento_usuario_externo='.$objMenuPeticionamentoUsuarioExternoDTO->getNumIdMenuPeticionamentoUsuarioExterno()));
				die;
			}
			break;

		case 'menu_peticionamento_usuario_externo_selecionar':
			
			$strTitulo = PaginaSEI::getInstance()->getTituloSelecao('Selecionar Menu','Selecionar Menus');

			//Se cadastrou alguem
			if ($_GET['acao_origem']=='menu_peticionamento_usuario_externo_cadastrar'){
				if (isset($_GET['id_menu_peticionamento_usuario_externo'])){
					PaginaSEI::getInstance()->adicionarSelecionado($_GET['id_menu_peticionamento_usuario_externo']);
				}
			}
			break;

		case 'menu_peticionamento_usuario_externo_listar':
			
			$strTitulo = 'Cadastro de Menus';			
			//continue;
			break;

		default:
			throw new InfraException("Ação '".$_GET['acao']."' não reconhecida.");
	}

	$arrComandos = array();
	$arrComandos[] = '<button type="button" accesskey="p" id="btnPesquisar" value="Pesquisar" onclick="pesquisar();" class="infraButton"><span class="infraTeclaAtalho">P</span>esquisar</button>';
	
	//TODO: Marcelo, qual é a utilidade dessa funcionalidade de Transportar seleção neste tela?
	if ($_GET['acao'] == 'menu_peticionamento_usuario_externo_selecionar'){
		$arrComandos[] = '<button type="button" accesskey="T" id="btnTransportarSelecao" value="Transportar" onclick="infraTransportarSelecao();" class="infraButton"><span class="infraTeclaAtalho">T</span>ransportar</button>';
	}

	$bolAcaoCadastrar = SessaoSEI::getInstance()->verificarPermissao('menu_peticionamento_usuario_externo_cadastrar');
	
	if ($bolAcaoCadastrar){
		$arrComandos[] = '<button type="button" accesskey="n" id="btnNovo" value="Nova" onclick="location.href=\''.PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?acao=menu_peticionamento_usuario_externo_cadastrar&acao_origem='.$_GET['acao'].'&acao_retorno='.$_GET['acao'])).'\'" class="infraButton"><span class="infraTeclaAtalho">N</span>ovo</button>';
	}

	$objMenuPeticionamentoUsuarioExternoDTO = new MenuPeticionamentoUsuarioExternoDTO();
	$objMenuPeticionamentoUsuarioExternoDTO->retNumIdMenuPeticionamentoUsuarioExterno();
	$objMenuPeticionamentoUsuarioExternoDTO->retStrNome();
	$objMenuPeticionamentoUsuarioExternoDTO->retStrTipo();
	$objMenuPeticionamentoUsuarioExternoDTO->retStrSinAtivo();
	
	if( isset( $_POST['txtNome'] ) ){
		$objMenuPeticionamentoUsuarioExternoDTO->setStrNome( '%'.$_POST['txtNome'].'%', InfraDTO::$OPER_LIKE );
	}
	
	if( isset( $_POST['selTipo'] ) && $_POST['selTipo'] != ""  ){
		$objMenuPeticionamentoUsuarioExternoDTO->setStrTipo( $_POST['selTipo'] );
	}
	
	PaginaSEI::getInstance()->prepararOrdenacao($objMenuPeticionamentoUsuarioExternoDTO, 'Nome', InfraDTO::$TIPO_ORDENACAO_ASC);
	PaginaSEI::getInstance()->prepararPaginacao($objMenuPeticionamentoUsuarioExternoDTO, 200);

	//if( isset( $_GET['id_menu_peticionamento_usuario_externo'] ) && $_GET['id_menu_peticionamento_usuario_externo'] != ""){
		//$objMenuPeticionamentoUsuarioExternoDTO->setNumIdMenuPeticionamentoUsuarioExterno($_GET['id_menu_peticionamento_usuario_externo']);
	//}

	if( isset( $_POST['id_menu_peticionamento_usuario_externo'] )){
		$objMenuPeticionamentoUsuarioExternoDTO->setNumIdTipoControleLitigioso( $_POST['id_menu_peticionamento_usuario_externo'] );
	}

	$objMenuPeticionamentoUsuarioExternoRN = new MenuPeticionamentoUsuarioExternoRN();

	$arrObjMenuPeticionamentoUsuarioExternoDTO = $objMenuPeticionamentoUsuarioExternoRN->listar($objMenuPeticionamentoUsuarioExternoDTO);

	PaginaSEI::getInstance()->processarPaginacao($objMenuPeticionamentoUsuarioExternoDTO);
	$numRegistros = count($arrObjMenuPeticionamentoUsuarioExternoDTO);

	if ($numRegistros > 0){

		$bolCheck = false;

		if ($_GET['acao']=='menu_peticionamento_usuario_externo_selecionar'){
			$bolAcaoReativar = false;
			$bolAcaoConsultar = SessaoSEI::getInstance()->verificarPermissao('menu_peticionamento_usuario_externo_consultar');
			$bolAcaoAlterar = SessaoSEI::getInstance()->verificarPermissao('menu_peticionamento_usuario_externo_alterar');
			$bolAcaoImprimir = false;
			$bolAcaoExcluir = false;
			$bolAcaoDesativar = false;
			$bolCheck = true;
		}else if ($_GET['acao']=='menu_peticionamento_usuario_externo_reativar'){
			$bolAcaoReativar = SessaoSEI::getInstance()->verificarPermissao('menu_peticionamento_usuario_externo_reativar');
			$bolAcaoConsultar = SessaoSEI::getInstance()->verificarPermissao('menu_peticionamento_usuario_externo_consultar');
			$bolAcaoAlterar = false;
			$bolAcaoImprimir = true;
			$bolAcaoExcluir = SessaoSEI::getInstance()->verificarPermissao('menu_peticionamento_usuario_externo_excluir');
			$bolAcaoDesativar = false;
		}else{
			$bolAcaoReativar = false;
			$bolAcaoConsultar = SessaoSEI::getInstance()->verificarPermissao('menu_peticionamento_usuario_externo_consultar');
			$bolAcaoAlterar = SessaoSEI::getInstance()->verificarPermissao('menu_peticionamento_usuario_externo_alterar');
			$bolAcaoImprimir = true;
			$bolAcaoExcluir = SessaoSEI::getInstance()->verificarPermissao('menu_peticionamento_usuario_externo_excluir');
			$bolAcaoDesativar = SessaoSEI::getInstance()->verificarPermissao('menu_peticionamento_usuario_externo_desativar');
		}

		//TODO: Marcelo, se não vai ter o botão de Desativar em lote, melhor retirar todo este bloco de código.
		if ($bolAcaoDesativar){
			$bolCheck = true;
			//$arrComandos[] = '<button type="button" accesskey="t" id="btnDesativar" value="Desativar" onclick="acaoDesativacaoMultipla();" class="infraButton">Desa<span class="infraTeclaAtalho">t</span>ivar</button>';
			$strLinkDesativar = SessaoSEI::getInstance()->assinarLink('controlador.php?id_menu_peticionamento_usuario_externo='. $_GET['id_menu_peticionamento_usuario_externo'] .'&acao=menu_peticionamento_usuario_externo_desativar&acao_origem='.$_GET['acao']);
		}

		$strLinkReativar = SessaoSEI::getInstance()->assinarLink('controlador.php?id_menu_peticionamento_usuario_externo='. $_GET['id_menu_peticionamento_usuario_externo'] .'&acao=menu_peticionamento_usuario_externo_reativar&acao_origem='.$_GET['acao'].'&acao_confirmada=sim');

		//TODO: Marcelo, se não vai ter o botão de Excluir em lote, melhor retirar todo este bloco de código.
		if ($bolAcaoExcluir){
			$bolCheck = true;
			//$arrComandos[] = '<button type="button" accesskey="E" id="btnExcluir" value="Excluir" onclick="acaoExclusaoMultipla();" class="infraButton"><span class="infraTeclaAtalho">E</span>xcluir</button>';
			$strLinkExcluir = SessaoSEI::getInstance()->assinarLink('controlador.php?id_menu_peticionamento_usuario_externo='. $_GET['id_menu_peticionamento_usuario_externo'] .'&acao=menu_peticionamento_usuario_externo_excluir&acao_origem='.$_GET['acao']);
		}
		
		if( $bolAcaoImprimir ) {
			$arrComandos[] = '<button type="button" accesskey="i" id="btnImprimir" value="Imprimir" onclick="infraImprimirTabela();" class="infraButton"><span class="infraTeclaAtalho">I</span>mprimir</button>';
		}
		
		$strResultado = '';

		if ($_GET['acao']!='menu_peticionamento_usuario_externo_reativar'){
			$strSumarioTabela = 'Tabela de Menus.';
			$strCaptionTabela = 'Menus';
		}else{
			$strSumarioTabela = 'Tabela de Menus Inativos.';
			$strCaptionTabela = 'Menus Inativos';
		}

		$strResultado .= '<table width="99%" class="infraTable" summary="'.$strSumarioTabela.'">'."\n";
		$strResultado .= '<caption class="infraCaption">'.PaginaSEI::getInstance()->gerarCaptionTabela($strCaptionTabela,$numRegistros).'</caption>';
		$strResultado .= '<tr>';
		if ($bolCheck) {
			$strResultado .= '<th class="infraTh" width="1%">'.PaginaSEI::getInstance()->getThCheck().'</th>'."\n";
		}
		$strResultado .= '<th class="infraTh" width="30%">'.PaginaSEI::getInstance()->getThOrdenacao($objMenuPeticionamentoUsuarioExternoDTO,'Nome do Menu','Nome',$arrObjMenuPeticionamentoUsuarioExternoDTO).'</th>'."\n";
		$strResultado .= '<th class="infraTh">'.PaginaSEI::getInstance()->getThOrdenacao($objMenuPeticionamentoUsuarioExternoDTO,'Tipo de Menu','Tipo',$arrObjMenuPeticionamentoUsuarioExternoDTO).'</th>'."\n";
		$strResultado .= '<th class="infraTh" width="15%">Ações</th>'."\n";
		$strResultado .= '</tr>'."\n";
		$strCssTr='';
		for($i = 0;$i < $numRegistros; $i++){

			if( isset( $_GET['id_menu_peticionamento_usuario_externo'] ) &&  $_GET['id_menu_peticionamento_usuario_externo'] == $arrObjMenuPeticionamentoUsuarioExternoDTO[$i]->getNumIdMenuPeticionamentoUsuarioExterno() ){
				$strCssTr = '<tr class="infraTrAcessada">';
			}			
			else if( $arrObjMenuPeticionamentoUsuarioExternoDTO[$i]->getStrSinAtivo()=='S' ){
				$strCssTr = ($strCssTr=='<tr class="infraTrClara">')?'<tr class="infraTrEscura">':'<tr class="infraTrClara">';
			} else {
				$strCssTr ='<tr class="trVermelha">';
			}
			 
			$strResultado .= $strCssTr;

			if ($bolCheck){
				$strResultado .= '<td valign="top">'.PaginaSEI::getInstance()->getTrCheck($i,$arrObjMenuPeticionamentoUsuarioExternoDTO[$i]->getNumIdMenuPeticionamentoUsuarioExterno(),$arrObjMenuPeticionamentoUsuarioExternoDTO[$i]->getStrNome()).'</td>';
			}
			$strResultado .= '<td>'. PaginaSEI::tratarHTML( $arrObjMenuPeticionamentoUsuarioExternoDTO[$i]->getStrNome() ) .'</td>';
			
			$strDescricaoTipo = "";
			
			if( $arrObjMenuPeticionamentoUsuarioExternoDTO[$i]->getStrTipo() == MenuPeticionamentoUsuarioExternoRN::$TP_EXTERNO ) {
				$strDescricaoTipo = "Link Externo";
			}
			
			else if( $arrObjMenuPeticionamentoUsuarioExternoDTO[$i]->getStrTipo() == MenuPeticionamentoUsuarioExternoRN::$TP_CONTEUDO_HTML ) {
				$strDescricaoTipo = "Conteúdo HTML";
			}
			
			$strResultado .= '<td>'. PaginaSEI::tratarHTML( $strDescricaoTipo ) .'</td>';
			
			$strResultado .= '<td align="center">';

			$strResultado .= PaginaSEI::getInstance()->getAcaoTransportarItem($i,$arrObjMenuPeticionamentoUsuarioExternoDTO[$i]->getNumIdMenuPeticionamentoUsuarioExterno());
			 
			if ($bolAcaoConsultar){
				$strResultado .= '<a href="'.PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?id_menu_peticionamento_usuario_externo='. $_GET['id_menu_peticionamento_usuario_externo'] .'&acao=menu_peticionamento_usuario_externo_consultar&acao_origem='.$_GET['acao'].'&acao_retorno='.$_GET['acao'].'&id_menu_peticionamento_usuario_externo='.$arrObjMenuPeticionamentoUsuarioExternoDTO[$i]->getNumIdMenuPeticionamentoUsuarioExterno())).'" tabindex="'.PaginaSEI::getInstance()->getProxTabTabela().'"><img src="'.PaginaSEI::getInstance()->getDiretorioImagensGlobal().'/consultar.gif" title="Consultar Menu" alt="Consultar Menu" class="infraImg" /></a>&nbsp;';
			}

			if ($bolAcaoAlterar){
				$idMenu = $arrObjMenuPeticionamentoUsuarioExternoDTO[$i]->getNumIdMenuPeticionamentoUsuarioExterno();
				$strResultado .= '<a href="'.PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?id_menu_peticionamento_usuario_externo='. $idMenu .'&acao=menu_peticionamento_usuario_externo_alterar&acao_origem='.$_GET['acao'].'&acao_retorno='.$_GET['acao'].'&id_menu_peticionamento_usuario_externo='.$arrObjMenuPeticionamentoUsuarioExternoDTO[$i]->getNumIdMenuPeticionamentoUsuarioExterno())).'" tabindex="'.PaginaSEI::getInstance()->getProxTabTabela().'"><img src="'.PaginaSEI::getInstance()->getDiretorioImagensGlobal().'/alterar.gif" title="Alterar Menu" alt="Alterar Menu" class="infraImg" /></a>&nbsp;';
			}

			if ($bolAcaoDesativar || $bolAcaoReativar || $bolAcaoExcluir){
				$strId = $arrObjMenuPeticionamentoUsuarioExternoDTO[$i]->getNumIdMenuPeticionamentoUsuarioExterno();
				$strDescricao = "'" . PaginaSEI::getInstance()->formatarParametrosJavaScript( PaginaSEI::tratarHTML( $arrObjMenuPeticionamentoUsuarioExternoDTO[$i]->getStrNome(), true ) ) . "'";
			}

			if ($bolAcaoDesativar && $arrObjMenuPeticionamentoUsuarioExternoDTO[$i]->getStrSinAtivo() == 'S'){
				$strResultado .= '<a href="'.PaginaSEI::getInstance()->montarAncora($strId).'" onclick="acaoDesativar(\''.$strId.'\', '. $strDescricao. ');" tabindex="'.PaginaSEI::getInstance()->getProxTabTabela().'"><img src="'.PaginaSEI::getInstance()->getDiretorioImagensGlobal().'/desativar.gif" title="Desativar Menu" alt="Desativar Menu" class="infraImg" /></a>&nbsp;';
			} else {
				$strResultado .= '<a href="'.PaginaSEI::getInstance()->montarAncora($strId).'" onclick="acaoReativar(\''.$strId.'\', '.$strDescricao. ');" tabindex="'.PaginaSEI::getInstance()->getProxTabTabela().'"><img src="'.PaginaSEI::getInstance()->getDiretorioImagensGlobal().'/reativar.gif" title="Reativar Menu" alt="Reativar Menu" class="infraImg" /></a>&nbsp;';
			}

			if ($bolAcaoExcluir){
				$strResultado .= '<a href="'.PaginaSEI::getInstance()->montarAncora($strId).'" onclick="acaoExcluir(\''.$strId.'\', '.$strDescricao. ');" tabindex="'.PaginaSEI::getInstance()->getProxTabTabela().'"><img src="'.PaginaSEI::getInstance()->getDiretorioImagensGlobal().'/excluir.gif" title="Excluir Menu" alt="Excluir Menu" class="infraImg" /></a>&nbsp;';
			}

			$strResultado .= '</td></tr>'."\n";
		}
		$strResultado .= '</table>';
	}
	if ($_GET['acao'] == 'menu_peticionamento_usuario_externo_selecionar'){
		$arrComandos[] = '<button type="button" accesskey="c" id="btnFecharSelecao" value="Fechar" onclick="window.close();" class="infraButton">Fe<span class="infraTeclaAtalho">c</span>har</button>';
	}else{
		$arrComandos[] = '<button type="button" accesskey="c" id="btnFechar" value="Fechar" onclick="location.href=\''.PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?id_menu_peticionamento_usuario_externo='.$_GET['id_menu_peticionamento_usuario_externo'].'&acao='.PaginaSEI::getInstance()->getAcaoRetorno().'&acao_origem='.$_GET['acao'])).'\'" class="infraButton">Fe<span class="infraTeclaAtalho">c</span>har</button>';
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

#lblNome {position:absolute;left:0%;top:0%;width:30%;}
#txtNome {position:absolute;left:0%;top:40%;width:20%;}

#lblTipo {position:absolute;left:21%;top:0%;width:30%;}
#selTipo {position:absolute;left:21%;top:40%;width:20%;}

<?
PaginaSEI::getInstance()->fecharStyle();
PaginaSEI::getInstance()->montarJavaScript();
PaginaSEI::getInstance()->abrirJavaScript();
?>

function inicializar(){
  if ('<?=$_GET['acao']?>'=='menu_peticionamento_usuario_externo_selecionar'){
    infraReceberSelecao();
    document.getElementById('btnFecharSelecao').focus();
  }else{
    document.getElementById('btnFechar').focus();
  }
  infraEfeitoTabelas();
}

<? if ($bolAcaoDesativar){ ?>
function acaoDesativar(id,desc){
  if (confirm("Confirma desativação do Menu \""+desc+"\"?")){
    document.getElementById('hdnInfraItemId').value=id;
    document.getElementById('frmLista').action='<?=$strLinkDesativar?>';
    document.getElementById('frmLista').submit();
  }
}

function acaoDesativacaoMultipla(){
  if (document.getElementById('hdnInfraItensSelecionados').value==''){
    alert('Nenhum menu selecionado.');
    return;
  }
  if (confirm("Confirma a desativação dos Menus selecionados?")){
    document.getElementById('hdnInfraItemId').value='';
    document.getElementById('frmLista').action='<?=$strLinkDesativar?>';
    document.getElementById('frmLista').submit();
  }
}
<? } ?>

function acaoReativar(id,desc){
  if (confirm("Confirma reativação do Menu \""+desc+"\"?")){
    document.getElementById('hdnInfraItemId').value=id;
    document.getElementById('frmLista').action='<?=$strLinkReativar?>';
    document.getElementById('frmLista').submit();
  }
}

function acaoReativacaoMultipla(){
  if (document.getElementById('hdnInfraItensSelecionados').value==''){
    alert('Nenhum Menu selecionado.');
    return;
  }
  if (confirm("Confirma a reativação dos Menus selecionados?")){
    document.getElementById('hdnInfraItemId').value='';
    document.getElementById('frmLista').action='<?=$strLinkReativar?>';
    document.getElementById('frmLista').submit();
  }
}

<? if ($bolAcaoExcluir){ ?>
function acaoExcluir(id,desc){
  if (confirm("Confirma exclusão do Menu \""+desc+"\"?")){
    document.getElementById('hdnInfraItemId').value=id;
    document.getElementById('frmLista').action='<?=$strLinkExcluir?>';
    document.getElementById('frmLista').submit();
  }
}

function acaoExclusaoMultipla(){
  if (document.getElementById('hdnInfraItensSelecionados').value==''){
    alert('Nenhum menu selecionado.');
    return;
  }
  if (confirm("Confirma a exclusão dos Menus selecionadas?")){
    document.getElementById('hdnInfraItemId').value='';
    document.getElementById('frmLista').action='<?=$strLinkExcluir?>';
    document.getElementById('frmLista').submit();
  }
}
<? } ?>

function pesquisar(){
   
   document.getElementById('frmLista').submit();
   
}

<?
PaginaSEI::getInstance()->fecharJavaScript();
PaginaSEI::getInstance()->fecharHead();
PaginaSEI::getInstance()->abrirBody($strTitulo,'onload="inicializar();"');
$strNome = $_POST['txtNome'];
$strTipo = $_POST['selTipo'];;
?>
<form id="frmLista" method="post" action="<?=PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?id_menu_peticionamento_usuario_externo='. $_GET['id_menu_peticionamento_usuario_externo'] .'&acao='.$_GET['acao'].'&acao_origem='.$_GET['acao']))?>">
    
  <? PaginaSEI::getInstance()->montarBarraComandosSuperior($arrComandos); ?>
  
   <div style="height:4.5em; margin-top: 11px;" class="infraAreaDados" id="divInfraAreaDados">
  
  <!--  Nome do Menu -->
  	<label id="lblNome" for="txtNome" class="infraLabelOpcional">Nome do Menu:</label>
    <input type="text" name="txtNome" id="txtNome" maxlength="30" value="<?= $strNome ?>" class="infraText" />

<!--  Tipo do Menu -->
 <label id="lblTipo" for="selTipo" class="infraLabelOpcional">Tipo de Menu:</label>
  <select onchange="pesquisar()" id="selTipo" name="selTipo" class="infraSelect" >
  <option value="" <?if( $strTipo == "" ) { echo " selected='selected' "; } ?> > Todos </option>
  <option value="E" <?if( $strTipo == "E" ) { echo " selected='selected' "; } ?> >Link Externo</option>
  <option value="H" <?if( $strTipo == "H" ) { echo " selected='selected' "; } ?> >Conteúdo HTML</option>
  </select> 
  
  <input type="submit" style="visibility: hidden;" />
 </div>
  
  <?  
  PaginaSEI::getInstance()->montarAreaTabela($strResultado,$numRegistros);
  PaginaSEI::getInstance()->montarBarraComandosInferior($arrComandos);
  ?>

</form>
<?
PaginaSEI::getInstance()->fecharBody();
PaginaSEI::getInstance()->fecharHtml();
?>