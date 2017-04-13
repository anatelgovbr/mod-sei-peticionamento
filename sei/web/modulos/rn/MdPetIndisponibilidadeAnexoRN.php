  <?
/**
* ANATEL
*
* 29/04/2016 - criado por marcelo.bezerra - CAST
*
*/

require_once dirname(__FILE__).'/../../../SEI.php';

class MdPetIndisponibilidadeAnexoRN extends InfraRN {
	
	
	public function __construct() {
		parent::__construct ();
	}
	
	protected function inicializarObjInfraIBanco() {
		return BancoSEI::getInstance ();
	}
	
	
	protected function cadastrarControlado(MdPetIndisponibilidadeAnexoDTO $objMdPetIndisponibilidadeAnexoDTO) {
		
		try{
	
			//Valida Permissao
		//	SessaoSEI::getInstance()->validarAuditarPermissao('anexo_cadastrar',__METHOD__,$objIndisponibilidadeAnexoDTO);
	
			//Regras de Negocio
			$objInfraException = new InfraException();
			$strNomeUpload = $objMdPetIndisponibilidadeAnexoDTO->getNumIdAnexoPeticionamento();
	

			$strNomeUploadCompleto = DIR_SEI_TEMP.'/'.$strNomeUpload;
			if (!file_exists($strNomeUploadCompleto)){
				$objInfraException->lancarValidacao('Anexo '.$objMdPetIndisponibilidadeAnexoDTO->getStrNome().' não encontrado.');
			}
	
			if (filesize($strNomeUploadCompleto)==0){
				$objInfraException->lancarValidacao('Anexo '.$objMdPetIndisponibilidadeAnexoDTO->getStrNome().' vazio.');
			}
	
			$finfo = finfo_open(FILEINFO_MIME_TYPE);
			$strMime = finfo_file($finfo, $strNomeUploadCompleto);
			finfo_close($finfo);
	
			if (strpos($strMime,'text/x-php')!==false || strpos($strMime,'text/x-shellscript')!==false){
				$objInfraException->adicionarValidacao('Conteúdo do anexo não permitido por restrição de segurança.');
			}
	
			$objInfraException->lancarValidacoes();
			
			if( $objMdPetIndisponibilidadeAnexoDTO->getStrHash() == "" ){
			  $objMdPetIndisponibilidadeAnexoDTO->setStrHash(hash_file('md5',$strNomeUploadCompleto));
			}
			
			$objMdPetIndisponibilidadeAnexoBD = new MdPetIndisponibilidadeAnexoBD($this->getObjInfraIBanco());
			$ret = $objMdPetIndisponibilidadeAnexoBD->cadastrar($objMdPetIndisponibilidadeAnexoDTO);
			
	
	
			$strDiretorio = $this->obterDiretorio($objMdPetIndisponibilidadeAnexoDTO);
			
			if (is_dir($strDiretorio) === false){
				if (mkdir($strDiretorio,0755,true) === false){
					throw new InfraException('Erro criando diretório "' .$strDiretorio.'".');
				}
			}			
			
			copy($strNomeUploadCompleto, $strDiretorio.'/'.$ret->getNumIdAnexoPeticionamento());
	
			/*if (!$objIndisponibilidadeAnexoDTO->isSetStrSinExclusaoAutomatica() || $objIndisponibilidadeAnexoDTO->getStrSinExclusaoAutomatica() == 'S' ) {
				unlink($strNomeUploadCompleto);
			}*/
	
			return $ret;
			//Auditoria
	
		}catch(Exception $e){
			throw new InfraException('Erro cadastrando Anexo.',$e);
		}
	}
	
	public function obterDiretorio(MdPetIndisponibilidadeAnexoDTO $objMdPetIndisponibilidadeAnexoDTO){
		try{
			return ConfiguracaoSEI::getInstance()->getValor('SEI','RepositorioArquivos').'/'.substr($objMdPetIndisponibilidadeAnexoDTO->getDthInclusao(),6,4).'/'.substr($objMdPetIndisponibilidadeAnexoDTO->getDthInclusao(),3,2) .'/' .substr($objMdPetIndisponibilidadeAnexoDTO->getDthInclusao(),0,2);
		}catch(Exception $e){
			throw new InfraException('Erro obtendo diretório do anexo.',$e);
		}
	}
	
	protected function listarConectado(MdPetIndisponibilidadeAnexoDTO $objMdPetIndisponibilidadeAnexoDTO) {
		try {
	
			//Valida Permissao
			//SessaoSEI::getInstance()->validarAuditarPermissao('indisponibilidade_anexo_listar',__METHOD__,$objAnexoDTO);
	
			//Regras de Negocio
			//$objInfraException = new InfraException();
	
			//$objInfraException->lancarValidacoes();
	
			$objMdPetIndisponibilidadeAnexoBD = new MdPetIndisponibilidadeAnexoBD($this->getObjInfraIBanco());
			$ret = $objMdPetIndisponibilidadeAnexoBD->listar($objMdPetIndisponibilidadeAnexoDTO);
	
			//Auditoria
	
			return $ret;
	
		}catch(Exception $e){
			throw new InfraException('Erro listando Anexos.',$e);
		}
	}
	
	protected function listarAnexoPublicoConectado(ArquivoExtensaoDTO $objArquivoExtensaoDTO){
	
		try {
	
			$objArquivoExtensaoBD = new ArquivoExtensaoBD($this->getObjInfraIBanco());
			$ret = $objArquivoExtensaoBD->listar($objArquivoExtensaoDTO);
			return $ret;
	
		}catch(Exception $e){
			throw new InfraException('Erro listando Extensões de Arquivos.',$e);
		}
	
	}
	
	protected function consultarConectado(MdPetIndisponibilidadeAnexoDTO $objMdPetIndisponibilidadeAnexoDTO) {
		
		try {
	
			$objMdPetIndisponibilidadeAnexoBD = new MdPetIndisponibilidadeAnexoBD($this->getObjInfraIBanco());
			$ret = $objMdPetIndisponibilidadeAnexoBD->consultar($objMdPetIndisponibilidadeAnexoDTO);
			return $ret;
	
		}catch(Exception $e){
			throw new InfraException('Erro listando Anexos.',$e);
		}
	}
	
	protected function excluirControlado($arrObjMdPetIndisponibilidadeAnexoDTO){
		try {
			//Valida Permissao
			//SessaoSEI::getInstance()->validarAuditarPermissao('anexo_excluir',__METHOD__,$arrObjAnexoDTO);
	
			//Regras de Negocio
			//$objInfraException = new InfraException();
	
			//$objInfraException->lancarValidacoes();
	
			$objMdPetIndisponibilidadeAnexoBD = new MdPetIndisponibilidadeAnexoBD($this->getObjInfraIBanco());
			for($i=0;$i<count($arrObjMdPetIndisponibilidadeAnexoDTO);$i++){
				$objMdPetIndisponibilidadeAnexoBD->excluir($arrObjMdPetIndisponibilidadeAnexoDTO[$i]);
			}
	
			//Auditoria
	
		}catch(Exception $e){
			throw new InfraException('Erro excluindo Anexo.',$e);
		}
	}
	
}
?>
  
 