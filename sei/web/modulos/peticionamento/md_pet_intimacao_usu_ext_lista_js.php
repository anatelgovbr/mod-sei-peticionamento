<script type="text/javascript">
    function inicializar() {

        infraEfeitoTabelas();
        addEventoEnter();
    }

    function pesquisar() {
        var frmIntimacaoEletronicaLista = document.getElementById('frmIntimacaoEletronicaLista');
        var dataInicio = document.getElementById('txtDataInicio');
        var dataFim = document.getElementById('txtDataFim');

        if (!infraValidaData(dataInicio)) {
            return false;
        }

        if (!infraValidaData(dataFim)) {
            return false;
        }

        if (dataInicio.value.trim() != '' && dataFim.value.trim() == '') {
            dataFim.focus();
            alert('Informe o per�odo final!');
            return false;
        }

        if (dataInicio.value.trim() == '' && dataFim.value.trim() != '') {
            dataInicio.focus();
            alert('Informe o per�odo inicial!');
            return false;
        }

        if (dataInicio.value.trim() != '' && dataFim.value.trim() != '') {
            var dtInicio = dataInicio.value.split('/').reverse().join('/');
            dtInicio = new Date(dtInicio);

            var dtFim = dataFim.value.split('/').reverse().join('/');
            dtFim = new Date(dtFim);

            if (dtInicio.getTime() > dtFim.getTime()) {
                alert('Per�odo inicial maior que final!');
                dataInicio.focus();
                return false;
            }
        }

        frmIntimacaoEletronicaLista.submit();
    }

    function addEventoEnter(){
        var form = document.getElementById('frmIntimacaoEletronicaLista');
        document.addEventListener("keypress", function(evt){
            var key_code = evt.keyCode  ? evt.keyCode  :
                evt.charCode ? evt.charCode :
                    evt.which    ? evt.which    : void 0;

            if (key_code == 13)
            {
                pesquisar();
            }

        });
    }

    function fechar() {
        document.location = '<?= $strUrlFechar ?>';
    }

    function responder() {
        document.location = '<?= $strUrlResponder ?>';
    }
</script>
