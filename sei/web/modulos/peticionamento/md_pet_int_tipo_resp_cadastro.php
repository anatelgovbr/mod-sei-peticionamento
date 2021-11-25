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
?>
#divGeral{height:50em;}
#fldPrazo {position:absolute;left:0%;top:10%;height:20%;width:80%;}
#fldResposta {position:absolute;left:0%;top:35%;height:13%;width:80%;}

#txtValorPrazoExternoDia {position: absolute;left: 8%;top: 25%;width: 6%;}
#txtValorPrazoExternoMes {position: absolute;left: 8%;top: 45%;width: 6%;}
#txtValorPrazoExternoAno {position: absolute;left: 8%;top: 67%;width: 6%;}

#lblNome {position: absolute;left: 0%;top: 0%;width: 50%;}
#txtNome {position: absolute;left: 0%;top: 4%;width: 40%;}
#tipoAjuda {position:absolute;left:41%;top:4%;}

#spnTipoDias {position:absolute;left:15%;top:25%;}
<?
PaginaSEI::getInstance()->fecharStyle();
PaginaSEI::getInstance()->montarJavaScript();
PaginaSEI::getInstance()->abrirJavaScript();
?>
        function inicializar() {
            <? if ($_GET['acao'] == 'md_pet_int_tipo_resp_cadastrar') {?>
            document.getElementById('txtNome').focus();
            <? } else if ($_GET['acao'] == 'md_pet_int_tipo_resp_consultar'){?>
            infraDesabilitarCamposAreaDados();
            <?}else{?>
            document.getElementById('btnCancelar').focus();
            <?}?>

            <?if ($objMdPetIntTipoRespDTO->getStrTipoPrazoExterno() != 'D'){ ?>
            document.getElementById('txtValorPrazoExternoDia').style.display = "none";
            document.getElementById('spnTipoDias').style.display = "none";
            <? }
            if ($objMdPetIntTipoRespDTO->getStrTipoPrazoExterno() != 'M') { ?>
            document.getElementById('txtValorPrazoExternoMes').style.display = "none";
            <? }
            if ($objMdPetIntTipoRespDTO->getStrTipoPrazoExterno() != 'A') { ?>
            document.getElementById('txtValorPrazoExternoAno').style.display = "none";
            <?}?>
        }
        function verificaPrazo(id){
            document.getElementById('txtValorPrazoExternoDia').style.display = "none";
            document.getElementById('txtValorPrazoExternoMes').style.display = "none";
            document.getElementById('txtValorPrazoExternoAno').style.display = "none";
            document.getElementById('spnTipoDias').style.display = "none";
            if(id != 'D'){
                document.getElementById('rdTipoDiaU').checked = false;
                document.getElementById('rdTipoDiaC').checked = false;
            }
            if (id == 'N') {
                document.getElementById('optTipoRespostaFacultativa').checked = true;
                document.getElementById('optTipoRespostaExige').disabled = true;
            }else{
                document.getElementById('optTipoRespostaFacultativa').disabled = false;
                document.getElementById('optTipoRespostaExige').disabled = false;
                document.getElementById('optTipoRespostaFacultativa').checked = false;
                if(id == 'D'){
                    document.getElementById('rdTipoDiaC').checked = true;
                    document.getElementById('txtValorPrazoExternoDia').style.display = "block";
                    document.getElementById('spnTipoDias').style.display = "block";
                    document.getElementById('txtValorPrazoExternoDia').value = "";
                }else if(id == 'M'){
                    document.getElementById('txtValorPrazoExternoMes').style.display = "block";
                    document.getElementById('txtValorPrazoExternoMes').value = "";
                }else if(id == 'A'){
                    document.getElementById('txtValorPrazoExternoAno').style.display = "block";
                    document.getElementById('txtValorPrazoExternoAno').value = "";
                }
            }
        }
<?
PaginaSEI::getInstance()->fecharJavaScript();
PaginaSEI::getInstance()->fecharHead();
PaginaSEI::getInstance()->abrirBody($strTitulo, 'onload="inicializar();"');
$title = '';
?>
<form id="frmMdPetIntTipoRespCadastro" method="post" onsubmit="return OnSubmitForm();"action="<?= SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . $_GET['acao'] . '&acao_origem=' . $_GET['acao']) ?>">
        <? PaginaSEI::getInstance()->montarBarraComandosSuperior($arrComandos); ?>
        <div id="divGeral" class="infraAreaDados">
            <label id="lblNome" for="txtNome" accesskey="" class="infraLabelObrigatorio">Nome:</label>
            <a href="javascript:void(0);" id="tipoAjuda" tabindex="<?=PaginaSEI::getInstance()->getProxTabDados()?>" <?=PaginaSEI::montarTitleTooltip('Escrever nome que reflita a possível Resposta do Usuário Externo a ser intimado. Exemplos: Recurso de 1ª Instância, Embargos de Declaração, Pedido de Reconsideração.')?>><img src="<?=PaginaSEI::getInstance()->getDiretorioImagensGlobal()?>/ajuda.gif" class="infraImg"/></a>
            <input type="text" id="txtNome" name="txtNome" class="infraText" value="<?= PaginaSEI::tratarHTML($objMdPetIntTipoRespDTO->getStrNome()); ?>" onkeypress="return infraMascaraTexto(this,event,70);" maxlength="70" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>

            <fieldset id="fldPrazo" >
                <legend class="infraLegend"> Prazo Externo </legend>
                <br>
                <div id="divOptDias" class="infraDivRadio">
                    <?  $checked = $objMdPetIntTipoRespDTO->getStrTipoPrazoExterno() == 'D' ? 'checked="checked"' : ''; ?>
                    <?  $valor = $objMdPetIntTipoRespDTO->getStrTipoPrazoExterno() == 'D' ? $objMdPetIntTipoRespDTO->getNumValorPrazoExterno() : ''; ?>
                    <span id="spnDias"><label id="lblDias" class="infraLabelRadio"><input type="radio" name="rdoPrazo" id="optPrazoDia" <? echo $checked ?> value="D"  class="infraRadio" onclick="verificaPrazo('D')" tabindex="<?=PaginaSEI::getInstance()->getProxTabDados()?>"/>
                    Dias</label></span>
                    <input type="text" id="txtValorPrazoExternoDia" onkeypress="return infraMascaraTexto(this,event,3);" name="txtValorPrazoExternoDia" class="infraText" value="<?= PaginaSEI::tratarHTML($valor); ?>" maxlength="3" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
                    <span id="spnTipoDias">
                        <label for="rdTipoDiaC" class="infraLabelRadio">
                        <input
                          <?php  echo ($objMdPetIntTipoRespDTO->getStrTipoDia() == 'C' || $objMdPetIntTipoRespDTO->getStrTipoDia() == '') ? 'checked="checked"' : ''; ?>
                               type="radio" id="rdTipoDiaC" name="rdTipoDia" class="infraText"
                               value="C"
                               tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/> Corridos
                        </label>
                        <label for="rdTipoDiaU" class="infraLabelRadio">
                        <input
                          <?php  echo $objMdPetIntTipoRespDTO->getStrTipoDia() == 'U' ? 'checked="checked"' : ''; ?>
                                type="radio" id="rdTipoDiaU" name="rdTipoDia" class="infraText"
                                value="U"
                                tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/> Úteis
                        </label>
                    </span>
                </div>
                <br>
                <div id="divOptMes" class="infraDivRadio">
                    <? $checked = $objMdPetIntTipoRespDTO->getStrTipoPrazoExterno() == 'M' ? 'checked="checked"' : ''; ?>
                    <?  $valor = $objMdPetIntTipoRespDTO->getStrTipoPrazoExterno() == 'M' ? $objMdPetIntTipoRespDTO->getNumValorPrazoExterno() : ''; ?>
                    <span id="spnMes"><label id="lblMes" class="infraLabelRadio"><input type="radio" name="rdoPrazo" id="optPrazoMes" <? echo $checked ?> value="M" class="infraRadio" onclick="verificaPrazo('M')" tabindex="<?=PaginaSEI::getInstance()->getProxTabDados()?>"/>
                    Mês</label></span>
                    <input type="text" id="txtValorPrazoExternoMes" name="txtValorPrazoExternoMes" onkeypress="return infraMascaraTexto(this,event,2);" class="infraText" value="<?= PaginaSEI::tratarHTML($valor); ?>" maxlength="3" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
                </div>
                <br>
                <div id="divOptAno" class="infraDivRadio">
                    <? $checked = $objMdPetIntTipoRespDTO->getStrTipoPrazoExterno() == 'A' ? 'checked="checked"' : ''; ?>
                    <?  $valor = $objMdPetIntTipoRespDTO->getStrTipoPrazoExterno() == 'A' ? $objMdPetIntTipoRespDTO->getNumValorPrazoExterno() : ''; ?>
                    <span id="spnAno"><label id="lblAno" class="infraLabelRadio"><input type="radio" name="rdoPrazo" id="optPrazoAno" <? echo $checked ?> value="A" class="infraRadio" onclick="verificaPrazo('A')" tabindex="<?=PaginaSEI::getInstance()->getProxTabDados()?>"/>
                    Ano</label></span>
                    <input type="text" id="txtValorPrazoExternoAno" name="txtValorPrazoExternoAno" onkeypress="return infraMascaraTexto(this,event,1);" class="infraText" value="<?= PaginaSEI::tratarHTML($valor); ?>" maxlength="3" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
                </div>
            </fieldset>

            <fieldset id="fldResposta" >
                <legend class="infraLegend"> Resposta do Usuário Externo </legend>
                <div id="divOptAno" class="infraDivRadio">
                    <? $checked = $objMdPetIntTipoRespDTO->getStrTipoRespostaAceita() == 'F' ? 'checked="checked"' : ''; ?>
                    <span id="spnAno"><label id="lblAno" class="infraLabelRadio">
                    <input type="radio" name="rdoResposta" id="optTipoRespostaFacultativa" <? echo $checked ?> value="F" class="infraRadio" tabindex="<?=PaginaSEI::getInstance()->getProxTabDados()?>"/>
                    Resposta Facultativa</label></span>
                </div>
                <br>
                <div id="divOptAno" class="infraDivRadio">
                    <? $checked = $objMdPetIntTipoRespDTO->getStrTipoRespostaAceita() == 'E' ? 'checked="checked"' : ''; ?>
                    <span id="spnExige"><label id="lblExige" class="infraLabelRadio">
                    <input type="radio" name="rdoResposta" id="optTipoRespostaExige" <? echo $checked ?> value="E" class="infraRadio" tabindex="<?=PaginaSEI::getInstance()->getProxTabDados()?>"/>
                    Exige Resposta (intimação destacará o Tipo de Resposta e Prazo Externo esperado e emitirá reiterações por e-mail) </span> <br>
                </div>
                <br>
            </fieldset>
            <input type="hidden" id="hdnIdMdPetIntTipoResp" name="hdnIdMdPetIntTipoResp" value="<?= $objMdPetIntTipoRespDTO->getNumIdMdPetIntTipoResp(); ?>"/>
        </div>

</form>
<?
PaginaSEI::getInstance()->montarAreaDebug(); 
PaginaSEI::getInstance()->fecharBody();
PaginaSEI::getInstance()->fecharHtml();
?>