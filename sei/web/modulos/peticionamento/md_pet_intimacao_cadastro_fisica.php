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
    
    //  Buscar Intima��es cadastradas.
    $arrIntimacoes = $objMdPetIntimacaoRN->buscaIntimacoesCadastradasFisica($idDocumento);
?>
<br>
        <input type="hidden" id="intimacoesFisica" value="<?php echo count($arrIntimacoes) ?>" />
        <fieldset id="fldDestinatarios" class="infraFieldset sizeFieldset" style="width:auto">
            <legend class="infraLegend" class="infraLabelObrigatorio"> Destinat�rios</legend>
                <!-- Usuario Externo -->
                <div class="grid_12">
                    <div class="grid grid_5-0">                    
                        <label id="lblUsuario" for="txtUsuario"class="infraLabelObrigatorio">Usu�rio Externo: </label>
                        <img style="margin-bottom:-3px;" src="<?= PaginaSEI::getInstance()->getDiretorioImagensGlobal() ?>/ajuda.gif" name="ajuda" id="imgAjudaUsuario" <?= PaginaSEI::montarTitleTooltip('A pesquisa � realizada somente sobre Usu�rios Externos liberados. \n \n A pesquisa pode ser efetuada pelo Nome, E-mail ou CPF do Usu�rio Externo.') ?> class="infraImg"/><br>
                        <input style="width:300px;margin-top:1px;" type="text" id="txtUsuario" name="txtUsuario" class="infraText campoPadrao" onkeypress="return infraMascaraTexto(this,event);" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
                        <img id="imgLupaTipoProcesso" style="margin-right:15px;width:15px;height:15px;float:right;margin-top:-1px;padding:3px;" onclick="objLupaTipoProcesso.selecionar(700,500);"
                        src="/infra_css/imagens/lupa.gif" alt="Selecionar Usu�rio Externo"
                        title="Selecionar Usu�rio Externo" class="infraImg"/>       
                        <input type="hidden" id="hdnIdTipoProcesso" name="hdnIdTipoProcesso" value="<?php echo $idTipoProcesso ?>"/>
                        <input type="hidden" id="hdnIdUsuario"   name="hdnIdUsuario" value="0"/>
                        <input type="hidden" id="hdnUsuarioNome" name="hdnUsuarioNome" >
                        <input type="hidden" id="hdnTipoPessoa" name="hdnTipoPessoa" value="F"/>
      
                    </div>
                    <!-- Email -->
                    <div class="grid grid_4-0" style="width: 30%;">
                        <label id="lblEmail" for="txtEmail"  class="infraLabelObrigatorio">E-mail do Usu�rio Externo:</label>
                        <input type="text" id="txtEmail" name="txtEmail" class="infraText campoPadrao infraAutoCompletar" disabled="disabled" onkeypress="return infraMascaraTexto(this,event,50);" maxlength="50" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
                    </div>

                    <div class="height_2"></div>

                    <!-- Botao Adicionar -->
                    <div class="grid grid_2">
                        <button type="button" id="sbmGravarUsuario"  accesskey="A" name="sbmGravarUsuario" class="infraButton" onclick="transportarUsuario();" value="Adicionar" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"><span class="infraTeclaAtalho">A</span>dicionar</button>
                    </div>
                </div>

                <div class="tabUsuario clear height_2" style="<?php echo $_REQUEST['is_alterar'] ? '' : 'display:none' ?>"></div>
                <!-- Tabela de Destinat�rios -->

                <div id="divTabelaUsuarioExterno" class="tabUsuario infraAreaTabela" style="<?php echo $_REQUEST['is_alterar'] ? '' : 'display:none' ?>">
                    <table id="tblEnderecosEletronicos" width="100%" summary="Lista de Pessoas Jur�dicas dispon�veis" class="infraTable">
                        <caption id="test"
                                class="infraCaption"><?= PaginaSEI::getInstance()->gerarCaptionTabela("Pessoas F�sicas dispon�veis", count($arrIntimacoes)) ?></caption>
                        <tr>
                            <th style="display:none;">ID</th>
                            <th class="infraTh" width="30%">Destinat�rio</th>
                            <th class="infraTh">E-mail</th>
                            <th class="infraTh" width="90px">CPF</th>
                            <th class="infraTh" width="66px">Data de Expedi��o</th>
                            <th class="infraTh" width="130px">Situa��o da Intima��o</th>
                            <th class="infraTh" width="40px">A��es</th>
                        </tr>
                        <? if ($_REQUEST['is_alterar']) { ?>
                            <input type="hidden" id="hdnIdUsuarios" name="hdnIdUsuarios" value="<?= $arrIntimacoes ?>"/>
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
                                            <img title='Consultar Intima��o Eletr�nica' alt='Consultar Intima��o Eletr�nica'
                                                 src='/infra_css/imagens/consultar.gif' class='infraImg'/></a></td>
                                </tr>
                            <? }
                        } ?>
                    </table>
                    <input type="hidden" id="hdnIdDadosUsuario" name="hdnIdDadosUsuario"
                           value="<?= $_POST['hdnIdDadosUsuario'] ?>"/>
                    <input type="hidden" id="hdnDadosUsuario" name="hdnDadosUsuario"
                           value="<?= $_POST['hdnDadosUsuario'] ?>"/>

                </div>
        </fieldset>

        <div class="clear height_1"></div>

        <div id="conteudoHide" style="display: none;">
        <div class="grid grid_9">
            <!-- Tipo de Intima��o -->
            <div class="grid grid_6" style="margin-left:2px;">
                <label id="lblTipodeIntimacao" for="lblTipodeIntimacao" accesskey="" class="infraLabelObrigatorio">Tipo de Intima��o:</label>
                <select style="width: 50%" id="selTipoIntimacao" name="selTipoIntimacao" onchange="mostraTipoResposta(this)" class="campoPadrao infraSelect" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>">
                    <?= $strTipoIntimacao ?>
                </select>
                <input type=hidden name=hdnTipoIntimacao id=hdnTipoIntimacao>
            </div>

            <div class="clear height_1"></div>

            <!-- Tipo de Resposta -->
            <div class="grid grid_11" id="divTipoResposta" name="divTipoResposta">
                <div class="grid grid_3">
                    <label id="lblTipodeResposta" for="lblTipodeResposta" class="infraLabelObrigatorio">Tipo de Resposta:</label>
                </div>
                <div class="clear"></div>
                <div class="grid grid_6" id="divSelectTipoResposta"></div>
            </div>
            <div style="display: none" id="divEspacoResposta" class="clear height_1"></div>
        </div>

        <div class="clear"></div>

        <fieldset id="fldDocumentosIntimacao">
            <legend class="infraLegend" class="infraLabelObrigatorio"> Documentos da Intima��o <img style="margin-top:1px; margin-bottom: -3px" src="<?= PaginaSEI::getInstance()->getDiretorioImagensGlobal() ?>/ajuda.gif" name="ajuda" id="imgAjudaAnexos" <?= PaginaSEI::montarTitleTooltip('Considerar-se-� cumprida a Intima��o Eletr�nica com a consulta ao Documento Principal ou, se indicados, a qualquer um dos Protocolos dos Anexos da Intima��o. \n\n Caso a consulta n�o seja efetuada em at� ' . $numNumPrazo . ' dias corridos da data de gera��o da Intima��o Eletr�nica, automaticamente ocorrer� seu Cumprimento por Decurso do Prazo T�cito. \n\n\n\n\n O Documento Principal e poss�veis Anexos ter�o o acesso ao seu teor protegidos at� o cumprimento da Intima��o.') ?> /></legend>

            <!-- Documento Principal-->
            <div class="grid grid_8" style="margin-top:5px">
                <label id="lblDocPrincIntimacao" for="lblDocPrincIntimacao" class="infraLabelOpcional">Documento Principal da Intima��o: <?= DocumentoINT::formatarIdentificacao($objDocumentoDTO) . ' (' . $strProtocoloDocumentoFormatado . ')'; ?></label>
            </div>

            <div class="clear height"></div>

            <div id="divOptAno" class="grid_8 infraDivCheckbox">
                <input type="checkbox" id="optPossuiAnexo" name="rdoPossuiAnexo" value="S"
                       onclick="esconderAnexos(this)" class="infraCheckbox" <?= (false ? 'checked="checked"' : '') ?>
                       tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
                <label id="lblPossuiAnexo" for="optPossuiAnexo" accesskey="" class="infraLabelCheckbox">Intima��o possui Anexos </label>
            </div>
            <div class="clear"></div>
            
            <!-- Anexos -->
            <div class="grid grid_10">
                <label id="lblAnexosIntimacao" for="lblAnexosIntimacao" accesskey="" class="infraLabelObrigatorio">Protocolos dos Anexos da Intima��o:</label>
                <div style="display: -webkit-box;">
                    <select onclick="controlarSelected(this);" id="selAnexosIntimacao" style="width: 85%" name="selAnexosIntimacao" size="7"
                            class="infraSelect" multiple="multiple"></select>
                    <img style="padding-left: 5px;" id="imgLupaAnexos" onclick="objLupaProtocolosIntimacao.selecionar(700,500);"
                         src="<?= PaginaSEI::getInstance()->getDiretorioImagensGlobal() ?>/lupa.gif"
                         alt="Selecionar Protocolos" title="Selecionar Protocolos" class="infraImg"/>
                    </br>
                    <img style="padding-left: 4px;" id="imgExcluirAnexos" onclick="objLupaProtocolosIntimacao.remover();"
                         src="<?= PaginaSEI::getInstance()->getDiretorioImagensGlobal() ?>/remover.gif"
                         alt="Remover Protocolos Selecionados" title="Remover Protocolos Selecionados"
                         class="infraImgNormal"/>
                </div>
                <input type="hidden" id="hdnAnexosIntimacao" name="hdnAnexosIntimacao"
                       value="<?= $_POST['hdnAnexosIntimacao'] ?>"/>
            </div>
        </fieldset>
        <div class="clear"></div>
            <fieldset id="flTpAcesso" style="margin-top:17px">
                <legend class="infraLegend" class="infraLabelObrigatorio"> Tipo de Acesso Externo </legend>
                <!-- Tipo de Acesso Externo -->
                <div class="clear height"></div>
                <div class="grid grid_8" style="margin-top:3px">
                    <!-- Integral -->
                    <div id="divOptAno" class="infraDivRadio">
                        <input type="radio" id="optIntegral" name="optIntegral" value="I" class="infraRadio" onclick="mostrarProtocoloParcial(this)" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
                        <label id="lblIntegral" for="optIntegral" accesskey="" class="infraLabelRadio">Integral </label> &nbsp;<img src="<?= PaginaSEI::getInstance()->getDiretorioImagensGlobal() ?>/ajuda.gif" name="ajuda" id="imgAjudaAnexos" <?= PaginaSEI::montarTitleTooltip('Aten��o! Toda Intima��o Eletr�nica ocorre por meio da funcionalidade de Disponibiliza��o de Acesso Externo do SEI. \n\n Selecionando o Tipo de Acesso Externo Integral, TODOS os Protocolos constantes no processo ser�o disponibilizados ao Destinat�rio, independentemente de seus N�veis de Acesso, incluindo Protocolos futuros que forem adicionados ao processo. \n\n\n\n\n Para que n�o ocorra nulidade da Intima��o, o Acesso Externo Integral somente poder� ser cancelado depois de cumprida a Intima��o e conclu�do o Prazo Externo correspondente (se indicado para poss�vel Resposta). Caso posteriormente o Acesso Externo Integral utilizado pela Intima��o Eletr�nica seja cancelado, ele ser� automaticamente substitu�do por um Acesso Externo Parcial abrangendo o Documento Principal e poss�veis Anexos da Intima��o, al�m de Documentos peticionados pelo pr�prio Usu�rio Externo.') ?> class="infraImg"/>
                    </div>

                    <!-- Parcial -->
                    <div id="divOptAno" class="infraDivRadio" style="margin-left: 16px;">
                        <input type="radio" id="optParcial" name="optParcial" value="P" class="infraRadio" onclick="mostrarProtocoloParcial(this)" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
                        <label id="lblParcial" for="optParcial" accesskey="" class="infraLabelRadio">Parcial </label> &nbsp;<img src="<?= PaginaSEI::getInstance()->getDiretorioImagensGlobal() ?>/ajuda.gif" name="ajuda" id="imgAjudaAnexos" <?= PaginaSEI::montarTitleTooltip('Aten��o! Toda Intima��o Eletr�nica ocorre por meio da funcionalidade de Disponibiliza��o de Acesso Externo do SEI. \n\n Selecionando o Tipo de Acesso Externo Parcial, SOMENTE ser�o disponibilizados ao Destinat�rio o Documento Principal, os Protocolos dos Anexos da Intima��o (se indicados) e os Protocolos adicionados no Acesso Parcial (se indicados). O Documento Principal e Protocolos dos Anexos ser�o automaticamente inclu�dos no Acesso Parcial. \n\n\n\n\n Para que n�o ocorra nulidade da Intima��o, o Acesso Externo Parcial n�o poder� ser alterado nem cancelado. Todos os Protocolos inclu�dos no Acesso Externo Parcial poder�o ser visualizados pelo Destinat�rio, independentemente de seus N�veis de Acesso, n�o abrangendo Protocolos futuros que forem adicionados ao processo.') ?> class="infraImg"/>
                    </div>
                </div>

                <div class="clear height"></div>

                <!-- Protocolos Dispon�veis -->
                <div class="grid grid_10">
                    <label id="lblProtocolosDisponibilizados" for="lblProtocolosDisponibilizados" accesskey="" class="infraLabelObrigatorio">Protocolos Disponibilizados:</label>
                    <div style="display: -webkit-box;">
                        <select onclick="controlarSelected(this);" style="width: 90%" id="selProtocolosDisponibilizados" multiple="multiple" name="selProtocolosDisponibilizados" size="7" class="infraSelect"></select>

                        <img style="padding-left: 5px;" id="imgLupaProtocolos" onclick="objLupaProtocolosDisponibilizados.selecionar(700,500);" src="<?= PaginaSEI::getInstance()->getDiretorioImagensGlobal() ?>/lupa.gif" alt="Selecionar Protocolos" title="Selecionar Protocolos" class="infraImg"/>
                        </br>
                        <img style="padding-left: 4px;" id="imgExcluirProtocolos" onclick="objLupaProtocolosDisponibilizados.remover();" src="<?= PaginaSEI::getInstance()->getDiretorioImagensGlobal() ?>/remover.gif" alt="Remover Protocolos Selecionados" title="Remover Protocolos Selecionados" class="infraImgNormal"/>
                    </div>
                </div>
            </fieldset>
            </div>

        <!-- Hiddens -->
        <select style="display: none" multiple="multiple" id="selMainIntimacao" name="selMainIntimacao" size="12"/>
        <input type="hidden" id="hdnIsAlterar" name="hdnIsAlterar" value="<?php echo $_REQUEST['is_alterar'] ? '1' : '0' ?>"/>
        <input type="hidden" id="hdnCountIntimacoes" name="hdnCountIntimacoes" value="<?php echo $countInt ?>"/>
        <input type="hidden" id="hdnProtocolosDisponibilizados" name="hdnProtocolosDisponibilizados"
               value="<?= $_POST['hdnProtocolosDisponibilizados'] ?>"/>
        <input type="hidden" id="hdnIdDocumento" name="hdnIdDocumento" value="<?php echo $idDocumento ?>"/>
        <input type="hidden" id="hndIdDocumento" name="hndIdDocumento" value="<?=$idDocumento?>" />
        <input type="hidden" id="hdnIdProcedimento" name="hdnIdProcedimento" value="<?= array_key_exists('id_procedimento', $_REQUEST) ? $_REQUEST['id_procedimento'] : $_POST['hdnIdProcedimento'] ?>"/>
        <input type="hidden" id="hdnIdsDocAnexo" name="hdnIdsDocAnexo" value=""/>
        <input type="hidden" id="hdnIdsDocDisponivel" name="hdnIdsDocDisponivel" value=""/>

        <!-- Hiddens das constantes do Acesso Parcial / Integral -->
    <input type="hidden" id="hdnStaAcessoParcial" name="hdnStaAcessoParcial" value="<?php echo MdPetIntAcessoExternoDocumentoRN::$ACESSO_PARCIAL ?>">
    <input type="hidden" id="hdnStaAcessoIntegral" name="hdnStaAcessoIntegral" value="<?php echo MdPetIntAcessoExternoDocumentoRN::$ACESSO_INTEGRAL ?>">
    <input type="hidden" id="hdnStaSemAcesso" name="hdnStaSemAcesso" value="<?php echo MdPetIntAcessoExternoDocumentoRN::$NAO_POSSUI_ACESSO ?>">
    
    <style>

        .bloco {
  float: left;
  margin-top: 1%;
  margin-right: 1%;
}

    </style>