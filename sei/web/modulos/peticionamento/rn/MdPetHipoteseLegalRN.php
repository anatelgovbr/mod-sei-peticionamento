<?
/**
* ANATEL
*
* 30/08/2016 - criado por jaqueline.mendes@castgroup.com.br - CAST
*
*/

require_once dirname(__FILE__).'/../../../SEI.php';

class MdPetHipoteseLegalRN extends InfraRN {
	
	public function __construct() {
		parent::__construct ();
	}
	
	protected function inicializarObjInfraIBanco() {
		return BancoSEI::getInstance ();
	}
	
	protected function cadastrarControlado($arrObjHipoteseLegalPeticionamento){
		try {
            // Valida Permissao
            SessaoSEI::getInstance ()->validarAuditarPermissao ('md_pet_hipotese_legal_nl_acesso_cadastrar', __METHOD__, $arrObjHipoteseLegalPeticionamento );
			if(is_array($arrObjHipoteseLegalPeticionamento)){
				$objMdPetHipoteseLegalBD = new MdPetHipoteseLegalBD($this->getObjInfraIBanco());
				foreach($arrObjHipoteseLegalPeticionamento as $obj){
					$objMdPetHipoteseLegalBD->cadastrar($obj);
				}
			return true;
			}
			return false;		
				
		} catch (Exception $e) {
			throw new InfraException ('Erro cadastrando Hipóteses Legais.', $e );
		}
	}

	protected function contarConectado($objMdPetHipoteseLegalDTO) {
		try {
			$objMdPetHipoteseLegalBD = new MdPetHipoteseLegalBD($this->getObjInfraIBanco());
			$ret = $objMdPetHipoteseLegalBD->contar($objMdPetHipoteseLegalDTO);

			return $ret;
		
		}catch(Exception $e) {
			throw new InfraException('Erro contando as Hipóteses Legais.',$e);
		}
	}
	
	protected function listarConectado($objMdPetHipoteseLegalDTO) {
		try {
			$objMdPetHipoteseLegalBD = new MdPetHipoteseLegalBD($this->getObjInfraIBanco());
			$ret = $objMdPetHipoteseLegalBD->listar($objMdPetHipoteseLegalDTO);

			return $ret;

		}catch(Exception $e) {
			throw new InfraException('Erro listando as Hipóteses Legais.',$e);
		}
	}
	
	protected function listarHipotesesParametrizadasConectado( $arrIdsHipoteses ) {
		try {
			$objHipoteseLegalBD = new HipoteseLegalBD($this->getObjInfraIBanco());
			$objHipoteseLegalDTO = new HipoteseLegalDTO();
			$objHipoteseLegalDTO->retTodos();
			$objHipoteseLegalDTO->setOrd('Nome', InfraDTO::$TIPO_ORDENACAO_ASC);
			
			$objHipoteseLegalDTO->adicionarCriterio(array('IdHipoteseLegal'),
				array(InfraDTO::$OPER_IN),
				array($arrIdsHipoteses));
			
			$ret = $objHipoteseLegalBD->listar($objHipoteseLegalDTO);
	
			return $ret;
			
		} catch(Exception $e) {
			throw new InfraException('Erro listando as Hipóteses Legais.',$e);
		}
	}

	protected function excluirControlado($arrObjMdPetHipoteseLegalDTO){
		try {
			$objMdPetHipoteseLegalBD = new MdPetHipoteseLegalBD($this->getObjInfraIBanco());
			
			for($i=0;$i<count($arrObjMdPetHipoteseLegalDTO);$i++){
				$objMdPetHipoteseLegalBD->excluir($arrObjMdPetHipoteseLegalDTO[$i]);
			}
		
		}catch(Exception $e) {
			throw new InfraException('Erro excluindo as Hipóteses Legais.',$e);
		}
	}

}
?>