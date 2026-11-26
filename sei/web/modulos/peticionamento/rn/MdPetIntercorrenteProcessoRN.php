<?php
/**
 * ANATEL
 *
 * 25/11/2016 - criado por marcelo.bezerra - CAST
 * 18/05/2026 - editado por Gabriel Neves - ANATEL
 *
 */

require_once dirname(__FILE__) . '/../../../SEI.php';

class MdPetIntercorrenteProcessoRN extends MdPetProcessoRN
{

    public static $FORMATO_DIGITALIZADO = 'D';
    public static $FORMATO_NATO_DIGITAL = 'N';

    //Mensagens da falha na conclusão: a Resposta a Intimação usa o mesmo cadastro do Intercorrente (ADR-PET-007)
    public static $MSG_INTERCORRENTE_NAO_CONCLUIDO = 'Não foi possível concluir o Peticionamento Intercorrente, e nenhum documento foi incluído. Tente novamente. Se o erro continuar, entre em contato com a gestão do SEI deste órgão.';
    public static $MSG_INTERCORRENTE_NAO_DESFEITO = 'Não foi possível concluir o Peticionamento Intercorrente. Não tente novamente. Entre em contato com a gestão do SEI deste órgão e informe o processo %s.';
    public static $MSG_RESPOSTA_NAO_CONCLUIDA = 'Não foi possível concluir a Resposta a Intimação, e nenhum documento foi incluído. Tente novamente. Se o erro continuar, entre em contato com a gestão do SEI deste órgão.';
    public static $MSG_RESPOSTA_NAO_DESFEITA = 'Não foi possível concluir a Resposta a Intimação. Não tente novamente. Entre em contato com a gestão do SEI deste órgão e informe o processo %s.';


    private $objAcessoExternoDTO = null;
    private $objUsuarioDTO = null;
    private $objCargoDTO = null;
    private $objOrgaoDTO = null;
    private $objUnidadeDTO = null;
    private $objProcedimentoDTO = null;
    private $senha = null;
    private $reciboDTO = null;
    private $participantesDTO = null;
    private $documentoRecibo = null;
    private $arrDocumentos = array();

    //guarda o id da maior atividade de liberacao de acesso externo para comparar na hora de limpar o historico
    private $maxIdAtividade = 0;


    private $tipoConferenciaAlterado = false;

    public function __construct()
    {
        parent::__construct();

    }

    protected function inicializarObjInfraIBanco()
    {
        return BancoSEI::getInstance();
    }

    /**
     * Short description of method validarUnidadeProcessoConectado
     * Valida se as unidades dos processos abertos para esse procedimento estão ativas
     * @access protected
     * @param ProcedimentoDTO $objProcedimentoDTO
     * @return boolean
     * @author Jaqueline Mendes <jaqueline.mendes@cast.com.br>
     */
    protected function validarUnidadeProcessoConectado($objProcedimentoDTO)
    {
        $objAtividadeRN = new AtividadeRN();
        $arrObjUnidadeDTO = $objAtividadeRN->listarUnidadesTramitacao($objProcedimentoDTO);

        if (count($arrObjUnidadeDTO) == 0) {
            return false;
        }

        return true;
    }

    /**
     * Short description of method pesquisarProtocoloFormatadoConectado
     * Pesquisa o processo exatamente como foi digitado SEM considerar a formatação
     * @access protected
     * @param ProtocoloDTO $parObjProtocoloDTO
     * @return mixed
     * @author Jaqueline Mendes <jaqueline.mendes@cast.com.br>
     */
    protected function pesquisarProtocoloFormatadoConectado(ProtocoloDTO $parObjProtocoloDTO)
    {
        try {
            $objProtocoloRN = new ProtocoloRN();
            //busca pelo numero do processo
            $objProtocoloDTOPesquisa = new ProtocoloDTO();
            $objProtocoloDTOPesquisa->retDblIdProtocolo();
            $objProtocoloDTOPesquisa->retStrProtocoloFormatado();
            $objProtocoloDTOPesquisa->retStrStaProtocolo();
            $objProtocoloDTOPesquisa->retStrStaNivelAcessoGlobal();
            $objProtocoloDTOPesquisa->setNumMaxRegistrosRetorno(2);

            $strProtocoloPesquisa = InfraUtil::retirarFormatacao($parObjProtocoloDTO->getStrProtocoloFormatadoPesquisa(), false);

            $objProtocoloDTOPesquisa->setStrProtocoloFormatadoPesquisa($strProtocoloPesquisa);
            $objProtocoloDTOPesquisa->setStrStaProtocolo(ProtocoloRN::$TPP_PROCEDIMENTOS);
            $arrObjProtocoloDTO = $objProtocoloRN->listarRN0668($objProtocoloDTOPesquisa);

            if (count($arrObjProtocoloDTO) > 1) {
                return null;
            }

            if (count($arrObjProtocoloDTO) == 1) {
                return $arrObjProtocoloDTO[0];
            }


            return null;

        } catch (Exception $e) {
            throw new InfraException('Erro pesquisando protocolo.', $e);
        }
    }

    /**
     * Retorna a ultima unidade que o processo foi
     * Pesquisa o processo exatamente como foi digitado SEM considerar a formatação
     * @access protected
     * @param ProtocoloDTO $parObjProtocoloDTO
     * @return mixed
     * @author Jaqueline Mendes <jaqueline.mendes@cast.com.br>
     */
    protected function retornaUltimaUnidadeProcessoConcluidoConectado(AtividadeDTO $objAtividadeDTO)
    {

        $idUnidadeReabrirProcesso = null;
        $objAtividadeRN = new AtividadeRN();
        $objAtividadeDTO->retDthConclusao();
        $objAtividadeDTO->retNumIdUnidade();
        $objAtividadeDTO->setOrdDthConclusao(InfraDTO::$TIPO_ORDENACAO_DESC);
        $arrObjAtividadeDTO = $objAtividadeRN->listarRN0036($objAtividadeDTO);
        $objUltimaAtvProcesso = count($arrObjAtividadeDTO) > 0 ? current($arrObjAtividadeDTO) : null;
        if (!is_null($objUltimaAtvProcesso)) {
            $idUnidadeReabrirProcesso = $objUltimaAtvProcesso->getNumIdUnidade();
        }

        return $idUnidadeReabrirProcesso;
    }

    protected function gerarProcedimentoApi($params)
    {

        $objProcedimentoDTO = $params[0];
        $objCriterioIntercorrenteDTO = $params[1];
        $especificacao = $params[2];

        $protocoloDTO = new ProtocoloDTO();
        $protocoloDTO->retTodos();
        $protocoloDTO->setDblIdProtocolo($objProcedimentoDTO->getDblIdProcedimento());
        $protocoloRN = new ProtocoloRN();
        $protocoloDTO = $protocoloRN->consultarRN0186($protocoloDTO);

        // Verifica se o processo é anexado, se for, retorna a unidade do processo pai.
        if ($objProcedimentoDTO->getStrStaEstadoProtocolo() == ProtocoloRN::$TE_PROCEDIMENTO_ANEXADO) {
            $objRelProtocoloProtocoloDTO = new RelProtocoloProtocoloDTO();
            $objRelProtocoloProtocoloDTO->retDblIdProtocolo1();
            $objRelProtocoloProtocoloDTO->retStrProtocoloFormatadoProtocolo1();
            $objRelProtocoloProtocoloDTO->setDblIdProtocolo2($objProcedimentoDTO->getDblIdProcedimento());
            $objRelProtocoloProtocoloDTO->setStrStaAssociacao(RelProtocoloProtocoloRN::$TA_PROCEDIMENTO_ANEXADO);

            $objRelProtocoloProtocoloRN = new RelProtocoloProtocoloRN();
            $objRelProtocoloProtocoloDTO = $objRelProtocoloProtocoloRN->consultarRN0841($objRelProtocoloProtocoloDTO);


            $idUnidadeAbrirNovoProcesso = $this->retornaUltimaUnidadeProcessoAberto($objRelProtocoloProtocoloDTO->getDblIdProtocolo1());
        } else if ($protocoloDTO->getStrStaNivelAcessoLocal() == ProtocoloRN::$NA_SIGILOSO ||
            $protocoloDTO->getStrStaNivelAcessoGlobal() == ProtocoloRN::$NA_SIGILOSO) {

            $objMdPetIntercorrenteAndamentoSigiloso = new MdPetIntercorrenteAndamentoSigilosoRN();

            $idUnidadeAbrirNovoProcesso = $objMdPetIntercorrenteAndamentoSigiloso->retornaIdUnidadeAberturaProcesso($objProcedimentoDTO->getDblIdProcedimento());

        } else {
            $idUnidadeAbrirNovoProcesso = $this->retornaUltimaUnidadeProcessoAberto($objProcedimentoDTO->getDblIdProcedimento());
        }

        //Se não existe Unidade em Aberto, busca pelas Concluídas
        if (is_null($idUnidadeAbrirNovoProcesso)) {
            $idUnidadeAbrirNovoProcesso = $this->_getUnidadesProcessoConcluido($objProcedimentoDTO->getDblIdProcedimento());
        } else {
            // inicio da verificação da unidade ativa, caso não esteja tenta buscar uma unidade ativa para reabrir o processo.
            $objRNGerais = new MdPetRegrasGeraisRN();
            $objUnidadeDTO = $objRNGerais->getObjUnidadePorId($idUnidadeAbrirNovoProcesso);

            if ($objUnidadeDTO->getStrSinAtivo() == 'N' || $objUnidadeDTO->getStrSinEnvioProcesso() == 'N' ) {
                $idUnidadeAbrirNovoProcesso = null;
                $objAtividadeRN = new MdPetIntercorrenteAtividadeRN();
                $arrObjUnidadeDTO = $objAtividadeRN->listarUnidadesTramitacao($objProcedimentoDTO);

                foreach ($arrObjUnidadeDTO as $itemObjUnidadeDTO) {
                    if ($itemObjUnidadeDTO->getStrSinAtivo() == 'S' && $objUnidadeDTO->getStrSinEnvioProcesso() == 'S') {
                        $idUnidadeAbrirNovoProcesso = $itemObjUnidadeDTO->getNumIdUnidade();
                    }
                }
            }
        }


        if ($idUnidadeAbrirNovoProcesso == null) {
            $objInfraException = new InfraException();
            $objInfraException->adicionarValidacao('O processo indicado não aceita peticionamento intercorrente. Utilize o Peticionamento de Processo Novo para protocolizar sua demanda.');
            $objInfraException->lancarValidacoes();
            return false;
        }
        // fim da verificação da unidade ativa

        // Salva um processo do tipo padrão selecionado
        $this->simularLogin($idUnidadeAbrirNovoProcesso);

        $objEntradaGerarProcedimentoAPI = new EntradaGerarProcedimentoAPI();
        $arrProcedimentoRelacionado = array($objProcedimentoDTO->getDblIdProcedimento());

        $objProcedimentoAPI = new ProcedimentoAPI();

        //Tipo Procedimento
        $objTipoProcedimentoDTO = new TipoProcedimentoDTO();
        $objTipoProcedimentoDTO->setBolExclusaoLogica(false);
        $objTipoProcedimentoDTO->setDistinct(true);
        $objTipoProcedimentoDTO->retStrSinAtivo();
        $objTipoProcedimentoDTO->setNumIdTipoProcedimento($objProcedimentoDTO->getNumIdTipoProcedimento());
        $objTipoProcedimentoDTO->setNumMaxRegistrosRetorno(1);
        $objTipoProcedimentoRN = new TipoProcedimentoRN();
        $objTipoProcedimentoDTO = $objTipoProcedimentoRN->consultarRN0267($objTipoProcedimentoDTO);

        $intTipoProcesso = $this->_definirTipoProcesso($objTipoProcedimentoDTO, $objCriterioIntercorrenteDTO, $objProcedimentoDTO, $idUnidadeAbrirNovoProcesso);

        $objProcedimentoAPI->setIdTipoProcedimento($intTipoProcesso);

        if ($especificacao != null) {
            $objProcedimentoAPI->setEspecificacao($especificacao);
        }

        $objParticipanteDTO = new ParticipanteDTO();
        $objParticipanteDTO->retNumIdContato();
        $objParticipanteDTO->setDblIdProtocolo($objProcedimentoDTO->getDblIdProcedimento());
        $objParticipanteDTO->setStrStaParticipacao(ParticipanteRN::$TP_INTERESSADO);
        $arrObjParticipanteDTO = (new ParticipanteRN())->listarRN0189($objParticipanteDTO);

        $arrInteressados = array();
        foreach ($arrObjParticipanteDTO as $contato){
            $objContatoDTO = new ContatoDTO();
            $objContatoDTO->setNumIdContato($contato->getNumIdContato());
            $objContatoDTO->retTodos(true);
            $objContato = (new ContatoRN())->consultarRN0324($objContatoDTO);

	        if(!empty($objContato)){
	        	$objParticipanteContato = new ContatoAPI();
		        $objParticipanteContato->setIdContato($objContato->getNumIdContato());
		        $objParticipanteContato->setSigla($objContato->getStrSigla());
		        $objParticipanteContato->setNome($objContato->getStrNome());
		        array_push($arrInteressados, $objParticipanteContato);
	        }
         
        }

        $objProcedimentoAPI->setInteressados($arrInteressados);

        $objEntradaGerarProcedimentoAPI->setProcedimento($objProcedimentoAPI);
        $objEntradaGerarProcedimentoAPI->setProcedimentosRelacionados($arrProcedimentoRelacionado);

        $objSEIRN = new SeiRN();
        return $objSEIRN->gerarProcedimento($objEntradaGerarProcedimentoAPI);
    }

    private function _getUnidadesProcessoConcluido($idProcedimento)
    {

        $idUnidadeReabrirProcesso = null;
        $objAtividadeRN = new AtividadeRN();
        $objAtividadeDTO = new MdPetAtividadeIntercorrenteDTO();
        $objAtividadeDTO->setDblIdProcedimentoProtocolo($idProcedimento);
        $objAtividadeDTO->retDthConclusao();
        $objAtividadeDTO->retNumIdUnidade();

        //Busca somente as Unidades Ativas
        $objAtividadeDTO->setStrSinAtivoUnidade('S');
        $objAtividadeDTO->setOrdDthConclusao(InfraDTO::$TIPO_ORDENACAO_DESC);
        $objAtividadeDTO->setNumIdTarefa(array(TarefaRN::$TI_CONCLUSAO_PROCESSO_UNIDADE, TarefaRN::$TI_CONCLUSAO_AUTOMATICA_UNIDADE, TarefaRN::$TI_CONCLUSAO_PROCESSO_USUARIO), InfraDTO::$OPER_IN);
        $objAtividadeDTO->setNumMaxRegistrosRetorno(1);

        if ($objAtividadeRN->contarRN0035($objAtividadeDTO) > 0) {
            $objDTO = $objAtividadeRN->consultarRN0033($objAtividadeDTO);
            $idUnidadeReabrirProcesso = $objDTO->getNumIdUnidade();
        }

        return $idUnidadeReabrirProcesso;
    }

    /**
     * @param $idTpProcedimento
     * @param $idUnidadeAbertura
     * @return bool
     */
    private function _verifUnidadePossuiPermissaoAberturaTipoProcesso($idTpProcedimento, $idUnidadeAbertura)
    {

        $objRNGerais = new MdPetRegrasGeraisRN();
        $objUnidadeDTO = $objRNGerais->getObjUnidadePorId($idUnidadeAbertura);
        $idOrgaoUnidade = !is_null($objUnidadeDTO) ? $objUnidadeDTO->getNumIdOrgao() : null;

        if (!is_null($idOrgaoUnidade)) {
            $idOrgaoUnidade = trim($idOrgaoUnidade);
            $objTipoProcedRestricaoRN = new TipoProcedRestricaoRN();
            $objTipoProcedRestricaoDTO = new TipoProcedRestricaoDTO();
            $objTipoProcedRestricaoDTO->setNumIdTipoProcedimento($idTpProcedimento);
            $objTipoProcedRestricaoDTO->retNumIdUnidade();

            //Se não encontrar nenhum dado vinculado a esse Tipo de Processo na tabela de Rel, é porque não existe restrição
            if ($objTipoProcedRestricaoRN->contar($objTipoProcedRestricaoDTO) == 0) {
                return true;
            } else {

                //Se a Unidade estiver nula é porque pode ser Utilizada em qualquer uma do órgão logado em questão
                $objTipoProcedRestricaoDTO2 = clone($objTipoProcedRestricaoDTO);
                $objTipoProcedRestricaoDTO2->setNumMaxRegistrosRetorno(1);
                $objTipoProcedRestricaoDTO2->setNumIdUnidade(null);
                $objTipoProcedRestricaoDTO2->setNumIdOrgao($idOrgaoUnidade);
                if ($objTipoProcedRestricaoRN->contar($objTipoProcedRestricaoDTO2) > 0) {
                    return true;
                }

                //Se a Unidade estiver nula e um órgão diferente do da Unidade Atual estiver cadastrado
                $objTipoProcedRestricaoDTO3 = clone($objTipoProcedRestricaoDTO);
                $objTipoProcedRestricaoDTO3->setNumMaxRegistrosRetorno(1);
                $objTipoProcedRestricaoDTO3->setNumIdUnidade(null);
                $objTipoProcedRestricaoDTO3->setNumIdOrgao($idOrgaoUnidade, InfraDTO::$OPER_DIFERENTE);
                if ($objTipoProcedRestricaoRN->contar($objTipoProcedRestricaoDTO3) > 0) {
                    return false;
                }


                //Se não, verifica se a Unidade em Questão possui autorização para o Tipo de Processo
                $objTipoProcedRestricaoDTO4 = clone($objTipoProcedRestricaoDTO);
                $objTipoProcedRestricaoDTO4->setNumIdUnidade($idUnidadeAbertura);
                $objTipoProcedRestricaoDTO4->setNumIdOrgao($idOrgaoUnidade);
                if ($objTipoProcedRestricaoRN->contar($objTipoProcedRestricaoDTO4) > 0) {
                    return true;
                }
            }
        }

        return false;
    }

    private function _definirTipoProcesso($objTipoProcedimentoDTO, $objCriterioIntercorrenteDTO, $objProcedimentoDTO, $idUnidade)
    {
        $intIdProcedimento = null;
        $idTipoProcCriterioPadrao = $objCriterioIntercorrenteDTO->getNumIdTipoProcedimento();
        $idTipoProcIgualIndicado = $objProcedimentoDTO->getNumIdTipoProcedimento();

        if ($objTipoProcedimentoDTO->getStrSinAtivo() == 'S') {
            $isTipoProcessoLiberado = $this->_verifUnidadePossuiPermissaoAberturaTipoProcesso($idTipoProcIgualIndicado, $idUnidade);
            $intIdProcedimento = $isTipoProcessoLiberado ? $idTipoProcIgualIndicado : $idTipoProcCriterioPadrao;
        } else {
            $intIdProcedimento = $idTipoProcCriterioPadrao;
        }

        return $intIdProcedimento;
    }

    /**
     * Função responsável por Retornar a última unidade em que o processo ESTÁ aberto agora
     * @param $idProcedimento
     * @return  string $idUnidade
     */
    protected function retornaUltimaUnidadeProcessoAbertoConectado($params)
    {

        if (is_array($params)) {
            $idProcedimento = $params[0];
            $idUnidadeEspecifica = $params[1];
            $retornarUsuarioAtribuicao = true;
        } else {
            $idProcedimento = $params;
        }

        $objSEIRN = new SeiRN();
        $objProcedimentoDTO = new ProcedimentoDTO();
        $objProcedimentoDTO->retTodos(true);
        $objProcedimentoDTO->setDblIdProcedimento($idProcedimento);
        $objProcedimentoRN = new ProcedimentoRN();
        $objProcedimentoDTO = $objProcedimentoRN->consultarRN0201($objProcedimentoDTO);

        $objEntradaConsultaProcApi = new EntradaConsultarProcedimentoAPI();
        $objEntradaConsultaProcApi->setIdProcedimento($idProcedimento);
        $objEntradaConsultaProcApi->setSinRetornarUnidadesProcedimentoAberto('S');
        $objEntradaConsultaProcApi->setSinRetornarUltimoAndamento('S');
        $objEntradaConsultaProcApi->setSinRetornarAndamentoConclusao('N');

        /**
         * @var $saidaConsultarProcedimentoAPI SaidaConsultarProcedimentoAPI
         */
        if ($objProcedimentoDTO->getStrStaNivelAcessoGlobalProtocolo() == ProtocoloRN::$NA_SIGILOSO ||
            $objProcedimentoDTO->getStrStaNivelAcessoLocalProtocolo() == ProtocoloRN::$NA_SIGILOSO) {
            $md = new MdPetIntercorrenteAndamentoSigilosoRN();
            $saidaConsultarProcedimentoAPI = $md->consultarProcedimento($objEntradaConsultaProcApi);
        } else {
            $saidaConsultarProcedimentoAPI = $objSEIRN->consultarProcedimento($objEntradaConsultaProcApi);
        }

        //informações da tarefa de conclusão de processo na unidade
        $tarefaRN = new TarefaRN();
        $tarefaDTO = new TarefaDTO();
        $tarefaDTO->retNumIdTarefa();
        $tarefaDTO->retStrNome();
        $tarefaDTO->setNumIdTarefa(TarefaRN::$TI_CONCLUSAO_PROCESSO_UNIDADE);
        $arrTarefaDTO = $tarefaRN->listar($tarefaDTO);
        $tarefaDTO = $arrTarefaDTO[0];

        //lista de unidades nas quais o processo ainda encontra-se aberto
        $arrUnidadesAbertas = $saidaConsultarProcedimentoAPI->getUnidadesProcedimentoAberto();

        //o processo encontra-se aberto em pelo menos uma unidade
        if (is_array($arrUnidadesAbertas) && count($arrUnidadesAbertas) > 0) {

            $arrIdUnidade = array();
            $arrIdUsuarioAtribuicao = array();

            foreach ($arrUnidadesAbertas as $unidadeAberta) {

                $unidadeIncluir = true;

                // mesma unidade da geradora
                if ($idUnidadeEspecifica && $idUnidadeEspecifica != $unidadeAberta->getUnidade()->getIdUnidade()) {
                    $unidadeIncluir = false;
                }

                if ($unidadeIncluir) {
                    $arrIdUnidade[] = $unidadeAberta->getUnidade()->getIdUnidade();
                    if ($retornarUsuarioAtribuicao) {
                        if (!empty($unidadeAberta->getUsuarioAtribuicao())) {
                            $arrIdUsuarioAtribuicao[$unidadeAberta->getUnidade()->getIdUnidade()] = $unidadeAberta->getUsuarioAtribuicao()->getIdUsuario();
                        }
                    }
                }

            }

            $objEntradaAndamentos = new EntradaListarAndamentosAPI();
            $objEntradaAndamentos->setIdProcedimento($idProcedimento);
            $objEntradaAndamentos->setTarefas(array(TarefaRN::$TI_GERACAO_PROCEDIMENTO, TarefaRN::$TI_REABERTURA_PROCESSO_UNIDADE, TarefaRN::$TI_PROCESSO_REMETIDO_UNIDADE));
            $arrAndamentos = $objSEIRN->listarAndamentos($objEntradaAndamentos);

            foreach ($arrAndamentos as $andamento) {
                $idUnidadeAndamento = $andamento->getUnidade()->getIdUnidade();
                if (in_array($idUnidadeAndamento, $arrIdUnidade)) {
                    if ($retornarUsuarioAtribuicao) {
                        $arrUnidadeAndamento = array();
                        $arrUnidadeAndamento[0] = $idUnidadeAndamento;
                        $arrUnidadeAndamento[1] = $arrIdUsuarioAtribuicao[$idUnidadeAndamento];
                        $idUnidadeAndamento = $arrUnidadeAndamento;
                    }


                    return $idUnidadeAndamento;
                }
            }

        } //o processo nao esta aberto em nenhuma unidade, nao ha id para ser retornado
        else {
            return null;
        }

    }

    /**
     * Função responsável por Retornar todas as unidades em que o processo está aberto
     *   buscado em: ProcedimentoINT.php - montarAcoesArvore() - linhas 766 a 870
     * @param $idProcedimento
     * @return AtividadeDTO $arrObjAtividadeDTO
     * @since  07/03/2017
     * @author CAST - castgroup.com.br
     */
    public function retornaUnidadesProcessoAbertoConectado($idProcedimento)
    {

        $arrAtividade = array();

        $objProcedimentoDTO = new ProcedimentoDTO();
        $objProcedimentoDTO->setDblIdProcedimento($idProcedimento);
        $objProcedimentoDTO->retStrStaNivelAcessoGlobalProtocolo();
        $objProcedimentoDTO->retStrStaEstadoProtocolo();
        $objProcedimentoDTO = (new ProcedimentoRN())->consultarRN0201($objProcedimentoDTO);

        $strStaNivelAcessoGlobal = $objProcedimentoDTO->getStrStaNivelAcessoGlobalProtocolo();

        $bolFlagSobrestado = false;
        if ($objProcedimentoDTO->getStrStaEstadoProtocolo() == ProtocoloRN::$TE_PROCEDIMENTO_SOBRESTADO) {
            $bolFlagSobrestado = true;
        }

        $objAtividadeRN = new AtividadeRN();
        $objAtividadeDTO = new AtividadeDTO();
        $objAtividadeDTO->setDistinct(true);
        $objAtividadeDTO->retNumIdUnidade();
        $objAtividadeDTO->retStrSiglaUnidade();
        $objAtividadeDTO->retStrDescricaoUnidade();

        // $objAtividadeDTO->setOrdStrSiglaUnidade(InfraDTO::$TIPO_ORDENACAO_ASC); // No make sense

        if ($strStaNivelAcessoGlobal == ProtocoloRN::$NA_SIGILOSO) {
            $objAtividadeDTO->retNumIdUsuario();
            $objAtividadeDTO->retStrSiglaUsuario();
            $objAtividadeDTO->retStrNomeUsuario();
            $objAtividadeDTO->setOrdStrSiglaUsuario(InfraDTO::$TIPO_ORDENACAO_DESC);
        } else {
            $objAtividadeDTO->retNumIdUsuarioAtribuicao();
            $objAtividadeDTO->retStrSiglaUsuarioAtribuicao();
            $objAtividadeDTO->retStrNomeUsuarioAtribuicao();

            //ordena descendente pois no envio de processo que já existe na unidade e está atribuído ficará com mais de um andamento em aberto
            //desta forma os andamentos com usuário nulo (envios do processo) serão listados depois
            $objAtividadeDTO->setOrdStrSiglaUsuarioAtribuicao(InfraDTO::$TIPO_ORDENACAO_DESC);
        }

        $objAtividadeDTO->setDblIdProtocolo($idProcedimento);
        $objAtividadeDTO->setDthConclusao(null);

        //sigiloso sem credencial nao considera o usuario atual
        if ($strStaNivelAcessoGlobal == ProtocoloRN::$NA_SIGILOSO) {

            $objAcessoDTO = new AcessoDTO();
            $objAcessoDTO->setDistinct(true);
            $objAcessoDTO->retNumIdUsuario();
            $objAcessoDTO->setDblIdProtocolo($idProcedimento);
            $objAcessoDTO->setStrStaTipo(AcessoRN::$TA_CREDENCIAL_PROCESSO);
            $arrObjAcessoDTO = (new AcessoRN())->listar($objAcessoDTO);

            $arrIdUsuario = InfraArray::converterArrInfraDTO($arrObjAcessoDTO, 'IdUsuario');

            $objAtividadeDTO->setNumIdUsuario($arrIdUsuario, InfraDTO::$OPER_IN);

        }

        $arrObjAtividadeDTO = $objAtividadeRN->listarRN0036($objAtividadeDTO);

        if ($strStaNivelAcessoGlobal != ProtocoloRN::$NA_SIGILOSO) {
            //filtra andamentos com indicação de usuário atribuído
            $arrObjAtividadeDTO = InfraArray::distinctArrInfraDTO($arrObjAtividadeDTO, 'SiglaUnidade');
        }
        return $arrObjAtividadeDTO;
    }

    protected function incluirDocumentosApi($objProcedimentoDTO, $arrObjDocumentoAPI, $idUnidadeRespostaIntimacao)
    {

        $arrObjReciboDocPet = array();
        $objSEIRN = new SeiRN();

        $idUnidadeProcesso  = $idUnidadeRespostaIntimacao;
        $naGlobal           = $objProcedimentoDTO->getStrStaNivelAcessoGlobalProtocolo();
        $naLocal            = $objProcedimentoDTO->getStrStaNivelAcessoLocalProtocolo();
        $isSigiloso         = in_array(ProtocoloRN::$NA_SIGILOSO, [$naGlobal, $naLocal]);
        $idProcedimento     = $objProcedimentoDTO->getDblIdProcedimento();

        /**
         * Identifica se a ação é para resposta a intimação
         */
        if (is_null($idUnidadeProcesso)) {
           
            if($isSigiloso){

                $arrUnidadesProcessoAberto = $this->retornaUnidadesProcessoAberto($idProcedimento);
                $idUnidadeProcesso = !empty($arrUnidadesProcessoAberto) ? end( $arrUnidadesProcessoAberto )->getNumIdUnidade() : null;

            }else{

                $idUnidadeProcesso = $this->retornaUltimaUnidadeProcessoAberto($idProcedimento);

            }

        }

        // inicio da verificação da unidade ativa, caso não esteja tenta buscar uma unidade ativa para reabrir o processo.
        if (is_numeric($idUnidadeProcesso)) {
            
            $unidadeDTO = new UnidadeDTO();
            $unidadeDTO->retTodos();
            $unidadeDTO->setBolExclusaoLogica(false);
            $unidadeDTO->retStrSinAtivo();
            $unidadeDTO->setNumIdUnidade($idUnidadeProcesso);
            $unidadeRN = new UnidadeRN();
            $objUnidadeDTO = $unidadeRN->consultarRN0125($unidadeDTO);
        }

        //resolver um problema de " Call to a member function getStrSinAtivo() on null"
        if ($objUnidadeDTO == null || !$objUnidadeDTO->isSetStrSinAtivo() || $objUnidadeDTO->getStrSinAtivo() == 'N' || $objUnidadeDTO->getStrSinEnvioProcesso() == 'N') {
            $idUnidadeProcesso = null;

            $objMdPetAtividadeRN = new MdPetAtividadeRN();
            $arrObjMdPetAtividadeDTO = $objMdPetAtividadeRN->listarUnidadesTramitacao($objProcedimentoDTO);

            foreach ($arrObjMdPetAtividadeDTO as $itemObjMdPetAtividadeDTO) {
                $unidadeDTO = new UnidadeDTO();
                $unidadeDTO->retTodos();
                $unidadeDTO->setBolExclusaoLogica(false);
                $unidadeDTO->setNumIdUnidade($itemObjMdPetAtividadeDTO->getNumIdUnidade());
                $unidadeRN = new UnidadeRN();
                $objUnidadeDTO = $unidadeRN->consultarRN0125($unidadeDTO);
                if ($objUnidadeDTO->getStrSinAtivo() == 'S' && $objUnidadeDTO->getStrSinEnvioProcesso() == 'S') {
                    $idUnidadeProcesso = $objUnidadeDTO->getNumIdUnidade();
                }
            }
        }

        if ($idUnidadeProcesso == null) {
            //trata-se de processo sigiloso global
            $objMdPetIntercorrenteAndamentoSigilosoRN = new MdPetIntercorrenteAndamentoSigilosoRN();
            $idUnidadeProcesso = $objMdPetIntercorrenteAndamentoSigilosoRN->retornaIdUnidadeAberturaProcesso($_POST['id_procedimento']);
        }

        // SIGILOSO - conceder credencial
        if($isSigiloso){

            if (is_numeric(SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno())) {
            
                $objMdPetProcedimentoRN = new MdPetProcedimentoRN();
                $objConcederCredencial = $objMdPetProcedimentoRN->concederCredencial(array($objProcedimentoDTO, $idUnidadeProcesso));
                
                if (is_array($objConcederCredencial) && count($objConcederCredencial) > 0) {
                    $numIdUsuarioExterno = $objConcederCredencial[4];
                    $idUnidade = $objConcederCredencial[6];
                    SessaoSEI::getInstance()->simularLogin(null, null, $numIdUsuarioExterno, $idUnidade);
                }
            
            }

        } else {
            SessaoSEI::getInstance()->simularLogin(null, null, SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno(), $idUnidadeProcesso);
            $idUnidade = $idUnidadeProcesso;
        }

        // SIGILOSO - conceder credencial - FIM

        // se não usar o //$this->simularLogin($idUnidadeProcesso);
        if(!empty($idUnidade)){
            $this->setUnidadeDTO($idUnidade);
        }else{
            $objInfraException = new InfraException();
            $objInfraException->lancarValidacao('O processo indicado não aceita peticionamento intercorrente. Utilize o Peticionamento de Processo Novo para protocolizar sua demanda.');
        }

        foreach ($arrObjDocumentoAPI as $documentoAPI) {

            $saidaIncluirDocumentoAPI = $objSEIRN->incluirDocumento($documentoAPI);

            // Remententes
            $idsParticipantes = array();
            $idsInteressados = array();

            $objParticipante = new ParticipanteDTO();
            $objParticipante->setDblIdProtocolo($saidaIncluirDocumentoAPI->getIdDocumento());
            $objParticipante->setNumIdContato($this->getContatoDTOUsuarioLogado()->getNumIdContato());
            $objParticipante->setNumIdUnidade($idUnidadeProcesso);
            $objParticipante->setStrStaParticipacao(ParticipanteRN::$TP_REMETENTE);
            $objParticipante->setNumSequencia(0);
            $idsParticipantes[] = $objParticipante;

            $objMdPetParticipanteRN = new MdPetParticipanteRN();
            $arrInteressado = array();
            $arrInteressado[0] = $saidaIncluirDocumentoAPI->getIdDocumento();
            $arrInteressado[1] = $idsParticipantes;

            $objMdPetParticipanteRN->setInteressadosRemetentesProcedimentoDocumento($arrInteressado);
            // Processo - Interessados - FIM

            $formato = MdPetIntercorrenteINT::retornaTipoFormatoDocumento($saidaIncluirDocumentoAPI);

            $objReciboDocAnexPetDTO = MdPetIntercorrenteINT::retornaObjReciboDocPreenchido(array($saidaIncluirDocumentoAPI->getIdDocumento(), $formato));

            array_push($arrObjReciboDocPet, $objReciboDocAnexPetDTO);

            $this->assinarETravarDocumento($saidaIncluirDocumentoAPI);

            //Participantes - Atualizando
            $this->setParticipante($idsInteressados);

        }
        // SIGILOSO - cassarcredencial
        if($isSigiloso){
            if (is_numeric(SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno())) {
                $objMdPetProcedimentoRN = new MdPetProcedimentoRN();
                $objMdPetProcedimentoRN->cassarCredencial($objConcederCredencial);
                $objMdPetProcedimentoRN->excluirAndamentoCredencial($objConcederCredencial);
            }
        }
        // SIGILOSO - cassarcredencial - FIM

        return $arrObjReciboDocPet;
    }

    private function abrirDocumentoParaAssinatura($dblIdDocumento)
    {
        $documentoRN = new DocumentoRN();
        $documentoBD = new DocumentoBD($this->getObjInfraIBanco());

        $documentoDTO = new DocumentoDTO();
        $documentoDTO->retTodos(true);
        $documentoDTO->setDblIdDocumento($dblIdDocumento);
        $documentoDTO = $documentoRN->consultarRN0005($documentoDTO);

        $this->tipoConferenciaAlterado = false;
        //setar temporariamente e depois remover da entidade
        if (!$documentoDTO->getNumIdTipoConferencia()) {
            // buscando o menor tipo de conferencia
            $tipoConferenciaDTOConsulta = new TipoConferenciaDTO();
            $tipoConferenciaDTOConsulta->retTodos();
            $tipoConferenciaDTOConsulta->setStrSinAtivo('S');
            $tipoConferenciaDTOConsulta->setOrd('IdTipoConferencia', InfraDTO::$TIPO_ORDENACAO_ASC);
            $tipoConferenciaRN = new TipoConferenciaRN();
            $arrTipoConferenciaDTO = $tipoConferenciaRN->listar($tipoConferenciaDTOConsulta);
            $numIdTipoConferencia = $arrTipoConferenciaDTO[0]->getNumIdTipoConferencia();
            // fim buscando o menor tipo de conferencia

            //setando um tipo de conferencia padrao (que sera removido depois), apenas para passar na validação
            $documentoDTO->setNumIdTipoConferencia($numIdTipoConferencia);
            $documentoAlteracaoDTO = new DocumentoDTO();
            $documentoAlteracaoDTO->retDblIdDocumento();
            $documentoAlteracaoDTO->retNumIdTipoConferencia();
            $documentoAlteracaoDTO->setDblIdDocumento($documentoDTO->getDblIdDocumento());
            $documentoAlteracaoDTO = $documentoRN->consultarRN0005($documentoAlteracaoDTO);

            $documentoAlteracaoDTO->setNumIdTipoConferencia($numIdTipoConferencia);
            $documentoBD->alterar($documentoAlteracaoDTO);
            $this->tipoConferenciaAlterado = true;
        }
        return $documentoDTO;
    }

    private function fecharDocumentoParaAssinatura($documentoDTO)
    {
        $documentoBD = new DocumentoBD($this->getObjInfraIBanco());
        if ($this->tipoConferenciaAlterado) {
            $documentoDTO->setNumIdTipoConferencia(null);
            $documentoBD->alterar($documentoDTO);
        }
        //nao aplicando metodo alterar da RN de Documento por conta de regras de negocio muito especificas aplicadas ali
        $documentoDTO->setStrSinBloqueado('S');
        $documentoBD->alterar($documentoDTO);
        //remover a liberação de acesso externo //AcessoRN.excluir nao permite exclusao, por isso chame AcessoExternoBD diretamente daqui
    }

    private function abrirAcessoParaAssinatura($orgaoDTO, $documentoDTO, $objParticipanteDTO)
    {
        //liberando assinatura externa para o documento
        $objAcessoExternoDTO = new AcessoExternoDTO();

        //trocado de $TA_ASSINATURA_EXTERNA para $TA_SISTEMA para evitar o envio de email de notificação
        $objAcessoExternoDTO->setStrStaTipo(AcessoExternoRN::$TA_ASSINATURA_EXTERNA);
        $objAcessoExternoDTO->setStrEmailUnidade($orgaoDTO->getStrEmailContato()); //informando o email do orgao associado a unidade
        $objAcessoExternoDTO->setDblIdDocumento($documentoDTO->getDblIdDocumento());
        $objAcessoExternoDTO->setNumIdParticipante($objParticipanteDTO->getNumIdParticipante());
        $objAcessoExternoDTO->setNumIdUsuarioExterno(SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno());
        $objAcessoExternoDTO->setStrSinProcesso('N'); //visualizacao integral do processo

        $objMdPetAcessoExternoRN = new MdPetAcessoExternoRN();
        $this->objAcessoExternoDTO = $objMdPetAcessoExternoRN->cadastrarAcessoExternoCore($objAcessoExternoDTO);
    }

    private function fecharAcessoParaAssinatura()
    {
        $objAcessoExternoBD = new AcessoExternoBD($this->getObjInfraIBanco());
        $objAcessoExternoBD->excluir($this->objAcessoExternoDTO);
    }

    private function retornarParticipante($objUsuarioDTO, $objUnidadeDTO, $objProcedimentoDTO)
    {
        $objParticipanteDTO = new ParticipanteDTO();
        $objParticipanteDTO->retTodos(true);
        $objParticipanteDTO->retStrSiglaContato();
        $objParticipanteDTO->retStrNomeContato();
        $objParticipanteDTO->retNumIdUnidade();
        $objParticipanteDTO->retDblIdProtocolo();
        $objParticipanteDTO->retNumIdParticipante();
        //FK de BD (ak1_participante) trata somente id_contato, id_protocolo, sta_participacao, então desconsiderar unidade
        $objParticipanteDTO->setNumIdContato($objUsuarioDTO->getNumIdContato());
        $objParticipanteDTO->setDblIdProtocolo($objProcedimentoDTO->getDblIdProcedimento());

        $objParticipanteRN = new ParticipanteRN();
        $arrObjParticipanteDTO = $objParticipanteRN->listarRN0189($objParticipanteDTO);

        if ($arrObjParticipanteDTO == null || count($arrObjParticipanteDTO) == 0) {
            //cadastrar o participante
            $objParticipanteDTO = new ParticipanteDTO();
            $objParticipanteDTO->setNumIdContato($objUsuarioDTO->getNumIdContato());
            $objParticipanteDTO->setDblIdProtocolo($objProcedimentoDTO->getDblIdProcedimento());
            $objParticipanteDTO->setStrStaParticipacao(ParticipanteRN::$TP_ACESSO_EXTERNO);
            $objParticipanteDTO->setNumIdUnidade($objUnidadeDTO->getNumIdUnidade());
            $objParticipanteDTO->setNumSequencia(0);

            $participanteDTO = $objParticipanteRN->cadastrarRN0170($objParticipanteDTO);

            $objParticipanteDTO = new ParticipanteDTO();
            $objParticipanteDTO->retTodos(true);
            $objParticipanteDTO->setNumIdParticipante($participanteDTO->getNumIdParticipante());
            $ret = $objParticipanteRN->consultarRN1008($objParticipanteDTO);
        } else {
            $ret = $arrObjParticipanteDTO[0];
        }
        $this->setParticipante($ret);
        return $ret;
    }

    public function assinarETravarDocumento($documento)
    {
        //consultar email da unidade (orgao)
        $orgaoDTO = $this->getOrgaoDTO();
        $cargoDTO = $this->getCargoDTO();
        $objUsuarioDTO = $this->getUsuarioDTO();
        $objUnidadeDTO = $this->getUnidadeDTO();
        $objProcedimentoDTO = $this->getProcedimentoDTO();

        $documentoDTO = $documento;
        if ($documento instanceof SaidaIncluirDocumentoAPI) {
            $dlbIdDocumento = $documento->getIdDocumento();
        } else {
            $dlbIdDocumento = $documento->getDblIdDocumento();
        }
        $documentoDTO = $this->abrirDocumentoParaAssinatura($dlbIdDocumento);
        $objParticipanteDTO = $this->retornarParticipante($objUsuarioDTO, $objUnidadeDTO, $objProcedimentoDTO);

        $this->abrirAcessoParaAssinatura($orgaoDTO, $documentoDTO, $objParticipanteDTO);
        $objAssinaturaDTO = new AssinaturaDTO();
        $objAssinaturaDTO->setStrStaFormaAutenticacao(AssinaturaRN::$TA_SENHA);
        $objAssinaturaDTO->setNumIdUsuario(SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno());
        $objAssinaturaDTO->setStrSenhaUsuario($this->getSenha());
        $objAssinaturaDTO->setStrCargoFuncao("Usuário Externo - " . $cargoDTO->getStrExpressao());
        $objAssinaturaDTO->setArrObjDocumentoDTO(array($documentoDTO));

        $objMdPetDocumentoRN = new MdPetDocumentoRN();
        $objAssinaturaDTO = $objMdPetDocumentoRN->assinar($objAssinaturaDTO);
        $this->addDocumentos($documentoDTO);
        $this->fecharDocumentoParaAssinatura($documentoDTO);
        $this->fecharAcessoParaAssinatura();
        return $objAssinaturaDTO;
    }

    private function retornarDocumentosRecibo($objSaidaGerarProcedimentoAPI)
    {
        $arrObjReciboDocPet = array();
        $objRetornoDocs = $objSaidaGerarProcedimentoAPI->getRetornoInclusaoDocumentos();
        $arrRetornoTpFormato = MdPetIntercorrenteINT::retornaTipoFormatoDocumento($objRetornoDocs);

        $formato = null;
        foreach ($objRetornoDocs as $doc) {
            if (array_key_exists($doc->getIdDocumento(), $arrRetornoTpFormato)) {
                $formato = $arrRetornoTpFormato[$doc->getIdDocumento()];
            }
            $objReciboDocAnexPetDTO = MdPetIntercorrenteINT::retornaObjReciboDocPreenchido(array($doc->getIdDocumento(), $formato));
            array_push($arrObjReciboDocPet, $objReciboDocAnexPetDTO);
        }
        return $arrObjReciboDocPet;
    }

    /**
     * Gera o documento recibo que vai dentro do processo
     * @param $params
     * @return DocumentoDTO
     */
    private function montarReciboIntercorrente($params)
    {
        $arrParams = array(
            $params,
            $this->getUnidadeDTO(),
            $this->getProcedimentoDTO(),
            $this->getParticipanteDTO(),
            $this->getReciboDTO(),
            $this->getDocumentos()
        );

        $objMdPetReciboIntercorrenteRN = new MdPetReciboIntercorrenteRN();
        return $objMdPetReciboIntercorrenteRN->montarRecibo($arrParams);
    }

    /*
     * Gera registro em banco de dados de documentos anexos referente ao recibo (diferente do documento recibo que vai dentro do processo)
     *
     */
    protected function cadastrarReciboDocumentoAnexoConectado($params)
    {
        $objMdPetReciboDTO = $params[0];
        $arrObjReciboDocPet = $params[1];

        $objMdPetRelReciboDocumentoAnexoRN = new MdPetRelReciboDocumentoAnexoRN();

        $numIdReciboPeticionamento = $objMdPetReciboDTO->getNumIdReciboPeticionamento();

        //Gerar Recibo Docs
        foreach ($arrObjReciboDocPet as $objReciboDocPet) {
            //Remover Depois - Campo está como NOT NULL, deve ser NULL na adaptação
            $objReciboDocPet->setStrClassificacaoDocumento('A');
            $objReciboDocPet->setNumIdReciboPeticionamento($numIdReciboPeticionamento);
            $objMdPetRelReciboDocumentoAnexoRN->cadastrar($objReciboDocPet);
        }
    }

    /*
     * Responsável por enviar emails ao final do processo do peticionamento intercorrente
     * */
    protected function enviarEmailConectado($params)
    {
        $arrParams = array();
        $arrParams[0] = $params;
        $arrParams[1] = $this->getUnidadeDTO();
        $arrParams[2] = $this->getProcedimentoDTO();
        $arrParams[3] = array($this->getParticipanteDTO());
        $arrParams[4] = $this->getReciboDTO();
        $arrParams[5] = $this->getDocumentoRecibo();

        $emailMdPetEmailNotificacaoIntercorrenteRN = new MdPetEmailNotificacaoIntercorrenteRN();
        return $emailMdPetEmailNotificacaoIntercorrenteRN->notificaoPeticionamentoExterno($arrParams);

    }

    /*
     * Metodo principal responsavel por coordenar o processamento do
     * peticionamento intercorrente em etapas controladas menores.
     *
     * Etapas criticas (com rollback em caso de falha):
     *   1. prepararCadastroIntercorrente    - validacao, resolucao de processo, criterio, reabertura
     *   2. incluirDocumentosCadastroIntercorrente - inclusao dos documentos no processo
     *   3. finalizarCadastroIntercorrente   - recibo, andamentos, resposta/intercorrente
     *
     * Etapa pos-cadastro (nao critica, sem rollback):
     *   4. posCadastroIntercorrente         - envio de e-mails e limpeza de temporarios
     */
    public function cadastrar($params)
    {
        $strFluxo = self::nomearFluxo($params);
        $strProcesso = MdPetRastreioINT::descreverProcesso((int) ($params['id_procedimento'] ?? 0));
        MdPetRastreioINT::registrar($strFluxo . ', etapa 1 de 3 (conferência dos dados, critério e reabertura do processo): iniciada no ' . $strProcesso . '.');

        try {
            $dadosCadastro = $this->prepararCadastroIntercorrente($params);
        } catch (\Throwable $objExceptionPreparacao) {
            $objValidacao = MdPetExcecaoINT::converterFalhaTecnica($objExceptionPreparacao, isset($params['isRespostaIntimacao']) ? self::$MSG_RESPOSTA_NAO_CONCLUIDA : self::$MSG_INTERCORRENTE_NAO_CONCLUIDO);
            MdPetRastreioINT::registrarFalha($strFluxo . ', etapa 1 de 3 (conferência dos dados, critério e reabertura do processo): falhou no ' . $strProcesso . ', e o banco desfez tudo o que ela tinha gravado.', $objExceptionPreparacao, MdPetExcecaoINT::lerMensagem($objValidacao));
            throw $objValidacao;
        }

        // Número e id do processo que recebe os documentos: o indicado ou o relacionado que a etapa 1 criou
        $numIdProcessoIndicado = (int) ($params['id_procedimento'] ?? 0);
        $numIdProcesso = (int) ($dadosCadastro['params']['id_procedimento'] ?? $numIdProcessoIndicado);
        $strProcesso = MdPetRastreioINT::descreverProcesso($numIdProcesso, $this->getProcedimentoDTO()?->getStrProtocoloProcedimentoFormatado());
        $strConclusao = $numIdProcesso === $numIdProcessoIndicado
            ? 'concluída no ' . $strProcesso . '.'
            : 'concluída. Os documentos vão para o ' . $strProcesso . ', criado nesta etapa e relacionado ao processo ' . $numIdProcessoIndicado . '.';
        MdPetRastreioINT::registrar($strFluxo . ', etapa 1 de 3 (conferência dos dados, critério e reabertura do processo): ' . $strConclusao);

        try {

            MdPetRastreioINT::registrar($strFluxo . ', etapa 2 de 3 (inclusão dos documentos): iniciada no ' . $strProcesso . '.');
            $dadosCadastro = $this->incluirDocumentosCadastroIntercorrente($dadosCadastro);
            MdPetRastreioINT::registrar($strFluxo . ', etapa 2 de 3 (inclusão dos documentos): concluída no ' . $strProcesso . '.');
            MdPetRastreioINT::registrar($strFluxo . ', etapa 3 de 3 (recibo e andamentos): iniciada no ' . $strProcesso . '.');
            $resultado = $this->finalizarCadastroIntercorrente($dadosCadastro);
            MdPetRastreioINT::registrar($strFluxo . ', etapa 3 de 3 (recibo e andamentos): concluída no ' . $strProcesso . '.');

        } catch (\Throwable $e) {
            
            $this->tratarFalhaCadastroIntercorrente($dadosCadastro, $e);
            return false;

        }

        // Etapa pos-cadastro: notificacoes e limpeza de temporarios.
        // Erros aqui nao desfazem o cadastro já concluido com sucesso.
        $this->posCadastroIntercorrente($dadosCadastro);

        return $resultado;

    }

    /**
     * Etapa 1 do cadastro (ver cadastrar): valida, decide se o peticionamento entra no processo indicado ou num
     * processo relacionado novo, reabre o processo ou a unidade quando preciso, define a unidade que recebe os
     * documentos e copia os interessados do processo principal.
     *
     * @param array $params dados da conclusão do peticionamento
     * @return array dados para as etapas seguintes
     */
    protected function prepararCadastroIntercorrenteControlado($params)
    {
        // Bloco de validações
        $this->validarCadastro($params);
        $this->validarXssDocumentos($params['hdnTbDocumento']);

        $objAtividadeRN = new AtividadeRN();
        $objProcedimentoRN = new ProcedimentoRN();
        $objRelProtocoloProtocoloRN = new RelProtocoloProtocoloRN();
        $objMdPetCriterioRN = new MdPetCriterioRN();
        $objMdPetIntimacaoRN = new MdPetIntimacaoRN();
        $objUnidadeRN = new UnidadeRN();
        $objAcessoRN = new AcessoRN();
        $objMdPetIntercorrenteReaberturaRN = new MdPetIntercorrenteReaberturaRN();
        $objMdPetIntercorrenteAndamentoSigilosoRN = new MdPetIntercorrenteAndamentoSigilosoRN();
        $objParticipanteRN = new ParticipanteRN();
        $objMdPetParticipanteRN = new MdPetParticipanteRN();

        // setando atributos que serão utilizados em outros métodos
        $this->setCargoDTO($params['selCargo']);
//		$this->setSenha($params['pwdsenhaSEI']);
        $params['pwdsenhaSEI'] = '***********';
        $_POST['pwdsenhaSEI'] = '***********';
        $idProcedimentoProcesso = $params['id_procedimento'];

        // Processo anexado: o peticionamento vai para o processo anexador
        $objProcedimentoDTO = $this->consultarProcedimentoPeticionado($params['id_procedimento'], $objProcedimentoRN);
        if ($objProcedimentoDTO->getStrStaEstadoProtocolo() == ProtocoloRN::$TE_PROCEDIMENTO_ANEXADO) {
            $params['id_procedimento'] = $this->consultarProcessoAnexador($objProcedimentoDTO, $objRelProtocoloProtocoloRN)->getDblIdProtocolo1();
            $objProcedimentoDTO = $this->consultarProcedimentoPeticionado($params['id_procedimento'], $objProcedimentoRN);
        }

        $params['sta_estado_protocolo'] = $objProcedimentoDTO->getStrStaEstadoProtocolo();

        $objCriterioIntercorrenteDTO = $objMdPetCriterioRN->retornarCriterioPorTipoProcesso($params);
        $objCriterioIntercorrentePadraoDTO = $this->consultarCriterioPadrao($objMdPetCriterioRN);

        $arrDadosRecibo = [];
        $arrDadosRecibo['idProcedimento'] = $params['id_procedimento'];

        $params['diretoProcessoIndicado'] = false;

        $arrUnidadeProcesso = null;
        $qtdArrUnidadeProcesso = 0;
        $gerouNovoProcedimento = false;
        $arrIdUnidadesReabertas = [];

        if ($this->isNecessarioProcessoRelacionado($params, $objProcedimentoDTO, $objCriterioIntercorrenteDTO, $objCriterioIntercorrentePadraoDTO)) {
            $especificacao = 'Peticionamento Intercorrente relacionado ao Processo nº ' . $objProcedimentoDTO->getStrProtocoloProcedimentoFormatado();
            $objSaidaGerarProcedimentoAPI = $this->gerarProcedimentoApi([$objProcedimentoDTO, $objCriterioIntercorrenteDTO, $especificacao]);

            $arrDadosRecibo['idProcedimentoRel'] = $params['id_procedimento'];
            $arrDadosRecibo['idProcedimento'] = $objSaidaGerarProcedimentoAPI->getIdProcedimento();
            $params['id_procedimento'] = $objSaidaGerarProcedimentoAPI->getIdProcedimento();
            $this->setProcedimentoDTO($objSaidaGerarProcedimentoAPI->getIdProcedimento());
            $gerouNovoProcedimento = true;
        } elseif ($this->isPeticionamentoNoProcessoIndicado($params, $objCriterioIntercorrenteDTO)) {
            $arrIdUnidadesAbertasAntes = $this->listarIdUnidadesAbertas($params['id_procedimento']);

            if (isset($params['isRespostaIntimacao'])) {
                $arrUnidadeProcesso = $this->obterUnidadeAbertaDaIntimacao($params['id_intimacao'], $objProcedimentoDTO, $objMdPetIntimacaoRN, $objUnidadeRN);
                $qtdArrUnidadeProcesso = isset($arrUnidadeProcesso) ? count($arrUnidadeProcesso) : 0;
            } else {
                $this->reabrirProcessoSigilosoSemUnidadeAberta($objProcedimentoDTO, $objAtividadeRN, $objAcessoRN, $objProcedimentoRN);
                $this->reabrirProcessoSeNecessario($objProcedimentoDTO, $objMdPetIntercorrenteReaberturaRN);
            }

            $arrIdUnidadesReabertas = array_values(array_diff($this->listarIdUnidadesAbertas($params['id_procedimento']), $arrIdUnidadesAbertasAntes));
            $params['diretoProcessoIndicado'] = true;
            $this->setProcedimentoDTO($params['id_procedimento']);
            $this->guardarUltimaAtividadeAcessoExterno($params['id_procedimento'], $objAtividadeRN);
        }

        $arrUnidadeProcesso = $this->obterUnidadesAbertasDoPeticionamento($objProcedimentoDTO, $arrUnidadeProcesso, $qtdArrUnidadeProcesso, $objRelProtocoloProtocoloRN);
        [$idUnidadeProcesso, $idUsuarioAtribuicao] = $this->extrairUnidadeEUsuarioAtribuicao($arrUnidadeProcesso);

        if (!is_numeric($idUnidadeProcesso)) {
            $idUnidadeProcesso = $objMdPetIntercorrenteAndamentoSigilosoRN->retornaIdUnidadeAberturaProcesso($this->getProcedimentoDTO()->getDblIdProcedimento());
        }

        $arrobjParticipanteProcPrinc = $this->copiarInteressadosDoProcessoPrincipal($objProcedimentoDTO, $objParticipanteRN, $objMdPetParticipanteRN);

        $idUnidadeRespostaIntimacao = $params['isRespostaIntimacao'] ? $idUnidadeProcesso : null;

        return [
            'params' => $params,
            'arrDadosRecibo' => $arrDadosRecibo,
            'arrobjParticipanteProcPrinc' => $arrobjParticipanteProcPrinc,
            'idProcedimentoProcesso' => $idProcedimentoProcesso,
            'idUnidadeProcesso' => $idUnidadeProcesso,
            'idUsuarioAtribuicao' => $idUsuarioAtribuicao,
            'idUnidadeRespostaIntimacao' => $idUnidadeRespostaIntimacao,
            'gerouNovoProcedimento' => $gerouNovoProcedimento,
            'arrIdUnidadesReabertas' => $arrIdUnidadesReabertas,
            'arrObjReciboDocPet' => []
        ];
    }

    /**
     * @param int|string $idProcedimento
     * @param ProcedimentoRN $objProcedimentoRN
     * @return ProcedimentoDTO|null
     */
    private function consultarProcedimentoPeticionado($idProcedimento, ProcedimentoRN $objProcedimentoRN): ?ProcedimentoDTO
    {
        $objProcedimentoDTO = new ProcedimentoDTO();
        $objProcedimentoDTO->setDblIdProcedimento($idProcedimento);
        $objProcedimentoDTO->retTodos(true);

        return $objProcedimentoRN->consultarRN0201($objProcedimentoDTO);
    }

    /**
     * Associação do processo anexado com o processo anexador (protocolo 1).
     *
     * @param ProcedimentoDTO $objProcedimentoDTO processo anexado
     * @param RelProtocoloProtocoloRN $objRelProtocoloProtocoloRN
     * @return RelProtocoloProtocoloDTO|null
     */
    private function consultarProcessoAnexador(ProcedimentoDTO $objProcedimentoDTO, RelProtocoloProtocoloRN $objRelProtocoloProtocoloRN): ?RelProtocoloProtocoloDTO
    {
        $objRelProtocoloProtocoloDTO = new RelProtocoloProtocoloDTO();
        $objRelProtocoloProtocoloDTO->retDblIdProtocolo1();
        $objRelProtocoloProtocoloDTO->retStrProtocoloFormatadoProtocolo1();
        $objRelProtocoloProtocoloDTO->setDblIdProtocolo2($objProcedimentoDTO->getDblIdProcedimento());
        $objRelProtocoloProtocoloDTO->setStrStaAssociacao(RelProtocoloProtocoloRN::$TA_PROCEDIMENTO_ANEXADO);

        return $objRelProtocoloProtocoloRN->consultarRN0841($objRelProtocoloProtocoloDTO);
    }

    /**
     * @param MdPetCriterioRN $objMdPetCriterioRN
     * @return MdPetCriterioDTO|null critério para Intercorrente padrão
     */
    private function consultarCriterioPadrao(MdPetCriterioRN $objMdPetCriterioRN): ?MdPetCriterioDTO
    {
        $objMdPetCriterioDTO = new MdPetCriterioDTO();
        $objMdPetCriterioDTO->setStrSinCriterioPadrao('S');
        $objMdPetCriterioDTO->retTodos(true);

        return $objMdPetCriterioRN->consultar($objMdPetCriterioDTO);
    }

    /**
     * O peticionamento abre um processo relacionado ao indicado quando, fora da Resposta a Intimação, o critério do
     * Intercorrente está inativo, vale o critério padrão ou o processo está sobrestado, anexado ou bloqueado; e, em
     * qualquer caso, quando o processo é sigiloso e o critério padrão não aceita Intercorrente em processo sigiloso.
     *
     * @param array $params
     * @param ProcedimentoDTO $objProcedimentoDTO
     * @param MdPetCriterioDTO|null $objCriterioIntercorrenteDTO
     * @param MdPetCriterioDTO|null $objCriterioIntercorrentePadraoDTO
     * @return bool
     */
    private function isNecessarioProcessoRelacionado(
        array $params,
        ProcedimentoDTO $objProcedimentoDTO,
        ?MdPetCriterioDTO $objCriterioIntercorrenteDTO,
        ?MdPetCriterioDTO $objCriterioIntercorrentePadraoDTO
    ): bool {
        $estadosReabrirRelacionado = [ProtocoloRN::$TE_PROCEDIMENTO_SOBRESTADO, ProtocoloRN::$TE_PROCEDIMENTO_ANEXADO, ProtocoloRN::$TE_PROCEDIMENTO_BLOQUEADO];

        if (!$params['isRespostaIntimacao'] == true && (($params['isRespostaIntercorrente'] == true && $objCriterioIntercorrenteDTO->getStrSinAtivo() == "N") || $objCriterioIntercorrenteDTO->getStrSinCriterioPadrao() == 'S'
                || in_array($objProcedimentoDTO->getStrStaEstadoProtocolo(), $estadosReabrirRelacionado))) {
            return true;
        }

        return $objProcedimentoDTO->getStrStaNivelAcessoGlobalProtocolo() == 2 && $objCriterioIntercorrentePadraoDTO->getStrSinIntercorrenteSigiloso() == 'N';
    }

    /**
     * O peticionamento entra no próprio processo indicado na Resposta a Intimação e quando há critério do tipo de
     * processo que não é o padrão.
     *
     * @param array $params
     * @param MdPetCriterioDTO|null $objCriterioIntercorrenteDTO
     * @return bool
     */
    private function isPeticionamentoNoProcessoIndicado(array $params, ?MdPetCriterioDTO $objCriterioIntercorrenteDTO): bool
    {
        return $params['isRespostaIntimacao'] == true || ($objCriterioIntercorrenteDTO != null && $objCriterioIntercorrenteDTO->getStrSinCriterioPadrao() == 'N');
    }

    /**
     * Unidade da intimação aberta no processo. Unidade inativa ou que não recebe processo fica de fora; a unidade
     * fechada é reaberta antes de ser consultada de novo.
     *
     * @param int|string $idIntimacao
     * @param ProcedimentoDTO $objProcedimentoDTO
     * @param MdPetIntimacaoRN $objMdPetIntimacaoRN
     * @param UnidadeRN $objUnidadeRN
     * @return array|null unidades abertas devolvidas por MdPetIntimacaoRN::verificarUnidadeAberta, ou null sem unidade
     */
    private function obterUnidadeAbertaDaIntimacao(
        $idIntimacao,
        ProcedimentoDTO $objProcedimentoDTO,
        MdPetIntimacaoRN $objMdPetIntimacaoRN,
        UnidadeRN $objUnidadeRN
    ) {
        $objUnidadeDTO = $objMdPetIntimacaoRN->getUnidadeIntimacao([$idIntimacao]);

        if (empty($objUnidadeDTO)) {
            return null;
        }

        //unidade esta ativa
        $unidadeDTO = new UnidadeDTO();
        $unidadeDTO->retTodos();
        $unidadeDTO->setBolExclusaoLogica(false);
        $unidadeDTO->setNumIdUnidade($objUnidadeDTO->getNumIdUnidade());
        $objUnidadeDTO = $objUnidadeRN->consultarRN0125($unidadeDTO);

        if ($objUnidadeDTO->getStrSinAtivo() != 'S' || $objUnidadeDTO->getStrSinEnvioProcesso() != 'S') {
            return null;
        }

        $arrUnidadeProcesso = $objMdPetIntimacaoRN->verificarUnidadeAberta([$objProcedimentoDTO, $objUnidadeDTO->getNumIdUnidade()]);
        $qtdArrUnidadeProcesso = isset($arrUnidadeProcesso) ? count($arrUnidadeProcesso) : 0;

        if ($qtdArrUnidadeProcesso != 0) {
            return $arrUnidadeProcesso;
        }

        $idUnidadeAberta = $objMdPetIntimacaoRN->reabrirUnidade([$objProcedimentoDTO, $objUnidadeDTO->getNumIdUnidade()]);

        if (!is_numeric($idUnidadeAberta)) {
            return $arrUnidadeProcesso;
        }

        return $objMdPetIntimacaoRN->verificarUnidadeAberta([$objProcedimentoDTO, $idUnidadeAberta]);
    }

    /**
     * Tratamento reabertura de Processo Sigiloso por Intercorrente, adicionado em 02/12/2025 por Gabriel Glauber:
     * processo sigiloso sem unidade aberta é reaberto na primeira unidade ativa, pela conclusão mais recente, em que
     * houve tramitação ou credencial, por cada usuário com credencial nessa unidade.
     *
     * @param ProcedimentoDTO $objProcedimentoDTO
     * @param AtividadeRN $objAtividadeRN
     * @param AcessoRN $objAcessoRN
     * @param ProcedimentoRN $objProcedimentoRN
     * @return void
     */
    private function reabrirProcessoSigilosoSemUnidadeAberta(
        ProcedimentoDTO $objProcedimentoDTO,
        AtividadeRN $objAtividadeRN,
        AcessoRN $objAcessoRN,
        ProcedimentoRN $objProcedimentoRN
    ): void {
        $naGlobal           = $objProcedimentoDTO->getStrStaNivelAcessoGlobalProtocolo();
        $naLocal            = $objProcedimentoDTO->getStrStaNivelAcessoLocalProtocolo();
        $isSigiloso         = in_array(ProtocoloRN::$NA_SIGILOSO, [$naGlobal, $naLocal]);
        $idProcedimento     = $objProcedimentoDTO->getDblIdProcedimento();

        if (!$isSigiloso) {
            return;
        }

        // Retorna as unidades onde o processo esta aberto em ordem de abertura decrescente
        $arrUnidadesProcessoAberto = $this->retornaUnidadesProcessoAberto($idProcedimento);
        // $arrUnidadesProcessoAberto = (new MdPetIntercorrenteAndamentoSigilosoRN())->retornaUltimaUnidadeProcessoSigilosoAberto($idProcedimento);

        $idUnidadeProcesso = !empty($arrUnidadesProcessoAberto) ? current( $arrUnidadesProcessoAberto )->getNumIdUnidade() : null;

        // Havendo Unidade aberta, não reabre
        if (!is_null($idUnidadeProcesso)) {
            return;
        }

        $objAtividadeDTO = new MdPetAtividadeIntercorrenteDTO();
        $objAtividadeDTO->retDthConclusao();
        $objAtividadeDTO->retNumIdUnidade();
        $objAtividadeDTO->retNumIdUsuario();
        $objAtividadeDTO->setDistinct(true);
        $objAtividadeDTO->setStrSinAtivoUnidade('S');
        $objAtividadeDTO->setDblIdProcedimentoProtocolo($idProcedimento);
        $objAtividadeDTO->setOrdDthConclusao(InfraDTO::$TIPO_ORDENACAO_DESC);
        $objAtividadeDTO->setNumIdTarefa(array_merge(
            TarefaRN::getArrTarefasTramitacao(),
            TarefaRN::getArrTarefasConcessaoCredencial(false),
            TarefaRN::getArrTarefasRenunciaCredencial(),
            TarefaRN::getArrTarefasCassacaoCredencial(false),
            TarefaRN::getArrTarefasAnulacaoCredencial(false)
        ), InfraDTO::$OPER_IN);
        // $objAtividadeDTO->setNumIdTarefa([TarefaRN::$TI_CONCLUSAO_PROCESSO_UNIDADE, TarefaRN::$TI_CONCLUSAO_AUTOMATICA_UNIDADE, TarefaRN::$TI_CONCLUSAO_PROCESSO_USUARIO], InfraDTO::$OPER_IN);
        $arrObjAtividadeDTO = $objAtividadeRN->listarRN0036($objAtividadeDTO);

        $arrIdUnidadesParaReaberturaProcesso = array_unique(InfraArray::converterArrInfraDTO($arrObjAtividadeDTO, 'IdUnidade'));

        foreach ($arrIdUnidadesParaReaberturaProcesso as $idUnidadeReabertura) {

            $objAcessoDTO = new AcessoDTO();
            $objAcessoDTO->retNumIdUsuario();
            $objAcessoDTO->setDblIdProtocolo($idProcedimento);
            $objAcessoDTO->setNumIdUnidade($idUnidadeReabertura);
            $objAcessoDTO->setStrStaTipo(AcessoRN::$TA_CREDENCIAL_PROCESSO);
            $objAcessoDTO->setOrdNumIdAcesso(InfraDTO::$TIPO_ORDENACAO_DESC);
            $arrObjAcessoDTO = $objAcessoRN->listar($objAcessoDTO);

            $arrIdUsuarios = InfraArray::converterArrInfraDTO($arrObjAcessoDTO, 'IdUsuario');

            if (empty($arrIdUsuarios)) {
                continue;
            }

            foreach ($arrIdUsuarios as $idUsuario) {

                $this->simularLogin($idUnidadeReabertura, $idUsuario);

                $objReabrirProcessoDTO = new ReabrirProcessoDTO();
                $objReabrirProcessoDTO->setDblIdProcedimento($idProcedimento);
                $objReabrirProcessoDTO->setNumIdUnidade($idUnidadeReabertura);
                $objReabrirProcessoDTO->setNumIdUsuario($idUsuario);
                $objProcedimentoRN->reabrirRN0966($objReabrirProcessoDTO);

            }

            // Reabre só na primeira unidade com credencial
            return;
        }
    }

    /**
     * Fluxo antigo mantido: reabre pela API do SEI o processo indicado que não tem unidade aberta.
     *
     * @param ProcedimentoDTO $objProcedimentoDTO
     * @param MdPetIntercorrenteReaberturaRN $objMdPetIntercorrenteReaberturaRN
     * @return void
     */
    private function reabrirProcessoSeNecessario(ProcedimentoDTO $objProcedimentoDTO, MdPetIntercorrenteReaberturaRN $objMdPetIntercorrenteReaberturaRN): void
    {
        if (!$objMdPetIntercorrenteReaberturaRN->isNecessarioReabrirProcedimento($objProcedimentoDTO)) {
            return;
        }

        $objMdPetIntercorrenteReaberturaRN->reabrirProcessoApi($objProcedimentoDTO);
        //$idUnidadeReabrirProcesso = $reaberturaRN->reabrirProcessoApi($objProcedimentoDTO);

        //if (!$idUnidadeReabrirProcesso) {
        //	$objInfraException = new InfraException();
        //	$objInfraException->lancarValidacao('O processo indicado não aceita peticionamento intercorrente. Utilize o Peticionamento de Processo Novo para protocolizar sua demanda.');
        //}
    }

    /**
     * Guarda o id da maior atividade de liberação de acesso externo, para comparar na hora de limpar o histórico.
     *
     * @param int|string $idProcedimento
     * @param AtividadeRN $objAtividadeRN
     * @return void
     */
    private function guardarUltimaAtividadeAcessoExterno($idProcedimento, AtividadeRN $objAtividadeRN): void
    {
        $atividadeConsultaDTO = new AtividadeDTO();
        $atividadeConsultaDTO->retNumIdAtividade();
        $atividadeConsultaDTO->setDblIdProtocolo($idProcedimento);
        $atividadeConsultaDTO->setNumIdTarefa(TarefaRN::$TI_ACESSO_EXTERNO_SISTEMA);
        $atividadeConsultaDTO->setOrd('IdAtividade', InfraDTO::$TIPO_ORDENACAO_DESC);
        $arrDTOAtividadesConsulta = $objAtividadeRN->listarRN0036($atividadeConsultaDTO);

        if ($arrDTOAtividadesConsulta == null || !is_array($arrDTOAtividadesConsulta) || count($arrDTOAtividadesConsulta) == 0) {
            return;
        }

        $this->maxIdAtividade = $arrDTOAtividadesConsulta[0]->getNumIdAtividade();
    }

    /**
     * Unidades abertas que recebem o peticionamento: no processo anexado, a última aberta do anexador; sem unidade
     * definida até aqui, a última aberta do processo que recebe os documentos.
     *
     * @param ProcedimentoDTO $objProcedimentoDTO
     * @param array|null $arrUnidadeProcesso unidades já definidas na Resposta a Intimação
     * @param int $qtdArrUnidadeProcesso
     * @param RelProtocoloProtocoloRN $objRelProtocoloProtocoloRN
     * @return array|null
     */
    private function obterUnidadesAbertasDoPeticionamento(
        ProcedimentoDTO $objProcedimentoDTO,
        $arrUnidadeProcesso,
        int $qtdArrUnidadeProcesso,
        RelProtocoloProtocoloRN $objRelProtocoloProtocoloRN
    ) {
        // 1) ANEXADO, vai pegar do ANEXADOR/PRINCIPAL
        if ($objProcedimentoDTO->getStrStaEstadoProtocolo() == ProtocoloRN::$TE_PROCEDIMENTO_ANEXADO) {
            $objRelProtocoloProtocoloDTO = $this->consultarProcessoAnexador($objProcedimentoDTO, $objRelProtocoloProtocoloRN);

            if ($objRelProtocoloProtocoloDTO == null) {
                return $arrUnidadeProcesso;
            }

            return $this->retornaUltimaUnidadeProcessoAberto([$objRelProtocoloProtocoloDTO->getDblIdProtocolo1()]);
        }

        if ($qtdArrUnidadeProcesso != 0) {
            return $arrUnidadeProcesso;
        }

        // 2) Última aberta
        return $this->retornaUltimaUnidadeProcessoAberto([$this->getProcedimentoDTO()->getDblIdProcedimento()]);
    }

    /**
     * Unidade e usuário de atribuição da primeira unidade aberta, que chega como par numérico ou como AtividadeDTO.
     *
     * @param array|null $arrUnidadeProcesso
     * @return array{0: mixed, 1: mixed} id da unidade e id do usuário de atribuição, ou null
     */
    private function extrairUnidadeEUsuarioAtribuicao($arrUnidadeProcesso): array
    {
        $qtdArrUnidadeProcesso = isset($arrUnidadeProcesso) ? count($arrUnidadeProcesso) : 0;

        if ($qtdArrUnidadeProcesso == 0) {
            return [null, null];
        }

        if (is_numeric($arrUnidadeProcesso[0])) {
            return [$arrUnidadeProcesso[0], is_numeric($arrUnidadeProcesso[1]) ? $arrUnidadeProcesso[1] : null];
        }

        $idUnidadeProcesso = $arrUnidadeProcesso[0]->getNumIdUnidade();

        return [$idUnidadeProcesso, $arrUnidadeProcesso[0]->isSetNumIdUsuarioAtribuicao() ? $arrUnidadeProcesso[0]->getNumIdUsuarioAtribuicao() : null];
    }

    /**
     * Copia os interessados do processo principal para o processo que recebe os documentos.
     *
     * @param ProcedimentoDTO $objProcedimentoDTO processo principal
     * @param ParticipanteRN $objParticipanteRN
     * @param MdPetParticipanteRN $objMdPetParticipanteRN
     * @return ParticipanteDTO[] interessados do processo principal
     */
    private function copiarInteressadosDoProcessoPrincipal(
        ProcedimentoDTO $objProcedimentoDTO,
        ParticipanteRN $objParticipanteRN,
        MdPetParticipanteRN $objMdPetParticipanteRN
    ) {
        $objParticipanteProcPrincDTO = new ParticipanteDTO();
        $objParticipanteProcPrincDTO->retNumIdParticipante();
        $objParticipanteProcPrincDTO->retNumIdContato();
        $objParticipanteProcPrincDTO->retNumIdUnidade();
        $objParticipanteProcPrincDTO->retStrStaParticipacao();
        $objParticipanteProcPrincDTO->retStrNomeContato();
        $objParticipanteProcPrincDTO->retNumSequencia();
        $objParticipanteProcPrincDTO->setDblIdProtocolo($objProcedimentoDTO->getDblIdProcedimento());
        $objParticipanteProcPrincDTO->setStrStaParticipacao(ParticipanteRN::$TP_INTERESSADO);
        $arrobjParticipanteProcPrinc = $objParticipanteRN->listarRN0189($objParticipanteProcPrincDTO);

        $idsParticipantes = [];

        $i = 0;
        foreach ($arrobjParticipanteProcPrinc as $objParticipanteProcPrinc) {

            $objParticipante = new ParticipanteDTO();
            $objParticipante->setDblIdProtocolo($this->getProcedimentoDTO()->getDblIdProcedimento());
            $objParticipante->setNumIdContato($objParticipanteProcPrinc->getNumIdContato());
            $objParticipante->setNumIdUnidade($objParticipanteProcPrinc->getNumIdUnidade());
            $objParticipante->setStrStaParticipacao(ParticipanteRN::$TP_INTERESSADO);
            $objParticipante->setNumSequencia($i);
            $idsParticipantes[] = $objParticipante;
            $i++;
        }

        $objMdPetParticipanteRN->setInteressadosRemetentesProcedimentoDocumento([$this->getProcedimentoDTO()->getDblIdProcedimento(), $idsParticipantes]);

        return $arrobjParticipanteProcPrinc;
    }

    protected function incluirDocumentosCadastroIntercorrenteControlado($dadosCadastro)
    {
        $params = $dadosCadastro['params'];
        $idUnidadeRespostaIntimacao = $dadosCadastro['idUnidadeRespostaIntimacao'];

        // Forca o Nivel de Acesso parametrizado na Administracao para Intercorrente e Resposta da Intimacao
        $versaoPeticionamento = intval(preg_replace("/\D/", "", (new InfraParametro(BancoSEI::getInstance()))->getValor('VERSAO_MODULO_PETICIONAMENTO', false)));
        if($versaoPeticionamento >= 410){
            $nivelAcessoDoc = MdPetForcarNivelAcessoDocINT::getDadosForcarNivelAcessoDoc('I');
            if(!empty($nivelAcessoDoc) && is_array($nivelAcessoDoc['documentos']) && count($nivelAcessoDoc['documentos']) > 0){

                $rows = explode("¥", $params['hdnTbDocumento']);

                $matrix = array_map(function ($row) use ($nivelAcessoDoc) {
                    $values = explode("±", $row);
                    if (in_array($values[1], $nivelAcessoDoc['documentos'])) {
                        $values[3] = $nivelAcessoDoc['nivel'];
                        $values[4] = $nivelAcessoDoc['hipotese'];
                    }
                    return $values;
                }, $rows);

                $params['hdnTbDocumento'] = implode("¥", array_map(function ($row) {
                    return implode("±", $row);
                }, $matrix));
            }
        }

        $arrDocApi = MdPetIntercorrenteINT::montarArrDocumentoAPI($params['id_procedimento'], $params['hdnTbDocumento']);

        $arrObjReciboDocPet = $this->incluirDocumentosApi($this->getProcedimentoDTO(), $arrDocApi, $idUnidadeRespostaIntimacao);

        $dadosCadastro['params'] = $params;
        $dadosCadastro['arrObjReciboDocPet'] = $arrObjReciboDocPet;

        return $dadosCadastro;
    }

    protected function finalizarCadastroIntercorrenteControlado($dadosCadastro)
    {
        $params = $dadosCadastro['params'];
        $arrDadosRecibo = $dadosCadastro['arrDadosRecibo'];
        $arrObjReciboDocPet = $dadosCadastro['arrObjReciboDocPet'];
        $idProcedimentoProcesso = $dadosCadastro['idProcedimentoProcesso'];
        $idUnidadeProcesso = $dadosCadastro['idUnidadeProcesso'];
        $idUsuarioAtribuicao = $dadosCadastro['idUsuarioAtribuicao'];
        $arrobjParticipanteProcPrinc = $dadosCadastro['arrobjParticipanteProcPrinc'];
        $atividadeRN = new AtividadeRN();

        if (isset($params['isRespostaIntimacao'])) {
            $arrDadosRecibo['isRespostaIntimacao'] = true;
        }

        $objUsuarioDTO = new UsuarioDTO();
        $objUsuarioDTO->retNumIdContato();
        $objUsuarioDTO->retStrNome();
        $objUsuarioDTO->setNumIdUsuario(SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno());
        $objUsuarioRN = new UsuarioRN();
        $objUsuarioDTO = $objUsuarioRN->consultarRN0489($objUsuarioDTO);

        $objMdPetIntRelDestinatarioDTO = new MdPetIntRelDestinatarioDTO();
        $objMdPetIntRelDestinatarioDTO->setNumIdContatoParticipante($objUsuarioDTO->getNumIdContato());
        $objMdPetIntRelDestinatarioDTO->setNumIdMdPetIntimacao($params['id_intimacao']);
        $objMdPetIntRelDestinatarioDTO->retNumIdMdPetIntRelDestinatario();
        $objMdPetIntRelDestinatarioDTO->setNumIdMdPetIntRelDestinatario($params['id_int_rel_dest']);
        $objMdPetIntRelDestinatarioDTO->retStrSinPessoaJuridica();
        $objMdPetIntRelDestinatarioDTO->retNumIdContato();
        $objMdPetIntRelDestinatarioDTO->retStrNomeContato();
        $objMdPetIntRelDestinatarioDTO->retStrCnpjContato();
        $objMdPetIntRelDestinatarioDTO->retDblCpfContato();
        $objMdPetIntRelDestinatarioRN = new MdPetIntRelDestinatarioRN();
        $objMdPetIntRelDestinatarioDTO = $objMdPetIntRelDestinatarioRN->consultar($objMdPetIntRelDestinatarioDTO);

        $objMdPetReciboRN = new MdPetReciboRN();

        $arrDadosRecibo['id_intimacao'] = $params['id_intimacao'];
        $arrDadosRecibo['id_aceite'] = $params['id_aceite'];
        $arrDadosRecibo['id_tipo_resposta'] = $params['id_tipo_resposta'];
        $arrDadosRecibo['idProcedimentoProcesso'] = $idProcedimentoProcesso;

        if ($objMdPetIntRelDestinatarioDTO) {
            if ($objMdPetIntRelDestinatarioDTO->getStrSinPessoaJuridica() == MdPetIntRelDestinatarioRN::$PESSOA_JURIDICA) {
                $params['id_contato'] = $objMdPetIntRelDestinatarioDTO->getNumIdContato();
                $params['sin_pessoa_juridica'] = $objMdPetIntRelDestinatarioDTO->getStrSinPessoaJuridica();
                $params['nome_contato'] = $objMdPetIntRelDestinatarioDTO->getStrNomeContato();
                $params['doc_contato'] = $objMdPetIntRelDestinatarioDTO->getStrSinPessoaJuridica() == 'S' ? InfraUtil::formatarCnpj($objMdPetIntRelDestinatarioDTO->getStrCnpjContato()) : InfraUtil::formatarCpf($objMdPetIntRelDestinatarioDTO->getDblCpfContato());
                $params['nome_usuario'] = $objUsuarioDTO->getStrNome();
            }
        }

        //realizar classificacao da metas ODS - IA
        if( PeticionamentoIntegracao::verificaSeModIAVersaoMinima() && PeticionamentoIntegracao::permitirClassificacaoODSUsuarioExterno()){

            // Inclui as informações da classificação no recibo antes da efetiva classificação por conta da sessão
            $params['metas_ods_onu'] = SessaoSEIExterna::getInstance()->getAtributo('METAS_SELECIONADAS');

        }

        $objReciboDTO = $objMdPetReciboRN->gerarReciboSimplificadoIntercorrente($arrDadosRecibo);

        if (!is_null($objReciboDTO)) {

            $this->setReciboDTO($objReciboDTO);
            $this->cadastrarReciboDocumentoAnexo(array($objReciboDTO, $arrObjReciboDocPet));

            $documentoReciboDTO = $this->montarReciboIntercorrente($params);

            //Remetentes
            $idsParticipantes = array();
            $objParticipante = new ParticipanteDTO();
            $objParticipante->setDblIdProtocolo($documentoReciboDTO->getDblIdDocumento());
            $objParticipante->setNumIdContato($this->getContatoDTOUsuarioLogado()->getNumIdContato());
            $objParticipante->setNumIdUnidade($idUnidadeProcesso);
            $objParticipante->setStrStaParticipacao(ParticipanteRN::$TP_REMETENTE);
            $objParticipante->setNumSequencia(0);
            if (!key_exists($this->getContatoDTOUsuarioLogado()->getNumIdContato(), $idsParticipantes)) {
                $idsParticipantes[$this->getContatoDTOUsuarioLogado()->getNumIdContato()] = $objParticipante;
            }

            $idsParticipantes = array();
            $objParticipante = new ParticipanteDTO();
            $objParticipante->setDblIdProtocolo($documentoReciboDTO->getDblIdDocumento());
            $objParticipante->setNumIdContato($this->getContatoDTOUsuarioLogado()->getNumIdContato());
            $objParticipante->setNumIdUnidade($idUnidadeProcesso);
            $objParticipante->setStrStaParticipacao(ParticipanteRN::$TP_INTERESSADO);
            $objParticipante->setNumSequencia(0);
            if (!key_exists($this->getContatoDTOUsuarioLogado()->getNumIdContato(), $idsParticipantes)) {
                $idsParticipantes[$this->getContatoDTOUsuarioLogado()->getNumIdContato()] = $objParticipante;
            }


            // Recibo - Interessados
            $i = 0;
            foreach ($arrobjParticipanteProcPrinc as $objParticipanteProcPrinc) {
                $objParticipante = new ParticipanteDTO();
                $objParticipante->setDblIdProtocolo($documentoReciboDTO->getDblIdDocumento());
                $objParticipante->setNumIdContato($objParticipanteProcPrinc->getNumIdContato());
                $objParticipante->setNumIdUnidade($objParticipanteProcPrinc->getNumIdUnidade());
                $objParticipante->setStrStaParticipacao(ParticipanteRN::$TP_INTERESSADO);
                $objParticipante->setNumSequencia($i);
                if (!key_exists($objParticipanteProcPrinc->getNumIdContato(), $idsParticipantes)) {
                    $idsParticipantes[$objParticipanteProcPrinc->getNumIdContato()] = $objParticipante;
                }
                $i++;
            }

            $objMdPetParticipanteRN = new MdPetParticipanteRN();
            $arrInteressado = array();
            $arrInteressado[0] = $documentoReciboDTO->getDblIdDocumento();
            $arrInteressado[1] = $idsParticipantes;

            $objMdPetParticipanteRN->setInteressadosRemetentesProcedimentoDocumento($arrInteressado);
            // Recibo - Interessados - FIM

            $this->setDocumentoRecibo($documentoReciboDTO);

            //apagando andamentos do tipo "Disponibilizado acesso externo para @INTERESSADO@"
            $objAtividadeDTOLiberacao = new AtividadeDTO();
            $objAtividadeDTOLiberacao->retTodos();
            $objAtividadeDTOLiberacao->setDblIdProtocolo($this->getProcedimentoDTO()->getDblIdProcedimento());
            $objAtividadeDTOLiberacao->setNumIdAtividade($this->maxIdAtividade, InfraDTO::$OPER_MAIOR);
            $objAtividadeDTOLiberacao->setNumIdTarefa(TarefaRN::$TI_ACESSO_EXTERNO_SISTEMA);

            $arrDTOAtividades = $atividadeRN->listarRN0036($objAtividadeDTOLiberacao);
            $atividadeRN->excluirRN0034($arrDTOAtividades);

            // Andamento - Processo remetido pela unidade
            $unidadeDTO = new UnidadeDTO();
            $unidadeDTO->retTodos();
            $unidadeDTO->setBolExclusaoLogica(false);
            $unidadeDTO->setNumIdUnidade($idUnidadeProcesso);
            $unidadeRN = new UnidadeRN();
            $unidadeDTO = $unidadeRN->consultarRN0125($unidadeDTO);

            if (isset($params['isRespostaIntimacao'])) {
                $this->salvarResposta($params, $arrObjReciboDocPet, $unidadeDTO, $documentoReciboDTO);
            } else if (isset($params['isRespostaIntercorrente'])) {
                $this->_controlarAcessoExterno($arrDadosRecibo, $arrObjReciboDocPet, $objReciboDTO);
                $this->lancarAndamentoIntercorrenteControlado($documentoReciboDTO);
            }

            $arrObjAtributoAndamentoDTO = array();
            $objAtributoAndamentoDTO = new AtributoAndamentoDTO();
            $objAtributoAndamentoDTO->setStrNome('UNIDADE');
            $objAtributoAndamentoDTO->setStrValor($unidadeDTO->getStrSigla() . ' ¥ ' . $unidadeDTO->getStrDescricao());
            $objAtributoAndamentoDTO->setStrIdOrigem($unidadeDTO->getNumIdUnidade());
            $arrObjAtributoAndamentoDTO[] = $objAtributoAndamentoDTO;


            $objAtividadeDTO = new AtividadeDTO();
            $objAtividadeDTO->setDblIdProtocolo($this->getProcedimentoDTO()->getDblIdProcedimento());
            $objAtividadeDTO->setNumIdUnidade($unidadeDTO->getNumIdUnidade());
            $objAtividadeDTO->setNumIdUnidadeOrigem($unidadeDTO->getNumIdUnidade());
            if (!empty($idUsuarioAtribuicao)) {
                $objAtividadeDTO->setNumIdUsuarioAtribuicao($idUsuarioAtribuicao);
            }
            $objAtividadeDTO->setArrObjAtributoAndamentoDTO($arrObjAtributoAndamentoDTO);
            $objAtividadeDTO->setNumIdTarefa(TarefaRN::$TI_PROCESSO_REMETIDO_UNIDADE);

            $objAtividadeRN = new AtividadeRN();
            $objAtividadeRN->gerarInternaRN0727($objAtividadeDTO);

            // obtendo a ultima atividade informada para o processo, para marcar
            // como nao visualizada, deixando assim o processo marcado como "vermelho"
            // (status de Nao Visualizado) na listagem da tela "Controle de processos"
            //trecho comentado para preservar apresentacao do "icone amarelo" na tela de Controle de Processos
            $atividadeRN = new AtividadeRN();
            $atividadeBD = new AtividadeBD($this->getObjInfraIBanco());
            $atividadeDTO = new AtividadeDTO();
            $atividadeDTO->retTodos();
            $atividadeDTO->setDblIdProtocolo($this->getProcedimentoDTO()->getDblIdProcedimento());
            $atividadeDTO->setOrd("IdAtividade", InfraDTO::$TIPO_ORDENACAO_DESC);
            $ultimaAtividadeDTO = $atividadeRN->listarRN0036($atividadeDTO);

            //alterar a ultima atividade criada para nao visualizado
            if ($ultimaAtividadeDTO != null && count($ultimaAtividadeDTO) > 0) {
                $ultimaAtividadeDTO[0]->setNumTipoVisualizacao(AtividadeRN::$TV_NAO_VISUALIZADO);
                $atividadeBD->alterar($ultimaAtividadeDTO[0]);
            }

            return array(
                'recibo'    => $objReciboDTO,
                'documento' => $documentoReciboDTO,
                'params'    => $params
            );
            //Gerar Recibo e executar javascript para fechar janela filha e redirecionar janela pai para a tela de detalhes do recibo que foi gerado]

        }

        return false;
    }

    /**
     * Nome do fluxo para o rastro e o aviso do rollback.
     *
     * @param array<string, mixed> $params parâmetros do peticionamento
     * @return string
     */
    private static function nomearFluxo(array $params): string
    {
        return isset($params['isRespostaIntimacao']) ? 'Resposta a Intimação' : 'Peticionamento Intercorrente';
    }

    private function tratarFalhaCadastroIntercorrente($dadosCadastro, \Throwable $e)
    {
        $bolResposta = isset($dadosCadastro['params']['isRespostaIntimacao']);
        $strFluxo = self::nomearFluxo($dadosCadastro['params'] ?? []);
        $strProcessoFormatado = (string) $this->getProcedimentoDTO()?->getStrProtocoloProcedimentoFormatado();
        $strProcesso = MdPetRastreioINT::descreverProcesso((int) ($dadosCadastro['params']['id_procedimento'] ?? 0), $strProcessoFormatado);
        $objValidacao = MdPetExcecaoINT::converterFalhaTecnica($e, $bolResposta ? self::$MSG_RESPOSTA_NAO_CONCLUIDA : self::$MSG_INTERCORRENTE_NAO_CONCLUIDO);
        $strMotivo = MdPetExcecaoINT::lerMensagem($objValidacao);
        MdPetRastreioINT::registrarFalha($strFluxo . ': a etapa em andamento falhou no ' . $strProcesso . '.', $e, $strMotivo);
        MdPetRastreioINT::registrarInicioDoRollback($strFluxo . ': rollback iniciado para apagar o que as etapas gravaram no ' . $strProcesso . '.');

        try {
            $this->realizarRollbackCadastroIntercorrente($dadosCadastro);
        } catch (\Throwable $eRollback) {
            MdPetRastreioINT::registrarFalha($strFluxo . ': o rollback falhou no ' . $strProcesso . ', e o banco manteve tudo como estava antes dele. O processo precisa de correção manual.', $eRollback);
            LogSEI::getInstance()->gravar(
                'Falha ao tentar reverter cadastro de peticionamento intercorrente após erro original ['
                . $e->getMessage() . ']: ' . $eRollback->getMessage() . ' Rastreio: pet=' . MdPetRastreioINT::obterUuid(),
                InfraLog::$ERRO
            );
            (new MdPetAvisoRollbackRN())->avisarFalha($strFluxo, 'processo ' . $strProcessoFormatado, $e, $eRollback, $strMotivo);
            (new InfraException())->lancarValidacao(sprintf($bolResposta ? self::$MSG_RESPOSTA_NAO_DESFEITA : self::$MSG_INTERCORRENTE_NAO_DESFEITO, $strProcessoFormatado), null, $eRollback);
        }

        MdPetRastreioINT::registrar($strFluxo . ': rollback concluído com sucesso. Tudo o que a tentativa gravou no ' . $strProcesso . ' foi apagado.');
        $this->concluirProcessoNasUnidadesReabertas($dadosCadastro['arrIdUnidadesReabertas'] ?? [], $strFluxo);
        (new MdPetAvisoRollbackRN())->avisarAcionamento($strFluxo, 'processo ' . $strProcessoFormatado, $e, $strMotivo);

        throw $objValidacao;
    }

    /*
     * Etapa pos-cadastro: responsabilidades nao-criticas que nao devem
     * interromper o fluxo nem acionar rollback em caso de falha.
     * - Envio de e-mails de notificacao (informativo, destinatario pode nao ter email valido)
     * - Limpeza de arquivos temporarios de upload
     */
    protected function posCadastroIntercorrenteControlado($dadosCadastro)
    {
        $params = $dadosCadastro['params'];

        // Envio de e-mail: informativo, nao deve interromper o fluxo principal
        try {
            $this->enviarEmail($params);
        } catch (\Throwable $e) {
            LogSEI::getInstance()->gravar(
                'Falha no envio de e-mail de notificação do peticionamento intercorrente'
                . ' (processo ' . (isset($params['id_procedimento']) ? $params['id_procedimento'] : 'indefinido') . ')'
                . ': ' . $e->getMessage(),
                InfraLog::$INFORMACAO
            );
        }

        // Limpeza de arquivos temporarios de upload
        try {
            $arquivos_enviados = PaginaSEIExterna::getInstance()->getArrItensTabelaDinamica($params['hdnTbDocumento']);
            foreach ($arquivos_enviados as $arquivo_enviado) {
                @unlink(DIR_SEI_TEMP . '/' . $arquivo_enviado[7]);
            }
        } catch (\Throwable $e) {
            // Falha na limpeza nao impede o retorno do cadastro
        }
    }

    /**
     * Desfaz as etapas já gravadas em uma só transação: se um passo falhar, nada é desfeito e o processo fica como estava,
     * sem ficar pela metade.
     *
     * @param array<string, mixed> $dadosCadastro dados devolvidos pela primeira etapa
     * @return void
     */
    protected function realizarRollbackCadastroIntercorrenteControlado($dadosCadastro)
    {
        $strFluxo = self::nomearFluxo($dadosCadastro['params'] ?? []);
        $objReciboDTO = $this->getReciboDTO();
        if ($objReciboDTO != null && is_numeric($objReciboDTO->getNumIdReciboPeticionamento()) && $this->reciboFoiGravado($objReciboDTO, $strFluxo)) {
            $objRelReciboDocDTO = new MdPetRelReciboDocumentoAnexoDTO();
            $objRelReciboDocDTO->retNumIdReciboDocumentoAnexoPeticionamento();
            $objRelReciboDocDTO->setNumIdReciboPeticionamento($objReciboDTO->getNumIdReciboPeticionamento());
            $objRelReciboDocRN = new MdPetRelReciboDocumentoAnexoRN();
            foreach ($objRelReciboDocRN->listar($objRelReciboDocDTO) as $objRelReciboDocItemDTO) {
                $objRelReciboDocRN->excluir($objRelReciboDocItemDTO);
            }
            (new MdPetReciboRN())->excluir($objReciboDTO);
            MdPetRastreioINT::registrarPassoDoRollback($strFluxo, 'recibo ' . $objReciboDTO->getNumIdReciboPeticionamento() . ' excluído.');
        }

        $arrDocumentos = $this->getDocumentos();
        $objDocumentoReciboDTO = $this->getDocumentoRecibo();
        if ($objDocumentoReciboDTO != null) {
            $arrDocumentos[] = $objDocumentoReciboDTO;
        }

        $arrCredencial = null;

        foreach (array_reverse($arrDocumentos) as $documentoDTO) {

            $idDocumento = $documentoDTO->getDblIdDocumento();

            if ($documentoDTO instanceof SaidaIncluirDocumentoAPI) {
                $idDocumento = $documentoDTO->getIdDocumento();
            }
            
            if (!is_numeric($idDocumento)) {
                continue;
            }

            $objDocumentoDTO = new DocumentoDTO();
            $objDocumentoDTO->retDblIdDocumento();
            $objDocumentoDTO->retStrStaDocumento();
            $objDocumentoDTO->retStrSinBloqueado();
            $objDocumentoDTO->setDblIdDocumento($idDocumento);
            $objDocumentoDTO = (new DocumentoRN())->consultarRN0005($objDocumentoDTO);

            if ($objDocumentoDTO == null) {
                MdPetRastreioINT::registrarPassoDoRollback($strFluxo, 'documento ' . $idDocumento . ' já tinha sido cancelado pelo banco junto com a etapa que falhou.');
                continue;
            }

            $arrCredencial ??= $this->concederCredencialParaDesfazer($dadosCadastro['idUnidadeProcesso'] ?? null);
            $objDocumentoDTO->setStrStaDocumento(DocumentoRN::$TD_EDITOR_INTERNO);
            $objDocumentoDTO->setStrSinBloqueado('N');
            (new DocumentoBD($this->getObjInfraIBanco()))->alterar($objDocumentoDTO);
            (new DocumentoRN())->excluirRN0006($objDocumentoDTO);
            MdPetRastreioINT::registrarPassoDoRollback($strFluxo, 'documento ' . $idDocumento . ' excluído.');
            
        }

        $this->cassarCredencialDoDesfazimento($arrCredencial);

        if (!empty($dadosCadastro['gerouNovoProcedimento'])) {
            (new ProcedimentoRN())->excluirRN0280($this->getProcedimentoDTO());
            MdPetRastreioINT::registrarPassoDoRollback($strFluxo, 'processo criado pela tentativa excluído.');
        }
    }

    /**
     * No processo sigiloso, a credencial provisória da inclusão dos documentos já foi cassada, e o core só exclui o
     * documento com andamento aberto para o usuário: o desfazimento precisa da mesma credencial.
     *
     * @param int|null $idUnidadeProcesso unidade que recebeu os documentos
     * @return array<int, mixed>|null dados da credencial, ou null no processo que não é sigiloso
     */
    private function concederCredencialParaDesfazer($idUnidadeProcesso): ?array
    {
        $objProcedimentoDTO = $this->getProcedimentoDTO();

        if ($objProcedimentoDTO === null || !in_array(ProtocoloRN::$NA_SIGILOSO, [$objProcedimentoDTO->getStrStaNivelAcessoGlobalProtocolo(), $objProcedimentoDTO->getStrStaNivelAcessoLocalProtocolo()])) {
            return null;
        }

        $arrCredencial = (new MdPetProcedimentoRN())->concederCredencial([$objProcedimentoDTO, $idUnidadeProcesso]);
        SessaoSEI::getInstance()->simularLogin(null, null, $arrCredencial[4], $arrCredencial[6]);

        return $arrCredencial;
    }

    /**
     * @param array<int, mixed>|null $arrCredencial dados devolvidos por concederCredencialParaDesfazer
     * @return void
     */
    private function cassarCredencialDoDesfazimento(?array $arrCredencial): void
    {
        if ($arrCredencial === null) {
            return;
        }

        $objMdPetProcedimentoRN = new MdPetProcedimentoRN();
        $objMdPetProcedimentoRN->cassarCredencial($arrCredencial);
        $objMdPetProcedimentoRN->excluirAndamentoCredencial($arrCredencial);
    }

    /**
     * Devolve à situação anterior o processo que a primeira etapa reabriu. Roda depois do desfazimento, em transação
     * própria: se o core recusar a conclusão, o peticionamento já foi desfeito, e a falha fica só no rastro.
     *
     * @param list<int> $arrIdUnidades unidades reabertas pela primeira etapa
     * @return void
     */
    private function concluirProcessoNasUnidadesReabertas(array $arrIdUnidades, string $strFluxo): void
    {
        foreach ($arrIdUnidades as $idUnidade) {
            try {
                foreach ($this->listarIdUsuariosDaConclusao($idUnidade) as $idUsuario) {
                    SessaoSEI::getInstance()->simularLogin(null, null, $idUsuario, $idUnidade);
                    $objEntradaConcluirProcessoAPI = new EntradaConcluirProcessoAPI();
                    $objEntradaConcluirProcessoAPI->setIdProcedimento($this->getProcedimentoDTO()->getDblIdProcedimento());
                    (new SeiRN())->concluirProcesso($objEntradaConcluirProcessoAPI);
                }
                MdPetRastreioINT::registrar($strFluxo . ': depois do rollback, o processo foi concluído de novo na unidade ' . $idUnidade . ', como estava antes da reabertura.');
            } catch (\Throwable $objErroConclusao) {
                MdPetRastreioINT::registrarFalha($strFluxo . ': depois do rollback, o processo não foi concluído de novo na unidade ' . $idUnidade . ' e continua aberto nela.', $objErroConclusao);
            }
        }
    }

    /**
     * O processo sigiloso fica aberto para cada usuário com credencial na unidade, e só ele conclui; nos demais, basta o
     * usuário externo, como na reabertura.
     *
     * @param int $idUnidade unidade reaberta
     * @return list<int>
     */
    private function listarIdUsuariosDaConclusao($idUnidade): array
    {
        $objProcedimentoDTO = $this->getProcedimentoDTO();

        if ($objProcedimentoDTO === null || $objProcedimentoDTO->getStrStaNivelAcessoGlobalProtocolo() !== ProtocoloRN::$NA_SIGILOSO) {
            return [SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno()];
        }

        $objAcessoDTO = new AcessoDTO();
        $objAcessoDTO->retNumIdUsuario();
        $objAcessoDTO->setDblIdProtocolo($objProcedimentoDTO->getDblIdProcedimento());
        $objAcessoDTO->setNumIdUnidade($idUnidade);
        $objAcessoDTO->setStrStaTipo(AcessoRN::$TA_CREDENCIAL_PROCESSO);

        return InfraArray::converterArrInfraDTO((new AcessoRN())->listar($objAcessoDTO), 'IdUsuario');
    }

    /**
     * @param int|string $idProcedimento processo
     * @return list<int> unidades em que o processo está aberto
     */
    private function listarIdUnidadesAbertas($idProcedimento): array
    {
        return array_values(array_unique(InfraArray::converterArrInfraDTO($this->retornaUnidadesProcessoAberto($idProcedimento), 'IdUnidade')));
    }

    /**
     * Confere se o recibo continua no banco: a etapa cancelada pode ter deixado em memória um recibo que voltou com a
     * transação.
     *
     * @param MdPetReciboDTO $objReciboDTO recibo guardado pela etapa
     * @return bool
     */
    private function reciboFoiGravado(MdPetReciboDTO $objReciboDTO, string $strFluxo): bool
    {
        $objReciboPesquisaDTO = new MdPetReciboDTO();
        $objReciboPesquisaDTO->setNumIdReciboPeticionamento($objReciboDTO->getNumIdReciboPeticionamento());

        if ((new MdPetReciboRN())->contar($objReciboPesquisaDTO) > 0) {
            return true;
        }

        MdPetRastreioINT::registrarPassoDoRollback($strFluxo, 'recibo ' . $objReciboDTO->getNumIdReciboPeticionamento() . ' já tinha sido cancelado pelo banco junto com a etapa que falhou.');
        return false;
    }

    private function _controlarAcessoExterno($arrDados, $arrObjDocumentos, $objReciboDTO)
    {
        $idProcedimento = array_key_exists('idProcedimento', $arrDados) ? $arrDados['idProcedimento'] : null;

        $arrRetorno = array();

        if (count($arrObjDocumentos) > 0) {
            $arrRetorno = InfraArray::converterArrInfraDTO($arrObjDocumentos, 'IdDocumento');
        }

        if (!is_null($objReciboDTO)) {
            $idDocumento = $objReciboDTO->getDblIdDocumento();
            array_push($arrRetorno, $idDocumento);
        }

        if (!is_null($idProcedimento)) {
            $objMdPetAcessoExternoRN = new MdPetAcessoExternoRN();
            $objMdPetAcessoExternoRN->aplicarRegrasGeraisAcessoExterno($idProcedimento, MdPetAcessoExternoRN::$MD_PET_PROCESSO_INTERCORRENTE, null, null, null, $arrRetorno);
        }
    }

    protected function lancarAndamentoIntercorrenteControlado($documentoDTO)
    {

        $documentoRN = new DocumentoRN();
        $documentoReciboDTO = new DocumentoDTO();
        $documentoReciboDTO->retStrProtocoloDocumentoFormatado();
        $documentoReciboDTO->setDblIdDocumento($documentoDTO->getDblIdDocumento());
        $documentoReciboDTO = $documentoRN->consultarRN0005($documentoReciboDTO);

        $arrParametrosResp['idProcedimento'] = $documentoDTO->getDblIdProcedimento();
        $arrParametrosResp['nomeTipoResposta'] = MdPetIntDestRespostaRN::$PETICIONAMENTO_INTERCORRENTE;
        $arrParametrosResp['nomeDocumentoPrincipal'] = $documentoReciboDTO->getStrProtocoloDocumentoFormatado();
        $arrParametrosResp['idUnidade'] = $documentoDTO->getNumIdUnidadeResponsavel();
        $arrParametrosResp['idDocumento'] = $documentoDTO->getDblIdDocumento();

        $objMdPetIntDestRespostaRN = new MdPetIntDestRespostaRN();
        $objMdPetIntDestRespostaRN->lancarAndamentoRecibo($arrParametrosResp);

    }

    //metodo responsavel por gravar todo conteudo da resposta a intimacao de pessoa fisica
    private function salvarResposta($params, $arrObjMdPetRelReciboDocumentoAnexoDTO, $unidadeDTO, $documentoReciboDTO)
    {

        $tipoPessoa = "";
        $objMdPetIntDestRespostaRN = new MdPetIntDestRespostaRN();
        $objMdPetIntRelDestRN = new MdPetIntRelDestinatarioRN();
        $objMdPetIntDestRespostaDTO = new MdPetIntDestRespostaDTO();
        //$id_intimacao = $params['id_intimacao'];
        $id_tipo_resposta = $params['id_tipo_resposta'];
        $id_procedimento = $params['id_procedimento'];

        //o sistema nao retornou dados de usuario externo em consulta via SeiRN->listarUsuarios (motivo ainda desconhecido), apelando para o uso da classe UsuarioRN
        $usuarioRN = new UsuarioRN();
        $usuarioDTO = new UsuarioDTO();
        $usuarioDTO->retNumIdContato();
        $usuarioDTO->retStrNome();
        $usuarioDTO->setNumIdUsuario(SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno());
        $usuarioDTO = $usuarioRN->consultarRN0489($usuarioDTO);


        //Recuperando Id Intimação

        $objMdPetIntRelDestinatarioDTO = new MdPetIntRelDestinatarioDTO();
        $objMdPetIntRelDestinatarioDTO->setNumIdMdPetIntRelDestinatario($params['id_int_rel_dest']);
        $objMdPetIntRelDestinatarioDTO->retNumIdMdPetIntimacao();
        $objMdPetIntRelDestinatarioRN = new MdPetIntRelDestinatarioRN();
        $objMdPetIntRelDestinatarioDTO = $objMdPetIntRelDestinatarioRN->consultar($objMdPetIntRelDestinatarioDTO);

        $id_intimacao = $objMdPetIntRelDestinatarioDTO->getNumIdMdPetIntimacao();

        $rnDestinatario = new MdPetIntRelDestinatarioRN();
        $dtoDestinatario = new MdPetIntRelDestinatarioDTO();
        $dtoDestinatario->retTodos(true);
        $dtoDestinatario->setNumIdContatoParticipante($usuarioDTO->getNumIdContato());
        //$dtoDestinatario->setNumIdMdPetIntimacao( $id_intimacao );

        $dtoDestinatario->setNumIdMdPetIntRelDestinatario($params['id_int_rel_dest']);

        $arrDtoDestinatario = $rnDestinatario->listar($dtoDestinatario);

        $idRelDest = $arrDtoDestinatario[0]->getNumIdMdPetIntRelDestinatario();
        $idAcessoExterno = $arrDtoDestinatario[0]->getNumIdAcessoExterno();

//                $objUsuarioDTO = new UsuarioDTO();
//                $objUsuarioDTO->retNumIdContato();
//                $objUsuarioDTO->retNumIdUsuario();
//                $objUsuarioDTO->retStrNome();
//                $objUsuarioDTO->setNumIdUsuario(SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno());
//                $objUsuarioRN  = new UsuarioRN();
//                $objUsuarioDTO = $objUsuarioRN->consultarRN0489($objUsuarioDTO);
//                echo "<pre>";
//                var_dump($objUsuarioDTO);
//                die;
        $objMdPetIntDestRespostaDTO->setNumIdMdPetIntRelDestinatario($idRelDest);

        $objMdPetIntDestRespostaDTO->setStrIp(InfraUtil::getStrIpUsuario());
        $objMdPetIntDestRespostaDTO->setDthData(InfraData::getStrDataHoraAtual());
        $objMdPetIntDestRespostaDTO->setNumIdMdPetIntRelTipoResp($id_tipo_resposta);
        $objMdPetIntDestRespostaDTO->setNumIdUsuario(SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno());

        //cadastrando a resposta do destinatario nesta intimação
        $objMdPetIntDestRespostaDTO = $objMdPetIntDestRespostaRN->cadastrar($objMdPetIntDestRespostaDTO);

        //Atualiza o novo campo de Situação da Intimação
        $objMdPetIntRelDestRN->atualizarStatusIntimacao(array(MdPetIntimacaoRN::$INTIMACAO_RESPONDIDA, $idRelDest));

        //vinculado documentos anexados que compoem esta resposta
        $arquivos_enviados = PaginaSEIExterna::getInstance()->getArrItensTabelaDinamica($params['hdnTbDocumento']);

        $rnDocResposta = new MdPetIntRelRespDocRN();

        $rnIntimacao = new MdPetIntimacaoRN();
        $dtoIntimacao = new MdPetIntimacaoDTO();
        $dtoIntimacao->retTodos();
        $dtoIntimacao->setNumIdMdPetIntimacao($id_intimacao);
        $dtoIntimacao = $rnIntimacao->consultar($dtoIntimacao);

        $sinTipoAcesso = $this->_getTipoAcessoExterno($idAcessoExterno);

        if (is_array($arrObjMdPetRelReciboDocumentoAnexoDTO) && count($arrObjMdPetRelReciboDocumentoAnexoDTO) > 0) {

            $arrDocAcessoExt = array($documentoReciboDTO->getDblIdDocumento());

            foreach ($arrObjMdPetRelReciboDocumentoAnexoDTO as $docDTO) {
                $arrDocAcessoExt[] = $docDTO->getNumIdDocumento();
            }

            $mdPetIntRelDestRN = new MdPetIntRelDestinatarioRN();
            $objMdPetIntRelDest = new MdPetIntRelDestinatarioDTO();
            $objMdPetIntRelDest->retNumIdAcessoExterno();
            $objMdPetIntRelDest->retNumIdUsuario();
            $objMdPetIntRelDest->setNumIdMdPetIntimacao($id_intimacao);
            $objMdPetIntRelDest->setNumIdContato($params['id_contato']);
            $arrMdPetIntRelDest = $mdPetIntRelDestRN->listar($objMdPetIntRelDest);

            $id_contato = $usuarioDTO->getNumIdContato();
            $contatoRN = new ContatoRN();
            $contatoDTO = new ContatoDTO();
            $contatoDTO->retStrEmail();
            $contatoDTO->retNumIdContato();
            $contatoDTO->setNumIdContato($id_contato);

            $contatoDTO = $contatoRN->consultarRN0324($contatoDTO);

            //so precisa intervir no acesso externo (para adicionar docs a mais nele, ou seja, ampliar o acesso ext) caso se trate de acesso ext parcial, para acesso integral não é necessário intervir


            if (!is_null($sinTipoAcesso) && $sinTipoAcesso == MdPetIntAcessoExternoDocumentoRN::$ACESSO_PARCIAL) {

                $objRelDestRN = new MdPetIntRelDestinatarioRN();
                $objRelDestDTO = new MdPetIntRelDestinatarioDTO();
                $objRelDestDTO->retTodos(true);
                $objRelDestDTO->setStrSinAtivo('S');
                $objRelDestDTO->setNumIdContatoParticipante($id_contato);
                $objRelDestDTO->setNumIdMdPetIntimacao($id_intimacao);
                $objRelDestDTO->setNumMaxRegistrosRetorno(1);
                $objRelDestDTO = $objRelDestRN->consultar($objRelDestDTO);

                //recuperando o id do acesso ext parcial, para amplia-lo
                $idAcessoExtParcial = $objRelDestDTO->getNumIdAcessoExterno();

                //Verifica o Tipo de Destinatário

                //Pessoa Juridica
                $mdPetVinculoRN = new MdPetVinculoRN();
                $mdPetVinculoDTO = new MdPetVinculoDTO();
                $mdPetVinculoDTO->setNumIdContato($params['id_contato']);
                $mdPetVinculoDTO->setStrStaEstado(MdPetVincRepresentantRN::$RP_ATIVO);
                $mdPetVinculoDTO->retNumIdContatoRepresentante();
                $mdPetVinculoDTO->retNumIdUsuario();
                $mdPetVinculoDTO->retStrStaEstado();
                $mdPetVinculoDTO->retDthDataEncerramento();
                $arrMdPetVinculoDTO = $mdPetVinculoRN->listar($mdPetVinculoDTO);
                $arrRepresentantes = InfraArray::converterArrInfraDTO($arrMdPetVinculoDTO, 'IdUsuario');

                $tipoPessoa = count($arrRepresentantes) ? 'J' : 'F';

                foreach ($arrDocAcessoExt as $docResposta) {

                    foreach ($arrMdPetIntRelDest as $objAcessoExterno) {
                        
                        if ($tipoPessoa == 'J') {

                            if (in_array($objAcessoExterno->getNumIdUsuario(), $arrRepresentantes)) {
                                $this->_cadastraAcessoProtocolo($objAcessoExterno->getNumIdAcessoExterno(), $docResposta);
                            }

                        } else {

                            $this->_cadastraAcessoProtocolo($objAcessoExterno->getNumIdAcessoExterno(), $docResposta);

                        }

                    }

                }

            }

        }

        foreach ($arrObjMdPetRelReciboDocumentoAnexoDTO as $doc) {

            $dtoDocResposta = new MdPetIntRelRespDocDTO();
            $dtoDocResposta->setNumIdMdPetIntDestResposta($objMdPetIntDestRespostaDTO->getNumIdMdPetIntDestResposta());
            $dtoDocResposta->setDblIdDocumento($doc->getNumIdDocumento());
            $dtoDocResposta = $rnDocResposta->cadastrar($dtoDocResposta);

        }

        $objMdPetReciboDTO = new MdPetReciboDTO();
        $objMdPetReciboDTO->retStrNumeroProcessoFormatadoDoc();
        $objMdPetReciboDTO->setDblIdDocumento($documentoReciboDTO->getDblIdDocumento());
        $objMdPetReciboRN = new MdPetReciboRN();

        $objMdPetRecibo = $objMdPetReciboRN->consultar($objMdPetReciboDTO);

        //Setando os parametros para lancar resposta andamento
        $arrParametros['idUnidade'] = $unidadeDTO->getNumIdUnidade();
        $arrParametros['idDocumento'] = $documentoReciboDTO->getDblIdDocumento();
        $arrParametros['nomeDocumentoPrincipal'] = $objMdPetRecibo->getStrNumeroProcessoFormatadoDoc();
        $arrParametros['idMdPetIntimacao'] = $id_intimacao;
        $arrParametros['idProcedimento'] = $id_procedimento;
        $arrParametros['nomeTipoResposta'] = MdPetIntDestRespostaRN::$TIPO_PETICIONAMENTO_INTIMACAO;

        $objMdPetIntDestRespostaRN = new MdPetIntDestRespostaRN();

        $objMdPetIntDestRespostaRN->lancarAndamentoRecibo($arrParametros);

    }

    private function _cadastraAcessoProtocolo($idAcessoExterno, $docResposta){

        $objRelAcessoExtProtocoloBD = new RelAcessoExtProtocoloBD($this->getObjInfraIBanco());

        $objRelAcessoExtProtocoloDTO = new RelAcessoExtProtocoloDTO();
        $objRelAcessoExtProtocoloDTO->setNumIdAcessoExterno($idAcessoExterno);
        $objRelAcessoExtProtocoloDTO->setDblIdProtocolo($docResposta);
        $objRelAcessoExtProtocoloDTO->retTodos();
        $arrObjRelAcessoExtProtocoloDTO = $objRelAcessoExtProtocoloBD->consultar($objRelAcessoExtProtocoloDTO);
        if(!$arrObjRelAcessoExtProtocoloDTO) {
            $objRelAcessoExtProtocoloBD->cadastrar($objRelAcessoExtProtocoloDTO);
        }

    }

    private function _getTipoAcessoExterno($idAcessoExterno)
    {
        $objMdPetAcessoExternoRN = new MdPetAcessoExternoRN();
        $arrRetorno = $objMdPetAcessoExternoRN->getTipoAcessoExternoPorAcessoExterno(array($idAcessoExterno));

        $tpAcesso = array_key_exists($idAcessoExterno, $arrRetorno) ? $arrRetorno[$idAcessoExterno] : null;

        return $tpAcesso;
    }

    private function getDocumentoRecibo()
    {
        return $this->documentoRecibo;
    }

    private function setDocumentoRecibo($documentoDTO)
    {
        $this->documentoRecibo = $documentoDTO;
    }

    private function simularLogin($idUnidade, $idUsuario = null)
    {
        $idUsuario = $idUsuario == null ? SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno() : $idUsuario;

        SessaoSEI::getInstance()->simularLogin(null, null, $idUsuario, $idUnidade);
        $this->setUnidadeDTO($idUnidade);
    }

    private function validarCadastro($params)
    {
        // Bloco de validações
        $objInfraException = new InfraException();
        $this->validarSenhaSei($params['pwdsenhaSEI'], $objInfraException);
        $this->validarDocumentos($params['hdnTbDocumento'], $objInfraException);
        $this->validarNumIdProcedimento($params['id_procedimento'], $objInfraException);
        $this->validarNumIdTipoProcedimento($params['id_tipo_procedimento'], $objInfraException);
        $objInfraException->lancarValidacoes();
    }

    private function validarSenhaSei($senha, InfraException $objInfraException)
    {
        if (InfraString::isBolVazia($senha)) {
            $objInfraException->adicionarValidacao('Senha não informada.');
        }
        $objMdPetProcessoRN = new MdPetProcessoRN();
        $objMdPetProcessoRN->validarSenha(array('pwdsenhaSEI' => $senha));
    }

    private function validarDocumentos($hdnTbDocumento, InfraException $objInfraException)
    {
        if (InfraString::isBolVazia($hdnTbDocumento)) {
            $objInfraException->adicionarValidacao('Nenhum documento foi enviado.');
        }
    }

    /**
     * ADR-PET-005: passa pelo filtro XSS do core o nome e o complemento de cada documento do Peticionamento
     * Intercorrente e da Resposta a Intimação e confere os arquivos com as regras do upload, inclusive o limite de tamanho
     * do SEI (ADR-PET-010), antes de gravar.
     *
     * @param string $strTbDocumento tabela de documentos da tela: complemento na coluna 2, arquivo temporário na 7 e
     * nome do arquivo na 9
     * @param string $strDiretorio diretório dos arquivos temporários
     * @return void
     * @throws InfraException validação para o usuário quando o conteúdo não é permitido ou o arquivo passa do limite
     */
    public function validarXssDocumentos(string $strTbDocumento, string $strDiretorio = DIR_SEI_TEMP): void
    {
        $objInfraException = new InfraException();
        $arrArquivos = [];

        $arrDocumentos = PaginaSEIExterna::getInstance()->getArrItensTabelaDinamica($strTbDocumento);
        foreach ($arrDocumentos as $arrDocumento) {
            PeticionamentoIntegracao::validarXssCampo($arrDocumento[9] ?? null, 'Nome de arquivo enviado', $objInfraException);
            PeticionamentoIntegracao::validarXssComplemento($arrDocumento[2] ?? null, $objInfraException);
            $arrArquivos[$arrDocumento[7] ?? ''] = $arrDocumento[9] ?? '';
        }

        $objInfraException->lancarValidacoes();

        MdPetTamanhoArquivoRN::validarQuantidadeAnexosExternos(
            count($arrDocumentos),
            new InfraParametro(BancoSEI::getInstance()),
            LogSEI::getInstance()
        );
        PeticionamentoIntegracao::validarArquivosDisponiveisConclusao($arrArquivos, $strDiretorio);
        PeticionamentoIntegracao::validarXssConclusao(null, $arrArquivos, $strDiretorio);
        PeticionamentoIntegracao::validarNomesAnexosConclusao($arrArquivos);
        PeticionamentoIntegracao::validarTamanhoConclusao($arrArquivos, $strDiretorio);
    }

    private function validarNumIdProcedimento($numIdProcedimento, InfraException $objInfraException)
    {
        if (InfraString::isBolVazia($numIdProcedimento)) {
            $objInfraException->adicionarValidacao('Processo não informado.');
        }
    }

    private function validarNumIdTipoProcedimento($numIdTipoProcedimento, InfraException $objInfraException)
    {
        if (InfraString::isBolVazia($numIdTipoProcedimento)) {
            $objInfraException->adicionarValidacao('Tipo de procedimento não informado.');
        }
    }

    private function setUnidadeDTO($numIdUnidade)
    {
        $objUnidadeConsultaDTO = new UnidadeDTO();
        $objUnidadeConsultaDTO->retTodos(true);
        $objUnidadeConsultaDTO->setNumIdUnidade($numIdUnidade);
        $objUnidadeRN = new UnidadeRN();
        $this->objUnidadeDTO = $objUnidadeRN->consultarRN0125($objUnidadeConsultaDTO);
        $this->setOrgaoDTO($this->objUnidadeDTO->getNumIdOrgao());
    }

    private function getUnidadeDTO()
    {
        return $this->objUnidadeDTO;
    }

    private function setOrgaoDTO($numIdOrgao)
    {
        $orgaoDTO = new OrgaoDTO();
        $orgaoDTO->retTodos();
        $orgaoDTO->retStrEmailContato();
        $orgaoDTO->setNumIdOrgao($numIdOrgao);
        $orgaoDTO->setStrSinAtivo('S');
        $orgaoRN = new OrgaoRN();
        $this->objOrgaoDTO = $orgaoRN->consultarRN1352($orgaoDTO);

    }

    private function getOrgaoDTO()
    {
        return $this->objOrgaoDTO;
    }

    private function setCargoDTO($numIdCargo)
    {
        $cargoDTO = new CargoDTO();
        $cargoDTO->retNumIdCargo();
        $cargoDTO->retStrExpressao();
        $cargoDTO->retStrSinAtivo();
        $cargoDTO->setNumIdCargo($numIdCargo);
        $cargoDTO->setStrSinAtivo('S');
        $cargoRN = new CargoRN();
        $this->objCargoDTO = $cargoRN->consultarRN0301($cargoDTO);
    }

    private function getCargoDTO()
    {
        return $this->objCargoDTO;
    }

    private function getUsuarioDTO()
    {
        if ($this->objUsuarioDTO === null) {
            $objUsuarioDTO = new UsuarioDTO();
            $objUsuarioDTO->retTodos();
            $objUsuarioDTO->setNumIdUsuario(SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno());

            $objUsuarioRN = new UsuarioRN();
            $this->objUsuarioDTO = $objUsuarioRN->consultarRN0489($objUsuarioDTO);
        }
        return $this->objUsuarioDTO;

    }

    private function setProcedimentoDTO($idProcedimento)
    {
        $objProcedimentoDTO = new ProcedimentoDTO();
        $objProcedimentoRN = new ProcedimentoRN();
        $objProcedimentoDTO->setDblIdProcedimento($idProcedimento);
        $objProcedimentoDTO->retDblIdProcedimento();
        $objProcedimentoDTO->retStrProtocoloProcedimentoFormatado();
        $objProcedimentoDTO->retTodos(true);
        $this->objProcedimentoDTO = $objProcedimentoRN->consultarRN0201($objProcedimentoDTO);
    }

    private function getProcedimentoDTO()
    {
        return $this->objProcedimentoDTO;
    }

    private function getSenha()
    {
        return $this->senha;
    }

    private function setSenha($senha)
    {
        $this->senha = $senha;
    }

    private function setReciboDTO($reciboDTO)
    {
        $this->reciboDTO = $reciboDTO;
    }

    private function getReciboDTO()
    {
        return $this->reciboDTO;
    }

    private function setParticipante($participanteDTO)
    {
        $this->participantesDTO = $participanteDTO;
    }

    private function getParticipanteDTO()
    {
        return $this->participantesDTO;
    }

    private function addDocumentos($documento)
    {
        $this->arrDocumentos[] = $documento;
    }

    private function getDocumentos()
    {
        return $this->arrDocumentos;
    }

    private function getContatoDTOUsuarioLogado()
    {

        $usuarioRN = new UsuarioRN();
        $usuarioDTO = new UsuarioDTO();
        $usuarioDTO->retNumIdUsuario();
        $usuarioDTO->setNumIdUsuario(SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno());
        $usuarioDTO->retNumIdContato();
        $usuarioDTO->retStrNomeContato();
        $usuarioDTO = $usuarioRN->consultarRN0489($usuarioDTO);

        $contatoRN = new ContatoRN();
        $contatoDTO = new ContatoDTO();
        $contatoDTO->retTodos();
        $contatoDTO->setNumIdContato($usuarioDTO->getNumIdContato());
        $contatoDTO = $contatoRN->consultarRN0324($contatoDTO);

        return $contatoDTO;
    }

    /**      $arquivos_enviados[0] recebe idLinha
     *        $arquivos_enviados[1] recebe idTipoDocumento
     *        $arquivos_enviados[2] recebe complementoTipoDocumento
     *        $arquivos_enviados[3] recebe idNivelAcesso
     *        $arquivos_enviados[4] recebe idHipoteseLegal
     *        $arquivos_enviados[5] recebe idFormato
     *        $arquivos_enviados[6] recebe idTipoConferencia
     *        $arquivos_enviados[7] recebe nomeArquivoHash
     *        $arquivos_enviados[8] recebe tamanhoArquivo
     *        $arquivos_enviados[9] recebe nomeArquivo
     *        $arquivos_enviados[10] recebe dataHora
     *        $arquivos_enviados[11] recebe tamanhoArquivoFormatado
     *        $arquivos_enviados[12] recebe documento
     *        $arquivos_enviados[13] recebe nivelAcesso
     *        $arquivos_enviados[14] recebe formato
     */
    public static function removerArquivoIntecorrenteTemp($arquivos_enviado)
    {
        //se for varios mandado pelo hdn
        if (is_string($arquivos_enviado)) {
            $arquivos_enviados = PaginaSEIExterna::getInstance()->getArrItensTabelaDinamica($arquivos_enviado);

            $xml = null;
            foreach ($arquivos_enviados as $arquivo_enviado) {
                $arquivo = DIR_SEI_TEMP . '/' . $arquivo_enviado[7];
                if (file_exists($arquivo)) {
                    unlink($arquivo);
                    $xml = "<success>true</success>";
                }
            }
        } else {
            $arquivo = DIR_SEI_TEMP . '/' . $arquivos_enviado[7];
            if (file_exists($arquivo)) {
                unlink($arquivo);
                $xml = "<success>true</success>";
            }
        }

        if ($xml)
            return $xml;

        return "<success>false</success>";
    }


    /**
     * $arquivos_enviados[0] recebe nome,
     * $arquivos_enviados[1] recebe dataHora,
     * $arquivos_enviados[2] recebe tamanhoFormatado,
     * $arquivos_enviados[3] recebe documento,
     * $arquivos_enviados[4] recebe nivelAcesso,
     * $arquivos_enviados[5] recebe hipoteseLegal,
     * $arquivos_enviados[6] recebe formatoDocumento,
     * $arquivos_enviados[7] recebe tipoConferencia,
     * $arquivos_enviados[8] recebe nomeUpload,
     * $arquivos_enviados[9] recebe idTpoPrincipal,
     * $arquivos_enviados[10] recebe strComplemento,
     * $arquivos_enviados[11] recebe formatoDocumentoLbl,
     * $arquivos_enviados[12] recebe ''
     */
    public static function removerArquivoTemp($arquivos_enviado)
    {
        $arquivo = DIR_SEI_TEMP . '/' . $arquivos_enviado[8];
        if (file_exists($arquivo)) {
            unlink($arquivo);
            return "<success>true</success>";
        }

        return "<success>false</success>";
    }


}

?>