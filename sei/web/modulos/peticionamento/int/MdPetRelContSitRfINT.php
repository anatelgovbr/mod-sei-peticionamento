<?
/**
* TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
*
* 21/06/2023 - criado por michaelr.colab
*
* Versão do Gerador de Código: 1.43.2
*/

require_once dirname(__FILE__).'/../SEI.php';

class MdPetRelContSitRfINT extends InfraINT {

  public static function montarSelectIdContato($strPrimeiroItemValor, $strPrimeiroItemDescricao, $strValorItemSelecionado){
    $objMdPetRelContSitRfDTO = new MdPetRelContSitRfDTO();
    $objMdPetRelContSitRfDTO->retNumIdContato();

    $objMdPetRelContSitRfDTO->setOrdNumIdContato(InfraDTO::$TIPO_ORDENACAO_ASC);

    $objMdPetRelContSitRfRN = new MdPetRelContSitRfRN();
    $arrObjMdPetRelContSitRfDTO = $objMdPetRelContSitRfRN->listar($objMdPetRelContSitRfDTO);

    return parent::montarSelectArrInfraDTO($strPrimeiroItemValor, $strPrimeiroItemDescricao, $strValorItemSelecionado, $arrObjMdPetRelContSitRfDTO, '', 'IdContato');
  }
}
