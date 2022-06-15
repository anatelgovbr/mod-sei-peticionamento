<?php

/**
 * ANATEL
 *
 * 28/03/2017 - criado por jaqueline.mendes - CAST
 *
 */
require_once dirname(__FILE__) . '/../../../SEI.php';

class MdPetIntReciboRN extends InfraRN {

    public function __construct() {
        parent::__construct();
    }

    protected function inicializarObjInfraIBanco() {
        return BancoSEI::getInstance();
    }

    private function _verificaReciboIntimacao($idRelDest, $idRecibo, $idAcessoExterno) {
        
        //get id resposta
        $ids = null;
        $objMdPetRespostaRN = new MdPetIntDestRespostaRN();
        $objMdPetRespDocRN = new MdPetIntRelRespDocRN();
        $objMdPetReciboProtRN = new MdPetReciboRN();
        $objMdPetReciboDocAnexoRN = new MdPetRelReciboDocumentoAnexoRN();

        $objMdPetRespostaDTO = new MdPetIntDestRespostaDTO();
        $objMdPetRespostaDTO->setNumIdMdPetIntRelDestinatario($idRelDest);
        $objMdPetRespostaDTO->retNumIdMdPetIntDestResposta();

        $arrObjDTO = $objMdPetRespostaRN->listar($objMdPetRespostaDTO);

        //Get todos os ids de resposta deste destinatário
        if (count($arrObjDTO) > 0) {
            $ids = InfraArray::converterArrInfraDTO($arrObjDTO, 'IdMdPetIntDestResposta');
        }

        if (!is_null($ids)) {
            $idsDoc = null;

            $objMdPetRespDocDTO = new MdPetIntRelRespDocDTO();
            $objMdPetRespDocDTO->setNumIdMdPetIntDestResposta($ids, InfraDTO::$OPER_IN);
            $objMdPetRespDocDTO->retDblIdDocumento();
            $arrObjDocDTO = $objMdPetRespDocRN->listar($objMdPetRespDocDTO);

            //Get Todos os Documentos Anexos
            if (count($arrObjDocDTO) > 0) {
                $idsDoc = InfraArray::converterArrInfraDTO($arrObjDocDTO, 'IdDocumento');
            }
            
            if ($idAcessoExterno != false) {
                $relAcessoExtProtocoloRN = new RelAcessoExtProtocoloRN();
                $objRelAcessoExtProtocoloDTO = new RelAcessoExtProtocoloDTO();
                $objRelAcessoExtProtocoloDTO->retTodos();
                $objRelAcessoExtProtocoloDTO->setNumIdAcessoExterno($idAcessoExterno);
                $objRelAcessoExtProtocoloDTO->setDblIdProtocolo($idsDoc, InfraDTO::$OPER_IN);
                $arrObjRelAcessoExtProtocoloDTO = $relAcessoExtProtocoloRN->listar($objRelAcessoExtProtocoloDTO);
                if (count($arrObjDocDTO) > 0) {
                    $idsProtocolos = InfraArray::converterArrInfraDTO($arrObjRelAcessoExtProtocoloDTO, 'IdProtocolo');
                }
                //foreach ($idsDoc as $chave => $item) {
                   // if (!in_array($item, $idsProtocolos)) {
                     //   unset($idsDoc[$chave]);
                   // }
                //}
            }
           
            if (!is_null($idsDoc)) {
                $idsRecibo = null;

                //Get id Recibo por Doc
                $objMdPetReciboProtDTO = new MdPetReciboDTO();
                $objMdPetReciboProtDTO->setDblIdDocumento($idRecibo);
                $objMdPetReciboProtDTO->retNumIdReciboPeticionamento();

                $arrObjRecDTO = $objMdPetReciboProtRN->listar($objMdPetReciboProtDTO);

                //Get Todos os ids de Recibo
                if (count($arrObjDocDTO) > 0) {
                    $idsRecibo = InfraArray::converterArrInfraDTO($arrObjRecDTO, 'IdReciboPeticionamento');
                }
               
                if (!is_null($idsRecibo)) {

                    $objMdPetReciboDocAnexoDTO = new MdPetRelReciboDocumentoAnexoDTO();
                    $objMdPetReciboDocAnexoDTO->setNumIdReciboPeticionamento($idsRecibo, InfraDTO::$OPER_IN);
                    $objMdPetReciboDocAnexoDTO->setNumIdDocumento($idsDoc, InfraDTO::$OPER_IN);

                    $count = $objMdPetReciboDocAnexoRN->contar($objMdPetReciboDocAnexoDTO);

                    if ($count > 0) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    protected function addIconeReciboConectado($params) {

//        echo "<pre>";
        
        $conteudoHtml = '';
        if (is_array(explode(' ', $params[0]))) {
            $data = explode(' ', $params[0]);
            $data = $data[0];
        } else {
            $data = $params[0];
        }

        $docPrinc = $params[1];
        $docTipo = array_key_exists(2, $params) ? $params[2] : false;
        $docNum = array_key_exists(3, $params) ? $params[3] : false;
        $strLink = $params[4];
        $idProtocolo = array_key_exists(5, $params) ? $params[5] : false;
        $idRelDest = array_key_exists(6, $params) ? $params[6] : false;
        $idAcessoExterno = array_key_exists(7, $params) ? $params[7] : false;
        
        //Verifica se esse recibo pertence a essa intimação
        $isReciboDoc = $this->_verificaReciboIntimacao($idRelDest, $idProtocolo, $idAcessoExterno);
        
        if ($isReciboDoc) {
            $js = '';

            if ($idProtocolo) {
                $objMdPetCertidaoRN = new MdPetIntCertidaoRN();
                $isValido = $objMdPetCertidaoRN->verificaDocumentoEAnexoIntimacaoNaoCumprida(array($idProtocolo, false, SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno()));

                if (!$isValido) {
                    $js = 'alert(\'Recibo Eletrônico bloqueado, pois está vinculado a uma Intimação ainda não Cumprida.\')';
                }
            }

            if ($js == '') {
                $js = 'window.open(\'' . $strLink . '\');';
            }


            $imgRecibo = '<img src="modulos/peticionamento/imagens/svg/intimacao_recibo_peticionamento_resposta.svg" style="width: 24px">';

            $ToolTipTitle = 'Recibo da Resposta à Intimação';

            $ToolTipText = 'Peticionada em ';
            $ToolTipText .= $data . ' ';
            $ToolTipText .= '<br/>Documento Principal: ';
            $ToolTipText .= $docTipo . ' ';
            if ($docNum) {
                $ToolTipText .= $docNum . ' ';
            }
            $ToolTipText .= '(SEI nº ';
            $ToolTipText .= $docPrinc;
            $ToolTipText .= ')';

            $ToolTipText .= '<br/><br/>Clique para visualizar o Recibo Eletrônico de Protocolo.';

            $conteudoHtml = '<a onclick="' . $js . '"';
            $conteudoHtml .= ' onmouseover ="return infraTooltipMostrar(\'' . $ToolTipText . '\',\'' . $ToolTipTitle . '\')"';
            $conteudoHtml .= 'onmouseout="return infraTooltipOcultar()">';
            $conteudoHtml .= $imgRecibo;
            $conteudoHtml .= '</a>';
        }

        return $conteudoHtml;
    }

}
