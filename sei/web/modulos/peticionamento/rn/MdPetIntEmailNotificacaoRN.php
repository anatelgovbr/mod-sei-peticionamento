<?php

/**
 * ANATEL
 *
 * 30/03/2017 - criado por jaqueline.mendes - CAST
 *
 */

require_once dirname(__FILE__) . '/../../../SEI.php';

class MdPetIntEmailNotificacaoRN extends InfraRN
{

    public static $EMAIL_INTIMACAO_CADASTRO = 'MD_PET_CADASTRO_INTIMACAO';

    public function __construct()
    {
        /**
         * Retirada do m�todo nativo que estava entrando em conflito com o AgendamentoRN :: otimizarIndicesSolr
         */
//		session_start();
        //////////////////////////////////////////////////////////////////////////////
        InfraDebug::getInstance()->setBolLigado(true);
        InfraDebug::getInstance()->setBolDebugInfra(true);
        InfraDebug::getInstance()->limpar();
        //////////////////////////////////////////////////////////////////////////////

        parent::__construct();
    }

    protected function inicializarObjInfraIBanco()
    {
        return BancoSEI::getInstance();
    }

    public function notificaCadastroIntimacaoConectado($arrParams)
    {
        $objEmailSistemaRN = new EmailSistemaRN();


    }

    protected function enviarEmailIntimacaoConectado($dadosIntimacao)
    {
        $arrDadosEmail = array();

        $arrDadosEmail['dadosUsuario']['nome'] = $dadosIntimacao['nome'];
        $arrDadosEmail['dadosUsuario']['email'] = $dadosIntimacao['email'];
        $arrDadosEmail['dadosUsuario']['dataHora'] = $dadosIntimacao['dataHora'];
        $arrDadosEmail['dadosUsuario']['processo'] = $dadosIntimacao['processo'];

        //Busca dados Montar Email.
        //Dados Documento
        $objDocumentoDTO = new DocumentoDTO();
        $objDocumentoDTO->retDblIdDocumento();
        $objDocumentoDTO->retNumIdOrgaoUnidadeResponsavel();
        $objDocumentoDTO->retStrProtocoloDocumentoFormatado();
        $objDocumentoDTO->retStrNomeSerie();
        $objDocumentoDTO->retStrNumero();
        $objDocumentoDTO->retNumIdSerie();
        $objDocumentoDTO->setDblIdDocumento($dadosIntimacao['POST']['hdnIdDocumento']);
        $objDocumentoRN = new DocumentoRN();
        $objDocumentoDTO = $objDocumentoRN->consultarRN0005($objDocumentoDTO);
        $strProtocoloDocumentoFormatado = $objDocumentoDTO->getStrProtocoloDocumentoFormatado();
        $arrDadosEmail['objDocumentoDTO'] = $objDocumentoDTO;

        //Dados Unidade
        $objUnidadeDTO = new UnidadeDTO();
        $objUnidadeDTO->retStrSigla();
        $objUnidadeDTO->retStrDescricao();
        $objUnidadeDTO->retStrSiglaOrgao();
        $objUnidadeDTO->retStrDescricaoOrgao();
        $objUnidadeDTO->retStrSitioInternetOrgaoContato();
        $objUnidadeDTO->setBolExclusaoLogica(false);
        $objUnidadeDTO->setNumIdUnidade(SessaoSEI::getInstance()->getNumIdUnidadeAtual());

        $objUnidadeRN = new UnidadeRN();
        $objUnidadeDTO = $objUnidadeRN->consultarRN0125($objUnidadeDTO);

        $arrDadosEmail['objUnidadeDTO'] = $objUnidadeDTO;

        //Dados Tipo Intimacao
        $objMdPetIntTipoIntimacaoDTO = new MdPetIntTipoIntimacaoDTO();
        $objMdPetIntTipoIntimacaoDTO->retTodos();
        $objMdPetIntTipoIntimacaoDTO->setNumIdMdPetIntTipoIntimacao($dadosIntimacao['POST']['selTipoIntimacao']);

        $objMdPetIntTipoIntimacaoRN = new MdPetIntTipoIntimacaoRN();
        $objMdPetIntTipoIntimacaoDTO = $objMdPetIntTipoIntimacaoRN->consultar($objMdPetIntTipoIntimacaoDTO);

        $arrDadosEmail['objMdPetIntTipoIntimacaoDTO'] = $objMdPetIntTipoIntimacaoDTO;

        $objMdPetIntPrazoTacitaDTO = new MdPetIntPrazoTacitaDTO();
        $objMdPetIntPrazoTacitaDTO->retTodos();
        $objMdPetIntPrazoTacitaRN = new MdPetIntPrazoTacitaRN();
        $objMdPetIntPrazoTacitaDTO = $objMdPetIntPrazoTacitaRN->consultar($objMdPetIntPrazoTacitaDTO);


        //PrazoTacita
        $arrDadosEmail['prazoTacita'] = $objMdPetIntPrazoTacitaDTO->getNumNumPrazo();
        $objMdPetIntPrazoRN = new MdPetIntPrazoRN();
        $arrDadosEmail['dataFinalPrazoTacita'] = $objMdPetIntPrazoRN->calcularDataPrazo($arrDadosEmail['prazoTacita']);

        if ($objMdPetIntTipoIntimacaoDTO->getStrTipoRespostaAceita() == MdPetIntTipoIntimacaoRN::$FACULTATIVA) {
            $this->emailRespostasFacultativas($arrDadosEmail);
        } else if ($objMdPetIntTipoIntimacaoDTO->getStrTipoRespostaAceita() == MdPetIntTipoIntimacaoRN::$EXIGE_RESPOSTA) {
            $objMdPetIntTipoRespDTO = new MdPetIntTipoRespDTO();
            $objMdPetIntTipoRespDTO->retTodos();
            $objMdPetIntTipoRespDTO->setNumIdMdPetIntTipoResp($dadosIntimacao['POST']['selTipoResposta'][0]);
            $objMdPetIntTipoRespRN = new MdPetIntTipoRespRN();
            $objMdPetIntTipoRespDTO = $objMdPetIntTipoRespRN->consultar($objMdPetIntTipoRespDTO);

            $arrDadosEmail['objMdPetIntTipoRespDTO'] = $objMdPetIntTipoRespDTO;

            $this->emailExigeResposta($arrDadosEmail);
        } else if ($objMdPetIntTipoIntimacaoDTO->getStrTipoRespostaAceita() == MdPetIntTipoIntimacaoRN::$SEM_RESPOSTA) {
            $this->emailSemResposta($arrDadosEmail);
        }

        return true;
    }


    //Juridico
    protected function enviarEmailIntimacaoJuridicoConectado($dadosIntimacao)
    {
        $arrDadosEmail = array();

        $arrDadosEmail['dadosUsuario']['nome'] = $dadosIntimacao['nome'];
        $arrDadosEmail['dadosUsuario']['email'] = $dadosIntimacao['email'];
        $arrDadosEmail['dadosUsuario']['dataHora'] = $dadosIntimacao['dataHora'];
        $arrDadosEmail['dadosUsuario']['cnpj'] = $dadosIntimacao['cnpj'];
        $arrDadosEmail['dadosUsuario']['razaoSocial'] = $dadosIntimacao['razaoSocial'];
        $arrDadosEmail['dadosUsuario']['tpVinc'] = $dadosIntimacao['tpVinc'];
        $arrDadosEmail['dadosUsuario']['processo'] = $dadosIntimacao['processo'];

        //Busca dados Montar Email.
        //Dados Documento
        $objDocumentoDTO = new DocumentoDTO();
        $objDocumentoDTO->retDblIdDocumento();
        $objDocumentoDTO->retNumIdOrgaoUnidadeResponsavel();
        $objDocumentoDTO->retStrProtocoloDocumentoFormatado();
        $objDocumentoDTO->retStrNomeSerie();
        $objDocumentoDTO->retStrNumero();
        $objDocumentoDTO->retNumIdSerie();
        $objDocumentoDTO->setDblIdDocumento($dadosIntimacao['POST']['hdnIdDocumento']);
        $objDocumentoRN = new DocumentoRN();
        $objDocumentoDTO = $objDocumentoRN->consultarRN0005($objDocumentoDTO);
        $strProtocoloDocumentoFormatado = $objDocumentoDTO->getStrProtocoloDocumentoFormatado();
        $arrDadosEmail['objDocumentoDTO'] = $objDocumentoDTO;

        //Dados Unidade
        $objUnidadeDTO = new UnidadeDTO();
        $objUnidadeDTO->retStrSigla();
        $objUnidadeDTO->retStrDescricao();
        $objUnidadeDTO->retStrSiglaOrgao();
        $objUnidadeDTO->retStrDescricaoOrgao();
        $objUnidadeDTO->retStrSitioInternetOrgaoContato();
        $objUnidadeDTO->setBolExclusaoLogica(false);
        $objUnidadeDTO->setNumIdUnidade(SessaoSEI::getInstance()->getNumIdUnidadeAtual());

        $objUnidadeRN = new UnidadeRN();
        $objUnidadeDTO = $objUnidadeRN->consultarRN0125($objUnidadeDTO);

        $arrDadosEmail['objUnidadeDTO'] = $objUnidadeDTO;

        //Dados Tipo Intimacao
        $objMdPetIntTipoIntimacaoDTO = new MdPetIntTipoIntimacaoDTO();
        $objMdPetIntTipoIntimacaoDTO->retTodos();
        $objMdPetIntTipoIntimacaoDTO->setNumIdMdPetIntTipoIntimacao($dadosIntimacao['POST']['selTipoIntimacao']);

        $objMdPetIntTipoIntimacaoRN = new MdPetIntTipoIntimacaoRN();
        $objMdPetIntTipoIntimacaoDTO = $objMdPetIntTipoIntimacaoRN->consultar($objMdPetIntTipoIntimacaoDTO);

        $arrDadosEmail['objMdPetIntTipoIntimacaoDTO'] = $objMdPetIntTipoIntimacaoDTO;

        $objMdPetIntPrazoTacitaDTO = new MdPetIntPrazoTacitaDTO();
        $objMdPetIntPrazoTacitaDTO->retTodos();
        $objMdPetIntPrazoTacitaRN = new MdPetIntPrazoTacitaRN();
        $objMdPetIntPrazoTacitaDTO = $objMdPetIntPrazoTacitaRN->consultar($objMdPetIntPrazoTacitaDTO);


        //PrazoTacita
        $arrDadosEmail['prazoTacita'] = $objMdPetIntPrazoTacitaDTO->getNumNumPrazo();
        $objMdPetIntPrazoRN = new MdPetIntPrazoRN();
        $arrDadosEmail['dataFinalPrazoTacita'] = $objMdPetIntPrazoRN->calcularDataPrazo($arrDadosEmail['prazoTacita']);

        if ($objMdPetIntTipoIntimacaoDTO->getStrTipoRespostaAceita() == MdPetIntTipoIntimacaoRN::$FACULTATIVA) {
            $this->emailRespostasFacultativasJuridico($arrDadosEmail);
        } else if ($objMdPetIntTipoIntimacaoDTO->getStrTipoRespostaAceita() == MdPetIntTipoIntimacaoRN::$EXIGE_RESPOSTA) {
            $objMdPetIntTipoRespDTO = new MdPetIntTipoRespDTO();
            $objMdPetIntTipoRespDTO->retTodos();
            $objMdPetIntTipoRespDTO->setNumIdMdPetIntTipoResp($dadosIntimacao['POST']['selTipoResposta'][0]);
            $objMdPetIntTipoRespRN = new MdPetIntTipoRespRN();
            $objMdPetIntTipoRespDTO = $objMdPetIntTipoRespRN->consultar($objMdPetIntTipoRespDTO);

            $arrDadosEmail['objMdPetIntTipoRespDTO'] = $objMdPetIntTipoRespDTO;

            $this->emailExigeRespostaJuridico($arrDadosEmail);
        } else if ($objMdPetIntTipoIntimacaoDTO->getStrTipoRespostaAceita() == MdPetIntTipoIntimacaoRN::$SEM_RESPOSTA) {
            $this->emailSemRespostaJuridico($arrDadosEmail);
        }

        return true;
    }

    protected function enviarEmailReiteracaoIntimacaoConectado($params)
    {

        try {
            $arrObjMdPetIntRelDestinatarioDTO = $params[0];
            $arrDadosEmailNaoEnviados = array();
            $qtdNaoEnviadas = 0;
            $qtdEnviadas = 0;
            $arrDadosEmail = array();

            //Usu�rio do M�dulo de Peticionamento
            $objUsuarioPetRN = new MdPetIntUsuarioRN();
            $idUsuarioPet = $objUsuarioPetRN->getObjUsuarioPeticionamento(true);

            ////////// CAMPOS EM COMUM
            $arrDadosEmail['sigla_sistema'] = ConfiguracaoSEI::getInstance()->getValor('SessaoSEI', 'SiglaSistema');

            $objInfraParametro = new InfraParametro(BancoSEI::getInstance());
            $arrDadosEmail['email_sistema'] = $objInfraParametro->getValor('SEI_EMAIL_SISTEMA');

            $arrDadosEmail['link_login_usuario_externo'] = ConfiguracaoSEI::getInstance()->getValor('SEI', 'URL') . '/controlador_externo.php?acao=usuario_externo_logar&id_orgao_acesso_externo=0';
            ////////// CAMPOS EM COMUM - fim

            if ($arrObjMdPetIntRelDestinatarioDTO) {
                foreach ($arrObjMdPetIntRelDestinatarioDTO as $destinatario) {
                    $arrDadosEmail['tipo_resposta'] = '';
                    $isReiteracao = false;

                    $idIntimacao = $destinatario->getNumIdMdPetIntimacao();
                    $idMdPetIntRelDestinatario = $destinatario->getNumIdMdPetIntRelDestinatario();
                    $idContato = $destinatario->getNumIdContato();

                    $dtIntimacaoAceite = !is_null($destinatario->getDthDataAceite()) ? explode(' ', $destinatario->getDthDataAceite()) : null;
                    $arrDadosEmail['data_cumprimento_intimacao'] = is_array($dtIntimacaoAceite) ? $dtIntimacaoAceite[0] : null;

                    $objMdPetIntPrazoRN = new MdPetIntPrazoRN();
                    $arrObjMdPetIntPrazoDTO = $objMdPetIntPrazoRN->retornarTipoRespostaValido(array($idIntimacao, $idMdPetIntRelDestinatario));

                    //Existe algum Tipo de Resposta que ainda possui prazo?
                    if ($arrObjMdPetIntPrazoDTO) {

                        $objMdPetIntPrazoRN = new MdPetIntPrazoRN();
                        $arrObjMdPetIntPrazoDTO = $objMdPetIntPrazoRN->retornarTipoRespostaValido(array($idIntimacao, $idMdPetIntRelDestinatario, true, false));

                        if (count($arrObjMdPetIntPrazoDTO) > 0) {

                            $tipoRespostaDTO = $arrObjMdPetIntPrazoDTO[0];

                            //Data prazo de 1 ou 5 dias
                            $dataAtual = InfraData::getStrDataAtual();

                            $dataFinal = $tipoRespostaDTO->getDthDataProrrogada();
                            if (empty($dataFinal)) {
                                $dataFinal = $tipoRespostaDTO->getDthDataLimite();
                            }
                            if (is_array(explode(" ", $dataFinal))) {
                                $dataFinal = explode(" ", $dataFinal);
                                $dataFinal = $dataFinal[0];
                            }


                            if (InfraData::compararDatas($dataFinal, $dataAtual) == -1 || InfraData::compararDatas($dataFinal, $dataAtual) == -5) {
                                $isReiteracao = true;
                                $arrDadosEmail['tipo_resposta'] = $tipoRespostaDTO->getStrNome();
                                $arrDadosEmail['prazo_externo_tipo_resposta'] = $tipoRespostaDTO->getNumValorPrazoExterno();
                                $arrDadosEmail['tipo_prazo_externo_tipo_resposta'] = $tipoRespostaDTO->getStrTipoPrazoExterno();
                            }
                        }

                        if ($isReiteracao) {
                            //Dados Unidade
                            $objMdPetIntimacaoRN = new MdPetIntimacaoRN();
                            $objUnidadeIntimacaoDTO = $objMdPetIntimacaoRN->getUnidadeIntimacao(array($idIntimacao));

                            if ($objUnidadeIntimacaoDTO) {
                                if (is_numeric($objUnidadeIntimacaoDTO->getNumIdUnidade())) {
                                    $objUnidadeDTO = new UnidadeDTO();
                                    $objUnidadeDTO->retStrSigla();
                                    $objUnidadeDTO->retStrDescricao();
                                    $objUnidadeDTO->retStrSiglaOrgao();
                                    $objUnidadeDTO->retStrDescricaoOrgao();
                                    $objUnidadeDTO->retStrSitioInternetOrgaoContato();
                                    $objUnidadeDTO->setBolExclusaoLogica(false);
                                    $objUnidadeDTO->setNumIdUnidade($objUnidadeIntimacaoDTO->getNumIdUnidade());

                                    $objUnidadeRN = new UnidadeRN();
                                    $objUnidadeDTO = $objUnidadeRN->consultarRN0125($objUnidadeDTO);

                                    if ($objUnidadeIntimacaoDTO) {
                                        $arrDadosEmail['sigla_orgao'] = $objUnidadeDTO->getStrSiglaOrgao();
                                        $arrDadosEmail['descricao_orgao'] = $objUnidadeDTO->getStrDescricaoOrgao();
                                        $arrDadosEmail['sitio_internet_orgao'] = $objUnidadeDTO->getStrSitioInternetOrgaoContato();
                                    }

                                }
                            }

                            //contato
                            $objUsuarioDTO = new UsuarioDTO();
                            $objUsuarioDTO->retNumIdUsuario();
                            $objUsuarioDTO->retStrSigla();
                            $objUsuarioDTO->retStrNome();
                            $objUsuarioDTO->retStrStaTipo();
                            $objUsuarioDTO->retNumIdContato();
                            $objUsuarioDTO->setNumIdContato($idContato);

                            $objUsuarioRN = new UsuarioRN();
                            $objUsuarioDTO = $objUsuarioRN->consultarRN0489($objUsuarioDTO);

                            if (is_object($objUsuarioDTO)) {
                                if ($objUsuarioDTO->getStrStaTipo() == UsuarioRN::$TU_EXTERNO_PENDENTE) {
//                                $objInfraException->lancarValidacao('Usu�rio externo "' . $objUsuarioDTO->getStrSigla() . '" ainda n�o foi liberado.');
                                }

                                if ($objUsuarioDTO->getStrStaTipo() != UsuarioRN::$TU_EXTERNO) {
//                                $objInfraException->lancarValidacao('Usu�rio "' . $objUsuarioDTO->getStrSigla() . '" n�o � um usu�rio externo.');
                                }
                                //contato - fim
                                $enviaEmail = true;
                                $conta = "^[a-zA-Z0-9\._-]+@";
                                $domino = "[a-zA-Z0-9\._-]+.";
                                $extensao = "([a-zA-Z]{2,4})$";
                                $pattern = "#" . $conta . $domino . $extensao . "#";
                                $isEmail = preg_match($pattern, $objUsuarioDTO->getStrSigla());
                                if (!$isEmail) {
                                    $enviaEmail = false;
                                }
                                $arrDadosEmail['email_usuario_externo'] = $objUsuarioDTO->getStrSigla();
                                $arrDadosEmail['nome_usuario_externo'] = $objUsuarioDTO->getStrNome();

                                $arrDadosEmail['tipo_intimacao'] = $destinatario->getStrNomeTipoIntimacao();

                                //Get Prazo T�cito
                                $objPrazoTacitoDTO = new MdPetIntPrazoTacitaDTO();
                                $objPrazoTacitoDTO->retNumNumPrazo();
                                $objPrazoTacitoRN = new MdPetIntPrazoTacitaRN();
                                $retLista = $objPrazoTacitoRN->listar($objPrazoTacitoDTO);
                                $objPrazoTacitoDTO = !is_null($retLista) && count($retLista) > 0 ? current($retLista) : null;
                                $arrDadosEmail['prazo_intimacao_tacita'] = !is_null($objPrazoTacitoDTO) ? $objPrazoTacitoDTO->getNumNumPrazo() : null;

                                $dtIntimacao = !is_null($destinatario->getDthDataCadastro()) ? explode(' ', $destinatario->getDthDataCadastro()) : null;

                                //Data Expedi��o Intima��o
                                $arrDadosEmail['data_expedicao_intimacao'] = count($dtIntimacao) > 0 ? $dtIntimacao[0] : null;

                                //Calcular Data Final do Prazo T�cito
                                $dataFimPrazoTacito = '';
                                $objMdPetIntPrazoRN = new MdPetIntPrazoRN();
                                $arrDadosEmail['data_final_prazo_intimacao_tacita'] = $objMdPetIntPrazoRN->calcularDataPrazo($arrDadosEmail['prazo_intimacao_tacita'], $arrDadosEmail['data_expedicao_intimacao']);

                                //Documento Principal
                                $dados = $objMdPetIntimacaoRN->retornaDadosDocPrincipalIntimacao(array($idIntimacao));

                                $objDocumentoDTO = new DocumentoDTO();
                                $objDocumentoDTO->retDblIdDocumento();
                                $objDocumentoDTO->retNumIdOrgaoUnidadeResponsavel();
                                $objDocumentoDTO->retStrProtocoloDocumentoFormatado();
                                $objDocumentoDTO->retStrNomeSerie();
                                $objDocumentoDTO->retStrNumero();
                                $objDocumentoDTO->retNumIdSerie();
                                $objDocumentoDTO->retStrProtocoloProcedimentoFormatado();
                                $objDocumentoDTO->retStrNomeTipoProcedimentoProcedimento();

                                $objDocumentoDTO->setDblIdDocumento($dados[3]);
                                $objDocumentoRN = new DocumentoRN();
                                $objDocumentoDTO = $objDocumentoRN->consultarRN0005($objDocumentoDTO);

                                $arrDadosEmail['documento_principal_intimacao'] = $dados[0];
                                $arrDadosEmail['tipo_documento_principal_intimacao'] = $dados[1];
                                if (!empty($dados[4])) {
                                    $arrDadosEmail['tipo_documento_principal_intimacao'] .= ' ' . $dados[4];
                                }

                                $arrDadosEmail['processo'] = $objDocumentoDTO->getStrProtocoloProcedimentoFormatado();
                                $arrDadosEmail['tipo_processo'] = $objDocumentoDTO->getStrNomeTipoProcedimentoProcedimento();
                                if ($enviaEmail) {
                                    $this->emailReiteracaoExigeResposta($arrDadosEmail, $params[1]);
                                    $qtdEnviadas++;

                                    $retornoIntimacaoPF = $this->enviarEmailProcuradorPf($idContato, $destinatario, $idIntimacao, $params[1], $arrDadosEmail);
                                    $qtdEnviadas += $retornoIntimacaoPF['qtdEnviadas'];
                                    $qtdNaoEnviadas += $retornoIntimacaoPF['qtdN�oEnviadas'];
                                    array_merge($arrDadosEmailNaoEnviados, $retornoIntimacaoPF['arrDadosEmailNaoEnviados']);
                                } else {
                                    $qtdNaoEnviadas++;
                                    $arrDadosEmailNaoEnviados[] = $arrDadosEmail;
                                }

                            }

                        }

                    }
                }
            }
            return array('qtdEnviadas' => $qtdEnviadas, 'qtdN�oEnviadas' => $qtdNaoEnviadas, 'arrDadosEmailNaoEnviados' => $arrDadosEmailNaoEnviados);
        } catch (Exception $e) {
            LogSEI::getInstance()->gravar('ReiterarIntimacaoExigeResposta: ' . $e, InfraLog::$INFORMACAO);
            throw new InfraException('Erro reiterando intimacoes pendentes exige resposta.', $e);
        }
    }


    //Juridico

    protected function enviarEmailReiteracaoIntimacaoJuridicoConectado($params)
    {
        try {
            $arrObjMdPetIntRelDestinatarioDTO = $params[0];

            $qtdEnviadas = 0;
            $qtdNaoEnviadas = 0;
            $arrDadosEmailNaoEnviados = array();
            $arrDadosEmail = array();

            //Usu�rio do M�dulo de Peticionamento
            $objUsuarioPetRN = new MdPetIntUsuarioRN();
            $idUsuarioPet = $objUsuarioPetRN->getObjUsuarioPeticionamento(true);

            ////////// CAMPOS EM COMUM
            $arrDadosEmail['sigla_sistema'] = ConfiguracaoSEI::getInstance()->getValor('SessaoSEI', 'SiglaSistema');

            $objInfraParametro = new InfraParametro(BancoSEI::getInstance());
            $arrDadosEmail['email_sistema'] = $objInfraParametro->getValor('SEI_EMAIL_SISTEMA');

            $arrDadosEmail['link_login_usuario_externo'] = ConfiguracaoSEI::getInstance()->getValor('SEI', 'URL') . '/controlador_externo.php?acao=usuario_externo_logar&id_orgao_acesso_externo=0';
            ////////// CAMPOS EM COMUM - fim

            if (count($arrObjMdPetIntRelDestinatarioDTO) > 0) {
                foreach ($arrObjMdPetIntRelDestinatarioDTO as $destinatario) {
                    $arrDadosEmail['tipo_resposta'] = '';
                    $isReiteracao = false;

                    $idIntimacao = $destinatario->getNumIdMdPetIntimacao();
                    $idMdPetIntRelDestinatario = $destinatario->getNumIdMdPetIntRelDestinatario();

                    $idContato = $destinatario->getNumIdContato();

                    $dtIntimacaoAceite = !is_null($destinatario->getDthDataAceite()) ? explode(' ', $destinatario->getDthDataAceite()) : null;
                    $arrDadosEmail['data_cumprimento_intimacao'] = count($dtIntimacaoAceite) > 0 ? $dtIntimacaoAceite[0] : null;

                    $objMdPetIntPrazoRN = new MdPetIntPrazoRN();
                    $arrObjMdPetIntPrazoDTO = $objMdPetIntPrazoRN->retornarTipoRespostaValido(array($idIntimacao, $idMdPetIntRelDestinatario));

                    //Existe algum Tipo de Resposta que ainda possui prazo?
                    if (count($arrObjMdPetIntPrazoDTO) > 0) {

                        $objMdPetIntPrazoRN = new MdPetIntPrazoRN();
                        $arrObjMdPetIntPrazoDTO = $objMdPetIntPrazoRN->retornarTipoRespostaValido(array($idIntimacao, $idMdPetIntRelDestinatario, true, false));
                        //var_dump($arrObjMdPetIntPrazoDTO[0]->getNumValorPrazoExterno());die;


                        if (count($arrObjMdPetIntPrazoDTO) > 0) {

                            $tipoRespostaDTO = $arrObjMdPetIntPrazoDTO[0];

                            //Data prazo de 1 ou 5 dias
                            $dataAtual = InfraData::getStrDataAtual();

                            $dataFinal = $tipoRespostaDTO->getDthDataProrrogada();

                            if (empty($dataFinal)) {
                                $dataFinal = $tipoRespostaDTO->getDthDataLimite();

                            }
                            if (is_array(explode(" ", $dataFinal))) {
                                $dataFinal = explode(" ", $dataFinal);

                                $dataFinal = $dataFinal[0];
                            }

                            if (InfraData::compararDatas($dataFinal, $dataAtual) == -1 || InfraData::compararDatas($dataFinal, $dataAtual) == -5) {

                                $isReiteracao = true;
                                $arrDadosEmail['tipo_resposta'] = $tipoRespostaDTO->getStrNome();
                                $arrDadosEmail['prazo_externo_tipo_resposta'] = $tipoRespostaDTO->getNumValorPrazoExterno();
                                $arrDadosEmail['tipo_prazo_externo_tipo_resposta'] = $tipoRespostaDTO->getStrTipoPrazoExterno();
                            }
                        }

                        if ($isReiteracao) {
                            //Dados Unidade
                            $objMdPetIntimacaoRN = new MdPetIntimacaoRN();
                            $objUnidadeIntimacaoDTO = $objMdPetIntimacaoRN->getUnidadeIntimacao(array($idIntimacao));

                            if ($objUnidadeIntimacaoDTO) {
                                if (is_numeric($objUnidadeIntimacaoDTO->getNumIdUnidade())) {
                                    $objUnidadeDTO = new UnidadeDTO();
                                    $objUnidadeDTO->retStrSigla();
                                    $objUnidadeDTO->retStrDescricao();
                                    $objUnidadeDTO->retStrSiglaOrgao();
                                    $objUnidadeDTO->retStrDescricaoOrgao();
                                    $objUnidadeDTO->retStrSitioInternetOrgaoContato();
                                    $objUnidadeDTO->setBolExclusaoLogica(false);
                                    $objUnidadeDTO->setNumIdUnidade($objUnidadeIntimacaoDTO->getNumIdUnidade());

                                    $objUnidadeRN = new UnidadeRN();
                                    $objUnidadeDTO = $objUnidadeRN->consultarRN0125($objUnidadeDTO);

                                    if ($objUnidadeIntimacaoDTO) {
                                        $arrDadosEmail['sigla_orgao'] = $objUnidadeDTO->getStrSiglaOrgao();
                                        $arrDadosEmail['descricao_orgao'] = $objUnidadeDTO->getStrDescricaoOrgao();
                                        $arrDadosEmail['sitio_internet_orgao'] = $objUnidadeDTO->getStrSitioInternetOrgaoContato();
                                    }

                                }
                            }

                            //Recuperando a pessoa relacionada a empresa
                            $dtoMdPetVincReptDTO = new MdPetVincRepresentantDTO();
                            $dtoMdPetVincReptDTO->setNumIdContatoVinc($idContato);
                            $dtoMdPetVincReptDTO->retNumIdContatoVinc();
                            $dtoMdPetVincReptDTO->retStrNomeProcurador();
                            $dtoMdPetVincReptDTO->retStrRazaoSocialNomeVinc();
                            $dtoMdPetVincReptDTO->retStrTipoRepresentante();
                            $dtoMdPetVincReptDTO->retStrCNPJ();
                            $dtoMdPetVincReptDTO->retStrEmail();
                            $dtoMdPetVincReptDTO->retNumIdContatoProcurador();
                            $dtoMdPetVincReptDTO->retNumIdMdPetVinculoRepresent();
                            $dtoMdPetVincReptDTO->setStrStaEstado(MdPetVincRepresentantRN::$RP_ATIVO);

                            $rnMdPetVincRepRN = new MdPetVincRepresentantRN();
                            $arrMdPetVincRepRN = $rnMdPetVincRepRN->listar($dtoMdPetVincReptDTO);

                            //Recuperando Vinculo, Raz�o Social e CNPJ
                            //Para cada pessoa vinculada a empresa

                            foreach ($arrMdPetVincRepRN as $key => $value) {

                                $temPoder = $this->verificaPoder($value->getStrTipoRepresentante(), $value->getNumIdMdPetVinculoRepresent());

                                if ($temPoder) {
                                    $arrDadosEmail['cnpj'] = $value->getStrCNPJ();
                                    $arrDadosEmail['razaoSocial'] = $value->getStrRazaoSocialNomeVinc();

                                    if ($value->getStrTipoRepresentante() == MdPetVincRepresentantRN::$PE_RESPONSAVEL_LEGAL) {
                                        $arrDadosEmail['tpVinc'] = "Responsavel Legal";
                                    }
                                    if ($value->getStrTipoRepresentante() == MdPetVincRepresentantRN::$PE_PROCURADOR_ESPECIAL) {
                                        $arrDadosEmail['tpVinc'] = "Procurador Especial";
                                    }
                                    if ($value->getStrTipoRepresentante() == MdPetVincRepresentantRN::$PE_PROCURADOR_SIMPLES) {
                                        $arrDadosEmail['tpVinc'] = "Procurador Simples";
                                    }

                                    //Recuperando a pessoa relacionada a empresa // FIM

                                    $objUsuarioDTO = new UsuarioDTO();
                                    $objUsuarioDTO->retNumIdUsuario();
                                    $objUsuarioDTO->retStrSigla();
                                    $objUsuarioDTO->retStrNome();
                                    $objUsuarioDTO->retStrStaTipo();
                                    $objUsuarioDTO->retNumIdContato();
                                    $objUsuarioDTO->setNumIdContato($value->getNumIdContatoProcurador());

                                    $objUsuarioRN = new UsuarioRN();
                                    $objUsuarioDTO = $objUsuarioRN->consultarRN0489($objUsuarioDTO);

                                    if (is_object($objUsuarioDTO)) {

                                        if ($objUsuarioDTO->getStrStaTipo() == UsuarioRN::$TU_EXTERNO_PENDENTE) {
//                                        $objInfraException->lancarValidacao('Usu�rio externo "' . $objUsuarioDTO->getStrSigla() . '" ainda n�o foi liberado.');
                                        }

                                        if ($objUsuarioDTO->getStrStaTipo() != UsuarioRN::$TU_EXTERNO) {
//                                        $objInfraException->lancarValidacao('Usu�rio "' . $objUsuarioDTO->getStrSigla() . '" n�o � um usu�rio externo.');
                                        }
                                        //contato - fim
                                        $enviaEmail = true;
                                        $conta = "^[a-zA-Z0-9\._-]+@";
                                        $domino = "[a-zA-Z0-9\._-]+.";
                                        $extensao = "([a-zA-Z]{2,4})$";
                                        $pattern = "#" . $conta . $domino . $extensao . "#";
                                        $isEmail = preg_match($pattern, $objUsuarioDTO->getStrSigla());
                                        if (!$isEmail) {
                                            $enviaEmail = false;
                                        }
                                        $arrDadosEmail['email_usuario_externo'] = $objUsuarioDTO->getStrSigla();
                                        $arrDadosEmail['nome_usuario_externo'] = $objUsuarioDTO->getStrNome();

                                        $arrDadosEmail['tipo_intimacao'] = $destinatario->getStrNomeTipoIntimacao();

                                        //Get Prazo T�cito
                                        $objPrazoTacitoDTO = new MdPetIntPrazoTacitaDTO();
                                        $objPrazoTacitoDTO->retNumNumPrazo();
                                        $objPrazoTacitoRN = new MdPetIntPrazoTacitaRN();
                                        $retLista = $objPrazoTacitoRN->listar($objPrazoTacitoDTO);
                                        $objPrazoTacitoDTO = !is_null($retLista) && count($retLista) > 0 ? current($retLista) : null;
                                        $arrDadosEmail['prazo_intimacao_tacita'] = !is_null($objPrazoTacitoDTO) ? $objPrazoTacitoDTO->getNumNumPrazo() : null;

                                        $dtIntimacao = !is_null($destinatario->getDthDataCadastro()) ? explode(' ', $destinatario->getDthDataCadastro()) : null;

                                        //Data Expedi��o Intima��o
                                        $arrDadosEmail['data_expedicao_intimacao'] = count($dtIntimacao) > 0 ? $dtIntimacao[0] : null;

                                        //Calcular Data Final do Prazo T�cito
                                        $dataFimPrazoTacito = '';
                                        $objMdPetIntPrazoRN = new MdPetIntPrazoRN();
                                        $arrDadosEmail['data_final_prazo_intimacao_tacita'] = $objMdPetIntPrazoRN->calcularDataPrazo($arrDadosEmail['prazo_intimacao_tacita'], $arrDadosEmail['data_expedicao_intimacao']);

                                        //Documento Principal
                                        $dados = $objMdPetIntimacaoRN->retornaDadosDocPrincipalIntimacao(array($idIntimacao));

                                        $objDocumentoDTO = new DocumentoDTO();
                                        $objDocumentoDTO->retDblIdDocumento();
                                        $objDocumentoDTO->retNumIdOrgaoUnidadeResponsavel();
                                        $objDocumentoDTO->retStrProtocoloDocumentoFormatado();
                                        $objDocumentoDTO->retStrNomeSerie();
                                        $objDocumentoDTO->retStrNumero();
                                        $objDocumentoDTO->retNumIdSerie();
                                        $objDocumentoDTO->retStrProtocoloProcedimentoFormatado();
                                        $objDocumentoDTO->retStrNomeTipoProcedimentoProcedimento();

                                        $objDocumentoDTO->setDblIdDocumento($dados[3]);
                                        $objDocumentoRN = new DocumentoRN();
                                        $objDocumentoDTO = $objDocumentoRN->consultarRN0005($objDocumentoDTO);

                                        $arrDadosEmail['documento_principal_intimacao'] = $dados[0];
                                        $arrDadosEmail['tipo_documento_principal_intimacao'] = $dados[1];
                                        if (!empty($dados[4])) {
                                            $arrDadosEmail['tipo_documento_principal_intimacao'] .= ' ' . $dados[4];
                                        }

                                        $arrDadosEmail['processo'] = $objDocumentoDTO->getStrProtocoloProcedimentoFormatado();
                                        $arrDadosEmail['tipo_processo'] = $objDocumentoDTO->getStrNomeTipoProcedimentoProcedimento();
                                        if ($isEmail) {
                                            $this->emailReiteracaoExigeResposta($arrDadosEmail, $params[1]);
                                            $qtdEnviadas++;
                                        } else {
                                            $qtdNaoEnviadas++;
                                            $arrDadosEmailNaoEnviados[] = $arrDadosEmail;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
            return array('qtdEnviadas' => $qtdEnviadas, 'qtdN�oEnviadas' => $qtdNaoEnviadas, 'arrDadosEmailNaoEnviados' => $arrDadosEmailNaoEnviados);
        } catch (Exception $e) {
            LogSEI::getInstance()->gravar('ReiterarIntimacaoExigeResposta: ' . $e, InfraLog::$INFORMACAO);
            throw new InfraException('Erro reiterando intimacoes pendentes exige resposta.', $e);
        }
    }


    public function enviarEmailVincSuspensaoConectado($params)
    {

        $objProcedimentoDTO = $params['procedimento'];
        $dados = $params['dados'];

        $idVinculo = isset($dados['hdnIdVinculo']) ? $dados['hdnIdVinculo'] : null;
        $numeroSEI = isset($dados['hdnNumeroSei']) ? $dados['hdnNumeroSei'] : null;
        $numeroSEIVinculacao = isset($dados['numeroSeiVinculacao']) ? $dados['numeroSeiVinculacao'] : null;
        //Usu�rio do M�dulo de Peticionamento
//        $objUsuarioPetRN  = new MdPetIntUsuarioRN();
//        $idUsuarioPet = $objUsuarioPetRN->getObjUsuarioPeticionamento(true);

        $arrDadosEmail = array();
        $arrDadosEmail['tipo_operacao'] = $dados['hdnOperacao'];

        ////////// CAMPOS EM COMUM

        $arrDadosEmail['link_login_usuario_externo'] = ConfiguracaoSEI::getInstance()->getValor('SEI', 'URL') . '/controlador_externo.php?acao=usuario_externo_logar&id_orgao_acesso_externo=0';
        $arrDadosEmail['sitio_internet_orgao'] = ConfiguracaoSEI::getInstance()->getValor('SessaoSEI', 'SiglaSistema');
        $arrDadosEmail['sigla_sistema'] = ConfiguracaoSEI::getInstance()->getValor('SessaoSEI', 'SiglaSistema');

        $objInfraParametro = new InfraParametro(BancoSEI::getInstance());
        $arrDadosEmail['email_sistema'] = $objInfraParametro->getValor('SEI_EMAIL_SISTEMA');

        $itemListaProcuradores = '';

        if(is_array($params['arrListaProcuradores']) && count($params['arrListaProcuradores']) > 0){
            $itemListaProcuradores = "- As Procura��es Eletr�nicas, abaixo listadas, concedidas para representa��o da Pessoa Jur�dica restam igualmente suspensas:
";
            for ($i=0; $i < count($params['arrListaProcuradores']); $i++) { 
                $itemListaProcuradores .= "
      -- ".$params['arrListaProcuradores'][$i];
            }
        }

        $arrDadosEmail['item_lista_procuradores'] = $itemListaProcuradores;

        ////////// CAMPOS EM COMUM - fim

        $objMdPetVinculoRN = new MdPetVinculoRN();
        $objMdPetVinculoDTO = new MdPetVinculoDTO();

        $objMdPetVinculoDTO->retTodos(true);
        $objMdPetVinculoDTO->retStrRazaoSocialNomeVinc();
        $objMdPetVinculoDTO->retDblCNPJ();
        $objMdPetVinculoDTO->retStrNomeContatoRepresentante();
        $objMdPetVinculoDTO->retStrCpfContatoRepresentante();
        $objMdPetVinculoDTO->retStrEmailContatoRepresentante();
        $objMdPetVinculoDTO->retStrRazaoSocialNomeVinc();
        $objMdPetVinculoDTO->retDthDataVinculo();
        $objMdPetVinculoDTO->setNumIdMdPetVinculo($idVinculo);
        $objMdPetVinculoDTO->setStrTipoRepresentante(MdPetVincRepresentantRN::$PE_RESPONSAVEL_LEGAL);

        $arrObjMdPetVinculoDTO = $objMdPetVinculoRN->listar($objMdPetVinculoDTO);

        if (count($arrObjMdPetVinculoDTO) > 0) {

            $arrDadosEmail['dadosUsuario']['nome'] = $arrObjMdPetVinculoDTO[0]->getStrNomeContatoRepresentante();
            $arrDadosEmail['dadosUsuario']['email'] = $arrObjMdPetVinculoDTO[0]->getStrEmailContatoRepresentante();
            $arrDadosEmail['dadosUsuario']['processo'] = $objProcedimentoDTO->getStrProtocoloProcedimentoFormatado();
            $arrDadosEmail['dadosUsuario']['razao_social'] = $arrObjMdPetVinculoDTO[0]->getStrRazaoSocialNomeVinc();
            $arrDadosEmail['dadosUsuario']['cnpj'] = $arrObjMdPetVinculoDTO[0]->getDblCNPJ();

            $objProtocoloDTO = new ProtocoloDTO();
            $objProtocoloDTO->setDblIdProtocolo($numeroSEIVinculacao);
            $objProtocoloDTO->retStrProtocoloFormatado();
            $objProtocoloDTO = (new ProtocoloRN())->consultarRN0186($objProtocoloDTO);
            $arrDadosEmail['dadosUsuario']['documento_suspensao_responsavel_pj'] = $objProtocoloDTO->getStrProtocoloFormatado();

            //Orgao
            $usuarioDTO = new UsuarioDTO();
            $usuarioDTO->retStrSiglaOrgao();
            $usuarioDTO->retStrDescricaoOrgao();
            $usuarioDTO->setNumIdContato($arrObjMdPetVinculoDTO[0]->getNumIdContatoRepresentante());

            $usuarioRN = new UsuarioRN();
            $usuarioDTO = $usuarioRN->consultarRN0489($usuarioDTO);

            if ($usuarioDTO) {
                if (!empty(SessaoSEI::getInstance()->getNumIdUnidadeAtual())) {
                    $idUnidade = SessaoSEI::getInstance()->getNumIdUnidadeAtual();
                } else {
                    $idUnidade = SessaoSEIExterna::getInstance()->getNumIdUnidadeAtual();
                }

                $arrDadosEmail['sigla_orgao'] = $usuarioDTO->getStrSiglaOrgao();
                $arrDadosEmail['descricao_orgao'] = $usuarioDTO->getStrDescricaoOrgao();

                $objUnidadeDTO = new UnidadeDTO();
                $objUnidadeDTO->retStrSitioInternetOrgaoContato();
                $objUnidadeDTO->setNumIdUnidade($idUnidade);

                $UnidadeRN = new UnidadeRN();
                $arrObjUnidadeDTO = $UnidadeRN->consultarRN0125($objUnidadeDTO);
                if ($arrObjUnidadeDTO) {
                    $arrDadosEmail['sitio_internet_orgao'] = $arrObjUnidadeDTO->getStrSitioInternetOrgaoContato();
                }
            }
        }

        $this->emailVincSuspensaoRestabelecimento($arrDadosEmail);

    }

    public function enviarEmailVincRestabelecimentoConectado($params)
    {

        $objProcedimentoDTO = $params['procedimento'];
        $dados = $params['dados'];

        $idVinculo = isset($dados['hdnIdVinculo']) ? $dados['hdnIdVinculo'] : null;
        $numeroSEI = isset($dados['hdnNumeroSei']) ? $dados['hdnNumeroSei'] : null;
        $numeroSEIVinculacao = isset($dados['numeroSeiVinculacao']) ? $dados['numeroSeiVinculacao'] : null;

        $arrDadosEmail = array();
        $arrDadosEmail['tipo_operacao'] = $dados['hdnOperacao'];

        ////////// CAMPOS EM COMUM
        
        $arrDadosEmail['link_login_usuario_externo'] = ConfiguracaoSEI::getInstance()->getValor('SEI', 'URL') . '/controlador_externo.php?acao=usuario_externo_logar&id_orgao_acesso_externo=0';
        $arrDadosEmail['sitio_internet_orgao'] = ConfiguracaoSEI::getInstance()->getValor('SessaoSEI', 'SiglaSistema');
        $arrDadosEmail['sigla_sistema'] = ConfiguracaoSEI::getInstance()->getValor('SessaoSEI', 'SiglaSistema');

        $objInfraParametro = new InfraParametro(BancoSEI::getInstance());
        $arrDadosEmail['email_sistema'] = $objInfraParametro->getValor('SEI_EMAIL_SISTEMA');

        $itemListaProcuradores = '';

        if(is_array($params['arrListaProcuradores']) && count($params['arrListaProcuradores']) > 0){
            $itemListaProcuradores = "- As Procura��es Eletr�nicas, abaixo listadas, que tenham sido suspensas restam igualmente restabelecidas:
";
            for ($i=0; $i < count($params['arrListaProcuradores']); $i++) { 
                $itemListaProcuradores .= "
      -- ".$params['arrListaProcuradores'][$i];
            }
        }

        $arrDadosEmail['item_lista_procuradores'] = $itemListaProcuradores;

        ////////// CAMPOS EM COMUM - fim

        $objMdPetVinculoRN = new MdPetVinculoRN();
        $objMdPetVinculoDTO = new MdPetVinculoDTO();

        $objMdPetVinculoDTO->retTodos(true);
        $objMdPetVinculoDTO->retStrRazaoSocialNomeVinc();
        $objMdPetVinculoDTO->retDblCNPJ();
        $objMdPetVinculoDTO->retStrNomeContatoRepresentante();
        $objMdPetVinculoDTO->retStrCpfContatoRepresentante();
        $objMdPetVinculoDTO->retStrEmailContatoRepresentante();
        $objMdPetVinculoDTO->retStrRazaoSocialNomeVinc();
        $objMdPetVinculoDTO->retDthDataVinculo();
        $objMdPetVinculoDTO->setNumIdMdPetVinculo($idVinculo);
        $objMdPetVinculoDTO->setStrTipoRepresentante(MdPetVincRepresentantRN::$PE_RESPONSAVEL_LEGAL);

        $arrObjMdPetVinculoDTO = $objMdPetVinculoRN->listar($objMdPetVinculoDTO);

        if (count($arrObjMdPetVinculoDTO) > 0) {
            $arrDadosEmail['dadosUsuario']['nome'] = $arrObjMdPetVinculoDTO[0]->getStrNomeContatoRepresentante();
            $arrDadosEmail['dadosUsuario']['email'] = $arrObjMdPetVinculoDTO[0]->getStrEmailContatoRepresentante();
            $arrDadosEmail['dadosUsuario']['processo'] = $objProcedimentoDTO->getStrProtocoloProcedimentoFormatado();
            $arrDadosEmail['dadosUsuario']['razao_social'] = $arrObjMdPetVinculoDTO[0]->getStrRazaoSocialNomeVinc();
            $arrDadosEmail['dadosUsuario']['cnpj'] = $arrObjMdPetVinculoDTO[0]->getDblCNPJ();

            $protocoloRN = new ProtocoloRN();
            $objProtocoloDTO = new ProtocoloDTO();
            $objProtocoloDTO->setDblIdProtocolo($numeroSEIVinculacao);
            $objProtocoloDTO->retStrProtocoloFormatado();
            $objProtocoloDTO = $protocoloRN->consultarRN0186($objProtocoloDTO);
            $arrDadosEmail['dadosUsuario']['documento_restabelecimento_responsavel_pj'] = $objProtocoloDTO->getStrProtocoloFormatado();

            //Orgao
            $usuarioDTO = new UsuarioDTO();
            $usuarioDTO->retStrSiglaOrgao();
            $usuarioDTO->retStrDescricaoOrgao();
            $usuarioDTO->setNumIdContato($arrObjMdPetVinculoDTO[0]->getNumIdContatoRepresentante());

            $usuarioRN = new UsuarioRN();
            $usuarioDTO = $usuarioRN->consultarRN0489($usuarioDTO);

            if ($usuarioDTO) {
                if (!empty(SessaoSEI::getInstance()->getNumIdUnidadeAtual())) {
                    $idUnidade = SessaoSEI::getInstance()->getNumIdUnidadeAtual();
                } else {
                    $idUnidade = SessaoSEIExterna::getInstance()->getNumIdUnidadeAtual();
                }

                $arrDadosEmail['sigla_orgao'] = $usuarioDTO->getStrSiglaOrgao();
                $arrDadosEmail['descricao_orgao'] = $usuarioDTO->getStrDescricaoOrgao();

                $objUnidadeDTO = new UnidadeDTO();
                $objUnidadeDTO->retStrSitioInternetOrgaoContato();
                $objUnidadeDTO->setNumIdUnidade($idUnidade);

                $UnidadeRN = new UnidadeRN();
                $arrObjUnidadeDTO = $UnidadeRN->consultarRN0125($objUnidadeDTO);
                if ($arrObjUnidadeDTO) {
                    $arrDadosEmail['sitio_internet_orgao'] = $arrObjUnidadeDTO->getStrSitioInternetOrgaoContato();
                }
            }
        }

        $this->emailVincSuspensaoRestabelecimento($arrDadosEmail);

    }
    
    public function enviarEmailProcuracaoSuspRestConectado($params)
    {

        $objProcedimentoDTO = $params['procedimento'];
        $dados = $params['dados'];

        $numeroSEIVinculacao = isset($dados['numeroSeiVinculacao']) ? $dados['numeroSeiVinculacao'] : null;
        $idVinculoRepresent = isset($dados['hdnIdVinculoRepresent']) ? $dados['hdnIdVinculoRepresent'] : null;
        $tipoRepresentante = isset($dados['tipo_representante']) ? $dados['tipo_representante'] : null;

        // CAMPOS EM COMUM
        $arrDadosEmail = [];
        $arrDadosEmail['link_login_usuario_externo'] = ConfiguracaoSEI::getInstance()->getValor('SEI', 'URL') . '/controlador_externo.php?acao=usuario_externo_logar&id_orgao_acesso_externo=0';
        $arrDadosEmail['sitio_internet_orgao'] = ConfiguracaoSEI::getInstance()->getValor('SessaoSEI', 'SiglaSistema');
        $arrDadosEmail['email_sistema'] = (new InfraParametro(BancoSEI::getInstance()))->getValor('SEI_EMAIL_SISTEMA');
        $arrDadosEmail['sigla_sistema'] = ConfiguracaoSEI::getInstance()->getValor('SessaoSEI', 'SiglaSistema');

        // RETORNANDO DADOS DO VINCULO DO PROCURADOR
        $objMdPetVincRepresentantDTO = new MdPetVincRepresentantDTO();
        $objMdPetVincRepresentantDTO->retTodos();
        $objMdPetVincRepresentantDTO->retNumIdMdPetVinculo();
        $objMdPetVincRepresentantDTO->retNumIdMdPetVinculoRepresent();
        $objMdPetVincRepresentantDTO->retStrTipoRepresentante();
        $objMdPetVincRepresentantDTO->retStrStaEstado();
        $objMdPetVincRepresentantDTO->retStrRazaoSocialNomeVinc();
        $objMdPetVincRepresentantDTO->retStrCNPJ();
        $objMdPetVincRepresentantDTO->retStrCPF();
        $objMdPetVincRepresentantDTO->retStrTpVinc();
        $objMdPetVincRepresentantDTO->retNumIdContatoVinc();
        $objMdPetVincRepresentantDTO->retStrNomeProcurador();
        $objMdPetVincRepresentantDTO->retNumIdContatoProcurador();
        $objMdPetVincRepresentantDTO->retStrCpfProcurador();
        $objMdPetVincRepresentantDTO->retStrEmail();
        $objMdPetVincRepresentantDTO->retDblIdProcedimentoVinculo();
        $objMdPetVincRepresentantDTO->setNumIdMdPetVinculoRepresent($idVinculoRepresent);
        $objMdPetVincRepresentantDTO = (new MdPetVincRepresentantRN())->consultar($objMdPetVincRepresentantDTO);

        if (!empty($objMdPetVincRepresentantDTO)) {

            $arrDadosEmail['dadosUsuario']['procuracao_tipo']        = $dados['procuracao_tipo'];
            $arrDadosEmail['dadosUsuario']['procuracao_doc_num']     = $dados['procuracao_doc_num'];

            $arrDadosEmail['dadosUsuario']['processo']               = $objProcedimentoDTO->getStrProtocoloProcedimentoFormatado();
            $arrDadosEmail['dadosUsuario']['outorgado_nome']         = mb_strtoupper($objMdPetVincRepresentantDTO->getStrNomeProcurador());
            $arrDadosEmail['dadosUsuario']['outorgado_email']        = $objMdPetVincRepresentantDTO->getStrEmail();
            $arrDadosEmail['dadosUsuario']['outorgante_tipo_pessoa'] = $objMdPetVincRepresentantDTO->getStrTpVinc() == 'J' ? 'Jur�dica' : 'F�sica';

            $arrDadosEmail['dadosUsuario']['outorgante_nome']        = mb_strtoupper($objMdPetVincRepresentantDTO->getStrRazaoSocialNomeVinc());
            if($objMdPetVincRepresentantDTO->getStrTpVinc() == 'J'){
                $arrDadosEmail['dadosUsuario']['outorgante_nome']    .= ' ('.InfraUtil::formatarCnpj($objMdPetVincRepresentantDTO->getStrCNPJ()).')';
            }

            $objProtocoloDTO = new ProtocoloDTO();
            $objProtocoloDTO->setDblIdProtocolo($numeroSEIVinculacao);
            $objProtocoloDTO->retStrProtocoloFormatado();
            $objProtocoloDTO = (new ProtocoloRN())->consultarRN0186($objProtocoloDTO);

            if($dados['hdnOperacao'] == 'S'){
                $arrDadosEmail['dadosUsuario']['documento_suspensao_procuracao'] = $objProtocoloDTO->getStrProtocoloFormatado();
            }else{
                $paragrafoSimples   = '- Ficam restabelecidos os Poderes de Representa��o e de peticionar em nome do Outorgante;';
                $paragrafoEspecial  = '- Ficam restabelecidos os Poderes de Representa��o, o seu direito de peticionar e emitir Procura��es Eletr�nicas Simples em nome do Outorgante;';
                
                $arrDadosEmail['dadosUsuario']['paragrafo_comunicado'] = $dados['hdnStrTipoVinculo'] == MdPetVincRepresentantRN::$PE_PROCURADOR_SIMPLES ? $paragrafoSimples : $paragrafoEspecial;
                $arrDadosEmail['dadosUsuario']['documento_restabelecimento_procuracao'] = $objProtocoloDTO->getStrProtocoloFormatado();
            }

            $arrDadosEmail['tipo_operacao'] = $dados['hdnOperacao'];
            $arrDadosEmail['tipo_vinculo']  = $dados['hdnStrTipoVinculo'];

            // Orgao
            $usuarioDTO = new UsuarioDTO();
            $usuarioDTO->retStrSiglaOrgao();
            $usuarioDTO->retStrDescricaoOrgao();
            $usuarioDTO->setNumIdContato($objMdPetVincRepresentantDTO->getNumIdContatoProcurador());
            $usuarioDTO = (new UsuarioRN())->consultarRN0489($usuarioDTO);

            if ($usuarioDTO) {

                $idUnidade = !empty(SessaoSEI::getInstance()->getNumIdUnidadeAtual()) ? SessaoSEI::getInstance()->getNumIdUnidadeAtual() : SessaoSEIExterna::getInstance()->getNumIdUnidadeAtual();
                
                $arrDadosEmail['sigla_orgao'] = $usuarioDTO->getStrSiglaOrgao();
                $arrDadosEmail['descricao_orgao'] = $usuarioDTO->getStrDescricaoOrgao();

                $objUnidadeDTO = new UnidadeDTO();
                $objUnidadeDTO->retStrSitioInternetOrgaoContato();
                $objUnidadeDTO->setNumIdUnidade($idUnidade);
                $arrObjUnidadeDTO = (new UnidadeRN())->consultarRN0125($objUnidadeDTO);
                
                if ($arrObjUnidadeDTO) {
                    $arrDadosEmail['sitio_internet_orgao'] = $arrObjUnidadeDTO->getStrSitioInternetOrgaoContato();
                }

            }

        }

        $this->emailProcuracaoSuspensaoRestabelecimento($arrDadosEmail);

    }


    public function emailRespostasFacultativas($arrDadosEmail)
    {
        //Enviar Email
        $objInfraParametro = new InfraParametro(BancoSEI::getInstance());
        $objEmailSistemaDTO = new EmailSistemaDTO();
        $objEmailSistemaDTO->retStrDe();
        $objEmailSistemaDTO->retStrPara();
        $objEmailSistemaDTO->retStrAssunto();
        $objEmailSistemaDTO->retStrConteudo();
        $objEmailSistemaDTO->setStrIdEmailSistemaModulo('MD_PET_INTIMACAO_APENAS_RESPOSTAS_FACULTATIVAS');

        $objEmailSistemaRN = new EmailSistemaRN();
        $objEmailSistemaDTO = $objEmailSistemaRN->consultar($objEmailSistemaDTO);

        //variaveis basicas em uso no email
        $linkLoginUsuarioExterno = SessaoSEIExterna::getInstance()->getStrPaginaLogin() . '&id_orgao_acesso_externo=0';

        //Monta Email
        $strDe = $objEmailSistemaDTO->getStrDe();
        $strDe = str_replace('@sigla_sistema@', SessaoSEI::getInstance()->getStrSiglaSistema(), $strDe);
        $strDe = str_replace('@email_sistema@', $objInfraParametro->getValor('SEI_EMAIL_SISTEMA'), $strDe);

        $strPara = $objEmailSistemaDTO->getStrPara();
        $strPara = str_replace('@email_usuario_externo@', $arrDadosEmail['dadosUsuario']['email'], $strPara);//email usuario

        $strAssunto = $objEmailSistemaDTO->getStrAssunto();
        $strAssunto = str_replace('@processo@', $arrDadosEmail['dadosUsuario']['processo'], $strAssunto);//sistema

        $strConteudo = $objEmailSistemaDTO->getStrConteudo();
        $strConteudo = str_replace('@nome_usuario_externo@', $arrDadosEmail['dadosUsuario']['nome'], $strConteudo);

        $strConteudo = str_replace('@sigla_orgao@', $arrDadosEmail['objUnidadeDTO']->getStrSiglaOrgao(), $strConteudo);
        $strConteudo = str_replace('@tipo_intimacao@', $arrDadosEmail['objMdPetIntTipoIntimacaoDTO']->getStrNome(), $strConteudo);
        $strConteudo = str_replace('@processo@', $arrDadosEmail['dadosUsuario']['processo'], $strConteudo);
        $strConteudo = str_replace('@documento_principal_intimacao@', $arrDadosEmail['objDocumentoDTO']->getStrProtocoloDocumentoFormatado(), $strConteudo);
        $strConteudo = str_replace('@tipo_documento_principal_intimacao@', DocumentoINT::formatarIdentificacao($arrDadosEmail['objDocumentoDTO']), $strConteudo);
        $strConteudo = str_replace('@link_login_usuario_externo@', $linkLoginUsuarioExterno, $strConteudo);
        $strConteudo = str_replace('@prazo_intimacao_tacita@', $arrDadosEmail['prazoTacita'], $strConteudo);
        $strConteudo = str_replace('@data_expedicao_intimacao@', $arrDadosEmail['dadosUsuario']['dataHora'], $strConteudo);
        $strConteudo = str_replace('@data_final_prazo_intimacao_tacita@', $arrDadosEmail['dataFinalPrazoTacita'], $strConteudo);
        $strConteudo = str_replace('@descricao_orgao@', $arrDadosEmail['objUnidadeDTO']->getStrDescricaoOrgao(), $strConteudo);
        $strConteudo = str_replace('@sitio_internet_orgao@', $arrDadosEmail['objUnidadeDTO']->getStrSitioInternetOrgaoContato(), $strConteudo);

        $objEmailDTO = new EmailDTO();
        $objEmailDTO->setStrDe($strDe);
        $objEmailDTO->setStrPara($strPara);
        $objEmailDTO->setStrAssunto($strAssunto);
        $objEmailDTO->setStrMensagem($strConteudo);

        EmailRN::processar(array($objEmailDTO));
    }

    //Juridico

    public function emailRespostasFacultativasJuridico($arrDadosEmail)
    {
        //Enviar Email
        $objInfraParametro = new InfraParametro(BancoSEI::getInstance());
        $objEmailSistemaDTO = new EmailSistemaDTO();
        $objEmailSistemaDTO->retStrDe();
        $objEmailSistemaDTO->retStrPara();
        $objEmailSistemaDTO->retStrAssunto();
        $objEmailSistemaDTO->retStrConteudo();
        $objEmailSistemaDTO->setStrIdEmailSistemaModulo('MD_PET_INTIMACAO_APENAS_RESPOSTAS_FACULTATIVAS_J');

        $objEmailSistemaRN = new EmailSistemaRN();
        $objEmailSistemaDTO = $objEmailSistemaRN->consultar($objEmailSistemaDTO);

        //variaveis basicas em uso no email
        $linkLoginUsuarioExterno = SessaoSEIExterna::getInstance()->getStrPaginaLogin() . '&id_orgao_acesso_externo=0';

        //Monta Email
        $strDe = $objEmailSistemaDTO->getStrDe();
        $strDe = str_replace('@sigla_sistema@', SessaoSEI::getInstance()->getStrSiglaSistema(), $strDe);
        $strDe = str_replace('@email_sistema@', $objInfraParametro->getValor('SEI_EMAIL_SISTEMA'), $strDe);

        $strPara = $objEmailSistemaDTO->getStrPara();
        $strPara = str_replace('@email_usuario_externo@', $arrDadosEmail['dadosUsuario']['email'], $strPara);//email usuario

        $strAssunto = $objEmailSistemaDTO->getStrAssunto();
        $strAssunto = str_replace('@processo@', $arrDadosEmail['dadosUsuario']['processo'], $strAssunto);//sistema

        $strConteudo = $objEmailSistemaDTO->getStrConteudo();
        $strConteudo = str_replace('@nome_usuario_externo@', $arrDadosEmail['dadosUsuario']['nome'], $strConteudo);

        $strConteudo = str_replace('@sigla_orgao@', $arrDadosEmail['objUnidadeDTO']->getStrSiglaOrgao(), $strConteudo);
        $strConteudo = str_replace('@tipo_intimacao@', $arrDadosEmail['objMdPetIntTipoIntimacaoDTO']->getStrNome(), $strConteudo);
        $strConteudo = str_replace('@processo@', $arrDadosEmail['dadosUsuario']['processo'], $strConteudo);
        $strConteudo = str_replace('@documento_principal_intimacao@', $arrDadosEmail['objDocumentoDTO']->getStrProtocoloDocumentoFormatado(), $strConteudo);
        $strConteudo = str_replace('@tipo_documento_principal_intimacao@', DocumentoINT::formatarIdentificacao($arrDadosEmail['objDocumentoDTO']), $strConteudo);
        //Campo Juridico
        $strConteudo = str_replace('@razao_social@', $arrDadosEmail['dadosUsuario']['razaoSocial'], $strConteudo);
        $strConteudo = str_replace('@cnpj@', InfraUtil::formatarCnpj($arrDadosEmail['dadosUsuario']['cnpj']), $strConteudo);
        $strConteudo = str_replace('@tipo_vinculo@', $arrDadosEmail['dadosUsuario']['tpVinc'], $strConteudo);


        $strConteudo = str_replace('@link_login_usuario_externo@', $linkLoginUsuarioExterno, $strConteudo);
        $strConteudo = str_replace('@prazo_intimacao_tacita@', $arrDadosEmail['prazoTacita'], $strConteudo);
        $strConteudo = str_replace('@data_expedicao_intimacao@', $arrDadosEmail['dadosUsuario']['dataHora'], $strConteudo);
        $strConteudo = str_replace('@data_final_prazo_intimacao_tacita@', $arrDadosEmail['dataFinalPrazoTacita'], $strConteudo);
        $strConteudo = str_replace('@descricao_orgao@', $arrDadosEmail['objUnidadeDTO']->getStrDescricaoOrgao(), $strConteudo);
        $strConteudo = str_replace('@sitio_internet_orgao@', $arrDadosEmail['objUnidadeDTO']->getStrSitioInternetOrgaoContato(), $strConteudo);

        $objEmailDTO = new EmailDTO();
        $objEmailDTO->setStrDe($strDe);
        $objEmailDTO->setStrPara($strPara);
        $objEmailDTO->setStrAssunto($strAssunto);
        $objEmailDTO->setStrMensagem($strConteudo);

        EmailRN::processar(array($objEmailDTO));
    }

    public function emailExigeResposta($arrDadosEmail)
    {
        //Enviar Email
        $objInfraParametro = new InfraParametro(BancoSEI::getInstance());
        $objEmailSistemaDTO = new EmailSistemaDTO();
        $objEmailSistemaDTO->retStrDe();
        $objEmailSistemaDTO->retStrPara();
        $objEmailSistemaDTO->retStrAssunto();
        $objEmailSistemaDTO->retStrConteudo();
        $objEmailSistemaDTO->setStrIdEmailSistemaModulo('MD_PET_INTIMACAO_QUE_EXIGE_RESPOSTA');

        $objEmailSistemaRN = new EmailSistemaRN();
        $objEmailSistemaDTO = $objEmailSistemaRN->consultar($objEmailSistemaDTO);

        //variaveis basicas em uso no email
        $linkLoginUsuarioExterno = SessaoSEIExterna::getInstance()->getStrPaginaLogin() . '&id_orgao_acesso_externo=0';

        $strDe = $objEmailSistemaDTO->getStrDe();
        $strDe = str_replace('@sigla_sistema@', SessaoSEI::getInstance()->getStrSiglaSistema(), $strDe);
        $strDe = str_replace('@email_sistema@', $objInfraParametro->getValor('SEI_EMAIL_SISTEMA'), $strDe);

        $strPara = $objEmailSistemaDTO->getStrPara();
        $strPara = str_replace('@email_usuario_externo@', $arrDadosEmail['dadosUsuario']['email'], $strPara);//email usuario

        $strAssunto = $objEmailSistemaDTO->getStrAssunto();
        $strAssunto = str_replace('@processo@', $arrDadosEmail['dadosUsuario']['processo'], $strAssunto);//sistema

        $strConteudo = $objEmailSistemaDTO->getStrConteudo();
        $strConteudo = str_replace('@nome_usuario_externo@', $arrDadosEmail['dadosUsuario']['nome'], $strConteudo);

        $strConteudo = str_replace('@sigla_orgao@', $arrDadosEmail['objUnidadeDTO']->getStrSiglaOrgao(), $strConteudo);
        $strConteudo = str_replace('@tipo_intimacao@', $arrDadosEmail['objMdPetIntTipoIntimacaoDTO']->getStrNome(), $strConteudo);
        $strConteudo = str_replace('@processo@', $arrDadosEmail['dadosUsuario']['processo'], $strConteudo);
        $strConteudo = str_replace('@documento_principal_intimacao@', $arrDadosEmail['objDocumentoDTO']->getStrProtocoloDocumentoFormatado(), $strConteudo);
        $strConteudo = str_replace('@tipo_documento_principal_intimacao@', DocumentoINT::formatarIdentificacao($arrDadosEmail['objDocumentoDTO']), $strConteudo);

        $strConteudo = str_replace('@tipo_resposta@', $arrDadosEmail['objMdPetIntTipoRespDTO']->getStrNome(), $strConteudo);
        $prazo = $arrDadosEmail['objMdPetIntTipoRespDTO']->getNumValorPrazoExterno();
        if ($arrDadosEmail['objMdPetIntTipoRespDTO']->getStrTipoPrazoExterno() == 'D') {
            $prazo .= $arrDadosEmail['objMdPetIntTipoRespDTO']->getNumValorPrazoExterno() > 1 ? ' Dias' : ' Dia';
        } else if ($arrDadosEmail['objMdPetIntTipoRespDTO']->getStrTipoPrazoExterno() == 'M') {
            $prazo .= $arrDadosEmail['objMdPetIntTipoRespDTO']->getNumValorPrazoExterno() > 1 ? ' Meses' : ' Ms';
        } else if ($arrDadosEmail['objMdPetIntTipoRespDTO']->getStrTipoPrazoExterno() == 'A') {
            $prazo .= $arrDadosEmail['objMdPetIntTipoRespDTO']->getNumValorPrazoExterno() > 1 ? ' Anos' : ' Ano';
        }
        $strConteudo = str_replace('@prazo_externo_tipo_resposta@', $prazo, $strConteudo);
        $strConteudo = str_replace('@link_login_usuario_externo@', $linkLoginUsuarioExterno, $strConteudo);
        $strConteudo = str_replace('@prazo_intimacao_tacita@', $arrDadosEmail['prazoTacita'], $strConteudo);
        $strConteudo = str_replace('@data_expedicao_intimacao@', $arrDadosEmail['dadosUsuario']['dataHora'], $strConteudo);
        $strConteudo = str_replace('@data_final_prazo_intimacao_tacita@', $arrDadosEmail['dataFinalPrazoTacita'], $strConteudo);
        $strConteudo = str_replace('@descricao_orgao@', $arrDadosEmail['objUnidadeDTO']->getStrDescricaoOrgao(), $strConteudo);
        $strConteudo = str_replace('@sitio_internet_orgao@', $arrDadosEmail['objUnidadeDTO']->getStrSitioInternetOrgaoContato(), $strConteudo);

        $objEmailDTO = new EmailDTO();
        $objEmailDTO->setStrDe($strDe);
        $objEmailDTO->setStrPara($strPara);
        $objEmailDTO->setStrAssunto($strAssunto);
        $objEmailDTO->setStrMensagem($strConteudo);

        EmailRN::processar(array($objEmailDTO));
    }


    //Juridico
    public function emailExigeRespostaJuridico($arrDadosEmail)
    {
        //Enviar Email
        $objInfraParametro = new InfraParametro(BancoSEI::getInstance());
        $objEmailSistemaDTO = new EmailSistemaDTO();
        $objEmailSistemaDTO->retStrDe();
        $objEmailSistemaDTO->retStrPara();
        $objEmailSistemaDTO->retStrAssunto();
        $objEmailSistemaDTO->retStrConteudo();
        $objEmailSistemaDTO->setStrIdEmailSistemaModulo('MD_PET_INTIMACAO_QUE_EXIGE_RESPOSTA_J');

        $objEmailSistemaRN = new EmailSistemaRN();
        $objEmailSistemaDTO = $objEmailSistemaRN->consultar($objEmailSistemaDTO);

        //variaveis basicas em uso no email
        $linkLoginUsuarioExterno = SessaoSEIExterna::getInstance()->getStrPaginaLogin() . '&id_orgao_acesso_externo=0';

        $strDe = $objEmailSistemaDTO->getStrDe();
        $strDe = str_replace('@sigla_sistema@', SessaoSEI::getInstance()->getStrSiglaSistema(), $strDe);
        $strDe = str_replace('@email_sistema@', $objInfraParametro->getValor('SEI_EMAIL_SISTEMA'), $strDe);

        $strPara = $objEmailSistemaDTO->getStrPara();
        $strPara = str_replace('@email_usuario_externo@', $arrDadosEmail['dadosUsuario']['email'], $strPara);//email usuario

        $strAssunto = $objEmailSistemaDTO->getStrAssunto();
        $strAssunto = str_replace('@processo@', $arrDadosEmail['dadosUsuario']['processo'], $strAssunto);//sistema

        $strConteudo = $objEmailSistemaDTO->getStrConteudo();
        $strConteudo = str_replace('@nome_usuario_externo@', $arrDadosEmail['dadosUsuario']['nome'], $strConteudo);

        $strConteudo = str_replace('@sigla_orgao@', $arrDadosEmail['objUnidadeDTO']->getStrSiglaOrgao(), $strConteudo);
        $strConteudo = str_replace('@tipo_intimacao@', $arrDadosEmail['objMdPetIntTipoIntimacaoDTO']->getStrNome(), $strConteudo);
        $strConteudo = str_replace('@processo@', $arrDadosEmail['dadosUsuario']['processo'], $strConteudo);
        $strConteudo = str_replace('@documento_principal_intimacao@', $arrDadosEmail['objDocumentoDTO']->getStrProtocoloDocumentoFormatado(), $strConteudo);
        $strConteudo = str_replace('@tipo_documento_principal_intimacao@', DocumentoINT::formatarIdentificacao($arrDadosEmail['objDocumentoDTO']), $strConteudo);

        //Campo Juridico
        $strConteudo = str_replace('@razao_social@', $arrDadosEmail['dadosUsuario']['razaoSocial'], $strConteudo);
        $strConteudo = str_replace('@cnpj@', InfraUtil::formatarCnpj($arrDadosEmail['dadosUsuario']['cnpj']), $strConteudo);
        $strConteudo = str_replace('@tipo_vinculo@', $arrDadosEmail['dadosUsuario']['tpVinc'], $strConteudo);

        $strConteudo = str_replace('@tipo_resposta@', $arrDadosEmail['objMdPetIntTipoRespDTO']->getStrNome(), $strConteudo);
        $prazo = $arrDadosEmail['objMdPetIntTipoRespDTO']->getNumValorPrazoExterno();
        if ($arrDadosEmail['objMdPetIntTipoRespDTO']->getStrTipoPrazoExterno() == 'D') {
            $prazo .= $arrDadosEmail['objMdPetIntTipoRespDTO']->getNumValorPrazoExterno() > 1 ? ' Dias' : ' Dia';
        } else if ($arrDadosEmail['objMdPetIntTipoRespDTO']->getStrTipoPrazoExterno() == 'M') {
            $prazo .= $arrDadosEmail['objMdPetIntTipoRespDTO']->getNumValorPrazoExterno() > 1 ? ' Meses' : ' Ms';
        } else if ($arrDadosEmail['objMdPetIntTipoRespDTO']->getStrTipoPrazoExterno() == 'A') {
            $prazo .= $arrDadosEmail['objMdPetIntTipoRespDTO']->getNumValorPrazoExterno() > 1 ? ' Anos' : ' Ano';
        }
        $strConteudo = str_replace('@prazo_externo_tipo_resposta@', $prazo, $strConteudo);
        $strConteudo = str_replace('@link_login_usuario_externo@', $linkLoginUsuarioExterno, $strConteudo);
        $strConteudo = str_replace('@prazo_intimacao_tacita@', $arrDadosEmail['prazoTacita'], $strConteudo);
        $strConteudo = str_replace('@data_expedicao_intimacao@', $arrDadosEmail['dadosUsuario']['dataHora'], $strConteudo);
        $strConteudo = str_replace('@data_final_prazo_intimacao_tacita@', $arrDadosEmail['dataFinalPrazoTacita'], $strConteudo);
        $strConteudo = str_replace('@descricao_orgao@', $arrDadosEmail['objUnidadeDTO']->getStrDescricaoOrgao(), $strConteudo);
        $strConteudo = str_replace('@sitio_internet_orgao@', $arrDadosEmail['objUnidadeDTO']->getStrSitioInternetOrgaoContato(), $strConteudo);

        $objEmailDTO = new EmailDTO();
        $objEmailDTO->setStrDe($strDe);
        $objEmailDTO->setStrPara($strPara);
        $objEmailDTO->setStrAssunto($strAssunto);
        $objEmailDTO->setStrMensagem($strConteudo);

        EmailRN::processar(array($objEmailDTO));
    }

    public function emailSemResposta($arrDadosEmail)
    {
        //Enviar Email
        $objInfraParametro = new InfraParametro(BancoSEI::getInstance());
        $objEmailSistemaDTO = new EmailSistemaDTO();
        $objEmailSistemaDTO->retStrDe();
        $objEmailSistemaDTO->retStrPara();
        $objEmailSistemaDTO->retStrAssunto();
        $objEmailSistemaDTO->retStrConteudo();
        $objEmailSistemaDTO->setStrIdEmailSistemaModulo('MD_PET_INTIMACAO_SEM_RESPOSTA');

        $objEmailSistemaRN = new EmailSistemaRN();
        $objEmailSistemaDTO = $objEmailSistemaRN->consultar($objEmailSistemaDTO);

        //variaveis basicas em uso no email
        $linkLoginUsuarioExterno = SessaoSEIExterna::getInstance()->getStrPaginaLogin() . '&id_orgao_acesso_externo=0';

        //Monta Email
        $strDe = $objEmailSistemaDTO->getStrDe();
        $strDe = str_replace('@sigla_sistema@', SessaoSEI::getInstance()->getStrSiglaSistema(), $strDe);
        $strDe = str_replace('@email_sistema@', $objInfraParametro->getValor('SEI_EMAIL_SISTEMA'), $strDe);

        $strPara = $objEmailSistemaDTO->getStrPara();
        $strPara = str_replace('@email_usuario_externo@', $arrDadosEmail['dadosUsuario']['email'], $strPara);//email usuario

        $strAssunto = $objEmailSistemaDTO->getStrAssunto();
        $strAssunto = str_replace('@processo@', $arrDadosEmail['dadosUsuario']['processo'], $strAssunto);//sistema

        $strConteudo = $objEmailSistemaDTO->getStrConteudo();
        $strConteudo = str_replace('@nome_usuario_externo@', $arrDadosEmail['dadosUsuario']['nome'], $strConteudo);

        $strConteudo = str_replace('@sigla_orgao@', $arrDadosEmail['objUnidadeDTO']->getStrSiglaOrgao(), $strConteudo);
        $strConteudo = str_replace('@tipo_intimacao@', $arrDadosEmail['objMdPetIntTipoIntimacaoDTO']->getStrNome(), $strConteudo);
        $strConteudo = str_replace('@processo@', $arrDadosEmail['dadosUsuario']['processo'], $strConteudo);
        $strConteudo = str_replace('@documento_principal_intimacao@', $arrDadosEmail['objDocumentoDTO']->getStrProtocoloDocumentoFormatado(), $strConteudo);
        $strConteudo = str_replace('@tipo_documento_principal_intimacao@', DocumentoINT::formatarIdentificacao($arrDadosEmail['objDocumentoDTO']), $strConteudo);
        $strConteudo = str_replace('@link_login_usuario_externo@', $linkLoginUsuarioExterno, $strConteudo);
        $strConteudo = str_replace('@prazo_intimacao_tacita@', $arrDadosEmail['prazoTacita'], $strConteudo);
        $strConteudo = str_replace('@data_expedicao_intimacao@', $arrDadosEmail['dadosUsuario']['dataHora'], $strConteudo);
        $strConteudo = str_replace('@data_final_prazo_intimacao_tacita@', $arrDadosEmail['dataFinalPrazoTacita'], $strConteudo);
        $strConteudo = str_replace('@descricao_orgao@', $arrDadosEmail['objUnidadeDTO']->getStrDescricaoOrgao(), $strConteudo);
        $strConteudo = str_replace('@sitio_internet_orgao@', $arrDadosEmail['objUnidadeDTO']->getStrSitioInternetOrgaoContato(), $strConteudo);

        $objEmailDTO = new EmailDTO();
        $objEmailDTO->setStrDe($strDe);
        $objEmailDTO->setStrPara($strPara);
        $objEmailDTO->setStrAssunto($strAssunto);
        $objEmailDTO->setStrMensagem($strConteudo);

        EmailRN::processar(array($objEmailDTO));
    }

    //Juridico

    public function emailSemRespostaJuridico($arrDadosEmail)
    {
        //Enviar Email
        $objInfraParametro = new InfraParametro(BancoSEI::getInstance());
        $objEmailSistemaDTO = new EmailSistemaDTO();
        $objEmailSistemaDTO->retStrDe();
        $objEmailSistemaDTO->retStrPara();
        $objEmailSistemaDTO->retStrAssunto();
        $objEmailSistemaDTO->retStrConteudo();
        $objEmailSistemaDTO->setStrIdEmailSistemaModulo('MD_PET_INTIMACAO_SEM_RESPOSTA_J');

        $objEmailSistemaRN = new EmailSistemaRN();
        $objEmailSistemaDTO = $objEmailSistemaRN->consultar($objEmailSistemaDTO);

        //variaveis basicas em uso no email
        $linkLoginUsuarioExterno = SessaoSEIExterna::getInstance()->getStrPaginaLogin() . '&id_orgao_acesso_externo=0';

        //Monta Email
        $strDe = $objEmailSistemaDTO->getStrDe();
        $strDe = str_replace('@sigla_sistema@', SessaoSEI::getInstance()->getStrSiglaSistema(), $strDe);
        $strDe = str_replace('@email_sistema@', $objInfraParametro->getValor('SEI_EMAIL_SISTEMA'), $strDe);

        $strPara = $objEmailSistemaDTO->getStrPara();
        $strPara = str_replace('@email_usuario_externo@', $arrDadosEmail['dadosUsuario']['email'], $strPara);//email usuario

        $strAssunto = $objEmailSistemaDTO->getStrAssunto();
        $strAssunto = str_replace('@processo@', $arrDadosEmail['dadosUsuario']['processo'], $strAssunto);//sistema

        $strConteudo = $objEmailSistemaDTO->getStrConteudo();
        $strConteudo = str_replace('@nome_usuario_externo@', $arrDadosEmail['dadosUsuario']['nome'], $strConteudo);

        $strConteudo = str_replace('@sigla_orgao@', $arrDadosEmail['objUnidadeDTO']->getStrSiglaOrgao(), $strConteudo);
        $strConteudo = str_replace('@tipo_intimacao@', $arrDadosEmail['objMdPetIntTipoIntimacaoDTO']->getStrNome(), $strConteudo);
        $strConteudo = str_replace('@processo@', $arrDadosEmail['dadosUsuario']['processo'], $strConteudo);
        $strConteudo = str_replace('@documento_principal_intimacao@', $arrDadosEmail['objDocumentoDTO']->getStrProtocoloDocumentoFormatado(), $strConteudo);
        $strConteudo = str_replace('@tipo_documento_principal_intimacao@', DocumentoINT::formatarIdentificacao($arrDadosEmail['objDocumentoDTO']), $strConteudo);
        //Campo Juridico
        $strConteudo = str_replace('@razao_social@', $arrDadosEmail['dadosUsuario']['razaoSocial'], $strConteudo);
        $strConteudo = str_replace('@cnpj@', InfraUtil::formatarCnpj($arrDadosEmail['dadosUsuario']['cnpj']), $strConteudo);
        $strConteudo = str_replace('@tipo_vinculo@', $arrDadosEmail['dadosUsuario']['tpVinc'], $strConteudo);

        $strConteudo = str_replace('@link_login_usuario_externo@', $linkLoginUsuarioExterno, $strConteudo);
        $strConteudo = str_replace('@prazo_intimacao_tacita@', $arrDadosEmail['prazoTacita'], $strConteudo);
        $strConteudo = str_replace('@data_expedicao_intimacao@', $arrDadosEmail['dadosUsuario']['dataHora'], $strConteudo);
        $strConteudo = str_replace('@data_final_prazo_intimacao_tacita@', $arrDadosEmail['dataFinalPrazoTacita'], $strConteudo);
        $strConteudo = str_replace('@descricao_orgao@', $arrDadosEmail['objUnidadeDTO']->getStrDescricaoOrgao(), $strConteudo);
        $strConteudo = str_replace('@sitio_internet_orgao@', $arrDadosEmail['objUnidadeDTO']->getStrSitioInternetOrgaoContato(), $strConteudo);

        $objEmailDTO = new EmailDTO();
        $objEmailDTO->setStrDe($strDe);
        $objEmailDTO->setStrPara($strPara);
        $objEmailDTO->setStrAssunto($strAssunto);
        $objEmailDTO->setStrMensagem($strConteudo);

        EmailRN::processar(array($objEmailDTO));
    }

    public function emailReiteracaoExigeResposta($arrDadosEmail, $pessoa)
    {

        //Enviar Email
        $objInfraParametro = new InfraParametro(BancoSEI::getInstance());
        $objEmailSistemaDTO = new EmailSistemaDTO();
        $objEmailSistemaDTO->retStrDe();
        $objEmailSistemaDTO->retStrPara();
        $objEmailSistemaDTO->retStrAssunto();
        $objEmailSistemaDTO->retStrConteudo();
        if ($pessoa == "J") {
            $objEmailSistemaDTO->setStrIdEmailSistemaModulo('MD_PET_REITERACAO_INTIMACAO_QUE_EXIGE_RESPOSTA_J');
        } else {
            $objEmailSistemaDTO->setStrIdEmailSistemaModulo('MD_PET_REITERACAO_INTIMACAO_QUE_EXIGE_RESPOSTA');

        }
        $objEmailSistemaRN = new EmailSistemaRN();
        $objEmailSistemaDTO = $objEmailSistemaRN->consultar($objEmailSistemaDTO);

        $strDe = $objEmailSistemaDTO->getStrDe();

        $strDe = str_replace('@sigla_sistema@', $arrDadosEmail['sigla_sistema'], $strDe);

        $strDe = str_replace('@email_sistema@', $arrDadosEmail['email_sistema'], $strDe);


        $strPara = $objEmailSistemaDTO->getStrPara();
        $strPara = str_replace('@email_usuario_externo@', $arrDadosEmail['email_usuario_externo'], $strPara);//email usuario


        $strAssunto = $objEmailSistemaDTO->getStrAssunto();
        $strAssunto = str_replace('@processo@', $arrDadosEmail['processo'], $strAssunto);//sistema


        $strConteudo = $objEmailSistemaDTO->getStrConteudo();
        $strConteudo = str_replace('@processo@', $arrDadosEmail['processo'], $strConteudo);
        $strConteudo = str_replace('@tipo_processo@', $arrDadosEmail['tipo_processo'], $strConteudo);
        $strConteudo = str_replace('@nome_usuario_externo@', $arrDadosEmail['nome_usuario_externo'], $strConteudo);
        $strConteudo = str_replace('@email_usuario_externo@', $arrDadosEmail['email_usuario_externo'], $strConteudo);

        //variaveis basicas em uso no email
        $strConteudo = str_replace('@link_login_usuario_externo@', $arrDadosEmail['link_login_usuario_externo'], $strConteudo);
        $strConteudo = str_replace('@tipo_intimacao@', $arrDadosEmail['tipo_intimacao'], $strConteudo);
        $strConteudo = str_replace('@data_expedicao_intimacao@', $arrDadosEmail['data_expedicao_intimacao'], $strConteudo);
        $strConteudo = str_replace('@prazo_intimacao_tacita@', $arrDadosEmail['prazo_intimacao_tacita'], $strConteudo);
        $strConteudo = str_replace('@data_final_prazo_intimacao_tacita@', $arrDadosEmail['data_final_prazo_intimacao_tacita'], $strConteudo);
        $strConteudo = str_replace('@documento_principal_intimacao@', $arrDadosEmail['documento_principal_intimacao'], $strConteudo);
        $strConteudo = str_replace('@tipo_documento_principal_intimacao@', /*DocumentoINT::formatarIdentificacao(*/ $arrDadosEmail['tipo_documento_principal_intimacao']/*)*/, $strConteudo);

        //Campo Juridico

        //If com juridico

        if ($pessoa == "J") {

            $strConteudo = str_replace('@razao_social@', $arrDadosEmail['razaoSocial'], $strConteudo);
            $strConteudo = str_replace('@cnpj@', InfraUtil::formatarCnpj($arrDadosEmail['cnpj']), $strConteudo);
            $strConteudo = str_replace('@tipo_vinculo@', $arrDadosEmail['tpVinc'], $strConteudo);

        }

        $strConteudo = str_replace('@tipo_resposta@', $arrDadosEmail['tipo_resposta'], $strConteudo);

        $prazo = $arrDadosEmail['prazo_externo_tipo_resposta'];
        if ($arrDadosEmail['tipo_prazo_externo_tipo_resposta'] == 'D') {
            $prazo .= $prazo > 1 ? ' dias' : ' dia';
        } else if ($arrDadosEmail['tipo_prazo_externo_tipo_resposta'] == 'M') {
            $prazo .= $prazo > 1 ? ' meses' : ' m�s';
        } else if ($arrDadosEmail['tipo_prazo_externo_tipo_resposta'] == 'A') {
            $prazo .= $prazo > 1 ? ' anos' : ' ano';
        }
        $strConteudo = str_replace('@prazo_externo_tipo_resposta@', $prazo, $strConteudo);

        $strConteudo = str_replace('@data_cumprimento_intimacao@', $arrDadosEmail['data_cumprimento_intimacao'], $strConteudo);

        $strConteudo = str_replace('@sigla_orgao@', $arrDadosEmail['sigla_orgao'], $strConteudo);
        $strConteudo = str_replace('@descricao_orgao@', $arrDadosEmail['descricao_orgao'], $strConteudo);
        $strConteudo = str_replace('@sitio_internet_orgao@', $arrDadosEmail['sitio_internet_orgao'], $strConteudo);

        $objEmailDTO = new EmailDTO();
        $objEmailDTO->setStrDe($strDe);
        $objEmailDTO->setStrPara($strPara);
        $objEmailDTO->setStrAssunto($strAssunto);
        $objEmailDTO->setStrMensagem($strConteudo);
        EmailRN::processar(array($objEmailDTO));
    }

    public function emailVincSuspensaoRestabelecimento($arrDadosEmail)
    {

        $emailSistemaModulo = $arrDadosEmail['tipo_operacao'] == 'S' ? 'MD_PET_VINC_SUSPENSAO' : 'MD_PET_VINC_RESTABELECIMENTO';

        //Enviar Email
        $objEmailSistemaDTO = new EmailSistemaDTO();
        $objEmailSistemaDTO->retStrDe();
        $objEmailSistemaDTO->retStrPara();
        $objEmailSistemaDTO->retStrAssunto();
        $objEmailSistemaDTO->retStrConteudo();
        $objEmailSistemaDTO->setStrIdEmailSistemaModulo($emailSistemaModulo);

        $objEmailSistemaRN = new EmailSistemaRN();
        $objEmailSistemaDTO = $objEmailSistemaRN->consultar($objEmailSistemaDTO);

        if (is_null($objEmailSistemaDTO)) {
            throw new InfraException('Tipo de email '.$emailSistemaModulo.' n�o encontrado');
        }

        //Monta Email
        $strDe = $objEmailSistemaDTO->getStrDe();
        $strDe = str_replace('@sigla_sistema@', $arrDadosEmail['sigla_sistema'], $strDe);
        $strDe = str_replace('@email_sistema@', $arrDadosEmail['email_sistema'], $strDe);

        $strPara = $objEmailSistemaDTO->getStrPara();
        $strPara = str_replace('@email_usuario_externo@', $arrDadosEmail['dadosUsuario']['email'], $strPara);//email usuario

        $strAssunto = $objEmailSistemaDTO->getStrAssunto();
        $strAssunto = str_replace('@processo@', $arrDadosEmail['dadosUsuario']['processo'], $strAssunto);//sistema

        $strConteudo = $objEmailSistemaDTO->getStrConteudo();

        $strConteudo = str_replace('@nome_usuario_externo@', $arrDadosEmail['dadosUsuario']['nome'], $strConteudo);
        $strConteudo = str_replace('@sigla_orgao@', $arrDadosEmail['sigla_orgao'], $strConteudo);
        $strConteudo = str_replace('@descricao_orgao@', $arrDadosEmail['descricao_orgao'], $strConteudo);
        $strConteudo = str_replace('@sitio_internet_orgao@', $arrDadosEmail['sitio_internet_orgao'], $strConteudo);
        $strConteudo = str_replace('@razao_social@', $arrDadosEmail['dadosUsuario']['razao_social'], $strConteudo);
        $strConteudo = str_replace('@cnpj@', InfraUtil::formatarCnpj($arrDadosEmail['dadosUsuario']['cnpj']), $strConteudo);
        if($arrDadosEmail['tipo_operacao'] == 'S'){
            $strConteudo = str_replace('@documento_suspensao_responsavel_pj@', $arrDadosEmail['dadosUsuario']['documento_suspensao_responsavel_pj'], $strConteudo);
        }else{
            $strConteudo = str_replace('@documento_restabelecimento_responsavel_pj@', $arrDadosEmail['dadosUsuario']['documento_restabelecimento_responsavel_pj'], $strConteudo);
        }
        $strConteudo = str_replace('@item_lista_procuradores@', $arrDadosEmail['item_lista_procuradores'], $strConteudo);

        $objEmailDTO = new EmailDTO();
        $objEmailDTO->setStrDe($strDe);
        $objEmailDTO->setStrPara($strPara);
//        $objEmailDTO->setStrCCO((new InfraParametro(BancoSEI::getInstance()))->getValor('SEI_EMAIL_ADMINISTRADOR'));
        $objEmailDTO->setStrAssunto($strAssunto);
        $objEmailDTO->setStrMensagem($strConteudo);
        EmailRN::processar(array($objEmailDTO));
        
    }

    public function emailProcuracaoSuspensaoRestabelecimento($arrDadosEmail)
    {

        $emailSistemaModulo = $arrDadosEmail['tipo_operacao'] == 'S' ? 'MD_PET_PROCURACAO_SUSPENSAO' : 'MD_PET_PROCURACAO_RESTABELECIMENTO';

        // Pega o template do email
        $objEmailSistemaDTO = new EmailSistemaDTO();
        $objEmailSistemaDTO->retStrDe();
        $objEmailSistemaDTO->retStrPara();
        $objEmailSistemaDTO->retStrAssunto();
        $objEmailSistemaDTO->retStrConteudo();
        $objEmailSistemaDTO->setStrIdEmailSistemaModulo($emailSistemaModulo);
        $objEmailSistemaDTO = (new EmailSistemaRN())->consultar($objEmailSistemaDTO);

        if (is_null($objEmailSistemaDTO)) {
            throw new InfraException('Tipo de email '.$emailSistemaModulo.' n�o encontrado');
        }

        // Monta Email
        $strDe = $objEmailSistemaDTO->getStrDe();
        $strDe = str_replace('@sigla_sistema@', $arrDadosEmail['sigla_sistema'], $strDe);
        $strDe = str_replace('@email_sistema@', $arrDadosEmail['email_sistema'], $strDe);

        $strPara = $objEmailSistemaDTO->getStrPara();
        $strPara = str_replace('@email_usuario_externo@', $arrDadosEmail['dadosUsuario']['outorgado_email'], $strPara);//email usuario

        $strAssunto = $objEmailSistemaDTO->getStrAssunto();
        $strAssunto = str_replace('@processo@', $arrDadosEmail['dadosUsuario']['processo'], $strAssunto);//sistema

        $strConteudo = $objEmailSistemaDTO->getStrConteudo();
        $strConteudo = str_replace('@nome_usuario_externo@', $arrDadosEmail['dadosUsuario']['outorgado_nome'], $strConteudo);
        $strConteudo = str_replace('@sigla_orgao@', $arrDadosEmail['sigla_orgao'], $strConteudo);
        $strConteudo = str_replace('@descricao_orgao@', $arrDadosEmail['descricao_orgao'], $strConteudo);
        $strConteudo = str_replace('@sitio_internet_orgao@', $arrDadosEmail['sitio_internet_orgao'], $strConteudo);
        $strConteudo = str_replace('@outorgante_tipo_pessoa@', $arrDadosEmail['dadosUsuario']['outorgante_tipo_pessoa'], $strConteudo);
        $strConteudo = str_replace('@outorgante_nome@', $arrDadosEmail['dadosUsuario']['outorgante_nome'], $strConteudo);
        $strConteudo = str_replace('@procuracao_tipo@', $arrDadosEmail['dadosUsuario']['procuracao_tipo'], $strConteudo);
        $strConteudo = str_replace('@procuracao_doc_num@', $arrDadosEmail['dadosUsuario']['procuracao_doc_num'], $strConteudo);

        if($arrDadosEmail['tipo_operacao'] == 'S'){
            $strConteudo = str_replace('@documento_suspensao_procuracao@', $arrDadosEmail['dadosUsuario']['documento_suspensao_procuracao'], $strConteudo);
        }else{
            $strConteudo = str_replace('@paragrafo_comunicado@', $arrDadosEmail['dadosUsuario']['paragrafo_comunicado'], $strConteudo);
            $strConteudo = str_replace('@documento_restabelecimento_procuracao@', $arrDadosEmail['dadosUsuario']['documento_restabelecimento_procuracao'], $strConteudo);
        }
        
        $objEmailDTO = new EmailDTO();
        $objEmailDTO->setStrDe($strDe);
        $objEmailDTO->setStrPara($strPara);
//        $objEmailDTO->setStrCCO((new InfraParametro(BancoSEI::getInstance()))->getValor('SEI_EMAIL_ADMINISTRADOR'));
        $objEmailDTO->setStrAssunto($strAssunto);
        $objEmailDTO->setStrMensagem($strConteudo);
        EmailRN::processar(array($objEmailDTO));

    }

    public function enviarEmailProcuradorPf($idContato, $destinatario, $idIntimacao, $params, $arrDadosEmail)
    {
        $qtdEnviadas = 0;
        $qtdNaoEnviadas = 0;
        $arrDadosEmailNaoEnviados = array();
        $objMdPetIntimacaoRN = new MdPetIntimacaoRN();

        //Recuperando procurador da PF
        $dtoMdPetVincReptDTO = new MdPetVincRepresentantDTO();
        $dtoMdPetVincReptDTO->setNumIdContatoVinc($idContato);
        $dtoMdPetVincReptDTO->retNumIdContatoVinc();
        $dtoMdPetVincReptDTO->retStrNomeProcurador();
        $dtoMdPetVincReptDTO->retStrRazaoSocialNomeVinc();
        $dtoMdPetVincReptDTO->retStrTipoRepresentante();
        $dtoMdPetVincReptDTO->retStrCNPJ();
        $dtoMdPetVincReptDTO->retStrEmail();
        $dtoMdPetVincReptDTO->retNumIdContatoProcurador();
        $dtoMdPetVincReptDTO->retNumIdMdPetVinculoRepresent();
        $dtoMdPetVincReptDTO->setStrStaEstado(MdPetVincRepresentantRN::$RP_ATIVO);
        $rnMdPetVincRepRN = new MdPetVincRepresentantRN();
        $arrMdPetVincRepRN = $rnMdPetVincRepRN->listar($dtoMdPetVincReptDTO);

        foreach ($arrMdPetVincRepRN as $key => $value) {

            $temPoder = $this->verificaPoder($value->getStrTipoRepresentante(), $value->getNumIdMdPetVinculoRepresent());

            if ($temPoder) {
                //contato
                $objUsuarioDTO = new UsuarioDTO();
                $objUsuarioDTO->retNumIdUsuario();
                $objUsuarioDTO->retStrSigla();
                $objUsuarioDTO->retStrNome();
                $objUsuarioDTO->retStrStaTipo();
                $objUsuarioDTO->retNumIdContato();
                $objUsuarioDTO->setNumIdContato($value->getNumIdContatoProcurador());

                $objUsuarioRN = new UsuarioRN();
                $objUsuarioDTO = $objUsuarioRN->consultarRN0489($objUsuarioDTO);

                if (is_object($objUsuarioDTO)) {
                    $enviaEmail = true;
                    $conta = "^[a-zA-Z0-9\._-]+@";
                    $domino = "[a-zA-Z0-9\._-]+.";
                    $extensao = "([a-zA-Z]{2,4})$";
                    $pattern = "#" . $conta . $domino . $extensao . "#";
                    $isEmail = preg_match($pattern, $objUsuarioDTO->getStrSigla());
                    if (!$isEmail) {
                        $enviaEmail = false;
                    }
                    $arrDadosEmail['email_usuario_externo'] = $objUsuarioDTO->getStrSigla();
                    $arrDadosEmail['nome_usuario_externo'] = $objUsuarioDTO->getStrNome();

                    $arrDadosEmail['tipo_intimacao'] = $destinatario->getStrNomeTipoIntimacao();

                    //Get Prazo T�cito
                    $objPrazoTacitoDTO = new MdPetIntPrazoTacitaDTO();
                    $objPrazoTacitoDTO->retNumNumPrazo();
                    $objPrazoTacitoRN = new MdPetIntPrazoTacitaRN();
                    $retLista = $objPrazoTacitoRN->listar($objPrazoTacitoDTO);
                    $objPrazoTacitoDTO = !is_null($retLista) && count($retLista) > 0 ? current($retLista) : null;
                    $arrDadosEmail['prazo_intimacao_tacita'] = !is_null($objPrazoTacitoDTO) ? $objPrazoTacitoDTO->getNumNumPrazo() : null;

                    $dtIntimacao = !is_null($destinatario->getDthDataCadastro()) ? explode(' ', $destinatario->getDthDataCadastro()) : null;

                    //Data Expedi��o Intima��o
                    $arrDadosEmail['data_expedicao_intimacao'] = count($dtIntimacao) > 0 ? $dtIntimacao[0] : null;

                    //Calcular Data Final do Prazo T�cito
                    $dataFimPrazoTacito = '';
                    $objMdPetIntPrazoRN = new MdPetIntPrazoRN();
                    $arrDadosEmail['data_final_prazo_intimacao_tacita'] = $objMdPetIntPrazoRN->calcularDataPrazo($arrDadosEmail['prazo_intimacao_tacita'], $arrDadosEmail['data_expedicao_intimacao']);

                    //Documento Principal
                    $dados = $objMdPetIntimacaoRN->retornaDadosDocPrincipalIntimacao(array($idIntimacao));

                    $objDocumentoDTO = new DocumentoDTO();
                    $objDocumentoDTO->retDblIdDocumento();
                    $objDocumentoDTO->retNumIdOrgaoUnidadeResponsavel();
                    $objDocumentoDTO->retStrProtocoloDocumentoFormatado();
                    $objDocumentoDTO->retStrNomeSerie();
                    $objDocumentoDTO->retStrNumero();
                    $objDocumentoDTO->retNumIdSerie();
                    $objDocumentoDTO->retStrProtocoloProcedimentoFormatado();
                    $objDocumentoDTO->retStrNomeTipoProcedimentoProcedimento();

                    $objDocumentoDTO->setDblIdDocumento($dados[3]);
                    $objDocumentoRN = new DocumentoRN();
                    $objDocumentoDTO = $objDocumentoRN->consultarRN0005($objDocumentoDTO);

                    $arrDadosEmail['documento_principal_intimacao'] = $dados[0];
                    $arrDadosEmail['tipo_documento_principal_intimacao'] = $dados[1];
                    if (!empty($dados[4])) {
                        $arrDadosEmail['tipo_documento_principal_intimacao'] .= ' ' . $dados[4];
                    }

                    $arrDadosEmail['processo'] = $objDocumentoDTO->getStrProtocoloProcedimentoFormatado();
                    $arrDadosEmail['tipo_processo'] = $objDocumentoDTO->getStrNomeTipoProcedimentoProcedimento();
                    if ($isEmail) {
                        $this->emailReiteracaoExigeResposta($arrDadosEmail, $params);
                        $qtdEnviadas++;
                    } else {
                        $qtdNaoEnviadas++;
                        $arrDadosEmailNaoEnviados[] = $arrDadosEmail;
                    }
                }
            }
        }
        return array('qtdEnviadas' => $qtdEnviadas, 'qtdN�oEnviadas' => $qtdNaoEnviadas, 'arrDadosEmailNaoEnviados' => $arrDadosEmailNaoEnviados);
    }

    public function verificaPoder($tipoRepresentante, $numIdVincRepresent)
    {
        $temPoder = true;
        //Caso o tipo de procura��o seja "Simples" ser� necess�rio
        //fazer algumas valida��es para cria��o dos destinat�rios externos da intima��o
        if ($tipoRepresentante == MdPetVincRepresentantRN::$PE_PROCURADOR_SIMPLES) {
            $dtoMdPetRelVincRepTpPoderDTO = new MdPetRelVincRepTpPoderDTO();
            $rnMdPetRelVincRepTpPoderRN = new MdPetRelVincRepTpPoderRN();
            $dtoMdPetRelVincRepTpPoderDTO->setNumIdVinculoRepresent($numIdVincRepresent);
            $dtoMdPetRelVincRepTpPoderDTO->setNumIdTipoPoderLegal(1);
            $dtoMdPetRelVincRepTpPoderDTO->retNumIdVinculoRepresent();
            $arrObjMdPetRelVincRepTpPoderDTO = $rnMdPetRelVincRepTpPoderRN->listar($dtoMdPetRelVincRepTpPoderDTO);

            //Verifica se o usu�rio em quest�o possui o poder "Recebimento e Cumprimento de Intima��o Eletr�nica"
            if (!count($arrObjMdPetRelVincRepTpPoderDTO)) {
                $temPoder = false;
            }
        }

        return $temPoder;
    }
}