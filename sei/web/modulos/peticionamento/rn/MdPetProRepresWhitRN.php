<?
/**
* TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
*
* 02/04/2018 - criado por jose vieira
*
* Versão do Gerador de Código: 1.41.0
*/

require_once dirname(__FILE__) . '/../../../SEI.php';

class MdPetProRepresWhitRN extends InfraRN {

  public function __construct(){
    parent::__construct();
  }

  protected function inicializarObjInfraIBanco(){
    return BancoSEI::getInstance();
  }

  protected function cadastrarControlado(MdPetProRepresWhitDTO $objMdPetProRepresWhitDTO) {
    try{
      //Valida Permissao
      //SessaoSEI::getInstance()->validarPermissao('md_pet_pro_repres_whit_cadastrar');
      //Regras de Negocio
      $objInfraException = new InfraException();

      $objInfraException->lancarValidacoes();

      $objMdPetProRepresWhitBD = new MdPetProRepresWhitBD($this->getObjInfraIBanco());
      $ret = $objMdPetProRepresWhitBD->cadastrar($objMdPetProRepresWhitDTO);

      //Auditoria

      return $ret;

    }catch(Exception $e){
      throw new InfraException('Erro cadastrando Vínculo.',$e);
    }
  }

  protected function alterarControlado(MdPetProRepresWhitDTO $objMdPetProRepresWhitDTO){
    try {

      //Valida Permissao
//      SessaoSEI::getInstance()->validarPermissao('md_pet_vinculacao_alterar');

      //Regras de Negocio
      $objInfraException = new InfraException();

      $objInfraException->lancarValidacoes();

      $objMdPetProRepresWhitBD = new MdPetProRepresWhitBD($this->getObjInfraIBanco());
      $objMdPetProRepresWhitBD->alterar($objMdPetProRepresWhitDTO);

      //Auditoria

    }catch(Exception $e){
      throw new InfraException('Erro alterando Vínculo.',$e);
    }
  }

  protected function excluirControlado($arrObjMdPetProRepresWhitDTO){
    try {

      //Valida Permissao
      //SessaoSEI::getInstance()->validarPermissao('md_pet_pro_repres_whit_excluir');

      //Regras de Negocio
      //$objInfraException = new InfraException();

      //$objInfraException->lancarValidacoes();

      $objMdPetProRepresWhitBD = new MdPetProRepresWhitBD($this->getObjInfraIBanco());
      for($i=0;$i<count($arrObjMdPetProRepresWhitDTO);$i++){
        $objMdPetProRepresWhitBD->excluir($arrObjMdPetProRepresWhitDTO[$i]);
      }

      //Auditoria

    }catch(Exception $e){
      throw new InfraException('Erro excluindo Vínculo.',$e);
    }
  }

  protected function consultarConectado(MdPetProRepresWhitDTO $objMdPetProRepresWhitDTO){
    try {

      //Valida Permissao
      //SessaoSEI::getInstance()->validarPermissao('md_pet_pro_repres_whit_consultar');

      //Regras de Negocio
      //$objInfraException = new InfraException();

      //$objInfraException->lancarValidacoes();

      $objMdPetProRepresWhitBD = new MdPetProRepresWhitBD($this->getObjInfraIBanco());
      $ret = $objMdPetProRepresWhitBD->consultar($objMdPetProRepresWhitDTO);

      //Auditoria

      return $ret;
    }catch(Exception $e){
      throw new InfraException('Erro consultando Vínculo.',$e);
    }
  }

  protected function listarConectado(MdPetProRepresWhitDTO $objMdPetProRepresWhitDTO) {
    try {

      //Valida Permissao
      //SessaoSEI::getInstance()->validarPermissao('md_pet_pro_repres_whit_listar');

      //Regras de Negocio
      //$objInfraException = new InfraException();

      //$objInfraException->lancarValidacoes();

      $objMdPetProRepresWhitBD = new MdPetProRepresWhitBD($this->getObjInfraIBanco());
      $ret = $objMdPetProRepresWhitBD->listar($objMdPetProRepresWhitDTO);
      //Auditoria

      return $ret;

    }catch(Exception $e){
      throw new InfraException('Erro listando Vínculo.',$e);
    }
  }

  protected function contarConectado(MdPetProRepresWhitDTO $objMdPetProRepresWhitDTO){
    try {

      //Valida Permissao
      //SessaoSEI::getInstance()->validarPermissao('md_pet_pro_repres_whit_contar');

      //Regras de Negocio
      //$objInfraException = new InfraException();

      //$objInfraException->lancarValidacoes();

      $objMdPetProRepresWhitBD = new MdPetProRepresWhitBD($this->getObjInfraIBanco());
      $ret = $objMdPetProRepresWhitBD->contar($objMdPetProRepresWhitDTO);

      //Auditoria

      return $ret;
    }catch(Exception $e){
      throw new InfraException('Erro contando Vínculo.',$e);
    }
  }
}
