<?
/**
* ANATEL
*
* 28/06/2016 - criado por marcelo.bezerra - CAST
*
*/

require_once dirname(__FILE__).'/../../../SEI.php';

class ReciboPeticionamentoRN extends InfraRN { 
	
	public function __construct() {
		parent::__construct ();
	}
	
	protected function inicializarObjInfraIBanco() {
		return BancoSEI::getInstance ();
	}
	
	/**
	 * Short description of method listarConectado
	 *
	 * @access protected
	 * @author Marcelo Bezerra <marcelo.bezerra@cast.com.br>
	 * @param $objDTO
	 * @return mixed
	 */
	protected function listarConectado(ReciboPeticionamentoDTO $objDTO) {
	
		try {
							
			$objInfraException = new InfraException();
			
			if ($objDTO->isSetDthInicial() || $objDTO->isSetDthFinal()){

				// Data Início
				if ($objDTO->isSetDthInicial()){
					if (strlen($objDTO->getDthInicial())=='10'){
						$objDTO->setDthInicial($objDTO->getDthInicial().' 00:00:00');
					}elseif (strlen($objDTO->getDthInicial())=='16'){
						$objDTO->setDthInicial($objDTO->getDthInicial().':00');
					}
					if (!InfraData::validarDataHora($objDTO->getDthInicial())){
						$objInfraException->lancarValidacao('Data/Hora Inválida.');
					}
				}
			
				// Data Final recebe Inicial se estiver em branco ?????
				if ($objDTO->isSetDthFinal()){
					if (strlen($objDTO->getDthFinal())=='10'){
						$objDTO->setDthFinal($objDTO->getDthFinal().' 23:59:59');
					}elseif (strlen($objDTO->getDthFinal())=='16'){
						$objDTO->setDthFinal($objDTO->getDthFinal().':59');
					}
					if (!InfraData::validarDataHora($objDTO->getDthFinal())){
						$objInfraException->lancarValidacao('Data/Hora Inválida.');
					}
				}
			
				// Data Incio e Data Fim - Comparando
				if ($objDTO->isSetDthInicial() && $objDTO->isSetDthFinal()){
					if (InfraData::compararDataHora($objDTO->getDthInicial(),$objDTO->getDthFinal())<0){
						$objInfraException->lancarValidacao('A Data/Hora Inicio deve ser menor que a Data/Hora Fim.');
					}
					// Data Incio e Data Fim - Comparando
					$objDTO->adicionarCriterio(array('DataHoraRecebimentoFinal','DataHoraRecebimentoFinal'),
							array(InfraDTO::$OPER_MAIOR_IGUAL,InfraDTO::$OPER_MENOR_IGUAL),
							array($objDTO->getDthInicial(),$objDTO->getDthFinal()),
							InfraDTO::$OPER_LOGICO_AND);
				}else{
					if ($objDTO->isSetDthInicial() && !$objDTO->isSetDthFinal()){
						// Data Incio - Comparando
						$objDTO->adicionarCriterio(array('DataHoraRecebimentoFinal'),
								array(InfraDTO::$OPER_MAIOR_IGUAL),
								array($objDTO->getDthInicial())/*,
								InfraDTO::$OPER_LOGICO_AND*/);
					}elseif (!$objDTO->isSetDthInicial() && $objDTO->isSetDthFinal()){
						// Data Fim - Comparando
						$objDTO->adicionarCriterio(array('DataHoraRecebimentoFinal'),
								array(InfraDTO::$OPER_MENOR_IGUAL),
								array($objDTO->getDthFinal())/*,
								InfraDTO::$OPER_LOGICO_AND*/);
					}
				}
			
			}
			
			$objBD = new ReciboPeticionamentoBD($this->getObjInfraIBanco());
			$ret = $objBD->listar($objDTO);	
			return $ret;
			
		} catch (Exception $e) {
			throw new InfraException ('Erro listando Recibo Peticionamento.', $e);
		}
	}
	
	/**
	 * Short description of method consultarConectado
	 *
	 * @access protected
	 * @author Marcelo Bezerra <marcelo.bezerra@cast.com.br>
	 * @param $objDTO
	 * @return mixed
	 */
	protected function consultarConectado(ReciboPeticionamentoDTO $objDTO) {
	
		try {
	
			$objBD = new ReciboPeticionamentoBD($this->getObjInfraIBanco());
			$ret = $objBD->consultar( $objDTO );
			$ret->setArrObjReciboDocumentoAnexoPeticionamentoDTO( array() );
			return $ret;
				
		} catch (Exception $e) {
			throw new InfraException ('Erro listando Recibo Peticionamento.', $e);
		}
	}
	
	protected function gerarReciboSimplificadoControlado( $idProcedimento ) {
		
		$reciboDTO = new ReciboPeticionamentoDTO();
		
		$reciboDTO->setNumIdProtocolo( $idProcedimento );
		$reciboDTO->setNumIdUsuario( SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno() );
		$reciboDTO->setDthDataHoraRecebimentoFinal( InfraData::getStrDataHoraAtual() );
		$reciboDTO->setStrIpUsuario( InfraUtil::getStrIpUsuario() );
		$reciboDTO->setStrSinAtivo('S');
		$reciboDTO->setStrStaTipoPeticionamento('N');
		
		$objBD = new ReciboPeticionamentoBD($this->getObjInfraIBanco());
		$ret = $objBD->cadastrar( $reciboDTO );
		return $ret;
		
	}
	
	/*
	 produz recibo pesquisavel, inserindo dados consultáveis pela consulta de recibos
	 (diferente do documento de recibo que é anexado ao processo do SEI)(
	 */
	protected function cadastrarControlado( $arrParams ) {
	    
		$arrParametros = $arrParams[0]; //parametros adicionais fornecidos no formulario de peticionamento
		$objUnidadeDTO = $arrParams[1]; //UnidadeDTO da unidade geradora do processo
		$objProcedimentoDTO = $arrParams[2]; //ProcedimentoDTO para vincular o recibo ao processo correto
		$arrParticipantesParametro = $arrParams[3]; //array de ParticipanteDTO
		$arrDocsPrincipais = $arrParams[4]; //array de DocumentoDTO (docs principais)
		$arrDocsEssenciais = $arrParams[5]; //array de DocumentoDTO (docs essenciais)
		$arrDocsComplementares = $arrParams[6]; //array de DocumentoDTO (docs complementares)

		$reciboDTO = new ReciboPeticionamentoDTO();
		
		$reciboDTO->setNumIdProtocolo( $objProcedimentoDTO->getDblIdProcedimento() );
		$reciboDTO->setNumIdUsuario( SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno() );
		$reciboDTO->setDthDataHoraRecebimentoFinal( InfraData::getStrDataHoraAtual() );		
		$reciboDTO->setStrIpUsuario( InfraUtil::getStrIpUsuario() );		
		$reciboDTO->setStrSinAtivo('S');		
		$reciboDTO->setStrStaTipoPeticionamento('N');
		
		$objBD = new ReciboPeticionamentoBD($this->getObjInfraIBanco());
		$ret = $objBD->cadastrar( $reciboDTO );
		
		return $ret; 
	
    }
	
	//método utilizado para gerar recibo ao final do cadastramento de um processo de peticionamento de usuario externo
	protected function montarReciboControlado( $arrParams ){
		
		$reciboDTO = $arrParams[4];
		
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
				
		//TODO montar corretamente conteudo HTML final do recibo
		//$htmlRecibo = "teste";
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
		$protocoloReciboDocumentoDTO->setStrStaArquivamento(ProtocoloRN::$TA_NAO_ARQUIVADO);
		$protocoloReciboDocumentoDTO->setNumIdLocalizador(null);
		$protocoloReciboDocumentoDTO->setNumIdUnidadeArquivamento(null);
		$protocoloReciboDocumentoDTO->setNumIdUsuarioArquivamento(null);
		$protocoloReciboDocumentoDTO->setDthArquivamento(null);
		$protocoloReciboDocumentoDTO->setArrObjObservacaoDTO( array() );
		$protocoloReciboDocumentoDTO->setArrObjParticipanteDTO( $arrParticipantesParametro );
		$protocoloReciboDocumentoDTO->setNumIdSerieDocumento( $idSerieRecibo );

		//==========================
		//ATRIBUTOS
		//==========================
		
		$arrRelProtocoloAtributo = AtributoINT::processarRI0691();
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
		
		//TODO de onde pega o tipo conferencia?
		$documentoReciboDTO->setNumIdTipoConferencia( null );
		$documentoReciboDTO->setStrNumero(''); //sistema atribui numeracao sequencial automatica						
		$documentoReciboDTO->setStrConteudo( $htmlRecibo );
		
		$documentoReciboDTO->setStrConteudoAssinatura(null);			
		$documentoReciboDTO->setStrCrcAssinatura(null);			
		$documentoReciboDTO->setStrQrCodeAssinatura(null);
		
		$documentoReciboDTO->setStrSinBloqueado('S');			
		$documentoReciboDTO->setStrStaEditor( EditorRN::$TE_NENHUM );			
		$documentoReciboDTO->setStrSinFormulario('S');			
		$documentoReciboDTO->setNumVersaoLock(0);
		
		$documentoReciboDTO->setNumIdTextoPadraoInterno(null);
		$documentoReciboDTO->setStrProtocoloDocumentoTextoBase('');
		
		$documentoReciboDTO = $docRN->gerarRN0003Customizado( $documentoReciboDTO );
				
		return $reciboDTO;

  }
  
  private function gerarHTMLConteudoDocRecibo( $arrParams ){
      
  	$arrParametros = $arrParams[0]; //parametros adicionais fornecidos no formulario de peticionamento
  	$objUnidadeDTO = $arrParams[1]; //UnidadeDTO da unidade geradora do processo
  	$objProcedimentoDTO = $arrParams[2]; //ProcedimentoDTO para vincular o recibo ao processo correto
  	$arrParticipantesParametro = $arrParams[3]; //array de ParticipanteDTO
  	$reciboDTO = $arrParams[4]; //ReciboPeticionamentoDTO
  	
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
    $objParticipanteDTO = new ParticipanteDTO();
    $objParticipanteDTO->setDblIdProtocolo( $reciboDTO->getNumIdProtocolo() );
    $objParticipanteDTO->setStrStaParticipacao( ParticipanteRN::$TP_INTERESSADO );
    $objParticipanteDTO->retNumIdContato();
    $objParticipanteRN = new ParticipanteRN();
    $arrObjParticipanteDTO = $objParticipanteRN->listarRN0189($objParticipanteDTO);
    
    foreach ($arrObjParticipanteDTO as $objParticipanteDTO) {
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
    
    $idPrincipalGerado = SessaoSEIExterna::getInstance()->getAtributo('idDocPrincipalGerado');
    $arrIdPrincipal = SessaoSEIExterna::getInstance()->getAtributo('arrIdAnexoPrincipal');
    $arrIdEssencial = SessaoSEIExterna::getInstance()->getAtributo('arrIdAnexoEssencial');
    $arrIdComplementar = SessaoSEIExterna::getInstance()->getAtributo('arrIdAnexoComplementar');
    
    $anexoRN = new AnexoRN();
    $documentoRN = new DocumentoRN();
    
    if( $idPrincipalGerado != null ){
    	
    	$html .= '<tr>';
    	$html .= '<td style="font-weight: bold;">&nbsp;&nbsp;&nbsp; - Documento Principal:</td>';
    	$html .= '<td></td>';
    	$html .= '</tr>';
    	    	 
    	$documentoDTO = new DocumentoDTO();
    	$documentoDTO->retStrNumero();
    	$documentoDTO->retStrNomeSerie();
    	$documentoDTO->retStrDescricaoProtocolo();
    	$documentoDTO->retStrProtocoloDocumentoFormatado();
    	$documentoDTO->setDblIdDocumento( $idPrincipalGerado );
    	$documentoDTO = $documentoRN->consultarRN0005( $documentoDTO );
    	
    	$strNome = $documentoDTO->getStrNomeSerie() . " " . $documentoDTO->getStrNumero();
    	$strNumeroSEI = $documentoDTO->getStrProtocoloDocumentoFormatado();
    	
    	$html .= '<tr>';
    	$html .= '<td> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; - ' . $strNome . '</td>';
    	$html .= '<td>' . $strNumeroSEI . '</td>';
    	$html .= '</tr>';
    	
    	//SessaoSEIExterna::getInstance()->removerAtributo('idDocPrincipalGerado');
    }
    
    if( $arrIdPrincipal != null && count( $arrIdPrincipal ) > 0  ){
      
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
      
      $arrAnexoDTO = $anexoRN->listarRN0218( $objAnexoDTO );
      
      foreach($arrAnexoDTO as $anexoPrincipal){
      	
      	$strNome = $anexoPrincipal->getStrNome();
      	$strTipoDocumento = "";
      	$strNumeroSEI = $anexoPrincipal->getStrProtocoloFormatadoProtocolo();
      	
      	$documentoDTO = new DocumentoDTO();
      	
      	$documentoDTO->retStrNumero();
      	$documentoDTO->retStrNomeSerie();
      	$documentoDTO->retStrDescricaoProtocolo();
      	$documentoDTO->retStrProtocoloDocumentoFormatado();
      	
      	$documentoDTO->setDblIdDocumento( $anexoPrincipal->getDblIdProtocolo() );
      	$documentoDTO = $documentoRN->consultarRN0005( $documentoDTO );
      	
      	//concatenar tipo e complemento
      	$strNome = $documentoDTO->getStrNomeSerie() . ' ' . $documentoDTO->getStrNumero();
      	
      	$html .= '<tr>';
      	$html .= '<td> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; - ' . $strNome . '</td>';
      	$html .= '<td>' . $strNumeroSEI . '</td>';
      	$html .= '</tr>';
      	      	
      }
      
      //fim loop de documentos principais
    }
    	
    //ESSENCIAL
    
    if( $arrIdEssencial != null && count( $arrIdEssencial ) > 0  ){
    	
    	$html .= '<tr>';
    	$html .= '<td style="font-weight: bold;">&nbsp;&nbsp;&nbsp; - Documentos Essenciais:</td>';
    	$html .= '<td></td>';
    	$html .= '</tr>';
    	    	
    	$objAnexoDTO = new AnexoDTO();
    	$objAnexoDTO->retTodos(true);
    	
    	$objAnexoDTO->adicionarCriterio(array('IdAnexo'),
    			array(InfraDTO::$OPER_IN),
    			array($arrIdEssencial));
    	
    	$arrAnexoDTOEssencial = $anexoRN->listarRN0218( $objAnexoDTO );
    	
    	foreach( $arrAnexoDTOEssencial as $objAnexoEssencial ){  		
    		
    		$strNome = $objAnexoEssencial->getStrNome();
    		$strTipoDocumento = "";
    		$strNumeroSEI = $objAnexoEssencial->getStrProtocoloFormatadoProtocolo();
    		 
    		$documentoDTO = new DocumentoDTO();
    		
    		$documentoDTO->retStrNumero();
    		$documentoDTO->retStrNomeSerie();
    		$documentoDTO->retStrDescricaoProtocolo();
    		$documentoDTO->retStrProtocoloDocumentoFormatado();
    		
    		$documentoDTO->setDblIdDocumento( $objAnexoEssencial->getDblIdProtocolo() );
    		$documentoDTO = $documentoRN->consultarRN0005( $documentoDTO );
    		
    		//concatenar tipo e complemento
    		$strNome = $documentoDTO->getStrNomeSerie() . ' ' . $documentoDTO->getStrNumero();
    		
    		$html .= '<tr>';
    		$html .= '<td> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; - ' . $strNome . '</td>';
    		$html .= '<td>' . $strNumeroSEI . '</td>';
    		$html .= '</tr>'; 
    		
    	}
    	
    }
    
    //FIM ESSENCIAL
    
    //COMPLEMENTAR
    
    if( $arrIdComplementar != null && count( $arrIdComplementar ) > 0  ){
    	
    	$html .= '<tr>';
    	$html .= '<td style="font-weight: bold;">&nbsp;&nbsp;&nbsp; - Documentos Complementares:</td>';
    	$html .= '<td></td>';
    	$html .= '</tr>';
    	
    	$objAnexoDTO = new AnexoDTO();
    	$objAnexoDTO->retTodos(true);
    	 
    	$objAnexoDTO->adicionarCriterio(array('IdAnexo'),
    			array(InfraDTO::$OPER_IN),
    			array($arrIdComplementar));
    	 
    	$arrAnexoDTOComplementar = $anexoRN->listarRN0218( $objAnexoDTO );
    	    	 
    	foreach( $arrAnexoDTOComplementar as $objAnexoComplementar ){
    		
    		$strNome = $objAnexoComplementar->getStrNome();
    		$strTipoDocumento = "";
    		$strNumeroSEI = $objAnexoComplementar->getStrProtocoloFormatadoProtocolo();
    		 
    		$documentoDTO = new DocumentoDTO();
    		
    		$documentoDTO->retStrNumero();
    		$documentoDTO->retStrNomeSerie();
    		$documentoDTO->retStrDescricaoProtocolo();
    		$documentoDTO->retStrProtocoloDocumentoFormatado();
    		
    		$documentoDTO->setDblIdDocumento( $objAnexoComplementar->getDblIdProtocolo() );
    		$documentoDTO = $documentoRN->consultarRN0005( $documentoDTO );
    		
    		//concatenar tipo e complemento
    		$strNome = $documentoDTO->getStrNomeSerie() . ' ' . $documentoDTO->getStrNumero();
    		
    		$html .= '<tr>';
    		$html .= '<td> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; - ' . $strNome . '</td>';
    		$html .= '<td>' . $strNumeroSEI . '</td>';
    		$html .= '</tr>';
    		
    	}
    	 
    }
    
    //FIM COMPLEMENTAR

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

}
?>