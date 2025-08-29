<script type="text/javascript">
    function inicializar() {
        if ('<?=$_GET['acao']?>' != 'hipotese_legal_nl_acesso_peticionamento_consultar') {
            carregarComponenteHipoteseLegal();
        }
        if ('<?=$_GET['acao']?>' == 'md_pet_hipotese_legal_nl_acesso_cadastrar') {
            document.getElementById('txtHipoteseLgl').focus();
        } else if ('<?=$_GET['acao']?>' == 'hipotese_legal_nl_acesso_peticionamento_consultar') {
            infraDesabilitarCamposAreaDados();
        } else {
            document.getElementById('btnFechar').focus();
        }
        infraEfeitoTabelas();
    }

    function validarCadastro() {

        var optionsSub = document.getElementById('selDescricaoHpLegalNvAcesso').options;

        if (optionsSub.length == 0) {
            alert('Informe ao menos uma Hipótese Legal.');
            document.getElementById('selDescricaoHpLegalNvAcesso').focus();
            return false;
        }

        return true;
    }


    function OnSubmitForm() {
        return validarCadastro();
    }

    function carregarComponenteHipoteseLegal() {

        objAutoCompletarHipLegal = new infraAjaxAutoCompletar('hdnIdHipoteseLgl', 'txtHipoteseLgl', '<?= isset($strLinkAjaxHipLegal) ? $strLinkAjaxHipLegal : '' ?>');
        objAutoCompletarHipLegal.limparCampo = true;
        objAutoCompletarHipLegal.tamanhoMinimo = 3;
        objAutoCompletarHipLegal.prepararExecucao = function () {
            return 'palavras_pesquisa=' + document.getElementById('txtHipoteseLgl').value;
        };

        objAutoCompletarHipLegal.processarResultado = function (id, nome, complemento) {

            if (id != '') {
                var options = document.getElementById('selDescricaoHpLegalNvAcesso').options;

                if (options != null) {
                    for (var i = 0; i < options.length; i++) {
                        if (options[i].value == id) {
                            alert('Hipótese Legal já consta na lista.');
                            break;
                        }
                    }
                }

                if (i == options.length) {

                    for (i = 0; i < options.length; i++) {
                        options[i].selected = false;
                    }

                    opt = infraSelectAdicionarOption(document.getElementById('selDescricaoHpLegalNvAcesso'), nome, id);

                    objLupaHipLegal.atualizar();

                    opt.selected = true;
                }

                document.getElementById('txtHipoteseLgl').value = '';
                document.getElementById('txtHipoteseLgl').focus();

            }
        };

        objLupaHipLegal = new infraLupaSelect('selDescricaoHpLegalNvAcesso', 'hdnHipoteseLgl', '<?= isset($strLinkHipoteseLglSelecao) ? $strLinkHipoteseLglSelecao : '' ?>');

    }

</script>