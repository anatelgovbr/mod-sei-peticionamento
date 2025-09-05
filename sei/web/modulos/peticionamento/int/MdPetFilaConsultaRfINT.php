<?
/**
* TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
*
* 21/06/2023 - criado por michaelr.colab
*
* Versão do Gerador de Código: 1.43.2
*/

require_once dirname(__FILE__).'/../SEI.php';

class MdPetFilaConsultaRfINT extends InfraINT {

  public static function montarSelectCpfCnpj($strPrimeiroItemValor, $strPrimeiroItemDescricao, $strValorItemSelecionado){
    $objMdPetFilaConsultaRfDTO = new MdPetFilaConsultaRfDTO();
    $objMdPetFilaConsultaRfDTO->retNumCpfCnpj();

    $objMdPetFilaConsultaRfDTO->setOrdNumCpfCnpj(InfraDTO::$TIPO_ORDENACAO_ASC);

    $objMdPetFilaConsultaRfRN = new MdPetFilaConsultaRfRN();
    $arrObjMdPetFilaConsultaRfDTO = $objMdPetFilaConsultaRfRN->listar($objMdPetFilaConsultaRfDTO);

    return parent::montarSelectArrInfraDTO($strPrimeiroItemValor, $strPrimeiroItemDescricao, $strValorItemSelecionado, $arrObjMdPetFilaConsultaRfDTO, '', 'CpfCnpj');
  }

  public static function montarSelectStaNatureza($strPrimeiroItemValor, $strPrimeiroItemDescricao, $strValorItemSelecionado){
    $objMdPetFilaConsultaRfRN = new MdPetFilaConsultaRfRN();

    $arrObjNaturezaMdPetFilaConsultaRfDTO = $objMdPetFilaConsultaRfRN->listarValoresNatureza();

    return parent::montarSelectArrInfraDTO($strPrimeiroItemValor, $strPrimeiroItemDescricao, $strValorItemSelecionado, $arrObjNaturezaMdPetFilaConsultaRfDTO, 'StaNatureza', 'Descricao');

  }
}
