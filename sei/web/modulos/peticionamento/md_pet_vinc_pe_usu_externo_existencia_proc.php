<?php
    
    /**
     * Created by PhpStorm.
     * User: Renato Chaves
     * Date: 08/07/2019
     * Time: 14:18
     * Last edit by: Gabriel Glauber (SPASSU) at 26/07/2024
     */

    try {
    
        require_once dirname(__FILE__) . '/../../SEI.php';
        
        session_start();
        
        //////////////////////////////////////////////////////////////////////////////
        InfraDebug::getInstance()->setBolLigado(false);
        InfraDebug::getInstance()->setBolDebugInfra(false);
        InfraDebug::getInstance()->limpar();
        //////////////////////////////////////////////////////////////////////////////
        PaginaSEIExterna::getInstance()->setTipoPagina(InfraPagina::$TIPO_PAGINA_SIMPLES);
        SessaoSEIExterna::getInstance()->validarLink();
        SessaoSEIExterna::getInstance()->validarPermissao($_GET['acao']);
        
        
        switch ($_GET['acao']) {
        
            case 'peticionamento_usuario_externo_vinc_validacao_procuracao':
        
                $strTitulo = 'Conflito com Procuração Eletrônica Existente';
                
                switch ($_GET['msg_conflito']) {
                    case 'poderes':
                        $msg_conflito = 'Em razão de conflito de poderes em Procuração Eletrônica já existente não foi possível emitir a nova Procuração. Para emiti-la você deve revogar a Procuração existente ou remover os poderes conflitantes da nova Procuração.';
                    break;
                    case 'processos_e_poderes':
                        $msg_conflito = 'Em razão de conflito de processos e poderes em Procuração Eletrônica já existente não foi possível emitir a nova Procuração. Para emiti-la você deve revogar a Procuração existente ou remover os processos e poderes conflitantes da nova Procuração.';
                    break;
                    case 'especial':
	                $msg_conflito = 'Em razão de conflito com Procuração Eletrônica já existente não foi possível emitir a nova Procuração Especial. Para emiti-la você deve revogar a(s) Procuração(ções) existente(s).';
	                break;
                    default:
                        $msg_conflito = 'Em razão de conflito de poderes em Procuração Eletrônica já existente não foi possível emitir a nova Procuração. Revise as procurações existente e ajuste a abrangência dos processos e poderes da nova Procuração antes de tentar emiti-la novamente.';
                    break;
                }
                
            break;
            
            default:
                throw new InfraException("Ação '" . $_GET['acao'] . "' não reconhecida.");
            }
    
        } catch (Exception $e) {
    
        PaginaSEIExterna::getInstance()->processarExcecao($e);
    
    }


PaginaSEIExterna::getInstance()->montarDocType();
PaginaSEIExterna::getInstance()->abrirHtml();
PaginaSEIExterna::getInstance()->abrirHead();
PaginaSEIExterna::getInstance()->montarMeta();
PaginaSEIExterna::getInstance()->montarTitle(':: ' . PaginaSEIExterna::getInstance()->getStrNomeSistema() . ' - ' . $strTitulo . ' ::');
PaginaSEIExterna::getInstance()->montarStyle();
PaginaSEIExterna::getInstance()->abrirStyle();
PaginaSEIExterna::getInstance()->fecharStyle();
PaginaSEIExterna::getInstance()->montarJavaScript();
PaginaSEIExterna::getInstance()->abrirJavaScript();
PaginaSEIExterna::getInstance()->fecharJavaScript();
PaginaSEIExterna::getInstance()->fecharHead();
PaginaSEIExterna::getInstance()->abrirBody($strTitulo);

$arrComandos = [];

?>

<form id="frmConcluir" method="post" action="<?= PaginaSEIExterna::getInstance()->formatarXHTML(SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=' . $_GET['acao'] . '&id_represent='.$_GET['id_represent'].'&acao_origem=' . $_GET['acao'])) ?>">
  
    <?php PaginaSEIExterna::getInstance()->montarBarraComandosSuperior($arrComandos); ?>
    <?php PaginaSEIExterna::getInstance()->abrirAreaDados('auto'); ?>
    
    <label><?= $msg_conflito ?></label>
   
    <?php
        
        //Inicio da Lista
        $objMdPetVincRepresentantDTO = new MdPetVincRepresentantDTO();
        $objMdPetVincRepresentantDTO->retStrTipoRepresentante();
        $objMdPetVincRepresentantDTO->retNumIdMdPetVinculoRepresent();
        $objMdPetVincRepresentantDTO->retStrStaAbrangencia();
        $objMdPetVincRepresentantDTO->retStrTipoRepresentante();
        $objMdPetVincRepresentantDTO->retNumIdContato();
        $objMdPetVincRepresentantDTO->retNumIdMdPetVinculo();
        $objMdPetVincRepresentantDTO->retStrRazaoSocialNomeVinc();
        $objMdPetVincRepresentantDTO->retDthDataLimite();
        $objMdPetVincRepresentantDTO->setNumIdMdPetVinculoRepresent(explode('-', $_GET['id_represent']), InfraDTO::$OPER_IN);
	    $objMdPetVincRepresentantDTO->setStrStaEstado(MdPetVincRepresentantRN::$RP_ATIVO);
        
        PaginaSEI::getInstance()->prepararPaginacao($objMdPetVincRepresentantDTO);
        PaginaSEI::getInstance()->prepararOrdenacao($objMdPetVincRepresentantDTO, 'TipoRepresentante', InfraDTO::$TIPO_ORDENACAO_ASC);
        
        $arrObjMdPetVincRepresentantDTO = (new MdPetVincRepresentantRN())->listar($objMdPetVincRepresentantDTO);
        
        PaginaSEI::getInstance()->processarPaginacao($objMdPetVincRepresentantDTO);
        
        $numRegistros = count($arrObjMdPetVincRepresentantDTO);
        
        if ($numRegistros > 0){
        
           $strResultado = '<table width="100%" class="infraTable" summary="Lista de Procurações Eletrônicas">'."\n";
           $strResultado .= '<caption class="infraCaption">'.PaginaSEI::getInstance()->gerarCaptionTabela("Procurações Eletrônicas Conflitantes",$numRegistros).'</caption>';
           $strResultado .= '<tr>';
           $strResultado .= '<th class="infraTh" style="width: 190px">'.PaginaSEI::getInstance()->getThOrdenacao($objMdPetVincRepresentantDTO,'Processo','TipoRepresentante',$arrObjMdPetVincRepresentantDTO).'</th>'."\n";
           $strResultado .= '<th class="infraTh">'.PaginaSEI::getInstance()->getThOrdenacao($objMdPetVincRepresentantDTO,'Procuração','TipoRepresentante',$arrObjMdPetVincRepresentantDTO).'</th>'."\n";
           $strResultado .= '<th class="infraTh">'.PaginaSEI::getInstance()->getThOrdenacao($objMdPetVincRepresentantDTO,'Tipo','RazaoSocialNomeVinc',$arrObjMdPetVincRepresentantDTO).'</th>'."\n";
           $strResultado .= '<th class="infraTh">'.PaginaSEI::getInstance()->getThOrdenacao($objMdPetVincRepresentantDTO,'Outorgante','RazaoSocialNomeVinc',$arrObjMdPetVincRepresentantDTO).'</th>'."\n";
           $strResultado .= '<th class="infraTh">'.PaginaSEI::getInstance()->getThOrdenacao($objMdPetVincRepresentantDTO,'Outorgado','TipoRepresentante',$arrObjMdPetVincRepresentantDTO).'</th>'."\n";
           $strResultado .= '<th class="infraTh">'.PaginaSEI::getInstance()->getThOrdenacao($objMdPetVincRepresentantDTO,'Poderes Legais','StaAbrangencia',$arrObjMdPetVincRepresentantDTO).'</th>'."\n";
           $strResultado .= '<th class="infraTh" width="210">'.PaginaSEI::getInstance()->getThOrdenacao($objMdPetVincRepresentantDTO,'Abrangência','StaAbrangencia',$arrObjMdPetVincRepresentantDTO).'</th>'."\n";
           $strResultado .= '<th class="infraTh">'.PaginaSEI::getInstance()->getThOrdenacao($objMdPetVincRepresentantDTO,'Validade','TipoRepresentante',$arrObjMdPetVincRepresentantDTO).'</th>'."\n";
           $strResultado .= '</tr>'."\n";
           
           $strCssTr='';
	
	        $arrObjMdPetTipoPoderLegalDTO = null;
	
            if(isset($_GET['conflitosPoderes']) && !empty($_GET['conflitosPoderes'])) {
            
                $objMdPetTipoPoderLegalDTO = new MdPetTipoPoderLegalDTO();
                $objMdPetTipoPoderLegalDTO->retStrNome();
                $objMdPetTipoPoderLegalDTO->setNumIdTipoPoderLegal(explode('-', $_GET['conflitosPoderes']), InfraDTO::$OPER_IN);
                $arrObjMdPetTipoPoderLegalDTO = (new MdPetTipoPoderLegalRN())->listar($objMdPetTipoPoderLegalDTO);
            
            }
           
           for($i = 0;$i < $numRegistros; $i++){
               
               $strCssTr = ($strCssTr=='<tr class="infraTrClara">')?'<tr class="infraTrEscura">':'<tr class="infraTrClara">';
               $strResultado .= $strCssTr;
               
               //Recuperando Processo
               $objMdPetVinculoDTO = new MdPetVinculoDTO();
               $objMdPetVinculoDTO->retStrProtocoloFormatado();
               $objMdPetVinculoDTO->setNumIdMdPetVinculo($arrObjMdPetVincRepresentantDTO[$i]->getNumIdMdPetVinculo());
               $objMdPetVinculoRN = (new MdPetVinculoRN())->consultar($objMdPetVinculoDTO);
               
               if(!empty($objMdPetVinculoRN)){
                    $strResultado .= '<td valign="middle">'.$objMdPetVinculoRN->getStrProtocoloFormatado().'</td>';
               }
        
               //Recuperando Documento
               $objMdPetVincDocumentoDTO = new MdPetVincDocumentoDTO();
               $objMdPetVincDocumentoDTO->setNumIdMdPetVinculoRepresent($arrObjMdPetVincRepresentantDTO[$i]->getNumIdMdPetVinculoRepresent());
               $objMdPetVincDocumentoDTO->retStrProtocoloFormatadoProtocolo();
               $objMdPetVincDocumentoDTO->setStrTipoDocumento("E");
               $objMdPetVincDocumentoRN = new MdPetVincDocumentoRN();
               $arrObjMdPetVincDocumentoRN = $objMdPetVincDocumentoRN->consultar($objMdPetVincDocumentoDTO);
               $strResultado .= '<td valign="middle" style="width: 190px">'.$arrObjMdPetVincDocumentoRN->getStrProtocoloFormatadoProtocolo().'</td>';
               
               $strResultado .= '<td valign="middle">'.$arrObjMdPetVincRepresentantDTO[$i]->getStrNomeTipoRepresentante().'</td>';
               $strResultado .= '<td valign="middle">'.$arrObjMdPetVincRepresentantDTO[$i]->getStrRazaoSocialNomeVinc().'</td>';
               $strResultado .= '<td valign="middle">'.$arrObjMdPetVincRepresentantDTO[$i]->getStrNomeOutorgado().'</td>';
	
	           $listaPoderes = $arrObjMdPetVincRepresentantDTO[$i]->getStrTipoPoderes();
	           $itens = preg_split('/,(?=[A-Za-z])/', $listaPoderes);
	           
	           if(!empty($arrObjMdPetTipoPoderLegalDTO) && is_countable($arrObjMdPetTipoPoderLegalDTO)){
		
		            $negritoArray = InfraArray::converterArrInfraDTO($arrObjMdPetTipoPoderLegalDTO, 'Nome');
		
		            $itensModificados = array_map(function($item) use ($negritoArray) {
                        $itemTrimmed = trim($item);
                        return in_array($itemTrimmed, $negritoArray) ? '<span class="text-danger" style="font-size: .875rem">' . $itemTrimmed . '</span>' : $itemTrimmed;
                    }, $itens);
		
		           // Reunir os itens modificados em uma única string
		           $listaPoderes = implode('; ', $itensModificados);
		
	           }else{
		           $listaPoderes = implode('; ', $itens);
               }
	
	           $strResultado .= '<td valign="middle">'.$listaPoderes.'</td>';
            
                // $strResultado .= '<td valign="middle">'.$arrObjMdPetVincRepresentantDTO[$i]->getStrTipoPoderes().'</td>';
        
               //Detectando Abrangência e Tratando
               if($arrObjMdPetVincRepresentantDTO[$i]->getStrStaAbrangencia() == "Q" || $arrObjMdPetVincRepresentantDTO[$i]->getStrStaAbrangencia() == null){
                    $strResultado .= '<td valign="middle">Qualquer Processo em Nome do Outorgante</td>';
               }else{
                   
                   if(isset($_GET['conflitoProcessos']) && !empty($_GET['conflitoProcessos'])){
	                   $conflitoProcessos = explode('-', $_GET['conflitoProcessos']);
	
	                   // BUSCA PROCESSOS ESPECIFICOS
	                   $objMdPetRelVincRepProtocDTO = new MdPetRelVincRepProtocDTO();
	                   $objMdPetRelVincRepProtocDTO->retNumIdProtocolo();
	                   $objMdPetRelVincRepProtocDTO->setNumIdVincRepresent($arrObjMdPetVincRepresentantDTO[$i]->getNumIdMdPetVinculoRepresent());
	                   $arrMdPetRelVincRepProtocDTO = (new MdPetRelVincRepProtocRN())->listar($objMdPetRelVincRepProtocDTO);
	                   $arrIdProtocoloSimplesExistente = InfraArray::converterArrInfraDTO($arrMdPetRelVincRepProtocDTO, 'IdProtocolo');
	
	                   $objProtocoloDTO = new ProtocoloDTO();
	                   $objProtocoloDTO->retStrProtocoloFormatado();
	                   $objProtocoloDTO->setDblIdProtocolo($arrIdProtocoloSimplesExistente, InfraDTO::$OPER_IN);
	                   $arrObjProtocoloDTO = (new ProtocoloRN())->listarRN0668($objProtocoloDTO);
	                   $arrObjProtocoloDTO = InfraArray::converterArrInfraDTO($arrObjProtocoloDTO, 'ProtocoloFormatado');
	
	                   $strResultado .= '<td valign="middle">Processos Específicos: <span class="text-danger" style="font-size: .875rem">'.implode(';', $arrObjProtocoloDTO).'</span></td>';
	                   
                   }else{
                    
	                   $strResultado .= '<td valign="middle">Processos Específicos</td>';
                    
                   }
                   
               }
        
               $strResultado .= '<td valign="middle">'.$arrObjMdPetVincRepresentantDTO[$i]->getDthDataLimiteValidade().'</td>';
               $strResultado .= '</td></tr>'."\n";
        
           }
        
           $strResultado .= '</table>';
        
        }
        
        ?>

    <input type="hidden" id="hdnIdContExternoPai" name="hdnIdContExterno"/>
    <input type="hidden" id="hdnTabelaProc" name="hdnTabelaProc"/>
    
    <?php PaginaSEI::getInstance()->montarAreaTabela($strResultado,$numRegistros); ?>
    
</form>

<?
PaginaSEIExterna::getInstance()->fecharAreaDados();
PaginaSEIExterna::getInstance()->fecharBody();
PaginaSEIExterna::getInstance()->fecharHtml();
?>
<script type="text/javascript">

    function inicializar() {
        infraEfeitoTabelas();
    }

    function fecharJanela() {

        if (window.opener != null && !window.opener.closed) {
            window.opener.focus();
        }

        window.close();
    }

    $(document).ready(function(){
        if($('div.infraAreaPaginacao').text().trim() == ''){
            $('div.infraAreaPaginacao').height('0px');
        }
    });
    
</script>