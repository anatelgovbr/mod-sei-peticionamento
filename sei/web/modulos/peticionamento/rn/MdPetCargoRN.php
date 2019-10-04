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

			$objUsuarioDTO = new UsuarioDTO();
			if (!empty(SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno())){
				$idUsuario = SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno();	
				$objUsuarioDTO->setStrStaTipo(array(UsuarioRN::$TU_EXTERNO), InfraDTO::$OPER_IN);
			}else{
				$idUsuario = SessaoSEI::getInstance()->getNumIdUsuario();
			}

			$objUsuarioDTO->setBolExclusaoLogica(false);
			$objUsuarioDTO->retNumIdContato();
			$objUsuarioDTO->setNumIdUsuario($idUsuario);
			$objUsuarioDTO->setNumMaxRegistrosRetorno(1);

			$objUsuarioRN = new UsuarioRN();
			$arrDadosUsuario = $objUsuarioRN->consultarRN0489($objUsuarioDTO);

			$contatoDTO = new ContatoDTO();
			$contatoDTO->setBolExclusaoLogica(false);
			$contatoDTO->retNumIdContato();
			$contatoDTO->retStrNome();
			$contatoDTO->retStrStaGenero();
			$contatoDTO->setNumIdContato($arrDadosUsuario->getNumIdContato());

			$objContatoRN = new ContatoRN();
			$arrContato = $objContatoRN->consultarRN0324($contatoDTO);
			$staGenero = $arrContato->getStrStaGenero();

			$objCargoDTO = new CargoDTO();

			$objCargoDTO->setDistinct(true);
			$objCargoDTO->setStrSinAtivo('S');
			$objCargoDTO->retNumIdCargo();
			$objCargoDTO->retStrExpressao();
			$objCargoDTO->setOrdStrExpressao(InfraDTO::$TIPO_ORDENACAO_ASC);

			if(is_null($staGenero)){
				$objCargoDTO->setStrStaGenero( array('M', 'F', null), InfraDTO::$OPER_IN);
			}else{
				$objCargoDTO->setStrStaGenero(array($staGenero, null), InfraDTO::$OPER_IN);
			}

			$objCargoMascRN = new CargoRN();
			$arrObjCargoDTO = $objCargoMascRN->listarRN0302($objCargoDTO);
			$arrCargoIdFem = InfraArray::converterArrInfraDTO($arrObjCargoDTO,'IdCargo', 'Expressao');

			return $arrCargoIdFem;
			
			//Auditoria
		}catch(Exception $e){
			throw new InfraException('Erro pesquisando Contato.',$e);
		}
	}

}
?>