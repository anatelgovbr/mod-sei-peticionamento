<?
/**
* TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
*
* 21/06/2023 - criado por michaelr.colab
*
* Versão do Gerador de Código: 1.43.2
*/

require_once dirname(__FILE__).'/../SEI.php';

class MdPetRelContSitRfRN extends InfraRN {

  public function __construct(){
    parent::__construct();
  }

  protected function inicializarObjInfraIBanco(){
    return BancoSei::getInstance();
  }

  private function validarNumIdContato(MdPetRelContSitRfDTO $objMdPetRelContSitRfDTO, InfraException $objInfraException){
    if (InfraString::isBolVazia($objMdPetRelContSitRfDTO->getNumIdContato())){
      $objInfraException->adicionarValidacao(' não informad.');
    }
  }

  private function validarNumCpfCnpj(MdPetRelContSitRfDTO $objMdPetRelContSitRfDTO, InfraException $objInfraException){
    if (InfraString::isBolVazia($objMdPetRelContSitRfDTO->getNumCpfCnpj())){
      $objInfraException->adicionarValidacao(' não informad.');
    }
  }

  private function validarNumCodReceita(MdPetRelContSitRfDTO $objMdPetRelContSitRfDTO, InfraException $objInfraException){
    if (InfraString::isBolVazia($objMdPetRelContSitRfDTO->getNumCodReceita())){
      $objInfraException->adicionarValidacao(' não informad.');
    }
  }

  private function validarDthDtConsulta(MdPetRelContSitRfDTO $objMdPetRelContSitRfDTO, InfraException $objInfraException){
    if (InfraString::isBolVazia($objMdPetRelContSitRfDTO->getDthDtConsulta())){
      $objInfraException->adicionarValidacao(' não informad.');
    }
  }

  protected function cadastrarControlado(MdPetRelContSitRfDTO $objMdPetRelContSitRfDTO) {
    try{

      SessaoSEI::getInstance()->validarAuditarPermissao('md_pet_rel_cont_sit_rf_cadastrar', __METHOD__, $objMdPetRelContSitRfDTO);

      //Regras de Negocio
      $objInfraException = new InfraException();

      $this->validarNumIdContato($objMdPetRelContSitRfDTO, $objInfraException);
      $this->validarNumCpfCnpj($objMdPetRelContSitRfDTO, $objInfraException);
      $this->validarNumCodReceita($objMdPetRelContSitRfDTO, $objInfraException);
      $this->validarDthDtConsulta($objMdPetRelContSitRfDTO, $objInfraException);

      $objInfraException->lancarValidacoes();

      $objMdPetRelContSitRfBD = new MdPetRelContSitRfBD($this->getObjInfraIBanco());
      $ret = $objMdPetRelContSitRfBD->cadastrar($objMdPetRelContSitRfDTO);

      return $ret;

    }catch(Exception $e){
      throw new InfraException('Erro cadastrando .',$e);
    }
  }

  protected function alterarControlado(MdPetRelContSitRfDTO $objMdPetRelContSitRfDTO){
    try {

      SessaoSEI::getInstance()->validarAuditarPermissao('md_pet_rel_cont_sit_rf_alterar', __METHOD__, $objMdPetRelContSitRfDTO);

      //Regras de Negocio
      $objInfraException = new InfraException();

      if ($objMdPetRelContSitRfDTO->isSetNumIdContato()){
        $this->validarNumIdContato($objMdPetRelContSitRfDTO, $objInfraException);
      }
      if ($objMdPetRelContSitRfDTO->isSetNumCpfCnpj()){
        $this->validarNumCpfCnpj($objMdPetRelContSitRfDTO, $objInfraException);
      }
      if ($objMdPetRelContSitRfDTO->isSetNumCodReceita()){
        $this->validarNumCodReceita($objMdPetRelContSitRfDTO, $objInfraException);
      }
      if ($objMdPetRelContSitRfDTO->isSetDthDtConsulta()){
        $this->validarDthDtConsulta($objMdPetRelContSitRfDTO, $objInfraException);
      }

      $objInfraException->lancarValidacoes();

      $objMdPetRelContSitRfBD = new MdPetRelContSitRfBD($this->getObjInfraIBanco());
      $objMdPetRelContSitRfBD->alterar($objMdPetRelContSitRfDTO);

    }catch(Exception $e){
      throw new InfraException('Erro alterando .',$e);
    }
  }

  protected function excluirControlado($arrObjMdPetRelContSitRfDTO){
    try {

      SessaoSEI::getInstance()->validarAuditarPermissao('md_pet_rel_cont_sit_rf_excluir', __METHOD__, $arrObjMdPetRelContSitRfDTO);

      //Regras de Negocio
      //$objInfraException = new InfraException();

      //$objInfraException->lancarValidacoes();

      $objMdPetRelContSitRfBD = new MdPetRelContSitRfBD($this->getObjInfraIBanco());
      for($i=0;$i<count($arrObjMdPetRelContSitRfDTO);$i++){
        $objMdPetRelContSitRfBD->excluir($arrObjMdPetRelContSitRfDTO[$i]);
      }

    }catch(Exception $e){
      throw new InfraException('Erro excluindo .',$e);
    }
  }

  protected function consultarConectado(MdPetRelContSitRfDTO $objMdPetRelContSitRfDTO){
    try {

      SessaoSEI::getInstance()->validarAuditarPermissao('md_pet_rel_cont_sit_rf_consultar', __METHOD__, $objMdPetRelContSitRfDTO);

      //Regras de Negocio
      //$objInfraException = new InfraException();

      //$objInfraException->lancarValidacoes();

      $objMdPetRelContSitRfBD = new MdPetRelContSitRfBD($this->getObjInfraIBanco());

      /** @var MdPetRelContSitRfDTO $ret */
      $ret = $objMdPetRelContSitRfBD->consultar($objMdPetRelContSitRfDTO);

      return $ret;
    }catch(Exception $e){
      throw new InfraException('Erro consultando .',$e);
    }
  }

  protected function listarConectado(MdPetRelContSitRfDTO $objMdPetRelContSitRfDTO) {
    try {

      SessaoSEI::getInstance()->validarAuditarPermissao('md_pet_rel_cont_sit_rf_listar', __METHOD__, $objMdPetRelContSitRfDTO);

      //Regras de Negocio
      //$objInfraException = new InfraException();

      //$objInfraException->lancarValidacoes();

      $objMdPetRelContSitRfBD = new MdPetRelContSitRfBD($this->getObjInfraIBanco());

      /** @var MdPetRelContSitRfDTO[] $ret */
      $ret = $objMdPetRelContSitRfBD->listar($objMdPetRelContSitRfDTO);

      return $ret;

    }catch(Exception $e){
      throw new InfraException('Erro listando .',$e);
    }
  }

  protected function contarConectado(MdPetRelContSitRfDTO $objMdPetRelContSitRfDTO){
    try {

      SessaoSEI::getInstance()->validarAuditarPermissao('md_pet_rel_cont_sit_rf_listar', __METHOD__, $objMdPetRelContSitRfDTO);

      //Regras de Negocio
      //$objInfraException = new InfraException();

      //$objInfraException->lancarValidacoes();

      $objMdPetRelContSitRfBD = new MdPetRelContSitRfBD($this->getObjInfraIBanco());
      $ret = $objMdPetRelContSitRfBD->contar($objMdPetRelContSitRfDTO);

      return $ret;
    }catch(Exception $e){
      throw new InfraException('Erro contando .',$e);
    }
  }
/* 
  protected function desativarControlado($arrObjMdPetRelContSitRfDTO){
    try {

      SessaoSEI::getInstance()->validarAuditarPermissao('md_pet_rel_cont_sit_rf_desativar', __METHOD__, $arrObjMdPetRelContSitRfDTO);

      //Regras de Negocio
      //$objInfraException = new InfraException();

      //$objInfraException->lancarValidacoes();

      $objMdPetRelContSitRfBD = new MdPetRelContSitRfBD($this->getObjInfraIBanco());
      for($i=0;$i<count($arrObjMdPetRelContSitRfDTO);$i++){
        $objMdPetRelContSitRfBD->desativar($arrObjMdPetRelContSitRfDTO[$i]);
      }

    }catch(Exception $e){
      throw new InfraException('Erro desativando .',$e);
    }
  }

  protected function reativarControlado($arrObjMdPetRelContSitRfDTO){
    try {

      SessaoSEI::getInstance()->validarAuditarPermissao('md_pet_rel_cont_sit_rf_reativar', __METHOD__, $arrObjMdPetRelContSitRfDTO);

      //Regras de Negocio
      //$objInfraException = new InfraException();

      //$objInfraException->lancarValidacoes();

      $objMdPetRelContSitRfBD = new MdPetRelContSitRfBD($this->getObjInfraIBanco());
      for($i=0;$i<count($arrObjMdPetRelContSitRfDTO);$i++){
        $objMdPetRelContSitRfBD->reativar($arrObjMdPetRelContSitRfDTO[$i]);
      }

    }catch(Exception $e){
      throw new InfraException('Erro reativando .',$e);
    }
  }

  protected function bloquearControlado(MdPetRelContSitRfDTO $objMdPetRelContSitRfDTO){
    try {

      SessaoSEI::getInstance()->validarAuditarPermissao('md_pet_rel_cont_sit_rf_consultar', __METHOD__, $objMdPetRelContSitRfDTO);

      //Regras de Negocio
      //$objInfraException = new InfraException();

      //$objInfraException->lancarValidacoes();

      $objMdPetRelContSitRfBD = new MdPetRelContSitRfBD($this->getObjInfraIBanco());
      $ret = $objMdPetRelContSitRfBD->bloquear($objMdPetRelContSitRfDTO);

      return $ret;
    }catch(Exception $e){
      throw new InfraException('Erro bloqueando .',$e);
    }
  }

 */
}
