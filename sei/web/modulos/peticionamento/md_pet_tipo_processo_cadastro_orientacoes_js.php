<script type="application/javascript">

    function inicializar() {
        infraEfeitoTabelas();
    }

    function onSubmitForm() {
        if (infraTrim(CKEDITOR.instances['txaConteudo'].getData()) == '') {
            alert('Informe o Conte�do.');
            document.getElementById('txaConteudo').focus();
            return false;
        }
    }
</script>
