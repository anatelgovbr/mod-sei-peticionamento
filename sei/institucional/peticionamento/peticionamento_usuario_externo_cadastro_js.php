<?
/**
* ANATEL
*
* 01/08/2016 - criado por marcelo.bezerra@cast.com.br - CAST
*
* Funções de JS para cadastro de peticionamento de usuario externo
* Essa página é incluida na página principal do cadastro de peticionamento
*
* Documento com este mesmo nome de arquivo já foi adicionado.
*
*/

$strLinkAnexos = SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=peticionamento_usuario_externo_upload_anexo&id_tipo_procedimento='
		. $_GET['id_tipo_procedimento'] . '&id_orgao_acesso_externo=0');

$strLinkPrincipal = SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=peticionamento_usuario_externo_upload_principal&id_tipo_procedimento='
		. $_GET['id_tipo_procedimento'] . '&id_orgao_acesso_externo=0');

//Acao para upload de documento principal
$strLinkUploadDocPrincipal = SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=peticionamento_usuario_externo_upload_doc_principal');

//Acao para upload de documento essencial
$strLinkUploadDocEssencial = SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=peticionamento_usuario_externo_upload_doc_essencial');

//Acao para upload de documento complementar
$strLinkUploadDocComplementar = SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=peticionamento_usuario_externo_upload_doc_complementar');

//==================================================================
//saber se o documento principal é externo ou gerado
//==================================================================
$externo = $ObjTipoProcessoPeticionamentoDTO->getStrSinDocExterno();

//==================================================================
//saber se tem documento essencial configurado na parametrização
//==================================================================
$objRelTipoProcessoSeriePeticionamentoDTO = new RelTipoProcessoSeriePeticionamentoDTO();
$objRelTipoProcessoSeriePeticionamentoDTO->retTodos();
$objRelTipoProcessoSeriePeticionamentoDTO->setStrStaTipoDoc( RelTipoProcessoSeriePeticionamentoRN::$DOC_ESSENCIAL );
$objRelTipoProcessoSeriePeticionamentoDTO->setNumIdTipoProcessoPeticionamento( $objTipoProcDTO->getNumIdTipoProcessoPeticionamento() );
$objRelTipoProcessoSeriePeticionamentoRN = new RelTipoProcessoSeriePeticionamentoRN();

$arrRelTipoProcessoSeriePeticionamentoDTO = $objRelTipoProcessoSeriePeticionamentoRN->listar( $objRelTipoProcessoSeriePeticionamentoDTO );

if( is_array( $arrRelTipoProcessoSeriePeticionamentoDTO ) && count( $arrRelTipoProcessoSeriePeticionamentoDTO ) > 0 ){

	//saber se foram configurados documentos essenciais
	$temDocEssencial = true;
	
} else {
	
	//saber se foram configurados documentos essenciais
	$temDocEssencial = false;
}

//==================================================================
//saber se tem documento Complementar configurado na parametrização
//==================================================================
$objRelTipoProcessoSeriePeticionamentoDTO = new RelTipoProcessoSeriePeticionamentoDTO();
$objRelTipoProcessoSeriePeticionamentoDTO->retTodos();
$objRelTipoProcessoSeriePeticionamentoDTO->setStrStaTipoDoc( RelTipoProcessoSeriePeticionamentoRN::$DOC_COMPLEMENTAR );
$objRelTipoProcessoSeriePeticionamentoDTO->setNumIdTipoProcessoPeticionamento( $objTipoProcDTO->getNumIdTipoProcessoPeticionamento() );
$objRelTipoProcessoSeriePeticionamentoRN = new RelTipoProcessoSeriePeticionamentoRN();

$arrRelTipoProcessoSeriePeticionamentoDTO = $objRelTipoProcessoSeriePeticionamentoRN->listar( $objRelTipoProcessoSeriePeticionamentoDTO );
//print_r( $arrRelTipoProcessoSeriePeticionamentoDTO ); die();

if( is_array( $arrRelTipoProcessoSeriePeticionamentoDTO ) && count( $arrRelTipoProcessoSeriePeticionamentoDTO ) > 0 ){

	//saber se foram configurados documentos complementares
	$temDocComplementar = true;
	
} else {
	
	//saber se foram configurados documentos complementares
	$temDocComplementar = false;
}

//TODO refatorar para utilizar controlador_ajax
$strLinkAjaxContato = SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=contato_cpf_cnpj');

//Validacao de tipo de arquivos
$strSelExtensoesPrin = GerirExtensoesArquivoPeticionamentoINT::recuperaExtensoes(null,null,null,'S');
$strSelExtensoesComp = GerirExtensoesArquivoPeticionamentoINT::recuperaExtensoes(null,null,null,'N');

$strLinkAjaxChecarConteudoDocumento = SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=validar_documento_principal');

?>
<script type="text/javascript">

var objAjaxContato = null;
var objPrincipalUpload = null;
var objTabelaDocPrincipal = null;

var objEssencialUpload = null;
var objTabelaDocEssencial = null;

var objComplementarUpload = null;
var objTabelaDocComplementar = null;

var objAutoCompletarInteressado = null;
var objLupaInteressados = null;

var docTipoEssencial = Array();

function validarQtdArquivosPrincipal(){

	try {
		objPrincipalUpload.executar();
	} catch(err) {
	    alert(" Erro: " + err);
	    console.log(err.stack);
	}
	
}

function validarUploadArquivo(numero){
	
	try {
		
		var isValido = true;

		if( numero == '1'){

		   //se a tabela existir na tela (ou seja se for doc principal do tipo externo)
		   //nao permitir adicionar mais do que 1 documento na grid	
		   var tbDocumentoPrincipal = document.getElementById('tbDocumentoPrincipal');
		   var hiddenCampoPrincipal = document.getElementById('hdnDocPrincipal');
		   
		   if( tbDocumentoPrincipal != null && 
			   tbDocumentoPrincipal != undefined ){

		    	if( hiddenCampoPrincipal != null && 
		    		hiddenCampoPrincipal != undefined &&
		    		hiddenCampoPrincipal.value != '' ){

					alert('Somente pode ter um Documento Principal.');
					isValido = false;
					return;
					
		 		}
			
		    }

		}
		
		//validar se selecionou nivel de acesso
		var cbHipoteseLegal = document.getElementById('hipoteseLegal'+numero);
		var cbNivelAcesso = document.getElementById('nivelAcesso'+numero);
		var strNivelAcesso = cbNivelAcesso.value;
		var strHipoteseLegal = '';

		if( cbHipoteseLegal != null && cbHipoteseLegal != undefined ){
		   strHipoteseLegal = cbHipoteseLegal.value;
		}
		
		//verificar se marcou o formato de documento
		var complemento = '';

		if(numero == '1'){ complemento = 'Principal'; }
		else if(numero == '2'){ complemento = 'Essencial'; }
		else if(numero == '3'){ complemento = 'Complementar'; }
		
		var fileArquivo = document.getElementById('fileArquivo' + complemento);
		var cbTipo = document.getElementById('tipoDocumento' + complemento);
		var strFormatoDocumento = '';
		var cbTipoConferencia = document.getElementById('TipoConferencia' + complemento);
		var strTipoConferencia = document.getElementById('TipoConferencia' + complemento).value;

		var radios = document.getElementsByName('formatoDocumento'+complemento);
		for (var i = 0, length = radios.length; i < length; i++) {

		    if (radios[i].checked) {		    
		    	strFormatoDocumento = radios[i].value;
		        break;
		    }
		}

		//validar campo Complemento
		var strTxtComplemento = document.getElementById('complemento' + complemento).value;

		//verificar se algum arquivo foi selecionado para o upload
		if( fileArquivo.value == '' ){
			   alert('Informe o arquivo para upload.');
			   isValido = false;
			   fileArquivo.focus();
			   return;
		}

		//validar campo/combo Tipo (apenas para Essencial ou Complementar)
		else if( ( numero == '2' || numero == '3' ) && ( cbTipo == undefined || cbTipo == null || cbTipo.value == '')  ){
			   alert('Informe o Tipo.');
			   isValido = false;
			   cbTipo.focus();
			   return;	
		}

		if(numero == '2'){

			docTipoEssencial.push(cbTipo.value);

			//validar campo Complemento
			if( strTxtComplemento == ""){
				   alert('Informe o Complemento.');
				   isValido = false;
				   document.getElementById('complemento' + complemento).focus();
				   return;
			}
				
		}

		//validar campo Complemento
		if( strTxtComplemento == ""){
			   alert('Informe o Complemento.');
			   isValido = false;
			   document.getElementById('complemento' + complemento).focus();
			   return;
		}
		
		//validar campo nivel de acesso
		else if( strNivelAcesso == ""){
		   alert('Informe o Nível de Acesso.');
		   isValido = false;
		   cbNivelAcesso.focus();
		   return;
		}

		//se informou Nivel de Acesso restrito, entao precisa informar tambem a hipotese legal
		else if( ( cbHipoteseLegal != null && cbHipoteseLegal != undefined ) && strNivelAcesso == '1' && strHipoteseLegal == ''){

			alert('Informe a Hipótese Legal.');
			isValido = false;
		   cbHipoteseLegal.focus();	
		   return;

		}

		else if( strFormatoDocumento == ''){
			alert('Informe o Formato do Documento.');
			isValido = false;
			return;
		}
		
		//se marcou formato de documento Digitalizado, verificar se selecione o tipo de conferencia
		else if( strFormatoDocumento == 'digitalizado' && strTipoConferencia == '' ){
			alert('Informe a Conferência com o documento digitalizado.');
			isValido = false;
			cbTipoConferencia.focus();
			return;
		}

		//validar tamanho do arquivo no lado server side apenas	
		if( isValido  ){

			if(numero == '1'){ objPrincipalUpload.executar(); }
			else if(numero == '2'){ objEssencialUpload.executar(); return true; }
			else if(numero == '3'){ objComplementarUpload.executar(); }
		  
		}
		
	} catch(err) {
	    alert(" Erro: " + err);
	    console.log(err.stack);
	}
	
}

//para uso em um caso excepcional do tipo essencial
function validarUploadArquivoEssencial(){
	
	try {

		var numero = '2';
		var isValido = true;
		
		//validar se selecionou nivel de acesso
		var cbHipoteseLegal = document.getElementById('hipoteseLegal'+numero);
		var cbNivelAcesso = document.getElementById('nivelAcesso'+numero);
		var strNivelAcesso = cbNivelAcesso.value;
		var strHipoteseLegal = '';

		if( cbHipoteseLegal != null && cbHipoteseLegal != undefined ){
		   strHipoteseLegal = cbHipoteseLegal.value;
		}
		
		//verificar se marcou o formato de documento
		var complemento = 'Essencial';
		var fileArquivo = document.getElementById('fileArquivo' + complemento);
		var cbTipo = document.getElementById('tipoDocumento' + complemento);
		var strFormatoDocumento = '';
		var cbTipoConferencia = document.getElementById('TipoConferencia' + complemento);
		var strTipoConferencia = document.getElementById('TipoConferencia' + complemento).value;

		var radios = document.getElementsByName('formatoDocumento'+complemento);
		for (var i = 0, length = radios.length; i < length; i++) {

		    if (radios[i].checked) {		    
		    	strFormatoDocumento = radios[i].value;
		        break;
		    }
		}

		//validar campo Complemento
		var strTxtComplemento = document.getElementById('complemento' + complemento).value;

		//verificar se algum arquivo foi selecionado para o upload
		if( fileArquivo.value == '' ){
			   alert('Informe o arquivo para upload.');
			   isValido = false;
			   fileArquivo.focus();
			   return;
		}

		//validar campo/combo Tipo (apenas para Essencial ou Complementar)
		else if( cbTipo == undefined || cbTipo == null || cbTipo.value == '' ){
			   alert('Informe o Tipo.');
			   isValido = false;
			   cbTipo.focus();
			   return;	
		}

		docTipoEssencial.push(cbTipo.value);

		//validar campo Complemento
		if( strTxtComplemento == ""){
				   alert('Informe o Complemento.');
				   isValido = false;
				   document.getElementById('complemento' + complemento).focus();
				   return;
		}

		//validar campo Complemento
		if( strTxtComplemento == ""){
			   alert('Informe o Complemento.');
			   isValido = false;
			   document.getElementById('complemento' + complemento).focus();
			   return;
		}
		
		//validar campo nivel de acesso
		else if( strNivelAcesso == ""){
		   alert('Informe o Nível de Acesso.');
		   isValido = false;
		   cbNivelAcesso.focus();
		   return;
		}

		//se informou Nivel de Acesso restrito, entao precisa informar tambem a hipotese legal
		else if( ( cbHipoteseLegal != null && cbHipoteseLegal != undefined ) && strNivelAcesso == '1' && strHipoteseLegal == ''){

			alert('Informe a Hipótese Legal.');
			isValido = false;
		   cbHipoteseLegal.focus();	
		   return;

		}

		else if( strFormatoDocumento == ''){
			alert('Informe o Formato do Documento.');
			isValido = false;
			return;
		}
		
		//se marcou formato de documento Digitalizado, verificar se selecione o tipo de conferencia
		else if( strFormatoDocumento == 'digitalizado' && strTipoConferencia == '' ){
			alert('Informe a Conferência com o documento digitalizado.');
			isValido = false;
			cbTipoConferencia.focus();
			return;
		}

		return isValido;
		
	} catch(err) {
	    alert(" Erro: " + err);
	    console.log(err.stack);
	}
	
}

function getStrTipoDocumento( idItem, complemento ){

	var options = document.getElementById('tipoDocumento'+complemento).options;
	var texto = '';
    for(var i=0;i < options.length;i++){
      if (options[i].value == idItem ){
    	texto = options[i].text;
        break;
      }
    }
	
	return texto;
	
}

function limparCampoUpload( numero ){

	//verificar se marcou o formato de documento
	var complemento = '';

	if(numero == '1'){ complemento = 'Principal'; }
	else if(numero == '2'){ complemento = 'Essencial'; }
	else if(numero == '3'){ complemento = 'Complementar'; }
	
	var fileArquivo = document.getElementById('fileArquivo' + complemento);

	var strFormatoDocumento = '';
	var cbNivelAcesso = document.getElementById('nivelAcesso'+numero);
	var cbTipoConferencia = document.getElementById('TipoConferencia' + complemento);
	var strTipoConferencia = document.getElementById('TipoConferencia' + complemento).value;

	var radios = document.getElementsByName('formatoDocumento'+complemento);
	for (var i = 0, length = radios.length; i < length; i++) {

	    if (radios[i].checked) {		    
	    	strFormatoDocumento = radios[i].value;
	        break;
	    }
	}
	
	if(numero == '1'){ objPrincipalUpload.executar(); }
	else if(numero == '2'){ objEssencialUpload.executar(); }
	else if(numero == '3'){ objComplementarUpload.executar(); }

	//==================================================
	//depois que o upload for executado limpar os campos
	//==================================================
	
	//limpar o campo de upload
	fileArquivo.value = '';
	
	//limpar e ocultar camposDigitalizadoEssencial (Formato Documento)
	
	for (var i = 0, length = radios.length; i < length; i++) {

		radios[i].checked = '';
	    radios[i].checked = false;		    
    
	}
	
	//document.getElementById('camposDigitalizado'+complemento).style.display = 'none';
	
	//limpar e ocultar hipotese legal ( divhipoteseLegal1 )
	if(cbNivelAcesso.getAttribute("type")!= 'hidden'){
		document.getElementById('divhipoteseLegal'+numero).style.display = 'none';
	}

	//limpar o campo Complemento
	document.getElementById('complemento'+complemento).value = '';

	//retornar a combo "Nivel de Acesso" para a primeira opçao selecionada
	if(cbNivelAcesso.getAttribute("type")!= 'hidden'){
		cbNivelAcesso.options[0].selected='selected';
	}

	//se nao for o "Principal", resetar a seleçao da combo "Tipo"
	if(numero != '1'){
		document.getElementById('tipoDocumento'+complemento).options[0].selected='selected';				
	}

	cbTipoConferencia.options[0].selected='selected';
	document.getElementById('camposDigitalizado'+complemento).style.display = 'none';
	document.getElementById('camposDigitalizado'+complemento+'Botao').style.display = 'block';
	
}

function validarQtdArquivosComplementar(){

	try {

		objComplementarUpload.executar();
		
	} catch(err) {
	    alert(" Erro: " + err);
	    console.log(err.stack);
	}
	
}

function validarQtdArquivos(){

	try {
		objUpload.executar();
	} catch(err) {
	    alert(" Erro: " + err);
	    console.log(err.stack);
	}
	
}

function validarQtdArquivosPrincipal(){

	try {
	  //alert('teste principal');
	  objPrincipalUpload.executar();
	} catch(err) {
	    alert(" Erro: " + err);
	    console.log(err.stack);
	}
}

function abrirJanelaDocumento( ){

	 var windowFeatures = "location=1,status=1,resizable=1,scrollbars=1";

	infraAbrirJanela('<?=PaginaSEIExterna::getInstance()->formatarXHTML(
    		           SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=editor_peticionamento_montar&id_serie=' . $objTipoProcDTO->getNumIdSerie() ))?>',
   	             'cadastrarInteressado',
   	             780,
   	             600,
   	             windowFeatures, //options
   	             true); //modal 
}

function receberInteressado( arrDadosInteressado, InteressadoCustomizado ){

	// Find a <table> element with id="myTable":
	var table = document.getElementById("tbInteressado");

	// Create an empty <tr> element and add it to the 1st position of the table:
	var row = table.insertRow(-1);
	row.className = 'infraTrClara'; 
	// Insert new cells (<td> elements) at the 1st and 2nd position of the "new" <tr> element:
	
	//CPF/CNPJ 	
	var cell1 = row.insertCell(0);
	cell1.className = 'infraTdSetaOrdenacao';
	
	//Nome/Razão social 
	var cell2 = row.insertCell(1);
	cell2.className = 'infraTdSetaOrdenacao';
	
	//Ações
	var cell3 = row.insertCell(2);
	cell3.className = 'infraTdSetaOrdenacao';

	//infraTrClara
	//infraTdSetaOrdenacao
	cell1.innerHTML = "NEW CELL1";
	cell2.innerHTML = "NEW CELL2";
	
	if( InteressadoCustomizado != null && InteressadoCustomizado == true ){
	  cell3.innerHTML = " customizado ";
	} else {
		cell3.innerHTML = " selecionado ";
	}

	infraEfeitoTabelas();
	
}

function addDocumento( docCustomizado ){

	//pegar valor da combo nivel de acesso nivelAcesso1
	var nivelAcessoCombo1 = document.getElementById("nivelAcesso1");
	var txtNivelAcessoCombo1 = nivelAcessoCombo1.options[nivelAcessoCombo1.selectedIndex].text;

	//pegar valor da hipotese legal
	var hipoteseCombo1 = document.getElementById("hipoteseLegal1");
	var valorHipoteseLegal = hipoteseCombo1.options[hipoteseCombo1.selectedIndex].text;
	
	//pegar data
	var data=new Date()
	var dia=data.getDate();
	var mes=data.getMonth();
	var ano=data.getFullYear();
	dataFormatada = dia + '/' + (mes++) + '/' + ano;
	
	//pegar tamanho
	var tamanhoFormatado = "tamanho";

	//montar nome documento
	var nomeDocumento = "nm documento";
	
	// Find a <table> element with id="myTable":
	var table = document.getElementById("tbDocumentoPrincipal");

	// Create an empty <tr> element and add it to the 1st position of the table:
	var row = table.insertRow(-1);
	row.className = 'infraTrClara';
	
	// Insert new cells (<td> elements) at the 1st and 2nd position of the "new" <tr> element:
	
	//Nome do arquivo	
	var cell1 = row.insertCell(0);
	cell1.className = 'infraTdSetaOrdenacao';

	//Data	
	var cell2 = row.insertCell(1);
	cell2.className = 'infraTdSetaOrdenacao';

	//Tamanho	
	var cell3 = row.insertCell(2);
	cell3.className = 'infraTdSetaOrdenacao';
	
	//Documento	
	var cell4 = row.insertCell(3);
	cell4.className = 'infraTdSetaOrdenacao';
	
	//Nível de acesso	
	var cell5 = row.insertCell(4);
	cell5.className = 'infraTdSetaOrdenacao';
	
	//Ações
	var cell6 = row.insertCell(5);
	cell6.className = 'infraTdSetaOrdenacao';

	cell1.innerHTML = nomeDocumento;
	cell1.align='center';
	
	cell2.innerHTML = dataFormatada;
	cell2.align='center';

	cell3.innerHTML = tamanhoFormatado;
	cell3.align='center';
	
	cell4.innerHTML = nomeDocumento;
	cell4.align='center';
	
	cell5.innerHTML = txtNivelAcessoCombo1;
	cell5.align='center';

	if( docCustomizado != null && docCustomizado == true ){
	  cell6.innerHTML = " customizado ";
	} else {
		cell6.innerHTML = " selecionado ";
	}

	cell6.align='center';

	infraEfeitoTabelas();
	
}

function deleteRow(btn) {
  var row = btn.parentNode.parentNode;
  row.parentNode.removeChild(row);
}

function addDocumentoComplementar( docCustomizado ){

	//pegar valor da combo nivel de acesso nivelAcesso1
	var nivelAcessoCombo2 = document.getElementById("nivelAcesso2");
	var txtNivelAcessoCombo2 = nivelAcessoCombo2.options[nivelAcessoCombo2.selectedIndex].text;

	//pegar valor da hipotese legal
	var hipoteseCombo2 = document.getElementById("hipoteseLegal2");
	var valorHipoteseLegal2 = hipoteseCombo2.options[hipoteseCombo2.selectedIndex].text;
	
	// Find a <table> element with id="myTable":
	//var table = document.getElementById("tbDocumentoComplementar");
	var table = document.getElementById("tblAnexos");

	// Create an empty <tr> element and add it to the 1st position of the table:
	var row = table.insertRow(-1);
	row.className = 'infraTrClara';

	// Insert new cells (<td> elements) at the 1st and 2nd position of the "new" <tr> element:
	
	//Nome do arquivo	
	var cell1 = row.insertCell(0);
	cell1.align = 'center';
	cell1.className = 'infraTdSetaOrdenacao';

	//Data	
	
	var data=new Date()
	var dia=data.getDate();
	var mes=data.getMonth();
	var ano=data.getFullYear();
	data = dia + '/' + (mes++) + '/' + ano;

	var cell2 = row.insertCell(1);
	cell2.style.width = '120';
	cell2.align = 'center';
	cell2.className = 'infraTdSetaOrdenacao';

	//Tamanho	
	var cell3 = row.insertCell(2);
	cell3.align = 'center';
	cell3.className = 'infraTdSetaOrdenacao';
	
	//Documento	
	var cell4 = row.insertCell(3);
	cell4.align = 'center';
	cell4.className = 'infraTdSetaOrdenacao';
	
	//Nível de acesso	
	var cell5 = row.insertCell(4);
	cell5.align = 'center';
	cell5.className = 'infraTdSetaOrdenacao';
	
	//Ações
	var cell6 = row.insertCell(5);
	cell6.align = 'center';
	cell6.className = 'infraTdSetaOrdenacao';

	cell1.innerHTML = "NEW CELL1";
	cell2.innerHTML = data;
	cell3.innerHTML = "NEW CELL3";
	cell4.innerHTML = "NEW CELL4";
	cell5.innerHTML = txtNivelAcessoCombo2;

	if( docCustomizado != null && docCustomizado == true ){
	  cell6.innerHTML = " customizado ";
	} else {
		cell6.innerHTML = " selecionado ";
	}

	infraEfeitoTabelas();
}

function addFormatoDocumento(){
	alert('Formato documento');
}

//função de apoio para debug
function dump(obj) {

    var out = '';

    for (var i in obj) {
        out += i + ": " + obj[i] + "\n";
    }

    alert(out);

}

function validarFormulario(){
	
	//valida campo especificação
	var textoEspecificacao = document.getElementById("txtEspecificacao").value;
	var cbUF = document.getElementById("selUFAberturaProcesso");	
	var ufSelecionada = '';

	var selInteressados = document.getElementById("selInteressados");	

	if( cbUF != undefined && cbUF != null ){ 
		ufSelecionada = cbUF.value;
	}
	
	if( textoEspecificacao == '' ){
      alert('Informe a Especificação.');
      document.getElementById("txtEspecificacao").focus();
      return false;      
	}

	if( cbUF != undefined && cbUF != null && ufSelecionada == '' ){
	      alert('Informe a UF em que o processo deve ser aberto:');
	      cbUF.focus();
	      return false;      
	}

	if( selInteressados != undefined && selInteressados != null && selInteressados.value == '' ){
	      alert('Informe o(s) Interessado(s).');
	      selInteressados.focus();
	      return false;      
	}		 

	//aplicando validações relacionadas ao documento principal
	var fileArquivoPrincipal = document.getElementById('fileArquivoPrincipal');
	var complementoPrincipal = document.getElementById('complementoPrincipal');
	var nivelAcessoPrincipal = document.getElementById('nivelAcesso1');
	var hipoteseLegalPrincipal = document.getElementById('hipoteseLegal1');
		
	//validando seleçao de nivel de acesso principal e hipotese legal principal
	var tbDocumentoPrincipal = document.getElementById('tbDocumentoPrincipal');

	//se for documento principao do tipo externo, só validar complemento, 
	// nivel de acesso e hipotese legal SE a grid estiver ainda sem nenhum documento
	if( tbDocumentoPrincipal != null && 
		tbDocumentoPrincipal != undefined ){

		var hdnDocPrincipal = document.getElementById('hdnDocPrincipal').value;
		
		if( hdnDocPrincipal == "" && fileArquivoPrincipal.value == ''){
			alert('Informe o Documento Principal.');
			fileArquivoPrincipal.focus();
			return false;
			
		} else if( hdnDocPrincipal == "" && complementoPrincipal.value == ''){
			alert('Informe o Complemento.');
			complementoPrincipal.focus();
			return false;
			
		} else if( hdnDocPrincipal == "" && nivelAcessoPrincipal.value == ''){
			alert('Informe o Nível de Acesso.');
			nivelAcessoPrincipal.focus();
			return false;
			
		} else if( hdnDocPrincipal == "" && nivelAcessoPrincipal.value == '1' && hipoteseLegalPrincipal.value == ''){
			alert('Informe a Hipótese Legal.');
			hipoteseLegalPrincipal.focus();
			return false;
		}
	
	} 

	//se for documento gerado sempre valida complemento, nivel de acesso e hipotese legal
	else {
		
		if( nivelAcessoPrincipal.value == ''){
			alert('Informe o Nível de Acesso.');
			nivelAcessoPrincipal.focus();
			return false;
			
		} else if( nivelAcessoPrincipal.value == '1' && hipoteseLegalPrincipal.value == ''){
			alert('Informe a Hipótese Legal.');
			hipoteseLegalPrincipal.focus();
			return false;
		}
		
	}

	//validar se pelo menos um doc principal foi adicionado CASO
	//a grid de doc principal exista na tela (ou seja, quando a parametrização)
	//informar doc principal do tipo Externo

	if( tbDocumentoPrincipal != null && 
		tbDocumentoPrincipal != undefined ){

		var strHashPrincipal = document.getElementById('hdnDocPrincipal').value;
		//alert( strHashPrincipal );

		if( strHashPrincipal == ''){
			alert('Informe o Documento Principal.');
			document.getElementById('fileArquivoPrincipal').focus();
			return false;
		}
	} 

	//caso doc principal seja do tipo "Gerado", fazer requisição AJAX
	//para validar se usuário salvou na sessao algum conteudo para o documento
	//caso nao tenha conteudo obrigar usuario a informar
	else {

		/*
		var conteudoDocumento = "";
		
		$( document ).ready(function() {
		
			var  formData = "";  
	   		
			$.ajax({
			    url : "",
			    type: "POST",
			    data : formData,
			    success: function(data, textStatus, jqXHR)
			    {

			        conteudoDocumento = data;
			    	
			    },
			    
			    error: function (jqXHR, textStatus, errorThrown)
			    {
				   alert('Erro ao validar documento principal.'); 	
				   console.log('Erro' + textStatus);
			       return;
			    }
			});
		});

		alert( conteudoDocumento ); 
		*/
		
	}
	
	//valida se todos os tipos essenciais contem na lista
	
	var comboTipoEssencial = document.getElementById('tipoDocumentoEssencial');
	
	if(comboTipoEssencial!=null){
		  var retornoUploadEssencial = false;
		  var validarTipoEssenc = true;
		  var strHashEssencial = document.getElementById('hdnDocEssencial').value;

		  //caractere de quebra de linha/registro
		  var arrHashEssencial = strHashEssencial.split('¥');
		  var qtdX = arrHashEssencial.length;
		  
		  if( qtdX == 1 && arrHashEssencial[0] == "" ){

			  arrHashEssencial = Array();
			  arrHashEssencial[0] = strHashEssencial;
			  
		  }	
		  			  
		  var local = 9;
		  var tiposIncluidos = Array();

		//so vai adicionar no array dos incluidos quando tem registros na grid
		  if( strHashEssencial != "") {		
		  
			  for(var i = 0; i < qtdX ; i++ ){
				  	
				  //caractere de quebra de coluna/campo	
				  var arrLocal = arrHashEssencial[i].split('±');	
				  var tipo = arrLocal[local];
	
				  if(tiposIncluidos.indexOf(tipo) <= -1){
					  tiposIncluidos.push(arrLocal[local]);
				  }
				  
			  }
		  
		  } else {
			  //grid vazia e campos de upload de essencial nao preenchidos	
			  retornoUploadEssencial = validarUploadArquivoEssencial();

			  if( retornoUploadEssencial != true ){
			     return false;
			  }
	      }
		  
		  var tamnhoOptions = comboTipoEssencial.options.length-1;
		  
		  if(tiposIncluidos.length == 0 || tamnhoOptions != tiposIncluidos.length){
			 validarTipoEssenc = false;
		  }
		  
	      if(!validarTipoEssenc){
	    	  alert('Deve adicionar pelo menos um Documento Essencial para cada Tipo.');
		      document.getElementById('fileArquivoEssencial').focus();
		      return false;      
	      }
	} 
	
	return true;
	
}

function abrirPeticionar(){

    if( validarFormulario() ) {
	
         infraAbrirJanela('<?=PaginaSEIExterna::getInstance()->formatarXHTML(SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?id_tipo_procedimento=' . $_GET['id_tipo_procedimento'] .'&acao=peticionamento_usuario_externo_concluir&tipo_selecao=2'))?>',
    	             'concluirPeticionamento',
    	             770,
    	             464,
    	             '', //options
    	             false); //modal     
    } 
}

function abrirCadastroInteressado(){

	//so abrir o CPF/CNPJ tiver sido informado e NAO pertencer a um contato cadastrado
	var txtcpf = document.getElementById("txtCPF").value;
	var txtcnpj = document.getElementById("txtCNPJ").value;
	var conteudo = '';
	
	var chkTipoPessoaFisica = document.getElementById("optTipoPessoaFisica").checked;
	var chkTipoPessoaJuridica = document.getElementById("optTipoPessoaJuridica").checked;

	if( chkTipoPessoaFisica ){
		conteudo = txtcpf;
	}

	else if( chkTipoPessoaJuridica ){
		conteudo = txtcnpj;
	}
	
	if( conteudo == '' ){

		if( chkTipoPessoaFisica ){
			alert('Informe o CPF.');
			document.getElementById("txtCPF").focus();
		}

		else if( chkTipoPessoaJuridica ){
			alert('Informe o CNPJ.');
			document.getElementById("txtCNPJ").focus();
		}

		return;
	} 

	//checar se o CPF/CNPJ está no formato válido e se estiver, consultar via AJAX para tentar obter um interessado cadastrado
	else {

		if( chkTipoPessoaFisica ){

			if (!infraValidarCpf(infraTrim( txtcpf ))){
				
				alert('CPF Inválido.');
				document.getElementById('txtCPF').focus();
				document.getElementById('txtNomeRazaoSocial').value = '';
				return;
			
			}
			
		}

		else if( chkTipoPessoaJuridica ){

			if (!infraValidarCnpj(infraTrim( txtcnpj ))){
				
				alert('CNPJ Inválido.');
				document.getElementById('txtCNPJ').focus();
				document.getElementById('txtNomeRazaoSocial').value = '';
				return;
			
			}
			
		}

		//se chegar aqui o CPF/CNPJ está valido, entao consultar via AJAX para ver se o contato já está cadastrado
		var  formData = "cpfcnpj=" +  conteudo;  //Name value Pair
   
		$.ajax({
		    url : "<?= $strLinkAjaxContato ?>",
		    type: "POST",
		    data : formData,
		    success: function(data, textStatus, jqXHR)
		    {
		        //data - response from server
		        
		        if( data != null && data != undefined && data != ""){
				  var obj = jQuery.parseJSON( data );
		    	  $('#txtNomeRazaoSocial').val(obj.nome);
		    	  return;
		        } 

		        else{
			      	  
					//charmar janela para cadastrar um novo interessado
					$('#txtNomeRazaoSocial').val('');

					if( chkTipoPessoaFisica ){
						var str = '<?= SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=peticionamento_interessado_cadastro&tipo_selecao=2&cpf=true') ?>';
					}

					else if( chkTipoPessoaJuridica ){
						var str = '<?= SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=peticionamento_interessado_cadastro&tipo_selecao=2&cnpj=true') ?>';
					}

					infraAbrirJanela( str, 'cadastrarInteressado', 900, 900, '', false); //modal 
					return;
					
				}
		    	
		    },
		    error: function (jqXHR, textStatus, errorThrown)
		    {
		       alert('Erro' + textStatus);
		       return;
		    }
		});
				
		//alert('Consultar CPF/CNPJ via AJAX 2');
		return;
		
	}
   	
    
}

function inicializar(){

	<? if( $objTipoProcDTO->getStrSinIIIndicacaoDiretaContato() == 'S') { ?>
	objLupaInteressados = new infraLupaSelect('selInteressados','hdnInteressados','<?=$strLinkInteressadosSelecao?>');   

	objAutoCompletarInteressado = new infraAjaxAutoCompletar('hdnIdInteressado','txtInteressado','<?=$strLinkAjaxInteressado?>');
    objAutoCompletarInteressado.limparCampo = true;
    //objAutoCompletarInteressado.tamanhoMinimo = 3;

    objAutoCompletarInteressado.prepararExecucao = function(){
      return 'extensao='+document.getElementById('txtInteressado').value;
    };
    
    objAutoCompletarInteressado.processarResultado = function(id,descricao,complemento){
      if (id!=''){
        var options = document.getElementById('selInteressados').options;

        for(var i=0;i < options.length;i++){
          if (options[i].value == id){
            self.setTimeout('alert(\'Interessado já consta na lista.\')',100);
            break;
          }
        }

        if (i==options.length){

        for(i=0;i < options.length;i++){
          options[i].selected = false;
        }

        opt = infraSelectAdicionarOption(document.getElementById('selInteressados'),descricao,id);
        objLupaInteressados.atualizar();
        opt.selected = true;
      }

      document.getElementById('txtInteressado').value = '';
      document.getElementById('txtInteressado').focus();
    }};
	<? } ?>

	<? if( $externo == "S" ) { ?>
	  //tem doc principal externo
	  carregarCamposDocPrincipalUpload();
	<? } ?>

	<? if( $temDocEssencial ) { ?>
	  //tem doc essencial
	  carregarCamposDocEssencialUpload();
	<? } ?>

	<? if( $temDocComplementar ) { ?>
	  //tem doc complementar
	  carregarCamposDocComplementarUpload();
	<? } ?>
	
	infraEfeitoTabelas();  
	document.getElementById('txtEspecificacao').focus();

	//Preenchimento com o endereço do contexto
	  objAjaxContato = new infraAjaxComplementar(null,'<?=$strLinkAjaxContato?>');
	  objAjaxContato.limparCampo = false;
	  
	  objAjaxContato.prepararExecucao = function(){
		   return 'cpfCnpj='+document.getElementById('txtCPF').value;
      }

	  objAjaxContato.processarResultado = function(arr){

		//console.log( arr );	
		//alert( arr );	
		//inicio processo ajax
		/*	
	    document.getElementById('txtTelefone').value = '';
	    document.getElementById('txtFax').value = '';
	    document.getElementById('txtEmail').value = '';
	    document.getElementById('txtSitioInternet').value = '';
	    document.getElementById('txtEndereco').value = '';
	    document.getElementById('txtBairro').value = '';
	    document.getElementById('txtNomePais').value = '';
	    document.getElementById('txtSiglaEstado').value = '';
	    document.getElementById('txtNomeCidade').value = '';
	    document.getElementById('txtCep').value = '';

	    if (arr!=null){
	      if (arr['Telefone']!=undefined){
	        document.getElementById('txtTelefone').value = arr['Telefone'];
	      }
	      if (arr['Fax']!=undefined){
	        document.getElementById('txtFax').value = arr['Fax'];
	      }
	      if (arr['Email']!=undefined){
	        document.getElementById('txtEmail').value = arr['Email'];
	      }
	      if (arr['SitioInternet']!=undefined){
	        document.getElementById('txtSitioInternet').value = arr['SitioInternet'];
	      }
	      if (arr['Endereco']!=undefined){
	        document.getElementById('txtEndereco').value = arr['Endereco'];
	      }
	      if (arr['Bairro']!=undefined){
	        document.getElementById('txtBairro').value = arr['Bairro'];
	      }

	      if (arr['SiglaEstado']!=undefined){
	        document.getElementById('txtSiglaEstado').value = arr['SiglaEstado'];
	      }

	      if (arr['NomeCidade']!=undefined){
	        document.getElementById('txtNomeCidade').value = arr['NomeCidade'];
	      }

	      if (arr['NomePais']!=undefined){
	        document.getElementById('txtNomePais').value = arr['NomePais'];
	        paisEstadoCidadeRI0520(document.getElementById('txtNomePais'),arr['NomePais']);
	      }

	      if (arr['Cep']!=undefined){
	        document.getElementById('txtCep').value = arr['Cep'];
	      }
	    } */

	    //fim processo ajax
	  }
}

function getStrNivelAcesso( nivel ){

	//alert(nivel);
	
	if( nivel == '0'){ return 'Público'; }
	else if( nivel == '1'){ return 'Restrito'; }
	else if( nivel == '' ) { return '-'; }
}

//campo de upload de documentos principais
function carregarCamposDocPrincipalUpload(){

	try {
	
	  objPrincipalUpload = new infraUpload('frmDocumentoPrincipal','<?=$strLinkUploadDocPrincipal?>', true);

	  objPrincipalUpload.finalizou = function(arr){

      var nomeUpload = arr['nome_upload'];
      var nome = arr['nome'];
      var dataHora = arr['data_hora'];
      var tamanho = arr['tamanho'];
      var dataHoraFormatada = '<?= time() ?>';
      var tamanhoFormatado = infraFormatarTamanhoBytes(arr['tamanho']);

      //Tamanho
      var tamanhoMb = tamanho/1024/1024; 
      var tamanhoPermitidoMb = document.getElementById('hdnTamArquivoPrincipal').value.toLowerCase().replace(' mb','');
      if(tamanhoMb>tamanhoPermitidoMb){
            alert('Arquivo com tamanho maior que o permitido.'); 
            return false;
      }
		
    //concatenacao de "Tipo" e "Complemento"
	  var cbTpoPrincipal = document.getElementById('tipoDocumentoPrincipal');
      var strComplemento = document.getElementById('complementoPrincipal').value;	
      var documento = getStrTipoDocumento( cbTpoPrincipal.value, 'Principal' ) + ' ' + strComplemento;

      var nivelAcesso = getStrNivelAcesso( document.getElementById('nivelAcesso1').value );

      //hipoteseLegal1
      var hipoteseLegal = '';
      var cbHipotese = document.getElementById('hipoteseLegal1');
      
      if( cbHipotese != null && cbHipotese != undefined ){
    	  hipoteseLegal = cbHipotese.value;
      }
      
      var formatoDocumento = $('input[name="formatoDocumentoPrincipal"]:checked').val();
      var formatoDocumentoLbl = 'Nato-digital';
      if(formatoDocumento != 'nato'){
    	  formatoDocumentoLbl = 'Digitalizado';
      }

      //TipoConferenciaPrincipal / TipoConferenciaEssencial
      var tipoConferencia = document.getElementById('TipoConferenciaPrincipal').value;

	  objTabelaDocPrincipal.adicionar([ nome , dataHora ,  tamanhoFormatado , documento , nivelAcesso , hipoteseLegal, formatoDocumento, tipoConferencia, nomeUpload, cbTpoPrincipal.value,strComplemento,formatoDocumentoLbl, '' ]);
	  
	  var strHashPrincipal = document.getElementById('hdnDocPrincipal').value;
	  var arrHashPrincipal = strHashPrincipal.split('±');

	  <? $linkBase = PaginaSEIExterna::getInstance()->formatarXHTML(SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=peticionamento_usuario_externo_download&id_tipo_procedimento=' . $_GET['id_tipo_procedimento'] . '&id_orgao_acesso_externo=0')); ?>  
	  var urlBase = "<?= $linkBase ?>";
  		
	  document.getElementById('hdnNomeArquivoDownload').value = arrHashPrincipal[0];
	  document.getElementById('hdnNomeArquivoDownloadReal').value = arrHashPrincipal[1];
	  
	  objTabelaDocPrincipal.adicionarAcoes(
		arr['nome'], 
		"", 
		//"<a href='#' onclick=\"downloadArquivo( ' "+ urlBase +"')\"><img title='Baixar anexo' alt='Baixar arquivo' src='/infra_css/imagens/download.gif' class='infraImg' /></a>",
		false,
		true);

	  //limpar campo do upload
	  document.getElementById("fileArquivoPrincipal").value = '';

	  limparCampoUpload('1');

	  //aplicando valign='middle' nas colunas  da tabela 
	  //(necessário especificamente para alinhar coluna ações)
	  var table = document.getElementById("tbDocumentoPrincipal");

	  for (var i = 0, row; row = table.rows[i]; i++) {

		   for (var j = 0, col; col = row.cells[j]; j++) {
		    col.setAttribute("valign","middle");
		   }
		     
	  }

	}

	  objPrincipalUpload.validar = function(arr){

		  //INICIO VALIDACAO EXTENSOES
		  var arrExtensoesPermitidas = [<?=$strSelExtensoesPrin?>];
		  if ( $("#fileArquivoPrincipal").val().replace(/^.*\./, '')!='' && $.inArray( $("#fileArquivoPrincipal").val().replace(/^.*\./, '') , arrExtensoesPermitidas ) == -1 ) {
			  alert("O arquivo selecionado não é permitido.\nSomente são permitidos arquivos com as extensões:\n<?=preg_replace("%'%"," ",$strSelExtensoesPrin)?> .");
			  return false;
		  }
		  //FIM VALIDACAO EXTENSOES
		  	
		  var arquivoPrincipal = document.getElementById('fileArquivoPrincipal').value;
		  var ext = (arquivoPrincipal.substring(arquivoPrincipal.lastIndexOf(".")).toLowerCase()).split('.')[1];

		  var extPermitida = false;
		  var extensoes = $('#hdnArquivosPermitidos').val();
		  //alert( 'Extensoes :: ' + extensoes ); 
		  var obj = JSON.parse($('#hdnArquivosPermitidos').val());

		  for (var index in obj) {

				 if(obj[index] ==  ext){
				    extPermitida = true;
				 }
		  }

		//INICIO VALIDACAO EXTENSOES  
	    if(ext != undefined && ext != '' && !extPermitida){
	        document.getElementById('fileArquivoPrincipal').value = '';
			alert('A extensão do arquivo não é permitida.');
	    }
	    //FIM VALIDACAO EXTENSOES
	    
	    //validar tamanho maximo de arquivo
	    
	    //validar quantidade registros na tabela?
	    //if (document.getElementById('tblAnexos').rows.length>1){
	    //alert('Apenas um anexo pode ser adicionado no cadastro.');
	    //return false;
	    //}
	    
	 	return extPermitida;

	  }
	  
	  //Monta tabela de anexos
	  objTabelaDocPrincipal = new infraTabelaDinamica('tbDocumentoPrincipal','hdnDocPrincipal',false,false);
	  objTabelaDocPrincipal.gerarEfeitoTabela=true;
	  
  } catch(err){
      alert(' ERRO ' + err);
      //console.log(err.stack);
  }
}

//campo de upload de documentos essenciais
function carregarCamposDocEssencialUpload(){

	  objEssencialUpload = new infraUpload('frmDocumentosEssenciais','<?=$strLinkUploadDocEssencial?>', true);

	  objEssencialUpload.finalizou = function(arr){

	  var nomeUpload = arr['nome_upload'];
      var nome = arr['nome'];
	  var data = arr['data'];
	  var dataHora = arr['data_hora'];
      var tamanho = arr['tamanho'];
      var dataHoraFormatada = '<?= time() ?>';
      var tamanhoFormatado = infraFormatarTamanhoBytes(arr['tamanho']);

      //Tamanho
      var tamanhoMb = tamanho/1024/1024; 
      var tamanhoPermitidoMb = document.getElementById('hdnTamArquivoEssencial').value.toLowerCase().replace(' mb','');
      if(tamanhoMb>tamanhoPermitidoMb){
            alert('Arquivo com tamanho maior que o permitido.'); 
            return false;
      }

      //Nome
      var linhas = document.getElementById('tbDocumentoEssencial').rows;
      var tamanhoInserido = 0;
      var tamanhoVerificar = 0;

      for (var i = 1; i < linhas.length; i++) {
		//Nome igual
		if (nome.toLowerCase()==linhas[i].cells[0].innerText.toLowerCase()){
			/*
			//TAMANHO
			tamanhoInserido = linhas[i].cells[2].innerText.toLowerCase().toLowerCase().replace(' Kb','');
			//arquivo em Kb
			if (tamanhoInserido != linhas[i].cells[2].innerText){
				tamanhoVerificar = tamanho/1024;
				tamanhoVerificar = parseFloat(tamanhoVerificar.toFixed(2));
			//arquivo em Mb
			}else{
				tamanhoInserido = linhas[i].cells[2].innerText.toLowerCase().toLowerCase().replace(' mb','');
				if (tamanhoInserido != linhas[i].cells[2].innerText){
					tamanhoVerificar = tamanho/1024/1024;
					tamanhoVerificar = parseFloat(tamanhoVerificar.toFixed(2));
				}else{
					tamanhoInserido=0;
				}
			}
			if (tamanhoVerificar==tamanhoInserido){
				alert('Arquivo já inserido');
	            return false;
			}
			*/
			alert('Arquivo já inserido');
            return false;
		}	
      }
      
	  //concatenacao de "Tipo" e "Complemento"
	  var cbTpoEssencial = document.getElementById('tipoDocumentoEssencial');
	  	
	  var strComplemento = document.getElementById('complementoEssencial').value;	
      var documento = getStrTipoDocumento( cbTpoEssencial.value, 'Essencial' ) + ' ' + strComplemento;
      
      var nivelAcesso = getStrNivelAcesso( document.getElementById('nivelAcesso2').value );

      var hipoteseLegal = '';
      var cbHipotese = document.getElementById('hipoteseLegal2');
      
      if( cbHipotese != null && cbHipotese != undefined ){
    	  hipoteseLegal = cbHipotese.value;
      }
      
	  //var hipoteseLegal=  ' hip legal essencial';
      var formatoDocumento = $('input[name="formatoDocumentoEssencial"]:checked').val();
      var formatoDocumentoLbl = 'Nato-digital';
      if(formatoDocumento != 'nato'){
    	  formatoDocumentoLbl = 'Digitalizado';
      }

    //TipoConferenciaPrincipal / TipoConferenciaEssencial
      var tipoConferencia = document.getElementById('TipoConferenciaEssencial').value;

      //objTabelaDocPrincipal.adicionar([ nome , dataHora ,  tamanhoFormatado , documento , nivelAcesso , hipoteseLegal, formatoDocumento, tipoConferencia, nomeUpload, cbTpoPrincipal.value, '' ]);
      objTabelaDocEssencial.adicionar([ nome , dataHora , tamanhoFormatado , documento , nivelAcesso, hipoteseLegal, formatoDocumento, tipoConferencia, nomeUpload, cbTpoEssencial.value,strComplemento, formatoDocumentoLbl, '' ]);	
      //objTabelaDocEssencial.adicionar([ nomeUpload , nomeUpload, dataHora , '4', '5', '6', '7']);		
      //objTabelaDocEssencial.adicionar([ nomeUpload , dataHora ,  tamanhoFormatado , documento , nivelAcesso , '']);
      //objTabelaDocEssencial.adicionar([ '-' , nomeUpload ,  dataHora , dataHora , tamanhoFormatado, documento, 'nivel de acesso', 'acoes' ]);
	  //objTabelaDocEssencial.adicionar([arr['nome_upload'],arr['nome'],arr['data_hora'],arr['tamanho'],infraFormatarTamanhoBytes(arr['tamanho']),'<?= time() ?>']);
	  
	  var strHashEssencial = document.getElementById('hdnDocEssencial').value;
	  var arrHashEssencial = strHashEssencial.split('±');

	  <? $linkBase = PaginaSEIExterna::getInstance()->formatarXHTML(SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=peticionamento_usuario_externo_download&id_tipo_procedimento=' . $_GET['id_tipo_procedimento'] . '&id_orgao_acesso_externo=0')); ?>  
	  var urlBase = "<?= $linkBase ?>";
  		
	  document.getElementById('hdnNomeArquivoDownload').value = arrHashEssencial[0];
	  document.getElementById('hdnNomeArquivoDownloadReal').value = arrHashEssencial[1];
	  
	  objTabelaDocEssencial.adicionarAcoes(
		arr['nome'], 
		"",
		//"<a href='#' onclick=\"downloadArquivo( ' "+ urlBase +"')\"><img title='Baixar anexo' alt='Baixar arquivo' src='/infra_css/imagens/download.gif' class='infraImg' /></a>",
		false,
		true);
		  
	  document.getElementById("fileArquivoEssencial").value = '';
	  //document.getElementById('divArquivo').style.display = 'none';

	  limparCampoUpload('2');

	  var table = document.getElementById("tbDocumentoEssencial");

	  for (var i = 0, row; row = table.rows[i]; i++) {

		   for (var j = 0, col; col = row.cells[j]; j++) {
		    col.setAttribute("valign","middle");
		   }
		     
	  }
	
	  
	}

	  objEssencialUpload.validar = function(arr){

		  //INICIO VALIDACAO EXTENSOES
		  var arrExtensoesPermitidas = [<?=$strSelExtensoesComp?>];
		  if ( $("#fileArquivoEssencial").val().replace(/^.*\./, '')!='' && $.inArray( $("#fileArquivoEssencial").val().replace(/^.*\./, '') , arrExtensoesPermitidas ) == -1 ) {
			  alert("O arquivo selecionado não é permitido.\nSomente são permitidos arquivos com as extensões:\n<?=preg_replace("%'%"," ",$strSelExtensoesComp)?> .");
			  return false;
		  }
		  //FIM VALIDACAO EXTENSOES

		  var arquivoEssencial = document.getElementById('fileArquivoEssencial').value;
		  var ext = (arquivoEssencial.substring(arquivoEssencial.lastIndexOf(".")).toLowerCase()).split('.')[1];

		  var extPermitida = false;
		  
		  var obj = JSON.parse($('#hdnArquivosPermitidosEssencialComplementar').val());

		  for (var index in obj) {

				 if(obj[index] ==  ext){
				    extPermitida = true;
				 }
		  }
		  
	    if(ext != undefined && ext != '' && !extPermitida){
	        document.getElementById('fileArquivoEssencial').value = '';
			alert('A extensão do arquivo não é permitida.');
	    }
	    
	 	return extPermitida;

	  }
	  
	  //Monta tabela de anexos
	  objTabelaDocEssencial = new infraTabelaDinamica('tbDocumentoEssencial','hdnDocEssencial',false,false);
	  objTabelaDocEssencial.gerarEfeitoTabela=true;
	
}

//campo de upload de documentos essenciais
function carregarCamposDocComplementarUpload(){

	  objComplementarUpload = new infraUpload('frmDocumentosComplementares','<?=$strLinkUploadDocComplementar?>', true);

	  objComplementarUpload.finalizou = function(arr){

	  var nomeUpload = arr['nome_upload'];
      var nome = arr['nome'];
	  var data = arr['data'];
	  var dataHora = arr['data_hora'];
      var tamanho = arr['tamanho'];
      var dataHoraFormatada = '<?= time() ?>';
      var tamanhoFormatado = infraFormatarTamanhoBytes(arr['tamanho']);

      //Tamanho
      var tamanhoMb = tamanho/1024/1024; 
      var tamanhoPermitidoMb = document.getElementById('hdnTamArquivoComplementar').value.toLowerCase().replace(' mb','');
      if(tamanhoMb>tamanhoPermitidoMb){
            alert('Arquivo com tamanho maior que o permitido.'); 
            return false;
      }

      //Nome
      var linhas = document.getElementById('tbDocumentoComplementar').rows;
      var tamanhoInserido = 0;
      var tamanhoVerificar = 0;

      for (var i = 1; i < linhas.length; i++) {
		//Nome igual
		if (nome.toLowerCase()==linhas[i].cells[0].innerText.toLowerCase()){
			/*
			//TAMANHO
			tamanhoInserido = linhas[i].cells[2].innerText.toLowerCase().toLowerCase().replace(' Kb','');
			//arquivo em Kb
			if (tamanhoInserido != linhas[i].cells[2].innerText){
				tamanhoVerificar = tamanho/1024;
				tamanhoVerificar = parseFloat(tamanhoVerificar.toFixed(2));
			//arquivo em Mb
			}else{
				tamanhoInserido = linhas[i].cells[2].innerText.toLowerCase().toLowerCase().replace(' mb','');
				if (tamanhoInserido != linhas[i].cells[2].innerText){
					tamanhoVerificar = tamanho/1024/1024;
					tamanhoVerificar = parseFloat(tamanhoVerificar.toFixed(2));
				}else{
					tamanhoInserido=0;
				}
			}
			if (tamanhoVerificar==tamanhoInserido){
				alert('Arquivo já inserido');
	            return false;
			}
			*/
			alert('Arquivo já inserido');
            return false;
		}	
      }
      
      //concatenacao de "Tipo" e "Complemento"
	  var cbTpoComplementar = document.getElementById('tipoDocumentoComplementar');
      var strComplemento = document.getElementById('complementoComplementar').value;	
      var documento = getStrTipoDocumento( cbTpoComplementar.value, 'Complementar' ) + ' ' + strComplemento;

      var nivelAcesso = getStrNivelAcesso( document.getElementById('nivelAcesso3').value );

      var hipoteseLegal = '';
      var cbHipotese = document.getElementById('hipoteseLegal3');
      
      if( cbHipotese != null && cbHipotese != undefined ){
    	  hipoteseLegal = cbHipotese.value;
      }

	  var formatoDocumento = $('input[name="formatoDocumentoComplementar"]:checked').val();
	  var formatoDocumentoLbl = 'Nato-digital';
      if(formatoDocumento != 'nato'){
    	  formatoDocumentoLbl = 'Digitalizado';
      }
	  
	  var tipoConferencia = document.getElementById('TipoConferenciaComplementar').value;

      objTabelaDocComplementar.adicionar([ nome , dataHora , tamanhoFormatado , documento , nivelAcesso, hipoteseLegal, formatoDocumento, tipoConferencia, nomeUpload, cbTpoComplementar.value, strComplemento,formatoDocumentoLbl, '' ]);	
      //objTabelaDocComplementar.adicionar([ '-' , nomeUpload ,  dataHora , dataHora , tamanhoFormatado, documento, 'nivel de acesso', 'acoes' ]);
	  //objTabelaDocComplementar.adicionar([arr['nome_upload'],arr['nome'],arr['data_hora'],arr['tamanho'],infraFormatarTamanhoBytes(arr['tamanho']),'<?= time() ?>']);
	  
	  var strHashComplementar = document.getElementById('hdnDocComplementar').value;
	  var arrHashComplementar = strHashComplementar.split('±');

	  <? $linkBase = PaginaSEIExterna::getInstance()->formatarXHTML(SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=peticionamento_usuario_externo_download&id_tipo_procedimento=' . $_GET['id_tipo_procedimento'] . '&id_orgao_acesso_externo=0')); ?>  
	  var urlBase = "<?= $linkBase ?>";
  		
	  document.getElementById('hdnNomeArquivoDownload').value = arrHashComplementar[0];
	  document.getElementById('hdnNomeArquivoDownloadReal').value = arrHashComplementar[1];
	  
	  objTabelaDocComplementar.adicionarAcoes(
		arr['nome'], 
		"",
		//"<a href='#' onclick=\"downloadArquivo( ' "+ urlBase +"')\"><img title='Baixar anexo' alt='Baixar arquivo' src='/infra_css/imagens/download.gif' class='infraImg' /></a>",
		false,
		true);
		  
	  document.getElementById("fileArquivoComplementar").value = '';

	  limparCampoUpload('3');

	  var table = document.getElementById("tbDocumentoComplementar");

	  for (var i = 0, row; row = table.rows[i]; i++) {

		   for (var j = 0, col; col = row.cells[j]; j++) {
		    col.setAttribute("valign","middle");
		   }
		     
	  }
	}

	  objComplementarUpload.validar = function(arr){

		  //INICIO VALIDACAO EXTENSOES
		  var arrExtensoesPermitidas = [<?=$strSelExtensoesComp?>];
		  if ( $("#fileArquivoComplementar").val().replace(/^.*\./, '')!='' && $.inArray( $("#fileArquivoComplementar").val().replace(/^.*\./, '') , arrExtensoesPermitidas ) == -1 ) {
			  alert("O arquivo selecionado não é permitido.\nSomente são permitidos arquivos com as extensões:\n<?=preg_replace("%'%"," ",$strSelExtensoesComp)?> .");
			  return false;
		  }
		  //FIM VALIDACAO EXTENSOES

		  var arquivoComplementar = document.getElementById('fileArquivoComplementar').value;
		  var ext = (arquivoComplementar.substring(arquivoComplementar.lastIndexOf(".")).toLowerCase()).split('.')[1];

		  var extPermitida = false;
		  var listaExtensoes = $('#hdnArquivosPermitidosEssencialComplementar').val();
		  //alert( 'Extensao enviada: ' + ext );
		  //alert( 'Extensoes permitidas: ' + listaExtensoes );
		  
		  var obj = JSON.parse( listaExtensoes );

		  for (var index in obj) {

				 if(obj[index] ==  ext){
				    extPermitida = true;
				 }
		  }
		  
	    if(ext != undefined && ext != '' && !extPermitida){
	        document.getElementById('fileArquivoComplementar').value = '';
			alert('A extensão do arquivo não é permitida.');
	    }
	    
	 	return extPermitida;

	  }
	  
	  //Monta tabela de anexos
	  objTabelaDocComplementar = new infraTabelaDinamica('tbDocumentoComplementar','hdnDocComplementar',false,false);
	  objTabelaDocComplementar.gerarEfeitoTabela=true;
	
}

function carregarComponenteLupaInteressados( tipoAcao ){

   if( tipoAcao == 'S'){
	 objLupaInteressados.selecionar(700,500);
  } else if( tipoAcao == 'R'){
	 objLupaInteressados.remover();
  }
	
}

function mascaraTexto( elem, evento ){

	//alert('entrou aqui');
	var formPeticionamento = document.getElementById('frmPeticionamentoCadastro');
	//alert(formPeticionamento.tipoPessoa.value);
	
	if(formPeticionamento.tipoPessoa.value == 'pf'){
		return infraMascaraCpf(elem, evento);
	} 
	else if(formPeticionamento.tipoPessoa.value == 'pj'){
		return infraMascaraCnpj(elem, evento);
	} 
	else {
      return false;
	}

}

function selecionarPF(){
	
  document.getElementById('descTipoPessoa').innerHTML = 'CPF:';
  document.getElementById('descNomePessoa').innerHTML = 'Nome:';	
  document.getElementById('tdDescTipoPessoa').innerHTML = 'CPF';
  document.getElementById('tdDescNomePessoa').innerHTML = 'Nome';

  document.getElementById('divSel1').style.display = 'inline';
  document.getElementById('divSel2').style.display = 'inline';

  document.getElementById('txtNomeRazaoSocial').style.display = 'inline';
  document.getElementById('btAdicionarInteressado').style.display = 'inline';
  
  document.getElementById('txtCPF').style.display = 'inline';
  //document.getElementById('txtCPF').style.width='80%';
  document.getElementById('txtCNPJ').value = '';
  document.getElementById('txtCNPJ').style.display = 'none';
  document.getElementById('btValidarCPFCNPJ').style.visibility = 'visible';

}

function selecionarPJ(){
	
  document.getElementById('descTipoPessoa').innerHTML = 'CNPJ:';
  document.getElementById('descNomePessoa').innerHTML = 'Razão Social:';
  document.getElementById('tdDescTipoPessoa').innerHTML = 'CNPJ';
  document.getElementById('tdDescNomePessoa').innerHTML = 'Razão Social';

  document.getElementById('divSel1').style.display = 'inline';
  document.getElementById('divSel2').style.display = 'inline';

  document.getElementById('txtNomeRazaoSocial').style.display = 'inline';
  document.getElementById('btAdicionarInteressado').style.display = 'inline';
  
  document.getElementById('txtCPF').style.display = 'none';
  document.getElementById('txtCPF').value = '';
  
  document.getElementById('txtCNPJ').style.display = 'inline';
  //document.getElementById('txtCNPJ').style.width='80%';
  document.getElementById('btValidarCPFCNPJ').style.visibility = 'visible';
  
}

function selecionarFormatoDigitalizadoEssencial(){
	document.getElementById("camposDigitalizadoEssencial").style.display='block';
	document.getElementById("camposDigitalizadoEssencialBotao").style.display='none';
}

function selecionarFormatoNatoDigitalEssencial(){
	document.getElementById("camposDigitalizadoEssencial").style.display='none';
	document.getElementById("camposDigitalizadoEssencialBotao").style.display='block';
	//retornando a combo para seu valor inicial
	document.getElementById("TipoConferenciaEssencial").selectedIndex=0;
}

function selecionarFormatoDigitalizadoComplementar(){
	document.getElementById("camposDigitalizadoComplementarBotao").style.display='none';
	document.getElementById("camposDigitalizadoComplementar").style.display='block';
	//retornando a combo para seu valor inicial
	document.getElementById("TipoConferenciaComplementar").selectedIndex=0;
}

function selecionarFormatoNatoDigitalComplementar(){
	document.getElementById("camposDigitalizadoComplementar").style.display='none';
	document.getElementById("camposDigitalizadoComplementarBotao").style.display='block';
}

function selecionarFormatoDigitalizadoPrincipal(){
	document.getElementById("camposDigitalizadoPrincipalBotao").style.display='none';
	document.getElementById("camposDigitalizadoPrincipal").style.display='block';
	//retornando a combo para seu valor inicial
	document.getElementById("TipoConferenciaPrincipal").selectedIndex=0;
}

function selecionarFormatoNatoDigitalPrincipal(){
	document.getElementById("camposDigitalizadoPrincipalBotao").style.display='block';
	document.getElementById("camposDigitalizadoPrincipal").style.display='none';
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

function exibirAjudaCaso1(){
	alert('Para o Tipo de Processo escolhido o Interessado do processo a ser aberto somente pode ser o próprio Usuário Externo logado no sistema.'); 
}

function exibirAjudaCaso2(){
	alert('Para o Tipo de Processo escolhido é possível adicionar os Interessados no processo a ser aberto por meio da indicação de CPF ou CNPJ válidos, devendo complementar seus cadastros caso necessário.'); 
}

function exibirAjudaCaso3(){
	alert('Para o Tipo de Processo escolhido é possível adicionar os Interessados no processo a partir da base de Interessados já existente do órgão, devendo complementar seus cadastros caso necessário.'); 
}

function exibirAjudaFormatoDocumento(){
	alert('Selecione a opção “Nato-digital” se o arquivo a ser carregado foi criado originalmente em meio eletrônico.\n\n' + 
		  'Selecione a opção “Digitalizado” somente se o arquivo a ser carregado foi produzido da digitalização de um documento em papel.');
}

function downloadArquivo( urlBaseDownload ){

    var actionAnterior = document.getElementById("frmPeticionamentoCadastro").action;
    var targetAnterior = document.getElementById("frmPeticionamentoCadastro").target;
    
	document.getElementById("frmPeticionamentoCadastro").action = urlBaseDownload;
	document.getElementById("frmPeticionamentoCadastro").target="_blank";
	document.getElementById("frmPeticionamentoCadastro").submit();

	document.getElementById("frmPeticionamentoCadastro").action = actionAnterior;
	document.getElementById("frmPeticionamentoCadastro").target=targetAnterior;
		
}

function concluirAssinarPeticionamento(){

	//alert('Finalizar o processo');
	var formCadastro = document.getElementById('frmPeticionamentoCadastro');
	formCadastro.target = "concluirPeticionamento";
	formCadastro.submit();
}

window.CallParent = function() {
    //alert(" Parent window Alert");
    concluirAssinarPeticionamento();
}

function selectNivelAcesso( idNivelAcesso, idHipoteseLegal ){

      var valorSelectNivelAcesso = document.getElementById(idNivelAcesso).value;
      
      if( valorSelectNivelAcesso == '1' ){
			//mostrar combo do nivel de acesso
    	   document.getElementById(idHipoteseLegal).selectedIndex=0;
    	   document.getElementById('div'+idHipoteseLegal).style.display='block';      
      }
      else{
           //ocultar combo do nivel de acesso e limpar a seleção da combo
    	  document.getElementById(idHipoteseLegal).selectedIndex=0;
    	  document.getElementById('div'+idHipoteseLegal).style.display='none';
    	  
      }
	
}

function adicionarInteressadoValido(){

	var txtNomeRazaoSocial = document.getElementById("txtNomeRazaoSocial").value;
	var chkTipoPessoaFisica = document.getElementById("optTipoPessoaFisica").checked;
	var chkTipoPessoaJuridica = document.getElementById("optTipoPessoaJuridica").checked;
	
	//checar se o Nome / Razao Social foi preenchido
	if( txtNomeRazaoSocial == ''){

		if( chkTipoPessoaFisica ){
			alert('Informe o Nome.');
			return;
		}

		else if( chkTipoPessoaJuridica ){
			alert('Informe a Razão Social.');
			return;
		}
		
	}

	//adicionar o interessado na grid
	var arrDadosInteressadoValido = [];
	var bolInteressadoCustomizado = false;
	receberInteressado( arrDadosInteressadoValido , bolInteressadoCustomizado );
	
}
</script>