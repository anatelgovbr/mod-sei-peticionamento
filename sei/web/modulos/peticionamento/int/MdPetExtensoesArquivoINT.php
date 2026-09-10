<?php
/**
* ANATEL
*
* 29/04/2016 - criado por alan.campos@castgroup.com.br - CAST
* 26/08/2024 - Atualização por gabrielg.colab - SPASSU
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

      // Recupera a lista de extensões permitidas pelo core:
      $objArquivoExtensaoDTO = new ArquivoExtensaoDTO();
      $objArquivoExtensaoDTO->retNumIdArquivoExtensao();
      $objArquivoExtensaoDTO->setBolExclusaoLogica(true);
      $objArquivoExtensaoDTO->setStrSinAtivo('S');
      $arrObjArquivoExtensaoDTO = (new ArquivoExtensaoRN())->listar($objArquivoExtensaoDTO);
      $arrArquivoExtensoesCore = InfraArray::converterArrInfraDTO($arrObjArquivoExtensaoDTO, 'IdArquivoExtensao');

      $objMdPetExtensoesArquivoDTO = new MdPetExtensoesArquivoDTO();
      $objMdPetExtensoesArquivoDTO->retNumIdArquivoExtensao();
      $objMdPetExtensoesArquivoDTO->retStrExtensao();
      // Filtra dentre as extensões cadastradas no peticionamento as permitidas pelo core
      $objMdPetExtensoesArquivoDTO->setNumIdArquivoExtensao($arrArquivoExtensoesCore, InfraDTO::$OPER_IN);
      $objMdPetExtensoesArquivoDTO->setStrSinPrincipal($strSinPrincipal);
      $objMdPetExtensoesArquivoDTO->setOrdStrExtensao(InfraDTO::$TIPO_ORDENACAO_ASC);
      $arrObjMdPetExtensoesArquivoDTO = (new MdPetExtensoesArquivoRN())->listar($objMdPetExtensoesArquivoDTO);

      $tamanho = count($arrObjMdPetExtensoesArquivoDTO);
      $arrExtPerm = "";
      
      for($i=0;$i<$tamanho;$i++){

        $nomeExtensao = strtolower($arrObjMdPetExtensoesArquivoDTO[$i]->get('Extensao'));
        $arrExtPerm .= ($i < $tamanho-1) ? "'".$nomeExtensao."'"."," : "'".$nomeExtensao."'";

      }
      
      return $arrExtPerm;

  }
  
}
