<?
/**
 * TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
 *
 * 14/03/2017 - criado por pedro.cast
 * 26/08/2024 - Atualização por gabrielg.colab - SPASSU
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

    public static function montarSelectRazaoSocial($strPrimeiroItemValor, $strPrimeiroItemDescricao, $strValorItemSelecionado, $numIdMdPetIntimacao = '', $idMdPetIntRelDestinatario,$idDocumento,$idAceite, $idContatoVincRepresentant)
    {
            $objMdPetIntRelDestinatarioDTO = new MdPetIntRelDestinatarioDTO();
			//$objMdPetIntRelDestinatarioDTO->setNumIdMdPetIntimacao($numIdMdPetIntimacao);
            $objMdPetIntRelDestinatarioDTO->retNumIdMdPetIntRelDestinatario();
            $objMdPetIntRelDestinatarioDTO->retNumIdMdPetIntimacao();
            $objMdPetIntRelDestinatarioDTO->setDblIdProtocolo($idDocumento);
            $objMdPetIntRelDestinatarioDTO->setNumIdMdPetAceite($idAceite,InfraDTO::$OPER_IN);
            $objMdPetIntRelDestinatarioDTO->retNumIdContato();
            $objMdPetIntRelDestinatarioDTO->retStrSinPessoaJuridica();
            $objMdPetIntRelDestinatarioDTO->retStrCnpjContato();
            $objMdPetIntRelDestinatarioDTO->retDblCpfContato();
            $objMdPetIntRelDestinatarioDTO->retStrEmailContato();
            $objMdPetIntRelDestinatarioDTO->retStrNomeContato();
			$objMdPetIntRelDestinatarioRN  = new MdPetIntRelDestinatarioRN();
			$objMdPetIntRelDestinatarioDTO = $objMdPetIntRelDestinatarioRN->listar($objMdPetIntRelDestinatarioDTO);

            // filtra a empresa para saber se aquela representação está ativa
			$MdPetVincRepresentantRN = new MdPetVincRepresentantRN();
            $arrObjMdPetIntRelDestinatarioFiltrado = [];
            foreach ($objMdPetIntRelDestinatarioDTO as $objMdPetIntRelDestinatario){
                $objMdPetVincRepresentantDTO = new MdPetVincRepresentantDTO();
                $objMdPetVincRepresentantDTO->setNumIdContato($idContatoVincRepresentant);
                $objMdPetVincRepresentantDTO->setStrStaEstado(MdPetVincRepresentantRN::$RP_ATIVO);
                $objMdPetVincRepresentantDTO->retNumIdMdPetVinculoRepresent();
                $objMdPetVincRepresentantDTO->setDblIdContatoVinculo($objMdPetIntRelDestinatario->getNumIdContato());
                $objMdPetVincRepresentantAtivo = $MdPetVincRepresentantRN->listar($objMdPetVincRepresentantDTO);
                if(!empty($objMdPetVincRepresentantAtivo)){
                    array_push($arrObjMdPetIntRelDestinatarioFiltrado, $objMdPetIntRelDestinatario);
                }
            }

        return parent::montarSelectArrInfraDTO($strPrimeiroItemValor, $strPrimeiroItemDescricao, $strValorItemSelecionado, $arrObjMdPetIntRelDestinatarioFiltrado, 'IdMdPetIntRelDestinatario', 'NomeEmailCnpjCpf');

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

    public static function consultarIntimacao($idMdPetIntRelDestinatario){
       
            $objMdPetIntRelDestinatarioDTO = new MdPetIntRelDestinatarioDTO();
			$objMdPetIntRelDestinatarioDTO->setNumIdMdPetIntRelDestinatario($idMdPetIntRelDestinatario['id']);
            $objMdPetIntRelDestinatarioDTO->retNumIdContato();
            $objMdPetIntRelDestinatarioDTO->retNumIdMdPetIntimacao();
            $objMdPetIntRelDestinatarioDTO->retNumIdMdPetAceite();
			$objMdPetIntRelDestinatarioRN  = new MdPetIntRelDestinatarioRN();
            $objMdPetIntRelDestinatarioDTO = $objMdPetIntRelDestinatarioRN->consultar($objMdPetIntRelDestinatarioDTO);
            

            $xml = '<dados>';
            $xml .= '<idContato>' . $objMdPetIntRelDestinatarioDTO->getNumIdContato() . '</idContato>';
            $xml .= '<idIntimacao>' . $objMdPetIntRelDestinatarioDTO->getNumIdMdPetIntimacao() . '</idIntimacao>';
            $xml .= '<idAceite>' . $objMdPetIntRelDestinatarioDTO->getNumIdMdPetAceite() . '</idAceite>';

            $xml .= '</dados>';


            return $xml;
    }
}
?>