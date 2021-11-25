<?
/**
 * TRIBUNAL REGIONAL FEDERAL DA 4? REGI?O
 *
 * 08/12/2016 - criado por Marcelo Bezerra - CAST
 *
 * Vers?o do Gerador de C?digo: 1.39.0
 */

require_once dirname(__FILE__) . '/../../../SEI.php';

class MdPetIntTipoIntimacaoINT extends InfraINT
{

    public static function montarSelectTipoIntimacaoListaExterna($strValorSelecionado = ''){
        $objMdPetRelDestRN = new MdPetIntRelDestinatarioRN();
        $arrIntimacao = $objMdPetRelDestRN->listarDadosComboIntimacaoExterno();

        $select =  '<option value="0"> </option>' ;

        if (count($arrIntimacao) > 0)
        {
            foreach ($arrIntimacao as $key => $item)
            {
                asort($arrIntimacao);
                $add = '';
                if($strValorSelecionado != '0' && $key == $strValorSelecionado)
                {
                    $add = 'selected = selected';
                }

                $select .= '<option '.$add.' value="' . $key . '">' . $item . '</option>';
            }
        }

        return $select;
    }

    public static function montarSelectIdMdPetIntTipoIntimacao($strPrimeiroItemValor, $strPrimeiroItemDescricao, $strValorItemSelecionado)
    {
        $objMdPetIntTipoIntimacaoDTO = new MdPetIntTipoIntimacaoDTO();
        $objMdPetIntTipoIntimacaoDTO->retTodos();

        if ($strValorItemSelecionado != null) {
            $objMdPetIntTipoIntimacaoDTO->setBolExclusaoLogica(false);
            $objMdPetIntTipoIntimacaoDTO->adicionarCriterio(array('SinAtivo', 'IdMdPetIntTipoIntimacao'), array(InfraDTO::$OPER_IGUAL, InfraDTO::$OPER_IGUAL), array('S', $strValorItemSelecionado), InfraDTO::$OPER_LOGICO_OR);
        }

        $objMdPetIntTipoIntimacaoDTO->setOrdStrNome(InfraDTO::$TIPO_ORDENACAO_ASC);

        $objMdPetIntTipoIntimacaoRN = new MdPetIntTipoIntimacaoRN();
        $arrObjMdPetIntTipoIntimacaoDTO = $objMdPetIntTipoIntimacaoRN->listar($objMdPetIntTipoIntimacaoDTO);

        return parent::montarSelectArrInfraDTO($strPrimeiroItemValor, $strPrimeiroItemDescricao, $strValorItemSelecionado, $arrObjMdPetIntTipoIntimacaoDTO, 'IdMdPetIntTipoIntimacao', 'Nome');
    }

    public static function montaSelectTipoResposta8612($arrTiposResposta, $strValorItemSelecionado = null)
    {
        $montaSelect = "<option value=\"null\">&nbsp;</option>";

        foreach($arrTiposResposta as $key => $label){
            if ($strValorItemSelecionado === $key) {
                $selecionado = " selected=\"selected\" ";
            }else{
                $selecionado = '';
            }
            $montaSelect .= "<option value=\"$key\" ". $selecionado . ">$label</option>";
        }

        return $montaSelect;
    }

    public static function montaSelectTipoRespostaIntimacao($idTipoIntimacao, $retornaXml = true){

        $objMdPetIntTipoIntimacaoDTO = new MdPetIntTipoIntimacaoDTO();
        $objMdPetIntTipoIntimacaoDTO->retStrTipoRespostaAceita();
        $objMdPetIntTipoIntimacaoDTO->setNumIdMdPetIntTipoIntimacao($idTipoIntimacao);
        $objMdPetIntTipoIntimacaoRN = new MdPetIntTipoIntimacaoRN();
        $arrObjMdPetIntTipoIntimacaoDTO = $objMdPetIntTipoIntimacaoRN->listar($objMdPetIntTipoIntimacaoDTO);

        $tipoRespostaAceita = $arrObjMdPetIntTipoIntimacaoDTO[0]->getStrTipoRespostaAceita();

        $objMdPetIntRelIntimRespDTO = new MdPetIntRelIntimRespDTO();
        $objMdPetIntRelIntimRespDTO->retTodos(true);
        $objMdPetIntRelIntimRespDTO->setNumIdMdPetIntTipoIntimacao($idTipoIntimacao);
        $objMdPetIntRelIntimRespDTO->setOrdNumIdMdPetIntTipoIntimacao(InfraDTO::$TIPO_ORDENACAO_ASC);
        $objMdPetIntRelIntimRespDTO->setOrdStrNomeMdPetIntTipoResp(InfraDTO::$TIPO_ORDENACAO_ASC);
        $objMdPetIntRelIntimRespRN = new MdPetIntRelIntimRespRN();
        $arrMdPetIntRelIntimRespDTO = $objMdPetIntRelIntimRespRN->listar($objMdPetIntRelIntimRespDTO);

        $tipoResposta = array();

        for ($i = 0; $i < count($arrMdPetIntRelIntimRespDTO); $i++){
            $id = $arrMdPetIntRelIntimRespDTO[$i]->getNumIdMdPetIntTipoIntimacao() . '_' .$arrMdPetIntRelIntimRespDTO[$i]->getNumIdMdPetIntTipoResp();
            if ($arrMdPetIntRelIntimRespDTO[$i]->getStrTipoPrazoExternoMdPetIntTipoResp() == 'N') {
              $prazo = '(Não Possui Prazo Externo)';
            }else{ 
              $prazo = '(' . $arrMdPetIntRelIntimRespDTO[$i]->getNumValorPrazoExternoMdPetIntTipoResp();
              if ($arrMdPetIntRelIntimRespDTO[$i]->getStrTipoPrazoExternoMdPetIntTipoResp() == 'D') {
                $tipoDia = '';
                if ($arrMdPetIntRelIntimRespDTO[$i]->getStrTipoDia() == 'U') {
                  $tipoDia = ' Útil';
                  if ($arrMdPetIntRelIntimRespDTO[$i]->getNumValorPrazoExternoMdPetIntTipoResp() > 1) {
                    $tipoDia = ' Úteis';
                  }
                }
                $prazo .= $arrMdPetIntRelIntimRespDTO[$i]->getNumValorPrazoExternoMdPetIntTipoResp() > 1 ?  ' Dias'.$tipoDia : ' Dia'.$tipoDia;
              } else if ($arrMdPetIntRelIntimRespDTO[$i]->getStrTipoPrazoExternoMdPetIntTipoResp() == 'M') {
                $prazo .= $arrMdPetIntRelIntimRespDTO[$i]->getNumValorPrazoExternoMdPetIntTipoResp() > 1 ?  ' Meses' : ' Mês';
              } else if ($arrMdPetIntRelIntimRespDTO[$i]->getStrTipoPrazoExternoMdPetIntTipoResp() == 'A') {
                $prazo .= $arrMdPetIntRelIntimRespDTO[$i]->getNumValorPrazoExternoMdPetIntTipoResp() > 1 ?  ' Anos' : ' Ano';
              }
              $prazo .= ')';
            }
        
            if ($arrMdPetIntRelIntimRespDTO[$i]->getStrTipoRespostaAceitaMdPetIntTipoResp() == 'E') {
                $resposta = 'Exige Resposta';
            } else {
                $resposta = 'Resposta Facultativa';
            }

            $nomeTpResp = PaginaSEI::tratarHTML($arrMdPetIntRelIntimRespDTO[$i]->getStrNomeMdPetIntTipoResp());
            $prazo = $prazo;
            $nome = $nomeTpResp . ' ' . $prazo . ' - ' . $resposta;
            
            // TODO: refatorar este trecho, para nao usar este delimitador '-#-', retornar em formato XML usando atributos ou subtags na tags Ids retornada pelo ajax
            $tipoResposta[$i] = $arrMdPetIntRelIntimRespDTO[$i]->getNumIdMdPetIntTipoResp() . '±' . $nome . '±' . $arrMdPetIntRelIntimRespDTO[$i]->getStrTipoRespostaAceitaMdPetIntTipoResp();
        }

        if($retornaXml){
            $xml = '<Documento>';
            $xml .= '<TipoRespostaAceita>'. $tipoRespostaAceita .'</TipoRespostaAceita>';
            $xml .= '<Ids>'. implode("¥", $tipoResposta) .'</Ids>';
            $xml .= '</Documento>';
        }else{
            $xml = $tipoResposta;
        }

        return $xml;
    }

    public static function autoCompletarTipoIntimacao($strPalavrasPesquisa){
        $objMdPetIntTipoIntimacaoDTO = new MdPetIntTipoIntimacaoDTO();
        $objMdPetIntTipoIntimacaoDTO->retNumIdMdPetIntTipoIntimacao();
        $objMdPetIntTipoIntimacaoDTO->retStrNome();

        if (!InfraString::isBolVazia($strPalavrasPesquisa)){

            $strPalavrasPesquisa = InfraString::prepararIndexacao($strPalavrasPesquisa);

            $objMdPetIntTipoIntimacaoDTO->setStrNome('%'.$strPalavrasPesquisa.'%', InfraDTO::$OPER_LIKE);

            $arrPalavrasPesquisa = explode(' ',$strPalavrasPesquisa);
            $numPalavras = count($arrPalavrasPesquisa);
            for($i=0;$i<$numPalavras;$i++){
                $arrPalavrasPesquisa[$i] = '%'.$arrPalavrasPesquisa[$i].'%';
            }

        }

        $objMdPetIntTipoIntimacaoDTO->setNumMaxRegistrosRetorno(50);
        $objMdPetIntTipoIntimacaoDTO->setOrdStrNome(InfraDTO::$TIPO_ORDENACAO_ASC);

        $objMdPetIntTipoIntimacaoRN = new MdPetIntTipoIntimacaoRN();
        $arrObjDTO = $objMdPetIntTipoIntimacaoRN->listar($objMdPetIntTipoIntimacaoDTO);

        return $arrObjDTO;
    }

}

?>