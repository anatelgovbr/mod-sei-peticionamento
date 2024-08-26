<?
/**
* TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
*
* 02/04/2018 - criado por jose vieira
*
* Versão do Gerador de Código: 1.41.0
*/

require_once dirname(__FILE__) . '/../../../SEI.php';

class MdPetVincDocumentoRN extends InfraRN {


  public static $TP_PROTOCOLO_ATOS = 'A';
  public static $TP_PROTOCOLO_PRINCIPAL = 'P';
  public static $TP_PROTOCOLO_RECIBO = 'R';
  public static $TP_PROTOCOLO_PROCURACAO_ESPECIAL = 'E';
  public static $TP_PROTOCOLO_PROCURACAO = 'N';

  public static $TP_PROTOCOLO_SUSPENSAO = 'S';
  public static $TP_PROTOCOLO_RESTABELECIMENTO = 'T';
  public static $TP_PROTOCOLO_DILIGENCIA_SUSPENSAO = 'D';
  public static $TP_PROTOCOLO_DILIGENCIA_RESTABELECIMENTO = 'I';

  //Representação tipo de disvinculo
  public static $TP_ATO_REVOGACAO = 'V';
  public static $TP_ATO_RENUNCIA  = 'R';

  public function __construct(){
    parent::__construct();
  }

  protected function inicializarObjInfraIBanco(){
    return BancoSEI::getInstance();
  }

  private function validarNumIdMdPetVincDocumento(MdPetVincDocumentoDTO $objMdPetVincDocumentoDTO, InfraException $objInfraException){
    if (InfraString::isBolVazia($objMdPetVincDocumentoDTO->getNumIdMdPetVincDocumento())){
      $objInfraException->adicionarValidacao('IdMdPetVincDocumento não informad.');
    }
  }

  private function validarDblIdDocumento(MdPetVincDocumentoDTO $objMdPetVincDocumentoDTO, InfraException $objInfraException){
    if (InfraString::isBolVazia($objMdPetVincDocumentoDTO->getDblIdDocumento())){
      $objInfraException->adicionarValidacao('IdDocumento não informad.');
    }
  }

  private function validarStrTipoDocumento(MdPetVincDocumentoDTO $objMdPetVincDocumentoDTO, InfraException $objInfraException){
    if (InfraString::isBolVazia($objMdPetVincDocumentoDTO->getStrTipoDocumento())){
      $objInfraException->adicionarValidacao('TipoDocumento não informad.');
    }else{
      $objMdPetVincDocumentoDTO->setStrTipoDocumento(trim($objMdPetVincDocumentoDTO->getStrTipoDocumento()));

      if (strlen($objMdPetVincDocumentoDTO->getStrTipoDocumento())>1){
        $objInfraException->adicionarValidacao('TipoDocumento possui tamanho superior a 1 caracteres.');
      }
    }
  }

  protected function cadastrarControlado(MdPetVincDocumentoDTO $objMdPetVincDocumentoDTO) {
    try{

      //Valida Permissao
      SessaoSEI::getInstance()->validarPermissao('md_pet_vinc_documento_cadastrar');

      //Regras de Negocio
      $objInfraException = new InfraException();

      $this->validarDblIdDocumento($objMdPetVincDocumentoDTO, $objInfraException);
      //$this->validarNumIdMdPetVinculo($objMdPetVincDocumentoDTO, $objInfraException);
      $this->validarStrTipoDocumento($objMdPetVincDocumentoDTO, $objInfraException);

      $objInfraException->lancarValidacoes();

      $objMdPetVincDocumentoBD = new MdPetVincDocumentoBD($this->getObjInfraIBanco());
      $ret = $objMdPetVincDocumentoBD->cadastrar($objMdPetVincDocumentoDTO);

      //Auditoria

      return $ret;

    }catch(Exception $e){
      throw new InfraException('Erro cadastrando Documento do Vínculo.',$e);
    }
  }

  protected function alterarControlado(MdPetVincDocumentoDTO $objMdPetVincDocumentoDTO){
    try {

      //Valida Permissao
      SessaoSEI::getInstance()->validarPermissao('md_pet_vinc_documento_alterar');

      //Regras de Negocio
      $objInfraException = new InfraException();

      if ($objMdPetVincDocumentoDTO->isSetNumIdMdPetVincDocumento()){
        $this->validarNumIdMdPetVincDocumento($objMdPetVincDocumentoDTO, $objInfraException);
      }
      if ($objMdPetVincDocumentoDTO->isSetDblIdDocumento()){
        $this->validarDblIdDocumento($objMdPetVincDocumentoDTO, $objInfraException);
      }
      if ($objMdPetVincDocumentoDTO->isSetStrTipoDocumento()){
        $this->validarStrTipoDocumento($objMdPetVincDocumentoDTO, $objInfraException);
      }

      $objInfraException->lancarValidacoes();

      $objMdPetVincDocumentoBD = new MdPetVincDocumentoBD($this->getObjInfraIBanco());
      $objMdPetVincDocumentoBD->alterar($objMdPetVincDocumentoDTO);

      //Auditoria

    }catch(Exception $e){
      throw new InfraException('Erro alterando Documento do Vínculo.',$e);
    }
  }

  protected function excluirControlado($arrObjMdPetVincDocumentoDTO){
    try {

      //Valida Permissao
      SessaoSEI::getInstance()->validarPermissao('md_pet_vinc_documento_excluir');

      //Regras de Negocio
      //$objInfraException = new InfraException();

      //$objInfraException->lancarValidacoes();

      $objMdPetVincDocumentoBD = new MdPetVincDocumentoBD($this->getObjInfraIBanco());
      for($i=0;$i<count($arrObjMdPetVincDocumentoDTO);$i++){
      	$objMdPetVincDocumentoBD->excluir($arrObjMdPetVincDocumentoDTO[$i]);
      }

      //Auditoria

    }catch(Exception $e){
      throw new InfraException('Erro excluindo Documento do Vínculo.',$e);
    }
  }

  protected function consultarConectado(MdPetVincDocumentoDTO $objMdPetVincDocumentoDTO){
    try {

      //Valida Permissao
      SessaoSEI::getInstance()->validarPermissao('md_pet_vinc_documento_consultar');

      //Regras de Negocio
      //$objInfraException = new InfraException();

      //$objInfraException->lancarValidacoes();

      $objMdPetVincDocumentoBD = new MdPetVincDocumentoBD($this->getObjInfraIBanco());
      $ret = $objMdPetVincDocumentoBD->consultar($objMdPetVincDocumentoDTO);

      //Auditoria

      return $ret;
    }catch(Exception $e){
      throw new InfraException('Erro consultando Documento do Vínculo.',$e);
    }
  }

  protected function listarConectado(MdPetVincDocumentoDTO $objMdPetVincDocumentoDTO) {
    try {

      //Valida Permissao
      SessaoSEI::getInstance()->validarPermissao('md_pet_vinc_documento_listar');

      //Regras de Negocio
      //$objInfraException = new InfraException();

      //$objInfraException->lancarValidacoes();

      $objMdPetVincDocumentoBD = new MdPetVincDocumentoBD($this->getObjInfraIBanco());
      $ret = $objMdPetVincDocumentoBD->listar($objMdPetVincDocumentoDTO);
      //Auditoria

      return $ret;

    }catch(Exception $e){
      throw new InfraException('Erro listando Documento do Vínculo.',$e);
    }
  }

  protected function contarConectado(MdPetVincDocumentoDTO $objMdPetVincDocumentoDTO){
    try {

      //Valida Permissao
      SessaoSEI::getInstance()->validarPermissao('md_pet_vinc_documento_listar');

      //Regras de Negocio
      //$objInfraException = new InfraException();

      //$objInfraException->lancarValidacoes();

      $objMdPetVincDocumentoBD = new MdPetVincDocumentoBD($this->getObjInfraIBanco());
      $ret = $objMdPetVincDocumentoBD->contar($objMdPetVincDocumentoDTO);

      //Auditoria

      return $ret;
    }catch(Exception $e){
      throw new InfraException('Erro contando Documento do Vínculo.',$e);
    }
  }

  protected function getDocumentosRepresentantesConectado($idsRepresentantes){
    $idsDocumentos = null;

    if(!empty($idsRepresentantes) && is_countable($idsRepresentantes)){
	    $objMdPetVincDocumentoDTO = new MdPetVincDocumentoDTO();
	    $objMdPetVincDocumentoDTO->setNumIdMdPetVinculoRepresent($idsRepresentantes, InfraDTO::$OPER_IN);
	    $objMdPetVincDocumentoDTO->retDblIdDocumento();

	    $count = $this->contar($objMdPetVincDocumentoDTO);

	    if($count > 0){
		    $objs = $this->listar($objMdPetVincDocumentoDTO);
		    $idsDocumentos = InfraArray::converterArrInfraDTO($objs, 'IdDocumento');
	    }
    }
    
    return $idsDocumentos;
  }
/* 
  protected function desativarControlado($arrObjMdPetVincDocumentoDTO){
    try {

      //Valida Permissao
      ::getInstance()->validarPermissao('md_pet_vinc_Documento_desativar');

      //Regras de Negocio
      //$objInfraException = new InfraException();

      //$objInfraException->lancarValidacoes();

      $objMdPetVincDocumentoBD = new MdPetVincDocumentoBD($this->getObjInfraIBanco());
      for($i=0;$i<count($arrObjMdPetVincDocumentoDTO);$i++){
        $objMdPetVincDocumentoBD->desativar($arrObjMdPetVincDocumentoDTO[$i]);
      }

      //Auditoria

    }catch(Exception $e){
      throw new InfraException('Erro desativando Documento do Vínculo.',$e);
    }
  }

  protected function reativarControlado($arrObjMdPetVincDocumentoDTO){
    try {

      //Valida Permissao
      ::getInstance()->validarPermissao('md_pet_vinc_Documento_reativar');

      //Regras de Negocio
      //$objInfraException = new InfraException();

      //$objInfraException->lancarValidacoes();

      $objMdPetVincDocumentoBD = new MdPetVincDocumentoBD($this->getObjInfraIBanco());
      for($i=0;$i<count($arrObjMdPetVincDocumentoDTO);$i++){
        $objMdPetVincDocumentoBD->reativar($arrObjMdPetVincDocumentoDTO[$i]);
      }

      //Auditoria

    }catch(Exception $e){
      throw new InfraException('Erro reativando Documento do Vínculo.',$e);
    }
  }

  protected function bloquearControlado(MdPetVincDocumentoDTO $objMdPetVincDocumentoDTO){
    try {

      //Valida Permissao
      ::getInstance()->validarPermissao('md_pet_vinc_Documento_consultar');

      //Regras de Negocio
      //$objInfraException = new InfraException();

      //$objInfraException->lancarValidacoes();

      $objMdPetVincDocumentoBD = new MdPetVincDocumentoBD($this->getObjInfraIBanco());
      $ret = $objMdPetVincDocumentoBD->bloquear($objMdPetVincDocumentoDTO);

      //Auditoria

      return $ret;
    }catch(Exception $e){
      throw new InfraException('Erro bloqueando Documento do Vínculo.',$e);
    }
  }

 */
}
