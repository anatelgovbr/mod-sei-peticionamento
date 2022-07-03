<script type="text/javascript">
    function desvincularProcuracao(link, idTd) {
        $('#tr-' + idTd).css('backgroundColor', '#efff00');
        infraAbrirJanela(link, 'janelaDesvinculo', 700, 220, '', true);
        return;
    }

    function infraMonitorarModal() {
        if (infraJanelaModal.closed) {
            infraFecharJanelaModal();
            $('.infraTrClara').css('backgroundColor', '#FFFFFF');
        }
    }

    if($(location).attr('hash').length > 0){
        $('tbody tr'+$(location).attr('hash')).addClass('infraTrAcessada');
    }

    function inicializar() {
//Escondendo o Campo data
        if (document.getElementById('hdnDtInicio').value != "" && document.getElementById('hdnDtFim').value != "") {
            document.getElementById('dtInicio').style.display = "";
            document.getElementById('dtFim').style.display = "";
            document.getElementById('txtPeriodoInicio').value = document.getElementById('hdnDtInicio').value;
            document.getElementById('txtPeriodoFim').value = document.getElementById('hdnDtFim').value;
        } else {
            document.getElementById('dtInicio').style.display = "none";
            document.getElementById('dtFim').style.display = "none";
        }


        infraEfeitoTabelas();
    }

    function controlarCpfCnpj(objeto) {
        var valor = $.trim(objeto.value.replace(/\D/g, ""));
        if (valor.length <= 11) {
            var novoValor = maskCPF($.trim(objeto.value));
            objeto.value = novoValor;
        } else {
            var novoValor = maskCNPJ(valor);
            objeto.value = novoValor;
        }
    }

    function validaCpfCnpjOutorgante(objeto) {
        var erro = false;
        var valor = $.trim(objeto.value.replace(/\D/g, ""));
        
        if(valor.length > 0){
            switch (valor.length) {
                case 11: if (!infraValidarCpf(valor)) { erro = true; } break;
                case 14: if (!infraValidarCnpj(valor)) { erro = true; } break;
                default: erro = true; break;
            }
        }

        if (erro) {
            alert('Informe um CPF/CNPJ completo ou válido para realizar a pesquisa.');
            document.getElementById('txtCnpj').value = '';
        }
    }

    function infraMascaraCPFProcurador(objeto) {
        var novoValor = maskCPF($.trim(objeto.value));
        objeto.value = novoValor;
    }

    function validaCpfProcurador(objeto) {
        var erro = false;
        var valor = $.trim(objeto.value.replace(/\D/g, ""));

        if (valor.length > 0 || valor.length == 11) {
            if (!infraValidarCpf(valor)) {
                erro = true;
            }
        }

        if (erro) {
            alert('Informe o CPF do outorgado completo ou válido para realizar a pesquisa.');
            document.getElementById('txtCpf').value = '';
        }
    }

    function maskCPF(cpf) {
        cpf = cpf.replace(/\D/g, "");
        cpf = cpf.replace(/(\d{3})(\d)/, "$1.$2");
        cpf = cpf.replace(/(\d{3})(\d)/, "$1.$2");
        cpf = cpf.replace(/(\d{3})(\d{1,2})$/, "$1-$2");

        return cpf;
    }

    function showData(val) {

        if (val.value == "D") {

            document.getElementById('dtInicio').style.display = "";
            document.getElementById('dtFim').style.display = "";
        } else {
            document.getElementById('dtInicio').style.display = "none";
            document.getElementById('dtFim').style.display = "none";
            document.getElementById('txtPeriodoInicio').value = "";
            document.getElementById('txtPeriodoFim').value = "";

        }

    }

    function maskCNPJ(cnpj) {
        cnpj = cnpj.replace(/\D/g, ""); //Remove tudo o que não é dígito
        cnpj = cnpj.replace(/^(\d{2})(\d)/, "$1.$2"); //Coloca ponto entre o segundo e o terceiro dígitos
        cnpj = cnpj.replace(/^(\d{2})\.(\d{3})(\d)/, "$1.$2.$3"); //Coloca ponto entre o quinto e o sexto dígitos
        cnpj = cnpj.replace(/\.(\d{3})(\d)/, ".$1/$2"); //Coloca uma barra entre o oitavo e o nono dígitos
        cnpj = cnpj.replace(/(\d{4})(\d)/, "$1-$2"); //Coloca um hífen depois do bloco de quatro dígitos

        return cnpj;
    }

    function validarCampoData() {
        if (document.getElementById('sllblValidade').value == "D") {
            if (document.getElementById('txtPeriodoInicio').value == "" || document.getElementById('txtPeriodoFim').value == "") {
                alert("Preencha os campos de Período.");

//Voltando para vazio

                var val = "";
                var sel = document.getElementById('sllblValidade');
                var opts = sel.options;
                for (var opt, j = 0; opt = opts[j]; j++) {
                    if (opt.value == val) {
                        sel.selectedIndex = j;
                        break;
                    }
                }

            }
        } else {
            document.getElementById('hdnDtInicio').value = "";
            document.getElementById('hdnDtFim').value = "";
            document.getElementById('txtPeriodoInicio').value = "";
            document.getElementById('txtPeriodoFim').value = "";
            document.getElementById('dtInicio').style.display = "none";
            document.getElementById('dtFim').style.display = "none";


        }
    }
</script>