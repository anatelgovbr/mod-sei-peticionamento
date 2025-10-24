<?
/**
 * TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
 *
 * 14/03/2017 - criado por pedro.cast
 *
 * Versão do Gerador de Código: 1.40.0
 */

require_once dirname(__FILE__).'/../../../SEI.php';

class MdPetIntProtDisponivelRN extends InfraRN {

    public function __construct(){
        parent::__construct();
    }

    protected function inicializarObjInfraIBanco(){
        return BancoSEI::getInstance();
    }

    private function validarNumIdMdPetIntimacao(MdPetIntProtDisponivelDTO $objMdPetIntDocDisponivelDTO, InfraException $objInfraException){
        if (InfraString::isBolVazia($objMdPetIntDocDisponivelDTO->getNumIdMdPetIntimacao())){
            $objInfraException->adicionarValidacao('Intimação não informada.');
        }
    }

    private function validarDblIdProtocolo(MdPetIntProtDisponivelDTO $objMdPetIntDocDisponivelDTO, InfraException $objInfraException){
        if (InfraString::isBolVazia($objMdPetIntDocDisponivelDTO->getDblIdProtocolo())){
            $objInfraException->adicionarValidacao('Documento não informado.');
        }
    }

    protected function cadastrarControlado(MdPetIntProtDisponivelDTO $objMdPetIntDocDisponivelDTO) {
        try{

            //Valida Permissao
            SessaoSEI::getInstance()->validarPermissao('md_pet_int_doc_disponivel_cadastrar');

            //Regras de Negocio
            $objInfraException = new InfraException();

            $this->validarNumIdMdPetIntimacao($objMdPetIntDocDisponivelDTO, $objInfraException);
            $this->validarDblIdProtocolo($objMdPetIntDocDisponivelDTO, $objInfraException);

            $objInfraException->lancarValidacoes();

            $objMdPetIntProtDisponivelBD = new MdPetIntProtDisponivelBD($this->getObjInfraIBanco());
            $ret = $objMdPetIntProtDisponivelBD->cadastrar($objMdPetIntDocDisponivelDTO);

            //Auditoria

            return $ret;

        }catch(Exception $e){
            throw new InfraException('Erro cadastrando Documento Disponível.',$e);
        }
    }

    protected function alterarControlado(MdPetIntProtDisponivelDTO $objMdPetIntDocDisponivelDTO){
        try {

            //Valida Permissao
            SessaoSEI::getInstance()->validarPermissao('md_pet_int_doc_disponivel_alterar');

            //Regras de Negocio
            $objInfraException = new InfraException();

            if ($objMdPetIntDocDisponivelDTO->isSetNumIdMdPetIntimacao()){
                $this->validarNumIdMdPetIntimacao($objMdPetIntDocDisponivelDTO, $objInfraException);
            }
            if ($objMdPetIntDocDisponivelDTO->isSetDblIdDocumento()){
                $this->validarDblIdDocumento($objMdPetIntDocDisponivelDTO, $objInfraException);
            }

            $objInfraException->lancarValidacoes();

            $objMdPetIntProtDisponivelBD = new MdPetIntProtDisponivelBD($this->getObjInfraIBanco());
            $objMdPetIntProtDisponivelBD->alterar($objMdPetIntDocDisponivelDTO);

            //Auditoria

        }catch(Exception $e){
            throw new InfraException('Erro alterando Documento Disponível.',$e);
        }
    }

    protected function excluirControlado($arrObjMdPetIntDocDisponivelDTO){
        try {

            //Valida Permissao
            SessaoSEI::getInstance()->validarPermissao('md_pet_int_doc_disponivel_excluir');

            //Regras de Negocio
            //$objInfraException = new InfraException();

            //$objInfraException->lancarValidacoes();

            $objMdPetIntProtDisponivelBD = new MdPetIntProtDisponivelBD($this->getObjInfraIBanco());
            for($i=0;$i<count($arrObjMdPetIntDocDisponivelDTO);$i++){
                $objMdPetIntProtDisponivelBD->excluir($arrObjMdPetIntDocDisponivelDTO[$i]);
            }

            //Auditoria

        }catch(Exception $e){
            throw new InfraException('Erro excluindo Documento Disponível.',$e);
        }
    }

    protected function consultarConectado(MdPetIntProtDisponivelDTO $objMdPetIntDocDisponivelDTO){
        try {

            //Valida Permissao
            SessaoSEI::getInstance()->validarPermissao('md_pet_int_doc_disponivel_consultar');

            //Regras de Negocio
            //$objInfraException = new InfraException();

            //$objInfraException->lancarValidacoes();

            $objMdPetIntProtDisponivelBD = new MdPetIntProtDisponivelBD($this->getObjInfraIBanco());
            $ret = $objMdPetIntProtDisponivelBD->consultar($objMdPetIntDocDisponivelDTO);

            //Auditoria

            return $ret;
        }catch(Exception $e){
            throw new InfraException('Erro consultando Documento Disponível.',$e);
        }
    }

    protected function listarConectado(MdPetIntProtDisponivelDTO $objMdPetIntDocDisponivelDTO) {
        try {

            //Valida Permissao
            SessaoSEI::getInstance()->validarPermissao('md_pet_int_doc_disponivel_listar');

            //Regras de Negocio
            //$objInfraException = new InfraException();

            //$objInfraException->lancarValidacoes();

            $objMdPetIntProtDisponivelBD = new MdPetIntProtDisponivelBD($this->getObjInfraIBanco());
            $ret = $objMdPetIntProtDisponivelBD->listar($objMdPetIntDocDisponivelDTO);

            //Auditoria

            return $ret;

        }catch(Exception $e){
            throw new InfraException('Erro listando Documentos Disponíveis.',$e);
        }
    }

    protected function contarConectado(MdPetIntProtDisponivelDTO $objMdPetIntDocDisponivelDTO){
        try {

            //Valida Permissao - retirada, pois contagem deve ser permitida a todos, para atender "moverDocumento"
            //SessaoSEI::getInstance()->validarPermissao('md_pet_int_doc_disponivel_listar');

            //Regras de Negocio
            //$objInfraException = new InfraException();

            //$objInfraException->lancarValidacoes();

            $objMdPetIntProtDisponivelBD = new MdPetIntProtDisponivelBD($this->getObjInfraIBanco());
            $ret = $objMdPetIntProtDisponivelBD->contar($objMdPetIntDocDisponivelDTO);

            //Auditoria

            return $ret;
        }catch(Exception $e){
            throw new InfraException('Erro contando Documentos Disponíveis.',$e);
        }
    }

    protected function verificaProcessoEDocDisponivelIntimacaoConectado($idProcedimento){
        //Valida Permissao
        $objMdPetIntProtDisponivelDTO = new MdPetIntProtDisponivelDTO();
        $objMdPetIntProtDisponivelDTO->setDblIdProtocolo($idProcedimento);
        $objMdPetIntProtDisponivelDTO->retNumIdMdPetIntimacao();

        return $this->contar($objMdPetIntProtDisponivelDTO) > 0;

    }

}
?>