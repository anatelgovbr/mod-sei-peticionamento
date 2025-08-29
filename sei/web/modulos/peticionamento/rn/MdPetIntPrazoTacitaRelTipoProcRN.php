<?
/**
 * 
 *
 * 02/10/2016 - criado por CAST
 *
 * 
 */

require_once dirname(__FILE__) . '/../../../SEI.php';

class MdPetIntPrazoTacitaRelTipoProcRN extends InfraRN
{

    public function __construct()
    {
        parent::__construct();
    }

    protected function inicializarObjInfraIBanco()
    {
        return BancoSEI::getInstance();
    }

    protected function cadastrarControlado(MdPetIntPrazoTacitaRelTipoProcDTO $objMdPetIntPrazoTacitaRelTipoProcDTO)
    {
        try {

            // Valida Permissao
			// SessaoSEI::getInstance()->validarPermissao('md_pet_int_aceite_cadastrar');

            $objMdPetIntPrazoTacitaRelTipoProcBD = new MdPetIntPrazoTacitaRelTipoProcBD($this->getObjInfraIBanco());
            return $objMdPetIntPrazoTacitaRelTipoProcBD->cadastrar($objMdPetIntPrazoTacitaRelTipoProcDTO);

        } catch (Exception $e) {
            throw new InfraException('Erro cadastrando .', $e);
        }
    }

    protected function alterarControlado(MdPetIntPrazoTacitaRelTipoProcDTO $objMdPetIntPrazoTacitaRelTipoProcDTO)
    {
        try {

            // Valida Permissao
			// SessaoSEI::getInstance()->validarAuditarPermissao('md_pet_int_tipo_resp_alterar',__METHOD__,$objMdPetIntTipoRespDTO);
	
	        $objMdPetIntPrazoTacitaRelTipoProcBD = new MdPetIntPrazoTacitaRelTipoProcBD($this->getObjInfraIBanco());
	        $objMdPetIntPrazoTacitaRelTipoProcBD->alterar($objMdPetIntPrazoTacitaRelTipoProcDTO);

            // Auditoria

        } catch (Exception $e) {
            throw new InfraException('Erro alterando .', $e);
        }
    }

    protected function consultarConectado(MdPetIntPrazoTacitaRelTipoProcDTO $objMdPetIntPrazoTacitaRelTipoProcDTO)
    {
        try {

            // Valida Permissao
			// SessaoSEI::getInstance()->validarPermissao('');
	
	        $objMdPetIntPrazoTacitaRelTipoProcBD = new MdPetIntPrazoTacitaRelTipoProcBD($this->getObjInfraIBanco());
            $ret = $objMdPetIntPrazoTacitaRelTipoProcBD->consultar($objMdPetIntPrazoTacitaRelTipoProcDTO);

            //Auditoria

            return $ret;
            
        } catch (Exception $e) {
            throw new InfraException('Erro consultando .', $e);
        }
    }

    protected function listarConectado(MdPetIntPrazoTacitaRelTipoProcDTO $objMdPetIntPrazoTacitaRelTipoProcDTO)
    {
        try {

            // Valida Permissao
			// SessaoSEI::getInstance()->validarPermissao('md_pet_int_tipo_resp_listar');

            // Regras de Negocio
            // $objInfraException = new InfraException();

            // $objInfraException->lancarValidacoes();
	
	        $objMdPetIntPrazoTacitaRelTipoProcBD = new MdPetIntPrazoTacitaRelTipoProcBD($this->getObjInfraIBanco());
            $ret = $objMdPetIntPrazoTacitaRelTipoProcBD->listar($objMdPetIntPrazoTacitaRelTipoProcDTO);

            //Auditoria

            return $ret;

        } catch (Exception $e) {
            throw new InfraException('Erro listando .', $e);
        }
    }

    protected function contarConectado(MdPetIntPrazoTacitaRelTipoProcDTO $objMdPetIntPrazoTacitaRelTipoProcDTO)
    {
        try {

            // Valida Permissao
			// SessaoSEI::getInstance()->validarPermissao('md_pet_int_tipo_resp_listar');

            //Regras de Negocio
            //$objInfraException = new InfraException();

            //$objInfraException->lancarValidacoes();
	
	        $objMdPetIntPrazoTacitaRelTipoProcBD = new MdPetIntPrazoTacitaRelTipoProcBD($this->getObjInfraIBanco());
            $ret = $objMdPetIntPrazoTacitaRelTipoProcBD->contar($objMdPetIntPrazoTacitaRelTipoProcDTO);
        	
            //Auditoria

            return $ret;
            
        } catch (Exception $e) {
            throw new InfraException('Erro contando .', $e);
        }
    }
	
	protected function excluirControlado($arrObjMdPetIntPrazoTacitaRelTipoProcDTO){
		try {
			
			//Valida Permissao
			// SessaoSEI::getInstance()->validarPermissao('md_lit_rel_controle_motivo_excluir');
			
			//Regras de Negocio
			//$objInfraException = new InfraException();
			//$objInfraException->lancarValidacoes();
			
			$objMdPetIntPrazoTacitaRelTipoProcBD = new MdPetIntPrazoTacitaRelTipoProcBD($this->getObjInfraIBanco());
			for($i = 0; $i < count($arrObjMdPetIntPrazoTacitaRelTipoProcDTO); $i++){
				$objMdPetIntPrazoTacitaRelTipoProcBD->excluir($arrObjMdPetIntPrazoTacitaRelTipoProcDTO[$i]);
			}
			
			//Auditoria
			
		}catch(Exception $e){
			throw new InfraException('Erro excluindo .',$e);
		}
	}
	
	protected function excluirRelacionamentosExistentesControlado(){
		
		BancoSEI::getInstance()->executarSql("DELETE FROM md_pet_prz_tac_rel_tp_proc");
		
	}
	
}

?>