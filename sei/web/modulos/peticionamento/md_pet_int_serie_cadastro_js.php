<script type="text/javascript">
    var objLupaTipoDocumentoEssencial = null
    var objAutoCompletarTipoDocumentoEssencial = null;

    function inicializar() {
        if ('<?=$_GET['acao']?>' == 'md_pet_int_serie_cadastrar') {
        } else if ('<?=$_GET['acao']?>' == 'md_pet_int_serie_consultar') {
            infraDesabilitarCamposAreaDados();
        } else {
            document.getElementById('btnCancelar').focus();
        }
        infraEfeitoTabelas();
        carregarComponenteTipoDocumentoEssencial();
    }

    function validarCadastro() {
        if (!infraSelectSelecionado('selSerie')) {
            alert('Selecione um Tipo de Documento.');
            document.getElementById('selSerie').focus();
            return false;
        }
        return true;
    }

    function OnSubmitForm() {
        return validarCadastro();
    }

    //Carrega o documento
    function carregarComponenteTipoDocumentoEssencial() {

        objAutoCompletarTipoDocumentoEssencial = new infraAjaxAutoCompletar('hdnIdSerie', 'txtSerie', '<?=$strLinkAjaxTipoDocumento?>');
        objAutoCompletarTipoDocumentoEssencial.limparCampo = true;
        objAutoCompletarTipoDocumentoEssencial.tamanhoMinimo = 3;

        objAutoCompletarTipoDocumentoEssencial.prepararExecucao = function () {
            var tipo = 'E';
            return 'palavras_pesquisa=' + document.getElementById('txtSerie').value + '&tipoDoc=' + tipo;
        };

        objAutoCompletarTipoDocumentoEssencial.processarResultado = function (id, nome, complemento) {

            if (id != '') {
                var options = document.getElementById('selSerie').options;

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
                    var opt = infraSelectAdicionarOption(document.getElementById('selSerie'), nome, id);
                    objLupaTipoDocumentoEssencial.atualizar();
                    opt.selected = true;
                }
                document.getElementById('txtSerie').value = '';
                document.getElementById('txtSerie').focus();
            }
        };

        objLupaTipoDocumentoEssencial = new infraLupaSelect('selSerie', 'hdnSerie', '<?=$strLinkTipoDocumentoEssencialSelecao?>');
    }
</script>
