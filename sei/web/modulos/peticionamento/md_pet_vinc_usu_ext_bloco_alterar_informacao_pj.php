<?php
    $readOnly =  'readonly="readonly"';
?>
<fieldset id="informacaoPJAlterar" class="infraFieldset sizeFieldset" style="width: auto;">
    <legend class="infraLegend">&nbsp; Informações da Pessoa Jurídica &nbsp;</legend>
    <div class="row">
        <div class="col-sm-12 col-md-6 col-lg-6">
            <label class="infraLabelObrigatorio" for="slTipoInteressado" id="lblTipoInteressado">
                Tipo:
            </label>
            <select class="infraSelect" id="slTipoInteressado" name="slTipoInteressado"
                <?php echo !is_null($readOnly) ? 'disabled="disabled"' : null?>
                    tabindex="<?= PaginaSEI::getInstance()->getProxTabDados(); ?>">
                <?php echo $strItensSelTipoInteressado;?>
            </select>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12 col-md-6 col-lg-10 col-xl-6">
            <label class="infraLabelObrigatorio" for="txtRazaoSocial" id="lblRazaoSocial">
                Razão Social:
            </label>
            <input type="text" class="infraText blocInformacaoPj" id="txtRazaoSocial" name="txtRazaoSocial" maxlength="250" onkeypress="return infraMascaraTexto(this,event,250);"
                <?php echo $readOnly ?>
                   value="<?php echo !is_null($arrDadosPessoaJuridicaVinculo) ? $arrDadosPessoaJuridicaVinculo->getStrRazaoSocialNomeVinc() : null; ?>"
                   tabindex="<?= PaginaSEI::getInstance()->getProxTabDados(); ?>"/>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12 col-md-5 col-lg-5 col-xl-3 ">
            <label class="infraLabelObrigatorio" for="txtNomeResponsavelLegal" id="lblResponsavelLegal">
                Nome do Responsável Legal:
            </label>
            <input type="text" class="infraText blocInformacaoPj" id="txtNomeResponsavelLegal" name="txtNomeResponsavelLegal" maxlength="250"
                   readonly
                   value="<?php echo !is_null($arrDadosPessoaJuridicaVinculo) ? $arrDadosPessoaJuridicaVinculo->getStrNomeContatoRepresentante() : null; ?>"
                   tabindex="<?= PaginaSEI::getInstance()->getProxTabDados(); ?>"/>
        </div>
        <div class="col-sm-12 col-md-5 col-lg-5 col-xl-3">
            <label class="infraLabelObrigatorio" for="txtNumeroCpfResponsavel" id="lblCpfResponsavelLegal">
                CPF do Responsável Legal:
            </label>
            <input type="text" class="infraText blocInformacaoPj" id="txtNumeroCpfResponsavel" name="txtNumeroCpfResponsavel"
                   readonly
                   value="<?php echo !is_null($arrDadosPessoaJuridicaVinculo) ? InfraUtil::formatarCpf($arrDadosPessoaJuridicaVinculo->getStrCpfContatoRepresentante()) : null; ?>"
                   tabindex="<?= PaginaSEI::getInstance()->getProxTabDados(); ?>"/>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12 col-md-8 col-lg-10 col-xl-6">
            <label class="infraLabelObrigatorio" for="txtLogradouro">
                Endereço:
            </label>
            <input onkeypress="return infraMascaraTexto(this,event,130)"  type="text" class="infraText blocInformacaoPj" id="txtLogradouro" name="txtLogradouro" maxlength="130"
                <?php echo $readOnly?>
                   value="<?php echo $strEndereco;?>"
                   tabindex="<?= PaginaSEI::getInstance()->getProxTabDados(); ?>"/>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12 col-md-3 col-lg-3 col-xl-2">
            <label class="infraLabelObrigatorio" for="txtBairro">
                Bairro:
            </label>
            <input type="text" class="infraText blocInformacaoPj" id="txtBairro" name="txtBairro" maxlength="70" onkeypress="return infraMascaraTexto(this,event, 70);"
                <?php echo $readOnly?>
                   value="<?php echo !is_null($arrContatoDTO) ? $arrContatoDTO->getStrBairro() : null?>"
                   tabindex="<?= PaginaSEI::getInstance()->getProxTabDados(); ?>"/>
        </div>
        <div class="col-sm-12 col-md-2 col-lg-2 col-xl-1">
            <label class="infraLabelObrigatorio" for="slUf">
                UF:
            </label>
            <select class="infraSelect blocInformacaoPj" id="slUf" name="slUf"
                <?php echo !is_null($readOnly) ? 'disabled="disabled"' : null?>
                    tabindex="<?= PaginaSEI::getInstance()->getProxTabDados(); ?>">
                <?php echo $slUf;?>
            </select>
            <?php if(!is_null($readOnly)){?>
                <input type="hidden"
                       name="slUf"
                       id="slUf"
                       value="<?php echo $arrContatoDTO->getStrSiglaUf()?>"/>
            <?php }?>
        </div>
        <div class="col-sm-12 col-md-2 col-lg-2 col-xl-1">
            <label class="infraLabelObrigatorio" for="selCidade">
                Cidade:
            </label>
            <select class="infraSelect blocInformacaoPj" name="selCidade" id="selCidade"
                <?php echo !is_null($readOnly) ? 'disabled="disabled"' : null?>
                    tabindex="<?=PaginaSEIExterna::getInstance()->getProxTabDados()?>"/>
            <?= $strItensSelCidade ?>
            </select>
        </div>
        <div class="col-sm-12 col-md-3 col-lg-3 col-xl-2">
            <label class="infraLabelObrigatorio" for="txtNumeroCEP">
                CEP:
            </label>
            <input type="text" class="infraText blocInformacaoPj" id="txtNumeroCEP" name="txtNumeroCEP" maxlength="15"
                   onkeypress="return infraMascaraNumero(this,event, 9);"
                   onkeyup="return infraMascara(this, event, '#####-###');" onchange="return controlarMascaraCep(this);"
                <?php echo $readOnly?>
                   value="<?php echo !is_null($arrContatoDTO) ? $arrContatoDTO->getStrCep() : null?>"
                   tabindex="<?= PaginaSEI::getInstance()->getProxTabDados(); ?>"/>
        </div>
    </div>
    <input type="hidden" name="hdnInformacaoPj" id="hdnInformacaoPj"/>
    <input type="hidden" name="isAlteracaoResponsavelLegal" id="isAlteracaoResponsavelLegal" value="0"/>
    <input type="hidden" name="txtMotivoAlteracaoRespLegal" id="txtMotivoAlteracaoRespLegal" value=""/>
    <input type="hidden" name="hdnValidadoCnpj" id="hdnValidadoCnpj" value="0"/>
    <br/>
</fieldset>
</br>