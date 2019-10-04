<?
/**
* TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
*
* 25/01/2018 - criado por Usuário
*
* Versão do Gerador de Código: 1.41.0
*/

require_once dirname(__FILE__).'/../../../SEI.php';

class MdPetIntegFuncionalidRN extends InfraRN {

    public static $ID_FUNCIONALIDADE_CNPJ_RECEITA_FEDERAL = '1';

  public function __construct(){
    parent::__construct();
  }

  protected function inicializarObjInfraIBanco(){
    return BancoSEI::getInstance();
  }

  private function validarStrNome(MdPetIntegFuncionalidDTO $objMdPetIntegFuncionalidDTO, InfraException $objInfraException){
    if (InfraString::isBolVazia($objMdPetIntegFuncionalidDTO->getStrNome())){
      $objInfraException->adicionarValidacao('Funcionalidade não informad.');
    }else{
      $objMdPetIntegFuncionalidDTO->setStrNome(trim($objMdPetIntegFuncionalidDTO->getStrNome()));

      if (strlen($objMdPetIntegFuncionalidDTO->getStrNome())>100){
        $objInfraException->adicionarValidacao('Funcionalidade possui tamanho superior a 100 caracteres.');
      }
    }
  }

  protected function cadastrarControlado(MdPetIntegFuncionalidDTO $objMdPetIntegFuncionalidDTO) {
    try{

      //Valida Permissao
      SessaoSEI::getInstance()->validarPermissao('md_pet_integ_funcionalid_cadastrar');

      //Regras de Negocio
      $objInfraException = new InfraException();

      $this->validarStrNome($objMdPetIntegFuncionalidDTO, $objInfraException);

      $objInfraException->lancarValidacoes();

      $objMdPetIntegFuncionalidBD = new MdPetIntegFuncionalidBD($this->getObjInfraIBanco());
      $ret = $objMdPetIntegFuncionalidBD->cadastrar($objMdPetIntegFuncionalidDTO);

      //Auditoria

      return $ret;

    }catch(Exception $e){
      throw new InfraException('Erro cadastrando Singular.',$e);
    }
  }

  protected function alterarControlado(MdPetIntegFuncionalidDTO $objMdPetIntegFuncionalidDTO){
    try {

      //Valida Permissao
  	   SessaoSEI::getInstance()->validarPermissao('md_pet_integ_funcionalid_alterar');

      //Regras de Negocio
      $objInfraException = new InfraException();

      if ($objMdPetIntegFuncionalidDTO->isSetStrNome()){
        $this->validarStrNome($objMdPetIntegFuncionalidDTO, $objInfraException);
      }

      $objInfraException->lancarValidacoes();

      $objMdPetIntegFuncionalidBD = new MdPetIntegFuncionalidBD($this->getObjInfraIBanco());
      $objMdPetIntegFuncionalidBD->alterar($objMdPetIntegFuncionalidDTO);

      //Auditoria

    }catch(Exception $e){
      throw new InfraException('Erro alterando Singular.',$e);
    }
  }

  protected function excluirControlado($arrObjMdPetIntegFuncionalidDTO){
    try {

      //Valida Permissao
      SessaoSEI::getInstance()->validarPermissao('md_pet_integ_funcionalid_excluir');

      //Regras de Negocio
      //$objInfraException = new InfraException();

      //$objInfraException->lancarValidacoes();

      $objMdPetIntegFuncionalidBD = new MdPetIntegFuncionalidBD($this->getObjInfraIBanco());
      for($i=0;$i<count($arrObjMdPetIntegFuncionalidDTO);$i++){
        $objMdPetIntegFuncionalidBD->excluir($arrObjMdPetIntegFuncionalidDTO[$i]);
      }

      //Auditoria

    }catch(Exception $e){
      throw new InfraException('Erro excluindo Singular.',$e);
    }
  }

  protected function consultarConectado(MdPetIntegFuncionalidDTO $objMdPetIntegFuncionalidDTO){
    try {

      //Valida Permissao
      SessaoSEI::getInstance()->validarPermissao('md_pet_integ_funcionalid_consultar');

      //Regras de Negocio
      //$objInfraException = new InfraException();

      //$objInfraException->lancarValidacoes();

      $objMdPetIntegFuncionalidBD = new MdPetIntegFuncionalidBD($this->getObjInfraIBanco());
      $ret = $objMdPetIntegFuncionalidBD->consultar($objMdPetIntegFuncionalidDTO);

      //Auditoria

      return $ret;
    }catch(Exception $e){
      throw new InfraException('Erro consultando Singular.',$e);
    }
  }

  protected function listarConectado(MdPetIntegFuncionalidDTO $objMdPetIntegFuncionalidDTO) {
    try {

      //Valida Permissao
      SessaoSEI::getInstance()->validarPermissao('md_pet_integ_funcionalid_listar');

      //Regras de Negocio
      //$objInfraException = new InfraException();

      //$objInfraException->lancarValidacoes();

      $objMdPetIntegFuncionalidBD = new MdPetIntegFuncionalidBD($this->getObjInfraIBanco());
      $ret = $objMdPetIntegFuncionalidBD->listar($objMdPetIntegFuncionalidDTO);

      //Auditoria

      return $ret;

    }catch(Exception $e){
      throw new InfraException('Erro listando Plural.',$e);
    }
  }

  protected function contarConectado(MdPetIntegFuncionalidDTO $objMdPetIntegFuncionalidDTO){
    try {

      //Valida Permissao
      SessaoSEI::getInstance()->validarPermissao('md_pet_integ_funcionalid_listar');

      //Regras de Negocio
      //$objInfraException = new InfraException();

      //$objInfraException->lancarValidacoes();

      $objMdPetIntegFuncionalidBD = new MdPetIntegFuncionalidBD($this->getObjInfraIBanco());
      $ret = $objMdPetIntegFuncionalidBD->contar($objMdPetIntegFuncionalidDTO);

      //Auditoria

      return $ret;
    }catch(Exception $e){
      throw new InfraException('Erro contando Plural.',$e);
    }
  }

  public static function verificarMdPetIntegFuncionalidUtilizado($numIdMdPetIntegracao=null, $numIdMdPetIntegFuncionalid=null, $strNomeMdPetIntegFuncionalid=null){

    $objMdPetIntegracaoDTO = new MdPetIntegracaoDTO();
    $objMdPetIntegracaoDTO->retNumIdMdPetIntegFuncionalid();
    $objMdPetIntegracaoDTO->setBolExclusaoLogica(true);
    $objMdPetIntegracaoDTO->setDistinct(true);
    if ($numIdMdPetIntegracao!=null){
      $objMdPetIntegracaoDTO->setNumIdMdPetIntegracao($numIdMdPetIntegracao, InfraDTO::$OPER_DIFERENTE);
    }
    if ($numIdMdPetIntegFuncionalid!=null){
      $objMdPetIntegracaoDTO->setNumIdMdPetIntegFuncionalid($numIdMdPetIntegFuncionalid);
    }
    if ($strNomeMdPetIntegFuncionalid!=null){
      $objMdPetIntegracaoDTO->setStrNomeMdPetIntegFuncionalid($strNomeMdPetIntegFuncionalid);
    }

    $objMdPetIntegracaoRN = new MdPetIntegracaoRN();
    $arrObjMdPetIntegracaoDTO = $objMdPetIntegracaoRN->listar($objMdPetIntegracaoDTO);

    if (count($arrObjMdPetIntegracaoDTO)>0){
      $arrIdMdPetIntegFuncionalidUtilizado = InfraArray::converterArrInfraDTO($arrObjMdPetIntegracaoDTO,'IdMdPetIntegFuncionalid');
    }
 
    return $arrIdMdPetIntegFuncionalidUtilizado;
  }
  
/* 
  protected function desativarControlado($arrObjMdPetIntegFuncionalidDTO){
    try {

      //Valida Permissao
      SessaoSEI::getInstance()->validarPermissao('md_pet_integ_funcionalid_desativar');

      //Regras de Negocio
      //$objInfraException = new InfraException();

      //$objInfraException->lancarValidacoes();

      $objMdPetIntegFuncionalidBD = new MdPetIntegFuncionalidBD($this->getObjInfraIBanco());
      for($i=0;$i<count($arrObjMdPetIntegFuncionalidDTO);$i++){
        $objMdPetIntegFuncionalidBD->desativar($arrObjMdPetIntegFuncionalidDTO[$i]);
      }

      //Auditoria

    }catch(Exception $e){
      throw new InfraException('Erro desativando Singular.',$e);
    }
  }

  protected function reativarControlado($arrObjMdPetIntegFuncionalidDTO){
    try {

      //Valida Permissao
      SessaoSEI::getInstance()->validarPermissao('md_pet_integ_funcionalid_reativar');

      //Regras de Negocio
      //$objInfraException = new InfraException();

      //$objInfraException->lancarValidacoes();

      $objMdPetIntegFuncionalidBD = new MdPetIntegFuncionalidBD($this->getObjInfraIBanco());
      for($i=0;$i<count($arrObjMdPetIntegFuncionalidDTO);$i++){
        $objMdPetIntegFuncionalidBD->reativar($arrObjMdPetIntegFuncionalidDTO[$i]);
      }

      //Auditoria

    }catch(Exception $e){
      throw new InfraException('Erro reativando Singular.',$e);
    }
  }

  protected function bloquearControlado(MdPetIntegFuncionalidDTO $objMdPetIntegFuncionalidDTO){
    try {

      //Valida Permissao
      SessaoSEI::getInstance()->validarPermissao('md_pet_integ_funcionalid_consultar');

      //Regras de Negocio
      //$objInfraException = new InfraException();

      //$objInfraException->lancarValidacoes();

      $objMdPetIntegFuncionalidBD = new MdPetIntegFuncionalidBD($this->getObjInfraIBanco());
      $ret = $objMdPetIntegFuncionalidBD->bloquear($objMdPetIntegFuncionalidDTO);

      //Auditoria

      return $ret;
    }catch(Exception $e){
      throw new InfraException('Erro bloqueando Singular.',$e);
    }
  }

 */
}
