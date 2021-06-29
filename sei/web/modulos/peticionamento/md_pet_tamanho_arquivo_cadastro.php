<?
/**
 * ANATEL
 *
 * 15/02/2016 - criado por jaqueline.mendes@cast.com.br - CAST
 *
 */

try {
    require_once dirname(__FILE__) . '/../../SEI.php';

    session_start();

    SessaoSEI::getInstance()->validarLink();

    PaginaSEI::getInstance()->verificarSelecao('md_pet_tamanho_arquivo_cadastrar');

    $objMdPetTamanhoArquivoDTO = new MdPetTamanhoArquivoDTO();
    $strDesabilitar = '';

    $arrComandos = array();

    switch ($_GET['acao']) {
        case 'md_pet_tamanho_arquivo_cadastrar':
            $strTitulo = 'Peticionamento - Tamanho Máximo de Arquivos';
            $arrComandos[] = '<button type="submit" accesskey="S" name="sbmCadastrarTamanhoArquivo" id="sbmCadastrarTamanhoArquivo" value="Salvar" class="infraButton"><span class="infraTeclaAtalho">S</span>alvar</button>';

            $objMdPetTamanhoArquivoRN = new MdPetTamanhoArquivoRN();
            $objMdPetTamanhoArquivoDTO->setNumIdTamanhoArquivo(MdPetTamanhoArquivoRN::$ID_FIXO_TAMANHO_ARQUIVO);
            $objMdPetTamanhoArquivoDTO->retTodos();
            $objMdPetTamanhoArquivoDTO = $objMdPetTamanhoArquivoRN->consultar($objMdPetTamanhoArquivoDTO);
            if (isset($_POST['sbmCadastrarTamanhoArquivo'])) {
                try {

                    $cadastrar = is_null($objMdPetTamanhoArquivoDTO) ? true : false;

                    $objMdPetTamanhoArquivoDTO = new MdPetTamanhoArquivoDTO();
                    $objMdPetTamanhoArquivoDTO->retTodos();

                    $objMdPetTamanhoArquivoDTO->setNumValorDocPrincipal($_POST['txtValorDocPrincipal']);
                    $objMdPetTamanhoArquivoDTO->setNumValorDocComplementar($_POST['txtValorDocComplementar']);
                    $objMdPetTamanhoArquivoDTO->setNumIdTamanhoArquivo(MdPetTamanhoArquivoRN::$ID_FIXO_TAMANHO_ARQUIVO);
                    $objMdPetTamanhoArquivoDTO->setStrSinAtivo('S');

                    if ($cadastrar) {
                        $objMdPetTamanhoArquivoDTO = $objMdPetTamanhoArquivoRN->cadastrar($objMdPetTamanhoArquivoDTO);
                    } else {
                        $objMdPetTamanhoArquivoDTO = $objMdPetTamanhoArquivoRN->alterar($objMdPetTamanhoArquivoDTO);
                    }
                    PaginaSEI::getInstance()->adicionarMensagem('Os dados cadastrados foram salvos com sucesso.');
                    header('Location: ' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . $_GET['acao'] . PaginaSEI::getInstance()->montarAncora(MdPetTamanhoArquivoRN::$ID_FIXO_TAMANHO_ARQUIVO)));
                    die;
                } catch (Exception $e) {
                    PaginaSEI::getInstance()->processarExcecao($e);
                }
            }

            break;

        default:
            throw new InfraException("Ação '" . $_GET['acao'] . "' não reconhecida.");
    }


} catch (Exception $e) {
    PaginaSEI::getInstance()->processarExcecao($e);
}

$arrComandos[] = '<button type="button" accesskey="c" name="btnFechar" id="btnFechar" value="Fechar" onclick="location.href=\'' . PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?acao=procedimento_controlar&acao_origem=' . $_GET['acao'])) . '\';" class="infraButton">Fe<span class="infraTeclaAtalho">c</span>har</button>';

PaginaSEI::getInstance()->montarDocType();
PaginaSEI::getInstance()->abrirHtml();
PaginaSEI::getInstance()->abrirHead();
PaginaSEI::getInstance()->montarMeta();
PaginaSEI::getInstance()->montarTitle(':: ' . PaginaSEI::getInstance()->getStrNomeSistema() . ' - ' . $strTitulo . ' ::');
PaginaSEI::getInstance()->montarStyle();
PaginaSEI::getInstance()->abrirStyle();
require_once 'md_pet_tamanho_arquivo_cadastro_css.php';
PaginaSEI::getInstance()->fecharStyle();
PaginaSEI::getInstance()->montarJavaScript();
PaginaSEI::getInstance()->abrirJavaScript();
PaginaSEI::getInstance()->fecharJavaScript();
PaginaSEI::getInstance()->fecharHead();
PaginaSEI::getInstance()->abrirBody($strTitulo, 'onload="inicializar();"');
?>
    <form id="frmCadastroTamanhoArquivo" method="post" onsubmit="return OnSubmitForm();"
          action="<?= PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . $_GET['acao'])) ?>">
        <?
        PaginaSEI::getInstance()->montarBarraComandosSuperior($arrComandos);
        PaginaSEI::getInstance()->abrirAreaDados('30em');
        ?>
        <div id="divGeral" class="infraAreaDados">
            <div class="rowFieldSet">
                <div class="col-sm-12 col-md-10 col-lg-10 col-xl-10">
                    <fieldset id="fieldsetTamanhoArquivo"
                              class="infraFieldset fieldsetTamanhoMaximoArquivo form-control">
                        <legend class="infraLegend">&nbsp;Limite em Mb para carregamento de Arquivos&nbsp;</legend>

                        <div class="col-sm-10 col-md-10 col-lg-8 col-xl-6" id="divValorDocPrincipal">

                            <label id="lblValorDocPrincipal" for="txtValorDocPrincipal"
                                   class="infraLabelObrigatorio">Documento
                                Principal (Processo Novo): <img align="top"
                                                                src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/ajuda.svg"
                                                                name="ajuda" <?= PaginaSEI::montarTitleTooltip('Limita o tamanho máximo de Arquivos em Mb no Peticionamento de Processo Novo somente do Documento Principal, que geralmente é de tamanho menor que os demais documentos, pois tende a ser Nato Digital.', 'Ajuda') ?>
                                                                class="infraImgModulo"/></label>
                            <input type="text" id="txtValorDocPrincipal" name="txtValorDocPrincipal"
                                   class="infraText form-control"
                                   value="<?php echo isset($objMdPetTamanhoArquivoDTO) ? PaginaSEI::tratarHTML($objMdPetTamanhoArquivoDTO->getNumValorDocPrincipal()) : '' ?>"
                                   onkeypress="return validarCampo(this, event, 11)" maxlength="11"
                                   tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
                        </div>


                        <div class="col-sm-10 col-md-10 col-lg-8 col-xl-6" id="divValorDocComplementar">
                            <label id="lblValorDocComplementar" for="txtValorDocComplementar"
                                   class="infraLabelObrigatorio">Demais Documentos: <img align="top"
                                                                                         src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/ajuda.svg"
                                                                                         name="ajuda" <?= PaginaSEI::montarTitleTooltip('Limita o tamanho máximo de Arquivos em Mb no Peticionamento de Processo Novo especificamente sobre os Documentos Essenciais e Complementares, no Peticionamento Intercorrente, no Peticionamento de Resposta a Intimação e no Peticionamento de Responsável Legal de Pessoa Jurídica.', 'Ajuda') ?>
                                                                                         class="infraImgModulo"/></label>
                            <input type="text" id="txtValorDocComplementar" name="txtValorDocComplementar"
                                   class="infraText form-control"
                                   value="<?php echo isset($objMdPetTamanhoArquivoDTO) ? PaginaSEI::tratarHTML($objMdPetTamanhoArquivoDTO->getNumValorDocComplementar()) : '' ?>"
                                   onkeypress="return validarCampo(this, event, 11);"
                                   onkeydown="somenteNumeros(event)"
                                   maxlength="11" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
                        </div>
                        <div class="clear">&nbsp;</div>
                    </fieldset>
                </div>
            </div>
        </div>
        <input type="hidden" id="hdnIdTamanhoArquivoPeticionamento" name="hdnIdTamanhoArquivoPeticionamento"
               value="<?= $_GET['id_tamanho_arquivo_peticionamento']; ?>"/>
        <?
        PaginaSEI::getInstance()->fecharAreaDados();
        ?>
    </form>
<?
PaginaSEI::getInstance()->fecharBody();
PaginaSEI::getInstance()->fecharHtml();
require_once 'md_pet_tamanho_arquivo_cadastro_js.php';
?>