<script type="text/javascript">
    function inicializar() {
        if ('<?=$_GET['acao']?>' == 'md_pet_tipo_poder_listar') {
            infraReceberSelecao();

        } else {

        }
        infraEfeitoTabelas();
    }


    function acaoDesativar(id, desc) {
        if (confirm("Confirma desativação do Tipo de Poder Legal \"" + desc + "\"?")) {
            document.getElementById('hdnInfraItemId').value = id;
            document.getElementById('frmLista').action = '<?=$strLinkDesativar?>';
            document.getElementById('frmLista').submit();
        }
    }


    function acaoReativar(id, desc) {
        if (confirm("Confirma reativação do Tipo de Poder Legal \"" + desc + "\"?")) {
            document.getElementById('hdnInfraItemId').value = id;
            document.getElementById('frmLista').action = '<?=$strLinkReativar?>';
            document.getElementById('frmLista').submit();
        }
    }


    function acaoExcluir(id, desc) {
        if (confirm("Confirma exclusão do Tipo de Poder Legal \"" + desc + "\"?")) {
            document.getElementById('hdnInfraItemId').value = id;
            document.getElementById('frmLista').action = '<?=$strLinkExcluir?>';
            document.getElementById('frmLista').submit();
        }
    }


    function pesquisar() {
        document.getElementById('frmLista').action = '<?=$strLinkPesquisar?>';
        document.getElementById('frmLista').submit();
    }
</script>
