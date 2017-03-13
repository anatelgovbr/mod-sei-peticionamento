<?
/**
* ANATEL
*
* 01/08/2016 - criado por marcelo.bezerra@cast.com.br - CAST
*
* Página contendo área "Documentos" da página
* Contém todo o FIELDSET da área Documentos, englobando Documentos Principais, Essenciais e Complementares
* Essa página é incluida na página principal do cadastro de peticionamento
*/
//Acao para upload de documento principal
$strLinkUploadDocPrincipal = SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=peticionamento_usuario_externo_upload_doc_principal');

//Acao para upload de documento essencial
$strLinkUploadDocEssencial = SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=peticionamento_usuario_externo_upload_doc_essencial');

//Acao para upload de documento complementar
$strLinkUploadDocComplementar = SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=peticionamento_usuario_externo_upload_doc_complementar');

?>
 <!-- =========================== -->
 <!--  INÍCIO BLOCO / ÁREA DOCUMENTOS -->
 <!-- =========================== --> 
 <form method="post" id="frmDocumentoPrincipal" enctype="multipart/form-data" action="<?= $strLinkUploadDocPrincipal ?>">
 
 <input type="hidden" id="hdnDocPrincipal" name="hdnDocPrincipal" value="<?=$_POST['hdnDocPrincipal']?>"/>
 <input type="hidden" id="hdnDocPrincipalInicial" name="hdnDocPrincipalInicial" value="<?=$_POST['hdnDocPrincipalInicial']?>"/>
 
 <fieldset id="field3" class="infraFieldset sizeFieldset">
 <legend class="infraLegend">&nbsp; Documentos &nbsp;</legend>
	<br/>
	<label>Os documentos devem ser carregados abaixo, sendo de sua exclusiva responsabilidade a conformidade entre os dados informados e os documentos. Os Níveis de Acesso que forem indicados abaixo estarão condicionados à análise por servidor público, que poderá, motivadamente, alterá-los a qualquer momento sem necessidade de prévio aviso.</label>
	<br/><br/>
   
   <?
   $objTamanhoMaximoDTO = new TamanhoArquivoPermitidoPeticionamentoDTO();
   $objTamanhoMaximoDTO->setStrSinAtivo('S');
   $objTamanhoMaximoDTO->retTodos();
   $objTamanhoMaximoRN = new TamanhoArquivoPermitidoPeticionamentoRN();
   $arrTamanhoMaximo = $objTamanhoMaximoRN->listarTamanhoMaximoConfiguradoParaUsuarioExterno( $objTamanhoMaximoDTO );
   
   $strTamanhoMaximoPrincipal = "Limite não configurado na Administração do Sistema.";
   $strTamanhoMaximoComplementar = "Limite não configurado na Administração do Sistema.";
   
   if( is_array( $arrTamanhoMaximo ) && count( $arrTamanhoMaximo ) > 0 ){
   	   
      $numValorTamanhoMaximo = $arrTamanhoMaximo[0]->getNumValorDocPrincipal();
      $numValorTamanhoMaximoComplementar = $arrTamanhoMaximo[0]->getNumValorDocComplementar();
   	  
   	  if( $numValorTamanhoMaximo != null && $numValorTamanhoMaximo > 0 ){   	      
          $strTamanhoMaximoPrincipal = $numValorTamanhoMaximo . " Mb"; 	
   	  }
   	  
   	  if( $numValorTamanhoMaximoComplementar != null && $numValorTamanhoMaximoComplementar > 0 ){   	  
   	  	$strTamanhoMaximoComplementar = $numValorTamanhoMaximoComplementar . " Mb";   	  
   	  }
   	   
   }

   //checando se Documento Principal está parametrizado para "Externo (Anexação de Arquivo) ou Gerador (editor do SEI)
   $gerado = $ObjTipoProcessoPeticionamentoDTO->getStrSinDocGerado();
   $externo = $ObjTipoProcessoPeticionamentoDTO->getStrSinDocExterno();

   if( $externo == 'S' ) { ?>
       <label class="infraLabelObrigatorio" for="fileArquivoPrincipal">Documento Principal (<?
       echo $strTamanhoMaximoPrincipal;
       echo "<input type=hidden name=hdnTamArquivoPrincipal id=hdnTamArquivoPrincipal value='" . $strTamanhoMaximoPrincipal . "'>"; 
       ?>):</label><br/>
       <input style="margin-top:0.3%" type="file" name="fileArquivoPrincipal" id="fileArquivoPrincipal" /> <br/><br/>
   <? }?>
   
   <!-- Quando é EXTERNO exibir "Tipo" e "Complemento" -->
   <? if( $externo == 'S' ) { ?>	   
	   
		<div style="float: left; height: 42px; margin-right: 10px;">
			<label id="lblPublico" class="infraLabelObrigatorio">Tipo de Documento: <img src="<?= PaginaSEI::getInstance()->getDiretorioImagensGlobal() ?>/ajuda.gif" name="ajuda" <?= PaginaSEI::montarTitleTooltip($strMsgTooltipTipoDocumentoPrincipal) ?> alt="Ajuda" class="infraImg"/></label>
			<br/>
			<label class="infraLabel">
			<?= $strTipoDocumentoPrincipal ?>
			</label>
			<select id="tipoDocumentoPrincipal" style="display:none;">
			<option value="<?= $serieDTO->getNumIdSerie() ?>"><?= $strTipoDocumentoPrincipal ?></option>
			</select>
		</div>

		<div style="float: left; height: 42px;">
			<label id="lblPublico" class="infraLabelObrigatorio">Complemento do Tipo de Documento: <img src="<?= PaginaSEI::getInstance()->getDiretorioImagensGlobal() ?>/ajuda.gif" name="ajuda" <?= PaginaSEI::montarTitleTooltip($strMsgTooltipComplementoTipoDocumento) ?> alt="Ajuda" class="infraImg"/></label>
			<br/>
			<input type="text" class="infraText" name="complementoPrincipal" id="complementoPrincipal" style="width: 240px;" maxlength="40" />
		</div>
       
   <? } ?>
   
   <? if( $gerado == 'S' ) { ?>

       <!-- DOCUMENTO PRINCIPAL DO TIPO GERADO -->
		<br />
		<div style="float: left; width: 90%;">
			<label class="infraLabelObrigatorio">Documento Principal:&nbsp;&nbsp;<img src="<?= PaginaSEI::getInstance()->getDiretorioImagensLocal() ?>/sei_formulario1.gif" name="formulario" <?= PaginaSEI::montarTitleTooltip($strMsgTooltipTipoDocumentoPrincipalFormulario) ?> alt="Formulário"/></label>&nbsp;&nbsp;
			<label class="infraLabelRadio" onclick="abrirJanelaDocumento()"><?= $strTipoDocumentoPrincipal ?> &nbsp;&nbsp;(clique aqui para editar conteúdo)</label>
		</div>

		<div style="clear: both;"> &nbsp; </div>

		<div style="float: left;">
			<? if( $isUsuarioExternoPodeIndicarNivelAcesso == 'S') { ?>
				<label id="lblPublico" class="infraLabelObrigatorio">Nível de Acesso: <img src="<?= PaginaSEI::getInstance()->getDiretorioImagensGlobal() ?>/ajuda.gif" name="ajuda" <?= PaginaSEI::montarTitleTooltip($strMsgTooltipNivelAcesso) ?> alt="Ajuda" class="infraImg"/></label> <br/>
				<select class="infraSelect" id="nivelAcesso1" name="nivelAcesso1" onchange="selectNivelAcesso('nivelAcesso1', 'hipoteseLegal1')" style="width: 120px; margin-right: 10px;">
				<?=$strItensSelNivelAcesso?>
				</select>
			<? } else if( $isNivelAcessoPadrao == 'S' ) { ?>
				<label id="lblPublico" class="infraLabelObrigatorio">Nível de Acesso: <img src="<?= PaginaSEI::getInstance()->getDiretorioImagensGlobal() ?>/ajuda.gif" name="ajuda" <?= PaginaSEI::montarTitleTooltip($strMsgTooltipNivelAcessoPadraoPreDefinido) ?> alt="Ajuda" class="infraImg"/></label> <br/>
				<label class="infraLabel"><?= $strNomeNivelAcessoPadrao ?></label>
				<input type="hidden" name="nivelAcesso1" id="nivelAcesso1" value="<?= $nivelAcessoPadrao ?>" />
			<? } ?>
		</div>

	   <? if( $isNivelAcessoPadrao == 'S' && $nivelAcessoPadrao == "1"  ) { ?>
			<div id="divhipoteseLegal1" style="float: left; width: 70%; display:block;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	    <? } else { ?>
			<div id="divhipoteseLegal1" style="float: left; width: 70%; display:none;">
	    <? } ?>
	         
	    <?if($isConfigHipoteseLegal && $isNivelAcessoPadrao != 'S' ) { ?>  
	         
	         <label id="lblPublico" class="infraLabelObrigatorio">Hipótese Legal: <img src="<?= PaginaSEI::getInstance()->getDiretorioImagensGlobal() ?>/ajuda.gif" name="ajuda" <?= PaginaSEI::montarTitleTooltip($strMsgTooltipHipoteseLegal) ?> alt="Ajuda" class="infraImg"/></label> <br/>
	         <select class="infraSelect" id="hipoteseLegal1" name="hipoteseLegal1" style="width: 95%; float: left; margin-right: 5px;">
	            <option value=""></option>
	            <?
	            if( $isConfigHipoteseLegal && is_array( $arrHipoteseLegal ) && count( $arrHipoteseLegal ) > 0 ) { 
	                 foreach( $arrHipoteseLegal as $itemObj ) { 
	            ?>
	                <option value="<?= $itemObj->getNumIdHipoteseLegal() ?>">
	                <?= $itemObj->getStrNome() ?> 
	                (<?= $itemObj->getStrBaseLegal() ?>) </option>
	            <?    } 
	               } ?>
	         </select>
	         
	         <? } else if($isConfigHipoteseLegal && $isNivelAcessoPadrao == 'S' && $nivelAcessoPadrao == "1" ) { ?>
	         
	          <label id="lblPublico" class="infraLabelObrigatorio">Hipótese Legal: <img src="<?= PaginaSEI::getInstance()->getDiretorioImagensGlobal() ?>/ajuda.gif" name="ajuda" <?= PaginaSEI::montarTitleTooltip($strMsgTooltipHipoteseLegalPadraoPreDefinido) ?> alt="Ajuda" class="infraImg"/></label> <br/>
	         <label class="infraLabel">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <?= $strHipoteseLegalPadrao ?> </label>
	         <input type="hidden" name="hipoteseLegal1" id="hipoteseLegal1" value="<?= $idHipoteseLegalPadrao ?>" />   
	         	         
	         <? } ?>
	         
	         <? if( $externo == 'S') { ?>
	         <input type="button" class="infraButton" value="Adicionar" name="btAddDocumentos" onclick="validarUploadArquivo('1')" />
	         <? } ?>
	         
         </div>
	   
       
   <? } else { ?>
   
   <!-- DOCUMENTO PRINCIPAL DO TIPO EXTERNO -->
	<div style="clear: both;"> &nbsp; </div>

	<div style="float: left;">
		<? if( $isUsuarioExternoPodeIndicarNivelAcesso == 'S') { ?>
			<label id="lblPublico" class="infraLabelObrigatorio">Nível de Acesso: <img src="<?= PaginaSEI::getInstance()->getDiretorioImagensGlobal() ?>/ajuda.gif" name="ajuda" <?= PaginaSEI::montarTitleTooltip($strMsgTooltipNivelAcesso) ?> alt="Ajuda" class="infraImg"/></label> <br/>
			<select class="infraSelect" id="nivelAcesso1" name="nivelAcesso1" onchange="selectNivelAcesso('nivelAcesso1', 'hipoteseLegal1')" style="width: 120px; margin-right: 10px;">
			<?=$strItensSelNivelAcesso?>
			</select>
		<? } else if( $isNivelAcessoPadrao == 'S' ) { ?>
			<label id="lblPublico" class="infraLabelObrigatorio">Nível de Acesso: <img src="<?= PaginaSEI::getInstance()->getDiretorioImagensGlobal() ?>/ajuda.gif" name="ajuda" <?= PaginaSEI::montarTitleTooltip($strMsgTooltipNivelAcessoPadraoPreDefinido) ?> alt="Ajuda" class="infraImg"/></label> <br/>
			<label class="infraLabel"><?= $strNomeNivelAcessoPadrao ?></label>
			<input type="hidden" name="nivelAcesso1" id="nivelAcesso1" value="<?= $nivelAcessoPadrao ?>" />
		<? } ?>
            
	</div>

	      <? if( $isNivelAcessoPadrao == 'S' && $nivelAcessoPadrao == "1"  ) { ?>
			<div id="divhipoteseLegal1" style="float: left; width: 70%; display:block;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	      <? } else { ?>
			<div id="divhipoteseLegal1" style="float: left; width: 70%; display:none;">
	      <? } ?>
	         
	         <?if($isConfigHipoteseLegal && $isNivelAcessoPadrao != 'S' ) { ?>  
	         
	         <label id="lblPublico" class="infraLabelObrigatorio">Hipótese Legal: <img src="<?= PaginaSEI::getInstance()->getDiretorioImagensGlobal() ?>/ajuda.gif" name="ajuda" <?= PaginaSEI::montarTitleTooltip($strMsgTooltipHipoteseLegal) ?> alt="Ajuda" class="infraImg"/></label> <br/>
	         <select class="infraSelect" id="hipoteseLegal1" name="hipoteseLegal1" style="width: 95%; float: left; margin-right: 5px;">
	            <option value=""></option>
	            <?
	            if( $isConfigHipoteseLegal && is_array( $arrHipoteseLegal ) && count( $arrHipoteseLegal ) > 0 ) { 
	                 foreach( $arrHipoteseLegal as $itemObj ) { 
	            ?>
	                <option value="<?= $itemObj->getNumIdHipoteseLegal() ?>">
	                <?= $itemObj->getStrNome() ?> 
	                (<?= $itemObj->getStrBaseLegal() ?>) </option>
	            <?    } 
	               } ?>
	         </select>
	         
	          <? } else if($isConfigHipoteseLegal && $isNivelAcessoPadrao == 'S' && $nivelAcessoPadrao == "1" ) { ?>
	         
	          <label id="lblPublico" class="infraLabelObrigatorio">Hipótese Legal: <img src="<?= PaginaSEI::getInstance()->getDiretorioImagensGlobal() ?>/ajuda.gif" name="ajuda" <?= PaginaSEI::montarTitleTooltip($strMsgTooltipHipoteseLegalPadraoPreDefinido) ?> alt="Ajuda" class="infraImg"/></label> <br/>
	         <label class="infraLabel">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <?= $strHipoteseLegalPadrao ?> </label>
	         <input type="hidden" name="hipoteseLegal1" id="hipoteseLegal1" value="<?= $idHipoteseLegalPadrao ?>" />   
	         	         
	         <? } ?>
	         
	</div>

	<div style="clear: both;">&nbsp;</div>

	<div style="float: left; margin-right: 20px;">
		<label id="lblPublico" class="infraLabelObrigatorio">Formato: <img src="<?= PaginaSEI::getInstance()->getDiretorioImagensGlobal() ?>/ajuda.gif" name="ajuda" <?= PaginaSEI::montarTitleTooltip($strMsgTooltipFormato) ?> alt="Ajuda" class="infraImg"/></label>
		<input type="radio" name="formatoDocumentoPrincipal" value="nato" id="rdNato1_1" onclick="selecionarFormatoNatoDigitalPrincipal()" />
		<label for="rdNato1_1" class="infraLabelRadio">Nato-digital</label>
	    <input type="radio" name="formatoDocumentoPrincipal" value="digitalizado" id="rdDigitalizado1_2" onclick="selecionarFormatoDigitalizadoPrincipal()" />
	    <label for="rdDigitalizado1_2" class="infraLabelRadio">Digitalizado</label>
	</div>
     
	<div id="camposDigitalizadoPrincipal" style="float: left; width: 55%; display: none;">
		<div style="float: left; width: 100%;">
			<label class="infraLabelObrigatorio">Conferência com o documento digitalizado:</label> <br/>
				<select class="infraSelect" id="TipoConferenciaPrincipal" name="TipoConferenciaPrincipal" width="285" style="width: 285px; float:left; margin-right: 5px;">
					<option value=""></option>
					<? foreach( $arrTipoConferencia as $tipoConferencia ){
					echo "<option value=' " . $tipoConferencia->getNumIdTipoConferencia() . "'>";
					echo $tipoConferencia->getStrDescricao();
					echo "</option>";
					} ?>
				</select>
				<input type="button" class="infraButton" value="Adicionar" onclick="validarUploadArquivo('1')">
		</div>
	</div>

	<div id="camposDigitalizadoPrincipalBotao" style="float: left; width: 15%;">
		<input type="button" class="infraButton" value="Adicionar" onclick="validarUploadArquivo('1')">
	</div>

    <? } ?>

	<div style="clear: both;"> &nbsp; </div>

    <? if( $externo == 'S') { ?>

		<table id="tbDocumentoPrincipal" name="tbDocumentoPrincipal" class="infraTable" style="width:95%;">
           
    		<tr>
    			<th class="infraTh" style="width:25%;">Nome do Arquivo</th>
				<th class="infraTh" style="width:80px;" align="center">Data</th>
    			<th class="infraTh" style="width:80px;" align="center">Tamanho</th>
    			<th class="infraTh" style="width:25%;" align="center">Documento</th>
    			<th class="infraTh" style="width:120px;" align="center">Nível de Acesso</th>
    		    
    		    <!--  colunas nao exibidas na tela, usadas apenas para guardar valor na grid (note que estao com display:none) -->
                <th class="infraTh" style="display: none;">Hipótese Legal</th>
                <th class="infraTh" style="display: none;">Formato</th>
                <th class="infraTh" style="display: none;">Tipo de Conferência</th>
                <th class="infraTh" style="display: none;">Nome Upload servidor</th>
                <th class="infraTh" style="display: none;">ID Tipo de Documento</th>
                <th class="infraTh" style="display: none;">Complemento</th>
                <th class="infraTh" style="width: 120px;" align="center">Formato</th>
               
                <!-- Coluna de ações (Baixar, remover) da grid -->
    			<th align="center" class="infraTh" style="width:50px;">Ações</th>
    		</tr>
    		   
		</table> <br/><br/>
		
		<? } ?>
		</form>
		<!-- ================================== FIM DOCUMENTO PRINCIPAL  =============================================== -->
       
       <form method="post" id="frmDocumentosEssenciais" enctype="multipart/form-data" action="<?= $strLinkUploadDocEssencial ?>">
       
       <input type="hidden" id="hdnDocEssencial" name="hdnDocEssencial" value="<?=$_POST['hdnDocEssencial']?>"/>
	   <input type="hidden" id="hdnDocEssencialInicial" name="hdnDocEssencialInicial" value="<?=$_POST['hdnDocEssencialInicial']?>"/>
       
       <!-- ================================== INICIO DOCUMENTOS ESSENCIAIS  =============================================== -->
       <? 
       $objRelTipoProcessoSeriePeticionamentoDTO = new RelTipoProcessoSeriePeticionamentoDTO();
	   $objRelTipoProcessoSeriePeticionamentoDTO->retTodos();
	   $objRelTipoProcessoSeriePeticionamentoDTO->setStrStaTipoDoc( RelTipoProcessoSeriePeticionamentoRN::$DOC_ESSENCIAL );
	   $objRelTipoProcessoSeriePeticionamentoDTO->setNumIdTipoProcessoPeticionamento( $objTipoProcDTO->getNumIdTipoProcessoPeticionamento() );
	   $objRelTipoProcessoSeriePeticionamentoRN = new RelTipoProcessoSeriePeticionamentoRN();
	   
	   $arrRelTipoProcessoSeriePeticionamentoDTO = $objRelTipoProcessoSeriePeticionamentoRN->listar( $objRelTipoProcessoSeriePeticionamentoDTO );
	   
	   if( is_array( $arrRelTipoProcessoSeriePeticionamentoDTO ) && count( $arrRelTipoProcessoSeriePeticionamentoDTO ) > 0 ){ ?>
	   
		<hr style="border:none; padding:0; margin:5px 6px 12px 6px; border-top:medium double #333" />
		<label class="infraLabelObrigatorio" for="fileArquivoEssencial">Documentos Essenciais (<?
		echo $strTamanhoMaximoComplementar;
		echo "<input type=hidden name=hdnTamArquivoEssencial id=hdnTamArquivoEssencial value='" . $strTamanhoMaximoComplementar . "'>"; 
		?>):</label><br/>
       
       <input style="margin-top:0.3%" type="file" id="fileArquivoEssencial" name="fileArquivoEssencial" size="50" /> <br/><br/>
   
		<div style="float: left; height: 42px;">
			<label id="lblPublico" class="infraLabelObrigatorio">Tipo de Documento: <img src="<?= PaginaSEI::getInstance()->getDiretorioImagensGlobal() ?>/ajuda.gif" name="ajuda" <?= PaginaSEI::montarTitleTooltip($strMsgTooltipTipoDocumento) ?> alt="Ajuda" class="infraImg"/></label>
			<br/>
       	 
			<select class="infraSelect" style="width: 200px; margin-right: 10px;" name="tipoDocumentoEssencial" id="tipoDocumentoEssencial" >
            <option value=""></option>
            <? 
            
            if( is_array( $arrRelTipoProcessoSeriePeticionamentoDTO ) && count( $arrRelTipoProcessoSeriePeticionamentoDTO ) > 0 ){
            	
				foreach( $arrRelTipoProcessoSeriePeticionamentoDTO as $item ){
				  
					$serieDTO = new SerieDTO();
					$serieDTO->retTodos();
					$serieDTO->setNumIdSerie($item->getNumIdSerie()); 
					$serieDTO = $serieRN->consultarRN0644( $serieDTO );
				  
			?>
            		<option value="<?= $item->getNumIdSerie() ?>"><?= $serieDTO->getStrNome() ?></option>
            <?	}
            }
            ?>
            
         </select>
          
		</div>

		<div style="float: left; height: 42px;">
			<label id="lblPublico" class="infraLabelObrigatorio">Complemento do Tipo de Documento: <img src="<?= PaginaSEI::getInstance()->getDiretorioImagensGlobal() ?>/ajuda.gif" name="ajuda" <?= PaginaSEI::montarTitleTooltip($strMsgTooltipComplementoTipoDocumento) ?> alt="Ajuda" class="infraImg"/></label>
			<br/>
			<input type="text" class="infraText" name="complementoEssencial" id="complementoEssencial" style="width: 240px;" maxlength="40" />
		</div>

	<div style="clear: both;"> &nbsp; </div>

	<div style="float: left;">
			<? if( $isUsuarioExternoPodeIndicarNivelAcesso == 'S') { ?>
				<label id="lblPublico" class="infraLabelObrigatorio">Nível de Acesso: <img src="<?= PaginaSEI::getInstance()->getDiretorioImagensGlobal() ?>/ajuda.gif" name="ajuda" <?= PaginaSEI::montarTitleTooltip($strMsgTooltipNivelAcesso) ?> alt="Ajuda" class="infraImg"/></label> <br/>
				<select class="infraSelect" id="nivelAcesso2" name="nivelAcesso2" onchange="selectNivelAcesso('nivelAcesso2', 'hipoteseLegal2')" style="width: 120px; margin-right: 10px;">
				<?=$strItensSelNivelAcesso?>
				</select>
			<? } else {?>
				<label id="lblPublico" class="infraLabelObrigatorio">Nível de Acesso: <img src="<?= PaginaSEI::getInstance()->getDiretorioImagensGlobal() ?>/ajuda.gif" name="ajuda" <?= PaginaSEI::montarTitleTooltip($strMsgTooltipNivelAcessoPadraoPreDefinido) ?> alt="Ajuda" class="infraImg"/></label> <br/>
				<label class="infraLabelRadio"><?= $strNomeNivelAcessoPadrao ?></label>
				<input type="hidden" value="<?= $nivelAcessoPadrao ?>" id="nivelAcesso2" name="nivelAcesso2" />
			<?} ?>
	</div>
   
    <? if( $isNivelAcessoPadrao == 'S' && $nivelAcessoPadrao == ProtocoloRN::$NA_RESTRITO ) { ?>       
		<div id="divhipoteseLegal2" style="float: left; width: 70%; display:block;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    <? } else { ?>
		<div id="divhipoteseLegal2" style="float: left; width: 70%; display:none;">
    <? } ?>
    
         <? if($isConfigHipoteseLegal && $isNivelAcessoPadrao != 'S') { ?>
	         
	         <label id="lblPublico" class="infraLabelObrigatorio">Hipótese Legal: <img src="<?= PaginaSEI::getInstance()->getDiretorioImagensGlobal() ?>/ajuda.gif" name="ajuda" <?= PaginaSEI::montarTitleTooltip($strMsgTooltipHipoteseLegal) ?> alt="Ajuda" class="infraImg"/></label> <br/>
	         
	         <select class="infraSelect" id="hipoteseLegal2" name="hipoteseLegal2" style="width: 95%; float: left; margin-right: 5px;">
	            <option value=""></option>
	            <? 
	            if( $isConfigHipoteseLegal && is_array( $arrHipoteseLegal ) && count( $arrHipoteseLegal ) > 0 ) { 
	                 foreach( $arrHipoteseLegal as $itemObj ) { 
	            ?>
	                <option value="<?= $itemObj->getNumIdHipoteseLegal() ?>">
	                <?= $itemObj->getStrNome() ?> 
	                (<?= $itemObj->getStrBaseLegal() ?>) </option>
	            <?    } 
	               } ?>
	         </select>
         
         <? } else if($isConfigHipoteseLegal && $isNivelAcessoPadrao == 'S' && $nivelAcessoPadrao == "1" ){ ?>
         	
         	<label id="lblPublico" class="infraLabelObrigatorio">Hipótese Legal: <img src="<?= PaginaSEI::getInstance()->getDiretorioImagensGlobal() ?>/ajuda.gif" name="ajuda" <?= PaginaSEI::montarTitleTooltip($strMsgTooltipHipoteseLegalPadraoPreDefinido) ?> alt="Ajuda" class="infraImg"/></label> <br/>
	         <label class="infraLabel">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <?= $strHipoteseLegalPadrao ?> </label>
	         <input type="hidden" name="hipoteseLegal2" id="hipoteseLegal2" value="<?= $idHipoteseLegalPadrao ?>" />   
         
         <? } ?>
   
	</div>

	<div style="clear: both;">&nbsp;</div>

	<div style="float: left; margin-right: 20px;">
		<label id="lblPublico" class="infraLabelObrigatorio">Formato: <img src="<?= PaginaSEI::getInstance()->getDiretorioImagensGlobal() ?>/ajuda.gif" name="ajuda" <?= PaginaSEI::montarTitleTooltip($strMsgTooltipFormato) ?> alt="Ajuda" class="infraImg"/></label>
		<input type="radio" name="formatoDocumentoEssencial" value="nato" id="rdNato2_1" onclick="selecionarFormatoNatoDigitalEssencial()" />
		<label for="rdNato2_1" class="infraLabelRadio">Nato-digital</label>
		<input type="radio" name="formatoDocumentoEssencial" value="digitalizado" id="rdDigitalizado2_2" onclick="selecionarFormatoDigitalizadoEssencial()" />
		<label for="rdDigitalizado2_2" class="infraLabelRadio">Digitalizado</label>
	</div>

	<div id="camposDigitalizadoEssencial" style="float: left; width: 55%; display: none;">
		<div style="float: left; width: 100%;">
			<label class="infraLabelObrigatorio">Conferência com o documento digitalizado:</label> <br/>
				<select class="infraSelect" id="TipoConferenciaEssencial" name="TipoConferenciaEssencial" width="285" style="width: 285px; float:left; margin-right: 5px;">
					<option value=""></option>
					<? foreach( $arrTipoConferencia as $tipoConferencia ){
					echo "<option value=' " . $tipoConferencia->getNumIdTipoConferencia() . "'>";
					echo $tipoConferencia->getStrDescricao();
					echo "</option>";
					} ?>
				</select>
				<input type="button" class="infraButton" value="Adicionar" onclick="validarUploadArquivo('2')">
		</div>
	</div>
      
	<div id="camposDigitalizadoEssencialBotao" style="float: left; width: 15%;">
		<input type="button" class="infraButton" value="Adicionar" onclick="validarUploadArquivo('2')">
	</div>
      
	<div style="clear: both;">&nbsp;</div>
    
		<table id="tbDocumentoEssencial" name="tbDocumentoEssencial" class="infraTable" style="width:95%;">

    		<tr>
    			<th class="infraTh" style="width:25%;">Nome do Arquivo</th>
				<th class="infraTh" style="width:80px;" align="center">Data</th>
    			<th class="infraTh" style="width:80px;" align="center">Tamanho</th>
    			<th class="infraTh" style="width:25%;" align="center">Documento</th>
    			<th class="infraTh" style="width:120px;" align="center">Nível de Acesso</th>
    		    
    		    <!--  colunas nao exibidas na tela, usadas apenas para guardar valor na grid (note que estao com display:none) -->
                <th class="infraTh" style="display: none;">Hipótese Legal</th>
                <th class="infraTh" style="display: none;">Formato</th>
                <th class="infraTh" style="display: none;">Tipo de Conferência</th>
                <th class="infraTh" style="display: none;">Nome Upload servidor</th>
                <th class="infraTh" style="display: none;">ID Tipo de Documento</th>
                <th class="infraTh" style="display: none;">Complemento</th>
                <th class="infraTh" style="width: 120px;" align="center">Formato</th>
               
                <!-- Coluna de ações (Baixar, remover) da grid -->
    			<th align="center" class="infraTh" style="width:50px;">Ações</th>
    		</tr>
    		   
		</table> <br/>
       
		<? } ?>
		</form>
		<!-- ================================== FIM DOCUMENTOS ESSENCIAIS  =============================================== -->
	   
       <form method="post" id="frmDocumentosComplementares" enctype="multipart/form-data" action="<?= $strLinkUploadDocComplementar ?>">
       
       <input type="hidden" id="hdnDocComplementar" name="hdnDocComplementar" value="<?=$_POST['hdnDocComplementar']?>"/>
	   <input type="hidden" id="hdnDocComplementarInicial" name="hdnDocComplementarInicial" value="<?=$_POST['hdnDocComplementarInicial']?>"/>
	   
       <!-- ================================== INICIO DOCUMENTOS COMPLEMENTARES  =============================================== -->
       <?php 
	   //o bloco de seleçao de documento essencial pode sumir da tela
       // conforme parametrizaçao da Administraçao do modulo 
       $objRelTipoProcessoSeriePeticionamentoDTO = new RelTipoProcessoSeriePeticionamentoDTO();
	   $objRelTipoProcessoSeriePeticionamentoDTO->retTodos();
	   $objRelTipoProcessoSeriePeticionamentoDTO->setStrStaTipoDoc( RelTipoProcessoSeriePeticionamentoRN::$DOC_COMPLEMENTAR );
	   $objRelTipoProcessoSeriePeticionamentoDTO->setNumIdTipoProcessoPeticionamento( $objTipoProcDTO->getNumIdTipoProcessoPeticionamento() );
	   $objRelTipoProcessoSeriePeticionamentoRN = new RelTipoProcessoSeriePeticionamentoRN();
	   
	   $arrRelTipoProcessoSeriePeticionamentoDTO = $objRelTipoProcessoSeriePeticionamentoRN->listar( $objRelTipoProcessoSeriePeticionamentoDTO );
	   
	   if( is_array( $arrRelTipoProcessoSeriePeticionamentoDTO ) && count( $arrRelTipoProcessoSeriePeticionamentoDTO ) > 0 ){ ?>
	     
		<hr style="border:none; padding:0; margin:5px 6px 12px 6px; border-top:medium double #333" />
		<label class="infraLabel" for="fileArquivoComplementar">Documentos Complementares (<?
       		echo $strTamanhoMaximoComplementar;
       		echo "<input type=hidden name=hdnTamArquivoComplementar id=hdnTamArquivoComplementar value='" . $strTamanhoMaximoComplementar . "'>"; 
		?>):</label><br/>
	     
	     <input style="margin-top:0.3%" type="file" id="fileArquivoComplementar" name="fileArquivoComplementar" size="50" /> <br/><br/>
   
		<div style="float: left; height: 42px;">
			<label id="lblPublico" class="infraLabelObrigatorio">Tipo de Documento: <img src="<?= PaginaSEI::getInstance()->getDiretorioImagensGlobal() ?>/ajuda.gif" name="ajuda" <?= PaginaSEI::montarTitleTooltip($strMsgTooltipTipoDocumento) ?> alt="Ajuda" class="infraImg"/></label>
			<br/>
			
			<select class="infraSelect" style="width: 200px; margin-right: 10px;" name="tipoDocumentoComplementar" id="tipoDocumentoComplementar" >
            <option value=""></option>
            <? 
            
            if( is_array( $arrRelTipoProcessoSeriePeticionamentoDTO ) && count( $arrRelTipoProcessoSeriePeticionamentoDTO ) > 0 ){
            	
				foreach( $arrRelTipoProcessoSeriePeticionamentoDTO as $item ){
				
					$serieDTO = new SerieDTO();
					$serieDTO->retTodos();
					$serieDTO->setNumIdSerie($item->getNumIdSerie()); 
				    $serieDTO = $serieRN->consultarRN0644( $serieDTO );
			
			?>
            		<option value="<?= $item->getNumIdSerie() ?>"><?= $serieDTO->getStrNome() ?></option>
            <?	}
            }
            ?>
            
         </select>
          
		</div>

		<div style="float: left; height: 42px;">
			<label id="lblPublico" class="infraLabelObrigatorio">Complemento do Tipo de Documento: <img src="<?= PaginaSEI::getInstance()->getDiretorioImagensGlobal() ?>/ajuda.gif" name="ajuda" <?= PaginaSEI::montarTitleTooltip($strMsgTooltipComplementoTipoDocumento) ?> alt="Ajuda" class="infraImg"/></label>
			<br/>
			<input type="text" class="infraText" name="complementoComplementar" id="complementoComplementar" style="width: 240px;" maxlength="40" />
		</div>
   
	<div style="clear: both;"> &nbsp; </div>

	<div style="float: left;">
		<? if( $isUsuarioExternoPodeIndicarNivelAcesso == 'S') { ?>
			<label id="lblPublico" class="infraLabelObrigatorio">Nível de Acesso: <img src="<?= PaginaSEI::getInstance()->getDiretorioImagensGlobal() ?>/ajuda.gif" name="ajuda" <?= PaginaSEI::montarTitleTooltip($strMsgTooltipNivelAcesso) ?> alt="Ajuda" class="infraImg"/></label> <br/>
			<select class="infraSelect" id="nivelAcesso3" name="nivelAcesso3" onchange="selectNivelAcesso('nivelAcesso3', 'hipoteseLegal3')" style="width: 120px; margin-right: 10px;">
			<?=$strItensSelNivelAcesso?>
			</select>
		<? } else {?>
			<label id="lblPublico" class="infraLabelObrigatorio">Nível de Acesso: <img src="<?= PaginaSEI::getInstance()->getDiretorioImagensGlobal() ?>/ajuda.gif" name="ajuda" <?= PaginaSEI::montarTitleTooltip($strMsgTooltipNivelAcessoPadraoPreDefinido) ?> alt="Ajuda" class="infraImg"/></label> <br/>
			<label class="infraLabelRadio"><?= $strNomeNivelAcessoPadrao ?></label>
			<input type="hidden" value="<?= $nivelAcessoPadrao ?>" id="nivelAcesso3" name="nivelAcesso3" />
		<?} ?>
	</div>

	<? if( $isNivelAcessoPadrao == 'S' && $nivelAcessoPadrao == ProtocoloRN::$NA_RESTRITO ) { ?>
		<div id="divhipoteseLegal3" style="float: left; width: 70%; display:block;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	<? } else { ?>
		<div id="divhipoteseLegal3" style="float: left; width: 70%; display:none;">
	<? } ?>
       
         <? if($isConfigHipoteseLegal && $isNivelAcessoPadrao != 'S') { ?>
	         
	         <label id="lblPublico" class="infraLabelObrigatorio">Hipótese Legal: <img src="<?= PaginaSEI::getInstance()->getDiretorioImagensGlobal() ?>/ajuda.gif" name="ajuda" <?= PaginaSEI::montarTitleTooltip($strMsgTooltipHipoteseLegal) ?> alt="Ajuda" class="infraImg"/></label> <br/>
	         
	         <select class="infraSelect" id="hipoteseLegal3" name="hipoteseLegal3" style="width: 95%; float: left; margin-right: 5px;">
	            <option value=""></option>
	            <? 
	            if( $isConfigHipoteseLegal && is_array( $arrHipoteseLegal ) && count( $arrHipoteseLegal ) > 0 ) { 
	                 foreach( $arrHipoteseLegal as $itemObj ) { 
	            ?>
	                <option value="<?= $itemObj->getNumIdHipoteseLegal() ?>">
	                <?= $itemObj->getStrNome() ?> 
	                (<?= $itemObj->getStrBaseLegal() ?>) </option>
	            <?    } 
	               } ?>
	         </select>
         
         <? } else if($isConfigHipoteseLegal && $isNivelAcessoPadrao == 'S' && $nivelAcessoPadrao == "1" ){ ?>
         	
         	 <label id="lblPublico" class="infraLabelObrigatorio">Hipótese Legal: <img src="<?= PaginaSEI::getInstance()->getDiretorioImagensGlobal() ?>/ajuda.gif" name="ajuda" <?= PaginaSEI::montarTitleTooltip($strMsgTooltipHipoteseLegalPadraoPreDefinido) ?> alt="Ajuda" class="infraImg"/></label> <br/>
	         <label class="infraLabel">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <?= $strHipoteseLegalPadrao ?> </label>
	         <input type="hidden" name="hipoteseLegal3" id="hipoteseLegal3" value="<?= $idHipoteseLegalPadrao ?>" />   
         
         <? } ?>
   
	</div>

	<div style="clear: both;">&nbsp;</div>

	<div style="float: left; margin-right: 20px;">
		<label id="lblPublico" class="infraLabelObrigatorio">Formato: <img src="<?= PaginaSEI::getInstance()->getDiretorioImagensGlobal() ?>/ajuda.gif" name="ajuda" <?= PaginaSEI::montarTitleTooltip($strMsgTooltipFormato) ?> alt="Ajuda" class="infraImg"/></label>
		<input type="radio" name="formatoDocumentoComplementar" value="nato" id="rdNato3_1" onclick="selecionarFormatoNatoDigitalComplementar()" />
		<label for="rdNato3_1" class="infraLabelRadio">Nato-digital</label>
		<input type="radio" name="formatoDocumentoComplementar" value="digitalizado" id="rdDigitalizado3_2" onclick="selecionarFormatoDigitalizadoComplementar()" />
		<label for="rdDigitalizado3_2" class="infraLabelRadio">Digitalizado</label>
	</div>

	<div id="camposDigitalizadoComplementar" style="float: left; width: 55%; display: none;">
		<div style="float: left; width: 100%;">
			<label class="infraLabelObrigatorio">Conferência com o documento digitalizado:</label> <br/>
				<select class="infraSelect" id="TipoConferenciaComplementar" name="TipoConferenciaComplementar" width="285" style="width: 285px; float:left; margin-right: 5px;">
					<option value=""></option>
					<? foreach( $arrTipoConferencia as $tipoConferencia ){
					echo "<option value=' " . $tipoConferencia->getNumIdTipoConferencia() . "'>";
					echo $tipoConferencia->getStrDescricao();
					echo "</option>";
					} ?>
				</select>
				<input type="button" class="infraButton" value="Adicionar" onclick="validarUploadArquivo('3')">
		</div>
	</div>
     
     <div id="camposDigitalizadoComplementarBotao" style="float: left; width: 15%;">
        <input type="button" class="infraButton" value="Adicionar" onclick="validarUploadArquivo('3')">
     </div>
      
     <div style="clear: both;"> &nbsp; </div>
    
		<table id="tbDocumentoComplementar" name="tbDocumentoComplementar" class="infraTable" style="width:95%;">
       
    		<tr>
    			<th class="infraTh" style="width:25%;">Nome do Arquivo</th>
				<th class="infraTh" style="width:80px;" align="center">Data</th>
    			<th class="infraTh" style="width:80px;" align="center">Tamanho</th>
    			<th class="infraTh" style="width:25%;" align="center">Documento</th>
    			<th class="infraTh" style="width:120px;" align="center">Nível de Acesso</th>
    		    
    		    <!--  colunas nao exibidas na tela, usadas apenas para guardar valor na grid (note que estao com display:none) -->
                <th class="infraTh" style="display: none;">Hipótese Legal</th>
                <th class="infraTh" style="display: none;">Formato</th>
                <th class="infraTh" style="display: none;">Tipo de Conferência</th>
                <th class="infraTh" style="display: none;">Nome Upload servidor</th>
                <th class="infraTh" style="display: none;">ID Tipo de Documento</th>
                <th class="infraTh" style="display: none;">Complemento</th>
                <th class="infraTh" style="width: 120px;" align="center">Formato</th>
               
                <!-- Coluna de ações (Baixar, remover) da grid -->
    			<th align="center" class="infraTh" style="width:50px;">Ações</th>
    		</tr>
    		   
		</table> <br/>
	     
		<? } ?>
		</form>
		<!-- ================================== FIM DOCUMENTOS COMPLEMENTARES  =============================================== -->
     
</fieldset>

<!-- =========================== -->
 <!--  FIM BLOCO / ÁREA DOCUMENTOS -->
 <!-- =========================== -->