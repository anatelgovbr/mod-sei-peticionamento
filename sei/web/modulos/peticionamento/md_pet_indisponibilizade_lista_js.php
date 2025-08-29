<script type="text/javascript">

    function inicializar() {
        if ('<?=$_GET['acao']?>' == 'indisponibilidade_litigioso_selecionar') {
            infraReceberSelecao();
            document.getElementById('btnFecharSelecao').focus();
        } else {
            document.getElementById('btnFechar').focus();
        }
        infraEfeitoTabelas();
    }


    function validDate(valor) {

        var campo = (valor === 'I') ? document.getElementById('txtDtInicio') : campo = document.getElementById('txtDtFim');
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

            var valido = (dataInicial.getTime() < dataFinal.getTime());

            if (!valido) {
                document.getElementById('txtDtInicio').value = '';
                document.getElementById('txtDtFim').value = '';
                alert('A Data/Hora Inicio deve ser menor que a Data/Hora Fim');
                return false;
            }
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

            var segundos = typeof horas[2] != 'undefined' ? horas[2] : '00';
            var dataCompleta = new Date(data[2], mes, data[0], horas[0], horas[1], segundos);
            return dataCompleta;
        }

        return false;
    }


    <? if ($bolAcaoDesativar){ ?>
    function acaoDesativar(id, dataInicio, dataFim) {
        if (confirm("Confirma desativação da Indisponibilidade referente ao período \"" + dataInicio + " a " + dataFim + "\"?")) {
            document.getElementById('hdnInfraItemId').value = id;
            document.getElementById('frmIndisponibilidadePeticionamentoLista').action = '<?=$strLinkDesativar?>';
            document.getElementById('frmIndisponibilidadePeticionamentoLista').submit();
        }
    }

    function acaoDesativacaoMultipla() {
        if (document.getElementById('hdnInfraItensSelecionados').value == '') {
            alert('Nenhuma Indisponibilidade selecionada.');
            return;
        }
        if (confirm("Confirma a desativação das Indisponibilidades selecionadas?")) {
            document.getElementById('hdnInfraItemId').value = '';
            document.getElementById('frmIndisponibilidadePeticionamentoLista').action = '<?=$strLinkDesativar?>';
            document.getElementById('frmIndisponibilidadePeticionamentoLista').submit();
        }
    }
    <? } ?>

    function acaoReativar(id, dataInicio, dataFim) {
        if (confirm("Confirma reativação da Indisponibilidade referente ao período \"" + dataInicio + " a " + dataFim + "\"?")) {
            document.getElementById('hdnInfraItemId').value = id;
            document.getElementById('frmIndisponibilidadePeticionamentoLista').action = '<?=$strLinkReativar?>';
            document.getElementById('frmIndisponibilidadePeticionamentoLista').submit();
        }
    }

    function acaoReativacaoMultipla() {
        if (document.getElementById('hdnInfraItensSelecionados').value == '') {
            alert('Nenhuma Indisponibilidade selecionada.');
            return;
        }
        if (confirm("Confirma a reativação das Indisponibilidades selecionadas?")) {
            document.getElementById('hdnInfraItemId').value = '';
            document.getElementById('frmIndisponibilidadePeticionamentoLista').action = '<?=$strLinkReativar?>';
            document.getElementById('frmIndisponibilidadePeticionamentoLista').submit();
        }
    }

    <? if ($bolAcaoExcluir){ ?>
    function acaoExcluir(id, dataInicio, dataFim, prorrogacao) {

        if (prorrogacao === 'S') {
            alert('A exclusão da Indisponibilidade não é permitida, pois a indisponibilidade justificou prorrogação automática de prazos.');
        } else if (confirm("Confirma exclusão da Indisponibilidade referente ao período \"" + dataInicio + " a " + dataFim + "\"?")) {
            document.getElementById('hdnInfraItemId').value = id;
            document.getElementById('frmIndisponibilidadePeticionamentoLista').action = '<?=$strLinkExcluir?>';
            document.getElementById('frmIndisponibilidadePeticionamentoLista').submit();
        }

    }

    function acaoExclusaoMultipla() {
        if (document.getElementById('hdnInfraItensSelecionados').value == '') {
            alert('Nenhuma Indisponibilidade selecionada.');
            return;
        }
        if (confirm("Confirma a exclusão das Indisponibilidades selecionadas?")) {
            document.getElementById('hdnInfraItemId').value = '';
            document.getElementById('frmIndisponibilidadePeticionamentoLista').action = '<?=$strLinkExcluir?>';
            document.getElementById('frmIndisponibilidadePeticionamentoLista').submit();
        }
    }

    <? } ?>

    function pesquisar() {
        document.getElementById('frmIndisponibilidadePeticionamentoLista').action = '<?=$strLinkPesquisar?>';
        document.getElementById('frmIndisponibilidadePeticionamentoLista').submit();
    }


</script>
