<style type="text/css">
#informacaoPJ {height: auto; width: 96%; margin-bottom: 11px;}
.sizeFieldset {height:auto; width: 86%;}
.fieldsetClear {border:none !important;}
</style>
<fieldset id="informacaoPJ" class="infraFieldset sizeFieldset" style="display: none;width: auto;" >
    <legend class="infraLegend">&nbsp; Informações da Pessoa Jurídica &nbsp;</legend>
    <div class="container">
        <div class="bloco">
            <label class="infraLabelObrigatorio" for="slTipoInteressado" id="lblTipoInteressado">
                Tipo:
            </label>
            <select class="infraSelect" id="slTipoInteressado" name="slTipoInteressado" 
            <?php echo !is_null($readOnly) ? 'disabled="disabled"' : null?>
            tabindex="<?= PaginaSEIExterna::getInstance()->getProxTabDados(); ?>">
            <?php echo $strItensSelTipoInteressado;?>
            </select>
        </div>

        <div class="clear"></div>

            <div class="bloco" id="blcInfRazaoSocial">
                <label class="infraLabelObrigatorio" for="txtRazaoSocial" id="lblRazaoSocial">
                    Razão Social:
                </label>
                <input type="text" class="infraText blocInformacaoPj" id="txtRazaoSocial" name="txtRazaoSocial" maxlength="250" onkeypress="return infraMascaraTexto(this,event,250);"
                 readonly
                tabindex="<?= PaginaSEIExterna::getInstance()->getProxTabDados(); ?>"/>
            </div>

        <div class="clear"></div>

            <div class="bloco" id="blcInfNomeResponsavel">
                <input type="hidden" class="infraText blocInformacaoPj" id="txtNomeResponsavelLegal" name="txtNomeResponsavelLegal" maxlength="250"
                value="<?php echo !is_null($arrDadosPessoaJuridicaVinculo) ? $arrDadosPessoaJuridicaVinculo->getStrNomeContatoRepresentante() : null; ?>"
                tabindex="<?= PaginaSEIExterna::getInstance()->getProxTabDados(); ?>"/>
            </div>
            <div class="bloco" id="blcInfCPFResponsavel">
                <input type="hidden" class="infraText blocInformacaoPj" id="txtNumeroCpfResponsavel" name="txtNumeroCpfResponsavel"
                value = '<?php echo (!$stWebService) ? InfraUtil::formatarCpfCnpj($objContatoDTO->getDblCpf()) : ''; ?>'
                tabindex="<?= PaginaSEIExterna::getInstance()->getProxTabDados(); ?>"/>
            </div>

        <div class="clear"></div>

            <div class="bloco" id="blcInfLogradouro">
                <label class="infraLabelObrigatorio" for="txtLogradouro">
                    Endereço:
                </label>
                <input onkeypress="return infraMascaraTexto(this,event,130);" type="text" class="infraText blocInformacaoPj" id="txtLogradouro" name="txtLogradouro" maxlength="130"
                readonly 
                tabindex="<?= PaginaSEIExterna::getInstance()->getProxTabDados(); ?>"/>
                <input type="hidden" class="infraText blocInformacaoPj" id="txtLogradouroPadrao" name="txtLogradouroPadrao" maxlength="130"
                readonly 
                tabindex="<?= PaginaSEIExterna::getInstance()->getProxTabDados(); ?>"/>
                <input type="hidden" class="infraText blocInformacaoPj" id="txtComplementoEndereco" name="txtComplementoEndereco" maxlength="130"
                readonly 
                tabindex="<?= PaginaSEIExterna::getInstance()->getProxTabDados(); ?>"/>
            </div>

        <div class="clear"></div>

            <div class="bloco" id="blcInfBairro">
                <label class="infraLabelObrigatorio" for="txtBairro">
                    Bairro:
                </label>
                <input type="text" class="infraText blocInformacaoPj" id="txtBairro" name="txtBairro" maxlength="70" onkeypress="return infraMascaraTexto(this,event,70);"
                    readonly
                    tabindex="<?= PaginaSEIExterna::getInstance()->getProxTabDados(); ?>"/>
            </div>
            <div class="bloco" id="blcInfUf">
                <label class="infraLabelObrigatorio" for="slUf">
                    UF:
                </label>
                <select class="infraSelect blocInformacaoPj" id="slUf" name="slUf"
                tabindex="<?= PaginaSEIExterna::getInstance()->getProxTabDados(); ?>">
                    <?php echo $slUf; ?>
                </select>
            </div>
            <div class="bloco" id="blcInfCidade">
                <label class="infraLabelObrigatorio" for="txtCidade">
                    Cidade:
                </label>
                <select class="infraSelect blocInformacaoPj" name="selCidade" id="selCidade"
                tabindex="<?= PaginaSEIExterna::getInstance()->getProxTabDados() ?>"/>
                    <?= $strItensSelCidade ?>
                </select>
            </div>
            <div class="bloco" id="blcInfCep">
                <label class="infraLabelObrigatorio" for="txtNumeroCEP">
                    CEP:
                </label>
                <input type="text" class="infraText blocInformacaoPj" id="txtNumeroCEP" name="txtNumeroCEP" maxlength="15"
                    onkeypress="return infraMascaraNumero(this,event, 9);" onchange="return controlarMascaraCep(this);"
                    onkeyup="return infraMascara(this, event, '#####-###');"
                    readonly 
                    tabindex="<?= PaginaSEIExterna::getInstance()->getProxTabDados(); ?>"/>
            </div>
    </div>

    <input type="hidden" name="hdnInformacaoPj" id="hdnInformacaoPj"/>
    <input type="hidden" name="isAlteracaoResponsavelLegal" id="isAlteracaoResponsavelLegal" value="0"/>
    <input type="hidden" name="hdnNomeUsuarioExterno" id="hdnNomeUsuarioExterno"
           value="<?php echo SessaoSEIExterna::getInstance()->getStrNomeUsuarioExterno(); ?>"/>
    <input type="hidden" name="hdnCpfUsuarioExterno" id="hdnCpfUsuarioExterno"
           value="<?php echo $cpfUsuarioExterno; ?>"/>
    <input type="hidden" name="hdnValidadoCnpj" id="hdnValidadoCnpj" value="0"/>
</fieldset>
<br id="informacaoPJ_BR" style="display: none;">