<?php
/**
 * ANATEL
 *
 * Restricao do Tipo de Processo do Peticionamento Interno por Orgaos e Unidades, no desenho da restricao do
 * Tipo de Processo do core (OrgaoINT::montarRestricaoOrgaoUnidade): Orgao sem Unidade libera o Orgao inteiro;
 * Orgao com Unidades libera so elas; nenhum Orgao libera todos.
 *
 * 08/09/2026 - criado por emerson_b.goncalves - ANATEL
 */
?>
<div class="row ml-0 mb-3">
    <div class="col-sm-12 col-md-6 col-lg-6">
        <div class="form-group">
            <label id="lblOrgaos" for="selOrgaos" class="infraLabelOpcional">Restrito a Órgãos:
                <img align="top" class="infraImg" name="ajuda"
                     src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/ajuda.svg"
                     <?= PaginaSEI::montarTitleTooltip('Campo opcional. Sem nenhum Órgão indicado, o Tipo de Processo fica disponível para todos os Órgãos e Unidades.' . "\n\n" . 'Indique Órgãos para disponibilizar o Tipo de Processo somente às Unidades dos Órgãos indicados e, ainda, poder limitar a Unidades específicas dentro de cada Órgão indicado.', 'Ajuda') ?> />
            </label>
            <?php if (!$bolConsultar) { ?>
                <input type="text" id="txtOrgao" name="txtOrgao" class="infraText form-control mb-1"
                       tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
            <?php } ?>
            <div class="d-flex">
                <select id="selOrgaos" name="selOrgaos" size="6" multiple="multiple" class="infraSelect form-select"
                        onchange="trocarOrgaoRestricao();" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>">
                    <?php foreach ($arrRestricao['orgaos'] as $numIdOrgaoRestricao => $strRotuloOrgao) { ?>
                        <option value="<?= (int) $numIdOrgaoRestricao ?>"><?= PaginaSEI::tratarHTML($strRotuloOrgao) ?></option>
                    <?php } ?>
                </select>
                <?php if (!$bolConsultar) { ?>
                    <div class="ml-1">
                        <img id="imgLupaOrgaos" onclick="objLupaOrgaos.selecionar(700, 500);"
                             src="<?= PaginaSEI::getInstance()->getIconePesquisar() ?>"
                             alt="Selecionar Órgãos" title="Selecionar Órgãos" class="infraImg"/>
                        <br/>
                        <img id="imgExcluirOrgaos" onclick="objLupaOrgaos.remover();"
                             src="<?= PaginaSEI::getInstance()->getIconeRemover() ?>"
                             alt="Remover Órgãos Selecionados" title="Remover Órgãos Selecionados" class="infraImg"/>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
    <div class="col-sm-12 col-md-6 col-lg-6">
        <div class="form-group">
            <label id="lblUnidades" for="selUnidades" class="infraLabelOpcional">Restrito a Unidades:
                <img align="top" class="infraImg" name="ajuda"
                     src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/ajuda.svg"
                     <?= PaginaSEI::montarTitleTooltip('Campo opcional, que depende de um Órgão selecionado na lista ao lado de restrição de uso: aqui aparecem só as Unidades dos Órgãos indicados no campo ao lado de restrição de uso.' . "\n\n" . 'Sem Unidade indicada, o Órgão inteiro tem acesso ao Tipo de Processo no Peticionamento Interno. Com Unidades indicadas, apenas elas poderão utilizar o Tipo de Processo correspondente.', 'Ajuda') ?> />
            </label>
            <?php if (!$bolConsultar) { ?>
                <input type="text" id="txtUnidade" name="txtUnidade" class="infraText form-control mb-1"
                       tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
            <?php } ?>
            <div class="d-flex">
                <select id="selUnidades" name="selUnidades" size="6" multiple="multiple" class="infraSelect form-select"
                        tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>">
                </select>
                <?php if (!$bolConsultar) { ?>
                    <div class="ml-1">
                        <img id="imgLupaUnidades" onclick="objLupaUnidades.selecionar(700, 500);"
                             src="<?= PaginaSEI::getInstance()->getIconePesquisar() ?>"
                             alt="Selecionar Unidades" title="Selecionar Unidades" class="infraImg"/>
                        <br/>
                        <img id="imgExcluirUnidades" onclick="objLupaUnidades.remover();"
                             src="<?= PaginaSEI::getInstance()->getIconeRemover() ?>"
                             alt="Remover Unidades Selecionadas" title="Remover Unidades Selecionadas" class="infraImg"/>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>

<input type="hidden" id="hdnOrgaos" name="hdnOrgaos" value="<?= PaginaSEI::tratarHTML(MdPetPiTpProcessoINT::gerarItensLupa($arrRestricao['orgaos'])) ?>"/>
<input type="hidden" id="hdnUnidades" name="hdnUnidades" value=""/>
<input type="hidden" id="hdnIdOrgao" name="hdnIdOrgao" value=""/>
<input type="hidden" id="hdnIdUnidade" name="hdnIdUnidade" value=""/>

<?php foreach ($arrLinksUnidadePorOrgao as $numIdOrgaoLink => $strLinkUnidadeOrgao) { ?>
    <input type="hidden" id="hdnOrgao<?= (int) $numIdOrgaoLink ?>" name="hdnOrgao<?= (int) $numIdOrgaoLink ?>"
           value="<?= PaginaSEI::tratarHTML(MdPetPiTpProcessoINT::gerarItensLupa($arrRestricao['unidades'][$numIdOrgaoLink] ?? [])) ?>"/>
    <input type="hidden" id="lnkOrgao<?= (int) $numIdOrgaoLink ?>" value="<?= PaginaSEI::tratarHTML($strLinkUnidadeOrgao) ?>"/>
<?php } ?>
