<fieldset id="fieldsetPessoaJuridicaConsulta" class="infraFieldset form-control sizeFieldset" style="width: auto;">
    <legend class="infraLegend">&nbsp; Registro da Pessoa Jurídica &nbsp;</legend>
    <form name=frmCNPJ id=frmCNPJ action='' method=POST><input type="hidden" name="hdnNumeroCnpj" id="hdnNumeroCnpj"></input></form>
    <div class="infraAreaDados">
        <?php $idDiv = $stWebService ? 'blcPj' : 'blcPjSemWs' ?>
        <form id="formCaptcha" name="formCaptcha" method="post"
                action="<?=SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao='.$_GET['acao'].'&acao_origem='.$_GET['acao'].'&lang='.$locale); ?>"
                onsubmit="return OnSubmitForm();">
        <div class="row">
            <div class="col-sm-12 col-md-6 col-lg-6 col-xl-6">
                <div id="<?php echo $idDiv ?>">
                    <div class="form-group mb-3">
                        <label class="infraLabelObrigatorio" for="txtNumeroCnpj" id="lblNumeroCnpj">CNPJ:
                            <img style="margin-bottom: -4px;width:20px; height:20px !important" src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/ajuda.svg" name="ajuda" <?= PaginaSEI::montarTitleTooltip('Insira no campo abaixo o CNPJ da Pessoa Jurídica à qual deseja se vincular.', 'Ajuda') ?> alt="Ajuda" class="infraImg" />
                        </label>
                        <input type="text" class="infraText form-control" id="txtNumeroCnpj" onchange="esconderCamposPJ();" name="txtNumeroCnpj" maxlength="18" value="<?php echo !is_null($arrDadosPessoaJuridicaVinculo) ? InfraUtil::formatarCnpj($arrDadosPessoaJuridicaVinculo->getStrCNPJ()) : $hdnNumeroCnpj; ?>" onkeypress="return infraMascaraCnpj(this,event);" tabindex="<?= PaginaSEIExterna::getInstance()->getProxTabDados(); ?>" autofocus />
                    </div>
                </div>
            </div>
            <?php if ($stWebService) { ?>
                <div class="col-sm-12 col-md-6 col-lg-6 col-xl-6">
                    <div id="blcPjRazaoSocial" class="form-group mb-3">
                        <label class="infraLabelObrigatorio" for="txtRazaoSocialWsdl" id="lblNumeroCnpj">Razão
                            Social:</label>
                        <input type="text" class="infraText form-control blocInformacaoPj" id="txtRazaoSocialWsdl" name="txtRazaoSocialWsdl" readonly />
                    </div>
                </div>
        </div>
        <div class="row">
            <div class="col-sm-6 col-md-6 col-lg-6 col-xl-6">
                <div class="card" id="cardCaptcha">
                    <?= CaptchaSEI::getInstance()->montarHtml(PaginaSEIExterna::getInstance()->getProxTabDados()); ?>
                    <div class="card-body">
                        <div class="form-group">
                            <div class="input-group" style="margin: 0 !important;">
                                <input type="hidden" name="hdnCapcha" id="hdnCapcha" value="1">
                                <input type="hidden" name="hdnWS" id="hdnWS" value="1">
                                <input type="submit" accesskey="V" name="btnValidar" id="btnValidar" value="Validar" class="infraButton" tabindex="<?= PaginaSEIExterna::getInstance()->getProxTabDados(); ?>" style="width: 100%;" Value="Validar">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php } else { ?>
        </div>
        <div class="row">
                <div class="col-sm-6 col-md-6 col-lg-6 col-xl-6">
                    <div class="" id="blc">
                        <div class="card" id="cardCaptcha">
                            <?= CaptchaSEI::getInstance()->montarHtml(PaginaSEIExterna::getInstance()->getProxTabDados()); ?>
                            <div class="card-body">
                                <div class="form-group">
                                    <div class="input-group">
                                        <input type="hidden" name="hdnCapcha" id="hdnCapcha" value="1">
                                        <input type="hidden" name="hdnWS" id="hdnWS" value="0">
                                        <input type="submit" accesskey="V" name="btnValidarSemWS" id="btnValidarSemWS" value="Validar" onclick="" class="infraButton" style="width: 100%;" Value="Validar" tabindex="<?= PaginaSEIExterna::getInstance()->getProxTabDados(); ?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php } ?>
            </div>
        </form>
    </div>

    <div class="bloco">
        <label class="infraLabelObrigatorio" for="txtComplementoTipoDocumento">Atenção:</label>
        <label>
            <ol class="Numerada" style="margin-top: 0%;">
                <li class="Numerada">Somente quem é de fato Responsável Legal pela Pessoa Jurídica junto à Receita
                    Federal do Brasil (RFB) pode exercer a presente vinculação.
                </li>
                <li class="Numerada">Ao efetivar a vinculação como Responsável Legal, no âmbito
                    do(a) <?= $descricaoOrgao ?>, você terá poderes para:
                    <ol class="Numerada">
                        <li class="Numerada">Gerenciar o cadastro da Pessoa Jurídica;</li>
                        <li class="Numerada">Receber Intimações Eletrônicas e realizar Peticionamento Eletrônico em nome
                            da Pessoa Jurídica, com todos os poderes previstos no sistema;
                        </li>
                        <li class="Numerada">Conceder Procurações Eletrônicas Especiais a outros Usuários Externos, bem
                            como revogá-las quando lhe convier;
                        </li>
                        <li class="Numerada">Conceder Procurações Eletrônicas Simples a outros Usuários Externos, em
                            âmbito geral ou para processos específicos, conforme poderes estabelecidos, para
                            representação da Pessoa Jurídica Outorgante, bem como revogá-las quando lhe convier.
                        </li>
                    </ol>
                </li>
                <li class="Numerada">É sua responsabilidade zelar pela veracidade e validade dos dados sobre a Pessoa
                    Jurídica à qual se vincula no âmbito do SEI-<?= $siglaOrgao ?>.
                </li>
            </ol>
        </label>
    </div>

    <div class="clear"></div>

    <div id="stDeclaracao" style="display:none">
        <div class="bloco" id="blcDeclaracaoCheck" style="width:3%; min-width: 25px">
            <input type=checkbox id="chkDeclaracao" value="S" class="infraCheckbox" tabindex="<?= PaginaSEIExterna::getInstance()->getProxTabDados(); ?>" onchange="//mostrarEsconderCampos(this)" style="margin-top: 30%;margin-left: 30%;">
        </div>
        <div class="bloco" id="blcDeclaracao" style="width:93%">
            <label id="lblDeclaracao"><span id="textoDeclaracao"><?= $textoFormatadoDeclaracao ?></span></label>
        </div>
        <div class="clear"></div>
    </div>

</fieldset>
<br />

<input type="hidden" id="hdnTextoDeclaracaoFormatado" name="hdnTextoDeclaracaoFormatado" value='<?php echo $textoFormatadoDeclaracao; ?>' />
<input type="hidden" id="hdnTextoDeclaracaoDestaque" name="hdnTextoDeclaracaoDestaque" value='<?php echo $textoDestaqueDeclaracao; ?>' />

<input type="hidden" id="hdnIsCnpjValidado" name="hdnIsCnpjValidado" value="0" />
<input type="hidden" id="hdnIdContatoNovo" name="hdnIdContatoNovo" value="" />
<input type="hidden" id="hdnValorCaptcha" name="hdnValorCaptcha" value="">
