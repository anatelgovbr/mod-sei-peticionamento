<?
/**
 * ANATEL
 *
 * 21/05/2016 - criado por marcelo.bezerra - CAST
 *
 */
require_once dirname(__FILE__).'/Sip.php';

class AtualizadorSipModuloPeticionamentoRN extends InfraRN {

	private $numSeg = 0;
	private $versaoAtualDesteModulo = '1.0.3';
	private $nomeParametroModulo = 'VERSAO_MODULO_PETICIONAMENTO';

	public function __construct(){
		parent::__construct();
		$this->inicializar(' SIP - INICIALIZAR ');
	}

	protected function inicializarObjInfraIBanco(){
		return BancoSip::getInstance();
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

	/* Contem atualizaçoes da versao 1.0.0 do modulo */
	protected function instalarv100(){
		
		//criar novo grupo de auditoria
		$objSistemaRN = new SistemaRN();
		$objPerfilRN = new PerfilRN();
		$objMenuRN = new MenuRN();
		$objItemMenuRN = new ItemMenuRN();
		$objRecursoRN = new RecursoRN();
			
		$objSistemaDTO = new SistemaDTO();
		$objSistemaDTO->retNumIdSistema();
		$objSistemaDTO->setStrSigla('SEI');
			
		$objSistemaDTO = $objSistemaRN->consultar($objSistemaDTO);
			
		if ($objSistemaDTO == null){
			throw new InfraException('Sistema SEI não encontrado.');
		}
			
		$numIdSistemaSei = $objSistemaDTO->getNumIdSistema();
		
		$objPerfilDTO = new PerfilDTO();
		$objPerfilDTO->retNumIdPerfil();
		$objPerfilDTO->setNumIdSistema($numIdSistemaSei);
		$objPerfilDTO->setStrNome('Administrador');
		$objPerfilDTO = $objPerfilRN->consultar($objPerfilDTO);
			
		if ($objPerfilDTO == null){
			throw new InfraException('Perfil Administrador do sistema SEI não encontrado.');
		}
			
		$numIdPerfilSeiAdministrador = $objPerfilDTO->getNumIdPerfil();
			
		$objPerfilDTO = new PerfilDTO();
		$objPerfilDTO->retNumIdPerfil();
		$objPerfilDTO->setNumIdSistema($numIdSistemaSei);
		$objPerfilDTO->setStrNome('Informática');
		$objPerfilDTO = $objPerfilRN->consultar($objPerfilDTO);
			
		if ($objPerfilDTO == null){
			throw new InfraException('Perfil Informática do sistema SEI não encontrado.');
		}
			
		$numIdPerfilSeiInformatica = $objPerfilDTO->getNumIdPerfil();
			
		$objMenuDTO = new MenuDTO();
		$objMenuDTO->retNumIdMenu();
		$objMenuDTO->setNumIdSistema($numIdSistemaSei);
		$objMenuDTO->setStrNome('Principal');
		$objMenuDTO = $objMenuRN->consultar($objMenuDTO);
			
		if ($objMenuDTO == null){
			throw new InfraException('Menu do sistema SEI não encontrado.');
		}
			
		$numIdMenuSei = $objMenuDTO->getNumIdMenu();
			
		$objItemMenuDTO = new ItemMenuDTO();
		$objItemMenuDTO->retNumIdItemMenu();
		$objItemMenuDTO->setNumIdSistema($numIdSistemaSei);
		$objItemMenuDTO->setStrRotulo('Administração');
		$objItemMenuDTO = $objItemMenuRN->consultar($objItemMenuDTO);
			
		if ($objItemMenuDTO == null){
			throw new InfraException('Item de menu Administração do sistema SEI não encontrado.');
		}
			
		$numIdItemMenuSeiAdministracao = $objItemMenuDTO->getNumIdItemMenu();
			
		$objItemMenuDTO = new ItemMenuDTO();
		$objItemMenuDTO->retNumIdItemMenu();
		$objItemMenuDTO->setNumIdSistema($numIdSistemaSei);
		$objItemMenuDTO->setStrRotulo('Usuários');
		$objItemMenuDTO = $objItemMenuRN->consultar($objItemMenuDTO);
			
		if ($objItemMenuDTO == null){
			throw new InfraException('Item de menu Administração/Usuários do sistema SEI não encontrado.');
		}
			
		$numIdItemMenuSeiUsuarios = $objItemMenuDTO->getNumIdItemMenu();
			
		//SEI ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$this->logar('ATUALIZANDO RECURSOS, MENUS E PERFIS DO MÓDULO PETICIONAMENTO NA BASE DO SIP...');
		
		//criando os recursos e vinculando-os aos perfil Administrador
		//recursos que serao chamados via menus vem primeiro
		
		//Cadastro de Menus
		//Hipoteses Legais Permitidas
		$objMenuListarDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'hipotese_legal_nl_acesso_peticionamento_cadastrar');
		
		$objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'hipotese_legal_peticionamento_selecionar');
		
		//recupera o ID do menu Peticionamento Eletronico
		$objItemMenuDTOPeticionamentoEletronico = new ItemMenuDTO();
		$objItemMenuDTOPeticionamentoEletronico->retNumIdItemMenu();
		$objItemMenuDTOPeticionamentoEletronico->setNumIdSistema($numIdSistemaSei);
		$objItemMenuDTOPeticionamentoEletronico->setStrRotulo('Peticionamento Eletrônico');
		$objItemMenuDTOPeticionamentoEletronico = $objItemMenuRN->consultar( $objItemMenuDTOPeticionamentoEletronico );

		//menu_peticionamento_usuario_externo_listar
		$this->adicionarItemMenu($numIdSistemaSei,
				$numIdPerfilSeiAdministrador,
				$numIdMenuSei,
				$objItemMenuDTOPeticionamentoEletronico->getNumIdItemMenu(),
				$objMenuListarDTO->getNumIdRecurso(),
				'Hipótese Legais Permitidas',
				60);
			
		$objRegraAuditoriaDTO = new RegraAuditoriaDTO();
		$objRegraAuditoriaDTO->retNumIdRegraAuditoria();
		$objRegraAuditoriaDTO->setNumIdSistema($numIdSistemaSei);
		$objRegraAuditoriaDTO->setStrDescricao('Modulo_Peticionamento_Eletronico');
			
		$objRegraAuditoriaRN = new RegraAuditoriaRN();
		$objRegraAuditoriaDTO = $objRegraAuditoriaRN->consultar($objRegraAuditoriaDTO);
			
		$rs = BancoSip::getInstance()->consultarSql('select id_recurso from recurso where id_sistema='.$numIdSistemaSei.' and nome in (
		  \'hipotese_legal_nl_acesso_peticionamento_cadastrar\')'
				);
			
		//CRIANDO REGRA DE AUDITORIA PARA NOVOS RECURSOS RECEM ADICIONADOS
		foreach($rs as $recurso){
			BancoSip::getInstance()->executarSql('insert into rel_regra_auditoria_recurso (id_regra_auditoria, id_sistema, id_recurso) values ('.$objRegraAuditoriaDTO->getNumIdRegraAuditoria().', '.$numIdSistemaSei.', '.$recurso['id_recurso'].')');
		}
			
		$objReplicacaoRegraAuditoriaDTO = new ReplicacaoRegraAuditoriaDTO();
		$objReplicacaoRegraAuditoriaDTO->setStrStaOperacao('A');
		$objReplicacaoRegraAuditoriaDTO->setNumIdRegraAuditoria($objRegraAuditoriaDTO->getNumIdRegraAuditoria());
			
		$objSistemaRN = new SistemaRN();
		$objSistemaRN->replicarRegraAuditoria($objReplicacaoRegraAuditoriaDTO);
	}
	
	/* Contem atualizaçoes da versao 0.0.2 do modulo */
	protected function instalarv002(){
		
		$objSistemaRN = new SistemaRN();
		$objPerfilRN = new PerfilRN();
		$objMenuRN = new MenuRN();
		$objItemMenuRN = new ItemMenuRN();
		$objRecursoRN = new RecursoRN();
			
		$objSistemaDTO = new SistemaDTO();
		$objSistemaDTO->retNumIdSistema();
		$objSistemaDTO->setStrSigla('SEI');
			
		$objSistemaDTO = $objSistemaRN->consultar($objSistemaDTO);
			
		if ($objSistemaDTO == null){
			throw new InfraException('Sistema SEI não encontrado.');
		}
			
		$numIdSistemaSei = $objSistemaDTO->getNumIdSistema();
		
		$objPerfilDTO = new PerfilDTO();
		$objPerfilDTO->retNumIdPerfil();
		$objPerfilDTO->setNumIdSistema($numIdSistemaSei);
		$objPerfilDTO->setStrNome('Administrador');
		$objPerfilDTO = $objPerfilRN->consultar($objPerfilDTO);
			
		if ($objPerfilDTO == null){
			throw new InfraException('Perfil Administrador do sistema SEI não encontrado.');
		}
			
		$numIdPerfilSeiAdministrador = $objPerfilDTO->getNumIdPerfil();
			
		$objPerfilDTO = new PerfilDTO();
		$objPerfilDTO->retNumIdPerfil();
		$objPerfilDTO->setNumIdSistema($numIdSistemaSei);
		$objPerfilDTO->setStrNome('Informática');
		$objPerfilDTO = $objPerfilRN->consultar($objPerfilDTO);
			
		if ($objPerfilDTO == null){
			throw new InfraException('Perfil Informática do sistema SEI não encontrado.');
		}
			
		$numIdPerfilSeiInformatica = $objPerfilDTO->getNumIdPerfil();
			
		$objMenuDTO = new MenuDTO();
		$objMenuDTO->retNumIdMenu();
		$objMenuDTO->setNumIdSistema($numIdSistemaSei);
		$objMenuDTO->setStrNome('Principal');
		$objMenuDTO = $objMenuRN->consultar($objMenuDTO);
			
		if ($objMenuDTO == null){
			throw new InfraException('Menu do sistema SEI não encontrado.');
		}
			
		$numIdMenuSei = $objMenuDTO->getNumIdMenu();
			
		$objItemMenuDTO = new ItemMenuDTO();
		$objItemMenuDTO->retNumIdItemMenu();
		$objItemMenuDTO->setNumIdSistema($numIdSistemaSei);
		$objItemMenuDTO->setStrRotulo('Administração');
		$objItemMenuDTO = $objItemMenuRN->consultar($objItemMenuDTO);
			
		if ($objItemMenuDTO == null){
			throw new InfraException('Item de menu Administração do sistema SEI não encontrado.');
		}
			
		$numIdItemMenuSeiAdministracao = $objItemMenuDTO->getNumIdItemMenu();
			
		$objItemMenuDTO = new ItemMenuDTO();
		$objItemMenuDTO->retNumIdItemMenu();
		$objItemMenuDTO->setNumIdSistema($numIdSistemaSei);
		$objItemMenuDTO->setStrRotulo('Usuários');
		$objItemMenuDTO = $objItemMenuRN->consultar($objItemMenuDTO);
			
		if ($objItemMenuDTO == null){
			throw new InfraException('Item de menu Administração/Usuários do sistema SEI não encontrado.');
		}
			
		$numIdItemMenuSeiUsuarios = $objItemMenuDTO->getNumIdItemMenu();
			
		//SEI ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$this->logar('ATUALIZANDO RECURSOS, MENUS E PERFIS DO MÓDULO PETICIONAMENTO NA BASE DO SIP...');
		
		//criando os recursos e vinculando-os aos perfil Administrador
		//recursos que serao chamados via menus vem primeiro
		
		//Cadastro de Menus
		$objMenuListarDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'menu_peticionamento_usuario_externo_listar');
		
		//Tipos de Contatos Permitidos
		$objMenuTipoInteressadoPermitidoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'gerir_tipo_contexto_peticionamento_cadastrar');
				
		$objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'menu_peticionamento_usuario_externo_desativar');
		$objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'menu_peticionamento_usuario_externo_reativar');
		$objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'menu_peticionamento_usuario_externo_excluir');
		$objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'menu_peticionamento_usuario_externo_cadastrar');
		$objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'menu_peticionamento_usuario_externo_consultar');
		$objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'menu_peticionamento_usuario_externo_alterar');
				
		//recupera o ID do menu Peticionamento Eletronico
		$objItemMenuDTOPeticionamentoEletronico = new ItemMenuDTO();
		$objItemMenuDTOPeticionamentoEletronico->retNumIdItemMenu();
		$objItemMenuDTOPeticionamentoEletronico->setNumIdSistema($numIdSistemaSei);
		$objItemMenuDTOPeticionamentoEletronico->setStrRotulo('Peticionamento Eletrônico');
		$objItemMenuDTOPeticionamentoEletronico = $objItemMenuRN->consultar( $objItemMenuDTOPeticionamentoEletronico );
		
		//recursos que nao sao chamados em menus
		//menu_peticionamento_usuario_externo_listar
		$this->adicionarItemMenu($numIdSistemaSei,
				$numIdPerfilSeiAdministrador,
				$numIdMenuSei,
				$objItemMenuDTOPeticionamentoEletronico->getNumIdItemMenu() ,
				$objMenuListarDTO->getNumIdRecurso(),
				'Cadastro de Menus',
				20);
		
		//menu_peticionamento_usuario_externo_listar
		$this->adicionarItemMenu($numIdSistemaSei,
				$numIdPerfilSeiAdministrador,
				$numIdMenuSei,
				$objItemMenuDTOPeticionamentoEletronico->getNumIdItemMenu(),
				$objMenuTipoInteressadoPermitidoDTO->getNumIdRecurso(),
				'Tipos de Contatos Permitidos',
				50);
			
		$objRegraAuditoriaDTO = new RegraAuditoriaDTO();
		$objRegraAuditoriaDTO->retNumIdRegraAuditoria();
		$objRegraAuditoriaDTO->setNumIdSistema($numIdSistemaSei);
		$objRegraAuditoriaDTO->setStrDescricao('Modulo_Peticionamento_Eletronico');
			
		$objRegraAuditoriaRN = new RegraAuditoriaRN();
		$objRegraAuditoriaDTO = $objRegraAuditoriaRN->consultar($objRegraAuditoriaDTO);
			
		$rs = BancoSip::getInstance()->consultarSql('select id_recurso from recurso where id_sistema='.$numIdSistemaSei.' and nome in (
          \'gerir_tipo_contexto_peticionamento_cadastrar\',
		  \'menu_peticionamento_usuario_externo_desativar\',
		  \'menu_peticionamento_usuario_externo_reativar\',
		  \'menu_peticionamento_usuario_externo_excluir\',
		  \'menu_peticionamento_usuario_externo_cadastrar\',
		  \'menu_peticionamento_usuario_externo_alterar\')'
		);
			
		//CRIANDO REGRA DE AUDITORIA PARA NOVOS RECURSOS RECEM ADICIONADOS
		foreach($rs as $recurso){
			BancoSip::getInstance()->executarSql('insert into rel_regra_auditoria_recurso (id_regra_auditoria, id_sistema, id_recurso) values ('.$objRegraAuditoriaDTO->getNumIdRegraAuditoria().', '.$numIdSistemaSei.', '.$recurso['id_recurso'].')');
		}
			
		$objReplicacaoRegraAuditoriaDTO = new ReplicacaoRegraAuditoriaDTO();
		$objReplicacaoRegraAuditoriaDTO->setStrStaOperacao('A');
		$objReplicacaoRegraAuditoriaDTO->setNumIdRegraAuditoria($objRegraAuditoriaDTO->getNumIdRegraAuditoria());
			
		$objSistemaRN = new SistemaRN();
		$objSistemaRN->replicarRegraAuditoria($objReplicacaoRegraAuditoriaDTO);
		
	}
	
	/* Contem atualizaçoes da versao 0.0.1 do modulo */
	protected function instalarv001(){
		 
		$objSistemaRN = new SistemaRN();
		$objPerfilRN = new PerfilRN();
		$objMenuRN = new MenuRN();
		$objItemMenuRN = new ItemMenuRN();
		$objRecursoRN = new RecursoRN();
		 
		$objSistemaDTO = new SistemaDTO();
		$objSistemaDTO->retNumIdSistema();
		$objSistemaDTO->setStrSigla('SEI');
		 
		$objSistemaDTO = $objSistemaRN->consultar($objSistemaDTO);
		 
		if ($objSistemaDTO == null){
			throw new InfraException('Sistema SEI não encontrado.');
		}
		 
		$numIdSistemaSei = $objSistemaDTO->getNumIdSistema();
		
		$objPerfilDTO = new PerfilDTO();
		$objPerfilDTO->retNumIdPerfil();
		$objPerfilDTO->setNumIdSistema($numIdSistemaSei);
		$objPerfilDTO->setStrNome('Administrador');
		$objPerfilDTO = $objPerfilRN->consultar($objPerfilDTO);
		 
		if ($objPerfilDTO == null){
			throw new InfraException('Perfil Administrador do sistema SEI não encontrado.');
		}
		 
		$numIdPerfilSeiAdministrador = $objPerfilDTO->getNumIdPerfil();
		 
		$objPerfilDTO = new PerfilDTO();
		$objPerfilDTO->retNumIdPerfil();
		$objPerfilDTO->setNumIdSistema($numIdSistemaSei);
		$objPerfilDTO->setStrNome('Informática');
		$objPerfilDTO = $objPerfilRN->consultar($objPerfilDTO);
		 
		if ($objPerfilDTO == null){
			throw new InfraException('Perfil Informática do sistema SEI não encontrado.');
		}
		 
		$numIdPerfilSeiInformatica = $objPerfilDTO->getNumIdPerfil();
		 
		$objMenuDTO = new MenuDTO();
		$objMenuDTO->retNumIdMenu();
		$objMenuDTO->setNumIdSistema($numIdSistemaSei);
		$objMenuDTO->setStrNome('Principal');
		$objMenuDTO = $objMenuRN->consultar($objMenuDTO);
		 
		if ($objMenuDTO == null){
			throw new InfraException('Menu do sistema SEI não encontrado.');
		}
		 
		$numIdMenuSei = $objMenuDTO->getNumIdMenu();
		 
		$objItemMenuDTO = new ItemMenuDTO();
		$objItemMenuDTO->retNumIdItemMenu();
		$objItemMenuDTO->setNumIdSistema($numIdSistemaSei);
		$objItemMenuDTO->setStrRotulo('Administração');
		$objItemMenuDTO = $objItemMenuRN->consultar($objItemMenuDTO);
		 
		if ($objItemMenuDTO == null){
			throw new InfraException('Item de menu Administração do sistema SEI não encontrado.');
		}
		 
		$numIdItemMenuSeiAdministracao = $objItemMenuDTO->getNumIdItemMenu();
		 
		$objItemMenuDTO = new ItemMenuDTO();
		$objItemMenuDTO->retNumIdItemMenu();
		$objItemMenuDTO->setNumIdSistema($numIdSistemaSei);
		$objItemMenuDTO->setStrRotulo('Usuários');
		$objItemMenuDTO = $objItemMenuRN->consultar($objItemMenuDTO);
		 
		if ($objItemMenuDTO == null){
			throw new InfraException('Item de menu Administração/Usuários do sistema SEI não encontrado.');
		}
		 
		$numIdItemMenuSeiUsuarios = $objItemMenuDTO->getNumIdItemMenu();
		 
		//SEI ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$this->logar('ATUALIZANDO RECURSOS, MENUS E PERFIS DO MÓDULO PETICIONAMENTO NA BASE DO SIP...');
		
		//criando os recursos e vinculando-os aos perfil Administrador
		//recursos que serao chamados via menus vem primeiro
		$objExtensoesArquivosDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'gerir_extensoes_arquivo_peticionamento_cadastrar');
		$objTamanhoArquivoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'gerir_tamanho_arquivo_peticionamento_cadastrar');
		$objIndisponibilidadeListarDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'indisponibilidade_peticionamento_listar');
		$objTipoProcessoListarDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador,'tipo_processo_peticionamento_listar');
		
		//recursos que nao sao chamados em menus
		//gerir tamanho de arquivo
		$objRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'arquivo_extensao_peticionamento_selecionar');
		$objRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'gerir_tamanho_arquivo_peticionamento_listar');
		$objRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'gerir_tamanho_arquivo_peticionamento_consultar');
		
		//indisponibilidade
		$objRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'indisponibilidade_peticionamento_desativar');
		$objRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'indisponibilidade_peticionamento_reativar');
		$objRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'indisponibilidade_peticionamento_excluir');
		$objRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'indisponibilidade_peticionamento_cadastrar');
		$objRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'indisponibilidade_peticionamento_consultar');
		$objRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'indisponibilidade_peticionamento_alterar');
		$objRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'indisponibilidade_peticionamento_upload_anexo');
		
		//tipo processo
		$objRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'tipo_processo_peticionamento_desativar');
		$objRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'tipo_processo_peticionamento_reativar');
		$objRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'tipo_processo_peticionamento_excluir');
		$objRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'tipo_processo_peticionamento_cadastrar');
		$objRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'tipo_processo_peticionamento_alterar');
		$objRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'tipo_processo_peticionamento_consultar');
		$objRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'tipo_processo_peticionamento_salvar');
		$objRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'tipo_processo_peticionamento_cadastrar_orientacoes');
		$objRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'indisponibilidade_peticionamento_download');
		//$objRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador,'tipo_procedimento_selecionar');
		
		//modal de tipo de documento
		$objRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador,'serie_peticionamento_selecionar');
		   
		//criando Administração -> Peticionamento Eletrônico
		$objItemMenuDTOPeticionamentoEletronico = $this->adicionarItemMenu($numIdSistemaSei, $numIdPerfilSeiAdministrador, $numIdMenuSei, $numIdItemMenuSeiAdministracao, null, 'Peticionamento Eletrônico', 0);
		 
		//criando Administração -> Peticionamento Eletrônico -> Tipos para Peticionamento
		//criando Administração -> Peticionamento Eletrônico -> Extensão de Arquivos Permitidos
		//criando Administração -> Peticionamento Eletrônico -> Tamanho Máximo de Arquivos
		//criando Administração -> Peticionamento Eletrônico -> Indisponibilidades do SEI
				
		$this->adicionarItemMenu($numIdSistemaSei,
				$numIdPerfilSeiAdministrador,
				$numIdMenuSei,
				$objItemMenuDTOPeticionamentoEletronico->getNumIdItemMenu() ,
				$objTipoProcessoListarDTO->getNumIdRecurso(),
				'Tipos para Peticionamento',
				10);
		
		$this->adicionarItemMenu($numIdSistemaSei,
				$numIdPerfilSeiAdministrador,
				$numIdMenuSei,
				$objItemMenuDTOPeticionamentoEletronico->getNumIdItemMenu() ,
				$objExtensoesArquivosDTO->getNumIdRecurso(),
				'Extensão de Arquivos Permitidos',
				40);
		
		$this->adicionarItemMenu($numIdSistemaSei,
				$numIdPerfilSeiAdministrador,
				$numIdMenuSei,
				$objItemMenuDTOPeticionamentoEletronico->getNumIdItemMenu() ,
				$objTamanhoArquivoDTO->getNumIdRecurso(),
				'Tamanho Máximo de Arquivos',
				30);
		
		$this->adicionarItemMenu($numIdSistemaSei,
				$numIdPerfilSeiAdministrador,
				$numIdMenuSei,
				$objItemMenuDTOPeticionamentoEletronico->getNumIdItemMenu() ,
				$objIndisponibilidadeListarDTO->getNumIdRecurso(),
				'Indisponibilidades do SEI',
				70);
		
		//novo grupo de regra de auditoria nova
		$objRegraAuditoriaDTO = new RegraAuditoriaDTO();
		$objRegraAuditoriaDTO->retNumIdRegraAuditoria();
		$objRegraAuditoriaDTO->setNumIdRegraAuditoria(null);
		$objRegraAuditoriaDTO->setStrSinAtivo('S');
		$objRegraAuditoriaDTO->setNumIdSistema($numIdSistemaSei);
		$objRegraAuditoriaDTO->setArrObjRelRegraAuditoriaRecursoDTO( array() );
		$objRegraAuditoriaDTO->setStrDescricao('Modulo_Peticionamento_Eletronico');
			
		$objRegraAuditoriaRN = new RegraAuditoriaRN();
		$objRegraAuditoriaDTO = $objRegraAuditoriaRN->cadastrar($objRegraAuditoriaDTO);
		 
		$rs = BancoSip::getInstance()->consultarSql('select id_recurso from recurso where id_sistema='.$numIdSistemaSei.' and nome in (
        \'gerir_extensoes_arquivo_peticionamento_cadastrar\', 
		\'gerir_tamanho_arquivo_peticionamento_cadastrar\', 
		\'indisponibilidade_peticionamento_desativar\', 
		\'indisponibilidade_peticionamento_reativar\', 
		\'indisponibilidade_peticionamento_excluir\', 
		\'indisponibilidade_peticionamento_cadastrar\',
		\'indisponibilidade_peticionamento_consultar\', 
		\'indisponibilidade_peticionamento_alterar\', 
		\'indisponibilidade_peticionamento_upload_anexo\',  
		\'tipo_processo_peticionamento_desativar\', 
		\'tipo_processo_peticionamento_reativar\', 
		\'tipo_processo_peticionamento_excluir\', 
		\'tipo_processo_peticionamento_cadastrar\', 
		\'tipo_processo_peticionamento_alterar\', 
		\'tipo_processo_peticionamento_salvar\', 
		\'tipo_processo_peticionamento_cadastrar_orientacoes\')'
		);
				 
		//CRIANDO REGRA DE AUDITORIA PARA NOVOS RECURSOS RECEM ADICIONADOS
		foreach($rs as $recurso){
			BancoSip::getInstance()->executarSql('insert into rel_regra_auditoria_recurso (id_regra_auditoria, id_sistema, id_recurso) values ('.$objRegraAuditoriaDTO->getNumIdRegraAuditoria().', '.$numIdSistemaSei.', '.$recurso['id_recurso'].')');
		}
		 
		$objReplicacaoRegraAuditoriaDTO = new ReplicacaoRegraAuditoriaDTO();
		$objReplicacaoRegraAuditoriaDTO->setStrStaOperacao('A');
		$objReplicacaoRegraAuditoriaDTO->setNumIdRegraAuditoria($objRegraAuditoriaDTO->getNumIdRegraAuditoria());
		 
		$objSistemaRN = new SistemaRN();
		$objSistemaRN->replicarRegraAuditoria($objReplicacaoRegraAuditoriaDTO);
		 
	}
	
	protected function atualizarVersaoConectado(){

	try {

	        $this->inicializar('INICIANDO ATUALIZACAO DO MODULO PETICIONAMENTO NO SIP VERSAO '. SIP_VERSAO );
	        
	        //checando versao do framework
			$numVersaoInfraRequerida = '1.208';
			if (VERSAO_INFRA != $numVersaoInfraRequerida){
				$this->finalizar('VERSAO DO FRAMEWORK PHP INCOMPATIVEL (VERSAO ATUAL '.VERSAO_INFRA.', VERSAO REQUERIDA '.$numVersaoInfraRequerida.')',true);
			}

			//checando BDs suportados
			if (!(BancoSip::getInstance() instanceof InfraMySql) &&
					!(BancoSip::getInstance() instanceof InfraSqlServer) &&
					!(BancoSip::getInstance() instanceof InfraOracle)){
						$this->finalizar('BANCO DE DADOS NAO SUPORTADO: '.get_parent_class(BancoSip::getInstance()),true);
	  }

	  //checando permissoes na base de dados
	  $objInfraMetaBD = new InfraMetaBD(BancoSip::getInstance());
	   
	  if (count($objInfraMetaBD->obterTabelas('sip_teste'))==0){
	  	BancoSip::getInstance()->executarSql('CREATE TABLE sip_teste (id '.$objInfraMetaBD->tipoNumero().' null)');
	  }
	   
	  BancoSip::getInstance()->executarSql('DROP TABLE sip_teste');
	   
	  //checando qual versao instalar
	  $objInfraParametro = new InfraParametro(BancoSip::getInstance());

	  $strVersaoModuloPeticionamento = $objInfraParametro->getValor($this->nomeParametroModulo, false);
	   
	  if (InfraString::isBolVazia($strVersaoModuloPeticionamento)){
	    
	  	//aplica atualizaçoes da versao 001
	  	$this->instalarv001();
	  	$this->instalarv002();
	  	$this->instalarv100();

	  	//adicionando parametro para controlar versao do modulo
	  	BancoSip::getInstance()->executarSql('insert into infra_parametro (valor, nome ) VALUES( \''. $this->versaoAtualDesteModulo .'\',  \''. $this->nomeParametroModulo .'\' )' );
	  	
		$this->logar('ATUALIZAÇÔES DA VERSÃO ' . $this->versaoAtualDesteModulo .' DO MÓDULO PETICIONAMENTO INSTALADAS COM SUCESSO NA BASE DO SIP');
		$this->finalizar('FIM', false);
	  }

	  else if ( $strVersaoModuloPeticionamento == '0.0.1' ){
	  	 
	  	//aplica atualizaçoes da versao 002
	  	$this->instalarv002();
	  	$this->instalarv100();
	  
	  	//adicionando parametro para controlar versao do modulo
	  	BancoSip::getInstance()->executarSql('UPDATE infra_parametro SET valor = \''. $this->versaoAtualDesteModulo .'\' WHERE nome = \''. $this->nomeParametroModulo .'\' ' );
	  		  	
	  	$this->logar('ATUALIZAÇÔES DA VERSÃO ' . $this->versaoAtualDesteModulo .' DO MÓDULO PETICIONAMENTO INSTALADAS COM SUCESSO NA BASE DO SIP');
	  	$this->finalizar('FIM', false);
	  	
	  }
	  
	  else if ( $strVersaoModuloPeticionamento == '0.0.2' ){
	  	 
	  	//aplica atualizaçoes da versao 100
	  	$this->instalarv100();
	  	 
	  	//adicionando parametro para controlar versao do modulo
	  	BancoSip::getInstance()->executarSql('UPDATE infra_parametro SET valor = \''. $this->versaoAtualDesteModulo .'\' WHERE nome = \''. $this->nomeParametroModulo .'\' ' );
	  
	  	$this->logar('ATUALIZAÇÔES DA VERSÃO ' . $this->versaoAtualDesteModulo .' DO MÓDULO PETICIONAMENTO INSTALADAS COM SUCESSO NA BASE DO SIP');
	  	$this->finalizar('FIM', false);
	  
	  }
	  
	  else {
	  	 
		$this->logar('SIP - MÓDULO PETICIONAMENTO v' . $this->versaoAtualDesteModulo . ' JÁ INSTALADO');
	  	$this->finalizar('FIM', true);
		
	  }

	  } catch(Exception $e){
			 
			InfraDebug::getInstance()->setBolLigado(false);
			InfraDebug::getInstance()->setBolDebugInfra(false);
			InfraDebug::getInstance()->setBolEcho(false);
			throw new InfraException('Erro atualizando versão.', $e);
			 
		}

	}

	private function adicionarRecursoPerfil($numIdSistema, $numIdPerfil, $strNome, $strCaminho = null){

	 $objRecursoDTO = new RecursoDTO();
	 $objRecursoDTO->retNumIdRecurso();
	 $objRecursoDTO->setNumIdSistema($numIdSistema);
	 $objRecursoDTO->setStrNome($strNome);

	 $objRecursoRN = new RecursoRN();
	 $objRecursoDTO = $objRecursoRN->consultar($objRecursoDTO);

	 if ($objRecursoDTO==null){

	 	$objRecursoDTO = new RecursoDTO();
	 	$objRecursoDTO->setNumIdRecurso(null);
	 	$objRecursoDTO->setNumIdSistema($numIdSistema);
	 	$objRecursoDTO->setStrNome($strNome);
	 	$objRecursoDTO->setStrDescricao(null);

	 	if ($strCaminho == null){
	 		$objRecursoDTO->setStrCaminho('controlador.php?acao='.$strNome);
	 	}else{
	 		$objRecursoDTO->setStrCaminho($strCaminho);
	 	}

	 	$objRecursoDTO->setStrSinAtivo('S');
	 	$objRecursoDTO = $objRecursoRN->cadastrar($objRecursoDTO);
	 }

	 if ($numIdPerfil!=null){
	 	$objRelPerfilRecursoDTO = new RelPerfilRecursoDTO();
	 	$objRelPerfilRecursoDTO->setNumIdSistema($numIdSistema);
	 	$objRelPerfilRecursoDTO->setNumIdPerfil($numIdPerfil);
	 	$objRelPerfilRecursoDTO->setNumIdRecurso($objRecursoDTO->getNumIdRecurso());

	 	$objRelPerfilRecursoRN = new RelPerfilRecursoRN();

	 	if ($objRelPerfilRecursoRN->contar($objRelPerfilRecursoDTO)==0){
	 		$objRelPerfilRecursoRN->cadastrar($objRelPerfilRecursoDTO);
	 	}
	 }

	 return $objRecursoDTO;
	 
	}

	private function removerRecursoPerfil($numIdSistema, $strNome, $numIdPerfil){

		$objRecursoDTO = new RecursoDTO();
		$objRecursoDTO->setBolExclusaoLogica(false);
		$objRecursoDTO->retNumIdRecurso();
		$objRecursoDTO->setNumIdSistema($numIdSistema);
		$objRecursoDTO->setStrNome($strNome);

		$objRecursoRN = new RecursoRN();
		$objRecursoDTO = $objRecursoRN->consultar($objRecursoDTO);

		if ($objRecursoDTO!=null){
			$objRelPerfilRecursoDTO = new RelPerfilRecursoDTO();
			$objRelPerfilRecursoDTO->retTodos();
			$objRelPerfilRecursoDTO->setNumIdSistema($numIdSistema);
			$objRelPerfilRecursoDTO->setNumIdRecurso($objRecursoDTO->getNumIdRecurso());
			$objRelPerfilRecursoDTO->setNumIdPerfil($numIdPerfil);

			$objRelPerfilRecursoRN = new RelPerfilRecursoRN();
			$objRelPerfilRecursoRN->excluir($objRelPerfilRecursoRN->listar($objRelPerfilRecursoDTO));

			$objRelPerfilItemMenuDTO = new RelPerfilItemMenuDTO();
			$objRelPerfilItemMenuDTO->retTodos();
			$objRelPerfilItemMenuDTO->setNumIdSistema($numIdSistema);
			$objRelPerfilItemMenuDTO->setNumIdRecurso($objRecursoDTO->getNumIdRecurso());
			$objRelPerfilItemMenuDTO->setNumIdPerfil($numIdPerfil);

			$objRelPerfilItemMenuRN = new RelPerfilItemMenuRN();
			$objRelPerfilItemMenuRN->excluir($objRelPerfilItemMenuRN->listar($objRelPerfilItemMenuDTO));
		}
	}

	private function desativarRecurso($numIdSistema, $strNome){
		$objRecursoDTO = new RecursoDTO();
		$objRecursoDTO->retNumIdRecurso();
		$objRecursoDTO->setNumIdSistema($numIdSistema);
		$objRecursoDTO->setStrNome($strNome);

		$objRecursoRN = new RecursoRN();
		$objRecursoDTO = $objRecursoRN->consultar($objRecursoDTO);

		if ($objRecursoDTO!=null){
			$objRecursoRN->desativar(array($objRecursoDTO));
		}
	}

	private function removerRecurso($numIdSistema, $strNome){

		$objRecursoDTO = new RecursoDTO();
		$objRecursoDTO->setBolExclusaoLogica(false);
		$objRecursoDTO->retNumIdRecurso();
		$objRecursoDTO->setNumIdSistema($numIdSistema);
		$objRecursoDTO->setStrNome($strNome);

		$objRecursoRN = new RecursoRN();
		$objRecursoDTO = $objRecursoRN->consultar($objRecursoDTO);

		if ($objRecursoDTO!=null){
			$objRelPerfilRecursoDTO = new RelPerfilRecursoDTO();
			$objRelPerfilRecursoDTO->retTodos();
			$objRelPerfilRecursoDTO->setNumIdSistema($numIdSistema);
			$objRelPerfilRecursoDTO->setNumIdRecurso($objRecursoDTO->getNumIdRecurso());

			$objRelPerfilRecursoRN = new RelPerfilRecursoRN();
			$objRelPerfilRecursoRN->excluir($objRelPerfilRecursoRN->listar($objRelPerfilRecursoDTO));

			$objItemMenuDTO = new ItemMenuDTO();
			$objItemMenuDTO->retNumIdMenu();
			$objItemMenuDTO->retNumIdItemMenu();
			$objItemMenuDTO->setNumIdSistema($numIdSistema);
			$objItemMenuDTO->setNumIdRecurso($objRecursoDTO->getNumIdRecurso());

			$objItemMenuRN = new ItemMenuRN();
			$arrObjItemMenuDTO = $objItemMenuRN->listar($objItemMenuDTO);

			$objRelPerfilItemMenuRN = new RelPerfilItemMenuRN();

			foreach($arrObjItemMenuDTO as $objItemMenuDTO){
				$objRelPerfilItemMenuDTO = new RelPerfilItemMenuDTO();
				$objRelPerfilItemMenuDTO->retTodos();
				$objRelPerfilItemMenuDTO->setNumIdSistema($numIdSistema);
				$objRelPerfilItemMenuDTO->setNumIdItemMenu($objItemMenuDTO->getNumIdItemMenu());

				$objRelPerfilItemMenuRN->excluir($objRelPerfilItemMenuRN->listar($objRelPerfilItemMenuDTO));
			}

			$objItemMenuRN->excluir($arrObjItemMenuDTO);
			$objRecursoRN->excluir(array($objRecursoDTO));
		}
	}

	private function adicionarItemMenu($numIdSistema, $numIdPerfil, $numIdMenu, $numIdItemMenuPai, $numIdRecurso, $strRotulo, $numSequencia ){

		$objItemMenuDTO = new ItemMenuDTO();
		$objItemMenuDTO->retNumIdItemMenu();
		$objItemMenuDTO->setNumIdMenu($numIdMenu);

		if ($numIdItemMenuPai==null){
			$objItemMenuDTO->setNumIdMenuPai(null);
			$objItemMenuDTO->setNumIdItemMenuPai(null);
		}else{
			$objItemMenuDTO->setNumIdMenuPai($numIdMenu);
			$objItemMenuDTO->setNumIdItemMenuPai($numIdItemMenuPai);
		}

		$objItemMenuDTO->setNumIdSistema($numIdSistema);
		$objItemMenuDTO->setNumIdRecurso($numIdRecurso);
		$objItemMenuDTO->setStrRotulo($strRotulo);

		$objItemMenuRN = new ItemMenuRN();
		$objItemMenuDTO = $objItemMenuRN->consultar($objItemMenuDTO);

		if ($objItemMenuDTO==null){

			$objItemMenuDTO = new ItemMenuDTO();
			$objItemMenuDTO->setNumIdItemMenu(null);
			$objItemMenuDTO->setNumIdMenu($numIdMenu);

			if ($numIdItemMenuPai==null){
				$objItemMenuDTO->setNumIdMenuPai(null);
				$objItemMenuDTO->setNumIdItemMenuPai(null);
			}else{
				$objItemMenuDTO->setNumIdMenuPai($numIdMenu);
				$objItemMenuDTO->setNumIdItemMenuPai($numIdItemMenuPai);
			}

			$objItemMenuDTO->setNumIdSistema($numIdSistema);
			$objItemMenuDTO->setNumIdRecurso($numIdRecurso);
			$objItemMenuDTO->setStrRotulo($strRotulo);
			$objItemMenuDTO->setStrDescricao(null);
			$objItemMenuDTO->setNumSequencia($numSequencia);
			$objItemMenuDTO->setStrSinNovaJanela('N');
			$objItemMenuDTO->setStrSinAtivo('S');
			$objItemMenuDTO = $objItemMenuRN->cadastrar($objItemMenuDTO);
		}


		if ($numIdPerfil!=null && $numIdRecurso!=null){

			$objRelPerfilRecursoDTO = new RelPerfilRecursoDTO();
			$objRelPerfilRecursoDTO->setNumIdSistema($numIdSistema);
			$objRelPerfilRecursoDTO->setNumIdPerfil($numIdPerfil);
			$objRelPerfilRecursoDTO->setNumIdRecurso($numIdRecurso);

			$objRelPerfilRecursoRN = new RelPerfilRecursoRN();

			if ($objRelPerfilRecursoRN->contar($objRelPerfilRecursoDTO)==0){
				$objRelPerfilRecursoRN->cadastrar($objRelPerfilRecursoDTO);
			}

			$objRelPerfilItemMenuDTO = new RelPerfilItemMenuDTO();
			$objRelPerfilItemMenuDTO->setNumIdPerfil($numIdPerfil);
			$objRelPerfilItemMenuDTO->setNumIdSistema($numIdSistema);
			$objRelPerfilItemMenuDTO->setNumIdRecurso($numIdRecurso);
			$objRelPerfilItemMenuDTO->setNumIdMenu($numIdMenu);
			$objRelPerfilItemMenuDTO->setNumIdItemMenu($objItemMenuDTO->getNumIdItemMenu());

			$objRelPerfilItemMenuRN = new RelPerfilItemMenuRN();

			if ($objRelPerfilItemMenuRN->contar($objRelPerfilItemMenuDTO)==0){
				$objRelPerfilItemMenuRN->cadastrar($objRelPerfilItemMenuDTO);
			}
		}

		return $objItemMenuDTO;
	}

	private function removerItemMenu($numIdSistema, $numIdMenu, $numIdItemMenu){

		$objItemMenuDTO = new ItemMenuDTO();
		$objItemMenuDTO->retNumIdMenu();
		$objItemMenuDTO->retNumIdItemMenu();
		$objItemMenuDTO->setNumIdSistema($numIdSistema);
		$objItemMenuDTO->setNumIdMenu($numIdMenu);
		$objItemMenuDTO->setNumIdItemMenu($numIdItemMenu);

		$objItemMenuRN = new ItemMenuRN();
		$objItemMenuDTO = $objItemMenuRN->consultar($objItemMenuDTO);

		if ($objItemMenuDTO!=null) {

			$objRelPerfilItemMenuDTO = new RelPerfilItemMenuDTO();
			$objRelPerfilItemMenuDTO->retTodos();
			$objRelPerfilItemMenuDTO->setNumIdSistema($numIdSistema);
			$objRelPerfilItemMenuDTO->setNumIdMenu($objItemMenuDTO->getNumIdMenu());
			$objRelPerfilItemMenuDTO->setNumIdItemMenu($objItemMenuDTO->getNumIdItemMenu());

			$objRelPerfilItemMenuRN = new RelPerfilItemMenuRN();
			$objRelPerfilItemMenuRN->excluir($objRelPerfilItemMenuRN->listar($objRelPerfilItemMenuDTO));

			$objItemMenuRN->excluir(array($objItemMenuDTO));
		}
	}

	private function removerPerfil($numIdSistema, $strNome){

		$objPerfilDTO = new PerfilDTO();
		$objPerfilDTO->retNumIdPerfil();
		$objPerfilDTO->setNumIdSistema($numIdSistema);
		$objPerfilDTO->setStrNome($strNome);

		$objPerfilRN = new PerfilRN();
		$objPerfilDTO = $objPerfilRN->consultar($objPerfilDTO);

		if ($objPerfilDTO!=null){

			$objPermissaoDTO = new PermissaoDTO();
			$objPermissaoDTO->retNumIdSistema();
			$objPermissaoDTO->retNumIdUsuario();
			$objPermissaoDTO->retNumIdPerfil();
			$objPermissaoDTO->retNumIdUnidade();
			$objPermissaoDTO->setNumIdSistema($numIdSistema);
			$objPermissaoDTO->setNumIdPerfil($objPerfilDTO->getNumIdPerfil());

			$objPermissaoRN = new PermissaoRN();
			$objPermissaoRN->excluir($objPermissaoRN->listar($objPermissaoDTO));

			$objRelPerfilItemMenuDTO = new RelPerfilItemMenuDTO();
			$objRelPerfilItemMenuDTO->retTodos();
			$objRelPerfilItemMenuDTO->setNumIdSistema($numIdSistema);
			$objRelPerfilItemMenuDTO->setNumIdPerfil($objPerfilDTO->getNumIdPerfil());

			$objRelPerfilItemMenuRN = new RelPerfilItemMenuRN();
			$objRelPerfilItemMenuRN->excluir($objRelPerfilItemMenuRN->listar($objRelPerfilItemMenuDTO));

			$objRelPerfilRecursoDTO = new RelPerfilRecursoDTO();
			$objRelPerfilRecursoDTO->retTodos();
			$objRelPerfilRecursoDTO->setNumIdSistema($numIdSistema);
			$objRelPerfilRecursoDTO->setNumIdPerfil($objPerfilDTO->getNumIdPerfil());

			$objRelPerfilRecursoRN = new RelPerfilRecursoRN();
			$objRelPerfilRecursoRN->excluir($objRelPerfilRecursoRN->listar($objRelPerfilRecursoDTO));

			$objCoordenadorPerfilDTO = new CoordenadorPerfilDTO();
			$objCoordenadorPerfilDTO->retTodos();
			$objCoordenadorPerfilDTO->setNumIdSistema($numIdSistema);
			$objCoordenadorPerfilDTO->setNumIdPerfil($objPerfilDTO->getNumIdPerfil());

			$objCoordenadorPerfilRN = new CoordenadorPerfilRN();
			$objCoordenadorPerfilRN->excluir($objCoordenadorPerfilRN->listar($objCoordenadorPerfilDTO));

			$objPerfilRN->excluir(array($objPerfilDTO));
		}
	}

}

//========================= INICIO SCRIPT EXECUÇAO =============	

	try{
	
    session_start();
		
		SessaoSip::getInstance(false);

		$objVersaoRN = new AtualizadorSipModuloPeticionamentoRN();
		$objVersaoRN->atualizarVersao();

	}catch(Exception $e){
		echo(nl2br(InfraException::inspecionar($e)));
		try{LogSip::getInstance()->gravar(InfraException::inspecionar($e));}catch(Exception $e){}
	}

//========================== FIM SCRIPT EXECUÇÂO ====================
?>