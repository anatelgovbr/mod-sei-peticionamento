<?
/**
* ANATEL
*
* 06/05/2016 - criado por alan.campos - CAST
*
*/

require_once dirname(__FILE__).'/../../../SEI.php';

class MdPetExtensoesArquivoRN extends InfraRN {
	
	public function __construct() {
		parent::__construct ();
	}
	
	protected function inicializarObjInfraIBanco() {
		return BancoSEI::getInstance ();
	}
	
	
	/**
	 * Short description of method excluirControlado
	 *
	 * @access protected
	 * @author Alan Campos <alan.campos@castgroup.com.br>
	 * @param $objMdPetExtensoesArquivoDTO
	 * @return void
	 */
	protected function excluirControlado($objMdPetExtensoesArquivoDTO){
		try {
	
			//Valida Permissao
			SessaoSEI::getInstance ()->validarAuditarPermissao ('gerir_extensoes_arquivo_peticionamento_cadastrar', __METHOD__, $objMdPetExtensoesArquivoDTO );
			
			$objMdPetExtensoesArquivoBD = new MdPetExtensoesArquivoBD($this->getObjInfraIBanco());
			for($i=0;$i<count($objMdPetExtensoesArquivoDTO);$i++){
				$objMdPetExtensoesArquivoBD->excluir($objMdPetExtensoesArquivoDTO[$i]);
			}
	
			//Auditoria
	
		}catch(Exception $e){
			throw new InfraException('Erro excluindo Extensão.',$e);
		}
	}
	
	/**
	 * Short description of method listarConectado
	 *
	 * @access protected
	 * @author Alan Campos <alan.campos@castgroup.com.br>
	 * @param $objMdPetExtensoesArquivoDTO
	 * @return mixed
	 */
	protected function listarConectado(MdPetExtensoesArquivoDTO $objMdPetExtensoesArquivoDTO) {
	
		try {
	
			//Regras de Negocio
			//$objInfraException = new InfraException();
	
			//$objInfraException->lancarValidacoes();
			//var_dump($objCondutaLitigiosoDTO->getStrSinAtivo());exit;
			$objMdPetExtensoesArquivoBD = new MdPetExtensoesArquivoBD($this->getObjInfraIBanco());
			$ret = $objMdPetExtensoesArquivoBD->listar($objMdPetExtensoesArquivoDTO);

			return $ret;
		} catch (Exception $e) {
			throw new InfraException ('Erro listando Indisponibilidade Peticionamento.', $e);
		}
	}
	
	
	/**
	 * Short description of method consultarConectado
	 *
	 * @access protected
	 * @author Alan Campos <alan.campos@castgroup.com.br>
	 * @param  $objMdPetExtensoesArquivoDTO
	 * @return mixed
	 */
	protected function consultarConectado(MdPetExtensoesArquivoDTO $objMdPetExtensoesArquivoDTO) {
		try {
			
			// Valida Permissao
			
			$objMdPetTamanhoArquivoBD = new MdPetTamanhoArquivoBD($this->getObjInfraIBanco());
			$ret = $objMdPetTamanhoArquivoBD->consultar($objMdPetExtensoesArquivoDTO);
			
			return $ret;
		} catch ( Exception $e ) {
			throw new InfraException('Erro consultando Tamanho de Arquivo Permitido Peticionamento.', $e);
		}
	}
	
	
	/**
	 * Short description of method cadastrarControlado
	 *
	 * @access protected
	 * @author Alan Campos <alan.campos@castgroup.com.br>
	 * @param  $objMdPetExtensoesArquivoDTO
	 * @return mixed
	 */
	protected function cadastrarControlado(MdPetExtensoesArquivoDTO $objMdPetExtensoesArquivoDTO) {
		try {
			// Valida Permissao
			SessaoSEI::getInstance ()->validarAuditarPermissao ('gerir_extensoes_arquivo_peticionamento_cadastrar', __METHOD__, $objMdPetExtensoesArquivoDTO );
				
			// Regras de Negocio
			/*
			$objInfraException = new InfraException();
			$valido = $this->_validarCamposDocumento($objTamanhoArquivoDTO->getNumValorDocPrincipal(), 'Valor para Documento Principal', $objInfraException);
			$valido = $this->_validarCamposDocumento($objTamanhoArquivoDTO->getNumValorDocComplementar(), 'Valor para Documento Complementar', $objInfraException);
			
			if($valido){
				$this->_validarParametroMaxPermitido($objTamanhoArquivoDTO, $objInfraException);
			}
			
			$objInfraException->lancarValidacoes();
			*/
	
			$objMdPetExtensoesArquivoBD = new MdPetExtensoesArquivoBD($this->getObjInfraIBanco());
			$ret = $objMdPetExtensoesArquivoBD->cadastrar($objMdPetExtensoesArquivoDTO);

			return $ret;
		} catch ( Exception $e ) {
			throw new InfraException ('Erro cadastrando Tamanho de Arquivo Peticionamento.', $e );
		}
	}
	
	
	/**
	 * Validate fields
	 *
	 * @access private
	 * @author Alan Campos <alan.campos@castgroup.com.br>
	 * @return bool
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