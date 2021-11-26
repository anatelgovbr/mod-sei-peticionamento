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
		case 'md_pet_menu_usu_ext_excluir':

			try{
				$arrStrIds = PaginaSEI::getInstance()->getArrStrItensSelecionados();
				$arrObjMdPetMenuUsuarioExternoDTO = array();
				
				for ($i=0;$i<count($arrStrIds);$i++){
					$objMdPetMenuUsuarioExternoDTO = new MdPetMenuUsuarioExternoDTO();
					$objMdPetMenuUsuarioExternoDTO->setNumIdMenuPeticionamentoUsuarioExterno($arrStrIds[$i]);
					$arrObjMdPetMenuUsuarioExternoDTO[] = $objMdPetMenuUsuarioExternoDTO;
				}

				$objMdPetMenuUsuarioExternoRN = new MdPetMenuUsuarioExternoRN();
				$objMdPetMenuUsuarioExternoRN->excluir($arrObjMdPetMenuUsuarioExternoDTO);
				PaginaSEI::getInstance()->adicionarMensagem('Opera��o realizada com sucesso.');

			}catch(Exception $e){
				PaginaSEI::getInstance()->processarExcecao($e);
			}
			header('Location: '.SessaoSEI::getInstance()->assinarLink('controlador.php?id_menu_peticionamento_usuario_externo='. $_GET['id_menu_peticionamento_usuario_externo'] .'&acao='.$_GET['acao_origem'].'&acao_origem='.$_GET['acao']));
			die;

		case 'md_pet_menu_usu_ext_desativar':
			try{
				$arrStrIds = PaginaSEI::getInstance()->getArrStrItensSelecionados();
				$arrObjMdPetMenuUsuarioExternoDTO = array();
				for ($i=0;$i<count($arrStrIds);$i++){
					$objMdPetMenuUsuarioExternoDTO = new MdPetMenuUsuarioExternoDTO();
					$objMdPetMenuUsuarioExternoDTO->setNumIdMenuPeticionamentoUsuarioExterno($arrStrIds[$i]);
					$arrObjMdPetMenuUsuarioExternoDTO[] = $objMdPetMenuUsuarioExternoDTO;
				}
				$objMdPetMenuUsuarioExternoRN = new MdPetMenuUsuarioExternoRN();
				$objMdPetMenuUsuarioExternoRN->desativar($arrObjMdPetMenuUsuarioExternoDTO);
			}catch(Exception $e){
				PaginaSEI::getInstance()->processarExcecao($e);
			}
			header('Location: '.SessaoSEI::getInstance()->assinarLink('controlador.php?id_menu_peticionamento_usuario_externo='. $_GET['id_menu_peticionamento_usuario_externo'] .'&acao='.$_GET['acao_origem'].'&acao_origem='.$_GET['acao']));
			die;

		case 'md_pet_menu_usu_ext_reativar':

			$strTitulo = 'Reativar Menus';

			if ($_GET['acao_confirmada']=='sim'){

				try{
					$arrStrIds = PaginaSEI::getInstance()->getArrStrItensSelecionados();
					$arrObjMdPetMenuUsuarioExternoDTO = array();
					for ($i=0;$i<count($arrStrIds);$i++){
						$objMdPetMenuUsuarioExternoDTO = new MdPetMenuUsuarioExternoDTO();
						$objMdPetMenuUsuarioExternoDTO->setNumIdMenuPeticionamentoUsuarioExterno($arrStrIds[$i]);
						$arrObjMdPetMenuUsuarioExternoDTO[] = $objMdPetMenuUsuarioExternoDTO;
					}
					$objMdPetMenuUsuarioExternoRN = new MdPetMenuUsuarioExternoRN();
					$objMdPetMenuUsuarioExternoRN->reativar($arrObjMdPetMenuUsuarioExternoDTO);
					PaginaSEI::getInstance()->adicionarMensagem('Opera��o realizada com sucesso.');
				}catch(Exception $e){
					PaginaSEI::getInstance()->processarExcecao($e);
				}
				header('Location: '.SessaoSEI::getInstance()->assinarLink('controlador.php?acao='.$_GET['acao_origem'].'&acao_origem='.$_GET['acao'].'&id_menu_peticionamento_usuario_externo='.$objMdPetMenuUsuarioExternoDTO->getNumIdMenuPeticionamentoUsuarioExterno()));
				die;
			}
			break;

		case 'menu_peticionamento_usuario_externo_selecionar':
			
			$strTitulo = PaginaSEI::getInstance()->getTituloSelecao('Selecionar Menu','Selecionar Menus');

			//Se cadastrou alguem
			if ($_GET['acao_origem']=='md_pet_menu_usu_ext_cadastrar'){
				if (isset($_GET['id_menu_peticionamento_usuario_externo'])){
					PaginaSEI::getInstance()->adicionarSelecionado($_GET['id_menu_peticionamento_usuario_externo']);
				}
			}
			break;

		case 'md_pet_menu_usu_ext_listar':

			$strTitulo = 'Cadastro de Menus';			
			//continue;
			break;

		default:
			throw new InfraException("A��o '".$_GET['acao']."' n�o reconhecida.");
	}

	$arrComandos = array();
	$arrComandos[] = '<button type="button" accesskey="p" id="btnPesquisar" value="Pesquisar" onclick="pesquisar();" class="infraButton"><span class="infraTeclaAtalho">P</span>esquisar</button>';
	
	//TODO: Marcelo, qual � a utilidade dessa funcionalidade de Transportar sele��o neste tela?
	if ($_GET['acao'] == 'menu_peticionamento_usuario_externo_selecionar'){
		$arrComandos[] = '<button type="button" accesskey="T" id="btnTransportarSelecao" value="Transportar" onclick="infraTransportarSelecao();" class="infraButton"><span class="infraTeclaAtalho">T</span>ransportar</button>';
	}

	$bolAcaoCadastrar = SessaoSEI::getInstance()->verificarPermissao('md_pet_menu_usu_ext_cadastrar');

	if ($bolAcaoCadastrar){
		$arrComandos[] = '<button type="button" accesskey="n" id="btnNovo" value="Nova" onclick="location.href=\''.PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_pet_menu_usu_ext_cadastrar&acao_origem='.$_GET['acao'].'&acao_retorno='.$_GET['acao'])).'\'" class="infraButton"><span class="infraTeclaAtalho">N</span>ovo</button>';
	}

	$objMdPetMenuUsuarioExternoDTO = new MdPetMenuUsuarioExternoDTO();
	$objMdPetMenuUsuarioExternoDTO->retNumIdMenuPeticionamentoUsuarioExterno();
	$objMdPetMenuUsuarioExternoDTO->retStrNome();
	$objMdPetMenuUsuarioExternoDTO->retStrTipo();
	$objMdPetMenuUsuarioExternoDTO->retStrSinAtivo();
	
	if( isset( $_POST['txtNome'] ) ){
		$objMdPetMenuUsuarioExternoDTO->setStrNome( '%'.$_POST['txtNome'].'%', InfraDTO::$OPER_LIKE );
	}
	
	if( isset( $_POST['selTipo'] ) && $_POST['selTipo'] != ""  ){
		$objMdPetMenuUsuarioExternoDTO->setStrTipo( $_POST['selTipo'] );
	}
	
	PaginaSEI::getInstance()->prepararOrdenacao($objMdPetMenuUsuarioExternoDTO, 'Nome', InfraDTO::$TIPO_ORDENACAO_ASC);
	PaginaSEI::getInstance()->prepararPaginacao($objMdPetMenuUsuarioExternoDTO, 200);

	if( isset( $_POST['id_menu_peticionamento_usuario_externo'] )){
		$objMdPetMenuUsuarioExternoDTO->setNumIdTipoControleLitigioso( $_POST['id_menu_peticionamento_usuario_externo'] );
	}

	$objMdPetMenuUsuarioExternoRN = new MdPetMenuUsuarioExternoRN();

	$arrObjMdPetMenuUsuarioExternoDTO = $objMdPetMenuUsuarioExternoRN->listar($objMdPetMenuUsuarioExternoDTO);

	PaginaSEI::getInstance()->processarPaginacao($objMdPetMenuUsuarioExternoDTO);
	$numRegistros = count($arrObjMdPetMenuUsuarioExternoDTO);

	if ($numRegistros > 0){

		$bolCheck = false;

		if ($_GET['acao']=='menu_peticionamento_usuario_externo_selecionar'){
			$bolAcaoReativar = false;
			$bolAcaoConsultar = SessaoSEI::getInstance()->verificarPermissao('md_pet_menu_usu_ext_consultar');
			$bolAcaoAlterar = SessaoSEI::getInstance()->verificarPermissao('md_pet_menu_usu_ext_alterar');
			$bolAcaoImprimir = false;
			$bolAcaoExcluir = false;
			$bolAcaoDesativar = false;
			$bolCheck = true;
		}else if ($_GET['acao']=='md_pet_menu_usu_ext_reativar'){
			$bolAcaoReativar = SessaoSEI::getInstance()->verificarPermissao('md_pet_menu_usu_ext_reativar');
			$bolAcaoConsultar = SessaoSEI::getInstance()->verificarPermissao('md_pet_menu_usu_ext_consultar');
			$bolAcaoAlterar = false;
			$bolAcaoImprimir = true;
			$bolAcaoExcluir = SessaoSEI::getInstance()->verificarPermissao('md_pet_menu_usu_ext_excluir');
			$bolAcaoDesativar = false;
		}else{
			$bolAcaoReativar = false;
			$bolAcaoConsultar = SessaoSEI::getInstance()->verificarPermissao('md_pet_menu_usu_ext_consultar');
			$bolAcaoAlterar = SessaoSEI::getInstance()->verificarPermissao('md_pet_menu_usu_ext_alterar');
			$bolAcaoImprimir = true;
			$bolAcaoExcluir = SessaoSEI::getInstance()->verificarPermissao('md_pet_menu_usu_ext_excluir');
			$bolAcaoDesativar = SessaoSEI::getInstance()->verificarPermissao('md_pet_menu_usu_ext_desativar');
		}

		//TODO: Marcelo, se n�o vai ter o bot�o de Desativar em lote, melhor retirar todo este bloco de c�digo.
		if ($bolAcaoDesativar){
			$bolCheck = true;
			$strLinkDesativar = SessaoSEI::getInstance()->assinarLink('controlador.php?id_menu_peticionamento_usuario_externo='. $_GET['id_menu_peticionamento_usuario_externo'] .'&acao=md_pet_menu_usu_ext_desativar&acao_origem='.$_GET['acao']);
		}

		$strLinkReativar = SessaoSEI::getInstance()->assinarLink('controlador.php?id_menu_peticionamento_usuario_externo='. $_GET['id_menu_peticionamento_usuario_externo'] .'&acao=md_pet_menu_usu_ext_reativar&acao_origem='.$_GET['acao'].'&acao_confirmada=sim');

		//TODO: Marcelo, se n�o vai ter o bot�o de Excluir em lote, melhor retirar todo este bloco de c�digo.
		if ($bolAcaoExcluir){
			$bolCheck = true;
			$strLinkExcluir = SessaoSEI::getInstance()->assinarLink('controlador.php?id_menu_peticionamento_usuario_externo='. $_GET['id_menu_peticionamento_usuario_externo'] .'&acao=md_pet_menu_usu_ext_excluir&acao_origem='.$_GET['acao']);
		}

		if( $bolAcaoImprimir ) {
			$arrComandos[] = '<button type="button" accesskey="i" id="btnImprimir" value="Imprimir" onclick="infraImprimirTabela();" class="infraButton"><span class="infraTeclaAtalho">I</span>mprimir</button>';
		}
		
		$strResultado = '';

		if ($_GET['acao']!='md_pet_menu_usu_ext_reativar'){
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
		$strResultado .= '<th class="infraTh" width="30%">'.PaginaSEI::getInstance()->getThOrdenacao($objMdPetMenuUsuarioExternoDTO,'Nome do Menu','Nome',$arrObjMdPetMenuUsuarioExternoDTO).'</th>'."\n";
		$strResultado .= '<th class="infraTh">'.PaginaSEI::getInstance()->getThOrdenacao($objMdPetMenuUsuarioExternoDTO,'Tipo de Menu','Tipo',$arrObjMdPetMenuUsuarioExternoDTO).'</th>'."\n";
		$strResultado .= '<th class="infraTh" width="15%">A��es</th>'."\n";
		$strResultado .= '</tr>'."\n";
		$strCssTr='';
		for($i = 0;$i < $numRegistros; $i++){

			if( isset( $_GET['id_menu_peticionamento_usuario_externo'] ) &&  $_GET['id_menu_peticionamento_usuario_externo'] == $arrObjMdPetMenuUsuarioExternoDTO[$i]->getNumIdMenuPeticionamentoUsuarioExterno() ){
				$strCssTr = '<tr class="infraTrAcessada">';
			}			
			else if( $arrObjMdPetMenuUsuarioExternoDTO[$i]->getStrSinAtivo()=='S' ){
				$strCssTr = ($strCssTr=='<tr class="infraTrClara">')?'<tr class="infraTrEscura">':'<tr class="infraTrClara">';
			} else {
				$strCssTr ='<tr class="trVermelha">';
			}
			 
			$strResultado .= $strCssTr;

			if ($bolCheck){
				$strResultado .= '<td valign="top">'.PaginaSEI::getInstance()->getTrCheck($i,$arrObjMdPetMenuUsuarioExternoDTO[$i]->getNumIdMenuPeticionamentoUsuarioExterno(),$arrObjMdPetMenuUsuarioExternoDTO[$i]->getStrNome()).'</td>';
			}
			$strResultado .= '<td>'. PaginaSEI::tratarHTML( $arrObjMdPetMenuUsuarioExternoDTO[$i]->getStrNome() ) .'</td>';
			
			$strDescricaoTipo = "";
			
			if( $arrObjMdPetMenuUsuarioExternoDTO[$i]->getStrTipo() == MdPetMenuUsuarioExternoRN::$TP_EXTERNO ) {
				$strDescricaoTipo = "Link Externo";
			}
			
			else if( $arrObjMdPetMenuUsuarioExternoDTO[$i]->getStrTipo() == MdPetMenuUsuarioExternoRN::$TP_CONTEUDO_HTML ) {
				$strDescricaoTipo = "Conte�do HTML";
			}
			
			$strResultado .= '<td>'. PaginaSEI::tratarHTML( $strDescricaoTipo ) .'</td>';
			
			$strResultado .= '<td align="center">';

			$strResultado .= PaginaSEI::getInstance()->getAcaoTransportarItem($i,$arrObjMdPetMenuUsuarioExternoDTO[$i]->getNumIdMenuPeticionamentoUsuarioExterno());
			 
			if ($bolAcaoConsultar){
				$strResultado .= '<a href="'.PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?id_menu_peticionamento_usuario_externo='. $_GET['id_menu_peticionamento_usuario_externo'] .'&acao=md_pet_menu_usu_ext_consultar&acao_origem='.$_GET['acao'].'&acao_retorno='.$_GET['acao'].'&id_menu_peticionamento_usuario_externo='.$arrObjMdPetMenuUsuarioExternoDTO[$i]->getNumIdMenuPeticionamentoUsuarioExterno())).'" tabindex="'.PaginaSEI::getInstance()->getProxTabTabela().'"><img src="'.PaginaSEI::getInstance()->getDiretorioImagensGlobal().'/consultar.gif" title="Consultar Menu" alt="Consultar Menu" class="infraImg" /></a>&nbsp;';
			}

			if ($bolAcaoAlterar){
				$idMenu = $arrObjMdPetMenuUsuarioExternoDTO[$i]->getNumIdMenuPeticionamentoUsuarioExterno();
				$strResultado .= '<a href="'.PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?id_menu_peticionamento_usuario_externo='. $idMenu .'&acao=md_pet_menu_usu_ext_alterar&acao_origem='.$_GET['acao'].'&acao_retorno='.$_GET['acao'].'&id_menu_peticionamento_usuario_externo='.$arrObjMdPetMenuUsuarioExternoDTO[$i]->getNumIdMenuPeticionamentoUsuarioExterno())).'" tabindex="'.PaginaSEI::getInstance()->getProxTabTabela().'"><img src="'.PaginaSEI::getInstance()->getDiretorioImagensGlobal().'/alterar.gif" title="Alterar Menu" alt="Alterar Menu" class="infraImg" /></a>&nbsp;';
			}

			if ($bolAcaoDesativar || $bolAcaoReativar || $bolAcaoExcluir){
				$strId = $arrObjMdPetMenuUsuarioExternoDTO[$i]->getNumIdMenuPeticionamentoUsuarioExterno();
				$strDescricao = "'" . PaginaSEI::getInstance()->formatarParametrosJavaScript( PaginaSEI::tratarHTML( $arrObjMdPetMenuUsuarioExternoDTO[$i]->getStrNome(), true ) ) . "'";
			}

			if ($bolAcaoDesativar && $arrObjMdPetMenuUsuarioExternoDTO[$i]->getStrSinAtivo() == 'S'){
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
  if (confirm("Confirma desativa��o do Menu \""+desc+"\"?")){
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
  if (confirm("Confirma a desativa��o dos Menus selecionados?")){
    document.getElementById('hdnInfraItemId').value='';
    document.getElementById('frmLista').action='<?=$strLinkDesativar?>';
    document.getElementById('frmLista').submit();
  }
}
<? } ?>

function acaoReativar(id,desc){
  if (confirm("Confirma reativa��o do Menu \""+desc+"\"?")){
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
  if (confirm("Confirma a reativa��o dos Menus selecionados?")){
    document.getElementById('hdnInfraItemId').value='';
    document.getElementById('frmLista').action='<?=$strLinkReativar?>';
    document.getElementById('frmLista').submit();
  }
}

<? if ($bolAcaoExcluir){ ?>
function acaoExcluir(id,desc){
  if (confirm("Confirma exclus�o do Menu \""+desc+"\"?")){
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
  if (confirm("Confirma a exclus�o dos Menus selecionadas?")){
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
    <input type="text" name="txtNome" id="txtNome" maxlength="30" value="<?= PaginaSEI::tratarHTML($strNome) ?>" class="infraText" />

<!--  Tipo do Menu -->
 <label id="lblTipo" for="selTipo" class="infraLabelOpcional">Tipo de Menu:</label>
  <select onchange="pesquisar()" id="selTipo" name="selTipo" class="infraSelect" >
  <option value="" <?if( $strTipo == "" ) { echo " selected='selected' "; } ?> > Todos </option>
  <option value="E" <?if( $strTipo == "E" ) { echo " selected='selected' "; } ?> >Link Externo</option>
  <option value="H" <?if( $strTipo == "H" ) { echo " selected='selected' "; } ?> >Conte�do HTML</option>
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