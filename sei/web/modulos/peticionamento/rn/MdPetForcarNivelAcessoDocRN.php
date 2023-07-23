<?
/**
* ANATEL
*
* 30/08/2016 - criado por jaqueline.mendes@castgroup.com.br - CAST
*
*/

require_once dirname(__FILE__).'/../../../SEI.php';

class MdPetForcarNivelAcessoDocRN extends InfraRN {
	
	public function __construct() {
		parent::__construct ();
	}
	
	protected function inicializarObjInfraIBanco() {
		return BancoSEI::getInstance ();
	}
	
	protected function cadastrarControlado(MdPetForcarNivelAcessoDocDTO $objMdPetForcarNivelAcessoDocDTO){
		try {
            if($objMdPetForcarNivelAcessoDocDTO){
                (new MdPetForcarNivelAcessoDocBD($this->getObjInfraIBanco()))->cadastrar($objMdPetForcarNivelAcessoDocDTO);
			    return true;
			}
			return false;		
				
		} catch (Exception $e) {
			throw new InfraException ('Erro cadastrando os Tipos de Documento para forçar Nível de Acesso.', $e );
		}
	}
	
	protected function listarConectado(MdPetForcarNivelAcessoDocDTO $objMdPetForcarNivelAcessoDocDTO) {
		try {
		    return (new MdPetForcarNivelAcessoDocBD($this->getObjInfraIBanco()))->listar($objMdPetForcarNivelAcessoDocDTO);
		}catch(Exception $e) {
			throw new InfraException('Erro listando os Tipos de Documento para forçar Nível de Acesso.',$e);
		}
	}

    protected function consultarConectado(MdPetForcarNivelAcessoDocDTO $objMdPetForcarNivelAcessoDocDTO) {

        try {
            return (new MdPetForcarNivelAcessoDocBD($this->getObjInfraIBanco()))->consultar($objMdPetForcarNivelAcessoDocDTO);
        }catch(Exception $e){
            throw new InfraException('Erro listando os Tipos de Documento para forçar Nível de Acesso.',$e);
        }
    }

    protected function alterarControlado(MdPetForcarNivelAcessoDocDTO $objMdPetForcarNivelAcessoDocDTO){
        try {
            return (new MdPetForcarNivelAcessoDocBD($this->getObjInfraIBanco()))->alterar($objMdPetForcarNivelAcessoDocDTO);
        }catch (Exception $e) {
            throw new InfraException('Erro alterando os Tipos de Documento para forçar Nível de Acesso.', $e);
        }
    }

	protected function excluirControlado(MdPetForcarNivelAcessoDocDTO $objMdPetForcarNivelAcessoDocDTO){
		try {
            (new MdPetForcarNivelAcessoDocBD($this->getObjInfraIBanco()))->excluir($objMdPetForcarNivelAcessoDocDTO);
		}catch(Exception $e) {
			throw new InfraException('Erro excluindo os Tipos de Documento para forçar Nível de Acesso.',$e);
		}
	}

    protected function listarHipotesesParametrizadasConectado( $arrIdsHipoteses ) {
        try {

            $objHipoteseLegalDTO = new HipoteseLegalDTO();
            $objHipoteseLegalDTO->retTodos();
            $objHipoteseLegalDTO->setOrd('Nome', InfraDTO::$TIPO_ORDENACAO_ASC);
            $objHipoteseLegalDTO->setNumIdHipoteseLegal($arrIdsHipoteses, InfraDTO::$OPER_IN);
            return (new MdPetForcarNivelAcessoDocBD($this->getObjInfraIBanco()))->listar($objHipoteseLegalDTO);

        } catch(Exception $e) {
            throw new InfraException('Erro listando as Hipóteses Legais.',$e);
        }
    }

}
