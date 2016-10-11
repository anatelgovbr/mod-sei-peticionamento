<?php 
$strLinkAjaxContatos = SessaoSEIExterna::getInstance()->assinarLink('/sei/institucional/peticionamento/controlador_ajax_externo.php?acao_ajax_externo=contato_pj_vinculada&id_orgao_acesso_externo=0');
$strLinkAjaxCidade = SessaoSEIExterna::getInstance()->assinarLink('controlador_ajax_externo.php?acao_ajax=cidade_montar_select_nome');
?>
<script type="text/javascript">
/**
* ANATEL
*
* 23/09/2016 - criado por marcelo.bezerra@cast.com.br - CAST
*
*/

var objAjaxNomeCidade = null;
var objSelectSiglaEstado = null;
var objSelectNomeCidade = null;
var objAjaxContatoRI0571 = null;
var objAutoCompletarContexto = null;

function selecionarTipoInteressado(){

	document.getElementById('hdnIdContextoContato').value = '';
    document.getElementById('txtPjVinculada').value = '';
}

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

  document.getElementById('lblNome').style.display = '';
  document.getElementById('lblCPF').style.display = '';

  <?php if( !isset( $_GET['cpf']) ) { ?>
  document.getElementById('lblRazaoSocial').style.display = 'none';
  document.getElementById('lblCNPJ').style.display = 'none';
  document.getElementById('txtCNPJ').value='';
  document.getElementById('txtRazaoSocial').value='';
  <?php } ?>
  
  //mostrar campos Vocativo, Tratamento, Cargo
  document.getElementById('lblCargo').style.display = '';
  document.getElementById('lblVocativo').style.display = '';
  document.getElementById('lblTratamento').style.display = '';

  //mostrar campos RG, orgao expedidor, numero da OAB
  document.getElementById('div1').style.display = '';

  <?php if( isset( $_GET['cnpj']) ) { ?>
  document.getElementById('lblCNPJ').style.display = 'none';
  document.getElementById('lblRazaoSocial').style.display = 'none';
  <?php } ?>
  
}

function mostrarCamposPJ(){

  <?php if( !isset( $_GET['cnpj']) ) { ?>	
  document.getElementById('rdPF1').style.display = 'none';
  document.getElementById('rdPF2').style.display = 'none';
  
  document.getElementById('rdPF1').checked = false;
  document.getElementById('rdPF2').checked = false;
  document.getElementById('rdPF1').checked = '';
  document.getElementById('rdPF2').checked = '';
  
  document.getElementById('lblrdPF1').style.display = 'none';
  document.getElementById('lblrdPF2').style.display = 'none';
  <?php } ?>
  
  document.getElementById('lblPjVinculada').style.display = 'none';
  document.getElementById('txtPjVinculada').style.display = 'none';
  document.getElementById('txtPjVinculada').value='';

  <?php if( !isset( $_GET['cnpj']) ) { ?>
  document.getElementById('lblNome').style.display = 'none';
  document.getElementById('lblCPF').style.display = 'none';
  document.getElementById('txtNome').value='';
  document.getElementById('txtCPF').value='';
  <?php } ?>
  
  document.getElementById('lblCNPJ').style.display = '';
  document.getElementById('lblRazaoSocial').style.display = '';

  //ocultar campos Vocativo, Tratamento, Cargo
  document.getElementById('lblCargo').style.display = 'none';
  document.getElementById('cargo').value = '';

  document.getElementById('lblVocativo').style.display = 'none';
  document.getElementById('vocativo').value = '';
  
  document.getElementById('lblTratamento').style.display = 'none';
  document.getElementById('tratamento').value = '';

  //mostrar campos RG, orgao expedidor, numero da OAB
  document.getElementById('div1').style.display = 'none';

  <?php if( isset( $_GET['cpf']) ) { ?>
  document.getElementById('rdPF1').style.display = 'none';
  document.getElementById('rdPF2').style.display = 'none';
  document.getElementById('lblrdPF1').style.display = 'none';
  document.getElementById('lblrdPF2').style.display = 'none';
  
  document.getElementById('rdPF1').checked = false;
  document.getElementById('rdPF1').checked = '';

  document.getElementById('rdPF2').checked = false;
  document.getElementById('rdPF2').checked = '';

  document.getElementById('lblNome').style.display = 'none';
  document.getElementById('lblCPF').style.display = 'none';
  document.getElementById('txtNome').value='';
  document.getElementById('txtCPF').value='';
  <?php } ?>
  
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

	var hdnIdContexto = document.getElementById('hdnIdContextoContato');
	var txtPjVinculada = document.getElementById('txtPjVinculada');

	if( txtPjVinculada != null && hdnIdContexto != null){
	
		objAutoCompletarContexto = new infraAjaxAutoCompletar('hdnIdContextoContato','txtPjVinculada','<?=$strLinkAjaxContatos?>');
		objAutoCompletarContexto.limparCampo = true;
	
		objAutoCompletarContexto.prepararExecucao = function(){
			return 'id_tipo_contexto_contato='+document.getElementById('tipoInteressado').value+'&palavras_pesquisa='+document.getElementById('txtPjVinculada').value;
		};
		  
		objAutoCompletarContexto.processarResultado = function(id,descricao,complemento){
		    	  
		if (id!=''){
		      document.getElementById('hdnIdContextoContato').value = id;
		      document.getElementById('txtPjVinculada').value = descricao;
	    }
		    
	    };

	}
	
	<?php if( isset( $_GET['edicao'] ) ) { ?>

      var idEdicao = window.opener.document.getElementById("hdnIdEdicao").value;
	  document.getElementById("hdnIdEdicaoAuxiliar").value = idEdicao;
	  document.frmEdicaoAuxiliar.submit();
	  return;
	
	<?php } else { ?>

	var txtcpf = '';
	var txtcnpj = '';
	
    if( window.opener.document.getElementById("txtCPF") != null ){	
	  txtcpf = window.opener.document.getElementById("txtCPF").value;
    }

    if( window.opener.document.getElementById("txtCNPJ") != null ){
	  txtcnpj = window.opener.document.getElementById("txtCNPJ").value;
    }
      
	<?php if( isset( $_GET['cpf'] ) ) { ?>
	  document.getElementById("rdPF").click();
	  document.getElementById("txtCPF").value = txtcpf;
	<?php } ?>
		
	<?php if( isset( $_GET['cnpj'] ) ) { ?>
	  document.getElementById("rdPJ").click();
	  document.getElementById("txtCNPJ").value = txtcnpj;
	<?php } ?>

	<?php if( isset( $_GET['edicaoExibir'] ) && isset( $_GET['cnpj'] )  ) { ?>
      document.getElementById("txtCNPJ").value = "<?= InfraUtil::formatarCnpj( $_POST['txtCNPJ'] ) ?>";	  
	<?php } ?>

	<?php if( isset( $_GET['edicaoExibir'] ) && isset( $_GET['cpf'] ) ) { ?>
	  document.getElementById("txtCPF").value = "<?= InfraUtil::formatarCpf( $_POST['txtCPF'] ) ?>";	  
	<?php } ?>  

	<?php if( isset( $_POST['hdnIdContextoContato'] ) && isset( $_GET['cpf'] ) ) { ?>

	    //rdPF2 com vinculo
	    document.getElementById("rdPF2").checked = 'checked';
	    document.getElementById("rdPF2").click();
	    document.getElementById("txtPjVinculada").value = '<?php echo $_POST['txtPjVinculada']; ?>';
	  
	<?php } else if( isset( $_GET['cpf'] ) ) { ?>

	    //rdPF1 com vinculo
	    document.getElementById("rdPF1").checked = 'checked';
	    document.getElementById("rdPF1").click();
	  
	  <?php } ?>
		  	
	  //Ajax para carregar as cidades na escolha do estado
	  objAjaxCidade = new infraAjaxMontarSelectDependente('selEstado','selCidade','<?=$strLinkAjaxCidade?>');
	  objAjaxCidade.prepararExecucao = function(){
	    return infraAjaxMontarPostPadraoSelect('null','','null') + '&siglaUf='+document.getElementById('selEstado').value;
	  }
	  objAjaxCidade.processarResultado = function(){
	    //alert('terminou carregamento');
	  }
	  
	  infraEfeitoTabelas();
    
    <?php } ?>

    <?php if( isset($_GET['edicaoExibir']) ) { ?>

      document.getElementById('txtPjVinculada').disabled = true;
      document.getElementById('txtPjVinculada').disabled = 'disabled';

      document.getElementById('tipoInteressado').disabled = true;
      document.getElementById('tipoInteressado').disabled = 'disabled';

      <?php if( isset( $_GET['cpf'] ) ) { ?>
      
	  document.getElementById("rdPF").disabled = true;
	  document.getElementById("rdPF").disabled = 'disabled';

	  document.getElementById("rdPF1").disabled = true;
	  document.getElementById("rdPF1").disabled = 'disabled';

	  document.getElementById("rdPF2").disabled = true;
	  document.getElementById("rdPF2").disabled = 'disabled';
	  
	  <?php } ?>
    	
    <?php } ?>

    <?php 
    $janelaSelecaoPorNome = SessaoSEIExterna::getInstance()->getAtributo('janelaSelecaoPorNome');
    if( $janelaSelecaoPorNome != null && $janelaSelecaoPorNome != "" && !isset( $_GET['cadastro'] ) ) { ?>

	//alert('aqui <?= $janelaSelecaoPorNome ?>');
    document.getElementById("rdPF").checked = false;
    document.getElementById("rdPF").checked = '';

    document.getElementById("rdPJ").checked = false;
    document.getElementById("rdPJ").checked = '';

    document.getElementById('lblNome').style.display = 'none';
    document.getElementById('lblCPF').style.display = 'none';

    document.getElementById('lblRazaoSocial').style.display = 'none';
    document.getElementById('lblCNPJ').style.display = 'none';

    document.getElementById('txtCPF').readOnly = false;
    document.getElementById('txtCPF').readOnly = '';

    document.getElementById('txtCNPJ').readOnly = false;
    document.getElementById('txtCNPJ').readOnly = '';
    
    <?php } ?>
  
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

function salvar(){
	
	//validar interessado
	var interessado1 = document.frmCadastro.tipoPessoa.value;
	var interessado2 = '';
	var tipoPessoaPF = document.frmCadastro.tipoPessoaPF;
	
	if( tipoPessoaPF != null && tipoPessoaPF != undefined ){
	  interessado2 = tipoPessoaPF.value;
	}
	
	if( interessado1 == '' ){
      alert('Informe se o Interessado é Pessoa Física ou Pessoa Jurídica.');
	  return;
	}

	else if( interessado1 == 'pf' && interessado2 == ''){
	  alert('Informe se o Interessado Pessoa Física a ser cadastrado possui ou não vínculo com Pessoa Jurídica.');
	  return;
	}
	
	//validar tipo de interessado
	var tipoInteressado = document.getElementById('tipoInteressado').value;
	
	if( tipoInteressado == '' || tipoInteressado == 'null'  ){
		alert('Informe o Tipo de Interessado.');
		document.getElementById('tipoInteressado').focus();
		return;
	}
	
	//validar nome ou razao social
	var nome = document.getElementById('txtNome').value;
	var razaoSocial = document.getElementById('txtRazaoSocial').value;

	if( interessado1 == 'pf' && nome == '' ){
		alert('Informe o Nome Completo.');
		document.getElementById('txtNome').focus();
		return;
		 
	} else if( interessado1 == 'pj' && razaoSocial == '' ){
		alert('Informe a Razão Social.');
		document.getElementById('txtRazaoSocial').focus();
		return;
	}
	
	//validar pj vinculada (caso exista)
	var pjVinculada = document.getElementById('txtPjVinculada').value;
	var idContextoAjax = document.getElementById('hdnIdContextoContato');

	if( interessado1 == 'pf' && interessado2 == '1' && ( pjVinculada == '' || idContextoAjax == null || idContextoAjax.value=='' ) ){
		alert('Informe a Razão Social da Pessoa Jurídica vinculada.');
		document.getElementById('txtPjVinculada').focus();
		return;		 
	}
	
	//validar se o cpf ou o cnpj foram preenchidos
	var cpf = document.getElementById('txtCPF').value;
	var cnpj = document.getElementById('txtCNPJ').value;

	if( interessado1 == 'pf' && cpf == '' ){
      alert('Informe o CPF.');
      document.getElementById('txtCPF').focus();
      return;
      
	} else if( interessado1 == 'pj' && cnpj == '' ){
	  alert('Informe o CNPJ.');
	  document.getElementById('txtCNPJ').focus();
	  return;
	}
	
	//validar se o CPF ou o CNPJ preenchidos são válidos
	if ( document.getElementById('txtCNPJ').value != "" && !infraValidarCnpj(infraTrim(document.getElementById('txtCNPJ').value))){
		
		alert('CNPJ inválido.');
		document.getElementById('txtCNPJ').focus();
		return;
	}
	
	if ( document.getElementById('txtCPF').value != "" && !infraValidarCpf(infraTrim(document.getElementById('txtCPF').value))){
		
		alert('CPF inválido.');
		document.getElementById('txtCPF').focus();
		return;
	}
	
	//rg
	var rg = document.getElementById('rg').value;

	if( interessado1 == 'pf' && rg == '' ){
	  alert('Informe o RG.');
	  document.getElementById('rg').focus();
	  return;
	}
	
	//orgao expedidor
	var orgaoExpedidor = document.getElementById('orgaoExpedidor').value;

	if( interessado1 == 'pf' && orgaoExpedidor == '' ){
	  alert('Informe o Órgão Expedidor do RG.');
	  document.getElementById('orgaoExpedidor').focus();
	  return;
	}
	
	//tratamento
	var tratamento = document.getElementById('tratamento').value;

	if( interessado1 == 'pf' && ( tratamento == 'null' || tratamento == '') ){
	  alert('Informe o Tratamento.');
	  document.getElementById('tratamento').focus();
	  return;
	}
	
	//cargo
	var cargo = document.getElementById('cargo').value;

	if( interessado1 == 'pf' && ( cargo == 'null' || cargo == '') ){
	  alert('Informe o Cargo.');
	  document.getElementById('cargo').focus();
	  return;
	}
	
	//vocativo
	var vocativo = document.getElementById('vocativo').value;

	if( interessado1 == 'pf' && ( vocativo == 'null' || vocativo == '') ){
	  alert('Informe o Vocativo.');
	  document.getElementById('vocativo').focus();
	  return;
	}
		
	//telefone
	var telefone = document.getElementById('telefone').value;

	if( telefone == ''){
	  alert('Informe o Telefone.');
	  document.getElementById('telefone').focus();
	  return;
	}

	//email
	if ( document.getElementById('email').value != "" && !infraValidarEmail(infraTrim(document.getElementById('email').value))){
		
		alert('E-mail inválido.');
		document.getElementById('email').focus();
		return false;
	
	}
	
	//endereco
	var endereco = document.getElementById('endereco').value;

	if( endereco == ''){
	  alert('Informe o Endereço.');
	  document.getElementById('endereco').focus();
	  return;
	}
	
	//bairro
	var bairro = document.getElementById('bairro').value;

	if( bairro == ''){
	  alert('Informe o Bairro.');
	  document.getElementById('bairro').focus();
	  return;
	}
	
	//estado
	var estado = document.getElementById('selEstado').value;

	if( estado == '' || estado == 'null'){
	  alert('Informe o Estado.');
	  document.getElementById('selEstado').focus();
	  return;
	}
	
	//cidade
	var cidade = document.getElementById('selCidade').value;

	if( cidade == '' || cidade == 'null'){
	  alert('Informe a Cidade.');
	  document.getElementById('selCidade').focus();
	  return;
	}
	
	//cep
	var cep = document.getElementById('cep').value;

	if( cep == '' ){
	  alert('Informe o CEP.');
	  document.getElementById('cep').focus();
	  return;
	}

	document.frmCadastro.submit();
	
}

</script>