<?
/**
* TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
*
* 08/12/2016 - criado por Marcelo Bezerra - CAST
*
* Versão do Gerador de Código: 1.39.0
*/

require_once dirname(__FILE__).'/../../../SEI.php';

class MdPetIntRelIntimRespRN extends InfraRN {

  public function __construct(){
    parent::__construct();
  }

  protected function inicializarObjInfraIBanco(){
    return BancoSEI::getInstance();
  }

  private function validarNumIdMdPetIntTipoIntimacao(MdPetIntRelIntimRespDTO $objMdPetIntRelIntimRespDTO, InfraException $objInfraException){
    if (InfraString::isBolVazia($objMdPetIntRelIntimRespDTO->getNumIdMdPetIntTipoIntimacao())){
      $objInfraException->adicionarValidacao(' não informado.');
    }
  }

  private function validarNumIdMdPetIntTipoResp(MdPetIntRelIntimRespDTO $objMdPetIntRelIntimRespDTO, InfraException $objInfraException){
    if (InfraString::isBolVazia($objMdPetIntRelIntimRespDTO->getNumIdMdPetIntTipoResp())){
      $objInfraException->adicionarValidacao(' não informado.');
    }
  }

  private function validarStrSinAtivo(MdPetIntRelIntimRespDTO $objMdPetIntRelIntimRespDTO, InfraException $objInfraException){
    if (InfraString::isBolVazia($objMdPetIntRelIntimRespDTO->getStrSinAtivo())){
      $objInfraException->adicionarValidacao('Sinalizador de Exclusão Lógica não informado.');
    }else{
      if (!InfraUtil::isBolSinalizadorValido($objMdPetIntRelIntimRespDTO->getStrSinAtivo())){
        $objInfraException->adicionarValidacao('Sinalizador de Exclusão Lógica inválido.');
      }
    }
  }

  protected function cadastrarControlado(MdPetIntRelIntimRespDTO $objMdPetIntRelIntimRespDTO) {
    try{

      //Valida Permissao
      SessaoSEI::getInstance()->validarAuditarPermissao('md_pet_int_rel_intim_resp_cadastrar',__METHOD__,$objMdPetIntRelIntimRespDTO);

      //Regras de Negocio
      $objInfraException = new InfraException();

      $this->validarNumIdMdPetIntTipoIntimacao($objMdPetIntRelIntimRespDTO, $objInfraException);
      $this->validarNumIdMdPetIntTipoResp($objMdPetIntRelIntimRespDTO, $objInfraException);
      $this->validarStrSinAtivo($objMdPetIntRelIntimRespDTO, $objInfraException);

      $objInfraException->lancarValidacoes();

      $objMdPetIntRelIntimRespBD = new MdPetIntRelIntimRespBD($this->getObjInfraIBanco());
      $ret = $objMdPetIntRelIntimRespBD->cadastrar($objMdPetIntRelIntimRespDTO);

      //Auditoria

      return $ret;

    }catch(Exception $e){
      throw new InfraException('Erro cadastrando .',$e);
    }
  }

  protected function alterarControlado(MdPetIntRelIntimRespDTO $objMdPetIntRelIntimRespDTO){
    try {

      //Valida Permissao
  	   SessaoSEI::getInstance()->validarAuditarPermissao('md_pet_int_rel_intim_resp_alterar',__METHOD__,$objMdPetIntRelIntimRespDTO);

      //Regras de Negocio
      $objInfraException = new InfraException();

      if ($objMdPetIntRelIntimRespDTO->isSetNumIdMdPetIntTipoIntimacao()){
        $this->validarNumIdMdPetIntTipoIntimacao($objMdPetIntRelIntimRespDTO, $objInfraException);
      }
      if ($objMdPetIntRelIntimRespDTO->isSetNumIdMdPetIntTipoResp()){
        $this->validarNumIdMdPetIntTipoResp($objMdPetIntRelIntimRespDTO, $objInfraException);
      }
      if ($objMdPetIntRelIntimRespDTO->isSetStrSinAtivo()){
        $this->validarStrSinAtivo($objMdPetIntRelIntimRespDTO, $objInfraException);
      }

      $objInfraException->lancarValidacoes();

      $objMdPetIntRelIntimRespBD = new MdPetIntRelIntimRespBD($this->getObjInfraIBanco());
      $objMdPetIntRelIntimRespBD->alterar($objMdPetIntRelIntimRespDTO);

      //Auditoria

    }catch(Exception $e){
      throw new InfraException('Erro alterando .',$e);
    }
  }

  protected function excluirControlado($arrObjMdPetIntRelIntimRespDTO){
    try {

      //Valida Permissao
      SessaoSEI::getInstance()->validarAuditarPermissao('md_pet_int_rel_intim_resp_excluir',__METHOD__,$arrObjMdPetIntRelIntimRespDTO);

      //Regras de Negocio
      //$objInfraException = new InfraException();

      //$objInfraException->lancarValidacoes();

      $objMdPetIntRelIntimRespBD = new MdPetIntRelIntimRespBD($this->getObjInfraIBanco());
      for($i=0;$i<count($arrObjMdPetIntRelIntimRespDTO);$i++){
        $objMdPetIntRelIntimRespBD->excluir($arrObjMdPetIntRelIntimRespDTO[$i]);
      }

      //Auditoria

    }catch(Exception $e){
      throw new InfraException('Erro excluindo .',$e);
    }
  }

  protected function consultarConectado(MdPetIntRelIntimRespDTO $objMdPetIntRelIntimRespDTO){
    try {

      //Valida Permissao
      SessaoSEI::getInstance()->validarPermissao('md_pet_int_rel_intim_resp_consultar');

      //Regras de Negocio
      //$objInfraException = new InfraException();

      //$objInfraException->lancarValidacoes();

      $objMdPetIntRelIntimRespBD = new MdPetIntRelIntimRespBD($this->getObjInfraIBanco());
      $ret = $objMdPetIntRelIntimRespBD->consultar($objMdPetIntRelIntimRespDTO);

      //Auditoria

      return $ret;
    }catch(Exception $e){
      throw new InfraException('Erro consultando .',$e);
    }
  }

  protected function listarConectado(MdPetIntRelIntimRespDTO $objMdPetIntRelIntimRespDTO) {
    try {

      //Valida Permissao
      SessaoSEI::getInstance()->validarPermissao('md_pet_int_rel_intim_resp_listar');

      //Regras de Negocio
      //$objInfraException = new InfraException();

      //$objInfraException->lancarValidacoes();

      $objMdPetIntRelIntimRespBD = new MdPetIntRelIntimRespBD($this->getObjInfraIBanco());
      $ret = $objMdPetIntRelIntimRespBD->listar($objMdPetIntRelIntimRespDTO);

      //Auditoria

      return $ret;

    }catch(Exception $e){
      throw new InfraException('Erro listando .',$e);
    }
  }

  protected function contarConectado(MdPetIntRelIntimRespDTO $objMdPetIntRelIntimRespDTO){
    try {

      //Valida Permissao
      SessaoSEI::getInstance()->validarPermissao('md_pet_int_rel_intim_resp_listar');

      //Regras de Negocio
      //$objInfraException = new InfraException();

      //$objInfraException->lancarValidacoes();

      $objMdPetIntRelIntimRespBD = new MdPetIntRelIntimRespBD($this->getObjInfraIBanco());
      $ret = $objMdPetIntRelIntimRespBD->contar($objMdPetIntRelIntimRespDTO);

      //Auditoria

      return $ret;
    }catch(Exception $e){
      throw new InfraException('Erro contando .',$e);
    }
  }

  protected function desativarControlado($arrObjMdPetIntRelIntimRespDTO){
    try {

      //Valida Permissao
      SessaoSEI::getInstance()->validarAuditarPermissao('md_pet_int_rel_intim_resp_desativar',__METHOD__,$arrObjMdPetIntRelIntimRespDTO);

      //Regras de Negocio
      //$objInfraException = new InfraException();

      //$objInfraException->lancarValidacoes();

      $objMdPetIntRelIntimRespBD = new MdPetIntRelIntimRespBD($this->getObjInfraIBanco());
      for($i=0;$i<count($arrObjMdPetIntRelIntimRespDTO);$i++){
        $objMdPetIntRelIntimRespBD->desativar($arrObjMdPetIntRelIntimRespDTO[$i]);
      }

      //Auditoria

    }catch(Exception $e){
      throw new InfraException('Erro desativando .',$e);
    }
  }

  protected function reativarControlado($arrObjMdPetIntRelIntimRespDTO){
    try {

      //Valida Permissao
      SessaoSEI::getInstance()->validarAuditarPermissao('md_pet_int_rel_intim_resp_reativar',__METHOD__,$arrObjMdPetIntRelIntimRespDTO);

      //Regras de Negocio
      //$objInfraException = new InfraException();

      //$objInfraException->lancarValidacoes();

      $objMdPetIntRelIntimRespBD = new MdPetIntRelIntimRespBD($this->getObjInfraIBanco());
      for($i=0;$i<count($arrObjMdPetIntRelIntimRespDTO);$i++){
        $objMdPetIntRelIntimRespBD->reativar($arrObjMdPetIntRelIntimRespDTO[$i]);
      }

      //Auditoria

    }catch(Exception $e){
      throw new InfraException('Erro reativando .',$e);
    }
  }

  protected function bloquearControlado(MdPetIntRelIntimRespDTO $objMdPetIntRelIntimRespDTO){
    try {

      //Valida Permissao
      SessaoSEI::getInstance()->validarPermissao('md_pet_int_rel_intim_resp_consultar');

      //Regras de Negocio
      //$objInfraException = new InfraException();

      //$objInfraException->lancarValidacoes();

      $objMdPetIntRelIntimRespBD = new MdPetIntRelIntimRespBD($this->getObjInfraIBanco());
      $ret = $objMdPetIntRelIntimRespBD->bloquear($objMdPetIntRelIntimRespDTO);

      //Auditoria

      return $ret;
    }catch(Exception $e){
      throw new InfraException('Erro bloqueando .',$e);
    }
  }


}
?>