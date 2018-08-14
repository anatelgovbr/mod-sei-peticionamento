<?
/**
 * TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
 *
 * 14/03/2017 - criado por pedro.cast
 *
 * Versão do Gerador de Código: 1.40.0
 */

require_once dirname(__FILE__).'/../../../SEI.php';

class MdPetIntRelTipoRespINT extends InfraINT {

    public static function montarSelectIdMdPetIntRelTipoResp($strPrimeiroItemValor, $strPrimeiroItemDescricao, $strValorItemSelecionado, $numIdMdPetIntimacao='', $numIdMdPetIntTipoResp=''){
        $objMdPetIntRelTipoRespDTO = new MdPetIntRelTipoRespDTO();
        $objMdPetIntRelTipoRespDTO->retNumIdMdPetIntRelTipoResp();
        $objMdPetIntRelTipoRespDTO->retNumIdMdPetIntRelTipoResp();

        if ($numIdMdPetIntimacao!==''){
            $objMdPetIntRelTipoRespDTO->setNumIdMdPetIntimacao($numIdMdPetIntimacao);
        }

        if ($numIdMdPetIntTipoResp!==''){
            $objMdPetIntRelTipoRespDTO->setNumIdMdPetIntTipoResp($numIdMdPetIntTipoResp);
        }

        if ($strValorItemSelecionado!=null){
            $objMdPetIntRelTipoRespDTO->setBolExclusaoLogica(false);
            $objMdPetIntRelTipoRespDTO->adicionarCriterio(array('SinAtivo','IdMdPetIntRelTipoResp'),array(InfraDTO::$OPER_IGUAL,InfraDTO::$OPER_IGUAL),array('S',$strValorItemSelecionado),InfraDTO::$OPER_LOGICO_OR);
        }

        $objMdPetIntRelTipoRespDTO->setOrdNumIdMdPetIntRelTipoResp(InfraDTO::$TIPO_ORDENACAO_ASC);

        $objMdPetIntRelTipoRespRN = new MdPetIntRelTipoRespRN();
        $arrObjMdPetIntRelTipoRespDTO = $objMdPetIntRelTipoRespRN->listar($objMdPetIntRelTipoRespDTO);

        return parent::montarSelectArrInfraDTO($strPrimeiroItemValor, $strPrimeiroItemDescricao, $strValorItemSelecionado, $arrObjMdPetIntRelTipoRespDTO, 'IdMdPetIntRelTipoResp', 'IdMdPetIntRelTipoResp');
    }

    public static function montarSelectTipoResposta($strPrimeiroItemValor, $strPrimeiroItemDescricao, $strValorItemSelecionado, $numIdMdPetIntimacao = '', $idMdPetIntRelDestinatario)
    {
        $objMdPetIntPrazoTipoRespostaRN = new MdPetIntPrazoRN();
        $arrObjMdPetIntRelTipoRespValido = $objMdPetIntPrazoTipoRespostaRN->retornarTipoRespostaValido(array($numIdMdPetIntimacao, $idMdPetIntRelDestinatario));

        return parent::montarSelectArrInfraDTO($strPrimeiroItemValor, $strPrimeiroItemDescricao, $strValorItemSelecionado, $arrObjMdPetIntRelTipoRespValido, 'IdMdPetIntRelTipoResp', 'Nome');

    }

}