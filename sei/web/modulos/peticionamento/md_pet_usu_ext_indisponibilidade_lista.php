<?
/**
 * ANATEL
 *
 * 21/06/2016 - criado por marcelo.bezerra@cast.com.br - CAST
 *
 */

try {

    require_once dirname(__FILE__) . '/../../SEI.php';

    session_start();

    //////////////////////////////////////////////////////////////////////////////
    //InfraDebug::getInstance()->setBolLigado(false);
    //InfraDebug::getInstance()->setBolDebugInfra(false);
    //InfraDebug::getInstance()->limpar();
    //////////////////////////////////////////////////////////////////////////////

    PaginaPeticionamentoExterna::getInstance()->setTipoPagina(PaginaPeticionamentoExterna::$TIPO_PAGINA_SEM_MENU);
    PaginaPeticionamentoExterna::getInstance()->getBolAutoRedimensionar();

    switch ($_GET['acao_externa']) {


        case 'md_pet_usu_ext_indisponibilidade_listar':

            $strTitulo = 'Indisponibilidades do Sistema';
            break;

        default:
            throw new InfraException("Ação '" . $_GET['acao'] . "' não reconhecida.");
    }

    $arrComandos = array();

    $objMdPetIndisponibilidadeDTO = new MdPetIndisponibilidadeDTO();
    $objMdPetIndisponibilidadeDTO->retTodos();

    if ($_POST['txtDtInicio']) {
        $objMdPetIndisponibilidadeDTO->setDthDataInicio($_POST['txtDtInicio'] . ':00', InfraDTO::$OPER_MAIOR_IGUAL);
    }

    if ($_POST['txtDtFim']) {
        $objMdPetIndisponibilidadeDTO->setDthDataFim($_POST['txtDtFim'] . ':00', InfraDTO::$OPER_MENOR_IGUAL);
    }

    if ($_POST['selSinProrrogacao'] && $_POST['selSinProrrogacao'] != 'null') {
        $objMdPetIndisponibilidadeDTO->setStrSinProrrogacao($_POST['selSinProrrogacao']);
    }

    PaginaPeticionamentoExterna::getInstance()->prepararOrdenacao($objMdPetIndisponibilidadeDTO, 'DataInicio', InfraDTO::$TIPO_ORDENACAO_DESC);
    PaginaPeticionamentoExterna::getInstance()->prepararPaginacao($objMdPetIndisponibilidadeDTO, 200);

    $objMdPetIndisponibilidadeRN = new MdPetIndisponibilidadeRN();
    $arrObjMdPetIndisponibilidadeDTO = $objMdPetIndisponibilidadeRN->listar($objMdPetIndisponibilidadeDTO);

    PaginaPeticionamentoExterna::getInstance()->processarPaginacao($objMdPetIndisponibilidadeDTO);
    $numRegistros = count($arrObjMdPetIndisponibilidadeDTO);

    $strLinkPesquisar = PaginaPeticionamentoExterna::getInstance()->formatarXHTML(SessaoSEIExterna::getInstance()->assinarLink('md_pet_usu_ext_indisponibilidade_lista.php?acao_externa=md_pet_usu_ext_indisponibilidade_listar&id_orgao_acesso_externo=0'));
    $arrComandos[] = '<button type="button" accesskey="P" id="btnPesquisar" value="Pesquisar" onclick="pesquisar();" class="infraButton"><span class="infraTeclaAtalho">P</span>esquisar</button>';
    $arrComandos[] = '<button type="button" accesskey="I" id="btnImprimir" value="Fechar" onclick="infraImprimirTabela();" class="infraButton"><span class="infraTeclaAtalho">I</span>mprimir</button>';

    if ($numRegistros > 0) {

        $bolCheck = true;
        $bolAcaoConsultar = SessaoSEIExterna::getInstance()->verificarPermissao('md_pet_usu_ext_indisponibilidade_consultar');
        $bolAcaoImprimir = true;

        $strResultado = '';
        $strSumarioTabela = 'Tabela de Indisponibilidades.';
        $strCaptionTabela = 'Indisponibilidades';

        $strResultado .= '<table width="100%" id="tbIndisponibilidade" class="infraTable" summary="' . $strSumarioTabela . '">' . "\n";
        $strResultado .= '<caption class="infraCaption">' . PaginaPeticionamentoExterna::getInstance()->gerarCaptionTabela($strCaptionTabela, $numRegistros) . '</caption>';
        $strResultado .= '<tr>';

        if ($bolCheck) {
            $strResultado .= '<th class="infraTh" width="1%">' . PaginaPeticionamentoExterna::getInstance()->getThCheck() . '</th>' . "\n";
        }

        $strResultado .= '<th class="infraTh" width="30%">' . PaginaPeticionamentoExterna::getInstance()->getThOrdenacao($objMdPetIndisponibilidadeDTO, 'Início', 'DataInicio', $arrObjMdPetIndisponibilidadeDTO) . '</th>' . "\n";
        $strResultado .= '<th class="infraTh">' . PaginaPeticionamentoExterna::getInstance()->getThOrdenacao($objMdPetIndisponibilidadeDTO, 'Fim', 'DataFim', $arrObjMdPetIndisponibilidadeDTO) . '</th>' . "\n";
        $strResultado .= '<th class="infraTh">' . PaginaPeticionamentoExterna::getInstance()->getThOrdenacao($objMdPetIndisponibilidadeDTO, 'Prorrogação Automática dos Prazos', 'SinProrrogacao', $arrObjMdPetIndisponibilidadeDTO) . '</th>' . "\n";
        $strResultado .= '<th class="infraTh" width="15%">Ações</th>' . "\n";
        $strResultado .= '</tr>' . "\n";
        $strCssTr = '';

        for ($i = 0; $i < $numRegistros; $i++) {

            if ($_GET['id_indisponibilidade_peticionamento'] == $arrObjMdPetIndisponibilidadeDTO[$i]->getNumIdIndisponibilidade()) {
                $strCssTr = '<tr class="infraTrAcessada">';
            } else {
                if ($arrObjMdPetIndisponibilidadeDTO[$i]->getStrSinAtivo() == 'S') {
                    $strCssTr = ($strCssTr == '<tr class="infraTrClara">') ? '<tr class="infraTrEscura">' : '<tr class="infraTrClara">';
                } else {
                    $strCssTr = '<tr class="trVermelha">';
                }
            }

            $strResultado .= $strCssTr;

            if ($bolCheck) {
                $strResultado .= '<td valign="top" style="vertical-align: middle;">' . PaginaPeticionamentoExterna::getInstance()->getTrCheck($i, $arrObjMdPetIndisponibilidadeDTO[$i]->getNumIdIndisponibilidade(), $arrObjMdPetIndisponibilidadeDTO[$i]->getStrSinProrrogacao()) . '</td>';
            }

            $dataInicio = isset($arrObjMdPetIndisponibilidadeDTO[$i]) && $arrObjMdPetIndisponibilidadeDTO[$i]->getDthDataInicio() != '' ? str_replace(' ', ' - ', substr($arrObjMdPetIndisponibilidadeDTO[$i]->getDthDataInicio(), 0, -3)) : '';
            $dataFim = isset($arrObjMdPetIndisponibilidadeDTO[$i]) && $arrObjMdPetIndisponibilidadeDTO[$i]->getDthDataFim() != '' ? str_replace(' ', ' - ', substr($arrObjMdPetIndisponibilidadeDTO[$i]->getDthDataFim(), 0, -3)) : '';

            $sinProrrogacao = $arrObjMdPetIndisponibilidadeDTO[$i]->getStrSinProrrogacao() === 'S' ? 'Sim' : 'Não';

            $strResultado .= '<td>' . $dataInicio . '</td>';
            $strResultado .= '<td>' . $dataFim . '</td>';
            $strResultado .= '<td>' . $sinProrrogacao . '</td>';
            $strResultado .= '<td align="center">';

            if ($bolAcaoConsultar) {
                $urlBase = ConfiguracaoSEI::getInstance()->getValor('SEI', 'URL');
                $strResultado .= '<a href="' . $urlBase . '/modulos/peticionamento/md_pet_usu_ext_indisponibilidade_cadastro.php?id_orgao_acesso_externo=0&acao_externa=md_pet_usu_ext_indisponibilidade_consultar&id_indisponibilidade_peticionamento=' . $arrObjMdPetIndisponibilidadeDTO[$i]->getNumIdIndisponibilidade() . '" tabindex="' . PaginaPeticionamentoExterna::getInstance()->getProxTabTabela() . '"><img src="/infra_css/svg/consultar.svg?'.Icone::VERSAO.'" title="Consultar Indisponibilidade" alt="Consultar Indisponibilidade" class="infraImg" /></a>&nbsp;';
            }

            $strResultado .= '</td></tr>' . "\n";

        }

        $strResultado .= '</table>';

    }

    $strDtInicio = '';
    $strDtFim = '';

    if (isset($_POST['txtDtInicio'])) {
        $strDtInicio = $_POST['txtDtInicio'];
    }

    if (isset($_POST['txtDtFim'])) {
        $strDtFim = $_POST['txtDtFim'];
    }

    $valorComboProrrogacao = '';

    if (isset($_POST['selSinProrrogacao'])) {
        $valorComboProrrogacao = $_POST['selSinProrrogacao'];
    }

    $strItensSelSinProrrogacaoAutomatica = MdPetIndisponibilidadeINT::montarSelectProrrogacaoAutomaticaPrazos('', 'Todos', $valorComboProrrogacao);

} catch (Exception $e) {
    PaginaPeticionamentoExterna::getInstance()->processarExcecao($e);
}

PaginaPeticionamentoExterna::getInstance()->montarDocType();
PaginaPeticionamentoExterna::getInstance()->abrirHtml();
PaginaPeticionamentoExterna::getInstance()->abrirHead();
PaginaPeticionamentoExterna::getInstance()->montarMeta();
PaginaPeticionamentoExterna::getInstance()->montarTitle(':: ' . PaginaPeticionamentoExterna::getInstance()->getStrNomeSistema() . ' - ' . $strTitulo . ' ::');
PaginaPeticionamentoExterna::getInstance()->montarStyle();
PaginaPeticionamentoExterna::getInstance()->abrirStyle();
?>
<?
PaginaPeticionamentoExterna::getInstance()->fecharStyle();
PaginaPeticionamentoExterna::getInstance()->montarJavaScript();
PaginaPeticionamentoExterna::getInstance()->abrirJavaScript();
?>
    function inicializar(){
    if ('<?= $_GET['acao'] ?>'=='md_pet_usu_ext_indisponibilidade_listar'){
    infraReceberSelecao();
    document.getElementById('btnFecharSelecao').focus();
    }
    }

    function validDate(valor) {

    var campo = (valor === 'I') ?  document.getElementById('txtDtInicio') : campo = document.getElementById('txtDtFim');
    var tamanhoCampo = parseInt((campo.value).length);

    if(tamanhoCampo < 16 || tamanhoCampo === 18){
    campo.focus();
    campo.value = "";
    alert('Data/Hora Inválida');
    return false;
    }

    var datetime = (campo.value).split(" ");
    var date = datetime[0];

    var ardt=new Array;
    var ExpReg=new RegExp("(0[1-9]|[12][0-9]|3[01])/(0[1-9]|1[012])/[12][0-9]{3}");
    ardt=date.split("/");
    erro=false;
    if ( date.search(ExpReg)==-1){
    erro = true;
    }
    else if (((ardt[1]==4)||(ardt[1]==6)||(ardt[1]==9)||(ardt[1]==11))&&(ardt[0]>30)){
    erro = true;
    }else if ( ardt[1]==2) {
    if ((ardt[0]>28)&&((ardt[2]%4)!=0))
    erro = true;
    if ((ardt[0]>29)&&((ardt[2]%4)==0))
    erro = true;
    }

    if (erro) {
    alert("Data/Hora Inválida");
    campo.focus();
    campo.value = "";
    return false;
    }else{

    var arrayHoras = datetime[1].split(':')
    var horas      = arrayHoras[0];
    var minutos    = arrayHoras[1];
    var segundos   = arrayHoras[2];
    if(horas > 23 || minutos > 59 || segundos > 59){
    alert('Data/Hora Inválida');
    campo.focus();
    campo.value = "";
    return false
    }

    }

    if(document.getElementById('txtDtInicio').value != '' && document.getElementById('txtDtFim').value != ''){
    var dataInicial = returnDateTime(document.getElementById('txtDtInicio').value);
    var dataFinal   = returnDateTime(document.getElementById('txtDtFim').value);

    var valido = (dataInicial.getTime() < dataFinal.getTime());

    if(!valido)
    {
    document.getElementById('txtDtInicio').value = '';
    document.getElementById('txtDtFim').value = '';
    alert('A Data/Hora Inicio deve ser menor que a Data/Hora Fim');
    return false;
    }
    }

    return true;

    }

    function returnDateTime(valor){

    valorArray = valor != '' ? valor.split(" ") : '';

    if(Array.isArray(valorArray)){
    var data = valorArray[0]
    data = data.split('/');
    var mes = parseInt(data[1]) - 1;
    var horas = valorArray[1].split(':');

    var segundos = typeof horas[2] != 'undefined' ?  horas[2] : 00;
    var dataCompleta = new Date(data[2], mes  ,data[0], horas[0] , horas[1] , segundos);
    return dataCompleta;
    }

    return false;
    }

    function pesquisar(){
    document.getElementById('frmIndisponibilidadePeticionamentoLista').action='<?= $strLinkPesquisar ?>';
    document.getElementById('frmIndisponibilidadePeticionamentoLista').submit();
    }

    function corrigirTela(){
    var tamanhoGrid = document.getElementById('tbIndisponibilidade').offsetHeight;
    var tamanhoLinha = document.getElementById('divInfraAreaTela').offsetHeight;
    var tamanhoTotal = (tamanhoGrid + tamanhoLinha) - 395;
    document.getElementById('divInfraAreaTela').style.height = tamanhoTotal + 'px';
    }

    function esconderMenu(){
    infraMenuSistemaEsquema();
    corrigirTela();
    }

<?
PaginaPeticionamentoExterna::getInstance()->fecharJavaScript();
PaginaPeticionamentoExterna::getInstance()->fecharHead();
?>

    <style type="text/css">

    </style>

<?php
PaginaPeticionamentoExterna::getInstance()->abrirBody($strTitulo, 'onload="inicializar();"');
$urlForm = 'modulos/peticionamento/md_pet_usu_ext_indisponibilidade_lista.php?acao_externa=md_pet_usu_ext_indisponibilidade_consultar&id_orgao_acesso_externo=0';
?>
    <form id="frmIndisponibilidadePeticionamentoLista" method="post" action="<?= $strLinkPesquisar ?>">

        <?php PaginaPeticionamentoExterna::getInstance()->montarBarraComandosSuperior($arrComandos); ?>

        <div class="row">
            <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                <label id="lblDescricao" class="infraLabelOpcional">
                    <br/>Conforme normativo próprio, algumas indisponibilidades justificam a prorrogação automática dos
                    prazos externos de Intimações Eletrônicas que venceriam durante o período da indisponibilidade,
                    prorrogando-os para o primeiro dia útil seguinte ao fim da respectiva indisponibilidade. Na coluna
                    "Prorrogação Automática dos Prazos", as indisponibilidades marcadas com "Sim" justificaram a
                    referida
                    prorrogação.<br/><br/><br/>
                </label>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12 col-md-3 col-lg-3 col-xl-2">
                <div class="form-group">
                    <label id="lblDtInicio" for="txtDtInicio" class="infraLabelOpcional">Início:</label>
                    <div class="input-group mb-3">
                        <input type="text" name="txtDtInicio" id="txtDtInicio" onchange="validDate('I');"
                            value="<?= PaginaSEIExterna::tratarHTML($strDtInicio) ?>"
                            onkeypress="return infraMascara(this, event, '##/##/#### ##:##');"
                            class="infraText form-control"/>
                        <img src="<?= PaginaPeticionamentoExterna::getInstance()->getDiretorioSvgGlobal() ?>/calendario.svg"
                            id="imgDtInicio"
                            title="Selecionar Data/Hora Inicial"
                            alt="Selecionar Data/Hora Inicial" class="infraImg"
                            onclick="infraCalendario('txtDtInicio',this,true,'<?= InfraData::getStrDataAtual() . ' 00:00' ?>');"/>
                    </div>
                </div>
            </div>
            <div class="col-sm-12 col-md-3 col-lg-3 col-xl-2">
                <div class="form-group">
                    <label id="lblDtFim" for="txtDtFim" class="infraLabelOpcional">Fim:</label>
                    <div class="input-group mb-3">
                        <input type="text" name="txtDtFim" onchange="validDate('F');" id="txtDtFim"
                            value="<?= PaginaSEIExterna::tratarHTML($strDtFim) ?>"
                            onchange="validDate('F');" onkeypress="return infraMascara(this, event, '##/##/#### ##:##');"
                            maxlength="16" class="infraText form-control"/>
                        <img src="<?= PaginaPeticionamentoExterna::getInstance()->getDiretorioSvgGlobal() ?>/calendario.svg"
                            id="imgDtFim"
                            title="Selecionar Data/Hora Final"
                            alt="Selecionar Data/Hora Final"
                            class="infraImg"
                            onclick="infraCalendario('txtDtFim',this,true,'<?= InfraData::getStrDataAtual() . ' 23:59' ?>');"/>
                    </div>
                </div>
            </div>
            <div class="col-sm-12 col-md-5 col-lg-4 col-xl-3">
                <div class="form-group">
                    <label id="lblSinProrrogacao" for="selSinProrrogacao" class="infraLabelOpcional">Prorrogação Automática
                        dos
                        Prazos:</label>
                    <select onchange="pesquisar()" id="selSinProrrogacao" name="selSinProrrogacao"
                            class="infraSelect form-control">
                        <?= $strItensSelSinProrrogacaoAutomatica ?>
                    </select>
                    <input type="submit" style="visibility: hidden; display:none;"/>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                <?
                PaginaPeticionamentoExterna::getInstance()->montarAreaTabela($strResultado, $numRegistros);
                ?>
            </div>
        </div>
    </form>
<?
PaginaPeticionamentoExterna::getInstance()->fecharBody();
PaginaPeticionamentoExterna::getInstance()->fecharHtml();
?>
