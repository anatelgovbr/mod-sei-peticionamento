<?
/**
* TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
*
* 08/02/2012 - criado por bcu
*
* Versão do Gerador de Código: 1.32.1
*
* Versão no CVS: $Id$
*/

require_once dirname(__FILE__).'/../../../SEI.php';

class ArquivoExtensaoPeticionamentoRN extends ArquivoExtensaoRN {
/*
  public function __construct(){
    parent::__construct();
  }

  protected function inicializarObjInfraIBanco(){
    return BancoSEI::getInstance();
  }

  private function validarStrExtensao(ArquivoExtensaoDTO $objArquivoExtensaoDTO, InfraException $objInfraException){
    if (InfraString::isBolVazia($objArquivoExtensaoDTO->getStrExtensao())){
      $objInfraException->adicionarValidacao('Extensão não informada.');
    }else{
      $objArquivoExtensaoDTO->setStrExtensao(trim($objArquivoExtensaoDTO->getStrExtensao()));

      if(substr($objArquivoExtensaoDTO->getStrExtensao(),0,1)=='.') {
      	$objInfraException->adicionarValidacao('Não cadastrar o ponto inicial da Extensão.');
      }
      if (strlen($objArquivoExtensaoDTO->getStrExtensao())>10){
        $objInfraException->adicionarValidacao('Extensão possui tamanho superior a 10 caracteres.');
      }
      
      $dto = new ArquivoExtensaoDTO();
      $dto->retStrSinAtivo();
      $dto->setNumIdArquivoExtensao($objArquivoExtensaoDTO->getNumIdArquivoExtensao(),InfraDTO::$OPER_DIFERENTE);
      $dto->setStrExtensao($objArquivoExtensaoDTO->getStrExtensao(),InfraDTO::$OPER_IGUAL);
      $dto->setBolExclusaoLogica(false);
      
      $dto = $this->consultar($dto);
      if ($dto != NULL){
        if ($dto->getStrSinAtivo() == 'S')
          $objInfraException->adicionarValidacao('Existe outra ocorrência cadastrada com a mesma Extensão.');
        else
          $objInfraException->adicionarValidacao('Existe ocorrência inativa cadastrada com a mesma Extensão.');
      }
    }
  }

  private function validarStrDescricao(ArquivoExtensaoDTO $objArquivoExtensaoDTO, InfraException $objInfraException){
    if (InfraString::isBolVazia($objArquivoExtensaoDTO->getStrDescricao())){
      $objArquivoExtensaoDTO->setStrDescricao(null);
    }else{
      $objArquivoExtensaoDTO->setStrDescricao(trim($objArquivoExtensaoDTO->getStrDescricao()));

      if (strlen($objArquivoExtensaoDTO->getStrDescricao())>250){
        $objInfraException->adicionarValidacao('Descrição possui tamanho superior a 250 caracteres.');
      }
    }
  }

  private function validarStrSinAtivo(ArquivoExtensaoDTO $objArquivoExtensaoDTO, InfraException $objInfraException){
    if (InfraString::isBolVazia($objArquivoExtensaoDTO->getStrSinAtivo())){
      $objInfraException->adicionarValidacao('Sinalizador de Exclusão Lógica não informado.');
    }else{
      if (!InfraUtil::isBolSinalizadorValido($objArquivoExtensaoDTO->getStrSinAtivo())){
        $objInfraException->adicionarValidacao('Sinalizador de Exclusão Lógica inválido.');
      }
    }
  }

  protected function cadastrarControlado(ArquivoExtensaoDTO $objArquivoExtensaoDTO) {
    try{

      //Valida Permissao
      SessaoSEI::getInstance()->validarAuditarPermissao('arquivo_extensao_cadastrar',__METHOD__,$objArquivoExtensaoDTO);

      //Regras de Negocio
      $objInfraException = new InfraException();

      $this->validarStrExtensao($objArquivoExtensaoDTO, $objInfraException);
      $this->validarStrDescricao($objArquivoExtensaoDTO, $objInfraException);
      $this->validarStrSinAtivo($objArquivoExtensaoDTO, $objInfraException);

      $objInfraException->lancarValidacoes();

      $objArquivoExtensaoBD = new ArquivoExtensaoBD($this->getObjInfraIBanco());
      $ret = $objArquivoExtensaoBD->cadastrar($objArquivoExtensaoDTO);

      //Auditoria

      return $ret;

    }catch(Exception $e){
      throw new InfraException('Erro cadastrando Extensão de Arquivo.',$e);
    }
  }

  protected function alterarControlado(ArquivoExtensaoDTO $objArquivoExtensaoDTO){
    try {

      //Valida Permissao
  	   SessaoSEI::getInstance()->validarAuditarPermissao('arquivo_extensao_alterar',__METHOD__,$objArquivoExtensaoDTO);

      //Regras de Negocio
      $objInfraException = new InfraException();

      if ($objArquivoExtensaoDTO->isSetStrExtensao()){
        $this->validarStrExtensao($objArquivoExtensaoDTO, $objInfraException);
      }
      if ($objArquivoExtensaoDTO->isSetStrDescricao()){
        $this->validarStrDescricao($objArquivoExtensaoDTO, $objInfraException);
      }
      if ($objArquivoExtensaoDTO->isSetStrSinAtivo()){
        $this->validarStrSinAtivo($objArquivoExtensaoDTO, $objInfraException);
      }

      $objInfraException->lancarValidacoes();

      $objArquivoExtensaoBD = new ArquivoExtensaoBD($this->getObjInfraIBanco());
      $objArquivoExtensaoBD->alterar($objArquivoExtensaoDTO);

      //Auditoria

    }catch(Exception $e){
      throw new InfraException('Erro alterando Extensão de Arquivo.',$e);
    }
  }

  protected function excluirControlado($arrObjArquivoExtensaoDTO){
    try {

      //Valida Permissao
      SessaoSEI::getInstance()->validarAuditarPermissao('arquivo_extensao_excluir',__METHOD__,$arrObjArquivoExtensaoDTO);

      //Regras de Negocio
      //$objInfraException = new InfraException();

      //$objInfraException->lancarValidacoes();

      $objArquivoExtensaoBD = new ArquivoExtensaoBD($this->getObjInfraIBanco());
      for($i=0;$i<count($arrObjArquivoExtensaoDTO);$i++){
        $objArquivoExtensaoBD->excluir($arrObjArquivoExtensaoDTO[$i]);
      }

      //Auditoria

    }catch(Exception $e){
      throw new InfraException('Erro excluindo Extensão de Arquivo.',$e);
    }
  }

  protected function consultarConectado(ArquivoExtensaoDTO $objArquivoExtensaoDTO){
    try {

      //Valida Permissao
      SessaoSEI::getInstance()->validarAuditarPermissao('arquivo_extensao_consultar',__METHOD__,$objArquivoExtensaoDTO);

      //Regras de Negocio
      //$objInfraException = new InfraException();

      //$objInfraException->lancarValidacoes();

      $objArquivoExtensaoBD = new ArquivoExtensaoBD($this->getObjInfraIBanco());
      $ret = $objArquivoExtensaoBD->consultar($objArquivoExtensaoDTO);

      //Auditoria

      return $ret;
    }catch(Exception $e){
      throw new InfraException('Erro consultando Extensão de Arquivo.',$e);
    }
  }

  protected function listarConectado(ArquivoExtensaoDTO $objArquivoExtensaoDTO) {
    try {

      //Valida Permissao
      SessaoSEI::getInstance()->validarAuditarPermissao('arquivo_extensao_listar',__METHOD__,$objArquivoExtensaoDTO);

      //Regras de Negocio
      //$objInfraException = new InfraException();

      //$objInfraException->lancarValidacoes();

      $objArquivoExtensaoBD = new ArquivoExtensaoBD($this->getObjInfraIBanco());
      $ret = $objArquivoExtensaoBD->listar($objArquivoExtensaoDTO);

      //Auditoria

      return $ret;

    }catch(Exception $e){
      throw new InfraException('Erro listando Extensões de Arquivos.',$e);
    }
  }

  protected function contarConectado(ArquivoExtensaoDTO $objArquivoExtensaoDTO){
    try {

      //Valida Permissao
      SessaoSEI::getInstance()->validarAuditarPermissao('arquivo_extensao_listar',__METHOD__,$objArquivoExtensaoDTO);

      //Regras de Negocio
      //$objInfraException = new InfraException();

      //$objInfraException->lancarValidacoes();

      $objArquivoExtensaoBD = new ArquivoExtensaoBD($this->getObjInfraIBanco());
      $ret = $objArquivoExtensaoBD->contar($objArquivoExtensaoDTO);

      //Auditoria

      return $ret;
    }catch(Exception $e){
      throw new InfraException('Erro contando Extensões de Arquivos.',$e);
    }
  }

  protected function desativarControlado($arrObjArquivoExtensaoDTO){
    try {

      //Valida Permissao
      SessaoSEI::getInstance()->validarAuditarPermissao('arquivo_extensao_desativar',__METHOD__,$arrObjArquivoExtensaoDTO);

      //Regras de Negocio
      //$objInfraException = new InfraException();

      //$objInfraException->lancarValidacoes();

      $objArquivoExtensaoBD = new ArquivoExtensaoBD($this->getObjInfraIBanco());
      for($i=0;$i<count($arrObjArquivoExtensaoDTO);$i++){
        $objArquivoExtensaoBD->desativar($arrObjArquivoExtensaoDTO[$i]);
      }

      //Auditoria

    }catch(Exception $e){
      throw new InfraException('Erro desativando Extensão de Arquivo.',$e);
    }
  }

  protected function reativarControlado($arrObjArquivoExtensaoDTO){
    try {

      //Valida Permissao
      SessaoSEI::getInstance()->validarAuditarPermissao('arquivo_extensao_reativar',__METHOD__,$arrObjArquivoExtensaoDTO);

      //Regras de Negocio
      //$objInfraException = new InfraException();

      //$objInfraException->lancarValidacoes();

      $objArquivoExtensaoBD = new ArquivoExtensaoBD($this->getObjInfraIBanco());
      for($i=0;$i<count($arrObjArquivoExtensaoDTO);$i++){
        $objArquivoExtensaoBD->reativar($arrObjArquivoExtensaoDTO[$i]);
      }

      //Auditoria

    }catch(Exception $e){
      throw new InfraException('Erro reativando Extensão de Arquivo.',$e);
    }
  }

  protected function bloquearControlado(ArquivoExtensaoDTO $objArquivoExtensaoDTO){
    try {

      //Valida Permissao
      SessaoSEI::getInstance()->validarAuditarPermissao('arquivo_extensao_consultar',__METHOD__,$objArquivoExtensaoDTO);

      //Regras de Negocio
      //$objInfraException = new InfraException();

      //$objInfraException->lancarValidacoes();

      $objArquivoExtensaoBD = new ArquivoExtensaoBD($this->getObjInfraIBanco());
      $ret = $objArquivoExtensaoBD->bloquear($objArquivoExtensaoDTO);

      //Auditoria

      return $ret;
    }catch(Exception $e){
      throw new InfraException('Erro bloqueando Extensão de Arquivo.',$e);
    }
  }
*/

   //
   // @author Alan Campos <alan.campos@castgroup.com.br>
   //
  protected function listarAutoCompleteConectado(ArquivoExtensaoDTO $objArquivoExtensaoDTO) {
  	try {
  
  		//Valida Permissao
  		SessaoSEI::getInstance()->validarAuditarPermissao('arquivo_extensao_listar',__METHOD__,$objArquivoExtensaoDTO);
 
  
  		$objArquivoExtensaoBD = new ArquivoExtensaoBD($this->getObjInfraIBanco());
  		$objArquivoExtensaoDTO->setStrExtensao('%'.$objArquivoExtensaoDTO->getStrPalavrasPesquisa().'%',InfraDTO::$OPER_LIKE);
  		
  		$ret = $objArquivoExtensaoBD->listar($objArquivoExtensaoDTO);
  
  		//Auditoria
  
  		return $ret;
  
  	}catch(Exception $e){
  		throw new InfraException('Erro listando Extensões de Arquivos.',$e);
  	}
  }	
	
}
?>