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

        try {

            $objMdPetVincTpProcessoRN = new MdPetVincTpProcessoRN();
            $objMdPetVincTpProcessoDTO = new MdPetVincTpProcessoDTO();
            $objMdPetVincTpProcessoDTO->retTodos();

            $arrObjMdPetVincTpProcesso = $objMdPetVincTpProcessoRN->consultar($objMdPetVincTpProcessoDTO);

            if (is_null($arrObjMdPetVincTpProcesso)) {
                throw new InfraException('Vinculação não configurada');
            }

            //obtendo a unidade de abertura do processo
            $idTipoUnidadeAberturaProcesso = $arrObjMdPetVincTpProcesso->getNumIdUnidade();

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

            $txtTipoProcessoEscolhido = $objTipoProcedimentoDTO->getStrNome();

            //obter unidade configurada no "Tipo de Processo para peticionamento"
            $unidadeRN = new UnidadeRN();
            $unidadeDTO = new UnidadeDTO();
            $unidadeDTO->retTodos();
            $unidadeDTO->setNumIdUnidade($idTipoUnidadeAberturaProcesso);
            $unidadeDTO = $unidadeRN->consultarRN0125($unidadeDTO);

            if (is_null($unidadeDTO)) {
                throw new InfraException('Tipo de unidade não encontrada.');
            }

            if(isset($_POST['hdnTbUsuarioProcuracao'])) {
                $dadosProcuracao = PaginaSEIExterna::getInstance()->getArrItensTabelaDinamica($_POST['hdnTbUsuarioProcuracao']);
            }

            $objMdPetContatoRN = new MdPetContatoRN();
            $idTipoContatoUsExt = $objMdPetContatoRN->getIdTipoContatoUsExt();

            $contatoRN = new ContatoRN();
            $contatoDTO = new ContatoDTO();
            $contatoDTO->retNumIdContato();
            $contatoDTO->setNumIdTipoContato($idTipoContatoUsExt);
            $arrIdContato = [];

            foreach ($dadosProcuracao as $procuracao) {
                $cpf = InfraUtil::retirarFormatacao($procuracao[0]);
                $contatoDTO->setDblCpf($cpf);
                $contatoDTO->setStrNome($procuracao[1]);
                $arrContato = $contatoRN->listarRN0325($contatoDTO);
                $arrIdContato[] = $arrContato[0]->getNumIdContato();
            }

            //$arrIdContato = $dados['hdnIdContExterno'];
            SessaoSEI::getInstance()->simularLogin(null, null, SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno(), $unidadeDTO->getNumIdUnidade());

            // Recupera o idProcedimento(Processo) referente ao cnpj informado (idContato)
            $objMdPetVinculoDTO = new MdPetVinculoDTO();
            $objMdPetVinculoRN = new MdPetVinculoRN();

            $objMdPetVinculoDTO->setNumIdContato($dados['idContato']);
            $objMdPetVinculoDTO->retTodos();

            $mdPetVinculacao = $objMdPetVinculoRN->listar($objMdPetVinculoDTO);


            $objProcedimentoDTO = new ProcedimentoDTO();
            $objProcedimentoRN = new ProcedimentoRN();
            $objProcedimentoDTO->retStrProtocoloProcedimentoFormatado();
            $objProcedimentoDTO->retDblIdProcedimento();
            $objProcedimentoDTO->setDblIdProcedimento($mdPetVinculacao[0]->getDblIdProtocolo());

            $objProcedimentoDTO = $objProcedimentoRN->consultarRN0201($objProcedimentoDTO);

            $idProcedimento = $objProcedimentoDTO->getDblIdProcedimento();

            $objMdPetVinculoDTO = new MdPetVinculoDTO();
            $mdPetVinculacaoRN = new MdPetVinculoRN();

            $objMdPetVinculoDTO->setNumIdContato($dados['selPessoaJuridica']);
            $objMdPetVinculoDTO->retNumIdMdPetVinculo();
            $objMdPetVinculoDTO->setDblIdProtocolo($idProcedimento);

            $objMdPetVinculoDTO = $objMdPetVinculoRN->consultar($objMdPetVinculoDTO);
            $idVinculo = $objMdPetVinculoDTO->getNumIdMdPetVinculo();

            //RECUPERANDO DADOS DO REP-lEGAL E PESSOA jURIDICA
            $idContatoVinc = $dados['selPessoaJuridica'];
            $idResponsavelLegal = $dados['idContatoExterno'];

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
            $contatoDTO->setNumIdContato($idResponsavelLegal);

            $contatoRN = new ContatoRN();
            $Replegal = $contatoRN->consultarRN0324($contatoDTO);
            $dadosRetornoProcuracao['RepLegal'] = $Replegal;

            $numRegistro = count($arrIdContato);
            for($i= 0 ;$i <$numRegistro;$i++) {
                $dados['IdOutorgado']=$arrIdContato[$i];

                //Gerar Documento Nova Procuracao
                $params = array('dados'=>$dados,
                    'procedimento'=>$objProcedimentoDTO,
                    'idVinculo'=>$idVinculo,
                    'RepLegal'=>$Replegal,
                    'contatoPj'=>$contatoPj,
                    'idContato'=>$dados['idContato'],
                    'unidadeDTO'=>$unidadeDTO);
                $dadosRetornoProcuracao['Procuracao'][] = $this->gerarFormularioProcuracao($params);

            }

            //RECUPERA ID_MD_PET_REPRESENTANT DO VINCULO PRINCIPAL CASO TENHA MAIS DE UMA PROCURAÇÃO
            if($numRegistro>1) {
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

            if(!isset($params['tela'])) {

                //GERAR RECIBO
                //if(!(boolean)$params['dados']['selTipoProcuracao']){
                      $tipoPeticionamento = MdPetReciboRN::$TP_RECIBO_PROCURACAO_ELETRONICA_EMISSAO;
                //}
                $objMdPetVinculoUsuExtRN = new MdPetVinculoUsuExtRN();

                $reciboDTOBasico = $objMdPetVinculoUsuExtRN->salvarDadosReciboPeticionamento(array('idProcedimento' => $idProcedimento, 'staTipoPeticionamento' => $tipoPeticionamento));

                $recibo = $this->gerarReciboProcuracao(array($dadosRetornoProcuracao, $objProcedimentoDTO, $reciboDTOBasico, $idMdPetVinculoRepresent, $tipoPeticionamento, $unidadeDTO, $dados));
                $idDocumentoRecibo = $reciboDTOBasico->getDblIdDocumento();

                $mdPetVinculoUsuExtRN = new MdPetVinculoUsuExtRN();
                $objMdPetIntimacaoRN = new MdPetIntimacaoRN();
                $objProcedimentoDTO = $mdPetVinculoUsuExtRN->getProcedimento($idProcedimento);
                $objUnidadeDTO = $mdPetVinculoUsuExtRN->getUnidade();
                $arrParams = array();
                $arrParams[0] = $dados;
                $arrParams[1] = $objUnidadeDTO;
                $arrParams[2] = $objProcedimentoDTO;
                $arrParams[3] = array();
                $arrParams[4] = $reciboDTOBasico;
                $arrParams[5] = $reciboDTOBasico;
                
                $objMdPetVinculoUsuExtRN->gerarAndamentoVinculo(array($idProcedimento, ' de ' . $tipoPeticionamento, $idDocumentoRecibo, $unidadeDTO->getNumIdUnidade()));
                
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
                
                


                //Disponibilazando Acesso Externo ao Responsável Legal
                $objMdPetAcessoExternoRN = new MdPetAcessoExternoRN();
                $objMdPetAcessoExternoRN->gerarAcessoExternoVinculo(array($idVinculo, $objProcedimentoDTO->getDblIdProcedimento()));
                
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
    public function gerarProcedimentoVinculoProcuracaoMotivoControlado($dados)
    {

        try {
            $objMdPetVincTpProcessoRN = new MdPetVincTpProcessoRN();
            $objMdPetVincTpProcessoDTO = new MdPetVincTpProcessoDTO();
            $objMdPetVincTpProcessoDTO->retTodos();

            $arrObjMdPetVincTpProcesso = $objMdPetVincTpProcessoRN->consultar($objMdPetVincTpProcessoDTO);

            if (is_null($arrObjMdPetVincTpProcesso)) {
                throw new InfraException('Vinculação não configurada');
            }

            //obtendo a unidade de abertura do processo
            $idTipoUnidadeAberturaProcesso = $arrObjMdPetVincTpProcesso->getNumIdUnidade();

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

            $txtTipoProcessoEscolhido = $objTipoProcedimentoDTO->getStrNome();

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

        $htmlModeloRevogacao = str_replace('@razaoSocial',$objMdPetVincRepresentantDTO->getStrRazaoSocialNomeVinc(),$htmlModeloRevogacao);
        $htmlModeloRevogacao = str_replace('@cnpj',InfraUtil::formatarCnpj($objMdPetVincRepresentantDTO->getStrCNPJ()),$htmlModeloRevogacao);
        $htmlModeloRevogacao = str_replace('@nomeOutorgante',$Outorgante->getStrNome(),$htmlModeloRevogacao);
        $htmlModeloRevogacao = str_replace('@cpfOutorgante',InfraUtil::formatarCpf($Outorgante->getDblCpf()),$htmlModeloRevogacao);
        $htmlModeloRevogacao = str_replace('@nomeOutorgado',$objMdPetVincRepresentantDTO->getStrNomeProcurador(),$htmlModeloRevogacao);
        $htmlModeloRevogacao = str_replace('@cpfOutorgado',InfraUtil::formatarCpf($objMdPetVincRepresentantDTO->getStrCpfProcurador()),$htmlModeloRevogacao);
        $htmlModeloRevogacao = str_replace('@motivo',$dados['txtJustificativa'],$htmlModeloRevogacao);
        $htmlModeloRevogacao = str_replace('@data',$dataAtual,$htmlModeloRevogacao);
        $htmlModeloRevogacao = str_replace('@descricao_orgao@',$orgao->getStrDescricao(),$htmlModeloRevogacao);

        //==========================================================================
        //incluindo doc recibo no processo via SEIRN
        //===========================================================
        if($dados['hdnTpDocumento']=='renunciar')
            $idSerieFormulario = $objInfraParametro->getValor(MdPetAtualizadorSeiRN::$MD_PET_ID_SERIE_RENUNCIA);
        else
            $idSerieFormulario = $objInfraParametro->getValor(MdPetAtualizadorSeiRN::$MD_PET_ID_SERIE_REVOGACAO);

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

        $htmlModeloRevogacao = str_replace('@tipoProcuracao',$tipoProcuracao,$htmlModeloRevogacao);
        $htmlModeloRevogacao = str_replace('@numProcuracao',$numProcuracao,$htmlModeloRevogacao);
        //$htmlModeloRevogacao = str_replace('@numDoc',$numProcuracao,$htmlModeloRevogacao);
        $htmlModeloRevogacao = str_replace('@numProcesso',$numProcesso,$htmlModeloRevogacao);

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

        $mdPetVinculoUsuExtRN = new MdPetVinculoUsuExtRN();
        $objMdPetIntimacaoRN = new MdPetIntimacaoRN();
        $objProcedimentoDTO = $mdPetVinculoUsuExtRN->getProcedimento($idProcedimento);
        $objUnidadeDTO = $mdPetVinculoUsuExtRN->getUnidade();
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
        
        $objMdPetVinculoUsuExtRN->gerarAndamentoVinculo(array($idProcedimento, $tipoPeticionamento, $idDocumentoRecibo, $objUnidadeDTO->getNumIdUnidade()));

        //Disponibilazando Acesso Externo ao outros representantes
        $objMdPetAcessoExternoRN = new MdPetAcessoExternoRN();
        $objMdPetAcessoExternoRN->gerarAcessoExternoVinculo(array($idVinculo,$objProcedimentoDTO->getDblIdProcedimento()));
        
        $objAtividadeRN = new AtividadeRN();
        $objAtividadeRN->gerarInternaRN0727($objAtividadeDTO);
        return $objMdPetVincRepresentantDTO;
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
            $Replegal           = $params['RepLegal'];
            $contatoPj          = $params['contatoPj'];
            $idContato          = $params['idContato'];
            $unidadeDTO         = $params['unidadeDTO'];
            $isProcuracao       = true;
        }

        $objInfraParametro = new InfraParametro($this->getObjInfraIBanco());
        $url = dirname(__FILE__) . '/../md_pet_vinc_modelo_procuracao.php';
        $htmlModeloProcuracao = file_get_contents($url);

        if(!$isProcuracao) {
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
        }

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
        $htmlModeloProcuracao = str_replace('@cpfRespLegal',InfraUtil::formatarCpf($Replegal->getDblCpf()),$htmlModeloProcuracao);
        $htmlModeloProcuracao = str_replace('@nomeOutorgado',$Outorgante->getStrNome(),$htmlModeloProcuracao);
        $htmlModeloProcuracao = str_replace('@cpfOutorgado',InfraUtil::formatarCpf($Outorgante->getDblCpf()),$htmlModeloProcuracao);
        $htmlModeloProcuracao = str_replace('@data',$dataAtual,$htmlModeloProcuracao);
        $htmlModeloProcuracao = str_replace('@cpfOutorgado',InfraUtil::formatarCpf($Outorgante->getDblCpf()),$htmlModeloProcuracao);
        $htmlModeloProcuracao = str_replace('@sigla_orgao@',$orgao->getStrSigla(),$htmlModeloProcuracao);
        $htmlModeloProcuracao = str_replace('@descricao_orgao@',$orgao->getStrDescricao(),$htmlModeloProcuracao);

        //==========================================================================
        //incluindo doc recibo no processo via SEIRN
        //===========================================================

        $idSerieFormulario = $objInfraParametro->getValor(MdPetAtualizadorSeiRN::$MD_PET_ID_SERIE_PROCURACAOE);

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

        $objMdPetVincTpProcessoRN = new MdPetVincTpProcessoRN();
        $objMdPetVincTpProcessoDTO = new MdPetVincTpProcessoDTO();
        $objMdPetVincTpProcessoDTO->retTodos();

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


        //Adicionar Procuracao especial caso tenha sido selecionado.
        $dados = array('IdOutorgado'=>$idOutorgado,
                       'idContatoExterno'=>$idResponsavelLegal,
                       'selTipoProcuracao'=>$params['dados']['selTipoProcuracao']
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

        $idSerieFormulario = $objInfraParametro->getValor(MdPetAtualizadorSeiRN::$MD_PET_ID_SERIE_VINC_SUSPENSAO);

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

        $idSerieFormulario = $objInfraParametro->getValor(MdPetAtualizadorSeiRN::$MD_PET_ID_SERIE_VINC_RESTABELECIMENTO);

        
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

        $idSerieRecibo = $objInfraParametro->getValor(MdPetAtualizadorSeiRN::$MD_PET_ID_SERIE_RECIBO);

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
        $objParticipante->setNumIdContato($dados['idContato']);
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

        $idSerieRecibo = $objInfraParametro->getValor(MdPetAtualizadorSeiRN::$MD_PET_ID_SERIE_RECIBO);

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