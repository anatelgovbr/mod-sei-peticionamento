<?php  if(0) {?>
<script type="javascript">
<?php } ?>

function inicializar() {
controlarVisualizacao();
//   hideOrShowTable(isHide, true);
 //   funcaoTemporariaProgramacao();
    infraEfeitoTabelas();
    carregarComponenteTipoIntimacao();
    carregarComponenteUnidade();
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
    }

    if(isPesquisa){
        document.getElementById('divTabelaIntimacao').style.display = '';
        document.getElementById('divGraficos').style.display = 'none';
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

    //Valida��es de Data
    if (dataInicialPreenchida || dataFinalPreenchida){
        alert('O per�odo da gera��o est� incompleto.');
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
        alert('A Situa��o da Intima��o � de preenchimento obrigat�rio.');
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
    infraAbrirJanela(link,'janelaHistoricoIntimacao',700,400,'location=0,status=1,resizable=1,scrollbars=1');
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

    document.getElementById('divInfraAreaTela').style.height = '100%';
    setBotoesInferior('none');
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
                        alert('Unidade j� consta na lista.');
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
                        alert('Tipo de Intima��o j� consta na lista.');
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






<?php  if(0) {?>
</script>
<?php } ?>
    