<?
/**
* ANATEL
*
* 25/04/2016 - criado por jaqueline.mendes - CAST
*
*/

require_once dirname(__FILE__).'/../../../SEI.php';

class MdPetTamanhoArquivoRN extends InfraRN {
	
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
	protected function listarTamanhoMaximoConfiguradoParaUsuarioExternoConectado(MdPetTamanhoArquivoDTO $objMdPetTamanhoArquivoDTO) {
	
		try {

			//SessaoSEIExterna::getInstance()->validarAuditarPermissao('md_pet_usu_ext_cadastrar',__METHOD__,$objDTO);
			$objMdPetTamanhoArquivoBD = new MdPetTamanhoArquivoBD($this->getObjInfraIBanco());
			$ret = $objMdPetTamanhoArquivoBD->listar($objMdPetTamanhoArquivoDTO);
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
	 *        	$objMdPetIndisponibilidadeDTO
	 * @return mixed
	 */
	protected function listarConectado(MdPetIndisponibilidadeDTO $objMdPetIndisponibilidadeDTO) {
	
		try {

			SessaoSEI::getInstance()->validarAuditarPermissao('md_pet_tamanho_arquivo_listar',__METHOD__,$objMdPetIndisponibilidadeDTO);

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
	
	
/**
	 * Short description of method consultarConectado
	 *
	 * @access protected
	 * @author Jaqueline Mendes <jaqueline.mendes@cast.com.br>
	 * @param  $objMdPetTamanhoArquivoDTO
 * @return mixed
	 */
	protected function consultarConectado(MdPetTamanhoArquivoDTO $objMdPetTamanhoArquivoDTO) {
		try {
			
			// Valida Permissao
			SessaoSEI::getInstance ()->validarAuditarPermissao ('md_pet_tamanho_arquivo_consultar', __METHOD__, $objMdPetTamanhoArquivoDTO );

		    $objMdPetTamanhoArquivoBD = new MdPetTamanhoArquivoBD($this->getObjInfraIBanco());
			$ret = $objMdPetTamanhoArquivoBD->consultar($objMdPetTamanhoArquivoDTO);
			
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
	 * @param  $objMdPetTamanhoArquivoDTO
	 * @return mixed
	 */
	protected function cadastrarControlado(MdPetTamanhoArquivoDTO $objMdPetTamanhoArquivoDTO) {
		try {
			// Valida Permissao
			SessaoSEI::getInstance ()->validarAuditarPermissao ('md_pet_tamanho_arquivo_cadastrar', __METHOD__, $objMdPetTamanhoArquivoDTO );

			// Regras de Negocio
			$objInfraException = new InfraException();

			$valido = $this->_validarCamposDocumento($objMdPetTamanhoArquivoDTO->getNumValorDocPrincipal(), 'Valor para Documento Principal', $objInfraException);
			$valido = $this->_validarCamposDocumento($objMdPetTamanhoArquivoDTO->getNumValorDocComplementar(), 'Valor para Documento Complementar', $objInfraException);
			
			if($valido){
				$this->_validarParametroMaxPermitido($objMdPetTamanhoArquivoDTO, $objInfraException);
			}
			
			$objInfraException->lancarValidacoes();

			$objMdPetTamanhoArquivoBD = new MdPetTamanhoArquivoBD ($this->getObjInfraIBanco ());
			$objMdPetTamanhoArquivoDTO = $objMdPetTamanhoArquivoBD->cadastrar($objMdPetTamanhoArquivoDTO);
	
			return $objMdPetTamanhoArquivoDTO;
		} catch ( Exception $e ) {
			throw new InfraException ('Erro cadastrando Tamanho de Arquivo Peticionamento.', $e );
		}
	}
	
	
	private function _validarParametroMaxPermitido($objMdPetTamanhoArquivoDTO, $objInfraException){
		$objInfraParametroDTO = new InfraParametroDTO();
		$objInfraParametroDTO->retStrNome();
		$objInfraParametroDTO->retStrValor();
		$objInfraParametroDTO->setStrNome('SEI_TAM_MB_DOC_EXTERNO');
		
		$objMdPetParametroRN = new MdPetParametroRN();
		$objInfraParametroDTO = $objMdPetParametroRN->consultar($objInfraParametroDTO);
		
		$valor = $objInfraParametroDTO->getStrValor();
		
		$erro = false;
		
		if($valor != ''){
			if($objMdPetTamanhoArquivoDTO->getNumValorDocPrincipal() > $valor){
				$erro = true;
			}
			
			if($objMdPetTamanhoArquivoDTO->getNumValorDocComplementar() > $valor){
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
	 * @param  $objMdPetTamanhoArquivoDTO
	 * @return void
	 */
	protected function alterarControlado(MdPetTamanhoArquivoDTO $objMdPetTamanhoArquivoDTO) {
	
		try {
				
			// Valida Permissao
			SessaoSEI::getInstance()->validarAuditarPermissao ('md_pet_tamanho_arquivo_cadastrar', __METHOD__, $objMdPetTamanhoArquivoDTO );


			// Regras de Negocio
			$objInfraException = new InfraException ();
			$valido = $this->_validarCamposDocumento($objMdPetTamanhoArquivoDTO->getNumValorDocPrincipal(), 'Valor para Documento Principal', $objInfraException);
			$valido = $this->_validarCamposDocumento($objMdPetTamanhoArquivoDTO->getNumValorDocComplementar(), 'Valor para Documento Complementar', $objInfraException);
		
			if($valido){
		    	$this->_validarParametroMaxPermitido($objMdPetTamanhoArquivoDTO, $objInfraException);
			}
			
			$objInfraException->lancarValidacoes();
				
	
			$objMdPetTamanhoArquivoBD = new MdPetTamanhoArquivoBD ($this->getObjInfraIBanco ());
			$objMdPetTamanhoArquivoBD->alterar($objMdPetTamanhoArquivoDTO);
	
				
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