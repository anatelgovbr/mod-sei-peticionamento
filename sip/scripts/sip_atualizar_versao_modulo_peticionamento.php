<?
/**
 * ANATEL
 *
 * 21/05/2016 - criado por marcelo.bezerra - CAST
 *
 */

require_once dirname(__FILE__).'/../web/Sip.php';

class MdPetAtualizadorSipRN extends InfraRN {

    private $numSeg = 0;
    private $versaoAtualDesteModulo = '2.0.0';
    private $nomeDesteModulo = 'MÓDULO DE PETICIONAMENTO E INTIMAÇÃO ELETRÔNICOS';
    private $nomeParametroModulo = 'VERSAO_MODULO_PETICIONAMENTO';
    private $historicoVersoes = array('0.0.1', '0.0.2', '1.0.3', '1.0.4', '1.1.0', '2.0.0');

    public function __construct(){
        parent::__construct();
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

    //Contem atualizações da versao 2.0.0
    protected function instalarv200(){
    	
    	$arrAuditoria = array();
    	
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
    	
    	$objPerfilBasicoDTO = new PerfilDTO();
    	$objPerfilBasicoDTO->retNumIdPerfil();
    	$objPerfilBasicoDTO->setNumIdSistema($numIdSistemaSei);
    	$objPerfilBasicoDTO->setStrNome('Básico');
    	$objPerfilBasicoDTO = $objPerfilRN->consultar( $objPerfilBasicoDTO );
    	
    	if ($objPerfilBasicoDTO== null){
    		throw new InfraException('Perfil Básico do sistema SEI não encontrado.');
    	}

    	$numIdPerfilSeiBasico = $objPerfilBasicoDTO->getNumIdPerfil();

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
    	
    	//SEI ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    	$this->logar('ATUALIZANDO RECURSOS, MENUS E PERFIS DO '. $this->nomeDesteModulo .' NA BASE DO SIP...');
    	
    	$objRegraAuditoriaDTO = new RegraAuditoriaDTO();
    	$objRegraAuditoriaDTO->retNumIdRegraAuditoria();
    	$objRegraAuditoriaDTO->setNumIdSistema($numIdSistemaSei);
    	$objRegraAuditoriaDTO->setStrDescricao('Modulo_Peticionamento_Eletronico');
    	
    	$objRegraAuditoriaRN = new RegraAuditoriaRN();
    	$objRegraAuditoriaDTO = $objRegraAuditoriaRN->consultar($objRegraAuditoriaDTO);
    	
    	//recupera o ID do menu Peticionamento Eletronico
    	$objItemMenuDTOPeticionamentoEletronico = new ItemMenuDTO();
    	$objItemMenuDTOPeticionamentoEletronico->retNumIdItemMenu();
    	$objItemMenuDTOPeticionamentoEletronico->setNumIdSistema($numIdSistemaSei);
    	$objItemMenuDTOPeticionamentoEletronico->setStrRotulo('Peticionamento Eletrônico');
    	$objItemMenuDTOPeticionamentoEletronico = $objItemMenuRN->consultar( $objItemMenuDTOPeticionamentoEletronico );
    	
    	//add menu default
    	$objItemMenuIntimacaoTacita = $this->adicionarItemMenu($numIdSistemaSei,
    			$numIdPerfilSeiAdministrador,
    			$numIdMenuSei,
    			$objItemMenuDTOPeticionamentoEletronico->getNumIdItemMenu(),
    			'',
    			'Intimação Eletrônica',
    			70);
    	
    	//Cadastro de Menus
    	// Prazo Tácito
    	$objMenuListarDTO1 = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'md_pet_int_prazo_tacita_alterar');
    	$objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'md_pet_int_prazo_tacita_cadastrar');
    	$objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiBasico, 'md_pet_int_prazo_tacita_consultar');
    	$objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiBasico, 'md_pet_int_prazo_tacita_listar');

    	//tipo de documento
    	$objMenuListarDTO3 = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador,     'md_pet_int_serie_cadastrar');

    	$objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiBasico,            'md_pet_int_serie_listar');
		
    	$objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador,     'md_pet_int_serie_alterar');
    	$objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador,     'md_pet_int_serie_excluir');
    	$objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador,     'md_pet_int_serie_desativar');
    	$objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador,     'md_pet_int_serie_reativar');
    	$objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiBasico,            'md_pet_int_serie_selecionar');

    	//tipo de resposta
    	$objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'md_pet_int_tipo_resp_cadastrar');
    	$objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'md_pet_int_tipo_resp_alterar');
    	
    	$objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiBasico, 'md_pet_int_tipo_resp_consultar');

    	$objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'md_pet_int_tipo_resp_listar');
    	$objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'md_pet_int_tipo_resp_desativar');
    	$objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'md_pet_int_tipo_resp_reativar');
    	$objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'md_pet_int_tipo_resp_excluir');
    	$objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'md_pet_int_tipo_resp_selecionar');
    	
    	//tipo de intimacao
    	$objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador    , 'md_pet_int_tipo_intimacao_cadastrar');
    	$objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador    , 'md_pet_int_tipo_intimacao_alterar');
        $objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador    , 'md_pet_int_tipo_intimacao_selecionar');
    	$objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiBasico           , 'md_pet_int_tipo_intimacao_consultar');
		
		$objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador    , 'md_pet_int_tipo_intimacao_listar');
    	$objMenuListarDTO2 = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiBasico           , 'md_pet_int_tipo_intimacao_listar');
		
    	$objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'md_pet_int_tipo_intimacao_desativar');
    	$objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'md_pet_int_tipo_intimacao_reativar');
    	$objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'md_pet_int_tipo_intimacao_excluir');
    	
    	//rel intimacao x resposta
    	$objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiBasico, 'md_pet_int_rel_intim_resp_cadastrar');
    	$objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiBasico, 'md_pet_int_rel_intim_resp_alterar');
    	$objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiBasico, 'md_pet_int_rel_intim_resp_excluir');
    	$objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiBasico, 'md_pet_int_rel_intim_resp_consultar');
    	$objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiBasico, 'md_pet_int_rel_intim_resp_listar');
    	$objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiBasico, 'md_pet_int_rel_intim_resp_desativar');
    	$objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiBasico, 'md_pet_int_rel_intim_resp_reativar');
    	
    	//cadastro destinatario
    	$objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiBasico,'md_pet_int_rel_destinatario_cadastrar');
    	$objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiBasico,'md_pet_int_rel_destinatario_alterar');
    	$objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiBasico,'md_pet_int_rel_destinatario_excluir');
    	$objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiBasico,'md_pet_int_rel_destinatario_listar');
    	$objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiBasico,'md_pet_int_rel_destinatario_consultar');
    	$objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiBasico,'md_pet_int_rel_destinatario_desativar');
    	$objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiBasico,'md_pet_int_rel_destinatario_reativar');
    	
    	//gerar intimacao + listar intimaçao
    	$objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiBasico, 'md_pet_intimacao_cadastrar');
    	$objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiBasico, 'md_pet_intimacao_consultar');
    	$objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiBasico, 'md_pet_intimacao_listar');
    	$objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiBasico, 'md_pet_intimacao_eletronica_listar');
    	$objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiBasico, 'md_pet_int_dest_resposta_listar');
    	
    	//Aceitar/Consultar Intimacao
    	$objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiBasico, 'md_pet_intimacao_usu_ext_confirmar_aceite');
    	
    	//responder intimaçao
    	$objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiBasico, 'md_pet_responder_intimacao_usu_ext');
    	
    	//rel tipo_resp x intimacao
    	$objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiBasico, 'md_pet_int_rel_tipo_resp_cadastrar');
    	$objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiBasico, 'md_pet_int_rel_tipo_resp_alterar');
    	$objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiBasico, 'md_pet_int_rel_tipo_resp_excluir');
    	$objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiBasico, 'md_pet_int_rel_tipo_resp_listar');
    	$objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiBasico, 'md_pet_int_rel_tipo_resp_consultar');
    	$objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiBasico, 'md_pet_int_rel_tipo_resp_reativar');
    	$objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiBasico, 'md_pet_int_rel_tipo_resp_desativar');
    	
    	//md_pet_int_documento_cadastrar
    	$objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiBasico,            'md_pet_int_documento_cadastrar');
    	$objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiBasico,            'md_pet_int_documento_alterar');
    	$objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiBasico,            'md_pet_int_documento_excluir');
    	$objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiBasico,            'md_pet_int_documento_reativar');
    	$objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiBasico,            'md_pet_int_documento_desativar');
        $objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiBasico,            'md_pet_int_documento_consultar');
    	$objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiBasico,            'md_pet_int_documento_listar');

        $objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'md_pet_int_documento_cadastrar');
        $objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'md_pet_int_documento_alterar');
        $objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'md_pet_int_documento_excluir');
        $objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'md_pet_int_documento_reativar');
        $objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'md_pet_int_documento_desativar');
        $objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'md_pet_int_documento_consultar');
        $objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'md_pet_int_documento_listar');

    	$objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiBasico,        'md_pet_int_documento_consultar');

    	//md_pet_int_doc_disponivel_cadastrar
    	$objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiBasico, 'md_pet_int_doc_disponivel_cadastrar');
    	$objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiBasico, 'md_pet_int_doc_disponivel_alterar');
    	$objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiBasico, 'md_pet_int_doc_disponivel_excluir');
    	$objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiBasico, 'md_pet_int_doc_disponivel_reativar');
    	$objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiBasico, 'md_pet_int_doc_disponivel_desativar');
    	$objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiBasico, 'md_pet_int_doc_disponivel_listar');
    	
    	$objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiBasico, 'md_pet_int_doc_disponivel_consultar');
    	
    	//md_pet_int_aceite_cadastrar
    	$objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiBasico, 'md_pet_int_aceite_cadastrar');
    	$objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiBasico, 'md_pet_int_aceite_consultar');
    	$objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiBasico, 'md_pet_int_aceite_listar');
    	$objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiBasico, 'md_pet_int_aceite_alterar');

    	//Add para Auditoria
    	array_push($arrAuditoria,
    			'\'md_pet_int_prazo_tacita_alterar\'',
    			'\'md_pet_int_prazo_tacita_cadastrar\'',
    			'\'md_pet_intimacao_usu_ext_confirmar_aceite\'',
    			'\'md_pet_responder_intimacao_usu_ext\'',
    			
    			'\'md_pet_int_tipo_resp_cadastrar\'',
    			'\'md_pet_int_tipo_resp_alterar\'',
    			'\'md_pet_int_tipo_resp_desativar\'',
    			'\'md_pet_int_tipo_resp_reativar\'',
    			'\'md_pet_int_tipo_resp_excluir\'',
    			
    			'\'md_pet_int_tipo_intimacao_cadastrar\'',
    			'\'md_pet_int_tipo_intimacao_alterar\'',
    			'\'md_pet_int_tipo_intimacao_desativar\'',
    			'\'md_pet_int_tipo_intimacao_reativar\'',
    			'\'md_pet_int_tipo_intimacao_excluir\'',
    			'\'md_pet_intimacao_cadastrar\'');
    	
    	//Menu Prazo para Intimação Tácita
    	$this->adicionarItemMenu($numIdSistemaSei,
    			$numIdPerfilSeiAdministrador,
    			$numIdMenuSei,
    			$objItemMenuIntimacaoTacita->getNumIdItemMenu(),
    			$objMenuListarDTO1->getNumIdRecurso(),
    			'Prazo para Intimação Tácita',
    			10);
    	
    	//menu Tipo de Intimação
    	$this->adicionarItemMenu($numIdSistemaSei,
    			$numIdPerfilSeiAdministrador,
    			$numIdMenuSei,
    			$objItemMenuIntimacaoTacita->getNumIdItemMenu(),
    			$objMenuListarDTO2->getNumIdRecurso(),
    			'Tipos de Intimação Eletrônica',
    			20);

    	//menu Tipos de Documentos para Intimação
    	$this->adicionarItemMenu($numIdSistemaSei,
    			$numIdPerfilSeiAdministrador,
    			$numIdMenuSei,
    			$objItemMenuIntimacaoTacita->getNumIdItemMenu(),
    			$objMenuListarDTO3->getNumIdRecurso(),
    			'Tipos de Documentos para Intimação',
    			30);
    	
    	//FIM 8610

        //recupera o ID do menu Relatorio EU9265
        $objItemMenuDTORelatorioDTO = new ItemMenuDTO();
        $objItemMenuDTORelatorioDTO->retNumIdItemMenu();
        $objItemMenuDTORelatorioDTO->setNumIdSistema($numIdSistemaSei);
        $objItemMenuDTORelatorioDTO->setStrRotulo('Relatórios');
        $objItemMenuDTORelatorioDTO = $objItemMenuRN->consultar( $objItemMenuDTORelatorioDTO );

        $objItemRecursoIntRelaListarDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiBasico, 'md_pet_int_relatorio_listar');
        $objItemRecursoDTO1             = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiBasico, 'md_pet_int_relatorio_ht_listar');
        $objItemRecursoDTO2             = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiBasico, 'md_pet_int_relatorio_exp_excel');

        $this->adicionarItemMenu($numIdSistemaSei,
            $numIdPerfilSeiBasico,
            $numIdMenuSei,
            $objItemMenuDTORelatorioDTO->getNumIdItemMenu(),
            $objItemRecursoIntRelaListarDTO->getNumIdRecurso(),
            'Intimações Eletrônicas',
            30);

    	//ADD para Auditoria todos recursos
    	$rs = BancoSip::getInstance()->consultarSql('select id_recurso from recurso where id_sistema='.$numIdSistemaSei.' and nome in (
		  '.implode(', ', $arrAuditoria).')'
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
    	
    	//Atualizando parametro para controlar versao do modulo
    	$this->logar('ATUALIZANDO PARÂMETRO '. $this->nomeParametroModulo .' NA TABELA infra_parametro PARA CONTROLAR A VERSÃO DO MÓDULO');
    	BancoSip::getInstance()->executarSql('UPDATE infra_parametro SET valor = \'2.0.0\' WHERE nome = \''. $this->nomeParametroModulo .'\' ' );
    }
    
    //Contem atualizações da versao 1.1.0
    protected function instalarv110(){
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

        //SIP v 0.0.1 ////////////////////////////////////////////////////////////////////////////////////////////////////////////
        $this->logar('ATUALIZANDO RECURSOS, MENUS E PERFIS DO '. $this->nomeDesteModulo .' NA BASE DO SIP...');

        $objDTO = $this->renomearRecurso($numIdSistemaSei, 'gerir_extensoes_arquivo_peticionamento_cadastrar', 'md_pet_extensoes_arquivo_cadastrar');
        $objDTO = $this->renomearRecurso($numIdSistemaSei, 'gerir_tamanho_arquivo_peticionamento_cadastrar', 'md_pet_tamanho_arquivo_cadastrar');
        $objDTO = $this->renomearRecurso($numIdSistemaSei, 'indisponibilidade_peticionamento_listar', 'md_pet_indisponibilidade_listar');
        $objDTO = $this->renomearRecurso($numIdSistemaSei, 'tipo_processo_peticionamento_listar', 'md_pet_tipo_processo_listar');

        //recursos que nao sao chamados em menus
        //gerir tamanho de arquivo
        $objDTO = $this->renomearRecurso($numIdSistemaSei, 'arquivo_extensao_peticionamento_selecionar', 'md_pet_arquivo_extensao_selecionar');
        $objDTO = $this->renomearRecurso($numIdSistemaSei, 'gerir_tamanho_arquivo_peticionamento_listar', 'md_pet_tamanho_arquivo_listar');
        $objDTO = $this->renomearRecurso($numIdSistemaSei, 'gerir_tamanho_arquivo_peticionamento_consultar', 'md_pet_tamanho_arquivo_consultar');

        $objDTO = $this->renomearRecurso($numIdSistemaSei, 'indisponibilidade_peticionamento_desativar', 'md_pet_indisponibilidade_desativar');
        $objDTO = $this->renomearRecurso($numIdSistemaSei, 'indisponibilidade_peticionamento_reativar', 'md_pet_indisponibilidade_reativar');
        $objDTO = $this->renomearRecurso($numIdSistemaSei, 'indisponibilidade_peticionamento_excluir', 'md_pet_indisponibilidade_excluir');
        $objDTO = $this->renomearRecurso($numIdSistemaSei, 'indisponibilidade_peticionamento_cadastrar', 'md_pet_indisponibilidade_cadastrar');
        $objDTO = $this->renomearRecurso($numIdSistemaSei, 'indisponibilidade_peticionamento_consultar', 'md_pet_indisponibilidade_consultar');
        $objDTO = $this->renomearRecurso($numIdSistemaSei, 'indisponibilidade_peticionamento_alterar', 'md_pet_indisponibilidade_alterar');
        $objDTO = $this->renomearRecurso($numIdSistemaSei, 'indisponibilidade_peticionamento_upload_anexo', 'md_pet_indisponibilidade_upload_anexo');

        //tipo processo
        $objDTO = $this->renomearRecurso($numIdSistemaSei, 'tipo_processo_peticionamento_desativar', 'md_pet_tipo_processo_desativar');
        $objDTO = $this->renomearRecurso($numIdSistemaSei, 'tipo_processo_peticionamento_reativar', 'md_pet_tipo_processo_reativar');
        $objDTO = $this->renomearRecurso($numIdSistemaSei, 'tipo_processo_peticionamento_excluir', 'md_pet_tipo_processo_excluir');
        $objDTO = $this->renomearRecurso($numIdSistemaSei, 'tipo_processo_peticionamento_cadastrar', 'md_pet_tipo_processo_cadastrar');
        $objDTO = $this->renomearRecurso($numIdSistemaSei, 'tipo_processo_peticionamento_alterar', 'md_pet_tipo_processo_alterar');
        $objDTO = $this->renomearRecurso($numIdSistemaSei, 'tipo_processo_peticionamento_consultar', 'md_pet_tipo_processo_consultar');
        $objDTO = $this->renomearRecurso($numIdSistemaSei, 'tipo_processo_peticionamento_salvar', 'md_pet_tipo_processo_salvar');
        $objDTO = $this->renomearRecurso($numIdSistemaSei, 'tipo_processo_peticionamento_cadastrar_orientacoes', 'md_pet_tipo_processo_cadastrar_orientacoes');

        $objDTO = $this->renomearRecurso($numIdSistemaSei, 'indisponibilidade_peticionamento_download', 'md_pet_indisponibilidade_download');

        $objDTO = $this->renomearRecurso($numIdSistemaSei, 'serie_peticionamento_selecionar', 'md_pet_serie_selecionar');

        //SIP v 0.0.1 - FIM///////////////////////////////////////////////////////////////////////////////////////////////////////


        //SIP v 0.0.2 ////////////////////////////////////////////////////////////////////////////////////////////////////////////
        $this->logar('ATUALIZANDO RECURSOS, MENUS E PERFIS DO '. $this->nomeDesteModulo .' NA BASE DO SIP...');

        $objDTO = $this->renomearRecurso($numIdSistemaSei, 'menu_peticionamento_usuario_externo_listar', 'md_pet_menu_usu_ext_listar');

        //Tipos de Contatos Permitidos
        $objDTO = $this->renomearRecurso($numIdSistemaSei, 'gerir_tipo_contexto_peticionamento_cadastrar', 'md_pet_tp_ctx_contato_cadastrar');

        $objDTO = $this->renomearRecurso($numIdSistemaSei, 'menu_peticionamento_usuario_externo_desativar', 'md_pet_menu_usu_ext_desativar');
        $objDTO = $this->renomearRecurso($numIdSistemaSei, 'menu_peticionamento_usuario_externo_reativar', 'md_pet_menu_usu_ext_reativar');
        $objDTO = $this->renomearRecurso($numIdSistemaSei, 'menu_peticionamento_usuario_externo_excluir', 'md_pet_menu_usu_ext_excluir');
        $objDTO = $this->renomearRecurso($numIdSistemaSei, 'menu_peticionamento_usuario_externo_cadastrar', 'md_pet_menu_usu_ext_cadastrar');
        $objDTO = $this->renomearRecurso($numIdSistemaSei, 'menu_peticionamento_usuario_externo_consultar', 'md_pet_menu_usu_ext_consultar');
        $objDTO = $this->renomearRecurso($numIdSistemaSei, 'menu_peticionamento_usuario_externo_alterar', 'md_pet_menu_usu_ext_alterar');

        //SIP v 0.0.2 - FIM///////////////////////////////////////////////////////////////////////////////////////////////////////


        //SIP v 1.0.0 ////////////////////////////////////////////////////////////////////////////////////////////////////////////
        $this->logar('ATUALIZANDO RECURSOS, MENUS E PERFIS DO '. $this->nomeDesteModulo .' NA BASE DO SIP...');

        //Hipoteses Legais Permitidas
        $objDTO = $this->renomearRecurso($numIdSistemaSei, 'hipotese_legal_nl_acesso_peticionamento_cadastrar', 'md_pet_hipotese_legal_nl_acesso_cadastrar');
        $objDTO = $this->renomearRecurso($numIdSistemaSei, 'hipotese_legal_peticionamento_selecionar', 'md_pet_hipotese_legal_selecionar');

        //SIP v 1.0.0 - FIM///////////////////////////////////////////////////////////////////////////////////////////////////////


        //SIP v 1.0.4 ////////////////////////////////////////////////////////////////////////////////////////////////////////////
        $this->logar('ATUALIZANDO RECURSOS, MENUS E PERFIS DO '. $this->nomeDesteModulo .' NA BASE DO SIP...');

        //SIP v 1.0.4 - FIM///////////////////////////////////////////////////////////////////////////////////////////////////////


        //SEI ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        $this->logar('ATUALIZANDO RECURSOS, MENUS E PERFIS DO '. $this->nomeDesteModulo .' NA BASE DO SIP...');
        //criando os recursos e vinculando-os aos perfil Administrador
        //recursos que serao chamados via menus vem primeiro
        $objRecursoComMenuDTO1 = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador,'md_pet_intercorrente_criterio_listar');
        $objRecursoDTO2 = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'md_pet_intercorrente_criterio_desativar');
        $objRecursoDTO3 = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'md_pet_intercorrente_criterio_reativar');
        $objRecursoDTO4 = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'md_pet_intercorrente_criterio_excluir');
        $objRecursoDTO5 = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'md_pet_intercorrente_criterio_cadastrar');
        $objRecursoDTO6 = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'md_pet_intercorrente_criterio_alterar');
        $objRecursoDTO6 = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'md_pet_intercorrente_criterio_consultar');
        $objRecursoDTO6 = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'md_pet_intercorrente_criterio_padrao');

        //criando os recursos e vinculando-os ao perfil Administrador
        //recursos que serao chamados via menus vem primeiro
        //Cadastro de Menus
        //Hipoteses Legais Permitidas
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
            $objRecursoComMenuDTO1->getNumIdRecurso(),
            'Critérios para Intercorrente',
            70);

        $objRegraAuditoriaDTO = new RegraAuditoriaDTO();
        $objRegraAuditoriaDTO->retNumIdRegraAuditoria();
        $objRegraAuditoriaDTO->setNumIdSistema($numIdSistemaSei);
        $objRegraAuditoriaDTO->setStrDescricao('Modulo_Peticionamento_Eletronico');

        $objRegraAuditoriaRN = new RegraAuditoriaRN();
        $objRegraAuditoriaDTO = $objRegraAuditoriaRN->consultar($objRegraAuditoriaDTO);

        $rs = BancoSip::getInstance()->consultarSql('select id_recurso from recurso where id_sistema='.$numIdSistemaSei.' and nome in (
		  \'criterio_intercorrente_peticionamento_listar\')'
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

        //Atualizando parametro para controlar versao do modulo
        $this->logar('ATUALIZANDO PARÂMETRO '. $this->nomeParametroModulo .' NA TABELA infra_parametro PARA CONTROLAR A VERSÃO DO MÓDULO');
        BancoSip::getInstance()->executarSql('UPDATE infra_parametro SET valor = \'1.1.0\' WHERE nome = \''. $this->nomeParametroModulo .'\' ' );
    }

    //Contem atualizações da versao 1.0.4
    protected function instalarv104(){

        //Atualizando parametro para controlar versao do modulo
        $this->logar('ATUALIZANDO PARÂMETRO '. $this->nomeParametroModulo .' NA TABELA infra_parametro PARA CONTROLAR A VERSÃO DO MÓDULO');
        BancoSip::getInstance()->executarSql('UPDATE infra_parametro SET valor = \'1.0.4\' WHERE nome = \''. $this->nomeParametroModulo .'\' ' );
    }

    //Contem atualizações da versao 1.0.0
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
        $this->logar('ATUALIZANDO RECURSOS, MENUS E PERFIS DO '. $this->nomeDesteModulo .' NA BASE DO SIP...');

        //criando os recursos e vinculando-os ao perfil Administrador
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

        //Atualizando parametro para controlar versao do modulo
        $this->logar('ATUALIZANDO PARÂMETRO '. $this->nomeParametroModulo .' NA TABELA infra_parametro PARA CONTROLAR A VERSÃO DO MÓDULO');
        BancoSip::getInstance()->executarSql('UPDATE infra_parametro SET valor = \'1.0.0\' WHERE nome = \''. $this->nomeParametroModulo .'\' ' );
    }

    //Contem atualizações da versao 0.0.2
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
        $this->logar('ATUALIZANDO RECURSOS, MENUS E PERFIS DO '. $this->nomeDesteModulo .' NA BASE DO SIP...');

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

        //Atualizando parametro para controlar versao do modulo
        $this->logar('ATUALIZANDO PARÂMETRO '. $this->nomeParametroModulo .' NA TABELA infra_parametro PARA CONTROLAR A VERSÃO DO MÓDULO');
        BancoSip::getInstance()->executarSql('UPDATE infra_parametro SET valor = \'0.0.2\' WHERE nome = \''. $this->nomeParametroModulo .'\' ' );
    }

    //Contem atualizações da versao 0.0.1
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
        $this->logar('ATUALIZANDO RECURSOS, MENUS E PERFIS DO '. $this->nomeDesteModulo .' NA BASE DO SIP...');

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

        //Adicionando parametro para controlar versao do modulo
        $this->logar('ADICIONANDO PARAMETRO '. $this->nomeParametroModulo .' NA TABELA infra_parametro PARA CONTROLAR A VERSÃO DO MÓDULO');
        BancoSip::getInstance()->executarSql('INSERT INTO infra_parametro (valor, nome ) VALUES( \'0.0.1\',  \''. $this->nomeParametroModulo .'\' )' );
    }

    protected function atualizarVersaoConectado(){

        try {
            $this->inicializar('INICIANDO A INSTALAÇÃO/ATUALIZAÇÃO DO ' . $this->nomeDesteModulo . ' NO SIP VERSÃO ' . SIP_VERSAO);

            //testando versao do framework
            $numVersaoInfraRequerida = '1.385';
            $versaoInfraFormatada = (int) str_replace('.','', VERSAO_INFRA);
            $versaoInfraReqFormatada = (int) str_replace('.','', $numVersaoInfraRequerida);
            
            if ($versaoInfraFormatada < $versaoInfraReqFormatada){
            	$this->finalizar('VERSÃO DO FRAMEWORK PHP INCOMPATÍVEL (VERSÃO ATUAL '.VERSAO_INFRA.', SENDO REQUERIDA VERSÃO IGUAL OU SUPERIOR A '.$numVersaoInfraRequerida.')',true);
            }

            //checando BDs suportados
            if (!(BancoSip::getInstance() instanceof InfraMySql) &&
                !(BancoSip::getInstance() instanceof InfraSqlServer) &&
                !(BancoSip::getInstance() instanceof InfraOracle)) {
                $this->finalizar('BANCO DE DADOS NÃO SUPORTADO: ' . get_parent_class(BancoSip::getInstance()), true);
            }

            //checando permissoes na base de dados
            $objInfraMetaBD = new InfraMetaBD(BancoSip::getInstance());

            if (count($objInfraMetaBD->obterTabelas('sip_teste'))==0){
                BancoSip::getInstance()->executarSql('CREATE TABLE sip_teste (id '.$objInfraMetaBD->tipoNumero().' null)');
            }

            BancoSip::getInstance()->executarSql('DROP TABLE sip_teste');

            $objInfraParametro = new InfraParametro(BancoSip::getInstance());

            $strVersaoModuloPeticionamento = $objInfraParametro->getValor($this->nomeParametroModulo, false);

            //VERIFICANDO QUAL VERSAO DEVE SER INSTALADA NESTA EXECUCAO
            //se nao tem nenhuma versao instalada, instalar todas
            if (InfraString::isBolVazia($strVersaoModuloPeticionamento)){
                $this->instalarv001();
                $this->instalarv002();
                $this->instalarv100();
                $this->instalarv104();
                $this->instalarv110();
                $this->instalarv200();
                $this->logar('INSTALAÇÃO/ATUALIZAÇÃO DA VERSÃO '. $this->versaoAtualDesteModulo .' DO '. $this->nomeDesteModulo .' REALIZADA COM SUCESSO NA BASE DO SIP');
                $this->finalizar('FIM', false);
            }

            //se ja tem 001 instala apenas 002, 100, 104 e 110
            else if ( $strVersaoModuloPeticionamento == '0.0.1' ){
                $this->instalarv002();
                $this->instalarv100();
                $this->instalarv104();
                $this->instalarv110();
                $this->instalarv200();
                $this->logar('INSTALAÇÃO/ATUALIZAÇÃO DA VERSÃO '. $this->versaoAtualDesteModulo .' DO '. $this->nomeDesteModulo .' REALIZADA COM SUCESSO NA BASE DO SIP');
                $this->finalizar('FIM', false);
            }

            //se ja tem 002 instala apenas 100, 104 e 110
            else if ( $strVersaoModuloPeticionamento == '0.0.2' ){
                $this->instalarv100();
                $this->instalarv104();
                $this->instalarv110();
                $this->instalarv200();
                $this->logar('INSTALAÇÃO/ATUALIZAÇÃO DA VERSÃO '. $this->versaoAtualDesteModulo .' DO '. $this->nomeDesteModulo .' REALIZADA COM SUCESSO NA BASE DO SIP');
                $this->finalizar('FIM', false);
            }

            //se ja tem 100 ou 103 instala a 104 e 110
            else if( in_array($strVersaoModuloPeticionamento, array('1.0.0', '1.0.3')) ){
                $this->instalarv104();
                $this->instalarv110();
                $this->instalarv200();
                $this->logar('INSTALAÇÃO/ATUALIZAÇÃO DA VERSÃO '. $this->versaoAtualDesteModulo .' DO '. $this->nomeDesteModulo .' REALIZADA COM SUCESSO NA BASE DO SIP');
                $this->finalizar('FIM', false);
            }
            //se ja tem 104 instala apenas 110
            else if ( $strVersaoModuloPeticionamento == '1.0.4' ){
                $this->instalarv110();
                $this->instalarv200();
                $this->logar('INSTALAÇÃO/ATUALIZAÇÃO DA VERSÃO '. $this->versaoAtualDesteModulo .' DO '. $this->nomeDesteModulo .' REALIZADA COM SUCESSO NA BASE DO SIP');
                $this->finalizar('FIM', false);
            }
            
            //se ja tem 104 instala apenas 200
            else if ( $strVersaoModuloPeticionamento == '1.1.0' ){
            	$this->instalarv200();
            	$this->logar('INSTALAÇÃO/ATUALIZAÇÃO DA VERSÃO '. $this->versaoAtualDesteModulo .' DO '. $this->nomeDesteModulo .' REALIZADA COM SUCESSO NA BASE DO SIP');
            	$this->finalizar('FIM', false);
            }
            
            //se a versão instalada já é a atual, então não instala nada e avisa
            else {
                $this->logar('A VERSÃO MAIS ATUAL DO '. $this->nomeDesteModulo .' (v'. $this->versaoAtualDesteModulo .') JÁ ESTÁ INSTALADA.');
                $this->finalizar('FIM', false);
            }

            InfraDebug::getInstance()->setBolLigado(false);
            InfraDebug::getInstance()->setBolDebugInfra(false);
            InfraDebug::getInstance()->setBolEcho(false);

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

    private function renomearRecurso($numIdSistema, $strNomeAtual, $strNomeNovo){
        $objRecursoDTO = new RecursoDTO();
        $objRecursoDTO->setBolExclusaoLogica(false);
        $objRecursoDTO->retNumIdRecurso();
        $objRecursoDTO->retStrCaminho();
        $objRecursoDTO->setNumIdSistema($numIdSistema);
        $objRecursoDTO->setStrNome($strNomeAtual);

        $objRecursoRN = new RecursoRN();
        $objRecursoDTO = $objRecursoRN->consultar($objRecursoDTO);

        if ($objRecursoDTO!=null){
            $objRecursoDTO->setStrNome($strNomeNovo);
            $objRecursoDTO->setStrCaminho(str_replace($strNomeAtual,$strNomeNovo,$objRecursoDTO->getStrCaminho()));
            $objRecursoRN->alterar($objRecursoDTO);
        }
    }

    private function adicionarItemMenu($numIdSistema, $numIdPerfil, $numIdMenu, $numIdItemMenuPai, $numIdRecurso, $strRotulo, $numSequencia ){
/*
 *   	//add menu default
    	$objItemMenuIntimacaoTacita = $this->adicionarItemMenu($numIdSistemaSei,
    			$numIdPerfilSeiAdministrador,
    			$numIdMenuSei,
    			$objItemMenuDTOPeticionamentoEletronico->getNumIdItemMenu(),
    			'',
    			'Intimação Eletrônica',
    			70);
 * */
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

    $objVersaoRN = new MdPetAtualizadorSipRN();
    $objVersaoRN->atualizarVersao();

}catch(Exception $e){
    echo(nl2br(InfraException::inspecionar($e)));
    try{LogSip::getInstance()->gravar(InfraException::inspecionar($e));}catch(Exception $e){}
}

//========================== FIM SCRIPT EXECUÇÂO ====================
?>