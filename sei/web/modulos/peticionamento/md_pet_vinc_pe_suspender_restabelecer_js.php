<script>
    function inicializar() {
    }

    function salvar() {
        if(document.getElementById('txtCnpj').value.trim().length == 0){
            alert('CNPJ n�o informado.');
            return false;
        }
        if(document.getElementById('txtRazaoSocial').value.trim().length == 0){
            alert('Raz�o Social n�o informado.');
            return false;
        }
        if(document.getElementById('txtCpf').value.trim().length == 0){
            alert('CPF do Respons�vel Legal Cadastrado n�o informado.');
            return false;
        }
        if(document.getElementById('txtNome').value.trim().length == 0){
            alert('Nome do Respons�vel Legal Cadastrado n�o informado.');
            return false;
        }
        if(document.getElementById('txtNumeroSei').value.trim().length == 0){
            alert('N�mero SEI da Justificativa n�o Informado.');
            return false;
        }
        if(document.getElementById('txtTipo').value.trim().length == 0){
            alert('N�mero SEI da Justificativa n�o v�lido.');
            return false;
        }

        url = '<?php echo PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_pet_vinc_suspender_restabelecer_concluir&idVinculo=' . $idVinculo))?>';
            infraAbrirJanela(url,
            'concluirPeticionamento',
            770,
            480,
            '',     //options
            false); //modal
    }

    function validarNumeroSEI() {
        document.getElementById('txtTipo').value='';
        if (document.getElementById('txtNumeroSei').value!=''){
            objAjax = new infraAjaxComplementar(null, '<?=$strLinkAjaxValidacoesNumeroSEI?>');
            objAjax.limparCampo = false;
            objAjax.mostrarAviso = false;
            objAjax.tempoAviso = 1000;
            objAjax.async = false;

            objAjax.prepararExecucao = function () {
                var numeroSEI = document.getElementById('txtNumeroSei').value
                return 'numeroSEI=' + numeroSEI;
            };

            objAjax.processarResultado = function (arr) {
                //verifica se o documento foi assinado.
                if ('Assinatura' in arr) {
                    alert(arr['Assinatura']);
                    return false;
                } else {
                    document.getElementById('hdnIdDocumento').value = arr['IdProtocolo'];
                    document.getElementById('txtTipo').value = arr['Identificacao'];
                }
            };

            objAjax.executar();
        }
    }

</script>