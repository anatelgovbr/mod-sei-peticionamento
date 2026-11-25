<?php
/**
 * ANATEL
 *
 * Peticionamento Interno: Disponibilizar e Remover acesso: o autor de um processo de Interesse Pessoal libera e remove o acesso
 * de usuários da unidade de abertura aos documentos Restritos peticionados.
 *
 * 25/09/2026 - criado por emerson_b.goncalves - ANATEL
 */
try {
    require_once dirname(__FILE__) . '/../../SEI.php';

    session_start();

    SessaoSEI::getInstance()->validarLink();

    $strAcao = MdPetPiEntradaINT::getStr('acao');

    SessaoSEI::getInstance()->validarPermissao($strAcao);

    if (MdPetPiEntradaINT::getStr('arvore') === '1') {
        PaginaSEI::getInstance()->setBolArvore(true);
    }

    $strTitulo = 'Peticionamento Interno: Disponibilizar e Remover acesso';
    $strResultado = '';
    $numRegistros = 0;
    $strItensSelUsuario = '';
    $strLinkCadastrar = '';
    $strLinkExcluir = '';
    $arrComandos = [];

    $dblIdProcedimento = MdPetPiEntradaINT::getInt('id_procedimento');
    $strParametros = '&id_procedimento=' . $dblIdProcedimento . '&arvore=1';
    $strLinkListar = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_pet_pi_rel_peticao_usu_listar&acao_origem=' . $strAcao . $strParametros);

    $objMdPetPiPeticaoDTO = new MdPetPiPeticaoDTO();
    $objMdPetPiPeticaoDTO->retNumIdMdPetPiPeticao();
    $objMdPetPiPeticaoDTO->retNumIdUnidade();
    $objMdPetPiPeticaoDTO->setDblIdProcedimento($dblIdProcedimento);
    $objMdPetPiPeticaoDTO->setStrStaIndicacaoInteressado(MdPetPiTpProcessoRN::$II_INTERESSE_PESSOAL);
    $objMdPetPiPeticaoDTO->setNumIdUsuario(SessaoSEI::getInstance()->getNumIdUsuario());
    $objMdPetPiPeticaoDTO->setNumIdUnidade(SessaoSEI::getInstance()->getNumIdUnidadeAtual());

    $objMdPetPiPeticaoDTO = $dblIdProcedimento === null ? null : (new MdPetPiPeticaoRN())->consultar($objMdPetPiPeticaoDTO);

    if ($objMdPetPiPeticaoDTO === null) {
        throw new InfraException('Somente quem peticionou, na unidade de abertura, controla o acesso aos documentos restritos deste processo.');
    }

    $numIdMdPetPiPeticao = $objMdPetPiPeticaoDTO->getNumIdMdPetPiPeticao();

    switch ($strAcao) {
        case 'md_pet_pi_rel_peticao_usu_cadastrar':
            try {
                $objMdPetPiRelPeticaoUsuDTO = new MdPetPiRelPeticaoUsuDTO();
                $objMdPetPiRelPeticaoUsuDTO->setNumIdMdPetPiPeticao($numIdMdPetPiPeticao);
                $objMdPetPiRelPeticaoUsuDTO->setNumIdUsuario(MdPetPiEntradaINT::postInt('selUsuario'));

                (new MdPetPiRelPeticaoUsuRN())->cadastrar($objMdPetPiRelPeticaoUsuDTO);

                PaginaSEI::getInstance()->adicionarMensagem('Acesso liberado.');
            } catch (Exception $e) {
                PaginaSEI::getInstance()->processarExcecao($e);
            }

            header('Location: ' . $strLinkListar);
            die;

        case 'md_pet_pi_rel_peticao_usu_excluir':
            try {
                $arrObjMdPetPiRelPeticaoUsuDTO = [];

                foreach (PaginaSEI::getInstance()->getArrStrItensSelecionados() as $strIdUsuario) {
                    $objMdPetPiRelPeticaoUsuDTO = new MdPetPiRelPeticaoUsuDTO();
                    $objMdPetPiRelPeticaoUsuDTO->setNumIdMdPetPiPeticao($numIdMdPetPiPeticao);
                    $objMdPetPiRelPeticaoUsuDTO->setNumIdUsuario((int) $strIdUsuario);
                    $arrObjMdPetPiRelPeticaoUsuDTO[] = $objMdPetPiRelPeticaoUsuDTO;
                }

                (new MdPetPiRelPeticaoUsuRN())->excluirLiberacao($arrObjMdPetPiRelPeticaoUsuDTO);

                PaginaSEI::getInstance()->adicionarMensagem('Acesso removido.');
            } catch (Exception $e) {
                PaginaSEI::getInstance()->processarExcecao($e);
            }

            header('Location: ' . $strLinkListar);
            die;

        case 'md_pet_pi_rel_peticao_usu_listar':
            break;

        default:
            throw new InfraException("Ação '" . $strAcao . "' não reconhecida.");
    }

    $bolAcaoCadastrar = SessaoSEI::getInstance()->verificarPermissao('md_pet_pi_rel_peticao_usu_cadastrar');
    $bolAcaoExcluir = SessaoSEI::getInstance()->verificarPermissao('md_pet_pi_rel_peticao_usu_excluir');

    if ($bolAcaoCadastrar) {
        $strLinkCadastrar = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_pet_pi_rel_peticao_usu_cadastrar&acao_origem=' . $strAcao . $strParametros);
    }

    if ($bolAcaoExcluir) {
        $strLinkExcluir = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_pet_pi_rel_peticao_usu_excluir&acao_origem=' . $strAcao . $strParametros);
    }

    $objMdPetPiRelPeticaoUsuDTO = new MdPetPiRelPeticaoUsuDTO();
    $objMdPetPiRelPeticaoUsuDTO->retNumIdUsuario();
    $objMdPetPiRelPeticaoUsuDTO->retStrSiglaUsuario();
    $objMdPetPiRelPeticaoUsuDTO->retStrNomeUsuario();
    $objMdPetPiRelPeticaoUsuDTO->setNumIdMdPetPiPeticao($numIdMdPetPiPeticao);
    $objMdPetPiRelPeticaoUsuDTO->setOrdStrSiglaUsuario(InfraDTO::$TIPO_ORDENACAO_ASC);

    $arrObjMdPetPiRelPeticaoUsuDTO = (new MdPetPiRelPeticaoUsuRN())->listar($objMdPetPiRelPeticaoUsuDTO);

    $arrIdUsuarioComAcesso = InfraArray::converterArrInfraDTO($arrObjMdPetPiRelPeticaoUsuDTO, 'IdUsuario');
    $arrIdUsuarioComAcesso[] = SessaoSEI::getInstance()->getNumIdUsuario();

    if ($bolAcaoCadastrar) {
        $strItensSelUsuario = MdPetPiPeticaoINT::montarSelectUsuarioLiberacao($objMdPetPiPeticaoDTO->getNumIdUnidade(), $arrIdUsuarioComAcesso);
    }

    $numRegistros = count($arrObjMdPetPiRelPeticaoUsuDTO) + 1;

    $strResultado .= '<table width="99%" class="infraTable" summary="Tabela de Usuários com acesso aos documentos restritos">' . "\n";
    $strResultado .= '<caption class="infraCaption">' . PaginaSEI::getInstance()->gerarCaptionTabela('Usuários com Acesso', $numRegistros) . '</caption>';
    $strResultado .= '<tr>';

    if ($bolAcaoExcluir) {
        $strResultado .= '<th class="infraTh" width="1%">' . PaginaSEI::getInstance()->getThCheck() . '</th>';
    }

    $strResultado .= '<th class="infraTh thLeft">Usuário</th>';
    $strResultado .= '<th class="infraTh" width="10%">Ações</th>';
    $strResultado .= '</tr>' . "\n";

    $strResultado .= '<tr class="infraTrAcessada">';

    if ($bolAcaoExcluir) {
        $strResultado .= '<td></td>';
    }

    $strResultado .= '<td>' . PaginaSEI::tratarHTML(UsuarioINT::formatarSiglaNome(SessaoSEI::getInstance()->getStrSiglaUsuario(), SessaoSEI::getInstance()->getStrNomeUsuario())) . '</td>';
    $strResultado .= '<td align="center"><span class="spnAutorPeticionamento" ' . PaginaSEI::montarTitleTooltip('Usuário que abriu o processo pelo Peticionamento Interno. Sempre tem acesso e não pode ser removido.', 'Autor do Peticionamento') . '>&#9733;</span></td>';
    $strResultado .= '</tr>' . "\n";

    $strCssTr = '<tr class="infraTrClara">';

    foreach ($arrObjMdPetPiRelPeticaoUsuDTO as $i => $objDTO) {
        $strCssTr = ($strCssTr === '<tr class="infraTrClara">') ? '<tr class="infraTrEscura">' : '<tr class="infraTrClara">';
        $strDescricao = UsuarioINT::formatarSiglaNome($objDTO->getStrSiglaUsuario(), $objDTO->getStrNomeUsuario());

        $strResultado .= $strCssTr;

        if ($bolAcaoExcluir) {
            $strResultado .= '<td valign="middle">' . PaginaSEI::getInstance()->getTrCheck($i, $objDTO->getNumIdUsuario(), $strDescricao) . '</td>';
        }

        $strResultado .= '<td>' . PaginaSEI::tratarHTML($strDescricao) . '</td>';
        $strResultado .= '<td align="center">';

        if ($bolAcaoExcluir) {
            $strResultado .= '<a href="' . PaginaSEI::getInstance()->montarAncora($objDTO->getNumIdUsuario()) . '" onclick="acaoExcluir(\'' . $objDTO->getNumIdUsuario() . '\',\'' . PaginaSEI::tratarHTML(PaginaSEI::formatarParametrosJavaScript($strDescricao, false)) . '\');" tabindex="' . PaginaSEI::getInstance()->getProxTabTabela() . '"><img src="' . PaginaSEI::getInstance()->getDiretorioSvgGlobal() . '/excluir.svg?' . Icone::VERSAO . '" title="Remover Acesso" alt="Remover Acesso" class="infraImg" /></a>';
        }

        $strResultado .= '</td>';
        $strResultado .= '</tr>' . "\n";
    }

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
?>
#divLiberarAcesso {margin-bottom: 1.5em;}
#btnAdicionar {margin-top: 1.6em;}
tr.infraTrAcessada span.spnAutorPeticionamento {color: #f2b705; font-size: 1.4em; cursor: default;}
<?php
PaginaSEI::getInstance()->fecharStyle();
PaginaSEI::getInstance()->montarJavaScript();
PaginaSEI::getInstance()->abrirJavaScript();
?>
function inicializar() {
    infraEfeitoTabelas();
}

function validarForm() {
    var selUsuario = document.getElementById('selUsuario');

    if (selUsuario === null || selUsuario.value === '' || selUsuario.value === 'null') {
        alert('Selecione o usuário para liberar o acesso.');
        return false;
    }

    return true;
}

function acaoExcluir(id, desc) {
    if (confirm('Confirma a remoção do acesso de "' + desc + '"?')) {
        document.getElementById('hdnInfraItemId').value = id;
        document.getElementById('frmMdPetPiRelPeticaoUsuLista').action = '<?= $strLinkExcluir ?>';
        document.getElementById('frmMdPetPiRelPeticaoUsuLista').submit();
    }
}
<?php
PaginaSEI::getInstance()->fecharJavaScript();
PaginaSEI::getInstance()->fecharHead();
PaginaSEI::getInstance()->abrirBody($strTitulo, 'onload="inicializar();"');
?>
<form id="frmMdPetPiRelPeticaoUsuLista" method="post" onsubmit="return validarForm();"
      action="<?= PaginaSEI::getInstance()->formatarXHTML($strLinkCadastrar) ?>">

    <?php PaginaSEI::getInstance()->montarBarraComandosSuperior($arrComandos); ?>

    <?php if ($strLinkCadastrar !== '') { ?>
    <div id="divLiberarAcesso" class="row">
        <div class="col-sm-10 col-md-7 col-lg-6 col-xl-5">
            <label id="lblUsuario" for="selUsuario" class="infraLabelObrigatorio">Disponibilizar acesso para:</label>
            <select id="selUsuario" name="selUsuario" class="infraSelect form-select" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>">
                <?= $strItensSelUsuario ?>
            </select>
        </div>
        <div class="col-sm-2 col-md-2">
            <button type="submit" accesskey="A" id="btnAdicionar" name="btnAdicionar" value="Adicionar" class="infraButton" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"><span class="infraTeclaAtalho">A</span>dicionar</button>
        </div>
    </div>
    <?php } ?>

    <?php PaginaSEI::getInstance()->montarAreaTabela($strResultado, $numRegistros); ?>

</form>
<?php
PaginaSEI::getInstance()->fecharBody();
PaginaSEI::getInstance()->fecharHtml();
