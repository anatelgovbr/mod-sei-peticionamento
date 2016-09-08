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
   
   <label> 
   Os documentos que constarão da petição devem ser anexados abaixo, sendo responsabilidade do Usuário Externo a integridade de seu conteúdo e correspondência com o preenchimento deste formulário. Ainda, os Níveis de Acesso abaixo indicados estão condicionados à análise por servidor público, que poderá, motivadamente, alterá-los a qualquer momento sem necessidade de prévio aviso.
   </label>
   
   <br/><br/>
   
   <? 
   //[RN8]	O sistema deve verificar na funcionalidade “Gerir Tipos de Processo para Peticionamento” 
   // se o documento principal selecionado foi “Externo (Anexação de Arquivo)”. 
   // Caso tenha sido selecionado ao preencher os dados do novo peticionamento, o sistema permitirá 
   // anexar o arquivo conforme o tipo informado. O sistema deve recuperar o tamanho e tipo de arquivo 
   // permitidos das funcionalidades “Gerir Tamanho Arquivo Permitido” e “Gerir Extensões Arquivo”.
   //- obter tamanho maximo de arquivo da configuraçao do modulo
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
          $strTamanhoMaximoPrincipal = $numValorTamanhoMaximo . " mb"; 	
   	  }
   	  
   	  if( $numValorTamanhoMaximoComplementar != null && $numValorTamanhoMaximoComplementar > 0 ){   	  
   	  	$strTamanhoMaximoComplementar = $numValorTamanhoMaximoComplementar . " mb";   	  
   	  }
   	   
   }
   
   //checando se Documento Principal está parametrizado para "Externo (Anexação de Arquivo) ou Gerador (editor do SEI)
   $gerado = $ObjTipoProcessoPeticionamentoDTO->getStrSinDocGerado();
   $externo = $ObjTipoProcessoPeticionamentoDTO->getStrSinDocExterno();

   //[RN7]	O sistema deve verificar na funcionalidade “Gerir Tipos de Processo para Peticionamento” se o 
   // documento principal selecionado foi “Gerado (Editor e Modelo do SEI)”. Caso tenha sido selecionado ao preencher os 
   // dados do novo peticionamento, o usuário irá editar o conteúdo do documento principal diretamente no editor HTML do SEI.
   if( $externo == 'S' ) { ?>
       <label style="font-weight: bold;" for="fileArquivoPrincipal"> Documento principal (<?= $strTamanhoMaximoPrincipal ?>):</label><br/>
       <input style="margin-top:0.3%" type="file" name="fileArquivoPrincipal" id="fileArquivoPrincipal" /> <br/><br/>
   <? }?>
   
   <!-- Quando é EXTERNO exibir "Tipo" e "Complemento" -->
   <? if( $externo == 'S' ) { ?>	   
	   
	   <div style=" float: left; width: 15%;"> 
	         <label style="font-weight: bold;" class="infraLabel"> Tipo: </label> <br/> 
	         
	         <label class="infraLabel">
	         <?= $strTipoDocumentoPrincipal ?>          
	         </label>
	         
	         <select id="tipoDocumentoPrincipal" style="display:none;">
	         <option value="<?= $serieDTO->getNumIdSerie() ?>"><?= $strTipoDocumentoPrincipal ?></option>
	         </select>
	         
	   </div>

       <div style=" float: left; width: 250px;"> 
         <label style="font-weight: bold;" class="infraLabel"> Complemento (limitado a 30 caracteres): </label> <br/> 
         <input type="text" class="infraText" name="complementoPrincipal" id="complementoPrincipal" style="width:220px;" maxlength="30" />          
       </div>
       
   <? } ?>
   
   <? if( $gerado == 'S' ) { ?>
       
       <!-- DOCUMENTO PRINCIPAL DO TIPO GERADO -->
       <div style=" float: left; width: 90%;"> 
         <label style="font-weight: bold;"> Documento Principal:</label> 
         <label class="infraLabelRadio" onclick="abrirJanelaDocumento()"><?= $strTipoDocumentoPrincipal ?> (clique aqui para editar conteúdo) </label>          
       </div>
       
       <!-- 
       Quando o Documento Principal é do tipo "Gerado", colocar as combos de Nível de Acesso e Hipótese Legal na linha de baixo, 
       para que não fique truncado a identificação do nome do Tipo do Documento e o parenteses existente.   
       [PENDENTE!!!!!!!!!]
	   -->
	   
	   <div style="clear: both;"> &nbsp; </div>
	    
	   <div style=" float: left; width: 15%;"> 
         <label style="font-weight: bold;" class="infraLabel"> Nível de Acesso: </label> <br/> 
         
         <? if( $isUsuarioExternoPodeIndicarNivelAcesso == 'S') { ?>
         <select class="infraSelect" width="140" id="nivelAcesso1" name="nivelAcesso1" onchange="selectNivelAcesso('nivelAcesso1', 'hipoteseLegal1')" style="width:140px;" >
            <option value=""></option>
            <option value="0">Público</option>
            <option value="1">Restrito</option>
         </select>
         <? } else if( $isNivelAcessoPadrao == 'S' ) { ?>
            <label class="infraLabel"><?= $strNomeNivelAcessoPadrao ?></label>
            <input type="hidden" name="nivelAcesso1" id="nivelAcesso1" value="<?= $nivelAcessoPadrao ?>" />
         <? } ?>
         
       </div>  

	   <!--  DICA -->
	   <!--  <div id="divhipoteseLegal1" style=" float: left; width: 38% ; display:none;"> -->
	   <? if( $isNivelAcessoPadrao == 'S' && $nivelAcessoPadrao == "1"  ) { ?>
	     <div id="divhipoteseLegal1" style="float: left; width: 38% ; display:block;"> 
	    <? } else { ?>
	      <div id="divhipoteseLegal1" style=" float: left; width: 38% ; display:none;">
	    <? } ?>
	         
	         
	    <?if($isConfigHipoteseLegal && $isNivelAcessoPadrao != 'S' ) { ?>  
	         
	         <label style="font-weight: bold;" class="infraLabel"> Hipótese Legal: </label> <br/>
	         <select class="infraSelect" id="hipoteseLegal1" name="hipoteseLegal1" width="285" style="width:285px; float: left; margin-right: 5px;">
	            <option value=""></option>
	            <? //$isConfigHipoteseLegal $arrHipoteseLegal
	            if( $isConfigHipoteseLegal && is_array( $arrHipoteseLegal ) && count( $arrHipoteseLegal ) > 0 ) { 
	                 foreach( $arrHipoteseLegal as $itemObj ) { 
	            ?>
	                <option value="<?= $itemObj->getNumIdHipoteseLegal() ?>">
	                <?= $itemObj->getStrNome() ?> 
	                ( <?= $itemObj->getStrBaseLegal() ?> ) </option>
	            <?    } 
	               } ?>
	         </select>
	         
	         <? } else if($isConfigHipoteseLegal && $isNivelAcessoPadrao == 'S' && $nivelAcessoPadrao == "1" ) { ?>
	         
	          <label style="font-weight: bold;" class="infraLabel"> Hipótese Legal: </label> <br/>
	         <label class="infraLabel"> <?= $strHipoteseLegalPadrao ?> </label>
	         <input type="hidden" name="hipoteseLegal1" id="hipoteseLegal1" value="<?= $idHipoteseLegalPadrao ?>" />   
	         	         
	         <? } ?>
	         
	         <? if( $externo == 'S') { ?>
	         <input type="button" class="infraButton" value="Adicionar" name="btAddDocumentos" onclick="validarUploadArquivo('1')" />
	         <? } ?>
	         
         </div>
	   
       
   <? } else { ?>
   
   <!-- DOCUMENTO PRINCIPAL DO TIPO EXTERNO -->
   <div style="clear: both;"> &nbsp; </div>
   
   <div style=" float: left; width: 15%;"> 
    
         <label style="font-weight: bold;" class="infraLabel"> Nível de Acesso: </label> <br/> 
         <select class="infraSelect" width="140" id="nivelAcesso1" name="nivelAcesso1" style="width:140px;" 
                 onchange="selectNivelAcesso('nivelAcesso1', 'hipoteseLegal1')" >
         <? if( $isUsuarioExternoPodeIndicarNivelAcesso == 'S') { ?>
            <option value=""></option>
            <option value="0">Público</option>
            <option value="1">Restrito</option>
         <? } else if( $isNivelAcessoPadrao == 'S' ) { ?>
             <option selected="selected" value="<?= $nivelAcessoPadrao ?>"><?= $strNomeNivelAcessoPadrao ?></option>
         <? } ?>
         </select>
   
   </div> 
      
	      <? if( $isNivelAcessoPadrao == 'S' && $nivelAcessoPadrao == "1"  ) { ?>
	         <div id="divhipoteseLegal1" style="float: left; width: 38% ; display:block;"> 
	      <? } else { ?>
	         <div id="divhipoteseLegal1" style=" float: left; width: 38% ; display:none;">
	      <? } ?>
	         
	         <?if($isConfigHipoteseLegal) { ?>  
	         
	         <label style="font-weight: bold;" class="infraLabel"> Hipótese Legal: </label> <br/>
	         <select class="infraSelect" id="hipoteseLegal1" name="hipoteseLegal1" width="285" style="width:285px; float: left; margin-right: 5px;">
	            <option value=""></option>
	            <? //$isConfigHipoteseLegal $arrHipoteseLegal
	            if( $isConfigHipoteseLegal && is_array( $arrHipoteseLegal ) && count( $arrHipoteseLegal ) > 0 ) { 
	                 foreach( $arrHipoteseLegal as $itemObj ) { 
	            ?>
	                <option value="<?= $itemObj->getNumIdHipoteseLegal() ?>">
	                <?= $itemObj->getStrNome() ?> 
	                ( <?= $itemObj->getStrBaseLegal() ?> ) </option>
	            <?    } 
	               } ?>
	         </select>
	         
	         <? } else { ?>
	         <label class="infraLabel"> &nbsp; </label> <br/>	         
	         <? } ?>
	         
    </div>
    
    <div style="clear: both;"> &nbsp; </div>
    
     <div style=" float: left; margin-right: 20px;">
    
	   <label style="font-weight: bold;" class="infraLabel"> Formato de Documento:</label>
	    
	   <span id="spnPublico">
		      <label id="lblPublico" class="infraLabelRadio">
		      <img src="/infra_css/imagens/ajuda.gif" title="Ajuda" alt="Ajuda" class="infraImg" onclick="exibirAjudaFormatoDocumento()"/> 
		      </label>
		</span>
	    
	    <input type="radio" name="formatoDocumentoPrincipal" value="nato" id="rdNato1_1" onclick="selecionarFormatoNatoDigitalPrincipal()" /> 
	    <label for="rdNato1_1" class="infraLabelRadio">
	    Nato-digital
	    </label>
	      
	    <input type="radio" name="formatoDocumentoPrincipal" value="digitalizado" id="rdDigitalizado1_2" onclick="selecionarFormatoDigitalizadoPrincipal()" /> 
	    <label for="rdDigitalizado1_2" class="infraLabelRadio">
	    Digitalizado
	    </label>  
    
    </div>
     
    <div id="camposDigitalizadoPrincipal" style=" float: left; width: 50%; display: none;">
     
      <div style="float: left; width: 100%;">
	      
	      <label style="font-weight: bold;" class="infraLabel"> Documento Objeto da Digitalização era: </label> <br/>
	  
	      <select class="infraSelect" id="TipoConferenciaPrincipal" name="TipoConferenciaPrincipal" width="285" 
	              style="width: 285px; float:left; margin-right: 5px;">
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
      
      <div id="camposDigitalizadoPrincipalBotao" style=" float: left; width: 50%; display: none;">
         <input type="button" class="infraButton" value="Adicionar" onclick="validarUploadArquivo('1')">
      </div>
          
    <? } ?>
        
   <div style="clear: both;"> &nbsp; </div>
    
    <? if( $externo == 'S') { ?>
    
    <table id="tbDocumentoPrincipal" class="infraTable" width="95%" >
    		
    	  <!--  	
          <caption class="infraCaption">Lista de Documentos principais</caption> 
         -->
           
           <tr>
               <th class="infraTh" style="width:30%;"> Nome do arquivo </th>
               <th class="infraTh" style="width:70px;"> Data </th>
               <th class="infraTh"> Tamanho </th>
               <th class="infraTh" style="width:30%;"> Documento </th>
               <th class="infraTh" style="width:120px;"> Nível de acesso </th>
               
               <!--  colunas nao exibidas na tela, usadas apenas para guardar valor na grid (note que estao com display:none) -->
               <th class="infraTh" style="display: none;"> Hipotese legal </th>
               <th class="infraTh" style="display: none;"> Formato de documento </th>
               <th class="infraTh" style="display: none;"> Tipo de Conferencia </th>
               <th class="infraTh" style="display: none;"> Nome Upload servidor </th>
               <th class="infraTh" style="display: none;"> ID Tipo de Documento </th>
               
               <!-- Coluna de ações (Baixar, remover) da grid -->
               <th align="center" class="infraTh" style="width:70px;"> Ações </th>                              
           </tr>
           
       </table>
       
       <br/><br/>
       
       <? } ?>
       </form>
       
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
	   
	   <label class="infraLabelObrigatorio" for="fileArquivoEssencial"> Documentos essenciais (<?= $strTamanhoMaximoComplementar ?>):</label><br/>
       
       <input style="margin-top:0.3%" type="file" id="fileArquivoEssencial" name="fileArquivoEssencial" size="50" /> <br/><br/>
   
       <div style=" float: left; width: 15%;"> 
       
         <label style="font-weight: bold;" class="infraLabel"> Tipo: </label> <br/> 
       	
       	 <?
       	 //print_r( $arrRelTipoProcessoSeriePeticionamentoDTO ); die();
       	 ?>
       	 
         <select class="infraSelect" width="140" style="width:140px;" name="tipoDocumentoEssencial" id="tipoDocumentoEssencial" >
            <option value=""></option>
            <? 
            
            
            if( is_array( $arrRelTipoProcessoSeriePeticionamentoDTO ) && count( $arrRelTipoProcessoSeriePeticionamentoDTO ) > 0 ){
            	
				foreach( $arrRelTipoProcessoSeriePeticionamentoDTO as $item ){
				  
				  $serieDTO = new SerieDTO();
				  $serieDTO->retTodos();
				  $serieDTO->setNumIdSerie($item->getNumIdSerie()); 
				  $serieDTO = $serieRN->consultarRN0644( $serieDTO );
				  //print_r( $serieDTO ); die();
				  
			?>
            		<option value="<?= $item->getNumIdSerie() ?>"><?= $serieDTO->getStrNome() ?></option>
            <?	}
            }
            ?>
            
         </select>
          
   </div>
   
   <div style=" float: left; width: 250px;"> 
         <label style="font-weight: bold;" class="infraLabel"> Complemento (limitado a 30 caracteres): </label> <br/> 
         <input type="text" class="infraText" name="complementoEssencial" id="complementoEssencial" style="width: 220px;" maxlength="30" />         
   </div>
   
   <div style="clear: both;"> &nbsp; </div>
   
   <div style=" float: left; width: 15%;"> 
   
         <label style="font-weight: bold;" class="infraLabel"> Nível de Acesso: </label> <br/> 
         
         <select class="infraSelect" name="nivelAcesso2" id="nivelAcesso2" onchange="selectNivelAcesso('nivelAcesso2', 'hipoteseLegal2')" width="140" style="width:140px;" >
         <? if( $isUsuarioExternoPodeIndicarNivelAcesso == 'S') { ?>
            <option value=""></option>
            <option value="0">Público</option>
            <option value="1">Restrito</option>
         <? } else if( $isNivelAcessoPadrao == 'S' ) { ?>
             <option selected="selected" value="<?= $nivelAcessoPadrao ?>"><?= $strNomeNivelAcessoPadrao ?></option>
         <? } ?>
         </select>
         
   </div> 
   
    <? if( $isNivelAcessoPadrao == 'S' && $nivelAcessoPadrao == ProtocoloRN::$NA_RESTRITO ) { ?>       
      <div id="divhipoteseLegal2" style=" float: left; width: 38%; display:block;">
    <? } else { ?>
      <div id="divhipoteseLegal2" style=" float: left; width: 38%; display:none;">
    <? } ?>
    
         <? if($isConfigHipoteseLegal) { ?>
	         
	         <label style="font-weight: bold;" class="infraLabel"> Hipótese Legal: </label> <br/>
	         
	         <select class="infraSelect" width="285" id="hipoteseLegal2" name="hipoteseLegal2" style="width:285px; float: left; margin-right: 5px;">
	            <option value=""></option>
	            <? 
	            if( $isConfigHipoteseLegal && is_array( $arrHipoteseLegal ) && count( $arrHipoteseLegal ) > 0 ) { 
	                 foreach( $arrHipoteseLegal as $itemObj ) { 
	            ?>
	                <option value="<?= $itemObj->getNumIdHipoteseLegal() ?>">
	                <?= $itemObj->getStrNome() ?> 
	                ( <?= $itemObj->getStrBaseLegal() ?> ) </option>
	            <?    } 
	               } ?>
	         </select>
         
         <? } else { ?>
         	
         	<label class="infraLabel"> &nbsp; </label> <br/>
         
         <? } ?>
   
   </div>
         
   <!--        
   <input type="button" class="infraButton" value="Adicionar" name="btAddDocumentos" onclick="addDocumentoEssencial()" />
   -->
           
   <div style="clear: both;"> &nbsp; </div>
   
   <div style=" float: left; margin-right: 20px;">
    
	   <label style="font-weight: bold;" class="infraLabel"> Formato de Documento:</label>
	    
	   <span id="spnPublico">
		      <label id="lblPublico" class="infraLabelRadio">
		      <img src="/infra_css/imagens/ajuda.gif" title="Ajuda" alt="Ajuda" class="infraImg" onclick="exibirAjudaFormatoDocumento()"/> 
		      </label>
		</span>
	    
	    <input type="radio" name="formatoDocumentoEssencial" value="nato" id="rdNato2_1" onclick="selecionarFormatoNatoDigitalEssencial()" /> 
	    <label for="rdNato2_1" class="infraLabelRadio">
	    Nato-digital
	    </label>
	      
	    <input type="radio" name="formatoDocumentoEssencial" value="digitalizado" id="rdDigitalizado2_2" onclick="selecionarFormatoDigitalizadoEssencial()" /> 
	    <label for="rdDigitalizado2_2" class="infraLabelRadio">
	    Digitalizado
	    </label>  
    
    </div>
     
    <div id="camposDigitalizadoEssencial" style=" float: left; width: 50%; display: none;">
     
      <div style="float: left; width: 100%;">
	      
	      <label style="font-weight: bold;" class="infraLabel"> Documento Objeto da Digitalização era: </label> <br/>
	  
	      <select class="infraSelect" id="TipoConferenciaEssencial" name="TipoConferenciaEssencial" width="285" 
	              style="width: 285px; float:left; margin-right: 5px;">
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
      
      <div id="camposDigitalizadoEssencialBotao" style=" float: left; width: 50%; display: none;">
         <input type="button" class="infraButton" value="Adicionar" onclick="validarUploadArquivo('2')">
      </div>
      
     <div style="clear: both;"> &nbsp; </div>
    
     <table id="tbDocumentoEssencial" name="tbDocumentoEssencial" class="infraTable" style="width:95%;">
 		
 		<!--     
        <caption class="infraCaption"><?=PaginaSEIExterna::getInstance()->gerarCaptionTabela("Lista de Documentos Essenciais",0)?></caption>
        -->
    		<tr>
    			<th style="display:none;">ID</th>
    			<th class="infraTh" style="width:30%;">Nome</th>
    			<th class="infraTh" style="width: 70px;" align="center">Data</th>
    			<th style="display:none;">Bytes</th>
    			<th class="infraTh" align="center">Tamanho</th>
    			<th class="infraTh" align="center" style="width:30%;">Documento</th>
    			<th class="infraTh" style="width: 120px;" align="center">Nível de acesso</th>
    			
    			<!--  colunas nao exibidas na tela, usadas apenas para guardar valor na grid (note que estao com display:none) -->
               <th class="infraTh" style="display: none;"> Hipotese legal </th>
               <th class="infraTh" style="display: none;"> Formato de documento </th>
               <th class="infraTh" style="display: none;"> Tipo de Conferencia </th>
               <th class="infraTh" style="display: none;"> Nome Upload servidor </th>
               <th class="infraTh" style="display: none;"> ID Tipo de Documento </th>
               
               <!-- Coluna de ações (Baixar, remover) da grid -->
    			<th class="infraTh" style="width: 70px;">Ações</th>
    			
    		</tr>
    		   
       </table> <br/><br/>
       
       <!-- =================================== FIM DOCUMENTOS ESSENCIAIS =====================================================  -->
       </form>
       
       <form method="post" id="frmDocumentosComplementares" enctype="multipart/form-data" action="<?= $strLinkUploadDocComplementar ?>">
       
       <input type="hidden" id="hdnDocComplementar" name="hdnDocComplementar" value="<?=$_POST['hdnDocComplementar']?>"/>
	   <input type="hidden" id="hdnDocComplementarInicial" name="hdnDocComplementarInicial" value="<?=$_POST['hdnDocComplementarInicial']?>"/>
	   
       <!-- ================================== INICIO DOCUMENTOS COMPLEMENTARES  =============================================== -->
       <? 
	   // TODO o bloco de seleçao de documento essencial pode sumir da tela
       // conforme parametrizaçao da Administraçao do modulo 
       $objRelTipoProcessoSeriePeticionamentoDTO = new RelTipoProcessoSeriePeticionamentoDTO();
	   $objRelTipoProcessoSeriePeticionamentoDTO->retTodos();
	   $objRelTipoProcessoSeriePeticionamentoDTO->setStrStaTipoDoc( RelTipoProcessoSeriePeticionamentoRN::$DOC_COMPLEMENTAR );
	   $objRelTipoProcessoSeriePeticionamentoDTO->setNumIdTipoProcessoPeticionamento( $objTipoProcDTO->getNumIdTipoProcessoPeticionamento() );
	   $objRelTipoProcessoSeriePeticionamentoRN = new RelTipoProcessoSeriePeticionamentoRN();
	   
	   $arrRelTipoProcessoSeriePeticionamentoDTO = $objRelTipoProcessoSeriePeticionamentoRN->listar( $objRelTipoProcessoSeriePeticionamentoDTO );
	   //print_r( $arrRelTipoProcessoSeriePeticionamentoDTO ); die();
	   
	   if( is_array( $arrRelTipoProcessoSeriePeticionamentoDTO ) && count( $arrRelTipoProcessoSeriePeticionamentoDTO ) > 0 ){ ?>
	     
	     <label class="infraLabel" for="fileArquivoComplementar"> Documentos complementares (<?= $strTamanhoMaximoComplementar ?>):</label><br/>
	     
	     <input style="margin-top:0.3%" type="file" id="fileArquivoComplementar" name="fileArquivoComplementar" size="50" /> <br/><br/>
   
       <div style=" float: left; width: 15%;"> 
       
         <label style="font-weight: bold;" class="infraLabel"> Tipo: </label> <br/> 
       
         <select class="infraSelect" width="140" style="width:140px;" name="tipoDocumentoComplementar" id="tipoDocumentoComplementar" >
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
   
   <div style=" float: left; width: 250px;"> 
         <label style="font-weight: bold;" class="infraLabel"> Complemento (limitado a 30 caracteres): </label> <br/> 
         <input type="text" class="infraText" name="complementoComplementar" id="complementoComplementar" style="width: 220px;" maxlength="30" />         
   </div>
   
   <div style="clear: both;"> &nbsp; </div>
   
   <div style=" float: left; width: 15%;"> 
   
         <label style="font-weight: bold;" class="infraLabel"> Nível de Acesso: </label> <br/> 
         
         <select class="infraSelect" name="nivelAcesso3" id="nivelAcesso3" onchange="selectNivelAcesso('nivelAcesso3', 'hipoteseLegal3')" width="140" style="width:140px;" >
         <? if( $isUsuarioExternoPodeIndicarNivelAcesso == 'S') { ?>
            <option value=""></option>
            <option value="0">Público</option>
            <option value="1">Restrito</option>
         <? } else if( $isNivelAcessoPadrao == 'S' ) { ?>
             <option selected="selected" value="<?= $nivelAcessoPadrao ?>"><?= $strNomeNivelAcessoPadrao ?></option>
         <? } ?>
         </select>
         
   </div> 
   
   <? if( $isNivelAcessoPadrao == 'S' && $nivelAcessoPadrao == ProtocoloRN::$NA_RESTRITO ) { ?>
     <div id="divhipoteseLegal3" style=" float: left; width: 38%; display:block;">
   <? } else { ?>
     <div id="divhipoteseLegal3" style=" float: left; width: 38%; display:none;">
   <? } ?>       
       
         <? if($isConfigHipoteseLegal) { ?>
	         
	         <label style="font-weight: bold;" class="infraLabel"> Hipótese Legal: </label> <br/>
	         
	         <select class="infraSelect" width="285" id="hipoteseLegal3" name="hipoteseLegal3" style="width:285px; float: left; margin-right: 5px;">
	            <option value=""></option>
	            <? 
	            if( $isConfigHipoteseLegal && is_array( $arrHipoteseLegal ) && count( $arrHipoteseLegal ) > 0 ) { 
	                 foreach( $arrHipoteseLegal as $itemObj ) { 
	            ?>
	                <option value="<?= $itemObj->getNumIdHipoteseLegal() ?>">
	                <?= $itemObj->getStrNome() ?> 
	                ( <?= $itemObj->getStrBaseLegal() ?> ) </option>
	            <?    } 
	               } ?>
	         </select>
         
         <? } else { ?>
         	
         	<label class="infraLabel"> &nbsp; </label> <br/>
         
         <? } ?>
   
   </div>
                 
   <div style="clear: both;"> &nbsp; </div>
   
   <div style=" float: left; margin-right: 20px;">
    
	   <label style="font-weight: bold;" class="infraLabel"> Formato de Documento:</label>
	    
	   <span id="spnPublico">
		      <label id="lblPublico" class="infraLabelRadio">
		      <img src="/infra_css/imagens/ajuda.gif" title="Ajuda" alt="Ajuda" class="infraImg" onclick="exibirAjudaFormatoDocumento()"/> 
		      </label>
		</span>
	    
	    <input type="radio" name="formatoDocumentoComplementar" value="nato" id="rdNato3_1" onclick="selecionarFormatoNatoDigitalComplementar()" /> 
	    <label for="rdNato3_1" class="infraLabelRadio">
	    Nato-digital
	    </label>
	      
	    <input type="radio" name="formatoDocumentoComplementar" value="digitalizado" id="rdDigitalizado3_2" onclick="selecionarFormatoDigitalizadoComplementar()" /> 
	    <label for="rdDigitalizado3_2" class="infraLabelRadio">
	    Digitalizado
	    </label>  
    
    </div>
     
    <div id="camposDigitalizadoComplementar" style=" float: left; width: 50%; display: none;">
     
	      <div style="float: left; width: 100%;">
		      
		      <label style="font-weight: bold;" class="infraLabel"> Documento Objeto da Digitalização era: </label> <br/>
		  
		      <select class="infraSelect" id="TipoConferenciaComplementar" name="TipoConferenciaComplementar" width="285" 
		              style="width: 285px; float:left; margin-right: 5px;">
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
     
     <div id="camposDigitalizadoComplementarBotao" style=" float: left; width: 50%; display: none;">
        <input type="button" class="infraButton" value="Adicionar" onclick="validarUploadArquivo('3')">
     </div>
      
     <div style="clear: both;"> &nbsp; </div>
    
     <table id="tbDocumentoComplementar" name="tbDocumentoComplementar" class="infraTable" style="width:95%;">
    	
    	<!--  
        <caption class="infraCaption"><?=PaginaSEIExterna::getInstance()->gerarCaptionTabela("Documentos complementares",0)?></caption>
         -->
       
    		<tr>
    			<th style="display:none;">ID</th>
    			<th class="infraTh" style="width:30%;">Nome</th>
    			<th class="infraTh" style="width: 70px;" align="center">Data</th>
    			<th style="display:none;">Bytes</th>
    			<th class="infraTh" align="center">Tamanho</th>
    			<th class="infraTh" align="center" style="width:30%;">Documento</th>
    			<th class="infraTh" style="width: 120px;" align="center">Nível de acesso</th>
    		    
    		    <!--  colunas nao exibidas na tela, usadas apenas para guardar valor na grid (note que estao com display:none) -->
                <th class="infraTh" style="display: none;"> Hipotese legal </th>
                <th class="infraTh" style="display: none;"> Formato de documento </th>
                <th class="infraTh" style="display: none;"> Tipo de Conferencia </th>
                <th class="infraTh" style="display: none;"> Nome Upload servidor </th>
                <th class="infraTh" style="display: none;"> ID Tipo de Documento </th>
               
                <!-- Coluna de ações (Baixar, remover) da grid -->
    			<th class="infraTh" style="width: 70px;">Ações</th>    			
    		</tr>
    		   
       </table> <br/><br/>
	     
       <? } ?>
       
       <!-- ================================== FIM DOCUMENTOS COMPLEMENTARES  =============================================== -->
       </form>
       
<? } ?>     
</fieldset>

<!-- =========================== -->
 <!--  FIM BLOCO / ÁREA DOCUMENTOS -->
 <!-- =========================== -->