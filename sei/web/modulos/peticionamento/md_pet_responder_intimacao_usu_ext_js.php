<script type="text/javascript">
    "use strict";

    var RESTRITO = '<?= ProtocoloRN::$NA_RESTRITO ?>';
    var TAMANHO_MAXIMO = '<?= $tamanhoMaximo ?>';
    var EXIBIR_HIPOTESE_LEGAL = '<?= $exibirHipoteseLegal ?>';
    var arrExtensoesPermitidas = [<?= $extensoesPermitidas ?>];
    var objAjaxSelectTipoDocumento = null;
    var objAjaxSelectHipoteseLegal = null;
    var objUploadArquivo = null;
    var objTabelaDinamicaDocumento = null;

    function inicializar() {

        infraEfeitoTabelas();
        if (EXIBIR_HIPOTESE_LEGAL) {
            verificarHipoteseLegal();
        }
        iniciarObjUploadArquivo();
        iniciarTabelaDinamicaDocumento();

    }

    function setaCamposNivelHipotese() {

        var arrDocForcaNivelAcesso = JSON.parse('<?= json_encode($nivelAcessoDoc['documentos']) ?>');
        var nivelDocForcaNivelAcesso = '<?= $nivelAcessoDoc['nivel'] ?>';
        var hipoteseDocForcaNivelAcesso = '<?= $nivelAcessoDoc['hipotese'] ?>';

        var nivelTpProcesso = '<?= $nivelAcessoTpProc['nivel'] ?>';
        var hipoteseTpProcesso = '<?= $nivelAcessoTpProc['hipotese'] ?>';

        var possuiConfiguracaoIntercorrentePadrao = (nivelDocForcaNivelAcesso != null || nivelDocForcaNivelAcesso !== '') && (hipoteseDocForcaNivelAcesso != null || hipoteseDocForcaNivelAcesso !== '');

        var selNivelAcesso      = document.getElementById('selNivelAcesso');
        var selHipoteseLegal    = document.getElementById('selHipoteseLegal');
        var divHipoteseLegal    = document.getElementById('divBlcHipoteseLegal');
        var selTipoDocumento    = document.getElementById('selTipoDocumento');

        $('#selNivelAcesso').val('').prop('disabled', false);
        $('#selHipoteseLegal').val('').prop('disabled', false);
        divHipoteseLegal.style.display = 'none';

        if (possuiConfiguracaoIntercorrentePadrao && arrDocForcaNivelAcesso.includes(selTipoDocumento.value)) {

            // Forca o Nivel de Acesso da configuração padrão
            $('#selNivelAcesso').val(nivelDocForcaNivelAcesso).prop('disabled', true);
            $('#hdnNivelAcesso').val(nivelDocForcaNivelAcesso);

            $('#selHipoteseLegal').val(hipoteseDocForcaNivelAcesso).prop('disabled', true);
            $('#hdnHipoteseLegal').val(hipoteseDocForcaNivelAcesso);

            divHipoteseLegal.style.display = 'block';
        
        }else{

            // Mantém a configuração do Tipo de Processo
            if (nivelTpProcesso != null && nivelTpProcesso !== '') {
                $('#selNivelAcesso').val(nivelTpProcesso).prop('disabled', true);
                $('#hdnNivelAcesso').val(nivelTpProcesso);
            }

            if (hipoteseTpProcesso != null && hipoteseTpProcesso !== '') {
                $('#selHipoteseLegal').val(hipoteseTpProcesso).prop('disabled', true);
                $('#hdnHipoteseLegal').val(hipoteseTpProcesso);
            }

            if (nivelTpProcesso == '<?= ProtocoloRN::$NA_RESTRITO ?>') {
                divHipoteseLegal.style.display = 'block';
            }
        }

    }

    function fechar() {
        document.location = '<?= $strUrlFechar ?>';
    }

    function iniciarTabelaDinamicaDocumento() {
        objTabelaDinamicaDocumento = new infraTabelaDinamica('tbDocumento', 'hdnTbDocumento', false, true);
        objTabelaDinamicaDocumento.gerarEfeitoTabela = true;
        objTabelaDinamicaDocumento.remover = function () {
            verificarTabelaVazia(2);
            return true;
        };
    }

    function exibirTipoConferencia() {
        var formatoDigitalizado = document.getElementById('rdoDigitalizado');
        var divTipoConferencia = document.getElementById('divTipoConferencia');
        var selTipoConferencia = document.getElementById('selTipoConferencia');
        var divTipoConferenciaBotao = document.getElementById('divTipoConferenciaBotao');
        selTipoConferencia.value = 'null';

        if (formatoDigitalizado.checked) {
            divTipoConferencia.style.display = 'block';
            divTipoConferenciaBotao.style.display = 'none';
        } else {
            divTipoConferencia.style.display = 'none';
            divTipoConferenciaBotao.style.display = '';
        }
    }

    function alterarHidden(val) {

        document.getElementById('hdnIdMdPetIntRelDest').value = val.value

        $.ajax({
            dataType: 'xml',
            method: 'POST',
            url: '<?php echo SessaoSEIExterna::getInstance()->assinarLink('controlador_ajax_externo.php?acao_ajax=md_pet_consultar_contato_intimacao'); ?>',
            data: {
                'id': val.value
            },
            error: function (dados) {
                console.log(dados);
            },
            success: function (data) {
                //console.log(data);
                document.getElementById('hdnIdContato').value = $(data).find('idContato').text();
                document.getElementById('hdnIdMdPetIntimacao').value = $(data).find('idIntimacao').text();
                document.getElementById('hdnIdMdPetIntAceite').value = $(data).find('idAceite').text();
            }

        });

    }

    function exibirHipoteseLegal() {
        var selNivelAcesso = document.getElementById('selNivelAcesso');
        var divBlcHipoteseLegal = document.getElementById('divBlcHipoteseLegal');
        var selHipoteseLegal = document.getElementById('selHipoteseLegal');
        var hdnNivelAcesso = document.getElementById('hdnNivelAcesso');
        hdnNivelAcesso.value = selNivelAcesso.value;

        if (selNivelAcesso.value == RESTRITO && EXIBIR_HIPOTESE_LEGAL) {
            divBlcHipoteseLegal.style.display = '';
            selHipoteseLegal.value = '';
            selHipoteseLegal.class = 'infraSelect form-control';
        } else {
            divBlcHipoteseLegal.style.display = 'none';
        }
    }

    function adicionarDocumento() {
        if (validarDocumento()) {
            objUploadArquivo.executar();
            document.getElementById('tbDocumento').style.display = '';
        }
    }

    function responderIntimacao() {

        var relDest = document.getElementById('hdnIdMdPetIntRelDest').value;
        var frm = document.getElementById('frmResponderIntimacao');

        if (validarResposta()) {     
            
            var paramsAjax = {
                idRelDest: relDest,
                idProcedimento: document.getElementById('hdnIdProcedimento').value
            };
            
            //verifica caso o usuário logado seja procurador se a procuração do mesmo está vigente
            $.ajax({
                url: '<?php echo SessaoSEIExterna::getInstance()->assinarLink('controlador_ajax_externo.php?acao_ajax=md_pet_validar_procuracao')?>',
                type: 'POST',
                dataType: 'XML',
                data: paramsAjax,
                async: false,
                success: function (result) {
                    if($(result).find('valor').text() == 'T'){
                        //@todo trocar url para a correta do intercorrente ou nova do resposta  
                        parent.infraAbrirJanelaModal('<?= $strResponderIntimacaoModel ?>',
                            770,
                            550,
                            '', //options
                            false); //modal
                    }else{                
//                        var texto = 'Você não possui mais permissão para responder a Intimação Eletrônica, conforme abaixo:\n\n';
//                        
//                        if($(result).find('tipo').text() == 'F'){
//                            texto = texto + 'Pessoa Física: '+$(result).find('nome').text()+' ('+$(result).find('documento').text()+'), verifique seus Poderes de Representação.';
//                        }else{
//                            texto = texto + 'Pessoa Jurídica: '+$(result).find('nome').text()+' ('+$(result).find('documento').text()+'), verifique seus Poderes de Representação.';
//                        }
                        infraAbrirJanela(atob($(result).find('contato').text()), 'janelaConsultarIntimacao', 900, 350)
                        return false;
                    }
                }
            });
        }
    }

    function iniciarObjUploadArquivo() {
        var tbDocumento = document.getElementById('tbDocumento');
        objUploadArquivo = new infraUpload('frmResponderIntimacao', '<?= $strUrlUploadArquivo ?>');
        objUploadArquivo.finalizou = function (arr) {

            //Tamanho do Arquivo
            var fileArquivo = document.getElementById('fileArquivo');
            var tamanhoArquivo = (arr['tamanho'] / 1024 / 1024).toFixed(2);
            if (tamanhoArquivo > parseInt(TAMANHO_MAXIMO)) {
                alert('Tamanho máximo para o arquivo é de ' + TAMANHO_MAXIMO + 'Mb');
                fileArquivo.value = '';
                fileArquivo.focus();
                verificarTabelaVazia(1);
                return false;
            }


            //Arquivo com o mesmo nome já adicionado
            for (var i = 0; i < tbDocumento.rows.length; i++) {

                var tr = tbDocumento.getElementsByTagName('tr')[i];

                if (arr['nome'].toLowerCase().trim() == tr.cells[9].innerText.toLowerCase().trim()) {
                    alert('Não é permitido adicionar documento com o mesmo nome de arquivo.');
                    fileArquivo.value = '';
                    fileArquivo.focus();
                    verificarTabelaVazia(1);
                    return false;
                }

            }
            console.log(arr);
            criarRegistroTabelaDocumento(arr);
            corrigirPosicaoAcaoExcluir();
            limparCampoDocumento();
        };

        objUploadArquivo.validar = function () {
            var fileArquivo = document.getElementById('fileArquivo');
            var ext = fileArquivo.value.split('.').pop().toLowerCase();
            var extensaoConfigurada = arrExtensoesPermitidas.length > 0;

            var tamanhoConfigurado = parseInt(TAMANHO_MAXIMO) > 0;
            if (!tamanhoConfigurado) {
                alert('Limite não configurado na Administração do Sistema.');
                fileArquivo.value = '';
                fileArquivo.focus();
                return false;
            }

            if (!extensaoConfigurada) {
                alert('Extensão de Arquivos Permitidos não foi configurado na Administração do Sistema.');
                fileArquivo.value = '';
                fileArquivo.focus();
                return false;
            }

            var arquivoPermitido = arrExtensoesPermitidas.indexOf(ext) != -1;
            if (!arquivoPermitido) {
                alert("O arquivo selecionado não é permitido.\n" +
                        "Somente são permitidos arquivos com as extensões:\n" +
                        arrExtensoesPermitidas.join().replace(/,/g, ' '));
                fileArquivo.value = '';
                fileArquivo.focus();
                return false;
            }
            return true;
        };
    }

    function limparCampoDocumento() {

        document.getElementById('fileArquivo').value = '';
        document.getElementById('selTipoDocumento').value = 'null';
        document.getElementById('txtComplementoTipoDocumento').value = '';

        document.getElementById('rdoNatoDigital').checked = false;
        document.getElementById('rdoDigitalizado').checked = false;
        document.getElementById('selTipoConferencia').value = 'null';
        document.getElementById('divTipoConferencia').style.display = 'none';
        document.getElementById('divTipoConferenciaBotao').style.display = 'block';

        document.getElementById('selNivelAcesso').value = 'null';
        document.getElementById('selHipoteseLegal').value = 'null';

        $('#selNivelAcesso').prop('disabled', false);
        $('#selHipoteseLegal').prop('disabled', false);
        document.getElementById('divBlcHipoteseLegal').style.display = 'none';

        setaCamposNivelHipotese();

    }

    function validarDocumento() {
        var tipoDocumento = document.getElementById('selTipoDocumento');
        tipoDocumento = tipoDocumento.options[tipoDocumento.selectedIndex];

        var complementoTipoDocumento = document.getElementById('txtComplementoTipoDocumento').value.trim();

        var formato = document.getElementsByName('rdoFormato');
        var formatoInformado = false;
        for (var i = 0; i < formato.length; i++) {
            if (formato[i].checked) {
                formatoInformado = true;
                break;
            }
        }
        var selTipoConferencia = document.getElementById('selTipoConferencia');
        var tipoConferencia = document.getElementById('selTipoConferencia');
        tipoConferencia = tipoConferencia.options[tipoConferencia.selectedIndex];

        var fileArquivo = document.getElementById('fileArquivo');

        if (fileArquivo.value.trim() == '') {
            alert('Informe o arquivo para upload.');
            fileArquivo.focus();
            return false;
        }

        if (tipoDocumento == null || tipoDocumento.value == 'null') {
            alert('Informe o Tipo de Documento.');
            document.getElementById('selTipoDocumento').focus();
            return false;
        }

        if (complementoTipoDocumento == '') {
            alert('Informe o Complemento do Tipo de Documento. \nPara mais informações, clique no ícone de Ajuda ao lado do nome do campo.');
            document.getElementById('txtComplementoTipoDocumento').focus();
            return false;
        }


        var selNivelAcesso = document.getElementById('selNivelAcesso');
        if (selNivelAcesso.nodeName == 'SELECT') {
            var nivelAcesso = selNivelAcesso.options[selNivelAcesso.selectedIndex];
            if (nivelAcesso == null || nivelAcesso.value == '') {
                alert('Informe o Nível de Acesso.');
                document.getElementById('selNivelAcesso').focus();
                return false;
            }
        }

        if (EXIBIR_HIPOTESE_LEGAL) {
            var selHipoteseLegal = document.getElementById('selHipoteseLegal');
            if (selHipoteseLegal.nodeName == 'SELECT' && selHipoteseLegal.offsetHeight > 0) {
                var hipoteseLegal = selHipoteseLegal.options[selHipoteseLegal.selectedIndex];
                if (hipoteseLegal == null || hipoteseLegal.value == '') {
                    alert('Informe a Hipótese Legal.');
                    selHipoteseLegal.focus();
                    return false;
                }
            }
        }

        if (!formatoInformado) {
            alert('Informe o Formato do Documento.');
            document.getElementById('rdoNatoDigital').focus();
            return false;
        }

        if (selTipoConferencia.offsetHeight > 0) {
            if (tipoConferencia == null || tipoConferencia.value == 'null') {
                alert('Informe a Conferência com o documento digitalizado.');
                selTipoConferencia.focus();
                return false;
            }
        }

        return true;

    }


    function criarRegistroTabelaDocumento(arr) {
        var nomeArquivo = arr['nome'];
        var nomeArquivoHash = arr['nome_upload'];
        var tamanhoArquivo = arr['tamanho'];
        var tamanhoArquivoFormatado = infraFormatarTamanhoBytes(tamanhoArquivo);
        var dataHora = arr['data_hora'];

        var rdoNatoDigital = document.getElementById('rdoNatoDigital');
        var rdoDigitalizado = document.getElementById('rdoDigitalizado');
        var formato = rdoNatoDigital.checked ? document.getElementById('labelrdoNatoDigital').innerHTML :
            document.getElementById('labelrdoDigitalizado').innerHTML;

        var tipoDocumento = document.getElementById('selTipoDocumento');
        tipoDocumento = tipoDocumento.options[tipoDocumento.selectedIndex].text;

        var complementoTipoDocumento = document.getElementById('txtComplementoTipoDocumento').value.trim();
        complementoTipoDocumento = $("<pre>").text(complementoTipoDocumento).html();
        var documento = tipoDocumento + ' ' + complementoTipoDocumento;

        var nivelAcesso = document.getElementById('selNivelAcesso');
        if (nivelAcesso.nodeName == 'SELECT') {
            nivelAcesso = nivelAcesso.options[nivelAcesso.selectedIndex].text;
        } else {
            nivelAcesso = nivelAcesso.innerHTML.trim();
        }

        var idLinha = gerarIdDocumento();
        var idTipoDocumento = document.getElementById('selTipoDocumento').value;
        var complementoTipoDocumento = document.getElementById('txtComplementoTipoDocumento').value;
        var idNivelAcesso = document.getElementById('hdnNivelAcesso').value;

        var idHipoteseLegal;
        if (EXIBIR_HIPOTESE_LEGAL) {
            idHipoteseLegal = document.getElementById('hdnHipoteseLegal').value;
        }

        var idFormato = rdoNatoDigital.checked ? rdoNatoDigital.value : rdoDigitalizado.value;
        var idTipoConferencia = document.getElementById('selTipoConferencia').value;

        var dados = [
            idLinha, idTipoDocumento, complementoTipoDocumento, idNivelAcesso,
            idHipoteseLegal, idFormato, idTipoConferencia, nomeArquivoHash, tamanhoArquivo, nomeArquivo,
            dataHora, tamanhoArquivoFormatado, documento, nivelAcesso, formato
        ];

        objTabelaDinamicaDocumento.adicionar(dados);
    }

    function mudarSelect(val) {
        infraSelectLimpar('selTipoResposta');
        validacaoAjaxTipoResp(val.value);
    }

    function validacaoAjaxTipoResp(valor) {

        $.ajax({
            dataType: 'xml',
            method: 'POST',
            url: '<?php echo SessaoSEIExterna::getInstance()->assinarLink('controlador_ajax_externo.php?acao_ajax=md_pet_validar_resposta'); ?>',
            data: {
                'id': valor
            },
            error: function (dados) {
                console.log(dados);
            },
            success: function (data) {

                console.log(data);

                var selectMultiple = document.getElementById('selTipoResposta');

                $.each($(data).find('item'), function (i, j) {

                    var opt = document.createElement('option');
                    opt.value = $(j).attr("id");
                    opt.innerHTML = $(j).attr("descricao");
                    selectMultiple.appendChild(opt);
                    
                });

                var div = document.getElementById('selectTipoResp');
                div.appendChild(selectMultiple);

            }

        });

    }


    function verificarHipoteseLegal() {
        var selNivelAcesso = document.getElementById('selNivelAcesso');

        if (selNivelAcesso.nodeName == 'SELECT') {
            selNivelAcesso.addEventListener('change', exibirHipoteseLegal);
        }

    }

    function exibirFieldsetDocumentos(el) {
        var fieldDocumentos = document.getElementById('fieldDocumentos');
        var hdnNomeTipoResposta = document.getElementById('hdnNomeTipoResposta');
        fieldDocumentos.style.display = 'none';

        if (el.value != 'null') {

            fieldDocumentos.style.display = '';
            hdnNomeTipoResposta.value = el.options[el.selectedIndex].text.trim();
            objTabelaDinamicaDocumento.limpar();
            document.getElementById('tbDocumento').style.display = 'none';

            //fileArquivo
            document.getElementById('fileArquivo').value = '';

            //rdoDigitalizado
            document.getElementById('rdoDigitalizado').checked = false;

            //rdoNatoDigital
            document.getElementById('rdoNatoDigital').click();
            document.getElementById('rdoNatoDigital').checked = false;

            //selHipoteseLegal
            if (EXIBIR_HIPOTESE_LEGAL) {
                document.getElementById('selHipoteseLegal').selectedIndex = 0;
            }

            //selTipoConferencia
            document.getElementById('selTipoConferencia').selectedIndex = 0;

            //selNivelAcesso
            document.getElementById('selNivelAcesso').selectedIndex = 0;

            //txtComplementoTipoDocumento
            document.getElementById('txtComplementoTipoDocumento').value = '';

            //selTipoDocumento
            document.getElementById('selTipoDocumento').selectedIndex = 0;
        }

        setaCamposNivelHipotese();

    }

    function validarResposta() {
        var selTipoResposta = document.getElementById('selTipoResposta');
        var tbDocumento = document.getElementById('tbDocumento');


        if (selTipoResposta.value == 'null') {
            alert('Informe o Tipo de Resposta!');
            selTipoResposta.focus();
            return false;
        }

        var selRazaoSocial = document.getElementById('selRazaoSocial');
<?php if ($contador == 2) { ?>
            if (selRazaoSocial.value == 'null') {
                alert('Informe a Razão Social!');
                selRazaoSocial.focus();
                return false;
            }
<?php } ?>

        if (tbDocumento.rows.length <= 1) {
            alert('Informe ao menos um documento!');
            return false;
        }

        return true;
    }

    <?php if(!empty($nivelAcessoDoc)): ?>

    $(document).ready(function(){

        $('body').on('change', '#selTipoDocumento', function(){

            setaCamposNivelHipotese();

        });

    });

    <?php endif; ?>


</script>