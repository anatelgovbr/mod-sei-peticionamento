<?
/**
 * TRIBUNAL REGIONAL FEDERAL DA 4� REGI�O
 *
 * 25/01/2018 - criado por Usu�rio
 *
 * Vers�o do Gerador de C�digo: 1.41.0
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

    public static function montarXMLBuscarOperacaoWSDL($enderecoWSDL){
        $xml = "";
        $xml .= "<operacoes>\n";

        try{

            if (!filter_var($enderecoWSDL, FILTER_VALIDATE_URL) || !InfraUtil::isBolUrlValida( $enderecoWSDL, FILTER_VALIDATE_URL )) {
                $xml .= "<success>false</success>\n";
                $xml .= "<msg>O endere�o WSDL n�o � uma URL v�lida.</msg>\n";
                $xml .= "</operacoes>\n";
                return $xml;
            }

            $objMdPetSoapClienteRN = new MdPetSoapClienteRN($enderecoWSDL, 'wsdl');
            $operacaoArr = $objMdPetSoapClienteRN->getFunctions();

            if(empty($operacaoArr)){
                $xml .= "<success>false</success>\n";
                $xml .= "<msg>N�o existe opera��o.</msg>\n";
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
            $xml .= "<msg>Erro na conex�o SOAP: {$e->getMessage()}</msg>\n";
        }

        $xml .= '</operacoes>';
        return $xml;
    }

    public static function montarXMLBuscarOperacaoWSDLParametro($enderecoWSDL, $operacaoWSDL, $tipo_parametro){

        $xml = "<parametros>\n";

//    try{
        /*
              if (!filter_var($enderecoWSDL, FILTER_VALIDATE_URL) || !InfraUtil::isBolUrlValida( $enderecoWSDL, FILTER_VALIDATE_URL )) {
                $xml .= "<success>false</success>\n";
                $xml .= "<msg>O endere�o WSDL n�o � uma URL v�lida.</msg>\n";
                $xml .= "</parametros>\n";
                return $xml;
              }

              $client = new MdLitSoapClienteRN($enderecoWSDL, 'wsdl');
              $operacaoArr = $client->getFunctions();

              if(empty($operacaoArr)){
                $xml .= "<success>false</success>\n";
                $xml .= "<msg>N�o existe parametro.</msg>\n";
                $xml .= "</parametros>\n";
                return $xml;
              }

              $xml .= "<success>true</success>\n";
              asort($operacaoArr);
              foreach ($operacaoArr as $key=>$operacao){
                $xml .= "<operacao key='{$key}'>{$operacao}</operacao>\n";
              }
        */

        $objMdPetSoapClienteRN = new MdPetSoapClienteRN($enderecoWSDL, 'wsdl');

      if ($tipo_parametro=='e'){
      	$arrParametro = $objMdPetSoapClienteRN->getParamsInput($operacaoWSDL);
      }else{
      	$arrParametro = $objMdPetSoapClienteRN->getParamsOutput($operacaoWSDL);
      }
      ksort($arrParametro);
      $xml .= "<success>true</success>\n";
      //asort($arrParametro);
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

        /*
                $strResultadoParamSaida = '';
                $arrObjMdLitMapearParamSaidaDTO = array();

                if(!empty($idMdLitIntegracao)){
                    $objMdLitMapearParamSaidaRN = new MdLitMapearParamSaidaRN();
                    $objMdLitMapearParamSaidaDTO->retTodos();

                    $objMdLitMapearParamSaidaDTO->setNumIdMdLitIntegracao($idMdLitIntegracao);
                    $objMdLitMapearParamSaidaDTO->setOrdStrCampo(InfraDTO::$TIPO_ORDENACAO_ASC);

                    $arrObjMdLitMapearParamSaidaDTO = $objMdLitMapearParamSaidaRN->listar($objMdLitMapearParamSaidaDTO);
                }

                //tabela de dados de sa�da
                $numRegistrosParametroSaida = count($arrParametroSaida);
                if($numRegistrosParametroSaida > 0){
                    $strSumarioTabela = 'Tabela de configura��o dos dados de sa�da do web-service.';
                    $strCaptionTabela = 'Dados de sa�da';

                    $strResultadoParamSaida .= '<table width="90%" id="tableParametroSaida" class="infraTable" summary="' . $strSumarioTabela . '">' . "\n";
                    $strResultadoParamSaida .= '<tr>';

                    $strResultadoParamSaida .= '<th class="infraTh" width="20%">&nbsp;Dados de Sa�da no Webservice&nbsp;</th>' . "\n";
                    $strResultadoParamSaida .= '<th class="infraTh" width="20%">&nbsp;Campo de Destino no SEI&nbsp;</th>' . "\n";
                    $strResultadoParamSaida .= '<th class="infraTh" width="5%">&nbsp;Chave �nica da Integra��o&nbsp;</th>' . "\n";
                    $strResultadoParamSaida .= '</tr>' . "\n";
                    $strCssTr = '';

                    for ($i = 0; $i < $numRegistrosParametroSaida; $i++) {
                        if ($idMdLitFuncionalidade == 1) {
                            $strItensSelNomeFuncional = MdLitNomeFuncionalINT::montarSelectNome('null', '&nbsp;', null);
                        }else{
                            $strItensSelNomeFuncional = MdLitCampoIntegracaoINT::montarSelectCampoIntergracao('null', '&nbsp;', $idMdLitFuncionalidade, 'S');
                        }

                        $idLinha = $i;

                        $strCssTr = '<tr id="paramSaidaTable_' . $idLinha . '" class="infraTrClara">';
                        $disable = 'disabled="disabled"';
                        $checked = '';

                        if(count($arrObjMdLitMapearParamSaidaDTO)> 0){
                            for ($j = 0; $j < count($arrObjMdLitMapearParamSaidaDTO); $j++){
                                if($arrObjMdLitMapearParamSaidaDTO[$j]->getStrCampo() == $arrParametroSaida[$i] ){
                                    $disable = '';
                                    if ($idMdLitFuncionalidade == 1) {
                                        $strItensSelNomeFuncional = MdLitNomeFuncionalINT::montarSelectNome('null', '&nbsp;', $arrObjMdLitMapearParamSaidaDTO[$j]->getNumIdMdLitNomeFuncional());
                                    }else{
                                        $strItensSelNomeFuncional = MdLitCampoIntegracaoINT::montarSelectCampoIntergracao('null', '&nbsp;', $idMdLitFuncionalidade, 'S', $arrObjMdLitMapearParamSaidaDTO[$j]->getNumIdMdLitCampoIntegracao());
                                    }
                                    $checked = $arrObjMdLitMapearParamSaidaDTO[$j]->getStrChaveUnica() == 'S'?'checked="checked"': '';
                                }
                            }
                        }

                        $strResultadoParamSaida .= $strCssTr;
                        $strResultadoParamSaida .= "<td id='campo_$idLinha>";
                        $strResultadoParamSaida .= "<input type='hidden' name='hdnArrayDadosSaida[$i]' value='{$arrParametroSaida[$i]}' />";
                        $strResultadoParamSaida .= PaginaSEI::tratarHTML($arrParametroSaida[$i]);
                        $strResultadoParamSaida .= "</td>";
                        $strResultadoParamSaida .= "<td align='center'><select id='nomeFuncionalDadosSaida_$idLinha' name='nomeFuncionalDadosSaida[$arrParametroSaida[$i]]' onchange='mudarNomeFuncionalDadosSaida(this)' style='width: 80%;'>{$strItensSelNomeFuncional}</select></td>";
                        $strResultadoParamSaida .= "<td align='center'><input type='radio'name='chaveUnicaDadosSaida' value='{$arrParametroSaida[$i]}' $checked id='chaveUnicaDadosSaida_{$idLinha}' $disable> </td>";

                        $strResultadoParamSaida .= '</tr>' . "\n";
                    }
                    $strResultadoParamSaida .= '</table>';
                }
        */

//    }catch(Exception $e){
        /*
              $xml .= "<success>false</success>\n";
              $xml .= "<msg>Erro na conex�o SOAP: {$e->getMessage()}</msg>\n";
        */
//    }

        $xml .= '</parametros>';
        return $xml;
    }

    public static function confirmarWsConsultaDadosCNPJReceitaFederal() {

        //Caso n�o exista a integra��o do tipo 'consultarCnpj' ativo � exibido um alerta para usu�rio
        $objMdPetVincTpProcessoRN = new MdPetVincTpProcessoRN();
        $objMdPetVincTpProcessoDTO = new MdPetVincTpProcessoDTO();
        $objMdPetVincTpProcessoDTO->retNumIdMdPetVincTpProcesso();
        $objMdPetVincTpProcessoDTO->setNumMaxRegistrosRetorno(1);
        $objMdPetVincUsuExtPj = $objMdPetVincTpProcessoRN->consultar($objMdPetVincTpProcessoDTO);

        //Configurado: "Integra��o" com funcionalidade "Consultar Dados CNPJ Receita Federal"
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
}
