<?
/**
 * TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
 *
 * 14/03/2017 - criado por pedro.cast
 *
 * Versão do Gerador de Código: 1.40.0
 */

require_once dirname(__FILE__).'/../../../SEI.php';

class MdPetIntimacaoINT extends InfraINT {

    public static function validarCadastro($arr){
        $objMdPetIntimacaoRN = new MdPetIntimacaoRN();
        $arrParams  = $objMdPetIntimacaoRN->realizarValidacoesCadastroIntimacao($arr);
        $xml = '<Dados>\n';
        $xml .= '<Mensagem>' .$arrParams[0]. '</Mensagem>';
        $xml .= '<Impeditivo>' .($arrParams[1] ? 'S' : 'N'). '</Impeditivo>';
        $xml .= '<Alerta>' .($arrParams[2] ? 'S' : 'N'). '</Alerta>';
        $xml .= '</Dados>';
        
        return $xml;
    }


    public static function montarSelectIdMdPetIntimacao($strPrimeiroItemValor, $strPrimeiroItemDescricao, $strValorItemSelecionado, $numIdMdPetIntTipoIntimacao=''){
        $objMdPetIntimacaoDTO = new MdPetIntimacaoDTO();
        $objMdPetIntimacaoDTO->retNumIdMdPetIntimacao();
        $objMdPetIntimacaoDTO->retNumIdMdPetIntimacao();

        if ($numIdMdPetIntTipoIntimacao!==''){
            $objMdPetIntimacaoDTO->setNumIdMdPetIntTipoIntimacao($numIdMdPetIntTipoIntimacao);
        }

        $objMdPetIntimacaoDTO->setOrdNumIdMdPetIntimacao(InfraDTO::$TIPO_ORDENACAO_ASC);

        $objMdPetIntimacaoRN = new MdPetIntimacaoRN();
        $arrObjMdPetIntimacaoDTO = $objMdPetIntimacaoRN->listar($objMdPetIntimacaoDTO);

        return parent::montarSelectArrInfraDTO($strPrimeiroItemValor, $strPrimeiroItemDescricao, $strValorItemSelecionado, $arrObjMdPetIntimacaoDTO, 'IdMdPetIntimacao', 'IdMdPetIntimacao');
    }

    public static function montarSelectSituacaoIntimacao($selected = ''){
        $objMdPetIntAceiteRN = new MdPetIntAceiteRN();
        $arr = $objMdPetIntAceiteRN->retornaArraySituacaoIntimacao();
        asort($arr);

        $select =  '<option value=""> </option>' ;

        if (count($arr) > 0) 
        {
            foreach ($arr as $key => $item) 
            {
                $add = '';
                if($selected != '' && $key == $selected)
                {
                    $add = 'selected = selected';
                }
                
                $select .= '<option '.$add.' value="' . $key . '">' . $item . '</option>';
            }
        }

        return $select;

    }

    public static function getSituacoesListaExterno()
    {
        $strReturn         = '<option value="">  </option>';
        $strOption         = '<option value="[valor]"> [descricaoSituacao] </option>';
        $strOptionSel      = '<option selected="selected" value="[valor]"> [descricaoSituacao] </option>';
        $arrSituacoes      =  MdPetIntRelDestinatarioINT::getArraySituacaoRelatorio();
        foreach($arrSituacoes as $key => $situacao)
        {
            if($key != MdPetIntimacaoRN::$INTIMACAO_PRAZO_VENCIDO) {
                //Verifica se no post temos id de situação para aplicar no filtro
                $hdnSituacao = array_key_exists('selCumprimentoIntimacao', $_POST) ? $_POST['selCumprimentoIntimacao'] : null;

                //Verifica se esse valor deve ser selecionado
                if (count($hdnSituacao != '')) {
                    $strOptionCorreta = $key == $hdnSituacao ? $strOptionSel : $strOption;
                } else {
                    $strOptionCorreta = $key == MdPetIntimacaoRN::$TODAS ? $strOptionSel : $strOption;
                }

                $add = str_replace('[valor]', $key, $strOptionCorreta);
                $add = str_replace('[descricaoSituacao]', $situacao, $add);
                $strReturn .= '' . $add;
            }
        }

        return $strReturn;
    }
}
?>