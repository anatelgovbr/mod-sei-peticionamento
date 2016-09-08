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

  SessaoSEI::getInstance()->validarLink();

  PaginaSEI::getInstance()->verificarSelecao('hipotese_legal_nl_acesso_peticionamento_cadastrar');

  SessaoSEI::getInstance()->validarPermissao($_GET['acao']);

  $objHipoteseLegal = new HipoteseLegalDTO();

  $strDesabilitar = '';

  $arrComandos = array();
  $strLinkHipoteseLglSelecao = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=hipotese_legal_peticionamento_selecionar&tipo_selecao=2&id_object=objLupaHipLegal&nvl_acesso='.ProtocoloRN::$NA_RESTRITO);
  $strLinkAjaxHipLegal       = SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=hipotese_legal_rest_peticionamento_auto_completar');
  
  switch($_GET['acao']){
    case 'hipotese_legal_nl_acesso_peticionamento_cadastrar':

      $strTitulo = 'Peticionamento - Hipóteses Legais Permitidas';

      $arrComandos[] = '<button type="submit" accesskey="S" name="sbmCadastrarHipoteseLegalRI" id="sbmCadastrarHipoteseLegalRI" value="Salvar" class="infraButton"><span class="infraTeclaAtalho">S</span>alvar</button>';
      $arrComandos[] = '<button type="button" accesskey="C" name="btnFechar" id="btnFechar" value="Cancelar" onclick="location.href=\''.PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?acao='.PaginaSEI::getInstance()->getAcaoRetorno().'&acao_origem='.$_GET['acao'])).'\';" class="infraButton"><span class="infraTeclaAtalho">F</span>echar</button>';

      $objHipoteseLegalPeticionamentoRN = new HipoteseLegalPeticionamentoRN();
      $objHipoteseLegalPeticionamentoDTOAll = new HipoteseLegalPeticionamentoDTO();
      $objHipoteseLegalPeticionamentoDTOAll->retTodos();
      $objHipoteseLegalPeticionamentoDTOAll->retStrNome();
      $objHipoteseLegalPeticionamentoDTOAll->retStrBaseLegal();
      $objHipoteseLegalPeticionamentoDTOAll->retStrSinAtivo();
      $objHipoteseLegalPeticionamentoDTOAll->setStrSinAtivo('S');
      $qtdHipLgl = $objHipoteseLegalPeticionamentoRN->contar($objHipoteseLegalPeticionamentoDTOAll);
      
	  $strItensSelHipLegal = "";
      $alterar = false;
	  $alterar = $qtdHipLgl > 0 ? true : false;
	  
	  if($alterar){
	  	$arrHipotesesLegais = $objHipoteseLegalPeticionamentoRN->listar($objHipoteseLegalPeticionamentoDTOAll);
	  	for($x = 0;$x<count($arrHipotesesLegais);$x++){
	  		$strItensSelHipLegal .= "<option value='" . $arrHipotesesLegais[$x]->getNumIdHipoteseLegalPeticionamento() .  "'>" . $arrHipotesesLegais[$x]->getStrNome().' ('.$arrHipotesesLegais[$x]->getStrBaseLegal().')'. "</option>";
	  	}
	  }

      if (isset($_POST['sbmCadastrarHipoteseLegalRI'])) {
        try{
        		$arrObjHipoteseLegalDTOCad = array();
        		$arrHipotesesLegais = PaginaSEI::getInstance()->getArrValuesSelect($_POST['hdnHipoteseLgl']);
        	
        		for($x = 0;$x<count($arrHipotesesLegais);$x++){
        			$objHipoteseLegalPeticionamentoDTO = new HipoteseLegalPeticionamentoDTO();
        			$objHipoteseLegalPeticionamentoDTO->setNumIdHipoteseLegalPeticionamento($arrHipotesesLegais[$x]);
        			array_push( $arrObjHipoteseLegalDTOCad, $objHipoteseLegalPeticionamentoDTO );
        		}
         
          if($alterar){
          	    $objHipoteseLegalPeticionamentoDTO = new HipoteseLegalPeticionamentoDTO();
          		$objHipoteseLegalPeticionamentoDTO->retTodos();
          	
          		$objHipoteseLegalPeticionamentoDTO->setStrSinAtivo('S');
          	
          		$arrObjHipoteseLegalDTOExcluir = array();
          		$arrObjHipoteseLegalDTOExcluir = $objHipoteseLegalPeticionamentoRN->listar($objHipoteseLegalPeticionamentoDTO);
          	
          		if(count($arrObjHipoteseLegalDTOExcluir) > 0){
          			$objHipoteseLegalPeticionamentoRN->excluir($arrObjHipoteseLegalDTOExcluir);
          		}
           }		
        		
          $objHipoteseLegalPeticionamentoRN->cadastrar($arrObjHipoteseLegalDTOCad);
          
          header('Location: '.SessaoSEI::getInstance()->assinarLink('controlador.php?acao='.PaginaSEI::getInstance()->getAcaoRetorno().'&acao_origem='.$_GET['acao']));
          die;
        }catch(Exception $e){
          PaginaSEI::getInstance()->processarExcecao($e);
        }
      }
      break;

    default:
      throw new InfraException("Ação '".$_GET['acao']."' não reconhecida.");
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
$browser = $_SERVER['HTTP_USER_AGENT'];
$firefox = strpos($browser, 'Firefox') ? true : false;
?>
#lblNome {position:absolute;left:0%;top:0%;width:50%;}

#lblDescricaoSubtema {position:absolute;left:0%;width:50%;}


<?php if($firefox){?>
#selDescricaoHpLegalNvAcesso {width:75%;margin-top:2.7%}
#imgLupaHipoteseLgl {position:absolute;left:75.5%;top:40.5%;}
#imgExcluirHipoteseLgl  {position:absolute;left:75.2%;top:60%;}
#txtHipoteseLgl {position:absolute;left:0%;width:50%;margin-top:1.6%}
#lblHipoteseLgl{}
<?php }else{?>
#selDescricaoHpLegalNvAcesso {width:75%;margin-top:2.6%}
#imgLupaHipoteseLgl {position:absolute;left:75.5%;top:40.2%;}
#imgExcluirHipoteseLgl  {position:absolute;left:75.2%;top:58%;}
#txtHipoteseLgl {position:absolute;left:0%;width:50%;margin-top:1.6%}
#lblHipoteseLgl{}
<?php } ?>

<?
PaginaSEI::getInstance()->fecharStyle();
PaginaSEI::getInstance()->montarJavaScript();
PaginaSEI::getInstance()->abrirJavaScript();
?>


<?
PaginaSEI::getInstance()->fecharJavaScript();
PaginaSEI::getInstance()->fecharHead();
PaginaSEI::getInstance()->abrirBody($strTitulo,'onload="inicializar();"');
?>
<form id="frmHipLglNlAccPermCadastro" method="post" onsubmit="return OnSubmitForm();" 
action="<?=PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?acao='.$_GET['acao'].'&acao_origem='.$_GET['acao']))?>">
<?
PaginaSEI::getInstance()->montarBarraComandosSuperior($arrComandos);
PaginaSEI::getInstance()->abrirAreaDados('30em');
?>

 
 <div style="clear: both"></div>
  
  <div id="hipLegalNvlAcessoAssociada" class="infraAreaDados">  
  
   <label id="lblHipoteseLgl" for="txtHipoteseLgl" accesskey="n" class="infraLabelObrigatorio">Hipóteses Legais:</label>
  <input type="text" id="txtHipoteseLgl" name="txtHipoteseLgl" class="infraText" tabindex="<?=PaginaSEI::getInstance()->getProxTabDados()?>" />
  
  <br/>
  
  <select id="selDescricaoHpLegalNvAcesso" name="selDescricaoHpLegalNvAcesso" size="4" multiple="multiple" class="infraSelect">
   <?=$strItensSelHipLegal?>
  </select>
		    
  <img id="imgLupaHipoteseLgl" onclick="objLupaHipLegal.selecionar(700,500);" src="/infra_css/imagens/lupa.gif" 
	    alt="Selecionar Hipóteses Legais" 
	    title="Selecionar Hipóteses Legais" class="infraImg" />	
	  
  <img id="imgExcluirHipoteseLgl" onclick="objLupaHipLegal.remover();" src="/infra_css/imagens/remover.gif" 
	    alt="Remover Hipóteses Legais Selecionadas" 
	    title="Remover Hipóteses Legais Selecionados" class="infraImg" />  
	  
  <input type="hidden" id="hdnHipoteseLgl" name="hdnHipoteseLgl" value="<?=$_POST['hdnHipoteseLgl']?>" />
  <input type="hidden" id="hdnIdHipoteseLgl" name="hdnIdHipoteseLgl" value="<?=$_POST['hdnIdHipoteseLgl']?>" />
	</div>

  <input type="hidden" id="hdnHipoteseLglRI" name="hdnHipoteseLglRI" value="<?= $_POST['hdnHipoteseLglRI']?>" />
  <?
  PaginaSEI::getInstance()->fecharAreaDados();
  ?>
</form>
<?
PaginaSEI::getInstance()->fecharBody();
PaginaSEI::getInstance()->fecharHtml();
?>

<script type="text/javascript">
function inicializar(){
if ('<?=$_GET['acao']?>'!='hipotese_legal_nl_acesso_peticionamento_consultar'){
	carregarComponenteHipoteseLegal();
  }
  if ('<?=$_GET['acao']?>'=='hipotese_legal_nl_acesso_peticionamento_cadastrar'){
    document.getElementById('txtHipoteseLgl').focus();
  } else if ('<?=$_GET['acao']?>'=='hipotese_legal_nl_acesso_peticionamento_consultar'){
    infraDesabilitarCamposAreaDados();
  }else{
    document.getElementById('btnFechar').focus();
  }
  infraEfeitoTabelas();
}

function validarCadastro() {

  var optionsSub = document.getElementById('selDescricaoHpLegalNvAcesso').options;
  
  if( optionsSub.length == 0 ){
    alert('Informe ao menos uma Hipótese Legal.');
    document.getElementById('selDescricaoHpLegalNvAcesso').focus();
    return false;
  } 
  
  return true;
}


function OnSubmitForm() {
  return validarCadastro();
}

function carregarComponenteHipoteseLegal(){
	
	objAutoCompletarHipLegal = new infraAjaxAutoCompletar('hdnIdHipoteseLgl', 'txtHipoteseLgl', '<?=$strLinkAjaxHipLegal?>');
	objAutoCompletarHipLegal.limparCampo = true;
	
	objAutoCompletarHipLegal.prepararExecucao = function(){
	    return 'palavras_pesquisa='+document.getElementById('txtHipoteseLgl').value;
	};
	  
	objAutoCompletarHipLegal.processarResultado = function(id,nome,complemento){
	    
	    if (id!=''){
	      var options = document.getElementById('selDescricaoHpLegalNvAcesso').options;

	      if(options != null){
	      for(var i=0;i < options.length;i++){
	        if (options[i].value == id){
	          alert('Hipótese Legal já consta na lista.');
	          break;
	        }
	      }
	      }
	      
	      if (i==options.length){
	      
	        for(i=0;i < options.length;i++){
	         options[i].selected = false; 
	        }
	      
	        opt = infraSelectAdicionarOption(document.getElementById('selDescricaoHpLegalNvAcesso'),nome,id);
	        
	        objLupaHipLegal.atualizar();
	        
	        opt.selected = true;
	      }
	                  
	      document.getElementById('txtHipoteseLgl').value = '';
	      document.getElementById('txtHipoteseLgl').focus();
	      
	    }
	  };
    
	  objLupaHipLegal = new infraLupaSelect('selDescricaoHpLegalNvAcesso' , 'hdnHipoteseLgl',  '<?=$strLinkHipoteseLglSelecao?>'); 
		   	
} 

</script>