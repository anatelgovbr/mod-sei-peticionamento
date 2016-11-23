  <?
/**
* ANATEL
*
* 29/04/2016 - criado por marcelo.bezerra - CAST
*
*/

require_once dirname(__FILE__).'/../../../SEI.php';

class IndisponibilidadeAnexoPeticionamentoRN extends InfraRN { 
	
	
	public function __construct() {
		parent::__construct ();
	}
	
	protected function inicializarObjInfraIBanco() {
		return BancoSEI::getInstance ();
	}
	
	
	protected function cadastrarControlado(IndisponibilidadeAnexoPeticionamentoDTO $objIndisponibilidadeAnexoPeticionamentoDTO) {
		
		try{
	
			//Valida Permissao
		//	SessaoSEI::getInstance()->validarAuditarPermissao('anexo_cadastrar',__METHOD__,$objIndisponibilidadeAnexoDTO);
	
			//Regras de Negocio
			$objInfraException = new InfraException();
			$strNomeUpload = $objIndisponibilidadeAnexoPeticionamentoDTO->getNumIdAnexoPeticionamento();
	

			$strNomeUploadCompleto = DIR_SEI_TEMP.'/'.$strNomeUpload;
			if (!file_exists($strNomeUploadCompleto)){
				$objInfraException->lancarValidacao('Anexo '.$objIndisponibilidadeAnexoPeticionamentoDTO->getStrNome().' não encontrado.');
			}
	
			if (filesize($strNomeUploadCompleto)==0){
				$objInfraException->lancarValidacao('Anexo '.$objIndisponibilidadeAnexoPeticionamentoDTO->getStrNome().' vazio.');
			}
	
			$finfo = finfo_open(FILEINFO_MIME_TYPE);
			$strMime = finfo_file($finfo, $strNomeUploadCompleto);
			finfo_close($finfo);
	
			if (strpos($strMime,'text/x-php')!==false || strpos($strMime,'text/x-shellscript')!==false){
				$objInfraException->adicionarValidacao('Conteúdo do anexo não permitido por restrição de segurança.');
			}
	
			$objInfraException->lancarValidacoes();
			
			if( $objIndisponibilidadeAnexoPeticionamentoDTO->getStrHash() == "" ){
			  $objIndisponibilidadeAnexoPeticionamentoDTO->setStrHash(hash_file('md5',$strNomeUploadCompleto));
			}
			
			$objIndisponibilidadeAnexoPeticionamentoBD = new IndisponibilidadeAnexoPeticionamentoBD($this->getObjInfraIBanco());
			$ret = $objIndisponibilidadeAnexoPeticionamentoBD->cadastrar($objIndisponibilidadeAnexoPeticionamentoDTO);
			
	
	
			$strDiretorio = $this->obterDiretorio($objIndisponibilidadeAnexoPeticionamentoDTO);
			
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
	
	public function obterDiretorio(IndisponibilidadeAnexoPeticionamentoDTO $objIndisponibilidadeAnexoPeticionamentoDTO){
		try{
			return ConfiguracaoSEI::getInstance()->getValor('SEI','RepositorioArquivos').'/'.substr($objIndisponibilidadeAnexoPeticionamentoDTO->getDthInclusao(),6,4).'/'.substr($objIndisponibilidadeAnexoPeticionamentoDTO->getDthInclusao(),3,2) .'/' .substr($objIndisponibilidadeAnexoPeticionamentoDTO->getDthInclusao(),0,2);
		}catch(Exception $e){
			throw new InfraException('Erro obtendo diretório do anexo.',$e);
		}
	}
	
	protected function listarConectado(IndisponibilidadeAnexoPeticionamentoDTO $objIndisponibilidadeAnexoPeticionamentoDTO) {
		try {
	
			//Valida Permissao
			//SessaoSEI::getInstance()->validarAuditarPermissao('indisponibilidade_anexo_listar',__METHOD__,$objAnexoDTO);
	
			//Regras de Negocio
			//$objInfraException = new InfraException();
	
			//$objInfraException->lancarValidacoes();
	
			$objIndisponibilidadeAnexoPeticionamentoBD = new IndisponibilidadeAnexoPeticionamentoBD($this->getObjInfraIBanco());
			$ret = $objIndisponibilidadeAnexoPeticionamentoBD->listar($objIndisponibilidadeAnexoPeticionamentoDTO);
	
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
	
	protected function consultarConectado(IndisponibilidadeAnexoPeticionamentoDTO $objIndisponibilidadeAnexoPeticionamentoDTO) {
		
		try {
	
			$objIndisponibilidadeAnexoPeticionamentoBD = new IndisponibilidadeAnexoPeticionamentoBD($this->getObjInfraIBanco());
			$ret = $objIndisponibilidadeAnexoPeticionamentoBD->consultar($objIndisponibilidadeAnexoPeticionamentoDTO);	
			return $ret;
	
		}catch(Exception $e){
			throw new InfraException('Erro listando Anexos.',$e);
		}
	}
	
	protected function excluirControlado($arrObjIndisponibilidadePeticionamentoAnexoDTO){
		try {
			//Valida Permissao
			//SessaoSEI::getInstance()->validarAuditarPermissao('anexo_excluir',__METHOD__,$arrObjAnexoDTO);
	
			//Regras de Negocio
			//$objInfraException = new InfraException();
	
			//$objInfraException->lancarValidacoes();
	
			$objIndisponibilidadeAnexoPeticionamentoBD = new IndisponibilidadeAnexoPeticionamentoBD($this->getObjInfraIBanco());
			for($i=0;$i<count($arrObjIndisponibilidadePeticionamentoAnexoDTO);$i++){
				$objIndisponibilidadeAnexoPeticionamentoBD->excluir($arrObjIndisponibilidadePeticionamentoAnexoDTO[$i]);
			}
	
			//Auditoria
	
		}catch(Exception $e){
			throw new InfraException('Erro excluindo Anexo.',$e);
		}
	}
	
}
?>
  
 