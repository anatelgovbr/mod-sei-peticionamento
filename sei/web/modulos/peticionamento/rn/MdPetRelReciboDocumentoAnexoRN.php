<?
/**
* ANATEL
*
* 01/07/2016 - criado por marcelo.bezerra - CAST
*
*/

require_once dirname(__FILE__).'/../../../SEI.php';

class MdPetRelReciboDocumentoAnexoRN extends InfraRN {
	
	public function __construct() {
		parent::__construct ();
	}
	
	public static $TP_PRINCIPAL = 'P';
    public static $TP_ESSENCIAL = 'E';
    public static $TP_COMPLEMENTAR = 'C';
	
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
	protected function listarConectado(MdPetRelReciboDocumentoAnexoDTO $objDTO) {
	
		try {
							
			$objInfraException = new InfraException();						
			$objBD = new MdPetRelReciboDocumentoAnexoBD($this->getObjInfraIBanco());
			$ret = $objBD->listar($objDTO);	
			return $ret;
			
		} catch (Exception $e) {
			throw new InfraException ('Erro listando Recibo Anexo Peticionamento.', $e);
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
	protected function consultarConectado(MdPetRelReciboDocumentoAnexoDTO $objDTO) {
	
		try {
	
			$objBD = new MdPetRelReciboDocumentoAnexoBD($this->getObjInfraIBanco());
			$ret = $objBD->consultar( $objDTO );
			return $ret;
				
		} catch (Exception $e) {
			throw new InfraException ('Erro listando Recibo Anexo Peticionamento.', $e);
		}
	}
	
	protected function cadastrarControlado(MdPetRelReciboDocumentoAnexoDTO $objDTO) {
		
		try {
			
			$objInfraException = new InfraException();
			$objBD = new MdPetRelReciboDocumentoAnexoBD($this->getObjInfraIBanco());
			$ret = $objBD->cadastrar($objDTO);	
			return $ret;
			
		} catch ( Exception $e ) {
			throw new InfraException ('Erro cadastrando Menu.', $e );
		}
	}

	/**
	 * Short description of method contarConectado
	 *
	 * @access protected
	 * @author Jaqueline Mendes <jaqueline.mendes@castgroup.com.br>
	 * @param $objDTO
	 * @return mixed
	 */
	protected function contarConectado(MdPetRelReciboDocumentoAnexoDTO $objDTO) {

		try {
			$objBD = new MdPetRelReciboDocumentoAnexoBD($this->getObjInfraIBanco());
			return $objBD->contar($objDTO);

		} catch ( Exception $e ) {
			throw new InfraException ('Erro contando Recibo Anexo do Peticionamento, ', $e);
		}
	}
	
}
?>