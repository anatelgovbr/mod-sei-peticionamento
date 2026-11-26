<?php
/**
 * ANATEL
 *
 * 03/09/2026 - criado por emerson_b.goncalves - ANATEL
 */
?>
function inicializar() {
    document.getElementById('<?= $strAcao === 'md_pet_pi_tp_processo_reativar' ? 'btnFecharSelecao' : 'btnFechar' ?>').focus();
    infraEfeitoTabelas();
}

function pesquisar() {
    document.getElementById('frmMdPetPiTpProcessoLista').action = '<?= $strLinkPesquisar ?>';
    document.getElementById('frmMdPetPiTpProcessoLista').submit();
}

<?php if ($bolAcaoDesativar) { ?>
function acaoDesativar(id, desc) {
    if (confirm('Confirma desativação do Tipo de Processo para Peticionamento Interno "' + desc + '"?')) {
        document.getElementById('hdnInfraItemId').value = id;
        document.getElementById('frmMdPetPiTpProcessoLista').action = '<?= $strLinkDesativar ?>';
        document.getElementById('frmMdPetPiTpProcessoLista').submit();
    }
}

function acaoDesativacaoMultipla() {
    if (document.getElementById('hdnInfraItensSelecionados').value === '') {
        alert('Nenhum Tipo de Processo selecionado.');
        return;
    }
    if (confirm('Confirma a desativação dos Tipos de Processo selecionados?')) {
        document.getElementById('hdnInfraItemId').value = '';
        document.getElementById('frmMdPetPiTpProcessoLista').action = '<?= $strLinkDesativar ?>';
        document.getElementById('frmMdPetPiTpProcessoLista').submit();
    }
}
<?php } ?>

<?php if ($bolAcaoReativar) { ?>
function acaoReativar(id, desc) {
    if (confirm('Confirma reativação do Tipo de Processo para Peticionamento Interno "' + desc + '"?')) {
        document.getElementById('hdnInfraItemId').value = id;
        document.getElementById('frmMdPetPiTpProcessoLista').action = '<?= $strLinkReativar ?>';
        document.getElementById('frmMdPetPiTpProcessoLista').submit();
    }
}

function acaoReativacaoMultipla() {
    if (document.getElementById('hdnInfraItensSelecionados').value === '') {
        alert('Nenhum Tipo de Processo selecionado.');
        return;
    }
    if (confirm('Confirma a reativação dos Tipos de Processo selecionados?')) {
        document.getElementById('hdnInfraItemId').value = '';
        document.getElementById('frmMdPetPiTpProcessoLista').action = '<?= $strLinkReativar ?>';
        document.getElementById('frmMdPetPiTpProcessoLista').submit();
    }
}
<?php } ?>

<?php if ($bolAcaoExcluir) { ?>
function acaoExcluir(id, desc) {
    if (confirm('Confirma exclusão do Tipo de Processo para Peticionamento Interno "' + desc + '"?')) {
        document.getElementById('hdnInfraItemId').value = id;
        document.getElementById('frmMdPetPiTpProcessoLista').action = '<?= $strLinkExcluir ?>';
        document.getElementById('frmMdPetPiTpProcessoLista').submit();
    }
}

function acaoExclusaoMultipla() {
    if (document.getElementById('hdnInfraItensSelecionados').value === '') {
        alert('Nenhum Tipo de Processo selecionado.');
        return;
    }
    if (confirm('Confirma a exclusão dos Tipos de Processo selecionados?')) {
        document.getElementById('hdnInfraItemId').value = '';
        document.getElementById('frmMdPetPiTpProcessoLista').action = '<?= $strLinkExcluir ?>';
        document.getElementById('frmMdPetPiTpProcessoLista').submit();
    }
}
<?php } ?>
