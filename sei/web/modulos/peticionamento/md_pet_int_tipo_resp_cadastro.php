<?
/**
 * TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
 *
 * 08/12/2016 - criado por Marcelo Bezerra - CAST
 *
 * Versão do Gerador de Código: 1.39.0
 *
 */

try {

    require_once dirname(__FILE__) . '/../../SEI.php';
    session_start();

    //////////////////////////////////////////////////////////////////////////////
    InfraDebug::getInstance()->setBolLigado(false);
    InfraDebug::getInstance()->setBolDebugInfra(true);
    InfraDebug::getInstance()->limpar();
    //////////////////////////////////////////////////////////////////////////////

    SessaoSEI::getInstance()->validarLink();
    PaginaSEI::getInstance()->verificarSelecao('md_pet_int_tipo_resp_selecionar');
    SessaoSEI::getInstance()->validarPermissao($_GET['acao']);
    $objMdPetIntTipoRespDTO = new MdPetIntTipoRespDTO();
    $strDesabilitar = '';
    $arrComandos = array();

    switch ($_GET['acao']) {

        case 'md_pet_int_tipo_resp_cadastrar':

            $strTitulo = 'Novo Tipo de Resposta';

            $arrComandos[] = '<button type="submit" accesskey="S" name="sbmCadastrarMdPetIntTipoResp" value="Salvar" class="infraButton"><span class="infraTeclaAtalho">S</span>alvar</button>';

            $arrComandos[] = '<button type="button" accesskey="C" name="btnCancelar" id="btnCancelar" value="Cancelar" onclick="location.href=\'' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . PaginaSEI::getInstance()->getAcaoRetorno() . '&acao_origem=' . $_GET['acao']) . '\';" class="infraButton"><span class="infraTeclaAtalho">C</span>ancelar</button>';

            $objMdPetIntTipoRespDTO->setNumIdMdPetIntTipoResp(null);

            $tipoDia = 'C';
            switch ($_POST['rdoPrazo']) {
                case 'N':
                    $valorPrazo = 0;
                    break;
                case 'D':
                    $valorPrazo = $_POST['txtValorPrazoExternoDia'];
                    $tipoDia = $_POST['rdTipoDia'];
                    break;
                case 'M':
                    $valorPrazo = $_POST['txtValorPrazoExternoMes'];
                    break;
                case 'A':
                    $valorPrazo = $_POST['txtValorPrazoExternoAno'];
                    break;
            }

            $objMdPetIntTipoRespDTO->setStrNome($_POST['txtNome']);
            $objMdPetIntTipoRespDTO->setStrTipoPrazoExterno($_POST['rdoPrazo']);
            $objMdPetIntTipoRespDTO->setNumValorPrazoExterno($valorPrazo);
            $objMdPetIntTipoRespDTO->setStrTipoRespostaAceita($_POST['rdoResposta']);
            $objMdPetIntTipoRespDTO->setStrSinAtivo('S');
            $objMdPetIntTipoRespDTO->setStrTipoDia($tipoDia);


            if (isset($_POST['sbmCadastrarMdPetIntTipoResp'])) {
                try {

                    $objMdPetIntTipoRespRN = new MdPetIntTipoRespRN();
                    $objMdPetIntTipoRespDTO = $objMdPetIntTipoRespRN->cadastrar($objMdPetIntTipoRespDTO);
                    PaginaSEI::getInstance()->adicionarMensagem(' "' . $objMdPetIntTipoRespDTO->getNumIdMdPetIntTipoResp() . '" cadastrado com sucesso.');
                    header('Location: ' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . PaginaSEI::getInstance()->getAcaoRetorno() . '&acao_origem=' . $_GET['acao'] . '&id_md_pet_int_tipo_resp=' . $objMdPetIntTipoRespDTO->getNumIdMdPetIntTipoResp() . PaginaSEI::getInstance()->montarAncora($objMdPetIntTipoRespDTO->getNumIdMdPetIntTipoResp())));
                    die;
                } catch (Exception $e) {
                    PaginaSEI::getInstance()->processarExcecao($e);
                }

            }

            break;

        case 'md_pet_int_tipo_resp_alterar':

            $strTitulo = 'Alterar Tipo de Resposta';

            $arrComandos[] = '<button type="submit" accesskey="S" name="sbmAlterarMdPetIntTipoResp" value="Salvar" class="infraButton"><span class="infraTeclaAtalho">S</span>alvar</button>';

            $strDesabilitar = 'disabled="disabled"';

            if (isset($_GET['id_md_pet_int_tipo_resp'])) {
                $objMdPetIntTipoRespDTO->setNumIdMdPetIntTipoResp($_GET['id_md_pet_int_tipo_resp']);
                $objMdPetIntTipoRespDTO->retTodos();
                $objMdPetIntTipoRespRN = new MdPetIntTipoRespRN();
                $objMdPetIntTipoRespDTO = $objMdPetIntTipoRespRN->consultar($objMdPetIntTipoRespDTO);
                if ($objMdPetIntTipoRespDTO == null) {
                    throw new InfraException("Registro de tipo de resposta não encontrado.");
                }

            } else {
                $tipoDia = null;
                switch ($_POST['rdoPrazo']) {
                    case 'N':
                        $valorPrazo = 0;
                        break;
                    case 'D':
                        $valorPrazo = $_POST['txtValorPrazoExternoDia'];
                        $tipoDia = $_POST['rdTipoDia'];
                        break;
                    case 'M':
                        $valorPrazo = $_POST['txtValorPrazoExternoMes'];
                        break;
                    case 'A':
                        $valorPrazo = $_POST['txtValorPrazoExternoAno'];
                        break;
                }

                $objMdPetIntTipoRespDTO->setStrNome($_POST['txtNome']);
                $objMdPetIntTipoRespDTO->setStrTipoPrazoExterno($_POST['rdoPrazo']);
                $objMdPetIntTipoRespDTO->setNumValorPrazoExterno($valorPrazo);
                $objMdPetIntTipoRespDTO->setStrTipoRespostaAceita($_POST['rdoResposta']);
                $objMdPetIntTipoRespDTO->setNumIdMdPetIntTipoResp($_POST['hdnIdMdPetIntTipoResp']);
                $objMdPetIntTipoRespDTO->setStrSinAtivo('S');
                $objMdPetIntTipoRespDTO->setStrTipoDia($tipoDia);

            }

            $arrComandos[] = '<button type="button" accesskey="C" name="btnCancelar" id="btnCancelar" value="Cancelar" onclick="location.href=\'' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . PaginaSEI::getInstance()->getAcaoRetorno() . '&acao_origem=' . $_GET['acao'] . PaginaSEI::getInstance()->montarAncora($objMdPetIntTipoRespDTO->getNumIdMdPetIntTipoResp())) . '\';" class="infraButton"><span class="infraTeclaAtalho">C</span>ancelar</button>';

            if (isset($_POST['sbmAlterarMdPetIntTipoResp'])) {

                try {
                    $objMdPetIntTipoRespRN = new MdPetIntTipoRespRN();
                    $objMdPetIntTipoRespRN->alterar($objMdPetIntTipoRespDTO);
                    PaginaSEI::getInstance()->adicionarMensagem(' "' . $objMdPetIntTipoRespDTO->getNumIdMdPetIntTipoResp() . '" alterado com sucesso.');
                    header('Location: ' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . PaginaSEI::getInstance()->getAcaoRetorno() . '&acao_origem=' . $_GET['acao'] . PaginaSEI::getInstance()->montarAncora($objMdPetIntTipoRespDTO->getNumIdMdPetIntTipoResp())));
                    die;

                } catch (Exception $e) {
                    PaginaSEI::getInstance()->processarExcecao($e);
                }

            }
            break;

        case 'md_pet_int_tipo_resp_consultar':

            $strTitulo = 'Consultar Tipo de Resposta';

            $arrComandos[] = '<button type="button" accesskey="C" name="btnFechar" value="Fechar" onclick="location.href=\'' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . PaginaSEI::getInstance()->getAcaoRetorno() . '&acao_origem=' . $_GET['acao'] . PaginaSEI::getInstance()->montarAncora($_GET['id_md_pet_int_tipo_resp'])) . '\';" class="infraButton">Fe<span class="infraTeclaAtalho">c</span>har</button>';
            $objMdPetIntTipoRespDTO->setNumIdMdPetIntTipoResp($_GET['id_md_pet_int_tipo_resp']);
            $objMdPetIntTipoRespDTO->setBolExclusaoLogica(false);
            $objMdPetIntTipoRespDTO->retTodos();
            $objMdPetIntTipoRespRN = new MdPetIntTipoRespRN();
            $objMdPetIntTipoRespDTO = $objMdPetIntTipoRespRN->consultar($objMdPetIntTipoRespDTO);

            if ($objMdPetIntTipoRespDTO === null) {
                throw new InfraException("Registro não encontrado.");
            }

            break;

        default:
            throw new InfraException("Ação '" . $_GET['acao'] . "' não reconhecida.");
    }

} catch (Exception $e) {
    PaginaSEI::getInstance()->processarExcecao($e);
}

PaginaSEI::getInstance()->montarDocType();
PaginaSEI::getInstance()->abrirHtml();
PaginaSEI::getInstance()->abrirHead();
PaginaSEI::getInstance()->montarMeta();
PaginaSEI::getInstance()->montarTitle(PaginaSEI::getInstance()->getStrNomeSistema() . ' - ' . $strTitulo);
PaginaSEI::getInstance()->montarStyle();
PaginaSEI::getInstance()->abrirStyle();
require_once "md_pet_int_tipo_resp_cadastro_css.php";
PaginaSEI::getInstance()->fecharStyle();
PaginaSEI::getInstance()->montarJavaScript();
PaginaSEI::getInstance()->abrirJavaScript();
PaginaSEI::getInstance()->fecharJavaScript();
PaginaSEI::getInstance()->fecharHead();
PaginaSEI::getInstance()->abrirBody($strTitulo, 'onload="inicializar();"');
$title = '';
?>
    <form id="frmMdPetIntTipoRespCadastro" method="post" onsubmit="return OnSubmitForm();"
          action="<?= SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . $_GET['acao'] . '&acao_origem=' . $_GET['acao']) ?>">
        <? PaginaSEI::getInstance()->montarBarraComandosSuperior($arrComandos); ?>

        <div class="row">
            <div class="col-sm-12 col-md-6 col-lg-6 col-xl-6">
                <label id="lblNome" for="txtNome" accesskey="" class="infraLabelObrigatorio">Nome:</label>
                <a href="javascript:void(0);" id="tipoAjuda"
                   tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>" <?= PaginaSEI::montarTitleTooltip('Escrever nome que reflita a possível Resposta do Usuário Externo a ser intimado. Exemplos: Recurso de 1ª Instância, Embargos de Declaração, Pedido de Reconsideração.', 'Ajuda') ?>>
                    <img src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/ajuda.svg" class="infraImgModulo"/></a>
                <input type="text" id="txtNome" name="txtNome" class="infraText form-control"
                       value="<?= PaginaSEI::tratarHTML($objMdPetIntTipoRespDTO->getStrNome()); ?>"
                       onkeypress="return infraMascaraTexto(this,event,70);" maxlength="70"
                       tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12 col-md-10 col-lg-10 col-xl-8">
                <fieldset id="fldPrazo" class="form-control">
                    <legend class="infraLegend"> Prazo Externo</legend>
                    <div class="row">
                        <div class="col-sm-6 col-md-3 col-lg-3 col-xl-2" style="padding-top: 5px; padding-bottom: 5px">
                            <? $checked = $objMdPetIntTipoRespDTO->getStrTipoPrazoExterno() == 'D' ? 'checked="checked"' : ''; ?>
                            <? $valor = $objMdPetIntTipoRespDTO->getStrTipoPrazoExterno() == 'D' ? $objMdPetIntTipoRespDTO->getNumValorPrazoExterno() : ''; ?>
                            <input type="radio" name="rdoPrazo"
                                   id="optPrazoDia" <? echo $checked ?> value="D"
                                   class="infraRadio"
                                   onclick="verificaPrazo('D')"
                                   tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
                            <label id="lblDias" for="optPrazoDia" class="infraLabelRadio">Dias</label>
                        </div>
                        <div class="col-sm-6 col-md-2 col-lg-2 col-xl-2">
                            <input type="text" id="txtValorPrazoExternoDia"
                                   onkeypress="return infraMascaraTexto(this,event,3);"
                                   name="txtValorPrazoExternoDia" class="infraText form-control"
                                   value="<?= PaginaSEI::tratarHTML($valor); ?>" maxlength="3"
                                   tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
                        </div>
                        <div class="col-sm-6 col-md-6 col-lg-5 col-xl-4" id="spnTipoDias">
                            <input
                                <?php echo ($objMdPetIntTipoRespDTO->getStrTipoDia() == 'C' || $objMdPetIntTipoRespDTO->getStrTipoDia() == '') ? 'checked="checked"' : ''; ?>
                                    type="radio" id="rdTipoDiaC" name="rdTipoDia" class="infraRadio"
                                    value="C"
                                    tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
                            <label for="rdTipoDiaC" class="infraLabelRadio">
                                Corridos
                            </label>
                            <input
                                <?php echo $objMdPetIntTipoRespDTO->getStrTipoDia() == 'U' ? 'checked="checked"' : ''; ?>
                                    type="radio" id="rdTipoDiaU" name="rdTipoDia" class="infraRadio"
                                    value="U"
                                    tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
                            <label for="rdTipoDiaU" class="infraLabelRadio">
                                Úteis
                            </label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6 col-md-3 col-lg-3 col-xl-2" style="padding-top: 5px; padding-bottom: 5px">
                            <? $checked = $objMdPetIntTipoRespDTO->getStrTipoPrazoExterno() == 'M' ? 'checked="checked"' : ''; ?>
                            <? $valor = $objMdPetIntTipoRespDTO->getStrTipoPrazoExterno() == 'M' ? $objMdPetIntTipoRespDTO->getNumValorPrazoExterno() : ''; ?>

                            <input type="radio" name="rdoPrazo"
                                   id="optPrazoMes" <? echo $checked ?> value="M"
                                   class="infraRadio"
                                   onclick="verificaPrazo('M')"
                                   tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
                            <label id="lblMes" for="optPrazoMes" class="infraLabelRadio">Mês</label>
                        </div>
                        <div class="col-sm-6 col-md-2 col-lg-2 col-xl-2">
                            <input type="text" id="txtValorPrazoExternoMes" name="txtValorPrazoExternoMes"
                                   onkeypress="return infraMascaraTexto(this,event,2);" class="infraText form-control"
                                   value="<?= PaginaSEI::tratarHTML($valor); ?>" maxlength="3"
                                   tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6 col-md-3 col-lg-3 col-xl-2" style="padding-top: 5px; padding-bottom: 5px">
                            <? $checked = $objMdPetIntTipoRespDTO->getStrTipoPrazoExterno() == 'A' ? 'checked="checked"' : ''; ?>
                            <? $valor = $objMdPetIntTipoRespDTO->getStrTipoPrazoExterno() == 'A' ? $objMdPetIntTipoRespDTO->getNumValorPrazoExterno() : ''; ?>
                            <input type="radio" name="rdoPrazo"
                                   id="optPrazoAno" <? echo $checked ?> value="A"
                                   class="infraRadio"
                                   onclick="verificaPrazo('A')"
                                   tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
                            <label id="lblAno" for="optPrazoAno" class="infraLabelRadio">Ano</label>
                        </div>
                        <div class="col-sm-6 col-md-2 col-lg-2 col-xl-2">
                            <input type="text" id="txtValorPrazoExternoAno" name="txtValorPrazoExternoAno"
                                   onkeypress="return infraMascaraTexto(this,event,1);" class="infraText form-control"
                                   value="<?= PaginaSEI::tratarHTML($valor); ?>" maxlength="3"
                                   tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
                        </div>
                    </div>
                </fieldset>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12 col-md-10 col-lg-10 col-xl-8">
                <fieldset id="fldResposta" class="form-control">
                    <legend class="infraLegend"> Resposta do Usuário Externo</legend>

                    <? $checked = $objMdPetIntTipoRespDTO->getStrTipoRespostaAceita() == 'F' ? 'checked="checked"' : ''; ?>

                    <input type="radio" name="rdoResposta" id="optTipoRespostaFacultativa" <? echo $checked ?>
                           value="F"
                           class="infraRadio" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
                    <label id="lblAno" for="optTipoRespostaFacultativa" class="infraLabelRadio">Resposta Facultativa</label>
                    <br/>
                    <? $checked = $objMdPetIntTipoRespDTO->getStrTipoRespostaAceita() == 'E' ? 'checked="checked"' : ''; ?>

                    <input type="radio" name="rdoResposta" id="optTipoRespostaExige" <? echo $checked ?> value="E"
                           class="infraRadio" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
                    <label id="lblExige" for="optTipoRespostaExige" class="infraLabelRadio">
                        Exige Resposta (intimação destacará o Tipo de Resposta e Prazo Externo esperado e emitirá
                        reiterações por e-mail) </label>

                </fieldset>
            </div>
        </div>
        <input type="hidden" id="hdnIdMdPetIntTipoResp" name="hdnIdMdPetIntTipoResp"
               value="<?= $objMdPetIntTipoRespDTO->getNumIdMdPetIntTipoResp(); ?>"/>


    </form>
<?
PaginaSEI::getInstance()->montarAreaDebug();
require_once "md_pet_int_tipo_resp_cadastro_js.php";
PaginaSEI::getInstance()->fecharBody();
PaginaSEI::getInstance()->fecharHtml();
?>