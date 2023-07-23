<?php
/**
 * @since  08/02/2018
 * @author Jaqueline Mendes <jaqueline.mendes@castgroup.com.br>
 */

require_once dirname(__FILE__) . '/../../SEI.php';

session_start();
SessaoSEI::getInstance()->validarLink();

//URL Base
$strUrl = 'controlador.php?acao=md_pet_int_relatorio';
$strTitulo = '';

$strSelSituacao             = MdPetIntRelatorioINT::getSituacoes();
$strLinkAjaxTpIntimacao     = SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=md_pet_tp_int_auto_completar');
$strLinkTpIntSelecionar     = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_pet_int_tipo_intimacao_selecionar&tipo_selecao=2&id_object=objLupaTpIntimacao');
$strLinkAjaxUnidade         = SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=unidade_auto_completar_todas');
$strLinkUnidSelecionar      = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=unidade_selecionar_todas&tipo_selecao=2&id_object=objLupaUnidade');
$strLinkGraficoIndividual   = SessaoSEI::getInstance()->assinarLink('modulos/peticionamento/controlador_ajax.php?acao_ajax=md_pet_int_relatorio_grafico');
$strLinkDestinatariosSelecao = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=contato_selecionar&tipo_selecao=2&id_object=objLupaDestinatarios');
$strLinkAjaxContatos = SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=contato_auto_completar_contexto_RI1225');

$strUrlGrafico1     = PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . $_GET['acao'] . '&acao_origem=' . $_GET['acao'] . '&grafico=1'));
$strUrlGrafico2     = PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . $_GET['acao'] . '&acao_origem=' . $_GET['acao'] . '&grafico=2'));
$strUrlGrafico3     = PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . $_GET['acao'] . '&acao_origem=' . $_GET['acao'] . '&grafico=3'));
$strUrlGrafico4     = PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . $_GET['acao'] . '&acao_origem=' . $_GET['acao'] . '&grafico=4'));
$strUrlGrafico5     = PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . $_GET['acao'] . '&acao_origem=' . $_GET['acao'] . '&grafico=5'));

$strUrlExcel        = PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_pet_int_relatorio_exp_excel&acao_origem=' . $_GET['acao'] . '&excel=1'));
$strUrlPesquisar    = PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . $_GET['acao'] . '&acao_origem=' . $_GET['acao'] . '&pesquisar=1'));

$tipoPesquisar  = count($_POST);
$tipoGrafico    = array_key_exists('grafico', $_GET) ? $_GET['grafico'] : 0;

$strSelGraficoGeral = MdPetIntRelatorioINT::getOptionsTipoGrafico($tipoGrafico);

// Alterado em 16/01/2023 para performar a pagina.
// Ao inves de gerar todos os graficos de uma vez eu gero um array inicial apenas com os tipos de intimacao para carregar um a um via ajax conforme demanda:

$arrTiposIntimacao  = [];
$arrTipoIntimacao   = PaginaSEI::getInstance()->getArrValuesSelect($_POST['hdnTpIntimacao']);

$objMdPetIntRelDestDTO = new MdPetIntRelDestinatarioDTO();
$objMdPetIntRelDestDTO->retNumIdMdPetTipoIntimacao();
$objMdPetIntRelDestDTO->retStrNomeTipoIntimacao();
if(count($arrTipoIntimacao) > 0) {
    $objMdPetIntRelDestDTO->setNumIdMdPetTipoIntimacao($arrTipoIntimacao, InfraDTO::$OPER_IN);
}
$objMdPetIntRelDestDTO->setDistinct(true);
$arrObjDTOs = (new MdPetIntRelDestinatarioRN())->listar($objMdPetIntRelDestDTO);

if(count($arrObjDTOs) > 0){
    $arrTiposIntimacao = array_unique(InfraArray::converterArrInfraDTO($arrObjDTOs, 'NomeTipoIntimacao', 'IdMdPetTipoIntimacao'));
}

// Final

switch ($_GET['acao']) {
    case 'md_pet_int_relatorio_listar':
        $strTitulo = 'Intimações Eletrônicas';
        break;

    default:
        throw new InfraException("Ação '" . $_GET['acao'] . "' não reconhecida.");
}

//Botões de ação do topo
$arrComandos[] = '<button type="button" accesskey="P" id="btnPesquisar" onclick="pesquisar()" class="infraButton"><span class="infraTeclaAtalho">P</span>esquisar</button>';
$arrComandos[] = '<button type="button" accesskey="G" id="btnGerarGrafico" onclick="gerarGrafico()" class="infraButton"><span class="infraTeclaAtalho">G</span>erar Gráfico </button>';
$arrComandos[] = '<button type="button" accesskey="X" id="btnExportarExcel" onclick="exportarExcel()" class="infraButton">E<span class="infraTeclaAtalho">x</span>portar Excel </button>';
$arrComandos[] = '<button type="button" accesskey="L" id="btnLimparCriterios" onclick="limparCriterios()" class="infraButton"><span class="infraTeclaAtalho">L</span>impar Critérios </button>';

if ($tipoPesquisar) {
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
PaginaSEI::getInstance()->fecharJavaScript();
PaginaSEI::getInstance()->fecharHead();
PaginaSEI::getInstance()->abrirBody($strTitulo, 'onload="inicializar();"');
PaginaSEI::getInstance()->abrirAreaDados('auto');
?>
    <form id="frmIntimacaoRelatorioLista" method="post"
          action="<?= PaginaSEI::getInstance()->formatarXHTML(
              SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . $_GET['acao'] . '&acao_origem=' . $_GET['acao'])
          ) ?>">

        <?php PaginaSEI::getInstance()->montarBarraComandosSuperior($arrComandos); ?>
        <div class="row">
            <div class="col-sm-12 col-md-12 col-lg-10 col-xl-10">
                <!-- Filtro padrão -->
                <?php require_once 'md_pet_int_relatorio_filtro.php'; ?>
            </div>
        </div>
        <!-- Lista de Dados -->
        <?php PaginaSEI::getInstance()->fecharAreaDados(); ?>

        <div class="row" id="divTabelaIntimacao" style="display:none;">
            <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                <?php
                if ($tipoPesquisar) {
                    PaginaSEI::getInstance()->montarAreaTabela($strResultado, $numRegistros);
                }
                ?>
            </div>
        </div>

        <div class="row mt-4" id="divGraficos" style="display:none;" >
            <div class="col-12">
                <?php require_once 'md_pet_int_relatorio_graficos.php'; ?>
            </div>
        </div>

        <input type="hidden" name="hdnIsPesquisa" id="hdnIsPesquisa"
               value="<?php echo array_key_exists('pesquisar', $_GET) ? $_GET['pesquisar'] : 0; ?>">
        <input type="hidden" name="hdnAcaoOrigem" id="hdnAcaoOrigem"
               value="<?php echo array_key_exists('acao_origem', $_GET) ? $_GET['acao_origem'] : ''; ?>">

        <div id="espacamento" class="row" style="display: block;"></div>

        <?php PaginaSEI::getInstance()->montarBarraComandosInferior($arrComandos); ?>
        <?php PaginaSEI::getInstance()->fecharAreaDados(); ?>
    </form>

    <?php require_once 'md_pet_int_relatorio_js.php'; ?>

    <script src="modulos/peticionamento/js/jquery.inview.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function(){

            $('body').on('change', 'input[type=checkbox]#ocultarTiposIntVazios', function(){
                if($(this).is(":checked")) {
                    $(".carregarGraficoAjax:contains('Nenhum registro encontrado.')").closest('div.col-6').hide();
                }else{
                    $(".carregarGraficoAjax").closest('div.col-6').show();
                }
            });

            $('.carregarGraficoAjax').bind('inview', function (event, visible) {

                event.preventDefault(); event.stopPropagation();
                var self = $(this);
                var url = '<?= $strLinkGraficoIndividual ?>';

                if(self.is(':empty')){
                    self.html('<div class="progress" style="height:3px;width:300px;margin:0 auto"><div class="progress-bar active" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: 0%; height: 10px"></div></div>');
                    if (visible) {
                        console.log('Tipo intimacao:' + self.data('idtipointimacao'));
                        $.ajax({
                            type : 'POST',
                            url : url,
                            dataType: 'html',
                            data: $('form#frmIntimacaoRelatorioLista').serialize() + '&' + $.param({ tipoGrafico: <?= $tipoGrafico ?>, idTipoIntimacao : self.data('idtipointimacao') }),
                            beforeSend: function(){
                                $('.progress-bar').animate({width: "40%"}, 100, 'swing');
                            },
                            success: function(data){
                                $('.progress-bar').clearQueue().animate({ width: '100%'}, 1150, 'swing', function(){
                                    setTimeout(function(){
                                        self.html(data);
                                        if($('input[type=checkbox]#ocultarTiposIntVazios').is(":checked") && data == 'Nenhum registro encontrado.'){
                                            self.closest('div.col-6').hide();
                                        }
                                    }, 200);
                                });
                            },
                            error: function(request, status, err) {
                                console.log((status == 'timeout') ? 'Request limit timeout' : 'Error: ' + request + status + err);
                            }
                        });
                    }
                }

            });
        });
    </script>

<?php
PaginaSEI::getInstance()->fecharBody();
PaginaSEI::getInstance()->fecharHtml();
