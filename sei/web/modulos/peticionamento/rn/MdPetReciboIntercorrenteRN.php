<?

/**
 * ANATEL
 *
 * 28/06/2016 - criado por marcelo.bezerra - CAST
 *
 */
require_once dirname(__FILE__) . '/../../../SEI.php';

class MdPetReciboIntercorrenteRN extends MdPetReciboRN {

    //m�todo utilizado para gerar recibo ao final do cadastramento de um processo de peticionamento de usuario externo
    protected function montarReciboControlado($arrParams) {

        $reciboDTO = $arrParams[4];
        $arrDocumentos = $arrParams[5];

        //gerando documento recibo (nao assinado) dentro do processo do SEI
        $objInfraParametro = new InfraParametro($this->getObjInfraIBanco());

        $arrParametros = $arrParams[0]; //parametros adicionais fornecidos no formulario de peticionamento
        $objUnidadeDTO = $arrParams[1]; //UnidadeDTO da unidade geradora do processo
        $objProcedimentoDTO = $arrParams[2]; //ProcedimentoDTO para vincular o recibo ao processo correto
        $arrParticipantesParametro = $arrParams[3]; //array de ParticipanteDTO
        //tentando simular sessao de usuario interno do SEI
        SessaoSEI::getInstance()->setNumIdUnidadeAtual($objUnidadeDTO->getNumIdUnidade());
        SessaoSEI::getInstance()->setNumIdUsuario(SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno());

        $grauSigiloDocPrincipal = $arrParametros['grauSigiloDocPrincipal'];
        $hipoteseLegalDocPrincipal = $arrParametros['hipoteseLegalDocPrincipal'];

        $htmlRecibo = $this->gerarHTMLConteudoDocRecibo($arrParams);

        $protocoloRN = new MdPetProtocoloRN();

        $idSerieRecibo = $objInfraParametro->getValor(MdPetIntSerieRN::$MD_PET_ID_SERIE_RECIBO);

        //=============================================
        //MONTAGEM DO DOCUMENTO VIA SEI RN
        //=============================================

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

        //necessario for�ar update da coluna sta_documento da tabela documento
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
        $reciboDTO = $objBD->alterar($reciboDTO);

        return $parObjDocumentoDTO;
    }

    private function gerarHTMLConteudoDocRecibo($arrParams) {

        $arrParametros = $arrParams[0]; //parametros adicionais fornecidos no formulario de peticionamento
        $objUnidadeDTO = $arrParams[1]; //UnidadeDTO da unidade geradora do processo
        $objProcedimentoDTO = $arrParams[2]; //ProcedimentoDTO para vincular o recibo ao processo correto
        $arrParticipantes = $arrParams[3]; //array de ParticipanteDTO
        $reciboDTO = $arrParams[4]; //MdPetReciboDTO
        $arrDocumentos = $arrParams[5]; //MdPetReciboDTO

        $objUsuarioDTO = new UsuarioDTO();
        $objUsuarioDTO->retTodos(true);
        $objUsuarioDTO->setNumIdUsuario($reciboDTO->getNumIdUsuario());

        $objUsuarioRN = new UsuarioRN();
        $objUsuarioDTO = $objUsuarioRN->consultarRN0489($objUsuarioDTO);

        $html = '';

        $html .= '<table align="center" style="width: 95%" border="0">';
        $html .= '<tbody>';
        
        if ($arrParametros['sin_pessoa_juridica'] == MdPetIntRelDestinatarioRN::$PESSOA_JURIDICA) {
            $html .= '<tr>';
            $html .= '<td style="font-weight: bold; width: 400px;">Pessoa Jur�dica:</td>';
            $html .= '<td>' . $arrParametros['nome_contato'] . '</td>';
            $html .= '</tr>';
            $html .= '<tr>';
            $html .= '<td style="font-weight: bold; width: 400px;">Usu�rio Externo (Representante):</td>';
            $html .= '<td>' . $arrParametros['nome_usuario'] . '</td>';
            $html .= '</tr>';
        } else {
            $html .= '<tr>';
            $html .= '<td style="font-weight: bold; width: 400px;">Usu�rio Externo (signat�rio):</td>';
            $html .= '<td>' . $objUsuarioDTO->getStrNome() . '</td>';
            $html .= '</tr>';
        }
        //$html .= '<tr>';
        //$html .= '<td style="font-weight: bold;">IP utilizado:</td>';
        //$html .= '<td>' . $reciboDTO->getStrIpUsuario() . '</td>';
        //$html .= '</tr>';

        $html .= '<tr>';
        $html .= '<td style="font-weight: bold;">Data e Hor�rio:</td>';
        $html .= '<td>' . $reciboDTO->getDthDataHoraRecebimentoFinal() . '</td>';
        $html .= '</tr>';

        $html .= '<tr>';
        $html .= '<td style="font-weight: bold;">Tipo de Peticionamento:</td>';
        $html .= '<td>' . $reciboDTO->getStrStaTipoPeticionamentoFormatado() . '</td>';
        $html .= '</tr>';

        $html .= '<tr>';
        $html .= '<td style="font-weight: bold;">N�mero do Processo:</td>';
        $html .= '<td>' . $objProcedimentoDTO->getStrProtocoloProcedimentoFormatado() . '</td>';
        $html .= '</tr>';

        if ($reciboDTO->isSetDblIdProtocoloRelacionado()) {

            $idProtocoloPrinc = $reciboDTO->getDblIdProtocoloRelacionado();

            if (!(InfraString::isBolVazia($idProtocoloPrinc))) {

                $objProtocoloRN = new ProtocoloRN();
                $objProtocoloDTO = new ProtocoloDTO();

                $objProtocoloDTO->setDblIdProtocolo($idProtocoloPrinc);
                $objProtocoloDTO->retTodos();

                $objProtocoloDTO = $objProtocoloRN->consultarRN0186($objProtocoloDTO);
                $protocoloRelFormatado = $objProtocoloDTO->getStrProtocoloFormatado();

                //se houver processo relacionado
                $html .= '<tr>';
                $html .= '<td> &ensp;&nbsp; Relacionado ao Processo Indicado:</td>';
                $html .= '<td>' . $protocoloRelFormatado . '</td>';
                $html .= '</tr>';
            }
        }

        //se for recibo de resposta adicionar campos especificos
        if (isset($arrParametros['isRespostaIntimacao'])) {

            //obter tipo da intim��o
            $id_intimacao = $arrParametros['id_intimacao'];
            $dtoIntimacao = new MdPetIntimacaoDTO();
            $rnIntimacao = new MdPetIntimacaoRN();
            $dtoIntimacao->retTodos();
            $dtoIntimacao->retStrNomeTipoIntimacao();
            $dtoIntimacao->setNumIdMdPetIntimacao($id_intimacao);
            $dtoIntimacao = $rnIntimacao->consultar($dtoIntimacao);

            //obter documento principal da intima��o
            $rnDocIntimacao = new MdPetIntProtocoloRN();
            $dtoDocIntimacao = new MdPetIntProtocoloDTO();
            $dtoDocIntimacao->setStrSinPrincipal('S');
            $dtoDocIntimacao->setNumIdMdPetIntimacao($id_intimacao);
            $dtoDocIntimacao->retTodos();
            $dtoDocIntimacao->retNumIdSerie();
            $dtoDocIntimacao->retStrNomeSerie();
            $dtoDocIntimacao->retStrNumeroDocumento();
            $dtoDocIntimacao->retStrProtocoloFormatadoDocumento();
            $dtoDocIntimacao->retDblIdProtocolo();
            $dtoDocIntimacao = $rnDocIntimacao->consultar($dtoDocIntimacao);

            $n1 = $dtoDocIntimacao->getStrNomeSerie();
            $n2 = $dtoDocIntimacao->getStrNumeroDocumento();
            $n3 = $dtoDocIntimacao->getStrProtocoloFormatadoDocumento();

            $texto_doc = $n1 . " " . $n2 . " (" . $n3 . ")";

            //obter tipo de resposta
            $id_md_pet_int_rel_tipo_resp = $arrParametros['id_tipo_resposta'];
            $rnTipoResposta = new MdPetIntRelTipoRespRN();
            $dtoTipoResposta = new MdPetIntRelTipoRespDTO();
            $dtoTipoResposta->retTodos();
            $dtoTipoResposta->retStrNome();
            $dtoTipoResposta->setNumIdMdPetIntRelTipoResp($id_md_pet_int_rel_tipo_resp);
            $dtoTipoResposta = $rnTipoResposta->consultar($dtoTipoResposta);

            $html .= '<tr>';
            $html .= '<td style="font-weight: bold;">Tipo de Intima��o:</td>';
            $html .= '<td> ' . $dtoIntimacao->getStrNomeTipoIntimacao() . ' </td>';
            $html .= '</tr>';

            $html .= '<tr>';
            $html .= '<td style="font-weight: bold;">Documento Principal da Intima��o:</td>';
            $html .= '<td> ' . $texto_doc . ' </td>';
            $html .= '</tr>';

            $html .= '<tr>';
            $html .= '<td style="font-weight: bold;">Tipo de Resposta:</td>';
            $html .= '<td> ' . $dtoTipoResposta->getStrNome() . ' </td>';
            $html .= '</tr>';
        }

        //obter interessados (apenas os do tipo interessado, nao os do tipo remetente)
        $arrInteressados = array();

        //obter interessados (apenas os do tipo interessado, nao os do tipo remetente)
        $arrInteressados = array();
        $objParticipanteDTO = new ParticipanteDTO();
        $objParticipanteDTO->setDblIdProtocolo($objProcedimentoDTO->getDblIdProcedimento());
        $objParticipanteDTO->setStrStaParticipacao(ParticipanteRN::$TP_INTERESSADO);
        $objParticipanteDTO->retNumIdContato();
        $objParticipanteRN = new ParticipanteRN();
        $arrObjParticipanteDTO = $objParticipanteRN->listarRN0189($objParticipanteDTO);

        $objContatoRN = new ContatoRN();

        foreach ($arrObjParticipanteDTO as $objParticipanteDTO) {
            $objContatoDTO = new ContatoDTO();
            $objContatoDTO->setNumIdContato($objParticipanteDTO->getNumIdContato());
            $objContatoDTO->retStrNome();
            $objContatoDTO->setBolExclusaoLogica(false); //� possivel e permitido usar aqui contato desativado!!!
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
        $html .= '<td style="font-weight: bold;">Protocolos dos Documentos (N�mero SEI):</td>';
        $html .= '<td></td>';
        $html .= '</tr>';

        if ($arrDocumentos != null && count($arrDocumentos) > 0) {
            foreach ($arrDocumentos as $documentoDTO) {
                $strNumeroSEI = $documentoDTO->getStrProtocoloDocumentoFormatado();
                //concatenar tipo e complemento
                $strNome = $documentoDTO->getStrNomeSerie() . ' ' . $documentoDTO->getStrDescricaoProtocolo() . ' ' . $documentoDTO->getStrNumero();
                $html .= '<tr>';
                $html .= '<td> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; - ' . $strNome . '</td>';
                $html .= '<td>' . $strNumeroSEI . '</td>';
                $html .= '</tr>';
            }
        }

        $html .= '</tbody></table>';

        $orgaoRN = new OrgaoRN();
        $objOrgaoDTO = new OrgaoDTO();
        $objOrgaoDTO->retTodos();
        $objOrgaoDTO->setNumIdOrgao($objUnidadeDTO->getNumIdOrgao());
        $objOrgaoDTO->setStrSinAtivo('S');
        $objOrgaoDTO = $orgaoRN->consultarRN1352($objOrgaoDTO);

        $html .= '<p>O Usu�rio Externo acima identificado foi previamente avisado que o peticionamento importa na aceita��o dos termos e condi��es que regem o processo eletr�nico, al�m do disposto no credenciamento pr�vio, e na assinatura dos documentos nato-digitais e declara��o de que s�o aut�nticos os digitalizados, sendo respons�vel civil, penal e administrativamente pelo uso indevido. Ainda, foi avisado que os n�veis de acesso indicados para os documentos estariam condicionados � an�lise por servidor p�blico, que poder� alter�-los a qualquer momento sem necessidade de pr�vio aviso, e de que s�o de sua exclusiva responsabilidade:</p><ul><li>a conformidade entre os dados informados e os documentos;</li><li>a conserva��o dos originais em papel de documentos digitalizados at� que decaia o direito de revis�o dos atos praticados no processo, para que, caso solicitado, sejam apresentados para qualquer tipo de confer�ncia;</li><li>a realiza��o por meio eletr�nico de todos os atos e comunica��es processuais com o pr�prio Usu�rio Externo ou, por seu interm�dio, com a entidade porventura representada;</li><li>a observ�ncia de que os atos processuais se consideram realizados no dia e hora do recebimento pelo SEI, considerando-se tempestivos os praticados at� as 23h59min59s do �ltimo dia do prazo, considerado sempre o hor�rio oficial de Bras�lia, independente do fuso hor�rio em que se encontre;</li><li>a consulta peri�dica ao SEI, a fim de verificar o recebimento de intima��es eletr�nicas.</li></ul><p>A exist�ncia deste Recibo, do processo e dos documentos acima indicados pode ser conferida no Portal na Internet do(a) ' . $objOrgaoDTO->getStrDescricao() . '.</p>';

        return $html;
    }

    protected function gerarReciboSimplificadoIntercorrenteControlado($arr) {

        if (is_array($arr)) {

            $idProcedimento = array_key_exists('idProcedimento', $arr) ? $arr['idProcedimento'] : null;
            $idProcedimentoRel = array_key_exists('idProcedimentoRel', $arr) ? $arr['idProcedimentoRel'] : null;

            if (!is_null($idProcedimento)) {
                $reciboDTO = new MdPetReciboDTO();

                $reciboDTO->setNumIdProtocolo($idProcedimento);
                $reciboDTO->setNumIdUsuario(SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno());
                $reciboDTO->setDthDataHoraRecebimentoFinal(InfraData::getStrDataHoraAtual());
                $reciboDTO->setStrIpUsuario(InfraUtil::getStrIpUsuario());
                $reciboDTO->setStrSinAtivo('S');

                //recibo intercorrente
                if (!isset($arr['isRespostaIntimacao'])) {
                    $reciboDTO->setStrStaTipoPeticionamento(MdPetReciboRN::$TP_RECIBO_INTERCORRENTE);
                }

                //recibo de resposta a intimacao
                else {
                    $reciboDTO->setStrStaTipoPeticionamento(MdPetReciboRN::$TP_RECIBO_RESPOSTA_INTIMACAO);
                }

                if (!is_null($idProcedimentoRel)) {
                    $reciboDTO->setDblIdProtocoloRelacionado($idProcedimentoRel);
                }

                $objBD = new MdPetReciboBD($this->getObjInfraIBanco());
                $ret = $objBD->cadastrar($reciboDTO);
                return $ret;
            }
        }

        return null;
    }

}

?>