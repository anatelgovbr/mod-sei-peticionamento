<?php
/**
 * @since  08/02/2018
 * @author Jaqueline Mendes <jaqueline.mendes@castgroup.com.br>
 */

require_once dirname(__FILE__) . '/../../SEI.php';

session_start();
SessaoSEI::getInstance()->validarLink();

//URL Base
$strUrl                   = 'controlador.php?acao=md_pet_int_relatorio';
$strTitulo                = '';
$strSelSituacao           = MdPetIntRelatorioINT::getSituacoes();
$strLinkAjaxTpIntimacao   = SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=md_pet_tp_int_auto_completar');
$strLinkTpIntSelecionar   = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_pet_int_tipo_intimacao_selecionar&tipo_selecao=2&id_object=objLupaTpIntimacao');
$strLinkAjaxUnidade       = SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=unidade_auto_completar_todas');
$strLinkUnidSelecionar    = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=unidade_selecionar_todas&tipo_selecao=2&id_object=objLupaUnidade');
$strUrlGrafico1           = PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . $_GET['acao'] . '&acao_origem=' . $_GET['acao'].'&grafico=1'));;
$strUrlGrafico2           = PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . $_GET['acao'] . '&acao_origem=' . $_GET['acao'].'&grafico=2'));;
$strUrlGrafico3           = PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . $_GET['acao'] . '&acao_origem=' . $_GET['acao'].'&grafico=3'));;
$strUrlGrafico4           = PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . $_GET['acao'] . '&acao_origem=' . $_GET['acao'].'&grafico=4'));;
$strUrlGrafico5           = PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . $_GET['acao'] . '&acao_origem=' . $_GET['acao'].'&grafico=5'));;
$tipoGrafico              = array_key_exists('grafico', $_GET) ? $_GET['grafico'] : 0;
$tipoPesquisar            = count($_POST);
$htmlGrafico              = $tipoGrafico != 0 ? MdPetIntRelatorioINT::gerarGraficoGeral($tipoGrafico) : null;
$arrGraficosTipoIntimacao = $tipoGrafico != 0 ? MdPetIntRelatorioINT::gerarGraficosTipoIntimacao($tipoGrafico) : array();
$strSelGraficoGeral       = MdPetIntRelatorioINT::getOptionsTipoGrafico($tipoGrafico);
$strUrlExcel              = PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_pet_int_relatorio_exp_excel&acao_origem=' . $_GET['acao'].'&excel=1'));;
$strUrlPesquisar          = PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . $_GET['acao'] . '&acao_origem=' . $_GET['acao'].'&pesquisar=1'));

switch ($_GET['acao'])
{
    case 'md_pet_int_relatorio_listar':
        $strTitulo = 'Intimações Eletrônicas';
        break;

    default:
        throw new InfraException("Ação '" . $_GET['acao'] . "' não reconhecida.");
}


//Botões de ação do topo
$arrComandos[] = '<button type="button" accesskey="P" id="btnPesquisar" onclick="pesquisar()" class="infraButton">
                                    <span class="infraTeclaAtalho">P</span>esquisar
                              </button>';

$arrComandos[] = '<button type="button" accesskey="G" id="btnGerarGrafico" onclick="gerarGrafico()" class="infraButton">
                                    <span class="infraTeclaAtalho">G</span>erar Gráfico </button>';

$arrComandos[] = '<button type="button" accesskey="X" id="btnExportarExcel" onclick="exportarExcel()" class="infraButton">
                                    E<span class="infraTeclaAtalho">x</span>portar Excel </button>';

$arrComandos[] = '<button type="button" accesskey="L" id="btnLimparCriterios" onclick="limparCriterios()" class="infraButton">
                                    <span class="infraTeclaAtalho">L</span>impar Critérios </button>';
if($tipoPesquisar){
    require_once 'md_pet_int_relatorio_lista.php';
}
PaginaSEI::getInstance()->montarDocType();
PaginaSEI::getInstance()->abrirHtml();
PaginaSEI::getInstance()->abrirHead();
PaginaSEI::getInstance()->montarMeta();
PaginaSEI::getInstance()->montarTitle(':: ' . PaginaSEI::getInstance()->getStrNomeSistema() . ' - ' . $strTitulo . ' ::');
PaginaSEI::getInstance()->montarStyle();
PaginaSEI::getInstance()->abrirStyle();

require_once 'md_pet_int_relatorio_css.php';

PaginaSEI::getInstance()->fecharStyle();
PaginaSEI::getInstance()->montarJavaScript();
PaginaSEI::getInstance()->abrirJavaScript();

require_once 'md_pet_int_relatorio_js.php';

PaginaSEI::getInstance()->fecharJavaScript();
PaginaSEI::getInstance()->fecharHead();
PaginaSEI::getInstance()->abrirBody($strTitulo, 'onload="inicializar();"');
PaginaSEI::getInstance()->abrirAreaDados('50em');
?>
    <form id="frmIntimacaoRelatorioLista" method="post"
          action="<?= PaginaSEI::getInstance()->formatarXHTML(
              SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . $_GET['acao'] . '&acao_origem=' . $_GET['acao'])
          ) ?>">

        <?php PaginaSEI::getInstance()->montarBarraComandosSuperior($arrComandos); ?>

        <!-- Filtro padrão -->
        <?php require_once 'md_pet_int_relatorio_filtro.php'; ?>

        <!-- Lista de Dados -->
        <?php
        PaginaSEI::getInstance()->fecharAreaDados(); ?>
        <div id="divTabelaIntimacao">
            <div class="grid grid-13 alturaPadrao"></div>

        <?php
        if($tipoPesquisar){
            PaginaSEI::getInstance()->montarAreaTabela($strResultado, $numRegistros);
        }
        ?>

        </div>

        <div id="divGraficos" style="display:none" >
            <?php require_once 'md_pet_int_relatorio_graficos.php'; ?>
        </div>
        <br><br><br>
        <?php PaginaSEI::getInstance()->montarBarraComandosInferior($arrComandos);
        ?>

        <input type="hidden" name="hdnIsPesquisa" id="hdnIsPesquisa" value="<?php echo array_key_exists('pesquisar', $_GET) ? $_GET['pesquisar'] : 0;  ?>">
        <input type="hidden" name="hdnAcaoOrigem" id="hdnAcaoOrigem" value="<?php echo array_key_exists('acao_origem', $_GET) ? $_GET['acao_origem'] : '';  ?>">

    </form>

<?php
PaginaSEI::getInstance()->fecharBody();
PaginaSEI::getInstance()->fecharHtml();