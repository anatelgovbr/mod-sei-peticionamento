<?
/**
* ANATEL
*
* 21/06/2019 - criado por renato.monteiro - CAST
*
*/

require_once dirname(__FILE__).'/../../../SEI.php';

class MdPetTipoPoderLegalRN extends InfraRN {

    public static $PODER_LEGAL_CUMPRIMENTO = 1;

	public function __construct() {
		parent::__construct ();
	}
	
	
	protected function inicializarObjInfraIBanco() {
		return BancoSEI::getInstance ();
	}
	
	
	protected function listarConectado(MdPetTipoPoderLegalDTO $objDTO) {
	
		try {

			$objInfraException = new InfraException();						
			$objBD = new MdPetTipoPoderLegalBD($this->getObjInfraIBanco());
			$ret = $objBD->listar($objDTO);	
			return $ret;
			
		} catch (Exception $e) {
			throw new InfraException ('Erro listando Tipos de Poderes Legais.', $e);
		}
	}
	
	
	protected function consultarConectado(MdPetTipoPoderLegalDTO $objDTO) {
	
		try {
	
			$objBD = new MdPetTipoPoderLegalBD($this->getObjInfraIBanco());
			$ret = $objBD->consultar( $objDTO );
			return $ret;
				
		} catch (Exception $e) {
			throw new InfraException ('Erro listando Tipo Poder Legal.', $e);
		}
	}
	
	protected function cadastrarControlado(MdPetTipoPoderLegalDTO $objDTO) {
		
		try {
            SessaoSEI::getInstance ()->validarAuditarPermissao ('md_pet_tipo_poder_cadastrar', __METHOD__, $objDTO );
			//Regras de Negocio
            $objInfraException = new InfraException();
			//Valida��o de nome j� existente
			$this->validarStrNome($objDTO,$objInfraException);
			$objInfraException->lancarValidacoes();
			$objBD = new MdPetTipoPoderLegalBD($this->getObjInfraIBanco());
			$ret = $objBD->cadastrar($objDTO);	
			return $ret;
			
		} catch ( Exception $e ) {
			throw new InfraException ('Erro cadastrando Tipo Poder Legal.', $e );
		}
	}

	private function validarStrNome(MdPetTipoPoderLegalDTO $objMdPetTipoPoderLegalDTO, InfraException $objInfraException){
		try {
        if (InfraString::isBolVazia($objMdPetTipoPoderLegalDTO->getStrNome())){
            $objInfraException->adicionarValidacao('Nome n�o informado.');
        }else{
          
            if (strlen($objMdPetTipoPoderLegalDTO->getStrNome())>100){
                $objInfraException->adicionarValidacao('Nome possui tamanho superior a 100 caracteres.');
            }
            $dto = new MdPetTipoPoderLegalDTO();
            $dto->retStrNome();
            $dto->retNumIdTipoPoderLegal();

			$dto = $this->listar($dto);
			
			//Comparando cada um dos registros para ver se tem algum igual
            $exite = false;
            $alteracao = false;
			foreach ($dto as $key => $value) {
				if(str_replace([' ', '.'],'',strtolower($value->getStrNome())) == str_replace([' ', '.'],'',strtolower($objMdPetTipoPoderLegalDTO->getStrNome()))){
                    if(!is_null($objMdPetTipoPoderLegalDTO->getNumIdTipoPoderLegal())){
                        if($objMdPetTipoPoderLegalDTO->getNumIdTipoPoderLegal() == $value->getNumIdTipoPoderLegal()){
                            $alteracao = true;
                        }
                    }
				    $exite = true;
				}
			}
			if($exite && $alteracao == false){
                $objInfraException->adicionarValidacao('Este Tipo de Poder Legal j� est� cadastrado.');
            }
            
		}
	}catch(Exception $e){
		throw new InfraException('Erro alterando .',$e);
	}
    }

	protected function alterarControlado(MdPetTipoPoderLegalDTO $objDTO) {
		
		try {
            SessaoSEI::getInstance ()->validarAuditarPermissao ('md_pet_tipo_poder_alterar', __METHOD__, $objDTO );
            //Regras de Negocio
            $objInfraException = new InfraException();
            //Valida��o de nome j� existente
            $this->validarStrNome($objDTO,$objInfraException);
            $objInfraException->lancarValidacoes();
			$objBD = new MdPetTipoPoderLegalBD($this->getObjInfraIBanco());
			$ret = $objBD->alterar($objDTO);	
			return $ret;
			
		} catch ( Exception $e ) {
			throw new InfraException ('Erro ao Alterar Tipo Poder Legal.', $e );
		}
	}

	
	protected function contarConectado(MdPetTipoPoderLegalDTO $objDTO) {

		try {
			$objBD = new MdPetTipoPoderLegalBD($this->getObjInfraIBanco());
			return $objBD->contar($objDTO);

		} catch ( Exception $e ) {
			throw new InfraException ('Erro contando Tipo Poder Legal, ', $e);
		}
	}

	protected function excluirConectado(MdPetTipoPoderLegalDTO $objDTO) {

		try {
            SessaoSEI::getInstance ()->validarAuditarPermissao ('md_pet_tipo_poder_excluir', __METHOD__, $objDTO );
            //Regra de Negocio
            $objInfraException     = new InfraException();
			$objMdPetRelVincRepTpPoderDTO = new MdPetRelVincRepTpPoderDTO();
             $objMdPetRelVincRepTpPoderDTO->setNumIdTipoPoderLegal($objDTO->getNumIdTipoPoderLegal());
             $objMdPetRelVincRepTpPoderDTO->retNumIdVinculoRepresent();
             $objMdPetRelVincRepTpPoderRN = new  MdPetRelVincRepTpPoderRN();
             $arrObjMdPetRelVincRepTpPoderRN = $objMdPetRelVincRepTpPoderRN->listar($objMdPetRelVincRepTpPoderDTO);
			
			 if(count($arrObjMdPetRelVincRepTpPoderRN) > 0){
				$objInfraException->adicionarValidacao('N�o � possivel excluir este Tipo de Poder Legal, pois ele j� foi utilizado no sistema.');
				$objInfraException->lancarValidacoes();
			 }



			$objBD = new MdPetTipoPoderLegalBD($this->getObjInfraIBanco());
			return $objBD->excluir($objDTO);

		} catch ( Exception $e ) {
			throw new InfraException ('Erro excluindo Tipo Poder Legal, ', $e);
		}
	}


	protected function desativarConectado(MdPetTipoPoderLegalDTO $objDTO) {

		try {
            SessaoSEI::getInstance ()->validarAuditarPermissao ('md_pet_tipo_poder_desativar', __METHOD__, $objDTO );
			$objBD = new MdPetTipoPoderLegalBD($this->getObjInfraIBanco());
			return $objBD->desativar($objDTO);

		} catch ( Exception $e ) {
			throw new InfraException ('Erro desativando Tipo Poder Legal, ', $e);
		}
	}

	protected function reativarConectado(MdPetTipoPoderLegalDTO $objDTO) {

		try {
            SessaoSEI::getInstance ()->validarAuditarPermissao ('md_pet_tipo_poder_reativar', __METHOD__, $objDTO );
			$objBD = new MdPetTipoPoderLegalBD($this->getObjInfraIBanco());
			return $objBD->reativar($objDTO);

		} catch ( Exception $e ) {
			throw new InfraException ('Erro reativando Tipo Poder Legal, ', $e);
		}
	}
}
?>