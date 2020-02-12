<?
/**
 * TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
 *
 * 14/03/2017 - criado por pedro.cast
 *
 * Versão do Gerador de Código: 1.40.0
 */

require_once dirname(__FILE__).'/../../../SEI.php';

class MdPetIntRelDestinatarioRN extends InfraRN {

    public static $SIM_ANEXO = 'Sim';
    public static $NAO_ANEXO = 'Não';
    
    public static $PESSOA_JURIDICA = 'S';
    public static $PESSOA_FISICA = 'N';

    public function __construct(){
        parent::__construct();
    }

    protected function inicializarObjInfraIBanco(){
        return BancoSEI::getInstance();
    }

    private function validarStrSinAtivo(MdPetIntRelDestinatarioDTO $objMdPetIntRelDestinatarioDTO, InfraException $objInfraException){
        if (InfraString::isBolVazia($objMdPetIntRelDestinatarioDTO->getStrSinAtivo())){
            $objInfraException->adicionarValidacao('Sinalizador de Exclusão Lógica não informado.');
        }else{
            if (!InfraUtil::isBolSinalizadorValido($objMdPetIntRelDestinatarioDTO->getStrSinAtivo())){
                $objInfraException->adicionarValidacao('Sinalizador de Exclusão Lógica inválido.');
            }
        }
    }

    private function validarStrSinPessoaJuridica(MdPetIntRelDestinatarioDTO $objMdPetIntRelDestinatarioDTO, InfraException $objInfraException){
        if (InfraString::isBolVazia($objMdPetIntRelDestinatarioDTO->getStrSinPessoaJuridica())){
            $objInfraException->adicionarValidacao('Sinalizador de Pessoa Juridica não informado.');
        }else{
            if (!InfraUtil::isBolSinalizadorValido($objMdPetIntRelDestinatarioDTO->getStrSinPessoaJuridica())){
                $objInfraException->adicionarValidacao('Sinalizador de Pessoa Juridica inválida.');
            }
        }
    }

    private function validarNumIdMdPetIntimacao(MdPetIntRelDestinatarioDTO $objMdPetIntRelDestinatarioDTO, InfraException $objInfraException){
        if (InfraString::isBolVazia($objMdPetIntRelDestinatarioDTO->getNumIdMdPetIntimacao())){
            $objInfraException->adicionarValidacao('Intimação não informada.');
        }
    }

    private function validarNumIdContato(MdPetIntRelDestinatarioDTO $objMdPetIntRelDestinatarioDTO, InfraException $objInfraException){
        if (InfraString::isBolVazia($objMdPetIntRelDestinatarioDTO->getNumIdContato())){
            $objInfraException->adicionarValidacao('Contato não informado.');
        }
    }

    protected function cadastrarControlado(MdPetIntRelDestinatarioDTO $objMdPetIntRelDestinatarioDTO) {
        try{

            //Valida Permissao
            SessaoSEI::getInstance()->validarPermissao('md_pet_int_rel_destinatario_cadastrar');

            //Regras de Negocio
            $objInfraException = new InfraException();

            $this->validarStrSinAtivo($objMdPetIntRelDestinatarioDTO, $objInfraException);
            $this->validarStrSinPessoaJuridica($objMdPetIntRelDestinatarioDTO, $objInfraException);
            $this->validarNumIdMdPetIntimacao($objMdPetIntRelDestinatarioDTO, $objInfraException);
            $this->validarNumIdContato($objMdPetIntRelDestinatarioDTO, $objInfraException);

            $objInfraException->lancarValidacoes();

            $objMdPetIntRelDestinatarioBD = new MdPetIntRelDestinatarioBD($this->getObjInfraIBanco());
            $ret = $objMdPetIntRelDestinatarioBD->cadastrar($objMdPetIntRelDestinatarioDTO);

            //Auditoria

            return $ret;

        }catch(Exception $e){
            throw new InfraException('Erro cadastrando Destinatário Intimação.',$e);
        }
    }

    protected function alterarControlado(MdPetIntRelDestinatarioDTO $objMdPetIntRelDestinatarioDTO){
        try {

            //Valida Permissao
            SessaoSEI::getInstance()->validarPermissao('md_pet_int_rel_destinatario_alterar');

            //Regras de Negocio
            $objInfraException = new InfraException();

            if ($objMdPetIntRelDestinatarioDTO->isSetStrSinAtivo()){
                $this->validarStrSinAtivo($objMdPetIntRelDestinatarioDTO, $objInfraException);
            }
            if ($objMdPetIntRelDestinatarioDTO->isSetStrSinPessoaJuridica()){
                $this->validarStrSinPessoaJuridica($objMdPetIntRelDestinatarioDTO, $objInfraException);
            }
            if ($objMdPetIntRelDestinatarioDTO->isSetNumIdMdPetIntimacao()){
                $this->validarNumIdMdPetIntimacao($objMdPetIntRelDestinatarioDTO, $objInfraException);
            }
            if ($objMdPetIntRelDestinatarioDTO->isSetNumIdContato()){
                $this->validarNumIdContato($objMdPetIntRelDestinatarioDTO, $objInfraException);
            }

            $objInfraException->lancarValidacoes();

            $objMdPetIntRelDestinatarioBD = new MdPetIntRelDestinatarioBD($this->getObjInfraIBanco());
            $objMdPetIntRelDestinatarioBD->alterar($objMdPetIntRelDestinatarioDTO);

            //Auditoria

        }catch(Exception $e){
            throw new InfraException('Erro alterando Destinatário Intimação.',$e);
        }
    }

    protected function excluirControlado($arrObjMdPetIntRelDestinatarioDTO){
        try {

            //Valida Permissao
            SessaoSEI::getInstance()->validarPermissao('md_pet_int_rel_destinatario_excluir');

            //Regras de Negocio
            //$objInfraException = new InfraException();

            //$objInfraException->lancarValidacoes();

            $objMdPetIntRelDestinatarioBD = new MdPetIntRelDestinatarioBD($this->getObjInfraIBanco());
            for($i=0;$i<count($arrObjMdPetIntRelDestinatarioDTO);$i++){
                $objMdPetIntRelDestinatarioBD->excluir($arrObjMdPetIntRelDestinatarioDTO[$i]);
            }

            //Auditoria

        }catch(Exception $e){
            throw new InfraException('Erro excluindo Destinatário Intimação.',$e);
        }
    }

    protected function consultarConectado(MdPetIntRelDestinatarioDTO $objMdPetIntRelDestinatarioDTO){
        try {

            //Valida Permissao
            SessaoSEI::getInstance()->validarPermissao('md_pet_int_rel_destinatario_consultar');

            //Regras de Negocio
            //$objInfraException = new InfraException();

            //$objInfraException->lancarValidacoes();

            $objMdPetIntRelDestinatarioBD = new MdPetIntRelDestinatarioBD($this->getObjInfraIBanco());
            $ret = $objMdPetIntRelDestinatarioBD->consultar($objMdPetIntRelDestinatarioDTO);
            //Auditoria

            return $ret;
        }catch(Exception $e){
            throw new InfraException('Erro consultando Destinatário Intimação.',$e);
        }
    }

    protected function listarConectado(MdPetIntRelDestinatarioDTO $objMdPetIntRelDestinatarioDTO) {
        try {

            //Valida Permissao
            SessaoSEI::getInstance()->validarPermissao('md_pet_int_rel_destinatario_listar');

            //Regras de Negocio
            //$objInfraException = new InfraException();

            //$objInfraException->lancarValidacoes();

            $objMdPetIntRelDestinatarioBD = new MdPetIntRelDestinatarioBD($this->getObjInfraIBanco());
           $ret = $objMdPetIntRelDestinatarioBD->listar($objMdPetIntRelDestinatarioDTO);

            return $ret;

        }catch(Exception $e){
            throw new InfraException('Erro listando Destinatários das Intimações.',$e);
        }
    }

    protected function contarConectado(MdPetIntRelDestinatarioDTO $objMdPetIntRelDestinatarioDTO){
        try {

            //Valida Permissao
            SessaoSEI::getInstance()->validarPermissao('md_pet_int_rel_destinatario_listar');

            //Regras de Negocio
            //$objInfraException = new InfraException();

            //$objInfraException->lancarValidacoes();

            $objMdPetIntRelDestinatarioBD = new MdPetIntRelDestinatarioBD($this->getObjInfraIBanco());
            $ret = $objMdPetIntRelDestinatarioBD->contar($objMdPetIntRelDestinatarioDTO);

            //Auditoria

            return $ret;
        }catch(Exception $e){
            throw new InfraException('Erro contando Destinatários das Intimações.',$e);
        }
    }

    protected function desativarControlado($arrObjMdPetIntRelDestinatarioDTO){
        try {

            //Valida Permissao
            SessaoSEI::getInstance()->validarPermissao('md_pet_int_rel_destinatario_desativar');

            //Regras de Negocio
            //$objInfraException = new InfraException();

            //$objInfraException->lancarValidacoes();

            $objMdPetIntRelDestinatarioBD = new MdPetIntRelDestinatarioBD($this->getObjInfraIBanco());
            for($i=0;$i<count($arrObjMdPetIntRelDestinatarioDTO);$i++){
                $objMdPetIntRelDestinatarioBD->desativar($arrObjMdPetIntRelDestinatarioDTO[$i]);
            }

            //Auditoria

        }catch(Exception $e){
            throw new InfraException('Erro desativando Destinatário Intimação.',$e);
        }
    }

    protected function reativarControlado($arrObjMdPetIntRelDestinatarioDTO){
        try {

            //Valida Permissao
            SessaoSEI::getInstance()->validarPermissao('md_pet_int_rel_destinatario_reativar');

            //Regras de Negocio
            //$objInfraException = new InfraException();

            //$objInfraException->lancarValidacoes();

            $objMdPetIntRelDestinatarioBD = new MdPetIntRelDestinatarioBD($this->getObjInfraIBanco());
            for($i=0;$i<count($arrObjMdPetIntRelDestinatarioDTO);$i++){
                $objMdPetIntRelDestinatarioBD->reativar($arrObjMdPetIntRelDestinatarioDTO[$i]);
            }

            //Auditoria

        }catch(Exception $e){
            throw new InfraException('Erro reativando Destinatário Intimação.',$e);
        }
    }

    protected function bloquearControlado(MdPetIntRelDestinatarioDTO $objMdPetIntRelDestinatarioDTO){
        try {

            //Valida Permissao
            SessaoSEI::getInstance()->validarPermissao('md_pet_int_rel_destinatario_consultar');

            //Regras de Negocio
            //$objInfraException = new InfraException();

            //$objInfraException->lancarValidacoes();

            $objMdPetIntRelDestinatarioBD = new MdPetIntRelDestinatarioBD($this->getObjInfraIBanco());
            $ret = $objMdPetIntRelDestinatarioBD->bloquear($objMdPetIntRelDestinatarioDTO);

            //Auditoria

            return $ret;
        }catch(Exception $e){
            throw new InfraException('Erro bloqueando Destinatário Intimação.',$e);
        }
    }

    protected function retornaRelIntDestinatarioConectado($arr){

        $objMdPetIntAceiteRN = new MdPetIntAceiteRN();

        $objContato     = false;
        $idIntimacao    = $arr[0];
        $idUsuario      = isset($arr[1]) && $arr[1] ? $arr[1] : false;
        $idContatoInt   = isset($arr[3]) && $arr[3] ? $arr[3] : false;


        if($idUsuario){
            $objContato     = $objMdPetIntAceiteRN->retornaObjContatoIdUsuario(array($idUsuario));
        }else{
            $objContato    = isset($arr[2]) ?  $arr[2] : false;
        }

        if($objContato || $idContatoInt){
        $idContato = $idContatoInt ? $idContatoInt : $objContato->getNumIdContato();
        }

        if($idContato && $idIntimacao)
        {
            $objMdPetIntRelDestinatarioDTO = new MdPetIntRelDestinatarioDTO();
            $objMdPetIntRelDestinatarioDTO->setNumIdMdPetIntimacao($idIntimacao);
            $objMdPetIntRelDestinatarioDTO->setNumIdContatoParticipante($idContato);
            $objMdPetIntRelDestinatarioDTO->retNumIdMdPetIntRelDestinatario();
            $objMdPetIntRelDestinatarioDTO->retStrSinPessoaJuridica();
            $objMdPetIntRelDestinatarioDTO->retStrNomeContato();
            $count = $this->contarConectado($objMdPetIntRelDestinatarioDTO);

            if($count > 0){
                $ret = $this->listarConectado($objMdPetIntRelDestinatarioDTO);
                return current($ret);
            }
            
        }
        
        return null;
    }
    
    protected function retornaTodosObjRelDestPorIntimacaoConectado($arr)
    {
        $idIntimacao = current($arr);
        $objMdPetIntRelDestinatarioDTO = new MdPetIntRelDestinatarioDTO();
        $objMdPetIntRelDestinatarioDTO->setNumIdMdPetIntimacao($idIntimacao);
        $objMdPetIntRelDestinatarioDTO->retNumIdMdPetIntRelDestinatario();
        $objMdPetIntRelDestinatarioDTO->retNumIdContato();
        $objMdPetIntRelDestinatarioDTO->retDthDataCadastro();
        $count = $this->contar($objMdPetIntRelDestinatarioDTO);

        if ($count > 0) {
            $ret = $this->listar($objMdPetIntRelDestinatarioDTO);
            return $ret;
        }

        return null;
    }

    protected function listarDadosComboIntimacaoExternoConectado(){
        $objMdPetRelDestDTO = new MdPetIntRelDestinatarioDTO();

        $objMdPetRelDestDTO->retNumIdMdPetIntimacao();
        $objMdPetRelDestDTO->retNumIdMdPetTipoIntimacao();
        $objMdPetRelDestDTO->retStrNomeTipoIntimacao();
        $objMdPetRelDestDTO->retStrSinPrincipalDoc();
        $objMdPetRelDestDTO->retDblIdDocumento();
        $objMdPetRelDestDTO->retDblIdProtocolo();

        $this->_setFiltroPadraoListaExterna($objMdPetRelDestDTO);

        $count = $this->contarConectado($objMdPetRelDestDTO);
        
        if($count > 0){
            $arrObjMdPetRelDestDTO = $this->listarConectado($objMdPetRelDestDTO);
            $arrIntimacaoDTO = InfraArray::distinctArrInfraDTO($arrObjMdPetRelDestDTO, 'IdMdPetTipoIntimacao');
            $arr = InfraArray::converterArrInfraDTO($arrIntimacaoDTO, 'NomeTipoIntimacao', 'IdMdPetTipoIntimacao');
        }
        
        return $arr;
    }

    protected function listarDadosUsuInternoConectado($idProcedimento)
    {  
        //Busca os dados gerais
        $objMdPetRelDestDTO = new MdPetIntRelDestinatarioDTO();

        $this->_addSelectsListaIntimacao($objMdPetRelDestDTO);

        $objMdPetRelDestDTO->retStrNomeContato();
        $objMdPetRelDestDTO->retNumIdContato();
        $objMdPetRelDestDTO->retStrStaSituacaoIntimacao();
        $objMdPetRelDestDTO->retStrSinPessoaJuridica();
        $objMdPetRelDestDTO->setDblIdProcedimento($idProcedimento);
        $objMdPetRelDestDTO->setStrSinPrincipalDoc('S');
        $objMdPetRelDestDTO->setProcedimentoDocTIPOFK(InfraDTO::$TIPO_FK_OBRIGATORIA);

        $arrObjDTORetorno = $this->listar($objMdPetRelDestDTO);

        //Cria um array de Ids do Rel destinatário que possuem anexos.
        $arrDadosAnexo = $this->_retornaIdsRelIntDestPossuiAnexo($idProcedimento);

        $arrRetorno = array($arrObjDTORetorno, $objMdPetRelDestDTO, $arrDadosAnexo);

        return $arrRetorno;
    }

    private function _formatarArrPrincipalDadosSituacao(&$dados, $arrSituacao){
        $sit = '';
        if (count($dados) > 0) {
            foreach ($dados as $obj) {

                $sit = !is_null($obj->getStrStaSituacaoIntimacao()) && $obj->getStrStaSituacaoIntimacao() != 0 ? $arrSituacao[$obj->getStrStaSituacaoIntimacao()] : MdPetIntimacaoRN::$STR_SITUACAO_NAO_CADASTRADA;
                $obj->setStrSituacaoIntimacao($sit);
            }
        }
    }

    private function _formatarArrSituacao($arrObj, &$arrSituacao, $sit){
        if (count($arrObj) > 0) {
            foreach ($arrObj as $obj) {
                $id = $obj->getNumIdMdPetIntRelDestinatario();
                $arrSituacao[$id] = isset($arrSituacao[$id]) ? $arrSituacao[$id] : $sit;
            }
        }
    }

    private function _retornaArrSituacaoPorProcesso($arrIds)
    {
        $arrSituacao = array();
        
        //se o array de ids de entrada vier vazio, ja retorna array vazio tambem (evita um caso de exception mais abaixo)
        if( !is_array( $arrIds) || count( $arrIds ) == 0  ){
        	return $arrSituacao;
        }

        $objMdPetIntDestRespRN = new MdPetIntDestRespostaRN();
        $objMdPetIntAceiteRN = new MdPetIntAceiteRN();

        //Preenche as Intimações Respondidas
        $objMdPetIntDestRespDTO = new MdPetIntDestRespostaDTO();
        $objMdPetIntDestRespDTO->setNumIdMdPetIntRelDestinatario($arrIds, InfraDTO::$OPER_IN);
        $objMdPetIntDestRespDTO->retNumIdMdPetIntRelDestinatario();
        $arrObjResposta = $objMdPetIntDestRespRN->listar($objMdPetIntDestRespDTO);
        $this->_formatarArrSituacao($arrObjResposta, $arrSituacao, MdPetIntimacaoRN::$STR_INTIMACAO_RESPONDIDA_ACEITE);

        //Preenche as Intimações Aceitas por Acesso
        $objMdPetIntAceiteDTO = new MdPetIntAceiteDTO();
        $objMdPetIntAceiteDTO->retNumIdMdPetIntRelDestinatario();
        $objMdPetIntAceiteDTO->setNumIdMdPetIntRelDestinatario($arrIds, InfraDTO::$OPER_IN);
        $objMdPetIntAceiteDTO->setStrTipoAceite(MdPetIntimacaoRN::$TP_MANUAL_USUARIO_EXTERNO_ACEITE);
        $arrObjAceiteAcesso = $objMdPetIntAceiteRN->listar($objMdPetIntAceiteDTO);
        $this->_formatarArrSituacao($arrObjAceiteAcesso, $arrSituacao, MdPetIntimacaoRN::$STR_INTIMACAO_CUMPRIDA_POR_ACESSO);

        //Preenche as Intimações Aceitas por Decurso de Prazo
        $objMdPetIntAceiteDTO = new MdPetIntAceiteDTO();
        $objMdPetIntAceiteDTO->retNumIdMdPetIntRelDestinatario();
        $objMdPetIntAceiteDTO->setNumIdMdPetIntRelDestinatario($arrIds, InfraDTO::$OPER_IN);
        $objMdPetIntAceiteDTO->setStrTipoAceite(MdPetIntimacaoRN::$TP_AUTOMATICO_POR_DECURSO_DE_PRAZO);
        $arrObjAceitePrazo = $objMdPetIntAceiteRN->listar($objMdPetIntAceiteDTO);
        $this->_formatarArrSituacao($arrObjAceitePrazo, $arrSituacao, MdPetIntimacaoRN::$STR_INTIMACAO_CUMPRIDA_PRAZO);

        foreach($arrIds as $id){
            if(!isset($arrSituacao[$id])){
                $arrSituacao[$id] =  MdPetIntimacaoRN::$STR_INTIMACAO_PENDENTE_ACEITE;
            }
        }

        return $arrSituacao;
    }


    private function _formatarArrPrincipalDadosAnexo(&$dados, $arrDadosAnexo)
    {
        if (count($dados) > 0) {
            foreach ($dados as $obj) {
                $id = $obj->getNumIdMdPetIntRelDestinatario();
                $anexo = count($arrDadosAnexo) > 0 && in_array($id, $arrDadosAnexo) ? MdPetIntRelDestinatarioRN::$SIM_ANEXO : MdPetIntRelDestinatarioRN::$NAO_ANEXO;

                $obj->setStrAnexos($anexo);
            }
        }
    }


    private function _retornaIdsRelIntDestPossuiAnexo($idProcedimento){
        $arrDados = array();
        $objMdPetRelDestDTO = new MdPetIntRelDestinatarioDTO();

        $objMdPetRelDestDTO->retNumIdMdPetIntimacao();
        $objMdPetRelDestDTO->retNumIdMdPetIntRelDestinatario();
        $objMdPetRelDestDTO->retDblIdProtocolo();
        $objMdPetRelDestDTO->retDblIdProcedimento();
        $objMdPetRelDestDTO->retDblIdDocumento();

        $objMdPetRelDestDTO->setDblIdProcedimento($idProcedimento);
        $objMdPetRelDestDTO->setStrSinPrincipalDoc('N');

        $count    = $this->contarConectado($objMdPetRelDestDTO);

        if ($count > 0) {
            $arrDados = $this->listarConectado($objMdPetRelDestDTO);
            $arrDados = InfraArray::converterArrInfraDTO($arrDados, 'IdMdPetIntRelDestinatario');
        }

        return $arrDados;
    }


    protected function listarDadosUsuExternoConectado($arrParams){


        $contar        = array_key_exists(0, $arrParams) ? $arrParams[0] : false;
        $post          = array_key_exists(1, $arrParams) ? $arrParams[1] : false;
        $objDTO        = array_key_exists(2, $arrParams) ? $arrParams[2] : new MdPetIntRelDestinatarioDTO();

        $objDTO->setProcedimentoDocTIPOFK(InfraDTO::$TIPO_FK_OBRIGATORIA);
        $qtd = $this->contar($objDTO);

        if($contar){
            return $qtd;
        }
        if($qtd > 0){
            $arrObjs = $this->listar($objDTO);
            $arrObjMdPetRelDestDTO =  $this->retornaDtoFormatado($arrObjs);

            return $arrObjMdPetRelDestDTO;
        }else{
            return null;
        }
    }

    private function retornaDtoFormatado($arrObjMdPetRelDestDTO){

        $arrSitIntimacao = MdPetIntRelDestinatarioINT::getArraySituacaoRelatorio();

        foreach ($arrObjMdPetRelDestDTO as $key =>$obtDto){

            //Documento Principal
            $docFormat    = $obtDto->getStrNomeSerie();
            if ($obtDto->getStrNumero()){
                $docFormat .= ' ' . $obtDto->getStrNumero() ;
            }
            $docFormat    .= ' ('.$obtDto->getStrProtocoloFormatadoDocumento().')';

            $obtDto->setStrDocumentoPrincipal($docFormat);

           //Define Situação Intimação
            $idSituacao      = $obtDto->getStrStaSituacaoIntimacao();
            $tipoCumprimento = $obtDto->getStrTipoAceite() == MdPetIntimacaoRN::$TP_MANUAL_USUARIO_EXTERNO_ACEITE ? MdPetIntimacaoRN::$INTIMACAO_CUMPRIDA_POR_ACESSO : MdPetIntimacaoRN::$INTIMACAO_CUMPRIDA_PRAZO;

            $idSituacao      = $obtDto->getStrStaSituacaoIntimacao() == MdPetIntimacaoRN::$INTIMACAO_PRAZO_VENCIDO ? $tipoCumprimento : $idSituacao;
            $strSituacao     = is_null($idSituacao) || $idSituacao == 0 ? MdPetIntimacaoRN::$STR_SITUACAO_NAO_CADASTRADA : $arrSitIntimacao[$idSituacao];
            $strSituacao    = !is_null($idSituacao) ? $arrSitIntimacao[$idSituacao] : '';

            $obtDto->setStrSituacaoIntimacao($strSituacao);
        }
        return $arrObjMdPetRelDestDTO;

    }

    protected function retornaSelectsDtoConectado($arrParams){
        $post          = isset($arrParams[1]) ? $arrParams[1] : false;

        $objMdPetRelDestDTO = new MdPetIntRelDestinatarioDTO();

        $this->_addSelectsListaIntimacao($objMdPetRelDestDTO);
        $this->_setFiltroListaExterna($objMdPetRelDestDTO, $post);

        $objMdPetRelDestDTO->retNumIdAcessoExterno();

        return $objMdPetRelDestDTO;

    }

    private function _setFiltroListaExterna(&$objMdPetRelDestDTO, $post){

        $this->_setFiltroPadraoListaExterna($objMdPetRelDestDTO);

        //Add número de Processo
        if((array_key_exists('txtNumeroProcesso', $post) && $post['txtNumeroProcesso'] != ''))
        {
            $strProtocolo = '%'.trim($post['txtNumeroProcesso']).'%';
            $objMdPetRelDestDTO->setStrProtocoloFormatadoProcedimento($strProtocolo, InfraDTO::$OPER_LIKE);
        }
        
        //Add Tipo de Destinatário
        if((array_key_exists('selTipoDestinatario', $post) && $post['selTipoDestinatario'] != ''))
        {
            $objMdPetRelDestDTO->setStrSinPessoaJuridica($post['selTipoDestinatario']);
        }

        //Add Tipo de Intimação
        if((array_key_exists('selTipoIntimacao', $post) && $post['selTipoIntimacao'] != '0'))
        {
            $objMdPetRelDestDTO->setNumIdMdPetTipoIntimacao(($post['selTipoIntimacao']));
        }

        $bolTxtDtInicio = array_key_exists('txtDataInicio', $post) && $post['txtDataInicio'] != '';
        $bolTxtDtFim    = array_key_exists('txtDataFim', $post) && $post['txtDataFim'] != '';

        //Add Data de Expedição
        if($bolTxtDtInicio && $bolTxtDtFim)
        {
            $dtInicio = $post['txtDataInicio'].' 00:00:00';
            $dtFim    = $post['txtDataFim'].' 23:59:59';
            $objMdPetRelDestDTO->adicionarCriterio(array('DataCadastro','DataCadastro'),
            array(InfraDTO::$OPER_MAIOR_IGUAL,InfraDTO::$OPER_MENOR_IGUAL),
            array($dtInicio,$dtFim),
            InfraDTO::$OPER_LOGICO_AND);
        }

        //Add Situação
        if((array_key_exists('selCumprimentoIntimacao', $post) && $post['selCumprimentoIntimacao'] != ''))
        {
            $idSituacao = $post['selCumprimentoIntimacao'];

            if($idSituacao == MdPetIntimacaoRN::$INTIMACAO_CUMPRIDA_POR_ACESSO || $idSituacao == MdPetIntimacaoRN::$INTIMACAO_CUMPRIDA_PRAZO ){
                $tipoAceiteCorreto = $idSituacao == MdPetIntimacaoRN::$INTIMACAO_CUMPRIDA_POR_ACESSO ? MdPetIntimacaoRN::$TP_MANUAL_USUARIO_EXTERNO_ACEITE : MdPetIntimacaoRN::$TP_AUTOMATICO_POR_DECURSO_DE_PRAZO;
                $objMdPetRelDestDTO->adicionarCriterio(array('StaSituacaoIntimacao','TipoAceite', 'StaSituacaoIntimacao'),
                    array(InfraDTO::$OPER_IGUAL,InfraDTO::$OPER_IGUAL, InfraDTO::$OPER_IGUAL),
                    array(MdPetIntimacaoRN::$INTIMACAO_PRAZO_VENCIDO, $tipoAceiteCorreto, $idSituacao),
                    array(InfraDTO::$OPER_LOGICO_AND, InfraDTO::$OPER_LOGICO_OR));
           }else{
                $objMdPetRelDestDTO->setStrStaSituacaoIntimacao($idSituacao);
            }

        }


    }

    private function _setFiltroPadraoListaExterna(&$objMdPetRelDestDTO){
        $objMdPetIntAceiteRN = new MdPetIntAceiteRN();
        $idUser     = SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno();
        $objContato = $objMdPetIntAceiteRN->retornaObjContatoIdUsuario(array($idUser));

        //Settar Valores estabelecidos pelo Requisito
        $objMdPetRelDestDTO->setStrSinPrincipalDoc('S');
        $objMdPetRelDestDTO->setNumIdContatoParticipante($objContato->getNumIdContato());
    }

    private function _addSelectsListaIntimacao(&$objMdPetRelDestDTO){
        $objMdPetRelDestDTO->retNumIdMdPetIntimacao();
        $objMdPetRelDestDTO->retNumIdMdPetIntRelDestinatario();
        $objMdPetRelDestDTO->retDblIdDocumento();
        $objMdPetRelDestDTO->retNumIdSerie();
        $objMdPetRelDestDTO->retStrProtocoloFormatadoProcedimento();
        $objMdPetRelDestDTO->retDthDataCadastro();
        $objMdPetRelDestDTO->retStrProtocoloFormatadoDocumento();
        $objMdPetRelDestDTO->retNumIdMdPetTipoIntimacao();
        $objMdPetRelDestDTO->retStrNomeSerie();
        $objMdPetRelDestDTO->retStrNumero();
        $objMdPetRelDestDTO->retStrSinAtivo();
        $objMdPetRelDestDTO->retNumIdTipoProcedimento();
        $objMdPetRelDestDTO->retStrNomeTipoProcedimento();
        $objMdPetRelDestDTO->retStrStaSituacaoIntimacao();
        $objMdPetRelDestDTO->retStrTipoAceite();

//Add
        $objMdPetRelDestDTO->retDblIdProtocolo();
        $objMdPetRelDestDTO->retDblIdProtocoloProcedimento();
        $objMdPetRelDestDTO->retDblIdProcedimento();
        $objMdPetRelDestDTO->retDblIdDocumento();
        $objMdPetRelDestDTO->retNumIdSerie();
        $objMdPetRelDestDTO->retStrNomeTipoIntimacao();
        $objMdPetRelDestDTO->retStrSinPrincipalDoc();
        $objMdPetRelDestDTO->retNumIdMdPetIntimacaoMdPetIntimacao();
        $objMdPetRelDestDTO->retStrEspecificacaoProcedimento();

        $objMdPetRelDestDTO->setAceiteTIPOFK(InfraDTO::$TIPO_FK_OPCIONAL);
    }

    private function _filtrarPorSituacaoIntimacaoListaExterna(&$arrDados, $arrSituacaoInt, $post){
      $idSituacao = array_key_exists('selCumprimentoIntimacao', $post) && $post['selCumprimentoIntimacao'] != '' ? $post['selCumprimentoIntimacao'] : false;

        if($idSituacao)
        {
            foreach($arrDados as $key=> $objDTO)
            {
              $idIntimacao =  $objDTO->getNumIdMdPetIntimacaoMdPetIntimacao();
              $sitInt = array_key_exists($idIntimacao, $arrSituacaoInt) ? $arrSituacaoInt[$idIntimacao] : false;

                if($sitInt && $sitInt != $idSituacao){
                    unset($arrDados[$key]);
                }
            }
        }

        $arrDados = $this->_formatarArrDadosSituacao($arrDados);
    }

    private function _formatarArrDadosSituacao($arrDados){
        $int        = 0;
        $arrRetorno = array();
        foreach($arrDados as $dado){
            $arrRetorno[$int] = $dado;
            $int++;
        }

        return $arrRetorno;
    }

    private function _retornarArraySituacaoIntimacao($objs){
        $objMdPetIntAceiteRN = new MdPetIntAceiteRN();
        $arrSitIntimacao = array();

        foreach($objs as $key=> $obj){

            $idIntimacao = $obj->getNumIdMdPetIntimacaoMdPetIntimacao();
            $idSituacao  = $objMdPetIntAceiteRN->retornaSituacaoIntimacao(array($idIntimacao));
            $arrSitIntimacao[$idIntimacao] = $idSituacao;
        }

        return $arrSitIntimacao;
    }

    protected function retornaIdAcessoExternoControlado($arr){
        $dlbIdProcedimento = count($arr) > 0 ? current($arr) : null;

        if(!is_null($dlbIdProcedimento)){
            $objAcessoExternoDTO = new AcessoExternoDTO();
            $objAcessoExternoRN  = new AcessoExternoRN();
            $objMdPetIntAceiteRN = new MdPetIntAceiteRN();
            
            $objContato = $objMdPetIntAceiteRN->retornaObjContatoIdUsuario(array(SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno()));
            $idContato  = $objContato->getNumIdContato();

            $objAcessoExternoDTO = new AcessoExternoDTO();
            $objAcessoExternoDTO->retNumIdAcessoExterno();
            $objAcessoExternoDTO->setDblIdProtocoloAtividade($dlbIdProcedimento);
            $objAcessoExternoDTO->setNumIdContatoParticipante($idContato);

            $objAcessoExternoRN = new AcessoExternoRN();
            $arrObjAcessoExternoDTO = $objAcessoExternoRN->listar($objAcessoExternoDTO);
            if($arrObjAcessoExternoDTO && count($arrObjAcessoExternoDTO)> 0){
                $objAcessoExternoDTO = current($arrObjAcessoExternoDTO);
                return $objAcessoExternoDTO->getNumIdAcessoExterno();
            }
        }

        return null;
    }

    public function addConsultarProcesso($idProcesso, $tpProcesso, $idAcessoExt, $descricao, $texto = ''){

        //verificar se o referido acesso externo ainda é valido (apesar de ser raro, usuarios internos podem em tese cancelar o acesso externo gerado no ato da intimação)
        $objAcessoExternoDTO = new AcessoExternoDTO();
        $objAcessoExternoDTO->retNumIdAcessoExterno();
        $objAcessoExternoDTO->setStrSinAtivo( 'S' );
        $objAcessoExternoDTO->setNumIdAcessoExterno( $idAcessoExt );
        
        $objAcessoExternoRN = new AcessoExternoRN();
        $total = $objAcessoExternoRN->contar($objAcessoExternoDTO);
        
        if( $total > 0 ) {
          $strLinkProcesso = SessaoSEIExterna::getInstance()->assinarLink('processo_acesso_externo_consulta.php?id_acesso_externo=' . $idAcessoExt.'&id_procedimento='.$idProcesso);
        } else {
            $strLinkProcesso = "#";
        } 
        
        $js = 'window.open(\''.$strLinkProcesso.'\');';
        $imgConsulta = '<img src="' . PaginaSEI::getInstance()->getDiretorioImagensGlobal() . '/consultar.gif" class="infraImg" />';
        $textTolTip = $tpProcesso;
        $descricao = '';

        if( $total > 0 ) {
          $conteudoHtml  = '<a onclick="'.$js.'"';
        } else {
          $conteudoHtml  = '<a href="#" ';
        }
        
        $conteudoHtml .= $texto != '' ? ' class="ancoraPadraoAzul" style="font-size: 1.0em;" ' : '';
        $conteudoHtml .= ' onmouseover ="return infraTooltipMostrar(\''.$descricao.'\',\''.$textTolTip.'\')"';
        $conteudoHtml .= 'onmouseout="return infraTooltipOcultar()">';
        $conteudoHtml .= $texto != '' ? $texto : $imgConsulta;
        $conteudoHtml .= '</a>';
         
        return $conteudoHtml;
    }

    
    public function consultarDadosIntimacao($idIntimacao, $returnObj = false, $idContato = false, $objContatoEnv = false){

    	$dtIntimacao = null;
        $objMdPetIntAceiteRN = new MdPetIntAceiteRN();
        $objMdPetContatoRN = new MdPetContatoRN();
        $objMdPetIntDestDTO = new MdPetIntRelDestinatarioDTO();
        if(!$idContato){
            if ($objContatoEnv)
            {
                $objContato = $objContatoEnv;
            }
            else 
            {
               $objContato = $objMdPetIntAceiteRN->retornaObjContatoIdUsuario(array(SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno()));
            }

            $idContato = $objContato->getNumIdContato();
        }
        
        
        $objMdPetIntDestDTO->setNumIdContatoParticipante($idContato);
        if(is_array($idIntimacao)){
            $objMdPetIntDestDTO->setNumIdMdPetIntimacao($idIntimacao, InfraDTO::$OPER_IN);
        }else{
            $objMdPetIntDestDTO->setNumIdMdPetIntimacao($idIntimacao);
        }
        $objMdPetIntDestDTO->setStrSinPrincipalDoc('S');
        $objMdPetIntDestDTO->retNumIdMdPetIntimacao();
        $objMdPetIntDestDTO->retDblIdProtocolo();
        $objMdPetIntDestDTO->retDblIdDocumento();
        $objMdPetIntDestDTO->retDthDataCadastro();
        $objMdPetIntDestDTO->retNumIdMdPetTipoIntimacao();
        $objMdPetIntDestDTO->retStrNomeTipoIntimacao();
        $objMdPetIntDestDTO->retNumIdMdPetIntRelDestinatario();
        $objMdPetIntDestDTO->retStrStaSituacaoIntimacao();
        $objMdPetIntDestDTO->retStrSinPessoaJuridica();
        $retLista = $this->listarConectado($objMdPetIntDestDTO);
        
//            $objMdPetIntRelDestinatarioBD = new MdPetIntRelDestinatarioBD($this->getObjInfraIBanco());
//            $retLista = $objMdPetIntRelDestinatarioBD->listar($objMdPetIntDestDTO, true);
//            echo $retLista;
//        var_dump($retLista);die;
        $objMdPetIntDestDTO = !is_null($retLista) && count($retLista) > 0 ? current($retLista) : null;
        $dtIntimacao        = !is_null($objMdPetIntDestDTO) ? $objMdPetIntDestDTO->getDthDataCadastro() : null;

        if($returnObj){
            return $objMdPetIntDestDTO;
        }

        return $dtIntimacao;
    }

    public function consultarDadosIntimacaoPorDestinario($idIntimacao, $returnObj = false, $idContato = false, $objContatoEnv = false){
        $dtIntimacao = null;
        $objMdPetIntAceiteRN = new MdPetIntAceiteRN();
        $objMdPetContatoRN = new MdPetContatoRN();
        if(!$idContato){
            if ($objContatoEnv)
            {
                $objContato = $objContatoEnv;
            }
            else 
            {
               $objContato = $objMdPetIntAceiteRN->retornaObjContatoIdUsuario(array(SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno()));
            }

           $idContato = $objContato->getNumIdContato();
        }

        $objMdPetIntDestDTO = new MdPetIntRelDestinatarioDTO();
        if(is_array($idIntimacao)){
            $objMdPetIntDestDTO->setNumIdMdPetIntimacao($idIntimacao, InfraDTO::$OPER_IN);
        }else{
            $objMdPetIntDestDTO->setNumIdMdPetIntimacao($idIntimacao);
        }
        $objMdPetIntDestDTO->setNumIdContato($idContato);
        $objMdPetIntDestDTO->setStrSinPrincipalDoc('S');
        $objMdPetIntDestDTO->retNumIdMdPetIntimacao();
        $objMdPetIntDestDTO->retDblIdProtocolo();
        $objMdPetIntDestDTO->retDblIdDocumento();
        $objMdPetIntDestDTO->retDthDataCadastro();
        $objMdPetIntDestDTO->retNumIdMdPetTipoIntimacao();
        $objMdPetIntDestDTO->retStrNomeTipoIntimacao();
        $objMdPetIntDestDTO->retNumIdMdPetIntRelDestinatario();
        $objMdPetIntDestDTO->retStrStaSituacaoIntimacao();
        $objMdPetIntDestDTO->retStrSinPessoaJuridica();
        $retLista = $this->listarConectado($objMdPetIntDestDTO);

        $objMdPetIntDestDTO = !is_null($retLista) && count($retLista) > 0 ? current($retLista) : null;
        $dtIntimacao        = !is_null($objMdPetIntDestDTO) ? $objMdPetIntDestDTO->getDthDataCadastro() : null;

        if($returnObj){
            return $objMdPetIntDestDTO;
        }

        return $dtIntimacao;
    }

    protected function getObjIntimacaoPorIdPetIntRelDestConectado($idMdPetRelDest){
        $objMdPetIntDestDTO = new MdPetIntRelDestinatarioDTO();
        $objMdPetIntDestDTO->setNumIdMdPetIntRelDestinatario($idMdPetRelDest);
        $objMdPetIntDestDTO->setNumMaxRegistrosRetorno(1);
        $objMdPetIntDestDTO->retNumIdMdPetIntimacao();
        $objMdPetIntDestDTO->retDthDataCadastro();

        $objMdPetIntDestDTO = $this->consultar($objMdPetIntDestDTO);
        
        return $objMdPetIntDestDTO;
    }

    protected function atualizarStatusIntimacaoControlado($arrParams)
    {
        $novoStatus = array_key_exists(0, $arrParams) ? $arrParams[0] : false;
        $idRelDest  = array_key_exists(1, $arrParams) ? $arrParams[1] : false;

        if($novoStatus && $idRelDest){
            $objMdPetIntRelDestDTO = new MdPetIntRelDestinatarioDTO();
            $objMdPetIntRelDestDTO->setStrStaSituacaoIntimacao($novoStatus);
            $objMdPetIntRelDestDTO->setNumIdMdPetIntRelDestinatario($idRelDest);

            $this->alterar($objMdPetIntRelDestDTO);
        }
    }

    protected function atualizarStatusIntimacoesConectado($arrParams)
    {
        $novoStatus = array_key_exists(0, $arrParams) ? $arrParams[0] : false;
        $idsRelDest  = array_key_exists(1, $arrParams) ? $arrParams[1] : false;

        if($novoStatus && $idsRelDest){
            foreach($idsRelDest as $idRelDest){
                $this->atualizarStatusIntimacao(array($novoStatus, $idRelDest));
            }
        }
    }

    protected function retornaAtualizaIntimacoesSemRespostaVencidasConectado(){
          $dtAtual = InfraData::getStrDataAtual();

        //Obs: Nesta tabela são salvos apenas as intimações que possuem prazo pra resposta
          $sitCumpridas = array(MdPetIntimacaoRN::$INTIMACAO_CUMPRIDA_PRAZO, MdPetIntimacaoRN::$INTIMACAO_CUMPRIDA_POR_ACESSO);
          $objMdPetIntRelTipoRespDestRN  = new MdPetIntRelTipoRespDestRN();
          $objMdPetIntRelTipoRespDestDTO = new MdPetIntRelTipoRespDestDTO();
          $objMdPetIntRelTipoRespDestDTO->setStrStaSituacaoIntimacao($sitCumpridas, InfraDTO::$OPER_IN);
          $objMdPetIntRelTipoRespDestDTO->retDthDataProrrogada();
          $objMdPetIntRelTipoRespDestDTO->retDthDataLimite();
          $objMdPetIntRelTipoRespDestDTO->retNumIdMdPetIntRelDest();
          $objMdPetIntRelTipoRespDestDTO->adicionarCriterio(array('DataLimite', 'DataProrrogada', 'DataProrrogada', 'DataProrrogada'),
                                                            array(InfraDTO::$OPER_MENOR, InfraDTO::$OPER_IGUAL, InfraDTO::$OPER_MENOR, InfraDTO::$OPER_DIFERENTE),
                                                            array($dtAtual, null, $dtAtual, null),
                                                            array(InfraDTO::$OPER_LOGICO_AND, InfraDTO::$OPER_LOGICO_OR, InfraDTO::$OPER_LOGICO_AND));

          $count = $objMdPetIntRelTipoRespDestRN->contar($objMdPetIntRelTipoRespDestDTO);

         if($count > 0)
         {
            $arrObjDTO = $objMdPetIntRelTipoRespDestRN->listar($objMdPetIntRelTipoRespDestDTO);
            foreach ($arrObjDTO as $objDTO)
            {
                   $this->atualizarStatusIntimacao(array(MdPetIntimacaoRN::$INTIMACAO_PRAZO_VENCIDO, $objDTO->getNumIdMdPetIntRelDest())) ;
            }
         }
        
        return $count;
    }

    public function atualizarCadaEstadoIntimacao($arrSituacaoSeparado, $statusAtual){
        if (array_key_exists($statusAtual, $arrSituacaoSeparado)) {
            $idsRelDestPendente = $arrSituacaoSeparado[$statusAtual];
            if(count($idsRelDestPendente)> 0) {
                $this->atualizarStatusIntimacoes(array($statusAtual, $idsRelDestPendente));
            }
        }
    }



    
    



}
?>