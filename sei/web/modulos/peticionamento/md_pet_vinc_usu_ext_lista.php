<?php
/**
 * Created by PhpStorm.
 * User: jhon.carvalho
 * Date: 08/02/2018
 * Time: 11:16
 */
try {
    require_once dirname(__FILE__) . '/../../SEI.php';

    session_start();
    SessaoSEI::getInstance()->validarLink();
    //SessaoSEI::getInstance()->validarPermissao($_GET['acao']);

    switch ($_GET['acao']) {
        case 'md_pet_vinc_usu_ext_pe_listar':
            $strTitulo = 'Procurações Eletrônicas';
            break;
    }
    $arrObj = array();
    PaginaSEIExterna::getInstance()->salvarCamposPost(array('txtCnpj', 'txtRazaoSocial', 'txtCpf', 'txtNomeProcurador', 'slTipoProcuracao', 'slSituacao', 'sllblAbrangencia', 'sllblValidade', 'txtPeriodoInicio', 'txtPeriodoFim'));
    $strLinkMotivoRevogar = SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=processo_eletronico_responder_motivo_revogar');
    $strLinkMotivoRenunciar = SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=processo_eletronico_responder_motivo_renunciar');
    $strLinkConsultaDocumento = SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=md_pet_vinc_doc_procuracao_consultar&id_documento=');

    $strCnpj = trim(PaginaSEIExterna::getInstance()->recuperarCampo('txtCnpj'));
    $strCnpj = InfraUtil::retirarFormatacao($strCnpj);

    if ($strCnpj) {
        $intCnpj = intval($strCnpj);
    }

    $strCpf = trim(PaginaSEIExterna::getInstance()->recuperarCampo('txtCpf'));
    $strCpf = InfraUtil::retirarFormatacao($strCpf);

    if ($strCpf) {
        $intCpf = intval($strCpf);
    }

    $strRazaoSocial = trim(PaginaSEIExterna::getInstance()->recuperarCampo('txtRazaoSocial'));
    $strNome = trim(PaginaSEIExterna::getInstance()->recuperarCampo('txtNomeProcurador'));
    $strTipoViculo = trim(PaginaSEIExterna::getInstance()->recuperarCampo('slTipoViculo'));
    $strSituacao = trim(PaginaSEIExterna::getInstance()->recuperarCampo('slSituacao'));
    //Novos Combos
    $strTipoProcuracao = trim(PaginaSEIExterna::getInstance()->recuperarCampo('slTipoProcuracao'));
    $strAbrangencia = trim(PaginaSEIExterna::getInstance()->recuperarCampo('sllblAbrangencia'));
    $strValidade = trim(PaginaSEIExterna::getInstance()->recuperarCampo('sllblValidade'));
    $dataInicio = PaginaSEIExterna::getInstance()->recuperarCampo('txtPeriodoInicio');
    $dataFim = PaginaSEIExterna::getInstance()->recuperarCampo('txtPeriodoFim');

    $idUsuarioExterno = SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno();
    $usuarioDTO = new UsuarioDTO();
    $usuarioRN = new UsuarioRN();
    $usuarioDTO->retNumIdContato();
    $usuarioDTO->setNumIdUsuario($idUsuarioExterno);
    $contatoExterno = $usuarioRN->consultarRN0489($usuarioDTO);
    $idContatoExterno = $contatoExterno->getNumIdContato();


    $objMdPetVincRepresentantRN = new MdPetVincRepresentantRN();
    $objMdPetVincRepresentantSuspensoDTO = new MdPetVincRepresentantDTO();
    $objMdPetVincRepresentantSuspensoDTO->retStrStaEstado();
    $objMdPetVincRepresentantSuspensoDTO->retNumIdMdPetVinculoRepresent();
    $objMdPetVincRepresentantSuspensoDTO->adicionarCriterio(array('IdContato', 'IdContatoOutorg'),
        array(InfraDTO::$OPER_IGUAL, InfraDTO::$OPER_IGUAL),
        array($idContatoExterno, $idContatoExterno),
        array(InfraDTO::$OPER_LOGICO_OR));

    $objMdPetVincRepresentantSuspensoDTO->adicionarCriterio(array('TipoRepresentante', 'StaEstado'),
        array(InfraDTO::$OPER_IGUAL, InfraDTO::$OPER_IGUAL),
        array(MdPetVincRepresentantRN::$PE_RESPONSAVEL_LEGAL, MdPetVincRepresentantRN::$RP_SUSPENSO),
        array(InfraDTO::$OPER_LOGICO_AND));

    $arrObjMdPetVincRepresentantSuspensoDTO = $objMdPetVincRepresentantRN->listar($objMdPetVincRepresentantSuspensoDTO);

    //Recuperando os documentos da suspensão
    $staEstadoRepresentantSuspenso = '';
    if ($arrObjMdPetVincRepresentantSuspensoDTO) {
        $staEstadoRepresentantSuspenso = current($arrObjMdPetVincRepresentantSuspensoDTO)->getStrStaEstado();
    }

    $arrIdVincRepresentantSuspenso = InfraArray::converterArrInfraDTO($arrObjMdPetVincRepresentantSuspensoDTO, 'IdMdPetVinculoRepresent');
    // Suspenso - fim

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
        $objMdPetVincRepresentantDTO->adicionarCriterio(
            array('CNPJ', 'CPF'),
            array(InfraDTO::$OPER_IGUAL, InfraDTO::$OPER_IGUAL),
            array($intCnpj, $intCnpj),
            array(InfraDTO::$OPER_LOGICO_OR));
    }

    if ($strTipoProcuracao != '') {

        $objMdPetVincRepresentantDTO->setStrTipoRepresentante($strTipoProcuracao);
    } else {
        $strTipoProcuracao = '';
    }

    if ($strSituacao != '') {
        $objMdPetVincRepresentantDTO->setStrStaEstado($strSituacao);
    }
    if ($strAbrangencia != '') {

        if ($strAbrangencia == MdPetVincRepresentantRN::$PR_ESPECIFICO) {
            $objMdPetVincRepresentantDTO->setStrStaAbrangencia($strAbrangencia);
        } else if ($strAbrangencia == MdPetVincRepresentantRN::$PR_QUALQUER) {
            $objMdPetVincRepresentantDTO->adicionarCriterio(
                array('StaAbrangencia', 'StaAbrangencia'),
                array(InfraDTO::$OPER_IGUAL, InfraDTO::$OPER_IGUAL),
                array($strAbrangencia, null),
                array(InfraDTO::$OPER_LOGICO_OR)
            );
        }

    } else {
        $strAbrangencia = '';
    }
    if ($strValidade == "null") {
        $objMdPetVincRepresentantDTO->setStrStaAbrangencia(null);
    } else {
        //Filtro de Buscar Datas entre periodos
        if ($dataInicio != "" && $dataFim != "") {
            $objMdPetVincRepresentantDTO->adicionarCriterio(array('DataLimite', 'DataLimite'),
                array(InfraDTO::$OPER_MAIOR_IGUAL, InfraDTO::$OPER_MENOR_IGUAL),
                array($dataInicio, $dataFim),
                InfraDTO::$OPER_LOGICO_AND);
        } else {
            $dataInicio = "";
            $dataFim = "";
        }
    }

    $objMdPetVincRepresentantDTO->retNumIdMdPetVinculoRepresent();
    $objMdPetVincRepresentantDTO->retNumIdMdPetVinculo();
    $objMdPetVincRepresentantDTO->retStrTipoRepresentante();
    $objMdPetVincRepresentantDTO->retStrRazaoSocialNomeVinc();
    $objMdPetVincRepresentantDTO->retStrCNPJ();
    $objMdPetVincRepresentantDTO->retStrTpVinc();
    $objMdPetVincRepresentantDTO->retStrStaEstado();
    $objMdPetVincRepresentantDTO->retNumIdContatoVinc();
    $objMdPetVincRepresentantDTO->retNumIdContato();
    $objMdPetVincRepresentantDTO->retNumIdContatoOutorg();

    $objMdPetVincRepresentantDTO->retStrCpfProcurador();
    $objMdPetVincRepresentantDTO->retStrNomeProcurador();
    $objMdPetVincRepresentantDTO->retStrStaAbrangencia();
    $objMdPetVincRepresentantDTO->retDthDataLimite();
    $objMdPetVincRepresentantDTO->retDblIdProcedimentoVinculo();

    $objMdPetVincRepresentantDTO->adicionarCriterio(array('IdContato', 'IdContatoOutorg'),
        array(InfraDTO::$OPER_IGUAL, InfraDTO::$OPER_IGUAL),
        array($idContatoExterno, $idContatoExterno),
        array(InfraDTO::$OPER_LOGICO_OR));

    $objMdPetVincRepresentantDTO->adicionarCriterio(array('TipoRepresentante'),
        array(InfraDTO::$OPER_NOT_IN),
        array(array(MdPetVincRepresentantRN::$PE_RESPONSAVEL_LEGAL)));

    PaginaSEIExterna::getInstance()->prepararOrdenacao($objMdPetVincRepresentantDTO, 'TipoRepresentante', InfraDTO::$TIPO_ORDENACAO_ASC);
    PaginaSEIExterna::getInstance()->prepararPaginacao($objMdPetVincRepresentantDTO);

    $arrObjMdPetVincRepresentantDTO = $objMdPetVincRepresentantRN->listar($objMdPetVincRepresentantDTO);

    //Recuperando os documentos da procuração
    $arrIdVincRepresentant = InfraArray::converterArrInfraDTO($arrObjMdPetVincRepresentantDTO, 'IdMdPetVinculoRepresent');

    PaginaSEIExterna::getInstance()->processarPaginacao($objMdPetVincRepresentantDTO);

    $strCnpj = trim(PaginaSEIExterna::getInstance()->recuperarCampo('txtCnpj'));
    $strCpf = trim(PaginaSEIExterna::getInstance()->recuperarCampo('txtCpf'));

} catch (Exception $e) {
    PaginaSEIExterna::getInstance()->processarExcecao($e);
}

$arrIdsSeries = (new MdPetVinculoUsuExtRN)->retornaSeriesInfraParamentro($idSerieFormulario);
$objMdPetVincDocumentoDTO = new MdPetVincDocumentoDTO();
$objMdPetVincDocumentoDTO->setStrTipoDocumento(array(MdPetVincDocumentoRN::$TP_PROTOCOLO_PROCURACAO_ESPECIAL), InfraDTO::$OPER_IN);

$objMdPetVincDocumentoDTO->retDblIdDocumento();
$objMdPetVincDocumentoDTO->retStrProtocoloFormatadoProtocolo();
$objMdPetVincDocumentoDTO->retNumIdSerie();
$objMdPetVincDocumentoDTO->retNumIdMdPetVinculo();
$objMdPetVincDocumentoDTO->retStrNomeSerieProtocolo();
$numRegistros = count($arrIdVincRepresentant);
if ($numRegistros > 0) {

    $strResultado = '';
    $strSumarioTabela = 'Procurações Eletrônicas';
    $strCaptionTabela = 'Procurações Eletrônicas';
    $strResultado .= '<table width="100%" class="infraTable" summary="' . $strSumarioTabela . '">';
    $strResultado .= '<caption class="infraCaption">' . PaginaSEIExterna::getInstance()->gerarCaptionTabela($strCaptionTabela, $numRegistros) . '</caption>';

    $strResultado .= '<tr>';
    //$strResultado .= '<th class="infraTh" width="1%">' . PaginaSEIExterna::getInstance()->getThCheck() . '</th>' . "\n";
    //$strResultado .= '<th class="infraTh" width="13%">' . PaginaSEIExterna::getInstance()->getThOrdenacao($objMdPetVincRepresentantDTO, 'N° do Documento', 'ProtocoloFormatado', $arrObjMdPetVincRepresentantDTO) . '</th>';
    $strResultado .= '<th class="infraTh"><div style="width:130px" class="text-center">' . PaginaSEIExterna::getInstance()->getThOrdenacao($objMdPetVincRepresentantDTO, 'CPF/CNPJ Outorgante', 'CNPJ', $arrObjMdPetVincRepresentantDTO) . '</div></th>';
    $strResultado .= '<th class="infraTh"><div style="width:170px">' . PaginaSEIExterna::getInstance()->getThOrdenacao($objMdPetVincRepresentantDTO, 'Nome/Razão Social do Outorgante', 'RazaoSocialNomeVinc', $arrObjMdPetVincRepresentantDTO) . '</div></th>';
    $strResultado .= '<th class="infraTh"><div style="width:100px">' . PaginaSEIExterna::getInstance()->getThOrdenacao($objMdPetVincRepresentantDTO, 'CPF Outorgado', 'CpfProcurador', $arrObjMdPetVincRepresentantDTO) . '</div></th>';
    $strResultado .= '<th class="infraTh"><div style="width:180px">' . PaginaSEIExterna::getInstance()->getThOrdenacao($objMdPetVincRepresentantDTO, 'Nome do Outorgado', 'NomeProcurador', $arrObjMdPetVincRepresentantDTO) . '</div></th>';
    $strResultado .= '<th class="infraTh"><div style="width:130px">' . PaginaSEIExterna::getInstance()->getThOrdenacao($objMdPetVincRepresentantDTO, 'Tipo de Procuração', 'TipoRepresentante', $arrObjMdPetVincRepresentantDTO) . '</div></th>';
    $strResultado .= '<th class="infraTh"><div style="width:150px">' . PaginaSEIExterna::getInstance()->getThOrdenacao($objMdPetVincRepresentantDTO, 'Abrangência', 'StaAbrangencia', $arrObjMdPetVincRepresentantDTO) . '</div></th>';
    $strResultado .= '<th class="infraTh"><div style="width:120px">' . PaginaSEIExterna::getInstance()->getThOrdenacao($objMdPetVincRepresentantDTO, 'Validade', 'DataLimite', $arrObjMdPetVincRepresentantDTO) . '</div></th>';
    $strResultado .= '<th class="infraTh"><div style="width:70px" class="text-center">' . PaginaSEIExterna::getInstance()->getThOrdenacao($objMdPetVincRepresentantDTO, 'Situação', 'StaEstado', $arrObjMdPetVincRepresentantDTO) . '</div></th>';
    $strResultado .= '<th class="infraTh"><div style="width:60px" class="text-center">Ações</div></th>';
    $strResultado .= '</tr>';

    $arrSelectTipoVinculo = array();
    //Populando obj para tabela
    for ($i = 0; $i < $numRegistros; $i++) {
        //Acesso Externo
        $idContato = "";
        $idContato = $arrObjMdPetVincRepresentantDTO[$i]->getNumIdContato();

        $arrSerieSituacao = MdPetVincRepresentantDTO::getArrSerieSituacao(
            MdPetVincRepresentantRN::$RP_ATIVO,
            $arrObjMdPetVincRepresentantDTO[$i]->getStrTipoRepresentante()
        );

        $strLabelSituacao = $arrSerieSituacao['strSituacao'];
        $idSerieFormulario = $arrSerieSituacao['numSerie'];

        if (!in_array($arrObjMdPetVincRepresentantDTO[$i]->getStrTipoRepresentante(), $arrSelectTipoVinculo)) {
            $arrSelectTipoVinculo[$arrObjMdPetVincRepresentantDTO[$i]->getStrTipoRepresentante()] = $arrObjMdPetVincRepresentantDTO[$i]->getStrNomeTipoRepresentante();
        }

        //Buscar documento da procuração
        $objMdPetVincDocumentoRN = new MdPetVincDocumentoRN();
        $objMdPetVincDocumentoDTO = new MdPetVincDocumentoDTO();
        $objMdPetVincDocumentoDTO->setNumIdMdPetVinculoRepresent($arrObjMdPetVincRepresentantDTO[$i]->getNumIdMdPetVinculoRepresent());


        $objMdPetVincDocumentoDTO->retDblIdDocumento();
        $objMdPetVincDocumentoDTO->retStrProtocoloFormatadoProtocolo();
        $objMdPetVincDocumentoDTO->retNumIdSerie();
        $objMdPetVincDocumentoDTO->retNumIdMdPetVinculo();
        $objMdPetVincDocumentoDTO->retStrNomeSerieProtocolo();
        $objMdPetVincDocumentoDTO->retStrTipoDocumento();
        $objMdPetVincDocumentoDTO->setStrTipoDocumento("E");

        $objMdPetVincDocumentoDTO->retNumIdMdPetVinculoRepresent();

        $arrObjMdPetVincDocumentoDTO = $objMdPetVincDocumentoRN->listar($objMdPetVincDocumentoDTO);

        $listaDocumento = "";
        foreach ($arrObjMdPetVincDocumentoDTO as $objMdPetVincDocumentoDTO) {

            if ($objMdPetVincDocumentoDTO->getNumIdMdPetVinculoRepresent() == $arrObjMdPetVincRepresentantDTO[$i]->getNumIdMdPetVinculoRepresent()) {
                $idVinculacao = $objMdPetVincDocumentoDTO->getNumIdMdPetVinculo();

                //$idDocumentoFormatado = $objMdPetVincDocumentoDTO->getStrProtocoloFormatadoProtocolo();
                $idDocumento = $objMdPetVincDocumentoDTO->getDblIdDocumento();
                $listaDocumento = $idDocumento;
                $tipoDocumento = $objMdPetVincDocumentoDTO->getStrTipoDocumento();

            }
        }

        $strResultado .= '<tr class="infraTrClara" id="ID-' . $arrObjMdPetVincRepresentantDTO[$i]->getNumIdMdPetVinculoRepresent() . '">';
        // $strResultado .= '<td valign="top">' . PaginaSEIExterna::getInstance()->getTrCheck($i, $arrObjMdPetVincRepresentantDTO[$i]->getNumIdMdPetVinculoRepresent(), $idDocumento) . '</td>';
        //$strResultado .= '<td>' . $idDocumentoFormatado . '</td>';
        if (InfraUtil::formatarCnpj($arrObjMdPetVincRepresentantDTO[$i]->getStrCNPJ()) == "") {
            //Pegando idContato
            $contatoRN = new ContatoRN();
            $contatoDTO = new ContatoDTO();
            $contatoDTO->setNumIdContato($arrObjMdPetVincRepresentantDTO[$i]->getNumIdContatoOutorg());
            $contatoDTO->retDblCpf();
            $contatoDTO->retDblCnpj();
            $valor = $contatoRN->consultarRN0324($contatoDTO);
            if ($valor->getDblCnpj() == null) {
                $strResultado .= '<td align="center">' . InfraUtil::formatarCpf($valor->getDblCpf()) . '</td>';
            } else {
                $strResultado .= '<td align="center">' . InfraUtil::formatarCnpj($valor->getDblCnpj()) . '</td>';

            }

        } else {
            $strResultado .= '<td align="center">' . InfraUtil::formatarCnpj($arrObjMdPetVincRepresentantDTO[$i]->getStrCNPJ()) . '</td>';
        }
        $strResultado .= '<td>' . PaginaSEI::tratarHTML($arrObjMdPetVincRepresentantDTO[$i]->getStrRazaoSocialNomeVinc()) . '</td>';
        $strResultado .= '<td>' . InfraUtil::formatarCpf($arrObjMdPetVincRepresentantDTO[$i]->getStrCpfProcurador()) . '</td>';
        $strResultado .= '<td>' . $arrObjMdPetVincRepresentantDTO[$i]->getStrNomeProcurador() . '</td>';
        $strResultado .= '<td>' . $arrObjMdPetVincRepresentantDTO[$i]->getStrNomeTipoRepresentante() /*$strTipoRepresentante*/ . '</td>';
        $strResultado .= '<td>' . $arrObjMdPetVincRepresentantDTO[$i]->getStrStaAbrangenciaTipo() . '</td>';
        $strResultado .= '<td>' . $arrObjMdPetVincRepresentantDTO[$i]->getDthDataLimiteValidade() . '</td>';
        $strResultado .= '<td>' . $arrObjMdPetVincRepresentantDTO[$i]->getStrStaEstadoTipo() . '</td>';


        /*if (count($arrObjUsuarioDTO)>0){
            $idContato = $arrObjUsuarioDTO[0]->getNumIdContato();
        }*/
        //Recuperando pelo id do outorgado
        $idProcedimento = $arrObjMdPetVincRepresentantDTO[$i]->getDblIdProcedimentoVinculo();
        //var_dump($idProcedimento,$idContato);die;
        $objMdPetAcessoExternoRN = new MdPetAcessoExternoRN();
        $idAcessoExterno = $objMdPetAcessoExternoRN->_getUltimaConcessaoAcessoExternoModulo($idProcedimento, $idContato, true);
        //Acesso Externo - fim

        if ($idAcessoExterno != '' and $listaDocumento != '') {
            SessaoSEIExterna::getInstance()->configurarAcessoExterno($idAcessoExterno);
            $strLinkConsultaDocumento = SessaoSEIExterna::getInstance()->assinarLink('documento_consulta_externa.php?id_acesso_externo=' . $idAcessoExterno . '&id_documento=' . $listaDocumento);

            $iconeConsulta = 'Consultar Procuração';

            $iconeConsulta = '<img style="width:24px;"  src="modulos/peticionamento/imagens/svg/visualizar_procuracao_especial.svg?'.Icone::VERSAO.'" title="' . $iconeConsulta . '" alt="' . $iconeConsulta . '" class="infraImg" />';

            $acaoConsulta = '<a target="_blank" href="' . $strLinkConsultaDocumento . '">' . $iconeConsulta . '</a>';
            SessaoSEIExterna::getInstance()->configurarAcessoExterno(null);
        }

        $iconeAcao = '';

        if ($arrObjMdPetVincRepresentantDTO[$i]->getStrStaEstado() == MdPetVincRepresentantRN::$RP_ATIVO) {
            if ($arrObjMdPetVincRepresentantDTO[$i]->getNumIdContato() == $idContatoExterno) {
                $iconeAcao = '<a href="' . SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=processo_eletronico_responder_motivo_renunciar&id_contato_vinc=' . $arrObjMdPetVincRepresentantDTO[$i]->getNumIdContatoVinc() . '&tpDocumento=renunciar&tpProc=' . $arrObjMdPetVincRepresentantDTO[$i]->getStrTipoRepresentante() . '&tpVinculo=' . $arrObjMdPetVincRepresentantDTO[$i]->getStrTpVinc() . '&id_procedimento=' . $arrObjMdPetVincRepresentantDTO[$i]->getDblIdProcedimentoVinculo() . '&id_documento=' . $listaDocumento . '&cpf=' . $arrObjMdPetVincRepresentantDTO[$i]->getStrCpfProcurador() . '&id_vinculacao=' . $arrObjMdPetVincRepresentantDTO[$i]->getNumIdMdPetVinculoRepresent()) .'"><img style="width:24px;"  src="modulos/peticionamento/imagens/svg/renunciar_procuracao.svg?'.Icone::VERSAO.'" title="Renunciar Procuração" alt="Renunciar Procuração" class="infraImg" /></a>';
            } else if ($staEstadoRepresentantSuspenso != MdPetVincRepresentantRN::$RP_SUSPENSO || $arrObjMdPetVincRepresentantDTO[$i]->getStrStaEstado() != MdPetVincRepresentantRN::$RP_REVOGADA || $arrObjMdPetVincRepresentantDTO[$i]->getStrStaEstado() != MdPetVincRepresentantRN::$RP_RENUNCIADA) {
                $iconeAcao = '<a href="' . SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=processo_eletronico_responder_motivo_revogar&id_contato_vinc=' . $arrObjMdPetVincRepresentantDTO[$i]->getNumIdContatoVinc() . '&tpDocumento=revogar&tpProc=' . $arrObjMdPetVincRepresentantDTO[$i]->getStrTipoRepresentante() . '&tpVinculo=' . $arrObjMdPetVincRepresentantDTO[$i]->getStrTpVinc() . '&id_procedimento=' . $arrObjMdPetVincRepresentantDTO[$i]->getDblIdProcedimentoVinculo() . '&id_documento=' . $listaDocumento . '&cpf=' . $arrObjMdPetVincRepresentantDTO[$i]->getStrCpfProcurador() . '&id_vinculacao=' . $arrObjMdPetVincRepresentantDTO[$i]->getNumIdMdPetVinculoRepresent()) . '"><img style="width:24px;"  src="modulos/peticionamento/imagens/svg/revogar_renunciar_procuracao.svg?'.Icone::VERSAO.'" title="Revogar Procuração" alt="Revogar Procuração" class="infraImg" /></a>';
            }
        }

        $strResultado .= '<td align="center">' . $acaoConsulta . $iconeAcao . '</td>';
        $strResultado .= '</tr>';
    }
    $strResultado .= '</table>';
}

//Responsável Legal
$objMdPetVincRepresentantRN = new MdPetVincRepresentantRN();
$objMdPetVincRepresentantDTO = new MdPetVincRepresentantDTO();

$objMdPetVincRepresentantDTO->retNumIdMdPetVinculo();
$objMdPetVincRepresentantDTO->setStrTipoRepresentante(MdPetVincRepresentantRN::$PE_RESPONSAVEL_LEGAL);
$objMdPetVincRepresentantDTO->setNumIdContato($idContatoExterno);
$objMdPetVincRepresentantDTO->setStrSinAtivo('S');
$objMdPetVincRepresentantDTO->setStrStaEstado(MdPetVincRepresentantRN::$RP_ATIVO);

$arrObjMdPetVincRepresentantDTO = $objMdPetVincRepresentantRN->listar($objMdPetVincRepresentantDTO);
$bolAcaoCadastrar = false;

if (!empty($arrObjMdPetVincRepresentantDTO)) {
    $bolAcaoCadastrar = true;
}

$mdPetVincTpProcessoRN = new MdPetVincTpProcessoRN();
$objMdPetVincTpProcessoDTO = new MdPetVincTpProcessoDTO();
$objMdPetVincTpProcessoDTO->retTodos();
$arrObjMdPetVincTpProcessoDTO = $mdPetVincTpProcessoRN->listar($objMdPetVincTpProcessoDTO);

$flagMostrarBotao = false;

foreach ($arrObjMdPetVincTpProcessoDTO as $objMdPetVincTpProcessoDTO) {
    if ($objMdPetVincTpProcessoDTO->getStrSinAtivo() == 'S') {
        $flagMostrarBotao = true;
    }
}

$arrComandos = array();
$arrComandos[] = '<button type="submit" accesskey="p" id="btnPesquisar" onclick="validarCampoData();" value="Pesquisar" class="infraButton"><span class="infraTeclaAtalho">P</span>esquisar</button>';
if ($flagMostrarBotao) {
    $arrComandos[] = '<button type="button" accesskey="N" id="btnNova" value="Nova Procuração Eletrônica" onclick="location.href=\'' . SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=md_pet_vinc_usu_ext_pe_cadastrar&acao_origem=' . $_GET['acao']) . '\';" class="infraButton"><span class="infraTeclaAtalho">N</span>ova Procuração Eletrônica</button>';
}
$arrComandos[] = '<button type="button" accesskey="c" name="btnFechar" onclick="location.href=\'' . PaginaSEIExterna::getInstance()->formatarXHTML(SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=usuario_externo_controle_acessos&id_orgao_acesso_externo=0')) . '\';" class="infraButton" >Fe<span class="infraTeclaAtalho">c</span>har</button>';

PaginaSEIExterna::getInstance()->montarDocType();
PaginaSEIExterna::getInstance()->abrirHtml();
PaginaSEIExterna::getInstance()->abrirHead();
PaginaSEIExterna::getInstance()->montarMeta();
PaginaSEIExterna::getInstance()->montarTitle(
    PaginaSEIExterna::getInstance()->getStrNomeSistema() . ' - ' . $strTitulo
);
PaginaSEIExterna::getInstance()->montarStyle();
PaginaSEIExterna::getInstance()->abrirStyle();
require_once 'md_pet_vinc_usu_ext_lista_css.php';
PaginaSEIExterna::getInstance()->fecharStyle();
PaginaSEIExterna::getInstance()->montarJavaScript();
PaginaSEIExterna::getInstance()->abrirJavaScript();
PaginaSEIExterna::getInstance()->fecharJavaScript();
PaginaSEIExterna::getInstance()->fecharHead();
PaginaSEIExterna::getInstance()->abrirBody($strTitulo, 'onload="inicializar();"');
?>


<form id="frmPesquisa" method="post"
      action="<?= SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=' . $_GET['acao'] . '&acao_origem=' . $_GET['acao']) ?>">
    <?
    PaginaSEIExterna::getInstance()->montarBarraComandosSuperior($arrComandos);
    PaginaSEIExterna::getInstance()->abrirAreaDados('auto');
    ?>
    <!-- div class="container" -->
    <div class="row">
        <div class="col-sm-12 col-md-4 col-lg-3 col-xl-3">
            <div class="form-group">
                <label id="lblTxtCnpj"
                    for="txtCnpj"
                    class="infraLabelOpcional">CPF/CNPJ Outorgante:</label>
                <input type="text"
                    id="txtCnpj"
                    name="txtCnpj"
                    class="infraText form-control"
                    value="<?= PaginaSEIExterna::tratarHTML($strCnpj) ?>"
                    maxlength="18"
                    tabindex="<?= PaginaSEIExterna::getInstance()->getProxTabDados() ?>"
                    onkeypress="return controlarCpfCnpj(this);"
                    onkeyup="return controlarCpfCnpj(this);"
                    onkeydown="return controlarCpfCnpj(this);"
                    onchange="validaCpfCnpjOutorgante(this)"/>
            </div>
        </div>
        <div class="col-sm-12 col-md-8 col-lg-6 col-xl-4">
            <div class="form-group">
                <label id="lblRazaoSocial"
                    for="txtRazaoSocial"
                    class="infraLabelOpcional">Nome/Razão Social do Outorgante:</label>
                <input type="text"
                    id="txtRazaoSocial"
                    name="txtRazaoSocial"
                    class="infraText form-control"
                    value="<?= PaginaSEIExterna::tratarHTML($strRazaoSocial) ?>"
                    maxlength="100"
                    tabindex="<?= PaginaSEIExterna::getInstance()->getProxTabDados() ?>"/>
            </div>
        </div>
        <div class="col-sm-12 col-md-3 col-lg-3 col-xl-2">
            <div class="form-group">
                <label id="lblCpf"
                    for="txtCpf"
                    class="infraLabelOpcional">CPF Outorgado:</label>
                <input type="text"
                    id="txtCpf"
                    name="txtCpf"
                    class="infraText form-control"
                    value="<?= PaginaSEIExterna::tratarHTML($strCpf) ?>"
                    maxlength="14"
                    tabindex="<?= PaginaSEIExterna::getInstance()->getProxTabDados() ?>"
                    onkeypress="return infraMascaraCPFProcurador(this);"
                    onchange="validaCpfProcurador(this)"/>
            </div>
        </div>
        <div class="col-sm-12 col-md-6 col-lg-6 col-xl-3">
            <div class="form-group">
                <label id="lblNomeProcurador"
                    for="txtNomeProcurador"
                    class="infraLabelOpcional">Nome do Outorgado:</label>
                <input type="text"
                    id="txtNomeProcurador"
                    name="txtNomeProcurador"
                    class="infraText form-control"
                    value="<?= PaginaSEIExterna::tratarHTML($strNome) ?>"
                    maxlength="100"
                    tabindex="<?= PaginaSEIExterna::getInstance()->getProxTabDados() ?>"/>
            </div>
        </div>
        <div class="col-sm-12 col-md-3 col-lg-3 col-xl-2">
            <div class="form-group">
                <label id="lblSituacao"
                    for="slSituacao"
                    class="infraLabelOpcional">Situação:</label>
                <select name="slSituacao" id="slSituacao" class="infraSelect form-control"
                        tabindex="<?= PaginaSEIExterna::getInstance()->getProxTabDados() ?>">
                    <option value=""></option>
                    <?php
                    if (!is_null($strResultado)) :
                        ?>
                        <option value="<?php echo MdPetVincRepresentantRN::$RP_ATIVO ?>"
                            <?php if (MdPetVincRepresentantRN::$RP_ATIVO == $strSituacao) { ?>
                                selected="selected"
                            <?php } ?>>
                            Ativa
                        </option>
                        <option value="<?php echo MdPetVincRepresentantRN::$RP_SUSPENSO ?>"
                            <?php if (MdPetVincRepresentantRN::$RP_SUSPENSO == $strSituacao) { ?>
                                selected="selected"
                            <?php } ?>>
                            Suspensa
                        </option>
                        <option value="<?php echo MdPetVincRepresentantRN::$RP_REVOGADA ?>"
                            <?php if (MdPetVincRepresentantRN::$RP_REVOGADA == $strSituacao) { ?>
                                selected="selected"
                            <?php } ?>>
                            Revogada
                        </option>
                        <option value="<?php echo MdPetVincRepresentantRN::$RP_RENUNCIADA ?>"
                            <?php if (MdPetVincRepresentantRN::$RP_RENUNCIADA == $strSituacao) { ?>
                                selected="selected"
                            <?php } ?>>
                            Renunciada
                        </option>
                        <option value="<?php echo MdPetVincRepresentantRN::$RP_VENCIDA ?>"
                            <?php if (MdPetVincRepresentantRN::$RP_VENCIDA == $strSituacao) { ?>
                                selected="selected"
                            <?php } ?>>
                            Vencida
                        </option>
                    <?php endif; ?>
                </select>
            </div>
        </div>
        <div class="col-sm-12 col-md-3 col-lg-3 col-xl-3">
            <div class="form-group">
                <label id="lblTipoVinculo"
                    for="slTipoVinculo"
                    class="infraLabelOpcional">Tipo de Procuração:</label>
                <select name="slTipoProcuracao" id="slTipoViculo" class="infraSelect form-control"
                        tabindex="<?= PaginaSEIExterna::getInstance()->getProxTabDados() ?>">
                    <option value=""></option>
                    <?php if ($arrSelectTipoVinculo) : ?>
                        <?php foreach ($arrSelectTipoVinculo as $chaveTipoVinculo => $itemTipoVinculo) : ?>
                            <option value="<?php echo $chaveTipoVinculo; ?>"
                                <?php if ($chaveTipoVinculo == $strTipoVinculo) { ?>
                                    selected="selected"
                                <?php } ?>>
                                <?php echo $itemTipoVinculo; ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
        </div>
        <div class="col-sm-12 col-md-6 col-lg-7 col-xl-4">
            <div class="form-group">
                <label id="lblAbrangencia"
                    for="lblAbrangencia"
                    class="infraLabelOpcional">Abrangência:</label>
                <br>
                <select name="sllblAbrangencia" id="sllblAbrangencia" class="infraSelect form-control"
                        tabindex="<?= PaginaSEIExterna::getInstance()->getProxTabDados() ?>">
                    <option value=""></option>
                    <option value="<?php echo MdPetVincRepresentantRN::$PR_QUALQUER ?>"
                        <?php if (MdPetVincRepresentantRN::$PR_QUALQUER == $strAbrangencia) { ?>
                            selected="selected"
                        <?php } ?>>
                        Qualquer Processo em Nome do Outorgante
                    </option>
                    <option value="<?php echo MdPetVincRepresentantRN::$PR_ESPECIFICO ?>"
                        <?php if (MdPetVincRepresentantRN::$PR_ESPECIFICO == $strAbrangencia) { ?>
                            selected="selected"
                        <?php } ?>>
                        Processos Específicos
                    </option>
                </select>
            </div>
        </div>
        <div class="col-sm-12 col-md-3 col-lg-5 col-xl-3">
            <div class="form-group">
                <label id="lblValidade"
                    for="lblValidade"
                    class="infraLabelOpcional">Validade:</label>
                <select name="sllblValidade" onchange="showData(this);" class="infraSelect  form-control" id="sllblValidade"
                        tabindex="<?= PaginaSEIExterna::getInstance()->getProxTabDados() ?>">

                    <option value=""></option>
                    <option value="null"
                        <?php if ("null" == $strValidade) { ?>
                            selected="selected"
                        <?php } ?>>
                        Indeterminado
                    </option>

                    <option value="D" <?php if ($strValidade == "D") { ?>
                        selected="selected" <?php } ?>>
                        Determinado
                    </option>

                </select>
            </div>
        </div>
        <div class="col-6 col-sm-4 col-md-3 col-lg-2 col-xl-2">
            <div id="dtInicio">
                <div class="form-group">
                    <label id="lblPeriodoInicio"
                        for="txtPeriodoInicio"
                    >Data Inicio:</label>
                    <div class="input-group mb-3">
                        <input type="text"
                            id="txtPeriodoInicio"
                            name="txtPeriodoInicio"
                            class="infraText form-control"
                            value=""
                            onkeypress="return infraMascaraData(this, event);"
                            maxlength="100"
                            tabindex="<?= PaginaSEIExterna::getInstance()->getProxTabDados() ?>"/>

                        <img src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal(); ?>/calendario.svg" id="imgDt"
                            title="Selecionar Data"
                            alt="Selecionar Data" class="infraImg"
                            onclick="infraCalendario('txtPeriodoInicio',this,false,'<?= InfraData::getStrDataAtual() . ' 00:00' ?>');"/>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-sm-4 col-md-3 col-lg-2 col-xl-2">
            <div id="dtFim">
                <div class="form-group">
                    <label id="lblPeriodoFim"
                        for="txtPeriodoFim"
                    >Data Fim: </label>
                    <div class="input-group mb-3">
                        <input type="text"
                            id="txtPeriodoFim"
                            name="txtPeriodoFim"
                            class="infraText"
                            value=""
                            onkeypress="return infraMascaraData(this, event);"
                            maxlength="100"
                            tabindex="<?= PaginaSEIExterna::getInstance()->getProxTabDados() ?>"/>
                        <img src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/calendario.svg" id="imgDt"
                            title="Selecionar Data"
                            alt="Selecionar Data" class="infraImg"
                            onclick="infraCalendario('txtPeriodoFim',this,false,'<?= InfraData::getStrDataAtual() . ' 00:00' ?>');"/>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Periodo Fim - Fim  -->
    <input type="hidden" name="hdnDataInicio" id="hdnDtInicio" value="<?php echo $dataInicio; ?>">
    <input type="hidden" name="hdnDataFim" id="hdnDtFim" value="<?php echo $dataFim; ?>">

    <!-- /div -->
    <?
    PaginaSEIExterna::getInstance()->fecharAreaDados();
    PaginaSEIExterna::getInstance()->montarAreaTabela($strResultado, $numRegistros);
    ?>
</form>
<?
PaginaSEIExterna::getInstance()->fecharBody();
PaginaSEIExterna::getInstance()->fecharHtml();
require_once 'md_pet_vinc_usu_ext_lista_js.php';
?>
