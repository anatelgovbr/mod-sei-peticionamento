<?
/**
* ANATEL
*
* 04/08/2016 - criado por jaqueline.mendes@castgroup.com.br - CAST
* 26/08/2024 - Atualização por gabrielg.colab - SPASSU 
*
*/

require_once dirname(__FILE__).'/../../../SEI.php';

class MdPetRelTpProcessoUnidRN extends InfraRN{ 

	
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
	 * @author Jaqueline Mendes <jaqueline.mendes@castgroup.com.br>
	 * @param  $objMdPetRelTpProcessoUnidDTO
	 * @return mixed
	 */
	protected function listarConectado(MdPetRelTpProcessoUnidDTO $objMdPetRelTpProcessoUnidDTO) {
	
		try {
	
			//Regras de Negocio
			$objInfraException = new InfraException();
	
			$objInfraException->lancarValidacoes();
			
			$objMdPetRelTpProcessoUnidBD = new MdPetRelTpProcessoUnidBD($this->getObjInfraIBanco());
			$ret = $objMdPetRelTpProcessoUnidBD->listar($objMdPetRelTpProcessoUnidDTO);
				
			return $ret;
		} catch (Exception $e) {
			throw new InfraException ('Erro listando Relacionamento de Tipo de Processo e Unidade Peticionamento.', $e);
		}
	}

	protected function contarConectado(MdPetRelTpProcessoUnidDTO $objMdPetRelTpProcessoUnidDTO){
		try {

			$objMdPetRelTpProcessoUnidBD = new MdPetIntSerieBD($this->getObjInfraIBanco());
			$ret = $objMdPetRelTpProcessoUnidBD->contar($objMdPetRelTpProcessoUnidDTO);

			return $ret;
		}catch(Exception $e){
			throw new InfraException('Erro contando .',$e);
		}
	}
/**
	 * Short description of method consultarConectado
	 *
	 * @access protected
	 * @author Jaqueline Mendes <jaqueline.mendes@castgroup.com.br>
	 * @param  $objMdPetTipoProcessoDTO
	 * @return mixed
	 */
	protected function consultarConectado(MdPetTipoProcessoDTO $objMdPetTipoProcessoDTO) {
		try {
			
			// Valida Permissao
			//SessaoSEI::getInstance ()->validarAuditarPermissao ('md_pet_tipo_processo_alterar', __METHOD__, $objMdPetTipoProcessoDTO );
			
		    $objMdPetTipoProcessoBD = new MdPetTipoProcessoBD($this->getObjInfraIBanco());
			$ret = $objMdPetTipoProcessoBD->consultar($objMdPetTipoProcessoDTO);
			
			return $ret;
		} catch ( Exception $e ) {
			throw new InfraException('Erro consultando Tipo de Processo Peticionamento.', $e);
		}
	}
	
	
	/**
	 * Short description of method cadastrarControlado
	 *
	 * @access protected
	 * @author Jaqueline Mendes <jaqueline.mendes@castgroup.com.br>
	 * @param  $objMdPetRelTpProcessoUnidDTO
	 * @return mixed
	 */
	protected function cadastrarControlado(MdPetRelTpProcessoUnidDTO $objMdPetRelTpProcessoUnidDTO) {
		try {
			// Valida Permissao
			$objMdPetRelTpProcessoUnidBD = new MdPetRelTpProcessoUnidBD($this->getObjInfraIBanco());
			$ret = $objMdPetRelTpProcessoUnidBD->cadastrar($objMdPetRelTpProcessoUnidDTO);
	
			return $ret;
		} catch ( Exception $e ) {
			throw new InfraException ('Erro cadastrando Relacionamento do Tipo de Processo.', $e );
		}
	}
	
	/**
	 * Short description of method excluirControlado
	 *
	 * @access protected
	 * @author Jaqueline Mendes <jaqueline.mendes@castgroup.com.br>
	 * @param  $arrMdPetRelTpProcessoUnidDTO
	 * @return void
	 */
	protected function excluirControlado($arrMdPetRelTpProcessoUnidDTO) {
	
		try {
	
			$objMdPetRelTpProcessoUnidBD = new MdPetRelTpProcessoUnidBD($this->getObjInfraIBanco());
			for($i = 0; $i < count($arrMdPetRelTpProcessoUnidDTO); $i ++) {
				$objMdPetRelTpProcessoUnidBD->excluir($arrMdPetRelTpProcessoUnidDTO[$i]);
			}
	
		} catch(Exception $e) {
			throw new InfraException ('Erro excluindo Tipo de Processo Peticionamento.', $e );
		}
	}
}
?>