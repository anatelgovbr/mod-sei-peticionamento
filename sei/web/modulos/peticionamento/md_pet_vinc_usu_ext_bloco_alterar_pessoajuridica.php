<?php
$idVinculo = $arrDadosPessoaJuridicaVinculo->getNumIdMdPetVinculo();
$readOnly = $stWebService ? 'readonly="readonly"' : null;
$readOnlyConsultar = $stConsultar ? 'readonly="readonly"' : null;
?>
<fieldset id="registroPessoaJuridica" class="infraFieldset form-control sizeFieldset">
    <legend class="infraLegend">&nbsp; Registro da Pessoa Jurídica &nbsp;</legend>
    <div class="row">
        <div class="col-sm-12 col-md-12 col-lg-12">
            <p>
                <label>Os dados aqui dispostos dizem respeito ao vínculo mais recente estabelecido no
                    SEI-<?= $siglaOrgao ?>
                    entre o Responsável Legal e a Pessoa Jurídica. É permitido atualizar os Atos Constitutivos por meio
                    da
                    seção
                    mais abaixo.</label>
            </p>
            <p>
                <label class="infraLabelObrigatorio">Atenção: </label>
                <label>Somente por meio da tela anterior, acessando o botão "Novo Responsável Legal" por Usuário Externo
                    que já conste como Responsável Legal junto à Receita Federal que é possível a alteração do
                    Responsável Legal da Pessoa Jurídica.</label>
            </p>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12 col-md-5 col-lg-4 col-xl-3">
            <div class="form-group">
            <label class="infraLabelObrigatorio" for="txtNumeroCnpj" id="lblNumeroCnpj">
                CNPJ:
            </label>
            <input type="text" class="infraText" id="txtNumeroCnpj" name="txtNumeroCnpj" maxlength="18" readonly="readonly"
                   value="<?php echo !is_null($arrDadosPessoaJuridicaVinculo) ? InfraUtil::formatarCnpj($arrDadosPessoaJuridicaVinculo->getDblCNPJ()) : null; ?>"
                   onkeypress="return infraMascaraCnpj(this,event);"
                   tabindex="<?= PaginaSEI::getInstance()->getProxTabDados(); ?>"/>
            </div>
        </div>
        <div class="col-sm-12 col-md-7 col-lg-6">
            <div class="form-group">
            <label class="infraLabelObrigatorio" for="txtRazaoSocial" id="lblRazaoSocial">
                Razão Social:
            </label>
            <input type="text" class="infraText blocInformacaoPj" id="txtRazaoSocialAlt" name="txtRazaoSocialAlt" readonly="readonly"
                <?php echo $readOnly ?>
                   value="<?php echo !is_null($arrDadosPessoaJuridicaVinculo) ? $arrDadosPessoaJuridicaVinculo->getStrRazaoSocialNomeVinc() : null; ?>"
                   onkeypress="return infraMascaraCnpj(this,event);"
                   tabindex="<?= PaginaSEI::getInstance()->getProxTabDados(); ?>"/>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12 col-md-5 col-lg-4 col-xl-3">
            <div class="form-group">
            <label class="infraLabelObrigatorio" for="txtNumeroCpfResponsavelAlt">
                CPF do Responsável Legal:
            </label>
            <input type="text" class="infraText" id="txtNumeroCpfResponsavelAlt" name="txtNumeroCpfResponsavelAlt"
                   readonly
                   onchange="validarCpf(this);"
                   tabindex="<?= PaginaSEI::getInstance()->getProxTabDados(); ?>"/>
            </div>
        </div>
        <div class="col-sm-12 col-md-7 col-lg-6">
            <div class="form-gruop">
            <label class="infraLabelObrigatorio" for="txtNomeResponsavelLegalAlt">
                Nome do Responsável Legal:
            </label>
            <input type="text" class="infraText" id="txtNomeResponsavelLegalAlt" name="txtNomeResponsavelLegalAlt"
                   maxlength="250"
                   readonly
                   tabindex="<?= PaginaSEI::getInstance()->getProxTabDados(); ?>"/>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12 col-md-12 col-lg-12" style="margin-top:20;">
            <br />
            <label class="infraLabelObrigatorio" for="txt">Atenção:</label>
            <ol class="Numerada" style="margin-top: 0%;">
                <li class="Numerada">Somente quem é de fato Responsável Legal pela Pessoa Jurídica junto à Receita
                    Federal do Brasil (RFB) pode exercer a presente vinculação.
                </li>
                <li class="Numerada">Ao efetivar a vinculação como Responsável Legal, no âmbito
                    do(a) <?= $descricaoOrgao ?>, você terá poderes para:
                    <ol class="Numerada">
                        <li class="Numerada">Gerenciar o cadastro da Pessoa Jurídica;</li>
                        <li class="Numerada">Receber Intimações Eletrônicas e realizar Peticionamento Eletrônico em
                            nome
                            da Pessoa Jurídica, com todos os poderes previstos no sistema;
                        </li>
                        <li class="Numerada">Conceder Procurações Eletrônicas Especiais a outros Usuários Externos,
                            bem
                            como revogá-las quando lhe convier;
                        </li>
                        <li class="Numerada">Conceder Procurações Eletrônicas Simples a outros Usuários Externos, em
                            âmbito geral ou para processos específicos, conforme poderes estabelecidos, para
                            representação da Pessoa Jurídica Outorgante, bem como revogá-las quando lhe convier.
                        </li>
                    </ol>
                </li>
                <li class="Numerada">É sua responsabilidade zelar pela veracidade e validade dos dados sobre a
                    Pessoa
                    Jurídica à qual se vincula no âmbito do SEI-<?= $siglaOrgao ?>.
                </li>
            </ol>
        </div>
    </div>
    <?php
    if (!$stWebService && $stConsultar) { ?>
        <div class="row">
            <div class="col-sm-12 col-md-12 col-lg-12">
                <div class="bloco" id="stDeclaracao">
                    <label id="lblDeclaracao" <?php echo $stWebService ? 'style="display: none;"' : null ?>>
                        <ul><input type=checkbox
                                   id="chkDeclaracao"
                                   value="S"
                                <?php echo !is_null($readOnlyConsultar) ? 'disabled="disabled"' : null; ?>
                                <?php echo $arrDadosPessoaJuridicaVinculo->getStrSinValidado() == 'S' ? 'checked="checked" disabled="disabled"' : null; ?>/>
                            <span id="textoDeclaracao">
                        <span style="font-weight: bold"> Declaro </span> ser o Responsável Legal pela Pessoa Jurídica cujo CNPJ informei e que concordo com os termos acima dispostos. Declaro ainda estar ciente de que o ato de inserir ou fazer inserir declaração falsa ou diversa da que devia ser escrita é crime, conforme disposto no art. 299 do Código Penal Brasileiro.
                    </span>
                        </ul>
                    </label>
                </div>
            </div>
        </div>
    <?php } ?>
    <input type="hidden" id="hdnTextoDeclaracaoFormatado" name="hdnTextoDeclaracaoFormatado"
           value='<?php echo $textoFormatadoDeclaracao; ?>'/>
    <input type="hidden" id="hdnTextoDeclaracaoDestaque" name="hdnTextoDeclaracaoDestaque"
           value='<?php echo $textoDestaqueDeclaracao; ?>'/>
    <input type="hidden" name="hdnIdContatoNovo" id="hdnIdContatoNovo" value=""/>
    <input type="hidden" name="hdnIsCnpjValidado" id="hdnIsCnpjValidado" value="1"/>
</fieldset>
<br/>
<script>
    function alterarResponsavel() {
        var str = '<?= SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=md_pet_vinc_pessoa_cadastrar&idVinculo=' . $idVinculo) ?>';
        infraAbrirJanela(str, 'cadastrarInteressado', 800, 200, '', true); //modal
        return;
    }
</script>
