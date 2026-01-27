<script type="text/javascript">
    //Processo
    var objLupaTipoProcesso = null;
    var objAutoCompletarTipoProcesso = null;

    //Processo PF
    var objLupaTipoProcessoPF = null;
    var objAutoCompletarTipoProcessoPF = null;

    //Docs
    var objLupaTipoDocumento = null;
    var objAutoCompletarTipoDocumento = null;

    var objLupaTipoDocPrinc = null;
    var objAutoCompletarTipoDocPrinc = null;

    var objLupaTipoDocumentoEssencial = null;
    var objAutoCompletarTipoDocumentoEssencial = null;

    //Unidades
    var objLupaUnidade = null;
    var objAutoCompletarUnidade = null;

    var objLupaUnidadeMultipla = null;
    var objAutoCompletarUnidadeMutipla = null;

    //Unidades PF
    var objLupaUnidadePF = null;
    var objAutoCompletarUnidadePF = null;


    function removerUnidade(idObj) {

        document.getElementById(idObj).remove();
        qtdLinhas = document.getElementsByClassName('linhas').length;
        document.getElementById('qtdRegistros').innerHTML = qtdLinhas;

        if (qtdLinhas == 0) {
            document.getElementById('divTableMultiplasUnidades').style.display = "none";
        }

    }

    function registroDuplicado(uf) {
        var todasUfs = document.getElementsByClassName('ufsSelecionadas');
        var ufAdd = (uf.trim()).toUpperCase();

        if (todasUfs.length > 0) {
            for (i = 0; i < todasUfs.length; i++) {
                var ufGrid = ((todasUfs[i].innerHTML).trim()).toUpperCase();
                if (ufGrid == ufAdd) {
                    alert('Não é permitido adicionar mais de uma Unidade de abertura para a mesma UF.');
                    return true;
                }
            }
        }

        return false;
    }


    function removerProcessoAssociado(remover) {

        document.getElementById('selNivelAcesso').innerHTML = '';
        document.getElementById('divHipoteseLegal').style.display = "none";
        console.log(remover);
        if (remover === '1') {
            objLupaTipoProcesso.remover();
        }
    }

    function removerProcessoAssociadoPF(remover) {

        document.getElementById('selNivelAcesso').innerHTML = '';
        document.getElementById('divHipoteseLegal').style.display = "none";
        console.log(remover);
        if (remover === '1') {
            objLupaTipoProcessoPF.remover();
        }
    }

    function changeNivelAcesso() {

        document.getElementById('divNivelAcesso').style.display = "none";
        var padrao = document.getElementsByName('rdNivelAcesso[]')[1].checked;

        document.getElementById('selNivelAcesso').value = '';
        document.getElementById('selHipoteseLegal').value = '';
        //document.getElementById('divHipoteseLegal').style.display = 'none';

        if (padrao) {
            document.getElementById('divNivelAcesso').style.display = "inherit";
        } else {
            document.getElementById('divHipoteseLegal').style.display = 'none';
        }

    }

    function changeSelectNivelAcesso() {
        document.getElementById('selHipoteseLegal').value = '';

        var valorSelectNivelAcesso = document.getElementById('selNivelAcesso').value;
        var valorHipoteseLegal = document.getElementById('hdnParametroHipoteseLegal').value;

        if (valorSelectNivelAcesso == '<?= ProtocoloRN::$NA_RESTRITO ?>' && valorHipoteseLegal != '0') {
            document.getElementById('divHipoteseLegal').style.display = 'inherit';

        } else {
            document.getElementById('divHipoteseLegal').style.display = 'none';

        }
    }


    function changeDocPrincipal() {

        var gerado = document.getElementsByName('rdDocPrincipal[]')[0].checked;
        var tipo = '';

        document.getElementById('divDocPrincipal').style.display = "inherit";
        //document.getElementById('fldDocObrigatorio').style.display = "inherit";
        document.getElementById('fldDocComplementar').style.display = "inherit";

        if (objLupaTipoDocPrinc != null) {
            objLupaTipoDocPrinc.remover();
        }

        if (gerado) {
            tipo = 'G';
            document.getElementById('txtTipoDocPrinc').value = '';
            document.getElementsByName("rdDocPrincipal[]")[0].focus();
        } else {
            tipo = 'E';
            document.getElementById('txtTipoDocPrinc').value = '';
            document.getElementsByName("rdDocPrincipal[]")[1].focus();
        }

        carregarComponenteAutoCompleteTpDocPrinc(tipo);


        //rdDocPrincipal
    }

    function changeDocPrincipalEdicao() {

        var gerado = document.getElementsByName('rdDocPrincipal[]')[0].checked;
        var tipo = '';

        document.getElementById('divDocPrincipal').style.display = "inherit";

        if (gerado) {
            tipo = 'G';
            document.getElementsByName("rdDocPrincipal[]")[0].focus();
        } else {
            tipo = 'E';
            document.getElementsByName("rdDocPrincipal[]")[1].focus();
        }

        carregarComponenteAutoCompleteTpDocPrinc(tipo);

    }


    function inicializar() {

        carregarComponenteTipoDocumento(); //Doc Complementares - Seleção Múltipla
        carregarComponenteTipoProcesso(); // Seleção Única
        carregarComponenteTipoProcessoPF();
        carregarComponenteUnidade();  // Seleção Única
        carregarComponenteUnidadePF();
        carregarComponenteTipoDocumentoEssencial(); // Seleção Múltipla
        carregarDependenciaNivelAcesso();
        carregarHipoteseLegal();
        infraEfeitoTabelas();


    }

    function carregarHipoteseLegal() {
        var parametroHipoteseLegal = document.getElementById('hdnParametroHipoteseLegal').value;
        if (parametroHipoteseLegal == '' || parametroHipoteseLegal == 0) {
            document.getElementById('divHipoteseLegal').style.display = 'none';
        }
    }

    function carregarDependenciaNivelAcesso() {
        //Ajax para carregar os niveis de acesso após a escolha do tipo de processo
        objAjaxIdNivelAcesso = new infraAjaxMontarSelectDependente('txtTipoProcesso', 'selNivelAcesso', '<?=$strLinkAjaxNivelAcesso?>');
        objAjaxIdNivelAcesso.prepararExecucao = function () {
            document.getElementById('selNivelAcesso').innerHTML = '';
            return infraAjaxMontarPostPadraoSelect('null', '', 'null') + '&idTipoProcesso=' + document.getElementById('hdnIdTipoProcesso').value;
        }
    }


    function carregarComponenteUnidade() {
        objLupaUnidade = new infraLupaText('txtUnidade', 'hdnIdUnidade', '<?=$strLinkUnidadeSelecao?>');

        objLupaUnidade.finalizarSelecao = function () {
            objAutoCompletarUnidade.selecionar(document.getElementById('hdnIdUnidade').value, document.getElementById('txtUnidade').value);
        };


        objAutoCompletarUnidade = new infraAjaxAutoCompletar('hdnIdUnidade', 'txtUnidade', '<?=$strLinkAjaxUnidade?>');
        objAutoCompletarUnidade.limparCampo = false;
        objAutoCompletarUnidade.tamanhoMinimo = 3;
        objAutoCompletarUnidade.prepararExecucao = function () {
            return 'palavras_pesquisa=' + document.getElementById('txtUnidade').value;
        };

        objAutoCompletarUnidade.processarResultado = function (id, descricao, complemento) {
            if (id != '') {
                document.getElementById('hdnIdUnidade').value = id;
                document.getElementById('txtUnidade').value = descricao;
            }
        }
        objAutoCompletarUnidade.selecionar('<?=$strIdUnidade?>', '<?=PaginaSEI::getInstance()->formatarParametrosJavascript($strNomeRemetente);?>');
    }

    function carregarComponenteUnidadePF() {
        objLupaUnidadePF = new infraLupaText('txtUnidadePF', 'hdnIdUnidadePF', '<?=$strLinkUnidadePFSelecao?>');

        objLupaUnidadePF.finalizarSelecao = function () {
            objAutoCompletarUnidadePF.selecionar(document.getElementById('hdnIdUnidadePF').value, document.getElementById('txtUnidadePF').value);
        };


        objAutoCompletarUnidadePF = new infraAjaxAutoCompletar('hdnIdUnidadePF', 'txtUnidadePF', '<?=$strLinkAjaxUnidade?>');
        objAutoCompletarUnidadePF.limparCampo = false;
        objAutoCompletarUnidadePF.tamanhoMinimo = 3;
        objAutoCompletarUnidadePF.prepararExecucao = function () {
            return 'palavras_pesquisa=' + document.getElementById('txtUnidadePF').value;
        };

        objAutoCompletarUnidadePF.processarResultado = function (id, descricao, complemento) {
            if (id != '') {
                document.getElementById('hdnIdUnidadePF').value = id;
                document.getElementById('txtUnidadePF').value = descricao;
            }
        }
        objAutoCompletarUnidadePF.selecionar('<?=$strIdUnidade?>', '<?=PaginaSEI::getInstance()->formatarParametrosJavascript($strNomeRemetente);?>');
    }

    function carregarComponenteLupaTpDocPrinc(acaoComponente) {

        var gerado = document.getElementsByName('rdDocPrincipal[]')[0].checked;
        var tipo = gerado ? 'G' : 'E';
        var link = '<?= $strLinkTipoDocPrincExternoSelecao ?>';

        if (gerado) {
            link = '<?= $strLinkTipoDocPrincGeradoSelecao ?>';
        }

        objLupaTipoDocPrinc = new infraLupaText('txtTipoDocPrinc', 'hdnIdTipoDocPrinc', link);

        objLupaTipoDocPrinc.finalizarSelecao = function () {
            objAutoCompletarTipoDocPrinc.selecionar(document.getElementById('hdnIdTipoDocPrinc').value, document.getElementById('txtTipoDocPrinc').value);
        }

        acaoComponente == 'S' ? objLupaTipoDocPrinc.selecionar(700, 500) : objLupaTipoDocPrinc.remover();
    }

    function carregarComponenteLupaTpDocComplementar(acaoComponente) {
        acaoComponente == 'S' ? objLupaTipoDocumento.selecionar(700, 500) : objLupaTipoDocumento.remover();
    }

    function returnLinkModificado(link, tipo) {
        var arrayLink = link.split('&filtro=1');

        var linkFim = '';
        if (arrayLink.length == 2) {
            linkFim = arrayLink[0] + '&filtro=1&tipoDoc=' + tipo + arrayLink[1];
        } else {
            linkFim = link;
        }

        return linkFim;
    }


    function carregarComponenteAutoCompleteTpDocPrinc(tipo) {

        objAutoCompletarTipoDocPrinc = new infraAjaxAutoCompletar('hdnIdTipoDocPrinc', 'txtTipoDocPrinc', '<?=$strLinkAjaxTipoDocPrinc?>');
        objAutoCompletarTipoDocPrinc.limparCampo = true;

        objAutoCompletarTipoDocPrinc.prepararExecucao = function () {
            return 'palavras_pesquisa=' + document.getElementById('txtTipoDocPrinc').value + '&tipoDoc=' + tipo;
        };

        objAutoCompletarTipoDocPrinc.processarResultado = function (id, descricao, complemento) {
            if (id != '') {
                document.getElementById('hdnIdTipoDocPrinc').value = id;
                document.getElementById('txtTipoDocPrinc').value = descricao;
            }
        }
        objAutoCompletarTipoDocPrinc.selecionar('<?=$strIdTipoDocPrinc?>', '<?=PaginaSEI::getInstance()->formatarParametrosJavascript($strNomeRemetente);?>');
    }


    function carregarComponenteTipoProcesso() {

        objLupaTipoProcesso = new infraLupaText('txtTipoProcesso', 'hdnIdTipoProcesso', '<?=$strLinkTipoProcessoSelecao?>');

        objLupaTipoProcesso.finalizarSelecao = function () {
            objAutoCompletarTipoProcesso.selecionar(document.getElementById('hdnIdTipoProcesso').value, document.getElementById('txtTipoProcesso').value);
            objAjaxIdNivelAcesso.executar();
        }
        objAutoCompletarTipoProcesso = new infraAjaxAutoCompletar('hdnIdTipoProcesso', 'txtTipoProcesso', '<?=$strLinkAjaxTipoProcesso?>');
        objAutoCompletarTipoProcesso.tamanhoMinimo = 3;
        objAutoCompletarTipoProcesso.limparCampo = false;

        objAutoCompletarTipoProcesso.prepararExecucao = function () {
            return 'palavras_pesquisa=' + document.getElementById('txtTipoProcesso').value;
        };

        objAutoCompletarTipoProcesso.processarResultado = function (id, descricao, complemento) {
            if (id != '') {
                document.getElementById('hdnIdTipoProcesso').value = id;
                document.getElementById('txtTipoProcesso').value = descricao;
                objAjaxIdNivelAcesso.executar();
            }
        };

        objAutoCompletarTipoProcesso.selecionar('<?=$strIdTipoProcesso?>', '<?=PaginaSEI::getInstance()->formatarParametrosJavascript($strNomeRemetente);?>');

    }

    function carregarComponenteTipoProcessoPF() {

        objLupaTipoProcessoPF = new infraLupaText('txtTipoProcessoPF', 'hdnIdTipoProcessoPF', '<?=$strLinkTipoProcessoPFSelecao?>');

        objLupaTipoProcessoPF.finalizarSelecao = function () {
            objAutoCompletarTipoProcessoPF.selecionar(document.getElementById('hdnIdTipoProcessoPF').value, document.getElementById('txtTipoProcessoPF').value);
            objAjaxIdNivelAcesso.executar();
        }
        objAutoCompletarTipoProcessoPF = new infraAjaxAutoCompletar('hdnIdTipoProcessoPF', 'txtTipoProcessoPF', '<?=$strLinkAjaxTipoProcesso?>');
        objAutoCompletarTipoProcessoPF.tamanhoMinimo = 3;
        objAutoCompletarTipoProcessoPF.limparCampo = false;

        objAutoCompletarTipoProcessoPF.prepararExecucao = function () {
            return 'palavras_pesquisa=' + document.getElementById('txtTipoProcessoPF').value;
        };

        objAutoCompletarTipoProcessoPF.processarResultado = function (id, descricao, complemento) {
            if (id != '') {
                document.getElementById('hdnIdTipoProcessoPF').value = id;
                document.getElementById('txtTipoProcessoPF').value = descricao;
                objAjaxIdNivelAcesso.executar();
            }
        };

        objAutoCompletarTipoProcessoPF.selecionar('<?=$strIdTipoProcesso?>', '<?=PaginaSEI::getInstance()->formatarParametrosJavascript($strNomeRemetente);?>');

    }

    //Carrega o documento para o documento complementar
    function carregarComponenteTipoDocumento() {

        objAutoCompletarTipoDocumento = new infraAjaxAutoCompletar('hdnIdSerie', 'txtSerie', '<?=$strLinkAjaxTipoDocumento?>');
        objAutoCompletarTipoDocumento.limparCampo = true;
        objAutoCompletarTipoDocumento.tamanhoMinimo = 3;
        objAutoCompletarTipoDocumento.prepararExecucao = function () {
            //var tipo   = gerado ? 'G' : 'E';
            //20160908 - Essencial e Complementar SEMPRE EXTERNO
            var tipo = 'E';
            return 'palavras_pesquisa=' + document.getElementById('txtSerie').value + '&tipoDoc=' + tipo;
        };

        objAutoCompletarTipoDocumento.processarResultado = function (id, nome, complemento) {

            if (id != '') {
                var options = document.getElementById('selDescricao').options;

                if (options != null) {
                    for (var i = 0; i < options.length; i++) {
                        if (options[i].value == id) {
                            alert('Tipo de Documento já consta na lista.');
                            break;
                        }
                    }
                }

                if (i == options.length) {

                    for (i = 0; i < options.length; i++) {
                        options[i].selected = false;
                    }

                    opt = infraSelectAdicionarOption(document.getElementById('selDescricao'), nome, id);

                    objLupaTipoDocumento.atualizar();

                    opt.selected = true;
                }

                document.getElementById('txtSerie').value = '';
                document.getElementById('txtSerie').focus();

            }
        };

        objLupaTipoDocumento = new infraLupaSelect('selDescricao', 'hdnSerie', '<?=$strLinkTipoDocumentoSelecao?>');
    }

    //Carrega o documento para o documento essencial
    function carregarComponenteTipoDocumentoEssencial() {

        objAutoCompletarTipoDocumentoEssencial = new infraAjaxAutoCompletar('hdnIdSerieEssencial', 'txtSerieEssencial', '<?=$strLinkAjaxTipoDocumento?>');
        objAutoCompletarTipoDocumentoEssencial.limparCampo = true;
        objAutoCompletarTipoDocumentoEssencial.tamanhoMinimo = 3;
        objAutoCompletarTipoDocumentoEssencial.prepararExecucao = function () {
            var tipo = 'E';
            return 'palavras_pesquisa=' + document.getElementById('txtSerieEssencial').value + '&tipoDoc=' + tipo;
        };

        objAutoCompletarTipoDocumentoEssencial.processarResultado = function (id, nome, complemento) {

            if (id != '') {
                var options = document.getElementById('selDescricaoEssencial').options;

                if (options != null) {
                    for (var i = 0; i < options.length; i++) {
                        if (options[i].value == id) {
                            alert('Tipo de Documento já consta na lista.');
                            break;
                        }
                    }
                }

                if (i == options.length) {

                    for (i = 0; i < options.length; i++) {
                        options[i].selected = false;
                    }

                    opt = infraSelectAdicionarOption(document.getElementById('selDescricaoEssencial'), nome, id);

                    objLupaTipoDocumentoEssencial.atualizar();

                    opt.selected = true;
                }

                document.getElementById('txtSerieEssencial').value = '';
                document.getElementById('txtSerieEssencial').focus();

            }
        };

        objLupaTipoDocumentoEssencial = new infraLupaSelect('selDescricaoEssencial', 'hdnSerieEssencial', '<?=$strLinkTipoDocumentoEssencialSelecao?>');
    }


    function validarCadastro() {

        var valorHipoteseLegal = document.getElementById('hdnParametroHipoteseLegal').value;

        //Pessoa Física
        var exibirMenuAcessoExternoPF = document.getElementById('rdExibirMenuAcessoExternoPF').checked;
        var naoExibirMenuAcessoExternoPF = document.getElementById('rdNaoExibirMenuAcessoExternoPF').checked;

        //Pessoa Jurídica
        var exibirMenuAcessoExterno = document.getElementById('rdExibirMenuAcessoExterno').checked;
        var naoExibirMenuAcessoExterno = document.getElementById('rdNaoExibirMenuAcessoExterno').checked;

        if (exibirMenuAcessoExternoPF == false && naoExibirMenuAcessoExternoPF == false) {
            alert('Informe sobre o menu Procuração Eletrônica da Pessoa Física.');
            document.getElementById('rdExibirMenuAcessoExternoPF').focus();
            return false;
        }

        if (exibirMenuAcessoExterno == false && naoExibirMenuAcessoExterno == false) {
            alert('Informe sobre o menu Procuração Eletrônica.');
            document.getElementById('rdNaoExibirMenuAcessoExterno').focus();
            return false;
        }

        //tratamento dos campos obrigatórios pessoa física
        /* if(exibirMenuAcessoExternoPF == true){
             if (infraTrim(document.getElementById('txtTipoProcessoPF').value) == '') {
                 alert('Informe o Tipo de Processo para abertura do processo para Pessoa Física.');
                 document.getElementById('txtTipoProcessoPF').focus();
                 return false;
             }
             if(document.getElementById('txtEspecProcPF').value == ""){
                 alert('Informe a Especificação do Processo.');
                 document.getElementById('txtTipoProcessoPF').focus();
                 return false;
             }
             vlUnidadePF = infraTrim(document.getElementById('hdnIdUnidadePF').value);
             if (vlUnidadePF == '' || vlUnidadePF == null) {
                 alert('Informe a Unidade para abertura do processo para Pessoa Física.');
                 document.getElementById('txtUnidadePF').focus();
                 return false;
             }
         }*/


        //tratamento dos campos obrigatórios pessoa jurídic
        //Validação para verificação no webservice


        //Validação Pessoa Física
        //var aviso = "Não foi possível habilitar a exibição do menu Responsável Legal no Acesso Externo. \n Para exibir o menu Responsável Legal de Pessoa Jurídica no Acesso Externo é necessário preencher os campos obrigatórios contidos em Configurações para Vinculação de Usuário Externo a Pessoa Jurídica e Configurações para Vinculação de Usuário Externo a Pessoa Física. Ainda, para exibir o menu nesse caso, necessariamente tem que selecionar acima para Exibir o menu de Procuração Eletrônica. ";

        if (infraTrim(document.getElementById('txtTipoProcessoPF').value) == '') {
            alert('Informe o Tipo de Processo para abertura do processo para Pessoa Física.');
            document.getElementById('txtTipoProcessoPF').focus();
            return false;
        }
        if (document.getElementById('txtEspecProcPF').value == "") {
            alert('Informe a Especificação do processo para Pessoa Física.');
            document.getElementById('txtEspecProcPF').focus();
            return false;
        } else {
            if (document.getElementById('txtEspecProcPF').value.length > 100) {
                alert('Tamanho do campo Especificação do Processo Pessoa Física excedido (máximo 100 caracteres).');
                document.getElementById('txtEspecProcPF').focus();
                return false;
            }
        }
        vlUnidadePF = infraTrim(document.getElementById('hdnIdUnidadePF').value);
        if (vlUnidadePF == '' || vlUnidadePF == null) {
            alert('Informe a Unidade para abertura do processo para Pessoa Física.');
            document.getElementById('txtUnidadePF').focus();
            return false;
        }

        if (infraTrim(document.getElementById('txtTipoProcesso').value) == '') {
            alert('Informe o Tipo de Processo para abertura do processo para Pessoa Jurídica.');
            document.getElementById('txtTipoProcesso').focus();
            return false;
        }
        if (document.getElementById('txtEspecProcPJ').value == "") {
            alert('Informe a Especificação do processo para Pessoa Jurídica.');
            document.getElementById('txtEspecProcPJ').focus();
            return false;
        } else {
            if (document.getElementById('txtEspecProcPJ').value.length > 100) {
                alert('Tamanho do campo Especificação do Processo Pessoa Jurídica excedido (máximo 100 caracteres).');
                document.getElementById('txtEspecProcPJ').focus();
                return false;
            }
        }
        vlUnidade = infraTrim(document.getElementById('hdnIdUnidade').value);
        if (vlUnidade == '' || vlUnidade == null) {
            alert('Informe a Unidade para abertura do processo para Pessoa Jurídica.');
            document.getElementById('txtUnidade').focus();
            return false;
        }

        //Validar Nível Acesso
        var elemsNA = document.getElementsByName("rdNivelAcesso[]");

        validoNA = false;
        for (var i = 0; i < elemsNA.length; i++) {
            if (elemsNA[i].checked === true) {
                validoNA = true;
            }
        }

        if (((infraTrim(document.getElementById('selNivelAcesso').value) == '') && document.getElementById('rdPadrao').checked) || (!validoNA)) {
            alert('Informe o Nível de Acesso para abertura do processo para Pessoa Jurídica.');
            document.getElementById('rdUsuExternoIndicarEntrePermitidos').focus();
            return false;
        } else {
            if (document.getElementById('selNivelAcesso').value == <?= ProtocoloRN::$NA_RESTRITO ?> && valorHipoteseLegal != '0') {

                //validar hipotese legal
                if (document.getElementById('selHipoteseLegal').value == '') {
                    alert('Informe a Hipótese legal padrão para abertura do processo para Pessoa Jurídica.');
                    document.getElementById('selHipoteseLegal').focus();
                    return false;
                }

            }
        }

        vlDocObrigatorio = document.getElementById('selDescricaoEssencial').options.length;
        if (vlDocObrigatorio == 0) {
            alert('Informe os Tipos dos Documentos de Atos Constitutivos Obrigatórios para abertura do processo para Pessoa Jurídica.');
            document.getElementById('selDescricaoEssencial').focus();
            return false;
        }

        if (exibirMenuAcessoExterno == true) {
            $.ajax({
                url: '<?=$strLinkAjaxWebServiceSalvar?>',
                type: 'POST',
                dataType: 'XML',
                async: false,
                success: function (result) {

                    if ($(result).find('valor').text() == 'N') {
                        alert('Não foi possível habilitar a exibição do menu Responsável Legal de Pessoa Jurídica no Acesso Externo.\n\n Acesse Administração >> Peticionamento Eletrônico >> Integrações >> Novo >> Funcionalidade: Consultar Dados CNPJ Receita Federal e preencha o Mapeamento da Integração com a Receita Federal para consultar os dados do CNPJ.');
                    }
                },
                error: function (e) {
                    console.error('Erro ao processar o XML do SEI: ' + e.responseText);
                }
            });
        }

        return true;
    }

    function OnSubmitForm() {
        return validarCadastro();
    }

    function getPercentTopStyle(element) {
        var parent = element.parentNode,
            computedStyle = getComputedStyle(element),
            value;
        parent.style.display = 'none';
        value = computedStyle.getPropertyValue('top');
        parent.style.removeProperty('display');

        if (value != '') {
            valor = value.replace('%', '');
            return parseInt(valor);
        }

        return false;
    }

</script>