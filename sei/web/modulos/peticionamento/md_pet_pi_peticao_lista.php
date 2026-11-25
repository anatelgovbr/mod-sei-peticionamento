<?php
/**
 * ANATEL
 *
 * Meus Processos: processos abertos pelo usuário logado por meio do Peticionamento Interno.
 *
 * 21/09/2026 - criado por emerson_b.goncalves - ANATEL
 */
try {
    require_once dirname(__FILE__) . '/../../SEI.php';

    session_start();

    SessaoSEI::getInstance()->validarLink();

    $strAcao = MdPetPiEntradaINT::getStr('acao');

    SessaoSEI::getInstance()->validarPermissao($strAcao);

    if ($strAcao !== 'md_pet_pi_peticao_listar') {
        throw new InfraException("Ação '" . $strAcao . "' não reconhecida.");
    }

    $strTitulo = 'Peticionamento Interno - Meus Processos';
    $strResultado = '';
    $arrComandos = [];

    $arrIndicacaoInteressado = MdPetPiTpProcessoINT::listarValoresIndicacaoInteressado();
    $strFiltroIndicacao = MdPetPiEntradaINT::postEnum('selIndicacaoInteressado', $arrIndicacaoInteressado);
    $strFiltroProcesso = MdPetPiEntradaINT::postStr('txtProcesso');
    $strFiltroTipoProcesso = MdPetPiEntradaINT::postStr('txtTipoProcesso');

    $objMdPetPiPeticaoDTO = new MdPetPiPeticaoDTO();
    $objMdPetPiPeticaoDTO->retDblIdProcedimento();
    $objMdPetPiPeticaoDTO->retStrProtocoloFormatadoProcedimento();
    $objMdPetPiPeticaoDTO->retStrNomeTipoProcedimentoProcesso();
    $objMdPetPiPeticaoDTO->retStrEspecificacaoProcedimento();
    $objMdPetPiPeticaoDTO->retStrStaIndicacaoInteressado();
    $objMdPetPiPeticaoDTO->retDtaGeracaoProcedimento();
    $objMdPetPiPeticaoDTO->setNumIdUsuario(SessaoSEI::getInstance()->getNumIdUsuario());

    if ($strFiltroProcesso !== '') {
        $objMdPetPiPeticaoDTO->setStrProtocoloFormatadoPesquisaProcedimento('%' . InfraUtil::retirarFormatacao($strFiltroProcesso, false) . '%', InfraDTO::$OPER_LIKE);
    }

    if ($strFiltroTipoProcesso !== '') {
        $objMdPetPiPeticaoDTO->setStrNomeTipoProcedimentoProcesso('%' . $strFiltroTipoProcesso . '%', InfraDTO::$OPER_LIKE);
    }

    if ($strFiltroIndicacao !== '') {
        $objMdPetPiPeticaoDTO->setStrStaIndicacaoInteressado($strFiltroIndicacao);
    }

    PaginaSEI::getInstance()->prepararOrdenacao($objMdPetPiPeticaoDTO, 'Peticionamento', InfraDTO::$TIPO_ORDENACAO_DESC);
    PaginaSEI::getInstance()->prepararPaginacao($objMdPetPiPeticaoDTO, 200);

    $arrObjMdPetPiPeticaoDTO = (new MdPetPiPeticaoRN())->listar($objMdPetPiPeticaoDTO);

    PaginaSEI::getInstance()->processarPaginacao($objMdPetPiPeticaoDTO);

    $numRegistros = count($arrObjMdPetPiPeticaoDTO);

    $arrComandos[] = '<button type="button" accesskey="p" id="btnPesquisar" value="Pesquisar" onclick="pesquisar();" class="infraButton"><span class="infraTeclaAtalho">P</span>esquisar</button>';

    if ($numRegistros > 0) {

        $bolAcaoProcedimentoTrabalhar = SessaoSEI::getInstance()->verificarPermissao('procedimento_trabalhar');

        $strResultado .= '<table width="99%" class="infraTable" summary="Tabela de Processos abertos por Peticionamento Interno">' . "\n";
        $strResultado .= '<caption class="infraCaption">' . PaginaSEI::getInstance()->gerarCaptionTabela('Processos', $numRegistros) . '</caption>';
        $strResultado .= '<tr>';
        $strResultado .= '<th class="infraTh thLeft" width="17%">' . PaginaSEI::getInstance()->getThOrdenacao($objMdPetPiPeticaoDTO, 'Processo', 'ProtocoloFormatadoProcedimento', $arrObjMdPetPiPeticaoDTO) . '</th>' . "\n";
        $strResultado .= '<th class="infraTh thLeft" width="28%">' . PaginaSEI::getInstance()->getThOrdenacao($objMdPetPiPeticaoDTO, 'Tipo de Processo', 'NomeTipoProcedimentoProcesso', $arrObjMdPetPiPeticaoDTO) . '</th>' . "\n";
        $strResultado .= '<th class="infraTh thLeft" width="28%">' . PaginaSEI::getInstance()->getThOrdenacao($objMdPetPiPeticaoDTO, 'Especificação', 'EspecificacaoProcedimento', $arrObjMdPetPiPeticaoDTO) . '</th>' . "\n";
        $strResultado .= '<th class="infraTh thLeft" width="15%">' . PaginaSEI::getInstance()->getThOrdenacao($objMdPetPiPeticaoDTO, 'Indicação de Interessado', 'StaIndicacaoInteressado', $arrObjMdPetPiPeticaoDTO) . '</th>' . "\n";
        $strResultado .= '<th class="infraTh" width="12%">' . PaginaSEI::getInstance()->getThOrdenacao($objMdPetPiPeticaoDTO, 'Data de Geração', 'GeracaoProcedimento', $arrObjMdPetPiPeticaoDTO) . '</th>' . "\n";
        $strResultado .= '</tr>' . "\n";

        $strCssTr = '';

        foreach ($arrObjMdPetPiPeticaoDTO as $objDTO) {
            $strCssTr = ($strCssTr === '<tr class="infraTrClara">') ? '<tr class="infraTrEscura">' : '<tr class="infraTrClara">';
            $strResultado .= $strCssTr;

            $strProtocolo = PaginaSEI::tratarHTML($objDTO->getStrProtocoloFormatadoProcedimento());

            if ($bolAcaoProcedimentoTrabalhar) {
                $strLinkProcesso = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=procedimento_trabalhar&acao_origem=' . $strAcao . '&acao_retorno=' . $strAcao . '&id_procedimento=' . $objDTO->getDblIdProcedimento());
                $strResultado .= '<td valign="middle"><a onclick="infraLimparFormatarTrAcessada(this.parentNode.parentNode);" href="' . PaginaSEI::getInstance()->formatarXHTML($strLinkProcesso) . '" target="_blank" class="protocoloNormal" title="' . PaginaSEI::tratarHTML($objDTO->getStrNomeTipoProcedimentoProcesso()) . '" tabindex="' . PaginaSEI::getInstance()->getProxTabTabela() . '">' . $strProtocolo . '</a></td>';
            } else {
                $strResultado .= '<td valign="middle">' . $strProtocolo . '</td>';
            }

            $strResultado .= '<td valign="middle">' . PaginaSEI::tratarHTML($objDTO->getStrNomeTipoProcedimentoProcesso()) . '</td>';
            $strResultado .= '<td valign="middle">' . PaginaSEI::tratarHTML($objDTO->getStrEspecificacaoProcedimento()) . '</td>';
            $strResultado .= '<td valign="middle">' . PaginaSEI::tratarHTML(MdPetPiTpProcessoINT::obterDescricaoIndicacaoInteressado($objDTO->getStrStaIndicacaoInteressado())) . '</td>';
            $strResultado .= '<td valign="middle" align="center">' . PaginaSEI::tratarHTML($objDTO->getDtaGeracaoProcedimento()) . '</td>';
            $strResultado .= '</tr>' . "\n";
        }

        $strResultado .= '</table>';

        $arrComandos[] = '<button type="button" accesskey="i" id="btnImprimir" value="Imprimir" onclick="infraImprimirTabela();" class="infraButton"><span class="infraTeclaAtalho">I</span>mprimir</button>';
    }

    $arrComandos[] = '<button type="button" accesskey="c" name="btnFechar" id="btnFechar" value="Fechar" onclick="location.href=\'' . PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?acao=procedimento_controlar&acao_origem=' . $strAcao)) . '\';" class="infraButton">Fe<span class="infraTeclaAtalho">c</span>har</button>';

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
require_once 'md_pet_pi_peticao_lista_css.php';
PaginaSEI::getInstance()->fecharStyle();
PaginaSEI::getInstance()->montarJavaScript();
PaginaSEI::getInstance()->abrirJavaScript();
require_once 'md_pet_pi_peticao_lista_js.php';
PaginaSEI::getInstance()->fecharJavaScript();
PaginaSEI::getInstance()->fecharHead();
PaginaSEI::getInstance()->abrirBody($strTitulo, 'onload="inicializar();"');
?>
<form id="frmMdPetPiPeticaoLista" method="post"
      action="<?= PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . $strAcao . '&acao_origem=' . $strAcao)) ?>">

    <?php PaginaSEI::getInstance()->montarBarraComandosSuperior($arrComandos); ?>

    <div class="row">
        <div class="col-sm-12 col-md-4 col-lg-3 col-xl-3">
            <label id="lblProcesso" for="txtProcesso" class="infraLabelOpcional">Processo:</label>
            <input type="text" name="txtProcesso" id="txtProcesso"
                   value="<?= PaginaSEI::tratarHTML($strFiltroProcesso) ?>"
                   class="infraText form-control"/>
        </div>
        <div class="col-sm-12 col-md-5 col-lg-5 col-xl-4">
            <label id="lblTipoProcesso" for="txtTipoProcesso" class="infraLabelOpcional">Tipo de Processo:</label>
            <input type="text" name="txtTipoProcesso" id="txtTipoProcesso"
                   value="<?= PaginaSEI::tratarHTML($strFiltroTipoProcesso) ?>"
                   class="infraText form-control"/>
        </div>
        <div class="col-sm-12 col-md-3 col-lg-4 col-xl-4">
            <label id="lblIndicacaoInteressado" for="selIndicacaoInteressado" class="infraLabelOpcional">Indicação de Interessado:</label>
            <select onchange="pesquisar();" id="selIndicacaoInteressado" name="selIndicacaoInteressado"
                    class="infraSelect form-select">
                <option value="">Todos</option>
                <?php foreach ($arrIndicacaoInteressado as $strChave => $strDescricao) { ?>
                    <option value="<?= PaginaSEI::tratarHTML($strChave) ?>" <?= $strFiltroIndicacao === $strChave ? 'selected="selected"' : '' ?>><?= PaginaSEI::tratarHTML($strDescricao) ?></option>
                <?php } ?>
            </select>
        </div>
    </div>

    <?php
    PaginaSEI::getInstance()->montarAreaTabela($strResultado, $numRegistros, true);
    PaginaSEI::getInstance()->montarBarraComandosInferior($arrComandos);
    ?>

</form>
<?php
PaginaSEI::getInstance()->fecharBody();
PaginaSEI::getInstance()->fecharHtml();
