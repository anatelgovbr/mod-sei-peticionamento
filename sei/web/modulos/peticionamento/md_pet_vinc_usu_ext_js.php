<?php
$strLinkConsultaVinculo = SessaoSEIExterna::getInstance()->assinarLink('controlador_ajax_externo.php?acao_ajax=md_pet_vinc_usu_ext_consulta_vinculo');
$strLinkConsultaReceita = SessaoSEIExterna::getInstance()->assinarLink('controlador_ajax_externo.php?acao_ajax=md_pet_vinc_usu_ext_consulta_receita');
$strLinkConsultaContato = SessaoSEIExterna::getInstance()->assinarLink('controlador_ajax_externo.php?acao_ajax=md_pet_vinc_usu_ext_consulta_contato');
$strLinkConsultaDadosUsuarioExterno = SessaoSEIExterna::getInstance()->assinarLink('controlador_ajax_externo.php?acao_ajax=md_pet_vinc_usu_ext_dados_usuario_externo');
$strLinkConsultaUsuarioExternoValido = SessaoSEIExterna::getInstance()->assinarLink('controlador_ajax_externo.php?acao_ajax=md_pet_vinc_consulta_usuext_valido');
$strLinkRedirecionamentoPrincipal = SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=md_pet_vinc_usu_ext');
$strLinkUploadArquivo = SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=md_pet_vinc_usu_ext_upload_anexo');
$strCaptcha = hash('SHA512', InfraCaptcha::gerar($strCodigoParaGeracaoCaptcha));
$strLinkAjaxUsuarios = SessaoSEIExterna::getInstance()->assinarLink('controlador_ajax_externo.php?acao_ajax=md_pet_vinc_usu_ext_autocompletar');
$strLinkVinculoUsuarioExternoNegado = SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=md_pet_vinc_usu_ext_negado');
?>

<script type="text/javascript">
    "use strict";

    var stWebservice = '<?php echo $stWebService ? 'true' : 'false' ?>';
    var stWebserviceBol = '<?php echo $stWebService ? 1 : 0 ?>';
    var RESTRITO = '<?php echo ProtocoloRN::$NA_RESTRITO ?>';
    var TAMANHO_MAXIMO = '<?php echo $tamanhoMaximo ?>';
    var EXIBIR_HIPOTESE_LEGAL = '<?php echo $exibirHipoteseLegal ?>';
    var arrExtensoesPermitidas = [<?php echo $extensoesPermitidas ?>];
    var objAjaxSelectTipoDocumento = null;
    var objAjaxSelectHipoteseLegal = null;
    var objUploadArquivo = null;
    var objTabelaDinamicaDocumento = null;
    var objAjaxCidade = null;

    //    var objAutoCompletarUsuario = null;

    function inicializar() {
        infraEfeitoTabelas();
        document.getElementsByTagName("BODY")[0].onresize = function() {
            resizeIFramePorConteudo()
        };
        if (EXIBIR_HIPOTESE_LEGAL) {
            verificarHipoteseLegal();
        }

        if (document.getElementById('tbDocumento') != null) {
            iniciarObjUploadArquivo();
            iniciarTabelaDinamicaDocumento();
            carregarDependenciaCidades();
            bloquearVirgula();
        }

        $("#txtNomeResponsavelLegalAlt").val($("#txtNomeResponsavelLegal").val());
        $("#txtNumeroCpfResponsavelAlt").val($("#txtNumeroCpfResponsavel").val());
        <?php if (!$stAlterar) : ?>
            //document.getElementById("txtCaptcha").addEventListener("keyup", controlarEnterValidarProcesso, false);
        <?php endif; ?>
        <?php if ($stWebService) : ?>
            //  document.getElementById("txtNumeroCpfProcurador").addEventListener("keyup", controlarEnterValidarUsuario, false);
        <?php endif; ?>

        $('#btnValidarSemWS').on('click', function() {
            carregarTelaNovoCnpj();
        });
        document.addEventListener("keypress", function(e) {
            if (e.key === 'Enter') {
                var btn = document.querySelector("#cardCaptcha button");
                btn.click();

            }
        });
    }


    function validarCpf(obj) {
        if (!infraValidarCpf(obj.value)) {
            alert('CPF informado é inválido.');
            obj.value = '';
            obj.focus();
        }
    }

    function carregarTelaNovoCnpj() {
        //Verificar se o cnpj já esta sendo utilizado num vinculo
        var qtdNuCPNJ = document.getElementById('txtNumeroCnpj').value.trim().length;
        var qtdTxtCaptcha = document.getElementById('txtCaptcha').value.trim().length;
        var valido = true;

        if (qtdNuCPNJ == 0) {
            alert('Antes, informe o CNPJ!');
            return false;
        }

        if (qtdTxtCaptcha == 0) {
            alert('Informe o código de confirmação.');
            return false;
        }

        $.ajax({
            dataType: 'xml',
            method: 'POST',
            url: '<?php echo $strLinkConsultaVinculo ?>',
            data: {
                'txtNumeroCnpj': $("#txtNumeroCnpj").val(),
                'txtCaptcha': $("#txtCaptcha").val()
            },
            error: function(dados) {
                console.log(dados);
            },
            success: function(data) {
                valido = $(data).find('success').text();

                var success = $.trim($('success', data).text()).length;
                var txtSuccess = $.trim($('success', data).text());

                if (success > 0) {
                    var message = $.trim($('msg', data).text());
                    if (txtSuccess == 'false') {
                        var procuracao = $.trim($('procuracao', data).text());
                        var url = $.trim($('url', data).text());
                        console.log(url);
                        if (procuracao == 'true') {
                            infraAbrirJanela(window.atob(url),
                                'Impedimento de Substitução de Responsável Legal',
                                770,
                                480,
                                '', //options
                                false); //modal
                            return false;
                        } else if (message.length > 0) {
                            alert(message);
                            return false;
                        }
                        document.getElementById('txtNumeroCnpj').value = '';
                        document.getElementById('txtNumeroCnpj').focus();
                    }
                }

                $('#hdnIdVinculo').val($('idVinculo', data).text());
                document.getElementById('hdnIsCnpjValidado').value = '1';

                var obj = document.getElementById('txtNumeroCnpj');
                var validCnpj = infraValidarCnpj(obj.value);

                if (!validCnpj) {
                    alert('CNPJ informado é inválido.');
                    obj.value = '';
                    obj.focus();

                } else {
                    exibirTodosCampos();
                    $("#stDeclaracao").show();
                    $.ajax({
                        dataType: 'xml',
                        method: 'POST',
                        url: '<?php echo $strLinkConsultaContato ?>',
                        data: {
                            'txtNumeroCnpj': $("#txtNumeroCnpj").val()
                        },
                        success: function(data) {
                            var success = $.trim($('success', data).text()).length;
                            if (success > 0) {
                                var message = $.trim($('msg', data).text());
                                alert(message);
                                return false;
                            }

                            $('#slTipoInteressado').val($('slTipoInteressado', data).text()).attr('readonly', false);
                            $('#txtRazaoSocial').val($('txtRazaoSocial', data).text()).attr('readonly', false);;
                            $('#txtLogradouro').val($('txtLogradouro', data).text()).attr('readonly', false);;
                            $('#txtBairro').val($('txtBairro', data).text()).attr('readonly', false);;
                            $('#slUf').val($('idUf', data).text()).attr('readonly', false);;
                            $('#selCidade').val($('txtLogradouro', data).text()).attr('readonly', false);;
                            $('#txtNumeroCEP').val($('txtNumeroCEP', data).text()).attr('readonly', false);;
                            var nomeCidade = $('txtCidade', data).text();
                            var idCidade = $('idCidade', data).text();

                            objAjaxCidade.executar();
                            objAjaxCidade.processarResultado = function() {
                                $('#selCidade').val(idCidade)
                            }


                            $("#stDeclaracao").show();
                            $("#fieldDocumentos").show();
                            $("#fieldDocumentos_BR").show();

                        }
                    })
                }

            }
        });

    }

    function exibirTodosCampos() {
        document.getElementById('informacaoPJ').style.display = '';
        document.getElementById('informacaoPJ_BR').style.display = '';
        document.getElementById('fieldDocumentos').style.display = '';
        document.getElementById('fieldDocumentos_BR').style.display = '';
    }


    function validoDeclaracaoOrWs() {
        var isChecked = document.getElementById('chkDeclaracao').checked;
        var validWebService = document.getElementById('hdnStaWebService').value == '1' && document.getElementById('hdnIsCnpjValidado').value == '1';

        if (isChecked || validWebService) {
            return true;
        }

        return false;
    }

    function esconderCamposPJ() {
        var obj = document.getElementById('txtNumeroCnpj');
        var qtdCaracteres = obj.value.length;
        var valido = true;

        //trata mascara
        var cnpjUsuExt = infraTrim(obj.value);
        if (cnpjUsuExt != '') {
            cnpjUsuExt = infraRetirarFormatacao(cnpjUsuExt);
            cnpjUsuExt = infraLPad(cnpjUsuExt, 14, '0');
            cnpjUsuExt = cnpjUsuExt.substring(0, 14);
            obj.value = cnpjUsuExt;

            //Coloca ponto entre o segundo e o terceiro dígitos
            cnpjUsuExt = cnpjUsuExt.replace(/^(\d{2})(\d)/, "$1.$2");
            //Coloca ponto entre o quinto e o sexto dígitos
            cnpjUsuExt = cnpjUsuExt.replace(/^(\d{2})\.(\d{3})(\d)/, "$1.$2.$3");
            //Coloca uma barra entre o oitavo e o nono dígitos
            cnpjUsuExt = cnpjUsuExt.replace(/\.(\d{3})(\d)/, ".$1/$2");
            //Coloca um hífen depois do bloco de quatro dígitos
            cnpjUsuExt = cnpjUsuExt.replace(/(\d{4})(\d)/, "$1-$2");

            obj.value = cnpjUsuExt;
        }

        if (qtdCaracteres == 18) {
            var validCnpj = infraValidarCnpj(obj.value);
            if (!validCnpj) {
                valido = false;
            }
        } else {
            valido = false;
        }

        var add = valido ? '' : 'none';

        if (validoDeclaracaoOrWs()) {
            document.getElementById('informacaoPJ').style.display = add;
            document.getElementById('informacaoPJ_BR').style.display = add;
        }

        if (!valido) {
            document.getElementById('hdnIsCnpjValidado').value = '0';
            document.getElementById('chkDeclaracao').checked = false;
        }


    }

    function mostrarEsconderCampos(campo) {


        var checked = campo.checked;

        if (checked) {
            addRemoverDestaqueDeclaracao(false);
        }

        var qtdNuCPNJ = document.getElementById('txtNumeroCnpj').value.trim().length;
        if (qtdNuCPNJ == 0) {
            alert('Antes, informe o CNPJ!');
            campo.checked = false;
            return false;
        }
        alert(document.getElementById('hdnIsCnpjValidado').value);
        var isCnpjValidado = document.getElementById('hdnIsCnpjValidado').value == '1';
        if (!isCnpjValidado) {
            alert('É necessário validar o CNPJ para continuar!');
            if (checked) {
                campo.checked = false;
            }

            return false;
        }



        carregarTelaNovoCnpj();


        var blocoPj = document.getElementById('informacaoPJ');
        var blocoPj_BR = document.getElementById('informacaoPJ_BR');
        var blocoDoc = document.getElementById('fieldDocumentos');
        var blocoDoc_BR = document.getElementById('fieldDocumentos_BR');
        var blocoUsuProcuracao = document.getElementById('fieldUsuarioProcuracao');
        var blocoUsuProcuracao_BR = document.getElementById('fieldUsuarioProcuracao_BR');

        if (checked) {

            $.ajax({
                dataType: 'xml',
                method: 'POST',
                url: '<?php echo $strLinkConsultaContato ?>',
                data: {
                    'txtNumeroCnpj': $("#txtNumeroCnpj").val()
                },
                success: function(data) {
                    var success = $.trim($('success', data).text()).length;
                    if (success > 0) {
                        var message = $.trim($('msg', data).text());
                        alert(message);
                        return false;
                    }

                    $('#slTipoInteressado').val($('slTipoInteressado', data).text());
                    $("#stDeclaracao").show();
                    $("#fieldDocumentos").show();
                    $("#fieldDocumentos_BR").show();

                    if (document.getElementById('hdnIdVinculo').value == '') {
                        $("#fieldUsuarioProcuracao").show();
                        $("#fieldUsuarioProcuracao_BR").show();
                    }

                    if (document.getElementById('hdnIsCnpjValidado').value == 1) {
                        if (document.getElementById('hdnIdVinculo').value == '') {
                            $("#informacaoPJ").show();
                            $("#informacaoPJ_BR").show();
                        }
                    }

                    var verificaContato = $.trim($('dados-pj', data).find('txtRazaoSocial').text());


                    if (verificaContato.length == 0) {
                        document.querySelectorAll('.blocInformacaoPj').forEach(function(campo) {
                            if (campo.value.trim().length == 0) {
                                campo.removeAttribute('readonly');
                            } else {
                                //    campo.readOnly = true;
                            }
                        });
                    }
                }
            })

        } else {
            blocoPj.style.display = 'none';
            blocoPj_BR.style.display = 'none';
            blocoDoc.style.display = 'none';
            blocoDoc_BR.style.display = 'none';
            blocoUsuProcuracao.style.display = 'none';
            blocoUsuProcuracao_BR.style.display = 'none';
        }
    }


    function createOptionCidade(idCidade, nomeCidade) {
        var optionNova = document.createElement("option");
        optionNova.value = idCidade;
        optionNova.text = nomeCidade;

        var selCidade = document.getElementById('selCidade');
        selCidade.innerHTML = '';
        selCidade.appendChild(optionNova);
    }

    function consultarVinculoExistenteCnpj() {
        let self = $('#txtNumeroCnpj');
        let vinculosExistentes = JSON.parse($('#hdnVinculoPreExistente').val());
        let novoVinculo = parseInt(self.val().replace(/\D/g,''));
        let message;
        if(self.val().length == 18) {
            if(vinculosExistentes.hasOwnProperty(parseInt(self.val().replace(/\D/g,'')))){
                $.each(vinculosExistentes, function(key, value){
                    if(key == novoVinculo && value == 'A'){
                        message = 'Você já possui uma vinculação de Responsável Legal de Pessoa Jurídica Ativa com este CNPJ!';
                    }
                    if(key == novoVinculo && value == 'S'){
                        message = 'Essa vinculação de Responsável Legal de Pessoa Jurídica está suspensa pela Administração do SEI.\n\nEntre em contato com a Administração do SEI para maiores informações.';
                    }
                });
                alert(message);
                document.location = '<?php echo $strUrlFechar ?>';
            }
        }
    }

    function consultarDadosReceita() {

        consultarVinculoExistenteCnpj();

        var qtdNuCPNJ = document.getElementById('txtNumeroCnpj').value.trim().length;
        var qtdTxtCaptcha = document.getElementById('txtCaptcha').value.trim().length;

        if (qtdNuCPNJ == 0) {
            alert('Antes, informe o CNPJ!');
            return false;
        }

        if (qtdTxtCaptcha == 0) {
            alert('Informe o código de confirmação.');
            return false;
        }

        $("#txtRazaoSocialWsdl").val('');

        $.ajax({
            dataType: 'xml',
            method: 'POST',
            url: '<?php echo $strLinkConsultaReceita ?>',
            beforeSend: function(request) {
                infraExibirAviso(false, 'Processando...');
                request.setRequestHeader("captcha", '<?php echo $strCaptcha ?>');
            },
            data: {
                'txtNumeroCnpj': $("#txtNumeroCnpj").val(),
                'txtCaptcha': $("#txtCaptcha").val()
            },
            error: function(dados) {
                console.log(dados);
                infraOcultarAviso();
            },
            success: function(data) {
                infraOcultarAviso();
                var nomeCidade = $('nomeCidade', data).text();
                var idCidade = $('selCidade', data).text();
                createOptionCidade(idCidade, nomeCidade);

                $("#txtRazaoSocialWsdl").val($('txtRazaoSocial', data).text());

                var success = $.trim($('success', data).text()).length;
                var txtSuccess = $.trim($('success', data).text());
                if (success > 0) {
                    var message = $.trim($('msg', data).text());
                    if (txtSuccess == 'false') {
                        var procuracao = $.trim($('procuracao', data).text());
                        var url = $.trim($('url', data).text());
                        console.log(url);
                        if (procuracao == 'true') {
                            infraAbrirJanela(window.atob(url),
                                'Impedimento de Substitução de Responsável Legal',
                                770,
                                480,
                                '', //options
                                false); //modal
                        } else if (message.length > 0) {
                            alert(message);
                        }
                    }
                    document.getElementById('hdnNumeroCnpj').value = document.getElementById('txtNumeroCnpj').value;
                    document.getElementById('frmCNPJ').submit();


                    return false;
                }

                var valorCmpAppend = '';
                $('dados-pj', data).children().each(function() {
                    console.log($(this)[0].tagName);
                    var idCampo = $(this)[0].localName;
                    var valor = $(this)[0].innerHTML;
                    var campo = $("#" + idCampo);
                    var noCampo = $(campo).attr('name');

                    if ($(campo).attr('type') == 'text' || $(campo).attr('type') == 'hidden') {
                        valorCmpAppend += valor + '±';
                        $(campo).val(valor);
                        if (noCampo == 'txtLogradouro') {
                            $('#txtLogradouroPadrao').val(valor);
                        }
                    } else if (typeof $(campo).attr('type') === 'undefined') {
                        //Tipo de Interessado não seleciona
                        if (noCampo != 'slTipoInteressado') {
                            $(campo).find('option[value="' + valor + '"]').prop('selected', true);
                        }

                        var cmpHidden = $("<input/>")
                            .attr({
                                'id': noCampo,
                                'name': noCampo,
                                'type': 'hidden'
                            })
                            .val(valor)
                        $(cmpHidden).insertAfter($(campo));

                        if (noCampo != 'slTipoInteressado' && valor != '') {
                            $(cmpHidden).addClass('disabled');
                            $(campo).attr('disabled', true)
                        }
                    } else {
                        if (noCampo == 'hdnIdVinculo') {
                            $(campo).val(valor);
                            if (valor != '') {
                                document.getElementById('isAlteracaoResponsavelLegal').value = '1';
                                document.getElementById('hdnIsAlteracao').value = '1';
                                document.getElementById('hdnIdContatoNovo').value = '1';
                                // document.getElementById('txtNumeroCpfResponsavel').value = '';
                                // document.getElementById('txtNomeResponsavelLegal').value = '';
                                // document.getElementById('txtMotivoAlteracaoRespLegal').value = '';
                            }
                        }
                    }

                })

                $('#hdnIsCnpjValidado').val(1);
                $("#stDeclaracao").show();
                $("#informacaoPJ").show();
                $("#informacaoPJ_BR").show();
                $("#fieldDocumentos").show();
                $("#fieldDocumentos_BR").show();
                if (document.getElementById('hdnIdVinculo').value == '') {
                    $("#fieldUsuarioProcuracao").show();
                    $("#fieldUsuarioProcuracao_BR").show();
                }
                $("#hdnValorCaptcha").val($("#txtCaptcha").val());
            }

        });

    }

    function fechar() {
        var isAlteracao = document.getElementById('isAlteracaoResponsavelLegal');

        if (isAlteracao && isAlteracao.value != 0) {
            if (confirm('As alteração não serão salvas, para conclui-lá é necessário Peticionar!! Deseja continuar?')) {
                document.location = '<?php echo $strUrlFechar ?>';
            }
        } else {
            document.location = '<?php echo $strUrlFechar ?>';
        }
    }

    function iniciarObjAjaxSelectHipoteseLegal() {
        objAjaxSelectHipoteseLegal = new infraAjaxMontarSelect('selHipoteseLegal', '<?php echo $strUrlAjaxMontarHipoteseLegal ?>');
        objAjaxSelectHipoteseLegal.processarResultado = function() {
            return 'nivelAcesso=' + RESTRITO;
        }
    }

    function iniciarTabelaDinamicaDocumento() {
        objTabelaDinamicaDocumento = new infraTabelaDinamica('tbDocumento', 'hdnTbDocumento', false, true);
        objTabelaDinamicaDocumento.gerarEfeitoTabela = true;
        objTabelaDinamicaDocumento.remover = function() {
            verificarTabelaVazia(2);
            return true;
        };

        objTabelaDinamicaDocumento.verificarTiposDocumentos = function(idSerieSel) {
            var qtd;
            var linha;
            qtd = document.getElementById('tbDocumento').rows.length;

            for (var i = 1; i < qtd; i++) {
                linha = document.getElementById('tbDocumento').rows[i];
                var valorLinha = $.trim(linha.cells[1].innerText);

                if (idSerieSel) {
                    if (valorLinha == idSerieSel) {
                        return true;
                    }
                }
            }
            return false;
        }
    }

    function exibirTipoConferencia() {
        var formatoDigitalizado = document.getElementById('rdoDigitalizado');
        var divTipoConferencia = document.getElementById('divTipoConferencia');
        var divTipoConferenciaBotao = document.getElementById('divTipoConferenciaBotao');
        var selTipoConferencia = document.getElementById('selTipoConferencia');
        divTipoConferencia.style.display = 'none';
        divTipoConferenciaBotao.style.display = 'block';
        selTipoConferencia.value = 'null';

        if (formatoDigitalizado.checked) {
            divTipoConferencia.style.display = 'block';
            divTipoConferenciaBotao.style.display = 'none';
        } else {
            divTipoConferencia.style.display = 'none';
            divTipoConferenciaBotao.style.display = 'block';
        }
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

    function iniciarObjUploadArquivo() {
        var tbDocumento = document.getElementById('tbDocumento');
        objUploadArquivo = new infraUpload('frmDocumentoAto', '<?php echo $strLinkUploadArquivo ?>');

        objUploadArquivo.finalizou = function(arr) {
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

            criarRegistroTabelaDocumento(arr);
            corrigirPosicaoAcaoExcluir();
            limparCampoDocumento();
        };

        objUploadArquivo.validar = function() {
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


    function verificarTabelaVazia(qtdLinha) {
        var tbDocumento = document.getElementById('tbDocumento');
        var ultimoRegistro = tbDocumento.rows.length == qtdLinha;
        if (ultimoRegistro) {
            tbDocumento.style.display = 'none';
        }
    }

    function limparCampoDocumento() {
        document.getElementById('fileArquivo').value = '';
        document.getElementById('selTipoDocumento').value = 'null';
        document.getElementById('txtComplementoTipoDocumento').value = '';

        if (!$('#selNivelAcesso').hasClass("unclear")) {
            document.getElementById('selNivelAcesso').value = '';
            document.getElementById('hdnNivelAcesso').value = '';
        }
        if (EXIBIR_HIPOTESE_LEGAL) {
            if (!$('#selHipoteseLegal').hasClass("unclear")) {
                document.getElementById('selHipoteseLegal').value = '';
                document.getElementById('hdnHipoteseLegal').value = '';
            }
        }
        //document.getElementById('divBlcHipoteseLegal').style.display = 'none';

        document.getElementById('rdoNatoDigital').checked = false;
        document.getElementById('rdoDigitalizado').checked = false;
        document.getElementById('selTipoConferencia').value = 'null';
        document.getElementById('divTipoConferencia').style.display = 'none';
        document.getElementById('divTipoConferenciaBotao').style.display = 'block';
    }

    function limparTabelaDocumento() {
        objTabelaDinamicaDocumento.limpar();
        verificarTabelaVazia(1);
    }


    function gerarIdDocumento() {
        var hdnIdDocumento = document.getElementById('hdnIdDocumento');
        hdnIdDocumento.value = parseInt(hdnIdDocumento.value) + 1;
        return hdnIdDocumento.value;
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
        console.log(labelrdoNatoDigital);
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

    function corrigirPosicaoAcaoExcluir() {
        var trs = document.getElementById('tbDocumento').getElementsByTagName('tr');
        for (var i = 1; i < trs.length; i++) {
            var tds = trs[i].getElementsByTagName('td');
            var td = tds[tds.length - 1];
            td.setAttribute('valign', 'center');
        }
    }

    function salvarValorHipoteseLegal(el) {
        if (EXIBIR_HIPOTESE_LEGAL) {
            var hdnHipoteseLegal = document.getElementById('hdnHipoteseLegal');
            hdnHipoteseLegal.value = el.value;
        }
    }

    function controlarEnterValidarProcesso(e) {
        var focus = returnElementFocus();
        if (infraGetCodigoTecla(e) == 13) {
            document.getElementById('btnValidar').onclick();
        }
    }

    function controlarEnterValidarUsuario(e) {
        var focus = returnElementFocus();
        if (infraGetCodigoTecla(e) == 13) {
            document.getElementById('btnValidarUsuario').onclick();
        }
    }

    function returnElementFocus() {
        var focused = document.activeElement;
        if (!focused || focused == document.body) {
            focused = null;
        } else if (document.querySelector) {
            focused = document.querySelector(":focus")
        }

        return focused;
    }

    function verificarHipoteseLegal() {
        if (document.getElementById('selNivelAcesso') != null) {
            var selNivelAcesso = document.getElementById('selNivelAcesso');

            if (selNivelAcesso.nodeName == 'SELECT') {
                selNivelAcesso.addEventListener('change', exibirHipoteseLegal);
            }
        }
    }

    function exibirFieldsetDocumentos(el) {
        var fieldDocumentos = document.getElementById('fieldDocumentos');
        var fieldDocumentos_BR = document.getElementById('fieldDocumentos_BR');
        var hdnNomeTipoResposta = document.getElementById('hdnNomeTipoResposta');

        fieldDocumentos.style.display = 'none';
        fieldDocumentos_BR.style.display = 'none';

        if (el.value != 'null') {

            fieldDocumentos.style.display = '';
            fieldDocumentos_BR.style.display = '';

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

    }

    function validarDocumentosObrigatorios() {
        var objJsonEncodDocs = JSON.parse(document.getElementById('hdnIdsDocObrigatorios').value);
        var docsMsg = '';
        var docsAusentes = false;

        for (var i = 0; i < objJsonEncodDocs.length; i++) {
            var str = objJsonEncodDocs[i];
            var ret = str.split('__');

            var valid = objTabelaDinamicaDocumento.verificarTiposDocumentos(ret[0]);

            if (!valid) {
                docsAusentes = true;
                docsMsg += '    * ' + ret[1];
            }
        }

        if (docsAusentes) {
            var msgCompleta = 'Os Tipos de Documentos listados abaixo são obrigatórios para efetivar a vinculação como Responsável Legal de Pessoa Jurídica:\n'
            msgCompleta += docsMsg;
            alert(msgCompleta);
            document.getElementById('fileArquivo').focus();
            return false;
        } else {
            return true;
        }
    }

    function carregarDependenciaCidades() {
        //Ajax para carregar os niveis de acesso após a escolha do tipo de processo
        objAjaxCidade = new infraAjaxMontarSelectDependente('slUf', 'selCidade', '<?= $strLinkAjaxCidade ?>');
        objAjaxCidade.prepararExecucao = function() {
            return infraAjaxMontarPostPadraoSelect('null', '', 'null') + '&idUf=' + document.getElementById('slUf').value;
        }

        objAjaxCidade.processarResultado = function() {
            //alert('terminou carregamento');
        }
    }

    function bloquearVirgula() {
        $('#txtNumeroEndereco').keydown(function(e) {
            if (e.which === 188 || e.which === 110) { // 188 é vírgula e 110 virgula do teclado numérico
                return false;
            }
        });
    }

    function validarTodosCamposContato() {
        var campos = document.getElementsByClassName("blocInformacaoPj");
        for (var i = 0; i < campos.length; i++) {
            if (campos[i].value == '' && campos[i].id != 'txtComplementoEndereco') {
                return false;
            }
        }

        return true;
    }

    function controlarMascaraCep(obj) {
        if (obj.value.length != 9) {
            obj.value = '';
        }
    }

    function peticionar() {

        var isVinculacao = document.getElementById('hdnIdVinculo').value == '' ? false : true;
        var isAlteracao = document.getElementById('hdnIsAlteracao').value == '1' || isVinculacao == true ? true : false;
        var isAlteracaoRespLql = document.getElementById('isAlteracaoResponsavelLegal').value == '1' ? true : false;
        var isTabelaVazia = document.getElementById('hdnTbDocumento').value.trim().length == 0 ? true : false;
        var isAlteracaoAtos    = document.getElementById('hdnStrTipo').value.trim() == 'Alteração' ? true : false;


        if (document.getElementById('txtNumeroCnpj').value.trim().length == 0) {
            alert('Antes, informe o CNPJ!');
            document.getElementById('txtNumeroCnpj').focus();
            return false;
        }

        if (document.getElementById('txtNumeroCnpj').value.trim().length < 18) {
            alert('CNPJ informado é inválido.');
            document.getElementById('txtNumeroCnpj').focus();
            return false;
        }

        if (stWebservice != 'false' && !isAlteracao) {
            if (document.getElementById('txtCaptcha').value.trim().length == 0) {
                alert('Informe o código de confirmação.');
                document.getElementById('txtCaptcha').focus();
                return false;
            }

            var captchaValidado = document.getElementById('hdnValorCaptcha').value;
            if (document.getElementById('txtCaptcha').value != captchaValidado) {
                alert('Código de confirmação inválido.');
                document.getElementById('txtCaptcha').focus();
                return false;
            }
        }

        var isCnpjValidado = document.getElementById('hdnIsCnpjValidado').value == '1';
        console.log(isCnpjValidado);
        var obj = document.getElementById('chkDeclaracao');
        if (!isCnpjValidado) {
            alert('É necessário validar o CNPJ para continuar!');
            if (obj) {
                document.getElementById('chkDeclaracao').checked = false;
            }
            return false;
        }

        var objDeclaracao = document.getElementById('chkDeclaracao');

        if (objDeclaracao && objDeclaracao.checked != true) {
            addRemoverDestaqueDeclaracao(true);
            alert('É necessário assinalar a declaração de responsabilidade pelo CNPJ e pela veracidade dos dados e documentos apresentados, destacada na tela.');
            document.getElementById('chkDeclaracao').focus();
            return false;
        }

        if (document.getElementById('slTipoInteressado').value == 0) {
            alert('Informe o Tipo de Interessado!');
            document.getElementById('slTipoInteressado').focus();
            return false;
        }

        if (stWebservice == 'false') {
            if (document.getElementById('txtRazaoSocial').value.length == 0) {
                alert('Informe a Razão Social!');
                document.getElementById('txtRazaoSocial').focus();
                return false;
            }

            if (document.getElementById('slUf').value == 'NULL') {
                alert('Informe a UF!');
                document.getElementById('slUf').focus();
                return false;
            }

            if (document.getElementById('selCidade').value.length == 0 || document.getElementById('selCidade').value == 'null') {
                alert('Informe a Cidade!');
                document.getElementById('selCidade').focus();
                return false;
            }

            if (document.getElementById('txtNumeroCEP').value.length == 0) {
                alert('Informe o CEP!');
                document.getElementById('txtNumeroCEP').focus();
                return false;
            }

            if (document.getElementById('txtLogradouro').value.length == 0) {
                alert('Informe o Endereço!');
                document.getElementById('txtLogradouro').focus();
                return false;
            }

            if (document.getElementById('txtBairro').value.length == 0) {
                alert('Informe o Bairro!');
                document.getElementById('txtBairro').focus();
                return false;
            }
        } else {

            if (!validarTodosCamposContato()) {
                alert('Dados Incompletos na Receita Federal!');
                return false;
            }
        }

        if (isTabelaVazia) {
            alert('Faltou adicionar os Documentos referentes aos Atos Constitutivos da Pessoa Jurídica.');
            document.getElementById('fileArquivo').focus();
            return false;
        }

        if (!validarDocumentosObrigatorios()) {
            return false;
        }

        if (isAlteracao && !isAlteracaoRespLql && isTabelaVazia) {
            alert('Para realizar o Peticionamento é necessário adicionar documentos ou Alterar o Responsável Legal!');
            return false;
        }

        if ($('#hdnIsWebServiceHabilitado').val(stWebserviceBol))


            var valorCmpAppend = '';
        document.querySelectorAll('.blocInformacaoPj').forEach(function(dados) {
            if (dados.nodeName == 'INPUT') {
                valorCmpAppend += dados.value + '±'
            }
            if (dados.nodeName == 'SELECT') {
                valorCmpAppend += dados.options[dados.selectedIndex].value + '±'
            }
        });

        document.getElementById('hdnInformacaoPj').value = valorCmpAppend;

        var url = '';

        if(isAlteracao && !isAlteracaoAtos){ // Substituicao do Responsavel Legal
            url = '<?php echo PaginaSEIExterna::getInstance()->formatarXHTML(SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=md_pet_usuario_ext_vinc_pj_concluir_alt')) ?>';
        }else if(isAlteracao && isAlteracaoAtos){  // Alteracao Atos Constitutivos
            url = '<?php echo PaginaSEIExterna::getInstance()->formatarXHTML(SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=md_pet_usuario_ext_vinc_pj_concluir_atos'))?>';
        } else {
            url = '<?php echo PaginaSEIExterna::getInstance()->formatarXHTML(SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=md_pet_usuario_ext_vinc_pj_concluir_cad')) ?>';
        }


        parent.infraAbrirJanelaModal(url,
            770,
            500,
            '', //options
            false); //modal
    }

    function addRemoverDestaqueDeclaracao(addDestaque) {
        var idHdn = addDestaque ? 'hdnTextoDeclaracaoDestaque' : 'hdnTextoDeclaracaoFormatado';
        var hdnTextoDeclaracao = document.getElementById(idHdn).value;
        document.getElementById('textoDeclaracao').innerHTML = hdnTextoDeclaracao;
    }

    function consultarUsuarioExternoValido() {

        //Verificar se o cnpj já esta sendo utilizado num vinculo
        if (document.getElementById('txtNumeroCpfProcurador').value.trim().length == 0) {
            alert('Informe o CPF completo');
            return false;
        }

        var valido = true;

        $.ajax({
            dataType: 'xml',
            method: 'POST',
            url: '<?php echo $strLinkConsultaUsuarioExternoValido ?>',
            data: {
                'cpf': $("#txtNumeroCpfProcurador").val()
            },
            error: function(dados) {
                console.log(dados);
            },
            success: function(data) {
                document.getElementById('txtNomeProcurador').value = '';
                document.getElementById('hdnIdUsuarioProcuracao').value = '';
                document.getElementById('btnAdicionarProcurador').style.display = 'none';

                if ($(data).find('no-usuario').text() != "" && $(data).find('nu-contato').text() != "") {
                    if ($(data).find('mensagem').text() == "pendente") {
                        alert('Usuário Externo com pendência de liberação de cadastro.');
                    } else {
                        document.getElementById('txtNomeProcurador').value = $(data).find('no-usuario').text();
                        document.getElementById('hdnIdUsuarioProcuracao').value = $(data).find('nu-contato').text();
                        document.getElementById('btnAdicionarProcurador').style.display = '';
                    }
                } else {
                    alert('Cadastro de Usuário Externo não localizado no sistema. Oriente o Usuário a realizar o Cadastro no Acesso Externo do SEI.');
                }
            }
        });

    }

    function validaCpf(objeto) {
        var erro = false;
        var valor = $.trim(objeto.value.replace(/\D/g, ""));

        if (valor.length == 10 && valor.indexOf('.') < 0) {
            objeto.value = '0' + objeto.value;
            valor = '0' + valor;
            if (!infraValidarCpf(valor)) {
                erro = true;
            }
        } else if (valor.length == 11) {
            if (!infraValidarCpf(valor)) {
                erro = true;
            }
        } else {
            erro = true;
        }

        if (erro) {
            alert('Informe o CPF do Usuário Externo completo ou válido.');
            objeto.value = '';
        }
    }
</script>
