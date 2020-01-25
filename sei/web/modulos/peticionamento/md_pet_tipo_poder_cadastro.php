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

	SessaoSEI::getInstance()->validarPermissao($_GET['acao']);

	$arrComandos = array();

	$disabled = '';

	?>
	<script type="text/javascript">
        var valor = "";
        function inicializar(){
            preencherCampo();
            infraEfeitoTabelas();

        }
    </script>
    <?php
	switch($_GET['acao']){
		
		case 'md_pet_tipo_poder_cadastrar':
			
			$strTitulo = 'Novo Tipo de Poder Legal';
			$arrComandos[] = '<button type="submit" accesskey="s" name="sbmCadastrarTpPoder" value="Salvar" class="infraButton"><span class="infraTeclaAtalho">S</span>alvar</button>';
			$arrComandos[] = '<button type="button" accesskey="c" name="btnCancelar" id="btnCancelar" value="Cancelar" onclick="location.href=\''.PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?acao='.PaginaSEI::getInstance()->getAcaoRetorno().'&acao_origem='.$_GET['acao'])).'\';" class="infraButton"><span class="infraTeclaAtalho">C</span>ancelar</button>';

			$objEditorRN=new EditorRN();
			$objEditorDTO=new EditorDTO();
				
			$objEditorDTO->setStrNomeCampo('txaConteudo');
			$objEditorDTO->setStrSinSomenteLeitura('N');
			$objEditorDTO->setNumTamanhoEditor(220);
            $retEditor = $objEditorRN->montarSimples($objEditorDTO);
            
            //Cadastrando tipo de poder
            if(isset($_POST['txtNome'])){
            $objMdPetTipoPoderLegalDTO = new MdPetTipoPoderLegalDTO();
            $objMdPetTipoPoderLegalDTO->retNumIdTipoPoderLegal();
            $objMdPetTipoPoderLegalDTO->setStrNome($_POST['txtNome']);
            $objMdPetTipoPoderLegalDTO->setDtaDtaCadastro(InfraData::getStrDataHoraAtual());
            $objMdPetTipoPoderLegalDTO->setStrStaSistema(null);
            $objMdPetTipoPoderLegalDTO->setStrSinAtivo('S');
            $objMdPetTipoPoderLegalRN = new MdPetTipoPoderLegalRN();
            $arrObjMdPetTipoPoderLegalDTO = $objMdPetTipoPoderLegalRN->cadastrar($objMdPetTipoPoderLegalDTO);
            header('Location: ' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . PaginaSEI::getInstance()->getAcaoRetorno() . '&acao_origem=' . $_GET['acao'] . '&id_md_pet_tipo_poder='.$arrObjMdPetTipoPoderLegalDTO->getNumIdTipoPoderLegal()));
           
            }
            
            
			break;
		
		case 'md_pet_tipo_poder_consultar':
            $disabled = "disabled";
            $idPoder = $_GET['IdTipoPoderLegal'];
            
            //Recuperando o Poder pelo ID e setando no campo de texto 'nome'.
            $objMdPetTipoPoderLegalDTO = new MdPetTipoPoderLegalDTO();
            $objMdPetTipoPoderLegalDTO->retTodos(true);
            $objMdPetTipoPoderLegalDTO->setNumIdTipoPoderLegal($idPoder);
            $objMdPetTipoPoderLegalRN = new MdPetTipoPoderLegalRN();
            $arrObjMdPetTipoPoderLegalDTO = $objMdPetTipoPoderLegalRN->consultar($objMdPetTipoPoderLegalDTO);
            $txtNome = $arrObjMdPetTipoPoderLegalDTO->getStrNome();

			$strTitulo = 'Consultar Tipo de Poder Legal';
		
			$arrComandos[] = '<button type="button" accesskey="c" name="btnFechar" id="btnFechar" value="Fechar" onclick="location.href=\''.PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?acao='.PaginaSEI::getInstance()->getAcaoRetorno().'&acao_origem='.$_GET['acao'].PaginaSEI::getInstance()->montarAncora($_GET['IdTipoPoderLegal']))).'\';" class="infraButton">Fe<span class="infraTeclaAtalho">c</span>har</button>';
			$objEditorRN=new EditorRN();
			$objEditorDTO=new EditorDTO();
			
			$objEditorDTO->setStrNomeCampo('txaConteudo');
			$objEditorDTO->setStrSinSomenteLeitura('N');
			$objEditorDTO->setNumTamanhoEditor(220);
			$retEditor = $objEditorRN->montarSimples($objEditorDTO);			
			
            
            
			break;
		
		case 'md_pet_tipo_poder_alterar':
			
			
			$strTitulo = 'Alterar Tipo de Poder Legal';
            $idPoder = $_GET['IdTipoPoderLegal'];

			$arrComandos[] = '<button type="submit" accesskey="s" name="sbmAlterarTpPoder" value="Salvar" class="infraButton"><span class="infraTeclaAtalho">S</span>alvar</button>';
            $arrComandos[] = '<button type="button" accesskey="C" name="btnCancelar" id="btnCancelar" value="Cancelar" onclick="location.href=\'' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . PaginaSEI::getInstance()->getAcaoRetorno() . '&acao_origem=' . $_GET['acao'] . PaginaSEI::getInstance()->montarAncora($_GET['IdTipoPoderLegal'])) . '\';" class="infraButton"><span class="infraTeclaAtalho">C</span>ancelar</button>';
			
			$objEditorRN=new EditorRN();
			$objEditorDTO=new EditorDTO();
			
			$objEditorDTO->setStrNomeCampo('txaConteudo');
			$objEditorDTO->setStrSinSomenteLeitura('N');
			$objEditorDTO->setNumTamanhoEditor(220);
            $retEditor = $objEditorRN->montarSimples($objEditorDTO);
            
            if(isset($idPoder)){
            //Recuperando o Poder pelo ID e setando no campo de texto 'nome'.
            $objMdPetTipoPoderLegalDTO = new MdPetTipoPoderLegalDTO();
            $objMdPetTipoPoderLegalDTO->retTodos(true);
            $objMdPetTipoPoderLegalDTO->setNumIdTipoPoderLegal($idPoder);
            $objMdPetTipoPoderLegalRN = new MdPetTipoPoderLegalRN();
            $arrObjMdPetTipoPoderLegalDTO = $objMdPetTipoPoderLegalRN->consultar($objMdPetTipoPoderLegalDTO);
            $txtNome = $arrObjMdPetTipoPoderLegalDTO->getStrNome();
            }

            //Cadastrando tipo de poder
            if(isset($_POST['txtNome'])){
                
                $objMdPetTipoPoderLegalDTO = new MdPetTipoPoderLegalDTO();
                $objMdPetTipoPoderLegalDTO->retNumIdTipoPoderLegal();
                $objMdPetTipoPoderLegalDTO->setNumIdTipoPoderLegal($_POST['hdnIdTpPoder']);
                $objMdPetTipoPoderLegalDTO->setStrNome($_POST['txtNome']);
                $objMdPetTipoPoderLegalDTO->setDtaDtaCadastro(InfraData::getStrDataHoraAtual());
                $objMdPetTipoPoderLegalDTO->setStrStaSistema(null);
                $objMdPetTipoPoderLegalRN = new MdPetTipoPoderLegalRN();
                $arrObjMdPetTipoPoderLegalDTO = $objMdPetTipoPoderLegalRN->alterar($objMdPetTipoPoderLegalDTO);
                if($arrObjMdPetTipoPoderLegalDTO) {
                    header('Location: ' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . PaginaSEI::getInstance()->getAcaoRetorno() . '&acao_origem=' . $_GET['acao'] . PaginaSEI::getInstance()->montarAncora($_POST['hdnIdTpPoder'])));
                }
            }
			
			
			break;
			
		default:
			throw new InfraException("Ação '".$_GET['acao']."' não reconhecida.");
			break;
			 
	}
}
catch(Exception $e){
    $texto = $_POST['txtNome'];
    echo "<script type='text/javascript' >valor = '{$texto}'; inicializar();</script>";
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


function preencherCampo(){
    if(document.getElementById('txtNome').value == ''){
        document.getElementById('txtNome').value = valor;
    }
}

function OnSubmitForm(){
	var txtNome = document.getElementById('txtNome').value;
	if( txtNome == ''){
		alert('Informe o Nome.');
		document.getElementById('txtNome').focus();
		return false;
		
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
<label id="lblNome" for="txtNome" class="infraLabelObrigatorio" style="line-height:12px;">Nome:</label><br>
<input type="text" id="txtNome" name="txtNome" onkeypress="return infraMascaraTexto(this,event,100);" maxlength="100" class="infraText" maxlength="30" <?= $disabled ?> value="<?= PaginaSEI::tratarHTML($txtNome) ?>">

<?php
PaginaSEI::getInstance()->fecharAreaDados();
?>
  
  <table id="tbConteudo" style="width: 100%; display: none;">
    <td style="width: 95%">
      <div id="divEditores" style="">
        <textarea id="txaConteudo" name="txaConteudo" <?= $disabled ?> rows="20" class="infraTextarea" tabindex="<?=PaginaSEI::getInstance()->getProxTabDados()?>"><?=PaginaSEI::tratarHTML($txtConteudo)?></textarea>
        <script type="text/javascript">
          <?=$retEditor->getStrEditores();?>
        </script>
      </div>
    </td>
  </table>

  <input type="hidden" id="hdnIdMenuPeticionamentoUsuarioExterno" name="hdnIdMenuPeticionamentoUsuarioExterno" 
         value="<?php echo isset($_GET['id_menu_peticionamento_usuario_externo']) ? $_GET['id_menu_peticionamento_usuario_externo'] : '' ?>" />
    
         <input type="hidden" id="hdnIdTpPoder" name="hdnIdTpPoder" 
         value="<?php echo $idPoder ?>" />
  </form>
  
  <?
PaginaSEI::getInstance()->fecharBody();
PaginaSEI::getInstance()->fecharHtml();
?>