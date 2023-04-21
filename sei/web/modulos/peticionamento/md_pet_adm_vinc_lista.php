<?php
/**
 * Created by PhpStorm.
 * User: jhon.carvalho
 * Date: 22/06/2018
 * Time: 09:51
 */

try {
    require_once dirname(__FILE__) . '/../../SEI.php';

    session_start();
    SessaoSEI::getInstance()->validarLink();
    //SessaoSEI::getInstance()->validarPermissao($_GET['acao']);
    $arvoreVincListar = false;

    $isAdm = false;

    switch ($_GET['acao']) {

        case 'md_pet_adm_vinc_listar':
            $strTitulo = 'Administrar Vinculações e Procurações Eletrônicas';
            $strColuna10 = 'CPF/CNPJ Outorgante';
            $strColuna11 = 'CNPJ';
            $strColuna20 = 'Nome/Razão Social Outorgante';
            $strColuna21 = 'RazaoSocialNomeVinc';
            $isAdm = true;
            break;

        case 'md_pet_adm_vinc_consultar':
            $strTitulo = 'Vinculações e Procurações Eletrônicas';
            $strColuna10 = 'CPF/CNPJ Outorgante';
            $strColuna11 = 'CNPJ';
            $strColuna20 = 'Nome/Razão Social Outorgante';
            $strColuna21 = 'RazaoSocialNomeVinc';
            if (isset($_POST['hdnIdProcedimento'])) {
                $idProcedimento = $_POST['hdnIdProcedimento'];
                $arvoreVincListar = true;
            } else if (isset($_GET['id_procedimento'])) {
                $idProcedimento = $_GET['id_procedimento'];
                $arvoreVincListar = true;
            } else {
                $idProcedimento = $_GET['id_procedimento'];
            }
            break;

    }

    $strColuna30 = 'CPF Outorgado';
    $strColuna31 = 'CpfProcurador';
    $strColuna40 = 'Nome Outorgado';
    $strColuna41 = 'NomeProcurador';
    $strColuna50 = 'Tipo de Vínculo';
    $strColuna51 = 'TipoRepresentante';
    $strColuna60 = 'Situação';
    $strColuna61 = 'StaEstado';
    $strColuna70 = 'Natureza do Vínculo';
    $strColuna71 = 'TpVinc';
    $strColuna80 = 'Tipo de Poder';

    $strCpf = '';
    $strRazaoSocial = '';
    $strCnpj = '';
    $strNome = '';
    $strStatus = '';
    $strSlTipoViculo = '';

    if (!$arvoreVincListar) {
        PaginaSEI::getInstance()->limparCampos();
        PaginaSEI::getInstance()->salvarCamposPost(array('txtCnpj', 'txtRazaoSocial', 'txtCpf', 'txtNomeProcurador', 'selStatus', 'slTipoViculo', 'selNaturezaVinculo', 'selTipoPoder'));
        $strLinkConsultaDocumento = SessaoSEI::getInstance()->assinarLink('controlador_externo.php?acao=md_pet_vinc_doc_procuracao_consultar&id_documento=');
        $strCnpj = trim(PaginaSEI::getInstance()->recuperarCampo('txtCnpj'));
        $strCnpj = InfraUtil::retirarFormatacao($strCnpj);
        if ($strCnpj) {
            $intCnpj = intval($strCnpj);
        }

        $strRazaoSocial = trim(PaginaSEI::getInstance()->recuperarCampo('txtRazaoSocial'));

        $strCpf = trim(PaginaSEI::getInstance()->recuperarCampo('txtCpf'));
        $strCpf = InfraUtil::retirarFormatacao($strCpf);
        if ($strCpf) {
            $intCpf = intval($strCpf);
        }

        $strNome = trim(PaginaSEI::getInstance()->recuperarCampo('txtNomeProcurador'));
        $strStatus = trim(PaginaSEI::getInstance()->recuperarCampo('selStatus'));
        $strSlTipoViculo = trim(PaginaSEI::getInstance()->recuperarCampo('slTipoViculo'));
        $selNaturezaVinculo = trim(PaginaSEI::getInstance()->recuperarCampo('selNaturezaVinculo'));
        $selTipoPoder = PaginaSEI::getInstance()->recuperarCampo('selTipoPoder');

        $arrComandos = array();
        $arrComandos[] = '<button type="submit" accesskey="p" id="btnPesquisar" value="Pesquisar" class="infraButton"><span class="infraTeclaAtalho">P</span>esquisar</button>';
        $arrComandos[] = '<button type="button" accesskey="c" name="btnFechar"  onclick="location.href=\'' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . PaginaSEI::getInstance()->getAcaoRetorno() . '&acao_origem=' . $_GET['acao']) . '\'" class="infraButton">Fe<span class="infraTeclaAtalho">c</span>har</button>';
    }

    $objMdPetVincRepresentantRN = new MdPetVincRepresentantRN();
    $objMdPetVincRepresentantDTO = new MdPetVincRepresentantDTO();

    if ($intCpf > 0) {
        $objMdPetVincRepresentantDTO->setStrCpfProcurador('%' . $intCpf . '%', InfraDTO::$OPER_LIKE);
    }

    if ($strNome != '') {
        $objMdPetVincRepresentantDTO->setStrNomeProcurador('%' . $strNome . '%', InfraDTO::$OPER_LIKE);
    }

    if ($strRazaoSocial != '') {
        $objMdPetVincRepresentantDTO->setStrRazaoSocialNomeVinc('%' . $strRazaoSocial . '%', InfraDTO::$OPER_LIKE);
    }

    if ($intCnpj > 0) {
        if (strlen($intCnpj) <= 11) {
            $objMdPetVincRepresentantDTO->setStrCPF('%' . $intCnpj . '%', InfraDTO::$OPER_LIKE);
        } else {
            $objMdPetVincRepresentantDTO->setStrCNPJ('%' . $intCnpj . '%', InfraDTO::$OPER_LIKE);
        }
    }

    if ($strStatus != '' && $strStatus != 'null') {
        $objMdPetVincRepresentantDTO->setStrStaEstado($strStatus);
    }

    if ($strSlTipoViculo != '' && $strSlTipoViculo != 'null') {
        $objMdPetVincRepresentantDTO->setStrTipoRepresentante($strSlTipoViculo);
    }

    if(!is_null($selNaturezaVinculo) && !empty($selNaturezaVinculo)){
        $objMdPetVincRepresentantDTO->setStrTpVinc($selNaturezaVinculo);
    }

    if(is_countable($selTipoPoder)){

        // Retorna os Vínculos que teem o(s) Tipo(s) de Poder(es) selecionados:
        $objMdPetRelVincRepTpPoderRN    = new MdPetRelVincRepTpPoderRN();
        $objMdPetRelVincRepTpPoderDTO   = new MdPetRelVincRepTpPoderDTO();
        $objMdPetRelVincRepTpPoderDTO->setNumIdTipoPoderLegal($selTipoPoder, InfraDTO::$OPER_IN);
        $objMdPetRelVincRepTpPoderDTO->retNumIdVinculoRepresent();
        $arrMdPetRelVincRepTpPoder = InfraArray::converterArrInfraDTO($objMdPetRelVincRepTpPoderRN->listar($objMdPetRelVincRepTpPoderDTO), 'IdVinculoRepresent');

        if(is_countable($arrMdPetRelVincRepTpPoder) && count($arrMdPetRelVincRepTpPoder) > 0){

            $tipoVinculo = empty($strSlTipoViculo) || is_null($strSlTipoViculo) ? ['L','E'] : [$strSlTipoViculo];

            $objMdPetVincRepresentantDTO->adicionarCriterio(
                array('IdMdPetVinculoRepresent', 'TipoRepresentante')
                , array(InfraDTO::$OPER_IN, InfraDTO::$OPER_IN)
                , array($arrMdPetRelVincRepTpPoder, $tipoVinculo), InfraDTO::$OPER_LOGICO_OR);

        }

    }

    //Validação para verificar se a unidade atual é compatival com a unidade de configuração do vinculo
    $idUnidadeAtual = SessaoSEI::getInstance()->getNumIdUnidadeAtual();

    $mdPetVincTpProcessoRN = new MdPetVincTpProcessoRN();
    $objMdPetVincTpProcessoDTO = new MdPetVincTpProcessoDTO();

    $objMdPetVincTpProcessoDTO->retNumIdUnidade();
    $objMdPetVincTpProcessoDTO->setNumIdMdPetVincTpProcesso(MdPetVincTpProcessoRN::$ID_FIXO_MD_PET_VINCULO_USU_EXT);
    $objMdPetVincTpProcessoDTO = $mdPetVincTpProcessoRN->consultar($objMdPetVincTpProcessoDTO);
    $bolAcoes = true;
    if ($objMdPetVincTpProcessoDTO) {
        if ($objMdPetVincTpProcessoDTO->getNumIdUnidade() != $idUnidadeAtual) {
            $bolAcoes = false;
        }
    }

    $objMdPetVincRepresentantDTO->retTodos();

    //$objMdPetVincRepresentantDTO->setNumIdContatoOutorg();
    $objMdPetVincRepresentantDTO->retNumIdMdPetVinculo();
    $objMdPetVincRepresentantDTO->retNumIdMdPetVinculoRepresent();
    $objMdPetVincRepresentantDTO->retStrTipoRepresentante();
    $objMdPetVincRepresentantDTO->retStrStaEstado();
    $objMdPetVincRepresentantDTO->retStrCpfProcurador();
    $objMdPetVincRepresentantDTO->retStrRazaoSocialNomeVinc();
    $objMdPetVincRepresentantDTO->retStrCNPJ();
    $objMdPetVincRepresentantDTO->retStrTpVinc();
    $objMdPetVincRepresentantDTO->retNumIdContatoVinc();
    $objMdPetVincRepresentantDTO->retStrNomeProcurador();
    $objMdPetVincRepresentantDTO->retDblIdProcedimentoVinculo();

    // chamada através do botão do Processo ou do Documento
    if ($arvoreVincListar) {

        //Recuperar Ids que contem vinculos
        $objMdPetVinculoRN = new MdPetVinculoRN();
        $arrObjMdPetVinculoDTO = $objMdPetVinculoRN->consultarProcedimentoVinculo(array($idProcedimento, 'retornoDTO' => true));

        // PJ - Representantes
        if ($arrObjMdPetVinculoDTO) {

            $idRepresentantes = InfraArray::converterArrInfraDTO($arrObjMdPetVinculoDTO, 'IdMdPetVinculoRepresent');

            if ($idRepresentantes) {
                $objMdPetVincRepresentantDTO->setNumIdMdPetVinculoRepresent($idRepresentantes, InfraDTO::$OPER_IN);
            } else {
                $objMdPetVincRepresentantDTO->setNumIdMdPetVinculoRepresent(0);
            }
        } else {
            $objMdPetVincRepresentantDTO->setNumIdMdPetVinculoRepresent(0);
        }

        PaginaSEI::getInstance()->setTipoPagina(InfraPagina::$TIPO_PAGINA_SIMPLES);
    } else {

        $idUnidadeAtual = SessaoSEI::getInstance()->getNumIdUnidadeAtual();
        $objMdPetVinculoRN = new MdPetVinculoRN();
        if ($_GET['acao'] == 'md_pet_adm_vinc_listar') {
//            $arrIdsProcedimento = $objMdPetVinculoRN->consultarIdProcedimentoByUnidade($idUnidadeAtual);
//            if(count($arrIdsProcedimento)>0) {
//                $objMdPetVincRepresentantDTO->setDblIdProcedimentoVinculo($arrIdsProcedimento, InfraDTO::$OPER_IN);
//            }else{
//                $objMdPetVincRepresentantDTO->setDblIdProcedimentoVinculo(0);
//            }
        }
    }

    PaginaSEI::getInstance()->prepararOrdenacao($objMdPetVincRepresentantDTO, 'CpfProcurador', InfraDTO::$TIPO_ORDENACAO_ASC);
    PaginaSEI::getInstance()->prepararPaginacao($objMdPetVincRepresentantDTO);

    $arrObjMdPetVincRepresentantDTO = $objMdPetVincRepresentantRN->listar($objMdPetVincRepresentantDTO);

    PaginaSEI::getInstance()->processarPaginacao($objMdPetVincRepresentantDTO);

    $strCnpj = trim(PaginaSEI::getInstance()->recuperarCampo('txtCnpj'));
    $strCpf = trim(PaginaSEI::getInstance()->recuperarCampo('txtCpf'));

} catch (Exception $e) {
    PaginaSEI::getInstance()->processarExcecao($e);
}

// Recuperando os documentos da procuração
foreach ($arrObjMdPetVincRepresentantDTO as $objMdPetVincRepresentantDTO) {
    $arrIdMdPetVinculoRepresent[] = $objMdPetVincRepresentantDTO->getNumIdMdPetVinculoRepresent();
}

if ($arrIdMdPetVinculoRepresent) {
    $objMdPetVincDocumentoRN = new MdPetVincDocumentoRN();
    $objMdPetVincDocumentoDTO = new MdPetVincDocumentoDTO();
    $objMdPetVincDocumentoDTO->setNumIdMdPetVinculoRepresent($arrIdMdPetVinculoRepresent, InfraDTO::$OPER_IN);
    $objMdPetVincDocumentoDTO->retDblIdDocumento();
    $objMdPetVincDocumentoDTO->retStrProtocoloFormatadoProtocolo();
    $objMdPetVincDocumentoDTO->retNumIdMdPetVinculoRepresent();
    $objMdPetVincDocumentoDTO->adicionarCriterio(
        array('TipoDocumento', 'TipoDocumento'),
        array(InfraDTO::$OPER_IGUAL, InfraDTO::$OPER_IGUAL),
        array('E', 'P'),
        array(InfraDTO::$OPER_LOGICO_OR)
    );

    $arrObjMdPetVincDocumentoDTO = $objMdPetVincDocumentoRN->listar($objMdPetVincDocumentoDTO);
    $arrDocumento = [];
    foreach ($arrObjMdPetVincDocumentoDTO as $objMdPetVincDocumentoDTO) {
        $arrDocumento[$objMdPetVincDocumentoDTO->getNumIdMdPetVinculoRepresent()] = $objMdPetVincDocumentoDTO;
    }
}
$arrSelectTipoVinculo = array(
    MdPetVincRepresentantRN::$PE_RESPONSAVEL_LEGAL => 'Responsável Legal',
    MdPetVincRepresentantRN::$PE_PROCURADOR_ESPECIAL => 'Procurador Especial',
    MdPetVincRepresentantRN::$PE_PROCURADOR_SIMPLES => 'Procurador Simples',
);
$numRegistros = count($arrObjMdPetVincRepresentantDTO);

if ($numRegistros > 0) {

    $strResultado = '';
    $strSumarioTabela = $strCaptionTabela = 'Vinculações e Procurações Eletrônicas';
    $strResultado .= '<table style="width: 100%" class="infraTable" summary="' . $strSumarioTabela . '">';
    $strResultado .= '<caption class="infraCaption">' . PaginaSEI::getInstance()->gerarCaptionTabela($strCaptionTabela, $numRegistros) . '</caption>';
    $strResultado .= '<thead>';
    $strResultado .= '<tr>';
    $strResultado .= '<th class="infraTh"><div style="width:110px">' . PaginaSEI::getInstance()->getThOrdenacao($objMdPetVincRepresentantDTO, $strColuna70, $strColuna71, $arrObjMdPetVincRepresentantDTO) . '</div></th>';
    $strResultado .= '<th class="infraTh"><div style="width:130px">' . PaginaSEI::getInstance()->getThOrdenacao($objMdPetVincRepresentantDTO, $strColuna10, $strColuna11, $arrObjMdPetVincRepresentantDTO) . '</div></th>';
    $strResultado .= '<th class="infraTh"><div style="width:180px">' . PaginaSEI::getInstance()->getThOrdenacao($objMdPetVincRepresentantDTO, $strColuna20, $strColuna21, $arrObjMdPetVincRepresentantDTO) . '</div></th>';
    $strResultado .= '<th class="infraTh"><div style="width:120px">' . PaginaSEI::getInstance()->getThOrdenacao($objMdPetVincRepresentantDTO, $strColuna30, $strColuna31, $arrObjMdPetVincRepresentantDTO) . '</div></th>';
    $strResultado .= '<th class="infraTh"><div style="width:150px">' . PaginaSEI::getInstance()->getThOrdenacao($objMdPetVincRepresentantDTO, $strColuna40, $strColuna41, $arrObjMdPetVincRepresentantDTO) . '</div></th>';
    $strResultado .= '<th class="infraTh"><div style="width:130px">' . PaginaSEI::getInstance()->getThOrdenacao($objMdPetVincRepresentantDTO, $strColuna50, $strColuna51, $arrObjMdPetVincRepresentantDTO) . '</div></th>';
    $strResultado .= '<th class="infraTh"><div>'.$strColuna80.'</div></th>';
    $strResultado .= '<th class="infraTh"><div style="width:90px; text-align: center">' . PaginaSEI::getInstance()->getThOrdenacao($objMdPetVincRepresentantDTO, $strColuna60, $strColuna61, $arrObjMdPetVincRepresentantDTO) . '</siv></th>';
    $strResultado .= '<th class="infraTh"><div style="width:90px; text-align: center">Ações</div></th>';
    $strResultado .= '</tr>';
    $strResultado .= '</thead><tbody>';
    //Populando obj para tabela

    for ($i = 0; $i < $numRegistros; $i++) {
        $strTipoRepresentante = MdPetVincRepresentantDTO::getStrNomeTipoRepresentante(
            $arrObjMdPetVincRepresentantDTO[$i]->getStrTipoRepresentante()
        );

        $arrSerieSituacao = MdPetVincRepresentantDTO::getArrSerieSituacao(
            $arrObjMdPetVincRepresentantDTO[$i]->getStrStaEstado()
        );
        $strLabelSituacao = $arrSerieSituacao['strSituacao'];

        //Buscar documento da procuração
        $idVinculacao = $arrObjMdPetVincRepresentantDTO[$i]->getNumIdMdPetVinculo();
        //$idDocumentoFormatado = $arrDocumento[$arrObjMdPetVincRepresentantDTO[$i]->getNumIdMdPetVinculoRepresent()]->getStrProtocoloFormatadoProtocolo();

        $idDocumento = $arrDocumento[$arrObjMdPetVincRepresentantDTO[$i]->getNumIdMdPetVinculoRepresent()] ? $arrDocumento[$arrObjMdPetVincRepresentantDTO[$i]->getNumIdMdPetVinculoRepresent()]->getDblIdDocumento() : '';
        if (!in_array($arrObjMdPetVincRepresentantDTO[$i]->getStrTipoRepresentante(), $arrSelectTipoVinculo)) {
            $arrSelectTipoVinculo[$arrObjMdPetVincRepresentantDTO[$i]->getStrTipoRepresentante()] = $arrObjMdPetVincRepresentantDTO[$i]->getStrNomeTipoRepresentante();
        }
        $strResultado .= '<tr class="infraTrClara">';
        //Recuperando Contato do Vinculo
        $objContatoDTO = new ContatoDTO();
        $objContatoDTO->setNumIdContato($arrObjMdPetVincRepresentantDTO[$i]->getNumIdContatoVinc());
        $objContatoDTO->setBolExclusaoLogica(false);
        $objContatoDTO->retDblCnpj();
        $objContatoDTO->retDblCpf();
        $objContatoRN = new ContatoRN();
        $objContatoRN = $objContatoRN->consultarRN0324($objContatoDTO);

        $strResultado .= '<td><div style="width:110px">' . PaginaSEI::tratarHTML($arrObjMdPetVincRepresentantDTO[$i]->getNaturezaVinculo()) . '</div></td>';
        $strResultado .= '<td><div style="width:130px">' . (($objContatoRN->getDblCpf() == null) ? InfraUtil::formatarCnpj($objContatoRN->getDblCnpj()) : InfraUtil::formatarCpf($objContatoRN->getDblCpf()) ) . '</div></td>';
        $strResultado .= '<td><div style="width:180px; word-wrap: break-word">' . PaginaSEI::tratarHTML($arrObjMdPetVincRepresentantDTO[$i]->getStrRazaoSocialNomeVinc()) . '</div></td>';
        $strResultado .= '<td><div style="width:110px">' . InfraUtil::formatarCpf($arrObjMdPetVincRepresentantDTO[$i]->getStrCpfProcurador()) . '</div></td>';
        $strResultado .= '<td><div style="width:150px">' . $arrObjMdPetVincRepresentantDTO[$i]->getStrNomeProcurador() . '</div></td>';
        $strResultado .= '<td><div style="width:130px">' . $strTipoRepresentante . '</div></td>';
        $strResultado .= '<td><div class="text-sm" style="width:180px; word-wrap: break-word; font-size: .8rem">'.$arrObjMdPetVincRepresentantDTO[$i]->getStrTipoPoderesLista().'</div></td>';
        $strResultado .= '<td><div style="text-align: center; width:90px">' . $strLabelSituacao . '</div></td>';

        $title = 'Consultar' . (($arrObjMdPetVincRepresentantDTO[$i]->getStrTipoRepresentante() == MdPetVincRepresentantRN::$PE_RESPONSAVEL_LEGAL) ? 'Vinculação' : 'Procuração');

        $acaoConsulta = '';
        $strLinkConsultaDocumento = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=documento_visualizar&id_documento=' . $idDocumento . '&arvore=1');
        $iconeConsulta = '<img src="' . PaginaSEI::getInstance()->getDiretorioSvgGlobal() . '/consultar.svg?'.Icone::VERSAO.'" title="' . $title . '" alt="' . $title . '" class="infraImg" />';
        $acaoConsulta = '<a target="_blank" href="' . $strLinkConsultaDocumento . '">' . $iconeConsulta . '</a>';

        $acaoResponsavel = '';
        if ($isAdm) {
            if ($arrObjMdPetVincRepresentantDTO[$i]->getStrTipoRepresentante() == MdPetVincRepresentantRN::$PE_RESPONSAVEL_LEGAL) {
                $acaoResponsavel = '';
                if ($arrObjMdPetVincRepresentantDTO[$i]->getStrStaEstado() == MdPetVincRepresentantRN::$RP_ATIVO) { //
                    $strLinkSuspenderVinc = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_pet_vinc_suspender_restabelecer&acao_origem=' . $_GET['acao'] . '&acao_retorno=' . $_GET['acao'] . '&idVinculo=' . $idVinculacao . '&operacao=' . MdPetVincRepresentantRN::$RP_SUSPENSO);
                    $iconeResponsavel = '<img style="width:24px;" src="modulos/peticionamento/imagens/svg/suspender_responsavel_legal.svg?'.Icone::VERSAO.'" title="Suspender Responsável Legal" alt="Suspender Responsável Legal" class="infraImg" />';

                    $acaoResponsavel = '<a href="' . $strLinkSuspenderVinc . '">' . $iconeResponsavel . '</a>';
                    if ($bolAcoes) {
                        $acaoResponsavel = '<a href="' . $strLinkSuspenderVinc . '">' . $iconeResponsavel . '</a>';
                    } else {
                        $acaoResponsavel = '<a onclick="mostrarExcessao();">' . $iconeResponsavel . '</a>';
                    }
                    $strLinkResponsavelVinc = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_pet_vinc_responsavel_cadastrar&acao_origem=' . $_GET['acao'] . '&acao_retorno=' . $_GET['acao'] . '&idVinculo=' . $arrObjMdPetVincRepresentantDTO[$i]->getNumIdMdPetVinculo());
                    $iconeResponsavel = '<img src="' . PaginaSEI::getInstance()->getDiretorioSvgGlobal() . '/alterar.svg?'.Icone::VERSAO.'" title="Alterar o Responsável Legal" alt="Alterar o Responsável Legal" class="infraImg" />';
                    if ($bolAcoes) {
                        $acaoResponsavel .= '<a href="' . $strLinkResponsavelVinc . '">' . $iconeResponsavel . '</a>';
                    } else {
                        $acaoResponsavel .= '<a onclick="mostrarExcessao();">' . $iconeResponsavel . '</a>';
                    }
                } else if ($arrObjMdPetVincRepresentantDTO[$i]->getStrStaEstado() == MdPetVincRepresentantRN::$RP_SUSPENSO) {
                    $strLinkRestabelecerVinc = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_pet_vinc_suspender_restabelecer&acao_origem=' . $_GET['acao'] . '&acao_retorno=' . $_GET['acao'] . '&idVinculo=' . $idVinculacao . '&operacao=' . MdPetVincRepresentantRN::$RP_ATIVO);
                    $iconeResponsavel = '<img style="width:24px;" src="modulos/peticionamento/imagens/png/retirarSuspensao.png" title="Restabelecer Responsável Legal" alt="Restabelecer Responsável Legal" class="infraImg" />';
                    if ($bolAcoes) {
                        $acaoResponsavel = '<a href="' . $strLinkRestabelecerVinc . '">' . $iconeResponsavel . '</a>';
                    } else {
                        $acaoResponsavel = '<a onclick="mostrarExcessao();">' . $iconeResponsavel . '</a>';
                    }
                }
            }
        }
        $strResultado .= '<td align="center">' . $acaoConsulta . $acaoResponsavel . '</td>';

        $strResultado .= '</tr>';


    }
    $strResultado .= '</tbody></table>';

}

$strSelStatus = MdPetVinculoINT::montarSelectStaEstado('null', '&nbsp;', $strStatus);
$strOptionsTiposPoder = MdPetTipoPoderLegalINT::montarOptionsTipoPoder($selTipoPoder);

PaginaSEI::getInstance()->montarDocType();
PaginaSEI::getInstance()->abrirHtml();
PaginaSEI::getInstance()->abrirHead();
PaginaSEI::getInstance()->montarMeta();
PaginaSEI::getInstance()->montarTitle(PaginaSEI::getInstance()->getStrNomeSistema() . ' - ' . $strTitulo);
PaginaSEI::getInstance()->montarStyle();
PaginaSEI::getInstance()->abrirStyle();
require_once('md_pet_adm_vinc_lista_css.php');
PaginaSEI::getInstance()->fecharStyle();
PaginaSEI::getInstance()->montarJavaScript();
PaginaSEI::getInstance()->abrirJavaScript();
?>
function desvincularProcuracao(link){
    infraAbrirJanela(link,'janelaDesvinculo',700,450,'location=0,status=1,resizable=1,scrollbars=1');
}

function inicializar(){
    infraEfeitoTabelas();
}
<?php
PaginaSEI::getInstance()->fecharJavaScript();
PaginaSEI::getInstance()->fecharHead();
PaginaSEI::getInstance()->abrirBody($strTitulo, 'onload="inicializar();"');

?>

<form id="frmPesquisa" method="post" action="<?= SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . $_GET['acao'] . '&acao_origem=' . $_GET['acao']) ?>">
    <?
    if ($arvoreVincListar):

        PaginaSEI::getInstance()->abrirAreaDados("auto");
    ?>
        <div class="row">
            <div class="col-sm-12">
                <input type="hidden" id="hdnIdProcedimento" name="hdnIdProcedimento" value="<?= $idProcedimento ?>">
                <p class="mb-4 mt-2" style="font-size: .875rem">A tabela abaixo exibe as vinculações ativas aos Interessados do presente processo como Responsável Legal, Procurador Especial e Procurador Simples.</p>
            </div>
        </div>

    <?
    else:

        PaginaSEI::getInstance()->montarBarraComandosSuperior($arrComandos);

    ?>

        <div class="row">
            <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                <p class="mb-4 mt-2" style="font-size: .875rem">
                <? if ($isAdm): ?>
                    A Vinculação a Pessoa Jurídica como Responsável Legal pode ser Suspensa, a partir de indício de falsidade ou
                    de prática de ato ilícito, devendo indicar o Número SEI de documento com registro do indício e da diligência
                    para sua investigação, afetando o vínculo do Usuário Externo com a Pessoa Jurídica e Procurações Eletrônicas
                    por ele geradas em sua representação. Em situações que o Usuário Externo não consiga realizar diretamente,
                    também é possível Alterar o Responsável Legal.
                <? else: ?>
                    Este relatório permite visualizar as Vinculações a Pessoas Jurídicas como Responsável Legal, Procurador
                    Especial e Procurador Simples concedidas no âmbito do SEI.
                <? endif ?>
                </p>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12 col-md-6 col-lg-3 col-xl-3">
                <div class="form-group">
                    <label id="lblNaturezaVinculo" for="selNaturezaVinculo" class="infraLabelOpcional"><?= $strColuna70 ?>:</label>
                    <select id="selNaturezaVinculo" name="selNaturezaVinculo" class="infraSelect form-control" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>">
                        <option value=""></option>
                        <option value="F" <?= $selNaturezaVinculo == 'F' ? 'selected="selected"' : '' ?> >Pessoa Física</option>
                        <option value="J" <?= $selNaturezaVinculo == 'J' ? 'selected="selected"' : '' ?> >Pessoa Jurídica</option>
                    </select>
                </div>
            </div>
            <div class="col-sm-12 col-md-6 col-lg-3 col-xl-3">
                <div class="form-group">
                    <label id="lblCnpj" for="txtCnpj" class="infraLabelOpcional"><?= $strColuna10 ?>:</label>
                    <input type="text" id="txtCnpj" name="txtCnpj" class="infraText form-control"
                        value="<?= $strCnpj ?>" maxlength="18"
                        tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"
                        onkeydown="return mascararCampoCnpjCpf(this);" autofocus/>
                </div>
            </div>
            <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6">
                <div class="form-group">
                    <label id="lblRazaoSocial" for="txtRazaoSocial" class="infraLabelOpcional"><?= $strColuna20 ?>:</label>
                    <input type="text" id="txtRazaoSocial" name="txtRazaoSocial" class="infraText form-control"
                        value="<?= PaginaSEI::tratarHTML($strRazaoSocial) ?>" maxlength="100"
                        tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12 col-md-4 col-lg-3 col-xl-2">
                <div class="form-group">
                    <label id="lblCpf" for="txtCpf" class="infraLabelOpcional"><?= $strColuna30 ?>:</label>
                    <input type="text" id="txtCpf" name="txtCpf" class="infraText form-control"
                        value="<?= $strCpf ?>" maxlength="100"
                        tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"
                        onkeypress="return infraMascaraCPF(this,event,250);"/>
                </div>
            </div>
            <div class="col-sm-12 col-md-8 col-lg-3 col-xl-4">
                <div class="form-group">
                    <label id="lblNomeProcurador" for="txtNomeProcurador" class="infraLabelOpcional"><?= $strColuna40 ?>:</label>
                    <input type="text" id="txtNomeProcurador" name="txtNomeProcurador" class="infraText form-control"
                        value="<?= PaginaSEI::tratarHTML($strNome) ?>" maxlength="100"
                        tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
                </div>
            </div>
            <div class="col-sm-12 col-md-6 col-lg-3 col-xl-3">
                <div class="form-group">
                    <label id="lblTipoVinculo" for="slTipoVinculo" class="infraLabelOpcional"><?= $strColuna50 ?>:</label>
                    <select name="slTipoViculo" id="slTipoViculo" class="infraSelect form-control" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>">
                        <option value=""></option>
                        <?php if ($arrSelectTipoVinculo) : ?>
                            <?php foreach ($arrSelectTipoVinculo as $chaveTipoVinculo => $itemTipoVinculo) : ?>
                                <option value="<?php echo $chaveTipoVinculo; ?>" <?= ($chaveTipoVinculo == $strSlTipoViculo) ? 'selected="selected"' : '' ?>>
                                    <?= $itemTipoVinculo ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
            </div>
            <div class="col-sm-12 col-md-6 col-lg-3 col-xl-3">
                <div class="form-group">
                    <label id="lblStatus" for="selStatus" class="infraLabelOpcional"><?= $strColuna60 ?>:</label>
                    <select id="selStatus" name="selStatus" class="infraSelect form-control" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>">
                        <?= $strSelStatus ?>
                    </select>
                </div>
            </div>
            <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6">
                <div class="form-group">
                    <label id="lblTipoPoder" for="selTipoPoder" class="infraLabelOpcional"><?= $strColuna80 ?>:</label>
                    <select id="selTipoPoder" name="selTipoPoder[]" class="infraSelect multipleSelect form-control" multiple="multiple" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>">
                        <!-- $strOptionsTiposPoder ?> -->
                        <?= MDPetTipoPoderLegalINT::montarArrSelect(null, null, $selTipoPoder) ?>
                    </select>
                </div>
            </div>
        </div>
        <div id="camposForm">

    <?php

        PaginaSEI::getInstance()->fecharAreaDados();

    endif;

    PaginaSEI::getInstance()->montarAreaTabela($strResultado, $numRegistros);

    //PaginaSEI::getInstance()->montarAreaDebug();
    PaginaSEI::getInstance()->montarBarraComandosInferior($arrComandos);

    ?>
    </div>

</form>
<?

require_once 'md_pet_adm_vinc_lista_js.php';
PaginaSEI::getInstance()->fecharBody();
PaginaSEI::getInstance()->fecharHtml();

?>