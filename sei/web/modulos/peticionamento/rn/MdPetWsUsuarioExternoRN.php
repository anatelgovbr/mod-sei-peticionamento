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
			throw new InfraException('Erro consultando Usu�rio Externo.',$e);
		}
	}

    protected function listarUsuarioExterno(MdPetWsUsuarioExternoDTO  $objUsuarioExternoDTO){
        try {

            //Valida Permissao
            SessaoSEI::getInstance()->validarAuditarPermissao('usuario_externo_consultar',__METHOD__,$objUsuarioExternoDTO);

            $objUsuarioBD = new UsuarioBD($this->getObjInfraIBanco());
            $ret = $objUsuarioBD->listar($objUsuarioExternoDTO);

            return $ret;
        }catch(Exception $e){
            throw new InfraException('Erro consultando Usu�rio Externo.',$e);
        }
    }
	
	public function consultarExterno($Cpf, $Sigla = ""){
		
		try {
			
			$objInfraException = new InfraException();
	
			$objUsuarioExternoDTO = new MdPetWsUsuarioExternoDTO();
				
			//campos que ser�o retornados
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
				
			//Par�metros para consulta
			$objUsuarioExternoDTO->setStrCpf($Cpf, InfraDTO::$OPER_IGUAL);
			if($Sigla != "") {
                $objUsuarioExternoDTO->setStrSigla($Sigla, InfraDTO::$OPER_IGUAL);
            }
			$objUsuarioExternoDTO = self::listarUsuarioExterno($objUsuarioExternoDTO);
				
			if ($objUsuarioExternoDTO==null) {
			    if($Sigla != "") {
                    $objInfraException->lancarValidacao('N�o existe cadastro de Usu�rio Externo no SEI com o E-mail e CPF informados.');
                } else {
                    $objInfraException->lancarValidacao('N�o existe cadastro de Usu�rio Externo no SEI com o CPF informados.');
                }
			}
	
			return $objUsuarioExternoDTO;
			 
		} catch(Exception $e){
			throw new InfraException('Erro ao consultar cadastro de Usu�rio Externo no SEI.',$e);
		}
	}
}
?>