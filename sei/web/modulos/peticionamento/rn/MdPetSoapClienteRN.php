<?
/**
 * TRIBUNAL REGIONAL FEDERAL DA 4� REGI�O
 *
 * 03/04/2017 - criado por Ellyson de Jesus Silva
 *
 * Vers�o do Gerador de C�digo: 1.40.1
 */
 
require_once dirname(__FILE__) . '/../../../SEI.php';
require_once dirname(__FILE__) . '/../lib/nusoap/nusoap.php';

class MdPetSoapClienteRN extends nusoap_client
{

    protected $wsdl;

    protected $options;

    function __construct($endpoint, $wsdl = false, $proxyhost = false, $proxyport = false, $proxyusername = false, $proxypassword = false, $timeout = 0, $response_timeout = 30, $portName = '')
    {
        ini_set('default_socket_timeout', 6000);
        ini_set("soap.wsdl_cache_enabled", "0");

        $this->wsdl = $wsdl;
        parent::nusoap_client($endpoint, $wsdl, $proxyhost, $proxyport, $proxyusername, $proxypassword, $timeout, $response_timeout, $portName);
    }

    public function getFunctions()
    {
        $functions = array();

        if ($this->endpointType == 'wsdl' && is_null($this->wsdl)) {
            $this->loadWSDL();
            if ($this->getError())
                return false;
        }
        //escrevendo nome de cada opera�ao disponivel
        foreach ($this->operations as $op) {
            $functions[] = $op['name']; //nome da opera�ao
        }
        return $functions;
    }

    public function getParamsInput($nameOperations, $recursivo = false)
    {
        $operations = $this->getOperationData($nameOperations);
        $complexTypes = $this->wsdl->schemas[$this->wsdl->namespaces['tns']][0]->complexTypes;
        $outputArr = array();

        if ($recursivo) {
            $returnType = $nameOperations;
        } else {
            if (!$operations) {
                throw new InfraException('Nome da opera��o n�o existe.');
            }

            $nameType = $this->getEntidadePorUrlWSDL($operations['input']['parts']['parameters']);

            if (!$nameType) {
                $nameType = key($operations['input']['parts']);
            }

            if (!$complexTypes[$nameType]['elements']) {
                return $outputArr;
            }


            $returnType = current($complexTypes[$nameType]['elements']);
            $returnType = $this->getEntidadePorUrlWSDL($returnType['type']);
            $returnType = $this->_verificaTipoDadosWebService($returnType, $nameType);
        }

        if (!empty($complexTypes[$returnType]['elements'])) {


            foreach ($complexTypes[$returnType]['elements'] as $nome => $elementArr) {
                $outputArr[] = $nome;
            }
        }

        if (array_key_exists('extensionBase', $complexTypes[$returnType])) {
            $returnType2 = $this->getEntidadePorUrlWSDL($complexTypes[$returnType]['extensionBase']);
            $outputArr2 = $this->getParamsInput($returnType2, true);

            if (count($outputArr2) > 0) {
                $outputArr = array_merge($outputArr, $outputArr2);
                sort($outputArr);
            }
        }

        return $outputArr;
    }

    /*
     * Verifica se o tipo retornado � um tipo ou realmente o nome.
     * */
    private function _verificaTipoDadosWebService($returnType, $nameType)
    {
        $isTipo = false;
        $arrTipos = array('string', 'boolean', 'long', 'int', 'decimal', 'dateTime', 'short');

        if (in_array($returnType, $arrTipos)) {
            $isTipo = true;
        }

        $retorno = $isTipo ? $nameType : $returnType;

        return $retorno;
    }


    public function getParamsOutput($nameOperations)
    {
        $operations = $this->getOperationData($nameOperations);
        $complexTypes = $this->wsdl->schemas[$this->wsdl->namespaces['tns']][0]->complexTypes;
        $outputArr = array();

        if (!$operations)
            throw new InfraException('Nome da opera��o n�o existe.');

        /**
         * @todo if para tratar o web-service da ANATEL de servi�o aonde o wsdl n�o possui assinatura de output
         */
        if (empty($operations['output']['parts'])) {
            $resp = $this->call($nameOperations, array());
            if ($this->responseData === false) {
                $objInfraException = new InfraException();
                $objInfraException->adicionarValidacao('N�o foi poss�vel comunica��o com o servidor.');
                $objInfraException->lancarValidacoes();
            }

            if (!$resp) {
                $objInfraException = new InfraException();
                $objInfraException->adicionarValidacao('N�o possui resposta do web-service.');
                $objInfraException->lancarValidacoes();
            }

            foreach ($resp['listaTipoServico'][0] as $campo => $valor) {
                $outputArr[] = $campo;
            }
            return $outputArr;
        }

        $nameType = $this->getEntidadePorUrlWSDL($operations['output']['parts']['parameters']);
        if (!$nameType)
            $nameType = key($operations['output']['parts']);

        $arrEnderecos = $complexTypes['endereco']['elements'];

        $returnType = current($complexTypes[$nameType]['elements']);
        $returnType = $this->getEntidadePorUrlWSDL($returnType['type']);

        if ($complexTypes[$returnType]['elements']) {
//            foreach ($complexTypes[$returnType]['elements'] as $nome => $elementArr) {
//                if ($nome == 'endereco') {
//                    $outputArr[][$nome] = $arrEnderecos;
//                } else {
//                    $outputArr[] = $nome;
//                }
//            }
            foreach ($complexTypes[$returnType]['elements'] as $chave => $elementos) {
                $arrTypes = explode('/:', $elementos['type']);
                $type = end($arrTypes);
                $filho = null;
                if(key_exists($type, $complexTypes)){
                    $filho = $complexTypes[$type];
                    foreach ($filho['elements'] as $filhoChave => $filhoValor){
                        $outputArr[$returnType][$chave][$filhoChave] = $filhoValor['name'];
                    }
                } else {
                    $outputArr[$returnType][$chave] = $elementos['name'];
                }
            }
        }
        return $outputArr;

    }

    private function getEntidadePorUrlWSDL($urlWSDL)
    {
        $urlWSDL = strrchr($urlWSDL, ':');
        if (!$urlWSDL) return null;

        return preg_replace('/[^a-z0-9]/i', '', $urlWSDL);
    }


    public function enviarDados($objMdLitIntegracaoDTO, $montarParametroEntrada, $nomeArrPrincipal = false)
    {
        $arrResultado = array();

        try {
            $err = $this->getError();

            if ($err) {
                throw new InfraException($err);
            }

            $this->soap_defencoding = 'ISO-8859-1';
            $this->decode_utf8 = false;
            if ($nomeArrPrincipal) {
                $montarParametroEntrada = array($nomeArrPrincipal => $montarParametroEntrada);
            }
            $opData = $this->getOperationData($objMdLitIntegracaoDTO->getStrOperacaWsdl());

            if (!empty($opData['endpoint'])) {
                //@todo retirar quanto verificar a configura��o do wso2 da anatel
                $this->forceEndpoint = str_replace('https', 'http', $opData['endpoint']);
            }

            $this->persistentConnection = false;
            $arrResultado = $this->call($objMdLitIntegracaoDTO->getStrOperacaWsdl(), $montarParametroEntrada);

            $err = $this->getError();

            if ($err) {

                if ($objMdLitIntegracaoDTO->getNumIdMdLitFuncionalidade() == MdLitIntegracaoRN::$ARRECADACAO_CONSULTAR_LANCAMENTO) {
                    $exception = new InfraException();
                    $exception->lancarValidacao('N�o foi poss�vel a comunica��o com o Webservice da Arrecada��o. Contate o Gestor do Controle.', null, new Exception($err));
                }

                InfraDebug::getInstance()->setBolLigado(true);
                InfraDebug::getInstance()->setBolDebugInfra(false);
                InfraDebug::getInstance()->limpar();
                InfraDebug::getInstance()->gravar($this->request);
                InfraDebug::getInstance()->gravar('Ocorreu erro ao conectar com a opera��o(' . $objMdLitIntegracaoDTO->getStrOperacaWsdl() . ').' . $err);

                LogSEI::getInstance()->gravar(InfraDebug::getInstance()->getStrDebug(), InfraLog::$INFORMACAO);

                $objInfraException = new InfraException();
                $objInfraException->adicionarValidacao('Ocorreu erro ao conectar com a opera��o(' . $objMdLitIntegracaoDTO->getStrOperacaWsdl() . '). ' . $err);
                $objInfraException->lancarValidacoes();
            }

        } catch (Exception $e) {

            InfraDebug::getInstance()->setBolLigado(false);
            InfraDebug::getInstance()->setBolDebugInfra(false);
            InfraDebug::getInstance()->setBolEcho(false);
            throw new InfraException('Ocorreu erro ao executar o servi�o de lan�amento. ', $e);
        }

        if (count($arrResultado) > 0) {
            return $arrResultado;
        }

        return false;
    }

    public function consultarWsdl($operacao, $dados)
    {
        if ($this->endpointType == 'wsdl' && is_null($this->wsdl)) {
            $this->loadWSDL();
            if ($this->getError())
                return false;
        }
        return $this->call($operacao, $dados);
    }

}