<?php
/**
 * TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
 *
 * 25/01/2018 - criado por Usuário
 *
 * Versão do Gerador de Código: 1.41.0
 */

require_once dirname(__FILE__) . '/../../../SEI.php';

class MdPetIntegracaoRN extends InfraRN
{

    private ?MdPetIntegClienteRN $objCliente = null;

    /**
     * CPF do solicitante na consulta de CNPJ sem usuário externo logado, como no agendamento.
     * O WS recusa a consulta sem CPF ("CPF do usuario nao informado"), e o usuário técnico
     * do módulo não tem CPF. Número válido gerado para essa identificação.
     */
    private const CPF_SOLICITANTE_SEM_USUARIO_EXTERNO = '45921393053';

    /** Clientes da integração, por id, reaproveitados nas consultas feitas pela mesma instância. */
    private array $arrObjCliente = [];

    public function __construct()
    {
        parent::__construct();
    }

    private function _getIdUf($consulta, $arrSaidaWS)
    {
        $siglaUf = $this->objCliente->lerDado($consulta, $arrSaidaWS['codIbgeMunicipio']) ? $this->objCliente->lerDado($consulta, $arrSaidaWS['codIbgeMunicipio']) : null;
        $idUf = '';

        if (!is_null($siglaUf)) {
            $objUfDTO = new UfDTO();
            $objUfDTO->setStrSigla($siglaUf);
            $objUfDTO->retNumIdUf();
            $objUfDTO->setNumMaxRegistrosRetorno(1);
            $objUfRN = new UfRN();
            $objUfDTO = $objUfRN->consultarRN0400($objUfDTO);
            $idUf = !is_null($objUfDTO) ? $objUfDTO->getNumIdUf() : '';
        }

        return $idUf;
    }

    private function _getDadosCidade($consulta, $arrSaidaWS)
    {
        $idCidade = '';
        $nomeCidade = '';
        $idUF = '';
        $siglaUF = '';
        $codigoIbge = $this->objCliente->lerDado($consulta, $arrSaidaWS['codIbgeMunicipio']) ? $this->objCliente->lerDado($consulta, $arrSaidaWS['codIbgeMunicipio']) : null;
        $arrRetorno = array();

        if (!is_null($codigoIbge)) {
            $objCidadeDTO = new CidadeDTO();
            $objCidadeDTO->setNumCodigoIbge($codigoIbge);
            $objCidadeDTO->retNumIdCidade();
            $objCidadeDTO->retStrNome();
            $objCidadeDTO->retNumIdUf();
            $objCidadeDTO->retStrSiglaUf();
            $objCidadeDTO->retNumIdPais();

            $objCidadeDTO->setNumMaxRegistrosRetorno(1);
            $objCidadeRN = new CidadeRN();
            $objCidadeDTO = $objCidadeRN->consultarRN0409($objCidadeDTO);
            $idCidade = !is_null($objCidadeDTO) ? $objCidadeDTO->getNumIdCidade() : '';
            $nomeCidade = !is_null($objCidadeDTO) ? $objCidadeDTO->getStrNome() : '';
            $idUF = !is_null($objCidadeDTO) ? $objCidadeDTO->getNumIdUf() : '';
            $siglaUF = !is_null($objCidadeDTO) ? $objCidadeDTO->getStrSiglaUf() : '';
            $idPais = !is_null($objCidadeDTO) ? $objCidadeDTO->getNumIdPais() : '';
        }

        $arrRetorno['idCidade'] = $idCidade;
        $arrRetorno['nomeCidade'] = $nomeCidade;
        $arrRetorno['idUF'] = $idUF;
        $arrRetorno['idPais'] = $idPais;
        $arrRetorno['siglaUF'] = $siglaUF;

        return $arrRetorno;
    }

    protected function atualizaContatoCNPJMedianteConsultaReceitaWSConectado($consulta, $arrSaidaWS){

        $idUsuarioPet = (new MdPetIntUsuarioRN())->getObjUsuarioPeticionamento(true);
        SessaoSEI::getInstance(false)->simularLogin(null, SessaoSEI::$UNIDADE_TESTE, $idUsuarioPet, null);

        $dadosCidade = $this->_getDadosCidade($consulta, $arrSaidaWS);

        $dadosPJ = [
            'cnpj'              => $this->objCliente->lerDado($consulta, $arrSaidaWS['cnpjEmpresa']),
            'nome'              => $this->objCliente->lerDado($consulta, $arrSaidaWS['razaoSocial']),
            'endereco'          => $this->objCliente->lerDado($consulta, $arrSaidaWS['logradouro']) . ', ' . $this->objCliente->lerDado($consulta, $arrSaidaWS['numero']),
            'bairro'            => $this->objCliente->lerDado($consulta, $arrSaidaWS['bairro']),
            'cep'               => MdPetDataUtils::formatCep($this->objCliente->lerDado($consulta, $arrSaidaWS['cep'])),
            'complemento'       => $this->objCliente->lerDado($consulta, $arrSaidaWS['complemento']),
            'id_cidade'         => $dadosCidade['idCidade'],
            'id_uf'             => $dadosCidade['idUF'],
            'id_pais'           => $dadosCidade['idPais'],
            'sta_natureza'      => 'J',
            'responsavel_cpf'   => $this->objCliente->lerDado($consulta, $arrSaidaWS['cpfRespLegal'])
        ];

        // Sem CNPJ no retorno, a pesquisa alcançaria todos os contatos sem CNPJ.
        if (InfraString::isBolVazia($dadosPJ['cnpj'])) {
            return;
        }

        $objContatoDTO = new ContatoDTO();
        $objContatoDTO->setStrCnpj($dadosPJ['cnpj']);
        $objContatoDTO->setStrStaNatureza(ContatoRN::$TN_PESSOA_JURIDICA);
        $objContatoDTO->retNumIdContato();
        $arrObjContatosCnpjDTO = (new ContatoBD(BancoSEI::getInstance()))->listar($objContatoDTO);

        if(empty($arrObjContatosCnpjDTO)){
            return;
        }

        $arrIdContatos = array_unique(InfraArray::converterArrInfraDTO($arrObjContatosCnpjDTO, 'IdContato'));

        $objMdPetVinculoDTO = new MdPetVinculoDTO();
        $objMdPetVinculoDTO->retNumIdContato();
        $objMdPetVinculoDTO->setNumIdContato($arrIdContatos, InfraDTO::$OPER_IN);

        // Mais de um contato do mesmo CNPJ pode ter vinculação, e todos recebem os dados da Receita.
        $arrIdContatosVinculados = array_values(array_unique(InfraArray::converterArrInfraDTO((new MdPetVinculoRN())->listar($objMdPetVinculoDTO), 'IdContato')));

        if ($arrIdContatosVinculados === []) {
            return;
        }

        foreach ($arrIdContatosVinculados as $numIdContato) {
            $objContatoDTO = new ContatoDTO();
            $objContatoDTO->setNumIdContato($numIdContato);
            $objContatoDTO->setStrObservacao('Cadastro atualizado mediante consulta na Receita Federal em ' . InfraData::getStrDataHoraAtual());
            $objContatoDTO->setStrNome($dadosPJ['nome']);
            $objContatoDTO->setStrEndereco($dadosPJ['endereco']);
            $objContatoDTO->setStrBairro($dadosPJ['bairro']);
            $objContatoDTO->setStrCep($dadosPJ['cep']);
            $objContatoDTO->setStrComplemento($dadosPJ['complemento']);
            $objContatoDTO->setNumIdCidade($dadosPJ['id_cidade']);
            $objContatoDTO->setNumIdUf($dadosPJ['id_uf']);
            $objContatoDTO->setNumIdPais($dadosPJ['id_pais']);

            (new ContatoBD(BancoSEI::getInstance()))->alterar($objContatoDTO);
        }

        // A gravação acima vai direto pela ContatoBD; o índice de pesquisa do nome é refeito pela ContatoRN.
        $objContatoDTO = new ContatoDTO();
        $objContatoDTO->setNumIdContato($arrIdContatosVinculados);
        (new ContatoRN())->montarIndexacao($objContatoDTO);

    }

    protected function consultarReceitaWsResponsavelLegalConectado($dados)
    {
        
        $xml = '<dados-pj>';
        $headers = apache_request_headers();
        $cnpj = InfraUtil::prepararCnpj($dados['txtNumeroCnpj']);
        $dados['idUsuarioLogado'] = SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno();

        if (!InfraUtil::validarCnpj($cnpj)) {
            $xml .= "<success>false</success>\n";
            $xml .= "<msg>CNPJ informado é inválido.</msg>\n";
            $xml .= '</dados-pj>';
            return $xml;
        }
	
	    $objMdPetIntegracao     = $this->consultarIntegracaoPorIdMdPetIntegFuncionalid(MdPetIntegFuncionalidRN::$ID_FUNCIONALIDADE_CNPJ_RECEITA_FEDERAL);
        $strMetodoWebservice    = $objMdPetIntegracao->getStrOperacaoWsdl();
        $cpfUsuarioLogado       = $this->retornaCpfUsuarioLogado($dados['idUsuarioLogado']);
        $arrParamsWebservice    = ['cnpj' => $cnpj];
        $arrSaidaWS             = [];

        $objMdPetIntegParametroDTO = new MdPetIntegParametroDTO();
        $objMdPetIntegParametroDTO->retStrValorPadrao();
        $objMdPetIntegParametroDTO->retStrTpParametro();
        $objMdPetIntegParametroDTO->retStrNome();
        $objMdPetIntegParametroDTO->retStrNomeCampo();
        $objMdPetIntegParametroDTO->retStrStaLocalParametro();
        $objMdPetIntegParametroDTO->retStrSinDadoConfidencial();
        $objMdPetIntegParametroDTO->setNumIdMdPetIntegracao($objMdPetIntegracao->getNumIdMdPetIntegracao());
        $arrObjMdPetIntegParametroRN = (new MdPetIntegParametroRN())->listar($objMdPetIntegParametroDTO);

        if (count($arrObjMdPetIntegParametroRN)) {

            $mes = 0;
            $chaveMes = '';

            foreach ($arrObjMdPetIntegParametroRN as $itemParam) {

                // Mapeando os de entrada
                if ($itemParam->getStrTpParametro() == 'E'){

                    if ($itemParam->getStrNome() == 'mesesExpiraCache') {

                        $mes = (int)$itemParam->getStrValorPadrao();
                        $chaveMes = $itemParam->getStrNome();

                        $arrParamsWebservice[$chaveMes] = $mes;

                    }

                    if ($itemParam->getStrNome() == 'identificacaoOrigem' && $itemParam->getStrNomeCampo() == 'origem') {
                        
                        //Verificação da Origem
                        $objInfraParametro = new InfraParametro(BancoSEI::getInstance());
                        $idUsuario = $objInfraParametro->getValor(MdPetContatoRN::$STR_INFRA_PARAMETRO_SIGLA_CONTATO, false);

                        if ($idUsuario != '' && !is_null($idUsuario)) {
                            $objUsuarioRN = new UsuarioRN();
                            $objUsuarioDTO = new UsuarioDTO();

                            $objUsuarioDTO->setNumIdUsuario($idUsuario);
                            $objUsuarioDTO->retStrSigla();

                            $objUsuarioDTO = $objUsuarioRN->consultarRN0489($objUsuarioDTO);

                            if ($objUsuarioDTO) {
                                $arrParamsWebservice['origem'] = $objUsuarioDTO->getStrSigla();
                            }
                        }

                    }

                    if ($itemParam->getStrNome() == 'cpfUsuario' && $itemParam->getStrNomeCampo() == 'cpfUsuario') {
                        if (!empty($cpfUsuarioLogado)) {
                            $arrParamsWebservice['cpfUsuario'] = $cpfUsuarioLogado;
                        }
                    }

                }

                // Mapeando os de saida todos de uma vez
                if ($itemParam->getStrTpParametro() == 'P'){

                    $arrSaidaWS[$itemParam->getStrNome()] = $itemParam->getStrNomeCampo();

                }
                
            
            }

        }

        /*
        * Realiza a consulta no WS
        */
        $parametro              = [ $strMetodoWebservice => $arrParamsWebservice ];
        $this->objCliente = MdPetIntegFabricaRN::criarCliente($objMdPetIntegracao, $arrObjMdPetIntegParametroRN);
        $consulta = $this->objCliente->consultar($strMetodoWebservice, $parametro);

        if ($this->objCliente->possuiDados($consulta, 'PessoaJuridica')) {
            $this->atualizaContatoCNPJMedianteConsultaReceitaWSConectado($consulta, $arrSaidaWS);
        }

	    if (!empty($objMdPetIntegracao->getStrCodReceitaSuspAuto()) && in_array(intval($this->objCliente->lerDado($consulta, $arrSaidaWS['codSituacaoCadastral'])), explode(',', $objMdPetIntegracao->getStrCodReceitaSuspAuto()))) {
            $xml .= "<success>false</success>\n";
            $xml .= "<msg>O cadastro do CNPJ indicado está suspenso na Receita Federal. Dessa forma, não pode ser efetivada a vinculação do Responsável Legal à Pessoa Jurídica.</msg>\n";
            $xml .= '</dados-pj>';
            return $xml;
        }

        $cpfResponsavelLegalReceita = $this->objCliente->lerDado($consulta, $arrSaidaWS['cpfRespLegal']);

        if (empty($cpfResponsavelLegalReceita)) {
            $xml .= "<success>false</success>\n";
            $xml .= '<msg>Erro na consulta: ' . $consulta['faultstring '] . '</msg>';
            $xml .= '</dados-pj>';
            return $xml;
        }

        if ($cpfUsuarioLogado != $cpfResponsavelLegalReceita) {
            $xml .= "<success>false</success>\n";
            $xml .= "<msg>Em consulta à base da Receita Federal do Brasil (RFB), verificou-se que o seu CPF não consta como Responsável Legal pelo CNPJ nº " . $dados['txtNumeroCnpj'] . ", o que impede a presente vinculação.\n \nResponsável Legal não se confunde com o conceito de sócio, havendo apenas um CPF na RFB como Responsável Legal pelo CNPJ.\n \n";
            $xml .= "Entre em contato com a RFB para verificar eventuais pendências.</msg>\n";
            $xml .= '</dados-pj>';
            return $xml;
        } 
        
        // Valida se já existe vinculo do CNPJ com o CPF do Usuário logado
        $VinculoPJ = MdPetVinculoINT::validarExistenciaVinculoCnpj($dados);
        $strXml = simplexml_load_string($VinculoPJ);

        if ($VinculoPJ != "<dados-pj></dados-pj>") {
            if (!isset($strXml->idVinculo)) {
                return $VinculoPJ;
            }
            $xml .= '<hdnIdVinculo>' . $strXml->idVinculo . '</hdnIdVinculo>';
        }

        $mdPetContatoRN = new MdPetContatoRN();
        $arrContatoDTO = $mdPetContatoRN->getContatoInclusoModPet($cnpj);

        $slTipoInteressado = '';
        if (!is_null($arrContatoDTO)) {
            $slTipoInteressado = $arrContatoDTO->getNumIdTipoContato();
        }

        $dadosCidade = $this->_getDadosCidade($consulta, $arrSaidaWS);

        if ($slTipoInteressado == '') {
            $xml .= '<slTipoInteressado>0</slTipoInteressado>';
        } else {
            $xml .= '<slTipoInteressado>' . $slTipoInteressado . '</slTipoInteressado>';
        }
        
        $objMdPetIntegParametroDTO = new MdPetIntegParametroDTO();
        $objMdPetIntegParametroDTO->retTodos();
        $objMdPetIntegParametroDTO->setStrTpParametro('P');
        $objMdPetIntegParametroDTO->setNumIdMdPetIntegracao($objMdPetIntegracao->getNumIdMdPetIntegracao());
        $arrObjMdPetIntegParametroDTO = (new MdPetIntegParametroRN())->listar($objMdPetIntegParametroDTO);

        if ($arrObjMdPetIntegParametroDTO) {

            $txtLogradouro = '';
            
            foreach ($arrObjMdPetIntegParametroDTO as $itemParametro) {

                $valor = htmlspecialchars(trim((string) $this->objCliente->lerDado($consulta, $itemParametro->getStrNomeCampo())));

                if ($itemParametro->getStrNome() == 'cnpjEmpresa') {
                    $xml .= '<txtNomeFantasia>' . $valor . '</txtNomeFantasia>';
                }
                if ($itemParametro->getStrNome() == 'cpfRespLegal') {
                    $xml .= '<txtNumeroCpfResponsavel>' . InfraUtil::formatarCpf($valor) . '</txtNumeroCpfResponsavel>';
                }
                if ($itemParametro->getStrNome() == 'razaoSocial') {
                    $xml .= '<txtRazaoSocial>' . $valor . '</txtRazaoSocial>';
                }
                if ($itemParametro->getStrNome() == 'nomeRespLegal') {
                    $xml .= '<txtNomeResponsavelLegal>' . $valor . '</txtNomeResponsavelLegal>';
                }
                if ($itemParametro->getStrNome() == 'cep') {
                    $xml .= '<txtNumeroCEP>' . MdPetDataUtils::formatCep($valor) . '</txtNumeroCEP>';
                }
                if ($itemParametro->getStrNome() == 'logradouro') {
                    $txtLogradouro .= $valor;
                }
                if ($itemParametro->getStrNome() == 'numero') {
                    $txtLogradouro .= ", " . $valor;
                }
                if ($itemParametro->getStrNome() == 'complemento') {
                    $txtLogradouro .= ", " . $valor;
                    $xml .= '<txtComplementoEndereco>' . $valor . '</txtComplementoEndereco>';
                }
                if ($itemParametro->getStrNome() == 'bairro') {
                    $xml .= '<txtBairro>' . $valor . '</txtBairro>';
                }
                if ($itemParametro->getStrNome() == 'razaoSocial') {

                }

            }

            $xml .= '<slUf>' . $dadosCidade['idUF'] . '</slUf>';
            $xml .= '<selCidade>' . htmlspecialchars($dadosCidade['idCidade']) . '</selCidade>';
            $xml .= '<nomeCidade>' . htmlspecialchars($dadosCidade['nomeCidade']) . '</nomeCidade>';
            $xml .= '<txtLogradouro>' . $txtLogradouro . '</txtLogradouro>';
            // $xml .= '<txtNumeroEndereco>'. $consulta['PessoaJuridica']['endereco']['numero'].'</txtNumeroEndereco>';

        } else {

            // Retorno do WS da ANATEL
            $xml .= '<txtNomeFantasia>' . htmlspecialchars($this->objCliente->lerDado($consulta, $arrSaidaWS['razaoSocial'])) . '</txtNomeFantasia>';
            $xml .= '<txtNumeroCpfResponsavel>' . InfraUtil::formatarCpf($cpfResponsavelLegalReceita) . '</txtNumeroCpfResponsavel>';
            $xml .= '<txtRazaoSocial>' . htmlspecialchars($this->objCliente->lerDado($consulta, $arrSaidaWS['razaoSocial'])) . '</txtRazaoSocial>';
            $xml .= '<txtNomeResponsavelLegal>' . htmlspecialchars($this->objCliente->lerDado($consulta, $arrSaidaWS['nomeRespLegal'])) . '</txtNomeResponsavelLegal>';
            $xml .= '<slUf>' . $dadosCidade['idUF'] . '</slUf>';
            $xml .= '<selCidade>' . htmlspecialchars($dadosCidade['idCidade']) . '</selCidade>';
            $xml .= '<txtNumeroCEP>' . MdPetDataUtils::formatCep($this->objCliente->lerDado($consulta, $arrSaidaWS['cep'])) . '</txtNumeroCEP>';
            $xml .= '<txtLogradouro>' . htmlspecialchars($this->objCliente->lerDado($consulta, $arrSaidaWS['logradouro'])) . ', ' . $this->objCliente->lerDado($consulta, $arrSaidaWS['numero']) . ' ' . htmlspecialchars($this->objCliente->lerDado($consulta, $arrSaidaWS['complemento'])) . '</txtLogradouro>';
            // $xml .= '<txtNumeroEndereco>'. $consulta['PessoaJuridica']['endereco']['numero'].'</txtNumeroEndereco>';
            $xml .= '<txtComplementoEndereco>' . htmlspecialchars($this->objCliente->lerDado($consulta, $arrSaidaWS['complemento'])) . '</txtComplementoEndereco>';
            $xml .= '<txtBairro>' . htmlspecialchars($this->objCliente->lerDado($consulta, $arrSaidaWS['bairro'])) . '</txtBairro>';
            $xml .= '<nomeCidade>' . htmlspecialchars($dadosCidade['nomeCidade']) . '</nomeCidade>';

        }

        $xml .= '</dados-pj>';

        return $xml;

    }

    /**
     * Consulta o CNPJ na Receita Federal para o agendamento e atualiza o contato da PJ.
     *
     * @param string $cnpj
     * @return array RetornoConsultaReceitaWS, com o retorno do WS, IntegracaoCodigosParaSuspensao e a situação cadastral e o CPF do responsável lidos pelo mapeamento
     * @throws InfraException integração inativa ou sem parâmetros, falha do WS ou retorno sem a pessoa jurídica
     */
    protected function consultarCNPJReceitaWsResponsavelLegalConectado($cnpj)
    {

        $objMdPetIntegracao = $this->consultarIntegracaoPorIdMdPetIntegFuncionalid(MdPetIntegFuncionalidRN::$ID_FUNCIONALIDADE_CNPJ_RECEITA_FEDERAL);

        if (!$objMdPetIntegracao) {
            throw new InfraException('A integração de consulta de CNPJ na Receita Federal não está ativa.');
        }

        $cnpj                   = InfraUtil::prepararCnpj($cnpj);
        $strMetodoWebservice    = $objMdPetIntegracao->getStrOperacaoWsdl();
        $cpfUsuarioLogado       = $this->retornaCpfUsuarioLogado(SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno());

        if(empty($cpfUsuarioLogado)){
            $cpfUsuarioLogado = self::CPF_SOLICITANTE_SEM_USUARIO_EXTERNO;
        }

        // Inicializa o array que será passado para o WS
        $arrParamsWebservice    = ['cnpj' => $cnpj];

        //Recuperando meses - alterado
        $objMdPetIntegParametroDTO = new MdPetIntegParametroDTO();
        $objMdPetIntegParametroDTO->retStrValorPadrao();
        $objMdPetIntegParametroDTO->retStrTpParametro();
        $objMdPetIntegParametroDTO->retStrNome();
        $objMdPetIntegParametroDTO->retStrNomeCampo();
        $objMdPetIntegParametroDTO->retStrStaLocalParametro();
        $objMdPetIntegParametroDTO->retStrSinDadoConfidencial();
        $objMdPetIntegParametroDTO->setNumIdMdPetIntegracao($objMdPetIntegracao->getNumIdMdPetIntegracao());
        $objMdPetIntegParametroRN = new MdPetIntegParametroRN();
        $arrObjMdPetIntegParametroRN = $objMdPetIntegParametroRN->listar($objMdPetIntegParametroDTO);

        if (!$arrObjMdPetIntegParametroRN) {
            throw new InfraException('A integração de consulta de CNPJ na Receita Federal está sem parâmetros.');
        }

        $mes = 0;
        $chaveMes = '';
        $arrSaidaWS = [];

        // Verifica os demais parametros mapeados para incluir no array e passar para o WS
        foreach ($arrObjMdPetIntegParametroRN as $itemParam) {

            // Verificação do Cache
            if ($itemParam->getStrTpParametro() == 'E' && $itemParam->getStrNome() == 'mesesExpiraCache') {

                $mes = (int)$itemParam->getStrValorPadrao();
                $chaveMes = $itemParam->getStrNome();
                $arrParamsWebservice[$chaveMes] = $mes;

            }
            
            // Verificação da Origem
            if ($itemParam->getStrTpParametro() == 'E' && $itemParam->getStrNome() == 'identificacaoOrigem' && $itemParam->getStrNomeCampo() == 'origem') {
                
                $idUsuario = (new InfraParametro(BancoSEI::getInstance()))->getValor(MdPetContatoRN::$STR_INFRA_PARAMETRO_SIGLA_CONTATO, false);

                if ($idUsuario != '' && !is_null($idUsuario)) {
                    
                    $objUsuarioDTO = new UsuarioDTO();
                    $objUsuarioDTO->setNumIdUsuario($idUsuario);
                    $objUsuarioDTO->retStrSigla();
                    $objUsuarioDTO = (new UsuarioRN())->consultarRN0489($objUsuarioDTO);

                    if ($objUsuarioDTO) {
                        $arrParamsWebservice['origem'] = $objUsuarioDTO->getStrSigla();
                    }

                }

            }

            // Verificação da cpfUsuario que está requisitando a consulta no WS
            if ($itemParam->getStrTpParametro() == 'E' && $itemParam->getStrNome() == 'cpfPessoa' && $itemParam->getStrNomeCampo() == 'cpfUsuario') {
                if (!empty($cpfUsuarioLogado)) {
                    $arrParamsWebservice['cpfUsuario'] = $cpfUsuarioLogado;
                }
            }

            // Mapeando os de saida todos de uma vez
            if ($itemParam->getStrTpParametro() == 'P'){
                $arrSaidaWS[$itemParam->getStrNome()] = $itemParam->getStrNomeCampo();
            }

        }

        // Monta a consulta para o WS
        $parametro = [ $strMetodoWebservice => $arrParamsWebservice ];
        $this->objCliente = $this->obterCliente($objMdPetIntegracao, $arrObjMdPetIntegParametroRN);
        $retorno = $this->objCliente->consultar($strMetodoWebservice, $parametro);

        // Falha do WS volta como array sem a pessoa jurídica, com a mensagem em "faultstring " ou "msg".
        if (!$this->objCliente->possuiDados($retorno, 'PessoaJuridica')) {
            throw new InfraException('A consulta do CNPJ na Receita Federal não retornou a pessoa jurídica. Retorno do WS: ' . ($retorno['faultstring '] ?? $retorno['msg'] ?? 'vazio'));
        }

        $this->atualizaContatoCNPJMedianteConsultaReceitaWSConectado($retorno, $arrSaidaWS);

        $cpfResponsavelLegal = InfraUtil::retirarFormatacao((string) $this->objCliente->lerDado($retorno, $arrSaidaWS['cpfRespLegal'] ?? null));

        return [
            'IntegracaoCodigosParaSuspensao' => explode(',', $objMdPetIntegracao->getStrCodReceitaSuspAuto()),
            'RetornoConsultaReceitaWS' => $retorno,
            'CodigoSituacaoCadastral' => $this->objCliente->lerDado($retorno, $arrSaidaWS['codSituacaoCadastral'] ?? null),
            'DescricaoSituacaoCadastral' => $this->objCliente->lerDado($retorno, $arrSaidaWS['descSituacaoCadastral'] ?? null),
            'CpfResponsavelLegal' => $cpfResponsavelLegal === '' ? null : str_pad($cpfResponsavelLegal, 11, '0', STR_PAD_LEFT)
        ];
    }

    /** Devolve o cliente da integração, criado na primeira consulta, para não baixar o WSDL a cada CNPJ. */
    private function obterCliente(MdPetIntegracaoDTO $objMdPetIntegracao, array $arrObjMdPetIntegParametro): MdPetIntegClienteRN
    {
        return $this->arrObjCliente[$objMdPetIntegracao->getNumIdMdPetIntegracao()] ??= MdPetIntegFabricaRN::criarCliente($objMdPetIntegracao, $arrObjMdPetIntegParametro);
    }

    protected function consultarCPFReceitaWsResponsavelLegalConectado($cpf)
    {

        $objMdPetIntegracao = $this->consultarIntegracaoPorIdMdPetIntegFuncionalid(MdPetIntegFuncionalidRN::$ID_FUNCIONALIDADE_CPF_RECEITA_FEDERAL);
        $cpf = str_pad(InfraUtil::retirarFormatacao($cpf), '11', '0', STR_PAD_LEFT);

        if($objMdPetIntegracao){

            $strMetodoWebservice    = $objMdPetIntegracao->getStrOperacaoWsdl();
            $cpfUsuarioLogado       = $this->retornaCpfUsuarioLogado(SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno());

            if(empty($cpfUsuarioLogado)){
                $cpfUsuarioLogado = $cpf;
            }

            // Inicializa o array que será passado para o WS
            $arrParamsWebservice    = ['cpf' => $cpf];

            //Recuperando meses - alterado
            $objMdPetIntegParametroDTO = new MdPetIntegParametroDTO();
            $objMdPetIntegParametroDTO->retStrValorPadrao();
            $objMdPetIntegParametroDTO->retStrTpParametro();
            $objMdPetIntegParametroDTO->retStrNome();
            $objMdPetIntegParametroDTO->retStrNomeCampo();
            $objMdPetIntegParametroDTO->retStrStaLocalParametro();
            $objMdPetIntegParametroDTO->retStrSinDadoConfidencial();
            $objMdPetIntegParametroDTO->setNumIdMdPetIntegracao($objMdPetIntegracao->getNumIdMdPetIntegracao());
            $arrObjMdPetIntegParametroRN = (new MdPetIntegParametroRN())->listar($objMdPetIntegParametroDTO);

            if ($arrObjMdPetIntegParametroRN) {

                $mes = 0;
                $chaveMes = '';

                foreach ($arrObjMdPetIntegParametroRN as $itemParam) {

                    if ($itemParam->getStrTpParametro() == 'E' && $itemParam->getStrNome() == 'mesesExpiraCache') {

                        $mes = (int)$itemParam->getStrValorPadrao();
                        $chaveMes = $itemParam->getStrNome();
                        $arrParamsWebservice[$chaveMes] = $mes;

                    }
                    
                    // Verificação da Origem
                    if ($itemParam->getStrTpParametro() == 'E' && $itemParam->getStrNome() == 'identificacaoOrigem' && $itemParam->getStrNomeCampo() == 'origem') {
                        
                        $idUsuario = (new InfraParametro(BancoSEI::getInstance()))->getValor(MdPetContatoRN::$STR_INFRA_PARAMETRO_SIGLA_CONTATO, false);

                        if ($idUsuario != '' && !is_null($idUsuario)) {
                            
                            $objUsuarioDTO = new UsuarioDTO();
                            $objUsuarioDTO->setNumIdUsuario($idUsuario);
                            $objUsuarioDTO->retStrSigla();
                            $objUsuarioDTO = (new UsuarioRN())->consultarRN0489($objUsuarioDTO);

                            if ($objUsuarioDTO) {
                                $arrParamsWebservice['origem'] = $objUsuarioDTO->getStrSigla();
                            }

                        }

                    }

                    // Verificação da cpfUsuario que está requisitando a consulta no WS
                    if ($itemParam->getStrTpParametro() == 'E' && $itemParam->getStrNome() == 'cpfUsuario' && $itemParam->getStrNomeCampo() == 'cpfUsuario') {
                        if (!empty($cpfUsuarioLogado)) {
                            $arrParamsWebservice['cpfUsuario'] = $cpfUsuarioLogado;
                        }
                    }

                }

                $parametro = [ $strMetodoWebservice => $arrParamsWebservice ];

                $this->objCliente = MdPetIntegFabricaRN::criarCliente($objMdPetIntegracao, $arrObjMdPetIntegParametroRN);
                $retorno = $this->objCliente->consultar($strMetodoWebservice, $parametro);

                if ($this->objCliente->possuiDados($retorno, 'PessoaFisica')) {

                    $arrSaidaWS = [];
                    foreach ($arrObjMdPetIntegParametroRN as $itemParam) {
                        if ($itemParam->getStrTpParametro() == 'P') {
                            $arrSaidaWS[$itemParam->getStrNome()] = $itemParam->getStrNomeCampo();
                        }
                    }

                    $codigoRetornado = $this->objCliente->lerDado($retorno, $arrSaidaWS['codSituacaoCadastral'] ?? null);

                    $arrCodReceita = explode(',', $objMdPetIntegracao->getStrCodReceitaSuspAuto());

                    if ($codigoRetornado && in_array($codigoRetornado,$arrCodReceita)) {
                        return true;
                    }

                }

            }

        }

        return false;
    }

    protected function inicializarObjInfraIBanco()
    {
        return BancoSEI::getInstance();
    }

    private function validarNumIdMdPetIntegFuncionalid(MdPetIntegracaoDTO $objMdPetIntegracaoDTO, InfraException $objInfraException)
    {
        if (InfraString::isBolVazia($objMdPetIntegracaoDTO->getNumIdMdPetIntegFuncionalid())) {
            $objInfraException->adicionarValidacao('Funcionalidade não informada.');
        }
    }

    private function validarStrNome(MdPetIntegracaoDTO $objMdPetIntegracaoDTO, InfraException $objInfraException)
    {
        if (InfraString::isBolVazia($objMdPetIntegracaoDTO->getStrNome())) {
            $objInfraException->adicionarValidacao('Nome não informado.');
        } else {
            $objMdPetIntegracaoDTO->setStrNome(trim($objMdPetIntegracaoDTO->getStrNome()));

            if (strlen($objMdPetIntegracaoDTO->getStrNome()) > 30) {
                $objInfraException->adicionarValidacao('Nome possui tamanho superior a 30 caracteres.');
            }
        }
    }

    private function validarStrEnderecoWsdl(MdPetIntegracaoDTO $objMdPetIntegracaoDTO, InfraException $objInfraException)
    {
        if (InfraString::isBolVazia($objMdPetIntegracaoDTO->getStrEnderecoWsdl())) {
            $objInfraException->adicionarValidacao('Endereço do Webservice não informado.');
        } else {
            $objMdPetIntegracaoDTO->setStrEnderecoWsdl(trim($objMdPetIntegracaoDTO->getStrEnderecoWsdl()));

            if (strlen($objMdPetIntegracaoDTO->getStrEnderecoWsdl()) > 100) {
                $objInfraException->adicionarValidacao('Endereço do Webservice possui tamanho superior a 100 caracteres.');
            }
        }
    }

    /**
     * Endereços e operação da integração. No REST, o documento OpenAPI, o endereço de execução, o método e o caminho; nos
     * demais tipos, o endereço do WSDL, como antes do Tipo de Integração.
     */
    private function validarEnderecos(MdPetIntegracaoDTO $objMdPetIntegracaoDTO, InfraException $objInfraException): void
    {
        if (!self::isIntegracaoRest($objMdPetIntegracaoDTO)) {
            $this->validarStrEnderecoWsdl($objMdPetIntegracaoDTO, $objInfraException);
            return;
        }

        self::validarEnderecoRest($objMdPetIntegracaoDTO->getStrEnderecoOpenapi(), 'Endereço do Documento OpenAPI', $objInfraException);
        self::validarEnderecoRest($objMdPetIntegracaoDTO->getStrEnderecoExecucao(), 'Endereço de Execução', $objInfraException);

        if (InfraString::isBolVazia($objMdPetIntegracaoDTO->getStrMetodoHttp()) || InfraString::isBolVazia($objMdPetIntegracaoDTO->getStrCaminhoOperacao())) {
            $objInfraException->adicionarValidacao('Operação não informada.');
        }
    }

    private static function validarEnderecoRest(?string $strEndereco, string $strRotulo, InfraException $objInfraException): void
    {
        $strEndereco = trim((string) $strEndereco);

        if ($strEndereco === '') {
            $objInfraException->adicionarValidacao($strRotulo . ' não informado.');
            return;
        }

        if (strlen($strEndereco) > 250) {
            $objInfraException->adicionarValidacao($strRotulo . ' possui tamanho superior a 250 caracteres.');
            return;
        }

        if (!preg_match('#^https?://#i', $strEndereco) || filter_var($strEndereco, FILTER_VALIDATE_URL) === false) {
            $objInfraException->adicionarValidacao($strRotulo . ' não é um endereço HTTP ou HTTPS válido.');
        }
    }

    private static function isIntegracaoRest(MdPetIntegracaoDTO $objMdPetIntegracaoDTO): bool
    {
        return $objMdPetIntegracaoDTO->isSetStrTpClienteWs() && $objMdPetIntegracaoDTO->getStrTpClienteWs() === MdPetIntegFabricaRN::TIPO_REST;
    }

    /**
     * Obrigatórios do mapeamento REST, os mesmos que a tela exige para cada funcionalidade.
     *
     * @param array<string, mixed> $arrEntrada campo do serviço escolhido para cada dado de entrada
     * @param array<string, mixed> $arrSaida campo do serviço escolhido para cada dado de saída
     */
    private static function validarMapeamentoRest(MdPetIntegracaoDTO $objMdPetIntegracaoDTO, array $arrEntrada, array $arrSaida, bool $bolNumeroSeparado, InfraException $objInfraException): void
    {
        if ($objMdPetIntegracaoDTO->getNumIdMdPetIntegFuncionalid() == MdPetIntegFuncionalidRN::$ID_FUNCIONALIDADE_CNPJ_RECEITA_FEDERAL) {
            $arrObrigatoriosEntrada = ['cnpjEmpresa' => 'CNPJ da Pessoa Jurídica'];
            $arrObrigatoriosSaida = [
                'cnpjEmpresa' => 'CNPJ da Pessoa Jurídica', 'razaoSocial' => 'Razão Social', 'codSituacaoCadastral' => 'Código da Situação Cadastral',
                'descSituacaoCadastral' => 'Descrição da Situação Cadastral', 'logradouro' => 'Logradouro do Endereço', 'cep' => 'CEP do Endereço',
                'bairro' => 'Bairro do Endereço', 'codIbgeMunicipio' => 'Código IBGE do Município do Endereço', 'cpfRespLegal' => 'CPF do Responsável Legal',
                'nomeRespLegal' => 'Nome do Responsável Legal',
            ];
            if ($bolNumeroSeparado) {
                $arrObrigatoriosSaida['numero'] = 'Número do Endereço';
            }
        } else {
            $arrObrigatoriosEntrada = ['cpfUsuario' => 'CPF do Usuário requisitante', 'identificacaoOrigem' => 'Identificação Origem'];
            $arrObrigatoriosSaida = ['codSituacaoCadastral' => 'Código da Situação Cadastral', 'descSituacaoCadastral' => 'Descrição da Situação Cadastral'];
        }

        foreach ($arrObrigatoriosEntrada as $strChave => $strRotulo) {
            if (InfraString::isBolVazia($arrEntrada[$strChave] ?? null)) {
                $objInfraException->adicionarValidacao('Indique o dado de entrada no webservice para ' . $strRotulo . '.');
            }
        }

        foreach ($arrObrigatoriosSaida as $strChave => $strRotulo) {
            if (InfraString::isBolVazia($arrSaida[$strChave] ?? null)) {
                $objInfraException->adicionarValidacao('Indique o dado de saída do webservice para ' . $strRotulo . '.');
            }
        }
    }
    /**
     * Headers fixos do REST vindos da grade da tela, cada linha com id, atributo, conteúdo exibido, dado restrito e conteúdo
     * enviado. O header de credencial só é aceito como dado restrito, e o dado restrito é gravado ofuscado, no padrão do
     * Utilidades; a máscara no lugar do conteúdo mantém o valor já gravado (ADR-PET-012). Roda antes da gravação auditada
     * da integração, e as linhas da tela saem do DTO, para a auditoria receber só o valor ofuscado.
     *
     * @param array<int, string> $arrRestritosGravados valor ofuscado de cada header restrito gravado, pelo id
     */
    private static function prepararHeaders(MdPetIntegracaoDTO $objMdPetIntegracaoDTO, array $arrRestritosGravados, InfraException $objInfraException): void
    {
        $strMetodoAutenticacao = $objMdPetIntegracaoDTO->isSetStrStaMetodoAutenticacao() ? $objMdPetIntegracaoDTO->getStrStaMetodoAutenticacao() : null;
        $arrLinhas = $objMdPetIntegracaoDTO->isSetArrLinhasHeaders() ? (array) $objMdPetIntegracaoDTO->getArrLinhasHeaders() : [];
        $objMdPetIntegracaoDTO->setArrLinhasHeaders(null);

        $arrHeaders = [];
        foreach ($arrLinhas as $arrLinha) {
            $strNome = trim((string) ($arrLinha[1] ?? ''));
            $strValor = trim((string) ($arrLinha[4] ?? ''));
            $bolRestrito = ($arrLinha[3] ?? '') === 'Sim';

            if (!preg_match('/^[A-Za-z0-9-]{1,30}$/', $strNome)) {
                $objInfraException->adicionarValidacao('Header fixo inválido: ' . $strNome . '. O Atributo aceita até 30 letras, números e hífens.');
                continue;
            }

            if (!$bolRestrito && in_array(strtolower($strNome), ['authorization', 'proxy-authorization', 'cookie'], true)) {
                $objInfraException->adicionarValidacao('O header ' . $strNome . ' leva credencial e só é aceito marcado como Dado Restrito.');
                continue;
            }

            $numId = is_numeric($arrLinha[0] ?? null) ? (int) $arrLinha[0] : 0;
            if ($bolRestrito && $strValor === MdPetIntegFabricaRN::MASCARA_DADO_RESTRITO && isset($arrRestritosGravados[$numId])) {
                $arrHeaders[] = ['nome' => $strNome, 'valor' => $arrRestritosGravados[$numId], 'restrito' => 'S'];
                continue;
            }

            if ($strValor === '' || $strValor === MdPetIntegFabricaRN::MASCARA_DADO_RESTRITO || strlen($strValor) > 100) {
                $objInfraException->adicionarValidacao('Informe o Conteúdo do header ' . $strNome . ', com até 100 caracteres.');
                continue;
            }

            $arrHeaders[] = ['nome' => $strNome, 'valor' => $bolRestrito ? MdPetIntegracaoINT::ofuscarDadoRestrito($strValor) : $strValor, 'restrito' => $bolRestrito ? 'S' : 'N'];
        }

        if ($strMetodoAutenticacao === MdPetIntegFabricaRN::AUTENTICACAO_HEADER && !in_array('S', array_column($arrHeaders, 'restrito'), true)) {
            $objInfraException->adicionarValidacao('O Método de Autenticação Header Authentication by Token exige ao menos um header marcado como Dado Restrito.');
        }

        $objMdPetIntegracaoDTO->setArrHeaders($arrHeaders);
    }

    /**
     * Token do Body Authentication by Token vindo da tela: exige operação que envia corpo, o campo do serviço e o conteúdo,
     * com até 100 caracteres, e deixa no DTO o conteúdo ofuscado, no padrão dos headers; a máscara no lugar do conteúdo
     * mantém o valor já gravado. Roda antes da gravação auditada, e o conteúdo digitado sai do DTO (ADR-PET-013).
     *
     * @param string|null $strTokenGravado valor ofuscado do token já gravado na integração
     */
    private static function prepararToken(MdPetIntegracaoDTO $objMdPetIntegracaoDTO, ?string $strTokenGravado, InfraException $objInfraException): void
    {
        $strMetodoAutenticacao = $objMdPetIntegracaoDTO->isSetStrStaMetodoAutenticacao() ? $objMdPetIntegracaoDTO->getStrStaMetodoAutenticacao() : null;
        $strCampo = $objMdPetIntegracaoDTO->isSetStrCampoTokenAutenticacao() ? trim((string) $objMdPetIntegracaoDTO->getStrCampoTokenAutenticacao()) : '';
        $strValor = $objMdPetIntegracaoDTO->isSetStrTokenAutenticacao() ? trim((string) $objMdPetIntegracaoDTO->getStrTokenAutenticacao()) : '';
        $objMdPetIntegracaoDTO->setStrCampoTokenAutenticacao(null);
        $objMdPetIntegracaoDTO->setStrTokenAutenticacao(null);

        if ($strMetodoAutenticacao !== MdPetIntegFabricaRN::AUTENTICACAO_CORPO) {
            return;
        }

        $strMetodoHttp = $objMdPetIntegracaoDTO->isSetStrMetodoHttp() ? strtoupper((string) $objMdPetIntegracaoDTO->getStrMetodoHttp()) : '';
        if (!in_array($strMetodoHttp, ['POST', 'PUT', 'PATCH'], true)) {
            $objInfraException->adicionarValidacao('O Método de Autenticação Body Authentication by Token exige operação que envia corpo, como POST.');
            return;
        }

        if ($strCampo === '') {
            $objInfraException->adicionarValidacao('Indique o dado de entrada no webservice para Authorization.');
        }

        $bolMantem = $strValor === MdPetIntegFabricaRN::MASCARA_DADO_RESTRITO && $strTokenGravado !== null;
        if (!$bolMantem && ($strValor === '' || $strValor === MdPetIntegFabricaRN::MASCARA_DADO_RESTRITO || strlen($strValor) > 100)) {
            $objInfraException->adicionarValidacao('Informe o conteúdo do Authorization, com até 100 caracteres.');
            return;
        }

        $objMdPetIntegracaoDTO->setStrCampoTokenAutenticacao($strCampo);
        $objMdPetIntegracaoDTO->setStrTokenAutenticacao($bolMantem ? $strTokenGravado : MdPetIntegracaoINT::ofuscarDadoRestrito($strValor));
    }

    /**
     * Valor ofuscado do token do corpo já gravado na integração, para a máscara da tela manter o valor.
     */
    private function consultarTokenGravado(MdPetIntegracaoDTO $objMdPetIntegracaoDTO): ?string
    {
        $objMdPetIntegParametroDTO = new MdPetIntegParametroDTO();
        $objMdPetIntegParametroDTO->retStrValorPadrao();
        $objMdPetIntegParametroDTO->setNumIdMdPetIntegracao($objMdPetIntegracaoDTO->getNumIdMdPetIntegracao());
        $objMdPetIntegParametroDTO->setStrTpParametro(MdPetIntegFabricaRN::PARAMETRO_ENTRADA);
        $objMdPetIntegParametroDTO->setStrNome(MdPetIntegFabricaRN::ENTRADA_TOKEN);
        $objMdPetIntegParametroDTO->setStrSinDadoConfidencial('S');

        $arrValores = InfraArray::converterArrInfraDTO((new MdPetIntegParametroRN())->listar($objMdPetIntegParametroDTO), 'ValorPadrao');
        return $arrValores[0] ?? null;
    }

    /**
     * Valor ofuscado de cada header restrito já gravado na integração, pelo id, para a máscara da tela manter o valor.
     *
     * @return array<int, string>
     */
    private function listarHeadersRestritosGravados(MdPetIntegracaoDTO $objMdPetIntegracaoDTO): array
    {
        $objMdPetIntegParametroDTO = new MdPetIntegParametroDTO();
        $objMdPetIntegParametroDTO->retNumIdMdPetIntegParametro();
        $objMdPetIntegParametroDTO->retStrValorPadrao();
        $objMdPetIntegParametroDTO->setNumIdMdPetIntegracao($objMdPetIntegracaoDTO->getNumIdMdPetIntegracao());
        $objMdPetIntegParametroDTO->setStrTpParametro(MdPetIntegFabricaRN::PARAMETRO_HEADER);
        $objMdPetIntegParametroDTO->setStrSinDadoConfidencial('S');

        return InfraArray::converterArrInfraDTO((new MdPetIntegParametroRN())->listar($objMdPetIntegParametroDTO), 'ValorPadrao', 'IdMdPetIntegParametro');
    }

    /**
     * Local do campo de entrada na chamada REST, vindo da descoberta: Q, C, H ou B; outro valor fica sem local.
     */
    private static function normalizarLocal(mixed $varLocal): ?string
    {
        return in_array($varLocal, [MdPetIntegRestRN::LOCAL_QUERY, MdPetIntegRestRN::LOCAL_CAMINHO, MdPetIntegRestRN::LOCAL_HEADER, MdPetIntegRestRN::LOCAL_CORPO], true) ? $varLocal : null;
    }

    private function validarStrOperacaoWsdl(MdPetIntegracaoDTO $objMdPetIntegracaoDTO, InfraException $objInfraException)
    {
        if (InfraString::isBolVazia($objMdPetIntegracaoDTO->getStrOperacaoWsdl())) {
            $objMdPetIntegracaoDTO->setStrOperacaoWsdl(null);
        } else {
            $objMdPetIntegracaoDTO->setStrOperacaoWsdl(trim($objMdPetIntegracaoDTO->getStrOperacaoWsdl()));

            if (strlen($objMdPetIntegracaoDTO->getStrOperacaoWsdl()) > 50) {
                $objInfraException->adicionarValidacao('Operação possui tamanho superior a 50 caracteres.');
            }
        }
    }

    private function validarStrSinCache(MdPetIntegracaoDTO $objMdPetIntegracaoDTO, InfraException $objInfraException)
    {
        if (InfraString::isBolVazia($objMdPetIntegracaoDTO->getStrSinCache())) {
            $objInfraException->adicionarValidacao('Sinalizador de Marque caso seu Webservice tenha controle de expiração de cache não informado.');
        } else {
            if (!InfraUtil::isBolSinalizadorValido($objMdPetIntegracaoDTO->getStrSinCache())) {
                $objInfraException->adicionarValidacao('Sinalizador de Marque caso seu Webservice tenha controle de expiração de cache inválido.');
            }
        }
    }

    private function validarStrSinTpLogradouro(MdPetIntegracaoDTO $objMdPetIntegracaoDTO, InfraException $objInfraException)
    {
        if (InfraString::isBolVazia($objMdPetIntegracaoDTO->getStrSinTpLogradouro())) {
            $objInfraException->adicionarValidacao('Sinalizador de Marque caso seu Webservice tenha o Tipo do Logradouro não informado.');
        } else {
            if (!InfraUtil::isBolSinalizadorValido($objMdPetIntegracaoDTO->getStrSinTpLogradouro())) {
                $objInfraException->adicionarValidacao('Sinalizador de Marque caso seu Webservice tenha o Tipo do Logradouro inválido.');
            }
        }
    }

    private function validarStrSinNuLogradouro(MdPetIntegracaoDTO $objMdPetIntegracaoDTO, InfraException $objInfraException)
    {
        if (InfraString::isBolVazia($objMdPetIntegracaoDTO->getStrSinNuLogradouro())) {
            $objInfraException->adicionarValidacao('Sinalizador de Marque caso seu Webservice tenha o Número do Logradouro não informado.');
        } else {
            if (!InfraUtil::isBolSinalizadorValido($objMdPetIntegracaoDTO->getStrSinNuLogradouro())) {
                $objInfraException->adicionarValidacao('Sinalizador de Marque caso seu Webservice tenha o Número do Logradouro inválido.');
            }
        }
    }

    private function validarStrSinCompLogradouro(MdPetIntegracaoDTO $objMdPetIntegracaoDTO, InfraException $objInfraException)
    {
        if (InfraString::isBolVazia($objMdPetIntegracaoDTO->getStrSinCompLogradouro())) {
            $objInfraException->adicionarValidacao('Sinalizador de Marque caso seu Webservice tenha o Complemento do Logradouro não informado.');
        } else {
            if (!InfraUtil::isBolSinalizadorValido($objMdPetIntegracaoDTO->getStrSinCompLogradouro())) {
                $objInfraException->adicionarValidacao('Sinalizador de Marque caso seu Webservice tenha o Complemento do Logradouro de cache inválido.');
            }
        }
    }

    private function validarStrSinAtivo(MdPetIntegracaoDTO $objMdPetIntegracaoDTO, InfraException $objInfraException)
    {
        if (InfraString::isBolVazia($objMdPetIntegracaoDTO->getStrSinAtivo())) {
            $objInfraException->adicionarValidacao('Sinalizador de Exclusão Lógica não informado.');
        } else {
            if (!InfraUtil::isBolSinalizadorValido($objMdPetIntegracaoDTO->getStrSinAtivo())) {
                $objInfraException->adicionarValidacao('Sinalizador de Exclusão Lógica inválido.');
            }
        }
    }

    protected function cadastrarControlado(MdPetIntegracaoDTO $objMdPetIntegracaoDTO)
    {
        try {

            //Valida Permissao
            SessaoSEI::getInstance()->validarAuditarPermissao('md_pet_integracao_cadastrar', __METHOD__, $objMdPetIntegracaoDTO);


            //Regras de Negocio
            $objInfraException = new InfraException();

            $this->validarNumIdMdPetIntegFuncionalid($objMdPetIntegracaoDTO, $objInfraException);
            $this->validarStrNome($objMdPetIntegracaoDTO, $objInfraException);
            if ($objMdPetIntegracaoDTO->getStrStaUtilizarWs() == 'S') {
                $this->validarEnderecos($objMdPetIntegracaoDTO, $objInfraException);
                $this->validarStrOperacaoWsdl($objMdPetIntegracaoDTO, $objInfraException);
                $this->validarStrSinCache($objMdPetIntegracaoDTO, $objInfraException);
            }
            $this->validarStrSinAtivo($objMdPetIntegracaoDTO, $objInfraException);

            $objInfraException->lancarValidacoes();

            $objMdPetIntegracaoBD = new MdPetIntegracaoBD($this->getObjInfraIBanco());
            $ret = $objMdPetIntegracaoBD->cadastrar($objMdPetIntegracaoDTO);

            //Auditoria

            return $ret;

        } catch (Exception $e) {
            throw new InfraException('Erro cadastrando Integração.', $e);
        }
    }

    protected function alterarControlado(MdPetIntegracaoDTO $objMdPetIntegracaoDTO)
    {
        try {

            //Valida Permissao
            SessaoSEI::getInstance()->validarAuditarPermissao('md_pet_integracao_alterar', __METHOD__, $objMdPetIntegracaoDTO);

            //Regras de Negocio
            $objInfraException = new InfraException();

            if ($objMdPetIntegracaoDTO->isSetNumIdMdPetIntegFuncionalid()) {
                $this->validarNumIdMdPetIntegFuncionalid($objMdPetIntegracaoDTO, $objInfraException);
            }
            if ($objMdPetIntegracaoDTO->isSetStrNome()) {
                $this->validarStrNome($objMdPetIntegracaoDTO, $objInfraException);
            }
            if ($objMdPetIntegracaoDTO->getStrStaUtilizarWs() == 'S') {
                if ($objMdPetIntegracaoDTO->isSetStrEnderecoWsdl()) {
                    $this->validarEnderecos($objMdPetIntegracaoDTO, $objInfraException);
                }
                if ($objMdPetIntegracaoDTO->isSetStrOperacaoWsdl()) {
                    $this->validarStrOperacaoWsdl($objMdPetIntegracaoDTO, $objInfraException);
                }
                if ($objMdPetIntegracaoDTO->isSetStrSinCache()) {
                    $this->validarStrSinCache($objMdPetIntegracaoDTO, $objInfraException);
                }
                if ($objMdPetIntegracaoDTO->isSetStrSinTpLogradouro()) {
                    $this->validarStrSinTpLogradouro($objMdPetIntegracaoDTO, $objInfraException);
                }
                if ($objMdPetIntegracaoDTO->isSetStrSinNuLogradouro()) {
                    $this->validarStrSinNuLogradouro($objMdPetIntegracaoDTO, $objInfraException);
                }
                if ($objMdPetIntegracaoDTO->isSetStrSinCompLogradouro()) {
                    $this->validarStrSinCompLogradouro($objMdPetIntegracaoDTO, $objInfraException);
                }
            }
            if ($objMdPetIntegracaoDTO->isSetStrSinAtivo()) {
                $this->validarStrSinAtivo($objMdPetIntegracaoDTO, $objInfraException);
            }

            $objInfraException->lancarValidacoes();

            $objMdPetIntegracaoBD = new MdPetIntegracaoBD($this->getObjInfraIBanco());
            $objMdPetIntegracaoBD->alterar($objMdPetIntegracaoDTO);

            //Auditoria

        } catch (Exception $e) {
            throw new InfraException('Erro alterando Integração.', $e);
        }
    }

    protected function excluirControlado($arrObjMdPetIntegracaoDTO)
    {
        try {

            //Valida Permissao
            SessaoSEI::getInstance()->validarAuditarPermissao('md_pet_integracao_excluir', __METHOD__, $arrObjMdPetIntegracaoDTO);

            //Regras de Negocio
            //$objInfraException = new InfraException();

            //$objInfraException->lancarValidacoes();

            $objMdPetIntegracaoBD = new MdPetIntegracaoBD($this->getObjInfraIBanco());
            for ($i = 0; $i < count($arrObjMdPetIntegracaoDTO); $i++) {
                $objMdPetIntegracaoBD->excluir($arrObjMdPetIntegracaoDTO[$i]);
            }

            //Auditoria

        } catch (Exception $e) {
            throw new InfraException('Erro excluindo Integração.', $e);
        }
    }

    protected function consultarConectado(MdPetIntegracaoDTO $objMdPetIntegracaoDTO)
    {
        try {

            //Valida Permissao
            SessaoSEI::getInstance()->validarPermissao('md_pet_integracao_consultar');

            //Regras de Negocio
            //$objInfraException = new InfraException();

            //$objInfraException->lancarValidacoes();

            $objMdPetIntegracaoBD = new MdPetIntegracaoBD($this->getObjInfraIBanco());
            $ret = $objMdPetIntegracaoBD->consultar($objMdPetIntegracaoDTO);

            //Auditoria

            return $ret;
        } catch (Exception $e) {
            throw new InfraException('Erro consultando Integração.', $e);
        }
    }

    protected function listarConectado(MdPetIntegracaoDTO $objMdPetIntegracaoDTO)
    {
        try {

            //Valida Permissao
            SessaoSEI::getInstance()->validarPermissao('md_pet_integracao_listar');

            //Regras de Negocio
            //$objInfraException = new InfraException();

            //$objInfraException->lancarValidacoes();

            $objMdPetIntegracaoBD = new MdPetIntegracaoBD($this->getObjInfraIBanco());
            $ret = $objMdPetIntegracaoBD->listar($objMdPetIntegracaoDTO);

            //Auditoria

            return $ret;

        } catch (Exception $e) {
            throw new InfraException('Erro listando Integrações.', $e);
        }
    }

    protected function contarConectado(MdPetIntegracaoDTO $objMdPetIntegracaoDTO)
    {
        try {

            //Valida Permissao
            SessaoSEI::getInstance()->validarPermissao('md_pet_integracao_listar');

            //Regras de Negocio
            //$objInfraException = new InfraException();

            //$objInfraException->lancarValidacoes();

            $objMdPetIntegracaoBD = new MdPetIntegracaoBD($this->getObjInfraIBanco());
            $ret = $objMdPetIntegracaoBD->contar($objMdPetIntegracaoDTO);

            //Auditoria

            return $ret;
        } catch (Exception $e) {
            throw new InfraException('Erro contando Integrações.', $e);
        }
    }

    protected function desativarControlado($arrObjMdPetIntegracaoDTO)
    {
        try {

            //Valida
            SessaoSEI::getInstance()->validarAuditarPermissao('md_pet_integracao_desativar', __METHOD__, $arrObjMdPetIntegracaoDTO);

            //Regras de Negocio
            //$objInfraException = new InfraException();

            //$objInfraException->lancarValidacoes();

            $objMdPetIntegracaoBD = new MdPetIntegracaoBD($this->getObjInfraIBanco());
            for ($i = 0; $i < count($arrObjMdPetIntegracaoDTO); $i++) {
                $objMdPetIntegracaoBD->desativar($arrObjMdPetIntegracaoDTO[$i]);
            }

            //Auditoria

        } catch (Exception $e) {
            throw new InfraException('Erro desativando Integração.', $e);
        }
    }

    protected function reativarControlado($arrObjMdPetIntegracaoDTO)
    {
        try {

            //Valida Permissao
            SessaoSEI::getInstance()->validarAuditarPermissao('md_pet_integracao_reativar', __METHOD__, $arrObjMdPetIntegracaoDTO);

            $objMdPetIntegracaoBD = new MdPetIntegracaoBD($this->getObjInfraIBanco());
            for ($i = 0; $i < count($arrObjMdPetIntegracaoDTO); $i++) {

                //Funcionalidade utilizada em outra integração
                $objMdPetIntegFuncionalidRN = new MdPetIntegFuncionalidRN();
                $arrIdMdPetIntegFuncionalidUtilizado = $objMdPetIntegFuncionalidRN->verificarMdPetIntegFuncionalidUtilizado($arrObjMdPetIntegracaoDTO[$i]->getNumIdMdPetIntegracao(), $arrObjMdPetIntegracaoDTO[$i]->getNumIdMdPetIntegFuncionalid());

                if ($arrIdMdPetIntegFuncionalidUtilizado) {
                    //Regras de Negocio
                    $objInfraException = new InfraException();

                    $objInfraException->adicionarValidacao('Funcionalidade sendo utilizada por outra Integração.');

                    $objInfraException->lancarValidacoes();
                }

                $objMdPetIntegracaoBD->reativar($arrObjMdPetIntegracaoDTO[$i]);
            }

            //Auditoria

        } catch (Exception $e) {
            throw new InfraException('Erro reativando Integração.', $e);
        }
    }

    protected function bloquearControlado(MdPetIntegracaoDTO $objMdPetIntegracaoDTO)
    {
        try {

            //Valida Permissao
            SessaoSEI::getInstance()->validarPermissao('md_pet_integracao_consultar');

            //Regras de Negocio
            //$objInfraException = new InfraException();

            //$objInfraException->lancarValidacoes();

            $objMdPetIntegracaoBD = new MdPetIntegracaoBD($this->getObjInfraIBanco());
            $ret = $objMdPetIntegracaoBD->bloquear($objMdPetIntegracaoDTO);

            //Auditoria

            return $ret;
        } catch (Exception $e) {
            throw new InfraException('Erro bloqueando Integração.', $e);
        }
    }


    protected function cadastrarCompletoControlado(MdPetIntegracaoDTO $objMdPetIntegracaoDTO)
    {
        try {

            //Valida Permissao
            SessaoSEI::getInstance()->validarPermissao('md_pet_integracao_cadastrar');

            //Regras de Negocio
            $objInfraException = new InfraException();

            $this->validarStrNome($objMdPetIntegracaoDTO, $objInfraException);
            if ($objMdPetIntegracaoDTO->getStrStaUtilizarWs() == 'S') {
                $this->validarEnderecos($objMdPetIntegracaoDTO, $objInfraException);
                $this->validarStrOperacaoWsdl($objMdPetIntegracaoDTO, $objInfraException);
                $this->validarStrSinCache($objMdPetIntegracaoDTO, $objInfraException);
                if (self::isIntegracaoRest($objMdPetIntegracaoDTO)) {
                    self::prepararHeaders($objMdPetIntegracaoDTO, [], $objInfraException);
                    self::prepararToken($objMdPetIntegracaoDTO, null, $objInfraException);
                }
            }
            $this->validarStrSinAtivo($objMdPetIntegracaoDTO, $objInfraException);

            //Funcionalidade utilizada em outra integração
            $objMdPetIntegFuncionalidRN = new MdPetIntegFuncionalidRN();
            $arrIdMdPetIntegFuncionalidUtilizado = $objMdPetIntegFuncionalidRN->verificarMdPetIntegFuncionalidUtilizado(null, $objMdPetIntegracaoDTO->getNumIdMdPetIntegFuncionalid());

            if ($arrIdMdPetIntegFuncionalidUtilizado) {
                $objInfraException->adicionarValidacao('Funcionalidade sendo utilizada por outra Integração.');
            }

            $objInfraException->lancarValidacoes();


            $objMdPetIntegracaoRN = new MdPetIntegracaoRN();
            $objMdPetIntegracaoDTO = $objMdPetIntegracaoRN->cadastrar($objMdPetIntegracaoDTO);

            $objMdPetIntegracaoRN->cadastrarParametros($objMdPetIntegracaoDTO);

            return $objMdPetIntegracaoDTO;

        } catch (Exception $e) {
            throw new InfraException('Erro cadastrando Integração.', $e);
        }
    }

    protected function alterarCompletoControlado(MdPetIntegracaoDTO $objMdPetIntegracaoDTO)
    {
        try {

            //Valida Permissao
            SessaoSEI::getInstance()->validarPermissao('md_pet_integracao_alterar');

            //Regras de Negocio
            $objInfraException = new InfraException();

            if ($objMdPetIntegracaoDTO->isSetNumIdMdPetIntegFuncionalid()) {
                $this->validarNumIdMdPetIntegFuncionalid($objMdPetIntegracaoDTO, $objInfraException);
            }
            if ($objMdPetIntegracaoDTO->isSetStrNome()) {
                $this->validarStrNome($objMdPetIntegracaoDTO, $objInfraException);
            }
            if ($objMdPetIntegracaoDTO->getStrStaUtilizarWs() == 'S') {
                if ($objMdPetIntegracaoDTO->isSetStrEnderecoWsdl()) {
                    $this->validarEnderecos($objMdPetIntegracaoDTO, $objInfraException);
                }
                if ($objMdPetIntegracaoDTO->isSetStrOperacaoWsdl()) {
                    $this->validarStrOperacaoWsdl($objMdPetIntegracaoDTO, $objInfraException);
                }
                if ($objMdPetIntegracaoDTO->isSetStrSinCache()) {
                    $this->validarStrSinCache($objMdPetIntegracaoDTO, $objInfraException);
                }
                if ($objMdPetIntegracaoDTO->isSetStrSinTpLogradouro()) {
                    $this->validarStrSinTpLogradouro($objMdPetIntegracaoDTO, $objInfraException);
                }
                if ($objMdPetIntegracaoDTO->isSetStrSinNuLogradouro()) {
                    $this->validarStrSinNuLogradouro($objMdPetIntegracaoDTO, $objInfraException);
                }
                if ($objMdPetIntegracaoDTO->isSetStrSinCompLogradouro()) {
                    $this->validarStrSinCompLogradouro($objMdPetIntegracaoDTO, $objInfraException);
                }
                if ($objMdPetIntegracaoDTO->isSetStrSinAtivo()) {
                    $this->validarStrSinAtivo($objMdPetIntegracaoDTO, $objInfraException);
                }
                //Funcionalidade utilizada em outra integração
                $objMdPetIntegFuncionalidRN = new MdPetIntegFuncionalidRN();
                $arrIdMdPetIntegFuncionalidUtilizado = $objMdPetIntegFuncionalidRN->verificarMdPetIntegFuncionalidUtilizado($objMdPetIntegracaoDTO->getNumIdMdPetIntegracao(), $objMdPetIntegracaoDTO->getNumIdMdPetIntegFuncionalid());

                if ($arrIdMdPetIntegFuncionalidUtilizado) {
                    $objInfraException->adicionarValidacao('Funcionalidade sendo utilizada por outra Integração.');
                }

                if (self::isIntegracaoRest($objMdPetIntegracaoDTO)) {
                    self::prepararHeaders($objMdPetIntegracaoDTO, $this->listarHeadersRestritosGravados($objMdPetIntegracaoDTO), $objInfraException);
                    self::prepararToken($objMdPetIntegracaoDTO, $this->consultarTokenGravado($objMdPetIntegracaoDTO), $objInfraException);
                }

                $objInfraException->lancarValidacoes();
            }

            $objMdPetIntegracaoRN = new MdPetIntegracaoRN();
            $objMdPetIntegracaoRN->alterar($objMdPetIntegracaoDTO);

            if ($objMdPetIntegracaoDTO->getStrStaUtilizarWs() == 'S') {
                $objMdPetIntegracaoRN->excluirParametros($objMdPetIntegracaoDTO);
                $objMdPetIntegracaoRN->cadastrarParametros($objMdPetIntegracaoDTO);
            }
            //Auditoria

        } catch (Exception $e) {
            throw new InfraException('Erro alterando Integração.', $e);
        }
    }

    protected function excluirCompletoControlado($arrObjMdPetIntegracaoDTO)
    {
        try {

            //Valida Permissao
            SessaoSEI::getInstance()->validarAuditarPermissao('md_pet_integracao_excluir', __METHOD__, $arrObjMdPetIntegracaoDTO);

            //Regras de Negocio
            //$objInfraException = new InfraException();

            //$objInfraException->lancarValidacoes();

            $objMdPetIntegracaoBD = new MdPetIntegracaoBD($this->getObjInfraIBanco());
            for ($i = 0; $i < count($arrObjMdPetIntegracaoDTO); $i++) {
                $objMdPetIntegracaoRN = new MdPetIntegracaoRN();
                $objMdPetIntegracaoRN->excluirParametros($arrObjMdPetIntegracaoDTO[$i]);

                $objMdPetIntegracaoBD->excluir($arrObjMdPetIntegracaoDTO[$i]);
            }

            //Auditoria

        } catch (Exception $e) {
            throw new InfraException('Erro excluindo Integração.', $e);
        }
    }

    protected function cadastrarParametrosControlado(MdPetIntegracaoDTO $objMdPetIntegracaoDTO)
    {

        $arrParametrosEntradaMontado = $_POST['nomeFuncionalDadosEntrada'];
        $arrParametrosEntradaMontado['mesesExpiraCache'] = $_POST['txtPrazo'];
        $arrParametros = array(
            'paramentrosEntrada' => $arrParametrosEntradaMontado,
            'parametrosSaida' => $_POST['nomeFuncionalDadosSaida'] ?: []
        );

        $bolRest = $objMdPetIntegracaoDTO && self::isIntegracaoRest($objMdPetIntegracaoDTO);
        $arrHeadersFixos = [];
        if ($bolRest) {
            $objInfraException = new InfraException();
            self::validarMapeamentoRest($objMdPetIntegracaoDTO, $arrParametros['paramentrosEntrada'], $arrParametros['parametrosSaida'], !empty($_POST['chkSinNumero']), $objInfraException);
            $objInfraException->lancarValidacoes();
            $arrHeadersFixos = $objMdPetIntegracaoDTO->isSetArrHeaders() ? (array) $objMdPetIntegracaoDTO->getArrHeaders() : [];
        }

        if ($objMdPetIntegracaoDTO) {

            if ($_POST['txtPrazo'] == '') {
                $prazo = null;
            } else {
                $prazo = $_POST['txtPrazo'];
            }

            if ($_POST['chkSinCache'] == null) {
                $prazo = null;
            }


            $arrParametrosE = $_POST['selParametrosE'];

            foreach ($arrParametros['paramentrosEntrada'] as $chaveEntrada => $valorEntrada) {
                // O token do corpo é gravado abaixo, só no Body Authentication by Token, e nunca no SOAP
                if ($chaveEntrada === MdPetIntegFabricaRN::ENTRADA_TOKEN) {
                    continue;
                }
                $objMdPetIntegParametroDTO = new MdPetIntegParametroDTO();
                $objMdPetIntegParametroDTO->setNumIdMdPetIntegParametro(null);
                $objMdPetIntegParametroDTO->setNumIdMdPetIntegracao($objMdPetIntegracaoDTO->getNumIdMdPetIntegracao());
                $objMdPetIntegParametroDTO->setStrNome($chaveEntrada);
                $objMdPetIntegParametroDTO->setStrTpParametro('E');
                if ($chaveEntrada == 'mesesExpiraCache') {
                    $objMdPetIntegParametroDTO->setStrValorPadrao($prazo);
                    $objMdPetIntegParametroDTO->setStrNomeCampo('PrazoExpiracao');
                } else {
                    $objMdPetIntegParametroDTO->setStrNomeCampo($valorEntrada);
                }

                if ($bolRest) {
                    $objMdPetIntegParametroDTO->setStrStaLocalParametro(self::normalizarLocal($_POST['localDadosEntrada'][$chaveEntrada] ?? null));
                }

                $objMdPetIntegParametroRN = new MdPetIntegParametroRN();
                $objMdPetIntegParametroDTO = $objMdPetIntegParametroRN->cadastrar($objMdPetIntegParametroDTO);
            }
            foreach ($arrParametros['parametrosSaida'] as $chaveSaida => $valorSaida) {
                $objMdPetIntegParametroDTO = new MdPetIntegParametroDTO();
                $objMdPetIntegParametroDTO->setNumIdMdPetIntegParametro(null);
                $objMdPetIntegParametroDTO->setNumIdMdPetIntegracao($objMdPetIntegracaoDTO->getNumIdMdPetIntegracao());
                $objMdPetIntegParametroDTO->setStrNome($chaveSaida);
                $objMdPetIntegParametroDTO->setStrTpParametro('P');
                $objMdPetIntegParametroDTO->setStrValorPadrao(null);
                $objMdPetIntegParametroDTO->setStrNomeCampo($valorSaida);

                $objMdPetIntegParametroRN = new MdPetIntegParametroRN();
                $objMdPetIntegParametroDTO = $objMdPetIntegParametroRN->cadastrar($objMdPetIntegParametroDTO);
            }

            foreach ($arrHeadersFixos as $arrHeader) {
                $objMdPetIntegParametroDTO = new MdPetIntegParametroDTO();
                $objMdPetIntegParametroDTO->setNumIdMdPetIntegParametro(null);
                $objMdPetIntegParametroDTO->setNumIdMdPetIntegracao($objMdPetIntegracaoDTO->getNumIdMdPetIntegracao());
                $objMdPetIntegParametroDTO->setStrNome($arrHeader['nome']);
                $objMdPetIntegParametroDTO->setStrTpParametro(MdPetIntegFabricaRN::PARAMETRO_HEADER);
                $objMdPetIntegParametroDTO->setStrValorPadrao($arrHeader['valor']);
                $objMdPetIntegParametroDTO->setStrSinDadoConfidencial($arrHeader['restrito']);
                $objMdPetIntegParametroDTO->setStrNomeCampo(null);
                (new MdPetIntegParametroRN())->cadastrar($objMdPetIntegParametroDTO);
            }

            $strTokenOfuscado = $bolRest && $objMdPetIntegracaoDTO->isSetStrTokenAutenticacao() ? $objMdPetIntegracaoDTO->getStrTokenAutenticacao() : null;
            if ($strTokenOfuscado !== null) {
                $objMdPetIntegParametroDTO = new MdPetIntegParametroDTO();
                $objMdPetIntegParametroDTO->setNumIdMdPetIntegParametro(null);
                $objMdPetIntegParametroDTO->setNumIdMdPetIntegracao($objMdPetIntegracaoDTO->getNumIdMdPetIntegracao());
                $objMdPetIntegParametroDTO->setStrNome(MdPetIntegFabricaRN::ENTRADA_TOKEN);
                $objMdPetIntegParametroDTO->setStrTpParametro(MdPetIntegFabricaRN::PARAMETRO_ENTRADA);
                $objMdPetIntegParametroDTO->setStrNomeCampo($objMdPetIntegracaoDTO->getStrCampoTokenAutenticacao());
                $objMdPetIntegParametroDTO->setStrValorPadrao($strTokenOfuscado);
                $objMdPetIntegParametroDTO->setStrSinDadoConfidencial('S');
                $objMdPetIntegParametroDTO->setStrStaLocalParametro(MdPetIntegRestRN::LOCAL_CORPO);
                (new MdPetIntegParametroRN())->cadastrar($objMdPetIntegParametroDTO);
            }
        }
    }

    protected function excluirParametrosControlado(MdPetIntegracaoDTO $objMdPetIntegracaoDTO)
    {
        if ($objMdPetIntegracaoDTO) {
            $objMdPetIntegParametroDTO = new MdPetIntegParametroDTO();
            $objMdPetIntegParametroDTO->setNumIdMdPetIntegracao($objMdPetIntegracaoDTO->getNumIdMdPetIntegracao());
            $objMdPetIntegParametroDTO->retTodos();

            $objMdPetIntegParametroRN = new MdPetIntegParametroRN();
            $objMdPetIntegParametroDTO = $objMdPetIntegParametroRN->listar($objMdPetIntegParametroDTO);
            $objMdPetIntegParametroRN->excluir($objMdPetIntegParametroDTO);
        }
    }

    public static function consultarContatoCpf($dados)
    {
        $objMdPetContatoRN = new MdPetContatoRN();
        $objMdPetVinculoUsuExtRN = new MdPetVinculoUsuExtRN();

        $cpf = InfraUtil::retirarFormatacao($dados['nuCpf']);
        $idVinculo = InfraUtil::retirarFormatacao($dados['idVinculo']);

        $xml = '<dados-pf>';
        if (!InfraUtil::validarCpf($cpf)) {
            $xml .= "<success>false</success>\n";
            $xml .= "<msg>CPF informado é inválido.</msg>\n";
            $xml .= '</dados-pf>';
            return $xml;
        }

        $idTipoContato = $objMdPetContatoRN->getIdTipoContatoUsExt();

        $contatoRN = new ContatoRN();
        $contatoDTO = new ContatoDTO();
        $contatoDTO->retNumIdTipoContato();
        $contatoDTO->retStrNome();
        $contatoDTO->retNumIdContato();
        $contatoDTO->setDblCpf($cpf);

        if (!empty($idTipoContato)) {
            $contatoDTO->setNumIdTipoContato($idTipoContato);
        }

        $arrContatoDTO = $contatoRN->consultarRN0324($contatoDTO);

        if (is_null($arrContatoDTO)) {
            $xml .= "<success>false</success>\n";
            $xml .= "<msg>CPF não encontrado.</msg>\n";
            $xml .= '</dados-pf>';
            return $xml;
        }

        $userIsProcurador = $objMdPetVinculoUsuExtRN->validarContatoProcurador(array($idVinculo, $arrContatoDTO->getNumIdContato()));

        if ($userIsProcurador) {
            $xml .= "<success>false</success>\n";
            $xml .= "<msg>O CPF informado já se encontra vinculado ao CNPJ. Para incluí-lo como Responsável Legal a Procuração Especial deve ser revogada/ renunciada. </msg>\n";
            $xml .= '</dados-pf>';
            return $xml;
        }

        $xml .= '<idContatoNovo>' . $arrContatoDTO->getNumIdContato() . '</idContatoNovo>';
        $xml .= '<txtNomeNovo>' . $arrContatoDTO->getStrNome() . '</txtNomeNovo>';
        $xml .= '</dados-pf>';

        return $xml;

    }
    
    private function retornaCpfUsuarioLogado($idUsuarioLogado){

        if(!empty($idUsuarioLogado)){
            $objUsuarioDTO = new UsuarioDTO();
            $objUsuarioDTO->retDblCpfContato();
            $objUsuarioDTO->retNumIdContato();
            $objUsuarioDTO->setNumIdUsuario($idUsuarioLogado);
            $arrObjUsuarioDTO = (new UsuarioRN())->consultarRN0489($objUsuarioDTO);
            
            return str_pad($arrObjUsuarioDTO->getDblCpfContato(), '11', '0', STR_PAD_LEFT);

        }else{

            return null;

        }
	    
    }

    private function consultarIntegracaoPorIdMdPetIntegFuncionalid($IdMdPetIntegFuncionalid)
    {
        $mdPetIntegracaoRN = new MdPetIntegracaoRN();
        $mdPetIntegracaoDTO = new MdPetIntegracaoDTO();
        $mdPetIntegracaoDTO->retNumIdMdPetIntegracao();
        $mdPetIntegracaoDTO->retStrEnderecoWsdl();
        $mdPetIntegracaoDTO->retStrOperacaoWsdl();
        $mdPetIntegracaoDTO->retDblNuVersao();
        $mdPetIntegracaoDTO->retStrCodReceitaSuspAuto();
        $mdPetIntegracaoDTO->retStrTpClienteWs();
        $mdPetIntegracaoDTO->retStrEnderecoExecucao();
        $mdPetIntegracaoDTO->retStrMetodoHttp();
        $mdPetIntegracaoDTO->retStrCaminhoOperacao();
        $mdPetIntegracaoDTO->setNumIdMdPetIntegFuncionalid($IdMdPetIntegFuncionalid);
        $mdPetIntegracaoDTO->setStrSinAtivo('S');

        return $mdPetIntegracaoRN->consultar($mdPetIntegracaoDTO);
    }
}
