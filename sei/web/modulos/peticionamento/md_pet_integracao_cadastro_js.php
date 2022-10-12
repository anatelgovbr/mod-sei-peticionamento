<?php
$strLinkAjaxValidarWsdl = SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=md_pet_integracao_busca_operacao_wsdl');
$strLinkAjaxBuscarParametroWsdl = SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=md_pet_integracao_busca_parametro_wsdl');
?>
<script type="text/javascript" charset="iso-8859-1">
    var preencheCache = false;
    var staUtilizaWs = false;
    var staUtilizaWsCheck = null;

    function inicializar() {
        habilitaWs();
        if ('<?=$_GET['acao']?>' == 'md_pet_integracao_cadastrar') {
            document.getElementById('selMdPetIntegFuncionalid').focus();
            $('#blcEnderecoWs').css('display', 'none');
            $('#blcOperacaoWs').css('display', 'none');
            $('#blcCacheWs').css('display', 'none');
            $('#fldParametrosCache').css('display', 'none');
            $('#blcTextoSemWs').css('display', 'none');
        } else if ('<?=$_GET['acao']?>' == 'md_pet_integracao_consultar') {
            infraDesabilitarCamposAreaDados();
            if (staUtilizaWs) {
                validarWsdl();
            }
        } else {
            document.getElementById('btnCancelar').focus();
            if (staUtilizaWs) {
                validarWsdl();
            }
        }
        infraEfeitoTabelas();
    }

    function habilitaWs(changeRadio = false) {
        var itensIntegracao = document.getElementsByName('rdStaUtilizarWs');

        $.each(itensIntegracao, function (i, item) {
            if (item.checked == true) {
                staUtilizaWsCheck = true;
                if (item.value == 'N') {
                    staUtilizaWs = false;
                    $('#blcEnderecoWs').css('display', 'none');
                    $('#blcOperacaoWs').css('display', 'none');
                    $('#blcCacheWs').css('display', 'none');
                    $('#fldParametrosCache').css('display', 'none');
                    $('#blcTipoClienteWs').css('display', 'none');
                    $('#blcEntradaWs').css('display', 'none');
                    $('#blcSaidaWs').css('display', 'none');
                    $('#blcTextoSemWs').css('display', 'inherit');
                } else {
                    staUtilizaWs = true;
                    $('#blcEnderecoWs').css('display', 'inherit');
                    $('#blcOperacaoWs').css('display', 'inherit');
                    $('#blcCacheWs').css('display', 'inherit');
                    $('#blcTipoClienteWs').css('display', 'flex');
                    $('#blcEntradaWs').css('display', 'inherit');
                    $('#blcSaidaWs').css('display', 'inherit');
                    cacheMarcaDesmarca($('#fldParametrosCache'));
                    $('#blcTextoSemWs').css('display', 'none');
                }
            }
        });

        if(changeRadio) validarWsdl();
    }

    function validarCadastro() {
        if (infraTrim(document.getElementById('txtNome').value) == '') {
            alert('Informe Nome.');
            document.getElementById('txtNome').focus();
            return false;
        }

        if (!infraSelectSelecionado('selMdPetIntegFuncionalid')) {
            alert('Selecione uma Funcionalidade.');
            document.getElementById('selMdPetIntegFuncionalid').focus();
            return false;
        }
        if (staUtilizaWsCheck == null) {
            alert('Informe Indicação de Integração com a Receita Federal.');
            document.getElementById('txtNome').focus();
            return false;
        }
        if (staUtilizaWs == true) {

            if (infraTrim(document.getElementById('txtEnderecoWsdl').value) == '') {
                alert('Informe Endereço do Webservice.');
                document.getElementById('txtEnderecoWsdl').focus();
                return false;
            }

            if (infraTrim(document.getElementById('txtEnderecoWsdl').value) == '') {
                alert('Informe Endereço do Webservice.');
                document.getElementById('txtEnderecoWsdl').focus();
                return false;
            }

            if (infraTrim(document.getElementById('selOperacaoWsdl').value) == '') {
                alert('Informe Operação.');
                document.getElementById('selOperacaoWsdl').focus();
                return false;
            }

            if (document.getElementById('chkSinCache').checked) {
                if (infraTrim(document.getElementById('selCacheDataArmazenamento').value) == '') {
                    alert('Informe Data de Armazenamento do Registro.');
                    document.getElementById('selCacheDataArmazenamento').focus();
                    return false;
                }
                if (infraTrim(document.getElementById('selCachePrazoExpiracao').value) == '') {
                    alert('Informe Prazo de Expiração do Cache.');
                    document.getElementById('selCachePrazoExpiracao').focus();
                    return false;
                }
                if (infraTrim(document.getElementById('selCachePrazoExpiracao').value) == '') {
                    alert('Informe Prazo de Expiração do Cache.');
                    document.getElementById('selCachePrazoExpiracao').focus();
                    return false;
                }
                if (infraTrim(document.getElementById('txtPrazo').value) == '') {
                    alert('Informe o Prazo.');
                    return false;
                }
            }
        }

        return true;
    }

    function OnSubmitForm() {
        if (!validarCadastro()) {
            return false;
        }

        var select = document.getElementById('selParametrosE');
        for (i = 0; i < select.length; i++) {
            select.options[i].selected = true;
        }

        var select = document.getElementById('selParametrosS');
        for (i = 0; i < select.length; i++) {
            select.options[i].selected = true;
        }

        return true;

        //return validarCadastro();
    }

    function validarWsdl() {

        var enderecoWsdl = document.getElementById('txtEnderecoWsdl').value;
        if (enderecoWsdl == '') {
            alert('Preenche o campo Endereço WSDL.');
            return false;
        }

        $.ajax({
            type: "POST",
            url: "<?= $strLinkAjaxValidarWsdl ?>",
            dataType: "xml",
            data: {
                endereco_wsdl: enderecoWsdl
            },
            beforeSend: function () {
                if( document.querySelector('#bolIsConsultar').value == 'N' ) infraExibirAviso(false);
            },
            success: function (result) {
                var select = document.getElementById('selOperacaoWsdl');
                //limpar todos os options
                select.options.length = 0;

                if ($(result).find('success').text() == 'true') {
                    var opt = document.createElement('option');
                    opt.value = '';
                    opt.innerHTML = '';
                    select.appendChild(opt);
                    var selectedValor = '<?= PaginaSEI::tratarHTML($objMdPetIntegracaoDTO->getStrOperacaoWsdl());?>';
                    $.each($(result).find('operacao'), function (key, value) {
                        var opt = document.createElement('option');
                        opt.value = $(value).text();
                        opt.innerHTML = $(value).text();
                        if ($(value).text() == selectedValor) {
                            opt.selected = true;
                            preencheCache = true;
                        }
                        select.appendChild(opt);
                    });
                    if (preencheCache) {
                        select.onchange();
                        chkSinCache.onchange();
                    }
                    preencheCache = false;
                    //document.getElementById('gridOperacao').style.display = "inherit";
                } else {
                    alert($(result).find('msg').text());
                    //document.getElementById('gridOperacao').style.display = "none";
                }
            },
            error: function (msgError) {
                msgCommit = "Erro ao processar o XML do SEI: " + msgError.responseText;
            },
            complete: function (result) {
                if( document.querySelector('#bolIsConsultar').value == 'N' ) infraAvisoCancelar();
            }
        });

    }


    function buscarParametroWsdl(tipo_parametro) {

        var enderecoWsdl = document.getElementById('txtEnderecoWsdl').value;
        var operacaoWsdl = document.getElementById('selOperacaoWsdl').value;

        /*
        if(enderecoWsdl == ''){
            alert('Preenche o campo Endereço WSDL.');
            return false;
        }
        */
        $.ajax({
            async: false,
            type: "POST",
            url: "<?= $strLinkAjaxBuscarParametroWsdl ?>",
            dataType: "xml",
            data: {
                endereco_wsdl: enderecoWsdl,
                operacao_wsdl: operacaoWsdl,
                tipo_parametro: tipo_parametro
            },
            beforeSend: function () {
                infraExibirAviso(false);
            },
            success: function (result) {
                var arraySelect = new Array();
                if (tipo_parametro == 'e') {
                    arraySelect.push('selParametrosE');
                    arraySelect.push('nomeFuncionalDadosEntrada_cnpjEmpresa');
                    arraySelect.push('nomeFuncionalDadosEntrada_identificacaoOrigem');
                } else {
                    arraySelect.push('nomeFuncionalDadosSaida_cnpjEmpresa');
                    arraySelect.push('nomeFuncionalDadosSaida_razaoSocial');
                    arraySelect.push('nomeFuncionalDadosSaida_codSituacaoCadastral');
                    arraySelect.push('nomeFuncionalDadosSaida_descSituacaoCadastral');
                    arraySelect.push('nomeFuncionalDadosSaida_dtUltAltSituacaoCadastral');
                    arraySelect.push('nomeFuncionalDadosSaida_tpLogradouro');
                    arraySelect.push('nomeFuncionalDadosSaida_logradouro');
                    arraySelect.push('nomeFuncionalDadosSaida_numero');
                    arraySelect.push('nomeFuncionalDadosSaida_complemento');
                    arraySelect.push('nomeFuncionalDadosSaida_cep');
                    arraySelect.push('nomeFuncionalDadosSaida_bairro');
                    arraySelect.push('nomeFuncionalDadosSaida_codIbgeMunicipio');
                    arraySelect.push('nomeFuncionalDadosSaida_cpfRespLegal');
                    arraySelect.push('nomeFuncionalDadosSaida_nomeRespLegal');
                }

                //limpar todos os options
                // select.options.length = 0;
                if ($(result).find('success').text() == 'true') {
                    var arrayParametros = $(result).find('parametro');

                    $.each(arraySelect, function (key, select) {
                        popularSelect(tipo_parametro, select, arrayParametros);
                    });
                    //document.getElementById('gridOperacao').style.display = "inherit";
                } else {
                    alert($(result).find('msg').text());
                    //document.getElementById('gridOperacao').style.display = "none";
                }
            },
            error: function (msgError) {
                msgCommit = "Erro ao processar o XML do SEI: " + msgError.responseText;
            },
            complete: function (result) {
                infraAvisoCancelar();
            }
        });

    }

    function buscarParametroWsdlCache(tipo_parametro) {

        var enderecoWsdl = document.getElementById('txtEnderecoWsdl').value;
        var operacaoWsdl = document.getElementById('selOperacaoWsdl').value;

        /*
        if(enderecoWsdl == ''){
            alert('Preenche o campo Endereço WSDL.');
            return false;
        }
        */
        $.ajax({
            async: false,
            type: "POST",
            url: "<?= $strLinkAjaxBuscarParametroWsdl ?>",
            dataType: "xml",
            data: {
                endereco_wsdl: enderecoWsdl,
                operacao_wsdl: operacaoWsdl,
                tipo_parametro: tipo_parametro
            },
            beforeSend: function () {
                infraExibirAviso(false);
            },
            success: function (result) {
                if (tipo_parametro == 'e') {
                    var select = document.getElementById('selCachePrazoExpiracao');
                    var selectedValor = '<?= $strItensSelCachePrazoExpiracao;?>';
                } else {
                    var select = document.getElementById('selCacheDataArmazenamento');
                    var selectedValor = '<?= $strItensSelCacheDataArmazenamento;?>';
                }

                //limpar todos os options
                select.options.length = 0;

                if ($(result).find('success').text() == 'true') {
                    $.each($(result).find('parametro'), function (key, value) {
                        var opt = document.createElement('option');
                        opt.value = $(value).text();
                        opt.innerHTML = $(value).text();
                        if ($(value).text() == selectedValor) {
                            opt.selected = true;
                        } else {
                            opt.selected = false;
                        }
                        select.appendChild(opt);
                    });

                    //document.getElementById('gridOperacao').style.display = "inherit";
                } else {
                    alert($(result).find('msg').text());
                    //document.getElementById('gridOperacao').style.display = "none";
                }

            },
            error: function (msgError) {
                msgCommit = "Erro ao processar o XML do SEI: " + msgError.responseText;
            },
            complete: function (result) {
                infraAvisoCancelar();
            }
        });

    }

    function cacheMarcaDesmarca(objeto) {
        if (objeto.checked) {
            document.getElementById('paramEntradaTable_periodoCache').style.display = '';
            buscarParametroWsdlCache('e');
            buscarParametroWsdlCache('s');
        } else {
            document.getElementById('paramEntradaTable_periodoCache').style.display = 'none';
            var select = document.getElementById('selCachePrazoExpiracao');
            select.options.length = 0;

            var select = document.getElementById('selCacheDataArmazenamento');
            select.options.length = 0;
        }
    }

    function tipoMarcaDesmarca(objeto) {
        if (objeto.checked) {
            document.getElementById('paramSaidaTable_tpLogradouro').style.display = '';
        } else {
            document.getElementById('paramSaidaTable_tpLogradouro').style.display = 'none';
        }
    }

    function numeroMarcaDesmarca(objeto) {
        if (objeto.checked) {
            document.getElementById('paramSaidaTable_numero').style.display = '';
        } else {
            document.getElementById('paramSaidaTable_numero').style.display = 'none';
        }
    }

    function complementoMarcaDesmarca(objeto) {
        if (objeto.checked) {
            document.getElementById('paramSaidaTable_complemento').style.display = '';
        } else {
            document.getElementById('paramSaidaTable_complemento').style.display = 'none';
        }
    }


    function operacaoSelecionar() {
        checkbox = document.getElementById('chkSinCache');
        if (!preencheCache) {
            checkbox.checked = false;
        }
        cacheMarcaDesmarca(checkbox);
        buscarParametroWsdl('e');
        buscarParametroWsdl('s');
    }

    function popularSelect(tipo_parametro, select, arrayValores) {

        var selectRetorno = document.getElementById(select);
        selectRetorno.options.length = 0;

        var opt = document.createElement('option');
        opt.value = '';
        opt.innerHTML = 'Selecione';
        selectRetorno.appendChild(opt);
        $.each(arrayValores, function (key, value) {

            var dados = <?php echo json_encode($arrParametrosCadastrados); ?>;
            var selectedValor = "";

            if (select == 'nomeFuncionalDadosEntrada_cnpjEmpresa' && tipo_parametro == 'e') {
                selectedValor = '<?php echo $strItensSelCnpjEmpresa; ?>';
            } else {
                var arrayNome = select.split('_');
                $.each(dados, function (chave, item) {
                    if (item['nome'] == arrayNome[1]) {
                        selectedValor = item['campo_nome'];
                    }
                });
            }
            
            if (key != "endereco") {

                if (tipo_parametro == 'e') {
                    var opt = document.createElement('option');
                    if (key > 99) {
                        opt.value = 'endereco.' + $(value).text();
                        opt.innerHTML = 'endereco.' + $(value).text();
                    } else {
                        opt.value = $(value).text();
                        opt.innerHTML = $(value).text();
                    }
                    if ($(value).text() == selectedValor) {
                        opt.selected = true;
                    } else {
                        opt.selected = false;
                    }
                    selectRetorno.appendChild(opt);
                } else {
                    var opt = document.createElement('option');
                    if (key > 99) {
                        opt.value = 'endereco.' + $(value).text();
                        opt.innerHTML = 'endereco.' + $(value).text();
                    } else {
                        opt.value = $(value).text();
                        opt.innerHTML = $(value).text();
                    }
                    if ($(value).text() == selectedValor) {
                        opt.selected = true;
                    } else {
                        opt.selected = false;
                    }
                    selectRetorno.appendChild(opt);
                }
            }
        });

        selectRetorno
    }


</script>
