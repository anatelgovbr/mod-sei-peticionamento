<script type="text/javascript">
    // "use strict";
    function inicializar() {
    }

    function mascararCampoCnpjCpf(campo){
        var valor = $('#txtCnpj').val();
        var valor1 = valor.replace('.', '');
        var valor2 = valor1.replace('.', '');
        var valor3 = valor2.replace('.', '');
        var valor4 = valor3.replace('-', '');
        var valor5 = valor4.replace('/', '');
        var tamanho = valor5.length;
        if(tamanho == 10){
            campo.value = valor5.substring(0,3) + "." + valor5.substring(3,6) +  "." + valor5.substring(6,9) + "-" + valor5.substring(9,11);
        } else if(tamanho >= 13) {
            campo.value = valor5.substring(0,2) + "." + valor5.substring(2,5) +  "." + valor5.substring(5,8) + "/" + valor5.substring(8,12) + "-" + valor5.substring(12,14);
        }
    }

    //document.getElementById('divInfraAreaDados').style.overflow = "hidden";
    
    function suspenderRestabelecerPE(url) {
        infraAbrirJanela(url, 'suspenderRestabelecerVincPJ', 800, 500, '', false); //modal
        return;
    }

    function mostrarExcessao(){
        alert('Para executar essa ação é necessário estar na Unidade parametrizada para abertura de processo de centralização da documentação da Pessoa Jurídica indicada no menu Administração > Peticionamento Eletrônico > Parâmetros para Vinculação a Usuário Externo.')
    }

    $("select.infraSelect.multipleSelect").multipleSelect({
        filter: true,
        minimumCountSelected: 1,
        selectAll: true
    }).parent('button').hide();


    if ($("#slTipoViculo").val() == 'S') {
        $("#divTipoPoder").show();
    } else {
        $("#divTipoPoder").hide();
    }

    $("#slTipoViculo").change(function() {
        if ($(this).val() == 'S') {
            $("#divTipoPoder").show();
        } else {
            $("#divTipoPoder").hide();
        }
    });

</script>