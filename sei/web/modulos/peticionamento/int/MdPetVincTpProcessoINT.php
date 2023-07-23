<?php

/**
 * Created by PhpStorm.
 * User: jhon.carvalho
 * Date: 15/05/2018
 * Time: 16:32
 */
require_once dirname(__FILE__) . '/../../../SEI.php';

class MdPetVincTpProcessoINT extends InfraINT {

    public static function montarSelectHipoteseLegal($idOrgao, $strPrimeiroItemDescricao, $strValorItemSelecionado) {

        $peticionamento = false;
        $objMdPetHipoteseLegalDTO = new MdPetHipoteseLegalDTO();
        $objMdPetHipoteseLegalRN = new MdPetHipoteseLegalRN();
        $objMdPetHipoteseLegalDTO->retNumIdHipoteseLegalPeticionamento();
        $objMdPetHipoteseLegalDTO->retStrNome();
        $objMdPetHipoteseLegalDTO->retStrBaseLegal();
        $objMdPetHipoteseLegalDTO->setOrd('Nome', InfraDTO::$TIPO_ORDENACAO_ASC);
        $countHipotesesPeticionamento = $objMdPetHipoteseLegalRN->contar($objMdPetHipoteseLegalDTO);

        if ($countHipotesesPeticionamento > 0) {
            $peticionamento = true;
            $objMdPetHipoteseLegalDTO->retStrNome();
            $objMdPetHipoteseLegalDTO->retStrBaseLegal();
            $arrHipoteses = $objMdPetHipoteseLegalRN->listar($objMdPetHipoteseLegalDTO);
            $stringFim = '<option value=""> </option>';
            if (count($arrHipoteses) > 0) {

                foreach ($arrHipoteses as $objHipoteseLegalDTO) {

                    $idHipoteseLegal = $peticionamento ? $objHipoteseLegalDTO->getNumIdHipoteseLegalPeticionamento() : $objHipoteseLegalDTO->getNumIdHipoteseLegal();

                    if (!is_null($strValorItemSelecionado) && $strValorItemSelecionado == $idHipoteseLegal) {
                        $stringFim .= '<option value="' . $idHipoteseLegal . '" selected="selected">' . $objHipoteseLegalDTO->getStrNome() . ' (' . $objHipoteseLegalDTO->getStrBaseLegal() . ')';
                    } else {
                        $stringFim .= '<option value="' . $idHipoteseLegal . '">' . $objHipoteseLegalDTO->getStrNome() . ' (' . $objHipoteseLegalDTO->getStrBaseLegal() . ')';
                    }
                    $stringFim .= '</option>';
                }
            }
        } else {
            $stringFim = '<option value=""> </option>';
        }

        return $stringFim;
    }

    public static function confirmarRestricao($idTipoProcesso, $idOrgaoUnidadeMultipla, $idUnidadeMultipla) {
        //Verifica se existe restrição para este tipo de processo em questão
        $objTipoProcedRestricaoRN = new TipoProcedRestricaoRN();
        $objTipoProcedRestricaoDTO = new TipoProcedRestricaoDTO();
        $objTipoProcedRestricaoDTO->retNumIdOrgao();
        $objTipoProcedRestricaoDTO->retNumIdUnidade();
        $objTipoProcedRestricaoDTO->setNumIdTipoProcedimento($idTipoProcesso);
        $arrObjTipoProcedRestricaoDTO = $objTipoProcedRestricaoRN->listar($objTipoProcedRestricaoDTO);
        $idOrgaoRestricao = InfraArray::converterArrInfraDTO($arrObjTipoProcedRestricaoDTO, 'IdOrgao');
        $idUnidadeRestricao = InfraArray::converterArrInfraDTO($arrObjTipoProcedRestricaoDTO, 'IdUnidade');

        $xml = '';
        $xml .= '<resposta>';
        if ($idOrgaoRestricao || $idUnidadeRestricao) {
            $restricao = "<valor>A</valor>";
            //Verifica se tem algum órgão diferente dos restritos, caso exista restrições para o tipo de processo
            if (($idOrgaoRestricao && $idOrgaoRestricao[0] != null) && !in_array($idOrgaoUnidadeMultipla, $idOrgaoRestricao)) {
                $restricao = "<valor>R</valor>";
            }
            //Verifica se tem alguma unidade diferente dos restritos, caso exista restrições para o tipo de processo
            if (($idUnidadeRestricao && $idUnidadeRestricao[0] != null) && !in_array($idUnidadeMultipla, $idUnidadeRestricao)) {
                $restricao = "<valor>R</valor>";
            }
            $xml .= $restricao;
        } else {
            $xml .= "<valor>A</valor>";
        }
        $xml .= "</resposta>";

        return $xml;
    }

    public static function confirmarRestricaoSalvar($idTipoProcesso, $idUnidadeMultipla) {
        //Caso tenha alguma restrição não pode salvar os dados   
        $idUnidadeMultipla = $idUnidadeMultipla != '' ? json_decode($idUnidadeMultipla) : array();
        
        $objUnidadeDTO = new UnidadeDTO();
        $objUnidadeRN = new UnidadeRN();
        $objUnidadeDTO->setNumIdUnidade($idUnidadeMultipla, InfraDTO::$OPER_IN);
        $objUnidadeDTO->retNumIdOrgao();
        $objUnidadeDTO->retNumIdUnidade();
        $objUnidadeDTO->retNumIdCidadeContato();
        $objUnidadeDTO = $objUnidadeRN->listarRN0127($objUnidadeDTO);

        $tipoProcessoRestricaoErro = false;
        $arrTipoProcessoOrgaoCidade = array();
        //$idOrgaoUnidadeMultipla = InfraArray::distinctArrInfraDTO($objUnidadeDTO, 'IdOrgao');

        $xml = '';
        $xml .= '<resposta>';
        foreach ($objUnidadeDTO as $objDTO) {
            //Criação do array para confirmar se existe para tipo de processo unidades com o mesmo orgao e cidade
            if (!key_exists($objDTO->getNumIdOrgao(), $arrTipoProcessoOrgaoCidade)) {
                $arrTipoProcessoOrgaoCidade[$objDTO->getNumIdOrgao()] = array();
            }
            if (!key_exists($objDTO->getNumIdCidadeContato(), $arrTipoProcessoOrgaoCidade[$objDTO->getNumIdOrgao()])) {
                $arrTipoProcessoOrgaoCidade[$objDTO->getNumIdOrgao()][$objDTO->getNumIdCidadeContato()] = 1;
            } else {
                $arrTipoProcessoOrgaoCidade[$objDTO->getNumIdOrgao()][$objDTO->getNumIdCidadeContato()] = $arrTipoProcessoOrgaoCidade[$objDTO->getNumIdOrgao()][$objDTO->getNumIdCidadeContato()] + 1;
            }
        }
        
        //verificando se existe algum tipo de processo com divergencia de orgao e cidade iguais
        if ($arrTipoProcessoOrgaoCidade) {
            $tipoProcessoDivergencia = false;
            foreach ($arrTipoProcessoOrgaoCidade as $key => $dados) {
                foreach ($dados as $qnt) {
                    if ($qnt > 1) {
                        $tipoProcessoDivergencia = true;
                        break;
                    }
                }
            }
        }
        
        //Verifica se existe restrição para este tipo de processo em questão
        $objTipoProcedRestricaoRN = new TipoProcedRestricaoRN();
        $objTipoProcedRestricaoDTO = new TipoProcedRestricaoDTO();
        $objTipoProcedRestricaoDTO->retNumIdOrgao();
        $objTipoProcedRestricaoDTO->retNumIdUnidade();
        $objTipoProcedRestricaoDTO->setNumIdTipoProcedimento($idTipoProcesso);
        $arrObjTipoProcedRestricaoDTO = $objTipoProcedRestricaoRN->listar($objTipoProcedRestricaoDTO);
        $idOrgaoRestricao = InfraArray::converterArrInfraDTO($arrObjTipoProcedRestricaoDTO, 'IdOrgao');
        $idUnidadeRestricao = InfraArray::converterArrInfraDTO($arrObjTipoProcedRestricaoDTO, 'IdUnidade');
        
        if ($idOrgaoRestricao || $idUnidadeRestricao) {
            foreach ($idUnidadeMultipla as $idUnidade) {
               //Verifica se tem alguma unidade diferente dos restritos, caso exista restrições para o tipo de processo
                if (($idUnidadeRestricao && $idUnidadeRestricao[0] != null) && !in_array($idUnidade, $idUnidadeRestricao)) {
                    $tipoProcessoRestricaoErro = true;
                } 
            }
            
            foreach ($objUnidadeDTO as $orgao) {
                //Verifica se tem algum órgão diferente dos restritos, caso exista restrições para o tipo de processo
                if (($idOrgaoRestricao && $idOrgaoRestricao[0] != null) && !in_array($orgao->getNumIdOrgao(), $idOrgaoRestricao)) {
                    $tipoProcessoRestricaoErro = true;
                }
            }      
            
        }        
       
        if ($tipoProcessoRestricaoErro || $tipoProcessoDivergencia) {
            $xml .= "<valor>R</valor>";
        }else{
            $xml .= "<valor>A</valor>";
        }     
        
        $xml .= "</resposta>";
        
        return $xml;
    }

    public static function retornaDadosUnidade($idUnidadeMultipla) {

        $xml = '<resposta>';
        if ($idUnidadeMultipla) {
            $objUnidadeDTO = new UnidadeDTO();
            $objUnidadeDTO->setNumIdUnidade($idUnidadeMultipla);
            $objUnidadeDTO->retNumIdUnidade();
            $objUnidadeDTO->retNumIdContato();
            $objUnidadeDTO->retStrSigla();
            $objUnidadeDTO->retStrDescricao();
            $objUnidadeDTO->retStrSiglaOrgao();
            $objUnidadeDTO->retNumIdOrgao();
            $objUnidadeDTO->retStrDescricaoOrgao();
            $objUnidadeRN = new UnidadeRN();
            $arrObjUnidadeDTO = $objUnidadeRN->listarTodasComFiltro($objUnidadeDTO);

            foreach ($arrObjUnidadeDTO as $objUnidadeDTO) {

                $xml .= '<siglaUnidade>' . PaginaSEI::tratarHTML($objUnidadeDTO->getStrSigla()) . '</siglaUnidade>';
                $xml .= '<descricaoUnidade>' . PaginaSEI::tratarHTML($objUnidadeDTO->getStrDescricao()) . '</descricaoUnidade>';
                $xml .= '<siglaOrgao>' . PaginaSEI::tratarHTML($objUnidadeDTO->getStrSiglaOrgao()) . '</siglaOrgao>';
                $xml .= '<descricaoOrgao>' . PaginaSEI::tratarHTML($objUnidadeDTO->getStrDescricaoOrgao()) . '</descricaoOrgao>';
                $xml .= '<idOrgao>' . $objUnidadeDTO->getNumIdOrgao() . '</idOrgao>';

                $contatoAssociadoDTO = new ContatoDTO();
                $contatoAssociadoRN = new ContatoRN();
                $contatoAssociadoDTO->retStrSiglaUf();
                $contatoAssociadoDTO->retNumIdContato();
                $contatoAssociadoDTO->retStrNomeCidade();
                $contatoAssociadoDTO->retNumIdCidade();
                $contatoAssociadoDTO->setNumIdContato($objUnidadeDTO->getNumIdContato());
                $contatoAssociadoDTO = $contatoAssociadoRN->consultarRN0324($contatoAssociadoDTO);
                //so recuperar caso se trata de unidade que possua UF configurada]
                if ($contatoAssociadoDTO != null && $contatoAssociadoDTO->isSetStrSiglaUf() && $contatoAssociadoDTO->getStrSiglaUf() != null) {
                    $xml .= '<uf>' . $contatoAssociadoDTO->getStrSiglaUf() . '</uf>';
                    $xml .= '<cidade>' . PaginaSEI::tratarHTML($contatoAssociadoDTO->getStrNomeCidade()) . '</cidade>';
                    $xml .= '<idCidade>' . $contatoAssociadoDTO->getNumIdCidade() . '</idCidade>';
                }
            }
        }
        $xml .= "</resposta>";

        return $xml;
    }
}
