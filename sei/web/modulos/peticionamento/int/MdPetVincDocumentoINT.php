<?
/**
* TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
*
* 02/04/2018 - criado por jose vieira
* 26/08/2024 - Atualização por gabrielg.colab - SPASSU
*
* Versão do Gerador de Código: 1.41.0
*/

require_once dirname(__FILE__) . '/../../../SEI.php';

class MdPetVincDocumentoINT extends InfraINT {

  public static function montarSelectIdMdPetVincDocumento($strPrimeiroItemValor, $strPrimeiroItemDescricao, $strValorItemSelecionado){
    $objMdPetVincDocumentoDTO = new MdPetVincDocumentoDTO();
    $objMdPetVincDocumentoDTO->retNumIdMdPetVincDocumento();

    $objMdPetVincDocumentoDTO->setOrdNumIdMdPetVincDocumento(InfraDTO::$TIPO_ORDENACAO_ASC);

    $objMdPetVincDocumentoRN = new MdPetVincDocumentoRN();
    $arrObjMdPetVincDocumentoDTO = $objMdPetVincDocumentoRN->listar($objMdPetVincDocumentoDTO);

    return parent::montarSelectArrInfraDTO($strPrimeiroItemValor, $strPrimeiroItemDescricao, $strValorItemSelecionado, $arrObjMdPetVincDocumentoDTO, '', 'IdMdPetVincDocumento');
  }
}
