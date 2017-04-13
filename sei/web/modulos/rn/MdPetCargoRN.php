<?
/**
* ANATEL
*
* 06/12/2016 - criado por marcelo.bezerra - CAST
*
*/

require_once dirname(__FILE__).'/../../../SEI.php';

class MdPetCargoRN extends InfraRN {
	
	public function __construct() {
		parent::__construct ();
	}
	
	protected function inicializarObjInfraIBanco() {
		return BancoSEI::getInstance ();
	}
	
	protected function listarDistintosConectado() {
			
		try {
	
			//Regras de Negocio
			$objInfraException = new InfraException();

			// Masculinos
			$objCargoMascDTO = new CargoDTO();
			$objCargoMascDTO->setDistinct(true);
			$objCargoMascDTO->setStrSinAtivo('S');
			$objCargoMascDTO->retNumIdCargo();
			$objCargoMascDTO->retStrExpressao();
			$objCargoMascDTO->adicionarCriterio(
				array('StaGenero')
				, array(InfraDTO::$OPER_IGUAL)
				, array('M')
				, NULL
			);
  			$objCargoMascRN = new CargoRN();
  			$arrObjCargoMascDTO = $objCargoMascRN->listarRN0302($objCargoMascDTO);
			$arrCargoMasc = InfraArray::converterArrInfraDTO($arrObjCargoMascDTO,'Expressao');
			$arrCargoIdMasc = InfraArray::converterArrInfraDTO($arrObjCargoMascDTO,'IdCargo');

			// Nulos
			$objCargoNuloDTO = new CargoDTO();
			$objCargoNuloDTO->setDistinct(true);
			$objCargoNuloDTO->setStrSinAtivo('S');
			$objCargoNuloDTO->retNumIdCargo();
			$objCargoNuloDTO->retStrExpressao();
			$objCargoNuloDTO->adicionarCriterio(
				array('StaGenero')
				, array(InfraDTO::$OPER_IGUAL)
				, array(NULL)
			);
			if (count($arrCargoMasc)>0){
				$objCargoNuloDTO->adicionarCriterio(
					array('Expressao')
					, array(InfraDTO::$OPER_NOT_IN)
					, array($arrCargoMasc)
				);
			};
			$objCargoNuloRN = new CargoRN();
			$arrObjCargoNuloDTO = $objCargoNuloRN->listarRN0302($objCargoNuloDTO);  			
			$arrCargoNulo = InfraArray::converterArrInfraDTO($arrObjCargoNuloDTO,'Expressao');
			$arrCargoIdNulo = InfraArray::converterArrInfraDTO($arrObjCargoNuloDTO,'IdCargo');

			// Femininos
			$objCargoFemDTO = new CargoDTO();
			$objCargoFemDTO->setDistinct(true);
			$objCargoFemDTO->setStrSinAtivo('S');
			$objCargoFemDTO->retNumIdCargo();
			$objCargoFemDTO->retStrExpressao();
			$objCargoFemDTO->adicionarCriterio(
				array('StaGenero')
				, array(InfraDTO::$OPER_IGUAL)
				, array('F')
			);
			if (count($arrCargoMasc)>0){
				$objCargoFemDTO->adicionarCriterio(
					array('Expressao')
					, array(InfraDTO::$OPER_NOT_IN)
					, array($arrCargoMasc)
				);
			};
			if (count($arrCargoNulo)>0){
				$objCargoFemDTO->adicionarCriterio(
					array('Expressao')
					, array(InfraDTO::$OPER_NOT_IN)
					, array($arrCargoNulo)
				);
			};
			$objCargoFemRN = new CargoRN();
			$arrObjCargoFemDTO = $objCargoFemRN->listarRN0302($objCargoFemDTO);  			
			$arrCargoIdFem = InfraArray::converterArrInfraDTO($arrObjCargoFemDTO,'IdCargo');

			// Resultado ordenado
			$arrCargo = array_merge($arrCargoIdMasc,$arrCargoIdNulo); 
			$arrCargo = array_merge($arrCargo,$arrCargoIdFem);
  			
			$objCargoDTO = new CargoDTO();
			$objCargoDTO->retNumIdCargo();
			$objCargoDTO->retStrExpressao();
			$objCargoDTO->adicionarCriterio(
				array('IdCargo')
				, array(InfraDTO::$OPER_IN)
				, array($arrCargo)
				, NULL
			);
			$objCargoDTO->setOrdStrExpressao(InfraDTO::$TIPO_ORDENACAO_ASC);
  			$objCargoRN = new CargoRN();
  			$arrObjCargoDTO = $objCargoRN->listarRN0302($objCargoDTO);
			
  			return $arrObjCargoDTO;	
			
			//Auditoria
		}catch(Exception $e){
			throw new InfraException('Erro pesquisando Contato.',$e);
		}
	}	
}
?>