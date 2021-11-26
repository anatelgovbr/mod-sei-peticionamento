<?
/*
 * @author Marcelo Bezerra <marcelo.bezerra@castgroup.com.br>
 * 
 * */

try {
	
	require_once dirname(__FILE__).'/../../SEI.php';

	session_start();	
	
	//////////////////////////////////////////////////////////////////////////////
	InfraDebug::getInstance()->setBolLigado(false);
	InfraDebug::getInstance()->setBolDebugInfra(false);
	InfraDebug::getInstance()->limpar();
	//////////////////////////////////////////////////////////////////////////////
	
	SessaoSei::getInstance()->validarLink();
	SessaoSei::getInstance()->validarPermissao($_GET['acao']);
		
	$objRN = new MdPetTpCtxContatoRN();

    if ((isset( $_POST['hdnPrincipal'] ) && $_POST['hdnPrincipal'] != "") || (isset( $_POST['hdnPrincipal2'] ) && $_POST['hdnPrincipal2'] != "")) {
        $arrContatosPrincipais = array();
        if (isset( $_POST['hdnPrincipal'] ) && $_POST['hdnPrincipal'] != "") {
            $arrPrincipal = PaginaSEI::getInstance()->getArrValuesSelect($_POST['hdnPrincipal']);
            $arrPrincipal['cadastro'] = 'S';
            array_push($arrContatosPrincipais, $arrPrincipal);
        }

        // S�o permitidos Contatos de sistema para Sele��o
        if (isset($_POST['hdnPrincipal2']) && $_POST['hdnPrincipal2'] != "") {
            $arrPrincipal2 = PaginaSEI::getInstance()->getArrValuesSelect($_POST['hdnPrincipal2']);
            $arrPrincipal2['cadastro'] = 'N';
            array_push($arrContatosPrincipais, $arrPrincipal2);
        }

        $objRN->cadastrarMultiplo($arrContatosPrincipais);

    }
   
   }catch(Exception $e){
   	PaginaSEI::getInstance()->processarExcecao($e);
   }

$objDTO = new MdPetRelTpCtxContatoDTO();
$objDTO->retTodos();
$objDTO->setStrSinCadastroInteressado('S');
$objDTO->setStrSinSelecaoInteressado('N');
$arrItens = $objRN->listar($objDTO);
$numero = count( $arrItens );
$strSelPrin = "";

$objDTO2 = new MdPetRelTpCtxContatoDTO();
$objDTO2->retTodos();
$objDTO2->setStrSinCadastroInteressado('N');
$objDTO2->setStrSinSelecaoInteressado('S');
$arrItens2 = $objRN->listar($objDTO2);
$numero2 = count( $arrItens2 );
$strSelPrin2 = "";

if( $numero > 0){
	
	//SEIv3
	$tipoContextoRN = new TipoContatoRN();
	
	foreach( $arrItens as $item ){

		//SEIv3
		$tipoContextoDTO = new TipoContatoDTO();
		$tipoContextoDTO->retNumIdTipoContato();
		$tipoContextoDTO->retStrNome();
		$tipoContextoDTO->setNumIdTipoContato( $item->getNumIdTipoContextoContato() );

		$tipoContextoDTO = $tipoContextoRN->consultarRN0336( $tipoContextoDTO );

		//SEIv3
		$strSelPrin .= "<option value='" . $item->getNumIdTipoContextoContato() . "'>" . $tipoContextoDTO->getStrNome() . " </option>";
	
	}
	
}

if( $numero2 > 0){

	//SEIv3
	$tipoContextoRN = new TipoContatoRN();

	foreach( $arrItens2 as $item ){

		//SEIv3
		$tipoContextoDTO = new TipoContatoDTO();
		$tipoContextoDTO->retNumIdTipoContato();
		$tipoContextoDTO->retStrNome();
		$tipoContextoDTO->setNumIdTipoContato( $item->getNumIdTipoContextoContato() );
		$tipoContextoDTO = $tipoContextoRN->consultarRN0336( $tipoContextoDTO );
		$strSelPrin2 .= "<option value='" . $item->getNumIdTipoContextoContato() . "'>" . $tipoContextoDTO->getStrNome() . " </option>";
		
	}

}

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

$strLinkAjaxPrincipal = SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=md_pet_tp_ctx_contato_listar');
$strLinkPrincipalSelecao = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=tipo_contato_selecionar&tipo_selecao=2&id_object=objLupaPrincipal');

$strLinkAjaxPrincipal2 = SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=md_pet_tp_ctx_contato_listar');
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
    objAutoCompletarPrincipal.tamanhoMinimo = 3;
    objAutoCompletarPrincipal.prepararExecucao = function(){
      return 'extensao='+document.getElementById('txtPrincipal').value;
    };
    
    objAutoCompletarPrincipal.processarResultado = function(id,descricao,complemento){
      if (id!=''){
        var options = document.getElementById('selPrincipal').options;

        for(var i=0;i < options.length;i++){
          if (options[i].value == id){
            self.setTimeout('alert(\'Tipo de Interessado j� consta na lista.\')',100);
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
    objAutoCompletarPrincipal2.tamanhoMinimo = 3;
    objAutoCompletarPrincipal2.prepararExecucao = function(){
      return 'extensao='+document.getElementById('txtPrincipal2').value;
    };
    
    objAutoCompletarPrincipal2.processarResultado = function(id,descricao,complemento){
      if (id!=''){
        var options = document.getElementById('selPrincipal2').options;

        for(var i=0;i < options.length;i++){
          if (options[i].value == id){
            self.setTimeout('alert(\'Tipo de Interessado j� consta na lista.\')',100);
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
    alert('Informe pelo menos um Tipo de Contato Permitido para Sele��o de Interessado.');
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
    <label id="lblPrincipal" for="txtPrincipal" class="infraLabelObrigatorio">Cadastro de Interessado: <img align="top" style="height:16px; width:16px;" src="<?= PaginaSEI::getInstance()->getDiretorioImagensGlobal() ?>/ajuda.gif" name="ajuda" <?= PaginaSEI::montarTitleTooltip('Nos casos em que o Usu�rio Externo tiver que cadastrar Contato, o campo de Tipo apresentado para ele ser� restringido aos Tipos de Contatos indicados aqui.')?> class="infraImg"/></label>
    
    <input type="text" id="txtPrincipal" name="txtPrincipal" class="infraText" onkeypress="return infraMascaraTexto(this,event,50);" maxlength="50" tabindex="<?=PaginaSEI::getInstance()->getProxTabDados()?>"/>
    <select id="selPrincipal" name="selPrincipal" size="8" multiple="multiple" class="infraSelect" tabindex="<?=PaginaSEI::getInstance()->getProxTabDados()?>">
      <?=$strSelPrin; ?>
    </select>
    
    <img id="imgLupaPrincipal" onclick="objLupaPrincipal.selecionar(700,500);" onkeypress="objLupaPrincipal.selecionar(700,500);" src="/infra_css/imagens/lupa.gif" alt="Selecionar Tipos de Contatos" title="Selecionar Tipos de Contatos" class="infraImg" tabindex="<?=PaginaSEI::getInstance()->getProxTabDados()?>" />
    
    <img id="imgExcluirPrincipal" onclick="objLupaPrincipal.remover();" onkeypress="objLupaPrincipal.remover();" src="/infra_css/imagens/remover.gif" alt="Remover Tipos de Contatos Selecionadas" title="Remover Tipos de Contatos Selecionados" class="infraImg" tabindex="<?=PaginaSEI::getInstance()->getProxTabDados()?>" />
    
    <!--  //////////////////////////////////// CAMPO 2 //////////////////////////////////// -->
    <label id="lblPrincipal2" for="txtPrincipal2" class="infraLabelObrigatorio">Sele��o de Interessado: <img align="top" style="height:16px; width:16px;" src="<?= PaginaSEI::getInstance()->getDiretorioImagensGlobal() ?>/ajuda.gif" name="ajuda" <?= PaginaSEI::montarTitleTooltip('Nos casos em que o Usu�rio Externo tiver que selecionar Contato, os Contatos dispon�veis para ele selecionar estar�o restringidos aos Tipos de Contatos indicados aqui.')?> class="infraImg"/></label>
    
    <input type="text" id="txtPrincipal2" name="txtPrincipal2" class="infraText" onkeypress="return infraMascaraTexto(this,event,50);" maxlength="50" tabindex="<?=PaginaSEI::getInstance()->getProxTabDados()?>"/>
    <select id="selPrincipal2" name="selPrincipal2" size="8" multiple="multiple" class="infraSelect" tabindex="<?=PaginaSEI::getInstance()->getProxTabDados()?>">
      <?=$strSelPrin2; ?>
    </select>
    
    <img id="imgLupaPrincipal2" onclick="objLupaPrincipal2.selecionar(700,500);" onkeypress="objLupaPrincipal2.selecionar(700,500);" src="/infra_css/imagens/lupa.gif" alt="Selecionar Tipos de Contatos" title="Selecionar Tipos de Contatos" class="infraImg" tabindex="<?=PaginaSEI::getInstance()->getProxTabDados()?>" />
    
    <img id="imgExcluirPrincipal2" onclick="objLupaPrincipal2.remover();" onkeypress="objLupaPrincipal2.remover();" src="/infra_css/imagens/remover.gif" alt="Remover Tipos de Contatos Selecionadas" title="Remover Tipos de Contatos Selecionados" class="infraImg" tabindex="<?=PaginaSEI::getInstance()->getProxTabDados()?>" />
	
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
PaginaSEI::getInstance()->montarAreaDebug();
PaginaSEI::getInstance()->fecharBody();
PaginaSEI::getInstance()->fecharHtml();
?>