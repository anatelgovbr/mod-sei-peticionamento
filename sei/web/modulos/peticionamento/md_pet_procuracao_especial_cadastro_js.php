<?php
$strUrlAjaxNumeroProcesso = SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=md_pet_processo_validar_numero');
$strLinkAjaxUsuarios = SessaoSEIExterna::getInstance()->assinarLink('controlador_ajax_externo.php?acao_ajax=md_pet_vinc_usu_ext_autocompletar');
$strLinkConsultaDadosUsuario = SessaoSEIExterna::getInstance()->assinarLink('controlador_ajax_externo.php?acao_ajax=md_pet_vinc_usu_ext_dados_usuario_externo_procuracao');
$strLinkConsultaResponsavelLegal = SessaoSEIExterna::getInstance()->assinarLink('controlador_ajax_externo.php?acao_ajax=md_pet_vinc_validar_representante');
$strLinkConsultaUsuarioExternoValido = SessaoSEIExterna::getInstance()->assinarLink('controlador_ajax_externo.php?acao_ajax=md_pet_vinc_consulta_usuext_valido_procuracao');
//Valida��o Usuario Externo
$strLinkAjaxValidarUsuarioExternoPendente = SessaoSEIExterna::getInstance()->assinarLink('controlador_ajax_externo.php?acao_ajax=md_pet_vinc_consulta_usuext_validacao');
//Valida��o de Existencia de Procura��o
$strLinkAjaxValidarExistenciaProc = SessaoSEIExterna::getInstance()->assinarLink('controlador_ajax_externo.php?acao_ajax=md_pet_vinc_usu_ext_dados_usuario_externo_validar_procuracao');
?>
<script type="text/javascript">

    function inicializar() {

        document.getElementById("sbmPeticionarInferior").style.display = "none";
        document.getElementById("btnCancelarInferior").style.display = "none";

        $("#selTpPoder").multipleSelect({
            filter: false,
            minimumCountSelected: 1,
            selectAll: false,
        });

        $("#lvbFisica").on('click', function () {
            if ($('#rbOutorgante1').prop('disabled')) {
                alert('Essa op��o esta desabilitada, Verifique junto ao �rg�o.');
            }
        });

        document.getElementById("procuracaoSimplesFieldSet").style.display = "none";
        document.getElementById("procuracaoEspecialTable").style.display = "none";
        document.getElementById("procuracaoEspecial").style.display = "none";
        document.getElementById("txtExplicativo").style.display = "none";


        document.getElementById("txtNumeroCpfProcurador").addEventListener("keyup", controlarEnterValidarProcesso, false);
        document.getElementById("txtNumeroCpfProcuradorSimples").addEventListener("keyup", controlarEnterValidarProcessoSimples, false);

        $('#infraCalendario').on('click', function (chave, item) {
            alert($(this));
        });

    }

    //Func�es Procura��o Simples

    function showPessoaOutorganteHidden() {
        document.getElementById('lvbPJProSimples').style.display = "none";
        document.getElementById('selPessoaJuridicaProcSimples').style.display = "none";
        document.getElementById('hdnRbOutorgante').value = "PF"
        document.getElementById('imgPj').style.display = "none";
    }

    function showPessoaOutorgante() {
        document.getElementById('lvbPJProSimples').style.display = "";
        if ($('#rbOutorgante1').prop('disabled')) {
            document.getElementById('lvbPJProSimples').style.paddingLeft = "52px";
        } else {
            document.getElementById('lvbPJProSimples').style.paddingLeft = "170px";
        }
        document.getElementById('selPessoaJuridicaProcSimples').style.display = "";
        document.getElementById('hdnRbOutorgante').value = "PJ"
        document.getElementById('imgPj').style.display = "";
    }

    function showData() {
        document.getElementById('lblDt').style.display = "";
        document.getElementById('txtDt').style.display = "";
        document.getElementById('imgDt').style.display = "";
        document.getElementById('hdnRbValidate').value = "Determinada";
    }

    function showDataNot() {
        document.getElementById('lblDt').style.display = "none";
        document.getElementById('txtDt').style.display = "none";
        document.getElementById('imgDt').style.display = "none";
        document.getElementById('hdnRbValidate').value = "Indeterminada";
    }

    function showTable1(val) {
        objTabelaDinamicaUsuarioProcessos.limpar();
        document.getElementById('tbProcessos').style.display = "none";
        document.getElementById('btnAdicionar').style.display = "none";
        document.getElementById('procDados').style.display = "none";
        document.getElementById('hdnRbAbrangencia').value = "Q";
        document.getElementById('hdnTbProcessos').value = '';
        document.getElementById('btnValidarProcesso').disabled = true;

    }

    function showTable2(val) {
        document.getElementById('procDados').style.display = "";
        document.getElementById('hdnRbAbrangencia').value = "E";
        document.getElementById('txtTipo').style.display = "none";
        document.getElementById('btnValidarProcesso').disabled = false;


    }

    //Recuperando o Processo
    function validarNumeroProcesso() {

        var numeroProcessoPreenchido = document.getElementById('txtNumeroProcesso').value != '';
        if (!numeroProcessoPreenchido) {
            alert('Informe o N�mero do Processo.');
            return false;
        }

        var paramsAjax = {
            txtNumeroProcesso: document.getElementById('txtNumeroProcesso').value,
            hdnUsuarioExterno: document.getElementById('hdnIdContExterno').value,
        };

        $.ajax({
            url: '<?=$strUrlAjaxNumeroProcesso?>',
            type: 'POST',
            async: false,
            dataType: 'XML',
            data: paramsAjax,
            success: function (r) {
                if ($(r).find('MensagemValidacao').text()) {
                    alert($(r).find('MensagemValidacao').text());
                    document.getElementById('txtTipo').value = "";
                } else {
                    document.getElementById('btnAdicionar').disabled = false;
                    document.getElementById('txtTipo').style.display = "";
                    document.getElementById('btnAdicionar').style.display = "";
                    document.getElementById('txtTipo').value = $(r).find('TipoProcedimento').text();
                    document.getElementById('hdnIdProcedimento').value = $(r).find('IdProcedimento').text();
                    document.getElementById('hdnTbNumeroProc').value = $(r).find('numeroProcesso').text();
                }


            },
            error: function (e) {
                console.error('Erro ao processar o XML do SEI: ' + e.responseText);
            }
        });


    }

    function adicionarProcesso() {
        document.getElementById('tbProcessos').style.display = "";
        //Limpando Campos
        objTabelaDinamicaUsuarioProcessos.adicionar([document.getElementById('hdnIdProcedimento').value, document.getElementById('hdnTbNumeroProc').value, document.getElementById('txtTipo').value]);

        document.getElementById('hdnIdProcedimento').value = '';
        document.getElementById('hdnTbNumeroProc').value = '';
        document.getElementById('txtNumeroProcesso').value = '';
        document.getElementById('txtTipo').value = '';
        document.getElementById('btnAdicionar').disabled = true;

    }

    //Func�es Procura��o Simples - FIM

    var objTabelaDinamicaUsuarioProcuracao = null;
    iniciarTabelaDinamicaUsuarioProcuracao();


    function iniciarTabelaDinamicaUsuarioProcuracao() {
        objTabelaDinamicaUsuarioProcuracao = new infraTabelaDinamica('tbUsuarioProcuracao', 'hdnTbUsuarioProcuracao', false, true);
        objTabelaDinamicaUsuarioProcuracao.gerarEfeitoTabela = true;
        objTabelaDinamicaUsuarioProcuracao.remover = function () {
            verificaTabelaProcuracao(2);
            return true;
        };
    }

    function iniciarTabelaDinamicaProcessos() {
        objTabelaDinamicaUsuarioProcessos = new infraTabelaDinamica('tbProcessos', 'hdnTbProcessos', false, true);
        objTabelaDinamicaUsuarioProcessos.gerarEfeitoTabela = true;
        objTabelaDinamicaUsuarioProcessos.remover = function () {
            verificaTabelaProcesso(2);
            return true;
        };
    }

    function criarRegistroTabelaProcuracao() {
        var dados = [];
        var nuCpf = document.getElementById('txtNumeroCpfProcurador').value;
        nuCpf = nuCpf.trim();

        var dsNome = document.getElementById('selUsuario').value;
        dsNome = dsNome.trim();

        if (nuCpf.length == 0) {
            alert('CPF do usu�rio externo � de preenchimento obrig�torio.');
            return false;
        }

        if (dsNome.length == 0) {
            alert('Nome do usu�rio externo � de preenchimento obrig�torio.');
            return false;
        }

        var hdnIdUsuarioProcuracao = document.getElementById('selUsuario').value;
        var hdnSelPessoaJuridica = document.getElementById('selPessoaJuridica').value;

        //Quando adicionado mais de um usuario ao mesmo tempo -- IdUsuario separado por +
        if (document.getElementById('hdnIdUsuario').value == '') {
            document.getElementById('hdnIdUsuario').value = hdnIdUsuarioProcuracao;
        } else {
            document.getElementById('hdnIdUsuario').value = hdnIdUsuarioProcuracao;
        }


        //Valida��o de Existencia de Procura��o Especial
        $.ajax({
            dataType: 'xml',
            method: 'POST',
            url: '<?php echo SessaoSEIExterna::getInstance()->assinarLink('controlador_ajax_externo.php?acao_ajax=md_pet_vinc_usu_ext_dados_usuario_externo'); ?>',
            data: {
                'hdnIdUsuarioProcuracao': document.getElementById('hdnIdUsuario').value,
                'hdnSelPessoaJuridica': document.getElementById('selPessoaJuridica').value
            },
            success: function (data) {
                if ($(data).find('sucesso').text() == 0) {
                    $.ajax({
                        dataType: 'xml',
                        method: 'POST',
                        url: '<?php echo $strLinkConsultaDadosUsuario?>',
                        data: {
                            'hdnIdUsuarioProcuracao': hdnIdUsuarioProcuracao,
                            'hdnSelPessoaJuridica': hdnSelPessoaJuridica
                        },
                        success: function (data) {

                            var valido = $(data).find('sucesso').text();

                            var dados = [];
                            $('dados', data).children().each(function () {

                                var valor = $(this).context.innerHTML;
                                dados.push(valor);


                                ;
                            });

                            //Inicio - Ajax Valida��o Usu�rio
                            //Validando para ver se o usu�rio externo n�o � pendente
                            $.ajax({
                                dataType: 'xml',
                                method: 'POST',
                                url: '<?php echo $strLinkAjaxValidarUsuarioExternoPendente?>',
                                data: {
                                    'idContato': document.getElementById('hdnIdUsuario').value,
                                    'hdnIdContExterno': document.getElementById('hdnIdUsuario').value
                                },
                                success: function (data) {

                                    if ($(data).find('existe').text() == 0) {
                                        alert("Usu�rio Externo com pend�ncia de libera��o de cadastro.");
                                        dados = [];
                                        return false;
                                    } else {

                                        objTabelaDinamicaUsuarioProcuracao.adicionar(dados);

                                        $("#tbUsuarioProcuracao").show();
                                        document.getElementById('txtNumeroCpfProcurador').value = '';
                                        infraSelectLimpar('selUsuario');

                                    }
                                }
                            });


                        },
                        error: function (e) {
                            console.error('Erro ao processar o XML do SEI: ' + e.responseText);
                        }
                    });
                } else {
                    alert("N�o � permitido adicionar este usu�rio, pois o mesmo j� possui uma Procura��o Especial para esta PJ.");
                    return false;
                }
            },
            error: function (e) {
                console.error('Erro ao processar o XML do SEI: ' + e.responseText);
            }
        });

    }

    function verificaTabelaProcuracao(qtdLinha) {
        var tbUsuarioProcuracao = document.getElementById('tbUsuarioProcuracao');
        var ultimoRegistro = tbUsuarioProcuracao.rows.length == qtdLinha;
        if (ultimoRegistro) {
            tbUsuarioProcuracao.style.display = 'none';
        }
    }

    function verificaTabelaProcesso(qtdLinha) {
        var tbProcesso = document.getElementById('tbProcessos');
        var ultimoRegistro = tbProcesso.rows.length == qtdLinha;
        if (ultimoRegistro) {
            tbProcesso.style.display = 'none';
        }
    }


    function peticionar() {

        console.log('Cheguei aqui');

        //Validando combo Tipo de PRocura��o
        if (document.getElementById('selTipoProcuracao').value == "") {
            alert("Escolha um Tipo de Procura��o.");
            return false;
        }
        //Pegando o Outorgante
        if (document.getElementById('selTipoProcuracao').value == "S" && document.getElementById('hdnRbOutorgante').value == "") {
            alert("Escolha um Outorgante.");
            return false;
        }

        var selPessoaJuridica = document.getElementById('selPessoaJuridica').value;
        var hdnIdContExterno = document.getElementById('hdnIdContExterno').value;
        if (document.getElementById('selTipoProcuracao').value == "S" && document.getElementById('hdnRbOutorgante').value == "PJ") {
            validarCampos();
        } else if (document.getElementById('selTipoProcuracao').value == "S" && document.getElementById('hdnRbOutorgante').value == "PF") {
            validarCampos();
        } else if (document.getElementById('selTipoProcuracao').value == "E") {
            validarCampos();
        }

        //var usuarioValido = validarResponsavelLegal(selPessoaJuridica,hdnIdContExterno);
    }

    function validarResponsavelLegal(selPessoaJuridica, hdnIdContExterno) {
        var valor = '';
        $.ajax({
            dataType: 'xml',
            method: 'POST',
            url: '<?php echo $strLinkConsultaResponsavelLegal?>',
            data: {
                'selPessoaJuridica': selPessoaJuridica,
                'hdnIdContExterno': hdnIdContExterno
            },
            success: function (data) {
                $('dados', data).children().each(function () {
                    var valor = $(this).context.innerHTML;
                    if (valor > 0)
                        validarCampos();
                    else
                        alert('Usuario n�o � um Respons�vel Legal.');
                })
            }
        });

    }


    function validarCampos() {
        $tpProc = document.getElementById('selTipoProcuracao').value;

        //Validando combo Tipo de PRocura��o
        if (document.getElementById('selTipoProcuracao').value == "") {
            alert("Escolha um Tipo de Procura��o.");
            return false;
        }
        if ($tpProc == "S") {
            //Validando CPF
            if ($tpProc == "S" && document.getElementById('txtNumeroCpfProcuradorSimples').value == "") {
                alert("Informe o CPF completo");
                return false;
            }


            //Validando Combo Usu�rio Externo

            if (document.getElementById('selUsuarioSimples').value == "") {
                alert("Selecione o Nome do Usu�rio Externo.");
                return false;
            }


            //Validando Combo Tipo de Poder
            if (document.getElementById('selTpPoder').value == "") {
                alert("Selecione um Tipo de Poder.");
                return false;
            } else {
                var tipoPoderes = [];
                //Recuperando os Tipo de Poderes Selecionados
                var selTpPoderes = document.getElementById('listaPoderes');
                var poderes = selTpPoderes.getElementsByTagName('li');

                for (var i = 0; i < poderes.length; i++) {
                    if (poderes[i].className == 'selected') {
                        tipoPoderes.push(poderes[i].getElementsByTagName('label')[0].getElementsByTagName('input')[0].value);

                    }
                }

                document.getElementById('hdnTpPoderes').value = tipoPoderes.join('-');

            }
            //Validando Radio Validade
            if (document.getElementById('hdnRbValidate').value == "") {
                alert("Selecione um tipo de Validade.");
                return false;
            }
            if (document.getElementById('hdnRbValidate').value == "Determinada") {
                if (document.getElementById('txtDt').value == "") {
                    alert("Informe a Data Limite.");
                    return false;
                }


            }
            //Verifica se a data � posteriar a data atua
            if (document.getElementById('txtDt').value != "") {
                if (!infraValidarData(document.getElementById('txtDt').value, false)) {
                    alert("Formato de data inv�lido.");
                    return false;
                }

                var data = document.getElementById('txtDt').value;
                if (infraCompararDatas(document.getElementById('hdnDtAtual').value, data) < 0) {
                    alert("A data informada no campo Validade n�o � permitida. Informe uma data posterior a data atual.");
                    return false;
                }
            }


            if (document.getElementById('hdnRbAbrangencia').value == "") {
                alert("Informe um tipo de Abrang�ncia.");
                return false;
            }

            //Caso a Abrang�ncia seja Especifica, validar Table
            if (document.getElementById('hdnRbAbrangencia').value == "E") {
                var tbUsuarioProcesso = document.getElementById('tbProcessos');
                var qtdLinhas = tbUsuarioProcesso.rows.length;

                if (qtdLinhas == 1) {
                    alert('Adicione ao menos um Processo ao gerar a Procura��o Eletr�nica sob a Abrang�ncia: Processos Espec�ficos.');
                    return false;
                }
            }
        }

        if ($tpProc == "S") {
            //Procura��o Simples
            //Valida��o de Modal
            var dados = {
                idOutorgado: document.getElementById('hdnIdContExterno').value,
                tipoProc: document.getElementById('selTipoProcuracao').value
            };

            if (document.getElementById('hdnRbOutorgante').value == "PF") {
                if (document.getElementById('hdnRbValidate').value == "Indeterminada") {
                    validadeValor = "Indeterminada";
                } else {
                    validadeValor = document.getElementById('txtDt').value;
                }

                var dados = {
                    idOutorgante: document.getElementById('hdnIdContExterno').value,
                    idOutorgado: document.getElementById('hdnIdUsuario').value,
                    tipoProc: document.getElementById('selTipoProcuracao').value,
                    poderes: document.getElementById('hdnTpPoderes').value,
                    validade: validadeValor,
                    processos: document.getElementById('hdnTbProcessos').value
                };
            } else if (document.getElementById('hdnRbOutorgante').value == "PJ") {

                if (document.getElementById('hdnRbValidate').value == "Indeterminada") {
                    validadeValor = "Indeterminada";
                } else {
                    validadeValor = document.getElementById('txtDt').value;
                }
                var dados = {
                    idOutorgante: document.getElementById('selPessoaJuridicaProcSimples').value,
                    idOutorgado: document.getElementById('hdnIdUsuario').value,
                    tipoProc: document.getElementById('selTipoProcuracao').value,
                    poderes: document.getElementById('hdnTpPoderes').value,
                    validade: validadeValor,
                    processos: document.getElementById('hdnTbProcessos').value
                };
            }

            //Fim Valida��o Modal

            //Verificando Pendencia de Usu�rio Externo
            $.ajax({
                dataType: 'xml',
                method: 'POST',
                url: '<?php echo $strLinkAjaxValidarUsuarioExternoPendente?>',
                data: {
                    'idContato': document.getElementById('hdnIdUsuario').value,
                    'hdnIdContExterno': document.getElementById('hdnIdUsuario').value
                },
                success: function (data) {
                    if ($(data).find('existe').text() == 0) {
                        alert("Usu�rio Externo com pend�ncia de libera��o de cadastro.");
                    } else {
                        //Abre Modal
                        $.ajax({
                            dataType: 'xml',
                            method: 'POST',
                            url: '<?php echo $strLinkAjaxValidarExistenciaProc?>',
                            data: dados,
                            success: function (data) {
                                $.each($(data).find('item'), function (i, j) {
                                    var valor = $(this).context.innerHTML;
                                    if ($(j).attr("id") == 0) {


                                        //Modal para Assinatura
                                        infraAbrirJanela('<?php echo PaginaSEIExterna::getInstance()->formatarXHTML(SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=peticionamento_usuario_externo_vinc_pe'))?>',
                                            'concluirPeticionamento',
                                            770,
                                            480,
                                            '', //options
                                            false); //modal

                                    } else {

                                        //Modal para Existencia de Procura��o
                                        //Modal para Valida��o
                                        infraAbrirJanela($(j).attr("id"),
                                            'AvisoDeExistencia',
                                            800,
                                            490,
                                            '', //options
                                            false); //modal


                                    }
                                })
                            }
                        });

                        //Abre Modal - Fim
                    }
                }
            });


            //Fim Procura��o Especial Modal
        }


        //Caso seja procura��o especial
        if ($tpProc == "E") {
            var tbUsuarioProcuracao = document.getElementById('tbUsuarioProcuracao');
            var qtdLinhas = tbUsuarioProcuracao.rows.length;

            if (qtdLinhas == 1) {
                alert('Usu�rio Externo n�o foi selecionado.');
                return false;
            }

            //Valida��o Modal - Procura��o Especial
            //Valida��o de Modal
            var dados = {
                idOutorgado: document.getElementById('hdnIdContExterno').value,
                tipoProc: document.getElementById('selTipoProcuracao').value,
                idOutorgante: document.getElementById('selPessoaJuridica').value
            };
            $.ajax({
                dataType: 'xml',
                method: 'POST',
                url: '<?php echo $strLinkAjaxValidarExistenciaProc?>',
                data: dados,
                success: function (data) {
                    $.each($(data).find('item'), function (i, j) {
                        var valor = $(this).context.innerHTML;
                        if ($(j).attr("id") == 0) {

                            //Modal para Assinatura
                            infraAbrirJanela('<?php echo PaginaSEIExterna::getInstance()->formatarXHTML(SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=peticionamento_usuario_externo_vinc_pe'))?>',
                                'concluirPeticionamento',
                                770,
                                480,
                                '', //options
                                false); //modal

                        } else {

                            //Modal para Existencia de Procura��o
                            //Modal para Valida��o
                            infraAbrirJanela($(j).attr("id"),
                                'AvisoDeExistencia',
                                770,
                                480,
                                '', //options
                                false); //modal


                        }
                    })
                }
            });

            //Fim Procura��o Especial Modal

        }
    }

    var textoProcEsp = '<div class="espacamentoConteudo">';
    textoProcEsp += 'A Procura��o Eletr�nica Especial concede, no �mbito do(a) <?=$siglaOrgao?>, ';
    textoProcEsp += 'ao Usu�rio Externo poderes para:';
    textoProcEsp += '</div>';

    textoProcEsp += '<div class="margemConteudo">';
    textoProcEsp += '1. Gerenciar o cadastro da Pessoa Jur�dica Outorgante ';
    textoProcEsp += '(exceto alterar o Respons�vel Legal ou outros Procuradores Especiais).';
    textoProcEsp += '</div>';

    textoProcEsp += '<div class="margemConteudo">';
    textoProcEsp += '2. Receber, Cumprir e Responder Intima��es Eletr�nicas e realizar Peticionamento Eletr�nico ';
    textoProcEsp += 'em nome da Pessoa Jur�dica Outorgante.';
    textoProcEsp += '</div>';

    textoProcEsp += '<div class="margemConteudo">';
    textoProcEsp += '3. Representar a Pessoa Jur�dica Outorgante com todos os poderes previstos no sistema, ';
    textoProcEsp += 'inclusive no substabelecimento ao emitir Procura��es Eletr�nicas Simples, habilitando-o a praticar todos os atos processuais, inclusive confessar, reconhecer a proced�ncia do pedido, transigir, desistir, renunciar, receber, dar quita��o e firmar compromisso.';
    textoProcEsp += '</div>';

    textoProcEsp += '<div class="margemConteudo">';
    textoProcEsp += '4. Substabelecer os poderes outorgados pela presente Procura��o, ao conceder Procura��es Eletr�nicas Simples ';
    textoProcEsp += 'a outros Usu�rios Externos, em �mbito geral ou para processos espec�ficos, conforme poderes ';
    textoProcEsp += 'definidos, para representa��o da Pessoa Jur�dica Outorgante.';
    textoProcEsp += '</div>';

    textoProcEsp += '<div class="espacamentoConteudo">';
    textoProcEsp += 'Ao conceder a Procura��o Eletr�nica Especial, voc� se declara ciente de que:';
    textoProcEsp += '</div>';

    textoProcEsp += '<div class="margemConteudo">';
    textoProcEsp += '&bullet; ';
    textoProcEsp += 'Poder�, a qualquer tempo, por meio do SEI-<?=$siglaOrgao?>, ';
    textoProcEsp += 'revogar a Procura��o Eletr�nica Especial;';
    textoProcEsp += '</div>';

    textoProcEsp += '<div class="margemConteudo">';
    textoProcEsp += '&bullet; ';
    textoProcEsp += 'O Outorgado poder�, a qualquer tempo, por meio do SEI-<?=$siglaOrgao?>, ';
    textoProcEsp += 'renunciar a Procura��o Eletr�nica Especial;';
    textoProcEsp += '</div>';

    textoProcEsp += '<div class="margemConteudo">';
    textoProcEsp += '&bullet; ';
    textoProcEsp += 'A validade desta Procura��o est� circunscrita ao(�) <?=$siglaOrgao?> ';
    textoProcEsp += 'e por tempo indeterminado, salvo se revogada ou renunciada, de modo que ela n�o pode ser usada para convalidar quaisquer atos praticados pelo Outorgado em representa��o da Pessoa Jur�dica no �mbito de outros �rg�os ou entidades.';
    textoProcEsp += '</div>';

    var igualHtml = '<div class="espacamentoConteudo">';
    igualHtml += '<label class="label-bold">Aten��o:</label> ';
    igualHtml += 'Para poder receber uma Procura��o Eletr�nica o ';
    igualHtml += 'Usu�rio Externo j� deve possuir cadastro no SEI-<?=$siglaOrgao?> liberado.';
    igualHtml += '</div>';

    //Procura��o Simples
    var textoProcSimp = '<div class="espacamentoConteudo">';
    textoProcSimp += 'A Procura��o Eletr�nica Simples concede, no �mbito do(a) <?=$siglaOrgao?>, ao Usu�rio Externo, os Poderes expressamente estabelecidos e em conformidade com a Validade e Abrang�ncia definidos.';
    textoProcSimp += '</div>';

    textoProcSimp += '<div class="espacamentoConteudo">';
    textoProcSimp += 'Ao conceder a Procura��o Eletr�nica Simples, voc� se declara ciente de que:';
    textoProcSimp += '</div>';

    textoProcSimp += '<div class="margemConteudo">';
    textoProcSimp += '&bullet; '
    textoProcSimp += 'Poder�, a qualquer tempo, por meio do SEI-<?=$siglaOrgao?>, revogar a Procura��o Eletr�nica Simples;';
    textoProcSimp += '</div>';

    textoProcSimp += '<div class="margemConteudo">';
    textoProcSimp += '&bullet; '
    textoProcSimp += 'O Outorgado poder�, a qualquer tempo, por meio do SEI-<?=$siglaOrgao?>, renunciar a Procura��o Eletr�nica Simples;';
    textoProcSimp += '</div>';

    textoProcSimp += '<div class="margemConteudo">';
    textoProcSimp += '&bullet; '
    textoProcSimp += 'A validade desta Procura��o est� circunscrita ao(�) <?=$siglaOrgao?> e em conformidade com os Poderes, Validade e Abrang�ncia definidos, salvo se revogada ou renunciada, de modo que ela n�o pode ser usada para convalidar quaisquer atos praticados pelo Outorgado em representa��o do Outorgante no �mbito de outros �rg�os ou entidades.';
    textoProcSimp += '</div>';

    var igualHtml = '<div class="espacamentoConteudo">';
    igualHtml += '<label class="label-bold">Aten��o:</label> ';
    igualHtml += 'Para poder receber uma Procura��o Eletr�nica o ';
    igualHtml += 'Usu�rio Externo j� deve possuir cadastro no SEI-<?=$siglaOrgao?> liberado.';
    igualHtml += '</div>';
    //Fim Procura��o Simples

    var textoProcElt = '';
    var textoProcSub = '';

    var html = textoProcEsp + igualHtml;
    document.getElementById("txtExplicativo").innerHTML = html;

    function pegaInfo(el) {
        if (el.value == "") {
            document.getElementById("procuracaoEspecial").style.display = "none";
            document.getElementById("procuracaoSimplesFieldSet").style.display = "none";
            document.getElementById("txtExplicativo").style.display = "none";
            document.getElementById("procuracaoEspecialTable").style.display = "none";
            document.getElementById("hiddenOutorgante").style.display = "none";
            document.getElementById("procuracaoEspecial").style.display = "none";
            document.getElementById("procuracaoEspecialTable").style.display = "none";
            document.getElementById("procuracaoSimplesFieldSet").style.display = "none";
            document.getElementById("txtExplicativo").style.display = "none";
            document.getElementById("procuracaoEspecial").style.display = "none";
            document.getElementById("hiddenOutorgante").style.display = "none";

            document.getElementById("sbmPeticionarInferior").style.display = "none";
            document.getElementById("btnCancelarInferior").style.display = "none";

            //document.getElementById("selPessoaJuridica").style.display = "none";
            //document.getElementById("lvbPJProSimples").style.display = "none";
        }
        if (el.value == '<?php echo MdPetVincRepresentantRN::$PE_PROCURADOR_ESPECIAL?>') {
            //Caso a Intima��o seja Especial, apagar os campos da Simples caso estejam preenchidos
            document.getElementById('txtNumeroCpfProcuradorSimples').value = "";
            infraSelectLimpar('selUsuarioSimples');
            document.getElementById('rbValidade').checked = false;
            document.getElementById('rbValidade2').checked = false;
            document.getElementById('lblDt').style.display = "none";
            document.getElementById('txtDt').style.display = "none";
            document.getElementById('imgDt').style.display = "none";
            document.getElementById('hiddenOutorgante').style.display = "none";
            document.getElementById('procuracaoSimplesFieldSet').style.display = "none";
            document.getElementById('rbAbrangencia').checked = false;
            document.getElementById('rbAbrangencia1').checked = false;
            //Limpando Hiddens
            document.getElementById('hdnTbProcessos').value = "";
            document.getElementById('hdnTbNumeroProc').value = "";
            document.getElementById('hdnRbValidate').value = "";
            document.getElementById('hdnCpf').value = "";
            document.getElementById('hdnExiteProc').value = "";
            document.getElementById('hdnRbAbrangencia').value = "";

            //Desbloqueando bot�es da tela de procura��o especial

            document.getElementById('btnValidarEspecial').disabled = false;
            document.getElementById('btnAdicionarProcurador').disabled = false;

            //Bloqueando bot�es da tela de procura��o simples
            document.getElementById('btnValidarSimples').disabled = true;
            document.getElementById('btnValidarProcesso').disabled = true;

            //Escondendo dados dos processos ----- LIMPAR TABELA DE PROCESSOS
            document.getElementById('tbProcessos').style.display = "none";
            document.getElementById('btnAdicionar').style.display = "none";
            document.getElementById('procDados').style.display = "none";
            document.getElementById('hdnRbAbrangencia').value = "Q";
            document.getElementById('hdnTbProcessos').value = '';
            document.getElementById('btnValidarProcesso').disabled = true;
            document.getElementById('txtNumeroProcesso').value = "";
            objTabelaDinamicaUsuarioProcessos = new infraTabelaDinamica('tbProcessos', 'hdnTbProcessos', false, true);
            objTabelaDinamicaUsuarioProcessos.limpar();

            //Mostrando os Bot�es Peticionar e Cancelar Inferior
            document.getElementById("sbmPeticionarInferior").style.display = "";
            document.getElementById("btnCancelarInferior").style.display = "";


            html = textoProcEsp;
            if (document.getElementById('hdnBloqueioRadio').value == "true") {
                document.getElementById("procuracaoSimplesFieldSet").style.display = "none";
                document.getElementById("hiddenOutorgante").style.display = "none";
                document.getElementById("sbmPeticionar").style.display = "none";
                document.getElementById("sbmPeticionarInferior").style.display = "none";
                document.getElementById("txtExplicativo").style.display = "none";
                //document.getElementById("lblOutorgante").style.display = "none";

            } else {
                document.getElementById("procuracaoEspecial").style.display = "";
                document.getElementById("txtExplicativo").style.display = "";
                document.getElementById("procuracaoEspecialTable").style.display = "";
                document.getElementById("txtExplicativo").style.display = "";
                document.getElementById("sbmPeticionar").style.display = "";
                document.getElementById("sbmPeticionarInferior").style.display = "";
                document.getElementById("txtExplicativo").style.display = "";
                document.getElementById('rbOutorgante1').checked = false;
                document.getElementById('rbOutorgante2').checked = false;
            }


        }

        if (el.value == '<?php echo MdPetVincRepresentantRN::$PE_PROCURADOR?>') {
            html = textoProcElt;
        }

        //Procura��o Simples
        if (el.value == '<?php echo MdPetVincRepresentantRN::$PE_PROCURADOR_SIMPLES?>') {

            //Limpando campos caso o Tipo de PRocura��o seja Especial
            objTabelaDinamicaUsuarioProcuracao.limpar();
            document.getElementById('tbUsuarioProcuracao').style.display = "none";
            document.getElementById('hdnIdUsuario').value = "";
            document.getElementById('hdnIdUsuarioProcuracao').value = "";
            $mostrar = true;
            if (document.getElementById('hdnBloqueioRadio').value == "true") {
                document.getElementById("lvbJuridica").style.display = "none";
                document.getElementById("rbOutorgante2").style.display = "none";
                document.getElementById('lvbPJProSimples').style.paddingLeft = "52px";
                document.getElementById("ajudaPJ").style.display = "none";
                document.getElementById("rbOutorgante2").disabled = true;
                document.getElementById('hdnRbOutorgante').value = "PF"
                if (!$('#rbOutorgante1').prop('disabled')) {
                    document.getElementById("rbOutorgante1").checked;
                } else {
                    $mostrar = false;
                }
            } else {
                document.getElementById('rbOutorgante1').checked = false;
                document.getElementById('rbOutorgante2').checked = false;
                document.getElementById("lvbJuridica").style.display = "";
                document.getElementById("rbOutorgante2").style.display = "";
                document.getElementById("ajudaPJ").style.display = "";
            }

            html = textoProcSimp;
            document.getElementById("procuracaoEspecial").style.display = "none";
            document.getElementById("procuracaoEspecialTable").style.display = "none";
            document.getElementById("procuracaoEspecial").style.display = "none";
            document.getElementById("selPessoaJuridicaProcSimples").style.display = "none";
            document.getElementById("lvbPJProSimples").style.display = "none";
            document.getElementById("imgPj").style.display = "none";
            if ($mostrar) {
                document.getElementById("procuracaoSimplesFieldSet").style.display = "";
                document.getElementById('lvbPJProSimples').style.paddingLeft = "170px";
                document.getElementById("txtExplicativo").style.display = "";
                document.getElementById("hiddenOutorgante").style.display = "";


                //Mostrando os Bot�es Peticionar e Cancelar Inferior
                document.getElementById("sbmPeticionarInferior").style.display = "";
                document.getElementById("btnCancelarInferior").style.display = "";

                //Bloqueando bot�es da tela de procura��o especial

                document.getElementById('btnValidarEspecial').disabled = true;
                document.getElementById('btnAdicionarProcurador').disabled = true;

                //Desbloqueando bot�es da tela de procura��o simples
                document.getElementById('btnValidarSimples').disabled = false;
                document.getElementById('txtNumeroCpfProcurador').value = "";
                infraSelectLimpar('selUsuario');


                iniciarTabelaDinamicaProcessos();
            }

        }

        html += igualHtml;
        document.getElementById("txtExplicativo").innerHTML = html;
    }

    function infraMascaraCPF(objeto) {
        var novoValor = maskCPF($.trim(objeto.value));
        objeto.value = novoValor;
    }

    function validaCpf(cpf) {

        var erro = false;
        var msg = '';
        var valor = $.trim(cpf.replace(/\D/g, ""));
        var cpfUsuariologado = document.getElementById('hdnCpfContExterno').value.replace(/\D/g, "");

        if (valor.length == 11) {
            if (!infraValidarCpf(valor)) {
                erro = true;
                msg = 'Informe o CPF do usu�rio externo completo ou v�lido para realizar a pesquisa.';
            }
        } else {
            erro = true;
        }

        //Verificando Usu�rio Logado
        if (valor == cpfUsuariologado) {
            erro = true;
            msg = "N�o � permitida a gera��o de Procura��o Eletr�nica para voc� mesmo. \n Informe o CPF da Pessoa F�sica que ir� te representar.";
        }

        if (erro) {
            alert(msg);
            return false;
        }
    }

    function maskCPF(cpf) {
        cpf = cpf.replace(/\D/g, "");
        cpf = cpf.replace(/(\d{3})(\d)/, "$1.$2");
        cpf = cpf.replace(/(\d{3})(\d)/, "$1.$2");
        cpf = cpf.replace(/(\d{3})(\d{1,2})$/, "$1-$2");

        return cpf;
    }

    function alterarHidden(val) {
        document.getElementById('hdnIdUsuarioProcuracao').value = val.value;
        document.getElementById('hdnIdUsuario').value = val.value;
    }

    function consultarUsuarioExternoValido() {
        infraSelectLimpar('selUsuario');

        var valido = validaCpf(document.getElementById('txtNumeroCpfProcurador').value);

        if (valido == false) {
            return false;
        }

        //Verificar se o cnpj j� esta sendo utilizado num vinculo
        if (document.getElementById('txtNumeroCpfProcurador').value.trim().length == 0) {
            alert('Informe o CPF completo');
            return false;
        }

        var valido = true;

        $.ajax({
            dataType: 'xml',
            method: 'POST',
            url: '<?php echo $strLinkConsultaUsuarioExternoValido?>',
            data: {
                'cpf': $("#txtNumeroCpfProcurador").val()
            },
            success: function (data) {
                var resultado = $(data).find('resultado');
                //Caso retorne somente um item, desativar a combo
                //Caso retorne nada, o cpf n�o existe
                if (resultado[0].childElementCount == 0) {
                    alert('Cadastro de Usu�rio Externo n�o localizado no sistema. Oriente o Usu�rio a realizar o Cadastro no Acesso Externo do SEI.');
                    return false;
                }

                //document.getElementById('txtNomeProcurador').value = '';
                document.getElementById('hdnIdUsuarioProcuracao').value = '';
                document.getElementById('btnAdicionarProcurador').style.display = 'none';
                var ids = [];

                //Foreach
                try {
                    var selectMultiple = document.getElementById('selUsuario');
                    var msg = '';

                    for (i = 0; i < resultado[0].childNodes.length; i++) {
                        var contato = $(resultado[0].childNodes[i]);

                        if (contato.attr('sucesso') == 1) {
                            var opt = document.createElement('option');
                            opt.value = contato.attr("id");
                            opt.innerHTML = contato.attr("descricao");
                            selectMultiple.appendChild(opt);
                            ids.push(contato.attr("id"));
                        } else {
                            if (ids.length === 0) {
                                msg = contato.attr('mensagem');
                            }
                        }
                    }

                    if (ids.length == 1) {
                        document.getElementById("selUsuario").disabled = true;
                    } else {
                        document.getElementById("selUsuario").disabled = false;
                    }

                    if (msg !== '') {
                        alert(msg);
                    }
                } catch (err) {

                }
                document.getElementById('hdnIdUsuarioProcuracao').value = ids[0];
                document.getElementById('btnAdicionarProcurador').style.display = '';
            },
            error: function (dados) {
                console.log(dados);
            }
        });

    }

    function consultarUsuarioExternoValidoSimples() {
        infraSelectLimpar('selUsuarioSimples');

        var valido = validaCpf(document.getElementById('txtNumeroCpfProcuradorSimples').value);

        if (valido == false) {
            return false;
        }

        //Verificar se o cnpj j� esta sendo utilizado num vinculo
        if (document.getElementById('txtNumeroCpfProcuradorSimples').value.trim().length == 0) {
            alert('Informe o CPF completo');
            return false;
        }

        valido = true;

        $.ajax({
            dataType: 'xml',
            method: 'POST',
            url: '<?php echo $strLinkConsultaUsuarioExternoValido?>',
            data: {
                'cpf': $("#txtNumeroCpfProcuradorSimples").val()
            },
            success: function (data) {
                var resultado = $(data).find('resultado');
                //Caso retorne somente um item, desativar a combo
                //Caso retorne nada, o cpf n�o existe
                if (resultado[0].childElementCount == 0) {
                    alert('Cadastro de Usu�rio Externo n�o localizado no sistema. Oriente o Usu�rio a realizar o Cadastro no Acesso Externo do SEI.');
                    return false;
                }
                //document.getElementById('txtNomeProcurador').value = '';
                document.getElementById('hdnIdUsuarioProcuracao').value = '';
                document.getElementById('btnAdicionarProcurador').style.display = 'none';
                var ids = [];

                //Foreach
                try {
                    var selectMultiple = document.getElementById('selUsuarioSimples');
                    var msg = '';

                    for (i = 0; i < resultado[0].childNodes.length; i++) {
                        var contato = $(resultado[0].childNodes[i]);

                        if (contato.attr('sucesso') == 1) {
                            var opt = document.createElement('option');
                            opt.value = contato.attr("id");
                            opt.innerHTML = contato.attr("descricao");
                            selectMultiple.appendChild(opt);
                            ids.push(contato.attr("id"));
                        } else {
                            if (ids.length === 0) {
                                msg = contato.attr('mensagem');
                            }
                        }
                    }

                    if (ids.length == 1) {
                        document.getElementById("selUsuarioSimples").disabled = true;
                    } else {
                        document.getElementById("selUsuarioSimples").disabled = false;
                    }

                    if (msg !== '') {
                        alert(msg);
                    }
                } catch (err) {

                }
                document.getElementById('hdnIdUsuarioProcuracao').value = ids[0];
                document.getElementById('hdnIdUsuario').value = ids[0];
                document.getElementById('hdnCpf').value = $("#txtNumeroCpfProcuradorSimples").val();
                document.getElementById('btnAdicionarProcurador').style.display = '';
            },
            error: function (dados) {
                console.log(dados);
            }
        });

    }

    function controlarEnterValidarProcesso(e) {
        var focus = returnElementFocus();
        if (infraGetCodigoTecla(e) == 13) {
            document.getElementById('btnValidarEspecial').onclick();
        }
    }

    function controlarEnterValidarProcessoSimples(e) {
        var focus = returnElementFocus();
        if (infraGetCodigoTecla(e) == 13) {
            document.getElementById('btnValidarSimples').onclick();
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
</script>