<?php
$strLinkAjaxValidarWsdl = SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=md_pet_integracao_busca_operacao_wsdl');
$strLinkAjaxBuscarParametroWsdl = SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=md_pet_integracao_busca_parametro_wsdl');
?>
<script type="text/javascript" charset="iso-8859-1">

    var preencheCache = false;
    var staUtilizaWs = false;
    var staUtilizaWsCheck = null;

    function inicializar() {

        comportamentoMedianteFuncionalidade();
        habilitaWs();

        if ('<?=$_GET['acao']?>' == 'md_pet_integracao_cadastrar') {

            document.getElementById('selMdPetIntegFuncionalid').focus();
            $('.initHidden').css('display', 'none');

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

        function comportamentoMedianteFuncionalidade() {

            $("#rdStaTpClienteWsSoap").attr("checked", "checked");
            $('.initHidden').css('display', 'none');

            switch ($('#selMdPetIntegFuncionalid').val()) {

                case '':

                    $('.initHidden').css('display', 'none');

                    break;
                case '1':

                    habilitaWs();

                    $('div#tipoLogradouro, div#numeroLogradouro, div#tipoLogradouro, div#expiracacaoCache, #blcUsarIntegracaoWs, #blcTipoClienteWs, #blcParamsSuspensaoAutomatica').css('display', 'block');
                    document.getElementById('lbltxtCodRFBSuspensaoAutomatica').innerText = 'C�digos de Situa��o Cadastral que identifica Pessoas Jur�dicas Inativas na Receita:';

                    $('[id^="paramSaidaTable_"]').css('display', 'table-row');
                    $('#paramEntradaTable_cnpjEmpresa').css('display', 'table-row');
                    $('#paramSaidaTable_cpfPessoa, #paramSaidaTable_tpLogradouro, #paramSaidaTable_numero, #paramSaidaTable_complemento, #paramEntradaTable_cpfPessoa').css('display', 'none');

                    if($('#chkSinCache:checked').length){
                        $('#paramEntradaTable_periodoCache').css('display', 'table-row');
                    }
                    if($('#chkSinTipo:checked').length){
                        $('#paramSaidaTable_tpLogradouro').css('display', 'table-row');
                    }
                    if($('#chkSinNumero:checked').length){
                        $('#paramSaidaTable_numero').css('display', 'table-row');
                    }
                    if($('#chkSinComplemento:checked').length){
                        $('#paramSaidaTable_complemento').css('display', 'table-row');
                    }

                    $('#imgAjuda2').attr('onmouseover', 'return infraTooltipMostrar(\'� extremamente recomendado que se utilize Integra��o com a base de dados da Receita Federal para validar se o CPF do Usu�rio Externo que est� formalizando a vincula��o como Respons�vel Legal de Pessoa Jur�dica � de fato do Respons�vel Legal pelo CNPJ constante na Receita Federal. \\n \\n Caso opte por ativar as funcionalidades afetas a Pessoa Jur�dica e Procura��o Eletr�nica para os Usu�rios Externos Sem Integra��o com a base da Receita Federal, os Usu�rios Externos continuar�o a declarar a responsabilidade, at� penal, sobre as informa��es prestadas, mas poder�o ocorrer contradi��o e, caso necessite, Suspens�o e Altera��o da vincula��o podem ser efetivadas pelo menu Administra��o > Peticionamento Eletr�nico > Vincula��es e Procura��es Eletr�nicas.\',\'Ajuda\');');
                    $('#imgAjuda3').attr('onmouseover', 'return infraTooltipMostrar(\'Ao selecionar esta op��o, n�o ocorrer� qualquer valida��o se o CPF do Usu�rio Externo que est� formalizando a vincula��o como Respons�vel Legal de Pessoa Jur�dica � de fato do Respons�vel Legal pelo CNPJ constante na Receita Federal, ficando exclusivamente sob responsabilidade, at� penal, da auto declara��o efetivada pelo Usu�rio Externo e documentos que anexar no Peticionamento de formaliza��o.\',\'Ajuda\');');
                    $('#imgAjuda4').attr('onmouseover', 'return infraTooltipMostrar(\'Ao selecionar esta op��o, o CPF do Usu�rio Externo que est� formalizando a vincula��o como Respons�vel Legal de Pessoa Jur�dica ser� validado por integra��o configurada abaixo se � de fato do Respons�vel Legal pelo CNPJ constante na Receita Federal. \\n \\n Se n�o ocorrer a valida��o o Usu�rio Externo n�o poder� prosseguir com o Peticionamento inicial de Respons�vel Legal de Pessoa Jur�dica.\',\'Ajuda\');');

                    $('#txtNome').focus();

                    break;
                case '2':

                    habilitaWs();

                    $("#rdStaUtilizarWsSim").attr("checked", "checked");
                    $('#rdStaUtilizarWsNao').closest('span').css('display', 'none');
                    $('div#tipoLogradouro, div#numeroLogradouro, div#complementoLogradouro').css('display', 'none');
                    $('div#expiracacaoCache, #blcUsarIntegracaoWs, #blcTipoClienteWs, #blcEnderecoWs').css('display', 'block');

                    $('[id^="paramSaidaTable_"], [id^="paramEntradaTable_"]').css('display', 'none');
                    $('#paramEntradaTable_cpfPessoa, #paramEntradaTable_identificacaoOrigem').css('display', 'table-row');
                    $('#paramSaidaTable_codSituacaoCadastral, #paramSaidaTable_descSituacaoCadastral').css('display', 'table-row');

                    $(':radio:not(:checked)').attr('disabled', true);

                    if($('#chkSinCache:checked').length){
                        $('#paramEntradaTable_periodoCache').css('display', 'table-row');
                    }

                    $('#imgAjuda2').attr('onmouseover', 'return infraTooltipMostrar(\'Esta integra��o ativa a consulta de dados sobre o CPF de Usu�rios Externos ativos e liberados no SEI do �rg�o junto � base da Receita Federal para automatizar sua desativa��o se retornar Situa��es Cadastrais espec�ficas listadas no campo pr�prio abaixo. \\n\\n\\n Por exemplo, quando na Receita Federal o CPF constar na situa��o &ldquo;Cancelada por Encerramento de Esp�lio&rdquo; e &ldquo;Cancelada por �bito sem Espolio&rdquo; o Agendamento relacionado vai desativar o cadastro do Usu�rio Externo.\',\'Ajuda\');');
                    $('#imgAjuda4').hide();

                    staUtilizaWsCheck = true;

                    $('#txtNome').focus();

                    break;
            }

        }

        $('#selMdPetIntegFuncionalid').change(function (){
            comportamentoMedianteFuncionalidade();
        });

    }

    function habilitaWs(changeRadio = false) {
        var itensIntegracao = document.getElementsByName('rdStaUtilizarWs');

        $.each(itensIntegracao, function (i, item) {
            if (item.checked == true) {
                staUtilizaWsCheck = true;
                if (item.value == 'N') {
                    staUtilizaWs = false;
                    $('#blcEnderecoWs, #blcOperacaoWs, #blcCacheWs, #fldParametrosCache, #blcTipoClienteWs, #blcEntradaWs, #blcSaidaWs, #blcParamsSuspensaoAutomatica').css('display', 'none');
                    $('#blcTextoSemWs').css('display', 'block');
                } else {
                    staUtilizaWs = true;
                    $('#blcTipoClienteWs, #blcEnderecoWs').css('display', 'block');
                    $('#blcTextoSemWs').css('display', 'none');
                }
            }
        });

        if(changeRadio) validarWsdl();
    }

    function validarCadastro() {

        if (!infraSelectSelecionado('selMdPetIntegFuncionalid')) {
            alert('Selecione uma Funcionalidade.');
            document.getElementById('selMdPetIntegFuncionalid').focus();
            return false;
        }

        if (infraTrim(document.getElementById('txtNome').value) == '') {
            alert('Informe o Nome da integra��o.');
            document.getElementById('txtNome').focus();
            return false;
        }

        if (staUtilizaWsCheck == null) {
            alert('Informe Indica��o de Integra��o com a Receita Federal.');
            document.getElementById('txtNome').focus();
            return false;
        }

        if (staUtilizaWs == true) {

            if (infraTrim(document.getElementById('selNuVersao').value) == '') {
                alert('Informe a vers�o SOAP.');
                document.getElementById('selNuVersao').focus();
                return false;
            }

            if (infraTrim(document.getElementById('txtEnderecoWsdl').value) == '') {
                alert('Informe Endere�o do Webservice.');
                document.getElementById('txtEnderecoWsdl').focus();
                return false;
            }

            if (infraTrim(document.getElementById('selOperacaoWsdl').value) == '') {
                alert('Informe Opera��o.');
                document.getElementById('selOperacaoWsdl').focus();
                return false;
            }

            if(document.getElementById('selOperacaoWsdl').value.includes('PessoaFisica')){

                if (infraTrim(document.getElementById('txtCodRFBSuspensaoAutomatica').value) == '') {
                    alert('Informe os C�digos das Situa��es Cadastrais do CPF na Receita Federal para bloquear Usu�rios Externos.');
                    document.getElementById('txtCodRFBSuspensaoAutomatica').focus();
                    return false;
                }

                if (infraTrim(document.getElementById('nomeFuncionalDadosEntrada_cpfPessoa').value) == '') {
                    alert('Indique o dado de entrada no webservice para CPF da Pessoa F�sica.');
                    document.getElementById('nomeFuncionalDadosEntrada_cpfPessoa').focus();
                    return false;
                }

                if (infraTrim(document.getElementById('nomeFuncionalDadosEntrada_identificacaoOrigem').value) == '') {
                    alert('Indique o dado de entrada no webservice para Identifica��o Origem.');
                    document.getElementById('nomeFuncionalDadosEntrada_identificacaoOrigem').focus();
                    return false;
                }

                if (infraTrim(document.getElementById('nomeFuncionalDadosSaida_codSituacaoCadastral').value) == '') {
                    alert('Indique o dado de saida do webservice para C�digo da Situa��o Cadastral.');
                    document.getElementById('nomeFuncionalDadosSaida_codSituacaoCadastral').focus();
                    return false;
                }

                if (infraTrim(document.getElementById('nomeFuncionalDadosSaida_descSituacaoCadastral').value) == '') {
                    alert('Indique o dado de saida do webservice para Descri��o da Situa��o Cadastral.');
                    document.getElementById('nomeFuncionalDadosSaida_descSituacaoCadastral').focus();
                    return false;
                }

            }

            if (document.getElementById('chkSinCache').checked) {
                if (infraTrim(document.getElementById('selCacheDataArmazenamento').value) == '') {
                    alert('Informe Data de Armazenamento do Registro.');
                    document.getElementById('selCacheDataArmazenamento').focus();
                    return false;
                }
                if (infraTrim(document.getElementById('selCachePrazoExpiracao').value) == '') {
                    alert('Informe Prazo de Expira��o do Cache.');
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
            alert('Preenche o campo Endere�o WSDL.');
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

                    $('#blcOperacaoWs, #blcParamsSuspensaoAutomatica').css('display', 'inherit');

                    $('#blcTextoSemWs').css('display', 'none');

                    if($('#chkSinCache:checked').length){
                        $('#paramEntradaTable_periodoCache').css('display', 'table-row');
                    }

                    //document.getElementById('gridOperacao').style.display = "inherit";
                } else {
                    alert('Erro:' + $(result).find('msg').text());
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


    function buscarParametroWsdl(tipo_parametro, cache = false) {

        var enderecoWsdl = document.getElementById('txtEnderecoWsdl').value;
        var operacaoWsdl = document.getElementById('selOperacaoWsdl').value;

        /*
        if(enderecoWsdl == ''){
            alert('Preenche o campo Endere�o WSDL.');
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

                if(cache){

                    if (tipo_parametro == 'e') {
                        var select = document.getElementById('selCachePrazoExpiracao');
                        var selectedValor = '<?= $strItensSelCachePrazoExpiracao;?>';
                    }else{
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
                            opt.selected = $(value).text() == selectedValor ? true : false;
                            select.appendChild(opt);
                        });

                        //document.getElementById('gridOperacao').style.display = "inherit";
                    } else {
                        alert('Erro ao montar par�metros.');
                        //document.getElementById('gridOperacao').style.display = "none";
                    }

                }else {

                    var arraySelect = new Array();
                    if (tipo_parametro == 'e') {
                        arraySelect.push('selParametrosE');
                        arraySelect.push('nomeFuncionalDadosEntrada_cnpjEmpresa');
                        arraySelect.push('nomeFuncionalDadosEntrada_cpfPessoa');
                        arraySelect.push('nomeFuncionalDadosEntrada_identificacaoOrigem');
                    } else {
                        arraySelect.push('nomeFuncionalDadosSaida_cnpjEmpresa');
                        arraySelect.push('nomeFuncionalDadosSaida_cpfPessoa');
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
                        alert('Erro:' + $(result).find('msg').text());
                        //document.getElementById('gridOperacao').style.display = "none";
                    }

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
        if (infraTrim(document.getElementById('selOperacaoWsdl').value) == '') {
            alert('Informe Opera��o.');
            document.getElementById('selOperacaoWsdl').focus();
            return false;
        }
        if (objeto.checked) {
            document.getElementById('paramEntradaTable_periodoCache').style.display = '';
            buscarParametroWsdl('e', true);
            buscarParametroWsdl('s', true);
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
        buscarParametroWsdl ('s');
        if($('select[name="selOperacaoWsdl"]').val() != ''){
            $('#blcCacheWs').css('display', 'inherit');
            $('#blcEntradaWs').css('display', 'inherit');
            $('#blcSaidaWs').css('display', 'inherit');

            if($('#selMdPetIntegFuncionalid').val() == '2'){
                $('#blcParamsSuspensaoAutomatica').css('display', 'block');
                document.getElementById('lbltxtCodRFBSuspensaoAutomatica').innerText = 'C�digos de Situa��o Cadastral na Receita para Suspender Usu�rios Externos';
            }
        }

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
            } else if(select == 'nomeFuncionalDadosEntrada_cpfPessoa' && tipo_parametro == 'e') {
                selectedValor = '<?php echo $strItensSelCpfPessoa; ?>';
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
