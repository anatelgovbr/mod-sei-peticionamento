<script type="text/javascript">
    //Processo
    var objLupaTipoProcesso = null;
    var objAutoCompletarTipoProcesso = null;

    //Docs
    var objLupaTipoDocumento = null;
    var objAutoCompletarTipoDocumento = null;

    var objLupaTipoDocPrinc = null;
    var objAutoCompletarTipoDocPrinc = null;

    var objLupaTipoDocumentoEssencial = null
    var objAutoCompletarTipoDocumentoEssencial = null;

    //Unidades
    var objLupaUnidade = null;
    var objAutoCompletarUnidade = null;

    var objLupaUnidadeMultipla = null;
    var objAutoCompletarUnidadeMutipla = null;

    //Orgao
    var objLupaOrgaoUnidadeMultipla = null;
    var objAutoCompletarOrgaoUnidadeMutipla = null;

    function criarLupaUnidade() {
        if (document.getElementById('hdnIdOrgaoUnidadeMultipla').value != '') {
            objLupaUnidadeMultipla = null;
            objAutoCompletarUnidadeMutipla = null;
            var link = document.getElementById('lnkOrgao' + document.getElementById('hdnIdOrgaoUnidadeMultipla').value).value;
            objLupaUnidadeMultipla = new infraLupaText('txtUnidadeMultipla', 'hdnIdUnidadeMultipla', link);

            objLupaUnidadeMultipla.finalizarSelecao = function () {
                objAutoCompletarUnidadeMultipla.selecionar(document.getElementById('hdnIdUnidadeMultipla').value, document.getElementById('txtUnidadeMultipla').value);
            }

            objAutoCompletarUnidadeMultipla = new infraAjaxAutoCompletar('hdnIdUnidadeMultipla', 'txtUnidadeMultipla', '<?= SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=unidade_auto_completar_todas'); ?>');
            objAutoCompletarUnidadeMultipla.limparCampo = true;
            objAutoCompletarUnidadeMultipla.tamanhoMinimo = 3;
            objAutoCompletarUnidadeMultipla.prepararExecucao = function () {
                return 'palavras_pesquisa=' + document.getElementById('txtUnidadeMultipla').value + '&id_orgao=' + document.getElementById('hdnIdOrgaoUnidadeMultipla').value;

            };

            objAutoCompletarUnidadeMultipla.processarResultado = function (id, descricao, uf) {
                if (id != '') {
                    document.getElementById('hdnIdUnidadeMultipla').value = id;
                    document.getElementById('txtUnidadeMultipla').value = descricao;
                    document.getElementById('hdnUfUnidadeMultipla').value = uf;
                }
            }
        } else {
            objLupaUnidadeMultipla = null;
            objAutoCompletarUnidadeMutipla = null;
        }
        document.getElementById('hdnIdUnidadeMultipla').value = '';
        document.getElementById('txtUnidadeMultipla').value = '';
        document.getElementById('hdnUfUnidadeMultipla').value = '';
    }

    function addUnidade() {
        var idUnidadeSelect = document.getElementById('hdnIdUnidadeMultipla').value;

        if (idUnidadeSelect != '') {
            var paramsAjax = {
                idTipoProcesso: document.getElementById('hdnIdTipoProcesso').value,
                idOrgaoUnidadeMultipla: document.getElementById('hdnIdOrgaoUnidadeMultipla').value,
                idUnidadeMultipla: document.getElementById('hdnIdUnidadeMultipla').value
            };

            $.ajax({
                url: '<?=$strLinkAjaxConfirmaRestricao?>',
                type: 'POST',
                dataType: 'XML',
                data: paramsAjax,
                success: function (result) {
                    if ($(result).find('valor').text() == 'A') {
                        var idLinhaTabela = 'tabNomeUnidade_' + idUnidadeSelect;
                        var existeUnidade = document.getElementById(idLinhaTabela);
                        var valueCodUnidades = document.getElementById('hdnTodasUnidades').value;

                        if (valueCodUnidades != '') {
                            var objUnidades = $.parseJSON(valueCodUnidades);

                            $.ajax({
                                url: '<?=$strLinkAjaxRetornaDadosUnidade?>',
                                type: 'POST',
                                dataType: 'XML',
                                data: { 'idUnidadeMultipla': idUnidadeSelect },
                                success: function (result) {
                                    if (!registroDuplicado($(result).find('siglaOrgao').text(), $(result).find('cidade').text())) {
                                        qtdLinhas = document.getElementsByClassName('linhas').length;
                                        var html = '';
                                        if (qtdLinhas > 0) {
                                            html = document.getElementById('corpoTabela').innerHTML;
                                        }

                                        html += '<tr class="infraTrClara linhas" id="' + idLinhaTabela + '"><td>';
                                        html += '<a alt="' + $(result).find('descricaoOrgao').text() + '" title="' + $(result).find('descricaoOrgao').text() + '" class="ancoraSigla">' + $(result).find('siglaOrgao').text() + '</a>';
                                        html += '</td><td>';
                                        html += '<a alt="' + $(result).find('descricaoUnidade').text() + '" title="' + $(result).find('descricaoUnidade').text() + '" class="ancoraSigla">' + $(result).find('siglaUnidade').text() + '</a>';
                                        html += '<td class="ufsSelecionadas">' + $(result).find('uf').text() + '</td>';
                                        html += '<td class="ufsSelecionadas">' + $(result).find('cidade').text() + '</td>';
                                        html += '<td align="center">';
                                        html += '<a><img class="infraImg" title="Remover Unidade" alt="Remover Unidade" src="<?= PaginaSEI::getInstance()->getDiretorioImagensGlobal() ?>/remover.gif" onclick="removerUnidade(\'' + idLinhaTabela + '\');" id="imgExcluirProcessoSobrestado"></a></td></tr>';

                                        //Adiciona Conteúdo da Tabela no HTML
                                        document.getElementById('corpoTabela').innerHTML = '';
                                        document.getElementById('corpoTabela').innerHTML = html;

                                        // Mostra a tabela
                                        document.getElementById('divTableMultiplasUnidades').style.display = "inherit";

                                        //Zera os campos, após adicionar
                                        document.getElementById('txtUnidadeMultipla').value = '';
                                        document.getElementById('hdnIdUnidadeMultipla').value = '';
                                        document.getElementById('txtOrgaoUnidadeMultipla').value = '';
                                        document.getElementById('hdnIdOrgaoUnidadeMultipla').value = '';

                                        document.getElementById('qtdRegistros').innerHTML = qtdLinhas + 1;
                                    }
                                },
                                error: function (e) {
                                    console.error('Erro ao buscar os dados da unidade: ' + e.responseText);
                                }
                            });
                        }
                    } else {
                        alert('Esta Unidade não pode utilizar o Tipo de Processo indicado, em razão de restrição de uso do Tipo de Processo configurado pela Administração do SEI. \n\nCaso seja pertinente, antes deve ampliar as restrições de uso do Tipo de Processo para adicionar esta Unidade, no menu Administração > Tipos de Processos > Listar.');
                        return false;
                    }
                },
                error: function (e) {
                    console.error('Erro ao processar o XML do SEI: ' + e.responseText);
                }
            });
        }
    }

    function verificarOrgaoSelecionado() {
        if (document.getElementById('hdnIdOrgaoUnidadeMultipla').value == '') {
            alert('Nenhum Órgão selecionado.');
            return false;
        } else {
            objLupaUnidadeMultipla.selecionar(700, 500);
        }
    }

    function removerUnidade(idObj) {

        document.getElementById(idObj).remove();
        qtdLinhas = document.getElementsByClassName('linhas').length;
        document.getElementById('qtdRegistros').innerHTML = qtdLinhas;

        if (qtdLinhas == 0) {
            document.getElementById('divTableMultiplasUnidades').style.display = "none";
        }

    }

    function registroDuplicado(orgao, cidade) {
        for (var i = 0; i < document.getElementById('corpoTabela').rows.length; i++) {
            var linha = document.getElementById('corpoTabela').rows[i];
            if (linha.cells[0].innerText.toLowerCase().trim() == orgao.toLowerCase().trim()
                && linha.cells[3].innerText.toLowerCase().trim() == cidade.toLowerCase().trim()) {
                alert('Não é permitido adicionar mais de uma Unidade para abertura do mesmo Órgão e para a mesma Cidade.');
                return true;
            }
        }
        return false;
    }


    function changeUnidade() {
        //Limpando tabela de unidades Múltiplas e campos vinculados as unidades multiplas
        document.getElementById("corpoTabela").innerHTML = '';
        document.getElementById('txtUnidadeMultipla').value = '';
        document.getElementById('hdnIdUnidadeMultipla').value = '';
        document.getElementById('txtOrgaoUnidadeMultipla').value = '';
        document.getElementById('hdnIdOrgaoUnidadeMultipla').value = '';
        document.getElementById('divTableMultiplasUnidades').style.display = "none";

        //Limpando campos vinculados a unidade Única
        document.getElementById("txtUnidade").value = '';
        document.getElementById("hdnIdUnidade").value = '';

        var unidUnic = document.getElementsByName('rdUnidade[]')[0].checked;

        document.getElementById("divCpUnidadeUnica").style.display = "none";
        document.getElementById("divCpUnidadeMultipla").style.display = "none";

        unidUnic ? document.getElementById("divCpUnidadeUnica").style.display = "inherit" : document.getElementById("divCpUnidadeMultipla").style.display = "inherit";
    }

    function changeUnidadeTipoProcesso() {
        //Limpando tabela de unidades Múltiplas e campos vinculados as unidades multiplas
        document.getElementById("corpoTabela").innerHTML = '';
        document.getElementById('txtUnidadeMultipla').value = '';
        document.getElementById('hdnIdUnidadeMultipla').value = '';
        document.getElementById('txtOrgaoUnidadeMultipla').value = '';
        document.getElementById('hdnIdOrgaoUnidadeMultipla').value = '';
        document.getElementById('divTableMultiplasUnidades').style.display = "none";

        //Limpando campos vinculados a unidade Única
        document.getElementById("txtUnidade").value = '';
        document.getElementById("hdnIdUnidade").value = '';

        document.getElementById("rdUnidadeUnica").checked = false;
        document.getElementById("rdUnidadeMultipla").checked = false;

        document.getElementById("divCpUnidadeUnica").style.display = "none";
        document.getElementById("divCpUnidadeMultipla").style.display = "none";
    }

    function changeIndicacaoInteressado() {
        var indIndireta = document.getElementsByName('indicacaoInteressado[]')[1].checked;
        document.getElementById('divRdIndicacaoIndiretaHide').style.display = "none";

        document.getElementsByName('indicacaoIndireta[]')[0].checked = false;
        document.getElementsByName('indicacaoIndireta[]')[0].checked = '';

        document.getElementsByName('indicacaoIndireta[]')[1].checked = false;
        document.getElementsByName('indicacaoIndireta[]')[1].checked = '';

        var elementLupa = document.getElementById('imgLupaTipoDocumento');
        var percentLupa = getPercentTopStyle(elementLupa);

        if (indIndireta) {
            document.getElementById('divRdIndicacaoIndiretaHide').style.display = "inherit";
        }

    }

    function removerProcessoAssociado(remover) {

        document.getElementById('selNivelAcesso').innerHTML = '';
        document.getElementById('divHipoteseLegal').style.display = "none";
        console.log(remover);
        if (remover === '1') {
            objLupaTipoProcesso.remover();
        }
    }

    function changeNivelAcesso() {

        document.getElementById('divNivelAcesso').style.display = "none";
        var padrao = document.getElementsByName('rdNivelAcesso[]')[1].checked;

        document.getElementById('selNivelAcesso').value = '';
        document.getElementById('selNivelAcesso').value = '';
        document.getElementById('selHipoteseLegal').value = '';
        document.getElementById('divHipoteseLegal').style.display = 'none';

        if (padrao) {
            document.getElementById('divNivelAcesso').style.display = "inherit";
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
        var externo = document.getElementsByName('rdDocPrincipal[]')[1].checked;
        var formulario = document.getElementsByName('rdDocPrincipal[]')[2].checked;
        var tipo = '';

        document.getElementById('divDocPrincipal').style.display = "inherit";
        document.getElementById('fldDocEssenciais').style.display = "inherit";
        document.getElementById('fldDocComplementar').style.display = "inherit";

        if (objLupaTipoDocPrinc != null) {
            objLupaTipoDocPrinc.remover();
        }

        if (gerado) {
            tipo = 'G';
            document.getElementById('txtTipoDocPrinc').value = '';
            document.getElementsByName("rdDocPrincipal[]")[0].focus();
        }
        if (externo) {
            tipo = 'E';
            document.getElementById('txtTipoDocPrinc').value = '';
            document.getElementsByName("rdDocPrincipal[]")[1].focus();
        }
        if (formulario) {
            tipo = 'F';
            document.getElementById('txtTipoDocPrinc').value = '';
            document.getElementsByName("rdDocPrincipal[]")[2].focus();
        }

        carregarComponenteAutoCompleteTpDocPrinc(tipo);


        //rdDocPrincipal
    }

    function changeDocPrincipalEdicao() {

        var gerado = document.getElementsByName('rdDocPrincipal[]')[0].checked;
        var externo = document.getElementsByName('rdDocPrincipal[]')[1].checked;
        var formulario = document.getElementsByName('rdDocPrincipal[]')[2].checked;

        var tipo = '';

        document.getElementById('divDocPrincipal').style.display = "inherit";

        if (gerado) {
            tipo = 'G';
            document.getElementsByName("rdDocPrincipal[]")[0].focus();
        }
        if (externo) {
            tipo = 'E';
            document.getElementsByName("rdDocPrincipal[]")[1].focus();
        }
        if (formulario) {
            tipo = 'F';
            document.getElementsByName("rdDocPrincipal[]")[2].focus();
        }

        carregarComponenteAutoCompleteTpDocPrinc(tipo);

    }


    function inicializar() {

        inicializarTela();
        verificarQtdRegistrosUndMultipla();

        if ('<?= $_GET['acao'] ?>' != 'md_pet_tipo_processo_consultar') {
            carregarComponenteTipoDocumento(); //Doc Complementares - Seleção Múltipla
            carregarComponenteTipoProcesso(); // Seleção Única
            carregarComponenteUnidade();  // Seleção Única
            carregarComponenteUnidadeMultipla(); // Seleção única (Múltipla Tabela)
            carregarComponenteOrgaoMultiplo(); // Seleção única (Múltipla Tabela)
            carregarComponenteTipoDocumentoEssencial(); // Seleção Múltipla
            carregarDependenciaNivelAcesso();
        }


        if ('<?= $_GET['acao'] ?>' == 'md_pet_tipo_processo_cadastrar') {
            document.getElementById('txtTipoProcesso').focus();
        } else if ('<?= $_GET['acao'] ?>' == 'md_pet_tipo_processo_consultar') {
            infraDesabilitarCamposAreaDados();
            var itemRestricao = document.getElementsByClassName('alertaRestricao');
            for (i = 0; i < itemRestricao.length; i++) {
                itemRestricao[i].removeAttribute('style');
            }
            var itemDivergencia = document.getElementsByClassName('alertaDivergencia');
            for (i = 0; i < itemDivergencia.length; i++) {
                itemDivergencia[i].removeAttribute('style');
            }
        } else {
            document.getElementById('btnCancelar').focus();
        }
        infraEfeitoTabelas();

        if ('<?= $_GET['acao'] ?>' == 'md_pet_tipo_processo_alterar') {
            changeDocPrincipalEdicao();
        }

    }

    function verificarQtdRegistrosUndMultipla() {
        var multiplasUnidades = document.getElementById('rdUnidadeMultipla').checked;

        if (multiplasUnidades) {
            var qtdRegistros = document.getElementById('qtdRegistros').innerHTML;
            var linhas = (document.getElementsByClassName('linhas')).length;
            if (qtdRegistros != linhas) {
                document.getElementById('qtdRegistros').innerHTML = linhas;
            }
        }
    }

    function carregarDependenciaNivelAcesso() {
        //Ajax para carregar os niveis de acesso após a escolha do tipo de processo
        objAjaxIdNivelAcesso = new infraAjaxMontarSelectDependente('txtTipoProcesso', 'selNivelAcesso', '<?= $strLinkAjaxNivelAcesso ?>');
        objAjaxIdNivelAcesso.prepararExecucao = function () {
            document.getElementById('selNivelAcesso').innerHTML = '';
            return infraAjaxMontarPostPadraoSelect('null', '', 'null') + '&idTipoProcesso=' + document.getElementById('hdnIdTipoProcesso').value;
        }
    }

    function inicializarTela() {
    }

    function carregarComponenteUnidadeMultipla() {
        objLupaUnidadeMultipla = new infraLupaText('txtUnidadeMultipla', 'hdnIdUnidadeMultipla', '');

        objLupaUnidadeMultipla.finalizarSelecao = function () {
            objAutoCompletarUnidadeMultipla.selecionar(document.getElementById('hdnIdUnidadeMultipla').value, document.getElementById('txtUnidadeMultipla').value);
        }

        objAutoCompletarUnidadeMultipla = new infraAjaxAutoCompletar('hdnIdUnidadeMultipla', 'txtUnidadeMultipla', '<?= SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=unidade_auto_completar_todas'); ?>');
        objAutoCompletarUnidadeMultipla.limparCampo = false;
        objAutoCompletarUnidadeMultipla.tamanhoMinimo = 3;
        objAutoCompletarUnidadeMultipla.prepararExecucao = function () {
            if (document.getElementById('hdnIdOrgaoUnidadeMultipla').value == '') {
                alert('Nenhum Órgão selecionado.');
                document.getElementById('txtUnidadeMultipla').value = '';
                return false;
            }
            return 'palavras_pesquisa=' + document.getElementById('txtUnidadeMultipla').value + '&id_orgao=' + document.getElementById('hdnIdOrgaoUnidadeMultipla').value;
        };

        objAutoCompletarUnidadeMultipla.processarResultado = function (id, descricao, uf) {
            if (id != '') {
                document.getElementById('hdnIdUnidadeMultipla').value = id;
                document.getElementById('txtUnidadeMultipla').value = descricao;
                document.getElementById('hdnUfUnidadeMultipla').value = uf;
            }
        }
    }

    function carregarComponenteOrgaoMultiplo() {
        objLupaOrgaoUnidadeMultipla = new infraLupaText('txtOrgaoUnidadeMultipla', 'hdnIdOrgaoUnidadeMultipla', '<?= $strLinkOrgaoMultiplaSelecao ?>');

        objLupaOrgaoUnidadeMultipla.validarSelecionar = function () {
            objLupaOrgaoUnidadeMultipla.limpar();
            return true;
        }

        objLupaOrgaoUnidadeMultipla.processarRemocao = function (itens) {
            objLupaOrgaoUnidadeMultipla.limpar();
            objAutoCompletarUnidadeMultipla.limpar();
            for (var i = 0; i < itens.length; i++) {
                document.getElementById('hdnOrgao' + itens[i].value).value = '';
            }
            return true;
        }

        objLupaOrgaoUnidadeMultipla.finalizarSelecao = function () {
            document.getElementById('hdnIdUnidadeMultipla').value = '';
            document.getElementById('txtUnidadeMultipla').value = '';
            document.getElementById('hdnUfUnidadeMultipla').value = '';
            criarLupaUnidade();
        }

        objAutoCompletarUnidadeOrgaoMultipla = new infraAjaxAutoCompletar('hdnIdOrgaoUnidadeMultipla', 'txtOrgaoUnidadeMultipla', '<?= $strLinkAjaxOrgao ?>');
        objAutoCompletarUnidadeOrgaoMultipla.limparCampo = true;
        objAutoCompletarUnidadeOrgaoMultipla.prepararExecucao = function () {
            return 'palavras_pesquisa=' + document.getElementById('txtOrgaoUnidadeMultipla').value;
        };

        objAutoCompletarUnidadeOrgaoMultipla.processarResultado = function (id, descricao, complemento) {
            if (id != '') {
                //objLupaOrgaoUnidadeMultipla.adicionar(id, descricao, document.getElementById('txtOrgaoUnidadeMultipla'));
                //objLupaUnidadeMultipla.limpar();
            }
        }
    }

    function carregarComponenteUnidade() {
        objLupaUnidade = new infraLupaText('txtUnidade', 'hdnIdUnidade', '<?= $strLinkUnidadeSelecao ?>');

        objLupaUnidade.finalizarSelecao = function () {
            objAutoCompletarUnidade.selecionar(document.getElementById('hdnIdUnidade').value, document.getElementById('txtUnidade').value);
        }


        objAutoCompletarUnidade = new infraAjaxAutoCompletar('hdnIdUnidade', 'txtUnidade', '<?= $strLinkAjaxUnidade ?>');
        objAutoCompletarUnidade.limparCampo = true;
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
        objAutoCompletarUnidade.selecionar('<?= $strIdUnidade ?>', '<?= PaginaSEI::getInstance()->formatarParametrosJavascript(PaginaSEI::tratarHTML($strNomeRemetente)); ?>');
    }

    function carregarComponenteLupaTpDocPrinc(acaoComponente) {

        var gerado = document.getElementsByName('rdDocPrincipal[]')[0].checked;
        var externo = document.getElementsByName('rdDocPrincipal[]')[1].checked;
        var formulario = document.getElementsByName('rdDocPrincipal[]')[2].checked;
        var link = '';

        if (gerado) {
            link = '<?= $strLinkTipoDocPrincGeradoSelecao ?>';
        }

        if (externo) {
            link = '<?= $strLinkTipoDocPrincExternoSelecao ?>';
        }

        if (formulario) {
            link = '<?= $strLinkTipoDocPrincFormularioSelecao ?>';
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
        var strLinkAjaxTipoDocPrinc = '<?= $strLinkAjaxTipoDocPrinc ?>';
        var formulario = document.getElementsByName('rdDocPrincipal[]')[2].checked;

        objAutoCompletarTipoDocPrinc = new infraAjaxAutoCompletar('hdnIdTipoDocPrinc', 'txtTipoDocPrinc', strLinkAjaxTipoDocPrinc);
        objAutoCompletarTipoDocPrinc.limparCampo = true;
        objAutoCompletarTipoDocPrinc.tamanhoMinimo = 3;
        objAutoCompletarTipoDocPrinc.prepararExecucao = function () {
            return 'palavras_pesquisa=' + document.getElementById('txtTipoDocPrinc').value + '&tipoDoc=' + tipo;
        };

        objAutoCompletarTipoDocPrinc.processarResultado = function (id, descricao, complemento) {
            if (id != '') {
                document.getElementById('hdnIdTipoDocPrinc').value = id;
                document.getElementById('txtTipoDocPrinc').value = descricao;
            }
        }
        objAutoCompletarTipoDocPrinc.selecionar('<?= $strIdTipoDocPrinc ?>', '<?= PaginaSEI::getInstance()->formatarParametrosJavascript(PaginaSEI::tratarHTML($strNomeRemetente)); ?>');
    }


    function carregarComponenteTipoProcesso() {
        objLupaTipoProcesso = new infraLupaText('txtTipoProcesso', 'hdnIdTipoProcesso', '<?= $strLinkTipoProcessoSelecao ?>');

        objLupaTipoProcesso.finalizarSelecao = function () {
            objAutoCompletarTipoProcesso.selecionar(document.getElementById('hdnIdTipoProcesso').value, document.getElementById('txtTipoProcesso').value);
            objAjaxIdNivelAcesso.executar();
            changeUnidadeTipoProcesso();

        }

        objAutoCompletarTipoProcesso = new infraAjaxAutoCompletar('hdnIdTipoProcesso', 'txtTipoProcesso', '<?= $strLinkAjaxTipoProcesso ?>');
        objAutoCompletarTipoProcesso.limparCampo = true;
        objAutoCompletarTipoProcesso.tamanhoMinimo = 3;
        objAutoCompletarTipoProcesso.prepararExecucao = function () {
            return 'palavras_pesquisa=' + document.getElementById('txtTipoProcesso').value;
        };

        objAutoCompletarTipoProcesso.processarResultado = function (id, descricao, complemento) {
            if (id != '') {
                document.getElementById('hdnIdTipoProcesso').value = id;
                document.getElementById('txtTipoProcesso').value = descricao;
                changeUnidadeTipoProcesso();
                objAjaxIdNivelAcesso.executar();
            }
        }
        objAutoCompletarTipoProcesso.selecionar('<?= $strIdTipoProcesso ?>', '<?= PaginaSEI::getInstance()->formatarParametrosJavascript(PaginaSEI::tratarHTML($strNomeRemetente)); ?>');

    }

    //Carrega o documento para o documento complementar
    function carregarComponenteTipoDocumento() {

        objAutoCompletarTipoDocumento = new infraAjaxAutoCompletar('hdnIdSerie', 'txtSerie', '<?= $strLinkAjaxTipoDocumento ?>');
        objAutoCompletarTipoDocumento.limparCampo = true;
        objAutoCompletarTipoDocumento.tamanhoMinimo = 3;
        objAutoCompletarTipoDocumento.prepararExecucao = function () {
            var gerado = document.getElementsByName('rdDocPrincipal[]')[0].checked;
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

        objLupaTipoDocumento = new infraLupaSelect('selDescricao', 'hdnSerie', '<?= $strLinkTipoDocumentoSelecao ?>');
    }

    //Carrega o documento para o documento essencial
    function carregarComponenteTipoDocumentoEssencial() {

        objAutoCompletarTipoDocumentoEssencial = new infraAjaxAutoCompletar('hdnIdSerieEssencial', 'txtSerieEssencial', '<?= $strLinkAjaxTipoDocumento ?>');
        objAutoCompletarTipoDocumentoEssencial.limparCampo = true;
        objAutoCompletarTipoDocumentoEssencial.tamanhoMinimo = 3;
        objAutoCompletarTipoDocumentoEssencial.prepararExecucao = function () {
            var gerado = document.getElementsByName('rdDocPrincipal[]')[0].checked;
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

        objLupaTipoDocumentoEssencial = new infraLupaSelect('selDescricaoEssencial', 'hdnSerieEssencial', '<?= $strLinkTipoDocumentoEssencialSelecao ?>');
    }


    function validarCadastro() {

        var valorHipoteseLegal = document.getElementById('hdnParametroHipoteseLegal').value;

        if (infraTrim(document.getElementById('txtTipoProcesso').value) == '') {
            alert('Informe o Tipo de Processo.');
            document.getElementById('txtTipoProcesso').focus();
            return false;
        }

        if (infraTrim(document.getElementById('txtOrientacoes').value) == '') {
            alert('Informe as Orientações.');
            document.getElementById('txtOrientacoes').focus();
            return false;
        }

//Validar Unidade SM - EU6155
        var unidUnic = document.getElementsByName('rdUnidade[]')[0].checked;
        var multUnic = document.getElementsByName('rdUnidade[]')[1].checked;

        if (unidUnic) {
            if (infraTrim(document.getElementById('hdnIdUnidade').value) == '') {
                alert('Informe a Unidade para abertura do processo.');
                document.getElementById('txtUnidade').focus();
                return false;
            }
        }

        if (multUnic) {
            var objUndSelecionadas = document.getElementsByClassName('linhas');
            if (objUndSelecionadas.length == 0) {
                alert('É necessário informar ao menos uma Unidade para Abertura de Processo.');
                document.getElementById('txtUnidadeMultipla').focus();
                return false;
            }
        }

        if (!multUnic && !unidUnic) {
            alert('Informe a Unidade para abertura do processo.');
            document.getElementById('txtUnidade').focus();
            return false;
        }


        //Validar Rádio Indicação de Interessado
        var elemsIndInt = document.getElementsByName("indicacaoInteressado[]");

        validoIndInt = false;
        for (var i = 0; i < elemsIndInt.length; i++) {
            if (elemsIndInt[i].checked === true) {
                validoIndInt = true;
            }
        }

        if (!validoIndInt) {
            alert('Informe a Indicação de Interessado.');
            document.getElementById('rdUsuExterno').focus();
            return false;
        }

//Validar Rádio Indicação de Interessado
        var indicacaoIndireta = document.getElementById('rdIndicacaoIndireta').checked;

        if (indicacaoIndireta) {
            var elemsIndInd = document.getElementsByName("indicacaoIndireta[]");

            validoIndInd = false;
            for (var i = 0; i < elemsIndInd.length; i++) {
                if (elemsIndInd[i].checked === true) {
                    validoIndInd = true;
                }
            }

            if (!validoIndInd) {
                alert('Informe a Indicação de Interessado.');
                document.getElementsByName('indicacaoIndireta[]')[0].focus();
                return false;
            }
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
            alert('Informe o Nível de Acesso.');
            document.getElementById('rdUsuExterno').focus();
            return false;
        } else if (document.getElementById('selNivelAcesso').value == <?= ProtocoloRN::$NA_RESTRITO ?> && valorHipoteseLegal != '0') {

            //validar hipotese legal
            if (document.getElementById('selHipoteseLegal').value == '') {
                alert('Informe a Hipótese legal padrão.');
                document.getElementById('selHipoteseLegal').focus();
                return false;
            }

        }

//Documento Principal
        var elemsDP = document.getElementsByName("rdDocPrincipal[]");

        validoDP = false;

        for (var i = 0; i < elemsDP.length; i++) {

            if (elemsDP[i].checked == true) {
                validoDP = true;
            }

        }

        if (!validoDP) {
            alert('Informe o Documento Principal.');
            document.getElementById('rdDocGerado').focus();
            return false;
        }

        if (infraTrim(document.getElementById('txtTipoDocPrinc').value) == '') {
            alert('Informe o Tipo de Documento Principal.');
            document.getElementById('txtOrientacoes').focus();
            return false;
        }

        var multiplasUnidades = document.getElementById('rdUnidadeMultipla').checked;

        if (multiplasUnidades) {
            var paramsAjax = {
                idTipoProcesso: document.getElementById('hdnIdTipoProcesso').value,
                idUnidadeMultipla: document.getElementById('hdnUnidadesSelecionadas').value
            };
        } else {
            var paramsAjax = {
                idTipoProcesso: document.getElementById('hdnIdTipoProcesso').value,
                idUnidadeMultipla: "[" + document.getElementById('hdnIdUnidade').value + "]"

            };
        }

        var restricao = false;

        $.ajax({
            url: '<?=$strLinkAjaxConfirmaRestricaoSalvar?>',
            type: 'POST',
            dataType: 'XML',
            async: false,
            data: paramsAjax,
            success: function (result) {
                if ($(result).find('valor').text() == 'R') {
                    alert('Existem conflitos de parametrização na seção Unidade para Abertura do Processo. \n\n Resolva os conflitos antes de salvar.');
                    restricao = true;
                }
            },
            error: function (e) {
                console.error('Erro ao processar o XML do SEI: ' + e.responseText);
            }
        });
        if (restricao) {
            return false;
        }

        //Verifica a Qtd de Unidades
        var tbUnidades = document.getElementById('tableTipoUnidade');
        if (tbUnidades.rows.length < 3 && multiplasUnidades) {
            alert(" Como foi selecionada a opção Múltiplas Unidades para Abertura do Processo, é necessário adicionar mais de uma Unidade na lista");
            restricao = true;
        }
        if (restricao) {
            return false;
        }

        return true;
    }

    function OnSubmitForm() {
        preencherUnidadesMultiplas();
        return validarCadastro();
    }

    function preencherUnidadesMultiplas() {
        var arrayIdsBd = new Array();
        var objUndSelecionadas = document.getElementsByClassName('linhas');

        for (var i = 0; i < objUndSelecionadas.length; i++) {
            idTabela = (objUndSelecionadas[i].id).split('_')[1];
            arrayIdsBd.push(idTabela);
        }

        document.getElementById("hdnUnidadesSelecionadas").value = JSON.stringify(arrayIdsBd);
        document.getElementById("hdnCorpoTabela").value = document.getElementById('corpoTabela').innerHTML;
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

    function removerIconeRestricao() {
        document.getElementById('divRestricaoUU').innerHTML = "";
    }

</script>
