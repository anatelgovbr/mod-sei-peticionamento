<?php
switch ($_GET['acao']) {
        
    	case 'md_pet_responder_intimacao_usu_ext':

            $strTitulo     = "Peticionamento de Resposta a Intimação Eletrônica";
            $arrComandos[] = '<button type="button" accesskey="P" name="btnResponder"  onclick = "responderIntimacao()" class="infraButton"><span class="infraTeclaAtalho">P</span>eticionar</button>';
            $arrComandos[] = '<button type="button" accesskey="C" id="btnFechar" class="infraButton" onclick="fechar()">Fe<span class="infraTeclaAtalho">c</span>har</button>';

            $idProcedimento   = $_GET['id_procedimento'];
            $idMdPetIntimacao = $_GET['id_intimacao'];
            $idMdPetIntAceite = $_GET['id_aceite'];

            $objProcedimentoDTO = new ProcedimentoDTO();
            $objProcedimentoDTO->setDblIdProcedimento($idProcedimento);
            $objProcedimentoDTO->retNumIdTipoProcedimento();
            $objProcedimentoDTO->retStrNomeTipoProcedimento();
            $objProcedimentoRN  = new ProcedimentoRN();
            $objProcedimentoDTO = $objProcedimentoRN->consultarRN0201($objProcedimentoDTO);
            $idTipoProcedimento = $objProcedimentoDTO->getNumIdTipoProcedimento();

            $objMdPetCriterioDTO = new MdPetCriterioDTO();
            $objMdPetCriterioDTO->retNumIdCriterioIntercorrentePeticionamento();
            $objMdPetCriterioDTO->setStrSinCriterioPadrao('N');
            $objMdPetCriterioDTO->setNumIdTipoProcedimento($idTipoProcedimento);
            $objMdPetCriterioRN  = new MdPetCriterioRN();
            $qtdMdPetCriterioDTO = $objMdPetCriterioRN->contar($objMdPetCriterioDTO);

            $strTipoProcessoPeticionamento = 'Novo Processo';
            if ($qtdMdPetCriterioDTO > 0) {
                $strTipoProcessoPeticionamento = 'Direto no Processo Indicado';
            }

            $objMdPetIntDestRespostaRN = new MdPetIntDestRespostaRN();
            $exibirHipoteseLegal = $objMdPetIntDestRespostaRN->verificarHipoteseLegal();
            
            $arrHipoteseNivel = $objMdPetIntDestRespostaRN->verificarCriterioIntercorrente($idTipoProcedimento);
           
            $selHipoteseLegal = MdPetIntercorrenteINT::montarSelectHipoteseLegalRespostaIntimacao();
            
            //Documento Principal
            $objMdPetIntDocumentoDTO = new MdPetIntProtocoloDTO();
            $objMdPetIntDocumentoDTO->setNumIdMdPetIntimacao($idMdPetIntimacao);
            $objMdPetIntDocumentoDTO->setStrSinPrincipal('S');
            $objMdPetIntDocumentoDTO->retTodos(true);
            $objMdPetIntDocumentoRN  = new MdPetIntProtocoloRN();
            $objMdPetIntDocumentoDTO = $objMdPetIntDocumentoRN->consultar($objMdPetIntDocumentoDTO);

            //Aceite
            $objMdPetIntAceiteDTO = new MdPetIntAceiteDTO();
            $objMdPetIntAceiteDTO->setNumIdMdPetIntAceite($idMdPetIntAceite);
            $objMdPetIntAceiteDTO->retTodos();
            $objMdPetIntAceiteRN  = new MdPetIntAceiteRN();
            $objMdPetIntAceiteDTO = $objMdPetIntAceiteRN->consultar($objMdPetIntAceiteDTO);

            //Data Intimacao
            $objMdPetIntRelDestinatarioRN = new MdPetIntRelDestinatarioRN();
            $strDataIntimacao = $objMdPetIntRelDestinatarioRN->consultarDadosIntimacao($idMdPetIntimacao);

            //Informações Fieldset Intimação
            $strNumeroProcesso    = $objMdPetIntDocumentoDTO->getStrProtocoloFormatadoProcedimento();
            $strNomeTipoIntimacao = $objMdPetIntDocumentoDTO->getStrNomeTipoIntimacao();
            $strNumeroProcesso .= ' (' . $objProcedimentoDTO->getStrNomeTipoProcedimento() . ')';
            $strNomeDocumentoPrincipal = $objMdPetIntDocumentoDTO->getStrNomeSerie();
            $strNomeDocumentoPrincipal .= ' ' . $objMdPetIntDocumentoDTO->getStrNumeroDocumento() . ' (' . $objMdPetIntDocumentoDTO->getStrProtocoloFormatadoDocumento() . ')';

            $arrData = explode(" ", $objMdPetIntAceiteDTO->getDthData());
            $strDataCumprimento = count($arrData) > 0 ? current($arrData) : null;

            $strTipoCumprimento = $objMdPetIntAceiteDTO->getStrTipoAceite() == MdPetIntimacaoRN::$TP_MANUAL_USUARIO_EXTERNO_ACEITE ? MdPetIntimacaoRN::$STR_INTIMACAO_CUMPRIDA_POR_ACESSO_ACEITE : MdPetIntimacaoRN::$STR_INTIMACAO_CUMPRIDA_PRAZO_ACEITE;

            //reaproveitando funcionalidade já em uso no Intercorrente para saber a situaçao do processo
            $stRespostaIntimacao = true;
            $xml = MdPetIntercorrenteINT::gerarXMLvalidacaoNumeroProcesso( $objMdPetIntDocumentoDTO->getStrProtocoloFormatadoProcedimento() , $stRespostaIntimacao);
            $arr = MdPetIntercorrenteINT::xmlToArray( utf8_encode( $xml ) );
            $strTipoProcessoPeticionamento = $arr['ProcessoIntercorrente'];


			$objMdPetIntimacaoRN = new MdPetIntimacaoRN();
			$objUnidadeDTO       = $objMdPetIntimacaoRN->getUnidadeIntimacao(array($idMdPetIntimacao));

			SessaoSEI::getInstance()->simularLogin(null, null, SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno(), $objUnidadeDTO->getNumIdUnidade());

			$objUsuarioDTO = new UsuarioDTO();
			$objUsuarioDTO->retNumIdContato();
			$objUsuarioDTO->setNumIdUsuario(SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno());
			$objUsuarioRN  = new UsuarioRN();
			$objUsuarioDTO = $objUsuarioRN->consultarRN0489($objUsuarioDTO);

			$objMdPetIntRelDestinatarioDTO = new MdPetIntRelDestinatarioDTO();
			$objMdPetIntRelDestinatarioDTO->setNumIdContato($objUsuarioDTO->getNumIdContato());
			$objMdPetIntRelDestinatarioDTO->setNumIdMdPetIntimacao($idMdPetIntimacao);
			$objMdPetIntRelDestinatarioDTO->retNumIdMdPetIntRelDestinatario();
			$objMdPetIntRelDestinatarioRN  = new MdPetIntRelDestinatarioRN();
			$objMdPetIntRelDestinatarioDTO = $objMdPetIntRelDestinatarioRN->consultar($objMdPetIntRelDestinatarioDTO);

            if (isset($_POST['hdnTbDocumento']) && $_POST['hdnTbDocumento'] != '') {

                $arrParametros                              = array();
                $arrParametros['IdMdPetIntRelDestinatario'] = $objMdPetIntRelDestinatarioDTO->getNumIdMdPetIntRelDestinatario();
                $arrParametros['IdMdPetIntRelTipoResp']     = $_POST['selTipoResposta'];
                $arrParametros['tbDocumentos']              = $_POST['hdnTbDocumento'];
                $arrParametros['idProcedimento']            = $_POST['hdnIdProcedimento'];
                $arrParametros['idMdPetIntimacao']          = $idMdPetIntimacao;
                $arrParametros['dataIntimacao']             = $strDataIntimacao;
                $arrParametros['nomeDocumentoPrincipal']    = $strNomeDocumentoPrincipal;
                $arrParametros['nomeTipoResposta']          = $_POST['hdnNomeTipoResposta'];

                $objMdPetIntDestRespostaRN->salvarResposta($arrParametros);

            }

            //Tipo Resposta
            $strSelectTipoResposta = MdPetIntRelTipoRespINT::montarSelectTipoResposta('null', '', 'null', $idMdPetIntimacao, $objMdPetIntRelDestinatarioDTO->getNumIdMdPetIntRelDestinatario());

            break;

        default:
            throw new InfraException("Ação '" . $_GET['acao'] . "' não reconhecida.");
            
}
