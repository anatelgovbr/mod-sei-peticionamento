<?php
/**
 * ANATEL
 *
 * 17/09/2026 - criado por emerson_b.goncalves - ANATEL
 */
?>
var objTabelaDocPrincipal = null;
var objMdPetLimitesDoSei = <?= PeticionamentoIntegracao::montarLimitesDeTamanhoParaTela() ?>;
var objTabelaAnexoObrig = null;
var objTabelaMaisAnexos = null;

var objUploadDocPrincipal = null;
var objUploadAnexoObrig = null;
var objUploadMaisAnexos = null;

var janelaDocumentoPrincipal = null;
var bolPeticionamentoEnviado = false;

var strNomeDocPrincipal = '<?= PaginaSEI::getInstance()->formatarParametrosJavaScript($strNomeDocPrinc, false) ?>';
var strNomeNivelAcessoDocPrincipal = '<?= PaginaSEI::getInstance()->formatarParametrosJavaScript($strNomeNivelAcessoDocPrinc, false) ?>';
var strStaNivelAcessoDocPrincipal = '<?= PaginaSEI::getInstance()->formatarParametrosJavaScript($strStaNivelAcessoDocPrinc, false) ?>';
var numIdSerieDocPrincipal = '<?= (int) $numIdSerieDocPrinc ?>';
var numIdHipoteseLegalDocPrincipal = '<?= (int) $numIdHipoteseLegalDocPrinc ?>';
var numTamanhoMaximoDocPrincipal = <?= (int) $numTamanhoMaximoDocPrinc ?>;

var NA_RESTRITO = '<?= ProtocoloRN::$NA_RESTRITO ?>';
var TNA_USUARIO = '<?= MdPetPiTpProcessoRN::$TNA_USUARIO ?>';
var bolDocPrincUsuarioIndica = <?= $bolDocPrincUsuarioIndica ? 'true' : 'false' ?>;
var bolHipoteseLegalHabilitada = <?= $bolHipoteseLegalHabilitada ? 'true' : 'false' ?>;
var arrExtensoesDocPrincipal = [<?= $strJsExtensoesDocPrinc ?>];
var strIdDocPrincipalGerado = '<?= MdPetPiPeticaoINT::$ID_DOC_PRINCIPAL_SEM_ARQUIVO ?>';

var objConfigAnexoObrig = <?= $strJsConfigAnexoObrig ?>;
var bolTemAnexoObrig = <?= $bolTemAnexoObrig ? 'true' : 'false' ?>;

var numTamanhoMaximoAnexo = <?= (int) $numTamanhoMaximoAnexo ?>;
var arrExtensoesAnexo = [<?= $strJsExtensoesAnexo ?>];

var strLinkRemoverArquivo = '<?= PaginaSEI::getInstance()->formatarParametrosJavaScript($strLinkRemoverArquivo, false) ?>';
var strLinkConfirmacao = '<?= PaginaSEI::getInstance()->formatarParametrosJavaScript($strLinkConfirmacao, false) ?>';

function inicializar() {
    document.getElementById('txtEspecificacao').focus();

    window.addEventListener('resize', function () {
        redimensionarOrientacoes(document.getElementById('ifrOrientacoesTipoProcesso'));
    });

    objTabelaDocPrincipal = criarTabelaDocumentos('tblDocPrincipal', 'hdnDocPrincipal');

    var fnRemoverDocPrincipal = objTabelaDocPrincipal.removerLinha;
    objTabelaDocPrincipal.removerLinha = function (numLinha) {
        fnRemoverDocPrincipal.call(objTabelaDocPrincipal, numLinha);
        atualizarCamposDocumentoPrincipal();
    };
    atualizarCamposDocumentoPrincipal();

<?php if ($bolDocPrincExterno) { ?>
    objUploadDocPrincipal = new infraUpload('frmMdPetPiDocPrincipal', '<?= PaginaSEI::getInstance()->formatarParametrosJavaScript($strLinkUploadDocPrinc, false) ?>');
    mdPetVerificarTamanhoNoUpload(objUploadDocPrincipal, objMdPetLimitesDoSei);
    objUploadDocPrincipal.validar = validarDocumentoPrincipal;
    objUploadDocPrincipal.finalizou = concluirUploadDocumentoPrincipal;
<?php } ?>

<?php if ($bolTemAnexoObrig) { ?>
    objTabelaAnexoObrig = criarTabelaDocumentos('tblAnexoObrig', 'hdnAnexoObrig');

    objUploadAnexoObrig = new infraUpload('frmMdPetPiAnexoObrig', '<?= PaginaSEI::getInstance()->formatarParametrosJavaScript($strLinkUploadAnexo, false) ?>');
    mdPetVerificarTamanhoNoUpload(objUploadAnexoObrig, objMdPetLimitesDoSei);
    objUploadAnexoObrig.validar = validarAnexoObrigatorio;
    objUploadAnexoObrig.finalizou = concluirUploadAnexoObrigatorio;
<?php } ?>

    objTabelaMaisAnexos = criarTabelaDocumentos('tblMaisAnexos', 'hdnMaisAnexos');

    objUploadMaisAnexos = new infraUpload('frmMdPetPiMaisAnexos', '<?= PaginaSEI::getInstance()->formatarParametrosJavaScript($strLinkUploadAnexo, false) ?>');
    mdPetVerificarTamanhoNoUpload(objUploadMaisAnexos, objMdPetLimitesDoSei);
    objUploadMaisAnexos.validar = validarMaisAnexos;
    objUploadMaisAnexos.finalizou = concluirUploadMaisAnexos;
}

function criarTabelaDocumentos(strIdTabela, strIdHidden) {
    var objTabela = new infraTabelaDinamica(strIdTabela, strIdHidden, false, true);
    objTabela.gerarEfeitoTabela = true;

    objTabela.remover = function (arrLinha) {
        removerArquivoTemporario(arrLinha[0]);
        return true;
    };

    return objTabela;
}

function removerArquivoTemporario(strNomeUpload) {

    if (strNomeUpload === '' || strNomeUpload === strIdDocPrincipalGerado) {
        return;
    }

    $.ajax({
        url: strLinkRemoverArquivo,
        type: 'POST',
        async: false,
        dataType: 'XML',
        data: {nomeArquivo: strNomeUpload},
        error: function (e) {
            console.error('Erro removendo o arquivo da area temporaria: ' + e.responseText);
        }
    });
}

function redimensionarOrientacoes(ifr) {
    var doc = ifr.contentDocument ? ifr.contentDocument : ifr.contentWindow.document;

    if (!doc || !doc.body) {
        return;
    }

    ifr.style.height = '10px';
    ifr.style.height = Math.max(doc.body.scrollHeight, doc.documentElement.scrollHeight) + 'px';
}

function selecaoInformada(sel) {
    return sel !== null && sel.value !== '' && sel.value !== 'null';
}

function obterFormatoSelecionado(strNomeRadio) {
    var rdSelecionado = document.querySelector('input[name="' + strNomeRadio + '"]:checked');

    return rdSelecionado !== null ? rdSelecionado.value : '';
}

function exibirConferencia(strFormato, strIdDiv, strIdSelect) {
    var divTipoConferencia = document.getElementById(strIdDiv);

    if (strFormato === 'D') {
        divTipoConferencia.style.display = 'block';
        return;
    }

    document.getElementById(strIdSelect).selectedIndex = 0;
    divTipoConferencia.style.display = 'none';
}

function obterTipoConferenciaSelecionado(strIdSelect) {
    var selTipoConferencia = document.getElementById(strIdSelect);

    return selecaoInformada(selTipoConferencia) ? selTipoConferencia.value : '';
}

// A hipotese legal so aparece no Restrito e quando o parametro SEI_HABILITAR_HIPOTESE_LEGAL nao a desliga
function exibirHipoteseEscolhida(strIdSelNivel, strIdDiv, strIdSelHipotese) {
    var divHipoteseLegal = document.getElementById(strIdDiv);
    var bolExibir = bolHipoteseLegalHabilitada && document.getElementById(strIdSelNivel).value === NA_RESTRITO;

    if (divHipoteseLegal === null) {
        return;
    }

    divHipoteseLegal.style.display = bolExibir ? 'block' : 'none';

    if (!bolExibir) {
        document.getElementById(strIdSelHipotese).selectedIndex = 0;
    }
}

// Nivel e hipotese escolhidos pelo usuario, conferidos; null quando falta algum
function obterNivelAcessoEscolhido(strIdSelNivel, strIdSelHipotese, strRotulo) {
    var selNivelAcesso = document.getElementById(strIdSelNivel);
    var selHipoteseLegal = document.getElementById(strIdSelHipotese);

    if (!selecaoInformada(selNivelAcesso)) {
        alert('Informe o Nível de Acesso ' + strRotulo + '.');
        selNivelAcesso.focus();
        return null;
    }

    var bolComHipotese = bolHipoteseLegalHabilitada && selNivelAcesso.value === NA_RESTRITO;

    if (bolComHipotese && !selecaoInformada(selHipoteseLegal)) {
        alert('Informe a Hipótese Legal ' + strRotulo + '.');
        selHipoteseLegal.focus();
        return null;
    }

    return {
        sta: selNivelAcesso.value,
        nome: selNivelAcesso.options[selNivelAcesso.selectedIndex].text,
        idHipoteseLegal: bolComHipotese ? selHipoteseLegal.value : ''
    };
}

function obterNivelAcessoDocumentoPrincipal() {
    if (bolDocPrincUsuarioIndica) {
        return obterNivelAcessoEscolhido('selNivelAcessoDocPrinc', 'selHipoteseLegalDocPrinc', 'do Documento Principal');
    }

    return {sta: strStaNivelAcessoDocPrincipal, nome: strNomeNivelAcessoDocPrincipal, idHipoteseLegal: numIdHipoteseLegalDocPrincipal};
}

function copiarOpcoes(strIdSelOrigem, selDestino) {
    selDestino.innerHTML = document.getElementById(strIdSelOrigem).innerHTML;
    selDestino.selectedIndex = 0;
}

function validarDocumentoExterno(strNomeRadio, strIdComplemento, strIdDivConferencia, strIdSelectConferencia) {

    if (document.getElementById(strIdComplemento).value === '') {
        alert('Informe o Complemento do Tipo de Documento.');
        document.getElementById(strIdComplemento).focus();
        return false;
    }

    if (!mdPetValidarCaracteresComplemento(document.getElementById(strIdComplemento))) {
        return false;
    }

    var strFormato = obterFormatoSelecionado(strNomeRadio);

    if (strFormato === '') {
        alert('Informe o Formato do Documento.');
        return false;
    }

    if (strFormato === 'D' && obterTipoConferenciaSelecionado(strIdSelectConferencia) === '') {
        alert('Informe a Conferência com o Documento Digitalizado.');
        document.getElementById(strIdSelectConferencia).focus();
        return false;
    }

    return true;
}

function validarArquivoSelecionado(strIdCampo, arrExtensoes, numTamanhoMaximo, strRotulo) {
    var oFile = document.getElementById(strIdCampo);

    if (oFile.files === undefined || oFile.files.length === 0) {
        alert('Informe o arquivo do ' + strRotulo + '.');
        oFile.focus();
        return false;
    }

    var strNomeArquivo = oFile.files[0].name;
    var numTamanhoArquivo = oFile.files[0].size;
    var strExtensao = strNomeArquivo.indexOf('.') === -1 ? '' : strNomeArquivo.replace(/^.*\./, '').toLowerCase();
    var i = 0;

    if (numTamanhoArquivo > (numTamanhoMaximo * 1024 * 1024)) {
        alert('Arquivo excede o tamanho máximo geral permitido para documentos externos de ' + numTamanhoMaximo + 'Mb.');
        return false;
    }

    if (arrExtensoes.length > 0) {

        for (i = 0; i < arrExtensoes.length; i++) {
            if (arrExtensoes[i].nome === strExtensao) {
                if (numTamanhoArquivo > (arrExtensoes[i].tamanho * 1024 * 1024)) {
                    alert('O tamanho máximo permitido para arquivos com extensão ' + arrExtensoes[i].nome.toUpperCase() + ' é ' + arrExtensoes[i].tamanho + 'Mb.');
                    return false;
                }
                break;
            }
        }

        if (i === arrExtensoes.length) {
            var strMensagem = 'O arquivo selecionado não é permitido.\n\nSomente são permitidos arquivos com as extensões: ';

            for (i = 0; i < arrExtensoes.length; i++) {
                strMensagem += (i > 0 ? ', ' : '') + arrExtensoes[i].nome;
            }

            alert(strMensagem + '.');
            return false;
        }
    }

    return true;
}

function adicionarLinhaDocumento(objTabela, arrUpload, strNomeDocumento, strComplemento, numIdSerie, strNomeNivelAcesso, strStaNivelAcesso, numIdHipoteseLegal, strTipoConferencia, strFormato) {

    objTabela.adicionar([
        arrUpload['nome_upload'],
        arrUpload['nome'],
        arrUpload['data_hora'],
        infraFormatarTamanhoBytes(arrUpload['tamanho']),
        strNomeDocumento + (strComplemento !== '' ? ' ' + strComplemento : ''),
        strNomeNivelAcesso,
        strFormato === 'D' ? 'Digitalizado' : 'Nato-digital',
        arrUpload['tamanho'],
        numIdSerie,
        strComplemento,
        numIdHipoteseLegal,
        strTipoConferencia,
        strFormato,
        strStaNivelAcesso
    ]);
}

function limparFormato(strIdRadioN, strIdRadioD, strIdDivConferencia, strIdSelectConferencia) {
    document.getElementById(strIdRadioN).checked = false;
    document.getElementById(strIdRadioD).checked = false;

    exibirConferencia('', strIdDivConferencia, strIdSelectConferencia);
}

function selecionarFormatoDocumentoPrincipal() {
    exibirConferencia(obterFormatoSelecionado('rdFormatoDocPrinc'), 'divTipoConferenciaDocPrinc', 'selTipoConferenciaDocPrinc');
}

function validarDocumentoPrincipal() {

    if (!validarDocumentoExterno('rdFormatoDocPrinc', 'txtComplementoDocPrinc', 'divTipoConferenciaDocPrinc', 'selTipoConferenciaDocPrinc')) {
        return false;
    }

    if (obterNivelAcessoDocumentoPrincipal() === null) {
        return false;
    }

    return validarArquivoSelecionado('filArquivoDocPrinc', arrExtensoesDocPrincipal, numTamanhoMaximoDocPrincipal, 'Documento Principal');
}

function concluirUploadDocumentoPrincipal(arrUpload) {
    if (!mdPetValidarRetornoUpload(arrUpload)) {
        return;
    }

    var objNivelAcesso = obterNivelAcessoDocumentoPrincipal();

    adicionarLinhaDocumento(
        objTabelaDocPrincipal,
        arrUpload,
        strNomeDocPrincipal,
        document.getElementById('txtComplementoDocPrinc').value,
        numIdSerieDocPrincipal,
        objNivelAcesso.nome,
        objNivelAcesso.sta,
        objNivelAcesso.idHipoteseLegal,
        obterTipoConferenciaSelecionado('selTipoConferenciaDocPrinc'),
        obterFormatoSelecionado('rdFormatoDocPrinc')
    );

    limparCamposDocumentoPrincipal();
}

function limparCamposDocumentoPrincipal() {
    if (document.getElementById('hdnDocPrincipal').value === '') {
        return;
    }

<?php if ($bolDocPrincExterno) { ?>
    document.getElementById('txtComplementoDocPrinc').value = '';
    document.getElementById('filArquivoDocPrinc').value = '';
    limparFormato('rdFormatoDocPrincN', 'rdFormatoDocPrincD', 'divTipoConferenciaDocPrinc', 'selTipoConferenciaDocPrinc');
<?php } ?>

    if (bolDocPrincUsuarioIndica) {
        document.getElementById('selNivelAcessoDocPrinc').selectedIndex = 0;
        exibirHipoteseEscolhida('selNivelAcessoDocPrinc', 'divHipoteseLegalDocPrinc', 'selHipoteseLegalDocPrinc');
    }

    atualizarCamposDocumentoPrincipal();
}

function atualizarCamposDocumentoPrincipal() {
    var bolAdicionado = document.getElementById('hdnDocPrincipal').value !== '';
    var arrCampos = document.getElementById('fldDocumentoPrincipal').querySelectorAll('input:not([type="hidden"]), select, button');

    for (var i = 0; i < arrCampos.length; i++) {
        var campo = arrCampos[i];
        var bolFixo = campo.id === 'selSerieDocPrinc'
            || (!bolDocPrincUsuarioIndica && (campo.id === 'selNivelAcessoDocPrinc' || campo.id === 'selHipoteseLegalDocPrinc'));
        campo.disabled = bolAdicionado || bolFixo;
    }
}

function adicionarDocumentoPrincipal() {

    if (document.getElementById('hdnDocPrincipal').value !== '') {
        alert('Somente pode adicionar um Documento Principal por Peticionamento Interno. Remova o documento da lista antes de adicionar outro.');
        return;
    }

<?php if ($bolDocPrincExterno) { ?>
    objUploadDocPrincipal.executar();
<?php } else { ?>
    if (document.getElementById('hdnConteudoDocPrincipal').value !== 'S') {
        alert('Preencha o conteúdo do documento antes de adicioná-lo.');
        return;
    }

    var objNivelAcesso = obterNivelAcessoDocumentoPrincipal();

    if (objNivelAcesso === null) {
        return;
    }

    objTabelaDocPrincipal.adicionar([
        strIdDocPrincipalGerado,
        strNomeDocPrincipal,
        montarDataHoraAtual(),
        '-',
        strNomeDocPrincipal,
        objNivelAcesso.nome,
        '-',
        '',
        numIdSerieDocPrincipal,
        '',
        objNivelAcesso.idHipoteseLegal,
        '',
        '',
        objNivelAcesso.sta
    ]);
    limparCamposDocumentoPrincipal();
<?php } ?>
}

function montarDataHoraAtual() {
    var dataHora = new Date();

    return ('0' + dataHora.getDate()).slice(-2) + '/' + ('0' + (dataHora.getMonth() + 1)).slice(-2) + '/' + dataHora.getFullYear()
        + ' ' + ('0' + dataHora.getHours()).slice(-2) + ':' + ('0' + dataHora.getMinutes()).slice(-2) + ':' + ('0' + dataHora.getSeconds()).slice(-2);
}

<?php if (!$bolDocPrincExterno) { ?>
// Como no Peticionamento Externo, a janela tem nome por usuário e, se já estiver aberta, só volta para a frente sem recarregar
function abrirJanelaDocumentoPrincipal() {
    janelaDocumentoPrincipal = infraAbrirJanela('', 'janelaEditorPeticionamentoInterno_<?= (int) SessaoSEI::getInstance()->getNumIdUsuario() ?>', infraClientWidth(), infraClientHeight(), 'location=0,status=0,resizable=1,scrollbars=1', false);

    if (janelaDocumentoPrincipal.location.href === 'about:blank') {
        janelaDocumentoPrincipal.location.href = '<?= PaginaSEI::getInstance()->formatarParametrosJavaScript($strLinkDocumentoJanela, false) ?>';
    }

    janelaDocumentoPrincipal.focus();
}

function receberConteudoDocumentoPrincipal() {
    document.getElementById('hdnConteudoDocPrincipal').value = 'S';
}
<?php } ?>

<?php if ($bolTemAnexoObrig) { ?>
function obterConfigAnexoObrigatorio() {
    var selSerie = document.getElementById('selSerieAnexoObrig');

    if (!selecaoInformada(selSerie)) {
        return null;
    }

    return objConfigAnexoObrig[selSerie.value] !== undefined ? objConfigAnexoObrig[selSerie.value] : null;
}

// Com "Usuario indica diretamente" o nivel fica liberado, com as opcoes dos Mais Anexos; senao vem da configuracao, bloqueado
function selecionarTipoAnexoObrigatorio() {
    var objConfig = obterConfigAnexoObrigatorio();
    var selNivelAcesso = document.getElementById('selNivelAcessoAnexoObrig');
    var selHipoteseLegal = document.getElementById('selHipoteseLegalAnexoObrig');
    var divHipoteseLegal = document.getElementById('divHipoteseLegalAnexoObrig');
    var bolEscolha = objConfig !== null && objConfig.tipoNivelAcesso === TNA_USUARIO;

    selNivelAcesso.disabled = !bolEscolha;
    selHipoteseLegal.disabled = !bolEscolha;

    if (bolEscolha) {
        copiarOpcoes('selNivelAcessoMaisAnexos', selNivelAcesso);
        copiarOpcoes('selHipoteseLegalMaisAnexos', selHipoteseLegal);
        divHipoteseLegal.style.display = 'none';
        return;
    }

    selNivelAcesso.innerHTML = '<option value=""></option>';
    selHipoteseLegal.innerHTML = '<option value=""></option>';

    selNivelAcesso.options[0].text = objConfig !== null ? objConfig.nomeNivelAcesso : ' ';

    if (objConfig !== null && objConfig.nomeHipoteseLegal !== '') {
        selHipoteseLegal.options[0].text = objConfig.nomeHipoteseLegal;
        divHipoteseLegal.style.display = 'block';
        return;
    }

    selHipoteseLegal.options[0].text = ' ';
    divHipoteseLegal.style.display = 'none';
}

function selecionarFormatoAnexoObrigatorio() {
    exibirConferencia(obterFormatoSelecionado('rdFormatoAnexoObrig'), 'divTipoConferenciaAnexoObrig', 'selTipoConferenciaAnexoObrig');
}

function validarAnexoObrigatorio() {

    if (obterConfigAnexoObrigatorio() === null) {
        alert('Informe o Tipo de Documento do Anexo Obrigatório.');
        document.getElementById('selSerieAnexoObrig').focus();
        return false;
    }

    if (!validarDocumentoExterno('rdFormatoAnexoObrig', 'txtComplementoAnexoObrig', 'divTipoConferenciaAnexoObrig', 'selTipoConferenciaAnexoObrig')) {
        return false;
    }

    if (obterNivelAcessoAnexoObrigatorio() === null) {
        return false;
    }

    return validarArquivoSelecionado('filArquivoAnexoObrig', arrExtensoesAnexo, numTamanhoMaximoAnexo, 'Anexo Obrigatório');
}

function obterNivelAcessoAnexoObrigatorio() {
    var objConfig = obterConfigAnexoObrigatorio();

    if (objConfig.tipoNivelAcesso === TNA_USUARIO) {
        return obterNivelAcessoEscolhido('selNivelAcessoAnexoObrig', 'selHipoteseLegalAnexoObrig', 'do Anexo Obrigatório');
    }

    return {sta: objConfig.nivelAcesso, nome: objConfig.nomeNivelAcesso, idHipoteseLegal: objConfig.idHipoteseLegal};
}

function concluirUploadAnexoObrigatorio(arrUpload) {
    if (!mdPetValidarRetornoUpload(arrUpload)) {
        return;
    }

    var objConfig = obterConfigAnexoObrigatorio();
    var objNivelAcesso = obterNivelAcessoAnexoObrigatorio();

    adicionarLinhaDocumento(
        objTabelaAnexoObrig,
        arrUpload,
        objConfig.nomeSerie,
        document.getElementById('txtComplementoAnexoObrig').value,
        document.getElementById('selSerieAnexoObrig').value,
        objNivelAcesso.nome,
        objNivelAcesso.sta,
        objNivelAcesso.idHipoteseLegal,
        obterTipoConferenciaSelecionado('selTipoConferenciaAnexoObrig'),
        obterFormatoSelecionado('rdFormatoAnexoObrig')
    );

    document.getElementById('txtComplementoAnexoObrig').value = '';
    document.getElementById('filArquivoAnexoObrig').value = '';
    document.getElementById('selSerieAnexoObrig').selectedIndex = 0;

    selecionarTipoAnexoObrigatorio();
    limparFormato('rdFormatoAnexoObrigN', 'rdFormatoAnexoObrigD', 'divTipoConferenciaAnexoObrig', 'selTipoConferenciaAnexoObrig');
}

function adicionarAnexoObrigatorio() {
    objUploadAnexoObrig.executar();
}
<?php } ?>

function selecionarNivelAcessoMaisAnexos() {
    var selNivelAcesso = document.getElementById('selNivelAcessoMaisAnexos');
    var divHipoteseLegal = document.getElementById('divHipoteseLegalMaisAnexos');

    document.getElementById('selHipoteseLegalMaisAnexos').selectedIndex = 0;

    divHipoteseLegal.style.display = (bolHipoteseLegalHabilitada && selecaoInformada(selNivelAcesso) && selNivelAcesso.value === NA_RESTRITO) ? 'block' : 'none';
}

function selecionarFormatoMaisAnexos() {
    exibirConferencia(obterFormatoSelecionado('rdFormatoMaisAnexos'), 'divTipoConferenciaMaisAnexos', 'selTipoConferenciaMaisAnexos');
}

function validarMaisAnexos() {
    var selSerie = document.getElementById('selSerieMaisAnexos');
    var selNivelAcesso = document.getElementById('selNivelAcessoMaisAnexos');
    var selHipoteseLegal = document.getElementById('selHipoteseLegalMaisAnexos');

    if (!selecaoInformada(selSerie)) {
        alert('Informe o Tipo de Documento do anexo.');
        selSerie.focus();
        return false;
    }

    if (!validarDocumentoExterno('rdFormatoMaisAnexos', 'txtComplementoMaisAnexos', 'divTipoConferenciaMaisAnexos', 'selTipoConferenciaMaisAnexos')) {
        return false;
    }

    if (!selecaoInformada(selNivelAcesso)) {
        alert('Informe o Nível de Acesso do anexo.');
        selNivelAcesso.focus();
        return false;
    }

    if (bolHipoteseLegalHabilitada && selNivelAcesso.value === NA_RESTRITO && !selecaoInformada(selHipoteseLegal)) {
        alert('Informe a Hipótese Legal do anexo.');
        selHipoteseLegal.focus();
        return false;
    }

    return validarArquivoSelecionado('filArquivoMaisAnexos', arrExtensoesAnexo, numTamanhoMaximoAnexo, 'anexo');
}

function concluirUploadMaisAnexos(arrUpload) {
    if (!mdPetValidarRetornoUpload(arrUpload)) {
        return;
    }

    var selSerie = document.getElementById('selSerieMaisAnexos');
    var selNivelAcesso = document.getElementById('selNivelAcessoMaisAnexos');
    var selHipoteseLegal = document.getElementById('selHipoteseLegalMaisAnexos');
    var bolRestrito = bolHipoteseLegalHabilitada && selNivelAcesso.value === NA_RESTRITO;

    adicionarLinhaDocumento(
        objTabelaMaisAnexos,
        arrUpload,
        selSerie.options[selSerie.selectedIndex].text,
        document.getElementById('txtComplementoMaisAnexos').value,
        selSerie.value,
        selNivelAcesso.options[selNivelAcesso.selectedIndex].text,
        selNivelAcesso.value,
        bolRestrito ? selHipoteseLegal.value : '',
        obterTipoConferenciaSelecionado('selTipoConferenciaMaisAnexos'),
        obterFormatoSelecionado('rdFormatoMaisAnexos')
    );

    document.getElementById('txtComplementoMaisAnexos').value = '';
    document.getElementById('filArquivoMaisAnexos').value = '';
    selSerie.selectedIndex = 0;
    selNivelAcesso.selectedIndex = 0;

    selecionarNivelAcessoMaisAnexos();
    limparFormato('rdFormatoMaisAnexosN', 'rdFormatoMaisAnexosD', 'divTipoConferenciaMaisAnexos', 'selTipoConferenciaMaisAnexos');
}

function adicionarMaisAnexos() {
    objUploadMaisAnexos.executar();
}

function peticionar() {
    if (bolPeticionamentoEnviado) {
        return;
    }

    if (infraTrim(document.getElementById('txtEspecificacao').value) === '') {
        alert('Informe a Especificação.');
        document.getElementById('txtEspecificacao').focus();
        return;
    }

    if (!mdPetValidarXssAntesDaAcao(
        document.getElementById('txtEspecificacao'),
        '<?= PaginaSEI::getInstance()->formatarParametrosJavaScript(PeticionamentoIntegracao::MSG_XSS_ESPECIFICACAO, false) ?>'
    )) {
        return;
    }

    if (document.getElementById('hdnDocPrincipal').value === '') {
        alert('Informe o Documento Principal antes de peticionar.');
        return;
    }

    var strFaltando = listarAnexosObrigatoriosFaltando();

    if (strFaltando !== '') {
        alert('Falta anexar documento dos seguintes Anexos Obrigatórios:\n\n' + strFaltando);
        return;
    }

    infraAbrirJanelaModal(strLinkConfirmacao, 800, 500, true, false);
}

function listarAnexosObrigatoriosFaltando() {
    var strFaltando = '';

    if (!bolTemAnexoObrig) {
        return strFaltando;
    }

    var arrItens = objTabelaAnexoObrig.obterItens();
    var arrIdSerie = [];
    var i;

    for (i = 0; i < arrItens.length; i++) {
        arrIdSerie.push(String(arrItens[i][8]));
    }

    for (var strIdSerie in objConfigAnexoObrig) {
        if (Object.prototype.hasOwnProperty.call(objConfigAnexoObrig, strIdSerie) && arrIdSerie.indexOf(strIdSerie) === -1) {
            strFaltando += '- ' + objConfigAnexoObrig[strIdSerie].nomeSerie + '\n';
        }
    }

    return strFaltando;
}

function concluirPeticionamentoInterno(strCargoFuncao, strSenha) {
    if (bolPeticionamentoEnviado) {
        return;
    }

    bolPeticionamentoEnviado = true;

    var arrComandos = document.querySelectorAll('button[name="btnPeticionar"], button[name="btnVoltar"]');
    for (var i = 0; i < arrComandos.length; i++) {
        arrComandos[i].disabled = true;
    }

    document.getElementById('hdnCargoFuncaoAssinatura').value = strCargoFuncao || '';
    document.getElementById('pwdSenha').value = strSenha || '';
    document.getElementById('hdnConfirmado').value = 'S';
    document.getElementById('frmMdPetPiPeticaoCadastro').submit();
}
