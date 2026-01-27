<?
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

    public function __construct()
    {
        parent::__construct();
    }

    private function _getIdUf($consulta, $arrSaidaWS)
    {
        $integra = new MdPetIntegracaoINT();
        $siglaUf = $integra::acessarDadoPorChave($consulta, $arrSaidaWS['codIbgeMunicipio']) ? $integra::acessarDadoPorChave($consulta, $arrSaidaWS['codIbgeMunicipio']) : null;
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
        $integra = new MdPetIntegracaoINT();
        $idCidade = '';
        $nomeCidade = '';
        $idUF = '';
        $siglaUF = '';
        $codigoIbge = $integra::acessarDadoPorChave($consulta, $arrSaidaWS['codIbgeMunicipio']) ? $integra::acessarDadoPorChave($consulta, $arrSaidaWS['codIbgeMunicipio']) : null;
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
        $cnpj = InfraUtil::retirarFormatacao($dados['txtNumeroCnpj']);
        $dados['idUsuarioLogado'] = SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno();
        $integra = new MdPetIntegracaoINT();

        if (!InfraUtil::validarCnpj($cnpj)) {
            $xml .= "<success>false</success>\n";
            $xml .= "<msg>CNPJ informado é inválido.</msg>\n";
            $xml .= '</dados-pj>';
            return $xml;
        }
	
	    $objMdPetIntegracao     = $this->consultarIntegracaoPorIdMdPetIntegFuncionalid(MdPetIntegFuncionalidRN::$ID_FUNCIONALIDADE_CNPJ_RECEITA_FEDERAL);
        $strUrlWebservice       = $objMdPetIntegracao->getStrEnderecoWsdl();
        $strMetodoWebservice    = $objMdPetIntegracao->getStrOperacaoWsdl();
        $cpfUsuarioLogado       = $this->retornaCpfUsuarioLogado($dados['idUsuarioLogado']);
        $arrParamsWebservice    = ['cnpj' => $cnpj];
        $arrSaidaWS             = [];

        $objMdPetIntegParametroDTO = new MdPetIntegParametroDTO();
        $objMdPetIntegParametroDTO->retStrValorPadrao();
        $objMdPetIntegParametroDTO->retStrTpParametro();
        $objMdPetIntegParametroDTO->retStrNome();
        $objMdPetIntegParametroDTO->retStrNomeCampo();
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
	    $objMdPetSoapClienteRN  = new MdPetSoapClienteRN($strUrlWebservice , ['soap_version' => $objMdPetIntegracao->getDblNuVersao()]);
	    $consulta               = $objMdPetSoapClienteRN->execOperacao($strMetodoWebservice, $parametro);

	    if (!empty($objMdPetIntegracao->getStrCodReceitaSuspAuto()) && in_array(intval($integra::acessarDadoPorChave($consulta, $arrSaidaWS['codSituacaoCadastral'])), explode(',', $objMdPetIntegracao->getStrCodReceitaSuspAuto()))) {
            $xml .= "<success>false</success>\n";
            $xml .= "<msg>O cadastro do CNPJ indicado está suspenso na Receita Federal. Dessa forma, não pode ser efetivada a vinculação do Responsável Legal à Pessoa Jurídica.</msg>\n";
            $xml .= '</dados-pj>';
            return $xml;
        }

        $cpfResponsavelLegalReceita = $integra::acessarDadoPorChave($consulta, $arrSaidaWS['cpfRespLegal']);

        if (empty($cpfResponsavelLegalReceita)) {
            $xml .= "<success>false</success>\n";
            $xml .= '<msg>Erro na consulta: ' . $consulta['faultstring'] . '</msg>';
            $xml .= '</dados-pj>';
            return $xml;
        }

        if ($cpfUsuarioLogado != $cpfResponsavelLegalReceita) {
            $xml .= "<success>false</success>\n";
            $xml .= "<msg>Em consulta à base da Receita Federal do Brasil (RFB), verificou-se que o seu CPF não consta como Responsável Legal pelo CNPJ nº " . $dados['txtNumeroCnpj'] . ", o que impede a presente vinculação.\n \nResponsável Legal não se confunde com o conceito de sócio, havendo apenas um CPF na RFB como Responsável Legal pelo CNPJ.\n \n";
            $xml .= "Entre em contato com a RFB para verificar eventuais pendências.</msg>\n";
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

                $chave = explode(" - ", $itemParametro->getStrNomeCampo());
                $valor = '';

                switch (count($chave)) {
                    case 1: $valor = htmlspecialchars(trim($consulta[ucfirst($chave[0])])); break;
                    case 3: $valor = htmlspecialchars(trim($consulta[ucfirst($chave[0])][$chave[1]][$chave[2]])); break;
                    case 4: $valor = htmlspecialchars(trim($consulta[ucfirst($chave[0])][$chave[1]][$chave[2]][$chave[3]])); break;
                    default: $valor = htmlspecialchars(trim($consulta[ucfirst($chave[0])][$chave[1]]));
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
            // $xml .= '<txtNumeroEndereco>'. $consulta['PessoaJuridica']['endereco']['numero'].'</txtNumeroEndereco>';

        } else {

            // Retorno do WS da ANATEL
            $xml .= '<txtNomeFantasia>' . htmlspecialchars($integra::acessarDadoPorChave($consulta, $arrSaidaWS['razaoSocial'])) . '</txtNomeFantasia>';
            $xml .= '<txtNumeroCpfResponsavel>' . InfraUtil::formatarCpf($cpfResponsavelLegalReceita) . '</txtNumeroCpfResponsavel>';
            $xml .= '<txtRazaoSocial>' . htmlspecialchars($integra::acessarDadoPorChave($consulta, $arrSaidaWS['razaoSocial'])) . '</txtRazaoSocial>';
            $xml .= '<txtNomeResponsavelLegal>' . htmlspecialchars($integra::acessarDadoPorChave($consulta, $arrSaidaWS['nomeRespLegal'])) . '</txtNomeResponsavelLegal>';
            $xml .= '<slUf>' . $dadosCidade['idUF'] . '</slUf>';
            $xml .= '<selCidade>' . htmlspecialchars($dadosCidade['idCidade']) . '</selCidade>';
            $xml .= '<txtNumeroCEP>' . MdPetDataUtils::formatCep($integra::acessarDadoPorChave($consulta, $arrSaidaWS['cep'])) . '</txtNumeroCEP>';
            $xml .= '<txtLogradouro>' . htmlspecialchars($integra::acessarDadoPorChave($consulta, $arrSaidaWS['logradouro'])) . ', ' . $integra::acessarDadoPorChave($consulta, $arrSaidaWS['numero']) . ' ' . htmlspecialchars($integra::acessarDadoPorChave($consulta, $arrSaidaWS['complemento'])) . '</txtLogradouro>';
            // $xml .= '<txtNumeroEndereco>'. $consulta['PessoaJuridica']['endereco']['numero'].'</txtNumeroEndereco>';
            $xml .= '<txtComplementoEndereco>' . htmlspecialchars($integra::acessarDadoPorChave($consulta, $arrSaidaWS['complemento'])) . '</txtComplementoEndereco>';
            $xml .= '<txtBairro>' . htmlspecialchars($integra::acessarDadoPorChave($consulta, $arrSaidaWS['bairro'])) . '</txtBairro>';
            $xml .= '<nomeCidade>' . htmlspecialchars($dadosCidade['nomeCidade']) . '</nomeCidade>';

        }

        $xml .= '</dados-pj>';

        return $xml;

    }

    protected function consultarCNPJReceitaWsResponsavelLegalConectado($cnpj)
    {

        $objMdPetIntegracao = $this->consultarIntegracaoPorIdMdPetIntegFuncionalid(MdPetIntegFuncionalidRN::$ID_FUNCIONALIDADE_CNPJ_RECEITA_FEDERAL);
        $cnpj = str_pad(InfraUtil::retirarFormatacao($cnpj), '14', '0', STR_PAD_LEFT);

        if($objMdPetIntegracao){

            $strUrlWebservice       = $objMdPetIntegracao->getStrEnderecoWsdl();
            $strMetodoWebservice    = $objMdPetIntegracao->getStrOperacaoWsdl();
            $cpfUsuarioLogado       = $this->retornaCpfUsuarioLogado(SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno());

            if(empty($cpfUsuarioLogado)){
                $cpfUsuarioLogado = '45921393053';
            }

            // Inicializa o array que será passado para o WS
            $arrParamsWebservice    = ['cnpj' => $cnpj];

            //Recuperando meses - alterado
            $objMdPetIntegParametroDTO = new MdPetIntegParametroDTO();
            $objMdPetIntegParametroDTO->retStrValorPadrao();
            $objMdPetIntegParametroDTO->retStrTpParametro();
            $objMdPetIntegParametroDTO->retStrNome();
            $objMdPetIntegParametroDTO->retStrNomeCampo();
            $objMdPetIntegParametroDTO->setNumIdMdPetIntegracao($objMdPetIntegracao->getNumIdMdPetIntegracao());
            $objMdPetIntegParametroRN = new MdPetIntegParametroRN();
            $arrObjMdPetIntegParametroRN = $objMdPetIntegParametroRN->listar($objMdPetIntegParametroDTO);

            if ($arrObjMdPetIntegParametroRN) {

                $mes = 0;
                $chaveMes = '';

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

                }

                // Monta a consulta para o WS
                $parametro = [ $strMetodoWebservice => $arrParamsWebservice ];

                $objMdPetSoapClienteRN = new MdPetSoapClienteRN($strUrlWebservice , ['soap_version' => $objMdPetIntegracao->getDblNuVersao()]);
                $retorno = $objMdPetSoapClienteRN->execOperacao($strMetodoWebservice, $parametro);
	            $codigoRetornado = isset($retorno['PessoaJuridica']['situacaoCadastral']['codigo']) ? $retorno['PessoaJuridica']['situacaoCadastral']['codigo'] : null;
	
	            $arrCodReceita = explode(',', $objMdPetIntegracao->getStrCodReceitaSuspAuto());

                if ($codigoRetornado && in_array($codigoRetornado,$arrCodReceita)) {
                    return true;
                }
            }
        }

        return false;
    }

    protected function consultarCPFReceitaWsResponsavelLegalConectado($cpf)
    {

        $objMdPetIntegracao = $this->consultarIntegracaoPorIdMdPetIntegFuncionalid(MdPetIntegFuncionalidRN::$ID_FUNCIONALIDADE_CPF_RECEITA_FEDERAL);
        $cpf = str_pad(InfraUtil::retirarFormatacao($cpf), '11', '0', STR_PAD_LEFT);

        if($objMdPetIntegracao){

            $strUrlWebservice       = $objMdPetIntegracao->getStrEnderecoWsdl();
            $strMetodoWebservice    = $objMdPetIntegracao->getStrOperacaoWsdl();
            $cpfUsuarioLogado       = $this->retornaCpfUsuarioLogado(SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno());

            if(empty($cpfUsuarioLogado)){
                $cpfUsuarioLogado = '45921393053';
            }

            // Inicializa o array que será passado para o WS
            $arrParamsWebservice    = ['cpf' => $cpf];

            //Recuperando meses - alterado
            $objMdPetIntegParametroDTO = new MdPetIntegParametroDTO();
            $objMdPetIntegParametroDTO->retStrValorPadrao();
            $objMdPetIntegParametroDTO->retStrTpParametro();
            $objMdPetIntegParametroDTO->retStrNome();
            $objMdPetIntegParametroDTO->retStrNomeCampo();
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

                $objMdPetSoapClienteRN = new MdPetSoapClienteRN($strUrlWebservice , ['soap_version' => $objMdPetIntegracao->getDblNuVersao()]);
                $retorno = $objMdPetSoapClienteRN->execOperacao($strMetodoWebservice, $parametro);

                if(array_key_exists('PessoaFisica', $retorno)){

                    $codigoRetornado = $retorno['PessoaFisica']['situacaoCadastral']['codigo'];

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
                $objInfraException->adicionarValidacao('Sinalizador de Marque caso seu Webservice tenha controle de expiração de cache inválid.');
            }
        }
    }

    private function validarStrSinTpLogradouro(MdPetIntegracaoDTO $objMdPetIntegracaoDTO, InfraException $objInfraException)
    {
        if (InfraString::isBolVazia($objMdPetIntegracaoDTO->getStrSinTpLogradouro())) {
            $objInfraException->adicionarValidacao('Sinalizador de Marque caso seu Webservice tenha o Tipo do Logradouro não informado.');
        } else {
            if (!InfraUtil::isBolSinalizadorValido($objMdPetIntegracaoDTO->getStrSinTpLogradouro())) {
                $objInfraException->adicionarValidacao('Sinalizador de Marque caso seu Webservice tenha o Tipo do Logradouro inválid.');
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
                $this->validarStrEnderecoWsdl($objMdPetIntegracaoDTO, $objInfraException);
                $this->validarStrOperacaoWsdl($objMdPetIntegracaoDTO, $objInfraException);
                $this->validarStrSinCache($objMdPetIntegracaoDTO, $objInfraException);
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
                //Funcionalidade utilizada em outra integração
                $objMdPetIntegFuncionalidRN = new MdPetIntegFuncionalidRN();
                $arrIdMdPetIntegFuncionalidUtilizado = $objMdPetIntegFuncionalidRN->verificarMdPetIntegFuncionalidUtilizado($objMdPetIntegracaoDTO->getNumIdMdPetIntegracao(), $objMdPetIntegracaoDTO->getNumIdMdPetIntegFuncionalid());

                if ($arrIdMdPetIntegFuncionalidUtilizado) {
                    $objInfraException->adicionarValidacao('Funcionalidade sendo utilizada por outra Integração.');
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
        $mdPetIntegracaoDTO->setNumIdMdPetIntegFuncionalid($IdMdPetIntegFuncionalid);
        $mdPetIntegracaoDTO->setStrSinAtivo('S');

        return $mdPetIntegracaoRN->consultar($mdPetIntegracaoDTO);
    }
}
