<script type="application/javascript">

    function inicializar() {
        infraEfeitoTabelas();
    }

    function onSubmitForm() {
        if (infraTrim(CKEDITOR.instances['txaConteudo'].getData()) == '') {
            alert('Informe o Conteúdo.');
            document.getElementById('txaConteudo').focus();
            return false;
        }
    }
</script>
