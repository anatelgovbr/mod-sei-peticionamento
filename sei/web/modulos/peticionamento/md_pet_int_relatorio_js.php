<script type="text/javascript">

function inicializar() {

    controlarVisualizacao();
    infraEfeitoTabelas();
    carregarComponenteTipoIntimacao();
    carregarComponenteUnidade();
    carregarComponenteDestinatarioSelect();

    //animate scroll quando é grafico
    var url_string = window.location.href;
    var url = new URL(url_string);
    var c = url.searchParams.get("grafico");
    if( c !== null ){
        var nivel = parseInt( $(".infraFieldset").offset().top );
        divInfraMoverTopo = document.getElementById("divInfraAreaTelaD");
        $( divInfraMoverTopo ).animate( { scrollTop: nivel - 100 } , 600 );
    }
}

function addControlePaginacao(){
    document.getElementById('selInfraPaginacaoSuperior').onchange = function(){
        document.getElementById('frmIntimacaoRelatorioLista').removeAttribute('target');
        document.getElementById('frmIntimacaoRelatorioLista').action = '<?= $strUrlPesquisar ?>';
        infraAcaoPaginar('=',this.value,'Infra');
    }
    document.getElementById('lnkInfraProximaPaginaSuperior').onchange = function(){
        infraAcaoPaginar('+',0,'Infra');
    }
}

function controlarVisualizacao(){
    var chamadaPosterior = document.getElementById('hdnAcaoOrigem').value == 'md_pet_int_relatorio_listar' ? true : false;
    var isGrafico        = document.getElementById('hdnIsGrafico').value != '0';
    var isPesquisa       = document.getElementById('hdnIsPesquisa').value != '0';

    if(isGrafico){
        document.getElementById('divTabelaIntimacao').style.display = 'none';
        document.getElementById('divGraficos').style.display = '';
        document.getElementById('espacamento').style.marginTop = '0px';
    }

    if(isPesquisa || (chamadaPosterior && !isGrafico)){
        document.getElementById('divTabelaIntimacao').style.display = 'block';
        document.getElementById('divGraficos').style.display = 'none';
        document.getElementById('espacamento').style.marginTop = '0px';
    }

    if(!chamadaPosterior && !isGrafico && !isPesquisa){
        document.getElementById('divGraficos').style.display = 'none';
        document.getElementById('divTabelaIntimacao').style.display = 'none';
    }

    setBotoesInferior('');
    document.getElementById('divInfraAreaTela').style.height = '100%';
}

function funcaoTemporariaProgramacao(){
    document.getElementById('divTabelaIntimacao').style.display = 'none';
    document.getElementById('divGraficos').style.display = '';
}

function hideOrShowTable(hideTabela, inicializacao){
    var addTabela = hideTabela ? 'none' : '';
    document.getElementById('divTabelaIntimacao').style.display = addTabela;

    if(inicializacao) {
        var addGrafico = 'none';
    }else{
        var addGrafico = hideTabela ? '' : 'none';
    }

    document.getElementById('divGraficos').style.display = addGrafico;
}

function pesquisar(){
    if(validarPesquisa()) {
        controlarVisualizacao();
        document.getElementById('frmIntimacaoRelatorioLista').removeAttribute('target');
        document.getElementById('frmIntimacaoRelatorioLista').action = '<?= $strUrlPesquisar ?>';
        document.getElementById('frmIntimacaoRelatorioLista').submit();
    }
}

function validarPesquisa(){
    var dataInicialPreenchida = (infraTrim(document.getElementById('txtDataInicio').value)!='') && (infraTrim(document.getElementById('txtDataFim').value)=='');
    var dataFinalPreenchida = (infraTrim(document.getElementById('txtDataInicio').value)=='') && (infraTrim(document.getElementById('txtDataFim').value)!='');

    //Validações de Data
    if (dataInicialPreenchida || dataFinalPreenchida){
        alert('O período da geração está incompleto.');
        document.getElementById('txtDataInicio').focus();
        return false;
    }

    if (infraTrim(document.getElementById('txtDataInicio').value)!='' && infraTrim(document.getElementById('txtDataFim').value)!='') {
        if (!infraValidarData(document.getElementById('txtDataInicio'))) {
            return false;
        }

        if (!infraValidarData(document.getElementById('txtDataFim'))) {
            return false;
        }

        if (infraCompararDatas(document.getElementById('txtDataInicio').value, document.getElementById('txtDataFim').value) < 0) {
            alert('Data Final deve ser igual ou superior a Data Inicial.');
            document.getElementById('txtDataInicio').focus();
            return false;
        }
    }

    var selSituacao = document.getElementById('selSituacao');

    if(selSituacao.options.selectedIndex == '-1'){
        alert('A Situação da Intimação é de preenchimento obrigatório.');
        return false;
    }

    preencherObjHdnSituacao();

    return true;
}

function preencherObjHdnSituacao(){
    var selSituacao = document.getElementById('selSituacao');
    var arrSituacao = new Array();

    for(i=0; i < selSituacao.options.length; i++){
        if(selSituacao.options[i].selected) {
            arrSituacao.push(selSituacao.options[i].value)
        }
    }

    if(arrSituacao.length > 0){
        var jsonRetorno = JSON.stringify(arrSituacao);
        document.getElementById('hdnIdsSituacao').value = jsonRetorno;
    }else{
        document.getElementById('hdnIdsSituacao').value = '';
    }

}

function abrirModalHistorico(link){
    infraAbrirJanelaModal(link,900,400);
}

function exportarExcel(){
    if(validarPesquisa()) {
        document.getElementById('divTabelaIntimacao').style.display = 'none';
        document.getElementById('divGraficos').style.display = 'none';
        setBotoesInferior('none');
        document.getElementById('divInfraAreaTela').style.height = '100%';
        var idHdnUrl = 'hdnExcel';
        var urlExcel = document.getElementById(idHdnUrl).value;
        document.getElementById('frmIntimacaoRelatorioLista').action = urlExcel;
        document.getElementById('frmIntimacaoRelatorioLista').target = '_blank';
        document.getElementById('frmIntimacaoRelatorioLista').submit();
    }
}

function gerarGrafico(){
    if(validarPesquisa()) {
        controlarVisualizacao();
        var idHdnUrl   = 'hdnTipoGrafico' + document.getElementById('selGrafico').value;
        var urlGrafico = document.getElementById(idHdnUrl).value;
        document.getElementById('frmIntimacaoRelatorioLista').removeAttribute('target');
        document.getElementById('frmIntimacaoRelatorioLista').action = urlGrafico;
        document.getElementById('frmIntimacaoRelatorioLista').submit();
    }
}

function limparCriterios(){
   if(document.getElementById('selDescricaoTpIntimacao').options.length > 0) {
       addSelectedComponentes('selDescricaoTpIntimacao');
       objLupaTpIntimacao.remover();
   }

    if(document.getElementById('selDescricaoUnidade').options.length > 0) {
        addSelectedComponentes('selDescricaoUnidade');
        objLupaUnidade.remover();
    }

    document.getElementById('txtDataInicio').value = '';
    document.getElementById('txtDataFim').value = '';

    limparSelecaoOptionsSituacao();

    document.getElementById('divTabelaIntimacao').style.display = 'none';
    document.getElementById('divGraficos').style.display = 'none';
    //document.getElementById('espacamento').style.marginTop = '200px';
    document.getElementById('divInfraAreaTela').style.height = '100%';

}

function setBotoesInferior(valorDisplay){
    var objBtnInferior = document.getElementById('divInfraBarraComandosInferior');
    if(objBtnInferior != null)
    {
        document.getElementById('divInfraBarraComandosInferior').style.display = valorDisplay;
    }
}

function limparSelecaoOptionsSituacao(){
        var objSituacao = document.getElementById('selSituacao');
        var valueTodas = document.getElementById('hdnIdSitTodas').value;

        for (i = 0; i < objSituacao.options.length; i++) {
            objSituacao.options[i].selected = false;

            if (objSituacao.options[i].value == valueTodas) {
                objSituacao.options[i].selected = true;
            }
    }
}

function addSelectedComponentes(id){
    for (i =0; i <  document.getElementById(id).options.length; i++) {
        document.getElementById(id).options[i].selected = true;
    }
}

function fechar(){
    location.href="<?= $strUrlFechar ?>";
}

function carregarComponenteUnidade(){
    objAutoCompletarUnidade = new infraAjaxAutoCompletar('hdnIdUnidade', 'txtUnidade', '<?=$strLinkAjaxUnidade?>');
    objAutoCompletarUnidade.limparCampo = true;
    objAutoCompletarUnidade.tamanhoMinimo = 3;
    objAutoCompletarUnidade.prepararExecucao = function(){
        return 'palavras_pesquisa='+document.getElementById('txtUnidade').value;
    };

    objAutoCompletarUnidade.processarResultado = function(id,nome,complemento){

        if (id!=''){
            var options = document.getElementById('selDescricaoUnidade').options;

            if(options != null){
                for(var i=0;i < options.length;i++){
                    if (options[i].value == id){
                        alert('Unidade já consta na lista.');
                        break;
                    }
                }
            }

            if (i==options.length){

                for(i=0;i < options.length;i++){
                    options[i].selected = false;
                }

                opt = infraSelectAdicionarOption(document.getElementById('selDescricaoUnidade'),nome,id);

                objLupaUnidade.atualizar();

                opt.selected = true;
            }

            document.getElementById('txtUnidade').value = '';
            document.getElementById('txtUnidade').focus();

        }
    };

    objLupaUnidade = new infraLupaSelect('selDescricaoUnidade' , 'hdnUnidade',  '<?=$strLinkUnidSelecionar ?>');
}

function carregarComponenteTipoIntimacao(){

    objAutoCompletarTpIntimacao = new infraAjaxAutoCompletar('hdnIdTpIntimacao', 'txtTpIntimacao', '<?=$strLinkAjaxTpIntimacao?>');
    objAutoCompletarTpIntimacao.limparCampo = true;
    objAutoCompletarTpIntimacao.tamanhoMinimo = 3;
    objAutoCompletarTpIntimacao.prepararExecucao = function(){
        return 'palavras_pesquisa='+document.getElementById('txtTpIntimacao').value;
    };

    objAutoCompletarTpIntimacao.processarResultado = function(id,nome,complemento){

        if (id!=''){
            var options = document.getElementById('selDescricaoTpIntimacao').options;

            if(options != null){
                for(var i=0;i < options.length;i++){
                    if (options[i].value == id){
                        alert('Tipo de Intimação já consta na lista.');
                        break;
                    }
                }
            }

            if (i==options.length){

                for(i=0;i < options.length;i++){
                    options[i].selected = false;
                }

                opt = infraSelectAdicionarOption(document.getElementById('selDescricaoTpIntimacao'),nome,id);

                objLupaTpIntimacao.atualizar();

                opt.selected = true;
            }

            document.getElementById('txtTpIntimacao').value = '';
            document.getElementById('txtTpIntimacao').focus();

        }
    };

    objLupaTpIntimacao = new infraLupaSelect('selDescricaoTpIntimacao' , 'hdnTpIntimacao',  '<?=$strLinkTpIntSelecionar ?>');

}

function carregarComponenteDestinatarioSelect(){

    $(document).ready(function(){

        url = $('select[name="selTipoDest"]').val() == 'N' ? '<?=$urlDestinatarioTipoFisica?>' : '<?=$urlDestinatarioTipoJuridica?>';

        $.post(url, {}, function(response){
            $('.destinatarios').html(response);
            $('.destinatarios').css('opacity', '1');
        });
        
        $('select[name="selDestinatarioPF"], select[name="selDestinatarioPJ"]').find('option').remove();
        $('input[name="hdnDestinatario"], input[name="hdnIdDestinatario"]').val('');

    });

}

$('body').on('change', 'select[name="selTipoDest"]', function(){
    $('.destinatarios').css('opacity', '.3');
    carregarComponenteDestinatarioSelect();
});

</script>