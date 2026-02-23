<?
/**
* TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
*
* 11/01/2008 - criado por marcio_db
*
* Versão do Gerador de Código: 1.12.0
*
* Versão no CVS: $Id$
*/

require_once dirname(__FILE__) . '/../../../SEI.php';

class MdPetUsuarioExternoRN extends InfraRN {

  public static $POS_USUARIO_SIGLA = 0;
  public static $POS_USUARIO_NOME = 1;
  public static $POS_USUARIO_UNIDADES = 2;

  public static $TU_SIP = '0';
  public static $TU_SISTEMA = '1';
  public static $TU_EXTERNO_PENDENTE = '2';
  public static $TU_EXTERNO = '3';
  public static $MSG_GERAR_SENHA = 'Você ainda não possui uma senha registrada no sistema.\nPara assinatura com senha acesse a opção Gerar Senha no menu.';	

  
  public function __construct(){
    parent::__construct();
  }

  protected function inicializarObjInfraIBanco(){
    return BancoSEI::getInstance();
  }

  protected function cadastrarExternoControlado(UsuarioDTO $objUsuarioDTO) {
    try{
      $objInfraException = new InfraException();

      $this->validarStrSinEstrangeiro($objUsuarioDTO, $objInfraException );
      if($objUsuarioDTO->getStrSinEstrangeiro() == "S") {
        $this->validarStrNumeroPassaporte($objUsuarioDTO, $objInfraException);
        $this->validarNumIdPaisPassaporte($objUsuarioDTO, $objInfraException);
      }else{
        $this->validarDblCpf($objUsuarioDTO, $objInfraException);;
        $this->validarDblRg($objUsuarioDTO, $objInfraException);;
        $this->validarStrOrgaoExpedidor($objUsuarioDTO, $objInfraException);;
      }
      $objInfraException->lancarValidacoes();

      //Valida Permissao
      $objUsuarioDTOAuditoria = clone($objUsuarioDTO);
      $objUsuarioDTOAuditoria->unSetStrSenha();
      SessaoSEI::getInstance()->validarAuditarPermissao('usuario_externo_enviar_cadastro', __METHOD__, $objUsuarioDTOAuditoria);

      $this->cadastrarRN0487($objUsuarioDTO);
      
      //Auditoria

    }catch(Exception $e){
      throw new InfraException('Erro cadastrando Usuário Externo.',$e);
    }
  }

  protected function consultarConectado(UsuarioDTO $objUsuarioDTO){
    try {

      //Valida Permissao
      SessaoSEI::getInstance()->validarAuditarPermissao('usuario_consultar',__METHOD__,$objUsuarioDTO);

      //Regras de Negocio
      //$objInfraException = new InfraException();

      //$objInfraException->lancarValidacoes();

      $objUsuarioBD = new UsuarioBD($this->getObjInfraIBanco());
      $ret = $objUsuarioBD->consultar($objUsuarioDTO);

      //Auditoria

      return $ret;
    }catch(Exception $e){
      throw new InfraException('Erro consultando Usuário.',$e);
    }
  }

  protected function listarConectado(MdPetUsuarioExternoDTO $objUsuarioDTO) {
    try {

      //Valida Permissao
      SessaoSEI::getInstance()->validarAuditarPermissao('usuario_listar',__METHOD__,$objUsuarioDTO);

      //Regras de Negocio
      //$objInfraException = new InfraException();

      //$objInfraException->lancarValidacoes();

      $objUsuarioBD = new UsuarioBD($this->getObjInfraIBanco());
      $ret = $objUsuarioBD->listar($objUsuarioDTO);

      //Auditoria

      return $ret;

    }catch(Exception $e){
      throw new InfraException('Erro listando Usuários.',$e);
    }
  }
  
  protected function pesquisarConectado(UsuarioDTO $objUsuarioDTO) {
    try {

      //Valida Permissao
      /////////////////////////////////////////////////////////////////
      SessaoSEI::getInstance()->validarAuditarPermissao('usuario_listar',__METHOD__,$objUsuarioDTO);
      /////////////////////////////////////////////////////////////////

      if ($objUsuarioDTO->isSetStrSigla()){
        if (!InfraString::isBolVazia($objUsuarioDTO->getStrSigla())) {
          $objUsuarioDTO->setStrSigla('%' . trim($objUsuarioDTO->getStrSigla()) . '%', InfraDTO::$OPER_LIKE);
        }else{
          $objUsuarioDTO->unSetStrSigla();
        }
      }

      if ($objUsuarioDTO->isSetStrNome()){
        if (!InfraString::isBolVazia($objUsuarioDTO->getStrNome())) {
          $strPalavrasPesquisa = InfraString::prepararPesquisa($objUsuarioDTO->getStrNome());
          $arrPalavrasPesquisa = explode(' ', $strPalavrasPesquisa);

          for ($i = 0; $i < count($arrPalavrasPesquisa); $i++) {
            $arrPalavrasPesquisa[$i] = '%' . $arrPalavrasPesquisa[$i] . '%';
          }

          if (count($arrPalavrasPesquisa) == 1) {
            $objUsuarioDTO->setStrNome($arrPalavrasPesquisa[0], InfraDTO::$OPER_LIKE);
          } else {
            $objUsuarioDTO->unSetStrNome();
            $a = array_fill(0, count($arrPalavrasPesquisa), 'Nome');
            $b = array_fill(0, count($arrPalavrasPesquisa), InfraDTO::$OPER_LIKE);
            $d = array_fill(0, count($arrPalavrasPesquisa) - 1, InfraDTO::$OPER_LOGICO_AND);
            $objUsuarioDTO->adicionarCriterio($a, $b, $arrPalavrasPesquisa, $d);
          }
        }else{
          $objUsuarioDTO->unSetStrNome();
        }
      }

      if ($objUsuarioDTO->isSetStrPalavrasPesquisa()) {
        if (!InfraString::isBolVazia($objUsuarioDTO->getStrPalavrasPesquisa())) {
          $strPalavrasPesquisa = InfraString::prepararIndexacao($objUsuarioDTO->getStrPalavrasPesquisa());

          $arrPalavrasPesquisa = explode(' ', $strPalavrasPesquisa);

          $numPalavrasPesquisa = count($arrPalavrasPesquisa);

          if ($numPalavrasPesquisa) {
            for ($i = 0; $i < $numPalavrasPesquisa; $i++) {
              $arrPalavrasPesquisa[$i] = '%' . $arrPalavrasPesquisa[$i] . '%';
            }

            if ($numPalavrasPesquisa == 1) {
              $objUsuarioDTO->setStrIdxUsuario($arrPalavrasPesquisa[0], InfraDTO::$OPER_LIKE);
            } else {
              $a = array_fill(0, $numPalavrasPesquisa, 'IdxUsuario');
              $b = array_fill(0, $numPalavrasPesquisa, InfraDTO::$OPER_LIKE);
              $d = array_fill(0, $numPalavrasPesquisa - 1, InfraDTO::$OPER_LOGICO_AND);
              $objUsuarioDTO->adicionarCriterio($a, $b, $arrPalavrasPesquisa, $d);
            }
          }
        } else {
          $objUsuarioDTO->unSetStrPalavrasPesquisa();
        }
      }

      return $this->listarRN0490($objUsuarioDTO);

    }catch(Exception $e){
      throw new InfraException('Erro pesquisando Usuários.',$e);
    }
  }

  protected function contarConectado(UsuarioDTO $objUsuarioDTO){
    try {

      //Valida Permissao
      SessaoSEI::getInstance()->validarAuditarPermissao('usuario_listar',__METHOD__,$objUsuarioDTO);

      //Regras de Negocio
      //$objInfraException = new InfraException();

      //$objInfraException->lancarValidacoes();

      $objUsuarioBD = new UsuarioBD($this->getObjInfraIBanco());
      $ret = $objUsuarioBD->contar($objUsuarioDTO);

      //Auditoria

      return $ret;
    }catch(Exception $e){
      throw new InfraException('Erro contando Usuários.',$e);
    }
  }


  private function validarStrStaTipo(UsuarioDTO $objUsuarioDTO, InfraException $objInfraException){
    if (InfraString::isBolVazia($objUsuarioDTO->getStrStaTipo())){
      $objInfraException->adicionarValidacao('Tipo do usuário não informado.');
    }else{
      if (!in_array($objUsuarioDTO->getStrStaTipo(), array(self::$TU_SIP, self::$TU_SISTEMA, self::$TU_EXTERNO_PENDENTE, self::$TU_EXTERNO))){
        $objInfraException->adicionarValidacao('Tipo do usuário inválido.');
      }
    }
  }

  private function validarStrSinAtivoRN0694(UsuarioDTO $objUsuarioDTO, InfraException $objInfraException){
    if (InfraString::isBolVazia($objUsuarioDTO->getStrSinAtivo())){
      $objInfraException->adicionarValidacao('Sinalizador de Exclusão Lógica não informado.');
    }else{
      if (!InfraUtil::isBolSinalizadorValido($objUsuarioDTO->getStrSinAtivo())){
        $objInfraException->adicionarValidacao('Sinalizador de Exclusão Lógica inválido.');
      }
    }
  }

  public function listarPorUnidadeRN0812($parObjUnidadeDTO){

    $ret = array();

    $strChave = 'SEI_U_'.CacheSEI::getInstance()->getAtributoVersao('SEI_U').'_'.$parObjUnidadeDTO->getNumIdUnidade();

    $arrCache = CacheSEI::getInstance()->getAtributo($strChave);

    if ($arrCache == null) {

      $objInfraSip = new InfraSip(SessaoSEI::getInstance());
      $arr = $objInfraSip->carregarUsuarios(SessaoSEI::getInstance()->getNumIdSistema(), $parObjUnidadeDTO->getNumIdUnidade(), 'procedimento_trabalhar');

      $temp = array_values($arr);

      InfraArray::ordenarArray($temp, InfraSip::$WS_USUARIO_SIGLA, InfraArray::$TIPO_ORDENACAO_ASC);

      $arrCache = array();
      foreach($temp as $usu){
        if ($usu[InfraSip::$WS_USUARIO_SIN_ATIVO] == 'S') {
          $arrCache[] = array($usu[InfraSip::$WS_USUARIO_ID], $usu[InfraSip::$WS_USUARIO_SIGLA], $usu[InfraSip::$WS_USUARIO_NOME]);
        }
      }

      CacheSEI::getInstance()->setAtributo($strChave, $arrCache, CacheSEI::getInstance()->getNumTempo());
    }

    foreach ($arrCache as $dados) {
      $objUsuarioDTO = new UsuarioDTO();
      $objUsuarioDTO->setNumIdUsuario($dados[0]);
      $objUsuarioDTO->setStrSigla($dados[1]);
      $objUsuarioDTO->setStrNome($dados[2]);
      $ret[] = $objUsuarioDTO;
    }

    return $ret;
  }

  private function validarStrSiglaRN0955(UsuarioDTO $objUsuarioDTO, InfraException $objInfraException){
    
    if (InfraString::isBolVazia($objUsuarioDTO->getStrSigla())){
      $objInfraException->adicionarValidacao('Sigla não informada.');
    }else{
      $objUsuarioDTO->setStrSigla(trim($objUsuarioDTO->getStrSigla()));
  
      if (strlen($objUsuarioDTO->getStrSigla())>100){
        $objInfraException->adicionarValidacao('Sigla possui tamanho superior a 100 caracteres.');
        return;
      }
      
      $dto = new UsuarioDTO();
      $dto->retNumIdUsuario();
      $dto->setNumIdUsuario($objUsuarioDTO->getNumIdUsuario(),InfraDTO::$OPER_DIFERENTE);
      $dto->setNumIdOrgao($objUsuarioDTO->getNumIdOrgao());
      $dto->setStrSigla($objUsuarioDTO->getStrSigla());
      $dto = $this->consultarRN0489($dto);
      if ($dto!=null){
      	$objInfraException->adicionarValidacao('Já existe registro para o usuário "'.$objUsuarioDTO->getStrSigla().'".');
      	return;
      }    
    }
    
  }
  
  private function validarNumIdOrgao(UsuarioDTO $objUsuarioDTO, InfraException $objInfraException){
    if (InfraString::isBolVazia($objUsuarioDTO->getNumIdOrgao())){    
      $objInfraException->adicionarValidacao('Órgão do usuário não informado.');
    }
  }
  
  private function validarStrNomeRN0956(UsuarioDTO $objUsuarioDTO, InfraException $objInfraException){
    if (InfraString::isBolVazia($objUsuarioDTO->getStrNome())){
      $objInfraException->adicionarValidacao('Nome não informado.');
    }else{
      $objUsuarioDTO->setStrNome(trim($objUsuarioDTO->getStrNome()));
  
      if (strlen($objUsuarioDTO->getStrNome())>100){
        $objInfraException->adicionarValidacao('Nome possui tamanho superior a 100 caracteres.');
      }
    }
  }

  private function validarStrIdxUsuario(UsuarioDTO $objUsuarioDTO, InfraException $objInfraException){
    if (InfraString::isBolVazia($objUsuarioDTO->getStrIdxUsuario())){
      $objUsuarioDTO->setStrIdxUsuario(null);
    }else{
      $objUsuarioDTO->setStrIdxUsuario(trim($objUsuarioDTO->getStrIdxUsuario()));

      if (strlen($objUsuarioDTO->getStrIdxUsuario())>500){
        $objInfraException->adicionarValidacao('Indexação possui tamanho superior a 500 caracteres.');
      }
    }
  }

  private function validarSenha($strSenha){

    $objInfraException = new InfraException();

    if (InfraString::isBolVazia($strSenha)){
      $objInfraException->lancarValidacao('Senha não informada.');
    }

    $numTamSenhaUsuarioExterno = ConfiguracaoSEI::getInstance()->getValor('SEI', 'TamSenhaUsuarioExterno', false, TAM_SENHA_USUARIO_EXTERNO);

    if (strlen($strSenha) < $numTamSenhaUsuarioExterno){
      $objInfraException->lancarValidacao('Senha deve ser maior que '.$numTamSenhaUsuarioExterno.' caracteres.');
    }

    if(!preg_match("#[0-9]+#", $strSenha)) {
      $objInfraException->lancarValidacao("Senha deve conter pelo menos um número.");
    }

    if(is_numeric($strSenha)) {
      $objInfraException->lancarValidacao("Senha deve conter pelo menos uma letra.");
    }

  }
  
  private function validarStrIdOrigem(UsuarioDTO $objUsuarioDTO, InfraException $objInfraException){
    //
  }  
  
  protected function gerarSenhaControlado(UsuarioDTO $parObjUsuarioDTO){
    try {

      SessaoSEI::getInstance()->validarAuditarPermissao('usuario_externo_gerar_senha',__METHOD__,$parObjUsuarioDTO);
      
      //Valida Permissao
      $objInfraException = new InfraException();  
      
    	$objUsuarioDTO = new UsuarioDTO();
  		$objUsuarioDTO->retNumIdUsuario();
  		$objUsuarioDTO->retStrSigla();
  		$objUsuarioDTO->retStrNome();
  		$objUsuarioDTO->retStrStaTipo();
  		$objUsuarioDTO->retNumIdOrgao();
  		$objUsuarioDTO->retStrSiglaOrgao();
  		$objUsuarioDTO->retStrDescricaoOrgao();
  		$objUsuarioDTO->retStrSitioInternetOrgaoContato();
    	$objUsuarioDTO->setStrSigla($parObjUsuarioDTO->getStrSigla());
    	$objUsuarioDTO->setStrStaTipo(array(self::$TU_EXTERNO_PENDENTE,self::$TU_EXTERNO),InfraDTO::$OPER_IN);
    	
  		$objUsuarioDTO = $this->consultarRN0489($objUsuarioDTO);
  		
  		if ($objUsuarioDTO==null) {
  			$objInfraException->lancarValidacao('Usuário não encontrado.');
  		}
  		
  		if ($objUsuarioDTO->getStrStaTipo()==UsuarioRN::$TU_EXTERNO_PENDENTE){
  			$objInfraException->lancarValidacao('Cadastro do usuário pendente.');
  		}
  		
  		if ($objUsuarioDTO->getStrStaTipo()!=UsuarioRN::$TU_EXTERNO){
  			$objInfraException->lancarValidacao('Usuário não é externo.');
  		}
  		
  		$numCaracteresNovaSenha = ConfiguracaoSEI::getInstance()->getValor('SEI', 'TamSenhaUsuarioExterno', false, TAM_SENHA_USUARIO_EXTERNO);
  		$strNovaSenha = '';
  		for($i=0;$i<$numCaracteresNovaSenha;$i++){
  		  $num = rand(0,61);
  		  if ($num < 10) { 
  		    $strNovaSenha .= $num;//caracter numerico
  		  }else if ($num < 36) {
  		    $strNovaSenha .= chr($num+55); //caracter maiusculo
  		  } else {
  		    $strNovaSenha .= chr($num+61); //caracter minusculo
  		  }		  
      }

      $bcrypt=new InfraBcrypt();

      $dto = new UsuarioDTO();
      $dto->setStrSenha($bcrypt->hash(md5($strNovaSenha)));
      $dto->setNumIdUsuario($objUsuarioDTO->getNumIdUsuario());
		  
      $objUsuarioBD = new UsuarioBD($this->getObjInfraIBanco());
      $objUsuarioBD->alterar($dto);		  
		
      $objInfraParametro = new InfraParametro(BancoSEI::getInstance());
		  
			$objEmailSistemaDTO = new EmailSistemaDTO();
			$objEmailSistemaDTO->retStrDe();
			$objEmailSistemaDTO->retStrPara();
			$objEmailSistemaDTO->retStrAssunto();
			$objEmailSistemaDTO->retStrConteudo();
			$objEmailSistemaDTO->setNumIdEmailSistema(EmailSistemaRN::$ES_GERACAO_SENHA_USUARIO_EXTERNO);
			
			$objEmailSistemaRN = new EmailSistemaRN();
			$objEmailSistemaDTO = $objEmailSistemaRN->consultar($objEmailSistemaDTO);
      
			if ($objEmailSistemaDTO!=null){
  		  $strDe = $objEmailSistemaDTO->getStrDe();
  		  $strDe = str_replace('@sigla_sistema@',SessaoSEI::getInstance()->getStrSiglaSistema(),$strDe);
  		  $strDe = str_replace('@email_sistema@',$objInfraParametro->getValor('SEI_EMAIL_SISTEMA'),$strDe);
  		  
  		  $strPara = $objEmailSistemaDTO->getStrPara();
  		  $strPara = str_replace('@email_usuario_externo@',$objUsuarioDTO->getStrSigla(),$strPara);
  				  
  		  $strAssunto = $objEmailSistemaDTO->getStrAssunto();
        $strAssunto = str_replace('@sigla_sistema@',SessaoSEI::getInstance()->getStrSiglaSistema(),$strAssunto);
        $strAssunto = str_replace('@sigla_orgao@',$objUsuarioDTO->getStrSiglaOrgao(),$strAssunto);

  		  $strConteudo = $objEmailSistemaDTO->getStrConteudo();
  		  $strConteudo = str_replace('@nome_usuario_externo@',$objUsuarioDTO->getStrNome(),$strConteudo);
  		  $strConteudo = str_replace('@email_usuario_externo@',$objUsuarioDTO->getStrSigla(),$strConteudo);
  		  $strConteudo = str_replace('@link_login_usuario_externo@',ConfiguracaoSEI::getInstance()->getValor('SEI','URL').'/controlador_externo.php?acao=usuario_externo_logar&id_orgao_acesso_externo='.$objUsuarioDTO->getNumIdOrgao(),$strConteudo);
        $strConteudo = str_replace('@sigla_orgao@',$objUsuarioDTO->getStrSiglaOrgao(),$strConteudo);
        $strConteudo = str_replace('@descricao_orgao@',$objUsuarioDTO->getStrDescricaoOrgao(),$strConteudo);
        $strConteudo = str_replace('@sitio_internet_orgao@',$objUsuarioDTO->getStrSitioInternetOrgaoContato(),$strConteudo);
        $strConteudo = str_replace('@nova_senha_usuario_externo@',$strNovaSenha,$strConteudo);

        $objEmailDTO = new EmailDTO();
        $objEmailDTO->setStrDe($strDe);
        $objEmailDTO->setStrPara($strPara);
        $objEmailDTO->setStrAssunto($strAssunto);
        $objEmailDTO->setStrMensagem($strConteudo);

        EmailRN::processar(array($objEmailDTO));
			}
			      
    } catch(Exception $e){
      throw new InfraException('Erro gerando nova senha para usuário externo.',$e);
    }
  }

  protected function alterarSenhaControlado(UsuarioDTO $parObjUsuarioDTO){
    try {

      //Valida Permissao
      $parObjUsuarioDTOAuditoria = clone($parObjUsuarioDTO);
      $parObjUsuarioDTOAuditoria->unSetStrSenha();
      $parObjUsuarioDTOAuditoria->unSetStrSenhaNova();
      SessaoSEI::getInstance()->validarAuditarPermissao('usuario_externo_alterar_senha',__METHOD__,$parObjUsuarioDTOAuditoria);
      
      $objInfraException = new InfraException();  

    	$objUsuarioDTO = new UsuarioDTO();
  		$objUsuarioDTO->retNumIdUsuario();
  		$objUsuarioDTO->retStrStaTipo();
  		$objUsuarioDTO->retStrSenha();
    	$objUsuarioDTO->setStrSigla($parObjUsuarioDTO->getStrSigla());
    	$objUsuarioDTO->setNumIdOrgao($parObjUsuarioDTO->getNumIdOrgao());
    	
  		$objUsuarioDTO = $this->consultarRN0489($objUsuarioDTO);
  		
  		if ($objUsuarioDTO==null) {
  			$objInfraException->lancarValidacao('Usuário não encontrado.');
  		}
  		
  		if ($objUsuarioDTO->getStrStaTipo()==UsuarioRN::$TU_EXTERNO_PENDENTE){
  			$objInfraException->lancarValidacao('Cadastro do usuário pendente.');
  		}
  		
  		if ($objUsuarioDTO->getStrStaTipo()!=UsuarioRN::$TU_EXTERNO){
  			$objInfraException->lancarValidacao('Usuário não é externo.');
  		}
      $bcrypt = new InfraBcrypt();

      $senhaBanco=$objUsuarioDTO->getStrSenha();
      $senhaInformada=md5($parObjUsuarioDTO->getStrSenha());
      if (!$bcrypt->verificar($senhaInformada,$senhaBanco)) {
        $objInfraException->lancarValidacao('Senha Atual inválida.');
      }

      $this->validarSenha($parObjUsuarioDTO->getStrSenhaNova());

      $dto = new UsuarioDTO();
      $dto->setStrSenha($bcrypt->hash(md5($parObjUsuarioDTO->getStrSenhaNova())));
      $dto->setNumIdUsuario($objUsuarioDTO->getNumIdUsuario());
		  
      $objUsuarioBD = new UsuarioBD($this->getObjInfraIBanco());
      $objUsuarioBD->alterar($dto);		  
      
    } catch(Exception $e){
      throw new InfraException('Erro alterando senha de usuário externo.',$e);
    }
  }

  private function obterTipoContatoUsuarios(OrgaoDTO $objOrgaoDTO){
    try {

      $objInfraParametro = new InfraParametro(BancoSEI::getInstance());
      $numIdTipoContato = $objInfraParametro->getValor($objOrgaoDTO->getStrSigla() . '_ID_TIPO_CONTATO_USUARIOS',false);

      if (InfraString::isBolVazia($numIdTipoContato)) {

        $objTipoContatoDTO = new TipoContatoDTO();
        $objTipoContatoDTO->setNumIdTipoContato(null);
        $objTipoContatoDTO->setStrNome('Usuários ' . $objOrgaoDTO->getStrSigla());
        $objTipoContatoDTO->setStrDescricao('Usuários ' . $objOrgaoDTO->getStrSigla());
        $objTipoContatoDTO->setStrStaAcesso(TipoContatoRN::$TA_CONSULTA_RESUMIDA);
        $objTipoContatoDTO->setStrSinSistema('S');
        $objTipoContatoDTO->setStrSinAtivo('S');

        $objTipoContatoRN = new TipoContatoRN();
        $objTipoContatoDTO = $objTipoContatoRN->cadastrarRN0334($objTipoContatoDTO);

        $numIdTipoContato = $objTipoContatoDTO->getNumIdTipoContato();

        $objRelUnidadeTipoContatoDTO = new RelUnidadeTipoContatoDTO();
        $objRelUnidadeTipoContatoDTO->setNumIdTipoContato($numIdTipoContato);
        $objRelUnidadeTipoContatoDTO->setNumIdUnidade($objInfraParametro->getValor('ID_UNIDADE_TESTE'));
        $objRelUnidadeTipoContatoDTO->setStrStaAcesso(TipoContatoRN::$TA_ALTERACAO);

        $objRelUnidadeTipoContatoRN = new RelUnidadeTipoContatoRN();
        $objRelUnidadeTipoContatoRN->cadastrarRN0545($objRelUnidadeTipoContatoDTO);

        $objInfraParametro->setValor($objOrgaoDTO->getStrSigla() . '_ID_TIPO_CONTATO_USUARIOS', $numIdTipoContato);
      }

      return $numIdTipoContato;

    } catch (Exception $e) {
      throw new InfraException('Erro obtendo tipo do contato associado com o usuário.', $e);
    }
  }

  protected function obterUsuariosRelacionadosConectado(UsuarioDTO $parObjUsuarioDTO){

    try{

      $objUsuarioDTO = new UsuarioDTO();
      $objUsuarioDTO->setBolExclusaoLogica(false);
      $objUsuarioDTO->retStrIdOrigem();
      $objUsuarioDTO->retStrNome();
      $objUsuarioDTO->retStrStaTipo();
      $objUsuarioDTO->retDblCpfContato();
      $objUsuarioDTO->setNumIdContato($parObjUsuarioDTO->getNumIdContato());
      $objUsuarioDTO->setStrStaTipo(array(UsuarioRN::$TU_SIP, UsuarioRN::$TU_EXTERNO), InfraDTO::$OPER_IN);

      $objUsuarioRN = new UsuarioRN();
      $arrObjUsuarioDTO = $objUsuarioRN->listarRN0490($objUsuarioDTO);

      if (count($arrObjUsuarioDTO)){

        $arrIdOrigem = array();
        $arrCpfContato = array();
        $arrStaTipoContato = array();
        foreach($arrObjUsuarioDTO as $objUsuarioDTO){

          $arrStaTipoContato[$objUsuarioDTO->getStrStaTipo()] = true;

          if ($objUsuarioDTO->getStrIdOrigem()!=null){
            $arrIdOrigem[$objUsuarioDTO->getStrIdOrigem()] = true;
          }

          if ($objUsuarioDTO->getDblCpfContato()!=null){
            $arrCpfContato[$objUsuarioDTO->getDblCpfContato()] = true;
          }
        }

        $arrStaTipoContato = array_keys($arrStaTipoContato);
        $arrIdOrigem = array_keys($arrIdOrigem);
        $arrCpfContato = array_keys($arrCpfContato);

        $objUsuarioDTO2 = new UsuarioDTO();
        $objUsuarioDTO2->setBolExclusaoLogica(false);
        $objUsuarioDTO2->retNumIdUsuario();
        $objUsuarioDTO2->retNumIdContato();
        $objUsuarioDTO2->retStrSigla();
        $objUsuarioDTO2->retStrNome();
        $objUsuarioDTO2->retStrIdOrigem();
        $objUsuarioDTO2->retDblCpfContato();

        $objUsuarioDTO2->setStrStaTipo($arrStaTipoContato, InfraDTO::$OPER_IN);

        if (count($arrIdOrigem) && count($arrCpfContato)) {
          $objUsuarioDTO2->adicionarCriterio(array('IdOrigem','CpfContato'),
                                             array(InfraDTO::$OPER_IN, InfraDTO::$OPER_IN),
                                             array($arrIdOrigem, $arrCpfContato),
                                             InfraDTO::$OPER_LOGICO_OR);
        } else if (count($arrIdOrigem)){
          $objUsuarioDTO2->setStrIdOrigem($arrIdOrigem, InfraDTO::$OPER_IN);
        } else if (count($arrCpfContato)) {
          $objUsuarioDTO2->setDblCpfContato($arrCpfContato, InfraDTO::$OPER_IN);
        } else {
          $objUsuarioDTO2->setNumIdContato($parObjUsuarioDTO->getNumIdContato());
        }

        $arrObjUsuarioDTO = $objUsuarioRN->listarRN0490($objUsuarioDTO2);
      }

      return $arrObjUsuarioDTO;

    } catch (Exception $e) {
      throw new InfraException('Erro obtendo usuários relacionados.');
    }
  }

  protected function listarCargoFuncaoConectado(UsuarioDTO $parObjUsuarioDTO){

    $objUsuarioDTO = new UsuarioDTO();
    $objUsuarioDTO->setBolExclusaoLogica(false);
    $objUsuarioDTO->retNumIdUsuario();
    $objUsuarioDTO->retNumIdOrgao();
    $objUsuarioDTO->retStrSigla();
    $objUsuarioDTO->retStrIdOrigem();
    $objUsuarioDTO->retStrStaTipo();
    $objUsuarioDTO->retStrExpressaoCargoContato();
    $objUsuarioDTO->setNumIdUsuario($parObjUsuarioDTO->getNumIdUsuario());

    $objUsuarioDTO = $this->consultarRN0489($objUsuarioDTO);

    $arrCargoFuncao = array();

    if ($objUsuarioDTO!=null) {

      if ($objUsuarioDTO->getStrStaTipo() == UsuarioRN::$TU_SIP) {

        $objRelAssinanteUnidadeDTO = new RelAssinanteUnidadeDTO();
        $objRelAssinanteUnidadeDTO->retNumIdAssinante();
        $objRelAssinanteUnidadeDTO->setNumIdUnidade(SessaoSEI::getInstance()->getNumIdUnidadeAtual());

        $objRelAssinanteUnidadeRN = new RelAssinanteUnidadeRN();
        $arrObjRelAssinanteUnidadeDTO = $objRelAssinanteUnidadeRN->listarRN1380($objRelAssinanteUnidadeDTO);

        if (count($arrObjRelAssinanteUnidadeDTO) > 0) {
          $objAssinanteDTO = new AssinanteDTO();
          $objAssinanteDTO->retStrCargoFuncao();
          $objAssinanteDTO->setNumIdAssinante(InfraArray::converterArrInfraDTO($arrObjRelAssinanteUnidadeDTO, 'IdAssinante'), InfraDTO::$OPER_IN);

          $objAssinanteRN = new AssinanteRN();
          $arrCargoFuncao = InfraArray::converterArrInfraDTO($objAssinanteRN->listarRN1339($objAssinanteDTO),'CargoFuncao');
        }

        if ($objUsuarioDTO->getStrExpressaoCargoContato() != null) {
          $arrCargoFuncao[] = $objUsuarioDTO->getStrExpressaoCargoContato();
        }

        if (ConfiguracaoSEI::getInstance()->isSetValor('RH', 'CargoFuncao') && !InfraString::isBolVazia(ConfiguracaoSEI::getInstance()->getValor('RH', 'CargoFuncao'))) {

          if ($objUsuarioDTO->getStrIdOrigem() != null) {

            $objWS = null;

            try {

              $strWSDL = ConfiguracaoSEI::getInstance()->getValor('RH', 'CargoFuncao');

              if (!@file_get_contents($strWSDL)) {
                throw new InfraException('Falha na leitura do arquivo WSDL (' . $strWSDL . ')');
              }

              $objWS = new SoapClient($strWSDL, array('encoding' => 'ISO-8859-1'));

            } catch (Exception $e) {
              throw new InfraException('Falha na conexão com o sistema de RH.', $e);
            }

            try {
              $ret = $objWS->listarCargoFuncao($objUsuarioDTO->getNumIdOrgao(), $objUsuarioDTO->getStrIdOrigem(), $objUsuarioDTO->getStrSigla());
            } catch (Exception $e) {
              throw new InfraException('Erro obtendo dados de cargo/função do sistema de RH.', $e);
            }

            if ($ret != null && $ret->CargoFuncao != null) {

              if (!is_array($ret->CargoFuncao)) {
                $ret->CargoFuncao = array($ret->CargoFuncao);
              }

              foreach ($ret->CargoFuncao as $strCargoFuncao) {

                if (!in_array($strCargoFuncao, $arrCargoFuncao)) {
                  $arrCargoFuncao[] = $strCargoFuncao;
                }
              }
            }
          }
        }

        $arrCargoFuncao = array_unique($arrCargoFuncao);

        sort($arrCargoFuncao);

      } else if ($objUsuarioDTO->getStrStaTipo() == UsuarioRN::$TU_EXTERNO) {

        if ($objUsuarioDTO->getStrExpressaoCargoContato() != null) {
          $arrCargoFuncao[] = $objUsuarioDTO->getStrExpressaoCargoContato();
        }

        $arrCargoFuncao[] = 'Usuário Externo';
      }
    }

    return InfraArray::gerarArrInfraDTO('AssinanteDTO', 'CargoFuncao', $arrCargoFuncao);
  }

  private function validarStrNumeroPassaporte(UsuarioDTO $objUsuarioDTO, InfraException $objInfraException){
    if(InfraString::isBolVazia($objUsuarioDTO->getStrNumeroPassaporte())){
      $objInfraException->adicionarValidacao(_('Número do Passaporte não informado.'));
    }else if (preg_match("/[^0-9A-Z-\s]/",$objUsuarioDTO->getStrNumeroPassaporte())){
      $objInfraException->adicionarValidacao(_('Número do Passaporte possui caracteres inválidos.'));
    }
  }

  private function validarStrSinEstrangeiro(UsuarioDTO $objUsuarioDTO, InfraException $objInfraException){
    if (InfraString::isBolVazia($objUsuarioDTO->getStrSinEstrangeiro())){
      $objInfraException->adicionarValidacao(_('Sinalizador de usuário estrangeiro não informado.'));
    }else{
      if (!InfraUtil::isBolSinalizadorValido($objUsuarioDTO->getStrSinEstrangeiro())){
        $objInfraException->adicionarValidacao(_('Sinalizador de usuário estrangeiro inválido.'));
      }
    }
  }

  private function validarNumIdPaisPassaporte(UsuarioDTO $objUsuarioDTO, InfraException $objInfraException){
    if (InfraString::isBolVazia($objUsuarioDTO->getNumIdPaisPassaporte())){
      $objInfraException->adicionarValidacao(_('País de Emissão não selecionado.'));
    }
  }

  private function validarDblCpf(UsuarioDTO $objUsuarioDTO, InfraException $objInfraException){
    if(!InfraUtil::validarCpf($objUsuarioDTO->getDblCpfContato())){
      $objInfraException->adicionarValidacao(_('Número de CPF inválido.'));
    }
  }

  private function validarDblRg(UsuarioDTO $objUsuarioDTO, InfraException $objInfraException){
    if (InfraString::isBolVazia($objUsuarioDTO->getDblRgContato())){
      $objInfraException->adicionarValidacao(_('RG não informado.'));
    }
  }

  private function validarStrOrgaoExpedidor(UsuarioDTO $objUsuarioDTO, InfraException $objInfraException){
    if (InfraString::isBolVazia($objUsuarioDTO->getStrOrgaoExpedidorContato())){
      $objInfraException->adicionarValidacao(_('Órgão Expedidor não informado.'));
    }else if (strlen($objUsuarioDTO->getStrOrgaoExpedidorContato()) > 50) {
      $objInfraException->adicionarValidacao(_('Órgão Expedidor possui tamanho superior a 50 caracteres.'));
    }
  }

  public static function usuarioSsoSemSenha(){

        $objUsuarioDTO = new UsuarioDTO();
        $objUsuarioDTO->retNumIdUsuario();
        $objUsuarioDTO->retStrSigla();
        $objUsuarioDTO->retStrSenha();
        $objUsuarioDTO->retStrSinGovBr();
        $objUsuarioDTO->setStrSigla( SessaoSEIExterna::getInstance()->getStrSiglaUsuarioExterno() );
        $objUsuarioDTO = (new UsuarioRN())->consultarRN0489($objUsuarioDTO);

        return $objUsuarioDTO->getStrSinGovBr() == 'S' && InfraString::isBolVazia($objUsuarioDTO->getStrSenha());

  }


}
?>