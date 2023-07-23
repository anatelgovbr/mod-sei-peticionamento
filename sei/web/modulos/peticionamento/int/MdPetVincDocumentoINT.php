<?
/**
* TRIBUNAL REGIONAL FEDERAL DA 4� REGI�O
*
* 02/04/2018 - criado por jose vieira
*
* Vers�o do Gerador de C�digo: 1.41.0
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
