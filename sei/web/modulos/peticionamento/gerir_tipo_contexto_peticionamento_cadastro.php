<?
/*
 * @author Marcelo Bezerra <marcelo.bezerra@castgroup.com.br>
 * 
 * */

try {
	
	require_once dirname(__FILE__).'/../../SEI.php';

	session_start();	
	SessaoSei::getInstance()->validarLink();
	SessaoSei::getInstance()->validarPermissao($_GET['acao']);
	
	}catch(Exception $e){
		PaginaSEI::getInstance()->processarExcecao($e);
	}
		
	$objRN = new GerirTipoContextoPeticionamentoRN();
	
    if( isset( $_POST['hdnPrincipal'] ) && $_POST['hdnPrincipal'] != "") {
		    	
		$objInfraException = new InfraException();
			
		// excluindo registros anteriores
		$objDTO = new RelTipoContextoPeticionamentoDTO();
		$objDTO->retTodos();
		$objDTO->setStrSinCadastroInteressado('S');
		$objDTO->setStrSinSelecaoInteressado('N');
		$objRN->excluir($objRN->listar($objDTO));
				
		$arrPrincipal = PaginaSEI::getInstance()->getArrValuesSelect($_POST['hdnPrincipal']);
		
		if(!$arrPrincipal) {
			$objInfraException->adicionarValidacao('Informe pelo menos um tipo de interessado.');
		}
	
		$objInfraException->lancarValidacoes();
				
		foreach($arrPrincipal as $numPrincipal){
			$objDTO = new RelTipoContextoPeticionamentoDTO();
			$objDTO->setNumIdTipoContextoContato($numPrincipal);
			$objDTO->setStrSinCadastroInteressado('S');
			$objDTO->setStrSinSelecaoInteressado('N');
			$objDTO = $objRN->cadastrar($objDTO);
		}
	
   }
   
   if( isset( $_POST['hdnPrincipal2'] ) && $_POST['hdnPrincipal2'] != "") {
   
	   	$objInfraException = new InfraException();
	   		
	   	// excluindo registros anteriores
	   	$objDTO2 = new RelTipoContextoPeticionamentoDTO();
	   	$objDTO2->retTodos();
	   	$objDTO2->setStrSinCadastroInteressado('N');
	   	$objDTO2->setStrSinSelecaoInteressado('S');
	   	$objRN->excluir($objRN->listar($objDTO2));
	   
	   	$arrPrincipal2 = PaginaSEI::getInstance()->getArrValuesSelect($_POST['hdnPrincipal2']);
	   
	   	if(!$arrPrincipal2) {
	   		$objInfraException->adicionarValidacao('Informe pelo menos um tipo de interessado.');
	   	}
	   
	   	$objInfraException->lancarValidacoes();
	   	   	
	   	foreach($arrPrincipal2 as $numPrincipal){
	   		$objDTO2 = new RelTipoContextoPeticionamentoDTO();
	   		$objDTO2->setNumIdTipoContextoContato($numPrincipal);
	   		$objDTO2->setStrSinCadastroInteressado('N');
	   		$objDTO2->setStrSinSelecaoInteressado('S');
	   		$objDTO2 = $objRN->cadastrar($objDTO2);
	   	}
   
    }

$objDTO = new RelTipoContextoPeticionamentoDTO();
$objDTO->retTodos();
$objDTO->setStrSinCadastroInteressado('S');
$objDTO->setStrSinSelecaoInteressado('N');
$arrItens = $objRN->listar($objDTO);
$numero = count( $arrItens );
$strSelPrin = "";

$objDTO2 = new RelTipoContextoPeticionamentoDTO();
$objDTO2->retTodos();
$objDTO2->setStrSinCadastroInteressado('N');
$objDTO2->setStrSinSelecaoInteressado('S');
$arrItens2 = $objRN->listar($objDTO2);
$numero2 = count( $arrItens2 );
$strSelPrin2 = "";

if( $numero > 0){
	
	//SEIv2
	//$tipoContextoRN = new TipoContextoContatoRN();
	
	//SEIv3
	$tipoContextoRN = new TipoContatoRN();
	
	foreach( $arrItens as $item ){
		
		//SEIv2
		//$tipoContextoDTO = new TipoContextoContatoDTO();
		//$tipoContextoDTO->retNumIdTipoContextoContato();
		//$tipoContextoDTO->retStrNome();
		//$tipoContextoDTO->setNumIdTipoContextoContato( $item->getNumIdTipoContextoContato() );
		
		//SEIv3
		$tipoContextoDTO = new TipoContatoDTO();
		$tipoContextoDTO->retNumIdTipoContato();
		$tipoContextoDTO->retStrNome();
		$tipoContextoDTO->setNumIdTipoContato( $item->getNumIdTipoContextoContato() );
		
		
		$tipoContextoDTO = $tipoContextoRN->consultarRN0336( $tipoContextoDTO );
		
		//SEIv2
		//$strSelPrin .= "<option value='" . $item->getNumIdTipoContextoContato() . "'>" . $tipoContextoDTO->getStrNome() . " </option>";
		
		//SEIv3
		$strSelPrin .= "<option value='" . $item->getNumIdTipoContextoContato() . "'>" . $tipoContextoDTO->getStrNome() . " </option>";
	
	}
	
}

if( $numero2 > 0){

	//SEIv2
	//$tipoContextoRN = new TipoContextoContatoRN();
	
	//SEIv3
	$tipoContextoRN = new TipoContatoRN();

	foreach( $arrItens2 as $item ){
		
		//SEIv2
		//$tipoContextoDTO = new TipoContextoContatoDTO();
		//$tipoContextoDTO->retNumIdTipoContextoContato();
		//$tipoContextoDTO->retStrNome();
		//$tipoContextoDTO->setNumIdTipoContextoContato( $item->getNumIdTipoContextoContato() );
		//$tipoContextoDTO = $tipoContextoRN->consultarRN0336( $tipoContextoDTO );
		//$strSelPrin2 .= "<option value='" . $item->getNumIdTipoContextoContato() . "'>" . $tipoContextoDTO->getStrNome() . " </option>";
		
		//SEIv3
		$tipoContextoDTO = new TipoContatoDTO();
		$tipoContextoDTO->retNumIdTipoContato();
		$tipoContextoDTO->retStrNome();
		$tipoContextoDTO->setNumIdTipoContato( $item->getNumIdTipoContextoContato() );
		$tipoContextoDTO = $tipoContextoRN->consultarRN0336( $tipoContextoDTO );
		$strSelPrin2 .= "<option value='" . $item->getNumIdTipoContextoContato() . "'>" . $tipoContextoDTO->getStrNome() . " </option>";
		
	}

}

//$strSelExtensoesPrin = TipoContextoContatoINT::montarSelectNomeRI0390(null,null,null);

$strTitulo = "Peticionamento - Tipos de Contatos Permitidos";

$arrComandos[] = '<button type="submit" accesskey="s" name="sbmCadastrarGrupoUnidade" value="Salvar" class="infraButton"><span class="infraTeclaAtalho">S</span>alvar</button>';
$arrComandos[] = '<button type="button" accesskey="c" name="btnFechar" id="btnFechar" value="Fechar" onclick="location.href=\''.PaginaSEI::getInstance()->formatarXHTML(SessaoSei::getInstance()->assinarLink('controlador.php?acao=procedimento_controlar&acao_origem='.$_GET['acao'])).'\';" class="infraButton">Fe<span class="infraTeclaAtalho">c</span>har</button>';
	
PaginaSEI::getInstance()->montarDocType();
PaginaSEI::getInstance()->abrirHtml();
PaginaSEI::getInstance()->abrirHead();
PaginaSEI::getInstance()->montarMeta();
PaginaSEI::getInstance()->montarTitle(':: '.PaginaSEI::getInstance()->getStrNomeSistema().' - '.$strTitulo.' ::');
PaginaSEI::getInstance()->montarStyle();
PaginaSEI::getInstance()->abrirStyle();
?>

  #lblPrincipal {position:absolute;left:0%;width:70.5%;top:0px;}
  #txtPrincipal{position:absolute;left:0%;width:70.5%;top:18px;}
  #selPrincipal {position:absolute;left:0%;width:81%;top:41px;}
  #imgLupaPrincipal {position:absolute;left:81.5%;top:41px;}
  #imgExcluirPrincipal {position:absolute;left:81.3%;top:61px;}
  
  #lblPrincipal2 {position:absolute;left:0%;width:70.5%;top:181px;}
  #txtPrincipal2 {position:absolute;left:0%;width:70.5%;top:199px;}
  #selPrincipal2 {position:absolute;left:0%;width:81%;top:222px;}
  #imgLupaPrincipal2 {position:absolute;left:81.5%;top:222px;}
  #imgExcluirPrincipal2 {position:absolute;left:81.3%;top:242px;}
  
<?
PaginaSEI::getInstance()->fecharStyle();
PaginaSEI::getInstance()->montarJavaScript();
PaginaSEI::getInstance()->abrirJavaScript();

$strLinkAjaxPrincipal = SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=tipo_contexto_contato_listar');
$strLinkPrincipalSelecao = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=tipo_contato_selecionar&tipo_selecao=2&id_object=objLupaPrincipal');

$strLinkAjaxPrincipal2 = SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=tipo_contexto_contato_listar');
$strLinkPrincipalSelecao2 = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=tipo_contato_selecionar&tipo_selecao=2&id_object=objLupaPrincipal2');
?>

var objLupaPrincipal = null;
var objAutoCompletarPrincipal = null;

var objLupaPrincipal2 = null;
var objAutoCompletarPrincipal2 = null;

  function inicializar(){
     
     //=================== CAMPO 1 =====================
     objLupaPrincipal = new infraLupaSelect('selPrincipal','hdnPrincipal','<?=$strLinkPrincipalSelecao?>');
     
    objAutoCompletarPrincipal = new infraAjaxAutoCompletar('hdnIdPrincipal','txtPrincipal','<?=$strLinkAjaxPrincipal?>');
    objAutoCompletarPrincipal.limparCampo = true;
    
    //objAutoCompletarPrincipal.tamanhoMinimo = 3;

    objAutoCompletarPrincipal.prepararExecucao = function(){
      return 'extensao='+document.getElementById('txtPrincipal').value;
    };
    
    objAutoCompletarPrincipal.processarResultado = function(id,descricao,complemento){
      if (id!=''){
        var options = document.getElementById('selPrincipal').options;

        for(var i=0;i < options.length;i++){
          if (options[i].value == id){
            self.setTimeout('alert(\'Tipo de Interessado já consta na lista.\')',100);
            break;
          }
        }

        if (i==options.length){

        for(i=0;i < options.length;i++){
          options[i].selected = false;
        }

        opt = infraSelectAdicionarOption(document.getElementById('selPrincipal'),descricao,id);

        objLupaPrincipal.atualizar();

        opt.selected = true;
      }

      document.getElementById('txtPrincipal').value = '';
      document.getElementById('txtPrincipal').focus();
    }};
    
    //=================== CAMPO 2 =====================
    objLupaPrincipal2 = new infraLupaSelect('selPrincipal2','hdnPrincipal2','<?=$strLinkPrincipalSelecao2?>');
    objAutoCompletarPrincipal2 = new infraAjaxAutoCompletar('hdnIdPrincipal2','txtPrincipal2','<?=$strLinkAjaxPrincipal2?>');
    objAutoCompletarPrincipal2.limparCampo = true;

    objAutoCompletarPrincipal2.prepararExecucao = function(){
      return 'extensao='+document.getElementById('txtPrincipal2').value;
    };
    
    objAutoCompletarPrincipal2.processarResultado = function(id,descricao,complemento){
      if (id!=''){
        var options = document.getElementById('selPrincipal2').options;

        for(var i=0;i < options.length;i++){
          if (options[i].value == id){
            self.setTimeout('alert(\'Tipo de Interessado já consta na lista.\')',100);
            break;
          }
        }

        if (i==options.length){

        for(i=0;i < options.length;i++){
          options[i].selected = false;
        }

        opt = infraSelectAdicionarOption(document.getElementById('selPrincipal2'),descricao,id);

        objLupaPrincipal2.atualizar();

        opt.selected = true;
      }

      document.getElementById('txtPrincipal2').value = '';
      document.getElementById('txtPrincipal2').focus();
    }};
    
    
    infraEfeitoTabelas();
}

function OnSubmitForm() {
  return validarCadastro();
}

function validarCadastro() {

  if (infraTrim(document.getElementById('hdnPrincipal').value)=='') {
    alert('Informe pelo menos um Tipo de Contato Permitido para Cadastro de Interessado.');
    return false;
  }
  
  else if (infraTrim(document.getElementById('hdnPrincipal2').value)=='') {
    alert('Informe pelo menos um Tipo de Contato Permitido para Seleção de Interessado.');
    return false;
  }

  return true;
}

<?
PaginaSEI::getInstance()->fecharJavaScript();
PaginaSEI::getInstance()->fecharHead();
PaginaSEI::getInstance()->abrirBody($strTitulo,'onload="inicializar();"');
?>
  <form id="frmGrupoCadastro" method="post" onsubmit="return OnSubmitForm();" 
        action="<?=PaginaSEI::getInstance()->formatarXHTML(SessaoSei::getInstance()->assinarLink('controlador.php?acao='.$_GET['acao'].'&acao_origem='.$_GET['acao']))?>">
    <?
    PaginaSEI::getInstance()->montarBarraComandosSuperior($arrComandos);
    PaginaSEI::getInstance()->abrirAreaDados('45em');
    ?>
        
    <!-- //////////////////////////////////// CAMPO 1 //////////////////////////////////// -->
    <label id="lblPrincipal" for="txtPrincipal" class="infraLabelObrigatorio">
	Para Cadastro de Interessado:
	</label>
    
    <input type="text" id="txtPrincipal" name="txtPrincipal" class="infraText"  
    onkeypress="return infraMascaraTexto(this,event,50);" maxlength="50" 
    tabindex="<?=PaginaSEI::getInstance()->getProxTabDados()?>"/>
    <select id="selPrincipal" name="selPrincipal" size="8" multiple="multiple" class="infraSelect" tabindex="<?=PaginaSEI::getInstance()->getProxTabDados()?>">
    <?=$strSelPrin; ?>
    </select>
    
    <img id="imgLupaPrincipal" onclick="objLupaPrincipal.selecionar(700,500);" src="/infra_css/imagens/lupa.gif" 
    alt="Selecionar Tipos de Contatos" title="Selecionar Tipos de Contatos" class="infraImg" 
    tabindex="<?=PaginaSEI::getInstance()->getProxTabDados()?>" />
    
    <img id="imgExcluirPrincipal" onclick="objLupaPrincipal.remover();" src="/infra_css/imagens/remover.gif" 
    alt="Remover Tipos de Contatos Selecionadas" title="Remover Tipos de Contatos Selecionados" 
    class="infraImg" tabindex="<?=PaginaSEI::getInstance()->getProxTabDados()?>" />
    
    <!--  //////////////////////////////////// CAMPO 2 //////////////////////////////////// -->
    <label id="lblPrincipal2" for="txtPrincipal2" class="infraLabelObrigatorio">
	Para Seleção de Interessado:
	</label>
    
    <input type="text" id="txtPrincipal2" name="txtPrincipal2" class="infraText"  
    onkeypress="return infraMascaraTexto(this,event,50);" maxlength="50" 
    tabindex="<?=PaginaSEI::getInstance()->getProxTabDados()?>"/>
    <select id="selPrincipal2" name="selPrincipal2" size="8" multiple="multiple" class="infraSelect" tabindex="<?=PaginaSEI::getInstance()->getProxTabDados()?>">
    <?=$strSelPrin2; ?>
    </select>
    
    <img id="imgLupaPrincipal2" onclick="objLupaPrincipal2.selecionar(700,500);" src="/infra_css/imagens/lupa.gif" 
    alt="Selecionar Tipos de Contatos" title="Selecionar Tipos de Contatos" class="infraImg" 
    tabindex="<?=PaginaSEI::getInstance()->getProxTabDados()?>" />
    
    <img id="imgExcluirPrincipal2" onclick="objLupaPrincipal2.remover();" src="/infra_css/imagens/remover.gif" 
    alt="Remover Tipos de Contatos Selecionadas" title="Remover Tipos de Contatos Selecionados" 
    class="infraImg" tabindex="<?=PaginaSEI::getInstance()->getProxTabDados()?>" />
	
	<!--  //////////////////////////////////// CAMPOS HIDDEN ////////////////////////////////////  -->
    <input type="hidden" id="hdnIdPrincipal" name="hdnIdPrincipal" class="infraText" value="" />
    <input type="hidden" id="hdnPrincipal" name="hdnPrincipal" value="" />
    
    <input type="hidden" id="hdnIdPrincipal2" name="hdnIdPrincipal2" class="infraText" value="" />
    <input type="hidden" id="hdnPrincipal2" name="hdnPrincipal2" value="" />
    
    <?
    PaginaSEI::getInstance()->fecharAreaDados();
    ?>
  </form>
<?
PaginaSEI::getInstance()->fecharBody();
PaginaSEI::getInstance()->fecharHtml();
?>