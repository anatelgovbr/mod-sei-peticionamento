<?php
require_once dirname(__FILE__) . '/../../../SEI.php';

/**
 * Created by PhpStorm.
 * User: jhon.carvalho
 * Date: 27/12/2017
 * Time: 09:52
 */
class MdPetVinculoUsuExtRN extends InfraRN
{

    public static $ID_FIXO_MD_PET_VINCULO_USU_EXT = '1';
    public static $TIPO_PETICIONAMENTO_VINC_PJ = 'de Vinculação a Pessoa Jurídica';
    public static $TIPO_PETICIONAMENTO_ALTERACAO_VINC_PJ = 'de Alteração da Vinculação a Pessoa Jurídica';
    public static $TIPO_PETICIONAMENTO_RECIBO_VINC_PJ = 'Vinculação a Pessoa Jurídica';

    public function __construct()
    {
        parent::__construct();
    }

    protected function inicializarObjInfraIBanco()
    {
        return BancoSEI::getInstance();
    }

    /*
      protected function cadastrarControlado(MdPetVinculoUsuExtDTO $objMdPetVinculoUsuExtDTO)
      {

        try {

          SessaoSEI::getInstance()->validarAuditarPermissao('md_pet_vinculacao_usu_ext_cadastrar', __METHOD__, $objMdPetVinculoUsuExtDTO);

          if ($this->_validarExistenciaVinculoCadastrado() > 0) {

              $objMdPetVinculoUsuExt = $this->alterar($objMdPetVinculoUsuExtDTO);

          } else {

            $objMdPetVinculoUsuExtDTO->setNumIdMdPetVinculoUsuExt(self::$ID_FIXO_MD_PET_VINCULO_USU_EXT);
            $objMdPetVinculoUsuExtBD = new MdPetVinculoUsuExtBD($this->getObjInfraIBanco());
            $objMdPetVinculoUsuExt = $objMdPetVinculoUsuExtBD->cadastrar($objMdPetVinculoUsuExtDTO);
          }
          return $objMdPetVinculoUsuExt;
        } catch (Exception $e) {
          throw  new InfraException('Erro cadastrando vinculação PJ', $e);
        }
      }
    */

    /*
      protected function consultarConectado(MdPetVinculoUsuExtDTO $objMdPetVinculoUsuExtDTO)
      {
        try {

          // Valida Permissao
          SessaoSEI::getInstance()->validarAuditarPermissao('md_pet_vinculacao_usu_ext_cadastrar', __METHOD__, $objMdPetVinculoUsuExtDTO);


          $objMdPetVinculoUsuExtBD = new MdPetVinculoUsuExtBD($this->getObjInfraIBanco());
          $objMdPetVinculoUsuExt = $objMdPetVinculoUsuExtBD->consultar($objMdPetVinculoUsuExtDTO);

          return $objMdPetVinculoUsuExt;

        } catch (Exception $e) {
          throw new InfraException('Erro consultando Tamanho de Arquivo Permitido Peticionamento.', $e);
        }
      }
    */

    /*
      protected function listarConectado(MdPetVinculoUsuExtDTO $objMdPetVinculoUsuExtDTO)
      {
        try {
          // Valida Permissao
          SessaoSEI::getInstance()->validarAuditarPermissao('md_pet_vinculacao_usu_ext_cadastrar', __METHOD__, $objMdPetVinculoUsuExtDTO);


          $objMdPetVinculoUsuExtBD = new MdPetVinculoUsuExtBD($this->getObjInfraIBanco());
          $objMdPetVinculoUsuExt = $objMdPetVinculoUsuExtBD->listar($objMdPetVinculoUsuExtDTO);

          return $objMdPetVinculoUsuExt;

        } catch (Exception $e) {
          throw new InfraException('Erro consultando Tamanho de Arquivo Permitido Peticionamento.', $e);
        }
      }
    */

    /*
      protected function contarConectado(MdPetVinculoUsuExtDTO $objMdPetVinculoUsuExtDTO)
      {
        try {

          //Valida Permissao

          SessaoSEI::getInstance()->validarAuditarPermissao('md_pet_vinculacao_usu_ext_cadastrar', __METHOD__, $objMdPetVinculoUsuExtDTO);

          //Regras de Negocio
          //$objInfraException = new InfraException();

          //$objInfraException->lancarValidacoes();

          $objMdPetVinculoUsuExtDB = new MdPetVinculoUsuExtBD($this->getObjInfraIBanco());
          $ret = $objMdPetVinculoUsuExtDB->contar($objMdPetVinculoUsuExtDTO);


          return $ret;
        } catch (Exception $e) {
          throw new InfraException('Erro contando Vinculo Usuario .', $e);
        }
      }
    */

    /*
      protected function alterarControlado($objMdPetVinculoUsuExtDTO)
      {
        try {
          $objMdPetVinculoUsuExtDTO->setNumIdMdPetVinculoUsuExt(self::$ID_FIXO_MD_PET_VINCULO_USU_EXT);
          $objMdPetVinculoUsuExtBD = new MdPetVinculoUsuExtBD($this->getObjInfraIBanco());
          $objMdPetVinculoUsuExt = $objMdPetVinculoUsuExtBD->alterar($objMdPetVinculoUsuExtDTO);
          return $objMdPetVinculoUsuExt;
        } catch (Exception $e) {
          throw  new InfraException('Erro cadastrando vinculação PJ', $e);
        }
      }
    */

    protected function verificaMudancaResponsavelLegalConectado($post)
    {


        $idVinculo = isset($_GET['idVinculo']) ? $_GET['idVinculo'] : $_POST['hdnIdVinculo'];
        $cpfRespLegal = null;
        $mudancaResponsavel = false;

        $objMdPetVincRepresentantRN = new MdPetVincRepresentantRN();
        $objMdPetVincRepresentantDTO = new MdPetVincRepresentantDTO();
        $objMdPetVincRepresentantDTO->retStrNomeProcurador();
        $objMdPetVincRepresentantDTO->retStrCpfProcurador();
        $objMdPetVincRepresentantDTO->setNumIdMdPetVinculo($idVinculo);
        $objMdPetVincRepresentantDTO->setStrTipoRepresentante(MdPetVincRepresentantRN::$PE_RESPONSAVEL_LEGAL);
        $objMdPetVincRepresentantDTO->setStrSinAtivo('S');

        $objMdPetVincRepresentantDTO = $objMdPetVincRepresentantRN->consultar($objMdPetVincRepresentantDTO);
        if (!is_null($objMdPetVincRepresentantDTO)) {
            $cpfRespLegal = trim($objMdPetVincRepresentantDTO->getStrCpfProcurador());
            $cpfAtual = array_key_exists('txtNumeroCpfResponsavel', $_POST) ? InfraUtil::retirarFormatacao(trim($_POST['txtNumeroCpfResponsavel'])) : null;
        }


        if (!is_null($cpfRespLegal) & !is_null($cpfAtual) & $cpfRespLegal != $cpfAtual) {
            return $objMdPetVincRepresentantDTO;
        }

        return null;
    }

    /*
    private function _validarExistenciaVinculoCadastrado()
    {
        $objMdPetVinculoUsuExtDTO = new MdPetVinculoUsuExtDTO();
        $objMdPetVinculoUsuExtDTO->setNumIdMdPetVinculoUsuExt(self::$ID_FIXO_MD_PET_VINCULO_USU_EXT);
        $objMdPetVinculoUsuExtDTO->retNumIdMdPetVinculoUsuExt();
        return $this->consultarConectado($objMdPetVinculoUsuExtDTO);
    }
    */

    /**
     * Verifica e salva os dados do contato do CNPJ sendo vinculado
     */
    protected function salvarDadosContatoCnpjControlado($post)
    {
        $cnpj = InfraUtil::retirarFormatacao($post['txtNumeroCnpj']);
        $objMdPetVincTpProc = $this->getConfiguracaoVinculo();

        //Verifica se o CNPJ já é cadastrado como contato do módulo
        $objMdPetContatoRN = new MdPetContatoRN();
        $objContatoRN = new ContatoRN();
        $objContatoDTORetorno = $objMdPetContatoRN->getContatoInclusoModPet($cnpj);
        $idUsuario = SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno();
        $isAlteracao = array_key_exists('isAlteracaoCrud', $post) ? $post['isAlteracaoCrud'] && $post['isAlteracaoCrud'] : false;
        $dadosPj = current(PaginaSEIExterna::getInstance()->getArrItensTabelaDinamica($_POST['hdnInformacaoPj']));

        $objMdPetIntegracaoDTO = new MdPetIntegracaoDTO();
        $objMdPetIntegracaoDTO->setNumIdMdPetIntegFuncionalid(MdPetIntegFuncionalidRN::$ID_FUNCIONALIDADE_CNPJ_RECEITA_FEDERAL);
        $objMdPetIntegracaoDTO->retStrStaUtilizarWs();
        $objMdPetIntegracaoDTO = (new MdPetIntegracaoRN)->consultar($objMdPetIntegracaoDTO);
        $strUtilizarWs = $objMdPetIntegracaoDTO->getStrStaUtilizarWs() == 'S' ? true : false;
//  SÓ CADASTRO. FALTA ALTERAÇÃO

        if($strUtilizarWs){
            $novosDadosPj = array();
            unset($dadosPj[1]);
            foreach($dadosPj as $item){
                $novosDadosPj[] = $item;
            }
            $dadosPj = $novosDadosPj;
        }

	    // MAPEIA OS DADOS DA PJ
	    $nomeContato            = $dadosPj[0];
	    $endereco               = $dadosPj[3];
	    $enderecoPadrao         = str_replace($dadosPj[4], "", $endereco);
	    $complemento            = $dadosPj[5];
	    $bairro                 = $dadosPj[6];
	    $idUf                   = $dadosPj[7];
	    $idCidade               = $dadosPj[8];
	    $cep                    = $dadosPj[9];

	    $idTipoContato = $post['slTipoInteressado'];

        $objContatoDTO = new ContatoDTO();
        $objContatoDTO->setStrNome($nomeContato); // Array Razao Social
        $objContatoDTO->setDblCnpj($cnpj);
        $objContatoDTO->setStrSigla($post['txtNumeroCnpj']);
        $objContatoDTO->setStrStaNatureza(ContatoRN::$TN_PESSOA_JURIDICA); // Identifica que o contato é CNPJ
        if (strlen($endereco) > 130) {
            if(strlen($enderecoPadrao) > 130){
                $strEnderecoPadraoTratado = substr($enderecoPadrao, 0, 130 );
                $numEspacoBranco = strripos($strEnderecoPadraoTratado, ' ');
                $enderecoPadrao = substr($strEnderecoPadraoTratado, 0, $numEspacoBranco);
            }
            $objContatoDTO->setStrEndereco($enderecoPadrao);
        } else {
            $objContatoDTO->setStrEndereco($endereco);
        }
        if(strlen($complemento) > 130){
            $strComplementoTratado = substr($complemento, 0, 130 );
            $numEspacoBranco = strripos($strComplementoTratado, ' ');
            $complemento = substr($strComplementoTratado, 0, $numEspacoBranco);
        }
        $objContatoDTO->setStrComplemento($complemento); // Array Complemento do Endereco
        $objContatoDTO->setStrCep($cep); // Array Cep
        $objContatoDTO->setStrBairro($bairro); // Array Bairro
        $objContatoDTO->setStrSinAtivo('S');
        $objContatoDTO->setStrStaGenero(null);
        $objContatoDTO->setDblCpf(null);
        $objContatoDTO->setDblRg(null);
        $objContatoDTO->setStrOrgaoExpedidor(null);
        $objContatoDTO->setStrMatricula(null);
        $objContatoDTO->setStrMatriculaOab(null);
        $objContatoDTO->setDtaNascimento(null);
        $objContatoDTO->setNumIdCargo(null);
        $objContatoDTO->setStrNumeroPassaporte(null);
        $objContatoDTO->setNumIdPaisPassaporte(null);
        $objContatoDTO->setNumIdPais(ID_BRASIL); // IdBrasil
        $objContatoDTO->setNumIdUf($idUf); // Array UF
        $objContatoDTO->setNumIdCidade($idCidade);
        $objContatoDTO->setNumIdTipoContato($idTipoContato); // IdBrasil
        $objContatoDTO->setNumIdUsuarioCadastro($idUsuario);

        SessaoSEI::getInstance()->simularLogin(null, null, SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno(), $objMdPetVincTpProc->getNumIdUnidade());

        if (is_null($objContatoDTORetorno)) {
            $objContatoDTO->setNumIdContato(null);
            $objContatoDTO->setStrStaNaturezaContatoAssociado(null);
            $objContatoDTO->setNumIdContatoAssociado('');
            $objContatoDTO->setStrSinEnderecoAssociado('N');
            $objContatoDTO->setStrTelefoneComercial('');
            $objContatoDTO->setStrTelefoneCelular('');
            $objContatoDTO->setStrStaGenero(null);
            $objContatoDTO->setStrEmail('');
            $objContatoDTO->setDblCpf(null);
            $objContatoDTO->setDblRg(null);
            $objContatoDTO->setStrOrgaoExpedidor(null);
            $objContatoDTO->setStrMatricula(null);
            $objContatoDTO->setStrMatriculaOab(null);
            $objContatoDTO->setDtaNascimento(null);
            $objContatoDTO->setNumIdCargo(null);
            $objContatoDTO->setStrSitioInternet('');
            $objContatoDTO->setStrObservacao('');
            $objContatoDTO->setStrNumeroPassaporte(null);
            $objContatoDTO->setNumIdPaisPassaporte(null);
            $objContatoDTO = $objContatoRN->cadastrarRN0322($objContatoDTO);
        } else {

            $mdPetVinculoRN = new MdPetVinculoRN();
            $objMdPetVinculoDTO = new MdPetVinculoDTO();
            $objMdPetVinculoDTO->setNumIdMdPetVinculo($post['hdnIdVinculo']);
            $objMdPetVinculoDTO->setStrTipoRepresentante(MdPetVincRepresentantRN::$PE_RESPONSAVEL_LEGAL);
            $objMdPetVinculoDTO->setStrStaEstado(MdPetVincRepresentantRN::$RP_ATIVO);
            $objMdPetVinculoDTO->retNumIdContatoRepresentante();
            $objMdPetVinculoDTO = $mdPetVinculoRN->consultar($objMdPetVinculoDTO);

            $idUsuarioExterno = SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno();

            $usuarioRN = new UsuarioRN();
            $objUsuarioDTO = new UsuarioDTO();
            $objUsuarioDTO->setNumIdUsuario($idUsuarioExterno);
            $objUsuarioDTO->retNumIdContato();
            $objUsuarioDTO = $usuarioRN->consultarRN0489($objUsuarioDTO);

            if($objMdPetVinculoDTO){
                if($objUsuarioDTO->getNumIdContato() != $objMdPetVinculoDTO->getNumIdContatoRepresentante()){
                    $flag = true;
                } else {
                    $flag = false;
                }
            } else {
                $flag = true;
            }

            $tipoPeticionamento = $this->_getTipoPeticionamento($post);

            if ( ( ($post['hdnIdContatoNovo'] != '' || $flag) && ($post['isAlteracaoCrud'] || $post['hdnStaWebService']) ) || !$strUtilizarWs && $tipoPeticionamento == MdPetReciboRN::$TP_RECIBO_RESPONSAVEL_LEGAL_ALTERACAO ) {
                $objContatoDTO->setNumIdCargo('');
                $objContatoDTO->setStrStaGenero('');
                $objContatoDTO->setNumIdContato($objContatoDTORetorno->getNumIdContato());

                $validarContatoComExpedicaoAndamento = $this->_validarContatoComExpedicaoAndamento($objContatoDTORetorno);

                if(is_null($validarContatoComExpedicaoAndamento)){
                    $objContatoRN->alterarRN0323($objContatoDTO);
                }

            }
            $objContatoDTO = $objContatoDTORetorno;
        }

        $idContato = $objContatoDTO->getNumIdContato();

        return $idContato;

    }

    private function _getTipoProcesso($arrObjMdPetVincTpProcesso)
    {
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

    private function _getUnidade($arrObjMdPetVincTpProcesso)
    {
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

    private function _getDadosProcuracao()
    {
        $dadosProcuracao = PaginaSEIExterna::getInstance()->getArrItensTabelaDinamica($_POST['hdnTbUsuarioProcuracao']);

        $objMdPetContatoRN = new MdPetContatoRN();
        $idTipoContatoUsExt = $objMdPetContatoRN->getIdTipoContatoUsExt();

        $contatoRN = new ContatoRN();
        $objContatoDTO = new ContatoDTO();
        $objContatoDTO->setNumIdTipoContato($idTipoContatoUsExt);
        $objContatoDTO->retNumIdContato();
        $arrIdContato = [];
        foreach ($dadosProcuracao as $procuracao) {

            $idContato = $procuracao[0];
            $objContatoDTO->setNumIdContato($idContato);
            $objContatoDTORet = $contatoRN->consultarRN0324($objContatoDTO);
            $arrIdContato[] = $objContatoDTORet->getNumIdContato();
        }
        return $arrIdContato;
    }

    private function _gerarProcedimento($idTipoProcesso, $objUnidadeDTO,$arrObjMdPetVincTpProcesso,$dados){

        $objProcedimentoAPI = new ProcedimentoAPI();
        $objProcedimentoAPI->setIdTipoProcedimento($idTipoProcesso);
        $objProcedimentoAPI->setIdUnidadeGeradora($objUnidadeDTO->getNumIdUnidade());
        //ESPECIFICAÇÃO
        $contatoDTO = new ContatoDTO();
        $contatoDTO->retStrNome();
        $contatoDTO->retDblCnpj();
        $contatoDTO->retStrSigla();
        $contatoDTO->setNumIdContato($dados['idContato']);
        $contatoRN = new ContatoRN();
        $objContatoRN = $contatoRN->consultarRN0324($contatoDTO);

        $contatoAPI = new ContatoAPI();
        $contatoAPI->setSigla($objContatoRN->getStrSigla());
        $contatoAPI->setNome($objContatoRN->getStrNome());

        $especificacao = $arrObjMdPetVincTpProcesso->getStrEspecificacao();
        $nomeModificado = str_replace("@razao_social@",$objContatoRN->getStrNome(),$especificacao);
        $nome_cnpj = str_replace("@cnpj@",InfraUtil::formatarCnpj($objContatoRN->getDblCnpj()),$nomeModificado);

        //trata campo especificacao limite de 100 caracteres
        //Se o conteúdo for superior a 100 caracteres, deve ser considerado somente o conteúdo até a última palavra inteira antes do 100º caracter.
        $nome_cnpj = trim($nome_cnpj);
        if(strlen($nome_cnpj) > 100){
            $nome_cnpj = substr($nome_cnpj, 0, 100);
            $arrNomeCnpj =  explode(" ", $nome_cnpj, -1);
            $nome_cnpj =  implode(" ", $arrNomeCnpj);
        }

        $objProcedimentoAPI->setEspecificacao($nome_cnpj);
        $objProcedimentoAPI->setNumeroProtocolo('');
        $objProcedimentoAPI->setNivelAcesso(ProtocoloRN::$NA_PUBLICO);
        $objProcedimentoAPI->setIdHipoteseLegal(null);
        $objProcedimentoAPI->setInteressados(array($contatoAPI));

        $objEntradaGerarProcedimentoAPI = new EntradaGerarProcedimentoAPI();
        $objEntradaGerarProcedimentoAPI->setProcedimento($objProcedimentoAPI);

        $objSeiRN = new SeiRN();
        SessaoSEI::getInstance()->simularLogin(null, null, SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno(), $objUnidadeDTO->getNumIdUnidade());
        $objSaidaGerarProcedimentoAPI = new SaidaGerarProcedimentoAPI();
        $objSaidaGerarProcedimentoAPI = $objSeiRN->gerarProcedimento($objEntradaGerarProcedimentoAPI);

        return $objSaidaGerarProcedimentoAPI;
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
        $objParticipante->setNumIdContato($dados['idContato']);
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

        //Adicionar o Vinculo do contato com a pessoa juridica
        $objVinculo = $this->_adicionarVinculo($dados, $idProcedimento);
        $idVinculo = $objVinculo->getNumIdMdPetVinculo();
        $idRepresentante = $this->_adicionarProcuracaoEspecialRepresentante($idVinculo, $dados, false, false);

        $reciboDTOBasico = $this->salvarDadosReciboPeticionamento(array('idProcedimento' => $idProcedimento, 'staTipoPeticionamento' => MdPetReciboRN::$TP_RECIBO_RESPONSAVEL_LEGAL_INICIAL));

        //Documento Principal
        $objArquivoPrincipal = $this->_gerarFormularioVinculacao($dados, $objProcedimentoDTO, $reciboDTOBasico, $objUnidadeDTO, $idRepresentante, $idVinculo);

        $arrDados['objProcedimentoDTO'] = $objProcedimentoDTO;
        $arrDados['reciboDTOBasico'] = $reciboDTOBasico;
        $arrDados['idVinculo'] = $idVinculo;
        $arrDados['idRepresentante'] = $idRepresentante;
        $arrDados['objFormularioVinc'] = $objArquivoPrincipal;


        return $arrDados;
    }

    private function _getIdProcessoPorVinculo($idVinculo)
    {
        $idProcedimento = null;
        $objMdPetVinculoRN = new MdPetVinculoRN();
        $objMdPetVinculoDTO = new MdPetVinculoDTO();
        $objMdPetVinculoDTO->retDblIdProtocolo();
        $objMdPetVinculoDTO->setNumIdMdPetVinculo($idVinculo);

        $objMdPetVinculoDTO = $objMdPetVinculoRN->consultar($objMdPetVinculoDTO);

        if ($objMdPetVinculoDTO) {
            $idProcedimento = $objMdPetVinculoDTO->getDblIdProtocolo();
        }

        return $idProcedimento;
    }

    public function _realizarVinculosProcessoAlteracao($idVinculo, &$dados, $objUnidadeDTO, $reciboDTOBasico, $idProcedimento, $idRepresentant)
    {
        $alteradoRespLegal = $dados['isAlteradoRespLegal'];

        if (!is_null($idProcedimento)) {

            $objProcedimentoRN = new ProcedimentoRN();
            $objProcedimentoDTO = new ProcedimentoDTO();
            $objProcedimentoDTO->retTodos(true);
            $objProcedimentoDTO->setDblIdProcedimento($idProcedimento);

            $objProcedimentoDTO = $objProcedimentoRN->consultarRN0201($objProcedimentoDTO);

            //Gerar Novamente o formulario
            $objArquivoPrincipal = $this->_gerarFormularioVinculacao($dados, $objProcedimentoDTO, $reciboDTOBasico, $objUnidadeDTO, $idRepresentant, $idVinculo);

            $arrDados['objProcedimentoDTO'] = $objProcedimentoDTO;
            $arrDados['reciboDTOBasico'] = $reciboDTOBasico;
            $arrDados['idVinculo'] = $idVinculo;
            $arrDados['objFormularioVinc'] = $objArquivoPrincipal;

            return $arrDados;
        }
    }

    protected function getConfiguracaoVinculoConectado()
    {
        $objMdPetVincTpProcessoRN = new MdPetVincTpProcessoRN();
        $objMdPetVincTpProcessoDTO = new MdPetVincTpProcessoDTO();
        $objMdPetVincTpProcessoDTO->retTodos();
        $objMdPetVincTpProcessoDTO->setNumMaxRegistrosRetorno(1);

        $arrObjMdPetVincTpProcesso = $objMdPetVincTpProcessoRN->consultar($objMdPetVincTpProcessoDTO);

        if (is_null($arrObjMdPetVincTpProcesso)) {
            throw new InfraException('Vinculação não configurada');
        }

        return $arrObjMdPetVincTpProcesso;
    }

    private function _buscarDadosDoVinculo($idVinculo, $existePet)
    {


    }

    private function _verificaExistePeticionamento($isAlteracao, $isAlteradoRespLegal, $addDocumento)
    {
        $peticionar = false;

        if (!$isAlteracao) {
            $peticionar = true;
        }

        if ($isAlteradoRespLegal || $addDocumento) {
            $peticionar = true;
        }

        return $peticionar;
    }

    public function _getObjProcedimentoPorVinculo($idVinculo)
    {
        $idProcedimento = $this->_getIdProcessoPorVinculo($idVinculo);
        $objProcedimentoDTO = null;

        if (!is_null($idProcedimento)) {

            $objProcedimentoRN = new ProcedimentoRN();
            $objProcedimentoDTO = new ProcedimentoDTO();
            $objProcedimentoDTO->retTodos(true);
            $objProcedimentoDTO->setDblIdProcedimento($idProcedimento);

            $objProcedimentoDTO = $objProcedimentoRN->consultarRN0201($objProcedimentoDTO);
        }

        return $objProcedimentoDTO;
    }

    private function _consultarReciboVinculacao($idProcedimento)
    {

        $objRecibo = null;
        $objMdPetReciboRN = new MdPetReciboRN();
        $objMdPetReciboDTO = new MdPetReciboDTO();
        $objMdPetReciboDTO->setNumIdProtocolo($idProcedimento);
        $objMdPetReciboDTO->setOrdDthDataHoraRecebimentoFinal(InfraDTO::$TIPO_ORDENACAO_DESC);
        $objMdPetReciboDTO->setStrSinAtivo('S');
        $objMdPetReciboDTO->setStrStaTipoPeticionamento(MdPetReciboRN::$TP_RECIBO_VINCULACAO);
        $objMdPetReciboDTO->setNumMaxRegistrosRetorno(1);
        $objMdPetReciboDTO->retTodos();
        $count = $objMdPetReciboRN->contar($objMdPetReciboDTO);

        if ($count > 0) {
            $objRecibo = $objMdPetReciboRN->consultar($objMdPetReciboDTO);
        }

        return $objRecibo;
    }

    /**
     * @param $dados
     * @throws InfraException
     */
    public function gerarProcedimentoVinculoControlado($dados)
    {
        FeedSEIProtocolos::getInstance()->setBolAcumularFeeds(true);

        $retorno = $this->gerarProcedimentoInterno($dados);

        FeedSEIProtocolos::getInstance()->setBolAcumularFeeds(false);
        FeedSEIProtocolos::getInstance()->indexarFeeds();

        if ($dados['hdnIdContatoNovo'] == '' && $retorno['acessoExterno'] == true) {
            $arrParams = array();
            $arrParams[0] = $dados;
            $arrParams[1] = $this->getUnidade();
            $arrParams[2] = $this->getProcedimento($retorno['idProcedimento']);
            $arrParams[3] = array();
            $arrParams[4] = $retorno['reciboDTOBasico'];
            $arrParams[5] = $retorno['reciboDTOBasico'];

            $this->enviarEmail($arrParams);
        }

        return $retorno['reciboDTOBasico'];

    }


    /**
     * @param $dados
     * @throws InfraException
     */
    public function gerarProcedimentoInterno($dados)
    {
        try {

            $isAlteracao = false;
            $isAlteradoRespLegal = false;
            $arrObjMdPetVincTpProcesso = $this->getConfiguracaoVinculo();
            $arrDadosProcuracao = null;

            //Obtendo tipo do processo
            $objTipoProcedimentoDTO = $this->_getTipoProcesso($arrObjMdPetVincTpProcesso);
            $idTipoProcesso = $objTipoProcedimentoDTO->getNumIdTipoProcedimento();
            $txtTipoProcessoEscolhido = $objTipoProcedimentoDTO->getStrNome();
            $idVinculo = array_key_exists('hdnIdVinculo', $dados) && !empty($dados['hdnIdVinculo']) ? $dados['hdnIdVinculo'] : null;

            //obtendo a unidade de abertura do processo
            $objUnidadeDTO = $this->_getUnidade($arrObjMdPetVincTpProcesso);
            $acessoExterno = isset($dados['chkDeclaracao']) ? true : false;
            if (!empty(SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno())) {
                SessaoSEI::getInstance()->simularLogin(null, null, SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno(), $objUnidadeDTO->getNumIdUnidade());
            }

            $addDocumento = array_key_exists('hdnTbDocumento', $dados) && $dados['hdnTbDocumento'] != '' ? true : false;

            if ($dados['hdnIdContatoNovo'] == 1) {
                $objMdPetAcessoExternoRN = new MdPetAcessoExternoRN();
                $dados['hdnIdContatoNovo'] = $objMdPetAcessoExternoRN->_retornaIdContatoUsuarioExterno();
            }

            if (is_null($idVinculo)) {
                $arrDados = $this->_gerarProcessoNovo($idTipoProcesso, $objUnidadeDTO, $dados,$arrObjMdPetVincTpProcesso);
                $existePeticionamento = true;
                $idRepresentant = $arrDados['idRepresentante'];
            } else {


                $mdPetVinculoRepresentant = new MdPetVincRepresentantRN();
                $objMdPetVinculoRepresentantDTORL = new MdPetVincRepresentantDTO();
                $objMdPetVinculoRepresentantDTORL->setNumIdMdPetVinculo($idVinculo);
                $objMdPetVinculoRepresentantDTORL->setStrTipoRepresentante(MdPetVincRepresentantRN::$PE_RESPONSAVEL_LEGAL);
                $objMdPetVinculoRepresentantDTORL->setStrSinAtivo('S');
                $objMdPetVinculoRepresentantDTORL->retStrCpfProcurador();
                $arrObjMdPetVinculoRepresentantDTORL = $mdPetVinculoRepresentant->consultar($objMdPetVinculoRepresentantDTORL);

                $strCpfRespLegalAntigo = "";
                if ($arrObjMdPetVinculoRepresentantDTORL) {
                    $strCpfRespLegalAntigo = $arrObjMdPetVinculoRepresentantDTORL->getStrCpfProcurador();
                } else {
                    $strCpfRespLegalAntigo = $dados['txtCpfResponsavelAntigo'];
                }

                $isAlteracao = true;
                $isAlteradoRespLegal = $strCpfRespLegalAntigo != InfraUtil::retirarFormatacao($dados['txtNumeroCpfResponsavel']) ? true : false;
                $tipoPeticionamento = $strCpfRespLegalAntigo != InfraUtil::retirarFormatacao($dados['txtNumeroCpfResponsavel']) ? MdPetReciboRN::$TP_RECIBO_RESPONSAVEL_LEGAL_ALTERACAO : MdPetReciboRN::$TP_RECIBO_ATUALIZACAO_ATOS_CONSTITUTIVOS;
                $existePeticionamento = $this->_verificaExistePeticionamento($isAlteracao, $isAlteradoRespLegal, $addDocumento);
                $idProcedimento = $this->_getIdProcessoPorVinculo($idVinculo);
                $reciboDTOBasico = null;
                if ($acessoExterno == true) {
                    $reciboDTOBasico = $this->salvarDadosReciboPeticionamento(array('idProcedimento' => $idProcedimento, 'staTipoPeticionamento' => $tipoPeticionamento));
                }
                $idRepresentant = null;

                if ($isAlteradoRespLegal) {
                    $idRepresentant = $this->_adicionarProcuracaoEspecialRepresentante($idVinculo, $dados, $isAlteracao, $isAlteradoRespLegal);

                    $arrDados = $this->_realizarVinculosProcessoAlteracao($idVinculo, $dados, $objUnidadeDTO, $reciboDTOBasico, $idProcedimento, $idRepresentant);

                } else {
                    $arrDados['idVinculo'] = $idVinculo;
                    $idRepresentant = $this->_getIdRepresentanteAtivoPorVinculo($idVinculo);
                }

                if ($idRepresentant == null) {
                    throw new InfraException('Erro buscar Representante da Vinculação');
                }

                $objProcedimentoDTO = $this->_getObjProcedimentoPorVinculo($idVinculo);
                $arrDados['objProcedimentoDTO'] = $objProcedimentoDTO;
                $arrDados['reciboDTOBasico'] = $reciboDTOBasico;
            }

            $objArquivoPrincipal = array_key_exists('objFormularioVinc', $arrDados) ? $arrDados['objFormularioVinc'] : null;
            $idVinculo = $arrDados['idVinculo'];
            $objProcedimentoDTO = $arrDados['objProcedimentoDTO'];
            $reciboDTOBasico = $arrDados['reciboDTOBasico'];
            $idProcedimento = $objProcedimentoDTO->getDblIdProcedimento();

            $arrSeries = $this->_adicionarArquivosAtosContitutivos($dados, $objUnidadeDTO->getNumIdUnidade(), $objProcedimentoDTO, $reciboDTOBasico, $idRepresentant);

            $tpRecibo = 'Vinculação de Responsável Legal a Pessoa Jurídica';
            if (!$isAlteracao) {

                $arrDadosProcuracao = $this->_vincularProcuradores($objProcedimentoDTO, $idVinculo, $dados, $objUnidadeDTO);
                $documentoRN = new DocumentoRN();
                $objDocumentoDTO = new DocumentoDTO();
                $objDocumentoDTO->retStrNomeSerie();
                $objDocumentoDTO->setStrProtocoloDocumentoFormatado($objArquivoPrincipal->getDocumentoFormatado());
                $objDocumentoDTO = $documentoRN->consultarRN0005($objDocumentoDTO);
                $dados['nomeTipoDocumento'] = $objDocumentoDTO->getStrNomeSerie();

            } else {
                if ($isAlteradoRespLegal) {
                    $tpRecibo = ' - Alteração';
                    $documentoRN = new DocumentoRN();
                    $objDocumentoDTO = new DocumentoDTO();
                    $objDocumentoDTO->retStrNomeSerie();
                    $objDocumentoDTO->setStrProtocoloDocumentoFormatado($objArquivoPrincipal->getDocumentoFormatado());
                    $objDocumentoDTO = $documentoRN->consultarRN0005($objDocumentoDTO);
                    $dados['nomeTipoDocumento'] = $objDocumentoDTO->getStrNomeSerie() . ' - Alteração';
                }
            }

            // Atualizando Conteúdo
            $this->_atualizarConteudoFormulario($idVinculo, $dados, $arrSeries, $objArquivoPrincipal, $isAlteracao);

            if (!is_null($objArquivoPrincipal)) {
                // Assinando
                $parObjDocumentoDTO = new DocumentoDTO();
                $parObjDocumentoDTO->retTodos();
                $parObjDocumentoDTO->setDblIdDocumento($objArquivoPrincipal->getIdDocumento());
                //$parObjDocumentoDTO->setStrStaDocumento(DocumentoRN::$TD_EDITOR_INTERNO);

                $objDocumentoRN = new DocumentoRN();
                $parObjDocumentoDTO = $objDocumentoRN->consultarRN0005($parObjDocumentoDTO);

                $mdPetProcessoRN = new mdPetProcessoRN();
                $mdPetProcessoRN->assinarETravarDocumentoProcesso($objUnidadeDTO, $dados, $parObjDocumentoDTO, $objProcedimentoDTO);
            }

            $objMdPetRegrasGeraisRN = new MdPetRegrasGeraisRN();

            $tipoPeticionamento = !is_null($reciboDTOBasico) ? $reciboDTOBasico->getStrStaTipoPeticionamento() : null;
            $strTipoPeticionamento = $objMdPetRegrasGeraisRN->getTipoPeticionamento($tipoPeticionamento, true);
            $this->gerarAndamentoVinculo(array($idProcedimento, $strTipoPeticionamento, $idDocumentoRecibo, $objUnidadeDTO->getNumIdUnidade()));

            if ($isAlteradoRespLegal) {
                $this->_atualizarProcuradoresVinculo($idVinculo, $dados);
            }

            //Inclusão do Acesso Externo
            if (!is_null($idProcedimento)) {
                if (!empty($dados['hdnIdContato'])) {
                    $this->_gerarAcessoExterno($isAlteracao, $idVinculo, $idProcedimento, $dados['hdnIdContato']);
                } else {
                    $this->_gerarAcessoExterno($isAlteracao, $idVinculo, $idProcedimento);
                }
            }

	        //Gerar Recibo de vinculação do protocolo
	        if (!empty(SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno()) && $acessoExterno == true) {
		        $this->gerarReciboVinculacao(array($dados, $objProcedimentoDTO, $reciboDTOBasico, $objArquivoPrincipal, $idVinculo, $idRepresentant, $tpRecibo, $arrDadosProcuracao, $objUnidadeDTO));
	        }

            $this->_remeterProcesso($objProcedimentoDTO, $objUnidadeDTO);

            if ($isAlteracao && $isAlteradoRespLegal) {
                $this->setDataEncerramentoVinculo(array($idVinculo));
            }

            $arrRetorno = array(
                'reciboDTOBasico' => $reciboDTOBasico,
                'acessoExterno' => $acessoExterno,
                'idProcedimento' => $idProcedimento
            );

            return $arrRetorno;

        } catch (Exception $e) {
            throw new InfraException('Erro cadastrando processo peticionamento do SEI.', $e);
        }

    }

    private function _atualizarProcuradoresVinculo($idVinculo, $dados)
    {
        $idContato = array_key_exists('hdnIdContatoNovo', $dados) ? $dados['hdnIdContatoNovo'] : null;

        if (!is_null($idContato)) {
            $objMdPetVincRepresentantRN = new MdPetVincRepresentantRN();
            $objMdPetVincRepresentantDTO = new MdPetVincRepresentantDTO();
            $objMdPetVincRepresentantDTO->setNumIdMdPetVinculo($idVinculo);
            $objMdPetVincRepresentantDTO->setStrTipoRepresentante(MdPetVincRepresentantRN::$PE_PROCURADOR_ESPECIAL);
            $objMdPetVincRepresentantDTO->setStrSinAtivo('S');
            $objMdPetVincRepresentantDTO->retNumIdMdPetVinculoRepresent();

            $count = $objMdPetVincRepresentantRN->contar($objMdPetVincRepresentantDTO);

            if ($count > 0) {
                $arrObjMdPetVincRepresentantDTO = $objMdPetVincRepresentantRN->listar($objMdPetVincRepresentantDTO);
                foreach ($arrObjMdPetVincRepresentantDTO as $objMdPetVincRepresentantDTO) {
                    $objMdPetVincRepresentantDTO->setNumIdContatoOutorg($idContato);
                    $objMdPetVincRepresentantRN->alterar($objMdPetVincRepresentantDTO);
                }
            }
        }
    }

    private function _atualizarConteudoFormulario($idVinculo, $dados, $arrSeries, $objArquivoPrincipal, $isAlteracao)
    {

        if (!is_null($objArquivoPrincipal)) {
            $isWebService = $_POST['hdnIsWebServiceHabilitado'] == 1;

            $htmlModeloFormulario = $this->_getModeloFormulario($idVinculo, $dados, $arrSeries, $isAlteracao, $isWebService);

            // Atualização de conteúdo sem versão
            //$parObjDocumentoConteudoDTO = new DocumentoConteudoDTO();
            //$parObjDocumentoConteudoDTO->setStrConteudo($htmlModeloFormulario);
            //$parObjDocumentoConteudoDTO->setDblIdDocumento($objArquivoPrincipal->getIdDocumento());

            //$objDocumentoConteudoBD = new DocumentoConteudoBD($this->getObjInfraIBanco());
            //$objDocumentoConteudoBD->alterar($parObjDocumentoConteudoDTO);


            $objEditorDTO = new EditorDTO();

            $objEditorDTO->setDblIdDocumento($objArquivoPrincipal->getIdDocumento());
            $objEditorDTO->setNumIdBaseConhecimento(null);
            $objEditorDTO->setNumVersao(0);
            $objEditorDTO->setStrSinIgnorarNovaVersao('S');

            $objSecaoDocumentoDTO = new SecaoDocumentoDTO();
            $objSecaoDocumentoDTO->retNumIdSecaoModelo();
            $objSecaoDocumentoDTO->retStrNomeSecaoModelo();
            $objSecaoDocumentoDTO->retStrConteudo();
            $objSecaoDocumentoDTO->setDblIdDocumento($objArquivoPrincipal->getIdDocumento());
//    $objSecaoDocumentoDTO->setNumIdBaseConhecimento($parObjEditorDTO->getNumIdBaseConhecimento());
            $objSecaoDocumentoDTO->setStrSinAssinatura('N');

            $objSecaoDocumentoRN = new SecaoDocumentoRN();
            $arrObjSecaoDocumentoDTOBanco = $objSecaoDocumentoRN->listar($objSecaoDocumentoDTO);

            if (count($arrObjSecaoDocumentoDTOBanco)) {

                $arrObjSecaoDocumentoDTO = array();

                foreach ($arrObjSecaoDocumentoDTOBanco as $item) {
                    $objSecaoDocumentoDTO = new SecaoDocumentoDTO();
                    $objSecaoDocumentoDTO->setNumIdSecaoModelo($item->getNumIdSecaoModelo());

                    if ($item->getStrNomeSecaoModelo() == 'Título do Documento') {
                        $strTitulo = $item->getStrConteudo();
                        $strTitulo = str_replace('@serie@', $dados['nomeTipoDocumento'], $strTitulo);
                        $strTitulo = str_replace('@numeracao_serie@', $objArquivoPrincipal->getDocumentoFormatado(), $strTitulo);
                        $objSecaoDocumentoDTO->setStrConteudo($strTitulo);
                    }
                    if ($item->getStrNomeSecaoModelo() == 'Corpo do Texto') {
                        $objSecaoDocumentoDTO->setStrConteudo($htmlModeloFormulario);
                    }
                    $arrObjSecaoDocumentoDTO[] = $objSecaoDocumentoDTO;
                }

                $objEditorDTO->setArrObjSecaoDocumentoDTO($arrObjSecaoDocumentoDTO);

                try {
                    $objEditorRN = new EditorRN();
                    $numVersao = $objEditorRN->adicionarVersao($objEditorDTO);
                } catch (Exception $e) {
                    if ($e instanceof InfraException && $e->contemValidacoes()) {
                        die("INFRA_VALIDACAO\n" . $e->__toString()); //retorna para o iframe exibir o alert
                    }

                    PaginaSEI::getInstance()->processarExcecao($e); //vai para a página de erro padrão
                }

            }

        }

    }

    private function _getProtocoloFormatadoReciboPorIdDocumento($idDocumentoRecibo)
    {
        $objDocumentoDTO = new DocumentoDTO();
        $objDocumentoRN = new DocumentoRN();
        $objDocumentoDTO->setDblIdDocumento($idDocumentoRecibo);
        $objDocumentoDTO->retStrProtocoloDocumentoFormatado();
        $objDocumentoDTO = $objDocumentoRN->consultarRN0005($objDocumentoDTO);

        if (!is_null($objDocumentoDTO)) {

            return $objDocumentoDTO->getStrProtocoloDocumentoFormatado();
        }

        return '';
    }

    protected function gerarAndamentoVinculoConectado($arrParams)
    {

        $idProcedimento = array_key_exists(0, $arrParams) ? $arrParams[0] : null;
        $tipoPeticionamento = array_key_exists(1, $arrParams) ? $arrParams[1] : null;
        $idDocumentoRecibo = array_key_exists(2, $arrParams) ? $arrParams[2] : null;
        $idUnidade = array_key_exists(3, $arrParams) ? $arrParams[3] : null;
        $protocoloFormatado = array_key_exists(4, $arrParams) ? $arrParams[4] : null;

        if (is_null($protocoloFormatado)) {
            $protocoloFormatado = $this->_getProtocoloFormatadoReciboPorIdDocumento($idDocumentoRecibo);
        }

        $objEntradaLancarAndamentoAPI = new EntradaLancarAndamentoAPI();

        $objEntradaLancarAndamentoAPI->setIdProcedimento($idProcedimento);
        $objEntradaLancarAndamentoAPI->setIdTarefaModulo(MdPetIntDestRespostaRN::$ID_TAREFA_MODULO_RESPOSTA_EFETIVADO);
        $objMdPetRegrasGeraisRN = new MdPetRegrasGeraisRN();
        $arrObjAtributoAndamentoAPI = array();
        $arrObjAtributoAndamentoAPI[] = $objMdPetRegrasGeraisRN->_retornaObjAtributoAndamentoAPI('TIPO_PETICIONAMENTO', $tipoPeticionamento);
        $arrObjAtributoAndamentoAPI[] = $objMdPetRegrasGeraisRN->_retornaObjAtributoAndamentoAPI('USUARIO_EXTERNO_NOME', SessaoSEIExterna::getInstance()->getStrNomeUsuarioExterno());
        $arrObjAtributoAndamentoAPI[] = $objMdPetRegrasGeraisRN->_retornaObjAtributoAndamentoAPI('DOCUMENTO', $protocoloFormatado, $idDocumentoRecibo);

        $objEntradaLancarAndamentoAPI->setAtributos($arrObjAtributoAndamentoAPI);

        if (!empty(SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno())) {
            SessaoSEI::getInstance()->simularLogin(null, null, SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno(), $idUnidade);
        }

        $objSeiRN = new SeiRN();
        $objSeiRN->lancarAndamento($objEntradaLancarAndamentoAPI);
    }

    private function _gerarAcessoExterno($isAlteracao, $idVinculo, $idProcedimento, $idContato = null)
    {
        $objRN = new MdPetIntAceiteRN();
        if ($isAlteracao) {
            if (is_null($idContato)) {
                $objContato = $objRN->retornaObjContatoIdUsuario(array(SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno()));
                $idContato = $objContato->getNumIdContato();
            }
            $arrParams = array($idVinculo, $idProcedimento, $idContato);
        } else {
            $arrParams = array($idVinculo, $idProcedimento);
        }

        if (count($arrParams) > 0) {
            $objMdPetAcessoExtRN = new MdPetAcessoExternoRN();
            $objMdPetAcessoExtRN->gerarAcessoExternoVinculo($arrParams);
        }
    }

    protected function setDataEncerramentoVinculoControlado($arrDados)
    {

        $idVinculo = array_key_exists(0, $arrDados) ? $arrDados[0] : null;
        $tipoRepresentante = array_key_exists(1, $arrDados) ? $arrDados[1] : null;

        if (is_null($tipoRepresentante)) {
            $tipoRepresentante = MdPetVincRepresentantRN::$PE_RESPONSAVEL_LEGAL;
        }

        if (!is_null($idVinculo)) {
            $objMdPetVincRepresentantRN = new MdPetVincRepresentantRN();
            $objMdPetVincRepresentantDTO = new MdPetVincRepresentantDTO();
            $objMdPetVincRepresentantDTO->retTodos();
            $objMdPetVincRepresentantDTO->setNumIdMdPetVinculo($idVinculo);
            $objMdPetVincRepresentantDTO->setStrTipoRepresentante($tipoRepresentante);
            $objMdPetVincRepresentantDTO->setStrSinAtivo('N');
            $objMdPetVincRepresentantDTO->setStrStaEstado('T');
            $objMdPetVincRepresentantDTO->setDthDataEncerramento(null);
            $objMdPetVincRepresentantDTO->setBolExclusaoLogica(false);
            $objMdPetVincRepresentantDTO = $objMdPetVincRepresentantRN->consultar($objMdPetVincRepresentantDTO);

            if (!is_null($objMdPetVincRepresentantDTO)) {
                $idRepresentant = $objMdPetVincRepresentantDTO->getNumIdMdPetVinculoRepresent();
                $objMdPetVincRepresentantDTO->setDthDataEncerramento(InfraData::getStrDataHoraAtual());
                $objMdPetVincRepresentantRN->alterar($objMdPetVincRepresentantDTO);
            }
        }
    }

    private function _vincularProcuradores($objProcedimentoDTO, $idVinculo, &$dados, $unidadeDTO)
    {

        //Id Usuário Responsável Legal
        $idUsuario = SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno();
        $usuarioRN = new UsuarioRN();
        $usuarioDTO = new UsuarioDTO();
        $usuarioDTO->retNumIdContato();
        $usuarioDTO->setNumIdUsuario($idUsuario);
        $objUsuarioDTO = $usuarioRN->consultarRN0489($usuarioDTO);

        $dadosProcuracao = PaginaSEIExterna::getInstance()->getArrItensTabelaDinamica($_POST['hdnTbUsuarioProcuracao']);

        $objMdPetContatoRN = new MdPetContatoRN();
        $idTipoContatoUsExt = $objMdPetContatoRN->getIdTipoContatoUsExt();

        $contatoRN = new ContatoRN();
        $contatoDTO = new ContatoDTO();
        $contatoDTO->setNumIdTipoContato($idTipoContatoUsExt);
        $contatoDTO->retNumIdContato();

        $arrIdContato = [];

        foreach ($dadosProcuracao as $procuracao) {
//      $cpf = InfraUtil::retirarFormatacao($procuracao[1]);
            $cpf = InfraUtil::retirarFormatacao($procuracao[3]);

            $contatoDTO->setDblCpf($cpf);
            $arrContato = $contatoRN->listarRN0325($contatoDTO);
            $arrIdContato[] = $arrContato[0]->getNumIdContato();
        }

        $numRegistro = count($arrIdContato);

        $arrRetorno = $numRegistro > 0 ? array() : null;

        // Adicionar mais de uma procuração ao mesmo tempo
        for ($i = 0; $i < $numRegistro; $i++) {

            $dados['IdOutorgado'] = $arrIdContato[$i];
            $dados['outorgado'] = $dadosProcuracao[$i];
            $dados['IdRepresentanteLegal'] = $objUsuarioDTO->getNumIdContato();

            // Gerar um documento referente a procuração eletrônica especial
            $mdPetVinUsuExtProcRN = new  MdPetVinUsuExtProcRN();

            //Setando Params para criaçãodo documento Procuração

            $params = array('dados' => $dados,
                'procedimento' => $objProcedimentoDTO,
                'idVinculo' => $idVinculo,
                'idContato' => $dados['idContato'],
                'unidadeDTO' => $unidadeDTO,
                'tela' => 'vinculo');

            $saidaDocExternoAPI = $mdPetVinUsuExtProcRN->gerarFormularioProcuracao($params);
            $dados['idDocumentoProcuracao'] = $saidaDocExternoAPI->getIdDocumento();

            $arrRetorno[$i]['nome_outorgado'] = $dadosProcuracao[$i][1];
            $arrRetorno[$i]['cpf_outorgado'] = $dadosProcuracao[$i][3];
            $arrRetorno[$i]['protocolo_procur'] = $saidaDocExternoAPI->getDocumentoFormatado();
            $arrRetorno[$i]['id_protocolo_procur'] = $saidaDocExternoAPI->getIdDocumento();
            $arrRetorno[$i]['id_contato_repres'] = $dados['IdRepresentanteLegal'];
        }

        return $arrRetorno;
    }

    private function _getDadosContatoVinculoPJ($idVinculo)
    {

	    $contato = null;

	    $objMdPetVinculoDTO = new MdPetVinculoDTO();
	    $objMdPetVinculoDTO->setNumIdMdPetVinculo($idVinculo);
	    $objMdPetVinculoDTO->retNumIdContato();
	    $objMdPetVinculoDTO->setNumMaxRegistrosRetorno(1);
	    $objMdPetVinculoDTO = (new MdPetVinculoRN())->consultar($objMdPetVinculoDTO);

	    if($objMdPetVinculoDTO){

		    $objContatoDTO = new ContatoDTO();
		    $objContatoDTO->setNumIdContato($objMdPetVinculoDTO->getNumIdContato());
		    $objContatoDTO->retNumIdContato();
		    $objContatoDTO->retNumIdCidade();
		    $objContatoDTO->retStrNomeCidade();
		    $objContatoDTO->retNumIdUf();
		    $objContatoDTO->retStrSiglaUf();
		    $contato = (new ContatoRN())->consultarRN0324($objContatoDTO);

	    }

	    return $contato;

    }

    private function _getSeriesIds($arrSeries)
    {
        $arrRetorno = array();
        $objSerieRN = new SerieRN();
        $objSerieDTO = new SerieDTO();
        $objSerieDTO->setNumIdSerie($arrSeries, InfraDTO::$OPER_IN);
        $objSerieDTO->retStrNome();
        $objSerieDTO->retNumIdSerie();

        $arrSeries = $objSerieRN->listarRN0646($objSerieDTO);

        if (count($arrSeries) > 0) {
            foreach ($arrSeries as $objSerie) {
                $arrRetorno[$objSerie->getNumIdSerie()] = $objSerie->getStrNome();
            }
        }

        return $arrRetorno;
    }

    private function _getNomesSeriesDocsIncluidos($arrSeries)
    {
        $arrSeriesIds = $this->_getSeriesIds($arrSeries);

        $html = '<li>- @serieDoc@</li>';
        $strRetorno = '';

        if (count($arrSeries) > 0) {
            foreach ($arrSeries as $serie) {
                $nomeSerie = array_key_exists($serie, $arrSeriesIds) ? $arrSeriesIds[$serie] : '';
                $strRetorno .= str_replace('@serieDoc@', $nomeSerie, $html);
            }
        }

        return $strRetorno;
    }

    private function _getOrgaoUsuarioExterno()
    {
        $idOrgao = SessaoSEIExterna::getInstance()->getNumIdOrgaoUsuarioExterno();
        $orgaoDTO = new  OrgaoDTO();
        $orgaoRN = new  OrgaoRN();
        $orgaoDTO->setNumIdOrgao($idOrgao);
        $orgaoDTO->retNumIdOrgao();
        $orgaoDTO->retStrSigla();
        $orgaoDTO->retStrDescricao();
        $orgaoDTO->retStrTimbre();

        $orgao = $orgaoRN->consultarRN1352($orgaoDTO);

        return $orgao;
    }

    private function _getOrgaoInterno($idOrgao)
    {
        $orgaoDTO = new  OrgaoDTO();
        $orgaoRN = new  OrgaoRN();
        $orgaoDTO->setNumIdOrgao($idOrgao);
        $orgaoDTO->retNumIdOrgao();
        $orgaoDTO->retStrSigla();
        $orgaoDTO->retStrDescricao();
        $orgaoDTO->retStrTimbre();

        $orgao = $orgaoRN->consultarRN1352($orgaoDTO);

        return $orgao;
    }


    private function _getModeloFormulario($idVinculo, $dados, $arrSeries, $isAlteracao, $isWebService)
    {

        $serieDocs = count($arrSeries) > 0 ? $this->_getNomesSeriesDocsIncluidos($arrSeries) : '';

        //consultar orgão
        $orgao = $this->_getOrgaoUsuarioExterno();

        if ($orgao == null){
            $orgao = $this->_getOrgaoInterno($dados['selOrgao']);
        }

        $objContatoVincDTO = $this->_getDadosContatoVinculoPJ($idVinculo);

        if(is_null($objContatoVincDTO)){
	        throw new InfraException('Não foram encontrados os dados do Contato deste Vínculo.');
        }

        $url = dirname(__FILE__) . '/../md_pet_vinc_usu_ext_modelo_formulario.php';
        $htmlModeloFormulario = file_get_contents($url);

        $dadosPj = current(PaginaSEIExterna::getInstance()->getArrItensTabelaDinamica($_POST['hdnInformacaoPj']));


        $noUsuario =  SessaoSEIExterna::getInstance()->getStrNomeUsuarioExterno();
        if($noUsuario == null){
            $noUsuario = $dados['hdnNomeNovo'];
        }
        $cpf = InfraUtil::formatarCpf(InfraUtil::retirarFormatacao($dados['txtNumeroCpfResponsavel']));

        $nomeSubstituido = $isAlteracao ? $dados['NomeProcurador'] : '';
        $cpfSubstituido = $isAlteracao ? InfraUtil::formatarCpf(InfraUtil::retirarFormatacao($dados['CpfProcurador'])) : '';
        $numeroSEI = $dados['hdnNumeroSei'];
       
        if($isWebService) {
            $razaoSocial = $dadosPj[0];
            $endereco = $dadosPj[4];
//    $numero      = $isAlteracao ? $dadosPj[7] : $dadosPj[7];
//    $complemento = $isAlteracao ? $dadosPj[4] : $dadosPj[8];
            $bairro = $dadosPj[5];
            $cep = $dadosPj[8];
        }else{
            $razaoSocial = $dadosPj[0];
            $endereco = $dadosPj[3];
            $bairro = $dadosPj[6];
            $cep = $dadosPj[9];
            $uf = $dadosPj[7];
            $cidade = $dadosPj[8];
        }

        if (!$isAlteracao) {
            $htmlModeloFormulario = str_replace('@p_estilo_substituido', 'display: none;', $htmlModeloFormulario); //p mostra/oculta
            $htmlModeloFormulario = str_replace('@table_estilo_substituido', 'display: none;', $htmlModeloFormulario); //table mostra/oculta
            $vinculacao_substituicao = 'O presente formulário formaliza a vinculação do Usuário Externo abaixo citado como Responsável Legal da Pessoa Jurídica indicada junto ao(à) @descricao_orgao@ (@sigla_orgao@).';
        } else {
            $htmlModeloFormulario = str_replace('@p_estilo_substituido', '', $htmlModeloFormulario); //p mostra/oculta
            $htmlModeloFormulario = str_replace('@table_estilo_substituido', '', $htmlModeloFormulario); //table mostra/oculta
            $vinculacao_substituicao = 'O presente formulário formaliza a substituição do Usuário Externo abaixo citado como Responsável Legal da Pessoa Jurídica indicada junto ao(à) @descricao_orgao@ (@sigla_orgao@), encerrando o vínculo como Responsável Legal do Usuário Externo substituído, este passando a não mais atuar em nome da Pessoa Jurídica.';
        }
        $htmlModeloFormulario = str_replace('@vinculacao_substituicao@', $vinculacao_substituicao, $htmlModeloFormulario); //Descritivo Vinculação/Substituição

//    $htmlModeloFormulario = str_replace('@timbre_orgao@', '<img alt="Timbre" src="data:image/png;base64,' .$orgao->getStrTimbre(). '" />', $htmlModeloFormulario); // timbre orgao
//    $htmlModeloFormulario = str_replace('@descricao_orgao_maiusculas@',strtoupper($orgao->getStrDescricao()), $htmlModeloFormulario); // descrição orgao
        $htmlModeloFormulario = str_replace('@nomeSubstituido', $nomeSubstituido, $htmlModeloFormulario); // Nome do responsavel
        $htmlModeloFormulario = str_replace('@cpfSubstituido', $cpfSubstituido, $htmlModeloFormulario); // Nome do CPF
        $htmlModeloFormulario = str_replace('@nome', $noUsuario, $htmlModeloFormulario); // Nome do responsavel
        $htmlModeloFormulario = str_replace('@cpf', $cpf, $htmlModeloFormulario); // Nome do CPF
        $htmlModeloFormulario = str_replace('@cnpjVinculo', $dados['txtNumeroCnpj'], $htmlModeloFormulario); // Cnpj do vinculo
        $htmlModeloFormulario = str_replace('@razaoSocial', $razaoSocial, $htmlModeloFormulario); // Razao Social
        $htmlModeloFormulario = str_replace('@uf', $objContatoVincDTO->getStrSiglaUf(), $htmlModeloFormulario); // Uf
        $htmlModeloFormulario = str_replace('@cidade', $objContatoVincDTO->getStrNomeCidade(), $htmlModeloFormulario); // Cidade
        $htmlModeloFormulario = str_replace('@endereco', $endereco, $htmlModeloFormulario); // logradouro
        $htmlModeloFormulario = str_replace('@bairro', $bairro, $htmlModeloFormulario); // bairro do logradouro
        $htmlModeloFormulario = str_replace('@cep', $cep, $htmlModeloFormulario); // cep do logradouro
        $htmlModeloFormulario = str_replace('@descricao_orgao@', $orgao->getStrDescricao(), $htmlModeloFormulario);
        $htmlModeloFormulario = str_replace('@sigla_orgao@', $orgao->getStrSigla(), $htmlModeloFormulario); // orgao
        $htmlModeloFormulario = str_replace('@serie-docs@', $serieDocs, $htmlModeloFormulario); // documentos

        if (!is_null($numeroSEI)) {
            $motivo = 'Alteração realizada pela Administração do Sistema, em atendimento ao disposto no documento nº ' . $numeroSEI;
        } else {
            $motivo = $dados['txtMotivo'];
        }
        if ($motivo != '' && !is_null($motivo)) {
            $txtMotivo = '<fieldset id="motivo" style="font-size: 12pt; font-family: Calibri; text-align: justify; border-radius: 10px; margin-bottom: 4%">';
            $txtMotivo .= '<legend>Motivo</legend>';
            $txtMotivo .= '    <p style="font-size: 12pt; font-family: Calibri; text-align: justify; word-wrap: normal; text-indent: 0; margin: 6pt;}">' . $motivo . '</p>';
            $txtMotivo .= '</fieldset>';
        } else {
            $txtMotivo = '';
        }
        $htmlModeloFormulario = str_replace('@motivo', $txtMotivo, $htmlModeloFormulario); // motivo

        return $htmlModeloFormulario;
    }

    /**
     * gerar o Formulario de solicitacao
     */
    private function _gerarFormularioVinculacao($dados, $objProcedimentoDTO, $reciboDTO, $unidadeDTO, $idRepresentant, $idVinculo)
    {
        $objMdPetVincTpProcessoRN = new MdPetVincTpProcessoRN();
        $objMdPetVincTpProcessoDTO = new MdPetVincTpProcessoDTO();
        $objMdPetVincTpProcessoDTO->retNumIdUnidade();
        $objMdPetVincTpProcessoDTO->setStrTipoVinculo(MdPetVincTpProcessoRN::$ID_FIXO_MD_PET_VINCULO_USU_EXT);

        $objMdPetVincTpProcessoDTO->retStrDescricaoUnidade();
        $objMdPetVincTpProcessoDTO->retStrSiglaUnidade();

        $arrObjMdPetVincTpProcesso = $objMdPetVincTpProcessoRN->consultar($objMdPetVincTpProcessoDTO);

        $htmlModeloFormulario = '';
        $objInfraParametro = new InfraParametro($this->getObjInfraIBanco());
        //==========================================================================
        //incluindo doc recibo no processo via SEIRN
        //===========================================================
        $idSerieFormulario = $objInfraParametro->getValor(MdPetIntSerieRN::$MD_PET_ID_SERIE_FORMULARIO);

        $objDocumentoAPI = new DocumentoAPI();
        $objDocumentoAPI->setIdProcedimento($objProcedimentoDTO->getDblIdProcedimento());
        $objDocumentoAPI->setTipo(ProtocoloRN::$TP_DOCUMENTO_GERADO);
        $objDocumentoAPI->setSubTipo(DocumentoRN::$TD_EDITOR_INTERNO /*DocumentoRN::$TD_FORMULARIO_GERADO*/);
        $objDocumentoAPI->setIdSerie($idSerieFormulario);

//    $objDocumentoAPI->setConteudo($htmlModeloFormulario);

        $objSeiRN = new SeiRN();
        $saidaDocExternoAPI = $objSeiRN->incluirDocumento($objDocumentoAPI);


        $participanteRN = new ParticipanteRN();
        $objParticipante = new ParticipanteDTO();
        $objParticipante->setDblIdProtocolo($saidaDocExternoAPI->getIdDocumento());
        if (isset($dados['isAlteradoRespLegal'])) {
            $objParticipante->setNumIdContato($dados['hdnIdContatoNovo']);
        } else {
            $objParticipante->setNumIdContato($dados['idContato']);
        }

        $objParticipante->setNumIdUnidade($unidadeDTO->getNumIdUnidade());
        $objParticipante->setStrStaParticipacao(ParticipanteRN::$TP_INTERESSADO);
        $objParticipante->setNumSequencia(0);
        $participanteRN->cadastrarRN0170($objParticipante);
//    $parObjDocumentoDTO = new DocumentoDTO();
//    $parObjDocumentoDTO->retTodos();
//    $parObjDocumentoDTO->setDblIdDocumento($saidaDocExternoAPI->getIdDocumento());
//    $parObjDocumentoDTO->setStrStaDocumento(DocumentoRN::$TD_EDITOR_INTERNO);

//    $mdPetProcessoRN = new mdPetProcessoRN();
//    $mdPetProcessoRN->assinarETravarDocumentoProcesso($unidadeDTO, $dados, $parObjDocumentoDTO, $objProcedimentoDTO);

        // Forçando o STADOCUMENTO
        $objDocumentoDTO = new DocumentoDTO();
        $objDocumentoDTO->retTodos();
        $objDocumentoDTO->retStrProtocoloDocumentoFormatado();
        $objDocumentoDTO->retStrConteudo();
        $objDocumentoDTO->setDblIdDocumento($saidaDocExternoAPI->getIdDocumento());
        $objDocumentoDTO->setStrStaDocumento(DocumentoRN::$TD_EDITOR_INTERNO);
        $objDocumentoRN = new DocumentoRN();
        $objDocumentoDTO = $objDocumentoRN->consultarRN0005($objDocumentoDTO);

        $objDocumentoBD = new DocumentoBD($this->getObjInfraIBanco());
        $objDocumentoBD->alterar($objDocumentoDTO);

        $tpProtocolo = MdPetVincDocumentoRN::$TP_PROTOCOLO_PRINCIPAL;
        $this->_adicionarDadosArquivoVinculacao($saidaDocExternoAPI->getIdDocumento(), $idRepresentant, $tpProtocolo);

        return $saidaDocExternoAPI;

    }

    public function _getIdRepresentanteAtivoPorVinculo($idVinculo)
    {
        $objMdPetVincRepresentantRN = new MdPetVincRepresentantRN();
        $objMdPetVincRepresentantDTO = new MdPetVincRepresentantDTO();
        $objMdPetVincRepresentantDTO->retNumIdMdPetVinculoRepresent();
        $objMdPetVincRepresentantDTO->setStrTipoRepresentante(MdPetVincRepresentantRN::$PE_RESPONSAVEL_LEGAL);
        $objMdPetVincRepresentantDTO->setNumIdMdPetVinculo($idVinculo);
        $objMdPetVincRepresentantDTO->setStrSinAtivo('S');

        $objMdPetVincRepresentantDTO = $objMdPetVincRepresentantRN->consultar($objMdPetVincRepresentantDTO);

        if ($objMdPetVincRepresentantDTO) {
            return $objMdPetVincRepresentantDTO->getNumIdMdPetVinculoRepresent();
        } else {
            return null;
        }
    }

    protected function salvarDadosReciboPeticionamentoControlado($dados)
    {
        $objMdPetReciboDTO = new MdPetReciboDTO();
        $objMdPetReciboDTO->retTodos();

        $objMdPetReciboDTO->setNumIdProtocolo($dados['idProcedimento']);
        if (!empty(SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno())) {
            $objMdPetReciboDTO->setNumIdUsuario(SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno());
        } else {
            $objMdPetReciboDTO->setNumIdUsuario(SessaoSEI::getInstance()->getNumIdUsuario());
        }
        $objMdPetReciboDTO->setDthDataHoraRecebimentoFinal(InfraData::getStrDataHoraAtual());
        $objMdPetReciboDTO->setStrIpUsuario(InfraUtil::getStrIpUsuario());
        $objMdPetReciboDTO->setStrSinAtivo('S');
        $objMdPetReciboDTO->setStrStaTipoPeticionamento($dados['staTipoPeticionamento']);
        $objBD = new MdPetReciboBD($this->getObjInfraIBanco());
        $ret = $objBD->cadastrar($objMdPetReciboDTO);

        return $ret;

    }

    private function _remeterProcesso($objProcedimentoDTO, $unidadeDTO)
    {
        $atividadeRN = new AtividadeRN();
        $atividadeBD = new AtividadeBD($this->getObjInfraIBanco());

        // Andamento - Processo remetido pela unidade
        $arrObjAtributoAndamentoDTO = array();
        $objAtributoAndamentoDTO = new AtributoAndamentoDTO();
        $objAtributoAndamentoDTO->setStrNome('UNIDADE');
        $objAtributoAndamentoDTO->setStrValor($unidadeDTO->getStrSigla() . '¥' . $unidadeDTO->getStrDescricao());
        $objAtributoAndamentoDTO->setStrIdOrigem($unidadeDTO->getNumIdUnidade());
        $arrObjAtributoAndamentoDTO[] = $objAtributoAndamentoDTO;

        $objAtividadeDTO = new AtividadeDTO();
        $objAtividadeDTO->setDblIdProtocolo($objProcedimentoDTO->getDblIdProcedimento());
        $objAtividadeDTO->setNumIdUnidade($unidadeDTO->getNumIdUnidade());
        $objAtividadeDTO->setNumIdUnidadeOrigem($unidadeDTO->getNumIdUnidade());
//    $objAtividadeDTO->setNumIdUsuario($objAtividadeDTO->getNumIdUsuario());
//    $objAtividadeDTO->setNumIdUsuarioOrigem($objAtividadeDTO->getNumIdUsuarioOrigem());
        //$objAtividadeDTO->setDtaPrazo($objAtividadeDTO->getDtaPrazo());
        $objAtividadeDTO->setArrObjAtributoAndamentoDTO($arrObjAtributoAndamentoDTO);
        $objAtividadeDTO->setNumIdTarefa(TarefaRN::$TI_PROCESSO_REMETIDO_UNIDADE);

        $objAtividadeRN = new AtividadeRN();
        $objAtividadeRN->gerarInternaRN0727($objAtividadeDTO);

        // obtendo a ultima atividade informada para o processo, para marcar
        // como nao visualizada, deixando assim o processo marcado como "vermelho"
        // (status de Nao Visualizado) na listagem da tela "Controle de processos"
        $atividadeDTO = new AtividadeDTO();
        $atividadeDTO->retTodos();
        $atividadeDTO->setDblIdProtocolo($objProcedimentoDTO->getDblIdProcedimento());
        $atividadeDTO->setOrd("IdAtividade", InfraDTO::$TIPO_ORDENACAO_DESC);
        $ultimaAtividadeDTO = $atividadeRN->listarRN0036($atividadeDTO);

        //alterar a ultima atividade criada para nao visualizado
        if ($ultimaAtividadeDTO != null && count($ultimaAtividadeDTO) > 0) {
            $ultimaAtividadeDTO[0]->setNumTipoVisualizacao(AtividadeRN::$TV_NAO_VISUALIZADO);
            $atividadeBD->alterar($ultimaAtividadeDTO[0]);
        }
    }

    private function _adicionarArquivosAtosContitutivos($dados, $idUnidade, $objProcedimentoDTO, $reciboDTOBasico, $idRepresentant)
    {

        $arrSeries = array();
        $unidadeRN = new UnidadeRN();
        $unidadeDTO = new UnidadeDTO();
        $unidadeDTO->retTodos();
        $unidadeDTO->setNumIdUnidade($idUnidade);
        $unidadeDTO = $unidadeRN->consultarRN0125($unidadeDTO);

        $anexoRN = new MdPetAnexoRN();
        $strSiglaUsuario = SessaoSEIExterna::getInstance()->getStrSiglaUsuarioExterno();

        $objMdPetTamanhoArquivoRN = new MdPetTamanhoArquivoRN();
        $objMdPetTamanhoArquivoDTO = new MdPetTamanhoArquivoDTO();
        $objMdPetTamanhoArquivoDTO->setStrSinAtivo('S');
        $objMdPetTamanhoArquivoDTO->retTodos();

        $arrTamanhoDTO = $objMdPetTamanhoArquivoRN->listarTamanhoMaximoConfiguradoParaUsuarioExterno($objMdPetTamanhoArquivoDTO);
        $tamanhoPrincipal = $arrTamanhoDTO[0]->getNumValorDocPrincipal();
        $tamanhoEssencialComplementar = $arrTamanhoDTO[0]->getNumValorDocComplementar();

        $arrLinhasAnexos = PaginaSEI::getInstance()->getArrItensTabelaDinamica($dados['hdnTbDocumento']);

        $contador = 0;
        $mdPetProcessoRN = new mdPetProcessoRN();
        $idHipoteseLegal = null;
        foreach ($arrLinhasAnexos as $itemAnexo) {

            $idSerieAnexo = $itemAnexo[1];
            $strComplemento = $itemAnexo[2];
            $idTipoConferencia = $itemAnexo[6];
            $nomeArquivo = $itemAnexo[7];
            $nome = $itemAnexo[9];

            $idNivelAcesso = null;

            if ($itemAnexo[13] == "Público") {
                $idNivelAcesso = ProtocoloRN::$NA_PUBLICO;
                $idHipoteseLegal = null;
            } else if ($itemAnexo[13] == "Restrito") {
                $idNivelAcesso = ProtocoloRN::$NA_RESTRITO;
                if($arrLinhasAnexos[$contador][4] != '') {
                    $idHipoteseLegal = $arrLinhasAnexos[$contador][4];
                }
            }

            $idGrauSigilo = null;

            $idProcedimento = $objProcedimentoDTO->getDblIdProcedimento();

            //criando registro em protocolo
            $objDocumentoDTO = new DocumentoDTO();
            $objDocumentoDTO->setStrNumero($strComplemento);
            $objDocumentoDTO->setDblIdDocumento(null);
            $objDocumentoDTO->setDblIdProcedimento($idProcedimento);

            $objDocumentoDTO->setNumIdSerie($idSerieAnexo);
            $objDocumentoDTO->setNumIdHipoteseLegalProtocolo($idHipoteseLegal);
            $objDocumentoDTO->setStrStaNivelAcessoLocalProtocolo($idNivelAcesso);

            $objDocumentoDTO->setDblIdDocumentoEdoc(null);
            $objDocumentoDTO->setDblIdDocumentoEdocBase(null);
            $objDocumentoDTO->setNumIdUnidadeResponsavel(SessaoSEI::getInstance()->getNumIdUnidadeAtual());
            $objDocumentoDTO->setNumIdTipoConferencia($idTipoConferencia);
            $objDocumentoDTO->setStrSinBloqueado('S');
            $objDocumentoDTO->setStrStaDocumento(DocumentoRN::$TD_EXTERNO);


            array_push($arrSeries, $idSerieAnexo);

            $arrObjUnidadeDTOReabertura = array();
            //se setar array da unidade pode cair na regra: "Unidade <nome-Unidade> não está sinalizada como protocolo."
            //nao esta fazendo reabertura de processo - trata-se de processo novo
            $objDocumentoDTO->setArrObjUnidadeDTO($arrObjUnidadeDTOReabertura);


            $objDocumentoDTO->setNumIdTextoPadraoInterno('');
            $objDocumentoDTO->setStrProtocoloDocumentoTextoBase('');
            $objDocumentoDTO->setNumIdSerie($idSerieAnexo);

            /***/

            $objDocumentoAPI = new DocumentoAPI();
            $objDocumentoAPI->setIdProcedimento($idProcedimento);
            $objDocumentoAPI->setTipo(ProtocoloRN::$TP_DOCUMENTO_RECEBIDO);
            $objDocumentoAPI->setIdSerie($objDocumentoDTO->getNumIdSerie());
            $objDocumentoAPI->setData(InfraData::getStrDataAtual());
            $objDocumentoAPI->setSinAssinado('S');
            $objDocumentoAPI->setSinBloqueado('S');
            $objDocumentoAPI->setIdHipoteseLegal($objDocumentoDTO->getNumIdHipoteseLegalProtocolo());
            $objDocumentoAPI->setNivelAcesso($objDocumentoDTO->getStrStaNivelAcessoLocalProtocolo());
            $objDocumentoAPI->setIdTipoConferencia($objDocumentoDTO->getNumIdTipoConferencia());


            $objDocumentoAPI->setNomeArquivo($nome);
            $objDocumentoAPI->setConteudo(base64_encode(file_get_contents(DIR_SEI_TEMP . '/' . $nomeArquivo)));

            $objSeiRN = new SeiRN();
            $objSaidaDocumentoAPI = $objSeiRN->incluirDocumento($objDocumentoAPI);

            $participanteRN = new ParticipanteRN();
            $objParticipante = new ParticipanteDTO();
            $objParticipante->setDblIdProtocolo($objSaidaDocumentoAPI->getIdDocumento());
            $objParticipante->setNumIdContato($dados['idContato']);
            $objParticipante->setNumIdUnidade($unidadeDTO->getNumIdUnidade());
            $objParticipante->setStrStaParticipacao(ParticipanteRN::$TP_INTERESSADO);
            $objParticipante->setNumSequencia(0);
            $participanteRN->cadastrarRN0170($objParticipante);

            $idUsuarioLogado = $this->_getIdContatoPorUsuario();

            $objParticipante = new ParticipanteDTO();
            $objParticipante->setDblIdProtocolo($objSaidaDocumentoAPI->getIdDocumento());
            $objParticipante->setNumIdContato($idUsuarioLogado);
            $objParticipante->setNumIdUnidade($unidadeDTO->getNumIdUnidade());
            $objParticipante->setStrStaParticipacao(ParticipanteRN::$TP_REMETENTE);
            $objParticipante->setNumSequencia(0);
            $participanteRN->cadastrarRN0170($objParticipante);

            $tpProtocolo = MdPetVincDocumentoRN::$TP_PROTOCOLO_ATOS;

            $this->_adicionarDadosArquivoVinculacao($objSaidaDocumentoAPI->getIdDocumento(), $idRepresentant, $tpProtocolo);


            $idDocumentoAnexo = $objSaidaDocumentoAPI->getIdDocumento();
            $objDocumentoDTO->setDblIdDocumento($idDocumentoAnexo);
            $mdPetProcessoRN->assinarETravarDocumentoProcesso($unidadeDTO, $dados, $objDocumentoDTO, $objProcedimentoDTO);

            //adiciona o doc no recibo pesquisavel
            //recibo do doc principal para consultar do usuario externo
            $objMdPetRelReciboDocumentoAnexoDTO = new MdPetRelReciboDocumentoAnexoDTO();
            $objMdPetRelReciboDocumentoAnexoRN = new MdPetRelReciboDocumentoAnexoRN();

            $objMdPetRelReciboDocumentoAnexoDTO->setNumIdAnexo(null);
            $objMdPetRelReciboDocumentoAnexoDTO->setNumIdReciboPeticionamento($reciboDTOBasico->getNumIdReciboPeticionamento());
            $objMdPetRelReciboDocumentoAnexoDTO->setNumIdDocumento($idDocumentoAnexo);
            $objMdPetRelReciboDocumentoAnexoDTO->setStrClassificacaoDocumento(MdPetRelReciboDocumentoAnexoRN::$TP_VINCULACAO);
            $objMdPetRelReciboDocumentoAnexoRN->cadastrar($objMdPetRelReciboDocumentoAnexoDTO);

            $arrAnexoEssencialVinculacaoProcesso[] = $itemAnexo;
            $arrIdAnexoEssencial[] = $idDocumentoAnexo;
            //$arrIdAnexoEssencial[] = $itemAnexo->getNumIdAnexo();
            $contador++;

        }

        return $arrSeries;
    }

    public function gerarReciboVinculacaoConectado($arrParams)
    {

        $dados = array_key_exists(0, $arrParams) ? $arrParams[0] : null;
        $objProcedimentoDTO = array_key_exists(1, $arrParams) ? $arrParams[1] : null;
        $reciboDTOBasico = array_key_exists(2, $arrParams) ? $arrParams[2] : null;
        $objArquivoPrincipal = array_key_exists(3, $arrParams) ? $arrParams[3] : null;
        $idVinculo = array_key_exists(4, $arrParams) ? $arrParams[4] : null;
        $idRepresentante = array_key_exists(5, $arrParams) ? $arrParams[5] : null;
        $tpRecibo = array_key_exists(6, $arrParams) ? $arrParams[6] : self::$TIPO_PETICIONAMENTO_RECIBO_VINC_PJ;
        $arrProcuracao = array_key_exists(7, $arrParams) ? $arrParams[7] : null;
        $objUnidade = array_key_exists(8, $arrParams) ? $arrParams[8] : null;

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

        $dadosPj = current(PaginaSEIExterna::getInstance()->getArrItensTabelaDinamica($_POST['hdnInformacaoPj']));

        $noUsuario = SessaoSEIExterna::getInstance()->getStrNomeUsuarioExterno();

        $tblPessoaJuridica = '
    <tr>
    <td colspan="2" style="font-weight: bold;">Pessoa Jurídica e Responsável Legal:</td>
    </tr>
    <tr>
    <td>&nbsp;&nbsp;&nbsp;&nbsp;CNPJ:</td>
    <td>@cnpj</td>
    </tr>
    <tr>
    <td>&nbsp;&nbsp;&nbsp;&nbsp;Razão Social:</td>
    <td>@nomeRazaoSocial</td>
    </tr>
    <tr>
    <td>&nbsp;&nbsp;&nbsp;&nbsp;Responsável Legal:</td>
    <td>@responsavelLegal</td>
    </tr>
    <tr>';
        $htmlModeloRecibo = str_replace('@tblPessoaJuridica', $tblPessoaJuridica, $htmlModeloRecibo);

        $htmlModeloRecibo = str_replace('@usuarioExterno', $noUsuario, $htmlModeloRecibo);
        $htmlModeloRecibo = str_replace('@numRecibo', $reciboDTOBasico->getNumIdReciboPeticionamento(), $htmlModeloRecibo); //Nome do responsavel
        $htmlModeloRecibo = str_replace('@responsavelLegal', $noUsuario, $htmlModeloRecibo); //Nome do responsavel
        $htmlModeloRecibo = str_replace('@ipUtilizado', $reciboDTOBasico->getStrIpUsuario(), $htmlModeloRecibo); //ip usuario
        $htmlModeloRecibo = str_replace('@dataHorario', $reciboDTOBasico->getDthDataHoraRecebimentoFinal(), $htmlModeloRecibo); //data hora
        $htmlModeloRecibo = str_replace('@tipoProcesso', $reciboDTOBasico->getStrStaTipoPeticionamentoFormatado(), $htmlModeloRecibo); //tipo de processo
        $htmlModeloRecibo = str_replace('@numProcesso', $objProcedimentoDTO->getStrProtocoloProcedimentoFormatado(), $htmlModeloRecibo); //numero do processo
        $htmlModeloRecibo = str_replace('@cpfResponsavel', $dadosPj[3], $htmlModeloRecibo); // CPF Responsável
        $htmlModeloRecibo = str_replace('@cnpj', $dados['txtNumeroCnpj'], $htmlModeloRecibo); // Cnpj do vinculo
        $htmlModeloRecibo = str_replace('@nomeRazaoSocial', $dadosPj[0], $htmlModeloRecibo); // Razao Social
        $htmlModeloRecibo = str_replace('@descricao_orgao@', $orgao->getStrDescricao(), $htmlModeloRecibo); // Descricao do Orgão

        $objMdPetRelReciboDocumentoAnexoDTO = new MdPetRelReciboDocumentoAnexoDTO();
        $objMdPetRelReciboDocumentoAnexoDTO->retStrNomeSerie();
        $objMdPetRelReciboDocumentoAnexoDTO->retStrProtocoloFormatado();
        $objMdPetRelReciboDocumentoAnexoDTO->retStrNumeroDocumento();

        $reciboAnexoRN = new MdPetRelReciboDocumentoAnexoRN();
        $objMdPetRelReciboDocumentoAnexoDTO->setNumIdReciboPeticionamento($reciboDTOBasico->getNumIdReciboPeticionamento());
        $arrReciboAnexoDTO = $reciboAnexoRN->listar($objMdPetRelReciboDocumentoAnexoDTO);

        $tblDocumentoPrincipal = '';
        if (!is_null($objArquivoPrincipal) || count($arrReciboAnexoDTO) > 0) {
            $tblDocumentoPrincipal .= '    <tr>';
            $tblDocumentoPrincipal .= '        <td colspan="2" style="font-weight: bold;">Protocolos dos Documentos (Número SEI):</td>';
            $tblDocumentoPrincipal .= '    </tr>';
        }
        $nomeDocPrincipal = ($reciboDTOBasico->getStrStaTipoPeticionamento() == MdPetReciboRN::$TP_RECIBO_RESPONSAVEL_LEGAL_INICIAL) ? $tpRecibo : $reciboDTOBasico->getStrStaTipoPeticionamentoFormatado();
        //Documento Principal
        if (!is_null($objArquivoPrincipal)) {
            $tblDocumentoPrincipal .= '    <tr>';
            $tblDocumentoPrincipal .= '        <td colspan="2" style="font-weight: bold;">&nbsp;&nbsp;&nbsp;&nbsp;- Documento Principal</td>';
            $tblDocumentoPrincipal .= '    </tr>';
            $tblDocumentoPrincipal .= '    <tr>';
            $tblDocumentoPrincipal .= '        <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $nomeDocPrincipal . '</td>';
            $tblDocumentoPrincipal .= '        <td>' . $objArquivoPrincipal->getDocumentoFormatado() . '</td>';
            $tblDocumentoPrincipal .= '    </tr>';
        }
        $htmlModeloRecibo = str_replace('@tblDocumentoPrincipal', $tblDocumentoPrincipal, $htmlModeloRecibo);

        //Atos Constitutivos
        $tblAtos = '';
        if (count($arrReciboAnexoDTO) > 0) {
            $tblAtos .= '    <tr>';
            $tblAtos .= '        <td colspan="2" style="font-weight: bold;">&nbsp;&nbsp;&nbsp;&nbsp;- Atos Constitutivos</td>';
            $tblAtos .= '    </tr>';
            foreach ($arrReciboAnexoDTO as $arquivos) {
                $tblAtos .= '    <tr>';
                $tblAtos .= '        <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $arquivos->getStrNomeSerie();
                $tblAtos .= $arquivos->getStrNumeroDocumento() != '' ? ' ' . $arquivos->getStrNumeroDocumento() : '';
                $tblAtos .= '</td>';
                $tblAtos .= '        <td>' . $arquivos->getStrProtocoloFormatado() . '</td>';
                $tblAtos .= '    </tr>';
            }
        }

        $htmlModeloRecibo = str_replace('@tblAtosConstitutivos', $tblAtos, $htmlModeloRecibo); // Atos Constitutivos

        //Procurações Especiais
        $tblProcuracoes = '';
        if (!is_null($arrProcuracao)) {
            $tblProcuracoes .= '    <tr>';
            $tblProcuracoes .= '        <td colspan="2" style="font-weight: bold;">&nbsp;&nbsp;&nbsp;&nbsp;- Procurações Eletrônicas</td>';
            $tblProcuracoes .= '    </tr>';

            foreach ($arrProcuracao as $procurador) {
                $objDocumentoRN = new DocumentoRN();
                $objDocumentoDTO = new DocumentoDTO();
                $objDocumentoDTO->retStrNomeSerie();
                $objDocumentoDTO->retStrProtocoloDocumentoFormatado();
                $objDocumentoDTO->retStrNumero();
                $objDocumentoDTO->setDblIdDocumento($procurador['id_protocolo_procur']);
                $arrObjDocumentoDTO = $objDocumentoRN->listarRN0008($objDocumentoDTO);

                $tblProcuracoes .= '    <tr>';
                $tblProcuracoes .= '        <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $arrObjDocumentoDTO[0]->getStrNomeSerie();
                $tblProcuracoes .= $arrObjDocumentoDTO[0]->getStrNumero() != '' ? ' ' . $arrObjDocumentoDTO[0]->getStrNumero() : '';
                $tblProcuracoes .= '</td>';
                $tblProcuracoes .= '        <td>' . $procurador['protocolo_procur'] . '</td>';
                $tblProcuracoes .= '    </tr>';
            }

            $tblProcuracoes .= '</table>';
        }
        $htmlModeloRecibo = str_replace('@tblProcuracoes', $tblProcuracoes, $htmlModeloRecibo); // Procuracoes especiais

        //==========================================================================
        //incluindo doc recibo no processo via SEIRN
        //==========================================================================

        $idSerieRecibo = $objInfraParametro->getValor(MdPetIntSerieRN::$MD_PET_ID_SERIE_FORMULARIO);

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

        //necessario forçar update da coluna sta_documento da tabela documento
        //inclusao via SeiRN nao permitiu definir como documento de formulario automatico
//    $parObjDocumentoDTO = new DocumentoDTO();
//    $parObjDocumentoDTO->retTodos();
//    $parObjDocumentoDTO->retStrConteudo();
//    $parObjDocumentoDTO->retStrProtocoloDocumentoFormatado();
//    $parObjDocumentoDTO->setDblIdDocumento($saidaDocExternoAPI->getIdDocumento());
//
//    $idSerieRecibo2 = $objInfraParametro->getValor(MdPetIntSerieRN::$MD_PET_ID_SERIE_RECIBO);
//    $docRN = new DocumentoRN();
//    $parObjDocumentoDTO = $docRN->consultarRN0005($parObjDocumentoDTO);
//    $strConteudoRecibo = $parObjDocumentoDTO->getStrConteudo();
//    $strConteudoRecibo2 = str_replace('</p>', '', $strConteudoRecibo);
//    echo "<pre>";
//    print_r($strConteudoRecibo2);
//    die;
//    $parObjDocumentoDTO->setStrConteudo($strConteudoRecibo2);
//    $parObjDocumentoDTO->setStrStaDocumento(DocumentoRN::$TD_FORMULARIO_AUTOMATICO);
//    $parObjDocumentoDTO->setNumIdSerie($idSerieRecibo2);
//
//    $objDocumentoBD = new DocumentoBD($this->getObjInfraIBanco());
//    $objDocumentoBD->alterar($parObjDocumentoDTO);

        $participanteRN = new ParticipanteRN();
        $objParticipante = new ParticipanteDTO();
        $objParticipante->setDblIdProtocolo($saidaDocExternoAPI->getIdDocumento());
        if (isset($dados['isAlteradoRespLegal'])) {
            if ($dados['hdnIdContatoNovo'] != '') {
                $objParticipante->setNumIdContato($dados['hdnIdContatoNovo']);
            } else {
                $objParticipante->setNumIdContato($dados['idContato']);
            }
        } else {
            $objParticipante->setNumIdContato($dados['idContato']);
        }
        $objParticipante->setNumIdUnidade($objUnidade->getNumIdUnidade());
        $objParticipante->setStrStaParticipacao(ParticipanteRN::$TP_INTERESSADO);
        $objParticipante->setNumSequencia(0);
        $participanteRN->cadastrarRN0170($objParticipante);

        $tpProtocolo = MdPetVincDocumentoRN::$TP_PROTOCOLO_RECIBO;
        $this->_adicionarDadosArquivoVinculacao($saidaDocExternoAPI->getIdDocumento(), $idRepresentante, $tpProtocolo);

        //necessario forçar update da coluna sta_documento da tabela documento
        //inclusao via SeiRN nao permitiu definir como documento de formulario automatico
        $parObjDocumentoDTO = new DocumentoDTO();
        $parObjDocumentoDTO->retTodos();
        $parObjDocumentoDTO->retStrConteudo();
        $parObjDocumentoDTO->retStrNomeSerie();
        $parObjDocumentoDTO->retStrProtocoloDocumentoFormatado();
        $parObjDocumentoDTO->setDblIdDocumento($saidaDocExternoAPI->getIdDocumento());

        $idSerieRecibo2 = $objInfraParametro->getValor(MdPetIntSerieRN::$MD_PET_ID_SERIE_RECIBO);

        $docRN = new DocumentoRN();
        $parObjDocumentoDTO = $docRN->consultarRN0005($parObjDocumentoDTO);

        $strConteudoRecibo = $parObjDocumentoDTO->getStrConteudo();
        $strConteudoRecibo2 = str_replace(htmlentities($parObjDocumentoDTO->getStrNomeSerie() . ' nº ' . $parObjDocumentoDTO->getStrProtocoloDocumentoFormatado()), '', $strConteudoRecibo);
        $strConteudoRecibo3 = str_replace('<p style="font-size:13pt; font-family:Calibri; text-align:center; text-transform:uppercase; word-wrap:normal"></p>', '', $strConteudoRecibo2);

        $parObjDocumentoDTO->setStrConteudo($strConteudoRecibo3);
        $parObjDocumentoDTO->setStrStaDocumento(DocumentoRN::$TD_FORMULARIO_AUTOMATICO);
        $parObjDocumentoDTO->setNumIdSerie($idSerieRecibo2);

        $objDocumentoBD = new DocumentoBD($this->getObjInfraIBanco());
        $objDocumentoBD->alterar($parObjDocumentoDTO);

        $parObjDocumentoConteudoDTO = new DocumentoConteudoDTO();
        $parObjDocumentoConteudoDTO->setDblIdDocumento($saidaDocExternoAPI->getIdDocumento());
        $parObjDocumentoConteudoDTO->setStrConteudo($strConteudoRecibo3);
        $objDocumentoConteudoBD = new DocumentoConteudoBD($this->getObjInfraIBanco());
        $objDocumentoConteudoBD->alterar($parObjDocumentoConteudoDTO);

        $reciboDTOBasico->setDblIdDocumento($saidaDocExternoAPI->getIdDocumento());

        $objBD = new MdPetReciboBD($this->getObjInfraIBanco());
        $objBD->alterar($reciboDTOBasico);
        return $reciboDTOBasico;

    }

    private function _adicionarVinculo($dados, $idProcedimento)
    {
        $ckDeclaracao = $dados['chkDeclaracao'] == 'S' ? $dados['chkDeclaracao'] : 'N';

        $objMdPetVinculoRN = new MdPetVinculoRN();
        $objMdPetVinculoDTO = new MdPetVinculoDTO();
        $objMdPetVinculoDTO->setNumIdContato($dados['idContato']);
        $objMdPetVinculoDTO->setStrSinValidado($ckDeclaracao);
        $objMdPetVinculoDTO->setDblIdProtocolo($idProcedimento);
        $objMdPetVinculoDTO->setStrTpVinculo('J');

        $stWebservice = 'N';
        if (!empty($dados['hdnStaWebService'])) {
            $stWebservice = 'S';
        }
        $objMdPetVinculoDTO->setStrSinWebService($stWebservice);

        $objMdPetVinculoDTO = $objMdPetVinculoRN->cadastrar($objMdPetVinculoDTO);

        return $objMdPetVinculoDTO;


    }

    public function _adicionarDadosArquivoVinculacao($idProtocolo, $idMdPetVinculoRepresent, $tpProtocolo)
    {
        $objMdPetUsuarioRN = new MdPetIntUsuarioRN();
        $objContatoDTO = $objMdPetUsuarioRN->retornaObjContatoPorIdUsuario(array(SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno()));

        $objMdPetVincDocumentoRN = new MdPetVincDocumentoRN();
        $objMdPetVincDocumentoDTO = new MdPetVincDocumentoDTO();
        $objMdPetVincDocumentoDTO->setDblIdDocumento($idProtocolo);
        $objMdPetVincDocumentoDTO->setStrTipoDocumento($tpProtocolo);
        $objMdPetVincDocumentoDTO->setNumIdMdPetVinculoRepresent($idMdPetVinculoRepresent);
        $objMdPetVincDocumentoDTO->setDthDataCadastro(InfraData::getStrDataHoraAtual());
        $objMdPetVincDocumentoRN->cadastrar($objMdPetVincDocumentoDTO);

    }

    private function _getProtocoloDocumentoVinculo($idRepresentante)
    {
        $strReturn = '';
        $objMdPetVincDocumentoRN = new MdPetVincDocumentoRN();
        $objMdPetVincDocumentoDTO = new MdPetVincDocumentoDTO();
        $objMdPetVincDocumentoDTO->retDblIdDocumento();
        $objMdPetVincDocumentoDTO->retStrProtocoloFormatadoProtocolo();
        $objMdPetVincDocumentoDTO->setNumIdMdPetVinculoRepresent($idRepresentante);
        $objMdPetVincDocumentoDTO->setStrTipoDocumento(MdPetVincDocumentoRN::$TP_PROTOCOLO_PRINCIPAL);
        $objMdPetVincDocumentoDTO->setNumMaxRegistrosRetorno(1);
        $objMdPetVincDocumentoDTO = $objMdPetVincDocumentoRN->consultar($objMdPetVincDocumentoDTO);

        if (!is_null($objMdPetVincDocumentoDTO)) {
            $strReturn = $objMdPetVincDocumentoDTO->getStrProtocoloFormatadoProtocolo();
        }

        return $strReturn;
    }

    private function _getIdContatoPorUsuario($idUsuario = null)
    {
        $idRetorno = null;
        $usuarioRN = new UsuarioRN();
        $usuarioDTO = new UsuarioDTO();
        $usuarioDTO->retNumIdContato();

        $idUsuarioExterno = is_null($idUsuario) ? SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno() : $idUsuario;

        $usuarioDTO->setNumIdUsuario($idUsuarioExterno);
        $usuarioDTO->setNumMaxRegistrosRetorno(1);
        $usuarioDTO->setStrSinAtivo('S');

        $objUsuarioDTO = $usuarioRN->consultarRN0489($usuarioDTO);

        if (!is_null($objUsuarioDTO)) {
            $idRetorno = $objUsuarioDTO->getNumIdContato();
        }

        return $idRetorno;
    }

    private function _adicionarProcuracaoEspecialRepresentante($idVinculo, &$dados, $isAlteracao, $alteradoRespLegal)
    {

        $objMdPetVincRepresentantRN = new MdPetVincRepresentantRN();

        if (empty($dados['hdnIdContato'])) {
            $idContatoLogado = $this->_getIdContatoPorUsuario();
            if(isset($dados['hdnIdVinculo'])){
                if($dados['hdnIdVinculo'] != ''){
                    $dados['hdnIdContatoNovo'] = $idContatoLogado;
                }
            }
        } else {
            $idContatoLogado = $dados['hdnIdContato'];

        }

        $idContatoRepresentante = $isAlteracao && $alteradoRespLegal && $dados['hdnIdContatoNovo'] != '' ? $dados['hdnIdContatoNovo'] : $idContatoLogado;

        $objMdPetVincRepresentantAltDTO = new MdPetVincRepresentantDTO();
        $objMdPetVincRepresentantAltDTO->setNumIdMdPetVinculo($idVinculo);
        $objMdPetVincRepresentantAltDTO->retStrSinAtivo();
        $objMdPetVincRepresentantAltDTO->retStrTipoRepresentante();
        $objMdPetVincRepresentantAltDTO->retNumIdMdPetVinculoRepresent();
        $arrObjMdPetVincRepresentantAltDTO = $objMdPetVincRepresentantRN->listar($objMdPetVincRepresentantAltDTO);
        if ($arrObjMdPetVincRepresentantAltDTO) {
            foreach ($arrObjMdPetVincRepresentantAltDTO as $objMdPetVincRepresentant) {
                if($objMdPetVincRepresentant->getStrTipoRepresentante() == MdPetVincRepresentantRN::$PE_RESPONSAVEL_LEGAL && $objMdPetVincRepresentant->getStrSinAtivo() == 'S') {
                    $objMdPetVincRepresentant->setStrSinAtivo('N');
                    $objMdPetVincRepresentant->setStrStaEstado(MdPetVincRepresentantRN::$RP_SUBSTITUIDA);
                    $objMdPetVincRepresentantRN->alterar($objMdPetVincRepresentant);
                }
            }
        }

        //Adicionar vinculo do representante
        $objMdPetVincRepresentantDTO = new MdPetVincRepresentantDTO();
        $objMdPetVincRepresentantDTO->setNumIdMdPetVinculo($idVinculo);
        $objMdPetVincRepresentantDTO->setNumIdContato($idContatoRepresentante);
        //$objMdPetVincRepresentantDTO->setNumIdAcessoExterno('');
        $objMdPetVincRepresentantDTO->setNumIdContatoOutorg($idContatoLogado);
        $objMdPetVincRepresentantDTO->setStrTipoRepresentante(MdPetVincRepresentantRN::$PE_RESPONSAVEL_LEGAL); // Responsável Legal
        $objMdPetVincRepresentantDTO->setStrSinAtivo('S');
        $objMdPetVincRepresentantDTO->setStrStaEstado(MdPetVincRepresentantRN::$RP_ATIVO);
        $objMdPetVincRepresentantDTO->setDthDataCadastro(InfraData::getStrDataHoraAtual());

        $objMdPetVincRepresentantDTO = $objMdPetVincRepresentantRN->cadastrar($objMdPetVincRepresentantDTO);

        $idRepresentante = $objMdPetVincRepresentantDTO->getNumIdMdPetVinculoRepresent();

        return $idRepresentante;
    }

    protected function buscarDocumentosObrigatoriosConectado()
    {
    }

    protected function validarContatoProcuradorConectado($arrParams)
    {

        $idVinculo = array_key_exists(0, $arrParams) ? $arrParams[0] : null;
        $idContatoRepr = array_key_exists(1, $arrParams) ? $arrParams[1] : null;

        $objMdPetVinculoDTO = new MdPetVinculoDTO();
        $objMdPetVinculoRN = new MdPetVinculoRN();

        $objMdPetVinculoDTO->setNumIdMdPetVinculo($idVinculo);
        $objMdPetVinculoDTO->setStrSinAtivoRepresentante('S');
        $objMdPetVinculoDTO->setNumIdContatoRepresentante($idContatoRepr);

        return $objMdPetVinculoRN->contar($objMdPetVinculoDTO) > 0;
    }


    public function enviarEmail($arrParams)
    {

        $emailMdPetEmailNotificacaoIntercorrenteRN = new MdPetEmailNotificacaoIntercorrenteRN();
        return $emailMdPetEmailNotificacaoIntercorrenteRN->notificaoPeticionamentoExterno($arrParams);

    }

    static public function getUnidade($tipoPessoa = null)
    {
        
        $mdPetVincTpProcessoRN = new MdPetVincTpProcessoRN();
        $objMdPetVincTpProcessoDTO = new MdPetVincTpProcessoDTO();
        $objMdPetVincTpProcessoDTO->retTodos();
        if($tipoPessoa != null){
            $objMdPetVincTpProcessoDTO->setNumIdMdPetVincTpProcesso($tipoPessoa);
        }else{
            $objMdPetVincTpProcessoDTO->setNumIdMdPetVincTpProcesso(MdPetVincTpProcessoRN::$ID_FIXO_MD_PET_VINCULO_USU_EXT);
        }
        $objMdPetVincTpProcessoDTO = $mdPetVincTpProcessoRN->consultar($objMdPetVincTpProcessoDTO);

        $unidadeRN = new UnidadeRN();
        $objUnidadeDTO = new UnidadeDTO();
        $objUnidadeDTO->retTodos(true);
        $objUnidadeDTO->setNumIdUnidade($objMdPetVincTpProcessoDTO->getNumIdUnidade());
        return $unidadeRN->consultarRN0125($objUnidadeDTO);


    }

    static public function getProcedimento($idProcedimento)
    {

        $objProcedimentoDTO = new ProcedimentoDTO();
        $objProcedimentoRN = new ProcedimentoRN();
        $objProcedimentoDTO->setDblIdProcedimento($idProcedimento);
        $objProcedimentoDTO->retDblIdProcedimento();
        $objProcedimentoDTO->retStrProtocoloProcedimentoFormatado();
        $objProcedimentoDTO->retTodos(true);
        return $objProcedimentoRN->consultarRN0201($objProcedimentoDTO);
    }

    public function retornaSeriesInfraParamentro($idSerie){
        $objInfraParametro = new InfraParametro($this->getObjInfraIBanco());
        $arrIdsSeries = array(
            $objInfraParametro->getValor(MdPetIntSerieRN::$MD_PET_ID_SERIE_PROCURACAOE)
        );

        return $arrIdsSeries;
    }

    private function _getTipoPeticionamento($dados){
        $mdPetVinculoRepresentant = new MdPetVincRepresentantRN();
        $objMdPetVinculoRepresentantDTORL = new MdPetVincRepresentantDTO();
        $objMdPetVinculoRepresentantDTORL->setNumIdMdPetVinculo($dados['hdnIdVinculo']);
        $objMdPetVinculoRepresentantDTORL->setStrTipoRepresentante(MdPetVincRepresentantRN::$PE_RESPONSAVEL_LEGAL);
        $objMdPetVinculoRepresentantDTORL->setStrSinAtivo('S');
        $objMdPetVinculoRepresentantDTORL->retStrCpfProcurador();
        $arrObjMdPetVinculoRepresentantDTORL = $mdPetVinculoRepresentant->consultar($objMdPetVinculoRepresentantDTORL);

        $strCpfRespLegalAntigo = "";
        if ($arrObjMdPetVinculoRepresentantDTORL) {
            $strCpfRespLegalAntigo = $arrObjMdPetVinculoRepresentantDTORL->getStrCpfProcurador();
        } else {
            $strCpfRespLegalAntigo = $dados['txtCpfResponsavelAntigo'];
        }

        return $strCpfRespLegalAntigo != InfraUtil::retirarFormatacao($dados['txtNumeroCpfResponsavel']) ? MdPetReciboRN::$TP_RECIBO_RESPONSAVEL_LEGAL_ALTERACAO : MdPetReciboRN::$TP_RECIBO_ATUALIZACAO_ATOS_CONSTITUTIVOS;
    }

    private function _validarContatoComExpedicaoAndamento($objContato)
    {
        $arrModulos = ConfiguracaoSEI::getInstance()->getValor('SEI','Modulos');
        if(is_array($arrModulos) && array_key_exists('CorreiosIntegracao', $arrModulos)) {

            $objInfraParametroDTO = new InfraParametroDTO();
            $objInfraParametroDTO->setStrNome('VERSAO_MODULO_CORREIOS');
            $objInfraParametroDTO->retStrValor();

            $objInfraParametroBD = new InfraParametroBD($this->getObjInfraIBanco());
            $arrObjInfraParametroDTO = $objInfraParametroBD->consultar($objInfraParametroDTO);

            if($arrObjInfraParametroDTO){

                $mdCorExpedicaoSolicitadaRN = new MdCorExpedicaoSolicitadaRN();
                $mdCorExpedicaoSolicitadaDTO = new MdCorExpedicaoSolicitadaDTO();

                $mdCorExpedicaoSolicitadaDTO->setNumIdContatoDestinatario($objContato->getNumIdContato(), InfraDTO::$OPER_IGUAL);
                $mdCorExpedicaoSolicitadaDTO->retNumIdMdCorExpedicaoSolicitada();
                $mdCorExpedicaoSolicitadaDTO->retStrStaPlp();
                $mdCorExpedicaoSolicitadaDTO->retNumCodigoPlp();
                $mdCorExpedicaoSolicitadaDTO->setDistinct(true);

                $arrMdCorExpedicaoSolicitadaDTO = $mdCorExpedicaoSolicitadaRN->listar($mdCorExpedicaoSolicitadaDTO);

                $arrCorPlp = array(
                    MdCorPlpRN::$STA_GERADA,
                    MdCorPlpRN::$STA_PENDENTE,
                    MdCorPlpRN::$STA_RETORNO_AR_PENDENTE
                );

                if (count($arrMdCorExpedicaoSolicitadaDTO) > 0) {
                    foreach ($arrMdCorExpedicaoSolicitadaDTO as $mdCorExpedicaoSolicitadaDTO) {
                        if (in_array($mdCorExpedicaoSolicitadaDTO->getStrStaPlp(), $arrCorPlp)) {
                            return true;
                        }
                    }
                }
            }
        }

    }
}