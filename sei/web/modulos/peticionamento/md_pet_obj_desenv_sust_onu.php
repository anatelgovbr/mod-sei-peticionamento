<?php SessaoSEIExterna::getInstance()->removerAtributo('METAS_SELECIONADAS'); ?>

<? if( PeticionamentoIntegracao::verificaSeModIAVersaoMinima() && PeticionamentoIntegracao::permitirClassificacaoODSUsuarioExterno() ): ?>

<div class="row" id="filset-onu" style="padding-bottom: 15px;">
    <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
        <fieldset id="objDesenvSustONU" class="infraFieldset" style="padding: 15px">
            <legend class="infraLegend">&nbsp; Objetivos de Desenvolvimento Sustentável da ONU &nbsp;</legend>
            <label class="d-block">
                Os Objetivos de Desenvolvimento Sustentável da ONU são um apelo global à ação para acabar com a pobreza, proteger o meio ambiente e o clima e garantir que as pessoas, em todos os lugares, possam desfrutar de paz e de prosperidade (<a
                        href="https://brasil.un.org/pt-br/sdgs" target="_blank">https://brasil.un.org/pt-br/sdgs</a>).
            </label>
            <div class="row">
                <div class="col-12 text-center my-3">
                    <img class="img-fluid" title="Acessando esta imagem é possível classificar o Processo com as Metas dos Objetivos de Desenvolvimento Sustentável da ONU." src="modulos/ia/imagens/logo_ods_onu.png" onclick="abrirModal()"/>
                </div>
            </div>
        </fieldset>
    </div>
</div>

<script type="text/javascript">

    function abrirModal() {
        infraAbrirJanelaModal("<?= SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=md_pet_classificar_ods') ?>",
            1200,
            1000, false);
    }

</script>

<? endif; ?>
