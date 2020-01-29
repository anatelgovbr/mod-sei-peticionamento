<?
/**
* ANATEL
*
* 29/04/2016 - criado por alan.campos@castgroup.com.br - CAST
*
*/

require_once dirname(__FILE__).'/../../../SEI.php';

class MdPetExtensoesArquivoINT extends InfraINT {

public static function montarSelectExtensoes($strPrimeiroItemValor, $strPrimeiroItemDescricao, $strValorItemSelecionado, $strSinPrincipal){

    $objMdPetExtensoesArquivoDTO = new MdPetExtensoesArquivoDTO();
    $objMdPetExtensoesArquivoDTO->retNumIdArquivoExtensao();
    $objMdPetExtensoesArquivoDTO->retStrExtensao();
    $objMdPetExtensoesArquivoDTO->setStrSinPrincipal($strSinPrincipal);
    $objMdPetExtensoesArquivoDTO->setOrdStrExtensao(InfraDTO::$TIPO_ORDENACAO_ASC);

    $objMdPetExtensoesArquivoRN = new MdPetExtensoesArquivoRN();

    $arrObjMdPetExtensoesArquivoDTO = $objMdPetExtensoesArquivoRN->listar($objMdPetExtensoesArquivoDTO);

    return parent::montarSelectArrInfraDTO($strPrimeiroItemValor, $strPrimeiroItemDescricao, $strValorItemSelecionado, $arrObjMdPetExtensoesArquivoDTO, 'IdArquivoExtensao', 'Extensao');
  }

  public static function recuperaExtensoes($strPrimeiroItemValor, $strPrimeiroItemDescricao, $strValorItemSelecionado, $strSinPrincipal){
      $objMdPetExtensoesArquivoDTO = new MdPetExtensoesArquivoDTO();
      $objMdPetExtensoesArquivoDTO->retNumIdArquivoExtensao();
      $objMdPetExtensoesArquivoDTO->retStrExtensao();
      $objMdPetExtensoesArquivoDTO->setStrSinPrincipal($strSinPrincipal);
      $objMdPetExtensoesArquivoDTO->setOrdStrExtensao(InfraDTO::$TIPO_ORDENACAO_ASC);

      $objMdPetExtensoesArquivoRN = new MdPetExtensoesArquivoRN();
      $arrObjMdPetExtensoesArquivoDTO = $objMdPetExtensoesArquivoRN->listar($objMdPetExtensoesArquivoDTO);
      $tamanho = count($arrObjMdPetExtensoesArquivoDTO);
      $arrExtPerm = "";
      for($i=0;$i<$tamanho;$i++){
        $nomeExtensao = strtolower($arrObjMdPetExtensoesArquivoDTO[$i]->get('Extensao'));
        if($i<$tamanho-1){
             $arrExtPerm .= "'".$nomeExtensao."'".",";
        }else{
             $arrExtPerm .= "'".$nomeExtensao."'";
        }
      }
      return $arrExtPerm;
  }
  
}
