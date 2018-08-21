<?php
    /**
     * Include de JS chamado pela pagina principal de cadastro/ediçao de peticionamento intercorrente
     */

    $strUrlAjaxNumeroProcesso = SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=md_pet_processo_validar_numero');
    $strUrlSubmit             = SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=md_pet_intercorrente_usu_ext_cadastrar');
    $strUrlMdPetUsuExtRemoverUploadArquivo             = SessaoSEIExterna::getInstance()->assinarLink('controlador_ajax_externo.php?acao_ajax=md_pet_intercorrente_usu_ext_remover_upload_arquivo');
?>

<script type="text/javascript">
    var objTabelaDinamicaProcesso = null;

    function inicializar() {
        iniciarGridDinamicaProcesso();
        inicializarDocumento();
        document.getElementById("txtNumeroProcesso").addEventListener("keyup", controlarEnterValidarProcesso, false);

        <?php if( isset( $_POST['id_procedimento'] ) ) { 
        	
        	$objEntradaConsultarProcedimentoAPI = new EntradaConsultarProcedimentoAPI();
        	$objEntradaConsultarProcedimentoAPI->setIdProcedimento( $_POST['id_procedimento'] );
        	$objSeiRN = new SeiRN();
        	$objSaidaConsultarProcedimentoAPI = $objSeiRN->consultarProcedimento( $objEntradaConsultarProcedimentoAPI );
        	    	
        	$numeroProcesso = $objSaidaConsultarProcedimentoAPI->getProcedimentoFormatado();
        	?>

         document.getElementById('txtNumeroProcesso').value = '<?= $numeroProcesso ?>';
         validarNumeroProcesso();
         adicionarProcesso();
         	
       <? } ?>
    }

    /**
     * Inicia Grid Dinâmica do Processo
     */
    function iniciarGridDinamicaProcesso() {
        objTabelaDinamicaProcesso = new infraTabelaDinamica('tbProcesso', 'hdnTbProcesso', false, true);
        objTabelaDinamicaProcesso.gerarEfeitoTabela = true;
        objTabelaDinamicaProcesso.remover = function () {
            return validarRemoverProcesso();
        };
    }

    /**
     * Add um processo na Grid Dinâmica
     */
    function adicionarProcesso() {
        iniciarGridDinamicaProcesso();
        var numeroProcesso = document.getElementById('txtNumeroProcesso');
        var tipoProcesso = document.getElementById('txtTipo');
        var processoIntercorrente = document.getElementById("hdnProcessoIntercorrente");
        var dataAtuacao = document.getElementById("hdnDataAtuacao");

        if (document.getElementById('hdnIdTipoProcedimento')==null       || document.getElementById('hdnIdTipoProcedimento').value==''
			|| document.getElementById('txtNumeroProcesso')==null        || numeroProcesso.value==''
			|| document.getElementById('txtTipo')==null                  || tipoProcesso.value==''
			|| document.getElementById('hdnProcessoIntercorrente')==null || processoIntercorrente.value==''
			|| document.getElementById('hdnDataAtuacao')==null           || dataAtuacao.value==''
		){
			return false;
        }
       
        objTabelaDinamicaProcesso.adicionar([document.getElementById('hdnIdTipoProcedimento').value, numeroProcesso.value, tipoProcesso.value, processoIntercorrente.value, dataAtuacao.value ]);

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
     */
    function abrirPeticionar() {
        if (validarCamposObrigatorios()) {
            var urlValida = document.getElementById('urlValidaAssinaturaProcesso');

            infraAbrirJanela(urlValida.value,
                'concluirPeticionamento',
                770,
                480,
                '', //options
                false); //modal*/
        }
    }


    /**
     * Validar Campos obrigataórios
     */
    function validarCamposObrigatorios() {
        var linhasTbProcesso = document.getElementById('tbProcesso').rows.length;
        if (linhasTbProcesso == 1) {
            alert('Informe o Processo.');
            return false;
        }

        var linhasTbArquivo = document.getElementById('tbDocumento').rows.length;
        if (linhasTbArquivo == 1) {
            alert('Adicione um Documento.');
            return false;
        }

        return true;
    }

    /**
     * Funções responsáveis pela validação do processo
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
            async: false,
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
                    document.getElementById('hdnDataAtuacao').value = $(r).find('DataGeracao').text();
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
    var labelFormato = {
        'N': 'Nato-Digital',
        'D': 'Digitalizado'
    };

	var MSGTOOLTIPNIVELACESSO = '<?=str_replace("'", "\'", PaginaSEI::montarTitleTooltip($strMsgTooltipNivelAcesso));?>';
	var MSGTOOLTIPHIPOTESELEGAL = '<?=str_replace("'", "\'", PaginaSEI::montarTitleTooltip($strMsgTooltipHipoteseLegal));?>';
	var MSGTOOLTIPNIVELACESSOPADRAOPREDEFINIDO = '<?=str_replace("'", "\'", PaginaSEI::montarTitleTooltip($strMsgTooltipNivelAcessoPadraoPreDefinido));?>';
	var MSGTOOLTIPHIPOTESELEGALPADRAOPREDEFINIDO = '<?=str_replace("'", "\'", PaginaSEI::montarTitleTooltip($strMsgTooltipHipoteseLegalPadraoPreDefinido));?>';

	function tooltip(tipo,evento,objeto){
		if (objeto==null) return false;
		switch (tipo) {
			case 1:
				MSGTOOLTIPNIVELACESSO = MSGTOOLTIPNIVELACESSO.replace(/\n/g, "\\n");
				MSGTOOLTIPNIVELACESSO = MSGTOOLTIPNIVELACESSO.replace('onmouseover=','');
				MSGTOOLTIPNIVELACESSO = MSGTOOLTIPNIVELACESSO.replace(/\"/g, '');
				MSGTOOLTIPNIVELACESSO = MSGTOOLTIPNIVELACESSO.replace(/return /g,'');
				var MSGTOOLTIPNIVELACESSO2 = MSGTOOLTIPNIVELACESSO.split(" onmouseout=");
				if (evento==0){ 
					objeto.addEventListener('mouseover', function () {
						eval (MSGTOOLTIPNIVELACESSO2[0]);
					});
				}
				if (evento==1){ 
					objeto.addEventListener('mouseout', function () {
						eval (MSGTOOLTIPNIVELACESSO2[1]);
					});
				}
				break;
			case 2:
				MSGTOOLTIPHIPOTESELEGAL = MSGTOOLTIPHIPOTESELEGAL.replace(/\n/g, "\\n");
				MSGTOOLTIPHIPOTESELEGAL = MSGTOOLTIPHIPOTESELEGAL.replace('onmouseover=','');
				MSGTOOLTIPHIPOTESELEGAL = MSGTOOLTIPHIPOTESELEGAL.replace(/\"/g, '');
				MSGTOOLTIPHIPOTESELEGAL = MSGTOOLTIPHIPOTESELEGAL.replace(/return /g,'');
				var MSGTOOLTIPHIPOTESELEGAL2 = MSGTOOLTIPHIPOTESELEGAL.split(" onmouseout=");
				if (evento==0){ 
					objeto.addEventListener('mouseover', function () {
						eval (MSGTOOLTIPHIPOTESELEGAL2[0]);
					});
				}
				if (evento==1){ 
					objeto.addEventListener('mouseout', function () {
						eval (MSGTOOLTIPHIPOTESELEGAL2[1]);
					});
				}
				break;
			case 3:
				MSGTOOLTIPNIVELACESSOPADRAOPREDEFINIDO = MSGTOOLTIPNIVELACESSOPADRAOPREDEFINIDO.replace(/\n/g, "\\n");
				MSGTOOLTIPNIVELACESSOPADRAOPREDEFINIDO = MSGTOOLTIPNIVELACESSOPADRAOPREDEFINIDO.replace('onmouseover=','');
				MSGTOOLTIPNIVELACESSOPADRAOPREDEFINIDO = MSGTOOLTIPNIVELACESSOPADRAOPREDEFINIDO.replace(/\"/g, '');
				MSGTOOLTIPNIVELACESSOPADRAOPREDEFINIDO = MSGTOOLTIPNIVELACESSOPADRAOPREDEFINIDO.replace(/return /g,'');
				var MSGTOOLTIPNIVELACESSOPADRAOPREDEFINIDO2 = MSGTOOLTIPNIVELACESSOPADRAOPREDEFINIDO.split(" onmouseout=");
				if (evento==0){ 
					objeto.addEventListener('mouseover', function () {
						eval (MSGTOOLTIPNIVELACESSOPADRAOPREDEFINIDO2[0]);
					});
				}
				if (evento==1){ 
					objeto.addEventListener('mouseout', function () {
						eval (MSGTOOLTIPNIVELACESSOPADRAOPREDEFINIDO2[1]);
					});
				}
				break;
			case 4:
				MSGTOOLTIPHIPOTESELEGALPADRAOPREDEFINIDO = MSGTOOLTIPHIPOTESELEGALPADRAOPREDEFINIDO.replace(/\n/g, "\\n");
				MSGTOOLTIPHIPOTESELEGALPADRAOPREDEFINIDO = MSGTOOLTIPHIPOTESELEGALPADRAOPREDEFINIDO.replace('onmouseover=','');
				MSGTOOLTIPHIPOTESELEGALPADRAOPREDEFINIDO = MSGTOOLTIPHIPOTESELEGALPADRAOPREDEFINIDO.replace(/\"/g, '');
				MSGTOOLTIPHIPOTESELEGALPADRAOPREDEFINIDO = MSGTOOLTIPHIPOTESELEGALPADRAOPREDEFINIDO.replace(/return /g,'');
				var MSGTOOLTIPHIPOTESELEGALPADRAOPREDEFINIDO2 = MSGTOOLTIPHIPOTESELEGALPADRAOPREDEFINIDO.split(" onmouseout=");
				if (evento==0){ 
					objeto.addEventListener('mouseover', function () {
						eval (MSGTOOLTIPHIPOTESELEGALPADRAOPREDEFINIDO2[0]);
					});
				}
				if (evento==1){ 
					objeto.addEventListener('mouseout', function () {
						eval (MSGTOOLTIPHIPOTESELEGALPADRAOPREDEFINIDO2[1]);
					});
				}
				break;
		}
    }
        
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
    
    function carregarFieldDocumentos() {

        //Combos que dependem do processo
        objAjaxSelectTipoDocumento.executar();

        //RN da Hipotese Legal
        verificarCriterioIntercorrente();

        document.getElementById('field_documentos').style.display = 'block';
    }

    function verificarCriterioIntercorrente() {
        var paramsAjax = {
            idTipoProcedimento: document.getElementById('hdnIdTipoProcedimento').value
        };

        $.ajax({
            url: '<?=$strUrlAjaxCriterioIntercorrente?>',
            type: 'POST',
            async: false,
            dataType: 'JSON',
            data: paramsAjax,
            success: function (r) {
                if (r.nivelAcesso) {
                    criarHiddenNivelAcesso(r.nivelAcesso);
                    if (EXIBIR_HIPOTESE_LEGAL && r.nivelAcesso.id == RESTRITO && r.hipoteseLegal) {
                        criarHiddenHipoteseLegal(r.hipoteseLegal);
                    }
                } else {
                    criarSelectNivelAcesso();
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
        var txtComplementoTipoDocumento = document.getElementById('txtComplementoTipoDocumento');

        divNivelAcesso.innerHTML = '';
        selNivelAcesso.name = 'selNivelAcesso';
        selNivelAcesso.id = 'selNivelAcesso';
        selNivelAcesso.className = 'infraSelect';
        selNivelAcesso.tabIndex = txtComplementoTipoDocumento.tabIndex + 1;

        selNivelAcesso.addEventListener('change', function () {
            exibirHipoteseLegal(selNivelAcesso.value);
        });
        divNivelAcesso.appendChild(selNivelAcesso);

        iniciarObjAjaxSelectNivelAcesso();
        objAjaxSelectNivelAcesso.executar();

        tooltip(1,0,document.getElementById("imgNivelAcesso"));
        tooltip(1,1,document.getElementById("imgNivelAcesso"));
        tooltip(2,0,document.getElementById("imgHipoteseLegal"));
        tooltip(2,1,document.getElementById("imgHipoteseLegal"));
    }

    function criarHiddenNivelAcesso(nivelAcesso) {
        var divNivelAcesso = document.getElementById('divNivelAcesso');
        var divHipoteseLegal = document.getElementById('divHipoteseLegal');
        var lblNivelAcesso = document.createElement('label');
        var hdnNivelAcesso = document.createElement('input');

        divNivelAcesso.innerHTML = '';

        if (divHipoteseLegal!=null){
			divHipoteseLegal.innerHTML = '';
        }

        lblNivelAcesso.className = 'infraLabel';
        lblNivelAcesso.id = 'lblNivelAcesso';
        lblNivelAcesso.innerHTML = nivelAcesso.descricao;
        divNivelAcesso.appendChild(lblNivelAcesso);

        hdnNivelAcesso.type = 'hidden';
        hdnNivelAcesso.name = 'selNivelAcesso';
        hdnNivelAcesso.id = 'hdnNivelAcesso';
        hdnNivelAcesso.value = nivelAcesso.id;
        divNivelAcesso.appendChild(hdnNivelAcesso);

        tooltip(3,0,document.getElementById("imgNivelAcesso"));
        tooltip(3,1,document.getElementById("imgNivelAcesso"));

    }

    function criarHiddenHipoteseLegal(hipoteseLegal) {
        var lblHipoteseLegal = document.createElement('label');
        var divBlcHipoteseLegal = document.getElementById('divBlcHipoteseLegal');
        var divHipoteseLegal = document.getElementById('divHipoteseLegal');
        var hdnHipoteseLegal = document.createElement('input');

        divHipoteseLegal.innerHTML = '';

        lblHipoteseLegal.className = 'infraLabel';
        lblHipoteseLegal.id = 'lblHipoteseLegal';
        lblHipoteseLegal.innerHTML = hipoteseLegal.descricao;
        divHipoteseLegal.appendChild(lblHipoteseLegal);

        hdnHipoteseLegal.type = 'hidden';
        hdnHipoteseLegal.name = 'selHipoteseLegal';
        hdnHipoteseLegal.id = 'hdnHipoteseLegal';
        hdnHipoteseLegal.value = hipoteseLegal.id;
        divHipoteseLegal.appendChild(hdnHipoteseLegal);

        divBlcHipoteseLegal.style.display = 'block';

        tooltip(3,0,document.getElementById("imgNivelAcesso"));
        tooltip(3,1,document.getElementById("imgNivelAcesso"));
        tooltip(4,0,document.getElementById("imgHipoteseLegal"));
        tooltip(4,1,document.getElementById("imgHipoteseLegal"));
    }


    function exibirTipoConferencia() {
        var formato = '';
        var divTipoConferencia = document.getElementById('divTipoConferencia');
        var selTipoConferencia = document.getElementById('selTipoConferencia');
        divTipoConferencia.style.display = 'none';
        selTipoConferencia.value = 'null';

        var radiosFormato = document.getElementsByName("rdoFormato");

        for (var i = 0; i < radiosFormato.length; i++) {
            if (radiosFormato[i].checked == true) {
                formato = radiosFormato[i].value;
                break;
            }
        }

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

    function exibirHipoteseLegal(nivelAcesso) {
		if (document.getElementById('divBlcHipoteseLegal')==null
			|| document.getElementById('selHipoteseLegal')==null){
			return false;
		}

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
        objTabelaDinamicaDocumento.remover = function (arrLinha) {
            //remove o arquivo da pasta temp
            $.ajax({
                url: '<?=$strUrlMdPetUsuExtRemoverUploadArquivo?>',
                type: 'POST',
                async: false,
                dataType: 'XML',
                data: {hdnTbDocumento:arrLinha},
                success: function (r) {
                    verificarTabelaVazia(2);
                },
                error: function (e) {
                    console.error('Erro ao processar o XML do SEI: ' + e.responseText);
                }
            });

            return true;
        };
    }

    //Essa validação só é executada em browsers com suport ao HTML5
    function validarArquivo(input) {
        if (input.value != '') {
            var tamanhoArquivo = input.files[0].size;
            var ext = input.files[0].name.split('.').pop().toLowerCase();

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
        var fileArquivo = document.getElementById('fileArquivo');
        if (fileArquivo.value.trim() == '') {
            alert('Informe o arquivo para upload.');
            fileArquivo.focus();
            return false;
        }
        var tipoDocumento = document.getElementById('selTipoDocumento');
        if (tipoDocumento == null || tipoDocumento.value == 'null') {
            alert('Informe o Tipo de Documento.');
            document.getElementById('selTipoDocumento').focus();
            return false;
        }
        var complementoTipoDocumento = document.getElementById('txtComplementoTipoDocumento').value.trim();
        if (complementoTipoDocumento == '') {
            alert('Informe o Complemento do Tipo de Documento. Para mais informações, clique no ícone de Ajuda ao lado do nome do campo.');
            document.getElementById('txtComplementoTipoDocumento').focus();
            return false;
        }
        var nivelAcesso = document.getElementById('selNivelAcesso');

        if (nivelAcesso) {
            if (nivelAcesso == null || nivelAcesso.value == '') {
                alert('Informe o Nível de Acesso.');
                document.getElementById('selNivelAcesso').focus();
                return false;
            }
        } else {
            nivelAcesso = document.getElementById('hdnNivelAcesso');
        }

        var selHipoteseLegal = document.getElementById('selHipoteseLegal');

        if (nivelAcesso.value == RESTRITO) {
            if (selHipoteseLegal && selHipoteseLegal.value == '') {
                alert('Informe a Hipótese Legal');
                selHipoteseLegal.focus();
                return false;
            }
        }

        var formato = null;
        var rdoFormato = document.getElementsByName('rdoFormato');
        for (var i = 0; i < rdoFormato.length; i++) {
            if (rdoFormato[i].checked == true) {
                formato = parseInt(rdoFormato[i].value);
                break;
            }
        }

        if (formato == null) {
            alert('Informe o Formato do Documento.');
            document.getElementById('rdoNatoDigital').focus();
            return false;
        }

        var selTipoConferencia = document.getElementById('selTipoConferencia');
        if (selTipoConferencia.offsetHeight > 0) {
            if (selTipoConferencia == null || selTipoConferencia.value == 'null') {
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

        var formato = '';
        var rdoFormato = document.getElementsByName('rdoFormato');


        var elTipoDocumento = document.getElementById('selTipoDocumento');
        var tipoDocumento = elTipoDocumento.options[elTipoDocumento.selectedIndex].text;

        // montando o nome do documento
        var complementoTipoDocumento = document.getElementById('txtComplementoTipoDocumento').value.trim();
        complementoTipoDocumento = $("<pre>").text(complementoTipoDocumento).html();
        var documento = tipoDocumento + ' ' + complementoTipoDocumento;

        // pegando o nivel de acesso selecionado
        var nivelAcesso = '-';
        var idNivelAcesso = '';
        var elNivelAcesso = document.getElementById('selNivelAcesso');
        if (elNivelAcesso) {
            nivelAcesso = elNivelAcesso.options[elNivelAcesso.selectedIndex].text;
            idNivelAcesso = elNivelAcesso.value;
        } else {
            nivelAcesso = document.getElementById('lblNivelAcesso').innerHTML.trim();
            idNivelAcesso = document.getElementById('hdnNivelAcesso').value;
        }

        //ids
        var idLinha = gerarIdDocumento();
        var idTipoDocumento = document.getElementById('selTipoDocumento').value;
        var complementoTipoDocumento = document.getElementById('txtComplementoTipoDocumento').value;
        var idHipoteseLegal = '';
        if(document.getElementById('selHipoteseLegal')){
            idHipoteseLegal = document.getElementById('selHipoteseLegal').value;
        } else if(document.getElementById('hdnHipoteseLegal')){
            idHipoteseLegal = document.getElementById('hdnHipoteseLegal').value;
        }

        var idTipoConferencia = '';
        var idFormato = '';

        for (var i = 0; i < rdoFormato.length; i++) {
            if (rdoFormato[i].checked == true) {
                idFormato = rdoFormato[i].value;
                break;
            }
        }

        if(idFormato == DIGITAL) {
            idTipoConferencia = document.getElementById('selTipoConferencia').value;
        }

        formato = labelFormato[idFormato];

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

        if (document.getElementById('selNivelAcesso')) {
            document.getElementById('selNivelAcesso').value = '';
        }

        if (document.getElementById('selHipoteseLegal')) {
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
            remover = confirm('Ao remover este processo os documentos abaixo carregados serão desconsiderados e somente poderão ser carregados novamente após adicionar novo número de processo.\n\n Deseja continuar?');
        }

        if (remover) {
            //remove os arquivos da pasta temp
            $.ajax({
                url: '<?=$strUrlMdPetUsuExtRemoverUploadArquivo?>',
                type: 'POST',
                async: false,
                dataType: 'XML',
                data: {hdnTbDocumento:objTabelaDinamicaDocumento.hdn.value},
                success: function (r) {
                    verificarTabelaVazia(2);
                },
                error: function (e) {
                    console.error('Erro ao processar o XML do SEI: ' + e.responseText);
                }
            });
           location.href=location.href;
        }
        return false;
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
            for (var i = 1; i < tbDocumento.rows.length; i++) {
                var tr = tbDocumento.getElementsByTagName('tr')[i];
                if (arr['nome'].toLowerCase().trim() == tr.cells[9].innerText.toLowerCase().trim()) {
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
            var ext = fileArquivo.value.split('.').pop().toLowerCase();
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
                alert("O arquivo selecionado não é permitido.\n Somente são permitidos arquivos com as extensões:\n" + arrExtensoesPermitidas.join().replace(/,/g, ' '));
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


    function controlarEnterValidarProcesso(e){
       var focus = returnElementFocus();
        if(infraGetCodigoTecla(e)==13) {
            document.getElementById('btnValidar').onclick();
        }
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


    //===============================================================================================================//
    //--------------------------------------------- FIM DOCUMENTO ---------------------------------------------------//
    //===============================================================================================================//


</script>