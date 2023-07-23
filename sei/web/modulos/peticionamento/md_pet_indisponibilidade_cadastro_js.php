<script type="text/javascript">

    var objTabelaDocumento = null;

    //funcao para visualizar estruturas complexas (arrays, objetos) em JS
    function dump(arr, level) {

        var dumped_text = "";

        if (!level) level = 0;

        //The padding given at the beginning of the line.
        var level_padding = "";
        for (var j = 0; j < level + 1; j++) level_padding += "    ";

        if (typeof (arr) == 'object') { //Array/Hashes/Objects
            for (var item in arr) {
                var value = arr[item];

                if (typeof (value) == 'object') { //If it is an array,
                    dumped_text += level_padding + "'" + item + "' ...\n";
                    dumped_text += dump(value, level + 1);
                } else {
                    dumped_text += level_padding + "'" + item + "' => \"" + value + "\"\n";
                }
            }
        } else { //Stings/Chars/Numbers etc.
            dumped_text = "===>" + arr + "<===(" + typeof (arr) + ")";
        }
        return dumped_text;
    }

    function removerValidacaoDocumento() {
        document.getElementById('txtTipo').value = '';
        document.getElementById('btnAdicionar').style.display = 'none';
    }


    function inicializarGrid() {
        objTabelaDocumento = new infraTabelaDinamica('tbDocumento', 'hdnTbDocumento', true, true);
        objTabelaDocumento.gerarEfeitoTabela = true;

        objTabelaDocumento.procuraLinha = function (id) {
            var qtd;
            var linha;
            qtd = document.getElementById('tbDocumento').rows.length;

            for (i = 1; i < qtd; i++) {
                linha = document.getElementById('tbDocumento').rows[i];
                var valorLinha = $.trim(linha.cells[0].innerText);
                if (id) {
                    id = $.trim(id.value);
                    if (valorLinha == id) {
                        return i;
                    }
                } else {
                    return i;
                }
            }
            return null;
        };

        objTabelaDocumento.alterar = function (arr) {
            document.getElementById('hdnPkTabela').value = arr[0];
            document.getElementById('hdnIdDocumento').value = arr[1];
            document.getElementById('txtTipo').value = document.getElementById('hdnTipoDocumentoGrid').value;
            document.getElementById('hdnDataAtualGrid').value = arr[4];
            document.getElementById('hdnUnidadeAtualGrid').value = arr[3];
            document.getElementById('txtNumeroSei').value = document.getElementById('hdnNumDocumentoGrid').value;
            document.getElementById('hdnIsAlteracaoGrid').value = 1;
            document.getElementById('btnAdicionar').style.display = '';
        };

        objTabelaDocumento.remover = function () {
            document.getElementById('hdnIsAlteracaoGrid').value = 0;
            document.getElementById('hdnDadosAlterados').value = 1;
            document.getElementById('hdnIdDocumento').value = '';

            var qtd = document.getElementById('tbDocumento').rows.length;
            if (qtd == 2) {
                document.getElementById('tbDocumento').style.display = 'none';
            }

            return true;
        };

    }

    function controlarNumeroSEI() {
        var numeroSEI = $.trim(document.getElementById('txtNumeroSei').value);
        var qtd = document.getElementById('tbDocumento').rows.length;
        var isAlteracao = document.getElementById('hdnIsAlteracaoGrid').value == 1;

        if (numeroSEI == '') {
            alert('Preencha o Número SEI.');
            return false;
        } else if (qtd == 2 && !isAlteracao) {
            alert('Para adicionar um novo documento remova o que estava vinculado anteriormente, cada indisponibilidade deve possuir somente um documento.');
            return false;
        } else {
            validarNumeroSEI();
        }


    }


    function validarNumeroSEI() {

        objAjax = new infraAjaxComplementar(null, '<?=$strLinkAjaxValidacoesNumeroSEI?>');
        objAjax.limparCampo = false;
        objAjax.mostrarAviso = false;
        objAjax.tempoAviso = 1000;
        objAjax.async = false;

        objAjax.prepararExecucao = function () {
            var numeroSEI = document.getElementById('txtNumeroSei').value
            return 'numeroSEI=' + numeroSEI;
        };

        objAjax.processarResultado = function (arr) {
            //verifica se o documento foi assinado.
            if ('Assinatura' in arr) {
                alert(arr['Assinatura']);
                return false;
            } else {

                document.getElementById('hdnIdDocumento').value = arr['IdProtocolo'];
                document.getElementById('txtTipo').value = arr['Identificacao'];
                document.getElementById('hdnDataAtualGrid').value = arr['DataAtual'];
                document.getElementById('hdnUnidadeAtualGrid').value = arr['UnidadeAtual'];
                document.getElementById('txtNumeroSei').value = arr['ProtocoloFormatado'];
                document.getElementById('hdnTipoDocumentoGrid').value = arr['Identificacao'];
                document.getElementById('hdnNumDocumentoGrid').value = arr['ProtocoloFormatado'];
                document.getElementById('btnAdicionar').style.display = '';
                document.getElementById('hdnUrlDocumento').value = arr['UrlDocumento'];
            }
        };

        objAjax.executar();
    }


    function addLinkExibicaoDocumento(nomeDoc, tituloDoc) {
        var url = document.getElementById('hdnUrlDocumento').value

        var strLink = "window.open('" + url + "')";
        var html = '<a title="' + tituloDoc + '" style="font-size:12.4px" class="ancoraPadraoAzul" onclick ="' + strLink + '"> ' + nomeDoc + ' </a>';

        return html;
    }

    function adicionarDocumento() {

        var isVazioTipo = $.trim(document.getElementById('txtTipo').value) == '';

        if (isVazioTipo) {
            alert('É necessário Validar o número SEI antes de adicioná-lo.');
        } else {
            var nomeDocCompleto = document.getElementById('txtTipo').value + ' (' + document.getElementById('txtNumeroSei').value + ')';
            var htmlDoc = addLinkExibicaoDocumento(nomeDocCompleto, document.getElementById('txtTipo').value);
            var addBranco;

            var arrLinha = [
                document.getElementById('hdnPkTabela').value,
                document.getElementById('hdnIdDocumento').value,
                addBranco,
                document.getElementById('hdnUnidadeAtualGrid').value,
                document.getElementById('hdnDataAtualGrid').value,
            ];

            objTabelaDocumento.recarregar();
            objTabelaDocumento.adicionar(arrLinha);

            document.getElementById('tbDocumento').rows[1].cells[2].innerHTML = '<div id="divTbDocumento" style="text-align:center;">' + htmlDoc + '</div>';
            document.getElementById('hdnDadosAlterados').value = '1';
            document.getElementById('tbDocumento').style.display = '';
            document.getElementById('txtTipo').value = '';
            document.getElementById('txtNumeroSei').value = '';
            document.getElementById('btnAdicionar').style.display = 'none';
            document.getElementById('hdnIsAlteracaoGrid').value = 0;
        }

    }


    function inicializar() {

        inicializarGrid();

        if ('<?=$_GET['acao']?>' == 'md_pet_indisponibilidade_cadastrar' || '<?=$_GET['acao']?>' == 'md_pet_indisponibilidade_alterar') {
            document.getElementById('txtDtInicio').focus();
        } else if ('<?=$_GET['acao']?>' == 'md_pet_indisponibilidade_consultar') {
            infraDesabilitarCamposAreaDados();
        } else {
            document.getElementById('btnCancelar').focus();
        }

        infraEfeitoTabelas();
    }

    function validarPeriodoIndisp(dataInicio, dataFim) {

        msg = '';
        // #EU4864 - INFRAAJAX - não encontrado método que retorna somente dados, sem componentes
        $.ajax({
            type: "POST",
            url: "<?= $strUrlAjaxValidacaoPeridoDta ?>",
            dataType: "xml",
            async: false,
            data: {
                dataInicio: dataInicio,
                dataFim: dataFim
            },
            success: function (result) {
                msg = $(result).find('validacao').text();
            },
            error: function (msgError) {
                msgCommit = "Erro ao processar o XML do SEI: " + msgError.responseText;
            },
            complete: function (result) {

            }
        });

        return msg;
    }


    function validarDataInicialMaiorQueFinal() {
        var dataInicial = returnDateTime(document.getElementById('txtDtInicio').value);
        var dataFinal = returnDateTime(document.getElementById('txtDtFim').value);
        var valido = (dataInicial.getTime() <= dataFinal.getTime());

        if (!valido) {
            alert('A Data/Hora Inicio deve ser menor  que a Data/Hora Fim');
            return false;
        }

        return true;

    }


    function returnDateTime(valor) {

        valorArray = valor != '' ? valor.split(" ") : '';

        if (Array.isArray(valorArray)) {
            var data = valorArray[0]
            data = data.split('/');
            var mes = parseInt(data[1]) - 1;
            var horas = valorArray[1].split(':');

            var segundos = typeof horas[2] != 'undefined' ? horas[2] : 00;
            var dataCompleta = new Date(data[2], mes, data[0], horas[0], horas[1], segundos);
            return dataCompleta;
        }

        return false;
    }


    function validarCadastro() {
        preencherHdnProrrogacao();

        var campoDtIni = document.getElementById('txtDtInicio');
        var tamanhoDataInicio = parseInt((campoDtIni.value).length);
        var campoDtFim = document.getElementById('txtDtFim');
        var tamanhoDataFim = parseInt((campoDtFim.value).length);
        var numeroSei = document.getElementById('txtNumeroSei').value.trim();
        var tipoDocumento = document.getElementById('txtTipo').value.trim();
        var qtdDocAdicionado = document.getElementById('tbDocumento').rows.length;
        var dataFim = campoDtFim.value;

        if (infraTrim(document.getElementById('txtDtInicio').value) == '') {
            alert('Informe o Início.');
            document.getElementById('txtDtInicio').focus();
            return false;
        }

        if (infraTrim(document.getElementById('txtDtFim').value) == '') {
            alert('Informe o Fim.');
            document.getElementById('txtDtFim').focus();
            return false;
        }

        if (infraTrim(document.getElementById('txtResumoIndisponibilidade').value) == '') {
            alert('Informe o Resumo da Indisponibilidade.');
            document.getElementById('txtResumoIndisponibilidade').focus();
            return false;
        }

        var prorrogacao = document.getElementsByName('rdProrrogacao[]')[0].checked;
        if (!prorrogacao) {
            prorrogacao = document.getElementsByName('rdProrrogacao[]')[1].checked;
        }

        if (!prorrogacao) {
            alert('Informe se a Indisponibilidade justifica prorrogação automática dos prazos.');
            document.getElementById('rdProrrogacaoSim').focus();
            return false;
        }

        //Validar Datas
        var dataInicial = returnDateTime(document.getElementById('txtDtInicio').value);
        var dataFinal = returnDateTime(document.getElementById('txtDtFim').value);

        var valido = (dataInicial.getTime() < dataFinal.getTime());

        if (!valido) {
            alert('A Data/Hora Inicio deve ser menor que a Data/Hora Fim');
            return false;
        }


        if (tamanhoDataInicio < 16 || tamanhoDataInicio === 18) {
            alert('Data/Hora Inválida');
            document.getElementById('txtDtInicio').focus();
            document.getElementById('txtDtInicio').value = '';
            return false;
        }

        if (tamanhoDataFim < 16 || tamanhoDataFim === 18) {
            alert('Data/Hora Inválida');
            document.getElementById('txtDtFim').focus();
            document.getElementById('txtDtFim').value = '';
            return false;
        }

        // Validar Documento

        if (qtdDocAdicionado < 2 && tipoDocumento == '') {
            document.getElementById('hdnIdDocumento').value = '';
        }


        if (tipoDocumento != '' && qtdDocAdicionado < 2) {
            alert('O documento informado no número SEI não foi adicionado e não será salvo.Caso seja necessário vincular o documento é preciso antes adicioná-lo.');
            return false;
        }


        var dataAtual = new Date();
        var validoDtFutura = (dataFinal.getTime() < dataAtual.getTime());

        if (!validoDtFutura) {
            alert('Não é permitido o cadastro de Indisponibilidades Programadas.');
            return false;
        }


        return true;

    }

    function preencherHdnProrrogacao() {
        var rdProrrogacao = document.getElementsByName('rdProrrogacao[]')[0].checked ? 'S' : '';

        if (rdProrrogacao == '') {
            rdProrrogacao = document.getElementsByName('rdProrrogacao[]') [1].checked ? 'N' : '';
        }

        document.getElementById('hdnSinProrrogacao').value = rdProrrogacao;
    }


    function validDate(valor) {

        var campo = (valor === 'I') ? document.getElementById('txtDtInicio') : document.getElementById('txtDtFim');
        var tamanhoCampo = parseInt((campo.value).length);

        if (tamanhoCampo < 16 || tamanhoCampo === 18) {
            campo.focus();
            campo.value = "";
            alert('Data/Hora Inválida');
            return false;
        }

        var datetime = (campo.value).split(" ");
        var date = datetime[0];

        var ardt = new Array;
        var ExpReg = new RegExp("(0[1-9]|[12][0-9]|3[01])/(0[1-9]|1[012])/[12][0-9]{3}");
        ardt = date.split("/");
        erro = false;
        if (date.search(ExpReg) == -1) {
            erro = true;
        } else if (((ardt[1] == 4) || (ardt[1] == 6) || (ardt[1] == 9) || (ardt[1] == 11)) && (ardt[0] > 30)) {
            erro = true;
        } else if (ardt[1] == 2) {
            if ((ardt[0] > 28) && ((ardt[2] % 4) != 0))
                erro = true;
            if ((ardt[0] > 29) && ((ardt[2] % 4) == 0))
                erro = true;
        }

        if (erro) {
            alert("Data/Hora Inválida");
            campo.focus();
            campo.value = "";
            return false;
        } else {

            var arrayHoras = datetime[1].split(':')
            var horas = arrayHoras[0];
            var minutos = arrayHoras[1];
            var segundos = arrayHoras[2];

            if (horas > 23 || minutos > 59 || segundos > 59) {
                alert('Data/Hora Inválida');
                campo.focus();
                campo.value = "";
                return false
            }

        }

        if (document.getElementById('txtDtInicio').value != '' && document.getElementById('txtDtFim').value != '') {
            var dataInicial = returnDateTime(document.getElementById('txtDtInicio').value);
            var dataFinal = returnDateTime(document.getElementById('txtDtFim').value);
            var valido = (dataInicial.getTime() <= dataFinal.getTime());

            if (!valido) {
                document.getElementById('txtDtInicio').value = '';
                document.getElementById('txtDtFim').value = '';
                alert('A Data/Hora Inicio deve ser menor que a Data/Hora Fim');
                return false;
            }
        }

        return true;
    }

    function OnSubmitForm() {
        var prorrogacaoSim = document.getElementsByName('rdProrrogacao[]')[0].checked;
        var isSalvoAnteriormente =  <?php echo $isDisabled ?> ==
        '1';
        var campoDtIni = document.getElementById('txtDtInicio').value;
        var campoDtFim = document.getElementById('txtDtFim').value;


        if (validarCadastro()) {

            if (prorrogacaoSim && !isSalvoAnteriormente) {
                var msg = 'Ao marcar que a presente indisponibilidade justifica a prorrogação automática dos prazos, as Intimações Eletrônicas que venceriam durante o período da indisponibilidade terão seus Prazos Externos prorrogados para o primeiro dia útil seguinte ao fim da respectiva indisponibilidade. E uma vez salva a indisponibilidade com a opção SIM selecionada no campo citado, NÃO será possível alterar o campo de SIM para NÃO e também não será possível alterar o Período de Indisponibilidade informado.\n\nConfirma a prorrogação automática dos prazos?';
                periodoExistente = validarPeriodoIndisp(campoDtIni, campoDtFim);

                if (periodoExistente == 'N') {
                    if (confirm(msg)) {
                        return true;
                    } else {
                        return false;
                    }
                } else {
                    alert('Já existe uma indisponibilidade gerada nesse período estabelecido.');
                    return false;
                }

            }

            return true;

        } else {
            return false;
        }
    }

</script>