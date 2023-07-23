<?php
$idVinculo = $arrDadosPessoaJuridicaVinculo->getNumIdMdPetVinculo();
$readOnly = $stWebService ? 'readonly="readonly"' : null;
$readOnlyConsultar = $stConsultar ? 'readonly="readonly"' : null;
?>
<fieldset id="registroPessoaJuridica" class="infraFieldset form-control sizeFieldset">
    <legend class="infraLegend">&nbsp; Registro da Pessoa Jur�dica &nbsp;</legend>
    <div class="row">
        <div class="col-sm-12 col-md-12 col-lg-12">
            <p>
                <label>Os dados aqui dispostos dizem respeito ao v�nculo mais recente estabelecido no
                    SEI-<?= $siglaOrgao ?>
                    entre o Respons�vel Legal e a Pessoa Jur�dica. � permitido atualizar os Atos Constitutivos por meio
                    da
                    se��o
                    mais abaixo.</label>
            </p>
            <p>
                <label class="infraLabelObrigatorio">Aten��o: </label>
                <label>Somente por meio da tela anterior, acessando o bot�o "Novo Respons�vel Legal" por Usu�rio Externo
                    que j� conste como Respons�vel Legal junto � Receita Federal que � poss�vel a altera��o do
                    Respons�vel Legal da Pessoa Jur�dica.</label>
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
                Raz�o Social:
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
                CPF do Respons�vel Legal:
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
                Nome do Respons�vel Legal:
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
            <label class="infraLabelObrigatorio" for="txt">Aten��o:</label>
            <ol class="Numerada" style="margin-top: 0%;">
                <li class="Numerada">Somente quem � de fato Respons�vel Legal pela Pessoa Jur�dica junto � Receita
                    Federal do Brasil (RFB) pode exercer a presente vincula��o.
                </li>
                <li class="Numerada">Ao efetivar a vincula��o como Respons�vel Legal, no �mbito
                    do(a) <?= $descricaoOrgao ?>, voc� ter� poderes para:
                    <ol class="Numerada">
                        <li class="Numerada">Gerenciar o cadastro da Pessoa Jur�dica;</li>
                        <li class="Numerada">Receber Intima��es Eletr�nicas e realizar Peticionamento Eletr�nico em
                            nome
                            da Pessoa Jur�dica, com todos os poderes previstos no sistema;
                        </li>
                        <li class="Numerada">Conceder Procura��es Eletr�nicas Especiais a outros Usu�rios Externos,
                            bem
                            como revog�-las quando lhe convier;
                        </li>
                        <li class="Numerada">Conceder Procura��es Eletr�nicas Simples a outros Usu�rios Externos, em
                            �mbito geral ou para processos espec�ficos, conforme poderes estabelecidos, para
                            representa��o da Pessoa Jur�dica Outorgante, bem como revog�-las quando lhe convier.
                        </li>
                    </ol>
                </li>
                <li class="Numerada">� sua responsabilidade zelar pela veracidade e validade dos dados sobre a
                    Pessoa
                    Jur�dica � qual se vincula no �mbito do SEI-<?= $siglaOrgao ?>.
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
                        <span style="font-weight: bold"> Declaro </span> ser o Respons�vel Legal pela Pessoa Jur�dica cujo CNPJ informei e que concordo com os termos acima dispostos. Declaro ainda estar ciente de que o ato de inserir ou fazer inserir declara��o falsa ou diversa da que devia ser escrita � crime, conforme disposto no art. 299 do C�digo Penal Brasileiro.
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
