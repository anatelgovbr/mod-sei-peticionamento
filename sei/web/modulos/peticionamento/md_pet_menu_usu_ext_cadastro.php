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
	PaginaSEI::getInstance()->setBolXHTML(false);
	
	//////////////////////////////////////////////////////////////////////////////
	//InfraDebug::getInstance()->setBolLigado(false);
	//InfraDebug::getInstance()->setBolDebugInfra(true);
	//InfraDebug::getInstance()->limpar();
	//////////////////////////////////////////////////////////////////////////////

	SessaoSEI::getInstance()->validarLink();

	PaginaSEI::getInstance()->verificarSelecao('menu_peticionamento_usuario_externo_selecionar');

	SessaoSEI::getInstance()->validarPermissao($_GET['acao']);

	$arrComandos = array();

	$disabled = '';
	
	switch($_GET['acao']){
		
		case 'md_pet_menu_usu_ext_cadastrar':
			
			$strTitulo = 'Novo Menu';
			$arrComandos[] = '<button type="submit" accesskey="s" name="sbmCadastrarOrientacoesPetIndisp" value="Salvar" class="infraButton"><span class="infraTeclaAtalho">S</span>alvar</button>';
			$arrComandos[] = '<button type="button" accesskey="c" name="btnCancelar" id="btnCancelar" value="Cancelar" onclick="location.href=\''.PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?acao='.PaginaSEI::getInstance()->getAcaoRetorno().'&acao_origem='.$_GET['acao'])).'\';" class="infraButton"><span class="infraTeclaAtalho">C</span>ancelar</button>';

			$objEditorRN=new EditorRN();
			$objEditorDTO=new EditorDTO();
				
			$objEditorDTO->setStrNomeCampo('txaConteudo');
			$objEditorDTO->setStrSinSomenteLeitura('N');
			$objEditorDTO->setNumTamanhoEditor(400);
			$retEditor = $objEditorRN->montarSimples($objEditorDTO);
			
			$txtConteudo = $_POST['txaConteudo'];
			$txtUrl = $_POST['txtUrl'];
			$txtNome = $_POST['txtNome'];
			$tipo = $_POST['tipo'];
			
			$objMdPetMenuUsuarioExternoDTO = new MdPetMenuUsuarioExternoDTO();
			$objMdPetMenuUsuarioExternoDTO->setStrConteudoHtml('');
			
			if (isset($_POST['hdnIdMenuPeticionamentoUsuarioExterno'])) {
				
				try{
					
					if( $_POST['tipo'] == MdPetMenuUsuarioExternoRN::$TP_EXTERNO ){
						$_POST['txaConteudo'] = '';
					}
						
					if( $_POST['tipo'] == MdPetMenuUsuarioExternoRN::$TP_CONTEUDO_HTML ){
						$_POST['txtUrl'] = '';
					}
					
					$objMdPetMenuUsuarioExternoDTO->setStrConteudoHtml($_POST['txaConteudo']);
					
					//Estilo
					$conjuntoEstilosRN = new ConjuntoEstilosRN();
			  		$conjuntoEstilosDTO = new ConjuntoEstilosDTO();
			  		$conjuntoEstilosDTO->setStrSinUltimo('S');
			  		$conjuntoEstilosDTO->retNumIdConjuntoEstilos();
			  		$conjuntoEstilosDTO = $conjuntoEstilosRN->consultar( $conjuntoEstilosDTO );
					$objMdPetMenuUsuarioExternoDTO->setNumIdConjuntoEstilos( $conjuntoEstilosDTO->getNumIdConjuntoEstilos() );

					$objMdPetMenuUsuarioExternoDTO->setStrUrl($_POST['txtUrl']);
					$objMdPetMenuUsuarioExternoDTO->setStrNome($_POST['txtNome']);
					$objMdPetMenuUsuarioExternoDTO->setStrTipo($_POST['tipo']);
					$objMdPetMenuUsuarioExternoRN  = new MdPetMenuUsuarioExternoRN();
					$objMdPetMenuUsuarioExternoDTO = $objMdPetMenuUsuarioExternoRN->cadastrar($objMdPetMenuUsuarioExternoDTO);
					header('Location: '.SessaoSEI::getInstance()->assinarLink('controlador.php?acao='.PaginaSEI::getInstance()->getAcaoRetorno().'&acao_origem='.$_GET['acao'].'&id_menu_peticionamento_usuario_externo='.$objMdPetMenuUsuarioExternoDTO->getNumIdMenuPeticionamentoUsuarioExterno()));
					die;
					
				} catch(Exception $e){
					PaginaSEI::getInstance()->processarExcecao($e);
				}
			}
			break;
		
		case 'md_pet_menu_usu_ext_consultar':
			
			$disabled = '';
			$strTitulo = 'Consultar Menu';
			$disabled = " disabled='disabled' ";

			//TODO: Marcelo ou Herley, a constru��o dos Cases Alterar e Consultar desta funcionalidade ficou muito diferente da forma que foi constru�do para Tipos de Processos para Peticionamento e para Indisponibilidades do SEI. Tem que padronizar, para ficar igual as outras duas funcionalidades. Ainda, Consultar tem o bot�o "Fechar", enquanto que Novo e Alterar tem o bot�o "Cancelar".
			$arrComandos[] = '<button type="button" accesskey="c" name="btnFechar" id="btnFechar" value="Fechar" onclick="location.href=\''.PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?acao='.PaginaSEI::getInstance()->getAcaoRetorno().'&acao_origem='.$_GET['acao'].PaginaSEI::getInstance()->montarAncora($_GET['id_menu_peticionamento_usuario_externo']))).'\';" class="infraButton">Fe<span class="infraTeclaAtalho">c</span>har</button>';
			
			$objEditorRN=new EditorRN();
			$objEditorDTO=new EditorDTO();
			
			$objEditorDTO->setStrNomeCampo('txaConteudo');
			$objEditorDTO->setStrSinSomenteLeitura('N');
			$objEditorDTO->setNumTamanhoEditor(400);
			$retEditor = $objEditorRN->montarSimples($objEditorDTO);			
			
			$objMdPetMenuUsuarioExternoDTO2 = new MdPetMenuUsuarioExternoDTO();
			$objMdPetMenuUsuarioExternoDTO2->retTodos();

			$objMdPetMenuUsuarioExternoRN  = new MdPetMenuUsuarioExternoRN();
			
			if ( !isset($_POST['hdnIdMenuPeticionamentoUsuarioExterno'])) {
				$objMdPetMenuUsuarioExternoDTO2->setNumIdMenuPeticionamentoUsuarioExterno( $_GET['id_menu_peticionamento_usuario_externo'] );
				$objLista = $objMdPetMenuUsuarioExternoRN->consultar($objMdPetMenuUsuarioExternoDTO2);
				
				$txtNome = $objLista->getStrNome();
				$tipo = $objLista->getStrTipo();
				$txtConteudo = $objLista->getStrConteudoHtml();
				$txtUrl = $objLista->getStrUrl();
				$sinAtivo = $objLista->getStrSinAtivo();
			} else {
				try{

					$objMdPetMenuUsuarioExternoDTO2->setNumIdMenuPeticionamentoUsuarioExterno( $_POST['hdnIdMenuPeticionamentoUsuarioExterno'] );
					$objMdPetMenuUsuarioExternoDTO2 = $objMdPetMenuUsuarioExternoRN->consultar($objMdPetMenuUsuarioExternoDTO2);
					
					$txtNome = $_POST['txtNome'];
					$tipo = $_POST['tipo'];
					$txtConteudo = $_POST['txaConteudo'];
					$txtUrl = $_POST['txtUrl'];
					
					if( $_POST['tipo'] == MdPetMenuUsuarioExternoRN::$TP_EXTERNO ){
						$_POST['txaConteudo'] = '';
					}
					
					if( $_POST['tipo'] == MdPetMenuUsuarioExternoRN::$TP_CONTEUDO_HTML ){
						$_POST['txtUrl'] = '';
					}

					$objMdPetMenuUsuarioExternoDTO2->setNumIdMenuPeticionamentoUsuarioExterno( $_POST['hdnIdMenuPeticionamentoUsuarioExterno'] );
					$objMdPetMenuUsuarioExternoDTO2->setStrConteudoHtml($_POST['txaConteudo']);
					
					//Estilo
					$conjuntoEstilosRN = new ConjuntoEstilosRN();
			  		$conjuntoEstilosDTO = new ConjuntoEstilosDTO();
			  		$conjuntoEstilosDTO->setStrSinUltimo('S');
			  		$conjuntoEstilosDTO->retNumIdConjuntoEstilos();
			  		$conjuntoEstilosDTO = $conjuntoEstilosRN->consultar( $conjuntoEstilosDTO );
					$objMdPetMenuUsuarioExternoDTO2->setNumIdConjuntoEstilos( $conjuntoEstilosDTO->getNumIdConjuntoEstilos() );

					$objMdPetMenuUsuarioExternoDTO2->setStrUrl($_POST['txtUrl']);
					$objMdPetMenuUsuarioExternoDTO2->setStrNome($_POST['txtNome']);
					$objMdPetMenuUsuarioExternoDTO2->setStrTipo($_POST['tipo']);

					$objMdPetMenuUsuarioExternoDTO =  $objMdPetMenuUsuarioExternoRN->alterar($objMdPetMenuUsuarioExternoDTO2);

					header('Location: '.SessaoSEI::getInstance()->assinarLink('controlador.php?acao='.PaginaSEI::getInstance()->getAcaoRetorno().'&acao_origem='.$_GET['acao'] . '&id_menu_peticionamento_usuario_externo=' . $_POST['hdnIdMenuPeticionamentoUsuarioExterno']));

					die;
					
				} catch(Exception $e){
					PaginaSEI::getInstance()->processarExcecao($e);
				}
			}
			
			break;
		
		case 'md_pet_menu_usu_ext_alterar':
			
			$disabled = '';
			$strTitulo = 'Alterar Menu';
			$disabled = '';

			$arrComandos[] = '<button type="submit" accesskey="s" name="sbmCadastrarOrientacoesPetIndisp" value="Salvar" class="infraButton"><span class="infraTeclaAtalho">S</span>alvar</button>';

			//TODO: Marcelo ou Herley, a constru��o dos Cases Alterar e Consultar desta funcionalidade ficou muito diferente da forma que foi constru�do para Tipos de Processos para Peticionamento e para Indisponibilidades do SEI. Tem que padronizar, para ficar igual as outras duas funcionalidades. Ainda, Consultar tem o bot�o "Fechar", enquanto que Novo e Alterar tem o bot�o "Cancelar".
			$arrComandos[] = '<button type="button" accesskey="c" name="btnCancelar" id="btnCancelar" value="Cancelar" onclick="location.href=\''.PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?acao='.PaginaSEI::getInstance()->getAcaoRetorno().'&acao_origem='.$_GET['acao'].PaginaSEI::getInstance()->montarAncora($_GET['id_menu_peticionamento_usuario_externo']))).'\';" class="infraButton"><span class="infraTeclaAtalho">C</span>ancelar</button>';
			
			$objEditorRN=new EditorRN();
			$objEditorDTO=new EditorDTO();
			
			$objEditorDTO->setStrNomeCampo('txaConteudo');
			$objEditorDTO->setStrSinSomenteLeitura('N');
			$objEditorDTO->setNumTamanhoEditor(400);
			$retEditor = $objEditorRN->montarSimples($objEditorDTO);			
			
			$objMdPetMenuUsuarioExternoDTO2 = new MdPetMenuUsuarioExternoDTO();
			$objMdPetMenuUsuarioExternoDTO2->retTodos();

			$objMdPetMenuUsuarioExternoRN  = new MdPetMenuUsuarioExternoRN();
			
			if ( !isset($_POST['hdnIdMenuPeticionamentoUsuarioExterno'])) {
				$objMdPetMenuUsuarioExternoDTO2->setNumIdMenuPeticionamentoUsuarioExterno( $_GET['id_menu_peticionamento_usuario_externo'] );
				$objLista = $objMdPetMenuUsuarioExternoRN->consultar($objMdPetMenuUsuarioExternoDTO2);
				
				$txtNome = $objLista->getStrNome();
				$tipo = $objLista->getStrTipo();
				$txtConteudo = $objLista->getStrConteudoHtml();
				$txtUrl = $objLista->getStrUrl();
				$sinAtivo = $objLista->getStrSinAtivo();
			} else {
				try{

					$objMdPetMenuUsuarioExternoDTO2->setNumIdMenuPeticionamentoUsuarioExterno( $_POST['hdnIdMenuPeticionamentoUsuarioExterno'] );
					$objMdPetMenuUsuarioExternoDTO2 = $objMdPetMenuUsuarioExternoRN->consultar($objMdPetMenuUsuarioExternoDTO2);
					
					$txtNome = $_POST['txtNome'];
					$tipo = $_POST['tipo'];
					$txtConteudo = $_POST['txaConteudo'];
					$txtUrl = $_POST['txtUrl'];
					
					if( $_POST['tipo'] == MdPetMenuUsuarioExternoRN::$TP_EXTERNO ){
						$_POST['txaConteudo'] = '';
					}
					
					if( $_POST['tipo'] == MdPetMenuUsuarioExternoRN::$TP_CONTEUDO_HTML ){
						$_POST['txtUrl'] = '';
					}

					$objMdPetMenuUsuarioExternoDTO2->setNumIdMenuPeticionamentoUsuarioExterno( $_POST['hdnIdMenuPeticionamentoUsuarioExterno'] );
					$objMdPetMenuUsuarioExternoDTO2->setStrConteudoHtml($_POST['txaConteudo']);
					
					//Estilo
					$conjuntoEstilosRN = new ConjuntoEstilosRN();
			  		$conjuntoEstilosDTO = new ConjuntoEstilosDTO();
			  		$conjuntoEstilosDTO->setStrSinUltimo('S');
			  		$conjuntoEstilosDTO->retNumIdConjuntoEstilos();
			  		$conjuntoEstilosDTO = $conjuntoEstilosRN->consultar( $conjuntoEstilosDTO );
					$objMdPetMenuUsuarioExternoDTO2->setNumIdConjuntoEstilos( $conjuntoEstilosDTO->getNumIdConjuntoEstilos() );

					$objMdPetMenuUsuarioExternoDTO2->setStrUrl($_POST['txtUrl']);
					$objMdPetMenuUsuarioExternoDTO2->setStrNome($_POST['txtNome']);
					$objMdPetMenuUsuarioExternoDTO2->setStrTipo($_POST['tipo']);

					$objMdPetMenuUsuarioExternoDTO =  $objMdPetMenuUsuarioExternoRN->alterar($objMdPetMenuUsuarioExternoDTO2);

					header('Location: '.SessaoSEI::getInstance()->assinarLink('controlador.php?acao='.PaginaSEI::getInstance()->getAcaoRetorno().'&acao_origem='.$_GET['acao'] . '&id_menu_peticionamento_usuario_externo=' . $_POST['hdnIdMenuPeticionamentoUsuarioExterno']));
					
					die;
					
				} catch(Exception $e){
					PaginaSEI::getInstance()->processarExcecao($e);
				}
			}
			
			break;
			
		default:
			throw new InfraException("A��o '".$_GET['acao']."' n�o reconhecida.");
			break;
			 
	}
}
catch(Exception $e){
	PaginaSEI::getInstance()->processarExcecao($e);
}

PaginaSEI::getInstance()->montarDocType();
PaginaSEI::getInstance()->abrirHtml();
PaginaSEI::getInstance()->abrirHead();
PaginaSEI::getInstance()->montarMeta();
PaginaSEI::getInstance()->montarTitle(':: '.PaginaSEI::getInstance()->getStrNomeSistema().' - '.$strTitulo.' ::');
PaginaSEI::getInstance()->montarStyle();
EditorINT::montarCss();
PaginaSEI::getInstance()->abrirStyle();
?>
#lblNome {position:absolute;left:0%;top:0%;width:30%;}
#txtNome {position:absolute;left:0%;top:15px;width:30%;}
  		  		
#fldPeriodoIndisponibilidade {position:absolute;left:0%;top:50px;width:800px; height:50px;}
  		
#lblUrl {position:absolute;left:0%;top:125px;width:30%; display:none;}
#txtUrl {position:absolute;left:0%;top:140px;width:30%; display:none;}
  		
#lblConteudo {position:absolute;left:0%;top:125px;width:95%; display:none;}
#containerEditor {position:absolute;top:290px;width:870px; display:none;}

.cke_contents#cke_1_contents {height:490px !important;}

<?
PaginaSEI::getInstance()->fecharStyle();
PaginaSEI::getInstance()->montarJavaScript();
PaginaSEI::getInstance()->abrirJavaScript();
?>
  		
function rdTipo(){
     
     var externo = document.getElementById("tipoExterno").checked;
     var html = document.getElementById("tipoHTML").checked;
     
     if( externo ){
        
        //divInfraAreaDados
        document.getElementById("divInfraAreaDados").style.height = "17em";
        document.getElementById("divInfraAreaDados").style.overflow = "hidden";
        
        document.getElementById("lblUrl").style.display = 'block';
        document.getElementById("txtUrl").style.display = 'block';

        document.getElementById("lblConteudo").style.display = 'none';
        document.getElementById("tbConteudo").style.display = 'none';
        
     } else if( html ){
        
        document.getElementById("divInfraAreaDados").style.height = "14em";
        document.getElementById("divInfraAreaDados").style.overflow = "hidden";
        
        document.getElementById("lblUrl").style.display = 'none';
        document.getElementById("txtUrl").style.display = 'none';

        document.getElementById("lblConteudo").style.display = 'block';
        document.getElementById("tbConteudo").style.display = 'block';

        //limpa campo
        $("#tbConteudo  iframe").contents().find("body").html('')
     }

}
  		
function inicializar(){

	  infraEfeitoTabelas(); 

	   <? if( $tipo == 'E' || $tipo == 'H' ){ ?>
       rdTipo();
       <? } ?>
 
}
  
function OnSubmitForm(){
	var txtNome = document.getElementById('txtNome').value;
	var txtUrl = document.getElementById('txtUrl').value;
	var tipo = $("[name=tipo]input:checked").attr('value');
	var txtHTML = document.getElementById('txaConteudo').value;

	//nome e tipo sao obrigatorios
	if( txtNome == ''){
		alert('Informe o Nome do Menu.');
		document.getElementById('txtNome').focus();
		return false;
		
	} else if( txtNome.length > 30){
	   alert('Tamanho do campo excedido (m�ximo 30 caracteres).');
	   document.getElementById('txtNome').focus();
	   return false;
	} else if( tipo == ''){
		alert('Informe o Tipo de Menu.');
		return false;
	}else if ( tipo == 'E'){
		if( txtUrl == ''){
			alert('Informe a URL do Link Externo.');
			document.getElementById('txtUrl').focus();
			return false;
			
		}
		else if( txtUrl.length > 2083){
		    alert('Tamanho do campo excedido (m�ximo 2083 caracteres).');
		    document.getElementById('txtUrl').focus();
		    return false;
		}
	}else if( tipo == 'H'){
     	if (
     		$("#containerEditor  iframe").contents().find("body").html()=='<br>'
     		|| $("#containerEditor  iframe").contents().find("body").html()=='' 
     	){
			alert('Informe o Conte�do HTML.');
			return false;
     	}
	}
	return true;
}
<?php 
PaginaSEI::getInstance()->fecharJavaScript();
echo $retEditor->getStrInicializacao();
PaginaSEI::getInstance()->fecharHead();
PaginaSEI::getInstance()->abrirBody($strTitulo,'onload="inicializar();"');
?>
  
<form id="frmTextoPadraoInternoCadastro" method="post" onsubmit="return OnSubmitForm();" action="<?=PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?acao='.$_GET['acao'].'&acao_origem='.$_GET['acao']))?>">
<?
PaginaSEI::getInstance()->montarBarraComandosSuperior($arrComandos);

if( $tipo == 'E' ){  
  PaginaSEI::getInstance()->abrirAreaDados('17em');
} 
else if( $tipo == 'H' ){
 PaginaSEI::getInstance()->abrirAreaDados('14em;overflow:hidden');
} else {
 PaginaSEI::getInstance()->abrirAreaDados('17em');
}
?>

<label id="lblNome" for="txtNome" class="infraLabelObrigatorio">Nome do Menu: <img align="top" style="height:16px; width:16px;" src="<?= PaginaSEI::getInstance()->getDiretorioImagensGlobal() ?>/ajuda.gif" name="ajuda" <?= PaginaSEI::montarTitleTooltip('O menu ser� listado para o Usu�rio Externo depois que ele fizer login no Acesso Externo do SEI.')?> class="infraImg"/></label>
<input type="text" id="txtNome" name="txtNome" class="infraText" maxlength="30" <?= $disabled ?> value="<?= PaginaSEI::tratarHTML($txtNome) ?>">

 <fieldset id="fldPeriodoIndisponibilidade" class="infraFieldset sizeFieldset">
 
  	<legend class="infraLegend">&nbsp;Tipo de Menu &nbsp;</legend>
  	
  	<input type="radio" id="tipoExterno" <?= $disabled ?> name="tipo" value="E" onclick="rdTipo()" <?php if( $tipo == 'E' ){ echo " checked='checked' "; } ?> > <label for="tipoExterno" class="infraLabelRadio"> Link Externo <img align="top" style="height:16px; width:16px;" src="<?= PaginaSEI::getInstance()->getDiretorioImagensGlobal() ?>/ajuda.gif" name="ajuda" <?= PaginaSEI::montarTitleTooltip('O menu abrir� o link externo sempre em nova janela do navegador do Usu�rio Externo logado.')?> class="infraImg"/></label> <br/>
  	<input type="radio" id="tipoHTML" name="tipo" <?= $disabled ?> value="H" onclick="rdTipo()" <?php if( $tipo == 'H' ){ echo " checked='checked' "; } ?> > <label for="tipoHTML" class="infraLabelRadio"> Conte�do HTML <img align="top" style="height:16px; width:16px;" src="<?= PaginaSEI::getInstance()->getDiretorioImagensGlobal() ?>/ajuda.gif" name="ajuda" <?= PaginaSEI::montarTitleTooltip('O menu abrir� tela no pr�prio SEI para o Usu�rio Externo logado com o texto HTML parametrizado.')?> class="infraImg"/></label> <br/>
     
 </fieldset>

<label id="lblUrl" for="txtUrl" class="infraLabelObrigatorio">URL do Link Externo:</label>
<input type="text" id="txtUrl" name="txtUrl" maxlength="2083" class="infraText" <?= $disabled ?> value="<?= PaginaSEI::tratarHTML($txtUrl) ?>">

<label id="lblConteudo" for="txaConteudo" class="infraLabelObrigatorio">Conte�do HTML:</label>

<?php
PaginaSEI::getInstance()->fecharAreaDados();
?>
  
  <table id="tbConteudo" style="width: 100%; display: none;">
    <td style="width: 95%">
      <div id="divEditores" style="">
        <textarea id="txaConteudo" name="txaConteudo" <?= $disabled ?> class="infraTextarea" tabindex="<?=PaginaSEI::getInstance()->getProxTabDados()?>"><?=PaginaSEI::tratarHTML($txtConteudo)?></textarea>
        <script type="text/javascript">
          <?=$retEditor->getStrEditores();?>
        </script>
      </div>
    </td>
  </table>

  <input type="hidden" id="hdnIdMenuPeticionamentoUsuarioExterno" name="hdnIdMenuPeticionamentoUsuarioExterno" 
         value="<?php echo isset($_GET['id_menu_peticionamento_usuario_externo']) ? $_GET['id_menu_peticionamento_usuario_externo'] : '' ?>" />
    
  </form>
  
  <?
PaginaSEI::getInstance()->fecharBody();
PaginaSEI::getInstance()->fecharHtml();
?>