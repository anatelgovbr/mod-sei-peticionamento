<?
/**
* ANATEL
*
* 07/04/2016 - criado por marcelo.bezerra - CAST
*
*/

require_once dirname(__FILE__).'/../../../SEI.php';

class MdPetIndisponibilidadeRN extends InfraRN {
	
	
	public static $SIM = 'S';
	public static $NAO = 'N';
	
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
	 * @author Jaqueline Mendes <jaqueline.mendes@cast.com.br>
	 * @param
	 *        	$objMdPetIndisponibilidadeDTO
	 * @return mixed
	 */
	protected function listarConectado(MdPetIndisponibilidadeDTO $objMdPetIndisponibilidadeDTO) {
	
		try {
			//SessaoSEI::getInstance()->validarAuditarPermissao('conduta_litigioso_listar',__METHOD__,$objCondutaLitigiosoDTO);
	
			//Regras de Negocio
			//$objInfraException = new InfraException();
	
			//$objInfraException->lancarValidacoes();
			$objMdPetIndisponibilidadeBD = new MdPetIndisponibilidadeBD($this->getObjInfraIBanco());
			$ret = $objMdPetIndisponibilidadeBD->listar($objMdPetIndisponibilidadeDTO);
				
			return $ret;
		} catch (Exception $e) {
			throw new InfraException ('Erro listando Indisponibilidade Peticionamento.', $e);
		}
	}
	
	public function listarValoresProrrogacao(){
		
		try{
		$objArrMdPetProrrogacaoAutomaticaPrazosDTO = array();
		
		$objMdPetProrrogacaoAutomaticaPrazosDTO = new MdPetProrrogacaoAutomaticaPrazosDTO();
		$objMdPetProrrogacaoAutomaticaPrazosDTO->setStrSinProrrogacao(self::$SIM);
		$objMdPetProrrogacaoAutomaticaPrazosDTO->setStrDescricao('Sim');
		$objArrMdPetProrrogacaoAutomaticaPrazosDTO[] = $objMdPetProrrogacaoAutomaticaPrazosDTO;
		
		$objMdPetProrrogacaoAutomaticaPrazosDTO = new MdPetProrrogacaoAutomaticaPrazosDTO();
		$objMdPetProrrogacaoAutomaticaPrazosDTO->setStrSinProrrogacao(self::$NAO);
		$objMdPetProrrogacaoAutomaticaPrazosDTO->setStrDescricao('Não');
		$objArrMdPetProrrogacaoAutomaticaPrazosDTO[] = $objMdPetProrrogacaoAutomaticaPrazosDTO;
		
		return $objArrMdPetProrrogacaoAutomaticaPrazosDTO;
		}catch(Exception $e){
			throw new InfraException('Erro listando valores de Prorrogacao.',$e);
		}
	}
	
	
/**
	 * Short description of method consultarConectado
	 *
	 * @access protected
	 * @author Jaqueline Mendes <jaqueline.mendes@cast.com.br>
	 * @param  $objMdPetIndisponibilidadeDTO
	 * @return mixed
	 */
	protected function consultarConectado(MdPetIndisponibilidadeDTO $objMdPetIndisponibilidadeDTO) {
		try {
			
			// Valida Permissao			
			$objMdPetIndisponibilidadeBD = new MdPetIndisponibilidadeBD($this->getObjInfraIBanco());
			$ret = $objMdPetIndisponibilidadeBD->consultar($objMdPetIndisponibilidadeDTO);
			
			return $ret;
		} catch ( Exception $e ) {
			throw new InfraException('Erro consultando Indisponibilidade Peticionamento.', $e);
		}
	}
	
	/**
	 * Short description of method alterarControlado
	 *
	 * @access protected
	 * @author Jaqueline Mendes <jaqueline.mendes@cast.com.br>
	 * @param  $objMdPetIndisponibilidadeDTO
	 * @return mixed
	 */
	protected function alterarControlado(MdPetIndisponibilidadeDTO $objMdPetIndisponibilidadeDTO){
		try {
				
			// Valida Permissao
			SessaoSEI::getInstance()->validarAuditarPermissao ('md_pet_indisponibilidade_alterar', __METHOD__, $objMdPetIndisponibilidadeDTO);


			// Regras de Negocio
			$objInfraException = new InfraException ();
			
			$this->_validarDuplicidade($objInfraException, $objMdPetIndisponibilidadeDTO);
			$this->_validarTxtResumoIndisponibilidade($objInfraException, $objMdPetIndisponibilidadeDTO);
			
			$objInfraException->lancarValidacoes();
		
			$objMdPetIndisponibilidadeBD = new MdPetIndisponibilidadeBD($this->getObjInfraIBanco());
			$objMdPetIndisponibilidadeBD->alterar($objMdPetIndisponibilidadeDTO);
		
			$this->_controlarAnexos($objMdPetIndisponibilidadeDTO);
				
			// Auditoria
		} catch ( Exception $e ) {
			throw new InfraException ('Erro alterando Indisponibilidade Peticionamento, ', $e);
		}
	}
	
	
	
	/**
	 * Short description of method cadastrarControlado
	 *
	 * @access protected
	 * @author Jaqueline Mendes <jaqueline.mendes@cast.com.br>
	 * @param  $objMdPetIndisponibilidadeDTO
	 * @return mixed
	 */
	protected function cadastrarControlado(MdPetIndisponibilidadeDTO $objMdPetIndisponibilidadeDTO) {
		try {
			// Valida Permissao
			SessaoSEI::getInstance ()->validarAuditarPermissao ('md_pet_indisponibilidade_cadastrar', __METHOD__, $objMdPetIndisponibilidadeDTO );


			// Regras de Negocio
			$objInfraException = new InfraException();
			
			$this->_validarDuplicidade($objInfraException, $objMdPetIndisponibilidadeDTO);
			$this->_validarTxtResumoIndisponibilidade($objInfraException, $objMdPetIndisponibilidadeDTO);
				
			$objInfraException->lancarValidacoes();
	
			//Cadastrar Indisponibilidade
			$objMdPetIndisponibilidadeBD = new MdPetIndisponibilidadeBD($this->getObjInfraIBanco());
			$objMdPetIndisponibilidadeDTO->setStrSinAtivo('S');
			
			$objRetorno = $objMdPetIndisponibilidadeBD->cadastrar($objMdPetIndisponibilidadeDTO);
	
			$this->_cadastrarAnexosIndisponibilidadePeticionamento($objMdPetIndisponibilidadeDTO, $objRetorno);
			
			return $objRetorno;
		} catch ( Exception $e ) {
			throw new InfraException ('Erro cadastrando Tamanho de Arquivo Peticionamento.', $e );
		}
	}
	
	
	private function _validarTxtResumoIndisponibilidade($objInfraException, $objMdPetIndisponibilidadeDTO){
		if (InfraString::isBolVazia ($objMdPetIndisponibilidadeDTO->getStrResumoIndisponibilidade())) {
			$objInfraException->adicionarValidacao('Resumo da Indisponibilidade não informada.');
		}
		if (trim ( $objMdPetIndisponibilidadeDTO->getStrResumoIndisponibilidade () ) != '')
		{
			if (strlen ( $objMdPetIndisponibilidadeDTO->getStrResumoIndisponibilidade () ) > 250) {
				$objInfraException->adicionarValidacao('Resumo da Indisponibilidade possui tamanho superior a 250 caracteres.');
			}
		}
	}
	
	
	
	private function _validarDuplicidade($objInfraException, $objMdPetIndisponibilidadeDTO){
		// VALIDA DUPLICAÇÃO
		// VALIDACAO A SER EXECUTADA NA INSERÇAO DE NOVOS REGISTROS
		$objMdPetIndisponibilidadeDTO2 = new MdPetIndisponibilidadeDTO();
		$objMdPetIndisponibilidadeDTO2->setDthDataInicio($objMdPetIndisponibilidadeDTO->getDthDataInicio());
		$objMdPetIndisponibilidadeDTO2->setDthDataFim($objMdPetIndisponibilidadeDTO->getDthDataFim());
		
		$objMdPetIndisponibilidadeBD = new MdPetIndisponibilidadeBD($this->getObjInfraIBanco());
		
		if (!is_numeric($objMdPetIndisponibilidadeDTO->getNumIdIndisponibilidade())) {
				
			$ret = $objMdPetIndisponibilidadeBD->contar($objMdPetIndisponibilidadeDTO2);
				
			if ($ret > 0) {
				$objInfraException->adicionarValidacao ('Já existe o período de indisponibilidade (Início/Fim) cadastrado.');
			} // VALIDACAO A SER EXECUTADA QUANDO É FEITO UPDATE DE REGISTROS
				
		} else {
				
			$dtoValidacao = new MdPetIndisponibilidadeDTO();
			$dtoValidacao->setDthDataInicio($objMdPetIndisponibilidadeDTO->getDthDataInicio(), InfraDTO::$OPER_IGUAL);
			$dtoValidacao->setDthDataFim($objMdPetIndisponibilidadeDTO->getDthDataFim(), InfraDTO::$OPER_IGUAL);
			$dtoValidacao->setNumIdIndisponibilidade( $objMdPetIndisponibilidadeDTO->getNumIdIndisponibilidade(), InfraDTO::$OPER_DIFERENTE );
				
			$retDuplicidade = $objMdPetIndisponibilidadeBD->contar( $dtoValidacao );
				
			if ($retDuplicidade > 0) {
				$objInfraException->adicionarValidacao('Já existe o período de indisponibilidade (Início/Fim) cadastrado.');
			}
		}
		
	}
	
	private function _cadastrarAnexosIndisponibilidadePeticionamento($objMdPetIndisponibilidadeDTO, $objRetorno){
		// Cadastra os anexos da Indisponibilidade
		if (count($objMdPetIndisponibilidadeDTO->getArrObjAnexoDTO()) > 0){
			$objMdPetIndisponibilidadeAnexoRN = new MdPetIndisponibilidadeAnexoRN();
			$arrAnexos = $objMdPetIndisponibilidadeDTO->getArrObjAnexoDTO();
		
			for($i=0;$i<count($arrAnexos);$i++){
				$arrAnexos[$i]->setNumIdIndisponibilidade($objRetorno->getNumIdIndisponibilidade());
				$arrAnexos[$i]->setNumIdUsuario(SessaoSEI::getInstance()->getNumIdUsuario());
				$arrAnexos[$i]->setNumIdUnidade(SessaoSEI::getInstance()->getNumIdUnidadeAtual());
				$arrAnexos[$i]->setStrSinAtivo('S');
				$objAnexoDTO = $objMdPetIndisponibilidadeAnexoRN->cadastrar($arrAnexos[$i]);
			}
		}
	}
	
	private function _controlarAnexos(MdPetIndisponibilidadeDTO $objMdPetIndisponibilidadeDTO){
		
		if ($objMdPetIndisponibilidadeDTO->isSetArrObjAnexoDTO()){
			$objMdPetIndisponibilidadeAnexoRN = new MdPetIndisponibilidadeAnexoRN();
			
			$objMdPetIndisponibilidadeAnexoDTO = new MdPetIndisponibilidadeAnexoDTO();
			$objMdPetIndisponibilidadeAnexoDTO->retTodos();
			
			$objMdPetIndisponibilidadeAnexoDTO->setNumIdAnexoPeticionamento($objMdPetIndisponibilidadeDTO->getNumIdIndisponibilidade());
			$arrAnexosAntigos = $objMdPetIndisponibilidadeAnexoRN->listar($objMdPetIndisponibilidadeAnexoDTO);
			
			$arrAnexosNovos = $objMdPetIndisponibilidadeDTO->getArrObjAnexoDTO();
			 
			$arrRemocao = array();
			foreach($arrAnexosAntigos as $anexoAntigo){
				$flagRemover = true;
				foreach($arrAnexosNovos as $anexoNovo){
					if ($anexoAntigo->getNumIdAnexoPeticionamento()==$anexoNovo->getNumIdAnexoPeticionamento()){
						$flagRemover = false;
						break;
					}
				}
				if ($flagRemover){
					$arrRemocao[] = $anexoAntigo;
				}
			}
			 
			foreach($arrRemocao as $anexoRemover){
				if ($anexoRemover->getNumIdUnidade()<>SessaoSEI::getInstance()->getNumIdUnidadeAtual()){
					$objUnidadeRN = new UnidadeRN();
					$objUnidadeDTO = new UnidadeDTO();
					$objUnidadeDTO->retStrSigla();
					$objUnidadeDTO->setNumIdUnidade($anexoRemover->getNumIdUnidade());
					$objUnidadeDTO = $objUnidadeRN->consultarRN0125($objUnidadeDTO);
					$objInfraException->adicionarValidacao('O anexo "'.$anexoRemover->getStrNome().'" não pode ser excluído porque foi adicionado por outra unidade ('.$objUnidadeDTO->getStrSigla().').');
				}
			}
			
			$objMdPetIndisponibilidadeAnexoRN->excluir($arrRemocao);
			 
			
			foreach($arrAnexosNovos as $anexoNovo){
				if (!is_numeric($anexoNovo->getNumIdAnexoPeticionamento())){
					$anexoNovo->setNumIdIndisponibilidade($objMdPetIndisponibilidadeDTO->getNumIdIndisponibilidade());
					$anexoNovo->setNumIdUnidade(SessaoSEI::getInstance()->getNumIdUnidadeAtual());
					$anexoNovo->setStrSinAtivo('S');
					$objMdPetIndisponibilidadeAnexoRN->cadastrar($anexoNovo);
				}
			}
		}
	}
		
		
		/**
		 * Short description of method desativarControlado
		 *
		 * @access protected
		 * @author Jaqueline Mendes <jaqueline.mendes@cast.com.br>
		 * @param  $arrMdPetIndisponibilidadeDTO
		 * @return void
		 */
		protected function desativarControlado($arrMdPetIndisponibilidadeDTO) {
		
			try {

				SessaoSEI::getInstance ()->validarAuditarPermissao('md_pet_indisponibilidade_desativar', __METHOD__ ,$arrMdPetIndisponibilidadeDTO);

				$objMdPetIndisponibilidadeBD = new MdPetIndisponibilidadeBD($this->getObjInfraIBanco());
					
				for($i = 0; $i < count($arrMdPetIndisponibilidadeDTO); $i ++) {
					$objMdPetIndisponibilidadeBD->desativar($arrMdPetIndisponibilidadeDTO[$i]);
				}
					
			} catch(Exception $e) {
				throw new InfraException ('Erro desativando Indisponibilidade Peticionamento.', $e );
			}
		}
		
		
		/**
		 * Short description of method reativarControlado
		 *
		 * @access protected
		 * @author Jaqueline Mendes <jaqueline.mendes@cast.com.br>
		 * @param  $arrMdPetIndisponibilidadeDTO
		 * @return void
		 */
		protected function reativarControlado($arrMdPetIndisponibilidadeDTO) {
		
			try {

				SessaoSEI::getInstance ()->validarAuditarPermissao('md_pet_indisponibilidade_reativar', __METHOD__,$arrMdPetIndisponibilidadeDTO);

				$objMdPetIndisponibilidadeBD = new MdPetIndisponibilidadeBD($this->getObjInfraIBanco());
					
				for($i = 0; $i < count($arrMdPetIndisponibilidadeDTO); $i ++) {
					$objMdPetIndisponibilidadeBD->reativar($arrMdPetIndisponibilidadeDTO[$i]);
				}
					
			} catch(Exception $e) {
				throw new InfraException ('Erro reativando Indisponibilidade Peticionamento.', $e );
			}
		}
		
		
		/**
		 * Short description of method excluirControlado
		 *
		 * @access protected
		 * @author Jaqueline Mendes <jaqueline.mendes@cast.com.br>
		 * @param  $arrMdPetIndisponibilidadeDTO
		 * @return void
		 */
		protected function excluirControlado($arrMdPetIndisponibilidadeDTO) {
		
			try {

				SessaoSEI::getInstance ()->validarAuditarPermissao('md_pet_indisponibilidade_excluir', __METHOD__,$arrMdPetIndisponibilidadeDTO);

				$objMdPetIndisponibilidadeBD = new MdPetIndisponibilidadeBD($this->getObjInfraIBanco());
				$objMdPetIndisponibilidadeAnexoRN = new MdPetIndisponibilidadeAnexoRN();
				
				for($i = 0; $i < count($arrMdPetIndisponibilidadeDTO); $i ++) {
					
					//Excluindo anexos relacionados
					$objMdPetIndisponibilidadeAnexoDTO = new MdPetIndisponibilidadeAnexoDTO();
					$objMdPetIndisponibilidadeAnexoDTO->retTodos();
					$objMdPetIndisponibilidadeAnexoDTO->setNumIdIndisponibilidade($arrMdPetIndisponibilidadeDTO[$i]->getNumIdIndisponibilidade(), InfraDTO::$OPER_IGUAL);
					$arrObjMdPetIndisponibilidadeAnexoDTO = $objMdPetIndisponibilidadeAnexoRN->listar($objMdPetIndisponibilidadeAnexoDTO);
				    					
					$objMdPetIndisponibilidadeAnexoRN->excluir($arrObjMdPetIndisponibilidadeAnexoDTO);
				    
				    //Excluindo Indisponibilidade
					$objMdPetIndisponibilidadeBD->excluir($arrMdPetIndisponibilidadeDTO[$i]);
				}
					
			} catch(Exception $e) {
				throw new InfraException ('Erro excluindo Indisponibilidade Peticionamento.', $e );
			}
		}
		
		//metodo customizado de upload visando permitir download posterior do arquivo
		public function processarUploadComRetornoDoNomeReal($strCampoArquivo, $strDirUpload, $bolArquivoTemporarioIdentificado = true){
			
			$ret = '';
			try{
		
				$_FILES[$strCampoArquivo]["name"] = str_replace(chr(0), '', $_FILES[$strCampoArquivo]["name"]);
		
				$arrStrNome = explode('.', $_FILES[$strCampoArquivo]["name"]);
		
				if (count($arrStrNome) < 2){
					$ret = 'ERRO#Nome do arquivo não possui extensão.';
				}else{
					if (in_array(str_replace(' ','',InfraString::transformarCaixaBaixa($arrStrNome[count($arrStrNome)-1])), array('php', 'php3', 'php4', 'phtml', 'sh' ,'cgi'))){
						$ret = 'ERRO#Extensão de arquivo não permitida.';
					}else{
		
						if (!isset($_FILES[$strCampoArquivo])){
							$ret = 'ERRO#Campo de arquivo "'.$strCampoArquivo.'" não foi enviado.';
						}else{
		
							if ($_FILES[$strCampoArquivo]["error"] != UPLOAD_ERR_OK){
		
								switch($_FILES[$strCampoArquivo]["error"]){
									
									case UPLOAD_ERR_INI_SIZE:
										$ret = 'ERRO#Tamanho do arquivo "'.$_FILES[$strCampoArquivo]["name"].'" excedeu o limite de '.ini_get('upload_max_filesize').'b permitido pelo servidor.';
										break;
		
									case UPLOAD_ERR_FORM_SIZE:
										$ret = 'ERRO#Tamanho do arquivo "'.$_FILES[$strCampoArquivo]["name"].'" excedeu o limite de '.$_POST['MAX_FILE_SIZE'].' bytes permitido pelo navegador.';
										break;
		
									case UPLOAD_ERR_PARTIAL:
										$ret = 'ERRO#Apenas uma parte do arquivo foi transferida.';
										break;
		
									case UPLOAD_ERR_NO_FILE:
										$ret = 'ERRO#Arquivo não foi transferido.';
										break;
		
									case UPLOAD_ERR_NO_TMP_DIR:
										$ret = 'ERRO#Diretório temporário para transferência não encontrado.';
										break;
		
									case UPLOAD_ERR_CANT_WRITE:
										$ret = 'ERRO#Erro gravando dados no servidor.';
										break;
		
									case UPLOAD_ERR_EXTENSION:
										$ret = 'ERRO#Transferência interrompida.';
										break;
		
									default:
										$ret = 'ERRO#Erro desconhecido tranferindo arquivo ['.$_FILES[$strCampoArquivo]["error"].'].';
										break;
								}
		
							}else {
		
								$strMime = null;
		
								if (function_exists(finfo_open)) {
									$finfo = finfo_open(FILEINFO_MIME_TYPE);
									$strMime = finfo_file($finfo, $_FILES[$strCampoArquivo]["tmp_name"]);
									finfo_close($finfo);
								}
		
								if ($strMime != null && strpos($strMime, 'text/x-php') !== false || strpos($strMime, 'text/x-shellscript') !== false) {
									$ret = 'ERRO#Tipo de arquivo não permitido.';
								}else{
		
									if (PaginaSEI::getInstance()->getObjInfraSessao() !== null) {
										$strUsuario = PaginaSEI::getInstance()->getObjInfraSessao()->getStrSiglaUsuario();
									} else {
										$strUsuario = 'anonimo';
									}
		
									$numTimestamp = time();
		
									if ($bolArquivoTemporarioIdentificado) {
										//[usuario][ddmmaaaa-hhmmss]-nomearquivo
										$strArquivo = InfraUtil::montarNomeArquivoUpload($strUsuario, $numTimestamp, $_FILES[$strCampoArquivo]["name"]);
									} else {
										$strArquivo = md5($strUsuario . mt_rand() . $numTimestamp . mt_rand() . $_FILES[$strCampoArquivo]["name"] . uniqid(mt_rand(), true));
									}
		
									if (file_exists($strDirUpload . '/' . $strArquivo)) {
										$ret = 'ERRO#Arquivo "' . $strArquivo . '" já existe no diretório de upload.';
									} else {
										try {
											//se der certo retorna o nome real do arquivo gerado
											if (!move_uploaded_file($_FILES[$strCampoArquivo]["tmp_name"], $strDirUpload . '/' . $strArquivo)) {
												$ret = 'ERRO#Erro movendo arquivo para o diretório de upload.';
											} else {
												$ret = $strDirUpload . '/' . $strArquivo;
												//$ret .= $strArquivo . '#';
												//$ret .= $_FILES[$strCampoArquivo]["name"] . "#";
												//$ret .= $_FILES[$strCampoArquivo]["type"] . '#';
												//$ret .= $_FILES[$strCampoArquivo]["size"] . "#";
												//$ret .= date('d/m/Y H:i:s', $numTimestamp);
											}
		
										} catch (Exception $e) {
											if (strpos(strtoupper($e->__toString()), 'PERMISSION DENIED') !== false) {
												$ret = 'ERRO#Permissão negada tentando mover o arquivo para o diretório de upload.';
											}
											throw $e;
										}
									}
								}
							}
						}
					}
				}
			}catch(Exception $e){
				$ret = 'ERRO#'.$e->__toString();
			}
				    
			if (substr($ret,0,5)=='ERRO#' && PaginaSEI::getInstance()->getObjInfraLog() instanceof InfraLog){
		
				$strTextoLog = '';
				
				if (PaginaSEI::getInstance()->getObjInfraSessao()!==null){
					
					if ( PaginaSEI::getInstance()->getObjInfraSessao()->getStrSiglaUsuario()!==null){
						
						$strTextoLog .= "Usuário: ". PaginaSEI::getInstance()->getObjInfraSessao()->getStrSiglaUsuario();
		
						if ( PaginaSEI::getInstance()->getObjInfraSessao()->getStrSiglaOrgaoUsuario()!==null){
							$strTextoLog .= '/'. PaginaSEI::getInstance()->getObjInfraSessao()->getStrSiglaOrgaoUsuario();
						}
					}
				}
		
				$strTextoLog .= "\nServidor: ". $_SERVER['SERVER_NAME'] . " (".$_SERVER['SERVER_ADDR'].")";
				$strTextoLog .= "\nErro: ".substr($ret,5);
				$strTextoLog .= "\nNavegador: ". $_SERVER['HTTP_USER_AGENT'];
				
				if (is_array($_GET)){
					$strTextoLog .= "\nGET:\n".print_r($_GET,true);
				}
		
				if (is_array($_FILES)) {
					$strTextoLog .= "\nFILES:\n" . print_r($_FILES, true);
				}
		
				try{
					PaginaSEI::getInstance()->getObjInfraLog()->gravar($strTextoLog);
				}catch(Exception $e){
					//Ignora, erro mais provavel queda da conexao com o banco
				}
			} 
		
			echo $ret;
			
		}
	
}
?>