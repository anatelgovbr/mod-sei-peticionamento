<script type="text/javascript">
    function inicializar() {
        if ('<?=$_GET['acao']?>' == 'tipo_processo_peticionamento_selecionar') {
            infraReceberSelecao();
            document.getElementById('btnFecharSelecao').focus();
        } else {
            document.getElementById('btnFechar').focus();
        }
        infraEfeitoTabelas();
    }

    function acaoDesativar(id, desc) {
        if (confirm("Confirma desativação do Critério Intercorrente para Peticionamento \"" + desc + "\"?")) {
            document.getElementById('hdnInfraItemId').value = id;
            document.getElementById('frmLista').action = '<?=$strLinkDesativar?>';
            document.getElementById('frmLista').submit();
        }
    }

    function acaoDesativacaoMultipla() {
        if (document.getElementById('hdnInfraItensSelecionados').value == '') {
            alert('Nenhum Critério Intercorrente selecionado.');
            return;
        }
        if (confirm("Confirma a desativação dos Critérios Intercorrentes selecionados?")) {
            document.getElementById('hdnInfraItemId').value = '';
            document.getElementById('frmLista').action = '<?=$strLinkDesativar?>';
            document.getElementById('frmLista').submit();
        }
    }

    function acaoReativar(id, desc) {
        if (confirm("Confirma reativação do Critério Intercorrente para Peticionamento \"" + desc + "\"?")) {
            document.getElementById('hdnInfraItemId').value = id;
            document.getElementById('frmLista').action = '<?=$strLinkReativar?>';
            document.getElementById('frmLista').submit();
        }
    }

    function acaoReativacaoMultipla() {
        if (document.getElementById('hdnInfraItensSelecionados').value == '') {
            alert('Nenhum Tipo de Processo selecionado.');
            return;
        }
        if (confirm("Confirma a reativação dos Critérios Intercorrentes selecionadas?")) {
            document.getElementById('hdnInfraItemId').value = '';
            document.getElementById('frmLista').action = '<?=$strLinkReativar?>';
            document.getElementById('frmLista').submit();
        }
    }

    function acaoExcluir(id, desc) {
        if (confirm("Confirma exclusão do Critério Intercorrente para Peticionamento \"" + desc + "\"?")) {
            document.getElementById('hdnInfraItemId').value = id;
            document.getElementById('frmLista').action = '<?=$strLinkExcluir?>';
            document.getElementById('frmLista').submit();
        }
    }

    function acaoExclusaoMultipla() {
        if (document.getElementById('hdnInfraItensSelecionados').value == '') {
            alert('Nenhuma Critério Intercorrente selecionado.');
            return;
        }
        if (confirm("Confirma a exclusão dos Critérios Intercorrentes selecionados?")) {
            document.getElementById('hdnInfraItemId').value = '';
            document.getElementById('frmLista').action = '<?=$strLinkExcluir?>';
            document.getElementById('frmLista').submit();
        }
    }

    function pesquisar() {
        document.getElementById('frmLista').action = '<?=$strLinkPesquisar?>';
        document.getElementById('frmLista').submit();
    }
</script>