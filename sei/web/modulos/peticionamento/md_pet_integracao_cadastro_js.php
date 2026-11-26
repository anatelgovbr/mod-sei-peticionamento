<?php
$strLinkAjaxValidarWsdl = SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=md_pet_integracao_busca_operacao_wsdl');
$strLinkAjaxBuscarParametroWsdl = SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=md_pet_integracao_busca_parametro_wsdl');
?>
<script type="text/javascript" charset="iso-8859-1">

    var preencheCache = false;
    var staUtilizaWs = false;
    var staUtilizaWsCheck = null;
    // Depois do clique em Validar, a operação e o mapeamento gravados deixam de ser selecionados
    var bolDescartarGravado = false;

    function inicializar() {

        iniciarTabelaHeaders();
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

            if (!$('input[name="rdStaTpClienteWs"]:checked').length) {
                $("#rdStaTpClienteWsSoap").attr("checked", "checked");
            }
            $('.initHidden').css('display', 'none');

            switch ($('#selMdPetIntegFuncionalid').val()) {

                case '':

                    $('.initHidden').css('display', 'none');

                    break;
                case '1':

                    habilitaWs();

                    $('div#tipoLogradouro, div#numeroLogradouro, div#tipoLogradouro, div#expiracacaoCache, #blcUsarIntegracaoWs').css('display', 'block');
                    document.getElementById('lbltxtCodRFBSuspensaoAutomatica').innerText = 'Códigos de Situação Cadastral que identifica Pessoas Jurídicas Inativas na Receita:';

                    $('[id^="paramSaidaTable_"]').css('display', 'table-row').find('select[data-obrigatorio="true"]').attr('required', true);
                    $('#paramEntradaTable_cnpjEmpresa').css('display', 'table-row').find('select[data-obrigatorio="true"]').attr('required', true);
                    $('#paramEntradaTable_cpfPessoa').css('display', 'none').find('select[data-obrigatorio="true"]').attr('required', false);
                    $('#paramSaidaTable_cpfPessoa, #paramSaidaTable_tpLogradouro, #paramSaidaTable_numero, #paramSaidaTable_complemento, #paramSaidaTable_cpfUsuario').css('display', 'none').find('select[data-obrigatorio="true"]').attr('required', false);

                    if($('#chkSinCache:checked').length){
                        $('#paramEntradaTable_periodoCache').css('display', 'table-row').find('select[data-obrigatorio="true"]').attr('required', true);
                    }
                    if($('#chkSinTipo:checked').length){
                        $('#paramSaidaTable_tpLogradouro').css('display', 'table-row');
                    }
                    if($('#chkSinNumero:checked').length){
                        $('#paramSaidaTable_numero').css('display', 'table-row').find('select[data-obrigatorio="true"]').attr('required', staUtilizaWs);
                    }
                    if($('#chkSinComplemento:checked').length){
                        $('#paramSaidaTable_complemento').css('display', 'table-row');
                    }

                    $('#imgAjuda2').attr('onmouseover', 'return infraTooltipMostrar(\'É extremamente recomendado que se utilize Integração com a base de dados da Receita Federal para validar se o CPF do Usuário Externo que está formalizando a vinculação como Responsável Legal de Pessoa Jurídica é de fato do Responsável Legal pelo CNPJ constante na Receita Federal. \\n \\n Caso opte por ativar as funcionalidades afetas a Pessoa Jurídica e Procuração Eletrônica para os Usuários Externos Sem Integração com a base da Receita Federal, os Usuários Externos continuarão a declarar a responsabilidade, até penal, sobre as informações prestadas, mas poderão ocorrer contradição e, caso necessite, Suspensão e Alteração da vinculação podem ser efetivadas pelo menu Administração > Peticionamento Eletrônico > Vinculações e Procurações Eletrônicas.\',\'Ajuda\');');
                    $('#imgAjuda3').attr('onmouseover', 'return infraTooltipMostrar(\'Ao selecionar esta opção, não ocorrerá qualquer validação se o CPF do Usuário Externo que está formalizando a vinculação como Responsável Legal de Pessoa Jurídica é de fato do Responsável Legal pelo CNPJ constante na Receita Federal, ficando exclusivamente sob responsabilidade, até penal, da auto declaração efetivada pelo Usuário Externo e documentos que anexar no Peticionamento de formalização.\',\'Ajuda\');');
                    $('#imgAjuda4').attr('onmouseover', 'return infraTooltipMostrar(\'Ao selecionar esta opção, o CPF do Usuário Externo que está formalizando a vinculação como Responsável Legal de Pessoa Jurídica será validado por integração configurada abaixo se é de fato do Responsável Legal pelo CNPJ constante na Receita Federal. \\n \\n Se não ocorrer a validação o Usuário Externo não poderá prosseguir com o Peticionamento inicial de Responsável Legal de Pessoa Jurídica.\',\'Ajuda\');');

                    $('#txtNome').focus();

                    break;
                case '2':

                    habilitaWs();

                    $("#rdStaUtilizarWsSim").attr("checked", "checked");
                    $('#rdStaUtilizarWsNao').closest('span').css('display', 'none');
                    $('div#tipoLogradouro, div#numeroLogradouro, div#complementoLogradouro').css('display', 'none');
                    $('div#expiracacaoCache, #blcUsarIntegracaoWs, #blcTipoClienteWs, #blcEnderecoWs').css('display', 'block');

                    $('[id^="paramSaidaTable_"], [id^="paramEntradaTable_"]').css('display', 'none').find('select[data-obrigatorio="true"]').attr('required', false);
                    $('#paramEntradaTable_identificacaoOrigem').css('display', 'table-row').find('select[data-obrigatorio="true"]').attr('required', true);
                    $('#paramEntradaTable_cpfUsuario').css('display', 'table-row').find('select[data-obrigatorio="true"]').attr('required', false);
                    $('#paramSaidaTable_codSituacaoCadastral, #paramSaidaTable_descSituacaoCadastral').css('display', 'table-row').find('select[data-obrigatorio="true"]').attr('required', true);

                    $('input[name="rdStaUtilizarWs"]:not(:checked)').attr('disabled', true);

                    if($('#chkSinCache:checked').length){
                        $('#paramEntradaTable_periodoCache').css('display', 'table-row');
                    }

                    $('#imgAjuda2').attr('onmouseover', 'return infraTooltipMostrar(\'Esta integração ativa a consulta de dados sobre o CPF de Usuários Externos ativos e liberados no SEI do órgão junto à base da Receita Federal para automatizar sua desativação se retornar Situações Cadastrais específicas listadas no campo próprio abaixo. \\n\\n\\n Por exemplo, quando na Receita Federal o CPF constar na situação &ldquo;Cancelada por Encerramento de Espólio&rdquo; e &ldquo;Cancelada por Óbito sem Espolio&rdquo; o Agendamento relacionado vai desativar o cadastro do Usuário Externo.\',\'Ajuda\');');
                    $('#imgAjuda4').hide();

                    staUtilizaWsCheck = true;

                    $('#txtNome').focus();

                    break;
            }

            atualizarLinhaToken();

        }

        $('#selMdPetIntegFuncionalid').change(function (){
            comportamentoMedianteFuncionalidade();
        });

        $('#selMetodoAutenticacao').change(function () {
            atualizarLinhaToken();
        });

    }

    function habilitaWs(changeRadio = false) {
        var itensIntegracao = document.getElementsByName('rdStaUtilizarWs');

        $.each(itensIntegracao, function (i, item) {
            if (item.checked == true) {
                staUtilizaWsCheck = true;
                if (item.value == 'N') {
                    staUtilizaWs = false;
                    $('#blcEnderecoWs, #blcOperacaoWs, #blcCacheWs, #fldParametrosCache, #blcEntradaWs, #blcSaidaWs, #blcParamsSuspensaoAutomatica, #blcTipoClienteWs, #blcParamsSuspensaoAutomatica, #blcRestWs').css('display', 'none');
                    $('#blcTextoSemWs').css('display', 'block');
                    $('#blcEntradaWs, #blcSaidaWs').find('select[data-obrigatorio="true"]').attr('required', false);
                } else {
                    staUtilizaWs = true;
                    $('#blcTipoClienteWs, #blcEnderecoWs').css('display', 'block');
                    $('#blcTextoSemWs').css('display', 'none');
                    // Campo em linha oculta não leva required, senão o navegador bloqueia o envio; o prazo do cache segue o cacheMarcaDesmarca
                    $('#blcEntradaWs, #blcSaidaWs').find('select[data-obrigatorio="true"]').not('#selCachePrazoExpiracao').filter(function () {
                        return $(this).closest('tr').css('display') !== 'none';
                    }).attr('required', true);
                    tipoIntegracaoSelecionar(false);
                }
            }
        });

        if(changeRadio) validarWsdl();
    }

    function isRest() {
        return $('#rdStaTpClienteWsRest').is(':checked');
    }

    // Mostra os campos do tipo escolhido; na troca de tipo, a operação e o mapeamento voltam a ser descobertos
    function tipoIntegracaoSelecionar(bolLimpar = true) {
        var bolRest = isRest();
        $('#blcNuVersao').css('display', bolRest ? 'none' : 'block');
        $('#blcRestWs').css('display', bolRest && staUtilizaWs ? 'block' : 'none');
        $('#lblEnderecoWsdl').text(bolRest ? 'Endereço do Documento OpenAPI:' : 'Endereço do Webservice:');

        if (bolLimpar) {
            document.getElementById('selOperacaoWsdl').options.length = 0;
            $('#blcOperacaoWs, #blcCacheWs, #blcEntradaWs, #blcSaidaWs').css('display', 'none');
        }
        atualizarLinhaToken();
    }

    // A linha Authorization da tabela de entrada só aparece no Body Authentication by Token do REST (ADR-PET-013)
    function atualizarLinhaToken() {
        var bolToken = isRest() && staUtilizaWs && document.getElementById('selMetodoAutenticacao').value == '<?= MdPetIntegFabricaRN::AUTENTICACAO_CORPO ?>';
        $('#paramEntradaTable_<?= MdPetIntegFabricaRN::ENTRADA_TOKEN ?>').css('display', bolToken ? 'table-row' : 'none');
    }

    var objTabelaHeaders = null;
    var idHeaderAlterar = null;
    var numHeaderNovo = 0;
    var MASCARA_DADO_RESTRITO = '<?= MdPetIntegFabricaRN::MASCARA_DADO_RESTRITO ?>';

    // Grade de headers do REST no padrão do Utilidades; o conteúdo do dado restrito gravado chega só como máscara
    function iniciarTabelaHeaders() {
        var bolEditar = '<?= $_GET['acao'] ?>' != 'md_pet_integracao_consultar';
        objTabelaHeaders = new infraTabelaDinamica('tblHeaders', 'hdnTbHeaders', bolEditar, bolEditar);
        objTabelaHeaders.gerarEfeitoTabela = true;
        objTabelaHeaders.alterar = function (arrLinha) {
            idHeaderAlterar = arrLinha[0];
            document.getElementById('txtAtributoHeader').value = arrLinha[1];
            document.getElementById('txtConteudoHeader').value = arrLinha[4];
            document.getElementById('chkDadoRestrito').checked = arrLinha[3] == 'Sim';
            document.getElementById('txtAtributoHeader').focus();
        };
    }

    function adicionarHeader() {
        var strAtributo = infraTrim(document.getElementById('txtAtributoHeader').value);
        var strConteudo = infraTrim(document.getElementById('txtConteudoHeader').value);
        var bolRestrito = document.getElementById('chkDadoRestrito').checked;

        if (strAtributo == '' || strConteudo == '' || (!bolRestrito && strConteudo == MASCARA_DADO_RESTRITO)) {
            alert('Informe o Atributo e o Conteúdo do header.');
            document.getElementById(strAtributo == '' ? 'txtAtributoHeader' : 'txtConteudoHeader').focus();
            return;
        }

        var strId = idHeaderAlterar !== null ? idHeaderAlterar : 'novo_' + (++numHeaderNovo);
        var strAtributoHtml = $('<pre>').text(strAtributo).html();
        var strConteudoHtml = $('<pre>').text(strConteudo).html();
        objTabelaHeaders.adicionar([strId, strAtributoHtml, bolRestrito ? MASCARA_DADO_RESTRITO : strConteudoHtml, bolRestrito ? 'Sim' : 'Não', strConteudoHtml]);

        idHeaderAlterar = null;
        document.getElementById('txtAtributoHeader').value = '';
        document.getElementById('txtConteudoHeader').value = '';
        document.getElementById('chkDadoRestrito').checked = false;
        document.getElementById('txtAtributoHeader').focus();
    }

    function possuiHeaderRestrito() {
        var tbl = document.getElementById('tblHeaders');
        for (var i = 1; i < tbl.rows.length; i++) {
            if (infraTrim(tbl.rows[i].cells[3].innerText) == 'Sim') {
                return true;
            }
        }
        return false;
    }

    function validarCadastro() {

        if (!infraSelectSelecionado('selMdPetIntegFuncionalid')) {
            alert('Selecione uma Funcionalidade.');
            document.getElementById('selMdPetIntegFuncionalid').focus();
            return false;
        }

        if (infraTrim(document.getElementById('txtNome').value) == '') {
            alert('Informe o Nome da integração.');
            document.getElementById('txtNome').focus();
            return false;
        }

        if (staUtilizaWsCheck == null) {
            alert('Informe Indicação de Integração com a Receita Federal.');
            document.getElementById('txtNome').focus();
            return false;
        }

        if (staUtilizaWs == true) {

            if (!isRest() && infraTrim(document.getElementById('selNuVersao').value) == '') {
                alert('Informe a versão SOAP.');
                document.getElementById('selNuVersao').focus();
                return false;
            }

            if (infraTrim(document.getElementById('txtEnderecoWsdl').value) == '') {
                alert(isRest() ? 'Informe o Endereço do Documento OpenAPI.' : 'Informe Endereço do Webservice.');
                document.getElementById('txtEnderecoWsdl').focus();
                return false;
            }

            if (isRest() && infraTrim(document.getElementById('txtEnderecoExecucao').value) == '') {
                alert('Informe o Endereço de Execução.');
                document.getElementById('txtEnderecoExecucao').focus();
                return false;
            }

            if (isRest() && document.getElementById('selMetodoAutenticacao').value == '<?= MdPetIntegFabricaRN::AUTENTICACAO_HEADER ?>' && !possuiHeaderRestrito()) {
                alert('O Método de Autenticação Header Authentication by Token exige ao menos um header marcado como Dado Restrito.');
                document.getElementById('txtAtributoHeader').focus();
                return false;
            }

            if (infraTrim(document.getElementById('selOperacaoWsdl').value) == '') {
                alert('Informe Operação.');
                document.getElementById('selOperacaoWsdl').focus();
                return false;
            }

            if (isRest() && document.getElementById('selMetodoAutenticacao').value == '<?= MdPetIntegFabricaRN::AUTENTICACAO_CORPO ?>') {
                if (infraTrim(document.getElementById('nomeFuncionalDadosEntrada_<?= MdPetIntegFabricaRN::ENTRADA_TOKEN ?>').value) == '') {
                    alert('Indique o dado de entrada no webservice para Authorization.');
                    document.getElementById('nomeFuncionalDadosEntrada_<?= MdPetIntegFabricaRN::ENTRADA_TOKEN ?>').focus();
                    return false;
                }
                if (infraTrim(document.getElementById('txtTokenAutenticacao').value) == '') {
                    alert('Informe o conteúdo do Authorization.');
                    document.getElementById('txtTokenAutenticacao').focus();
                    return false;
                }
            }

            if(document.getElementById('selOperacaoWsdl').value.includes('PessoaFisica')){

                if (infraTrim(document.getElementById('txtCodRFBSuspensaoAutomatica').value) == '') {
                    alert('Informe os Códigos das Situações Cadastrais do CPF na Receita Federal para bloquear Usuários Externos.');
                    document.getElementById('txtCodRFBSuspensaoAutomatica').focus();
                    return false;
                }

                // if (infraTrim(document.getElementById('nomeFuncionalDadosEntrada_cpfPessoa').value) == '') {
                //     alert('Indique o dado de entrada no webservice para CPF da Pessoa Física.');
                //     document.getElementById('nomeFuncionalDadosEntrada_cpfPessoa').focus();
                //     return false;
                // }

                if (infraTrim(document.getElementById('nomeFuncionalDadosEntrada_cpfUsuario').value) == '') {
                    alert('Indique o dado de entrada no webservice para CPF do Usuário requisitante.');
                    document.getElementById('nomeFuncionalDadosEntrada_cpfUsuario').focus();
                    return false;
                }

                if (infraTrim(document.getElementById('nomeFuncionalDadosEntrada_identificacaoOrigem').value) == '') {
                    alert('Indique o dado de entrada no webservice para Identificação Origem.');
                    document.getElementById('nomeFuncionalDadosEntrada_identificacaoOrigem').focus();
                    return false;
                }

                if (infraTrim(document.getElementById('nomeFuncionalDadosSaida_codSituacaoCadastral').value) == '') {
                    alert('Indique o dado de saída do webservice para Código da Situação Cadastral.');
                    document.getElementById('nomeFuncionalDadosSaida_codSituacaoCadastral').focus();
                    return false;
                }

                if (infraTrim(document.getElementById('nomeFuncionalDadosSaida_descSituacaoCadastral').value) == '') {
                    alert('Indique o dado de saída do webservice para Descrição da Situação Cadastral.');
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

        // No REST, o local de cada dado de entrada na chamada segue a opção escolhida
        $('#frmMdPetIntegracaoCadastro input[name^="localDadosEntrada"]').remove();
        if (isRest()) {
            $('select[id^="nomeFuncionalDadosEntrada_"]').each(function () {
                $('<input type="hidden">')
                    .attr('name', 'localDadosEntrada[' + this.id.replace('nomeFuncionalDadosEntrada_', '') + ']')
                    .val($(this).find('option:selected').attr('data-local') || '')
                    .appendTo('#frmMdPetIntegracaoCadastro');
            });
        }

        return true;

        //return validarCadastro();
    }

    // Limpa a grade de headers e os campos de destino do mapeamento; Endereço de Execução e Método de Autenticação ficam
    function limparMapeamento() {
        bolDescartarGravado = true;
        objTabelaHeaders.limpar();
        idHeaderAlterar = null;
        $('#txtAtributoHeader, #txtConteudoHeader').val('');
        document.getElementById('chkDadoRestrito').checked = false;
        document.getElementById('chkSinCache').checked = false;
        document.getElementById('txtTokenAutenticacao').value = '';
        $('#selOperacaoWsdl, #selCachePrazoExpiracao, #selCacheDataArmazenamento, select[id^="nomeFuncionalDadosEntrada_"], select[id^="nomeFuncionalDadosSaida_"]').each(function () {
            this.options.length = 0;
        });
        $('#paramEntradaTable_periodoCache').css('display', 'none');
        $('#blcOperacaoWs, #blcCacheWs, #blcEntradaWs, #blcSaidaWs').css('display', 'none');
    }

    function validarWsdl(bolLimpar = false) {

        var enderecoWsdl = document.getElementById('txtEnderecoWsdl').value;
        var versao = document.getElementById('selNuVersao').value;

        if (enderecoWsdl == '') {
            alert(isRest() ? 'Preencha o campo Endereço do Documento OpenAPI.' : 'Preencha o campo Endereço WSDL.');
            return false;
        }

        if (bolLimpar) {
            limparMapeamento();
        }

        $.ajax({
            type: "POST",
            url: "<?= $strLinkAjaxValidarWsdl ?>",
            dataType: "xml",
            data: {
                endereco_wsdl: enderecoWsdl,
                versao: versao,
                tipo: isRest() ? 'R' : 'S'
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
                    var selectedValor = bolDescartarGravado ? '' : '<?= PaginaSEI::tratarHTML($objMdPetIntegracaoDTO->getStrOperacaoWsdl());?>';
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

                    var sugestao = $(result).find('sugestao').text();
                    if (sugestao != '' && infraTrim(document.getElementById('txtEnderecoExecucao').value) == '') {
                        document.getElementById('txtEnderecoExecucao').value = sugestao;
                    }

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
        var versao = document.getElementById('selNuVersao').value;

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
                tipo_parametro: tipo_parametro,
                versao: versao,
                tipo: isRest() ? 'R' : 'S'
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
                    if (bolDescartarGravado) {
                        selectedValor = '';
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
                        alert('Erro ao montar parâmetros.');
                        //document.getElementById('gridOperacao').style.display = "none";
                    }

                }else {

                    var arraySelect = new Array();
                    if (tipo_parametro == 'e') {
                        arraySelect.push('selParametrosE');
                        arraySelect.push('nomeFuncionalDadosEntrada_cnpjEmpresa');
                        arraySelect.push('nomeFuncionalDadosEntrada_cpfPessoa');
                        arraySelect.push('nomeFuncionalDadosEntrada_cpfUsuario');
                        arraySelect.push('nomeFuncionalDadosEntrada_identificacaoOrigem');
                        arraySelect.push('nomeFuncionalDadosEntrada_<?= MdPetIntegFabricaRN::ENTRADA_TOKEN ?>');
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
        infraExibirAviso(false);
        if (infraTrim(document.getElementById('selOperacaoWsdl').value) == '') {
            alert('Informe Operação.');
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
        $('#selCachePrazoExpiracao').attr('required', objeto.checked);
        infraAvisoCancelar();
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
        $('#nomeFuncionalDadosSaida_numero').attr('required', objeto.checked && staUtilizaWs);
    }

    function complementoMarcaDesmarca(objeto) {
        if (objeto.checked) {
            document.getElementById('paramSaidaTable_complemento').style.display = '';
        } else {
            document.getElementById('paramSaidaTable_complemento').style.display = 'none';
        }
    }


    function operacaoSelecionar() {
        let checkbox = document.getElementById('chkSinCache');
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
                document.getElementById('lbltxtCodRFBSuspensaoAutomatica').innerText = 'Códigos de Situação Cadastral na Receita para Suspender Usuários Externos';
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
            } else if(select == 'nomeFuncionalDadosEntrada_cpfUsuario' && tipo_parametro == 'e') {
                selectedValor = '<?php echo $strItensSelCpfUsuario; ?>';
            } else {
                var arrayNome = select.split('_');
                $.each(dados, function (chave, item) {
                    if (item['nome'] == arrayNome[1]) {
                        selectedValor = item['campo_nome'];
                    }
                });
            }

            if (bolDescartarGravado) {
                selectedValor = '';
            }

            if (key != "endereco") {

                if (tipo_parametro == 'e') {
                    var opt = document.createElement('option');
                    opt.setAttribute('data-local', $(value).attr('local') || '');
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
