<script type="text/javascript" charset="iso-8859-1">
    function rdTipo() {

        var externo = document.getElementById("tipoExterno").checked;
        var html = document.getElementById("tipoHTML").checked;

        if (externo) {

//divInfraAreaDados

            document.getElementById("divTxtUrl").style.display = 'block';
            document.getElementById("divTbConteudo").style.display = 'none';

        } else if (html) {
            document.getElementById("divTxtUrl").style.display = 'none';
            document.getElementById("divTbConteudo").style.display = 'block';
//limpa campo
            $("#tbConteudo  iframe").contents().find("body").html('')
        }

    }

    function inicializar() {

        infraEfeitoTabelas();

        <? if( isset($tipo) && in_array($tipo, ['E', 'H']) ){ ?>
        rdTipo();
        <? } ?>

    }

    function OnSubmitForm() {
        var txtNome = document.getElementById('txtNome').value;
        var txtUrl = document.getElementById('txtUrl').value;
        var tipo = $("[name=tipo]input:checked").attr('value');
        var txtHTML = document.getElementById('txaConteudo').value;

//nome e tipo sao obrigatorios
        if (txtNome == '') {
            alert('Informe o Nome do Menu.');
            document.getElementById('txtNome').focus();
            return false;

        } else if (txtNome.length > 30) {
            alert('Tamanho do campo excedido (máximo 30 caracteres).');
            document.getElementById('txtNome').focus();
            return false;
        } else if (tipo == '') {
            alert('Informe o Tipo de Menu.');
            return false;
        } else if (tipo == 'E') {
            if (txtUrl == '') {
                alert('Informe a URL do Link Externo.');
                document.getElementById('txtUrl').focus();
                return false;

            } else if (txtUrl.length > 2083) {
                alert('Tamanho do campo excedido (máximo 2083 caracteres).');
                document.getElementById('txtUrl').focus();
                return false;
            }
        } else if (tipo == 'H') {
            if (
                $("#containerEditor  iframe").contents().find("body").html() == '<br>'
                || $("#containerEditor  iframe").contents().find("body").html() == ''
            ) {
                alert('Informe o Conteúdo HTML.');
                return false;
            }
        }
        return true;
    }
</script>