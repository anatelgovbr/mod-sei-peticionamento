<script type="text/javascript">

    var tipoRespostaMultipla = true;
    var staTipoAcessoParcial = null;
    var staTipoAcessoIntegral = null;
    var staSemAcessoExterno = null;
    var objLupaUsuarioExternoF = null;
    var valor = null;
    var identificacaoDiv = null;


    function inicializar() {
        infraEfeitoTabelas();
        document.getElementsByTagName("BODY")[0].onresize = function () {
            resizeIFramePorConteudo()
        };

        try {
            objTabelaDinamicaUsuarios.remover = function () {
                controlarShowHideTabDestinatario();
                controlarShowHideTabDestinatarioJuridico();
                return true;
            }

            objTabelaDinamicaUsuarios.registroDuplicado = function (cpf) {
                var duplicado = false;
                var tbUsuarios = document.getElementById('tblEnderecosEletronicos');

                for (var i = 1; i < tbUsuarios.rows.length; i++) {
                    var cpfTab = tbUsuarios.rows[i].cells[3].innerText.trim();

                    if (cpf == cpfTab) {
                        duplicado = true;
                        break;
                    }
                }
                return duplicado;
            };

            objTabelaDinamicaUsuarios.registroDuplicadoPorId = function (id) {
                var duplicado = false;
                var tbUsuarios = document.getElementById('tblEnderecosEletronicos');

                for (var i = 1; i < tbUsuarios.rows.length; i++) {
                    var idTab = tbUsuarios.rows[i].cells[0].innerText.trim();

                    if (id == idTab) {
                        duplicado = true;
                        break;
                    }
                }
                return duplicado;
            };


            infraEfeitoTabelas();
            document.getElementById('txtUsuario').focus();
            preencherVarsAcessoExterno();
        } catch (err) {

        }

    }


    function preencherVarsAcessoExterno() {
        staTipoAcessoParcial = document.getElementById('hdnStaAcessoParcial').value;
        staTipoAcessoIntegral = document.getElementById('hdnStaAcessoIntegral').value;
        staSemAcessoExterno = document.getElementById('hdnStaSemAcesso').value;
    }

    //Validando troca radio


    function preparaPessoaFisica() {

        try {
            if (document.getElementById('intimacoesFisica').value == 0) {
                document.getElementById("tblEnderecosEletronicos").style.display = "none";
            } else {
                document.getElementById("tblEnderecosEletronicos").style.display = "";
            }
        } catch (err) {

        }

        objAutoCompletarUsuario = new infraAjaxAutoCompletar('hdnIdDadosUsuario', 'txtUsuario', '<?= $strLinkAjaxUsuarios ?>');
        objAutoCompletarUsuario.limparCampo = true;
        objAutoCompletarUsuario.tamanhoMinimo = 3;


        objAutoCompletarUsuario.prepararExecucao = function () {


            document.getElementById('txtEmail').value = '';
            return 'intimacaoPF=t&txtUsuario=' + document.getElementById('txtUsuario').value + '&hdnDadosUsuario=' + document.getElementById('hdnDadosUsuario').value;

        };

        objAutoCompletarUsuario.processarResultado = function (id, descricao, complemento) {
            if (id != '') {
                var arrayResultado = complemento.split(" - ");
                document.getElementById('txtEmail').value = arrayResultado[0];
            }
        };


        objTabelaDinamicaUsuarios = new infraTabelaDinamica('tblEnderecosEletronicos', 'hdnDadosUsuario', false, false);
        objTabelaDinamicaUsuarios.gerarEfeitoTabela = true;

        objTabelaDinamicaUsuarios.registroDuplicado = function (cpf) {
            var duplicado = false;
            var tbUsuarios = document.getElementById('tblEnderecosEletronicos');

            for (var i = 1; i < tbUsuarios.rows.length; i++) {
                var cpfTab = tbUsuarios.rows[i].cells[3].innerText.trim();

                if (cpf == cpfTab) {
                    duplicado = true;
                    break;
                }
            }
            return duplicado;
        };

        objTabelaDinamicaUsuarios.registroDuplicadoPorId = function (id) {
            var duplicado = false;
            var tbUsuarios = document.getElementById('tblEnderecosEletronicos');

            for (var i = 1; i < tbUsuarios.rows.length; i++) {
                var idTab = tbUsuarios.rows[i].cells[0].innerText.trim();

                if (id == idTab) {
                    duplicado = true;
                    break;
                }
            }
            return duplicado;
        };

        //Protocolos da Intimação
        carregarComponenteProtocoloIntimacao();

        //Protocolos Disponibilizados
        carregarComponenteProtocoloDisponibilizado();

        esconderAnexos(false);
        mostrarProtocoloParcial(false);

        objLupaTipoProcesso = new infraLupaText('txtUsuario', 'hdnIdUsuario', '<?= $strLinkTipoProcessoSelecaoF ?>');
        objLupaTipoProcesso.processarSelecao = function (itens) {
            var paramsAjax = {
                paramsBusca: itens.value,
                paramsIdDocumento: document.getElementById('hdnIdDocumento').value
            };

            $.ajax({
                url: '<?= $strLinkAjaxTransportaUsuarios ?>',
                type: 'POST',
                dataType: 'XML',
                data: paramsAjax,
                success: function (r) {

                    document.getElementById('txtUsuario').value = $(r).find('Nome').text();
                    document.getElementById('txtEmail').value = $(r).find('Email').text();
                    document.getElementById('hdnIdDadosUsuario').value = $(r).find('Id').text();
                },
                error: function (e) {
                    console.error('Erro ao processar o XML do SEI: ' + e.responseText);
                }
            });
            return true;
        }
    }

    function preparaPessoaJuridica(tipo) {

        objAutoCompletarUsuario = new infraAjaxAutoCompletar('hdnIdDadosUsuario', 'txtUsuario', '<?= $strLinkAjaxUsuariosJuridicos ?>');
        objAutoCompletarUsuario.limparCampo = true;
        objAutoCompletarUsuario.tamanhoMinimo = 3;

//AutoComplete
        objAutoCompletarUsuario.prepararExecucao = function () {

            document.getElementById('txtEmail').value = '';
            return 'intimacaoPJ=t&txtUsuario=' + document.getElementById('txtUsuario').value + '&hdnDadosUsuario=' + document.getElementById('hdnDadosUsuario').value + '&gerados=' + document.getElementById('gerados').value;

        };


        objAutoCompletarUsuario.processarResultado = function (id, descricao, complemento) {
            if (id != '') {

                document.getElementById('txtEmail').value = infraFormatarCnpj(complemento);
                document.getElementById('txtUsuario').value = descricao;

            }
        };

        objTabelaDinamicaUsuarios = new infraTabelaDinamica('tblEnderecosEletronicos', 'hdnDadosUsuario', false, false);
        objTabelaDinamicaUsuarios.gerarEfeitoTabela = true;

        objTabelaDinamicaUsuarios.registroDuplicado = function (cpf) {

            var duplicado = false;
            var tbUsuarios = document.getElementById('tblEnderecosEletronicos');

            for (var i = 1; i < tbUsuarios.rows.length; i++) {
                var cpfTab = tbUsuarios.rows[i].cells[2].innerText;

                if (cpf == cpfTab) {

                    return true;
                    break;
                }
            }
            return duplicado;
        };


//Protocolos da Intimação
        carregarComponenteProtocoloIntimacao();

//Protocolos Disponibilizados
        carregarComponenteProtocoloDisponibilizado();

        esconderAnexos(false);
        mostrarProtocoloParcial(false);


        var tbUsuarios = document.getElementById('tblEnderecosEletronicos');
        if (tbUsuarios.rows.length < 2) {
            document.getElementById("tblEnderecosEletronicos").style.display = "none";
        } else {
            document.getElementById("tblEnderecosEletronicos").style.display = "";
        }


        document.body.addEventListener('DOMSubtreeModified', function () {


            try {
                var tbUsuarios = document.getElementById('tblEnderecosEletronicos');
                var qtdEmGeracao = (tbUsuarios.rows.length - 1) - document.getElementById('intimacoes').value;

                if (document.getElementById('intimacoes').value != 0) {
                    //Com intimação já gerada
                    if (document.getElementById('intimacoes').value >= 1 && qtdEmGeracao == 0) {

                        document.getElementById("hiddeAll1").style.display = "none";
                        document.getElementById("hiddeAll2").style.display = "none";
                        document.getElementById("conteudoHide2").style.display = "none";

                    } else {

                        document.getElementById("hiddeAll1").style.display = "";
                        document.getElementById("hiddeAll2").style.display = "";
                        document.getElementById("conteudoHide2").style.display = "";

                    }

                } else {

                    if (qtdEmGeracao >= 1) {

                        document.getElementById("hiddeAll1").style.display = "";
                        document.getElementById("hiddeAll2").style.display = "";
                        document.getElementById("conteudoHide2").style.display = "";
                        document.getElementById("hiddeTable").style.display = "";

                    } else {

                        document.getElementById("hiddeAll1").style.display = "none";
                        document.getElementById("hiddeAll2").style.display = "none";
                        document.getElementById("conteudoHide2").style.display = "none";
                        document.getElementById("hiddeTable").style.display = "none";
                    }

                }


            } catch (err) {

            }


        }, false);

//Limpando input hidden

        document.getElementById("txtUsuario").addEventListener("keydown", function (event) {

            if (event.keyCode == 8) {
                document.getElementById('hdnIdDadosUsuario').value = '';
                document.getElementById('hdnIdUsuario').value = '';
            }

        });

        objLupaJuridico = new infraLupaText('txtUsuario', 'hdnIdUsuario', '<?= $strLinkTipoProcessoSelecao ?>');
        objLupaJuridico.processarSelecao = function (itens) {
            var paramsAjax = {
                paramsBusca: itens.value,
                paramsIdDocumento: document.getElementById('hdnIdDocumento').value
            };

            $.ajax({

                url: '<?= $strLinkAjaxJuridicos ?>',
                type: 'POST',
                dataType: 'XML',
                data: paramsAjax,
                success: function (r) {

                    document.getElementById('txtUsuario').value = $(r).find('Nome').text();
                    document.getElementById('txtEmail').value = $(r).find('Cnpj').text();
                    document.getElementById('hdnIdDadosUsuario').value = $(r).find('Id').text();
                },
                error: function (e) {
                    console.error('Erro ao processar o XML do SEI: ' + e.responseText);
                }
            });
            return true;
        }


    }


    function verificarExistenciaRegistro(item) {
        var objSelected = document.getElementById('selMainIntimacao');
        var qtdOptionMain = objSelected.options.length;

        if (qtdOptionMain > 0) {
            for (var i = 0; i < qtdOptionMain; i++) {
                var valueOption = objSelected.options[i].value;
                if (item.value == valueOption) {
                    return true;
                }
            }
        }

        return false;
    }

    function carregarComponenteProtocoloDisponibilizado() {
        objLupaProtocolosDisponibilizados = new infraLupaSelect('selMainIntimacao', 'hdnProtocolosDisponibilizados', '<?= SessaoSEI::getInstance()->assinarLink('controlador.php?acao=acesso_externo_protocolo_selecionar&tipo_selecao=2&id_object=objLupaProtocolosDisponibilizados&id_procedimento=' . $_GET['id_procedimento'] . '&id_documento=' . $_GET['id_documento']) ?>');

        objLupaProtocolosDisponibilizados.processarSelecao = function (itens) {
            limparSelectedComponentes('A');
            limparSelectedComponentes('P');
            var qtd = itens.length;
            var unicoItem = itens.length == 1;
            var registroExistente = unicoItem ? verificarExistenciaRegistro(itens[0]) : false;
            for (var i = 0; i < qtd; i++) {

                if (unicoItem && registroExistente) {
                    return false;
                }

                preencherObjSelecao(itens[i], false);
            }


            //addSelectedCampos(0);
            return true;
        }

        objLupaProtocolosDisponibilizados.processarRemocao = function (itens) {
            var qtd = itens.length;

            var isPermiteExclusao = permiteExclusao(itens, 'P');

            if (isPermiteExclusao) {
                if (qtd > 0) {
                    for (var i = 0; i < qtd; i++) {
                        var valueOpt = itens[i].value;
                        var remover = '#selProtocolosDisponibilizados option[value="' + valueOpt + '"]';
                        $(remover).remove();
                    }

                    return true;
                }
            }
        }
    }

    function carregarComponenteProtocoloIntimacao() {
        objLupaProtocolosIntimacao = new infraLupaSelect('selMainIntimacao', 'hdnAnexosIntimacao', '<?= SessaoSEI::getInstance()->assinarLink('controlador.php?acao=acesso_externo_protocolo_selecionar&tipo_selecao=2&id_object=objLupaProtocolosIntimacao&id_procedimento=' . $_GET['id_procedimento'] . '&id_documento=' . $_GET['id_documento']) ?>');

        objLupaProtocolosIntimacao.processarSelecao = function (itens) {
            limparSelectedComponentes('A')
            limparSelectedComponentes('P')
            var qtd = itens.length;
            var unicoItem = itens.length == 1;
            var registroExistente = unicoItem ? verificarExistenciaRegistro(itens[0]) : false;

            for (var i = 0; i < qtd; i++) {
                if (unicoItem && registroExistente) {
                    return false;
                }

                preencherObjSelecao(itens[i], true);
            }

            return true;
        }

        objLupaProtocolosIntimacao.processarRemocao = function (itens) {
            var qtd = itens.length;
            var isPermiteExclusao = permiteExclusao(itens, 'A');

            if (isPermiteExclusao) {
                if (qtd > 0) {
                    for (var i = 0; i < qtd; i++) {
                        var valueOpt = itens[i].value;
                        var remover = '#selAnexosIntimacao option[value="' + valueOpt + '"]';
                        $(remover).remove();
                    }
                }

                return true;
            }
        }
    }

    function permiteExclusao(itens, tpSelect) {
        var valueEx = itens[0].value;
        var arrSelected = getValuesOptionsSelected(tpSelect, false);
        if (arrSelected.indexOf(valueEx) == -1) {
            return false;
        }

        return true;
    }

    function controlarSelected(el) {
        //limparSelectedComponentes('M');
        //addSelectedCampos(0);
        //addSelectedCampos(1);
    }

    function limparSelectedComponentes(opcao) {
        var selectMain = '';

        if (opcao == 'M') {
            selectMain = 'selMainIntimacao';
        } else if (opcao == 'A') {
            selectMain = 'selAnexosIntimacao'
        } else {
            selectMain = 'selProtocolosDisponibilizados';
        }

        var objSelected = document.getElementById(selectMain);
        var qtdOptionMain = objSelected.options.length;

        if (qtdOptionMain > 0) {
            for (var i = 0; i < qtdOptionMain; i++) {
                objSelected.options[i].selected = false;
            }
        }
    }


    function addSelectedCampos(anexo) {
        var tpSelect = anexo == '1' ? 'A' : 'P';
        var arrSelected = getValuesOptionsSelected(tpSelect, true);

        if (arrSelected.length > 0) {
            var selectMain = document.getElementById('selMainIntimacao');
            var qtdOptionMain = selectMain.options.length;

            if (qtdOptionMain > 0) {
                for (var i = 0; i < qtdOptionMain; i++) {
                    var valueOpt = selectMain.options[i].value;
                    if (arrSelected.indexOf(valueOpt) != -1) {
                        selectMain.options[i].selected = true;
                    }
                }
            }

        }
    }

    function getValuesOptionsSelected(opcao, isSelected) {
        var idCampoSel = '';

        if (opcao == 'M') {
            idCampoSel = 'selMainIntimacao';
        } else if (opcao == 'A') {
            idCampoSel = 'selAnexosIntimacao'
        } else {
            idCampoSel = 'selProtocolosDisponibilizados';
        }

        var objSel = document.getElementById(idCampoSel);
        var qtdOptions = objSel.options.length;

        var arrSelected = new Array();

        for (var i = 0; i < qtdOptions; i++) {
            var addValor = isSelected ? objSel.options[i].selected : true;

            if (addValor) {
                var valueOption = objSel.options[i].value;
                arrSelected.push(valueOption);
            }
        }

        return arrSelected;
    }

    function preencherObjSelecao(item, anexo) {
        var idCampoSel = anexo ? 'selAnexosIntimacao' : 'selProtocolosDisponibilizados';
        var tpLimpar = anexo ? 'P' : 'A'; //Se for anexo limpa o campo de protocolo, se não limpa o campo de anexo; (problema da remoção)
        var campoMultiplo = document.getElementById(idCampoSel);
        var opcao = document.createElement("option");

        opcao.value = item.value;
        opcao.text = item.title;
        opcao.selected = true;
        opcao.onclick = function () {
            limparSelectedComponentes('M');
            limparSelectedComponentes(tpLimpar);
            addSelectedCampos(0);
            addSelectedCampos(1);
        };

        campoMultiplo.appendChild(opcao);
    }


    function focusUltimaOption(anexo) {
    }


    //Transporta os intens do select Para a tabela.
    function transportarUsuario() {

        if (validarCamposObrigatoriosUsuario()) {

            var paramsAjax = {
                paramsBusca: document.getElementById('hdnIdDadosUsuario').value,
                paramsIdDocumento: document.getElementById('hdnIdDocumento').value
            };

            var tbUsuarios = document.getElementById('tblEnderecosEletronicos');

            for (var i = 1; i < tbUsuarios.rows.length; i++) {
                var razao = tbUsuarios.rows[i].cells[0].innerText;
                if (razao.trim() == document.getElementById('hdnIdDadosUsuario').value) {
                    alert("A pessoa física informada já possui intimação gerada para este documento.");

                    return true;
                    break;
                }
            }

            $.ajax({

                url: '<?= $strLinkAjaxTransportaUsuarios ?>',
                type: 'POST',
                dataType: 'XML',
                data: paramsAjax,
                success: function (r) {
                    if ($(r).find('Quantidade').text() == 1) {

                        if ($(r).find('Cadastro').text() > 0) {
                            alert(" A Pessoa Física selecionada já foi intimada através da Pessoa Jurídica abaixo. " + $(r).find('Vinculo').text());
                            return;
                        }

                    } else if ($(r).find('Quantidade').text() > 1) {

                        if ($(r).find('Cadastro').text() > 0) {
                            alert(" A Pessoa Física selecionada já foi intimada através das Pessoas Jurídicas abaixo. " + $(r).find('Vinculo').text());
                            return;
                        }

                    }

                    var tbUsuarios = document.getElementById('tblEnderecosEletronicos');
                    if (tbUsuarios.rows.length < 2) {
                        document.getElementById("tblEnderecosEletronicos").style.display = '';
                    }

                    showOrHideClass('tabUsuario', '');
                    limparCamposUsuario();

                    var cpf = $(r).find('Cpf').text();

                    while (cpf.length < 11) {
                        cpf = "0" + cpf;
                    }

                    cpf = cpf.substr(0, 3) + '.' + cpf.substr(3, 3) + '.' + cpf.substr(6, 3) + '-' + cpf.substr(9, 2);

                    var idContato = $(r).find('Id').text().trim();
                    var registroDuplicado = objTabelaDinamicaUsuarios.registroDuplicadoPorId(idContato);

                    if (registroDuplicado) {
                        alert("O usuário externo informado já possui intimação gerada para este documento.");
                        return false;
                    }

                    //Show Conteudo
                    document.getElementById('conteudoHide').style.display = '';
                    document.getElementById('lblTipodeResposta').style.display = 'none';

                    var usuariosCadastro = document.getElementById('hdnDadosUsuario').value;

                    if ($(r).find('Intimacao').text() > 0) {
                        objTabelaDinamicaUsuarios.adicionar([$(r).find('Id').text(), $(r).find('Nome').text(), $(r).find('Email').text(), cpf, $(r).find('DataIntimacao').text(), $(r).find('Situacao').text()]);
                    } else {
                        objTabelaDinamicaUsuarios.adicionar([$(r).find('Id').text(), $(r).find('Nome').text(), $(r).find('Email').text(), cpf, infraDataAtual(), 'Em geração']);
                    }

                    if ($(r).find('Intimacao').text() > 0) {
                        objTabelaDinamicaUsuarios.adicionarAcoes(document.getElementById('hdnIdDadosUsuario').value,
                            "<a href='#' onclick=\"abrirIntimacaoCadastrada('" + $(r).find('Url').text() + "')\"><img title='Consultar Destinatário' alt='Consultar Destinatário' src='/infra_css/imagens/consultar.gif' class='infraImg' /></a>",
                            false, false);
                        document.getElementById('hdnDadosUsuario').value = usuariosCadastro;
                    } else {
                        objTabelaDinamicaUsuarios.adicionarAcoes(document.getElementById('hdnIdDadosUsuario').value, "", false, true);
                    }
                },
                error: function (e) {
                    console.error('Erro ao processar o XML do SEI: ' + e.responseText);
                }
            });

            document.getElementById('hdnIdUsuario').value = '';
        }
    }


    function transportarUsuarioJuridico() {


        if (validarCamposObrigatoriosUsuario()) {

            var paramsAjax = {
                paramsBusca: document.getElementById('hdnIdDadosUsuario').value,
                paramsIdDocumento: document.getElementById('hdnIdDocumento').value
            };

            var tbUsuarios = document.getElementById('tblEnderecosEletronicos');

            for (var i = 1; i < tbUsuarios.rows.length; i++) {
                var razao = tbUsuarios.rows[i].cells[0].innerText;

                if (razao.trim() == document.getElementById('hdnIdDadosUsuario').value) {
                    alert("A pessoa jurídica informado já possui intimação gerada para este documento.");

                    return true;
                    break;
                }
            }

            $.ajax({

                url: '<?= $strLinkAjaxJuridicos ?>',
                type: 'POST',
                dataType: 'XML',
                data: paramsAjax,
                success: function (r) {

                    //tratando XSS
                    var nome = $(r).find('Nome').text();
                    var nomeTratado = nome.replace("<", "& lt;");

                    document.getElementById("tblEnderecosEletronicos").style.display = "";
                    document.getElementById("hiddeAll1").style.display = "";
                    document.getElementById("hiddeAll2").style.display = "";
                    document.getElementById("conteudoHide2").style.display = "";
                    document.getElementById("hiddeTable").style.display = "";

                    showOrHideClass('tabUsuario', '');
                    limparCamposUsuario();

                    var cpf = $(r).find('Cnpj').text();

                    var registroDuplicado = objTabelaDinamicaUsuarios.registroDuplicado(cpf);

                    if (registroDuplicado) {
                        alert("O usuário externo informado já possui intimação gerada para este documento.");
                        return false;
                    }

                    //Show Conteudo
                    document.getElementById('conteudoHide2').style.display = '';
                    document.getElementById('lblTipodeResposta').style.display = 'none';

                    var usuariosCadastro = document.getElementById('hdnDadosUsuario').value;

                    if ($(r).find('Intimacao').text() > 0) {
                        objTabelaDinamicaUsuarios.adicionar([$(r).find('Id').text(), $(r).find('Nome').text(), infraFormatarCnpj(cpf), $(r).find('DataIntimacao').text(), $(r).find('Situacao').text()]);
                    } else {
                        objTabelaDinamicaUsuarios.adicionar([$(r).find('Id').text(), nomeTratado, infraFormatarCnpj(cpf), infraDataAtual(), 'Em geração']);
                    }

                    if ($(r).find('Intimacao').text() > 0) {
                        objTabelaDinamicaUsuarios.adicionarAcoes(document.getElementById('hdnIdDadosUsuario').value,
                            "<a href='#' onclick=\"abrirIntimacaoCadastradaJuridico('" + $(r).find('Url').text() + "','valor')\"><img title='Consultar Destinatário' alt='Consultar Destinatário' src='/infra_css/imagens/consultar.gif' class='infraImg' /></a>",
                            false, false);
                        document.getElementById('hdnDadosUsuario').value = usuariosCadastro;
                    } else {
                        objTabelaDinamicaUsuarios.adicionarAcoes(document.getElementById('hdnIdDadosUsuario').value, "", false, true);
                    }
                },
                error: function (e) {

                    console.error('Erro ao processar o XML do SEI: ' + e.responseText);
                }
            });

            document.getElementById('hdnIdUsuario').value = '';
        }
    }


    function controlarShowHideTabDestinatario() {
        var divTabela = document.getElementById('divTabelaUsuarioExterno');
        var conteudoTela = document.getElementById('conteudoHide');
        var isAlteracao = document.getElementById('hdnIsAlterar');
        var tbUsuarios = document.getElementById('tblEnderecosEletronicos');
        var qtdLinhas = tbUsuarios.rows.length;
        var countInt = document.getElementById('hdnCountIntimacoes').value;

        if (isAlteracao.value == '1') {
            var totalLinhas = qtdLinhas - countInt;

            if (totalLinhas == '2') {
                limparCamposGerarIntimacao();
                conteudoTela.style.display = 'none';
            }

        } else {
            if (qtdLinhas == '2') {
                limparCamposGerarIntimacao();
                divTabela.style.display = 'none';
                conteudoTela.style.display = 'none';
            }
        }
    }

    function controlarShowHideTabDestinatarioJuridico() {

        var divTabela = document.getElementById('divTabelaUsuarioExterno');
        var conteudoTela = document.getElementById('conteudoHide2');
        var isAlteracao = document.getElementById('hdnIsAlterar');
        var tbUsuarios = document.getElementById('tblEnderecosEletronicos');
        var qtdLinhas = tbUsuarios.rows.length;
        var countInt = document.getElementById('hdnCountIntimacoes').value;

        if (isAlteracao.value == '1') {
            var totalLinhas = qtdLinhas - countInt;

            if (totalLinhas == '2') {
                limparCamposGerarIntimacao();
                conteudoTela.style.display = 'none';
            }

        } else {
            if (qtdLinhas == '2') {
                limparCamposGerarIntimacao();
                divTabela.style.display = 'none';
                conteudoTela.style.display = 'none';
            }
        }
    }


    function limparCamposGerarIntimacao() {

        //Sel Tipo de Intimação e Resposta
        var selTipoIntimacao = document.getElementById('selTipoIntimacao');
        selTipoIntimacao.value = '0';
        mostraTipoResposta(selTipoIntimacao);

        //Limpar Anexos
        var checkedAnexo = document.getElementById('optPossuiAnexo');
        checkedAnexo.checked = false;
        esconderAnexos(checkedAnexo);

        //Integral e parcial
        var optIntegral = document.getElementById('optIntegral');
        var optParcial = document.getElementById('optParcial');
        optIntegral.checked = false;
        optParcial.checked = false;

        mostrarProtocoloParcial(optIntegral);

        var objSelected = document.getElementById('selMainIntimacao');
        objSelected.innerHTML = '';
    }

    function validarCamposObrigatoriosUsuario() {
        if (document.getElementById('txtUsuario').value == "") {
            alert("Informe o Usuário Externo.");
            return false;
        } else {
            return true;
        }
    }

    function limparCamposUsuario() {
        document.getElementById('txtUsuario').value = '';
        document.getElementById('txtEmail').value = '';
        document.getElementById('txtUsuario').focus();
    }


    function abrirIntimacaoCadastrada(Url, id) {

        var tbUsuarios = document.getElementById('tblEnderecosEletronicos');
        var total = document.getElementById('hdnCountIntimacoes').value;
        for (var i = 0; i < total; i++) {

            document.getElementById("changeColor" + i).className = 'infraTrClara';
        }

        valor = infraAbrirJanela(Url, 'consultarIntimacao', 900, 900, '', false); //modal
        valor.onbeforeunload = function () {

            document.getElementById("changeColor" + id).className = 'infraTrAcessada';
        }

        return;


    }


    function abrirIntimacaoCadastradaJuridico(Url, id) {

        var tbUsuarios = document.getElementById('tblEnderecosEletronicos');
        var total = document.getElementById('hdnCountIntimacoes').value;
        for (var i = 0; i < total; i++) {

            document.getElementById("changeColorJuridico" + i).className = 'infraTrClara';
        }

        valor = infraAbrirJanela(Url, 'consultarIntimacao', 900, 900, '', false); //modal
        valor.onbeforeunload = function () {

            document.getElementById("changeColorJuridico" + id).className = 'infraTrAcessada';
        }

        return;

    }

    function removerMarcacoesLinha(nomeClass) {
        var objs = document.getElementsByClassName(nomeClass);

        for (var i = 0; i < objs.length; i++) {
            objs[i].className = nomeClass;
        }
    }


    function showOrHideClass(classe, opcao) {
        var elements = document.getElementsByClassName(classe);

        for (i = 0; i < elements.length; i++) {
            elements[i].style.display = opcao;
        }
    }


    function esconderAnexos(documento) {
        if (documento.checked == true) {
            document.getElementById('lblAnexosIntimacao').style.display = "";
            document.getElementById('selAnexosIntimacao').style.display = "";
            document.getElementById('imgLupaAnexos').style.display = "";
            document.getElementById('imgExcluirAnexos').style.display = "";
        } else {
            atualizarSelectedMultipleMain(true);
            document.getElementById('selAnexosIntimacao').innerHTML = '';
            document.getElementById('lblAnexosIntimacao').style.display = "none";
            document.getElementById('selAnexosIntimacao').style.display = "none";
            document.getElementById('imgLupaAnexos').style.display = "none";
            document.getElementById('imgExcluirAnexos').style.display = "none";
        }

        document.getElementById('hdnAnexosIntimacao').value = "";
        var selAnexosIntimacao = document.getElementById("selAnexosIntimacao");
        for (var x = 0; x < selAnexosIntimacao.length; x++) {
            selAnexosIntimacao.remove(x);
        }
    }

    function atualizarSelectedMultipleMain(anexo) {
        var idCampoSel = anexo ? 'selAnexosIntimacao' : 'selProtocolosDisponibilizados';
        var objSelected = document.getElementById(idCampoSel);

        var arrIds = new Array();

        var qtdOption = objSelected.options.length;

        if (qtdOption > 0) {
            for (var i = 0; i < qtdOption; i++) {
                var valueOption = objSelected.options[i].value;
                var remover = '#selMainIntimacao option[value="' + valueOption + '"]';
                $(remover).remove();
            }
        }
    }

    function mostrarProtocoloParcial(documento) {
        if (documento.value == 'P') {
            document.getElementById('optIntegral').checked = false;
            document.getElementById('lblProtocolosDisponibilizados').style.display = "";
            document.getElementById('selProtocolosDisponibilizados').style.display = "";
            document.getElementById('imgLupaProtocolos').style.display = "";
            document.getElementById('imgExcluirProtocolos').style.display = "";
        } else {
            atualizarSelectedMultipleMain(false);
            document.getElementById('optParcial').checked = false;
            document.getElementById('lblProtocolosDisponibilizados').style.display = "none";
            document.getElementById('selProtocolosDisponibilizados').style.display = "none";
            document.getElementById('imgLupaProtocolos').style.display = "none";
            document.getElementById('imgExcluirProtocolos').style.display = "none";
        }

        document.getElementById('hdnProtocolosDisponibilizados').value = "";
        var selAnexosIntimacao = document.getElementById("selProtocolosDisponibilizados");
        for (var x = 0; x < selAnexosIntimacao.length; x++) {
            selAnexosIntimacao.remove(x);
        }
    }

    function mostraTipoResposta(objSelTipoIntimacao) {

        removerAntigosElTpResposta();
        if (objSelTipoIntimacao.value != '0') {
            showOrHideTipoResposta('');
            realizarAjaxTipoResposta(objSelTipoIntimacao);
        } else {
            showOrHideTipoResposta('none');
        }
    }

    function removerAntigosElTpResposta() {
        var div = document.getElementById('divSelectTipoResposta');
        if (div) {
            div.innerHTML = '';
        }
    }

    function showOrHideTipoResposta(acao) {
        document.getElementById('divTipoResposta').style.display = acao;
        document.getElementById('lblTipodeResposta').style.display = acao;
        document.getElementById('divEspacoResposta').style.display = acao;
    }

    function realizarAjaxTipoResposta(objSelTipoIntimacao) {
        var paramsAjax = {
            paramsBusca: objSelTipoIntimacao.value
        };

        $.ajax({
            url: '<?= $strLinkAjaxBuscaTiposRespostaTipoIntimacao ?>',
            type: 'POST',
            dataType: 'XML',
            data: paramsAjax,
            success: function (r) {

                document.getElementById('hdnTipoIntimacao').value = $(r).find('TipoRespostaAceita').text();

                var ids = $(r).find('Ids').text();

                if (ids != '') {

                    var selectMultiple = document.createElement('select');
                    selectMultiple.id = 'selTipoResposta';
                    selectMultiple.name = 'selTipoResposta[]';
                    selectMultiple.className = 'infraSelect multipleSelect';
                    selectMultiple.multiple = '';
                    //  selectMultiple.style.width = '40%';

                    var id = ids.split("¥");

                    for (var x = 0; x < id.length; x++) {
                        // TODO: refatorar este trecho, para nao usar este delimitador '-#-', retornar em formato XML usando atributos ou subtags na tags Ids retornada pelo ajax
                        var montaCheck = id[x].split("±");

                        var opt = document.createElement('option');
                        opt.value = montaCheck[0];
                        opt.innerHTML = montaCheck[1];
                        selectMultiple.appendChild(opt);
                    }

                    var div = document.getElementById('divSelectTipoResposta');
                    div.appendChild(selectMultiple);

                    if (montaCheck[2] == 'E') {
                        tipoRespostaMultipla = false;
                    } else {
                        tipoRespostaMultipla = true;
                    }
                    if (tipoRespostaMultipla) {
                        configurarSelectMultiple();
                    } else {
                        configurarSelectSingle();
                    }
                    $("#selTipoResposta").multipleSelect('uncheckAll');
                    document.getElementById('lblTipodeResposta').style.display = '';

                } else {

                    document.getElementById('lblTipodeResposta').style.display = 'none';

                }

            },
            error: function (e) {
                console.error('Erro ao processar o XML do SEI: ' + e.responseText);
            }
        });
    }


    function configurarSelectSingle() {
        $("#selTipoResposta").multipleSelect({
            filter: false,
            single: true,
            minimumCountSelected: 1,
            selectAll: false,
        });
    }

    function configurarSelectMultiple() {
        $("#selTipoResposta").multipleSelect({
            filter: false,
            minimumCountSelected: 1,
            selectAll: false,
        });
    }

    function validarCadastro() {
        //valido Destinatários
        if (document.getElementById("hdnDadosUsuario").value == "") {
            alert("Insira um Destinatário.");
            document.getElementById("hdnDadosUsuario").focus();
            return false;
        }

        //Validar tipo de Intimação
        if (document.getElementById("selTipoIntimacao").value == 0) {
            alert("Selecione um Tipo de Intimação.");
            document.getElementById("selTipoIntimacao").focus();
            return false;
        }

        //Validar Tipo de Resposta

        if (document.getElementById("selTipoIntimacao").value != 0) {

            var selTpResp = document.getElementById('divSelectTipoResposta');
            var lis = selTpResp.getElementsByTagName('li');
            var tpRespPreenchido = false;
            var tpResposta = document.getElementById("hdnTipoIntimacao").value;

            for (var i = 0; i < lis.length; i++) {
                if (lis[i].className == 'selected') {
                    tpRespPreenchido = true;
                }
            }

            if (tpResposta != '<?= MdPetIntTipoIntimacaoRN::$SEM_RESPOSTA ?>' && !tpRespPreenchido) {
                alert("Selecione um Tipo de Resposta.");
                document.getElementsByClassName("ms-choice")[0].focus();
                return false;
            }

        }

        //Validar Anexos da Intimação
        if (document.getElementById("optPossuiAnexo").checked && document.getElementById("hdnAnexosIntimacao").value == "") {
            alert("Insira um Protocolo dos Anexos da Intimação.");
            document.getElementById("optPossuiAnexo").focus();
            return false;
        }

        //Valida se foi selecionado um Tipo de Acesso
        if (!document.getElementById("optParcial").checked && !document.getElementById("optIntegral").checked) {
            alert("Selecione um Tipo de Acesso.");
            document.getElementById("optIntegral").focus();
            return false;
        }

        validacoesAjax();

    }

    function retornarJsonIdsAnexos() {
        var selAnexo = document.getElementById('selAnexosIntimacao');
        var stringIds = '';

        if (selAnexo.options.length > 0) {
            for (i = 0; i < selAnexo.options.length; i++) {
                if (i != 0) {
                    stringIds += '_';
                }

                stringIds += selAnexo.options[i].value;
            }

        }

        return stringIds;
    }

    function validacoesAjax() {
        var tpAcesso = document.getElementById('optIntegral').checked ? document.getElementById('hdnStaAcessoIntegral').value : document.getElementById('hdnStaAcessoParcial').value;
        var documentosAnexos = retornarJsonIdsAnexos();

        var paramsAjax = {
            hdnDadosUsuario: document.getElementById('hdnDadosUsuario').value,
            tpAcessoSelecao: tpAcesso,
            idProcedimento: document.getElementById('hdnIdProcedimento').value,
            stringDocAnex: documentosAnexos,
            idDocumento: document.getElementById('hdnIdDocumento').value,
            tipoPessoa: document.getElementById('hdnTipoPessoa').value
        };


        $.ajax({
            url: '<?= $strLinkAjaxValidacoesSubmit ?>',
            type: 'POST',
            dataType: 'XML',
            data: paramsAjax,
            success: function (r) {
                console.log(r);
                var impeditivo = $(r).find('Impeditivo').text() == 'S';
                var alerta = $(r).find('Alerta').text() == 'S';
                var msg = $(r).find('Mensagem').text();


                if (impeditivo) {
                    alert(msg);
                    return false;
                } else if (alerta) {
                    if (confirm(msg)) {
                        confirmarTipoAcessoExterno();
                    } else {
                        return false;
                    }
                } else {
                    confirmarTipoAcessoExterno();
                }

            },
            error: function (e) {
                console.error('Erro ao processar o XML do SEI: ' + e.responseText);
            }
        });

        return false;
    }

    function carregarComponenteUsuarioExternoF() {
        objLupaTipoUsuarioExternoF = new infraLupaSelect('selTipoProcesso', 'hdnIdTipoProcesso', '<?= $strLinkTipoProcessoSelecaoF ?>');

        objLupaTipoUsuarioExternoF.finalizarSelecao = function () {
            var options = document.getElementById('selTipoProcesso').options;
            if (options.length < 1) {
                return;
            }
            for (var i = 0; i < options.length; i++) {
                options[i].selected = true;
            }
            objLupaTipoProcesso.atualizar();
        };

        objAutoCompletarTipoProcesso = new infraAjaxAutoCompletar('hdnIdTipoProcesso', 'txtTipoProcesso', '<?= $strLinkAjaxTipoProcesso ?>');
        objAutoCompletarTipoProcesso.limparCampo = false;

        objAutoCompletarTipoProcesso.prepararExecucao = function () {
            var itensSelecionados = '';
            var options = document.getElementById('selTipoProcesso').options;

            if (options.length > 0) {
                for (var i = 0; i < options.length; i++) {
                    itensSelecionados += '&itens_selecionados[]=' + options[i].value;
                }
            }
            return 'palavras_pesquisa=' + document.getElementById('txtTipoProcesso').value + '&' + itensSelecionados;
        };

        objAutoCompletarTipoProcesso.processarResultado = function (id, descricao, complemento) {
            if (id != '') {
                var options = document.getElementById('selTipoProcesso').options;

                for (var i = 0; i < options.length; i++) {
                    if (options[i].value == id) {
                        self.setTimeout('alert(\'Tipo de Processo [' + descricao + '] já consta na lista.\')', 100);
                        break;
                    }
                }

                if (i == options.length) {

                    for (i = 0; i < options.length; i++) {
                        options[i].selected = false;
                    }

                    opt = infraSelectAdicionarOption(document.getElementById('selTipoProcesso'), descricao, id);

                    objLupaTipoProcesso.atualizar();

                    opt.selected = true;
                }

                document.getElementById('txtTipoProcesso').value = '';
                document.getElementById('txtTipoProcesso').focus();
            }
        }
        objAutoCompletarTipoProcesso.selecionar('<?= $strIdTipoProcesso ?>', '<?= PaginaSEI::getInstance()->formatarParametrosJavascript(PaginaSEI::tratarHTML($strNomeRemetente)); ?>');
    }

    function preencherHiddenComponente(tpSelect) {

        var arrValues = getValuesOptionsSelected(tpSelect, false);
        var objHdn = tpSelect == 'A' ? document.getElementById('hdnIdsDocAnexo') : document.getElementById('hdnIdsDocDisponivel');

        var jsonArrValues = arrValues.length > 0 ? JSON.stringify(arrValues) : '';

        if (jsonArrValues != '') {
            objHdn.value = jsonArrValues;
        }

    }

    function confirmarTipoAcessoExterno() {

        if (document.getElementById('optIntegral').checked == true) {
            console.log('integral');
            <?
            $urlBase = ConfiguracaoSEI::getInstance()->getValor('SEI', 'URL');
            $strLinkIntegral = SessaoSEI::getInstance()->assinarLink($urlBase . '/controlador.php?acao=md_pet_intimacao_cadastro_confirmar&tipo=integral');
            ?>
            parent.infraAbrirJanelaModal('<? echo $strLinkIntegral; ?>', 900, 400);
        } else {
            console.log('parcial')
            <?
            $urlBase = ConfiguracaoSEI::getInstance()->getValor('SEI', 'URL');
            $strLinkParcial = SessaoSEI::getInstance()->assinarLink($urlBase . '/controlador.php?acao=md_pet_intimacao_cadastro_confirmar&tipo=parcial');
            ?>
            parent.infraAbrirJanelaModal('<? echo $strLinkParcial; ?>', 900, 400);
        }


    }

    function onSubmitForm() {

        if (document.getElementById('tipoPessoaFisica').checked == false && document.getElementById('tipoPessoaJuridica').checked == false) {
            alert("Selecione um tipo de Destinatário");
        }

        preencherHiddenComponente('A');
        preencherHiddenComponente('P');
        validarCadastro();

    }
</script>
