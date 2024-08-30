<?php

class MdPetIntCertidaoRN extends InfraRN
{

    public static $STR_TP_CUMPRIMENTO_CERTIDAO_ACESSO_DIRETO = 'Consulta Direta';
    public static $STR_TP_CUMPRIMENTO_CERTIDAO_PRAZO_TACITO = 'Por Decurso do Prazo Tácito';
    public static $STR_ID_SERIE_CERTIDAO = 'MODULO_PETICIONAMENTO_ID_SERIE_CERTIDAO_INTIMACAO_CUMPRIDA';

    public function __construct()
    {
        parent::__construct();
    }

    protected function inicializarObjInfraIBanco()
    {
        return BancoSEI::getInstance();
    }

    //funcao nao fazendo mençao a elementos de sessao do SEI Interno (classe SessaoSEI, PaginaSEI) apenas do externo (SessaoSEIExterna, PaginaSEIExterna)
    public function gerarCertidaoExternaControlado($arrParams)
    {


        //gerando documento certidão (nao assinada) dentro do processo do SEI
        try {

            $objInfraParametro = new InfraParametro($this->getObjInfraIBanco());
            $objSeiRN = new SeiRN();
            $objDocumentoRN = new DocumentoRN();
            $objMdPetIntAceiteRN = new MdPetIntAceiteRN();
            $objUsuarioPetRN = new MdPetIntUsuarioRN();
            $objMdPetIntDocAcessoExt = new MdPetIntAcessoExternoDocumentoRN();
            //Get parametros do Array
            $idIntimacao = $arrParams[0]; //Id Intimação
            $objUnidadeDTO = $arrParams[1]; //UnidadeDTO da unidade geradora do processo
            $objProcedimentoDTO = $arrParams[2]; //ProcedimentoDTO para vincular o recibo ao processo correto
            $objMdPetIntAceiteDTO = $arrParams[3];
            $objMdPetIntRelDestinatarioDTO = isset($arrParams[4]) ? $arrParams[4] : null;
            $job = isset($arrParams[5]) ? $arrParams[5] : false;
            $dataCumprimento = isset($arrParams[6]) ? $arrParams[6] : null;

            $idUsuario = $objUsuarioPetRN->getObjUsuarioPeticionamento(true);

            if (is_null($objUnidadeDTO)) {
                $detalhes = "Certidão de Intimação Cumprida referente ao Documento Principal " . $arrParams[6] . " no âmbito do Processo " . $objProcedimentoDTO->getStrProtocoloProcedimentoFormatado() . ", tendo em vista que todas as Unidades de tramitação estão desativadas.";
                throw new InfraException('Unidades de tramitação estão desativadas', null, $detalhes);
            }
            SessaoSEI::getInstance()->setNumIdUnidadeAtual($objUnidadeDTO->getNumIdUnidade());
            SessaoSEI::getInstance()->setNumIdUsuario(SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno());

            if (is_null($objMdPetIntRelDestinatarioDTO)) {
                //simular sessao de usuario interno do SEI
                $objContato = $objMdPetIntAceiteRN->retornaObjContatoIdUsuario(array(SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno()));
            } else {
                $objContato = $this->_retornarObjContatoPorId($objMdPetIntRelDestinatarioDTO->getNumIdContato());
            }

            if (is_null($objContato)) {
                $detalhes = "O contato " . $objMdPetIntRelDestinatarioDTO->getNumIdContato() . " está inativo.";
                throw new InfraException('Contato inativo', null, $detalhes);
            }

            $htmlCertidao = $this->gerarHTMLConteudoCertidao(array($idIntimacao, $objMdPetIntRelDestinatarioDTO, $objContato, $job, $dataCumprimento));
            $idSerieCertidao = $objInfraParametro->getValor(static::$STR_ID_SERIE_CERTIDAO);

            //=============================================
            //MONTAGEM DO DOCUMENTO VIA SEI RN
            //=============================================


            $objDocumentoAPI = new DocumentoAPI();
            $objDocumentoAPI->setIdProcedimento($objProcedimentoDTO->getDblIdProcedimento());
            $objDocumentoAPI->setSubTipo(DocumentoRN::$TD_FORMULARIO_AUTOMATICO);
            $objDocumentoAPI->setTipo(ProtocoloRN::$TP_DOCUMENTO_GERADO);
            $objDocumentoAPI->setIdSerie($idSerieCertidao);
            $objDocumentoAPI->setSinAssinado('N');
            $objDocumentoAPI->setSinBloqueado('S');
            $objDocumentoAPI->setIdHipoteseLegal(null);
            $objDocumentoAPI->setNivelAcesso(ProtocoloRN::$NA_PUBLICO);
            $objDocumentoAPI->setIdTipoConferencia(null);
            $objDocumentoAPI->setConteudo(base64_encode(utf8_encode($htmlCertidao)));
            $objDocumentoAPI->setIdUnidadeGeradora($objUnidadeDTO->getNumIdUnidade());

            $objSaidaDocumentoAPI = $objSeiRN->incluirDocumento($objDocumentoAPI);

            //necessario forçar update da coluna sta_documento da tabela documento
            //Add conteúdo atualizado com o nome do documento formatado gerado.
            //inclusao via SeiRN nao permitiu definir como documento de formulario automatico

            $objDocumentoDTO = new DocumentoDTO();

            $objDocumentoDTO->retTodos();
            $objDocumentoDTO->setDblIdDocumento($objSaidaDocumentoAPI->getIdDocumento());
            $objDocumentoDTO = $objDocumentoRN->consultarRN0005($objDocumentoDTO);

            $objDocumentoDTO->setStrStaDocumento(DocumentoRN::$TD_FORMULARIO_AUTOMATICO);

            $objDocumentoBD = new DocumentoBD($this->getObjInfraIBanco());
            $objDocumentoBD->alterar($objDocumentoDTO);

            $objMdPetIntAceiteDTO->setDblIdDocumentoCertidao($objDocumentoDTO->getDblIdDocumento());
            $objMdPetIntAceiteRN->alterar($objMdPetIntAceiteDTO);

            $objMdPetIntDocAcessoExt->verificarConcessaoAcessoExterno($job, $objDocumentoDTO, $objMdPetIntAceiteDTO);

            //Adicionando usuário no documento
            $objParticipante = new ParticipanteDTO();
            $objParticipante->setDblIdProtocolo($objSaidaDocumentoAPI->getIdDocumento());
            $objParticipante->setNumIdContato($objMdPetIntRelDestinatarioDTO->getNumIdContato());
            $objParticipante->setNumIdUnidade(SessaoSEI::getInstance()->getNumIdUnidadeAtual());
            $objParticipante->setStrStaParticipacao(ParticipanteRN::$TP_INTERESSADO);
            $objParticipante->setNumSequencia(0);

            $objParticipanteRN = new ParticipanteRN();
            $objParticipanteRN->cadastrarRN0170($objParticipante);

            return $objSaidaDocumentoAPI;
        } catch (Exception $ex) {
            PaginaSEIExterna::getInstance()->processarExcecao($ex);
        }
    }

    protected function gerarCertidaoControlado($arrParams)
    {
        //gerando documento certidão (nao assinada) dentro do processo do SEI

        $objInfraParametro = new InfraParametro($this->getObjInfraIBanco());
        $objSeiRN = new SeiRN();
        $objDocumentoRN = new DocumentoRN();
        $objMdPetIntAceiteRN = new MdPetIntAceiteRN();
        $objUsuarioPetRN = new MdPetIntUsuarioRN();
        $objMdPetIntDocAcessoExt = new MdPetIntAcessoExternoDocumentoRN();

        //Get parametros do Array
        $idIntimacao = $arrParams[0]; //Id Intimação
        $objUnidadeDTO = $arrParams[1]; //UnidadeDTO da unidade geradora do processo
        $objProcedimentoDTO = $arrParams[2]; //ProcedimentoDTO para vincular o recibo ao processo correto
        $objMdPetIntAceiteDTO = $arrParams[3];
        $objMdPetIntRelDestinatarioDTO = isset($arrParams[4]) ? $arrParams[4] : null;
        $job = isset($arrParams[5]) ? $arrParams[5] : false;
        $dataCumprimento = isset($arrParams[6]) ? $arrParams[6] : null;

        $dateCumprimento = strtotime(str_replace('/', '-', $dataCumprimento));
        $dateAtual = strtotime(str_replace('/', '-', date('d/m/Y')));

        if ($dateCumprimento < $dateAtual) {
            $dataCumprimento = date('d/m/Y');
        }

        $idUsuario = $objUsuarioPetRN->getObjUsuarioPeticionamento(true);

        if (is_null($objUnidadeDTO)) {
            $detalhes = "Certidão de Intimação Cumprida referente ao Documento Principal " . $arrParams[6] . " no âmbito do Processo " . $objProcedimentoDTO->getStrProtocoloProcedimentoFormatado() . ", tendo em vista que todas as Unidades de tramitação estão desativadas.";
            throw new InfraException('Unidades de tramitação estão desativadas', null, $detalhes);
        }

        SessaoSEI::getInstance()->setNumIdUnidadeAtual($objUnidadeDTO->getNumIdUnidade());
        SessaoSEI::getInstance()->setNumIdUsuario($idUsuario);

        if (is_null($objMdPetIntRelDestinatarioDTO)) {
            //simular sessao de usuario interno do SEI
            $objContato = $objMdPetIntAceiteRN->retornaObjContatoIdUsuario(array(SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno()));
        } else {
            $objContato = $this->_retornarObjContatoPorId($objMdPetIntRelDestinatarioDTO->getNumIdContato());
        }

        $htmlCertidao = $this->gerarHTMLConteudoCertidao(array($idIntimacao, $objMdPetIntRelDestinatarioDTO, $objContato, $job, $dataCumprimento));


        $idSerieCertidao = $objInfraParametro->getValor(static::$STR_ID_SERIE_CERTIDAO);

        //=============================================
        //MONTAGEM DO DOCUMENTO VIA SEI RN
        //=============================================

        $objDocumentoAPI = new DocumentoAPI();
        $objDocumentoAPI->setIdProcedimento($objProcedimentoDTO->getDblIdProcedimento());
        $objDocumentoAPI->setSubTipo(DocumentoRN::$TD_FORMULARIO_AUTOMATICO);
        $objDocumentoAPI->setTipo(ProtocoloRN::$TP_DOCUMENTO_GERADO);
        $objDocumentoAPI->setIdSerie($idSerieCertidao);
        $objDocumentoAPI->setSinAssinado('N');
        $objDocumentoAPI->setSinBloqueado('S');
        $objDocumentoAPI->setIdHipoteseLegal(null);
        $objDocumentoAPI->setNivelAcesso(ProtocoloRN::$NA_PUBLICO);
        $objDocumentoAPI->setIdTipoConferencia(null);
        $objDocumentoAPI->setConteudo(base64_encode(utf8_encode($htmlCertidao)));
        $objDocumentoAPI->setIdUnidadeGeradora($objUnidadeDTO->getNumIdUnidade());

        $objSaidaDocumentoAPI = $objSeiRN->incluirDocumento($objDocumentoAPI);

        //necessario forçar update da coluna sta_documento da tabela documento
        //Add conteúdo atualizado com o nome do documento formatado gerado.
        //inclusao via SeiRN nao permitiu definir como documento de formulario automatico
        $objDocumentoDTO = new DocumentoDTO();

        $objDocumentoDTO->retTodos();
        $objDocumentoDTO->setDblIdDocumento($objSaidaDocumentoAPI->getIdDocumento());
        $objDocumentoDTO = $objDocumentoRN->consultarRN0005($objDocumentoDTO);

        $objDocumentoDTO->setStrStaDocumento(DocumentoRN::$TD_FORMULARIO_AUTOMATICO);

        $objDocumentoBD = new DocumentoBD($this->getObjInfraIBanco());
        $objDocumentoBD->alterar($objDocumentoDTO);

        $objMdPetIntAceiteDTO->setDblIdDocumentoCertidao($objDocumentoDTO->getDblIdDocumento());
        $objMdPetIntAceiteRN->alterar($objMdPetIntAceiteDTO);

        $objMdPetIntDocAcessoExt->verificarConcessaoAcessoExterno($job, $objDocumentoDTO, $objMdPetIntAceiteDTO);

    }

    protected function concederAcessoExternoCertidaoGeradaControlado($arr)
    {
        $objContatoPet = new MdPetContatoRN();
        $objUsuarioPetRN = new MdPetIntUsuarioRN();
        $objMdPetIntAceiteRN = new MdPetIntAceiteRN();
        $objMdPetIntDocExt = new MdPetIntAcessoExternoDocumentoRN();
        $objDocumentoDTO = $arr[0];
        $objUnidadeDTO = $arr[1];
        $objProcedimentoDTO = $arr[2];
        $objContato = $arr[3];
        $job = $arr[4];

        if ($job) {
            $idUsuario = $objUsuarioPetRN->getObjUsuarioPeticionamento(true);
        } else {
            $idUsuario = $objMdPetIntAceiteRN->retornaIdUsuarioIdContato(array($objContato->getNumIdContato()));
        }

        $staConcessao = $job ? MdPetIntAcessoExternoDocumentoRN::$STA_AGENDAMENTO : MdPetIntAcessoExternoDocumentoRN::$STA_EXTERNO;

        $arrParamsPart = array($objProcedimentoDTO->getDblIdProcedimento(), $objUnidadeDTO->getNumIdUnidade(), $objContato->getNumIdContato());

        $objParticipanteDTO = $objMdPetIntDocExt->adicionarParticipanteProcessoAcessoExterno($arrParamsPart);

        $objMdPetIntAcessoExtDocDTO = new MdPetIntAcessoExternoDocumentoDTO();
        $objMdPetIntAcessoExtDocDTO->setNumIdUsuarioExterno($idUsuario);
        $objMdPetIntAcessoExtDocDTO->setNumIdUnidade($objUnidadeDTO->getNumIdUnidade());
        $objMdPetIntAcessoExtDocDTO->setNumIdParticipante($objParticipanteDTO->getNumIdParticipante());
        $objMdPetIntAcessoExtDocDTO->setDblIdProtocoloProcesso($objProcedimentoDTO->getDblIdProcedimento());
        $objMdPetIntAcessoExtDocDTO->setArrIdDocumentos(array($objDocumentoDTO->getDblIdDocumento()));
        $objMdPetIntAcessoExtDocDTO->setStrNomeUsuarioExterno($objContato->getStrNome());
        $objMdPetIntAcessoExtDocDTO->setStrEmailUsuarioExterno($objContato->getStrEmail());
        $objMdPetIntAcessoExtDocDTO->setStrSinVisualizacaoIntegral('N');
        $objMdPetIntAcessoExtDocDTO->setStrStaConcessao($staConcessao);
        $objMdPetIntAcessoExtDocDTO->setStrMotivo('Em razão do aceite da Intimação Eletrônica.');

        $objMdPetIntAcessoExtDocRN = new MdPetIntAcessoExternoDocumentoRN();
        $objMdPetIntAcessoExtDocRN->concederAcessoExternoParaDocumentos($objMdPetIntAcessoExtDocDTO);
    }

    private function _retornarObjContatoPorId($idContato)
    {

        $objContatoDTO = new ContatoDTO();
        $objContatoRN = new ContatoRN();
        $objContatoDTO->setBolExclusaoLogica(false);
        $objContatoDTO->setNumIdContato($idContato);
        $objContatoDTO->retNumIdContato();
        $objContatoDTO->retStrNome();
        $objContatoDTO->retStrEmail();
        $objContatoDTO->retStrSinAtivo();
        $lista = $objContatoRN->listarRN0325($objContatoDTO);

        if (count($lista) > 0) {
            if (current($lista)->getStrSinAtivo() == 'N') {
                throw new InfraException('O contato - ' . $idContato . ' não está ativo.');
            }
            return current($lista);
        }

        return null;
    }

    private function gerarHTMLConteudoCertidao($arrParams)
    {

        $idIntimacao = $arrParams[0];
        $objMdPetIntDestDTO = isset($arrParams[1]) ? $arrParams[1] : null;
        $objContato = $arrParams[2];
        $job = $arrParams[3];
        $dataCumprimento = isset($arrParams[4]) ? $arrParams[4] : null;

        $objMdPetIntRN = new MdPetIntimacaoRN();
        $objMdPetIntAceiteRN = new MdPetIntAceiteRN();
        $objMdPetIntDestRN = new MdPetIntRelDestinatarioRN();
        $dadosDocPrinc = $objMdPetIntRN->retornaDadosDocPrincipalIntimacao(array($idIntimacao));

        $objIntimacao = $objMdPetIntDestRN->consultarDadosIntimacaoPorDestinario($idIntimacao, true, false, $objContato);

        $situacaoInt = $objIntimacao->getStrStaSituacaoIntimacao();
        $situacaoCertidao = $situacaoInt == MdPetIntimacaoRN::$INTIMACAO_CUMPRIDA_POR_ACESSO ? MdPetIntCertidaoRN::$STR_TP_CUMPRIMENTO_CERTIDAO_ACESSO_DIRETO : MdPetIntCertidaoRN::$STR_TP_CUMPRIMENTO_CERTIDAO_PRAZO_TACITO;

        //Get Data
        if (is_null($objMdPetIntDestDTO)) {
            $objMdPetIntDestDTO = $objMdPetIntDestRN->retornaRelIntDestinatario(array($idIntimacao, false, $objContato));
        }

        $html = '';
        $html .= '<table align="center" style="width: 98%" border="0">';
        $html .= '<tbody>';
        $html .= '<tr>';
        $html .= '<td style="font-weight: bold; width: 300px">Tipo de Destinatário:</td>';
        $html .= '<td>Pessoa '.($objMdPetIntDestDTO->getStrSinPessoaJuridica() == 'S' ? 'Jurídica' : 'Física').'</td>';
        $html .= '</tr>';

        $html .= '<tr>';
        $html .= '<td style="font-weight: bold; width: 300px">Destinatário:</td>';
        $html .= '<td>' . $objMdPetIntDestDTO->getStrNomeContato() . '</td>';
        $html .= '</tr>';

        $html .= '<tr>';
        $html .= '<td style="font-weight: bold;">Tipo de Intimação:</td>';
        $html .= '<td>' . $objIntimacao->getStrNomeTipoIntimacao() . '</td>';
        $html .= '</tr>';

        $html .= '<tr>';
        $html .= '<td style="font-weight: bold;">Documento Principal da Intimação:</td>';

        //se tiver numero montar o numero no texto
        if (isset($dadosDocPrinc[4]) && $dadosDocPrinc[4] != "") {
            $strTexto = '<td>' . $dadosDocPrinc[1] . ' ' . $dadosDocPrinc[4] . ' (' . $dadosDocPrinc[0] . ')</td>';
        } else {
            $strTexto = '<td>' . $dadosDocPrinc[1] . ' (' . $dadosDocPrinc[0] . ')</td>';
        }

        $html .= count($dadosDocPrinc) > 0 ? $strTexto : '';
        $html .= '</tr>';

        // Caso haja, lista os documentos anexos na Certidão
        $arr_protocolos_anexos = (new MdPetIntimacaoRN())->retornaArrDocumentosAnexosIntimacao($idIntimacao);

        if(!empty($arr_protocolos_anexos) && count($arr_protocolos_anexos) > 0){

            $html .= '<tr>';
            $html .= '<td style="padding-left: 15px">- Anexos:</td>';
            $html .= '<td>' . implode(', ', $arr_protocolos_anexos) . '<td>';
            $html .= '</tr>';

        }

        $arrDtIntimacao = isset($objIntimacao) ? explode(' ', $objIntimacao->getDthDataCadastro()) : '';
        $dtIntimacao = count($arrDtIntimacao) > 0 ? current($arrDtIntimacao) : null;

        $html .= '<tr>';
        $html .= '<td style="font-weight: bold;">Data de Expedição da Intimação:</td>';
        $html .= '<td>' . $objIntimacao->getDthDataCadastro() . '</td>';
        $html .= '</tr>';

        $html .= '<tr>';
        $html .= '<td style="font-weight: bold;">Tipo de Cumprimento da Intimação:</td>';
        $html .= '<td>' . $situacaoCertidao . '</td>';
        $html .= '</tr>';

        $dataAtual = InfraData::getStrDataHoraAtual();
        $arrDataAtual = explode(" ", $dataAtual);
        $dataAtual = count($arrDataAtual) > 0 ? current($arrDataAtual) : null;

        $html .= '<tr>';
        $html .= '<td style="font-weight: bold;">Data do Cumprimento:</td>';
        $html .= '<td>' . $dataCumprimento . '</td>';
        $html .= '</tr>';

        if ($situacaoInt == MdPetIntimacaoRN::$INTIMACAO_CUMPRIDA_POR_ACESSO && $dataAtual != null && $dataAtual != $dataCumprimento) {
            $html .= '<tr>';
            $html .= '<td> &ensp;&nbsp; Data da Consulta em dia não útil:</td>';
            $html .= '<td>' . $dataAtual . '</td>';
            $html .= '</tr>';
        }
        if ($situacaoInt == MdPetIntimacaoRN::$INTIMACAO_CUMPRIDA_POR_ACESSO) {
            //Recuperando o usuário que cumpriu a intimação;

            $objMdPetIntAceiteDTO = new MdPetIntAceiteDTO();
            $objMdPetIntAceiteDTO->retNumIdUsuario();
            $objMdPetIntAceiteDTO->setNumIdMdPetIntRelDestinatario($objMdPetIntDestDTO->getNumIdMdPetIntRelDestinatario());
            $objMdPetIntAceiteRN = new MdPetIntAceiteRN();
            $arrMdPetIntAceiteRN = $objMdPetIntAceiteRN->consultar($objMdPetIntAceiteDTO);

            $objUsuarioRN = new UsuarioRN();
            $objUsuarioDTO = new UsuarioDTO();
            $objUsuarioDTO->retNumIdContato();
            $objUsuarioDTO->setNumIdUsuario($arrMdPetIntAceiteRN->getNumIdUsuario());
            $arrObjUsuarioDTO = $objUsuarioRN->consultarRN0489($objUsuarioDTO);

            $objContatoDTO = new ContatoDTO();
            $objContatoDTO->retStrNome();
            $objContatoDTO->setNumIdContato($arrObjUsuarioDTO->getNumIdContato());

            $objContatoRN = new ContatoRN();
            $objContatoDTO = $objContatoRN->consultarRN0324($objContatoDTO);

            $html .= '<tr>';
            $html .= '<td style="font-weight: bold;">Usuário Responsável pelo Cumprimento:</td>';
            $html .= '<td>' . $objContatoDTO->getStrNome() . '</td>';
            $html .= '</tr>';
        }

        $html .= '</tbody></table>';

        $html .= '<p>Esta Certidão formaliza o cumprimento da intimação eletrônica referente aos dados acima, observado o seguinte:</p>
        <ul>
        <li>O Tipo de Cumprimento "Consulta Direta" indica que o "Destinatário" realizou a consulta aos documentos da intimação diretamente no sistema antes do término do Prazo Tácito para intimação.
            <ul>
                <li>O Prazo Tácito para intimação é definido conforme normativo aplicável ao órgão, em que, a partir da "Data de Expedição da Intimação", o Destinatário possui o referido prazo para consultar os documentos diretamente no sistema, sob pena de ser considerado automaticamente intimado na data de término desse prazo.</li>
            </ul>
        </li>
        <li>O Tipo de Cumprimento "Por Decurso do Prazo Tácito" indica que não ocorreu a mencionada consulta aos documentos da intimação diretamente no sistema, situação na qual a Certidão é gerada automaticamente na data de término desse prazo.
            <ul>
                <li>No caso do Prazo Tácito terminar em dia não útil, a geração automática da Certidão ocorrerá somente no primeiro dia útil seguinte.</li>
            </ul>
        </li>
        <li>Conforme regras de contagem de prazo processual e normas afetas a processo eletrônico, tanto no Prazo Tácito para intimação como nos possíveis prazos externos para Peticionamento de Resposta:
            <ul>
                <li>sempre é excluído da contagem o dia do começo e incluído o do vencimento;</li>
                <li>o dia do começo e o do vencimento nunca ocorrem em dia não útil, prorrogando-o para o primeiro dia útil seguinte;</li>
                <li>a consulta a intimação ocorrida em dia não útil tem a correspondente data apresentada em linha separada, sendo a "Data do Cumprimento" a do primeiro dia útil seguinte.</li>
            </ul>
        </li>
        <li>Para todos os efeitos legais, somente após a geração da presente Certidão e com base exclusivamente na "Data do Cumprimento" é que o Destinatário, ou a Pessoa Jurídica ou Física por ele representada, é considerado efetivamente intimado e são iniciados os possíveis prazos externos para Peticionamento de Resposta.
            <ul>
                <li>Caso a intimação se dirija a Pessoa Jurídica, ela será considerada efetivamente intimada na "Data do Cumprimento" correspondente à primeira Certidão gerada referente a Usuário Externo que possua poderes de representação.</li>
            </ul>
        </li>
        </ul>';

        return $html;
    }

    private function _getIdContatoPorAcessoExterno($idAcessoExterno)
    {
        $objRN = new AcessoExternoRN();
        $objDTO = new AcessoExternoDTO();
        $objDTO->setNumIdAcessoExterno($idAcessoExterno);
        $objDTO->retNumIdContatoParticipante();
        $objDTO->setNumMaxRegistrosRetorno(1);

        $objDTO = $objRN->consultar($objDTO);

        if (!is_null($objDTO)) {
            return $objDTO->getNumIdContatoParticipante();
        }

        return null;
    }

    public function addIconeAcessoCertidao($arr)
    {
        $docPrinc = $arr[0];
        $idIntimacao = $arr[1];
        $idAcessoExt = $arr[2];
        $idCertidao = $arr[3];
        $cnpjs = $arr[4];
        $dataAceite = $arr[5];
	    $objMdPetIntProtRN = new MdPetIntProtocoloRN();

        if (!$idCertidao) {
            $idCertidao = $this->retornaIdDocCertidaoPorIntimacaoConectado((array)$idIntimacao);
        }

        $strLink = $this->retornaLinkAcessoDocumento($idCertidao, $idAcessoExt);

        //Var que verifica se a certidão é anexo de uma Intimação que não está aceita
        $isValido = $this->verificaDocumentoEAnexoIntimacaoNaoCumprida(array($idCertidao, $idAcessoExt));

        $alertMsg = 'Documento bloqueado, pois está vinculado a uma Intimação ainda não Cumprida.';
        $js = $isValido ? 'window.open(\'' . $strLink . '\');' : 'alert(\'' . $alertMsg . '\')';

        $imgCertidao = '<img src="modulos/peticionamento/imagens/svg/intimacao_certidao.svg?'.Icone::VERSAO.'" style="height: 24px">';

        //obter informacoes do doc principal da intimação
        $objMdPetIntDocumentoRN = new MdPetIntProtocoloRN();
        $objMdPetIntDocumentoDTO = new MdPetIntProtocoloDTO();
        $objMdPetIntDocumentoDTO->retTodos();
        $objMdPetIntDocumentoDTO->retStrNumeroDocumento();
        $objMdPetIntDocumentoDTO->retNumIdSerie();
        $objMdPetIntDocumentoDTO->retStrNomeSerie();
        $objMdPetIntDocumentoDTO->retStrProtocoloFormatadoDocumento();
        $objMdPetIntDocumentoDTO->setNumIdMdPetIntimacao($idIntimacao);
        $objMdPetIntDocumentoDTO->setStrSinPrincipal('S');
        $objMdPetIntDocumentoDTO->setNumMaxRegistrosRetorno(1);
        $objMdPetIntDocumentoDTO = $objMdPetIntDocumentoRN->consultar($objMdPetIntDocumentoDTO);

	    $ToolTipText = 'Cumprida em: ';
	    $ToolTipText .= explode(' ', $dataAceite)[0] . ' ';
	    $ToolTipText .= '<br/>Documento Principal: ';
	    $ToolTipText .= $objMdPetIntDocumentoDTO->getStrNomeSerie() . ' ';
        if ($objMdPetIntDocumentoDTO->getStrNumeroDocumento()) {
            $ToolTipText .= $objMdPetIntDocumentoDTO->getStrNumeroDocumento() . ' ';
        }
        $ToolTipText .= '(SEI nº ' . $objMdPetIntDocumentoDTO->getStrProtocoloFormatadoDocumento() . ')';

        if ($cnpjs) {
            $ToolTipText .= '<br/><br/>Destinatários:';
            foreach ($cnpjs as $emp) {
                $ToolTipText .= '<br/>' . $emp ;
            }
        }

        $ToolTipTitulo = 'Certidão de Intimação Cumprida';

        $ToolTipText .= '<br/><br/>Clique para visualizar a Certidão.';

        $conteudoHtml = '<a onclick="' . $js . '"';
        $conteudoHtml .= 'onmouseover ="return infraTooltipMostrar(\'' . $ToolTipText . '\',\'' . $ToolTipTitulo . '\')"';
        $conteudoHtml .= 'onmouseout="return infraTooltipOcultar()">';
        $conteudoHtml .= $imgCertidao;
        $conteudoHtml .= '</a>';

        return $conteudoHtml;
    }

//    public function addIconeAcessoCertidaoAcao($arr){
//        $docPrinc    = $arr[0];
//        $idIntimacao = $arr[1];
//        $idAcessoExt = $arr[2];
//        $idCertidao = $arr[3];
//
//        if(!$idCertidao){
//            $arrEnvio = array($idIntimacao);
//            $idCertidao = $this->retornaIdDocCertidaoPorIntimacaoConectado($arrEnvio);
//        }
//        $strLink = $this->retornaLinkAcessoDocumento($idCertidao, $idAcessoExt);
//
//        //Var que verifica se a certidão é anexo de uma Intimação que não está aceita
//        $isValido = $this->verificaDocumentoEAnexoIntimacaoNaoCumprida(array($idCertidao, $idAcessoExt));
//
//        $alertMsg = 'Documento bloqueado, pois está vinculado a uma Intimação ainda não Cumprida.';
//        $js       = $isValido ? 'window.open(\''.$strLink.'\');' : 'alert(\''.$alertMsg.'\')';
//
//        $imgCertidao = '<img src="modulos/peticionamento/imagens/intimacao_certidao.png">';
//
//        //obter informacoes do doc principal da intimação
//        $objMdPetIntDocumentoRN = new MdPetIntProtocoloRN();
//        $objMdPetIntDocumentoDTO = new MdPetIntProtocoloDTO();
//        $objMdPetIntDocumentoDTO->retTodos();
//        $objMdPetIntDocumentoDTO->retStrNumeroDocumento();
//        $objMdPetIntDocumentoDTO->retNumIdSerie();
//        $objMdPetIntDocumentoDTO->retStrNomeSerie();
//        $objMdPetIntDocumentoDTO->retStrProtocoloFormatadoDocumento();
//        $objMdPetIntDocumentoDTO->setNumIdMdPetIntimacao( $idIntimacao );
//        $objMdPetIntDocumentoDTO->setStrSinPrincipal('S');
//        $objMdPetIntDocumentoDTO->setNumMaxRegistrosRetorno(1);
//        $objMdPetIntDocumentoDTO = $objMdPetIntDocumentoRN->consultar( $objMdPetIntDocumentoDTO );
//
//        $ToolTipTitle = 'Certidão de Intimação Cumprida';
//        $ToolTipTitle .= '<br/>Documento Principal: ';
//        $ToolTipTitle .= $objMdPetIntDocumentoDTO->getStrNomeSerie() . ' ';
//        if ($objMdPetIntDocumentoDTO->getStrNumeroDocumento()){
//            $ToolTipTitle .= $objMdPetIntDocumentoDTO->getStrNumeroDocumento(). ' ' ;
//        }
//        $ToolTipTitle .= '(SEI nº ' . $objMdPetIntDocumentoDTO->getStrProtocoloFormatadoDocumento() . ')';
//
//        $ToolTipText  = '';
//        $ToolTipText .= 'Clique para visualizar a Certidão.';
//
//        $conteudoHtml  = '<a onclick="'.$js.'"';
//        $conteudoHtml .= 'onmouseover ="return infraTooltipMostrar(\''.$ToolTipText.'\',\''.$ToolTipTitle.'\')"';
//        $conteudoHtml .= 'onmouseout="return infraTooltipOcultar()">';
//        $conteudoHtml .= $imgCertidao;
//        $conteudoHtml .= '</a>';
//
//        return $conteudoHtml;
//    }

    public function retornaLinkAcessoDocumento($idProtocolo, $idAcessoEx, $isProcedimento = false)
    {
        if ($isProcedimento) {
            return SessaoSEIExterna::getInstance()->assinarLink('processo_acesso_externo_consulta.php?id_acesso_externo=' . $idAcessoEx . '&id_procedimento_anexado=' . $idProtocolo);
        } else {
            return SessaoSEIExterna::getInstance()->assinarLink('documento_consulta_externa.php?id_acesso_externo=' . $idAcessoEx . '&id_documento=' . $idProtocolo);
        }
    }

    protected function retornaIdDocCertidaoPorIntimacaoConectado($arr)
    {

        $idIntimacao = current($arr);
        $IdUsuario = SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno();

        $objMdPetIntDestRN = new MdPetIntRelDestinatarioRN();
        $objMdPetIntAceiteRN = new MdPetIntAceiteRN();

        $objMdPetIntDestDTO = $objMdPetIntDestRN->retornaRelIntDestinatario(array($idIntimacao, $IdUsuario));

        if ($objMdPetIntDestDTO) {
            $idDest = $objMdPetIntDestDTO->getNumIdMdPetIntRelDestinatario();

            $objMdPetIntAceiteDTO = new MdPetIntAceiteDTO();
            $objMdPetIntAceiteDTO->setNumIdMdPetIntRelDestinatario($idDest);
            $objMdPetIntAceiteDTO->retDblIdDocumentoCertidao();
            $lista = $objMdPetIntAceiteRN->listar($objMdPetIntAceiteDTO);
            $objMdPetIntAceiteDTO = current($lista);

            if (!empty($lista)) {
                return $objMdPetIntAceiteDTO->getDblIdDocumentoCertidao();
            } else {
                return null;
            }
        }

        return null;
    }

    protected function verificaDocumentoEAnexoIntimacaoNaoCumpridaConectado($arrParams)
    {
        $idProtocolo = $arrParams[0];
        $idAcessoExterno = array_key_exists(1, $arrParams) ? $arrParams[1] : false;
        $idUsuario = array_key_exists(2, $arrParams) ? $arrParams[2] : false;
        $idContato = array_key_exists(3, $arrParams) ? $arrParams[3] : false;
        $isValido = true;

        if ($idAcessoExterno) {
            $idContato = $this->_getIdContatoPorAcessoExterno($idAcessoExterno);
        }

        if ($idUsuario) {
            $objMdPetUsuarioRN = new MdPetIntUsuarioRN();
            $objContatoDTO = $objMdPetUsuarioRN->retornaObjContatoPorIdUsuario(array($idUsuario));
            $idContato = !is_null($objContatoDTO) ? $objContatoDTO->getNumIdContato() : false;
        }

        $objMdPetRelDestRN = new MdPetIntRelDestinatarioRN();
        $objMdPetRelDestDTO = new MdPetIntRelDestinatarioDTO();
        $objMdPetRelDestDTO->setDblIdProtocolo($idProtocolo);
        if (is_numeric($idContato)) {
            $objMdPetRelDestDTO->setNumIdContato($idContato);
        }
        $objMdPetRelDestDTO->retNumIdMdPetIntRelDestinatario();
        $objMdPetRelDestDTO->retDblIdProtocolo();
        $objMdPetRelDestDTO->retDblIdDocumento();

        $count = $objMdPetRelDestRN->contar($objMdPetRelDestDTO);

        if ($count > 0) {
            //Get Qtd Intimacoe
            $arrObjDTO = $objMdPetRelDestRN->listar($objMdPetRelDestDTO);
            $idsIntimacoes = array_unique(InfraArray::converterArrInfraDTO($arrObjDTO, 'IdMdPetIntRelDestinatario'));
            $qtdIntimacoes = count($idsIntimacoes);

            //Get Qtd Aceites
            $objMdPetIntAcRN = new MdPetIntAceiteRN();
            $objMdPetIntAcDTO = new MdPetIntAceiteDTO();
            $objMdPetIntAcDTO->setNumIdMdPetIntRelDestinatario($idsIntimacoes, InfraDTO::$OPER_IN);
            $objMdPetIntAcDTO->retNumIdMdPetIntRelDestinatario();

            $qtdAceites = $objMdPetIntAcRN->contar($objMdPetIntAcDTO);

            //condição
            $isValido = $qtdIntimacoes == $qtdAceites;
        }

        return $isValido;
    }

}

?>