<fieldset id="informacaoPJ" class="infraFieldset form-control sizeFieldset" style="display: none;" >
    <legend class="infraLegend">&nbsp; Informações da Pessoa Jurídica &nbsp;</legend>
    <div class="row">
        <div class="col-sm-12 col-md-4 col-lg-4">
            <div class="form-group">
                <label class="infraLabelObrigatorio" for="slTipoInteressado" id="lblTipoInteressado">
                    Tipo:
                </label>
                <select class="infraSelect form-control" id="slTipoInteressado" name="slTipoInteressado"
                    <?php echo !is_null($readOnly) ? 'disabled="disabled"' : null?>
                        tabindex="<?= PaginaSEIExterna::getInstance()->getProxTabDados(); ?>">
                    <?php echo $strItensSelTipoInteressado;?>
                </select>
            </div>
        </div>
        <div class="col-sm-12 col-md-8 col-lg-8">
            <div class="form-group">
                <label class="infraLabelObrigatorio" for="txtRazaoSocial" id="lblRazaoSocial">
                    Razão Social:
                </label>
                <input type="text" class="infraText blocInformacaoPj form-control" id="txtRazaoSocial" name="txtRazaoSocial" maxlength="250" onkeypress="return infraMascaraTexto(this,event,250);"
                    tabindex="<?= PaginaSEIExterna::getInstance()->getProxTabDados(); ?>"/>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12 col-md-8 col-lg-8">
            <div class="form-group">
                <input type="hidden" class="infraText blocInformacaoPj" id="txtNomeResponsavelLegal" name="txtNomeResponsavelLegal" maxlength="250"
                    value="<?php echo !is_null($arrDadosPessoaJuridicaVinculo) ? $arrDadosPessoaJuridicaVinculo->getStrNomeContatoRepresentante() : null; ?>"
                    tabindex="<?= PaginaSEIExterna::getInstance()->getProxTabDados(); ?>"/>
                <input type="hidden" class="infraText blocInformacaoPj" id="txtNumeroCpfResponsavel" name="txtNumeroCpfResponsavel"
                    value = '<?php echo (!$stWebService) ? InfraUtil::formatarCpfCnpj($objContatoDTO->getDblCpf()) : ''; ?>'
                    tabindex="<?= PaginaSEIExterna::getInstance()->getProxTabDados(); ?>"/>
                <label class="infraLabelObrigatorio" for="txtLogradouro">
                    Endereço:
                </label>
                <input onkeypress="return infraMascaraTexto(this,event,130);" type="text" class="infraText blocInformacaoPj form-control" id="txtLogradouro" name="txtLogradouro" maxlength="130"
                    tabindex="<?= PaginaSEIExterna::getInstance()->getProxTabDados(); ?>"/>
                <input type="hidden" class="infraText blocInformacaoPj" id="txtLogradouroPadrao" name="txtLogradouroPadrao" maxlength="130"
                    tabindex="<?= PaginaSEIExterna::getInstance()->getProxTabDados(); ?>"/>
                <input type="hidden" class="infraText blocInformacaoPj" id="txtComplementoEndereco" name="txtComplementoEndereco" maxlength="130"
                    tabindex="<?= PaginaSEIExterna::getInstance()->getProxTabDados(); ?>"/>
            </div>
        </div>
        <div class="col-sm-12 col-md-4 col-lg-4">
            <label class="infraLabelObrigatorio" for="txtBairro">
                Bairro:
            </label>
            <input type="text" class="infraText blocInformacaoPj form-control" id="txtBairro" name="txtBairro" maxlength="70" onkeypress="return infraMascaraTexto(this,event,70);"
                   tabindex="<?= PaginaSEIExterna::getInstance()->getProxTabDados(); ?>"/>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-6 col-md-3 col-lg-2">
            <div class="form-group">
                <label class="infraLabelObrigatorio" for="slUf">
                    UF:
                </label>
                <select class="infraSelect blocInformacaoPj form-control" id="slUf" name="slUf"
                        tabindex="<?= PaginaSEIExterna::getInstance()->getProxTabDados(); ?>">
                    <?php echo $slUf; ?>
                </select>
            </div>
        </div>
        <div class="col-sm-6 col-md-6 col-lg-6">
            <div class="form-group">
                <label class="infraLabelObrigatorio" for="txtCidade">
                    Cidade:
                </label>
                <select class="infraSelect blocInformacaoPj form-control" name="selCidade" id="selCidade"
                        tabindex="<?= PaginaSEIExterna::getInstance()->getProxTabDados() ?>"/>
                <?= $strItensSelCidade ?>
                </select>
            </div>
        </div>
        <div class="col-sm-12 col-md-3 col-lg-4">
            <div class="form-group">
                <label class="infraLabelObrigatorio" for="txtNumeroCEP">
                    CEP:
                </label>
                <input type="text" class="infraText blocInformacaoPj form-control" id="txtNumeroCEP" name="txtNumeroCEP" maxlength="15"
                    onkeypress="return infraMascaraNumero(this,event, 9);" onchange="return controlarMascaraCep(this);"
                    onkeyup="return infraMascara(this, event, '#####-###');"
                    tabindex="<?= PaginaSEIExterna::getInstance()->getProxTabDados(); ?>"/>
            </div>
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
