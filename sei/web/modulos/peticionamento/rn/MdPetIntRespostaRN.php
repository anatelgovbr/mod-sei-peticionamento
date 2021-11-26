<?php

/**
 * ANATEL
 *
 * 28/03/2017 - criado por jaqueline.mendes - CAST
 *
 */
require_once dirname(__FILE__) . '/../../../SEI.php';

class MdPetIntRespostaRN extends InfraRN {

    public function __construct() {
        parent::__construct();
    }

    protected function inicializarObjInfraIBanco() {
        return BancoSEI::getInstance();
    }

        protected function addIconeRespostaConectado($arrParams){

            $conteudoHtml                   = '';
            $idIntimacao                    = $arrParams[0];
            $idAcessoEx                     = $arrParams[1];
            $idProcedimento                 = $arrParams[2];
            $idAceite                       = $arrParams[3];
            $idMdPetDest                    = $arrParams[4];

            $objMdPetIntPrazoTipoRespostaRN = new MdPetIntPrazoRN();
            $arr                            = $objMdPetIntPrazoTipoRespostaRN->retornarTipoRespostaValido(array($idIntimacao,$idMdPetDest));

           if(count($arr) > 0) {
                
            	//necess�rio fazer calculo manual de hash por estar adicionando parametros nao padrao via GET e por conta do calculo manual de hash dispensou o uso da fun�ao assinarLink
            	$strParam = 'acao=md_pet_responder_intimacao_usu_ext&id_orgao_acesso_externo=0&id_intimacao=' . $idIntimacao . '&id_aceite=' . $idAceite . '&id_procedimento=' . $idProcedimento;
            	
            	$hash = md5($strParam.'#'.SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno().'@'.SessaoSEIExterna::getInstance()->getAtributo('RAND_USUARIO_EXTERNO'));
            	
            	$urlBase = ConfiguracaoSEI::getInstance()->getValor('SEI','URL');
            	$strLink = $urlBase . '/controlador_externo.php?' . $strParam . '&infra_hash=' . $hash;
                      
                $js = 'window.location = \''.$strLink.'\';';
                $imgResposta = '<img src="modulos/peticionamento/imagens/intimacao_peticionar_resposta.png">';
                $ToolTipTitle = 'Responder Intima��o Eletr�nica';
                $ToolTipTitle .= '<br/>Documento Principal: ';
                
                //obter informacoes do doc principal da intima��o
                $objMdPetIntDocumentoRN = new MdPetIntProtocoloRN();
                $objMdPetIntDocumentoDTO = new MdPetIntProtocoloDTO();
                $objMdPetIntDocumentoDTO->retTodos();
                $objMdPetIntDocumentoDTO->retStrNumeroDocumento();
                $objMdPetIntDocumentoDTO->retNumIdSerie();
                $objMdPetIntDocumentoDTO->retStrNomeSerie();
                $objMdPetIntDocumentoDTO->retStrProtocoloFormatadoDocumento();
                $objMdPetIntDocumentoDTO->setNumIdMdPetIntimacao( $idIntimacao );
                $objMdPetIntDocumentoDTO->setStrSinPrincipal('S');
                $objMdPetIntDocumentoDTO->setNumMaxRegistrosRetorno(1);
                $objMdPetIntDocumentoDTO = $objMdPetIntDocumentoRN->consultar( $objMdPetIntDocumentoDTO );
                
                $ToolTipTitle .= $objMdPetIntDocumentoDTO->getStrNomeSerie() . ' ' . $objMdPetIntDocumentoDTO->getStrNumeroDocumento() .' (SEI n� ' . $objMdPetIntDocumentoDTO->getStrProtocoloFormatadoDocumento() . ')';
                
                $ToolTipText = 'Clique para Peticionar Resposta a Intima��o.';

                $conteudoHtml  = '<a onclick="'.$js.'"';
                $conteudoHtml .= 'onmouseover ="return infraTooltipMostrar(\''.$ToolTipText.'\',\''.$ToolTipTitle.'\')"';
                $conteudoHtml .= 'onmouseout="return infraTooltipOcultar()">';
                $conteudoHtml .= $imgResposta;
                $conteudoHtml .= '</a>';
                
            }
            
            return $conteudoHtml;
        }
        
        protected function addIconeRespostaAcaoConectado($arrParams){

            $conteudoHtml                   = '';
            $idIntimacao                    = $arrParams[0];
            $idAcessoEx                     = $arrParams[1];
            $idProcedimento                 = $arrParams[2];
            $idAceite                       = $arrParams[3];
            $idMdPetDest                    = $arrParams[4];
            $cnpjs                          = $arrParams[5];
            $cpfs                           = $arrParams[6];
            
            $objMdPetIntPrazoTipoRespostaRN = new MdPetIntPrazoRN();
            $arr                            = $objMdPetIntPrazoTipoRespostaRN->retornarTipoRespostaValido(array($idIntimacao,$idMdPetDest));
            if(count($arr) > 0) {
                foreach ($idAceite as $id) {
                   $linkIdAceite .= '&id_aceite[]='.$id;
                } 
                
                foreach ($idIntimacao as $id) {
                   $linkIdIntimacao .= '&id_intimacao[]='.$id;
                }
               
            	//necess�rio fazer calculo manual de hash por estar adicionando parametros nao padrao via GET e por conta do calculo manual de hash dispensou o uso da fun�ao assinarLink
            	$strParam = 'acao=md_pet_responder_intimacao_usu_ext&id_orgao_acesso_externo=0' . $linkIdIntimacao . $linkIdAceite . '&id_procedimento=' . $idProcedimento;
            	
            	$hash = md5($strParam.'#'.SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno().'@'.SessaoSEIExterna::getInstance()->getAtributo('RAND_USUARIO_EXTERNO'));
            	
            	$urlBase = ConfiguracaoSEI::getInstance()->getValor('SEI','URL');
            	$strLink = $urlBase . '/controlador_externo.php?' . $strParam . '&infra_hash=' . $hash;
                      
                $js = 'window.location = \''.$strLink.'\';';
                $imgResposta = '<img src="modulos/peticionamento/imagens/intimacao_peticionar_resposta.png">';
                $ToolTipTitle = 'Responder Intima��o Eletr�nica';
                $ToolTipTitle .= '<br/>Documento Principal: ';
                
                //obter informacoes do doc principal da intima��o
                $objMdPetIntDocumentoRN = new MdPetIntProtocoloRN();
                $objMdPetIntDocumentoDTO = new MdPetIntProtocoloDTO();
                $objMdPetIntDocumentoDTO->retTodos();
                $objMdPetIntDocumentoDTO->retStrNumeroDocumento();
                $objMdPetIntDocumentoDTO->retNumIdSerie();
                $objMdPetIntDocumentoDTO->retStrNomeSerie();
                $objMdPetIntDocumentoDTO->retStrProtocoloFormatadoDocumento();
                $objMdPetIntDocumentoDTO->setNumIdMdPetIntimacao( $idIntimacao, InfraDTO::$OPER_IN );
                $objMdPetIntDocumentoDTO->setStrSinPrincipal('S');
                $objMdPetIntDocumentoDTO->setNumMaxRegistrosRetorno(1);
                $objMdPetIntDocumentoDTO = $objMdPetIntDocumentoRN->consultar( $objMdPetIntDocumentoDTO );
                
                $ToolTipTitle .= $objMdPetIntDocumentoDTO->getStrNomeSerie() . ' ' . $objMdPetIntDocumentoDTO->getStrNumeroDocumento() .' (SEI n� ' . $objMdPetIntDocumentoDTO->getStrProtocoloFormatadoDocumento() . ')';
                
                if($cnpjs || $cpfs){
                    $ToolTipTitle .= '<br/><br/>Destinat�rios:<br/>';
                    if($cnpjs){
                        foreach ($cnpjs as $emp) {
                            $ToolTipTitle .= $emp.'<br/>';
                        }
                    }
                    if($cpfs){
                        foreach ($cpfs as $pes) {
                            $ToolTipTitle .= $pes.'<br/>';
                        }
                    }
                }
                
                
                $ToolTipText = 'Clique para Peticionar Resposta a Intima��o.';

                $conteudoHtml  = '<a onclick="'.$js.'"';
                $conteudoHtml .= 'onmouseover ="return infraTooltipMostrar(\''.$ToolTipText.'\',\''.$ToolTipTitle.'\')"';
                $conteudoHtml .= 'onmouseout="return infraTooltipOcultar()">';
                $conteudoHtml .= $imgResposta;
                $conteudoHtml .= '</a>';
                
            }
            
            return $conteudoHtml;
    }

    protected function addIconeRespostaNegadaConectado($arrParams) {

        $conteudoHtml = '';
        $idIntimacao = $arrParams[0];
        $idAcessoEx = $arrParams[1];
        $idProcedimento = $arrParams[2];
        $idAceite = $arrParams[3];
        $idMdPetDest = $arrParams[4];
        $razao = $arrParams[5];
        $cnpj = $arrParams[6];
        $estado = $arrParams[7];
        $idDestinatario = $arrParams[8];
        $idContato = $arrParams[9];
        $cnpjs = $arrParams[10];
        $cpfs = $arrParams[11];

        $objMdPetIntPrazoTipoRespostaRN = new MdPetIntPrazoRN();
        $arr = $objMdPetIntPrazoTipoRespostaRN->retornarTipoRespostaValido(array($idIntimacao, $idMdPetDest));

        if (count($arr) > 0) {

            if (count($idContato) >= 1) {
                foreach ($idContato as $id) {
                    $linkIdDestinatario .= '&id_contato[]=' . $id;
                }
            }

            $urlBase = ConfiguracaoSEI::getInstance()->getValor('SEI', 'URL');

            //$strLink = SessaoSEIExterna::getInstance()->assinarLink($urlBase . '/controlador_externo.php?acao=md_pet_intimacao_usu_ext_negar_resposta&id_procedimento=' . $idProcedimento . '&id_acesso_externo=' . $idAcessoEx .'&id_md_pet_int_rel_dest=' . $idMdPetDest[0].'&estado='.$estado);
            $strLink = SessaoSEIExterna::getInstance()->assinarLink($urlBase . '/controlador_externo.php?acao=md_pet_intimacao_usu_ext_negar_resposta&id_acesso_externo=' . $idAcessoEx . $linkIdDestinatario . '&id_destinatario=' . $idDestinatario);

            $js = "infraAbrirJanela('" . $strLink . "', 'janelaConsultarIntimacao', 900, 350);";


            //  $js = 'alert(\'Voc� n�o pode mais responder a intima��o destinada � '.$razao.' ('.infraUtil::formatarCnpj($cnpj).') pois sua vincula��o � Pessoa Jur�dica est� '.$estado.'.\')';
            $imgResposta = '<img src="modulos/peticionamento/imagens/intimacao_peticionar_resposta_negada.png">';
            $ToolTipTitle = 'Responder Intima��o Eletr�nica';
            $ToolTipTitle .= '<br/>Documento Principal: ';

            //obter informacoes do doc principal da intima��o
            $objMdPetIntDocumentoRN = new MdPetIntProtocoloRN();
            $objMdPetIntDocumentoDTO = new MdPetIntProtocoloDTO();
            $objMdPetIntDocumentoDTO->retTodos();
            $objMdPetIntDocumentoDTO->retStrNumeroDocumento();
            $objMdPetIntDocumentoDTO->retNumIdSerie();
            $objMdPetIntDocumentoDTO->retStrNomeSerie();
            $objMdPetIntDocumentoDTO->retStrProtocoloFormatadoDocumento();
            $objMdPetIntDocumentoDTO->setNumIdMdPetIntimacao($idIntimacao, InfraDTO::$OPER_IN);
            $objMdPetIntDocumentoDTO->setStrSinPrincipal('S');
            $objMdPetIntDocumentoDTO->setNumMaxRegistrosRetorno(1);
            $objMdPetIntDocumentoDTO = $objMdPetIntDocumentoRN->consultar($objMdPetIntDocumentoDTO);

            $ToolTipTitle .= $objMdPetIntDocumentoDTO->getStrNomeSerie() . ' ' . $objMdPetIntDocumentoDTO->getStrNumeroDocumento() . ' (SEI n� ' . $objMdPetIntDocumentoDTO->getStrProtocoloFormatadoDocumento() . ')';

            //$ToolTipText = 'Voc� n�o possui mais permiss�o para responder a Intima��o Eletr�nica, conforme abaixo:';

            if ($cnpjs || $cpfs) {
                $ToolTipTitle .= '<br/><br/>Destinat�rios:<br/>';
                if ($cnpjs) {
                    foreach ($cnpjs as $emp) {
                        $ToolTipTitle .= $emp . '<br/>';
                    }
                }
                if ($cpfs) {
                    foreach ($cpfs as $pes) {
                        $ToolTipTitle .= $pes . '<br/>';
                    }
                }
            }
           
            $ToolTipText .= "Voc� n�o possui mais permiss�o para responder a Intima��o Eletr�nica. Verifique seus Poderes de Representa��o.";
                            
            $conteudoHtml = '<a onclick="' . $js . '"';
            $conteudoHtml .= 'onmouseover ="return infraTooltipMostrar(\'' . $ToolTipText . '\',\'' . $ToolTipTitle . '\')"';
            $conteudoHtml .= 'onmouseout="return infraTooltipOcultar()">';
            $conteudoHtml .= $imgResposta;
            $conteudoHtml .= '</a>';
        }

        return $conteudoHtml;
    }

}
