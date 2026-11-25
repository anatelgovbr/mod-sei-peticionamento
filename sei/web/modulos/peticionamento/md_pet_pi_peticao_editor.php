<?php
/**
 * ANATEL
 *
 * Editor do Documento Principal Gerado do Peticionamento Interno, aberto em outra janela, na versao que o core define
 * para a unidade. No CKEditor 5, editor completo como o do documento no core (editor/ck5_processar.php), com o Salvar
 * na barra do editor. No CKEditor 4, mesma tela do editor do Peticionamento Externo
 * (md_pet_editor_usuario_externo_processar.php). O conteudo fica na sessao ate a conclusao do peticionamento.
 *
 * 17/09/2026 - criado por emerson_b.goncalves - ANATEL
 */
try {
    require_once dirname(__FILE__) . '/../../SEI.php';

    session_start();

    PaginaSEI::getInstance()->setBolXHTML(false);
    PaginaSEI::getInstance()->setTipoPagina(InfraPagina::$TIPO_PAGINA_SIMPLES);

    SessaoSEI::getInstance()->validarLink();

    $strAcao = MdPetPiEntradaINT::getStr('acao');

    SessaoSEI::getInstance()->validarPermissao($strAcao);

    if ($strAcao !== 'md_pet_pi_peticao_editor_montar') {
        throw new InfraException("Ação '" . $strAcao . "' não reconhecida.");
    }

    $strTitulo = 'Documento Principal';
    $strConteudo = '';
    $strValidacao = '';
    $bolConteudoSalvo = false;

    $objMdPetPiDocPrincDTO = MdPetPiPeticaoINT::consultarDocPrincipalDisponivel(
        (int) MdPetPiEntradaINT::getInt('id_md_pet_pi_tp_processo'),
        MdPetPiTpProcessoRN::$DOC_GERADO
    );

    $strTitulo = $objMdPetPiDocPrincDTO->getStrNomeSerie();

    // Mesma regra que o core usa para o editor do documento gerado no processo
    $staTipoEditor = EditorRN::obterTipoEditorUnidade();

    if ($staTipoEditor === EditorRN::$VE_CK5) {

        try {
            $objEditorDTO = MdPetPiPeticaoINT::montarEditorCompletoDocPrincipal($objMdPetPiDocPrincDTO);
        } catch (Exception $e) {
            if (!$e instanceof InfraException || !$e->contemValidacoes()) {
                throw $e;
            }

            $objEditorDTO = new EditorDTO();
            $objEditorDTO->setStrCss('');
            $objEditorDTO->setStrJs('');
            $objEditorDTO->setStrHtml('');
            $strValidacao = '<div style="border-radius:.25rem;padding:1rem;margin:1rem;background-color:#f8d7da;">' . str_replace(['\n', "\n"], '<br>', PaginaSEI::tratarHTML($e->__toString())) . '</div>';
        }

    } else {

        // O formulário só é enviado pelo botão Salvar
        if (MdPetPiEntradaINT::postStr('hdnSalvar') === 'S') {

            $strConteudo = MdPetPiEntradaINT::postStr('txaConteudo');

            try {
                $objInfraException = new InfraException();

                if (InfraString::isBolVazia($strConteudo)) {
                    $objInfraException->adicionarValidacao('Conteúdo do documento não informado.');
                }

                $objInfraException->lancarValidacoes();

                EditorRN::validarTagsCriticas(EditorINT::getArrImagensPermitidas(), $strConteudo);

                PeticionamentoIntegracao::validarXssEditor($strConteudo);

                SessaoSEI::getInstance()->setAtributo(MdPetPiPeticaoINT::obterAtributoConteudoDocPrincipal($objMdPetPiDocPrincDTO->getNumIdMdPetPiTpProcesso()), $strConteudo);

                $bolConteudoSalvo = true;

            } catch (Exception $e) {
                PaginaSEI::getInstance()->processarExcecao($e);
            }

        } elseif (SessaoSEI::getInstance()->isSetAtributo(MdPetPiPeticaoINT::obterAtributoConteudoDocPrincipal($objMdPetPiDocPrincDTO->getNumIdMdPetPiTpProcesso()))) {
            $strConteudo = MdPetPiPeticaoINT::obterConteudoDocPrincipalSessao($objMdPetPiDocPrincDTO->getNumIdMdPetPiTpProcesso());
        } else {
            $strConteudo = MdPetPiPeticaoINT::obterConteudoInicialDocPrincipal($objMdPetPiDocPrincDTO);
        }

        $objEditorDTO = MdPetPiPeticaoINT::montarEditorDocumentoPrincipal($objMdPetPiDocPrincDTO->getNumIdSerie(), 'txaConteudo');
    }

} catch (Exception $e) {
    PaginaSEI::getInstance()->processarExcecao($e);
}

if ($staTipoEditor === EditorRN::$VE_CK5) {
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="iso-8859-1">
    <title><?= PaginaSEI::tratarHTML(':: ' . PaginaSEI::getInstance()->getStrNomeSistema() . ' - ' . $strTitulo . ' ::') ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <style>
<?php require_once 'md_pet_pi_peticao_editor_css.php'; ?>
    </style>
<?php require_once 'md_pet_pi_peticao_editor_saida.php'; ?>
    <script type="text/javascript">
<?php require_once 'md_pet_pi_peticao_editor_js.php'; ?>
    </script>
</head>
<body>
<?php
echo $strValidacao;
echo $objEditorDTO->getStrHtml();
PaginaSEI::getInstance()->montarAreaDebug();
?>
</body>
</html>
<?php
    return;
}

PaginaSEI::getInstance()->montarDocType();
PaginaSEI::getInstance()->abrirHtml();
PaginaSEI::getInstance()->abrirHead();
PaginaSEI::getInstance()->montarMeta();
PaginaSEI::getInstance()->montarTitle(':: ' . PaginaSEI::getInstance()->getStrNomeSistema() . ' - ' . $strTitulo . ' ::');
PaginaSEI::getInstance()->montarStyle();
EditorINT::montarCss();
PaginaSEI::getInstance()->abrirStyle();
require_once 'md_pet_pi_peticao_editor_css.php';
PaginaSEI::getInstance()->fecharStyle();
PaginaSEI::getInstance()->montarJavaScript();
PaginaSEI::getInstance()->abrirJavaScript();
require_once 'md_pet_pi_peticao_editor_js.php';
PaginaSEI::getInstance()->fecharJavaScript();

require_once 'md_pet_pi_peticao_editor_saida.php';

PaginaSEI::getInstance()->fecharHead();
?>
<body onload="inicializar();" style="margin: 5px;">
<form id="frmMdPetPiPeticaoEditor" method="post"
      action="<?= PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . $strAcao . '&acao_origem=' . $strAcao . '&id_md_pet_pi_tp_processo=' . $objMdPetPiDocPrincDTO->getNumIdMdPetPiTpProcesso())) ?>">

    <div id="divComandos"></div>

    <div id="divEditores">
        <textarea id="txaConteudo" name="txaConteudo" rows="10" class="infraTextarea"
                  tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"><?= PaginaSEI::tratarHTML($strConteudo) ?></textarea>
        <script type="text/javascript"><?= $objEditorDTO->getStrEditores() ?></script>
    </div>

    <input type="hidden" id="hdnSalvar" name="hdnSalvar" value="S"/>

</form>
<?php
PaginaSEI::getInstance()->fecharBody();
PaginaSEI::getInstance()->fecharHtml();
