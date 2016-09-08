<?
/**
* ANATEL
*
* 28/06/2016 - criado por marcelo.bezerra - CAST
*
*/

require_once dirname(__FILE__).'/../../../SEI.php';

//Data
require_once dirname(__FILE__).'/../util/DataUtils.php';

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
		
		//echo $idProcedimento; die();
		$reciboDTO = new ReciboPeticionamentoDTO();
		
		$reciboDTO->setNumIdProtocolo( $idProcedimento );
		$reciboDTO->setNumIdUsuario( SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno() );
		$reciboDTO->setDthDataHoraRecebimentoFinal( InfraData::getStrDataHoraAtual() );
		$reciboDTO->setStrIpUsuario( InfraUtil::getStrIpUsuario() );
		$reciboDTO->setStrSinAtivo('S');
		$reciboDTO->setStrTipoPeticionamento('Novo');
		
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
		$reciboDTO->setStrTipoPeticionamento('Novo');
		
		$objBD = new ReciboPeticionamentoBD($this->getObjInfraIBanco());
		$ret = $objBD->cadastrar( $reciboDTO );
		
		return $ret; 
	
    }
	
	//método utilizado para gerar recibo ao final do cadastramento de um processo de peticionamento de usuario externo
	protected function montarReciboControlado( $arrParams ){
		
		$reciboDTO = $this->cadastrar( $arrParams );
		
		//gerando documento recibo (nao assinado) dentro do processo do SEI
		$objInfraParametro = new InfraParametro($this->getObjInfraIBanco());
		
		$arrParametros = $arrParams[0]; //parametros adicionais fornecidos no formulario de peticionamento
		$objUnidadeDTO = $arrParams[1]; //UnidadeDTO da unidade geradora do processo
		$objProcedimentoDTO = $arrParams[2]; //ProcedimentoDTO para vincular o recibo ao processo correto
		$arrParticipantesParametro = $arrParams[3]; //array de ParticipanteDTO
		$arrParams[4] = $reciboDTO;
		
		//tentando simular sessao de usuario interno do SEI
		SessaoSEI::getInstance()->setNumIdUnidadeAtual( $objUnidadeDTO->getNumIdUnidade() );
		SessaoSEI::getInstance()->setNumIdUsuario( SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno() );
		
		$grauSigiloDocPrincipal = $arrParametros['grauSigiloDocPrincipal'];
		$hipoteseLegalDocPrincipal = $arrParametros['hipoteseLegalDocPrincipal']; 
				
		//TODO montar corretamente conteudo HTML final do recibo
		$htmlRecibo = $this->gerarHTMLConteudoDocRecibo( $arrParams );
		
		$protocoloRN = new ProtocoloPeticionamentoRN();
		
		$numeroDocumento = $protocoloRN->gerarNumeracaoDocumento();
		$idSerieRecibo = $objInfraParametro->getValor('ID_SERIE_RECIBO_MODULO_PETICIONAMENTO');
		
		//=============================================
		//MONTAGEM DO PROTOCOLODTO DO DOCUMENTO
		//=============================================
		
		$protocoloReciboDocumentoDTO = new ProtocoloDTO();
		
		$protocoloReciboDocumentoDTO->setDblIdProtocolo(null);
		$protocoloReciboDocumentoDTO->setStrDescricao( null );
		$protocoloReciboDocumentoDTO->setStrStaNivelAcessoLocal( ProtocoloRN::$NA_PUBLICO );
		$protocoloReciboDocumentoDTO->setStrProtocoloFormatado( $numeroDocumento );
		$protocoloReciboDocumentoDTO->setStrProtocoloFormatadoPesquisa( $numeroDocumento );
		$protocoloReciboDocumentoDTO->setNumIdUnidadeGeradora( $objUnidadeDTO->getNumIdUnidade() );
		$protocoloReciboDocumentoDTO->setNumIdUsuarioGerador( SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno() );
		$protocoloReciboDocumentoDTO->setStrStaProtocolo( ProtocoloRN::$TP_DOCUMENTO_GERADO );
		
		$protocoloReciboDocumentoDTO->setStrStaNivelAcessoLocal( ProtocoloRN::$NA_PUBLICO );
		$protocoloReciboDocumentoDTO->setNumIdHipoteseLegal( null );
		$protocoloReciboDocumentoDTO->setStrStaGrauSigilo('');
					
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
		
		$documentoReciboDTO->setStrSinBloqueado('N');			
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
	
    $html .= '<table width="90%" align="center" style="width: 90%" border="0">';
    $html .= '<tbody><tr>';
    $html .= '<td style="font-weight: bold; width: 280px;" width="280">Usuário Externo (signatário):</td>';
    $html .= '<td>' . $objUsuarioDTO->getStrNome() . '</td>';
    $html .= '</tr>';
    
    $html .= '<tr>';
    $html .= '<td style="font-weight: bold;">IP utilizado: </td>';
    $html .= '<td>' . $reciboDTO->getStrIpUsuario() .'</td>';
    $html .= '</tr>';
    
    $html .= '<tr>';
    $html .= '<td style="font-weight: bold;">Tipo de Peticionamento:</td>';
    $html .= '<td>Processo ' . $reciboDTO->getStrTipoPeticionamento() . '</td>';
    $html .= '</tr>';
    
    $html .= '<tr>';
    $html .= '<td style="font-weight: bold;">Data e horário (recebimento final pelo SEI):</td>';
    $html .= '<td>' . DataUtils::setFormat( $reciboDTO->getDthDataHoraRecebimentoFinal(),'dd/mm/yyyy hh:mm')  .  '</td>';
    $html .= '</tr>';
    
    $html .= '<tr>';
    $html .= '<td style="font-weight: bold;">Número do processo:</td>';
    $html .= '<td>' . $objProcedimentoDTO->getStrProtocoloProcedimentoFormatado() .  '</td>';
    $html .= '</tr>';
    
    $html .= '<tr>';
    $html .= '<td style="font-weight: bold;">Interessados:</td>';
    $html .= '<td></td>';
    $html .= '</tr>';
    
    $html .= '<tr>';
    $html .= '<td style="font-weight: bold;">Protocolos dos Documentos (Número SEI):</td>';
    $html .= '<td></td>';
    $html .= '</tr>';
    
    $anexoRN = new AnexoRN();
    $documentoRN = new DocumentoRN();
    
    if( isset($arrParams[0]['hdnDocPrincipal']) && $arrParams[0]['hdnDocPrincipal'] != ""  ){
      
      $html .= '<tr>';
      $html .= '<td style="font-weight: bold;">- Documento Principal:</td>';
      $html .= '<td></td>';
      $html .= '</tr>';
      
      //loop na lista de documentos principais
    	
      $arrAnexos = PaginaSEI::getInstance()->getArrItensTabelaDinamica( $arrParams[0]['hdnDocPrincipal'] );
      $arrObjAnexoDTO = array();
      	
      foreach($arrAnexos as $anexo){
      
      	$objAnexoDTO = new AnexoDTO();
      	$objAnexoDTO->retTodos(true);
      	//$objAnexoDTO->setNumIdAnexo( null );
      	$objAnexoDTO->setStrSinAtivo('S');
      	$objAnexoDTO->setStrNome($anexo[8]);
      	//$objAnexoDTO->setDthInclusao($anexo[1]);
      	//$objAnexoDTO->setNumTamanho($anexo[2]);
      	//$objAnexoDTO->setStrSiglaUsuario( $strSiglaUsuario );
      	//$objAnexoDTO->setStrSiglaUnidade( $idUnidade );
      	$objAnexoDTO->setNumIdUsuario(SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno());
      	
      	$objAnexoDTO = $anexoRN->consultarRN0736( $objAnexoDTO );
      	$strNome = $objAnexoDTO->getStrNome();
      	$strTipoDocumento = "";
      	$strNumeroSEI = $objAnexoDTO->getStrProtocoloFormatadoProtocolo();
      	
      	$documentoDTO = new DocumentoDTO();
      	$documentoDTO->retStrNomeSerie();
      	$documentoDTO->setDblIdDocumento( $objAnexoDTO->getDblIdProtocolo() );
      	$documentoDTO = $documentoRN->consultarRN0005( $documentoDTO );
      	
      	//concatenar tipo e complemento
      	$strNome = $documentoDTO->getStrNomeSerie() . ' - Complemento:';
      	
      	$html .= '<tr>';
      	$html .= '<td> &nbsp;&nbsp;&nbsp; - ' . $strNome . '</td>';
      	$html .= '<td>' . $strNumeroSEI . '</td>';
      	$html .= '</tr>';
      	
      	//$arrObjAnexoDTO[] = $objAnexoDTO;
      }
      
      //fim loop de documentos principais
    }
    	
    //ESSENCIAL
    
    if( isset($arrParams[0]['hdnDocEssencial']) && $arrParams[0]['hdnDocEssencial'] != ""  ){
    	
    	$html .= '<tr>';
    	$html .= '<td style="font-weight: bold;">- Documentos Essenciais:</td>';
    	$html .= '<td></td>';
    	$html .= '</tr>';
    	
    	$arrAnexos = PaginaSEI::getInstance()->getArrItensTabelaDinamica( $arrParams[0]['hdnDocEssencial'] );
    	$arrObjAnexoDTO = array();
    	
    	foreach($arrAnexos as $anexo){
    		
    		$objAnexoDTO = new AnexoDTO();
    		$objAnexoDTO->retTodos(true);
    		//$objAnexoDTO->setNumIdAnexo( null );
    		$objAnexoDTO->setStrSinAtivo('S');
    		$objAnexoDTO->getStrProtocoloFormatadoProtocolo();
    		$objAnexoDTO->setStrNome($anexo[10]);
    		//$objAnexoDTO->setDthInclusao($anexo[2]);
    		//$objAnexoDTO->setNumTamanho($anexo[4]);
    		//$objAnexoDTO->setStrSiglaUsuario( $strSiglaUsuario );
    		//$objAnexoDTO->setStrSiglaUnidade( $idUnidade );
    		//$objAnexoDTO->setNumIdUsuario(SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno());
    		$arrObjAnexoDTO[] = $objAnexoDTO;
    		
    		$strNome = $objAnexoDTO->getStrNome();
    		$strTipoDocumento = "";
    		$strNumeroSEI = $objAnexoDTO->getStrProtocoloFormatadoProtocolo();
    		 
    		$documentoDTO = new DocumentoDTO();
    		$documentoDTO->retStrNomeSerie();
    		$documentoDTO->setDblIdDocumento( $objAnexoDTO->getDblIdProtocolo() );
    		$documentoDTO = $documentoRN->consultarRN0005( $documentoDTO );
    		//concatenar tipo e complemento
    		$strNome = $documentoDTO->getStrNomeSerie() . ' - Complemento:';
    		
    		$html .= '<tr>';
    		$html .= '<td> &nbsp;&nbsp;&nbsp; - ' . $strNome . '</td>';
    		$html .= '<td>' . $strNumeroSEI . '</td>';
    		$html .= '</tr>';
    		 
    	
    	}
    	
    }
    
    //FIM ESSENCIAL
    
    //COMPLEMENTAR
    
    if( isset($arrParams[0]['hdnDocComplementar']) && $arrParams[0]['hdnDocComplementar'] != ""  ){
    	
    	$html .= '<tr>';
    	$html .= '<td style="font-weight: bold;">- Documentos Complementares:</td>';
    	$html .= '<td></td>';
    	$html .= '</tr>';
    	
    	$arrAnexos = PaginaSEI::getInstance()->getArrItensTabelaDinamica( $arrParams[0]['hdnDocComplementar'] );
    	$arrObjAnexoDTO = array();
    	 
    	foreach($arrAnexos as $anexo){
    
    		$objAnexoDTO = new AnexoDTO();
    		$objAnexoDTO->setNumIdAnexo( null );
    		$objAnexoDTO->setStrSinAtivo('S');
    		$objAnexoDTO->setStrNome($anexo[10]);
    		$objAnexoDTO->setDthInclusao($anexo[2]);
    		$objAnexoDTO->setNumTamanho($anexo[4]);
    		$objAnexoDTO->setStrSiglaUsuario( $strSiglaUsuario );
    		$objAnexoDTO->setStrSiglaUnidade( $idUnidade );
    		$objAnexoDTO->setNumIdUsuario(SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno());
    		$arrObjAnexoDTO[] = $objAnexoDTO;
    		
    		$strNome = $objAnexoDTO->getStrNome();
    		$strTipoDocumento = "";
    		$strNumeroSEI = $objAnexoDTO->getStrProtocoloFormatadoProtocolo();
    		 
    		$documentoDTO = new DocumentoDTO();
    		$documentoDTO->retStrNomeSerie();
    		$documentoDTO->setDblIdDocumento( $objAnexoDTO->getDblIdProtocolo() );
    		$documentoDTO = $documentoRN->consultarRN0005( $documentoDTO );
    		//concatenar tipo e complemento
    		$strNome = $documentoDTO->getStrNomeSerie() . ' - Complemento:';
    		
    		$html .= '<tr>';
    		$html .= '<td> &nbsp;&nbsp;&nbsp; - ' . $strNome . '</td>';
    		$html .= '<td>' . $strNumeroSEI . '</td>';
    		$html .= '</tr>';
    		 
    	}
    	 
    }
    
    //FIM COMPLEMENTAR
    
    /*
	$html .= '<tr>';
    $html .= '<td colspan="2" align="left"> 
    		  A existência deste Recibo e do processo e documentos acima indicados podem ser 
    		  conferidas na Página Eletrônica do(a) ' . $objOrgaoDTO->getStrDescricao() . ' 
    		</td>';
    
    $html .= '</tr>';
    */
    
    $html .= '</tbody></table>';
    
    $orgaoRN = new OrgaoRN();
    $objOrgaoDTO = new OrgaoDTO();
    $objOrgaoDTO->retTodos();
    $objOrgaoDTO->setNumIdOrgao( $objUnidadeDTO->getNumIdOrgao() );
    $objOrgaoDTO->setStrSinAtivo('S');
    $objOrgaoDTO = $orgaoRN->consultarRN1352( $objOrgaoDTO );
    
    $html .= '<p> A existência deste Recibo e do processo e documentos acima indicados podem ser 
    		  conferidas na Página Eletrônica do(a) ' . $objOrgaoDTO->getStrDescricao() . '</p>';
	
	return $html;
	
  }

}
?>