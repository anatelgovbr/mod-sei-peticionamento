<?php
/**
* ANATEL
*
* 23/06/2016 - criado por marcelo.bezerra@cast.com.br - CAST
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
	
  PaginaSEIExterna::getInstance()->setTipoPagina( InfraPagina::$TIPO_PAGINA_SIMPLES );  
  //SessaoSEIExterna::getInstance()->validarLink();
  
  //SessaoSEI::getInstance(false);
  //SessaoSEI::getInstance()->simularLogin(null, null, SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno() , SessaoSEIExterna::getInstance()->getNumIdUnidadeAtual() );
  //SessaoSEIExterna::getInstance()->validarPermissao($_GET['acao']);
  
  //=====================================================
  //INICIO - VARIAVEIS PRINCIPAIS E LISTAS DA PAGINA
  //=====================================================

  //=====================================================
  //FIM - VARIAVEIS PRINCIPAIS E LISTAS DA PAGINA
  //=====================================================
  
  switch($_GET['acao']){
    
  	case 'peticionamento_interessado_cadastro':
  		$strTitulo = 'Cadastro de Interessado';
  		break;
  		
    default:
      throw new InfraException("Ação '".$_GET['acao']."' não reconhecida.");
  }

}catch(Exception $e){
  PaginaSEIExterna::getInstance()->processarExcecao($e);
}

$hashAnexo = "";
$idAnexo = "";

PaginaSEIExterna::getInstance()->montarDocType();
PaginaSEIExterna::getInstance()->abrirHtml();
PaginaSEIExterna::getInstance()->abrirHead();
PaginaSEIExterna::getInstance()->montarMeta();
PaginaSEIExterna::getInstance()->montarTitle(':: '.PaginaSEIExterna::getInstance()->getStrNomeSistema().' - '.$strTitulo.' ::');
PaginaSEIExterna::getInstance()->montarStyle();
PaginaSEIExterna::getInstance()->abrirStyle();
PaginaSEIExterna::getInstance()->fecharStyle();
PaginaSEIExterna::getInstance()->montarJavaScript();
PaginaSEIExterna::getInstance()->abrirJavaScript();
PaginaSEIExterna::getInstance()->fecharJavaScript();
?>
<style type="text/css">
#field1 {height: auto; width: 96%; margin-bottom: 11px;}
#field2 {height: auto; width: 96%; margin-bottom: 11px;}
.sizeFieldset {height:auto; width: 86%;}
.fieldsetClear {border:none !important;}
</style>
<?php 
PaginaSEIExterna::getInstance()->fecharHead();
PaginaSEIExterna::getInstance()->abrirBody($strTitulo,'onload="inicializar();"');
$urlBaseLink = "";
$arrComandos = array();
$arrComandos[] = '<button type="button" accesskey="S" name="Salvar" value="Salvar" onclick="salvar()" class="infraButton"><span class="infraTeclaAtalho">S</span>alvar</button>';
$arrComandos[] = '<button type="button" accesskey="F" name="btnFechar" value="Fechar" onclick="location.href=\''.PaginaSEIExterna::getInstance()->formatarXHTML(SessaoSEIExterna::getInstance()->assinarLink('controlador.php?acao='.PaginaSEIExterna::getInstance()->getAcaoRetorno().'&acao_origem='.$_GET['acao'].PaginaSEIExterna::getInstance()->montarAncora($_GET['id_indisponibilidade_peticionamento']))).'\';" class="infraButton"><span class="infraTeclaAtalho">F</span>echar</button>';
?> 
<form id="frmIndisponibilidadeCadastro" method="post" onsubmit="return OnSubmitForm();"  action="<?=PaginaSEIExterna::getInstance()->formatarXHTML(SessaoSEIExterna::getInstance()->assinarLink('controlador.php?acao='.$_GET['acao'].'&acao_origem='.$_GET['acao']))?>">
<?php
PaginaSEIExterna::getInstance()->montarBarraComandosSuperior($arrComandos);
PaginaSEIExterna::getInstance()->abrirAreaDados('auto');
?>

 <fieldset id="field1" class="infraFieldset sizeFieldset">
    
    <legend class="infraLegend">&nbsp; Interessado &nbsp;</legend>       
	
	<input type="radio" name="tipoPessoa" value="pf" id="rdPF" onclick="selecionarPF()" />
	<label for="rdPF" class="infraLabelRadio">Pessoa física</label> <br/>
	
	    <input type="radio" name="tipoPessoaPF" value="0" id="rdPF1" style="display: none;" onclick="selecionarPF1()" />
	    <label for="rdPF1" id="lblrdPF1" class="infraLabelRadio" style="display: none;">
	    Sem vínculo com qualquer Pessoa Jurídica <br/> 
	    </label>
	
	    <input type="radio" name="tipoPessoaPF" value="1" id="rdPF2" style="display: none;" onclick="selecionarPF2()" />
	    <label for="rdPF2" id="lblrdPF2" class="infraLabelRadio" style="display: none;">
	    Com vínculo com Pessoa Jurídica <br/> 
	    </label> 
	
	<input type="radio" name="tipoPessoa" value="pj" id="rdPJ" onclick="selecionarPJ()" />
	<label for="rdPJ" class="infraLabelRadio">Pessoa jurídica</label>
			
 </fieldset>
  
 <fieldset id="field2" class="infraFieldset sizeFieldset">
    
    <legend class="infraLegend">&nbsp; Formulário de Cadastro &nbsp;</legend>
    
    <label class="infraLabelObrigatorio"> Tipo de Interessado:</label><br/>
    <select class="infraSelect" width="380" id="tipoInteressado" name="tipoInteressado" style="width:380px;" >
        <option value=""></option>
    </select> <br/>
    
    <label class="infraLabelObrigatorio"> Nome / Razão Social:</label><br/>
    <select class="infraSelect" width="380" id="razaoSocial" name="razaoSocial" style="width:380px;" >
        <option value=""></option>
    </select> <br/>
    
    <label id="lblPjVinculada" style="display: none;" class="infraLabelObrigatorio"> 
    Pessoa jurídica a qual o interessado é vinculado:<br/>
    <input type="text" class="infraText" name="txtPjVinculada" id="txtPjVinculada" style="width: 540px; display: none;" />
    <br/><br/>
    </label>
    
    <label class="infraLabelObrigatorio"> CPF/CNPJ:</label><br/>
    <input type="text" class="infraText" name="cpfCnpj" id="cpfCnpj" style="width: 540px;" />
    <br/><br/>
    
    <label class="infraLabelObrigatorio"> Tratamento:</label><br/>
    <select class="infraSelect" width="380" id="tratamento" name="tratamento" style="width:380px;" >
        <option value=""></option>
    </select> <br/>
    
    <label class="infraLabelObrigatorio"> Cargo:</label><br/>
    <select class="infraSelect" width="380" id="cargo" name="cargo" style="width:380px;" >
        <option value=""></option>
    </select> <br/>
    
    <label class="infraLabelObrigatorio"> Vocativo:</label><br/>
    <select class="infraSelect" width="380" id="vocativo" name="vocativo" style="width:380px;" >
        <option value=""></option>
    </select> <br/>  
    
    <div class="div1" style="float:left; width: auto;">
        
        <div id="div1_1" style="float:left; width: auto;">
        <label class="infraLabel">Número da OAB:</label><br/>
        <input type="text" class="infraText" name="numeroOab" id="numeroOab" />
        </div>
        
        <div id="div1_2" style="float:left; margin-left:20px; width: auto;">
        <label class="infraLabelObrigatorio">RG:</label><br/>
        <input type="text" class="infraText" name="rg" id="rg" />
        </div>
        
        <div id="div1_3" style="float:left; margin-left:20px; width: auto;">
        <label class="infraLabelObrigatorio">Órgão Expedidor do RG:</label><br/>
        <input type="text" class="infraText" name="orgaoExpedidor" id="orgaoExpedidor" />
        </div>
        
        <div id="div1_4" style="float:left; margin-left:20px; width: auto;">
        <label class="infraLabelObrigatorio">Telefone:</label><br/>
        <input type="text" class="infraText" name="telefone" id="telefone" />
        </div>
                
    </div>  
    
    <div style="clear: both;"></div>
    
    <div class="div2" style="float:left; width: auto;">
    
        <div id="div2_1" style="float:left; width: 280px;">
          <label class="infraLabel">Email:</label><br/>
          <input type="text" class="infraText" name="email" id="email" style="width: 280px;" />
        </div>
        
        <div id="div2_2" style="float:left; margin-left:20px; width: 280px;">
          <label class="infraLabel">Sítio na Internet:</label><br/>
          <input type="text" class="infraText" style="width: 280px;" name="sitioInternet" id="sitioInternet" />
        </div>
    
    </div>  
    
    <div style="clear: both;"></div>
    
    <div class="div3" style="float:left; width: auto;">
    
        <div id="div3_1" style="float:left; width: 280px;">
        <label class="infraLabelObrigatorio">Endereço:</label><br/>
        <input type="text" class="infraText" style="width: 280px;" name="endereco" id="endereco" />
        </div>
        
        <div id="div3_2" style="float:left; margin-left:20px; width: 280px;">
        <label class="infraLabelObrigatorio">Bairro:</label><br/>
        <input type="text" class="infraText" style="width: 280px;" name="bairro" id="bairro" />
        </div>
    
    </div>  
    
    <div style="clear: both;"></div>
    
    <div class="div4" style="float:left; width: auto;">
    
        <div id="div4_1" style="float:left; width: auto;">
        <label class="infraLabelObrigatorio">País:</label><br/>
        <input type="text" class="infraText" name="pais" id="pais" />
        </div>
        
        <div id="div4_2" style="float:left; margin-left:20px; width: auto;">
        <label class="infraLabelObrigatorio">Estado:</label><br/>
        <input type="text" class="infraText" name="estado" id="estado" />
        </div>
        
        <div id="div4_3" style="float:left; margin-left:20px; width: auto;">
        <label class="infraLabelObrigatorio">Cidade:</label><br/>
        <input type="text" class="infraText" name="cidade" id="cidade" />
        </div>
        
        <div id="div4_4" style="float:left; margin-left:20px; width: auto;">
        <label class="infraLabelObrigatorio">CEP:</label><br/>
        <input type="text" class="infraText" name="cep" id="cep" />
        </div>
    
    </div>  
    
    <div style="clear: both;"></div>
  
  </fieldset>  
    
</form>

<?php
//PaginaSEIExterna::getInstance()->montarBarraComandosInferior($arrComandos);  
PaginaSEIExterna::getInstance()->montarAreaDebug();
PaginaSEIExterna::getInstance()->fecharAreaDados();
PaginaSEIExterna::getInstance()->fecharBody();
PaginaSEIExterna::getInstance()->fecharHtml();
?>
<script type="text/javascript">

function selecionarPF(){
  mostrarCamposPF();
}

function selecionarPF1(){
  ocultarComboPJVinculada();
}

function selecionarPF2(){
	mostrarComboPJVinculada();	
}

function ocultarComboPJVinculada(){
  document.getElementById('lblPjVinculada').style.display = 'none';
  document.getElementById('txtPjVinculada').style.display = 'none';
  document.getElementById('txtPjVinculada').value = '';
}

function mostrarComboPJVinculada(){
  document.getElementById('lblPjVinculada').style.display = '';
  document.getElementById('txtPjVinculada').style.display = '';
}

function selecionarPJ(){
	mostrarCamposPJ();
}

function mostrarCamposPF(){
	
  document.getElementById('rdPF1').style.display = '';
  document.getElementById('rdPF2').style.display = '';
  document.getElementById('lblrdPF1').style.display = '';
  document.getElementById('lblrdPF2').style.display = '';
}

function mostrarCamposPJ(){
	
  document.getElementById('rdPF1').style.display = 'none';
  document.getElementById('rdPF2').style.display = 'none';

  document.getElementById('rdPF1').checked = false;
  document.getElementById('rdPF2').checked = false;
  document.getElementById('rdPF1').checked = '';
  document.getElementById('rdPF2').checked = '';
  
  document.getElementById('lblrdPF1').style.display = 'none';
  document.getElementById('lblrdPF2').style.display = 'none';

  document.getElementById('lblPjVinculada').style.display = 'none';
  document.getElementById('txtPjVinculada').style.display = 'none';
  
}

function enviarInteressado(){

	//alert('Enviar interessados - INICIO');
	
	var arrDados = ["Banana1", "Orange1", "Apple1", "Mango1"];
	arrDados.push("Kiwi1");
	opener.receberInteressado(arrDados, true);

	var arrDados2 = ["Banana2", "Orange2", "Apple2", "Mango2"];
	arrDados2.push("Kiwi2");
	opener.receberInteressado(arrDados2, false);

	//alert('Enviar interessados - FIM');
	
}

function validarFormulario(){

	//valida campo especificação
	var textoEspecificacao = document.getElementById("txtEspecificacao").value;

	if( textoEspecificacao == '' ){
      alert('Informe a especificação.');
      document.getElementById("txtEspecificacao").focus();
      return false;      
	}

	return true;
}

function inicializar(){
  
  infraEfeitoTabelas();
  
}

function returnDateTime(valor){

	valorArray = valor != '' ? valor.split(" ") : '';

	if(Array.isArray(valorArray)){
	  var data = valorArray[0]
	  data = data.split('/');
	  var mes = parseInt(data[1]) - 1; 
      var horas = valorArray[1].split(':');

      var segundos = typeof horas[2] != 'undefined' ?  horas[2] : 00;
	  var dataCompleta = new Date(data[2], mes  ,data[0], horas[0] , horas[1] , segundos);
	  return dataCompleta;
	}

	return false;
}

function OnSubmitForm() {
		
	return true;
}
	 
</script>