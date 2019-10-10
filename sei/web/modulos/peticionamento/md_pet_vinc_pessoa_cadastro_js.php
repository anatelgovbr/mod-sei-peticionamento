<?php
$strLinkUsuarioAjax = SessaoSEIExterna::getInstance()->assinarLink('controlador_ajax_externo.php?acao_ajax=md_pet_vinc_usu_ext_usuario_externo');
?>
<script>
    function inicializar() {
    }

    function buscarCpf(campo) {
        var nuCpf = infraRetirarFormatacao(campo.value.trim());
        var cpfAtual = infraRetirarFormatacao(document.getElementById('txtCpfCad').value.trim());

        if(parseFloat(nuCpf) == parseFloat(cpfAtual)){
            alert('O Cpf informado pertence ao atual Responsável Legal dessa PJ.');
            return false;
        }

        var campoNome = document.getElementById('txtNomeNovo');
        var idVinculo = document.getElementById('hdnIdVinculo').value;

        if (nuCpf.length > 0) {
            $.ajax({
                type: 'post',
                url: '<?php echo $strLinkUsuarioAjax?>',
                data: {
                    nuCpf: nuCpf,
                    idVinculo : idVinculo
                },
                dataType: 'xml',
                success: function (data) {
                    var success = $.trim($('success', data).text()).length;

                    if (success > 0) {
                        var message = $.trim($('msg', data).text());
                        campo.value = '';
                        campoNome.value = '';
                        campo.focus();
                        alert(message);
                        return false;
                    }

                    $('dados-pf', data).children().each(function () {
                        var idCampo = $(this).context.localName;
                        if(idCampo != '') {
                            var valor = $(this).context.innerHTML;
                            var campo = $("#" + idCampo);
                            $(campo).val(valor);
                        }
                    });

                    window.opener.document.getElementById('hdnIdContatoNovo').value = $('idContatoNovo', data).text()
                }
            })
        }
    }

    function salvar() {
        var cpf = document.getElementById('txtCpfNovo').value.trim();

        if (cpf.length == 0) {
            alert('Usuário não encontrado');
            return false;
        }


        var nome = document.getElementById('txtNomeNovo').value.trim();
        if (nome.length == 0) {
            alert('Nome do Usuário Externo não Informado.');
            return false;
        }

        var motivo  = document.getElementById('txtMotivo').value.trim();
        if(motivo.length == 0){
            alert('Motivo não Informado.');
            return false;
        }

        document.getElementById('frmEdicaoAuxiliar').submit();
    }
</script>