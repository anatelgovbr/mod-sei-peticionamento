<script type="text/javascript">
	// 26/08/2024 - Atualização por gabrielg.colab - SPASSU
    function inicializar() {
        <? if ($_GET['acao'] == 'md_pet_int_tipo_resp_cadastrar') { ?>
        document.getElementById('txtNome').focus();
        <? } else if ($_GET['acao'] == 'md_pet_int_tipo_resp_consultar') { ?>
        infraDesabilitarCamposAreaDados();
        <? } else { ?>
        document.getElementById('btnCancelar').focus();
        <? } ?>

        <? if (isset($objMdPetIntTipoRespDTO) && $objMdPetIntTipoRespDTO->getStrTipoPrazoExterno() != 'D') { ?>
        document.getElementById('txtValorPrazoExternoDia').style.display = "none";
        document.getElementById('spnTipoDias').style.display = "none";
        <? }
        if (isset($objMdPetIntTipoRespDTO) && $objMdPetIntTipoRespDTO->getStrTipoPrazoExterno() != 'M') { ?>
        document.getElementById('txtValorPrazoExternoMes').style.display = "none";
        <? }
        if (isset($objMdPetIntTipoRespDTO) && $objMdPetIntTipoRespDTO->getStrTipoPrazoExterno() != 'A') { ?>
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

    function OnSubmitForm(){

        let txtNome = document.getElementById('txtNome').value.trim();
        if (txtNome.length < 3) {
            alert("O nome do Tipo de Resposta deve ter pelo menos 3 caracteres.");
            return;
        }

        let rdoSelecionado = document.querySelector('input[name="rdoPrazo"]:checked');
        if (!rdoSelecionado) {
            alert("Selecione um tipo de prazo (Dia, Mês ou Ano).");
            return;
        }

        let campoId = "";
        if (rdoSelecionado.value === "D") campoId = "txtValorPrazoExternoDia";
        if (rdoSelecionado.value === "M") campoId = "txtValorPrazoExternoMes";
        if (rdoSelecionado.value === "A") campoId = "txtValorPrazoExternoAno";

        let valorCampo = document.getElementById(campoId).value.trim();

        let numero = Number(valorCampo);

        if (!Number.isInteger(numero) || numero <= 0) {
            alert("Digite um número inteiro positivo maior ou igual a 1 no campo Prazo Externo.");
            return;
        }

    }
</script>