<?
/**
* TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
*
* 07/12/2016 - criado por Marcelo Bezerra
*
* Versão do Gerador de Código: 1.39.0
*/

require_once dirname(__FILE__) . '/../../../SEI.php';

class MdPetRelIntDestExternoRN extends InfraRN {

  public function __construct(){
    parent::__construct();
  }

  protected function inicializarObjInfraIBanco(){
    return BancoSEI::getInstance();
  }

  protected function cadastrarControlado(MdPetRelIntDestExternoDTO $objMdPetRelIntDestExternoDTO) {
    try{
        
      //Valida Permissao
      SessaoSEI::getInstance()->validarPermissao('md_pet_pessoa_fisica');

      //Regras de Negocio
      //$objInfraException = new InfraException();

     // $this->validarNumIdSerie($objMdPetIntSerieDTO, $objInfraException);

      //$objInfraException->lancarValidacoes();

      $objMdPetRelIntDestExternoBD = new MdPetRelIntDestExternoBD($this->getObjInfraIBanco());
      $ret = $objMdPetRelIntDestExternoBD->cadastrar($objMdPetRelIntDestExternoDTO);

      //Auditoria

      return $ret;

    }catch(Exception $e){
      throw new InfraException('Erro cadastrando .',$e);
    }
  }

  //Juridico
  protected function cadastrarJuridicoControlado(MdPetRelIntDestExternoDTO $objMdPetRelIntDestExternoDTO) {
    try{
        
      //Valida Permissao
      SessaoSEI::getInstance()->validarPermissao('md_pet_pessoa_juridica');

      //Regras de Negocio
      //$objInfraException = new InfraException();


      //$objInfraException->lancarValidacoes();

      $objMdPetRelIntDestExternoBD = new MdPetRelIntDestExternoBD($this->getObjInfraIBanco());
      $ret = $objMdPetRelIntDestExternoBD->cadastrar($objMdPetRelIntDestExternoDTO);

      //Auditoria

      return $ret;

    }catch(Exception $e){
      throw new InfraException('Erro cadastrando .',$e);
    }
  }

  protected function alterarControlado(MdPetRelIntDestExternoDTO $objMdPetRelIntDestExternoDTO){
    try {

      //Valida Permissao
//  	   SessaoSEI::getInstance()->validarPermissao('md_pet_int_dest_externo_alterar');

      //Regras de Negocio



      $objMdPetRelIntDestExternoBD = new MdPetRelIntDestExternoBD($this->getObjInfraIBanco());
        $objMdPetRelIntDestExternoBD->alterar($objMdPetRelIntDestExternoDTO);

      //Auditoria

    }catch(Exception $e){
      throw new InfraException('Erro alterando .',$e);
    }
  }

  protected function excluirControlado($arrObjMdPetRelIntDestExternoDTO){
    try {

      //Valida Permissao
//      SessaoSEI::getInstance()->validarPermissao('md_pet_int_dest_externo_excluir');

      //Regras de Negocio
      //$objInfraException = new InfraException();

      //$objInfraException->lancarValidacoes();

      $objMdPetRelIntDestExternoBD = new MdPetRelIntDestExternoBD($this->getObjInfraIBanco());
      for($i=0;$i<count($arrObjMdPetRelIntDestExternoDTO);$i++){
          $objMdPetRelIntDestExternoBD->excluir($arrObjMdPetRelIntDestExternoDTO[$i]);
      }

      //Auditoria

    }catch(Exception $e){
      throw new InfraException('Erro excluindo .',$e);
    }
  }

  protected function consultarConectado(MdPetRelIntDestExternoDTO $objMdPetRelIntDestExternoDTO){
    try {

      //Valida Permissao
//      SessaoSEI::getInstance()->validarPermissao('md_pet_int_dest_externo_consultar');

      //Regras de Negocio
      //$objInfraException = new InfraException();

      //$objInfraException->lancarValidacoes();

      $objMdPetRelIntDestExternoBD = new MdPetRelIntDestExternoBD($this->getObjInfraIBanco());
      $ret = $objMdPetRelIntDestExternoBD->consultar($objMdPetRelIntDestExternoDTO);

      //Auditoria

      return $ret;
    }catch(Exception $e){
      throw new InfraException('Erro consultando .',$e);
    }
  }

  protected function listarConectado(MdPetRelIntDestExternoDTO $objMdPetRelIntDestExternoDTO) {
    try {

      //Valida Permissao
//      SessaoSEI::getInstance()->validarPermissao('md_pet_int_dest_externo_listar');

      //Regras de Negocio
      //$objInfraException = new InfraException();

      //$objInfraException->lancarValidacoes();

      $objMdPetRelIntDestExternoBD = new MdPetRelIntDestExternoBD($this->getObjInfraIBanco());
      $ret = $objMdPetRelIntDestExternoBD->listar($objMdPetRelIntDestExternoDTO);

      //Auditoria

      return $ret;

    }catch(Exception $e){
      throw new InfraException('Erro listando .',$e);
    }
  }

  protected function contarConectado(MdPetRelIntDestExternoDTO $objMdPetRelIntDestExternoDTO){
    try {

      //Valida Permissao
//      SessaoSEI::getInstance()->validarPermissao('md_pet_int_dest_externo_listar');

      //Regras de Negocio
      //$objInfraException = new InfraException();

      //$objInfraException->lancarValidacoes();

      $objMdPetRelIntDestExternoBD = new MdPetRelIntDestExternoBD($this->getObjInfraIBanco());
      $ret = $objMdPetRelIntDestExternoBD->contar($objMdPetRelIntDestExternoDTO);

      //Auditoria

      return $ret;
    }catch(Exception $e){
      throw new InfraException('Erro contando .',$e);
    }
  }

}
?>