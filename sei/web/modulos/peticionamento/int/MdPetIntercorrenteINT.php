<?
    /**
     * ANATEL
     *
     * 25/11/2016 - criado por jaqueline.mendes@cast.com.br - CAST
     *
     */

    require_once dirname(__FILE__) . '/../../../SEI.php';

    class MdPetIntercorrenteINT extends InfraINT
    {

    	public static function xmlToArray ( $xmlTexto ) {
    		
    		$Class = Array();
    		$Class['UsersFileXml'] = $xmlTexto;
    		$Class['FileXml'] = simplexml_load_string($Class['UsersFileXml']);
    		$Class['FileJson'] = json_encode($Class['FileXml']);
    		$Array = json_decode($Class['FileJson'],TRUE);
    		unset($Class);
    		
    		return $Array;
    	}
        
        /**
         * Função responsável por gerar o XML para validação do número Processo
         * @param int $numeroProcesso
         * @return string
         * @since  28/11/2016
         */
        public static function gerarXMLvalidacaoNumeroProcesso($numeroProcesso, $stRespostaIntimacao = false)
        {

            $xmlMensagemErro = '<Validacao><MensagemValidacao>%s</MensagemValidacao></Validacao>';
            $strMsgProcessoNaoExiste = 'O número de processo indicado não existe no sistema. Verifique se o número está correto e completo, inclusive com o Dígito Verificador.';
            $strMsgProcessoNaoAceitaPeticionamento = 'O processo indicado não aceita peticionamento intercorrente. Utilize o Peticionamento de Processo Novo para protocolizar sua demanda.';

            $objMdPetIntercorrenteRN = new MdPetIntercorrenteProcessoRN();
            $objProtocoloDTO         = new ProtocoloDTO();
            $objProtocoloDTO->setStrProtocoloFormatadoPesquisa(InfraUtil::retirarFormatacao($numeroProcesso, false));
            $objProtocoloDTO = $objMdPetIntercorrenteRN->pesquisarProtocoloFormatado($objProtocoloDTO);
            $xml = '<Validacao>';

            if (!$objProtocoloDTO || $objProtocoloDTO == null || $objProtocoloDTO == '' ) {
                return sprintf($xmlMensagemErro, $strMsgProcessoNaoExiste);
            }

            $objProcedimentoDTO = new ProcedimentoDTO();
            $objProcedimentoRN  = new ProcedimentoRN();

            $objProcedimentoDTO->setDblIdProcedimento($objProtocoloDTO->getDblIdProtocolo());
            $objProcedimentoDTO->retTodos(true);
            $objProcedimentoDTO = $objProcedimentoRN->consultarRN0201($objProcedimentoDTO);

            $unidadeValida  = $objMdPetIntercorrenteRN->validarUnidadeProcesso($objProcedimentoDTO);

            if (! $unidadeValida) {
                return sprintf($xmlMensagemErro, $strMsgProcessoNaoAceitaPeticionamento);
            }

            $objAtividadeDTO = new AtividadeDTO();
            $objAtividadeDTO->setDblIdProcedimentoProtocolo($objProcedimentoDTO->getDblIdProcedimento());
            $idUnidadeReabrirProcesso = $objMdPetIntercorrenteRN->retornaUltimaUnidadeProcessoConcluido($objAtividadeDTO);

            $unidadeDTO = new UnidadeDTO();
            $unidadeDTO->retTodos();
            $unidadeDTO->setBolExclusaoLogica(false);
            $unidadeDTO->setNumIdUnidade($idUnidadeReabrirProcesso);
            $unidadeRN = new UnidadeRN();
            $objUnidadeDTO = $unidadeRN->consultarRN0125($unidadeDTO);

            if($objUnidadeDTO->getStrSinAtivo() == 'N'){
                $idUnidadeReabrirProcesso = null;
                $objAtividadeRN  = new MdPetIntercorrenteAtividadeRN();
                $arrObjUnidadeDTO = $objAtividadeRN->listarUnidadesTramitacao($objProcedimentoDTO);

                foreach ($arrObjUnidadeDTO as $itemObjUnidadeDTO) {
                    if ($itemObjUnidadeDTO->getStrSinAtivo() == 'S') {
                        $idUnidadeReabrirProcesso = $itemObjUnidadeDTO->getNumIdUnidade();
                    }
                }

                if($idUnidadeReabrirProcesso == null) {
                    return sprintf($xmlMensagemErro, $strMsgProcessoNaoAceitaPeticionamento);
                }
            }

            $objMdPetCriterioDTO = new MdPetCriterioDTO();
            $objMdPetCriterioRN  = new MdPetCriterioRN();
            $objMdPetCriterioDTO->setNumIdTipoProcedimento($objProcedimentoDTO->getNumIdTipoProcedimento());
            $objMdPetCriterioDTO->setStrSinCriterioPadrao('N');
            if (!$stRespostaIntimacao) {
              $objMdPetCriterioDTO->setStrSinAtivo('S');
            }
            $objMdPetCriterioDTO->retTodos(true);

            $contadorCriterioIntercorrente = $objMdPetCriterioRN->contar($objMdPetCriterioDTO);

            $estadosReabrirRelacionado = array(ProtocoloRN::$TE_PROCEDIMENTO_SOBRESTADO, ProtocoloRN::$TE_PROCEDIMENTO_BLOQUEADO);
            /**
             * Verifica se:
             * 1 - Se o processo eh sigiloso (nivel de acesso global ou local eh igual a 2)
             * 2 - Se o Tipo do Processo do procedimento informado nao possui um intercorrente cadastrado(neste caso irah utilizar o Intercorrente Padrao)
             */
            $processoIntercorrente = 'Direto no Processo Indicado';

            if ($contadorCriterioIntercorrente <= 0 && !$stRespostaIntimacao
              || in_array($objProcedimentoDTO->getStrStaEstadoProtocolo(), $estadosReabrirRelacionado)) {
              $processoIntercorrente = 'Em Processo Novo Relacionado ao Processo Indicado';
            } elseif ($objProcedimentoDTO->getStrStaEstadoProtocolo() == ProtocoloRN::$TE_PROCEDIMENTO_ANEXADO) {
              $objRelProtocoloProtocoloRN = new RelProtocoloProtocoloRN();
              $objRelProtocoloProtocoloDTO = new RelProtocoloProtocoloDTO();
              $objRelProtocoloProtocoloDTO->retStrProtocoloFormatadoProtocolo1();
              $objRelProtocoloProtocoloDTO->setDblIdProtocolo2($objProcedimentoDTO->getDblIdProcedimento());
              $objRelProtocoloProtocoloDTO->setStrStaAssociacao(RelProtocoloProtocoloRN::$TA_PROCEDIMENTO_ANEXADO);
              $objRelProtocoloProtocoloDTO->setOrdDthAssociacao(InfraDTO::$TIPO_ORDENACAO_DESC);

              $dadosProtocoloAnexador = $objRelProtocoloProtocoloRN->consultarRN0841($objRelProtocoloProtocoloDTO);
              $processoIntercorrente = 'Diretamente no Processo Anexador nº ' . $dadosProtocoloAnexador->getStrProtocoloFormatadoProtocolo1();
            }



            $urlValida = PaginaSEIExterna::getInstance()->formatarXHTML(SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?id_procedimento=' . $objProcedimentoDTO->getDblIdProcedimento() . '&id_tipo_procedimento=' . $objProcedimentoDTO->getNumIdTipoProcedimento() . '&acao=md_pet_intercorrente_usu_ext_assinar&tipo_selecao=2'));

            $xml .= '<IdTipoProcedimento>' . $objProcedimentoDTO->getNumIdTipoProcedimento() . '</IdTipoProcedimento>';
            $xml .= '<IdProcedimento>' . $objProcedimentoDTO->getDblIdProcedimento() . '</IdProcedimento>';
            $xml .= '<numeroProcesso>' . $objProcedimentoDTO->getStrProtocoloProcedimentoFormatado() . '</numeroProcesso>';
            $xml .= '<TipoProcedimento> ' . $objProcedimentoDTO->getStrNomeTipoProcedimento() . ' </TipoProcedimento>';
            $xml .= '<ProcessoIntercorrente>' . $processoIntercorrente . '</ProcessoIntercorrente>';
            $xml .= '<DataGeracao> ' . $objProcedimentoDTO->getDtaGeracaoProtocolo() . ' </DataGeracao>';
            $xml .= '<UrlValida>' . htmlentities($urlValida) . '</UrlValida>';
            $xml .= '</Validacao>';

            return $xml;
        }


        /**
         * Função responsável por montar os options do select "Conferência com o documento digitalizado"
         * @param $strPrimeiroItemValor
         * @param $strPrimeiroItemDescricao
         * @param $strValorItemSelecionado
         * @return string
         * @since  29/11/2016
         * @author André Luiz <andre.luiz@castgroup.com.br>
         */
        public static function montarSelectTipoConferencia($strPrimeiroItemValor, $strPrimeiroItemDescricao, $strValorItemSelecionado)
        {
            $objTipoConferenciaDTO = new TipoConferenciaDTO();
            $objTipoConferenciaDTO->retNumIdTipoConferencia();
            $objTipoConferenciaDTO->retStrDescricao();
            $objTipoConferenciaDTO->setStrSinAtivo('S');
            $objTipoConferenciaDTO->setOrdStrDescricao(InfraDTO::$TIPO_ORDENACAO_ASC);
            $objTipoConferenciaRN     = new TipoConferenciaRN();
            $arrObjTipoConferenciaDTO = $objTipoConferenciaRN->listar($objTipoConferenciaDTO);

            return parent::montarSelectArrInfraDTO($strPrimeiroItemValor, $strPrimeiroItemDescricao, $strValorItemSelecionado, $arrObjTipoConferenciaDTO, 'IdTipoConferencia', 'Descricao');
        }


        /**
         * Função responsável por montar os options do select "Tipo de Documento"
         * @param $strPrimeiroItemValor
         * @param $strPrimeiroItemDescricao
         * @param $strValorItemSelecionado
         * @return string
         * @since  29/11/2016
         * @author André Luiz <andre.luiz@castgroup.com.br>
         */
        public static function montarSelectTipoDocumento($strPrimeiroItemValor, $strPrimeiroItemDescricao, $strValorItemSelecionado)
        {

            $objSerieRN  = new SerieRN();
            $objSerieDTO = new SerieDTO();
            $objSerieDTO->retTodos(true);
            $objSerieDTO->setStrSinAtivo('S');
            $objSerieDTO->setOrdStrNome(InfraDTO::$TIPO_ORDENACAO_ASC);
            $objSerieDTO->adicionarCriterio(array('StaAplicabilidade'),
                                            array(InfraDTO::$OPER_IN),
                                            array(array(SerieRN::$TA_INTERNO_EXTERNO, SerieRN::$TA_EXTERNO)));

            $arrSerieDTO = $objSerieRN->listarRN0646($objSerieDTO);


            return parent::montarSelectArrInfraDTO($strPrimeiroItemValor, $strPrimeiroItemDescricao, $strValorItemSelecionado, $arrSerieDTO, 'IdSerie', 'Nome');
        }

        /**
         * Função responsável por retornoar o tamanho maximo permitido para upload
         * Configuração realizada em Administração > Peticionamento Eletrônico > Tamanho Máximo de Arquivos
         * @return string
         * @since  29/11/2016
         * @author André Luiz <andre.luiz@castgroup.com.br>
         */
        public static function tamanhoMaximoArquivoPermitido()
        {
            $tamanhoMaximo          = "Limite não configurado na Administração do Sistema.";
            $objTamanhoPermitidoDTO = new MdPetTamanhoArquivoDTO();
            $objTamanhoPermitidoDTO->setNumIdTamanhoArquivo(MdPetTamanhoArquivoRN::$ID_FIXO_TAMANHO_ARQUIVO);
            $objTamanhoPermitidoDTO->setStrSinAtivo('S');
            $objTamanhoPermitidoDTO->retNumValorDocComplementar();
            $objTamanhoPermitidoRN  = new MdPetTamanhoArquivoRN();
            $arrTamanhoPermitidoDTO = $objTamanhoPermitidoRN->listarTamanhoMaximoConfiguradoParaUsuarioExterno($objTamanhoPermitidoDTO);
            $objTamanhoPermitidoDTO = reset($arrTamanhoPermitidoDTO);
            if ($objTamanhoPermitidoDTO) {
                $tamanhoMaximo = (int)$objTamanhoPermitidoDTO->getNumValorDocComplementar();
            }

            return $tamanhoMaximo;
        }

        /**
         * Função responsável por verificar se a hipotese legal vai ser exibida ou não no fieldset Documentos
         * SOMENTE deve ser exibido SE no Infra > Parâmetros a opção SEI_HABILITAR_HIPOTESE_LEGAL estiver configurado
         * como 1 ou 2.
         * @return string
         * @since  05/12/2016
         * @author André Luiz <andre.luiz@castgroup.com.br>
         */
        public static function verificarHipoteseLegal()
        {
            $objInfraParametroDTO = new InfraParametroDTO();
            $objInfraParametroDTO->setStrNome('SEI_HABILITAR_HIPOTESE_LEGAL');
            $objInfraParametroDTO->retTodos();
            $objMdPetParametroRN = new MdPetParametroRN();

            $objInfraParametroDTO = $objMdPetParametroRN->consultar($objInfraParametroDTO);

            return $objInfraParametroDTO->isSetStrValor() &&
            ($objInfraParametroDTO->getStrValor() == 1 || $objInfraParametroDTO->getStrValor() == 2);
        }


        /**
         * Função responsável por verificar se existe criterio intercorrente cadastrado ou intercorrente padrão
         * cadastrado.
         * @param $idTipoProcessoPeticionamento
         * @return array
         * @since  06/12/2016
         * @author André Luiz <andre.luiz@castgroup.com.br>
         */
        public static function verificarCriterioIntercorrente($idTipoProcessoPeticionamento)
        {

            //Verifica se tem criterio intercorrente cadastrado;
            $objMdPetCriterioDTO = new MdPetCriterioDTO();
            $objMdPetCriterioDTO->setNumIdTipoProcedimento($idTipoProcessoPeticionamento);
            $objMdPetCriterioDTO->setStrSinCriterioPadrao('N');
            $objMdPetCriterioDTO->setStrSinAtivo('S');
            $objMdPetCriterioDTO->retTodos(true);

            $objMdPetCriterioRN  = new MdPetCriterioRN();
            $arrMdPetCriterioDTO = $objMdPetCriterioRN->consultar($objMdPetCriterioDTO);

            //Se não tem criterio intercorrente cadastrado, verifica se tem interorrente padrão cadastrado.
            if (is_null($arrMdPetCriterioDTO)) {
                $objMdPetCriterioDTO = new MdPetCriterioDTO();
                $objMdPetCriterioDTO->setStrSinCriterioPadrao('S');
                $objMdPetCriterioDTO->setStrSinAtivo('S');
                $objMdPetCriterioDTO->retTodos(true);

                $objMdPetCriterioRN  = new MdPetCriterioRN();
                $arrMdPetCriterioDTO = $objMdPetCriterioRN->consultar($objMdPetCriterioDTO);
            }

            $arrRetorno = array();
            if (!is_null($arrMdPetCriterioDTO)) {

                $arrDescricaoNivelAcesso = ['P' => 'Público', 'I' => 'Restrito'];
                $arrIdNivelAcesso        = ['P' => 0, 'I' => 1];

                if ($arrMdPetCriterioDTO->getStrStaNivelAcesso() == 2) { //2 = Padrão Pré-definido
                    $descricaoNivel = $arrDescricaoNivelAcesso[$arrMdPetCriterioDTO->getStrStaTipoNivelAcesso()];

                    $arrRetorno['nivelAcesso'] = array(
                        'id'        => $arrIdNivelAcesso[$arrMdPetCriterioDTO->getStrStaTipoNivelAcesso()],
                        'descricao' => utf8_encode($descricaoNivel)
                    );

                    if ($arrMdPetCriterioDTO->getStrStaTipoNivelAcesso() == 'I') {// I = Restrito
                        $descricaoHipotese = $arrMdPetCriterioDTO->getStrNomeHipoteseLegal() .
                            ' (' . $arrMdPetCriterioDTO->getStrBaseLegalHipoteseLegal() . ')';

                        $arrRetorno['hipoteseLegal'] = array(
                            'id'        => $arrMdPetCriterioDTO->getNumIdHipoteseLegal(),
                            'descricao' => utf8_encode($descricaoHipotese)
                        );

                    }
                }

            }
            return $arrRetorno;

        }

        /**
         * Função responsável por montar os options do select "Hipótese Legal"
         * @param $objEntradaListarHipotesesLegaisAPI
         * @return string
         * @since  08/12/2016
         * @author André Luiz <andre.luiz@castgroup.com.br>
         */
        public static function montarSelectHipoteseLegal()
        {
            $objMdPetHipoteseLegalDTO = new MdPetHipoteseLegalDTO();
            $objMdPetHipoteseLegalDTO->setStrNivelAcessoHl(ProtocoloRN::$NA_RESTRITO);
            $objMdPetHipoteseLegalDTO->setStrSinAtivo('S');
            $objMdPetHipoteseLegalDTO->retStrBaseLegal();
            $objMdPetHipoteseLegalDTO->retStrNome();
            $objMdPetHipoteseLegalDTO->retNumIdHipoteseLegalPeticionamento();
            $objMdPetHipoteseLegalDTO->setOrd("Nome", InfraDTO::$TIPO_ORDENACAO_ASC);

            $objHipoteseLegalPetRN       = new MdPetHipoteseLegalRN();
            $arrObjMdPetHipoteseLegalDTO = $objHipoteseLegalPetRN->listar($objMdPetHipoteseLegalDTO);
            $strOptions                  = '<select id="selHipoteseLegal" name="selHipoteseLegal"><option value=""> </option>';

            foreach ($arrObjMdPetHipoteseLegalDTO as $objMdPetHipoteseLegalDTO) {
                $nomeBaseLegal = $objMdPetHipoteseLegalDTO->getStrNome() . ' (' . $objMdPetHipoteseLegalDTO->getStrBaseLegal() . ')';
                $strOptions .= '<option value="' . $objMdPetHipoteseLegalDTO->getNumIdHipoteseLegalPeticionamento() . '">';
                $strOptions .= $nomeBaseLegal;
                $strOptions .= '</option>';
            }

            $strOptions .= '</select>';

            return $strOptions;
        }

        /**
         * Função responsável por montar o array com os documentos que foram adicionados na grid da tela Peticionamento
         * Interorrente
         * @param $idProcedimento
         * @param $hdnTabelaDinamicaDocumento
         * @return  array $arrDocumentoAPI
         * @since  15/12/2016
         * @author André Luiz <andre.luiz@castgroup.com.br>
         */
        public static function montarArrDocumentoAPI($idProcedimento, $hdnTabelaDinamicaDocumento)
        {

            $arrItensTbDocumento = PaginaSEIExterna::getInstance()->getArrItensTabelaDinamica($hdnTabelaDinamicaDocumento);
            $arrDocumentoAPI     = array();

            foreach ($arrItensTbDocumento as $itemTbDocumento) {
                $documentoAPI = new DocumentoAPI();
                $documentoAPI->setIdProcedimento($idProcedimento);
                $documentoAPI->setIdSerie($itemTbDocumento[1]);
                $documentoAPI->setDescricao($itemTbDocumento[2]);
                $documentoAPI->setNumero($itemTbDocumento[2]);
                $documentoAPI->setNivelAcesso($itemTbDocumento[3]);
                $documentoAPI->setIdHipoteseLegal($itemTbDocumento[4]);
                $documentoAPI->setIdTipoConferencia($itemTbDocumento[6]);
                $documentoAPI->setNomeArquivo($itemTbDocumento[9]);
                $documentoAPI->setTipo(ProtocoloRN::$TP_DOCUMENTO_RECEBIDO);
                $documentoAPI->setConteudo(base64_encode(file_get_contents(DIR_SEI_TEMP . '/' . $itemTbDocumento[7])));
                $documentoAPI->setData(InfraData::getStrDataAtual());
                $documentoAPI->setIdArquivo($itemTbDocumento[7]);
                $documentoAPI->setSinAssinado('S');
                $documentoAPI->setSinBloqueado('S');

                $arrDocumentoAPI[] = $documentoAPI;
            }

            return $arrDocumentoAPI;
        }

        /**
         * Função responsável por Retornar o Id do Anexo Salvo
         * @param SaidaIncluirDocumentoAPI $ret
         * @return  string $idAnexo
         * @since  20/12/2016
         * @author Jaqueline Mendes <jaqueline.mendes@castgroup.com.br>
         */
        public static function retornaIdAnexo($idDocumento){

            $arrObjAnexoDTO = array();
            $objAnexoDTO = new AnexoDTO();
            $objAnexoDTO->retNumIdAnexo();
            $objAnexoDTO->setDblIdProtocolo($idDocumento);
            $objAnexoRN =  new AnexoRN();
            $arrObjAnexoDTO = $objAnexoRN->listarRN0218($objAnexoDTO);
            $objAnexoDTO = count($arrObjAnexoDTO) > 0 ? current($arrObjAnexoDTO) : null;

            $idAnexo = $objAnexoDTO->getNumIdAnexo();
            return $idAnexo;
        }

        /**
         * Função responsável por gerar o XML para validação do número Processo
         * @param array $params
         * @return MdPetRelReciboDocumentoAnexoDTO $objMdPetRelReciboDocumentoAnexoDTO
         * @since  20/12/2016
         * @author Jaqueline Mendes jaqueline.mendes@castgroup.com.br
         */
        public static function retornaObjReciboDocPreenchido($params){
            $idDocumento = $params[0];
            $formato = $params[1];
            $idAnexo = MdPetIntercorrenteINT::retornaIdAnexo($idDocumento);
            $objMdPetRelReciboDocumentoAnexoDTO = new MdPetRelReciboDocumentoAnexoDTO();
            $objMdPetRelReciboDocumentoAnexoDTO->setNumIdAnexo($idAnexo);
            $objMdPetRelReciboDocumentoAnexoDTO->setNumIdDocumento($idDocumento);
            $objMdPetRelReciboDocumentoAnexoDTO->setStrClassificacaoDocumento(null);
            $objMdPetRelReciboDocumentoAnexoDTO->setStrFormatoDocumento($formato);

            return $objMdPetRelReciboDocumentoAnexoDTO;
    }


        /**
         * Função que verifica o tipo de formato de acorodo com o Documento
         * @param SaidaIncluirDocumentoAPI $objRetornoDoc
         * @return array $arrRetorno
         * @since  21/12/2016
         * @author Jaqueline Mendes jaqueline.mendes@castgroup.com.br
         */
        public static function retornaTipoFormatoDocumento($objDoc){
            $ids = null;
            $retorno = null;
            if(!is_null($objDoc))
            {

                if(is_array($objDoc)) {
                    $ids = array();
                    foreach ($objDoc as $doc) {
                        $idDocumento = $doc->getIdDocumento();
                        array_push($ids, $idDocumento);
                    }
                }else{
                    $ids = $objDoc->getIdDocumento();
                }

                $objDocumentoRN = new DocumentoRN();
                $objDocumentoDTO = new DocumentoDTO();
                is_array($ids) ? $objDocumentoDTO->setDblIdDocumento($ids, InfraDTO::$OPER_IN) : $objDocumentoDTO->setDblIdDocumento($ids);
                $objDocumentoDTO->retDblIdDocumento();
                $objDocumentoDTO->retNumIdTipoConferencia();
                $objDocs =  is_array($ids) ? $objDocumentoRN->listarRN0008($objDocumentoDTO) : $objDocumentoRN->consultarRN0005($objDocumentoDTO);


                if(is_array($objDocs)){
                    $retorno = array();
                foreach($objDocs as $objDoc){
                    $retorno[$objDoc->getDblIdDocumento()] = is_null($objDoc->getNumIdTipoConferencia()) ? MdPetIntercorrenteProcessoRN::$FORMATO_NATO_DIGITAL : MdPetIntercorrenteProcessoRN::$FORMATO_DIGITALIZADO;
                }
                }else{
                    $retorno = is_null($objDocs->getNumIdTipoConferencia()) ? MdPetIntercorrenteProcessoRN::$FORMATO_NATO_DIGITAL : MdPetIntercorrenteProcessoRN::$FORMATO_DIGITALIZADO;
                }
               
            }

            return $retorno;
        }
        
        /**
         * Função responsável por montar os options do select "Hipótese Legal" para a tela de resposta a intimacao
         * @return string
         * @author Marcelo Bezerra
         */
        public static function montarSelectHipoteseLegalRespostaIntimacao()
        {
        	$objMdPetHipoteseLegalDTO = new MdPetHipoteseLegalDTO();
        	$objMdPetHipoteseLegalDTO->setStrNivelAcessoHl(ProtocoloRN::$NA_RESTRITO);
        	$objMdPetHipoteseLegalDTO->setStrSinAtivo('S');
        	$objMdPetHipoteseLegalDTO->retStrBaseLegal();
        	$objMdPetHipoteseLegalDTO->retStrNome();
        	$objMdPetHipoteseLegalDTO->retNumIdHipoteseLegalPeticionamento();
        	$objMdPetHipoteseLegalDTO->setOrd("Nome", InfraDTO::$TIPO_ORDENACAO_ASC);
        	
        	$objHipoteseLegalPetRN       = new MdPetHipoteseLegalRN();
        	$arrObjMdPetHipoteseLegalDTO = $objHipoteseLegalPetRN->listar($objMdPetHipoteseLegalDTO);
        	$strOptions  = '<select id="selHipoteseLegal" class="infraSelect" onchange="salvarValorHipoteseLegal(this)"
                        tabindex="'. PaginaSEIExterna::getInstance()->getProxTabDados() . '"><option value=""> </option>';
        	
        	if( is_array( $arrObjMdPetHipoteseLegalDTO ) && count( $arrObjMdPetHipoteseLegalDTO ) > 0){
	        	
        		foreach ($arrObjMdPetHipoteseLegalDTO as $objMdPetHipoteseLegalDTO) {
	        		$nomeBaseLegal = $objMdPetHipoteseLegalDTO->getStrNome() . ' (' . $objMdPetHipoteseLegalDTO->getStrBaseLegal() . ')';
	        		$strOptions .= '<option value="' . $objMdPetHipoteseLegalDTO->getNumIdHipoteseLegalPeticionamento() . '">';
	        		$strOptions .= $nomeBaseLegal;
	        		$strOptions .= '</option>';
	        	}
        	}
        	
        	$strOptions .= '</select>';
        	
        	return $strOptions;
        }


        public static function removerNullsArr($ids){
            if(count($ids)>0 ) {
                foreach ($ids as $key => $valor) {

                    if (is_null($valor)) {
                        unset($ids[$key]);
                    }
                }

                return $ids;
            }
            return array();
        }

    }


