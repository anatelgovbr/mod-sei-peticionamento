<?php
$objMdPetIntimacaoRN = new MdPetIntimacaoRN();
$strTipoIntimacao = MdPetIntTipoIntimacaoINT::montarSelectIdMdPetIntTipoIntimacao('0', '', '0');

$idDocumento = isset($_REQUEST['id_documento']) ? $_REQUEST['id_documento'] : $_POST['hdnIdDocumento'];
$objDocumentoDTO = new DocumentoDTO();
$objDocumentoDTO->retDblIdDocumento();
$objDocumentoDTO->retDblIdProcedimento();
$objDocumentoDTO->retNumIdOrgaoUnidadeResponsavel();
$objDocumentoDTO->retStrProtocoloDocumentoFormatado();
$objDocumentoDTO->retStrNomeSerie();
$objDocumentoDTO->retStrNumero();
$objDocumentoDTO->retNumIdSerie();
$objDocumentoDTO->setDblIdDocumento($idDocumento);
$objDocumentoRN = new DocumentoRN();
$objDocumentoDTO = $objDocumentoRN->consultarRN0005($objDocumentoDTO);
$strProtocoloDocumentoFormatado = !is_null($objDocumentoDTO) ? $objDocumentoDTO->getStrProtocoloDocumentoFormatado() : '';

//  Buscar Intimações cadastradas.
$arrIntimacoes = $objMdPetIntimacaoRN->buscaIntimacoesCadastradasJuridico($idDocumento);

$objMdPetIntPrazoTacitaDTO = new MdPetIntPrazoTacitaDTO();
$objMdPetIntPrazoTacitaDTO->setBolExclusaoLogica(false);
$objMdPetIntPrazoTacitaDTO->retTodos();

$objMdPetIntPrazoTacitaRN = new MdPetIntPrazoTacitaRN();
$objMdPetIntPrazoTacitaDTO = $objMdPetIntPrazoTacitaRN->consultar($objMdPetIntPrazoTacitaDTO);
$numNumPrazo = null;
if ( !is_null( $objMdPetIntPrazoTacitaDTO ) ) {
    $numNumPrazo = $objMdPetIntPrazoTacitaDTO->getNumNumPrazo();
}

// Destinatarios em Massa

function printHelp($msg){
    echo '<img src="'.PaginaSEI::getInstance()->getDiretorioSvgGlobal().'/ajuda.svg" name="ajuda" id="imgAjudaUsuario" '.PaginaSEI::montarTitleTooltip($msg, 'Ajuda').' class="infraImgModulo"/>';
}

$idProcedimento             = array_key_exists('id_procedimento', $_REQUEST) ? $_REQUEST['id_procedimento'] : $_POST['hdnIdProcedimento'];
$strLinkAjaxDestinatarios   = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_pet_pessoa_juridica&tipo_selecao=2&id_object=objLupaInteressados&id_documento=' . $idDocumento);
$strLinkInteressados        = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=contato_selecionar&tipo_selecao=2&id_object=objLupaInteressados');

//Pessoa Jurídica - Lupa do multiselect
$strLinkTipoProcessoSelecaoJLote = SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=md_pet_int_usuario_auto_completar_juridica_lote&id_documento=' . $idDocumento);
// Destinatarios em Massa

?>
<style>
    #txtUsuario2 {position:absolute;left:0%;top:18%;width:50%;}
    #selDadosUsuario2 {position:absolute;left:0%;top:43%;width:85%;height:50%;}
    #divOpcoesDadosUsuario2 {position:relative;left:85.2%;top:14.4%;width: 1px}
</style>
<br>
<input type="hidden" id="intimacoes" value="<?php echo count($arrIntimacoes) ?>"/>
<div class="row">
    <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
        <fieldset id="fldDestinatarios" class="infraFieldset sizeFieldset form-control">
            <legend class="infraLegend" class="infraLabelObrigatorio"> Destinatários</legend>

            <!-- Destinatários em Massa START -->

            <div class="row">
                <div class="col-md-12">
                    <div class="text-right">
                        <button type="button"
                                onclick="infraAbrirJanelaModal('<?= SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_pet_intimacao_eletronica_listar&id_procedimento=' . $idProcedimento) ?>' , 1200 , 600 );"
                                class="infraButton"
                                tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"
                                accesskey="V">
                            <span class="infraTeclaAtalho">V</span>er intimações do processo
                        </button>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div id="divInteressados" class="infraAreaDados" style="height:14em;">

                        <div class="form-group mb-0">
                            <label id="lbltxtUsuario2" for="txtUsuario2" class="infraLabelObrigatorio mb-0">Pessoa Jurídica:</label>
                            <?= printHelp('A pesquisa é realizada somente sobre Pessoas Jurídicas que já tenham vinculado pelo menos o Responsável Legal no âmbito do Acesso Externo do SEI. \n \n A consulta pode ser efetuada pela Razão Social ou CNPJ da Pessoa Jurídica.'); ?>
                            <input type="text" id="txtUsuario2" name="txtUsuario2" class="infraText" style="margin-bottom: 0px !important;  margin-top: -14px !important" tabindex="<?=PaginaSEI::getInstance()->getProxTabDados()?>" autofocus/>
                            <select id="selDadosUsuario2" name="selDadosUsuario2" class="infraSelect" multiple="multiple" size="6" style="height: 110px; margin-top: -34px !important" tabindex="<?=PaginaSEI::getInstance()->getProxTabDados()?>">
								<?=$strItensSelParticipante?>
                            </select>
                        </div>

                        <div id="divOpcoesDadosUsuario2">
                            <img id="imgSelecionarGrupo" onclick="objLupaInteressados.selecionar(800,800);" src="<?=PaginaSEI::getInstance()->getIconePesquisar()?>" title="Selecionar Pessoa Jurídica" alt="Selecionar Pessoa Jurídica" class="infraImg" tabindex="<?=PaginaSEI::getInstance()->getProxTabDados()?>" />
                            <img id="imgRemoverInteressados" onclick="objLupaInteressados.remover();" src="<?=PaginaSEI::getInstance()->getIconeRemover()?>" alt="Remover Pessoa Jurídica Selecionada" title="Remover Pessoa Jurídica Selecionada" class="infraImg" tabindex="<?=PaginaSEI::getInstance()->getProxTabDados()?>" />
                        </div>

                    </div>
                    <input type="hidden" id="hdnIdDadosUsuario2" name="hdnIdDadosUsuario2" class="infraText campoPadrao" value="" />
                    <input type="hidden" id="hdnDadosUsuario2" name="hdnDadosUsuario2" class="infraText campoPadrao" value="" />
                </div>
            </div>

            <!-- Destinatários em Massa END -->

            <!-- Todo: Destinatários em Massa Remover -->
            <!-- Pessoa Jurídica -->
            <div class="row" style="display:none">
                <div class="col-sm-12 col-md-7 col-lg-7 col-xl-7">
                    <label id="lblUsuario" for="txtUsuario" class="infraLabelObrigatorio">Pessoa Jurídica: </label>
                    <img src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal(); ?>/ajuda.svg" name="ajuda"
                         id="imgAjudaUsuario" <?= PaginaSEI::montarTitleTooltip('A pesquisa é realizada somente sobre Pessoas Jurídicas que já tenham vinculado pelo menos o Responsável Legal no âmbito do Acesso Externo do SEI. \n \n A consulta pode ser efetuada pela Razão Social ou CNPJ da Pessoa Jurídica.', 'Ajuda') ?>
                         class="infraImgModulo"/><br>
                    <div class="input-group mb-3">
                        <input style="width: 85%; margin-top:1px;" type="text" id="txtUsuario" name="txtUsuario"
                               class="infraText campoPadrao" onkeypress="return infraMascaraTexto(this,event);"
                               tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
                        <img id="imgLupaTipoProcesso"
                             onclick="objLupaJuridico.selecionar(700,500);"
                             src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal(); ?>/pesquisar.svg"
                             alt="Selecionar Pessoa Jurídica"
                             title="Selecionar Pessoa Jurídica" class="infraImg"/>
                    </div>
                    <input type="hidden" id="hdnIdTipoProcesso" name="hdnIdTipoProcesso"
                           value="<?php echo $idTipoProcesso ?>"/>
                    <input type="hidden" id="hdnIdUsuario" name="hdnIdUsuario" value=""/>
                    <input type="hidden" id="hdnTipoPessoa" name="hdnTipoPessoa" value="J"/>
                </div>
                <!-- CNPJ -->
                <div class="col-sm-10 col-md-3 col-lg-3 col-xl-4" style="padding-top: 5px">
                    <label id="lblUsuario" for="txtUsuario"
                           class="infraLabelObrigatorio">CNPJ:</label><br>
                    <input type="text" id="txtEmail" name="txtEmail"
                           class="infraText campoPadrao infraAutoCompletar" disabled="disabled"
                           onkeypress="return infraMascaraTexto(this,event,50);" maxlength="50"
                           tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
                </div>
                <!-- Botao Adicionar -->
                <div class="col-sm-2 col-md-2 col-lg-2 col-xl-1 text-right">
                    <!--<input type="button" id="sbmGravarUsuario" accesskey="A" name="sbmGravarUsuario" class="infraButton" onclick="transportarUsuario();" value="Adicionar" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>-->
                    <button type="button" id="sbmGravarUsuario" style="margin-left: -3px;margin-top: 28px;"
                            accesskey="A"
                            name="sbmGravarUsuario" class="infraButton" onclick="transportarUsuarioJuridico();"
                            value="Adicionar" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"><span
                                class="infraTeclaAtalho">A</span>dicionar
                    </button>
                </div>

                <!--
                 TODO: Mostrar avisos para os usuários com links para as páginas de Procurações Conhecidas da Anatel e da Wiki de como Gerar Intimação Eletrônica
                <div class="grid" width="98%">
                    <br/>
                    <label class="infraLabelObrigatorio">Atenção: Consulte o <a href="https://sistemasnet/wiki/doku.php?id=artigos:processo_eletronico:sei_roteiro_usuario_gerar_intimacao_eletronica" target="_blank" title="Orientações sobre expedição de Intimações Eletrônicas">Artigo na Wiki</a> com orientações sobre expedição de Intimações Eletrônicas. Especialmente quando se tratar de Intimação de Pessoa Jurídica, verifique previamente a lista de <a href="http://integra/Lists/Procuraes%20Conhecidas%20na%20Anatel/AllItems.aspx" target="_blank" title="Acesse a Lista de Procurações Conhecidas">Procurações Conhecidas da Anatel</a> e confira se existe indicação formal para fins de recebimento de intimação.</label>
                </div>
                -->

            </div>

            <!-- Todo: Destinatários em Massa Remover -->
<!--            <div class="tabUsuario clear height_2" style="--><?php //echo $_REQUEST['is_alterar'] ? '' : 'display:none' ?><!--"></div>-->
            <div class="tabUsuario clear height_2" style="display:none"></div>
            <!-- Tabela de Destinatários -->
            <!-- Todo: Destinatários em Massa Remover -->
            <div class="row" style="display: none">
                <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                    <div id="divTabelaUsuarioExterno" class="tabUsuario infraAreaTabela"
                         style="<?php echo $_REQUEST['is_alterar'] ? '' : 'display:none' ?>">
                        <div id="hiddeTable">
                            <table id="tblEnderecosEletronicos" width="100%" summary="Lista de Pessoas Jurídicas disponiveis" class="infraTable">
                                <caption id="test" class="infraCaption"><?= PaginaSEI::getInstance()->gerarCaptionTabela("Pessoas Jurídicas disponiveis", count($arrIntimacoes)) ?></caption>
                                <tr>
                                    <th style="display:none;">ID</th>
                                    <th class="infraTh">Razão Social</th>
                                    <th class="infraTh" width="20%">CNPJ</th>
                                    <th class="infraTh" width="15%">Data de Expedição</th>
                                    <th class="infraTh" width="20%">Situação da Intimação</th>
                                    <th class="infraTh" width="10%">Ações</th>
                                </tr>
                                <? if ($_REQUEST['is_alterar']) { ?>
                                    <input type="hidden" id="hdnIdUsuarios" name="hdnIdUsuarios" value="<?= $arrIntimacoes ?>"/>
                                    <? foreach ($arrIntimacoes as $key => $intimacao) {

                                        $countInt++;

                                        $gerados .= $intimacao['Id'] . "-";
                                        ?>

                                        <tr id="changeColorJuridico<?php echo $key ?>" class="infraTrClara">
                                            <td class="d-none"><?= $intimacao['Id'] ?></td>
                                            <td class="text-center"><?= $intimacao['Nome'] ?></td>
                                            <td class="text-center"><?= InfraUtil::formatarCnpj($intimacao['Cnpj']) ?></td>
                                            <td class="text-center"><?= $intimacao['DataIntimacao'] ?></td>
                                            <td class="text-center"><?= $intimacao['Situacao'] ?></td>
                                            <td class="text-center">
                                                <a href='#' onclick="abrirIntimacaoCadastradaJuridico('<?= $intimacao['Url'] ?>','<?= $key ?>')">
                                                    <img title='Consultar Intimação Eletrônica'
                                                         alt='Consultar Intimação Eletrônica'
                                                         src='<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal(); ?>/consultar.svg'
                                                         class='infraImg'/>
                                                </a>
                                            </td>
                                        </tr>

                                    <? }
                                } ?>
                            </table>
                            <br/>
                        </div>
                        <input type="hidden" id="gerados" value="<?php echo $gerados ?>"/>
                        <input type="hidden" id="hdnIdDadosUsuario" name="hdnIdDadosUsuario" value="<?= $_POST['hdnIdDadosUsuario'] ?>"/>
                        <input type="hidden" id="hdnDadosUsuario" name="hdnDadosUsuario" value="<?= $_POST['hdnDadosUsuario'] ?>"/>
                    </div>
                </div>
            </div>
        </fieldset>
    </div>
</div>
<div id="conteudoHide2" style="display: none;">
    <div class="row">
        <div class="col-sm-12 col-md-6 col-lg-6 col-xl-6">
            <div class="form-group">
                <label id="lblTipodeIntimacao" for="lblTipodeIntimacao" accesskey="" class="infraLabelObrigatorio">Tipo de Intimação:</label>
                <select id="selTipoIntimacao" name="selTipoIntimacao" onchange="mostraTipoResposta(this)" class="campoPadrao infraSelect form-control" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>">
                    <?= $strTipoIntimacao ?>
                </select>
                <input type=hidden name=hdnTipoIntimacao id=hdnTipoIntimacao>
            </div>
        </div>
        <div class="col-sm-12 col-md-6 col-lg-6 col-xl-6" id="divTipoResposta" name="divTipoResposta">
            <div class="form-group">
                <label id="lblTipodeResposta" for="lblTipodeResposta" class="infraLabelObrigatorio">Tipo de
                    Resposta:</label>
                <div id="divSelectTipoResposta"></div>
                <div style="display: none" id="divEspacoResposta" class="clear height_1"></div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div id="hiddeAll2">
                <fieldset id="fldDocumentosIntimacao" class="infraFieldset sizeFieldset form-control" style="width: 100%">
                    <legend class="infraLegend" class="infraLabelOpcional"> Documentos da Intimação
                        <img style="margin-top:1px; margin-bottom: -3px"
                             src="<?= PaginaSEI::getInstance()->getDiretorioImagensGlobal() ?>/ajuda.gif" name="ajuda"
                             id="imgAjudaAnexos" <?= PaginaSEI::montarTitleTooltip('Considerar-se-á cumprida a Intimação Eletrônica com a consulta ao Documento Principal ou, se indicados, a qualquer um dos Protocolos dos Anexos da Intimação. \n\n Caso a consulta não seja efetuada em até ' . $numNumPrazo . ' dias corridos da data de geração da Intimação Eletrônica, automaticamente ocorrerá seu Cumprimento por Decurso do Prazo Tácito. \n\n O Documento Principal e possíveis Anexos terão o acesso ao seu teor protegidos até o cumprimento da Intimação.', 'Ajuda') ?> />
                    </legend>
                    <div class="row">
                        <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                            <!-- Documento Principal-->
                            <label id="lblDocPrincIntimacao" for="lblDocPrincIntimacao" class="infraLabelOpcional">Documento
                                Principal da
                                Intimação: <?= DocumentoINT::formatarIdentificacao($objDocumentoDTO) . ' (' . $strProtocoloDocumentoFormatado . ')'; ?></label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                            <div class="infraCheckboxDiv ">
                                <input type="checkbox" id="optPossuiAnexo" name="rdoPossuiAnexo"
                                       value="S" onclick="esconderAnexos(this)"
                                       class="infraCheckboxInput" <?= (false ? 'checked="checked"' : '') ?>
                                       tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
                                <label class="infraCheckboxLabel " for="optPossuiAnexo"></label></div>
                            <label id="lblPossuiAnexo" for="optPossuiAnexo" accesskey="" class="infraLabelCheckbox">Intimação
                                possui Anexos
                            </label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12 col-md-8 col-lg-8 col-xl-6">
                            <label id="lblAnexosIntimacao" for="lblAnexosIntimacao" accesskey=""
                                   class="infraLabelObrigatorio">Protocolos
                                dos Anexos da Intimação:</label>
                            <div class="input-group mb-3">
                                <select id="selAnexosIntimacao" style="width: 80%"
                                        name="selAnexosIntimacao" size="7"
                                        class="infraSelect" multiple="multiple"></select>
                                <div class="botoes">
                                    <img id="imgLupaAnexos"
                                         onclick="objLupaProtocolosIntimacao.selecionar(700,500);"
                                         src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/pesquisar.svg"
                                         alt="Selecionar Protocolos" title="Selecionar Protocolos" class="infraImg"/>
                                    </br>
                                    <img id="imgExcluirAnexos"
                                         onclick="objLupaProtocolosIntimacao.remover();"
                                         src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/remover.svg"
                                         alt="Remover Protocolos Selecionados" title="Remover Protocolos Selecionados"
                                         class="infraImgNormal"/>
                                </div>
                            </div>
                            <input type="hidden" id="hdnAnexosIntimacao" name="hdnAnexosIntimacao"
                                   value="<?= $_POST['hdnAnexosIntimacao'] ?>"/>
                        </div>
                    </div>
                </fieldset>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
            <div id="hiddeAll1">
                <fieldset id="flTpAcesso" class="infraFieldset sizeFieldset form-control" style="width:auto; min-height: 125px; margin-top:17px">
                    <legend class="infraLegend" class="infraLabelObrigatorio"> Tipo de Acesso Externo</legend>
                    <!-- Tipo de Acesso Externo -->
                    <div class="row">
                        <div class="col-sm-12 col-md-8 col-lg-8 col-xl-6">
                            <div id="divOptTipoPessoaFisica" class="infraDivRadio">
                                <div class="infraRadioDiv ">
                                    <input type="radio" id="optIntegral" name="optIntegral" value="I" class="infraRadio" onclick="mostrarProtocoloParcial(this)" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
                                    <label class="infraRadioLabel" for="optIntegral"></label>
                                </div>
                                <span id="spnFisica">
                                    <label id="lblIntegral" for="optIntegral" accesskey="" class="infraLabelRadio">Integral
                                        <? printHelp('Atenção! Toda Intimação Eletrônica ocorre por meio da funcionalidade de Disponibilização de Acesso Externo do SEI. \n\n Selecionando o Tipo de Acesso Externo Integral, TODOS os Protocolos constantes no processo serão disponibilizados ao Destinatário, independentemente de seus Níveis de Acesso, incluindo Protocolos futuros que forem adicionados ao processo. \n\n Para que não ocorra nulidade da Intimação, o Acesso Externo Integral somente poderá ser cancelado depois de cumprida a Intimação e concluído o Prazo Externo correspondente (se indicado para possível Resposta). Caso posteriormente o Acesso Externo Integral utilizado pela Intimação Eletrônica seja cancelado, ele será automaticamente substituído por um Acesso Externo Parcial abrangendo o Documento Principal e possíveis Anexos da Intimação, além de Documentos peticionados pelo próprio Usuário Externo.'); ?>
                                    </label>
                                </span>
                            </div>
                            <div id="divOptTipoPessoaJuridica" class="infraDivRadio">
                                <div class="infraRadioDiv ">
                                    <input type="radio" id="optParcial" name="optParcial" value="P" class="infraRadioInput" onclick="mostrarProtocoloParcial(this)" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
                                    <label class="infraRadioLabel" for="optParcial"></label>
                                </div>
                                <span id="spnJuridica">
                                    <label id="lblParcial" for="optParcial" accesskey="" class="infraLabelRadio">Parcial  &nbsp;
                                        <? printHelp('Atenção! Toda Intimação Eletrônica ocorre por meio da funcionalidade de Disponibilização de Acesso Externo do SEI. \n\n Selecionando o Tipo de Acesso Externo Parcial, SOMENTE serão disponibilizados ao Destinatário o Documento Principal, os Protocolos dos Anexos da Intimação (se indicados) e os Protocolos adicionados no Acesso Parcial (se indicados). O Documento Principal e Protocolos dos Anexos serão automaticamente incluídos no Acesso Parcial. \n\n Para que não ocorra nulidade da Intimação, o Acesso Externo Parcial não poderá ser alterado nem cancelado. Todos os Protocolos incluídos no Acesso Externo Parcial poderão ser visualizados pelo Destinatário, independentemente de seus Níveis de Acesso, não abrangendo Protocolos futuros que forem adicionados ao processo.'); ?>
                                    </label>
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Protocolos Disponíveis -->
                    <div class="row">
                        <div class="col-sm-12 col-md-8 col-lg-8 col-xl-6">
                            <label id="lblProtocolosDisponibilizados" for="lblProtocolosDisponibilizados" accesskey="" class="infraLabelObrigatorio">Protocolos Disponibilizados:</label>
                            <div class="input-group mb-3">
                                <select style="width: 80%" id="selProtocolosDisponibilizados" multiple="multiple" name="selProtocolosDisponibilizados" size="7" class="infraSelect"></select>
                                <div class="botoes">
                                    <img id="imgLupaProtocolos" onclick="objLupaProtocolosDisponibilizados.selecionar(700,500);" src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/pesquisar.svg" alt="Selecionar Protocolos" title="Selecionar Protocolos" class="infraImg"/>
                                    </br>
                                    <img id="imgExcluirProtocolos" onclick="objLupaProtocolosDisponibilizados.remover();" src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/remover.svg" alt="Remover Protocolos Selecionados" title="Remover Protocolos Selecionados" class="infraImgNormal"/>
                                </div>
                            </div>
                        </div>
                    </div>
                </fieldset>
            </div>
        </div>
    </div>
</div>

<!-- Hiddens -->
<select style="display: none" multiple="multiple" id="selMainIntimacao" name="selMainIntimacao" size="12"/>
<select style="display: none" multiple="multiple" id="selMainIntimacaoProtocoloDisponibilizado" name="selMainIntimacaoProtocoloDisponibilizado" size="12"/>
<input type="hidden" id="hdnIsAlterar" name="hdnIsAlterar" value="<?php echo $_REQUEST['is_alterar'] ? '1' : '0' ?>"/>
<input type="hidden" id="hdnCountIntimacoes" name="hdnCountIntimacoes" value="<?php echo $countInt ?>"/>
<input type="hidden" id="hdnProtocolosDisponibilizados" name="hdnProtocolosDisponibilizados" value="<?= $_POST['hdnProtocolosDisponibilizados'] ?>"/>
<input type="hidden" id="hdnIdDocumento" name="hdnIdDocumento" value="<?php echo $idDocumento ?>"/>
<input type="hidden" id="hndIdDocumento" name="hndIdDocumento" value="<?= $idDocumento ?>"/>
<input type="hidden" id="hdnIdProcedimento" name="hdnIdProcedimento" value="<?= array_key_exists('id_procedimento', $_REQUEST) ? $_REQUEST['id_procedimento'] : $_POST['hdnIdProcedimento'] ?>"/>
<input type="hidden" id="hdnIdsDocAnexo" name="hdnIdsDocAnexo" value=""/>
<input type="hidden" id="hdnIdsDocDisponivel" name="hdnIdsDocDisponivel" value=""/>

<!-- Hiddens das constantes do Acesso Parcial / Integral -->
<input type="hidden" id="hdnStaAcessoParcial" name="hdnStaAcessoParcial" value="<?php echo MdPetIntAcessoExternoDocumentoRN::$ACESSO_PARCIAL ?>">
<input type="hidden" id="hdnStaAcessoIntegral" name="hdnStaAcessoIntegral" value="<?php echo MdPetIntAcessoExternoDocumentoRN::$ACESSO_INTEGRAL ?>">
<input type="hidden" id="hdnStaSemAcesso" name="hdnStaSemAcesso" value="<?php echo MdPetIntAcessoExternoDocumentoRN::$NAO_POSSUI_ACESSO ?>">

<style>
    .bloco { float: left; margin-top: 1%;  margin-right: 1%; }
</style>

<? require_once 'md_pet_intimacao_cadastro_pj_js.php'; ?>
