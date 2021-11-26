<?
require_once dirname(__FILE__) . '/../web/Sip.php';

set_include_path(implode(PATH_SEPARATOR, array(
    realpath(__DIR__ . '/../../infra/infra_php'),
    get_include_path(),
)));


class MdPetAtualizadorSipRN extends InfraRN
{

    private $numSeg = 0;
    private $versaoAtualDesteModulo = '3.4.2';
    private $nomeDesteModulo = 'M�DULO DE PETICIONAMENTO E INTIMA��O ELETR�NICOS';
    private $nomeParametroModulo = 'VERSAO_MODULO_PETICIONAMENTO';
    private $historicoVersoes = array('0.0.1', '0.0.2', '1.0.3', '1.0.4', '1.1.0', '2.0.0', '2.0.1', '2.0.2', '2.0.3', '2.0.4', '2.0.5', '3.0.0', '3.0.1', '3.1.0', '3.2.0', '3.3.0', '3.4.0', '3.4.1', '3.4.2');

    public function __construct()
    {
        parent::__construct();
    }

    protected function inicializarObjInfraIBanco()
    {
        return BancoSip::getInstance();
    }

    /**
     * Inicia o script criando um contator interno do tempo de execu��o
     *
     * @return null
     */
    protected function inicializar($strTitulo)
    {
        session_start();
        SessaoSip::getInstance(false);

        ini_set('max_execution_time', '0');
        ini_set('memory_limit', '-1');
        @ini_set('zlib.output_compression', '0');
        @ini_set('implicit_flush', '1');
        ob_implicit_flush();

        $this->objDebug = InfraDebug::getInstance();
        $this->objDebug->setBolLigado(true);
        $this->objDebug->setBolDebugInfra(true);
        $this->objDebug->setBolEcho(true);
        $this->objDebug->limpar();

        $this->numSeg = InfraUtil::verificarTempoProcessamento();
        $this->logar($strTitulo);

    }

    protected function logar($strMsg)
    {
        $this->objDebug->gravar($strMsg);
        flush();
    }

    /**
     * Finaliza o script informando o tempo de execu��o.
     *
     * @return null
     */
    protected function finalizar($strMsg = null, $bolErro = false)
    {
        if (!$bolErro) {
            $this->numSeg = InfraUtil::verificarTempoProcessamento($this->numSeg);
            $this->logar('TEMPO TOTAL DE EXECU��O: ' . $this->numSeg . ' s');
        } else {
            $strMsg = 'ERRO: ' . $strMsg;
        }

        if ($strMsg != null) {
            $this->logar($strMsg);
        }

        InfraDebug::getInstance()->setBolLigado(false);
        InfraDebug::getInstance()->setBolDebugInfra(false);
        InfraDebug::getInstance()->setBolEcho(false);
        $this->numSeg = 0;
        die;
    }

    protected function atualizarVersaoConectado()
    {
        try {
            $this->inicializar('INICIANDO A INSTALA��O/ATUALIZA��O DO ' . $this->nomeDesteModulo . ' NO SIP VERS�O ' . SIP_VERSAO);

            //checando BDs suportados
            if (!(BancoSip::getInstance() instanceof InfraMySql) &&
                !(BancoSip::getInstance() instanceof InfraSqlServer) &&
                !(BancoSip::getInstance() instanceof InfraOracle)) {

                $this->finalizar('BANCO DE DADOS N�O SUPORTADO: ' . get_parent_class(BancoSip::getInstance()), true);
            }

            //testando versao do framework
            $numVersaoInfraRequerida = '1.532.3';
            $versaoInfraFormatada = (int)str_replace('.', '', VERSAO_INFRA);
            $versaoInfraReqFormatada = (int)str_replace('.', '', $numVersaoInfraRequerida);

            if ($versaoInfraFormatada < $versaoInfraReqFormatada) {
                $this->finalizar('VERS�O DO FRAMEWORK PHP INCOMPAT�VEL (VERS�O ATUAL ' . VERSAO_INFRA . ', SENDO REQUERIDA VERS�O IGUAL OU SUPERIOR A ' . $numVersaoInfraRequerida . ')', true);
            }


            //checando permissoes na base de dados
            $objInfraMetaBD = new InfraMetaBD(BancoSip::getInstance());

            if (count($objInfraMetaBD->obterTabelas('sip_teste')) == 0) {
                BancoSip::getInstance()->executarSql('CREATE TABLE sip_teste (id ' . $objInfraMetaBD->tipoNumero() . ' null)');
            }
            BancoSip::getInstance()->executarSql('DROP TABLE sip_teste');

            $objInfraParametro = new InfraParametro(BancoSip::getInstance());

            $strVersaoModuloPeticionamento = $objInfraParametro->getValor($this->nomeParametroModulo, false);

            switch ($strVersaoModuloPeticionamento) {
                //case '' - Nenhuma vers�o instalada
                case '':
                    $this->instalarv001();
                case '0.0.1':
                    $this->instalarv002();
                case '0.0.2':
                    $this->instalarv100();
                case '1.0.0':
                    $this->instalarv104();
                case '1.0.4':
                    $this->instalarv110();
                case '1.1.0':
                    $this->instalarv200();
                case '2.0.0':
                    $this->instalarv201();
                case '2.0.1':
                    $this->instalarv202();
                case '2.0.2':
                    $this->instalarv203();
                case '2.0.3':
                    $this->instalarv204();
                case '2.0.4':
                    $this->instalarv205();
                case '2.0.5':
                    $this->instalarv300();
                case '3.0.0':
                    $this->instalarv301();
                case '3.0.1':
                    $this->instalarv310();
                case '3.1.0':
                    $this->instalarv320();
                case '3.2.0':
                    $this->instalarv330();
                case '3.3.0':
                    $this->instalarv340();
                case '3.4.0':
                    $this->instalarv341();
                case '3.4.1':
                    $this->instalarv342();
                    break;

                default:
                    $this->finalizar('A VERS�O MAIS ATUAL DO ' . $this->nomeDesteModulo . ' (v' . $this->versaoAtualDesteModulo . ') J� EST� INSTALADA.');
                    break;

            }

            $this->finalizar('FIM');
            InfraDebug::getInstance()->setBolDebugInfra(true);
        } catch (Exception $e) {
            InfraDebug::getInstance()->setBolLigado(false);
            InfraDebug::getInstance()->setBolDebugInfra(false);
            InfraDebug::getInstance()->setBolEcho(false);
            throw new InfraException('Erro atualizando vers�o.', $e);
        }
    }

    protected function instalarv001()
    {

        $this->logar('EXECUTANDO A INSTALA��O/ATUALIZA��O DA VERS�O 0.0.1 DO ' . $this->nomeDesteModulo . ' NA BASE DO SIP');

        $objSistemaRN = new SistemaRN();
        $objPerfilRN = new PerfilRN();
        $objMenuRN = new MenuRN();
        $objItemMenuRN = new ItemMenuRN();
        $objRecursoRN = new RecursoRN();

        $objSistemaDTO = new SistemaDTO();
        $objSistemaDTO->retNumIdSistema();
        $objSistemaDTO->setStrSigla('SEI');

        $objSistemaDTO = $objSistemaRN->consultar($objSistemaDTO);

        if ($objSistemaDTO == null) {
            throw new InfraException('Sistema SEI n�o encontrado.');
        }

        $numIdSistemaSei = $objSistemaDTO->getNumIdSistema();

        $objPerfilDTO = new PerfilDTO();
        $objPerfilDTO->retNumIdPerfil();
        $objPerfilDTO->setNumIdSistema($numIdSistemaSei);
        $objPerfilDTO->setStrNome('Administrador');
        $objPerfilDTO = $objPerfilRN->consultar($objPerfilDTO);

        if ($objPerfilDTO == null) {
            throw new InfraException('Perfil Administrador do sistema SEI n�o encontrado.');
        }

        $numIdPerfilSeiAdministrador = $objPerfilDTO->getNumIdPerfil();

        $objPerfilDTO = new PerfilDTO();
        $objPerfilDTO->retNumIdPerfil();
        $objPerfilDTO->setNumIdSistema($numIdSistemaSei);
        $objPerfilDTO->setStrNome('Inform�tica');
        $objPerfilDTO = $objPerfilRN->consultar($objPerfilDTO);

        if ($objPerfilDTO == null) {
            throw new InfraException('Perfil Inform�tica do sistema SEI n�o encontrado.');
        }

        $numIdPerfilSeiInformatica = $objPerfilDTO->getNumIdPerfil();

        $objMenuDTO = new MenuDTO();
        $objMenuDTO->retNumIdMenu();
        $objMenuDTO->setNumIdSistema($numIdSistemaSei);
        $objMenuDTO->setStrNome('Principal');
        $objMenuDTO = $objMenuRN->consultar($objMenuDTO);

        if ($objMenuDTO == null) {
            throw new InfraException('Menu do sistema SEI n�o encontrado.');
        }

        $numIdMenuSei = $objMenuDTO->getNumIdMenu();

        $objItemMenuDTO = new ItemMenuDTO();
        $objItemMenuDTO->retNumIdItemMenu();
        $objItemMenuDTO->setNumIdSistema($numIdSistemaSei);
        $objItemMenuDTO->setStrRotulo('Administra��o');
        $objItemMenuDTO = $objItemMenuRN->consultar($objItemMenuDTO);

        if ($objItemMenuDTO == null) {
            throw new InfraException('Item de menu Administra��o do sistema SEI n�o encontrado.');
        }

        $numIdItemMenuSeiAdministracao = $objItemMenuDTO->getNumIdItemMenu();

        $objItemMenuDTO = new ItemMenuDTO();
        $objItemMenuDTO->retNumIdItemMenu();
        $objItemMenuDTO->setNumIdSistema($numIdSistemaSei);
        $objItemMenuDTO->setStrRotulo('Usu�rios');
        $objItemMenuDTO = $objItemMenuRN->consultar($objItemMenuDTO);

        if ($objItemMenuDTO == null) {
            throw new InfraException('Item de menu Administra��o/Usu�rios do sistema SEI n�o encontrado.');
        }

        $numIdItemMenuSeiUsuarios = $objItemMenuDTO->getNumIdItemMenu();


        $this->logar('ATUALIZANDO RECURSOS, MENUS E PERFIS DO ' . $this->nomeDesteModulo . ' NA BASE DO SIP...');


        $this->logar('CRIANDO e VINCULANDO RECURSO DE MENU A PERFIL - gerir extensao/tamanho de arquivo EM Administrador');
        $objExtensoesArquivosDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'gerir_extensoes_arquivo_peticionamento_cadastrar');
        $objTamanhoArquivoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'gerir_tamanho_arquivo_peticionamento_cadastrar');


        $this->logar('CRIANDO e VINCULANDO RECURSO DE MENU A PERFIL - Indisponibilidade EM Administrador');
        $objIndisponibilidadeListarDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'indisponibilidade_peticionamento_listar');


        $this->logar('CRIANDO e VINCULANDO RECURSO DE MENU A PERFIL - tipo processo peticionamento EM Administrador');
        $objTipoProcessoListarDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'tipo_processo_peticionamento_listar');


        $this->logar('CRIANDO e VINCULANDO RECURSO A PERFIL - gerir extensao/tamanho de arquivo EM Administrador');
        $objRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'arquivo_extensao_peticionamento_selecionar');
        $objRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'gerir_tamanho_arquivo_peticionamento_listar');
        $objRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'gerir_tamanho_arquivo_peticionamento_consultar');


        $this->logar('CRIANDO e VINCULANDO RECURSO A PERFIL - indisponibilidade EM Administrador');
        $objRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'indisponibilidade_peticionamento_desativar');
        $objRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'indisponibilidade_peticionamento_reativar');
        $objRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'indisponibilidade_peticionamento_excluir');
        $objRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'indisponibilidade_peticionamento_cadastrar');
        $objRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'indisponibilidade_peticionamento_consultar');
        $objRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'indisponibilidade_peticionamento_alterar');
        $objRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'indisponibilidade_peticionamento_upload_anexo');


        $this->logar('CRIANDO e VINCULANDO RECURSO A PERFIL - tipo processo peticionamento EM Administrador');
        $objRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'tipo_processo_peticionamento_desativar');
        $objRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'tipo_processo_peticionamento_reativar');
        $objRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'tipo_processo_peticionamento_excluir');
        $objRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'tipo_processo_peticionamento_cadastrar');
        $objRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'tipo_processo_peticionamento_alterar');
        $objRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'tipo_processo_peticionamento_consultar');
        $objRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'tipo_processo_peticionamento_salvar');
        $objRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'tipo_processo_peticionamento_cadastrar_orientacoes');


        $this->logar('CRIANDO e VINCULANDO RECURSO A PERFIL - indisponibilidade peticionamento download EM Administrador');
        $objRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'indisponibilidade_peticionamento_download');


        $this->logar('CRIANDO e VINCULANDO RECURSO A PERFIL - serie peticionamento selecionar EM Administrador');
        $objRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'serie_peticionamento_selecionar');


        $this->logar('CRIANDO e VINCULANDO ITEM MENU A PERFIL - Administra��o->Peticionamento Eletr�nico EM Administrador');
        $objItemMenuDTOPeticionamentoEletronico = $this->adicionarItemMenu($numIdSistemaSei, $numIdPerfilSeiAdministrador, $numIdMenuSei, $numIdItemMenuSeiAdministracao, null, 'Peticionamento Eletr�nico', 0);


        $this->logar('CRIANDO e VINCULANDO ITEM MENU A PERFIL - Administra��o->Peticionamento Eletr�nico->Tipos para Peticionamento EM Administrador');
        $this->adicionarItemMenu($numIdSistemaSei,
            $numIdPerfilSeiAdministrador,
            $numIdMenuSei,
            $objItemMenuDTOPeticionamentoEletronico->getNumIdItemMenu(),
            $objTipoProcessoListarDTO->getNumIdRecurso(),
            'Tipos para Peticionamento',
            10);


        $this->logar('CRIANDO e VINCULANDO ITEM MENU A PERFIL - Administra��o->Peticionamento Eletr�nico->Tamanho M�ximo de Arquivos EM Administrador');
        $this->adicionarItemMenu($numIdSistemaSei,
            $numIdPerfilSeiAdministrador,
            $numIdMenuSei,
            $objItemMenuDTOPeticionamentoEletronico->getNumIdItemMenu(),
            $objTamanhoArquivoDTO->getNumIdRecurso(),
            'Tamanho M�ximo de Arquivos',
            30);


        $this->logar('CRIANDO e VINCULANDO ITEM MENU A PERFIL - Administra��o->Peticionamento Eletr�nico->Extens�o de Arquivos Permitidos EM Administrador');
        $this->adicionarItemMenu($numIdSistemaSei,
            $numIdPerfilSeiAdministrador,
            $numIdMenuSei,
            $objItemMenuDTOPeticionamentoEletronico->getNumIdItemMenu(),
            $objExtensoesArquivosDTO->getNumIdRecurso(),
            'Extens�o de Arquivos Permitidos',
            40);


        $this->logar('CRIANDO e VINCULANDO ITEM MENU A PERFIL - Administra��o->Peticionamento Eletr�nico->Indisponibilidades do SEI EM Administrador');
        $this->adicionarItemMenu($numIdSistemaSei,
            $numIdPerfilSeiAdministrador,
            $numIdMenuSei,
            $objItemMenuDTOPeticionamentoEletronico->getNumIdItemMenu(),
            $objIndisponibilidadeListarDTO->getNumIdRecurso(),
            'Indisponibilidades do SEI',
            120);


        $this->logar('CRIANDO REGRA DE AUDITORIA PARA NOVOS RECURSOS RECEM ADICIONADOS');

        $objRegraAuditoriaDTO = new RegraAuditoriaDTO();
        $objRegraAuditoriaDTO->retNumIdRegraAuditoria();
        $objRegraAuditoriaDTO->setNumIdRegraAuditoria(null);
        $objRegraAuditoriaDTO->setStrSinAtivo('S');
        $objRegraAuditoriaDTO->setNumIdSistema($numIdSistemaSei);
        $objRegraAuditoriaDTO->setArrObjRelRegraAuditoriaRecursoDTO(array());
        $objRegraAuditoriaDTO->setStrDescricao('Modulo_Peticionamento_Eletronico');

        $objRegraAuditoriaRN = new RegraAuditoriaRN();
        $objRegraAuditoriaDTO = $objRegraAuditoriaRN->cadastrar($objRegraAuditoriaDTO);

        $rs = BancoSip::getInstance()->consultarSql('select id_recurso from recurso where id_sistema=' . $numIdSistemaSei . ' and nome in (
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
        foreach ($rs as $recurso) {
            BancoSip::getInstance()->executarSql('insert into rel_regra_auditoria_recurso (id_regra_auditoria, id_sistema, id_recurso) values (' . $objRegraAuditoriaDTO->getNumIdRegraAuditoria() . ', ' . $numIdSistemaSei . ', ' . $recurso['id_recurso'] . ')');
        }

        $objReplicacaoRegraAuditoriaDTO = new ReplicacaoRegraAuditoriaDTO();
        $objReplicacaoRegraAuditoriaDTO->setStrStaOperacao('A');
        $objReplicacaoRegraAuditoriaDTO->setNumIdRegraAuditoria($objRegraAuditoriaDTO->getNumIdRegraAuditoria());

        $objSistemaRN = new SistemaRN();
        $objSistemaRN->replicarRegraAuditoria($objReplicacaoRegraAuditoriaDTO);


        $this->logar('ADICIONANDO PAR�METRO ' . $this->nomeParametroModulo . ' NA TABELA infra_parametro PARA CONTROLAR A VERS�O DO M�DULO');
        BancoSip::getInstance()->executarSql('INSERT INTO infra_parametro (valor, nome ) VALUES( \'0.0.1\',  \'' . $this->nomeParametroModulo . '\' )');

        $this->logar('INSTALA��O/ATUALIZA��O DA VERS�O ' . $this->versaoAtualDesteModulo . ' DO ' . $this->nomeDesteModulo . ' REALIZADA COM SUCESSO NA BASE DO SIP');
    }

    protected function instalarv002()
    {

        $this->logar('EXECUTANDO A INSTALA��O/ATUALIZA��O DA VERS�O 0.0.2 DO ' . $this->nomeDesteModulo . ' NA BASE DO SIP');

        $objSistemaRN = new SistemaRN();
        $objPerfilRN = new PerfilRN();
        $objMenuRN = new MenuRN();
        $objItemMenuRN = new ItemMenuRN();
        $objRecursoRN = new RecursoRN();

        $objSistemaDTO = new SistemaDTO();
        $objSistemaDTO->retNumIdSistema();
        $objSistemaDTO->setStrSigla('SEI');

        $objSistemaDTO = $objSistemaRN->consultar($objSistemaDTO);

        if ($objSistemaDTO == null) {
            throw new InfraException('Sistema SEI n�o encontrado.');
        }

        $numIdSistemaSei = $objSistemaDTO->getNumIdSistema();

        $objPerfilDTO = new PerfilDTO();
        $objPerfilDTO->retNumIdPerfil();
        $objPerfilDTO->setNumIdSistema($numIdSistemaSei);
        $objPerfilDTO->setStrNome('Administrador');
        $objPerfilDTO = $objPerfilRN->consultar($objPerfilDTO);

        if ($objPerfilDTO == null) {
            throw new InfraException('Perfil Administrador do sistema SEI n�o encontrado.');
        }

        $numIdPerfilSeiAdministrador = $objPerfilDTO->getNumIdPerfil();

        $objPerfilDTO = new PerfilDTO();
        $objPerfilDTO->retNumIdPerfil();
        $objPerfilDTO->setNumIdSistema($numIdSistemaSei);
        $objPerfilDTO->setStrNome('Inform�tica');
        $objPerfilDTO = $objPerfilRN->consultar($objPerfilDTO);

        if ($objPerfilDTO == null) {
            throw new InfraException('Perfil Inform�tica do sistema SEI n�o encontrado.');
        }

        $numIdPerfilSeiInformatica = $objPerfilDTO->getNumIdPerfil();

        $objMenuDTO = new MenuDTO();
        $objMenuDTO->retNumIdMenu();
        $objMenuDTO->setNumIdSistema($numIdSistemaSei);
        $objMenuDTO->setStrNome('Principal');
        $objMenuDTO = $objMenuRN->consultar($objMenuDTO);

        if ($objMenuDTO == null) {
            throw new InfraException('Menu do sistema SEI n�o encontrado.');
        }

        $numIdMenuSei = $objMenuDTO->getNumIdMenu();

        $objItemMenuDTO = new ItemMenuDTO();
        $objItemMenuDTO->retNumIdItemMenu();
        $objItemMenuDTO->setNumIdSistema($numIdSistemaSei);
        $objItemMenuDTO->setStrRotulo('Administra��o');
        $objItemMenuDTO = $objItemMenuRN->consultar($objItemMenuDTO);

        if ($objItemMenuDTO == null) {
            throw new InfraException('Item de menu Administra��o do sistema SEI n�o encontrado.');
        }

        $numIdItemMenuSeiAdministracao = $objItemMenuDTO->getNumIdItemMenu();

        $objItemMenuDTO = new ItemMenuDTO();
        $objItemMenuDTO->retNumIdItemMenu();
        $objItemMenuDTO->setNumIdSistema($numIdSistemaSei);
        $objItemMenuDTO->setStrRotulo('Usu�rios');
        $objItemMenuDTO = $objItemMenuRN->consultar($objItemMenuDTO);

        if ($objItemMenuDTO == null) {
            throw new InfraException('Item de menu Administra��o/Usu�rios do sistema SEI n�o encontrado.');
        }

        $numIdItemMenuSeiUsuarios = $objItemMenuDTO->getNumIdItemMenu();

        $this->logar('ATUALIZANDO RECURSOS, MENUS E PERFIS DO ' . $this->nomeDesteModulo . ' NA BASE DO SIP...');


        $this->logar('CRIANDO e VINCULANDO RECURSO DE MENU A PERFIL - menu listar EM Administrador');
        $objMenuListarDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'menu_peticionamento_usuario_externo_listar');


        $this->logar('CRIANDO e VINCULANDO RECURSO DE MENU A PERFIL - tipo contexto EM Administrador');
        $objMenuTipoInteressadoPermitidoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'gerir_tipo_contexto_peticionamento_cadastrar');


        $this->logar('CRIANDO e VINCULANDO RECURSO A PERFIL - cadastro menu EM Administrador');
        $objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'menu_peticionamento_usuario_externo_desativar');
        $objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'menu_peticionamento_usuario_externo_reativar');
        $objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'menu_peticionamento_usuario_externo_excluir');
        $objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'menu_peticionamento_usuario_externo_cadastrar');
        $objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'menu_peticionamento_usuario_externo_consultar');
        $objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'menu_peticionamento_usuario_externo_alterar');


        $this->logar('RECUPERANDO MENU DE PETICIONAMENTO');
        $objItemMenuDTOPeticionamentoEletronico = new ItemMenuDTO();
        $objItemMenuDTOPeticionamentoEletronico->retNumIdItemMenu();
        $objItemMenuDTOPeticionamentoEletronico->setNumIdSistema($numIdSistemaSei);
        $objItemMenuDTOPeticionamentoEletronico->setStrRotulo('Peticionamento Eletr�nico');
        $objItemMenuDTOPeticionamentoEletronico = $objItemMenuRN->consultar($objItemMenuDTOPeticionamentoEletronico);


        $this->logar('CRIANDO e VINCULANDO ITEM MENU A PERFIL - Administra��o->Peticionamento Eletr�nico->Cadastro de Menus EM Administrador');
        $this->adicionarItemMenu($numIdSistemaSei,
            $numIdPerfilSeiAdministrador,
            $numIdMenuSei,
            $objItemMenuDTOPeticionamentoEletronico->getNumIdItemMenu(),
            $objMenuListarDTO->getNumIdRecurso(),
            'Cadastro de Menus',
            20);


        $this->logar('CRIANDO e VINCULANDO ITEM MENU A PERFIL - Administra��o->Peticionamento Eletr�nico->Cadastro de Menu EM Administrador');
        $this->adicionarItemMenu($numIdSistemaSei,
            $numIdPerfilSeiAdministrador,
            $numIdMenuSei,
            $objItemMenuDTOPeticionamentoEletronico->getNumIdItemMenu(),
            $objMenuTipoInteressadoPermitidoDTO->getNumIdRecurso(),
            'Tipos de Contatos Permitidos',
            50);


        $this->logar('CRIANDO REGRA DE AUDITORIA PARA NOVOS RECURSOS RECEM ADICIONADOS');

        $objRegraAuditoriaDTO = new RegraAuditoriaDTO();
        $objRegraAuditoriaDTO->retNumIdRegraAuditoria();
        $objRegraAuditoriaDTO->setNumIdSistema($numIdSistemaSei);
        $objRegraAuditoriaDTO->setStrDescricao('Modulo_Peticionamento_Eletronico');

        $objRegraAuditoriaRN = new RegraAuditoriaRN();
        $objRegraAuditoriaDTO = $objRegraAuditoriaRN->consultar($objRegraAuditoriaDTO);

        $rs = BancoSip::getInstance()->consultarSql('select id_recurso from recurso where id_sistema=' . $numIdSistemaSei . ' and nome in (
          \'gerir_tipo_contexto_peticionamento_cadastrar\',
          \'menu_peticionamento_usuario_externo_desativar\',
          \'menu_peticionamento_usuario_externo_reativar\',
          \'menu_peticionamento_usuario_externo_excluir\',
          \'menu_peticionamento_usuario_externo_cadastrar\',
          \'menu_peticionamento_usuario_externo_alterar\')'
        );

        //CRIANDO REGRA DE AUDITORIA PARA NOVOS RECURSOS RECEM ADICIONADOS
        foreach ($rs as $recurso) {
            BancoSip::getInstance()->executarSql('insert into rel_regra_auditoria_recurso (id_regra_auditoria, id_sistema, id_recurso) values (' . $objRegraAuditoriaDTO->getNumIdRegraAuditoria() . ', ' . $numIdSistemaSei . ', ' . $recurso['id_recurso'] . ')');
        }

        $objReplicacaoRegraAuditoriaDTO = new ReplicacaoRegraAuditoriaDTO();
        $objReplicacaoRegraAuditoriaDTO->setStrStaOperacao('A');
        $objReplicacaoRegraAuditoriaDTO->setNumIdRegraAuditoria($objRegraAuditoriaDTO->getNumIdRegraAuditoria());

        $objSistemaRN = new SistemaRN();
        $objSistemaRN->replicarRegraAuditoria($objReplicacaoRegraAuditoriaDTO);


        $this->logar('ATUALIZANDO PAR�METRO ' . $this->nomeParametroModulo . ' NA TABELA infra_parametro PARA CONTROLAR A VERS�O DO M�DULO');
        BancoSip::getInstance()->executarSql('UPDATE infra_parametro SET valor = \'0.0.2\' WHERE nome = \'' . $this->nomeParametroModulo . '\' ');

        $this->logar('INSTALA��O/ATUALIZA��O DA VERS�O ' . $this->versaoAtualDesteModulo . ' DO ' . $this->nomeDesteModulo . ' REALIZADA COM SUCESSO NA BASE DO SIP');
    }

    protected function instalarv100()
    {

        $this->logar('EXECUTANDO A INSTALA��O/ATUALIZA��O DA VERS�O 1.0.0 DO ' . $this->nomeDesteModulo . ' NA BASE DO SIP');

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

        if ($objSistemaDTO == null) {
            throw new InfraException('Sistema SEI n�o encontrado.');
        }

        $numIdSistemaSei = $objSistemaDTO->getNumIdSistema();

        $objPerfilDTO = new PerfilDTO();
        $objPerfilDTO->retNumIdPerfil();
        $objPerfilDTO->setNumIdSistema($numIdSistemaSei);
        $objPerfilDTO->setStrNome('Administrador');
        $objPerfilDTO = $objPerfilRN->consultar($objPerfilDTO);

        if ($objPerfilDTO == null) {
            throw new InfraException('Perfil Administrador do sistema SEI n�o encontrado.');
        }

        $numIdPerfilSeiAdministrador = $objPerfilDTO->getNumIdPerfil();

        $objPerfilDTO = new PerfilDTO();
        $objPerfilDTO->retNumIdPerfil();
        $objPerfilDTO->setNumIdSistema($numIdSistemaSei);
        $objPerfilDTO->setStrNome('Inform�tica');
        $objPerfilDTO = $objPerfilRN->consultar($objPerfilDTO);

        if ($objPerfilDTO == null) {
            throw new InfraException('Perfil Inform�tica do sistema SEI n�o encontrado.');
        }

        $numIdPerfilSeiInformatica = $objPerfilDTO->getNumIdPerfil();

        $objMenuDTO = new MenuDTO();
        $objMenuDTO->retNumIdMenu();
        $objMenuDTO->setNumIdSistema($numIdSistemaSei);
        $objMenuDTO->setStrNome('Principal');
        $objMenuDTO = $objMenuRN->consultar($objMenuDTO);

        if ($objMenuDTO == null) {
            throw new InfraException('Menu do sistema SEI n�o encontrado.');
        }

        $numIdMenuSei = $objMenuDTO->getNumIdMenu();

        $objItemMenuDTO = new ItemMenuDTO();
        $objItemMenuDTO->retNumIdItemMenu();
        $objItemMenuDTO->setNumIdSistema($numIdSistemaSei);
        $objItemMenuDTO->setStrRotulo('Administra��o');
        $objItemMenuDTO = $objItemMenuRN->consultar($objItemMenuDTO);

        if ($objItemMenuDTO == null) {
            throw new InfraException('Item de menu Administra��o do sistema SEI n�o encontrado.');
        }

        $numIdItemMenuSeiAdministracao = $objItemMenuDTO->getNumIdItemMenu();

        $objItemMenuDTO = new ItemMenuDTO();
        $objItemMenuDTO->retNumIdItemMenu();
        $objItemMenuDTO->setNumIdSistema($numIdSistemaSei);
        $objItemMenuDTO->setStrRotulo('Usu�rios');
        $objItemMenuDTO = $objItemMenuRN->consultar($objItemMenuDTO);

        if ($objItemMenuDTO == null) {
            throw new InfraException('Item de menu Administra��o/Usu�rios do sistema SEI n�o encontrado.');
        }

        $numIdItemMenuSeiUsuarios = $objItemMenuDTO->getNumIdItemMenu();

        $this->logar('ATUALIZANDO RECURSOS, MENUS E PERFIS DO ' . $this->nomeDesteModulo . ' NA BASE DO SIP...');


        $this->logar('CRIANDO e VINCULANDO RECURSO DE MENU A PERFIL - hipoteses legais permitidas EM Administrador');
        $objMenuListarDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'hipotese_legal_nl_acesso_peticionamento_cadastrar');


        $this->logar('CRIANDO e VINCULANDO RECURSO A PERFIL - hipoteses legais selecionar EM Administrador');
        $objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'hipotese_legal_peticionamento_selecionar');


        $this->logar('RECUPERANDO MENU DE PETICIONAMENTO');
        $objItemMenuDTOPeticionamentoEletronico = new ItemMenuDTO();
        $objItemMenuDTOPeticionamentoEletronico->retNumIdItemMenu();
        $objItemMenuDTOPeticionamentoEletronico->setNumIdSistema($numIdSistemaSei);
        $objItemMenuDTOPeticionamentoEletronico->setStrRotulo('Peticionamento Eletr�nico');
        $objItemMenuDTOPeticionamentoEletronico = $objItemMenuRN->consultar($objItemMenuDTOPeticionamentoEletronico);


        $this->logar('CRIANDO e VINCULANDO ITEM MENU A PERFIL - Administra��o->Peticionamento Eletr�nico->Hip�tese Legais Permitidas EM Administrador');
        $this->adicionarItemMenu($numIdSistemaSei,
            $numIdPerfilSeiAdministrador,
            $numIdMenuSei,
            $objItemMenuDTOPeticionamentoEletronico->getNumIdItemMenu(),
            $objMenuListarDTO->getNumIdRecurso(),
            'Hip�tese Legais Permitidas',
            60);


        $this->logar('CRIANDO REGRA DE AUDITORIA PARA NOVOS RECURSOS RECEM ADICIONADOS');
        $objRegraAuditoriaDTO = new RegraAuditoriaDTO();
        $objRegraAuditoriaDTO->retNumIdRegraAuditoria();
        $objRegraAuditoriaDTO->setNumIdSistema($numIdSistemaSei);
        $objRegraAuditoriaDTO->setStrDescricao('Modulo_Peticionamento_Eletronico');

        $objRegraAuditoriaRN = new RegraAuditoriaRN();
        $objRegraAuditoriaDTO = $objRegraAuditoriaRN->consultar($objRegraAuditoriaDTO);

        $rs = BancoSip::getInstance()->consultarSql('select id_recurso from recurso where id_sistema=' . $numIdSistemaSei . ' and nome in (
          \'hipotese_legal_nl_acesso_peticionamento_cadastrar\')'
        );

        //CRIANDO REGRA DE AUDITORIA PARA NOVOS RECURSOS RECEM ADICIONADOS
        foreach ($rs as $recurso) {
            BancoSip::getInstance()->executarSql('insert into rel_regra_auditoria_recurso (id_regra_auditoria, id_sistema, id_recurso) values (' . $objRegraAuditoriaDTO->getNumIdRegraAuditoria() . ', ' . $numIdSistemaSei . ', ' . $recurso['id_recurso'] . ')');
        }

        $objReplicacaoRegraAuditoriaDTO = new ReplicacaoRegraAuditoriaDTO();
        $objReplicacaoRegraAuditoriaDTO->setStrStaOperacao('A');
        $objReplicacaoRegraAuditoriaDTO->setNumIdRegraAuditoria($objRegraAuditoriaDTO->getNumIdRegraAuditoria());

        $objSistemaRN = new SistemaRN();
        $objSistemaRN->replicarRegraAuditoria($objReplicacaoRegraAuditoriaDTO);


        $this->logar('ATUALIZANDO PAR�METRO ' . $this->nomeParametroModulo . ' NA TABELA infra_parametro PARA CONTROLAR A VERS�O DO M�DULO');
        BancoSip::getInstance()->executarSql('UPDATE infra_parametro SET valor = \'1.0.0\' WHERE nome = \'' . $this->nomeParametroModulo . '\' ');

        $this->logar('INSTALA��O/ATUALIZA��O DA VERS�O ' . $this->versaoAtualDesteModulo . ' DO ' . $this->nomeDesteModulo . ' REALIZADA COM SUCESSO NA BASE DO SIP');
    }

    protected function instalarv104()
    {

        $this->logar('EXECUTANDO A INSTALA��O/ATUALIZA��O DA VERS�O 1.0.4 DO ' . $this->nomeDesteModulo . ' NA BASE DO SIP');

        $this->logar('ATUALIZANDO PAR�METRO ' . $this->nomeParametroModulo . ' NA TABELA infra_parametro PARA CONTROLAR A VERS�O DO M�DULO');
        BancoSip::getInstance()->executarSql('UPDATE infra_parametro SET valor = \'1.0.4\' WHERE nome = \'' . $this->nomeParametroModulo . '\' ');

        $this->logar('INSTALA��O/ATUALIZA��O DA VERS�O ' . $this->versaoAtualDesteModulo . ' DO ' . $this->nomeDesteModulo . ' REALIZADA COM SUCESSO NA BASE DO SIP');
    }

    protected function instalarv110()
    {

        $this->logar('EXECUTANDO A INSTALA��O/ATUALIZA��O DA VERS�O 1.1.0 DO ' . $this->nomeDesteModulo . ' NA BASE DO SIP');

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

        if ($objSistemaDTO == null) {
            throw new InfraException('Sistema SEI n�o encontrado.');
        }

        $numIdSistemaSei = $objSistemaDTO->getNumIdSistema();

        $objPerfilDTO = new PerfilDTO();
        $objPerfilDTO->retNumIdPerfil();
        $objPerfilDTO->setNumIdSistema($numIdSistemaSei);
        $objPerfilDTO->setStrNome('Administrador');
        $objPerfilDTO = $objPerfilRN->consultar($objPerfilDTO);

        if ($objPerfilDTO == null) {
            throw new InfraException('Perfil Administrador do sistema SEI n�o encontrado.');
        }

        $numIdPerfilSeiAdministrador = $objPerfilDTO->getNumIdPerfil();

        $objPerfilDTO = new PerfilDTO();
        $objPerfilDTO->retNumIdPerfil();
        $objPerfilDTO->setNumIdSistema($numIdSistemaSei);
        $objPerfilDTO->setStrNome('Inform�tica');
        $objPerfilDTO = $objPerfilRN->consultar($objPerfilDTO);

        if ($objPerfilDTO == null) {
            throw new InfraException('Perfil Inform�tica do sistema SEI n�o encontrado.');
        }

        $numIdPerfilSeiInformatica = $objPerfilDTO->getNumIdPerfil();

        $objMenuDTO = new MenuDTO();
        $objMenuDTO->retNumIdMenu();
        $objMenuDTO->setNumIdSistema($numIdSistemaSei);
        $objMenuDTO->setStrNome('Principal');
        $objMenuDTO = $objMenuRN->consultar($objMenuDTO);

        if ($objMenuDTO == null) {
            throw new InfraException('Menu do sistema SEI n�o encontrado.');
        }

        $numIdMenuSei = $objMenuDTO->getNumIdMenu();

        $objItemMenuDTO = new ItemMenuDTO();
        $objItemMenuDTO->retNumIdItemMenu();
        $objItemMenuDTO->setNumIdSistema($numIdSistemaSei);
        $objItemMenuDTO->setStrRotulo('Administra��o');
        $objItemMenuDTO = $objItemMenuRN->consultar($objItemMenuDTO);

        if ($objItemMenuDTO == null) {
            throw new InfraException('Item de menu Administra��o do sistema SEI n�o encontrado.');
        }

        $numIdItemMenuSeiAdministracao = $objItemMenuDTO->getNumIdItemMenu();

        $objItemMenuDTO = new ItemMenuDTO();
        $objItemMenuDTO->retNumIdItemMenu();
        $objItemMenuDTO->setNumIdSistema($numIdSistemaSei);
        $objItemMenuDTO->setStrRotulo('Usu�rios');
        $objItemMenuDTO = $objItemMenuRN->consultar($objItemMenuDTO);

        if ($objItemMenuDTO == null) {
            throw new InfraException('Item de menu Administra��o/Usu�rios do sistema SEI n�o encontrado.');
        }

        $numIdItemMenuSeiUsuarios = $objItemMenuDTO->getNumIdItemMenu();

        $this->logar('ATUALIZANDO RECURSOS, MENUS E PERFIS DO ' . $this->nomeDesteModulo . ' NA BASE DO SIP...');

        $this->logar('RENOMEANDO RECURSO DE MENU EM PERFIL - gerir extensao/tamanho de arquivo EM Administrador');
        $objDTO = $this->renomearRecurso($numIdSistemaSei, 'gerir_extensoes_arquivo_peticionamento_cadastrar', 'md_pet_extensoes_arquivo_cadastrar');
        $objDTO = $this->renomearRecurso($numIdSistemaSei, 'gerir_tamanho_arquivo_peticionamento_cadastrar', 'md_pet_tamanho_arquivo_cadastrar');


        $this->logar('RENOMEANDO RECURSO DE MENU EM PERFIL - Indisponibilidade EM Administrador');
        $objDTO = $this->renomearRecurso($numIdSistemaSei, 'indisponibilidade_peticionamento_listar', 'md_pet_indisponibilidade_listar');


        $this->logar('RENOMEANDO RECURSO DE MENU EM PERFIL - tipo processo peticionamento EM Administrador');
        $objDTO = $this->renomearRecurso($numIdSistemaSei, 'tipo_processo_peticionamento_listar', 'md_pet_tipo_processo_listar');

        //recursos que nao sao chamados em menus
        $this->logar('RENOMEANDO RECURSO A PERFIL - gerir extensao/tamanho de arquivo EM Administrador');
        $objDTO = $this->renomearRecurso($numIdSistemaSei, 'arquivo_extensao_peticionamento_selecionar', 'md_pet_arquivo_extensao_selecionar');
        $objDTO = $this->renomearRecurso($numIdSistemaSei, 'gerir_tamanho_arquivo_peticionamento_listar', 'md_pet_tamanho_arquivo_listar');
        $objDTO = $this->renomearRecurso($numIdSistemaSei, 'gerir_tamanho_arquivo_peticionamento_consultar', 'md_pet_tamanho_arquivo_consultar');


        $this->logar('RENOMEANDO RECURSO A PERFIL - indisponibilidade EM Administrador');
        $objDTO = $this->renomearRecurso($numIdSistemaSei, 'indisponibilidade_peticionamento_desativar', 'md_pet_indisponibilidade_desativar');
        $objDTO = $this->renomearRecurso($numIdSistemaSei, 'indisponibilidade_peticionamento_reativar', 'md_pet_indisponibilidade_reativar');
        $objDTO = $this->renomearRecurso($numIdSistemaSei, 'indisponibilidade_peticionamento_excluir', 'md_pet_indisponibilidade_excluir');
        $objDTO = $this->renomearRecurso($numIdSistemaSei, 'indisponibilidade_peticionamento_cadastrar', 'md_pet_indisponibilidade_cadastrar');
        $objDTO = $this->renomearRecurso($numIdSistemaSei, 'indisponibilidade_peticionamento_consultar', 'md_pet_indisponibilidade_consultar');
        $objDTO = $this->renomearRecurso($numIdSistemaSei, 'indisponibilidade_peticionamento_alterar', 'md_pet_indisponibilidade_alterar');
        $objDTO = $this->renomearRecurso($numIdSistemaSei, 'indisponibilidade_peticionamento_upload_anexo', 'md_pet_indisponibilidade_upload_anexo');


        $this->logar('RENOMEANDO RECURSO A PERFIL - tipo processo peticionamento EM Administrador');
        $objDTO = $this->renomearRecurso($numIdSistemaSei, 'tipo_processo_peticionamento_desativar', 'md_pet_tipo_processo_desativar');
        $objDTO = $this->renomearRecurso($numIdSistemaSei, 'tipo_processo_peticionamento_reativar', 'md_pet_tipo_processo_reativar');
        $objDTO = $this->renomearRecurso($numIdSistemaSei, 'tipo_processo_peticionamento_excluir', 'md_pet_tipo_processo_excluir');
        $objDTO = $this->renomearRecurso($numIdSistemaSei, 'tipo_processo_peticionamento_cadastrar', 'md_pet_tipo_processo_cadastrar');
        $objDTO = $this->renomearRecurso($numIdSistemaSei, 'tipo_processo_peticionamento_alterar', 'md_pet_tipo_processo_alterar');
        $objDTO = $this->renomearRecurso($numIdSistemaSei, 'tipo_processo_peticionamento_consultar', 'md_pet_tipo_processo_consultar');
        $objDTO = $this->renomearRecurso($numIdSistemaSei, 'tipo_processo_peticionamento_salvar', 'md_pet_tipo_processo_salvar');
        $objDTO = $this->renomearRecurso($numIdSistemaSei, 'tipo_processo_peticionamento_cadastrar_orientacoes', 'md_pet_tipo_processo_cadastrar_orientacoes');


        $this->logar('RENOMEANDO RECURSO EM PERFIL - indisponibilidade peticionamento download EM Administrador');
        $objDTO = $this->renomearRecurso($numIdSistemaSei, 'indisponibilidade_peticionamento_download', 'md_pet_indisponibilidade_download');


        $this->logar('RENOMEANDO RECURSO EM PERFIL - serie peticionamento selecionar EM Administrador');
        $objDTO = $this->renomearRecurso($numIdSistemaSei, 'serie_peticionamento_selecionar', 'md_pet_serie_selecionar');


        $this->logar('RENOMEANDO RECURSO DE MENU EM PERFIL - menu listar EM Administrador');
        $objDTO = $this->renomearRecurso($numIdSistemaSei, 'menu_peticionamento_usuario_externo_listar', 'md_pet_menu_usu_ext_listar');


        $this->logar('RENOMEANDO RECURSO DE MENU EM PERFIL - tipo contexto EM Administrador');
        $objDTO = $this->renomearRecurso($numIdSistemaSei, 'gerir_tipo_contexto_peticionamento_cadastrar', 'md_pet_tp_ctx_contato_cadastrar');


        $this->logar('RENOMEANDO RECURSO EM PERFIL - cadastro menu EM Administrador');
        $objDTO = $this->renomearRecurso($numIdSistemaSei, 'menu_peticionamento_usuario_externo_desativar', 'md_pet_menu_usu_ext_desativar');
        $objDTO = $this->renomearRecurso($numIdSistemaSei, 'menu_peticionamento_usuario_externo_reativar', 'md_pet_menu_usu_ext_reativar');
        $objDTO = $this->renomearRecurso($numIdSistemaSei, 'menu_peticionamento_usuario_externo_excluir', 'md_pet_menu_usu_ext_excluir');
        $objDTO = $this->renomearRecurso($numIdSistemaSei, 'menu_peticionamento_usuario_externo_cadastrar', 'md_pet_menu_usu_ext_cadastrar');
        $objDTO = $this->renomearRecurso($numIdSistemaSei, 'menu_peticionamento_usuario_externo_consultar', 'md_pet_menu_usu_ext_consultar');
        $objDTO = $this->renomearRecurso($numIdSistemaSei, 'menu_peticionamento_usuario_externo_alterar', 'md_pet_menu_usu_ext_alterar');


        $this->logar('RENOMEANDO RECURSO EM PERFIL - hipoteses legais permitidas EM Administrador');
        $objDTO = $this->renomearRecurso($numIdSistemaSei, 'hipotese_legal_nl_acesso_peticionamento_cadastrar', 'md_pet_hipotese_legal_nl_acesso_cadastrar');
        $objDTO = $this->renomearRecurso($numIdSistemaSei, 'hipotese_legal_peticionamento_selecionar', 'md_pet_hipotese_legal_selecionar');


        $this->logar('CRIANDO e VINCULANDO RECURSO A PERFIL - hipoteses legais selecionar EM Administrador');
        $objRecursoComMenuDTO1 = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'md_pet_intercorrente_criterio_listar');
        $objRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'md_pet_intercorrente_criterio_desativar');
        $objRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'md_pet_intercorrente_criterio_reativar');
        $objRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'md_pet_intercorrente_criterio_excluir');
        $objRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'md_pet_intercorrente_criterio_cadastrar');
        $objRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'md_pet_intercorrente_criterio_alterar');
        $objRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'md_pet_intercorrente_criterio_consultar');
        $objRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'md_pet_intercorrente_criterio_padrao');


        $this->logar('RECUPERANDO MENU DE PETICIONAMENTO');
        $objItemMenuDTOPeticionamentoEletronico = new ItemMenuDTO();
        $objItemMenuDTOPeticionamentoEletronico->retNumIdItemMenu();
        $objItemMenuDTOPeticionamentoEletronico->setNumIdSistema($numIdSistemaSei);
        $objItemMenuDTOPeticionamentoEletronico->setStrRotulo('Peticionamento Eletr�nico');
        $objItemMenuDTOPeticionamentoEletronico = $objItemMenuRN->consultar($objItemMenuDTOPeticionamentoEletronico);


        $this->logar('CRIANDO e VINCULANDO ITEM MENU A PERFIL - Administra��o->Peticionamento Eletr�nico->Crit�rios para Intercorrente EM Administrador');
        $this->adicionarItemMenu($numIdSistemaSei,
            $numIdPerfilSeiAdministrador,
            $numIdMenuSei,
            $objItemMenuDTOPeticionamentoEletronico->getNumIdItemMenu(),
            $objRecursoComMenuDTO1->getNumIdRecurso(),
            'Crit�rios para Intercorrente',
            70);


        $this->logar('CRIANDO REGRA DE AUDITORIA PARA NOVOS RECURSOS RECEM ADICIONADOS');
        $objRegraAuditoriaDTO = new RegraAuditoriaDTO();
        $objRegraAuditoriaDTO->retNumIdRegraAuditoria();
        $objRegraAuditoriaDTO->setNumIdSistema($numIdSistemaSei);
        $objRegraAuditoriaDTO->setStrDescricao('Modulo_Peticionamento_Eletronico');

        $objRegraAuditoriaRN = new RegraAuditoriaRN();
        $objRegraAuditoriaDTO = $objRegraAuditoriaRN->consultar($objRegraAuditoriaDTO);

        $rs = BancoSip::getInstance()->consultarSql('select id_recurso from recurso where id_sistema=' . $numIdSistemaSei . ' and nome in (
          \'criterio_intercorrente_peticionamento_listar\')'
        );

        //CRIANDO REGRA DE AUDITORIA PARA NOVOS RECURSOS RECEM ADICIONADOS
        foreach ($rs as $recurso) {
            BancoSip::getInstance()->executarSql('insert into rel_regra_auditoria_recurso (id_regra_auditoria, id_sistema, id_recurso) values (' . $objRegraAuditoriaDTO->getNumIdRegraAuditoria() . ', ' . $numIdSistemaSei . ', ' . $recurso['id_recurso'] . ')');
        }

        $objReplicacaoRegraAuditoriaDTO = new ReplicacaoRegraAuditoriaDTO();
        $objReplicacaoRegraAuditoriaDTO->setStrStaOperacao('A');
        $objReplicacaoRegraAuditoriaDTO->setNumIdRegraAuditoria($objRegraAuditoriaDTO->getNumIdRegraAuditoria());

        $objSistemaRN = new SistemaRN();
        $objSistemaRN->replicarRegraAuditoria($objReplicacaoRegraAuditoriaDTO);


        $this->logar('ATUALIZANDO PAR�METRO ' . $this->nomeParametroModulo . ' NA TABELA infra_parametro PARA CONTROLAR A VERS�O DO M�DULO');
        BancoSip::getInstance()->executarSql('UPDATE infra_parametro SET valor = \'1.1.0\' WHERE nome = \'' . $this->nomeParametroModulo . '\' ');

        $this->logar('INSTALA��O/ATUALIZA��O DA VERS�O ' . $this->versaoAtualDesteModulo . ' DO ' . $this->nomeDesteModulo . ' REALIZADA COM SUCESSO NA BASE DO SIP');
    }

    protected function instalarv200()
    {

        $this->logar('EXECUTANDO A INSTALA��O/ATUALIZA��O DA VERS�O 2.0.0 DO ' . $this->nomeDesteModulo . ' NA BASE DO SIP');
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

        if ($objSistemaDTO == null) {
            throw new InfraException('Sistema SEI n�o encontrado.');
        }

        $numIdSistemaSei = $objSistemaDTO->getNumIdSistema();

        $objPerfilDTO = new PerfilDTO();
        $objPerfilDTO->retNumIdPerfil();
        $objPerfilDTO->setNumIdSistema($numIdSistemaSei);
        $objPerfilDTO->setStrNome('Administrador');
        $objPerfilDTO = $objPerfilRN->consultar($objPerfilDTO);

        if ($objPerfilDTO == null) {
            throw new InfraException('Perfil Administrador do sistema SEI n�o encontrado.');
        }

        $numIdPerfilSeiAdministrador = $objPerfilDTO->getNumIdPerfil();

        $objPerfilBasicoDTO = new PerfilDTO();
        $objPerfilBasicoDTO->retNumIdPerfil();
        $objPerfilBasicoDTO->setNumIdSistema($numIdSistemaSei);
        $objPerfilBasicoDTO->setStrNome('B�sico');
        $objPerfilBasicoDTO = $objPerfilRN->consultar($objPerfilBasicoDTO);

        if ($objPerfilBasicoDTO == null) {
            throw new InfraException('Perfil B�sico do sistema SEI n�o encontrado.');
        }

        $numIdPerfilSeiBasico = $objPerfilBasicoDTO->getNumIdPerfil();

        $objMenuDTO = new MenuDTO();
        $objMenuDTO->retNumIdMenu();
        $objMenuDTO->setNumIdSistema($numIdSistemaSei);
        $objMenuDTO->setStrNome('Principal');
        $objMenuDTO = $objMenuRN->consultar($objMenuDTO);

        if ($objMenuDTO == null) {
            throw new InfraException('Menu do sistema SEI n�o encontrado.');
        }

        $numIdMenuSei = $objMenuDTO->getNumIdMenu();

        $objItemMenuDTO = new ItemMenuDTO();
        $objItemMenuDTO->retNumIdItemMenu();
        $objItemMenuDTO->setNumIdSistema($numIdSistemaSei);
        $objItemMenuDTO->setStrRotulo('Administra��o');
        $objItemMenuDTO = $objItemMenuRN->consultar($objItemMenuDTO);

        if ($objItemMenuDTO == null) {
            throw new InfraException('Item de menu Administra��o do sistema SEI n�o encontrado.');
        }

        $this->logar('ATUALIZANDO RECURSOS, MENUS E PERFIS DO ' . $this->nomeDesteModulo . ' NA BASE DO SIP...');


        $this->logar('RECUPERANDO MENU DE PETICIONAMENTO');
        $objItemMenuDTOPeticionamentoEletronico = new ItemMenuDTO();
        $objItemMenuDTOPeticionamentoEletronico->retNumIdItemMenu();
        $objItemMenuDTOPeticionamentoEletronico->setNumIdSistema($numIdSistemaSei);
        $objItemMenuDTOPeticionamentoEletronico->setStrRotulo('Peticionamento Eletr�nico');
        $objItemMenuDTOPeticionamentoEletronico = $objItemMenuRN->consultar($objItemMenuDTOPeticionamentoEletronico);


        $this->logar('CRIANDO e VINCULANDO ITEM MENU A PERFIL - Administra��o->Peticionamento Eletr�nico->Intima��o Eletr�nica EM Administrador');
        $objItemMenuIntimacaoTacita = $this->adicionarItemMenu($numIdSistemaSei,
            $numIdPerfilSeiAdministrador,
            $numIdMenuSei,
            $objItemMenuDTOPeticionamentoEletronico->getNumIdItemMenu(),
            '',
            'Intima��o Eletr�nica',
            80);


        $this->logar('CRIANDO e VINCULANDO RECURSO A PERFIL - prazo tacito EM Administrador');
        $objMenuListarDTO1 = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'md_pet_int_prazo_tacita_alterar');
        $objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'md_pet_int_prazo_tacita_cadastrar');


        $this->logar('CRIANDO e VINCULANDO RECURSO A PERFIL - prazo tacito EM B�sico');
        $objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiBasico, 'md_pet_int_prazo_tacita_consultar');
        $objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiBasico, 'md_pet_int_prazo_tacita_listar');


        $this->logar('CRIANDO e VINCULANDO RECURSO A PERFIL - tipo documento EM Administrador');
        $objMenuListarDTO3 = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'md_pet_int_serie_cadastrar');
        $objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'md_pet_int_serie_alterar');
        $objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'md_pet_int_serie_excluir');
        $objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'md_pet_int_serie_desativar');
        $objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'md_pet_int_serie_reativar');


        $this->logar('CRIANDO e VINCULANDO RECURSO A PERFIL - tipo documento EM B�sico');
        $objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiBasico, 'md_pet_int_serie_listar');
        $objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiBasico, 'md_pet_int_serie_selecionar');


        $this->logar('CRIANDO e VINCULANDO RECURSO A PERFIL - tipo resposta EM Administrador');
        $objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'md_pet_int_tipo_resp_cadastrar');
        $objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'md_pet_int_tipo_resp_alterar');
        $objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'md_pet_int_tipo_resp_listar');
        $objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'md_pet_int_tipo_resp_desativar');
        $objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'md_pet_int_tipo_resp_reativar');
        $objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'md_pet_int_tipo_resp_excluir');
        $objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'md_pet_int_tipo_resp_selecionar');


        $this->logar('CRIANDO e VINCULANDO RECURSO A PERFIL - tipo resposta EM B�sico');
        $objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiBasico, 'md_pet_int_tipo_resp_consultar');
        $objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiBasico, 'md_pet_int_tipo_intimacao_selecionar');

        $this->logar('CRIANDO e VINCULANDO RECURSO A PERFIL - tipo intimacao EM Administrador');
        $objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'md_pet_int_tipo_intimacao_cadastrar');
        $objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'md_pet_int_tipo_intimacao_alterar');
        $objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'md_pet_int_tipo_intimacao_desativar');
        $objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'md_pet_int_tipo_intimacao_reativar');
        $objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'md_pet_int_tipo_intimacao_excluir');


        $this->logar('CRIANDO e VINCULANDO RECURSO A PERFIL - tipo intimacao EM B�sico');
        $objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiBasico, 'md_pet_int_tipo_intimacao_consultar');
        $objMenuListarDTO2 = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiBasico, 'md_pet_int_tipo_intimacao_listar');


        $this->logar('CRIANDO e VINCULANDO RECURSO A PERFIL - rel intimacao X resposta EM B�sico');
        $objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiBasico, 'md_pet_int_rel_intim_resp_cadastrar');
        $objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiBasico, 'md_pet_int_rel_intim_resp_alterar');
        $objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiBasico, 'md_pet_int_rel_intim_resp_excluir');
        $objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiBasico, 'md_pet_int_rel_intim_resp_consultar');
        $objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiBasico, 'md_pet_int_rel_intim_resp_listar');
        $objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiBasico, 'md_pet_int_rel_intim_resp_desativar');
        $objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiBasico, 'md_pet_int_rel_intim_resp_reativar');


        $this->logar('CRIANDO e VINCULANDO RECURSO A PERFIL - cadastro destinatario EM B�sico');
        $objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiBasico, 'md_pet_int_rel_destinatario_cadastrar');
        $objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiBasico, 'md_pet_int_rel_destinatario_alterar');
        $objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiBasico, 'md_pet_int_rel_destinatario_excluir');
        $objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiBasico, 'md_pet_int_rel_destinatario_listar');
        $objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiBasico, 'md_pet_int_rel_destinatario_consultar');
        $objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiBasico, 'md_pet_int_rel_destinatario_desativar');
        $objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiBasico, 'md_pet_int_rel_destinatario_reativar');


        $this->logar('CRIANDO e VINCULANDO RECURSO A PERFIL - gerar intimacao + listar intima�ao EM B�sico');
        $objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiBasico, 'md_pet_intimacao_cadastrar');
        $objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiBasico, 'md_pet_intimacao_consultar');
        $objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiBasico, 'md_pet_intimacao_listar');
        $objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiBasico, 'md_pet_intimacao_eletronica_listar');
        $objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiBasico, 'md_pet_int_dest_resposta_listar');


        $this->logar('CRIANDO e VINCULANDO RECURSO A PERFIL - aceitar + consultar intimacao EM B�sico');
        $objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiBasico, 'md_pet_intimacao_usu_ext_confirmar_aceite');


        $this->logar('CRIANDO e VINCULANDO RECURSO A PERFIL - responder intima�ao EM B�sico');
        $objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiBasico, 'md_pet_responder_intimacao_usu_ext');


        $this->logar('CRIANDO e VINCULANDO RECURSO A PERFIL - rel tipo_resp x intimacao EM B�sico');
        $objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiBasico, 'md_pet_int_rel_tipo_resp_cadastrar');
        $objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiBasico, 'md_pet_int_rel_tipo_resp_alterar');
        $objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiBasico, 'md_pet_int_rel_tipo_resp_excluir');
        $objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiBasico, 'md_pet_int_rel_tipo_resp_listar');
        $objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiBasico, 'md_pet_int_rel_tipo_resp_consultar');
        $objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiBasico, 'md_pet_int_rel_tipo_resp_reativar');
        $objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiBasico, 'md_pet_int_rel_tipo_resp_desativar');


        $this->logar('CRIANDO e VINCULANDO RECURSO A PERFIL - documento intimacao EM B�sico');
        $objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiBasico, 'md_pet_int_documento_cadastrar');
        $objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiBasico, 'md_pet_int_documento_alterar');
        $objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiBasico, 'md_pet_int_documento_excluir');
        $objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiBasico, 'md_pet_int_documento_reativar');
        $objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiBasico, 'md_pet_int_documento_desativar');
        $objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiBasico, 'md_pet_int_documento_consultar');
        $objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiBasico, 'md_pet_int_documento_listar');


        $this->logar('CRIANDO e VINCULANDO RECURSO A PERFIL - documento disponivel intimacao EM B�sico');
        $objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiBasico, 'md_pet_int_doc_disponivel_cadastrar');
        $objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiBasico, 'md_pet_int_doc_disponivel_alterar');
        $objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiBasico, 'md_pet_int_doc_disponivel_excluir');
        $objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiBasico, 'md_pet_int_doc_disponivel_reativar');
        $objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiBasico, 'md_pet_int_doc_disponivel_desativar');
        $objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiBasico, 'md_pet_int_doc_disponivel_consultar');
        $objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiBasico, 'md_pet_int_doc_disponivel_listar');


        $this->logar('CRIANDO e VINCULANDO RECURSO A PERFIL - aceite intimacao EM B�sico');
        $objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiBasico, 'md_pet_int_aceite_cadastrar');
        $objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiBasico, 'md_pet_int_aceite_consultar');
        $objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiBasico, 'md_pet_int_aceite_listar');
        $objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiBasico, 'md_pet_int_aceite_alterar');


        $this->logar('CRIANDO e VINCULANDO ITEM MENU A PERFIL - Administra��o->Peticionamento Eletr�nico->Intima��o Eletr�nica->Prazo para Intima��o T�cita EM Administrador');
        $this->adicionarItemMenu($numIdSistemaSei,
            $numIdPerfilSeiAdministrador,
            $numIdMenuSei,
            $objItemMenuIntimacaoTacita->getNumIdItemMenu(),
            $objMenuListarDTO1->getNumIdRecurso(),
            'Prazo para Intima��o T�cita',
            10);


        $this->logar('CRIANDO e VINCULANDO ITEM MENU A PERFIL - Administra��o->Peticionamento Eletr�nico->Intima��o Eletr�nica->Tipos de Intima��o Eletr�nica EM Administrador');
        $this->adicionarItemMenu($numIdSistemaSei,
            $numIdPerfilSeiAdministrador,
            $numIdMenuSei,
            $objItemMenuIntimacaoTacita->getNumIdItemMenu(),
            $objMenuListarDTO2->getNumIdRecurso(),
            'Tipos de Intima��o Eletr�nica',
            20);


        $this->logar('CRIANDO e VINCULANDO ITEM MENU A PERFIL - Administra��o->Peticionamento Eletr�nico->Intima��o Eletr�nica->Tipos de Documentos para Intima��o EM Administrador');
        $this->adicionarItemMenu($numIdSistemaSei,
            $numIdPerfilSeiAdministrador,
            $numIdMenuSei,
            $objItemMenuIntimacaoTacita->getNumIdItemMenu(),
            $objMenuListarDTO3->getNumIdRecurso(),
            'Tipos de Documentos para Intima��o',
            30);


        $this->logar('RECUPERANDO MENU DE RELAT�RIOS');
        $objItemMenuDTORelatorioDTO = new ItemMenuDTO();
        $objItemMenuDTORelatorioDTO->retNumIdItemMenu();
        $objItemMenuDTORelatorioDTO->setNumIdSistema($numIdSistemaSei);
        $objItemMenuDTORelatorioDTO->setStrRotulo('Relat�rios');
        $objItemMenuDTORelatorioDTO = $objItemMenuRN->consultar($objItemMenuDTORelatorioDTO);


        $this->logar('CRIANDO e VINCULANDO RECURSO A PERFIL - Relatorio EM B�sico');
        $objItemRecursoIntRelaListarDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiBasico, 'md_pet_int_relatorio_listar');
        $objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiBasico, 'md_pet_int_relatorio_ht_listar');
        $objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiBasico, 'md_pet_int_relatorio_exp_excel');


        $this->logar('CRIANDO e VINCULANDO ITEM MENU A PERFIL - Relat�rios->Intima��es Eletr�nicas EM B�sico');
        $this->adicionarItemMenu($numIdSistemaSei,
            $numIdPerfilSeiBasico,
            $numIdMenuSei,
            $objItemMenuDTORelatorioDTO->getNumIdItemMenu(),
            $objItemRecursoIntRelaListarDTO->getNumIdRecurso(),
            'Intima��es Eletr�nicas',
            30);


        $this->logar('CRIANDO REGRA DE AUDITORIA PARA NOVOS RECURSOS RECEM ADICIONADOS');
        $objRegraAuditoriaDTO = new RegraAuditoriaDTO();
        $objRegraAuditoriaDTO->retNumIdRegraAuditoria();
        $objRegraAuditoriaDTO->setNumIdSistema($numIdSistemaSei);
        $objRegraAuditoriaDTO->setStrDescricao('Modulo_Peticionamento_Eletronico');

        $objRegraAuditoriaRN = new RegraAuditoriaRN();
        $objRegraAuditoriaDTO = $objRegraAuditoriaRN->consultar($objRegraAuditoriaDTO);

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

        $rs = BancoSip::getInstance()->consultarSql('select id_recurso from recurso where id_sistema=' . $numIdSistemaSei . ' and nome in (
          ' . implode(', ', $arrAuditoria) . ')'
        );

        //CRIANDO REGRA DE AUDITORIA PARA NOVOS RECURSOS RECEM ADICIONADOS
        foreach ($rs as $recurso) {
            BancoSip::getInstance()->executarSql('insert into rel_regra_auditoria_recurso (id_regra_auditoria, id_sistema, id_recurso) values (' . $objRegraAuditoriaDTO->getNumIdRegraAuditoria() . ', ' . $numIdSistemaSei . ', ' . $recurso['id_recurso'] . ')');
        }

        $objReplicacaoRegraAuditoriaDTO = new ReplicacaoRegraAuditoriaDTO();
        $objReplicacaoRegraAuditoriaDTO->setStrStaOperacao('A');
        $objReplicacaoRegraAuditoriaDTO->setNumIdRegraAuditoria($objRegraAuditoriaDTO->getNumIdRegraAuditoria());

        $objSistemaRN = new SistemaRN();
        $objSistemaRN->replicarRegraAuditoria($objReplicacaoRegraAuditoriaDTO);


        $this->logar('ATUALIZANDO PAR�METRO ' . $this->nomeParametroModulo . ' NA TABELA infra_parametro PARA CONTROLAR A VERS�O DO M�DULO');
        BancoSip::getInstance()->executarSql('UPDATE infra_parametro SET valor = \'2.0.0\' WHERE nome = \'' . $this->nomeParametroModulo . '\' ');

        $this->logar('INSTALA��O/ATUALIZA��O DA VERS�O ' . $this->versaoAtualDesteModulo . ' DO ' . $this->nomeDesteModulo . ' REALIZADA COM SUCESSO NA BASE DO SIP');
    }

    protected function instalarv201()
    {

        $this->logar('EXECUTANDO A INSTALA��O/ATUALIZA��O DA VERS�O 2.0.1 DO ' . $this->nomeDesteModulo . ' NA BASE DO SIP');

        $this->logar('ATUALIZANDO PAR�METRO ' . $this->nomeParametroModulo . ' NA TABELA infra_parametro PARA CONTROLAR A VERS�O DO M�DULO');
        BancoSip::getInstance()->executarSql('UPDATE infra_parametro SET valor = \'2.0.1\' WHERE nome = \'' . $this->nomeParametroModulo . '\' ');

        $this->logar('INSTALA��O/ATUALIZA��O DA VERS�O ' . $this->versaoAtualDesteModulo . ' DO ' . $this->nomeDesteModulo . ' REALIZADA COM SUCESSO NA BASE DO SIP');
    }

    protected function instalarv202()
    {

        $this->logar('EXECUTANDO A INSTALA��O/ATUALIZA��O DA VERS�O 2.0.2 DO ' . $this->nomeDesteModulo . ' NA BASE DO SIP');

        $this->logar('ATUALIZANDO PAR�METRO ' . $this->nomeParametroModulo . ' NA TABELA infra_parametro PARA CONTROLAR A VERS�O DO M�DULO');
        BancoSip::getInstance()->executarSql('UPDATE infra_parametro SET valor = \'2.0.2\' WHERE nome = \'' . $this->nomeParametroModulo . '\' ');

        $this->logar('INSTALA��O/ATUALIZA��O DA VERS�O ' . $this->versaoAtualDesteModulo . ' DO ' . $this->nomeDesteModulo . ' REALIZADA COM SUCESSO NA BASE DO SIP');
    }

    protected function instalarv203()
    {

        $this->logar('EXECUTANDO A INSTALA��O/ATUALIZA��O DA VERS�O 2.0.3 DO ' . $this->nomeDesteModulo . ' NA BASE DO SIP');

        $this->logar('ATUALIZANDO PAR�METRO ' . $this->nomeParametroModulo . ' NA TABELA infra_parametro PARA CONTROLAR A VERS�O DO M�DULO');
        BancoSip::getInstance()->executarSql('UPDATE infra_parametro SET valor = \'2.0.3\' WHERE nome = \'' . $this->nomeParametroModulo . '\' ');

        $this->logar('INSTALA��O/ATUALIZA��O DA VERS�O ' . $this->versaoAtualDesteModulo . ' DO ' . $this->nomeDesteModulo . ' REALIZADA COM SUCESSO NA BASE DO SIP');
    }

    protected function instalarv204()
    {

        $this->logar('EXECUTANDO A INSTALA��O/ATUALIZA��O DA VERS�O 2.0.4 DO ' . $this->nomeDesteModulo . ' NA BASE DO SIP');

        $this->logar('ATUALIZANDO PAR�METRO ' . $this->nomeParametroModulo . ' NA TABELA infra_parametro PARA CONTROLAR A VERS�O DO M�DULO');
        BancoSip::getInstance()->executarSql('UPDATE infra_parametro SET valor = \'2.0.4\' WHERE nome = \'' . $this->nomeParametroModulo . '\' ');

        $this->logar('INSTALA��O/ATUALIZA��O DA VERS�O ' . $this->versaoAtualDesteModulo . ' DO ' . $this->nomeDesteModulo . ' REALIZADA COM SUCESSO NA BASE DO SIP');
    }

    protected function instalarv205()
    {

        $this->logar('EXECUTANDO A INSTALA��O/ATUALIZA��O DA VERS�O 2.0.5 DO ' . $this->nomeDesteModulo . ' NA BASE DO SIP');

        $this->logar('ATUALIZANDO PAR�METRO ' . $this->nomeParametroModulo . ' NA TABELA infra_parametro PARA CONTROLAR A VERS�O DO M�DULO');
        BancoSip::getInstance()->executarSql('UPDATE infra_parametro SET valor = \'2.0.5\' WHERE nome = \'' . $this->nomeParametroModulo . '\' ');

        $this->logar('INSTALA��O/ATUALIZA��O DA VERS�O ' . $this->versaoAtualDesteModulo . ' DO ' . $this->nomeDesteModulo . ' REALIZADA COM SUCESSO NA BASE DO SIP');
    }

    protected function instalarv300()
    {

        $this->logar('EXECUTANDO A INSTALA��O/ATUALIZA��O DA VERS�O 3.0.0 DO ' . $this->nomeDesteModulo . ' NA BASE DO SIP');

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

        if ($objSistemaDTO == null) {
            throw new InfraException('Sistema SEI n�o encontrado.');
        }

        $numIdSistemaSei = $objSistemaDTO->getNumIdSistema();

        $objPerfilDTO = new PerfilDTO();
        $objPerfilDTO->retNumIdPerfil();
        $objPerfilDTO->setNumIdSistema($numIdSistemaSei);
        $objPerfilDTO->setStrNome('Administrador');
        $objPerfilDTO = $objPerfilRN->consultar($objPerfilDTO);

        if ($objPerfilDTO == null) {
            throw new InfraException('Perfil Administrador do sistema SEI n�o encontrado.');
        }

        $numIdPerfilSeiAdministrador = $objPerfilDTO->getNumIdPerfil();

        $objPerfilBasicoDTO = new PerfilDTO();
        $objPerfilBasicoDTO->retNumIdPerfil();
        $objPerfilBasicoDTO->setNumIdSistema($numIdSistemaSei);
        $objPerfilBasicoDTO->setStrNome('B�sico');
        $objPerfilBasicoDTO = $objPerfilRN->consultar($objPerfilBasicoDTO);

        if ($objPerfilBasicoDTO == null) {
            throw new InfraException('Perfil B�sico do sistema SEI n�o encontrado.');
        }

        $numIdPerfilSeiBasico = $objPerfilBasicoDTO->getNumIdPerfil();

        $objPerfilColaboradorBasicoDTO = new PerfilDTO();
        $objPerfilColaboradorBasicoDTO->retNumIdPerfil();
        $objPerfilColaboradorBasicoDTO->setNumIdSistema($numIdSistemaSei);
        $objPerfilColaboradorBasicoDTO->setStrNome('Colaborador (B�sico sem Assinatura)');
        $objPerfilColaboradorBasicoDTO = $objPerfilRN->consultar($objPerfilColaboradorBasicoDTO);

        //if ($objPerfilColaboradorBasicoDTO== null){
        //    throw new InfraException('Perfil Colaborador B�sico do sistema SEI n�o encontrado.');
        //}
        if ($objPerfilColaboradorBasicoDTO != null) {
            $numIdPerfilSeiColaboradorBasico = $objPerfilColaboradorBasicoDTO->getNumIdPerfil();
        }

        $objMenuDTO = new MenuDTO();
        $objMenuDTO->retNumIdMenu();
        $objMenuDTO->setNumIdSistema($numIdSistemaSei);
        $objMenuDTO->setStrNome('Principal');
        $objMenuDTO = $objMenuRN->consultar($objMenuDTO);

        if ($objMenuDTO == null) {
            throw new InfraException('Menu do sistema SEI n�o encontrado.');
        }

        $numIdMenuSei = $objMenuDTO->getNumIdMenu();

        $objItemMenuDTO = new ItemMenuDTO();
        $objItemMenuDTO->retNumIdItemMenu();
        $objItemMenuDTO->setNumIdSistema($numIdSistemaSei);
        $objItemMenuDTO->setStrRotulo('Administra��o');
        $objItemMenuDTO = $objItemMenuRN->consultar($objItemMenuDTO);

        if ($objItemMenuDTO == null) {
            throw new InfraException('Item de menu Administra��o do sistema SEI n�o encontrado.');
        }

        $this->logar('ATUALIZANDO RECURSOS, MENUS E PERFIS DO MODULO ' . $this->nomeDesteModulo . ' NA BASE DO SIP...');

        //recupera o ID do menu Peticionamento Eletronico
        $objItemMenuDTOPeticionamentoEletronico = new ItemMenuDTO();
        $objItemMenuDTOPeticionamentoEletronico->retNumIdItemMenu();
        $objItemMenuDTOPeticionamentoEletronico->setNumIdSistema($numIdSistemaSei);
        $objItemMenuDTOPeticionamentoEletronico->setStrRotulo('Peticionamento Eletr�nico');
        $objItemMenuDTOPeticionamentoEletronico = $objItemMenuRN->consultar($objItemMenuDTOPeticionamentoEletronico);


        $this->logar('CRIANDO e VINCULANDO RECURSO A PERFIL - integra��o EM Administrador');
        $objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'md_pet_integ_funcionalid_listar');
        $objItemRecursoIntegracaoListarDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'md_pet_integracao_listar');
        $objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'md_pet_integracao_excluir');
        $objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'md_pet_integracao_desativar');
        $objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'md_pet_integracao_reativar');
        $objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'md_pet_integracao_cadastrar');
        $objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'md_pet_integracao_alterar');


        $this->logar('CRIANDO e VINCULANDO RECURSO A PERFIL - Integra��o EM B�sico');
        $objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiBasico, 'md_pet_integracao_listar');
        $objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiBasico, 'md_pet_integracao_selecionar');
        $objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiBasico, 'md_pet_integracao_consultar');


        $this->logar('CRIANDO e VINCULANDO ITEM MENU A PERFIL - Administra��o->Peticionamento Eletr�nico->Integra��es EM Administrador');
        $this->adicionarItemMenu($numIdSistemaSei,
            $numIdPerfilSeiAdministrador,
            $numIdMenuSei,
            $objItemMenuDTOPeticionamentoEletronico->getNumIdItemMenu(),
            $objItemRecursoIntegracaoListarDTO->getNumIdRecurso(),
            'Integra��es',
            90);


        $this->logar('CRIANDO e VINCULANDO ITEM MENU A PERFIL - Administra��o->Peticionamento Eletr�nico->Par�metros para Vincula��o a Usu�rio Externo EM Administrador');
        $objItemRecursoDTOMenu = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiBasico, 'md_pet_vinc_tp_processo_cadastrar');
        $objItemRecursoDTOMenu = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'md_pet_vinc_tp_processo_cadastrar');
        $objItemMenuVinculacaoUsuExtPJ = $this->adicionarItemMenu($numIdSistemaSei,
            $numIdPerfilSeiAdministrador,
            $numIdMenuSei,
            $objItemMenuDTOPeticionamentoEletronico->getNumIdItemMenu(),
            $objItemRecursoDTOMenu->getNumIdRecurso(),
            'Par�metros para Vincula��o a Usu�rio Externo',
            100);


        $this->logar('CRIANDO e VINCULANDO RECURSO A PERFIL - vinculacao PJ - tipo processo EM Administrador');
        $objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'md_pet_vinc_tp_processo_listar');
        $objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'md_pet_vinc_tp_processo_excluir');
        $objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'md_pet_vinc_tp_processo_consultar');
        $objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'md_pet_vinc_tp_processo_alterar');


        $this->logar('CRIANDO e VINCULANDO RECURSO A PERFIL - V�nculo PJ EM Administrador');
        $objItemRecursoDTOMenu = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'md_pet_adm_vinc_listar');
        $objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiBasico, 'md_pet_vinc_documento_listar');
        $objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiBasico, 'md_pet_vinculacao_listar');
        $objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'md_pet_vinc_documento_cadastrar');
        $objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'md_pet_vinc_documento_consultar');
        $objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'md_pet_vinc_suspender_restabelecer');
        $objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'md_pet_vinc_representant_alterar');
        $objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'md_pet_vinc_representant_cadastrar');
        $objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'md_pet_vinc_representant_consultar');
        $objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'md_pet_vinc_representant_listar');
        $objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'md_pet_vinc_responsavel_cadastrar');

        $objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'md_pet_vinculacao_consultar');
        $objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiBasico, 'md_pet_vinc_usu_ext_pe_listar');


        $this->logar('CRIANDO e VINCULANDO ITEM MENU A PERFIL - Administra��o->Peticionamento Eletr�nico->Vincula��es e Procura��es Eletr�nicas EM Administrador');
        $this->adicionarItemMenu($numIdSistemaSei,
            $numIdPerfilSeiAdministrador,
            $numIdMenuSei,
            $objItemMenuDTOPeticionamentoEletronico->getNumIdItemMenu(),
            $objItemRecursoDTOMenu->getNumIdRecurso(),
            'Vincula��es e Procura��es Eletr�nicas',
            110);


        $this->logar('RECUPERANDO MENU DE RELAT�RIOS');
        $objItemMenuDTORelatorios = new ItemMenuDTO();
        $objItemMenuDTORelatorios->retNumIdItemMenu();
        $objItemMenuDTORelatorios->setNumIdSistema($numIdSistemaSei);
        $objItemMenuDTORelatorios->setStrRotulo('Relat�rios');
        $objItemMenuDTORelatorios = $objItemMenuRN->consultar($objItemMenuDTORelatorios);


        $this->logar('CRIANDO e VINCULANDO RECURSO A PERFIL - V�nculo PJ consultar EM B�sico');
        $objItemRecursoDTOMenu = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiBasico, 'md_pet_adm_vinc_consultar');


        $this->logar('CRIANDO e VINCULANDO ITEM MENU A PERFIL - Relat�rios->Vincula��es e Procura��es Eletr�nicas EM B�sico');
        $this->adicionarItemMenu($numIdSistemaSei,
            $numIdPerfilSeiBasico,
            $numIdMenuSei,
            $objItemMenuDTORelatorios->getNumIdItemMenu(),
            $objItemRecursoDTOMenu->getNumIdRecurso(),
            'Vincula��es e Procura��es Eletr�nicas',
            10);


        $this->logar('CRIANDO REGRA DE AUDITORIA PARA NOVOS RECURSOS RECEM ADICIONADOS');
        $objRegraAuditoriaDTO = new RegraAuditoriaDTO();
        $objRegraAuditoriaDTO->retNumIdRegraAuditoria();
        $objRegraAuditoriaDTO->setNumIdSistema($numIdSistemaSei);
        $objRegraAuditoriaDTO->setStrDescricao('Modulo_Peticionamento_Eletronico');

        $objRegraAuditoriaRN = new RegraAuditoriaRN();
        $objRegraAuditoriaDTO = $objRegraAuditoriaRN->consultar($objRegraAuditoriaDTO);

        $rs = BancoSip::getInstance()->consultarSql('select id_recurso from recurso where id_sistema=' . $numIdSistemaSei . ' and nome in (
          \'md_pet_vinc_tp_processo_cadastrar\',
          \'md_pet_vinc_tp_processo_alterar\')'
        );

        foreach ($rs as $recurso) {
            BancoSip::getInstance()->executarSql('insert into rel_regra_auditoria_recurso (id_regra_auditoria, id_sistema, id_recurso) values (' . $objRegraAuditoriaDTO->getNumIdRegraAuditoria() . ', ' . $numIdSistemaSei . ', ' . $recurso['id_recurso'] . ')');
        }

        $objReplicacaoRegraAuditoriaDTO = new ReplicacaoRegraAuditoriaDTO();
        $objReplicacaoRegraAuditoriaDTO->setStrStaOperacao('A');
        $objReplicacaoRegraAuditoriaDTO->setNumIdRegraAuditoria($objRegraAuditoriaDTO->getNumIdRegraAuditoria());

        $objSistemaRN = new SistemaRN();
        $objSistemaRN->replicarRegraAuditoria($objReplicacaoRegraAuditoriaDTO);

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

        if ($objSistemaDTO == null) {
            throw new InfraException('Sistema SEI n�o encontrado.');
        }

        $numIdSistemaSei = $objSistemaDTO->getNumIdSistema();

        $objPerfilDTO = new PerfilDTO();
        $objPerfilDTO->retNumIdPerfil();
        $objPerfilDTO->setNumIdSistema($numIdSistemaSei);
        $objPerfilDTO->setStrNome('Administrador');
        $objPerfilDTO = $objPerfilRN->consultar($objPerfilDTO);

        if ($objPerfilDTO == null) {
            throw new InfraException('Perfil Administrador do sistema SEI n�o encontrado.');
        }

        $numIdPerfilSeiAdministrador = $objPerfilDTO->getNumIdPerfil();

        $this->logar('ATUALIZANDO RECURSOS, MENUS E PERFIS DO MODULO ' . $this->nomeDesteModulo . ' NA BASE DO SIP...');

        $this->logar('CRIANDO e VINCULANDO RECURSO A PERFIL - integra��o EM Administrador');
        $objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'md_pet_orientacoes_tipo_destinatario');

        $this->logar('CRIANDO REGRA DE AUDITORIA PARA NOVOS RECURSOS RECEM ADICIONADOS');
        $objRegraAuditoriaDTO = new RegraAuditoriaDTO();
        $objRegraAuditoriaDTO->retNumIdRegraAuditoria();
        $objRegraAuditoriaDTO->setNumIdSistema($numIdSistemaSei);
        $objRegraAuditoriaDTO->setStrDescricao('Modulo_Peticionamento_Eletronico');

        $objRegraAuditoriaRN = new RegraAuditoriaRN();
        $objRegraAuditoriaDTO = $objRegraAuditoriaRN->consultar($objRegraAuditoriaDTO);

        $rs = BancoSip::getInstance()->consultarSql('select id_recurso from recurso where id_sistema=' . $numIdSistemaSei . ' and nome in (
          \'md_pet_orientacoes_tipo_destinatario\')'
        );

        foreach ($rs as $recurso) {
            BancoSip::getInstance()->executarSql('insert into rel_regra_auditoria_recurso (id_regra_auditoria, id_sistema, id_recurso) values (' . $objRegraAuditoriaDTO->getNumIdRegraAuditoria() . ', ' . $numIdSistemaSei . ', ' . $recurso['id_recurso'] . ')');
        }

        $objReplicacaoRegraAuditoriaDTO = new ReplicacaoRegraAuditoriaDTO();
        $objReplicacaoRegraAuditoriaDTO->setStrStaOperacao('A');
        $objReplicacaoRegraAuditoriaDTO->setNumIdRegraAuditoria($objRegraAuditoriaDTO->getNumIdRegraAuditoria());

        $objSistemaRN = new SistemaRN();
        $objSistemaRN->replicarRegraAuditoria($objReplicacaoRegraAuditoriaDTO);


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

        if ($objSistemaDTO == null) {
            throw new InfraException('Sistema SEI n�o encontrado.');
        }

        $numIdSistemaSei = $objSistemaDTO->getNumIdSistema();

        $objPerfilDTO = new PerfilDTO();
        $objPerfilDTO->retNumIdPerfil();
        $objPerfilDTO->setNumIdSistema($numIdSistemaSei);
        $objPerfilDTO->setStrNome('B�sico');
        $objPerfilDTO = $objPerfilRN->consultar($objPerfilDTO);

        if ($objPerfilDTO == null) {
            throw new InfraException('Perfil B�sico do sistema SEI n�o encontrado.');
        }

        $numIdPerfilSeiBasico = $objPerfilDTO->getNumIdPerfil();

        //pessoa fisica
        $this->logar('ATUALIZANDO RECURSOS, MENUS E PERFIS DO MODULO ' . $this->nomeDesteModulo . ' NA BASE DO SIP...');

        $this->logar('CRIANDO e VINCULANDO RECURSO A PERFIL - integra��o EM b�sico');
        $objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiBasico, 'md_pet_pessoa_fisica');

        $this->logar('CRIANDO REGRA DE AUDITORIA PARA NOVOS RECURSOS RECEM ADICIONADOS');
        $objRegraAuditoriaDTO = new RegraAuditoriaDTO();
        $objRegraAuditoriaDTO->retNumIdRegraAuditoria();
        $objRegraAuditoriaDTO->setNumIdSistema($numIdSistemaSei);
        $objRegraAuditoriaDTO->setStrDescricao('Modulo_Peticionamento_Eletronico');

        $objRegraAuditoriaRN = new RegraAuditoriaRN();
        $objRegraAuditoriaDTO = $objRegraAuditoriaRN->consultar($objRegraAuditoriaDTO);

        $rs = BancoSip::getInstance()->consultarSql('select id_recurso from recurso where id_sistema=' . $numIdSistemaSei . ' and nome in (
          \'md_pet_pessoa_fisica\')'
        );

        foreach ($rs as $recurso) {
            BancoSip::getInstance()->executarSql('insert into rel_regra_auditoria_recurso (id_regra_auditoria, id_sistema, id_recurso) values (' . $objRegraAuditoriaDTO->getNumIdRegraAuditoria() . ', ' . $numIdSistemaSei . ', ' . $recurso['id_recurso'] . ')');
        }

        $objReplicacaoRegraAuditoriaDTO = new ReplicacaoRegraAuditoriaDTO();
        $objReplicacaoRegraAuditoriaDTO->setStrStaOperacao('A');
        $objReplicacaoRegraAuditoriaDTO->setNumIdRegraAuditoria($objRegraAuditoriaDTO->getNumIdRegraAuditoria());

        $objSistemaRN = new SistemaRN();
        $objSistemaRN->replicarRegraAuditoria($objReplicacaoRegraAuditoriaDTO);

        //pessoa juridica
        $this->logar('ATUALIZANDO RECURSOS, MENUS E PERFIS DO MODULO ' . $this->nomeDesteModulo . ' NA BASE DO SIP...');

        $this->logar('CRIANDO e VINCULANDO RECURSO A PERFIL - integra��o EM b�sico');
        $objItemRecursoDTO = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiBasico, 'md_pet_pessoa_juridica');

        $this->logar('CRIANDO REGRA DE AUDITORIA PARA NOVOS RECURSOS RECEM ADICIONADOS');
        $objRegraAuditoriaDTO = new RegraAuditoriaDTO();
        $objRegraAuditoriaDTO->retNumIdRegraAuditoria();
        $objRegraAuditoriaDTO->setNumIdSistema($numIdSistemaSei);
        $objRegraAuditoriaDTO->setStrDescricao('Modulo_Peticionamento_Eletronico');

        $objRegraAuditoriaRN = new RegraAuditoriaRN();
        $objRegraAuditoriaDTO = $objRegraAuditoriaRN->consultar($objRegraAuditoriaDTO);

        $rs = BancoSip::getInstance()->consultarSql('select id_recurso from recurso where id_sistema=' . $numIdSistemaSei . ' and nome in (
          \'md_pet_pessoa_juridica\')'
        );

        foreach ($rs as $recurso) {
            BancoSip::getInstance()->executarSql('insert into rel_regra_auditoria_recurso (id_regra_auditoria, id_sistema, id_recurso) values (' . $objRegraAuditoriaDTO->getNumIdRegraAuditoria() . ', ' . $numIdSistemaSei . ', ' . $recurso['id_recurso'] . ')');
        }

        $objReplicacaoRegraAuditoriaDTO = new ReplicacaoRegraAuditoriaDTO();
        $objReplicacaoRegraAuditoriaDTO->setStrStaOperacao('A');
        $objReplicacaoRegraAuditoriaDTO->setNumIdRegraAuditoria($objRegraAuditoriaDTO->getNumIdRegraAuditoria());

        $objSistemaRN = new SistemaRN();
        $objSistemaRN->replicarRegraAuditoria($objReplicacaoRegraAuditoriaDTO);

        $this->logar('ATUALIZANDO PAR�METRO ' . $this->nomeParametroModulo . ' NA TABELA infra_parametro PARA CONTROLAR A VERS�O DO M�DULO');
        BancoSip::getInstance()->executarSql('UPDATE infra_parametro SET valor = \'3.0.0\' WHERE nome = \'' . $this->nomeParametroModulo . '\' ');

        $this->logar('INSTALA��O/ATUALIZA��O DA VERS�O ' . $this->versaoAtualDesteModulo . ' DO ' . $this->nomeDesteModulo . ' REALIZADA COM SUCESSO NA BASE DO SIP');
    }

    protected function instalarv301()
    {

        $this->logar('EXECUTANDO A INSTALA��O/ATUALIZA��O DA VERS�O 3.0.1 DO ' . $this->nomeDesteModulo . ' NA BASE DO SIP');

        $this->logar('ATUALIZANDO PAR�METRO ' . $this->nomeParametroModulo . ' NA TABELA infra_parametro PARA CONTROLAR A VERS�O DO M�DULO');
        BancoSip::getInstance()->executarSql('UPDATE infra_parametro SET valor = \'3.0.1\' WHERE nome = \'' . $this->nomeParametroModulo . '\' ');

        $this->logar('INSTALA��O/ATUALIZA��O DA VERS�O ' . $this->versaoAtualDesteModulo . ' DO ' . $this->nomeDesteModulo . ' REALIZADA COM SUCESSO NA BASE DO SIP');
    }

    protected function instalarv310()
    {

        $this->logar('EXECUTANDO A INSTALA��O/ATUALIZA��O DA VERS�O 3.1.0 DO ' . $this->nomeDesteModulo . ' NA BASE DO SIP');

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

        if ($objSistemaDTO == null) {
            throw new InfraException('Sistema SEI no encontrado.');
        }

        $numIdSistemaSei = $objSistemaDTO->getNumIdSistema();

        $objPerfilDTO = new PerfilDTO();
        $objPerfilDTO->retNumIdPerfil();
        $objPerfilDTO->setNumIdSistema($numIdSistemaSei);
        $objPerfilDTO->setStrNome('Administrador');
        $objPerfilDTO = $objPerfilRN->consultar($objPerfilDTO);

        if ($objPerfilDTO == null) {
            throw new InfraException('Perfil Administrador do sistema SEI no encontrado.');
        }

        $numIdPerfilSeiAdministrador = $objPerfilDTO->getNumIdPerfil();

        $objPerfilBasicoDTO = new PerfilDTO();
        $objPerfilBasicoDTO->retNumIdPerfil();
        $objPerfilBasicoDTO->setNumIdSistema($numIdSistemaSei);
        $objPerfilBasicoDTO->setStrNome('B�sico');
        $objPerfilBasicoDTO = $objPerfilRN->consultar($objPerfilBasicoDTO);

        if ($objPerfilBasicoDTO == null) {
            throw new InfraException('Perfil B�sico do sistema SEI no encontrado.');
        }

        $numIdPerfilSeiBasico = $objPerfilBasicoDTO->getNumIdPerfil();

        $objMenuDTO = new MenuDTO();
        $objMenuDTO->retNumIdMenu();
        $objMenuDTO->setNumIdSistema($numIdSistemaSei);
        $objMenuDTO->setStrNome('Principal');
        $objMenuDTO = $objMenuRN->consultar($objMenuDTO);

        if ($objMenuDTO == null) {
            throw new InfraException('Menu do sistema SEI no encontrado.');
        }

        $numIdMenuSei = $objMenuDTO->getNumIdMenu();


        $this->logar('ADICIONANDO RECURSOS, MENUS DO TIPO DE PODER ' . $this->nomeDesteModulo . ' NA BASE DO SIP..');
        $objItemRecursoDTOMenu = $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'md_pet_tipo_poder_listar');
        $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'md_pet_tipo_poder_desativar');
        $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'md_pet_tipo_poder_reativar');
        $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'md_pet_tipo_poder_excluir');
        $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'md_pet_tipo_poder_cadastrar');
        $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'md_pet_tipo_poder_consultar');
        $this->adicionarRecursoPerfil($numIdSistemaSei, $numIdPerfilSeiAdministrador, 'md_pet_tipo_poder_alterar');

        $objItemMenuDTOPeticionamentoEletronico = new ItemMenuDTO();
        $objItemMenuDTOPeticionamentoEletronico->retNumIdItemMenu();
        $objItemMenuDTOPeticionamentoEletronico->setNumIdSistema($numIdSistemaSei);
        $objItemMenuDTOPeticionamentoEletronico->setStrRotulo('Peticionamento Eletr�nico');
        $objItemMenuDTOPeticionamentoEletronico = $objItemMenuRN->consultar($objItemMenuDTOPeticionamentoEletronico);

        $this->adicionarItemMenu($numIdSistemaSei,
            $numIdPerfilSeiAdministrador,
            $numIdMenuSei,
            $objItemMenuDTOPeticionamentoEletronico->getNumIdItemMenu(),
            $objItemRecursoDTOMenu->getNumIdRecurso(),
            'Tipos de Poderes Legais',
            120);

        $this->logar('ATUALIZANDO PAR�METRO ' . $this->nomeParametroModulo . ' NA TABELA infra_parametro PARA CONTROLAR A VERS�O DO M�DULO');
        BancoSip::getInstance()->executarSql('UPDATE infra_parametro SET valor = \'3.1.0\' WHERE nome = \'' . $this->nomeParametroModulo . '\' ');

        $this->logar('INSTALA��O/ATUALIZA��O DA VERS�O ' . $this->versaoAtualDesteModulo . ' DO ' . $this->nomeDesteModulo . ' REALIZADA COM SUCESSO NA BASE DO SIP');

    }

    protected function instalarv320()
    {

        $this->logar('EXECUTANDO A INSTALA��O/ATUALIZA��O DA VERS�O 3.2.0 DO ' . $this->nomeDesteModulo . ' NA BASE DO SIP');

        $this->logar('ATUALIZANDO PAR�METRO ' . $this->nomeParametroModulo . ' NA TABELA infra_parametro PARA CONTROLAR A VERS�O DO M�DULO');
        BancoSip::getInstance()->executarSql('UPDATE infra_parametro SET valor = \'3.2.0\' WHERE nome = \'' . $this->nomeParametroModulo . '\' ');

        $this->logar('INSTALA��O/ATUALIZA��O DA VERS�O ' . $this->versaoAtualDesteModulo . ' DO ' . $this->nomeDesteModulo . ' REALIZADA COM SUCESSO NA BASE DO SIP');
    }

    protected function instalarv330()
    {

        $this->logar('EXECUTANDO A INSTALA��O/ATUALIZA��O DA VERS�O ' . $this->versaoAtualDesteModulo . ' DO ' . $this->nomeDesteModulo . ' NA BASE DO SIP');

        $this->logar('EXCLUINDO RECURSOS DA REGRA DE AUDITORIA DO M�DULO ' . $this->nomeDesteModulo . ' NA BASE DO SIP..');

        $objSistemaRN = new SistemaRN();
        $objSistemaDTO = new SistemaDTO();
        $objSistemaDTO->retNumIdSistema();
        $objSistemaDTO->setStrSigla('SEI');

        $objSistemaDTO = $objSistemaRN->consultar($objSistemaDTO);

        if ($objSistemaDTO == null) {
            throw new InfraException('Sistema SEI n�o encontrado.');
        }

        $numIdSistemaSei = $objSistemaDTO->getNumIdSistema();

        $rs = BancoSip::getInstance()->consultarSql('select id_recurso from recurso where id_sistema=' . $numIdSistemaSei . ' and nome in (
          \'md_pet_intercorrente_criterio_listar\',
          \'md_pet_indisponibilidade_consultar\',
          \'md_pet_pessoa_fisica\',
          \'md_pet_pessoa_juridica\'          
          )'
        );

        foreach ($rs as $recurso) {
            BancoSip::getInstance()->executarSql('DELETE FROM rel_regra_auditoria_recurso WHERE id_recurso = ' . $recurso['id_recurso']);
        }

        $this->logar('CRIANDO REGRA DE AUDITORIA PARA NOVOS RECURSOS RECEM ADICIONADOS');
        $objRegraAuditoriaDTO = new RegraAuditoriaDTO();
        $objRegraAuditoriaDTO->retNumIdRegraAuditoria();
        $objRegraAuditoriaDTO->setNumIdSistema($numIdSistemaSei);
        $objRegraAuditoriaDTO->setStrDescricao('Modulo_Peticionamento_Eletronico');

        $objRegraAuditoriaRN = new RegraAuditoriaRN();
        $objRegraAuditoriaDTO = $objRegraAuditoriaRN->consultar($objRegraAuditoriaDTO);

        $rs = BancoSip::getInstance()->consultarSql('select id_recurso from recurso where id_sistema=' . $numIdSistemaSei . ' and nome in (
          \'md_pet_intercorrente_criterio_cadastrar\',
          \'md_pet_intercorrente_criterio_alterar\',
          \'md_pet_intercorrente_criterio_excluir\',
          \'md_pet_intercorrente_criterio_desativar\',
          \'md_pet_intercorrente_criterio_reativar\',       
          \'md_pet_int_serie_cadastrar\',    
          \'md_pet_int_serie_alterar\',    
          \'md_pet_int_serie_excluir\',
          \'md_pet_integracao_cadastrar\',
          \'md_pet_integracao_alterar\',
          \'md_pet_integracao_excluir\',
          \'md_pet_integracao_desativar\',
          \'md_pet_integracao_reativar\',
          \'md_pet_tipo_poder_cadastrar\',
          \'md_pet_tipo_poder_alterar\',
          \'md_pet_tipo_poder_excluir\',
          \'md_pet_tipo_poder_desativar\',
          \'md_pet_tipo_poder_reativar\'
          )'
        );

        foreach ($rs as $recurso) {
            BancoSip::getInstance()->executarSql('insert into rel_regra_auditoria_recurso (id_regra_auditoria, id_sistema, id_recurso) values (' . $objRegraAuditoriaDTO->getNumIdRegraAuditoria() . ', ' . $numIdSistemaSei . ', ' . $recurso['id_recurso'] . ')');
        }

        $objReplicacaoRegraAuditoriaDTO = new ReplicacaoRegraAuditoriaDTO();
        $objReplicacaoRegraAuditoriaDTO->setStrStaOperacao('A');
        $objReplicacaoRegraAuditoriaDTO->setNumIdRegraAuditoria($objRegraAuditoriaDTO->getNumIdRegraAuditoria());

        $objSistemaRN = new SistemaRN();
        $objSistemaRN->replicarRegraAuditoria($objReplicacaoRegraAuditoriaDTO);

        $this->logar('ATUALIZANDO PAR�METRO ' . $this->nomeParametroModulo . ' NA TABELA infra_parametro PARA CONTROLAR A VERS�O DO M�DULO');
        BancoSip::getInstance()->executarSql('UPDATE infra_parametro SET valor = \'3.3.0\' WHERE nome = \'' . $this->nomeParametroModulo . '\' ');

        $this->logar('INSTALA��O/ATUALIZA��O DA VERS�O ' . $this->versaoAtualDesteModulo . ' DO ' . $this->nomeDesteModulo . ' REALIZADA COM SUCESSO NA BASE DO SIP');
    }

    protected function instalarv340()
    {

        $this->logar('EXECUTANDO A INSTALA��O/ATUALIZA��O DA VERS�O 3.4.0 DO ' . $this->nomeDesteModulo . ' NA BASE DO SIP');

        $this->logar('ATUALIZANDO PAR�METRO ' . $this->nomeParametroModulo . ' NA TABELA infra_parametro PARA CONTROLAR A VERS�O DO M�DULO');
        BancoSip::getInstance()->executarSql('UPDATE infra_parametro SET valor = \'3.4.0\' WHERE nome = \'' . $this->nomeParametroModulo . '\' ');

        $this->logar('INSTALA��O/ATUALIZA��O DA VERS�O ' . $this->versaoAtualDesteModulo . ' DO ' . $this->nomeDesteModulo . ' REALIZADA COM SUCESSO NA BASE DO SIP');
    }

    protected function instalarv341()
    {

        $this->logar('EXECUTANDO A INSTALA��O/ATUALIZA��O DA VERS�O 3.4.1 DO ' . $this->nomeDesteModulo . ' NA BASE DO SIP');

        $this->logar('ATUALIZANDO PAR�METRO ' . $this->nomeParametroModulo . ' NA TABELA infra_parametro PARA CONTROLAR A VERS�O DO M�DULO');
        BancoSip::getInstance()->executarSql('UPDATE infra_parametro SET valor = \'3.4.1\' WHERE nome = \'' . $this->nomeParametroModulo . '\' ');

        $this->logar('INSTALA��O/ATUALIZA��O DA VERS�O ' . $this->versaoAtualDesteModulo . ' DO ' . $this->nomeDesteModulo . ' REALIZADA COM SUCESSO NA BASE DO SIP');
    }

    protected function instalarv342()
    {

        $this->logar('EXECUTANDO A INSTALA��O/ATUALIZA��O DA VERS�O 3.4.2 DO ' . $this->nomeDesteModulo . ' NA BASE DO SIP');

        $this->logar('ATUALIZANDO PAR�METRO ' . $this->nomeParametroModulo . ' NA TABELA infra_parametro PARA CONTROLAR A VERS�O DO M�DULO');
        BancoSip::getInstance()->executarSql('UPDATE infra_parametro SET valor = \'3.4.2\' WHERE nome = \'' . $this->nomeParametroModulo . '\' ');

        $this->logar('INSTALA��O/ATUALIZA��O DA VERS�O ' . $this->versaoAtualDesteModulo . ' DO ' . $this->nomeDesteModulo . ' REALIZADA COM SUCESSO NA BASE DO SIP');
    }


    private function adicionarRecursoPerfil($numIdSistema, $numIdPerfil, $strNome, $strCaminho = null)
    {

        $objRecursoDTO = new RecursoDTO();
        $objRecursoDTO->retNumIdRecurso();
        $objRecursoDTO->setNumIdSistema($numIdSistema);
        $objRecursoDTO->setStrNome($strNome);

        $objRecursoRN = new RecursoRN();
        $objRecursoDTO = $objRecursoRN->consultar($objRecursoDTO);

        if ($objRecursoDTO == null) {
            $objRecursoDTO = new RecursoDTO();
            $objRecursoDTO->setNumIdRecurso(null);
            $objRecursoDTO->setNumIdSistema($numIdSistema);
            $objRecursoDTO->setStrNome($strNome);
            $objRecursoDTO->setStrDescricao(null);

            if ($strCaminho == null) {
                $objRecursoDTO->setStrCaminho('controlador.php?acao=' . $strNome);
            } else {
                $objRecursoDTO->setStrCaminho($strCaminho);
            }
            $objRecursoDTO->setStrSinAtivo('S');
            $objRecursoDTO = $objRecursoRN->cadastrar($objRecursoDTO);
        }

        if ($numIdPerfil != null) {
            $objRelPerfilRecursoDTO = new RelPerfilRecursoDTO();
            $objRelPerfilRecursoDTO->setNumIdSistema($numIdSistema);
            $objRelPerfilRecursoDTO->setNumIdPerfil($numIdPerfil);
            $objRelPerfilRecursoDTO->setNumIdRecurso($objRecursoDTO->getNumIdRecurso());

            $objRelPerfilRecursoRN = new RelPerfilRecursoRN();

            if ($objRelPerfilRecursoRN->contar($objRelPerfilRecursoDTO) == 0) {
                $objRelPerfilRecursoRN->cadastrar($objRelPerfilRecursoDTO);
            }
        }

        return $objRecursoDTO;

    }

    private function removerRecursoPerfil($numIdSistema, $strNome, $numIdPerfil)
    {
        $objRecursoDTO = new RecursoDTO();
        $objRecursoDTO->setBolExclusaoLogica(false);
        $objRecursoDTO->retNumIdRecurso();
        $objRecursoDTO->setNumIdSistema($numIdSistema);
        $objRecursoDTO->setStrNome($strNome);

        $objRecursoRN = new RecursoRN();
        $objRecursoDTO = $objRecursoRN->consultar($objRecursoDTO);

        if ($objRecursoDTO != null) {
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

    private function desativarRecurso($numIdSistema, $strNome)
    {
        $objRecursoDTO = new RecursoDTO();
        $objRecursoDTO->retNumIdRecurso();
        $objRecursoDTO->setNumIdSistema($numIdSistema);
        $objRecursoDTO->setStrNome($strNome);

        $objRecursoRN = new RecursoRN();
        $objRecursoDTO = $objRecursoRN->consultar($objRecursoDTO);

        if ($objRecursoDTO != null) {
            $objRecursoRN->desativar(array($objRecursoDTO));
        }
    }

    private function removerRecurso($numIdSistema, $strNome)
    {
        $objRecursoDTO = new RecursoDTO();
        $objRecursoDTO->setBolExclusaoLogica(false);
        $objRecursoDTO->retNumIdRecurso();
        $objRecursoDTO->setNumIdSistema($numIdSistema);
        $objRecursoDTO->setStrNome($strNome);

        $objRecursoRN = new RecursoRN();
        $objRecursoDTO = $objRecursoRN->consultar($objRecursoDTO);

        if ($objRecursoDTO != null) {
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

            foreach ($arrObjItemMenuDTO as $objItemMenuDTO) {
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

    private function renomearRecurso($numIdSistema, $strNomeAtual, $strNomeNovo)
    {
        $objRecursoDTO = new RecursoDTO();
        $objRecursoDTO->setBolExclusaoLogica(false);
        $objRecursoDTO->retNumIdRecurso();
        $objRecursoDTO->retStrCaminho();
        $objRecursoDTO->setNumIdSistema($numIdSistema);
        $objRecursoDTO->setStrNome($strNomeAtual);

        $objRecursoRN = new RecursoRN();
        $objRecursoDTO = $objRecursoRN->consultar($objRecursoDTO);

        if ($objRecursoDTO != null) {
            $objRecursoDTO->setStrNome($strNomeNovo);
            $objRecursoDTO->setStrCaminho(str_replace($strNomeAtual, $strNomeNovo, $objRecursoDTO->getStrCaminho()));
            $objRecursoRN->alterar($objRecursoDTO);
        }
    }

    private function adicionarItemMenu($numIdSistema, $numIdPerfil, $numIdMenu, $numIdItemMenuPai, $numIdRecurso, $strRotulo, $numSequencia)
    {

        $objItemMenuDTO = new ItemMenuDTO();
        $objItemMenuDTO->retNumIdItemMenu();
        $objItemMenuDTO->setNumIdMenu($numIdMenu);

        if ($numIdItemMenuPai == null) {
            $objItemMenuDTO->setNumIdMenuPai(null);
            $objItemMenuDTO->setNumIdItemMenuPai(null);
        } else {
            $objItemMenuDTO->setNumIdMenuPai($numIdMenu);
            $objItemMenuDTO->setNumIdItemMenuPai($numIdItemMenuPai);
        }

        $objItemMenuDTO->setNumIdSistema($numIdSistema);
        $objItemMenuDTO->setNumIdRecurso($numIdRecurso);
        $objItemMenuDTO->setStrRotulo($strRotulo);

        $objItemMenuRN = new ItemMenuRN();
        $objItemMenuDTO = $objItemMenuRN->consultar($objItemMenuDTO);

        if ($objItemMenuDTO == null) {
            $objItemMenuDTO = new ItemMenuDTO();
            $objItemMenuDTO->setNumIdItemMenu(null);
            $objItemMenuDTO->setNumIdMenu($numIdMenu);

            if ($numIdItemMenuPai == null) {
                $objItemMenuDTO->setNumIdMenuPai(null);
                $objItemMenuDTO->setNumIdItemMenuPai(null);
            } else {
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

        if ($numIdPerfil != null && $numIdRecurso != null) {
            $objRelPerfilRecursoDTO = new RelPerfilRecursoDTO();
            $objRelPerfilRecursoDTO->setNumIdSistema($numIdSistema);
            $objRelPerfilRecursoDTO->setNumIdPerfil($numIdPerfil);
            $objRelPerfilRecursoDTO->setNumIdRecurso($numIdRecurso);

            $objRelPerfilRecursoRN = new RelPerfilRecursoRN();

            if ($objRelPerfilRecursoRN->contar($objRelPerfilRecursoDTO) == 0) {
                $objRelPerfilRecursoRN->cadastrar($objRelPerfilRecursoDTO);
            }

            $objRelPerfilItemMenuDTO = new RelPerfilItemMenuDTO();
            $objRelPerfilItemMenuDTO->setNumIdPerfil($numIdPerfil);
            $objRelPerfilItemMenuDTO->setNumIdSistema($numIdSistema);
            $objRelPerfilItemMenuDTO->setNumIdRecurso($numIdRecurso);
            $objRelPerfilItemMenuDTO->setNumIdMenu($numIdMenu);
            $objRelPerfilItemMenuDTO->setNumIdItemMenu($objItemMenuDTO->getNumIdItemMenu());

            $objRelPerfilItemMenuRN = new RelPerfilItemMenuRN();

            if ($objRelPerfilItemMenuRN->contar($objRelPerfilItemMenuDTO) == 0) {
                $objRelPerfilItemMenuRN->cadastrar($objRelPerfilItemMenuDTO);
            }
        }

        return $objItemMenuDTO;

    }

    private function removerItemMenu($numIdSistema, $numIdMenu, $numIdItemMenu)
    {

        $objItemMenuDTO = new ItemMenuDTO();
        $objItemMenuDTO->retNumIdMenu();
        $objItemMenuDTO->retNumIdItemMenu();
        $objItemMenuDTO->setNumIdSistema($numIdSistema);
        $objItemMenuDTO->setNumIdMenu($numIdMenu);
        $objItemMenuDTO->setNumIdItemMenu($numIdItemMenu);

        $objItemMenuRN = new ItemMenuRN();
        $objItemMenuDTO = $objItemMenuRN->consultar($objItemMenuDTO);

        if ($objItemMenuDTO != null) {
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

    private function removerPerfil($numIdSistema, $strNome)
    {

        $objPerfilDTO = new PerfilDTO();
        $objPerfilDTO->retNumIdPerfil();
        $objPerfilDTO->setNumIdSistema($numIdSistema);
        $objPerfilDTO->setStrNome($strNome);

        $objPerfilRN = new PerfilRN();
        $objPerfilDTO = $objPerfilRN->consultar($objPerfilDTO);

        if ($objPerfilDTO != null) {
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

//========================= INICIO SCRIPT EXECU�AO =============

try {

    session_start();
    SessaoSip::getInstance(false);
    BancoSip::getInstance()->setBolScript(true);

    if (!ConfiguracaoSip::getInstance()->isSetValor('BancoSip', 'UsuarioScript')) {
        throw new InfraException('Chave BancoSip/UsuarioScript n�o encontrada.');
    }

    if (InfraString::isBolVazia(ConfiguracaoSip::getInstance()->getValor('BancoSip', 'UsuarioScript'))) {
        throw new InfraException('Chave BancoSip/UsuarioScript n�o possui valor.');
    }

    if (!ConfiguracaoSip::getInstance()->isSetValor('BancoSip', 'SenhaScript')) {
        throw new InfraException('Chave BancoSip/SenhaScript n�o encontrada.');
    }

    if (InfraString::isBolVazia(ConfiguracaoSip::getInstance()->getValor('BancoSip', 'SenhaScript'))) {
        throw new InfraException('Chave BancoSip/SenhaScript n�o possui valor.');
    }

    $objVersaoRN = new MdPetAtualizadorSipRN();
    $objVersaoRN->atualizarVersao();

} catch (Exception $e) {
    echo(InfraException::inspecionar($e));
    try {
        LogSip::getInstance()->gravar(InfraException::inspecionar($e));
    } catch (Exception $e) {
    }
    exit(1);
}

print PHP_EOL;