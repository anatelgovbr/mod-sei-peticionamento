<script type="text/javascript">

    function validarCampo(obj, event, tamanho){
        if(!somenteNumeros(event)){
            return somenteNumeros(event)
        }else{
            return infraMascaraTexto(obj, event, tamanho);
        }

    }

    function inicializar(){

        if ('<?=$_GET['acao']?>'=='md_pet_tamanho_arquivo_cadastrar'){
            addEventoEnter();
            document.getElementById('txtValorDocPrincipal').focus();
        }else{
            document.getElementById('btnFechar').focus();
        }
        infraEfeitoTabelas();
    }

    function addEventoEnter(){
        var form = document.getElementById('frmCadastroTamanhoArquivo');
        document.addEventListener("keypress", function(evt){
            var key_code = evt.keyCode  ? evt.keyCode  :
                evt.charCode ? evt.charCode :
                    evt.which    ? evt.which    : void 0;


            if (key_code == 13)
            {
                $('#sbmCadastrarTamanhoArquivo').click();
            }

        });
    }

    function validarCadastro() {

        if (infraTrim(document.getElementById('txtValorDocPrincipal').value)=='') {
            alert('Informe o Valor para Documento Principal.');
            document.getElementById('txtValorDocPrincipal').focus();
            return false;
        }

        if (infraTrim(document.getElementById('txtValorDocComplementar').value)=='') {
            alert('Informe o Valor para Documento Complementar.');
            document.getElementById('txtValorDocComplementar').focus();
            return false;
        }

        return true;
    }

    function OnSubmitForm() {
        return validarCadastro();
    }

    function somenteNumeros(e){
        var tecla=(window.event)?event.keyCode:e.which;
        if((tecla>47 && tecla<58))
            return true;
        else{
            if (tecla==8 || tecla==0)
                return true;
            else  return false;
        }
    }

</script>