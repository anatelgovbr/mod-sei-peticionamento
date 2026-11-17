<?php ?>
<script type="text/javascript">
    function mdPetValidarXssAntesDaAcao(objCampo, strMensagem) {
        return mdPetEnviarValidacaoXss({mdPetXssTexto: objCampo.value}, objCampo, strMensagem);
    }

    function mdPetValidarXssCamposAntesDaAcao(arrCampos, strMensagem) {
        var arrTextos = arrCampos.map(function (objCampo) { return objCampo.value; });
        return mdPetEnviarValidacaoXss({mdPetXssTextos: arrTextos}, arrCampos[0], strMensagem);
    }

    function mdPetEnviarValidacaoXss(objDados, objCampo, strMensagem) {
        var strResultado = null;
        $.ajax({
            url: window.location.href.split('#')[0],
            type: 'POST',
            async: false,
            dataType: 'text',
            data: objDados,
            success: function (strResposta) {
                strResultado = strResposta.trim();
            }
        });

        if (strResultado === 'OK') {
            return true;
        }

        alert(strResultado === 'RECUSADO' ? strMensagem : 'Não foi possível verificar o conteúdo. Tente novamente.');
        objCampo.focus();
        return false;
    }

    // RT-PET-024: o navegador envia como &#NNN; o caractere que não existe na codificação da página, e o SEI recusa o
    // Complemento com esse caractere. Confere ao clicar em Adicionar, antes do upload e sem requisição ao servidor; o upload
    // e a conclusão repetem a verificação. A página é ISO-8859-1, que o navegador envia como Windows-1252: os 27 caracteres
    // do Windows-1252 acima de 255 chegam ao servidor como byte e são aceitos. A referência &#NNN; digitada como texto chega
    // ao servidor igual à que o navegador gera e também é recusada quando passa de 255.
    function mdPetValidarCaracteresComplemento(objCampo) {
        var strCaracteresExtrasWindows1252 = '\u20AC\u201A\u0192\u201E\u2026\u2020\u2021\u02C6\u2030\u0160\u2039\u0152\u017D\u2018\u2019' +
            '\u201C\u201D\u2022\u2013\u2014\u02DC\u2122\u0161\u203A\u0153\u017E\u0178';
        var bolTemReferenciaNaoAceita = (objCampo.value.match(/&#\d+;/g) || []).some(function (strReferencia) {
            return parseInt(strReferencia.slice(2, -1), 10) > 255;
        });
        var bolTemCaractereNaoAceito = Array.prototype.some.call(objCampo.value, function (strCaractere) {
            return strCaractere.charCodeAt(0) > 255 && strCaracteresExtrasWindows1252.indexOf(strCaractere) === -1;
        });

        if (!bolTemReferenciaNaoAceita && !bolTemCaractereNaoAceito) {
            return true;
        }

        alert('<?= PaginaSEI::formatarParametrosJavaScript(PeticionamentoIntegracao::MSG_COMPLEMENTO_CARACTERE_NAO_ACEITO, false) ?>');
        objCampo.focus();
        return false;
    }

    function mdPetValidarRetornoUpload(arrUpload) {
        var strNomeTemporario = arrUpload && arrUpload['nome_upload'];
        var bolValido = typeof strNomeTemporario === 'string' && strNomeTemporario !== '' &&
            !/[<>\/\\\r\n]/.test(strNomeTemporario) &&
            typeof arrUpload['nome'] === 'string' && arrUpload['nome'] !== '' &&
            /^\d+$/.test(String(arrUpload['tamanho'])) &&
            /^\d{2}\/\d{2}\/\d{4} \d{2}:\d{2}:\d{2}$/.test(arrUpload['data_hora']);

        if (!bolValido) {
            alert('Não foi possível adicionar o arquivo. Verifique a sessão e tente novamente.');
            return false;
        }

        return true;
    }

    // ADR-PET-010: antes de transferir o arquivo, confere no navegador o nome e o tamanho informados pelo navegador com os
    // limites do SEI que a página trouxe ao carregar, sem requisição ao servidor. O upload e a conclusão repetem a verificação
    // com o tamanho real.
    function mdPetValidarTamanhoAntesDoUpload(objCampoArquivo, objLimitesDoSei) {
        var objArquivo = objCampoArquivo && objCampoArquivo.files && objCampoArquivo.files.length > 0 ? objCampoArquivo.files[0] : null;
        if (objArquivo === null || !objLimitesDoSei) {
            return true;
        }

        var strRecusa = mdPetDescreverRecusaPorTamanho(objArquivo.name, objArquivo.size, objLimitesDoSei);
        if (strRecusa === null) {
            return true;
        }

        alert(strRecusa);
        objCampoArquivo.value = '';
        objCampoArquivo.focus();
        return false;
    }

    // Mesma regra de MdPetTamanhoArquivoRN::conferirTamanho, com os limites e os textos que o servidor gerou para a página.
    function mdPetDescreverRecusaPorTamanho(strNome, numTamanho, objLimitesDoSei) {
        // Mesmo tratamento do nome que o servidor aplica, sem caractere de controle, "#", "<", ">" e "&"
        var strNomeTratado = strNome.replace(/[\x00-\x1F\x7F]+/g, ' ').replace(/[#<>&]/g, '').trim();
        if (strNomeTratado.length > 100) {
            strNomeTratado = strNomeTratado.substring(0, 100) + '...';
        }

        // RT-PET-027: o SEI só recusa o arquivo de 0 byte quando o anexo entra no processo
        if (numTamanho === 0) {
            return objLimitesDoSei.vazio.replace('{nome}', function () {
                return strNomeTratado;
            });
        }

        var arrPartes = strNome.split('.');
        if (arrPartes.length < 2) {
            return null;
        }

        var strExtensao = arrPartes[arrPartes.length - 1].toLowerCase().replace(/ /g, '');
        if (objLimitesDoSei.configuracoesInvalidas[strExtensao]) {
            return objLimitesDoSei.configuracoesInvalidas[strExtensao];
        }

        var objLimite = objLimitesDoSei.extensoes[strExtensao] || objLimitesDoSei.geral;
        if (numTamanho <= objLimite.bytes) {
            return null;
        }

        // Arredondado para cima, com vírgula decimal e ponto de milhar, como o servidor escreve
        var arrTamanho = (Math.ceil(numTamanho * 100 / 1048576) / 100).toFixed(2).split('.');
        var strTamanho = arrTamanho[0].replace(/\B(?=(\d{3})+(?!\d))/g, '.') + ',' + arrTamanho[1];

        // Uma passada só: o nome com um dos marcadores não é trocado de novo
        return objLimitesDoSei.recusa.replace(/\{nome\}|\{tamanho\}/g, function (strMarcador) {
            return strMarcador === '{nome}' ? strNomeTratado : strTamanho;
        }) + ' ' + objLimite.frase;
    }

    // ADR-PET-010: faz o upload passar pelas verificações de tamanho antes da transferência, inclusive quando o arquivo é
    // arrastado. O limite do módulo vem antes dos limites do SEI: fnValidarLimiteDoModulo recebe o tamanho em bytes, mostra a
    // mensagem da tela e devolve false na recusa.
    function mdPetVerificarTamanhoNoUpload(objUpload, objLimitesDoSei, fnValidarLimiteDoModulo) {
        var fnExecutar = objUpload.executar;
        objUpload.executar = function () {
            var objCampoArquivo = objUpload.inputFile;
            var bolTemArquivo = objCampoArquivo && objCampoArquivo.files && objCampoArquivo.files.length > 0;
            if (bolTemArquivo && typeof fnValidarLimiteDoModulo === 'function' && !fnValidarLimiteDoModulo(objCampoArquivo.files[0].size)) {
                return false;
            }

            if (!mdPetValidarTamanhoAntesDoUpload(objCampoArquivo, objLimitesDoSei)) {
                return false;
            }

            return fnExecutar.apply(objUpload, arguments);
        };
    }

    function carregarComponenteLupaTpDocComplementar(acaoComponente) {
        acaoComponente == 'S' ? objLupaTipoDocumento.selecionar(700, 500) : objLupaTipoDocumento.remover();
    }

    function carregarDependenciaNivelAcesso() {
        //Ajax para carregar os niveis de acesso após a escolha do tipo de processo
        objAjaxIdNivelAcesso = new infraAjaxMontarSelectDependente('txtTipoProcesso', 'selNivelAcesso', '<?=$strLinkAjaxNivelAcesso?>');
        objAjaxIdNivelAcesso.prepararExecucao = function () {
            document.getElementById('selNivelAcesso').innerHTML = '';
            return infraAjaxMontarPostPadraoSelect('null', '', 'null') + '&idTipoProcesso=' + document.getElementById('hdnIdTipoProcesso').value;
        }
    }

    function changeStaNivelAcesso() {
        document.getElementById('forcarHipoteseLegal').style.display = document.getElementById('staNivelAcesso').value == 'R' ? 'block' : 'none';
    }

    function corrigirPosicaoAcaoExcluir() {
        var trs = document.getElementById('tbDocumento').getElementsByTagName('tr');
        for (var i = 1; i < trs.length; i++) {
            var tds = trs[i].getElementsByTagName('td');
            var td = tds[tds.length - 1];
            td.setAttribute('valign', 'center');
        }
    }

    function gerarIdDocumento() {
        var hdnIdDocumento = document.getElementById('hdnIdDocumento');
        hdnIdDocumento.value = parseInt(hdnIdDocumento.value) + 1;
        return hdnIdDocumento.value;
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

    function iniciarObjAjaxSelectHipoteseLegal() {
        objAjaxSelectHipoteseLegal = new infraAjaxMontarSelect('selHipoteseLegal', '<?= $strUrlAjaxMontarHipoteseLegal ?>');
        objAjaxSelectHipoteseLegal.processarResultado = function () {
            return 'nivelAcesso=' + RESTRITO;
        }
    }

    function iniciarTabelaDinamicaUsuarioProcuracao() {
        objTabelaDinamicaUsuarioProcuracao = new infraTabelaDinamica('tbUsuarioProcuracao', 'hdnTbUsuarioProcuracao', false, true);
        objTabelaDinamicaUsuarioProcuracao.gerarEfeitoTabela = true;
        objTabelaDinamicaUsuarioProcuracao.remover = function () {
            verificaTabelaProcuracao(1);
            return true;
        };
    }

    function limparTabelaDocumento() {
        objTabelaDinamicaDocumento.limpar();
        verificarTabelaVazia(1);
    }

    function maskCPF(cpf) {
        cpf = cpf.replace(/\D/g, "");
        cpf = cpf.replace(/(\d{3})(\d)/, "$1.$2");
        cpf = cpf.replace(/(\d{3})(\d)/, "$1.$2");
        cpf = cpf.replace(/(\d{3})(\d{1,2})$/, "$1-$2");

        return cpf;
    }

    function preencherHdnProrrogacao() {
        var rdProrrogacao = document.getElementsByName('rdProrrogacao[]')[0].checked ? 'S' : '';

        if (rdProrrogacao == '') {
            rdProrrogacao = document.getElementsByName('rdProrrogacao[]') [1].checked ? 'N' : '';
        }

        document.getElementById('hdnSinProrrogacao').value = rdProrrogacao;
    }

    function removerMarcacoesLinha(nomeClass){
        var objs = document.getElementsByClassName(nomeClass);

        for (var i = 0; i < objs.length; i++) {
            objs[i].className = nomeClass;
        }
    }

    function removerProcessoAssociado(remover) {
        if (remover === '1') {
            objLupaTipoProcesso.remover();
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

    function resizeIFramePorConteudo(){
        var id = 'ifrConteudoHTML';
        var ifrm = document.getElementById(id);
        ifrm.style.visibility = 'hidden';
        ifrm.style.height = "10px"; 

        var doc = ifrm.contentDocument? ifrm.contentDocument : ifrm.contentWindow.document;
        doc = doc || document;
        var body = doc.body, html = doc.documentElement;

        var width = Math.max( body.scrollWidth, body.offsetWidth, 
                            html.clientWidth, html.scrollWidth, html.offsetWidth );
        ifrm.style.width='100%';

        var height = Math.max( body.scrollHeight, body.offsetHeight, 
                            html.clientHeight, html.scrollHeight, html.offsetHeight );
        ifrm.style.height=height+'px';

        ifrm.style.visibility = 'visible';
    }

    function returnDateTime(valor) {

        valorArray = valor != '' ? valor.split(" ") : '';

        if (Array.isArray(valorArray)) {
            var data = valorArray[0]
            data = data.split('/');
            var mes = parseInt(data[1]) - 1;
            var horas = valorArray[1].split(':');

            var segundos = typeof horas[2] != 'undefined' ? horas[2] : '00';
            var dataCompleta = new Date(data[2], mes, data[0], horas[0], horas[1], segundos);
            return dataCompleta;
        }

        return false;
    }


    function returnElementFocus() {
        var focused = document.activeElement;
        if (!focused || focused == document.body) {
            focused = null;
        }
        else if (document.querySelector){
            focused = document.querySelector(":focus")
        }

        return focused;
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

    function salvarValorHipoteseLegal(el) {
        if (EXIBIR_HIPOTESE_LEGAL) {
            var hdnHipoteseLegal = document.getElementById('hdnHipoteseLegal');
            hdnHipoteseLegal.value = el.value;
        }
    }

    function somenteNumeros(e){
        var tecla=(window.event)?event.keyCode:e.which;
        if((tecla>47 && tecla<58))
            return true;
        else{
            if (tecla==8 || tecla==0)
                return true;
            else  return false;
        }
    }

    function tratarEnterUsuarioLote(ev){
        var key = infraGetCodigoTecla(ev);
    }
    
    function validDate(valor) {

        var campo = (valor === 'I') ? document.getElementById('txtDtInicio') : document.getElementById('txtDtFim');
        var tamanhoCampo = parseInt((campo.value).length);

        if (tamanhoCampo < 16 || tamanhoCampo === 18) {
            campo.focus();
            campo.value = "";
            alert('Data/Hora Inválida');
            return false;
        }

        var datetime = (campo.value).split(" ");
        var date = datetime[0];

        var ardt = new Array;
        var ExpReg = new RegExp("(0[1-9]|[12][0-9]|3[01])/(0[1-9]|1[012])/[12][0-9]{3}");
        ardt = date.split("/");
        erro = false;
        if (date.search(ExpReg) == -1) {
            erro = true;
        } else if (((ardt[1] == 4) || (ardt[1] == 6) || (ardt[1] == 9) || (ardt[1] == 11)) && (ardt[0] > 30)) {
            erro = true;
        } else if (ardt[1] == 2) {
            if ((ardt[0] > 28) && ((ardt[2] % 4) != 0))
                erro = true;
            if ((ardt[0] > 29) && ((ardt[2] % 4) == 0))
                erro = true;
        }

        if (erro) {
            alert("Data/Hora Inválida");
            campo.focus();
            campo.value = "";
            return false;
        } else {

            var arrayHoras = datetime[1].split(':')
            var horas = arrayHoras[0];
            var minutos = arrayHoras[1];
            var segundos = arrayHoras[2];

            if (horas > 23 || minutos > 59 || segundos > 59) {
                alert('Data/Hora Inválida');
                campo.focus();
                campo.value = "";
                return false
            }

        }

        if (document.getElementById('txtDtInicio').value != '' && document.getElementById('txtDtFim').value != '') {
            var dataInicial = returnDateTime(document.getElementById('txtDtInicio').value);
            var dataFinal = returnDateTime(document.getElementById('txtDtFim').value);
            var valido = (dataInicial.getTime() <= dataFinal.getTime());

            if (!valido) {
                document.getElementById('txtDtInicio').value = '';
                document.getElementById('txtDtFim').value = '';
                alert('A Data/Hora Início deve ser menor que a Data/Hora Fim');
                return false;
            }
        }

        return true;
    }

    function verificarTabelaVazia(qtdLinha) {
        var tbDocumento = document.getElementById('tbDocumento');
        var ultimoRegistro = tbDocumento.rows.length == qtdLinha;
        if (ultimoRegistro) {
            tbDocumento.style.display = 'none';
        }
    }

    function verificaTabelaProcuracao(qtdLinha) {
        var tbUsuarioProcuracao = document.getElementById('tbUsuarioProcuracao');
        var ultimoRegistro = tbUsuarioProcuracao.rows.length == qtdLinha;
        if (ultimoRegistro) {
            tbUsuarioProcuracao.style.display = 'none';
        }
    }

    function dataHoraLocal() {
        return new Date().toLocaleDateString('pt-BR', {
            hour12: false,
            hour: 'numeric',
            minute: 'numeric',
            second: 'numeric'
        });
    }

    function isInternetExplorer() {
        var ua = window.navigator.userAgent;
        var msie = ua.indexOf('MSIE ') > 0; // IE 10-
        var trident = ua.indexOf('Trident/') > 0; //IE 11;

        return msie || trident;
    }

</script>