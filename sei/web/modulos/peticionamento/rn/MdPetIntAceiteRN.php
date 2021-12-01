<?

/**
 * TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
 *
 * 10/03/2017 - criado por jaqueline.mendes
 *
 * Versão do Gerador de Código: 1.40.0
 */
require_once dirname(__FILE__) . '/../../../SEI.php';

class MdPetIntAceiteRN extends InfraRN
{

    //Id Tarefa Módulo
    public static $ID_TAREFA_ACEITE = 'MD_PET_INTIMACAO_CUMPRIDA';

    public function __construct()
    {
        parent::__construct();
    }

    protected function inicializarObjInfraIBanco()
    {
        return BancoSEI::getInstance();
    }

    protected function cadastrarControlado(MdPetIntAceiteDTO $objMdPetIntAceiteDTO)
    {
        try {

            //Valida Permissao
            SessaoSEI::getInstance()->validarPermissao('md_pet_int_aceite_cadastrar');

            //Regras de Negocio
            $objInfraException = new InfraException();


            $objInfraException->lancarValidacoes();

            $objMdPetIntAceiteBD = new MdPetIntAceiteBD($this->getObjInfraIBanco());
            $ret = $objMdPetIntAceiteBD->cadastrar($objMdPetIntAceiteDTO);
            //Auditoria

            return $ret;
        } catch (Exception $e) {
            throw new InfraException('Erro cadastrando aceite.', $e);
        }
    }

    protected function alterarControlado(MdPetIntAceiteDTO $objMdPetIntAceiteDTO)
    {
        try {

            //Valida Permissao
            SessaoSEI::getInstance()->validarPermissao('md_pet_int_aceite_alterar');

            //Regras de Negocio
            $objMdPetIntAceiteBD = new MdPetIntAceiteBD($this->getObjInfraIBanco());
            $objMdPetIntAceiteBD->alterar($objMdPetIntAceiteDTO);

            //Auditoria
        } catch (Exception $e) {
            throw new InfraException('Erro alterando aceite.', $e);
        }
    }

    protected function excluirControlado($arrObjMdPetIntAceiteDTO)
    {
        try {

            //Valida Permissao
            SessaoSEI::getInstance()->validarPermissao('md_pet_int_aceite_excluir');

            //Regras de Negocio
            //$objInfraException = new InfraException();
            //$objInfraException->lancarValidacoes();

            $objMdPetIntAceiteBD = new MdPetIntAceiteBD($this->getObjInfraIBanco());
            for ($i = 0; $i < count($arrObjMdPetIntAceiteDTO); $i++) {
                $objMdPetIntAceiteBD->excluir($arrObjMdPetIntAceiteDTO[$i]);
            }

            //Auditoria
        } catch (Exception $e) {
            throw new InfraException('Erro excluindo aceite.', $e);
        }
    }

    protected function consultarConectado(MdPetIntAceiteDTO $objMdPetIntAceiteDTO)
    {
        try {

            //Valida Permissao
            SessaoSEI::getInstance()->validarPermissao('md_pet_int_aceite_consultar');

            //Regras de Negocio
            //$objInfraException = new InfraException();
            //$objInfraException->lancarValidacoes();

            $objMdPetIntAceiteBD = new MdPetIntAceiteBD($this->getObjInfraIBanco());
            $ret = $objMdPetIntAceiteBD->consultar($objMdPetIntAceiteDTO);

            //Auditoria

            return $ret;
        } catch (Exception $e) {
            throw new InfraException('Erro consultando aceite.', $e);
        }
    }

    protected function listarConectado(MdPetIntAceiteDTO $objMdPetIntAceiteDTO)
    {
        try {

            //Valida Permissao
            SessaoSEI::getInstance()->validarPermissao('md_pet_int_aceite_listar');

            //Regras de Negocio
            //$objInfraException = new InfraException();
            //$objInfraException->lancarValidacoes();

            $objMdPetIntAceiteBD = new MdPetIntAceiteBD($this->getObjInfraIBanco());
            $ret = $objMdPetIntAceiteBD->listar($objMdPetIntAceiteDTO);

            //Auditoria

            return $ret;
        } catch (Exception $e) {
            throw new InfraException('Erro listando aceites.', $e);
        }
    }

    protected function contarConectado(MdPetIntAceiteDTO $objMdPetIntAceiteDTO)
    {
        try {

            //Valida Permissao
            SessaoSEI::getInstance()->validarPermissao('md_pet_int_aceite_listar');

            //Regras de Negocio
            //$objInfraException = new InfraException();
            //$objInfraException->lancarValidacoes();

            $objMdPetIntAceiteBD = new MdPetIntAceiteBD($this->getObjInfraIBanco());
            $ret = $objMdPetIntAceiteBD->contar($objMdPetIntAceiteDTO);

            //Auditoria

            return $ret;
        } catch (Exception $e) {
            throw new InfraException('Erro contando aceites.', $e);
        }
    }

    // TODO Apagar caso não aparece nenhum estouro de tela.
//    protected function existeAceiteIntimacaoConectado($arr) {
//        $idIntimacao = count($arr) > 0 ? current($arr) : '';
//        $bolRetDados = isset($arr[1]) ? $arr[1] : false;
//
//        //Get Id Contato
//        $objUsuarioDTO = new UsuarioDTO();
//        $objUsuarioRN = new UsuarioRN();
//        $objUsuarioDTO->retNumIdContato();
//        $objUsuarioDTO->setNumIdUsuario(SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno());
//        $objUsuarioDTO = $objUsuarioRN->consultarRN0489($objUsuarioDTO);
//        $idContato = isset($objUsuarioDTO) && !is_null($objUsuarioDTO) ? $objUsuarioDTO->getNumIdContato() : null;
//
//        //Get Pet Rel Destinatario
//        $objMdPetIntDestDTO = new MdPetIntRelDestinatarioDTO();
//        $objMdPetIntDestDTO->setNumIdMdPetIntimacao($idIntimacao);
//        $objMdPetIntDestDTO->setNumIdContatoParticipante($idContato);
//        $objMdPetIntDestDTO->retNumIdMdPetIntRelDestinatario();
//        $objMdPetIntDestDTO->retStrStaSituacaoIntimacao();
//
//        $objMdPetIntDestRN = new MdPetIntRelDestinatarioRN();
//        $retLista = $objMdPetIntDestRN->listar($objMdPetIntDestDTO);
//        $objMdPetIntDestDTO = !is_null($retLista) && count($retLista) > 0 ? current($retLista) : null;
//        $idMdPetIntDest = !is_null($objMdPetIntDestDTO) ? $objMdPetIntDestDTO->getNumIdMdPetIntRelDestinatario() : null;
//
//        $objMdPetIntAceiteDTO = new MdPetIntAceiteDTO();
//        $objMdPetIntAceiteDTO->setNumIdMdPetIntRelDestinatario($idMdPetIntDest);
//        $count = $this->contarConectado($objMdPetIntAceiteDTO);
//        if (!$bolRetDados) {
//            return $count > 0;
//        } else {
//            $countRet = $count > 0;
//            $idAceite = null;
//            $dataAceite = null;
//            if ($countRet) {
//                $objMdPetIntAceiteDTO->retNumIdMdPetIntAceite();
//                $objMdPetIntAceiteDTO->retDthData();
//                $ret = $this->listarConectado($objMdPetIntAceiteDTO);
//                $idAceite = $ret[0]->getNumIdMdPetIntAceite();
//                $dataAceite = $ret[0]->getDthData();
//            }
//
//            return array($countRet, $idAceite, $dataAceite);
//        }
//    }

    protected function existeAceiteIntimacaoAcaoConectado($arr)
    {
        $idIntimacao = count($arr) > 0 ? current($arr) : '';
        $bolRetDados = isset($arr[1]) ? $arr[1] : false;

        //Get Id Contato
        $objUsuarioDTO = new UsuarioDTO();
        $objUsuarioRN = new UsuarioRN();
        $objUsuarioDTO->retNumIdContato();
        $objUsuarioDTO->setNumIdUsuario(SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno());
        $objUsuarioDTO = $objUsuarioRN->consultarRN0489($objUsuarioDTO);
        $idContato = isset($objUsuarioDTO) && !is_null($objUsuarioDTO) ? $objUsuarioDTO->getNumIdContato() : null;

        //Get Pet Rel Destinatario,
        $objMdPetIntDestDTO = new MdPetIntRelDestinatarioDTO();
        if (is_array($idIntimacao)) {
            $objMdPetIntDestDTO->setNumIdMdPetIntimacao($idIntimacao, InfraDTO::$OPER_IN);
        } else {
            $objMdPetIntDestDTO->setNumIdMdPetIntimacao($idIntimacao);
        }
        $objMdPetIntDestDTO->setNumIdContatoParticipante($idContato);
        $objMdPetIntDestDTO->retNumIdMdPetIntRelDestinatario();
        $objMdPetIntDestDTO->retStrStaSituacaoIntimacao();
        $objMdPetIntDestRN = new MdPetIntRelDestinatarioRN();
        $retLista = $objMdPetIntDestRN->listar($objMdPetIntDestDTO);
        $idMdPetIntDest = InfraArray::converterArrInfraDTO($retLista, 'IdMdPetIntRelDestinatario');

        if ($idMdPetIntDest) {
            $objMdPetIntAceiteDTO = new MdPetIntAceiteDTO();
            $objMdPetIntAceiteDTO->setNumIdMdPetIntRelDestinatario($idMdPetIntDest, InfraDTO::$OPER_IN);
            $count = $this->contar($objMdPetIntAceiteDTO);

            if (!$bolRetDados) {
                return $count > 0;
            } else {
                $countRet = $count > 0;
                $idAceite = null;
                $dataAceite = null;
                if ($countRet) {
                    $objMdPetIntAceiteDTO->retDblIdDocumentoCertidao();
                    $objMdPetIntAceiteDTO->retNumIdMdPetIntAceite();
                    $objMdPetIntAceiteDTO->retNumIdMdPetIntRelDestinatario();
                    $objMdPetIntAceiteDTO->retDthData();
                    $ret = $this->listar($objMdPetIntAceiteDTO);
                    if ($ret) {
                        foreach ($ret as $aceite) {
                            $arrDados[] = array('INT' => $count > 0,
                                'ID_DOCUMENTO_CERTIDAO' => $aceite->getDblIdDocumentoCertidao(),
                                'ID_ACEITE' => $aceite->getNumIdMdPetIntAceite(),
                                'ID_DESTINATARIO' => $aceite->getNumIdMdPetIntRelDestinatario(),
                                'DATA_ACEITE' => $aceite->getDthData());
                        }
                    }
                }
                return $arrDados;
            }
        }
    }

    protected function retornaIdUsuarioIdContatoConectado($params)
    {
        $objContatoRN = new ContatoRN();
        $objUsuarioRN = new UsuarioRN();

        $idContato = current($params);
        $objContatoDTO = new ContatoDTO();
        $objContatoDTO->setNumIdContato($idContato);
        $objContatoDTO->retNumIdUsuarioCadastro();
        $objContatoDTO = $objContatoRN->consultarRN0324($objContatoDTO);

        if (is_null($objContatoDTO->getNumIdUsuarioCadastro())) {
            $objUsuarioDTO = new UsuarioDTO();
            $objUsuarioDTO->retNumIdUsuario();
            $objUsuarioDTO->setNumIdContato($idContato);
            $objUsuarioDTO = $objUsuarioRN->consultarRN0489($objUsuarioDTO);

            return $objUsuarioDTO->getNumIdUsuario();
        }

        return $objContatoDTO->getNumIdUsuarioCadastro();
    }

    protected function retornaObjContatoIdUsuarioConectado($params)
    {
        $idUsuario = $params[0];
        $retTodos = isset($params[1]) ? $params[1] : false;
        $objRetorno = null;

        $objUsuarioDTO = new UsuarioDTO();
        $objUsuarioDTO->setNumIdUsuario($idUsuario);
        $objUsuarioDTO->retNumIdContato();
        $objUsuarioRN = new UsuarioRN();
        $ret = $objUsuarioRN->listarRN0490($objUsuarioDTO);
        $objUsuarioDTO = count($ret) > 0 ? current($ret) : null;
        $idContato = !is_null($objUsuarioDTO) ? $objUsuarioDTO->getNumIdContato() : null;

        if ($idContato) {
            $objContatoDTO = new ContatoDTO();
            $objContatoDTO->setNumIdContato($idContato);
            if ($retTodos) {
                $objContatoDTO->retTodos(true);
            } else {
                $objContatoDTO->retNumIdContato();
                $objContatoDTO->retStrNome();
                $objContatoDTO->retStrEmail();
            }

            $objContatoRN = new ContatoRN();
            $count = $objContatoRN->contarRN0327($objContatoDTO);

            if ($count > 0) {
                $arr = $objContatoRN->listarRN0325($objContatoDTO);
                $objRetorno = current($arr);
            }
        }

        return $objRetorno;
    }

    protected function retornaSituacaoIntimacaoConectado($arr)
    {
        $objMdPetIntRelDestRN = new MdPetIntRelDestinatarioRN();
        $idIntimacao = isset($arr[0]) ? $arr[0] : null;
        $IdUsuario = isset($arr[1]) && $arr[1] ? $arr[1] : SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno();
        $verificaResposta = isset($arr[2]) ? $arr[2] : true;
        $idContato = isset($arr[3]) ? $arr[3] : false;
        $objMdPetIntRelDestDTO = isset($arr[4]) ? $arr[4] : null;

        if ($idIntimacao) {
            //Get Id Rel Int x Dest

            if (is_null($objMdPetIntRelDestDTO)) {
                if ($IdUsuario) {
                    $objMdPetIntRelDestDTO = $objMdPetIntRelDestRN->retornaRelIntDestinatario(array($idIntimacao, $IdUsuario));
                } else {
                    $objMdPetIntRelDestDTO = $objMdPetIntRelDestRN->retornaRelIntDestinatario(array($idIntimacao, false, false, $idContato));
                }
            }

            $idMdPetIntRelDest = $objMdPetIntRelDestDTO->getNumIdMdPetIntRelDestinatario();

            if ($verificaResposta) {
                $objMdPetIntDestRespRN = new MdPetIntDestRespostaRN();

                $objMdPetIntDestRespDTO = new MdPetIntDestRespostaDTO();
                $objMdPetIntDestRespDTO->setNumIdMdPetIntRelDestinatario($idMdPetIntRelDest);
                $objMdPetIntDestRespDTO->retNumIdMdPetIntDestResposta();
                $countResp = $objMdPetIntDestRespRN->contar($objMdPetIntDestRespDTO);

                if ($countResp > 0) {
                    return MdPetIntimacaoRN::$INTIMACAO_RESPONDIDA;
                }
            }

            $objMdPetIntAceiteDTO = new MdPetIntAceiteDTO();
            $objMdPetIntAceiteRN = new MdPetIntAceiteRN();

            $objMdPetIntAceiteDTO->setNumIdMdPetIntRelDestinatario($idMdPetIntRelDest);
            $objMdPetIntAceiteDTO->retNumIdMdPetIntAceite();

            $count = $objMdPetIntAceiteRN->contar($objMdPetIntAceiteDTO);

            if ($count > 0) {
                $objMdPetIntAceiteDTO->retStrIp();
                $lista = $objMdPetIntAceiteRN->listar($objMdPetIntAceiteDTO);
                $objAceiteDTO = current($lista);

                $ip = $objAceiteDTO->getStrIp();

                if (is_null($ip) || $ip == '') {
                    return MdPetIntimacaoRN::$INTIMACAO_CUMPRIDA_PRAZO;
                } else {
                    return MdPetIntimacaoRN::$INTIMACAO_CUMPRIDA_POR_ACESSO;
                }
            } else {
                return MdPetIntimacaoRN::$INTIMACAO_PENDENTE;
            }
        }

        return null;
    }

    protected function lancarAndamentoAceiteControlado($arrParametros)
    {
        $objMdIntimacaoRN = new MdPetIntimacaoRN();

        $idProcedimento = $arrParametros[0];
        $dataInt = $arrParametros[1];
        $dataFinal = $arrParametros[2];
        $idIntimacao = $arrParametros[3];
        $idMdPetIntDest = $arrParametros[4];
        $idUsuario = isset($arrParametros[5]) ? $arrParametros[5] : SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno();
        $staConcessao = isset($arrParametros[6]) ? $arrParametros[6] : MdPetIntAcessoExternoDocumentoRN::$STA_INTERNO;
        $objUnidade = isset($arrParametros[7]) ? $arrParametros[7] : $objMdIntimacaoRN->getUnidadeIntimacao(array($idIntimacao));
        $jobManual = isset($arrParametros[8]) ? $arrParametros[8] : null;

        $docPrinc = $objMdIntimacaoRN->retornaDadosDocPrincipalIntimacao(array($idIntimacao));
        if (count($docPrinc) > 0) {
            $idDoc = $docPrinc[3];
            $docForm = $docPrinc[0];
        }

        if ($objUnidade) {
            if ($staConcessao != MdPetIntAcessoExternoDocumentoRN::$STA_INTERNO) {
                SessaoSEI::getInstance()->simularLogin(null, null, $idUsuario, $objUnidade->getNumIdUnidade());
            }

            $tpCumprimento = ($staConcessao == MdPetIntAcessoExternoDocumentoRN::$STA_AGENDAMENTO) ? MdPetIntimacaoRN::$STR_TP_MANUAL_USUARIO_EXTERNO_ACEITE : MdPetIntimacaoRN::$STR_TP_CUMPRIMENTO_LANC_ACESSO_DIRETO;

            // Usuário
            $objMdPetIntRelDestinatarioDTO = new MdPetIntRelDestinatarioDTO();
            $objMdPetIntRelDestinatarioRN = new MdPetIntRelDestinatarioRN();
            $objMdPetIntRelDestinatarioDTO->setNumIdMdPetIntRelDestinatario($idMdPetIntDest);
            $objMdPetIntRelDestinatarioDTO->retNumIdContatoParticipante();
//            $objMdPetIntRelDestinatarioDTO->retStrNomeContato();
            $objMdPetIntRelDestinatarioDTO->retStrNomeContatoParticipante();
            $objMdPetIntRelDestinatarioDTO->setNumMaxRegistrosRetorno(1);

            if ($staConcessao == MdPetIntAcessoExternoDocumentoRN::$STA_EXTERNO) {
                //adaptação para intimação juridica quanto tiver representante legal e procurador
                $objUsuarioDTO = new UsuarioDTO();
                $objUsuarioDTO->setNumIdUsuario($idUsuario);
                $objUsuarioDTO->retNumIdContato();
                $objUsuarioRN = new UsuarioRN();
                $objUsuarioDTO = $objUsuarioRN->consultarRN0489($objUsuarioDTO);
                $idContato = !is_null($objUsuarioDTO) ? $objUsuarioDTO->getNumIdContato() : null;

                $objMdPetIntRelDestinatarioDTO->setNumIdContatoParticipante($idContato);
                $objMdPetIntRelDestinatarioDTO->retNumIdMdPetIntRelDestinatario();
                $objMdPetIntRelDestinatarioDTO = $objMdPetIntRelDestinatarioRN->consultar($objMdPetIntRelDestinatarioDTO);
                $qtdObjMdPetIntRelDestinatarioDTO = is_array($objMdPetIntRelDestinatarioDTO) ? count($objMdPetIntRelDestinatarioDTO) : 0;
                if ($qtdObjMdPetIntRelDestinatarioDTO > 0) {
                    $idContato = $objMdPetIntRelDestinatarioDTO->getNumIdContatoParticipante();
                    $nomeContato = $objMdPetIntRelDestinatarioDTO->getStrNomeContatoParticipante();
                }

                //@todo testar aqui o que o idContato faz
                $idCertidao = $this->getIdCertidaoPorIntimacao(array($idIntimacao, $idContato, $objMdPetIntRelDestinatarioDTO));
            } else {
                $objUsuarioPetRN = new MdPetIntUsuarioRN();
                $objUsuarioPeticionamentoDTO = $objUsuarioPetRN->getObjUsuarioPeticionamento();

                $idContato = $objUsuarioPeticionamentoDTO->getNumIdContato();
                $nomeContato = $objUsuarioPeticionamentoDTO->getStrNome();


                $objMdPetIntAceiteDTO = new MdPetIntAceiteDTO();
                $objMdPetIntAceiteDTO->setNumIdMdPetIntRelDestinatario($idMdPetIntDest);
                $objMdPetIntAceiteDTO->retDblIdDocumentoCertidao();

                $objMdPetIntAceiteRN = new MdPetIntAceiteRN();
                $objMdPetIntAceiteDTO = $objMdPetIntAceiteRN->consultar($objMdPetIntAceiteDTO);
                $idCertidao = $objMdPetIntAceiteDTO->getDblIdDocumentoCertidao();
            }


            if ($idCertidao) {
                $objDocumentoDTO = new DocumentoDTO();
                $objDocumentoRN = new DocumentoRN();
                $objDocumentoDTO->setDblIdDocumento($idCertidao);
                $objDocumentoDTO->retStrProtocoloDocumentoFormatado();
                $objDocumentoDTO = $objDocumentoRN->consultarRN0005($objDocumentoDTO);
                if ($objDocumentoDTO) {
                    $docCertidao = $objDocumentoDTO->getStrProtocoloDocumentoFormatado();
                }
            }

            //@todo retirando esse codigo não faz sentido msm condição da de cima em caso de erro no aceite tacito ou manual analisar esse trecho de código
//            if (count($objMdPetIntRelDestinatarioDTO)>0){
//                $idContato = $objMdPetIntRelDestinatarioDTO->getNumIdContatoParticipante();
//                $nomeContato = $objMdPetIntRelDestinatarioDTO->getStrNomeContatoParticipante();
//            }

            $objEntradaLancarAndamentoAPI = new EntradaLancarAndamentoAPI();
            $objEntradaLancarAndamentoAPI->setIdProcedimento($idProcedimento);
            $objEntradaLancarAndamentoAPI->setIdTarefaModulo(MdPetIntAceiteRN::$ID_TAREFA_ACEITE);

            $arrObjAtributoAndamentoAPI = array();

            $arrObjAtributoAndamentoAPI[] = $this->retornaObjAtributoAndamentoAPI('DATA_CUMPRIMENTO_INTIMACAO', $dataFinal);
            $arrObjAtributoAndamentoAPI[] = $this->retornaObjAtributoAndamentoAPI('TIPO_CUMPRIMENTO_INTIMACAO', $tpCumprimento);
            $arrObjAtributoAndamentoAPI[] = $this->retornaObjAtributoAndamentoAPI('DATA_EXPEDICAO_INTIMACAO', $dataInt);
            $arrObjAtributoAndamentoAPI[] = $this->retornaObjAtributoAndamentoAPI('DOCUMENTO', $docForm, $idDoc);
            $arrObjAtributoAndamentoAPI[] = $this->retornaObjAtributoAndamentoAPI('DOC_CERTIDAO_INTIMACAO', $docCertidao, $idCertidao);
            $arrObjAtributoAndamentoAPI[] = $this->retornaObjAtributoAndamentoAPI('USUARIO_EXTERNO_NOME', $nomeContato, $idContato);

            $objEntradaLancarAndamentoAPI->setAtributos($arrObjAtributoAndamentoAPI);

            // SIGILOSO - conceder credencial
            $objProcedimentoDTO = MdPetIntAceiteRN::_retornaObjProcedimento($idProcedimento);
            if ($objProcedimentoDTO->getStrStaNivelAcessoGlobalProtocolo() == ProtocoloRN::$NA_SIGILOSO || $objProcedimentoDTO->getStrStaNivelAcessoLocalProtocolo() == ProtocoloRN::$NA_SIGILOSO) {
                if (isset($jobManual)) {
                    $objMdPetIntUsuarioRN = new MdPetIntUsuarioRN();
                    $idUsuarioCredencial = $objMdPetIntUsuarioRN->getObjUsuarioPeticionamento(true);
                }
                if (!is_numeric($idUsuarioCredencial)) {
                    $idUsuarioCredencial = is_numeric(SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno()) ? SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno() : null;
                }
                if (is_numeric($idUsuarioCredencial)) {
                    $objMdPetProcedimentoRN = new MdPetProcedimentoRN();
                    $objConcederCredencial = $objMdPetProcedimentoRN->concederCredencial(array($objProcedimentoDTO, $objUnidade->getNumIdUnidade(), null, null, $idUsuarioCredencial));
                }
            }
            // SIGILOSO - conceder credencial - FIM

            $objSeiRN = new SeiRN();
            $objSeiRN->lancarAndamento($objEntradaLancarAndamentoAPI);

            // SIGILOSO - retirando credencial provisória
            if ($objProcedimentoDTO->getStrStaNivelAcessoGlobalProtocolo() == ProtocoloRN::$NA_SIGILOSO || $objProcedimentoDTO->getStrStaNivelAcessoLocalProtocolo() == ProtocoloRN::$NA_SIGILOSO) {
                if (is_numeric($idUsuarioCredencial)) {
                    $objMdPetProcedimentoRN = new MdPetProcedimentoRN();
                    $objCassarCredencial = $objMdPetProcedimentoRN->cassarCredencial($objConcederCredencial);
                    $objMdPetProcedimentoRN->excluirAndamentoCredencial($objConcederCredencial);
                }
            }
            // SIGILOSO - retirando credencial provisória - FIM
        }
    }

    public function retornaObjAtributoAndamentoAPI($nome, $valor, $id = null)
    {
        $objAtributoAndamentoAPI = new AtributoAndamentoAPI();
        $objAtributoAndamentoAPI->setNome($nome);

        $objAtributoAndamentoAPI->setValor($valor);
        $objAtributoAndamentoAPI->setIdOrigem($id); //ID do prédio, pode ser null

        return $objAtributoAndamentoAPI;
    }

    protected function existeAceitePorIntimacoesConectado($dados)
    {
        $arrIdsInt = $dados[0];
        $returnArray = isset($dados[1]) ? $dados[1] : false;

        $objMdPetIntAceiteDTO = new MdPetIntAceiteDTO();
        $objMdPetIntAceiteDTO->setNumIdMdPetIntimacao($arrIdsInt, InfraDTO::$OPER_IN);
        $objMdPetIntAceiteDTO->retNumIdMdPetIntimacao();

        $count = $this->contarConectado($objMdPetIntAceiteDTO);

        if ($count > 0) {
            if ($returnArray) {
                $arrRetorno = array();
                $lista = $this->listarConectado($objMdPetIntAceiteDTO);
                foreach ($lista as $dado) {
                    $arrRetorno[] = $dado->getNumIdMdPetIntimacao();
                }

                return $arrRetorno;
            }
            return $this->listarConectado($objMdPetIntAceiteDTO);
        } else {
            return null;
        }
    }

    protected function retornaDataCumprimentoIntimacaoConectado($idMdPetDest)
    {
        $objMdPetIntAceiteDTO = new MdPetIntAceiteDTO();
        $objMdPetIntAceiteDTO->retDthData();

        if (is_array($idMdPetDest)) {
            $objMdPetIntAceiteDTO->setNumIdMdPetIntRelDestinatario($idMdPetDest, InfraDTO::$OPER_IN);
        } else {
            $objMdPetIntAceiteDTO->setNumIdMdPetIntRelDestinatario($idMdPetDest);
        }

        $objMdPetIntAceiteDTO = $this->listar($objMdPetIntAceiteDTO);
        $objMdPetIntAceiteDTO = current($objMdPetIntAceiteDTO);

        return $objMdPetIntAceiteDTO && $objMdPetIntAceiteDTO->isSetDthData() ? $objMdPetIntAceiteDTO->getDthData() : null;
    }

    protected function verificarIntimacoesPrazoExpiradoConectado()
    {
        $intimacoesPendentes = array();

        $objPrazoTacitoRN = new MdPetIntPrazoTacitaRN();
        $objAceiteRN = new MdPetIntAceiteRN();
        $objIntimacaoRN = new MdPetIntimacaoRN();

        $objPrazoTacitoDTO = new MdPetIntPrazoTacitaDTO();
        $objPrazoTacitoDTO->retNumNumPrazo();

        $count = $objPrazoTacitoRN->contar($objPrazoTacitoDTO);

        if ($count > 0) {
            $objPrazoTacitoDTO = current($objPrazoTacitoRN->listar($objPrazoTacitoDTO));
            $diasExp = $objPrazoTacitoDTO->getNumNumPrazo();
            $intimacoesPendentes = $objIntimacaoRN->retornarDadosIntimacaoPrazoExpirado(array($diasExp));
        }

        return $intimacoesPendentes;
    }

    protected function realizarEtapasAceiteAgendadoControlado($intimacoesPendentes)
    {
        try {
            $registros = count($intimacoesPendentes);

            $objMdPetUsuarioRN = new MdPetIntUsuarioRN();
            $idUsuarioPet = $objMdPetUsuarioRN->getObjUsuarioPeticionamento(true);
            $jobManual = array_key_exists('acao_retorno', $_GET) && $_GET['acao_retorno'] == 'infra_agendamento_tarefa_listar';
            $arrRetornoIntimacoes = array(
                'cumpridas' => 0,
                'naoCumpridas' => 0,
                'procedimentos' => array()
            );
            if ($registros > 0) {
                for ($i = 0; $i < $registros; $i++) {
                    $objUsuarioPetRN = new MdPetIntUsuarioRN();
                    $objMdPetIntimacaoRN = new MdPetIntimacaoRN();
                    $objMdPetCertidaoRN = new MdPetIntCertidaoRN();
                    $objMdPetIntPrazoRN = new MdPetIntPrazoRN();

                    $objDTO = $intimacoesPendentes[$i];

                    $idMdPetIntDest = $objDTO->getNumIdMdPetIntRelDestinatario();
                    $idIntimacao = $objDTO->getNumIdMdPetIntimacao();
                    $dataIntimacao = $objDTO->getDthDataCadastro();
                    $datafinal = $objDTO->getDthDataFinal();

                    //doc principal
                    $dados = $objMdPetIntimacaoRN->retornaDadosDocPrincipalIntimacao(array($idIntimacao));
                    $idProcedimento = $dados[2];
                    $objProcedimentoDTO = $this->_retornaObjProcedimento($idProcedimento);

                    $arrStaEstado = array(
                        ProtocoloRN::$TE_PROCEDIMENTO_SOBRESTADO,
                        ProtocoloRN::$TE_PROCEDIMENTO_BLOQUEADO,
                        ProtocoloRN::$TE_PROCEDIMENTO_ANEXADO
                    );
                    if ($objProcedimentoDTO) {
                        if (in_array($objProcedimentoDTO->getStrStaEstadoProtocolo(), $arrStaEstado)) {
                            switch ($objProcedimentoDTO->getStrStaEstadoProtocolo()) {
                                case ProtocoloRN::$TE_PROCEDIMENTO_SOBRESTADO :
                                    $motivo = 'Processo Sobrestado';
                                    break;
                                case ProtocoloRN::$TE_PROCEDIMENTO_BLOQUEADO :
                                    $motivo = 'Processo Bloqueado';
                                    break;
                                case ProtocoloRN::$TE_PROCEDIMENTO_ANEXADO :
                                    $motivo = 'Processo Anexado';
                                    break;
                            }

                            $arrRetornoIntimacoes['naoCumpridas'] = $arrRetornoIntimacoes['naoCumpridas'] + 1;
                            $arrRetornoIntimacoes['procedimentos'][] = array(
                                $objProcedimentoDTO->getStrProtocoloProcedimentoFormatado(),
                                $motivo
                            );
                        } else {

                            //unidade intimação
                            $objUnidadeDTO = $objMdPetIntimacaoRN->getUnidadeIntimacao(array($idIntimacao));

                            //usuario módulo
                            $idUsuario = $objUsuarioPetRN->getObjUsuarioPeticionamento(true);

                            //parametros
                            $arrParametros = array($idIntimacao, $objUnidadeDTO, $objProcedimentoDTO);

                            //Gerar Aceite
                            $objMdPetIntAceiteDTO = $this->_realizarAceitePorPrazoTacito(array($objDTO, $dados[3]));

                            //Cadastrando Data Limite para Tipo Resposta
                            $arrObjMdPetIntRelTipoRespDestDTO = $objMdPetIntPrazoRN->retornarTipoRespostaDataLimite(array($idIntimacao, $idMdPetIntDest));

                            if (count($arrObjMdPetIntRelTipoRespDestDTO) > 0) {
                                $objMdPetIntRelTipoRespDestRN = new MdPetIntRelTipoRespDestRN();
                                foreach ($arrObjMdPetIntRelTipoRespDestDTO as $objMdPetIntRelTipoRespDestDTO) {
                                    $objMdPetIntRelTipoRespDestRN->cadastrar($objMdPetIntRelTipoRespDestDTO);
                                }
                            }

                            //Unidade Geradora
                            //unidade esta ativa
                            $unidadeDTO = new UnidadeDTO();
                            $unidadeDTO->retTodos();
                            $unidadeDTO->setBolExclusaoLogica(false);
                            $unidadeDTO->setNumIdUnidade($objUnidadeDTO->getNumIdUnidade());
                            $unidadeRN = new UnidadeRN();
                            $objUnidadeDTO = $unidadeRN->consultarRN0125($unidadeDTO);

                            $arrAtividadeDTO = null;
                            if ($objUnidadeDTO->getStrSinAtivo() == 'S') {
                                $arrAtividadeDTO = $objMdPetIntimacaoRN->verificarUnidadeAberta(array($objProcedimentoDTO, $objUnidadeDTO->getNumIdUnidade()));
                            }

                            $idUsuarioAtribuicao = null;
                            if (count($arrAtividadeDTO) == 0) {
                                $idUnidadeAberta = $objMdPetIntimacaoRN->reabrirUnidade(array($objProcedimentoDTO, $objUnidadeDTO->getNumIdUnidade()));
                            } else {
                                $idUnidadeAberta = $arrAtividadeDTO[0]->getNumIdUnidade();
                                if ($arrAtividadeDTO[0]->isSetNumIdUsuarioAtribuicao()) {
                                    $idUsuarioAtribuicao = $arrAtividadeDTO[0]->getNumIdUsuarioAtribuicao();
                                }
                            }
                            if (is_numeric($idUnidadeAberta)) {
                                $arrParametros[1] = $objMdPetIntimacaoRN->retornaObjUnidadePorId($idUnidadeAberta, true);
                            }

                            if ($arrParametros[1]) {
                                //Gerar Certidão
                                $arrParametros[3] = $objMdPetIntAceiteDTO;
                                $arrParametros[4] = $objDTO;
                                $arrParametros[5] = true;
                                $arrParametros[6] = $datafinal;

                                if ($jobManual) {
                                    SessaoSEI::getInstance()->setBolHabilitada(false);
                                    SessaoSEI::getInstance()->simularLogin(null, SessaoSEI::$UNIDADE_TESTE, $idUsuarioPet, null);
                                }

                                $objMdPetCertidaoRN->gerarCertidao($arrParametros);

                                //Usuário do Módulo de Peticionamento
                                $objUsuarioPetRN = new MdPetIntUsuarioRN();
                                $idUsuarioPet = $objUsuarioPetRN->getObjUsuarioPeticionamento(true);

                                $arr = array($idProcedimento, $dataIntimacao, $datafinal, $idIntimacao, $idMdPetIntDest, $idUsuarioPet, MdPetIntAcessoExternoDocumentoRN::$STA_AGENDAMENTO, $arrParametros[1], $jobManual);

                                $this->lancarAndamentoAceite($arr);

                                if ($jobManual) {
                                    SessaoSEI::getInstance()->setBolHabilitada(true);
                                }

                                // REENVIAR ou REENVIAR E REATRIBUIR
                                if (is_numeric($idUnidadeAberta) && is_numeric($idProcedimento)) {
                                    $arrParams = array();
                                    $arrParams[0] = $idUnidadeAberta;
                                    $arrParams[1] = $idProcedimento;

                                    if (!is_null($idUsuarioAtribuicao)) {
                                        $arrParams[2] = $idUsuarioAtribuicao;
                                    }
                                    $objMdPetIntimacaoRN->reenviarReatribuirUnidade($arrParams);
                                }
                                $arrRetornoIntimacoes['cumpridas'] = $arrRetornoIntimacoes['cumpridas'] + 1;
                            } else {
                                //EXCEÇÃO DE UNIDADE
                                $detalhes = "Unidade não definida";
                                throw new InfraException('Erro na definição da Unidade da Consulta Direta', null, $detalhes);
                            }
                        }
                    }else {
                        $arrRetornoIntimacoes['naoCumpridas'] = $arrRetornoIntimacoes['naoCumpridas'] + 1;
                        $arrRetornoIntimacoes['erros'][] = array(
                            $dados[1] . " " . $dados[4] . "(" . $dados[0] .")",
                            "Não retornou nenhum registro na consulta para cumprimento das Intimações."
                        );
                    }
                }
            }
            return $arrRetornoIntimacoes;
        } catch (Exception $e) {
            LogSEI::getInstance()->gravar('MdPetIntAceiteRN->realizarEtapasAceiteAgendado: ' . $e, InfraLog::$INFORMACAO);
            throw new InfraException('Erro cadastrando aceite. MdPetIntAceiteRN->realizarEtapasAceiteAgendado: ', $e);
        }
    }

    private function _gerarInfraLog()
    {
        PaginaSEI::getInstance()->getObjInfraLog()->gravar('Certidão não gerada e andamento não criado no âmbito do Processo "número do processo", tendo em vista que todas as Unidades de tramitação estão desativadas.');
    }

    public function _retornaObjProcedimento($idProcedimento)
    {
        $objProcedimentoRN = new ProcedimentoRN();

        $objProcedimentoDTO = new ProcedimentoDTO();
        $objProcedimentoDTO->setDblIdProcedimento($idProcedimento);
        $objProcedimentoDTO->retDblIdProcedimento();
        $objProcedimentoDTO->retStrStaNivelAcessoGlobalProtocolo();
        $objProcedimentoDTO->retStrStaNivelAcessoLocalProtocolo();
        $objProcedimentoDTO->retStrProtocoloProcedimentoFormatado();
        $objProcedimentoDTO->retStrStaEstadoProtocolo();
        $objProcedimentoDTO = $objProcedimentoRN->consultarRN0201($objProcedimentoDTO);

        return $objProcedimentoDTO;
    }

    protected function _realizarAceitePorPrazoTacitoControlado($arrParams)
    {
        $objMdPetIntRelDestRN = new MdPetIntRelDestinatarioRN();
        $objDTO = $arrParams[0];
        $idDoc = $arrParams[1];
        $objUsuarioPetRN = new MdPetIntUsuarioRN();
        $objUsuarioPetDTO = $objUsuarioPetRN->getObjUsuarioPeticionamento();

        $objMdPetIntAceiteDTO = new MdPetIntAceiteDTO();
        $objMdPetIntAceiteDTO->setStrIp(null);
        $objMdPetIntAceiteDTO->setDthData(InfraData::getStrDataHoraAtual());
        $objMdPetIntAceiteDTO->setNumIdMdPetIntRelDestinatario($objDTO->getNumIdMdPetIntRelDestinatario());
        $objMdPetIntAceiteDTO->setStrTipoAceite(MdPetIntimacaoRN::$TP_AUTOMATICO_POR_DECURSO_DE_PRAZO);
        $objMdPetIntAceiteDTO->setDblIdDocumentoCertidao($idDoc);
        $objMdPetIntAceiteDTO->setNumIdUsuario($objUsuarioPetDTO->getNumIdUsuario());
        $objMdPetIntAceiteDTO = $this->cadastrar($objMdPetIntAceiteDTO);

        $objMdPetIntRelDestRN->atualizarStatusIntimacao(array(MdPetIntimacaoRN::$INTIMACAO_CUMPRIDA_PRAZO, $objDTO->getNumIdMdPetIntRelDestinatario()));

        return $objMdPetIntAceiteDTO;
    }

    public function retornaArraySituacaoIntimacao()
    {
        $arrSituacao = array();
        $arrSituacao[MdPetIntimacaoRN::$INTIMACAO_PENDENTE] = MdPetIntimacaoRN::$STR_INTIMACAO_PENDENTE_ACEITE;
        $arrSituacao[MdPetIntimacaoRN::$INTIMACAO_CUMPRIDA_POR_ACESSO] = MdPetIntimacaoRN::$STR_INTIMACAO_CUMPRIDA_POR_ACESSO;
        $arrSituacao[MdPetIntimacaoRN::$INTIMACAO_CUMPRIDA_PRAZO] = MdPetIntimacaoRN::$STR_INTIMACAO_CUMPRIDA_PRAZO;
        $arrSituacao[MdPetIntimacaoRN::$INTIMACAO_RESPONDIDA] = MdPetIntimacaoRN::$STR_INTIMACAO_RESPONDIDA_ACEITE;

        return $arrSituacao;
    }

    protected function getIdCertidaoPorIntimacaoConectado($arr)
    {
        $idIntimacao = $arr[0];
        $idUsuario = isset($arr[1]) && $arr[1] ? $arr[1] : null;
        $idCertidao = null;
        $objMdPetIntRelDestRN = new MdPetIntRelDestinatarioRN();
        $objRelDestDTO = $arr[2];

        $objUsuarioDTO = new UsuarioDTO();
        $objUsuarioDTO->setNumIdUsuario($idUsuario);
        $objUsuarioDTO->retNumIdContato();
        $objUsuarioRN = new UsuarioRN();
        $ret = $objUsuarioRN->listarRN0490($objUsuarioDTO);
        $objUsuarioDTO = count($ret) > 0 ? current($ret) : null;
        $idContato = !is_null($objUsuarioDTO) ? $objUsuarioDTO->getNumIdContato() : null;

        if (!is_null($objRelDestDTO)) {
            $objMdPetIntAceiteRN = new MdPetIntAceiteRN();

            $idRelDest = $objRelDestDTO->getNumIdMdPetIntRelDestinatario();
            $objMdPetIntAceiteDTO = new MdPetIntAceiteDTO();
            $objMdPetIntAceiteDTO->setNumIdMdPetIntRelDestinatario($idRelDest);
            $objMdPetIntAceiteDTO->retDblIdDocumentoCertidao();
            $lista = $objMdPetIntAceiteRN->listar($objMdPetIntAceiteDTO);
            $idCertidao = count($lista) > 0 ? $lista[0]->getDblIdDocumentoCertidao() : null;
        } else {

            $objMdPetIntDestDTO = new MdPetIntRelDestinatarioDTO();
            $objMdPetIntDestDTO->setNumIdContatoParticipante($idContato);
            $objMdPetIntDestDTO->setNumIdMdPetIntimacao($idIntimacao);
            $objMdPetIntDestDTO->retNumIdMdPetIntRelDestinatario();
            $objMdPetIntDestRN = new MdPetIntRelDestinatarioRN();
            $retLista = $objMdPetIntDestRN->listar($objMdPetIntDestDTO);
            $idMdPetIntDest = InfraArray::converterArrInfraDTO($retLista, 'IdMdPetIntRelDestinatario');

            $objMdPetIntAceiteDTO = new MdPetIntAceiteDTO();
            if (is_array($idMdPetIntDest)) {
                $objMdPetIntAceiteDTO->setNumIdMdPetIntRelDestinatario($idMdPetIntDest, InfraDTO::$OPER_IN);
            } else {
                $objMdPetIntAceiteDTO->setNumIdMdPetIntRelDestinatario($idRelDest);
            }
            $objMdPetIntAceiteDTO->retDblIdDocumentoCertidao();
            $objMdPetIntAceiteRN = new MdPetIntAceiteRN();
            $lista = $objMdPetIntAceiteRN->listar($objMdPetIntAceiteDTO);
            $idCertidao = count($lista) > 0 ? $lista[0]->getDblIdDocumentoCertidao() : null;
        }

        return $idCertidao;
    }

    protected function todasIntimacoesAceitasConectado($arrIntimacoes)
    {
        $todasAceitas = false;
        $objMdPetIntDestRN = new MdPetIntRelDestinatarioRN();
        $objMdPetIntAceiteRN = new MdPetIntAceiteRN();
        $objContato = $this->retornaObjContatoIdUsuarioConectado(array(SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno()));
        $idContato = $objContato->getNumIdContato();
        $qtdIntimacoes = count($arrIntimacoes);

        $objMdPetIntDestDTO = new MdPetIntRelDestinatarioDTO();

        if (count($arrIntimacoes) > 0) {
            $objMdPetIntDestDTO->setNumIdMdPetIntimacao($arrIntimacoes, InfraDTO::$OPER_IN);
            $objMdPetIntDestDTO->setNumIdContatoParticipante($idContato);
            $objMdPetIntDestDTO->retNumIdMdPetIntRelDestinatario();
            $objMdPetIntDestDTO->retDthDataCadastro();
            $objMdPetIntDestDTO->retNumIdMdPetIntRelDestinatario();
            $objMdPetIntDestDTO->retStrSinPessoaJuridica();
            $objMdPetIntDestDTO->retNumIdContato();
            $objMdPetIntDestDTO->retDblCnpjContato();
            $objMdPetIntDestDTO->retDblIdDocumento();
            $objMdPetIntDestDTO->retStrNomeContato();

            $count = $objMdPetIntDestRN->contar($objMdPetIntDestDTO);
            $objMdPetIntRelDestDTO = $objMdPetIntDestRN->listar($objMdPetIntDestDTO);
            $mdPetVinculoRN = new MdPetVinculoRN();
            $objMdPetIntRelDestDTOTratado = $objMdPetIntRelDestDTO;
            $removerRevogado = false;
            $arrObjDestinatariosIntimacoesCopia = $objMdPetIntRelDestDTO;
            $arrObjDestinatarios = array();
            $arrObjDestinatariosUnicosIntimacao = array();
            $arrObjDestinatariosUnicosIntimacaoComProcuracao = array();
            $qtdDestinatariosIntimacao = 0;
            foreach ($objMdPetIntRelDestDTOTratado as $chave => $itemObjMdPetIntRelDestDTOTratado) {
                $mdPetVincRepresentantRN = new MdPetVincRepresentantRN();
                $arrProcuracoesAtivasDestinatario = $mdPetVincRepresentantRN->retornarProcuradoresComPoderCumprirResponder($itemObjMdPetIntRelDestDTOTratado->getNumIdContato(), $idProtocolo, $objContato->getNumIdContato());
                if (count($arrProcuracoesAtivasDestinatario) > 0) {
                    $arrObjDestinatarios[$chave]['objeto'] = $itemObjMdPetIntRelDestDTOTratado;
                    $arrObjDestinatarios[$chave]['procuracoes'] = $arrProcuracoesAtivasDestinatario;
                    if (!key_exists($itemObjMdPetIntRelDestDTOTratado->getNumIdMdPetIntRelDestinatario(), $arrObjDestinatariosUnicosIntimacaoComProcuracao)) {
                        $arrObjDestinatariosUnicosIntimacaoComProcuracao[$itemObjMdPetIntRelDestDTOTratado->getNumIdMdPetIntRelDestinatario()] = $itemObjMdPetIntRelDestDTOTratado;
                    }
                }
                if (!key_exists($itemObjMdPetIntRelDestDTOTratado->getNumIdMdPetIntRelDestinatario(), $arrObjDestinatariosUnicosIntimacao)) {
                    $arrObjDestinatariosUnicosIntimacao[$itemObjMdPetIntRelDestDTOTratado->getNumIdMdPetIntRelDestinatario()] = $itemObjMdPetIntRelDestDTOTratado;
                    $qtdDestinatariosIntimacao++;
                }
            }
            if ($count > 0) {
                $arrIntRelDest = InfraArray::converterArrInfraDTO($objMdPetIntRelDestDTOTratado, 'IdMdPetIntRelDestinatario');
                if ($arrIntRelDest) {
                    $objMdPetIntAceiteDTO = new MdPetIntAceiteDTO();
                    $objMdPetIntAceiteDTO->setNumIdMdPetIntRelDestinatario($arrIntRelDest, InfraDTO::$OPER_IN);
                    $objMdPetIntAceiteDTO->retNumIdMdPetIntAceite();

                    $countAceites = $objMdPetIntAceiteRN->contar($objMdPetIntAceiteDTO);
                } else {
                    $countAceites = 0;
                }

                $todasAceitas = false;
                $qntDestinatarioAntes = count($arrObjDestinatariosUnicosIntimacaoComProcuracao);
                $qntDestinatario = count($arrObjDestinatariosUnicosIntimacao);
                if($qntDestinatarioAntes > 0) {
                    $todasAceitas = ($countAceites == $qntDestinatario && $qntDestinatarioAntes == $qntDestinatario);
                } else {
                    $todasAceitas = ($countAceites == $qntDestinatario);
                }

                $retorno = array('todasAceitas' => $todasAceitas, 'qntDestinatario' => $qntDestinatario);
            }
        }
        return $retorno;
    }


    protected function existeAceiteIntimacoesConectado($arrIntimacoes)
    {
        $existeAceite = false;
        $objMdPetIntDestRN = new MdPetIntRelDestinatarioRN();
        $objMdPetIntAceiteRN = new MdPetIntAceiteRN();
        $objContato = $this->retornaObjContatoIdUsuarioConectado(array(SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno()));
        $idContato = $objContato->getNumIdContato();

        $objMdPetIntDestDTO = new MdPetIntRelDestinatarioDTO();
        if (count($arrIntimacoes) > 0) {
            $objMdPetIntDestDTO->setNumIdMdPetIntimacao($arrIntimacoes, InfraDTO::$OPER_IN);
            $objMdPetIntDestDTO->setNumIdContatoParticipante($idContato);
            $objMdPetIntDestDTO->retNumIdMdPetIntRelDestinatario();
            $count = $objMdPetIntDestRN->contar($objMdPetIntDestDTO);
            if ($count > 0) {
                $arrIntRelDest = InfraArray::converterArrInfraDTO($objMdPetIntDestRN->listar($objMdPetIntDestDTO), 'IdMdPetIntRelDestinatario');

                $objMdPetIntAceiteDTO = new MdPetIntAceiteDTO();
                $objMdPetIntAceiteDTO->setNumIdMdPetIntRelDestinatario($arrIntRelDest, InfraDTO::$OPER_IN);
                $objMdPetIntAceiteDTO->retNumIdMdPetIntAceite();

                $qntAceite = $objMdPetIntAceiteRN->contar($objMdPetIntAceiteDTO);
                $existeAceite = $qntAceite > 0 && $qntAceite == $count;
            }
        }

        return $existeAceite;
    }

    //método principal responsável pelos procedimentos de aceite / cumprimento manual da intimacao
    public function processarAceiteManualControlado($arrParametros)
    {
        try {
            //Start Rns
            $objMdPetIntCertidaoRN = new MdPetIntCertidaoRN();
            $objMdPetIntimacaoRN = new MdPetIntimacaoRN();
            $objProcedimentoRN = new ProcedimentoRN();
            $objUsuarioRN = new UsuarioRN();
            $objMdPetIntDestRN = new MdPetIntRelDestinatarioRN();

            //Get Id Contato
            $objUsuarioDTO = new UsuarioDTO();
            $objUsuarioDTO->retNumIdContato();
            $objUsuarioDTO->setNumIdUsuario(SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno());
            $objUsuarioDTO = $objUsuarioRN->consultarRN0489($objUsuarioDTO);
            $idContato = isset($objUsuarioDTO) && !is_null($objUsuarioDTO) ? $objUsuarioDTO->getNumIdContato() : null;

            //Get Pet Rel Destinatario
            $idIntimacao = $arrParametros['hdnIdIntimacao'];
            $arrDados = $this->existeAceiteIntimacaoAcao(array($idIntimacao, true));

            if ($arrDados) {
                foreach ($arrDados as $aceite) {
                    $idDestinatarioAceite[] = $aceite['ID_DESTINATARIO'];
                }
            }

            $objMdPetIntDestDTO = new MdPetIntRelDestinatarioDTO();

            if (is_array($idIntimacao)) {
                $objMdPetIntDestDTO->setNumIdMdPetIntimacao($idIntimacao, InfraDTO::$OPER_IN);
            } else {
                $objMdPetIntDestDTO->setNumIdMdPetIntimacao($idIntimacao);
            }
            $objMdPetIntDestDTO->setNumIdContatoParticipante($idContato);
            $objMdPetIntDestDTO->retNumIdMdPetIntRelDestinatario();
            $objMdPetIntDestDTO->retNumIdContato();
            $objMdPetIntDestDTO->retNumIdContatoParticipante();
            $objMdPetIntDestDTO->retStrSinPessoaJuridica();
            $objMdPetIntDestDTO->retStrNomeContato();
            $retLista = $objMdPetIntDestRN->listar($objMdPetIntDestDTO);
            $qntDest = count($retLista);
            $dest = 0;
            foreach ($retLista as $objMdPetIntDestDTO) {
                $dest++;
                $objMdPetVincRepresentantDTO = new MdPetVincRepresentantDTO();
                $objMdPetVincRepresentantDTO->setNumIdContato($idContato);
                $objMdPetVincRepresentantDTO->setStrSinAtivo('S');
                $objMdPetVincRepresentantDTO->setStrStaEstado(MdPetVincRepresentantRN::$RP_ATIVO);
                $objMdPetVincRepresentantDTO->setNumIdContatoVinc($objMdPetIntDestDTO->getNumIdContato());
                $objMdPetVincRepresentantDTO->retNumIdContatoVinc();
                $objMdPetVincRepresentantDTO->retNumIdContato();
                $objMdPetVincRepresentantDTO->retNumIdMdPetVinculoRepresent();
                $objMdPetVincRepresentantDTO->retStrTipoRepresentante();
                $objMdPetVincRepresentantDTO->retDthDataLimite();
                $objMdPetVincRepresentantDTO->retStrStaAbrangencia();
                $objMdPetVincRepresentantDTO->retStrStaEstado();

                $objMdPetVincRepresentantRN = new MdPetVincRepresentantRN();
                $contarobjMdPetVincRepresentantDTO = $objMdPetVincRepresentantRN->contar($objMdPetVincRepresentantDTO);
                $objMdPetVincRepresentantDTO = $objMdPetVincRepresentantRN->listar($objMdPetVincRepresentantDTO);
                //verificado se existe alguma procuração simples, só será cumprida caso a mesma esteja 
                foreach ($objMdPetVincRepresentantDTO as $chaveVinculo => $itemObjMdPetVinculoDTO) {
                    if ($itemObjMdPetVinculoDTO->getStrTipoRepresentante() == MdPetVincRepresentantRN::$PE_PROCURADOR_SIMPLES) {
                        $rnMdPetIntimacaoRN = new MdPetIntimacaoRN();
                        $verificacaoCriteriosProcuracaoSimples = $rnMdPetIntimacaoRN->_verificarCriteriosProcuracaoSimples($itemObjMdPetVinculoDTO->getNumIdMdPetVinculoRepresent(), $itemObjMdPetVinculoDTO->getStrStaEstado(), $itemObjMdPetVinculoDTO->getDthDataLimite(), $arrParametros['id_documento'], $itemObjMdPetVinculoDTO->getStrStaAbrangencia());
                        if (!$verificacaoCriteriosProcuracaoSimples) {
                            continue;
                        }
                    }
                }

                //Caso tenha alguma intimação de pessoa com vinculação/procuração diferente de ativo a mesma não deve ser cumprida
                if ($objMdPetIntDestDTO->getNumIdContato() != $objMdPetIntDestDTO->getNumIdContatoParticipante() && $contarobjMdPetVincRepresentantDTO == 0) {
                    continue;
                }

                //Só será cumprida as intimações que ainda não possuem aceite
                if (!empty($idDestinatarioAceite) && in_array($objMdPetIntDestDTO->getNumIdMdPetIntRelDestinatario(), $idDestinatarioAceite)) {
                    continue;
                }

                //        $objMdPetIntDestDTO = !is_null($retLista) && count($retLista) > 0 ? current($retLista) : null;
                $idMdPetIntDest = !is_null($objMdPetIntDestDTO) ? $objMdPetIntDestDTO->getNumIdMdPetIntRelDestinatario() : null;

                $objMdPetIntAceiteDTO = new MdPetIntAceiteDTO();

                //data cumprimento intimacao
                $arrDataAtual = explode(" ", InfraData::getStrDataHoraAtual());

                $dataAtual = count($arrDataAtual) > 0 ? current($arrDataAtual) : null;
                $dateAtual = DateTime::createFromFormat('d/m/Y', $dataAtual);
                $dateAtual->sub(new DateInterval('P1D'));

                $objMdPetIntPrazoRN = new MdPetIntPrazoRN();
                $dataCumprimento = $objMdPetIntPrazoRN->calcularDataPrazo(1, $dateAtual->format('d/m/Y'));

                $objMdPetIntAceiteDTO->setDthData($dataCumprimento);

                //data_consulta_direta
                $objMdPetIntAceiteDTO->setDthDataConsultaDireta(InfraData::getStrDataHoraAtual());

                $objMdPetIntAceiteDTO->setNumIdMdPetIntRelDestinatario($idMdPetIntDest);
                $objMdPetIntAceiteDTO->setStrTipoAceite(MdPetIntimacaoRN::$TP_MANUAL_USUARIO_EXTERNO_ACEITE);
                $objMdPetIntAceiteDTO->setDblIdDocumentoCertidao(null);
                $objMdPetIntAceiteDTO->setStrIp(InfraUtil::getStrIpUsuario());
                $objMdPetIntAceiteDTO->setNumIdUsuario(SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno());

                //Cadastrando o Aceite
                $objMdPetIntAceiteDTO = $this->cadastrar($objMdPetIntAceiteDTO);

                //Atualizando o campo de situação
                $objMdPetIntDestRN->atualizarStatusIntimacao(array(MdPetIntimacaoRN::$INTIMACAO_CUMPRIDA_POR_ACESSO, $idMdPetIntDest));

                //Cadastrando Data Limite para Tipo Resposta
                $arrObjMdPetIntRelTipoRespDestDTO = $objMdPetIntPrazoRN->retornarTipoRespostaDataLimite(array($idIntimacao, $idMdPetIntDest));

                if (count($arrObjMdPetIntRelTipoRespDestDTO) > 0) {
                    $objMdPetIntRelTipoRespDestRN = new MdPetIntRelTipoRespDestRN();
                    foreach ($arrObjMdPetIntRelTipoRespDestDTO as $objMdPetIntRelTipoRespDestDTO) {
                        $objMdPetIntRelTipoRespDestRN->cadastrar($objMdPetIntRelTipoRespDestDTO);
                    }
                }

                $objUnidadeDTO = $objMdPetIntimacaoRN->getUnidadeIntimacao(array($idIntimacao));


                $dados = $objMdPetIntimacaoRN->retornaDadosDocPrincipalIntimacao(array($idIntimacao));

                $idProcedimento = $dados[2];

                $objProcedimentoDTO = new ProcedimentoDTO();
                $objProcedimentoDTO->setDblIdProcedimento($idProcedimento);
                $objProcedimentoDTO->retDblIdProcedimento();
                $objProcedimentoDTO->retStrStaEstadoProtocolo();
                $objProcedimentoDTO->retStrStaNivelAcessoGlobalProtocolo();
                $objProcedimentoDTO->retStrStaNivelAcessoLocalProtocolo();
                $objProcedimentoDTO->retStrProtocoloProcedimentoFormatado();
                $objProcedimentoDTO = $objProcedimentoRN->consultarRN0201($objProcedimentoDTO);

                if ($objProcedimentoDTO->getStrStaEstadoProtocolo() == ProtocoloRN::$TE_PROCEDIMENTO_ANEXADO) {
                    $objRelProtocoloProtocoloDTO = new RelProtocoloProtocoloDTO();
                    $objRelProtocoloProtocoloDTO->retDblIdProtocolo1();
                    $objRelProtocoloProtocoloDTO->retStrProtocoloFormatadoProtocolo1();
                    $objRelProtocoloProtocoloDTO->setDblIdProtocolo2($objProcedimentoDTO->getDblIdProcedimento());
                    $objRelProtocoloProtocoloDTO->setStrStaAssociacao(RelProtocoloProtocoloRN::$TA_PROCEDIMENTO_ANEXADO);

                    $objRelProtocoloProtocoloRN = new RelProtocoloProtocoloRN();
                    $objRelProtocoloProtocoloDTO = $objRelProtocoloProtocoloRN->consultarRN0841($objRelProtocoloProtocoloDTO);

                    $objProcedimentoDTO = new ProcedimentoDTO();
                    $objProcedimentoRN = new ProcedimentoRN();
                    $objProcedimentoDTO->setDblIdProcedimento($objRelProtocoloProtocoloDTO->getDblIdProtocolo1());
                    $objProcedimentoDTO->retDblIdProcedimento();
                    $objProcedimentoDTO->retStrStaEstadoProtocolo();
                    $objProcedimentoDTO->retStrStaNivelAcessoGlobalProtocolo();
                    $objProcedimentoDTO->retStrStaNivelAcessoLocalProtocolo();

                    $objProcedimentoDTO->retStrProtocoloProcedimentoFormatado();
                    $objProcedimentoDTO = $objProcedimentoRN->consultarRN0201($objProcedimentoDTO);
                    $idProcedimento = $objRelProtocoloProtocoloDTO->getDblIdProtocolo1();
                }


                $arrParams = array();
                $arrParams[0] = $idIntimacao;
                $arrParams[1] = $objUnidadeDTO;

                //Unidade Geradora
                //unidade esta ativa
                $unidadeDTO = new UnidadeDTO();
                $unidadeDTO->retTodos();
                $unidadeDTO->setBolExclusaoLogica(false);
                $unidadeDTO->setNumIdUnidade($objUnidadeDTO->getNumIdUnidade());
                $unidadeRN = new UnidadeRN();
                $objUnidadeDTO = $unidadeRN->consultarRN0125($unidadeDTO);

                $arrAtividadeDTO = null;
                if ($objUnidadeDTO->getStrSinAtivo() == 'S') {
                    $arrAtividadeDTO = $objMdPetIntimacaoRN->verificarUnidadeAberta(array($objProcedimentoDTO, $objUnidadeDTO->getNumIdUnidade()));
                }

                $idUsuarioAtribuicao = null;

                if (count($arrAtividadeDTO) == 0) {
                    // Sigiloso - não tem nenhuma credencial 
                    if ($objProcedimentoDTO->getStrStaNivelAcessoGlobalProtocolo() == ProtocoloRN::$NA_SIGILOSO || $objProcedimentoDTO->getStrStaNivelAcessoLocalProtocolo() == ProtocoloRN::$NA_SIGILOSO) {
                        $arrParams[1] = null;
                    } else {
                        $idUnidadeAberta = $objMdPetIntimacaoRN->reabrirUnidade(array($objProcedimentoDTO, $objUnidadeDTO->getNumIdUnidade()));
                        if ($idUnidadeAberta) {
                            $arrParams[1] = $objMdPetIntimacaoRN->retornaObjUnidadePorId($idUnidadeAberta, true);
                        } else {
                            $arrParams[1] = null;
                        }
                    }
                } else {
                    //Unidade da intimação ainda tem credencial
                    if ($objProcedimentoDTO->getStrStaNivelAcessoGlobalProtocolo() == ProtocoloRN::$NA_SIGILOSO || $objProcedimentoDTO->getStrStaNivelAcessoLocalProtocolo() == ProtocoloRN::$NA_SIGILOSO) {
                        if (!array_search($objUnidadeDTO->getNumIdUnidade(), $arrAtividadeDTO)) {
                            $arrParams[1] = $objMdPetIntimacaoRN->retornaObjUnidadePorId($arrAtividadeDTO[0]->getNumIdUnidade(), $retTodos);
                        }
                    }
                    $idUnidadeAberta = $arrAtividadeDTO[0]->getNumIdUnidade();
                    if ($arrAtividadeDTO[0]->isSetNumIdUsuarioAtribuicao()) {
                        $idUsuarioAtribuicao = $arrAtividadeDTO[0]->getNumIdUsuarioAtribuicao();
                    }
                }

                $arrParams[2] = $objProcedimentoDTO;
                $arrParams[3] = $objMdPetIntAceiteDTO;
                $arrParams[6] = $dataCumprimento;
                $arrParams[4] = $objMdPetIntDestDTO;
                if ($arrParams[1]) {

                    //Gerando a Certidão
                    $objMdPetIntCertidaoRN->gerarCertidaoExterna($arrParams);

                    $idAcessoExterno = $arrParametros['id_acesso_externo'];

                    $dataIntimacao = $objMdPetIntDestRN->consultarDadosIntimacao($idIntimacao);

                    $arr = array($idProcedimento, $dataIntimacao, $dataCumprimento, $idIntimacao, $idMdPetIntDest, null, MdPetIntAcessoExternoDocumentoRN::$STA_EXTERNO, $arrParams[1]);

                    $this->lancarAndamentoAceite($arr);

                    // REENVIAR ou REENVIAR E REATRIBUIR
                    if (is_numeric($idUnidadeAberta) && is_numeric($idProcedimento) && $qntDest == $dest) {
                        $arrParams = array();
                        $arrParams[0] = $idUnidadeAberta;
                        $arrParams[1] = $idProcedimento;

                        if (!is_null($idUsuarioAtribuicao)) {
                            $arrParams[2] = $idUsuarioAtribuicao;
                        }
                        $objMdPetIntimacaoRN->reenviarReatribuirUnidade($arrParams);
                    }

//                 return $objMdPetIntAceiteDTO;
                } else {
                    // EXCEÇÃO DE UNIDADE
                    $detalhes = "Unidade não definida";
                    throw new InfraException('Erro na definição da Unidade da Consulta Direta', null, $detalhes);
                }
            }
            //@todo verificar para que usar este objeto
            return $objMdPetIntAceiteDTO;
        } catch (Exception $e) {
            throw new InfraException('Erro processando Consulta Direta', $e);
        }
    }

}

?>