<?php

class MdPetIntCertidaoRN extends InfraRN
{

    public static $STR_TP_CUMPRIMENTO_CERTIDAO_ACESSO_DIRETO = 'Consulta Direta';
    public static $STR_TP_CUMPRIMENTO_CERTIDAO_PRAZO_TACITO = 'Por Decurso do Prazo T�cito';
    public static $STR_ID_SERIE_CERTIDAO = 'MODULO_PETICIONAMENTO_ID_SERIE_CERTIDAO_INTIMACAO_CUMPRIDA';

    public function __construct()
    {
        parent::__construct();
    }

    protected function inicializarObjInfraIBanco()
    {
        return BancoSEI::getInstance();
    }

    //funcao nao fazendo men�ao a elementos de sessao do SEI Interno (classe SessaoSEI, PaginaSEI) apenas do externo (SessaoSEIExterna, PaginaSEIExterna)
    public function gerarCertidaoExternaControlado($arrParams)
    {


        //gerando documento certid�o (nao assinada) dentro do processo do SEI
        try {

            $objInfraParametro = new InfraParametro($this->getObjInfraIBanco());
            $objSeiRN = new SeiRN();
            $objDocumentoRN = new DocumentoRN();
            $objMdPetIntAceiteRN = new MdPetIntAceiteRN();
            $objUsuarioPetRN = new MdPetIntUsuarioRN();
            $objMdPetIntDocAcessoExt = new MdPetIntAcessoExternoDocumentoRN();
            //Get parametros do Array
            $idIntimacao = $arrParams[0]; //Id Intima��o
            $objUnidadeDTO = $arrParams[1]; //UnidadeDTO da unidade geradora do processo
            $objProcedimentoDTO = $arrParams[2]; //ProcedimentoDTO para vincular o recibo ao processo correto
            $objMdPetIntAceiteDTO = $arrParams[3];
            $objMdPetIntRelDestinatarioDTO = isset($arrParams[4]) ? $arrParams[4] : null;
            $job = isset($arrParams[5]) ? $arrParams[5] : false;
            $dataCumprimento = isset($arrParams[6]) ? $arrParams[6] : null;

            $idUsuario = $objUsuarioPetRN->getObjUsuarioPeticionamento(true);

            if (is_null($objUnidadeDTO)) {
                $detalhes = "Certid�o de Intima��o Cumprida referente ao Documento Principal " . $arrParams[6] . " no �mbito do Processo " . $objProcedimentoDTO->getStrProtocoloProcedimentoFormatado() . ", tendo em vista que todas as Unidades de tramita��o est�o desativadas.";
                throw new InfraException('Unidades de tramita��o est�o desativadas', null, $detalhes);
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
                $detalhes = "O contato " . $objMdPetIntRelDestinatarioDTO->getNumIdContato() . " est� inativo.";
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

            //necessario for�ar update da coluna sta_documento da tabela documento
            //Add conte�do atualizado com o nome do documento formatado gerado.
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

            //Adicionando usu�rio no documento
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
        //gerando documento certid�o (nao assinada) dentro do processo do SEI

        $objInfraParametro = new InfraParametro($this->getObjInfraIBanco());
        $objSeiRN = new SeiRN();
        $objDocumentoRN = new DocumentoRN();
        $objMdPetIntAceiteRN = new MdPetIntAceiteRN();
        $objUsuarioPetRN = new MdPetIntUsuarioRN();
        $objMdPetIntDocAcessoExt = new MdPetIntAcessoExternoDocumentoRN();

        //Get parametros do Array
        $idIntimacao = $arrParams[0]; //Id Intima��o
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
            $detalhes = "Certid�o de Intima��o Cumprida referente ao Documento Principal " . $arrParams[6] . " no �mbito do Processo " . $objProcedimentoDTO->getStrProtocoloProcedimentoFormatado() . ", tendo em vista que todas as Unidades de tramita��o est�o desativadas.";
            throw new InfraException('Unidades de tramita��o est�o desativadas', null, $detalhes);
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

        //necessario for�ar update da coluna sta_documento da tabela documento
        //Add conte�do atualizado com o nome do documento formatado gerado.
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
        $objMdPetIntAcessoExtDocDTO->setStrMotivo('Em raz�o do aceite da Intima��o Eletr�nica.');

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
                throw new InfraException('O contato - ' . $idContato . ' n�o est� ativo.');
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
//        if($job){
//            $objIntimacao      = $objMdPetIntDestRN->consultarDadosIntimacaoPorDestinario($idIntimacao, true, false, $objContato);
//        }else{
////            $objIntimacao      = $objMdPetIntDestRN->consultarDadosIntimacao($idIntimacao, true, false, $objContato);
//        $objIntimacao      = $objMdPetIntDestRN->consultarDadosIntimacaoPorDestinario($idIntimacao, true, false, $objContato);
//        }
//        var_dump('akiii');die;

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
        $html .= '<td style="font-weight: bold; width: 300px">Tipo de Destinat�rio:</td>';
        if ($objMdPetIntDestDTO->getStrSinPessoaJuridica() == 'S') {
            $html .= '<td>Pessoa Jur�dica</td>';
        } elseif ($objMdPetIntDestDTO->getStrSinPessoaJuridica() == 'N') {
            $html .= '<td>Pessoa F�sica</td>';
        }
        $html .= '</tr>';

        $html .= '<tr>';
        $html .= '<td style="font-weight: bold; width: 300px">Destinat�rio:</td>';
        $html .= '<td>' . $objMdPetIntDestDTO->getStrNomeContato() . '</td>';
        $html .= '</tr>';

        $html .= '<tr>';
        $html .= '<td style="font-weight: bold;">Tipo de Intima��o:</td>';
        $html .= '<td>' . $objIntimacao->getStrNomeTipoIntimacao() . '</td>';
        $html .= '</tr>';

        $html .= '<tr>';
        $html .= '<td style="font-weight: bold;">Documento Principal da Intima��o:</td>';

        //se tiver numero montar o numero no texto
        if (isset($dadosDocPrinc[4]) && $dadosDocPrinc[4] != "") {
            $strTexto = '<td>' . $dadosDocPrinc[1] . ' ' . $dadosDocPrinc[4] . ' (' . $dadosDocPrinc[0] . ')</td>';
        } else {
            $strTexto = '<td>' . $dadosDocPrinc[1] . ' (' . $dadosDocPrinc[0] . ')</td>';
        }

        $html .= count($dadosDocPrinc) > 0 ? $strTexto : '';
        $html .= '</tr>';

        $arrDtIntimacao = isset($objIntimacao) ? explode(' ', $objIntimacao->getDthDataCadastro()) : '';
        $dtIntimacao = count($arrDtIntimacao) > 0 ? current($arrDtIntimacao) : null;

        $html .= '<tr>';
        $html .= '<td style="font-weight: bold;">Data de Expedi��o da Intima��o:</td>';
        $html .= '<td>' . $objIntimacao->getDthDataCadastro() . '</td>';
        $html .= '</tr>';

        $html .= '<tr>';
        $html .= '<td style="font-weight: bold;">Tipo de Cumprimento da Intima��o:</td>';
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
            $html .= '<td> &ensp;&nbsp; Data da Consulta em dia n�o �til:</td>';
            $html .= '<td>' . $dataAtual . '</td>';
            $html .= '</tr>';
        }
        if ($situacaoInt == MdPetIntimacaoRN::$INTIMACAO_CUMPRIDA_POR_ACESSO) {
            //Recuperando o usu�rio que cumpriu a intima��o;

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
            $html .= '<td style="font-weight: bold;">Usu�rio Respons�vel pelo Cumprimento:</td>';
            $html .= '<td>' . $objContatoDTO->getStrNome() . '</td>';
            $html .= '</tr>';
        }

        $html .= '</tbody></table>';

        $html .= '<p>Esta Certid�o formaliza o cumprimento da intima��o eletr�nica referente aos dados acima, observado o seguinte:</p>
        <ul>
        <li>O Tipo de Cumprimento "Consulta Direta" indica que o "Destinat�rio" realizou a consulta aos documentos da intima��o diretamente no sistema antes do t�rmino do Prazo T�cito para intima��o.
            <ul>
                <li>O Prazo T�cito para intima��o � definido conforme normativo aplic�vel ao �rg�o, em que, a partir da "Data de Expedi��o da Intima��o", o Destinat�rio possui o referido prazo para consultar os documentos diretamente no sistema, sob pena de ser considerado automaticamente intimado na data de t�rmino desse prazo.</li>
            </ul>
        </li>
        <li>O Tipo de Cumprimento "Por Decurso do Prazo T�cito" indica que n�o ocorreu a mencionada consulta aos documentos da intima��o diretamente no sistema, situa��o na qual a Certid�o � gerada automaticamente na data de t�rmino desse prazo.
            <ul>
                <li>No caso do Prazo T�cito terminar em dia n�o �til, a gera��o autom�tica da Certid�o ocorrer� somente no primeiro dia �til seguinte.</li>
            </ul>
        </li>
        <li>Conforme regras de contagem de prazo processual e normas afetas a processo eletr�nico, tanto no Prazo T�cito para intima��o como nos poss�veis prazos externos para Peticionamento de Resposta:
            <ul>
                <li>sempre � exclu�do da contagem o dia do come�o e inclu�do o do vencimento;</li>
                <li>o dia do come�o e o do vencimento nunca ocorrem em dia n�o �til, prorrogando-o para o primeiro dia �til seguinte;</li>
                <li>a consulta a intima��o ocorrida em dia n�o �til tem a correspondente data apresentada em linha separada, sendo a "Data do Cumprimento" a do primeiro dia �til seguinte.</li>
            </ul>
        </li>
        <li>Para todos os efeitos legais, somente ap�s a gera��o da presente Certid�o e com base exclusivamente na "Data do Cumprimento" � que o Destinat�rio, ou a Pessoa Jur�dica ou F�sica por ele representada, � considerado efetivamente intimado e s�o iniciados os poss�veis prazos externos para Peticionamento de Resposta.
            <ul>
                <li>Caso a intima��o se dirija a Pessoa Jur�dica, ela ser� considerada efetivamente intimada na "Data do Cumprimento" correspondente � primeira Certid�o gerada referente a Usu�rio Externo que possua poderes de representa��o.</li>
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
        $objMdPetIntProtRN = new MdPetIntProtocoloRN();

        if (!$idCertidao) {
            $arrEnvio = array($idIntimacao);
            $idCertidao = $this->retornaIdDocCertidaoPorIntimacaoConectado($arrEnvio);
        }

        $strLink = $this->retornaLinkAcessoDocumento($idCertidao, $idAcessoExt);

        //Var que verifica se a certid�o � anexo de uma Intima��o que n�o est� aceita
        $isValido = $this->verificaDocumentoEAnexoIntimacaoNaoCumprida(array($idCertidao, $idAcessoExt));

        $alertMsg = 'Documento bloqueado, pois est� vinculado a uma Intima��o ainda n�o Cumprida.';
        $js = $isValido ? 'window.open(\'' . $strLink . '\');' : 'alert(\'' . $alertMsg . '\')';

        $imgCertidao = '<img src="modulos/peticionamento/imagens/intimacao_certidao.png">';

        //obter informacoes do doc principal da intima��o
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

        $ToolTipTitle = 'Certid�o de Intima��o Cumprida';
        $ToolTipTitle .= '<br/>Documento Principal: ';
        $ToolTipTitle .= $objMdPetIntDocumentoDTO->getStrNomeSerie() . ' ';
        if ($objMdPetIntDocumentoDTO->getStrNumeroDocumento()) {
            $ToolTipTitle .= $objMdPetIntDocumentoDTO->getStrNumeroDocumento() . ' ';
        }
        $ToolTipTitle .= '(SEI n� ' . $objMdPetIntDocumentoDTO->getStrProtocoloFormatadoDocumento() . ')';

        if ($cnpjs) {
            $ToolTipTitle .= '<br/><br/>';
            foreach ($cnpjs as $emp) {
                $ToolTipTitle .= 'Pessoa Jur�dica: ' . $emp . '<br/>';
            }
        }

        $ToolTipText = '';
        $ToolTipText .= 'Clique para visualizar a Certid�o.';

        $conteudoHtml = '<a onclick="' . $js . '"';
        $conteudoHtml .= 'onmouseover ="return infraTooltipMostrar(\'' . $ToolTipText . '\',\'' . $ToolTipTitle . '\')"';
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
//        //Var que verifica se a certid�o � anexo de uma Intima��o que n�o est� aceita
//        $isValido = $this->verificaDocumentoEAnexoIntimacaoNaoCumprida(array($idCertidao, $idAcessoExt));
//
//        $alertMsg = 'Documento bloqueado, pois est� vinculado a uma Intima��o ainda n�o Cumprida.';
//        $js       = $isValido ? 'window.open(\''.$strLink.'\');' : 'alert(\''.$alertMsg.'\')';
//
//        $imgCertidao = '<img src="modulos/peticionamento/imagens/intimacao_certidao.png">';
//
//        //obter informacoes do doc principal da intima��o
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
//        $ToolTipTitle = 'Certid�o de Intima��o Cumprida';
//        $ToolTipTitle .= '<br/>Documento Principal: ';
//        $ToolTipTitle .= $objMdPetIntDocumentoDTO->getStrNomeSerie() . ' ';
//        if ($objMdPetIntDocumentoDTO->getStrNumeroDocumento()){
//            $ToolTipTitle .= $objMdPetIntDocumentoDTO->getStrNumeroDocumento(). ' ' ;
//        }
//        $ToolTipTitle .= '(SEI n� ' . $objMdPetIntDocumentoDTO->getStrProtocoloFormatadoDocumento() . ')';
//
//        $ToolTipText  = '';
//        $ToolTipText .= 'Clique para visualizar a Certid�o.';
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

        if ($idContato) {
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

                //condi��o
                $isValido = $qtdIntimacoes == $qtdAceites;
            }
        }

        return $isValido;
    }

}

?>