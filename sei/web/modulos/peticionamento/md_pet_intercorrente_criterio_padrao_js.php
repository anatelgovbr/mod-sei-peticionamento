<script type="text/javascript">
    //Processo
    var objLupaTipoProcesso = null;
    var objAutoCompletarTipoProcesso = null;
    var objLupaTipoDocumento = null;
    var objAutoCompletarTipoDocumento = null;

    function changeNivelAcesso() {
        document.getElementById('divNivelAcesso').style.display = "none";
        var padrao = document.getElementsByName('rdNivelAcesso[]')[1].checked;

        document.getElementById('selHipoteseLegal').value = '';
        document.getElementById('divHipoteseLegal').style.display = 'none';
        document.getElementById('selNivelAcesso').value = '';

        if (padrao) {
            document.getElementById('divNivelAcesso').style.display = "flex";
        }
    }

    function changeStaNivelAcesso() {
        document.getElementById('forcarHipoteseLegal').style.display = document.getElementById('staNivelAcesso').value == 'R' ? 'block' : 'none';
    }

    function changeSelectNivelAcesso() {
        document.getElementById('selHipoteseLegal').value = '';

        var valorSelectNivelAcesso = document.getElementById('selNivelAcesso').value;
        var valorHipoteseLegal = document.getElementById('hdnParametroHipoteseLegal').value;

        if (valorSelectNivelAcesso == 'I' && valorHipoteseLegal != '0') {
            document.getElementById('divHipoteseLegal').style.display = 'flex';
        } else {
            document.getElementById('divHipoteseLegal').style.display = 'none';
        }
    }

    function inicializar() {
        if ('<?=$_GET['acao']?>' == 'md_pet_intercorrente_criterio_padrao') {
            carregarComponenteTipoProcesso();
            carregarDependenciaNivelAcesso();
            carregarComponenteTipoDocumento();
            document.getElementById('txtTipoProcesso').focus();
        }
        infraEfeitoTabelas();
        changeStaNivelAcesso();
    }

    function carregarDependenciaNivelAcesso() {
        //Ajax para carregar os niveis de acesso após a escolha do tipo de processo
        objAjaxIdNivelAcesso = new infraAjaxMontarSelectDependente('txtTipoProcesso', 'selNivelAcesso', '<?=$strLinkAjaxNivelAcesso?>');
        objAjaxIdNivelAcesso.prepararExecucao = function () {
            document.getElementById('selNivelAcesso').innerHTML = '';
            return infraAjaxMontarPostPadraoSelect('null', '', 'null') + '&idTipoProcesso=' + document.getElementById('hdnIdTipoProcesso').value;
        }
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

    function carregarComponenteTipoProcesso() {
        objLupaTipoProcesso = new infraLupaText('txtTipoProcesso', 'hdnIdTipoProcesso', '<?=$strLinkTipoProcessoSelecao?>');

        objLupaTipoProcesso.finalizarSelecao = function () {
            objAutoCompletarTipoProcesso.selecionar(document.getElementById('hdnIdTipoProcesso').value, document.getElementById('txtTipoProcesso').value);
            objAjaxIdNivelAcesso.executar();
        }

        objAutoCompletarTipoProcesso = new infraAjaxAutoCompletar('hdnIdTipoProcesso', 'txtTipoProcesso', '<?=$strLinkAjaxTipoProcesso?>');
        objAutoCompletarTipoProcesso.limparCampo = false;

        objAutoCompletarTipoProcesso.prepararExecucao = function () {
            return 'palavras_pesquisa=' + document.getElementById('txtTipoProcesso').value;
        };

        objAutoCompletarTipoProcesso.processarResultado = function (id, descricao, complemento) {
            if (id != '') {
                document.getElementById('hdnIdTipoProcesso').value = id;
                document.getElementById('txtTipoProcesso').value = descricao;
                document.getElementById('selNivelAcesso').value = '';
                changeSelectNivelAcesso();
            }
        }
        objAutoCompletarTipoProcesso.selecionar('<?=$strIdTipoProcesso?>', '<?=PaginaSEI::getInstance()->formatarParametrosJavascript(PaginaSEI::tratarHTML($strNomeRemetente));?>');
    }

    function removerProcessoAssociado(remover) {
        document.getElementById('selNivelAcesso').innerHTML = '';
        document.getElementById('divHipoteseLegal').style.display = "none";
        if (remover === '1') {
            objLupaTipoProcesso.remover();
        }
    }

    // AUTOCOMPLETAR TipoDocumento
    function carregarComponenteTipoDocumento(){

        objAutoCompletarTipoDocumento = new infraAjaxAutoCompletar('hdnIdTipoDocumento', 'txtTipoDocumento', '<?= $strLinkAjaxTiposDocumentos ?>');
        objAutoCompletarTipoDocumento.limparCampo = true;
        objAutoCompletarTipoDocumento.tamanhoMinimo = 3;

        objAutoCompletarTipoDocumento.prepararExecucao = function(){
            return 'palavras_pesquisa='+document.getElementById('txtTipoDocumento').value;
        };

        objAutoCompletarTipoDocumento.processarResultado = function(id, descricao, complemento){

            if (id != ''){
                var options = document.getElementById('selTipoDocumento').options;

                for(var i=0;i < options.length;i++){
                    if (options[i].value == id){
                        var msg = setMensagemPersonalizada(msg10Padrao, ['TipoDocumento']);
                        alert(msg);
                        break;
                    }
                }

                if (i==options.length){

                    for(i=0;i < options.length;i++){
                        options[i].selected = false;
                    }

                    opt = infraSelectAdicionarOption(document.getElementById('selTipoDocumento'), descricao ,id);
                    objLupaTipoDocumento.atualizar();
                    opt.selected = true;
                }

                document.getElementById('txtTipoDocumento').value = '';
                document.getElementById('txtTipoDocumento').focus();

            }
        };

        objLupaTipoDocumento = new infraLupaSelect('selTipoDocumento', 'hdnTipoDocumento', '<?= $strLinkTipoDocumentoSelecao ?>');
    }

    function validarCadastro() {

        var valorHipoteseLegal = document.getElementById('hdnParametroHipoteseLegal').value;

        if (infraTrim(document.getElementById('txtTipoProcesso').value) == '') {
            alert('Informe o Tipo de Processo.');
            document.getElementById('txtTipoProcesso').focus();
            return false;
        }

        //Validar Nível Acesso
        var elemsNA = document.getElementsByName("rdNivelAcesso[]");

        var validoNA = false, valorNA = 0;

        for (var i = 0; i < elemsNA.length; i++) {
            if (elemsNA[i].checked === true) {
                validoNA = true;
                valorNA = parseInt(elemsNA[i].value);
            }
        }

        if (validoNA === false) {
            alert('Informe o Nível de Acesso.');
            return false;
        }

        if (infraTrim(document.getElementById('selNivelAcesso').value) == '' && valorNA != 1) {
            alert('Informe o Nível de Acesso.');
            document.getElementById('selNivelAcesso').focus();
            return false;
        } else if (document.getElementById('selNivelAcesso').value == 'I' && valorHipoteseLegal != '0') {

            //validar hipotese legal
            if (document.getElementById('selHipoteseLegal').value == '') {
                alert('Informe a Hipótese legal padrão.');
                document.getElementById('selHipoteseLegal').focus();
                return false;
            }
        }

        var elemsSA = document.getElementsByName("rdSinAtivo[]");
        var validoSA = false;

        for (var i = 0; i < elemsSA.length; i++) {
            if (elemsSA[i].checked === true) {
                validoSA = true;
            }
        }

        if (validoSA === false) {
            alert('Indique a opção para exibição ou não do menu Peticionamento Intercorrente.');
            return false;
        }

        // Forcar Nivel Acesso Documentos
        var optionsTipoDocumento = document.getElementById('selTipoDocumento').options;
        var nivelAcesso = document.getElementById('staNivelAcesso').value;
        var hipoteseLegal = document.getElementById('idHipoteseLegal').value;

        if( nivelAcesso == 'R' && hipoteseLegal == '' ){
            alert('Selecione a Hipotese Legal.');
            document.getElementById('idHipoteseLegal').focus();
            return false;
        }

        if( nivelAcesso != '' && optionsTipoDocumento.length == 0 ){
            alert('Selecione os Tipos de Documentos.');
            document.getElementById('txtTipoDocumento').focus();
            return false;
        }

        if( nivelAcesso == '' && optionsTipoDocumento.length > 0 ){
            alert('Selecione um Nível de Acesso.');
            document.getElementById('staNivelAcesso').focus();
            return false;
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
