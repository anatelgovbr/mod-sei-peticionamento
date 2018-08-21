<?php


    /**
     * ANATEL
     *
     * 28/03/2017 - criado por jaqueline.mendes - CAST
     *
     */

    require_once dirname(__FILE__) . '/../../../SEI.php';

    class MdPetIntRespostaRN extends InfraRN
    {

        public function __construct()
        {
            parent::__construct();
        }

        protected function inicializarObjInfraIBanco()
        {
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

    }