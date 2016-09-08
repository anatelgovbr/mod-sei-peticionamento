<?
/**
* ANATEL
*
* 25/04/2016 - criado por jaqueline.mendes - CAST
*
*/

require_once dirname(__FILE__).'/../../../SEI.php';

class TamanhoArquivoPermitidoPeticionamentoRN extends InfraRN {
	
	public static $ID_FIXO_TAMANHO_ARQUIVO = '1';
	
	public function __construct() {
		parent::__construct ();
	}
	
	protected function inicializarObjInfraIBanco() {
		return BancoSEI::getInstance ();
	}
	
    /**
	 * Short description of method listarParaUsuarioExternoConectado
	 *
	 * @access protected
	 * @author Marcelo Bezerra <marcelo.bezerra@cast.com.br>
	 * @param  $objCondutaLitigiosoDTO
	 * @return mixed
	 */
	protected function listarTamanhoMaximoConfiguradoParaUsuarioExternoConectado(TamanhoArquivoPermitidoPeticionamentoDTO $objDTO) {
	
		try {

			//SessaoSEIExterna::getInstance()->validarAuditarPermissao('peticionamento_usuario_externo_cadastrar',__METHOD__,$objDTO);
			$objBD = new TamanhoArquivoPermitidoPeticionamentoBD($this->getObjInfraIBanco());
			$ret = $objBD->listar($objDTO);				
			return $ret;

		} catch (Exception $e) {
			throw new InfraException ('Erro listando Tamanho Maximo Peticionamento.', $e);
		}
	}	

    /**
	 * Short description of method listarConectado
	 *
	 * @access protected
	 * @author Jaqueline Mendes <jaqueline.mendes@cast.com.br>
	 * @param
	 *        	$objCondutaLitigiosoDTO
	 * @return mixed
	 */
	protected function listarConectado(IndisponibilidadePeticionamentoDTO $objIndisponibilidadePeticionamentoDTO) {
	
		try {

			SessaoSEI::getInstance()->validarAuditarPermissao('gerir_tamanho_arquivo_peticionamento_listar',__METHOD__,$objCondutaLitigiosoDTO);
	
			//Regras de Negocio
			//$objInfraException = new InfraException();
	
			//$objInfraException->lancarValidacoes();
			//var_dump($objCondutaLitigiosoDTO->getStrSinAtivo());exit;
			$objIndisponibilidadePeticionamentoBD = new IndisponibilidadePeticionamentoBD($this->getObjInfraIBanco());
			$ret = $objIndisponibilidadePeticionamentoBD->listar($objIndisponibilidadePeticionamentoDTO);				
			return $ret;

		} catch (Exception $e) {
			throw new InfraException ('Erro listando Indisponibilidade Peticionamento.', $e);
		}
	}
	
	
/**
	 * Short description of method consultarConectado
	 *
	 * @access protected
	 * @author Jaqueline Mendes <jaqueline.mendes@cast.com.br>
	 * @param  $objIndisponibilidadePeticionamentoDTO
	 * @return mixed
	 */
	protected function consultarConectado(TamanhoArquivoPermitidoPeticionamentoDTO $objTamanhoArquivoDTO) {
		try {
			
			// Valida Permissao
			SessaoSEI::getInstance ()->validarAuditarPermissao ('gerir_tamanho_arquivo_peticionamento_consultar', __METHOD__, $objTamanhoArquivoDTO );
			
		    $objTamanhoArquivoBD = new TamanhoArquivoPermitidoPeticionamentoBD($this->getObjInfraIBanco());
			$ret = $objTamanhoArquivoBD->consultar($objTamanhoArquivoDTO);
			
			return $ret;
		} catch ( Exception $e ) {
			throw new InfraException('Erro consultando Tamanho de Arquivo Permitido Peticionamento.', $e);
		}
	}
	
	
	/**
	 * Short description of method cadastrarControlado
	 *
	 * @access protected
	 * @author Jaqueline Mendes <jaqueline.mendes@cast.com.br>
	 * @param  $objTamanhoArquivoDTO
	 * @return mixed
	 */
	protected function cadastrarControlado(TamanhoArquivoPermitidoPeticionamentoDTO $objTamanhoArquivoDTO) {
		try {
			// Valida Permissao
			SessaoSEI::getInstance ()->validarAuditarPermissao ('gerir_tamanho_arquivo_peticionamento_cadastrar', __METHOD__, $objTamanhoArquivoDTO );
				
			// Regras de Negocio
			$objInfraException = new InfraException();
			$valido = $this->_validarCamposDocumento($objTamanhoArquivoDTO->getNumValorDocPrincipal(), 'Valor para Documento Principal', $objInfraException);
			$valido = $this->_validarCamposDocumento($objTamanhoArquivoDTO->getNumValorDocComplementar(), 'Valor para Documento Complementar', $objInfraException);
			
			if($valido){
				$this->_validarParametroMaxPermitido($objTamanhoArquivoDTO, $objInfraException);
			}
			
			$objInfraException->lancarValidacoes();
	
			//$sql  = "INSERT INTO md_pet_tamanho_arquivo (id_md_pet_tamanho_arquivo,valor_doc_principal,valor_doc_complementar,sin_ativo)"; 
			//$sql .= "VALUES (".self::$ID_FIXO_TAMANHO_ARQUIVO.", ".$objTamanhoArquivoDTO->getNumValorDocPrincipal().", ";
			//$sql .= $objTamanhoArquivoDTO->getNumValorDocComplementar().", 'S')";
	
			//$rs = $this->getObjInfraIBanco ()->executarSql ( $sql );
			
			$objTamanhoArquivoBD = new TamanhoArquivoPermitidoPeticionamentoBD ($this->getObjInfraIBanco ());
			$objTamanhoArquivoDTO = $objTamanhoArquivoBD->cadastrar($objTamanhoArquivoDTO);
	
			return $objTamanhoArquivoDTO;
		} catch ( Exception $e ) {
			throw new InfraException ('Erro cadastrando Tamanho de Arquivo Peticionamento.', $e );
		}
	}
	
	
	private function _validarParametroMaxPermitido($objTamanhoArquivoDTO, $objInfraException){
		$objInfraParametroDTO = new InfraParametroDTO();
		$objInfraParametroDTO->retStrNome();
		$objInfraParametroDTO->retStrValor();
		$objInfraParametroDTO->setStrNome('SEI_TAM_MB_DOC_EXTERNO');
		
		$objInfraParametroRN = new InfraParametroRN();
		$objInfraParametroDTO = $objInfraParametroRN->consultar($objInfraParametroDTO);
		
		$valor = $objInfraParametroDTO->getStrValor();
		
		$erro = false;
		
		if($valor != ''){
			if($objTamanhoArquivoDTO->getNumValorDocPrincipal() > $valor){
				$erro = true;
			}
			
			if($objTamanhoArquivoDTO->getNumValorDocComplementar() > $valor){
				$erro = true;
			}
		}
		
	if($erro){
		$objInfraException->adicionarValidacao('Limite em Mb superior ao limite global do SEI indicado em Infra > Parâmetros. Informar valor menor.');
	}		
		
	}
	
	
	
	/**
	 * Short description of method alterarControlado
	 *
	 * @access protected
	 * @author Jaqueline Mendes <jaqueline.mendes@cast.com.br>
	 * @param  $objTamanhoArquivoDTO
	 * @return void
	 */
	protected function alterarControlado(TamanhoArquivoPermitidoPeticionamentoDTO $objTamanhoArquivoDTO) {
	
		try {
				
			// Valida Permissao
			SessaoSEI::getInstance()->validarAuditarPermissao ('gerir_tamanho_arquivo_peticionamento_cadastrar', __METHOD__, $objTamanhoArquivoDTO );
				
	
			// Regras de Negocio
			$objInfraException = new InfraException ();
			$valido = $this->_validarCamposDocumento($objTamanhoArquivoDTO->getNumValorDocPrincipal(), 'Valor para Documento Principal', $objInfraException);
			$valido = $this->_validarCamposDocumento($objTamanhoArquivoDTO->getNumValorDocComplementar(), 'Valor para Documento Complementar', $objInfraException);
		
			if($valido){
		    	$this->_validarParametroMaxPermitido($objTamanhoArquivoDTO, $objInfraException);
			}
			
			$objInfraException->lancarValidacoes();
				
	
			$objTamanhoArquivoBD = new TamanhoArquivoPermitidoPeticionamentoBD ($this->getObjInfraIBanco ());
			$objTamanhoArquivoBD->alterar($objTamanhoArquivoDTO);
	
				
			// Auditoria
		} catch ( Exception $e ) {
			throw new InfraException ('Erro alterando Tamanho Arquivo Permitido Peticionamento.', $e);
		}
	}
	
	
	/**
	 * Validate fields
	 *
	 * @access private
	 * @author Jaqueline Mendes <jaqueline.mendes@cast.com.br>
	 * @return void
	 */
	private function _validarCamposDocumento($campo, $nomeCampo , $objInfraException) {
		$valido = true;
		
		// VERIFICA SE O CAMPO FOI PREENCHIDO
		if (InfraString::isBolVazia (trim($campo))) {
			$msg1 = $nomeCampo. ' não informado.';
			$objInfraException->adicionarValidacao($msg1);
			$valido = false;
		}
		if (trim ( $campo ) != '')
		{
			if (strlen ($campo) > 11) {
				$msg2 = $nomeCampo .' possui tamanho superior a 11 caracteres.';
				$objInfraException->adicionarValidacao($msg2);
				$valido = false;
			}
		}
		
		return $valido;
		
	
	}	
	
}
?>