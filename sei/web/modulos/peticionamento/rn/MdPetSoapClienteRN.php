<?
/**
 * TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
 *
 * 03/04/2017 - criado por Ellyson de Jesus Silva
 * 02/07/2025 - atualizado por michaelr.colab
 *
 * Versão do Gerador de Código: 1.40.1
 */
 
require_once dirname(__FILE__) . '/../../../SEI.php';

class MdPetSoapClienteRN extends BeSimple\SoapClient\SoapClient
{

    public $strUrlWsdl = null;

    function __construct($enderecoWSDL, $options = [] ){
        ini_set("default_socket_timeout", "5");
        ini_set("soap.wsdl_cache_enabled", "0");

        $arrOptions = [
            'trace'		                   => true,
            'exceptions'                   => true,
            'encoding'	                   => 'ISO-8859-1',
            'cache_wsdl'                   => WSDL_CACHE_NONE,
            'soap_version'                 => SOAP_1_2,
            'resolve_wsdl_remote_includes' => true
        ];

        // informa a versao do soap
        if ( !empty( $options) ) {
            foreach ( $options as $k => $v ) {
                if ( $k == 'soap_version' )
                    $arrOptions[$k] = $v == '1.1' ? SOAP_1_1 : SOAP_1_2;
                else
                    $arrOptions[$k] = $v;
            }
        }

        $this->strUrlWsdl = $enderecoWSDL;

        parent::__construct($enderecoWSDL,$arrOptions);
    }

    public function getFunctions()
    {
        $arrOperacao = $this->__getFunctions();
        $arrMetodos  = [];

        // trata o nome da operacao para retornar somente o valor necessario
        foreach ( $arrOperacao as $key => $operacao ) {
            $array = explode(' ', substr($operacao, 0, strpos($operacao, '(')));
            $arrMetodos[] = end($array);
        }

        // ordena de forma crescente
        asort( $arrMetodos );

        // remove duplicidade
        $arrMetodos = array_unique( $arrMetodos );

        return $arrMetodos;
    }

    public function getParametrosEntradaSaidaWsdl() 
    {
        $soapTypes = $this->parseSoapClientTypes( $this->__getTypes() );
        $wsdlTypes = $this->parseComplexTypesWithExtensionBase( $this->strUrlWsdl );

        // Unir informações
        foreach ($wsdlTypes as $typeName => $wsdlInfo) {
            if (!isset($soapTypes[$typeName])) {
                $soapTypes[$typeName] = $wsdlInfo;
            } else {
                // Se já existe no SoapClient, vamos tentar adicionar o base
                $soapTypes[$typeName]['base'] = $wsdlInfo['base'] ?? null;
            }
        }
        return $soapTypes;
    }

    private function parseSoapClientTypes(array $types) {
        $parsed = [];

        foreach ($types as $type) {
            if (preg_match('/^struct\s+(\w+)\s*\{\s*(.*?)\s*\}$/s', $type, $matches)) {
                $typeName = $matches[1];
                $fieldsBlock = $matches[2];

                $fields = [];
                foreach (explode(";\n", $fieldsBlock) as $line) {
                    $line = trim($line);
                    if (!$line) continue;

                    if (preg_match('/(\w+)\s+(\w+)/', $line, $fmatch)) {
                        $fieldType = $fmatch[1];
                        $fieldName = $fmatch[2];
                        $fields[$fieldName] = $fieldType;
                    }
                }

                $parsed[$typeName] = [
                    'fields' => $fields
                ];
            }
        }

        return $parsed;
    }

    private function parseComplexTypesWithExtensionBase(string $wsdlUrl) {
        $dom = new DOMDocument();
        $dom->load($wsdlUrl);

        $xpath = new DOMXPath($dom);
        $xpath->registerNamespace("xsd", "http://www.w3.org/2001/XMLSchema");

        $types = [];

        $query = "//xsd:complexType";
        foreach ($xpath->query($query) as $complexType) {
            $name = $complexType->getAttribute('name');
            if (!$name) continue;

            $base = null;
            $fields = [];

            $extension = $xpath->query("xsd:complexContent/xsd:extension", $complexType)->item(0);
            if ($extension instanceof DOMElement) {
                $base = $extension->getAttribute('base');

                foreach ($xpath->query("xsd:sequence/xsd:element", $extension) as $el) {
                    $fields[$el->getAttribute('name')] = $el->getAttribute('type');
                }
            } else {
                // Caso não haja extensão (tipo comum)
                foreach ($xpath->query("xsd:sequence/xsd:element", $complexType) as $el) {
                    $fields[$el->getAttribute('name')] = $el->getAttribute('type');
                }
            }

            $types[$name] = [
                'base' => $base,
                'fields' => $fields
            ];
        }

        return $types;
    }

    
    /* Verifica se o tipo retornado é um tipo ou realmente o nome */
    public static function _verificaTipoDadosWebService($tipo){
        $isTipo = false;
        $arrTipos = ['string', 'boolean', 'long', 'int', 'decimal', 'dateTime', 'short'];
        if ( in_array($tipo, $arrTipos) ) $isTipo = true;
        return $isTipo;
    }

    public function execOperacao($strOperacao,$montarParametroEntrada = []) {
        try {
            if ( ! InfraWS::isBolServicoExiste($this, $strOperacao))
                return ['suc' => false , 'msg' => "Não existe ou não foi encontrado a operação: $strOperacao."];

            $arrResultado = $this->__soapCall($strOperacao, $montarParametroEntrada);

            $arrResultado = $this->objetoParaArray($arrResultado);

            return $arrResultado;
        } catch ( SoapFault $s ) {
            $err = $this->trataSoapFaul( $s );
            LogSEI::getInstance()->gravar( $err , InfraLog::$INFORMACAO );
            return ['faultcode ' => 'env:Server' , 'faultstring ' => $err];
        }
    }

    public function objetoParaArray( $object ) {
        $result = (array) $object;
        foreach( $result as &$value ) {
            if ( $value instanceof stdClass || $value instanceof SimpleXMLElement )
                $value = $this->objetoParaArray( $value );

            if ( is_array( $value ) ) {
                foreach ( $value as $k => $v ) {
                    if ( $v instanceof stdClass || $v instanceof SimpleXMLElement )
                        $value[$k] = $this->objetoParaArray( $v );
                }
            }
        }
        return $result;
    }

    public function trataSoapFaul($objSoapFault) {
        // 1. Tente extrair o faultstring do XML de resposta
        if (!empty($this->__getLastResponse())) {
            $arrResp = $this->getSoapFaultString($this->__getLastResponse());
            if (!empty($arrResp['faultstring'])) {
                return mb_convert_encoding($arrResp['faultstring'], 'UTF-8');
            }
        }
        // 2. Se não conseguir, use a mensagem da exceção
        if ($objSoapFault->getMessage()) {
            return $objSoapFault->getMessage();
        }
        // 3. Caso não tenha nada, mensagem padrão
        return 'Não Identificada';
    }

    private function getSoapFaultString($xml)
    {
        $faultstring = '';
        if (!empty($xml)) {
            $dom = new DOMDocument();
            @$dom->loadXML($xml);
            $nodes = $dom->getElementsByTagName('faultstring');
            if ($nodes->length > 0) {
                $faultstring = $nodes->item(0)->nodeValue;
            }
        }
        return ['faultstring' => $faultstring];
    }
}