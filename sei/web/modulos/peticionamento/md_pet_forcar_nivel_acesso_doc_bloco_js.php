<script type="application/javascript">

    var objLupaTipoDocumento = null;
    var objAutoCompletarTipoDocumento = null;

    // Extendendo inicializar()
    var masterInicializar = inicializar;
    inicializar = function() {
        masterInicializar.apply(this, arguments);

        carregarComponenteTipoDocumento();
        changeStaNivelAcesso();
    }

    function changeStaNivelAcesso() {
        document.getElementById('forcarHipoteseLegal').style.display = document.getElementById('staNivelAcesso').value == 'R' ? 'block' : 'none';
    }

    function limparForcarNivelAcessoDoc(){
        if(confirm('Deseja realmente limpar todos os campos da seção Forçar Nível de Acesso em Documentos Externos Específicos?')) {
            $('#fldForcarNivelAcessoDocumentosExternos').children(':input').each(function() { $(this).val(''); });
            $('#fldForcarNivelAcessoDocumentosExternos select>option:eq(0)').prop('selected', true);
            $('#fldForcarNivelAcessoDocumentosExternos select#idHipoteseLegal>option:eq(0)').prop('selected', true);
            $('#fldForcarNivelAcessoDocumentosExternos #selTipoDocumento').find('option').remove();
            document.getElementById('forcarHipoteseLegal').style.display = 'none';
        }
    }

    function carregarComponenteTipoDocumento(){
        objAutoCompletarTipoDocumento = new infraAjaxAutoCompletar('hdnIdTipoDocumento', 'txtTipoDocumento', '<?= $strLinkAjaxTiposDocumentos ?>');
        objAutoCompletarTipoDocumento.limparCampo = true;
        objAutoCompletarTipoDocumento.tamanhoMinimo = 2;

        objAutoCompletarTipoDocumento.prepararExecucao = function(){
            return 'palavras_pesquisa='+document.getElementById('txtTipoDocumento').value;
        }

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
        }

        objLupaTipoDocumento = new infraLupaSelect('selTipoDocumento', 'hdnTipoDocumento', '<?= $strLinkTipoDocumentoSelecao ?>');
    }

    // Extendendo onSubmitForm()
    var masterOnSubmitForm = onSubmitForm;
    onSubmitForm = function() {
        masterOnSubmitForm.apply(this, arguments);

        var optionsTipoDocumento = document.getElementById('selTipoDocumento').options;
        var nivelAcesso = document.getElementById('staNivelAcesso').value;
        var hipoteseLegal = document.getElementById('idHipoteseLegal').value;

        if( nivelAcesso == 'R' && hipoteseLegal == '' ){
            alert('Selecione a Hipotese Legal para o Nível de Acesso Restrito.');
            document.getElementById('idHipoteseLegal').focus();
            return false;
        }

        if( nivelAcesso != '' && optionsTipoDocumento.length == 0 ){
            alert('Selecione os Tipos de Documentos.');
            document.getElementById('txtTipoDocumento').focus();
            return false;
        }
    }
</script>
