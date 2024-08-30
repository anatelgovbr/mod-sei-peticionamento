<?
/**
 * TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
 *
 * 14/03/2017 - criado por pedro.cast
 *
 * Versão do Gerador de Código: 1.40.0
 */

require_once dirname(__FILE__).'/../../../SEI.php';

class MdPetIntRelTipoRespRN extends InfraRN {

    public function __construct(){
        parent::__construct();
    }

    protected function inicializarObjInfraIBanco(){
        return BancoSEI::getInstance();
    }

    private function validarStrSinAtivo(MdPetIntRelTipoRespDTO $objMdPetIntRelTipoRespDTO, InfraException $objInfraException){
        if (InfraString::isBolVazia($objMdPetIntRelTipoRespDTO->getStrSinAtivo())){
            $objInfraException->adicionarValidacao('Sinalizador de Exclusão Lógica não informado.');
        }else{
            if (!InfraUtil::isBolSinalizadorValido($objMdPetIntRelTipoRespDTO->getStrSinAtivo())){
                $objInfraException->adicionarValidacao('Sinalizador de Exclusão Lógica inválido.');
            }
        }
    }

    private function validarNumIdMdPetIntimacao(MdPetIntRelTipoRespDTO $objMdPetIntRelTipoRespDTO, InfraException $objInfraException){
        if (InfraString::isBolVazia($objMdPetIntRelTipoRespDTO->getNumIdMdPetIntimacao())){
            $objInfraException->adicionarValidacao('Intimação não informada.');
        }
    }

    private function validarNumIdMdPetIntTipoResp(MdPetIntRelTipoRespDTO $objMdPetIntRelTipoRespDTO, InfraException $objInfraException){
        if (InfraString::isBolVazia($objMdPetIntRelTipoRespDTO->getNumIdMdPetIntTipoResp())){
            $objInfraException->adicionarValidacao('Tipo de Resposta não informado.');
        }
    }

    protected function cadastrarControlado(MdPetIntRelTipoRespDTO $objMdPetIntRelTipoRespDTO) {
        try{

            //Valida Permissao
            SessaoSEI::getInstance()->validarPermissao('md_pet_int_rel_tipo_resp_cadastrar');

            //Regras de Negocio
            $objInfraException = new InfraException();

            $this->validarStrSinAtivo($objMdPetIntRelTipoRespDTO, $objInfraException);
            $this->validarNumIdMdPetIntimacao($objMdPetIntRelTipoRespDTO, $objInfraException);
            $this->validarNumIdMdPetIntTipoResp($objMdPetIntRelTipoRespDTO, $objInfraException);

            $objInfraException->lancarValidacoes();

            $objMdPetIntRelTipoRespBD = new MdPetIntRelTipoRespBD($this->getObjInfraIBanco());
            $ret = $objMdPetIntRelTipoRespBD->cadastrar($objMdPetIntRelTipoRespDTO);

            //Auditoria

            return $ret;

        }catch(Exception $e){
            throw new InfraException('Erro cadastrando Tipo De Resposta da Intimação.',$e);
        }
    }

    protected function alterarControlado(MdPetIntRelTipoRespDTO $objMdPetIntRelTipoRespDTO){
        try {

            //Valida Permissao
            SessaoSEI::getInstance()->validarPermissao('md_pet_int_rel_tipo_resp_alterar');

            //Regras de Negocio
            $objInfraException = new InfraException();

            if ($objMdPetIntRelTipoRespDTO->isSetStrSinAtivo()){
                $this->validarStrSinAtivo($objMdPetIntRelTipoRespDTO, $objInfraException);
            }
            if ($objMdPetIntRelTipoRespDTO->isSetNumIdMdPetIntimacao()){
                $this->validarNumIdMdPetIntimacao($objMdPetIntRelTipoRespDTO, $objInfraException);
            }
            if ($objMdPetIntRelTipoRespDTO->isSetNumIdMdPetIntTipoResp()){
                $this->validarNumIdMdPetIntTipoResp($objMdPetIntRelTipoRespDTO, $objInfraException);
            }

            $objInfraException->lancarValidacoes();

            $objMdPetIntRelTipoRespBD = new MdPetIntRelTipoRespBD($this->getObjInfraIBanco());
            $objMdPetIntRelTipoRespBD->alterar($objMdPetIntRelTipoRespDTO);

            //Auditoria

        }catch(Exception $e){
            throw new InfraException('Erro alterando Tipo De Resposta da Intimação.',$e);
        }
    }

    protected function excluirControlado($arrObjMdPetIntRelTipoRespDTO){
        try {

            //Valida Permissao
            SessaoSEI::getInstance()->validarPermissao('md_pet_int_rel_tipo_resp_excluir');

            //Regras de Negocio
            //$objInfraException = new InfraException();

            //$objInfraException->lancarValidacoes();

            $objMdPetIntRelTipoRespBD = new MdPetIntRelTipoRespBD($this->getObjInfraIBanco());
            for($i=0;$i<count($arrObjMdPetIntRelTipoRespDTO);$i++){
                $objMdPetIntRelTipoRespBD->excluir($arrObjMdPetIntRelTipoRespDTO[$i]);
            }

            //Auditoria

        }catch(Exception $e){
            throw new InfraException('Erro excluindo Tipo De Resposta da Intimação.',$e);
        }
    }

    protected function consultarConectado(MdPetIntRelTipoRespDTO $objMdPetIntRelTipoRespDTO){
        try {

            //Valida Permissao
            SessaoSEI::getInstance()->validarPermissao('md_pet_int_rel_tipo_resp_consultar');

            //Regras de Negocio
            //$objInfraException = new InfraException();

            //$objInfraException->lancarValidacoes();

            $objMdPetIntRelTipoRespBD = new MdPetIntRelTipoRespBD($this->getObjInfraIBanco());
            $ret = $objMdPetIntRelTipoRespBD->consultar($objMdPetIntRelTipoRespDTO);

            //Auditoria

            return $ret;
        }catch(Exception $e){
            throw new InfraException('Erro consultando Tipo De Resposta da Intimação.',$e);
        }
    }

    protected function listarConectado(MdPetIntRelTipoRespDTO $objMdPetIntRelTipoRespDTO) {
        try {

            //Valida Permissao
            SessaoSEI::getInstance()->validarPermissao('md_pet_int_rel_tipo_resp_listar');

            //Regras de Negocio
            //$objInfraException = new InfraException();

            //$objInfraException->lancarValidacoes();

            $objMdPetIntRelTipoRespBD = new MdPetIntRelTipoRespBD($this->getObjInfraIBanco());
            $ret = $objMdPetIntRelTipoRespBD->listar($objMdPetIntRelTipoRespDTO);

            //Auditoria

            return $ret;

        }catch(Exception $e){
            throw new InfraException('Erro listando Tipos de Resposta da Intimação.',$e);
        }
    }

    protected function contarConectado(MdPetIntRelTipoRespDTO $objMdPetIntRelTipoRespDTO){
        try {

            //Valida Permissao
            SessaoSEI::getInstance()->validarPermissao('md_pet_int_rel_tipo_resp_listar');

            //Regras de Negocio
            //$objInfraException = new InfraException();

            //$objInfraException->lancarValidacoes();

            $objMdPetIntRelTipoRespBD = new MdPetIntRelTipoRespBD($this->getObjInfraIBanco());
            $ret = $objMdPetIntRelTipoRespBD->contar($objMdPetIntRelTipoRespDTO);

            //Auditoria

            return $ret;
        }catch(Exception $e){
            throw new InfraException('Erro contando Tipos de Resposta da Intimação.',$e);
        }
    }

    protected function desativarControlado($arrObjMdPetIntRelTipoRespDTO){
        try {

            //Valida Permissao
            SessaoSEI::getInstance()->validarPermissao('md_pet_int_rel_tipo_resp_desativar');

            //Regras de Negocio
            //$objInfraException = new InfraException();

            //$objInfraException->lancarValidacoes();

            $objMdPetIntRelTipoRespBD = new MdPetIntRelTipoRespBD($this->getObjInfraIBanco());
            for($i=0;$i<count($arrObjMdPetIntRelTipoRespDTO);$i++){
                $objMdPetIntRelTipoRespBD->desativar($arrObjMdPetIntRelTipoRespDTO[$i]);
            }

            //Auditoria

        }catch(Exception $e){
            throw new InfraException('Erro desativando Tipo De Resposta da Intimação.',$e);
        }
    }

    protected function reativarControlado($arrObjMdPetIntRelTipoRespDTO){
        try {

            //Valida Permissao
            SessaoSEI::getInstance()->validarPermissao('md_pet_int_rel_tipo_resp_reativar');

            //Regras de Negocio
            //$objInfraException = new InfraException();

            //$objInfraException->lancarValidacoes();

            $objMdPetIntRelTipoRespBD = new MdPetIntRelTipoRespBD($this->getObjInfraIBanco());
            for($i=0;$i<count($arrObjMdPetIntRelTipoRespDTO);$i++){
                $objMdPetIntRelTipoRespBD->reativar($arrObjMdPetIntRelTipoRespDTO[$i]);
            }

            //Auditoria

        }catch(Exception $e){
            throw new InfraException('Erro reativando Tipo De Resposta da Intimação.',$e);
        }
    }

    protected function bloquearControlado(MdPetIntRelTipoRespDTO $objMdPetIntRelTipoRespDTO){
        try {

            //Valida Permissao
            SessaoSEI::getInstance()->validarPermissao('md_pet_int_rel_tipo_resp_consultar');

            //Regras de Negocio
            //$objInfraException = new InfraException();

            //$objInfraException->lancarValidacoes();

            $objMdPetIntRelTipoRespBD = new MdPetIntRelTipoRespBD($this->getObjInfraIBanco());
            $ret = $objMdPetIntRelTipoRespBD->bloquear($objMdPetIntRelTipoRespDTO);

            //Auditoria

            return $ret;
        }catch(Exception $e){
            throw new InfraException('Erro bloqueando Tipo De Resposta da Intimação.',$e);
        }
    }

    protected function listarTipoRespostaConectado($params)
    {
        $numIdMdPetIntimacao   = $params[0];
        $numIdMdPetIntRelDest  = $params[1];

        $objMdPetIntRelTipoRespDTO = new MdPetIntRelTipoRespDTO();
        $objMdPetIntRelTipoRespDTO->retTodos(true);
        
        if(is_array($numIdMdPetIntimacao)){
            $objMdPetIntRelTipoRespDTO->setNumIdMdPetIntimacao($numIdMdPetIntimacao, InfraDTO::$OPER_IN);
        }else{
            $objMdPetIntRelTipoRespDTO->setNumIdMdPetIntimacao($numIdMdPetIntimacao);
        }        

        if (!is_null($numIdMdPetIntRelDest)){
            if(is_array($numIdMdPetIntRelDest)){
                $objMdPetIntRelTipoRespDTO->setNumIdMdPetIntRelDest($numIdMdPetIntRelDest, InfraDTO::$OPER_IN);
            }else{
                $objMdPetIntRelTipoRespDTO->setNumIdMdPetIntRelDest($numIdMdPetIntRelDest);
            }
        }
        $objMdPetIntRelTipoRespDTO->setStrSinAtivo('S');
        return $this->listar($objMdPetIntRelTipoRespDTO);
    }


    protected function listarTipoRespostaExternoConectado($params)
    {
        
        $numIdMdPetIntimacao   = $params[0];
        $numIdMdPetIntRelDest  = $params[1];

        $objMdPetIntRelTipoRespDTO = new MdPetIntRelTipoRespDTO();
        $objMdPetIntRelTipoRespDTO->retTodos(true);
        
        if(is_array($numIdMdPetIntimacao)){
                
                $objMdPetIntRelDestinatarioDTO = new MdPetIntRelDestinatarioDTO();
                $objMdPetIntRelDestinatarioDTO->setNumIdMdPetIntRelDestinatario($numIdMdPetIntRelDest);
                $objMdPetIntRelDestinatarioDTO->setNumIdMdPetIntimacao($numIdMdPetIntimacao,InfraDTO::$OPER_IN);
                $objMdPetIntRelDestinatarioDTO->retNumIdMdPetIntRelDestinatario();
                $objMdPetIntRelDestinatarioDTO->retNumIdMdPetIntimacao();
                $objMdPetIntRelDestinatarioRN  = new MdPetIntRelDestinatarioRN();
                $objMdPetIntRelDestinatarioDTO = $objMdPetIntRelDestinatarioRN->listar($objMdPetIntRelDestinatarioDTO);
                $intimacao = InfraArray::converterArrInfraDTO($objMdPetIntRelDestinatarioDTO, 'IdMdPetIntimacao');
                

            $objMdPetIntRelTipoRespDTO->setNumIdMdPetIntimacao($intimacao, InfraDTO::$OPER_IN);
        }else{
            $objMdPetIntRelTipoRespDTO->setNumIdMdPetIntimacao($numIdMdPetIntimacao);
        }        

        if (!is_null($numIdMdPetIntRelDest)){
            if(is_array($numIdMdPetIntRelDest)){
                $objMdPetIntRelTipoRespDTO->setNumIdMdPetIntRelDest($numIdMdPetIntRelDest, InfraDTO::$OPER_IN);
            }else{
                $objMdPetIntRelTipoRespDTO->setNumIdMdPetIntRelDest($numIdMdPetIntRelDest);
            }
        }
        $objMdPetIntRelTipoRespDTO->setStrSinAtivo('S');
        
        return $this->listar($objMdPetIntRelTipoRespDTO);
    }

    


    protected function validarExclusaoTipoRespostaConectado($idTipoResposta)
    {
        $objMdPetIntRelTpRespDTO = new MdPetIntRelTipoRespDTO();
        $objMdPetIntRelTpRespRN = new MdPetIntRelTipoRespRN();

        $objMdPetIntRelTpRespDTO->setNumIdMdPetIntTipoResp($idTipoResposta);
        $objMdPetIntRelTpRespDTO->retNumIdMdPetIntTipoResp();

        $existeResp = $objMdPetIntRelTpRespRN->contar($objMdPetIntRelTpRespDTO) > 0 ? 1 : 0;

        return $existeResp;
    }


}
?>