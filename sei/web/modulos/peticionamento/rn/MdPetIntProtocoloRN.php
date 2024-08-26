<?
/**
 * TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
 *
 * 14/03/2017 - criado por pedro.cast
 *
 * Versão do Gerador de Código: 1.40.0
 */

require_once dirname(__FILE__).'/../../../SEI.php';

class MdPetIntProtocoloRN extends InfraRN {

    //Vars para Sta Tipo de Documento na Intimação
    public static $TP_INT_DOC_ANEXO      = 'A';
    public static $TP_INT_DOC_PRINCIPAL  = 'P';
    public static $TP_INT_DOC_DISPONIVEL = 'D';

    public function __construct(){
        parent::__construct();
    }

    protected function inicializarObjInfraIBanco(){
        return BancoSEI::getInstance();
    }

    private function validarStrSinPrincipal(MdPetIntProtocoloDTO $objMdPetIntDocumentoDTO, InfraException $objInfraException){
        if (InfraString::isBolVazia($objMdPetIntDocumentoDTO->getStrSinPrincipal())){
            $objInfraException->adicionarValidacao('Sinalizador de Documento Principal não informado.');
        }else{
            if (!InfraUtil::isBolSinalizadorValido($objMdPetIntDocumentoDTO->getStrSinPrincipal())){
                $objInfraException->adicionarValidacao('Sinalizador de Documento Principal inválido.');
            }
        }
    }

    private function validarNumIdMdPetIntimacao(MdPetIntProtocoloDTO $objMdPetIntDocumentoDTO, InfraException $objInfraException){
        if (InfraString::isBolVazia($objMdPetIntDocumentoDTO->getNumIdMdPetIntimacao())){
            $objInfraException->adicionarValidacao('Intimação não informada.');
        }
    }

    private function validarDblIdProtocolo(MdPetIntProtocoloDTO $objMdPetIntDocumentoDTO, InfraException $objInfraException){
        if (InfraString::isBolVazia($objMdPetIntDocumentoDTO->getDblIdProtocolo())){
            $objInfraException->adicionarValidacao('Protocolo não informado.');
        }
    }

    protected function cadastrarControlado(MdPetIntProtocoloDTO $objMdPetIntDocumentoDTO) {
        try{

            //Valida Permissao
            SessaoSEI::getInstance()->validarPermissao('md_pet_int_documento_cadastrar');

            //Regras de Negocio
            $objInfraException = new InfraException();

            $this->validarStrSinPrincipal($objMdPetIntDocumentoDTO, $objInfraException);
            $this->validarNumIdMdPetIntimacao($objMdPetIntDocumentoDTO, $objInfraException);
            $this->validarDblIdProtocolo($objMdPetIntDocumentoDTO, $objInfraException);

            $objInfraException->lancarValidacoes();

            $objMdPetIntProtocoloBD = new MdPetIntProtocoloBD($this->getObjInfraIBanco());
            $ret = $objMdPetIntProtocoloBD->cadastrar($objMdPetIntDocumentoDTO);

            //Auditoria

            return $ret;

        }catch(Exception $e){
            throw new InfraException('Erro cadastrando Documento da Intimação.',$e);
        }
    }

    protected function alterarControlado(MdPetIntProtocoloDTO $objMdPetIntDocumentoDTO){
        try {

            //Valida Permissao
            SessaoSEI::getInstance()->validarPermissao('md_pet_int_documento_alterar');

            //Regras de Negocio
            $objInfraException = new InfraException();

            if ($objMdPetIntDocumentoDTO->isSetStrSinPrincipal()){
                $this->validarStrSinPrincipal($objMdPetIntDocumentoDTO, $objInfraException);
            }
            if ($objMdPetIntDocumentoDTO->isSetNumIdMdPetIntimacao()){
                $this->validarNumIdMdPetIntimacao($objMdPetIntDocumentoDTO, $objInfraException);
            }
            if ($objMdPetIntDocumentoDTO->isSetDblIdDocumento()){
                $this->validarDblIdProtocolo($objMdPetIntDocumentoDTO, $objInfraException);
            }

            $objInfraException->lancarValidacoes();

            $objMdPetIntProtocoloBD = new MdPetIntProtocoloBD($this->getObjInfraIBanco());
            $objMdPetIntProtocoloBD->alterar($objMdPetIntDocumentoDTO);

            //Auditoria

        }catch(Exception $e){
            throw new InfraException('Erro alterando Documento da Intimação.',$e);
        }
    }

    protected function excluirControlado($arrObjMdPetIntDocumentoDTO){
        try {

            //Valida Permissao
            SessaoSEI::getInstance()->validarPermissao('md_pet_int_documento_excluir');

            //Regras de Negocio
            //$objInfraException = new InfraException();

            //$objInfraException->lancarValidacoes();

            $objMdPetIntProtocoloBD = new MdPetIntProtocoloBD($this->getObjInfraIBanco());
            for($i=0;$i<count($arrObjMdPetIntDocumentoDTO);$i++){
                $objMdPetIntProtocoloBD->excluir($arrObjMdPetIntDocumentoDTO[$i]);
            }

            //Auditoria

        }catch(Exception $e){
            throw new InfraException('Erro excluindo Documento da Intimação.',$e);
        }
    }

    protected function consultarConectado(MdPetIntProtocoloDTO $objMdPetIntDocumentoDTO){
        try {

            //Valida Permissao
            SessaoSEI::getInstance()->validarPermissao('md_pet_int_documento_consultar');

            //Regras de Negocio
            //$objInfraException = new InfraException();

            //$objInfraException->lancarValidacoes();

            $objMdPetIntProtocoloBD = new MdPetIntProtocoloBD($this->getObjInfraIBanco());
            $ret = $objMdPetIntProtocoloBD->consultar($objMdPetIntDocumentoDTO);

            //Auditoria

            return $ret;
        }catch(Exception $e){
            throw new InfraException('Erro consultando Documento da Intimação.',$e);
        }
    }

    protected function listarConectado(MdPetIntProtocoloDTO $objMdPetIntDocumentoDTO) {
        try {

            //Valida Permissao
            SessaoSEI::getInstance()->validarPermissao('md_pet_int_documento_listar');

            //Regras de Negocio
            //$objInfraException = new InfraException();

            //$objInfraException->lancarValidacoes();

            $objMdPetIntProtocoloBD = new MdPetIntProtocoloBD($this->getObjInfraIBanco());
            $ret = $objMdPetIntProtocoloBD->listar($objMdPetIntDocumentoDTO);

            //Auditoria

            return $ret;

        }catch(Exception $e){
            throw new InfraException('Erro listando Documentos da Intimação.',$e);
        }
    }

    protected function contarConectado(MdPetIntProtocoloDTO $objMdPetIntDocumentoDTO){
        try {

            //Valida Permissao - retirada, pois contagem deve ser permitida a todos, para atender "moverDocumento"
            //SessaoSEI::getInstance()->validarPermissao('md_pet_int_documento_listar');

            //Regras de Negocio
            //$objInfraException = new InfraException();

            //$objInfraException->lancarValidacoes();

            $objMdPetIntProtocoloBD = new MdPetIntProtocoloBD($this->getObjInfraIBanco());
            $ret = $objMdPetIntProtocoloBD->contar($objMdPetIntDocumentoDTO);

            //Auditoria

            return $ret;
        }catch(Exception $e){
            throw new InfraException('Erro contando Documentos da Intimação.',$e);
        }
    }


    protected function getArrIdsProtocoloIntimacaoConectado($arrParams){

        $idAcessoExt = $arrParams[0];
        $arrIdsProtocolo = array();
        $objAcessoExternoRN = new AcessoExternoRN();

        $objAcessoExternoDTO = new AcessoExternoDTO();
        $objAcessoExternoDTO->setNumIdAcessoExterno($idAcessoExt);

        $objAcessoExternoDTO = $objAcessoExternoRN->consultarProcessoAcessoExterno($objAcessoExternoDTO);
        $objProcedimentoDTO  = $objAcessoExternoDTO->getObjProcedimentoDTO();
        $arrObjRelProtocoloProtocoloDTO = $objProcedimentoDTO->getArrObjRelProtocoloProtocoloDTO();

        foreach ($arrObjRelProtocoloProtocoloDTO as $objRelProtocoloProtocoloDTO) {

            $objProtocolo = $objRelProtocoloProtocoloDTO->getObjProtocoloDTO2();

            if( $objProtocolo != null){

                $idArr = $objProtocolo->isSetDblIdDocumento() ? $objProtocolo->getDblIdDocumento() : $objProtocolo->getDblIdProcedimento();
                $arrIdsProtocolo[] = $idArr;

            }
        }

        return $arrIdsProtocolo;
    }

    protected function verificaProcessoEAnexoIntimacaoConectado($idProcedimento){
        //Valida Permissao
        $objMdPetIntProtocoloDTO = new MdPetIntProtocoloDTO();
        $objMdPetIntProtocoloDTO->setDblIdProtocolo($idProcedimento);
        $objMdPetIntProtocoloDTO->retNumIdMdPetIntProtocolo();

        return $this->contar($objMdPetIntProtocoloDTO) > 0;
       
    }

}
?>