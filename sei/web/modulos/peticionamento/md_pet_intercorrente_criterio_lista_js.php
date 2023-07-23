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
        if (confirm("Confirma desativa��o do Crit�rio Intercorrente para Peticionamento \"" + desc + "\"?")) {
            document.getElementById('hdnInfraItemId').value = id;
            document.getElementById('frmLista').action = '<?=$strLinkDesativar?>';
            document.getElementById('frmLista').submit();
        }
    }

    function acaoDesativacaoMultipla() {
        if (document.getElementById('hdnInfraItensSelecionados').value == '') {
            alert('Nenhum Crit�rio Intercorrente selecionado.');
            return;
        }
        if (confirm("Confirma a desativa��o dos Crit�rios Intercorrentes selecionados?")) {
            document.getElementById('hdnInfraItemId').value = '';
            document.getElementById('frmLista').action = '<?=$strLinkDesativar?>';
            document.getElementById('frmLista').submit();
        }
    }

    function acaoReativar(id, desc) {
        if (confirm("Confirma reativa��o do Crit�rio Intercorrente para Peticionamento \"" + desc + "\"?")) {
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
        if (confirm("Confirma a reativa��o dos Crit�rios Intercorrentes selecionadas?")) {
            document.getElementById('hdnInfraItemId').value = '';
            document.getElementById('frmLista').action = '<?=$strLinkReativar?>';
            document.getElementById('frmLista').submit();
        }
    }

    function acaoExcluir(id, desc) {
        if (confirm("Confirma exclus�o do Crit�rio Intercorrente para Peticionamento \"" + desc + "\"?")) {
            document.getElementById('hdnInfraItemId').value = id;
            document.getElementById('frmLista').action = '<?=$strLinkExcluir?>';
            document.getElementById('frmLista').submit();
        }
    }

    function acaoExclusaoMultipla() {
        if (document.getElementById('hdnInfraItensSelecionados').value == '') {
            alert('Nenhuma Crit�rio Intercorrente selecionado.');
            return;
        }
        if (confirm("Confirma a exclus�o dos Crit�rios Intercorrentes selecionados?")) {
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