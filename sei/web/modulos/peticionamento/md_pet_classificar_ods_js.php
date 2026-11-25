<script type="text/javascript">
    function inicializar() {
        atualizarObjetivosAtivos();
    }

    function atualizarListaObjetivos(obj){
        var divsObjetivos = document.querySelectorAll("#todos-objetivos > div");
        if (obj.checked) {
            var arrIds = document.getElementById("arr-objetivos-forte-relacao").value.split(',');
            divsObjetivos.forEach(function(div) {
                if(!arrIds.includes(div.id)){
                    div.style.display = "none";
                }
            });
        } else {
            divsObjetivos.forEach(function(div) {
                div.style.display = "";
            });
        }
    }
    
    function atualizarListaMetas(obj){
        if(obj.checked){
            $('tr.item_meta_fraca').hide();
        }else{
            $('tr.item_meta_fraca').show();
        }
    }

    function fecharModal() {
        $(window.top.document).find('div[id^=divInfraSparklingModalClose]').click();
    }

    function exibirObjetivos(){
        document.getElementById('step-1').classList.remove('d-none');
        document.getElementById('step-2').classList.add('d-none');
        document.querySelectorAll('.form-stepper-list').forEach(item => {
            item.classList.remove('form-stepper-active');
        });
        document.querySelector(`.form-stepper-list[step="1"]`).classList.add('form-stepper-active');
    }

    function mudarStep(step){

        $('#btnNovaClassificacao, #btnProsseguir, #btnSalvar').css('display', 'none');
        document.getElementById('step-1').classList.add('d-none');
        document.getElementById('step-2').classList.add('d-none');
        document.getElementById('step-3').classList.add('d-none');

        document.querySelectorAll('.form-stepper-list').forEach(item => {
            item.classList.remove('form-stepper-active');
            item.classList.add('form-stepper-unfinished');
        });

        document.getElementById(`step-${step}`).classList.remove('d-none');
        document.querySelector(`.form-stepper-list[step="${step}"]`).classList.add('form-stepper-active');

        switch (step) {

            case 1:
                atualizarObjetivosAtivos();
                break;
            case 2:
                $('#btnNovaClassificacao, #btnProsseguir').css('display', 'inline-block');
                break;
            case 3:
                $('#btnNovaClassificacao, #btnSalvar').css('display', 'inline-block');
                criarListaMetasSelecionadas();
                break;
        }
    }

    function exibirMetas(idObjetivo){

        mudarStep(2);

        //consultar as metas para exibir na tela
        var url = '<?= SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=md_pet_ods_consultar_metas_ods_ajax'); ?>';
        var params = {};
        params["idObjetivo"] = idObjetivo;
        params["SinForteRelacao"] = document.getElementById("btn-checkbox").checked;
        params["MetasMarcadas"] = document.getElementById("hdnInfraItensSelecionados").value;

        $.ajax({
           url: url,
           type: 'POST', //selecionando o tipo de requisição, PUT,GET,POST,DELETE
           dataType: "json",//Tipo de dado que será enviado ao servidor
           data: params, // Enviando o JSON com o nome de itens
           async: false,
           success: function (data) {
               $('#step-2 #metas-selecionar').html(data);
               infraCriarCheckboxRadio("infraCheckbox","infraCheckboxDiv","infraCheckboxLabel","infraCheckbox","infraCheckboxInput");
           },
           error: function (err) {
               callback("Ocorreu um erro ao consultar as metas.");
           }
        });
    }

    function infraCriarCheckboxRadio(classInput,classDiv,classLabel,classRemocao,classAdicao){
        $("."+classInput).each(function (index) {
            var div = $('<div class="'+classDiv+'" ></div>');
            var isVisible = this.style.visibility !== 'hidden' && this.style.display !== 'none';
            if (isVisible) {
                $(this).wrap(div);
            }else{
                $(this).addClass("infraCheckboxRadioSemDiv");
            }

            var id = $(this).attr("id");
            var title = $(this).attr("title");

            var label = $('<label class="'+classLabel+'"></label>');

            if(id != undefined){
                $(label).attr("for",id);
            }
            if(title != undefined){
                $(label).attr("title",title);
            }

            $(this).removeClass(classRemocao);
            $(this).addClass(classAdicao);

            label.insertAfter($(this));
        });
    }

    $('body').off('click').on('click', '.metaItemLista', function(){

        if($(this).hasClass('lineOpen')){
            $(this).find('span.points').show();
            $(this).find('span.more').hide();
            $(this).find('img').attr('src', 'modulos/ia/imagens/sei_seta_direita.png');
            $(this).removeClass('lineOpen');
        }else{
            $('.metaItemLista span.points').show();
            $('.metaItemLista span.more').hide();
            $('.metaItemLista img').attr('src', 'modulos/ia/imagens/sei_seta_direita.png');
            $('.metaItemLista').removeClass('lineOpen');

            $(this).find('span.points').hide();
            $(this).find('span.more').show();
            $(this).find('img').attr('src', 'modulos/ia/imagens/sei_seta_abaixo.png');
            $(this).addClass('lineOpen');
        }

    });

    function criarListaMetasSelecionadas(){

        $.ajax({
            url: '<?= SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=md_pet_ods_consultar_metas_selecionadas_sessao_ajax'); ?>',
            type: 'POST',
            dataType: 'html',
            async: false,
            beforeSend: function(){
                $('#metas-selecionadas').html('Atualziando metas selecionadas...');
            },
            success: function (lista) {
                $('#metas-selecionadas').html(lista);
            },
            error: function (err) {
                $('#metas-selecionadas').html("Ocorreu um erro ao consultar as metas.");
                console.log(err);
            }
        });

    }

    function atualizarObjetivosAtivos() {

        var url = '<?= SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=md_pet_ods_consultar_objetivos_selecionados_ajax'); ?>';
        var params = {};

        params['itensSelecionados'] = document.getElementById("hdnInfraItensSelecionados").value;

        $.ajax({
            url: url,
            type: 'POST',
            dataType: "json",
            data: params,
            async: false,
            success: function (data) {
                var div = document.getElementById('todos-objetivos');
                var elementos = div.querySelectorAll('.col-2');
                elementos.forEach(function(elemento) {
                    var id = elemento.id;
                    var imagem = elemento.querySelector('img');
                    imagem.classList.add('img-desfoque');
                    if(data.indexOf(parseInt(id)) !== -1){
                        var imagem = elemento.querySelector('img');
                        imagem.classList.remove('img-desfoque');
                    }
                });
            },
            error: function (err) {
                callback("Ocorreu um erro ao consultar Objetivos.");
            }
        });
    }

    function salvarMetasSessao() {

        var url = '<?= SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=md_pet_ods_salvar_metas_selecionadas_sessao_ajax'); ?>';
        var params = {};

        params['itensSelecionados'] = document.getElementById("hdnInfraItensSelecionados").value;

        $.ajax({
            url: url,
            type: 'POST',
            dataType: "json",
            data: params,
            async: true,
            error: function (err) {
                callback("Ocorreu um erro ao salvar meta.");
            }
        });
    }

</script>