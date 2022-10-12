<script type="text/javascript">
    function inicializar() {
        <? if ($_GET['acao'] == 'md_pet_int_tipo_resp_cadastrar') { ?>
        document.getElementById('txtNome').focus();
        <? } else if ($_GET['acao'] == 'md_pet_int_tipo_resp_consultar') { ?>
        infraDesabilitarCamposAreaDados();
        <? } else { ?>
        document.getElementById('btnCancelar').focus();
        <? } ?>

        <? if ($objMdPetIntTipoRespDTO->getStrTipoPrazoExterno() != 'D') { ?>
        document.getElementById('txtValorPrazoExternoDia').style.display = "none";
        document.getElementById('spnTipoDias').style.display = "none";
        <? }
        if ($objMdPetIntTipoRespDTO->getStrTipoPrazoExterno() != 'M') { ?>
        document.getElementById('txtValorPrazoExternoMes').style.display = "none";
        <? }
        if ($objMdPetIntTipoRespDTO->getStrTipoPrazoExterno() != 'A') { ?>
        document.getElementById('txtValorPrazoExternoAno').style.display = "none";
        <? } ?>
    }

    function verificaPrazo(id) {
        document.getElementById('txtValorPrazoExternoDia').style.display = "none";
        document.getElementById('txtValorPrazoExternoMes').style.display = "none";
        document.getElementById('txtValorPrazoExternoAno').style.display = "none";
        document.getElementById('spnTipoDias').style.display = "none";
        if (id != 'D') {
            document.getElementById('rdTipoDiaU').checked = false;
            document.getElementById('rdTipoDiaC').checked = false;
        }
        if (id == 'N') {
            document.getElementById('optTipoRespostaFacultativa').checked = true;
            document.getElementById('optTipoRespostaExige').disabled = true;
        } else {
            document.getElementById('optTipoRespostaFacultativa').disabled = false;
            document.getElementById('optTipoRespostaExige').disabled = false;
            document.getElementById('optTipoRespostaFacultativa').checked = false;
            if (id == 'D') {
                document.getElementById('rdTipoDiaC').checked = true;
                document.getElementById('txtValorPrazoExternoDia').style.display = "block";
                document.getElementById('spnTipoDias').style.display = "block";
                document.getElementById('txtValorPrazoExternoDia').value = "";
            } else if (id == 'M') {
                document.getElementById('txtValorPrazoExternoMes').style.display = "block";
                document.getElementById('txtValorPrazoExternoMes').value = "";
            } else if (id == 'A') {
                document.getElementById('txtValorPrazoExternoAno').style.display = "block";
                document.getElementById('txtValorPrazoExternoAno').value = "";
            }
        }
    }
</script>