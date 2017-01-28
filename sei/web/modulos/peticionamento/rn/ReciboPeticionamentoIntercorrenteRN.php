<?
/**
* ANATEL
*
* 28/06/2016 - criado por marcelo.bezerra - CAST
*
*/

require_once dirname(__FILE__).'/../../../SEI.php';

class ReciboPeticionamentoIntercorrenteRN extends ReciboPeticionamentoRN {
	
	//método utilizado para gerar recibo ao final do cadastramento de um processo de peticionamento de usuario externo
	protected function montarReciboControlado( $arrParams ){
		
		$reciboDTO = $arrParams[4];
		$arrDocumentos = $arrParams[5];

		//gerando documento recibo (nao assinado) dentro do processo do SEI
		$objInfraParametro = new InfraParametro($this->getObjInfraIBanco());
		
		$arrParametros = $arrParams[0]; //parametros adicionais fornecidos no formulario de peticionamento
		$objUnidadeDTO = $arrParams[1]; //UnidadeDTO da unidade geradora do processo
		$objProcedimentoDTO = $arrParams[2]; //ProcedimentoDTO para vincular o recibo ao processo correto
		$arrParticipantesParametro = $arrParams[3]; //array de ParticipanteDTO
		
		//tentando simular sessao de usuario interno do SEI
		SessaoSEI::getInstance()->setNumIdUnidadeAtual( $objUnidadeDTO->getNumIdUnidade() );
		SessaoSEI::getInstance()->setNumIdUsuario( SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno() );
		
		$grauSigiloDocPrincipal = $arrParametros['grauSigiloDocPrincipal'];
		$hipoteseLegalDocPrincipal = $arrParametros['hipoteseLegalDocPrincipal'];

        $htmlRecibo = $this->gerarHTMLConteudoDocRecibo( $arrParams );

		$protocoloRN = new ProtocoloPeticionamentoRN();
		
		//$numeroDocumento = $protocoloRN->gerarNumeracaoDocumento();
		$idSerieRecibo = $objInfraParametro->getValor('ID_SERIE_RECIBO_MODULO_PETICIONAMENTO');
		
		//=============================================
		//MONTAGEM DO PROTOCOLODTO DO DOCUMENTO
		//=============================================
		
		$protocoloReciboDocumentoDTO = new ProtocoloDTO();
		
		$protocoloReciboDocumentoDTO->setDblIdProtocolo(null);
		$protocoloReciboDocumentoDTO->setStrDescricao( null );
		$protocoloReciboDocumentoDTO->setStrStaNivelAcessoLocal( ProtocoloRN::$NA_PUBLICO );
		//$protocoloReciboDocumentoDTO->setStrProtocoloFormatado( $numeroDocumento );
		//$protocoloReciboDocumentoDTO->setStrProtocoloFormatadoPesquisa( $numeroDocumento );
		$protocoloReciboDocumentoDTO->setNumIdUnidadeGeradora( $objUnidadeDTO->getNumIdUnidade() );
		$protocoloReciboDocumentoDTO->setNumIdUsuarioGerador( SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno() );
		$protocoloReciboDocumentoDTO->setStrStaProtocolo( ProtocoloRN::$TP_DOCUMENTO_GERADO );
		
		$protocoloReciboDocumentoDTO->setStrStaNivelAcessoLocal( ProtocoloRN::$NA_PUBLICO );
		$protocoloReciboDocumentoDTO->setNumIdHipoteseLegal( null );
		$protocoloReciboDocumentoDTO->setStrStaGrauSigilo(null);
					
		$protocoloReciboDocumentoDTO->setDtaGeracao( InfraData::getStrDataAtual() );
		$protocoloReciboDocumentoDTO->setArrObjAnexoDTO(array());
		$protocoloReciboDocumentoDTO->setArrObjRelProtocoloAssuntoDTO(array());
		$protocoloReciboDocumentoDTO->setArrObjRelProtocoloProtocoloDTO(array());
		
		$protocoloReciboDocumentoDTO->setStrStaEstado( ProtocoloRN::$TE_NORMAL );
		$protocoloReciboDocumentoDTO->setArrObjLocalizadorDTO(array());
		$protocoloReciboDocumentoDTO->setArrObjObservacaoDTO( array() );
		$protocoloReciboDocumentoDTO->setArrObjParticipanteDTO( $arrParticipantesParametro );
		$protocoloReciboDocumentoDTO->setNumIdSerieDocumento( $idSerieRecibo );

		//==========================
		//ATRIBUTOS
		//==========================
		$arrRelProtocoloAtributo = AtributoINT::processar(null, null);
		
		$arrObjRelProtocoloAtributoDTO = array();
		
		for($x = 0;$x<count($arrRelProtocoloAtributo);$x++){
			$arrRelProtocoloAtributoDTO = new RelProtocoloAtributoDTO();
			$arrRelProtocoloAtributoDTO->setStrValor($arrRelProtocoloAtributo[$x]->getStrValor());
			$arrRelProtocoloAtributoDTO->setNumIdAtributo($arrRelProtocoloAtributo[$x]->getNumIdAtributo());
			$arrObjRelProtocoloAtributoDTO[$x] = $arrRelProtocoloAtributoDTO;
		}
		
		$protocoloReciboDocumentoDTO->setArrObjRelProtocoloAtributoDTO($arrObjRelProtocoloAtributoDTO);

		//=============================================
		//MONTAGEM DO DOCUMENTODTO
		//=============================================
					
		//TESTE COMENTADO $documentoBD = new DocumentoBD( $this->getObjInfraIBanco() );
		$docRN = new DocumentoPeticionamentoRN();
		
		$documentoReciboDTO = new DocumentoDTO();
		$documentoReciboDTO->setDblIdDocumento( $protocoloReciboDocumentoDTO->getDblIdProtocolo() );
		$documentoReciboDTO->setDblIdProcedimento( $objProcedimentoDTO->getDblIdProcedimento() );
		$documentoReciboDTO->setNumIdSerie( $idSerieRecibo );
		$documentoReciboDTO->setNumIdUnidadeResponsavel( $objUnidadeDTO->getNumIdUnidade() );
		$documentoReciboDTO->setObjProtocoloDTO( $protocoloReciboDocumentoDTO );
		
		$documentoReciboDTO->setNumIdConjuntoEstilos(null);
		
		$documentoReciboDTO->setNumIdTipoConferencia( null );
		$documentoReciboDTO->setStrNumero(''); //sistema atribui numeracao sequencial automatica						
		$documentoReciboDTO->setStrConteudo( $htmlRecibo );
		
		$documentoReciboDTO->setStrConteudoAssinatura(null);			
		$documentoReciboDTO->setStrCrcAssinatura(null);			
		$documentoReciboDTO->setStrQrCodeAssinatura(null);
		
		$documentoReciboDTO->setStrSinBloqueado('S');			
		
		$documentoReciboDTO->setStrStaDocumento(DocumentoRN::$TD_FORMULARIO_AUTOMATICO);
		
		$documentoReciboDTO->setNumIdTextoPadraoInterno(null);
		$documentoReciboDTO->setStrProtocoloDocumentoTextoBase('');
		
		$documentoReciboDTO = $docRN->gerarRN0003Customizado( $documentoReciboDTO );
				
		return $reciboDTO;

    }
  
    private function gerarHTMLConteudoDocRecibo( $arrParams ){
        $arrParametros      = $arrParams[0]; //parametros adicionais fornecidos no formulario de peticionamento
        $objUnidadeDTO      = $arrParams[1]; //UnidadeDTO da unidade geradora do processo
        $objProcedimentoDTO = $arrParams[2]; //ProcedimentoDTO para vincular o recibo ao processo correto
        $arrParticipantes   = $arrParams[3]; //array de ParticipanteDTO
        $reciboDTO          = $arrParams[4]; //ReciboPeticionamentoDTO
        $arrDocumentos      = $arrParams[5]; //ReciboPeticionamentoDTO

        $objUsuarioDTO = new UsuarioDTO();
        $objUsuarioDTO->retTodos();
        $objUsuarioDTO->setNumIdUsuario( $reciboDTO->getNumIdUsuario() );

        $objUsuarioRN = new UsuarioRN();
        $objUsuarioDTO = $objUsuarioRN->consultarRN0489( $objUsuarioDTO );

        $html = '';

        $html .= '<table align="center" style="width: 90%" border="0">';
        $html .= '<tbody><tr>';
        $html .= '<td style="font-weight: bold; width: 300px;">Usuário Externo (signatário):</td>';
        $html .= '<td>' . $objUsuarioDTO->getStrNome() . '</td>';
        $html .= '</tr>';

        $html .= '<tr>';
        $html .= '<td style="font-weight: bold;">IP utilizado: </td>';
        $html .= '<td>' . $reciboDTO->getStrIpUsuario() .'</td>';
        $html .= '</tr>';

        $html .= '<tr>';
        $html .= '<td style="font-weight: bold;">Data e Horário:</td>';
        $html .= '<td>' . $reciboDTO->getDthDataHoraRecebimentoFinal() .  '</td>';
        $html .= '</tr>';

        $html .= '<tr>';
        $html .= '<td style="font-weight: bold;">Tipo de Peticionamento:</td>';
        $html .= '<td>' . $reciboDTO->getStrStaTipoPeticionamentoFormatado() . '</td>';
        $html .= '</tr>';

        $html .= '<tr>';
        $html .= '<td style="font-weight: bold;">Número do Processo:</td>';
        $html .= '<td>' . $objProcedimentoDTO->getStrProtocoloProcedimentoFormatado() .  '</td>';
        $html .= '</tr>';

        //obter interessados (apenas os do tipo interessado, nao os do tipo remetente)
        $arrInteressados = array();
        /*
        $objParticipanteDTO = new ParticipanteDTO();
        $objParticipanteDTO->setDblIdProtocolo( $reciboDTO->getNumIdProtocolo() );
        $objParticipanteDTO->setStrStaParticipacao( ParticipanteRN::$TP_INTERESSADO );
        $objParticipanteDTO->retNumIdContato();
        */
        $objParticipanteRN = new ParticipanteRN();
        //$arrObjParticipanteDTO = $objParticipanteRN->listarRN0189($objParticipanteDTO);

        foreach ($arrParticipantes as $objParticipanteDTO) {
            $objContatoDTO = new ContatoDTO();
            $objContatoDTO->setNumIdContato($objParticipanteDTO->getNumIdContato());
            $objContatoDTO->retStrNome();
            $objContatoRN      = new ContatoRN();
            $arrInteressados[] = $objContatoRN->consultarRN0324($objContatoDTO);
        }

        $html .= '<tr>';
        $html .= '<td colspan="2" style="font-weight: bold;">Interessados:</td>';
        $html .= '</tr>';

        if( $arrInteressados != null && count( $arrInteressados ) > 0 ){
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
        /*
        $arr = PaginaSEI::getInstance()->getArrItensTabelaDinamica($arrParametros['hdnTbDocumento']);
        ob_start();
        var_dump($arr);
        var_dump($arrParams);
        $dump = ob_get_contents();
        ob_end_clean();

        $html .= $dump;
        */
        if( $arrDocumentos != null && count( $arrDocumentos ) > 0  ){
          foreach($arrDocumentos as $documentoDTO){
            $strNumeroSEI = $documentoDTO->getStrProtocoloDocumentoFormatado();
            //concatenar tipo e complemento
            $strNome = $documentoDTO->getStrNomeSerie() . ' ' . $documentoDTO->getStrNumero();
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
        $objOrgaoDTO->setNumIdOrgao( $objUnidadeDTO->getNumIdOrgao() );
        $objOrgaoDTO->setStrSinAtivo('S');
        $objOrgaoDTO = $orgaoRN->consultarRN1352( $objOrgaoDTO );

        $html .= '<p>O Usuário Externo acima identificado foi previamente avisado que o peticionamento importa na aceitação dos termos e condições que regem o processo eletrônico, além do disposto no credenciamento prévio, e na assinatura dos documentos nato-digitais e declaração de que são autênticos os digitalizados, sendo responsável civil, penal e administrativamente pelo uso indevido. Ainda, foi avisado que os níveis de acesso indicados para os documentos estariam condicionados à análise por servidor público, que poderá, motivadamente, alterá-los a qualquer momento sem necessidade de prévio aviso, e de que são de sua exclusiva responsabilidade:</p><ul><li>a conformidade entre os dados informados e os documentos;</li><li>a conservação dos originais em papel de documentos digitalizados até que decaia o direito de revisão dos atos praticados no processo, para que, caso solicitado, sejam apresentados para qualquer tipo de conferência;</li><li>a realização por meio eletrônico de todos os atos e comunicações processuais com o próprio Usuário Externo ou, por seu intermédio, com a entidade porventura representada;</li><li>a observância de que os atos processuais se consideram realizados no dia e hora do recebimento pelo SEI, considerando-se tempestivos os praticados até as 23h59min59s do último dia do prazo, considerado sempre o horário oficial de Brasília, independente do fuso horário em que se encontre;</li><li>a consulta periódica ao SEI, a fim de verificar o recebimento de intimações eletrônicas.</li></ul><p>A existência deste Recibo, do processo e dos documentos acima indicados pode ser conferida no Portal na Internet do(a) ' . $objOrgaoDTO->getStrDescricao() . '.</p>';
        return $html;
  }


	protected function gerarReciboSimplificadoIntercorrenteControlado($arr) {
		if(is_array($arr)){

			$idProcedimento    = array_key_exists('idProcedimento', $arr) ? $arr['idProcedimento'] : null;
			$idProcedimentoRel = array_key_exists('idProcedimentoRel', $arr) ? $arr['idProcedimentoRel'] : null;

				if(!is_null($idProcedimento))
				{
					$reciboDTO = new ReciboPeticionamentoDTO();

					$reciboDTO->setNumIdProtocolo( $idProcedimento );
					$reciboDTO->setNumIdUsuario( SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno() );
					$reciboDTO->setDthDataHoraRecebimentoFinal( InfraData::getStrDataHoraAtual() );
					$reciboDTO->setStrIpUsuario( InfraUtil::getStrIpUsuario() );
					$reciboDTO->setStrSinAtivo('S');
					$reciboDTO->setStrStaTipoPeticionamento('I');

                    if(!is_null($idProcedimentoRel)){
						$reciboDTO->setDblIdProtocoloRelacionado($idProcedimentoRel);
					}

					$objBD = new ReciboPeticionamentoBD($this->getObjInfraIBanco());
					$ret = $objBD->cadastrar( $reciboDTO );
					return $ret;
				}
		}

		return null;
	}

}
?>