<?php
switch ($_GET['acao']) {

        case 'md_pet_responder_intimacao_usu_ext':

            $contador = null;
            $strTitulo     = "Peticionamento de Resposta a Intimação Eletrônica";
            $arrComandos[] = '<button type="button" accesskey="P" name="btnResponder"  onclick = "responderIntimacao()" class="infraButton"><span class="infraTeclaAtalho">P</span>eticionar</button>';
            $arrComandos[] = '<button type="button" accesskey="C" id="btnFechar" class="infraButton" onclick="fechar()">Fe<span class="infraTeclaAtalho">c</span>har</button>';

            $idMdPetIntimacao = $_GET['id_intimacao'][0];

            $objMdPetIntimacaoRN = new MdPetIntimacaoRN();
            $idProcedimento = $objMdPetIntimacaoRN->getIdProcedimentoPorIntimacao($idMdPetIntimacao);
            $idMdPetIntAceite = $_GET['id_aceite'][0];
            $idAceite = $_GET['id_aceite'];

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
           
            $selHipoteseLegal = MdPetHipoteseLegalINT::montarSelectHipoteseLegal($booOnlyOptions = true);
            
            //Documento Principal
            $objMdPetIntDocumentoDTO = new MdPetIntProtocoloDTO();
            $objMdPetIntDocumentoDTO->setNumIdMdPetIntimacao($idMdPetIntimacao);
            $objMdPetIntDocumentoDTO->setStrSinPrincipal('S');
            $objMdPetIntDocumentoDTO->retTodos(true);
            $objMdPetIntDocumentoRN  = new MdPetIntProtocoloRN();
            $objMdPetIntDocumentoDTO = $objMdPetIntDocumentoRN->consultar($objMdPetIntDocumentoDTO);
            
            //Aceite
            $objMdPetIntAceiteDTO = new MdPetIntAceiteDTO();
	        $objMdPetIntAceiteDTO->retTodos();
            $objMdPetIntAceiteDTO->setNumIdMdPetIntAceite($idMdPetIntAceite);
	        $objMdPetIntAceiteDTO->setOrdDthData(InfraDTO::$TIPO_ORDENACAO_ASC);
	        $objMdPetIntAceiteDTO->setNumMaxRegistrosRetorno(1);
            $objMdPetIntAceiteDTO = (new MdPetIntAceiteRN())->consultar($objMdPetIntAceiteDTO);

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
           
            //Recuperando id do document pelo id da intimacao
            $objMdPetIntRelDestinatarioDTO = new MdPetIntRelDestinatarioDTO();
			$objMdPetIntRelDestinatarioDTO->setNumIdMdPetIntimacao($idMdPetIntimacao);
            $objMdPetIntRelDestinatarioDTO->retDblIdProtocolo();
			$objMdPetIntRelDestinatarioRN  = new MdPetIntRelDestinatarioRN();
			$objMdPetIntRelDestinatarioDTO = $objMdPetIntRelDestinatarioRN->listar($objMdPetIntRelDestinatarioDTO);
    
            $idDocumento = $objMdPetIntRelDestinatarioDTO[0]->getDblIdProtocolo();
            
			$objMdPetIntRelDestinatarioDTO = new MdPetIntRelDestinatarioDTO();
			$objMdPetIntRelDestinatarioDTO->setNumIdContatoParticipante($objUsuarioDTO->getNumIdContato());
			//$objMdPetIntRelDestinatarioDTO->setNumIdMdPetIntimacao($idMdPetIntimacao);
            $objMdPetIntRelDestinatarioDTO->retNumIdMdPetIntRelDestinatario();
            $objMdPetIntRelDestinatarioDTO->setNumIdMdPetAceite($_GET['id_aceite'],InfraDTO::$OPER_IN);
            $objMdPetIntRelDestinatarioDTO->setDblIdProtocolo($idDocumento);
			$objMdPetIntRelDestinatarioRN  = new MdPetIntRelDestinatarioRN();
			$objMdPetIntRelDestinatarioDTO = $objMdPetIntRelDestinatarioRN->listar($objMdPetIntRelDestinatarioDTO);
           

            if(count($objMdPetIntRelDestinatarioDTO) == 1){

                $objMdPetIntRelDestinatarioDTO = new MdPetIntRelDestinatarioDTO();
                $objMdPetIntRelDestinatarioDTO->setNumIdContatoParticipante($objUsuarioDTO->getNumIdContato());
                //$objMdPetIntRelDestinatarioDTO->setNumIdMdPetIntimacao($_GET['id_intimacao'],InfraDTO::$OPER_IN);
                $objMdPetIntRelDestinatarioDTO->retNumIdMdPetIntRelDestinatario();
                $objMdPetIntRelDestinatarioDTO->retNumIdContato();
                $objMdPetIntRelDestinatarioDTO->setNumIdMdPetAceite($_GET['id_aceite'],InfraDTO::$OPER_IN);

                $objMdPetIntRelDestinatarioDTO->setDblIdProtocolo($idDocumento);
                $objMdPetIntRelDestinatarioRN  = new MdPetIntRelDestinatarioRN();
                $objMdPetIntRelDestinatarioDTO = $objMdPetIntRelDestinatarioRN->consultar($objMdPetIntRelDestinatarioDTO);
                $contador = 1;
                $idMdPetIntRelDestHidden = $objMdPetIntRelDestinatarioDTO->getNumIdMdPetIntRelDestinatario();
                $idContatoHidden = $objMdPetIntRelDestinatarioDTO->getNumIdContato();

            }else{

                $objMdPetIntRelDestinatarioDTO = new MdPetIntRelDestinatarioDTO();
                $objMdPetIntRelDestinatarioDTO->setNumIdContatoParticipante($objUsuarioDTO->getNumIdContato());
                //$objMdPetIntRelDestinatarioDTO->setNumIdMdPetIntimacao($idMdPetIntimacao);
                $objMdPetIntRelDestinatarioDTO->retNumIdMdPetIntRelDestinatario();
                $objMdPetIntRelDestinatarioDTO->setDblIdProtocolo($idDocumento);
                $objMdPetIntRelDestinatarioDTO->setNumIdMdPetAceite($_GET['id_aceite'],InfraDTO::$OPER_IN);

                $objMdPetIntRelDestinatarioDTO->retNumIdMdPetIntimacao();
                $objMdPetIntRelDestinatarioRN  = new MdPetIntRelDestinatarioRN();
                $objMdPetIntRelDestinatarioDTO = $objMdPetIntRelDestinatarioRN->listar($objMdPetIntRelDestinatarioDTO);
                $contador = 2;
            
            }
            
        

            if (isset($_POST['hdnTbDocumento']) && $_POST['hdnTbDocumento'] != '') {

                //Selecionando um dos contatos - Razão Social
                if(!empty($_POST['selRazaoSocial'])){

                    $objMdPetIntRelDestinatarioDTO = new MdPetIntRelDestinatarioDTO();
                    $objMdPetIntRelDestinatarioDTO->setNumIdMdPetIntRelDestinatario($_POST['selRazaoSocial']);
                    $objMdPetIntRelDestinatarioDTO->setNumIdMdPetIntimacao($idMdPetIntimacao);
                    $objMdPetIntRelDestinatarioDTO->retNumIdMdPetIntRelDestinatario();
                    $objMdPetIntRelDestinatarioDTO->setNumIdMdPetAceite($_GET['id_aceite'],InfraDTO::$OPER_IN);

                    $objMdPetIntRelDestinatarioRN  = new MdPetIntRelDestinatarioRN();
                    $objMdPetIntRelDestinatarioDTO = $objMdPetIntRelDestinatarioRN->consultar($objMdPetIntRelDestinatarioDTO);
                    $idMdPetIntRelDestHidden = $objMdPetIntRelDestinatarioDTO->getNumIdMdPetIntRelDestinatario();
                    
                }
                
                $arrParametros                              = array();
                $arrParametros['IdMdPetIntRelDestinatario'] = $idMdPetIntRelDestHidden;
                $arrParametros['IdMdPetIntRelTipoResp']     = $_POST['selTipoResposta'];
                $arrParametros['tbDocumentos']              = $_POST['hdnTbDocumento'];
                $arrParametros['idProcedimento']            = $_POST['hdnIdProcedimento'];
                $arrParametros['idMdPetIntimacao']          = $idMdPetIntimacao;
                $arrParametros['dataIntimacao']             = $strDataIntimacao;
                $arrParametros['nomeDocumentoPrincipal']    = $strNomeDocumentoPrincipal;
                $arrParametros['nomeTipoResposta']          = $_POST['hdnNomeTipoResposta'];

                $objMdPetIntDestRespostaRN->salvarResposta($arrParametros);

            }

            //Empresa
            //$strSelectEmpresa = MdPetIntRelTipoRespINT::montarSelectTipoResposta('null', '', 'null', $idMdPetIntimacao, $objMdPetIntRelDestinatarioDTO->getNumIdMdPetIntRelDestinatario());

            //Tipo Resposta
            if($contador == 1){

                $strSelectTipoResposta = MdPetIntRelTipoRespINT::montarSelectTipoResposta('null', '', 'null', $_GET['id_intimacao'], $objMdPetIntRelDestinatarioDTO->getNumIdMdPetIntRelDestinatario());
                
            }else{
                
                $strSelectTipoResposta = MdPetIntRelTipoRespINT::montarSelectTipoResposta('null', '', 'null', $_GET['id_intimacao'], $objMdPetIntRelDestinatarioDTO[0]->getNumIdMdPetIntRelDestinatario());
                $strSelectEmpresa = MdPetIntRelDestinatarioINT::montarSelectRazaoSocial('null', '', 'null', $_GET['id_intimacao'], $objMdPetIntRelDestinatarioDTO[0]->getNumIdMdPetIntimacao(),$idDocumento,$idAceite, $objUsuarioDTO->getNumIdContato());

            }

            break;

        default:
            throw new InfraException("Ação '" . $_GET['acao'] . "' não reconhecida.");
            
}
