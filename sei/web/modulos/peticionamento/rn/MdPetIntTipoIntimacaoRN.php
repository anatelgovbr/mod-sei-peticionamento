<?
/**
 * TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
 *
 * 08/12/2016 - criado por Marcelo Bezerra - CAST
 *
 * Versão do Gerador de Código: 1.39.0
 */

require_once dirname(__FILE__).'/../../../SEI.php';

class MdPetIntTipoIntimacaoRN extends InfraRN {

    public static $EXIGE_RESPOSTA = 'E';
    public static $FACULTATIVA = 'F';
    public static $SEM_RESPOSTA = 'S';

    public function __construct(){
        parent::__construct();
    }

    protected function inicializarObjInfraIBanco(){
        return BancoSEI::getInstance();
    }

    private function validarStrNome(MdPetIntTipoIntimacaoDTO $objMdPetIntTipoIntimacaoDTO, InfraException $objInfraException){
        if (InfraString::isBolVazia($objMdPetIntTipoIntimacaoDTO->getStrNome())){
            $objInfraException->adicionarValidacao('Nome não informado.');
        }else{
            $objMdPetIntTipoIntimacaoDTO->setStrNome(trim($objMdPetIntTipoIntimacaoDTO->getStrNome()));

            if (strlen($objMdPetIntTipoIntimacaoDTO->getStrNome())>70){
                $objInfraException->adicionarValidacao('Nome possui tamanho superior a 70 caracteres.');
            }

            $dto = new MdPetIntTipoIntimacaoDTO();
            $dto->setNumIdMdPetIntTipoIntimacao($objMdPetIntTipoIntimacaoDTO->getNumIdMdPetIntTipoIntimacao(),InfraDTO::$OPER_DIFERENTE);
            $dto->setStrNome($objMdPetIntTipoIntimacaoDTO->getStrNome());
            $dto->retTodos(true);

            $dto = $this->consultar($dto);
            if($dto){
                $objInfraException->adicionarValidacao('Nome já existente.');
            }
        }
    }

    private function validarStrSinAtivo(MdPetIntTipoIntimacaoDTO $objMdPetIntTipoIntimacaoDTO, InfraException $objInfraException){
        if (InfraString::isBolVazia($objMdPetIntTipoIntimacaoDTO->getStrSinAtivo())){
            $objInfraException->adicionarValidacao('Sinalizador de Exclusão Lógica não informado.');
        }else{
            if (!InfraUtil::isBolSinalizadorValido($objMdPetIntTipoIntimacaoDTO->getStrSinAtivo())){
                $objInfraException->adicionarValidacao('Sinalizador de Exclusão Lógica inválido.');
            }
        }
    }

    private function validarStrTipoRespostaAceita(MdPetIntTipoIntimacaoDTO $objMdPetIntTipoIntimacaoDTO, InfraException $objInfraException){
        if ($objMdPetIntTipoIntimacaoDTO->getStrTipoRespostaAceita() == ''){
            $objInfraException->adicionarValidacao('Tipo de Resposta não informado.');
        }
    }

    protected function cadastrarControlado(MdPetIntTipoIntimacaoDTO $objMdPetIntTipoIntimacaoDTO) {
        try{

            //Valida Permissao
            SessaoSEI::getInstance()->validarAuditarPermissao('md_pet_int_tipo_intimacao_cadastrar',__METHOD__,$objMdPetIntTipoIntimacaoDTO);

            //Regras de Negocio
            $objInfraException = new InfraException();

            $this->validarStrNome($objMdPetIntTipoIntimacaoDTO, $objInfraException);
            $this->validarStrSinAtivo($objMdPetIntTipoIntimacaoDTO, $objInfraException);
            $this->validarStrTipoRespostaAceita($objMdPetIntTipoIntimacaoDTO, $objInfraException);
            $rrObjRelMdPetIntTipoRespostaDTO =  $objMdPetIntTipoIntimacaoDTO->getArrObjRelIntRespostaDTO();

            if ($objMdPetIntTipoIntimacaoDTO->getStrTipoRespostaAceita()=='F' || $objMdPetIntTipoIntimacaoDTO->getStrTipoRespostaAceita()=='E'){
                if(count($rrObjRelMdPetIntTipoRespostaDTO) < 1){
                    $objInfraException->adicionarValidacao('Selecione pelo menos 1(um) Tipo de Resposta.');
                }
            }

            $objInfraException->lancarValidacoes();

            $objMdPetIntTipoIntimacaoBD = new MdPetIntTipoIntimacaoBD($this->getObjInfraIBanco());
            $ret = $objMdPetIntTipoIntimacaoBD->cadastrar($objMdPetIntTipoIntimacaoDTO);

            $objMdPetIntRelIntimRespRN = new MdPetIntRelIntimRespRN();
            $objRelMdPetIntTipoRespostaDTO = new MdPetIntRelIntimRespDTO();
            foreach($rrObjRelMdPetIntTipoRespostaDTO as $TipoResposta){
                $objRelMdPetIntTipoRespostaDTO->setNumIdMdPetIntTipoIntimacao($ret->getNumIdMdPetIntTipoIntimacao());
                $objRelMdPetIntTipoRespostaDTO->setNumIdMdPetIntTipoResp($TipoResposta);
                $objRelMdPetIntTipoRespostaDTO->setStrSinAtivo('S');

                $objMdPetIntRelIntimRespRN->cadastrar($objRelMdPetIntTipoRespostaDTO);
            }

            //Auditoria

            return $ret;

        }catch(Exception $e){
            throw new InfraException('Erro cadastrando .',$e);
        }
    }

    protected function alterarControlado(MdPetIntTipoIntimacaoDTO $objMdPetIntTipoIntimacaoDTO){
        try {

            //Valida Permissao
            SessaoSEI::getInstance()->validarAuditarPermissao('md_pet_int_tipo_intimacao_alterar',__METHOD__,$objMdPetIntTipoIntimacaoDTO);

            //Regras de Negocio
            $objInfraException = new InfraException();

            if ($objMdPetIntTipoIntimacaoDTO->isSetStrNome()){
                $this->validarStrNome($objMdPetIntTipoIntimacaoDTO, $objInfraException);
            }

            if ($objMdPetIntTipoIntimacaoDTO->isSetStrSinAtivo()){
                $this->validarStrSinAtivo($objMdPetIntTipoIntimacaoDTO, $objInfraException);
            }

            $arrObjRelMdPetIntTipoRespostaDTO = array();
            if ($objMdPetIntTipoIntimacaoDTO->getStrTipoRespostaAceita()=='F' || $objMdPetIntTipoIntimacaoDTO->getStrTipoRespostaAceita()=='E'){
                $arrObjRelMdPetIntTipoRespostaDTO =  $objMdPetIntTipoIntimacaoDTO->getArrObjRelIntRespostaDTO();
                if(count($arrObjRelMdPetIntTipoRespostaDTO) < 1){
                    $objInfraException->adicionarValidacao('Selecione pelo menos 1(um) Tipo de Resposta.');
                }
            }
            $objInfraException->lancarValidacoes();


            $objMdPetIntTipoIntimacaoBD = new MdPetIntTipoIntimacaoBD($this->getObjInfraIBanco());
            $objMdPetIntTipoIntimacaoBD->alterar($objMdPetIntTipoIntimacaoDTO);

            $objMdPetIntRelIntimRespDTO = new MdPetIntRelIntimRespDTO();
            $objMdPetIntRelIntimRespDTO->setNumIdMdPetIntTipoIntimacao($objMdPetIntTipoIntimacaoDTO->getNumIdMdPetIntTipoIntimacao());
            $objMdPetIntRelIntimRespDTO->retTodos(true);

            $objMdPetIntRelIntimRespRN = new MdPetIntRelIntimRespRN();
            $arrObjMdPetIntRelIntimRespDTO = $objMdPetIntRelIntimRespRN->listar($objMdPetIntRelIntimRespDTO);

            $objMdPetIntRelIntimRespRN->excluir($arrObjMdPetIntRelIntimRespDTO);

            foreach($arrObjRelMdPetIntTipoRespostaDTO as $TipoResposta){
                $objMdPetIntRelIntimRespDTO->setNumIdMdPetIntTipoIntimacao($objMdPetIntTipoIntimacaoDTO->getNumIdMdPetIntTipoIntimacao());
                $objMdPetIntRelIntimRespDTO->setNumIdMdPetIntTipoResp($TipoResposta);
                $objMdPetIntRelIntimRespDTO->setStrSinAtivo('S');

                $objMdPetIntRelIntimRespRN->cadastrar($objMdPetIntRelIntimRespDTO);
            }

            //Auditoria

        }catch(Exception $e){
            throw new InfraException('Erro alterando .',$e);
        }
    }

    protected function excluirControlado($arrObjMdPetIntTipoIntimacaoDTO){
        try {

            //Valida Permissao
            SessaoSEI::getInstance()->validarAuditarPermissao('md_pet_int_tipo_intimacao_excluir',__METHOD__,$arrObjMdPetIntTipoIntimacaoDTO);

            //Regras de Negocio
            //$objInfraException = new InfraException();

            //$objInfraException->lancarValidacoes();

            $objMdPetIntTipoIntimacaoBD = new MdPetIntTipoIntimacaoBD($this->getObjInfraIBanco());
            for($i=0;$i<count($arrObjMdPetIntTipoIntimacaoDTO);$i++){

                $objMdPetIntRelIntimRespDTO = new MdPetIntRelIntimRespDTO();
                $objMdPetIntRelIntimRespDTO->setNumIdMdPetIntTipoIntimacao($arrObjMdPetIntTipoIntimacaoDTO[$i]->getNumIdMdPetIntTipoIntimacao());
                $objMdPetIntRelIntimRespDTO->retTodos(true);

                if($this->_validarExclusaoTipoIntimacao($arrObjMdPetIntTipoIntimacaoDTO[$i]->getNumIdMdPetIntTipoIntimacao())) {
                    $objMdPetIntRelIntimRespRN = new MdPetIntRelIntimRespRN();
                    $arrObjMdPetIntRelIntimRespDTO = $objMdPetIntRelIntimRespRN->listar($objMdPetIntRelIntimRespDTO);
                    $objMdPetIntRelIntimRespRN->excluir($arrObjMdPetIntRelIntimRespDTO);
                    $objMdPetIntTipoIntimacaoBD->excluir($arrObjMdPetIntTipoIntimacaoDTO[$i]);
                }
            }

            //Auditoria

        }catch(Exception $e){
            throw new InfraException('Erro excluindo .',$e);
        }
    }

    private function _validarExclusaoTipoIntimacao($idTipoIntimacao)
    {
        $objMdPetIntimacaoRN = new MdPetIntimacaoRN();
        $objMdPetIntimacaoDTO = new MdPetIntimacaoDTO();
        $objMdPetIntimacaoDTO->setNumIdMdPetIntTipoIntimacao($idTipoIntimacao);
        $objMdPetIntimacaoDTO->retNumIdMdPetIntimacao();
        $permiteExclusao = $objMdPetIntimacaoRN->contar($objMdPetIntimacaoDTO) == 0;

        if (!$permiteExclusao) {
            $nomeTpIntimacao   = $this->_getNomeTipoIntimacao($idTipoIntimacao);
            $objInfraException = new InfraException();
            $objInfraException->adicionarValidacao('O Tipo de Intimação Eletrônica "'.$nomeTpIntimacao.'" não pode ser excluído pois está vinculado à uma Intimação.');
            $objInfraException->lancarValidacoes();
            return false;
        }

        return true;
    }

    private function _getNomeTipoIntimacao($idTipoIntimacao){
        $nome = '';
        $objMdPetIntTipoIntimacaoDTO = new MdPetIntTipoIntimacaoDTO();
        $objMdPetIntTipoIntimacaoDTO->setNumIdMdPetIntTipoIntimacao($idTipoIntimacao);
        $objMdPetIntTipoIntimacaoDTO->retStrNome();

        $objMdPetIntTipoIntimacaoDTO = $this->consultarConectado($objMdPetIntTipoIntimacaoDTO);

        $nome = !is_null($objMdPetIntTipoIntimacaoDTO) ? $objMdPetIntTipoIntimacaoDTO->getStrNome() : '';

        return $nome;
    }

    protected function consultarConectado(MdPetIntTipoIntimacaoDTO $objMdPetIntTipoIntimacaoDTO){
        try {

            //Valida Permissao
            SessaoSEI::getInstance()->validarPermissao('md_pet_int_tipo_intimacao_consultar');

            //Regras de Negocio
            //$objInfraException = new InfraException();

            //$objInfraException->lancarValidacoes();

            $objMdPetIntTipoIntimacaoBD = new MdPetIntTipoIntimacaoBD($this->getObjInfraIBanco());
            $ret = $objMdPetIntTipoIntimacaoBD->consultar($objMdPetIntTipoIntimacaoDTO);

            //Auditoria

            return $ret;
        }catch(Exception $e){
            throw new InfraException('Erro consultando .',$e);
        }
    }

    protected function listarConectado(MdPetIntTipoIntimacaoDTO $objMdPetIntTipoIntimacaoDTO) {
        try {

            //Valida Permissao
            SessaoSEI::getInstance()->validarPermissao('md_pet_int_tipo_intimacao_listar');

            //Regras de Negocio
            //$objInfraException = new InfraException();

            //$objInfraException->lancarValidacoes();

            $objMdPetIntTipoIntimacaoBD = new MdPetIntTipoIntimacaoBD($this->getObjInfraIBanco());
            $ret = $objMdPetIntTipoIntimacaoBD->listar($objMdPetIntTipoIntimacaoDTO);

            //Auditoria

            return $ret;

        }catch(Exception $e){
            throw new InfraException('Erro listando .',$e);
        }
    }

    protected function contarConectado(MdPetIntTipoIntimacaoDTO $objMdPetIntTipoIntimacaoDTO){
        try {

            //Valida Permissao
            SessaoSEI::getInstance()->validarPermissao('md_pet_int_tipo_intimacao_listar');

            //Regras de Negocio
            //$objInfraException = new InfraException();

            //$objInfraException->lancarValidacoes();

            $objMdPetIntTipoIntimacaoBD = new MdPetIntTipoIntimacaoBD($this->getObjInfraIBanco());
            $ret = $objMdPetIntTipoIntimacaoBD->contar($objMdPetIntTipoIntimacaoDTO);

            //Auditoria

            return $ret;
        }catch(Exception $e){
            throw new InfraException('Erro contando .',$e);
        }
    }

    protected function desativarControlado($arrObjMdPetIntTipoIntimacaoDTO){
        try {

            //Valida Permissao
            SessaoSEI::getInstance()->validarAuditarPermissao('md_pet_int_tipo_intimacao_desativar',__METHOD__,$arrObjMdPetIntTipoIntimacaoDTO);

            //Regras de Negocio
            //$objInfraException = new InfraException();

            //$objInfraException->lancarValidacoes();

            $objMdPetIntTipoIntimacaoBD = new MdPetIntTipoIntimacaoBD($this->getObjInfraIBanco());
            for($i=0;$i<count($arrObjMdPetIntTipoIntimacaoDTO);$i++){
                $objMdPetIntTipoIntimacaoBD->desativar($arrObjMdPetIntTipoIntimacaoDTO[$i]);
            }

            //Auditoria

        }catch(Exception $e){
            throw new InfraException('Erro desativando .',$e);
        }
    }

    protected function reativarControlado($arrObjMdPetIntTipoIntimacaoDTO){
        try {

            //Valida Permissao
            SessaoSEI::getInstance()->validarAuditarPermissao('md_pet_int_tipo_intimacao_reativar',__METHOD__,$arrObjMdPetIntTipoIntimacaoDTO);

            //Regras de Negocio
            //$objInfraException = new InfraException();

            //$objInfraException->lancarValidacoes();

            $objMdPetIntTipoIntimacaoBD = new MdPetIntTipoIntimacaoBD($this->getObjInfraIBanco());
            for($i=0;$i<count($arrObjMdPetIntTipoIntimacaoDTO);$i++){
                $objMdPetIntTipoIntimacaoBD->reativar($arrObjMdPetIntTipoIntimacaoDTO[$i]);
            }

            //Auditoria

        }catch(Exception $e){
            throw new InfraException('Erro reativando .',$e);
        }
    }

    protected function bloquearControlado(MdPetIntTipoIntimacaoDTO $objMdPetIntTipoIntimacaoDTO){
        try {

            //Valida Permissao
            SessaoSEI::getInstance()->validarAuditarPermissao('md_pet_int_tipo_intimacao_consultar',__METHOD__,$objMdPetIntTipoIntimacaoDTO);

            //Regras de Negocio
            //$objInfraException = new InfraException();

            //$objInfraException->lancarValidacoes();

            $objMdPetIntTipoIntimacaoBD = new MdPetIntTipoIntimacaoBD($this->getObjInfraIBanco());
            $ret = $objMdPetIntTipoIntimacaoBD->bloquear($objMdPetIntTipoIntimacaoDTO);

            //Auditoria

            return $ret;
        }catch(Exception $e){
            throw new InfraException('Erro bloqueando .',$e);
        }
    }



}
?>