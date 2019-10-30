<?php
    $idVinculo = $arrDadosPessoaJuridicaVinculo->getNumIdMdPetVinculo();
    $readOnly = $stWebService ? 'readonly="readonly"' : null;
    $readOnlyConsultar = $stConsultar ? 'readonly="readonly"' : null;
?>
<style type="text/css">
#field2 {height: auto; width: 96%; margin-bottom: 11px;}
.sizeFieldset {height:auto; width: 86%;}
.fieldsetClear {border:none !important;}
</style>
<fieldset style="width: auto;" id="field2" class="infraFieldset sizeFieldset">
    <legend class="infraLegend">&nbsp; Registro da Pessoa Jurídica &nbsp;</legend>
    <div style="clear:both;margin-top: 7px;"></div>
    <div class="bloco">
        <label>Os dados aqui dispostos dizem respeito ao vínculo mais recente estabelecido no SEI-<?=$siglaOrgao?> entre o Responsável Legal e a Pessoa Jurídica. É permitido atualizar os Atos Constitutivos por meio da seção mais abaixo.</label>
    </div>
    <div class="clear"></div>
    <div id="container">
        <div class="bloco" id="blcPj">
            <label class="infraLabelObrigatorio" 
                   for="txtNumeroCnpj" 
                   id="lblNumeroCnpj">
                CNPJ:
            </label>
            <input type="text" class="infraText" 
                   id="txtNumeroCnpj"
                   name="txtNumeroCnpj" maxlength="18"
                   readonly="readonly"
                   value="<?php echo !is_null($arrDadosPessoaJuridicaVinculo) ? InfraUtil::formatarCnpj($arrDadosPessoaJuridicaVinculo->getDblCNPJ()) : null; ?>"
                   onkeypress="return infraMascaraCnpj(this,event);"
                   tabindex="<?= PaginaSEI::getInstance()->getProxTabDados(); ?>"/>
        </div>
        <div class="bloco" id="blcPjRazaoSocial">
            <label class="infraLabelObrigatorio" 
                   for="txtRazaoSocial" 
                   id="lblRazaoSocial">
                Razão Social:
            </label>
            <input type="text" 
                   class="infraText blocInformacaoPj" 
                   id="txtRazaoSocialAlt"
                   name="txtRazaoSocialAlt"
                   readonly="readonly"
                   <?php echo $readOnly ?>
                   value="<?php echo !is_null($arrDadosPessoaJuridicaVinculo) ? $arrDadosPessoaJuridicaVinculo->getStrRazaoSocialNomeVinc() : null; ?>"
                   onkeypress="return infraMascaraCnpj(this,event);"
                   tabindex="<?= PaginaSEI::getInstance()->getProxTabDados(); ?>"/>
        </div>
      <?php if (!$stConsultar) { ?>
          <div class="bloco" id="blcPjBtnAlterar">
            <label class="infraLabelObrigatorio" for="txt">&nbsp;</label>          
            <button tabindex="<?= PaginaSEI::getInstance()->getProxTabDados(); ?>" 
                    type="button" 
                    name="btnAlterarLegal" 
                    onclick="alterarResponsavel()" 
                    class="infraButton">
                    Alterar
            </button>
          </div>
      <?php } ?>
    </div>

    <div class="clear"></div>

    <div class="bloco" id="blcInfCPFResponsavel">
        <label class="infraLabelObrigatorio" for="txtNumeroCpfResponsavelAlt">
             CPF do Responsável Legal:
        </label>
        <input type="text" class="infraText" id="txtNumeroCpfResponsavelAlt" name="txtNumeroCpfResponsavelAlt"         
               readonly 
               onchange="validarCpf(this);"
               tabindex="<?= PaginaSEI::getInstance()->getProxTabDados(); ?>"/>
    </div>
    <div class="bloco" id="blcInfNomeResponsavel">
        <label class="infraLabelObrigatorio" for="txtNomeResponsavelLegalAlt">
            Nome do Responsável Legal:
        </label>
        <input type="text" class="infraText" id="txtNomeResponsavelLegalAlt" name="txtNomeResponsavelLegalAlt" maxlength="250"         
               readonly
               tabindex="<?= PaginaSEI::getInstance()->getProxTabDados(); ?>"/>
    </div>

    <div class="clear"></div>

    <div class="bloco">
        <label class="infraLabelObrigatorio" for="txt">Atenção:</label>
        <label>
            <ol class="Numerada" style="margin-top: 0%;">
                <li class="Numerada">Somente quem é de fato Responsável Legal pela Pessoa Jurídica junto à Receita Federal do Brasil (RFB) pode exercer a presente vinculação.</li>
                <li class="Numerada">Ao efetivar a vinculação como Responsável Legal, no âmbito do(a) <?=$descricaoOrgao?>, você terá poderes para:
                <ol class="Numerada">
                    <li class="Numerada">Gerenciar o cadastro da Pessoa Jurídica;</li>
                    <li class="Numerada">Receber Intimações Eletrônicas e realizar Peticionamento Eletrônico em nome da Pessoa Jurídica, com todos os poderes previstos no sistema;</li>
                    <li class="Numerada">Conceder Procurações Eletrônicas Especiais a outros Usuários Externos, bem como revogá-las quando lhe convier;</li>
                    <li class="Numerada">Conceder Procurações Eletrônicas a outros Usuários Externos, em âmbito geral ou para processos específicos, conforme poderes estabelecidos, para representação da Pessoa Jurídica Outorgante, bem como revogá-las quando lhe convier.</li>
                </ol>
                </li>
                <li class="Numerada">É sua responsabilidade zelar pela veracidade e validade dos dados sobre a Pessoa Jurídica à qual se vincula no âmbito do SEI-<?=$siglaOrgao?>.</li>
            </ol>
        </label>
    </div>

    <div class="clear"></div>

    <?php
    if(!$stWebService && $stConsultar){ ?>
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
    <?php } ?>
    <div class="clear"></div>
    <div class="bloco"></div>
    <input type="hidden" id="hdnTextoDeclaracaoFormatado" name="hdnTextoDeclaracaoFormatado" value='<?php echo $textoFormatadoDeclaracao; ?>'/>
    <input type="hidden"  id="hdnTextoDeclaracaoDestaque" name="hdnTextoDeclaracaoDestaque" value='<?php echo $textoDestaqueDeclaracao; ?>'/>
    <input type="hidden" name="hdnIdContatoNovo" id="hdnIdContatoNovo"  value=""/>
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