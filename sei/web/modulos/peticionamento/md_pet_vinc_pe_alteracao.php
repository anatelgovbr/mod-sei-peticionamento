<?php
/**
 * Created by PhpStorm.
 * User: jhon.carvalho
 * Date: 09/02/2018
 * Time: 10:05
 */
try{

    require_once dirname(__FILE__) . '/../../SEI.php';

    session_start();

    switch ($_GET['acao']) {

        case 'md_pet_vinc_pe_alterar':
            $strTitulo = 'Alterar o Responsável Legal';
            $siglaOrgao = SessaoSEIExterna::getInstance()->getStrSiglaOrgaoUsuarioExterno();
            break;
    }

    $arrComandos = array();
    $arrComandos[] = '<input type="submit" id="btnSalvar" style="width: 80px" value="Salvar" class="infraButton" />';
    $arrComandos[] = '<input type="submit" id="btnVoltar"  value="Voltar" style="width: 80px" class="infraButton" />';


}catch(Exception $e){

}

PaginaSEI::getInstance()->montarDocType();
PaginaSEI::getInstance()->abrirHtml();
PaginaSEI::getInstance()->abrirHead();
PaginaSEI::getInstance()->montarMeta();
PaginaSEI::getInstance()->montarTitle(PaginaSEI::getInstance()->getStrNomeSistema() . ' - ' . $strTitulo);
PaginaSEI::getInstance()->montarStyle();
PaginaSEI::getInstance()->abrirStyle();

PaginaSEI::getInstance()->fecharStyle();
PaginaSEI::getInstance()->montarJavaScript();
PaginaSEI::getInstance()->abrirJavaScript();


PaginaSEI::getInstance()->fecharJavaScript();
PaginaSEI::getInstance()->fecharHead();
PaginaSEI::getInstance()->abrirBody($strTitulo, 'onload="inicializar();"');
?>

<style type="text/css">
    #txtInformativo1{
        font-size: 13px;
    }

    #lblCnpj{
        position: absolute;
        top: 10%;
        left:2%;
    }
    
	#txtCnpj{
        position: absolute;
        top: 13%;
        left:2%;
    }

    #lblRazaoSocial{
        position: absolute;
        top: 10%;
        left:21%;
    }
    #txtRazaoSocial{
        position: absolute;
        top: 13%;
        left:21%;
        width: 37%;
    }

    #lblCpf{
        position: absolute;
        top: 21%;
        left:2%;
    }
    
	#txtCpf{
        position: absolute;
        top: 24%;
        left: 2%;
        width: 24%;
    }
    
	#lblNomeResp{
        position: absolute;
        top: 21%;
        left:29%;
    }
    
	#txtNomeResp{
        position: absolute;
        top: 24%;
        left: 29%;
        width: 29%;
    }

    #txtInformativo2{
           margin-top: 14%;
           font-size: 13px;
           margin-left: 2%;
           font-weight: bold;
    }

    #txtInformativo3{
        font-size: 12px;
        margin-left: 2%;
    }
    #lblCpfUsu{
        position: absolute;
        top: 44%;
        left: 2%;
        width: 29%;
    }
    #txtCpfUsu{
        position: absolute;
        top: 47%;
        left: 2%;
        width: 17%;
    }
    #lblNomeUsu{
        position: absolute;
        top: 44%;
        left: 23%;
        width: 29%;
    }
    #txtNomeUsu{
        position: absolute;
        top: 47%;
        left: 23%;
        width: 35%;
    }

    #lblNumSei{
        position: absolute;
        top: 54%;
        left: 2%;

    }
    #txtNumSei{
        position: absolute;
        top: 57%;
        left: 2%;
        width: 17%;
    }

    #sbmAdicionarNumeroSei{
        position: absolute;
        top: 57%;
        left: 22%;

    }

    #divInfraBarraComandosInferior{
        margin-top: 16%;
    }

</style>
<form>
    <?
    PaginaSEI::getInstance()->abrirAreaDados('53em;');
    ?>
    <p id="txtInformativo1">Os dados aqui dispostos dizem respeito ao Responsável Legal pela Pessoa Jurídica indicada, conforme constante no SEI.</p>

    <label id="lblCnpj" for="txtCnpj" class="infraLabelObrigatorio">CNPJ:</label>
    <input type="text" id="txtCnpj" name="txtCnpj" class="infraText"
           value="" maxlength="100"
           tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>

    <label id="lblRazaoSocial" for="txtRazaoSocial" class="infraLabelObrigatorio">Razão Social:</label>
    <input type="text" id="txtRazaoSocial" name="txtRazaoSocial" class="infraText"
           value="" maxlength="100"
           tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>

    <label id="lblCpf" for="txtCpf" class="infraLabelObrigatorio">CPF do Responsável Legal:</label>
    <input type="text" id="txtCpf" name="txtCpf" class="infraText"
           value="" maxlength="100"
           tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>

    <label id="lblNomeResp" for="txtNomeResp" class="infraLabelObrigatorio">Nome do Responsável Legal:</label>
    <input type="text" id="txtNomeResp" name="txtNomeResp" class="infraText"
           value="" maxlength="100"
           tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>

    <p id="txtInformativo2">Informe abaixo o CPF do Usuário Externo que deseja indicar como novo Responsável Legal por esta Pessoa Jurídica.</p>

    <label id="lblCpfUsu" for="txtCpfUsu" class="infraLabelObrigatorio">CPF do Usuário Externo:</label>
    <input type="text" id="txtCpfUsu" name="txtCpfUsu" class="infraText"
           value="" maxlength="100"
           tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>

    <label id="lblNomeUsu" for="txtNomeUsu" class="infraLabelObrigatorio">Nome do Usuário Externo:</label>
    <input type="text" id="txtNomeUsu" name="txtNomeUsu" class="infraText"
           value="" maxlength="100"
           tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>

    <label id="lblNumSei" for="txtNumSei" class="infraLabelObrigatorio">Número SEI da Justificativa:</label>
    <input type="text" id="txtNumSei" name="txtNumSei" class="infraText"
           value="" maxlength="100"
           tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>

    <button type="button" name="sbmAdicionarNumeroSei" onclick="adicionarDocInstaurador();"
            id="sbmAdicionarNumeroSei" value="Adicionar"
            class="infraButton NumeroSEINaoValidado NumeroSEIAdicionar"
            tabindex="<?= PaginaSEI::getInstance()->getProxTabDados()?>">Adicionar
    </button>
<?
PaginaSEI::getInstance()->montarBarraComandosInferior($arrComandos);
PaginaSEI::getInstance()->fecharAreaDados();

//PaginaSEI::getInstance()->montarAreaDebug();
?>
</form>
<?PaginaSEI::getInstance()->fecharBody();
PaginaSEI::getInstance()->fecharHtml();
