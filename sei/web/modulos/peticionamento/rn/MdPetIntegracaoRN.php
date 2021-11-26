<?
/**
 * TRIBUNAL REGIONAL FEDERAL DA 4� REGI�O
 *
 * 25/01/2018 - criado por Usu�rio
 *
 * Vers�o do Gerador de C�digo: 1.41.0
 */

require_once dirname(__FILE__) . '/../../../SEI.php';

class MdPetIntegracaoRN extends InfraRN
{

    public function __construct()
    {
        parent::__construct();
    }

    private function _getIdUf($consulta)
    {
        $siglaUf = $consulta['PessoaJuridica']['endereco']['uf'] ? $consulta['PessoaJuridica']['endereco']['uf'] : null;
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

    private function _getDadosCidade($consulta)
    {
        $idCidade = '';
        $nomeCidade = '';
        $idUF = '';
        $siglaUF = '';
        $codigoIbge = $consulta['PessoaJuridica']['endereco']['codigoMunicipio'] ? $consulta['PessoaJuridica']['endereco']['codigoMunicipio'] : null;
        $arrRetorno = array();

        if (!is_null($codigoIbge)) {
            $objCidadeDTO = new CidadeDTO();
            $objCidadeDTO->setNumCodigoIbge($codigoIbge);
            $objCidadeDTO->retNumIdCidade();
            $objCidadeDTO->retStrNome();
            $objCidadeDTO->retNumIdUf();
            $objCidadeDTO->retStrSiglaUf();

            $objCidadeDTO->setNumMaxRegistrosRetorno(1);
            $objCidadeRN = new CidadeRN();
            $objCidadeDTO = $objCidadeRN->consultarRN0409($objCidadeDTO);
            $idCidade = !is_null($objCidadeDTO) ? $objCidadeDTO->getNumIdCidade() : '';
            $nomeCidade = !is_null($objCidadeDTO) ? $objCidadeDTO->getStrNome() : '';
            $idUF = !is_null($objCidadeDTO) ? $objCidadeDTO->getNumIdUf() : '';
            $siglaUF = !is_null($objCidadeDTO) ? $objCidadeDTO->getStrSiglaUf() : '';
        }

        $arrRetorno['idCidade'] = $idCidade;
        $arrRetorno['nomeCidade'] = $nomeCidade;
        $arrRetorno['idUF'] = $idUF;
        $arrRetorno['siglaUF'] = $siglaUF;

        return $arrRetorno;
    }

    protected function consultarReceitaWsResponsavelLegalConectado($dados)
    {
        $xml = '<dados-pj>';
        $headers = apache_request_headers();
        $captcha = $headers['captcha'];
        $dadosCaptcha = hash('SHA512', $dados['txtCaptcha']);
        $cnpj = InfraUtil::retirarFormatacao($dados['txtNumeroCnpj']);
        $dados['idUsuarioLogado'] = SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno();

        if (!InfraUtil::validarCnpj($cnpj)) {
            $xml .= "<success>false</success>\n";
            $xml .= "<msg>CNPJ informado � inv�lido.</msg>\n";
            $xml .= '</dados-pj>';
            return $xml;
        }

        if ($captcha != $dadosCaptcha) {
            $xml .= "<success>false</success>";
            $xml .= "<msg>C�digo de confirma��o inv�lido.</msg>";
            $xml .= '</dados-pj>';
            return $xml;
        }

        $mdPetIntegracaoRN = new MdPetIntegracaoRN();
        $mdPetIntegracaoDTO = new MdPetIntegracaoDTO();
        $mdPetIntegracaoDTO->retNumIdMdPetIntegracao();
        $mdPetIntegracaoDTO->retStrEnderecoWsdl();
        $mdPetIntegracaoDTO->retStrOperacaoWsdl();
        $mdPetIntegracaoDTO->setNumIdMdPetIntegFuncionalid(MdPetIntegFuncionalidRN::$ID_FUNCIONALIDADE_CNPJ_RECEITA_FEDERAL);
        $mdPetIntegracaoDTO->setStrSinAtivo('S');

        $objMdPetIntegracao = $mdPetIntegracaoRN->consultar($mdPetIntegracaoDTO);

        $strUrlWebservice = $objMdPetIntegracao->getStrEnderecoWsdl();
        $strMetodoWebservice = $objMdPetIntegracao->getStrOperacaoWsdl();

        $objMdPetSoapClienteRN = new MdPetSoapClienteRN($strUrlWebservice, 'wsdl');

        $objUsuarioRN = new UsuarioRN();
        $objUsuarioDTO = new UsuarioDTO();
        $objUsuarioDTO->retDblCpfContato();
        $objUsuarioDTO->retNumIdContato();
        $objUsuarioDTO->setNumIdUsuario($dados['idUsuarioLogado']);

        $arrObjUsuarioDTO = $objUsuarioRN->consultarRN0489($objUsuarioDTO);
        $cpfUsuarioLogado = str_pad($arrObjUsuarioDTO->getDblCpfContato(), '11', '0', STR_PAD_LEFT);


        //Recuperando meses - alterado
        $objMdPetIntegParametroDTO = new MdPetIntegParametroDTO();
        $objMdPetIntegParametroDTO->retStrValorPadrao();
        $objMdPetIntegParametroDTO->retStrTpParametro();
        $objMdPetIntegParametroDTO->retStrNome();
        $objMdPetIntegParametroDTO->retStrNomeCampo();
        $objMdPetIntegParametroDTO->setNumIdMdPetIntegracao($objMdPetIntegracao->getNumIdMdPetIntegracao());
        $objMdPetIntegParametroRN = new MdPetIntegParametroRN();
        $arrObjMdPetIntegParametroRN = $objMdPetIntegParametroRN->listar($objMdPetIntegParametroDTO);

        if (count($arrObjMdPetIntegParametroRN)) {
            $mes = 0;
            $chaveMes = '';
            foreach ($arrObjMdPetIntegParametroRN as $itemParam) {
                if ($itemParam->getStrTpParametro() == 'E' && $itemParam->getStrNome() == 'mesesExpiraCache') {
                    $mes = (int)$itemParam->getStrValorPadrao();
                    $chaveMes = $itemParam->getStrNome();
                }

                if ($itemParam->getStrTpParametro() == 'E' && $itemParam->getStrNome() == 'identificacaoOrigem' && $itemParam->getStrNomeCampo() == 'origem') {
                    //Verifica��o da Origem
                    $objInfraParametro = new InfraParametro(BancoSEI::getInstance());
                    $idUsuario = $objInfraParametro->getValor(MdPetContatoRN::$STR_INFRA_PARAMETRO_SIGLA_CONTATO, false);

                    if ($idUsuario != '' && !is_null($idUsuario)) {
                        $objUsuarioRN = new UsuarioRN();
                        $objUsuarioDTO = new UsuarioDTO();

                        $objUsuarioDTO->setNumIdUsuario($idUsuario);
                        $objUsuarioDTO->retStrSigla();

                        $objUsuarioDTO = $objUsuarioRN->consultarRN0489($objUsuarioDTO);

                        if ($objUsuarioDTO) {
                            $siglaContato = $objUsuarioDTO->getStrSigla();
                        }
                    }
                }
            }

            if ($siglaContato) {
                $parametro = [
                    $strMetodoWebservice => [
                        'cnpj' => $cnpj,
                        'cpfUsuario' => $cpfUsuarioLogado,
                        $chaveMes => $mes,
                        'origem' => $siglaContato
                    ]
                ];
            } else {
                $parametro = [
                    $strMetodoWebservice => [
                        'cnpj' => $cnpj,
                        'cpfUsuario' => $cpfUsuarioLogado,
                        $chaveMes => $mes
                    ]
                ];
            }

        } else {

            $parametro = [
                $strMetodoWebservice => [
                    'cnpj' => $cnpj,
                    'cpfUsuario' => $cpfUsuarioLogado
                ]
            ];

        }
        $consulta = $objMdPetSoapClienteRN->consultarWsdl($strMetodoWebservice, $parametro);

        if ($consulta['PessoaJuridica']['situacaoCadastral']['codigo'] == '03') {
            $xml .= "<success>false</success>\n";
            $xml .= "<msg>Empresa Suspensa junto a Receita Federal, n�o podendo ser vinculada a Pessoa Jur�dica.</msg>\n";
            $xml .= '</dados-pj>';
            return $xml;
        }


        $cpfResponsavelLegalReceita = $consulta['PessoaJuridica']['responsavel']['cpf'];

        if (empty($cpfResponsavelLegalReceita)) {
            $xml .= "<success>false</success>\n";
            $xml .= '<msg>' . $consulta['faultstring'] . '</msg>';
            $xml .= '</dados-pj>';
            return $xml;
        }

        if ($cpfUsuarioLogado != $cpfResponsavelLegalReceita) {
            $xml .= "<success>false</success>\n";
            $xml .= "<msg>Em consulta junto � base de dados da Receita Federal do Brasil (RFB), verificou-se que o CPF do Usu�rio Externo logado n�o consta como Respons�vel Legal pelo CNPJ informado (" . $dados['txtNumeroCnpj'] . "), o que impede a formaliza��o da presente vincula��o.\n \n";
            $xml .= "Entre em contato com a RFB para verificar sua situa��o e regularizar eventuais pend�ncias.</msg>\n";
            $xml .= '</dados-pj>';
            return $xml;
        } else {

            $VinculoPJ = MdPetVinculoINT::validarExistenciaVinculoCnpj($dados);

            $strXml = simplexml_load_string($VinculoPJ);

            if ($VinculoPJ != "<dados-pj></dados-pj>") {
                if (!isset($strXml->idVinculo)) {
                    return $VinculoPJ;
                }
                $xml .= '<hdnIdVinculo>' . $strXml->idVinculo . '</hdnIdVinculo>';
            }
        }

        $mdPetContatoRN = new MdPetContatoRN();
        $arrContatoDTO = $mdPetContatoRN->getContatoInclusoModPet($cnpj);

        $slTipoInteressado = '';
        if (!is_null($arrContatoDTO)) {
            $slTipoInteressado = $arrContatoDTO->getNumIdTipoContato();
        }

        $dadosCidade = $this->_getDadosCidade($consulta);

        if ($slTipoInteressado == '') {
            $xml .= '<slTipoInteressado>0</slTipoInteressado>';
        } else {
            $xml .= '<slTipoInteressado>' . $slTipoInteressado . '</slTipoInteressado>';
        }


        $mdPetIntegParametroRN = new MdPetIntegParametroRN();
        $objMdPetIntegParametroDTO = new MdPetIntegParametroDTO();
        $objMdPetIntegracaoDTO = new MdPetIntegracaoDTO();
        $objMdPetIntegracaoDTO->retTodos();
        $objMdPetIntegracaoDTO->setStrNome('ConsultaDadosReceitaCNPJ');
        $arrObjMdPetIntegracaoDTO = $this->consultar($objMdPetIntegracaoDTO);

        $objMdPetIntegParametroDTO->retTodos();
        $objMdPetIntegParametroDTO->setStrTpParametro('P');
        $objMdPetIntegParametroDTO->setNumIdMdPetIntegracao($arrObjMdPetIntegracaoDTO->getNumIdMdPetIntegracao());
        $arrObjMdPetIntegParametroDTO = $mdPetIntegParametroRN->listar($objMdPetIntegParametroDTO);

        if ($arrObjMdPetIntegParametroDTO) {
            $txtLogradouro = '';
            foreach ($arrObjMdPetIntegParametroDTO as $itemParametro) {

                $chave = explode(" - ", $itemParametro->getStrNomeCampo());
                $valor = '';
                switch (count($chave)) {
                    case 1:
                        $valor = htmlspecialchars(trim($consulta[ucfirst($chave[0])]));
                        break;
                    case 3:
                        $valor = htmlspecialchars(trim($consulta[ucfirst($chave[0])][$chave[1]][$chave[2]]));
                        break;
                    case 4:
                        $valor = htmlspecialchars(trim($consulta[ucfirst($chave[0])][$chave[1]][$chave[2]][$chave[3]]));
                        break;
                    default:
                        $valor = htmlspecialchars(trim($consulta[ucfirst($chave[0])][$chave[1]]));
                }
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
//    $xml .= '<txtNumeroEndereco>'. $consulta['PessoaJuridica']['endereco']['numero'].'</txtNumeroEndereco>';
        } else {

            $xml .= '<txtNomeFantasia>' . htmlspecialchars($consulta['PessoaJuridica']['nomeFantasia']) . '</txtNomeFantasia>';
            $xml .= '<txtNumeroCpfResponsavel>' . InfraUtil::formatarCpf($cpfResponsavelLegalReceita) . '</txtNumeroCpfResponsavel>';
            $xml .= '<txtRazaoSocial>' . htmlspecialchars($consulta['PessoaJuridica']['nomeEmpresarial']) . '</txtRazaoSocial>';
            $xml .= '<txtNomeResponsavelLegal>' . htmlspecialchars($consulta['PessoaJuridica']['responsavel']['nome']) . '</txtNomeResponsavelLegal>';
            $xml .= '<slUf>' . $dadosCidade['idUF'] . '</slUf>';
            $xml .= '<selCidade>' . htmlspecialchars($dadosCidade['idCidade']) . '</selCidade>';
            $xml .= '<txtNumeroCEP>' . MdPetDataUtils::formatCep($consulta['PessoaJuridica']['endereco']['cep']) . '</txtNumeroCEP>';
            $xml .= '<txtLogradouro>' . htmlspecialchars($consulta['PessoaJuridica']['endereco']['logradouro']) . ', ' . $consulta['PessoaJuridica']['endereco']['numero'] . ' ' . htmlspecialchars($consulta['PessoaJuridica']['endereco']['complemento']) . '</txtLogradouro>';
//    $xml .= '<txtNumeroEndereco>'. $consulta['PessoaJuridica']['endereco']['numero'].'</txtNumeroEndereco>';
            $xml .= '<txtComplementoEndereco>' . htmlspecialchars($consulta['PessoaJuridica']['endereco']['complemento']) . '</txtComplementoEndereco>';
            $xml .= '<txtBairro>' . htmlspecialchars($consulta['PessoaJuridica']['endereco']['bairro']) . '</txtBairro>';
            $xml .= '<nomeCidade>' . htmlspecialchars($dadosCidade['nomeCidade']) . '</nomeCidade>';
        }
        $xml .= '</dados-pj>';

        return $xml;

    }


    protected function inicializarObjInfraIBanco()
    {
        return BancoSEI::getInstance();
    }

    private function validarNumIdMdPetIntegFuncionalid(MdPetIntegracaoDTO $objMdPetIntegracaoDTO, InfraException $objInfraException)
    {
        if (InfraString::isBolVazia($objMdPetIntegracaoDTO->getNumIdMdPetIntegFuncionalid())) {
            $objInfraException->adicionarValidacao('Funcionalidade n�o informada.');
        }
    }

    private function validarStrNome(MdPetIntegracaoDTO $objMdPetIntegracaoDTO, InfraException $objInfraException)
    {
        if (InfraString::isBolVazia($objMdPetIntegracaoDTO->getStrNome())) {
            $objInfraException->adicionarValidacao('Nome n�o informado.');
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
            $objInfraException->adicionarValidacao('Endere�o do Webservice n�o informado.');
        } else {
            $objMdPetIntegracaoDTO->setStrEnderecoWsdl(trim($objMdPetIntegracaoDTO->getStrEnderecoWsdl()));

            if (strlen($objMdPetIntegracaoDTO->getStrEnderecoWsdl()) > 100) {
                $objInfraException->adicionarValidacao('Endere�o do Webservice possui tamanho superior a 100 caracteres.');
            }
        }
    }

    private function validarStrOperacaoWsdl(MdPetIntegracaoDTO $objMdPetIntegracaoDTO, InfraException $objInfraException)
    {
        if (InfraString::isBolVazia($objMdPetIntegracaoDTO->getStrOperacaoWsdl())) {
            $objMdPetIntegracaoDTO->setStrOperacaoWsdl(null);
        } else {
            $objMdPetIntegracaoDTO->setStrOperacaoWsdl(trim($objMdPetIntegracaoDTO->getStrOperacaoWsdl()));

            if (strlen($objMdPetIntegracaoDTO->getStrOperacaoWsdl()) > 50) {
                $objInfraException->adicionarValidacao('Opera��o possui tamanho superior a 50 caracteres.');
            }
        }
    }

    private function validarStrSinCache(MdPetIntegracaoDTO $objMdPetIntegracaoDTO, InfraException $objInfraException)
    {
        if (InfraString::isBolVazia($objMdPetIntegracaoDTO->getStrSinCache())) {
            $objInfraException->adicionarValidacao('Sinalizador de Marque caso seu Webservice tenha controle de expira��o de cache n�o informado.');
        } else {
            if (!InfraUtil::isBolSinalizadorValido($objMdPetIntegracaoDTO->getStrSinCache())) {
                $objInfraException->adicionarValidacao('Sinalizador de Marque caso seu Webservice tenha controle de expira��o de cache inv�lid.');
            }
        }
    }

    private function validarStrSinTpLogradouro(MdPetIntegracaoDTO $objMdPetIntegracaoDTO, InfraException $objInfraException)
    {
        if (InfraString::isBolVazia($objMdPetIntegracaoDTO->getStrSinTpLogradouro())) {
            $objInfraException->adicionarValidacao('Sinalizador de Marque caso seu Webservice tenha o Tipo do Logradouro n�o informado.');
        } else {
            if (!InfraUtil::isBolSinalizadorValido($objMdPetIntegracaoDTO->getStrSinTpLogradouro())) {
                $objInfraException->adicionarValidacao('Sinalizador de Marque caso seu Webservice tenha o Tipo do Logradouro inv�lid.');
            }
        }
    }

    private function validarStrSinNuLogradouro(MdPetIntegracaoDTO $objMdPetIntegracaoDTO, InfraException $objInfraException)
    {
        if (InfraString::isBolVazia($objMdPetIntegracaoDTO->getStrSinNuLogradouro())) {
            $objInfraException->adicionarValidacao('Sinalizador de Marque caso seu Webservice tenha o N�mero do Logradouro n�o informado.');
        } else {
            if (!InfraUtil::isBolSinalizadorValido($objMdPetIntegracaoDTO->getStrSinNuLogradouro())) {
                $objInfraException->adicionarValidacao('Sinalizador de Marque caso seu Webservice tenha o N�mero do Logradouro inv�lid.');
            }
        }
    }

    private function validarStrSinCompLogradouro(MdPetIntegracaoDTO $objMdPetIntegracaoDTO, InfraException $objInfraException)
    {
        if (InfraString::isBolVazia($objMdPetIntegracaoDTO->getStrSinCompLogradouro())) {
            $objInfraException->adicionarValidacao('Sinalizador de Marque caso seu Webservice tenha o Complemento do Logradouro n�o informado.');
        } else {
            if (!InfraUtil::isBolSinalizadorValido($objMdPetIntegracaoDTO->getStrSinCompLogradouro())) {
                $objInfraException->adicionarValidacao('Sinalizador de Marque caso seu Webservice tenha o Complemento do Logradouro de cache inv�lid.');
            }
        }
    }

    private function validarStrSinAtivo(MdPetIntegracaoDTO $objMdPetIntegracaoDTO, InfraException $objInfraException)
    {
        if (InfraString::isBolVazia($objMdPetIntegracaoDTO->getStrSinAtivo())) {
            $objInfraException->adicionarValidacao('Sinalizador de Exclus�o L�gica n�o informado.');
        } else {
            if (!InfraUtil::isBolSinalizadorValido($objMdPetIntegracaoDTO->getStrSinAtivo())) {
                $objInfraException->adicionarValidacao('Sinalizador de Exclus�o L�gica inv�lido.');
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
                $this->validarStrEnderecoWsdl($objMdPetIntegracaoDTO, $objInfraException);
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
            throw new InfraException('Erro cadastrando Integra��o.', $e);
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
                    $this->validarStrEnderecoWsdl($objMdPetIntegracaoDTO, $objInfraException);
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
            throw new InfraException('Erro alterando Integra��o.', $e);
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
            throw new InfraException('Erro excluindo Integra��o.', $e);
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
            throw new InfraException('Erro consultando Integra��o.', $e);
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
            throw new InfraException('Erro listando Integra��es.', $e);
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
            throw new InfraException('Erro contando Integra��es.', $e);
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
            throw new InfraException('Erro desativando Integra��o.', $e);
        }
    }

    protected function reativarControlado($arrObjMdPetIntegracaoDTO)
    {
        try {

            //Valida Permissao
            SessaoSEI::getInstance()->validarAuditarPermissao('md_pet_integracao_reativar', __METHOD__, $arrObjMdPetIntegracaoDTO);

            $objMdPetIntegracaoBD = new MdPetIntegracaoBD($this->getObjInfraIBanco());
            for ($i = 0; $i < count($arrObjMdPetIntegracaoDTO); $i++) {

                //Funcionalidade utilizada em outra integra��o
                $objMdPetIntegFuncionalidRN = new MdPetIntegFuncionalidRN();
                $arrIdMdPetIntegFuncionalidUtilizado = $objMdPetIntegFuncionalidRN->verificarMdPetIntegFuncionalidUtilizado($arrObjMdPetIntegracaoDTO[$i]->getNumIdMdPetIntegracao(), $arrObjMdPetIntegracaoDTO[$i]->getNumIdMdPetIntegFuncionalid());

                if (count($arrIdMdPetIntegFuncionalidUtilizado) > 0) {
                    //Regras de Negocio
                    $objInfraException = new InfraException();

                    $objInfraException->adicionarValidacao('Funcionalidade sendo utilizada por outra Integra��o.');

                    $objInfraException->lancarValidacoes();
                }

                $objMdPetIntegracaoBD->reativar($arrObjMdPetIntegracaoDTO[$i]);
            }

            //Auditoria

        } catch (Exception $e) {
            throw new InfraException('Erro reativando Integra��o.', $e);
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
            throw new InfraException('Erro bloqueando Integra��o.', $e);
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
                $this->validarStrEnderecoWsdl($objMdPetIntegracaoDTO, $objInfraException);
                $this->validarStrOperacaoWsdl($objMdPetIntegracaoDTO, $objInfraException);
                $this->validarStrSinCache($objMdPetIntegracaoDTO, $objInfraException);
            }
            $this->validarStrSinAtivo($objMdPetIntegracaoDTO, $objInfraException);

            //Funcionalidade utilizada em outra integra��o
            $objMdPetIntegFuncionalidRN = new MdPetIntegFuncionalidRN();
            $arrIdMdPetIntegFuncionalidUtilizado = $objMdPetIntegFuncionalidRN->verificarMdPetIntegFuncionalidUtilizado(null, $objMdPetIntegracaoDTO->getNumIdMdPetIntegFuncionalid());

            if (count($arrIdMdPetIntegFuncionalidUtilizado) > 0) {
                $objInfraException->adicionarValidacao('Funcionalidade sendo utilizada por outra Integra��o.');
            }

            $objInfraException->lancarValidacoes();


            $objMdPetIntegracaoRN = new MdPetIntegracaoRN();
            $objMdPetIntegracaoDTO = $objMdPetIntegracaoRN->cadastrar($objMdPetIntegracaoDTO);

            $objMdPetIntegracaoRN->cadastrarParametros($objMdPetIntegracaoDTO);

            return $objMdPetIntegracaoDTO;

        } catch (Exception $e) {
            throw new InfraException('Erro cadastrando Integra��o.', $e);
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
                    $this->validarStrEnderecoWsdl($objMdPetIntegracaoDTO, $objInfraException);
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
                //Funcionalidade utilizada em outra integra��o
                $objMdPetIntegFuncionalidRN = new MdPetIntegFuncionalidRN();
                $arrIdMdPetIntegFuncionalidUtilizado = $objMdPetIntegFuncionalidRN->verificarMdPetIntegFuncionalidUtilizado($objMdPetIntegracaoDTO->getNumIdMdPetIntegracao(), $objMdPetIntegracaoDTO->getNumIdMdPetIntegFuncionalid());

                if (count($arrIdMdPetIntegFuncionalidUtilizado) > 0) {
                    $objInfraException->adicionarValidacao('Funcionalidade sendo utilizada por outra Integra��o.');
                }

                $objInfraException->lancarValidacoes();
            }

            $objMdPetIntegracaoRN = new MdPetIntegracaoRN();
            $objMdPetIntegracaoRN->alterar($objMdPetIntegracaoDTO);

            $objMdPetIntegracaoRN->excluirParametros($objMdPetIntegracaoDTO);

            if ($objMdPetIntegracaoDTO->getStrStaUtilizarWs() == 'S') {

                $objMdPetIntegracaoRN->cadastrarParametros($objMdPetIntegracaoDTO);
            }
            //Auditoria

        } catch (Exception $e) {
            throw new InfraException('Erro alterando Integra��o.', $e);
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
            throw new InfraException('Erro excluindo Integra��o.', $e);
        }
    }

    protected function cadastrarParametrosControlado(MdPetIntegracaoDTO $objMdPetIntegracaoDTO)
    {

        $arrParametrosEntradaMontado = $_POST['nomeFuncionalDadosEntrada'];
        $arrParametrosEntradaMontado['mesesExpiraCache'] = $_POST['txtPrazo'];
        $arrParametros = array(
            'paramentrosEntrada' => $arrParametrosEntradaMontado,
            'parametrosSaida' => $_POST['nomeFuncionalDadosSaida']
        );

        if (count($objMdPetIntegracaoDTO) == 1) {

            if ($_POST['txtPrazo'] == '') {
                $prazo = null;
            } else {
                $prazo = $_POST['txtPrazo'];
            }

            if ($_POST['chkSinCache'] == null) {
                $prazo = null;
            }


            $arrParametrosE = $_POST['selParametrosE'];

            if ($_POST['chkSinCache'] != null) {

//         if (count($arrParametrosE)){
//
//               $objMdPetIntegParametroDTO = new MdPetIntegParametroDTO();
//               $objMdPetIntegParametroDTO->setNumIdMdPetIntegParametro(null);
//               $objMdPetIntegParametroDTO->setNumIdMdPetIntegracao($objMdPetIntegracaoDTO->getNumIdMdPetIntegracao());
//               $objMdPetIntegParametroDTO->setStrNome($_POST['selCachePrazoExpiracao']);
//               $objMdPetIntegParametroDTO->setStrTpParametro('E');
//               $objMdPetIntegParametroDTO->setStrValorPadrao($prazo);
//               $objMdPetIntegParametroDTO->unSetStrNomeCampo();
//
//               $objMdPetIntegParametroRN = new MdPetIntegParametroRN();
//               $objMdPetIntegParametroDTO = $objMdPetIntegParametroRN->cadastrar($objMdPetIntegParametroDTO);
//
//         }


//        if ($_POST['selCachePrazoExpiracao']!=''){
//            $objMdPetIntegParametroDTO = new MdPetIntegParametroDTO();
//            $objMdPetIntegParametroDTO->setNumIdMdPetIntegParametro(null);
//            $objMdPetIntegParametroDTO->setNumIdMdPetIntegracao($objMdPetIntegracaoDTO->getNumIdMdPetIntegracao());
//            $objMdPetIntegParametroDTO->setStrNome($_POST['selCachePrazoExpiracao']);
//            $objMdPetIntegParametroDTO->setStrTpParametro('P');
//            $objMdPetIntegParametroDTO->setStrValorPadrao($prazo);
//            $objMdPetIntegParametroDTO->setStrNomeCampo('PrazoExpiracao');
//
//            $objMdPetIntegParametroRN = new MdPetIntegParametroRN();
//            $objMdPetIntegParametroDTO = $objMdPetIntegParametroRN->cadastrar($objMdPetIntegParametroDTO);
//        }

                foreach ($arrParametros['paramentrosEntrada'] as $chaveEntrada => $valorEntrada) {
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
            }
        }
    }

    protected function excluirParametrosControlado(MdPetIntegracaoDTO $objMdPetIntegracaoDTO)
    {
        if (count($objMdPetIntegracaoDTO) == 1) {
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
            $xml .= "<msg>CPF informado � inv�lido.</msg>\n";
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
            $xml .= "<msg>CPF n�o encontrado.</msg>\n";
            $xml .= '</dados-pf>';
            return $xml;
        }

        $userIsProcurador = $objMdPetVinculoUsuExtRN->validarContatoProcurador(array($idVinculo, $arrContatoDTO->getNumIdContato()));

        if ($userIsProcurador) {
            $xml .= "<success>false</success>\n";
            $xml .= "<msg>O CPF informado j� se encontra vinculado ao CNPJ. Para inclu�-lo como Respons�vel Legal a Procura��o Especial deve ser revogada/ renunciada. </msg>\n";
            $xml .= '</dados-pf>';
            return $xml;
        }

        $xml .= '<idContatoNovo>' . $arrContatoDTO->getNumIdContato() . '</idContatoNovo>';
        $xml .= '<txtNomeNovo>' . $arrContatoDTO->getStrNome() . '</txtNomeNovo>';
        $xml .= '</dados-pf>';

        return $xml;

    }
}
