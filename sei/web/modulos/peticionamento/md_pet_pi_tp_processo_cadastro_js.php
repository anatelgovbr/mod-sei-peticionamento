<?php
/**
 * ANATEL
 *
 * 08/09/2026 - criado por emerson_b.goncalves - ANATEL
 */
?>
<script type="text/javascript">

    var DOC_GERADO = '<?= MdPetPiTpProcessoRN::$DOC_GERADO ?>';
    var DOC_EXTERNO = '<?= MdPetPiTpProcessoRN::$DOC_EXTERNO ?>';
    var DOC_FORMULARIO = '<?= MdPetPiTpProcessoRN::$DOC_FORMULARIO ?>';
    var NA_RESTRITO = '<?= ProtocoloRN::$NA_RESTRITO ?>';
    var TNA_USUARIO = '<?= MdPetPiTpProcessoRN::$TNA_USUARIO ?>';
    var TNA_PADRAO = '<?= MdPetPiTpProcessoRN::$TNA_PADRAO ?>';

    var bolConsultar = <?= $bolConsultar ? 'true' : 'false' ?>;
    var bolHipoteseLegalHabilitada = <?= $bolHipoteseLegalHabilitada ? 'true' : 'false' ?>;

    var arrNomesTipoNivelAcesso = <?= json_encode(array_map(function ($strValor) { return mb_convert_encoding($strValor, 'UTF-8', 'ISO-8859-1'); }, $arrTipoNivelAcesso)) ?>;
    var arrNomesEspecie = <?= json_encode(array_map(function ($strValor) { return mb_convert_encoding($strValor, 'UTF-8', 'ISO-8859-1'); }, $arrDocPrincipal)) ?>;

    var objLupaTipoProcesso = null;
    var objAutoCompletarTipoProcesso = null;
    var objAjaxAssunto = null;
    var objAjaxModelo = null;
    var arrAjaxNivelAcesso = [];

    var objLupaOrgaos = null;
    var objAutoCompletarOrgao = null;
    var objLupaUnidades = null;
    var objAutoCompletarUnidade = null;

    var strAplicabilidadeAtual = '';

    // Cada grid carrega sua propria colecao, espelhada no hidden de mesmo nome
    var arrGrids = {
        DocPrinc: [],
        AnexoObrig: [],
        DocAuto: []
    };

    var arrBlocosDoc = ['DocPrinc', 'AnexoObrig', 'DocAuto'];
    var arrBlocosComModelo = ['DocPrinc', 'DocAuto'];
    var arrBlocosComComportamento = ['DocPrinc', 'AnexoObrig'];

    function inicializar() {

        for (var strBloco in arrGrids) {
            if (arrGrids.hasOwnProperty(strBloco)) {
                arrGrids[strBloco] = lerHidden('hdnGrid' + strBloco);
            }
        }

        for (var i = 0; i < arrBlocosDoc.length; i++) {
            renderizarGridDocumentos(arrBlocosDoc[i]);
        }

        strAplicabilidadeAtual = obterAplicabilidadeSelecionada();

        carregarComponentesRestricao();

        if (!bolConsultar) {
            carregarComponenteTipoProcesso();
            carregarAjaxNivelAcesso();

            for (var j = 0; j < arrBlocosDoc.length; j++) {
                carregarOpcoesTipoDocumento(arrBlocosDoc[j]);
            }

            document.getElementById('txtTipoProcesso').focus();
        } else {
            document.getElementById('btnCancelar').focus();
        }

        aplicarVisibilidadeCamposDocPrinc();
        aplicarVisibilidadeModelo();
        infraEfeitoTabelas();
    }

    function lerHidden(strId) {
        try {
            return JSON.parse(document.getElementById(strId).value || '[]');
        } catch (e) {
            return [];
        }
    }

    // Escapa os caracteres fora do ASCII para o JSON chegar intacto ao servidor, que trabalha em ISO-8859-1
    function serializarGrid(arrGrid) {
        return JSON.stringify(arrGrid).replace(/[\u007f-\uffff]/g, function (strCaractere) {
            return '\\u' + ('0000' + strCaractere.charCodeAt(0).toString(16)).slice(-4);
        });
    }

    function carregarComponenteTipoProcesso() {
        objLupaTipoProcesso = new infraLupaText('txtTipoProcesso', 'hdnIdTipoProcesso', '<?= $strLinkTipoProcessoSelecao ?>');

        objLupaTipoProcesso.finalizarSelecao = function () {
            objAutoCompletarTipoProcesso.selecionar(document.getElementById('hdnIdTipoProcesso').value, document.getElementById('txtTipoProcesso').value);
        };

        objAutoCompletarTipoProcesso = new infraAjaxAutoCompletar('hdnIdTipoProcesso', 'txtTipoProcesso', '<?= $strLinkAjaxTipoProcesso ?>');
        objAutoCompletarTipoProcesso.limparCampo = true;
        objAutoCompletarTipoProcesso.tamanhoMinimo = 3;
        objAutoCompletarTipoProcesso.prepararExecucao = function () {
            return 'palavras_pesquisa=' + document.getElementById('txtTipoProcesso').value;
        };
        objAutoCompletarTipoProcesso.processarResultado = function (id, descricao) {
            if (id !== '') {
                document.getElementById('hdnIdTipoProcesso').value = id;
                document.getElementById('txtTipoProcesso').value = descricao;
                aoTrocarTipoProcesso();
            }
        };
    }

    function carregarAjaxNivelAcesso() {
        arrAjaxNivelAcesso = [];

        for (var i = 0; i < arrBlocosDoc.length; i++) {
            var objAjax = new infraAjaxMontarSelectDependente('txtTipoProcesso', 'selNivelAcesso' + arrBlocosDoc[i], '<?= $strLinkAjaxNivelAcesso ?>');
            objAjax.prepararExecucao = (function (strBloco) {
                return function () {
                    document.getElementById('selNivelAcesso' + strBloco).innerHTML = '';
                    return infraAjaxMontarPostPadraoSelect('null', '', 'null') + '&idTipoProcesso=' + document.getElementById('hdnIdTipoProcesso').value;
                };
            })(arrBlocosDoc[i]);

            arrAjaxNivelAcesso.push(objAjax);
        }
    }

    // Copia para o select do bloco a lista de Tipos de Documento da aplicabilidade correspondente
    function carregarOpcoesTipoDocumento(strBloco) {
        var objSelDestino = document.getElementById('selSerie' + strBloco);
        var objSelLista = document.getElementById('selModeloSerie' + obterTipoDocumentoDoBloco(strBloco));

        if (objSelDestino === null) {
            return;
        }

        while (objSelDestino.options.length > 0) {
            objSelDestino.remove(0);
        }

        if (objSelLista === null) {
            objSelDestino.add(new Option(' ', ''));
            return;
        }

        for (var i = 0; i < objSelLista.options.length; i++) {
            objSelDestino.add(new Option(objSelLista.options[i].text, objSelLista.options[i].value));
        }
    }

    // Mesmo comportamento de OrgaoINT::montarRestricaoOrgaoUnidade do core: as Unidades seguem o Orgao selecionado na lista
    function carregarComponentesRestricao() {
        objLupaUnidades = new infraLupaSelect('selUnidades', 'hdnUnidades', '');

        if (!bolConsultar) {
            objLupaOrgaos = new infraLupaSelect('selOrgaos', 'hdnOrgaos', '<?= $strLinkOrgaoSelecao ?>');
            objLupaOrgaos.processarRemocao = function (itens) {
                objLupaUnidades.limpar();
                for (var i = 0; i < itens.length; i++) {
                    document.getElementById('hdnOrgao' + itens[i].value).value = '';
                }
                return true;
            };
            objLupaOrgaos.finalizarSelecao = function () {
                objLupaUnidades.limpar();
            };

            objAutoCompletarOrgao = new infraAjaxAutoCompletar('hdnIdOrgao', 'txtOrgao', '<?= $strLinkAjaxOrgao ?>');
            objAutoCompletarOrgao.limparCampo = true;
            objAutoCompletarOrgao.marcarSelecao = false;
            objAutoCompletarOrgao.prepararExecucao = function () {
                return 'palavras_pesquisa=' + document.getElementById('txtOrgao').value;
            };
            objAutoCompletarOrgao.processarResultado = function (id, descricao) {
                if (id !== '') {
                    objLupaOrgaos.adicionar(id, descricao, document.getElementById('txtOrgao'));
                    objLupaUnidades.limpar();
                }
            };

            objLupaUnidades.url = '';
            objLupaUnidades.validarSelecionar = function () {
                if (document.getElementById('selOrgaos').selectedIndex === -1) {
                    alert('Selecione antes um Órgão na lista Restrito a Órgãos.');
                    return false;
                }
                objLupaUnidades.url = document.getElementById('lnkOrgao' + document.getElementById('selOrgaos').value).value;
                return true;
            };
            objLupaUnidades.finalizarRemocao = function () {
                guardarUnidadesOrgaoSelecionado();
                return true;
            };
            objLupaUnidades.finalizarSelecao = function () {
                guardarUnidadesOrgaoSelecionado();
            };

            objAutoCompletarUnidade = new infraAjaxAutoCompletar('hdnIdUnidade', 'txtUnidade', '<?= $strLinkAjaxUnidade ?>');
            objAutoCompletarUnidade.limparCampo = true;
            objAutoCompletarUnidade.marcarSelecao = false;
            objAutoCompletarUnidade.prepararExecucao = function () {
                if (document.getElementById('selOrgaos').selectedIndex === -1) {
                    alert('Selecione antes um Órgão na lista Restrito a Órgãos.');
                    return false;
                }
                return 'palavras_pesquisa=' + document.getElementById('txtUnidade').value + '&id_orgao=' + document.getElementById('selOrgaos').value;
            };
            objAutoCompletarUnidade.processarResultado = function (id, descricao) {
                if (id !== '') {
                    objLupaUnidades.adicionar(id, descricao, document.getElementById('txtUnidade'));
                    guardarUnidadesOrgaoSelecionado();
                }
            };
        }

        if (document.getElementById('selOrgaos').options.length > 0) {
            document.getElementById('selOrgaos').options[0].selected = true;
            trocarOrgaoRestricao();
        }
    }

    function trocarOrgaoRestricao() {
        var objSelOrgaos = document.getElementById('selOrgaos');

        if (objSelOrgaos.selectedIndex === -1) {
            objLupaUnidades.limpar();
            return;
        }

        document.getElementById('hdnUnidades').value = document.getElementById('hdnOrgao' + objSelOrgaos.value).value;
        objLupaUnidades.montar();
    }

    function guardarUnidadesOrgaoSelecionado() {
        document.getElementById('hdnOrgao' + document.getElementById('selOrgaos').value).value = document.getElementById('hdnUnidades').value;
    }

    // Ao trocar o Tipo de Processo, critica a ausencia de assunto e recarrega os niveis de acesso
    function aoTrocarTipoProcesso() {
        if (document.getElementById('hdnIdTipoProcesso').value === '') {
            return;
        }

        validarAssuntoTipoProcesso();

        if (document.getElementById('hdnIdTipoProcesso').value === '') {
            return;
        }

        for (var i = 0; i < arrAjaxNivelAcesso.length; i++) {
            arrAjaxNivelAcesso[i].executar();
        }
    }

    function validarAssuntoTipoProcesso() {
        objAjaxAssunto = new infraAjaxComplementar(null, '<?= $strLinkAjaxAssuntoValidar ?>');
        objAjaxAssunto.limparCampo = false;
        objAjaxAssunto.mostrarAviso = false;
        objAjaxAssunto.async = false;

        objAjaxAssunto.prepararExecucao = function () {
            return 'idTipoProcesso=' + document.getElementById('hdnIdTipoProcesso').value;
        };

        objAjaxAssunto.processarResultado = function (arr) {
            if ('MensagemValidacao' in arr) {
                alert(arr['MensagemValidacao']);
                document.getElementById('hdnIdTipoProcesso').value = '';
                document.getElementById('txtTipoProcesso').value = '';
                document.getElementById('txtTipoProcesso').focus();
            }
        };

        objAjaxAssunto.executar();
    }

    function validarModeloProtocolo(strBloco) {
        var strValor = document.getElementById('txtModelo' + strBloco).value;

        document.getElementById('hdnIdModelo' + strBloco).value = '';

        if (strValor === '') {
            return;
        }

        objAjaxModelo = new infraAjaxComplementar(null, '<?= $strLinkAjaxModeloProtocolo ?>');
        objAjaxModelo.limparCampo = false;
        objAjaxModelo.mostrarAviso = false;
        objAjaxModelo.async = false;

        objAjaxModelo.prepararExecucao = function () {
            return 'protocoloFormatado=' + strValor;
        };

        objAjaxModelo.processarResultado = function (arr) {
            if ('MensagemValidacao' in arr) {
                alert(arr['MensagemValidacao']);
                document.getElementById('txtModelo' + strBloco).value = '';
                document.getElementById('txtModelo' + strBloco).focus();
                return;
            }

            document.getElementById('hdnIdModelo' + strBloco).value = arr['IdProtocolo'];
            document.getElementById('txtModelo' + strBloco).value = arr['ProtocoloFormatado'];
        };

        objAjaxModelo.executar();
    }

    function obterAplicabilidadeSelecionada() {
        var arrRadios = document.getElementsByName('rdAplicabilidade');

        for (var i = 0; i < arrRadios.length; i++) {
            if (arrRadios[i].checked) {
                return arrRadios[i].value;
            }
        }

        return '';
    }

    // O bloco Documento Principal segue a aplicabilidade escolhida; os outros dois sao fixos
    function obterTipoDocumentoDoBloco(strBloco) {
        if (strBloco === 'DocPrinc') {
            return obterAplicabilidadeSelecionada();
        }

        return strBloco === 'DocAuto' ? DOC_GERADO : DOC_EXTERNO;
    }

    // Os campos e a grid do Documento Principal so aparecem depois de escolher a aplicabilidade
    function aplicarVisibilidadeCamposDocPrinc() {
        var objDiv = document.getElementById('divCamposDocPrinc');

        if (objDiv !== null) {
            objDiv.style.display = obterAplicabilidadeSelecionada() !== '' ? 'block' : 'none';
        }
    }

    // Modelo por Protocolo so se aplica a documento Gerado
    function aplicarVisibilidadeModelo() {
        var objDiv = document.getElementById('divModeloDocPrinc');

        if (objDiv !== null) {
            objDiv.style.display = obterAplicabilidadeSelecionada() === DOC_GERADO ? 'block' : 'none';
        }
    }

    function changeAplicabilidade() {
        var strNovaAplicabilidade = obterAplicabilidadeSelecionada();

        if (strNovaAplicabilidade === strAplicabilidadeAtual) {
            return;
        }

        if (arrGrids.DocPrinc.length > 0
            && !confirm('Alterar a aplicabilidade vai remover o Documento Principal já adicionado. Deseja continuar?')) {
            marcarAplicabilidade(strAplicabilidadeAtual);
            return;
        }

        strAplicabilidadeAtual = strNovaAplicabilidade;

        arrGrids.DocPrinc = [];
        renderizarGridDocumentos('DocPrinc');

        limparCamposBloco('DocPrinc');
        aplicarVisibilidadeCamposDocPrinc();
        aplicarVisibilidadeModelo();
        carregarOpcoesTipoDocumento('DocPrinc');
    }

    function marcarAplicabilidade(strValor) {
        var arrRadios = document.getElementsByName('rdAplicabilidade');

        for (var i = 0; i < arrRadios.length; i++) {
            arrRadios[i].checked = (arrRadios[i].value === strValor);
        }
    }

    // Sem comportamento no bloco (Documento Adicional Gerado Automatico), o documento e sempre pre-definido
    function obterTipoNivelAcessoSelecionado(strBloco) {
        if (arrBlocosComComportamento.indexOf(strBloco) === -1) {
            return TNA_PADRAO;
        }

        var objRadio = document.querySelector('input[name="rdTipoNivelAcesso' + strBloco + '"]:checked');

        return objRadio !== null ? objRadio.value : '';
    }

    function marcarTipoNivelAcesso(strBloco, strValor) {
        var arrRadios = document.getElementsByName('rdTipoNivelAcesso' + strBloco);

        for (var i = 0; i < arrRadios.length; i++) {
            arrRadios[i].checked = (arrRadios[i].value === strValor);
        }
    }

    // O nivel de acesso so e pedido no Padrao pre-definido
    function changeTipoNivelAcesso(strBloco) {
        var bolPadrao = obterTipoNivelAcessoSelecionado(strBloco) === TNA_PADRAO;

        document.getElementById('divNivelAcesso' + strBloco).style.display = bolPadrao ? 'block' : 'none';

        if (!bolPadrao) {
            document.getElementById('selNivelAcesso' + strBloco).value = '';
        }

        changeNivelAcesso(strBloco);
    }

    // A hipotese legal so aparece no Restrito e quando o parametro SEI_HABILITAR_HIPOTESE_LEGAL nao a desliga
    function changeNivelAcesso(strBloco) {
        var bolExibir = bolHipoteseLegalHabilitada && document.getElementById('selNivelAcesso' + strBloco).value === NA_RESTRITO;

        document.getElementById('divHipoteseLegal' + strBloco).style.display = bolExibir ? 'block' : 'none';

        if (!bolExibir) {
            document.getElementById('selHipoteseLegal' + strBloco).value = '';
        }
    }

    function limparCamposBloco(strBloco) {
        document.getElementById('selSerie' + strBloco).value = '';
        document.getElementById('selNivelAcesso' + strBloco).value = '';
        document.getElementById('selHipoteseLegal' + strBloco).value = '';
        document.getElementById('divHipoteseLegal' + strBloco).style.display = 'none';

        if (arrBlocosComComportamento.indexOf(strBloco) !== -1) {
            marcarTipoNivelAcesso(strBloco, TNA_USUARIO);
            document.getElementById('divNivelAcesso' + strBloco).style.display = 'none';
        }

        if (document.getElementById('txtModelo' + strBloco) !== null) {
            document.getElementById('txtModelo' + strBloco).value = '';
            document.getElementById('hdnIdModelo' + strBloco).value = '';
        }
    }

    function adicionarDocumento(strBloco) {
        var objSelSerie = document.getElementById('selSerie' + strBloco);
        var objSelNivel = document.getElementById('selNivelAcesso' + strBloco);
        var objSelHipotese = document.getElementById('selHipoteseLegal' + strBloco);
        var strTipoNivelAcesso = obterTipoNivelAcessoSelecionado(strBloco);
        var bolPadrao = strTipoNivelAcesso === TNA_PADRAO;
        var bolComHipotese = bolPadrao && bolHipoteseLegalHabilitada && objSelNivel.value === NA_RESTRITO;

        if (strBloco === 'DocPrinc' && obterAplicabilidadeSelecionada() === '') {
            alert('Escolha antes a aplicabilidade do Documento Principal.');
            return;
        }

        if (objSelSerie.value === '') {
            alert('Informe o Tipo de Documento.');
            objSelSerie.focus();
            return;
        }

        if (strTipoNivelAcesso === '') {
            alert('Informe o Comportamento do Nível de Acesso.');
            return;
        }

        if (bolPadrao && objSelNivel.value === '') {
            alert('Informe o Nível de Acesso.');
            objSelNivel.focus();
            return;
        }

        if (bolComHipotese && objSelHipotese.value === '') {
            alert('Informe a Hipótese Legal.');
            objSelHipotese.focus();
            return;
        }

        if (strBloco === 'DocPrinc' && arrGrids.DocPrinc.length > 0) {
            alert('O Documento Principal já foi adicionado. Remova o registro atual antes de adicionar outro.');
            return;
        }

        for (var i = 0; i < arrGrids[strBloco].length; i++) {
            if (String(arrGrids[strBloco][i].idSerie) === String(objSelSerie.value)) {
                alert('Este Tipo de Documento já foi adicionado neste bloco.');
                return;
            }
        }

        var objLinha = {
            idSerie: objSelSerie.value,
            nomeSerie: objSelSerie.options[objSelSerie.selectedIndex].text,
            tipoNivelAcesso: strTipoNivelAcesso,
            nivelAcesso: bolPadrao ? objSelNivel.value : '',
            idHipoteseLegal: bolComHipotese ? objSelHipotese.value : '',
            nomeHipoteseLegal: bolComHipotese ? objSelHipotese.options[objSelHipotese.selectedIndex].text : ''
        };

        if (document.getElementById('txtModelo' + strBloco) !== null) {
            objLinha.idProtocoloModelo = document.getElementById('hdnIdModelo' + strBloco).value;
            objLinha.protocoloModelo = document.getElementById('txtModelo' + strBloco).value;
        }

        if (strBloco === 'DocPrinc') {
            objLinha.aplicabilidade = obterAplicabilidadeSelecionada();
        }

        arrGrids[strBloco].push(objLinha);

        limparCamposBloco(strBloco);
        renderizarGridDocumentos(strBloco);
        objSelSerie.focus();
    }

    function removerLinha(strBloco, strIdSerie) {
        for (var i = 0; i < arrGrids[strBloco].length; i++) {
            if (String(arrGrids[strBloco][i].idSerie) === String(strIdSerie)) {
                arrGrids[strBloco].splice(i, 1);
                break;
            }
        }

        renderizarGridDocumentos(strBloco);
    }

    function alterarLinha(strBloco, strIdSerie) {
        for (var i = 0; i < arrGrids[strBloco].length; i++) {
            if (String(arrGrids[strBloco][i].idSerie) !== String(strIdSerie)) {
                continue;
            }

            var objLinha = arrGrids[strBloco][i];

            document.getElementById('selSerie' + strBloco).value = objLinha.idSerie;

            if (arrBlocosComComportamento.indexOf(strBloco) !== -1) {
                marcarTipoNivelAcesso(strBloco, objLinha.tipoNivelAcesso || '');
                changeTipoNivelAcesso(strBloco);
            }

            document.getElementById('selNivelAcesso' + strBloco).value = objLinha.nivelAcesso || '';

            changeNivelAcesso(strBloco);

            if (objLinha.nivelAcesso === NA_RESTRITO && bolHipoteseLegalHabilitada) {
                document.getElementById('selHipoteseLegal' + strBloco).value = objLinha.idHipoteseLegal;
            }

            if (document.getElementById('txtModelo' + strBloco) !== null) {
                document.getElementById('txtModelo' + strBloco).value = objLinha.protocoloModelo || '';
                document.getElementById('hdnIdModelo' + strBloco).value = objLinha.idProtocoloModelo || '';
            }

            arrGrids[strBloco].splice(i, 1);
            renderizarGridDocumentos(strBloco);
            document.getElementById('selSerie' + strBloco).focus();
            return;
        }
    }

    function renderizarGridDocumentos(strBloco) {
        var objCorpo = document.getElementById('corpoTbl' + strBloco);
        var bolComModelo = arrBlocosComModelo.indexOf(strBloco) !== -1;

        limparCorpo(objCorpo);

        for (var i = 0; i < arrGrids[strBloco].length; i++) {
            var objLinha = arrGrids[strBloco][i];
            var objTr = document.createElement('tr');
            objTr.className = (i % 2 === 0) ? 'infraTrClara' : 'infraTrEscura';

            var strTipoNivelAcesso = arrBlocosComComportamento.indexOf(strBloco) !== -1 ? objLinha.tipoNivelAcesso : TNA_PADRAO;

            objTr.appendChild(criarTd(arrNomesEspecie[obterTipoDocumentoDoBloco(strBloco)] || '', 'left'));
            objTr.appendChild(criarTd(objLinha.nomeSerie, 'left'));
            objTr.appendChild(criarTd(arrNomesTipoNivelAcesso[strTipoNivelAcesso] || '-', 'left'));
            objTr.appendChild(criarTd(formatarNivelAcesso(strTipoNivelAcesso, objLinha), 'left'));
            objTr.appendChild(criarTd(bolComModelo && objLinha.protocoloModelo ? objLinha.protocoloModelo : '-', 'left'));
            objTr.appendChild(criarTdAcoesDocumento(strBloco, objLinha.idSerie));

            objCorpo.appendChild(objTr);
        }

        document.getElementById('spnQtd' + strBloco).textContent = arrGrids[strBloco].length;
        document.getElementById('hdnGrid' + strBloco).value = serializarGrid(arrGrids[strBloco]);
    }

    // Traco quando o usuario indica; no Restrito a hipotese legal vai entre parenteses
    function formatarNivelAcesso(strTipoNivelAcesso, objLinha) {
        if (strTipoNivelAcesso !== TNA_PADRAO || !objLinha.nivelAcesso) {
            return '-';
        }

        if (objLinha.nivelAcesso !== NA_RESTRITO) {
            return 'Público';
        }

        return 'Restrito' + (objLinha.nomeHipoteseLegal ? ' (' + objLinha.nomeHipoteseLegal + ')' : '');
    }

    function limparCorpo(objCorpo) {
        while (objCorpo.firstChild) {
            objCorpo.removeChild(objCorpo.firstChild);
        }
    }

    function criarTd(strTexto, strAlinhamento) {
        var objTd = document.createElement('td');
        objTd.align = strAlinhamento;
        objTd.appendChild(document.createTextNode(strTexto));

        return objTd;
    }

    function criarTdAcoesDocumento(strBloco, strIdSerie) {
        var objTd = document.createElement('td');
        objTd.align = 'center';

        if (bolConsultar) {
            return objTd;
        }

        objTd.appendChild(criarIcone(INFRA_ICONE_ALTERAR, 'Alterar Item', function () {
            alterarLinha(strBloco, strIdSerie);
        }));

        objTd.appendChild(criarIcone(INFRA_ICONE_REMOVER, 'Remover Item', function () {
            removerLinha(strBloco, strIdSerie);
        }));

        return objTd;
    }

    // Mesmo icone, titulo e classe que a infraTabelaDinamica usa nas acoes das tabelas do SEI
    function criarIcone(strSrc, strTitulo, funcao) {
        var objImg = document.createElement('img');
        objImg.src = strSrc;
        objImg.title = strTitulo;
        objImg.alt = strTitulo;
        objImg.className = 'infraImg';
        objImg.style.cursor = 'pointer';
        objImg.onclick = funcao;

        return objImg;
    }

    function onSubmitForm() {

        if (document.getElementById('hdnIdTipoProcesso').value === '') {
            alert('Informe o Tipo de Processo.');
            document.getElementById('txtTipoProcesso').focus();
            return false;
        }

        if (obterAplicabilidadeSelecionada() === '') {
            alert('Informe a aplicabilidade do Documento Principal.');
            return false;
        }

        if (arrGrids.DocPrinc.length === 0) {
            alert('Adicione o Documento Principal.');
            document.getElementById('selSerieDocPrinc').focus();
            return false;
        }

        for (var strBloco in arrGrids) {
            if (arrGrids.hasOwnProperty(strBloco)) {
                document.getElementById('hdnGrid' + strBloco).value = serializarGrid(arrGrids[strBloco]);
            }
        }

        return true;
    }

</script>
