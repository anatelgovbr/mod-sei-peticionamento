<?php
    /**
     * @since  25/11/2016
     * @author Jaqueline Mendes <jaqueline.mendes@castgroup.com.br>
     * @author André Luiz <andre.luiz@castgroup.com.br>
     *
     * Include de JS chamado pela pagina principal de cadastro/ediçao de peticionamento intercorrente
     */

    $strUrlAjaxNumeroProcesso = SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=validar_numero_processo_peticionamento');
    $strUrlSubmit             = SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=md_pet_intercorrente_usu_ext_cadastrar');
?>

<script type="text/javascript">
    var objTabelaDinamicaProcesso = null;

    function inicializar() {
        iniciarGridDinamicaProcesso();
        inicializarDocumento();
    }

    /**
     * Inicia Grid Dinâmica do Processo
     * @author: Jaqueline Mendes <jaqueline.mendes@castgroup.com.br>
     * @since: 28/11/2016
     */
    function iniciarGridDinamicaProcesso() {
        objTabelaDinamicaProcesso = new infraTabelaDinamica('tbProcesso', 'hdnTbProcesso', false, true);
        objTabelaDinamicaProcesso.gerarEfeitoTabela = true;
        objTabelaDinamicaProcesso.remover = function () {
            if (validarRemoverProcesso()) {
                document.getElementById('tbProcesso').style.display = 'none';
                document.getElementById('txtNumeroProcesso').removeAttribute("disabled");
                document.getElementById('btnValidar').removeAttribute('disabled');
                return true;
            }
            return false;
        };
    }

    /**
     * Add um processo na Grid Dinâmica
     * @author: Jaqueline Mendes <jaqueline.mendes@castgroup.com.br>
     * @since: 28/11/2016
     */
    function adicionarProcesso() {
        var numeroProcesso = document.getElementById('txtNumeroProcesso');
        var tipoProcesso = document.getElementById('txtTipo');
        var processoIntercorrente = document.getElementById("hdnProcessoIntercorrente");

        objTabelaDinamicaProcesso.adicionar([document.getElementById('hdnIdTipoProcedimento').value, numeroProcesso.value, tipoProcesso.value, processoIntercorrente.value, '28/11/2016']);

        document.getElementById('tbProcesso').style.display = '';
        document.getElementById('btnAdicionar').style.display = 'none';
        document.getElementById('txtNumeroProcesso').setAttribute("disabled", "disabled");
        document.getElementById('btnValidar').setAttribute("disabled", "disabled");

        numeroProcesso.value = '';
        tipoProcesso.value = '';

        //Carrega os campos de documento junto com suas RN's
        carregarFieldDocumentos();

    }

    /**
     * Salva um peticionamento
     * @author: Jaqueline Mendes <jaqueline.mendes@castgroup.com.br>
     * @since: 28/11/2016
     */
    function abrirPeticionar() {
        if (validarCamposObrigatorios()) {
            var urlValida = document.getElementById('urlValidaAssinaturaProcesso');

            infraAbrirJanela(urlValida.value,
                'concluirPeticionamento',
                770,
                464,
                '', //options
                false); //modal*/
        }
    }


    /**
     * Validar Campos obrigataórios
     * @author: Jaqueline Mendes <jaqueline.mendes@castgroup.com.br>
     * @since: 08/12/2016
     */
    function validarCamposObrigatorios() {
        var linhasTbProcesso = document.getElementById('tbProcesso').rows.length;

        if (linhasTbProcesso == 1) {
            alert('Informe o Processo.');
            return false;
        }

        /*var linhaTbDocumento = document.getElementById('tbDocumento').rows.length;

         if (linhaTbDocumento == 1) {
         alert('Informe ao menos um Documento.');
         return false;
         }*/

        return true;
    }

    /**
     * Funções responsáveis pela validação do processo
     * @author: Jaqueline Mendes <jaqueline.mendes@castgroup.com.br>
     * @since: 28/11/2016
     */
    function validarNumeroProcesso() {

        var numeroProcessoPreenchido = document.getElementById('txtNumeroProcesso').value != '';
        if (!numeroProcessoPreenchido) {
            alert('Informe o Número.');
            return false;
        }

        var paramsAjax = {
            txtNumeroProcesso: document.getElementById('txtNumeroProcesso').value
        };

        $.ajax({
            url: '<?=$strUrlAjaxNumeroProcesso?>',
            type: 'POST',
            dataType: 'XML',
            data: paramsAjax,
            success: function (r) {
                if (!$(r).find('IdTipoProcedimento').text()) {
                    inicializarCamposPadroesProcesso();
                    alert($(r).find('MensagemValidacao').text());
                }
                else {
                    document.getElementById('txtNumeroProcesso').value = $(r).find('numeroProcesso').text();
                    document.getElementById('hdnIdTipoProcedimento').value = $(r).find('IdTipoProcedimento').text();
                    document.getElementById('hdnIdProcedimento').value = $(r).find('IdProcedimento').text();
                    document.getElementById('txtTipo').value = $(r).find('TipoProcedimento').text();
                    document.getElementById('btnAdicionar').style.display = '';
                    document.getElementById('hdnProcessoIntercorrente').value = $(r).find('ProcessoIntercorrente').text();
                    document.getElementById('urlValidaAssinaturaProcesso').value = $(r).find('UrlValida').text();
                }
            },
            error: function (e) {
                console.error('Erro ao processar o XML do SEI: ' + e.responseText);
            }
        });
    }

    function inicializarCamposPadroesProcesso() {
        document.getElementById('txtTipo').value = '';
        document.getElementById('btnAdicionar').style.display = 'none';
    }


    //===============================================================================================================//
    //--------------------------------------------- INICIO DOCUMENTO ------------------------------------------------//
    //===============================================================================================================//

    /**
     * Funções responsáveis pelo controle do fieldset Documentos
     * @author: André Luiz <andre.luiz@castgroup.com>
     * @since: 28/11/2016
     */
    var NATO_DIGITAL = 'N';
    var DIGITAL = 'D';
    var PUBLICO = '<?= ProtocoloRN::$NA_PUBLICO?>';
    var RESTRITO = '<?= ProtocoloRN::$NA_RESTRITO?>';
    var SIGILOSO = '<?= ProtocoloRN::$NA_SIGILOSO?>';
    var TAMANHO_MAXIMO = '<?=$tamanhoMaximo?>';
    var EXIBIR_HIPOTESE_LEGAL = '<?=$exibirHipoteseLegal?>';
    var arrExtensoesPermitidas = [<?=$extensoesPermitidas?>];
    var objTabelaDinamicaDocumento = null;
    var objUploadArquivo = null;
    var objAjaxSelectTipoDocumento = null;
    var objAjaxSelectNivelAcesso = null;
    var objAjaxSelectHipoteseLegal = null;


    function inicializarDocumento() {
        iniciarTabelaDinamicaDocumento();
        iniciarObjUploadArquivo();
        iniciarObjAjaxSelectTipoDocumento();

        //=======================================================//
        //------ Validação para Browse com suporte a HTML5 ------//
        //=======================================================//
        if (window.FileReader && window.File && window.FileList && window.Blob) {
            var input = document.getElementById('fileArquivo');
            input.addEventListener('change', function () {
                validarArquivo(input);
            });
        }
        //=======================================================//
        //------------------ Fim da Validação -------------------//
        //=======================================================//


    }


    function iniciarObjAjaxSelectTipoDocumento() {
        objAjaxSelectTipoDocumento = new infraAjaxMontarSelect('selTipoDocumento', '<?= $strUrlAjaxMontarSelectTipoDocumento?>');
    }

    function iniciarObjAjaxSelectNivelAcesso() {
        objAjaxSelectNivelAcesso = new infraAjaxMontarSelect('selNivelAcesso', '<?= $strUrlAjaxMontarSelectNivelAcesso?>');
        objAjaxSelectNivelAcesso.prepararExecucao = function () {
            return 'id_tipo_procedimento=' + document.getElementById('hdnIdTipoProcedimento').value;
        };

    }

    function iniciarObjAjaxSelectHipoteseLegal() {
        objAjaxSelectHipoteseLegal = new infraAjaxMontarSelect('selHipoteseLegal', '<?= $strUrlAjaxMontarHipoteseLegal?>');
        objAjaxSelectHipoteseLegal.processarResultado = function () {
            return 'nivelAcesso=' + RESTRITO;
        }

    }

    function carregarFieldDocumentos() {

        //Combos que dependem do processo
        objAjaxSelectTipoDocumento.executar();

        //RN da Hipotese Legal
        verificarCriterioIntercorrente();

        document.getElementById('field_documentos').style.display = '';
    }

    function verificarCriterioIntercorrente() {

        var paramsAjax = {
            idTipoProcedimento: document.getElementById('hdnIdTipoProcedimento').value
        };

        $.ajax({
            url: '<?=$strUrlAjaxCriterioIntercorrente?>',
            type: 'POST',
            dataType: 'JSON',
            data: paramsAjax,
            success: function (r) {
                if (r.nivelAcesso) {
                    criarHiddenNivelAcesso(r.nivelAcesso);
                } else {
                    criarSelectNivelAcesso();
                }

                if (EXIBIR_HIPOTESE_LEGAL) {
                    if (r.hipoteseLegal) {
                        criarHiddenHipoteseLegal(r.hipoteseLegal);
                    } else {
                        criarSelectHipoteseLegal();
                    }
                }
            },
            error: function (e) {
                console.error('Erro ao processar o AJAX do SEI: ' + e.responseText);
            }

        });
    }

    function criarSelectNivelAcesso() {
        var selNivelAcesso = document.createElement("select");
        var divNivelAcesso = document.getElementById('divNivelAcesso');

        divNivelAcesso.innerHTML = '';
        selNivelAcesso.name = 'selNivelAcesso';
        selNivelAcesso.id = 'selNivelAcesso';
        selNivelAcesso.className = 'infraSelect';
        selNivelAcesso.addEventListener('change', function () {
            exibirHipoteseLegal(selNivelAcesso);
        });
        divNivelAcesso.appendChild(selNivelAcesso);

        iniciarObjAjaxSelectNivelAcesso();
        objAjaxSelectNivelAcesso.executar();
    }

    function criarHiddenNivelAcesso(nivelAcesso) {
        var divNivelAcesso = document.getElementById('divNivelAcesso');
        var divHipoteseLegal = document.getElementById('divHipoteseLegal');
        var lblNivelAcesso = document.createElement('label');
        var hdnNivelAcesso = document.createElement('input');

        divNivelAcesso.innerHTML = '';
        divHipoteseLegal.innerHTML = '';

        lblNivelAcesso.className = 'infraLabelRadio';
        lblNivelAcesso.id = 'lblNivelAcesso';
        lblNivelAcesso.innerHTML = nivelAcesso.descricao;
        divNivelAcesso.appendChild(lblNivelAcesso);

        hdnNivelAcesso.type = 'hidden';
        hdnNivelAcesso.name = 'selNivelAcesso';
        hdnNivelAcesso.id = 'hdnNivelAcesso';
        hdnNivelAcesso.value = nivelAcesso.id;
        divNivelAcesso.appendChild(hdnNivelAcesso);
    }

    function criarSelectHipoteseLegal() {

        var selHipoteseLegal = document.createElement("select");
        var divHipoteseLegal = document.getElementById('divHipoteseLegal');
        var divBlcHipoteseLegal = document.getElementById('divBlcHipoteseLegal');

        divHipoteseLegal.innerHTML = '';
        divBlcHipoteseLegal.style.display = 'none';
        selHipoteseLegal.id = 'selHipoteseLegal';
        selHipoteseLegal.name = 'selHipoteseLegal';
        selHipoteseLegal.className = 'infraSelect';
        divHipoteseLegal.appendChild(selHipoteseLegal);

        iniciarObjAjaxSelectHipoteseLegal();
        objAjaxSelectHipoteseLegal.executar();

    }

    function criarHiddenHipoteseLegal(hipoteseLegal) {
        var lblHipoteseLegal = document.createElement('label');
        var divBlcHipoteseLegal = document.getElementById('divBlcHipoteseLegal');
        var divHipoteseLegal = document.getElementById('divHipoteseLegal');
        var hdnHipoteseLegal = document.createElement('input');

        divHipoteseLegal.innerHTML = '';

        lblHipoteseLegal.className = 'infraLabelRadio';
        lblHipoteseLegal.id = 'lblHipoteseLegal';
        lblHipoteseLegal.innerHTML = hipoteseLegal.descricao;
        divHipoteseLegal.appendChild(lblHipoteseLegal);

        hdnHipoteseLegal.type = 'hidden';
        hdnHipoteseLegal.name = 'selHipoteseLegal';
        hdnHipoteseLegal.id = 'hdnHipoteseLegal';
        hdnHipoteseLegal.value = hipoteseLegal.id;
        divHipoteseLegal.appendChild(hdnHipoteseLegal);

        divBlcHipoteseLegal.style.display = '';
    }


    function exibirTipoConferencia() {
        var formato = document.querySelector('input[name="rdoFormato"]:checked').value;
        var divTipoConferencia = document.getElementById('divTipoConferencia');
        var selTipoConferencia = document.getElementById('selTipoConferencia');
        divTipoConferencia.style.display = 'none';
        selTipoConferencia.value = 'null';

        //Ajusta a posição quando o browser é o IE;
        if (isInternetExplorer()) {
            selTipoConferencia.style.marginTop = '-3px';
            selTipoConferencia.style.display = 'inline-block';
        }

        if (formato == DIGITAL) {
            divTipoConferencia.style.marginTop = '-5px';
            divTipoConferencia.style.display = '';
        }

    }

    function exibirHipoteseLegal(select) {
        var nivelAcesso = select.querySelector('option:checked').value;
        var divBlcHipoteseLegal = document.getElementById('divBlcHipoteseLegal');

        document.getElementById('selHipoteseLegal').value = '';
        divBlcHipoteseLegal.style.display = 'none';
        if (nivelAcesso == RESTRITO) {
            divBlcHipoteseLegal.style.display = '';
        }

    }

    function verificarTabelaVazia(qtdLinha) {
        var tbDocumento = document.getElementById('tbDocumento');
        var ultimoRegistro = tbDocumento.rows.length == qtdLinha;
        if (ultimoRegistro) {
            tbDocumento.style.display = 'none';
        }
    }

    function iniciarTabelaDinamicaDocumento() {
        objTabelaDinamicaDocumento = new infraTabelaDinamica('tbDocumento', 'hdnTbDocumento', false, true);
        objTabelaDinamicaDocumento.gerarEfeitoTabela = true;
        objTabelaDinamicaDocumento.remover = function () {
            verificarTabelaVazia(2);
            return true;
        };
    }

    //Essa validação só é executada em browsers com suport ao HTML5
    function validarArquivo(input) {
        if (input.value != '') {
            var tamanhoArquivo = input.files[0].size;
            var ext = input.files[0].name.split('.').pop();

            var tamanhoConfigurado = parseInt(TAMANHO_MAXIMO) > 0;
            if (!tamanhoConfigurado) {
                alert('Limite não configurado na Administração do Sistema.');
                input.value = '';
                input.focus();
                return false;
            }

            var extensaoConfigurada = arrExtensoesPermitidas.length > 0;
            if (!extensaoConfigurada) {
                alert('Extensão de Arquivos Permitidos não foi configurado na Administração do Sistema.');
                input.value = '';
                input.focus();
                return false;
            }

            var arquivoPermitido = arrExtensoesPermitidas.indexOf(ext) != -1;
            var tamanhoArquivo = (tamanhoArquivo / 1024 / 1024).toFixed(2);
            if (tamanhoArquivo > parseInt(TAMANHO_MAXIMO)) {
                alert('Tamanho máximo para o arquivo é de ' + TAMANHO_MAXIMO + 'Mb');
                input.value = '';
                input.focus();
                return false;
            }

            if (!arquivoPermitido) {
                alert("O arquivo selecionado não é permitido.\n" +
                    "Somente são permitidos arquivos com as extensões:\n" +
                    arrExtensoesPermitidas.join().replace(/,/g, ' '));
                input.value = '';
                input.focus();
                return false;
            }
        }
    }

    function validarDocumento() {
        var tipoDocumento = document.querySelector('#selTipoDocumento option:checked');
        var complementoTipoDocumento = document.getElementById('txtComplementoTipoDocumento').value.trim();
        var nivelAcesso = document.querySelector('#selNivelAcesso option:checked');
        var hipoteseLegal = document.querySelector('#selHipoteseLegal option:checked');
        var selHipoteseLegal = document.getElementById('selHipoteseLegal');
        var formato = document.querySelector('input[name="rdoFormato"]:checked');
        var selTipoConferencia = document.getElementById('selTipoConferencia');
        var tipoConferencia = document.querySelector('#selTipoConferencia option:checked');
        var fileArquivo = document.getElementById('fileArquivo');

        if (fileArquivo.value.trim() == '') {
            alert('Informe o arquivo para upload.');
            fileArquivo.focus();
            return false;
        }

        if (tipoDocumento == null || tipoDocumento.value == 'null') {
            alert('Informe o Tipo de Documento.');
            document.getElementById('selTipoDocumento').focus();
            return false;
        }

        if (complementoTipoDocumento == '') {
            alert('Informe o Complemento do Tipo de Documento. Para mais informações, clique no ícone de Ajuda ao lado do nome do campo.');
            document.getElementById('txtComplementoTipoDocumento').focus();
            return false;
        }

        if (document.querySelector('[name="selNivelAcesso"]').nodeName.toLowerCase() == 'select') {
            if (nivelAcesso == null || nivelAcesso.value == '') {
                alert('Informe o Nível de Acesso.');
                document.getElementById('selNivelAcesso').focus();
                return false;
            }
        }

        if (document.querySelector('[name="selHipoteseLegal"]').nodeName.toLowerCase() == 'select') {
            if (selHipoteseLegal.offsetHeight > 0) {
                if (hipoteseLegal == null || hipoteseLegal.value == '') {
                    alert('Informe a Hipótese Legal.');
                    selHipoteseLegal.focus();
                    return false;
                }
            }
        }

        if (formato == null) {
            alert('Informe o Formato do Documento.');
            document.getElementById('rdoNatoDigital').focus();
            return false;
        }

        if (selTipoConferencia.offsetHeight > 0) {
            if (tipoConferencia == null || tipoConferencia.value == 'null') {
                alert('Informe a Conferência com o documento digitalizado.');
                selTipoConferencia.focus();
                return false;
            }
        }

        return true;

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

    function criarRegistroTabelaDocumento(arr) {
        var nomeArquivo = arr['nome'];
        var nomeArquivoHash = arr['nome_upload'];
        var tamanhoArquivo = arr['tamanho'];
        var tamanhoArquivoFormatado = infraFormatarTamanhoBytes(tamanhoArquivo);
        var dataHora = arr['data_hora'];

        var formato = document.querySelector('input[name="rdoFormato"]:checked').nextSibling.nextSibling.innerHTML.trim();
        var tipoDocumento = document.querySelector('#selTipoDocumento option:checked').text;
        var complementoTipoDocumento = document.getElementById('txtComplementoTipoDocumento').value.trim();
        complementoTipoDocumento = $("<pre>").text(complementoTipoDocumento).html();
        var documento = tipoDocumento + ' ' + complementoTipoDocumento;

        var nivelAcesso = '-';
        if (document.querySelector('[name="selNivelAcesso"]').nodeName.toLowerCase() == 'select') {
            nivelAcesso = document.querySelector('#selNivelAcesso option:checked').text;
        } else {
            nivelAcesso = document.getElementById('lblNivelAcesso').innerHTML.trim();
        }

        //ids
        var idLinha = gerarIdDocumento();
        var idTipoDocumento = document.getElementById('selTipoDocumento').value;
        var complementoTipoDocumento = document.getElementById('txtComplementoTipoDocumento').value;
        var idNivelAcesso = document.getElementById('selNivelAcesso').value;
        var idHipoteseLegal = document.getElementById('selHipoteseLegal').value;
        var idFormato = document.querySelector('input[name="rdoFormato"]:checked').value;
        var idTipoConferencia = document.getElementById('selTipoConferencia').value;

        var dados = [
            idLinha, idTipoDocumento, complementoTipoDocumento, idNivelAcesso,
            idHipoteseLegal, idFormato, idTipoConferencia, nomeArquivoHash, tamanhoArquivo, nomeArquivo,
            dataHora, tamanhoArquivoFormatado, documento, nivelAcesso, formato
        ];

        objTabelaDinamicaDocumento.adicionar(dados);
    }

    function corrigirPosicaoAcaoExcluir() {
        var trs = document.getElementById('tbDocumento').getElementsByTagName('tr');
        for (var i = 1; i < trs.length; i++) {
            var tds = trs[i].getElementsByTagName('td');
            var td = tds[tds.length - 1];
            td.setAttribute('valign', 'center');
        }
    }

    function limparCampoDocumento() {
        document.getElementById('fileArquivo').value = '';
        document.getElementById('selTipoDocumento').value = 'null';
        document.getElementById('txtComplementoTipoDocumento').value = '';

        var selNivelAcesso = document.querySelector('[name="selNivelAcesso"]');
        if (selNivelAcesso.nodeName.toLowerCase() == 'select') {
            document.getElementById('selNivelAcesso').value = '';
        }

        var selHipoteseLegal = document.querySelector('[name="selHipoteseLegal"]');
        if (selHipoteseLegal.nodeName.toLowerCase() == 'select') {
            document.getElementById('selHipoteseLegal').value = '';
            document.getElementById('divBlcHipoteseLegal').style.display = 'none';
        }

        document.getElementById('rdoNatoDigital').checked = false;
        document.getElementById('rdoDigitalizado').checked = false;
        document.getElementById('selTipoConferencia').value = 'null';
        document.getElementById('divTipoConferencia').style.display = 'none';
    }

    function limparTabelaDocumento() {
        objTabelaDinamicaDocumento.limpar();
        verificarTabelaVazia(1);
    }

    function validarRemoverProcesso() {
        var remover = true;
        var tbDocumento = document.getElementById('tbDocumento');

        if (tbDocumento.rows.length > 1) {
            remover = confirm('Ao remover este processo os documentos abaixo carregados serão desconsiderados e somente' +
                ' poderão ser carregados novamente após adicionar novo número de processo.\n\n' +
                'Deseja continuar?');

        }

        if (remover) {
            limparCampoDocumento();
            limparTabelaDocumento();
            document.getElementById('field_documentos').style.display = 'none';
        }
        return remover;
    }

    function gerarIdDocumento() {
        var hdnIdDocumento = document.getElementById('hdnIdDocumento');
        hdnIdDocumento.value = parseInt(hdnIdDocumento.value) + 1;
        return hdnIdDocumento.value;
    }

    function adicionarDocumento() {
        if (validarDocumento()) {
            objUploadArquivo.executar();
            document.getElementById('tbDocumento').style.display = '';
        }
    }

    function iniciarObjUploadArquivo() {
        var tbDocumento = document.getElementById('tbDocumento');
        objUploadArquivo = new infraUpload('frmPeticionamentoIntercorrente', '<?=$strLinkUploadArquivo?>');
        objUploadArquivo.finalizou = function (arr) {
            //===========================================//
            //--------- Validações pós-upload  ----------//
            //===========================================//

            //Tamanho do Arquivo
            var fileArquivo = document.getElementById('fileArquivo');
            var tamanhoArquivo = (arr['tamanho'] / 1024 / 1024).toFixed(2);
            if (tamanhoArquivo > parseInt(TAMANHO_MAXIMO)) {
                alert('Tamanho máximo para o arquivo é de ' + TAMANHO_MAXIMO + 'Mb');
                fileArquivo.value = '';
                fileArquivo.focus();
                verificarTabelaVazia(1);
                return false;
            }


            //Arquivo com o mesmo nome já adicionado
            for (var i = 0; i < tbDocumento.rows.length; i++) {
                var tr = tbDocumento.getElementsByTagName('tr')[i];
                if (arr['nome'].toLowerCase().trim() == tr.cells[1].innerText.toLowerCase().trim()) {
                    alert('Não é permitido adicionar documento com o mesmo nome de arquivo.');
                    fileArquivo.value = '';
                    fileArquivo.focus();
                    verificarTabelaVazia(1);
                    return false;
                }
            }

            //===========================================//
            //------------- Fim Validações --------------//
            //===========================================//

            criarRegistroTabelaDocumento(arr);
            corrigirPosicaoAcaoExcluir();
            limparCampoDocumento();
        };

        objUploadArquivo.validar = function () {
            var fileArquivo = document.getElementById('fileArquivo');
            var ext = fileArquivo.value.split('.').pop();
            var extensaoConfigurada = arrExtensoesPermitidas.length > 0;

            var tamanhoConfigurado = parseInt(TAMANHO_MAXIMO) > 0;
            if (!tamanhoConfigurado) {
                alert('Limite não configurado na Administração do Sistema.');
                fileArquivo.value = '';
                fileArquivo.focus();
                return false;
            }

            if (!extensaoConfigurada) {
                alert('Extensão de Arquivos Permitidos não foi configurado na Administração do Sistema.');
                fileArquivo.value = '';
                fileArquivo.focus();
                return false;
            }

            var arquivoPermitido = arrExtensoesPermitidas.indexOf(ext) != -1;
            if (!arquivoPermitido) {
                alert("O arquivo selecionado não é permitido.\n" +
                    "Somente são permitidos arquivos com as extensões:\n" +
                    arrExtensoesPermitidas.join().replace(/,/g, ' '));
                fileArquivo.value = '';
                fileArquivo.focus();
                return false;
            }
            return true;
        };
    }

    function controlarChangeNumeroProcesso(){
        document.getElementById('txtTipo').value = '';
        document.getElementById('btnValidar').removeAttribute('disabled');
        document.getElementById('btnAdicionar').style.display = 'none';
    }

    //===============================================================================================================//
    //--------------------------------------------- FIM DOCUMENTO ---------------------------------------------------//
    //===============================================================================================================//


</script>