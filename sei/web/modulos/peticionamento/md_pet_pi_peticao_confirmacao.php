<?php
/**
 * ANATEL
 *
 * Modal de confirmação do Peticionamento Interno, com as Orientações exibidas ao Usuário pós Peticionamento.
 *
 * 18/09/2026 - criado por emerson_b.goncalves - ANATEL
 */
try {
    require_once dirname(__FILE__) . '/../../SEI.php';

    session_start();

    PaginaSEI::getInstance()->setBolXHTML(false);
    PaginaSEI::getInstance()->setTipoPagina(InfraPagina::$TIPO_PAGINA_SIMPLES);

    SessaoSEI::getInstance()->validarLink();

    $strAcao = MdPetPiEntradaINT::getStr('acao');

    SessaoSEI::getInstance()->validarPermissao($strAcao);

    if ($strAcao !== 'md_pet_pi_peticao_confirmar') {
        throw new InfraException("Ação '" . $strAcao . "' não reconhecida.");
    }

    $strTitulo = 'Confirmação do Peticionamento Interno';
    $strLinkOrientacoesPosPet = '';
    $bolTemOrientacoesPosPet = false;
    $bolAssinaPrincipal = false;
    $bolPodeAssinar = false;
    $strItensSelCargoFuncao = '';
    $strLinkAjaxSenha = '';

    // Texto provisório para o tipo sem Orientações pós Peticionamento, a definir com o negócio (QA-03)
    $strMsgSemOrientacoes = 'Ao confirmar, o processo é aberto no SEI com os documentos informados. Esta operação não pode ser desfeita por esta tela.';

    $objMdPetPiTpProcessoDTO = new MdPetPiTpProcessoDTO();
    $objMdPetPiTpProcessoDTO->retStrOrientacoesPosPet();
    $objMdPetPiTpProcessoDTO->retNumIdConjuntoEstilos();
    $objMdPetPiTpProcessoDTO->setNumIdMdPetPiTpProcesso((int) MdPetPiEntradaINT::getInt('id_md_pet_pi_tp_processo'));

    $arrObjMdPetPiTpProcessoDTO = (new MdPetPiTpProcessoRN())->listarDisponiveis($objMdPetPiTpProcessoDTO);

    if (count($arrObjMdPetPiTpProcessoDTO) === 0) {
        throw new InfraException('Tipo de Processo não disponível para Peticionamento Interno.');
    }

    $numIdMdPetPiTpProcesso = $arrObjMdPetPiTpProcessoDTO[0]->getNumIdMdPetPiTpProcesso();
    $bolTemOrientacoesPosPet = !InfraString::isBolVazia($arrObjMdPetPiTpProcessoDTO[0]->getStrOrientacoesPosPet());

    if (MdPetPiEntradaINT::getStr('iframe') === 'S') {
        $strOrientacoesHtml = $arrObjMdPetPiTpProcessoDTO[0]->getStrOrientacoesPosPet();
        $numIdConjuntoEstilosOrientacoes = $arrObjMdPetPiTpProcessoDTO[0]->getNumIdConjuntoEstilos();
        require_once 'md_pet_pi_peticao_orientacoes.php';
        die;
    }

    $strLinkOrientacoesPosPet = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . $strAcao . '&iframe=S&id_md_pet_pi_tp_processo=' . $numIdMdPetPiTpProcesso);

    $objMdPetPiDocPrincDTO = new MdPetPiDocPrincDTO();
    $objMdPetPiDocPrincDTO->retStrStaAplicabilidade();
    $objMdPetPiDocPrincDTO->setNumIdMdPetPiTpProcesso($numIdMdPetPiTpProcesso);
    $objMdPetPiDocPrincDTO = (new MdPetPiDocPrincRN())->consultar($objMdPetPiDocPrincDTO);

    $bolAssinaPrincipal = $objMdPetPiDocPrincDTO !== null && in_array($objMdPetPiDocPrincDTO->getStrStaAplicabilidade(), [MdPetPiTpProcessoRN::$DOC_GERADO, MdPetPiTpProcessoRN::$DOC_FORMULARIO], true);
    $bolPodeAssinar = SessaoSEI::getInstance()->verificarPermissao('documento_assinar');

    if ($bolAssinaPrincipal && $bolPodeAssinar) {
        $strCargoFuncaoAnterior = (new InfraDadoUsuario(SessaoSEI::getInstance()))->getValor('ASSINATURA_CARGO_FUNCAO_' . SessaoSEI::getInstance()->getNumIdUnidadeAtual());
        $strItensSelCargoFuncao = AssinanteINT::montarSelectCargoFuncaoUnidadeUsuarioRI1344('null', '&nbsp;', $strCargoFuncaoAnterior, SessaoSEI::getInstance()->getNumIdUsuario());
    }

    if (!$bolAssinaPrincipal || $bolPodeAssinar) {
        $strLinkAjaxSenha = SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=md_pet_pi_peticao_senha_validar');
    }

    $arrComandos = [];
    if (!$bolAssinaPrincipal || $bolPodeAssinar) {
        $arrComandos[] = '<button type="button" accesskey="c" name="btnConfirmar" id="btnConfirmar" value="Confirmar Peticionamento" onclick="confirmarPeticionamento();" class="infraButton"><span class="infraTeclaAtalho">C</span>onfirmar Peticionamento</button>';
    }
    $arrComandos[] = '<button type="button" accesskey="a" name="btnCancelar" id="btnCancelar" value="Cancelar" onclick="cancelarPeticionamento();" class="infraButton">C<span class="infraTeclaAtalho">a</span>ncelar</button>';

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
require_once 'md_pet_pi_peticao_confirmacao_css.php';
PaginaSEI::getInstance()->fecharStyle();
PaginaSEI::getInstance()->montarJavaScript();
PaginaSEI::getInstance()->abrirJavaScript();
require_once 'md_pet_pi_peticao_confirmacao_js.php';
PaginaSEI::getInstance()->fecharJavaScript();
PaginaSEI::getInstance()->fecharHead();
PaginaSEI::getInstance()->abrirBody($strTitulo, 'onload="inicializarConfirmacao();"');
PaginaSEI::getInstance()->abrirAreaDados('auto');
?>

<div class="row">
    <div class="col-12">
        <?php if ($bolTemOrientacoesPosPet) { ?>
            <iframe id="ifrOrientacoesPosPet" name="ifrOrientacoesPosPet" frameborder="0" marginheight="0" marginwidth="0"
                    onload="redimensionarOrientacoes(this);"
                    src="<?= PaginaSEI::getInstance()->formatarXHTML($strLinkOrientacoesPosPet) ?>"></iframe>
        <?php } else { ?>
            <p id="pSemOrientacoes" class="text-justify"><?= PaginaSEI::tratarHTML($strMsgSemOrientacoes) ?></p>
        <?php } ?>
    </div>
</div>

<?php if (isset($bolAssinaPrincipal, $bolPodeAssinar) && (!$bolAssinaPrincipal || $bolPodeAssinar)) { ?>
    <form id="frmMdPetPiPeticaoConfirmacao" onsubmit="return false;">
    <div id="divAssinatura" class="row mb-3">
        <div class="col-12">
            <?php if ($bolAssinaPrincipal) { ?>
                <p id="pAssinatura">Ao confirmar, o Documento Principal será assinado eletronicamente com a sua senha.</p>
            <?php } else { ?>
                <p id="pConfirmacao">Ao confirmar, o processo vai ser gerado com o Documento Principal e demais documentos carregados.</p>
            <?php } ?>
        </div>
        <?php if ($bolAssinaPrincipal) { ?>
        <div class="col-sm-12 col-md-6">
            <label id="lblCargoFuncao" for="selCargoFuncao" class="infraLabelObrigatorio">Cargo/Função:</label>
            <select id="selCargoFuncao" name="selCargoFuncao" class="infraSelect form-select" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>">
                <?= $strItensSelCargoFuncao ?>
            </select>
        </div>
        <?php } ?>
        <div class="col-sm-12 col-md-6">
            <label id="lblSenha" for="pwdSenha" class="infraLabelObrigatorio">Senha:</label>
            <?= InfraINT::montarInputPassword('pwdSenha', '', 'tabindex="' . PaginaSEI::getInstance()->getProxTabDados() . '"') ?>
        </div>
    </div>
    </form>
<?php } elseif (!empty($bolAssinaPrincipal)) { ?>
    <div id="divAssinatura" class="row mb-3">
        <div class="col-12">
            <p id="pSemPermissaoAssinatura">O Documento Principal deste Tipo de Processo é assinado na confirmação, e o seu perfil não permite assinar documentos. Procure o gestor de perfis do SEI da sua unidade.</p>
        </div>
    </div>
<?php } ?>

<?php PaginaSEI::getInstance()->fecharAreaDados(); ?>

<div id="divComandosConfirmacao">
    <?php PaginaSEI::getInstance()->montarBarraComandosSuperior($arrComandos); ?>
</div>

<?php
PaginaSEI::getInstance()->fecharBody();
PaginaSEI::getInstance()->fecharHtml();
