<?
/**
* TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
*
* 02/04/2018 - criado por jose vieira
*
* Versão do Gerador de Código: 1.41.0
*/

require_once dirname(__FILE__) . '/../../../SEI.php';

class MdPetVincRepresentantRN extends InfraRN {

  //Procuração Eletrônica
  public static $PE_RESPONSAVEL_LEGAL = 'L';
  public static $PE_PROCURADOR_ESPECIAL = 'E';
  public static $PE_PROCURADOR = 'C';
  public static $PE_PROCURADOR_SUBSTALECIDO = 'S';

  //Representação - Estado
  public static $RP_ATIVO = 'A';
  public static $RP_SUSPENSO = 'S';
  public static $RP_REVOGADA = 'R';
  public static $RP_RENUNCIADA = 'C';
  public static $RP_VENCIDA = 'V';
  public static $RP_SUBSTITUIDA = 'T';

  public function __construct(){
    parent::__construct();
  }

  protected function inicializarObjInfraIBanco(){
    return BancoSEI::getInstance();
  }

  private function validarNumIdMdPetVinculoRepresent(MdPetVincRepresentantDTO $objMdPetVincRepresentantDTO, InfraException $objInfraException){
    if (InfraString::isBolVazia($objMdPetVincRepresentantDTO->getNumIdMdPetVinculoRepresent())){
      $objInfraException->adicionarValidacao('IdMdPetVinculoRepresent não informado.');
    }
  }

  private function validarNumIdMdPetVinculo(MdPetVincRepresentantDTO $objMdPetVincRepresentantDTO, InfraException $objInfraException){
    if (InfraString::isBolVazia($objMdPetVincRepresentantDTO->getNumIdMdPetVinculo())){
      $objInfraException->adicionarValidacao('IdMdPetVinculo não informado.');
    }
  }

  private function validarNumIdContato(MdPetVincRepresentantDTO $objMdPetVincRepresentantDTO, InfraException $objInfraException){
    if (InfraString::isBolVazia($objMdPetVincRepresentantDTO->getNumIdContato())){
      $objInfraException->adicionarValidacao('IdContato não informado.');
    }
  }

  private function validarNumIdContatoOutorg(MdPetVincRepresentantDTO $objMdPetVincRepresentantDTO, InfraException $objInfraException){
    if (InfraString::isBolVazia($objMdPetVincRepresentantDTO->getNumIdContatoOutorg())){
      $objInfraException->adicionarValidacao('IdContatoOutorg não informado.');
    }
  }

  private function validarNumIdAcessoExterno(MdPetVincRepresentantDTO $objMdPetVincRepresentantDTO, InfraException $objInfraException){
    if (InfraString::isBolVazia($objMdPetVincRepresentantDTO->getNumIdAcessoExterno())){
      $objInfraException->adicionarValidacao('IdAcessoExterno não informado.');
    }
  }

  private function validarStrTipoRepresentante(MdPetVincRepresentantDTO $objMdPetVincRepresentantDTO, InfraException $objInfraException){
    if (InfraString::isBolVazia($objMdPetVincRepresentantDTO->getStrTipoRepresentante())){
      $objInfraException->adicionarValidacao(' TipoRepresentante não informado.');
    }else{
      $objMdPetVincRepresentantDTO->setStrTipoRepresentante(trim($objMdPetVincRepresentantDTO->getStrTipoRepresentante()));

      if (strlen($objMdPetVincRepresentantDTO->getStrTipoRepresentante())>1){
        $objInfraException->adicionarValidacao(' possui tamanho superior a 1 caracteres.');
      }
    }
  }

  private function validarStrSinAtivo(MdPetVincRepresentantDTO $objMdPetVincRepresentantDTO, InfraException $objInfraException){
    if (InfraString::isBolVazia($objMdPetVincRepresentantDTO->getStrSinAtivo())){
      $objInfraException->adicionarValidacao('Sinalizador de Exclusão Lógica não informado.');
    }else{
      if (!InfraUtil::isBolSinalizadorValido($objMdPetVincRepresentantDTO->getStrSinAtivo())){
        $objInfraException->adicionarValidacao('Sinalizador de Exclusão Lógica inválido.');
      }
    }
  }

  private function validarDthDataCadastro(MdPetVincRepresentantDTO $objMdPetVincRepresentantDTO, InfraException $objInfraException){
    if (InfraString::isBolVazia($objMdPetVincRepresentantDTO->getDthDataCadastro())){
      $objInfraException->adicionarValidacao('DthDataCadastro não informad.');
    }
  }

  private function validarStrStaEstado(MdPetVincRepresentantDTO $objMdPetVincRepresentantDTO, InfraException $objInfraException){
    if (InfraString::isBolVazia($objMdPetVincRepresentantDTO->getStrStaEstado())){
      $objInfraException->adicionarValidacao('StaEstado não informad.');
    }else{
      if (!in_array($objMdPetVincRepresentantDTO->getStrStaEstado(),InfraArray::converterArrInfraDTO($this->listarValoresEstado(),'StaEstado'))){
        $objInfraException->adicionarValidacao(' inválid.');
      }
    }
  }

  protected function cadastrarControlado(MdPetVincRepresentantDTO $objMdPetVincRepresentantDTO) {
    try{

      //Valida Permissao
      SessaoSEI::getInstance()->validarPermissao('md_pet_vinc_representant_cadastrar');

      //Regras de Negocio
      $objInfraException = new InfraException();

      $this->validarNumIdMdPetVinculo($objMdPetVincRepresentantDTO, $objInfraException);
      $this->validarNumIdContato($objMdPetVincRepresentantDTO, $objInfraException);
      $this->validarNumIdContatoOutorg($objMdPetVincRepresentantDTO, $objInfraException);
      $this->validarStrTipoRepresentante($objMdPetVincRepresentantDTO, $objInfraException);
      $this->validarStrSinAtivo($objMdPetVincRepresentantDTO, $objInfraException);
      $this->validarDthDataCadastro($objMdPetVincRepresentantDTO, $objInfraException);

      $objInfraException->lancarValidacoes();

      $objMdPetVincRepresentantBD = new MdPetVincRepresentantBD($this->getObjInfraIBanco());
      $ret = $objMdPetVincRepresentantBD->cadastrar($objMdPetVincRepresentantDTO);

      //Auditoria

      return $ret;

    }catch(Exception $e){
      throw new InfraException('Erro cadastrando Representante do Vínculo.',$e);
    }
  }

  protected function alterarControlado(MdPetVincRepresentantDTO $objMdPetVincRepresentantDTO){
    try {

      //Valida Permissao
      SessaoSEI::getInstance()->validarPermissao('md_pet_vinc_representant_alterar');

      //Regras de Negocio
      $objInfraException = new InfraException();

      if ($objMdPetVincRepresentantDTO->isSetNumIdMdPetVinculoRepresent()){
        $this->validarNumIdMdPetVinculoRepresent($objMdPetVincRepresentantDTO, $objInfraException);
      }
      if ($objMdPetVincRepresentantDTO->isSetNumIdMdPetVinculo()){
        $this->validarNumIdMdPetVinculo($objMdPetVincRepresentantDTO, $objInfraException);
      }
      if ($objMdPetVincRepresentantDTO->isSetNumIdContato()){
        $this->validarNumIdContato($objMdPetVincRepresentantDTO, $objInfraException);
      }
      if ($objMdPetVincRepresentantDTO->isSetNumIdContatoOutorg()){
        $this->validarNumIdContatoOutorg($objMdPetVincRepresentantDTO, $objInfraException);
      }
      if ($objMdPetVincRepresentantDTO->isSetStrTipoRepresentante()){
        $this->validarStrTipoRepresentante($objMdPetVincRepresentantDTO, $objInfraException);
      }
      if ($objMdPetVincRepresentantDTO->isSetStrSinAtivo()){
        $this->validarStrSinAtivo($objMdPetVincRepresentantDTO, $objInfraException);
      }
      if ($objMdPetVincRepresentantDTO->isSetDthDataCadastro()){
        $this->validarDthDataCadastro($objMdPetVincRepresentantDTO, $objInfraException);
      }

      $objInfraException->lancarValidacoes();

      $objMdPetVincRepresentantBD = new MdPetVincRepresentantBD($this->getObjInfraIBanco());
      $objMdPetVincRepresentantBD->alterar($objMdPetVincRepresentantDTO);

      //Auditoria

    }catch(Exception $e){
      throw new InfraException('Erro alterando Representante do Vínculo.',$e);
    }
  }

  protected function excluirControlado($arrObjMdPetVincRepresentantDTO){
    try {

      //Valida Permissao
      SessaoSEI::getInstance()->validarPermissao('md_pet_vinc_representant_excluir');

      //Regras de Negocio
      //$objInfraException = new InfraException();

      //$objInfraException->lancarValidacoes();

      $objMdPetVincRepresentantBD = new MdPetVincRepresentantBD($this->getObjInfraIBanco());
      for($i=0;$i<count($arrObjMdPetVincRepresentantDTO);$i++){
        $objMdPetVincRepresentantBD->excluir($arrObjMdPetVincRepresentantDTO[$i]);
      }

      //Auditoria

    }catch(Exception $e){
      throw new InfraException('Erro excluindo Representante do Vínculo.',$e);
    }
  }

  protected function consultarConectado(MdPetVincRepresentantDTO $objMdPetVincRepresentantDTO){
    try {

      //Valida Permissao
//      SessaoSEI::getInstance()->validarPermissao('md_pet_vinc_representant_consultar');

      //Regras de Negocio
      //$objInfraException = new InfraException();

      //$objInfraException->lancarValidacoes();
      $objMdPetVincRepresentantBD = new MdPetVincRepresentantBD($this->getObjInfraIBanco());

      $ret = $objMdPetVincRepresentantBD->consultar($objMdPetVincRepresentantDTO);



      //Auditoria

      return $ret;
    }catch(Exception $e){
      throw new InfraException('Erro consultando Representante do Vínculo.',$e);
    }
  }

  protected function listarConectado(MdPetVincRepresentantDTO $objMdPetVincRepresentantDTO) {
    try {

      //Valida Permissao
      SessaoSEI::getInstance()->validarPermissao('md_pet_vinc_usu_ext_pe_listar');

      //Regras de Negocio
      //$objInfraException = new InfraException();

      //$objInfraException->lancarValidacoes();

      $objMdPetVincRepresentantBD = new MdPetVincRepresentantBD($this->getObjInfraIBanco());
      $ret = $objMdPetVincRepresentantBD->listar($objMdPetVincRepresentantDTO);

      return $ret;

    }catch(Exception $e){
      throw new InfraException('Erro listando Representante do Vínculo.',$e);
    }
  }

  protected function contarConectado(MdPetVincRepresentantDTO $objMdPetVincRepresentantDTO){
    try {

      //Valida Permissao
      SessaoSEI::getInstance()->validarPermissao('md_pet_vinc_representant_listar');

      //Regras de Negocio
      //$objInfraException = new InfraException();

      //$objInfraException->lancarValidacoes();

      $objMdPetVincRepresentantBD = new MdPetVincRepresentantBD($this->getObjInfraIBanco());
      $ret = $objMdPetVincRepresentantBD->contar($objMdPetVincRepresentantDTO);
        $ret = $objMdPetVincRepresentantBD->contar($objMdPetVincRepresentantDTO);

      //Auditoria

      return $ret;
    }catch(Exception $e){
      throw new InfraException('Erro contando Representante do Vínculo.',$e);
    }
  }

  protected function desativarControlado($arrObjMdPetVincRepresentantDTO){
    try {

      //Valida Permissao
      SessaoSEI::getInstance()->validarPermissao('md_pet_vinc_representant_desativar');

      //Regras de Negocio
      //$objInfraException = new InfraException();

      //$objInfraException->lancarValidacoes();

      $objMdPetVincRepresentantBD = new MdPetVincRepresentantBD($this->getObjInfraIBanco());
      for($i=0;$i<count($arrObjMdPetVincRepresentantDTO);$i++){
        $objMdPetVincRepresentantBD->desativar($arrObjMdPetVincRepresentantDTO[$i]);
      }

      //Auditoria

    }catch(Exception $e){
      throw new InfraException('Erro desativando Representante do Vínculo.',$e);
    }
  }

  protected function reativarControlado($arrObjMdPetVincRepresentantDTO){
    try {

      //Valida Permissao
      SessaoSEI::getInstance()->validarPermissao('md_pet_vinc_representant_reativar');

      //Regras de Negocio
      //$objInfraException = new InfraException();

      //$objInfraException->lancarValidacoes();

      $objMdPetVincRepresentantBD = new MdPetVincRepresentantBD($this->getObjInfraIBanco());
      for($i=0;$i<count($arrObjMdPetVincRepresentantDTO);$i++){
        $objMdPetVincRepresentantBD->reativar($arrObjMdPetVincRepresentantDTO[$i]);
      }

      //Auditoria

    }catch(Exception $e){
      throw new InfraException('Erro reativando Representante do Vínculo.',$e);
    }
  }

  protected function bloquearControlado(MdPetVincRepresentantDTO $objMdPetVincRepresentantDTO){
    try {

      //Valida Permissao
      SessaoSEI::getInstance()->validarPermissao('md_pet_vinc_representant_consultar');

      //Regras de Negocio
      //$objInfraException = new InfraException();

      //$objInfraException->lancarValidacoes();

      $objMdPetVincRepresentantBD = new MdPetVincRepresentantBD($this->getObjInfraIBanco());
      $ret = $objMdPetVincRepresentantBD->bloquear($objMdPetVincRepresentantDTO);

      //Auditoria

      return $ret;
    }catch(Exception $e){
      throw new InfraException('Erro bloqueando Representante do Vínculo.',$e);
    }
  }

  protected function realizarProcessosAlteracaoResponsavelLegalControlado($dados){

    $objMdPetVinculoUsuExtRN = new MdPetVinculoUsuExtRN();
    $responsavelLegal = $objMdPetVinculoUsuExtRN->verificaMudancaResponsavelLegal($dados);
    $objMdPetVinculoUsuExtRN = new MdPetVinculoUsuExtRN();

    $dados['isAlteradoRespLegal'] = false;
    
    if ($responsavelLegal instanceof MdPetVincRepresentantDTO){

      $idVinculo = isset($_GET['idVinculo']) ? $_GET['idVinculo'] : $_POST['hdnIdVinculo'];
      $numeroSEI = isset($_GET['numeroSEI']) ? $_GET['numeroSEI'] : $_POST['hdnNumeroSei'];
      $this->_realizarProcessoEncerramentoVinculo( array($idVinculo, $numeroSEI) );
      $dados['isAlteradoRespLegal'] = true;
      $dados['NomeProcurador'] = $responsavelLegal->getStrNomeProcurador();
      $dados['CpfProcurador'] = $responsavelLegal->getStrCpfProcurador();
    }

    $reciboGerado = $objMdPetVinculoUsuExtRN->gerarProcedimentoVinculo($dados);
    $idRecibo = $reciboGerado ? $reciboGerado->getNumIdReciboPeticionamento() : '';
    return $idRecibo;
  }

  private function _realizarProcessoEncerramentoVinculo($params){
    $idVinculo = isset($params[0]) ? $params[0] : null;
    $numeroSEI = isset($params[1]) ? $params[1] : null;

    $objMdPetVinculoUsuExtRN = new MdPetVinculoUsuExtRN();

    $objMdPetVincRepresentantRN = new MdPetVincRepresentantRN();
    $objMdPetVincRepresentantDTO = new MdPetVincRepresentantDTO();
    $objMdPetVincRepresentantDTO->retTodos();
    $objMdPetVincRepresentantDTO->setNumIdMdPetVinculo($idVinculo);
    $objMdPetVincRepresentantDTO->setStrTipoRepresentante(MdPetVincRepresentantRN::$PE_RESPONSAVEL_LEGAL);
    $objMdPetVincRepresentantDTO->setStrSinAtivo('S');
    $objMdPetVincRepresentantDTO = $objMdPetVincRepresentantRN->consultar($objMdPetVincRepresentantDTO);

    $idRepresentante = $objMdPetVincRepresentantDTO->getNumIdMdPetVinculoRepresent();

    if (is_numeric($numeroSEI)){
        // Justificativa é um documento
        $objDocumentoRN  = new DocumentoRN();
        $objDocumentoDTO = new DocumentoDTO();
        $numeroSEIFormt = '%'.trim($numeroSEI);
        $objDocumentoDTO->setStrProtocoloDocumentoFormatado($numeroSEIFormt,  InfraDTO::$OPER_LIKE);
        $objDocumentoDTO->retDblIdDocumento();
        $objDocumentoDTO->setNumMaxRegistrosRetorno('1');

        $arrObjDocumentoDTO = $objDocumentoRN->consultarRN0005($objDocumentoDTO);

        $tpProtocolo = MdPetVincDocumentoRN::$TP_PROTOCOLO_RECIBO;
        $objMdPetVinculoUsuExtRN->_adicionarDadosArquivoVinculacao($arrObjDocumentoDTO->getDblIdDocumento(), $idRepresentante, MdPetVincDocumentoRN::$TP_PROTOCOLO_RECIBO);    
    }

    $objMdPetVincRepresentantDTO->setStrMotivo($_POST['txtMotivo']);
    $objMdPetVincRepresentantDTO->setStrSinAtivo('N'); // Desativar
    $objMdPetVincRepresentantDTO->setStrStaEstado(self::$RP_SUBSTITUIDA); // Marcar como Procuração como Substituída
    $objMdPetVincRepresentantRN->alterar($objMdPetVincRepresentantDTO);

    // retornando representante anterior
    return $idRepresentante;
  }

  public function realizarProcessoSuspensaoRestabelecimentoVinculoControlado($params){

  try {
    //$objProcedimentoDTO = isset($params['procedimento']) ? $params['procedimento'] : null;
    $dados     = isset($params['dados']) ? $params['dados'] : null;
    $idVinculo = isset($dados['hdnIdVinculo']) ? $dados['hdnIdVinculo'] : null;
    $numeroSEI = isset($dados['hdnNumeroSei']) ? $dados['hdnNumeroSei'] : null;
    $operacao  = isset($dados['hdnOperacao']) ? $dados['hdnOperacao'] : null;
    
    $objMdPetVinUsuExtProcRN = new MdPetVinUsuExtProcRN();

    $objMdPetVincRepresentantRN  = new MdPetVincRepresentantRN();
    $objMdPetVincRepresentantDTO = new MdPetVincRepresentantDTO();

    if ($operacao==MdPetVincRepresentantRN::$RP_ATIVO){
        $arrIdRepresentantes = $this->getIdRepresentantesVinculo(array($idVinculo,true,false));
        $situacao = MdPetVincRepresentantRN::$RP_SUSPENSO;
    }else if ($operacao==MdPetVincRepresentantRN::$RP_SUSPENSO){        
        $arrIdRepresentantes = $this->getIdRepresentantesVinculo(array($idVinculo,true,true));
        $situacao = MdPetVincRepresentantRN::$RP_ATIVO;
    }

    if (is_array($arrIdRepresentantes)){
    	$objMdPetVincRepresentantDTO->retNumIdMdPetVinculoRepresent();
    	$objMdPetVincRepresentantDTO->retStrTipoRepresentante();
        $objMdPetVincRepresentantDTO->retNumIdContato();
        $objMdPetVincRepresentantDTO->retDthDataCadastro();
    	$objMdPetVincRepresentantDTO->setNumIdMdPetVinculo($idVinculo);
        $objMdPetVincRepresentantDTO->setNumIdMdPetVinculoRepresent($arrIdRepresentantes,InfraDTO::$OPER_IN);
        $objMdPetVincRepresentantDTO->setStrStaEstado($situacao);

        $arrObjMdPetVincRepresentantDTO = $objMdPetVincRepresentantRN->listar($objMdPetVincRepresentantDTO);
        $dtCadastroRespLegal = new DateTime();
        $idContatoResponsavelLegal = '';
        foreach ($arrObjMdPetVincRepresentantDTO as $objMdPetVincRepresentantDTO) {
            
            if ($objMdPetVincRepresentantDTO->getStrTipoRepresentante()==MdPetVincRepresentantRN::$PE_RESPONSAVEL_LEGAL){
                $datahora = explode(' ',$objMdPetVincRepresentantDTO->getDthDataCadastro());
                $data = explode('/', $datahora[0]);
                $hora = explode(':', $datahora[1]);
                $dtCadastroRespLegal->setDate($data[2], $data[1], $data[0]);
                $dtCadastroRespLegal->setTime($hora[0], $hora[1], $hora[2]);

                $idContatoResponsavelLegal = $objMdPetVincRepresentantDTO->getNumIdContato();
                $idMdPetVinculoRepresentLegal = $objMdPetVincRepresentantDTO->getNumIdMdPetVinculoRepresent();
                $representanteDTO = new MdPetVincRepresentantDTO();
                $representanteDTO->setNumIdMdPetVinculoRepresent($objMdPetVincRepresentantDTO->getNumIdMdPetVinculoRepresent());
                $representanteDTO->setStrStaEstado($operacao);
                $objMdPetVincRepresentantRN->alterar($representanteDTO);

            }  
            
        }
        $objMdPetVincRepresentantDTO = new MdPetVincRepresentantDTO();
        $objMdPetVincRepresentantDTO->retNumIdMdPetVinculoRepresent();
    	$objMdPetVincRepresentantDTO->retStrTipoRepresentante();
        $objMdPetVincRepresentantDTO->retDthDataCadastro();
    	$objMdPetVincRepresentantDTO->setNumIdMdPetVinculo($idVinculo);
        $objMdPetVincRepresentantDTO->retNumIdContatoOutorg();
        $objMdPetVincRepresentantDTO->setNumIdMdPetVinculoRepresent($arrIdRepresentantes,InfraDTO::$OPER_IN);
        $objMdPetVincRepresentantDTO->setStrStaEstado($situacao);

        $arrObjMdPetVincRepresentantDTO = $objMdPetVincRepresentantRN->listar($objMdPetVincRepresentantDTO);

        foreach ($arrObjMdPetVincRepresentantDTO as $objMdPetVincRepresentantDTO) {
            $dtCadastroRespLegal2 = new DateTime();
            $datahora2 = explode(' ',$objMdPetVincRepresentantDTO->getDthDataCadastro());
            $data2 = explode('/', $datahora2[0]);
            $hora2 = explode(':', $datahora2[1]);
            $dtCadastroRespLegal2->setDate($data2[2], $data2[1], $data2[0]);
            $dtCadastroRespLegal2->setTime($hora2[0], $hora2[1], $hora2[2]);
            if($dtCadastroRespLegal < $dtCadastroRespLegal2){
                $representanteDTO = new MdPetVincRepresentantDTO();
                $representanteDTO->setNumIdMdPetVinculoRepresent($objMdPetVincRepresentantDTO->getNumIdMdPetVinculoRepresent());
                $representanteDTO->setStrStaEstado($operacao);
                $objMdPetVincRepresentantRN->alterar($representanteDTO);
            }
        }

        $objMdPetVinculoUsuExtRN = new MdPetVinculoUsuExtRN();
        $objProcedimentoDTO = $objMdPetVinculoUsuExtRN->_getObjProcedimentoPorVinculo($idVinculo);

        $params = array(
            'dados' => $dados
            , 'procedimento' => $objProcedimentoDTO
        );

        //gerar dcoumento Suspensão
        if ($operacao==MdPetVincRepresentantRN::$RP_ATIVO){
            $objSaidaIncluirDocumentoAPI  = $objMdPetVinUsuExtProcRN->gerarDocumentoRestabelecimento( $params );
            $staTipoDocumento = MdPetVincDocumentoRN::$TP_PROTOCOLO_RESTABELECIMENTO;
            $staDiligenciaTipoDocumento = MdPetVincDocumentoRN::$TP_PROTOCOLO_DILIGENCIA_RESTABELECIMENTO;
        }else if ($operacao==MdPetVincRepresentantRN::$RP_SUSPENSO){
            $objSaidaIncluirDocumentoAPI = $objMdPetVinUsuExtProcRN->gerarDocumentoSuspensao( $params );
            $staTipoDocumento = MdPetVincDocumentoRN::$TP_PROTOCOLO_SUSPENSAO;
            $staDiligenciaTipoDocumento = MdPetVincDocumentoRN::$TP_PROTOCOLO_DILIGENCIA_SUSPENSAO;
        }

        if (is_numeric($objSaidaIncluirDocumentoAPI->getIdDocumento())){
            $objMdPetVinUsuExtProcRN = new MdPetVinUsuExtProcRN();
            $objMdPetVinUsuExtProcRN->_adicionarDadosArquivoVinculacao($objSaidaIncluirDocumentoAPI->getIdDocumento(), $idMdPetVinculoRepresentLegal, $staTipoDocumento);
            $params['dados']['numeroSeiVinculacao'] = $objSaidaIncluirDocumentoAPI->getIdDocumento();
        }

        if (is_numeric($numeroSEI)){
            
            // Justificativa é um documento
            $objDocumentoRN  = new DocumentoRN();
            $objDocumentoDTO = new DocumentoDTO();
            $numeroSEIFormt = '%'.trim($numeroSEI);
            $objDocumentoDTO->setStrProtocoloDocumentoFormatado($numeroSEIFormt,  InfraDTO::$OPER_LIKE);
            $objDocumentoDTO->retDblIdDocumento();
            $objDocumentoDTO->setNumMaxRegistrosRetorno('1');

            $arrObjDocumentoDTO = $objDocumentoRN->consultarRN0005($objDocumentoDTO);

            $tpProtocolo = MdPetVincDocumentoRN::$TP_PROTOCOLO_RECIBO;

            
            $objMdPetVinUsuExtProcRN = new MdPetVinUsuExtProcRN();
            $objMdPetVinUsuExtProcRN->_adicionarDadosArquivoVinculacao($arrObjDocumentoDTO->getDblIdDocumento(), $idMdPetVinculoRepresentLegal, $staDiligenciaTipoDocumento);
        }
        
        $mdPetVinculoUsuExtRN = new MdPetVinculoUsuExtRN();
        $objMdPetIntimacaoRN = new MdPetIntimacaoRN();
        $objUnidadeDTO = $mdPetVinculoUsuExtRN->getUnidade();      

        if($objUnidadeDTO->getStrSinAtivo()=='S'){
            $arrUnidadeProcesso = $objMdPetIntimacaoRN->verificarUnidadeAberta( array($objProcedimentoDTO, $objUnidadeDTO->getNumIdUnidade()) );
            if (count($arrUnidadeProcesso)==0){
                    $idUnidadeAberta = $objMdPetIntimacaoRN->reabrirUnidade( array($objProcedimentoDTO, $objUnidadeDTO->getNumIdUnidade()) );
                    if (is_numeric($idUnidadeAberta)){
                            $arrUnidadeProcesso = $objMdPetIntimacaoRN->verificarUnidadeAberta( array($objProcedimentoDTO, $idUnidadeAberta) );
                    }
            }
        }

        // 1) ANEXADO, vai pegar do ANEXADOR/PRINCIPAL
        if($objProcedimentoDTO->getStrStaEstadoProtocolo() == ProtocoloRN::$TE_PROCEDIMENTO_ANEXADO){
                $objRelProtocoloProtocoloDTO = new RelProtocoloProtocoloDTO();
                $objRelProtocoloProtocoloDTO->retDblIdProtocolo1();
                $objRelProtocoloProtocoloDTO->retStrProtocoloFormatadoProtocolo1();
                $objRelProtocoloProtocoloDTO->setDblIdProtocolo2($objProcedimentoDTO->getDblIdProcedimento());
                $objRelProtocoloProtocoloDTO->setStrStaAssociacao(RelProtocoloProtocoloRN::$TA_PROCEDIMENTO_ANEXADO);

                $objRelProtocoloProtocoloRN = new RelProtocoloProtocoloRN();
                $objRelProtocoloProtocoloDTO = $objRelProtocoloProtocoloRN->consultarRN0841($objRelProtocoloProtocoloDTO);

                if ( count($objRelProtocoloProtocoloDTO)==1 ){
                        $arrUnidadeProcesso = $this->retornaUltimaUnidadeProcessoAberto( array($objRelProtocoloProtocoloDTO->getDblIdProtocolo1()) );
                }
                // 2) Última aberta
        }else if (count($arrUnidadeProcesso)==0){
                $arrUnidadeProcesso = $this->retornaUltimaUnidadeProcessoAberto( array($this->getProcedimentoDTO()->getDblIdProcedimento()) );
        }

        $idUnidadeProcesso = null;
        $idUsuarioAtribuicao = null;
        if ( count($arrUnidadeProcesso)>0 ){
                if( is_numeric($arrUnidadeProcesso[0]) ){
                        $idUnidadeProcesso = $arrUnidadeProcesso[0];
                        if( is_numeric($arrUnidadeProcesso[1]) ){
                                $idUsuarioAtribuicao = $arrUnidadeProcesso[1];
                        }
                }else{
                        $idUnidadeProcesso = $arrUnidadeProcesso[0]->getNumIdUnidade();
                        if ( $arrUnidadeProcesso[0]->isSetNumIdUsuarioAtribuicao() ){
                                $idUsuarioAtribuicao = $arrUnidadeProcesso[0]->getNumIdUsuarioAtribuicao();
                        }
                }
        }

        if( !is_numeric($idUnidadeProcesso) ){
                $mdPetAndamentoSigilosoRN = new MdPetIntercorrenteAndamentoSigilosoRN();
                $idUnidadeProcesso = $mdPetAndamentoSigilosoRN->retornaIdUnidadeAberturaProcesso( $this->getProcedimentoDTO()->getDblIdProcedimento() );
        }

        $arrObjAtributoAndamentoDTO = array();
        $objAtributoAndamentoDTO = new AtributoAndamentoDTO();
        $objAtributoAndamentoDTO->setStrNome('UNIDADE');
        $objAtributoAndamentoDTO->setStrValor($objUnidadeDTO->getStrSigla().' ¥ '.$objUnidadeDTO->getStrDescricao());
        $objAtributoAndamentoDTO->setStrIdOrigem($objUnidadeDTO->getNumIdUnidade());
        $arrObjAtributoAndamentoDTO[] = $objAtributoAndamentoDTO;

        $objAtividadeDTO = new AtividadeDTO();
        $objAtividadeDTO->setDblIdProtocolo( $objProcedimentoDTO->getDblIdProcedimento() );
        $objAtividadeDTO->setNumIdUnidade( $objUnidadeDTO->getNumIdUnidade() );
        $objAtividadeDTO->setNumIdUnidadeOrigem( $objUnidadeDTO->getNumIdUnidade() );
        if ( !empty($idUsuarioAtribuicao) ){
                $objAtividadeDTO->setNumIdUsuarioAtribuicao( $idUsuarioAtribuicao );
        }
        $objAtividadeDTO->setArrObjAtributoAndamentoDTO($arrObjAtributoAndamentoDTO);
        $objAtividadeDTO->setNumIdTarefa(TarefaRN::$TI_PROCESSO_REMETIDO_UNIDADE);
        
        // Email
        $objMdPetIntEmailNotificacaoRN = new MdPetIntEmailNotificacaoRN();

        if ($operacao==MdPetVincRepresentantRN::$RP_ATIVO){
            $objMdPetIntEmailNotificacaoRN->enviarEmailVincRestabelecimento($params);
        }else if ($operacao==MdPetVincRepresentantRN::$RP_SUSPENSO){
            $objMdPetIntEmailNotificacaoRN->enviarEmailVincSuspensao($params);
        }
        $objAtividadeRN = new AtividadeRN();
        $objAtividadeRN->gerarInternaRN0727($objAtividadeDTO);
    }

  }catch(Exception $e){
        PaginaSEI::getInstance()->processarExcecao($e, true);
  }
  }
  
  public function getIdRepresentantesVinculoConectado($arrParam){
      
    $idVinculo = array_key_exists(0, $arrParam) ? $arrParam[0] : null;
    $ativo     = array_key_exists(1, $arrParam) ? $arrParam[1] : true; //true=Ativo false=Ativo e Inativo
    $estado    = array_key_exists(2, $arrParam) ? $arrParam[2] : true; //true=Ativo false=Suspenso

    $idRepresentantes = null;
    $objMdPetVincRepresentantDTO = new MdPetVincRepresentantDTO();
    $objMdPetVincRepresentantDTO->retNumIdMdPetVinculoRepresent();
    $objMdPetVincRepresentantDTO->setNumIdMdPetVinculo($idVinculo);

    if ($ativo==false){
      $objMdPetVincRepresentantDTO->setBolExclusaoLogica(false);
    }else if (is_array($ativo)){
      $objMdPetVincRepresentantDTO->setStrSinAtivo($ativo, InfraDTO::$FLAG_IN);
      $objMdPetVincRepresentantDTO->setBolExclusaoLogica(false);
    }

    if ($estado==false){
        $objMdPetVincRepresentantDTO->setStrStaEstado(MdPetVincRepresentantRN::$RP_SUSPENSO);
    }else{
        $estado = array(
        	MdPetVincRepresentantRN::$RP_ATIVO,
        	MdPetVincRepresentantRN::$RP_REVOGADA,
        	MdPetVincRepresentantRN::$RP_RENUNCIADA
        );

        $objMdPetVincRepresentantDTO->adicionarCriterio(
            array('StaEstado'),array(InfraDTO::$OPER_IN),array($estado)
        );            
    }

//    if ($estado==true){
//      $objMdPetVincRepresentantDTO->setStrStaEstado(MdPetVincRepresentantRN::$RP_ATIVO);
//    }else if ($estado==false){
//      $objMdPetVincRepresentantDTO->setStrStaEstado(MdPetVincRepresentantRN::$RP_SUSPENSO);
//    }else if (is_array($estado)){
//      $objMdPetVincRepresentantDTO->setStrStaEstado($estado, InfraDTO::$FLAG_IN);
//    }
    
    $arrObj = $this->listar($objMdPetVincRepresentantDTO);

    if(count($arrObj)>0){
      $idRepresentantes = InfraArray::converterArrInfraDTO($arrObj, 'IdMdPetVinculoRepresent');
    }

    return $idRepresentantes;
  }

  protected function getIdContatoTodosRepresentantesVinculoConectado($arrParam){
    $idVinculo = array_key_exists(0, $arrParam) ? $arrParam[0] : null;
    $ativos    = array_key_exists(1, $arrParam) ? $arrParam[1] : true;
    
    $idsContatos = null;
    $objMdPetVincRepresentantDTO = new MdPetVincRepresentantDTO();
    $objMdPetVincRepresentantDTO->retNumIdContato();
    $objMdPetVincRepresentantDTO->setNumIdMdPetVinculo($idVinculo);
    
    if($ativos) {
      $objMdPetVincRepresentantDTO->setStrSinAtivo('S');
    }else{
      $objMdPetVincRepresentantDTO->setStrSinAtivo(array('S', 'N'), InfraDTO::$FLAG_IN);
      $objMdPetVincRepresentantDTO->setBolExclusaoLogica(false);
    }
    
    $count  = $this->contar($objMdPetVincRepresentantDTO);

    if($count > 0) 
    {
      $arrObj          = $this->listar($objMdPetVincRepresentantDTO);
      $idsContatos     = InfraArray::converterArrInfraDTO($arrObj, 'IdContato');
    }
    
    return $idsContatos;
  }

  public function getResponsavelLegalConectado($arrParam){

    $idVinculo = array_key_exists('idVinculo', $arrParam) ? $arrParam['idVinculo'] : null;

    $objMdPetVincRepresentantRN = new MdPetVincRepresentantRN();
    $objMdPetVincRepresentantDTO = new MdPetVincRepresentantDTO();

    $objMdPetVincRepresentantDTO->retTodos(true);
    $objMdPetVincRepresentantDTO->setNumIdMdPetVinculo($idVinculo);
    $objMdPetVincRepresentantDTO->setStrTipoRepresentante(MdPetVincRepresentantRN::$PE_RESPONSAVEL_LEGAL);
    $objMdPetVincRepresentantDTO->setStrSinAtivo('S');
    $objMdPetVincRepresentantDTO->setOrd('IdMdPetVinculoRepresent', InfraDTO::$TIPO_ORDENACAO_DESC);
    $objMdPetVincRepresentantDTO->setNumMaxRegistrosRetorno(1);

    $objMdPetVincRepresentantDTO = $objMdPetVincRepresentantRN->consultar($objMdPetVincRepresentantDTO);

    return $objMdPetVincRepresentantDTO;

  }


}
