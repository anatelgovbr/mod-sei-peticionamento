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
        if (confirm("Confirma desativa��o do Tipo de Intima��o Eletr�nica  \"" + desc + "\"?")) {
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
        if (confirm("Confirma desativa��o ds  selecionads?")) {
            document.getElementById('hdnInfraItemId').value = '';
            document.getElementById('frmMdPetIntTipoIntimacaoLista').action = '<?= $strLinkDesativar ?>';
            document.getElementById('frmMdPetIntTipoIntimacaoLista').submit();
        }
    }
    <? } ?>

    <? if ($bolAcaoReativar){ ?>
    function acaoReativar(id, desc) {
        desc = $("<pre>").html(desc).text();
        if (confirm("Confirma reativa��o do Tipo de Intima��o Eletr�nica  \"" + desc + "\"?")) {
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
        if (confirm("Confirma reativa��o ds  selecionads?")) {
            document.getElementById('hdnInfraItemId').value = '';
            document.getElementById('frmMdPetIntTipoIntimacaoLista').action = '<?= $strLinkReativar ?>';
            document.getElementById('frmMdPetIntTipoIntimacaoLista').submit();
        }
    }
    <? } ?>

    <? if ($bolAcaoExcluir){ ?>
    function acaoExcluir(id, desc) {
        desc = $("<pre>").html(desc).text();
        if (confirm("Confirma exclus�o do Tipo de Intima��o Eletr�nica \"" + desc + "\"?")) {
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
        if (confirm("Confirma exclus�o ds  selecionads?")) {
            document.getElementById('hdnInfraItemId').value = '';
            document.getElementById('frmMdPetIntTipoIntimacaoLista').action = '<?= $strLinkExcluir ?>';
            document.getElementById('frmMdPetIntTipoIntimacaoLista').submit();
        }
    }
    <? } ?>
</script>
