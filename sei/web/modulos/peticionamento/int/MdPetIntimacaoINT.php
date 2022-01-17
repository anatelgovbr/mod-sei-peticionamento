<?

/**
 * TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
 *
 * 14/03/2017 - criado por pedro.cast
 *
 * Versão do Gerador de Código: 1.40.0
 */
require_once dirname(__FILE__) . '/../../../SEI.php';

class MdPetIntimacaoINT extends InfraINT {

    public static function validarCadastro($arr) {
        $objMdPetIntimacaoRN = new MdPetIntimacaoRN();
        $arrParams = $objMdPetIntimacaoRN->realizarValidacoesCadastroIntimacao($arr);
        $xml = '<Dados>\n';
        $xml .= '<Mensagem>' . $arrParams[0] . '</Mensagem>';
        $xml .= '<Impeditivo>' . ($arrParams[1] ? 'S' : 'N') . '</Impeditivo>';
        $xml .= '<Alerta>' . ($arrParams[2] ? 'S' : 'N') . '</Alerta>';
        $xml .= '</Dados>';

        return $xml;
    }

    public static function montarSelectIdMdPetIntimacao($strPrimeiroItemValor, $strPrimeiroItemDescricao, $strValorItemSelecionado, $numIdMdPetIntTipoIntimacao = '') {
        $objMdPetIntimacaoDTO = new MdPetIntimacaoDTO();
        $objMdPetIntimacaoDTO->retNumIdMdPetIntimacao();
        $objMdPetIntimacaoDTO->retNumIdMdPetIntimacao();

        if ($numIdMdPetIntTipoIntimacao !== '') {
            $objMdPetIntimacaoDTO->setNumIdMdPetIntTipoIntimacao($numIdMdPetIntTipoIntimacao);
        }

        $objMdPetIntimacaoDTO->setOrdNumIdMdPetIntimacao(InfraDTO::$TIPO_ORDENACAO_ASC);

        $objMdPetIntimacaoRN = new MdPetIntimacaoRN();
        $arrObjMdPetIntimacaoDTO = $objMdPetIntimacaoRN->listar($objMdPetIntimacaoDTO);

        return parent::montarSelectArrInfraDTO($strPrimeiroItemValor, $strPrimeiroItemDescricao, $strValorItemSelecionado, $arrObjMdPetIntimacaoDTO, 'IdMdPetIntimacao', 'IdMdPetIntimacao');
    }

    public static function montarSelectSituacaoIntimacao($selected = '') {
        $objMdPetIntAceiteRN = new MdPetIntAceiteRN();
        $arr = $objMdPetIntAceiteRN->retornaArraySituacaoIntimacao();
        asort($arr);

        $select = '<option value=""> </option>';

        if (count($arr) > 0) {
            foreach ($arr as $key => $item) {
                $add = '';
                if ($selected != '' && $key == $selected) {
                    $add = 'selected = selected';
                }

                $select .= '<option ' . $add . ' value="' . $key . '">' . $item . '</option>';
            }
        }

        return $select;
    }

    public static function getSituacoesListaExterno() {
        $strReturn = '<option value="">  </option>';
        $strOption = '<option value="[valor]"> [descricaoSituacao] </option>';
        $strOptionSel = '<option selected="selected" value="[valor]"> [descricaoSituacao] </option>';
        $arrSituacoes = MdPetIntRelDestinatarioINT::getArraySituacaoRelatorio();
        foreach ($arrSituacoes as $key => $situacao) {
            if ($key != MdPetIntimacaoRN::$INTIMACAO_PRAZO_VENCIDO) {
                //Verifica se no post temos id de situação para aplicar no filtro
                $hdnSituacao = array_key_exists('selCumprimentoIntimacao', $_POST) ? $_POST['selCumprimentoIntimacao'] : null;

                //Verifica se esse valor deve ser selecionado
                $qtdHdnSituacao = (is_array($hdnSituacao) ? count($hdnSituacao) : 0);
                if ($qtdHdnSituacao > 0) {
                    $strOptionCorreta = $key == $hdnSituacao ? $strOptionSel : $strOption;
                } else {
                    $strOptionCorreta = $key == MdPetIntimacaoRN::$TODAS ? $strOptionSel : $strOption;
                }

                $add = str_replace('[valor]', $key, $strOptionCorreta);
                $add = str_replace('[descricaoSituacao]', $situacao, $add);
                $strReturn .= '' . $add;
            }
        }

        return $strReturn;
    }

    public static function validarProcuracao($idRelDest, $idProcedimento) {
        //Recupera o id contato do usuário logado
        $idUsuarioExterno = SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno();
        $usuarioDTO = new UsuarioDTO();
        $usuarioRN = new UsuarioRN();
        $usuarioDTO->retNumIdContato();
        $usuarioDTO->setNumIdUsuario($idUsuarioExterno);
        $contatoExterno = $usuarioRN->consultarRN0489($usuarioDTO);
        $idContatoExterno = $contatoExterno->getNumIdContato();

        //recupera os dados de relacionamento 
        $dtoMdPetIntRelDestinatarioDTO = new MdPetIntRelDestinatarioDTO();
        $rnMdPetIntRelDestinatarioDTO = new MdPetIntRelDestinatarioRN();
        $dtoMdPetIntRelDestinatarioDTO->setNumIdMdPetIntRelDestinatario($idRelDest);
        $dtoMdPetIntRelDestinatarioDTO->retNumIdContato();
        $arrObjMdPetIntRelDestinatarioDTO = $rnMdPetIntRelDestinatarioDTO->consultar($dtoMdPetIntRelDestinatarioDTO);
        $xml = '';
        $xml .= '<resposta>';

        //Caso o contato do usuário logado seja diferente do contato da intimação 
        //é verificado a procuração do mesmo
        if ($arrObjMdPetIntRelDestinatarioDTO->getNumIdContato() != $idContatoExterno) {
            //recupera os dados da procuração do mesmo
            $dtoMdPetVincReptDTO = new MdPetVincRepresentantDTO();
            $dtoMdPetVincReptDTO->setNumIdContato($idContatoExterno);
            $dtoMdPetVincReptDTO->setNumIdContatoVinc($arrObjMdPetIntRelDestinatarioDTO->getNumIdContato());
            $dtoMdPetVincReptDTO->retNumIdMdPetVinculoRepresent();
            $dtoMdPetVincReptDTO->retStrTipoRepresentante();
            $dtoMdPetVincReptDTO->retNumIdContatoVinc();
            $dtoMdPetVincReptDTO->retDthDataLimite();
            $dtoMdPetVincReptDTO->retStrStaEstado();
            $dtoMdPetVincReptDTO->retStrStaAbrangencia();
            $dtoMdPetVincReptDTO->retNumIdMdPetVinculoRepresent();
            $dtoMdPetVincReptDTO->setStrSinAtivo('S');
            $dtoMdPetVincReptDTO->setStrStaEstado(MdPetVincRepresentantRN::$RP_ATIVO);
            $rnMdPetVincRepRN = new MdPetVincRepresentantRN();
            $arrObjMdPetVincRepresentantDTO = $rnMdPetVincRepRN->listar($dtoMdPetVincReptDTO);

            if (count($arrObjMdPetVincRepresentantDTO)) {
                foreach ($arrObjMdPetVincRepresentantDTO as $value) {
                    //Caso o tipo de procuração seja "Simples" será necessário 
                    //fazer algumas validações para liberar o usuário responder a intimação
                    if ($value->getStrTipoRepresentante() == 'S') {
                        $rnMdPetIntimacaoRN = new MdPetIntimacaoRN();
                        $verificacaoCriteriosProcuracaoSimples = $rnMdPetIntimacaoRN->_verificarCriteriosProcuracaoSimples($value->getNumIdMdPetVinculoRepresent(), $value->getStrStaEstado(), $value->getDthDataLimite(), null, $value->getStrStaAbrangencia(), $idProcedimento);
                        if ($verificacaoCriteriosProcuracaoSimples) {
                            $verificacao = "<valor>T</valor>";
                        } else {
                            $verificacao = "<valor>F</valor>";
                        }
                        //Caso a procuração seja especial, como já foi verificado que a mesma é ativa na consulta
                        //o contato pode responder a intimação 
                    } else {
                        $verificacao = "<valor>T</valor>";
                    }                    

                    $url = SessaoSEIExterna::getInstance()->assinarLink(ConfiguracaoSEI::getInstance()->getValor('SEI', 'URL') . '/controlador_externo.php?acao=md_pet_intimacao_usu_ext_negar_resposta_peticionar&id_contato=' . $value->getNumIdContatoVinc());
                    $contatoVinculado = "<contato>" . PaginaSEI::tratarHTML($url) . "</contato>";
                }

                //Caso não tenha procuração ativa, o mesmo não pode responder a intimação    
            } else {
                $verificacao = "<valor>F</valor>";
            }
            // O contato da intimação pode responder a mesma
        } else {
            $verificacao = "<valor>T</valor>";
        }
        $xml .= $verificacao;
        $xml .= $contatoVinculado;
        $xml .= "</resposta>";
        return $xml;
    }

}

?>