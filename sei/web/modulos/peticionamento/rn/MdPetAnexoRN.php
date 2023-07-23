<?
/**
* ANATEL
*
* 31/08/2016 - criado por marcelo.bezerra - CAST
*
*/

require_once dirname(__FILE__).'/../../../SEI.php';

class MdPetAnexoRN extends InfraRN {

	public function __construct(){
		parent::__construct();
	}

	protected function inicializarObjInfraIBanco(){
		return BancoSEI::getInstance();
	}

	public function obterDiretorio(AnexoDTO $objAnexoDTO){
		try{
			return ConfiguracaoSEI::getInstance()->getValor('SEI','RepositorioArquivos').'/'.substr($objAnexoDTO->getDthInclusao(),6,4).'/'.substr($objAnexoDTO->getDthInclusao(),3,2) .'/' .substr($objAnexoDTO->getDthInclusao(),0,2);
		}catch(Exception $e){
			throw new InfraException('Erro obtendo diret�rio do anexo.',$e);
		}
	}

	public function obterLocalizacao(AnexoDTO $objAnexoDTO){
		try{
			return $this->obterDiretorio($objAnexoDTO).'/'.$objAnexoDTO->getNumIdAnexo();
		}catch(Exception $e){
			throw new InfraException('Erro obtendo localiza��o do anexo.',$e);
		}
	}

	protected function gerarNomeArquivoTemporarioConectado($strSufixo = null){
		try{
			return BancoSEI::getInstance()->getValorSequencia('seq_upload').'_'.InfraUtil::formatarNomeArquivo(md5($strSufixo . '_' . uniqid(mt_rand())) . $strSufixo);
		}catch(Exception $e){
			throw new InfraException('Erro gerando nome de arquivo tempor�rio.',$e);
		}
	}

	protected function montarNomeUploadConectado(AnexoDTO $objAnexoDTO){
		try{
			return '['.$this->getObjInfraIBanco()->getValorSequencia('seq_upload').']'.InfraUtil::montarNomeArquivoUpload($objAnexoDTO->getStrSiglaUsuario(), time(), $objAnexoDTO->getStrNome());
		}catch(Exception $e){
			throw new InfraException('Erro montando nome de arquivo para upload.',$e);
		}
	}

	protected function cadastrarRN0172Controlado(AnexoDTO $objAnexoDTO) {
		
		try{
			
			//Regras de Negocio
			$objInfraException = new InfraException();

			$this->validarStrNomeRN0228($objAnexoDTO, $objInfraException);
			$this->validarProtocoloBaseConhecimento($objAnexoDTO, $objInfraException);
			$this->validarNumIdUnidadeRN0834($objAnexoDTO, $objInfraException);
			$this->validarNumIdUsuarioRN0866($objAnexoDTO, $objInfraException);
			$this->validarNumTamanhoRN0867($objAnexoDTO, $objInfraException);
			$this->validarDthInclusaoRN0868($objAnexoDTO, $objInfraException);
			$this->validarStrSinAtivoRN0886($objAnexoDTO, $objInfraException);
			
			$strNomeUpload = $objAnexoDTO->getStrNome();
			$strNomeUploadCompleto = DIR_SEI_TEMP.'/'.$strNomeUpload;

			if (!file_exists($strNomeUploadCompleto)){
				$objInfraException->lancarValidacao('Anexo '.$objAnexoDTO->getStrNome().' n�o encontrado.');
			}

			if (filesize($strNomeUploadCompleto)==0){
				$objInfraException->lancarValidacao('Anexo '.$objAnexoDTO->getStrNome().' vazio.');
			}

			$finfo = finfo_open(FILEINFO_MIME_TYPE);
			$strMime = finfo_file($finfo, $strNomeUploadCompleto);
			finfo_close($finfo);

			if (strpos($strMime,'text/x-php')!==false || strpos($strMime,'text/x-shellscript')!==false){
				$objInfraException->adicionarValidacao('Conte�do do anexo n�o permitido por restri��o de seguran�a.');
			}

			$objInfraException->lancarValidacoes();

			$objAnexoDTO->setStrHash(hash_file('md5',$strNomeUploadCompleto));
			 
			$objAnexoBD = new AnexoBD($this->getObjInfraIBanco());
			$ret = $objAnexoBD->cadastrar($objAnexoDTO);

			$strDiretorio = $this->obterDiretorio($objAnexoDTO);
			 
			if (is_dir($strDiretorio) === false){
				if (mkdir($strDiretorio,0755,true) === false){
					throw new InfraException('Erro criando diret�rio "' .$strDiretorio.'".');
				}
			}

			copy($strNomeUploadCompleto, $strDiretorio.'/'.$ret->getNumIdAnexo());

			if (!$objAnexoDTO->isSetStrSinExclusaoAutomatica() || $objAnexoDTO->getStrSinExclusaoAutomatica() == 'S' ) {
				unlink($strNomeUploadCompleto);
			}

			return $ret;
			//Auditoria

		}catch(Exception $e){
			throw new InfraException('Erro cadastrando Anexo.',$e);
		}
	}

	protected function excluirRN0226Controlado($arrObjAnexoDTO){
		try {
			//Valida Permissao
			SessaoSEI::getInstance()->validarAuditarPermissao('anexo_excluir',__METHOD__,$arrObjAnexoDTO);

			//Regras de Negocio
			//$objInfraException = new InfraException();

			//$objInfraException->lancarValidacoes();

			$objAnexoBD = new AnexoBD($this->getObjInfraIBanco());
			for($i=0;$i<count($arrObjAnexoDTO);$i++){
				$objAnexoBD->excluir($arrObjAnexoDTO[$i]);
			}

			//Auditoria

		}catch(Exception $e){
			throw new InfraException('Erro excluindo Anexo.',$e);
		}
	}

	protected function consultarRN0736Conectado(AnexoDTO $objAnexoDTO){
		try {

			//Valida Permissao
			SessaoSEI::getInstance()->validarAuditarPermissao('anexo_consultar',__METHOD__,$objAnexoDTO);

			//Regras de Negocio
			//$objInfraException = new InfraException();

			//$objInfraException->lancarValidacoes();

			$objAnexoBD = new AnexoBD($this->getObjInfraIBanco());
			$ret = $objAnexoBD->consultar($objAnexoDTO);

			//Auditoria

			return $ret;
		}catch(Exception $e){
			throw new InfraException('Erro consultando Anexo.',$e);
		}
	}

	protected function listarRN0218Conectado(AnexoDTO $objAnexoDTO) {
		try {

			//Valida Permissao
			SessaoSEI::getInstance()->validarAuditarPermissao('anexo_listar',__METHOD__,$objAnexoDTO);

			//Regras de Negocio
			//$objInfraException = new InfraException();

			//$objInfraException->lancarValidacoes();

			$objAnexoBD = new AnexoBD($this->getObjInfraIBanco());
			$ret = $objAnexoBD->listar($objAnexoDTO);

			//Auditoria

			return $ret;

		}catch(Exception $e){
			throw new InfraException('Erro listando Anexos.',$e);
		}
	}

	protected function contarRN0734Conectado(AnexoDTO $objAnexoDTO){
		try {

			//Valida Permissao
			SessaoSEI::getInstance()->validarAuditarPermissao('anexo_listar',__METHOD__,$objAnexoDTO);

			//Regras de Negocio
			//$objInfraException = new InfraException();

			//$objInfraException->lancarValidacoes();

			$objAnexoBD = new AnexoBD($this->getObjInfraIBanco());
			$ret = $objAnexoBD->contar($objAnexoDTO);

			//Auditoria

			return $ret;
		}catch(Exception $e){
			throw new InfraException('Erro contando Anexos.',$e);
		}
	}

	protected function desativarRN0745Controlado($arrObjAnexoDTO){
		try {

			//Valida Permissao
			SessaoSEI::getInstance()->validarAuditarPermissao('anexo_desativar',__METHOD__,$arrObjAnexoDTO);

			//Regras de Negocio
			//$objInfraException = new InfraException();

			//$objInfraException->lancarValidacoes();

			$objAnexoBD = new AnexoBD($this->getObjInfraIBanco());
			for($i=0;$i<count($arrObjAnexoDTO);$i++){
				$objAnexoBD->desativar($arrObjAnexoDTO[$i]);
			}

			//Auditoria

		}catch(Exception $e){
			throw new InfraException('Erro desativando Anexo.',$e);
		}
	}

	protected function reativarRN0746Controlado($arrObjAnexoDTO){
		try {

			//Valida Permissao
			SessaoSEI::getInstance()->validarAuditarPermissao('anexo_reativar',__METHOD__,$arrObjAnexoDTO);

			//Regras de Negocio
			//$objInfraException = new InfraException();

			//$objInfraException->lancarValidacoes();

			$objAnexoBD = new AnexoBD($this->getObjInfraIBanco());
			for($i=0;$i<count($arrObjAnexoDTO);$i++){
				$objAnexoBD->reativar($arrObjAnexoDTO[$i]);
			}

			//Auditoria

		}catch(Exception $e){
			throw new InfraException('Erro reativando Anexo.',$e);
		}
	}

	private function validarStrNomeRN0228(AnexoDTO $objAnexoDTO, InfraException $objInfraException){
		
		if (InfraString::isBolVazia($objAnexoDTO->getStrNome())){
			$objInfraException->adicionarValidacao('Nome do anexo n�o informado.');
		}else{

			$objAnexoDTO->setStrNome(trim($objAnexoDTO->getStrNome()));

			if (strpos($objAnexoDTO->getStrNome(),'&#')!==false){
				$objInfraException->adicionarValidacao('Nome do anexo possui caracteres especiais.');
			}

			if (strlen($objAnexoDTO->getStrNome())>255){
				$objInfraException->adicionarValidacao('Nome do anexo possui tamanho superior a 255 caracteres.');
			}

			$objInfraParametro = new InfraParametro(BancoSEI::getInstance());

			if ($objInfraParametro->getValor('SEI_HABILITAR_VALIDACAO_EXTENSAO_ARQUIVOS')=='1' && (!$objAnexoDTO->isSetStrSinDuplicando() || $objAnexoDTO->getStrSinDuplicando()=='N')){

				$arrStrNome = explode(".", $objAnexoDTO->getStrNome());

				if (count($arrStrNome) < 2){

					$objInfraException->adicionarValidacao('Nome do arquivo n�o possui extens�o.');

				}else {

					$strExtensao = str_replace(' ', '', InfraString::transformarCaixaBaixa($arrStrNome[count($arrStrNome) - 1]));

					$objArquivoExtensaoDTO = new ArquivoExtensaoDTO();
					$objArquivoExtensaoDTO->setStrExtensao($strExtensao);

					$objArquivoExtensaoRN = new ArquivoExtensaoRN();
					if ($objArquivoExtensaoRN->contar($objArquivoExtensaoDTO) == 0) {
						$objInfraException->adicionarValidacao('Tipo do arquivo ".' . $strExtensao . '" n�o autorizado.');
					}else if (in_array($strExtensao, array('php', 'php3', 'php4', 'phtml', 'sh', 'cgi'))) {
						$objInfraException->adicionarValidacao('Extens�o do arquivo n�o permitida por restri��o de seguran�a.');
					}
				}
			}
		}
	}


	private function validarProtocoloBaseConhecimento(AnexoDTO $objAnexoDTO, InfraException $objInfraException){
		if (!$objAnexoDTO->isSetDblIdProtocolo() && !$objAnexoDTO->isSetNumIdBaseConhecimento()){
			$objInfraException->adicionarValidacao('Protocolo ou Base de Conhecimento do anexo n�o informado.');
		}
	}

	private function validarNumIdUnidadeRN0834(AnexoDTO $objAnexoDTO, InfraException $objInfraException){
		if (InfraString::isBolVazia($objAnexoDTO->getNumIdUnidade())){
			$objInfraException->adicionarValidacao('Unidade do anexo n�o informada.');
		}
	}

	private function validarNumIdUsuarioRN0866(AnexoDTO $objAnexoDTO, InfraException $objInfraException){
		if (InfraString::isBolVazia($objAnexoDTO->getNumIdUsuario())){
			$objInfraException->adicionarValidacao('Usu�rio do anexo n�o informado.');
		}
	}

	private function validarNumTamanhoRN0867(AnexoDTO $objAnexoDTO, InfraException $objInfraException){
		if (InfraString::isBolVazia($objAnexoDTO->getNumTamanho())){
			$objInfraException->adicionarValidacao('Tamanho do anexo n�o informado.');
		}

		$objInfraParametro = new InfraParametro(BancoSEI::getInstance());

		$numTamDocExterno = $objInfraParametro->getValor('SEI_TAM_MB_DOC_EXTERNO');

		if($objAnexoDTO->getNumTamanho() > ($numTamDocExterno*1024*1024)){
			$objInfraException->adicionarValidacao('Documentos externos n�o podem ultrapassar '.$numTamDocExterno.'Mb, o tamanho do anexo � '.round($objAnexoDTO->getNumTamanho()/(1024*1024),1).'Mb.');
		}
	}

	private function validarDthInclusaoRN0868(AnexoDTO $objAnexoDTO, InfraException $objInfraException){
		if (InfraString::isBolVazia($objAnexoDTO->getDthInclusao())){
			$objInfraException->adicionarValidacao('Data de inclus�o do anexo n�o informada.');
		}else{
			if (!InfraData::validarDataHora($objAnexoDTO->getDthInclusao())){
				$objInfraException->adicionarValidacao('Data de inclus�o do anexo inv�lida.');
			}
		}
	}

	private function validarStrSinAtivoRN0886(AnexoDTO $objAnexoDTO, InfraException $objInfraException){
		if (InfraString::isBolVazia($objAnexoDTO->getStrSinAtivo())){
			$objInfraException->adicionarValidacao('Sinalizador de Exclus�o L�gica n�o informado.');
		}else{
			if (!InfraUtil::isBolSinalizadorValido($objAnexoDTO->getStrSinAtivo())){
				$objInfraException->adicionarValidacao('Sinalizador de Exclus�o L�gica inv�lido.');
			}
		}
	}

}
?>