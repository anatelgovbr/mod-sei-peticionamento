<?
/**
 * 
 *
 * 02/10/2016 - criado por CAST
 *
 * 
 */

require_once dirname(__FILE__) . '/../../../SEI.php';

class MdPetForcarNivelAcessoDocRelTipoDocRN extends InfraRN
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

            $objMdPetIntPrazoTacitaRelTipoProcBD = new MdPetIntPrazoTacitaRelTipoProcBD($this->getObjInfraIBanco());
            return $objMdPetIntPrazoTacitaRelTipoProcBD->cadastrar($objMdPetIntPrazoTacitaRelTipoProcDTO);

        } catch (Exception $e) {
            throw new InfraException('Erro cadastrando .', $e);
        }
    }

    protected function alterarControlado(MdPetIntPrazoTacitaRelTipoProcDTO $objMdPetIntPrazoTacitaRelTipoProcDTO)
    {
        try {

            $objMdPetIntPrazoTacitaRelTipoProcBD = new MdPetIntPrazoTacitaRelTipoProcBD($this->getObjInfraIBanco());
	        $objMdPetIntPrazoTacitaRelTipoProcBD->alterar($objMdPetIntPrazoTacitaRelTipoProcDTO);

        } catch (Exception $e) {
            throw new InfraException('Erro alterando .', $e);
        }
    }

    protected function consultarConectado(MdPetIntPrazoTacitaRelTipoProcDTO $objMdPetIntPrazoTacitaRelTipoProcDTO)
    {
        try {

            $objMdPetIntPrazoTacitaRelTipoProcBD = new MdPetIntPrazoTacitaRelTipoProcBD($this->getObjInfraIBanco());
            $ret = $objMdPetIntPrazoTacitaRelTipoProcBD->consultar($objMdPetIntPrazoTacitaRelTipoProcDTO);

            return $ret;
            
        } catch (Exception $e) {
            throw new InfraException('Erro consultando .', $e);
        }
    }

    protected function listarConectado(MdPetIntPrazoTacitaRelTipoProcDTO $objMdPetIntPrazoTacitaRelTipoProcDTO)
    {
        try {
        	
        	$objMdPetIntPrazoTacitaRelTipoProcBD = new MdPetIntPrazoTacitaRelTipoProcBD($this->getObjInfraIBanco());
            $ret = $objMdPetIntPrazoTacitaRelTipoProcBD->listar($objMdPetIntPrazoTacitaRelTipoProcDTO);

            return $ret;

        } catch (Exception $e) {
            throw new InfraException('Erro listando .', $e);
        }
    }

    protected function contarConectado(MdPetIntPrazoTacitaRelTipoProcDTO $objMdPetIntPrazoTacitaRelTipoProcDTO)
    {
        try {

            $objMdPetIntPrazoTacitaRelTipoProcBD = new MdPetIntPrazoTacitaRelTipoProcBD($this->getObjInfraIBanco());
            $ret = $objMdPetIntPrazoTacitaRelTipoProcBD->contar($objMdPetIntPrazoTacitaRelTipoProcDTO);
        	
            return $ret;
            
        } catch (Exception $e) {
            throw new InfraException('Erro contando .', $e);
        }
    }
	
	protected function excluirControlado($arrObjMdPetIntPrazoTacitaRelTipoProcDTO){
		try {
			
			$objMdPetIntPrazoTacitaRelTipoProcBD = new MdPetIntPrazoTacitaRelTipoProcBD($this->getObjInfraIBanco());
			for($i = 0; $i < count($arrObjMdPetIntPrazoTacitaRelTipoProcDTO); $i++){
				$objMdPetIntPrazoTacitaRelTipoProcBD->excluir($arrObjMdPetIntPrazoTacitaRelTipoProcDTO[$i]);
			}
			
		}catch(Exception $e){
			throw new InfraException('Erro excluindo .',$e);
		}
	}
	
	protected function excluirRelacionamentosExistentesControlado(){
		
		BancoSEI::getInstance()->executarSql("DELETE FROM md_pet_prz_tac_rel_tp_proc");
		
	}
	
}

?>