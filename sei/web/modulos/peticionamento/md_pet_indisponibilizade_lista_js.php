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
