<?
/**
* TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
*
* 07/12/2016 - criado por Marcelo Bezerra
*
* Versão do Gerador de Código: 1.39.0
*/

require_once dirname(__FILE__) . '/../../../SEI.php';

class MdPetIntSerieRN extends InfraRN {

  public function __construct(){
    parent::__construct();
  }

  protected function inicializarObjInfraIBanco(){
    return BancoSEI::getInstance();
  }

  private function validarNumIdSerie(MdPetIntSerieDTO $objMdPetIntSerieDTO, InfraException $objInfraException){
    if (InfraString::isBolVazia($objMdPetIntSerieDTO->getNumIdSerie())){
      $objInfraException->adicionarValidacao('Tipo de Documento não informado.');
    }
  }

  protected function cadastrarControlado(MdPetIntSerieDTO $objMdPetIntSerieDTO) {
    try{

      //Valida Permissao
      SessaoSEI::getInstance()->validarPermissao('md_pet_int_serie_cadastrar');

      //Regras de Negocio
      $objInfraException = new InfraException();

      $this->validarNumIdSerie($objMdPetIntSerieDTO, $objInfraException);

      $objInfraException->lancarValidacoes();

      $objMdPetIntSerieBD = new MdPetIntSerieBD($this->getObjInfraIBanco());
      $ret = $objMdPetIntSerieBD->cadastrar($objMdPetIntSerieDTO);

      //Auditoria

      return $ret;

    }catch(Exception $e){
      throw new InfraException('Erro cadastrando .',$e);
    }
  }

  protected function alterarControlado(MdPetIntSerieDTO $objMdPetIntSerieDTO){
    try {

      //Valida Permissao
  	   SessaoSEI::getInstance()->validarPermissao('md_pet_int_serie_alterar');

      //Regras de Negocio
      $objInfraException = new InfraException();

      if ($objMdPetIntSerieDTO->isSetNumIdSerie()){
        $this->validarNumIdSerie($objMdPetIntSerieDTO, $objInfraException);
      }

      $objInfraException->lancarValidacoes();

      $objMdPetIntSerieBD = new MdPetIntSerieBD($this->getObjInfraIBanco());
      $objMdPetIntSerieBD->alterar($objMdPetIntSerieDTO);

      //Auditoria

    }catch(Exception $e){
      throw new InfraException('Erro alterando .',$e);
    }
  }

  protected function excluirControlado($arrObjMdPetIntSerieDTO){
    try {

      //Valida Permissao
      SessaoSEI::getInstance()->validarPermissao('md_pet_int_serie_excluir');

      //Regras de Negocio
      //$objInfraException = new InfraException();

      //$objInfraException->lancarValidacoes();

      $objMdPetIntSerieBD = new MdPetIntSerieBD($this->getObjInfraIBanco());
      for($i=0;$i<count($arrObjMdPetIntSerieDTO);$i++){
        $objMdPetIntSerieBD->excluir($arrObjMdPetIntSerieDTO[$i]);
      }

      //Auditoria

    }catch(Exception $e){
      throw new InfraException('Erro excluindo .',$e);
    }
  }

  protected function consultarConectado(MdPetIntSerieDTO $objMdPetIntSerieDTO){
    try {

      //Valida Permissao
      SessaoSEI::getInstance()->validarPermissao('md_pet_int_serie_consultar');

      //Regras de Negocio
      //$objInfraException = new InfraException();

      //$objInfraException->lancarValidacoes();

      $objMdPetIntSerieBD = new MdPetIntSerieBD($this->getObjInfraIBanco());
      $ret = $objMdPetIntSerieBD->consultar($objMdPetIntSerieDTO);

      //Auditoria

      return $ret;
    }catch(Exception $e){
      throw new InfraException('Erro consultando .',$e);
    }
  }

  protected function listarConectado(MdPetIntSerieDTO $objMdPetIntSerieDTO) {
    try {

      //Valida Permissao
      SessaoSEI::getInstance()->validarPermissao('md_pet_int_serie_listar');

      //Regras de Negocio
      //$objInfraException = new InfraException();

      //$objInfraException->lancarValidacoes();

      $objMdPetIntSerieBD = new MdPetIntSerieBD($this->getObjInfraIBanco());
      $ret = $objMdPetIntSerieBD->listar($objMdPetIntSerieDTO);

      //Auditoria

      return $ret;

    }catch(Exception $e){
      throw new InfraException('Erro listando .',$e);
    }
  }

  protected function contarConectado(MdPetIntSerieDTO $objMdPetIntSerieDTO){
    try {

      //Valida Permissao
      SessaoSEI::getInstance()->validarPermissao('md_pet_int_serie_listar');

      //Regras de Negocio
      //$objInfraException = new InfraException();

      //$objInfraException->lancarValidacoes();

      $objMdPetIntSerieBD = new MdPetIntSerieBD($this->getObjInfraIBanco());
      $ret = $objMdPetIntSerieBD->contar($objMdPetIntSerieDTO);

      //Auditoria

      return $ret;
    }catch(Exception $e){
      throw new InfraException('Erro contando .',$e);
    }
  }

}
?>