<?
/**
* TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
*
* 02/04/2018 - criado por jose vieira
*
* Versão do Gerador de Código: 1.41.0
*/

require_once dirname(__FILE__) . '/../../../SEI.php';

class MdPetVincRepresentantINT extends InfraINT {

  public static function montarSelectIdMdPetVinculoRepresent($strPrimeiroItemValor, $strPrimeiroItemDescricao, $strValorItemSelecionado){
    $objMdPetVincRepresentantDTO = new MdPetVincRepresentantDTO();
    $objMdPetVincRepresentantDTO->retNumIdMdPetVinculoRepresent();

    if ($strValorItemSelecionado!=null){
      $objMdPetVincRepresentantDTO->setBolExclusaoLogica(false);
      $objMdPetVincRepresentantDTO->adicionarCriterio(array('SinAtivo',''),array(InfraDTO::$OPER_IGUAL,InfraDTO::$OPER_IGUAL),array('S',$strValorItemSelecionado),InfraDTO::$OPER_LOGICO_OR);
    }

    $objMdPetVincRepresentantDTO->setOrdNumIdMdPetVinculoRepresent(InfraDTO::$TIPO_ORDENACAO_ASC);

    $objMdPetVincRepresentantRN = new MdPetVincRepresentantRN();
    $arrObjMdPetVincRepresentantDTO = $objMdPetVincRepresentantRN->listar($objMdPetVincRepresentantDTO);

    return parent::montarSelectArrInfraDTO($strPrimeiroItemValor, $strPrimeiroItemDescricao, $strValorItemSelecionado, $arrObjMdPetVincRepresentantDTO, '', 'IdMdPetVinculoRepresent');
  }

  public static function montarSelectStaEstado($strPrimeiroItemValor, $strPrimeiroItemDescricao, $strValorItemSelecionado){
    $objMdPetVincRepresentantRN = new MdPetVincRepresentantRN();

    $arrObjEstadoMdPetVincRepresentantDTO = $objMdPetVincRepresentantRN->listarValoresEstado();

    return parent::montarSelectArrInfraDTO($strPrimeiroItemValor, $strPrimeiroItemDescricao, $strValorItemSelecionado, $arrObjEstadoMdPetVincRepresentantDTO, 'StaEstado', 'Descricao');

  }
   public static function montarSelectOutorgante($strPrimeiroItemValor, $strPrimeiroItemDescricao, $strValorItemSelecionado){

       $idUsuarioExterno = SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno();
       $usuarioDTO = new UsuarioDTO();
       $usuarioRN = new UsuarioRN();
       $usuarioDTO->retNumIdContato();

       $usuarioDTO->setNumIdUsuario($idUsuarioExterno);
       $contatoExterno = $usuarioRN->consultarRN0489($usuarioDTO);

       $idContatoExterno = $contatoExterno->getNumIdContato();

       $objMdPetVincRepresentantRN = new MdPetVincRepresentantRN();
       $objMdPetVincRepresentantDTO = new MdPetVincRepresentantDTO();
       $objMdPetVincRepresentantDTO->retStrRazaoSocialNomeVinc();
       $objMdPetVincRepresentantDTO->retStrCNPJ();
       $objMdPetVincRepresentantDTO->retNumIdContatoVinc();
       $objMdPetVincRepresentantDTO->setDistinct(true);
       $objMdPetVincRepresentantDTO->adicionarCriterio(array('IdContato','IdContatoOutorg'),
           array(InfraDTO::$OPER_IGUAL,InfraDTO::$OPER_IGUAL),
           array($idContatoExterno,$idContatoExterno),
           array(InfraDTO::$OPER_LOGICO_OR));
       $objMdPetVincRepresentantDTO->setStrStaEstado(MdPetVincRepresentantRN::$RP_ATIVO);

       $arrObjEstadoMdPetVincRepresentantDTO = $objMdPetVincRepresentantRN->listar($objMdPetVincRepresentantDTO);

        foreach ($arrObjEstadoMdPetVincRepresentantDTO as $key => $objEstadoMdPetVincRepresentantDTO2) {
            $descricao = InfraUtil::formatarCnpj($objEstadoMdPetVincRepresentantDTO2->getStrCNPJ()).' - '.$objEstadoMdPetVincRepresentantDTO2->getStrRazaoSocialNomeVinc();
            $arrObjEstadoMdPetVincRepresentantDTO[$key]->setStrRazaoSocialNomeVinc($descricao);
        }

       return parent::montarSelectArrInfraDTO($strPrimeiroItemValor, $strPrimeiroItemDescricao, $strValorItemSelecionado, $arrObjEstadoMdPetVincRepresentantDTO, 'IdContatoVinc', 'RazaoSocialNomeVinc');
   }

   public static function validarUsuarioResponsavelLegal($dados){


       $objMdPetVincRepresentantDTO = new MdPetVincRepresentantDTO();
       $objMdPetVincRepresentantDTO->setNumIdContatoVinc($dados['selPessoaJuridica']);
       $objMdPetVincRepresentantDTO->setNumIdContato($dados['hdnIdContExterno']);
       $objMdPetVincRepresentantDTO->setStrTipoRepresentante(MdPetVincRepresentantRN::$PE_RESPONSAVEL_LEGAL);
       $objMdPetVincRepresentantDTO->retNumIdMdPetVinculo();

       $objMdPetVincRepresentantRN = new MdPetVincRepresentantRN();
       $numRegistros = $objMdPetVincRepresentantRN->contar($objMdPetVincRepresentantDTO);

       $xml  = '<dados>';
       $xml .= '<resultado>'.$numRegistros.'</resultado>';
       $xml .= '</dados>';

       return $xml;

   }

  public static function montarSelectTipoVinculoGrid($strPrimeiroItemValor, $strPrimeiroItemDescricao, $strValorItemSelecionado, $arrObjMdPetVincRepresentantDTO){
  
      $arrObjMdPetVincRepresentantDTO = InfraArray::distinctArrInfraDTO($arrObjMdPetVincRepresentantDTO, 'TipoRepresentante');

      return parent::montarSelectArrInfraDTO($strPrimeiroItemValor, $strPrimeiroItemDescricao, $strValorItemSelecionado, $arrObjMdPetVincRepresentantDTO, 'TipoRepresentante', 'NomeTipoRepresentante');
  }

}
