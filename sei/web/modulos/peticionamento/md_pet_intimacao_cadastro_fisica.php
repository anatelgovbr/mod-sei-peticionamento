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
$arrIntimacoes = $objMdPetIntimacaoRN->buscaIntimacoesCadastradasFisica($idDocumento);
?>
<br>
<input type="hidden" id="intimacoesFisica" value="<?php echo count($arrIntimacoes) ?>"/>
<div class="row">
    <div class="col-sm-12 col-md-10 col-lg-10 col-xl-10">
        <fieldset id="fldDestinatarios" class="infraFieldset sizeFieldset form-control">
            <legend class="infraLegend" class="infraLabelObrigatorio"> Destinatários</legend>
            <!-- Usuario Externo -->
            <div class="row">
                <div class="col-sm-12 col-md-4 col-lg-3 col-xl-3">
                    <label id="lblUsuario" for="txtUsuario" class="infraLabelObrigatorio">Usuário Externo: </label>
                    <img src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal(); ?>/ajuda.svg" name="ajuda"
                         id="imgAjudaUsuario" <?= PaginaSEI::montarTitleTooltip('A pesquisa é realizada somente sobre Usuários Externos liberados. \n \n A pesquisa pode ser efetuada pelo Nome, E-mail ou CPF do Usuário Externo.', 'Ajuda') ?>
                         class="infraImgModulo"/><br>
                    <div class="input-group mb-3">
                        <input style="width:85%;margin-top:1px;" type="text" id="txtUsuario" name="txtUsuario"
                               class="infraText campoPadrao" onkeypress="return infraMascaraTexto(this,event);"
                               tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
                        <img id="imgLupaTipoProcesso" onclick="objLupaTipoProcesso.selecionar(700,500);"
                             src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal(); ?>/pesquisar.svg"
                             alt="Selecionar Usuário Externo"
                             title="Selecionar Usuário Externo" class="infraImg"/>
                        <input type="hidden" id="hdnIdTipoProcesso" name="hdnIdTipoProcesso"
                               value="<?php echo $idTipoProcesso ?>"/>
                        <input type="hidden" id="hdnIdUsuario" name="hdnIdUsuario" value="0"/>
                        <input type="hidden" id="hdnUsuarioNome" name="hdnUsuarioNome">
                        <input type="hidden" id="hdnTipoPessoa" name="hdnTipoPessoa" value="F"/>
                    </div>
                </div>
                <!-- Email -->
                <div class="col-sm-12 col-md-4 col-lg-3 col-xl-3" style="padding-top: 5px">
                    <label id="lblEmail" for="txtEmail" class="infraLabelObrigatorio">E-mail do Usuário Externo:</label>
                    <input type="text" id="txtEmail" name="txtEmail" class="infraText campoPadrao infraAutoCompletar"
                           disabled="disabled" onkeypress="return infraMascaraTexto(this,event,50);" maxlength="50"
                           tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
                </div>
                <!-- Botao Adicionar -->
                <div class="col-sm-12 col-md-4 col-lg-4 col-xl-4">
                    <button type="button" id="sbmGravarUsuario" style="margin-left: -3px;margin-top: 28px;"
                            accesskey="A"
                            name="sbmGravarUsuario" class="infraButton" onclick="transportarUsuario();"
                            value="Adicionar"
                            tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"><span
                                class="infraTeclaAtalho">A</span>dicionar
                    </button>
                </div>
            </div>

            <div class="tabUsuario clear height_2"
                 style="<?php echo $_REQUEST['is_alterar'] ? '' : 'display:none' ?>"></div>
            <!-- Tabela de Destinatários -->
            <div class="row">
                <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                    <div id="divTabelaUsuarioExterno" class="tabUsuario infraAreaTabela"
                         style="<?php echo $_REQUEST['is_alterar'] ? '' : 'display:none' ?>">
                        <table id="tblEnderecosEletronicos" width="100%"
                               summary="Lista de Pessoas Jurídicas disponíveis"
                               class="infraTable">
                            <caption id="test"
                                     class="infraCaption"><?= PaginaSEI::getInstance()->gerarCaptionTabela("Pessoas Físicas disponíveis", count($arrIntimacoes)) ?></caption>
                            <tr>
                                <th style="display:none;">ID</th>
                                <th class="infraTh" width="30%">Destinatário</th>
                                <th class="infraTh">E-mail</th>
                                <th class="infraTh" width="15%">CPF</th>
                                <th class="infraTh" width="10%">Data de Expedição</th>
                                <th class="infraTh" width="10%">Situação da Intimação</th>
                                <th class="infraTh" width="10%">Ações</th>
                            </tr>
                            <? if ($_REQUEST['is_alterar']) { ?>
                                <input type="hidden" id="hdnIdUsuarios" name="hdnIdUsuarios"
                                       value="<?= $arrIntimacoes ?>"/>
                                <? foreach ($arrIntimacoes as $key => $intimacao) {
                                    $countInt++;
                                    ?>
                                    <tr id="changeColor<?php echo $key ?>" class="infraTrClara">
                                        <!--<tr class="<?php echo $key % 2 == 0 ? 'infraTrClara' : 'infraTrEscura'; ?>">-->
                                        <td style="display:none; width: 100px;  "> <?= $intimacao['Id'] ?></td>
                                        <td> <?= $intimacao['Nome'] ?></td>
                                        <td> <?= $intimacao['Email'] ?></td>
                                        <td> <?= InfraUtil::formatarCpf($intimacao['Cpf']) ?></td>
                                        <td align="center"> <?= $intimacao['DataIntimacao'] ?></td>
                                        <td> <?= $intimacao['Situacao'] ?></td>
                                        <td align="center"><a href='#'
                                                              onclick="abrirIntimacaoCadastrada('<?= $intimacao['Url'] ?>','<?= $key ?>')">
                                                <img title='Consultar Intimação Eletrônica'
                                                     alt='Consultar Intimação Eletrônica'
                                                     src='<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal(); ?>/consultar.svg'
                                                     class='infraImg'/></a></td>
                                    </tr>
                                <? }
                            } ?>
                        </table>
                        <br/>
                        <input type="hidden" id="hdnIdDadosUsuario" name="hdnIdDadosUsuario"
                               value="<?= $_POST['hdnIdDadosUsuario'] ?>"/>
                        <input type="hidden" id="hdnDadosUsuario" name="hdnDadosUsuario"
                               value="<?= $_POST['hdnDadosUsuario'] ?>"/>

                    </div>
                </div>
            </div>

        </fieldset>
    </div>
</div>
<div id="conteudoHide" style="display: none;">
    <div class="row">
        <div class="col-sm-12 col-md-5 col-lg-4 col-xl-4">
            <label id="lblTipodeIntimacao" for="lblTipodeIntimacao" accesskey="" class="infraLabelObrigatorio">Tipo de
                Intimação:</label>
            <select id="selTipoIntimacao" name="selTipoIntimacao" onchange="mostraTipoResposta(this)"
                    class="campoPadrao infraSelect form-control"
                    tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>">
                <?= $strTipoIntimacao ?>
            </select>
            <input type=hidden name=hdnTipoIntimacao id=hdnTipoIntimacao>
        </div>
    </div>
    <div class="row" id="divTipoResposta" name="divTipoResposta">
        <div class="col-sm-12 col-md-5 col-lg-4 col-xl-4">
            <label id="lblTipodeResposta" for="lblTipodeResposta" class="infraLabelObrigatorio">Tipo de
                Resposta:</label>
            <div class="grid grid_6" id="divSelectTipoResposta"></div>
            <div style="display: none" id="divEspacoResposta" class="clear height_1"></div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12 col-md-10 col-lg-10 col-xl-10">
            <fieldset id="fldDocumentosIntimacao" class="infraFieldset sizeFieldset form-control" style="width: 100%;">
                <legend class="infraLegend" class="infraLabelObrigatorio"> Documentos da Intimação <img
                            style="margin-top:1px; margin-bottom: -3px"
                            src="<?= PaginaSEI::getInstance()->getDiretorioImagensGlobal() ?>/ajuda.gif" name="ajuda"
                            id="imgAjudaAnexos" <?= PaginaSEI::montarTitleTooltip('Considerar-se-á cumprida a Intimação Eletrônica com a consulta ao Documento Principal ou, se indicados, a qualquer um dos Protocolos dos Anexos da Intimação. \n\n Caso a consulta não seja efetuada em até ' . $numNumPrazo . ' dias corridos da data de geração da Intimação Eletrônica, automaticamente ocorrerá seu Cumprimento por Decurso do Prazo Tácito. \n\n\n\n\n O Documento Principal e possíveis Anexos terão o acesso ao seu teor protegidos até o cumprimento da Intimação.', 'Ajuda') ?> />
                </legend>
                <div class="row">
                    <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                        <label id="lblDocPrincIntimacao" for="lblDocPrincIntimacao" class="infraLabelOpcional">Documento
                            Principal
                            da
                            Intimação: <?= DocumentoINT::formatarIdentificacao($objDocumentoDTO) . ' (' . $strProtocoloDocumentoFormatado . ')'; ?></label>
                    </div>
                </div>
                <!-- Documento Principal-->
                <div class="row">
                    <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                        <div class="infraCheckboxDiv ">
                            <input type="checkbox" id="optPossuiAnexo" name="rdoPossuiAnexo" value="S"
                                   onclick="esconderAnexos(this)"
                                   class="infraCheckboxInput" <?= (false ? 'checked="checked"' : '') ?>
                                   tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
                            <label class="infraCheckboxLabel " for="optPossuiAnexo"></label>
                        </div>
                        <label id="lblPossuiAnexo" for="optPossuiAnexo" accesskey="" class="infraLabelCheckbox">Intimação
                            possui
                            Anexos </label>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12 col-md-8 col-lg-8 col-xl-6">
                        <label id="lblAnexosIntimacao" for="lblAnexosIntimacao" accesskey=""
                               class="infraLabelObrigatorio">Protocolos
                            dos Anexos da Intimação:</label>
                        <div class="input-group mb-3">
                            <select onclick="controlarSelected(this);" id="selAnexosIntimacao" style="width: 80%"
                                    name="selAnexosIntimacao" size="7"
                                    class="infraSelect" multiple="multiple"></select>
                            <div class="botoes">
                                <img id="imgLupaAnexos"
                                     onclick="objLupaProtocolosIntimacao.selecionar(700,500);"
                                     src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal(); ?>/pesquisar.svg"
                                     alt="Selecionar Protocolos" title="Selecionar Protocolos" class="infraImg"/>
                                <br/>
                                <img id="imgExcluirAnexos" onclick="objLupaProtocolosIntimacao.remover();"
                                     src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal(); ?>/remover.svg"
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
    <div class="row">
        <div class="col-sm-12 col-md-10 col-lg-10 col-xl-10">
            <fieldset id="flTpAcesso" class="infraFieldset sizeFieldset form-control"
                      style="width:auto; min-height: 125px; margin-top:17px">
                <legend class="infraLegend" class="infraLabelObrigatorio"> Tipo de Acesso Externo</legend>
                <!-- Tipo de Acesso Externo -->
                <div class="row">
                    <div class="col-sm-12 col-md-8 col-lg-8 col-xl-6">
                        <div id="divOptTipoPessoaFisica" class="infraDivRadio">
                            <div class="infraRadioDiv ">
                                <input type="radio" id="optIntegral" name="optIntegral" value="I" class="infraRadio"
                                       onclick="mostrarProtocoloParcial(this)"
                                       tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
                                <label class="infraRadioLabel" for="optIntegral"></label>
                            </div>
                            <span id="spnFisica">
                        <label id="lblIntegral" for="optIntegral" accesskey=""
                               class="infraLabelRadio">Integral </label>
                        <img src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/ajuda.svg"
                             name="ajuda"
                             id="imgAjudaAnexos" <?= PaginaSEI::montarTitleTooltip('Atenção! Toda Intimação Eletrônica ocorre por meio da funcionalidade de Disponibilização de Acesso Externo do SEI. \n\n Selecionando o Tipo de Acesso Externo Integral, TODOS os Protocolos constantes no processo serão disponibilizados ao Destinatário, independentemente de seus Níveis de Acesso, incluindo Protocolos futuros que forem adicionados ao processo. \n\n\n\n\n Para que não ocorra nulidade da Intimação, o Acesso Externo Integral somente poderá ser cancelado depois de cumprida a Intimação e concluído o Prazo Externo correspondente (se indicado para possível Resposta). Caso posteriormente o Acesso Externo Integral utilizado pela Intimação Eletrônica seja cancelado, ele será automaticamente substituído por um Acesso Externo Parcial abrangendo o Documento Principal e possíveis Anexos da Intimação, além de Documentos peticionados pelo próprio Usuário Externo.', 'Ajuda') ?>
                             class="infraImgModulo"/>
                        </span>
                        </div>
                        <div id="divOptTipoPessoaJuridica" class="infraDivRadio">
                            <div class="infraRadioDiv ">
                                <input type="radio" id="optParcial" name="optParcial" value="P" class="infraRadioInput"
                                       onclick="mostrarProtocoloParcial(this)"
                                       tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
                                <label class="infraRadioLabel" for="optParcial"></label>
                            </div>
                            <span id="spnJuridica">
                            <label id="lblParcial" for="optParcial" accesskey=""
                                   class="infraLabelRadio">Parcial </label>
                        <img src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/ajuda.svg"
                             name="ajuda"
                             id="imgAjudaAnexos" <?= PaginaSEI::montarTitleTooltip('Atenção! Toda Intimação Eletrônica ocorre por meio da funcionalidade de Disponibilização de Acesso Externo do SEI. \n\n Selecionando o Tipo de Acesso Externo Parcial, SOMENTE serão disponibilizados ao Destinatário o Documento Principal, os Protocolos dos Anexos da Intimação (se indicados) e os Protocolos adicionados no Acesso Parcial (se indicados). O Documento Principal e Protocolos dos Anexos serão automaticamente incluídos no Acesso Parcial. \n\n\n\n\n Para que não ocorra nulidade da Intimação, o Acesso Externo Parcial não poderá ser alterado nem cancelado. Todos os Protocolos incluídos no Acesso Externo Parcial poderão ser visualizados pelo Destinatário, independentemente de seus Níveis de Acesso, não abrangendo Protocolos futuros que forem adicionados ao processo.', 'Ajuda') ?>
                             class="infraImgModulo"/>
                        </span>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12 col-md-8 col-lg-8 col-xl-6">
                        <label id="lblProtocolosDisponibilizados" for="lblProtocolosDisponibilizados" accesskey=""
                               class="infraLabelObrigatorio">Protocolos Disponibilizados:</label>
                        <div class="input-group mb-3">
                            <select onclick="controlarSelected(this);" style="width: 80%"
                                    id="selProtocolosDisponibilizados"
                                    multiple="multiple" name="selProtocolosDisponibilizados" size="7"
                                    class="infraSelect"></select>
                            <div class="botoes">
                                <img id="imgLupaProtocolos"
                                     onclick="objLupaProtocolosDisponibilizados.selecionar(700,500);"
                                     src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal(); ?>/pesquisar.svg"
                                     alt="Selecionar Protocolos" title="Selecionar Protocolos" class="infraImg"/>
                                <br/>
                                <img id="imgExcluirProtocolos" onclick="objLupaProtocolosDisponibilizados.remover();"
                                     src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal(); ?>/remover.svg"
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

<!-- Hiddens -->
<select style="display: none" multiple="multiple" id="selMainIntimacao" name="selMainIntimacao" size="12"/>
<input type="hidden" id="hdnIsAlterar" name="hdnIsAlterar" value="<?php echo $_REQUEST['is_alterar'] ? '1' : '0' ?>"/>
<input type="hidden" id="hdnCountIntimacoes" name="hdnCountIntimacoes" value="<?php echo $countInt ?>"/>
<input type="hidden" id="hdnProtocolosDisponibilizados" name="hdnProtocolosDisponibilizados"
       value="<?= $_POST['hdnProtocolosDisponibilizados'] ?>"/>
<input type="hidden" id="hdnIdDocumento" name="hdnIdDocumento" value="<?php echo $idDocumento ?>"/>
<input type="hidden" id="hndIdDocumento" name="hndIdDocumento" value="<?= $idDocumento ?>"/>
<input type="hidden" id="hdnIdProcedimento" name="hdnIdProcedimento"
       value="<?= array_key_exists('id_procedimento', $_REQUEST) ? $_REQUEST['id_procedimento'] : $_POST['hdnIdProcedimento'] ?>"/>
<input type="hidden" id="hdnIdsDocAnexo" name="hdnIdsDocAnexo" value=""/>
<input type="hidden" id="hdnIdsDocDisponivel" name="hdnIdsDocDisponivel" value=""/>

<!-- Hiddens das constantes do Acesso Parcial / Integral -->
<input type="hidden" id="hdnStaAcessoParcial" name="hdnStaAcessoParcial"
       value="<?php echo MdPetIntAcessoExternoDocumentoRN::$ACESSO_PARCIAL ?>">
<input type="hidden" id="hdnStaAcessoIntegral" name="hdnStaAcessoIntegral"
       value="<?php echo MdPetIntAcessoExternoDocumentoRN::$ACESSO_INTEGRAL ?>">
<input type="hidden" id="hdnStaSemAcesso" name="hdnStaSemAcesso"
       value="<?php echo MdPetIntAcessoExternoDocumentoRN::$NAO_POSSUI_ACESSO ?>">

<style>

    .bloco {
        float: left;
        margin-top: 1%;
        margin-right: 1%;
    }

</style>