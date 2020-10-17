<?
/**
 * ANATEL
 *
 * 22/04/2016 - criado por Marcus Dionisio - ORLE
 *
 */

require_once dirname(__FILE__).'/../../../SEI.php';

class MdPetWsUsuarioExternoRN extends InfraRN {

	public function __construct(){
		parent::__construct();
	}
	
	protected function inicializarObjInfraIBanco(){
		return BancoSEI::getInstance();
	}
	
	protected function consultarUsuarioExterno(MdPetWsUsuarioExternoDTO  $objUsuarioExternoDTO){
		try {
	
			//Valida Permissao
			SessaoSEI::getInstance()->validarAuditarPermissao('usuario_externo_consultar',__METHOD__,$objUsuarioExternoDTO);
	
			$objUsuarioBD = new UsuarioBD($this->getObjInfraIBanco());
			$ret = $objUsuarioBD->consultar($objUsuarioExternoDTO);
	
			return $ret;
		}catch(Exception $e){
			throw new InfraException('Erro consultando Usuário Externo.',$e);
		}
	}
	
	public function consultarExternoControlado($Sigla){
		
		try {
			
			$objInfraException = new InfraException();
	
			$objUsuarioExternoDTO = new MdPetWsUsuarioExternoDTO();
				
			//campos que serão retornados
			$objUsuarioExternoDTO->retNumIdUsuario();
			$objUsuarioExternoDTO->retStrSigla();
			$objUsuarioExternoDTO->retStrNome();
			$objUsuarioExternoDTO->retStrSinAtivo();
			$objUsuarioExternoDTO->retStrStaTipo();
			$objUsuarioExternoDTO->retNumIdContato();
				
			$objUsuarioExternoDTO->retDblRgContato();
			$objUsuarioExternoDTO->retStrOrgaoExpedidorContato();
			$objUsuarioExternoDTO->retStrTelefoneFixo();
			$objUsuarioExternoDTO->retStrTelefoneCelular();
			$objUsuarioExternoDTO->retStrEnderecoContato();
			$objUsuarioExternoDTO->retStrBairroContato();
			
			$objUsuarioExternoDTO->retStrCepContato();
			$objUsuarioExternoDTO->retDthDataCadastroContato();
				
			//Parâmetros para consulta
			$objUsuarioExternoDTO->setStrSigla($Sigla, InfraDTO::$OPER_IGUAL);
	
			$objUsuarioExternoDTO = self::consultarUsuarioExterno($objUsuarioExternoDTO);
				
			if ($objUsuarioExternoDTO==null) {
				$objInfraException->lancarValidacao('Não existe cadastro de Usuário Externo no SEI com o e-mail informado.');
			}
	
			return $objUsuarioExternoDTO;
			 
		} catch(Exception $e){
			throw new InfraException('Erro ao consultar cadastro de Usuário Externo no SEI.',$e);
		}
	}
}
?>