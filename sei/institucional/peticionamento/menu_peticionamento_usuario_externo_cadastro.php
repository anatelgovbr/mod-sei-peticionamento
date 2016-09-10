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
		
		case 'menu_peticionamento_usuario_externo_cadastrar':
			
			$strTitulo = 'Novo Menu';
			$arrComandos[] = '<button type="submit" accesskey="s" name="sbmCadastrarOrientacoesPetIndisp" value="Salvar" class="infraButton"><span class="infraTeclaAtalho">S</span>alvar</button>';
			$arrComandos[] = '<button type="button" accesskey="c" name="btnCancelar" id="btnCancelar" value="Cancelar" onclick="location.href=\''.PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?acao='.PaginaSEI::getInstance()->getAcaoRetorno().'&acao_origem='.$_GET['acao'])).'\';" class="infraButton"><span class="infraTeclaAtalho">C</span>ancelar</button>';

			$objEditorRN=new EditorRN();
			$objEditorDTO=new EditorDTO();
				
			$objEditorDTO->setStrNomeCampo('txaConteudo');
			$objEditorDTO->setStrSinSomenteLeitura('N');
			$objEditorDTO->setNumTamanhoEditor(220);
			$retEditor = $objEditorRN->montarSimples($objEditorDTO);
			
			$txtConteudo = $_POST['txaConteudo'];
			$txtUrl = $_POST['txtUrl'];
			$txtNome = $_POST['txtNome'];
			$tipo = $_POST['tipo'];
			
			$objMenuPeticionamentoUsuarioExternoDTO = new MenuPeticionamentoUsuarioExternoDTO();
			$objMenuPeticionamentoUsuarioExternoDTO->setStrConteudoHtml('');
			
			if (isset($_POST['hdnIdMenuPeticionamentoUsuarioExterno'])) {
				
				try{
					
					if( $_POST['tipo'] == MenuPeticionamentoUsuarioExternoRN::$TP_EXTERNO ){
						$_POST['txaConteudo'] = '';
					}
						
					if( $_POST['tipo'] == MenuPeticionamentoUsuarioExternoRN::$TP_CONTEUDO_HTML ){
						$_POST['txtUrl'] = '';
					}
					
					$objMenuPeticionamentoUsuarioExternoDTO->setStrConteudoHtml($_POST['txaConteudo']);
					$objMenuPeticionamentoUsuarioExternoDTO->setStrUrl($_POST['txtUrl']);
					$objMenuPeticionamentoUsuarioExternoDTO->setStrNome($_POST['txtNome']);
					$objMenuPeticionamentoUsuarioExternoDTO->setStrTipo($_POST['tipo']);
					$objMenuPeticionamentoUsuarioExternoRN  = new MenuPeticionamentoUsuarioExternoRN();
					$objMenuPeticionamentoUsuarioExternoDTO = $objMenuPeticionamentoUsuarioExternoRN->cadastrar($objMenuPeticionamentoUsuarioExternoDTO);					
					header('Location: '.SessaoSEI::getInstance()->assinarLink('controlador.php?acao='.PaginaSEI::getInstance()->getAcaoRetorno().'&acao_origem='.$_GET['acao'].'&id_menu_peticionamento_usuario_externo='.$objMenuPeticionamentoUsuarioExternoDTO->getNumIdMenuPeticionamentoUsuarioExterno()));
					die;
					
				} catch(Exception $e){
					PaginaSEI::getInstance()->processarExcecao($e);
				}
			}
			break;
		
		case 'menu_peticionamento_usuario_externo_alterar':
		case 'menu_peticionamento_usuario_externo_consultar':
			
			$disabled = '';
			
			if( $_GET['acao'] == "menu_peticionamento_usuario_externo_alterar" ){
				$strTitulo = 'Alterar Menu';
				$disabled = '';
			}
			
			else if( $_GET['acao'] == "menu_peticionamento_usuario_externo_consultar" ){
				$strTitulo = 'Consultar Menu';
				$disabled = " disabled='disabled' ";
			}

			if ($_GET['acao']=='menu_peticionamento_usuario_externo_alterar'){
				$arrComandos[] = '<button type="submit" accesskey="s" name="sbmCadastrarOrientacoesPetIndisp" value="Salvar" class="infraButton"><span class="infraTeclaAtalho">S</span>alvar</button>';
			}

			//TODO: Marcelo ou Herley, a construção dos Cases Alterar e Consultar desta funcionalidade ficou muito diferente da forma que foi construído para Tipos de Processos para Peticionamento e para Indisponibilidades do SEI. Tem que padronizar, para ficar igual as outras duas funcionalidades. Ainda, Consultar tem o botão "Fechar", enquanto que Novo e Alterar tem o botão "Cancelar".
			$arrComandos[] = '<button type="button" accesskey="c" name="btnCancelar" id="btnCancelar" value="Cancelar" onclick="location.href=\''.PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?acao='.PaginaSEI::getInstance()->getAcaoRetorno().'&acao_origem='.$_GET['acao'].PaginaSEI::getInstance()->montarAncora($_GET['id_menu_peticionamento_usuario_externo']))).'\';" class="infraButton">Fe<span class="infraTeclaAtalho">c</span>har</button>';
			
			$objEditorRN=new EditorRN();
			$objEditorDTO=new EditorDTO();
			
			$objEditorDTO->setStrNomeCampo('txaConteudo');
			$objEditorDTO->setStrSinSomenteLeitura('N');
			$objEditorDTO->setNumTamanhoEditor(220);
			$retEditor = $objEditorRN->montarSimples($objEditorDTO);			
			
			$objMenuPeticionamentoUsuarioExternoDTO2 = new MenuPeticionamentoUsuarioExternoDTO();
			//$objMenuPeticionamentoUsuarioExternoDTO2->setNumIdTipoProcessoOrientacoesPeticionamento(MenuPeticionamentoUsuarioExternoRN::$ID_FIXO_TP_PROCESSO);
			$objMenuPeticionamentoUsuarioExternoDTO2->retTodos();
			
			$objMenuPeticionamentoUsuarioExternoRN  = new MenuPeticionamentoUsuarioExternoRN();
			
			if ( !isset($_POST['hdnIdMenuPeticionamentoUsuarioExterno'])) {
				$objMenuPeticionamentoUsuarioExternoDTO2->setNumIdMenuPeticionamentoUsuarioExterno( $_GET['id_menu_peticionamento_usuario_externo'] );
				$objLista = $objMenuPeticionamentoUsuarioExternoRN->consultar($objMenuPeticionamentoUsuarioExternoDTO2);
				
				$txtNome = $objLista->getStrNome();
				$tipo = $objLista->getStrTipo();
				$txtConteudo = $objLista->getStrConteudoHtml();
				$txtUrl = $objLista->getStrUrl();
				$sinAtivo = $objLista->getStrSinAtivo();
			} else {
				try{
			
					$objMenuPeticionamentoUsuarioExternoDTO2->setNumIdMenuPeticionamentoUsuarioExterno( $_POST['hdnIdMenuPeticionamentoUsuarioExterno'] );
					$objMenuPeticionamentoUsuarioExternoDTO2 = $objMenuPeticionamentoUsuarioExternoRN->consultar($objMenuPeticionamentoUsuarioExternoDTO2);
					
					$txtNome = $_POST['txtNome'];
					$tipo = $_POST['tipo'];
					$txtConteudo = $_POST['txaConteudo'];
					$txtUrl = $_POST['txtUrl'];
					
					if( $_POST['tipo'] == MenuPeticionamentoUsuarioExternoRN::$TP_EXTERNO ){
						$_POST['txaConteudo'] = '';
					}
					
					if( $_POST['tipo'] == MenuPeticionamentoUsuarioExternoRN::$TP_CONTEUDO_HTML ){
						$_POST['txtUrl'] = '';
					}
					
					//$sinAtivo = $objMenuPeticionamentoUsuarioExternoDTO2->getStrSinAtivo();
					
					$objMenuPeticionamentoUsuarioExternoDTO2->setNumIdMenuPeticionamentoUsuarioExterno( $_POST['hdnIdMenuPeticionamentoUsuarioExterno'] );
					$objMenuPeticionamentoUsuarioExternoDTO2->setStrConteudoHtml($_POST['txaConteudo']);
					$objMenuPeticionamentoUsuarioExternoDTO2->setStrUrl($_POST['txtUrl']);
					$objMenuPeticionamentoUsuarioExternoDTO2->setStrNome($_POST['txtNome']);
					$objMenuPeticionamentoUsuarioExternoDTO2->setStrTipo($_POST['tipo']);			
											
					$objMenuPeticionamentoUsuarioExternoDTO =  $objMenuPeticionamentoUsuarioExternoRN->alterar($objMenuPeticionamentoUsuarioExternoDTO2);
					
					//PaginaSEI::getInstance()->setStrMensagem('Assunto "'.$objAssuntoDTO->getStrCodigoEstruturado().'" alterado com sucesso.');
					header('Location: '.SessaoSEI::getInstance()->assinarLink('controlador.php?acao='.PaginaSEI::getInstance()->getAcaoRetorno().'&acao_origem='.$_GET['acao'] . '&id_menu_peticionamento_usuario_externo=' . $_POST['hdnIdMenuPeticionamentoUsuarioExterno']));
					
					die;
					
				} catch(Exception $e){
					PaginaSEI::getInstance()->processarExcecao($e);
				}
			}
			
			break;
		
		default:
			throw new InfraException("Ação '".$_GET['acao']."' não reconhecida.");
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
  		
/*
#txaConteudo { display:none; }  		
.cke_contents#cke_1_contents {left:0%; height:290px !important; display:none;}
*/
  		
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
        
        //document.getElementById("txaConteudo").style.display = 'none';
        document.getElementById("lblConteudo").style.display = 'none';
        document.getElementById("containerEditor").style.display = 'none';        
        
     } else if( html ){
        
        document.getElementById("divInfraAreaDados").style.height = "14em";
        document.getElementById("divInfraAreaDados").style.overflow = "hidden";
        
        document.getElementById("lblUrl").style.display = 'none';
        document.getElementById("txtUrl").style.display = 'none';
        
        //document.getElementById("txaConteudo").style.display = 'block';
        document.getElementById("lblConteudo").style.display = 'block';
         document.getElementById("containerEditor").style.display = 'block'; 
         
         //limpa campo
         $("#containerEditor  iframe").contents().find("body").html('')    
     }
     
}
  		
function inicializar(){
  
	  if ('<?=$_GET['acao']?>'=='menu_peticionamento_usuario_externo_cadastrar'){
	  
	    //window.onload = document.getElementById('cke_1_contents').style.height = '290px';
	    //document.getElementById('txtNome').focus();
	  } 
	  //document.getElementById('btnCancelar').focus();
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
	   alert('Tamanho do campo excedido (máximo 30 caracteres).');
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
		    alert('Tamanho do campo excedido (máximo 2083 caracteres).');
		    document.getElementById('txtUrl').focus();
		    return false;
		}
	}else if( tipo == 'H'){
     	if (
     		$("#containerEditor  iframe").contents().find("body").html()=='<br>'
     		|| $("#containerEditor  iframe").contents().find("body").html()=='' 
     	){
			alert('Informe o Conteúdo HTML.');
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
//PaginaSEI::getInstance()->montarAreaValidacao();

if( $tipo == 'E' ){  
  PaginaSEI::getInstance()->abrirAreaDados('17em');
} 
else if( $tipo == 'H' ){
 PaginaSEI::getInstance()->abrirAreaDados('14em;overflow:hidden');
} else {
 PaginaSEI::getInstance()->abrirAreaDados('17em');
}
?>

<label id="lblNome" for="txtNome" class="infraLabelObrigatorio">Nome do Menu:</label>
<input type="text" id="txtNome" name="txtNome" class="infraText" maxlength="30" <?= $disabled ?> value="<?= $txtNome ?>">

 <fieldset id="fldPeriodoIndisponibilidade" class="infraFieldset sizeFieldset">
 
  	<legend class="infraLegend">&nbsp;Tipo de Menu &nbsp;</legend>
  	
  	<input type="radio" id="tipoExterno" <?= $disabled ?> name="tipo" value="E" onclick="rdTipo()" <?php if( $tipo == 'E' ){ echo " checked='checked' "; } ?> > <label for="tipoExterno" class="infraLabelRadio"> Link externo </label> <br/>
  	<input type="radio" id="tipoHTML" name="tipo" <?= $disabled ?> value="H" onclick="rdTipo()" <?php if( $tipo == 'H' ){ echo " checked='checked' "; } ?> > <label for="tipoHTML" class="infraLabelRadio"> Conteúdo HTML </label> <br/>
     
 </fieldset>

<label id="lblUrl" for="txtUrl" class="infraLabelObrigatorio">URL do Link Externo:</label>
<input type="text" id="txtUrl" name="txtUrl" maxlength="2083" class="infraText" <?= $disabled ?> value="<?= $txtUrl ?>">

<label id="lblConteudo" for="txaConteudo" class="infraLabelObrigatorio">Conteúdo HTML:</label>

<?php 
PaginaSEI::getInstance()->fecharAreaDados();
?>
  
  <div id="containerEditor">
  <table id="tbConteudo" style="width: 95%;">
    <td style="width: 95%">
      <div id="divEditores" style="overflow: auto;">
        <textarea id="txaConteudo" name="txaConteudo" <?= $disabled ?> rows="4" class="infraTextarea" tabindex="<?=PaginaSEI::getInstance()->getProxTabDados()?>"><?=$txtConteudo?></textarea>
        <script type="text/javascript">
          <?=$retEditor->getStrEditores();?>
        </script>
      </div>
    </td>
  </table>
  </div>
  
  <input type="hidden" id="hdnIdMenuPeticionamentoUsuarioExterno" name="hdnIdMenuPeticionamentoUsuarioExterno" 
         value="<?php echo isset($_GET['id_menu_peticionamento_usuario_externo']) ? $_GET['id_menu_peticionamento_usuario_externo'] : '' ?>" />
    
  </form>
  
  <?
PaginaSEI::getInstance()->fecharBody();
PaginaSEI::getInstance()->fecharHtml();
?>