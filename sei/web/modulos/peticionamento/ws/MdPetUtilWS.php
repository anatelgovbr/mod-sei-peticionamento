<?
/*
* TRIBUNAL REGIONAL FEDERAL DA 4� REGI�O
*
* 16/03/2022 - criado por mga
*
*
*/

require_once dirname(__FILE__) . '/../../../SEI.php';

abstract class MdPetUtilWS extends InfraWS {

  public function __call($func, $params) {

    try{


      SessaoSEI::getInstance(false);

      if (!method_exists($this, $func.'Monitorado')) {
        throw new InfraException('Servi�o ['.get_class($this).'.'.$func.'] n�o encontrado.');
      }

      BancoSEI::getInstance()->abrirConexao();

      $objUsuarioDTO = new UsuarioDTO();
      $objUsuarioDTO->retNumIdUsuario();
      $objUsuarioDTO->setStrSigla($params[0]);
      $objUsuarioDTO->setStrStaTipo(UsuarioRN::$TU_SISTEMA);

      $objUsuarioRN = new UsuarioRN();
      $objUsuarioDTO = $objUsuarioRN->consultarRN0489($objUsuarioDTO);

      if ($objUsuarioDTO==null){
        throw new InfraException('Sistema ['.$params[0].'] n�o encontrado.');
      }

      $objServicoRN = new ServicoRN();

      $objServicoDTO = new ServicoDTO();
      $objServicoDTO->retNumIdServico();
      $objServicoDTO->retStrIdentificacao();
      $objServicoDTO->retStrSiglaUsuario();
      $objServicoDTO->retNumIdUsuario();
      $objServicoDTO->retStrServidor();
      $objServicoDTO->retStrSinLinkExterno();
      $objServicoDTO->retNumIdContatoUsuario();
      $objServicoDTO->retStrChaveAcesso();
      $objServicoDTO->retStrSinServidor();
      $objServicoDTO->retStrSinChaveAcesso();
      $objServicoDTO->setNumIdUsuario($objUsuarioDTO->getNumIdUsuario());

      if(strpos($params[1], ' ') === false && strlen($params[1]) == 72 && preg_match("/[0-9a-z]/", $params[1])){

        $objServicoDTO->setStrCrc(substr($params[1],0,8));
        $objServicoDTO = $objServicoRN->consultar($objServicoDTO);

        if ($objServicoDTO==null){
          throw new InfraException('Servi�o do sistema ['.$params[0].'] n�o encontrado.' );
        }

        if ($objServicoDTO->getStrSinChaveAcesso() == 'N'){
          throw new InfraException('Servi�o ['.$objServicoDTO->getStrIdentificacao().'] do sistema ['.$objServicoDTO->getStrSiglaUsuario().'] n�o possui autentica��o por Chave de Acesso.');
        }

        $objInfraBcrypt = new InfraBcrypt();
        if (!$objInfraBcrypt->verificar(md5(substr($params[1],8)), $objServicoDTO->getStrChaveAcesso())) {
          throw new InfraException('Chave de Acesso inv�lida para o servi�o ['.$objServicoDTO->getStrIdentificacao().'] do sistema ['.$objServicoDTO->getStrSiglaUsuario().'].');
        }

      }else{

        $objServicoDTO->setStrIdentificacao($params[1]);

        $objServicoDTO = $objServicoRN->consultar($objServicoDTO);

        if ($objServicoDTO==null){
          throw new InfraException('Servi�o ['.$params[1].'] do sistema ['.$params[0].'] n�o encontrado.');
        }

        if ($objServicoDTO->getStrSinServidor() == 'N'){
          throw new InfraException('Servi�o ['.$params[1].'] do sistema ['.$params[0].'] n�o possui autentica��o por Endere�o.');
        }

        $this->validarAcessoAutorizado(explode(',',str_replace(' ','',$objServicoDTO->getStrServidor())));

      }

      // Valida se ao menos a opera��o * Listar Contatos * est� configurada no Servi�o:
      if (!is_null($objServicoDTO)){
          $operacaoServicoDTO = new OperacaoServicoDTO();
          $operacaoServicoRN = new OperacaoServicoRN();
          $operacaoServicoDTO->setNumStaOperacaoServico(OperacaoServicoRN::$TS_LISTAR_CONTATOS);
          $operacaoServicoDTO->setNumIdServico($objServicoDTO->getNumIdServico());
          $operacaoServicoDTO->retNumIdServico();
          $objOperacaoServicoDTO = $operacaoServicoRN->listar($operacaoServicoDTO);

          if (empty($objOperacaoServicoDTO)) {
              throw new InfraException('Opera��o n�o permitida pois n�o consta para a integra��o deste Sistema e Servi�o ao menos a opera��o "Listar Contatos". Entre em contato com a Administra��o do SEI.');
          }
      }

      SessaoSEI::getInstance()->setObjServicoDTO($objServicoDTO);

      $numSeg = InfraUtil::verificarTempoProcessamento();

      $debugWebServices = (int)ConfiguracaoSEI::getInstance()->getValor('SEI','DebugWebServices',false,0);

      if ($debugWebServices) {
        InfraDebug::getInstance()->setBolLigado(true);
        InfraDebug::getInstance()->setBolDebugInfra(($debugWebServices==2));
        InfraDebug::getInstance()->limpar();

        InfraDebug::getInstance()->gravar("Servi�o: ".$func."\nPar�metros: ".$this->debugParametros($params));

        if ($debugWebServices==1) {
          LogSEI::getInstance()->gravar(InfraDebug::getInstance()->getStrDebug(),InfraLog::$DEBUG);
        }
      }

      $ret = call_user_func_array(array($this, $func.'Monitorado'), $params);

      if ($debugWebServices==2) {
        LogSEI::getInstance()->gravar(InfraDebug::getInstance()->getStrDebug(),InfraLog::$DEBUG);
      }

      try {

        $numSeg = InfraUtil::verificarTempoProcessamento($numSeg);

        $objMonitoramentoServicoDTO = new MonitoramentoServicoDTO();
        $objMonitoramentoServicoDTO->setNumIdServico($objServicoDTO->getNumIdServico());
        $objMonitoramentoServicoDTO->setStrOperacao($func);
        $objMonitoramentoServicoDTO->setDblTempoExecucao($numSeg*1000);
        $objMonitoramentoServicoDTO->setStrIpAcesso(InfraUtil::getStrIpUsuario());
        $objMonitoramentoServicoDTO->setDthAcesso(InfraData::getStrDataHoraAtual());
        $objMonitoramentoServicoDTO->setStrServidor(substr($_SERVER['SERVER_NAME'].' ('.$_SERVER['SERVER_ADDR'].')',0,250));
        $objMonitoramentoServicoDTO->setStrUserAgent(substr($_SERVER['HTTP_USER_AGENT'], 0, 250));

        $objMonitoramentoServicoRN = new MonitoramentoServicoRN();
        $objMonitoramentoServicoRN->cadastrar($objMonitoramentoServicoDTO);

      }catch(Throwable $e){
        try{
          LogSEI::getInstance()->gravar('Erro monitorando acesso do servi�o.'."\n".InfraException::inspecionar($e));
        }catch (Throwable $e){}
      }

      BancoSEI::getInstance()->fecharConexao();

      return $ret;

    }catch(Throwable $e){

      try{
        BancoSEI::getInstance()->fecharConexao();
      }catch(Throwable $e2){}

      $this->processarExcecao($e);
    }
  }

  private function debugParametros($var){
    $ret = '';
    if (is_array($var)) {
      $arr = $var;
      if (isset($arr['Conteudo']) && $arr['Conteudo'] != null) {
        $arr['Conteudo'] = strlen($arr['Conteudo']) . ' bytes';
      }
      if (isset($arr['ConteudoMTOM']) && $arr['ConteudoMTOM'] != null) {
        $arr['ConteudoMTOM'] = strlen($arr['ConteudoMTOM']) . ' bytes';
      }
      $numItens = count($arr);
      for ($i = 0; $i < $numItens; $i++) {
        $arr[$i] = $this->debugParametros($arr[$i]);
      }
      $ret = print_r($arr, true);
    }elseif (is_object($var)) {
      $obj = clone($var);
      if (isset($obj->Conteudo) && $obj->Conteudo != null) {
        $obj->Conteudo = strlen($obj->Conteudo) . ' bytes';
      }
      if (isset($obj->ConteudoMTOM) && $obj->ConteudoMTOM != null) {
        $obj->ConteudoMTOM = strlen($obj->ConteudoMTOM) . ' bytes';
      }
      $ret = print_r($obj, true);
    }else{
      $ret = $var;
    }
    return $ret;
  }

}
