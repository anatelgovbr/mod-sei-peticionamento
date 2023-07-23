<?
/**
* TRIBUNAL REGIONAL FEDERAL DA 4� REGI�O
*
* 22/03/2017 - criado por jaqueline.cast
*
* Vers�o do Gerador de C�digo: 1.40.0
*/

require_once dirname(__FILE__) . '/../../../SEI.php';

class MdPetIntDestRespostaINT extends InfraINT {

  public static function montarSelectIdMdPetIntDestResposta($strPrimeiroItemValor, $strPrimeiroItemDescricao, $strValorItemSelecionado){
    $objMdPetIntDestRespostaDTO = new MdPetIntDestRespostaDTO();
    $objMdPetIntDestRespostaDTO->retNumIdMdPetIntDestResposta();

    $objMdPetIntDestRespostaDTO->setOrdNumIdMdPetIntDestResposta(InfraDTO::$TIPO_ORDENACAO_ASC);

    $objMdPetIntDestRespostaRN = new MdPetIntDestRespostaRN();
    $arrObjMdPetIntDestRespostaDTO = $objMdPetIntDestRespostaRN->listar($objMdPetIntDestRespostaDTO);

    return parent::montarSelectArrInfraDTO($strPrimeiroItemValor, $strPrimeiroItemDescricao, $strValorItemSelecionado, $arrObjMdPetIntDestRespostaDTO, '', 'IdMdPetIntDestResposta');
  }
}
?>