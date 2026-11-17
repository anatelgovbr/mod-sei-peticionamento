<?php
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

  /**
   * Devolve o documento de vinculação ou de procuração de cada representante, com null para o representante sem documento.
   *
   * @param int[] $arrIdRepresentante
   * @return array<int, string|null> id do representante => id do documento ou null
   */
  public static function listarIdDocumentoPorRepresentante(array $arrIdRepresentante): array {
    $objMdPetVincDocumentoDTO = new MdPetVincDocumentoDTO();
    $objMdPetVincDocumentoDTO->setNumIdMdPetVinculoRepresent($arrIdRepresentante, InfraDTO::$OPER_IN);
    $objMdPetVincDocumentoDTO->retDblIdDocumento();
    $objMdPetVincDocumentoDTO->retStrProtocoloFormatadoProtocolo();
    $objMdPetVincDocumentoDTO->retNumIdMdPetVinculoRepresent();
    $objMdPetVincDocumentoDTO->adicionarCriterio(
        [
            'TipoDocumento',
            'TipoDocumento',
            'TipoDocumento',
        ],
        [
            InfraDTO::$OPER_IGUAL,
            InfraDTO::$OPER_IGUAL,
            InfraDTO::$OPER_IGUAL,
        ],
        [
            MdPetVincDocumentoRN::$TP_PROTOCOLO_PROCURACAO_ESPECIAL,
            MdPetVincDocumentoRN::$TP_PROTOCOLO_PRINCIPAL,
            MdPetVincDocumentoRN::$TP_PROTOCOLO_PROCURACAO,
        ],
        [
            InfraDTO::$OPER_LOGICO_OR,
            InfraDTO::$OPER_LOGICO_OR,
        ]
    );

    $arrIdDocumento = array_fill_keys($arrIdRepresentante, null);
    foreach ((new MdPetVincDocumentoRN())->listar($objMdPetVincDocumentoDTO) as $objMdPetVincDocumentoItemDTO) {
      $arrIdDocumento[$objMdPetVincDocumentoItemDTO->getNumIdMdPetVinculoRepresent()] = $objMdPetVincDocumentoItemDTO->getDblIdDocumento();
    }

    return $arrIdDocumento;
  }
}
