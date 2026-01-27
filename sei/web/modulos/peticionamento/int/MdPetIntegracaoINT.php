<?
/**
 * TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
 *
 * 25/01/2018 - criado por Usuário
 * 26/08/2024 - Atualização por gabrielg.colab - SPASSU
 *
 * Versão do Gerador de Código: 1.41.0
 */

require_once dirname(__FILE__).'/../../../SEI.php';

class MdPetIntegracaoINT extends InfraINT {

    public static function montarSelectNome($strPrimeiroItemValor, $strPrimeiroItemDescricao, $strValorItemSelecionado, $numIdMdPetIntegFuncionalid=''){
        $objMdPetIntegracaoDTO = new MdPetIntegracaoDTO();
        $objMdPetIntegracaoDTO->retNumIdMdPetIntegracao();
        $objMdPetIntegracaoDTO->retStrNome();

        if ($numIdMdPetIntegFuncionalid!==''){
            $objMdPetIntegracaoDTO->setNumIdMdPetIntegFuncionalid($numIdMdPetIntegFuncionalid);
        }

        if ($strValorItemSelecionado!=null){
            $objMdPetIntegracaoDTO->setBolExclusaoLogica(false);
            $objMdPetIntegracaoDTO->adicionarCriterio(array('SinAtivo','IdMdPetIntegracao'),array(InfraDTO::$OPER_IGUAL,InfraDTO::$OPER_IGUAL),array('S',$strValorItemSelecionado),InfraDTO::$OPER_LOGICO_OR);
        }

        $objMdPetIntegracaoDTO->setOrdStrNome(InfraDTO::$TIPO_ORDENACAO_ASC);

        $objMdPetIntegracaoRN = new MdPetIntegracaoRN();
        $arrObjMdPetIntegracaoDTO = $objMdPetIntegracaoRN->listar($objMdPetIntegracaoDTO);

        return parent::montarSelectArrInfraDTO($strPrimeiroItemValor, $strPrimeiroItemDescricao, $strValorItemSelecionado, $arrObjMdPetIntegracaoDTO, 'IdMdPetIntegracao', 'Nome');
    }

    public static function montarXMLBuscarOperacaoWSDL($enderecoWSDL, $versao){
        $xml = "";
        $xml .= "<operacoes>\n";

        try{

            if (!filter_var($enderecoWSDL, FILTER_VALIDATE_URL) || !InfraUtil::isBolUrlValida( $enderecoWSDL, FILTER_VALIDATE_URL )) {
                $xml .= "<success>false</success>\n";
                $xml .= "<msg>O endereço WSDL não é uma URL válida.</msg>\n";
                $xml .= "</operacoes>\n";
                return $xml;
            }

            $objMdPetSoapClienteRN = new MdPetSoapClienteRN($_POST['endereco_wsdl'] , ['soap_version' => $versao]);
            $operacaoArr = $objMdPetSoapClienteRN->getFunctions();

            if(empty($operacaoArr)){
                $xml .= "<success>false</success>\n";
                $xml .= "<msg>Não existe operação.</msg>\n";
                $xml .= "</operacoes>\n";
                return $xml;
            }

            $xml .= "<success>true</success>\n";
            asort($operacaoArr);
            foreach ($operacaoArr as $key=>$operacao){
                $xml .= "<operacao key='{$key}'>{$operacao}</operacao>\n";
            }

        }catch(Exception $e){
            $xml = "<operacoes>\n";
            $xml .= "<success>false</success>\n";
            $xml .= "<msg>Erro na conexão SOAP: {$e->getMessage()}</msg>\n";
        }

        $xml .= '</operacoes>';
        return $xml;
    }

    public static function montarXMLBuscarOperacaoWSDLParametro($enderecoWSDL, $operacaoWSDL, $tipo_parametro, $versao){

        $xml = "<parametros>\n";
        $objMdPetSoapClienteRN = new MdPetSoapClienteRN($enderecoWSDL , ['soap_version' => $versao]);
        $arrParametro = [];

        if ($tipo_parametro=='e'){
            self::trataDadosEntrada( $objMdPetSoapClienteRN->getParametrosEntradaSaidaWsdl() , $operacaoWSDL , $arrParametro);
        }else{
            self::trataDadosSaida( $objMdPetSoapClienteRN->getParametrosEntradaSaidaWsdl() , $operacaoWSDL, $arrParametro);
        }
        ksort($arrParametro);
        $xml .= "<success>true</success>\n";

        foreach ($arrParametro as $key=>$arrayParam) {

            if(is_array($arrayParam)) {
                ksort($arrayParam);
                foreach ($arrayParam as $chave => $item) {
                    if(is_array($item)){
                        foreach($item as $chaveItem => $value) {
                            $chaveFormatada = $key . " - " . $chave . " - " . $chaveItem;
                            $xml .= "<parametro key='{$chaveFormatada}'>{$chaveFormatada}</parametro>\n";
                        }
                    } else {
                        $chaveFormatada = $key . " - " . $item;
                        $xml .= "<parametro key='{$chaveFormatada}'>{$chaveFormatada}</parametro>\n";
                    }
                }
            } else {
                $xml .= "<parametro key='{$arrayParam}'>{$arrayParam}</parametro>\n";
            }
        }

        $xml .= '</parametros>';
        return $xml;
    }

    public static function confirmarWsConsultaDadosCNPJReceitaFederal() {

        //Caso não exista a integração do tipo 'consultarCnpj' ativo é exibido um alerta para usuário
        $objMdPetVincTpProcessoRN = new MdPetVincTpProcessoRN();
        $objMdPetVincTpProcessoDTO = new MdPetVincTpProcessoDTO();
        $objMdPetVincTpProcessoDTO->retNumIdMdPetVincTpProcesso();
        $objMdPetVincTpProcessoDTO->setNumMaxRegistrosRetorno(1);
        $objMdPetVincUsuExtPj = $objMdPetVincTpProcessoRN->consultar($objMdPetVincTpProcessoDTO);

        //Configurado: "Integração" com funcionalidade "Consultar Dados CNPJ Receita Federal"
        $objMdPetIntegFuncionalidRN = new MdPetIntegFuncionalidRN();
        $arrIdMdPetIntegFuncionalidUtilizado = $objMdPetIntegFuncionalidRN->verificarMdPetIntegFuncionalidUtilizado(null, null, 'Consultar Dados CNPJ Receita Federal');

        $xml = '';
        $xml .= '<resposta>';

        if (!is_null($arrIdMdPetIntegFuncionalidUtilizado)) {
            $xml .= "<valor>S</valor>";
        }else{
            $xml .= "<valor>N</valor>";
        }

        $xml .= "</resposta>";

        return $xml;
    }

    public static function trataDadosEntrada( $arrParametrosEntradaSaidaWsdl, $nmEntidade, &$arrParametro ){
      if ( isset($arrParametrosEntradaSaidaWsdl[$nmEntidade] ) ) {
          foreach ($arrParametrosEntradaSaidaWsdl[$nmEntidade]['fields'] as $k => $v) {
              if (MdPetSoapClienteRN::_verificaTipoDadosWebService($v)) {
                  $arrParametro[$k] = $k;
              } else {
                  self::trataDadosEntrada($arrParametrosEntradaSaidaWsdl, $v, $arrParametro);
              }
          }
      }
    }

    public static function trataDadosSaida($arrParametrosEntradaSaidaWsdl, $operacao = null, &$arrParametro)
    {
        $tipoRetorno = $operacao . 'Response';
        $campoPrincipal = array_key_first($arrParametrosEntradaSaidaWsdl[$tipoRetorno]['fields']);
        $tipoCampo = $arrParametrosEntradaSaidaWsdl[$tipoRetorno]['fields'][$campoPrincipal];

        $resultado = [
            $tipoCampo => self::montarArrayParametros($arrParametrosEntradaSaidaWsdl, $tipoCampo)
        ];

        $arrParametro = $resultado;
    }

    private static function montarArrayParametros($arrParametrosEntradaSaidaWsdl, $tipoCampo)
    {
        $saida = [];
        if (
            !isset($arrParametrosEntradaSaidaWsdl[$tipoCampo]['fields']) ||
            !is_array($arrParametrosEntradaSaidaWsdl[$tipoCampo]['fields'])
        ) {
            return $tipoCampo;
        }

        foreach ($arrParametrosEntradaSaidaWsdl[$tipoCampo]['fields'] as $k => $v) {
            if (MdPetSoapClienteRN::_verificaTipoDadosWebService($v)) {
                $saida[$k] = $k;
            } else {
                $saida[$k] = self::montarArrayParametros($arrParametrosEntradaSaidaWsdl, $v);
            }
        }
        return $saida;
    }

    public static function acessarDadoPorChave($array, $chave) {

        $keys = array_map('trim', explode('-', $chave));
        $keys[0] = ucfirst($keys[0]);

        foreach ($keys as $k) {
            if (!isset($array[$k])) {
                return null;
            }
            $array = $array[$k];
        }

        return $array;
        
    }

}
