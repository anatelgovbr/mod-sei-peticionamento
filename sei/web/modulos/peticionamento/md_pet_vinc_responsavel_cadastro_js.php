<?php
//$strLinkUsuarioAjax = SessaoSEIExterna::getInstance()->assinarLink('controlador_ajax_externo.php?acao_ajax=md_pet_vinc_usu_ext_usuario_externo');
$strLinkUsuarioAjax = SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=md_pet_vinc_responsavellegal_cpf_consultar');
?>
<script>
    function inicializar() {
    }

    function buscarCpf() {
        campo = document.getElementById('txtCpfNovo');
        var nuCpf = infraRetirarFormatacao(campo.value.trim());
        var cpfAtual = infraRetirarFormatacao(document.getElementById('txtCpf').value.trim());

        if(parseFloat(infraRetirarFormatacao(nuCpf)) == parseFloat(cpfAtual)){
            alert('O Cpf informado pertence ao atual Responsável Legal dessa PJ.');
            return false;
        }

        var campoNome = document.getElementById('txtNomeNovo');
        var idVinculo = document.getElementById('hdnIdVinculo').value;

        if (nuCpf.length > 0) {
            processando();
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
                    var erros = $('erros', data).length;

                    if (success > 0) {
                        fecharProcessando();
                        var message = $.trim($('msg', data).text());
                        campo.value = '';
                        campoNome.value = '';
                        campo.focus();
                        alert(message);
                        return false;
                    }

                    if(erros > 0){
                        fecharProcessando();
                        var message = '';
                        $('erros', data).children().each(function () {
                            message = $.trim(this.getAttribute('descricao'));
                        });
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
                    fecharProcessando();
                    //window.opener.document.getElementById('hdnIdContatoNovo').value = $('idContatoNovo', data).text()
                    document.getElementById('hdnIdContatoNovo').value = $('idContatoNovo', data).text()
                }
            })
        }
        
    }

    function salvar() {
        var cpf = document.getElementById('txtCpfNovo').value.trim();

        if (cpf.length == 0) {
            alert('CPF do Usuário Externo não informado.');
            return false;
        }

        var nome = document.getElementById('txtNomeNovo').value.trim();
        if (nome.length == 0) {
            alert('Usuário Externo não encontrado.');
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

        url = '<?php echo PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_pet_vinc_responsavel_concluir_alt&idVinculo=' . $idVinculo))?>';
        infraAbrirJanelaModal(url, 600, 400);
        
    }

    function validarNumeroSEI() {

    	if (document.getElementById('txtNumeroSei').value!=''){
	        objAjax = new infraAjaxComplementar(null, '<?=$strLinkAjaxValidacoesNumeroSEI?>');
	        objAjax.limparCampo = true;
	        objAjax.mostrarAviso = true;
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

    function fecharProcessando() {
        var divFundo = document.getElementById('divInfraAvisoFundo');
        divFundo.style.visibility = 'hidden';
    }

    function processando() {

        exibirAvisoEditor();
        self.setTimeout('exibirBotaoCancelarAviso()', 30000);

        if (INFRA_IE > 0) {
            window.tempoInicio = (new Date()).getTime();
        } else {
            console.time('s');
        }

    }

    function exibirAvisoEditor() {

        var divFundo = document.getElementById('divInfraAvisoFundo');

        if (divFundo == null) {
            divFundo = infraAviso(false, 'Processando...');
        } else {
            document.getElementById('btnInfraAvisoCancelar').style.display = 'none';
            document.getElementById('imgInfraAviso').src = '/infra_css/imagens/aguarde.gif';
        }

        if (INFRA_IE == 0 || INFRA_IE >= 7) {
            divFundo.style.position = 'fixed';
        }

        var divAviso = document.getElementById('divInfraAviso');

        divAviso.style.top = Math.floor(infraClientHeight() / 3) + 'px';
        divAviso.style.left = Math.floor((infraClientWidth() - 200) / 2) + 'px';
        divAviso.style.width = '200px';
        divAviso.style.border = '1px solid black';

        divFundo.style.width = screen.width * 2 + 'px';
        divFundo.style.height = screen.height * 2 + 'px';
        divFundo.style.visibility = 'visible';

    }
</script>