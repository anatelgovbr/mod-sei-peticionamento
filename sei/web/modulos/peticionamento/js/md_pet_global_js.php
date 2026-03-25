<script type="text/javascript">
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
                alert('A Data/Hora Inicio deve ser menor que a Data/Hora Fim');
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