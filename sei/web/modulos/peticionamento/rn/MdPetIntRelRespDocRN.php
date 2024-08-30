<?
/**
* TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
*
* 22/03/2017 - criado por jaqueline.cast
*
* Versão do Gerador de Código: 1.40.1
*/

require_once dirname(__FILE__) . '/../../../SEI.php';

class MdPetIntRelRespDocRN extends InfraRN {

  public function __construct(){
    parent::__construct();
  }

  protected function inicializarObjInfraIBanco(){
    return BancoSEI::getInstance();
  }

  private function validarNumIdMdPetIntRespDocumento(MdPetIntRelRespDocDTO $objMdPetIntRelRespDocDTO, InfraException $objInfraException){
    if (InfraString::isBolVazia($objMdPetIntRelRespDocDTO->getNumIdMdPetIntRespDocumento())){
      $objInfraException->adicionarValidacao(' não informad.');
    }
  }

  private function validarNumIdMdPetIntDestResposta(MdPetIntRelRespDocDTO $objMdPetIntRelRespDocDTO, InfraException $objInfraException){
    if (InfraString::isBolVazia($objMdPetIntRelRespDocDTO->getNumIdMdPetIntDestResposta())){
      $objInfraException->adicionarValidacao(' não informad.');
    }
  }

  private function validarDblIdDocumento(MdPetIntRelRespDocDTO $objMdPetIntRelRespDocDTO, InfraException $objInfraException){
    if (InfraString::isBolVazia($objMdPetIntRelRespDocDTO->getDblIdDocumento())){
      $objInfraException->adicionarValidacao(' não informad.');
    }
  }

  protected function cadastrarControlado(MdPetIntRelRespDocDTO $objMdPetIntRelRespDocDTO) {
    try{

      //Valida Permissao
      SessaoSEI::getInstance()->validarPermissao('md_pet_intimacao_cadastrar');

      //Regras de Negocio
      $objInfraException = new InfraException();

      $this->validarNumIdMdPetIntDestResposta($objMdPetIntRelRespDocDTO, $objInfraException);
      $this->validarDblIdDocumento($objMdPetIntRelRespDocDTO, $objInfraException);

      $objInfraException->lancarValidacoes();

      $objMdPetIntRelRespDocBD = new MdPetIntRelRespDocBD($this->getObjInfraIBanco());
      $ret = $objMdPetIntRelRespDocBD->cadastrar($objMdPetIntRelRespDocDTO);

      //Auditoria

      return $ret;

    }catch(Exception $e){
      throw new InfraException('Erro cadastrando .',$e);
    }
  }

  protected function alterarControlado(MdPetIntRelRespDocDTO $objMdPetIntRelRespDocDTO){
    try {

      //Valida Permissao
  	   SessaoSEI::getInstance()->validarPermissao('md_pet_intimacao_cadastrar');

      //Regras de Negocio
      $objInfraException = new InfraException();

      if ($objMdPetIntRelRespDocDTO->isSetNumIdMdPetIntRespDocumento()){
        $this->validarNumIdMdPetIntRespDocumento($objMdPetIntRelRespDocDTO, $objInfraException);
      }
      if ($objMdPetIntRelRespDocDTO->isSetNumIdMdPetIntDestResposta()){
        $this->validarNumIdMdPetIntDestResposta($objMdPetIntRelRespDocDTO, $objInfraException);
      }
      if ($objMdPetIntRelRespDocDTO->isSetDblIdDocumento()){
        $this->validarDblIdDocumento($objMdPetIntRelRespDocDTO, $objInfraException);
      }

      $objInfraException->lancarValidacoes();

      $objMdPetIntRelRespDocBD = new MdPetIntRelRespDocBD($this->getObjInfraIBanco());
      $objMdPetIntRelRespDocBD->alterar($objMdPetIntRelRespDocDTO);

      //Auditoria

    }catch(Exception $e){
      throw new InfraException('Erro alterando .',$e);
    }
  }

  protected function excluirControlado($arrObjMdPetIntRelRespDocDTO){
    try {

      //Valida Permissao
      SessaoSEI::getInstance()->validarPermissao('md_pet_intimacao_cadastrar');

      //Regras de Negocio
      //$objInfraException = new InfraException();

      //$objInfraException->lancarValidacoes();

      $objMdPetIntRelRespDocBD = new MdPetIntRelRespDocBD($this->getObjInfraIBanco());
      for($i=0;$i<count($arrObjMdPetIntRelRespDocDTO);$i++){
        $objMdPetIntRelRespDocBD->excluir($arrObjMdPetIntRelRespDocDTO[$i]);
      }

      //Auditoria

    }catch(Exception $e){
      throw new InfraException('Erro excluindo .',$e);
    }
  }

  protected function consultarConectado(MdPetIntRelRespDocDTO $objMdPetIntRelRespDocDTO){
    try {

      //Valida Permissao
      SessaoSEI::getInstance()->validarPermissao('md_pet_intimacao_cadastrar');

      //Regras de Negocio
      //$objInfraException = new InfraException();

      //$objInfraException->lancarValidacoes();

      $objMdPetIntRelRespDocBD = new MdPetIntRelRespDocBD($this->getObjInfraIBanco());
      $ret = $objMdPetIntRelRespDocBD->consultar($objMdPetIntRelRespDocDTO);

      //Auditoria

      return $ret;
    }catch(Exception $e){
      throw new InfraException('Erro consultando .',$e);
    }
  }

  protected function listarConectado(MdPetIntRelRespDocDTO $objMdPetIntRelRespDocDTO) {
    try {

      //Valida Permissao
      SessaoSEI::getInstance()->validarPermissao('md_pet_intimacao_cadastrar');

      //Regras de Negocio
      //$objInfraException = new InfraException();

      //$objInfraException->lancarValidacoes();

      $objMdPetIntRelRespDocBD = new MdPetIntRelRespDocBD($this->getObjInfraIBanco());
      $ret = $objMdPetIntRelRespDocBD->listar($objMdPetIntRelRespDocDTO);

      //Auditoria

      return $ret;

    }catch(Exception $e){
      throw new InfraException('Erro listando .',$e);
    }
  }

  protected function contarConectado(MdPetIntRelRespDocDTO $objMdPetIntRelRespDocDTO){
    try {

      //Valida Permissao
      SessaoSEI::getInstance()->validarPermissao('md_pet_intimacao_cadastrar');

      //Regras de Negocio
      //$objInfraException = new InfraException();

      //$objInfraException->lancarValidacoes();

      $objMdPetIntRelRespDocBD = new MdPetIntRelRespDocBD($this->getObjInfraIBanco());
      $ret = $objMdPetIntRelRespDocBD->contar($objMdPetIntRelRespDocDTO);

      //Auditoria

      return $ret;
    }catch(Exception $e){
      throw new InfraException('Erro contando .',$e);
    }
  }

}
?>