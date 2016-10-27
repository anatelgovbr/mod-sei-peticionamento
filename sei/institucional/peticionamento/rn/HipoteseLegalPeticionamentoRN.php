  <?
/**
* ANATEL
*
* 30/08/2016 - criado por jaqueline.mendes@castgroup.com.br - CAST
*
*/

require_once dirname(__FILE__).'/../../../SEI.php';

class HipoteseLegalPeticionamentoRN extends InfraRN { 
	
	
	public function __construct() {
		parent::__construct ();
	}
	
	protected function inicializarObjInfraIBanco() {
		return BancoSEI::getInstance ();
	}
	
	
	protected function cadastrarControlado($arrObjHipoteseLegalPeticionamento){
		try {
				
//			SessaoSEI::getInstance ()->validarAuditarPermissao ('hipotese_legal_nl_acesso_peticionamento_cadastrar', __METHOD__, $arrObjHipoteseLegalPeticionamento);
			
		   if(is_array($arrObjHipoteseLegalPeticionamento))
		   {
		 	$objHipoteseLegalPeticionamentoBD = new HipoteseLegalPeticionamentoBD($this->getObjInfraIBanco());
		 	
		 	foreach($arrObjHipoteseLegalPeticionamento as $obj){
				 $objHipoteseLegalPeticionamentoBD->cadastrar($obj);
		 	}
			return true;
		   }
				
	    return false;		
				
		} catch ( Exception $e ) {
			throw new InfraException ('Erro cadastrando Classificação do Tema e Subtema do Relacionamento Institucional.', $e );
		}
	}
	
	
	

	protected function contarConectado($objHipoteseLegalPeticionamentoDTO) {
		try {
	
			$objHipoteseLegalPeticionamentoBD = new HipoteseLegalPeticionamentoBD($this->getObjInfraIBanco());
			$ret = $objHipoteseLegalPeticionamentoBD->contar($objHipoteseLegalPeticionamentoDTO);
	
			return $ret;
	
		}catch(Exception $e){
			throw new InfraException('Erro contando as Hípoteses Legais.',$e);
		}
	}
	
	
	protected function listarConectado($objHipoteseLegalPeticionamentoDTO) {
		try {
	
			$objHipoteseLegalPeticionamentoBD = new HipoteseLegalPeticionamentoBD($this->getObjInfraIBanco());
			$ret = $objHipoteseLegalPeticionamentoBD->listar($objHipoteseLegalPeticionamentoDTO);
	
			return $ret;
	
		}catch(Exception $e){
			throw new InfraException('Erro listando as Hípoteses Legais.',$e);
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
	
		} catch(Exception $e){
			 throw new InfraException('Erro listando as Hípoteses Legais.',$e);
		}
	}
	
	
	protected function excluirControlado($arrObjHipoteseLegalPeticionamentoDTO){
		try {
	
			$objHipoteseLegalPeticionamentoBD = new HipoteseLegalPeticionamentoBD($this->getObjInfraIBanco());
			
			for($i=0;$i<count($arrObjHipoteseLegalPeticionamentoDTO);$i++){
				$objHipoteseLegalPeticionamentoBD->excluir($arrObjHipoteseLegalPeticionamentoDTO[$i]);
			}
	
		}catch(Exception $e){
			throw new InfraException('Erro cadastrando as Hípoteses Legais.',$e);
		}
	}
	
}
?>
  
 