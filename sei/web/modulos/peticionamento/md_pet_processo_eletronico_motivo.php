<?php
/**
 * Created by PhpStorm.
 * User: jhon.carvalho
 * Date: 06/04/2018
 * Time: 11:13
 */
require_once dirname(__FILE__) . '/../../SEI.php';

session_start();

SessaoSEIExterna::getInstance()->validarLink();

SessaoSEIExterna::getInstance()->validarPermissao($_GET['acao']);

switch ($_GET['acao']){
    case 'processo_eletronico_responder_motivo_renunciar':
        $strTitulo = 'Renunciar Procuração Eletrônica';
        $strMotivo = 'Motivo da Renúncia (constará no teor do documento de Renúncia que será gerado):';
        break;
    case 'processo_eletronico_responder_motivo_revogar':
        $strTitulo = 'Revogar Procuração Eletrônica';
        $strMotivo = 'Motivo da Revogação (constará no teor do documento de Revogação que será gerado):';
        break;

}
$strLinkRetorno = SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=md_pet_vinc_usu_ext_pe_listar&acao_origem='.$_GET['acao']);
$arrComandos = array();
$arrComandos[] = '<button type="button" accesskey="P" name="btnPeticionar" onclick="onSubmitForm()" class="infraButton"><span class="infraTeclaAtalho">P</span>eticionar</button>';
$arrComandos[] = '<button type="button" accesskey="c" name="btnFechar" value="Fechar" onclick="window.close();" class="infraButton"><span class="infraTeclaAtalho">C</span>ancelar</button>';
$id_contato_vin = $_GET['id_contato_vinc'];
$id_procedimento = $_GET['id_procedimento'];
$cpfProcurador = $_GET['cpf'];
$idProcuracao = $_GET['id_documento'];
$id_vinculacao = $_GET['id_vinculacao'];
$tpDocumento = $_GET['tpDocumento'];
$motivoOk=false;
$tipoVinculo = $_GET['tpVinculo'];
$tipoProcuracao = $_GET['tpProc'];
if(isset($_POST['txtJustificativa']) && $_POST['txtJustificativa']!=''){

    /*$mdPetVinUsuExtProcRN = new MdPetVinUsuExtProcRN();
    $mdPetVinUsuExtProc = $mdPetVinUsuExtProcRN->gerarProcedimentoVinculoProcuracaoMotivo($_POST);
    $motivoOk = $mdPetVinUsuExtProc;*/
}

PaginaSEIExterna::getInstance()->setTipoPagina(InfraPagina::$TIPO_PAGINA_SIMPLES);

PaginaSEIExterna::getInstance()->montarDocType();
PaginaSEIExterna::getInstance()->abrirHtml();
PaginaSEIExterna::getInstance()->abrirHead();
PaginaSEIExterna::getInstance()->montarMeta();
PaginaSEIExterna::getInstance()->montarTitle(PaginaSEI::getInstance()->getStrNomeSistema().' - '.$strTitulo);
PaginaSEIExterna::getInstance()->montarStyle();
PaginaSEIExterna::getInstance()->abrirStyle();

PaginaSEIExterna::getInstance()->fecharStyle();
PaginaSEIExterna::getInstance()->montarJavaScript();
PaginaSEIExterna::getInstance()->abrirJavaScript();
if(0){?>
<script type="text/javascript">
<?} ?>
    function onSubmitForm() {

        var txtMotivo = document.getElementById('txtJustificativa').value;
        if(txtMotivo ==''){
           alert('Informe o motivo.');
            return false;
        }else{

            infraAbrirJanela('<?php echo PaginaSEIExterna::getInstance()->formatarXHTML(SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=md_pet_vinc_pe_desvinculo_concluir&tipoDocumento='.$tpDocumento.'&tpVinc='.$tipoVinculo.'&acao_origem='.$_GET['acao']))?>',
                'concluirPeticionamento',
                770,
                480,
                '', //options
                true); //modal
            //document.getElementById('frmPesquisa').submit();
        }
    }
<?php

if(0){ ?>
</script>
<?}
PaginaSEIExterna::getInstance()->fecharJavaScript();
PaginaSEIExterna::getInstance()->fecharHead();
PaginaSEIExterna::getInstance()->abrirBody($strTitulo);
?>
<style>
    #lblJustificativa{
        position: absolute;
    }
    #txtJustificativa{
        position: absolute;
        margin-top: 3%;
        width: 99%;
    }

</style>
<form id="frmPesquisa" method="post"
      action="<?= SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao='.$_GET['acao'].'&acao_origem=' . $_GET['acao']) ?>">
    <?
    PaginaSEIExterna::getInstance()->montarBarraComandosSuperior($arrComandos);
    PaginaSEIExterna::getInstance()->abrirAreaDados("12em");
    ?>

<label id="lblJustificativa" for="txtJustificativa" class="infraLabelObrigatorio"><?=$strMotivo?></label>
<textarea name="txtJustificativa" id="txtJustificativa" maxlength=250 type="text" class="infraText" rows="5" onkeypress="return infraMascaraTexto(this,event,250);"></textarea>
    <input type="hidden" id="hdnIdContatoVinc" name="hdnIdContatoVinc" value="<?= $id_contato_vin?>">
    <input type="hidden" id="hdnCpfProcurador" name="hdnCpfProcurador" value="<?=$cpfProcurador?>">
    <input type="hidden" id="hdnIdProcuracao" name="hdnIdProcuracao" value="<?=$idProcuracao?>">
    <input type="hidden" id="hdnIdProcedimento" name="hdnIdProcedimento" value="<?=$id_procedimento?>">
    <input type="hidden" id="hdnIdVinculacao" name="hdnIdVinculacao" value="<?=$id_vinculacao?>">
    <input type="hidden" id="hdnTpDocumento" name="hdnTpDocumento" value="<?=$tpDocumento?>">
    <input type="hidden" id="hdnTpVinculo" name="hdnTpVinculo" value="<?=$tipoVinculo?>">
    <input type="hidden" id="hdnTpProcuracao" name="hdnTpProcuracao" value="<?=$tipoProcuracao?>">
<?
PaginaSEIExterna::getInstance()->fecharAreaDados();
?>
</form>
<?
PaginaSEIExterna::getInstance()->fecharBody();
PaginaSEIExterna::getInstance()->fecharHtml();
?>
