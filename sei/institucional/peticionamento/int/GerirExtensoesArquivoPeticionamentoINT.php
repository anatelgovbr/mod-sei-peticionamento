<?
/**
* ANATEL
*
* 29/04/2016 - criado por alan.campos@castgroup.com.br - CAST
*
*/

require_once dirname(__FILE__).'/../../../SEI.php';

class GerirExtensoesArquivoPeticionamentoINT extends InfraINT {

public static function montarSelectExtensoes($strPrimeiroItemValor, $strPrimeiroItemDescricao, $strValorItemSelecionado, $strSinPrincipal){

    $objExtArqPermDTO = new GerirExtensoesArquivoPeticionamentoDTO();
    $objExtArqPermDTO->retNumIdArquivoExtensao();
    $objExtArqPermDTO->retStrExtensao();
    $objExtArqPermDTO->setStrSinPrincipal($strSinPrincipal);
    $objExtArqPermDTO->setOrdStrExtensao(InfraDTO::$TIPO_ORDENACAO_ASC);

    $objExtArqPermRN = new GerirExtensoesArquivoPeticionamentoRN();

    $arrExtArqPermDTO = $objExtArqPermRN->listar($objExtArqPermDTO);

    return parent::montarSelectArrInfraDTO($strPrimeiroItemValor, $strPrimeiroItemDescricao, $strValorItemSelecionado, $arrExtArqPermDTO, 'IdArquivoExtensao', 'Extensao');
  }

  public static function recuperaExtensoes($strPrimeiroItemValor, $strPrimeiroItemDescricao, $strValorItemSelecionado, $strSinPrincipal){
      $objExtArqPermDTO = new GerirExtensoesArquivoPeticionamentoDTO();
      $objExtArqPermDTO->retNumIdArquivoExtensao();
      $objExtArqPermDTO->retStrExtensao();
      $objExtArqPermDTO->setStrSinPrincipal($strSinPrincipal);
      $objExtArqPermDTO->setOrdStrExtensao(InfraDTO::$TIPO_ORDENACAO_ASC);

      $objExtArqPermRN = new GerirExtensoesArquivoPeticionamentoRN();
      $arrExtArqPermDTO = $objExtArqPermRN->listar($objExtArqPermDTO);
      $tamanho = count($arrExtArqPermDTO);
      $arrExtPerm = "";
      for($i=0;$i<$tamanho;$i++){
        if($i<$tamanho-1){
             $arrExtPerm .= "'".$arrExtArqPermDTO[$i]->get('Extensao')."'".",";
        }else{
             $arrExtPerm .= "'".$arrExtArqPermDTO[$i]->get('Extensao')."'";
        }
      }
      return $arrExtPerm;
  }
  
}
