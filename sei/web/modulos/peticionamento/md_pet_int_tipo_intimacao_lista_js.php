<script type="text/javascript">
    function inicializar() {
        if ('<?= $_GET['acao'] ?>' == 'md_pet_int_tipo_intimacao_selecionar') {
            infraReceberSelecao();
            document.getElementById('btnFecharSelecao').focus();
        } else {
            document.getElementById('btnFechar').focus();
        }

        infraEfeitoTabelas();
    }

    <? if ($bolAcaoDesativar) { ?>
    function acaoDesativar(id, desc) {
        desc = $("<pre>").html(desc).text();
        if (confirm("Confirma desativação do Tipo de Intimação Eletrônica  \"" + desc + "\"?")) {
            document.getElementById('hdnInfraItemId').value = id;
            document.getElementById('frmMdPetIntTipoIntimacaoLista').action = '<?= $strLinkDesativar ?>';
            document.getElementById('frmMdPetIntTipoIntimacaoLista').submit();
        }
    }

    function acaoDesativacaoMultipla() {
        if (document.getElementById('hdnInfraItensSelecionados').value == '') {
            alert('Nenhuma  selecionada.');
            return;
        }
        if (confirm("Confirma desativação ds  selecionads?")) {
            document.getElementById('hdnInfraItemId').value = '';
            document.getElementById('frmMdPetIntTipoIntimacaoLista').action = '<?= $strLinkDesativar ?>';
            document.getElementById('frmMdPetIntTipoIntimacaoLista').submit();
        }
    }
    <? } ?>

    <? if ($bolAcaoReativar){ ?>
    function acaoReativar(id, desc) {
        desc = $("<pre>").html(desc).text();
        if (confirm("Confirma reativação do Tipo de Intimação Eletrônica  \"" + desc + "\"?")) {
            document.getElementById('hdnInfraItemId').value = id;
            document.getElementById('frmMdPetIntTipoIntimacaoLista').action = '<?= $strLinkReativar ?>';
            document.getElementById('frmMdPetIntTipoIntimacaoLista').submit();
        }
    }

    function acaoReativacaoMultipla() {
        if (document.getElementById('hdnInfraItensSelecionados').value == '') {
            alert('Nenhuma  selecionada.');
            return;
        }
        if (confirm("Confirma reativação ds  selecionads?")) {
            document.getElementById('hdnInfraItemId').value = '';
            document.getElementById('frmMdPetIntTipoIntimacaoLista').action = '<?= $strLinkReativar ?>';
            document.getElementById('frmMdPetIntTipoIntimacaoLista').submit();
        }
    }
    <? } ?>

    <? if ($bolAcaoExcluir){ ?>
    function acaoExcluir(id, desc) {
        desc = $("<pre>").html(desc).text();
        if (confirm("Confirma exclusão do Tipo de Intimação Eletrônica \"" + desc + "\"?")) {
            document.getElementById('hdnInfraItemId').value = id;
            document.getElementById('frmMdPetIntTipoIntimacaoLista').action = '<?= $strLinkExcluir ?>';
            document.getElementById('frmMdPetIntTipoIntimacaoLista').submit();
        }
    }

    function acaoExclusaoMultipla() {
        if (document.getElementById('hdnInfraItensSelecionados').value == '') {
            alert('Nenhuma  selecionada.');
            return;
        }
        if (confirm("Confirma exclusão ds  selecionads?")) {
            document.getElementById('hdnInfraItemId').value = '';
            document.getElementById('frmMdPetIntTipoIntimacaoLista').action = '<?= $strLinkExcluir ?>';
            document.getElementById('frmMdPetIntTipoIntimacaoLista').submit();
        }
    }
    <? } ?>
</script>
