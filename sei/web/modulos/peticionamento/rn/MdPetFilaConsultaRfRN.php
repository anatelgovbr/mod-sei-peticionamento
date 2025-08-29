<?
/**
* TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
*
* 21/06/2023 - criado por michaelr.colab
*
* Versão do Gerador de Código: 1.43.2
*/

require_once dirname(__FILE__).'/../../../SEI.php';

class MdPetFilaConsultaRfRN extends InfraRN {

  public static $NATUREZA_1 = 'A';
  public static $NATUREZA_2 = 'B';
  public static $NATUREZA_3 = 'C';

  public function __construct(){
    parent::__construct();
  }

  protected function inicializarObjInfraIBanco(){
    return BancoSei::getInstance();
  }

  private function validarNumCpfCnpj(MdPetFilaConsultaRfDTO $objMdPetFilaConsultaRfDTO, InfraException $objInfraException){
    if (InfraString::isBolVazia($objMdPetFilaConsultaRfDTO->getDblCpfCnpj())){
      $objInfraException->adicionarValidacao(' não informado.');
    }
  }

  protected function cadastrarControlado(MdPetFilaConsultaRfDTO $objMdPetFilaConsultaRfDTO) {
    try{
        //todo verificar se precisa de permissão
//      SessaoSEI::getInstance()->validarAuditarPermissao('md_pet_fila_consulta_rf_cadastrar', __METHOD__, $objMdPetFilaConsultaRfDTO);

      //Regras de Negocio
      $objInfraException = new InfraException();

      $this->validarNumCpfCnpj($objMdPetFilaConsultaRfDTO, $objInfraException);

      $objInfraException->lancarValidacoes();

      $objMdPetFilaConsultaRfBD = new MdPetFilaConsultaRfBD($this->getObjInfraIBanco());
      $ret = $objMdPetFilaConsultaRfBD->cadastrar($objMdPetFilaConsultaRfDTO);

      return $ret;

    }catch(Exception $e){
      throw new InfraException('Erro cadastrando .',$e);
    }
  }

  protected function alterarControlado(MdPetFilaConsultaRfDTO $objMdPetFilaConsultaRfDTO){
    try {

//      SessaoSEI::getInstance()->validarAuditarPermissao('md_pet_fila_consulta_rf_alterar', __METHOD__, $objMdPetFilaConsultaRfDTO);

      //Regras de Negocio
      $objInfraException = new InfraException();

      if ($objMdPetFilaConsultaRfDTO->isSetStrStaNatureza()){
        $this->validarStrStaNatureza($objMdPetFilaConsultaRfDTO, $objInfraException);
      }
      if ($objMdPetFilaConsultaRfDTO->isSetNumCpfCnpj()){
        $this->validarNumCpfCnpj($objMdPetFilaConsultaRfDTO, $objInfraException);
      }

      $objInfraException->lancarValidacoes();

      $objMdPetFilaConsultaRfBD = new MdPetFilaConsultaRfBD($this->getObjInfraIBanco());
      $objMdPetFilaConsultaRfBD->alterar($objMdPetFilaConsultaRfDTO);

    }catch(Exception $e){
      throw new InfraException('Erro alterando .',$e);
    }
  }

  protected function excluirControlado($arrObjMdPetFilaConsultaRfDTO){
    try {

//      SessaoSEI::getInstance()->validarAuditarPermissao('md_pet_fila_consulta_rf_excluir', __METHOD__, $arrObjMdPetFilaConsultaRfDTO);

      //Regras de Negocio
      //$objInfraException = new InfraException();

      //$objInfraException->lancarValidacoes();

      $objMdPetFilaConsultaRfBD = new MdPetFilaConsultaRfBD($this->getObjInfraIBanco());
      for($i=0;$i<count($arrObjMdPetFilaConsultaRfDTO);$i++){
        $objMdPetFilaConsultaRfBD->excluir($arrObjMdPetFilaConsultaRfDTO[$i]);
      }

    }catch(Exception $e){
      throw new InfraException('Erro excluindo .',$e);
    }
  }

  protected function consultarConectado(MdPetFilaConsultaRfDTO $objMdPetFilaConsultaRfDTO){
    try {

//      SessaoSEI::getInstance()->validarAuditarPermissao('md_pet_fila_consulta_rf_consultar', __METHOD__, $objMdPetFilaConsultaRfDTO);

      //Regras de Negocio
      //$objInfraException = new InfraException();

      //$objInfraException->lancarValidacoes();

      $objMdPetFilaConsultaRfBD = new MdPetFilaConsultaRfBD($this->getObjInfraIBanco());

      /** @var MdPetFilaConsultaRfDTO $ret */
      $ret = $objMdPetFilaConsultaRfBD->consultar($objMdPetFilaConsultaRfDTO);

      return $ret;
    }catch(Exception $e){
      throw new InfraException('Erro consultando .',$e);
    }
  }

  protected function listarConectado(MdPetFilaConsultaRfDTO $objMdPetFilaConsultaRfDTO) {
    try {

      //todo deve ter permissao ???
//      SessaoSEI::getInstance()->validarAuditarPermissao('md_pet_fila_consulta_rf_listar', __METHOD__, $objMdPetFilaConsultaRfDTO);

      //Regras de Negocio
      //$objInfraException = new InfraException();

      //$objInfraException->lancarValidacoes();

      $objMdPetFilaConsultaRfBD = new MdPetFilaConsultaRfBD($this->getObjInfraIBanco());

      /** @var MdPetFilaConsultaRfDTO[] $ret */
      $ret = $objMdPetFilaConsultaRfBD->listar($objMdPetFilaConsultaRfDTO);

      return $ret;

    }catch(Exception $e){
      throw new InfraException('Erro listando .',$e);
    }
  }

  protected function contarConectado(MdPetFilaConsultaRfDTO $objMdPetFilaConsultaRfDTO){
    try {

      SessaoSEI::getInstance()->validarAuditarPermissao('md_pet_fila_consulta_rf_listar', __METHOD__, $objMdPetFilaConsultaRfDTO);

      //Regras de Negocio
      //$objInfraException = new InfraException();

      //$objInfraException->lancarValidacoes();

      $objMdPetFilaConsultaRfBD = new MdPetFilaConsultaRfBD($this->getObjInfraIBanco());
      $ret = $objMdPetFilaConsultaRfBD->contar($objMdPetFilaConsultaRfDTO);

      return $ret;
    }catch(Exception $e){
      throw new InfraException('Erro contando .',$e);
    }
  }
/* 
  protected function desativarControlado($arrObjMdPetFilaConsultaRfDTO){
    try {

      SessaoSEI::getInstance()->validarAuditarPermissao('md_pet_fila_consulta_rf_desativar', __METHOD__, $arrObjMdPetFilaConsultaRfDTO);

      //Regras de Negocio
      //$objInfraException = new InfraException();

      //$objInfraException->lancarValidacoes();

      $objMdPetFilaConsultaRfBD = new MdPetFilaConsultaRfBD($this->getObjInfraIBanco());
      for($i=0;$i<count($arrObjMdPetFilaConsultaRfDTO);$i++){
        $objMdPetFilaConsultaRfBD->desativar($arrObjMdPetFilaConsultaRfDTO[$i]);
      }

    }catch(Exception $e){
      throw new InfraException('Erro desativando .',$e);
    }
  }

  protected function reativarControlado($arrObjMdPetFilaConsultaRfDTO){
    try {

      SessaoSEI::getInstance()->validarAuditarPermissao('md_pet_fila_consulta_rf_reativar', __METHOD__, $arrObjMdPetFilaConsultaRfDTO);

      //Regras de Negocio
      //$objInfraException = new InfraException();

      //$objInfraException->lancarValidacoes();

      $objMdPetFilaConsultaRfBD = new MdPetFilaConsultaRfBD($this->getObjInfraIBanco());
      for($i=0;$i<count($arrObjMdPetFilaConsultaRfDTO);$i++){
        $objMdPetFilaConsultaRfBD->reativar($arrObjMdPetFilaConsultaRfDTO[$i]);
      }

    }catch(Exception $e){
      throw new InfraException('Erro reativando .',$e);
    }
  }

  protected function bloquearControlado(MdPetFilaConsultaRfDTO $objMdPetFilaConsultaRfDTO){
    try {

      SessaoSEI::getInstance()->validarAuditarPermissao('md_pet_fila_consulta_rf_consultar', __METHOD__, $objMdPetFilaConsultaRfDTO);

      //Regras de Negocio
      //$objInfraException = new InfraException();

      //$objInfraException->lancarValidacoes();

      $objMdPetFilaConsultaRfBD = new MdPetFilaConsultaRfBD($this->getObjInfraIBanco());
      $ret = $objMdPetFilaConsultaRfBD->bloquear($objMdPetFilaConsultaRfDTO);

      return $ret;
    }catch(Exception $e){
      throw new InfraException('Erro bloqueando .',$e);
    }
  }

 */
}
