<?
/**
 * TRIBUNAL REGIONAL FEDERAL DA 4� REGI�O
 *
 * 14/03/2017 - criado por pedro.cast
 *
 * Vers�o do Gerador de C�digo: 1.40.0
 */

try {
    require_once dirname(__FILE__) . '/../../SEI.php';

    session_start();
    //////////////////////////////////////////////////////////////////////////////
    InfraDebug::getInstance()->setBolLigado(false);
    InfraDebug::getInstance()->setBolDebugInfra(false);
    InfraDebug::getInstance()->limpar();
    //////////////////////////////////////////////////////////////////////////////

//    SessaoSEI::getIn  stance()->validarLink();

    $arrComandos = array();
    PaginaSEI::getInstance()->setBolArvore($_GET['arvore']);
    $strParametros .= '&arvore='.$_GET['arvore'];

    switch($_GET['acao']){
        case 'md_pet_intimacao_consulta':
            $strTitulo = 'Consultar Intima��o Eletr�nica';

            $idDocumento = isset($_GET['id_documento']) ? $_GET['id_documento'] : $_POST['hdnIdDocumento'];
            $objDocumentoDTO = new DocumentoDTO();
            $objDocumentoDTO->retNumIdOrgaoUnidadeResponsavel();
            $objDocumentoDTO->retStrProtocoloDocumentoFormatado();
            $objDocumentoDTO->retStrNomeSerie();
            $objDocumentoDTO->retStrNumero();
            $objDocumentoDTO->retNumIdSerie();
            $objDocumentoDTO->setDblIdDocumento($idDocumento);
            $objDocumentoRN = new DocumentoRN();
            $objDocumentoDTO = $objDocumentoRN->consultarRN0005($objDocumentoDTO);

            $strProtocoloDocumentoFormatado = $objDocumentoDTO->getStrProtocoloDocumentoFormatado();

            


            //Juridica

            $dtoContato = new ContatoDTO();
            $dtoContato->setNumIdContato($_GET['id_contato']);
            $dtoContato->retDblCnpj();
            $dtoContato->retDblCpf();
            $dtoContato->retStrNome();
            $rnContato = new ContatoRN();
            $arr = $rnContato->listarRN0325($dtoContato);
            
            if($arr[0]->getDblCpf() == null){
                $pessoa = "J";

                //Cria Intimacao
            $objMdPetIntimacaoRN = new MdPetIntimacaoRN();
            
            $dadosIntimacao = $objMdPetIntimacaoRN->dadosIntimacaoByIDJuridico($_GET['id_intimacao'], $_GET['id_contato']);

            $strTipoIntimacao = MdPetIntTipoIntimacaoINT::montarSelectIdMdPetIntTipoIntimacao('0', '', $dadosIntimacao['tipo_intimacao']);

            }else{
                
                $pessoa = "F";

                $objMdPetIntimacaoRN = new MdPetIntimacaoRN();
            
            $dadosIntimacao = $objMdPetIntimacaoRN->dadosIntimacaoByID($_GET['id_intimacao'], $_GET['id_contato']);

            $strTipoIntimacao = MdPetIntTipoIntimacaoINT::montarSelectIdMdPetIntTipoIntimacao('0', '', $dadosIntimacao['tipo_intimacao']);
            }

            break;

        default:
            throw new InfraException("A��o '".$_GET['acao']."' n�o reconhecida.");
    }

    $arrComandos[] = '<button type="button" accesskey="C" id="btnFechar" name="btnFechar" value="Fechar" onclick="infraFecharJanelaModal();" class="infraButton">Fe<span class="infraTeclaAtalho">c</span>har</button>';

}catch(Exception $e){
    PaginaSEI::getInstance()->processarExcecao($e);
}

PaginaSEI::getInstance()->montarDocType();
PaginaSEI::getInstance()->abrirHtml();
PaginaSEI::getInstance()->abrirHead();
PaginaSEI::getInstance()->montarMeta();
PaginaSEI::getInstance()->montarTitle(PaginaSEI::getInstance()->getStrNomeSistema().' - '.$strTitulo);
PaginaSEI::getInstance()->montarStyle();
PaginaSEI::getInstance()->abrirStyle();
PaginaSEI::getInstance()->fecharStyle();
PaginaSEI::getInstance()->montarJavaScript();
PaginaSEI::getInstance()->abrirJavaScript();
?>
function fechar(){
    var hdnIsListaInt = document.getElementById('hdnIsListaInt').value;

   if(hdnIsListaInt == '1')
   {
    var idDest  = document.getElementById('hdnIdDestInt').value;
    var idLinha = 'linha_' + idDest;
    window.opener.document.getElementById(idLinha).className += ' infraTrAcessada';
   }

   window.close();
}
<?
PaginaSEI::getInstance()->fecharJavaScript();
PaginaSEI::getInstance()->fecharHead();
PaginaSEI::getInstance()->abrirBody($strTitulo);
PaginaSEI::getInstance()->montarBarraComandosSuperior($arrComandos);

?>
    <input type="hidden" name="hdnIdDestInt" id="hdnIdDestInt" value="<?php echo isset($dadosIntimacao['id_dest_int']) ? $dadosIntimacao['id_dest_int'] : '' ?>" />
    <input type="hidden" name="hdnIsListaInt" id="hdnIsListaInt" value="<?php echo isset($_GET['lista_int']) ? $_GET['lista_int'] : '0' ?>" />

    <div class="row mb-3">
        <div class="col-10">
            <label class="infraLabelObrigatorio">Destinat�rio:</label> <label class="infraLabelOpcional"><?= PaginaSEI::tratarHTML($dadosIntimacao['nome'])?></label><br>
            <?php if($pessoa == "F"){ ?><label class="infraLabelObrigatorio">E-mail:</label><label class="infraLabelOpcional"> <?= $dadosIntimacao['email']?></label><br><?php } ?>

            <?php if($pessoa == "F"){ ?><label class="infraLabelObrigatorio">CPF:</label> <label class="infraLabelOpcional"><?= InfraUtil::formatarCpf($dadosIntimacao['cpf'])?></label><br> <?php } ?>
            <?php if($pessoa == "J"){ ?><label class="infraLabelObrigatorio">CNPJ:</label> <label class="infraLabelOpcional"><?= InfraUtil::formatarCnpj($dadosIntimacao['cpf'])?></label><br> <?php } ?>

            <label class="infraLabelObrigatorio">Data de Expedi��o:</label> <label class="infraLabelOpcional"><?= $dadosIntimacao['data_geracao']?></label><br>
            <label class="infraLabelObrigatorio">Situa��o da Intima��o:</label> <label class="infraLabelOpcional"><?= $dadosIntimacao['situacao']?></label><br>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-sm-12 col-md-6 col-lg-6">
            <label class="infraLabelObrigatorio">Tipo de Intima��o:</label>
            <select disabled="disabled" class="infraSelect form-control">
                <?= $strTipoIntimacao ?>
            </select>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-sm-12 col-md-9 col-lg-8">
            <label class="infraLabelObrigatorio">Tipo de Resposta:</label> <br>
            <?= $dadosIntimacao['arr_tipo_resposta']?>
        </div>
    </div>

<?php PaginaSEI::getInstance()->abrirAreaDados(); ?>

    <fieldset id="fldDestinatarios" class="infraFieldset p-3 mb-3">
        <legend class="infraLegend" class="infraLabelObrigatorio" > Documentos da Intima��o 
            <img style="margin-top:1px; margin-bottom: -3px" src="<?= PaginaSEI::getInstance()->getDiretorioImagensGlobal() ?>/ajuda.gif" name="ajuda"
                id="imgAjudaAnexos" <?= PaginaSEI::montarTitleTooltip('Considerar-se-� cumprida a Intima��o Eletr�nica com a consulta ao Documento Principal ou, se indicados, a qualquer um dos Protocolos dos Anexos da Intima��o. \n\n Caso a consulta n�o seja efetuada em at� ' . $numNumPrazo . ' dias corridos da data de gera��o da Intima��o Eletr�nica, automaticamente ocorrer� seu Cumprimento por Decurso do Prazo T�cito. \n\n\n\n\n O Documento Principal e poss�veis Anexos ter�o o acesso ao seu teor protegidos at� o cumprimento da Intima��o.', 'Ajuda') ?> />
        </legend>
        <div class="row mb-3">
            <div class="col-sm-12 col-md-8 col-lg-7">
                <label class="infraLabelOpcional">Documento Principal da Intima��o: <?= DocumentoINT::formatarIdentificacao($objDocumentoDTO) . ' (' .$strProtocoloDocumentoFormatado . ')'; ?></label>                
                <div id="divOptAno" class="infraDivCheckbox">
                    <input type="checkbox" <?= ($dadosIntimacao['documento_principal']) ? 'checked="checked"' : ''; ?> id="optPossuiAnexo" disabled="disabled" class="infraCheckbox" />
                    <label id="lblPossuiAnexo" for="optPossuiAnexo" accesskey="" class="infraLabelOpcional">Intima��o possui Anexos </label>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-sm-12 col-md-8 col-lg-8">
                <label id="lblAnexosIntimacao" <?= ($dadosIntimacao['documento_principal']) ? '' : 'style="display:none;"'; ?> for="lblAnexosIntimacao" accesskey="" class="infraLabelObrigatorio">Protocolos dos Anexos da Intima��o:</label>
                <select id="selAnexosIntimacao" <?= ($dadosIntimacao['documento_principal']) ? '' : 'style="display:none;"'; ?> name="selAnexosIntimacao" disabled="disabled" size="5" class="infraSelect form-control" ><?= $dadosIntimacao['arr_protocolos_anexos'] ?></select>
            </div>
        </div>
    </fieldset>

    <fieldset id="fldDestinatarios" class="infraFieldset p-3 mb-3">
        <legend class="infraLegend" class="infraLabelObrigatorio"> Tipo de Acesso Externo </legend>
        <div class="row mb-3">
            <div class="col-sm-12 col-md-8 col-lg-7">
                <div id="divOptAno" class="infraDivRadio">
                    <input id="lblIntegral" type="radio" <?= $dadosIntimacao['tipo_acesso'] == 'I' ? 'checked="checked"' : '' ?> class="infraRadio" disabled="disabled" />
                    <label class="infraLabelOpcional">Integral </label>
                    <img align="top" src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/ajuda.svg?<?= Icone::VERSAO ?>" class="infraImg" name="ajuda"
                        <?= PaginaSEI::montarTitleTooltip('Aten��o! Toda Intima��o Eletr�nica ocorre por meio da funcionalidade de Disponibiliza��o de Acesso Externo do SEI. \n\n Selecionando o Tipo de Acesso Externo Integral, TODOS os Protocolos constantes no processo ser�o disponibilizados ao Destinat�rio, independentemente de seus N�veis de Acesso, incluindo Protocolos futuros que forem adicionados ao processo. \n\n\n\n\n Para que n�o ocorra nulidade da Intima��o, o Acesso Externo Integral somente poder� ser cancelado depois de cumprida a Intima��o e conclu�do o Prazo Externo correspondente (se indicado para poss�vel Resposta). Caso posteriormente o Acesso Externo Integral utilizado pela Intima��o Eletr�nica seja cancelado, ele ser� automaticamente substitu�do por um Acesso Externo Parcial abrangendo o Documento Principal e poss�veis Anexos da Intima��o, al�m de Documentos peticionados pelo pr�prio Usu�rio Externo.','Ajuda') ?> />
                </div>

                <div id="divOptAno" class="infraDivRadio">
                    <input id="lblParcial" type="radio" <?= $dadosIntimacao['tipo_acesso'] == 'P' ? 'checked="checked"' : '' ?> class="infraRadio" disabled="disabled" />
                    <label class="infraLabelOpcional">Parcial </label>
                    <img align="top" src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/ajuda.svg?<?= Icone::VERSAO ?>" class="infraImg" name="ajuda"
                        <?= PaginaSEI::montarTitleTooltip('Aten��o! Toda Intima��o Eletr�nica ocorre por meio da funcionalidade de Disponibiliza��o de Acesso Externo do SEI. \n\n Selecionando o Tipo de Acesso Externo Parcial, SOMENTE ser�o disponibilizados ao Destinat�rio o Documento Principal, os Protocolos dos Anexos da Intima��o (se indicados) e os Protocolos adicionados no Acesso Parcial (se indicados). O Documento Principal e Protocolos dos Anexos ser�o automaticamente inclu�dos no Acesso Parcial. \n\n\n\n\n Para que n�o ocorra nulidade da Intima��o, o Acesso Externo Parcial n�o poder� ser alterado nem cancelado. Todos os Protocolos inclu�dos no Acesso Externo Parcial poder�o ser visualizados pelo Destinat�rio, independentemente de seus N�veis de Acesso, n�o abrangendo Protocolos futuros que forem adicionados ao processo.','Ajuda') ?> />
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-sm-12 col-md-8 col-lg-8">
                <label <?= ($dadosIntimacao['tipo_acesso'] == 'I') ? 'style="display:none;"' : ''; ?> class="infraLabelObrigatorio">Protocolos Disponibilizados:</label>
                <select <?= ($dadosIntimacao['tipo_acesso'] == 'I') ? 'style="display:none;"' : ''; ?> id="selProtocolosDisponibilizados" disabled="disabled" name="selProtocolosDisponibilizados" size="5" class="infraSelect form-control" > <?= $dadosIntimacao['arr_protocolos_disponibilizados'] ?></select>
            </div>
        </div>
    </fieldset>

<?php 
PaginaSEI::getInstance()->fecharAreaDados();
//PaginaSEI::getInstance()->montarAreaDebug();
PaginaSEI::getInstance()->fecharBody();
PaginaSEI::getInstance()->fecharHtml();
?>