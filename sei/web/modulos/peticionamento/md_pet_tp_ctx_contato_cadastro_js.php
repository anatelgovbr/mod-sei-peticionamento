<script type="text/javascript">

    var objLupaPrincipal = null;
    var objAutoCompletarPrincipal = null;

    var objLupaPrincipal2 = null;
    var objAutoCompletarPrincipal2 = null;

    function inicializar() {

        //=================== CAMPO 1 =====================
        objLupaPrincipal = new infraLupaSelect('selPrincipal', 'hdnPrincipal', '<?=$strLinkPrincipalSelecao?>');

        objAutoCompletarPrincipal = new infraAjaxAutoCompletar('hdnIdPrincipal', 'txtPrincipal', '<?=$strLinkAjaxPrincipal?>');
        objAutoCompletarPrincipal.limparCampo = true;
        objAutoCompletarPrincipal.tamanhoMinimo = 3;
        objAutoCompletarPrincipal.prepararExecucao = function () {
            return 'extensao=' + document.getElementById('txtPrincipal').value;
        };

        objAutoCompletarPrincipal.processarResultado = function (id, descricao, complemento) {
            if (id != '') {
                var options = document.getElementById('selPrincipal').options;

                for (var i = 0; i < options.length; i++) {
                    if (options[i].value == id) {
                        self.setTimeout('alert(\'Tipo de Interessado já consta na lista.\')', 100);
                        break;
                    }
                }

                if (i == options.length) {

                    for (i = 0; i < options.length; i++) {
                        options[i].selected = false;
                    }

                    opt = infraSelectAdicionarOption(document.getElementById('selPrincipal'), descricao, id);

                    objLupaPrincipal.atualizar();

                    opt.selected = true;
                }

                document.getElementById('txtPrincipal').value = '';
                document.getElementById('txtPrincipal').focus();
            }
        };

        //=================== CAMPO 2 =====================
        objLupaPrincipal2 = new infraLupaSelect('selPrincipal2', 'hdnPrincipal2', '<?=$strLinkPrincipalSelecao2?>');
        objAutoCompletarPrincipal2 = new infraAjaxAutoCompletar('hdnIdPrincipal2', 'txtPrincipal2', '<?=$strLinkAjaxPrincipal2?>');
        objAutoCompletarPrincipal2.limparCampo = true;
        objAutoCompletarPrincipal2.tamanhoMinimo = 3;
        objAutoCompletarPrincipal2.prepararExecucao = function () {
            return 'extensao=' + document.getElementById('txtPrincipal2').value;
        };

        objAutoCompletarPrincipal2.processarResultado = function (id, descricao, complemento) {
            if (id != '') {
                var options = document.getElementById('selPrincipal2').options;

                for (var i = 0; i < options.length; i++) {
                    if (options[i].value == id) {
                        self.setTimeout('alert(\'Tipo de Interessado já consta na lista.\')', 100);
                        break;
                    }
                }

                if (i == options.length) {

                    for (i = 0; i < options.length; i++) {
                        options[i].selected = false;
                    }

                    opt = infraSelectAdicionarOption(document.getElementById('selPrincipal2'), descricao, id);

                    objLupaPrincipal2.atualizar();

                    opt.selected = true;
                }

                document.getElementById('txtPrincipal2').value = '';
                document.getElementById('txtPrincipal2').focus();
            }
        };


        infraEfeitoTabelas();
    }

    function OnSubmitForm() {
        return validarCadastro();
    }

    function validarCadastro() {

        if (infraTrim(document.getElementById('hdnPrincipal').value) == '') {
            alert('Informe pelo menos um Tipo de Contato Permitido para Cadastro de Interessado.');
            return false;
        } else if (infraTrim(document.getElementById('hdnPrincipal2').value) == '') {
            alert('Informe pelo menos um Tipo de Contato Permitido para Seleção de Interessado.');
            return false;
        }

        return true;
    }
</script>