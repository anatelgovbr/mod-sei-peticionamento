<?php
$strUrlAjaxNumeroProcesso = SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=md_pet_processo_validar_numero');
$strLinkAjaxUsuarios = SessaoSEIExterna::getInstance()->assinarLink('controlador_ajax_externo.php?acao_ajax=md_pet_vinc_usu_ext_autocompletar');
$strLinkConsultaDadosUsuario = SessaoSEIExterna::getInstance()->assinarLink('controlador_ajax_externo.php?acao_ajax=md_pet_vinc_usu_ext_dados_usuario_externo_procuracao');
$strLinkConsultaResponsavelLegal = SessaoSEIExterna::getInstance()->assinarLink('controlador_ajax_externo.php?acao_ajax=md_pet_vinc_validar_representante');
$strLinkConsultaUsuarioExternoValido = SessaoSEIExterna::getInstance()->assinarLink('controlador_ajax_externo.php?acao_ajax=md_pet_vinc_consulta_usuext_valido_procuracao');
//Validação Usuario Externo
$strLinkAjaxValidarUsuarioExternoPendente = SessaoSEIExterna::getInstance()->assinarLink('controlador_ajax_externo.php?acao_ajax=md_pet_vinc_consulta_usuext_validacao');
//Validação de Existencia de Procuração
$strLinkAjaxValidarExistenciaProc = SessaoSEIExterna::getInstance()->assinarLink('controlador_ajax_externo.php?acao_ajax=md_pet_vinc_usu_ext_dados_usuario_externo_validar_procuracao');
?>
<script type="text/javascript">

    function inicializar() {

        document.getElementById("sbmPeticionarInferior").style.display = "none";
        document.getElementById("btnCancelarInferior").style.display = "none";

        $("#selTpPoder").multipleSelect({
            placeholder: 'Selecione',
            filter: true,
            minimumCountSelected: 1,
            selectAll: false,
        });

        $("#lvbFisica").on('click', function () {
            if ($('#rbOutorgante1').prop('disabled')) {
                alert('Essa opção esta desabilitada, Verifique junto ao Órgão.');
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

    //Funcões Procuração Simples

    function showPessoaOutorganteHidden() {
        document.getElementById('lvbPJProSimples').style.display = "none";
        document.getElementById('selPessoaJuridicaProcSimples').style.display = "none";
        document.getElementById('hdnRbOutorgante').value = "PF"
        document.getElementById('imgPj').style.display = "none";
        document.getElementById('PessoaJuridicaOutorgante').style.display = "none";
    }

    function showPessoaOutorgante() {
        document.getElementById('PessoaJuridicaOutorgante').style.display = "";
        document.getElementById('lvbPJProSimples').style.display = "";
        document.getElementById('selPessoaJuridicaProcSimples').style.display = "inline";
        document.getElementById('hdnRbOutorgante').value = "PJ"
        document.getElementById('imgPj').style.display = "";
    }

    function showData() {
        document.getElementById('lblDt').style.display = "";
        document.getElementById('txtDt').style.display = "";
        document.getElementById('imgDt').style.display = "";
        document.getElementById('dvDataLimite').style.display = "";
        document.getElementById('hdnRbValidate').value = "Determinada";
    }

    function showDataNot() {
        document.getElementById('lblDt').style.display = "none";
        document.getElementById('txtDt').style.display = "none";
        document.getElementById('txtDt').value = "";
        document.getElementById('imgDt').style.display = "none";
        document.getElementById('dvDataLimite').style.display = "none";
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
            alert('Informe o Número do Processo.');
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
        objTabelaDinamicaUsuarioProcessos.adicionar([document.getElementById('hdnIdProcedimento').value, document.getElementById('hdnTbNumeroProc').value, document.getElementById('txtTipo').value, '<img src="/infra_css/svg/remover.svg" title="Remover Processo" onclick="$(this).closest(\'tr\').remove(); removerProcesso('+document.getElementById('hdnIdProcedimento').value+')" class="infraImg">']);

        document.getElementById('hdnIdProcedimento').value = '';
        document.getElementById('hdnTbNumeroProc').value = '';
        document.getElementById('txtNumeroProcesso').value = '';
        document.getElementById('txtTipo').value = '';
        document.getElementById('btnAdicionar').disabled = true;

    }

    function removerProcesso(IdProcedimento){

        var clean = '', arrhdnTbProcessos = $('input#hdnTbProcessos').val().split('¥');

        if (arrhdnTbProcessos.length > 0) {
            for (i = 0; i < arrhdnTbProcessos.length; i++) {
                var hdnLinha = arrhdnTbProcessos[i].split('±');
                if(IdProcedimento != hdnLinha[0]){
                    clean += (clean != '' ? '¥' : '') + arrhdnTbProcessos[i];
                }
            }
        }

        $('input#hdnTbProcessos').val(clean);
        if(clean == ''){ $('#tbProcessos').hide() }

    }

    //Funcões Procuração Simples - FIM

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
            alert('CPF do usuário externo é de preenchimento obrigátorio.');
            return false;
        }

        if (dsNome.length == 0) {
            alert('Nome do usuário externo é de preenchimento obrigátorio.');
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


        //Validação de Existencia de Procuração Especial
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
                                console.log($(this)[0].innerHTML)
                                var valor = $(this)[0].innerHTML;
                                dados.push(valor);
                            });

                            //Inicio - Ajax Validação Usuário
                            //Validando para ver se o usuário externo não é pendente
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
                                        alert("Usuário Externo com pendência de liberação de cadastro.");
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
                    alert("Não é permitido adicionar este usuário, pois o mesmo já possui uma Procuração Especial para esta PJ.");
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

        console.log(456);
        //Validando combo Tipo de PRocuração
        if (document.getElementById('selTipoProcuracao').value == "") {
            alert("Escolha um Tipo de Procuração.");
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
                        alert('Usuario não é um Responsável Legal.');
                })
            }
        });

    }


    function validarCampos() {
        $tpProc = document.getElementById('selTipoProcuracao').value;

        if(document.getElementById('hdnCpfContExterno').value.replace(/\D/g, "") == ""){
            alert('O seu cadastro está incompleto, faltando o CPF. \n\nEntre em contato com a gestão do SEI deste órgão pedindo que atualize dados do seu cadastro como Usuário Externo.');
            return false;
        }

        //Validando combo Tipo de PRocuração
        if (document.getElementById('selTipoProcuracao').value == "") {
            alert("Escolha um Tipo de Procuração.");
            return false;
        }
        if ($tpProc == "S") {
            //Validando CPF
            if ($tpProc == "S" && document.getElementById('txtNumeroCpfProcuradorSimples').value == "") {
                alert("Informe o CPF completo");
                return false;
            }


            //Validando Combo Usuário Externo

            if (document.getElementById('selUsuarioSimples').value == "") {
                alert("Selecione o Nome do Usuário Externo.");
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
            //Verifica se a data é posteriar a data atua
            if (document.getElementById('txtDt').value != "") {
                if (!infraValidarData(document.getElementById('txtDt').value, false)) {
                    alert("Formato de data inválido.");
                    return false;
                }

                var data = document.getElementById('txtDt').value;
                if (infraCompararDatas(document.getElementById('hdnDtAtual').value, data) < 0) {
                    alert("A data informada no campo Validade não é permitida. Informe uma data posterior a data atual.");
                    return false;
                }
            }


            if (document.getElementById('hdnRbAbrangencia').value == "") {
                alert("Informe um tipo de Abrangência.");
                return false;
            }

            //Caso a Abrangência seja Especifica, validar Table
            if (document.getElementById('hdnRbAbrangencia').value == "E") {
                var tbUsuarioProcesso = document.getElementById('tbProcessos');
                var qtdLinhas = tbUsuarioProcesso.rows.length;

                if (qtdLinhas == 1) {
                    alert('Adicione ao menos um Processo ao gerar a Procuração Eletrônica sob a Abrangência: Processos Específicos.');
                    return false;
                }
            }
        }

        if ($tpProc == "S") {
            //Procuração Simples
            //Validação de Modal
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

            //Fim Validação Modal

            //Verificando Pendencia de Usuário Externo
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
                        alert("Usuário Externo com pendência de liberação de cadastro.");
                    } else {
                        //Abre Modal
                        $.ajax({
                            dataType: 'xml',
                            method: 'POST',
                            url: '<?php echo $strLinkAjaxValidarExistenciaProc?>',
                            data: dados,
                            success: function (data) {
                                $.each($(data).find('item'), function (i, j) {
                                    var valor = $(this)[i].innerHTML;
                                    if ($(j).attr("id") == 0) {


                                        //Modal para Assinatura
                                        parent.infraAbrirJanelaModal('<?php echo PaginaSEIExterna::getInstance()->formatarXHTML(SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=peticionamento_usuario_externo_vinc_pe'))?>',
                                            770,
                                            520,
                                            '', //options
                                            false); //modal

                                    } else {

                                        //Modal para Existencia de Procuração
                                        //Modal para Validação
                                        parent.infraAbrirJanelaModal($(j).attr("id"),
                                            600,
                                            300,
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


            //Fim Procuração Especial Modal
        }


        //Caso seja procuração especial
        if ($tpProc == "E") {
            var tbUsuarioProcuracao = document.getElementById('tbUsuarioProcuracao');
            var qtdLinhas = tbUsuarioProcuracao.rows.length;

            if (qtdLinhas == 1) {
                alert('Usuário Externo não foi selecionado.');
                return false;
            }

            //Validação Modal - Procuração Especial
            //Validação de Modal
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
                        var valor = $(this)[0].innerHTML;
                        if ($(j).attr("id") == 0) {

                            //Modal para Assinatura
                            parent.infraAbrirJanelaModal('<?php echo PaginaSEIExterna::getInstance()->formatarXHTML(SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=peticionamento_usuario_externo_vinc_pe'))?>',
                                770,
                                520,
                                '', //options
                                false); //modal

                        } else {

                            //Modal para Existencia de Procuração
                            //Modal para Validação
                            parent.infraAbrirJanelaModal($(j).attr("id"),
                                770,
                                520,
                                '', //options
                                false); //modal


                        }
                    })
                }
            });

            //Fim Procuração Especial Modal

        }

    }

    var textoProcEsp = '<div class="espacamentoConteudo">';
    textoProcEsp += 'A Procuração Eletrônica Especial concede, no âmbito do(a) <?=$siglaOrgao?>, ';
    textoProcEsp += 'ao Usuário Externo poderes para:';
    textoProcEsp += '</div>';

    textoProcEsp += '<div class="margemConteudo">';
    textoProcEsp += '1. Gerenciar o cadastro da Pessoa Jurídica Outorgante ';
    textoProcEsp += '(exceto alterar o Responsável Legal ou outros Procuradores Especiais).';
    textoProcEsp += '</div>';

    textoProcEsp += '<div class="margemConteudo">';
    textoProcEsp += '2. Receber, Cumprir e Responder Intimações Eletrônicas e realizar Peticionamento Eletrônico ';
    textoProcEsp += 'em nome da Pessoa Jurídica Outorgante.';
    textoProcEsp += '</div>';

    textoProcEsp += '<div class="margemConteudo">';
    textoProcEsp += '3. Representar a Pessoa Jurídica Outorgante com todos os poderes previstos no sistema, ';
    textoProcEsp += 'inclusive no substabelecimento ao emitir Procurações Eletrônicas Simples, habilitando-o a praticar todos os atos processuais, inclusive confessar, reconhecer a procedência do pedido, transigir, desistir, renunciar, receber, dar quitação e firmar compromisso.';
    textoProcEsp += '</div>';

    textoProcEsp += '<div class="margemConteudo">';
    textoProcEsp += '4. Substabelecer os poderes outorgados pela presente Procuração, ao conceder Procurações Eletrônicas Simples ';
    textoProcEsp += 'a outros Usuários Externos, em âmbito geral ou para processos específicos, conforme poderes ';
    textoProcEsp += 'definidos, para representação da Pessoa Jurídica Outorgante.';
    textoProcEsp += '</div>';

    textoProcEsp += '<div class="espacamentoConteudo">';
    textoProcEsp += 'Ao conceder a Procuração Eletrônica Especial, você se declara ciente de que:';
    textoProcEsp += '</div>';

    textoProcEsp += '<div class="margemConteudo">';
    textoProcEsp += '&bullet; ';
    textoProcEsp += 'Poderá, a qualquer tempo, por meio do SEI-<?=$siglaOrgao?>, ';
    textoProcEsp += 'revogar a Procuração Eletrônica Especial;';
    textoProcEsp += '</div>';

    textoProcEsp += '<div class="margemConteudo">';
    textoProcEsp += '&bullet; ';
    textoProcEsp += 'O Outorgado poderá, a qualquer tempo, por meio do SEI-<?=$siglaOrgao?>, ';
    textoProcEsp += 'renunciar a Procuração Eletrônica Especial;';
    textoProcEsp += '</div>';

    textoProcEsp += '<div class="margemConteudo">';
    textoProcEsp += '&bullet; ';
    textoProcEsp += 'A validade desta Procuração está circunscrita ao(à) <?=$siglaOrgao?> ';
    textoProcEsp += 'e por tempo indeterminado, salvo se revogada ou renunciada, de modo que ela não pode ser usada para convalidar quaisquer atos praticados pelo Outorgado em representação da Pessoa Jurídica no âmbito de outros órgãos ou entidades.';
    textoProcEsp += '</div>';

    var igualHtml = '<div class="espacamentoConteudo">';
    igualHtml += '<label class="label-bold">Atenção:</label> ';
    igualHtml += 'Para poder receber uma Procuração Eletrônica o ';
    igualHtml += 'Usuário Externo já deve possuir cadastro no SEI-<?=$siglaOrgao?> liberado.';
    igualHtml += '</div>';

    //Procuração Simples
    var textoProcSimp = '<div class="espacamentoConteudo">';
    textoProcSimp += 'A Procuração Eletrônica Simples concede, no âmbito do(a) <?=$siglaOrgao?>, ao Usuário Externo, os Poderes expressamente estabelecidos e em conformidade com a Validade e Abrangência definidos.';
    textoProcSimp += '</div>';

    textoProcSimp += '<div class="espacamentoConteudo">';
    textoProcSimp += 'Ao conceder a Procuração Eletrônica Simples, você se declara ciente de que:';
    textoProcSimp += '</div>';

    textoProcSimp += '<div class="margemConteudo">';
    textoProcSimp += '&bullet; '
    textoProcSimp += 'Poderá, a qualquer tempo, por meio do SEI-<?=$siglaOrgao?>, revogar a Procuração Eletrônica Simples;';
    textoProcSimp += '</div>';

    textoProcSimp += '<div class="margemConteudo">';
    textoProcSimp += '&bullet; '
    textoProcSimp += 'O Outorgado poderá, a qualquer tempo, por meio do SEI-<?=$siglaOrgao?>, renunciar a Procuração Eletrônica Simples;';
    textoProcSimp += '</div>';

    textoProcSimp += '<div class="margemConteudo">';
    textoProcSimp += '&bullet; '
    textoProcSimp += 'A validade desta Procuração está circunscrita ao(à) <?=$siglaOrgao?> e em conformidade com os Poderes, Validade e Abrangência definidos, salvo se revogada ou renunciada, de modo que ela não pode ser usada para convalidar quaisquer atos praticados pelo Outorgado em representação do Outorgante no âmbito de outros órgãos ou entidades.';
    textoProcSimp += '</div>';

    var igualHtml = '<div class="espacamentoConteudo">';
    igualHtml += '<label class="label-bold">Atenção:</label> ';
    igualHtml += 'Para poder receber uma Procuração Eletrônica o ';
    igualHtml += 'Usuário Externo já deve possuir cadastro no SEI-<?=$siglaOrgao?> liberado.';
    igualHtml += '</div>';
    //Fim Procuração Simples

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

        }
        if (el.value == '<?php echo MdPetVincRepresentantRN::$PE_PROCURADOR_ESPECIAL?>') {
            //Caso a Intimação seja Especial, apagar os campos da Simples caso estejam preenchidos
            document.getElementById('txtNumeroCpfProcuradorSimples').value = "";
            infraSelectLimpar('selUsuarioSimples');
            document.getElementById('rbValidade').checked = false;
            document.getElementById('rbValidade2').checked = false;
            document.getElementById('lblDt').style.display = "none";
            document.getElementById('txtDt').style.display = "none";
            document.getElementById('imgDt').style.display = "none";
            document.getElementById('PessoaJuridicaOutorgante').style.display = "none";
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

            //Desbloqueando botões da tela de procuração especial

            document.getElementById('btnValidarEspecial').disabled = false;
            document.getElementById('btnAdicionarProcurador').disabled = false;

            //Bloqueando botões da tela de procuração simples
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

            //Mostrando os Botões Peticionar e Cancelar Inferior
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

        //Procuração Simples
        if (el.value == '<?php echo MdPetVincRepresentantRN::$PE_PROCURADOR_SIMPLES?>') {

            //Limpando campos caso o Tipo de PRocuração seja Especial
            objTabelaDinamicaUsuarioProcuracao.limpar();
            document.getElementById('tbUsuarioProcuracao').style.display = "none";
            document.getElementById('hdnIdUsuario').value = "";
            document.getElementById('hdnIdUsuarioProcuracao').value = "";
            $mostrar = true;
            if (document.getElementById('hdnBloqueioRadio').value == "true") {
                $('.radioJuridica').hide().find('input').prop('disabled', true);
                document.getElementById('lvbPJProSimples').style.paddingLeft = "52px";
                document.getElementById("ajudaPJ").style.display = "none";
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
                document.getElementById("txtExplicativo").style.display = "";
                document.getElementById("hiddenOutorgante").style.display = "";


                //Mostrando os Botões Peticionar e Cancelar Inferior
                document.getElementById("sbmPeticionarInferior").style.display = "";
                document.getElementById("btnCancelarInferior").style.display = "";

                //Bloqueando botões da tela de procuração especial

                document.getElementById('btnValidarEspecial').disabled = true;
                document.getElementById('btnAdicionarProcurador').disabled = true;

                //Desbloqueando botões da tela de procuração simples
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
                msg = 'Informe o CPF do usuário externo completo ou válido para realizar a pesquisa.';
            }
        } else {
            msg = 'É necessário preecher o campo CPF do Usuário Externo com 11 caracteres.';
            erro = true;
        }

        //Verificando Usuário Logado
        if (valor == cpfUsuariologado) {
            erro = true;
            msg = "Não é permitida a geração de Procuração Eletrônica para você mesmo. \n Informe o CPF da Pessoa Física que irá te representar.";
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

        //Verificar se o cnpj já esta sendo utilizado num vinculo
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
                //Caso retorne nada, o cpf não existe
                if (resultado[0].childElementCount == 0) {
                    alert('Cadastro de Usuário Externo não localizado no sistema. Oriente o Usuário a realizar o Cadastro no Acesso Externo do SEI.');
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
                        $('#selUsuario').prepend('<option value="" selected="selected">Selecione o Usuario Externo</option>');
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

        //Verificar se o cnpj já esta sendo utilizado num vinculo
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
                //Caso retorne nada, o cpf não existe
                if (resultado[0].childElementCount == 0) {
                    alert('Cadastro de Usuário Externo não localizado no sistema. Oriente o Usuário a realizar o Cadastro no Acesso Externo do SEI.');
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
                        $('#selUsuarioSimples').prepend('<option value="" selected="selected">Selecione o Usuario Externo</option>');
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
