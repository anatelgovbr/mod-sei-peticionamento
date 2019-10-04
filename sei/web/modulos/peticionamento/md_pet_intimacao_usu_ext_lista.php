<?php
try {
        /**
         * @author André Luiz <andre.luiz@castgroup.com.br>
         * @since  08/03/2017
         */

        require_once dirname(__FILE__) . '/../../SEI.php';

        session_start();
        //====================================================
        //InfraDebug::getInstance()->setBolLigado(false);
        //InfraDebug::getInstance()->setBolDebugInfra(false);
        //InfraDebug::getInstance()->limpar();
        //====================================================

        SessaoSEIExterna::getInstance()->validarLink();
        SessaoSEIExterna::getInstance()->validarPermissao($_GET['acao']);

        //URL's
        $strUrlAcaoForm   = SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=' . $_GET['acao'] . '&acao_origem=' . $_GET['acao']);
        
        $strUrlFechar     = SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=usuario_externo_controle_acessos');
        
        $strUrlResponder  = SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=md_pet_responder_intimacao_usu_ext');

        $comboSituacao = MdPetIntimacaoINT::getSituacoesListaExterno();

        //Combo tipo de Intimação
        $selectedTpIntim       = array_key_exists('selTipoIntimacao', $_POST) ? $_POST['selTipoIntimacao'] : '0';
        $selTipoIntimacao      = MdPetIntTipoIntimacaoINT::montarSelectTipoIntimacaoListaExterna($selectedTpIntim);
        $selectedSitIntim      = array_key_exists('selCumprimentoIntimacao', $_POST) ? $_POST['selCumprimentoIntimacao'] : '';
        $selSituacaoIntimacao  = MdPetIntimacaoINT::montarSelectSituacaoIntimacao($selectedSitIntim);

        //Init RN
        $objMdPetRelDestRN  = new MdPetIntRelDestinatarioRN();

        switch ($_GET['acao']) {

            case 'md_pet_intimacao_usu_ext_listar':

                $strTitulo     = "Intimações Eletrônicas";
                break;

            default:
                throw new InfraException("Ação '" . $_GET['acao'] . "' não reconhecida.");
        }

        $arrComandos[] = '<button type="button" accesskey="P" name="btnPesquisar" onclick="pesquisar()" value="Pesquisar" class="infraButton"><span class="infraTeclaAtalho">P</span>esquisar</button>';
        
        $arrComandos[] = '<button type="button" accesskey="C" id="btnFechar" value="Fechar" class="infraButton" onclick="fechar()">Fe<span class="infraTeclaAtalho">c</span>har</button>';
       
        $arrPost = $_POST;

        $selTipoDestinatario = isset($_POST['selTipoDestinatario']) ? $_POST['selTipoDestinatario'] : '';
//        echo "<pre>";
//        var_dump($selTipoDestinatario);
////        die;
         $objDTO = $objMdPetRelDestRN->retornaSelectsDto(array(false, $arrPost));

        PaginaSEIExterna::getInstance()->prepararOrdenacao($objDTO, 'DataCadastro', InfraDTO::$TIPO_ORDENACAO_DESC);
        PaginaSEIExterna::getInstance()->prepararPaginacao($objDTO, 200);

        $objDTO->retStrNomeContato();
        $objDTO->retDblCnpjContato();
        $objDTO->retDblCpfContato();
        $objDTO->retStrSinPessoaJuridica();

       $arrObjDTO = $objMdPetRelDestRN->listarDadosUsuExterno(array(false, $arrPost,$objDTO));

        PaginaSEIExterna::getInstance()->processarPaginacao($objDTO);
        $arrTipoDestinatario = array(
            "N" => "Pessoa Física",
            "S" => "Pessoa Jurídica"
        );

        $numRegistros = count($arrObjDTO);

        if ($numRegistros > 0) {
        	
            $objMdPetIntAceiteRN = new MdPetIntAceiteRN();
            $objMdPetCertidaoRN  = new MdPetIntCertidaoRN();
            $objMdPetIntReciboRN = new MdPetIntReciboRN();

            $strResultado .= '<table width="99%" class="infraTable" summary="Intimações Eletrônicas">';
            $strResultado .= '<caption class="infraCaption">';
            
            $strResultado .= PaginaSEIExterna::getInstance()->gerarCaptionTabela('Intimações Eletrônicas', $numRegistros);
            $strResultado .= '</caption>';

            $strResultado .= '<tr>';
            $strResultado .= '<th class="infraTh" width="600px">' . PaginaSEIExterna::getInstance()->getThOrdenacao($objDTO, 'Processo', 'ProtocoloFormatadoProcedimento', $arrObjDTO) . '</th>';
            
            $strResultado .= '<th class="infraTh" width="66px">' . PaginaSEIExterna::getInstance()->getThOrdenacao($objDTO, 'Data de Expedição', 'DataCadastro', $arrObjDTO) . '</th>';
            
            $strResultado .= '<th class="infraTh" width="15%">' . PaginaSEIExterna::getInstance()->getThOrdenacao($objDTO, 'Documento Principal', 'DocumentoPrincipal', $arrObjDTO) . '</th>';

            $strResultado .= '<th class="infraTh" width="30%" >' . PaginaSEIExterna::getInstance()->getThOrdenacao($objDTO, 'Destinatário', 'NomeContato', $arrObjDTO) . '</th>';

            $strResultado .= '<th class="infraTh" width="66px">' . PaginaSEIExterna::getInstance()->getThOrdenacao($objDTO, 'Tipo de Destinatário', 'SinPessoaJuridica', $arrObjDTO) . '</th>';
            
            $strResultado .= '<th class="infraTh" width="12%">' . PaginaSEIExterna::getInstance()->getThOrdenacao($objDTO, 'Tipo de Intimação', 'NomeTipoIntimacao', $arrObjDTO) . '</th>';
            
            $strResultado .= '<th class="infraTh" width="225px">' . PaginaSEIExterna::getInstance()->getThOrdenacao($objDTO, 'Situação', 'StaSituacaoIntimacao', $arrObjDTO) . '</th>';
            
            $strResultado .= '<th class="infraTh" width="90px">Ações</th>';
            $strResultado .= '</tr>';

            $strCssTr = '<tr class="infraTrEscura">';
            
            
            foreach ($arrObjDTO as $key => $objRet) {
                
                $idAcExt     =  $objRet->getNumIdAcessoExterno();
                SessaoSEIExterna::getInstance()->configurarAcessoExterno($idAcExt);

                //vars
                $strId                   = $objRet->getNumIdMdPetIntimacao();
                $bolRegistroAtivo        = $objRet->getStrSinAtivo() == 'S';
                $idIntimacao             = $objRet->getNumIdMdPetIntimacao(); //Corrigir
                $nomeTela                = 'Intimação Eletrônica';

                $idProcesso  = isset($objRet) && !is_null($objRet) ? $objRet->getDblIdProtocoloProcedimento() : null;
                $tpProcesso  = $objRet->getStrNomeTipoProcedimento();

                $idMdPetDest = $objRet->getNumIdMdPetIntRelDestinatario();

                $descricao  = $objRet->getStrEspecificacaoProcedimento();
                $strCssTr = !$bolRegistroAtivo ? '<tr class="trVermelha">' : ($strCssTr == '<tr class="infraTrClara">' ? '<tr class="infraTrEscura">' : '<tr class="infraTrClara">');
                $strResultado .= $strCssTr;

                //Linha Número do Processo
                $strResultado .= '<td align="center" >';
                $strResultado .=  $objMdPetRelDestRN->addConsultarProcesso($idProcesso, $tpProcesso, $idAcExt, $descricao, $objRet->getStrProtocoloFormatadoProcedimento());
                $strResultado .= '</td>';

                //Linha Data de Expedição
                $arrData = explode(' ', $objRet->getDthDataCadastro());
                $strResultado .= '<td align="center">';
                $strResultado .= $arrData[0];
                $strResultado .= '</td>';

                //Documento Principal
                $strResultado .= '<td>';
                $strResultado .= PaginaSEI::tratarHTML($objRet->getStrDocumentoPrincipal());
                $strResultado .= '</td>';

                //Destinatário
                $strResultado .= '<td>';
                $strResultado .= PaginaSEI::tratarHTML($objRet->getStrNomeContato())." (";
                $strResultado .= $objRet->getStrSinPessoaJuridica() == 'S'? PaginaSEI::tratarHTML(InfraUtil::formatarCnpj($objRet->getDblCnpjContato())) : InfraUtil::formatarCpf(PaginaSEI::tratarHTML($objRet->getDblCpfContato()));
                $strResultado .= ') </td>';

                //Destinatário
                $strResultado .= '<td>';
                $strResultado .= $objRet->getStrSinPessoaJuridica() == 'S'? "Pessoa Jurídica":"Pessoa Física";
                $strResultado .= '</td>';


                //Tipo de Intimação
                $strResultado .= '<td>';
                $strResultado .= PaginaSEI::tratarHTML($objRet->getStrNomeTipoIntimacao());
                $strResultado .= '</td>';

                //Situação
                $strResultado .= '<td>';
                $strResultado .= PaginaSEI::tratarHTML($objRet->getStrSituacaoIntimacao());
                $strResultado .= '</td>';

                $strResultado .= '<td align="center">';
                
                //Ação Consulta                 
                if(!is_null($idProcesso))
                {
                    $strResultado .= $objMdPetRelDestRN->addConsultarProcesso($idProcesso, $tpProcesso, $idAcExt, $descricao);

                    $idSituacao    = $objRet->getStrStaSituacaoIntimacao();

                     if(!is_null($idSituacao) && $idSituacao != MdPetIntimacaoRN::$INTIMACAO_PENDENTE)
                     {
                        $docPrinc = $objRet->getStrProtocoloFormatadoDocumento();
                        $docTipo = str_replace('('.$objRet->getStrProtocoloFormatadoDocumento().')', '', $objRet->getStrDocumentoPrincipal());
                        $docNum = '';
                        $idDocCert = $objMdPetIntAceiteRN->getIdCertidaoPorIntimacao(array($idIntimacao, SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno()));
                        $strResultado .= $objMdPetCertidaoRN->addIconeAcessoCertidao(array($docPrinc, $idIntimacao, $idAcExt,$idDocCert));

                        //RECIBO
                        $objMdPetReciboDTO = new MdPetReciboDTO();
                        $objMdPetReciboDTO->retTodos( );

                        $objInfraParametro = new InfraParametro(BancoSEI::getInstance());
                        $strVersaoModuloPeticionamento = $objInfraParametro->getValor('VERSAO_MODULO_PETICIONAMENTO', false);

//                        $objMdPetReciboDTO->setNumIdUsuario( SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno() );
                        $objMdPetReciboDTO->setStrStaTipoPeticionamento( MdPetReciboRN::$TP_RECIBO_RESPOSTA_INTIMACAO );

                        $objMdPetReciboRN = new MdPetReciboRN();

                        //Próprio Processo
                        $isRelacionado = false;
                        $objMdPetReciboDTO->setNumIdProtocolo( $idProcesso );
                        $objMdPetReciboDTO->unSetDblIdProtocoloRelacionado();

                        $arrObjMdPetReciboDTO = $objMdPetReciboRN->listar($objMdPetReciboDTO);
                        
                        if (count($arrObjMdPetReciboDTO)==0){
                            //Relacionado
                            $isRelacionado = true;
                            $objMdPetReciboDTO->unSetNumIdProtocolo();
                            $objMdPetReciboDTO->setDblIdProtocoloRelacionado( $idProcesso );
                            
                            $arrObjMdPetReciboDTO = $objMdPetReciboRN->listar($objMdPetReciboDTO);
                        }

                        foreach($arrObjMdPetReciboDTO as $objMdPetReciboDTO){

                            $usuarioDTO = new UsuarioDTO();
                            $usuarioRN = new UsuarioRN();
                            $usuarioDTO->retNumIdUsuario();
                            $usuarioDTO->retNumIdContato();
                            $usuarioDTO->setNumIdUsuario( SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno() );

                            $usuarioDTO = $usuarioRN->consultarRN0489( $usuarioDTO );

                            $emailDestinatario = SessaoSEIExterna::getInstance()->getStrSiglaUsuarioExterno();
                            $acessoExtRN = new AcessoExternoRN();
                            $acessoExtDTO = new AcessoExternoDTO();
                            $acessoExtDTO->retTodos();
                            $acessoExtDTO->setOrd("IdAcessoExterno", InfraDTO::$TIPO_ORDENACAO_DESC);
                            $acessoExtDTO->retDblIdProtocoloAtividade();
                            $acessoExtDTO->retNumIdContatoParticipante();

                            //trazer acesso externo  mais recente, deste processo, para este usuario externo, que estejam dentro da data de validade
                            $acessoExtDTO->setDblIdProtocoloAtividade( $objMdPetReciboDTO->getNumIdProtocolo() );

                            $acessoExtDTO->setNumIdContatoParticipante( $usuarioDTO->getNumIdContato() );
                            $acessoExtDTO->setStrEmailDestinatario( $emailDestinatario );
                            $acessoExtDTO->setStrStaTipo( AcessoExternoRN::$TA_USUARIO_EXTERNO );
                            $acessoExtDTO->setStrSinAtivo('S');

                            //Verificar se traz somente o do acesso atual ou do relacionado desta intimação (linha 1215)
                            //$acessoExtDTO->setNumIdAcessoExterno($idAcessoExterno);
                            //@todo adicionar verificaçao de data de validade do acesso externo

                            $arrAcessosExternos = $acessoExtRN->listar( $acessoExtDTO );
                            
//                            var_dump($arrAcessosExternos);

                            if( is_array( $arrAcessosExternos ) && count( $arrAcessosExternos ) > 0 ){
                                $id_acesso_ext_link = $arrAcessosExternos[0]->getNumIdAcessoExterno();

                                $docLink = "documento_consulta_externa.php?id_acesso_externo=" . $id_acesso_ext_link;
                                $docLink .= "&id_documento=" . $objMdPetReciboDTO->getDblIdDocumento();
                                $docLink .= "&id_orgao_acesso_externo=0";
                                SessaoSEIExterna::getInstance()->configurarAcessoExterno( $id_acesso_ext_link );

                                //se nao configurar acesso externo ANTES, a assinatura do link falha
                                $linkAssinado = PaginaSEIExterna::getInstance()->formatarXHTML(SessaoSEIExterna::getInstance()->assinarLink( $docLink ));

                                $strResultado .= $objMdPetIntReciboRN->addIconeRecibo(array($objMdPetReciboDTO->getDthDataHoraRecebimentoFinal(), $docPrinc, $docTipo, $docNum, $linkAssinado, $objMdPetReciboDTO->getDblIdDocumento(), $idMdPetDest));
                            }
                        }

                        //necessario fazer isso para nao quebrar a navegaçao (se nao fizer isso e tem clicar em qualquer outro link do usuario externo, quebra a sessao e usuario é enviado de volta para a tela de login externo (trata-se de funcionamento incorporado ao Core do SEI)
                        SessaoSEIExterna::getInstance()->configurarAcessoExterno($idAcessoExterno);
                        //RECIBO - fim
                        

                     }

                }

                $strResultado .= '</td>';
                $strResultado .= '</tr>';

            }
            
            $strResultado .= '</table>';
        }
        
  	SessaoSEIExterna::getInstance()->configurarAcessoExterno(null);

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
?>
    #frmIntimacaoEletronicaLista label[for^=txt],
    #frmIntimacaoEletronicaLista label[for^=sel] {display: block;white-space: nowrap;}
    #frmIntimacaoEletronicaLista input[type=text] {border: .1em solid #666;}
    #frmIntimacaoEletronicaLista img[id^='imgData'] {vertical-align: middle;}
    #frmIntimacaoEletronicaLista input[id^='txtData'] {width: 70px;}
    #frmIntimacaoEletronicaLista .selectPadrao {min-width: 200px;max-width: 330px;border: .1em solid #666;}
    .bloco {float: left;margin-top: 1%;margin-right: 1%;}
    .clear {clear: both;}
<?php
    PaginaSEIExterna::getInstance()->fecharStyle();
    PaginaSEIExterna::getInstance()->montarJavaScript();
    PaginaSEIExterna::getInstance()->abrirJavaScript();
?>    
        function inicializar() {

            infraEfeitoTabelas();
            addEventoEnter();
        }

        function pesquisar() {
            var frmIntimacaoEletronicaLista = document.getElementById('frmIntimacaoEletronicaLista');
            var dataInicio = document.getElementById('txtDataInicio');
            var dataFim = document.getElementById('txtDataFim');

            if (!infraValidaData(dataInicio)) {
                return false;
            }

            if (!infraValidaData(dataFim)) {
                return false;
            }

            if (dataInicio.value.trim() != '' && dataFim.value.trim() == '') {
                dataFim.focus();
                alert('Informe o período final!');
                return false;
            }

            if (dataInicio.value.trim() == '' && dataFim.value.trim() != '') {
                dataInicio.focus();
                alert('Informe o período inicial!');
                return false;
            }

            if (dataInicio.value.trim() != '' && dataFim.value.trim() != '') {
                var dtInicio = dataInicio.value.split('/').reverse().join('/');
                dtInicio = new Date(dtInicio);

                var dtFim = dataFim.value.split('/').reverse().join('/');
                dtFim = new Date(dtFim);

                if (dtInicio.getTime() > dtFim.getTime()) {
                    alert('Período inicial maior que final!');
                    dataInicio.focus();
                    return false;
                }
            }

            frmIntimacaoEletronicaLista.submit();
        }

        function addEventoEnter(){
            var form = document.getElementById('frmIntimacaoEletronicaLista');
            document.addEventListener("keypress", function(evt){
                var key_code = evt.keyCode  ? evt.keyCode  :
                    evt.charCode ? evt.charCode :
                        evt.which    ? evt.which    : void 0;

                if (key_code == 13)
                {
                    pesquisar();
                }

            });
        }

        function fechar() {
            document.location = '<?= $strUrlFechar ?>';
        }

        function responder() {
            document.location = '<?= $strUrlResponder ?>';
        }


<?php 
PaginaSEIExterna::getInstance()->fecharJavaScript();
PaginaSEIExterna::getInstance()->fecharHead();
PaginaSEIExterna::getInstance()->abrirBody($strTitulo, 'onload="inicializar();"');
?>
<form id="frmIntimacaoEletronicaLista" method="POST" action="<?= $strUrlAcaoForm ?>"/>
<?php
    PaginaSEIExterna::getInstance()->montarBarraComandosSuperior($arrComandos);
    PaginaSEIExterna::getInstance()->abrirAreaDados('auto', 'style="margin-bottom: 25px"'); ?>

    <!--NUMERO PROCESSO-->
    <div class="bloco" style="min-width:130px; width:12%">
        <label class="infraLabelOpcional" for="txtNumeroProcesso">Número do Processo:</label>
        <input type="text" name="txtNumeroProcesso" id="txtNumeroProcesso" style="width: 130px"
               tabindex="<?= PaginaSEIExterna::getInstance()->getProxTabDados(); ?>" value="<?php  echo array_key_exists('txtNumeroProcesso', $_POST) ? PaginaSEIExterna::tratarHTML($_POST['txtNumeroProcesso']) : '' ?>"/>
    </div>
    <!--FIM NUMERO PROCESSO-->

    <!-- PERIODO DE EXPEDICAO-->
    <div class="bloco" style="min-width:205px; width:20%">
        <label class="infraLabelOpcional" for="txtPeriodoExpedicao">Período de Expedição:</label>
        <!--DATA INICIAL-->
        <input type="text" name="txtDataInicio" id="txtDataInicio"
               onkeypress="return infraMascaraData(this, event);" maxlength="10"
               tabindex="<?= PaginaSEIExterna::getInstance()->getProxTabDados(); ?>" value="<?php  echo array_key_exists('txtDataInicio', $_POST) ?  PaginaSEIExterna::tratarHTML($_POST['txtDataInicio']) : '' ?>"/>

        <img src="<?= PaginaSEIExterna::getInstance()->getDiretorioImagensGlobal() ?>/calendario.gif"
             id="imgDataInicio"
             title="Selecionar Data Inicial"
             alt="Selecionar Data Inicial" class="infraImg"
             onclick="infraCalendario('txtDataInicio',this,false,'<?= InfraData::getStrDataAtual() ?>');"/>
        <!--FIM DATA INICIAL-->

        <label class="infraLabelOpcional">até</label>

        <!--DATA FINAL-->
        <input type="text" id="txtDataFim" name="txtDataFim"
               value="<?php  echo array_key_exists('txtDataFim', $_POST) ? PaginaSEIExterna::tratarHTML($_POST['txtDataFim']) : '' ?>"
               onkeypress="return infraMascaraData(this, event);" maxlength="10"
               tabindex="<?= PaginaSEIExterna::getInstance()->getProxTabDados(); ?>"/>

        <img src="<?= PaginaSEIExterna::getInstance()->getDiretorioImagensGlobal() ?>/calendario.gif"
             id="imgDataFim"
             title="Selecionar Data Final"
             alt="Selecionar Data Final" class="infraImg"
             onclick="infraCalendario('txtDataFim',this,false,'<?= InfraData::getStrDataAtual() ?>');"/>
        <!--FIM DATA FINAL-->
    </div>
    <!-- FIM PERIODO DE EXPEDICAO-->

    <!--TIPO DE DESTINATÁRIO-->
    <div class="bloco" style="min-width:120px; width:14%">
        <label class="infraLabelOpcional" for="selTipoDestinatario">Tipo de Destinatário:</label>
        <select onchange="pesquisar();" class="infraSelect selectPadrao" name="selTipoDestinatario" style="min-width: 100%" id="selTipoDestinatario" tabindex="<?= PaginaSEIExterna::getInstance()->getProxTabDados(); ?>">
            <option value=""></option>
            <?php foreach ($arrTipoDestinatario as $chaveTipoDestinatario => $itemTipoDestinatario) : ?>
                <option <?php if($selTipoDestinatario == $chaveTipoDestinatario) echo "selected='selected'"; ?> value="<?php echo $chaveTipoDestinatario; ?>"><?php echo $itemTipoDestinatario; ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <!--FIM TIPO DE DESTINATÁRIO-->
    
    <!--TIPO DE INTIMACAO-->
    <div class="bloco" style="min-width:160px; width:14%">
        <label class="infraLabelOpcional" for="selTipoIntimacao">Tipo de Intimação:</label>
        <select onchange="pesquisar();" class="infraSelect " name="selTipoIntimacao"  style="width: 160px" id="selTipoIntimacao" tabindex="<?= PaginaSEIExterna::getInstance()->getProxTabDados(); ?>">
          <?php echo $selTipoIntimacao; ?>
        </select>
    </div>
    <!--FIM TIPO DE INTIMACAO-->

    <!--CUMPRIMENTO DA INTIMACAO-->
    <div class="bloco" style="min-width:120px; width:14%">
        <label class="infraLabelOpcional" for="selCumprimentoIntimacao">Situação:</label>
        <select onchange="pesquisar();" class="infraSelect selectPadrao" name="selCumprimentoIntimacao" style="width: 13%; min-width: 100%;" id="selCumprimentoIntimacao"
                tabindex="<?= PaginaSEIExterna::getInstance()->getProxTabDados(); ?>">
            <?php echo $comboSituacao; ?>
        </select>
    </div>
    <!--FIM CUMPRIMENTO DA INTIMACAO-->

<?php
PaginaSEIExterna::getInstance()->fecharAreaDados();
PaginaSEIExterna::getInstance()->montarAreaTabela($strResultado, $numRegistros);
?>
</form>
<?php
    PaginaSEIExterna::getInstance()->fecharBody();
    PaginaSEIExterna::getInstance()->fecharHead();