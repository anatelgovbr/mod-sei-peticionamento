<?
/**
* TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
*
* 25/01/2018 - criado por Usuário
*
* Versão do Gerador de Código: 1.41.0
*/

require_once dirname(__FILE__).'/../../../SEI.php';

class MdPetIntegParametroRN extends InfraRN {

  public static $TIPO_PARAMETRO  = 'P';

  public function __construct(){
    parent::__construct();
  }

  protected function inicializarObjInfraIBanco(){
    return BancoSEI::getInstance();
  }

  private function validarNumIdMdPetIntegracao(MdPetIntegParametroDTO $objMdPetIntegParametroDTO, InfraException $objInfraException){
    if (InfraString::isBolVazia($objMdPetIntegParametroDTO->getNumIdMdPetIntegracao())){
      $objInfraException->adicionarValidacao('Integração não informada.');
    }
  }

  private function validarStrNome(MdPetIntegParametroDTO $objMdPetIntegParametroDTO, InfraException $objInfraException){
    if (InfraString::isBolVazia($objMdPetIntegParametroDTO->getStrNome())){
      $objInfraException->adicionarValidacao('Nome do Parâmetro não informado.');
    }else{
      $objMdPetIntegParametroDTO->setStrNome(trim($objMdPetIntegParametroDTO->getStrNome()));

      if (strlen($objMdPetIntegParametroDTO->getStrNome())>30){
        $objInfraException->adicionarValidacao('Nome do Parâmetro possui tamanho superior a 30 caracteres.');
      }
    }
  }

  private function validarStrTpParametro(MdPetIntegParametroDTO $objMdPetIntegParametroDTO, InfraException $objInfraException){
    if (InfraString::isBolVazia($objMdPetIntegParametroDTO->getStrTpParametro())){
      $objInfraException->adicionarValidacao('Tipo do Parâmetro não informad.');
    }else{
      $objMdPetIntegParametroDTO->setStrTpParametro(trim($objMdPetIntegParametroDTO->getStrTpParametro()));

      if (strlen($objMdPetIntegParametroDTO->getStrTpParametro())>1){
        $objInfraException->adicionarValidacao('Tipo do Parâmetro possui tamanho superior a 1 caracteres.');
      }
    }
  }

  protected function cadastrarControlado(MdPetIntegParametroDTO $objMdPetIntegParametroDTO) {
    try{

      //Valida Permissao
      //Cadastro através do Integração
      //SessaoSEI::getInstance()->validarPermissao('md_pet_integ_parametro_cadastrar');
      SessaoSEI::getInstance()->validarPermissao('md_pet_integracao_cadastrar');
    	
      //Regras de Negocio
      $objInfraException = new InfraException();

      $this->validarNumIdMdPetIntegracao($objMdPetIntegParametroDTO, $objInfraException);
      $this->validarStrNome($objMdPetIntegParametroDTO, $objInfraException);
      $this->validarStrTpParametro($objMdPetIntegParametroDTO, $objInfraException);

      $objInfraException->lancarValidacoes();

      $objMdPetIntegParametroBD = new MdPetIntegParametroBD($this->getObjInfraIBanco());
      $ret = $objMdPetIntegParametroBD->cadastrar($objMdPetIntegParametroDTO);

      //Auditoria

      return $ret;

    }catch(Exception $e){
      throw new InfraException('Erro cadastrando Parâmetro.',$e);
    }
  }

  protected function alterarControlado(MdPetIntegParametroDTO $objMdPetIntegParametroDTO){
    try {

      //Valida Permissao
  	   SessaoSEI::getInstance()->validarPermissao('md_pet_integ_parametro_alterar');

      //Regras de Negocio
      $objInfraException = new InfraException();

      if ($objMdPetIntegParametroDTO->isSetNumIdMdPetIntegracao()){
        $this->validarNumIdMdPetIntegracao($objMdPetIntegParametroDTO, $objInfraException);
      }
      if ($objMdPetIntegParametroDTO->isSetStrNome()){
        $this->validarStrNome($objMdPetIntegParametroDTO, $objInfraException);
      }
      if ($objMdPetIntegParametroDTO->isSetStrTpParametro()){
        $this->validarStrTpParametro($objMdPetIntegParametroDTO, $objInfraException);
      }
      if ($objMdPetIntegParametroDTO->isSetStrNomeCampo()){
        $this->validarStrNomeCampo($objMdPetIntegParametroDTO, $objInfraException);
      }

      $objInfraException->lancarValidacoes();

      $objMdPetIntegParametroBD = new MdPetIntegParametroBD($this->getObjInfraIBanco());
      $objMdPetIntegParametroBD->alterar($objMdPetIntegParametroDTO);

      //Auditoria

    }catch(Exception $e){
      throw new InfraException('Erro alterando Parâmetro.',$e);
    }
  }

  protected function excluirControlado($arrObjMdPetIntegParametroDTO){
    try {

      //Valida Permissao
      //SessaoSEI::getInstance()->validarPermissao('md_pet_integ_parametro_excluir');
      SessaoSEI::getInstance()->validarPermissao('md_pet_integracao_alterar');
    	
      //Regras de Negocio
      //$objInfraException = new InfraException();

      //$objInfraException->lancarValidacoes();

      $objMdPetIntegParametroBD = new MdPetIntegParametroBD($this->getObjInfraIBanco());
      for($i=0;$i<count($arrObjMdPetIntegParametroDTO);$i++){
        $objMdPetIntegParametroBD->excluir($arrObjMdPetIntegParametroDTO[$i]);
      }

      //Auditoria

    }catch(Exception $e){
      throw new InfraException('Erro excluindo Parâmetro.',$e);
    }
  }

  protected function consultarConectado(MdPetIntegParametroDTO $objMdPetIntegParametroDTO){
    try {

      //Valida Permissao
      SessaoSEI::getInstance()->validarPermissao('md_pet_integ_parametro_consultar');

      //Regras de Negocio
      //$objInfraException = new InfraException();

      //$objInfraException->lancarValidacoes();

      $objMdPetIntegParametroBD = new MdPetIntegParametroBD($this->getObjInfraIBanco());
      $ret = $objMdPetIntegParametroBD->consultar($objMdPetIntegParametroDTO);

      //Auditoria

      return $ret;
    }catch(Exception $e){
      throw new InfraException('Erro consultando Parâmetro.',$e);
    }
  }

  protected function listarConectado(MdPetIntegParametroDTO $objMdPetIntegParametroDTO) {
    try {

      //Valida Permissao
      //SessaoSEI::getInstance()->validarPermissao('md_pet_integ_parametro_listar');
      SessaoSEI::getInstance()->validarPermissao('md_pet_integracao_cadastrar');
      
      //Regras de Negocio
      //$objInfraException = new InfraException();

      //$objInfraException->lancarValidacoes();

      $objMdPetIntegParametroBD = new MdPetIntegParametroBD($this->getObjInfraIBanco());
      $ret = $objMdPetIntegParametroBD->listar($objMdPetIntegParametroDTO);

      //Auditoria

      return $ret;

    }catch(Exception $e){
      throw new InfraException('Erro listando Parâmetros.',$e);
    }
  }

  protected function contarConectado(MdPetIntegParametroDTO $objMdPetIntegParametroDTO){
    try {

      //Valida Permissao
      SessaoSEI::getInstance()->validarPermissao('md_pet_integ_parametro_listar');

      //Regras de Negocio
      //$objInfraException = new InfraException();

      //$objInfraException->lancarValidacoes();

      $objMdPetIntegParametroBD = new MdPetIntegParametroBD($this->getObjInfraIBanco());
      $ret = $objMdPetIntegParametroBD->contar($objMdPetIntegParametroDTO);

      //Auditoria

      return $ret;
    }catch(Exception $e){
      throw new InfraException('Erro contando Parâmetros.',$e);
    }
  }
/* 
  protected function desativarControlado($arrObjMdPetIntegParametroDTO){
    try {

      //Valida Permissao
      SessaoSEI::getInstance()->validarPermissao('md_pet_integ_parametro_desativar');

      //Regras de Negocio
      //$objInfraException = new InfraException();

      //$objInfraException->lancarValidacoes();

      $objMdPetIntegParametroBD = new MdPetIntegParametroBD($this->getObjInfraIBanco());
      for($i=0;$i<count($arrObjMdPetIntegParametroDTO);$i++){
        $objMdPetIntegParametroBD->desativar($arrObjMdPetIntegParametroDTO[$i]);
      }

      //Auditoria

    }catch(Exception $e){
      throw new InfraException('Erro desativando Parâmetro.',$e);
    }
  }

  protected function reativarControlado($arrObjMdPetIntegParametroDTO){
    try {

      //Valida Permissao
      SessaoSEI::getInstance()->validarPermissao('md_pet_integ_parametro_reativar');

      //Regras de Negocio
      //$objInfraException = new InfraException();

      //$objInfraException->lancarValidacoes();

      $objMdPetIntegParametroBD = new MdPetIntegParametroBD($this->getObjInfraIBanco());
      for($i=0;$i<count($arrObjMdPetIntegParametroDTO);$i++){
        $objMdPetIntegParametroBD->reativar($arrObjMdPetIntegParametroDTO[$i]);
      }

      //Auditoria

    }catch(Exception $e){
      throw new InfraException('Erro reativando Parâmetro.',$e);
    }
  }

  protected function bloquearControlado(MdPetIntegParametroDTO $objMdPetIntegParametroDTO){
    try {

      //Valida Permissao
      SessaoSEI::getInstance()->validarPermissao('md_pet_integ_parametro_consultar');

      //Regras de Negocio
      //$objInfraException = new InfraException();

      //$objInfraException->lancarValidacoes();

      $objMdPetIntegParametroBD = new MdPetIntegParametroBD($this->getObjInfraIBanco());
      $ret = $objMdPetIntegParametroBD->bloquear($objMdPetIntegParametroDTO);

      //Auditoria

      return $ret;
    }catch(Exception $e){
      throw new InfraException('Erro bloqueando Parâmetro.',$e);
    }
  }

 */
}
