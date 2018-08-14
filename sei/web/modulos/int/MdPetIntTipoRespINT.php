<?
/**
 * TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
 *
 * 08/12/2016 - criado por Marcelo Bezerra - CAST
 *
 * Versão do Gerador de Código: 1.39.0
 */

require_once dirname(__FILE__) . '/../../../SEI.php';

class MdPetIntTipoRespINT extends InfraINT
{

    public static function montarSelectIdMdPetIntTipoResp($strPrimeiroItemValor, $strPrimeiroItemDescricao, $strValorItemSelecionado)
    {
        $objMdPetIntTipoRespDTO = new MdPetIntTipoRespDTO();
        $objMdPetIntTipoRespDTO->retNumIdMdPetIntTipoResp();
        $objMdPetIntTipoRespDTO->retNumIdMdPetIntTipoResp();

        if ($strValorItemSelecionado != null) {
            $objMdPetIntTipoRespDTO->setBolExclusaoLogica(false);
            $objMdPetIntTipoRespDTO->adicionarCriterio(array('SinAtivo', 'IdMdPetIntTipoResp'), array(InfraDTO::$OPER_IGUAL, InfraDTO::$OPER_IGUAL), array('S', $strValorItemSelecionado), InfraDTO::$OPER_LOGICO_OR);
        }

        $objMdPetIntTipoRespDTO->setOrdNumIdMdPetIntTipoResp(InfraDTO::$TIPO_ORDENACAO_ASC);

        $objMdPetIntTipoRespRN = new MdPetIntTipoRespRN();
        $arrObjMdPetIntTipoRespDTO = $objMdPetIntTipoRespRN->listar($objMdPetIntTipoRespDTO);

        return parent::montarSelectArrInfraDTO($strPrimeiroItemValor, $strPrimeiroItemDescricao, $strValorItemSelecionado, $arrObjMdPetIntTipoRespDTO, 'IdMdPetIntTipoResp', 'IdMdPetIntTipoResp');
    }

    public static function autoCompletarTiposResposta($strPalavrasPesquisa)
    {

        $objMdPetIntTipoRespDTO = new MdPetIntTipoRespDTO();
        $objMdPetIntTipoRespDTO->retStrNome();
        $objMdPetIntTipoRespDTO->retStrTipoPrazoExterno();
        $objMdPetIntTipoRespDTO->retNumValorPrazoExterno();
        $objMdPetIntTipoRespDTO->retStrTipoRespostaAceita();
        $objMdPetIntTipoRespDTO->retNumIdMdPetIntTipoResp();
        $objMdPetIntTipoRespDTO->setNumMaxRegistrosRetorno(50);
        $objMdPetIntTipoRespDTO->setOrdStrNome(InfraDTO::$TIPO_ORDENACAO_ASC);

        $objMdPetIntTipoRespDTO->setStrNome('%' . $strPalavrasPesquisa . '%', InfraDTO::$OPER_LIKE);

        $MdPetIntTipoRespRN = new MdPetIntTipoRespRN();
        $objMdPetIntTipoRespDTO = $MdPetIntTipoRespRN->listar($objMdPetIntTipoRespDTO);

        foreach ($objMdPetIntTipoRespDTO as $key => $objMdPetIntTipoRespDTO2) {
            $objMdPetIntTipoRespDTO[$key]->setStrNome(MdPetIntTipoRespINT::montaAjaxTipoResposta($objMdPetIntTipoRespDTO2));
        }

        return $objMdPetIntTipoRespDTO;
    }

    public static function montaAjaxTipoResposta($objMdPetIntTipoRespDTO)
    {
        if ($objMdPetIntTipoRespDTO->getStrTipoPrazoExterno() == 'N') {
            $prazo = '(Não Possui Prazo Externo)';
        } else {
            $prazo = '(' . $objMdPetIntTipoRespDTO->getNumValorPrazoExterno();

            if ($objMdPetIntTipoRespDTO->getStrTipoPrazoExterno() == 'D') {
                $tipoDia = null;
                if($objMdPetIntTipoRespDTO->getStrTipoDia() == 'U'){
                  $tipoDia = ' Útil';
                  if($objMdPetIntTipoRespDTO->getNumValorPrazoExterno() > 1){
                    $tipoDia = ' Úteis';
                  }
                }

                $prazo .= $objMdPetIntTipoRespDTO->getNumValorPrazoExterno() > 1 ? ' Dias'.$tipoDia.')' : ' Dia'.$tipoDia.')';
            } else if ($objMdPetIntTipoRespDTO->getStrTipoPrazoExterno() == 'M') {
                $prazo .= $objMdPetIntTipoRespDTO->getNumValorPrazoExterno() > 1 ? ' Meses)' : ' Mês)';
            } else if ($objMdPetIntTipoRespDTO->getStrTipoPrazoExterno() == 'A') {
                $prazo .= $objMdPetIntTipoRespDTO->getNumValorPrazoExterno() > 1 ? ' Anos)' : ' Ano)';
            }
        }

        if ($objMdPetIntTipoRespDTO->getStrTipoRespostaAceita() == 'E') {
            $resposta = 'Exige Resposta';
        } else {
            $resposta = 'Resposta Facultativa';
        }

        return $objMdPetIntTipoRespDTO->getStrNome() . ' ' . $prazo . ' - ' . $resposta;

    }

    public static function montaSelectRespostaUsuario8612($strPrimeiroItemValor, $strPrimeiroItemDescricao, $strValorItemSelecionado)
    {
        $montaSelect = "<option value=\"$strPrimeiroItemValor\">$strPrimeiroItemDescricao</option>";

        if ($strValorItemSelecionado === 'F') {
            $facultativo = " selected=\"selected\" ";
        }else if ($strValorItemSelecionado === 'E') {
            $exige = " selected=\"selected\" ";
        }
        $montaSelect .= "<option value=\"F\" ". $facultativo . ">Resposta Facultativa</option>";
        $montaSelect .= "<option value=\"E\" ". $exige . ">Exige Resposta</option>";

        return $montaSelect;
    }

    public static function montaSelectPrazoExterno8612($strPrimeiroItemValor, $strPrimeiroItemDescricao, $strValorItemSelecionado)
    {
        $objMdPetIntTipoRespDTO = new MdPetIntTipoRespDTO();
        $objMdPetIntTipoRespDTO->retTodos(true);
        $objMdPetIntTipoRespDTO->setOrdStrTipoPrazoExterno(InfraDTO::$TIPO_ORDENACAO_ASC);
        $objMdPetIntTipoRespDTO->setOrdNumValorPrazoExterno(InfraDTO::$TIPO_ORDENACAO_ASC);

        $objUnidadeRN = new MdPetIntTipoRespRN();
        $arrObjUnidadeDTO = $objUnidadeRN->listar($objMdPetIntTipoRespDTO);

        $montaSelect = "<option value=\"$strPrimeiroItemValor\">$strPrimeiroItemDescricao</option>";

        if(count($arrObjUnidadeDTO) > 0){
            for($i = 0; $i < count($arrObjUnidadeDTO); $i++){
                if($arrObjUnidadeDTO[$i]->getStrTipoPrazoExterno() == 'N'){
                        $value['0-N'] = "Não Possui Prazo Externo";
                }else{
                    $chave = $arrObjUnidadeDTO[$i]->getNumValorPrazoExterno() . '-' . $arrObjUnidadeDTO[$i]->getStrTipoPrazoExterno();
                     if($arrObjUnidadeDTO[$i]->getStrTipoPrazoExterno() == 'D'){
                        $tipoDia = '';
                        if($arrObjUnidadeDTO[$i]->getStrTipoDia() == 'U'){
                            $tipoDia = ' Útil';
                            if($arrObjUnidadeDTO[$i]->getNumValorPrazoExterno() > 1){
                                $tipoDia = ' Úteis';
                            }
                        }
                        $chave .= '-' . $arrObjUnidadeDTO[$i]->getStrTipoDia();
                        $value[$chave] = $arrObjUnidadeDTO[$i]->getNumValorPrazoExterno();
                        $value[$chave] .= $arrObjUnidadeDTO[$i]->getNumValorPrazoExterno() > 1 ? ' Dias'.$tipoDia : ' Dia'.$tipoDia;
                    }else if($arrObjUnidadeDTO[$i]->getStrTipoPrazoExterno() == 'M'){
                        $value[$chave] = $arrObjUnidadeDTO[$i]->getNumValorPrazoExterno();
                        $value[$chave] .= $arrObjUnidadeDTO[$i]->getNumValorPrazoExterno() > 1 ? ' Meses' : ' Mês';
                    }else if($arrObjUnidadeDTO[$i]->getStrTipoPrazoExterno() == 'A'){
                        $value[$chave] = $arrObjUnidadeDTO[$i]->getNumValorPrazoExterno();
                        $value[$chave] .= $arrObjUnidadeDTO[$i]->getNumValorPrazoExterno() > 1 ? ' Anos' : ' Ano';
                    }
                }
            }

            foreach($value as $key => $label){
                $montaSelect .= '<option value="'.$key.'"';
                if($key == $strValorItemSelecionado){
                    $montaSelect .= ' selected="selected"';
                }
                $montaSelect .= '>'.InfraString::formatarXML($label/*utf8_decode($label)*/).'</option>';
            }
      }

    return $montaSelect;
    }

    public static function montarSelectTipoRespostaEU8612($tipoRespostaUsuario){
        $objMdPetIntTipoRespDTO = new MdPetIntTipoRespDTO();
        $objMdPetIntTipoRespDTO->retTodos();
        $objMdPetIntTipoRespDTO->setOrdStrNome(InfraDTO::$TIPO_ORDENACAO_ASC);

        $objMdPetIntTipoRespDTO->setStrTipoRespostaAceita($tipoRespostaUsuario);

        $objUnidadeRN = new MdPetIntTipoRespRN();
        $arrObjUnidadeDTO = $objUnidadeRN->listar($objMdPetIntTipoRespDTO);

        foreach ($arrObjUnidadeDTO as $key => $objMdPetIntTipoRespDTO2) {
            $arrObjUnidadeDTO[$key]->setStrNome(MdPetIntTipoRespINT::montaAjaxTipoResposta($objMdPetIntTipoRespDTO2));
        }

        return parent::montarSelectArrInfraDTO('0', ' ', null, $arrObjUnidadeDTO, 'IdMdPetIntTipoResp', 'Nome');
    }

    public static function buscaTipoResposta($id){
        $objMdPetIntRelTpRespRN = new MdPetIntRelTipoRespRN();
        $isVinculado = $objMdPetIntRelTpRespRN->validarExclusaoTipoResposta($id);
        $objMdPetIntTipoRespDTO = new MdPetIntTipoRespDTO();
        $objMdPetIntTipoRespDTO->retTodos();
        $objMdPetIntTipoRespDTO->setNumIdMdPetIntTipoResp($id);

        $objMdPetIntTipoRespRN = new MdPetIntTipoRespRN();
        $arrMdPetIntTipoRespDTO = $objMdPetIntTipoRespRN->consultar($objMdPetIntTipoRespDTO);

        if ($arrMdPetIntTipoRespDTO->getStrTipoPrazoExterno() == 'N') {
            $prazo = '(Não Possui Prazo Externo)';
        } else{
            $prazo = '(' . $arrMdPetIntTipoRespDTO->getNumValorPrazoExterno();
            if ($arrMdPetIntTipoRespDTO->getStrTipoPrazoExterno() == 'D') {
                $tipoDia = null;
                if($arrMdPetIntTipoRespDTO->getStrTipoDia() == 'U'){
                  $tipoDia = 'Útil';
                  if($arrMdPetIntTipoRespDTO->getNumValorPrazoExterno() > 1){
                    $tipoDia = 'Úteis';
                  }
                }
                $prazo .= $arrMdPetIntTipoRespDTO->getNumValorPrazoExterno() > 1 ? ' Dias '.$tipoDia.')' : ' Dia '.$tipoDia.')';
            } else if ($arrMdPetIntTipoRespDTO->getStrTipoPrazoExterno() == 'M') {
                $prazo .= $arrMdPetIntTipoRespDTO->getNumValorPrazoExterno() > 1 ? ' Meses)' : ' Mês)';
            } else if ($arrMdPetIntTipoRespDTO->getStrTipoPrazoExterno() == 'A') {
                $prazo .= $arrMdPetIntTipoRespDTO->getNumValorPrazoExterno() > 1 ? ' Anos)' : ' Ano)';
            }
        }


        if ($arrMdPetIntTipoRespDTO->getStrTipoRespostaAceita() == 'E') {
            $resposta = 'Exige Resposta';
        } else {
            $resposta = 'Resposta Facultativa';
        }

        $xml = '<Documento>';
        $xml .= '<Id>'. $arrMdPetIntTipoRespDTO->getNumIdMdPetIntTipoResp() .'</Id>';
        $xml .= '<Nome>'. $arrMdPetIntTipoRespDTO->getStrNome() .'</Nome>';
        $xml .= '<Prazo>'. utf8_decode($prazo) .'</Prazo>';
        $xml .= '<Tipo>'. $resposta .'</Tipo>';
        $xml .= '<Vinculado>'.$isVinculado.'</Vinculado>';
        $xml .= '</Documento>';

        return $xml;

    }

    public function montaCheckboxTipoResposta() {
        $objMdPetIntTipoRespDTO = new MdPetIntTipoRespDTO();
        $objMdPetIntTipoRespDTO->retTodos();
        $objMdPetIntTipoRespDTO->setOrdStrNome(InfraDTO::$TIPO_ORDENACAO_ASC);

        $objUnidadeRN = new MdPetIntTipoRespRN();
        $arrObjUnidadeDTO = $objUnidadeRN->listar($objMdPetIntTipoRespDTO);

        $tipoResposta = '';

        if(count($arrObjUnidadeDTO)){
            for($i = 0; $i < count($arrObjUnidadeDTO); $i++){
                if ($arrObjUnidadeDTO[$i]->getStrTipoPrazoExterno() == 'N') {
                    $prazo = '(Não Possui Prazo Externo)';
                } else if ($arrObjUnidadeDTO[$i]->getStrTipoPrazoExterno() == 'D') {
                    $prazo = '(' . $arrObjUnidadeDTO[$i]->getNumValorPrazoExterno() . ' Dias)';
                } else if ($arrObjUnidadeDTO[$i]->getStrTipoPrazoExterno() == 'M') {
                    $prazo = '(' . $arrObjUnidadeDTO[$i]->getNumValorPrazoExterno() . ' Meses)';
                } else if ($arrObjUnidadeDTO[$i]->getStrTipoPrazoExterno() == 'A') {
                    $prazo = '(' . $arrObjUnidadeDTO[$i]->getNumValorPrazoExterno() . ' Anos)';
                }

                if ($arrObjUnidadeDTO[$i]->getStrTipoRespostaAceita() == 'E') {
                    $resposta = 'Exige Resposta';
                } else {
                    $resposta = 'Resposta Facultativa';
                }

                $id = 'ck_'.$arrObjUnidadeDTO[$i]->getNumIdMdPetIntTipoResp();
                $nome = utf8_decode($arrObjUnidadeDTO[$i]->getStrNome() . ' ' . $prazo . ' - ' . $resposta);
                $tipoResposta .= '<label class="infraLabelRadio" id="'.$id.'"><input type="checkbox" id="chkTipoResposta" value="'.$arrObjUnidadeDTO[$i]->getNumIdMdPetIntTipoResp().'" class="infraRadio" name="chkTipoResposta[]">'. $nome .'</label>';
            }
        }

        return $tipoResposta;

    }

}

?>