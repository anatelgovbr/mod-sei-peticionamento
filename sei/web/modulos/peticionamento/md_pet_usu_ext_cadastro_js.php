<?
/**
 * ANATEL
 *
 * 01/08/2016 - criado por marcelo.bezerra@cast.com.br - CAST
 * 26/08/2024 - Atualização por gabrielg.colab - SPASSU
 *
 * Funções de JS para cadastro de peticionamento de usuario externo
 * Essa página é incluida na página principal do cadastro de peticionamento
 *
 * Documento com este mesmo nome de arquivo já foi adicionado.
 *
 */

$strLinkAnexos = SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=md_pet_usu_ext_upload_anexo&id_tipo_procedimento=' . $_GET['id_tipo_procedimento'] . '&id_orgao_acesso_externo=0');

$strLinkPrincipal = SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=peticionamento_usuario_externo_upload_principal&id_tipo_procedimento=' . $_GET['id_tipo_procedimento'] . '&id_orgao_acesso_externo=0');

//Ação para upload de documento principal
$strLinkUploadDocPrincipal = SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=md_pet_usu_ext_upload_doc_principal');

//Ação para upload de documento essencial
$strLinkUploadDocEssencial = SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=md_pet_usu_ext_upload_doc_essencial');

//Ação para upload de documento complementar
$strLinkUploadDocComplementar = SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=md_pet_usu_ext_upload_doc_complementar');

//Ação para apagar o arquivo temp
$strUrlMdPetUsuExtRemoverUploadArquivo = SessaoSEIExterna::getInstance()->assinarLink('controlador_ajax_externo.php?acao_ajax=md_pet_usu_ext_remover_upload_arquivo');

//==================================================================
//saber se o documento principal é externo ou gerado
//==================================================================
$externo = $ObjMdPetTipoProcessoDTO->getStrSinDocExterno();

//==================================================================
//saber se tem documento essencial configurado na parametrização
//==================================================================
$objMdPetRelTpProcSerieDTO = new MdPetRelTpProcSerieDTO();
$objMdPetRelTpProcSerieDTO->retTodos();
$objMdPetRelTpProcSerieDTO->setStrStaTipoDoc(MdPetRelTpProcSerieRN::$DOC_ESSENCIAL);
$objMdPetRelTpProcSerieDTO->setNumIdTipoProcessoPeticionamento($objTipoProcDTO->getNumIdTipoProcessoPeticionamento());
$objMdPetRelTpProcSerieRN = new MdPetRelTpProcSerieRN();

$arrMdPetRelTpProcSerieDTO = $objMdPetRelTpProcSerieRN->listar($objMdPetRelTpProcSerieDTO);

if (is_array($arrMdPetRelTpProcSerieDTO) && count($arrMdPetRelTpProcSerieDTO) > 0) {

    //saber se foram configurados documentos essenciais
    $temDocEssencial = true;

} else {

    //saber se foram configurados documentos essenciais
    $temDocEssencial = false;
}

//==================================================================
//saber se tem documento Complementar configurado na parametrização
//==================================================================
$objMdPetRelTpProcSerieDTO = new MdPetRelTpProcSerieDTO();
$objMdPetRelTpProcSerieDTO->retTodos();
$objMdPetRelTpProcSerieDTO->setStrStaTipoDoc(MdPetRelTpProcSerieRN::$DOC_COMPLEMENTAR);
$objMdPetRelTpProcSerieDTO->setNumIdTipoProcessoPeticionamento($objTipoProcDTO->getNumIdTipoProcessoPeticionamento());
$objMdPetRelTpProcSerieRN = new MdPetRelTpProcSerieRN();

$arrMdPetRelTpProcSerieDTO = $objMdPetRelTpProcSerieRN->listar($objMdPetRelTpProcSerieDTO);

if (is_array($arrMdPetRelTpProcSerieDTO) && count($arrMdPetRelTpProcSerieDTO) > 0) {

    //saber se foram configurados documentos complementares
    $temDocComplementar = true;

} else {

    //saber se foram configurados documentos complementares
    $temDocComplementar = false;
}

//TODO refatorar para utilizar controlador_ajax
$strLinkAjaxContato = SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=md_pet_contato_cpf_cnpj');

//Validacao de tipo de arquivos
$strSelExtensoesPrin = MdPetExtensoesArquivoINT::recuperaExtensoes(null, null, null, 'S');
$strSelExtensoesComp = MdPetExtensoesArquivoINT::recuperaExtensoes(null, null, null, 'N');

$strLinkAjaxChecarConteudoDocumento = SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=md_pet_validar_documento_principal');

?>
<script type="text/javascript">

    var objAjaxContato = null;
    var objPrincipalUpload = null;
    var objTabelaDocPrincipal = null;

    var objEssencialUpload = null;
    var objTabelaDocEssencial = null;

    var objComplementarUpload = null;
    var objTabelaDocComplementar = null;

    var objTabelaInteressado = null;

    var objAutoCompletarInteressado = null;
    var objLupaInteressados = null;

    var docTipoEssencial = Array();

    <?php if(!empty($nivelAcessoDoc)): ?>
    var arrDocForcaNivelAcesso = JSON.parse('<?= json_encode($nivelAcessoDoc['documentos']) ?>');
    var nivel = '<?= $nivelAcessoDoc['nivel'] ?>';
    var hipotese = '<?= $nivelAcessoDoc['hipotese'] ?>';
    <? endif ?>

    function validarQtdArquivosPrincipal() {

        try {
            objPrincipalUpload.executar();
        } catch (err) {
            alert(" Erro: " + err);
            console.log(err.stack);
        }

    }

    function validarUploadArquivo(numero) {

        try {

            var isValido = true;

            if (numero == '1') {

                //se a tabela existir na tela (ou seja se for doc principal do tipo externo)
                //nao permitir adicionar mais do que 1 documento na grid
                var tbDocumentoPrincipal = document.getElementById('tbDocumentoPrincipal');
                var hiddenCampoPrincipal = document.getElementById('hdnDocPrincipal');

                if (tbDocumentoPrincipal != null &&
                    tbDocumentoPrincipal != undefined) {

                    if (hiddenCampoPrincipal != null &&
                        hiddenCampoPrincipal != undefined &&
                        hiddenCampoPrincipal.value != '') {

                        alert('Somente pode ter um Documento Principal.');

                        document.getElementById("fileArquivoPrincipal").value = '';
                        $(document).find('#ifrProgressofrmDocumentoPrincipal').hide();
                        limparCampoUpload('1');

                        isValido = false;
                        return;

                    }

                }

            }

            //validar se selecionou nivel de acesso
            var cbHipoteseLegal = document.getElementById('hipoteseLegal' + numero);
            var cbNivelAcesso = document.getElementById('nivelAcesso' + numero);
            var strNivelAcesso = cbNivelAcesso.value;
            var strHipoteseLegal = '';

            if (cbHipoteseLegal != null && cbHipoteseLegal != undefined) {
                strHipoteseLegal = cbHipoteseLegal.value;
            }

            //verificar se marcou o formato de documento
            var complemento = '';

            if (numero == '1') {
                complemento = 'Principal';
            } else if (numero == '2') {
                complemento = 'Essencial';
            } else if (numero == '3') {
                complemento = 'Complementar';
            }

            var fileArquivo = document.getElementById('fileArquivo' + complemento);
            var cbTipo = document.getElementById('tipoDocumento' + complemento);
            var strFormatoDocumento = '';
            var cbTipoConferencia = document.getElementById('TipoConferencia' + complemento);
            var strTipoConferencia = document.getElementById('TipoConferencia' + complemento).value;

            var radios = document.getElementsByName('formatoDocumento' + complemento);
            for (var i = 0, length = radios.length; i < length; i++) {

                if (radios[i].checked) {
                    strFormatoDocumento = radios[i].value;
                    break;
                }
            }

            //validar campo Complemento
            var strTxtComplemento = document.getElementById('complemento' + complemento).value;

            //verificar se algum arquivo foi selecionado para o upload
            if (fileArquivo.value == '') {
                alert('Informe o arquivo para upload.');
                isValido = false;
                fileArquivo.focus();
                return;
            }

            //validar campo/combo Tipo (apenas para Essencial ou Complementar)
            else if ((numero == '2' || numero == '3') && (cbTipo == undefined || cbTipo == null || cbTipo.value == '')) {
                alert('Informe o Tipo de Documento.');
                isValido = false;
                cbTipo.focus();
                return;
            }

            if (numero == '2') {

                docTipoEssencial.push(cbTipo.value);

                //validar campo Complemento
                if (strTxtComplemento == "") {
                    alert('Informe o Complemento do Tipo de Documento. Para mais informações, clique no ícone de Ajuda ao lado do nome do campo.');
                    isValido = false;
                    document.getElementById('complemento' + complemento).focus();
                    return;
                }

            }

            //validar campo Complemento
            if (strTxtComplemento == "") {
                alert('Informe o Complemento do Tipo de Documento. Para mais informações, clique no ícone de Ajuda ao lado do nome do campo.');
                isValido = false;
                document.getElementById('complemento' + complemento).focus();
                return;
            }

            //validar campo nivel de acesso
            else if (strNivelAcesso == "") {
                alert('Informe o Nível de Acesso.');
                isValido = false;
                cbNivelAcesso.focus();
                return;
            }

            //se informou Nivel de Acesso restrito, entao precisa informar tambem a hipotese legal
            else if ((cbHipoteseLegal != null && cbHipoteseLegal != undefined) && strNivelAcesso == '1' && strHipoteseLegal == '') {

                alert('Informe a Hipótese Legal.');
                isValido = false;
                cbHipoteseLegal.focus();
                return;

            } else if (strFormatoDocumento == '') {
                alert('Informe o Formato do Documento.');
                isValido = false;
                return;
            }

            //se marcou formato de documento Digitalizado, verificar se selecione o tipo de conferencia
            else if (strFormatoDocumento == 'digitalizado' && strTipoConferencia == '') {
                alert('Informe a Conferência com o documento digitalizado.');
                isValido = false;
                cbTipoConferencia.focus();
                return;
            }

            //validar tamanho do arquivo no lado server side apenas
            if (isValido) {

                if (numero == '1') {
                    objPrincipalUpload.executar();
                } else if (numero == '2') {
                    objEssencialUpload.executar();
                    return true;
                } else if (numero == '3') {
                    objComplementarUpload.executar();
                }

            }

        } catch (err) {
            alert(" Erro: " + err);
            console.log(err.stack);
        }

    }

    //para uso em um caso excepcional do tipo essencial
    function validarUploadArquivoEssencial() {

        try {

            var numero = '2';
            var isValido = true;

            //validar se selecionou nivel de acesso
            var cbHipoteseLegal = document.getElementById('hipoteseLegal' + numero);
            var cbNivelAcesso = document.getElementById('nivelAcesso' + numero);
            var strNivelAcesso = cbNivelAcesso.value;
            var strHipoteseLegal = '';

            if (cbHipoteseLegal != null && cbHipoteseLegal != undefined) {
                strHipoteseLegal = cbHipoteseLegal.value;
            }

            //verificar se marcou o formato de documento
            var complemento = 'Essencial';
            var fileArquivo = document.getElementById('fileArquivo' + complemento);
            var cbTipo = document.getElementById('tipoDocumento' + complemento);
            var strFormatoDocumento = '';
            var cbTipoConferencia = document.getElementById('TipoConferencia' + complemento);
            var strTipoConferencia = document.getElementById('TipoConferencia' + complemento).value;

            var radios = document.getElementsByName('formatoDocumento' + complemento);
            for (var i = 0, length = radios.length; i < length; i++) {

                if (radios[i].checked) {
                    strFormatoDocumento = radios[i].value;
                    break;
                }
            }

            //validar campo Complemento
            var strTxtComplemento = document.getElementById('complemento' + complemento).value;

            //verificar se algum arquivo foi selecionado para o upload
            if (fileArquivo.value == '') {
                //alert('Informe o arquivo para upload.');
                alert('Deve adicionar pelo menos um Documento Essencial para cada Tipo.');
                isValido = false;
                fileArquivo.focus();
                return;
            }

            //validar campo/combo Tipo (apenas para Essencial ou Complementar)
            else if (cbTipo == undefined || cbTipo == null || cbTipo.value == '') {
                alert('Informe o Tipo de Documento.');
                isValido = false;
                cbTipo.focus();
                return;
            }

            docTipoEssencial.push(cbTipo.value);

            //validar campo Complemento
            if (strTxtComplemento == "") {
                alert('Informe o Complemento do Tipo de Documento. Para mais informações, clique no ícone de Ajuda ao lado do nome do campo.');
                isValido = false;
                document.getElementById('complemento' + complemento).focus();
                return;
            }

            //validar campo Complemento
            if (strTxtComplemento == "") {
                alert('Informe o Complemento do Tipo de Documento. Para mais informações, clique no ícone de Ajuda ao lado do nome do campo.');
                isValido = false;
                document.getElementById('complemento' + complemento).focus();
                return;
            }

            //validar campo nivel de acesso
            else if (strNivelAcesso == "") {
                alert('Informe o Nível de Acesso.');
                isValido = false;
                cbNivelAcesso.focus();
                return;
            }

            //se informou Nivel de Acesso restrito, entao precisa informar tambem a hipotese legal
            else if ((cbHipoteseLegal != null && cbHipoteseLegal != undefined) && strNivelAcesso == '1' && strHipoteseLegal == '') {

                alert('Informe a Hipótese Legal.');
                isValido = false;
                cbHipoteseLegal.focus();
                return;

            } else if (strFormatoDocumento == '') {
                alert('Informe o Formato do Documento.');
                isValido = false;
                return;
            }

            //se marcou formato de documento Digitalizado, verificar se selecione o tipo de conferencia
            else if (strFormatoDocumento == 'digitalizado' && strTipoConferencia == '') {
                alert('Informe a Conferência com o documento digitalizado.');
                isValido = false;
                cbTipoConferencia.focus();
                return;
            }

            return isValido;

        } catch (err) {
            alert(" Erro: " + err);
            console.log(err.stack);
        }

    }

    function getStrTipoDocumento(idItem, complemento) {

        var options = document.getElementById('tipoDocumento' + complemento).options;
        var texto = '';
        for (var i = 0; i < options.length; i++) {
            if (options[i].value == idItem) {
                texto = options[i].text;
                break;
            }
        }

        return texto;

    }

    function limparCampoUpload(numero) {

        //verificar se marcou o formato de documento
        var complemento = '';

        if (numero == '1') {
            complemento = 'Principal';
        } else if (numero == '2') {
            complemento = 'Essencial';
        } else if (numero == '3') {
            complemento = 'Complementar';
        }

        var fileArquivo = document.getElementById('fileArquivo' + complemento);

        var strFormatoDocumento = '';
        var cbNivelAcesso = document.getElementById('nivelAcesso' + numero);
        var cbTipoConferencia = document.getElementById('TipoConferencia' + complemento);
        var strTipoConferencia = document.getElementById('TipoConferencia' + complemento).value;

        var radios = document.getElementsByName('formatoDocumento' + complemento);
        for (var i = 0, length = radios.length; i < length; i++) {

            if (radios[i].checked) {
                strFormatoDocumento = radios[i].value;
                break;
            }
        }

        if (numero == '1') {
            objPrincipalUpload.executar();
        } else if (numero == '2') {
            objEssencialUpload.executar();
        } else if (numero == '3') {
            objComplementarUpload.executar();
        }

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
        if (cbNivelAcesso.getAttribute("type") != 'hidden') {
            document.getElementById('divhipoteseLegal' + numero).style.display = 'none';
        }

        //limpar o campo Complemento
        document.getElementById('complemento' + complemento).value = '';

        //retornar a combo "Nivel de Acesso" para a primeira opçao selecionada
        if (cbNivelAcesso.getAttribute("type") != 'hidden') {
            cbNivelAcesso.options[0].selected = 'selected';
        }

        //se nao for o "Principal", resetar a seleçao da combo "Tipo"
        if (numero != '1') {
            document.getElementById('tipoDocumento' + complemento).options[0].selected = 'selected';
            document.querySelector('select[name="hipoteseLegal' + numero + '"]').removeAttribute("disabled");
            document.querySelector('select[name="nivelAcesso' + numero + '"]').removeAttribute("disabled");
        }

        cbTipoConferencia.options[0].selected = 'selected';
        document.getElementById('camposDigitalizado' + complemento).style.display = 'none';
        document.getElementById('camposDigitalizado' + complemento + 'Botao').style.display = 'block';

    }

    function validarQtdArquivosComplementar() {

        try {

            objComplementarUpload.executar();

        } catch (err) {
            alert(" Erro: " + err);
            console.log(err.stack);
        }

    }

    function validarQtdArquivos() {

        try {
            objUpload.executar();
        } catch (err) {
            alert(" Erro: " + err);
            console.log(err.stack);
        }

    }

    function validarQtdArquivosPrincipal() {

        try {
            objPrincipalUpload.executar();
        } catch (err) {
            alert(" Erro: " + err);
            console.log(err.stack);
        }
    }

    function abrirJanelaDocumento() {

        // apontar para formulario aqui
	    <?php
	
	    $linkEditor = PaginaSEIExterna::getInstance()->formatarXHTML(SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=md_pet_editor_montar&id_serie=' . $objTipoProcDTO->getNumIdSerie()));
	
	    if ($objTipoProcDTO->getStrSinDocFormulario() == 'S') {
		    $linkEditor = PaginaSEIExterna::getInstance()->formatarXHTML(SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=md_pet_formulario_gerar&id_serie=' . $objTipoProcDTO->getNumIdSerie()));
	    }
	
	    ?>

        var janelaEditor = infraAbrirJanela('', 'janelaEditor_<?=SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno()?>', infraClientWidth(), infraClientHeight(), 'location=0,status=0,resizable=1,scrollbars=1', false);

        if (janelaEditor.location == 'about:blank') {
            janelaEditor.location.href = '<?=$linkEditor?>';
        }

        janelaEditor.focus();

    }

    function receberInteressado(arrDadosInteressado, InteressadoCustomizado) {

        //antes de adicionar verificar se o interessado ja está na grid
        var strHash = document.getElementById('hdnListaInteressadosIndicados').value;
        var strUsuarioLogado = document.getElementById('hdnUsuarioLogado').value;

        //caractere de quebra de linha/registro
        var arrHash = strHash.split('¥');
        var qtdX = arrHash.length;

        if (qtdX == 1 && arrHash[0] == "") {

            arrHash = Array();
            arrHash[0] = strHash;

        }

        if (strHash != "") {

            for (var i = 0; i < qtdX; i++) {

                //caractere de quebra de coluna/campo
                var arrLocal = arrHash[i].split('±');
                var idContato = arrLocal[0];

                if (idContato == arrDadosInteressado[0]) {
                    alert('O Interessado informado já foi selecionado.');
                    document.getElementById('txtCPF').value = '';
                    document.getElementById('txtNomeRazaoSocial').value = '';
                    return false;
                }

            }

        }

        var nomeInteressadoTratado = document.getElementById('txtNomeRazaoSocialTratadoHTML').value;
        objTabelaInteressado.adicionar([arrDadosInteressado[0],
            arrDadosInteressado[1],
            arrDadosInteressado[2],
            nomeInteressadoTratado,
            '']);
        if (InteressadoCustomizado != "" || strUsuarioLogado == arrDadosInteressado[4]) {

            objTabelaInteressado.adicionarAcoes(
                arrDadosInteressado[0],
                "<a href='javascript:;' onclick=\"abrirCadastroInteressadoAlterar('" + arrDadosInteressado[0] + "', '" + arrDadosInteressado[1] + "', '" + arrDadosInteressado[2] + "')\"><img title='Alterar Interessado' alt='Alterar Interessado' src='/infra_css/svg/alterar.svg' class='infraImg' /></a>",
                false,
                true);

        } else {

            objTabelaInteressado.adicionarAcoes(
                arrDadosInteressado[0],
                "",
                false,
                true);
        }

        document.getElementById("txtCPF").value = '';
        document.getElementById("txtCNPJ").value = '';
        document.getElementById("hdnCustomizado").value = '';
        document.getElementById("txtNomeRazaoSocial").value = '';
        document.getElementById("hdnIdInteressadoCadastrado").value = '';

        infraEfeitoTabelas();

    }

    function addDocumento(docCustomizado) {

        //pegar valor da combo nivel de acesso nivelAcesso1
        var nivelAcessoCombo1 = document.getElementById("nivelAcesso1");
        var txtNivelAcessoCombo1 = nivelAcessoCombo1.options[nivelAcessoCombo1.selectedIndex].text;

        //pegar valor da hipotese legal
        var hipoteseCombo1 = document.getElementById("hipoteseLegal1");
        var valorHipoteseLegal = hipoteseCombo1.options[hipoteseCombo1.selectedIndex].text;

        //pegar data
        var data = new Date()
        var dia = data.getDate();
        var mes = data.getMonth();
        var ano = data.getFullYear();
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
        cell1.align = 'center';

        cell2.innerHTML = dataFormatada;
        cell2.align = 'center';

        cell3.innerHTML = tamanhoFormatado;
        cell3.align = 'center';

        cell4.innerHTML = nomeDocumento;
        cell4.align = 'center';

        cell5.innerHTML = txtNivelAcessoCombo1;
        cell5.align = 'center';

        if (docCustomizado != null && docCustomizado == true) {
            cell6.innerHTML = " customizado ";
        } else {
            cell6.innerHTML = " selecionado ";
        }

        cell6.align = 'center';

        infraEfeitoTabelas();

    }

    function deleteRow(btn) {
        var row = btn.parentNode.parentNode;
        row.parentNode.removeChild(row);
    }

    function addDocumentoComplementar(docCustomizado) {

        //pegar valor da combo nivel de acesso nivelAcesso1
        var nivelAcessoCombo2 = document.getElementById("nivelAcesso2");
        var txtNivelAcessoCombo2 = nivelAcessoCombo2.options[nivelAcessoCombo2.selectedIndex].text;

        //pegar valor da hipotese legal
        var hipoteseCombo2 = document.getElementById("hipoteseLegal2");
        var valorHipoteseLegal2 = hipoteseCombo2.options[hipoteseCombo2.selectedIndex].text;

        var table = document.getElementById("tblAnexos");

        var row = table.insertRow(-1);
        row.className = 'infraTrClara';

        //Nome do arquivo
        var cell1 = row.insertCell(0);
        cell1.align = 'center';
        cell1.className = 'infraTdSetaOrdenacao';

        //Data

        var data = new Date()
        var dia = data.getDate();
        var mes = data.getMonth();
        var ano = data.getFullYear();
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

        if (docCustomizado != null && docCustomizado == true) {
            cell6.innerHTML = " customizado ";
        } else {
            cell6.innerHTML = " selecionado ";
        }

        infraEfeitoTabelas();
    }

    function addFormatoDocumento() {
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

    function validarFormulario() {

        //valida campo especificação
        var textoEspecificacao = document.getElementById("txtEspecificacao").value;
        var cbUF = document.getElementById("selUFAberturaProcesso");
        var ufSelecionada = '';
        var DocPrincipalValidado = false;

        var selInteressados = document.getElementById("selInteressados");

        if (cbUF != undefined && cbUF != null) {
            ufSelecionada = cbUF.value;
        }

        if (textoEspecificacao == '') {
            alert('Informe a Especificação.');
            document.getElementById("txtEspecificacao").focus();
            return false;
        }

        //Validar combos
        if (document.getElementById('selOrgao') && document.getElementById('selOrgao').value === '') {
            alert("Informe o Orgão.");
            document.getElementById("selOrgao").focus();
            return false;
        }

        if (document.getElementById('selUF') && document.getElementById('selUF').value === '') {
            alert("Informe a UF.");
            document.getElementById("selUF").focus();
            return false;
        }

        if (document.getElementById("selUFAberturaProcesso") && document.getElementById("selUFAberturaProcesso").value === '') {
            alert("Informe a Cidade.");
            document.getElementById("selUFAberturaProcesso").focus();
            return false;
        }

        if (selInteressados != undefined && selInteressados != null && selInteressados.value == '') {
            alert('Informe o(s) Interessado(s).');
            selInteressados.focus();
            return false;
        }

        //aplicando validaçao de interessados informados no cenario de indicaçao por cpf ou cnpj
        var tbInteressadosIndicados = document.getElementById("tbInteressadosIndicados");
        var hdnListaInteressadosIndicados = document.getElementById("hdnListaInteressadosIndicados");
        var optTipoPessoaFisica = document.getElementById("optTipoPessoaFisica");

        if (tbInteressadosIndicados != null && hdnListaInteressadosIndicados != null && hdnListaInteressadosIndicados.value == "") {

            alert('Informe o(s) Interessado(s).');
            optTipoPessoaFisica.focus();
            return false;
        }

        //aplicando validações relacionadas ao documento principal
        var fileArquivoPrincipal = document.getElementById('fileArquivoPrincipal');
        var complementoPrincipal = document.getElementById('complementoPrincipal');
        var nivelAcessoPrincipal = document.getElementById('nivelAcesso1');

        var hipoteseLegalPrincipal;
        if (document.getElementById('hipoteseLegal1') != null) {
            hipoteseLegalPrincipal = document.getElementById('hipoteseLegal1');
        }

        //validando seleçao de nivel de acesso principal e hipotese legal principal
        var tbDocumentoPrincipal = document.getElementById('tbDocumentoPrincipal');

        //se for documento principao do tipo externo, só validar complemento,
        // nivel de acesso e hipotese legal SE a grid estiver ainda sem nenhum documento
        if (tbDocumentoPrincipal != null &&
            tbDocumentoPrincipal != undefined) {

            var hdnDocPrincipal = document.getElementById('hdnDocPrincipal').value;

            var strFormatoDocumento = '';
            var cbTipoConferencia = document.getElementById('TipoConferenciaPrincipal');
            var strTipoConferencia = document.getElementById('TipoConferenciaPrincipal').value;

            var radios = document.getElementsByName('formatoDocumentoPrincipal');
            for (var i = 0, length = radios.length; i < length; i++) {

                if (radios[i].checked) {
                    strFormatoDocumento = radios[i].value;
                    break;
                }
            }

            if (hdnDocPrincipal == "" && fileArquivoPrincipal.value == '') {
                alert('Informe o Documento Principal.');
                fileArquivoPrincipal.focus();
                return false;

            } else if (hdnDocPrincipal == "" && complementoPrincipal.value == '') {
                alert('Informe o Complemento do Tipo de Documento. Para mais informações, clique no ícone de Ajuda ao lado do nome do campo.');
                complementoPrincipal.focus();
                return false;

            } else if (hdnDocPrincipal == "" && nivelAcessoPrincipal.value == '') {
                alert('Informe o Nível de Acesso.');
                nivelAcessoPrincipal.focus();
                return false;

            } else if (hdnDocPrincipal == "" && nivelAcessoPrincipal.value == '1' && hipoteseLegalPrincipal != null && hipoteseLegalPrincipal.value == '') {
                alert('Informe a Hipótese Legal.');
                hipoteseLegalPrincipal.focus();
                return false;
            } else if (hdnDocPrincipal == "" && strFormatoDocumento == '') {
                alert('Informe o Formato do Documento.');
                return false;
            } else if (hdnDocPrincipal == "" && strFormatoDocumento == 'digitalizado' && strTipoConferencia == '') {
                alert('Informe a Conferência com o documento digitalizado.');
                return false;
            }

        }

        //se for documento gerado sempre valida complemento, nivel de acesso e hipotese legal
        else {

            if (nivelAcessoPrincipal.value == '') {
                alert('Informe o Nível de Acesso.');
                nivelAcessoPrincipal.focus();
                return false;

            } else if (nivelAcessoPrincipal.value == '1' && hipoteseLegalPrincipal != null && hipoteseLegalPrincipal.value == '') {
                alert('Informe a Hipótese Legal.');
                hipoteseLegalPrincipal.focus();
                return false;
            }

        }

        //validar se pelo menos um doc principal foi adicionado CASO
        //a grid de doc principal exista na tela (ou seja, quando a parametrização)
        //informar doc principal do tipo Externo

        if (tbDocumentoPrincipal != null &&
            tbDocumentoPrincipal != undefined) {

            var strHashPrincipal = document.getElementById('hdnDocPrincipal').value;

            if (strHashPrincipal == '') {
                alert('Informe o Documento Principal.');
                document.getElementById('fileArquivoPrincipal').focus();
                return false;
            } else {
                DocPrincipalValidado = true;
            }
        }

        //caso doc principal seja do tipo "Gerado", fazer requisição AJAX
        //para validar se usuário salvou na sessao algum conteudo para o documento
        //caso nao tenha conteudo obrigar usuario a informar
        else {

            var conteudoDocumento = "";

            $.ajax({
                url: "<?=PaginaSEIExterna::getInstance()->formatarXHTML(SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=md_pet_validar_documento_principal'))?>",
                type: "POST",
                async: false,
                success: function (data, textStatus, jqXHR) {
                    conteudoDocumento = data;
                    if (data == '') {
                        alert("O documento principal deste tipo de peticionamento possui modelo previamente definido e deve ser editado diretamente no sistema. Para continuar o peticionamento, antes é necessário acessar o Editor do SEI no link clique aqui para editar conteúdo em frente ao campo Documento Principal, preencher apenas os campos pertinentes com os dados da demanda e clicar no botão Salvar no canto superior esquerdo do Editor.");
                        return;
                    } else {
                        DocPrincipalValidado = true;
                    }

                },
                error: function (jqXHR, textStatus, errorThrown) {
                    alert('Erro ao validar documento principal.');
                    console.log('Erro' + textStatus);
                    return;
                }
            });

        }

        if (DocPrincipalValidado == true) {

            //valida se todos os tipos essenciais contem na lista
            var comboTipoEssencial = document.getElementById('tipoDocumentoEssencial');

            if (comboTipoEssencial != null) {
                var retornoUploadEssencial = false;
                var validarTipoEssenc = true;
                var strHashEssencial = document.getElementById('hdnDocEssencial').value;

                //caractere de quebra de linha/registro
                var arrHashEssencial = strHashEssencial.split('¥');
                var qtdX = arrHashEssencial.length;

                if (qtdX == 1 && arrHashEssencial[0] == "") {

                    arrHashEssencial = Array();
                    arrHashEssencial[0] = strHashEssencial;

                }

                var local = 9;
                var tiposIncluidos = Array();

                //so vai adicionar no array dos incluidos quando tem registros na grid
                if (strHashEssencial != "") {

                    for (var i = 0; i < qtdX; i++) {

                        //caractere de quebra de coluna/campo
                        var arrLocal = arrHashEssencial[i].split('±');
                        var tipo = arrLocal[local];

                        if (tiposIncluidos.indexOf(tipo) <= -1) {
                            tiposIncluidos.push(arrLocal[local]);
                        }

                    }

                } else {
                    //grid vazia e campos de upload de essencial nao preenchidos
                    retornoUploadEssencial = validarUploadArquivoEssencial();

                    if (retornoUploadEssencial != true) {
                        return false;
                    }
                }

                var tamnhoOptions = comboTipoEssencial.options.length - 1;

                if (tiposIncluidos.length == 0 || tamnhoOptions != tiposIncluidos.length) {
                    validarTipoEssenc = false;
                }

                if (!validarTipoEssenc) {
                    alert('Deve adicionar pelo menos um Documento Essencial para cada Tipo.');
                    document.getElementById('fileArquivoEssencial').focus();
                    return false;
                }
            }

            return true;

        } else {
            console.log('error');
            return false;

        }


    }

    function abrirPeticionar() {

        if (validarFormulario()) {
            parent.infraAbrirJanelaModal('<?=PaginaSEIExterna::getInstance()->formatarXHTML(SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?id_tipo_procedimento=' . $_GET['id_tipo_procedimento'] . '&acao=peticionamento_usuario_externo_concluir&tipo_selecao=2'))?>',
                800,
                520,
                '', //options
                false); //modal
        }
    }

    function abrirCadastroInteressadoAlterar(id, tipo, cpfcnpj) {

        //charmar janela para cadastrar um novo interessado
        $('#txtNomeRazaoSocial').val('');
        $('#hdnCustomizado').val('');
        $('#hdnIdEdicao').val(id);

        <?php
        $strLinkEdicaoPF = SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?edicao=true&acao=md_pet_interessado_cadastro&tipo_selecao=2&cpf=true&id_orgao_acesso_externo=0');
        $strLinkEdicaoPJ = SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?edicao=true&acao=md_pet_interessado_cadastro&tipo_selecao=2&cnpj=true&id_orgao_acesso_externo=0');
        ?>

        if (tipo == 'Pessoa Física') {
            var str = '<?= $strLinkEdicaoPF ?>';
        } else if (tipo == 'Pessoa Jurídica') {
            var str = '<?= $strLinkEdicaoPJ ?>';
        }

        infraAbrirJanelaModal(str, 900, 900); //modal
        return;

    }

    function abrirCadastroInteressado() {

        //so abrir o CPF/CNPJ tiver sido informado e NAO pertencer a um contato cadastrado
        var txtcpf = document.getElementById("txtCPF").value;
        var txtcnpj = document.getElementById("txtCNPJ").value;
        var conteudo = '';

        var chkTipoPessoaFisica = document.getElementById("optTipoPessoaFisica").checked;
        var chkTipoPessoaJuridica = document.getElementById("optTipoPessoaJuridica").checked;

        if (chkTipoPessoaFisica) {
            conteudo = txtcpf;
        } else if (chkTipoPessoaJuridica) {
            conteudo = txtcnpj;
        }

        if (conteudo == '') {

            if (chkTipoPessoaFisica) {
                alert('Informe o CPF.');
                document.getElementById("txtCPF").focus();
            } else if (chkTipoPessoaJuridica) {
                alert('Informe o CNPJ.');
                document.getElementById("txtCNPJ").focus();
            }

            return;
        }

        //checar se o CPF/CNPJ está no formato válido e se estiver, consultar via AJAX para tentar obter um interessado cadastrado
        else {

            if (chkTipoPessoaFisica) {

                ponto = txtcpf.split(".");
                traco = txtcpf.split("-");

                //cpf tem que ser valido , ter 2 pontos e um traço (ou seja, estar na mascara)
                if (!infraValidarCpf(infraTrim(txtcpf)) || (ponto.length - 1) != 2 || (traco.length - 1) != 1) {

                    alert('CPF Inválido.');
                    document.getElementById('txtCPF').focus();
                    document.getElementById('txtNomeRazaoSocial').value = '';
                    return;

                }

            } else if (chkTipoPessoaJuridica) {

                ponto = txtcnpj.split(".");
                traco = txtcnpj.indexOf("-");
                barra = txtcnpj.indexOf("/");

                if (!infraValidarCnpj(infraTrim(txtcnpj)) || ponto.length != 3 || traco != 15 || barra != 10) {

                    alert('CNPJ Inválido.');
                    document.getElementById('txtCNPJ').focus();
                    document.getElementById('txtNomeRazaoSocial').value = '';
                    return;

                }

            }

            //se chegar aqui o CPF/CNPJ está valido, entao consultar via AJAX para ver se o contato já está cadastrado
            var formData = "cpfcnpj=" + conteudo;  //Name value Pair
            $.ajax({
                url: "<?= $strLinkAjaxContato ?>",
                type: "POST",
                data: formData,
                success: function (data, textStatus, jqXHR) {
                    //data - response from server
                    if (data != null && data != undefined && data != "") {

                        var obj = jQuery.parseJSON(data);
                        $('#hdnIdInteressadoCadastrado').val(obj.id);
                        $('#txtNomeRazaoSocial').val(obj.nome);
                        $('#hdnUsuarioCadastro').val(obj.usuario);
                        $('#txtNomeRazaoSocialTratadoHTML').val(obj.nomeTratado);
                        $('#hdnCustomizado').val('');
                        return;
                    } else {

                        //charmar janela para cadastrar um novo interessado
                        $('#txtNomeRazaoSocial').val('');
                        $('#txtNomeRazaoSocialTratadoHTML').val('');
                        $('#hdnCustomizado').val('');
                        if (chkTipoPessoaFisica) {
                            var str = '<?= SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=md_pet_interessado_cadastro&tipo_selecao=2&cpf=true&cadastro=true') ?>';
                        } else if (chkTipoPessoaJuridica) {
                            var str = '<?= SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=md_pet_interessado_cadastro&tipo_selecao=2&cnpj=true&cadastro=true') ?>';
                        }

                        infraAbrirJanelaModal(str, 900, 900); //modal

                        return;

                    }

                },
                error: function (jqXHR, textStatus, errorThrown) {
                    alert('Erro' + textStatus);
                    return;
                }
            });

            return;

        }


    }

    function inicializar() {
		//Caso tenha somente um ORGAO dentro do processo
        try {
            if (document.getElementById('hdnIdOrgaoDisabled').value == "disabled") {
                var idOrgao = document.getElementById('hdnIdOrgaoUnico').value;
            } else {
                var idOrgao = document.getElementById('hdnIdOrgaoTelaAnterior').value;
            }
            if (document.getElementById('hdnIdOrgaoDisabled').value == "disabled" && document.getElementById('hdnIdUfTelaAnterior').value == "" && document.getElementById('hdnIdUfTelaAnterior').value == "") {
                infraSelectLimpar('selUFAberturaProcesso');
                document.getElementById("ufHidden").style.display = "none";
                $.ajax({
                    dataType: 'xml',
                    method: 'POST',
                    url: '<?php echo SessaoSEIExterna::getInstance()->assinarLink('controlador_ajax_externo.php?acao_ajax=md_pet_consultar_tipo_processo_uf');?>',
                    data: {
                        'idOrgao': idOrgao,
                        'idTpProc': document.getElementById('hdnTpProcesso').value
                    },
                    error: function (dados) {
                        console.log(dados);
                    },
                    success: function (data) {
                        var selectMultiple = document.getElementById('selUF');
                        try {
                            //Quando tiver mais de uma UF
                            var count = $(data).find("item").length;
                            if (count > 1) {
                                //Esconder Combo Cidade Caso Retorne mais de uma Cidade
                                document.getElementById("ufHidden").style.display = "block";
                                var opt = document.createElement('option');
                                opt.value = "";
                                opt.innerHTML = "";
                                selectMultiple.empty().end().appendChild(opt);
                                $.each($(data).find('item'), function (i, j) {
                                    var opt = document.createElement('option');
                                    opt.value = $(j).attr("id");
                                    opt.innerHTML = $(j).attr("descricao");
                                    selectMultiple.appendChild(opt);
                                });
                            }
                            $.each($(data).find('item'), function (i, j) {
                                //Quando retornar somente uma UF
                                var count = $(data).find("item").length;
                                if (count < 2) {
                                    //document.getElementById('hdnIdUf').value = $(j).attr("id");
                                    document.getElementById("ufHidden").style.display = "none";
                                    $.ajax({
                                        dataType: 'xml',
                                        method: 'POST',
                                        url: '<?php echo SessaoSEIExterna::getInstance()->assinarLink('controlador_ajax_externo.php?acao_ajax=md_pet_consultar_tipo_processo_cidade');?>',
                                        data: {
                                            'idOrgao': idOrgao,
                                            'idUf': $(j).attr("id"),
                                            'idTpProc': document.getElementById('hdnTpProcesso').value
                                        },
                                        error: function (dados) {
                                            console.log(dados);
                                        },
                                        success: function (data) {
                                            var selectMultiple = document.getElementById('selUFAberturaProcesso');
                                            try {
                                                var count = $(data).find("item").length;
                                                if (count > 0) {
                                                    document.getElementById("cidadeHidden").style.display = "";
                                                }
                                                if (count > 0) {
                                                    var opt = document.createElement('option');
                                                    opt.value = "";
                                                    opt.innerHTML = "";
                                                    selectMultiple.appendChild(opt);
                                                }
                                                $.each($(data).find('item'), function (i, j) {
                                                    var opt = document.createElement('option');
                                                    opt.value = $(j).attr("id");
                                                    opt.innerHTML = $(j).attr("descricao");
                                                    selectMultiple.appendChild(opt);
                                                });
                                                var div = document.getElementById('selUFAberturaProcesso');
                                                div.appendChild(selectMultiple);

                                            } catch (err) {

                                            }

                                        }

                                    });
                                    // document.getElementById("selUF").disabled = true;
                                }

                                var count = $(data).find("item").length;
                                if (count < 2) {
                                    //document.getElementById("selUF").disabled = true;

                                }
                            });

                            var div = document.getElementById('selUF');
                            div.appendChild(selectMultiple);

                        } catch (err) {

                        }

                    }
                });
            }
            if (document.getElementById('hdnIdUfTelaAnterior').value != '') {
                var val = document.getElementById('hdnIdUfTelaAnterior').value;
                var sel = document.getElementById('selUF');
                var opts = sel.options;
                for (var opt, j = 0; opt = opts[j]; j++) {
                    if (opt.value == val) {
                        sel.selectedIndex = j;
                        break;
                    }
                }
            }
           if (document.getElementById('hdnIdOrgaoTelaAnterior').value != '') {
                var val = document.getElementById('hdnIdOrgaoTelaAnterior').value;
                var sel = document.getElementById('selOrgao');
                var opts = sel.options;
                for (var opt, j = 0; opt = opts[j]; j++) {
                    if (opt.value == val) {
                        sel.selectedIndex = j;
                        break;
                    }
                }
            }
            if (document.getElementById('hdnIdCidadeTelaAnterior').value != '') {
                var val = document.getElementById('hdnIdCidadeTelaAnterior').value;
                var sel = document.getElementById('selUFAberturaProcesso');
                var opts = sel.options;
                for (var opt, j = 0; opt = opts[j]; j++) {
                    if (opt.innerHTML == val) {
                        sel.selectedIndex = j;
                        break;
                    }
                }
            }
        } catch (err) {

        }

        <? if( $objTipoProcDTO->getStrSinIIIndicacaoDiretaContato() == 'S') { ?>
        objLupaInteressados = new infraLupaSelect('selInteressados', 'hdnInteressados', '<?=$strLinkInteressadosSelecao?>');

        objAutoCompletarInteressado = new infraAjaxAutoCompletar('hdnIdInteressado', 'txtInteressado', '<?=$strLinkAjaxInteressado?>');
        objAutoCompletarInteressado.limparCampo = true;
        //objAutoCompletarInteressado.tamanhoMinimo = 3;

        objAutoCompletarInteressado.prepararExecucao = function () {
            return 'extensao=' + document.getElementById('txtInteressado').value;
        };

        objAutoCompletarInteressado.processarResultado = function (id, descricao, complemento) {
            if (id != '') {
                var options = document.getElementById('selInteressados').options;

                for (var i = 0; i < options.length; i++) {
                    if (options[i].value == id) {
                        self.setTimeout('alert(\'Interessado já consta na lista.\')', 100);
                        break;
                    }
                }

                if (i == options.length) {

                    for (i = 0; i < options.length; i++) {
                        options[i].selected = false;
                    }

                    opt = infraSelectAdicionarOption(document.getElementById('selInteressados'), descricao, id);
                    objLupaInteressados.atualizar();
                    opt.selected = true;
                }

                document.getElementById('txtInteressado').value = '';
                document.getElementById('txtInteressado').focus();
            }
        };
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
        objAjaxContato = new infraAjaxComplementar(null, '<?=$strLinkAjaxContato?>');
        objAjaxContato.limparCampo = false;

        objAjaxContato.prepararExecucao = function () {
            return 'cpfCnpj=' + document.getElementById('txtCPF').value;
        }

        objAjaxContato.processarResultado = function (arr) {

            //inicio processo ajax

            //fim processo ajax
        }

        //instanciar tabela dinamica de interessados caso os objetos existam na tela
        var tabelaInteressadosIndicados = document.getElementById('tbInteressadosIndicados');
        var hdnTabelaInteressadosIndicados = document.getElementById('hdnListaInteressadosIndicados');

        if ((tabelaInteressadosIndicados != null && tabelaInteressadosIndicados != undefined)
            &&
            (hdnTabelaInteressadosIndicados != null && hdnTabelaInteressadosIndicados != undefined)) {

            objTabelaInteressado = new infraTabelaDinamica('tbInteressadosIndicados', 'hdnListaInteressadosIndicados', false, false);
            objTabelaInteressado.gerarEfeitoTabela = true;

            document.getElementById("txtCPF").addEventListener("keyup", function (event) {
                event.preventDefault();
                if (event.keyCode == 13) {
                    abrirCadastroInteressado();
                }
            });

            document.getElementById("txtCNPJ").addEventListener("keyup", function (event) {
                event.preventDefault();
                if (event.keyCode == 13) {
                    abrirCadastroInteressado();
                }
            });
        }

    }

    function getStrNivelAcesso(nivel) {

        if (nivel == '0') {
            return 'Público';
        } else if (nivel == '1') {
            return 'Restrito';
        } else if (nivel == '') {
            return '-';
        }
    }

    //campo de upload de documentos principais
    function carregarCamposDocPrincipalUpload() {

        try {

            objPrincipalUpload = new infraUpload('frmDocumentoPrincipal', '<?=$strLinkUploadDocPrincipal?>');

            objPrincipalUpload.finalizou = function (arr) {

                var nomeUpload = arr['nome_upload'];
                var nome = arr['nome'];
                var dataHora = arr['data_hora'];
                var tamanho = arr['tamanho'];
                var dataHoraFormatada = '<?= time() ?>';
                var tamanhoFormatado = infraFormatarTamanhoBytes(arr['tamanho']);

                //Tamanho
                var tamanhoMb = tamanho / 1024 / 1024;
                var tamanhoPermitidoMb = parseInt(document.getElementById('hdnTamArquivoPrincipal').value.toLowerCase().replace(' mb', ''));
                if (tamanhoMb > tamanhoPermitidoMb) {
                    alert('Arquivo Principal com tamanho maior que o permitido. \nTamanho do arquivo: ' + tamanhoMb.toFixed(2) + 'Mb\nTamanho permitido: ' + tamanhoPermitidoMb + 'Mb');
                    return false;
                }

                //concatenacao de "Tipo" e "Complemento"
                var cbTpoPrincipal = document.getElementById('tipoDocumentoPrincipal');

                //abordagem anti-XSS client side
                var htmlComplemento = document.getElementById('complementoPrincipal').value;
                var escaped = $("<pre>").text(htmlComplemento).html();
                var strComplemento = escaped;

                //var strComplemento = document.getElementById('complementoPrincipal').value;
                var documento = getStrTipoDocumento(cbTpoPrincipal.value, 'Principal') + ' ' + strComplemento;

                var nivelAcesso = getStrNivelAcesso(document.getElementById('nivelAcesso1').value);

                //hipoteseLegal1
                var hipoteseLegal = '';
                var cbHipotese = document.getElementById('hipoteseLegal1');

                if (cbHipotese != null && cbHipotese != undefined) {
                    hipoteseLegal = cbHipotese.value;
                }

                var formatoDocumento = $('input[name="formatoDocumentoPrincipal"]:checked').val();
                var formatoDocumentoLbl = 'Nato-digital';
                if (formatoDocumento != 'nato') {
                    formatoDocumentoLbl = 'Digitalizado';
                }

                //TipoConferenciaPrincipal / TipoConferenciaEssencial
                var tipoConferencia = document.getElementById('TipoConferenciaPrincipal').value;

                objTabelaDocPrincipal.adicionar([nome, dataHora, tamanhoFormatado, documento, nivelAcesso, hipoteseLegal, formatoDocumento, tipoConferencia, nomeUpload, cbTpoPrincipal.value, strComplemento, formatoDocumentoLbl, '']);

                var strHashPrincipal = document.getElementById('hdnDocPrincipal').value;
                var arrHashPrincipal = strHashPrincipal.split('±');

                <? $linkBase = PaginaSEIExterna::getInstance()->formatarXHTML(SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=md_pet_usu_ext_download&id_tipo_procedimento=' . $_GET['id_tipo_procedimento'] . '&id_orgao_acesso_externo=0')); ?>
                var urlBase = "<?= $linkBase ?>";

                document.getElementById('hdnNomeArquivoDownload').value = arrHashPrincipal[0];
                document.getElementById('hdnNomeArquivoDownloadReal').value = arrHashPrincipal[1];

                objTabelaDocPrincipal.adicionarAcoes(
                    arr['nome'],
                    "",
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
                        col.setAttribute("valign", "middle");
                    }
                }

            }

            objPrincipalUpload.validar = function (arr) {

                //INICIO VALIDACAO EXTENSOES
                var arrExtensoesPermitidas = [<?=$strSelExtensoesPrin?>];
                if ($("#fileArquivoPrincipal").val().replace(/^.*\./, '') != '' && $.inArray($("#fileArquivoPrincipal").val().replace(/^.*\./, '').toLowerCase(), arrExtensoesPermitidas) == -1) {
                    alert("O arquivo selecionado não é permitido.\nSomente são permitidos arquivos com as extensões:\n<?=preg_replace("%'%", " ", $strSelExtensoesPrin)?> .");
                    return false;
                }
                //FIM VALIDACAO EXTENSOES

                var arquivoPrincipal = document.getElementById('fileArquivoPrincipal').value;
                var ext = (arquivoPrincipal.substring(arquivoPrincipal.lastIndexOf(".")).toLowerCase()).split('.')[1];

                var extPermitida = false;
                var extensoes = $('#hdnArquivosPermitidos').val();
                var obj = JSON.parse($('#hdnArquivosPermitidos').val());

                for (var index in obj) {

                    if (obj[index] == ext) {
                        extPermitida = true;
                    }
                }

                //INICIO VALIDACAO EXTENSOES
                if (ext != undefined && ext != '' && !extPermitida) {
                    document.getElementById('fileArquivoPrincipal').value = '';
                    alert('A extensão do arquivo não é permitida.');
                }
                //FIM VALIDACAO EXTENSOES

                return extPermitida;

            }

            //Monta tabela de anexos
            objTabelaDocPrincipal = new infraTabelaDinamica('tbDocumentoPrincipal', 'hdnDocPrincipal', false, false);
            objTabelaDocPrincipal.gerarEfeitoTabela = true;

            objTabelaDocPrincipal.remover = function (arrLinha) {
                //remove o arquivo da pasta temp
                $.ajax({
                    url: '<?=$strUrlMdPetUsuExtRemoverUploadArquivo?>',
                    type: 'POST',
                    async: false,
                    dataType: 'XML',
                    data: {hdnTbDocumento: arrLinha},
                    success: function (r) {
                    },
                    error: function (e) {
                        console.error('Erro ao processar o XML do SEI: ' + e.responseText);
                    }
                });

                return true;
            };

        } catch (err) {
            alert(' ERRO ' + err);
        }
    }

    //campo de upload de documentos essenciais
    function carregarCamposDocEssencialUpload() {

        objEssencialUpload = new infraUpload('frmDocumentosEssenciais', '<?=$strLinkUploadDocEssencial?>');

        objEssencialUpload.finalizou = function (arr) {

            var nomeUpload = arr['nome_upload'];
            var nome = arr['nome'];
            var data = arr['data'];
            var dataHora = arr['data_hora'];
            var tamanho = arr['tamanho'];
            var dataHoraFormatada = '<?= time() ?>';
            var tamanhoFormatado = infraFormatarTamanhoBytes(arr['tamanho']);

            //Tamanho
            var tamanhoMb = tamanho / 1024 / 1024;
            var tamanhoPermitidoMb = parseInt(document.getElementById('hdnTamArquivoEssencial').value.toLowerCase().replace(' mb', ''));
            if (tamanhoMb > tamanhoPermitidoMb) {
                alert('Arquivo Essencial com tamanho maior que o permitido. \nTamanho do arquivo: ' + tamanhoMb.toFixed(2) + 'Mb\nTamanho permitido: ' + tamanhoPermitidoMb + 'Mb');
                return false;
            }

            //Nome
            var linhas = document.getElementById('tbDocumentoEssencial').rows;
            var tamanhoInserido = 0;
            var tamanhoVerificar = 0;

            for (var i = 1; i < linhas.length; i++) {
                //Nome igual
                if (nome.toLowerCase().trim() == linhas[i].cells[0].innerText.toLowerCase().trim()) {
                    alert('Não é permitido adicionar documento com o mesmo nome de arquivo.');
                    return false;
                }
            }

            //concatenacao de "Tipo" e "Complemento"
            var cbTpoEssencial = document.getElementById('tipoDocumentoEssencial');

            //abordagem anti-XSS client side
            var htmlComplemento = document.getElementById('complementoEssencial').value;
            var escaped = $("<pre>").text(htmlComplemento).html();
            var strComplemento = escaped;

            var documento = getStrTipoDocumento(cbTpoEssencial.value, 'Essencial') + ' ' + strComplemento;

            var nivelAcesso = getStrNivelAcesso(document.getElementById('nivelAcesso2').value);

            var hipoteseLegal = '';
            var cbHipotese = document.getElementById('hipoteseLegal2');

            if (cbHipotese != null && cbHipotese != undefined) {
                hipoteseLegal = cbHipotese.value;
            }

            var formatoDocumento = $('input[name="formatoDocumentoEssencial"]:checked').val();
            var formatoDocumentoLbl = 'Nato-digital';
            if (formatoDocumento != 'nato') {
                formatoDocumentoLbl = 'Digitalizado';
            }

            //TipoConferenciaPrincipal / TipoConferenciaEssencial
            var tipoConferencia = document.getElementById('TipoConferenciaEssencial').value;

            objTabelaDocEssencial.adicionar([nome, dataHora, tamanhoFormatado, documento, nivelAcesso, hipoteseLegal, formatoDocumento, tipoConferencia, nomeUpload, cbTpoEssencial.value, strComplemento, formatoDocumentoLbl, '']);

            var strHashEssencial = document.getElementById('hdnDocEssencial').value;
            var arrHashEssencial = strHashEssencial.split('±');

            <? $linkBase = PaginaSEIExterna::getInstance()->formatarXHTML(SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=md_pet_usu_ext_download&id_tipo_procedimento=' . $_GET['id_tipo_procedimento'] . '&id_orgao_acesso_externo=0')); ?>
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
            $(document).find('#ifrProgressofrmDocumentosEssenciais').hide();
            limparCampoUpload('2');

            var table = document.getElementById("tbDocumentoEssencial");

            for (var i = 0, row; row = table.rows[i]; i++) {

                for (var j = 0, col; col = row.cells[j]; j++) {
                    col.setAttribute("valign", "middle");
                }

            }


        }

        objEssencialUpload.validar = function (arr) {

            //INICIO VALIDACAO EXTENSOES
            var arrExtensoesPermitidas = [<?=$strSelExtensoesComp?>];
            if ($("#fileArquivoEssencial").val().replace(/^.*\./, '') != '' && $.inArray($("#fileArquivoEssencial").val().replace(/^.*\./, '').toLowerCase(), arrExtensoesPermitidas) == -1) {
                alert("O arquivo selecionado não é permitido.\nSomente são permitidos arquivos com as extensões:\n<?=preg_replace("%'%", " ", $strSelExtensoesComp)?> .");
                return false;
            }
            //FIM VALIDACAO EXTENSOES

            var arquivoEssencial = document.getElementById('fileArquivoEssencial').value;
            var ext = (arquivoEssencial.substring(arquivoEssencial.lastIndexOf(".")).toLowerCase()).split('.')[1];

            var extPermitida = false;

            var obj = JSON.parse($('#hdnArquivosPermitidosEssencialComplementar').val());

            for (var index in obj) {

                if (obj[index] == ext) {
                    extPermitida = true;
                }
            }

            if (ext != undefined && ext != '' && !extPermitida) {
                document.getElementById('fileArquivoEssencial').value = '';
                alert('A extensão do arquivo não é permitida.');
            }

            return extPermitida;

        }

        //Monta tabela de anexos
        objTabelaDocEssencial = new infraTabelaDinamica('tbDocumentoEssencial', 'hdnDocEssencial', false, false);
        objTabelaDocEssencial.gerarEfeitoTabela = true;

        objTabelaDocEssencial.remover = function (arrLinha) {
            //remove o arquivo da pasta temp
            $.ajax({
                url: '<?=$strUrlMdPetUsuExtRemoverUploadArquivo?>',
                type: 'POST',
                async: false,
                dataType: 'XML',
                data: {hdnTbDocumento: arrLinha},
                success: function (r) {
                },
                error: function (e) {
                    console.error('Erro ao processar o XML do SEI: ' + e.responseText);
                }
            });

            return true;
        };

    }

    //campo de upload de documentos essenciais
    function carregarCamposDocComplementarUpload() {

        objComplementarUpload = new infraUpload('frmDocumentosComplementares', '<?=$strLinkUploadDocComplementar?>');

        objComplementarUpload.finalizou = function (arr) {

            var nomeUpload = arr['nome_upload'];
            var nome = arr['nome'];
            var data = arr['data'];
            var dataHora = arr['data_hora'];
            var tamanho = arr['tamanho'];
            var dataHoraFormatada = '<?= time() ?>';
            var tamanhoFormatado = infraFormatarTamanhoBytes(arr['tamanho']);

            //Tamanho
            var tamanhoMb = tamanho / 1024 / 1024;
            var tamanhoPermitidoMb = parseInt(document.getElementById('hdnTamArquivoComplementar').value.toLowerCase().replace(' mb', ''));
            if (tamanhoMb > tamanhoPermitidoMb) {
                alert('Arquivo Complementar com tamanho maior que o permitido. \nTamanho do arquivo: ' + tamanhoMb.toFixed(2) + 'Mb\nTamanho permitido: ' + tamanhoPermitidoMb + 'Mb');
                return false;
            }

            //Nome
            var linhas = document.getElementById('tbDocumentoComplementar').rows;
            var tamanhoInserido = 0;
            var tamanhoVerificar = 0;

            for (var i = 1; i < linhas.length; i++) {
                //Nome igual
                if (nome.toLowerCase().trim() == linhas[i].cells[0].innerText.toLowerCase().trim()) {
                    alert('Não é permitido adicionar documento com o mesmo nome de arquivo.');
                    return false;
                }
            }

            //concatenacao de "Tipo" e "Complemento"
            var cbTpoComplementar = document.getElementById('tipoDocumentoComplementar');

            //abordagem anti-XSS client side
            var htmlComplemento = document.getElementById('complementoComplementar').value;
            var escaped = $("<pre>").text(htmlComplemento).html();
            var strComplemento = escaped;

            var documento = getStrTipoDocumento(cbTpoComplementar.value, 'Complementar') + ' ' + strComplemento;

            var nivelAcesso = getStrNivelAcesso(document.getElementById('nivelAcesso3').value);

            var hipoteseLegal = '';
            var cbHipotese = document.getElementById('hipoteseLegal3');

            if (cbHipotese != null && cbHipotese != undefined) {
                hipoteseLegal = cbHipotese.value;
            }

            var formatoDocumento = $('input[name="formatoDocumentoComplementar"]:checked').val();
            var formatoDocumentoLbl = 'Nato-digital';
            if (formatoDocumento != 'nato') {
                formatoDocumentoLbl = 'Digitalizado';
            }

            var tipoConferencia = document.getElementById('TipoConferenciaComplementar').value;

            objTabelaDocComplementar.adicionar([nome, dataHora, tamanhoFormatado, documento, nivelAcesso, hipoteseLegal, formatoDocumento, tipoConferencia, nomeUpload, cbTpoComplementar.value, strComplemento, formatoDocumentoLbl, '']);

            var strHashComplementar = document.getElementById('hdnDocComplementar').value;
            var arrHashComplementar = strHashComplementar.split('±');

            <? $linkBase = PaginaSEIExterna::getInstance()->formatarXHTML(SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=md_pet_usu_ext_download&id_tipo_procedimento=' . $_GET['id_tipo_procedimento'] . '&id_orgao_acesso_externo=0')); ?>
            var urlBase = "<?= $linkBase ?>";

            document.getElementById('hdnNomeArquivoDownload').value = arrHashComplementar[0];
            document.getElementById('hdnNomeArquivoDownloadReal').value = arrHashComplementar[1];

            objTabelaDocComplementar.adicionarAcoes(
                arr['nome'],
                "",
                false,
                true);

            document.getElementById("fileArquivoComplementar").value = '';
            $(document).find('#ifrProgressofrmDocumentosComplementares').hide();
            limparCampoUpload('3');

            var table = document.getElementById("tbDocumentoComplementar");

            for (var i = 0, row; row = table.rows[i]; i++) {

                for (var j = 0, col; col = row.cells[j]; j++) {
                    col.setAttribute("valign", "middle");
                }

            }
        }

        objComplementarUpload.validar = function (arr) {

            //INICIO VALIDACAO EXTENSOES
            var arrExtensoesPermitidas = [<?=$strSelExtensoesComp?>];
            if ($("#fileArquivoComplementar").val().replace(/^.*\./, '') != '' && $.inArray($("#fileArquivoComplementar").val().replace(/^.*\./, '').toLowerCase(), arrExtensoesPermitidas) == -1) {
                alert("O arquivo selecionado não é permitido.\nSomente são permitidos arquivos com as extensões:\n<?=preg_replace("%'%", " ", $strSelExtensoesComp)?> .");
                return false;
            }
            //FIM VALIDACAO EXTENSOES

            var arquivoComplementar = document.getElementById('fileArquivoComplementar').value;
            var ext = (arquivoComplementar.substring(arquivoComplementar.lastIndexOf(".")).toLowerCase()).split('.')[1];

            var extPermitida = false;
            var listaExtensoes = $('#hdnArquivosPermitidosEssencialComplementar').val();
            var obj = JSON.parse(listaExtensoes);

            for (var index in obj) {

                if (obj[index] == ext) {
                    extPermitida = true;
                }
            }

            if (ext != undefined && ext != '' && !extPermitida) {
                document.getElementById('fileArquivoComplementar').value = '';
                alert('A extensão do arquivo não é permitida.');
            }

            return extPermitida;

        }

        //Monta tabela de anexos
        objTabelaDocComplementar = new infraTabelaDinamica('tbDocumentoComplementar', 'hdnDocComplementar', false, false);
        objTabelaDocComplementar.gerarEfeitoTabela = true;

        objTabelaDocComplementar.remover = function (arrLinha) {
            //remove o arquivo da pasta temp
            $.ajax({
                url: '<?=$strUrlMdPetUsuExtRemoverUploadArquivo?>',
                type: 'POST',
                async: false,
                dataType: 'XML',
                data: {hdnTbDocumento: arrLinha},
                success: function (r) {
                },
                error: function (e) {
                    console.error('Erro ao processar o XML do SEI: ' + e.responseText);
                }
            });

            return true;
        };

    }

    function carregarComponenteLupaInteressados(tipoAcao) {

        if (tipoAcao == 'S') {
            objLupaInteressados.selecionar(900, 900);
        } else if (tipoAcao == 'R') {
            objLupaInteressados.remover();
        }

    }

    function mascaraTexto(elem, evento) {

        var formPeticionamento = document.getElementById('frmPeticionamentoCadastro');

        if (formPeticionamento.tipoPessoa.value == 'pf') {
            return infraMascaraCpf(elem, evento);
        } else if (formPeticionamento.tipoPessoa.value == 'pj') {
            return infraMascaraCnpj(elem, evento);
        } else {
            return false;
        }

    }

    function selecionarPF() {

        document.getElementById('descTipoPessoa').innerHTML = 'CPF:';
        document.getElementById('descNomePessoa').innerHTML = 'Nome:';

        document.getElementById('divSel1').style.display = 'inline';
        document.getElementById('divSel2').style.display = 'inline';

        document.getElementById('txtNomeRazaoSocial').value = '';
        document.getElementById('txtNomeRazaoSocial').style.display = 'inline';
        document.getElementById('btAdicionarInteressado').style.display = 'inline';

        document.getElementById('txtCPF').style.display = 'inline';
        document.getElementById('txtCNPJ').value = '';
        document.getElementById('txtCNPJ').style.display = 'none';
        document.getElementById('btValidarCPFCNPJ').style.visibility = 'visible';

    }

    function selecionarPJ() {

        document.getElementById('descTipoPessoa').innerHTML = 'CNPJ:';
        document.getElementById('descNomePessoa').innerHTML = 'Razão Social:';

        document.getElementById('divSel1').style.display = 'inline';
        document.getElementById('divSel2').style.display = 'inline';

        document.getElementById('txtNomeRazaoSocial').value = '';
        document.getElementById('txtNomeRazaoSocial').style.display = 'inline';
        document.getElementById('btAdicionarInteressado').style.display = 'inline';

        document.getElementById('txtCPF').style.display = 'none';
        document.getElementById('txtCPF').value = '';

        document.getElementById('txtCNPJ').style.display = 'inline';
        document.getElementById('btValidarCPFCNPJ').style.visibility = 'visible';

    }

    function selecionarFormatoDigitalizadoEssencial() {
        document.getElementById("camposDigitalizadoEssencial").style.display = 'block';
        document.getElementById("camposDigitalizadoEssencialBotao").style.display = 'none';
    }

    function selecionarFormatoNatoDigitalEssencial() {
        document.getElementById("camposDigitalizadoEssencial").style.display = 'none';
        document.getElementById("camposDigitalizadoEssencialBotao").style.display = 'block';
        //retornando a combo para seu valor inicial
        document.getElementById("TipoConferenciaEssencial").selectedIndex = 0;
    }

    function selecionarFormatoDigitalizadoComplementar() {
        document.getElementById("camposDigitalizadoComplementarBotao").style.display = 'none';
        document.getElementById("camposDigitalizadoComplementar").style.display = 'block';
        //retornando a combo para seu valor inicial
        document.getElementById("TipoConferenciaComplementar").selectedIndex = 0;
    }

    function selecionarFormatoNatoDigitalComplementar() {
        document.getElementById("camposDigitalizadoComplementar").style.display = 'none';
        document.getElementById("camposDigitalizadoComplementarBotao").style.display = 'block';
    }

    function selecionarFormatoDigitalizadoPrincipal() {
        document.getElementById("camposDigitalizadoPrincipalBotao").style.display = 'none';
        document.getElementById("camposDigitalizadoPrincipal").style.display = 'block';
        //retornando a combo para seu valor inicial
        document.getElementById("TipoConferenciaPrincipal").selectedIndex = 0;
    }

    function selecionarFormatoNatoDigitalPrincipal() {
        document.getElementById("camposDigitalizadoPrincipalBotao").style.display = 'block';
        document.getElementById("camposDigitalizadoPrincipal").style.display = 'none';
    }

    function returnDateTime(valor) {

        valorArray = valor != '' ? valor.split(" ") : '';

        if (Array.isArray(valorArray)) {
            var data = valorArray[0]
            data = data.split('/');
            var mes = parseInt(data[1]) - 1;
            var horas = valorArray[1].split(':');

            var segundos = typeof horas[2] != 'undefined' ? horas[2] : '00';
            var dataCompleta = new Date(data[2], mes, data[0], horas[0], horas[1], segundos);
            return dataCompleta;
        }

        return false;
    }

    function OnSubmitForm() {

        return true;
    }

    function downloadArquivo(urlBaseDownload) {

        var actionAnterior = document.getElementById("frmPeticionamentoCadastro").action;
        var targetAnterior = document.getElementById("frmPeticionamentoCadastro").target;

        document.getElementById("frmPeticionamentoCadastro").action = urlBaseDownload;
        document.getElementById("frmPeticionamentoCadastro").target = "_blank";
        document.getElementById("frmPeticionamentoCadastro").submit();

        document.getElementById("frmPeticionamentoCadastro").action = actionAnterior;
        document.getElementById("frmPeticionamentoCadastro").target = targetAnterior;

    }

    function concluirAssinarPeticionamento() {

        var formCadastro = document.getElementById('frmPeticionamentoCadastro');
        formCadastro.target = "concluirPeticionamento";
        formCadastro.submit();
    }

    window.CallParent = function () {
        concluirAssinarPeticionamento();
    }

    function selectNivelAcesso(idNivelAcesso, idHipoteseLegal) {

        var valorSelectNivelAcesso = document.getElementById(idNivelAcesso).value;

        if (valorSelectNivelAcesso == '1') {
            //mostrar combo e selecionar se existe existe
            if (document.getElementById(idHipoteseLegal) != null) {
                document.getElementById(idHipoteseLegal).selectedIndex = 0;
            }
            document.getElementById('div' + idHipoteseLegal).style.display = 'block';
        } else {
            //ocultar combo e limpar a seleção da combo
            if (document.getElementById(idHipoteseLegal) != null) {
                document.getElementById(idHipoteseLegal).selectedIndex = 0;
            }
            document.getElementById('div' + idHipoteseLegal).style.display = 'none';
        }

    }

    function adicionarInteressadoValido() {

        var txtCPF = window.top.document.getElementById("txtCPF").value;
        var txtCNPJ = window.top.document.getElementById("txtCNPJ").value;
        var txtCPFCNPJ = '';

        if (txtCPF != "") {
            txtCPFCNPJ = txtCPF;
        } else if (txtCNPJ != "") {
            txtCPFCNPJ = txtCNPJ;
        }

        var hdnCustomizado = window.top.document.getElementById("hdnCustomizado").value;
        var txtNomeRazaoSocial = window.top.document.getElementById("txtNomeRazaoSocial").value;
        var hdnIdInteressadoCadastrado = window.top.document.getElementById('hdnIdInteressadoCadastrado').value;
        var chkTipoPessoaFisica = window.top.document.getElementById("optTipoPessoaFisica").checked;
        var chkTipoPessoaJuridica = window.top.document.getElementById("optTipoPessoaJuridica").checked;
        var hdnUsuarioLogado = window.top.document.getElementById("hdnUsuarioCadastro").value;

        //checar se o Nome / Razao Social foi preenchido
        if (txtNomeRazaoSocial == '') {

            if (chkTipoPessoaFisica) {
                alert('Antes é necessário validar o CPF.');
                return;
            } else if (chkTipoPessoaJuridica) {
                alert('Antes é necessário validar o CNPJ.');
                return;
            }

        }

        //adicionar o interessado na grid
        var arrDadosInteressadoValido = [];

        if (txtCPF != "") {
            arrDadosInteressadoValido[1] = "Pessoa Física";
        } else if (txtCNPJ != "") {
            arrDadosInteressadoValido[1] = "Pessoa Jurídica";
        }

        arrDadosInteressadoValido[2] = txtCPFCNPJ;
        arrDadosInteressadoValido[3] = txtNomeRazaoSocial;
        arrDadosInteressadoValido[0] = hdnIdInteressadoCadastrado;
        arrDadosInteressadoValido[4] = hdnUsuarioLogado;

        var bolInteressadoCustomizado = hdnCustomizado;

        //checar se o cpf ou cnpj informado já existe na grid
        var hdnListaInteressadosIndicados = document.getElementById('hdnListaInteressadosIndicados').value;

        if (hdnListaInteressadosIndicados != "") {

            //caractere de quebra de linha
            var arrHash = hdnListaInteressadosIndicados.split('¥');
            var quantidadeRegistro = arrHash.length;

            if (quantidadeRegistro == 0) {

                var cpfInserido = arrDadosInteressadoValido[2];

                //caractere de quebra de coluna
                var arrLocal = hdnListaInteressadosIndicados.split('±');
                var cpfLocal = arrLocal[2];

                if (cpfInserido == cpfLocal) {
                    alert('Não é permitido adicionar interessado com CPF ou CNPJ já adicionado.');
                    return;
                }

            } else if (quantidadeRegistro > 0) {

                var cpfInserido = arrDadosInteressadoValido[1];

                for (var i = 0; i < quantidadeRegistro; i++) {

                    var arrLocal = arrHash[i].split('±');
                    var cpfLocal = arrLocal[2];

                    if (cpfInserido == cpfLocal) {
                        alert('Não é permitido adicionar interessado com CPF ou CNPJ já adicionado.');
                        return;
                    }

                }

            }

        }

        receberInteressado(arrDadosInteressadoValido, bolInteressadoCustomizado);

    }

    function atualizarNomeRazaoSocial(cpfEditado, nomeEditado) {

        var table = document.getElementById("tbInteressadosIndicados");
        var linhas = table.rows;

        for (var i = 0; row = table.rows[i]; i++) {

            row = table.rows[i];

            if (i > 0) {

                cpflinha = row.cells[2].innerHTML;
                nomeRazaoSocialLinha = row.cells[3].innerHTML;

                cpfEditado = '<div>' + cpfEditado + '</div>';

                if (cpflinha == cpfEditado) {
                    row.cells[3].innerHTML = '<div>' + nomeEditado + '</div>';
                }

            }

        }

    }

    function alterandoCPF(objeto, evt) {
        document.getElementById('txtNomeRazaoSocial').value = '';
        document.getElementById('hdnIdInteressadoCadastrado').value = '';
        return infraMascaraCpf(objeto, evt);

    }

    function alterandoCNPJ(objeto, evt) {
        document.getElementById('txtNomeRazaoSocial').value = '';
        document.getElementById('hdnIdInteressadoCadastrado').value = '';
        return infraMascaraCnpj(objeto, evt);

    }

    //Novo

    function pesquisarUF(idOrgao) {
        document.getElementById("selUF").disabled = false;
        document.getElementById("selUFAberturaProcesso").disabled = false;
        document.getElementById('hdnIdOrgao').value = idOrgao.value;
        //Caso selecione vazio na combo orgão, sumir com a combo uf
        if (document.getElementById("selOrgao").value == "") {
            document.getElementById("ufHidden").style.display = "none";
            document.getElementById("cidadeHidden").style.display = "none";
            return false;
        }

        infraSelectLimpar('selUF');
        infraSelectLimpar('selUFAberturaProcesso');

        $.ajax({
            dataType: 'xml',
            method: 'POST',
            url: '<?php echo SessaoSEIExterna::getInstance()->assinarLink('controlador_ajax_externo.php?acao_ajax=md_pet_consultar_tipo_processo_uf');?>',
            data: {
                'idOrgao': idOrgao.value,
                'idTpProc': document.getElementById('hdnTpProcesso').value
            },
            error: function (dados) {
                console.log(dados);
            },
            success: function (data) {

                var selectMultiple = document.getElementById('selUF');

                try {

                    var count = $(data).find("item").length;
                    if (count < 2) {
                        document.getElementById("ufHidden").style.display = "none";
                    } else {
                        document.getElementById("ufHidden").style.display = "";
                    }


                    if (count > 1) {
                        var opt = document.createElement('option');
                        opt.value = "";
                        opt.innerHTML = "";
                        selectMultiple.appendChild(opt);
                    }

                    $.each($(data).find('item'), function (i, j) {
                        //Quando retornar somente uma UF
                        var count = $(data).find("item").length;
                        if (count < 2) {
                            //document.getElementById('hdnIdUf').value = $(j).attr("id");

                            $.ajax({
                                dataType: 'xml',
                                method: 'POST',
                                url: '<?php echo SessaoSEIExterna::getInstance()->assinarLink('controlador_ajax_externo.php?acao_ajax=md_pet_consultar_tipo_processo_cidade');?>',
                                data: {
                                    'idOrgao': document.getElementById('hdnIdOrgao').value,
                                    'idUf': $(j).attr("id"),
                                    'idTpProc': document.getElementById('hdnTpProcesso').value
                                },
                                error: function (dados) {
                                    console.log(dados);
                                },
                                success: function (data) {


                                    var selectMultiple = document.getElementById('selUFAberturaProcesso');

                                    try {

                                        //Caso retorne somente uma cidade, travar a combo
                                        var count = $(data).find("item").length;
                                        if (count < 2) {
                                            document.getElementById("cidadeHidden").style.display = "none";
                                        } else {
                                            document.getElementById("cidadeHidden").style.display = "";
                                        }


                                        if (count > 1) {
                                            var opt = document.createElement('option');
                                            opt.value = "";
                                            opt.innerHTML = "";
                                            selectMultiple.appendChild(opt);
                                        } else {
                                            document.getElementById("selUFAberturaProcesso").disabled = true;
                                        }

                                        $.each($(data).find('item'), function (i, j) {

                                            var opt = document.createElement('option');
                                            opt.value = $(j).attr("id");
                                            opt.innerHTML = $(j).attr("descricao");
                                            selectMultiple.appendChild(opt);
                                        });


                                        var div = document.getElementById('selUFAberturaProcesso');
                                        div.appendChild(selectMultiple);

                                    } catch (err) {

                                    }

                                }

                            });


                            document.getElementById("selUF").disabled = true;
                        }

                        var opt = document.createElement('option');
                        opt.value = $(j).attr("id");
                        opt.innerHTML = $(j).attr("descricao");
                        selectMultiple.appendChild(opt);
                    });
                    var div = document.getElementById('selUF');
                    div.appendChild(selectMultiple);

                } catch (err) {

                }

            }

        });


    }

    //Uf
    function pesquisarCidade(idUf) {
        document.getElementById("selUFAberturaProcesso").disabled = false;
        document.getElementById('hdnIdUf').value = idUf.value;
        document.getElementById('hdnIdCidade').value = '';
        //Caso o usuário não tenha selecionado o combo orgão na combo da tela anterior
        if (document.getElementById('hdnIdOrgao').value == '') {
            document.getElementById('hdnIdOrgao').value = document.getElementById('hdnIdOrgaoTelaAnterior').value;
        }
        infraSelectLimpar('selUFAberturaProcesso');

        $.ajax({
            dataType: 'xml',
            method: 'POST',
            url: '<?php echo SessaoSEIExterna::getInstance()->assinarLink('controlador_ajax_externo.php?acao_ajax=md_pet_consultar_tipo_processo_cidade');?>',
            data: {
                'idOrgao': document.getElementById('hdnIdOrgao').value,
                'idUf': idUf.value,
                'idTpProc': document.getElementById('hdnTpProcesso').value
            },
            error: function (dados) {
                console.log(dados);
            },
            success: function (data) {

                var selectMultiple = document.getElementById('selUFAberturaProcesso');

                try {

                    //Caso retorne somente uma cidade, travar a combo e esconder
                    var count = $(data).find("item").length;

                    if (count < 2) {

                        document.getElementById("cidadeHidden").style.display = "none";
                    } else {
                        document.getElementById("cidadeHidden").style.display = "";
                    }

                    if (count > 1) {
                        var opt = document.createElement('option');
                        opt.value = "";
                        opt.innerHTML = "";
                        selectMultiple.appendChild(opt);
                    }

                    if (count < 2) {
                        document.getElementById("selUFAberturaProcesso").disabled = true;
                    }

                    $.each($(data).find('item'), function (i, j) {

                        var opt = document.createElement('option');
                        opt.value = $(j).attr("id");
                        opt.innerHTML = $(j).attr("descricao");
                        selectMultiple.appendChild(opt);
                    });

                    var div = document.getElementById('selUFAberturaProcesso');
                    div.appendChild(selectMultiple);


                } catch (err) {

                }

            }

        });

    }

    function pesquisarFinal(idCidade) {
        document.getElementById('hdnIdCidade').value = idCidade.value;
        //mudarTpProcesso();

    }

    <?php if(!empty($nivelAcessoDoc)): ?>

    $(document).ready(function(){

        var arrDocForcaNivelAcesso = JSON.parse('<?= json_encode($nivelAcessoDoc['documentos']) ?>');
        var nivel = "<?= $nivelAcessoDoc['nivel'] ?>";
        var hipotese = "<?= $nivelAcessoDoc['hipotese'] ?>";

        $('body').on('change', '#tipoDocumentoEssencial, #tipoDocumentoComplementar', function(){

            var self = $(this);
            var selectNivelAcesso = self.closest('form').find('select[id^="nivelAcesso"]');
            var selectHipoteseLegal = self.closest('form').find('select[id^="hipoteseLegal"]');
            var inputComplemento = self.closest('form').find('input[id^="complemento"]');

            if(arrDocForcaNivelAcesso.indexOf(self.val()) !== -1){

                selectNivelAcesso.val(nivel).prop('disabled', true);
                selectNivelAcesso.closest('div').append('<input type="hidden" value="'+nivel+'" id="'+selectNivelAcesso.attr('id')+'" name="'+selectNivelAcesso.attr('id')+'" tabindex="-1"/>');

                selectHipoteseLegal.val(hipotese).prop('disabled', true);
                selectHipoteseLegal.closest('div').append('<input type="hidden" value="'+hipotese+'" id="'+selectHipoteseLegal.attr('id')+'" name="'+selectHipoteseLegal.attr('id')+'" tabindex="-1"/>');

                selectHipoteseLegal.closest('div.form-group').show();
                if(inputComplemento.val() == '') {
                    inputComplemento.focus();
                }

            }else{

                selectNivelAcesso.val('').prop('disabled', false);
                selectNivelAcesso.closest('div').find('input[id="'+selectNivelAcesso.attr('id')+'"]').remove();

                selectHipoteseLegal.val('').prop('disabled', false);
                selectHipoteseLegal.closest('div').find('input[id="'+selectHipoteseLegal.attr('id')+'"]').remove();

                if(selectNivelAcesso.is(':disabled') === false){
                    selectHipoteseLegal.closest('div.form-group').hide();
                }else{
                    selectHipoteseLegal.closest('div.form-group').show();
                }
                
                if(inputComplemento.val() == ''){
                    inputComplemento.focus();
                }else{
                    selectNivelAcesso.focus();
                }

            }

        });

    });

    <?php endif; ?>

</script>