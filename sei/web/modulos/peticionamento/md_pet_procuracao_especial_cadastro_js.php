<?php
$strLinkAjaxUsuarios = SessaoSEIExterna::getInstance()->assinarLink('controlador_ajax_externo.php?acao_ajax=md_pet_vinc_usu_ext_autocompletar');
$strLinkConsultaDadosUsuario = SessaoSEIExterna::getInstance()->assinarLink('controlador_ajax_externo.php?acao_ajax=md_pet_vinc_usu_ext_dados_usuario_externo');
$strLinkConsultaResponsavelLegal = SessaoSEIExterna::getInstance()->assinarLink('controlador_ajax_externo.php?acao_ajax=md_pet_vinc_validar_representante');
$strLinkConsultaUsuarioExternoValido = SessaoSEIExterna::getInstance()->assinarLink('controlador_ajax_externo.php?acao_ajax=md_pet_vinc_consulta_usuext_valido');
?>
<script type="text/javascript">

    function inicializar() {
        // document.getElementById("txtNumeroCpfProcurador").addEventListener("keyup", controlarEnterValidarProcesso, false);
    }

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

    function criarRegistroTabelaProcuracao() {
        var nuCpf = document.getElementById('txtNumeroCpfProcurador').value;
        nuCpf = nuCpf.trim();

        var dsNome = document.getElementById('txtNomeProcurador').value;
        dsNome = dsNome.trim();

        if (nuCpf.length == 0) {
            alert('CPF do usuário externo é de preenchimento obrigátorio.');
            return false;
        }

        if (dsNome.length == 0) {
            alert('Nome do usuário externo é de preenchimento obrigátorio.');
            return false;
        }

        var hdnIdUsuarioProcuracao = document.getElementById('txtNomeProcurador').value;
        var hdnSelPessoaJuridica = document.getElementById('selPessoaJuridica').value;

        //Quando adicionado mais de um usuario ao mesmo tempo -- IdUsuario separado por +
        if (document.getElementById('hdnIdUsuario').value == '') {
            document.getElementById('hdnIdUsuario').value = hdnIdUsuarioProcuracao;
        } else {
            document.getElementById('hdnIdUsuario').value += '+' + hdnIdUsuarioProcuracao;
        }

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
                if (valido == 0) {
                    alert('Não é permitido adicionar este usuário, pois o mesmo já possui uma Procuração Especial para esta PJ.');
                    return false;
                } else {
                    var dados = [];
                    $('dados', data).children().each(function () {
                        var valor = $(this).context.innerHTML;
                        dados.push(valor);
                        ;
                    });

                    objTabelaDinamicaUsuarioProcuracao.adicionar(dados);

                    $("#tbUsuarioProcuracao").show();
                    document.getElementById('txtNumeroCpfProcurador').value = '';
                    document.getElementById('txtNomeProcurador').value = '';
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

    function peticionar() {
        var selPessoaJuridica = document.getElementById('selPessoaJuridica').value;
        var hdnIdContExterno = document.getElementById('hdnIdContExterno').value;
        var usuarioValido = validarResponsavelLegal(selPessoaJuridica, hdnIdContExterno);
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
        var tbUsuarioProcuracao = document.getElementById('tbUsuarioProcuracao');
        var qtdLinhas = tbUsuarioProcuracao.rows.length;

        if (qtdLinhas == 1) {
            alert('Usuário Externo não foi selecionado.');
            return false;
        }

        //Modal para Assinatura
        infraAbrirJanela('<?php echo PaginaSEIExterna::getInstance()->formatarXHTML(SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=peticionamento_usuario_externo_vinc_pe'))?>',
            'concluirPeticionamento',
            770,
            480,
            '', //options
            false); //modal
    }

    var textoProcEsp = '<div class="espacamentoConteudo">';
    textoProcEsp += '<label class="label-bold">Observação:</label> ';
    textoProcEsp += 'Por enquanto apenas o tipo "Procuração Eletrônica Especial" está disponível. Até outubro será disponibilizado o tipo "Procuração Eletrônica", para conceder poderes a outros Usuários Externos, em âmbito geral ou para processos específicos, conforme poderes estabelecidos.';
    textoProcEsp += '</div>';

    textoProcEsp += '<div class="espacamentoConteudo">';
    textoProcEsp += 'A Procuração Eletrônica Especial concede, no âmbito do(a) <?=$siglaOrgao?>, ';
    textoProcEsp += 'ao Usuário Externo poderes para:';
    textoProcEsp += '</div>';

    textoProcEsp += '<div class="margemConteudo">';
    textoProcEsp += '1. Gerenciar o cadastro da Pessoa Jurídica Outorgante ';
    textoProcEsp += '(exceto alterar o Responsável Legal ou outros Procuradores Especiais).';
    textoProcEsp += '</div>';

    textoProcEsp += '<div class="margemConteudo">';
    textoProcEsp += '2. Receber Intimações Eletrônicas e realizar Peticionamento Eletrônico ';
    textoProcEsp += 'em nome da Pessoa Jurídica Outorgante, com todos os poderes previstos no sistema.';
    textoProcEsp += '</div>';

    textoProcEsp += '<div class="margemConteudo">';
    textoProcEsp += '3. Conceder Procurações Eletrônicas a outros Usuários Externos, ';
    textoProcEsp += 'em âmbito geral ou para processos específicos, conforme poderes ';
    textoProcEsp += 'estabelecidos, para representação da Pessoa Jurídica Outorgante.';
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
    textoProcEsp += 'e por tempo indeterminado, salvo se revogada ou renunciada, ';
    textoProcEsp += 'de modo que ela não pode ser usada para convalidar quaisquer ';
    textoProcEsp += 'atos praticados pelo Outorgado em representação da Pessoa Jurídica ';
    textoProcEsp += 'no âmbito de outros órgãos ou entidades.';
    textoProcEsp += '</div>';

    textoProcEsp += '<div class="espacamentoConteudo">';
    textoProcEsp += 'Caso concorde com os termos apresentados, indique abaixo o ';
    textoProcEsp += 'Usuário Externo para o qual deseja conceder Procuração Eletrônica Especial.';
    textoProcEsp += '</div>';

    var igualHtml = '<div class="espacamentoConteudo">';
    igualHtml += '<label class="label-bold">Atenção:</label> ';
    igualHtml += 'Para poder receber uma Procuração Eletrônica Especial o ';
    igualHtml += 'Usuário Externo já deve possuir cadastro no SEI-<?=$siglaOrgao?> liberado.';
    igualHtml += '</div>';

    var textoProcElt = '';
    var textoProcSub = '';

    var html = textoProcEsp + igualHtml;
    document.getElementById("txtExplicativo").innerHTML = html;

    function pegaInfo(el) {
        if (el.value == '<?php echo MdPetVincRepresentantRN::$PE_PROCURADOR_ESPECIAL?>') {
            html = textoProcEsp;
        }

        if (el.value == '<?php echo MdPetVincRepresentantRN::$PE_PROCURADOR?>') {
            html = textoProcElt;
        }

        if (el.value == '<?php echo MdPetVincRepresentantRN::$PE_PROCURADOR_SUBSTALECIDO?>') {
            html = textoProcSub;
        }

        html += igualHtml;
        document.getElementById("txtExplicativo").innerHTML = html;
    }

    function infraMascaraCPF(objeto) {
        var novoValor = maskCPF($.trim(objeto.value));
        objeto.value = novoValor;
    }

    function validaCpf(objeto) {
        var erro = false;
        var valor = $.trim(objeto.value.replace(/\D/g, ""));

        if (valor.length == 11) {
            if (!infraValidarCpf(valor)) {
                erro = true;
            }
        } else {
            erro = true;
        }

        if (erro) {
            alert('Informe o CPF do usuário externo completo ou válido para realizar a pesquisa.');
            document.getElementById('txtNumeroCpfProcurador').value = '';
        }
    }

    function maskCPF(cpf) {
        cpf = cpf.replace(/\D/g, "");
        cpf = cpf.replace(/(\d{3})(\d)/, "$1.$2");
        cpf = cpf.replace(/(\d{3})(\d)/, "$1.$2");
        cpf = cpf.replace(/(\d{3})(\d{1,2})$/, "$1-$2");

        return cpf;
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
            url: '<?php echo $strLinkConsultaUsuarioExternoValido?>',
            data: {
                'cpf': $("#txtNumeroCpfProcurador").val()
            },
            error: function (dados) {
                console.log(dados);
            },
            success: function (data) {
                document.getElementById('txtNomeProcurador').value = '';
                document.getElementById('hdnIdUsuarioProcuracao').value = '';
                document.getElementById('btnAdicionarProcurador').style.display = 'none';

                if ($(data).find('no-usuario').text() != "" && $(data).find('nu-contato').text() != "") {
                    if ($(data).find('mensagem').text() == "pendente") {
                        alert('Usuário Externo com pendência de liberação de cadastro.');
                    } else {
                        var contatos = $(data).find('contato');
                        var select = document.getElementById('txtNomeProcurador');
                        select.length = 0;
                        $.each($(data).find('contato'), function (chave, item) {
                            var option = document.createElement("option");
                            option.value = $(item).find('nu-contato').text();
                            option.text = $(item).find('no-usuario').text() + " (" + $(item).find('sg-contato').text() + ")";
                            select.add(option);
                            if (contatos.length > 1) {
                                select.removeAttribute('disabled');
                            }
                        });
                        document.getElementById('btnAdicionarProcurador').style.display = '';
                    }
                } else {
                    alert('Cadastro de Usuário Externo não localizado no sistema. Oriente o Usuário a realizar o Cadastro no Acesso Externo do SEI.');
                }
            }
        });

    }

    function controlarEnterValidarProcesso(e) {
        var focus = returnElementFocus();
        if (infraGetCodigoTecla(e) == 13) {
            document.getElementById('btnValidar').onclick();
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