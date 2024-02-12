<script type="text/javascript">

    $(document).ready(function() {
        $('tr.tr-acoes-dinamicas').each(function() {

            var dadosColuna = $(this).find('.td-acoes-dinamicas');
            var dataAttributes = $(this).data();

            $.ajax({
                type: 'POST',
                url: '<?= $strUrlAcaoLinhas ?>',
                data: { dataAttributes: dataAttributes },
                success: function(result) {
                    var strResultado = $(result).find('actions').text();

                    // Substituindo as entidades HTML pelos caracteres originais
                    strResultado = strResultado.replace(/&amp;/g, '&');
                    strResultado = strResultado.replace(/&lt;/g, '<');
                    strResultado = strResultado.replace(/&gt;/g, '>');
                    strResultado = strResultado.replace(/&quot;/g, '"');

                    dadosColuna.html(strResultado);
                },
                error: function(xhr, status, error) {
                    console.log('Error:', xhr, status, error);
                }
            });

        });
    });

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
            alert('Informe o período final!');
            return false;
        }

        if (dataInicio.value.trim() == '' && dataFim.value.trim() != '') {
            dataInicio.focus();
            alert('Informe o período inicial!');
            return false;
        }

        if (dataInicio.value.trim() != '' && dataFim.value.trim() != '') {
            var dtInicio = dataInicio.value.split('/').reverse().join('/');
            dtInicio = new Date(dtInicio);

            var dtFim = dataFim.value.split('/').reverse().join('/');
            dtFim = new Date(dtFim);

            if (dtInicio.getTime() > dtFim.getTime()) {
                alert('Período inicial maior que final!');
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
