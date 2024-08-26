<?
/**
* TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
*
* 02/04/2018 - criado por jose vieira
*
* Versão do Gerador de Código: 1.41.0
*/

require_once dirname(__FILE__) . '/../../../SEI.php';

class MdPetVinculoRN extends InfraRN {

  public function __construct(){
    parent::__construct();
  }

  protected function inicializarObjInfraIBanco(){
    return BancoSEI::getInstance();
  }

  private function validarNumIdMdPetVinculo(MdPetVinculoDTO $objMdPetVinculoDTO, InfraException $objInfraException){
    if (InfraString::isBolVazia($objMdPetVinculoDTO->getNumIdMdPetVinculo())){
      $objInfraException->adicionarValidacao(' não informad.');
    }
  }

  private function validarNumIdContato(MdPetVinculoDTO $objMdPetVinculoDTO, InfraException $objInfraException){
    if (InfraString::isBolVazia($objMdPetVinculoDTO->getNumIdContato())){
      $objInfraException->adicionarValidacao(' não informad.');
    }
  }

  private function validarStrSinValidado(MdPetVinculoDTO $objMdPetVinculoDTO, InfraException $objInfraException){
    if (InfraString::isBolVazia($objMdPetVinculoDTO->getStrSinValidado())){
      $objInfraException->adicionarValidacao('Sinalizador de  não informado.');
    }else{
      if (!InfraUtil::isBolSinalizadorValido($objMdPetVinculoDTO->getStrSinValidado())){
        $objInfraException->adicionarValidacao('Sinalizador de  inválid.');
      }
    }
  }

  protected function cadastrarControlado(MdPetVinculoDTO $objMdPetVinculoDTO) {
    try{
      //Valida Permissao
      SessaoSEI::getInstance()->validarPermissao('md_pet_vinculacao_cadastrar');
      //Regras de Negocio
      $objInfraException = new InfraException();

      $this->validarNumIdContato($objMdPetVinculoDTO, $objInfraException);
      $this->validarStrSinValidado($objMdPetVinculoDTO, $objInfraException);

      $objInfraException->lancarValidacoes();

      $objMdPetVinculoBD = new MdPetVinculoBD($this->getObjInfraIBanco());
      $ret = $objMdPetVinculoBD->cadastrar($objMdPetVinculoDTO);

      //Auditoria

      return $ret;

    }catch(Exception $e){
      var_dump($e);die;
      throw new InfraException('Erro cadastrando Vínculo.',$e);
    }
  }

  protected function alterarControlado(MdPetVinculoDTO $objMdPetVinculoDTO){
    try {

      //Valida Permissao
      SessaoSEI::getInstance()->validarPermissao('md_pet_vinculacao_alterar');

      //Regras de Negocio
      $objInfraException = new InfraException();

      if ($objMdPetVinculoDTO->isSetNumIdMdPetVinculo()){
        $this->validarNumIdMdPetVinculo($objMdPetVinculoDTO, $objInfraException);
      }
      if ($objMdPetVinculoDTO->isSetNumIdContato()){
        $this->validarNumIdContato($objMdPetVinculoDTO, $objInfraException);
      }
      if ($objMdPetVinculoDTO->isSetStrSinValidado()){
        $this->validarStrSinValidado($objMdPetVinculoDTO, $objInfraException);
      }

      $objInfraException->lancarValidacoes();

      $objMdPetVinculoBD = new MdPetVinculoBD($this->getObjInfraIBanco());
      $objMdPetVinculoBD->alterar($objMdPetVinculoDTO);

      //Auditoria

    }catch(Exception $e){
      throw new InfraException('Erro alterando Vínculo.',$e);
    }
  }

  protected function excluirControlado($arrObjMdPetVinculoDTO){
    try {

      //Valida Permissao
      SessaoSEI::getInstance()->validarPermissao('md_pet_vinculacao_excluir');

      //Regras de Negocio
      //$objInfraException = new InfraException();

      //$objInfraException->lancarValidacoes();

      $objMdPetVinculoBD = new MdPetVinculoBD($this->getObjInfraIBanco());
      for($i=0;$i<count($arrObjMdPetVinculoDTO);$i++){
        $objMdPetVinculoBD->excluir($arrObjMdPetVinculoDTO[$i]);
      }

      //Auditoria

    }catch(Exception $e){
      throw new InfraException('Erro excluindo Vínculo.',$e);
    }
  }

  protected function consultarConectado(MdPetVinculoDTO $objMdPetVinculoDTO){
    try {

      //Valida Permissao
      SessaoSEI::getInstance()->validarPermissao('md_pet_vinculacao_consultar');

      //Regras de Negocio
      //$objInfraException = new InfraException();

      //$objInfraException->lancarValidacoes();

      $objMdPetVinculoBD = new MdPetVinculoBD($this->getObjInfraIBanco());
      $ret = $objMdPetVinculoBD->consultar($objMdPetVinculoDTO);

      //Auditoria

      return $ret;
    }catch(Exception $e){
      throw new InfraException('Erro consultando Vínculo.',$e);
    }
  }

  protected function listarConectado(MdPetVinculoDTO $objMdPetVinculoDTO) {
    try {

      //Valida Permissao
      SessaoSEI::getInstance()->validarPermissao('md_pet_vinculacao_listar');

      //Regras de Negocio
      //$objInfraException = new InfraException();

      //$objInfraException->lancarValidacoes();

      $objMdPetVinculoBD = new MdPetVinculoBD($this->getObjInfraIBanco());
      $ret = $objMdPetVinculoBD->listar($objMdPetVinculoDTO);
      //Auditoria

      return $ret;

    }catch(Exception $e){
      throw new InfraException('Erro listando Vínculo.',$e);
    }
  }

  protected function contarConectado(MdPetVinculoDTO $objMdPetVinculoDTO){
    try {

      //Valida Permissao
      SessaoSEI::getInstance()->validarPermissao('md_pet_vinculacao_listar');

      //Regras de Negocio
      //$objInfraException = new InfraException();

      //$objInfraException->lancarValidacoes();

      $objMdPetVinculoBD = new MdPetVinculoBD($this->getObjInfraIBanco());
      $ret = $objMdPetVinculoBD->contar($objMdPetVinculoDTO);

      //Auditoria

      return $ret;
    }catch(Exception $e){
      throw new InfraException('Erro contando Vínculo.',$e);
    }
  }

  public function consultarProcedimentoVinculoControlado($params){

      $idProcedimento = isset($params[0]) ? $params[0] : null;
      $retornoDTO     = array_key_exists('retornoDTO', $params) ? $params['retornoDTO'] : false;
      $isAtivos       = array_key_exists('isAtivos', $params) ? $params['isAtivos'] : true;

      // Interessado (participamente tipo I)
      $participanteDTO = new ParticipanteDTO();
      $participanteRN  = new ParticipanteRN();

      $participanteDTO->setDblIdProtocolo($idProcedimento);
      $participanteDTO->setStrStaParticipacao('I');
      $participanteDTO->retNumIdContato();

      $arrParticipanteDTO = $participanteRN->listarRN0189($participanteDTO);

      if(count($arrParticipanteDTO)>0) {
        $arrIdContato = InfraArray::converterArrInfraDTO($arrParticipanteDTO,'IdContato');

        if(count($arrIdContato)>0) {
            // Vinculação PJ
            $objMdPetVinculoDTO = new MdPetVinculoDTO();
            $objMdPetVinculoRN = new MdPetVinculoRN();

            $objMdPetVinculoDTO->retNumIdMdPetVinculo();
            $objMdPetVinculoDTO->retNumIdMdPetVinculoRepresent();
            $objMdPetVinculoDTO->setNumIdContato($arrIdContato,InfraDTO::$OPER_IN);

            if ($isAtivos){
                $objMdPetVinculoDTO->setStrStaEstado(MdPetVincRepresentantRN::$RP_ATIVO);
            }

            $arrObjMdPetVinculoDTO = $objMdPetVinculoRN->listar($objMdPetVinculoDTO);
        }

        if(count($arrObjMdPetVinculoDTO) > 0) {
            if ($retornoDTO) {
                return $arrObjMdPetVinculoDTO;
            } else {
                return true;
            }
        }
      }

      return false;

  }

  public function consultarDocumentoVinculoControlado($params){

      $idProcedimento = isset($params[0]) ? $params[0] : null;
      $idDocumento    = isset($params[1]) ? $params[1] : null;
      $retornoDTO     = array_key_exists('retornoDTO', $params) ? $params['retornoDTO'] : false;

      $objMdPetVincDocumentoDTO = new MdPetVincDocumentoDTO();
      $objMdPetVincDocumentoDTO->setDblIdProcedimentoVinculo($idProcedimento);
      $objMdPetVincDocumentoDTO->setDblIdDocumento($idDocumento);
      $objMdPetVincDocumentoDTO->retNumIdMdPetVinculo();

      $objMdPetVincDocumentoRN = new MdPetVincDocumentoRN();
      $arrObjMdPetVincDocumentoDTO = $objMdPetVincDocumentoRN->listar($objMdPetVincDocumentoDTO);

      if(count($arrObjMdPetVincDocumentoDTO)>0){
          if ($retornoDTO){
              return $arrObjMdPetVincDocumentoDTO;
          }else{
              return true;
          }
      }
      return false;
  }
  

  public function consultarDocumentosVinculoControlado($params){

      $idProcedimento = isset($params[0]) ? $params[0] : null;

      // Documento do Vinculo - Principal (último)
      $objMdPetVincDocumentoRN = new MdPetVincDocumentoRN;

      $objMdPetVincDocumentoDTO = new MdPetVincDocumentoDTO();
      $objMdPetVincDocumentoDTO->retStrProtocoloFormatadoProtocolo();
      $objMdPetVincDocumentoDTO->retStrNomeSerieProtocolo();
      $objMdPetVincDocumentoDTO->retStrNumeroDocumento();
      $objMdPetVincDocumentoDTO->retStrTipoDocumento();
      $objMdPetVincDocumentoDTO->setDblIdProcedimentoVinculo($idProcedimento);
      $objMdPetVincDocumentoDTO->setStrTipoDocumento(MdPetVincDocumentoRN::$TP_PROTOCOLO_PRINCIPAL);
      $objMdPetVincDocumentoDTO->setOrdNumIdMdPetVincDocumento(InfraDTO::$TIPO_ORDENACAO_DESC);
      $objMdPetVincDocumentoDTO->setNumMaxRegistrosRetorno(1);

      $arrObjMdPetVincDocumentoDTOPrinc = $objMdPetVincDocumentoRN->listar($objMdPetVincDocumentoDTO);

      // Documento do Vinculo - Demais
      $objMdPetVincDocumentoDTO->setStrTipoDocumento( MdPetVincDocumentoRN::$TP_PROTOCOLO_ATOS);
      $objMdPetVincDocumentoDTO->setOrdNumIdMdPetVincDocumento(InfraDTO::$TIPO_ORDENACAO_ASC);
      $objMdPetVincDocumentoDTO->unSetNumMaxRegistrosRetorno();
      
      $arrObjMdPetVincDocumentoDTO = $objMdPetVincDocumentoRN->listar($objMdPetVincDocumentoDTO);
      
      $arrObjMdPetVincDocumentoDTO = array_merge($arrObjMdPetVincDocumentoDTOPrinc, $arrObjMdPetVincDocumentoDTO);
      
      return $arrObjMdPetVincDocumentoDTO;
  }

  protected function consultarIdProcedimentoByUnidadeControlado($idUnidadeAtual){

      $protocoloDTO = new ProtocoloDTO();
      $protocoloRN = new ProtocoloRN();

      $protocoloDTO->setNumIdUnidadeGeradora($idUnidadeAtual);
      $protocoloDTO->setStrStaProtocolo(ProtocoloRN::$TP_PROCEDIMENTO);
      $protocoloDTO->retDblIdProtocolo();

      $protocolo = $protocoloRN->listarRN0668($protocoloDTO);
      $arrIds = array();
      if(count($protocolo)>0){


        for($i = 0 ; $i < count($protocolo) ; $i++){
            $arrIds[]= $protocolo[$i]->getDblIdProtocolo();
        }

        return $arrIds;

      }
      return $arrIds;
  }



/* 
  protected function desativarControlado($arrObjMdPetVinculoDTO){
    try {

      //Valida Permissao
      ::getInstance()->validarPermissao('md_pet_vinculacao_desativar');

      //Regras de Negocio
      //$objInfraException = new InfraException();

      //$objInfraException->lancarValidacoes();

      $objMdPetVinculoBD = new MdPetVinculoBD($this->getObjInfraIBanco());
      for($i=0;$i<count($arrObjMdPetVinculoDTO);$i++){
        $objMdPetVinculoBD->desativar($arrObjMdPetVinculoDTO[$i]);
      }

      //Auditoria

    }catch(Exception $e){
      throw new InfraException('Erro desativando Vínculo.',$e);
    }
  }

  protected function reativarControlado($arrObjMdPetVinculoDTO){
    try {

      //Valida Permissao
      ::getInstance()->validarPermissao('md_pet_vinculacao_reativar');

      //Regras de Negocio
      //$objInfraException = new InfraException();

      //$objInfraException->lancarValidacoes();

      $objMdPetVinculoBD = new MdPetVinculoBD($this->getObjInfraIBanco());
      for($i=0;$i<count($arrObjMdPetVinculoDTO);$i++){
        $objMdPetVinculoBD->reativar($arrObjMdPetVinculoDTO[$i]);
      }

      //Auditoria

    }catch(Exception $e){
      throw new InfraException('Erro reativando Vínculo.',$e);
    }
  }

  protected function bloquearControlado(MdPetVinculoDTO $objMdPetVinculoDTO){
    try {

      //Valida Permissao
      ::getInstance()->validarPermissao('md_pet_vinculacao_consultar');

      //Regras de Negocio
      //$objInfraException = new InfraException();

      //$objInfraException->lancarValidacoes();

      $objMdPetVinculoBD = new MdPetVinculoBD($this->getObjInfraIBanco());
      $ret = $objMdPetVinculoBD->bloquear($objMdPetVinculoDTO);

      //Auditoria

      return $ret;
    }catch(Exception $e){
      throw new InfraException('Erro bloqueando Vínculo.',$e);
    }
  }

 */
}
