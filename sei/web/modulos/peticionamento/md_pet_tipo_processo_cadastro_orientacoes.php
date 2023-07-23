<?php

try {

    require_once dirname(__FILE__).'/../../SEI.php';

    session_start();

    PaginaSEI::getInstance()->setBolXHTML(false);
    //////////////////////////////////////////////////////////////////////////////
    // InfraDebug::getInstance()->setBolLigado(false);
    // InfraDebug::getInstance()->setBolDebugInfra(true);
    // InfraDebug::getInstance()->limpar();
    //////////////////////////////////////////////////////////////////////////////
    SessaoSEI::getInstance()->validarLink();
    PaginaSEI::getInstance()->verificarSelecao('tipo_processo_peticionamento_selecionar_orientacoes');
    SessaoSEI::getInstance()->validarPermissao($_GET['acao']);

    $arrComandos = array();

    switch($_GET['acao']){

        case 'md_pet_tipo_processo_cadastrar_orientacoes':

            $strTitulo      = 'Configurações Gerais';
            $arrComandos[]  = '<button type="submit" accesskey="S" name="sbmCadastrarOrientacoesPetIndisp" value="Salvar" class="infraButton"><span class="infraTeclaAtalho">S</span>alvar</button>';
            $arrComandos[]  = '<button type="button" accesskey="C" name="btnCancelar" id="btnCancelar" value="Cancelar" onclick="location.href=\''.PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?acao='.PaginaSEI::getInstance()->getAcaoRetorno().'&acao_origem='.$_GET['acao'])).'\';" class="infraButton"><span class="infraTeclaAtalho">C</span>ancelar</button>';

            $objEditorDTO=new EditorDTO();
            $objEditorDTO->setStrNomeCampo('txaConteudo');
            $objEditorDTO->setStrSinSomenteLeitura('N');
            $retEditor = (new EditorRN())->montarSimples($objEditorDTO);

            $objMdPetTpProcessoOrientacoesDTO2 = new MdPetTpProcessoOrientacoesDTO();
            $objMdPetTpProcessoOrientacoesDTO2->setNumIdTipoProcessoOrientacoesPet(MdPetTpProcessoOrientacoesRN::$ID_FIXO_TP_PROCESSO_ORIENTACOES);
            $objMdPetTpProcessoOrientacoesDTO2->retTodos();
            $objLista = (new MdPetTpProcessoOrientacoesRN())->listar($objMdPetTpProcessoOrientacoesDTO2);

            $alterar        = count($objLista) > 0;
            $txtConteudo    = $alterar ? $objLista[0]->getStrOrientacoesGerais() : '';

            $objMdPetTpProcessoOrientacoesDTO = new MdPetTpProcessoOrientacoesDTO();
            $objMdPetTpProcessoOrientacoesDTO->setStrOrientacoesGerais($_POST['txaConteudo']);
            $objMdPetTpProcessoOrientacoesDTO->setNumIdTipoProcessoOrientacoesPet(MdPetTpProcessoOrientacoesRN::$ID_FIXO_TP_PROCESSO_ORIENTACOES);

            if (isset($_POST['sbmCadastrarOrientacoesPetIndisp'])) {

                try{

                    $objInfraException = new InfraException();
                    $strConteudoSalvar = trim($_POST['txaConteudo']);

                    if($strConteudoSalvar != '') {

                        (new EditorRN())->validarTagsCriticas(array('jpg','png'), $_POST['txaConteudo']);
                        $objMdPetTpProcessoOrientacoesDTO2->setStrOrientacoesGerais($_POST['txaConteudo']);

                        //Estilo
                        $conjuntoEstilosDTO = new ConjuntoEstilosDTO();
                        $conjuntoEstilosDTO->setStrSinUltimo('S');
                        $conjuntoEstilosDTO->retNumIdConjuntoEstilos();
                        $conjuntoEstilosDTO = (new ConjuntoEstilosRN())->consultar( $conjuntoEstilosDTO );

                        $objMdPetTpProcessoOrientacoesDTO2->setNumIdConjuntoEstilos( $conjuntoEstilosDTO->getNumIdConjuntoEstilos() );

                        $objMdPetTpProcessoOrientacoesDTO =  $alterar ? (new MdPetTpProcessoOrientacoesRN())->alterar($objMdPetTpProcessoOrientacoesDTO2) : (new MdPetTpProcessoOrientacoesRN())->cadastrar($objMdPetTpProcessoOrientacoesDTO);

                    }else{

                        $objInfraException->lancarValidacao('Informe o campo Conteúdo!');

                    }

                    MdPetForcarNivelAcessoDocINT::forcarNivelAcessoDocumento($tipoPeticionamento = 'N');

                    header('Location: '.SessaoSEI::getInstance()->assinarLink('controlador.php?acao='.PaginaSEI::getInstance()->getAcaoRetorno().'&acao_origem='.$_GET['acao']));
                    die;

                }catch(Exception $e){
                    PaginaSEI::getInstance()->processarExcecao($e);
                }
            }

        break;

        default: throw new InfraException("Ação '".$_GET['acao']."' não reconhecida.");

    }

}catch(Exception $e){
    PaginaSEI::getInstance()->processarExcecao($e);
}

PaginaSEI::getInstance()->montarDocType();
PaginaSEI::getInstance()->abrirHtml();
PaginaSEI::getInstance()->abrirHead();
PaginaSEI::getInstance()->montarMeta();
PaginaSEI::getInstance()->montarTitle(':: '.PaginaSEI::getInstance()->getStrNomeSistema().' - '.$strTitulo.' ::');
PaginaSEI::getInstance()->montarStyle();
EditorINT::montarCss();
PaginaSEI::getInstance()->abrirStyle();
require_once 'md_pet_tipo_processo_cadastro_orientacoes_css.php';
PaginaSEI::getInstance()->fecharStyle();
PaginaSEI::getInstance()->montarJavaScript();
PaginaSEI::getInstance()->abrirJavaScript();
PaginaSEI::getInstance()->fecharJavaScript();
echo $retEditor->getStrInicializacao();
PaginaSEI::getInstance()->fecharHead();
PaginaSEI::getInstance()->abrirBody($strTitulo,'onload="inicializar();"');

?>

    <form id="frmTextoPadraoInternoCadastro" method="post" onsubmit="return onSubmitForm();" action="<?=PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?acao='.$_GET['acao'].'&acao_origem='.$_GET['acao']))?>">

        <? PaginaSEI::getInstance()->montarBarraComandosSuperior($arrComandos); ?>
        <? PaginaSEI::getInstance()->abrirAreaDados(''); ?>
        <? PaginaSEI::getInstance()->fecharAreaDados(); ?>

        <div class="row">
            <div class="col-12">
                <div class="row">
                    <div class="col-12">
                        <div class="form-group">
                            <label id="lblConteudo" for="txaConteudo" accesskey="" class="infraLabelObrigatorio">Orientações Gerais:</label>
                            <div id="divEditores" class="mb-4">
                                <textarea id="txaConteudo" name="txaConteudo" rows="10" class="infraTextarea" tabindex="<?=PaginaSEI::getInstance()->getProxTabDados()?>"><?=PaginaSEI::tratarHTML($txtConteudo)?></textarea>
                                <script type="text/javascript"> <?= $retEditor->getStrEditores(); ?> </script>
                            </div>
                        </div>
                    </div>
                </div>

                <?php $staTipoPeticinamento='N'; require_once 'md_pet_forcar_nivel_acesso_doc_bloco.php'; ?>

            </div>
        </div>

        <? PaginaSEI::getInstance()->montarBarraComandosInferior($arrComandos, true); ?>

    </form>

    <?php require_once 'md_pet_tipo_processo_cadastro_orientacoes_js.php'; ?>
    <?php require_once 'md_pet_forcar_nivel_acesso_doc_bloco_js.php'; ?>

<? PaginaSEI::getInstance()->fecharBody(); ?>
<? PaginaSEI::getInstance()->fecharHtml(); ?>