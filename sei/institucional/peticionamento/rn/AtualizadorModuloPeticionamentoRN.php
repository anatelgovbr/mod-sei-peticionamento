<?
/**
* ANATEL
*
* 21/05/2016 - criado por marcelo.bezerra - CAST
*
*/

require_once dirname(__FILE__).'/../../../SEI.php';

class AtualizadorModuloPeticionamentoRN extends InfraRN {

  private $numSeg = 0;
  private $versaoAtualDesteModulo = '1.0.0'; //atualizaçoes do pacote 10
  private $nomeDesteModulo = 'Peticionamento';
  private $nomeParametroModulo = 'VERSAO_MODULO_PETICIONAMENTO';
  private $historicoVersoes = array('0.0.1','0.0.2','1.0.0');
  
  public function __construct(){
    parent::__construct();
  }

  protected function inicializarObjInfraIBanco(){
    return BancoSEI::getInstance();
  }

  private function inicializar($strTitulo){

    ini_set('max_execution_time','0');
    ini_set('memory_limit','-1');

    try {
      @ini_set('zlib.output_compression','0');
      @ini_set('implicit_flush', '1');
    }catch(Exception $e){}

    ob_implicit_flush();

    InfraDebug::getInstance()->setBolLigado(true);
    InfraDebug::getInstance()->setBolDebugInfra(true);
    InfraDebug::getInstance()->setBolEcho(true);
    InfraDebug::getInstance()->limpar();

    $this->numSeg = InfraUtil::verificarTempoProcessamento();

    $this->logar($strTitulo);
  }

  private function logar($strMsg){
    InfraDebug::getInstance()->gravar($strMsg);
    flush();
  }

  private function finalizar($strMsg=null, $bolErro){

    if (!$bolErro) {
      $this->numSeg = InfraUtil::verificarTempoProcessamento($this->numSeg);
      $this->logar('TEMPO TOTAL DE EXECUÇÃO: ' . $this->numSeg . ' s');
    }else{
      $strMsg = 'ERRO: '.$strMsg;
    }

    if ($strMsg!=null){
      $this->logar($strMsg);
    }

    InfraDebug::getInstance()->setBolLigado(false);
    InfraDebug::getInstance()->setBolDebugInfra(false);
    InfraDebug::getInstance()->setBolEcho(false);
    $this->numSeg = 0;
    die;
  }
  
  /* Contem atualizações da versao 1.0.0 do modulo (atualizaçoes do pacote 10) */
  protected function instalarv100(){
	 
  	try {
  	
	 $objInfraMetaBD = new InfraMetaBD(BancoSEI::getInstance());  	
  	 
	 $this->logar(' EXECUTANDO A INSTALACAO DA VERSAO 1.0.0 DO MODULO PETICIONAMENTO NA BASE DO SEI ');
  	 $this->logar(' CRIANDO A TABELA md_pet_hipotese_legal ');
  	   	 
	 BancoSEI::getInstance()->executarSql(' CREATE TABLE md_pet_hipotese_legal (
	 id_md_pet_hipotese_legal ' . $objInfraMetaBD->tipoNumero() .' NOT NULL )');
	  
	 $objInfraMetaBD->adicionarChavePrimaria('md_pet_hipotese_legal','pk_md_pet_hipotese_legal',array('id_md_pet_hipotese_legal'));
  	 
	 $objInfraMetaBD->adicionarChaveEstrangeira('fk_id_md_pet_hip_legal1','md_pet_hipotese_legal',
	 		array('id_md_pet_hipotese_legal'), 'hipotese_legal',array('id_hipotese_legal'));
	 
	$this->logar(' DROPANDO COLUNA  id_unidade (Não é mais unidade única. Agora terá opção para Peticionamento de Processo Novo para Múltiplas Unidades)');
	$objInfraMetaBD->excluirChaveEstrangeira('md_pet_tipo_processo', 'fk_pet_tp_proc_unidade_02');
	$objInfraMetaBD->excluirIndice('md_pet_tipo_processo', 'fk_pet_tp_proc_unidade_02');	
	BancoSEI::getInstance()->executarSql(' ALTER TABLE md_pet_tipo_processo DROP COLUMN id_unidade');
	 
	$this->logar(' CRIANDO A TABELA md_pet_rel_tp_processo_unid (para permitir multiplas unidades)');
	
	BancoSEI::getInstance()->executarSql('CREATE TABLE md_pet_rel_tp_processo_unid (
	  id_md_pet_tipo_processo ' . $objInfraMetaBD->tipoNumero() .' NOT NULL,
	  id_unidade ' . $objInfraMetaBD->tipoNumero() .' NOT NULL,
	  sta_tp_unidade ' .  $objInfraMetaBD->tipoTextoFixo(1)  .' NOT NULL	  
	)'); 
	
	//Tabelas Abaixo é o problema da modificação da PK (Pk deixou de ser composta e passou a ter SEQ)
	$this->logar(' RECRIANDO tabela md_pet_rel_tp_processo_serie (renomeada para md_pet_rel_tp_proc_serie) ');
	BancoSEI::getInstance()->executarSql(' DROP TABLE md_pet_rel_tp_processo_serie');
	
	BancoSEI::getInstance()->executarSql(' CREATE TABLE md_pet_rel_tp_proc_serie (
			id_md_pet_rel_tipo_proc ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL,
			id_md_pet_tipo_processo ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL,
			id_serie ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL,
			sta_tp_doc ' . $objInfraMetaBD->tipoTextoFixo(1) .' NOT NULL ) ');
	
	//tabela SEQ
	$objInfraMetaBD->adicionarChavePrimaria('md_pet_rel_tp_proc_serie','pk_id_md_pet_rel_tipo_proc',array('id_md_pet_rel_tipo_proc'));
	
	$objInfraMetaBD->adicionarChaveEstrangeira('fk_md_pet_rel_tp_proc_serie1','md_pet_rel_tp_proc_serie',
			array('id_md_pet_tipo_processo'), 'md_pet_tipo_processo',array('id_md_pet_tipo_processo'));

	$objInfraMetaBD->adicionarChaveEstrangeira('fk_md_pet_rel_tp_proc_serie2','md_pet_rel_tp_proc_serie',
			array('id_serie'), 'serie',array('id_serie'));
	
	if (BancoSEI::getInstance() instanceof InfraMySql){
		BancoSEI::getInstance()->executarSql('create table seq_md_pet_rel_tp_proc_serie (id bigint not null primary key AUTO_INCREMENT, campo char(1) null) AUTO_INCREMENT = 1');
	} else if (BancoSEI::getInstance() instanceof InfraSqlServer){
		BancoSEI::getInstance()->executarSql('create table seq_md_pet_rel_tp_proc_serie (id bigint identity(1,1), campo char(1) null)');
	} else if (BancoSEI::getInstance() instanceof InfraOracle){
		BancoSEI::getInstance()->criarSequencialNativa('seq_md_pet_rel_tp_proc_serie', 1);
	}
	
	//CRIANDO NOVO TIPO DE DOCUMENTO "RECIBIO ELETRONICO"
	$this->logar(' CRIANDO MODELO "Modulo_Peticionamento_Recibo_Eletronico_Protocolo"');
	$modeloRN = new ModeloRN();
	$modeloDTO = new ModeloDTO();
	$modeloDTO->retTodos();
	$modeloDTO->setStrNome('Modulo_Peticionamento_Recibo_Eletronico_Protocolo');
	$modeloDTO->setStrSinAtivo('S');
	$modeloDTO = $modeloRN->cadastrar( $modeloDTO );
	
	//adicionando as seções do modelo: Corpo de Texto e Rodapé
	$this->logar(' CRIANDO SEÇAO DO MODELO - Corpo do Texto');
	$secaoModeloRN = new SecaoModeloRN();
	
	$secaoModeloCorpoTextoDTO = new SecaoModeloDTO();
	$secaoModeloCorpoTextoDTO->retTodos();
	$secaoModeloCorpoTextoDTO->setNumIdSecaoModelo(null);
	$secaoModeloCorpoTextoDTO->setNumIdModelo( $modeloDTO->getNumIdModelo() );
	$secaoModeloCorpoTextoDTO->setStrNome('Corpo do Texto');
	$secaoModeloCorpoTextoDTO->setStrConteudo(null);
	$secaoModeloCorpoTextoDTO->setNumOrdem(0);
	$secaoModeloCorpoTextoDTO->setStrSinSomenteLeitura('N');
	$secaoModeloCorpoTextoDTO->setStrSinAssinatura('N');
	$secaoModeloCorpoTextoDTO->setStrSinPrincipal('S');
	$secaoModeloCorpoTextoDTO->setStrSinDinamica('N');
	$secaoModeloCorpoTextoDTO->setStrSinHtml('N');	
	$secaoModeloCorpoTextoDTO->setStrSinCabecalho('N');	
	$secaoModeloCorpoTextoDTO->setStrSinRodape('N');	
	$secaoModeloCorpoTextoDTO->setStrSinAtivo('S');
	
	$secaoModeloCorpoTextoDTO = $secaoModeloRN->cadastrar( $secaoModeloCorpoTextoDTO );
	
	//secao do rodapé
	$this->logar(' CRIANDO SEÇAO DO MODELO - Rodapé');
	$secaoModeloRodapeDTO = new SecaoModeloDTO();
	$secaoModeloRodapeDTO->retTodos();
	$secaoModeloRodapeDTO->setNumIdSecaoModelo(null);
	
	$htmlConteudo = '<hr style="border:none; padding:0; margin:5px 2px 0 2px; border-top:medium double #333" />
    <table border="0" cellpadding="2" cellspacing="0" width="100%">
	<tbody>
		<tr>
			<td align="left" style="font-family:Calibri;font-size:9pt;border:0;" width="50%"><strong>Refer&ecirc;ncia:</strong> Processo n&ordm; @processo@</td>
			<td align="right" style="font-family:Calibri;font-size:9pt;border:0;" width="50%">SEI n&ordm; @documento@</td>
		</tr>
	</tbody>
    </table>';
	
	$secaoModeloRodapeDTO->setNumIdModelo( $modeloDTO->getNumIdModelo() );
	$secaoModeloRodapeDTO->setStrNome('Rodapé');
	$secaoModeloRodapeDTO->setStrConteudo($htmlConteudo);
	$secaoModeloRodapeDTO->setNumOrdem(1000);
	$secaoModeloRodapeDTO->setStrSinSomenteLeitura('S');
	$secaoModeloRodapeDTO->setStrSinAssinatura('N');
	$secaoModeloRodapeDTO->setStrSinPrincipal('N');
	$secaoModeloRodapeDTO->setStrSinDinamica('S');
	$secaoModeloRodapeDTO->setStrSinHtml('S');
	$secaoModeloRodapeDTO->setStrSinCabecalho('N');
	$secaoModeloRodapeDTO->setStrSinRodape('S');
	$secaoModeloRodapeDTO->setStrSinAtivo('S');
	
	$secaoModeloRodapeDTO = $secaoModeloRN->cadastrar( $secaoModeloRodapeDTO );
	
	//Criar o Grupo de Tipo de Documento “Internos do Sistema”.
	$grupoSerieRN = new GrupoSerieRN();
	
	if (BancoSEI::getInstance() instanceof InfraMySql){
	   
	   //verificando antes a situaçao da tabela seq_grupo_serie
	   $arrDados = BancoSEI::getInstance()->consultarSql(' SELECT * FROM seq_grupo_serie ORDER BY id DESC LIMIT 1 ');
	   
	   $grupoSerieDTOLista = new GrupoSerieDTO();
	   $grupoSerieDTOLista->retTodos();	   
	   $grupoSerieDTOLista->setOrd("IdGrupoSerie", InfraDTO::$TIPO_ORDENACAO_DESC );
	   $grupoSerieDTOLista->setNumMaxRegistrosRetorno(1);
	   
	   $arrListaGrupoSerie = $grupoSerieRN->listarRN0778( $grupoSerieDTOLista );
	   
	   //ja tem registro na SEQ, insere apenas se ID da SEQ estiver incorreto 
	   if( $arrDados != null && count( $arrDados ) > 0){
	   	   
	   	  if( $arrDados[0]['id'] < $arrListaGrupoSerie[0]->getNumIdGrupoSerie() ){
	   	  	
	   	  	//INSERT para garantir a SEQ na posiçao correta
	   	    BancoSEI::getInstance()->executarSql('INSERT INTO seq_grupo_serie ( id ) VALUES ( ' . $arrListaGrupoSerie[0]->getNumIdGrupoSerie()  .') ');
	   	  }
	   		
	   	   
	   } 
	   
	   //nao tem registro na SEQ ainda, colocar o ID do grupo_serie mais atual
	   else {
	   	
	      //INSERT para garantir a SEQ na posiçao correta
	   	  BancoSEI::getInstance()->executarSql('INSERT INTO seq_grupo_serie ( id ) VALUES ( ' . $arrListaGrupoSerie[0]->getNumIdGrupoSerie()  .') ');
	   }
	   
	}
	
	$this->logar(' CRIANDO GRUPO DE TIPO DE DOCUMENTO "Internos do Sistema"');
	$grupoSerieDTO = new GrupoSerieDTO();
	$grupoSerieDTO->retTodos();
	$grupoSerieDTO->setNumIdGrupoSerie(null);
	
	$grupoSerieDTO->setStrNome('Internos do Sistema');
	$grupoSerieDTO->setStrDescricao('Tipos de Documentos internos do sistema');
	$grupoSerieDTO->setStrSinAtivo('S');
	$grupoSerieDTO = $grupoSerieRN->cadastrarRN0775( $grupoSerieDTO );
	
	//Criar o Tipo de Documento “Recibo Eletrônico de Protocolo”
	$this->logar(' CRIANDO TIPO DE DOCUMENTO Recibo Eletrônico de Protocolo');
	$serieDTO = new SerieDTO();
	$serieDTO->retTodos();
	$serieRN = new SerieRN();
	
	$serieDTO->setNumIdSerie(null);
	$serieDTO->setNumIdGrupoSerie( $grupoSerieDTO->getNumIdGrupoSerie() );
	$serieDTO->setStrStaNumeracao( SerieRN::$TN_SEM_NUMERACAO );
	$serieDTO->setStrStaAplicabilidade( SerieRN::$TA_INTERNO );
	$serieDTO->setNumIdModeloEdoc(null);
	$serieDTO->setNumIdModelo( $modeloDTO->getNumIdModelo() );
	$serieDTO->setStrNome('Recibo Eletrônico de Protocolo');
	$serieDTO->setStrDescricao('Utilizado para a geração automática do Recibo Eletrônico de Protocolo nos Peticionamentos Eletrônicos realizados por Usuário Externo diretamente no Acesso Externo do SEI.');	 
	$serieDTO->setStrSinInteressado('S');
	$serieDTO->setStrSinDestinatario('N');
	$serieDTO->setStrSinAssinaturaPublicacao('S');
	$serieDTO->setStrSinInterno('S');	
	$serieDTO->setStrSinAtivo('S');	
	$serieDTO->setArrObjRelSerieAssuntoDTO( array() );
	$serieDTO->setArrObjRelSerieVeiculoPublicacaoDTO( array() );
	$serieDTO = $serieRN->cadastrarRN0642( $serieDTO );
	
	$this->logar(' ATUALIZANDO INFRA_PARAMETRO (ID_SERIE_RECIBO_MODULO_PETICIONAMENTO)');
	$nomeParamIdSerie = 'ID_SERIE_RECIBO_MODULO_PETICIONAMENTO';
	BancoSEI::getInstance()->executarSql('insert into infra_parametro ( valor, nome )  VALUES (\''. $serieDTO->getNumIdSerie() .'\' , \''. $nomeParamIdSerie .'\' ) ' );
	
	//============================== atualizando parametro para controlar versao do modulo
	$this->logar(' ATUALIZANDO INFRA_PARAMETRO (versao do sistema)');
	BancoSEI::getInstance()->executarSql('update infra_parametro SET valor = \''. $this->versaoAtualDesteModulo .'\' WHERE nome = \''. $this->nomeParametroModulo .'\' ' );
	 
  	} catch( Exception $e){  		
  		$this->logar( $e->getTraceAsString());
  		print_r($e); die();
  	}
  	
  }
  
  /* Contem atualizações da versao 0.0.2 do modulo */
  protected function instalarv002(){
  	  
  	$objInfraMetaBD = new InfraMetaBD(BancoSEI::getInstance());  	
  	$this->logar(' EXECUTANDO A INSTALACAO DA VERSAO 0.0.2 DO MODULO PETICIONAMENTO NA BASE DO SEI');
  	$this->logar(' CRIANDO A TABELA md_pet_usu_externo_menu E SUA sequence'); 	 
  	
  	BancoSEI::getInstance()->executarSql(' CREATE TABLE md_pet_usu_externo_menu( id_md_pet_usu_externo_menu ' . $objInfraMetaBD->tipoNumero() .' NOT NULL,
    id_conjunto_estilos ' . $objInfraMetaBD->tipoNumero() . ' NULL ,
  	nome ' . $objInfraMetaBD->tipoTextoVariavel(30) . ' NOT NULL ,
    tipo ' . $objInfraMetaBD->tipoTextoFixo(1) . ' NOT NULL , 
    url ' . $objInfraMetaBD->tipoTextoVariavel(2083) .' DEFAULT NULL , 
    conteudo_html ' . $objInfraMetaBD->tipoTextoGrande() .' DEFAULT NULL, 
    sin_ativo  ' . $objInfraMetaBD->tipoTextoFixo(1) . ' NOT NULL ) ');
  	
  	$objInfraMetaBD->adicionarChavePrimaria('md_pet_usu_externo_menu','pk_md_pet_usu_externo_menu',array('id_md_pet_usu_externo_menu'));
    
  	$objInfraMetaBD->adicionarChaveEstrangeira('fk_md_pet_menu_cj_est_01',
  			'md_pet_usu_externo_menu',
  			array('id_conjunto_estilos'), 
  			'conjunto_estilos',array('id_conjunto_estilos'));
  	
  	if (BancoSEI::getInstance() instanceof InfraMySql){
  		BancoSEI::getInstance()->executarSql('create table seq_md_pet_usu_externo_menu (id bigint not null primary key AUTO_INCREMENT, campo char(1) null) AUTO_INCREMENT = 1');
  	} else if (BancoSEI::getInstance() instanceof InfraSqlServer){
  		BancoSEI::getInstance()->executarSql('create table seq_md_pet_usu_externo_menu (id bigint identity(1,1), campo char(1) null)');
  	} else if (BancoSEI::getInstance() instanceof InfraOracle){
  		BancoSEI::getInstance()->criarSequencialNativa('seq_md_pet_usu_externo_menu', 1);
  	}
  	  	
  	//INSERCAO DE DOIS NOVOS MODELOS DE EMAIL em "EMAILS DE SISTEMA"
  	$this->logar(' INSERINDO EMAILS 3001 e 3002 em email_sistema');
  	
  	//Parametrizar Email de Alerta às Unidades
  	$conteudo1 = "      :: Este é um e-mail automático ::

O Usuário Externo @nome_usuario_externo@ (@email_usuario_externo@) efetivou o Peticionamento Eletrônico do tipo @tipo_peticionamento@ (@tipo_processo@), no âmbito do processo nº @processo@, conforme disposto no Recibo Eletrônico de Protocolo SEI nº @documento_recibo_eletronico_de_protocolo@.

O mencionado processo se encontra aberto em sua Unidade (@sigla_unidade_abertura_do_processo@). Entre no SEI e confira! Caso não seja de competência de sua Unidade, verifique se já está aberto na Unidade correta e, do contrário, envie-o para a Unidade competente para que seja devidamente tratado.


@sigla_orgao@
@descricao_orgao@
@sitio_internet_orgao@

ATENÇÃO: As informações contidas neste e-mail, incluindo seus anexos, podem ser restritas apenas à pessoa ou entidade para a qual foi endereçada. Se você não é o destinatário ou a pessoa responsável por encaminhar esta mensagem ao destinatário, você está, por meio desta, notificado que não deverá rever, retransmitir, imprimir, copiar, usar ou distribuir esta mensagem ou quaisquer anexos. Caso você tenha recebido esta mensagem por engano, por favor, contate o remetente imediatamente e em seguida apague esta mensagem.";

	$insert1 = "INSERT INTO email_sistema
  	(id_email_sistema,
  			descricao,
  			de,
  			para,
  			assunto,
  			conteudo,
  			sin_ativo
  	)
  	VALUES
  	(3002,
  			'Peticionamento Eletrônico - Alerta às Unidades',
  			'@sigla_sistema@ <@email_sistema@>',
  			'@emails_unidade@',
  			'SEI Peticionamento Eletrônico - Processo nº @processo@',
  			'" . $conteudo1 . "',
  			'S'
  	)";
  	
  	//Parametrizar Email de Confirmação ao Usuario Externo
  	$conteudo2 = "      :: Este é um e-mail automático ::

Prezado(a) @nome_usuario_externo@,

Este e-mail confirma a realização do Peticionamento Eletrônico do tipo @tipo_peticionamento@ no SEI-@sigla_orgao@, no âmbito do processo nº @processo@, conforme disposto no Recibo Eletrônico de Protocolo SEI nº @documento_recibo_eletronico_de_protocolo@.

Caso no futuro precise realizar novo peticionamento, sempre acesse a área destinada aos Usuários Externos no SEI-@sigla_orgao@ destacada em seu Portal na Internet ou acesse diretamente o link a seguir: @link_login_usuario_externo@


@sigla_orgao@
@descricao_orgao@
@sitio_internet_orgao@

ATENÇÃO: As informações contidas neste e-mail, incluindo seus anexos, podem ser restritas apenas à pessoa ou entidade para a qual foi endereçada. Se você não é o destinatário ou a pessoa responsável por encaminhar esta mensagem ao destinatário, você está, por meio desta, notificado que não deverá rever, retransmitir, imprimir, copiar, usar ou distribuir esta mensagem ou quaisquer anexos. Caso você tenha recebido esta mensagem por engano, por favor, contate o remetente imediatamente e em seguida apague esta mensagem.";
		
  	$insert2 = "INSERT INTO email_sistema
  	(id_email_sistema,
  			descricao,
  			de,
  			para,
  			assunto,
  			conteudo,
  			sin_ativo
  	)
  	VALUES
  	(3001,
  			'Peticionamento Eletrônico - Confirmação ao Usuário Externo',
  			'@sigla_sistema@ <@email_sistema@>',
  			'@email_usuario_externo@',
  			'SEI - Confirmação de Peticionamento Eletrônico (Processo nº @processo@)',
  			'" . $conteudo2 ."',
  			'S'
  	)";
	
  	BancoSEI::getInstance()->executarSql( $insert1 );
  	BancoSEI::getInstance()->executarSql( $insert2 );
  	
  	$this->logar(' CRIANDO A TABELA md_pet_usu_ext_processo E SUA sequence');
  	
  	//Inserindo tabelas referentes ao Recibo Eletronico de Protocolo
  	BancoSEI::getInstance()->executarSql(' CREATE TABLE md_pet_usu_ext_processo (
  	id_md_pet_usu_externo_processo ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL,
  	id_md_pet_tipo_processo ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL,
  	especificacao ' . $objInfraMetaBD->tipoTextoVariavel(50) . ' DEFAULT NULL,
  	tipo_interessado ' . $objInfraMetaBD->tipoTextoFixo(1) . ' DEFAULT NULL,
  	id_usuario_externo ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL,
  	data_hora_recebimento ' . $objInfraMetaBD->tipoDataHora() . ' NULL,
  	ip_usuario ' . $objInfraMetaBD->tipoTextoVariavel(60) . ' DEFAULT NULL,
  	numero_processo ' . $objInfraMetaBD->tipoTextoVariavel(40) . ' DEFAULT NULL,
	sin_ativo  ' . $objInfraMetaBD->tipoTextoFixo(1) . ' NOT NULL ) ');

  	$objInfraMetaBD->adicionarChavePrimaria('md_pet_usu_ext_processo','pk_md_pet_usu_externo_processo',array('id_md_pet_usu_externo_processo'));
  	
  	if (BancoSEI::getInstance() instanceof InfraMySql){
  		BancoSEI::getInstance()->executarSql('create table seq_md_pet_usu_ext_processo (id bigint not null primary key AUTO_INCREMENT, campo char(1) null) AUTO_INCREMENT = 1');
  	} else if (BancoSEI::getInstance() instanceof InfraSqlServer){
  		BancoSEI::getInstance()->executarSql('create table seq_md_pet_usu_ext_processo (id bigint identity(1,1), campo char(1) null)');
  	} else if (BancoSEI::getInstance() instanceof InfraOracle){
  		BancoSEI::getInstance()->criarSequencialNativa('seq_md_pet_usu_ext_processo', 1);
  	}
  	
  	//Tabelas relacionais com Tipos de Contatos permitidos para Cadastro e para Seleção
  	$this->logar(' CRIANDO A TABELA md_pet_rel_tp_ctx_contato');
  	
  	BancoSEI::getInstance()->executarSql(' CREATE TABLE md_pet_rel_tp_ctx_contato (
	  id_tipo_contexto_contato ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL,
	  sin_cadastro_interessado ' . $objInfraMetaBD->tipoTextoFixo(1) . ' NOT NULL,
	  sin_selecao_interessado ' . $objInfraMetaBD->tipoTextoFixo(1) .  ' NOT NULL,
	  id_md_pet_rel_tp_ctx_contato ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL
	 ) ');
  	 
  	$objInfraMetaBD->adicionarChavePrimaria('md_pet_rel_tp_ctx_contato','pk1_md_pet_rel_tp_ctx_cont',array('id_md_pet_rel_tp_ctx_contato'));
  	
  	$objInfraMetaBD->adicionarChaveEstrangeira('fk_md_pet_rel_tp_ctx_cont_1','md_pet_rel_tp_ctx_contato',
  			                                   array('id_tipo_contexto_contato'),
  			                                   'tipo_contexto_contato',array('id_tipo_contexto_contato'));
  	
  	if (BancoSEI::getInstance() instanceof InfraMySql){
  		BancoSEI::getInstance()->executarSql('create table seq_md_pet_rel_tp_ctx_contato (id bigint not null primary key AUTO_INCREMENT, campo char(1) null) AUTO_INCREMENT = 1');
  	} else if (BancoSEI::getInstance() instanceof InfraSqlServer){
  		BancoSEI::getInstance()->executarSql('create table seq_md_pet_rel_tp_ctx_contato (id bigint identity(1,1), campo char(1) null)');
  	} else if (BancoSEI::getInstance() instanceof InfraOracle){
  		BancoSEI::getInstance()->criarSequencialNativa('seq_md_pet_rel_tp_ctx_contato', 1);
  	}
  	
  	//Tabelas referentes ao Recibo Eletronico de Protocolo
  	$this->logar(' CRIANDO A TABELA md_pet_rel_recibo_protoc e SUA SEQUENCE');
  	
  	BancoSEI::getInstance()->executarSql('CREATE TABLE md_pet_rel_recibo_protoc (
	id_md_pet_rel_recibo_protoc ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL,
	id_protocolo ' . $objInfraMetaBD->tipoNumeroGrande() .' NOT NULL,
	id_usuario ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL,
	ip_usuario ' . $objInfraMetaBD->tipoTextoVariavel(500) . ' NOT NULL,
	data_hora_recebimento_final ' . $objInfraMetaBD->tipoDataHora() . ' DEFAULT NULL,
	sin_ativo ' . $objInfraMetaBD->tipoTextoFixo(1) . ' NOT NULL,
	sta_tipo_peticionamento ' . $objInfraMetaBD->tipoTextoVariavel(1) . ' DEFAULT NULL )');
  	
  	$objInfraMetaBD->adicionarChavePrimaria('md_pet_rel_recibo_protoc','pk1_md_pet_rel_recibo_protoc',array('id_md_pet_rel_recibo_protoc'));
  	
  	$objInfraMetaBD->adicionarChaveEstrangeira('fk1_md_pet_rel_recibo_protoc','md_pet_rel_recibo_protoc',array('id_protocolo'),'protocolo',array('id_protocolo'));
  	$objInfraMetaBD->adicionarChaveEstrangeira('fk2_md_pet_rel_recibo_protoc','md_pet_rel_recibo_protoc',array('id_usuario'),'usuario',array('id_usuario'));
  	   	
  	//seq_md_pet_rel_recibo_protoc
  	if (BancoSEI::getInstance() instanceof InfraMySql){
  		BancoSEI::getInstance()->executarSql('create table seq_md_pet_rel_recibo_protoc (id bigint not null primary key AUTO_INCREMENT, campo char(1) null) AUTO_INCREMENT = 1');
  	} else if (BancoSEI::getInstance() instanceof InfraSqlServer){
  		BancoSEI::getInstance()->executarSql('create table seq_md_pet_rel_recibo_protoc (id bigint identity(1,1), campo char(1) null)');
  	} else if (BancoSEI::getInstance() instanceof InfraOracle){
  		BancoSEI::getInstance()->criarSequencialNativa('seq_md_pet_rel_recibo_protoc', 1);
  	}
  	
  	//Tabelas de recibo X documentos
  	$this->logar(' CRIANDO A TABELA md_pet_rel_recibo_docanexo e SUA SEQUENCE');
  	
  	BancoSEI::getInstance()->executarSql(' CREATE TABLE md_pet_rel_recibo_docanexo (
	id_md_pet_rel_recibo_docanexo ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL,
	id_md_pet_rel_recibo_protoc ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL,
	formato_documento ' . $objInfraMetaBD->tipoTextoFixo(1) . ' NULL,
  	id_documento ' . $objInfraMetaBD->tipoNumeroGrande() . ' DEFAULT NULL,
	id_anexo ' . $objInfraMetaBD->tipoNumero() . ' DEFAULT NULL,
	classificacao_documento ' . $objInfraMetaBD->tipoTextoFixo(1) . ' NOT NULL )');
  	
  	$objInfraMetaBD->adicionarChavePrimaria('md_pet_rel_recibo_docanexo','pk1_md_pet_rel_recibo_docanexo',array('id_md_pet_rel_recibo_docanexo'));
  	   	
  	$objInfraMetaBD->adicionarChaveEstrangeira('fk_pet_rel_recibo_docanexo_01','md_pet_rel_recibo_docanexo',array('id_md_pet_rel_recibo_protoc'),'md_pet_rel_recibo_protoc',array('id_md_pet_rel_recibo_protoc'));
  	$objInfraMetaBD->adicionarChaveEstrangeira('fk_pet_rel_recibo_docanexo_02','md_pet_rel_recibo_docanexo',array('id_documento'),'documento',array('id_documento'));
  	$objInfraMetaBD->adicionarChaveEstrangeira('fk_pet_rel_recibo_docanexo_03','md_pet_rel_recibo_docanexo',array('id_anexo'),'anexo',array('id_anexo'));
  	   	
  	if (BancoSEI::getInstance() instanceof InfraMySql){
  		BancoSEI::getInstance()->executarSql('create table seq_md_pet_rel_recibo_docanexo (id bigint not null primary key AUTO_INCREMENT, campo char(1) null) AUTO_INCREMENT = 1');
  	} else if (BancoSEI::getInstance() instanceof InfraSqlServer){
  		BancoSEI::getInstance()->executarSql('create table seq_md_pet_rel_recibo_docanexo (id bigint identity(1,1), campo char(1) null)');
  	} else if (BancoSEI::getInstance() instanceof InfraOracle){
  		BancoSEI::getInstance()->criarSequencialNativa('seq_md_pet_rel_recibo_docanexo', 1);
  	}
  	
  	//============================== atualizando parametro para controlar versao do modulo
  	BancoSEI::getInstance()->executarSql('update infra_parametro SET valor = \''. $this->versaoAtualDesteModulo .'\' WHERE nome = \''. $this->nomeParametroModulo .'\' ' );
  	
  }
  
  /* Contem atualizações da versao 0.0.1 do modulo */
  protected function instalarv001(){
  	
  $objInfraMetaBD = new InfraMetaBD(BancoSEI::getInstance());
  	
  //$this->finalizar(' EXECUTANDO v001', true);
  $this->logar(' EXECUTANDO A INSTALACAO DA VERSAO 0.0.1 DO MODULO PETICIONAMENTO NA BASE DO SEI');  	
  $this->logar(' CRIANDO A TABELA md_pet_tipo_processo E SUA sequence');  	
  //1 - md_pet_tipo_processo
  
  BancoSEI::getInstance()->executarSql(' CREATE TABLE md_pet_tipo_processo( id_md_pet_tipo_processo ' . $objInfraMetaBD->tipoNumero() .' NOT NULL,
  id_tipo_procedimento ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL ,
  id_unidade ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL , 
  id_serie ' . $objInfraMetaBD->tipoNumero() . ' DEFAULT NULL , '
  
  . 'id_hipotese_legal ' . $objInfraMetaBD->tipoNumero() .' DEFAULT NULL ,
  orientacoes ' . $objInfraMetaBD->tipoTextoVariavel(500) .' NOT NULL,
  sta_nivel_acesso  ' . $objInfraMetaBD->tipoTextoFixo(1) . ' DEFAULT NULL,
  sin_ii_proprio_usuario_externo ' . $objInfraMetaBD->tipoTextoFixo(1) .' DEFAULT NULL,
  sin_ii_indicacao_direta ' . $objInfraMetaBD->tipoTextoFixo(1) .' DEFAULT NULL,
  sin_ii_indic_direta_cpf_cnpj ' . $objInfraMetaBD->tipoTextoFixo(1) .  ' DEFAULT NULL,
  sin_ii_indic_direta_contato ' . $objInfraMetaBD->tipoTextoFixo(1) . ' DEFAULT NULL,
  sin_na_usuario_externo ' . $objInfraMetaBD->tipoTextoFixo(1) . ' DEFAULT NULL,
  sin_na_padrao ' . $objInfraMetaBD->tipoTextoFixo(1) . ' DEFAULT NULL,
  sin_doc_gerado ' . $objInfraMetaBD->tipoTextoFixo(1) . ' DEFAULT NULL,
  sin_doc_externo ' . $objInfraMetaBD->tipoTextoFixo(1) . ' DEFAULT NULL,
  sin_ativo ' . $objInfraMetaBD->tipoTextoFixo(1) .' NOT NULL ) ');

  $objInfraMetaBD->adicionarChavePrimaria('md_pet_tipo_processo','pk_md_pet_tipo_processo',array('id_md_pet_tipo_processo'));
 
 $objInfraMetaBD->adicionarChaveEstrangeira('fk_pet_tp_proc_tipo_proc_01','md_pet_tipo_processo',array('id_tipo_procedimento'),'tipo_procedimento',array('id_tipo_procedimento'));
 $objInfraMetaBD->adicionarChaveEstrangeira('fk_pet_tp_proc_unidade_02','md_pet_tipo_processo',array('id_unidade'),'unidade',array('id_unidade'));
 $objInfraMetaBD->adicionarChaveEstrangeira('fk_pet_tp_proc_serie_03','md_pet_tipo_processo',array('id_serie'),'serie',array('id_serie'));

 $objInfraMetaBD->adicionarChaveEstrangeira('fk_pet_tp_proc_hip_legal_04','md_pet_tipo_processo',array('id_hipotese_legal'),'hipotese_legal',array('id_hipotese_legal'));

if (BancoSEI::getInstance() instanceof InfraMySql){
	BancoSEI::getInstance()->executarSql('create table seq_md_pet_tipo_processo (id bigint not null primary key AUTO_INCREMENT, campo char(1) null) AUTO_INCREMENT = 1');
} else if (BancoSEI::getInstance() instanceof InfraSqlServer){
	BancoSEI::getInstance()->executarSql('create table seq_md_pet_tipo_processo (id bigint identity(1,1), campo char(1) null)');
} else if (BancoSEI::getInstance() instanceof InfraOracle){
	BancoSEI::getInstance()->criarSequencialNativa('seq_md_pet_tipo_processo', 1);
}

//2- md_pet_rel_tp_processo_serie
$this->logar(' CRIANDO A TABELA md_pet_rel_tp_processo_serie');

BancoSEI::getInstance()->executarSql(' CREATE TABLE md_pet_rel_tp_processo_serie (
  id_md_pet_tipo_processo ' . $objInfraMetaBD->tipoNumero() .' NOT NULL,
  id_serie ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL)');

$objInfraMetaBD->adicionarChavePrimaria('md_pet_rel_tp_processo_serie','pk1_md_pet_rel_tp_processo_serie',array('id_md_pet_tipo_processo', 'id_serie'));

$objInfraMetaBD->adicionarChaveEstrangeira('fk_md_pet_rel_tp_processo_serie_01','md_pet_rel_tp_processo_serie',array('id_md_pet_tipo_processo'),'md_pet_tipo_processo',array('id_md_pet_tipo_processo'));
$objInfraMetaBD->adicionarChaveEstrangeira('fk_md_pet_rel_tp_processo_serie_02','md_pet_rel_tp_processo_serie',array('id_serie'),'serie',array('id_serie'));

//3 - md_pet_tp_processo_orientacoes
$this->logar(' CRIANDO A TABELA md_pet_tp_processo_orientacoes');

BancoSEI::getInstance()->executarSql('CREATE TABLE md_pet_tp_processo_orientacoes (
  id_md_pet_tp_proc_orientacoes ' . $objInfraMetaBD->tipoNumero() .' NOT NULL,
  id_conjunto_estilos ' . $objInfraMetaBD->tipoNumero() .' NULL,
  orientacoes_gerais ' . $objInfraMetaBD->tipoTextoGrande() .' NOT NULL )');

$objInfraMetaBD->adicionarChavePrimaria('md_pet_tp_processo_orientacoes','pk_md_pet_tp_proc_orient',array('id_md_pet_tp_proc_orientacoes'));

$objInfraMetaBD->adicionarChaveEstrangeira('fk_md_pet_tp_proc_or_cj_est','md_pet_tp_processo_orientacoes',array('id_conjunto_estilos'),'conjunto_estilos',array('id_conjunto_estilos'));

//4 - md_pet_extensao_arquivo_perm

$this->logar(' CRIANDO A TABELA md_pet_ext_arquivo_perm e sua sequence ');

BancoSEI::getInstance()->executarSql(' CREATE TABLE IF NOT EXISTS md_pet_ext_arquivo_perm (
  id_md_pet_ext_arquivo_perm ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL,
  id_arquivo_extensao ' . $objInfraMetaBD->tipoNumero() . ' DEFAULT NULL ,
  sin_principal ' . $objInfraMetaBD->tipoTextoFixo(1) .' NOT NULL,  
  sin_ativo ' . $objInfraMetaBD->tipoTextoFixo(1) . ' NOT NULL )');

$objInfraMetaBD->adicionarChavePrimaria('md_pet_ext_arquivo_perm','pk_md_pet_extensao_arquivo_perm',array('id_md_pet_ext_arquivo_perm'));
$objInfraMetaBD->adicionarChaveEstrangeira('fk_md_pet_ext_arquivo_perm_01','md_pet_ext_arquivo_perm',array('id_arquivo_extensao'),'arquivo_extensao',array('id_arquivo_extensao'));

if (BancoSEI::getInstance() instanceof InfraMySql){
	BancoSEI::getInstance()->executarSql('create table seq_md_pet_ext_arquivo_perm (id bigint not null primary key AUTO_INCREMENT, campo char(1) null) AUTO_INCREMENT = 1');
} else if (BancoSEI::getInstance() instanceof InfraSqlServer){
	BancoSEI::getInstance()->executarSql('create table seq_md_pet_ext_arquivo_perm (id bigint identity(1,1), campo char(1) null)');
} else if (BancoSEI::getInstance() instanceof InfraOracle){
	BancoSEI::getInstance()->criarSequencialNativa('seq_md_pet_ext_arquivo_perm', 1);
}

//6 - md_pet_tamanho_arquivo
$this->logar(' CRIANDO A TABELA md_pet_tamanho_arquivo ');

BancoSEI::getInstance()->executarSql(' CREATE TABLE md_pet_tamanho_arquivo (
  id_md_pet_tamanho_arquivo ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL,
  valor_doc_principal ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL,
  valor_doc_complementar ' . $objInfraMetaBD->tipoNumero() . '  DEFAULT NULL,
  sin_ativo ' . $objInfraMetaBD->tipoTextoFixo(1) . ' NOT NULL ) ');

$objInfraMetaBD->adicionarChavePrimaria('md_pet_tamanho_arquivo','pk_md_pet_tamanho_arquivo',array('id_md_pet_tamanho_arquivo'));

$objTamanhoArquivoDTO = new TamanhoArquivoPermitidoPeticionamentoDTO();
$objTamanhoArquivoRN  = new TamanhoArquivoPermitidoPeticionamentoRN();

$objTamanhoArquivoDTO->retTodos();
$objTamanhoArquivoDTO->setNumValorDocPrincipal('5');
$objTamanhoArquivoDTO->setNumValorDocComplementar('10');
$objTamanhoArquivoDTO->setNumIdTamanhoArquivo(TamanhoArquivoPermitidoPeticionamentoRN::$ID_FIXO_TAMANHO_ARQUIVO);
$objTamanhoArquivoDTO->setStrSinAtivo('S');

$objTamanhoArquivoRN->cadastrar($objTamanhoArquivoDTO);

//7 - md_pet_indisponibilidade e sequence
$this->logar(' CRIANDO A TABELA md_pet_indisponibilidade e sequence ');

BancoSEI::getInstance()->executarSql(' CREATE TABLE md_pet_indisponibilidade (
  id_md_pet_indisponibilidade ' . $objInfraMetaBD->tipoNumero() .' NOT NULL,
  dth_inicio ' . $objInfraMetaBD->tipoDataHora() . ' NOT NULL,
  dth_fim ' . $objInfraMetaBD->tipoDataHora() .' NOT NULL,
  resumo_indisponibilidade ' . $objInfraMetaBD->tipoTextoVariavel(500) . ' DEFAULT NULL,
  sin_prorrogacao ' . $objInfraMetaBD->tipoTextoFixo(1) . ' NOT NULL,
  sin_ativo '. $objInfraMetaBD->tipoTextoFixo(1) .' NOT NULL ) ');

$objInfraMetaBD->adicionarChavePrimaria('md_pet_indisponibilidade','pk_md_pet_indisponibilidade',array('id_md_pet_indisponibilidade'));

if (BancoSEI::getInstance() instanceof InfraMySql){
	BancoSEI::getInstance()->executarSql('create table seq_md_pet_indisponibilidade (id bigint not null primary key AUTO_INCREMENT, campo char(1) null) AUTO_INCREMENT = 1');
} else if (BancoSEI::getInstance() instanceof InfraSqlServer){
	BancoSEI::getInstance()->executarSql('create table seq_md_pet_indisponibilidade (id bigint identity(1,1), campo char(1) null)');
} else if (BancoSEI::getInstance() instanceof InfraOracle){
	BancoSEI::getInstance()->criarSequencialNativa('seq_md_pet_indisponibilidade', 1);
}

//8 - md_pet_indisponibilidade_anexo e sequence
$this->logar(' CRIANDO A TABELA md_pet_indisp_anexo e sequence ');

BancoSEI::getInstance()->executarSql(' CREATE TABLE md_pet_indisp_anexo (
  id_md_pet_anexo ' .  $objInfraMetaBD->tipoNumero() .' NOT NULL,
  id_md_pet_indisponibilidade ' .  $objInfraMetaBD->tipoNumero() .' NOT NULL,
  id_unidade ' .  $objInfraMetaBD->tipoNumero() .' NOT NULL,
  id_usuario ' .  $objInfraMetaBD->tipoNumero() .' NOT NULL,
  dth_inclusao ' . $objInfraMetaBD->tipoDataHora() . ' NOT NULL,
  nome ' . $objInfraMetaBD->tipoTextoVariavel(255) . ' NOT NULL,
  tamanho  ' .  $objInfraMetaBD->tipoNumero() .' NOT NULL,
  sin_ativo ' . $objInfraMetaBD->tipoTextoFixo(1) . ' NOT NULL,  
  hash ' . $objInfraMetaBD->tipoTextoFixo(32) . ' NOT NULL ) ');

$objInfraMetaBD->adicionarChavePrimaria('md_pet_indisp_anexo','pk_pet_indisponibilidade_anexo',array('id_md_pet_anexo'));

$objInfraMetaBD->adicionarChaveEstrangeira('fk_md_pet_indisp_anexo_01','md_pet_indisp_anexo',array('id_md_pet_indisponibilidade'),'md_pet_indisponibilidade',array('id_md_pet_indisponibilidade'));
$objInfraMetaBD->adicionarChaveEstrangeira('fk_md_pet_indisp_anexo_02','md_pet_indisp_anexo',array('id_unidade'),'unidade',array('id_unidade'));
$objInfraMetaBD->adicionarChaveEstrangeira('fk_md_pet_indisp_anexo_03','md_pet_indisp_anexo',array('id_usuario'),'usuario',array('id_usuario'));

if (BancoSEI::getInstance() instanceof InfraMySql){
	BancoSEI::getInstance()->executarSql('create table seq_md_pet_indisp_anexo (id bigint not null primary key AUTO_INCREMENT, campo char(1) null) AUTO_INCREMENT = 1');
} else if (BancoSEI::getInstance() instanceof InfraSqlServer){
	BancoSEI::getInstance()->executarSql('create table seq_md_pet_indisp_anexo (id bigint identity(1,1), campo char(1) null)');
} else if (BancoSEI::getInstance() instanceof InfraOracle){
	BancoSEI::getInstance()->criarSequencialNativa('seq_md_pet_indisp_anexo', 1);
}
  	  	 
//adicionando parametro para controlar versao do modulo
BancoSEI::getInstance()->executarSql('insert into infra_parametro (valor, nome ) VALUES( \''. $this->versaoAtualDesteModulo .'\',  \''. $this->nomeParametroModulo .'\' )' );
  	 
//$this->logar(' EXECUTADA A INSTALACAO DA VERSAO 0.0.1 DO MODULO PETICIONAMENTO NO SEI COM SUCESSO');
  	
}
  
protected function atualizarVersaoConectado(){
	
	try{
	
		$this->inicializar('INICIANDO ATUALIZACAO DO MODULO PETICIONAMENTO NO SEI VERSAO '.SEI_VERSAO);
		 
		//testando versao do framework
		$numVersaoInfraRequerida = '1.208';
		if (VERSAO_INFRA != $numVersaoInfraRequerida){
			$this->finalizar('VERSAO DO FRAMEWORK PHP INCOMPATIVEL (VERSAO ATUAL '.VERSAO_INFRA.', VERSAO REQUERIDA '.$numVersaoInfraRequerida.')',true);
		}
	
		//testando se esta usando BDs suportados
		if (!(BancoSEI::getInstance() instanceof InfraMySql) &&
				!(BancoSEI::getInstance() instanceof InfraSqlServer) &&
				!(BancoSEI::getInstance() instanceof InfraOracle)){
	
					$this->finalizar('BANCO DE DADOS NAO SUPORTADO: '.get_parent_class(BancoSEI::getInstance()),true);
	
		}
		 
		//testando permissoes de criaï¿½oes de tabelas
		$objInfraMetaBD = new InfraMetaBD(BancoSEI::getInstance());
	
		if (count($objInfraMetaBD->obterTabelas('sei_teste'))==0){
			BancoSEI::getInstance()->executarSql('CREATE TABLE sei_teste (id '.$objInfraMetaBD->tipoNumero().' null)');
		}
	
		BancoSEI::getInstance()->executarSql('DROP TABLE sei_teste');
	
		$objInfraParametro = new InfraParametro(BancoSEI::getInstance());
	
		//$strVersaoAtual = $objInfraParametro->getValor('SEI_VERSAO', false);
		$strVersaoModuloLitigioso = $objInfraParametro->getValor($this->nomeParametroModulo, false);
		 
		//VERIFICANDO QUAL VERSAO DEVE SER INSTALADA NESTA EXECUCAO
		if (InfraString::isBolVazia($strVersaoModuloLitigioso)){
			 
			//nao tem nenhuma versao ainda, instalar todas
			$this->instalarv001();
			$this->instalarv002();
			$this->instalarv100();
			$this->logar('ATUALIZAÇÔES DA VERSÃO ' . $this->versaoAtualDesteModulo .  ' DO MÓDULO PETICIONAMENTO INSTALADAS COM SUCESSO NA BASE DO SEI');
			$this->finalizar('FIM', false);
		}
		
		//se ja tem 001 instala apenas 002 e 100
		else if( $strVersaoModuloLitigioso == '0.0.1' ){
			
			$this->instalarv002();
			$this->instalarv100();
			$this->logar('ATUALIZAÇÔES DA VESRÃO ' . $this->versaoAtualDesteModulo .  ' DO MÓDULO PETICIONAMENTO INSTALADAS COM SUCESSO NA BASE DO SEI');
			$this->finalizar('FIM', false);
		}
		
		//se ja tem 002 instala apenas 100
		else if( $strVersaoModuloLitigioso == '0.0.2' ){
			
			$this->instalarv100();
			$this->logar('ATUALIZAÇÔES DA VERSÃO ' . $this->versaoAtualDesteModulo .  ' DO MÓDULO PETICIONAMENTO INSTALADAS COM SUCESSO NA BASE DO SEI');
			$this->finalizar('FIM', false);
		}
		
		else if( $strVersaoModuloLitigioso == '1.0.0' ){
			$this->logar(' A VERSAO MAIS ATUAL DO MODULO ' . $this->nomeDesteModulo .' (v ' . $this->versaoAtualDesteModulo  . ') JA ESTA INSTALADA. ');
			$this->finalizar('FIM', true);
		}
	
		InfraDebug::getInstance()->setBolLigado(false);
		InfraDebug::getInstance()->setBolDebugInfra(false);
		InfraDebug::getInstance()->setBolEcho(false);
	
	} catch(Exception $e){
	
		InfraDebug::getInstance()->setBolLigado(false);
		InfraDebug::getInstance()->setBolDebugInfra(false);
		InfraDebug::getInstance()->setBolEcho(false);
		$this->logar( $e->getTraceAsString() );
		$this->finalizar('FIM', true);
		throw new InfraException('Erro atualizando VERSAO.', $e);
	}
	
}

}
?>