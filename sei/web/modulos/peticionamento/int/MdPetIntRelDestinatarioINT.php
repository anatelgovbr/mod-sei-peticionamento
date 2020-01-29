<?
/**
 * TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
 *
 * 14/03/2017 - criado por pedro.cast
 *
 * Versão do Gerador de Código: 1.40.0
 */

require_once dirname(__FILE__).'/../../../SEI.php';

class MdPetIntRelDestinatarioINT extends InfraINT {

    public static function montarSelectIdMdPetIntRelDestinatario($strPrimeiroItemValor, $strPrimeiroItemDescricao, $strValorItemSelecionado, $numIdMdPetIntimacao='', $numIdContato=''){
        $objMdPetIntRelDestinatarioDTO = new MdPetIntRelDestinatarioDTO();
        $objMdPetIntRelDestinatarioDTO->retNumIdMdPetIntRelDestinatario();
        $objMdPetIntRelDestinatarioDTO->retNumIdMdPetIntRelDestinatario();

        if ($numIdMdPetIntimacao!==''){
            $objMdPetIntRelDestinatarioDTO->setNumIdMdPetIntimacao($numIdMdPetIntimacao);
        }

        if ($numIdContato!==''){
            $objMdPetIntRelDestinatarioDTO->setNumIdContato($numIdContato);
        }

        if ($strValorItemSelecionado!=null){
            $objMdPetIntRelDestinatarioDTO->setBolExclusaoLogica(false);
            $objMdPetIntRelDestinatarioDTO->adicionarCriterio(array('SinAtivo','IdMdPetIntRelDestinatario'),array(InfraDTO::$OPER_IGUAL,InfraDTO::$OPER_IGUAL),array('S',$strValorItemSelecionado),InfraDTO::$OPER_LOGICO_OR);
        }

        $objMdPetIntRelDestinatarioDTO->setOrdNumIdMdPetIntRelDestinatario(InfraDTO::$TIPO_ORDENACAO_ASC);

        $objMdPetIntRelDestinatarioRN = new MdPetIntRelDestinatarioRN();
        $arrObjMdPetIntRelDestinatarioDTO = $objMdPetIntRelDestinatarioRN->listar($objMdPetIntRelDestinatarioDTO);

        return parent::montarSelectArrInfraDTO($strPrimeiroItemValor, $strPrimeiroItemDescricao, $strValorItemSelecionado, $arrObjMdPetIntRelDestinatarioDTO, 'IdMdPetIntRelDestinatario', 'IdMdPetIntRelDestinatario');
    }

    public static function montarSelectRazaoSocial($strPrimeiroItemValor, $strPrimeiroItemDescricao, $strValorItemSelecionado, $numIdMdPetIntimacao = '', $idMdPetIntRelDestinatario,$idDocumento,$idAceite)
    {
            $objMdPetIntRelDestinatarioDTO = new MdPetIntRelDestinatarioDTO();
			//$objMdPetIntRelDestinatarioDTO->setNumIdMdPetIntimacao($numIdMdPetIntimacao);
            $objMdPetIntRelDestinatarioDTO->retNumIdMdPetIntRelDestinatario();
            $objMdPetIntRelDestinatarioDTO->retNumIdMdPetIntimacao();
            $objMdPetIntRelDestinatarioDTO->setDblIdProtocolo($idDocumento);
            $objMdPetIntRelDestinatarioDTO->setNumIdMdPetAceite($idAceite,InfraDTO::$OPER_IN);
            $objMdPetIntRelDestinatarioDTO->retNumIdContato();
            $objMdPetIntRelDestinatarioDTO->retStrSinPessoaJuridica();
            $objMdPetIntRelDestinatarioDTO->retDblCnpjContato();
            $objMdPetIntRelDestinatarioDTO->retDblCpfContato();
            $objMdPetIntRelDestinatarioDTO->retStrEmailContato();
            $objMdPetIntRelDestinatarioDTO->retStrNomeContato();
			$objMdPetIntRelDestinatarioRN  = new MdPetIntRelDestinatarioRN();
			$objMdPetIntRelDestinatarioDTO = $objMdPetIntRelDestinatarioRN->listar($objMdPetIntRelDestinatarioDTO);
            

        return parent::montarSelectArrInfraDTO($strPrimeiroItemValor, $strPrimeiroItemDescricao, $strValorItemSelecionado, $objMdPetIntRelDestinatarioDTO, 'IdMdPetIntRelDestinatario', 'NomeEmailCnpjCpf');

    }

    public static function getArraySituacaoRelatorio(){
        $arrSituacao = array();

        $arrSituacao[MdPetIntimacaoRN::$INTIMACAO_PENDENTE]            = MdPetIntimacaoRN::$STR_INTIMACAO_PENDENTE;
        $arrSituacao[MdPetIntimacaoRN::$INTIMACAO_CUMPRIDA_POR_ACESSO] = MdPetIntimacaoRN::$STR_INTIMACAO_CUMPRIDA_POR_ACESSO;
        $arrSituacao[MdPetIntimacaoRN::$INTIMACAO_CUMPRIDA_PRAZO]      = MdPetIntimacaoRN::$STR_INTIMACAO_CUMPRIDA_PRAZO;
        $arrSituacao[MdPetIntimacaoRN::$INTIMACAO_RESPONDIDA]          = MdPetIntimacaoRN::$STR_INTIMACAO_RESPONDIDA;
        $arrSituacao[MdPetIntimacaoRN::$INTIMACAO_PRAZO_VENCIDO]       = MdPetIntimacaoRN::$STR_INTIMACAO_PRAZO_VENCIDO;

        return $arrSituacao;
    }

    public function consultarIntimacao($idMdPetIntRelDestinatario){
       
            $objMdPetIntRelDestinatarioDTO = new MdPetIntRelDestinatarioDTO();
			$objMdPetIntRelDestinatarioDTO->setNumIdMdPetIntRelDestinatario($idMdPetIntRelDestinatario['id']);
            $objMdPetIntRelDestinatarioDTO->retNumIdContato();
            $objMdPetIntRelDestinatarioDTO->retNumIdMdPetIntimacao();
            $objMdPetIntRelDestinatarioDTO->retNumIdMdPetAceite();
			$objMdPetIntRelDestinatarioRN  = new MdPetIntRelDestinatarioRN();
            $objMdPetIntRelDestinatarioDTO = $objMdPetIntRelDestinatarioRN->consultar($objMdPetIntRelDestinatarioDTO);
            

            $xml .= '<dados>';
            $xml .= '<idContato>' . $objMdPetIntRelDestinatarioDTO->getNumIdContato() . '</idContato>';
            $xml .= '<idIntimacao>' . $objMdPetIntRelDestinatarioDTO->getNumIdMdPetIntimacao() . '</idIntimacao>';
            $xml .= '<idAceite>' . $objMdPetIntRelDestinatarioDTO->getNumIdMdPetAceite() . '</idAceite>';

            $xml .= '</dados>';


            return $xml;
    }
}
?>