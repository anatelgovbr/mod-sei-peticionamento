<?
/**
* TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
*
* 25/01/2018 - criado por Usuário
*
* Versão do Gerador de Código: 1.41.0
*/

require_once dirname(__FILE__).'/../../../SEI.php';

class MdPetIntegFuncionalidINT extends InfraINT {

  public static function montarSelectNome($strPrimeiroItemValor, $strPrimeiroItemDescricao, $strValorItemSelecionado){
    $objMdPetIntegFuncionalidDTO = new MdPetIntegFuncionalidDTO();
    $objMdPetIntegFuncionalidDTO->retNumIdMdPetIntegFuncionalid();
    $objMdPetIntegFuncionalidDTO->retStrNome();

    $objMdPetIntegFuncionalidDTO->setOrdStrNome(InfraDTO::$TIPO_ORDENACAO_ASC);

    $objMdPetIntegFuncionalidRN = new MdPetIntegFuncionalidRN();
    $arrObjMdPetIntegFuncionalidDTO = $objMdPetIntegFuncionalidRN->listar($objMdPetIntegFuncionalidDTO);

    return parent::montarSelectArrInfraDTO($strPrimeiroItemValor, $strPrimeiroItemDescricao, $strValorItemSelecionado, $arrObjMdPetIntegFuncionalidDTO, 'IdMdPetIntegFuncionalid', 'Nome');
  }

  public static function montarSelectNomeNaoUtilizado($strPrimeiroItemValor, $strPrimeiroItemDescricao, $strValorItemSelecionado, $numIdMdPetIntegracao=null){
    $objMdPetIntegFuncionalidRN = new MdPetIntegFuncionalidRN();
    $arrIdMdPetIntegFuncionalidUtilizado = $objMdPetIntegFuncionalidRN->verificarMdPetIntegFuncionalidUtilizado($numIdMdPetIntegracao);

    $objMdPetIntegFuncionalidDTO = new MdPetIntegFuncionalidDTO();
    $objMdPetIntegFuncionalidDTO->retNumIdMdPetIntegFuncionalid();
    $objMdPetIntegFuncionalidDTO->retStrNome();
    if (count($arrIdMdPetIntegFuncionalidUtilizado)>0){
      $objMdPetIntegFuncionalidDTO->setNumIdMdPetIntegFuncionalid( $arrIdMdPetIntegFuncionalidUtilizado, InfraDTO::$OPER_NOT_IN );    
    }

    $objMdPetIntegFuncionalidDTO->setOrdStrNome(InfraDTO::$TIPO_ORDENACAO_ASC);

    $arrObjMdPetIntegFuncionalidDTO = $objMdPetIntegFuncionalidRN->listar($objMdPetIntegFuncionalidDTO);

    return parent::montarSelectArrInfraDTO($strPrimeiroItemValor, $strPrimeiroItemDescricao, $strValorItemSelecionado, $arrObjMdPetIntegFuncionalidDTO, 'IdMdPetIntegFuncionalid', 'Nome');
  }

  
}

