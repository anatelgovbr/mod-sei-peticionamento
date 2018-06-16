<?
/**
* TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
*
* 06/12/2016 - criado por Wilton Júnior - CAST
*
* Versão do Gerador de Código: 1.39.0
*/

require_once dirname(__FILE__) . '/../../../SEI.php';

class MdPetIntPrazoTacitaRN extends InfraRN {

  public function __construct(){
    parent::__construct();
  }

  protected function inicializarObjInfraIBanco(){
    return BancoSEI::getInstance();
  }

  private function validarNumIdMdPetIntPrazoTacita(MdPetIntPrazoTacitaDTO $objMdPetIntPrazoTacitaDTO, InfraException $objInfraException){
    if (InfraString::isBolVazia($objMdPetIntPrazoTacitaDTO->getNumIdMdPetIntPrazoTacita())){
      $objInfraException->adicionarValidacao('Id não informado.');
    }
  }

  private function validarNumNumPrazo(MdPetIntPrazoTacitaDTO $objMdPetIntPrazoTacitaDTO, InfraException $objInfraException){
    if (InfraString::isBolVazia($objMdPetIntPrazoTacitaDTO->getNumNumPrazo())){
      $objInfraException->adicionarValidacao('Prazo não informado.');
    }
  }

  protected function cadastrarControlado(MdPetIntPrazoTacitaDTO $objMdPetIntPrazoTacitaDTO) {
    try{

      //Valida Permissao
      SessaoSEI::getInstance()->validarPermissao('md_pet_int_prazo_tacita_cadastrar');

      //Regras de Negocio
      $objInfraException = new InfraException();

      $this->validarNumIdMdPetIntPrazoTacita($objMdPetIntPrazoTacitaDTO, $objInfraException);
      $this->validarNumNumPrazo($objMdPetIntPrazoTacitaDTO, $objInfraException);

      $objInfraException->lancarValidacoes();

      $objMdPetIntPrazoTacitaBD = new MdPetIntPrazoTacitaBD($this->getObjInfraIBanco());
      $ret = $objMdPetIntPrazoTacitaBD->cadastrar($objMdPetIntPrazoTacitaDTO);

      //Auditoria

      return $ret;

    }catch(Exception $e){
      throw new InfraException('Erro cadastrando .',$e);
    }
  }

  protected function alterarControlado(MdPetIntPrazoTacitaDTO $objMdPetIntPrazoTacitaDTO){
    try {

      //Valida Permissao
  	   SessaoSEI::getInstance()->validarPermissao('md_pet_int_prazo_tacita_alterar');

      //Regras de Negocio
      $objInfraException = new InfraException();

      if ($objMdPetIntPrazoTacitaDTO->isSetNumIdMdPetIntPrazoTacita()){
        $this->validarNumIdMdPetIntPrazoTacita($objMdPetIntPrazoTacitaDTO, $objInfraException);
      }
      if ($objMdPetIntPrazoTacitaDTO->isSetNumNumPrazo()){
        $this->validarNumNumPrazo($objMdPetIntPrazoTacitaDTO, $objInfraException);
      }

      $objInfraException->lancarValidacoes();

      $objMdPetIntPrazoTacitaBD = new MdPetIntPrazoTacitaBD($this->getObjInfraIBanco());
      $objMdPetIntPrazoTacitaBD->alterar($objMdPetIntPrazoTacitaDTO);

      //Auditoria

    }catch(Exception $e){
      throw new InfraException('Erro alterando .',$e);
    }
  }

  protected function excluirControlado($arrObjMdPetIntPrazoTacitaDTO){
    try {

      //Valida Permissao
      SessaoSEI::getInstance()->validarPermissao('md_pet_int_prazo_tacita_excluir');

      $objMdPetIntPrazoTacitaBD = new MdPetIntPrazoTacitaBD($this->getObjInfraIBanco());
      for($i=0;$i<count($arrObjMdPetIntPrazoTacitaDTO);$i++){
        $objMdPetIntPrazoTacitaBD->excluir($arrObjMdPetIntPrazoTacitaDTO[$i]);
      }

      //Auditoria

    }catch(Exception $e){
      throw new InfraException('Erro excluindo .',$e);
    }
  }

  protected function consultarConectado(MdPetIntPrazoTacitaDTO $objMdPetIntPrazoTacitaDTO){
    try {

      //Valida Permissao
      SessaoSEI::getInstance()->validarPermissao('md_pet_int_prazo_tacita_consultar');

      $objMdPetIntPrazoTacitaBD = new MdPetIntPrazoTacitaBD($this->getObjInfraIBanco());
      $ret = $objMdPetIntPrazoTacitaBD->consultar($objMdPetIntPrazoTacitaDTO);

      return $ret;
    }catch(Exception $e){
      throw new InfraException('Erro consultando .',$e);
    }
  }

  protected function listarConectado(MdPetIntPrazoTacitaDTO $objMdPetIntPrazoTacitaDTO) {
    try {

      //Valida Permissao
      SessaoSEI::getInstance()->validarPermissao('md_pet_int_prazo_tacita_listar');

      //Regras de Negocio
      //$objInfraException = new InfraException();

      //$objInfraException->lancarValidacoes();

      $objMdPetIntPrazoTacitaBD = new MdPetIntPrazoTacitaBD($this->getObjInfraIBanco());
      $ret = $objMdPetIntPrazoTacitaBD->listar($objMdPetIntPrazoTacitaDTO);

      //Auditoria

      return $ret;

    }catch(Exception $e){
      throw new InfraException('Erro listando .',$e);
    }
  }

  protected function contarConectado(MdPetIntPrazoTacitaDTO $objMdPetIntPrazoTacitaDTO){
    try {

      //Valida Permissao
      SessaoSEI::getInstance()->validarPermissao('md_pet_int_prazo_tacita_listar');

      //Regras de Negocio
      //$objInfraException = new InfraException();

      //$objInfraException->lancarValidacoes();

      $objMdPetIntPrazoTacitaBD = new MdPetIntPrazoTacitaBD($this->getObjInfraIBanco());
      $ret = $objMdPetIntPrazoTacitaBD->contar($objMdPetIntPrazoTacitaDTO);

      //Auditoria

      return $ret;
    }catch(Exception $e){
      throw new InfraException('Erro contando .',$e);
    }
  }
}
?>