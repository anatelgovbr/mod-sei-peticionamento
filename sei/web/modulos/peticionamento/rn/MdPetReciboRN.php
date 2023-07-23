<?
/**
 * ANATEL
 *
 * 28/06/2016 - criado por marcelo.bezerra - CAST
 *
 */

require_once dirname(__FILE__) . '/../../../SEI.php';

class MdPetReciboRN extends InfraRN
{

    public static $TP_RECIBO_NOVO = 'N';
    public static $STR_TP_RECIBO_NOVO = 'Processo Novo';

    public static $TP_RECIBO_INTERCORRENTE = 'I';
    public static $STR_TP_RECIBO_INTERCORRENTE = 'Intercorrente';

    public static $TP_RECIBO_RESPOSTA_INTIMACAO = 'R';
    public static $STR_TP_RECIBO_RESPOSTA_INTIMACAO = 'Resposta a Intimação';

    public static $TP_RECIBO_RESPONSAVEL_LEGAL_INICIAL = 'V';
    public static $STR_TP_RECIBO_RESPONSAVEL_LEGAL_INICIAL = 'Responsável Legal - Inicial';

    public static $TP_RECIBO_RESPONSAVEL_LEGAL_ALTERACAO = 'A';
    public static $STR_TP_RECIBO_RESPONSAVEL_LEGAL_ALTERACAO = 'Responsável Legal - Alteração';

    public static $TP_RECIBO_ATUALIZACAO_ATOS_CONSTITUTIVOS = 'C';
    public static $STR_TP_RECIBO_ATUALIZACAO_ATOS_CONSTITUTIVOS = 'Atualização de Atos Constitutivos';

    public static $TP_RECIBO_PROCURACAO_ELETRONICA_EMISSAO = 'P';
    public static $STR_TP_RECIBO_PROCURACAO_ELETRONICA_EMISSAO = 'Procuração Eletrônica - Emissão';

    public static $TP_RECIBO_PROCURACAO_ELETRONICA_REVOGACAO = 'G';
    public static $STR_TP_RECIBO_PROCURACAO_ELETRONICA_REVOGACAO = 'Procuração Eletrônica - Revogação';

    public static $TP_RECIBO_PROCURACAO_ELETRONICA_RENUNCIA = 'U';
    public static $STR_TP_RECIBO_PROCURACAO_ELETRONICA_RENUNCIA = 'Procuração Eletrônica - Renúncia';

    public function __construct()
    {
        parent::__construct();
    }

    protected function inicializarObjInfraIBanco()
    {
        return BancoSEI::getInstance();
    }

    /**
     * Short description of method listarConectado
     *
     * @access protected
     * @param $objDTO
     * @return mixed
     * @author Marcelo Bezerra <marcelo.bezerra@cast.com.br>
     */
    protected function listarConectado(MdPetReciboDTO $objDTO)
    {

        try {

            $objInfraException = new InfraException();

            if ($objDTO->isSetDthInicial() || $objDTO->isSetDthFinal()) {

                // Data Início
                if ($objDTO->isSetDthInicial()) {
                    if (strlen($objDTO->getDthInicial()) == '10') {
                        $objDTO->setDthInicial($objDTO->getDthInicial() . ' 00:00:00');
                    } elseif (strlen($objDTO->getDthInicial()) == '16') {
                        $objDTO->setDthInicial($objDTO->getDthInicial() . ':00');
                    }
                    if (!InfraData::validarDataHora($objDTO->getDthInicial())) {
                        $objInfraException->lancarValidacao('Data/Hora Inválida.');
                    }
                }

                // Data Final recebe Inicial se estiver em branco ?????
                if ($objDTO->isSetDthFinal()) {
                    if (strlen($objDTO->getDthFinal()) == '10') {
                        $objDTO->setDthFinal($objDTO->getDthFinal() . ' 23:59:59');
                    } elseif (strlen($objDTO->getDthFinal()) == '16') {
                        $objDTO->setDthFinal($objDTO->getDthFinal() . ':59');
                    }
                    if (!InfraData::validarDataHora($objDTO->getDthFinal())) {
                        $objInfraException->lancarValidacao('Data/Hora Inválida.');
                    }
                }

                // Data Incio e Data Fim - Comparando
                if ($objDTO->isSetDthInicial() && $objDTO->isSetDthFinal()) {
                    if (InfraData::compararDataHora($objDTO->getDthInicial(), $objDTO->getDthFinal()) < 0) {
                        $objInfraException->lancarValidacao('A Data/Hora Inicio deve ser menor que a Data/Hora Fim.');
                    }
                    // Data Incio e Data Fim - Comparando
                    $objDTO->adicionarCriterio(array('DataHoraRecebimentoFinal', 'DataHoraRecebimentoFinal'),
                        array(InfraDTO::$OPER_MAIOR_IGUAL, InfraDTO::$OPER_MENOR_IGUAL),
                        array($objDTO->getDthInicial(), $objDTO->getDthFinal()),
                        InfraDTO::$OPER_LOGICO_AND);
                } else {
                    if ($objDTO->isSetDthInicial() && !$objDTO->isSetDthFinal()) {
                        // Data Incio - Comparando
                        $objDTO->adicionarCriterio(array('DataHoraRecebimentoFinal'),
                            array(InfraDTO::$OPER_MAIOR_IGUAL),
                            array($objDTO->getDthInicial())/*,
								InfraDTO::$OPER_LOGICO_AND*/);
                    } elseif (!$objDTO->isSetDthInicial() && $objDTO->isSetDthFinal()) {
                        // Data Fim - Comparando
                        $objDTO->adicionarCriterio(array('DataHoraRecebimentoFinal'),
                            array(InfraDTO::$OPER_MENOR_IGUAL),
                            array($objDTO->getDthFinal())/*,
								InfraDTO::$OPER_LOGICO_AND*/);
                    }
                }

            }

            $objBD = new MdPetReciboBD($this->getObjInfraIBanco());
            $ret = $objBD->listar($objDTO);
            return $ret;

        } catch (Exception $e) {
            throw new InfraException ('Erro listando Recibo Peticionamento.', $e);
        }
    }

    /**
     * Short description of method contarConectado
     *
     * @access protected
     * @param $objDTO
     * @return mixed
     * @author Jaqueline Mendes <jaqueline.mendes@castgroup.com.br>
     */
    protected function contarConectado(MdPetReciboDTO $objDTO)
    {

        try {

            $objBD = new MdPetReciboBD($this->getObjInfraIBanco());
            $ret = $objBD->contar($objDTO);
            return $ret;

        } catch (Exception $e) {
            throw new InfraException ('Erro contando Recibo Peticionamento.', $e);
        }
    }

    /**
     * Short description of method consultarConectado
     *
     * @access protected
     * @param $objDTO
     * @return mixed
     * @author Marcelo Bezerra <marcelo.bezerra@cast.com.br>
     */
    protected function consultarConectado(MdPetReciboDTO $objDTO)
    {

        try {

            $objBD = new MdPetReciboBD($this->getObjInfraIBanco());
            $ret = $objBD->consultar($objDTO);
            $ret->setArrObjMdPetRelReciboDocumentoAnexoDTO(array());
            return $ret;

        } catch (Exception $e) {
            throw new InfraException ('Erro listando Recibo Peticionamento.', $e);
        }
    }

    protected function gerarReciboSimplificadoControlado($idProcedimento)
    {

        $objMdPetReciboDTO = new MdPetReciboDTO();
        $objMdPetReciboDTO->retTodos();

        $objMdPetReciboDTO->setNumIdProtocolo($idProcedimento);
        $objMdPetReciboDTO->setNumIdUsuario(SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno());
        $objMdPetReciboDTO->setDthDataHoraRecebimentoFinal(InfraData::getStrDataHoraAtual());
        $objMdPetReciboDTO->setStrIpUsuario(InfraUtil::getStrIpUsuario());
        $objMdPetReciboDTO->setStrSinAtivo('S');
        $objMdPetReciboDTO->setStrStaTipoPeticionamento('N');

        $objBD = new MdPetReciboBD($this->getObjInfraIBanco());
        $ret = $objBD->cadastrar($objMdPetReciboDTO);
        return $ret;

    }

    /*
     produz recibo pesquisavel, inserindo dados consultáveis pela consulta de recibos
     (diferente do documento de recibo que é anexado ao processo do SEI)(
     */
    protected function cadastrarControlado($arrParams)
    {

        $arrParametros = $arrParams[0]; //parametros adicionais fornecidos no formulario de peticionamento
        $objUnidadeDTO = $arrParams[1]; //UnidadeDTO da unidade geradora do processo
        $objProcedimentoDTO = $arrParams[2]; //ProcedimentoDTO para vincular o recibo ao processo correto
        $arrParticipantesParametro = $arrParams[3]; //array de ParticipanteDTO
        $arrDocsPrincipais = $arrParams[4]; //array de DocumentoDTO (docs principais)
        $arrDocsEssenciais = $arrParams[5]; //array de DocumentoDTO (docs essenciais)
        $arrDocsComplementares = $arrParams[6]; //array de DocumentoDTO (docs complementares)

        $objMdPetReciboDTO = new MdPetReciboDTO();

        $objMdPetReciboDTO->setNumIdProtocolo($objProcedimentoDTO->getDblIdProcedimento());
        $objMdPetReciboDTO->setNumIdUsuario(SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno());
        $objMdPetReciboDTO->setDthDataHoraRecebimentoFinal(InfraData::getStrDataHoraAtual());
        $objMdPetReciboDTO->setStrIpUsuario(InfraUtil::getStrIpUsuario());
        $objMdPetReciboDTO->setStrSinAtivo('S');
        $objMdPetReciboDTO->setStrStaTipoPeticionamento('N');

        $objBD = new MdPetReciboBD($this->getObjInfraIBanco());
        $ret = $objBD->cadastrar($objMdPetReciboDTO);

        return $ret;

    }

    //método utilizado para gerar recibo ao final do cadastramento de um processo de peticionamento de usuario externo
    protected function montarReciboControlado($arrParams)
    {

        $reciboDTO = $arrParams[4];

        //Verifica se retorna o objeto antes ou depois da alteração - solução adaptada em decorrencia do trycatch
        $returnObjDTO = array_key_exists(5, $arrParams) ? $arrParams[5] : false;

        //gerando documento recibo (nao assinado) dentro do processo do SEI
        $objInfraParametro = new InfraParametro($this->getObjInfraIBanco());

        $arrParametros = $arrParams[0]; //parametros adicionais fornecidos no formulario de peticionamento
        $objUnidadeDTO = $arrParams[1]; //UnidadeDTO da unidade geradora do processo
        $objProcedimentoDTO = $arrParams[2]; //ProcedimentoDTO para vincular o recibo ao processo correto

        //tentando simular sessao de usuario interno do SEI
        SessaoSEI::getInstance()->simularLogin(null, null, SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno(), $objUnidadeDTO->getNumIdUnidade());

        $htmlRecibo = $this->gerarHTMLConteudoDocRecibo($arrParams);

        $idSerieRecibo = $objInfraParametro->getValor(MdPetIntSerieRN::$MD_PET_ID_SERIE_RECIBO);

        //==========================================================================
        //incluindo doc recibo no processo via SEIRN
        //==========================================================================

        $objDocumentoAPI = new DocumentoAPI();
        $objDocumentoAPI->setIdProcedimento($objProcedimentoDTO->getDblIdProcedimento());
        $objDocumentoAPI->setSubTipo(DocumentoRN::$TD_FORMULARIO_AUTOMATICO);
        $objDocumentoAPI->setTipo(ProtocoloRN::$TP_DOCUMENTO_GERADO);
        $objDocumentoAPI->setIdSerie($idSerieRecibo);
        $objDocumentoAPI->setSinAssinado('N');
        $objDocumentoAPI->setSinBloqueado('S');
        $objDocumentoAPI->setIdHipoteseLegal(null);
        $objDocumentoAPI->setNivelAcesso(ProtocoloRN::$NA_PUBLICO);
        $objDocumentoAPI->setIdTipoConferencia(null);

        $objDocumentoAPI->setConteudo(base64_encode(utf8_encode($htmlRecibo)));

        $objSeiRN = new SeiRN();
        $saidaDocExternoAPI = $objSeiRN->incluirDocumento($objDocumentoAPI);

        //necessario forçar update da coluna sta_documento da tabela documento
        //inclusao via SeiRN nao permitiu definir como documento de formulario automatico
        $parObjDocumentoDTO = new DocumentoDTO();
        $parObjDocumentoDTO->retTodos();
        $parObjDocumentoDTO->setDblIdDocumento($saidaDocExternoAPI->getIdDocumento());

        $docRN = new DocumentoRN();
        $parObjDocumentoDTO = $docRN->consultarRN0005($parObjDocumentoDTO);
        $parObjDocumentoDTO->setStrStaDocumento(DocumentoRN::$TD_FORMULARIO_AUTOMATICO);
        $objDocumentoBD = new DocumentoBD($this->getObjInfraIBanco());
        $objDocumentoBD->alterar($parObjDocumentoDTO);

        $reciboDTO->setDblIdDocumento($saidaDocExternoAPI->getIdDocumento());

        $objBD = new MdPetReciboBD($this->getObjInfraIBanco());

        $reciboDTOAlterado = $objBD->alterar($reciboDTO);

        $reciboDTO = $returnObjDTO ? $reciboDTO : $reciboDTOAlterado;

        return $reciboDTO;

    }

    private function gerarHTMLConteudoDocRecibo($arrParams)
    {

        $arrParametros = $arrParams[0]; //parametros adicionais fornecidos no formulario de peticionamento
        $objUnidadeDTO = $arrParams[1]; //UnidadeDTO da unidade geradora do processo
        $objProcedimentoDTO = $arrParams[2]; //ProcedimentoDTO para vincular o recibo ao processo correto
        $arrParticipantesParametro = $arrParams[3]; //array de ParticipanteDTO
        $reciboDTO = $arrParams[4]; //MdPetReciboDTO

        $objUsuarioDTO = new UsuarioDTO();
        $objUsuarioDTO->retTodos();
        $objUsuarioDTO->setNumIdUsuario($reciboDTO->getNumIdUsuario());

        $objUsuarioRN = new UsuarioRN();
        $objUsuarioDTO = $objUsuarioRN->consultarRN0489($objUsuarioDTO);

        $html = '';

        $html .= '<table align="center" style="width: 95%" border="0">';
        $html .= '<tbody>';
        $html .= '<tr>';
        $html .= '<td style="font-weight: bold; width: 400px;">Usuário Externo (signatário):</td>';
        $html .= '<td>' . $objUsuarioDTO->getStrNome() . '</td>';
        $html .= '</tr>';

        //$html .= '<tr>';
        //$html .= '<td style="font-weight: bold;">IP utilizado:</td>';
        //$html .= '<td>' . $reciboDTO->getStrIpUsuario() .'</td>';
        //$html .= '</tr>';

        $html .= '<tr>';
        $html .= '<td style="font-weight: bold;">Data e Horário:</td>';
        $html .= '<td>' . $reciboDTO->getDthDataHoraRecebimentoFinal() . '</td>';
        $html .= '</tr>';

        $html .= '<tr>';
        $html .= '<td style="font-weight: bold;">Tipo de Peticionamento:</td>';
        $html .= '<td>' . $reciboDTO->getStrStaTipoPeticionamentoFormatado() . '</td>';
        $html .= '</tr>';

        $html .= '<tr>';
        $html .= '<td style="font-weight: bold;">Número do Processo:</td>';
        $html .= '<td>' . $objProcedimentoDTO->getStrProtocoloProcedimentoFormatado() . '</td>';
        $html .= '</tr>';

        //obter interessados (apenas os do tipo interessado, nao os do tipo remetente)
        $arrInteressados = array();
        $objParticipanteDTO = new ParticipanteDTO();
        $objParticipanteDTO->setDblIdProtocolo($reciboDTO->getNumIdProtocolo());
        $objParticipanteDTO->setStrStaParticipacao(ParticipanteRN::$TP_INTERESSADO);
        $objParticipanteDTO->retNumIdContato();
        $objParticipanteRN = new ParticipanteRN();
        $arrObjParticipanteDTO = $objParticipanteRN->listarRN0189($objParticipanteDTO);

        foreach ($arrObjParticipanteDTO as $objParticipanteDTO) {
            $objContatoDTO = new ContatoDTO();
            $objContatoDTO->setNumIdContato($objParticipanteDTO->getNumIdContato());
            $objContatoDTO->retStrNome();
            $objContatoRN = new ContatoRN();
            $arrInteressados[] = $objContatoRN->consultarRN0324($objContatoDTO);
        }

        if ($arrInteressados != null && count($arrInteressados) > 0) {

            $html .= '<tr>';
            $html .= '<td colspan="2" style="font-weight: bold;">Interessados:</td>';
            $html .= '</tr>';

            foreach ($arrInteressados as $interessado) {
                $html .= '<tr>';
                $html .= '<td colspan="2" >&nbsp&nbsp&nbsp&nbsp ' . $interessado->getStrNome() . '</td>';
                $html .= '</tr>';
            }

        }

        $html .= '<tr>';
        $html .= '<td style="font-weight: bold;">Protocolos dos Documentos (Número SEI):</td>';
        $html .= '<td></td>';
        $html .= '</tr>';

        //consultando DOCs

        $objMdPetRelReciboDocumentoAnexoDTO = new MdPetRelReciboDocumentoAnexoDTO();
        $objMdPetRelReciboDocumentoAnexoDTO->retTodos(true);
        $reciboAnexoRN = new MdPetRelReciboDocumentoAnexoRN();
        $objMdPetRelReciboDocumentoAnexoDTO->setNumIdReciboPeticionamento($reciboDTO->getNumIdReciboPeticionamento());

        $arrReciboAnexoDTO = $reciboAnexoRN->listar($objMdPetRelReciboDocumentoAnexoDTO);

        $idPrincipalGerado = null;
        $arrIdPrincipal = array();
        $arrIdEssencial = array();
        $arrIdComplementar = array();
        $erroDocumentos = [];
        foreach ($arrReciboAnexoDTO as $itemReciboAnexoDTO) {

            if ($itemReciboAnexoDTO->getStrClassificacaoDocumento() == MdPetRelReciboDocumentoAnexoRN::$TP_PRINCIPAL) {

                $idPrincipalGerado = $itemReciboAnexoDTO->getNumIdDocumento();
                $erroDocumentos['documentoPrincipal'] = true;
            } else if ($itemReciboAnexoDTO->getStrClassificacaoDocumento() == MdPetRelReciboDocumentoAnexoRN::$TP_ESSENCIAL) {

                array_push($arrIdEssencial, $itemReciboAnexoDTO->getNumIdDocumento());
                $erroDocumentos['documentosEssenciais'] = true;
            } else if ($itemReciboAnexoDTO->getStrClassificacaoDocumento() == MdPetRelReciboDocumentoAnexoRN::$TP_COMPLEMENTAR) {

                array_push($arrIdComplementar, $itemReciboAnexoDTO->getNumIdDocumento());
                $erroDocumentos['documentosComplementares'] = true;
            }

        }

        $anexoRN = new AnexoRN();
        $documentoRN = new DocumentoRN();
        if ($idPrincipalGerado != null) {

            $html .= '<tr>';
            $html .= '<td style="font-weight: bold;">&nbsp;&nbsp;&nbsp; - Documento Principal:</td>';
            $html .= '<td></td>';
            $html .= '</tr>';

            $documentoDTO = new DocumentoDTO();
            $documentoDTO->retStrNumero();
            $documentoDTO->retStrNomeSerie();
            $documentoDTO->retStrDescricaoProtocolo();
            $documentoDTO->retStrProtocoloDocumentoFormatado();
            $documentoDTO->setDblIdDocumento($idPrincipalGerado);
            $documentoDTO = $documentoRN->consultarRN0005($documentoDTO);

            if ($documentoDTO) {
                unset($erroDocumentos['documentoPrincipal']);
            }

	        $strNome = $documentoDTO->getStrNomeSerie() . ' ' . $documentoDTO->getStrDescricaoProtocolo() . ' ' . $documentoDTO->getStrNumero();
            $strNumeroSEI = $documentoDTO->getStrProtocoloDocumentoFormatado();

            $html .= '<tr>';
            $html .= '<td> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; - ' . $strNome . '</td>';
            $html .= '<td>' . $strNumeroSEI . '</td>';
            $html .= '</tr>';

        }

        if ($arrIdPrincipal != null && count($arrIdPrincipal) > 0) {

            $html .= '<tr>';
            $html .= '<td style="font-weight: bold;">&nbsp;&nbsp;&nbsp; - Documento Principal:</td>';
            $html .= '<td></td>';
            $html .= '</tr>';

            //loop na lista de documentos principais

            $objAnexoDTO = new AnexoDTO();
            $objAnexoDTO->retTodos(true);

            $objAnexoDTO->adicionarCriterio(array('IdAnexo'),
                array(InfraDTO::$OPER_IN),
                array($arrIdPrincipal));

            $arrAnexoDTO = $anexoRN->listarRN0218($objAnexoDTO);
            $bolExclusao = false;
            foreach ($arrAnexoDTO as $anexoPrincipal) {

                $strNome = $anexoPrincipal->getStrNome();
                $strTipoDocumento = "";
                $strNumeroSEI = $anexoPrincipal->getStrProtocoloFormatadoProtocolo();

                $documentoDTO = new DocumentoDTO();

                $documentoDTO->retStrNumero();
                $documentoDTO->retStrNomeSerie();
                $documentoDTO->retStrDescricaoProtocolo();
                $documentoDTO->retStrProtocoloDocumentoFormatado();

                $documentoDTO->setDblIdDocumento($anexoPrincipal->getDblIdProtocolo());
                $documentoDTO = $documentoRN->consultarRN0005($documentoDTO);
                if ($documentoDTO) {
                    $bolExclusao = true;
                } else {
                    $bolExclusao = false;
                }
                //concatenar tipo e complemento
	            $strNome = $documentoDTO->getStrNomeSerie() . ' ' . $documentoDTO->getStrDescricaoProtocolo() . ' ' . $documentoDTO->getStrNumero();

                $html .= '<tr>';
                $html .= '<td> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; - ' . $strNome . '</td>';
                $html .= '<td>' . $strNumeroSEI . '</td>';
                $html .= '</tr>';

            }
            if ($bolExclusao) {
                unset($erroDocumentos['documentosPrincipais']);
            }
            //fim loop de documentos principais
        }

        //ESSENCIAL
        if ($arrIdEssencial != null && count($arrIdEssencial) > 0) {

            $html .= '<tr>';
            $html .= '<td style="font-weight: bold;">&nbsp;&nbsp;&nbsp; - Documentos Essenciais:</td>';
            $html .= '<td></td>';
            $html .= '</tr>';

            $objAnexoDTO = new AnexoDTO();
            $objAnexoDTO->retTodos(true);

            $objAnexoDTO->adicionarCriterio(array('IdProtocolo'),
                array(InfraDTO::$OPER_IN),
                array($arrIdEssencial));

            $arrAnexoDTOEssencial = $anexoRN->listarRN0218($objAnexoDTO);
            $bolExclusao = false;
            foreach ($arrAnexoDTOEssencial as $objAnexoEssencial) {

                $strNome = $objAnexoEssencial->getStrNome();
                $strTipoDocumento = "";
                $strNumeroSEI = $objAnexoEssencial->getStrProtocoloFormatadoProtocolo();

                $documentoDTO = new DocumentoDTO();

                $documentoDTO->retStrNumero();
                $documentoDTO->retStrNomeSerie();
                $documentoDTO->retStrDescricaoProtocolo();
                $documentoDTO->retStrProtocoloDocumentoFormatado();

                $documentoDTO->setDblIdDocumento($objAnexoEssencial->getDblIdProtocolo());
                $documentoDTO = $documentoRN->consultarRN0005($documentoDTO);
                if ($documentoDTO) {
                    $bolExclusao = true;
                } else {
                    $bolExclusao = false;
                }
                //concatenar tipo e complemento
	            $strNome = $documentoDTO->getStrNomeSerie() . ' ' . $documentoDTO->getStrDescricaoProtocolo() . ' ' . $documentoDTO->getStrNumero();

                $html .= '<tr>';
                $html .= '<td> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; - ' . $strNome . '</td>';
                $html .= '<td>' . $strNumeroSEI . '</td>';
                $html .= '</tr>';

            }
            if ($bolExclusao) {
                unset($erroDocumentos['documentosEssenciais']);
            }
        }

        //FIM ESSENCIAL

        //COMPLEMENTAR
        if ($arrIdComplementar != null && count($arrIdComplementar) > 0) {

            $html .= '<tr>';
            $html .= '<td style="font-weight: bold;">&nbsp;&nbsp;&nbsp; - Documentos Complementares:</td>';
            $html .= '<td></td>';
            $html .= '</tr>';

            $objAnexoDTO = new AnexoDTO();
            $objAnexoDTO->retTodos(true);

            $objAnexoDTO->adicionarCriterio(array('IdProtocolo'),
                array(InfraDTO::$OPER_IN),
                array($arrIdComplementar));

            $arrAnexoDTOComplementar = $anexoRN->listarRN0218($objAnexoDTO);
            $bolExclusao = false;
            foreach ($arrAnexoDTOComplementar as $objAnexoComplementar) {

                $strNome = $objAnexoComplementar->getStrNome();
                $strTipoDocumento = "";
                $strNumeroSEI = $objAnexoComplementar->getStrProtocoloFormatadoProtocolo();

                $documentoDTO = new DocumentoDTO();

                $documentoDTO->retStrNumero();
                $documentoDTO->retStrNomeSerie();
                $documentoDTO->retStrDescricaoProtocolo();
                $documentoDTO->retStrProtocoloDocumentoFormatado();

                $documentoDTO->setDblIdDocumento($objAnexoComplementar->getDblIdProtocolo());
                $documentoDTO = $documentoRN->consultarRN0005($documentoDTO);
                if ($documentoDTO) {
                    $bolExclusao = true;
                } else {
                    $bolExclusao = false;
                }
                //concatenar tipo e complemento
	            $strNome = $documentoDTO->getStrNomeSerie() . ' ' . $documentoDTO->getStrDescricaoProtocolo() . ' ' . $documentoDTO->getStrNumero();

                $html .= '<tr>';
                $html .= '<td> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; - ' . $strNome . '</td>';
                $html .= '<td>' . $strNumeroSEI . '</td>';
                $html .= '</tr>';

            }
            if ($bolExclusao) {
                unset($erroDocumentos['documentosComplementares']);
            }
        }
        $objInfraException = new InfraException();
        $contador = 0;
        foreach ($erroDocumentos as $chave => $erro) {
            if($contador == 0){
                $objInfraException->adicionarValidacao('Não foi possível gerar o Recibo do Peticionamento devido a ausência de:');
            }
            if ($chave == 'documentoPrincipal') {
                $objInfraException->adicionarValidacao(' - Documento Principal.');
            } else if ($chave == 'documentosEssenciais') {
                $objInfraException->adicionarValidacao(' - Documentos Essenciais.');
            } else if ($chave == 'documentosComplementares') {
                $objInfraException->adicionarValidacao(' - Documentos Complementares.');
            }
            $contador++;
        }

        if ($objInfraException->contemValidacoes()) {
            $objInfraException->lancarValidacoes();
        }
        //FIM COMPLEMENTAR

        $html .= '</tbody></table>';

        $orgaoRN = new OrgaoRN();
        $objOrgaoDTO = new OrgaoDTO();
        $objOrgaoDTO->retTodos();
        $objOrgaoDTO->setNumIdOrgao($objUnidadeDTO->getNumIdOrgao());
        $objOrgaoDTO->setStrSinAtivo('S');
        $objOrgaoDTO = $orgaoRN->consultarRN1352($objOrgaoDTO);

        $html .= '<p>O Usuário Externo acima identificado foi previamente avisado que o peticionamento importa na aceitação dos termos e condições que regem o processo eletrônico, além do disposto no credenciamento prévio, e na assinatura dos documentos nato-digitais e declaração de que são autênticos os digitalizados, sendo responsável civil, penal e administrativamente pelo uso indevido. Ainda, foi avisado que os níveis de acesso indicados para os documentos estariam condicionados à análise por servidor público, que poderá alterá-los a qualquer momento sem necessidade de prévio aviso, e de que são de sua exclusiva responsabilidade:</p><ul><li>a conformidade entre os dados informados e os documentos;</li><li>a conservação dos originais em papel de documentos digitalizados até que decaia o direito de revisão dos atos praticados no processo, para que, caso solicitado, sejam apresentados para qualquer tipo de conferência;</li><li>a realização por meio eletrônico de todos os atos e comunicações processuais com o próprio Usuário Externo ou, por seu intermédio, com a entidade porventura representada;</li><li>a observância de que os atos processuais se consideram realizados no dia e hora do recebimento pelo SEI, considerando-se tempestivos os praticados até as 23h59min59s do último dia do prazo, considerado sempre o horário oficial de Brasília, independente do fuso horário em que se encontre;</li><li>a consulta periódica ao SEI, a fim de verificar o recebimento de intimações eletrônicas.</li></ul><p>A existência deste Recibo, do processo e dos documentos acima indicados pode ser conferida no Portal na Internet do(a) ' . $objOrgaoDTO->getStrDescricao() . '.</p>';

        return $html;

    }

    protected function gerarReciboSimplificadoIntercorrenteControlado($arr)
    {
        if (is_array($arr)) {

            $idProcedimento = array_key_exists('idProcedimento', $arr) ? $arr['idProcedimento'] : null;
            $idProcedimentoRel = array_key_exists('idProcedimentoRel', $arr) ? $arr['idProcedimentoRel'] : null;
            $idProcedimentoProcesso = array_key_exists('idProcedimentoProcesso', $arr) ? $arr['idProcedimentoProcesso'] : null;

            $stAnexado = $idProcedimento != $idProcedimentoProcesso;

            if (!is_null($idProcedimento)) {
                $objMdPetReciboDTO = new MdPetReciboDTO();

                $objMdPetReciboDTO->setNumIdProtocolo($idProcedimento);
                $objMdPetReciboDTO->setNumIdUsuario(SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno());
                $objMdPetReciboDTO->setDthDataHoraRecebimentoFinal(InfraData::getStrDataHoraAtual());
                $objMdPetReciboDTO->setStrIpUsuario(InfraUtil::getStrIpUsuario());
                $objMdPetReciboDTO->setStrSinAtivo('S');

                //recibo intercorrente
                if (!isset($arr['isRespostaIntimacao'])) {
                    $objMdPetReciboDTO->setStrStaTipoPeticionamento(MdPetReciboRN::$TP_RECIBO_INTERCORRENTE);
                } //recibo resposta a intimacao
                else {

                    $objDocIntimacaoRN = new MdPetIntProtocoloRN();
                    $objDocIntimacaoDTO = new MdPetIntProtocoloDTO();
                    $objDocIntimacaoDTO->setNumMaxRegistrosRetorno(1);
                    $objDocIntimacaoDTO->retTodos(true);
                    $objDocIntimacaoDTO->setNumIdMdPetIntimacao($arr['id_intimacao']);
                    $objDocIntimacaoDTO->setStrSinPrincipal('S');

                    $objDocIntimacaoDTO = $objDocIntimacaoRN->consultar($objDocIntimacaoDTO);

                    //setar o numero do doc principal da intimacao
                    $objMdPetReciboDTO->setStrTextoDocumentoPrincipalIntimac($objDocIntimacaoDTO->getStrProtocoloFormatadoDocumento());

                    //setando tipo de recibo
                    $objMdPetReciboDTO->setStrStaTipoPeticionamento(MdPetReciboRN::$TP_RECIBO_RESPOSTA_INTIMACAO);
                }

                if ($stAnexado) {
                    $objMdPetReciboDTO->setDblIdProtocoloRelacionado($idProcedimentoProcesso);
                }

                if (!is_null($idProcedimentoRel)) {
                    $objMdPetReciboDTO->setDblIdProtocoloRelacionado($idProcedimentoRel);
                }


                $objBD = new MdPetReciboBD($this->getObjInfraIBanco());
                $ret = $objBD->cadastrar($objMdPetReciboDTO);
                return $ret;
            }
        }

        return null;
    }


    /**
     * Short description of method alterarControlado
     *
     * @access protected
     * @param $objDTO
     * @return mixed
     * @author Jaqueline Mendes <jaqueline.mendes@castgroup.com.br>
     */
    protected function alterarControlado(MdPetReciboDTO $objDTO)
    {

        try {
            $objBD = new MdPetReciboBD($this->getObjInfraIBanco());
            $objBD->alterar($objDTO);

        } catch (Exception $e) {
            throw new InfraException ('Erro alterando Recibo Peticionamento, ', $e);
        }
    }

    protected function getUrlReciboConectado($arrParams)
    {
        $intercorrente = $arrParams[0];
        $objMdPetReciboDTO = $arrParams[1];
        $objMdPetAcessoExternoRN = new MdPetAcessoExternoRN();
        $idDocumentoRecibo = $objMdPetReciboDTO->getDblIdDocumento();
        $linkAssinado = '';

        //Se não possui o documento do sei de Recibo salva, redireciona para antiga tela
        if (is_null($idDocumentoRecibo)) {
            $linkAssinado = $this->_retornaLinkAntigoRecibo($intercorrente, $objMdPetReciboDTO->getNumIdReciboPeticionamento());
        } else {
            $idAcessoExLink = $objMdPetAcessoExternoRN->getIdAcessoExternoRecibo($objMdPetReciboDTO);

            if (!is_null($idAcessoExLink)) {
                $docLink = "documento_consulta_externa.php?id_acesso_externo=" . $idAcessoExLink . "&id_documento=" . $idDocumentoRecibo . "&id_orgao_acesso_externo=0";

                //se nao configurar acesso externo ANTES, a assinatura do link falha
                SessaoSEIExterna::getInstance()->configurarAcessoExterno($idAcessoExLink);

                $linkAssinado = PaginaSEIExterna::getInstance()->formatarXHTML(SessaoSEIExterna::getInstance()->assinarLink($docLink));

                //necessario fazer isso para nao quebrar a navegaçao (se nao fizer isso e tem clicar em qualquer outro link do usuario externo, quebra a sessao e usuario é enviado de volta para a tela de login externo (trata-se de funcionamento incorporado ao Core do SEI)
                SessaoSEIExterna::getInstance()->configurarAcessoExterno(0);
            } else {
                $linkAssinado = $this->_retornaLinkAntigoRecibo($intercorrente, $objMdPetReciboDTO->getNumIdReciboPeticionamento());
            }
        }

        return $linkAssinado;
    }


    private function _retornaLinkAntigoRecibo($intercorrente, $idRecibo)
    {

        $acao = $_GET['acao'];

        if ($intercorrente) {
            $urlLink = 'controlador_externo.php?&acao=md_pet_intercorrente_usu_ext_recibo_consultar&acao_origem=' . $acao . '&acao_retorno=' . $acao . '&id_md_pet_rel_recibo_protoc=' . $idRecibo;
        } else {
            $urlLink = 'controlador_externo.php?id_md_pet_rel_recibo_protoc=' . $idRecibo . '&acao=md_pet_usu_ext_recibo_consultar&acao_origem=' . $acao . '&acao_retorno=' . $acao;
        }

        $linkAssinado = PaginaSEIExterna::getInstance()->formatarXHTML(SessaoSEIExterna::getInstance()->assinarLink($urlLink));

        return $linkAssinado;
    }

}

?>