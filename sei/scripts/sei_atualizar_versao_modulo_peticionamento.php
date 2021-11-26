<?
require_once dirname(__FILE__) . '/../web/SEI.php';

class MdPetAtualizadorSeiRN extends InfraRN
{

    private $numSeg = 0;
    private $versaoAtualDesteModulo = '3.4.2';
    private $nomeDesteModulo = 'M�DULO DE PETICIONAMENTO E INTIMA��O ELETR�NICOS';
    private $nomeParametroModulo = 'VERSAO_MODULO_PETICIONAMENTO';
    private $historicoVersoes = array('0.0.1', '0.0.2', '1.0.3', '1.0.4', '1.1.0', '2.0.0', '2.0.1', '2.0.2', '2.0.3', '2.0.4', '2.0.5', '3.0.0', '3.0.1', '3.1.0', '3.2.0', '3.3.0', '3.4.0', '3.4.1', '3.4.2');
    public static $MD_PET_ID_SERIE_RECIBO = 'MODULO_PETICIONAMENTO_ID_SERIE_RECIBO_PETICIONAMENTO';
    public static $MD_PET_ID_SERIE_FORMULARIO = 'MODULO_PETICIONAMENTO_ID_SERIE_VINC_FORMULARIO';
    public static $MD_PET_ID_SERIE_PROCURACAOE = 'MODULO_PETICIONAMENTO_ID_SERIE_PROCURACAO_ELETRONICA_ESPECIAL';
    public static $MD_PET_ID_SERIE_PROCURACAOS = 'MODULO_PETICIONAMENTO_ID_SERIE_PROCURACAO_ELETRONICA_SIMPLES';
    public static $MD_PET_ID_SERIE_REVOGACAO = 'MODULO_PETICIONAMENTO_ID_SERIE_PROCURACAO_REVOGACAO';
    public static $MD_PET_ID_SERIE_RENUNCIA = 'MODULO_PETICIONAMENTO_ID_SERIE_PROCURACAO_RENUNCIA';
    public static $MD_PET_ID_SERIE_ENCERRAMENTO = 'MODULO_PETICIONAMENTO_ID_SERIE_ENCERRAMENTO';
    public static $MD_PET_ID_SERIE_VINC_SUSPENSAO = 'MODULO_PETICIONAMENTO_ID_SERIE_VINC_SUSPENSAO';
    public static $MD_PET_ID_SERIE_VINC_RESTABELECIMENTO = 'MODULO_PETICIONAMENTO_ID_SERIE_VINC_RESTABELECIMENTO';

    protected function getHistoricoVersoes()
    {
        return $this->historicoVersoes;
    }

    public function __construct()
    {
        parent::__construct();
    }

    protected function inicializarObjInfraIBanco()
    {
        return BancoSEI::getInstance();
    }

    private function inicializar($strTitulo)
    {
        ini_set('max_execution_time', '0');
        ini_set('memory_limit', '-1');
        @ini_set('zlib.output_compression', '0');
        @ini_set('implicit_flush', '1');
        ob_implicit_flush();

        InfraDebug::getInstance()->setBolLigado(true);
        InfraDebug::getInstance()->setBolDebugInfra(true);
        InfraDebug::getInstance()->setBolEcho(true);
        InfraDebug::getInstance()->limpar();

        $this->numSeg = InfraUtil::verificarTempoProcessamento();

        $this->logar($strTitulo);
    }

    private function logar($strMsg)
    {
        InfraDebug::getInstance()->gravar($strMsg);
        flush();
    }

    private function finalizar($strMsg = null, $bolErro = false)
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
            $this->inicializar('INICIANDO A INSTALA��O/ATUALIZA��O DO ' . $this->nomeDesteModulo . ' NO SEI VERS�O ' . SEI_VERSAO);

            //checando BDs suportados
            if (!(BancoSEI::getInstance() instanceof InfraMySql) &&
                !(BancoSEI::getInstance() instanceof InfraSqlServer) &&
                !(BancoSEI::getInstance() instanceof InfraOracle)) {
                $this->finalizar('BANCO DE DADOS N�O SUPORTADO: ' . get_parent_class(BancoSEI::getInstance()), true);
            }

            //testando versao do framework
            $numVersaoInfraRequerida = '1.532.3';
            $versaoInfraFormatada = (int)str_replace('.', '', VERSAO_INFRA);
            $versaoInfraReqFormatada = (int)str_replace('.', '', $numVersaoInfraRequerida);

            if ($versaoInfraFormatada < $versaoInfraReqFormatada) {
                $this->finalizar('VERS�O DO FRAMEWORK PHP INCOMPAT�VEL (VERS�O ATUAL ' . VERSAO_INFRA . ', SENDO REQUERIDA VERS�O IGUAL OU SUPERIOR A ' . $numVersaoInfraRequerida . ')', true);
            }


            //checando permissoes na base de dados
            $objInfraMetaBD = new InfraMetaBD(BancoSEI::getInstance());

            if (count($objInfraMetaBD->obterTabelas('sei_teste')) == 0) {
                BancoSEI::getInstance()->executarSql('CREATE TABLE sei_teste (id ' . $objInfraMetaBD->tipoNumero() . ' null)');
            }

            BancoSEI::getInstance()->executarSql('DROP TABLE sei_teste');

            $objInfraParametro = new InfraParametro(BancoSEI::getInstance());

            $strVersaoModuloPeticionamento = $objInfraParametro->getValor($this->nomeParametroModulo, false);

            switch ($strVersaoModuloPeticionamento) {
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
            InfraDebug::getInstance()->setBolLigado(true);
            InfraDebug::getInstance()->setBolDebugInfra(true);
            InfraDebug::getInstance()->setBolEcho(true);
            throw new InfraException('Erro instalando/atualizando vers�o.', $e);
        }
    }

    protected function instalarv001()
    {

        $objInfraMetaBD = new InfraMetaBD(BancoSEI::getInstance());
        $this->logar('EXECUTANDO A INSTALA��O/ATUALIZA��O DA VERS�O 0.0.1 DO ' . $this->nomeDesteModulo . ' NA BASE DO SEI');

        $this->logar('CRIANDO A TABELA md_pet_tipo_processo');

        BancoSEI::getInstance()->executarSql('CREATE TABLE md_pet_tipo_processo ( 
            id_md_pet_tipo_processo ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL,
            id_tipo_procedimento ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL ,
            id_unidade ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL ,
            id_serie ' . $objInfraMetaBD->tipoNumero() . ' NULL , 
            id_hipotese_legal ' . $objInfraMetaBD->tipoNumero() . ' NULL ,
            orientacoes ' . $objInfraMetaBD->tipoTextoVariavel(500) . ' NOT NULL,
            sta_nivel_acesso  ' . $objInfraMetaBD->tipoTextoFixo(1) . ' NULL,
            sin_ii_proprio_usuario_externo ' . $objInfraMetaBD->tipoTextoFixo(1) . ' NULL,
            sin_ii_indicacao_direta ' . $objInfraMetaBD->tipoTextoFixo(1) . ' NULL,
            sin_ii_indic_direta_cpf_cnpj ' . $objInfraMetaBD->tipoTextoFixo(1) . ' NULL,
            sin_ii_indic_direta_contato ' . $objInfraMetaBD->tipoTextoFixo(1) . ' NULL,
            sin_na_usuario_externo ' . $objInfraMetaBD->tipoTextoFixo(1) . ' NULL,
            sin_na_padrao ' . $objInfraMetaBD->tipoTextoFixo(1) . ' NULL,
            sin_doc_gerado ' . $objInfraMetaBD->tipoTextoFixo(1) . ' NULL,
            sin_doc_externo ' . $objInfraMetaBD->tipoTextoFixo(1) . ' NULL,
            sin_ativo ' . $objInfraMetaBD->tipoTextoFixo(1) . ' NOT NULL 
            )'
        );

        $objInfraMetaBD->adicionarChavePrimaria('md_pet_tipo_processo', 'pk_md_pet_tipo_processo', array('id_md_pet_tipo_processo'));
        $objInfraMetaBD->adicionarChaveEstrangeira('fk_pet_tp_proc_tipo_proc_01', 'md_pet_tipo_processo', array('id_tipo_procedimento'), 'tipo_procedimento', array('id_tipo_procedimento'));
        $objInfraMetaBD->adicionarChaveEstrangeira('fk_pet_tp_proc_unidade_02', 'md_pet_tipo_processo', array('id_unidade'), 'unidade', array('id_unidade'));
        $objInfraMetaBD->adicionarChaveEstrangeira('fk_pet_tp_proc_serie_03', 'md_pet_tipo_processo', array('id_serie'), 'serie', array('id_serie'));
        $objInfraMetaBD->adicionarChaveEstrangeira('fk_pet_tp_proc_hip_legal_04', 'md_pet_tipo_processo', array('id_hipotese_legal'), 'hipotese_legal', array('id_hipotese_legal'));

        $this->logar('CRIANDO A SEQUENCE seq_md_pet_tipo_processo');
        BancoSEI::getInstance()->criarSequencialNativa('seq_md_pet_tipo_processo', 1);

        $this->logar('CRIANDO A TABELA md_pet_rel_tp_processo_serie');

        BancoSEI::getInstance()->executarSql('CREATE TABLE md_pet_rel_tp_processo_serie ( 
            id_md_pet_tipo_processo ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL,
            id_serie ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL
            )'
        );

        $objInfraMetaBD->adicionarChavePrimaria('md_pet_rel_tp_processo_serie', 'pk1_md_pet_rel_tp_proc_serie', array('id_md_pet_tipo_processo', 'id_serie'));
        $objInfraMetaBD->adicionarChaveEstrangeira('fk1_md_pet_rel_tp_proc_serie', 'md_pet_rel_tp_processo_serie', array('id_md_pet_tipo_processo'), 'md_pet_tipo_processo', array('id_md_pet_tipo_processo'));
        $objInfraMetaBD->adicionarChaveEstrangeira('fk2_md_pet_rel_tp_proc_serie', 'md_pet_rel_tp_processo_serie', array('id_serie'), 'serie', array('id_serie'));

        $this->logar('CRIANDO A TABELA md_pet_tp_processo_orientacoes');

        BancoSEI::getInstance()->executarSql('CREATE TABLE md_pet_tp_processo_orientacoes ( 
            id_md_pet_tp_proc_orientacoes ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL,
            id_conjunto_estilos ' . $objInfraMetaBD->tipoNumero() . ' NULL,
            orientacoes_gerais ' . $objInfraMetaBD->tipoTextoGrande() . ' NOT NULL 
            )'
        );

        $objInfraMetaBD->adicionarChavePrimaria('md_pet_tp_processo_orientacoes', 'pk_md_pet_tp_proc_orient', array('id_md_pet_tp_proc_orientacoes'));
        $objInfraMetaBD->adicionarChaveEstrangeira('fk_md_pet_tp_proc_or_cj_est', 'md_pet_tp_processo_orientacoes', array('id_conjunto_estilos'), 'conjunto_estilos', array('id_conjunto_estilos'));

        $this->logar('CRIANDO A TABELA md_pet_ext_arquivo_perm');

        BancoSEI::getInstance()->executarSql('CREATE TABLE md_pet_ext_arquivo_perm ( 
            id_md_pet_ext_arquivo_perm ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL,
            id_arquivo_extensao ' . $objInfraMetaBD->tipoNumero() . ' NULL ,
            sin_principal ' . $objInfraMetaBD->tipoTextoFixo(1) . ' NOT NULL,
            sin_ativo ' . $objInfraMetaBD->tipoTextoFixo(1) . ' NOT NULL )'
        );

        $objInfraMetaBD->adicionarChavePrimaria('md_pet_ext_arquivo_perm', 'pk_md_pet_ext_arquivo_perm', array('id_md_pet_ext_arquivo_perm'));
        $objInfraMetaBD->adicionarChaveEstrangeira('fk1_md_pet_ext_arquivo_perm', 'md_pet_ext_arquivo_perm', array('id_arquivo_extensao'), 'arquivo_extensao', array('id_arquivo_extensao'));


        $this->logar('CRIANDO A SEQUENCE seq_md_pet_ext_arquivo_perm');
        BancoSEI::getInstance()->criarSequencialNativa('seq_md_pet_ext_arquivo_perm', 1);


        $this->logar('CRIANDO A TABELA md_pet_tamanho_arquivo');

        BancoSEI::getInstance()->executarSql('CREATE TABLE md_pet_tamanho_arquivo ( 
            id_md_pet_tamanho_arquivo ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL,
            valor_doc_principal ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL,
            valor_doc_complementar ' . $objInfraMetaBD->tipoNumero() . '  NULL,
            sin_ativo ' . $objInfraMetaBD->tipoTextoFixo(1) . ' NOT NULL ) '
        );

        $objInfraMetaBD->adicionarChavePrimaria('md_pet_tamanho_arquivo', 'pk_md_pet_tamanho_arquivo', array('id_md_pet_tamanho_arquivo'));


        $this->logar('INSERINDO Tamanho de Arquivo para Doc Principal e Doc Complementar');

        $objMdPetTamanhoArquivoDTO = new MdPetTamanhoArquivoDTO();
        $objMdPetTamanhoArquivoRN = new MdPetTamanhoArquivoRN();
        $objMdPetTamanhoArquivoDTO->retTodos();
        $objMdPetTamanhoArquivoDTO->setNumValorDocPrincipal('5');
        $objMdPetTamanhoArquivoDTO->setNumValorDocComplementar('10');
        $objMdPetTamanhoArquivoDTO->setNumIdTamanhoArquivo(MdPetTamanhoArquivoRN::$ID_FIXO_TAMANHO_ARQUIVO);
        $objMdPetTamanhoArquivoDTO->setStrSinAtivo('S');
        $objMdPetTamanhoArquivoRN->cadastrar($objMdPetTamanhoArquivoDTO);


        $this->logar('CRIANDO A TABELA md_pet_indisponibilidade');

        BancoSEI::getInstance()->executarSql('CREATE TABLE md_pet_indisponibilidade ( 
            id_md_pet_indisponibilidade ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL,
            dth_inicio ' . $objInfraMetaBD->tipoDataHora() . ' NOT NULL,
            dth_fim ' . $objInfraMetaBD->tipoDataHora() . ' NOT NULL,
            resumo_indisponibilidade ' . $objInfraMetaBD->tipoTextoVariavel(500) . ' NULL,
            sin_prorrogacao ' . $objInfraMetaBD->tipoTextoFixo(1) . ' NOT NULL,
            sin_ativo ' . $objInfraMetaBD->tipoTextoFixo(1) . ' NOT NULL ) '
        );

        $objInfraMetaBD->adicionarChavePrimaria('md_pet_indisponibilidade', 'pk_md_pet_indisponibilidade', array('id_md_pet_indisponibilidade'));


        $this->logar('CRIANDO A SEQUENCE seq_md_pet_indisponibilidade');
        BancoSEI::getInstance()->criarSequencialNativa('seq_md_pet_indisponibilidade', 1);


        $this->logar('CRIANDO A TABELA md_pet_indisp_doc');

        BancoSEI::getInstance()->executarSql('CREATE TABLE md_pet_indisp_doc (
            id_md_pet_indisp_doc ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL,
            id_md_pet_indisponibilidade ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL,
            id_unidade ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL,
            id_usuario ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL,
            id_documento ' . $objInfraMetaBD->tipoNumeroGrande() . ' NULL,
            id_acesso_externo ' . $objInfraMetaBD->tipoNumero() . ' NULL,
            dth_inclusao ' . $objInfraMetaBD->tipoDataHora() . ' NOT NULL,
            sin_ativo ' . $objInfraMetaBD->tipoTextoFixo(1) . ' NOT NULL 
            )'
        );

        $objInfraMetaBD->adicionarChavePrimaria('md_pet_indisp_doc', 'pk_md_pet_indisp_doc', array('id_md_pet_indisp_doc'));
        $objInfraMetaBD->adicionarChaveEstrangeira('fk_md_pet_indisp_doc_01', 'md_pet_indisp_doc', array('id_md_pet_indisponibilidade'), 'md_pet_indisponibilidade', array('id_md_pet_indisponibilidade'));
        $objInfraMetaBD->adicionarChaveEstrangeira('fk_md_pet_indisp_doc_02', 'md_pet_indisp_doc', array('id_unidade'), 'unidade', array('id_unidade'));
        $objInfraMetaBD->adicionarChaveEstrangeira('fk_md_pet_indisp_doc_03', 'md_pet_indisp_doc', array('id_usuario'), 'usuario', array('id_usuario'));
        $objInfraMetaBD->adicionarChaveEstrangeira('fk_md_pet_indisp_doc_04', 'md_pet_indisp_doc', array('id_documento'), 'documento', array('id_documento'));
        $objInfraMetaBD->adicionarChaveEstrangeira('fk_md_pet_indisp_doc_05', 'md_pet_indisp_doc', array('id_acesso_externo'), 'acesso_externo', array('id_acesso_externo'));


        $this->logar('CRIANDO A SEQUENCE seq_md_pet_indisp_doc');
        BancoSEI::getInstance()->criarSequencialNativa('seq_md_pet_indisp_doc', 1);


        $this->logar('ADICIONANDO PAR�METRO ' . $this->nomeParametroModulo . ' NA TABELA infra_parametro PARA CONTROLAR A VERS�O DO M�DULO');
        BancoSEI::getInstance()->executarSql('INSERT INTO infra_parametro (valor, nome ) VALUES( \'0.0.1\',  \'' . $this->nomeParametroModulo . '\' )');

        $this->logar('INSTALA��O/ATUALIZA��O DA VERS�O ' . $this->versaoAtualDesteModulo . ' DO ' . $this->nomeDesteModulo . ' REALIZADA COM SUCESSO NA BASE DO SEI');
    }

    protected function instalarv002()
    {

        $objInfraMetaBD = new InfraMetaBD(BancoSEI::getInstance());
        $this->logar('EXECUTANDO A INSTALA��O/ATUALIZA��O DA VERS�O 0.0.2 DO ' . $this->nomeDesteModulo . ' NA BASE DO SEI');

        $this->logar('CRIANDO A TABELA md_pet_usu_externo_menu');

        BancoSEI::getInstance()->executarSql('CREATE TABLE md_pet_usu_externo_menu (  
            id_md_pet_usu_externo_menu ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL,
            id_conjunto_estilos ' . $objInfraMetaBD->tipoNumero() . ' NULL ,
            nome ' . $objInfraMetaBD->tipoTextoVariavel(30) . ' NOT NULL ,
            tipo ' . $objInfraMetaBD->tipoTextoFixo(1) . ' NOT NULL ,
            url ' . $objInfraMetaBD->tipoTextoVariavel(2083) . ' NULL ,
            conteudo_html ' . $objInfraMetaBD->tipoTextoGrande() . ' NULL,
            sin_ativo  ' . $objInfraMetaBD->tipoTextoFixo(1) . ' NOT NULL 
            )'
        );

        $objInfraMetaBD->adicionarChavePrimaria('md_pet_usu_externo_menu', 'pk_md_pet_usu_externo_menu', array('id_md_pet_usu_externo_menu'));
        $objInfraMetaBD->adicionarChaveEstrangeira('fk_md_pet_menu_cj_est_01', 'md_pet_usu_externo_menu', array('id_conjunto_estilos'), 'conjunto_estilos', array('id_conjunto_estilos'));


        $this->logar('CRIANDO A SEQUENCE seq_md_pet_usu_externo_menu');
        BancoSEI::getInstance()->criarSequencialNativa('seq_md_pet_usu_externo_menu', 1);


        //INSERCAO DE DOIS NOVOS MODELOS DE EMAIL NO MENU E-MAILS DO SISTEMA
        $this->logar('INSERINDO EMAILS MD_PET_CONFIRMACAO_PETICIONAMENTO_USUARIO_EXTERNO e MD_PET_ALERTA_PETICIONAMENTO_UNIDADES NA TABELA email_sistema');

        //Parametrizar Email de Alerta �s Unidades
        $conteudo1 = "      :: Este � um e-mail autom�tico ::

O Usu�rio Externo @nome_usuario_externo@ (@email_usuario_externo@) efetivou o Peticionamento Eletr�nico do tipo @tipo_peticionamento@ (@tipo_processo@), no �mbito do processo n� @processo@, conforme disposto no Recibo Eletr�nico de Protocolo SEI n� @documento_recibo_eletronico_de_protocolo@.

O mencionado processo se encontra aberto em sua Unidade (@sigla_unidade_abertura_do_processo@). Entre no SEI e confira! Caso n�o seja de compet�ncia de sua Unidade, verifique se j� est� aberto na Unidade correta e, do contr�rio, envie-o para a Unidade competente para que seja devidamente tratado.


@sigla_orgao@
@descricao_orgao@
@sitio_internet_orgao@

ATEN��O: As informa��es contidas neste e-mail, incluindo seus anexos, podem ser restritas apenas � pessoa ou entidade para a qual foi endere�ada. Se voc� n�o � o destinat�rio ou a pessoa respons�vel por encaminhar esta mensagem ao destinat�rio, voc� est�, por meio desta, notificado que n�o dever� rever, retransmitir, imprimir, copiar, usar ou distribuir esta mensagem ou quaisquer anexos. Caso voc� tenha recebido esta mensagem por engano, por favor, contate o remetente imediatamente e em seguida apague esta mensagem.";

        $maxIdEmailSistemaAlertaUnidades = $this->retornarMaxIdEmailSistema();

        $insert1 = "INSERT INTO email_sistema
            (id_email_sistema,
              descricao,
              de,
              para,
              assunto,
              conteudo,
              sin_ativo,
            id_email_sistema_modulo
            )
        VALUES
            (" . $maxIdEmailSistemaAlertaUnidades . ",
              'Peticionamento Eletr�nico - Alerta �s Unidades',
              '@sigla_sistema@ <@email_sistema@>',
              '@emails_unidade@',
              'SEI Peticionamento Eletr�nico - Processo n� @processo@',
              '" . $conteudo1 . "',
              'S',
            'MD_PET_ALERTA_PETICIONAMENTO_UNIDADES'
            )";
        BancoSEI::getInstance()->executarSql($insert1);

        //Parametrizar Email de Confirma��o ao Usuario Externo
        $conteudo2 = "      :: Este � um e-mail autom�tico ::

Prezado(a) @nome_usuario_externo@,

Este e-mail confirma a realiza��o do Peticionamento Eletr�nico do tipo @tipo_peticionamento@ no SEI-@sigla_orgao@, no �mbito do processo n� @processo@, conforme disposto no Recibo Eletr�nico de Protocolo SEI n� @documento_recibo_eletronico_de_protocolo@.

Caso no futuro precise realizar novo peticionamento, sempre acesse a �rea destinada aos Usu�rios Externos no SEI-@sigla_orgao@ destacada em seu Portal na Internet ou acesse diretamente o link a seguir: @link_login_usuario_externo@


@sigla_orgao@
@descricao_orgao@
@sitio_internet_orgao@

ATEN��O: As informa��es contidas neste e-mail, incluindo seus anexos, podem ser restritas apenas � pessoa ou entidade para a qual foi endere�ada. Se voc� n�o � o destinat�rio ou a pessoa respons�vel por encaminhar esta mensagem ao destinat�rio, voc� est�, por meio desta, notificado que n�o dever� rever, retransmitir, imprimir, copiar, usar ou distribuir esta mensagem ou quaisquer anexos. Caso voc� tenha recebido esta mensagem por engano, por favor, contate o remetente imediatamente e em seguida apague esta mensagem.";

        $maxIdEmailSistemaConfirmacaoUsuarioExterno = $this->retornarMaxIdEmailSistema();

        $insert2 = "INSERT INTO email_sistema
        (id_email_sistema,
              descricao,
              de,
              para,
              assunto,
              conteudo,
              sin_ativo,
            id_email_sistema_modulo
        )
        VALUES
        (" . $maxIdEmailSistemaConfirmacaoUsuarioExterno . ",
              'Peticionamento Eletr�nico - Confirma��o ao Usu�rio Externo',
              '@sigla_sistema@ <@email_sistema@>',
              '@email_usuario_externo@',
              'SEI - Confirma��o de Peticionamento Eletr�nico (Processo n� @processo@)',
              '" . $conteudo2 . "',
              'S',
            'MD_PET_CONFIRMACAO_PETICIONAMENTO_USUARIO_EXTERNO'
        )";

        BancoSEI::getInstance()->executarSql($insert2);


        //Tabelas relacionais com Tipos de Contatos permitidos para Cadastro e para Sele��o
        $this->logar('CRIANDO A TABELA md_pet_rel_tp_ctx_contato');

        BancoSEI::getInstance()->executarSql('CREATE TABLE md_pet_rel_tp_ctx_contato (
            id_tipo_contato ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL,
            sin_cadastro_interessado ' . $objInfraMetaBD->tipoTextoFixo(1) . ' NOT NULL,
            sin_selecao_interessado ' . $objInfraMetaBD->tipoTextoFixo(1) . ' NOT NULL,
            id_md_pet_rel_tp_ctx_contato ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL
            )'
        );

        $objInfraMetaBD->adicionarChavePrimaria('md_pet_rel_tp_ctx_contato', 'pk1_md_pet_rel_tp_ctx_cont', array('id_md_pet_rel_tp_ctx_contato'));
        $objInfraMetaBD->adicionarChaveEstrangeira('fk_md_pet_rel_tp_ctx_cont_1', 'md_pet_rel_tp_ctx_contato', array('id_tipo_contato'), 'tipo_contato', array('id_tipo_contato'));


        $this->logar('CRIANDO A SEQUENCE seq_md_pet_rel_tp_ctx_contato');
        BancoSEI::getInstance()->criarSequencialNativa('seq_md_pet_rel_tp_ctx_contato', 1);


        //Tabelas referentes ao Recibo Eletronico de Protocolo
        $this->logar('CRIANDO A TABELA md_pet_rel_recibo_protoc');

        BancoSEI::getInstance()->executarSql('CREATE TABLE md_pet_rel_recibo_protoc ( 
            id_md_pet_rel_recibo_protoc ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL,
            id_protocolo ' . $objInfraMetaBD->tipoNumeroGrande() . ' NOT NULL,
            id_protocolo_relacionado ' . $objInfraMetaBD->tipoNumeroGrande() . ' NULL,
            id_usuario ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL,
            ip_usuario ' . $objInfraMetaBD->tipoTextoVariavel(500) . ' NOT NULL,
            data_hora_recebimento_final ' . $objInfraMetaBD->tipoDataHora() . ' NULL,
            sin_ativo ' . $objInfraMetaBD->tipoTextoFixo(1) . ' NOT NULL,
            sta_tipo_peticionamento ' . $objInfraMetaBD->tipoTextoVariavel(1) . ' NULL 
            )'
        );

        $objInfraMetaBD->adicionarChavePrimaria('md_pet_rel_recibo_protoc', 'pk1_md_pet_rel_recibo_protoc', array('id_md_pet_rel_recibo_protoc'));
        $objInfraMetaBD->adicionarChaveEstrangeira('fk1_md_pet_rel_recibo_protoc', 'md_pet_rel_recibo_protoc', array('id_protocolo'), 'protocolo', array('id_protocolo'));
        $objInfraMetaBD->adicionarChaveEstrangeira('fk2_md_pet_rel_recibo_protoc', 'md_pet_rel_recibo_protoc', array('id_usuario'), 'usuario', array('id_usuario'));
        $objInfraMetaBD->adicionarChaveEstrangeira('fk3_md_pet_rel_recibo_protoc', 'md_pet_rel_recibo_protoc', array('id_protocolo_relacionado'), 'protocolo', array('id_protocolo'));


        //Tabelas referentes ao Recibo Eletronico de Protocolo
        $this->logar('CRIANDO A SEQUENCE seq_md_pet_rel_recibo_protoc');
        BancoSEI::getInstance()->criarSequencialNativa('seq_md_pet_rel_recibo_protoc', 1);


        //Tabelas de recibo X documentos
        $this->logar('CRIANDO A TABELA md_pet_rel_recibo_docanexo');

        BancoSEI::getInstance()->executarSql('CREATE TABLE md_pet_rel_recibo_docanexo (
            id_md_pet_rel_recibo_docanexo ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL,
            id_md_pet_rel_recibo_protoc ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL,
            formato_documento ' . $objInfraMetaBD->tipoTextoFixo(1) . ' NULL,
            id_documento ' . $objInfraMetaBD->tipoNumeroGrande() . ' NULL,
            id_anexo ' . $objInfraMetaBD->tipoNumero() . ' NULL,
            classificacao_documento ' . $objInfraMetaBD->tipoTextoFixo(1) . ' NOT NULL 
            )'
        );

        $objInfraMetaBD->adicionarChavePrimaria('md_pet_rel_recibo_docanexo', 'pk1_md_pet_rel_recibo_docanexo', array('id_md_pet_rel_recibo_docanexo'));
        $objInfraMetaBD->adicionarChaveEstrangeira('fk_pet_rel_recibo_docanexo_01', 'md_pet_rel_recibo_docanexo', array('id_md_pet_rel_recibo_protoc'), 'md_pet_rel_recibo_protoc', array('id_md_pet_rel_recibo_protoc'));
        $objInfraMetaBD->adicionarChaveEstrangeira('fk_pet_rel_recibo_docanexo_02', 'md_pet_rel_recibo_docanexo', array('id_documento'), 'documento', array('id_documento'));
        $objInfraMetaBD->adicionarChaveEstrangeira('fk_pet_rel_recibo_docanexo_03', 'md_pet_rel_recibo_docanexo', array('id_anexo'), 'anexo', array('id_anexo'));


        //Tabelas de recibo X documentos
        $this->logar('CRIANDO A SEQUENCE seq_md_pet_rel_recibo_docanexo');
        BancoSEI::getInstance()->criarSequencialNativa('seq_md_pet_rel_recibo_docanexo', 1);


        $this->logar('ATUALIZANDO PAR�METRO ' . $this->nomeParametroModulo . ' NA TABELA infra_parametro PARA CONTROLAR A VERS�O DO M�DULO');
        BancoSEI::getInstance()->executarSql('UPDATE infra_parametro SET valor = \'0.0.2\' WHERE nome = \'' . $this->nomeParametroModulo . '\' ');

        $this->logar('INSTALA��O/ATUALIZA��O DA VERS�O ' . $this->versaoAtualDesteModulo . ' DO ' . $this->nomeDesteModulo . ' REALIZADA COM SUCESSO NA BASE DO SEI');
    }

    protected function instalarv100()
    {

        $objInfraMetaBD = new InfraMetaBD(BancoSEI::getInstance());


        $this->logar('EXECUTANDO A INSTALA��O/ATUALIZA��O DA VERS�O 1.0.3 DO ' . $this->nomeDesteModulo . ' NA BASE DO SEI');


        $this->logar('CRIANDO A TABELA md_pet_hipotese_legal');

        BancoSEI::getInstance()->executarSql('CREATE TABLE md_pet_hipotese_legal (
                id_md_pet_hipotese_legal ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL 
            )');

        $objInfraMetaBD->adicionarChavePrimaria('md_pet_hipotese_legal', 'pk_md_pet_hipotese_legal', array('id_md_pet_hipotese_legal'));
        if (!BancoSEI::getInstance() instanceof InfraOracle) {
            $objInfraMetaBD->adicionarChaveEstrangeira('fk_id_md_pet_hip_legal1', 'md_pet_hipotese_legal', array('id_md_pet_hipotese_legal'), 'hipotese_legal', array('id_hipotese_legal'));
        }

        $this->logar('DROP DA COLUNA id_unidade (N�o � mais unidade �nica. Agora ter� op��o para Peticionamento de Processo Novo para M�ltiplas Unidades)');

        $objInfraMetaBD->excluirChaveEstrangeira('md_pet_tipo_processo', 'fk_pet_tp_proc_unidade_02');
        $objInfraMetaBD->excluirIndice('md_pet_tipo_processo', 'fk_pet_tp_proc_unidade_02');


        BancoSEI::getInstance()->executarSql('ALTER TABLE md_pet_tipo_processo DROP COLUMN id_unidade');


        $this->logar('CRIANDO A TABELA md_pet_rel_tp_processo_unid (para permitir multiplas unidades)');

        BancoSEI::getInstance()->executarSql('CREATE TABLE md_pet_rel_tp_processo_unid ( 
                id_md_pet_tipo_processo ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL,
                id_unidade ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL,
                sta_tp_unidade ' . $objInfraMetaBD->tipoTextoFixo(1) . ' NOT NULL
                )'
        );


        //Tabelas Abaixo � o problema da modifica��o da PK (Pk deixou de ser composta e passou a ter SEQ)
        $this->logar('RECRIANDO A TABELA md_pet_rel_tp_processo_serie (renomeada para md_pet_rel_tp_proc_serie)');

        BancoSEI::getInstance()->executarSql('DROP TABLE md_pet_rel_tp_processo_serie');

        BancoSEI::getInstance()->executarSql('CREATE TABLE md_pet_rel_tp_proc_serie ( 
                id_md_pet_rel_tipo_proc ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL,
                id_md_pet_tipo_processo ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL,
                id_serie ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL,
                sta_tp_doc ' . $objInfraMetaBD->tipoTextoFixo(1) . ' NOT NULL 
                )'
        );

        //tabela SEQ
        $objInfraMetaBD->adicionarChavePrimaria('md_pet_rel_tp_proc_serie', 'pk_id_md_pet_rel_tipo_proc', array('id_md_pet_rel_tipo_proc'));
        $objInfraMetaBD->adicionarChaveEstrangeira('fk_md_pet_rel_tp_proc_serie1', 'md_pet_rel_tp_proc_serie', array('id_md_pet_tipo_processo'), 'md_pet_tipo_processo', array('id_md_pet_tipo_processo'));
        $objInfraMetaBD->adicionarChaveEstrangeira('fk_md_pet_rel_tp_proc_serie2', 'md_pet_rel_tp_proc_serie', array('id_serie'), 'serie', array('id_serie'));


        $this->logar('CRIANDO A SEQUENCE seq_md_pet_rel_tp_proc_serie');
        BancoSEI::getInstance()->criarSequencialNativa('seq_md_pet_rel_tp_proc_serie', 1);


        //CRIANDO NOVO TIPO DE DOCUMENTO "Recibo Eletr�nico de Protocolo"
        $this->logar('CRIANDO MODELO "Modulo_Peticionamento_Recibo_Eletronico_Protocolo"');
        $modeloRN = new ModeloRN();
        $modeloDTO = new ModeloDTO();
        $modeloDTO->setNumIdModelo(null);
        $modeloDTO->retTodos();
        $modeloDTO->setStrNome('Modulo_Peticionamento_Recibo_Eletronico_Protocolo');
        $modeloDTO->setStrSinAtivo('S');
        $modeloDTO = $modeloRN->cadastrar($modeloDTO);


        //adicionando as se��es do modelo: Corpo de Texto e Rodap�
        $this->logar('CRIANDO SE�AO DO MODELO - Corpo do Texto');
        $secaoModeloRN = new SecaoModeloRN();

        $secaoModeloCorpoTextoDTO = new SecaoModeloDTO();
        $secaoModeloCorpoTextoDTO->retTodos();
        $secaoModeloCorpoTextoDTO->setNumIdSecaoModelo(null);
        $secaoModeloCorpoTextoDTO->setNumIdModelo($modeloDTO->getNumIdModelo());
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

        $secaoModeloCorpoTextoDTO = $secaoModeloRN->cadastrar($secaoModeloCorpoTextoDTO);


        //secao do rodap�
        $this->logar('CRIANDO SE�AO DO MODELO - Rodap�');
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

        $secaoModeloRodapeDTO->setNumIdModelo($modeloDTO->getNumIdModelo());
        $secaoModeloRodapeDTO->setStrNome('Rodap�');
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

        $secaoModeloRodapeDTO = $secaoModeloRN->cadastrar($secaoModeloRodapeDTO);

        //Criar o Grupo de Tipo de Documento "Internos do Sistema".
        $grupoSerieRN = new GrupoSerieRN();

        if (BancoSEI::getInstance() instanceof InfraMySql) {

            //verificando antes a situa�ao da tabela seq_grupo_serie
            $arrDados = BancoSEI::getInstance()->consultarSql('SELECT * FROM seq_grupo_serie ORDER BY id DESC LIMIT 1 ');

            $grupoSerieDTOLista = new GrupoSerieDTO();
            $grupoSerieDTOLista->retTodos();
            $grupoSerieDTOLista->setOrd("IdGrupoSerie", InfraDTO::$TIPO_ORDENACAO_DESC);
            $grupoSerieDTOLista->setNumMaxRegistrosRetorno(1);

            $arrListaGrupoSerie = $grupoSerieRN->listarRN0778($grupoSerieDTOLista);

            //ja tem registro na SEQ, insere apenas se ID da SEQ estiver incorreto
            if ($arrDados != null && count($arrDados) > 0) {

                if ($arrDados[0]['id'] < $arrListaGrupoSerie[0]->getNumIdGrupoSerie()) {

                    //INSERT para garantir a SEQ na posi�ao correta
                    BancoSEI::getInstance()->executarSql('INSERT INTO seq_grupo_serie ( id ) VALUES ( ' . $arrListaGrupoSerie[0]->getNumIdGrupoSerie() . ') ');
                }
            } //nao tem registro na SEQ ainda, colocar o ID do grupo_serie mais atual
            else {

                //INSERT para garantir a SEQ na posi�ao correta
                BancoSEI::getInstance()->executarSql('INSERT INTO seq_grupo_serie ( id ) VALUES ( ' . $arrListaGrupoSerie[0]->getNumIdGrupoSerie() . ') ');
            }
        }


        $this->logar('CRIANDO GRUPO DE TIPO DE DOCUMENTO "Internos do Sistema"');
        $grupoSerieDTO = new GrupoSerieDTO();
        $grupoSerieDTO->retTodos();
        $grupoSerieDTO->setNumIdGrupoSerie(null);
        $grupoSerieDTO->setStrNome('Internos do Sistema');
        $grupoSerieDTO->setStrDescricao('Tipos de Documentos internos do sistema');
        $grupoSerieDTO->setStrSinAtivo('S');
        $grupoSerieDTO = $grupoSerieRN->cadastrarRN0775($grupoSerieDTO);


        //Criar o Tipo de Documento "Recibo Eletr�nico de Protocolo"
        $this->logar('CRIANDO TIPO DE DOCUMENTO Recibo Eletr�nico de Protocolo');
        $serieDTO = new SerieDTO();
        $serieDTO->retTodos();
        $serieRN = new SerieRN();

        $serieDTO->setNumIdSerie(null);
        $serieDTO->setNumIdGrupoSerie($grupoSerieDTO->getNumIdGrupoSerie());
        $serieDTO->setStrStaNumeracao(SerieRN::$TN_SEM_NUMERACAO);
        $serieDTO->setStrStaAplicabilidade(SerieRN::$TA_INTERNO);
        $serieDTO->setNumIdModeloEdoc(null);
        $serieDTO->setNumIdModelo($modeloDTO->getNumIdModelo());
        $serieDTO->setStrNome('Recibo Eletr�nico de Protocolo');
        $serieDTO->setStrDescricao('Utilizado para a gera��o autom�tica do Recibo Eletr�nico de Protocolo nos Peticionamentos Eletr�nicos realizados por Usu�rio Externo diretamente no Acesso Externo do SEI.');
        $serieDTO->setStrSinInteressado('S');
        $serieDTO->setStrSinDestinatario('N');
        $serieDTO->setStrSinAssinaturaPublicacao('S');
        $serieDTO->setStrSinInterno('S');
        $serieDTO->setStrSinAtivo('S');
        $serieDTO->setArrObjRelSerieAssuntoDTO(array());
        $serieDTO->setArrObjRelSerieVeiculoPublicacaoDTO(array());
        $serieDTO->setNumIdTipoFormulario(null);
        $serieDTO->setArrObjSerieRestricaoDTO(array());

        $serieDTO = $serieRN->cadastrarRN0642($serieDTO);


        $this->logar('ATUALIZANDO INFRA_PARAMETRO (' . MdPetIntSerieRN::$MD_PET_ID_SERIE_RECIBO . ')');
        $nomeParamIdSerie = MdPetIntSerieRN::$MD_PET_ID_SERIE_RECIBO;

        BancoSEI::getInstance()->executarSql('INSERT INTO infra_parametro ( valor, nome )  VALUES (\'' . $serieDTO->getNumIdSerie() . '\' , \'' . $nomeParamIdSerie . '\' ) ');


        $this->logar('ATUALIZANDO PAR�METRO ' . $this->nomeParametroModulo . ' NA TABELA infra_parametro PARA CONTROLAR A VERS�O DO M�DULO');
        BancoSEI::getInstance()->executarSql('UPDATE infra_parametro SET valor = \'1.0.3\' WHERE nome = \'' . $this->nomeParametroModulo . '\' ');

        $this->logar('INSTALA��O/ATUALIZA��O DA VERS�O ' . $this->versaoAtualDesteModulo . ' DO ' . $this->nomeDesteModulo . ' REALIZADA COM SUCESSO NA BASE DO SEI');
    }

    protected function instalarv104()
    {

        $objInfraMetaBD = new InfraMetaBD(BancoSEI::getInstance());


        $this->logar('EXECUTANDO A INSTALA��O/ATUALIZA��O DA VERS�O 1.0.4 DO ' . $this->nomeDesteModulo . ' NA BASE DO SEI');

        //Caso exista a coluna na tabela a instala��o � nova, ent�o n�o � necessario executar a migra��o de dados
        $colunasTabela = $objInfraMetaBD->obterColunasTabela('md_pet_rel_tp_ctx_contato', 'id_tipo_contato');

        if (count($colunasTabela) <= 0 || $colunasTabela[0]['column_name'] != 'id_tipo_contato') {

            $this->logar('ADICIONANDO A COLUNA id_tipo_contato NA TABELA md_pet_rel_tp_ctx_contato');
            $objInfraMetaBD->adicionarColuna('md_pet_rel_tp_ctx_contato', 'id_tipo_contato', $objInfraMetaBD->tipoNumero(), 'NOT NULL');

            $this->logar('ATUALIZANDO OS REGISTROS DA TABELA md_pet_rel_tp_ctx_contato');
            BancoSEI::getInstance()->executarSql('UPDATE md_pet_rel_tp_ctx_contato set id_tipo_contato = id_tipo_contexto_contato');

            $this->logar('EXCLUINDO A COLUNA id_tipo_contexto_contato DA TABELA md_pet_rel_tp_ctx_contato');
            $objInfraMetaBD->excluirColuna('md_pet_rel_tp_ctx_contato', 'id_tipo_contexto_contato');
        }

        if ($this->existeIdEmailSistemaPecitionamento()) {
            $this->atualizarIdEmailSistemaAlertaPecitionamento();
            $this->atualizarIdEmailSistemaConfirmacaoPeticionamento();
        }

        //inclusao de nova tarja de assinatura customizada, para uso pelo modulo peticionamento em caso de documento nato-digital
        $objTarjaAssinaturaDTO = new TarjaAssinaturaDTO();
        $objTarjaAssinaturaDTO->setStrStaTarjaAssinatura(MdPetAssinaturaRN::$TT_ASSINATURA_SENHA_PETICIONAMENTO);
        $objTarjaAssinaturaDTO->setStrTexto('<hr style="margin: 0 0 4px 0;" />  <table>    <tr>      <td>  @logo_assinatura@      </td>      <td>  <p style="margin:0;text-align: left; font-size:11pt;font-family: Calibri;">Documento assinado eletronicamente por <b>@nome_assinante@</b>, <b>@tratamento_assinante@</b>, em @data_assinatura@, �s @hora_assinatura@, conforme hor�rio oficial de Bras�lia, com fundamento no art. 6�, � 1�, do <a title="Acesse o Decreto" href="http://www.planalto.gov.br/ccivil_03/_Ato2015-2018/2015/Decreto/D8539.htm" target="_blank">Decreto n� 8.539, de 8 de outubro de 2015</a>.</p>      </td>    </tr>  </table>');
        $objTarjaAssinaturaDTO->setStrLogo('iVBORw0KGgoAAAANSUhEUgAAAFkAAAA8CAMAAAA67OZ0AAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAADTtpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+Cjx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDQuMi4yLWMwNjMgNTMuMzUyNjI0LCAyMDA4LzA3LzMwLTE4OjEyOjE4ICAgICAgICAiPgogPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4KICA8cmRmOkRlc2NyaXB0aW9uIHJkZjphYm91dD0iIgogICAgeG1sbnM6ZGM9Imh0dHA6Ly9wdXJsLm9yZy9kYy9lbGVtZW50cy8xLjEvIgogICAgeG1sbnM6eG1wUmlnaHRzPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvcmlnaHRzLyIKICAgIHhtbG5zOnBob3Rvc2hvcD0iaHR0cDovL25zLmFkb2JlLmNvbS9waG90b3Nob3AvMS4wLyIKICAgIHhtbG5zOklwdGM0eG1wQ29yZT0iaHR0cDovL2lwdGMub3JnL3N0ZC9JcHRjNHhtcENvcmUvMS4wL3htbG5zLyIKICAgeG1wUmlnaHRzOldlYlN0YXRlbWVudD0iIgogICBwaG90b3Nob3A6QXV0aG9yc1Bvc2l0aW9uPSIiPgogICA8ZGM6cmlnaHRzPgogICAgPHJkZjpBbHQ+CiAgICAgPHJkZjpsaSB4bWw6bGFuZz0ieC1kZWZhdWx0Ii8+CiAgICA8L3JkZjpBbHQ+CiAgIDwvZGM6cmlnaHRzPgogICA8ZGM6Y3JlYXRvcj4KICAgIDxyZGY6U2VxPgogICAgIDxyZGY6bGk+QWxiZXJ0byBCaWdhdHRpPC9yZGY6bGk+CiAgICA8L3JkZjpTZXE+CiAgIDwvZGM6Y3JlYXRvcj4KICAgPGRjOnRpdGxlPgogICAgPHJkZjpBbHQ+CiAgICAgPHJkZjpsaSB4bWw6bGFuZz0ieC1kZWZhdWx0Ii8+CiAgICA8L3JkZjpBbHQ+CiAgIDwvZGM6dGl0bGU+CiAgIDx4bXBSaWdodHM6VXNhZ2VUZXJtcz4KICAgIDxyZGY6QWx0PgogICAgIDxyZGY6bGkgeG1sOmxhbmc9IngtZGVmYXVsdCIvPgogICAgPC9yZGY6QWx0PgogICA8L3htcFJpZ2h0czpVc2FnZVRlcm1zPgogICA8SXB0YzR4bXBDb3JlOkNyZWF0b3JDb250YWN0SW5mbwogICAgSXB0YzR4bXBDb3JlOkNpQWRyRXh0YWRyPSIiCiAgICBJcHRjNHhtcENvcmU6Q2lBZHJDaXR5PSIiCiAgICBJcHRjNHhtcENvcmU6Q2lBZHJSZWdpb249IiIKICAgIElwdGM0eG1wQ29yZTpDaUFkclBjb2RlPSIiCiAgICBJcHRjNHhtcENvcmU6Q2lBZHJDdHJ5PSIiCiAgICBJcHRjNHhtcENvcmU6Q2lUZWxXb3JrPSIiCiAgICBJcHRjNHhtcENvcmU6Q2lFbWFpbFdvcms9IiIKICAgIElwdGM0eG1wQ29yZTpDaVVybFdvcms9IiIvPgogIDwvcmRmOkRlc2NyaXB0aW9uPgogPC9yZGY6UkRGPgo8L3g6eG1wbWV0YT4KICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAg
ICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgIAo8P3hwYWNrZXQgZW5kPSJ3Ij8+RO84nQAAAwBQTFRFamts+fn5mp6hc3Nz9fX1U1NTS0tKnaGk6unqzM3P7e3u8fHxuLm7/Pz8lZmc2dnZxcXGWlpavr29wsLCp6eniYmKhYaGZWZmkpaZ0dHS5eXlkZGSrq2utbW2XV1d4uHhfX1+sbGy1dXW3d3dqampgYGCjY2OyMnKYWJihYaIjY6RnZ2ejpGSra+xeHl7lZWVmJiYgoKFpKaptre5vb7Aurq8oaGikpSWmJufh4iKkZKVysrMtrq7ioyOdXZ4fn+ArrGywcLEzc7QiYqMt7W1/v/8mZqcxsbIpqqrZGFhztDSeXp7iIWGnJqalJKSf4CCg4B/amZmoaSm5+fmvLy6ys3OzMzL2tze3dzaa2hny8nH0M7NiYiGbG5v19jYWFVVcG5s2drcxMTD0dPUx8jJ/P79sbO1j46OmZWU1dfXhIKC1NLTd3h68fL0wsTGb3By+vf3YV1d2NjW7u7u6Ojpe3x9fHp54eLkxMLAvLq5/f39+vr63t7fXFtamZiW6urqzMnKwL+98PHvrKytq6qq7evpr62toKKkvr/BOzk42dvad3V06OjmpaSj5efnnZyblpWT/fz6ZWZo9/f3jYyKqquteXd47u3rhYSC5eTisbCueXh2qaimWlhXjImIY2Bfc3Bw////UFBP/v7+/v////7///3+g4SHaGlpYmNj8vPzZ2dn/vz9WFhYtbO0ztDPWltbbW9u/v7/xcPEiouLrayq4+Tms7S2VldX7/DyqKel+/z++Pj4+ff4cXBuuru7u7y+7+/vx8fH8/HysK+wXFxc/fv8s7OztrWzZWRio6Ohl5eZ1NTUZGRkraus2NbX4N/d0dDP3dzc9ff14ODg9/n4oaCg4eHf+/v76+vrQD4+7Ozs/f3/7evsRUJCvLy87vDtysvLXl9fzczNwsPDYGBgw7+/ysjJgH19gH9/29rbwMC/Tk1MlJCPoaCeX1tb6ufo4uPjx8fF5OPht7e3X15cuLe4tLKzn56f09TW1dXTYWJkh4eHZGJj3+Diq6urXLJJJAAAC8BJREFUeNqsmAtYE1cWgAcmJLwSwjMJAYxiQhIeITyEgCGiAioCaiqWaoCiFQVKtgWsJFRapEpFatuodetKHYaQkIiipZVWqqBQ64OqrduGuquVR1sDu62u69JdW/fOZCCJovjttyffl9yZ3PvfM2fOOffcC6UgJ1a5R1GeJI6OjvHx8TQgTCYzLiEsTCgU8qRSQcaN4VNsWWpsndep7u7u2NhY9+7UkpKSJFnqkApBIOTrufFgJDb2MUIQ4xLYAMnjSRf4+koEAoGupLcMdQtVRBs0JA3JImovpVKpUED6SAMCnZhLo1Dmrlzp8hhJxCQkJGRdGhA6nV5aWjrs7T08nJw8Ono6hD7aXZd2ml5ALygoGAb33QPvBs68ACsZIjXkAcBLmpH/RVC7H7xlaZ86qmTcgY47UsKbEW3LU4Mmx9tTJwWYGJFAeh4URXGc2/yUCqJTaGrLRlFi3khIAUMUCxl9Kjj4qFQo1WYeC27ie6KjSK+AMHIsuDu92qpq8wCK+P+6cdasGvRRM6G21yI9hJPdn+Z1vTCfJvZlNccIgQt6IIj2iZ0zjY+Q0SnfGvZ921EiMC645kKjxNOen06NTMaTdH5oklwhl8OHdyyhUWgJudOS+yG9HRl9RGWrzm/FKfRNHYZEWnyCdON0ZHa/Xv8kO9u9FJSlY3DNzclMmtD34rTkVr1xajKKpFgaVIcu9URkkKq7EFW3MEEiZk1L5hsfJqtfrP74lXK3LhTDqQy/r+uOTX7egIUVKbhKvmOGQ7dEKpaxpvN/Np/BsLdzWeJWkDMpi+reAv5NNftIsjjpEekXLgJ0bgUDapf2JIsFnIgj0+o8YkMGuQMtX8SkgbTpyGTSEcTkIuX6CsTcLJkyAlzmRvD1nR1lXhXcJNjl4fTxsBSO9Pfb6IwaFjG3UxxXrKDQHF9B0F+lAp5AOH5BnM5RyF5Gnk9vVbR3lMUmVcBHb05lDXwm4nbhYH/rJBmY1QWAKe65q+avX09CB1LFPMF4VZchWQxH6MdR834+1OZbFg0nKfQhdo5Dch0YcHYu7zFZ/Yk3yG+10blrHo3iGK4G/1JdUWoal6eLm4Hli25FEsSZcTVp0Nh5v+w4BBtbT9u4peFITF1dTMyN7ple8kkD8YL4fCv5mGZRPIWynhjRM0cs0bljHY9VySDo6OmP69sZTvfLZr6raA2iW5+/pjSKsvb34FWrqrZXsM0TobY7iD9iq3N4PLDyuhfxQTMWSHSSdSiJZHCokjIUrXdvw56tTX6uvXx9X9vwpM7Hopes2h7uHh14/LhIEiF0Jf7Y3TcyaGNndSITXDAD1oL/UVaWRCcIDZ8d1eATWgFBg1uD4c4RcpHrg3Z+Z97w5Bv7mFI3b3ag+73AwMAGXwFcSrWQO9oHrWTQ75M9NEdHmlAYdaRLlVYh0GUlgVXY2M+Ajur7onJhp0FA9ukMcsLJ+HM3r3WUht0mgixUnBTVRZA9bcmgc3k4M4FJCxNIujXrSnRiTokSLA16Bn8waGzcA27qI+9znUNuc3LyBp0t4b8yXrjiE2L4VhkcqrE0fduCgmysAeQT+oowaUKYQJecXcLlyETbx0NDIyNFIrZvmhkCZL9rqdedxsijk2QXmnROGUHew1FSSBPkwT47ncHK4UwPFUil4oQbHE4JJw3RdHVpcEGK9WN9ZG519vjs83OCJ1VxuSChlFmax/ZUKLdP6NzZ5/lIrnvh9rhOIpb0LigpgWfa+G0xoymILCt/KO7qhIK4UtYQVuzMT4AhHuEckjxPTxtrEM5IXVKhyxK4z1FEKGWzrOVAsbGpncypPrG2O61nYj6VSxxPKJX4+XFlsor0iJIkRUbPo2SAHPDH0qU6OV3HEbMS34WVUBa9vMvk0ONxcwC5aAR25pYvYQqSomoIdHXc9vmzWNnZiUNHbp6mh4TcPB9UgPvdfSc7skN0agzL7FEnzBKXSNxqeIPw0X6935ZQkS/EGEZYmM5+ueESiQJiEY/isSARxZ8UdbCULLf7A9TYtZ892ZCqE0jZPLFMXAIHHkNyZUFGqLU9z8mpiUz2QS7qgZ0lG1ekVwwGzSfywyrpOrwhj5L0GrCGf384npcIcny05dleEesEYhmHE6FMegC8R2Vm97e1tXViYPIu5Erbd+Q395bHQJ1kdg9R+ezwpWP2+0sql62IVYPprvID1FayI0FGetzHpTpAFqSmGfBnqykY58IKCL7FPvsVMkPkx/ZrMJBOZdZWEzlNtUNQipEN6RdmKSOBMujVwQdWMohnQmeE6hzMCkk8Eoy7vhYb3SU35+Z+Jce81ERyc6shqRCVxpqHPcSlKqwRKhNCoyYsjwXZkwMfrYhQrdam4kBtVyfU2jtXh+mMojWi/4Tj0VfVNwV5wp/BF6CabhSqrfUm+tln9lMT9Fxusgq/2Ws047/BbbU25HjacaK/CWO3oGhKi4n64zcqAnZIiw5EHp7QFEsXVCoB
3wjiH7ea+0l/vK+8rcFhkhwfz7SsI2UiTuOlzxcWRbpd2VcYXDx+5nDGT2zDQObezKob3x34MGSraX7tzoLdmffG6wu/smi9sWS9BqWaTIj/SoMJ+50/5mOa9Od4moWM9Cz02r9JPpZhvpoPm3cG5LgeXJzh+aXmVOXBwtU/wzPG8x1q859dQ/7mtTs/LM50sEQAO4nH5nV0SDo6/Li3blVwRposRQ5OTqXFncW7/Xlh5smcr/curjS8nfcnUu1yZ/jtmk085HDm4qVvbArVhsLUXtjMLULdvsjIW2qw2OZqQ0eH732/fUXcW6Dk2Qune1mmtCNTh/NW716c0rOtafM7r3+w695y5/pxTdHu0Zw7t5a9AW/R7jK+tyUneFkm4nPyuYNFZyYqgoGBakxAVVBeLpdfI14HTqbR4nBrqH68viY/p3rpTwfunN/00vszR+T5W7r276aP7ftg2R8av/sh22nxq3Dwpkbko7w1efvcpq7iJ27h5AvMhHmW6V9beKRYQ194STMUkK3xH3JgVakuehxaXfmcBzJj5iztjwuHzGcumRFSQWVBlRqx2wXZxYKVHEYk+BbcFVuaX9CasLSAZ4bmQ+oW0L25GbW6MVX1GE2tgpNFcWHzrNO5iR5YulJVzRjboXd5LbEJHe2oslHv2BRA1J4cFxcWbg2sayd5WLPlzDe7QEy0IN9v/sKbZFG/+MtyEJ1EtKOP6os+rPMEGVF/eHDT6jP1mSnPHFz2cvb1po8ub2k8//Xfzq35x19rRQc3vDOU8d7Oxg+e8WjMKfRHp96IoXZ2jgsThuO9nv353vv/lHM2fPuS16fL/52zfEfBdU7Blpy6+qWXc/K3BHlXnnyZnV97h5V959zfU560H8QiBVsHE9jScGwuauX1xv2d5qK3R683wucuFxaleB0I/jZnA7ItZ3P9pzvza73g1+HzKSnv1S4dy6BOs43G10FA3ooZjup1/crOPzrvFXmTL/3yS/WyZSleL8nlOY0p53Oy92/7Hv7Iq35zfkbKO0s3FednTkO2WCNMKN2Kvxb5b78tTehRFrr+zCjaRY18s+HGgatow1iO57bL/bU9xk8rzz3bQH61IXPxMvIG6jRnCvcJ8h7LPed7hz3QWVVa/38trEJcn2H1DGkQUvb7qxFSsVx90f8ai6ShH/Ynfeh95bZqmvMK3M5Coe8eyyvVfq5WYYs8SlXjDo2AK0SlPgS8D7QRVIVlZrSZapr+xMLiG1LJnscnAIsrt9itUehjDmNsROLUxod8BJJQ1HYQShx1aK1orR1IO/2RRX2nUwW0VrxAQkf+vxLQ6Tl2AzoxO0si8ekG26OYmG7sQK/S3f3evbt3o6MDwebj7NmzMzHpBRIQELAVyIPa2trZPk+SfZ6eZD8HCCHNlnFBLSnjVIByEtSTQGAYVlqO9EDJrzcaGYz+Vj6fPzIY1Nfe7gnqpk5Qkz1WmpyamvxqECgFURX78HQ6MdgHZ+F8vF618MEER5VHIWwCI5igH5tgEEhfu+cTpN/PGzj8fwUYAEHf/4ET3ikCAAAAAElFTkSuQmCC');
        $objTarjaAssinaturaDTO->setStrSinAtivo('S');

        $objTarjaAssinaturaBD = new TarjaAssinaturaBD($this->getObjInfraIBanco());
        $objTarjaAssinaturaDTO = $objTarjaAssinaturaBD->cadastrar($objTarjaAssinaturaDTO);


        $this->logar('ATUALIZANDO PAR�METRO ' . $this->nomeParametroModulo . ' NA TABELA infra_parametro PARA CONTROLAR A VERS�O DO M�DULO');
        BancoSEI::getInstance()->executarSql('UPDATE infra_parametro SET valor = \'1.0.4\' WHERE nome = \'' . $this->nomeParametroModulo . '\' ');

        $this->logar('INSTALA��O/ATUALIZA��O DA VERS�O ' . $this->versaoAtualDesteModulo . ' DO ' . $this->nomeDesteModulo . ' REALIZADA COM SUCESSO NA BASE DO SEI');
    }

    protected function instalarv110()
    {

        $objInfraMetaBD = new InfraMetaBD(BancoSEI::getInstance());

        $this->logar('EXECUTANDO A INSTALA��O/ATUALIZA��O DA VERS�O 1.1.0 DO ' . $this->nomeDesteModulo . ' NA BASE DO SEI');

        $this->logar('CRIANDO A TABELA md_pet_criterio');

        BancoSEI::getInstance()->executarSql('CREATE TABLE md_pet_criterio (
                id_md_pet_criterio ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL,
                sin_ativo ' . $objInfraMetaBD->tipoTextoFixo(1) . ' NOT NULL,
                sin_criterio_padrao ' . $objInfraMetaBD->tipoTextoFixo(1) . ' NOT NULL,
                sta_nivel_acesso ' . $objInfraMetaBD->tipoTextoFixo(1) . '  NULL,
                sta_tipo_nivel_acesso ' . $objInfraMetaBD->tipoTextoFixo(1) . '  NULL,
                id_hipotese_legal ' . $objInfraMetaBD->tipoNumero() . '  NULL,
                id_tipo_procedimento ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL
                )');

        $objInfraMetaBD->adicionarChavePrimaria('md_pet_criterio', 'pk_md_pet_criterio', array('id_md_pet_criterio'));
        $objInfraMetaBD->adicionarChaveEstrangeira('fk1_md_pet_criterio', 'md_pet_criterio', array('id_hipotese_legal'), 'hipotese_legal', array('id_hipotese_legal'));
        $objInfraMetaBD->adicionarChaveEstrangeira('fk2_md_pet_criterio', 'md_pet_criterio', array('id_tipo_procedimento'), 'tipo_procedimento', array('id_tipo_procedimento'));


        $this->logar('CREATE SEQUENCE seq_md_pet_criterio');
        BancoSEI::getInstance()->criarSequencialNativa('seq_md_pet_criterio', 1);


        //Criando campo "md_pet_rel_recibo_protoc.id_protocolo_relacionado caso" ainda nao exista
        $coluna = $objInfraMetaBD->obterColunasTabela('md_pet_rel_recibo_protoc', 'id_protocolo_relacionado');

        if ($coluna == null || !is_array($coluna)) {
            $this->logar('CREATE CAMPO id_protocolo_relacionado');

            $objInfraMetaBD->adicionarColuna('md_pet_rel_recibo_protoc', 'id_protocolo_relacionado', '' . $objInfraMetaBD->tipoNumeroGrande(), 'NULL');

            $objInfraMetaBD->adicionarChaveEstrangeira('fk5_md_pet_rel_recibo_protoc', 'md_pet_rel_recibo_protoc', array('id_protocolo_relacionado'), 'protocolo', array('id_protocolo'));
        }


        //coluna id_documento na tabela de recibo
        $this->logar('CREATE CAMPO md_pet_rel_recibo_protoc.id_documento');

        $objInfraMetaBD->adicionarColuna('md_pet_rel_recibo_protoc', 'id_documento', '' . $objInfraMetaBD->tipoNumeroGrande(), 'NULL');
        $objInfraMetaBD->adicionarChaveEstrangeira('fk6_md_pet_rel_recibo_protoc', 'md_pet_rel_recibo_protoc', array('id_documento'), 'documento', array('id_documento'));

        //Atualizando dados da tabela
        $objInfraParametro = new InfraParametro(BancoSEI::getInstance());
        $ret = $objInfraParametro->listarValores(array(MdPetIntSerieRN::$MD_PET_ID_SERIE_RECIBO), false);

        $arrObjInfraParametroDTO = NULL;
        $idSeriePet = array_key_exists(MdPetIntSerieRN::$MD_PET_ID_SERIE_RECIBO, $ret) ? $ret[MdPetIntSerieRN::$MD_PET_ID_SERIE_RECIBO] : null;

        if ($idSeriePet) {
            $arrObjDocumentDTO = array();

            $objDocumentoDTO = new DocumentoDTO();
            $objDocumentoDTO->retDblIdDocumento();
            $objDocumentoDTO->retDblIdProcedimento();
            $objDocumentoDTO->setNumIdSerie($idSeriePet);
            $objDocumentoRN = new DocumentoRN();
            $countDoc = $objDocumentoRN->contarRN0007($objDocumentoDTO);

            if ($countDoc > 0) {
                $arrObjDocumentDTO = $objDocumentoRN->listarRN0008($objDocumentoDTO);
                foreach ($arrObjDocumentDTO as $objDocumentoDTO) {
                    $objMdPetReciboDTO = new MdPetReciboDTO();
                    $objMdPetReciboRN = new MdPetReciboRN();
                    $objMdPetReciboDTO->setNumIdProtocolo($objDocumentoDTO->getDblIdProcedimento());
                    $objMdPetReciboDTO->setDblIdDocumento($objDocumentoDTO->getDblIdDocumento());
                    $objMdPetReciboDTO->retNumIdReciboPeticionamento();
                    $arrObjMdPetReciboDTO = $objMdPetReciboRN->listar($objMdPetReciboDTO);

                    foreach ($arrObjMdPetReciboDTO as $objDTO) {
                        $objMdPetReciboRN->alterar($objDTO);
                    }
                }
            }
        }


        $this->logar('ATUALIZANDO PAR�METRO ' . $this->nomeParametroModulo . ' NA TABELA infra_parametro PARA CONTROLAR A VERS�O DO M�DULO');
        BancoSEI::getInstance()->executarSql('UPDATE infra_parametro SET valor = \'1.1.0\' WHERE nome = \'' . $this->nomeParametroModulo . '\' ');

        $this->logar('INSTALA��O/ATUALIZA��O DA VERS�O ' . $this->versaoAtualDesteModulo . ' DO ' . $this->nomeDesteModulo . ' REALIZADA COM SUCESSO NA BASE DO SEI');
    }

    protected function instalarv200()
    {

        $objInfraMetaBD = new InfraMetaBD(BancoSEI::getInstance());

        $this->logar('EXECUTANDO A INSTALA��O/ATUALIZA��O DA VERS�O 2.0.0 DO ' . $this->nomeDesteModulo . ' NA BASE DO SEI');

        $this->logar('INSERINDO EMAIL MD_PET_INTIMACAO_APENAS_RESPOSTAS_FACULTATIVAS NA TABELA email_sistema');

        //Parametrizar Email de Alerta �s Unidades
        $conteudoRespostaFacultativa = "      :: Este � um e-mail autom�tico ::

Prezado(a) @nome_usuario_externo@,

No SEI-@sigla_orgao@ foi expedida Intima��o Eletr�nica referente a @tipo_intimacao@, no �mbito do processo n� @processo@, conforme documento principal de protocolo n� @documento_principal_intimacao@ (@tipo_documento_principal_intimacao@).

Para visualizar o documento principal da Intima��o Eletr�nica e poss�veis anexos, acesse a �rea destinada aos Usu�rios Externos no SEI-@sigla_orgao@ destacada em nosso Portal na Internet ou acesse diretamente o link a seguir: @link_login_usuario_externo@

Caso tenha interesse, a resposta � Intima��o Eletr�nica deve ser realizada na �rea destinada aos Usu�rios Externos indicada acima. Com o processo aberto, acesse o bot�o de A��o Responder Intima��o Eletr�nica.

Lembramos que, independentemente de e-mail de alerta, � de responsabilidade exclusiva do Usu�rio Externo a consulta peri�dica ao SEI a fim de verificar o recebimento de Intima��es, considerando-se realizadas na data em que efetuar sua consulta no sistema ou, n�o efetuada a consulta, em @prazo_intimacao_tacita@ dias ap�s a data de sua expedi��o.

Dessa forma, como a presente Intima��o foi expedida em @data_expedicao_intimacao@ e em conformidade com as regras de contagem de prazo dispostas no art. 66 da Lei n� 9.784/1999, mesmo se n�o ocorrer a consulta direta no sistema aos documentos correspondentes, a Intima��o ser� considerada cumprida por decurso do prazo t�cito ao final do dia @data_final_prazo_intimacao_tacita@.


@sigla_orgao@
@descricao_orgao@
@sitio_internet_orgao@

ATEN��O: As informa��es contidas neste e-mail, incluindo seus anexos, podem ser restritas apenas � pessoa ou entidade para a qual foi endere�ada. Se voc� n�o � o destinat�rio ou a pessoa respons�vel por encaminhar esta mensagem ao destinat�rio, voc� est�, por meio desta, notificado que n�o dever� rever, retransmitir, imprimir, copiar, usar ou distribuir esta mensagem ou quaisquer anexos. Caso voc� tenha recebido esta mensagem por engano, por favor, contate o remetente imediatamente e em seguida apague esta mensagem.";

        $maxIdEmailSistemaRespostaFacultativa = $this->retornarMaxIdEmailSistema();

        $insertRespostaFacultativa = "INSERT INTO email_sistema
            (id_email_sistema,
              descricao,
              de,
              para,
              assunto,
              conteudo,
              sin_ativo,
            id_email_sistema_modulo
            )
        VALUES
            (" . $maxIdEmailSistemaRespostaFacultativa . ",
              'Peticionamento Eletr�nico - Intima��o Eletr�nica apenas com Respostas Facultativas',
              '@sigla_sistema@ <@email_sistema@>',
              '@email_usuario_externo@',
              'SEI - Intima��o Eletr�nica Gerada no Processo n� @processo@',
              '" . $conteudoRespostaFacultativa . "',
              'S',
            'MD_PET_INTIMACAO_APENAS_RESPOSTAS_FACULTATIVAS'
            )";

        BancoSEI::getInstance()->executarSql($insertRespostaFacultativa);


        $this->logar('INSERINDO EMAIL MD_PET_INTIMACAO_QUE_EXIGE_RESPOSTA NA TABELA email_sistema');

        $conteudoExigeResposta = "      :: Este � um e-mail autom�tico ::

Prezado(a) @nome_usuario_externo@,

No SEI-@sigla_orgao@ foi expedida Intima��o Eletr�nica referente a @tipo_intimacao@, no �mbito do processo n� @processo@, conforme documento principal de protocolo n� @documento_principal_intimacao@ (@tipo_documento_principal_intimacao@).

A mencionada Intima��o exige resposta para @tipo_resposta@, no prazo de @prazo_externo_tipo_resposta@, contados a partir do dia �til seguinte ao da data de cumprimento da presente Intima��o.

Para visualizar o documento principal da Intima��o Eletr�nica e poss�veis anexos, acesse a �rea destinada aos Usu�rios Externos no SEI-@sigla_orgao@ destacada em nosso Portal na Internet ou acesse diretamente o link a seguir: @link_login_usuario_externo@

A resposta � Intima��o Eletr�nica que � exigida deve ser realizada na �rea destinada aos Usu�rios Externos indicada acima. Com o processo aberto, acesse o bot�o de A��o Responder Intima��o Eletr�nica.

Lembramos que, independentemente de e-mail de alerta, � de responsabilidade exclusiva do Usu�rio Externo a consulta peri�dica ao SEI a fim de verificar o recebimento de Intima��es, considerando-se realizadas na data em que efetuar sua consulta no sistema ou, n�o efetuada a consulta, em @prazo_intimacao_tacita@ dias ap�s a data de sua expedi��o.

Dessa forma, como a presente Intima��o foi expedida em @data_expedicao_intimacao@ e em conformidade com as regras de contagem de prazo dispostas no art. 66 da Lei n� 9.784/1999, mesmo se n�o ocorrer a consulta direta no sistema aos documentos correspondentes, a Intima��o ser� considerada cumprida por decurso do prazo t�cito ao final do dia @data_final_prazo_intimacao_tacita@.


@sigla_orgao@
@descricao_orgao@
@sitio_internet_orgao@

ATEN��O: As informa��es contidas neste e-mail, incluindo seus anexos, podem ser restritas apenas � pessoa ou entidade para a qual foi endere�ada. Se voc� n�o � o destinat�rio ou a pessoa respons�vel por encaminhar esta mensagem ao destinat�rio, voc� est�, por meio desta, notificado que n�o dever� rever, retransmitir, imprimir, copiar, usar ou distribuir esta mensagem ou quaisquer anexos. Caso voc� tenha recebido esta mensagem por engano, por favor, contate o remetente imediatamente e em seguida apague esta mensagem.";

        $maxIdEmailSistemaExigeResposta = $this->retornarMaxIdEmailSistema();

        $insertExigeResposta = "INSERT INTO email_sistema
            (id_email_sistema,
                descricao,
                de,
                para,
                assunto,
                conteudo,
                sin_ativo,
                id_email_sistema_modulo
            )
            VALUES
            (" . $maxIdEmailSistemaExigeResposta . ",
                'Peticionamento Eletr�nico - Intima��o Eletr�nica que Exige Resposta',
                '@sigla_sistema@ <@email_sistema@>',
                '@email_usuario_externo@',
                'SEI - Intima��o Eletr�nica que Exige Resposta no Processo n� @processo@',
                '" . $conteudoExigeResposta . "',
                'S',
                'MD_PET_INTIMACAO_QUE_EXIGE_RESPOSTA'
            )";
        BancoSEI::getInstance()->executarSql($insertExigeResposta);


        $this->logar('INSERINDO EMAIL MD_PET_INTIMACAO_SEM_RESPOSTA NA TABELA email_sistema');

        $conteudoSemResposta = "      :: Este � um e-mail autom�tico ::

Prezado(a) @nome_usuario_externo@,

No SEI-@sigla_orgao@ foi expedida Intima��o Eletr�nica referente a @tipo_intimacao@, no �mbito do processo n� @processo@, conforme documento principal de protocolo n� @documento_principal_intimacao@ (@tipo_documento_principal_intimacao@).

OBSERVA��O: A presente intima��o n�o demanda qualquer tipo de resposta, por geralmente encaminhar documento para mero conhecimento, o que n�o dispensa a necessidade de acesso aos documentos para ci�ncia de seu teor. Ap�s o cumprimento da intima��o, observar que neste caso n�o ser� disponibilizada a funcionalidade para Peticionamento de Resposta a Intima��o Eletr�nica, sem que isso impe�a o uso do Peticionamento Intercorrente, caso ainda seja necess�rio protocolizar documento no processo acima indicado.

Para visualizar o documento principal da Intima��o Eletr�nica e poss�veis anexos, acesse a �rea destinada aos Usu�rios Externos no SEI-@sigla_orgao@ destacada em nosso Portal na Internet ou acesse diretamente o link a seguir: @link_login_usuario_externo@

Lembramos que, independentemente de e-mail de alerta, � de responsabilidade exclusiva do Usu�rio Externo a consulta peri�dica ao SEI a fim de verificar o recebimento de Intima��es, considerando-se realizadas na data em que efetuar sua consulta no sistema ou, n�o efetuada a consulta, em @prazo_intimacao_tacita@ dias ap�s a data de sua expedi��o.

Dessa forma, como a presente Intima��o foi expedida em @data_expedicao_intimacao@ e em conformidade com as regras de contagem de prazo dispostas no art. 66 da Lei n� 9.784/1999, mesmo se n�o ocorrer a consulta direta no sistema aos documentos correspondentes, a Intima��o ser� considerada cumprida por decurso do prazo t�cito ao final do dia @data_final_prazo_intimacao_tacita@.


@sigla_orgao@
@descricao_orgao@
@sitio_internet_orgao@

ATEN��O: As informa��es contidas neste e-mail, incluindo seus anexos, podem ser restritas apenas � pessoa ou entidade para a qual foi endere�ada. Se voc� n�o � o destinat�rio ou a pessoa respons�vel por encaminhar esta mensagem ao destinat�rio, voc� est�, por meio desta, notificado que n�o dever� rever, retransmitir, imprimir, copiar, usar ou distribuir esta mensagem ou quaisquer anexos. Caso voc� tenha recebido esta mensagem por engano, por favor, contate o remetente imediatamente e em seguida apague esta mensagem.";

        $maxIdEmailSistemaSemResposta = $this->retornarMaxIdEmailSistema();

        $insertSemResposta = "INSERT INTO email_sistema
            (id_email_sistema,
            descricao,
            de,
            para,
            assunto,
            conteudo,
            sin_ativo,
            id_email_sistema_modulo
            )
        VALUES
            (" . $maxIdEmailSistemaSemResposta . ",
            'Peticionamento Eletr�nico - Intima��o Eletr�nica Sem Resposta',
            '@sigla_sistema@ <@email_sistema@>',
            '@email_usuario_externo@',
            'SEI - Intima��o Eletr�nica Gerada no Processo n� @processo@',
            '" . $conteudoSemResposta . "',
            'S',
            'MD_PET_INTIMACAO_SEM_RESPOSTA'
            )";

        BancoSEI::getInstance()->executarSql($insertSemResposta);


        $this->logar('INSERINDO EMAIL MD_PET_REITERACAO_INTIMACAO_QUE_EXIGE_RESPOSTA NA TABELA email_sistema');

        $conteudoReiteracaoExigeResposta = "      :: Este � um e-mail autom�tico ::

Prezado(a) @nome_usuario_externo@,

Reiteramos a necessidade de Resposta � Intima��o Eletr�nica expedida no SEI-@sigla_orgao@ referente a @tipo_intimacao@, no �mbito do processo n� @processo@, conforme documento principal de protocolo n� @documento_principal_intimacao@ (@tipo_documento_principal_intimacao@).

A mencionada Intima��o exige resposta para @tipo_resposta@, no prazo de @prazo_externo_tipo_resposta@, contados a partir do dia �til seguinte ao da data de cumprimento da Intima��o, que ocorreu em @data_cumprimento_intimacao@.

Para visualizar o documento principal da Intima��o Eletr�nica e poss�veis anexos, acesse a �rea destinada aos Usu�rios Externos no SEI-@sigla_orgao@ destacada em nosso Portal na Internet ou acesse diretamente o link a seguir: @link_login_usuario_externo@

A resposta � Intima��o Eletr�nica que � exigida deve ser realizada na �rea destinada aos Usu�rios Externos indicada acima. Com o processo aberto, acesse o bot�o de A��o Responder Intima��o Eletr�nica.

OBSERVA��O: A presente reitera��o ocorre quando a resposta ainda n�o tenha sido efetivada pelo Destinat�rio da Intima��o, em 5 dias e 1 dia antes da Data Limite para Resposta. Caso a Intima��o j� tenha sido respondida por outro Destinat�rio da mesma Intima��o ou por outro Usu�rio Externo (por exemplo, utilizando o Peticionamento Intercorrente), por favor, ignorar esta reitera��o.


@sigla_orgao@
@descricao_orgao@
@sitio_internet_orgao@

ATEN��O: As informa��es contidas neste e-mail, incluindo seus anexos, podem ser restritas apenas � pessoa ou entidade para a qual foi endere�ada. Se voc� n�o � o destinat�rio ou a pessoa respons�vel por encaminhar esta mensagem ao destinat�rio, voc� est�, por meio desta, notificado que n�o dever� rever, retransmitir, imprimir, copiar, usar ou distribuir esta mensagem ou quaisquer anexos. Caso voc� tenha recebido esta mensagem por engano, por favor, contate o remetente imediatamente e em seguida apague esta mensagem.";

        $maxIdEmailSistemaReiteracaoExigeResposta = $this->retornarMaxIdEmailSistema();

        $insertReiteracaoExigeResposta = "INSERT INTO email_sistema
            (id_email_sistema,
                descricao,
                de,
                para,
                assunto,
                conteudo,
                sin_ativo,
                id_email_sistema_modulo
            )
            VALUES
            (" . $maxIdEmailSistemaReiteracaoExigeResposta . ",
                'Peticionamento Eletr�nico - Reitera��o de Intima��o Eletr�nica que Exige Resposta',
                '@sigla_sistema@ <@email_sistema@>',
                '@email_usuario_externo@',
                'SEI - Reitera��o de Intima��o Eletr�nica que Exige Resposta no Processo n� @processo@',
                '" . $conteudoReiteracaoExigeResposta . "',
                'S',
                'MD_PET_REITERACAO_INTIMACAO_QUE_EXIGE_RESPOSTA'
            )";

        BancoSEI::getInstance()->executarSql($insertReiteracaoExigeResposta);


        $this->logar('CRIANDO USU�RIO do M�dulo de Peticionamento');

        $objRN = new MdPetIntUsuarioRN();
        $objRN->realizarInsercoesUsuarioModuloPet();


        $this->logar('CRIANDO A TABELA md_pet_int_prazo_tacita');

        BancoSEI::getInstance()->executarSql('CREATE TABLE md_pet_int_prazo_tacita (
                id_md_pet_int_prazo_tacita ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL,
                num_prazo ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL) '
        );
        $objInfraMetaBD->adicionarChavePrimaria('md_pet_int_prazo_tacita', 'pk_md_pet_int_prazo_tacita', array('id_md_pet_int_prazo_tacita'));


        $this->logar('CRIANDO A SEQUENCE seq_md_pet_int_prazo_tacita');
        BancoSEI::getInstance()->criarSequencialNativa('seq_md_pet_int_prazo_tacita', 1);


        $this->logar('INSERINDO Prazo T�cito com valor default 15');

        $objMdPetIntPrazoTacitaDTO = new MdPetIntPrazoTacitaDTO();
        $objMdPetIntPrazoTacitaDTO->setNumNumPrazo(15);
        $objMdPetIntPrazoTacitaDTO->setNumIdMdPetIntPrazoTacita(1);
        $objMdPetIntPrazoTacitaRN = new MdPetIntPrazoTacitaRN();
        $objMdPetIntPrazoTacitaRN->cadastrar($objMdPetIntPrazoTacitaDTO);


        $this->logar('CRIANDO A TABELA md_pet_int_tipo_intimacao');

        BancoSEI::getInstance()->executarSql('CREATE TABLE md_pet_int_tipo_intimacao  (
                id_md_pet_int_tipo_intimacao ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL,
                nome ' . $objInfraMetaBD->tipoTextoVariavel(100) . ' NOT NULL,
                tipo_resposta_aceita ' . $objInfraMetaBD->tipoTextoFixo(1) . ' NOT NULL,
                sin_ativo ' . $objInfraMetaBD->tipoTextoFixo(1) . ' NOT NULL )'
        );
        $objInfraMetaBD->adicionarChavePrimaria('md_pet_int_tipo_intimacao', 'pk_md_pet_int_tipo_intimacao', array('id_md_pet_int_tipo_intimacao'));


        $this->logar('CRIANDO A SEQUENCE seq_md_pet_int_tipo_intimacao');
        BancoSEI::getInstance()->criarSequencialNativa('seq_md_pet_int_tipo_intimacao', 1);


        $this->logar('CRIANDO A TABELA md_pet_int_tipo_resp');

        BancoSEI::getInstance()->executarSql('CREATE TABLE md_pet_int_tipo_resp (
                id_md_pet_int_tipo_resp ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL,
                tipo_prazo_externo ' . $objInfraMetaBD->tipoTextoFixo(1) . ' NULL,
                valor_prazo_externo ' . $objInfraMetaBD->tipoNumero() . ' NULL,
                nome ' . $objInfraMetaBD->tipoTextoVariavel(100) . ' NOT NULL,
                tipo_resposta_aceita ' . $objInfraMetaBD->tipoTextoFixo(1) . ' NOT NULL,
                sin_ativo ' . $objInfraMetaBD->tipoTextoFixo(1) . ' NOT NULL,
                tipo_dia ' . $objInfraMetaBD->tipoTextoFixo(1) . ' NULL )'
        );
        $objInfraMetaBD->adicionarChavePrimaria('md_pet_int_tipo_resp', 'pk_md_pet_int_tipo_resp', array('id_md_pet_int_tipo_resp'));


        $this->logar('CRIANDO A SEQUENCE seq_md_pet_int_tipo_resp');
        BancoSEI::getInstance()->criarSequencialNativa('seq_md_pet_int_tipo_resp', 1);


        $this->logar('CRIANDO A TABELA md_pet_int_rel_intim_resp');
        BancoSEI::getInstance()->executarSql('CREATE TABLE md_pet_int_rel_intim_resp (
                id_md_pet_int_tipo_intimacao ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL,
                id_md_pet_int_tipo_resp ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL,
                sin_ativo ' . $objInfraMetaBD->tipoTextoFixo(1) . ' NOT NULL )');

        $objInfraMetaBD->adicionarChavePrimaria('md_pet_int_rel_intim_resp', 'pk_md_pet_int_rel_intim_resp', array('id_md_pet_int_tipo_intimacao', 'id_md_pet_int_tipo_resp'));
        $objInfraMetaBD->adicionarChaveEstrangeira('fk1_md_pet_int_rel_intim_resp', 'md_pet_int_rel_intim_resp', array('id_md_pet_int_tipo_intimacao'), 'md_pet_int_tipo_intimacao', array('id_md_pet_int_tipo_intimacao'));
        $objInfraMetaBD->adicionarChaveEstrangeira('fk2_md_pet_int_rel_intim_resp', 'md_pet_int_rel_intim_resp', array('id_md_pet_int_tipo_resp'), 'md_pet_int_tipo_resp', array('id_md_pet_int_tipo_resp'));


        $this->logar('CRIANDO A TABELA md_pet_int_serie');

        BancoSEI::getInstance()->executarSql('CREATE TABLE md_pet_int_serie (
                id_serie ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL )');

        $objInfraMetaBD->adicionarChavePrimaria('md_pet_int_serie', 'pk_md_pet_int_serie', array('id_serie'));
        if (!BancoSEI::getInstance() instanceof InfraOracle) {
            $objInfraMetaBD->adicionarChaveEstrangeira('fk1_md_pet_int_serie', 'md_pet_int_serie', array('id_serie'), 'serie', array('id_serie'));
        }

        $this->logar('CRIANDO A TABELA md_pet_acesso_externo');
        BancoSEI::getInstance()->executarSql('CREATE TABLE md_pet_acesso_externo (
                id_acesso_externo ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL,
                sin_proc_intercorrente ' . $objInfraMetaBD->tipoTextoFixo(1) . ' NOT NULL ,
                sin_proc_novo ' . $objInfraMetaBD->tipoTextoFixo(1) . ' NOT NULL ,
                sin_intimacao ' . $objInfraMetaBD->tipoTextoFixo(1) . ' NOT NULL ,
                sin_ativo ' . $objInfraMetaBD->tipoTextoFixo(1) . ' NOT NULL )'
        );

        $objInfraMetaBD->adicionarChavePrimaria('md_pet_acesso_externo', 'fk_pet_acesso_externo_01', array('id_acesso_externo'));

        $objInfraMetaBD->adicionarChaveEstrangeira('fk1_pet_acesso_externo', 'md_pet_acesso_externo', array('id_acesso_externo'), 'acesso_externo', array('id_acesso_externo'));


        $this->logar('CRIA��O DE HIST�RICOS E GERA��O DE ANDAMENTOS NO PROCESSO DA INTIMA��O ELETR�NICA');

        $texto1 = " Intima��o Eletr�nica expedida em @DATA_EXPEDICAO_INTIMACAO@, sobre o Documento Principal @DOCUMENTO@, para @USUARIO_EXTERNO_NOME@";

        $texto2 = "Intima��o cumprida em @DATA_CUMPRIMENTO_INTIMACAO@, conforme Certid�o @DOC_CERTIDAO_INTIMACAO@, por @TIPO_CUMPRIMENTO_INTIMACAO@, sobre a Intima��o expedida em @DATA_EXPEDICAO_INTIMACAO@ e Documento Principal @DOCUMENTO@ para @USUARIO_EXTERNO_NOME@";

        $texto3 = "O Usu�rio Externo @USUARIO_EXTERNO_NOME@ efetivou Peticionamento @TIPO_PETICIONAMENTO@, tendo gerado o recibo @DOCUMENTO@";

        $texto4 = "Prorroga��o Autom�tica do Prazo Externo de poss�vel Resposta a Intima��o, relativa � Intima��o expedida em @DATA_EXPEDICAO_INTIMACAO@ e ao Documento Principal @DOCUMENTO@, para @DATA_LIMITE_RESPOSTAS@";

        //@todo incrementar a seq de um jeito diferente para cada modelo de SGBD (ver pagina 8 do manual)
        $arrMaxIdTarefa = BancoSEI::getInstance()->consultarSql('SELECT MAX(id_tarefa) as max FROM tarefa');
        $numIdTarefaMax = $arrMaxIdTarefa[0]['max'];

        if ($numIdTarefaMax < 1000) {
            $numIdTarefaMax = 1000;
        } else {
            $numIdTarefaMax++;
        }

        if (BancoSEI::getInstance() instanceof InfraMySql) {
            BancoSEI::getInstance()->executarSql(" DELETE FROM seq_tarefa");
            BancoSEI::getInstance()->executarSql(" INSERT INTO seq_tarefa (id) VALUES (" . $numIdTarefaMax . ") ");
        } elseif (BancoSEI::getInstance() instanceof InfraOracle) {
            BancoSEI::getInstance()->executarSql("drop sequence seq_tarefa");
            BancoSEI::getInstance()->executarSql("CREATE SEQUENCE seq_tarefa START WITH " . $numIdTarefaMax . " INCREMENT BY 1 NOCACHE NOCYCLE");
        } else if (BancoSEI::getInstance() instanceof InfraSqlServer) {
            BancoSEI::getInstance()->executarSql("DBCC CHECKIDENT ('seq_tarefa', RESEED, " . $numIdTarefaMax . ");");
        }

        //campo setStrSinFecharAndamentosAbertos de N para S por estar lan�ando andamento em processo que estara aberto na unidade (seguindo recomenda��o do manual do SEI)
        $tarefaDTO1 = new TarefaDTO();
        $tarefaDTO1->setNumIdTarefa($numIdTarefaMax);
        $tarefaDTO1->setStrIdTarefaModulo('MD_PET_INTIMACAO_EXPEDIDA');
        $tarefaDTO1->setStrNome($texto1);
        $tarefaDTO1->setStrSinHistoricoResumido('S');
        $tarefaDTO1->setStrSinHistoricoCompleto('S');
        $tarefaDTO1->setStrSinFecharAndamentosAbertos('S');
        $tarefaDTO1->setStrSinLancarAndamentoFechado('N');
        $tarefaDTO1->setStrSinPermiteProcessoFechado('N');

        $numIdTarefaMax++;

        if (BancoSEI::getInstance() instanceof InfraMySql) {
            BancoSEI::getInstance()->executarSql(" DELETE FROM seq_tarefa");
            BancoSEI::getInstance()->executarSql(" INSERT INTO seq_tarefa (id) VALUES (" . $numIdTarefaMax . ") ");
        } elseif (BancoSEI::getInstance() instanceof InfraOracle) {
            BancoSEI::getInstance()->executarSql("drop sequence seq_tarefa");
            BancoSEI::getInstance()->executarSql("CREATE SEQUENCE seq_tarefa START WITH " . $numIdTarefaMax . " INCREMENT BY 1 NOCACHE NOCYCLE");
        } else if (BancoSEI::getInstance() instanceof InfraSqlServer) {
            BancoSEI::getInstance()->executarSql("DBCC CHECKIDENT ('seq_tarefa', RESEED, " . $numIdTarefaMax . ");");
        }

        $tarefaDTO2 = new TarefaDTO();
        $tarefaDTO2->setNumIdTarefa($numIdTarefaMax);
        $tarefaDTO2->setStrIdTarefaModulo('MD_PET_INTIMACAO_CUMPRIDA');
        $tarefaDTO2->setStrNome($texto2);
        $tarefaDTO2->setStrSinHistoricoResumido('S');
        $tarefaDTO2->setStrSinHistoricoCompleto('S');
        $tarefaDTO2->setStrSinFecharAndamentosAbertos('S');
        $tarefaDTO2->setStrSinLancarAndamentoFechado('N');
        $tarefaDTO2->setStrSinPermiteProcessoFechado('N');

        $numIdTarefaMax++;

        if (BancoSEI::getInstance() instanceof InfraMySql) {
            BancoSEI::getInstance()->executarSql(" DELETE FROM seq_tarefa");
            BancoSEI::getInstance()->executarSql(" INSERT INTO seq_tarefa (id) VALUES (" . $numIdTarefaMax . ") ");
        } elseif (BancoSEI::getInstance() instanceof InfraOracle) {
            BancoSEI::getInstance()->executarSql("drop sequence seq_tarefa");
            BancoSEI::getInstance()->executarSql("CREATE SEQUENCE seq_tarefa START WITH " . $numIdTarefaMax . " INCREMENT BY 1 NOCACHE NOCYCLE");
        } else if (BancoSEI::getInstance() instanceof InfraSqlServer) {
            BancoSEI::getInstance()->executarSql("DBCC CHECKIDENT ('seq_tarefa', RESEED, " . $numIdTarefaMax . ");");
        }

        $tarefaDTO3 = new TarefaDTO();
        $tarefaDTO3->setNumIdTarefa($numIdTarefaMax);
        $tarefaDTO3->setStrIdTarefaModulo('MD_PET_PETICIONAMENTO_EFETIVADO');
        $tarefaDTO3->setStrNome($texto3);
        $tarefaDTO3->setStrSinHistoricoResumido('S');
        $tarefaDTO3->setStrSinHistoricoCompleto('S');
        $tarefaDTO3->setStrSinFecharAndamentosAbertos('S');
        $tarefaDTO3->setStrSinLancarAndamentoFechado('N');
        $tarefaDTO3->setStrSinPermiteProcessoFechado('N');

        $numIdTarefaMax++;

        if (BancoSEI::getInstance() instanceof InfraMySql) {
            BancoSEI::getInstance()->executarSql(" DELETE FROM seq_tarefa");
            BancoSEI::getInstance()->executarSql(" INSERT INTO seq_tarefa (id) VALUES (" . $numIdTarefaMax . ") ");
        } elseif (BancoSEI::getInstance() instanceof InfraOracle) {
            BancoSEI::getInstance()->executarSql("drop sequence seq_tarefa");
            BancoSEI::getInstance()->executarSql("CREATE SEQUENCE seq_tarefa START WITH " . $numIdTarefaMax . " INCREMENT BY 1 NOCACHE NOCYCLE");
        } else if (BancoSEI::getInstance() instanceof InfraSqlServer) {
            BancoSEI::getInstance()->executarSql("DBCC CHECKIDENT ('seq_tarefa', RESEED, " . $numIdTarefaMax . ");");
        }

        $tarefaDTO4 = new TarefaDTO();
        $tarefaDTO4->setNumIdTarefa($numIdTarefaMax);
        $tarefaDTO4->setStrIdTarefaModulo('MD_PET_INTIMACAO_PRORROGACAO_AUTOMATICA_PRAZO_EXT');
        $tarefaDTO4->setStrNome($texto4);
        $tarefaDTO4->setStrSinHistoricoResumido('S');
        $tarefaDTO4->setStrSinHistoricoCompleto('S');
        $tarefaDTO4->setStrSinFecharAndamentosAbertos('S');
        $tarefaDTO4->setStrSinLancarAndamentoFechado('N');
        $tarefaDTO4->setStrSinPermiteProcessoFechado('S');

        $tarefaRN = new TarefaRN();
        $tarefaRN->cadastrar($tarefaDTO1);
        $tarefaRN->cadastrar($tarefaDTO2);
        $tarefaRN->cadastrar($tarefaDTO3);
        $tarefaRN->cadastrar($tarefaDTO4);

        //CRIANDO NOVO TIPO DE DOCUMENTO "Certid�o"
        $this->logar('CRIANDO MODELO "Modulo_Peticionamento_Certidao"');
        $modeloRN = new ModeloRN();
        $modeloDTO = new ModeloDTO();
        $modeloDTO->setNumIdModelo(null);
        $modeloDTO->retTodos();
        $modeloDTO->setStrNome('Modulo_Peticionamento_Certidao');
        $modeloDTO->setStrSinAtivo('S');
        $modeloDTO = $modeloRN->cadastrar($modeloDTO);

        //adicionando as se��es do modelo: Corpo de Texto e Rodap�
        $this->logar('CRIANDO SE�AO DO MODELO - Corpo do Texto');
        $secaoModeloRN = new SecaoModeloRN();

        $secaoModeloCorpoTextoDTO = new SecaoModeloDTO();
        $secaoModeloCorpoTextoDTO->retTodos();
        $secaoModeloCorpoTextoDTO->setNumIdSecaoModelo(null);
        $secaoModeloCorpoTextoDTO->setNumIdModelo($modeloDTO->getNumIdModelo());
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

        $secaoModeloCorpoTextoDTO = $secaoModeloRN->cadastrar($secaoModeloCorpoTextoDTO);

        //secao do rodap�
        $this->logar('CRIANDO SE�AO DO MODELO - Rodap�');
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

        $secaoModeloRodapeDTO->setNumIdModelo($modeloDTO->getNumIdModelo());
        $secaoModeloRodapeDTO->setStrNome('Rodap�');
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

        $secaoModeloRodapeDTO = $secaoModeloRN->cadastrar($secaoModeloRodapeDTO);

        //Obter o Grupo de Tipo de Documento "Internos do Sistema".
        $grupoSerieRN = new GrupoSerieRN();

        $this->logar('OBTER GRUPO DE TIPO DE DOCUMENTO "Internos do Sistema"');
        $grupoSerieDTO = new GrupoSerieDTO();
        $grupoSerieDTO->retTodos();
        $grupoSerieDTO->setStrNome('Internos do Sistema');
        $grupoSerieDTO->setStrSinAtivo('S');
        $grupoSerieDTO = $grupoSerieRN->consultarRN0777($grupoSerieDTO);

        //Criar o Tipo de Documento "Recibo Eletr�nico de Protocolo"
        $this->logar('CRIANDO TIPO DE DOCUMENTO Certidao');
        $serieDTO = new SerieDTO();
        $serieDTO->retTodos();
        $serieRN = new SerieRN();

        $serieDTO->setNumIdSerie(null);
        $serieDTO->setNumIdGrupoSerie($grupoSerieDTO->getNumIdGrupoSerie());
        $serieDTO->setStrStaNumeracao(SerieRN::$TN_SEM_NUMERACAO);
        $serieDTO->setStrStaAplicabilidade(SerieRN::$TA_INTERNO);
        $serieDTO->setNumIdModeloEdoc(null);
        $serieDTO->setNumIdModelo($modeloDTO->getNumIdModelo());
        $serieDTO->setStrNome('Certid�o de Intima��o Cumprida');
        $serieDTO->setStrDescricao('Utilizado para a gera��o autom�tica da Certidao em Intima��es feitas pelo Peticionamentos Eletr�nicos realizados por Usu�rio Externo.');
        $serieDTO->setStrSinInteressado('S');
        $serieDTO->setStrSinDestinatario('N');
        $serieDTO->setStrSinAssinaturaPublicacao('S');
        $serieDTO->setStrSinInterno('S');
        $serieDTO->setStrSinAtivo('S');
        $serieDTO->setArrObjRelSerieAssuntoDTO(array());
        $serieDTO->setArrObjRelSerieVeiculoPublicacaoDTO(array());

        $serieDTO->setNumIdTipoFormulario(null);
        $serieDTO->setArrObjSerieRestricaoDTO(array());

        $serieDTO = $serieRN->cadastrarRN0642($serieDTO);

        $this->logar('ATUALIZANDO INFRA_PARAMETRO (MODULO_PETICIONAMENTO_ID_SERIE_CERTIDAO_INTIMACAO_CUMPRIDA)');

        $nomeParamIdSerie = 'MODULO_PETICIONAMENTO_ID_SERIE_CERTIDAO_INTIMACAO_CUMPRIDA';

        BancoSEI::getInstance()->executarSql('INSERT INTO infra_parametro ( valor, nome )  VALUES (\'' . $serieDTO->getNumIdSerie() . '\' , \'' . $nomeParamIdSerie . '\' ) ');

        $objInfraMetaBD->adicionarColuna('md_pet_rel_recibo_protoc', 'txt_doc_principal_intimacao', $objInfraMetaBD->tipoTextoVariavel(250), 'NULL');


        $this->logar('CRIANDO A TABELA md_pet_intimacao');

        BancoSEI::getInstance()->executarSql('CREATE TABLE md_pet_intimacao (
                id_md_pet_intimacao ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL ,
                id_md_pet_int_tipo_intimacao ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL ,
                sin_tipo_acesso_processo ' . $objInfraMetaBD->tipoTextoFixo(1) . ' NOT NULL )'
        );

        $objInfraMetaBD->adicionarChavePrimaria('md_pet_intimacao', 'pk_md_pet_intimacao', array('id_md_pet_intimacao'));
        $objInfraMetaBD->adicionarChaveEstrangeira('fk1_md_pet_intimacao', 'md_pet_intimacao', array('id_md_pet_int_tipo_intimacao'), 'md_pet_int_tipo_intimacao', array('id_md_pet_int_tipo_intimacao'));


        $this->logar('CRIA��O DA SEQUENCE seq_md_pet_intimacao');
        BancoSEI::getInstance()->criarSequencialNativa('seq_md_pet_intimacao', 1);


        $this->logar('CRIANDO A TABELA md_pet_int_rel_tipo_resp');

        BancoSEI::getInstance()->executarSql('CREATE TABLE md_pet_int_rel_tipo_resp (
                id_md_pet_int_rel_tipo_resp ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL ,
                sin_ativo ' . $objInfraMetaBD->tipoTextoFixo(1) . ' NOT NULL ,
                id_md_pet_intimacao ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL ,
                id_md_pet_int_tipo_resp ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL )'
        );

        $objInfraMetaBD->adicionarChavePrimaria('md_pet_int_rel_tipo_resp', 'pk_md_pet_int_rel_tipo_resp', array('id_md_pet_int_rel_tipo_resp'));
        $objInfraMetaBD->adicionarChaveEstrangeira('fk1_md_pet_int_rel_tipo_resp', 'md_pet_int_rel_tipo_resp', array('id_md_pet_int_tipo_resp'), 'md_pet_int_tipo_resp', array('id_md_pet_int_tipo_resp'));
        $objInfraMetaBD->adicionarChaveEstrangeira('fk2_md_pet_int_rel_tipo_resp', 'md_pet_int_rel_tipo_resp', array('id_md_pet_intimacao'), 'md_pet_intimacao', array('id_md_pet_intimacao'));


        $this->logar('CRIA��O DA SEQUENCE seq_md_pet_int_rel_tipo_resp');
        BancoSEI::getInstance()->criarSequencialNativa('seq_md_pet_int_rel_tipo_resp', 1);


        $this->logar('CRIANDO A TABELA md_pet_int_rel_dest');

        BancoSEI::getInstance()->executarSql('CREATE TABLE md_pet_int_rel_dest (
                id_md_pet_int_rel_dest ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL ,
                sin_ativo ' . $objInfraMetaBD->tipoTextoFixo(1) . ' NULL ,
                sin_pessoa_juridica ' . $objInfraMetaBD->tipoTextoFixo(1) . ' NULL ,
                id_md_pet_intimacao ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL ,
                id_contato ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL ,
                id_acesso_externo ' . $objInfraMetaBD->tipoNumero() . ' NULL ,
                id_unidade ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL ,
                data_cadastro ' . $objInfraMetaBD->tipoDataHora() . ' NOT NULL,
                sta_situacao_intimacao ' . $objInfraMetaBD->tipoTextoFixo(1) . ' NOT NULL )'
        );

        $objInfraMetaBD->adicionarChavePrimaria('md_pet_int_rel_dest', 'pk_md_pet_int_rel_dest', array('id_md_pet_int_rel_dest'));
        $objInfraMetaBD->adicionarChaveEstrangeira('fk1_md_pet_int_rel_dest', 'md_pet_int_rel_dest', array('id_contato'), 'contato', array('id_contato'));
        $objInfraMetaBD->adicionarChaveEstrangeira('fk2_md_pet_int_rel_dest', 'md_pet_int_rel_dest', array('id_md_pet_intimacao'), 'md_pet_intimacao', array('id_md_pet_intimacao'));
        $objInfraMetaBD->adicionarChaveEstrangeira('fk3_md_pet_int_rel_dest', 'md_pet_int_rel_dest', array('id_acesso_externo'), 'acesso_externo', array('id_acesso_externo'));
        $objInfraMetaBD->adicionarChaveEstrangeira('fk4_md_pet_int_rel_dest', 'md_pet_int_rel_dest', array('id_unidade'), 'unidade', array('id_unidade'));


        $this->logar('CRIA��O DA SEQUENCE seq_md_pet_int_rel_dest');
        BancoSEI::getInstance()->criarSequencialNativa('seq_md_pet_int_rel_dest', 1);


        $this->logar('CRIANDO A TABELA md_pet_int_protocolo');

        BancoSEI::getInstance()->executarSql('CREATE TABLE md_pet_int_protocolo (
               id_md_pet_int_protocolo ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL ,
               sin_principal ' . $objInfraMetaBD->tipoTextoFixo(1) . ' NOT NULL ,
               id_md_pet_intimacao ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL ,
               id_protocolo ' . $objInfraMetaBD->tipoNumeroGrande() . ' NOT NULL )'
        );

        $objInfraMetaBD->adicionarChavePrimaria('md_pet_int_protocolo', 'pk_md_pet_int_protocolo', array('id_md_pet_int_protocolo'));
        $objInfraMetaBD->adicionarChaveEstrangeira('fk1_md_pet_int_protocolo', 'md_pet_int_protocolo', array('id_protocolo'), 'protocolo', array('id_protocolo'));
        $objInfraMetaBD->adicionarChaveEstrangeira('fk2_md_pet_int_protocolo', 'md_pet_int_protocolo', array('id_md_pet_intimacao'), 'md_pet_intimacao', array('id_md_pet_intimacao'));


        $this->logar('CRIA��O DA SEQUENCE seq_md_pet_int_protocolo');
        BancoSEI::getInstance()->criarSequencialNativa('seq_md_pet_int_protocolo', 1);


        $this->logar('CRIANDO A TABELA md_pet_int_prot_disponivel');

        BancoSEI::getInstance()->executarSql('CREATE TABLE md_pet_int_prot_disponivel (
                id_md_pet_intimacao ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL ,
                id_protocolo ' . $objInfraMetaBD->tipoNumeroGrande() . ' NOT NULL)'
        );

        $objInfraMetaBD->adicionarChavePrimaria('md_pet_int_prot_disponivel', 'pk_md_pet_int_prot_disponivel', array('id_protocolo', 'id_md_pet_intimacao'));
        $objInfraMetaBD->adicionarChaveEstrangeira('fk1_md_pet_int_prot_disponivel', 'md_pet_int_prot_disponivel', array('id_protocolo'), 'protocolo', array('id_protocolo'));
        $objInfraMetaBD->adicionarChaveEstrangeira('fk2_md_pet_int_prot_disponivel', 'md_pet_int_prot_disponivel', array('id_md_pet_intimacao'), 'md_pet_intimacao', array('id_md_pet_intimacao'));


        $this->logar('CRIANDO A TABELA md_pet_int_dest_resposta');

        BancoSEI::getInstance()->executarSql('CREATE TABLE md_pet_int_dest_resposta (
                id_md_pet_int_dest_resposta ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL ,
                id_md_pet_int_rel_dest ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL ,
                ip ' . $objInfraMetaBD->tipoTextoVariavel(45) . ' NULL ,
                data ' . $objInfraMetaBD->tipoDataHora() . ' NULL ,
                id_md_pet_int_rel_tipo_resp ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL)'
        );

        $objInfraMetaBD->adicionarChavePrimaria('md_pet_int_dest_resposta', 'pk_md_pet_int_dest_resposta', array('id_md_pet_int_dest_resposta'));
        $objInfraMetaBD->adicionarChaveEstrangeira('fk1_md_pet_int_dest_resposta', 'md_pet_int_dest_resposta', array('id_md_pet_int_rel_dest'), 'md_pet_int_rel_dest', array('id_md_pet_int_rel_dest'));
        $objInfraMetaBD->adicionarChaveEstrangeira('fk2_md_pet_int_dest_resposta', 'md_pet_int_dest_resposta', array('id_md_pet_int_rel_tipo_resp'), 'md_pet_int_rel_tipo_resp', array('id_md_pet_int_rel_tipo_resp'));


        $this->logar('CRIA��O DA SEQUENCE seq_md_pet_int_dest_resposta');
        BancoSEI::getInstance()->criarSequencialNativa('seq_md_pet_int_dest_resposta', 1);


        $this->logar('CRIANDO A TABELA md_pet_int_rel_resp_doc');

        BancoSEI::getInstance()->executarSql('CREATE TABLE md_pet_int_rel_resp_doc ( 
              id_md_pet_int_resp_documento ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL ,
              id_md_pet_int_dest_resposta ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL ,
              id_documento ' . $objInfraMetaBD->tipoNumeroGrande() . ' NOT NULL )'
        );

        $objInfraMetaBD->adicionarChavePrimaria('md_pet_int_rel_resp_doc', 'pk_md_pet_int_rel_resp_doc', array('id_md_pet_int_resp_documento'));
        $objInfraMetaBD->adicionarChaveEstrangeira('fk1_md_pet_int_rel_resp_doc', 'md_pet_int_rel_resp_doc', array('id_documento'), 'documento', array('id_documento'));
        $objInfraMetaBD->adicionarChaveEstrangeira('fk2_md_pet_int_rel_resp_doc', 'md_pet_int_rel_resp_doc', array('id_md_pet_int_dest_resposta'), 'md_pet_int_dest_resposta', array('id_md_pet_int_dest_resposta'));


        $this->logar('CRIANDO A SEQUENCE seq_md_pet_int_rel_resp_doc');
        BancoSEI::getInstance()->criarSequencialNativa('seq_md_pet_int_rel_resp_doc', 1);


        $this->logar('CRIANDO A TABELA md_pet_int_aceite');

        BancoSEI::getInstance()->executarSql('CREATE TABLE md_pet_int_aceite (
                id_md_pet_int_aceite ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL ,
                ip ' . $objInfraMetaBD->tipoTextoVariavel(45) . ' NULL ,
                data ' . $objInfraMetaBD->tipoDataHora() . ' NULL ,
                data_consulta_direta ' . $objInfraMetaBD->tipoDataHora() . ' NULL ,
                id_md_pet_int_rel_dest ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL ,
                id_documento_certidao ' . $objInfraMetaBD->tipoNumeroGrande() . ' NULL ,
                tipo_aceite ' . $objInfraMetaBD->tipoTextoFixo(1) . ' NOT NULL )'
        );

        $objInfraMetaBD->adicionarChavePrimaria('md_pet_int_aceite', 'pk_md_pet_int_aceite', array('id_md_pet_int_aceite'));

        $objInfraMetaBD->adicionarChaveEstrangeira('fk1_md_pet_int_aceite_doc', 'md_pet_int_aceite', array('id_documento_certidao'), 'documento', array('id_documento'));
        $objInfraMetaBD->adicionarChaveEstrangeira('fk2_md_pet_int_aceite_doc', 'md_pet_int_aceite', array('id_md_pet_int_rel_dest'), 'md_pet_int_rel_dest', array('id_md_pet_int_rel_dest'));


        $this->logar('CRIANDO A TABELA md_pet_int_aceite');

        BancoSEI::getInstance()->criarSequencialNativa('seq_md_pet_int_aceite', 1);


        $this->logar('CRIANDO A TABELA md_pet_int_rel_tpo_res_des');

        BancoSEI::getInstance()->executarSql('CREATE TABLE md_pet_int_rel_tpo_res_des (
                id_md_pet_int_rel_tipo_res_des ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL ,
                id_md_pet_int_rel_tipo_resp ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL ,
                id_md_pet_int_rel_dest ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL ,
                data_limite ' . $objInfraMetaBD->tipoDataHora() . ' NOT NULL ,
                data_prorrogada ' . $objInfraMetaBD->tipoDataHora() . ' NULL )'
        );

        $objInfraMetaBD->adicionarChavePrimaria('md_pet_int_rel_tpo_res_des', 'pk_md_pet_int_rel_tipo_res_des', array('id_md_pet_int_rel_tipo_res_des'));
        $objInfraMetaBD->adicionarChaveEstrangeira('fk1_md_pt_it_rl_tp_rp_tp_rp_dt', 'md_pet_int_rel_tpo_res_des', array('id_md_pet_int_rel_tipo_resp'), 'md_pet_int_rel_tipo_resp', array('id_md_pet_int_rel_tipo_resp'));
        $objInfraMetaBD->adicionarChaveEstrangeira('fk2_md_pt_it_rl_dst_tp_rp_dst', 'md_pet_int_rel_tpo_res_des', array('id_md_pet_int_rel_dest'), 'md_pet_int_rel_dest', array('id_md_pet_int_rel_dest'));


        $this->logar('CRIA��O DA SEQUENCE seq_md_pet_int_rel_tpo_res_des');
        BancoSEI::getInstance()->criarSequencialNativa('seq_md_pet_int_rel_tpo_res_des', 1);


        $this->logar('CRIA��O DA SEQUENCE seq_md_pet_int_resp_documento');
        BancoSEI::getInstance()->criarSequencialNativa('seq_md_pet_int_resp_documento', 1);


        $this->logar('CRIA��O DOS AGENDAMENTOS AUTOM�TICOS DO M�DULO');

        $infraAgendamentoDTO = new InfraAgendamentoTarefaDTO();
        $infraAgendamentoDTO->retTodos();
        $infraAgendamentoDTO->setStrDescricao('Script para cumprimento autom�tico de intima��o por decurso de prazo');

        $infraAgendamentoDTO->setStrComando('MdPetAgendamentoAutomaticoRN::CumprirPorDecursoPrazoTacito');

        $infraAgendamentoDTO->setStrSinAtivo('S');
        $infraAgendamentoDTO->setStrStaPeriodicidadeExecucao(InfraAgendamentoTarefaRN::$PERIODICIDADE_EXECUCAO_HORA);
        $infraAgendamentoDTO->setStrPeriodicidadeComplemento(23);
        $infraAgendamentoDTO->setStrParametro(null);
        $infraAgendamentoDTO->setDthUltimaExecucao(null);
        $infraAgendamentoDTO->setDthUltimaConclusao(null);
        $infraAgendamentoDTO->setStrSinSucesso('S');
        $infraAgendamentoDTO->setStrEmailErro(null);

        $infraAgendamentoRN = new InfraAgendamentoTarefaRN();
        $infraAgendamentoDTO = $infraAgendamentoRN->cadastrar($infraAgendamentoDTO);

        $infraAgendamentoDTO = new InfraAgendamentoTarefaDTO();
        $infraAgendamentoDTO->retTodos();
        $infraAgendamentoDTO->setStrDescricao('Script para atualizar os estados das Intima��es com Prazo Externo Vencido');

        $infraAgendamentoDTO->setStrComando('MdPetAgendamentoAutomaticoRN::atualizarEstadoIntimacoesPrazoExternoVencido');

        $infraAgendamentoDTO->setStrSinAtivo('S');
        $infraAgendamentoDTO->setStrStaPeriodicidadeExecucao(InfraAgendamentoTarefaRN::$PERIODICIDADE_EXECUCAO_HORA);
        $infraAgendamentoDTO->setStrPeriodicidadeComplemento(0);
        $infraAgendamentoDTO->setStrParametro(null);
        $infraAgendamentoDTO->setDthUltimaExecucao(null);
        $infraAgendamentoDTO->setDthUltimaConclusao(null);
        $infraAgendamentoDTO->setStrSinSucesso('S');
        $infraAgendamentoDTO->setStrEmailErro(null);

        $infraAgendamentoRN = new InfraAgendamentoTarefaRN();
        $infraAgendamentoDTO = $infraAgendamentoRN->cadastrar($infraAgendamentoDTO);

        $infraAgendamentoDTO = new InfraAgendamentoTarefaDTO();
        $infraAgendamentoDTO->retTodos();
        $infraAgendamentoDTO->setStrDescricao('Dispara E-mails do Sistema do M�dulo de Peticionamento e Intima��o Eletr�nicos de Reitera��o de Intima��o Eletr�nica que Exige Resposta pendentes de Resposta pelo Usu�rio Externo');

        $infraAgendamentoDTO->setStrComando('MdPetAgendamentoAutomaticoRN::ReiterarIntimacaoExigeResposta');

        $infraAgendamentoDTO->setStrSinAtivo('S');
        $infraAgendamentoDTO->setStrStaPeriodicidadeExecucao(InfraAgendamentoTarefaRN::$PERIODICIDADE_EXECUCAO_HORA);
        $infraAgendamentoDTO->setStrPeriodicidadeComplemento(7);
        $infraAgendamentoDTO->setStrParametro(null);
        $infraAgendamentoDTO->setDthUltimaExecucao(null);
        $infraAgendamentoDTO->setDthUltimaConclusao(null);
        $infraAgendamentoDTO->setStrSinSucesso('S');
        $infraAgendamentoDTO->setStrEmailErro(null);

        $infraAgendamentoRN = new InfraAgendamentoTarefaRN();
        $infraAgendamentoDTO = $infraAgendamentoRN->cadastrar($infraAgendamentoDTO);

        //checar se precisa atualizar infra_parametro ID_SERIE_RECIBO_MODULO_PETICIONAMENTO
        $idParamAntigo = 'ID_SERIE_RECIBO_MODULO_PETICIONAMENTO';
        $objInfraParamRN = new InfraParametroRN();
        $objInfraParamDTO = new InfraParametroDTO();
        $objInfraParamDTO->retTodos();
        $objInfraParamDTO->setStrNome($idParamAntigo);

        $arrObjInfraParamDTO = $objInfraParamRN->listar($objInfraParamDTO);

        if (is_array($arrObjInfraParamDTO) && count($arrObjInfraParamDTO) > 0) {
            BancoSEI::getInstance()->executarSql("UPDATE infra_parametro SET nome ='" . MdPetIntSerieRN::$MD_PET_ID_SERIE_RECIBO . "'  WHERE nome = '" . $idParamAntigo . "'");
        }

        //Altera��o na tarefa "Cancelada disponibiliza��o de acesso externo", passando a permitir em PROCESSO FECHADO
        $tarefaDTO = new TarefaDTO();
        $tarefaDTO->setNumIdTarefa(90);
        $tarefaDTO->setStrSinPermiteProcessoFechado('S');

        $tarefaRN = new TarefaRN();
        $tarefaRN->alterar($tarefaDTO);


        $this->logar('ATUALIZANDO PAR�METRO ' . $this->nomeParametroModulo . ' NA TABELA infra_parametro PARA CONTROLAR A VERS�O DO M�DULO');
        BancoSEI::getInstance()->executarSql('UPDATE infra_parametro SET valor = \'2.0.0\' WHERE nome = \'' . $this->nomeParametroModulo . '\' ');

        $this->logar('INSTALA��O/ATUALIZA��O DA VERS�O ' . $this->versaoAtualDesteModulo . ' DO ' . $this->nomeDesteModulo . ' REALIZADA COM SUCESSO NA BASE DO SEI');
    }

    protected function instalarv201()
    {

        $this->logar('EXECUTANDO A INSTALA��O/ATUALIZA��O DA VERS�O 2.0.1 DO ' . $this->nomeDesteModulo . ' NA BASE DO SEI');

        $this->logar('ATUALIZANDO PAR�METRO ' . $this->nomeParametroModulo . ' NA TABELA infra_parametro PARA CONTROLAR A VERS�O DO M�DULO');
        BancoSEI::getInstance()->executarSql('UPDATE infra_parametro SET valor = \'2.0.1\' WHERE nome = \'' . $this->nomeParametroModulo . '\' ');

        $this->logar('INSTALA��O/ATUALIZA��O DA VERS�O ' . $this->versaoAtualDesteModulo . ' DO ' . $this->nomeDesteModulo . ' REALIZADA COM SUCESSO NA BASE DO SEI');
    }

    protected function instalarv202()
    {

        $this->logar('EXECUTANDO A INSTALA��O/ATUALIZA��O DA VERS�O 2.0.2 DO ' . $this->nomeDesteModulo . ' NA BASE DO SEI');

        //checando permissoes na base de dados
        $objInfraMetaBD = new InfraMetaBD(BancoSEI::getInstance());

        if (count($objInfraMetaBD->obterTabelas('md_pet_indisp_anexo')) > 0) {
            $this->logar('DELETANDO A TABELA md_pet_indisp_anexo');
            BancoSEI::getInstance()->executarSql('DROP TABLE md_pet_indisp_anexo');

            $this->logar('DELETANDO A SEQUENCE seq_md_pet_indisp_anexo');
            if ((BancoSEI::getInstance() instanceof InfraMySql) OR (BancoSEI::getInstance() instanceof InfraSqlServer)) {
                BancoSEI::getInstance()->executarSql("DROP TABLE seq_md_pet_indisp_anexo");
            } else if (BancoSEI::getInstance() instanceof InfraOracle) {
                BancoSEI::getInstance()->executarSql("DROP SEQUENCE seq_md_pet_indisp_anexo");
            }
        }

        if (count($objInfraMetaBD->obterTabelas('md_pet_indisp_doc')) == 0) {
            $this->logar('CRIANDO A TABELA md_pet_indisp_doc');

            BancoSEI::getInstance()->executarSql('CREATE TABLE md_pet_indisp_doc (
                id_md_pet_indisp_doc ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL,
                id_md_pet_indisponibilidade ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL,
                id_unidade ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL,
                id_usuario ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL,
                id_documento ' . $objInfraMetaBD->tipoNumeroGrande() . ' NULL,
                id_acesso_externo ' . $objInfraMetaBD->tipoNumero() . ' NULL,
                dth_inclusao ' . $objInfraMetaBD->tipoDataHora() . ' NOT NULL,
                sin_ativo ' . $objInfraMetaBD->tipoTextoFixo(1) . ' NOT NULL
                )'
            );

            $objInfraMetaBD->adicionarChavePrimaria('md_pet_indisp_doc', 'pk_md_pet_indisp_doc', array('id_md_pet_indisp_doc'));
            $objInfraMetaBD->adicionarChaveEstrangeira('fk_md_pet_indisp_doc_01', 'md_pet_indisp_doc', array('id_md_pet_indisponibilidade'), 'md_pet_indisponibilidade', array('id_md_pet_indisponibilidade'));
            $objInfraMetaBD->adicionarChaveEstrangeira('fk_md_pet_indisp_doc_02', 'md_pet_indisp_doc', array('id_unidade'), 'unidade', array('id_unidade'));
            $objInfraMetaBD->adicionarChaveEstrangeira('fk_md_pet_indisp_doc_03', 'md_pet_indisp_doc', array('id_usuario'), 'usuario', array('id_usuario'));
            $objInfraMetaBD->adicionarChaveEstrangeira('fk_md_pet_indisp_doc_04', 'md_pet_indisp_doc', array('id_documento'), 'documento', array('id_documento'));
            $objInfraMetaBD->adicionarChaveEstrangeira('fk_md_pet_indisp_doc_05', 'md_pet_indisp_doc', array('id_acesso_externo'), 'acesso_externo', array('id_acesso_externo'));

            $this->logar('CRIANDO A SEQUENCE seq_md_pet_indisp_doc');
            BancoSEI::getInstance()->criarSequencialNativa('seq_md_pet_indisp_doc', 1);
        }

        $colunasTabela = $objInfraMetaBD->obterColunasTabela('md_pet_rel_recibo_protoc', 'nome_tipo_intimacao');
        if (count($colunasTabela) == 1 && $colunasTabela[0]['column_name'] == 'nome_tipo_intimacao') {
            $this->logar('DELETANDO A COLUNA md_pet_rel_recibo_protoc.nome_tipo_intimacao');
            $objInfraMetaBD->excluirColuna('md_pet_rel_recibo_protoc', 'nome_tipo_intimacao');
        }

        $colunasTabela = $objInfraMetaBD->obterColunasTabela('md_pet_rel_recibo_protoc', 'nome_tipo_resposta');
        if (count($colunasTabela) == 1 && $colunasTabela[0]['column_name'] == 'nome_tipo_resposta') {
            $this->logar('DELETANDO A COLUNA md_pet_rel_recibo_protoc.nome_tipo_resposta');
            $objInfraMetaBD->excluirColuna('md_pet_rel_recibo_protoc', 'nome_tipo_resposta');
        }

        if (count($objInfraMetaBD->obterTabelas('md_pet_usu_ext_processo')) == 1) {
            $this->logar('DELETANDO A TABELA md_pet_usu_ext_processo');
            BancoSEI::getInstance()->executarSql('DROP TABLE md_pet_usu_ext_processo');
        }

        $this->logar('ATUALIZANDO PAR�METRO ' . $this->nomeParametroModulo . ' NA TABELA infra_parametro PARA CONTROLAR A VERS�O DO M�DULO');
        BancoSEI::getInstance()->executarSql('UPDATE infra_parametro SET valor = \'2.0.2\' WHERE nome = \'' . $this->nomeParametroModulo . '\' ');

        $this->logar('INSTALA��O/ATUALIZA��O DA VERS�O ' . $this->versaoAtualDesteModulo . ' DO ' . $this->nomeDesteModulo . ' REALIZADA COM SUCESSO NA BASE DO SEI');
    }

    protected function instalarv203()
    {

        $this->logar('EXECUTANDO A INSTALA��O/ATUALIZA��O DA VERS�O 2.0.3 DO ' . $this->nomeDesteModulo . ' NA BASE DO SEI');

        $this->logar('ATUALIZANDO PAR�METRO ' . $this->nomeParametroModulo . ' NA TABELA infra_parametro PARA CONTROLAR A VERS�O DO M�DULO');
        BancoSEI::getInstance()->executarSql('UPDATE infra_parametro SET valor = \'2.0.3\' WHERE nome = \'' . $this->nomeParametroModulo . '\' ');

        $this->logar('INSTALA��O/ATUALIZA��O DA VERS�O ' . $this->versaoAtualDesteModulo . ' DO ' . $this->nomeDesteModulo . ' REALIZADA COM SUCESSO NA BASE DO SEI');
    }

    protected function instalarv204()
    {

        $this->logar('EXECUTANDO A INSTALA��O/ATUALIZA��O DA VERS�O 2.0.4 DO ' . $this->nomeDesteModulo . ' NA BASE DO SEI');

        $this->logar('ATUALIZANDO PAR�METRO ' . $this->nomeParametroModulo . ' NA TABELA infra_parametro PARA CONTROLAR A VERS�O DO M�DULO');
        BancoSEI::getInstance()->executarSql('UPDATE infra_parametro SET valor = \'2.0.4\' WHERE nome = \'' . $this->nomeParametroModulo . '\' ');

        $this->logar('INSTALA��O/ATUALIZA��O DA VERS�O ' . $this->versaoAtualDesteModulo . ' DO ' . $this->nomeDesteModulo . ' REALIZADA COM SUCESSO NA BASE DO SEI');
    }

    protected function instalarv205()
    {

        $this->logar('EXECUTANDO A INSTALA��O/ATUALIZA��O DA VERS�O 2.0.5 DO ' . $this->nomeDesteModulo . ' NA BASE DO SEI');

        $this->logar('ATUALIZANDO PAR�METRO ' . $this->nomeParametroModulo . ' NA TABELA infra_parametro PARA CONTROLAR A VERS�O DO M�DULO');
        BancoSEI::getInstance()->executarSql('UPDATE infra_parametro SET valor = \'2.0.5\' WHERE nome = \'' . $this->nomeParametroModulo . '\' ');

        $this->logar('INSTALA��O/ATUALIZA��O DA VERS�O ' . $this->versaoAtualDesteModulo . ' DO ' . $this->nomeDesteModulo . ' REALIZADA COM SUCESSO NA BASE DO SEI');
    }

    protected function instalarv300()
    {
        $objInfraMetaBD = new InfraMetaBD(BancoSEI::getInstance());

        $this->logar('EXECUTANDO A INSTALA��O/ATUALIZA��O DA VERS�O 3.0.0 DO ' . $this->nomeDesteModulo . ' NA BASE DO SEI');

        $this->logar('ATUALIZANDO A DESCRI��O DOS E-MAILS DE SISTEMA DE INTIMA��O ELETR�NICA DE PESSOA F�SICA');

        $arrEmailSistemas = array(
            'MD_PET_INTIMACAO_APENAS_RESPOSTAS_FACULTATIVAS' => 'Peticionamento Eletr�nico - Intima��o Eletr�nica apenas com Respostas Facultativas - Pessoa F�sica',
            'MD_PET_INTIMACAO_QUE_EXIGE_RESPOSTA' => 'Peticionamento Eletr�nico - Intima��o Eletr�nica que Exige Resposta - Pessoa F�sica',
            'MD_PET_REITERACAO_INTIMACAO_QUE_EXIGE_RESPOSTA' => 'Peticionamento Eletr�nico - Reitera��o de Intima��o Eletr�nica que Exige Resposta - Pessoa F�sica',
            'MD_PET_INTIMACAO_SEM_RESPOSTA' => 'Peticionamento Eletr�nico - Intima��o Eletr�nica Sem Resposta - Pessoa F�sica'
        );

        $emailSistemasRN = new EmailSistemaRN();
        foreach ($arrEmailSistemas as $chave => $item) {
            $emailSistemaDTO = new EmailSistemaDTO();
            $emailSistemaDTO->setStrIdEmailSistemaModulo($chave);
            $emailSistemaDTO->retTodos();
            $objEmailSistemaDTO = $emailSistemasRN->consultar($emailSistemaDTO);
            $objEmailSistemaDTO->setStrDescricao($item);
            $emailSistemasRN->alterar($objEmailSistemaDTO);
        }

        //===============================
        //INICIO - Tabelas e Colunas novas do M�dulo Peticionamento v3.0.0
        //==============================
        $this->logar('CRIANDO A TABELA md_pet_adm_integ_funcion');

        $sql_tabelas = 'CREATE TABLE md_pet_adm_integ_funcion (
              id_md_pet_adm_integ_funcion ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL ,
              nome ' . $objInfraMetaBD->tipoTextoVariavel(100) . ' NOT NULL
              )';

        BancoSEI::getInstance()->executarSql($sql_tabelas);
        $objInfraMetaBD->adicionarChavePrimaria('md_pet_adm_integ_funcion', 'pk_md_pet_adm_integ_funcion', array('id_md_pet_adm_integ_funcion'));


        $this->logar('CRIANDO A SEQUENCE seq_md_pet_adm_integ_funcion');
        BancoSEI::getInstance()->criarSequencialNativa('seq_md_pet_adm_integ_funcion', 1);


        $this->logar('CRIANDO A TABELA md_pet_adm_integracao');

        $sql_tabelas = 'CREATE TABLE md_pet_adm_integracao (
              id_md_pet_adm_integracao ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL ,
              id_md_pet_adm_integ_funcion ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL ,
              nome ' . $objInfraMetaBD->tipoTextoVariavel(30) . ' NOT NULL ,              
              sta_utilizar_ws ' . $objInfraMetaBD->tipoTextoFixo(1) . ' NOT NULL ,
              endereco_wsdl ' . $objInfraMetaBD->tipoTextoVariavel(100) . ' NULL,
              operacao_wsdl ' . $objInfraMetaBD->tipoTextoVariavel(50) . ' NULL,
              sin_cache ' . $objInfraMetaBD->tipoTextoFixo(1) . ' NULL,
              sin_ativo ' . $objInfraMetaBD->tipoTextoFixo(1) . ' NULL
              )';

        BancoSEI::getInstance()->executarSql($sql_tabelas);
        $objInfraMetaBD->adicionarChavePrimaria('md_pet_adm_integracao', 'pk_md_pet_adm_integracao', array('id_md_pet_adm_integracao'));
        $objInfraMetaBD->adicionarChaveEstrangeira('fk1_md_pet_adm_integracao', 'md_pet_adm_integracao', array('id_md_pet_adm_integ_funcion'), 'md_pet_adm_integ_funcion', array('id_md_pet_adm_integ_funcion'));


        $this->logar('CRIANDO A TABELA seq_md_pet_adm_integracao');

        BancoSEI::getInstance()->criarSequencialNativa('seq_md_pet_adm_integracao', 1);


        $this->logar('CRIANDO A TABELA md_pet_adm_integ_param');

        $sql_tabelas = 'CREATE TABLE md_pet_adm_integ_param (
              id_md_pet_adm_integ_param ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL ,
              id_md_pet_adm_integracao ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL ,
              nome ' . $objInfraMetaBD->tipoTextoVariavel(30) . ' NOT NULL ,
              tp_parametro ' . $objInfraMetaBD->tipoTextoFixo(1) . ' NOT NULL ,
              nome_campo ' . $objInfraMetaBD->tipoTextoVariavel(50) . ' NULL ,
              valor_padrao ' . $objInfraMetaBD->tipoTextoVariavel(100) . ' NULL
              )';

        BancoSEI::getInstance()->executarSql($sql_tabelas);
        $objInfraMetaBD->adicionarChavePrimaria('md_pet_adm_integ_param', 'pk_md_pet_adm_integ_param', array('id_md_pet_adm_integ_param'));
        $objInfraMetaBD->adicionarChaveEstrangeira('fk1_md_pet_adm_integ_param', 'md_pet_adm_integ_param', array('id_md_pet_adm_integracao'), 'md_pet_adm_integracao', array('id_md_pet_adm_integracao'));


        $this->logar('CRIANDO A SEQUENCE seq_md_pet_adm_integ_param');
        BancoSEI::getInstance()->criarSequencialNativa('seq_md_pet_adm_integ_param', 1);


        $this->logar('CRIANDO A TABELA md_pet_adm_vinc_tp_proced');

        $sql_tabelas = 'CREATE TABLE md_pet_adm_vinc_tp_proced (
              id_md_pet_adm_vinc_tp_proced ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL ,
              id_tipo_procedimento ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL,
              id_unidade ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL,
              id_hipotese_legal ' . $objInfraMetaBD->tipoNumero() . ' NULL,
              orientacoes ' . $objInfraMetaBD->tipoTextoGrande() . '  NULL,
              sin_na_usuario_externo ' . $objInfraMetaBD->tipoTextoFixo(1) . ' NOT NULL,
              sin_na_padrao ' . $objInfraMetaBD->tipoTextoFixo(1) . ' NOT NULL,
              sta_nivel_acesso ' . $objInfraMetaBD->tipoTextoFixo(1) . ' NULL,
              sin_ativo ' . $objInfraMetaBD->tipoTextoFixo(1) . ' NOT NULL
              )';

        BancoSEI::getInstance()->executarSql($sql_tabelas);
        $objInfraMetaBD->adicionarChavePrimaria('md_pet_adm_vinc_tp_proced', 'pk_md_pet_adm_vinc_tp_proced', array('id_md_pet_adm_vinc_tp_proced'));
        $objInfraMetaBD->adicionarChaveEstrangeira('fk1_md_pet_adm_vinc_tp_proced', 'md_pet_adm_vinc_tp_proced', array('id_tipo_procedimento'), 'tipo_procedimento', array('id_tipo_procedimento'));
        $objInfraMetaBD->adicionarChaveEstrangeira('fk2_md_pet_adm_vinc_tp_proced', 'md_pet_adm_vinc_tp_proced', array('id_unidade'), 'unidade', array('id_unidade'));
        $objInfraMetaBD->adicionarChaveEstrangeira('fk3_md_pet_adm_vinc_tp_proced', 'md_pet_adm_vinc_tp_proced', array('id_hipotese_legal'), 'hipotese_legal', array('id_hipotese_legal'));


        $this->logar('CRIANDO A TABELA md_pet_adm_vinc_rel_serie');

        $sql_tabelas = 'CREATE TABLE md_pet_adm_vinc_rel_serie (
              id_md_pet_adm_vinc_rel_ser ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL ,
              id_md_pet_adm_vinc_tp_proced ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL,
              id_serie ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL,
              sin_obrigatorio ' . $objInfraMetaBD->tipoTextoFixo(1) . ' NOT NULL
              )';

        BancoSEI::getInstance()->executarSql($sql_tabelas);
        $objInfraMetaBD->adicionarChavePrimaria('md_pet_adm_vinc_rel_serie', 'pk_md_pet_adm_vinc_rel_serie', array('id_md_pet_adm_vinc_rel_ser'));
        $objInfraMetaBD->adicionarChaveEstrangeira('fk1_pet_adm_vinc_rel_serie', 'md_pet_adm_vinc_rel_serie', array('id_md_pet_adm_vinc_tp_proced'), 'md_pet_adm_vinc_tp_proced', array('id_md_pet_adm_vinc_tp_proced'));
        $objInfraMetaBD->adicionarChaveEstrangeira('fk2_pet_adm_vinc_rel_serie', 'md_pet_adm_vinc_rel_serie', array('id_serie'), 'serie', array('id_serie'));


        $this->logar('CRIANDO A SEQUENCE seq_md_pet_adm_vinc_rel_serie');
        BancoSEI::getInstance()->criarSequencialNativa('seq_md_pet_adm_vinc_rel_serie', 1);


        $this->logar('CRIANDO A TABELA md_pet_vinculo');

        $sql_tabelas = 'CREATE TABLE md_pet_vinculo (
              id_md_pet_vinculo ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL ,
              id_contato ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL,
              id_procedimento ' . $objInfraMetaBD->tipoNumeroGrande() . ' NOT NULL,
              sin_validado ' . $objInfraMetaBD->tipoTextoFixo(1) . ' NOT NULL,
              sin_web_service ' . $objInfraMetaBD->tipoTextoFixo(1) . ' NOT NULL,
              tp_vinculo ' . $objInfraMetaBD->tipoTextoFixo(1) . ' NOT NULL
              )';
        BancoSEI::getInstance()->executarSql($sql_tabelas);

        $objInfraMetaBD->adicionarChavePrimaria('md_pet_vinculo', 'pk_md_pet_vinculo', array('id_md_pet_vinculo'));
        $objInfraMetaBD->adicionarChaveEstrangeira('fk1_md_pet_vinculo', 'md_pet_vinculo', array('id_contato'), 'contato', array('id_contato'));
        $objInfraMetaBD->adicionarChaveEstrangeira('fk2_md_pet_vinculo', 'md_pet_vinculo', array('id_procedimento'), 'procedimento', array('id_procedimento'));


        $this->logar('CRIANDO A SEQUENCE seq_md_pet_vinculo');
        BancoSEI::getInstance()->criarSequencialNativa('seq_md_pet_vinculo', 1);


        $this->logar('CRIANDO A TABELA md_pet_vinculo_represent');

        $sql_tabelas = 'CREATE TABLE md_pet_vinculo_represent (
              id_md_pet_vinculo_represent ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL,
              id_md_pet_vinculo ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL,
              id_contato ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL,
              id_contato_outorg ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL,  
              tipo_representante ' . $objInfraMetaBD->tipoTextoFixo(1) . ' NOT NULL,
              sin_ativo ' . $objInfraMetaBD->tipoTextoFixo(1) . ' NOT NULL,
              data_cadastro ' . $objInfraMetaBD->tipoDataHora() . ' NOT NULL,
              sta_estado ' . $objInfraMetaBD->tipoTextoFixo(1) . ' NOT NULL,
              motivo ' . $objInfraMetaBD->tipoTextoVariavel(250) . ' NULL,
              data_encerramento ' . $objInfraMetaBD->tipoDataHora() . ' NULL  
            )';

        BancoSEI::getInstance()->executarSql($sql_tabelas);

        $objInfraMetaBD->adicionarChavePrimaria('md_pet_vinculo_represent', 'pk_md_pet_vinculo_represent', array('id_md_pet_vinculo_represent'));
        $objInfraMetaBD->adicionarChaveEstrangeira('fk1_md_pet_vinculo_represent', 'md_pet_vinculo_represent', array('id_md_pet_vinculo'), 'md_pet_vinculo', array('id_md_pet_vinculo'));
        $objInfraMetaBD->adicionarChaveEstrangeira('fk2_md_pet_vinculo_represent', 'md_pet_vinculo_represent', array('id_contato'), 'contato', array('id_contato'));
        $objInfraMetaBD->adicionarChaveEstrangeira('fk3_md_pet_vinculo_represent', 'md_pet_vinculo_represent', array('id_contato_outorg'), 'contato', array('id_contato'));


        $this->logar('CRIANDO A SEQUENCE seq_md_pet_vinculo_represent');
        BancoSEI::getInstance()->criarSequencialNativa('seq_md_pet_vinculo_represent', 1);


        $this->logar('CRIANDO A TABELA md_pet_vinculo_documento');

        $sql_tabelas = 'CREATE TABLE md_pet_vinculo_documento (
              id_md_pet_vinculo_documento ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL,
              id_md_pet_vinculo_represent ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL,
              id_documento ' . $objInfraMetaBD->tipoNumeroGrande() . ' NOT NULL,  
              tipo_documento ' . $objInfraMetaBD->tipoTextoFixo(1) . ' NOT NULL, 
              data_cadastro ' . $objInfraMetaBD->tipoDataHora() . ' NOT NULL
              )';

        BancoSEI::getInstance()->executarSql($sql_tabelas);

        $objInfraMetaBD->adicionarChavePrimaria('md_pet_vinculo_documento', 'pk_md_pet_vinculo_documento', array('id_md_pet_vinculo_documento'));
        $objInfraMetaBD->adicionarChaveEstrangeira('fk1_md_pet_vinculo_documento', 'md_pet_vinculo_documento', array('id_md_pet_vinculo_represent'), 'md_pet_vinculo_represent', array('id_md_pet_vinculo_represent'));
        $objInfraMetaBD->adicionarChaveEstrangeira('fk2_md_pet_vinculo_documento', 'md_pet_vinculo_documento', array('id_documento'), 'documento', array('id_documento'));


        $this->logar('CRIANDO A SEQUENCE seq_md_pet_vinculo_documento');
        BancoSEI::getInstance()->criarSequencialNativa('seq_md_pet_vinculo_documento', 1);


        $this->logar('ALTERANDO A TABELA - adicionado md_pet_acesso_externo.sin_vinculo');
        $objInfraMetaBD->adicionarColuna('md_pet_acesso_externo', 'sin_vinculo', $objInfraMetaBD->tipoTextoFixo(1), 'NULL');

        $this->logar('ATUALIZANDO A TABELA - populando novo campo campo sin_vinculo com valor N');
        $sqlTabela = 'UPDATE md_pet_acesso_externo SET sin_vinculo=\'N\' WHERE sin_vinculo IS NULL';
        BancoSEI::getInstance()->executarSql($sqlTabela);

        $this->logar('ALTERANDO A TABELA - alterando md_pet_acesso_externo.sin_vinculo para NOT NULL');
        $objInfraMetaBD->alterarColuna('md_pet_acesso_externo', 'sin_vinculo', $objInfraMetaBD->tipoTextoFixo(1), 'NOT NULL');


        //-- ==============================================================
        //--  Populando a tabela: md_pet_adm_integ_funcion
        //-- ==============================================================
        $this->logar('INSERINDO FUNCIONALIDADE - Consultar Dados CNPJ Receita Federal');

        $objMdPetFuncionalidadeRN = new MdPetIntegFuncionalidRN();
        $objMdPetFuncionalidadeDTO = new MdPetIntegFuncionalidDTO();
        $objMdPetFuncionalidadeDTO->setNumIdMdPetIntegFuncionalid(MdPetIntegFuncionalidRN::$ID_FUNCIONALIDADE_CNPJ_RECEITA_FEDERAL);
        $objMdPetFuncionalidadeDTO->setStrNome('Consultar Dados CNPJ Receita Federal');
        $objMdPetFuncionalidadeRN->cadastrar($objMdPetFuncionalidadeDTO);

        //Gerar Tipo de Documento para Formul�rio
        $this->_gerarModeloFormularioVinculo();

        //Gerar tipo de Documento Procura��o eletronica
        $this->_gerarModeloProcuracaoEletronica();

        //Gerar Tipo de Documento Revoga��o
        $this->_gerarModeloRevogacao();

        //Gerar Tipo de Documento Renuncia
        $this->_gerarModeloRenuncia();

        //Gerar Tipo de Documento Suspender Vinculo
        $this->_gerarModeloSuspenderVinculo();

        //Gerar Tipo de Documento Restabelecer Vinculo
        $this->_gerarModeloRestabelecerVinculo();


        $this->logar('CRIANDO A TABELA md_pet_int_tp_int_orient');

        BancoSEI::getInstance()->executarSql('CREATE TABLE md_pet_int_tp_int_orient ( 
            id_md_pet_int_tp_int_orient ' . $objInfraMetaBD->tipoNumero(11) . ' NOT NULL,
            id_conjunto_estilos ' . $objInfraMetaBD->tipoNumero(11) . ' NULL ,
            orientacoes_tp_destinatario ' . $objInfraMetaBD->tipoTextoGrande() . ' NOT NULL)'
        );

        $objInfraMetaBD->adicionarChavePrimaria('md_pet_int_tp_int_orient', 'pk_md_pet_int_tp_int_orient', array('id_md_pet_int_tp_int_orient'));
        $objInfraMetaBD->adicionarChaveEstrangeira('fk1_md_pet_int_tp_int_orient', 'md_pet_int_tp_int_orient', array('id_conjunto_estilos'), 'conjunto_estilos', array('id_conjunto_estilos'));

        $this->logar('INSERINDO texto(orienta��o) padr�o da tabela md_pet_int_tp_int_orient');

        $objMdPetIntOrientacoesDTO = new MdPetIntOrientacoesDTO();
        $objMdPetIntOrientacoesRN = new MdPetIntOrientacoesRN();
        $objMdPetIntOrientacoesDTO->setStrOrientacoesTipoDestinatario('<p style="font-family:arial,verdana,helvetica,sans-serif; font-size:13px">Caso selecionada a op&ccedil;&atilde;o &quot;Pessoa Jur&iacute;dica&quot;, a Intima&ccedil;&atilde;o Eletr&ocirc;nica ser&aacute; encaminhada ao Respons&aacute;vel Legal e aos portadores de&nbsp;Procura&ccedil;&atilde;o Eletr&ocirc;nica Especial ou de Procura&ccedil;&atilde;o Eletr&ocirc;nica que inclua&nbsp;o poder legal para Recebimento e Cumprimento de Intima&ccedil;&atilde;o Eletr&ocirc;nica. Somente ser&atilde;o listados os CNPJs das Pessoas Jur&iacute;dicas que j&aacute; tenham vinculado pelo menos o Respons&aacute;vel Legal no &acirc;mbito do Acesso Externo do SEI. &Eacute;&nbsp;de responsabilidade exclusiva da Pessoa Jur&iacute;dica manter o Respons&aacute;vel Legal atualizado e&nbsp;a gest&atilde;o das Procura&ccedil;&otilde;es Eletr&ocirc;nicas emitidas.</p><p style="font-family:arial,verdana,helvetica,sans-serif; font-size:13px">Caso selecionada a op&ccedil;&atilde;o &quot;Pessoa F&iacute;sica&quot;, dever&aacute; indicar nominalmente o Usu&aacute;rio Externo destinat&aacute;rio da&nbsp;Intima&ccedil;&atilde;o Eletr&ocirc;nica, ficando sob a responsabilidade de quem gera a intima&ccedil;&atilde;o a confer&ecirc;ncia pr&eacute;via se o destinat&aacute;rio possui poderes de recebimento de intima&ccedil;&atilde;o.</p>');
        $objMdPetIntOrientacoesDTO->setNumIdIntOrientTpDest(MdPetIntOrientacoesRN::$ID_FIXO_INT_ORIENTACOES);
        $objMdPetIntOrientacoesRN->cadastrar($objMdPetIntOrientacoesDTO);

        $this->logar('CRIANDO A TABELA md_pet_rel_int_dest_extern');

        BancoSEI::getInstance()->executarSql('CREATE TABLE md_pet_rel_int_dest_extern ( 
            id_acesso_externo ' . $objInfraMetaBD->tipoNumero(11) . ' NOT NULL,
            id_md_pet_int_rel_dest ' . $objInfraMetaBD->tipoNumero(11) . ' NOT NULL )'
        );
        $objInfraMetaBD->adicionarChavePrimaria('md_pet_rel_int_dest_extern', 'pk1_md_pet_rel_int_dest_extern', array('id_acesso_externo', 'id_md_pet_int_rel_dest'));
        $objInfraMetaBD->adicionarChaveEstrangeira('fk1_md_pet_rel_int_dest_extern', 'md_pet_rel_int_dest_extern', array('id_acesso_externo'), 'acesso_externo', array('id_acesso_externo'));
        $objInfraMetaBD->adicionarChaveEstrangeira('fk2_md_pet_rel_int_dest_extern', 'md_pet_rel_int_dest_extern', array('id_md_pet_int_rel_dest'), 'md_pet_int_rel_dest', array('id_md_pet_int_rel_dest'));

        $this->logar('ADICIONANDO A COLUNA id_usuario NA TABELA md_pet_int_aceite');
        $objInfraMetaBD->adicionarColuna('md_pet_int_aceite', 'id_usuario', $objInfraMetaBD->tipoNumero(), 'NULL');
        $objInfraMetaBD->adicionarChaveEstrangeira('fk_md_pet_int_aceite_doc3', 'md_pet_int_aceite', array('id_usuario'), 'usuario', array('id_usuario'));

        $this->logar('LISTANDO OS DADOS DA TABELA md_pet_int_rel_dest');
        $sqlDest = "select 
                    id_acesso_externo,
                    id_md_pet_int_rel_dest
                from md_pet_int_rel_dest";
        $rs = BancoSEI::getInstance()->consultarSql($sqlDest);

        if ($rs) {
            $this->logar('POPULANDO A TABELA md_pet_rel_int_dest_extern');
            $objMdPetIntRelDestExternoRN = new MdPetRelIntDestExternoRN();
            foreach ($rs as $dados) {
                if ($dados['id_acesso_externo'] != NULL) {
                    $objMdPetIntRelDestExternoDTO = new MdPetRelIntDestExternoDTO();
                    $objMdPetIntRelDestExternoDTO->setNumIdMdPetIntRelDestinatario($dados['id_md_pet_int_rel_dest']);
                    $objMdPetIntRelDestExternoDTO->setNumIdAcessoExterno($dados['id_acesso_externo']);
                    $objMdPetIntRelDestExternoRN->cadastrar($objMdPetIntRelDestExternoDTO);
                }
            }
        }

        $this->logar('DELETANDO A CHAVE ESTRANGEIRA md_pet_int_rel_dest.fk3_md_pet_int_rel_dest');
        $objInfraMetaBD->excluirChaveEstrangeira('md_pet_int_rel_dest', 'fk3_md_pet_int_rel_dest');


        $this->logar('DELETANDO O INDICE md_pet_int_rel_dest.fk3_md_pet_int_rel_dest');
        $arrIndices = $objInfraMetaBD->obterIndices(null, md_pet_int_rel_dest);
        if($arrIndices) {
            foreach ($arrIndices['md_pet_int_rel_dest'] as $indice => $valor) {
                if ($indice == 'fk3_md_pet_int_rel_dest') {
                    $objInfraMetaBD->excluirIndice('md_pet_int_rel_dest', 'fk3_md_pet_int_rel_dest');
                }
            }
        }

        $this->logar('DELETANDO A COLUNA md_pet_int_rel_dest.id_acesso_externo');
        $objInfraMetaBD->excluirColuna('md_pet_int_rel_dest', 'id_acesso_externo');

        $aceiteDTOLista = new MdPetIntAceiteDTO();
        $aceiteRN = new MdPetIntAceiteRN();
        $aceiteDTOLista->retTodos();
        $aceiteDTOLista->setStrTipoAceite(MdPetIntimacaoRN::$TP_AUTOMATICO_POR_DECURSO_DE_PRAZO);

        $this->logar('LISTANDO OS DADOS TACITO DA TABELA md_pet_int_aceite');
        $arrListaAceiteTacito = $aceiteRN->listar($aceiteDTOLista);

        if ($arrListaAceiteTacito) {
            $this->logar('ATUALIZANDO A TABELA md_pet_int_aceite COM DADOS TACITO');
            $objMdPetUsuarioRN = new MdPetIntUsuarioRN();
            $idUsuarioPet = $objMdPetUsuarioRN->getObjUsuarioPeticionamento(true);
            foreach ($arrListaAceiteTacito as $objMdPetIntAceiteDTO) {
                if ($objMdPetIntAceiteDTO->getNumIdMdPetIntAceite() != NULL) {
                    $aceiteDTOLista = new MdPetIntAceiteDTO();
                    $aceiteDTOLista->setNumIdMdPetIntAceite($objMdPetIntAceiteDTO->getNumIdMdPetIntAceite());
                    $aceiteDTOLista->setNumIdUsuario($idUsuarioPet);
                    $aceiteRN->alterar($aceiteDTOLista);
                }
            }
        }

        $aceiteDTOLista = new MdPetIntAceiteDTO();
        $aceiteRN = new MdPetIntAceiteRN();
        $aceiteDTOLista->retTodos(true);
        $aceiteDTOLista->setStrTipoAceite(MdPetIntimacaoRN::$TP_MANUAL_USUARIO_EXTERNO_ACEITE);

        $this->logar('LISTANDO OS DADOS DE USU�RIO EXTERNO DA TABELA md_pet_int_aceite');
        $arrListaAceiteUsuarioExterno = $aceiteRN->listar($aceiteDTOLista);

        if ($arrListaAceiteUsuarioExterno) {
            $this->logar('ATUALIZANDO A TABELA md_pet_int_aceite COM DADOS USU�RIO EXTERNO');
            $objMdPetUsuarioRN = new MdPetIntUsuarioRN();
            foreach ($arrListaAceiteUsuarioExterno as $objMdPetIntAceiteDTO) {
                $aceiteDTOLista = new MdPetIntAceiteDTO();
                $aceiteDTOLista->setNumIdMdPetIntAceite($objMdPetIntAceiteDTO->getNumIdMdPetIntAceite());
                $aceiteDTOLista->setNumIdUsuario($objMdPetIntAceiteDTO->getNumIdUsuarioExterno());
                $aceiteRN->alterar($aceiteDTOLista);
            }
        }

        $this->logar('ADICIONANDO A COLUNA id_usuario NA TABELA md_pet_int_dest_resposta');
        $objInfraMetaBD->adicionarColuna('md_pet_int_dest_resposta', 'id_usuario', $objInfraMetaBD->tipoNumero(), 'NULL');
        $objInfraMetaBD->adicionarChaveEstrangeira('fk3_md_pet_int_dest_resp_usu', 'md_pet_int_dest_resposta', array('id_usuario'), 'usuario', array('id_usuario'));

        $aceiteDTOLista = new MdPetIntAceiteDTO();
        $aceiteRN = new MdPetIntAceiteRN();
        $aceiteDTOLista->retTodos(true);

        $this->logar('LISTANDO OS DADOS DA TABELA md_pet_int_aceite');
        $arrListaAceite = $aceiteRN->listar($aceiteDTOLista);

        if ($arrListaAceite) {
            $this->logar('ATUALIZANDO A COLUNA id_usuario da TABELA md_pet_int_dest_resposta');
            foreach ($arrListaAceite as $objMdPetIntAceiteDTO) {

                $respostaDTOLista = new MdPetIntDestRespostaDTO();
                $respostaRN = new MdPetIntDestRespostaRN();
                $respostaDTOLista->setNumIdMdPetIntRelDestinatario($objMdPetIntAceiteDTO->getNumIdMdPetIntRelDestinatario());
                $respostaDTOLista->retNumIdMdPetIntDestResposta();
                $arrListaResposta = $respostaRN->listar($respostaDTOLista);
                if ($arrListaResposta) {
                    foreach ($arrListaResposta as $objMdPetIntRespostaDTO) {
                        $respostaDTO2Lista = new MdPetIntDestRespostaDTO();
                        $respostaDTO2Lista->setNumIdMdPetIntDestResposta($objMdPetIntRespostaDTO->getNumIdMdPetIntDestResposta());
                        $respostaDTO2Lista->setNumIdUsuario($objMdPetIntAceiteDTO->getNumIdUsuarioExterno());
                        $respostaRN->alterar($respostaDTO2Lista);
                    }
                }
            }
        }

        //INSERCAO DE NOVOS MODELOS DE EMAIL NO MENU E-MAILS DO SISTEMA
        $this->logar('INSERINDO EMAIL MD_PET_INTIMACAO_QUE_EXIGE_RESPOSTA_J NA TABELA email_sistema');

        $textoEmailExigeResposta = '      :: Este � um e-mail autom�tico ::

Prezado(a) @nome_usuario_externo@,

No SEI-@sigla_orgao@ foi expedida Intima��o Eletr�nica referente a "@tipo_intimacao@", no �mbito do processo n� @processo@, conforme documento principal de protocolo n� @documento_principal_intimacao@ (@tipo_documento_principal_intimacao@).

A presente Intima��o foi destinada � Pessoa Jur�dica @razao_social@ (@cnpj@), � qual voc� possui vincula��o na qualidade de @tipo_vinculo@, com poderes de recebimento de Intima��o.

A mencionada Intima��o exige resposta para "@tipo_resposta@", no prazo de @prazo_externo_tipo_resposta@, contados a partir do dia �til seguinte ao da data de cumprimento da presente Intima��o.

Para visualizar o documento principal da Intima��o Eletr�nica e poss�veis anexos, acesse a �rea destinada aos Usu�rios Externos no SEI-@sigla_orgao@ destacada em nosso Portal na Internet ou acesse diretamente o link a seguir: @link_login_usuario_externo@

A resposta � Intima��o Eletr�nica que � exigida deve ser realizada na �rea destinada aos Usu�rios Externos indicada acima. Com o processo aberto, acesse o bot�o de A��o "Responder Intima��o Eletr�nica".

Lembramos que, independentemente de e-mail de alerta, � de responsabilidade exclusiva do Usu�rio Externo a consulta peri�dica ao SEI a fim de verificar o recebimento de Intima��es, considerando-se realizadas na data em que efetuar sua consulta no sistema ou, n�o efetuada a consulta, em @prazo_intimacao_tacita@ dias ap�s a data de sua expedi��o.

Dessa forma, como a presente Intima��o foi expedida em @data_expedicao_intimacao@ e em conformidade com as regras de contagem de prazo dispostas no art. 66 da Lei n� 9.784/1999, mesmo se n�o ocorrer a consulta direta no sistema aos documentos correspondentes, a Intima��o ser� considerada cumprida por decurso do prazo t�cito ao final do dia @data_final_prazo_intimacao_tacita@.


@sigla_orgao@
@descricao_orgao@
@sitio_internet_orgao@

ATEN��O: As informa��es contidas neste e-mail, incluindo seus anexos, podem ser restritas apenas � pessoa ou entidade para a qual foi endere�ada. Se voc� n�o � o destinat�rio ou a pessoa respons�vel por encaminhar esta mensagem ao destinat�rio, voc� est�, por meio desta, notificado que n�o dever� rever, retransmitir, imprimir, copiar, usar ou distribuir esta mensagem ou quaisquer anexos. Caso voc� tenha recebido esta mensagem por engano, por favor, contate o remetente imediatamente e em seguida apague esta mensagem.';

        $maxIdEmailSistemaExigeRespostaPJ = $this->retornarMaxIdEmailSistema();
        $objEmailSistemaDTO = new EmailSistemaDTO();
        $objEmailSistemaRN = new EmailSistemaRN();
        $objEmailSistemaDTO->setStrDescricao('Peticionamento Eletr�nico - Intima��o Eletr�nica que Exige Resposta - Pessoa Jur�dica');
        $objEmailSistemaDTO->setStrDe('@sigla_sistema@ <@email_sistema@>');
        $objEmailSistemaDTO->setStrPara('@email_usuario_externo@');
        $objEmailSistemaDTO->setStrAssunto('SEI - Intima��o Eletr�nica que Exige Resposta no Processo n� @processo@');
        $objEmailSistemaDTO->setStrConteudo($textoEmailExigeResposta);
        $objEmailSistemaDTO->setStrSinAtivo('S');
        $objEmailSistemaDTO->setStrIdEmailSistemaModulo('MD_PET_INTIMACAO_QUE_EXIGE_RESPOSTA_J');
        $objEmailSistemaDTO->setNumIdEmailSistema($maxIdEmailSistemaExigeRespostaPJ);
        $objEmailSistemaRN->cadastrar($objEmailSistemaDTO);

        $this->logar('INSERINDO texto padr�o para e-mail com respostas facultativas para pessoa juridica da tabela email_sistema');
        $textoEmailRespostasFacultativas = '      :: Este � um e-mail autom�tico ::

Prezado(a) @nome_usuario_externo@,

No SEI-@sigla_orgao@ foi expedida Intima��o Eletr�nica referente a "@tipo_intimacao@", no �mbito do processo n� @processo@, conforme documento principal de protocolo n� @documento_principal_intimacao@ (@tipo_documento_principal_intimacao@).

A presente Intima��o foi destinada � Pessoa Jur�dica @razao_social@ (@cnpj@), � qual voc� possui vincula��o na qualidade de @tipo_vinculo@, com poderes de recebimento de Intima��o.

Para visualizar o documento principal da Intima��o Eletr�nica e poss�veis anexos, acesse a �rea destinada aos Usu�rios Externos no SEI-@sigla_orgao@ destacada em nosso Portal na Internet ou acesse diretamente o link a seguir: @link_login_usuario_externo@

Caso tenha interesse, a resposta � Intima��o Eletr�nica deve ser realizada na �rea destinada aos Usu�rios Externos indicada acima. Com o processo aberto, acesse o bot�o de A��o "Responder Intima��o Eletr�nica".

Lembramos que, independentemente de e-mail de alerta, � de responsabilidade exclusiva do Usu�rio Externo a consulta peri�dica ao SEI a fim de verificar o recebimento de Intima��es, considerando-se realizadas na data em que efetuar sua consulta no sistema ou, n�o efetuada a consulta, em @prazo_intimacao_tacita@ dias ap�s a data de sua expedi��o.

Dessa forma, como a presente Intima��o foi expedida em @data_expedicao_intimacao@ e em conformidade com as regras de contagem de prazo dispostas no art. 66 da Lei n� 9.784/1999, mesmo se n�o ocorrer a consulta direta no sistema aos documentos correspondentes, a Intima��o ser� considerada cumprida por decurso do prazo t�cito ao final do dia @data_final_prazo_intimacao_tacita@.


@sigla_orgao@
@descricao_orgao@
@sitio_internet_orgao@

ATEN��O: As informa��es contidas neste e-mail, incluindo seus anexos, podem ser restritas apenas � pessoa ou entidade para a qual foi endere�ada. Se voc� n�o � o destinat�rio ou a pessoa respons�vel por encaminhar esta mensagem ao destinat�rio, voc� est�, por meio desta, notificado que n�o dever� rever, retransmitir, imprimir, copiar, usar ou distribuir esta mensagem ou quaisquer anexos. Caso voc� tenha recebido esta mensagem por engano, por favor, contate o remetente imediatamente e em seguida apague esta mensagem.';

        $maxIdEmailSistemaRespostaFacultativaPJ = $this->retornarMaxIdEmailSistema();
        $objEmailSistemaDTO = new EmailSistemaDTO();
        $objEmailSistemaRN = new EmailSistemaRN();
        $objEmailSistemaDTO->setStrDescricao('Peticionamento Eletr�nico - Intima��o Eletr�nica apenas com Respostas Facultativas - Pessoa Jur�dica');
        $objEmailSistemaDTO->setStrDe('@sigla_sistema@ <@email_sistema@>');
        $objEmailSistemaDTO->setStrPara('@email_usuario_externo@');
        $objEmailSistemaDTO->setStrAssunto('SEI - Intima��o Eletr�nica Gerada no Processo n� @processo@');
        $objEmailSistemaDTO->setStrConteudo($textoEmailRespostasFacultativas);
        $objEmailSistemaDTO->setStrSinAtivo('S');
        $objEmailSistemaDTO->setStrIdEmailSistemaModulo('MD_PET_INTIMACAO_APENAS_RESPOSTAS_FACULTATIVAS_J');
        $objEmailSistemaDTO->setNumIdEmailSistema($maxIdEmailSistemaRespostaFacultativaPJ);
        $objEmailSistemaRN->cadastrar($objEmailSistemaDTO);

        $this->logar('INSERINDO texto padr�o para e-mail reitera��o para pessoa juridica da tabela email_sistema');
        $textoEmailReiteracao = '      :: Este � um e-mail autom�tico ::

Prezado(a) @nome_usuario_externo@,

Reiteramos a necessidade de Resposta � Intima��o Eletr�nica expedida no SEI-@sigla_orgao@ referente a "@tipo_intimacao@", no �mbito do processo n� @processo@, conforme documento principal de protocolo n� @documento_principal_intimacao@ (@tipo_documento_principal_intimacao@).

A mencionada Intima��o exige resposta para "@tipo_resposta@", no prazo de @prazo_externo_tipo_resposta@, contados a partir do dia �til seguinte ao da data de cumprimento da Intima��o, que ocorreu em @data_cumprimento_intimacao@.

Para visualizar o documento principal da Intima��o Eletr�nica e poss�veis anexos, acesse a �rea destinada aos Usu�rios Externos no SEI-@sigla_orgao@ destacada em nosso Portal na Internet ou acesse diretamente o link a seguir: @link_login_usuario_externo@

A resposta � Intima��o Eletr�nica que � exigida deve ser realizada na �rea destinada aos Usu�rios Externos indicada acima. Com o processo aberto, acesse o bot�o de A��o "Responder Intima��o Eletr�nica".

OBSERVA��O: A presente reitera��o ocorre quando a resposta ainda n�o tenha sido efetivada pelo Destinat�rio da Intima��o, em 5 dias e 1 dia antes da Data Limite para Resposta. Caso a Intima��o j� tenha sido respondida, por favor, ignorar esta reitera��o.


@sigla_orgao@
@descricao_orgao@
@sitio_internet_orgao@

ATEN��O: As informa��es contidas neste e-mail, incluindo seus anexos, podem ser restritas apenas � pessoa ou entidade para a qual foi endere�ada. Se voc� n�o � o destinat�rio ou a pessoa respons�vel por encaminhar esta mensagem ao destinat�rio, voc� est�, por meio desta, notificado que n�o dever� rever, retransmitir, imprimir, copiar, usar ou distribuir esta mensagem ou quaisquer anexos. Caso voc� tenha recebido esta mensagem por engano, por favor, contate o remetente imediatamente e em seguida apague esta mensagem.';

        $maxIdEmailSistemaReiteracaoExigeRespostaPJ = $this->retornarMaxIdEmailSistema();
        $objEmailSistemaDTO->setStrDescricao('Peticionamento Eletr�nico - Reitera��o de Intima��o Eletr�nica que Exige Resposta - Pessoa Jur�dica');
        $objEmailSistemaDTO->setStrDe('@sigla_sistema@ <@email_sistema@>');
        $objEmailSistemaDTO->setStrPara('@email_usuario_externo@');
        $objEmailSistemaDTO->setStrAssunto('SEI - Reitera��o de Intima��o Eletr�nica que Exige Resposta no Processo n� @processo@');
        $objEmailSistemaDTO->setStrConteudo($textoEmailReiteracao);
        $objEmailSistemaDTO->setStrSinAtivo('S');
        $objEmailSistemaDTO->setStrIdEmailSistemaModulo('MD_PET_REITERACAO_INTIMACAO_QUE_EXIGE_RESPOSTA_J');
        $objEmailSistemaDTO->setNumIdEmailSistema($maxIdEmailSistemaReiteracaoExigeRespostaPJ);
        $objEmailSistemaRN->cadastrar($objEmailSistemaDTO);

        $this->logar('INSERINDO texto padr�o para e-mail sem resposta para pessoa juridica da tabela email_sistema');
        $textoEmailSemResposta = '      :: Este � um e-mail autom�tico ::

Prezado(a) @nome_usuario_externo@,

No SEI-@sigla_orgao@ foi expedida Intima��o Eletr�nica referente a "@tipo_intimacao@", no �mbito do processo n� @processo@, conforme documento principal de protocolo n� @documento_principal_intimacao@ (@tipo_documento_principal_intimacao@).

A presente Intima��o foi destinada � Pessoa Jur�dica @razao_social@ (@cnpj@), � qual voc� possui vincula��o na qualidade de @tipo_vinculo@, com poderes de recebimento de Intima��o.

OBSERVA��O: A presente intima��o n�o demanda qualquer tipo de resposta, por geralmente encaminhar documento para mero conhecimento, o que n�o dispensa a necessidade de acesso aos documentos para ci�ncia de seu teor. Ap�s o cumprimento da intima��o, observar que neste caso n�o ser� disponibilizada a funcionalidade para Peticionamento de Resposta a Intima��o Eletr�nica, sem que isso impe�a o uso do Peticionamento Intercorrente, caso ainda seja necess�rio protocolizar documento no processo acima indicado.

Para visualizar o documento principal da Intima��o Eletr�nica e poss�veis anexos, acesse a �rea destinada aos Usu�rios Externos no SEI-@sigla_orgao@ destacada em nosso Portal na Internet ou acesse diretamente o link a seguir: @link_login_usuario_externo@

Lembramos que, independentemente de e-mail de alerta, � de responsabilidade exclusiva do Usu�rio Externo a consulta peri�dica ao SEI a fim de verificar o recebimento de Intima��es, considerando-se realizadas na data em que efetuar sua consulta no sistema ou, n�o efetuada a consulta, em @prazo_intimacao_tacita@ dias ap�s a data de sua expedi��o.

Dessa forma, como a presente Intima��o foi expedida em @data_expedicao_intimacao@ e em conformidade com as regras de contagem de prazo dispostas no art. 66 da Lei n� 9.784/1999, mesmo se n�o ocorrer a consulta direta no sistema aos documentos correspondentes, a Intima��o ser� considerada cumprida por decurso do prazo t�cito ao final do dia @data_final_prazo_intimacao_tacita@.


@sigla_orgao@
@descricao_orgao@
@sitio_internet_orgao@

ATEN��O: As informa��es contidas neste e-mail, incluindo seus anexos, podem ser restritas apenas � pessoa ou entidade para a qual foi endere�ada. Se voc� n�o � o destinat�rio ou a pessoa respons�vel por encaminhar esta mensagem ao destinat�rio, voc� est�, por meio desta, notificado que n�o dever� rever, retransmitir, imprimir, copiar, usar ou distribuir esta mensagem ou quaisquer anexos. Caso voc� tenha recebido esta mensagem por engano, por favor, contate o remetente imediatamente e em seguida apague esta mensagem.';

        $maxIdEmailSistemaSemRespostaPJ = $this->retornarMaxIdEmailSistema();
        $objEmailSistemaDTO = new EmailSistemaDTO();
        $objEmailSistemaRN = new EmailSistemaRN();
        $objEmailSistemaDTO->setStrDescricao('Peticionamento Eletr�nico - Intima��o Eletr�nica Sem Resposta - Pessoa Jur�dica');
        $objEmailSistemaDTO->setStrDe('@sigla_sistema@ <@email_sistema@>');
        $objEmailSistemaDTO->setStrPara('@email_usuario_externo@');
        $objEmailSistemaDTO->setStrAssunto('SEI - Intima��o Eletr�nica Gerada no Processo n� @processo@');
        $objEmailSistemaDTO->setStrConteudo($textoEmailSemResposta);
        $objEmailSistemaDTO->setStrSinAtivo('S');
        $objEmailSistemaDTO->setStrIdEmailSistemaModulo('MD_PET_INTIMACAO_SEM_RESPOSTA_J');
        $objEmailSistemaDTO->setNumIdEmailSistema($maxIdEmailSistemaSemRespostaPJ);
        $objEmailSistemaRN->cadastrar($objEmailSistemaDTO);

        //INSERCAO DE NOVOS MODELOS DE EMAIL NO MENU E-MAILS DO SISTEMA
        $this->logar('INSERINDO EMAIL MD_PET_VINC_SUSPENSAO NA TABELA email_sistema');

        $conteudoVinculoSuspensao = "      :: Este � um e-mail autom�tico ::

Prezado(a) @nome_usuario_externo@,

A Administra��o do SEI-@sigla_orgao@ suspendeu sua vincula��o como Respons�vel Legal da Pessoa Jur�dica @razao_social@ (@cnpj@), conforme instrumento de Suspens�o de Vincula��o a Pessoa Jur�dica SEI n� @documento_suspensao_responsavel_pj@.

Comunicamos que:
- A suspens�o da sua vincula��o como Respons�vel Legal da Pessoa Jur�dica n�o impede o peticionamento em nome pr�prio;
- Poder� realizar Peticionamento Intercorrente no processo citado no assunto deste e-mail para comprovar seus poderes de representa��o como Respons�vel Legal da Pessoa Jur�dica;
- As Procura��es Eletr�nicas concedidas para representa��o da Pessoa Jur�dica restam igualmente suspensas at� que seja restabelecida a vincula��o como Respons�vel Legal.


@sigla_orgao@
@descricao_orgao@
@sitio_internet_orgao@

ATEN��O: As informa��es contidas neste e-mail, incluindo seus anexos, podem ser restritas apenas � pessoa ou entidade para a qual foi endere�ada. Se voc� n�o � o destinat�rio ou a pessoa respons�vel por encaminhar esta mensagem ao destinat�rio, voc� est�, por meio desta, notificado que n�o dever� rever, retransmitir, imprimir, copiar, usar ou distribuir esta mensagem ou quaisquer anexos. Caso voc� tenha recebido esta mensagem por engano, por favor, contate o remetente imediatamente e em seguida apague esta mensagem.";

        $maxIdEmailSistemaVinculoSuspensao = $this->retornarMaxIdEmailSistema();

        $insertVinculoSuspensao = "INSERT INTO email_sistema
            (id_email_sistema,
            descricao,
            de,
            para,
            assunto,
            conteudo,
            sin_ativo,
            id_email_sistema_modulo
             )
        VALUES
            (" . $maxIdEmailSistemaVinculoSuspensao . ",
            'Peticionamento Eletr�nico - Suspens�o de Vincula��o a Pessoa Jur�dica',
            '@sigla_sistema@ <@email_sistema@>',
            '@email_usuario_externo@',
            'SEI - Suspens�o de Vincula��o a Pessoa Jur�dica no Processo n� @processo@',
            '" . $conteudoVinculoSuspensao . "',
            'S',
            'MD_PET_VINC_SUSPENSAO'
            )";

        BancoSEI::getInstance()->executarSql($insertVinculoSuspensao);

        //INSERCAO DE NOVOS MODELOS DE EMAIL NO MENU E-MAILS DO SISTEMA
        $this->logar('INSERINDO EMAIL MD_PET_VINC_RESTABELECIMENTO NA TABELA email_sistema');

        $conteudoVinculoRestabelecimento = "      :: Este � um e-mail autom�tico ::

Prezado(a) @nome_usuario_externo@,

A Administra��o do SEI-@sigla_orgao@ restabeleceu sua vincula��o como Respons�vel Legal da Pessoa Jur�dica @razao_social@ (@cnpj@), conforme instrumento de Restabelecimento de Vincula��o a Pessoa Jur�dica SEI n� @documento_restabelecimento_responsavel_pj@.

Comunicamos que:
- Fica restabelecido seu direito de peticionar e emitir Procura��es Eletr�nicas em nome da Pessoa Jur�dica, bem como realizar altera��es de seus dados cadastrais e atos constitutivos;
- As Procura��es Eletr�nicas concedidas que tenham sido suspensas restam igualmente restabelecidas.


@sigla_orgao@
@descricao_orgao@
@sitio_internet_orgao@

ATEN��O: As informa��es contidas neste e-mail, incluindo seus anexos, podem ser restritas apenas � pessoa ou entidade para a qual foi endere�ada. Se voc� n�o � o destinat�rio ou a pessoa respons�vel por encaminhar esta mensagem ao destinat�rio, voc� est�, por meio desta, notificado que n�o dever� rever, retransmitir, imprimir, copiar, usar ou distribuir esta mensagem ou quaisquer anexos. Caso voc� tenha recebido esta mensagem por engano, por favor, contate o remetente imediatamente e em seguida apague esta mensagem.";

        $maxIdEmailSistemaVinculoRestabelecimento = $this->retornarMaxIdEmailSistema();

        $insertVinculoRestabelecimento = "INSERT INTO email_sistema
            (id_email_sistema,
            descricao,
            de,
            para,
            assunto,
            conteudo,
            sin_ativo,
            id_email_sistema_modulo
             )
        VALUES
            (" . $maxIdEmailSistemaVinculoRestabelecimento . ",
            'Peticionamento Eletr�nico - Restabelecimento de Vincula��o a Pessoa Jur�dica',
            '@sigla_sistema@ <@email_sistema@>',
            '@email_usuario_externo@',
            'SEI - Restabelecimento de Vincula��o a Pessoa Jur�dica no Processo n� @processo@',
            '" . $conteudoVinculoRestabelecimento . "',
            'S',
            'MD_PET_VINC_RESTABELECIMENTO'
            )";

        BancoSEI::getInstance()->executarSql($insertVinculoRestabelecimento);

        //Atualizando parametro para controlar versao do modulo
        $this->logar('ATUALIZANDO PARAMETRO ' . $this->nomeParametroModulo . ' NA TABELA infra_parametro PARA CONTROLAR A VERS�O DO M�DULO');
        BancoSEI::getInstance()->executarSql('UPDATE infra_parametro SET valor = \'3.0.0\' WHERE nome = \'' . $this->nomeParametroModulo . '\' ');

        $this->logar('INSTALA��O/ATUALIZA��O DA VERS�O ' . $this->versaoAtualDesteModulo . ' DO ' . $this->nomeDesteModulo . ' REALIZADA COM SUCESSO NA BASE DO SEI');
    }

    protected function instalarv301()
    {
        $this->logar('EXECUTANDO A INSTALA��O/ATUALIZA��O DA VERS�O 3.0.1 DO ' . $this->nomeDesteModulo . ' NA BASE DO SEI');

        $this->logar('ATUALIZANDO PAR�METRO ' . $this->nomeParametroModulo . ' NA TABELA infra_parametro PARA CONTROLAR A VERS�O DO M�DULO');
        BancoSEI::getInstance()->executarSql('UPDATE infra_parametro SET valor = \'3.0.1\' WHERE nome = \'' . $this->nomeParametroModulo . '\' ');

        $this->logar('INSTALA��O/ATUALIZA��O DA VERS�O ' . $this->versaoAtualDesteModulo . ' DO ' . $this->nomeDesteModulo . ' REALIZADA COM SUCESSO NA BASE DO SEI');
    }

    protected function instalarv310()
    {
        $this->logar('EXECUTANDO A INSTALACAO DA VERSAO 3.1.0 DO MODULO ' . $this->nomeDesteModulo . ' NA BASE DO SEI');
        $this->logar('CRIANDO DOCUMENTO/MODELO DE PROCURA��O ELETR�NICA SIMPLES');
        $this->_gerarModeloProcuracaoEletronicaSimples();
        $objInfraMetaBD = new InfraMetaBD(BancoSEI::getInstance());

        $this->logar('ADICIONANDO COLUNAS NA TABELA md_pet_vinculo_representant ');
        $objInfraMetaBD->adicionarColuna('md_pet_vinculo_represent', 'sta_abrangencia', $objInfraMetaBD->tipoTextoFixo(1), 'NULL');
        $objInfraMetaBD->adicionarColuna('md_pet_vinculo_represent', 'data_limite', $objInfraMetaBD->tipoDataHora(), 'NULL');

        $this->logar('ADICIONANDO COLUNAS NA TABELA md_pet_adm_vinc_tp_proced ');
        $objInfraMetaBD->adicionarColuna('md_pet_adm_vinc_tp_proced', 'especificacao', $objInfraMetaBD->tipoTextoVariavel(100), 'NULL');
        $objInfraMetaBD->adicionarColuna('md_pet_adm_vinc_tp_proced', 'tipo_vinculo', $objInfraMetaBD->tipoTextoFixo(1), 'NULL');

        $this->logar('MODIFICANDO COLUNAS NA TABELA md_pet_adm_vinc_tp_proced ');
        $objInfraMetaBD->alterarColuna('md_pet_adm_vinc_tp_proced', 'sin_na_usuario_externo', $objInfraMetaBD->tipoTextoFixo(1), 'NULL');
        $objInfraMetaBD->alterarColuna('md_pet_adm_vinc_tp_proced', 'sin_na_padrao', $objInfraMetaBD->tipoTextoFixo(1), 'NULL');
        $objInfraMetaBD->alterarColuna('md_pet_adm_vinc_tp_proced', 'sin_ativo', $objInfraMetaBD->tipoTextoFixo(1), 'NULL');

        $this->logar('CRIANDO A TABELA md_pet_adm_tipo_poder');

        $sql_tabelas = 'CREATE TABLE md_pet_adm_tipo_poder (
            id_md_pet_tipo_poder ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL ,
            nome ' . $objInfraMetaBD->tipoTextoVariavel(255) . ' NOT NULL,
            sin_ativo ' . $objInfraMetaBD->tipoTextoFixo(1) . ',
            data_cadastro ' . $objInfraMetaBD->tipoDataHora() . ',
            sta_sistema ' . $objInfraMetaBD->tipoTextoFixo(1) . ' NULL
            )';

        BancoSEI::getInstance()->executarSql($sql_tabelas);
        $objInfraMetaBD->adicionarChavePrimaria('md_pet_adm_tipo_poder', 'pk_md_pet_adm_tipo_poder', array('id_md_pet_tipo_poder'));

        $this->logar('CRIANDO A SEQUENCE seq_md_pet_adm_tipo_poder');
        BancoSEI::getInstance()->criarSequencialNativa('seq_md_pet_adm_tipo_poder', 1);

        //Adicionando Registros
        $this->logar('Adicionando Registro Padr�o na TABELA md_pet_adm_tipo_poder');
        $objMdPetTipoPoderLegalDTO = new MdPetTipoPoderLegalDTO();
        $objMdPetTipoPoderLegalDTO->setStrNome("Receber, Cumprir e Responder Intima��o Eletr�nica");
        $objMdPetTipoPoderLegalDTO->setDtaDtaCadastro(InfraData::getStrDataHoraAtual());
        $objMdPetTipoPoderLegalDTO->setStrStaSistema("I");
        $objMdPetTipoPoderLegalDTO->setStrSinAtivo('S');
        $objMdPetTipoPoderLegalRN = new MdPetTipoPoderLegalRN();
        $arrObjMdPetTipoPoderLegalDTO = $objMdPetTipoPoderLegalRN->cadastrar($objMdPetTipoPoderLegalDTO);


        $objMdPetVincTpProcessoDTO = new MdPetVincTpProcessoDTO();
        $objMdPetVincTpProcessoDTO->retNumIdTipoProcedimento();
        $objMdPetVincTpProcessoDTO->setNumIdMdPetVincTpProcesso(1);
        $objMdPetVincTpProcessoRN = new MdPetVincTpProcessoRN();
        $objMdPetVincTpProcessoRN = $objMdPetVincTpProcessoRN->listar($objMdPetVincTpProcessoDTO);
        if (count($objMdPetVincTpProcessoRN)) {
            $this->logar('Atualizando Registro da Pessoa Jur�dica na TABELA md_pet_adm_vinc_tp_proced');
            $objMdPetVincTpProcessoDTO = new MdPetVincTpProcessoDTO();
            $objMdPetVincTpProcessoDTO->setNumIdMdPetVincTpProcesso(1);
            $objMdPetVincTpProcessoDTO->setStrTipoVinculo("J");
            $objMdPetVincTpProcessoDTO->setStrEspecificacao("@cnpj@ - @razao_social@");
            $objMdPetVincTpProcessoRN = new MdPetVincTpProcessoRN();
            $objMdPetVincTpProcessoRN = $objMdPetVincTpProcessoRN->alterar($objMdPetVincTpProcessoDTO);
        }

        $this->logar('CRIANDO A TABELA md_pet_rel_vincrep_tipo_poder');
        $sql_tabelas = 'CREATE TABLE md_pet_rel_vincrep_tipo_poder (
            id_md_pet_tipo_poder ' . $objInfraMetaBD->tipoNumero(11) . ' NOT NULL ,
            id_md_pet_vinculo_represent ' . $objInfraMetaBD->tipoNumero(11) . ' NOT NULL
            )';

        BancoSEI::getInstance()->executarSql($sql_tabelas);
        //Montando REl com Tabela Tipo de Poder
        $objInfraMetaBD->adicionarChaveEstrangeira('FK_Reference_211', 'md_pet_rel_vincrep_tipo_poder', array('id_md_pet_tipo_poder'), 'md_pet_adm_tipo_poder', array('id_md_pet_tipo_poder'));
        //Montando REl com Tabela Vinculo Representante
        $objInfraMetaBD->adicionarChaveEstrangeira('FK_Reference_212', 'md_pet_rel_vincrep_tipo_poder', array('id_md_pet_vinculo_represent'), 'md_pet_vinculo_represent', array('id_md_pet_vinculo_represent'));

        //Tabela de Protocolo
        $this->logar('CRIANDO A TABELA md_pet_rel_vincrep_protoc');

        $sql_tabelas = 'CREATE TABLE md_pet_rel_vincrep_protoc (
            id_md_pet_vinculo_represent ' . $objInfraMetaBD->tipoNumero(11) . ' NOT NULL ,
            id_protocolo ' . $objInfraMetaBD->tipoNumeroGrande(20) . ' NOT NULL
            )';

        BancoSEI::getInstance()->executarSql($sql_tabelas);

        //Montando REl com Tabela Vinculo Presentante
        $objInfraMetaBD->adicionarChaveEstrangeira('FK_Reference_213', 'md_pet_rel_vincrep_protoc', array('id_md_pet_vinculo_represent'), 'md_pet_vinculo_represent', array('id_md_pet_vinculo_represent'));
        //Montando REl com Tabela Protocolo
        $objInfraMetaBD->adicionarChaveEstrangeira('FK_Reference_214', 'md_pet_rel_vincrep_protoc', array('id_protocolo'), 'protocolo', array('id_protocolo'));

        $this->logar('CRIA��O DOS AGENDAMENTOS AUTOM�TICOS PARA ALTERAR O STATUS DA PROCURA��O ELETR�NICA SIMPLES PARA VENCIDA');

        $infraAgendamentoDTO = new InfraAgendamentoTarefaDTO();
        $infraAgendamentoDTO->retTodos();
        $infraAgendamentoDTO->setStrDescricao('Script para altera��o da situa��o de Procura��o Simples com Data Limite Vencida.');

        $infraAgendamentoDTO->setStrComando('MdPetAgendamentoAutomaticoRN::AtualizarSituacaoProcuracaoSimplesVencida');

        $infraAgendamentoDTO->setStrSinAtivo('S');
        $infraAgendamentoDTO->setStrStaPeriodicidadeExecucao(InfraAgendamentoTarefaRN::$PERIODICIDADE_EXECUCAO_HORA);
        $infraAgendamentoDTO->setStrPeriodicidadeComplemento(0);
        $infraAgendamentoDTO->setStrParametro(null);
        $infraAgendamentoDTO->setDthUltimaExecucao(null);
        $infraAgendamentoDTO->setDthUltimaConclusao(null);
        $infraAgendamentoDTO->setStrSinSucesso('S');
        $infraAgendamentoDTO->setStrEmailErro(null);

        $infraAgendamentoRN = new InfraAgendamentoTarefaRN();
        $infraAgendamentoDTO = $infraAgendamentoRN->cadastrar($infraAgendamentoDTO);

        $this->logar('ADICIONANDO COLUNAS NA TABELA md_pet_adm_integracao ');
        $objInfraMetaBD->adicionarColuna('md_pet_adm_integracao', 'sta_tp_cliente_ws', $objInfraMetaBD->tipoTextoFixo(1), 'NULL');
        $objInfraMetaBD->adicionarColuna('md_pet_adm_integracao', 'nu_versao', $objInfraMetaBD->tipoNumeroDecimal(2, 1), 'NULL');
        $objInfraMetaBD->adicionarColuna('md_pet_adm_integracao', 'sin_tp_lougradouro', $objInfraMetaBD->tipoTextoFixo(1), 'NULL');
        $objInfraMetaBD->adicionarColuna('md_pet_adm_integracao', 'sin_nu_lougradouro', $objInfraMetaBD->tipoTextoFixo(1), 'NULL');
        $objInfraMetaBD->adicionarColuna('md_pet_adm_integracao', 'sin_comp_lougradouro', $objInfraMetaBD->tipoTextoFixo(1), 'NULL');

        $this->logar('ATUALIZANDO PAR�METRO ' . $this->nomeParametroModulo . ' NA TABELA infra_parametro PARA CONTROLAR A VERS�O DO M�DULO');
        BancoSEI::getInstance()->executarSql('UPDATE infra_parametro SET valor = \'3.1.0\' WHERE nome = \'' . $this->nomeParametroModulo . '\' ');

        $this->logar('INSTALA��O/ATUALIZA��O DA VERS�O ' . $this->versaoAtualDesteModulo . ' DO ' . $this->nomeDesteModulo . ' REALIZADA COM SUCESSO NA BASE DO SEI');

    }

    protected function instalarv320()
    {
        $this->logar('EXECUTANDO A INSTALA��O/ATUALIZA��O DA VERS�O 3.2.0 DO ' . $this->nomeDesteModulo . ' NA BASE DO SEI');

        $objInfraMetaBD = new InfraMetaBD(BancoSEI::getInstance());
        $objInfraMetaBD->setBolValidarIdentificador(true);

        $arrTabelas = array('md_pet_acesso_externo', 'md_pet_criterio', 'md_pet_ext_arquivo_perm', 'md_pet_hipotese_legal', 'md_pet_indisp_doc', 'md_pet_indisponibilidade', 'md_pet_int_aceite', 'md_pet_int_dest_resposta', 'md_pet_int_prazo_tacita', 'md_pet_int_prot_disponivel', 'md_pet_int_protocolo', 'md_pet_int_rel_dest', 'md_pet_int_rel_intim_resp', 'md_pet_int_rel_resp_doc', 'md_pet_int_rel_tipo_resp', 'md_pet_int_rel_tpo_res_des', 'md_pet_int_serie', 'md_pet_int_tipo_intimacao', 'md_pet_int_tipo_resp', 'md_pet_intimacao', 'md_pet_rel_recibo_docanexo', 'md_pet_rel_recibo_protoc', 'md_pet_rel_tp_ctx_contato', 'md_pet_rel_tp_proc_serie', 'md_pet_rel_tp_processo_unid', 'md_pet_tamanho_arquivo', 'md_pet_tipo_processo', 'md_pet_tp_processo_orientacoes', 'md_pet_usu_externo_menu',
            //Lista de 13 tabelas que faltou processar o indice na versao 3.0.1
            'md_pet_adm_integ_funcion', 'md_pet_adm_integ_param', 'md_pet_adm_integracao', 'md_pet_adm_tipo_poder', 'md_pet_adm_vinc_rel_serie', 'md_pet_adm_vinc_tp_proced', 'md_pet_int_tp_int_orient', 'md_pet_rel_int_dest_extern', 'md_pet_rel_vincrep_protoc', 'md_pet_rel_vincrep_tipo_poder', 'md_pet_vinculo', 'md_pet_vinculo_documento', 'md_pet_vinculo_represent');

        $this->fixIndices($objInfraMetaBD, $arrTabelas);

        $this->logar('ATUALIZANDO PAR�METRO ' . $this->nomeParametroModulo . ' NA TABELA infra_parametro PARA CONTROLAR A VERS�O DO M�DULO');
        BancoSEI::getInstance()->executarSql('UPDATE infra_parametro SET valor = \'3.2.0\' WHERE nome = \'' . $this->nomeParametroModulo . '\' ');

        $this->logar('INSTALA��O/ATUALIZA��O DA VERS�O ' . $this->versaoAtualDesteModulo . ' DO ' . $this->nomeDesteModulo . ' REALIZADA COM SUCESSO NA BASE DO SEI');

    }

    protected function instalarv330()
    {
        $this->logar('EXECUTANDO A INSTALA��O/ATUALIZA��O DA VERS�O 3.3.0 DO ' . $this->nomeDesteModulo . ' NA BASE DO SEI');

        $objInfraMetaBD = new InfraMetaBD(BancoSEI::getInstance());
        $objInfraMetaBD->setBolValidarIdentificador(true);

        $this->logar('ALTERANDO TAMANHO DA COLUNA nome_campo NA TABELA md_pet_adm_integ_param');
        $objInfraMetaBD->alterarColuna('md_pet_adm_integ_param', 'nome_campo', $objInfraMetaBD->tipoTextoVariavel(500), 'NULL');

        $this->logar('ALTERANDO O NOME DO TIPO DO DOCUMENTO DA S�RIE MODULO_PETICIONAMENTO_ID_SERIE_PROCURACAO_ELETRONICA_SIMPLES');
        $idSerie = BancoSEI::getInstance()->consultarSql('SELECT valor FROM infra_parametro WHERE nome  = \'' . MdPetAtualizadorSeiRN::$MD_PET_ID_SERIE_PROCURACAOS . '\' ');
        BancoSEI::getInstance()->executarSql('UPDATE serie SET nome = \'Procura��o Eletr�nica Simples\' WHERE id_serie = \'' . $idSerie[0]['valor'] . '\' ');

        $this->logar('ATUALIZANDO PAR�METRO ' . $this->nomeParametroModulo . ' NA TABELA infra_parametro PARA CONTROLAR A VERS�O DO M�DULO');
        BancoSEI::getInstance()->executarSql('UPDATE infra_parametro SET valor = \'3.3.0\' WHERE nome = \'' . $this->nomeParametroModulo . '\' ');

        $this->logar('INSTALA��O/ATUALIZA��O DA VERS�O ' . $this->versaoAtualDesteModulo . ' DO ' . $this->nomeDesteModulo . ' REALIZADA COM SUCESSO NA BASE DO SEI');

    }

    protected function instalarv340()
    {
        $this->logar('EXECUTANDO A INSTALA��O/ATUALIZA��O DA VERS�O 3.4.0 DO ' . $this->nomeDesteModulo . ' NA BASE DO SEI');

        $this->logar('ATUALIZANDO PAR�METRO ' . $this->nomeParametroModulo . ' NA TABELA infra_parametro PARA CONTROLAR A VERS�O DO M�DULO');
        BancoSEI::getInstance()->executarSql('UPDATE infra_parametro SET valor = \'3.4.0\' WHERE nome = \'' . $this->nomeParametroModulo . '\' ');

        $this->logar('INSTALA��O/ATUALIZA��O DA VERS�O ' . $this->versaoAtualDesteModulo . ' DO ' . $this->nomeDesteModulo . ' REALIZADA COM SUCESSO NA BASE DO SEI');
    }

    protected function instalarv341()
    {
        $this->logar('EXECUTANDO A INSTALA��O/ATUALIZA��O DA VERS�O 3.4.1 DO ' . $this->nomeDesteModulo . ' NA BASE DO SEI');

        $this->logar('ATUALIZANDO PAR�METRO ' . $this->nomeParametroModulo . ' NA TABELA infra_parametro PARA CONTROLAR A VERS�O DO M�DULO');
        BancoSEI::getInstance()->executarSql('UPDATE infra_parametro SET valor = \'3.4.1\' WHERE nome = \'' . $this->nomeParametroModulo . '\' ');

        $this->logar('INSTALA��O/ATUALIZA��O DA VERS�O ' . $this->versaoAtualDesteModulo . ' DO ' . $this->nomeDesteModulo . ' REALIZADA COM SUCESSO NA BASE DO SEI');
    }

    protected function instalarv342()
    {
        $this->logar('EXECUTANDO A INSTALA��O/ATUALIZA��O DA VERS�O 3.4.2 DO ' . $this->nomeDesteModulo . ' NA BASE DO SEI');

        $this->logar('ATUALIZANDO PAR�METRO ' . $this->nomeParametroModulo . ' NA TABELA infra_parametro PARA CONTROLAR A VERS�O DO M�DULO');
        BancoSEI::getInstance()->executarSql('UPDATE infra_parametro SET valor = \'3.4.2\' WHERE nome = \'' . $this->nomeParametroModulo . '\' ');

        $this->logar('INSTALA��O/ATUALIZA��O DA VERS�O ' . $this->versaoAtualDesteModulo . ' DO ' . $this->nomeDesteModulo . ' REALIZADA COM SUCESSO NA BASE DO SEI');
    }

    private function existeIdEmailSistemaPecitionamento()
    {
        $this->logar('VERIFICANDO A EXISTENCIA DE MODELOS DE EMAIL PARA PETICIONAMENTO');
        $sql = "select 
				id_email_sistema 
				from email_sistema 
				where 
					id_email_sistema in (3001,3002)";
        $rs = BancoSEI::getInstance()->consultarSql($sql);
        return (count($rs) > 0) ? true : false;
    }

    private function atualizarIdEmailSistemaAlertaPecitionamento()
    {
        $this->logar('ATUALIZANDO O IDENTIFICADOR DO MODELO DE EMAIL PARA PETICIONAMENTO DA CONSTANTE MD_PET_ALERTA_PETICIONAMENTO_UNIDADES');
        $idEmailSistema = $this->retornarMaxIdEmailSistema();
        BancoSEI::getInstance()->executarSql('update email_sistema SET id_email_sistema = ' . $idEmailSistema . ', id_email_sistema_modulo = \'MD_PET_ALERTA_PETICIONAMENTO_UNIDADES\' WHERE id_email_sistema = 3002');
    }

    private function atualizarIdEmailSistemaConfirmacaoPeticionamento()
    {
        $this->logar('ATUALIZANDO O IDENTIFICADOR DO MODELO DE EMAIL PARA PETICIONAMENTO DA CONSTANTE MD_PET_CONFIRMACAO_PETICIONAMENTO_USUARIO_EXTERNO');
        $idEmailSistema = $this->retornarMaxIdEmailSistema();
        BancoSEI::getInstance()->executarSql('update email_sistema SET id_email_sistema = ' . $idEmailSistema . ', id_email_sistema_modulo = \'MD_PET_CONFIRMACAO_PETICIONAMENTO_USUARIO_EXTERNO\' WHERE  id_email_sistema = 3001');
    }

    private function retornarMaxIdEmailSistema()
    {
        $this->logar('BUSCANDO O PROXIMO ID DISPONIVEL NA TABELA EMAIL_SISTEMA');
        $arrMaxIdEmailSistemaSelect = BancoSEI::getInstance()->consultarSql('SELECT MAX(id_email_sistema) as max FROM email_sistema');
        $numMaxIdEmailSistemaSelect = $arrMaxIdEmailSistemaSelect[0]['max'];

        if ($numMaxIdEmailSistemaSelect >= 1000) {
            $this->$numMaxIdEmailSistemaSelect = $numMaxIdEmailSistemaSelect + 1;
        } else {
            $this->$numMaxIdEmailSistemaSelect = 1000;
        }
        return $this->$numMaxIdEmailSistemaSelect;
    }

    private function _gerarModeloFormularioVinculo()
    {

        $this->logar('CRIANDO MODELO "Modulo_Peticionamento_Vinc_Formulario"');
        $modeloRN = new ModeloRN();
        $modeloDTO = new ModeloDTO();
        $modeloDTO->setNumIdModelo(null);
        $modeloDTO->retTodos();
        $modeloDTO->setStrNome('Modulo_Peticionamento_Vinc_Formulario');
        $modeloDTO->setStrSinAtivo('S');
        $modeloDTO = $modeloRN->cadastrar($modeloDTO);

        //adicionando as se��es do modelo: Cabe�alho
        $this->logar('CRIANDO SE�AO DO MODELO - Cabe�alho');
        $secaoModeloRN = new SecaoModeloRN();

        $secaoModeloCabecalhoTextoDTO = new SecaoModeloDTO();
        $secaoModeloCabecalhoTextoDTO->retTodos();
        $secaoModeloCabecalhoTextoDTO->setNumIdSecaoModelo(null);
        $secaoModeloCabecalhoTextoDTO->setNumIdModelo($modeloDTO->getNumIdModelo());
        $secaoModeloCabecalhoTextoDTO->setStrNome('Cabe�alho');

        $htmlCabecalho = '<div align="center">@timbre_orgao@</div>

<p style="font-weight:bold; font-size:13pt; font-family:Calibri; text-align:center; text-transform:uppercase; word-wrap:normal">@descricao_orgao_maiusculas@</p>

<p>&nbsp;&nbsp;</p>';

        $secaoModeloCabecalhoTextoDTO->setStrConteudo($htmlCabecalho);
        $secaoModeloCabecalhoTextoDTO->setNumOrdem(0);
        $secaoModeloCabecalhoTextoDTO->setStrSinCabecalho('S');
        $secaoModeloCabecalhoTextoDTO->setStrSinRodape('N');
        $secaoModeloCabecalhoTextoDTO->setStrSinPrincipal('N');
        $secaoModeloCabecalhoTextoDTO->setStrSinAssinatura('N');
        $secaoModeloCabecalhoTextoDTO->setStrSinSomenteLeitura('S');
        $secaoModeloCabecalhoTextoDTO->setStrSinDinamica('S');
        $secaoModeloCabecalhoTextoDTO->setStrSinHtml('S');
        $secaoModeloCabecalhoTextoDTO->setStrSinAtivo('S');

        $secaoModeloCabecalhoTextoDTO = $secaoModeloRN->cadastrar($secaoModeloCabecalhoTextoDTO);

        //adicionando as se��es do modelo: T�tulo do Documento
        $this->logar('CRIANDO SE�AO DO MODELO - T�tulo do Documento');
        $secaoModeloRN = new SecaoModeloRN();

        $secaoModeloTituloTextoDTO = new SecaoModeloDTO();
        $secaoModeloTituloTextoDTO->retTodos();
        $secaoModeloTituloTextoDTO->setNumIdSecaoModelo(null);
        $secaoModeloTituloTextoDTO->setNumIdModelo($modeloDTO->getNumIdModelo());
        $secaoModeloTituloTextoDTO->setStrNome('T�tulo do Documento');

        $htmlTitulo = '<p style="font-size:13pt; font-family:Calibri; text-align:center; text-transform:uppercase; word-wrap:normal">@serie@ n&ordm; @numeracao_serie@</p>';

        $secaoModeloTituloTextoDTO->setStrConteudo($htmlTitulo);
        $secaoModeloTituloTextoDTO->setNumOrdem(10);
        $secaoModeloTituloTextoDTO->setStrSinCabecalho('N');
        $secaoModeloTituloTextoDTO->setStrSinRodape('N');
        $secaoModeloTituloTextoDTO->setStrSinPrincipal('N');
        $secaoModeloTituloTextoDTO->setStrSinAssinatura('N');
        $secaoModeloTituloTextoDTO->setStrSinSomenteLeitura('S');
        $secaoModeloTituloTextoDTO->setStrSinDinamica('S');
        $secaoModeloTituloTextoDTO->setStrSinHtml('S');
        $secaoModeloTituloTextoDTO->setStrSinAtivo('S');

        $secaoModeloCabecalhoTextoDTO = $secaoModeloRN->cadastrar($secaoModeloTituloTextoDTO);

        //adicionando as se��es do modelo: Corpo de Texto
        $this->logar('CRIANDO SE�AO DO MODELO - Corpo do Texto');
        $secaoModeloRN = new SecaoModeloRN();

        $secaoModeloCorpoTextoDTO = new SecaoModeloDTO();
        $secaoModeloCorpoTextoDTO->retTodos();
        $secaoModeloCorpoTextoDTO->setNumIdSecaoModelo(null);
        $secaoModeloCorpoTextoDTO->setNumIdModelo($modeloDTO->getNumIdModelo());
        $secaoModeloCorpoTextoDTO->setStrNome('Corpo do Texto');
        $secaoModeloCorpoTextoDTO->setStrConteudo(null);
        $secaoModeloCorpoTextoDTO->setNumOrdem(20);
        $secaoModeloCorpoTextoDTO->setStrSinCabecalho('N');
        $secaoModeloCorpoTextoDTO->setStrSinRodape('N');
        $secaoModeloCorpoTextoDTO->setStrSinPrincipal('S');
        $secaoModeloCorpoTextoDTO->setStrSinAssinatura('N');
        $secaoModeloCorpoTextoDTO->setStrSinSomenteLeitura('S');
        $secaoModeloCorpoTextoDTO->setStrSinDinamica('N');
        $secaoModeloCorpoTextoDTO->setStrSinHtml('N');
        $secaoModeloCorpoTextoDTO->setStrSinAtivo('S');

        $secaoModeloCorpoTextoDTO = $secaoModeloRN->cadastrar($secaoModeloCorpoTextoDTO);

        //adicionando as se��es do modelo: Assinatura
        $this->logar('CRIANDO SE�AO DO MODELO - Assinatura');
        $secaoModeloRN = new SecaoModeloRN();

        $secaoModeloAssinaturaTextoDTO = new SecaoModeloDTO();
        $secaoModeloAssinaturaTextoDTO->retTodos();
        $secaoModeloAssinaturaTextoDTO->setNumIdSecaoModelo(null);
        $secaoModeloAssinaturaTextoDTO->setNumIdModelo($modeloDTO->getNumIdModelo());
        $secaoModeloAssinaturaTextoDTO->setStrNome('Assinatura');
        $secaoModeloAssinaturaTextoDTO->setStrConteudo(null);
        $secaoModeloAssinaturaTextoDTO->setNumOrdem(30);
        $secaoModeloAssinaturaTextoDTO->setStrSinCabecalho('N');
        $secaoModeloAssinaturaTextoDTO->setStrSinRodape('N');
        $secaoModeloAssinaturaTextoDTO->setStrSinPrincipal('N');
        $secaoModeloAssinaturaTextoDTO->setStrSinAssinatura('S');
        $secaoModeloAssinaturaTextoDTO->setStrSinSomenteLeitura('N');
        $secaoModeloAssinaturaTextoDTO->setStrSinDinamica('N');
        $secaoModeloAssinaturaTextoDTO->setStrSinHtml('N');
        $secaoModeloAssinaturaTextoDTO->setStrSinAtivo('S');

        $secaoModeloCorpoTextoDTO = $secaoModeloRN->cadastrar($secaoModeloAssinaturaTextoDTO);

        //secao do rodap�
        $this->logar('CRIANDO SE�AO DO MODELO - Rodap�');
        $secaoModeloRodapeDTO = new SecaoModeloDTO();
        $secaoModeloRodapeDTO->retTodos();
        $secaoModeloRodapeDTO->setNumIdSecaoModelo(null);

        $htmlRodape = '<hr style="border:none; padding:0; margin:5px 2px 0 2px; border-top:medium double #333" />
    <table border="0" cellpadding="2" cellspacing="0" width="100%">
        <tbody>
            <tr>
                <td align="left" style="font-family:Calibri;font-size:9pt;border:0;" width="50%"><strong>Refer&ecirc;ncia:</strong> Processo n&ordm; @processo@</td>
                <td align="right" style="font-family:Calibri;font-size:9pt;border:0;" width="50%">SEI n&ordm; @documento@</td>
            </tr>
        </tbody>
    </table>
    ';

        $secaoModeloRodapeDTO->setNumIdModelo($modeloDTO->getNumIdModelo());
        $secaoModeloRodapeDTO->setStrNome('Rodap�');
        $secaoModeloRodapeDTO->setStrConteudo($htmlRodape);
        $secaoModeloRodapeDTO->setNumOrdem(1000);
        $secaoModeloRodapeDTO->setStrSinCabecalho('N');
        $secaoModeloRodapeDTO->setStrSinRodape('S');
        $secaoModeloRodapeDTO->setStrSinPrincipal('N');
        $secaoModeloRodapeDTO->setStrSinAssinatura('N');
        $secaoModeloRodapeDTO->setStrSinSomenteLeitura('S');
        $secaoModeloRodapeDTO->setStrSinDinamica('S');
        $secaoModeloRodapeDTO->setStrSinHtml('S');
        $secaoModeloRodapeDTO->setStrSinAtivo('S');

        $secaoModeloRodapeDTO = $secaoModeloRN->cadastrar($secaoModeloRodapeDTO);

        //Criar o Grupo de Tipo de Documento Internos do Sistema.
        $grupoSerieRN = new GrupoSerieRN();

        if (BancoSEI::getInstance() instanceof InfraMySql) {

            //verificando antes a situa�ao da tabela seq_grupo_serie
            $arrDados = BancoSEI::getInstance()->consultarSql('SELECT * FROM seq_grupo_serie ORDER BY id DESC LIMIT 1 ');

            $grupoSerieDTOLista = new GrupoSerieDTO();
            $grupoSerieDTOLista->retTodos();
            $grupoSerieDTOLista->setOrd("IdGrupoSerie", InfraDTO::$TIPO_ORDENACAO_DESC);
            $grupoSerieDTOLista->setNumMaxRegistrosRetorno(1);

            $arrListaGrupoSerie = $grupoSerieRN->listarRN0778($grupoSerieDTOLista);

            //ja tem registro na SEQ, insere apenas se ID da SEQ estiver incorreto
            if ($arrDados != null && count($arrDados) > 0) {

                if ($arrDados[0]['id'] < $arrListaGrupoSerie[0]->getNumIdGrupoSerie()) {

                    //INSERT para garantir a SEQ na posi�ao correta
                    BancoSEI::getInstance()->executarSql('INSERT INTO seq_grupo_serie ( id ) VALUES ( ' . $arrListaGrupoSerie[0]->getNumIdGrupoSerie() . ') ');
                }
            } //nao tem registro na SEQ ainda, colocar o ID do grupo_serie mais atual
            else {

                //INSERT para garantir a SEQ na posi�ao correta
                BancoSEI::getInstance()->executarSql('INSERT INTO seq_grupo_serie ( id ) VALUES ( ' . $arrListaGrupoSerie[0]->getNumIdGrupoSerie() . ') ');
            }
        }

        $this->logar('BUSCANDO GRUPO DE TIPO DE DOCUMENTO "Internos do Sistema"');
        $grupoSerieDTO = new GrupoSerieDTO();
        $grupoSerieDTO->retTodos();
        $grupoSerieDTO->setStrNome('Internos do Sistema');
        $grupoSerieDTO = $grupoSerieRN->consultarRN0777($grupoSerieDTO);

        $this->logar('CRIANDO TIPO DE DOCUMENTO Vincula��o de Respons�vel Legal a Pessoa Jur�dica');
        $serieDTO = new SerieDTO();
        $serieDTO->retTodos();
        $serieRN = new SerieRN();

        $serieDTO->setNumIdSerie(null);
        $serieDTO->setNumIdGrupoSerie($grupoSerieDTO->getNumIdGrupoSerie());
        $serieDTO->setStrStaNumeracao(SerieRN::$TN_SEM_NUMERACAO);
        $serieDTO->setStrStaAplicabilidade(SerieRN::$TA_INTERNO);
        $serieDTO->setNumIdModeloEdoc(null);
        $serieDTO->setNumIdModelo($modeloDTO->getNumIdModelo());
        $serieDTO->setStrNome('Vincula��o de Respons�vel Legal a Pessoa Jur�dica');
        $serieDTO->setStrDescricao('Utilizado para a gera��o autom�tica do formul�rio de Vincula��o de Respons�vel Legal a Pessoa Jur�dica por Usu�rio Externo diretamente no Acesso Externo do SEI.');
        $serieDTO->setStrSinInteressado('S');
        $serieDTO->setStrSinDestinatario('N');
        $serieDTO->setStrSinAssinaturaPublicacao('S');
        $serieDTO->setStrSinInterno('S');
        $serieDTO->setStrSinAtivo('S');
        $serieDTO->setArrObjRelSerieAssuntoDTO(array());
        $serieDTO->setArrObjRelSerieVeiculoPublicacaoDTO(array());
        $serieDTO->setNumIdTipoFormulario(null);
        $serieDTO->setArrObjSerieRestricaoDTO(array());

        $serieDTO = $serieRN->cadastrarRN0642($serieDTO);

        $this->logar('ATUALIZANDO INFRA_PARAMETRO (' . MdPetIntSerieRN::$MD_PET_ID_SERIE_FORMULARIO . ')');
        $nomeParamIdSerie = MdPetIntSerieRN::$MD_PET_ID_SERIE_FORMULARIO;

        BancoSEI::getInstance()->executarSql('INSERT INTO infra_parametro ( valor, nome )  VALUES (\'' . $serieDTO->getNumIdSerie() . '\' , \'' . $nomeParamIdSerie . '\' ) ');
    }

    //Procura��o Eletr�nica Simples
    private function _gerarModeloProcuracaoEletronicaSimples()
    {

        $this->logar('CRIANDO MODELO "Modelo_Peticionamento_ProcuracaoS"');
        $modeloRN = new ModeloRN();
        $modeloDTO = new ModeloDTO();
        $modeloDTO->retTodos();
        $modeloDTO->setNumIdModelo(null);
        $modeloDTO->setStrNome('Modelo_Peticionamento_ProcuracaoS');
        $modeloDTO->setStrSinAtivo('S');
        $modeloDTO = $modeloRN->cadastrar($modeloDTO);

        //adicionando as se��es do modelo: Cabe�alho
        $this->logar('CRIANDO SE�AO DO MODELO - Cabe�alho');
        $secaoModeloRN = new SecaoModeloRN();

        $secaoModeloCabecalhoTextoDTO = new SecaoModeloDTO();
        $secaoModeloCabecalhoTextoDTO->retTodos();
        $secaoModeloCabecalhoTextoDTO->setNumIdSecaoModelo(null);
        $secaoModeloCabecalhoTextoDTO->setNumIdModelo($modeloDTO->getNumIdModelo());
        $secaoModeloCabecalhoTextoDTO->setStrNome('Cabe�alho');

        $htmlCabecalho = '<div align="center">@timbre_orgao@</div>

	<p style="font-weight:bold; font-size:13pt; font-family:Calibri; text-align:center; text-transform:uppercase; word-wrap:normal">@descricao_orgao_maiusculas@</p>

	<p>&nbsp;&nbsp;</p>';

        $secaoModeloCabecalhoTextoDTO->setStrConteudo($htmlCabecalho);
        $secaoModeloCabecalhoTextoDTO->setNumOrdem(0);
        $secaoModeloCabecalhoTextoDTO->setStrSinCabecalho('S');
        $secaoModeloCabecalhoTextoDTO->setStrSinRodape('N');
        $secaoModeloCabecalhoTextoDTO->setStrSinPrincipal('N');
        $secaoModeloCabecalhoTextoDTO->setStrSinAssinatura('N');
        $secaoModeloCabecalhoTextoDTO->setStrSinSomenteLeitura('S');
        $secaoModeloCabecalhoTextoDTO->setStrSinDinamica('S');
        $secaoModeloCabecalhoTextoDTO->setStrSinHtml('S');
        $secaoModeloCabecalhoTextoDTO->setStrSinAtivo('S');

        $secaoModeloCabecalhoTextoDTO = $secaoModeloRN->cadastrar($secaoModeloCabecalhoTextoDTO);

        //adicionando as se��es do modelo: T�tulo do Documento
        $this->logar('CRIANDO SE�AO DO MODELO - T�tulo do Documento');
        $secaoModeloRN = new SecaoModeloRN();

        $secaoModeloTituloTextoDTO = new SecaoModeloDTO();
        $secaoModeloTituloTextoDTO->retTodos();
        $secaoModeloTituloTextoDTO->setNumIdSecaoModelo(null);
        $secaoModeloTituloTextoDTO->setNumIdModelo($modeloDTO->getNumIdModelo());
        $secaoModeloTituloTextoDTO->setStrNome('T�tulo do Documento');

        $htmlTitulo = '<p style="font-size:13pt; font-family:Calibri; text-align:center; text-transform:uppercase; word-wrap:normal">@serie@ n&ordm; @numeracao_serie@</p>';

        $secaoModeloTituloTextoDTO->setStrConteudo($htmlTitulo);
        $secaoModeloTituloTextoDTO->setNumOrdem(10);
        $secaoModeloTituloTextoDTO->setStrSinCabecalho('N');
        $secaoModeloTituloTextoDTO->setStrSinRodape('N');
        $secaoModeloTituloTextoDTO->setStrSinPrincipal('N');
        $secaoModeloTituloTextoDTO->setStrSinAssinatura('N');
        $secaoModeloTituloTextoDTO->setStrSinSomenteLeitura('S');
        $secaoModeloTituloTextoDTO->setStrSinDinamica('S');
        $secaoModeloTituloTextoDTO->setStrSinHtml('S');
        $secaoModeloTituloTextoDTO->setStrSinAtivo('S');

        $secaoModeloCabecalhoTextoDTO = $secaoModeloRN->cadastrar($secaoModeloTituloTextoDTO);

        //adicionando as se��es do modelo: Corpo de Texto
        $this->logar('CRIANDO SE�AO DO MODELO - Corpo do Texto');
        $secaoModeloRN = new SecaoModeloRN();

        $secaoModeloCorpoTextoDTO = new SecaoModeloDTO();
        $secaoModeloCorpoTextoDTO->retTodos();
        $secaoModeloCorpoTextoDTO->setNumIdSecaoModelo(null);
        $secaoModeloCorpoTextoDTO->setNumIdModelo($modeloDTO->getNumIdModelo());
        $secaoModeloCorpoTextoDTO->setStrNome('Corpo do Texto');
        $secaoModeloCorpoTextoDTO->setStrConteudo(null);
        $secaoModeloCorpoTextoDTO->setNumOrdem(20);
        $secaoModeloCorpoTextoDTO->setStrSinCabecalho('N');
        $secaoModeloCorpoTextoDTO->setStrSinRodape('N');
        $secaoModeloCorpoTextoDTO->setStrSinPrincipal('S');
        $secaoModeloCorpoTextoDTO->setStrSinAssinatura('N');
        $secaoModeloCorpoTextoDTO->setStrSinSomenteLeitura('S');
        $secaoModeloCorpoTextoDTO->setStrSinDinamica('N');
        $secaoModeloCorpoTextoDTO->setStrSinHtml('N');
        $secaoModeloCorpoTextoDTO->setStrSinAtivo('S');

        $secaoModeloCorpoTextoDTO = $secaoModeloRN->cadastrar($secaoModeloCorpoTextoDTO);

        //adicionando as se��es do modelo: Assinatura
        $this->logar('CRIANDO SE�AO DO MODELO - Assinatura');
        $secaoModeloRN = new SecaoModeloRN();

        $secaoModeloAssinaturaDTO = new SecaoModeloDTO();
        $secaoModeloAssinaturaDTO->retTodos();
        $secaoModeloAssinaturaDTO->setNumIdSecaoModelo(null);
        $secaoModeloAssinaturaDTO->setNumIdModelo($modeloDTO->getNumIdModelo());
        $secaoModeloAssinaturaDTO->setStrNome('Assinatura');
        $secaoModeloAssinaturaDTO->setStrConteudo(null);
        $secaoModeloAssinaturaDTO->setNumOrdem(30);
        $secaoModeloAssinaturaDTO->setStrSinCabecalho('N');
        $secaoModeloAssinaturaDTO->setStrSinRodape('N');
        $secaoModeloAssinaturaDTO->setStrSinPrincipal('N');
        $secaoModeloAssinaturaDTO->setStrSinAssinatura('S');
        $secaoModeloAssinaturaDTO->setStrSinSomenteLeitura('N');
        $secaoModeloAssinaturaDTO->setStrSinDinamica('N');
        $secaoModeloAssinaturaDTO->setStrSinHtml('N');
        $secaoModeloAssinaturaDTO->setStrSinAtivo('S');

        $secaoModeloCorpoTextoDTO = $secaoModeloRN->cadastrar($secaoModeloAssinaturaDTO);

        //secao do rodap�
        $this->logar('CRIANDO SE�AO DO MODELO - Rodap�');
        $secaoModeloRodapeDTO = new SecaoModeloDTO();
        $secaoModeloRodapeDTO->retTodos();
        $secaoModeloRodapeDTO->setNumIdSecaoModelo(null);

        $htmlRodape = '<hr style="border:none; padding:0; margin:5px 2px 0 2px; border-top:medium double #333" />
		<table border="0" cellpadding="2" cellspacing="0" width="100%">
			<tbody>
				<tr>
					<td align="left" style="font-family:Calibri;font-size:9pt;border:0;" width="50%"><strong>Refer&ecirc;ncia:</strong> Processo n&ordm; @processo@</td>
					<td align="right" style="font-family:Calibri;font-size:9pt;border:0;" width="50%">SEI n&ordm; @documento@</td>
				</tr>
			</tbody>
		</table>
		';

        $secaoModeloRodapeDTO->setNumIdModelo($modeloDTO->getNumIdModelo());
        $secaoModeloRodapeDTO->setStrNome('Rodap�');
        $secaoModeloRodapeDTO->setStrConteudo($htmlRodape);
        $secaoModeloRodapeDTO->setNumOrdem(1000);
        $secaoModeloRodapeDTO->setStrSinCabecalho('N');
        $secaoModeloRodapeDTO->setStrSinRodape('S');
        $secaoModeloRodapeDTO->setStrSinPrincipal('N');
        $secaoModeloRodapeDTO->setStrSinAssinatura('N');
        $secaoModeloRodapeDTO->setStrSinSomenteLeitura('S');
        $secaoModeloRodapeDTO->setStrSinDinamica('S');
        $secaoModeloRodapeDTO->setStrSinHtml('S');
        $secaoModeloRodapeDTO->setStrSinAtivo('S');

        $secaoModeloRodapeDTO = $secaoModeloRN->cadastrar($secaoModeloRodapeDTO);

        //Criar o Grupo de Tipo de Documento Internos do Sistema.
        $grupoSerieRN = new GrupoSerieRN();

        if (BancoSEI::getInstance() instanceof InfraMySql) {

            //verificando antes a situa�ao da tabela seq_grupo_serie
            $arrDados = BancoSEI::getInstance()->consultarSql('SELECT * FROM seq_grupo_serie ORDER BY id DESC LIMIT 1 ');

            $grupoSerieDTOLista = new GrupoSerieDTO();
            $grupoSerieDTOLista->retTodos();
            $grupoSerieDTOLista->setOrd("IdGrupoSerie", InfraDTO::$TIPO_ORDENACAO_DESC);
            $grupoSerieDTOLista->setNumMaxRegistrosRetorno(1);

            $arrListaGrupoSerie = $grupoSerieRN->listarRN0778($grupoSerieDTOLista);

            //ja tem registro na SEQ, insere apenas se ID da SEQ estiver incorreto
            if ($arrDados != null && count($arrDados) > 0) {

                if ($arrDados[0]['id'] < $arrListaGrupoSerie[0]->getNumIdGrupoSerie()) {

                    //INSERT para garantir a SEQ na posi�ao correta
                    BancoSEI::getInstance()->executarSql('INSERT INTO seq_grupo_serie ( id ) VALUES ( ' . $arrListaGrupoSerie[0]->getNumIdGrupoSerie() . ') ');
                }
            } //nao tem registro na SEQ ainda, colocar o ID do grupo_serie mais atual
            else {

                //INSERT para garantir a SEQ na posi�ao correta
                BancoSEI::getInstance()->executarSql('INSERT INTO seq_grupo_serie ( id ) VALUES ( ' . $arrListaGrupoSerie[0]->getNumIdGrupoSerie() . ') ');
            }
        }

        $this->logar('BUSCANDO GRUPO DE TIPO DE DOCUMENTO "Internos do Sistema"');
        $grupoSerieDTO = new GrupoSerieDTO();
        $grupoSerieDTO->retTodos();
        $grupoSerieDTO->setStrNome('Internos do Sistema');
        $grupoSerieDTO = $grupoSerieRN->consultarRN0777($grupoSerieDTO);

        $this->logar('CRIANDO TIPO DE DOCUMENTO Modelo de Procura��o Eletr�nica Simples');
        $serieDTO = new SerieDTO();
        $serieDTO->retTodos();
        $serieRN = new SerieRN();

        $serieDTO->setNumIdSerie(null);
        $serieDTO->setNumIdGrupoSerie($grupoSerieDTO->getNumIdGrupoSerie());
        $serieDTO->setStrStaNumeracao(SerieRN::$TN_SEM_NUMERACAO);
        $serieDTO->setStrStaAplicabilidade(SerieRN::$TA_INTERNO);
        $serieDTO->setNumIdModeloEdoc(null);
        $serieDTO->setNumIdModelo($modeloDTO->getNumIdModelo());
        $serieDTO->setStrNome('Procura��o Eletr�nica');
        $serieDTO->setStrDescricao('Utilizado para a gera��o autom�tica da Procura��o Eletr�nica nos Peticionamentos Eletr�nicos realizados por Usu�rio Externo diretamente no Acesso Externo do SEI.');
        $serieDTO->setStrSinInteressado('S');
        $serieDTO->setStrSinDestinatario('N');
        $serieDTO->setStrSinAssinaturaPublicacao('S');
        $serieDTO->setStrSinInterno('S');
        $serieDTO->setStrSinAtivo('S');
        $serieDTO->setArrObjRelSerieAssuntoDTO(array());
        $serieDTO->setArrObjRelSerieVeiculoPublicacaoDTO(array());
        $serieDTO->setNumIdTipoFormulario(null);
        $serieDTO->setArrObjSerieRestricaoDTO(array());

        $serieDTO = $serieRN->cadastrarRN0642($serieDTO);

        $this->logar('ATUALIZANDO INFRA_PARAMETRO (' . MdPetAtualizadorSeiRN::$MD_PET_ID_SERIE_PROCURACAOS . ')');
        $nomeParamIdSerie = MdPetAtualizadorSeiRN::$MD_PET_ID_SERIE_PROCURACAOS;

        BancoSEI::getInstance()->executarSql('INSERT INTO infra_parametro ( valor, nome )  VALUES (\'' . $serieDTO->getNumIdSerie() . '\' , \'' . $nomeParamIdSerie . '\' ) ');
    }

    //Fim Procura��o Eletr�nica Simples

    private function _gerarModeloProcuracaoEletronica()
    {

        $this->logar('CRIANDO MODELO "Modulo_Peticionamento_ProcuracaoEletronicaEspecial"');
        $modeloRN = new ModeloRN();
        $modeloDTO = new ModeloDTO();
        $modeloDTO->retTodos();
        $modeloDTO->setNumIdModelo(null);
        $modeloDTO->setStrNome('Modulo_Peticionamento_ProcuracaoEletronicaEspecial');
        $modeloDTO->setStrSinAtivo('S');
        $modeloDTO = $modeloRN->cadastrar($modeloDTO);

        //adicionando as se��es do modelo: Cabe�alho
        $this->logar('CRIANDO SE�AO DO MODELO - Cabe�alho');
        $secaoModeloRN = new SecaoModeloRN();

        $secaoModeloCabecalhoTextoDTO = new SecaoModeloDTO();
        $secaoModeloCabecalhoTextoDTO->retTodos();
        $secaoModeloCabecalhoTextoDTO->setNumIdSecaoModelo(null);
        $secaoModeloCabecalhoTextoDTO->setNumIdModelo($modeloDTO->getNumIdModelo());
        $secaoModeloCabecalhoTextoDTO->setStrNome('Cabe�alho');

        $htmlCabecalho = '<div align="center">@timbre_orgao@</div>

<p style="font-weight:bold; font-size:13pt; font-family:Calibri; text-align:center; text-transform:uppercase; word-wrap:normal">@descricao_orgao_maiusculas@</p>

<p>&nbsp;&nbsp;</p>';

        $secaoModeloCabecalhoTextoDTO->setStrConteudo($htmlCabecalho);
        $secaoModeloCabecalhoTextoDTO->setNumOrdem(0);
        $secaoModeloCabecalhoTextoDTO->setStrSinCabecalho('S');
        $secaoModeloCabecalhoTextoDTO->setStrSinRodape('N');
        $secaoModeloCabecalhoTextoDTO->setStrSinPrincipal('N');
        $secaoModeloCabecalhoTextoDTO->setStrSinAssinatura('N');
        $secaoModeloCabecalhoTextoDTO->setStrSinSomenteLeitura('S');
        $secaoModeloCabecalhoTextoDTO->setStrSinDinamica('S');
        $secaoModeloCabecalhoTextoDTO->setStrSinHtml('S');
        $secaoModeloCabecalhoTextoDTO->setStrSinAtivo('S');

        $secaoModeloCabecalhoTextoDTO = $secaoModeloRN->cadastrar($secaoModeloCabecalhoTextoDTO);

        //adicionando as se��es do modelo: T�tulo do Documento
        $this->logar('CRIANDO SE�AO DO MODELO - T�tulo do Documento');
        $secaoModeloRN = new SecaoModeloRN();

        $secaoModeloTituloTextoDTO = new SecaoModeloDTO();
        $secaoModeloTituloTextoDTO->retTodos();
        $secaoModeloTituloTextoDTO->setNumIdSecaoModelo(null);
        $secaoModeloTituloTextoDTO->setNumIdModelo($modeloDTO->getNumIdModelo());
        $secaoModeloTituloTextoDTO->setStrNome('T�tulo do Documento');

        $htmlTitulo = '<p style="font-size:13pt; font-family:Calibri; text-align:center; text-transform:uppercase; word-wrap:normal">@serie@ n&ordm; @numeracao_serie@</p>';

        $secaoModeloTituloTextoDTO->setStrConteudo($htmlTitulo);
        $secaoModeloTituloTextoDTO->setNumOrdem(10);
        $secaoModeloTituloTextoDTO->setStrSinCabecalho('N');
        $secaoModeloTituloTextoDTO->setStrSinRodape('N');
        $secaoModeloTituloTextoDTO->setStrSinPrincipal('N');
        $secaoModeloTituloTextoDTO->setStrSinAssinatura('N');
        $secaoModeloTituloTextoDTO->setStrSinSomenteLeitura('S');
        $secaoModeloTituloTextoDTO->setStrSinDinamica('S');
        $secaoModeloTituloTextoDTO->setStrSinHtml('S');
        $secaoModeloTituloTextoDTO->setStrSinAtivo('S');

        $secaoModeloCabecalhoTextoDTO = $secaoModeloRN->cadastrar($secaoModeloTituloTextoDTO);

        //adicionando as se��es do modelo: Corpo de Texto
        $this->logar('CRIANDO SE�AO DO MODELO - Corpo do Texto');
        $secaoModeloRN = new SecaoModeloRN();

        $secaoModeloCorpoTextoDTO = new SecaoModeloDTO();
        $secaoModeloCorpoTextoDTO->retTodos();
        $secaoModeloCorpoTextoDTO->setNumIdSecaoModelo(null);
        $secaoModeloCorpoTextoDTO->setNumIdModelo($modeloDTO->getNumIdModelo());
        $secaoModeloCorpoTextoDTO->setStrNome('Corpo do Texto');
        $secaoModeloCorpoTextoDTO->setStrConteudo(null);
        $secaoModeloCorpoTextoDTO->setNumOrdem(20);
        $secaoModeloCorpoTextoDTO->setStrSinCabecalho('N');
        $secaoModeloCorpoTextoDTO->setStrSinRodape('N');
        $secaoModeloCorpoTextoDTO->setStrSinPrincipal('S');
        $secaoModeloCorpoTextoDTO->setStrSinAssinatura('N');
        $secaoModeloCorpoTextoDTO->setStrSinSomenteLeitura('S');
        $secaoModeloCorpoTextoDTO->setStrSinDinamica('N');
        $secaoModeloCorpoTextoDTO->setStrSinHtml('N');
        $secaoModeloCorpoTextoDTO->setStrSinAtivo('S');

        $secaoModeloCorpoTextoDTO = $secaoModeloRN->cadastrar($secaoModeloCorpoTextoDTO);

        //adicionando as se��es do modelo: Assinatura
        $this->logar('CRIANDO SE�AO DO MODELO - Assinatura');
        $secaoModeloRN = new SecaoModeloRN();

        $secaoModeloAssinaturaDTO = new SecaoModeloDTO();
        $secaoModeloAssinaturaDTO->retTodos();
        $secaoModeloAssinaturaDTO->setNumIdSecaoModelo(null);
        $secaoModeloAssinaturaDTO->setNumIdModelo($modeloDTO->getNumIdModelo());
        $secaoModeloAssinaturaDTO->setStrNome('Assinatura');
        $secaoModeloAssinaturaDTO->setStrConteudo(null);
        $secaoModeloAssinaturaDTO->setNumOrdem(30);
        $secaoModeloAssinaturaDTO->setStrSinCabecalho('N');
        $secaoModeloAssinaturaDTO->setStrSinRodape('N');
        $secaoModeloAssinaturaDTO->setStrSinPrincipal('N');
        $secaoModeloAssinaturaDTO->setStrSinAssinatura('S');
        $secaoModeloAssinaturaDTO->setStrSinSomenteLeitura('N');
        $secaoModeloAssinaturaDTO->setStrSinDinamica('N');
        $secaoModeloAssinaturaDTO->setStrSinHtml('N');
        $secaoModeloAssinaturaDTO->setStrSinAtivo('S');

        $secaoModeloCorpoTextoDTO = $secaoModeloRN->cadastrar($secaoModeloAssinaturaDTO);

        //secao do rodap�
        $this->logar('CRIANDO SE�AO DO MODELO - Rodap�');
        $secaoModeloRodapeDTO = new SecaoModeloDTO();
        $secaoModeloRodapeDTO->retTodos();
        $secaoModeloRodapeDTO->setNumIdSecaoModelo(null);

        $htmlRodape = '<hr style="border:none; padding:0; margin:5px 2px 0 2px; border-top:medium double #333" />
    <table border="0" cellpadding="2" cellspacing="0" width="100%">
        <tbody>
            <tr>
                <td align="left" style="font-family:Calibri;font-size:9pt;border:0;" width="50%"><strong>Refer&ecirc;ncia:</strong> Processo n&ordm; @processo@</td>
                <td align="right" style="font-family:Calibri;font-size:9pt;border:0;" width="50%">SEI n&ordm; @documento@</td>
            </tr>
        </tbody>
    </table>
    ';

        $secaoModeloRodapeDTO->setNumIdModelo($modeloDTO->getNumIdModelo());
        $secaoModeloRodapeDTO->setStrNome('Rodap�');
        $secaoModeloRodapeDTO->setStrConteudo($htmlRodape);
        $secaoModeloRodapeDTO->setNumOrdem(1000);
        $secaoModeloRodapeDTO->setStrSinCabecalho('N');
        $secaoModeloRodapeDTO->setStrSinRodape('S');
        $secaoModeloRodapeDTO->setStrSinPrincipal('N');
        $secaoModeloRodapeDTO->setStrSinAssinatura('N');
        $secaoModeloRodapeDTO->setStrSinSomenteLeitura('S');
        $secaoModeloRodapeDTO->setStrSinDinamica('S');
        $secaoModeloRodapeDTO->setStrSinHtml('S');
        $secaoModeloRodapeDTO->setStrSinAtivo('S');

        $secaoModeloRodapeDTO = $secaoModeloRN->cadastrar($secaoModeloRodapeDTO);

        //Criar o Grupo de Tipo de Documento Internos do Sistema.
        $grupoSerieRN = new GrupoSerieRN();

        if (BancoSEI::getInstance() instanceof InfraMySql) {

            //verificando antes a situa�ao da tabela seq_grupo_serie
            $arrDados = BancoSEI::getInstance()->consultarSql('SELECT * FROM seq_grupo_serie ORDER BY id DESC LIMIT 1 ');

            $grupoSerieDTOLista = new GrupoSerieDTO();
            $grupoSerieDTOLista->retTodos();
            $grupoSerieDTOLista->setOrd("IdGrupoSerie", InfraDTO::$TIPO_ORDENACAO_DESC);
            $grupoSerieDTOLista->setNumMaxRegistrosRetorno(1);

            $arrListaGrupoSerie = $grupoSerieRN->listarRN0778($grupoSerieDTOLista);

            //ja tem registro na SEQ, insere apenas se ID da SEQ estiver incorreto
            if ($arrDados != null && count($arrDados) > 0) {

                if ($arrDados[0]['id'] < $arrListaGrupoSerie[0]->getNumIdGrupoSerie()) {

                    //INSERT para garantir a SEQ na posi�ao correta
                    BancoSEI::getInstance()->executarSql('INSERT INTO seq_grupo_serie ( id ) VALUES ( ' . $arrListaGrupoSerie[0]->getNumIdGrupoSerie() . ') ');
                }
            } //nao tem registro na SEQ ainda, colocar o ID do grupo_serie mais atual
            else {

                //INSERT para garantir a SEQ na posi�ao correta
                BancoSEI::getInstance()->executarSql('INSERT INTO seq_grupo_serie ( id ) VALUES ( ' . $arrListaGrupoSerie[0]->getNumIdGrupoSerie() . ') ');
            }
        }

        $this->logar('BUSCANDO GRUPO DE TIPO DE DOCUMENTO "Internos do Sistema"');
        $grupoSerieDTO = new GrupoSerieDTO();
        $grupoSerieDTO->retTodos();
        $grupoSerieDTO->setStrNome('Internos do Sistema');
        $grupoSerieDTO = $grupoSerieRN->consultarRN0777($grupoSerieDTO);

        $this->logar('CRIANDO TIPO DE DOCUMENTO Modelo de Procura��o Eletr�nica Especial');
        $serieDTO = new SerieDTO();
        $serieDTO->retTodos();
        $serieRN = new SerieRN();

        $serieDTO->setNumIdSerie(null);
        $serieDTO->setNumIdGrupoSerie($grupoSerieDTO->getNumIdGrupoSerie());
        $serieDTO->setStrStaNumeracao(SerieRN::$TN_SEM_NUMERACAO);
        $serieDTO->setStrStaAplicabilidade(SerieRN::$TA_INTERNO);
        $serieDTO->setNumIdModeloEdoc(null);
        $serieDTO->setNumIdModelo($modeloDTO->getNumIdModelo());
        $serieDTO->setStrNome('Procura��o Eletr�nica Especial');
        $serieDTO->setStrDescricao('Utilizado para a gera��o autom�tica da Procura��o Eletr�nica Especial nos Peticionamentos Eletr�nicos realizados por Usu�rio Externo diretamente no Acesso Externo do SEI.');
        $serieDTO->setStrSinInteressado('S');
        $serieDTO->setStrSinDestinatario('N');
        $serieDTO->setStrSinAssinaturaPublicacao('S');
        $serieDTO->setStrSinInterno('S');
        $serieDTO->setStrSinAtivo('S');
        $serieDTO->setArrObjRelSerieAssuntoDTO(array());
        $serieDTO->setArrObjRelSerieVeiculoPublicacaoDTO(array());
        $serieDTO->setNumIdTipoFormulario(null);
        $serieDTO->setArrObjSerieRestricaoDTO(array());

        $serieDTO = $serieRN->cadastrarRN0642($serieDTO);

        $this->logar('ATUALIZANDO INFRA_PARAMETRO (' . MdPetIntSerieRN::$MD_PET_ID_SERIE_PROCURACAOE . ')');
        $nomeParamIdSerie = MdPetIntSerieRN::$MD_PET_ID_SERIE_PROCURACAOE;

        BancoSEI::getInstance()->executarSql('INSERT INTO infra_parametro ( valor, nome )  VALUES (\'' . $serieDTO->getNumIdSerie() . '\' , \'' . $nomeParamIdSerie . '\' ) ');
    }

    private function _gerarModeloRevogacao()
    {

        $this->logar('CRIANDO MODELO "Modulo_Peticionamento_Procuracao_Revogacao"');
        $modeloRN = new ModeloRN();
        $modeloDTO = new ModeloDTO();
        $modeloDTO->setNumIdModelo(null);
        $modeloDTO->retTodos();
        $modeloDTO->setStrNome('Modulo_Peticionamento_Procuracao_Revogacao');
        $modeloDTO->setStrSinAtivo('S');
        $modeloDTO = $modeloRN->cadastrar($modeloDTO);

        //adicionando as se��es do modelo: Cabe�alho
        $this->logar('CRIANDO SE�AO DO MODELO - Cabe�alho');
        $secaoModeloRN = new SecaoModeloRN();

        $secaoModeloCabecalhoTextoDTO = new SecaoModeloDTO();
        $secaoModeloCabecalhoTextoDTO->retTodos();
        $secaoModeloCabecalhoTextoDTO->setNumIdSecaoModelo(null);
        $secaoModeloCabecalhoTextoDTO->setNumIdModelo($modeloDTO->getNumIdModelo());
        $secaoModeloCabecalhoTextoDTO->setStrNome('Cabe�alho');

        $htmlCabecalho = '<div align="center">@timbre_orgao@</div>

<p style="font-weight:bold; font-size:13pt; font-family:Calibri; text-align:center; text-transform:uppercase; word-wrap:normal">@descricao_orgao_maiusculas@</p>

<p>&nbsp;&nbsp;</p>';

        $secaoModeloCabecalhoTextoDTO->setStrConteudo($htmlCabecalho);
        $secaoModeloCabecalhoTextoDTO->setNumOrdem(0);
        $secaoModeloCabecalhoTextoDTO->setStrSinCabecalho('S');
        $secaoModeloCabecalhoTextoDTO->setStrSinRodape('N');
        $secaoModeloCabecalhoTextoDTO->setStrSinPrincipal('N');
        $secaoModeloCabecalhoTextoDTO->setStrSinAssinatura('N');
        $secaoModeloCabecalhoTextoDTO->setStrSinSomenteLeitura('S');
        $secaoModeloCabecalhoTextoDTO->setStrSinDinamica('S');
        $secaoModeloCabecalhoTextoDTO->setStrSinHtml('S');
        $secaoModeloCabecalhoTextoDTO->setStrSinAtivo('S');

        $secaoModeloCabecalhoTextoDTO = $secaoModeloRN->cadastrar($secaoModeloCabecalhoTextoDTO);

        //adicionando as se��es do modelo: T�tulo do Documento
        $this->logar('CRIANDO SE�AO DO MODELO - T�tulo do Documento');
        $secaoModeloRN = new SecaoModeloRN();

        $secaoModeloTituloTextoDTO = new SecaoModeloDTO();
        $secaoModeloTituloTextoDTO->retTodos();
        $secaoModeloTituloTextoDTO->setNumIdSecaoModelo(null);
        $secaoModeloTituloTextoDTO->setNumIdModelo($modeloDTO->getNumIdModelo());
        $secaoModeloTituloTextoDTO->setStrNome('T�tulo do Documento');

        $htmlTitulo = '<p style="font-size:13pt; font-family:Calibri; text-align:center; text-transform:uppercase; word-wrap:normal">@serie@ n&ordm; @numeracao_serie@</p>';

        $secaoModeloTituloTextoDTO->setStrConteudo($htmlTitulo);
        $secaoModeloTituloTextoDTO->setNumOrdem(10);
        $secaoModeloTituloTextoDTO->setStrSinCabecalho('N');
        $secaoModeloTituloTextoDTO->setStrSinRodape('N');
        $secaoModeloTituloTextoDTO->setStrSinPrincipal('N');
        $secaoModeloTituloTextoDTO->setStrSinAssinatura('N');
        $secaoModeloTituloTextoDTO->setStrSinSomenteLeitura('S');
        $secaoModeloTituloTextoDTO->setStrSinDinamica('S');
        $secaoModeloTituloTextoDTO->setStrSinHtml('S');
        $secaoModeloTituloTextoDTO->setStrSinAtivo('S');

        $secaoModeloCabecalhoTextoDTO = $secaoModeloRN->cadastrar($secaoModeloTituloTextoDTO);

        //adicionando as se��es do modelo: Corpo de Texto
        $this->logar('CRIANDO SE�AO DO MODELO - Corpo do Texto');
        $secaoModeloRN = new SecaoModeloRN();

        $secaoModeloCorpoTextoDTO = new SecaoModeloDTO();
        $secaoModeloCorpoTextoDTO->retTodos();
        $secaoModeloCorpoTextoDTO->setNumIdSecaoModelo(null);
        $secaoModeloCorpoTextoDTO->setNumIdModelo($modeloDTO->getNumIdModelo());
        $secaoModeloCorpoTextoDTO->setStrNome('Corpo do Texto');
        $secaoModeloCorpoTextoDTO->setStrConteudo(null);
        $secaoModeloCorpoTextoDTO->setNumOrdem(20);
        $secaoModeloCorpoTextoDTO->setStrSinCabecalho('N');
        $secaoModeloCorpoTextoDTO->setStrSinRodape('N');
        $secaoModeloCorpoTextoDTO->setStrSinPrincipal('S');
        $secaoModeloCorpoTextoDTO->setStrSinAssinatura('N');
        $secaoModeloCorpoTextoDTO->setStrSinSomenteLeitura('S');
        $secaoModeloCorpoTextoDTO->setStrSinDinamica('N');
        $secaoModeloCorpoTextoDTO->setStrSinHtml('N');
        $secaoModeloCorpoTextoDTO->setStrSinAtivo('S');

        $secaoModeloCorpoTextoDTO = $secaoModeloRN->cadastrar($secaoModeloCorpoTextoDTO);

        //adicionando as se��es do modelo: Assinatura
        $this->logar('CRIANDO SE�AO DO MODELO - Assinatura');
        $secaoModeloRN = new SecaoModeloRN();

        $secaoModeloAssinaturaTextoDTO = new SecaoModeloDTO();
        $secaoModeloAssinaturaTextoDTO->retTodos();
        $secaoModeloAssinaturaTextoDTO->setNumIdSecaoModelo(null);
        $secaoModeloAssinaturaTextoDTO->setNumIdModelo($modeloDTO->getNumIdModelo());
        $secaoModeloAssinaturaTextoDTO->setStrNome('Assinatura');
        $secaoModeloAssinaturaTextoDTO->setStrConteudo(null);
        $secaoModeloAssinaturaTextoDTO->setNumOrdem(30);
        $secaoModeloAssinaturaTextoDTO->setStrSinCabecalho('N');
        $secaoModeloAssinaturaTextoDTO->setStrSinRodape('N');
        $secaoModeloAssinaturaTextoDTO->setStrSinPrincipal('N');
        $secaoModeloAssinaturaTextoDTO->setStrSinAssinatura('S');
        $secaoModeloAssinaturaTextoDTO->setStrSinSomenteLeitura('N');
        $secaoModeloAssinaturaTextoDTO->setStrSinDinamica('N');
        $secaoModeloAssinaturaTextoDTO->setStrSinHtml('N');
        $secaoModeloAssinaturaTextoDTO->setStrSinAtivo('S');

        $secaoModeloCorpoTextoDTO = $secaoModeloRN->cadastrar($secaoModeloAssinaturaTextoDTO);

        //secao do rodap�
        $this->logar('CRIANDO SE�AO DO MODELO - Rodap�');
        $secaoModeloRodapeDTO = new SecaoModeloDTO();
        $secaoModeloRodapeDTO->retTodos();
        $secaoModeloRodapeDTO->setNumIdSecaoModelo(null);

        $htmlRodape = '<hr style="border:none; padding:0; margin:5px 2px 0 2px; border-top:medium double #333" />
    <table border="0" cellpadding="2" cellspacing="0" width="100%">
        <tbody>
            <tr>
                <td align="left" style="font-family:Calibri;font-size:9pt;border:0;" width="50%"><strong>Refer&ecirc;ncia:</strong> Processo n&ordm; @processo@</td>
            <td align="right" style="font-family:Calibri;font-size:9pt;border:0;" width="50%">SEI n&ordm; @documento@</td>
            </tr>
        </tbody>
    </table>
    ';

        $secaoModeloRodapeDTO->setNumIdModelo($modeloDTO->getNumIdModelo());
        $secaoModeloRodapeDTO->setStrNome('Rodap�');
        $secaoModeloRodapeDTO->setStrConteudo($htmlRodape);
        $secaoModeloRodapeDTO->setNumOrdem(1000);
        $secaoModeloRodapeDTO->setStrSinCabecalho('N');
        $secaoModeloRodapeDTO->setStrSinRodape('S');
        $secaoModeloRodapeDTO->setStrSinPrincipal('N');
        $secaoModeloRodapeDTO->setStrSinAssinatura('N');
        $secaoModeloRodapeDTO->setStrSinSomenteLeitura('S');
        $secaoModeloRodapeDTO->setStrSinDinamica('S');
        $secaoModeloRodapeDTO->setStrSinHtml('S');
        $secaoModeloRodapeDTO->setStrSinAtivo('S');

        $secaoModeloRodapeDTO = $secaoModeloRN->cadastrar($secaoModeloRodapeDTO);

        //Criar o Grupo de Tipo de Documento Internos do Sistema.
        $grupoSerieRN = new GrupoSerieRN();

        if (BancoSEI::getInstance() instanceof InfraMySql) {

            //verificando antes a situa�ao da tabela seq_grupo_serie
            $arrDados = BancoSEI::getInstance()->consultarSql('SELECT * FROM seq_grupo_serie ORDER BY id DESC LIMIT 1 ');

            $grupoSerieDTOLista = new GrupoSerieDTO();
            $grupoSerieDTOLista->retTodos();
            $grupoSerieDTOLista->setOrd("IdGrupoSerie", InfraDTO::$TIPO_ORDENACAO_DESC);
            $grupoSerieDTOLista->setNumMaxRegistrosRetorno(1);

            $arrListaGrupoSerie = $grupoSerieRN->listarRN0778($grupoSerieDTOLista);

            //ja tem registro na SEQ, insere apenas se ID da SEQ estiver incorreto
            if ($arrDados != null && count($arrDados) > 0) {

                if ($arrDados[0]['id'] < $arrListaGrupoSerie[0]->getNumIdGrupoSerie()) {

                    //INSERT para garantir a SEQ na posi�ao correta
                    BancoSEI::getInstance()->executarSql('INSERT INTO seq_grupo_serie ( id ) VALUES ( ' . $arrListaGrupoSerie[0]->getNumIdGrupoSerie() . ') ');
                }
            } //nao tem registro na SEQ ainda, colocar o ID do grupo_serie mais atual
            else {

                //INSERT para garantir a SEQ na posi�ao correta
                BancoSEI::getInstance()->executarSql('INSERT INTO seq_grupo_serie ( id ) VALUES ( ' . $arrListaGrupoSerie[0]->getNumIdGrupoSerie() . ') ');
            }
        }

        $this->logar('BUSCANDO GRUPO DE TIPO DE DOCUMENTO "Internos do Sistema"');
        $grupoSerieDTO = new GrupoSerieDTO();
        $grupoSerieDTO->retTodos();
        $grupoSerieDTO->setStrNome('Internos do Sistema');
        $grupoSerieDTO = $grupoSerieRN->consultarRN0777($grupoSerieDTO);

        $this->logar('CRIANDO TIPO DE DOCUMENTO Revoga��o de Procura��o Eletr�nica');
        $serieDTO = new SerieDTO();
        $serieDTO->retTodos();
        $serieRN = new SerieRN();

        $serieDTO->setNumIdSerie(null);
        $serieDTO->setNumIdGrupoSerie($grupoSerieDTO->getNumIdGrupoSerie());
        $serieDTO->setStrStaNumeracao(SerieRN::$TN_SEM_NUMERACAO);
        $serieDTO->setStrStaAplicabilidade(SerieRN::$TA_INTERNO);
        $serieDTO->setNumIdModeloEdoc(null);
        $serieDTO->setNumIdModelo($modeloDTO->getNumIdModelo());
        $serieDTO->setStrNome('Revoga��o de Procura��o Eletr�nica');
        $serieDTO->setStrDescricao('Utilizado para a gera��o autom�tica da Revoga��o de Procura��o Eletr�nica nos Peticionamentos Eletr�nicos realizados por Usu�rio Externo diretamente no Acesso Externo do SEI.');
        $serieDTO->setStrSinInteressado('S');
        $serieDTO->setStrSinDestinatario('N');
        $serieDTO->setStrSinAssinaturaPublicacao('S');
        $serieDTO->setStrSinInterno('S');
        $serieDTO->setStrSinAtivo('S');
        $serieDTO->setArrObjRelSerieAssuntoDTO(array());
        $serieDTO->setArrObjRelSerieVeiculoPublicacaoDTO(array());
        $serieDTO->setNumIdTipoFormulario(null);
        $serieDTO->setArrObjSerieRestricaoDTO(array());

        $serieDTO = $serieRN->cadastrarRN0642($serieDTO);

        $this->logar('ATUALIZANDO INFRA_PARAMETRO (' . MdPetIntSerieRN::$MD_PET_ID_SERIE_REVOGACAO . ')');
        $nomeParamIdSerie = MdPetIntSerieRN::$MD_PET_ID_SERIE_REVOGACAO;

        BancoSEI::getInstance()->executarSql('INSERT INTO infra_parametro ( valor, nome )  VALUES (\'' . $serieDTO->getNumIdSerie() . '\' , \'' . $nomeParamIdSerie . '\' ) ');
    }

    private function _gerarModeloRenuncia()
    {

        $this->logar('CRIANDO MODELO "Modulo_Peticionamento_Procuracao_Renuncia"');
        $modeloRN = new ModeloRN();
        $modeloDTO = new ModeloDTO();
        $modeloDTO->setNumIdModelo(null);
        $modeloDTO->retTodos();
        $modeloDTO->setStrNome('Modulo_Peticionamento_Procuracao_Renuncia');
        $modeloDTO->setStrSinAtivo('S');
        $modeloDTO = $modeloRN->cadastrar($modeloDTO);

        //adicionando as se��es do modelo: Cabe�alho
        $this->logar('CRIANDO SE�AO DO MODELO - Cabe�alho');
        $secaoModeloRN = new SecaoModeloRN();

        $secaoModeloCabecalhoTextoDTO = new SecaoModeloDTO();
        $secaoModeloCabecalhoTextoDTO->retTodos();
        $secaoModeloCabecalhoTextoDTO->setNumIdSecaoModelo(null);
        $secaoModeloCabecalhoTextoDTO->setNumIdModelo($modeloDTO->getNumIdModelo());
        $secaoModeloCabecalhoTextoDTO->setStrNome('Cabe�alho');

        $htmlCabecalho = '<div align="center">@timbre_orgao@</div>

<p style="font-weight:bold; font-size:13pt; font-family:Calibri; text-align:center; text-transform:uppercase; word-wrap:normal">@descricao_orgao_maiusculas@</p>

<p>&nbsp;&nbsp;</p>';

        $secaoModeloCabecalhoTextoDTO->setStrConteudo($htmlCabecalho);
        $secaoModeloCabecalhoTextoDTO->setNumOrdem(0);
        $secaoModeloCabecalhoTextoDTO->setStrSinCabecalho('S');
        $secaoModeloCabecalhoTextoDTO->setStrSinRodape('N');
        $secaoModeloCabecalhoTextoDTO->setStrSinPrincipal('N');
        $secaoModeloCabecalhoTextoDTO->setStrSinAssinatura('N');
        $secaoModeloCabecalhoTextoDTO->setStrSinSomenteLeitura('S');
        $secaoModeloCabecalhoTextoDTO->setStrSinDinamica('S');
        $secaoModeloCabecalhoTextoDTO->setStrSinHtml('S');
        $secaoModeloCabecalhoTextoDTO->setStrSinAtivo('S');

        $secaoModeloCabecalhoTextoDTO = $secaoModeloRN->cadastrar($secaoModeloCabecalhoTextoDTO);

        //adicionando as se��es do modelo: T�tulo do Documento
        $this->logar('CRIANDO SE�AO DO MODELO - T�tulo do Documento');
        $secaoModeloRN = new SecaoModeloRN();

        $secaoModeloTituloTextoDTO = new SecaoModeloDTO();
        $secaoModeloTituloTextoDTO->retTodos();
        $secaoModeloTituloTextoDTO->setNumIdSecaoModelo(null);
        $secaoModeloTituloTextoDTO->setNumIdModelo($modeloDTO->getNumIdModelo());
        $secaoModeloTituloTextoDTO->setStrNome('T�tulo do Documento');

        $htmlTitulo = '<p style="font-size:13pt; font-family:Calibri; text-align:center; text-transform:uppercase; word-wrap:normal">@serie@ n&ordm; @numeracao_serie@</p>';

        $secaoModeloTituloTextoDTO->setStrConteudo($htmlTitulo);
        $secaoModeloTituloTextoDTO->setNumOrdem(10);
        $secaoModeloTituloTextoDTO->setStrSinCabecalho('N');
        $secaoModeloTituloTextoDTO->setStrSinRodape('N');
        $secaoModeloTituloTextoDTO->setStrSinPrincipal('N');
        $secaoModeloTituloTextoDTO->setStrSinAssinatura('N');
        $secaoModeloTituloTextoDTO->setStrSinSomenteLeitura('S');
        $secaoModeloTituloTextoDTO->setStrSinDinamica('S');
        $secaoModeloTituloTextoDTO->setStrSinHtml('S');
        $secaoModeloTituloTextoDTO->setStrSinAtivo('S');

        $secaoModeloTituloTextoDTO = $secaoModeloRN->cadastrar($secaoModeloTituloTextoDTO);

        //adicionando as se��es do modelo: Corpo de Texto
        $this->logar('CRIANDO SE�AO DO MODELO - Corpo do Texto');
        $secaoModeloRN = new SecaoModeloRN();

        $secaoModeloCorpoTextoDTO = new SecaoModeloDTO();
        $secaoModeloCorpoTextoDTO->retTodos();
        $secaoModeloCorpoTextoDTO->setNumIdSecaoModelo(null);
        $secaoModeloCorpoTextoDTO->setNumIdModelo($modeloDTO->getNumIdModelo());
        $secaoModeloCorpoTextoDTO->setStrNome('Corpo do Texto');
        $secaoModeloCorpoTextoDTO->setStrConteudo(null);
        $secaoModeloCorpoTextoDTO->setNumOrdem(20);
        $secaoModeloCorpoTextoDTO->setStrSinCabecalho('N');
        $secaoModeloCorpoTextoDTO->setStrSinRodape('N');
        $secaoModeloCorpoTextoDTO->setStrSinPrincipal('S');
        $secaoModeloCorpoTextoDTO->setStrSinAssinatura('N');
        $secaoModeloCorpoTextoDTO->setStrSinSomenteLeitura('S');
        $secaoModeloCorpoTextoDTO->setStrSinDinamica('N');
        $secaoModeloCorpoTextoDTO->setStrSinHtml('N');
        $secaoModeloCorpoTextoDTO->setStrSinAtivo('S');

        $secaoModeloCorpoTextoDTO = $secaoModeloRN->cadastrar($secaoModeloCorpoTextoDTO);

        //adicionando as se��es do modelo: Assinatura
        $this->logar('CRIANDO SE�AO DO MODELO - Assinatura');
        $secaoModeloRN = new SecaoModeloRN();

        $secaoModeloAssinaturaTextoDTO = new SecaoModeloDTO();
        $secaoModeloAssinaturaTextoDTO->retTodos();
        $secaoModeloAssinaturaTextoDTO->setNumIdSecaoModelo(null);
        $secaoModeloAssinaturaTextoDTO->setNumIdModelo($modeloDTO->getNumIdModelo());
        $secaoModeloAssinaturaTextoDTO->setStrNome('Assinatura');
        $secaoModeloAssinaturaTextoDTO->setStrConteudo(null);
        $secaoModeloAssinaturaTextoDTO->setNumOrdem(30);
        $secaoModeloAssinaturaTextoDTO->setStrSinCabecalho('N');
        $secaoModeloAssinaturaTextoDTO->setStrSinRodape('N');
        $secaoModeloAssinaturaTextoDTO->setStrSinPrincipal('N');
        $secaoModeloAssinaturaTextoDTO->setStrSinAssinatura('S');
        $secaoModeloAssinaturaTextoDTO->setStrSinSomenteLeitura('N');
        $secaoModeloAssinaturaTextoDTO->setStrSinDinamica('N');
        $secaoModeloAssinaturaTextoDTO->setStrSinHtml('N');
        $secaoModeloAssinaturaTextoDTO->setStrSinAtivo('S');

        $secaoModeloAssinaturaTextoDTO = $secaoModeloRN->cadastrar($secaoModeloAssinaturaTextoDTO);

        //secao do rodap�
        $this->logar('CRIANDO SE�AO DO MODELO - Rodap�');
        $secaoModeloRodapeDTO = new SecaoModeloDTO();
        $secaoModeloRodapeDTO->retTodos();
        $secaoModeloRodapeDTO->setNumIdSecaoModelo(null);

        $htmlRodape = '<hr style="border:none; padding:0; margin:5px 2px 0 2px; border-top:medium double #333" />
    <table border="0" cellpadding="2" cellspacing="0" width="100%">
        <tbody>
            <tr>
                <td align="left" style="font-family:Calibri;font-size:9pt;border:0;" width="50%"><strong>Refer&ecirc;ncia:</strong> Processo n&ordm; @processo@</td>
                <td align="right" style="font-family:Calibri;font-size:9pt;border:0;" width="50%">SEI n&ordm; @documento@</td>
            </tr>
        </tbody>
    </table>
    ';

        $secaoModeloRodapeDTO->setNumIdModelo($modeloDTO->getNumIdModelo());
        $secaoModeloRodapeDTO->setStrNome('Rodap�');
        $secaoModeloRodapeDTO->setStrConteudo($htmlRodape);
        $secaoModeloRodapeDTO->setNumOrdem(1000);
        $secaoModeloRodapeDTO->setStrSinCabecalho('N');
        $secaoModeloRodapeDTO->setStrSinRodape('S');
        $secaoModeloRodapeDTO->setStrSinPrincipal('N');
        $secaoModeloRodapeDTO->setStrSinAssinatura('N');
        $secaoModeloRodapeDTO->setStrSinSomenteLeitura('S');
        $secaoModeloRodapeDTO->setStrSinDinamica('S');
        $secaoModeloRodapeDTO->setStrSinHtml('S');
        $secaoModeloRodapeDTO->setStrSinAtivo('S');

        $secaoModeloRodapeDTO = $secaoModeloRN->cadastrar($secaoModeloRodapeDTO);

        //Criar o Grupo de Tipo de Documento Internos do Sistema.
        $grupoSerieRN = new GrupoSerieRN();

        if (BancoSEI::getInstance() instanceof InfraMySql) {

            //verificando antes a situa�ao da tabela seq_grupo_serie
            $arrDados = BancoSEI::getInstance()->consultarSql('SELECT * FROM seq_grupo_serie ORDER BY id DESC LIMIT 1 ');

            $grupoSerieDTOLista = new GrupoSerieDTO();
            $grupoSerieDTOLista->retTodos();
            $grupoSerieDTOLista->setOrd("IdGrupoSerie", InfraDTO::$TIPO_ORDENACAO_DESC);
            $grupoSerieDTOLista->setNumMaxRegistrosRetorno(1);

            $arrListaGrupoSerie = $grupoSerieRN->listarRN0778($grupoSerieDTOLista);

            //ja tem registro na SEQ, insere apenas se ID da SEQ estiver incorreto
            if ($arrDados != null && count($arrDados) > 0) {

                if ($arrDados[0]['id'] < $arrListaGrupoSerie[0]->getNumIdGrupoSerie()) {

                    //INSERT para garantir a SEQ na posi�ao correta
                    BancoSEI::getInstance()->executarSql('INSERT INTO seq_grupo_serie ( id ) VALUES ( ' . $arrListaGrupoSerie[0]->getNumIdGrupoSerie() . ') ');
                }
            } //nao tem registro na SEQ ainda, colocar o ID do grupo_serie mais atual
            else {

                //INSERT para garantir a SEQ na posi�ao correta
                BancoSEI::getInstance()->executarSql('INSERT INTO seq_grupo_serie ( id ) VALUES ( ' . $arrListaGrupoSerie[0]->getNumIdGrupoSerie() . ') ');
            }
        }

        $this->logar('BUSCANDO GRUPO DE TIPO DE DOCUMENTO "Internos do Sistema"');
        $grupoSerieDTO = new GrupoSerieDTO();
        $grupoSerieDTO->retTodos();
        $grupoSerieDTO->setStrNome('Internos do Sistema');
        $grupoSerieDTO = $grupoSerieRN->consultarRN0777($grupoSerieDTO);

        $this->logar('CRIANDO TIPO DE DOCUMENTO Ren�ncia de Procura��o Eletr�nica');
        $serieDTO = new SerieDTO();
        $serieDTO->retTodos();
        $serieRN = new SerieRN();

        $serieDTO->setNumIdSerie(null);
        $serieDTO->setNumIdGrupoSerie($grupoSerieDTO->getNumIdGrupoSerie());
        $serieDTO->setStrStaNumeracao(SerieRN::$TN_SEM_NUMERACAO);
        $serieDTO->setStrStaAplicabilidade(SerieRN::$TA_INTERNO);
        $serieDTO->setNumIdModeloEdoc(null);
        $serieDTO->setNumIdModelo($modeloDTO->getNumIdModelo());
        $serieDTO->setStrNome('Ren�ncia de Procura��o Eletr�nica');
        $serieDTO->setStrDescricao('Utilizado para a gera��o autom�tica da Ren�ncia de Procura��o Eletr�nica nos Peticionamentos Eletr�nicos realizados por Usu�rio Externo diretamente no Acesso Externo do SEI.');
        $serieDTO->setStrSinInteressado('S');
        $serieDTO->setStrSinDestinatario('N');
        $serieDTO->setStrSinAssinaturaPublicacao('S');
        $serieDTO->setStrSinInterno('S');
        $serieDTO->setStrSinAtivo('S');
        $serieDTO->setArrObjRelSerieAssuntoDTO(array());
        $serieDTO->setArrObjRelSerieVeiculoPublicacaoDTO(array());
        $serieDTO->setNumIdTipoFormulario(null);
        $serieDTO->setArrObjSerieRestricaoDTO(array());

        $serieDTO = $serieRN->cadastrarRN0642($serieDTO);

        $this->logar('ATUALIZANDO INFRA_PARAMETRO (' . MdPetIntSerieRN::$MD_PET_ID_SERIE_RENUNCIA . ')');
        $nomeParamIdSerie = MdPetIntSerieRN::$MD_PET_ID_SERIE_RENUNCIA;

        BancoSEI::getInstance()->executarSql('INSERT INTO infra_parametro ( valor, nome )  VALUES (\'' . $serieDTO->getNumIdSerie() . '\' , \'' . $nomeParamIdSerie . '\' ) ');
    }

    private function _gerarModeloSuspenderVinculo()
    {

        $this->logar('CRIANDO MODELO "Modulo_Peticionamento_Vinc_Suspender"');
        $modeloRN = new ModeloRN();
        $modeloDTO = new ModeloDTO();
        $modeloDTO->setNumIdModelo(null);
        $modeloDTO->retTodos();
        $modeloDTO->setStrNome('Modulo_Peticionamento_Vinc_Suspender');
        $modeloDTO->setStrSinAtivo('S');
        $modeloDTO = $modeloRN->cadastrar($modeloDTO);

        //adicionando as se��es do modelo: Cabe�alho
        $this->logar('CRIANDO SE�AO DO MODELO - Cabe�alho');
        $secaoModeloRN = new SecaoModeloRN();

        $secaoModeloCabecalhoTextoDTO = new SecaoModeloDTO();
        $secaoModeloCabecalhoTextoDTO->retTodos();
        $secaoModeloCabecalhoTextoDTO->setNumIdSecaoModelo(null);
        $secaoModeloCabecalhoTextoDTO->setNumIdModelo($modeloDTO->getNumIdModelo());
        $secaoModeloCabecalhoTextoDTO->setStrNome('Cabe�alho');

        $htmlCabecalho = '<div align="center">@timbre_orgao@</div>

<p style="font-weight:bold; font-size:13pt; font-family:Calibri; text-align:center; text-transform:uppercase; word-wrap:normal">@descricao_orgao_maiusculas@</p>

<p>&nbsp;&nbsp;</p>';

        $secaoModeloCabecalhoTextoDTO->setStrConteudo($htmlCabecalho);
        $secaoModeloCabecalhoTextoDTO->setNumOrdem(0);
        $secaoModeloCabecalhoTextoDTO->setStrSinCabecalho('S');
        $secaoModeloCabecalhoTextoDTO->setStrSinRodape('N');
        $secaoModeloCabecalhoTextoDTO->setStrSinPrincipal('N');
        $secaoModeloCabecalhoTextoDTO->setStrSinAssinatura('N');
        $secaoModeloCabecalhoTextoDTO->setStrSinSomenteLeitura('S');
        $secaoModeloCabecalhoTextoDTO->setStrSinDinamica('S');
        $secaoModeloCabecalhoTextoDTO->setStrSinHtml('S');
        $secaoModeloCabecalhoTextoDTO->setStrSinAtivo('S');

        $secaoModeloCabecalhoTextoDTO = $secaoModeloRN->cadastrar($secaoModeloCabecalhoTextoDTO);

        //adicionando as se��es do modelo: T�tulo do Documento
        $this->logar('CRIANDO SE�AO DO MODELO - T�tulo do Documento');
        $secaoModeloRN = new SecaoModeloRN();

        $secaoModeloTituloTextoDTO = new SecaoModeloDTO();
        $secaoModeloTituloTextoDTO->retTodos();
        $secaoModeloTituloTextoDTO->setNumIdSecaoModelo(null);
        $secaoModeloTituloTextoDTO->setNumIdModelo($modeloDTO->getNumIdModelo());
        $secaoModeloTituloTextoDTO->setStrNome('T�tulo do Documento');

        $htmlTitulo = '<p style="font-size:13pt; font-family:Calibri; text-align:center; text-transform:uppercase; word-wrap:normal">@serie@ n&ordm; @numeracao_serie@</p>';

        $secaoModeloTituloTextoDTO->setStrConteudo($htmlTitulo);
        $secaoModeloTituloTextoDTO->setNumOrdem(10);
        $secaoModeloTituloTextoDTO->setStrSinCabecalho('N');
        $secaoModeloTituloTextoDTO->setStrSinRodape('N');
        $secaoModeloTituloTextoDTO->setStrSinPrincipal('N');
        $secaoModeloTituloTextoDTO->setStrSinAssinatura('N');
        $secaoModeloTituloTextoDTO->setStrSinSomenteLeitura('S');
        $secaoModeloTituloTextoDTO->setStrSinDinamica('S');
        $secaoModeloTituloTextoDTO->setStrSinHtml('S');
        $secaoModeloTituloTextoDTO->setStrSinAtivo('S');

        $secaoModeloCabecalhoTextoDTO = $secaoModeloRN->cadastrar($secaoModeloTituloTextoDTO);

        //adicionando as se��es do modelo: Corpo de Texto
        $this->logar('CRIANDO SE�AO DO MODELO - Corpo do Texto');
        $secaoModeloRN = new SecaoModeloRN();

        $secaoModeloCorpoTextoDTO = new SecaoModeloDTO();
        $secaoModeloCorpoTextoDTO->retTodos();
        $secaoModeloCorpoTextoDTO->setNumIdSecaoModelo(null);
        $secaoModeloCorpoTextoDTO->setNumIdModelo($modeloDTO->getNumIdModelo());
        $secaoModeloCorpoTextoDTO->setStrNome('Corpo do Texto');
        $secaoModeloCorpoTextoDTO->setStrConteudo(null);
        $secaoModeloCorpoTextoDTO->setNumOrdem(20);
        $secaoModeloCorpoTextoDTO->setStrSinCabecalho('N');
        $secaoModeloCorpoTextoDTO->setStrSinRodape('N');
        $secaoModeloCorpoTextoDTO->setStrSinPrincipal('S');
        $secaoModeloCorpoTextoDTO->setStrSinAssinatura('N');
        $secaoModeloCorpoTextoDTO->setStrSinSomenteLeitura('S');
        $secaoModeloCorpoTextoDTO->setStrSinDinamica('N');
        $secaoModeloCorpoTextoDTO->setStrSinHtml('N');
        $secaoModeloCorpoTextoDTO->setStrSinAtivo('S');

        $secaoModeloCorpoTextoDTO = $secaoModeloRN->cadastrar($secaoModeloCorpoTextoDTO);

        //adicionando as se��es do modelo: Assinatura
        $this->logar('CRIANDO SE�AO DO MODELO - Assinatura');
        $secaoModeloRN = new SecaoModeloRN();

        $secaoModeloAssinaturaDTO = new SecaoModeloDTO();
        $secaoModeloAssinaturaDTO->retTodos();
        $secaoModeloAssinaturaDTO->setNumIdSecaoModelo(null);
        $secaoModeloAssinaturaDTO->setNumIdModelo($modeloDTO->getNumIdModelo());
        $secaoModeloAssinaturaDTO->setStrNome('Assinatura');
        $secaoModeloAssinaturaDTO->setStrConteudo(null);
        $secaoModeloAssinaturaDTO->setNumOrdem(30);
        $secaoModeloAssinaturaDTO->setStrSinCabecalho('N');
        $secaoModeloAssinaturaDTO->setStrSinRodape('N');
        $secaoModeloAssinaturaDTO->setStrSinPrincipal('N');
        $secaoModeloAssinaturaDTO->setStrSinAssinatura('S');
        $secaoModeloAssinaturaDTO->setStrSinSomenteLeitura('N');
        $secaoModeloAssinaturaDTO->setStrSinDinamica('N');
        $secaoModeloAssinaturaDTO->setStrSinHtml('N');
        $secaoModeloAssinaturaDTO->setStrSinAtivo('S');

        $secaoModeloCorpoTextoDTO = $secaoModeloRN->cadastrar($secaoModeloAssinaturaDTO);

        //secao do rodap�
        $this->logar('CRIANDO SE�AO DO MODELO - Rodap�');
        $secaoModeloRodapeDTO = new SecaoModeloDTO();
        $secaoModeloRodapeDTO->retTodos();
        $secaoModeloRodapeDTO->setNumIdSecaoModelo(null);

        $htmlRodape = '<hr style="border:none; padding:0; margin:5px 2px 0 2px; border-top:medium double #333" />
    <table border="0" cellpadding="2" cellspacing="0" width="100%">
        <tbody>
            <tr>
                <td align="left" style="font-family:Calibri;font-size:9pt;border:0;" width="50%"><strong>Refer&ecirc;ncia:</strong> Processo n&ordm; @processo@</td>
                <td align="right" style="font-family:Calibri;font-size:9pt;border:0;" width="50%">SEI n&ordm; @documento@</td>
            </tr>
        </tbody>
    </table>
    ';

        $secaoModeloRodapeDTO->setNumIdModelo($modeloDTO->getNumIdModelo());
        $secaoModeloRodapeDTO->setStrNome('Rodap�');
        $secaoModeloRodapeDTO->setStrConteudo($htmlRodape);
        $secaoModeloRodapeDTO->setNumOrdem(1000);
        $secaoModeloRodapeDTO->setStrSinCabecalho('N');
        $secaoModeloRodapeDTO->setStrSinRodape('S');
        $secaoModeloRodapeDTO->setStrSinPrincipal('N');
        $secaoModeloRodapeDTO->setStrSinAssinatura('N');
        $secaoModeloRodapeDTO->setStrSinSomenteLeitura('S');
        $secaoModeloRodapeDTO->setStrSinDinamica('S');
        $secaoModeloRodapeDTO->setStrSinHtml('S');
        $secaoModeloRodapeDTO->setStrSinAtivo('S');

        $secaoModeloRodapeDTO = $secaoModeloRN->cadastrar($secaoModeloRodapeDTO);

        //Criar o Grupo de Tipo de Documento Internos do Sistema.
        $grupoSerieRN = new GrupoSerieRN();

        if (BancoSEI::getInstance() instanceof InfraMySql) {

            //verificando antes a situa�ao da tabela seq_grupo_serie
            $arrDados = BancoSEI::getInstance()->consultarSql('SELECT * FROM seq_grupo_serie ORDER BY id DESC LIMIT 1 ');

            $grupoSerieDTOLista = new GrupoSerieDTO();
            $grupoSerieDTOLista->retTodos();
            $grupoSerieDTOLista->setOrd("IdGrupoSerie", InfraDTO::$TIPO_ORDENACAO_DESC);
            $grupoSerieDTOLista->setNumMaxRegistrosRetorno(1);

            $arrListaGrupoSerie = $grupoSerieRN->listarRN0778($grupoSerieDTOLista);

            //ja tem registro na SEQ, insere apenas se ID da SEQ estiver incorreto
            if ($arrDados != null && count($arrDados) > 0) {

                if ($arrDados[0]['id'] < $arrListaGrupoSerie[0]->getNumIdGrupoSerie()) {

                    //INSERT para garantir a SEQ na posi�ao correta
                    BancoSEI::getInstance()->executarSql('INSERT INTO seq_grupo_serie ( id ) VALUES ( ' . $arrListaGrupoSerie[0]->getNumIdGrupoSerie() . ') ');
                }
            } //nao tem registro na SEQ ainda, colocar o ID do grupo_serie mais atual
            else {

                //INSERT para garantir a SEQ na posi�ao correta
                BancoSEI::getInstance()->executarSql('INSERT INTO seq_grupo_serie ( id ) VALUES ( ' . $arrListaGrupoSerie[0]->getNumIdGrupoSerie() . ') ');
            }
        }

        $this->logar('BUSCANDO GRUPO DE TIPO DE DOCUMENTO "Internos do Sistema"');
        $grupoSerieDTO = new GrupoSerieDTO();
        $grupoSerieDTO->retTodos();
        $grupoSerieDTO->setStrNome('Internos do Sistema');
        $grupoSerieDTO = $grupoSerieRN->consultarRN0777($grupoSerieDTO);

        $this->logar('CRIANDO TIPO DE DOCUMENTO Suspens�o de Vincula��o a Pessoa Jur�dica');
        $serieDTO = new SerieDTO();
        $serieDTO->retTodos();
        $serieRN = new SerieRN();

        $serieDTO->setNumIdSerie(null);
        $serieDTO->setNumIdGrupoSerie($grupoSerieDTO->getNumIdGrupoSerie());
        $serieDTO->setStrStaNumeracao(SerieRN::$TN_SEM_NUMERACAO);
        $serieDTO->setStrStaAplicabilidade(SerieRN::$TA_INTERNO);
        $serieDTO->setNumIdModeloEdoc(null);
        $serieDTO->setNumIdModelo($modeloDTO->getNumIdModelo());
        $serieDTO->setStrNome('Suspens�o de Vincula��o a Pessoa Jur�dica');
        $serieDTO->setStrDescricao('Utilizado para a gera��o autom�tica da Suspens�o de Vincula��o a Pessoa Jur�dica por Usu�rio Externo diretamente no Acesso Externo do SEI.');
        $serieDTO->setStrSinInteressado('S');
        $serieDTO->setStrSinDestinatario('N');
        $serieDTO->setStrSinAssinaturaPublicacao('S');
        $serieDTO->setStrSinInterno('S');
        $serieDTO->setStrSinAtivo('S');
        $serieDTO->setArrObjRelSerieAssuntoDTO(array());
        $serieDTO->setArrObjRelSerieVeiculoPublicacaoDTO(array());
        $serieDTO->setNumIdTipoFormulario(null);
        $serieDTO->setArrObjSerieRestricaoDTO(array());

        $serieDTO = $serieRN->cadastrarRN0642($serieDTO);

        $this->logar('ATUALIZANDO INFRA_PARAMETRO (' . MdPetIntSerieRN::$MD_PET_ID_SERIE_VINC_SUSPENSAO . ')');
        $nomeParamIdSerie = MdPetIntSerieRN::$MD_PET_ID_SERIE_VINC_SUSPENSAO;

        BancoSEI::getInstance()->executarSql('INSERT INTO infra_parametro ( valor, nome )  VALUES (\'' . $serieDTO->getNumIdSerie() . '\' , \'' . $nomeParamIdSerie . '\' ) ');
    }

    private function _gerarModeloRestabelecerVinculo()
    {

        $this->logar('CRIANDO MODELO "Modulo_Peticionamento_Vinc_Restabelecer"');
        $modeloRN = new ModeloRN();
        $modeloDTO = new ModeloDTO();
        $modeloDTO->setNumIdModelo(null);
        $modeloDTO->retTodos();
        $modeloDTO->setStrNome('Modulo_Peticionamento_Vinc_Restabelecer');
        $modeloDTO->setStrSinAtivo('S');
        $modeloDTO = $modeloRN->cadastrar($modeloDTO);

        //adicionando as se��es do modelo: Cabe�alho
        $this->logar('CRIANDO SE�AO DO MODELO - Cabe�alho');
        $secaoModeloRN = new SecaoModeloRN();

        $secaoModeloCabecalhoTextoDTO = new SecaoModeloDTO();
        $secaoModeloCabecalhoTextoDTO->retTodos();
        $secaoModeloCabecalhoTextoDTO->setNumIdSecaoModelo(null);
        $secaoModeloCabecalhoTextoDTO->setNumIdModelo($modeloDTO->getNumIdModelo());
        $secaoModeloCabecalhoTextoDTO->setStrNome('Cabe�alho');

        $htmlCabecalho = '<div align="center">@timbre_orgao@</div>

<p style="font-weight:bold; font-size:13pt; font-family:Calibri; text-align:center; text-transform:uppercase; word-wrap:normal">@descricao_orgao_maiusculas@</p>

<p>&nbsp;&nbsp;</p>';

        $secaoModeloCabecalhoTextoDTO->setStrConteudo($htmlCabecalho);
        $secaoModeloCabecalhoTextoDTO->setNumOrdem(0);
        $secaoModeloCabecalhoTextoDTO->setStrSinCabecalho('S');
        $secaoModeloCabecalhoTextoDTO->setStrSinRodape('N');
        $secaoModeloCabecalhoTextoDTO->setStrSinPrincipal('N');
        $secaoModeloCabecalhoTextoDTO->setStrSinAssinatura('N');
        $secaoModeloCabecalhoTextoDTO->setStrSinSomenteLeitura('S');
        $secaoModeloCabecalhoTextoDTO->setStrSinDinamica('S');
        $secaoModeloCabecalhoTextoDTO->setStrSinHtml('S');
        $secaoModeloCabecalhoTextoDTO->setStrSinAtivo('S');

        $secaoModeloCabecalhoTextoDTO = $secaoModeloRN->cadastrar($secaoModeloCabecalhoTextoDTO);

        //adicionando as se��es do modelo: T�tulo do Documento
        $this->logar('CRIANDO SE�AO DO MODELO - T�tulo do Documento');
        $secaoModeloRN = new SecaoModeloRN();

        $secaoModeloTituloTextoDTO = new SecaoModeloDTO();
        $secaoModeloTituloTextoDTO->retTodos();
        $secaoModeloTituloTextoDTO->setNumIdSecaoModelo(null);
        $secaoModeloTituloTextoDTO->setNumIdModelo($modeloDTO->getNumIdModelo());
        $secaoModeloTituloTextoDTO->setStrNome('T�tulo do Documento');

        $htmlTitulo = '<p style="font-size:13pt; font-family:Calibri; text-align:center; text-transform:uppercase; word-wrap:normal">@serie@ n&ordm; @numeracao_serie@</p>';

        $secaoModeloTituloTextoDTO->setStrConteudo($htmlTitulo);
        $secaoModeloTituloTextoDTO->setNumOrdem(10);
        $secaoModeloTituloTextoDTO->setStrSinCabecalho('N');
        $secaoModeloTituloTextoDTO->setStrSinRodape('N');
        $secaoModeloTituloTextoDTO->setStrSinPrincipal('N');
        $secaoModeloTituloTextoDTO->setStrSinAssinatura('N');
        $secaoModeloTituloTextoDTO->setStrSinSomenteLeitura('S');
        $secaoModeloTituloTextoDTO->setStrSinDinamica('S');
        $secaoModeloTituloTextoDTO->setStrSinHtml('S');
        $secaoModeloTituloTextoDTO->setStrSinAtivo('S');

        $secaoModeloCabecalhoTextoDTO = $secaoModeloRN->cadastrar($secaoModeloTituloTextoDTO);

        //adicionando as se��es do modelo: Corpo de Texto
        $this->logar('CRIANDO SE�AO DO MODELO - Corpo do Texto');
        $secaoModeloRN = new SecaoModeloRN();

        $secaoModeloCorpoTextoDTO = new SecaoModeloDTO();
        $secaoModeloCorpoTextoDTO->retTodos();
        $secaoModeloCorpoTextoDTO->setNumIdSecaoModelo(null);
        $secaoModeloCorpoTextoDTO->setNumIdModelo($modeloDTO->getNumIdModelo());
        $secaoModeloCorpoTextoDTO->setStrNome('Corpo do Texto');
        $secaoModeloCorpoTextoDTO->setStrConteudo(null);
        $secaoModeloCorpoTextoDTO->setNumOrdem(20);
        $secaoModeloCorpoTextoDTO->setStrSinCabecalho('N');
        $secaoModeloCorpoTextoDTO->setStrSinRodape('N');
        $secaoModeloCorpoTextoDTO->setStrSinPrincipal('S');
        $secaoModeloCorpoTextoDTO->setStrSinAssinatura('N');
        $secaoModeloCorpoTextoDTO->setStrSinSomenteLeitura('S');
        $secaoModeloCorpoTextoDTO->setStrSinDinamica('N');
        $secaoModeloCorpoTextoDTO->setStrSinHtml('N');
        $secaoModeloCorpoTextoDTO->setStrSinAtivo('S');

        $secaoModeloCorpoTextoDTO = $secaoModeloRN->cadastrar($secaoModeloCorpoTextoDTO);

        //adicionando as se��es do modelo: Assinatura
        $this->logar('CRIANDO SE�AO DO MODELO - Assinatura');
        $secaoModeloRN = new SecaoModeloRN();

        $secaoModeloAssinaturaDTO = new SecaoModeloDTO();
        $secaoModeloAssinaturaDTO->retTodos();
        $secaoModeloAssinaturaDTO->setNumIdSecaoModelo(null);
        $secaoModeloAssinaturaDTO->setNumIdModelo($modeloDTO->getNumIdModelo());
        $secaoModeloAssinaturaDTO->setStrNome('Assinatura');
        $secaoModeloAssinaturaDTO->setStrConteudo(null);
        $secaoModeloAssinaturaDTO->setNumOrdem(30);
        $secaoModeloAssinaturaDTO->setStrSinCabecalho('N');
        $secaoModeloAssinaturaDTO->setStrSinRodape('N');
        $secaoModeloAssinaturaDTO->setStrSinPrincipal('N');
        $secaoModeloAssinaturaDTO->setStrSinAssinatura('S');
        $secaoModeloAssinaturaDTO->setStrSinSomenteLeitura('N');
        $secaoModeloAssinaturaDTO->setStrSinDinamica('N');
        $secaoModeloAssinaturaDTO->setStrSinHtml('N');
        $secaoModeloAssinaturaDTO->setStrSinAtivo('S');

        $secaoModeloCorpoTextoDTO = $secaoModeloRN->cadastrar($secaoModeloAssinaturaDTO);

        //secao do rodap�
        $this->logar('CRIANDO SE�AO DO MODELO - Rodap�');
        $secaoModeloRodapeDTO = new SecaoModeloDTO();
        $secaoModeloRodapeDTO->retTodos();
        $secaoModeloRodapeDTO->setNumIdSecaoModelo(null);

        $htmlRodape = '<hr style="border:none; padding:0; margin:5px 2px 0 2px; border-top:medium double #333" />
    <table border="0" cellpadding="2" cellspacing="0" width="100%">
        <tbody>
            <tr>
                <td align="left" style="font-family:Calibri;font-size:9pt;border:0;" width="50%"><strong>Refer&ecirc;ncia:</strong> Processo n&ordm; @processo@</td>
                <td align="right" style="font-family:Calibri;font-size:9pt;border:0;" width="50%">SEI n&ordm; @documento@</td>
            </tr>
        </tbody>
    </table>
    ';

        $secaoModeloRodapeDTO->setNumIdModelo($modeloDTO->getNumIdModelo());
        $secaoModeloRodapeDTO->setStrNome('Rodap�');
        $secaoModeloRodapeDTO->setStrConteudo($htmlRodape);
        $secaoModeloRodapeDTO->setNumOrdem(1000);
        $secaoModeloRodapeDTO->setStrSinCabecalho('N');
        $secaoModeloRodapeDTO->setStrSinRodape('S');
        $secaoModeloRodapeDTO->setStrSinPrincipal('N');
        $secaoModeloRodapeDTO->setStrSinAssinatura('N');
        $secaoModeloRodapeDTO->setStrSinSomenteLeitura('S');
        $secaoModeloRodapeDTO->setStrSinDinamica('S');
        $secaoModeloRodapeDTO->setStrSinHtml('S');
        $secaoModeloRodapeDTO->setStrSinAtivo('S');

        $secaoModeloRodapeDTO = $secaoModeloRN->cadastrar($secaoModeloRodapeDTO);

        //Criar o Grupo de Tipo de Documento Internos do Sistema.
        $grupoSerieRN = new GrupoSerieRN();

        if (BancoSEI::getInstance() instanceof InfraMySql) {

            //verificando antes a situa�ao da tabela seq_grupo_serie
            $arrDados = BancoSEI::getInstance()->consultarSql('SELECT * FROM seq_grupo_serie ORDER BY id DESC LIMIT 1 ');

            $grupoSerieDTOLista = new GrupoSerieDTO();
            $grupoSerieDTOLista->retTodos();
            $grupoSerieDTOLista->setOrd("IdGrupoSerie", InfraDTO::$TIPO_ORDENACAO_DESC);
            $grupoSerieDTOLista->setNumMaxRegistrosRetorno(1);

            $arrListaGrupoSerie = $grupoSerieRN->listarRN0778($grupoSerieDTOLista);

            //ja tem registro na SEQ, insere apenas se ID da SEQ estiver incorreto
            if ($arrDados != null && count($arrDados) > 0) {

                if ($arrDados[0]['id'] < $arrListaGrupoSerie[0]->getNumIdGrupoSerie()) {

                    //INSERT para garantir a SEQ na posi�ao correta
                    BancoSEI::getInstance()->executarSql('INSERT INTO seq_grupo_serie ( id ) VALUES ( ' . $arrListaGrupoSerie[0]->getNumIdGrupoSerie() . ') ');
                }
            } //nao tem registro na SEQ ainda, colocar o ID do grupo_serie mais atual
            else {

                //INSERT para garantir a SEQ na posi�ao correta
                BancoSEI::getInstance()->executarSql('INSERT INTO seq_grupo_serie ( id ) VALUES ( ' . $arrListaGrupoSerie[0]->getNumIdGrupoSerie() . ') ');
            }
        }

        $this->logar('BUSCANDO GRUPO DE TIPO DE DOCUMENTO "Internos do Sistema"');
        $grupoSerieDTO = new GrupoSerieDTO();
        $grupoSerieDTO->retTodos();
        $grupoSerieDTO->setStrNome('Internos do Sistema');
        $grupoSerieDTO = $grupoSerieRN->consultarRN0777($grupoSerieDTO);

        $this->logar('CRIANDO TIPO DE DOCUMENTO Restabelecimento de Vincula��o a Pessoa Jur�dica');
        $serieDTO = new SerieDTO();
        $serieDTO->retTodos();
        $serieRN = new SerieRN();

        $serieDTO->setNumIdSerie(null);
        $serieDTO->setNumIdGrupoSerie($grupoSerieDTO->getNumIdGrupoSerie());
        $serieDTO->setStrStaNumeracao(SerieRN::$TN_SEM_NUMERACAO);
        $serieDTO->setStrStaAplicabilidade(SerieRN::$TA_INTERNO);
        $serieDTO->setNumIdModeloEdoc(null);
        $serieDTO->setNumIdModelo($modeloDTO->getNumIdModelo());
        $serieDTO->setStrNome('Restabelecimento de Vincula��o a Pessoa Jur�dica');
        $serieDTO->setStrDescricao('Utilizado para a gera��o autom�tica do Restabelecimento de Vincula��o a Pessoa Jur�dica por Usu�rio Externo diretamente no Acesso Externo do SEI.');
        $serieDTO->setStrSinInteressado('S');
        $serieDTO->setStrSinDestinatario('N');
        $serieDTO->setStrSinAssinaturaPublicacao('S');
        $serieDTO->setStrSinInterno('S');
        $serieDTO->setStrSinAtivo('S');
        $serieDTO->setArrObjRelSerieAssuntoDTO(array());
        $serieDTO->setArrObjRelSerieVeiculoPublicacaoDTO(array());
        $serieDTO->setNumIdTipoFormulario(null);
        $serieDTO->setArrObjSerieRestricaoDTO(array());

        $serieDTO = $serieRN->cadastrarRN0642($serieDTO);

        $this->logar('ATUALIZANDO INFRA_PARAMETRO (' . MdPetIntSerieRN::$MD_PET_ID_SERIE_VINC_RESTABELECIMENTO . ')');
        $nomeParamIdSerie = MdPetIntSerieRN::$MD_PET_ID_SERIE_VINC_RESTABELECIMENTO;

        BancoSEI::getInstance()->executarSql('INSERT INTO infra_parametro ( valor, nome )  VALUES (\'' . $serieDTO->getNumIdSerie() . '\' , \'' . $nomeParamIdSerie . '\' ) ');
    }

    protected function fixIndices(InfraMetaBD $objInfraMetaBD, $arrTabelas)
    {
        InfraDebug::getInstance()->setBolDebugInfra(true);

        $this->logar('ATUALIZANDO INDICES...');

        $objInfraMetaBD->processarIndicesChavesEstrangeiras($arrTabelas);

        InfraDebug::getInstance()->setBolDebugInfra(false);
    }

}

try {
    SessaoSEI::getInstance(false);
    BancoSEI::getInstance()->setBolScript(true);

    if (!ConfiguracaoSEI::getInstance()->isSetValor('BancoSEI', 'UsuarioScript')) {
        throw new InfraException('Chave BancoSEI/UsuarioScript n�o encontrada.');
    }

    if (InfraString::isBolVazia(ConfiguracaoSEI::getInstance()->getValor('BancoSEI', 'UsuarioScript'))) {
        throw new InfraException('Chave BancoSEI/UsuarioScript n�o possui valor.');
    }

    if (!ConfiguracaoSEI::getInstance()->isSetValor('BancoSEI', 'SenhaScript')) {
        throw new InfraException('Chave BancoSEI/SenhaScript n�o encontrada.');
    }

    if (InfraString::isBolVazia(ConfiguracaoSEI::getInstance()->getValor('BancoSEI', 'SenhaScript'))) {
        throw new InfraException('Chave BancoSEI/SenhaScript n�o possui valor.');
    }

    $configuracaoSEI = new ConfiguracaoSEI();
    $arrConfig = $configuracaoSEI->getInstance()->getArrConfiguracoes();

    if (!isset($arrConfig['SEI']['Modulos'])) {
        throw new InfraException('PAR�METROS DE M�DULOS NO CONFIGURA��O DO SEI N�O DECLARADO');
    } else {
        $arrModulos = $arrConfig['SEI']['Modulos'];
        if (!key_exists('PeticionamentoIntegracao', $arrModulos)) {
            throw new InfraException('M�DULO DO PETICIONAMENTO ELETR�NICO N�O DECLARADO NA CONFIGURA��O DO SEI');
        }
    }

    if (!class_exists('PeticionamentoIntegracao')) {
        throw new InfraException('A CLASSE PRINCIPAL "PETICIONAMENTOINTEGRACAO" DO M�DULO DO PETICIONAMENTO ELETR�NICO N�O ENCONTRADA');
    }

    $objVersaoSeiRN = new MdPetAtualizadorSeiRN();
    $objVersaoSeiRN->atualizarVersao();
    exit;

} catch (Exception $e) {
    echo(InfraException::inspecionar($e));
    try {
        LogSEI::getInstance()->gravar(InfraException::inspecionar($e));
    } catch (Exception $e) {
    }
    exit(1);
}
?>