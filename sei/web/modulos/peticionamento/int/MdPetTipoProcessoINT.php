<?
/**
 * ANATEL
 *
 * 30/03/2016 - criado por jaqueline.mendes@cast.com.br - CAST
 *
 */

require_once dirname(__FILE__) . '/../../../SEI.php';

class MdPetTipoProcessoINT extends InfraINT
{

    public static function montarSelectIndicacaoInteressadoPeticionamento($strPrimeiroItemValor, $strPrimeiroItemDescricao, $strValorItemSelecionado)
    {
        $objMdPetTipoProcessoRN = new MdPetTipoProcessoRN();

        $arrObjIndicacaoInteressadaDTO = $objMdPetTipoProcessoRN->listarValoresIndicacaoInteressado();

        return parent::montarSelectArrInfraDTO($strPrimeiroItemValor, $strPrimeiroItemDescricao, $strValorItemSelecionado, $arrObjIndicacaoInteressadaDTO, 'SinIndicacao', 'Descricao');

    }


    public static function montarSelectTipoDocumento($strPrimeiroItemValor, $strPrimeiroItemDescricao, $strValorItemSelecionado)
    {
        $objMdPetTipoProcessoRN = new MdPetTipoProcessoRN();

        $arrObjTipoDocumentoPeticionamentDTO = $objMdPetTipoProcessoRN->listarValoresTipoDocumento();

        return parent::montarSelectArrInfraDTO($strPrimeiroItemValor, $strPrimeiroItemDescricao, $strValorItemSelecionado, $arrObjTipoDocumentoPeticionamentDTO, 'TipoDoc', 'Descricao');

    }

    public static function montarSelectTipoProcesso($strPrimeiroItemValor, $strPrimeiroItemDescricao, $strValorItemSelecionado)
    {
        $objTipoProcedimentoRN = new TipoProcedimentoRN();

        $objTipoProcedimento = new TipoProcedimentoDTO();
        $objTipoProcedimento->retTodos();
        //listarRN0244Conectado
        $arrObjTiposProcessoDTO = $objTipoProcedimentoRN->listarRN0244($objTipoProcedimento);

        return parent::montarSelectArrInfraDTO($strPrimeiroItemValor, $strPrimeiroItemDescricao, $strValorItemSelecionado, $arrObjTiposProcessoDTO, 'IdTipoProcedimento', 'Nome');

    }

    //Recuperando Cidedes com parametro do tipo de processo

    public static function montarSelectOrgaoTpProcessoCidadePetNovo($id)
    {


        $objMdPetTipoProcessoDTO = new MdPetTipoProcessoDTO();
        $objMdPetTipoProcessoDTO->retTodos();
        if (array_key_exists("idTpProc", $id)) {
            $objMdPetTipoProcessoDTO->setNumIdTipoProcessoPeticionamento($id['idTpProc']);
        }
        $objMdPetTipoProcessoRN = new MdPetTipoProcessoRN();
        $arrObjMdPetTipoProcessoRN = $objMdPetTipoProcessoRN->listar($objMdPetTipoProcessoDTO);
        $arrTipoPet = InfraArray::converterArrInfraDTO($arrObjMdPetTipoProcessoRN, 'IdTipoProcessoPeticionamento');

        $objMdPetRelTpProcessoUnidRN = new MdPetRelTpProcessoUnidRN();
        $objMdPetRelTpProcessoUnidDTO = new MdPetRelTpProcessoUnidDTO();
        $objMdPetRelTpProcessoUnidDTO->retTodos();
        $objMdPetRelTpProcessoUnidDTO->retNumIdUnidade();
        $objMdPetRelTpProcessoUnidDTO->retStrStaTipoUnidade();
        $objMdPetRelTpProcessoUnidDTO->setNumIdTipoProcessoPeticionamento($arrTipoPet, InfraDTO::$OPER_IN);
        $arrobjMdPetRelTpProcessoUnidDTO = $objMdPetRelTpProcessoUnidRN->listar($objMdPetRelTpProcessoUnidDTO);
        $arrIdsUnidade = InfraArray::converterArrInfraDTO($arrobjMdPetRelTpProcessoUnidDTO, 'IdUnidade');

        $objUnidadeDTO = new UnidadeDTO();
        $objUnidadeDTO->retNumIdContato();
        if ($id['idOrgao'] != "") {
            $objUnidadeDTO->setNumIdOrgao($id['idOrgao']);
        }
        $objUnidadeDTO->setNumIdUnidade($arrIdsUnidade, InfraDTO::$OPER_IN);
        $objUnidadeRN = new UnidadeRN();
        $arrIdsContato = $objUnidadeRN->listarRN0127($objUnidadeDTO);
        $arrIdsContato = InfraArray::converterArrInfraDTO($arrIdsContato, 'IdContato');


        $objContatoDTO = new ContatoDTO();
        $objContatoDTO->setNumIdContato($arrIdsContato, InfraDTO::$OPER_IN);
        $objContatoDTO->setNumIdUf($id['idUf']);
        $objContatoDTO->retStrNomeCidade();
        $objContatoDTO->retNumIdCidade();
        $objContatoDTO->retNumIdContato();
        $objContatoRN = new ContatoRN();
        $arrIdsContato = $objContatoRN->listarRN0325($objContatoDTO);
        $arrIdContato = InfraArray::converterArrInfraDTO($arrIdsContato, 'IdContato');

        $objUnidadeDTO = new UnidadeDTO();
        $objUnidadeDTO->setNumIdContato($arrIdContato, InfraDTO::$OPER_IN);
        if ($id['idOrgao'] != "") {
            $objUnidadeDTO->setNumIdOrgao($id['idOrgao']);
        }
        $objUnidadeDTO->retNumIdUnidade();
        $objUnidadeRN = new UnidadeRN();
        $arrIdsContato = $objUnidadeRN->listarRN0127($objUnidadeDTO);
        $arrIdUnidade = InfraArray::converterArrInfraDTO($arrIdsContato, 'IdUnidade');


        $arrUni = array();
        $arrCid = array();
        $arrUnidadePrincipal = array();
        //PEticionamento processo novo
        //Recuperando Unidade
        foreach ($arrIdUnidade as $idUnidade) {

            $objUnidadeDTO = new UnidadeDTO();
            $objUnidadeDTO->setNumIdUnidade($idUnidade);
            $objUnidadeDTO->retNumIdContato();
            $objUnidadeRN = new UnidadeRN();
            $arrIdUnidade = $objUnidadeRN->consultarRN0125($objUnidadeDTO);
            $arrUnidadePrincipal[] = $arrIdUnidade->getNumIdContato();
        }

        $objContatoDTO = new ContatoDTO();
        $objContatoDTO->setNumIdContato($arrUnidadePrincipal, infraDTO::$OPER_IN);
        $objContatoDTO->retStrNomeCidade();
        $objContatoDTO->retNumIdContato();
        $objContatoDTO->setOrdStrNomeCidade(InfraDTO::$TIPO_ORDENACAO_ASC);
        $objContatoRN = new ContatoRN();
        $arrIdsContato = $objContatoRN->listarRN0325($objContatoDTO);

        foreach ($arrIdsContato as $value) {

            $objUnidadeDTO = new UnidadeDTO();
            $objUnidadeDTO->setNumIdContato($value->getNumIdContato());
            $objUnidadeDTO->retNumIdUnidade();
            $objUnidadeRN = new UnidadeRN();
            $arrIdUnidade = $objUnidadeRN->consultarRN0125($objUnidadeDTO);

            $arrUni[] = $arrIdUnidade->getNumIdUnidade();
            $arrCid[] = $value->getStrNomeCidade();
        }


        $xml = '';
        $xml .= '<itens>';
        if (count($arrCid) > 0 && count($arrUni) > 0) {
            for ($i = 0; $i < count($arrUni); $i++) {
                $xml .= '<item id="' . $arrUni[$i] . '"';
                $xml .= ' descricao="' . $arrCid[$i] . '"';
                $xml .= '></item>';
            }
        }
        $xml .= '</itens>';

        return $xml;

    }

    //Recuperando Cidades

    public static function montarSelectCidade($id = null, $orgao = null, $uf = null, $cidade = null)
    {
        $arrUni = array();
        $arrCid = array();

        //Restrição Orgão
        $objMdPetTipoProcessoRN = new MdPetTipoProcessoRN();
        $arrRestricaoOrgao = $objMdPetTipoProcessoRN->restricaoOrgao();


        $objMdPetTipoProcessoDTO = new MdPetTipoProcessoDTO();
        $objMdPetTipoProcessoDTO->retStrNomeProcesso();
        $objMdPetTipoProcessoDTO->retStrOrientacoes();
        $objMdPetTipoProcessoDTO->retNumIdTipoProcessoPeticionamento();
        if ($id != null) {
            $objMdPetTipoProcessoDTO->setNumIdTipoProcessoPeticionamento($id);
        }
        if ($id == null) {
            if (count($arrRestricaoOrgao)) {
                $objMdPetTipoProcessoDTO->setNumIdProcedimento($arrRestricaoOrgao, infraDTO::$OPER_NOT_IN);
            }
        }
        $objMdPetTipoProcessoRN = new MdPetTipoProcessoRN();
        $arrObjMdPetTipoProcessoRN = $objMdPetTipoProcessoRN->listar($objMdPetTipoProcessoDTO);
        $arrIdTipoProcessoPeticionamento = InfraArray::converterArrInfraDTO($arrObjMdPetTipoProcessoRN, 'IdTipoProcessoPeticionamento');


        $objMdPetRelTpProcessoUnidRN = new MdPetRelTpProcessoUnidRN();
        $objMdPetRelTpProcessoUnidDTO = new MdPetRelTpProcessoUnidDTO();
        $objMdPetRelTpProcessoUnidDTO->setNumIdTipoProcessoPeticionamento($arrIdTipoProcessoPeticionamento, InfraDTO::$OPER_IN);
        $objMdPetRelTpProcessoUnidDTO->retNumIdUnidade();
        $objMdPetRelTpProcessoUnidDTO->retNumIdTipoProcessoPeticionamento();
        $objMdPetRelTpProcessoUnidDTO->retStrStaTipoUnidade();
        $arrobjMdPetRelTpProcessoUnidDTO = $objMdPetRelTpProcessoUnidRN->listar($objMdPetRelTpProcessoUnidDTO);
        $arrIdsUnidade = InfraArray::converterArrInfraDTO($arrobjMdPetRelTpProcessoUnidDTO, 'IdUnidade');


        $objUnidadeDTO = new UnidadeDTO();
        $objUnidadeDTO->retNumIdCidadeContato();
        $objUnidadeDTO->retNumIdUnidade();
        if ($orgao != null) {
            $objUnidadeDTO->setNumIdOrgao($orgao);
        }
        $objUnidadeDTO->setNumIdUnidade($arrIdsUnidade, InfraDTO::$OPER_IN);
        $objUnidadeRN = new UnidadeRN();
        $arrIdsCidadeArr = $objUnidadeRN->listarRN0127($objUnidadeDTO);
        $arrIdsCidade = InfraArray::converterArrInfraDTO($arrIdsCidadeArr, 'IdCidadeContato');
        $arrUnidades = InfraArray::converterArrInfraDTO($arrIdsCidadeArr, 'IdUnidade');

        $objUnidadeDTO = new CidadeDTO();
        $objUnidadeDTO->setNumIdCidade($arrIdsCidade, InfraDTO::$OPER_IN);
        $objUnidadeDTO->retStrNome();
        $objUnidadeDTO->setOrdStrNome(InfraDTO::$TIPO_ORDENACAO_ASC);
        if ($uf != null) {

            $objUnidadeDTO->setNumIdUf($uf);
        }
        $objUnidadeDTO->retNumIdCidade();
        $objUnidadeRN = new CidadeRN();
        $arrObjCidadeDTO = $objUnidadeRN->listarRN0410($objUnidadeDTO);
        $arrCidade = InfraArray::converterArrInfraDTO($arrObjCidadeDTO, 'Nome');


        if ($id == null) {

            $arrCidadeId = InfraArray::converterArrInfraDTO($arrObjCidadeDTO, 'IdCidade');
            foreach ($arrCidadeId as $idCidade) {
                $arrIdCidade [] = $idCidade;
            }
            //Recuperando Cidade
            foreach ($arrCidade as $cidade) {
                $arrCid [] = $cidade;
            }

            return array($arrIdCidade, $arrCid);

        } else {

            //PEticionamento processo novo
            //Recuperando Unidade
            foreach ($arrUnidades as $idUnidade) {

                $objUnidadeDTO = new UnidadeDTO();
                $objUnidadeDTO->setNumIdUnidade($idUnidade);

                $objUnidadeDTO->retNumIdContato();
                $objUnidadeRN = new UnidadeRN();
                $arrIdUnidade = $objUnidadeRN->consultarRN0125($objUnidadeDTO);

                $objContatoDTO = new ContatoDTO();
                $objContatoDTO->setNumIdContato($arrIdUnidade->getNumIdContato());
                if ($uf != null) {
                    $objContatoDTO->setNumIdUf($uf);
                }
                $objContatoDTO->retStrNomeCidade();
                $objContatoDTO->setOrdStrNomeCidade(InfraDTO::$TIPO_ORDENACAO_ASC);
                $objContatoRN = new ContatoRN();
                $arrIdsContato = $objContatoRN->listarRN0325($objContatoDTO);

                foreach ($arrIdsContato as $key => $value) {
                    $arrUni [] = $idUnidade;
                    $arrCid [] = $value->getStrNomeCidade();

                }


            }


            return array($arrUni, $arrCid);

        }
    }

    //Recuperando Uf
    public static function montarSelectUf($id = null, $orgao = null, $uf = null, $cidade = null)
    {
        $ufId = array();
        $uf = array();

        //Restrição Orgão
        $objMdPetTipoProcessoRN = new MdPetTipoProcessoRN();
        $arrRestricaoOrgao = $objMdPetTipoProcessoRN->restricaoOrgao();

        $objMdPetTipoProcessoDTO = new MdPetTipoProcessoDTO();
        $objMdPetTipoProcessoDTO->retStrNomeProcesso();
        $objMdPetTipoProcessoDTO->retStrOrientacoes();
        $objMdPetTipoProcessoDTO->retNumIdTipoProcessoPeticionamento();
        if ($id != null) {
            $objMdPetTipoProcessoDTO->setNumIdTipoProcessoPeticionamento($id);
        }
        if ($id == null) {
            if (count($arrRestricaoOrgao)) {
                $objMdPetTipoProcessoDTO->setNumIdProcedimento($arrRestricaoOrgao, infraDTO::$OPER_NOT_IN);
            }
        }
        $objMdPetTipoProcessoRN = new MdPetTipoProcessoRN();
        $arrObjMdPetTipoProcessoRN = $objMdPetTipoProcessoRN->listar($objMdPetTipoProcessoDTO);
        $arrIdTipoProcessoPeticionamento = InfraArray::converterArrInfraDTO($arrObjMdPetTipoProcessoRN, 'IdTipoProcessoPeticionamento');


        $objMdPetRelTpProcessoUnidRN = new MdPetRelTpProcessoUnidRN();
        $objMdPetRelTpProcessoUnidDTO = new MdPetRelTpProcessoUnidDTO();
        $objMdPetRelTpProcessoUnidDTO->setNumIdTipoProcessoPeticionamento($arrIdTipoProcessoPeticionamento, InfraDTO::$OPER_IN);
        $objMdPetRelTpProcessoUnidDTO->retNumIdUnidade();
        $objMdPetRelTpProcessoUnidDTO->retNumIdTipoProcessoPeticionamento();
        $objMdPetRelTpProcessoUnidDTO->retStrStaTipoUnidade();
        $arrobjMdPetRelTpProcessoUnidDTO = $objMdPetRelTpProcessoUnidRN->listar($objMdPetRelTpProcessoUnidDTO);
        $arrIdsUnidade = InfraArray::converterArrInfraDTO($arrobjMdPetRelTpProcessoUnidDTO, 'IdUnidade');


        $objUnidadeDTO = new UnidadeDTO();
        $objUnidadeDTO->retNumIdContato();
        $objUnidadeDTO->setNumIdUnidade($arrIdsUnidade, InfraDTO::$OPER_IN);
        if ($orgao != null) {
            $objUnidadeDTO->setNumIdOrgao($orgao);
        }
        $objUnidadeRN = new UnidadeRN();
        $arrObjUnidadeDTO = $objUnidadeRN->listarRN0127($objUnidadeDTO);
        $arrIdsContato = InfraArray::converterArrInfraDTO($arrObjUnidadeDTO, 'IdContato');

        $objContatoDTO = new ContatoDTO();
        $objContatoDTO->setNumIdContato($arrIdsContato, InfraDTO::$OPER_IN);
        $objContatoDTO->retNumIdUf();
        $objContatoDTO->retStrSiglaUf();
        $objContatoDTO->setOrdStrSiglaUf(InfraDTO::$TIPO_ORDENACAO_ASC);
        $objContatoDTO->retStrNomeCidade();
        $objContatoRN = new ContatoRN();
        $arrObjContato = $objContatoRN->listarRN0325($objContatoDTO);
        $arrIdsContatoDistinct = infraArray::distinctArrInfraDTO($arrObjContato, 'IdUf');

        foreach ($arrIdsContatoDistinct as $key => $value) {
            $ufId[] = $value->getNumIdUf();
            $uf[] = $value->getStrSiglaUf();
        }

        return array($ufId, $uf);
    }


    //Recuperando tipo processo

    public static function montarSelectOrgaoTpProcessoExterno($ids)
    {

        $filtroDinamico = '';
        if (array_key_exists("orgao", $ids) == false && array_key_exists("uf", $ids) == false && array_key_exists("cidade", $ids) == false) {
            $filtroDinamico = "ORGAO";
            $objUnidadeDTO = new UnidadeDTO();
            $objUnidadeDTO->retNumIdUnidade();
            $objUnidadeRN = new UnidadeRN();
            $arrIdsUnidade = $objUnidadeRN->listarRN0127($objUnidadeDTO);
            $arrIdsUnidade = InfraArray::converterArrInfraDTO($arrIdsUnidade, 'IdUnidade');
        }

        //Mostra somente orgão
        if (array_key_exists("orgao", $ids) == true && array_key_exists("uf", $ids) == false) {
            $filtroDinamico = "ORGAO";
            $objUnidadeDTO = new UnidadeDTO();
            $objUnidadeDTO->setNumIdOrgao($ids['orgao']);
            $objUnidadeDTO->retNumIdUnidade();
            $objUnidadeRN = new UnidadeRN();
            $arrIdsUnidade = $objUnidadeRN->listarRN0127($objUnidadeDTO);

            $arrIdsUnidade = InfraArray::converterArrInfraDTO($arrIdsUnidade, 'IdUnidade');

        } //Mostra orgão e Uf
        else if (array_key_exists("orgao", $ids) == true && array_key_exists("uf", $ids) == true && array_key_exists("cidade", $ids) == false) {
            $filtroDinamico = "ORGAO_UF";
            $objUnidadeDTO = new UnidadeDTO();
            $objUnidadeDTO->setNumIdOrgao($ids['orgao']);
            $objUnidadeDTO->retNumIdUnidade();
            $objUnidadeDTO->retNumIdContato();
            $objUnidadeRN = new UnidadeRN();
            $arrIdsUnidade = $objUnidadeRN->listarRN0127($objUnidadeDTO);
            $arrIdsContato = InfraArray::converterArrInfraDTO($arrIdsUnidade, 'IdContato');


            $objContatoDTO = new ContatoDTO();
            $objContatoDTO->setNumIdContato($arrIdsContato, InfraDTO::$OPER_IN);
            $objContatoDTO->setNumIdUf($ids['uf']);
            $objContatoDTO->retNumIdUnidadeCadastro();
            $objContatoDTO->retNumIdContato();
            $objContatoRN = new ContatoRN();
            $arrIdsContato = $objContatoRN->listarRN0325($objContatoDTO);

            $arrIdsContato = InfraArray::converterArrInfraDTO($arrIdsContato, 'IdContato');

            $objUnidadeDTO = new UnidadeDTO();
            $objUnidadeDTO->retNumIdUnidade();
            $objUnidadeDTO->setNumIdContato($arrIdsContato, InfraDTO::$OPER_IN);
            $objUnidadeRN = new UnidadeRN();
            $arrIdsUnidade = $objUnidadeRN->listarRN0127($objUnidadeDTO);
            $arrIdsUnidade = InfraArray::converterArrInfraDTO($arrIdsUnidade, 'IdUnidade');


        } //Mostra orgão, uf e cidade
        else if (array_key_exists("orgao", $ids) == true && array_key_exists("uf", $ids) == true && array_key_exists("cidade", $ids) == true) {
            $filtroDinamico = "ORGAO_UF_CIDADE";


            $objContatoDTO = new ContatoDTO();
            $objContatoDTO->setNumIdUf($ids['uf']);
            $objContatoDTO->setNumIdCidade($ids['cidade']);
            $objContatoDTO->retNumIdUnidadeCadastro();
            $objContatoDTO->retNumIdContato();
            $objContatoRN = new ContatoRN();
            $arrIdsContato = $objContatoRN->listarRN0325($objContatoDTO);

            $arrIdsContato = InfraArray::converterArrInfraDTO($arrIdsContato, 'IdContato');

            $objUnidadeDTO = new UnidadeDTO();
            $objUnidadeDTO->setNumIdOrgao($ids['orgao']);
            $objUnidadeDTO->retNumIdUnidade();
            $objUnidadeDTO->setNumIdContato($arrIdsContato, InfraDTO::$OPER_IN);
            $objUnidadeRN = new UnidadeRN();
            $arrIdsUnidade = $objUnidadeRN->listarRN0127($objUnidadeDTO);
            $arrIdsUnidade = InfraArray::converterArrInfraDTO($arrIdsUnidade, 'IdUnidade');

        } //Recuperando somente Tipos de Processos do filtro de Uf
        else if (array_key_exists("orgao", $ids) == false && array_key_exists("uf", $ids) == true && array_key_exists("cidade", $ids) == false) {
            $filtroDinamico = "UF";
            $objContatoDTO = new ContatoDTO();
            $objContatoDTO->setNumIdUf($ids['uf']);
            $objContatoDTO->retNumIdUnidadeCadastro();
            $objContatoDTO->retNumIdContato();
            $objContatoRN = new ContatoRN();
            $arrIdsContato = $objContatoRN->listarRN0325($objContatoDTO);

            $arrIdsContato = InfraArray::converterArrInfraDTO($arrIdsContato, 'IdContato');

            $objUnidadeDTO = new UnidadeDTO();
            $objUnidadeDTO->retNumIdUnidade();
            $objUnidadeDTO->setNumIdContato($arrIdsContato, InfraDTO::$OPER_IN);
            $objUnidadeRN = new UnidadeRN();
            $arrIdsUnidade = $objUnidadeRN->listarRN0127($objUnidadeDTO);
            $arrIdsUnidade = InfraArray::converterArrInfraDTO($arrIdsUnidade, 'IdUnidade');

            //Se existir somente uma cidade vinculada com a UF
            if (array_key_exists("cidade", $ids) == false) {

                $objMdPetTipoProcessoDTO = new MdPetTipoProcessoDTO();
                $objMdPetTipoProcessoDTO->retStrNomeProcesso();
                $objMdPetTipoProcessoDTO->retStrOrientacoes();
                $objMdPetTipoProcessoDTO->retNumIdTipoProcessoPeticionamento();
                $objMdPetTipoProcessoDTO->setOrdStrNomeProcesso(InfraDTO::$TIPO_ORDENACAO_ASC);
                $objMdPetTipoProcessoRN = new MdPetTipoProcessoRN();
                $arrObjMdPetTipoProcessoRN = $objMdPetTipoProcessoRN->listar($objMdPetTipoProcessoDTO);
                $arrIdsTipoProcesso = InfraArray::converterArrInfraDTO($arrObjMdPetTipoProcessoRN, 'IdTipoProcessoPeticionamento');


                $objMdPetRelTpProcessoUnidRN = new MdPetRelTpProcessoUnidRN();
                $objMdPetRelTpProcessoUnidDTO = new MdPetRelTpProcessoUnidDTO();
                $objMdPetRelTpProcessoUnidDTO->setNumIdTipoProcessoPeticionamento($arrIdsTipoProcesso, infraDTO::$OPER_IN);
                $objMdPetRelTpProcessoUnidDTO->retNumIdUnidade();
                $arrobjMdPetRelTpProcessoUnidDTO = $objMdPetRelTpProcessoUnidRN->listar($objMdPetRelTpProcessoUnidDTO);
                $arrIdUnidade = InfraArray::converterArrInfraDTO($arrobjMdPetRelTpProcessoUnidDTO, 'IdUnidade');


                $objUnidade2DTO = new UnidadeDTO();
                $objUnidade2DTO->setNumIdUnidade($arrIdUnidade, infraDTO::$OPER_IN);
                $objUnidade2DTO->retNumIdContato();
                $objUnidade2RN = new UnidadeRN();
                $arrIdsUnidade2 = $objUnidade2RN->listarRN0127($objUnidade2DTO);
                $arrIdsContato = InfraArray::converterArrInfraDTO($arrIdsUnidade2, 'IdContato');

                $objContatoDTO = new ContatoDTO();
                $objContatoDTO->retNumIdCidade();
                $objContatoDTO->setNumIdUf($ids['uf']);
                $objContatoDTO->setNumIdContato($arrIdsContato, InfraDTO::$OPER_IN);
                $objContatoRN = new ContatoRN();
                $arrIdsContato = $objContatoRN->listarRN0325($objContatoDTO);
                $qtdCidade = InfraArray::converterArrInfraDTO($arrIdsContato, 'IdCidade');
                if (count($qtdCidade) < 2) {
                    $ids['cidade'] = $qtdCidade[0];
                }
            }
        }

        //Recuperando somente Tipos de Processos do filtro de Cidade
        if (array_key_exists("orgao", $ids) == false && array_key_exists("uf", $ids) == false && array_key_exists("cidade", $ids) == true) {

            $filtroDinamico = "CIDADE";
            $objContatoDTO = new ContatoDTO();
            $objContatoDTO->setNumIdCidade($ids['cidade']);
            $objContatoDTO->retNumIdContato();
            $objContatoRN = new ContatoRN();
            $arrIdsContato = $objContatoRN->listarRN0325($objContatoDTO);

            $arrIdsContato = InfraArray::converterArrInfraDTO($arrIdsContato, 'IdContato');

            $objUnidadeDTO = new UnidadeDTO();
            $objUnidadeDTO->retNumIdUnidade();
            $objUnidadeDTO->setNumIdContato($arrIdsContato, InfraDTO::$OPER_IN);
            $objUnidadeRN = new UnidadeRN();
            $arrIdsUnidade = $objUnidadeRN->listarRN0127($objUnidadeDTO);
            $arrIdsUnidade = InfraArray::converterArrInfraDTO($arrIdsUnidade, 'IdUnidade');

        }

        //Recuperando somente Tipos de Processos do filtro de Uf e Cidade
        if (array_key_exists("orgao", $ids) == false && array_key_exists("uf", $ids) == true && array_key_exists("cidade", $ids) == true) {
            $filtroDinamico = "UF_CIDADE";
            $objContatoDTO = new ContatoDTO();
            $objContatoDTO->setNumIdCidade($ids['cidade']);
            $objContatoDTO->setNumIdUf($ids['uf']);
            $objContatoDTO->retNumIdContato();
            $objContatoRN = new ContatoRN();
            $arrIdsContato = $objContatoRN->listarRN0325($objContatoDTO);

            $arrIdsContato = InfraArray::converterArrInfraDTO($arrIdsContato, 'IdContato');

            $objUnidadeDTO = new UnidadeDTO();
            $objUnidadeDTO->retNumIdUnidade();
            $objUnidadeDTO->setNumIdContato($arrIdsContato, InfraDTO::$OPER_IN);
            $objUnidadeRN = new UnidadeRN();
            $arrIdsUnidade = $objUnidadeRN->listarRN0127($objUnidadeDTO);
            $arrIdsUnidade = InfraArray::converterArrInfraDTO($arrIdsUnidade, 'IdUnidade');

        }

        $objMdPetRelTpProcessoUnidRN = new MdPetRelTpProcessoUnidRN();
        $objMdPetRelTpProcessoUnidDTO = new MdPetRelTpProcessoUnidDTO();
        $objMdPetRelTpProcessoUnidDTO->retNumIdTipoProcessoPeticionamento();
        $objMdPetRelTpProcessoUnidDTO->setNumIdUnidade($arrIdsUnidade, InfraDTO::$OPER_IN);
        $arrobjMdPetRelTpProcessoUnidDTO = $objMdPetRelTpProcessoUnidRN->listar($objMdPetRelTpProcessoUnidDTO);
        $arrIdTipoProcessoPeticionamento = InfraArray::converterArrInfraDTO($arrobjMdPetRelTpProcessoUnidDTO, 'IdTipoProcessoPeticionamento');

        //Validação Cidade Unica
        $objTipoProcessoDTO = new MdPetTipoProcessoDTO();
        $objTipoProcessoDTO->setNumIdTipoProcessoPeticionamento($arrIdTipoProcessoPeticionamento, infraDTO::$OPER_IN);
        $objTipoProcessoDTO->retNumIdTipoProcessoPeticionamento();
        $objTipoProcessoDTO->retStrNomeProcesso();
        $objTipoProcessoDTO->retNumIdProcedimento();
        $objTipoProcessoDTO->retStrOrientacoes();
        $objTipoProcessoDTO->setStrSinAtivo('S');
        $objTipoProcessoDTO->setOrdStrNomeProcesso(InfraDTO::$TIPO_ORDENACAO_ASC);

        $objTipoProcedimentoRN = new MdPetTipoProcessoRN();
        $arrObjTipoProcedimentoFiltroDTO = $objTipoProcedimentoRN->listar($objTipoProcessoDTO);
        $arrObjTipoProcedimentoRestricaoDTO = InfraArray::converterArrInfraDTO($arrObjTipoProcedimentoFiltroDTO, 'IdProcedimento');


        $arrTipoProcessoOrgaoCidade = array();
        $arrIdTipoProcesso = array();
        foreach ($arrObjTipoProcedimentoFiltroDTO as $key => $tpProc) {
            if (!in_array($tpProc->getNumIdTipoProcessoPeticionamento(), $arrIdTipoProcesso)) {
                array_push($arrIdTipoProcesso, $tpProc->getNumIdTipoProcessoPeticionamento());
            }
        }

        $objMdPetRelTpProcessoUnidRN = new MdPetRelTpProcessoUnidRN();
        $objMdPetRelTpProcessoUnidDTO = new MdPetRelTpProcessoUnidDTO();
        $objMdPetRelTpProcessoUnidDTO->setNumIdTipoProcessoPeticionamento($arrIdTipoProcesso, InfraDTO::$OPER_IN);
        $objMdPetRelTpProcessoUnidDTO->retNumIdUnidade();
        $objMdPetRelTpProcessoUnidDTO->retNumIdOrgaoUnidade();
        $objMdPetRelTpProcessoUnidDTO->retNumIdCidadeContato();
        $objMdPetRelTpProcessoUnidDTO->retNumIdTipoProcessoPeticionamento();
        $objMdPetRelTpProcessoUnidDTO->retStrStaTipoUnidade();
        $arrobjMdPetRelTpProcessoUnidDTO = $objMdPetRelTpProcessoUnidRN->listar($objMdPetRelTpProcessoUnidDTO);

        foreach ($arrobjMdPetRelTpProcessoUnidDTO as $key => $objDTO) {
            //print_r($objDTO->getNumIdTipoProcessoPeticionamento()); die;
            if (!key_exists($objDTO->getNumIdTipoProcessoPeticionamento(), $arrTipoProcessoOrgaoCidade)) {
                $arrTipoProcessoOrgaoCidade[$objDTO->getNumIdTipoProcessoPeticionamento()] = array();
            }
            if (!key_exists($objDTO->getNumIdOrgaoUnidade(), $arrTipoProcessoOrgaoCidade[$objDTO->getNumIdTipoProcessoPeticionamento()])) {
                $arrTipoProcessoOrgaoCidade[$objDTO->getNumIdTipoProcessoPeticionamento()][$objDTO->getNumIdOrgaoUnidade()] = array();
            }

            if (!key_exists($objDTO->getNumIdCidadeContato(), $arrTipoProcessoOrgaoCidade[$objDTO->getNumIdTipoProcessoPeticionamento()][$objDTO->getNumIdOrgaoUnidade()])) {
                $arrTipoProcessoOrgaoCidade[$objDTO->getNumIdTipoProcessoPeticionamento()][$objDTO->getNumIdOrgaoUnidade()][$objDTO->getNumIdCidadeContato()] = 1;
            } else {
                $arrTipoProcessoOrgaoCidade[$objDTO->getNumIdTipoProcessoPeticionamento()][$objDTO->getNumIdOrgaoUnidade()][$objDTO->getNumIdCidadeContato()] = $arrTipoProcessoOrgaoCidade[$objDTO->getNumIdTipoProcessoPeticionamento()][$objDTO->getNumIdOrgaoUnidade()][$objDTO->getNumIdCidadeContato()] + 1;
            }


        }
        $arrIdsTpProcesso = array_keys($arrTipoProcessoOrgaoCidade);
        //verificando se existe algum tipo de processo com divergencia de orgao e cidade iguais
        if ($arrTipoProcessoOrgaoCidade) {
            $tipoProcessoDivergencia = false;
            foreach ($arrTipoProcessoOrgaoCidade as $key => $dados) {
                foreach ($dados as $cidade) {
                    foreach ($cidade as $qnt) {
                        if ($qnt > 1) {
                            foreach ($arrObjTipoProcedimentoFiltroDTO as $chaveTpProc => $tpProc) {
                                if ($tpProc->getNumIdTipoProcessoPeticionamento() == $key) {
                                    unset($arrObjTipoProcedimentoFiltroDTO[$chaveTpProc]);
                                    $chaveRemover = array_search($key, $arrIdsTpProcesso);
                                    unset($arrIdsTpProcesso[$chaveRemover]);
                                }
                            }
                        }
                    }
                }

            }
        }
//Fim validação cidade Unica

//Restrição
        $arrRestricao = array();
        foreach ($arrObjTipoProcedimentoFiltroDTO as $key => $tpProc) {

            //Verifica se existe restrição para o tipo de processo
            $objTipoProcedRestricaoRN = new TipoProcedRestricaoRN();
            $objTipoProcedRestricaoDTO = new TipoProcedRestricaoDTO();
            $objTipoProcedRestricaoDTO->retNumIdOrgao();
            $objTipoProcedRestricaoDTO->retNumIdUnidade();
            $objTipoProcedRestricaoDTO->setNumIdTipoProcedimento($tpProc->getNumIdProcedimento());
            $arrObjTipoProcedRestricaoDTO = $objTipoProcedRestricaoRN->listar($objTipoProcedRestricaoDTO);

            $idOrgaoRestricao = InfraArray::converterArrInfraDTO($arrObjTipoProcedRestricaoDTO, 'IdOrgao');
            $idUnidadeRestricao = InfraArray::converterArrInfraDTO($arrObjTipoProcedRestricaoDTO, 'IdUnidade');

            $objMdPetRelTpProcessoUnidRN = new MdPetRelTpProcessoUnidRN();
            $objMdPetRelTpProcessoUnidDTO = new MdPetRelTpProcessoUnidDTO();
            $objMdPetRelTpProcessoUnidDTO->retTodos();
            $objMdPetRelTpProcessoUnidDTO->retStrsiglaUnidade();
            $objMdPetRelTpProcessoUnidDTO->retStrStaTipoUnidade();
            $objMdPetRelTpProcessoUnidDTO->retStrdescricaoUnidade();
            $objMdPetRelTpProcessoUnidDTO->retNumIdUnidade();
            $objMdPetRelTpProcessoUnidDTO->retNumIdOrgaoUnidade();
            $objMdPetRelTpProcessoUnidDTO->retStrDescricaoOrgao();
            $objMdPetRelTpProcessoUnidDTO->retStrSiglaOrgao();
            $objMdPetRelTpProcessoUnidDTO->retNumIdCidadeContato();
            $objMdPetRelTpProcessoUnidDTO->setNumIdTipoProcessoPeticionamento($tpProc->getNumIdTipoProcessoPeticionamento());
            $arrobjMdPetRelTpProcessoUnidDTO = $objMdPetRelTpProcessoUnidRN->listar($objMdPetRelTpProcessoUnidDTO);


            foreach ($arrobjMdPetRelTpProcessoUnidDTO as $objDTO) {

                //Verifica se tem alguma unidade ou órgão diferente dos restritos
                if (($idOrgaoRestricao && $idOrgaoRestricao[0] != null) && !in_array($objDTO->getNumIdOrgaoUnidade(), $idOrgaoRestricao)) {
                    $arrRestricao [] = $tpProc->getNumIdProcedimento();
                }
                if (($idUnidadeRestricao && $idUnidadeRestricao[0] != null) && !in_array($objDTO->getNumIdUnidade(), $idUnidadeRestricao)) {
                    $arrRestricao [] = $tpProc->getNumIdProcedimento();
                }

            }

        }
//Fim restrição


        $objMdPetTipoProcessoDTO = new MdPetTipoProcessoDTO();
        $objMdPetTipoProcessoDTO->retStrNomeProcesso();
        $objMdPetTipoProcessoDTO->retStrOrientacoes();
        $objMdPetTipoProcessoDTO->retNumIdProcedimento();
        if (count($arrRestricao)) {
            $objMdPetTipoProcessoDTO->setNumIdProcedimento($arrRestricao, infraDTO::$OPER_NOT_IN);
        }
        $objMdPetTipoProcessoDTO->retNumIdTipoProcessoPeticionamento();
        $objMdPetTipoProcessoDTO->setOrdStrNomeProcesso(InfraDTO::$TIPO_ORDENACAO_ASC);
        $objMdPetTipoProcessoDTO->setNumIdTipoProcessoPeticionamento($arrIdsTpProcesso, InfraDTO::$OPER_IN);
        $objMdPetTipoProcessoRN = new MdPetTipoProcessoRN();
        $arrObjMdPetTipoProcessoRN = $objMdPetTipoProcessoRN->listar($objMdPetTipoProcessoDTO);


        $xml = '';
        $xml .= '<itens>';
        if ($arrObjMdPetTipoProcessoRN !== null) {
            foreach ($arrObjMdPetTipoProcessoRN as $dto) {

                if ($filtroDinamico == "ORGAO") {
                    $xml .= '<item id="' . InfraString::formatarXML(SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=md_pet_usu_ext_cadastrar&id_orgao=' . $ids['orgao'] . '&id_tipo_procedimento=' . $dto->getNumIdTipoProcessoPeticionamento())) . '"';
                }
                if ($filtroDinamico == "CIDADE") {
                    $xml .= '<item id="' . InfraString::formatarXML(SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=md_pet_usu_ext_cadastrar&id_cidade=' . $ids['cidade'] . '&id_tipo_procedimento=' . $dto->getNumIdTipoProcessoPeticionamento())) . '"';
                }
                if ($filtroDinamico == "UF") {
                    $xml .= '<item id="' . InfraString::formatarXML(SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=md_pet_usu_ext_cadastrar&id_uf=' . $ids['uf'] . '&id_tipo_procedimento=' . $dto->getNumIdTipoProcessoPeticionamento())) . '"';
                }
                if ($filtroDinamico == "UF_CIDADE") {
                    $xml .= '<item id="' . InfraString::formatarXML(SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=md_pet_usu_ext_cadastrar&id_uf=' . $ids['uf'] . '&id_cidade=' . $ids['cidade'] . '&id_tipo_procedimento=' . $dto->getNumIdTipoProcessoPeticionamento())) . '"';
                }
                if ($filtroDinamico == "ORGAO_UF_CIDADE") {
                    $xml .= '<item id="' . InfraString::formatarXML(SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=md_pet_usu_ext_cadastrar&id_orgao=' . $ids['orgao'] . '&id_uf=' . $ids['uf'] . '&id_cidade=' . $ids['cidade'] . '&id_tipo_procedimento=' . $dto->getNumIdTipoProcessoPeticionamento())) . '"';
                }
                if ($filtroDinamico == "ORGAO_UF") {
                    $xml .= '<item id="' . InfraString::formatarXML(SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=md_pet_usu_ext_cadastrar&id_orgao=' . $ids['orgao'] . '&id_uf=' . $ids['uf'] . '&id_tipo_procedimento=' . $dto->getNumIdTipoProcessoPeticionamento())) . '"';
                }

                $xml .= ' complemento="' . PaginaSEI::tratarHTML($dto->getStrOrientacoes()) . '"';
                $xml .= ' descricao="' . PaginaSEI::tratarHTML($dto->getStrNomeProcesso()) . '"';
                $xml .= '></item>';
            }
        }
        $xml .= '</itens>';

        return $xml;
    }


    //Tela Externa
    public static function montarSelectOrgaoTpProcesso($id = null, $orgao = null)
    {
        $idOrgao = array();
        $orgao = array();

        //Restrição Orgão
        $objMdPetTipoProcessoRN = new MdPetTipoProcessoRN();
        $arrRestricaoOrgao = $objMdPetTipoProcessoRN->restricaoOrgao();

        $objMdPetTipoProcessoDTO = new MdPetTipoProcessoDTO();
        $objMdPetTipoProcessoDTO->retTodos();
        if ($id != null) {
            $objMdPetTipoProcessoDTO->setNumIdTipoProcessoPeticionamento($id);
        }
        if ($id == null) {
            if (count($arrRestricaoOrgao)) {
                $objMdPetTipoProcessoDTO->setNumIdProcedimento($arrRestricaoOrgao, infraDTO::$OPER_NOT_IN);
            }
        }
        $objMdPetTipoProcessoRN = new MdPetTipoProcessoRN();
        $arrObjTipoProcedimentoFiltroDTO = $objMdPetTipoProcessoRN->listar($objMdPetTipoProcessoDTO);
        //$arrTipoPet = InfraArray::converterArrInfraDTO($arrObjTipoProcedimentoFiltroDTO, 'IdTipoProcessoPeticionamento');

        //Validação Cidade Unica
        $arrTipoProcessoOrgaoCidade = array();
        $arrIdTipoProcesso = array();
        foreach ($arrObjTipoProcedimentoFiltroDTO as $key => $tpProc) {
            if (!in_array($tpProc->getNumIdTipoProcessoPeticionamento(), $arrIdTipoProcesso)) {
                array_push($arrIdTipoProcesso, $tpProc->getNumIdTipoProcessoPeticionamento());
            }
        }

        $objMdPetRelTpProcessoUnidRN = new MdPetRelTpProcessoUnidRN();
        $objMdPetRelTpProcessoUnidDTO = new MdPetRelTpProcessoUnidDTO();
        $objMdPetRelTpProcessoUnidDTO->setNumIdTipoProcessoPeticionamento($arrIdTipoProcesso, InfraDTO::$OPER_IN);
        $objMdPetRelTpProcessoUnidDTO->retNumIdUnidade();
        $objMdPetRelTpProcessoUnidDTO->retNumIdOrgaoUnidade();
        $objMdPetRelTpProcessoUnidDTO->retNumIdCidadeContato();
        $objMdPetRelTpProcessoUnidDTO->retNumIdTipoProcessoPeticionamento();
        $objMdPetRelTpProcessoUnidDTO->retStrStaTipoUnidade();
        $arrobjMdPetRelTpProcessoUnidDTO = $objMdPetRelTpProcessoUnidRN->listar($objMdPetRelTpProcessoUnidDTO);

        foreach ($arrobjMdPetRelTpProcessoUnidDTO as $key => $objDTO) {
            //print_r($objDTO->getNumIdTipoProcessoPeticionamento()); die;
            if (!key_exists($objDTO->getNumIdTipoProcessoPeticionamento(), $arrTipoProcessoOrgaoCidade)) {
                $arrTipoProcessoOrgaoCidade[$objDTO->getNumIdTipoProcessoPeticionamento()] = array();
            }
            if (!key_exists($objDTO->getNumIdOrgaoUnidade(), $arrTipoProcessoOrgaoCidade[$objDTO->getNumIdTipoProcessoPeticionamento()])) {
                $arrTipoProcessoOrgaoCidade[$objDTO->getNumIdTipoProcessoPeticionamento()][$objDTO->getNumIdOrgaoUnidade()] = array();
            }

            if (!key_exists($objDTO->getNumIdCidadeContato(), $arrTipoProcessoOrgaoCidade[$objDTO->getNumIdTipoProcessoPeticionamento()][$objDTO->getNumIdOrgaoUnidade()])) {
                $arrTipoProcessoOrgaoCidade[$objDTO->getNumIdTipoProcessoPeticionamento()][$objDTO->getNumIdOrgaoUnidade()][$objDTO->getNumIdCidadeContato()] = 1;
            } else {
                $arrTipoProcessoOrgaoCidade[$objDTO->getNumIdTipoProcessoPeticionamento()][$objDTO->getNumIdOrgaoUnidade()][$objDTO->getNumIdCidadeContato()] = $arrTipoProcessoOrgaoCidade[$objDTO->getNumIdTipoProcessoPeticionamento()][$objDTO->getNumIdOrgaoUnidade()][$objDTO->getNumIdCidadeContato()] + 1;
            }


        }
        $arrIdsTpProcesso = array_keys($arrTipoProcessoOrgaoCidade);
        //verificando se existe algum tipo de processo com divergencia de orgao e cidade iguais
        if ($arrTipoProcessoOrgaoCidade) {
            $tipoProcessoDivergencia = false;
            foreach ($arrTipoProcessoOrgaoCidade as $key => $dados) {
                foreach ($dados as $cidade) {
                    foreach ($cidade as $qnt) {
                        if ($qnt > 1) {
                            foreach ($arrObjTipoProcedimentoFiltroDTO as $chaveTpProc => $tpProc) {
                                if ($tpProc->getNumIdTipoProcessoPeticionamento() == $key) {
                                    unset($arrObjTipoProcedimentoFiltroDTO[$chaveTpProc]);
                                    $chaveRemover = array_search($key, $arrIdsTpProcesso);
                                    unset($arrIdsTpProcesso[$chaveRemover]);
                                }
                            }
                        }
                    }
                }

            }
        }
        //Validação Cidade Unida - FIM

        $objMdPetRelTpProcessoUnidRN = new MdPetRelTpProcessoUnidRN();
        $objMdPetRelTpProcessoUnidDTO = new MdPetRelTpProcessoUnidDTO();
        $objMdPetRelTpProcessoUnidDTO->retTodos();
        $objMdPetRelTpProcessoUnidDTO->retNumIdUnidade();
        $objMdPetRelTpProcessoUnidDTO->retStrStaTipoUnidade();
        $objMdPetRelTpProcessoUnidDTO->setNumIdTipoProcessoPeticionamento($arrIdsTpProcesso, InfraDTO::$OPER_IN);
        $arrobjMdPetRelTpProcessoUnidDTO = $objMdPetRelTpProcessoUnidRN->listar($objMdPetRelTpProcessoUnidDTO);
        $arrIdsUnidade = InfraArray::converterArrInfraDTO($arrobjMdPetRelTpProcessoUnidDTO, 'IdUnidade');


        $objUnidadeDTO = new UnidadeDTO();
        $objUnidadeDTO->retNumIdOrgao();

        $objUnidadeDTO->setNumIdUnidade($arrIdsUnidade, InfraDTO::$OPER_IN);
        $objUnidadeRN = new UnidadeRN();
        $arrIdsOrgao = $objUnidadeRN->listarRN0127($objUnidadeDTO);
        $arrIdsOrgao = InfraArray::converterArrInfraDTO($arrIdsOrgao, 'IdOrgao');


        $objOrgaoDTO = new OrgaoDTO();
        $objOrgaoDTO->retStrSigla();
        $objOrgaoDTO->retNumIdOrgao();
        $objOrgaoDTO->setNumIdOrgao($arrIdsOrgao, InfraDTO::$OPER_IN);
	    $objOrgaoDTO->setStrSinConsultaProcessual('S');
        $objOrgaoRN = new OrgaoRN();
        $arrObjOrgaoRN = $objOrgaoRN->listarRN1353($objOrgaoDTO);


        foreach ($arrObjOrgaoRN as $key => $value) {
            $idOrgao[] = $value->getNumIdOrgao();
            $orgao[] = $value->getStrSigla();
        }


        return array($idOrgao, $orgao);
    }


    //Ajax UF
    public static function montarSelectOrgaoTpProcessoOrgaoUf($idOrgao)
    {

        $arrRestricaoOrgao = null;
        ////Restrição Orgão

        if (!array_key_exists("idTpProc", $idOrgao)) {
            $objMdPetTipoProcessoRN = new MdPetTipoProcessoRN();
            $arrRestricaoOrgao = $objMdPetTipoProcessoRN->restricaoOrgao();
        }
        //Restrição Orgão - FIM

        $objMdPetTipoProcessoDTO = new MdPetTipoProcessoDTO();
        $objMdPetTipoProcessoDTO->retTodos();
        if (!is_null($arrRestricaoOrgao) && count($arrRestricaoOrgao) > 0) {
            $objMdPetTipoProcessoDTO->setNumIdProcedimento($arrRestricaoOrgao, infraDTO::$OPER_NOT_IN);
        }
        if (array_key_exists("idTpProc", $idOrgao)) {
            $objMdPetTipoProcessoDTO->setNumIdTipoProcessoPeticionamento($idOrgao['idTpProc']);
        }
        $objMdPetTipoProcessoRN = new MdPetTipoProcessoRN();
        $arrObjMdPetTipoProcessoRN = $objMdPetTipoProcessoRN->listar($objMdPetTipoProcessoDTO);
        $arrTipoPet = InfraArray::converterArrInfraDTO($arrObjMdPetTipoProcessoRN, 'IdTipoProcessoPeticionamento');

        //Cidade Duplicada
        //UFVALIDACAO
        $objMdPetTipoProcessoRN = new MdPetTipoProcessoRN();
        $arrCidadeDuplicada = $objMdPetTipoProcessoRN->peticionamentoNovoCidadeDuplicada($arrObjMdPetTipoProcessoRN);

        //Cidade Duplicada - FIM

        $objMdPetRelTpProcessoUnidRN = new MdPetRelTpProcessoUnidRN();
        $objMdPetRelTpProcessoUnidDTO = new MdPetRelTpProcessoUnidDTO();
        $objMdPetRelTpProcessoUnidDTO->retNumIdUnidade();
        $objMdPetRelTpProcessoUnidDTO->retNumIdTipoProcessoPeticionamento();
        $objMdPetRelTpProcessoUnidDTO->retStrStaTipoUnidade();
        $objMdPetRelTpProcessoUnidDTO->setNumIdTipoProcessoPeticionamento($arrCidadeDuplicada, InfraDTO::$OPER_IN);
        $arrobjMdPetRelTpProcessoUnidDTO = $objMdPetRelTpProcessoUnidRN->listar($objMdPetRelTpProcessoUnidDTO);
        $arrIdsUnidade = InfraArray::converterArrInfraDTO($arrobjMdPetRelTpProcessoUnidDTO, 'IdUnidade');


        //Validação Cidade Unica
        //$objTipoProcedimentoRN = new MdPetTipoProcessoRN();
        //$arrIdsUnidade = $objTipoProcedimentoRN->validacaoCidadeDuplcada($arrobjMdPetRelTpProcessoUnidDTO);


        $objUnidadeDTO = new UnidadeDTO();
        $objUnidadeDTO->setNumIdOrgao($idOrgao['idOrgao']);
        $objUnidadeDTO->retNumIdContato();
        $objUnidadeDTO->setNumIdUnidade($arrIdsUnidade, InfraDTO::$OPER_IN);
        $objUnidadeRN = new UnidadeRN();
        $arrIdsContato = $objUnidadeRN->listarRN0127($objUnidadeDTO);
        $arrIdsContato = InfraArray::converterArrInfraDTO($arrIdsContato, 'IdContato');


        $objContatoDTO = new ContatoDTO();
        $objContatoDTO->setNumIdContato($arrIdsContato, InfraDTO::$OPER_IN);
        $objContatoDTO->retNumIdUf();
        $objContatoDTO->retStrSiglaUf();
        $objContatoDTO->retStrNomeCidade();
        $objContatoDTO->setOrdStrSiglaUf(InfraDTO::$TIPO_ORDENACAO_ASC);
        $objContatoRN = new ContatoRN();
        $arrIdsContato = $objContatoRN->listarRN0325($objContatoDTO);
        $arrIdsContatoDistinct = infraArray::distinctArrInfraDTO($arrIdsContato, 'IdUf');

        return $arrIdsContatoDistinct;

    }


    //Ajax Cidade
    public static function montarSelectOrgaoTpProcessoOrgaoCidade($id)
    {

        //Restrição Orgão
        $objMdPetTipoProcessoRN = new MdPetTipoProcessoRN();
        $arrRestricaoOrgao = $objMdPetTipoProcessoRN->restricaoOrgao();

        $objMdPetTipoProcessoDTO = new MdPetTipoProcessoDTO();
        $objMdPetTipoProcessoDTO->retTodos();
        if (count($arrRestricaoOrgao)) {
            $objMdPetTipoProcessoDTO->setNumIdProcedimento($arrRestricaoOrgao, infraDTO::$OPER_NOT_IN);
        }
        $objMdPetTipoProcessoRN = new MdPetTipoProcessoRN();
        $arrObjMdPetTipoProcessoRN = $objMdPetTipoProcessoRN->listar($objMdPetTipoProcessoDTO);
        $arrTipoPet = InfraArray::converterArrInfraDTO($arrObjMdPetTipoProcessoRN, 'IdTipoProcessoPeticionamento');

        //Cidade Duplicada
        $objMdPetTipoProcessoRN = new MdPetTipoProcessoRN();
        $arrCidadeDuplicada = $objMdPetTipoProcessoRN->peticionamentoNovoCidadeDuplicada($arrObjMdPetTipoProcessoRN);
        //Cidade Duplicada - FIM

        $objMdPetRelTpProcessoUnidRN = new MdPetRelTpProcessoUnidRN();
        $objMdPetRelTpProcessoUnidDTO = new MdPetRelTpProcessoUnidDTO();
        $objMdPetRelTpProcessoUnidDTO->retTodos();
        $objMdPetRelTpProcessoUnidDTO->retNumIdUnidade();
        $objMdPetRelTpProcessoUnidDTO->retStrStaTipoUnidade();
        $objMdPetRelTpProcessoUnidDTO->setNumIdTipoProcessoPeticionamento($arrCidadeDuplicada, InfraDTO::$OPER_IN);
        $arrobjMdPetRelTpProcessoUnidDTO = $objMdPetRelTpProcessoUnidRN->listar($objMdPetRelTpProcessoUnidDTO);
        $arrIdsUnidade = InfraArray::converterArrInfraDTO($arrobjMdPetRelTpProcessoUnidDTO, 'IdUnidade');

        $objUnidadeDTO = new UnidadeDTO();
        //Ajustar
        if ($id['idOrgao'] != '') {
            $objUnidadeDTO->setNumIdOrgao($id['idOrgao']);
        }
        $objUnidadeDTO->retNumIdContato();
        $objUnidadeDTO->setNumIdUnidade($arrIdsUnidade, InfraDTO::$OPER_IN);
        $objUnidadeRN = new UnidadeRN();
        $arrIdsContato = $objUnidadeRN->listarRN0127($objUnidadeDTO);
        $arrIdsContato = InfraArray::converterArrInfraDTO($arrIdsContato, 'IdContato');


        $objContatoDTO = new ContatoDTO();
        $objContatoDTO->setNumIdContato($arrIdsContato, InfraDTO::$OPER_IN);
        $objContatoDTO->setNumIdUf($id['idUf']);
        $objContatoDTO->retStrNomeCidade();
        $objContatoDTO->retNumIdCidade();
        $objContatoDTO->setOrdStrNomeCidade(InfraDTO::$TIPO_ORDENACAO_ASC);
        $objContatoRN = new ContatoRN();
        $arrIdsContato = $objContatoRN->listarRN0325($objContatoDTO);

        $arrIdsContatoDistinct = infraArray::distinctArrInfraDTO($arrIdsContato, 'IdCidade');


        return $arrIdsContatoDistinct;

    }

    public static function montarSelectHipoteseLegal($strPrimeiroItemValor, $strPrimeiroItemDescricao, $strValorItemSelecionado)
    {

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
            $objEntradaListarHipotesesLegaisAPI = new EntradaListarHipotesesLegaisAPI();
            $objEntradaListarHipotesesLegaisAPI->setNivelAcesso(ProtocoloRN::$NA_RESTRITO);

            $objSeiRN = new SeiRN();
            $arrHipoteseLegalAPI = $objSeiRN->listarHipotesesLegais($objEntradaListarHipotesesLegaisAPI);

            $stringFim = '<option value=""> </option>';
            if (count($arrHipoteseLegalAPI) > 0) {
                foreach ($arrHipoteseLegalAPI as $hipoteseLegalAPI) {

                    $idHipoteseLegal = $hipoteseLegalAPI->getIdHipoteseLegal();

                    if (!is_null($strValorItemSelecionado) && $strValorItemSelecionado == $idHipoteseLegal) {
                        $stringFim .= '<option value="' . $idHipoteseLegal . '" selected="selected">' . $hipoteseLegalAPI->getNome() . ' (' . $hipoteseLegalAPI->getBaseLegal() . ')';
                    } else {
                        $stringFim .= '<option value="' . $idHipoteseLegal . '">' . $hipoteseLegalAPI->getNome() . ' (' . $hipoteseLegalAPI->getBaseLegal() . ')';
                    }
                    $stringFim .= '</option>';

                }
            }
        }

        return $stringFim;
    }

    public static function validarNivelAcesso($params)
    {
	
	    $objMdPetCriterioDTO = new MdPetCriterioDTO();
	    $objMdPetCriterioDTO->retNumIdTipoProcedimento();
	    $objMdPetCriterioDTO->retStrNomeProcesso();
	    $objMdPetCriterioDTO->setStrSinCriterioPadrao('N');
	    $objMdPetCriterioDTO->setDistinct(true);
	    $arrObjMdPetCriterioDTO = (new MdPetCriterioRN())->listar($objMdPetCriterioDTO);
	
	    $idsTiposProcedimentosCadastrados = InfraArray::converterArrInfraDTO($arrObjMdPetCriterioDTO,'IdTipoProcedimento');
	
	    $arrTipoProcessoTmp = array_unique(PaginaSEI::getInstance()->getArrValuesSelect($params['hdnIdTipoProcesso']));
	    $arrTipoProcedimento = [];
	
	    foreach ($arrTipoProcessoTmp as $tipoProcesso) {
		    if (!in_array((int)$tipoProcesso, $idsTiposProcedimentosCadastrados)) {
			    $arrTipoProcedimento[] = (int)$tipoProcesso;
		    }
	    }
	    
        $objNivelAcessoRN = new NivelAcessoPermitidoRN();
        $objNivelAcessoDTO = new NivelAcessoPermitidoDTO();
        $objNivelAcessoDTO->retTodos();
        $objNivelAcessoDTO->setOrd('StaNivelAcesso', InfraDTO::$TIPO_ORDENACAO_ASC);
        $arrayDescricoes = array(
            'P' => 'Público',
            'I' => 'Restrito'
        );
        $msg = '';
        $xml = '<Validacao>';
        $staTipoNivelAcesso = 1;
        if ($params['selNivelAcesso'] == 'P') {
            $staTipoNivelAcesso = 0;
        }
        if (!empty($arrTipoProcedimento)) {
            foreach ($arrTipoProcedimento as $tipoProcedimento) {
                $objNivelAcessoDTO->setNumIdTipoProcedimento($tipoProcedimento);
                $objNivelAcessoDTO->setStrStaNivelAcesso($staTipoNivelAcesso);
                $contador = $objNivelAcessoRN->contar($objNivelAcessoDTO);
                if ($contador <= 0) {
                    $msg .= $tipoProcedimento[1] . "\r\n";
                }
            }
        } else {
            $objNivelAcessoDTO->setNumIdTipoProcedimento($params['txtTipoProcesso']);
            $objNivelAcessoDTO->setStrStaNivelAcesso($staTipoNivelAcesso);
            $contador = $objNivelAcessoRN->contar($objNivelAcessoDTO);
            if ($contador <= 0) {
                $objTipoProcedimentoConsultaDTO = new TipoProcedimentoDTO();
                $objTipoProcedimentoConsultaDTO->setNumIdTipoProcedimento($params['txtTipoProcesso']);
                $objTipoProcedimentoConsultaDTO->retTodos();
                $objTipoProcedimentoRN = new TipoProcedimentoRN();
                $objTipoProcedimentoDTO = $objTipoProcedimentoRN->consultarRN0267($objTipoProcedimentoConsultaDTO);
                $msg .= $objTipoProcedimentoDTO->getStrNome() . "\r\n";
            }
        }

        if ($msg != '') {
            $msg = 'O Critério não pôde ser cadastrado, pois os Tipo de Processo abaixo não permite o Nível de Acesso [ ' . $arrayDescricoes[$params['selNivelAcesso']] . " ]\n\r" . $msg;
            $xml .= '<MensagemValidacao>' . $msg . '</MensagemValidacao>';
        }

        $xml .= '</Validacao>';
        return $xml;
    }

    public static function montarSelectNivelAcesso($strPrimeiroItemValor, $strPrimeiroItemDescricao, $strValorItemSelecionado, $idTipoProcedimento = null)
    {
        $objNivelAcessoRN = new NivelAcessoPermitidoRN();

        $objNivelAcessoDTO = new NivelAcessoPermitidoDTO();
        $objNivelAcessoDTO->retTodos();
        $objNivelAcessoDTO->setOrd('StaNivelAcesso', InfraDTO::$TIPO_ORDENACAO_ASC);

        if ($idTipoProcedimento != null) {
            if (!is_int($idTipoProcedimento)) {
                $arrTipoProcedimento = PaginaSEI::getInstance()->getArrItensTabelaDinamica($idTipoProcedimento);
                $arrIdTipoProcedimento = array();
                foreach ($arrTipoProcedimento as $tipoProcedimento) {
                    $arrIdTipoProcedimento[] = $tipoProcedimento[0];
                }

                $objNivelAcessoDTO->adicionarCriterio(array('IdTipoProcedimento'), array(InfraDTO::$OPER_IN), array($arrIdTipoProcedimento));
                $objNivelAcessoDTO->setDistinct(true);
            } else {
                $objNivelAcessoDTO->setNumIdTipoProcedimento($idTipoProcedimento);
            }
        }

        $arrObjNivelAcessoDTO = $objNivelAcessoRN->listar($objNivelAcessoDTO);

        // removendo as duplicidades na colecao de objetos
        $arrObjNivelAcessoUnicoDTO = array();
        foreach ($arrObjNivelAcessoDTO as $objNivelAcessoDTO) {
            $arrObjNivelAcessoUnicoDTO[$objNivelAcessoDTO->getStrStaNivelAcesso()] = $objNivelAcessoDTO;
        }

        //montarItemSelect
        $stringFim = '';
        $arrayDescricoes = array();
        $arrayDescricoes[ProtocoloRN::$NA_PUBLICO] = 'Público';
        $arrayDescricoes[ProtocoloRN::$NA_RESTRITO] = 'Restrito';
        $arrayDescricoes[''] = '';

        $stringFim = '<option value=""> </option>';

        if (count($arrObjNivelAcessoUnicoDTO) > 0) {
            foreach ($arrObjNivelAcessoUnicoDTO as $objNivelAcessoDTO) {

                if ($objNivelAcessoDTO->getStrStaNivelAcesso() != ProtocoloRN::$NA_SIGILOSO) {

                    $stringFim .= '<option value="' . $objNivelAcessoDTO->getStrStaNivelAcesso() . '"';

                    if (!is_null($strValorItemSelecionado) && ($strValorItemSelecionado == $objNivelAcessoDTO->getStrStaNivelAcesso())) {
                        $stringFim .= 'selected = selected';
                    }

                    $stringFim .= '>';
                    $stringFim .= $arrayDescricoes[$objNivelAcessoDTO->getStrStaNivelAcesso()];

                    $stringFim .= '</option>';
                }

            }
        }

        return $stringFim;
    }

    public static function validarTipoProcessoComAssunto($params)
    {
        $msg = '';
        $xml = '<Validacao>';
        $relTipoProcedimentoDTO = new RelTipoProcedimentoAssuntoDTO();
        $relTipoProcedimentoDTO->retTodos();
        $relTipoProcedimentoRN = new RelTipoProcedimentoAssuntoRN();

        if ($params['hdnIdTipoProcesso'] != '') {
            $arrTipoProcedimento = PaginaSEI::getInstance()->getArrItensTabelaDinamica($params['hdnIdTipoProcesso']);
            foreach ($arrTipoProcedimento as $tipoProcedimento) {
                $relTipoProcedimentoDTO->setNumIdTipoProcedimento($tipoProcedimento[0]);
                $arrLista = $relTipoProcedimentoRN->listarRN0192($relTipoProcedimentoDTO);

                if (!is_array($arrLista) || count($arrLista) == 0) {
                    $objTipoProcedimentoConsultaDTO = new TipoProcedimentoDTO();
                    $objTipoProcedimentoConsultaDTO->setNumIdTipoProcedimento($tipoProcedimento[0]);
                    $objTipoProcedimentoConsultaDTO->retTodos();
                    $objTipoProcedimentoRN = new TipoProcedimentoRN();
                    $objTipoProcedimentoDTO = $objTipoProcedimentoRN->consultarRN0267($objTipoProcedimentoConsultaDTO);
                    $msg .= $objTipoProcedimentoDTO->getStrNome() . "\r\n";
                }
            }
        } else {
            $relTipoProcedimentoDTO->setNumIdTipoProcedimento($params['txtTipoProcesso']);
            $arrLista = $relTipoProcedimentoRN->listarRN0192($relTipoProcedimentoDTO);
            if (!is_array($arrLista) || count($arrLista) == 0) {
                $objTipoProcedimentoConsultaDTO = new TipoProcedimentoDTO();
                $objTipoProcedimentoConsultaDTO->setNumIdTipoProcedimento($params['txtTipoProcesso']);
                $objTipoProcedimentoConsultaDTO->retTodos();
                $objTipoProcedimentoRN = new TipoProcedimentoRN();
                $objTipoProcedimentoDTO = $objTipoProcedimentoRN->consultarRN0267($objTipoProcedimentoConsultaDTO);
                $msg .= $objTipoProcedimentoDTO->getStrNome() . "\r\n";
            }
        }

        if ($msg != '') {
            $msg = 'O Critério não pôde ser cadastrado, pois existe Tipo de Processo que não possue indicação de pelo menos uma sugestão de assunto' . "\n\r" . $msg;
            $xml .= '<MensagemValidacao>' . $msg . '</MensagemValidacao>';
        }

        $xml .= '</Validacao>';
        return $xml;
    }

    public static function autoCompletarTipoProcedimento($strPalavrasPesquisa, $itensSelecionados = null)
    {
        $objTipoProcedimentoDTO = new TipoProcedimentoDTO();
        $objTipoProcedimentoDTO->retNumIdTipoProcedimento();
        $objTipoProcedimentoDTO->retStrNome();
        $objTipoProcedimentoDTO->setOrd('Nome', InfraDTO::$TIPO_ORDENACAO_ASC);

        $objTipoProcedimentoRN = new TipoProcedimentoRN();
        $arrObjTipoProcedimentoDTO = $objTipoProcedimentoRN->listarRN0244($objTipoProcedimentoDTO);


        if ($strPalavrasPesquisa != '' || $itensSelecionados != null) {
            $ret = array();
            $strPalavrasPesquisa = strtolower($strPalavrasPesquisa);
            foreach ($arrObjTipoProcedimentoDTO as $objTipoProcedimentoDTO) {
                if ($itensSelecionados != null && in_array($objTipoProcedimentoDTO->getNumIdTipoProcedimento(), $itensSelecionados)) {
                    continue;
                }
                if ($strPalavrasPesquisa != '' && strpos(strtolower($objTipoProcedimentoDTO->getStrNome()), $strPalavrasPesquisa) === false) {
                    continue;
                }

                //checando se o tipo de processo informado possui sugestao de assunto

                $rnAssunto = new RelTipoProcedimentoAssuntoRN();
                $dto = new RelTipoProcedimentoAssuntoDTO();
                $dto->retTodos();
                $dto->setNumIdTipoProcedimento($objTipoProcedimentoDTO->getNumIdTipoProcedimento());

                $arrAssuntos = $rnAssunto->listarRN0192($dto);

                if (is_array($arrAssuntos) && count($arrAssuntos) > 0) {
                    $ret[] = $objTipoProcedimentoDTO;
                }
            }
        }
        return $ret;
    }

    public static function gerarXMLItensArrInfraApi($arr, $strAtributoId, $strAtributoDescricao, $strAtributoComplemento = null, $strAtributoGrupo = null)
    {
        $metodoAtributoId = "get{$strAtributoId}";
        $metodoAtributoDescricao = "get{$strAtributoDescricao}";
        $metodoAtributoComplemento = "get{$strAtributoComplemento}";
        $metodoAtributoGrupo = "get{$strAtributoGrupo}";

        $xml = '';
        $xml .= '<itens>';
        if ($arr !== null) {
            foreach ($arr as $dto) {
                $xml .= '<item id="' . self::formatarXMLAjax($dto->$metodoAtributoId()) . '"';
                $xml .= ' descricao="' . self::formatarXMLAjax($dto->$metodoAtributoDescricao()) . '"';

                if ($strAtributoComplemento !== null) {
                    $xml .= ' complemento="' . self::formatarXMLAjax($dto->$metodoAtributoComplemento()) . '"';
                }

                if ($strAtributoGrupo !== null) {
                    $xml .= ' grupo="' . self::formatarXMLAjax($dto->$metodoAtributoGrupo()) . '"';
                }

                $xml .= '></item>';
            }
        }
        $xml .= '</itens>';
        return $xml;
    }

    private static function formatarXMLAjax($str)
    {
        if (!is_numeric($str)) {
            $str = str_replace('&', '&amp;', $str);
            $str = str_replace('<', '&amp;lt;', $str);
            $str = str_replace('>', '&amp;gt;', $str);
            $str = str_replace('\"', '&amp;quot;', $str);
            $str = str_replace('"', '&amp;quot;', $str);
            //$str = str_replace("\n",'_',$str);
        }
        return $str;
    }

}