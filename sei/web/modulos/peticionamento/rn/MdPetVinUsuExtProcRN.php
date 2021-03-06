<?php
/**
 * Created by PhpStorm.
 * User: jhon.carvalho
 * Date: 13/04/2018
 * Time: 09:57
 */

require_once dirname(__FILE__) . '/../../../SEI.php';

class MdPetVinUsuExtProcRN extends InfraRN
{
//    public static $TIPO_PETICIONAMENTO_RECIBO_VINC_PJ = 'Vinculação à Pessoa Jurídica';
    public static $TIPO_PETICIONAMENTO_RECIBO_PROCURACAOE_VINC_PJ = 'Procuração Eletrônica - Emissão';
    public static $TIPO_PETICIONAMENTO_RECIBO_PROCURACAOE_VINC_PF = 'Procuração Eletrônica';
    public static $TIPO_PETICIONAMENTO_RECIBO_RENUNCIA_VINC_PJ = 'Procuração Eletrônica - Renúncia';
    public static $TIPO_PETICIONAMENTO_RECIBO_REVOGACAO_VINC_PJ = 'Procuração Eletrônica - Revogação';
//    public static $TIPO_PETICIONAMENTO_PROC_VINC_PJ = 'Procuração Especial da Vinculação à Pessoa Jurídica';
    public static $TIPO_PETICIONAMENTO_REN_VINC_PJ = 'de Procuração Especial - Renúncia';
    public static $TIPO_PETICIONAMENTO_REV_VINC_PJ = 'de Procuração Especial - Revogação';

    public function __construct()
    {
        parent::__construct();
    }

    protected function inicializarObjInfraIBanco()
    {
        // TODO: Implement inicializarObjInfraIBanco() method.
        return BancoSEI::getInstance();
    }

    /**
     * @param $dados
     * @throws InfraException
     */
    public function gerarProcedimentoVinculoProcuracaoControlado($dados)
    {   
        $existeProc = false;
        
        try {
            if($dados['hdnOutorgante'] != ""){
                $outorgant = $dados['hdnOutorgante'];
            }
           
            $objMdPetVincTpProcessoRN = new MdPetVincTpProcessoRN();
            $objMdPetVincTpProcessoDTO = new MdPetVincTpProcessoDTO();
            $objMdPetVincTpProcessoDTO->retTodos();
            if($dados['hdnOutorgante'] == "PJ" && $dados['selTipoProcuracao'] == MdPetVincRepresentantRN::$PE_PROCURADOR_SIMPLES){
            $objMdPetVincTpProcessoDTO->setStrTipoVinculo('J');
            }else if($dados['hdnOutorgante'] == "PF" && $dados['selTipoProcuracao'] == MdPetVincRepresentantRN::$PE_PROCURADOR_SIMPLES){
            $objMdPetVincTpProcessoDTO->setStrTipoVinculo('F');
            }else if($dados['selTipoProcuracao'] == "E"){
            $objMdPetVincTpProcessoDTO->setStrTipoVinculo('J');
            }
            
            $arrObjMdPetVincTpProcesso = $objMdPetVincTpProcessoRN->consultar($objMdPetVincTpProcessoDTO);
            
            if (is_null($arrObjMdPetVincTpProcesso)) {
                throw new InfraException('Vinculação não configurada');
            }
            
            //Recuperando Vinculo já existente
            $objMdPetVincRepresentantDTO = new MdPetVincRepresentantDTO();
            $objMdPetVincRepresentantDTO->retDblIdProcedimentoVinculo();
            if($dados['hdnOutorgante'] == "PJ" && $dados['selTipoProcuracao'] == MdPetVincRepresentantRN::$PE_PROCURADOR_SIMPLES){
                $objMdPetVincRepresentantDTO->setNumIdContatoVinc($dados['hdnSelPJSimples']);
                $objMdPetVincRepresentantDTO->setStrTpVinc("J");
            }else if($dados['hdnOutorgante'] == "PF" && $dados['selTipoProcuracao'] == MdPetVincRepresentantRN::$PE_PROCURADOR_SIMPLES){
                $objMdPetVincRepresentantDTO->setNumIdContatoVinc($dados['idContatoExterno']);
                $objMdPetVincRepresentantDTO->setStrTpVinc("F");
            }
            $objMdPetVincRepresentantRN = new MdPetVincRepresentantRN();
            $arrObjMdPetVincRepresentantDTO = $objMdPetVincRepresentantRN->listar($objMdPetVincRepresentantDTO);
            $arrProcessos = InfraArray::converterArrInfraDTO($arrObjMdPetVincRepresentantDTO,'IdProcedimentoVinculo');
            
            if(count(array_unique($arrProcessos))){
                $objProtocoloDTO = new ProtocoloDTO();
                $objProtocoloDTO->setDblIdProtocolo($arrProcessos[0]);
                $objProtocoloDTO->retNumIdUnidadeGeradora();
                $objProtocoloRN = new ProtocoloRN();
                $objUnidadeGeradora = $objProtocoloRN->consultarRN0186($objProtocoloDTO);
                if($objUnidadeGeradora->getNumIdUnidadeGeradora() == $arrObjMdPetVincTpProcesso->getNumIdUnidade()){
                    $idTipoUnidadeAberturaProcesso = $objUnidadeGeradora->getNumIdUnidadeGeradora();
                } else {
                    $idTipoUnidadeAberturaProcesso = $arrObjMdPetVincTpProcesso->getNumIdUnidade();
                }                
            }else{
                $idTipoUnidadeAberturaProcesso = $arrObjMdPetVincTpProcesso->getNumIdUnidade();
            }
           // var_dump($idTipoUnidadeAberturaProcesso);die;
           // $idTipoUnidadeAberturaProcesso = 110000909;
           //110000909
            //Obtendo tipo do processo
            $idTipoProcesso = $arrObjMdPetVincTpProcesso->getNumIdTipoProcedimento();

            $objTipoProcedimentoDTO = new TipoProcedimentoDTO();
            $objTipoProcedimentoDTO->retNumIdTipoProcedimento();
            $objTipoProcedimentoDTO->retStrNome();
            $objTipoProcedimentoDTO->setNumIdTipoProcedimento($idTipoProcesso);

            $objTipoProcedimentoRN = new TipoProcedimentoRN();
            $objTipoProcedimentoDTO = $objTipoProcedimentoRN->consultarRN0267($objTipoProcedimentoDTO);

            if ($objTipoProcedimentoDTO == null) {
                throw new InfraException('Tipo de processo não encontrado.');
            }

           
            
            //obter unidade configurada no "Tipo de Processo para peticionamento"
            $unidadeRN = new UnidadeRN();
            $unidadeDTO = new UnidadeDTO();
            $unidadeDTO->retTodos();
            $unidadeDTO->setNumIdUnidade($idTipoUnidadeAberturaProcesso);
            $unidadeDTO = $unidadeRN->consultarRN0125($unidadeDTO);

            if (is_null($unidadeDTO)) {
                throw new InfraException('Tipo de unidade não encontrada.');
            }

            if(isset($_POST['hdnTbUsuarioProcuracao']) && $_POST['hdnTbUsuarioProcuracao'] != "") {
                $dadosProcuracao = PaginaSEIExterna::getInstance()->getArrItensTabelaDinamica($_POST['hdnTbUsuarioProcuracao']);
            }


            $objMdPetContatoRN = new MdPetContatoRN();
            $idTipoContatoUsExt = $objMdPetContatoRN->getIdTipoContatoUsExt();

            $contatoRN = new ContatoRN();
            $contatoDTO = new ContatoDTO();
            $contatoDTO->retNumIdContato();
            $contatoDTO->setNumIdTipoContato($idTipoContatoUsExt);
            $arrIdContato = [];

            //Caso venha da tela de especial
            if(isset($_POST['hdnTbUsuarioProcuracao']) && $_POST['hdnTbUsuarioProcuracao'] != "" ) {
            foreach ($dadosProcuracao as $procuracao) {
                $cpf = InfraUtil::retirarFormatacao($procuracao[1]);
                $contatoDTO->setDblCpf($cpf);
                $contatoDTO->setNumIdContato($procuracao[0]);
                $arrContato = $contatoRN->listarRN0325($contatoDTO);
                $arrIdContato[] = $arrContato[0]->getNumIdContato();
                
            }
            }else{
                $contatoDTO->setNumIdContato($dados['hdnIdUsuario']);
                $arrContato = $contatoRN->listarRN0325($contatoDTO);
                $arrIdContato[] = $arrContato[0]->getNumIdContato();
            }
            
            //$arrIdContato = $dados['hdnIdContExterno'];
            SessaoSEI::getInstance()->simularLogin(null, null, SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno(), $unidadeDTO->getNumIdUnidade());
            
            // Recupera o idProcedimento(Processo) referente ao cnpj informado (idContato)
            $objMdPetVinculoDTO = new MdPetVinculoDTO();
            $objMdPetVinculoRN = new MdPetVinculoRN();
            if($dados['selTipoProcuracao'] == MdPetVincRepresentantRN::$PE_PROCURADOR_SIMPLES && $dados['hdnOutorgante'] == "PJ"){
            $objMdPetVinculoDTO->setNumIdContato($dados['idContato']);
            }
            //Caso seja outorgante pessoa física
            if($dados['selTipoProcuracao'] == MdPetVincRepresentantRN::$PE_PROCURADOR_SIMPLES && $dados['hdnOutorgante'] == "PF"){
            $objMdPetVinculoDTO->setNumIdContato($dados['idContatoExterno']);
            }
            if($dados['selTipoProcuracao'] == "E"){
            $objMdPetVinculoDTO->setNumIdContato($dados['idContato']);
            }
            $objMdPetVinculoDTO->retTodos();

            $mdPetVinculacao = $objMdPetVinculoRN->listar($objMdPetVinculoDTO);

            $qtd = $objMdPetVinculoRN->contar($objMdPetVinculoDTO);
            
            //Verifica se existe o idContato na tabela MdPetVinculoDTO
            if($qtd == 0){
            
            //Criando Processo Novo Caso seja o primeiro acesso
            //Recuperando Configuração Estabelecida de Pessoa Jurídica
            
            $arrObjMdPetVincTpProcesso   = $this->getConfiguracaoVinculo();
            
            $objTipoProcedimentoDTO   = $this->_getTipoProcesso($arrObjMdPetVincTpProcesso);
            //obtendo a unidade de abertura do processo
            $objUnidadeDTO = $this->_getUnidade($arrObjMdPetVincTpProcesso);
            //Criação do PRocess
            $idProc = $this->_gerarProcessoNovo($idTipoProcesso, $objUnidadeDTO, $dados,$arrObjMdPetVincTpProcesso);

            $id_vinc = $this->_adicionarVinculoPF($dados,$idProc);
            
            }else{

            $idProc = $mdPetVinculacao[0]->getDblIdProtocolo();
      
               // $arrObjMdPetVincTpProcesso   = $this->getConfiguracaoVinculo();
                //Remetendo PRocesso
                $objTipoProcedimentoDTO   = $this->_getTipoProcesso($arrObjMdPetVincTpProcesso);
                //obtendo a unidade de abertura do processo
                $objUnidadeDTO = $this->_getUnidade($arrObjMdPetVincTpProcesso);
                //Unidade Atual do Processo Diferente do Parametrizado
                //Busca Unidade do Processo já Existente

                $objProtocoloDTO = new ProtocoloDTO();
                $objProtocoloDTO->setDblIdProtocolo($idProc);
                $objProtocoloDTO->retNumIdUnidadeGeradora();
                $objProtocoloRN = new ProtocoloRN();
                $objUnidadeGeradora = $objProtocoloRN->consultarRN0186($objProtocoloDTO);
              
                
                if(($objUnidadeDTO->getNumIdUnidade() != $objUnidadeGeradora->getNumIdUnidadeGeradora())){
                   // var_dump($objUnidadeDTO->getNumIdUnidade());die;
                    //Realiza Tramitação
                    $arrObjAtributoAndamentoDTO = array();
                    $objAtributoAndamentoDTO = new AtributoAndamentoDTO();
                    $objAtributoAndamentoDTO->setStrNome('UNIDADE');
                    $objAtributoAndamentoDTO->setStrValor($objUnidadeDTO->getStrSigla().' ¥ '.$objUnidadeDTO->getStrDescricao());
                    $objAtributoAndamentoDTO->setStrIdOrigem($objUnidadeGeradora->getNumIdUnidadeGeradora());
                    $arrObjAtributoAndamentoDTO[] = $objAtributoAndamentoDTO;
                   
                    $objAtividadeDTO = new AtividadeDTO();
                    $objAtividadeDTO->setDblIdProtocolo( $idProc );
                    $objAtividadeDTO->setNumIdUnidade( $objUnidadeDTO->getNumIdUnidade() );
                    $objAtividadeDTO->setNumIdUnidadeOrigem( $objUnidadeGeradora->getNumIdUnidadeGeradora() );
                    $objAtividadeDTO->setArrObjAtributoAndamentoDTO($arrObjAtributoAndamentoDTO);
                    $objAtividadeDTO->setNumIdTarefa(TarefaRN::$TI_PROCESSO_REMETIDO_UNIDADE);

                    $objAtividadeRN = new AtividadeRN();
                    $objAtividadeRN->gerarInternaRN0727($objAtividadeDTO);
                    
                }

            }
            
            $objProcedimentoDTO = new ProcedimentoDTO();
            $objProcedimentoRN = new ProcedimentoRN();
            $objProcedimentoDTO->retStrProtocoloProcedimentoFormatado();
            $objProcedimentoDTO->retDblIdProcedimento();
            $objProcedimentoDTO->setDblIdProcedimento($idProc);

            $objProcedimentoDTO = $objProcedimentoRN->consultarRN0201($objProcedimentoDTO);

            $idProcedimento = $objProcedimentoDTO->getDblIdProcedimento();
            

            
            $objMdPetVinculoDTO = new MdPetVinculoDTO();
            $mdPetVinculacaoRN = new MdPetVinculoRN();
            
            //Caso a Pessoa Seja Juridica da Tela de Procuração Especial
            if($dados['selTipoProcuracao'] == "E"){
            $objMdPetVinculoDTO->setNumIdContato($dados['selPessoaJuridica']);
            }
            if($dados['selTipoProcuracao'] == MdPetVincRepresentantRN::$PE_PROCURADOR_SIMPLES && $dados['hdnOutorgante'] == "PJ"){
            $objMdPetVinculoDTO->setNumIdContato($dados['hdnSelPJSimples']);   
            }
            if($dados['selTipoProcuracao'] == MdPetVincRepresentantRN::$PE_PROCURADOR_SIMPLES && $dados['hdnOutorgante'] == "PF"){
            $objMdPetVinculoDTO->setNumIdContato($dados['idContatoExterno']);   
            }
            $objMdPetVinculoDTO->retNumIdMdPetVinculo();
            $objMdPetVinculoDTO->setDblIdProtocolo($idProcedimento);

            $objMdPetVinculoDTO = $objMdPetVinculoRN->consultar($objMdPetVinculoDTO);
            $idVinculo = $objMdPetVinculoDTO->getNumIdMdPetVinculo();
            
            
            if($dados['selTipoProcuracao'] == "E"){
            $idContatoVinc = $dados['selPessoaJuridica'];
            }if($dados['selTipoProcuracao'] == MdPetVincRepresentantRN::$PE_PROCURADOR_SIMPLES && $dados['hdnOutorgante'] == "PF" ){
            $idContatoVinc = $dados['idContatoExterno'];   
            }
            if($dados['selTipoProcuracao'] == MdPetVincRepresentantRN::$PE_PROCURADOR_SIMPLES && $dados['hdnOutorgante'] == "PJ" ){
            $idContatoVinc = $dados['hdnSelPJSimples'];   
            }
            

           if(($dados['selTipoProcuracao'] == MdPetVincRepresentantRN::$PE_PROCURADOR_SIMPLES && $dados['hdnOutorgante'] == "PJ") || ($dados['selTipoProcuracao'] == MdPetVincRepresentantRN::$PE_PROCURADOR_SIMPLES && $dados['hdnOutorgante'] == "PF") ||  ($dados['selTipoProcuracao'] == "E")){
               if($dados['selTipoProcuracao'] == "E"){

                $idPessoa = $dados['selPessoaJuridica'];

               }else if($dados['selTipoProcuracao'] == MdPetVincRepresentantRN::$PE_PROCURADOR_SIMPLES && $dados['hdnOutorgante'] == "PJ"){

                $idPessoa = $dados['hdnSelPJSimples'];

               }else if($dados['selTipoProcuracao'] == MdPetVincRepresentantRN::$PE_PROCURADOR_SIMPLES && $dados['hdnOutorgante'] == "PF"){

                $idPessoa = $dados['idContatoExterno'];

               }
           
            // consultar dados Pj
            $contatoDTO = new ContatoDTO();
            $contatoDTO->retDblCnpj();
            $contatoDTO->retStrNome();
            $contatoDTO->setNumIdContato($idContatoVinc);

            $contatoRN = new ContatoRN();
            $contatoPj = $contatoRN->consultarRN0324($contatoDTO);
            $dadosRetornoProcuracao['contatoPj'] = $contatoPj;

            // consultar dados Responsável Legal
            $contatoDTO = new ContatoDTO();
            $contatoDTO->retDblCpf();
            $contatoDTO->retStrNome();
            $contatoDTO->setNumIdContato($idPessoa);

            $contatoRN = new ContatoRN();
            $Replegal = $contatoRN->consultarRN0324($contatoDTO);
            $dadosRetornoProcuracao['RepLegal'] = $Replegal;
         
           }

            $numRegistro = count($arrIdContato);
            
            //Caso seja Procuração Especial
            if($dados['selTipoProcuracao'] == "E"){
           
            for($i= 0 ;$i <$numRegistro;$i++) {
                $dados['IdOutorgado']=$arrIdContato[$i];

                //Gerar Documento Nova Procuracao
                $params = array('dados'=>$dados,
                    'procedimento'=>$objProcedimentoDTO,
                    'idVinculo'=>$idVinculo,
                    'RepLegal'=>$Replegal,
                    'contatoPj'=>$contatoPj,
                    'idContato'=>$dados['idContato'],
                    'tipoProcuracao'=>$dados['selTipoProcuracao'],
                    'outorgante'=>$dados['hdnOutorgante'],
                    'unidadeDTO'=>$unidadeDTO,
                    'idProc' => $idProc);
                $dadosRetornoProcuracao['Procuracao'][] = $this->gerarFormularioProcuracao($params);

            }

            }else if(($dados['selTipoProcuracao'] == MdPetVincRepresentantRN::$PE_PROCURADOR_SIMPLES && $dados['hdnOutorgante'] == "PJ") ||  ($dados['selTipoProcuracao'] == MdPetVincRepresentantRN::$PE_PROCURADOR_SIMPLES && $dados['hdnOutorgante'] == "PF") ){
               
                //Gerar Documento Nova Procuracao
                $params = array('dados'=>$dados,
                    'procedimento'=>$objProcedimentoDTO,
                    'idVinculo'=>$idVinculo,
                    'RepLegal'=>$Replegal,
                    'contatoPj'=>$contatoPj,
                    'idContato'=>$dados['idContato'],
                    'tipoProcuracao'=>$dados['selTipoProcuracao'],
                    'outorgante'=>$dados['hdnOutorgante'],
                    'unidadeDTO'=>$unidadeDTO,
                    'idProc' => $idProc);
                    
                $dadosRetornoProcuracao['Procuracao'][] = $this->gerarFormularioProcuracaoSimples($params);

            }
           
            //RECUPERA ID_MD_PET_REPRESENTANT DO VINCULO PRINCIPAL CASO TENHA MAIS DE UMA PROCURAÇÃO
            
            if($numRegistro>1) {
                $idResponsavelLegal = $dados['idContatoExterno'];
                $objMdPetVincRepresentantDTO = new MdPetVincRepresentantDTO();
                $objMdPetVincRepresentantRN = new MdPetVincRepresentantRN();

                $objMdPetVincRepresentantDTO->setNumIdMdPetVinculo($idVinculo);
                $objMdPetVincRepresentantDTO->setNumIdContato($idResponsavelLegal);
                $objMdPetVincRepresentantDTO->setNumIdContato($idResponsavelLegal);
                $objMdPetVincRepresentantDTO->setStrStaEstado(MdPetVincRepresentantRN::$RP_ATIVO);
                $objMdPetVincRepresentantDTO->setStrTipoRepresentante(MdPetVincRepresentantRN::$PE_RESPONSAVEL_LEGAL);
                $objMdPetVincRepresentantDTO->retNumIdMdPetVinculoRepresent();

                $objMdPetVincRepresentantDTO = $objMdPetVincRepresentantRN->consultar($objMdPetVincRepresentantDTO);
                $idMdPetVinculoRepresent = $objMdPetVincRepresentantDTO->getNumIdMdPetVinculoRepresent();
                
            }else{
                
                $idMdPetVinculoRepresent = $dadosRetornoProcuracao['Procuracao'][0]['IdMdPetVinculoRepresent'];
            }
            //Tratanto Tipo de Poderes
            if($dados['selTipoProcuracao'] == MdPetVincRepresentantRN::$PE_PROCURADOR_SIMPLES && $dados['hdnTipoPoder'] != ""){
                $tipoPoderes = explode("-",$dados['hdnTipoPoder']);
                
                foreach ($tipoPoderes as $poder) {
                    $objMdPetRelVincRepTpPoderDTO = new MdPetRelVincRepTpPoderDTO();
                    $objMdPetRelVincRepTpPoderDTO->setNumIdTipoPoderLegal($poder);
                    $objMdPetRelVincRepTpPoderDTO->setNumIdVinculoRepresent($idMdPetVinculoRepresent);
                    $objMdPetRelVincRepTpPoderRN = new MdPetRelVincRepTpPoderRN();
                    $objMdPetRelVincRepTpPoderRN->cadastrar($objMdPetRelVincRepTpPoderDTO);
                }

            }
            //Caso seja Simples a Procuração, inserir dinamicamente os Processos
            if($dados['selTipoProcuracao'] == MdPetVincRepresentantRN::$PE_PROCURADOR_SIMPLES){
                //Caso os Dados Venha da tela de Procuração Simples
            if(isset($_POST['hdnTbProcessos']) && $_POST['selTipoProcuracao'] == MdPetVincRepresentantRN::$PE_PROCURADOR_SIMPLES && $_POST['hdnTbProcessos'] != "") {
                $dadosProcesso = PaginaSEIExterna::getInstance()->getArrItensTabelaDinamica($_POST['hdnTbProcessos']);
                foreach ($dadosProcesso as $key => $idProtocolo) {
                   
                $objMdPetRelVincRepProtocDTO = new MdPetRelVincRepProtocDTO();
                $objMdPetRelVincRepProtocRN = new MdPetRelVincRepProtocRN();
                $objMdPetRelVincRepProtocDTO->setNumIdVincRepresent($idMdPetVinculoRepresent);
                $objMdPetRelVincRepProtocDTO->setNumIdProtocolo($idProtocolo[0]);
                $objMdPetRelVincRepProtocRN->cadastrar($objMdPetRelVincRepProtocDTO);

                }
             }
            }
            //Peticionamento Eletrônico - Alerta às Unidades
            //Peticionamento Eletrônico - Confirmação ao Usuário Externo
            if(!isset($params['tela'])) {
               
                if($dados['hdnOutorgante'] == "PF"){
                    $tipoPessoa = MdPetVincTpProcessoRN::$ID_FIXO_MD_PET_VINCULO_USU_EXT_PF;
                }else if($dados['hdnOutorgante'] == "PJ"){
                    $tipoPessoa = MdPetVincTpProcessoRN::$ID_FIXO_MD_PET_VINCULO_USU_EXT;
                }
                
                $tipoPeticionamento = MdPetReciboRN::$TP_RECIBO_PROCURACAO_ELETRONICA_EMISSAO;
                
                $objMdPetVinculoUsuExtRN = new MdPetVinculoUsuExtRN();
              
                $reciboDTOBasico = $objMdPetVinculoUsuExtRN->salvarDadosReciboPeticionamento(array('idProcedimento' => $idProcedimento, 'staTipoPeticionamento' => $tipoPeticionamento));
                
                $recibo = $this->gerarReciboProcuracao(array($dadosRetornoProcuracao, $objProcedimentoDTO, $reciboDTOBasico, $idMdPetVinculoRepresent, $tipoPeticionamento, $unidadeDTO, $dados));
                $idDocumentoRecibo = $reciboDTOBasico->getDblIdDocumento();
               
                $mdPetVinculoUsuExtRN = new MdPetVinculoUsuExtRN();
                $objMdPetIntimacaoRN = new MdPetIntimacaoRN();
                $objProcedimentoDTO = $mdPetVinculoUsuExtRN->getProcedimento($idProcedimento);
                $objUnidadeDTO = $mdPetVinculoUsuExtRN->getUnidade($tipoPessoa);
                $arrParams = array();
                $arrParams[0] = $dados;
                $arrParams[1] = $objUnidadeDTO;
                $arrParams[2] = $objProcedimentoDTO;
                $arrParams[3] = array();
                $arrParams[4] = $reciboDTOBasico;
                $arrParams[5] = $reciboDTOBasico;

                $objMdRegrasGeraisRN = new MdPetRegrasGeraisRN();
                $strTipoPeticionamento =  $objMdRegrasGeraisRN->getTipoPeticionamento($tipoPeticionamento, true);

                $objMdPetVinculoUsuExtRN->gerarAndamentoVinculo(array($idProcedimento, $strTipoPeticionamento, $idDocumentoRecibo, $unidadeDTO->getNumIdUnidade()));
               
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

            $objAtividadeDTOExclusao = new AtividadeDTO();
            $atividadeRN = new AtividadeRN();
            $objAtividadeDTOExclusao->setDblIdProtocolo( $objProcedimentoDTO->getDblIdProcedimento() );
            $objAtividadeDTOExclusao->setNumIdUnidade( $objUnidadeDTO->getNumIdUnidade() );
            $objAtividadeDTOExclusao->setNumIdTarefa(TarefaRN::$TI_PROCESSO_REMETIDO_UNIDADE);
            $objAtividadeDTOExclusao->setOrd('IdAtividade', InfraDTO::$TIPO_ORDENACAO_DESC);
            $objAtividadeDTOExclusao->retTodos();
            $objAtividadeDTOExclusao = $atividadeRN->listarRN0036($objAtividadeDTOExclusao);

            if($objAtividadeDTOExclusao) {
                //apagando andamentos do tipo "Disponibilizado acesso externo para @INTERESSADO@"
                $objAtividadeDTOLiberacao = new AtividadeDTO();
                $objAtividadeDTOLiberacao->retTodos();
                $objAtividadeDTOLiberacao->setDblIdProtocolo($objProcedimentoDTO->getDblIdProcedimento());
                $objAtividadeDTOLiberacao->setNumIdAtividade(current($objAtividadeDTOExclusao)->getNumIdAtividade());
                $objAtividadeDTOLiberacao->setNumIdTarefa(TarefaRN::$TI_PROCESSO_REMETIDO_UNIDADE);

                $arrDTOAtividades = $atividadeRN->listarRN0036($objAtividadeDTOLiberacao);
                $atividadeRN->excluirRN0034($arrDTOAtividades);
            }

            $objAtividadeDTOExclusao = new AtividadeDTO();
            $atividadeRN = new AtividadeRN();
            $objAtividadeDTOExclusao->setDblIdProtocolo( $objProcedimentoDTO->getDblIdProcedimento() );
            $objAtividadeDTOExclusao->setNumIdUnidade( $objUnidadeDTO->getNumIdUnidade() );
            $objAtividadeDTOExclusao->setNumIdTarefa(TarefaRN::$TI_PROCESSO_RECEBIDO_UNIDADE);
            $objAtividadeDTOExclusao->setOrd('IdAtividade', InfraDTO::$TIPO_ORDENACAO_DESC);
            $objAtividadeDTOExclusao->retTodos();
            $objAtividadeDTOExclusao = $atividadeRN->listarRN0036($objAtividadeDTOExclusao);

            if($objAtividadeDTOExclusao) {
                //apagando andamentos do tipo "Disponibilizado acesso externo para @INTERESSADO@"
                $objAtividadeDTOLiberacao = new AtividadeDTO();
                $objAtividadeDTOLiberacao->retTodos();
                $objAtividadeDTOLiberacao->setDblIdProtocolo($objProcedimentoDTO->getDblIdProcedimento());
                $objAtividadeDTOLiberacao->setNumIdAtividade(current($objAtividadeDTOExclusao)->getNumIdAtividade());
                $objAtividadeDTOLiberacao->setNumIdTarefa(TarefaRN::$TI_PROCESSO_RECEBIDO_UNIDADE);

                $arrDTOAtividades = $atividadeRN->listarRN0036($objAtividadeDTOLiberacao);
                $atividadeRN->excluirRN0034($arrDTOAtividades);
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

                $mdPetVinculoUsuExtRN->enviarEmail($arrParams);
                
                //Disponibilazando Acesso Externo ao Responsável Legal
                $objMdPetAcessoExternoRN = new MdPetAcessoExternoRN();
                $objMdPetAcessoExternoRN->gerarAcessoExternoVinculo(array($idVinculo, $idProcedimento));
                
                $objAtividadeRN = new AtividadeRN();
                $objAtividadeRN->gerarInternaRN0727($objAtividadeDTO);
            }
            return true;

        } catch (Exception $e) {
            throw new InfraException('Erro cadastrando processo peticionamento do SEI.', $e);
        }

    }

    /**
     *
     */

    
    private function _adicionarVinculoPF($dados, $idProcedimento)
    {
      
  
      $objMdPetVinculoRN = new MdPetVinculoRN();
      $objMdPetVinculoDTO = new MdPetVinculoDTO();
      $objMdPetVinculoDTO->setNumIdContato($dados['idContatoExterno']);
      $objMdPetVinculoDTO->setStrSinValidado("N");
      $objMdPetVinculoDTO->retNumIdMdPetVinculo();
      $objMdPetVinculoDTO->setDblIdProtocolo($idProcedimento);
      $objMdPetVinculoDTO->setStrTpVinculo('F');
      $objMdPetVinculoDTO->setStrSinWebService("N");
  
      $objMdPetVinculoDTO = $objMdPetVinculoRN->cadastrar($objMdPetVinculoDTO);
  
      return $objMdPetVinculoDTO->getNumIdMdPetVinculo();
  
  
    }

    protected function getConfiguracaoVinculoConectado(){
        
        $objMdPetVincTpProcessoRN = new MdPetVincTpProcessoRN();
        $objMdPetVincTpProcessoDTO = new MdPetVincTpProcessoDTO();
        $objMdPetVincTpProcessoDTO->retTodos();
        $objMdPetVincTpProcessoDTO->setStrTipoVinculo('F');
        $arrObjMdPetVincTpProcesso = $objMdPetVincTpProcessoRN->consultar($objMdPetVincTpProcessoDTO);

        if (is_null($arrObjMdPetVincTpProcesso)) {
          throw new InfraException('Vinculação não configurada');
        }
    
        return $arrObjMdPetVincTpProcesso;
      }


      private function _getTipoProcesso($arrObjMdPetVincTpProcesso){
        $idTipoProcesso = $arrObjMdPetVincTpProcesso->getNumIdTipoProcedimento();
    
        $objTipoProcedimentoDTO = new TipoProcedimentoDTO();
        $objTipoProcedimentoDTO->retNumIdTipoProcedimento();
        $objTipoProcedimentoDTO->retStrNome();
        $objTipoProcedimentoDTO->setNumIdTipoProcedimento($idTipoProcesso);
    
        $objTipoProcedimentoRN = new TipoProcedimentoRN();
        $objTipoProcedimentoDTO = $objTipoProcedimentoRN->consultarRN0267($objTipoProcedimentoDTO);
    
        if ($objTipoProcedimentoDTO == null) {
          throw new InfraException('Tipo de processo não encontrado.');
        }
    
    
        return $objTipoProcedimentoDTO;
      }

      private function _gerarProcessoNovo($idTipoProcesso, $objUnidadeDTO, &$dados,$arrObjMdPetVincTpProcesso)
  {
   
    $arrDados = array();
    $objSeiRN = new SeiRN();
    $objMdPetReciboRN = new MdPetReciboRN();
    $usuarioRN = new UsuarioRN();

    //Gera um processo
    $objSaidaGerarProcedimentoAPI = $this->_gerarProcedimento($idTipoProcesso, $objUnidadeDTO,$arrObjMdPetVincTpProcesso,$dados);

    //Processo - Interessado somente a PJ
    $objParticipante = new ParticipanteDTO();
    $objParticipante->setDblIdProtocolo($objSaidaGerarProcedimentoAPI->getIdProcedimento());
    if($dados['selTipoProcuracao'] == MdPetVincRepresentantRN::$PE_PROCURADOR_SIMPLES && $dados['hdnOutorgante'] == "PF"){
    $objParticipante->setNumIdContato($dados['idContatoExterno']);
    }else{
    $objParticipante->setNumIdContato($dados['idContato']);   
    }
    $objParticipante->setNumIdUnidade($objUnidadeDTO->getNumIdUnidade());
    $objParticipante->setStrStaParticipacao(ParticipanteRN::$TP_INTERESSADO);
    $objParticipante->setNumSequencia(0);
    $idsParticipantes[] = $objParticipante;

    $objMdPetParticipanteRN = new MdPetParticipanteRN();
    $arrInteressado = array();
    $arrInteressado[0] = $objSaidaGerarProcedimentoAPI->getIdProcedimento();
    $arrInteressado[1] = $idsParticipantes;
    $objMdPetParticipanteRN->setInteressadosRemetentesProcedimentoDocumento($arrInteressado);
    // Processo - Interessado somente a PJ - FIM

    $objEntradaConsultarProcedimentoAPI = new EntradaConsultarProcedimentoAPI();
    $objEntradaConsultarProcedimentoAPI->setIdProcedimento($objSaidaGerarProcedimentoAPI->getIdProcedimento());
    $objSaidaConsultarProcedimentoAPI = $objSeiRN->consultarProcedimento($objEntradaConsultarProcedimentoAPI);

    $nomeTipo = $objSaidaConsultarProcedimentoAPI->getTipoProcedimento()->getNome();

    $objProcedimentoDTO = new ProcedimentoDTO();
    $objProcedimentoDTO->setStrNomeTipoProcedimento($nomeTipo);
    $objProcedimentoDTO->setDblIdProcedimento($objSaidaGerarProcedimentoAPI->getIdProcedimento());
    $objProcedimentoDTO->setStrProtocoloProcedimentoFormatado($objSaidaConsultarProcedimentoAPI->getProcedimentoFormatado());
    $objProcedimentoDTO->setNumIdTipoProcedimento($objSaidaConsultarProcedimentoAPI->getTipoProcedimento()->getIdTipoProcedimento());
    $idProcedimento = $objProcedimentoDTO->getDblIdProcedimento();


    return $idProcedimento;
  }

  private function _gerarProcedimento($idTipoProcesso, $objUnidadeDTO,$arrObjMdPetVincTpProcesso,$dados){
    $objProcedimentoAPI = new ProcedimentoAPI();
    $objProcedimentoAPI->setIdTipoProcedimento($idTipoProcesso);
    $objProcedimentoAPI->setIdUnidadeGeradora($objUnidadeDTO->getNumIdUnidade());
    //ESPECIFICAÇÃO
    $contatoDTO = new ContatoDTO();
    $contatoDTO->retStrNome();
    $contatoDTO->retDblCpf();
    $contatoDTO->setNumIdContato($dados['hdnIdContExterno']);
    $contatoRN = new ContatoRN();
    $objContatoRN = $contatoRN->consultarRN0324($contatoDTO);
    
    $especificacao = $arrObjMdPetVincTpProcesso->getStrEspecificacao();
    $nomeModificado = str_replace("@nome_completo@",$objContatoRN->getStrNome(),$especificacao);
    $nome_cpf = str_replace("@cpf@",InfraUtil::formatarCpf($objContatoRN->getDblCpf()),$nomeModificado);

    //trata campo especificacao limite de 100 caracteres
    //Se o conteúdo for superior a 100 caracteres, deve ser considerado somente o conteúdo até a última palavra inteira antes do 100º caracter.
    $nome_cpf = trim($nome_cpf);
    if(strlen($nome_cpf) > 100){
        $nome_cpf = substr($nome_cpf, 0, 100);
        $arrNomeCpf =  explode(" ", $nome_cpf, -1);
        $nome_cpf =  implode(" ", $arrNomeCpf);
    }
    
    $objProcedimentoAPI->setEspecificacao($nome_cpf);
    $objProcedimentoAPI->setNumeroProtocolo('');
    $objProcedimentoAPI->setNivelAcesso(ProtocoloRN::$NA_PUBLICO);
    $objProcedimentoAPI->setIdHipoteseLegal(null);

    $objEntradaGerarProcedimentoAPI = new EntradaGerarProcedimentoAPI();
    $objEntradaGerarProcedimentoAPI->setProcedimento($objProcedimentoAPI);

    $objSeiRN = new SeiRN();
    SessaoSEI::getInstance()->simularLogin(null, null, SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno(), $objUnidadeDTO->getNumIdUnidade());
    $objSaidaGerarProcedimentoAPI = new SaidaGerarProcedimentoAPI();
    $objSaidaGerarProcedimentoAPI = $objSeiRN->gerarProcedimento($objEntradaGerarProcedimentoAPI);

    return $objSaidaGerarProcedimentoAPI;
}


      private function _getUnidade($arrObjMdPetVincTpProcesso){
        $idUnidadeAberturaProcesso = $arrObjMdPetVincTpProcesso->getNumIdUnidade();
    
        //obter unidade configurada no "Tipo de Processo para peticionamento"
        $unidadeRN = new UnidadeRN();
        $unidadeDTO = new UnidadeDTO();
        $unidadeDTO->retTodos();
        $unidadeDTO->setNumIdUnidade($idUnidadeAberturaProcesso);
        $unidadeDTO = $unidadeRN->consultarRN0125($unidadeDTO);
    
        if (is_null($unidadeDTO)) {
          throw new InfraException('Tipo de unidade não encontrada.');
        }
    
        return $unidadeDTO;
      }

    public function gerarProcedimentoVinculoProcuracaoMotivoControlado($dados)
    {

        try {
            
            $objMdPetVincTpProcessoRN = new MdPetVincTpProcessoRN();
            $objMdPetVincTpProcessoDTO = new MdPetVincTpProcessoDTO();
            $objMdPetVincTpProcessoDTO->retTodos();
            $objMdPetVincTpProcessoDTO->setStrTipoVinculo($dados['tpVinc']);
            $arrObjMdPetVincTpProcesso = $objMdPetVincTpProcessoRN->consultar($objMdPetVincTpProcessoDTO);

            if (is_null($arrObjMdPetVincTpProcesso)) {
                throw new InfraException('Vinculação não configurada');
            }

            //obtendo a unidade de abertura do processo
            //Recuperando Vinculo já existente
            $objMdPetVincRepresentantDTO = new MdPetVincRepresentantDTO();
            $objMdPetVincRepresentantDTO->retDblIdProcedimentoVinculo();
            if($dados['tpVinc'] == "J" && $dados['tpProc'] == MdPetVincRepresentantRN::$PE_PROCURADOR_SIMPLES){
                $objMdPetVincRepresentantDTO->setNumIdContatoVinc($dados['hdnIdContatoVinc']);
                $objMdPetVincRepresentantDTO->setStrTpVinc("J");
            }else if($dados['tpVinc'] == "F" && $dados['tpProc'] == MdPetVincRepresentantRN::$PE_PROCURADOR_SIMPLES){
                $objMdPetVincRepresentantDTO->setNumIdContatoVinc($dados['hdnIdContatoVinc']);
                $objMdPetVincRepresentantDTO->setStrTpVinc("F");
            }
            $objMdPetVincRepresentantRN = new MdPetVincRepresentantRN();
            $objMdPetVincRepresentantRN = $objMdPetVincRepresentantRN->listar($objMdPetVincRepresentantDTO);
            $arrProcessos = InfraArray::converterArrInfraDTO($objMdPetVincRepresentantRN,'IdProcedimentoVinculo');
            
            if(count(array_unique($arrProcessos))){
                $objProtocoloDTO = new ProtocoloDTO();
                $objProtocoloDTO->setDblIdProtocolo($arrProcessos[0]);
                $objProtocoloDTO->retNumIdUnidadeGeradora();
                $objProtocoloRN = new ProtocoloRN();
                $objUnidadeGeradora = $objProtocoloRN->consultarRN0186($objProtocoloDTO);
                $idTipoUnidadeAberturaProcesso = $objUnidadeGeradora->getNumIdUnidadeGeradora();
            }else{
                $idTipoUnidadeAberturaProcesso = $arrObjMdPetVincTpProcesso->getNumIdUnidade();
            }

            //Obtendo tipo do processo
            $idTipoProcesso = $arrObjMdPetVincTpProcesso->getNumIdTipoProcedimento();

            $objTipoProcedimentoDTO = new TipoProcedimentoDTO();
            $objTipoProcedimentoDTO->retNumIdTipoProcedimento();
            $objTipoProcedimentoDTO->retStrNome();
            $objTipoProcedimentoDTO->setNumIdTipoProcedimento($idTipoProcesso);

            $objTipoProcedimentoRN = new TipoProcedimentoRN();
            $objTipoProcedimentoDTO = $objTipoProcedimentoRN->consultarRN0267($objTipoProcedimentoDTO);

            if ($objTipoProcedimentoDTO == null) {
                throw new InfraException('Tipo de processo não encontrado.');
            }

            //obter unidade configurada no "Tipo de Processo para peticionamento"
            $unidadeRN = new UnidadeRN();
            $unidadeDTO = new UnidadeDTO();
            $unidadeDTO->retTodos();
            $unidadeDTO->setNumIdUnidade($idTipoUnidadeAberturaProcesso);
            $unidadeDTO = $unidadeRN->consultarRN0125($unidadeDTO);

            if (is_null($unidadeDTO)) {
                throw new InfraException('Tipo de unidade não encontrada.');
            }

            //dados do procurador e Pj
            $objMdPetVincRepresentantRN = new MdPetVincRepresentantRN();
            $objMdPetVincRepresentantDTO = new MdPetVincRepresentantDTO();
            $objMdPetVincRepresentantDTO->retStrNomeProcurador();
            $objMdPetVincRepresentantDTO->retNumIdContatoVinc();
            $objMdPetVincRepresentantDTO->retNumIdContatoProcurador();
            $objMdPetVincRepresentantDTO->retStrCpfProcurador();
            $objMdPetVincRepresentantDTO->retStrRazaoSocialNomeVinc();
            $objMdPetVincRepresentantDTO->retStrCNPJ();
            $objMdPetVincRepresentantDTO->retNumIdMdPetVinculoRepresent();
            $objMdPetVincRepresentantDTO->retNumIdContatoOutorg();
            $objMdPetVincRepresentantDTO->setNumIdMdPetVinculoRepresent($dados['hdnIdVinculacao']);
            $objMdPetVincRepresentantDTO->retNumIdMdPetVinculo();

            $objMdPetVincRepresentantDTO = $objMdPetVincRepresentantRN->consultar($objMdPetVincRepresentantDTO);

            // Dados do Responsável legal
            $contatoRN = new ContatoRN();
            $contatoDTO = new ContatoDTO();
            $contatoDTO->setNumIdContato($objMdPetVincRepresentantDTO->getNumIdContatoOutorg());
            $contatoDTO->retStrNome();
            $contatoDTO->retDblCpf();
            $contatoDTO->retNumIdContato();
            $arrContato = $contatoRN->consultarRN0324($contatoDTO);

            $dados['NomeOutorgante']= $arrContato->getStrNome();
            $dados['CpfOutorgante']= $arrContato->getDblCpf();
            $dados['IdOutorgante']= $arrContato->getNumIdContato();


            SessaoSEI::getInstance()->simularLogin(null, null, SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno(), $unidadeDTO->getNumIdUnidade());

            // Recupera o idProcedimento(Processo) referente ao cnpj informado (idContato)

            $objProcedimentoDTO = new ProcedimentoDTO();
            $objProcedimentoRN = new ProcedimentoRN();
            $objProcedimentoDTO->setDblIdProcedimento($dados['hdnIdProcedimento']);
            $objProcedimentoDTO->retDblIdProcedimento();
            $objProcedimentoDTO->retStrProtocoloProcedimentoFormatado();

            $objProcedimentoDTO = $objProcedimentoRN->consultarRN0201($objProcedimentoDTO);

            $idProcedimento = $objProcedimentoDTO->getDblIdProcedimento();

            //gerar modelo de Revogação
            $objMdPetVincRepresentantDTO = $this->_gerarModeloDesvinculoProcuracao($dados, $objProcedimentoDTO, $objMdPetVincRepresentantDTO, $unidadeDTO);

            return true;

        } catch (Exception $e) {
            throw new InfraException('Erro cadastrando processo peticionamento do SEI.', $e);
        }

    }

    /*
     * gerar modelo de Revogação
     */

    private function _gerarModeloDesvinculoProcuracao($dados, $objProcedimentoDTO, $objMdPetVincRepresentantDTO, $objUnidadeDTO){
        
        $idProcedimento = $objProcedimentoDTO->getDblIdProcedimento();
        //$idResponsavelLegal = $dados['IdOutorgante'];
        $idOutorgado = $objMdPetVincRepresentantDTO->getNumIdContatoProcurador();
        $idMdPetVinculoRepresent = $objMdPetVincRepresentantDTO->getNumIdMdPetVinculoRepresent();
        $idVinculo = $objMdPetVincRepresentantDTO->getNumIdMdPetVinculo();
        $idContatoVinculo = $objMdPetVincRepresentantDTO->getNumIdContatoVinc();
        
        $objInfraParametro= new InfraParametro($this->getObjInfraIBanco());
        
        if($dados['tpProc'] == "E"){
        if($dados['hdnTpDocumento']=='renunciar') {
            $url = dirname(__FILE__) . '/../md_pet_vinc_modelo_renuncia.php';
            //$tipoPeticionamento = MdPetAcessoExternoRN::$MD_PET_PROCURACAO_RENUCIA;
            $tipoAto = MdPetVincDocumentoRN::$TP_ATO_RENUNCIA;
            $tipoPeticionamento = MdPetReciboRN::$TP_RECIBO_PROCURACAO_ELETRONICA_RENUNCIA;
            $tipoRecibo = MdPetReciboRN::$TP_RECIBO_PROCURACAO_ELETRONICA_RENUNCIA;

        }else {
            $url = dirname(__FILE__) . '/../md_pet_vinc_modelo_revogacao.php';
            //$tipoPeticionamento = MdPetAcessoExternoRN::$MD_PET_PROCURACAO_REVOGACAO;
            $tipoAto = MdPetVincDocumentoRN::$TP_ATO_REVOGACAO;
            $tipoPeticionamento = MdPetReciboRN::$TP_RECIBO_PROCURACAO_ELETRONICA_REVOGACAO;
            $tipoRecibo = MdPetReciboRN::$TP_RECIBO_PROCURACAO_ELETRONICA_REVOGACAO;
        }

        }
        //Revogar procuração simples
        if($dados['hdnTpDocumento']=='revogar' || $dados['hdnTpDocumento']=='renunciar') {
            if($dados['tpProc'] == MdPetVincRepresentantRN::$PE_PROCURADOR_SIMPLES && $dados['tpVinc'] == "J"){
                //Caso seja procuração simples com outorgante PJ
                if($dados['hdnTpDocumento']=='revogar'){
                $url = dirname(__FILE__) . '/../md_pet_vinc_modelo_revogacao_pessoa_juridica.php';
                }else if($dados['hdnTpDocumento']=='renunciar'){
                $url = dirname(__FILE__) . '/../md_pet_vinc_modelo_renuncia_pessoa_juridica.php';
                }

                if($dados['hdnTpDocumento']=='revogar'){

                    $tipoAto = MdPetVincDocumentoRN::$TP_ATO_REVOGACAO;
                    $tipoPeticionamento = MdPetReciboRN::$TP_RECIBO_PROCURACAO_ELETRONICA_REVOGACAO;
                    $tipoRecibo = MdPetReciboRN::$TP_RECIBO_PROCURACAO_ELETRONICA_REVOGACAO;


                }else if($dados['hdnTpDocumento']=='renunciar'){

                    $tipoAto = MdPetVincDocumentoRN::$TP_ATO_RENUNCIA;
                    $tipoPeticionamento = MdPetReciboRN::$TP_RECIBO_PROCURACAO_ELETRONICA_RENUNCIA;
                    $tipoRecibo = MdPetReciboRN::$TP_RECIBO_PROCURACAO_ELETRONICA_RENUNCIA;

                }


                $objMdPetVincRepresentantOutorganteRN = new MdPetVincRepresentantRN();
                $objMdPetVincRepresentantOutorganteDTO = new MdPetVincRepresentantDTO();
                $objMdPetVincRepresentantOutorganteDTO->setNumIdMdPetVinculoRepresent($idMdPetVinculoRepresent);
                $objMdPetVincRepresentantOutorganteDTO->retNumIdMdPetVinculo();
                $objMdPetVincRepresentantOutorganteDTO->retNumIdContato();
                $objMdPetVincRepresentantOutorganteDTO->retNumIdContatoOutorg();
                $objMdPetVincRepresentantOutorganteDTO->setStrTipoRepresentante(array('L'),InfraDTO::$OPER_NOT_IN);
                $objMdPetVincRepresentantOutorganteRN =  $objMdPetVincRepresentantOutorganteRN->consultar($objMdPetVincRepresentantOutorganteDTO);

                //Recuperando responsave legal
                //Recuperando Pessoa Jurídica Outorgante
                $contatoDTO = new ContatoDTO();
                $contatoDTO->setNumIdContato($objMdPetVincRepresentantOutorganteRN->getNumIdContatoOutorg());
                $contatoDTO->retStrNome();
                $contatoDTO->retNumIdContato();
                $contatoDTO->retDblCnpj();
                $contatoRN = new ContatoRN();
                $objContatoRN = $contatoRN->consultarRN0324($contatoDTO);
                $nomeResponsavelLegal = $objContatoRN->getStrNome();
                
            
                $objMdPetVinculoDTO = new MdPetVinculoDTO();
                $objMdPetVinculoDTO->setNumIdMdPetVinculo($objMdPetVincRepresentantOutorganteRN->getNumIdMdPetVinculo());
                $objMdPetVinculoDTO->retNumIdContato();
                $objMdPetVinculoRN = new MdPetVinculoRN();
                $objMdPetVinculoRN = $objMdPetVinculoRN->consultar($objMdPetVinculoDTO);


                //Recuperando Pessoa Jurídica Outorgante
                $contatoDTO = new ContatoDTO();
                $contatoDTO->setNumIdContato($objMdPetVinculoRN->getNumIdContato());
                $contatoDTO->retStrNome();
                $contatoDTO->retNumIdContato();
                $contatoDTO->retDblCnpj();
                $contatoRN = new ContatoRN();
                $objContatoRN = $contatoRN->consultarRN0324($contatoDTO);
                $nomeOutorgante = $objContatoRN->getStrNome();
                $cnpjOutorgante = $objContatoRN->getDblCnpj();

                //Recuperando Outorgado
                $contatoDTO = new ContatoDTO();
                $contatoDTO->setNumIdContato($objMdPetVincRepresentantOutorganteRN->getNumIdContato());
                $contatoDTO->retStrNome();
                $contatoDTO->retNumIdContato();
                $contatoDTO->retDblCnpj();
                $contatoRN = new ContatoRN();
                $objContatoRN = $contatoRN->consultarRN0324($contatoDTO);
                $nomeOutorgado = $objContatoRN->getStrNome();
            }
            
            if($dados['tpProc'] == MdPetVincRepresentantRN::$PE_PROCURADOR_SIMPLES && $dados['tpVinc'] == "F"){
                //Caso seja procuração simples com outorgante PJ
                if($dados['hdnTpDocumento']=='revogar'){
                    $url = dirname(__FILE__) . '/../md_pet_vinc_modelo_revogacao_pessoa_fisica.php';
                    }else if($dados['hdnTpDocumento']=='renunciar'){
                    $url = dirname(__FILE__) . '/../md_pet_vinc_modelo_renuncia_pessoa_fisica.php';
                    }

                if($dados['hdnTpDocumento']=='revogar'){

                        $tipoAto = MdPetVincDocumentoRN::$TP_ATO_REVOGACAO;
                        $tipoPeticionamento = MdPetReciboRN::$TP_RECIBO_PROCURACAO_ELETRONICA_REVOGACAO;
                        $tipoRecibo = MdPetReciboRN::$TP_RECIBO_PROCURACAO_ELETRONICA_REVOGACAO;
    
                    }else if($dados['hdnTpDocumento']=='renunciar'){
    
                        $tipoAto = MdPetVincDocumentoRN::$TP_ATO_RENUNCIA;
                        $tipoPeticionamento = MdPetReciboRN::$TP_RECIBO_PROCURACAO_ELETRONICA_RENUNCIA;
                        $tipoRecibo = MdPetReciboRN::$TP_RECIBO_PROCURACAO_ELETRONICA_RENUNCIA;
    
                    }

                $objMdPetVincRepresentantOutorganteRN = new MdPetVincRepresentantRN();
                $objMdPetVincRepresentantOutorganteDTO = new MdPetVincRepresentantDTO();
                $objMdPetVincRepresentantOutorganteDTO->setNumIdMdPetVinculoRepresent($idMdPetVinculoRepresent);
                $objMdPetVincRepresentantOutorganteDTO->retNumIdMdPetVinculo();
                $objMdPetVincRepresentantOutorganteDTO->retNumIdContato();
                $objMdPetVincRepresentantOutorganteDTO->setStrTipoRepresentante(array('L'),InfraDTO::$OPER_NOT_IN);
                $objMdPetVincRepresentantOutorganteRN =  $objMdPetVincRepresentantOutorganteRN->consultar($objMdPetVincRepresentantOutorganteDTO);

            
                $objMdPetVinculoDTO = new MdPetVinculoDTO();
                $objMdPetVinculoDTO->setNumIdMdPetVinculo($objMdPetVincRepresentantOutorganteRN->getNumIdMdPetVinculo());
                $objMdPetVinculoDTO->retNumIdContato();
                $objMdPetVinculoRN = new MdPetVinculoRN();
                $objMdPetVinculoRN = $objMdPetVinculoRN->consultar($objMdPetVinculoDTO);

                
                //Recuperando Pessoa Física Outorgante
                $contatoDTO = new ContatoDTO();
                $contatoDTO->setNumIdContato($objMdPetVinculoRN->getNumIdContato());
                $contatoDTO->retStrNome();
                $contatoDTO->retNumIdContato();
                $contatoDTO->retDblCnpj();
                $contatoRN = new ContatoRN();
                $objContatoRN = $contatoRN->consultarRN0324($contatoDTO);
                $nomeOutorgante = $objContatoRN->getStrNome();

                //Outorgado
                $contatoDTO = new ContatoDTO();
                $contatoDTO->setNumIdContato($objMdPetVincRepresentantOutorganteRN->getNumIdContato());
                $contatoDTO->retStrNome();
                $contatoDTO->retNumIdContato();
                $contatoDTO->retDblCnpj();
                $contatoRN = new ContatoRN();
                $objContatoRN = $contatoRN->consultarRN0324($contatoDTO);
                $nomeOutorgado = $objContatoRN->getStrNome();

            }
        }

            //Procuração Dados
            $docRN = new DocumentoRN();
            $parObjDocumentoDTO = new DocumentoDTO();
            $parObjDocumentoDTO->retStrProtocoloProcedimentoFormatado();
            $parObjDocumentoDTO->retStrProtocoloDocumentoFormatado();
            $parObjDocumentoDTO->retStrNomeSerie();
            $parObjDocumentoDTO->setDblIdDocumento($dados['hdnIdProcuracao']);
            $parObjDocumentoDTO->setNumMaxRegistrosRetorno(1);
            $parObjDocumentoDTO = $docRN->consultarRN0005($parObjDocumentoDTO);


            $numProcuracao='';
            $numProcesso='';
            if (count($parObjDocumentoDTO)>0){
                  $tipoProcuracao = $parObjDocumentoDTO->getStrNomeSerie();
                  $numProcuracao = $parObjDocumentoDTO->getStrProtocoloDocumentoFormatado();
                  $numProcesso = $parObjDocumentoDTO->getStrProtocoloProcedimentoFormatado();
                }
 
        $htmlModeloRevogacao = file_get_contents($url);
        $dataAtual = InfraData::getStrDataAtual();
        $horaAtual = InfraData::getStrHoraAtual();
        $dataAtual .= '  '.$horaAtual;


        //consultar orgão
        $idOrgao = SessaoSEIExterna::getInstance()->getNumIdOrgaoUsuarioExterno();
        $orgaoDTO = new  OrgaoDTO();
        $orgaoRN = new  OrgaoRN();
        $orgaoDTO->setNumIdOrgao($idOrgao);
        $orgaoDTO->retNumIdOrgao();
        $orgaoDTO->retStrSigla();
        $orgaoDTO->retStrDescricao();
        $orgao = $orgaoRN->consultarRN1352($orgaoDTO);

        // consultar dados Outorgado
        $contatoDTO = new ContatoDTO();
        $contatoDTO->setDblCpf($dados['CpfOutorgante']);
        $contatoDTO->retStrNome();
        $contatoDTO->retNumIdContato();
        $contatoDTO->retDblCpf();
        //$contatoDTO->setNumIdContato($idOutorgado);
        $contatoDTO->setNumMaxRegistrosRetorno(1);

        $contatoRN = new ContatoRN();
        $Outorgante = $contatoRN->consultarRN0324($contatoDTO);
        if($dados['tpProc'] == "E"){
        $htmlModeloRevogacao = str_replace('@razaoSocial',$objMdPetVincRepresentantDTO->getStrRazaoSocialNomeVinc(),$htmlModeloRevogacao);
        $htmlModeloRevogacao = str_replace('@cnpj',InfraUtil::formatarCnpj($objMdPetVincRepresentantDTO->getStrCNPJ()),$htmlModeloRevogacao);
        $htmlModeloRevogacao = str_replace('@nomeOutorgante',$Outorgante->getStrNome(),$htmlModeloRevogacao);
        $htmlModeloRevogacao = str_replace('@cpfOutorgante',InfraUtil::formatarCpf($Outorgante->getDblCpf()),$htmlModeloRevogacao);
        $htmlModeloRevogacao = str_replace('@nomeOutorgado',$objMdPetVincRepresentantDTO->getStrNomeProcurador(),$htmlModeloRevogacao);
        $htmlModeloRevogacao = str_replace('@cpfOutorgado',InfraUtil::formatarCpf($objMdPetVincRepresentantDTO->getStrCpfProcurador()),$htmlModeloRevogacao);
        $htmlModeloRevogacao = str_replace('@motivo',$dados['txtJustificativa'],$htmlModeloRevogacao);
        $htmlModeloRevogacao = str_replace('@data',$dataAtual,$htmlModeloRevogacao);
        $htmlModeloRevogacao = str_replace('@descricao_orgao@',$orgao->getStrDescricao(),$htmlModeloRevogacao);
        $htmlModeloRevogacao = str_replace('@tipoProcuracao',$tipoProcuracao,$htmlModeloRevogacao);
        $htmlModeloRevogacao = str_replace('@numProcuracao',$numProcuracao,$htmlModeloRevogacao);
        //$htmlModeloRevogacao = str_replace('@numDoc',$numProcuracao,$htmlModeloRevogacao);
        $htmlModeloRevogacao = str_replace('@numProcesso',$numProcesso,$htmlModeloRevogacao);

        }else if($dados['tpProc'] == MdPetVincRepresentantRN::$PE_PROCURADOR_SIMPLES && $dados['tpVinc'] == "J"){
            $htmlModeloRevogacao = str_replace('@motivo',$dados['txtJustificativa'],$htmlModeloRevogacao);
            $htmlModeloRevogacao = str_replace('@Outorgante',$nomeOutorgante,$htmlModeloRevogacao);
            $htmlModeloRevogacao = str_replace('@cnpj',InfraUtil::formatarCnpj($cnpjOutorgante),$htmlModeloRevogacao);
            $htmlModeloRevogacao = str_replace('@outorgado',$nomeOutorgado,$htmlModeloRevogacao);
            $htmlModeloRevogacao = str_replace('@respLegal',$nomeResponsavelLegal,$htmlModeloRevogacao);
            $htmlModeloRevogacao = str_replace('@numProcesso',$numProcesso,$htmlModeloRevogacao);
            $htmlModeloRevogacao = str_replace('@descricao_orgao@',$orgao->getStrDescricao(),$htmlModeloRevogacao);
        }else if($dados['tpProc'] == MdPetVincRepresentantRN::$PE_PROCURADOR_SIMPLES && $dados['tpVinc'] == "F"){
            $htmlModeloRevogacao = str_replace('@motivo',$dados['txtJustificativa'],$htmlModeloRevogacao);
            $htmlModeloRevogacao = str_replace('@Outorgante',$nomeOutorgante,$htmlModeloRevogacao);
            $htmlModeloRevogacao = str_replace('@outorgado',$nomeOutorgado,$htmlModeloRevogacao);
            $htmlModeloRevogacao = str_replace('@numProcesso',$numProcesso,$htmlModeloRevogacao);
            $htmlModeloRevogacao = str_replace('@descricao_orgao@',$orgao->getStrDescricao(),$htmlModeloRevogacao);
        }
        //==========================================================================
        //incluindo doc recibo no processo via SEIRN
        //===========================================================
        if($dados['hdnTpDocumento']=='renunciar')
            $idSerieFormulario = $objInfraParametro->getValor(MdPetIntSerieRN::$MD_PET_ID_SERIE_RENUNCIA);
        else
            $idSerieFormulario = $objInfraParametro->getValor(MdPetIntSerieRN::$MD_PET_ID_SERIE_REVOGACAO);

        
        
        if($dados['tpProc'] == MdPetVincRepresentantRN::$PE_PROCURADOR_SIMPLES && $dados['tpVinc'] == "J"){

        $htmlModeloRevogacao = str_replace('@tipoProcuracao',"Procuração Eletrônica",$htmlModeloRevogacao);
        $htmlModeloRevogacao = str_replace('@numProcuracao',$numProcuracao,$htmlModeloRevogacao);
        $htmlModeloRevogacao = str_replace('@numProcesso',$numProcesso,$htmlModeloRevogacao);    
        }else if($dados['tpProc'] == MdPetVincRepresentantRN::$PE_PROCURADOR_SIMPLES && $dados['tpVinc'] == "F"){

        $htmlModeloRevogacao = str_replace('@tipoProcuracao',"Procuração Eletrônica",$htmlModeloRevogacao);
        $htmlModeloRevogacao = str_replace('@numProcuracao',$numProcuracao,$htmlModeloRevogacao);
        $htmlModeloRevogacao = str_replace('@numProcesso',$numProcesso,$htmlModeloRevogacao);
        }else if($dados['tpProc'] == "E"){
        $htmlModeloRevogacao = str_replace('@tipoProcuracao',"Procuração Especial",$htmlModeloRevogacao);
        $htmlModeloRevogacao = str_replace('@numProcuracao',$numProcuracao,$htmlModeloRevogacao);
        $htmlModeloRevogacao = str_replace('@numProcesso',$numProcesso,$htmlModeloRevogacao);
        }
        
        //Incluindo documento
        $objDocumentoAPI = new DocumentoAPI();
        $objDocumentoAPI->setIdProcedimento($objProcedimentoDTO->getDblIdProcedimento());
        $objDocumentoAPI->setTipo(ProtocoloRN::$TP_DOCUMENTO_GERADO);
        $objDocumentoAPI->setSubTipo(DocumentoRN::$TD_EDITOR_INTERNO /*DocumentoRN::$TD_FORMULARIO_AUTOMATICO*/);
        //        $objDocumentoAPI->setSinAssinado('S');
        //        $objDocumentoAPI->setSinBloqueado('S');
        $objDocumentoAPI->setIdSerie($idSerieFormulario);
        //        $objDocumentoAPI->setIdHipoteseLegal(null);
        //        $objDocumentoAPI->setNivelAcesso(ProtocoloRN::$NA_PUBLICO);
        //        $objDocumentoAPI->setIdTipoConferencia(null);
        //        $objDocumentoAPI->setConteudo(null);
        
        $objDocumentoAPI->setTipo(ProtocoloRN::$TP_DOCUMENTO_GERADO);
        $objDocumentoAPI->setSubTipo(DocumentoRN::$TD_EDITOR_INTERNO /*DocumentoRN::$TD_FORMULARIO_AUTOMATICO*/);
        
        $objSeiRN = new SeiRN();
        $saidaDocExternoAPI = $objSeiRN->incluirDocumento($objDocumentoAPI);

        $participanteRN = new ParticipanteRN();
        $objParticipante = new ParticipanteDTO();
        $objParticipante->setDblIdProtocolo($saidaDocExternoAPI->getIdDocumento());
        $objParticipante->setNumIdContato($objMdPetVincRepresentantDTO->getNumIdContatoVinc());
        $objParticipante->setNumIdUnidade($objUnidadeDTO->getNumIdUnidade());
        $objParticipante->setStrStaParticipacao(ParticipanteRN::$TP_INTERESSADO);
        $objParticipante->setNumSequencia(0);      
        $participanteRN->cadastrarRN0170($objParticipante);

        // Alterando Valores do Documento //
        $docRN = new DocumentoRN();
        $parObjDocumentoDTO = new DocumentoDTO();
        $parObjDocumentoDTO->retStrProtocoloProcedimentoFormatado();
        $parObjDocumentoDTO->retStrProtocoloDocumentoFormatado();
        $parObjDocumentoDTO->retStrStaDocumento();
        $parObjDocumentoDTO->setDblIdDocumento($saidaDocExternoAPI->getIdDocumento());
        $parObjDocumentoDTO->retDblIdDocumento();
        $parObjDocumentoDTO = $docRN->consultarRN0005($parObjDocumentoDTO);

        //$parObjDocumentoDTO->setStrStaDocumento(DocumentoRN::$TD_FORMULARIO_AUTOMATICO);
        $parObjDocumentoDTO->setStrStaDocumento(DocumentoRN::$TD_EDITOR_INTERNO);

        $objDocumentoBD = new DocumentoBD($this->getObjInfraIBanco());
        $objDocumentoBD->alterar($parObjDocumentoDTO);


        //Alterando Conteudo do Documento com as Informções atualizadas
        //$parObjDocumentoConteudoDTO = new DocumentoConteudoDTO();
        //$parObjDocumentoConteudoDTO->setStrConteudo($htmlModeloRevogacao);
        //$parObjDocumentoConteudoDTO->setDblIdDocumento($saidaDocExternoAPI->getIdDocumento());

        //$objDocumentoConteudoBD = new DocumentoConteudoBD($this->getObjInfraIBanco());
        //$objDocumentoConteudoBD->alterar($parObjDocumentoConteudoDTO);


        $objEditorDTO = new EditorDTO();

        $objEditorDTO->setDblIdDocumento($saidaDocExternoAPI->getIdDocumento());
        $objEditorDTO->setNumIdBaseConhecimento(null);
        $objEditorDTO->setNumVersao(1);
        $objEditorDTO->setStrSinIgnorarNovaVersao('S');

        $objSecaoDocumentoDTO = new SecaoDocumentoDTO();
        $objSecaoDocumentoDTO->retNumIdSecaoModelo();
        $objSecaoDocumentoDTO->retStrNomeSecaoModelo();
        $objSecaoDocumentoDTO->setDblIdDocumento($saidaDocExternoAPI->getIdDocumento());
        //    $objSecaoDocumentoDTO->setNumIdBaseConhecimento($parObjEditorDTO->getNumIdBaseConhecimento());
        $objSecaoDocumentoDTO->setStrSinAssinatura('N');

        $objSecaoDocumentoRN = new SecaoDocumentoRN();
        $arrObjSecaoDocumentoDTOBanco = $objSecaoDocumentoRN->listar($objSecaoDocumentoDTO);

        if (count($arrObjSecaoDocumentoDTOBanco)){

            $arrObjSecaoDocumentoDTO = array();

            foreach($arrObjSecaoDocumentoDTOBanco as $item){
                $objSecaoDocumentoDTO = new SecaoDocumentoDTO();
                $objSecaoDocumentoDTO->setNumIdSecaoModelo($item->getNumIdSecaoModelo());

                if ($item->getStrNomeSecaoModelo()=='Corpo do Texto'){
                    $objSecaoDocumentoDTO->setStrConteudo($htmlModeloRevogacao);
                }
                $arrObjSecaoDocumentoDTO[] = $objSecaoDocumentoDTO;
            }

            $objEditorDTO->setArrObjSecaoDocumentoDTO($arrObjSecaoDocumentoDTO);

            try{
                $objEditorRN = new EditorRN();
                $numVersao = $objEditorRN->adicionarVersao($objEditorDTO);
            }catch(Exception $e){
                if ($e instanceof InfraException && $e->contemValidacoes()){
                    die("INFRA_VALIDACAO\n".$e->__toString()); //retorna para o iframe exibir o alert
                }

                PaginaSEI::getInstance()->processarExcecao($e); //vai para a página de erro padrão
            }

        }

        // Alterando Valores do Documento //
//        $docRN = new DocumentoRN();
//        $parObjDocumentoDTO = new DocumentoDTO();
//        $parObjDocumentoDTO->retStrProtocoloProcedimentoFormatado();
//        $parObjDocumentoDTO->retStrProtocoloDocumentoFormatado();
//        $parObjDocumentoDTO->retStrStaDocumento();
//        $parObjDocumentoDTO->setDblIdDocumento($saidaDocExternoAPI->getIdDocumento());
//        $parObjDocumentoDTO->retDblIdDocumento();
//        $parObjDocumentoDTO = $docRN->consultarRN0005($parObjDocumentoDTO);

////        $parObjDocumentoDTO->setStrStaDocumento(DocumentoRN::$TD_FORMULARIO_AUTOMATICO);

        //RECUPERANDO DADOS PARA ASSINATURA

        $mdPetProcessoRN = new mdPetProcessoRN();
        $mdPetProcessoRN->assinarETravarDocumentoProcesso($objUnidadeDTO, $dados, $parObjDocumentoDTO, $objProcedimentoDTO);

        $dadosRetornoDesvinculo['RepLegal'] = $Outorgante;
        $dadosRetornoDesvinculo['Procuracao'] = $objMdPetVincRepresentantDTO;
        $dadosRetornoDesvinculo['Documento'] = $saidaDocExternoAPI;
       
        $tpProtocolo = $tipoAto;
        $this->_adicionarDadosArquivoVinculacao($saidaDocExternoAPI->getIdDocumento(), $idMdPetVinculoRepresent, $tpProtocolo);
        $objMdPetVincRepresentantDTO = $this->_encerramentoProcuracaoEspecial($idMdPetVinculoRepresent,$dados);
        
        $objMdPetVinculoUsuExtRN = new MdPetVinculoUsuExtRN();
        
        $reciboDTOBasico = $objMdPetVinculoUsuExtRN->salvarDadosReciboPeticionamento(array('idProcedimento' => $idProcedimento, 'staTipoPeticionamento' =>$tipoRecibo));
        

        $idMdPetVinculoRepresent = $objMdPetVincRepresentantDTO->getNumIdMdPetVinculoRepresent();

        $recibo = $this->gerarReciboDesvinculo(array($dadosRetornoDesvinculo, $objProcedimentoDTO, $reciboDTOBasico, $idMdPetVinculoRepresent,$tipoRecibo, $idContatoVinculo,$objUnidadeDTO));
        $idDocumentoRecibo = $reciboDTOBasico->getDblIdDocumento();

     
        if($dados['tpVinc'] == "F"){
            $tipoPessoa = MdPetVincTpProcessoRN::$ID_FIXO_MD_PET_VINCULO_USU_EXT_PF;
        }else if($dados['tpVinc'] == "J"){
            $tipoPessoa = MdPetVincTpProcessoRN::$ID_FIXO_MD_PET_VINCULO_USU_EXT;
        }

        
        $mdPetVinculoUsuExtRN = new MdPetVinculoUsuExtRN();
        $objMdPetIntimacaoRN = new MdPetIntimacaoRN();
        $objProcedimentoDTO = $mdPetVinculoUsuExtRN->getProcedimento($idProcedimento);
        $objUnidadeDTO = $mdPetVinculoUsuExtRN->getUnidade($tipoPessoa);
        $arrParams = array();
        $arrParams[0] = $dados;
        $arrParams[1] = $objUnidadeDTO;
        $arrParams[2] = $objProcedimentoDTO;
        $arrParams[3] = array();
        $arrParams[4] = $reciboDTOBasico;
        $arrParams[5] = $reciboDTOBasico;

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


        $mdPetVinculoUsuExtRN->enviarEmail($arrParams);


        $objMdRegrasGeraisRN = new MdPetRegrasGeraisRN();
        $strTipoPeticionamento =  $objMdRegrasGeraisRN->getTipoPeticionamento($tipoPeticionamento, true);

        $objMdPetVinculoUsuExtRN->gerarAndamentoVinculo(array($idProcedimento, $strTipoPeticionamento, $idDocumentoRecibo, $objUnidadeDTO->getNumIdUnidade()));

        //Disponibilazando Acesso Externo ao outros representantes
        $objMdPetAcessoExternoRN = new MdPetAcessoExternoRN();
        $objMdPetAcessoExternoRN->gerarAcessoExternoVinculo(array($idVinculo,$objProcedimentoDTO->getDblIdProcedimento()));
        
        $objAtividadeRN = new AtividadeRN();
        $objAtividadeRN->gerarInternaRN0727($objAtividadeDTO);
        return $objMdPetVincRepresentantDTO;
    }

     /*
     * gerar modelo Procuração Simples
     */

    protected function gerarFormularioProcuracaoSimplesControlado($params){
   
        if(!(boolean)$params['dados']['selTipoProcuracao']){
            $params['dados']['selTipoProcuracao'] = MdPetVincRepresentantRN::$PE_PROCURADOR_SIMPLES;
        }
        
        $objProcedimentoDTO = $params['procedimento'];

        $idProcedimento = $objProcedimentoDTO->getDblIdProcedimento();
        $isProcuracao       = false;

            //Caso o outorgante seja pj
            if($params['dados']['hdnOutorgante'] == "PJ"){
                if($params['dados']['selTipoProcuracao'] == 'S') {
                    $idContatoVinc      = $params['dados']['hdnSelPJSimples'];
                } else {
                    $idContatoVinc      = $params['dados']['selPessoaJuridica'];
                }
            }
            //caso o outorgante seja pf
            else{
            $idContatoVinc      = $params['dados']['idContatoExterno']; 
            }
            $id = $params['dados']['idContatoExterno'];
            //Id do Outorgado
            $idOutorgado        = $params['dados']['hdnIdUsuario'];
            //Fim Id Outorgado
            $idVinculo          = $params['idVinculo'];
            $Replegal           = $params['RepLegal'];
            $contatoPj          = $params['contatoPj'];

            if($params['dados']['hdnOutorgante'] == "PF"){
            $idContato          = $params['dados']['idContatoExterno'];
            }else{
            $idContato          = $params['dados']['idContato'];
            }
            $unidadeDTO         = $params['unidadeDTO'];
            $isProcuracao       = true;
            
        
        $objInfraParametro = new InfraParametro($this->getObjInfraIBanco());

        if($params['dados']['selTipoProcuracao'] == MdPetVincRepresentantRN::$PE_PROCURADOR_SIMPLES && $params['dados']['hdnOutorgante'] == "PF" ){

            $url = dirname(__FILE__) . '/../md_pet_vinc_modelo_procuracao_simples_pessoa_fisica.php';

        }else if($params['dados']['selTipoProcuracao'] == MdPetVincRepresentantRN::$PE_PROCURADOR_SIMPLES && $params['dados']['hdnOutorgante'] == "PJ" ){

            $url = dirname(__FILE__) . '/../md_pet_vinc_modelo_procuracao_simples_pessoa_juridica.php';

        }
        $htmlModeloProcuracao = file_get_contents($url);
       
        
            // consultar dados Pj
            
            $contatoDTO = new ContatoDTO();
            $contatoDTO->retDblCnpj();
            $contatoDTO->retStrNome();
            $contatoDTO->setNumIdContato($idContatoVinc);

            $contatoRN = new ContatoRN();
            $contatoPjPf = $contatoRN->consultarRN0324($contatoDTO);

            // consultar dados Responsável Legal
            $contatoDTO = new ContatoDTO();
            $contatoDTO->retDblCpf();
            $contatoDTO->retStrNome();
            $contatoDTO->setNumIdContato($id);

            $contatoRN = new ContatoRN();
            $Replegal = $contatoRN->consultarRN0324($contatoDTO);
        
        // consultar dados Outorgado
        $contatoDTO = new ContatoDTO();
        $contatoDTO->retDblCpf();
        $contatoDTO->retStrNome();
        $contatoDTO->retNumIdContato();
        $contatoDTO->setNumMaxRegistrosRetorno(1);
        $contatoDTO->setNumIdContato($idOutorgado);

        $contatoRN = new ContatoRN();
        $Outorgante = $contatoRN->consultarRN0324($contatoDTO);
        $dadosProcuracacao['Outorgado']['dadosOutorgado'] = $Outorgante;

        // Recuperando data e Hora atual
        $dataAtual = InfraData::getStrDataAtual();
        $horaAtual = InfraData::getStrHoraAtual();
        $dataAtual .= '  '.$horaAtual;

        //consultar orgão
        $idOrgao = SessaoSEIExterna::getInstance()->getNumIdOrgaoUsuarioExterno();
        $orgaoDTO = new  OrgaoDTO();
        $orgaoRN = new  OrgaoRN();
        $orgaoDTO->setNumIdOrgao($idOrgao);
        $orgaoDTO->retNumIdOrgao();
        $orgaoDTO->retStrSigla();
        $orgaoDTO->retStrDescricao();
        $orgao = $orgaoRN->consultarRN1352($orgaoDTO);

        //Recuperando Tipo de Poderes

        $arrTipoPoderes = explode("-",$params['dados']['hdnTipoPoder']);
        $objMdPetTipoPoderDTO = new MdPetTipoPoderLegalDTO();
        $objMdPetTipoPoderDTO->setNumIdTipoPoderLegal($arrTipoPoderes,infraDTO::$OPER_IN);
        $objMdPetTipoPoderDTO->retStrNome();
        $objMdPetTipoPoderRN = new MdPetTipoPoderLegalRN();
        $arrObjMdPetTipoPoderRN = $objMdPetTipoPoderRN->listar($objMdPetTipoPoderDTO);
        $arrNome =  InfraArray::converterArrInfraDTO($arrObjMdPetTipoPoderRN,'Nome');

        $poder = '';
        $poder .='<ul style="padding-left:17px;margin:0">';
        foreach (array_unique($arrNome) as $poderes) {
        $poder .='<li><p style="margin: 0">'.$poderes.'</p></li>';
        }
        $poder .='</ul>';
        // Fim Tipo Poderes
       
        
        //Tabela de abrangência
        if($params['dados']['hdnTbProcessos'] != ""){
        $dadosProcessos = PaginaSEIExterna::getInstance()->getArrItensTabelaDinamica($params['dados']['hdnTbProcessos']);
        $tblAbrangencia = '';
        $tblAbrangencia .= '<ul style="margin-top: 0px">';
        foreach ($dadosProcessos as $key => $value) {
            $tblAbrangencia .='<li><p style="margin: 0" class="Texto_Justificado" >'.$value[1].'</p></li>';
        }
        $tblAbrangencia .= '</ul>';
      
        
    }

        //Verificando se o outorgante é PF ou PJ
        
        if($params['dados']['selTipoProcuracao'] == MdPetVincRepresentantRN::$PE_PROCURADOR_SIMPLES && $params['dados']['hdnOutorgante'] == "PJ" ){
        //Outorgante Pessoa Jurídica
        $htmlModeloProcuracao = str_replace('@RazaoSocial',$contatoPjPf->getStrNome(),$htmlModeloProcuracao);
        $htmlModeloProcuracao = str_replace('@Cnpj',InfraUtil::formatarCnpj($contatoPj->getDblCnpj()),$htmlModeloProcuracao);

        }else if($params['dados']['selTipoProcuracao'] == MdPetVincRepresentantRN::$PE_PROCURADOR_SIMPLES && $params['dados']['hdnOutorgante'] == "PF" ){
        //Outorgante Pessoa Física
        $htmlModeloProcuracao = str_replace('@pessoaFisica',$contatoPjPf->getStrNome(),$htmlModeloProcuracao);
        //$htmlModeloProcuracao = str_replace('@nomeOutorgado',InfraUtil::formatarCnpj($contatoPj->getDblCnpj()),$htmlModeloProcuracao);  

        }
        $htmlModeloProcuracao = str_replace('@nomeRespLegal',$Replegal->getStrNome(),$htmlModeloProcuracao);
        $htmlModeloProcuracao = str_replace('@nomeOutorgado',$Outorgante->getStrNome(),$htmlModeloProcuracao);
        $htmlModeloProcuracao = str_replace('@tpPoderes',$poder,$htmlModeloProcuracao);
        //Validade
        if($params['dados']['hdnValidade'] == ""){
        $htmlModeloProcuracao = str_replace('@validade',"Indeterminado",$htmlModeloProcuracao);
        }else{
        $htmlModeloProcuracao = str_replace('@validade',"Determinado (Data Limite: ".$params['dados']['hdnValidade'].")",$htmlModeloProcuracao);
        }
        //Abrangencia
        if($params['dados']['hdnTbProcessos'] == ""){
        $htmlModeloProcuracao = str_replace('@abrangencia',"Qualquer Processo em nome do Outorgante.",$htmlModeloProcuracao);
        $htmlModeloProcuracao = str_replace('@tabela_processos_procuracao',"",$htmlModeloProcuracao);  

        }else{
        $htmlModeloProcuracao = str_replace('@abrangencia',"Processos especificados abaixo:",$htmlModeloProcuracao);
        $htmlModeloProcuracao = str_replace('@tabela_processos_procuracao',$tblAbrangencia,$htmlModeloProcuracao);  
        }
        //$htmlModeloProcuracao = str_replace('@cpfOutorgado',InfraUtil::formatarCpf($Outorgante->getDblCpf()),$htmlModeloProcuracao);
        $htmlModeloProcuracao = str_replace('@sigla_orgao@',$orgao->getStrSigla(),$htmlModeloProcuracao);
        $htmlModeloProcuracao = str_replace('@descricao_orgao@',$orgao->getStrDescricao(),$htmlModeloProcuracao);
        
        //==========================================================================
        //incluindo doc recibo no processo via SEIRN
        //===========================================================
        //Definindo o Tipo de Documento a ser cadastrado atraves da constante abaixo
        $idSerieFormulario = $objInfraParametro->getValor(MdPetIntSerieRN::$MD_PET_ID_SERIE_PROCURACAOS);
        
        $objDocumentoAPI = new DocumentoAPI();
        $objDocumentoAPI->setIdProcedimento($objProcedimentoDTO->getDblIdProcedimento());
        $objDocumentoAPI->setTipo(ProtocoloRN::$TP_DOCUMENTO_GERADO);
        $objDocumentoAPI->setSubTipo(DocumentoRN::$TD_EDITOR_INTERNO /*DocumentoRN::$TD_FORMULARIO_AUTOMATICO*/);
//        $objDocumentoAPI->setSinAssinado('S');
//        $objDocumentoAPI->setSinBloqueado('S');
        $objDocumentoAPI->setIdSerie($idSerieFormulario);
//        $objDocumentoAPI->setIdHipoteseLegal(null);
//        $objDocumentoAPI->setNivelAcesso(ProtocoloRN::$NA_PUBLICO);
//        $objDocumentoAPI->setIdTipoConferencia(null);
//        $objDocumentoAPI->setConteudo(null);


        $objSeiRN = new SeiRN();
        $saidaDocExternoAPI = $objSeiRN->incluirDocumento($objDocumentoAPI);
      
        $participanteRN = new ParticipanteRN();
        $objParticipante = new ParticipanteDTO();
        $objParticipante->setDblIdProtocolo($saidaDocExternoAPI->getIdDocumento());
        $objParticipante->setNumIdContato($idContato);
        $objParticipante->setNumIdUnidade($unidadeDTO->getNumIdUnidade());
        $objParticipante->setStrStaParticipacao(ParticipanteRN::$TP_INTERESSADO);
        $objParticipante->setNumSequencia(0);      
        $participanteRN->cadastrarRN0170($objParticipante);
        $dadosProcuracacao['Outorgado']['Documento'] = $saidaDocExternoAPI;

        
        // Alterando Valores do Documento //
        $docRN = new DocumentoRN();
        $parObjDocumentoDTO = new DocumentoDTO();
        $parObjDocumentoDTO->retStrProtocoloProcedimentoFormatado();
        $parObjDocumentoDTO->retStrProtocoloDocumentoFormatado();
        $parObjDocumentoDTO->retStrStaDocumento();
        $parObjDocumentoDTO->setDblIdDocumento($saidaDocExternoAPI->getIdDocumento());
        $parObjDocumentoDTO->retDblIdDocumento();
        $parObjDocumentoDTO = $docRN->consultarRN0005($parObjDocumentoDTO);

        //Setando os parâmetros no Documento
        $htmlModeloProcuracao = str_replace('@numProcuracao',$parObjDocumentoDTO->getStrProtocoloDocumentoFormatado(),$htmlModeloProcuracao);
        $htmlModeloProcuracao = str_replace('@numDoc',$parObjDocumentoDTO->getStrProtocoloDocumentoFormatado(),$htmlModeloProcuracao);
        $htmlModeloProcuracao = str_replace('@numProcesso',$parObjDocumentoDTO->getStrProtocoloProcedimentoFormatado(),$htmlModeloProcuracao);
        
        //$parObjDocumentoDTO->setStrStaDocumento(DocumentoRN::$TD_FORMULARIO_AUTOMATICO);
        $parObjDocumentoDTO->setStrStaDocumento(DocumentoRN::$TD_EDITOR_INTERNO);

        $objDocumentoBD = new DocumentoBD($this->getObjInfraIBanco());
        $objDocumentoBD->alterar($parObjDocumentoDTO);
        
        //Alterando Conteudo do Documento com as Informções atualizadas
//        $parObjDocumentoConteudoDTO = new DocumentoConteudoDTO();
//        $parObjDocumentoConteudoDTO->setStrConteudo($htmlModeloProcuracao);
//        $parObjDocumentoConteudoDTO->setDblIdDocumento($saidaDocExternoAPI->getIdDocumento());

//        $objDocumentoConteudoBD = new DocumentoConteudoBD($this->getObjInfraIBanco());
//        $objDocumentoConteudoBD->alterar($parObjDocumentoConteudoDTO);

        $objEditorDTO = new EditorDTO();

        $objEditorDTO->setDblIdDocumento($saidaDocExternoAPI->getIdDocumento());
        $objEditorDTO->setNumIdBaseConhecimento(null);
        $objEditorDTO->setNumVersao(1);
        $objEditorDTO->setStrSinIgnorarNovaVersao('S');

        $objSecaoDocumentoDTO = new SecaoDocumentoDTO();
        $objSecaoDocumentoDTO->retNumIdSecaoModelo();
        $objSecaoDocumentoDTO->retStrNomeSecaoModelo();
        $objSecaoDocumentoDTO->setDblIdDocumento($saidaDocExternoAPI->getIdDocumento());
        //    $objSecaoDocumentoDTO->setNumIdBaseConhecimento($parObjEditorDTO->getNumIdBaseConhecimento());
        $objSecaoDocumentoDTO->setStrSinAssinatura('N');

        $objSecaoDocumentoRN = new SecaoDocumentoRN();
        $arrObjSecaoDocumentoDTOBanco = $objSecaoDocumentoRN->listar($objSecaoDocumentoDTO);
        
        if (count($arrObjSecaoDocumentoDTOBanco)){

            $arrObjSecaoDocumentoDTO = array();

            foreach($arrObjSecaoDocumentoDTOBanco as $item){
                $objSecaoDocumentoDTO = new SecaoDocumentoDTO();
                $objSecaoDocumentoDTO->setNumIdSecaoModelo($item->getNumIdSecaoModelo());

                if ($item->getStrNomeSecaoModelo()=='Corpo do Texto'){
                    $objSecaoDocumentoDTO->setStrConteudo($htmlModeloProcuracao);
                }
                $arrObjSecaoDocumentoDTO[] = $objSecaoDocumentoDTO;
            }

            $objEditorDTO->setArrObjSecaoDocumentoDTO($arrObjSecaoDocumentoDTO);
            
            try{
                $objEditorRN = new EditorRN();
                $numVersao = $objEditorRN->adicionarVersao($objEditorDTO);
            }catch(Exception $e){
                if ($e instanceof InfraException && $e->contemValidacoes()){
                    die("INFRA_VALIDACAO\n".$e->__toString()); //retorna para o iframe exibir o alert
                }

                PaginaSEI::getInstance()->processarExcecao($e); //vai para a página de erro padrão
            }
           
        }
        
        //RECUPERANDO DADOS PARA ASSINATURA
        
        $mdPetProcessoRN = new mdPetProcessoRN();
        $idUnidade = $unidadeDTO->getNumIdUnidade();
        //$dados['dados']['idProc']
       
        //Recuperando unidade origem
        $objProtocoloDTO = new ProtocoloDTO();
        $objProtocoloDTO->setDblIdProtocolo($params['idProc']);
        $objProtocoloDTO->retNumIdUnidadeGeradora();
        $objProtocoloRN = new ProtocoloRN();
        $objUnidadeGeradora = $objProtocoloRN->consultarRN0186($objProtocoloDTO);
        
        //Unidade Origem
        $objUnidadeDTO = new UnidadeDTO();
        $objUnidadeRN = new UnidadeRN();
        $objUnidadeDTO->setNumIdUnidade($objUnidadeGeradora->getNumIdUnidadeGeradora());
        $objUnidadeDTO->retTodos();

        $objUnidadeDTO= $objUnidadeRN->consultarRN0125($objUnidadeDTO);

        $mdPetProcessoRN->assinarETravarDocumentoProcesso($objUnidadeDTO, $params['dados'], $parObjDocumentoDTO, $objProcedimentoDTO);

       
        //Adicionar Procuracao especial caso tenha sido selecionado.
        $dados = array('IdOutorgado'=>$idOutorgado,
                       'idContatoExterno'=>$id,
                       'selTipoProcuracao'=>$params['dados']['selTipoProcuracao'],
                       'tabelaProcessos'=>$params['dados']['hdnTbProcessos'],
                       'validade'=>$params['dados']['hdnValidade']
                 );
        
        $idMdPetVinculoRepresent = $this->_adicionarProcuracaoEspecial($dados, $idVinculo ,$saidaDocExternoAPI->getIdDocumento());
        
        $tpDocumento = MdPetVincDocumentoRN::$TP_PROTOCOLO_PROCURACAO_ESPECIAL;
        $this->_adicionarDadosArquivoVinculacao($saidaDocExternoAPI->getIdDocumento(), $idMdPetVinculoRepresent, $tpDocumento);
                
        $dadosProcuracacao['idVinculo'] = $idVinculo;
        $dadosProcuracacao['IdMdPetVinculoRepresent']= $idMdPetVinculoRepresent;
        if($isProcuracao) {
            return $dadosProcuracacao;
        }
        return $saidaDocExternoAPI;
    }

    /*
     * gerar modelo Procuração Especial
     */

    protected function gerarFormularioProcuracaoControlado($params){
       
        if(!(boolean)$params['dados']['selTipoProcuracao']){
            $params['dados']['selTipoProcuracao'] = MdPetVincRepresentantRN::$PE_PROCURADOR_ESPECIAL;
        }
        
        $objProcedimentoDTO = $params['procedimento'];

        $idProcedimento = $objProcedimentoDTO->getDblIdProcedimento();
        $isProcuracao       = false;

      
        // Caso a chamada venha da tela de Vinculação
        if(isset($params['tela'])){
            $idContatoVinc      = $params['dados']['idContato'];
            $idResponsavelLegal = $params['dados']['IdRepresentanteLegal'];
            $idOutorgado        = $params['dados']['IdOutorgado'];
            $idVinculo          = $params['idVinculo'];
            $idContato          = $params['idContato'];
            $unidadeDTO         = $params['unidadeDTO'];
        }else{
            $idContatoVinc      = $params['dados']['selPessoaJuridica'];
            $idResponsavelLegal = $params['dados']['idContatoExterno'];
            $idOutorgado        = $params['dados']['IdOutorgado'];
            $idVinculo          = $params['idVinculo'];
            $contatoPj          = $params['contatoPj'];
            $idContato          = $params['idContato'];
            $unidadeDTO         = $params['unidadeDTO'];
            $isProcuracao       = true;
        }
        
        $objInfraParametro = new InfraParametro($this->getObjInfraIBanco());
        $url = dirname(__FILE__) . '/../md_pet_vinc_modelo_procuracao.php';
        $htmlModeloProcuracao = file_get_contents($url);
       
        
            // consultar dados Pj
            
            $contatoDTO = new ContatoDTO();
            $contatoDTO->retDblCnpj();
            $contatoDTO->retStrNome();
            $contatoDTO->setNumIdContato($idContatoVinc);

            $contatoRN = new ContatoRN();
            $contatoPj = $contatoRN->consultarRN0324($contatoDTO);

            // consultar dados Responsável Legal
            $contatoDTO = new ContatoDTO();
            $contatoDTO->retDblCpf();
            $contatoDTO->retStrNome();
            $contatoDTO->setNumIdContato($idResponsavelLegal);

            $contatoRN = new ContatoRN();
            $Replegal = $contatoRN->consultarRN0324($contatoDTO);
        
        
        // consultar dados Outorgado
        $contatoDTO = new ContatoDTO();
        $contatoDTO->retDblCpf();
        $contatoDTO->retStrNome();
        $contatoDTO->retNumIdContato();
        $contatoDTO->setNumMaxRegistrosRetorno(1);
        $contatoDTO->setNumIdContato($idOutorgado);

        $contatoRN = new ContatoRN();
        $Outorgante = $contatoRN->consultarRN0324($contatoDTO);
        $dadosProcuracacao['Outorgado']['dadosOutorgado'] = $Outorgante;

        // Recuperando data e Hora atual
        $dataAtual = InfraData::getStrDataAtual();
        $horaAtual = InfraData::getStrHoraAtual();
        $dataAtual .= '  '.$horaAtual;

        //consultar orgão
        $idOrgao = SessaoSEIExterna::getInstance()->getNumIdOrgaoUsuarioExterno();
        $orgaoDTO = new  OrgaoDTO();
        $orgaoRN = new  OrgaoRN();
        $orgaoDTO->setNumIdOrgao($idOrgao);
        $orgaoDTO->retNumIdOrgao();
        $orgaoDTO->retStrSigla();
        $orgaoDTO->retStrDescricao();
        $orgao = $orgaoRN->consultarRN1352($orgaoDTO);
    
        $htmlModeloProcuracao = str_replace('@RazaoSocial',$contatoPj->getStrNome(),$htmlModeloProcuracao);
        $htmlModeloProcuracao = str_replace('@Cnpj',InfraUtil::formatarCnpj($contatoPj->getDblCnpj()),$htmlModeloProcuracao);
        $htmlModeloProcuracao = str_replace('@nomeRespLegal',$Replegal->getStrNome(),$htmlModeloProcuracao);
        $htmlModeloProcuracao = str_replace('@nomeOutorgado',$Outorgante->getStrNome(),$htmlModeloProcuracao);
        $htmlModeloProcuracao = str_replace('@sigla_orgao@',$orgao->getStrSigla(),$htmlModeloProcuracao);
        $htmlModeloProcuracao = str_replace('@descricao_orgao@',$orgao->getStrDescricao(),$htmlModeloProcuracao);

        //==========================================================================
        //incluindo doc recibo no processo via SEIRN
        //===========================================================

        $idSerieFormulario = $objInfraParametro->getValor(MdPetIntSerieRN::$MD_PET_ID_SERIE_PROCURACAOE);

        $objDocumentoAPI = new DocumentoAPI();
        $objDocumentoAPI->setIdProcedimento($objProcedimentoDTO->getDblIdProcedimento());
        $objDocumentoAPI->setTipo(ProtocoloRN::$TP_DOCUMENTO_GERADO);
        $objDocumentoAPI->setSubTipo(DocumentoRN::$TD_EDITOR_INTERNO /*DocumentoRN::$TD_FORMULARIO_AUTOMATICO*/);
//        $objDocumentoAPI->setSinAssinado('S');
//        $objDocumentoAPI->setSinBloqueado('S');
        $objDocumentoAPI->setIdSerie($idSerieFormulario);
//        $objDocumentoAPI->setIdHipoteseLegal(null);
//        $objDocumentoAPI->setNivelAcesso(ProtocoloRN::$NA_PUBLICO);
//        $objDocumentoAPI->setIdTipoConferencia(null);
//        $objDocumentoAPI->setConteudo(null);

        
        $objSeiRN = new SeiRN();
        $saidaDocExternoAPI = $objSeiRN->incluirDocumento($objDocumentoAPI);

        $participanteRN = new ParticipanteRN();
        $objParticipante = new ParticipanteDTO();
        $objParticipante->setDblIdProtocolo($saidaDocExternoAPI->getIdDocumento());
        $objParticipante->setNumIdContato($idContato);
        $objParticipante->setNumIdUnidade($unidadeDTO->getNumIdUnidade());
        $objParticipante->setStrStaParticipacao(ParticipanteRN::$TP_INTERESSADO);
        $objParticipante->setNumSequencia(0);      
        $participanteRN->cadastrarRN0170($objParticipante);
        $dadosProcuracacao['Outorgado']['Documento'] = $saidaDocExternoAPI;


        // Alterando Valores do Documento //
        $docRN = new DocumentoRN();
        $parObjDocumentoDTO = new DocumentoDTO();
        $parObjDocumentoDTO->retStrProtocoloProcedimentoFormatado();
        $parObjDocumentoDTO->retStrProtocoloDocumentoFormatado();
        $parObjDocumentoDTO->retStrStaDocumento();
        $parObjDocumentoDTO->setDblIdDocumento($saidaDocExternoAPI->getIdDocumento());
        $parObjDocumentoDTO->retDblIdDocumento();
        $parObjDocumentoDTO = $docRN->consultarRN0005($parObjDocumentoDTO);

        //Setando os parâmetros no Documento
        $htmlModeloProcuracao = str_replace('@numProcuracao',$parObjDocumentoDTO->getStrProtocoloDocumentoFormatado(),$htmlModeloProcuracao);
        $htmlModeloProcuracao = str_replace('@numDoc',$parObjDocumentoDTO->getStrProtocoloDocumentoFormatado(),$htmlModeloProcuracao);
        $htmlModeloProcuracao = str_replace('@numProcesso',$parObjDocumentoDTO->getStrProtocoloProcedimentoFormatado(),$htmlModeloProcuracao);

        //$parObjDocumentoDTO->setStrStaDocumento(DocumentoRN::$TD_FORMULARIO_AUTOMATICO);
        $parObjDocumentoDTO->setStrStaDocumento(DocumentoRN::$TD_EDITOR_INTERNO);

        $objDocumentoBD = new DocumentoBD($this->getObjInfraIBanco());
        $objDocumentoBD->alterar($parObjDocumentoDTO);

        //Alterando Conteudo do Documento com as Informções atualizadas
//        $parObjDocumentoConteudoDTO = new DocumentoConteudoDTO();
//        $parObjDocumentoConteudoDTO->setStrConteudo($htmlModeloProcuracao);
//        $parObjDocumentoConteudoDTO->setDblIdDocumento($saidaDocExternoAPI->getIdDocumento());

//        $objDocumentoConteudoBD = new DocumentoConteudoBD($this->getObjInfraIBanco());
//        $objDocumentoConteudoBD->alterar($parObjDocumentoConteudoDTO);

        $objEditorDTO = new EditorDTO();
      
        $objEditorDTO->setDblIdDocumento($saidaDocExternoAPI->getIdDocumento());
        $objEditorDTO->setNumIdBaseConhecimento(null);
        $objEditorDTO->setNumVersao(1);
        $objEditorDTO->setStrSinIgnorarNovaVersao('S');
        $objSecaoDocumentoDTO = new SecaoDocumentoDTO();
        $objSecaoDocumentoDTO->retNumIdSecaoModelo();
        $objSecaoDocumentoDTO->retStrNomeSecaoModelo();
        $objSecaoDocumentoDTO->setDblIdDocumento($saidaDocExternoAPI->getIdDocumento());
        //    $objSecaoDocumentoDTO->setNumIdBaseConhecimento($parObjEditorDTO->getNumIdBaseConhecimento());
        $objSecaoDocumentoDTO->setStrSinAssinatura('N');

        $objSecaoDocumentoRN = new SecaoDocumentoRN();
        $arrObjSecaoDocumentoDTOBanco = $objSecaoDocumentoRN->listar($objSecaoDocumentoDTO);

        if (count($arrObjSecaoDocumentoDTOBanco)){
            
            $arrObjSecaoDocumentoDTO = array();

            foreach($arrObjSecaoDocumentoDTOBanco as $item){
               
                $objSecaoDocumentoDTO = new SecaoDocumentoDTO();
                $objSecaoDocumentoDTO->setNumIdSecaoModelo($item->getNumIdSecaoModelo());
                
                if ($item->getStrNomeSecaoModelo()=='Corpo do Texto'){
                    
                    $objSecaoDocumentoDTO->setStrConteudo($htmlModeloProcuracao);
                }
                $arrObjSecaoDocumentoDTO[] = $objSecaoDocumentoDTO;
            }
        
            $objEditorDTO->setArrObjSecaoDocumentoDTO($arrObjSecaoDocumentoDTO);
            
            try{
                $objEditorRN = new EditorRN();
                $numVersao = $objEditorRN->adicionarVersao($objEditorDTO);
            }catch(Exception $e){
                if ($e instanceof InfraException && $e->contemValidacoes()){
                    die("INFRA_VALIDACAO\n".$e->__toString()); //retorna para o iframe exibir o alert
                }

                PaginaSEI::getInstance()->processarExcecao($e); //vai para a página de erro padrão
            }

        }

        //RECUPERANDO DADOS PARA ASSINATURA

        $mdPetProcessoRN = new mdPetProcessoRN();
        $idUnidade = $unidadeDTO->getNumIdUnidade();
        //Unidade

        //Recuperando unidade origem
        $objProtocoloDTO = new ProtocoloDTO();
        $objProtocoloDTO->setDblIdProtocolo($params['idProc']);
        $objProtocoloDTO->retNumIdUnidadeGeradora();
        $objProtocoloRN = new ProtocoloRN();
        $objUnidadeGeradora = $objProtocoloRN->consultarRN0186($objProtocoloDTO);

        $objUnidadeDTO = new UnidadeDTO();
        $objUnidadeRN = new UnidadeRN();
        $objUnidadeDTO->setNumIdUnidade($objUnidadeGeradora->getNumIdUnidadeGeradora());
        $objUnidadeDTO->retTodos();

        $objUnidadeDTO= $objUnidadeRN->consultarRN0125($objUnidadeDTO);

        $mdPetProcessoRN->assinarETravarDocumentoProcesso($objUnidadeDTO, $params['dados'], $parObjDocumentoDTO, $objProcedimentoDTO);


        //Adicionar Procuracao especial caso tenha sido selecionado.
        $dados = array('IdOutorgado'=>$idOutorgado,
                       'idContatoExterno'=>$idResponsavelLegal,
                       'selTipoProcuracao'=>$params['dados']['selTipoProcuracao'],
                       'tabelaProcessos'=>$params['dados']['hdnTbProcessos'],
                       'validade'=>$params['dados']['hdnValidade']
                 );
        
        $idMdPetVinculoRepresent = $this->_adicionarProcuracaoEspecial($dados, $idVinculo ,$saidaDocExternoAPI->getIdDocumento());
        
        $tpDocumento = MdPetVincDocumentoRN::$TP_PROTOCOLO_PROCURACAO_ESPECIAL;
        $this->_adicionarDadosArquivoVinculacao($saidaDocExternoAPI->getIdDocumento(), $idMdPetVinculoRepresent, $tpDocumento);

        $dadosProcuracacao['idVinculo'] = $idVinculo;
        $dadosProcuracacao['IdMdPetVinculoRepresent']= $idMdPetVinculoRepresent;
        if($isProcuracao) {
            return $dadosProcuracacao;
        }
        return $saidaDocExternoAPI;
    }

    private function _adicionarVinculo($dados, $idProcedimento)
    {

        $ckDeclaracao = $dados['chkDeclaracao'] == 'S' ? $dados['chkDeclaracao'] : 'N';

        $objMdPetVinculoRN = new MdPetVinculoRN();
        $objMdPetVinculoDTO = new MdPetVinculoDTO();
        $objMdPetVinculoDTO->setNumIdContato($dados['idContato']);
        $objMdPetVinculoDTO->setStrSinValidado($ckDeclaracao);
        $objMdPetVinculoDTO->setNumIdProtocolo($idProcedimento);
        $objMdPetVinculoDTO->setStrSinWebService('N');
        $objMdPetVinculoDTO->setStrTpVinculo('J');

        $objMdPetVinculoDTO = $objMdPetVinculoRN->cadastrar($objMdPetVinculoDTO);

        return $objMdPetVinculoDTO;


    }

    private function _adicionarProcuracaoEspecial($dados, $idVinculo,$idDocumento)
    {
        
        $objMdPetVincRepresentantRN = new MdPetVincRepresentantRN();
        $objMdPetVincRepresentantDTO = new MdPetVincRepresentantDTO();
        
        $objMdPetVincRepresentantDTO->setNumIdMdPetVinculo($idVinculo);
        $objMdPetVincRepresentantDTO->setNumIdContato($dados['IdOutorgado']);
        $objMdPetVincRepresentantDTO->setNumIdContatoOutorg($dados['idContatoExterno']);
        $objMdPetVincRepresentantDTO->setStrTipoRepresentante($dados['selTipoProcuracao']);
        $objMdPetVincRepresentantDTO->setStrSinAtivo('S');
        $objMdPetVincRepresentantDTO->setStrStaEstado('A');
        //Novas Colunas
        //Data Validade
        if($dados['validade'] != ""){
            $objMdPetVincRepresentantDTO->setDthDataLimite($dados['validade'] . " 23:59:59");
        }else{
            $objMdPetVincRepresentantDTO->setDthDataLimite(null);
        }
        //Colocar POST
        if($dados['tabelaProcessos'] != ""){
            $objMdPetVincRepresentantDTO->setStrStaAbrangencia("E");
        }else{
            $objMdPetVincRepresentantDTO->setStrStaAbrangencia("Q");
        }
        
        //Fim Novas Colunas
        $objMdPetVincRepresentantDTO->setDthDataCadastro(InfraData::getStrDataHoraAtual());
        $objMdPetVincRepresentantDTO->retNumIdMdPetVinculoRepresent();

        $objMdPetVincRepresentantRN->cadastrar($objMdPetVincRepresentantDTO);

        return $objMdPetVincRepresentantDTO->getNumIdMdPetVinculoRepresent();
    }

    private function _encerramentoProcuracaoEspecial($idMdPetVinculoRepresent,$dados)
    {
        
    	$objMdPetVincRepresentantRN = new MdPetVincRepresentantRN();
        $objMdPetVincRepresentantDTO = new MdPetVincRepresentantDTO();

        $objMdPetVincRepresentantDTO->setDthDataEncerramento(InfraData::getStrDataHoraAtual());
        $objMdPetVincRepresentantDTO->setNumIdMdPetVinculoRepresent($idMdPetVinculoRepresent);
        $objMdPetVincRepresentantDTO->setStrMotivo($dados['txtJustificativa']);
        $objMdPetVincRepresentantDTO->retNumIdMdPetVinculoRepresent();

        $strEstado = MdPetVincRepresentantRN::$RP_RENUNCIADA;
        if($dados['hdnTpDocumento'] == 'revogar'){
            $strEstado = MdPetVincRepresentantRN::$RP_REVOGADA;
        }
        $objMdPetVincRepresentantDTO->setStrStaEstado($strEstado);

        $objMdPetVincRepresentantRN->alterar($objMdPetVincRepresentantDTO);

        return $objMdPetVincRepresentantDTO;
    }

    protected function gerarReciboVinculacaoControlado($idProcedimento)
    {
        $objMdPetReciboDTO = new MdPetReciboDTO();
        $objMdPetReciboDTO->retTodos();

        $objMdPetReciboDTO->setNumIdProtocolo($idProcedimento);
        $objMdPetReciboDTO->setNumIdUsuario(SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno());
        $objMdPetReciboDTO->setDthDataHoraRecebimentoFinal(InfraData::getStrDataHoraAtual());
        $objMdPetReciboDTO->setStrIpUsuario(InfraUtil::getStrIpUsuario());
        $objMdPetReciboDTO->setStrSinAtivo('S');
        $objMdPetReciboDTO->setStrStaTipoPeticionamento(MdPetReciboRN::$TP_RECIBO_VINCULACAO);

        $objBD = new MdPetReciboBD($this->getObjInfraIBanco());
        $ret = $objBD->cadastrar($objMdPetReciboDTO);
        return $ret;

    }

    public function _adicionarDadosArquivoVinculacao($idDocumento, $idMdPetVinculoRepresent = null, $tpProtocolo)
    {

        $objMdPetUsuarioRN    = new MdPetIntUsuarioRN();

        $objMdPetVincDocumentoRN = new MdPetVincDocumentoRN();
        $objMdPetVincDocumentoDTO = new MdPetVincDocumentoDTO();
        $objMdPetVincDocumentoDTO->setDblIdDocumento($idDocumento);
        $objMdPetVincDocumentoDTO->setStrTipoDocumento($tpProtocolo);
        $objMdPetVincDocumentoDTO->setNumIdMdPetVinculoRepresent($idMdPetVinculoRepresent);
        $objMdPetVincDocumentoDTO->setDthDataCadastro(InfraData::getStrDataHoraAtual());
        $objMdPetVincDocumentoRN->cadastrar($objMdPetVincDocumentoDTO);

        return $objMdPetVincDocumentoDTO;
    }
    
    public function gerarDocumentoSuspensaoControlado($params)
    {
        $objProcedimentoDTO = $params['procedimento'];
        $dados = $params['dados'];

        $idVinculo = isset($dados['hdnIdVinculo']) ? $dados['hdnIdVinculo'] : null;
        $numeroSEI = isset($dados['hdnNumeroSei']) ? $dados['hdnNumeroSei'] : null;

        $objInfraParametro = new InfraParametro($this->getObjInfraIBanco());
        $url = dirname(__FILE__) . '/../md_pet_vinc_modelo_suspensao.php';
        $htmlModelo = file_get_contents($url);
        
        $objMdPetVinculoRN = new MdPetVinculoRN();
        $objMdPetVinculoDTO = new MdPetVinculoDTO();

        $objMdPetVinculoDTO->retTodos(true);
        $objMdPetVinculoDTO->retStrRazaoSocialNomeVinc();
        $objMdPetVinculoDTO->retDblCNPJ();
        $objMdPetVinculoDTO->retStrNomeContatoRepresentante();
        $objMdPetVinculoDTO->retStrCpfContatoRepresentante();
        $objMdPetVinculoDTO->retDthDataVinculo();
        $objMdPetVinculoDTO->setNumIdMdPetVinculo($idVinculo);
        $objMdPetVinculoDTO->setStrTipoRepresentante(MdPetVincRepresentantRN::$PE_RESPONSAVEL_LEGAL);

        $arrObjMdPetVinculoDTO = $objMdPetVinculoRN->listar($objMdPetVinculoDTO);

        if (count($arrObjMdPetVinculoDTO)>0){
            $razaoSocial  = $arrObjMdPetVinculoDTO[0]->getStrRazaoSocialNomeVinc();
            $cnpj         = $arrObjMdPetVinculoDTO[0]->getDblCNPJ();
            $idContatoRepresentante = $arrObjMdPetVinculoDTO[0]->getNumIdContatoRepresentante();
            $RepLegalNome = $arrObjMdPetVinculoDTO[0]->getStrNomeContatoRepresentante();
            $RepLegalCpf  = $arrObjMdPetVinculoDTO[0]->getStrCpfContatoRepresentante();
            $dataVinc     = $arrObjMdPetVinculoDTO[0]->getDthDataVinculo();

            // Documento do Vinculo
            $objMdPetVincDocumentoDTO = new MdPetVincDocumentoDTO();
            $objMdPetVincDocumentoDTO->retStrProtocoloFormatadoProtocolo();
            $objMdPetVincDocumentoDTO->setNumIdMdPetVinculoRepresent($arrObjMdPetVinculoDTO[0]->getNumIdMdPetVinculoRepresent());
            $objMdPetVincDocumentoDTO->setStrTipoDocumento(MdPetVincDocumentoRN::$TP_PROTOCOLO_PRINCIPAL);
            $objMdPetVincDocumentoDTO->setOrdNumIdMdPetVincDocumento(InfraDTO::$TIPO_ORDENACAO_DESC);

            $objMdPetVincDocumentoRN = new MdPetVincDocumentoRN;
            $arrObjMdPetVincDocumentoDTO = $objMdPetVincDocumentoRN->listar($objMdPetVincDocumentoDTO);

            if (count($arrObjMdPetVincDocumentoDTO)>0){
                $numDocVinc = $arrObjMdPetVincDocumentoDTO[0]->getStrProtocoloFormatadoProtocolo();
            }
        }

        //Orgao
        $usuarioDTO = new UsuarioDTO();
        $usuarioDTO->retStrSiglaOrgao();
        $usuarioDTO->retStrDescricaoOrgao();
        $usuarioDTO->setNumIdContato($arrObjMdPetVinculoDTO[0]->getNumIdContatoRepresentante());

        $usuarioRN = new UsuarioRN();
        $usuarioDTO = $usuarioRN->consultarRN0489($usuarioDTO);		

        if (count($usuarioDTO)>0){
            $siglaOrgao = $usuarioDTO->getStrSiglaOrgao();
            $descricaoOrgao = $usuarioDTO->getStrDescricaoOrgao();
        }

        $htmlModelo = str_replace('@dataVinc',$dataVinc,$htmlModelo);
        $htmlModelo = str_replace('@numProcessoVinc',$objProcedimentoDTO->getStrProtocoloProcedimentoFormatado(),$htmlModelo);
        $htmlModelo = str_replace('@numDocVinc',$numDocVinc,$htmlModelo);

        $htmlModelo = str_replace('@RazaoSocial',$razaoSocial,$htmlModelo);
        $htmlModelo = str_replace('@Cnpj',InfraUtil::formatarCnpj($cnpj),$htmlModelo);
        $htmlModelo = str_replace('@nomeRespLegal',$RepLegalNome,$htmlModelo);
        $htmlModelo = str_replace('@cpfRespLegal',InfraUtil::formatarCpf($RepLegalCpf),$htmlModelo);
        $htmlModelo = str_replace('@cpfRespLegal',InfraUtil::formatarCpf($RepLegalCpf),$htmlModelo);        
        $htmlModelo = str_replace('@numeroSEI',$numeroSEI,$htmlModelo);        
        $htmlModelo = str_replace('@sigla_orgao@',$siglaOrgao,$htmlModelo);
        $htmlModelo = str_replace('@descricao_orgao@',$descricaoOrgao,$htmlModelo);


        //==========================================================================
        //incluindo doc recibo no processo via SEIRN
        //===========================================================

        $idSerieFormulario = $objInfraParametro->getValor(MdPetIntSerieRN::$MD_PET_ID_SERIE_VINC_SUSPENSAO);

        $objDocumentoAPI = new DocumentoAPI();
        $objDocumentoAPI->setIdProcedimento($objProcedimentoDTO->getDblIdProcedimento());
        $objDocumentoAPI->setTipo(ProtocoloRN::$TP_DOCUMENTO_GERADO);
        $objDocumentoAPI->setSubTipo(DocumentoRN::$TD_EDITOR_INTERNO /*DocumentoRN::$TD_FORMULARIO_AUTOMATICO*/);
        //        $objDocumentoAPI->setSinAssinado('S');
        //        $objDocumentoAPI->setSinBloqueado('S');
        $objDocumentoAPI->setIdSerie($idSerieFormulario);
        //        $objDocumentoAPI->setIdHipoteseLegal(null);
        //        $objDocumentoAPI->setNivelAcesso(ProtocoloRN::$NA_PUBLICO);
        //        $objDocumentoAPI->setIdTipoConferencia(null);
        //        $objDocumentoAPI->setConteudo(null);


        $objSeiRN = new SeiRN();
        $saidaDocExternoAPI = $objSeiRN->incluirDocumento($objDocumentoAPI);


        // Alterando Valores do Documento //
        $docRN = new DocumentoRN();
        $parObjDocumentoDTO = new DocumentoDTO();
        $parObjDocumentoDTO->retStrProtocoloProcedimentoFormatado();
        $parObjDocumentoDTO->retStrProtocoloDocumentoFormatado();
        $parObjDocumentoDTO->retStrStaDocumento();
        $parObjDocumentoDTO->setDblIdDocumento($saidaDocExternoAPI->getIdDocumento());
        $parObjDocumentoDTO->retDblIdDocumento();
        $parObjDocumentoDTO = $docRN->consultarRN0005($parObjDocumentoDTO);

        //Setando os parâmetros no Documento
        $htmlModelo = str_replace('@numSuspensao',$parObjDocumentoDTO->getStrProtocoloDocumentoFormatado(),$htmlModelo);

        //$parObjDocumentoDTO->setStrStaDocumento(DocumentoRN::$TD_FORMULARIO_AUTOMATICO);
        $parObjDocumentoDTO->setStrStaDocumento(DocumentoRN::$TD_EDITOR_INTERNO);

        $objDocumentoBD = new DocumentoBD($this->getObjInfraIBanco());
        $objDocumentoBD->alterar($parObjDocumentoDTO);


        //Alterando Conteudo do Documento com as Informções atualizadas
//        $parObjDocumentoConteudoDTO = new DocumentoConteudoDTO();
//        $parObjDocumentoConteudoDTO->setStrConteudo($htmlModelo);
//        $parObjDocumentoConteudoDTO->setDblIdDocumento($saidaDocExternoAPI->getIdDocumento());

//        $objDocumentoConteudoBD = new DocumentoConteudoBD($this->getObjInfraIBanco());
//        $objDocumentoConteudoBD->alterar($parObjDocumentoConteudoDTO);


        $objEditorDTO = new EditorDTO();
        
        $objEditorDTO->setDblIdDocumento($saidaDocExternoAPI->getIdDocumento());
        $objEditorDTO->setNumIdBaseConhecimento(null);
        $objEditorDTO->setNumVersao(1);
        $objEditorDTO->setStrSinIgnorarNovaVersao('S');
        
        $objSecaoDocumentoDTO = new SecaoDocumentoDTO();
        $objSecaoDocumentoDTO->retNumIdSecaoModelo();
        $objSecaoDocumentoDTO->retStrNomeSecaoModelo();
        $objSecaoDocumentoDTO->setDblIdDocumento($saidaDocExternoAPI->getIdDocumento());
        //    $objSecaoDocumentoDTO->setNumIdBaseConhecimento($parObjEditorDTO->getNumIdBaseConhecimento());
        $objSecaoDocumentoDTO->setStrSinAssinatura('N');
        
        $objSecaoDocumentoRN = new SecaoDocumentoRN();
        $arrObjSecaoDocumentoDTOBanco = $objSecaoDocumentoRN->listar($objSecaoDocumentoDTO);
        
        if (count($arrObjSecaoDocumentoDTOBanco)){
        
        	$arrObjSecaoDocumentoDTO = array();
        
        	foreach($arrObjSecaoDocumentoDTOBanco as $item){
        		$objSecaoDocumentoDTO = new SecaoDocumentoDTO();
        		$objSecaoDocumentoDTO->setNumIdSecaoModelo($item->getNumIdSecaoModelo());
        
        		if ($item->getStrNomeSecaoModelo()=='Corpo do Texto'){
        			$objSecaoDocumentoDTO->setStrConteudo($htmlModelo);
        		}
        		$arrObjSecaoDocumentoDTO[] = $objSecaoDocumentoDTO;
        	}
        
        	$objEditorDTO->setArrObjSecaoDocumentoDTO($arrObjSecaoDocumentoDTO);
        
        	try{
        		$objEditorRN = new EditorRN();
        		$numVersao = $objEditorRN->adicionarVersao($objEditorDTO);
        	}catch(Exception $e){
        		if ($e instanceof InfraException && $e->contemValidacoes()){
        			die("INFRA_VALIDACAO\n".$e->__toString()); //retorna para o iframe exibir o alert
        		}
        
        		PaginaSEI::getInstance()->processarExcecao($e); //vai para a página de erro padrão
        	}
        
        }


        //RECUPERANDO DADOS PARA ASSINATURA

        $mdPetProcessoRN = new mdPetProcessoRN();

        $objMdPetVincTpProcessoRN = new MdPetVincTpProcessoRN();
        $objMdPetVincTpProcessoDTO = new MdPetVincTpProcessoDTO();
        $objMdPetVincTpProcessoDTO->retTodos();
        $objMdPetVincTpProcessoDTO->setStrTipoVinculo("J");
        $objMdPetVincTpProcesso = $objMdPetVincTpProcessoRN->consultar($objMdPetVincTpProcessoDTO);

        $idUnidade = null;
        if (count($objMdPetVincTpProcesso)>0){
            $idUnidade = $objMdPetVincTpProcesso->getNumIdUnidade();
        }

        //Unidade
        $objUnidadeDTO = new UnidadeDTO();
        $objUnidadeRN = new UnidadeRN();
        $objUnidadeDTO->setNumIdUnidade($idUnidade);
        $objUnidadeDTO->retTodos();

        $objUnidadeDTO= $objUnidadeRN->consultarRN0125($objUnidadeDTO);

        $mdPetProcessoRN->assinarETravarDocumentoProcesso($objUnidadeDTO, $params['dados'], $parObjDocumentoDTO, $objProcedimentoDTO);

        //$parObjDocumentoDTO->setDblIdDocumento($saidaDocExternoAPI->getIdDocumento());
        //$objDocumentoBD = new DocumentoBD($this->getObjInfraIBanco());
        //$objDocumentoBD->alterar($parObjDocumentoDTO);


        //Disponibilazando Acesso Externo ao Procurador Especial
        $objMdPetAcessoExternoRN = new MdPetAcessoExternoRN();
        $objMdPetAcessoExternoRN->aplicarRegrasGeraisAcessoExterno(
            $objProcedimentoDTO->getDblIdProcedimento(),
            MdPetAcessoExternoRN::$MD_PET_VINC_PROCURACAO,$idContatoRepresentante,null,null,
            array($saidaDocExternoAPI->getIdDocumento())
        );

        return $saidaDocExternoAPI;

    }
    
    public function gerarDocumentoRestabelecimentoControlado($params)
    {
        $objProcedimentoDTO = $params['procedimento'];
        $dados = $params['dados'];

        $idVinculo = isset($dados['hdnIdVinculo']) ? $dados['hdnIdVinculo'] : null;
        $numeroSEI = isset($dados['hdnNumeroSei']) ? $dados['hdnNumeroSei'] : null;

        $objInfraParametro = new InfraParametro($this->getObjInfraIBanco());
        $url = dirname(__FILE__) . '/../md_pet_vinc_modelo_restabelecimento.php';
        $htmlModelo = file_get_contents($url);

        $objMdPetVinculoRN = new MdPetVinculoRN();
        $objMdPetVinculoDTO = new MdPetVinculoDTO();

        $objMdPetVinculoDTO->retTodos(true);
        $objMdPetVinculoDTO->retStrRazaoSocialNomeVinc();
        $objMdPetVinculoDTO->retDblCNPJ();
        $objMdPetVinculoDTO->retStrNomeContatoRepresentante();
        $objMdPetVinculoDTO->retStrCpfContatoRepresentante();
        $objMdPetVinculoDTO->retDthDataVinculo();

        $objMdPetVinculoDTO->setNumIdMdPetVinculo($idVinculo);

        $arrObjMdPetVinculoDTO = $objMdPetVinculoRN->listar($objMdPetVinculoDTO);

        if (count($arrObjMdPetVinculoDTO)>0){
            $razaoSocial  = $arrObjMdPetVinculoDTO[0]->getStrRazaoSocialNomeVinc();
            $cnpj         = $arrObjMdPetVinculoDTO[0]->getDblCNPJ();
            $idContatoRepresentante = $arrObjMdPetVinculoDTO[0]->getNumIdContatoRepresentante();
            $RepLegalNome = $arrObjMdPetVinculoDTO[0]->getStrNomeContatoRepresentante();
            $RepLegalCpf  = $arrObjMdPetVinculoDTO[0]->getStrCpfContatoRepresentante();
            $dataVinc     = $arrObjMdPetVinculoDTO[0]->getDthDataVinculo();

            // Documento do Vinculo
            $objMdPetVincDocumentoDTO = new MdPetVincDocumentoDTO();
            $objMdPetVincDocumentoDTO->retStrProtocoloFormatadoProtocolo();
            $objMdPetVincDocumentoDTO->setNumIdMdPetVinculoRepresent($arrObjMdPetVinculoDTO[0]->getNumIdMdPetVinculoRepresent());
            $objMdPetVincDocumentoDTO->setStrTipoDocumento(MdPetVincDocumentoRN::$TP_PROTOCOLO_PRINCIPAL);
            $objMdPetVincDocumentoDTO->setOrdNumIdMdPetVincDocumento(InfraDTO::$TIPO_ORDENACAO_DESC);

            $objMdPetVincDocumentoRN = new MdPetVincDocumentoRN;
            $arrObjMdPetVincDocumentoDTO = $objMdPetVincDocumentoRN->listar($objMdPetVincDocumentoDTO);

            if (count($arrObjMdPetVincDocumentoDTO)>0){
                $numDocVinc = $arrObjMdPetVincDocumentoDTO[0]->getStrProtocoloFormatadoProtocolo();
            }
        }

        //Orgao
        $usuarioDTO = new UsuarioDTO();
        $usuarioDTO->retStrSiglaOrgao();
        $usuarioDTO->retStrDescricaoOrgao();
        $usuarioDTO->setNumIdContato($arrObjMdPetVinculoDTO[0]->getNumIdContatoRepresentante());

        $usuarioRN = new UsuarioRN();
        $usuarioDTO = $usuarioRN->consultarRN0489($usuarioDTO);		

        if (count($usuarioDTO)>0){
            $siglaOrgao = $usuarioDTO->getStrSiglaOrgao();
            $descricaoOrgao = $usuarioDTO->getStrDescricaoOrgao();
        }

        $htmlModelo = str_replace('@dataVinc',$dataVinc,$htmlModelo);
        $htmlModelo = str_replace('@numProcessoVinc',$objProcedimentoDTO->getStrProtocoloProcedimentoFormatado(),$htmlModelo);
        $htmlModelo = str_replace('@numDocVinc',$numDocVinc,$htmlModelo);

        $htmlModelo = str_replace('@RazaoSocial',$razaoSocial,$htmlModelo);
        $htmlModelo = str_replace('@NomeFantasia',$nomeFantasia,$htmlModelo);
        $htmlModelo = str_replace('@Cnpj',InfraUtil::formatarCnpj($cnpj),$htmlModelo);
        $htmlModelo = str_replace('@nomeRespLegal',$RepLegalNome,$htmlModelo);
        $htmlModelo = str_replace('@cpfRespLegal',InfraUtil::formatarCpf($RepLegalCpf),$htmlModelo);
        $htmlModelo = str_replace('@numeroSEI',$numeroSEI,$htmlModelo);

        $htmlModelo = str_replace('@dataVinc',$dataVinc,$htmlModelo);
        $htmlModelo = str_replace('@sigla_orgao@',$siglaOrgao,$htmlModelo);
        $htmlModelo = str_replace('@descricao_orgao@',$descricaoOrgao,$htmlModelo);


        //==========================================================================
        //incluindo doc recibo no processo via SEIRN
        //===========================================================

        $idSerieFormulario = $objInfraParametro->getValor(MdPetIntSerieRN::$MD_PET_ID_SERIE_VINC_RESTABELECIMENTO);

        
        $objDocumentoAPI = new DocumentoAPI();
        $objDocumentoAPI->setIdProcedimento($objProcedimentoDTO->getDblIdProcedimento());
        $objDocumentoAPI->setTipo(ProtocoloRN::$TP_DOCUMENTO_GERADO);
        $objDocumentoAPI->setSubTipo(DocumentoRN::$TD_EDITOR_INTERNO /*DocumentoRN::$TD_FORMULARIO_AUTOMATICO*/);
        //        $objDocumentoAPI->setSinAssinado('S');
        //        $objDocumentoAPI->setSinBloqueado('S');
        $objDocumentoAPI->setIdSerie($idSerieFormulario);
        //        $objDocumentoAPI->setIdHipoteseLegal(null);
        //        $objDocumentoAPI->setNivelAcesso(ProtocoloRN::$NA_PUBLICO);
        //        $objDocumentoAPI->setIdTipoConferencia(null);
        //        $objDocumentoAPI->setConteudo(null);

        $objSeiRN = new SeiRN();
        $saidaDocExternoAPI = $objSeiRN->incluirDocumento($objDocumentoAPI);

        // Alterando Valores do Documento //
        $docRN = new DocumentoRN();
        $parObjDocumentoDTO = new DocumentoDTO();
        $parObjDocumentoDTO->retStrProtocoloProcedimentoFormatado();
        $parObjDocumentoDTO->retStrProtocoloDocumentoFormatado();
        $parObjDocumentoDTO->retStrStaDocumento();
        $parObjDocumentoDTO->setDblIdDocumento($saidaDocExternoAPI->getIdDocumento());
        $parObjDocumentoDTO->retDblIdDocumento();
        $parObjDocumentoDTO = $docRN->consultarRN0005($parObjDocumentoDTO);

        //Setando os parâmetros no Documento
        $htmlModelo = str_replace('@numRestabelecimento',$parObjDocumentoDTO->getStrProtocoloDocumentoFormatado(),$htmlModelo);

        //$parObjDocumentoDTO->setStrStaDocumento(DocumentoRN::$TD_FORMULARIO_AUTOMATICO);
        $parObjDocumentoDTO->setStrStaDocumento(DocumentoRN::$TD_EDITOR_INTERNO);
        
        $objDocumentoBD = new DocumentoBD($this->getObjInfraIBanco());
        $objDocumentoBD->alterar($parObjDocumentoDTO);

        
        //Alterando Conteudo do Documento com as Informções atualizadas
//        $parObjDocumentoConteudoDTO = new DocumentoConteudoDTO();
//        $parObjDocumentoConteudoDTO->setStrConteudo($htmlModelo);
//        $parObjDocumentoConteudoDTO->setDblIdDocumento($saidaDocExternoAPI->getIdDocumento());

//        $objDocumentoConteudoBD = new DocumentoConteudoBD($this->getObjInfraIBanco());
//        $objDocumentoConteudoBD->alterar($parObjDocumentoConteudoDTO);


        $objEditorDTO = new EditorDTO();
        
        $objEditorDTO->setDblIdDocumento($saidaDocExternoAPI->getIdDocumento());
        $objEditorDTO->setNumIdBaseConhecimento(null);
        $objEditorDTO->setNumVersao(1);
        $objEditorDTO->setStrSinIgnorarNovaVersao('S');
        
        $objSecaoDocumentoDTO = new SecaoDocumentoDTO();
        $objSecaoDocumentoDTO->retNumIdSecaoModelo();
        $objSecaoDocumentoDTO->retStrNomeSecaoModelo();
        $objSecaoDocumentoDTO->setDblIdDocumento($saidaDocExternoAPI->getIdDocumento());
        //    $objSecaoDocumentoDTO->setNumIdBaseConhecimento($parObjEditorDTO->getNumIdBaseConhecimento());
        $objSecaoDocumentoDTO->setStrSinAssinatura('N');
        
        $objSecaoDocumentoRN = new SecaoDocumentoRN();
        $arrObjSecaoDocumentoDTOBanco = $objSecaoDocumentoRN->listar($objSecaoDocumentoDTO);
        
        if (count($arrObjSecaoDocumentoDTOBanco)){
        
        	$arrObjSecaoDocumentoDTO = array();
        
        	foreach($arrObjSecaoDocumentoDTOBanco as $item){
        		$objSecaoDocumentoDTO = new SecaoDocumentoDTO();
        		$objSecaoDocumentoDTO->setNumIdSecaoModelo($item->getNumIdSecaoModelo());
        
        		if ($item->getStrNomeSecaoModelo()=='Corpo do Texto'){
        			$objSecaoDocumentoDTO->setStrConteudo($htmlModelo);
        		}
        		$arrObjSecaoDocumentoDTO[] = $objSecaoDocumentoDTO;
        	}
        
        	$objEditorDTO->setArrObjSecaoDocumentoDTO($arrObjSecaoDocumentoDTO);
        
        	try{
        		$objEditorRN = new EditorRN();
        		$numVersao = $objEditorRN->adicionarVersao($objEditorDTO);
        	}catch(Exception $e){
        		if ($e instanceof InfraException && $e->contemValidacoes()){
        			die("INFRA_VALIDACAO\n".$e->__toString()); //retorna para o iframe exibir o alert
        		}
        
        		PaginaSEI::getInstance()->processarExcecao($e); //vai para a página de erro padrão
        	}
        
        }
        
        
        //RECUPERANDO DADOS PARA ASSINATURA

        $mdPetProcessoRN = new mdPetProcessoRN();

        $objMdPetVincTpProcessoRN = new MdPetVincTpProcessoRN();
        $objMdPetVincTpProcessoDTO = new MdPetVincTpProcessoDTO();
        $objMdPetVincTpProcessoDTO->retTodos();
        $objMdPetVincTpProcessoDTO->setNumIdMdPetVincTpProcesso(MdPetVincTpProcessoRN::$ID_FIXO_MD_PET_VINCULO_USU_EXT);
        $objMdPetVincTpProcesso = $objMdPetVincTpProcessoRN->consultar($objMdPetVincTpProcessoDTO);

        $idUnidade = null;
        if (count($objMdPetVincTpProcesso)>0){
            $idUnidade = $objMdPetVincTpProcesso->getNumIdUnidade();
        }

        //Unidade
        $objUnidadeDTO = new UnidadeDTO();
        $objUnidadeRN = new UnidadeRN();
        $objUnidadeDTO->setNumIdUnidade($idUnidade);
        $objUnidadeDTO->retTodos();

        $objUnidadeDTO= $objUnidadeRN->consultarRN0125($objUnidadeDTO);

        $mdPetProcessoRN->assinarETravarDocumentoProcesso($objUnidadeDTO, $params['dados'], $parObjDocumentoDTO, $objProcedimentoDTO);

        //$parObjDocumentoDTO->setDblIdDocumento($saidaDocExternoAPI->getIdDocumento());
        //$objDocumentoBD = new DocumentoBD($this->getObjInfraIBanco());
        //$objDocumentoBD->alterar($parObjDocumentoDTO);
        

        //Disponibilazando Acesso Externo ao Procurador Especial
        $objMdPetAcessoExternoRN = new MdPetAcessoExternoRN();
        $objMdPetAcessoExternoRN->aplicarRegrasGeraisAcessoExterno(
            $objProcedimentoDTO->getDblIdProcedimento(),
            MdPetAcessoExternoRN::$MD_PET_VINC_PROCURACAO,$idContatoRepresentante,null,null,
            array($saidaDocExternoAPI->getIdDocumento())
        );

        return $saidaDocExternoAPI;

    }

    /*GERA RECIBO QUANDO OCORRE UMA NOVA PROCURAÇÃO ELETRONICA */

    private function gerarReciboProcuracao($arrParams)
    {
        $dadosProcuracao     = array_key_exists(0, $arrParams) ? $arrParams[0] : null;
        $objProcedimentoDTO  = array_key_exists(1, $arrParams) ? $arrParams[1] : null;
        $reciboDTOBasico     = array_key_exists(2, $arrParams) ? $arrParams[2] : null;
        $idRepresentante     = array_key_exists(3, $arrParams) ? $arrParams[3] : null;
        $tpRecibo            = array_key_exists(4, $arrParams) ? $arrParams[4] : self::$TIPO_PETICIONAMENTO_RECIBO_PROCURACAOE_VINC_PJ;
        $unidadeDTO          = array_key_exists(5, $arrParams) ? $arrParams[5] : null;
        $dados               = array_key_exists(6, $arrParams) ? $arrParams[6] : null;

        //consultar orgão externo
        $idOrgao = SessaoSEIExterna::getInstance()->getNumIdOrgaoUsuarioExterno();
        $orgaoDTO = new  OrgaoDTO();
        $orgaoRN = new  OrgaoRN();
        $orgaoDTO->setNumIdOrgao($idOrgao);
        $orgaoDTO->retNumIdOrgao();
        $orgaoDTO->retStrSigla();
        $orgaoDTO->retStrDescricao();
        $orgao = $orgaoRN->consultarRN1352($orgaoDTO);

        $objInfraParametro = new InfraParametro($this->getObjInfraIBanco());
        $url = dirname(__FILE__) . '/../md_pet_vinc_usu_ext_recibo_eletronico.php';
        $htmlModeloRecibo = file_get_contents($url);

        
        if($dados['selTipoProcuracao'] == MdPetVincRepresentantRN::$PE_PROCURADOR_SIMPLES && $dados['hdnOutorgante'] == "PF"){
            $idContato          = $dados['hdnIdContExterno'];
        }else if($dados['selTipoProcuracao'] == "E"){
            $idContato          = $dados['selPessoaJuridica'];
        }else if($dados['selTipoProcuracao'] == MdPetVincRepresentantRN::$PE_PROCURADOR_SIMPLES && $dados['hdnOutorgante'] == "PJ" ){
            $idContato          = $dados['hdnSelPJSimples'];
        }
        
        $dadosPj = $dadosProcuracao['contatoPj'];
        $dadosRepresentante = $dadosProcuracao['RepLegal'];
        
        $htmlModeloRecibo = str_replace('@usuarioExterno', $dadosRepresentante->getStrNome(), $htmlModeloRecibo); //Nome do responsavel
        $htmlModeloRecibo = str_replace('@numRecibo', $reciboDTOBasico->getNumIdReciboPeticionamento(), $htmlModeloRecibo); //Nome do responsavel
        $htmlModeloRecibo = str_replace('@responsavelLegal', $dadosRepresentante->getStrNome(), $htmlModeloRecibo); //Nome do responsavel
        $htmlModeloRecibo = str_replace('@ipUtilizado', $reciboDTOBasico->getStrIpUsuario(), $htmlModeloRecibo); //ip usuario
        $htmlModeloRecibo = str_replace('@dataHorario', $reciboDTOBasico->getDthDataHoraRecebimentoFinal(), $htmlModeloRecibo); //data hora
        $htmlModeloRecibo = str_replace('@tipoProcesso', $reciboDTOBasico->getStrStaTipoPeticionamentoFormatado(), $htmlModeloRecibo); //tipo de processo
        $htmlModeloRecibo = str_replace('@numProcesso', $objProcedimentoDTO->getStrProtocoloProcedimentoFormatado(), $htmlModeloRecibo); //numero do processo
        $htmlModeloRecibo = str_replace('@cpfResponsavel',InfraUtil::formatarCpf($dadosRepresentante->getDblCpf()), $htmlModeloRecibo); // Nome do CPF
        $htmlModeloRecibo = str_replace('@cnpj',InfraUtil::formatarCnpj($dadosPj->getDblCnpj()), $htmlModeloRecibo); // Cnpj do vinculo
        $htmlModeloRecibo = str_replace('@nomeRazaoSocial', $dadosPj->getStrNome(), $htmlModeloRecibo); // Razao Social
        $htmlModeloRecibo = str_replace('@descricao_orgao@',$orgao->getStrDescricao(),$htmlModeloRecibo); // Descricao do Orgão

        $tblPessoaJuridica = '';
        $htmlModeloRecibo = str_replace('@tblPessoaJuridica', $tblPessoaJuridica, $htmlModeloRecibo);

        $modeloProcuracao = $dadosProcuracao['Procuracao'];

        $idProcedimento = $objProcedimentoDTO->getDblIdProcedimento();

        //Documento Principal
        $tblDocumentoPrincipal= '';
        $htmlModeloRecibo = str_replace('@tblDocumentoPrincipal', $tblDocumentoPrincipal, $htmlModeloRecibo);

        //Atos Constitutivos
        $tblAtos = '';
        $htmlModeloRecibo = str_replace('@tblAtosConstitutivos', $tblAtos, $htmlModeloRecibo); // Atos Constitutivos

        //Procurações Especiais
        $tblProcuracoes = '';
        if(!is_null($modeloProcuracao)){
            $tblProcuracoes .= '    <tr>';
            $tblProcuracoes .= '        <td colspan="2" style="font-weight: bold;">Protocolos dos Documentos (Número SEI):</td>';
            $tblProcuracoes .= '    </tr>';

            for ($i = 0 ; $i <count($modeloProcuracao); $i++) {
            	$objDocumentoRN = new DocumentoRN();
            	$objDocumentoDTO = new DocumentoDTO();
            	$objDocumentoDTO->retStrNomeSerie();
            	$objDocumentoDTO->retStrProtocoloDocumentoFormatado();
            	$objDocumentoDTO->retStrNumero();
            	$objDocumentoDTO->setDblIdDocumento($modeloProcuracao[$i]['Outorgado']['Documento']->getIdDocumento());
            	$arrObjDocumentoDTO = $objDocumentoRN->listarRN0008($objDocumentoDTO);

                $tblProcuracoes .= '    <tr>';
                $tblProcuracoes .= '        <td>&nbsp;&nbsp;&nbsp;&nbsp;- '.$arrObjDocumentoDTO[0]->getStrNomeSerie();
                $tblProcuracoes .= $arrObjDocumentoDTO[0]->getStrNumero()!='' ? ' '.$arrObjDocumentoDTO[0]->getStrNumero() : '';
                $tblProcuracoes .='</td>';
                $tblProcuracoes .= '        <td>'.$arrObjDocumentoDTO[0]->getStrProtocoloDocumentoFormatado().'</td>';
                $tblProcuracoes .= '    </tr>';
            }
        }
        $htmlModeloRecibo = str_replace('@tblProcuracoes', $tblProcuracoes, $htmlModeloRecibo); // Procuracoes especiais

        //==========================================================================
        //incluindo doc recibo no processo via SEIRN
        //==========================================================================

        $idSerieRecibo = $objInfraParametro->getValor(MdPetIntSerieRN::$MD_PET_ID_SERIE_RECIBO);

        $objDocumentoAPI = new DocumentoAPI();
        $objDocumentoAPI->setIdProcedimento($objProcedimentoDTO->getDblIdProcedimento());
        $objDocumentoAPI->setSubTipo(DocumentoRN::$TD_FORMULARIO_AUTOMATICO);
        $objDocumentoAPI->setTipo(ProtocoloRN::$TP_DOCUMENTO_GERADO);
        $objDocumentoAPI->setIdSerie($idSerieRecibo);
        $objDocumentoAPI->setSinAssinado('N');
        $objDocumentoAPI->setSinBloqueado('S');
        $objDocumentoAPI->setIdHipoteseLegal(null);
        $objDocumentoAPI->setNivelAcesso(ProtocoloRN::$NA_PUBLICO);
        $objDocumentoAPI->setIdTipoConferencia(null);

        $objDocumentoAPI->setConteudo(base64_encode(utf8_encode($htmlModeloRecibo)));

        $objSeiRN = new SeiRN();
        $saidaDocExternoAPI = $objSeiRN->incluirDocumento($objDocumentoAPI);

        $participanteRN = new ParticipanteRN();
        $objParticipante = new ParticipanteDTO();
        $objParticipante->setDblIdProtocolo($saidaDocExternoAPI->getIdDocumento());
        $objParticipante->setNumIdContato($idContato);
        $objParticipante->setNumIdUnidade($unidadeDTO->getNumIdUnidade());
        $objParticipante->setStrStaParticipacao(ParticipanteRN::$TP_INTERESSADO);
        $objParticipante->setNumSequencia(0);      
        $participanteRN->cadastrarRN0170($objParticipante);
        
        $tpProtocolo = MdPetVincDocumentoRN::$TP_PROTOCOLO_RECIBO;
        $this->_adicionarDadosArquivoVinculacao($saidaDocExternoAPI->getIdDocumento(), $idRepresentante, $tpProtocolo);

        //necessario forçar update da coluna sta_documento da tabela documento
        //inclusao via SeiRN nao permitiu definir como documento de formulario automatico
        $parObjDocumentoDTO = new DocumentoDTO();
        $parObjDocumentoDTO->retTodos();
        $parObjDocumentoDTO->setDblIdDocumento($saidaDocExternoAPI->getIdDocumento());

        $docRN = new DocumentoRN();
        $parObjDocumentoDTO = $docRN->consultarRN0005($parObjDocumentoDTO);
        $parObjDocumentoDTO->setStrStaDocumento(DocumentoRN::$TD_FORMULARIO_AUTOMATICO);
        $objDocumentoBD = new DocumentoBD($this->getObjInfraIBanco());
        $objDocumentoBD->alterar($parObjDocumentoDTO);

        $reciboDTOBasico->setDblIdDocumento($saidaDocExternoAPI->getIdDocumento());

        $objBD = new MdPetReciboBD($this->getObjInfraIBanco());
        $objBD->alterar($reciboDTOBasico);
        return $reciboDTOBasico;

    }

    private function gerarReciboDesvinculo($arrParams)
    {
        $dadosProcuracao     = array_key_exists(0, $arrParams) ? $arrParams[0] : null;
        $objProcedimentoDTO  = array_key_exists(1, $arrParams) ? $arrParams[1] : null;
        $reciboDTOBasico     = array_key_exists(2, $arrParams) ? $arrParams[2] : null;
        $idRepresentante     = array_key_exists(3, $arrParams) ? $arrParams[3] : null;
        $tpRecibo            = array_key_exists(4, $arrParams) ? $arrParams[4] : self::$TIPO_PETICIONAMENTO_RECIBO_PROCURACAOE_VINC_PJ;
        $idContato           = array_key_exists(5, $arrParams) ? $arrParams[5] : null;
        $unidadeDTO          = array_key_exists(6, $arrParams) ? $arrParams[6] : null;

        //consultar orgão externo
        $idOrgao = SessaoSEIExterna::getInstance()->getNumIdOrgaoUsuarioExterno();
        $orgaoDTO = new  OrgaoDTO();
        $orgaoRN = new  OrgaoRN();
        $orgaoDTO->setNumIdOrgao($idOrgao);
        $orgaoDTO->retNumIdOrgao();
        $orgaoDTO->retStrSigla();
        $orgaoDTO->retStrDescricao();
        $orgao = $orgaoRN->consultarRN1352($orgaoDTO);

        //Consultar usuario externo logado
        $idusuarioExterno = SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno();
        $usuarioDTO = new UsuarioDTO();
        $usuarioRN = new UsuarioRN();
        $usuarioDTO->setNumIdUsuario($idusuarioExterno);
        $usuarioDTO->retNumIdContato();

        $usuario = $usuarioRN->consultarRN0489($usuarioDTO);

        $contatoExternoDTO = new ContatoDTO();
        $contatoExternoRN = new ContatoRN();
        $contatoExternoDTO->setNumIdContato($usuario->getNumIdContato());
        $contatoExternoDTO->retStrNome();
        $contatoExterno = $contatoExternoRN->consultarRN0324($contatoExternoDTO);

        $objInfraParametro = new InfraParametro($this->getObjInfraIBanco());
        $url = dirname(__FILE__) . '/../md_pet_vinc_usu_ext_recibo_eletronico.php';
        $htmlModeloRecibo = file_get_contents($url);


        $objMdPetVincRepresentantDTO = $dadosProcuracao['Procuracao'];
        $dadosRepresentante = $dadosProcuracao['RepLegal'];
        $documento = $dadosProcuracao['Documento'];

        $htmlModeloRecibo = str_replace('@usuarioExterno', $contatoExterno->getStrNome(), $htmlModeloRecibo); //Nome do responsavel
        $htmlModeloRecibo = str_replace('@numRecibo', $reciboDTOBasico->getNumIdReciboPeticionamento(), $htmlModeloRecibo); //Nome do responsavel
        $htmlModeloRecibo = str_replace('@responsavelLegal', $dadosRepresentante->getStrNome(), $htmlModeloRecibo); //Nome do responsavel
        $htmlModeloRecibo = str_replace('@ipUtilizado', $reciboDTOBasico->getStrIpUsuario(), $htmlModeloRecibo); //ip usuario
        $htmlModeloRecibo = str_replace('@dataHorario', $reciboDTOBasico->getDthDataHoraRecebimentoFinal(), $htmlModeloRecibo); //data hora
        $htmlModeloRecibo = str_replace('@tipoProcesso', $reciboDTOBasico->getStrStaTipoPeticionamentoFormatado(), $htmlModeloRecibo); //tipo de processo
        $htmlModeloRecibo = str_replace('@numProcesso', $objProcedimentoDTO->getStrProtocoloProcedimentoFormatado(), $htmlModeloRecibo); //numero do processo
        $htmlModeloRecibo = str_replace('@cpfResponsavel',InfraUtil::formatarCpf($dadosRepresentante->getDblCpf()), $htmlModeloRecibo); // Nome do CPF
        $htmlModeloRecibo = str_replace('@cnpj',InfraUtil::formatarCnpj($objMdPetVincRepresentantDTO->getStrCNPJ()), $htmlModeloRecibo); // Cnpj do vinculo
        $htmlModeloRecibo = str_replace('@nomeRazaoSocial', $objMdPetVincRepresentantDTO->getStrRazaoSocialNomeVinc(), $htmlModeloRecibo); // Razao Social
        $htmlModeloRecibo = str_replace('@descricao_orgao@',$orgao->getStrDescricao(),$htmlModeloRecibo); // Descricao do Orgão


        $tblPessoaJuridica = '';
        $htmlModeloRecibo = str_replace('@tblPessoaJuridica', $tblPessoaJuridica, $htmlModeloRecibo);

        //Documento Principal
        $tblDocumentoPrincipal= '';
        $htmlModeloRecibo = str_replace('@tblDocumentoPrincipal', $tblDocumentoPrincipal, $htmlModeloRecibo);

        //Atos Constitutivos
        $tblAtos = '';
        $htmlModeloRecibo = str_replace('@tblAtosConstitutivos', $tblAtos, $htmlModeloRecibo); // Atos Constitutivos

        //Procurações Especiais - Revogação / Renúncia
        $objDocumentoRN = new DocumentoRN();
        $objDocumentoDTO = new DocumentoDTO();
        $objDocumentoDTO->retStrNomeSerie();
        $objDocumentoDTO->retStrProtocoloDocumentoFormatado();
        $objDocumentoDTO->retStrNumero();
        $objDocumentoDTO->setDblIdDocumento($documento->getIdDocumento());
        $arrObjDocumentoDTO = $objDocumentoRN->listarRN0008($objDocumentoDTO);
        
        $tblProcuracoes = '';
        if(count($arrObjDocumentoDTO)>0){
            $tblProcuracoes = '';
            $tblProcuracoes .= '    <tr>';
            $tblProcuracoes .= '        <td colspan="2" style="font-weight: bold;">Protocolos dos Documentos (Número SEI):</td>';
            $tblProcuracoes .= '    </tr>';
            $tblProcuracoes .= '    <tr>';
            $tblProcuracoes .= '        <td>&nbsp;&nbsp;&nbsp;&nbsp;- '.$arrObjDocumentoDTO[0]->getStrNomeSerie();
            $tblProcuracoes .= $arrObjDocumentoDTO[0]->getStrNumero()!='' ? ' '.$arrObjDocumentoDTO[0]->getStrNumero() : '';
            $tblProcuracoes .='</td>';
            $tblProcuracoes .= '        <td>'.$arrObjDocumentoDTO[0]->getStrProtocoloDocumentoFormatado().'</td>';
            $tblProcuracoes .= '    </tr>';
        }
        $htmlModeloRecibo = str_replace('@tblProcuracoes', $tblProcuracoes, $htmlModeloRecibo); // Procuracoes especiais

        //==========================================================================
        //incluindo doc recibo no processo via SEIRN
        //==========================================================================

        $idSerieRecibo = $objInfraParametro->getValor(MdPetIntSerieRN::$MD_PET_ID_SERIE_RECIBO);

        $objDocumentoAPI = new DocumentoAPI();
        $objDocumentoAPI->setIdProcedimento($objProcedimentoDTO->getDblIdProcedimento());
        $objDocumentoAPI->setSubTipo(DocumentoRN::$TD_FORMULARIO_AUTOMATICO);
        $objDocumentoAPI->setTipo(ProtocoloRN::$TP_DOCUMENTO_GERADO);
        $objDocumentoAPI->setIdSerie($idSerieRecibo);
        $objDocumentoAPI->setSinAssinado('N');
        $objDocumentoAPI->setSinBloqueado('S');
        $objDocumentoAPI->setIdHipoteseLegal(null);
        $objDocumentoAPI->setNivelAcesso(ProtocoloRN::$NA_PUBLICO);
        $objDocumentoAPI->setIdTipoConferencia(null);

        $objDocumentoAPI->setConteudo(base64_encode(utf8_encode($htmlModeloRecibo)));

        $objSeiRN = new SeiRN();
        $saidaDocExternoAPI = $objSeiRN->incluirDocumento($objDocumentoAPI);
        
        $participanteRN = new ParticipanteRN();
        $objParticipante = new ParticipanteDTO();
        $objParticipante->setDblIdProtocolo($saidaDocExternoAPI->getIdDocumento());
        $objParticipante->setNumIdContato($idContato);
        $objParticipante->setNumIdUnidade($unidadeDTO->getNumIdUnidade());
        $objParticipante->setStrStaParticipacao(ParticipanteRN::$TP_INTERESSADO);
        $objParticipante->setNumSequencia(0);      
        $participanteRN->cadastrarRN0170($objParticipante);
        
        $tpProtocolo = MdPetVincDocumentoRN::$TP_PROTOCOLO_RECIBO;
        $this->_adicionarDadosArquivoVinculacao($saidaDocExternoAPI->getIdDocumento(), $idRepresentante, $tpProtocolo);

        //necessario forçar update da coluna sta_documento da tabela documento
        //inclusao via SeiRN nao permitiu definir como documento de formulario automatico
        $parObjDocumentoDTO = new DocumentoDTO();
        $parObjDocumentoDTO->retTodos();
        $parObjDocumentoDTO->setDblIdDocumento($saidaDocExternoAPI->getIdDocumento());

        $docRN = new DocumentoRN();
        $parObjDocumentoDTO = $docRN->consultarRN0005($parObjDocumentoDTO);
        $parObjDocumentoDTO->setStrStaDocumento(DocumentoRN::$TD_FORMULARIO_AUTOMATICO);
        $objDocumentoBD = new DocumentoBD($this->getObjInfraIBanco());
        $objDocumentoBD->alterar($parObjDocumentoDTO);

        $reciboDTOBasico->setDblIdDocumento($saidaDocExternoAPI->getIdDocumento());

        $objBD = new MdPetReciboBD($this->getObjInfraIBanco());
        $objBD->alterar($reciboDTOBasico);
        return $reciboDTOBasico;

    }
}