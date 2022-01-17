<?
/**
 * @since  08/02/2018
 * @author Jaqueline Mendes <jaqueline.mendes@castgroup.com.br>
 */

require_once dirname(__FILE__).'/../../../SEI.php';

class MdPetIntRelatorioRN extends InfraRN {


    //Graficos
    public static $GRAFICO_BARRA    = '1'; 
    public static $GRAFICO_PIZZA    = '2';
    public static $GRAFICO_RADAR    = '3';
    public static $GRAFICO_AR_POLAR = '4';
    
    public static $STR_GRAFICO_BARRA    = 'Barra';
    public static $STR_GRAFICO_PIZZA    = 'Pizza';
    public static $STR_GRAFICO_RADAR    = 'Radar';
    public static $STR_GRAFICO_AR_POLAR = 'Área Polar';
    
    public static $GRAFICO_TAMANHO_PADRAO = '300px';
    public static $ID_TP_INT_FILTRO_GRAFICO = 'ID_TP_INT_FILTRO_GRAFICO';
    
    
    public function __construct(){
        parent::__construct();
    }

    protected function inicializarObjInfraIBanco(){
        return BancoSEI::getInstance();
    }
    
    public function retornaSelectsRelatorio(){
        $objMdPetIntRelDestDTO = new MdPetIntRelDestinatarioDTO();
        $objMdPetIntRelDestDTO->retNumIdMdPetIntimacao();
        $objMdPetIntRelDestDTO->retNumIdMdPetIntRelDestinatario();
        $objMdPetIntRelDestDTO->retDblIdDocumento();
        $objMdPetIntRelDestDTO->retNumIdSerie();
        $objMdPetIntRelDestDTO->retStrProtocoloFormatadoProcedimento();
        $objMdPetIntRelDestDTO->retDthDataCadastro();
        $objMdPetIntRelDestDTO->retStrProtocoloFormatadoDocumento();
        $objMdPetIntRelDestDTO->retNumIdMdPetTipoIntimacao();
        $objMdPetIntRelDestDTO->retStrNomeSerie();
        $objMdPetIntRelDestDTO->retStrNumero();
        $objMdPetIntRelDestDTO->retStrSinAtivo();
        $objMdPetIntRelDestDTO->retNumIdTipoProcedimento();
        $objMdPetIntRelDestDTO->retStrNomeTipoProcedimento();
        $objMdPetIntRelDestDTO->retDblIdProtocolo();
        $objMdPetIntRelDestDTO->retDblIdProtocoloProcedimento();
        $objMdPetIntRelDestDTO->retDblIdProcedimento();
        $objMdPetIntRelDestDTO->retNumIdSerie();
        $objMdPetIntRelDestDTO->retStrNomeTipoIntimacao();
        $objMdPetIntRelDestDTO->retStrSinPrincipalDoc();
        $objMdPetIntRelDestDTO->retStrSinPessoaJuridica();
        $objMdPetIntRelDestDTO->retNumIdMdPetIntimacaoMdPetIntimacao();
        $objMdPetIntRelDestDTO->retNumIdContato();
        $objMdPetIntRelDestDTO->retStrNomeContato();
        $objMdPetIntRelDestDTO->retDthDataAceite();
        $objMdPetIntRelDestDTO->retNumIdUnidade();
        $objMdPetIntRelDestDTO->retStrSiglaUnidadeIntimacao();
        $objMdPetIntRelDestDTO->retStrDescricaoUnidadeIntimacao();
        $objMdPetIntRelDestDTO->retStrTipoAceite();
        $objMdPetIntRelDestDTO->retStrEspecificacaoProcedimento();
        $objMdPetIntRelDestDTO->retStrEmailContato();
        $objMdPetIntRelDestDTO->retStrStaSituacaoIntimacao();
        $objMdPetIntRelDestDTO->setProcedimentoDocTIPOFK(InfraDTO::$TIPO_FK_OBRIGATORIA);
        $objMdPetIntRelDestDTO->setAceiteTIPOFK(InfraDTO::$TIPO_FK_OPCIONAL);


        return $objMdPetIntRelDestDTO;
    }

    public function retornaSelectsGrafico(){
        $objMdPetIntRelDestDTO = new MdPetIntRelDestinatarioDTO();
        $objMdPetIntRelDestDTO->retStrStaSituacaoIntimacao();
        $objMdPetIntRelDestDTO->setProcedimentoDocTIPOFK(InfraDTO::$TIPO_FK_OBRIGATORIA);
        $objMdPetIntRelDestDTO->setAceiteTIPOFK(InfraDTO::$TIPO_FK_OPCIONAL);

        return $objMdPetIntRelDestDTO;
    }

    private function _addFiltroListagem($objDTO){
        
        //Tipo de Intimação
        $arrTipoIntimacao = PaginaSEI::getInstance()->getArrValuesSelect($_POST['hdnTpIntimacao']);
        if(count($arrTipoIntimacao) > 0) {
            $objDTO->setNumIdMdPetTipoIntimacao($arrTipoIntimacao, InfraDTO::$OPER_IN);
        }

        $arrTpIntSession  = array();
        $tpIntSession     = array_key_exists(static::$ID_TP_INT_FILTRO_GRAFICO, $_SESSION) ? $_SESSION[static::$ID_TP_INT_FILTRO_GRAFICO] : null;
        $arrTpIntSession  = !is_null($tpIntSession) ? array($tpIntSession) : array();

        if(count($arrTpIntSession) > 0) {
            $objDTO->setNumIdMdPetTipoIntimacao($arrTpIntSession, InfraDTO::$OPER_IN);
        }

        //Settando Tipo de Destinatario
        if($_POST['selTipoDest'] != ""){
            $objDTO->setStrSinPessoaJuridica($_POST['selTipoDest']);

        }

        //Unidade
        $arrUnidade = PaginaSEI::getInstance()->getArrValuesSelect($_POST['hdnUnidade']);
        if(count($arrUnidade) > 0) {
            $objDTO->setNumIdUnidade($arrUnidade, InfraDTO::$OPER_IN);
        }

        $bolTxtDtInicio = array_key_exists('txtDataInicio', $_POST) && $_POST['txtDataInicio'] != '';
        $bolTxtDtFim    = array_key_exists('txtDataFim', $_POST) && $_POST['txtDataFim'] != '';

        //Add Data de Expedição
        if($bolTxtDtInicio && $bolTxtDtFim)
        {
            $dtInicio = $_POST['txtDataInicio'].' 00:00:00';
            $dtFim    = $_POST['txtDataFim'].' 23:59:59';
            $objDTO->adicionarCriterio(array('DataCadastro','DataCadastro'),
                array(InfraDTO::$OPER_MAIOR_IGUAL,InfraDTO::$OPER_MENOR_IGUAL),
                array($dtInicio,$dtFim),
                InfraDTO::$OPER_LOGICO_AND);
        }


        $hdnSituacao            = array_key_exists('hdnIdsSituacao', $_POST) ? $_POST['hdnIdsSituacao'] : null;

        if(!is_null($hdnSituacao)){
            $arrSituacaoFiltro = json_decode($hdnSituacao);
        }

        if(count($arrSituacaoFiltro) > 0 && !in_array(MdPetIntimacaoRN::$TODAS,$arrSituacaoFiltro)){
            $objDTO->setStrStaSituacaoIntimacao($arrSituacaoFiltro, InfraDTO::$OPER_IN);
        }

        return $objDTO;
    }
    

    protected function listarDadosConectado($objMdPetIntRelDestDTO){

        $arrRetornoDTO        = array();

        $objMdPetIntRelDestRN  = new MdPetIntRelDestinatarioRN();

        $objMdPetIntRelDestDTO->setStrSinPrincipalDoc('S');
        $objMdPetIntRelDestDTO = $this->_addFiltroListagem($objMdPetIntRelDestDTO);

        $arrObjDTO = $objMdPetIntRelDestRN->listar($objMdPetIntRelDestDTO);

        $objMdPetRegrasGeraisRN = new MdPetRegrasGeraisRN();
        $idsRelDest             = InfraArray::converterArrInfraDTO($arrObjDTO, 'IdMdPetIntRelDestinatario');
        $arrAnexos              = $objMdPetRegrasGeraisRN->retornarArrIntimacaoAnexo($idsRelDest);
        $arrDescricaoSituacao   = MdPetIntRelatorioINT::retornaArraySituacaoRelatorio();

        //Formatar Dados pra exibição
        foreach($arrObjDTO as $objDTO){
            //Doc Principal
            $docFormat = $this->_getDocPrincipalFormatado($objDTO);
            $objDTO->setStrDocumentoPrincipal($docFormat);

            //Data Cadastro da Intimação Formatada
            $dataForm = !is_null($objDTO->getDthDataCadastro()) ? explode(' ', $objDTO->getDthDataCadastro()) : null;
            $dataForm = count($dataForm) > 0 ? $dataForm[0] : '';
            $objDTO->setDthDataCadastro($dataForm);

            //Data Cumprimento da Intimação
            $dataForm = !is_null($objDTO->getDthDataAceite()) ? explode(' ', $objDTO->getDthDataAceite()) : null;
            $dataForm = !is_null($dataForm) > 0 ? $dataForm[0] : '';
            $objDTO->setDthDataAceite($dataForm);

            //Define Situação Intimação
            $idSituacao    = $objDTO->getStrStaSituacaoIntimacao();
            $strSituacao   = !is_null($idSituacao) ? $arrDescricaoSituacao[$idSituacao] : '';
            $objDTO->setStrSituacaoIntimacao($strSituacao);
            $objDTO->setNumIdSituacaoIntimacao($idSituacao);

            //Define Anexo
            $strAnexo = array_key_exists($objDTO->getNumIdMdPetIntRelDestinatario(), $arrAnexos) ? $arrAnexos[$objDTO->getNumIdMdPetIntRelDestinatario()] : null;
            $strAnexo = $strAnexo == 'S' ? 'Sim' : 'Não';
            $objDTO->setStrAnexos($strAnexo);
        }

        return $arrObjDTO;
    }

    protected function listarDadosGraficoConectado($objMdPetIntRelDestDTO){

        $objMdPetIntRelDestRN  = new MdPetIntRelDestinatarioRN();

        $objMdPetIntRelDestDTO->setStrSinPrincipalDoc('S');
        $objMdPetIntRelDestDTO = $this->_addFiltroListagem($objMdPetIntRelDestDTO);

        $arrObjDTO = $objMdPetIntRelDestRN->listar($objMdPetIntRelDestDTO);

        return $arrObjDTO;
    }

    private function _getDocPrincipalFormatado($objDTO){
        //Documento Principal
        $docFormat    = $objDTO->getStrNomeSerie();
        if ($objDTO->getStrNumero()){
            $docFormat .= ' ' . $objDTO->getStrNumero() ;
        }
        $docFormat    .= ' ('.$objDTO->getStrProtocoloFormatadoDocumento().')';

        return $docFormat;
    }

    protected function getQtdDadosPorSituacaoConectado(){
        $objDTO     = $this->retornaSelectsGrafico();
        $arrObjs    = $this->listarDadosGrafico($objDTO);
        $arrDados   = array();
        $arrRetorno = array();

        if($arrObjs){
            foreach($arrObjs as $objDTO){
                if(array_key_exists($objDTO->getStrStaSituacaoIntimacao(), $arrDados)){
                    $arrDados[$objDTO->getStrStaSituacaoIntimacao()]['qtd']++;
                }else {
                    $arrDados[$objDTO->getStrStaSituacaoIntimacao()]['qtd'] = 1;
                }
            }
        }

        $arrDados = $this->_preencherArrSituacao($arrDados);

        if(count($arrDados) > 0) {
            $arrRetorno = $this->_formatarArrSituacaoGrafico($arrDados);
        }

        return $arrRetorno;
    }

    private function _formatarArrSituacaoGrafico($arrDados){
        $arrRetorno  =  array();
        $arrSituacao = MdPetIntRelatorioINT::retornaArraySituacaoRelatorio();

        $contador = 0;
        foreach($arrSituacao as $idSituacao => $situacao){
            if(array_key_exists($idSituacao, $arrDados)) {
                $label = $arrDados[$idSituacao]['nome'];
                $cor = MdPetIntRelatorioINT::retornaArrayCorGrafico($label);

                $arrRetorno[$contador]['valor'] = $arrDados[$idSituacao]['qtd'];
                $arrRetorno[$contador]['cor'] = $cor;
                $arrRetorno[$contador]['label'] = $label;
                $contador++;
            }
        }

       return $arrRetorno;
    }


    private function _preencherArrSituacao($arrDados){
        $arrSituacao = MdPetIntRelatorioINT::retornaArraySituacaoRelatorio();

        foreach ($arrSituacao as $key => $situacao){
                if(array_key_exists($key, $arrDados) && $arrDados[$key]['qtd'] != null){
                    $arrDados[$key]['nome'] = $situacao;
                }else{
                    unset($arrDados[$key]);
                    //$arrDados[$key]['qtd'] = 0;
                }
        }

        return $arrDados;
    }

    protected function listarDadosModalSituacaoConectado($objMdPetIntRelDestDTO){

        $objMdPetRegrasGeraisRN = new MdPetRegrasGeraisRN();
        $objMdPetIntRelDestRN   = new MdPetIntRelDestinatarioRN();
        $objMdPetIntRelDestDTO->setNumMaxRegistrosRetorno(1);
        $objMdPetIntRelDestDTO->setStrSinPrincipalDoc('S');
        $idRelDest = $objMdPetIntRelDestDTO->getNumIdMdPetIntRelDestinatario();

        $objMdPetIntRelDestDTO  = $objMdPetIntRelDestRN->consultar($objMdPetIntRelDestDTO);
        $idSituacao             = $objMdPetIntRelDestDTO->getStrStaSituacaoIntimacao();

        return $this->_formatarDadosModalSituacao($idSituacao, $idRelDest, $objMdPetIntRelDestDTO);

    }


    private function _formatarDadosModalSituacao($idSituacao, $idRelDest, $objMdPetIntRelDestDTO){
        $dados = array();

        switch ($idSituacao){

            case MdPetIntimacaoRN::$INTIMACAO_PENDENTE:
                $dado = $this->_addDadosSituacaoPendente($objMdPetIntRelDestDTO);
                $dados[MdPetIntimacaoRN::$INTIMACAO_PENDENTE] = $dado;
                break;

            case MdPetIntimacaoRN::$INTIMACAO_CUMPRIDA_PRAZO:
            case MdPetIntimacaoRN::$INTIMACAO_CUMPRIDA_POR_ACESSO:
                $dado = $this->_addDadosSituacaoPendente($objMdPetIntRelDestDTO);
                $dados[MdPetIntimacaoRN::$INTIMACAO_PENDENTE] = $dado;

                $dado = $this->_addDadosSituacaoCumprida($objMdPetIntRelDestDTO);
                $dados[$idSituacao] = $dado;
                break;

            case MdPetIntimacaoRN::$INTIMACAO_RESPONDIDA:
                $dado = $this->_addDadosSituacaoPendente($objMdPetIntRelDestDTO);
                $dados[MdPetIntimacaoRN::$INTIMACAO_PENDENTE] = $dado;

                $dado = $this->_addDadosSituacaoCumprida($objMdPetIntRelDestDTO);
                $idTipoCumprimento = array_key_exists('id', $dado) ? $dado['id'] : 0;
                $dados[$idTipoCumprimento] = $dado;

                $dado = $this->_addDadosSituacaoRespondida($objMdPetIntRelDestDTO);
                $dados[MdPetIntimacaoRN::$INTIMACAO_RESPONDIDA] = $dado;
                break;

            case MdPetIntimacaoRN::$INTIMACAO_PRAZO_VENCIDO:
                $dado = $this->_addDadosSituacaoPendente($objMdPetIntRelDestDTO);
                $dados[MdPetIntimacaoRN::$INTIMACAO_PENDENTE] = $dado;

                $dado = $this->_addDadosSituacaoCumprida($objMdPetIntRelDestDTO);
                $idTipoCumprimento = array_key_exists('id', $dado) ? $dado['id'] : 0;
                $dados[$idTipoCumprimento] = $dado;

                $dado = $this->_addDadosSituacaoPrazoVencido($objMdPetIntRelDestDTO);
                $dados[MdPetIntimacaoRN::$INTIMACAO_PRAZO_VENCIDO] = $dado;

                break;
        }

        return $dados;
    }

    private function _addDadosSituacaoPendente($objMdPetIntRelDestDTO){

        $idIntimacao = $objMdPetIntRelDestDTO->getNumIdMdPetIntimacao();
        $nomeUnidade = $this->_formatarNomeUnidade($objMdPetIntRelDestDTO);
        //Data
        $dataForm =  !is_null($objMdPetIntRelDestDTO->getDthDataCadastro()) ?  substr($objMdPetIntRelDestDTO->getDthDataCadastro(), 0, -3) : null;

        $row = array();
        $row['id']       = MdPetIntimacaoRN::$INTIMACAO_PENDENTE;
        $row['data']     = $dataForm;
        $row['usuarioNome']  = $objMdPetIntRelDestDTO->getStrNomeContato();
        $row['usuarioEmail']  = $objMdPetIntRelDestDTO->getStrEmailContato();
        $row['unidadeSigla']  = $objMdPetIntRelDestDTO->getStrSiglaUnidadeIntimacao();
        $row['unidadeDescricao']  = $objMdPetIntRelDestDTO->getStrDescricaoUnidadeIntimacao();
        $row['tipoPessoa']  = $objMdPetIntRelDestDTO->getStrSinPessoaJuridica();
        $row['situacao'] = MdPetIntimacaoRN::$STR_INTIMACAO_PENDENTE_ACEITE;
        $row['tipoResp'] = $this->_getTiposRespostaIntimacao($idIntimacao);;

        return $row;
    }

    private function _addDadosSituacaoCumprida($objMdPetIntRelDestDTO){

        $idSituacao           = $objMdPetIntRelDestDTO->getStrTipoAceite() == MdPetIntimacaoRN::$TP_AUTOMATICO_POR_DECURSO_DE_PRAZO ? MdPetIntimacaoRN::$INTIMACAO_CUMPRIDA_PRAZO : MdPetIntimacaoRN::$INTIMACAO_CUMPRIDA_POR_ACESSO;
        $nomeUnidade          = $this->_formatarNomeUnidade($objMdPetIntRelDestDTO);
        $arrDescricaoSituacao = MdPetIntRelatorioINT::retornaArraySituacaoRelatorio();
        $dataForm             =  !is_null($objMdPetIntRelDestDTO->getDthDataAceite()) ?  substr($objMdPetIntRelDestDTO->getDthDataAceite(), 0, -3) : null;
        $objUsuarioPetRN      = new MdPetIntUsuarioRN();
        $objUsuarioPetDTO     = $objUsuarioPetRN->getObjUsuarioPeticionamento();

        $row = array();
        $row['id']       = $idSituacao;
        $row['data']     = $dataForm;
        $row['usuarioNome']  = $idSituacao == MdPetIntimacaoRN::$INTIMACAO_CUMPRIDA_POR_ACESSO ? $objMdPetIntRelDestDTO->getStrNomeContato() : $objUsuarioPetDTO->getStrNome();
        $row['usuarioEmail'] = $idSituacao == MdPetIntimacaoRN::$INTIMACAO_CUMPRIDA_POR_ACESSO ? $objMdPetIntRelDestDTO->getStrEmailContato() : $objUsuarioPetDTO->getStrSigla();
        $row['unidadeSigla']  = $objMdPetIntRelDestDTO->getStrSiglaUnidadeIntimacao();
        $row['unidadeDescricao']  = $objMdPetIntRelDestDTO->getStrDescricaoUnidadeIntimacao();
        $row['tipoPessoa']  = $objMdPetIntRelDestDTO->getStrSinPessoaJuridica();
        $row['situacao'] = $arrDescricaoSituacao[$idSituacao];
        $row['tipoResp']=  '';

        return $row;
    }

    private function _addDadosSituacaoRespondida($objMdPetIntRelDestDTO){
        $dthData      =  $this->_getDataResposta($objMdPetIntRelDestDTO);
        $dataForm     =  !is_null($dthData) ?  substr($dthData, 0, -3) : null;

        $row = array();
        $row['id']       = MdPetIntimacaoRN::$INTIMACAO_RESPONDIDA;
        $row['data']     = $dataForm;
        $row['usuarioNome']  = $objMdPetIntRelDestDTO->getStrNomeContato();
        $row['usuarioEmail']  = $objMdPetIntRelDestDTO->getStrEmailContato();
        $row['unidadeSigla']  = $objMdPetIntRelDestDTO->getStrSiglaUnidadeIntimacao();
        $row['unidadeDescricao']  = $objMdPetIntRelDestDTO->getStrDescricaoUnidadeIntimacao();
        $row['tipoPessoa']  = $objMdPetIntRelDestDTO->getStrSinPessoaJuridica();
        $row['situacao'] = MdPetIntimacaoRN::$STR_INTIMACAO_RESPONDIDA_ACEITE;
        $row['tipoResp'] = $this->_getTiposRespostaResp($objMdPetIntRelDestDTO);

        return $row;
    }

    private function _addDadosSituacaoPrazoVencido($objMdPetIntRelDestDTO){
        $dthData      =  $this->_getDataUltimoPrazoResposta($objMdPetIntRelDestDTO);
        $dataForm     =  !is_null($dthData) ?  substr($dthData, 0, -3) : null;

        $row = array();
        $row['id']       = MdPetIntimacaoRN::$INTIMACAO_PRAZO_VENCIDO;
        $row['data']     = $dataForm;
        $row['usuarioNome']  = $objMdPetIntRelDestDTO->getStrNomeContato();
        $row['usuarioEmail']  = $objMdPetIntRelDestDTO->getStrEmailContato();
        $row['unidadeSigla']  = $objMdPetIntRelDestDTO->getStrSiglaUnidadeIntimacao();
        $row['unidadeDescricao']  = $objMdPetIntRelDestDTO->getStrDescricaoUnidadeIntimacao();
        $row['tipoPessoa']  = $objMdPetIntRelDestDTO->getStrSinPessoaJuridica();
        $row['situacao'] = MdPetIntimacaoRN::$STR_INTIMACAO_PRAZO_VENCIDO_ACEITE;
        $row['tipoResp'] = '';

        return $row;
    }

    private function _getDataUltimoPrazoResposta($objMdPetIntRelDestDTO){

        $objMdPetIntRelTpDestRespRN  = new MdPetIntRelTipoRespDestRN();
        $id                          = $objMdPetIntRelDestDTO->getNumIdMdPetIntRelDestinatario();
        $dtReturn                    = '';

        //Get Data Limite
        $objMdPetIntRelTpDestRespDTO = new MdPetIntRelTipoRespDestDTO();
        $objMdPetIntRelTpDestRespDTO->setNumIdMdPetIntRelDest($id);
        $objMdPetIntRelTpDestRespDTO->retDthDataLimite();
        $objMdPetIntRelTpDestRespDTO->setOrdDthDataLimite(InfraDTO::$TIPO_ORDENACAO_DESC);
        $objMdPetIntRelTpDestRespDTO->setNumMaxRegistrosRetorno(1);

        $objMdPetIntRelTpDestRespDTO = $objMdPetIntRelTpDestRespRN->consultar($objMdPetIntRelTpDestRespDTO);

        $dtLimite = $objMdPetIntRelTpDestRespDTO->getDthDataLimite();

        //Get Data Prorrogada
        $objMdPetIntRelTpDestRespDTO = new MdPetIntRelTipoRespDestDTO();
        $objMdPetIntRelTpDestRespDTO->setNumIdMdPetIntRelDest($id);
        $objMdPetIntRelTpDestRespDTO->retDthDataProrrogada();
        $objMdPetIntRelTpDestRespDTO->setOrdDthDataProrrogada(InfraDTO::$TIPO_ORDENACAO_ASC);
        $objMdPetIntRelTpDestRespDTO->setNumMaxRegistrosRetorno(1);
        $objMdPetIntRelTpDestRespDTO->adicionarCriterio(array('DataProrrogada','DataProrrogada'),array(InfraDTO::$OPER_DIFERENTE,InfraDTO::$OPER_MAIOR),array(null ,$dtLimite),InfraDTO::$OPER_LOGICO_AND);

        $objMdPetIntRelTpDestRespDTO = $objMdPetIntRelTpDestRespRN->consultar($objMdPetIntRelTpDestRespDTO);


        $dtReturn = !is_null($objMdPetIntRelTpDestRespDTO) && !is_null($objMdPetIntRelTpDestRespDTO->getDthDataProrrogada()) ? $objMdPetIntRelTpDestRespDTO->getDthDataProrrogada() : $dtLimite;

        return $dtReturn;
    }

    private function _getDataResposta($objMdPetIntRelDestDTO){
        $id  = $objMdPetIntRelDestDTO->getNumIdMdPetIntRelDestinatario();

        $objMdPetIntDestRespRN  = new MdPetIntDestRespostaRN();
        $objMdPetIntDestRespDTO = new MdPetIntDestRespostaDTO();
        $objMdPetIntDestRespDTO->setNumIdMdPetIntRelDestinatario($id);
        $objMdPetIntDestRespDTO->setNumMaxRegistrosRetorno(1);
        $objMdPetIntDestRespDTO->retDthData();
        $objMdPetIntDestRespDTO->setOrdDthData(InfraDTO::$TIPO_ORDENACAO_ASC);
        $objMdPetIntDestRespDTO = $objMdPetIntDestRespRN->consultar($objMdPetIntDestRespDTO);

        if(!is_null($objMdPetIntDestRespDTO)){
            return $objMdPetIntDestRespDTO->getDthData();
        }

        return null;
    }

    private function _formatarNomeUnidade($objMdPetIntRelDestDTO){
        //Unidade
        $nomeUnidadeIntimacao = !is_null($objMdPetIntRelDestDTO->getStrSiglaUnidadeIntimacao()) ? $objMdPetIntRelDestDTO->getStrSiglaUnidadeIntimacao(). ' - '. $objMdPetIntRelDestDTO->getStrDescricaoUnidadeIntimacao() : '';
        $nomeUnidadeIntimacao = is_null($nomeUnidadeIntimacao) ? $objMdPetIntRelDestDTO->getStrSiglaUnidadeAcessoExt(). ' - '. $objMdPetIntRelDestDTO->getStrDescricaoUnidadeAcessoExt() : $nomeUnidadeIntimacao;

        return $nomeUnidadeIntimacao;
    }

    private function _getTiposRespostaIntimacao($idIntimacao){

        $objMdPetIntRelTpRespRN  = new MdPetIntRelTipoRespRN();
        $objMdPetIntRelTpRespDTO = new MdPetIntRelTipoRespDTO();
        $objMdPetIntRelTpRespDTO->setNumIdMdPetIntimacao($idIntimacao);
        $objMdPetIntRelTpRespDTO->retStrNome();
        $arrObjDTO = $objMdPetIntRelTpRespRN->listar($objMdPetIntRelTpRespDTO);

        $strReturn = '';
        foreach($arrObjDTO as $key=> $objDTO){
            $strReturn .= $objDTO->getStrNome();
            $proxKey = $key + 1;

            if(array_key_exists($proxKey, $arrObjDTO)){
                $strReturn .= ', ';
            }

        }

        return $strReturn;

    }

    private function _getTiposRespostaResp($objMdPetIntRelDestDTO){

        $arrControle = array();
        $id  = $objMdPetIntRelDestDTO->getNumIdMdPetIntRelDestinatario();
        $strReturn = '';

        $objMdPetIntRelRespRN = new MdPetIntDestRespostaRN();
        $objMdPetIntRelRespDTO = new MdPetIntDestRespostaDTO();
        $objMdPetIntRelRespDTO->setNumIdMdPetIntRelDestinatario($id);
        $objMdPetIntRelRespDTO->retNumIdMdPetIntRelTipoResp();

        $arrObjDTO = $objMdPetIntRelRespRN->listar($objMdPetIntRelRespDTO);

        if (count($arrObjDTO) > 0) {
            foreach($arrObjDTO as $key => $objDTO){
                if(!in_array($objDTO->getNumIdMdPetIntRelTipoResp(), $arrControle)){
                    array_push($arrControle, $objDTO->getNumIdMdPetIntRelTipoResp());
                }
            }
        }

        $strReturn = $this->_getArrNomesTpResposta($arrControle);

        return $strReturn;
    }

    //Função temporária, validar o modelo de dados para corrigir
    private function _getArrNomesTpResposta($arrIds){
        $str = '';
        $objMdPetIntRelTipoRespRN  = new MdPetIntRelTipoRespRN();
        $objMdPetIntRelTipoRespDTO = new MdPetIntRelTipoRespDTO();
        $objMdPetIntRelTipoRespDTO->setNumIdMdPetIntRelTipoResp($arrIds, InfraDTO::$OPER_IN);
        $objMdPetIntRelTipoRespDTO->retStrNome();

        $arrDados = $objMdPetIntRelTipoRespRN->listar($objMdPetIntRelTipoRespDTO);

        $arrNomes = InfraArray::converterArrInfraDTO($arrDados, 'Nome');

        $str = count($arrNomes) > 0 ? implode (", ", $arrNomes) : '';

        return $str;
    }

    private function _getTiposIntimacaoDasIntimacoes(){
        $objMdPetIntRelDestRN  = new MdPetIntRelDestinatarioRN();
        $objMdPetIntRelDestDTO = new MdPetIntRelDestinatarioDTO();
        $objMdPetIntRelDestDTO->retNumIdMdPetTipoIntimacao();
        $objMdPetIntRelDestDTO->retNumIdMdPetIntRelDestinatario();
        $objMdPetIntRelDestDTO->retStrNomeTipoIntimacao();

        $arrTipoIntimacao = PaginaSEI::getInstance()->getArrValuesSelect($_POST['hdnTpIntimacao']);
        if(count($arrTipoIntimacao) > 0) {
            $objMdPetIntRelDestDTO->setNumIdMdPetTipoIntimacao($arrTipoIntimacao, InfraDTO::$OPER_IN);
        }

        $arrDados = $objMdPetIntRelDestRN->listar($objMdPetIntRelDestDTO);

        return $arrDados;
    }


    /**
     *
     */
    protected function getArrGraficosIntimacaoConectado($tipoGrafico){

        ini_set('max_execution_time', '0');
        ini_set('memory_limit', '1024M');
        $arrRetorno = array();
        $arrObjDTOs = $this->_getTiposIntimacaoDasIntimacoes();

        if(count($arrObjDTOs) > 0){
            $idsTipoIntimacao = array_unique(InfraArray::converterArrInfraDTO($arrObjDTOs, 'IdMdPetTipoIntimacao'));
            $arrNomes     = $this->_retornaArrNomesTipoIntimacao($arrObjDTOs);

            foreach($idsTipoIntimacao as $idTipoIntimacao){
                $_SESSION[static::$ID_TP_INT_FILTRO_GRAFICO] = $idTipoIntimacao;
                $tamanhoGrafico = MdPetIntRelatorioRN::$GRAFICO_TAMANHO_PADRAO;
           
                $htmlGrafico = MdPetIntRelatorioINT::gerarGraficoGeral($tipoGrafico, $idTipoIntimacao, $tamanhoGrafico);

                if(!is_null($htmlGrafico)) {
                    $arrRetorno[$idTipoIntimacao]['html']  = $htmlGrafico;
                    $arrRetorno[$idTipoIntimacao]['label'] = $arrNomes[$idTipoIntimacao];
                }

                unset($_SESSION[static::$ID_TP_INT_FILTRO_GRAFICO]);
            }

        }

        return $arrRetorno;
    }

    private function _retornaArrNomesTipoIntimacao($arrObjDTOs){
        $arrNomes = array();
        foreach($arrObjDTOs as $objDTO){
            $id = $objDTO->getNumIdMdPetTipoIntimacao();
            if(!array_key_exists($id, $arrNomes)){
                $arrNomes[$id] = $objDTO->getStrNomeTipoIntimacao();
            }
        }
        return $arrNomes;
    }
    











}