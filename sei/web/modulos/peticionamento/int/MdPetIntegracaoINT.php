<?php
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

    /**
     * Operações do serviço, pelo WSDL no SOAP ou pelo documento OpenAPI no REST, para a tela de cadastro da integração.
     *
     * @param string|null $enderecoWSDL endereço validado; o cliente usa o endereço enviado pela tela
     * @param mixed $versao versão SOAP
     * @param string|null $strTipo tipo de integração: S, SOAP, ou R, REST
     * @return string
     */
    public static function montarXMLBuscarOperacaoWSDL($enderecoWSDL, $versao, $strTipo = 'S'){
        $xml = "";
        $xml .= "<operacoes>\n";
        $objDescoberta = MdPetIntegFabricaRN::criarDescoberta($strTipo, $_POST['endereco_wsdl'], $versao);

        try{

            $bolRest = $strTipo === MdPetIntegFabricaRN::TIPO_REST;
            // No REST, o isBolUrlValida fica de fora: ele testa com HEAD, que há serviço que recusa, e a descoberta já baixa o documento com GET
            if (!filter_var($enderecoWSDL, FILTER_VALIDATE_URL) || (!$bolRest && !InfraUtil::isBolUrlValida( $enderecoWSDL, FILTER_VALIDATE_URL ))) {
                $xml .= "<success>false</success>\n";
                $xml .= $bolRest ? "<msg>O endereço do documento OpenAPI não é uma URL válida.</msg>\n" : "<msg>O endereço WSDL não é uma URL válida.</msg>\n";
                $xml .= "</operacoes>\n";
                return $xml;
            }

            $operacaoArr = $objDescoberta->listarOperacoes();

            if(empty($operacaoArr)){
                $xml .= "<success>false</success>\n";
                $xml .= "<msg>Não existe operação.</msg>\n";
                $xml .= "</operacoes>\n";
                return $xml;
            }

            $xml .= "<success>true</success>\n";
            asort($operacaoArr);
            foreach ($operacaoArr as $key=>$operacao){
                $strOperacao = htmlspecialchars((string) $operacao, ENT_QUOTES, 'ISO-8859-1');
                $xml .= "<operacao key='{$key}'>{$strOperacao}</operacao>\n";
            }

            $strSugestao = $objDescoberta->sugerirEnderecoExecucao((string) $_POST['endereco_wsdl']);
            if ($strSugestao !== null) {
                $xml .= '<sugestao>' . htmlspecialchars($strSugestao, ENT_QUOTES, 'ISO-8859-1') . "</sugestao>\n";
            }

        }catch(Exception $e){
            $xml = "<operacoes>\n";
            $xml .= "<success>false</success>\n";
            $xml .= '<msg>' . $objDescoberta->descreverFalha($e) . "</msg>\n";
        }

        $xml .= '</operacoes>';
        return $xml;
    }

    /**
     * Campos de entrada ("e") ou de saída da operação, no formato de caminho que o mapeamento grava. No REST, cada campo
     * de entrada leva também o local dele na chamada.
     *
     * @param string|null $enderecoWSDL endereço do WSDL ou do documento OpenAPI
     * @param string|null $operacaoWSDL
     * @param string|null $tipo_parametro
     * @param mixed $versao versão SOAP
     * @param string|null $strTipo tipo de integração: S, SOAP, ou R, REST
     * @return string
     */
    public static function montarXMLBuscarOperacaoWSDLParametro($enderecoWSDL, $operacaoWSDL, $tipo_parametro, $versao, $strTipo = 'S'){

        $xml = "<parametros>\n";
        $arrCampos = MdPetIntegFabricaRN::criarDescoberta($strTipo, $enderecoWSDL, $versao)->listarCampos((string) $operacaoWSDL, (string) $tipo_parametro);
        $xml .= "<success>true</success>\n";

        foreach ($arrCampos as $arrCampo) {
            $strCampo = htmlspecialchars($arrCampo['campo'], ENT_QUOTES, 'ISO-8859-1');
            $strLocal = $arrCampo['local'] === null ? '' : " local='{$arrCampo['local']}'";
            $xml .= "<parametro key='{$strCampo}'{$strLocal}>{$strCampo}</parametro>\n";
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

    /**
     * Parâmetros de saída da operação do webservice, a partir dos tipos lidos do WSDL.
     *
     * @param array<string, mixed> $arrParametrosEntradaSaidaWsdl tipos do WSDL
     * @param string|null $operacao nome da operação
     * @param array<string, mixed> $arrParametro recebe os parâmetros de saída
     * @return void
     */
    public static function trataDadosSaida(array $arrParametrosEntradaSaidaWsdl, ?string $operacao, array &$arrParametro): void
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

    /**
     * Completa a integração com os dados do Tipo de Integração REST enviados pela tela: o endereço do documento OpenAPI vai
     * para a coluna própria, o método e o caminho saem da operação escolhida, e as linhas da grade de headers e o token do
     * corpo vão para a RN pelo DTO. Nos demais tipos, nada muda.
     *
     * @param MdPetIntegracaoDTO $objMdPetIntegracaoDTO
     * @param array<string, mixed> $arrPost
     * @return void
     */
    public static function preencherTipoIntegracao(MdPetIntegracaoDTO $objMdPetIntegracaoDTO, array $arrPost): void
    {
        if (($arrPost['rdStaTpClienteWs'] ?? null) !== MdPetIntegFabricaRN::TIPO_REST) {
            return;
        }

        $strOperacao = trim((string) ($arrPost['selOperacaoWsdl'] ?? ''));
        [$strMetodo, $strCaminho] = str_contains($strOperacao, ' ') ? MdPetIntegOpenApiRN::separarOperacao($strOperacao) : [null, null];

        $objMdPetIntegracaoDTO->setStrTpClienteWs(MdPetIntegFabricaRN::TIPO_REST);
        $objMdPetIntegracaoDTO->setDblNuVersao(null);
        $objMdPetIntegracaoDTO->setStrEnderecoWsdl(null);
        $objMdPetIntegracaoDTO->setStrEnderecoOpenapi(trim((string) ($arrPost['txtEnderecoWsdl'] ?? '')));
        $objMdPetIntegracaoDTO->setStrEnderecoExecucao(trim((string) ($arrPost['txtEnderecoExecucao'] ?? '')));
        $objMdPetIntegracaoDTO->setStrMetodoHttp($strMetodo);
        $objMdPetIntegracaoDTO->setStrCaminhoOperacao($strCaminho);
        $strMetodoAutenticacao = $arrPost['selMetodoAutenticacao'] ?? null;
        $objMdPetIntegracaoDTO->setStrStaMetodoAutenticacao(in_array($strMetodoAutenticacao, [MdPetIntegFabricaRN::AUTENTICACAO_SEM, MdPetIntegFabricaRN::AUTENTICACAO_HEADER, MdPetIntegFabricaRN::AUTENTICACAO_CORPO], true) ? $strMetodoAutenticacao : null);
        $objMdPetIntegracaoDTO->setArrLinhasHeaders(PaginaSEI::getInstance()->getArrItensTabelaDinamica((string) ($arrPost['hdnTbHeaders'] ?? '')));
        $objMdPetIntegracaoDTO->setStrCampoTokenAutenticacao(trim((string) ($arrPost['nomeFuncionalDadosEntrada'][MdPetIntegFabricaRN::ENTRADA_TOKEN] ?? '')));
        $objMdPetIntegracaoDTO->setStrTokenAutenticacao(trim((string) ($arrPost['txtTokenAutenticacao'] ?? '')));
    }

    /**
     * Opções do Método de Autenticação do REST, no padrão do Utilidades; sem método gravado, fica "Sem Autenticação".
     *
     * @param string|null $strSelecionado método gravado
     * @return string
     */
    public static function montarSelectMetodoAutenticacao(?string $strSelecionado): string
    {
        $arrMetodos = [
            MdPetIntegFabricaRN::AUTENTICACAO_SEM => 'Sem Autenticação',
            MdPetIntegFabricaRN::AUTENTICACAO_HEADER => 'Header Authentication by Token',
            MdPetIntegFabricaRN::AUTENTICACAO_CORPO => 'Body Authentication by Token',
        ];

        return InfraINT::montarSelectArray(null, null, $strSelecionado ?? MdPetIntegFabricaRN::AUTENTICACAO_SEM, $arrMetodos);
    }

    /**
     * Headers fixos gravados, para a grade da tela: id, atributo, conteúdo exibido, dado restrito e conteúdo enviado. O
     * conteúdo do dado restrito não volta ao navegador e vai como máscara nas duas colunas (ADR-PET-012).
     *
     * @param array<MdPetIntegParametroDTO> $arrObjMdPetIntegParametroDTO mapeamento da integração
     * @return string
     */
    public static function montarItensHeaders(array $arrObjMdPetIntegParametroDTO): string
    {
        $arrItens = [];
        foreach ($arrObjMdPetIntegParametroDTO as $objMdPetIntegParametroDTO) {
            if ($objMdPetIntegParametroDTO->getStrTpParametro() !== MdPetIntegFabricaRN::PARAMETRO_HEADER) {
                continue;
            }

            $bolRestrito = $objMdPetIntegParametroDTO->getStrSinDadoConfidencial() === 'S';
            $strConteudo = $bolRestrito ? MdPetIntegFabricaRN::MASCARA_DADO_RESTRITO : (string) $objMdPetIntegParametroDTO->getStrValorPadrao();
            $arrItens[] = [$objMdPetIntegParametroDTO->getNumIdMdPetIntegParametro(), $objMdPetIntegParametroDTO->getStrNome(), $strConteudo, $bolRestrito ? 'Sim' : 'Não', $strConteudo];
        }

        return PaginaSEI::getInstance()->gerarItensTabelaDinamica($arrItens);
    }

    /**
     * Ofuscação do dado restrito, a mesma do Utilidades (MdUtlAdmIntegracaoINT::gerenciaDadosRestritos): base64 e inversão
     * da string, duas vezes. Não é criptografia (ADR-PET-012).
     */
    public static function ofuscarDadoRestrito(string $strValor): string
    {
        return base64_encode(strrev(base64_encode(strrev($strValor))));
    }

    /**
     * Desfaz a ofuscação de ofuscarDadoRestrito, só para montar a chamada ao serviço.
     */
    public static function desfazerOfuscacao(string $strValor): string
    {
        return strrev(base64_decode(strrev(base64_decode($strValor))));
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
