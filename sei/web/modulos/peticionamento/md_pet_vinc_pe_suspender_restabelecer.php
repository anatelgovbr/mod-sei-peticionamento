<?
/**
 * ANATEL
 *
 * 23/06/2016 - criado por marcelo.bezerra@cast.com.br - CAST
 * 27/01/2025 - atualizado por gabirelg.colab@anatel.gov.br - SPASSU
 *
 */

try {

    require_once dirname(__FILE__) . '/../../SEI.php';

    session_start();

    //////////////////////////////////////////////////////////////////////////////
    InfraDebug::getInstance()->setBolLigado(false);
    InfraDebug::getInstance()->setBolDebugInfra(false);
    InfraDebug::getInstance()->limpar();
    //////////////////////////////////////////////////////////////////////////////
	
	SessaoSEI::getInstance()->validarLink();
	SessaoSEI::getInstance()->validarPermissao($_GET['acao']);
    
    // PaginaSEI::getInstance()->setTipoPagina(InfraPagina::$TIPO_PAGINA_SIMPLES);
    $strLinkAjaxValidacoesNumeroSEI = SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=md_pet_validar_num_sei');

    switch ($_GET['acao']) {

        case 'md_pet_vinc_suspender_restabelecer':

            $operacao                   = isset($_GET['operacao']) ? $_GET['operacao'] : $_POST['hdnOperacao'];
            $idVinculo                  = isset($_GET['idVinculo']) ? $_GET['idVinculo'] : $_POST['hdnIdVinculo'];

            $idVinculoRepresent         = isset($_GET['idVinculoRepresent']) ? $_GET['idVinculoRepresent'] : $_POST['hdnIdVinculoRepresent'];
            $strTipoVinculo             = isset($_GET['tipoVinculo']) ? $_GET['tipoVinculo'] : $_POST['hdnTipoVinculo'];
            $hdnIdDocumentoRepresent    = isset($_GET['idDocumentoRepresent']) ? $_GET['idDocumentoRepresent'] : $_POST['hdnIdDocumentoRepresent'];
            $exibirEmCascata = false;

            //Recuperar dados da Pessoa Juridica
            $objMdPetVinculoDTO = new MdPetVinculoDTO();
            $objMdPetVinculoDTO->retNumIdMdPetVinculo();
            $objMdPetVinculoDTO->retStrCNPJ();
            $objMdPetVinculoDTO->retStrRazaoSocialNomeVinc();
            $objMdPetVinculoDTO->retNumIdContatoRepresentante();
            $objMdPetVinculoDTO->retStrCpfContatoRepresentante();
            $objMdPetVinculoDTO->retStrNomeContatoRepresentante();
            $objMdPetVinculoDTO->setNumIdMdPetVinculo($idVinculo);
	        $objMdPetVinculoDTO->setDistinct(true);
	        $objMdPetVinculoDTO->setNumMaxRegistrosRetorno(1);
            $objMdPetVinculoDTO = (new MdPetVinculoRN())->consultar($objMdPetVinculoDTO);

            // RETORNANDO DADOS DO VÍNCULO
            $objMdPetVincRepresentantDTO = new MdPetVincRepresentantDTO();
            $objMdPetVincRepresentantDTO->retTodos();
            $objMdPetVincRepresentantDTO->retNumIdMdPetVinculo();
            $objMdPetVincRepresentantDTO->retNumIdMdPetVinculoRepresent();
            $objMdPetVincRepresentantDTO->retStrTipoRepresentante();
            $objMdPetVincRepresentantDTO->retStrStaEstado();
            $objMdPetVincRepresentantDTO->retStrCpfProcurador();
            $objMdPetVincRepresentantDTO->retStrRazaoSocialNomeVinc();
            $objMdPetVincRepresentantDTO->retStrCNPJ();
            $objMdPetVincRepresentantDTO->retStrCPF();
            $objMdPetVincRepresentantDTO->retStrTpVinc();
            $objMdPetVincRepresentantDTO->retNumIdContatoVinc();
            $objMdPetVincRepresentantDTO->retStrNomeProcurador();
            $objMdPetVincRepresentantDTO->retNumIdContatoProcurador();
            $objMdPetVincRepresentantDTO->retDblIdProcedimentoVinculo();
            $objMdPetVincRepresentantDTO->retStrStaAbrangencia();
            $objMdPetVincRepresentantDTO->setNumIdMdPetVinculoRepresent($idVinculoRepresent);
            $objMdPetVincRepresentantDTO = (new MdPetVincRepresentantRN())->consultar($objMdPetVincRepresentantDTO);

            if(empty($objMdPetVincRepresentantDTO)){
                throw new InfraException("Vínculo (".$idVinculoRepresent.") não encontrado na tabela md_pet_vinculo_represent.");
            }

            $strTipoRepresentante = (new MdPetVincRepresentantDTO())->getStrNomeTipoRepresentante( $objMdPetVincRepresentantDTO->getStrTipoRepresentante() );

            // RETORNANDO DADOS DO DOCUMENTO DA VINCULAÇÃO:
            $objMdPetVincDocumentoDTO = new MdPetVincDocumentoDTO();
            $objMdPetVincDocumentoDTO->setNumIdMdPetVinculoRepresent($objMdPetVincRepresentantDTO->getNumIdMdPetVinculoRepresent());
            $objMdPetVincDocumentoDTO->retDblIdDocumento();
            $objMdPetVincDocumentoDTO->retStrProtocoloFormatadoProtocolo();
            $objMdPetVincDocumentoDTO->retNumIdMdPetVinculoRepresent();
            $objMdPetVincDocumentoDTO->adicionarCriterio(
                array('TipoDocumento', 'TipoDocumento', 'TipoDocumento'),
                array(InfraDTO::$OPER_IGUAL, InfraDTO::$OPER_IGUAL, InfraDTO::$OPER_IGUAL),
                array('E', 'P', 'N'),
                array(InfraDTO::$OPER_LOGICO_OR, InfraDTO::$OPER_LOGICO_OR)
            );
            $arrObjMdPetVincDocumentoDTO = (new MdPetVincDocumentoRN())->consultar($objMdPetVincDocumentoDTO);

            // INICIALIZANDO VARIÁVEIS:
            $strTitulo = ($_GET['operacao'] == 'A' ? 'Restabelecer ' : 'Suspender ') . $strTipoRepresentante;
            $strAtenção = $_GET['operacao'] == 'A' ? 'O Restabelecimento' : 'A Suspensão';
            $janelaSelecaoPorNome = SessaoSEIExterna::getInstance()->getAtributo('janelaSelecaoPorNome');
            $strPrimeiroItemValor = 'null';
            $strPrimeiroItemDescricao = '&nbsp;';
            $strValorItemSelecionado = null;
            $strTipo = 'Cadastro';

            //$strLinkBaseFormEdicao = 'controlador_externo.php?edicaoExibir=true&acao=' . $_GET['acao'];
            $strLinkBaseFormEdicao = 'controlador.php?edicaoExibir=true&acao=' . $_GET['acao'];
            $strLinkEdicaHash = PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink($strLinkBaseFormEdicao));

            $titleConsultar = 'Consultar ' . (($objMdPetVincRepresentantDTO->getStrTipoRepresentante() == MdPetVincRepresentantRN::$PE_RESPONSAVEL_LEGAL) ? 'Vinculação' : 'Procuração');
            $strLinkConsultaDocumento = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=documento_visualizar&id_documento=' . $arrObjMdPetVincDocumentoDTO->getDblIdDocumento() . '&arvore=1');
            if($strTipoVinculo == 'L'){
              $exibirEmCascata = true;
            }

            if ($_GET['operacao'] == MdPetVincRepresentantRN::$RP_ATIVO){
                $subTitulo = 'O Restabelecimento do Responsável Legal deve ser motivado em documento específico, a ser indicado no
                            campo abaixo. Todas as Procurações Eletrônicas geradas serão restabelecidas, o Usuário Externo
                            será notificado e este ato constará no processo referente à Pessoa Jurídica.';
            } else {
                $subTitulo = 'A Suspensão do '. $strTipoRepresentante . ' deve ser motivada em documento específico, a ser indicado no campo abaixo. A suspensão como ' . $strTipoRepresentante . ' não impede o Usuário Externo de peticionar em próprio nome.';
            }


            if($strTipoVinculo == 'L'){
                if ($_GET['operacao'] == MdPetVincRepresentantRN::$RP_ATIVO){
                    $subTitulo = 'O Restabelecimento do Responsável Legal deve ser motivado em documento específico, a ser indicado no
                            campo abaixo. Todas as Procurações Eletrônicas geradas serão restabelecidas, o Usuário Externo
                            será notificado e este ato constará no processo referente à Pessoa Jurídica.';
                } else {
                    $subTitulo = 'A Suspensão do Responsável Legal deve ser motivado em documento específico, a ser indicado no campo abaixo. Todas as Procurações Eletrônicas geradas pelo Responsável Legal serão suspensas, o Usuário Externo será notificado e este ato constará no processo referente à Pessoa Jurídica. A suspensão como Responsável Legal não impede o Usuário Externo de peticionar em próprio nome.';
                }
            }

          break;
        
        // Nao vai mais entrar aqui
        case 'md_pet_vinc_suspender_autorepresentacao':
	
	        throw new InfraException('A Suspensão ou Restabelecimento de vinculos de Autorrepresentação, cujo registro está vinculado diretamente ao cadastro do Usuário Externo, devem ser realizados através do menu: Adminstração > Usuários Externos');

	        // Todo: Remover conteudo abaixo
//            $idVinculoRepresent = isset($_GET['idVinculoRepresent']) ? $_GET['idVinculoRepresent'] : $_POST['hdnIdVinculoRepresent'];
//            $strTipoVinculo     = isset($_GET['tipoVinculo']) ? $_GET['tipoVinculo'] : $_POST['hdnTipoVinculo'];
//
//            // RETORNANDO DADOS DO VÍNCULO
//            $objMdPetVincRepresentantDTO = new MdPetVincRepresentantDTO();
//            $objMdPetVincRepresentantDTO->retTodos();
//            $objMdPetVincRepresentantDTO->retNumIdMdPetVinculo();
//            $objMdPetVincRepresentantDTO->retNumIdMdPetVinculoRepresent();
//            $objMdPetVincRepresentantDTO->retStrTipoRepresentante();
//            $objMdPetVincRepresentantDTO->retStrStaEstado();
//            $objMdPetVincRepresentantDTO->retStrCpfProcurador();
//            $objMdPetVincRepresentantDTO->retStrRazaoSocialNomeVinc();
//            $objMdPetVincRepresentantDTO->retStrCNPJ();
//            $objMdPetVincRepresentantDTO->retStrCPF();
//            $objMdPetVincRepresentantDTO->retStrTpVinc();
//            $objMdPetVincRepresentantDTO->retNumIdContatoVinc();
//            $objMdPetVincRepresentantDTO->retStrNomeProcurador();
//            $objMdPetVincRepresentantDTO->retNumIdContatoProcurador();
//            $objMdPetVincRepresentantDTO->retDblIdProcedimentoVinculo();
//            $objMdPetVincRepresentantDTO->retStrStaAbrangencia();
//            $objMdPetVincRepresentantDTO->setNumIdMdPetVinculoRepresent($idVinculoRepresent);
//            $objMdPetVincRepresentantDTO = (new MdPetVincRepresentantRN())->consultar($objMdPetVincRepresentantDTO);
//
//            if(empty($objMdPetVincRepresentantDTO)){
//                throw new InfraException("Vínculo (".$idVinculoRepresent.") não encontrado na tabela md_pet_vinculo_represent.");
//            }
//
//            $arrProcuracoes = (new MdPetVincRepresentantRN())->listarVincRepresentAtivosPorCPF($objMdPetVincRepresentantDTO->getStrCPF());
//            $temProcuradores = count($arrProcuracoes) > 1 ? true : false;
//            $disabled = $temProcuradores ? 'disabled ' : '';
//            $checked = $temProcuradores ? 'checked ' : '';
//
//            $strTipoRepresentante = (new MdPetVincRepresentantDTO())->getStrNomeTipoRepresentante( $objMdPetVincRepresentantDTO->getStrTipoRepresentante() );
//
//            $strLinkBaseFormEdicao = 'controlador.php?edicaoExibir=true&acao=' . $_GET['acao'];
//            $strLinkEdicaHash = PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink($strLinkBaseFormEdicao));
//            $titleConsultar = 'Consultar ' . (($objMdPetVincRepresentantDTO->getStrTipoRepresentante() == MdPetVincRepresentantRN::$PE_RESPONSAVEL_LEGAL) ? 'Vinculação' : 'Procuração');
//            $strTitulo = 'Suspender Autorepresentação';
//            $strLinkConsultaDocumento = '';
//
//            $subTitulo = '';
//            $exibirEmCascata = true;
//            $operacao = 'S';

          break;

        default:
            throw new InfraException("Ação '" . $_GET['acao'] . "' não reconhecida.");
    }

} catch (Exception $e) {
    PaginaSEI::getInstance()->processarExcecao($e);
}

$hashAnexo = "";
$idAnexo = "";

PaginaSEI::getInstance()->montarDocType();
PaginaSEI::getInstance()->abrirHtml();
PaginaSEI::getInstance()->abrirHead();
PaginaSEI::getInstance()->montarMeta();
PaginaSEI::getInstance()->montarTitle(PaginaSEI::getInstance()->getStrNomeSistema() . ' - ' . $strTitulo);
PaginaSEI::getInstance()->montarStyle();
PaginaSEI::getInstance()->abrirStyle();

PaginaSEI::getInstance()->fecharStyle();
PaginaSEI::getInstance()->montarJavaScript();
PaginaSEI::getInstance()->abrirJavaScript();
PaginaSEI::getInstance()->fecharJavaScript();
PaginaSEI::getInstance()->fecharHead();
PaginaSEI::getInstance()->abrirBody($strTitulo, 'onload="inicializar();"');
?>
    <style type="text/css">
    #txtInformativo { font-size: 13px; }
    #field1 { height: auto; width: 97%; margin-bottom: 11px; }
    #field2 { height: auto; width: 97%; margin-bottom: 11px; }
    .sizeFieldset { height: auto; width: 88%; }
    .fieldsetClear { border: none !important; }
    .infraLabelCheckbox { font-size: .875rem; }
    </style>

<?php

$urlBaseLink = "";
$arrComandos = array();
$arrComandos[] = '<button type="button" accesskey="s" name="Salvar" value="Salvar" onclick="salvar()" class="infraButton"><span class="infraTeclaAtalho">S</span>alvar</button>';
$arrComandos[] = '<button type="button" accesskey="c" name="btnFechar" value="Fechar" onclick="location.href=\'' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . PaginaSEI::getInstance()->getAcaoRetorno() . '&acao_origem=' . $_GET['acao']) . '\';" class="infraButton"><span class="infraTeclaAtalho">C</span>ancelar</button>';


?>

    <!-- Formulario usado para viabilizar fluxo de edição de contato -->

    <form id="frmEdicaoAuxiliar" name="frmEdicaoAuxiliar" method="post" action="<?= $strLinkEdicaHash ?>">

        <?php
            PaginaSEI::getInstance()->abrirAreaDados('auto');
            PaginaSEI::getInstance()->montarBarraComandosSuperior($arrComandos);
        ?>

        <div class="row">
            <div class="col-12">
                <p>ATENÇÃO</p>
                <p id="txtInformativo">
                    <? echo $subTitulo; ?>
                </p>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-sm-12 col-md-5 col-lg-4 col-xl-3">
                <div class="form-group">
                    <label id="lblCnpj" class="infraLabelObrigatorio">CPF/CNPJ Outorgante:</label><br/>
                    <input type="text" id="txtCnpj" name="txtCnpj"
                    class="infraText form-control"
                    disabled="disabled"
                    value="<?= PaginaSEI::tratarHTML(InfraUtil::formatarCpfCnpj(($objMdPetVincRepresentantDTO->getStrTpVinc() == 'F' ? $objMdPetVincRepresentantDTO->getStrCPF() : $objMdPetVincRepresentantDTO->getStrCNPJ()))) ?>"
                    onkeypress="return infraMascaraTexto(this,event,250);" maxlength="250"
                    tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
                </div>
            </div>
            <div class="col-sm-12 col-md-7 col-lg-6 col-xl-9">
                <div class="form-group">
                    <label id="lblRazaoSocial" class="infraLabelObrigatorio">Nome/Razão Social Outorgante:</label><br/>
                    <input type="text" id="txtRazaoSocial" name="txtRazaoSocial"
                    class="infraText form-control"
                    disabled="disabled"
                    value="<?= PaginaSEI::tratarHTML($objMdPetVincRepresentantDTO->getStrRazaoSocialNomeVinc()) ?>"
                    onkeypress="return infraMascaraTexto(this,event,250);" maxlength="250"
                    tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12 col-md-4 col-lg-4 col-xl-3">
                <div class="form-group">
                    <label id="lblCnpj" class="infraLabelObrigatorio">CPF Outorgado:</label><br/>
                    <input type="text" id="txtCpf" name="txtCpf"
                    class="infraText form-control"
                    disabled="disabled"
                    value="<?= PaginaSEI::tratarHTML(InfraUtil::formatarCpf($objMdPetVincRepresentantDTO->getStrCpfProcurador())) ?>"
                    onkeypress="return infraMascaraTexto(this,event,250);" maxlength="250"
                    tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
                </div>
            </div>
            <div class="col-sm-12 col-md-4 col-lg-4 col-xl-6">
                <div class="form-group">
                    <label id="txtRazaoSocial" class="infraLabelObrigatorio">Nome Outorgado:</label><br/>
                    <input type="text" id="txtNome" name="txtNome"
                    disabled="disabled"
                    class="infraText form-control"
                    value="<?= PaginaSEI::tratarHTML($objMdPetVincRepresentantDTO->getStrNomeProcurador()) ?>"
                    onkeypress="return infraMascaraTexto(this,event,250);" maxlength="250"
                    tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
                </div>
            </div>
            <div class="col-sm-12 col-md-4 col-lg-4 col-xl-3">
                <div class="form-group">
                    <label id="txtRazaoSocial" class="infraLabelObrigatorio">Tipo do Vínculo:</label>
                    <div class="input-group mb-3">
                        <input type="text" id="txtTipoVinculo" name="txtTipoVinculo" disabled="disabled" class="infraText form-control rounded" value="<?= $strTipoRepresentante ?>" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
                        <div class="input-group-append">
                            <a target="_blank" href="<?= $strLinkConsultaDocumento ?>">
                                <img src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/consultar.svg" title="<?= $titleConsultar ?>" alt="<?= $titleConsultar ?>" class="infraImg mt-1" />
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php if($temProcuradores || $strTipoVinculo != 'U'): ?>
            <div class="row">
                <div class="col-sm-12 col-md-5 col-lg-4 col-xl-3">
                    <div class="form-group">
                        <label class="infraLabelObrigatorio">Número SEI da Justificativa: </label><br/>
                        <div class="input-group mb-3">
                            <input type="text" id="txtNumeroSei" name="txtNumeroSei" class="infraText form-control" value=""
                                onkeypress="return infraMascaraTexto(this,event,250);" maxlength="10"
                                tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
                            <button type="button" accesskey="V" style="margin-left: 5px" id="btnValidar" onclick="controlarNumeroSEI();" class="infraButton">
                                <span class="infraTeclaAtalho">V</span>alidar
                            </button>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12 col-md-5 col-lg-6 col-xl-9">
                    <div class="form-group">
                        <label class="infraLabelObrigatorio"></label><br/>
                        <div class="input-group mb-3">
                            <input type="text" id="txtTipo" name="txtTipo" class="infraText form-control rounded" readonly="readonly" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>" value=""/>
                            <div class="input-group-append">
                                <a target="_blank" href="" id="txtTipoLink" style="display:none">
                                    <img src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/consultar.svg" title="Consultar documento da Justificativa" alt="<?= $titleConsultar ?>" class="infraImg mt-1" />
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Checkbox para suspensão ou restabelecimento em cascata -->
            <?php if($exibirEmCascata): ?>
                <div class="row">
                    <div class="col-12">
                        <div class="infraCheckboxDiv">
                            <input type="checkbox" name="rdoCascata" class="infraCheckboxInput" id="optCascata" value="S" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>" <?php echo $disabled; echo $checked; ?>/>
                            <label class="infraCheckboxLabel" for="optCascata"></label>
                        </div>
                        <label id="lblCascata" for="optCascata" accesskey="" class="infraLabelCheckbox">
                            Também <?= $operacao == 'S' ? 'suspender' : 'restabelecer' ?> os atuais Procuradores <?= $operacao == 'S' ? 'ativos' : 'suspensos' ?>
                        </label>
                    </div>
                </div>
            <?php endif ?>
        <?php endif ?>

        <input type="hidden" name="hdnIdVinculo" id="hdnIdVinculo" value="<?= $idVinculo ?>"/>
        <input type="hidden" name="hdnOperacao" id="hdnOperacao" value="<?= $operacao ?>"/>
        <input type="hidden" name="hdnIdContatoNovo" id="hdnIdContatoNovo" value=""/>
        <input type="hidden" name="hdnIdContato" id="hdnIdContato" value="<?= $objMdPetVincRepresentantDTO->getNumIdMdPetVinculoRepresent() ?>"/>
        <input type="hidden" name="hdnIdDocumento" id="hdnIdDocumento" value=""/>

        <input type="hidden" name="hdnIdContatoProc" id="hdnIdContatoProc" value="<?= $objMdPetVincRepresentantDTO->getNumIdContatoProcurador() ?>"/>
        <input type="hidden" name="hdnIdVinculoRepresent" id="hdnIdVinculoRepresent" value="<?= $idVinculoRepresent ?>"/>
        <input type="hidden" name="hdnStrTipoVinculo" id="hdnStrTipoVinculo" value="<?= $strTipoVinculo ?>"/>
        <input type="hidden" name="hdnPossuiProcurador" id="hdnPossuiProcurador" value="<?= $temProcuradores ? 'true' : 'false'; ?>"/>
        <input type="hidden" name="hdnIdDocumentoRepresent" id="hdnIdDocumentoRepresent" value="<?= $hdnIdDocumentoRepresent ?>"/>

    </form>

<?php
PaginaSEI::getInstance()->montarBarraComandosInferior($arrComandos);
//PaginaSEI::getInstance()->montarAreaDebug();
PaginaSEI::getInstance()->fecharAreaDados();
require_once 'md_pet_vinc_pe_suspender_restabelecer_js.php';
PaginaSEI::getInstance()->fecharBody();
PaginaSEI::getInstance()->fecharHtml();
?>
