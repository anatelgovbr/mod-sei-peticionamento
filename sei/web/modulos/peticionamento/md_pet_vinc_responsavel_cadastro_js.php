<?php
//$strLinkUsuarioAjax = SessaoSEIExterna::getInstance()->assinarLink('controlador_ajax_externo.php?acao_ajax=md_pet_vinc_usu_ext_usuario_externo');
$strLinkUsuarioAjax = SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=md_pet_vinc_responsavellegal_cpf_consultar');
?>
<script>
    function inicializar() {
    }

    function buscarCpf(campo) {

        var nuCpf = infraRetirarFormatacao(campo.value.trim());
        var cpfAtual = infraRetirarFormatacao(document.getElementById('txtCpf').value.trim());

        if(parseFloat(infraRetirarFormatacao(nuCpf)) == parseFloat(cpfAtual)){
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

                        var idCampo = $(this)[0].tagName;
                        if(idCampo != '') {
                            var valor = $(this)[0].innerHTML;
                            var campo = $("#" + idCampo);
                            $(campo).val(valor);
                        }
                    });

                    //window.opener.document.getElementById('hdnIdContatoNovo').value = $('idContatoNovo', data).text()
                    document.getElementById('hdnIdContatoNovo').value = $('idContatoNovo', data).text()
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

        var validarNumeroSeiPreenchido  = document.getElementById('txtNumeroSei').value.trim();
        if(validarNumeroSeiPreenchido.length == 0){
           alert('Número SEI não Informado.');
           return false;
        }

        var validarNumeroSeiCorreto  = document.getElementById('hdnValidarNumSEI').value.trim();
        if(validarNumeroSeiCorreto != 'true'){
            alert('Número SEI inválido.');
            return false;
        }

        //var motivo  = document.getElementById('txtMotivo').value.trim();
        //if(motivo.length == 0){
        //    alert('Motivo não Informado.');
        //    return false;
        //}


//        document.getElementById('frmEdicaoAuxiliar').submit();

//        var alteracao = document.getElementById('hdnIsAlteracao').value == '1';

//        if(alteracao){
            url = '<?php echo PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_pet_vinc_responsavel_concluir_alt&idVinculo=' . $idVinculo))?>';
//        }else{
//            url = '<?php echo PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_pet_vinc_responsavel_concluir_cad'))?>';
//        }
        
        infraAbrirJanela(url,
            'concluirPeticionamento',
            770,
            480,
            '', //options
            false); //modal
        
    }

    function validarNumeroSEI() {

    	if (document.getElementById('txtNumeroSei').value!=''){
	        objAjax = new infraAjaxComplementar(null, '<?=$strLinkAjaxValidacoesNumeroSEI?>');
	        objAjax.limparCampo = true;
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
                    document.getElementById('hdnValidarNumSEI').value = 'true';
	                //document.getElementById('hdnDataAtualGrid').value = arr['DataAtual'];
	                //document.getElementById('hdnUnidadeAtualGrid').value = arr['UnidadeAtual'];
	                //document.getElementById('txtNumeroSei').value = arr['ProtocoloFormatado'];
	                //document.getElementById('hdnTipoDocumentoGrid').value = arr['Identificacao'];
	                //document.getElementById('hdnNumDocumentoGrid').value = arr['ProtocoloFormatado'];
	                //document.getElementById('btnAdicionar').style.display = '';
	                //document.getElementById('hdnUrlDocumento').value = arr['UrlDocumento'];
	            }
	        };
	
	        objAjax.executar();
    	}else{
    		document.getElementById('txtTipo').value='';
            document.getElementById('hdnValidarNumSEI').value = 'false';
    	}

    }

    
</script>