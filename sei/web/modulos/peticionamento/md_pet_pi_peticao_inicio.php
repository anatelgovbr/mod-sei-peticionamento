<?php
/**
 * ANATEL
 *
 * Escolha do Tipo de Processo do Peticionamento Interno, com as Orientações Gerais no topo.
 *
 * 17/09/2026 - criado por emerson_b.goncalves - ANATEL
 */
try {
    require_once dirname(__FILE__) . '/../../SEI.php';

    session_start();

    SessaoSEI::getInstance()->validarLink();

    $strAcao = MdPetPiEntradaINT::getStr('acao');

    SessaoSEI::getInstance()->validarPermissao($strAcao);

    if ($strAcao !== 'md_pet_pi_peticao_iniciar') {
        throw new InfraException("Ação '" . $strAcao . "' não reconhecida.");
    }

    $strTitulo = 'Peticionamento Interno - Iniciar Processo';

    $objMdPetPiOrientacaoDTO = new MdPetPiOrientacaoDTO();
    $objMdPetPiOrientacaoDTO->retStrOrientacoesGerais();
    $objMdPetPiOrientacaoDTO->retNumIdConjuntoEstilos();
    $objMdPetPiOrientacaoDTO->setNumIdMdPetPiOrientacao(MdPetPiOrientacaoRN::$ID_FIXO_ORIENTACOES);
    $objMdPetPiOrientacaoDTO = (new MdPetPiOrientacaoRN())->consultar($objMdPetPiOrientacaoDTO);

    if (MdPetPiEntradaINT::getStr('iframe') === 'S') {
        $strOrientacoesHtml = $objMdPetPiOrientacaoDTO !== null ? $objMdPetPiOrientacaoDTO->getStrOrientacoesGerais() : '';
        $numIdConjuntoEstilosOrientacoes = $objMdPetPiOrientacaoDTO !== null ? $objMdPetPiOrientacaoDTO->getNumIdConjuntoEstilos() : null;
        require_once 'md_pet_pi_peticao_orientacoes.php';
        die;
    }

    $strLinkOrientacoes = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . $strAcao . '&iframe=S');

    $objMdPetPiTpProcessoDTO = new MdPetPiTpProcessoDTO();
    $objMdPetPiTpProcessoDTO->retStrNomeTipoProcedimento();
    $objMdPetPiTpProcessoDTO->retStrOrientacoes();
    $objMdPetPiTpProcessoDTO->setOrdStrNomeTipoProcedimento(InfraDTO::$TIPO_ORDENACAO_ASC);

    $arrObjMdPetPiTpProcessoDTO = (new MdPetPiTpProcessoRN())->listarDisponiveis($objMdPetPiTpProcessoDTO);

    $numRegistros = count($arrObjMdPetPiTpProcessoDTO);
    $bolAcaoPeticionar = SessaoSEI::getInstance()->verificarPermissao('md_pet_pi_peticao_cadastrar');

    $strResultado = '<table id="tblTipoProcedimento" class="tabelaOpcoes infraTable" summary="Tabela de Tipos de Processo.">' . "\n";
    $strResultado .= '<thead><tr style="display:none"><th class="infraTh">' . PaginaSEI::getInstance()->getThCheck() . '</th></tr></thead>';
    $strResultado .= '<tbody>' . "\n";

    $numIndice = 0;
    foreach ($arrObjMdPetPiTpProcessoDTO as $objDTO) {
        $strNomeTipoProcesso = $objDTO->getStrNomeTipoProcedimento();
        $strOrientacoesTexto = MdPetPiPeticaoINT::converterHtmlEmTexto($objDTO->getStrOrientacoes());
        $strTooltipOrientacoes = '';

        if ($strOrientacoesTexto !== '') {
            // O tooltip do core monta o conteúdo por innerHTML e mantém a formatação; o aria-label fica em texto.
            $strOrientacoesHtml = str_replace(["\r", "\n"], ' ', (string) $objDTO->getStrOrientacoes());
            $strTooltipOrientacoes = ' aria-label="' . PaginaSEI::tratarHTML($strNomeTipoProcesso) . ' / ' . str_replace("\n", '&#13;', PaginaSEI::tratarHTML($strOrientacoesTexto)) . '"'
                . ' onmouseover="return infraTooltipMostrar(\'' . PaginaSEI::tratarHTML(PaginaSEI::formatarParametrosJavaScript($strOrientacoesHtml, false)) . '\',\'' . PaginaSEI::tratarHTML(PaginaSEI::formatarParametrosJavaScript($strNomeTipoProcesso)) . '\');"'
                . ' onmouseout="return infraTooltipOcultar();"';
        }

        $strResultado .= '<tr class="infraTrClara" data-desc="' . PaginaSEI::tratarHTML(strtolower(InfraString::excluirAcentos($strNomeTipoProcesso))) . '">';
        $strResultado .= '<td>';
        $strResultado .= PaginaSEI::getInstance()->getTrCheck($numIndice++, $objDTO->getNumIdMdPetPiTpProcesso(), $strNomeTipoProcesso, 'N', 'Infra', 'style="display:none;"');

        if ($bolAcaoPeticionar) {
            $strLinkPeticionar = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_pet_pi_peticao_cadastrar&acao_origem=' . $strAcao . '&acao_retorno=' . $strAcao . '&id_md_pet_pi_tp_processo=' . $objDTO->getNumIdMdPetPiTpProcesso());
            $strResultado .= '<a href="' . PaginaSEI::getInstance()->formatarXHTML($strLinkPeticionar) . '" tabindex="' . PaginaSEI::getInstance()->getProxTabTabela() . '" class="ancoraOpcao"' . $strTooltipOrientacoes . '>' . PaginaSEI::tratarHTML($strNomeTipoProcesso) . '</a>';
        } else {
            $strResultado .= '<span' . $strTooltipOrientacoes . '>' . PaginaSEI::tratarHTML($strNomeTipoProcesso) . '</span>';
        }

        $strResultado .= '</td>';
        $strResultado .= '</tr>' . "\n";
    }

    $strResultado .= '</tbody>' . "\n";
    $strResultado .= '</table>';

} catch (Exception $e) {
    PaginaSEI::getInstance()->processarExcecao($e);
}

PaginaSEI::getInstance()->montarDocType();
PaginaSEI::getInstance()->abrirHtml();
PaginaSEI::getInstance()->abrirHead();
PaginaSEI::getInstance()->montarMeta();
PaginaSEI::getInstance()->montarTitle(':: ' . PaginaSEI::getInstance()->getStrNomeSistema() . ' - ' . $strTitulo . ' ::');
PaginaSEI::getInstance()->montarStyle();
PaginaSEI::getInstance()->abrirStyle();
require_once 'md_pet_pi_peticao_inicio_css.php';
PaginaSEI::getInstance()->fecharStyle();
PaginaSEI::getInstance()->montarJavaScript();
PaginaSEI::getInstance()->abrirJavaScript();
require_once 'md_pet_pi_peticao_inicio_js.php';
PaginaSEI::getInstance()->fecharJavaScript();
PaginaSEI::getInstance()->fecharHead();
PaginaSEI::getInstance()->abrirBody($strTitulo, 'onload="inicializar();"');
?>
<form id="frmMdPetPiPeticaoInicio" method="post" onsubmit="return false;"
      action="<?= PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . $strAcao . '&acao_origem=' . $strAcao)) ?>">

    <?php
    PaginaSEI::getInstance()->montarBarraComandosSuperior([]);
    PaginaSEI::getInstance()->abrirAreaDados('auto');
    ?>

    <div class="row">
        <div class="col-12">
            <fieldset id="fldOrientacoesGerais" class="infraFieldset form-control">
                <legend class="infraLegend">&nbsp;Orientações Gerais&nbsp;</legend>
                <iframe id="ifrOrientacoesGerais" name="ifrOrientacoesGerais" frameborder="0" marginheight="0" marginwidth="0"
                        onload="redimensionarOrientacoes(this);"
                        src="<?= PaginaSEI::getInstance()->formatarXHTML($strLinkOrientacoes) ?>"></iframe>
            </fieldset>
        </div>
    </div>

    <?php PaginaSEI::getInstance()->fecharAreaDados(); ?>

    <div class="mx-auto w-md-50 w-100">
        <label id="lblEscolhaTipoProcesso" for="txtFiltro" class="infraLabelObrigatorio">Escolha o Tipo do Processo:</label>
        <input type="text" id="txtFiltro" class="infraAutoCompletar infraText" autocomplete="off"
               tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
    </div>

    <div class="mx-auto w-md-50 w-100">
        <?php PaginaSEI::getInstance()->montarAreaTabela($strResultado, $numRegistros, true); ?>
    </div>

    <?php PaginaSEI::getInstance()->montarBarraComandosInferior([]); ?>

</form>
<?php
PaginaSEI::getInstance()->fecharBody();
PaginaSEI::getInstance()->fecharHtml();
