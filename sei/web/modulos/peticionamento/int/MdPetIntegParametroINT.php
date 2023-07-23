<?
/**
* TRIBUNAL REGIONAL FEDERAL DA 4� REGI�O
*
* 25/01/2018 - criado por Usu�rio
*
* Vers�o do Gerador de C�digo: 1.41.0
*/

require_once dirname(__FILE__).'/../../../SEI.php';

class MdPetIntegParametroINT extends InfraINT {

  public static function montarSelectIdMdPetIntegParametro($strPrimeiroItemValor, $strPrimeiroItemDescricao, $strValorItemSelecionado, $numIdMdPetIntegracao=''){
    $objMdPetIntegParametroDTO = new MdPetIntegParametroDTO();
    $objMdPetIntegParametroDTO->retNumIdMdPetIntegParametro();
    $objMdPetIntegParametroDTO->retNumIdMdPetIntegParametro();

    if ($numIdMdPetIntegracao!==''){
      $objMdPetIntegParametroDTO->setNumIdMdPetIntegracao($numIdMdPetIntegracao);
    }

    $objMdPetIntegParametroDTO->setOrdNumIdMdPetIntegParametro(InfraDTO::$TIPO_ORDENACAO_ASC);

    $objMdPetIntegParametroRN = new MdPetIntegParametroRN();
    $arrObjMdPetIntegParametroDTO = $objMdPetIntegParametroRN->listar($objMdPetIntegParametroDTO);

    return parent::montarSelectArrInfraDTO($strPrimeiroItemValor, $strPrimeiroItemDescricao, $strValorItemSelecionado, $arrObjMdPetIntegParametroDTO, 'IdMdPetIntegParametro', 'IdMdPetIntegParametro');
  }
}
