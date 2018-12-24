<?
/**
 * ANATEL
 *
 * 21/05/2016 - criado por marcelo.bezerra - CAST
 *
 */

require_once dirname(__FILE__) . '/../../../SEI.php';

class MdPetAtualizadorSeiRN extends InfraRN {

    private $numSeg = 0;
    private $versaoAtualDesteModulo = '2.0.2';
    private $nomeDesteModulo = 'MÓDULO DE PETICIONAMENTO E INTIMAÇÃO ELETRÔNICOS';
    private $nomeParametroModulo = 'VERSAO_MODULO_PETICIONAMENTO';
    private $historicoVersoes = array('0.0.1', '0.0.2', '1.0.3', '1.0.4', '1.1.0', '2.0.0', '2.0.1', '2.0.2');

    public static $MD_PET_ID_SERIE_RECIBO = 'MODULO_PETICIONAMENTO_ID_SERIE_RECIBO_PETICIONAMENTO';

    public function __construct(){
        parent::__construct();
    }

    protected function inicializarObjInfraIBanco(){
        return BancoSEI::getInstance();
    }

    private function inicializar($strTitulo){
        ini_set('max_execution_time', '0');
        ini_set('memory_limit', '-1');

        try {
            @ini_set('zlib.output_compression', '0');
            @ini_set('implicit_flush', '1');
        } catch (Exception $e) {
        }

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
            $this->logar('TEMPO TOTAL DE EXECUÇÃO: '.$this->numSeg.' s');
        } else {
            $strMsg = 'ERRO: '.$strMsg;
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

    protected function atualizarVersaoConectado(){

        try {
            $this->inicializar('INICIANDO A INSTALAÇÃO/ATUALIZAÇÃO DO '.$this->nomeDesteModulo.' NO SEI VERSÃO '.SEI_VERSAO);

            //testando versao do framework
            $numVersaoInfraRequerida = '1.493';
            $versaoInfraFormatada = (int) str_replace('.','', VERSAO_INFRA);
            $versaoInfraReqFormatada = (int) str_replace('.','', $numVersaoInfraRequerida);

            if ($versaoInfraFormatada < $versaoInfraReqFormatada){
                $this->finalizar('VERSÃO DO FRAMEWORK PHP INCOMPATÍVEL (VERSÃO ATUAL '.VERSAO_INFRA.', SENDO REQUERIDA VERSÃO IGUAL OU SUPERIOR A '.$numVersaoInfraRequerida.')',true);
            }

            //checando BDs suportados
            if (!(BancoSEI::getInstance() instanceof InfraMySql) &&
                !(BancoSEI::getInstance() instanceof InfraSqlServer) &&
                !(BancoSEI::getInstance() instanceof InfraOracle)) {
                    $this->finalizar('BANCO DE DADOS NÃO SUPORTADO: ' . get_parent_class(BancoSEI::getInstance()), true);
            }

            //checando permissoes na base de dados
            $objInfraMetaBD = new InfraMetaBD(BancoSEI::getInstance());

            if (count($objInfraMetaBD->obterTabelas('sei_teste')) == 0) {
                BancoSEI::getInstance()->executarSql('CREATE TABLE sei_teste (id ' . $objInfraMetaBD->tipoNumero() . ' null)');
            }

            BancoSEI::getInstance()->executarSql('DROP TABLE sei_teste');

            $objInfraParametro = new InfraParametro(BancoSEI::getInstance());

            $strVersaoModuloPeticionamento = $objInfraParametro->getValor($this->nomeParametroModulo, false);

            //VERIFICANDO QUAL VERSAO DEVE SER INSTALADA NESTA EXECUCAO
            if (InfraString::isBolVazia($strVersaoModuloPeticionamento)) {
                $this->instalarv001();
                $this->instalarv002();
                $this->instalarv100();
                $this->instalarv104();
                $this->instalarv110();
                $this->instalarv200();
                $this->instalarv201();
                $this->instalarv202();
                $this->logar('INSTALAÇÃO/ATUALIZAÇÃO DA VERSÃO '.$this->versaoAtualDesteModulo.' DO '.$this->nomeDesteModulo.' REALIZADA COM SUCESSO NA BASE DO SEI');
                $this->finalizar('FIM', false);
            } elseif ($strVersaoModuloPeticionamento == '0.0.1') {
                $this->instalarv002();
                $this->instalarv100();
                $this->instalarv104();
                $this->instalarv110();
                $this->instalarv200();
                $this->instalarv201();
                $this->instalarv202();
                $this->logar('INSTALAÇÃO/ATUALIZAÇÃO DA VERSÃO '.$this->versaoAtualDesteModulo.' DO '.$this->nomeDesteModulo.' REALIZADA COM SUCESSO NA BASE DO SEI');
                $this->finalizar('FIM', false);
            } elseif ($strVersaoModuloPeticionamento == '0.0.2') {
                $this->instalarv100();
                $this->instalarv104();
                $this->instalarv110();
                $this->instalarv200();
                $this->instalarv201();
                $this->instalarv202();
                $this->logar('INSTALAÇÃO/ATUALIZAÇÃO DA VERSÃO '.$this->versaoAtualDesteModulo.' DO '.$this->nomeDesteModulo.' REALIZADA COM SUCESSO NA BASE DO SEI');
                $this->finalizar('FIM', false);
            } elseif (in_array($strVersaoModuloPeticionamento, array('1.0.0', '1.0.3'))) {
                $this->instalarv104();
                $this->instalarv110();
                $this->instalarv200();
                $this->instalarv201();
                $this->instalarv202();
                $this->logar('INSTALAÇÃO/ATUALIZAÇÃO DA VERSÃO '.$this->versaoAtualDesteModulo.' DO '.$this->nomeDesteModulo.' REALIZADA COM SUCESSO NA BASE DO SEI');
                $this->finalizar('FIM', false);
            } elseif ($strVersaoModuloPeticionamento == '1.0.4') {
                $this->instalarv110();
                $this->instalarv200();
                $this->instalarv201();
                $this->instalarv202();
                $this->logar('INSTALAÇÃO/ATUALIZAÇÃO DA VERSÃO '.$this->versaoAtualDesteModulo.' DO '.$this->nomeDesteModulo.' REALIZADA COM SUCESSO NA BASE DO SEI');
                $this->finalizar('FIM', false);
            } elseif ($strVersaoModuloPeticionamento == '1.1.0') {
                $this->instalarv200();
                $this->instalarv201();
                $this->instalarv202();
                $this->logar('INSTALAÇÃO/ATUALIZAÇÃO DA VERSÃO '.$this->versaoAtualDesteModulo.' DO '.$this->nomeDesteModulo.' REALIZADA COM SUCESSO NA BASE DO SEI');
                $this->finalizar('FIM', false);
            } elseif ($strVersaoModuloPeticionamento == '2.0.0') {
                $this->instalarv201();
                $this->instalarv202();
                $this->logar('INSTALAÇÃO/ATUALIZAÇÃO DA VERSÃO '.$this->versaoAtualDesteModulo.' DO '.$this->nomeDesteModulo.' REALIZADA COM SUCESSO NA BASE DO SEI');
                $this->finalizar('FIM', false);
            } elseif ($strVersaoModuloPeticionamento == '2.0.1') {
                $this->instalarv202();
                $this->logar('INSTALAÇÃO/ATUALIZAÇÃO DA VERSÃO '.$this->versaoAtualDesteModulo.' DO '.$this->nomeDesteModulo.' REALIZADA COM SUCESSO NA BASE DO SEI');
                $this->finalizar('FIM', false);
            } elseif ($strVersaoModuloPeticionamento == '2.0.2') {
                $this->logar('A VERSÃO MAIS ATUAL DO '.$this->nomeDesteModulo.' (v'.$this->versaoAtualDesteModulo.') JÁ ESTÁ INSTALADA.');
                $this->finalizar('FIM', false);
            } else {
                $this->logar('A VERSÃO DO '.$this->nomeDesteModulo.' INSTALADA NESTE AMBIENTE (v'.$strVersaoModuloPeticionamento.') NÃO É COMPATÍVEL COM A ATUALIZAÇÃO PARA A VERSÃO MAIS RECENTE (v'.$this->versaoAtualDesteModulo.').');
                $this->instalarv202();
                $this->finalizar('FIM', false);
            }

            InfraDebug::getInstance()->setBolLigado(false);
            InfraDebug::getInstance()->setBolDebugInfra(false);
            InfraDebug::getInstance()->setBolEcho(false);

        } catch (Exception $e) {
            InfraDebug::getInstance()->setBolLigado(true);
            InfraDebug::getInstance()->setBolDebugInfra(true);
            InfraDebug::getInstance()->setBolEcho(true);
            throw new InfraException('Erro instalando/atualizando versão.', $e);
        }
    }

    //Contem atualizações da versao 0.0.1
    protected function instalarv001(){

        $objInfraMetaBD = new InfraMetaBD(BancoSEI::getInstance());
        $this->logar('EXECUTANDO A INSTALAÇÃO/ATUALIZAÇÃO DA VERSÃO 0.0.1 DO '.$this->nomeDesteModulo.' NA BASE DO SEI');

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


        $this->logar('ADICIONANDO PARÂMETRO '.$this->nomeParametroModulo.' NA TABELA infra_parametro PARA CONTROLAR A VERSÃO DO MÓDULO');
        BancoSEI::getInstance()->executarSql('INSERT INTO infra_parametro (valor, nome ) VALUES( \'0.0.1\',  \'' . $this->nomeParametroModulo . '\' )');

    }

    //Contem atualizações da versao 0.0.2
    protected function instalarv002(){

        $objInfraMetaBD = new InfraMetaBD(BancoSEI::getInstance());
        $this->logar('EXECUTANDO A INSTALAÇÃO/ATUALIZAÇÃO DA VERSÃO 0.0.2 DO '.$this->nomeDesteModulo.' NA BASE DO SEI');

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

        //Parametrizar Email de Alerta às Unidades
        $conteudo1 = "      :: Este é um e-mail automático ::

O Usuário Externo @nome_usuario_externo@ (@email_usuario_externo@) efetivou o Peticionamento Eletrônico do tipo @tipo_peticionamento@ (@tipo_processo@), no âmbito do processo nº @processo@, conforme disposto no Recibo Eletrônico de Protocolo SEI nº @documento_recibo_eletronico_de_protocolo@.

O mencionado processo se encontra aberto em sua Unidade (@sigla_unidade_abertura_do_processo@). Entre no SEI e confira! Caso não seja de competência de sua Unidade, verifique se já está aberto na Unidade correta e, do contrário, envie-o para a Unidade competente para que seja devidamente tratado.


@sigla_orgao@
@descricao_orgao@
@sitio_internet_orgao@

ATENÇÃO: As informações contidas neste e-mail, incluindo seus anexos, podem ser restritas apenas à pessoa ou entidade para a qual foi endereçada. Se você não é o destinatário ou a pessoa responsável por encaminhar esta mensagem ao destinatário, você está, por meio desta, notificado que não deverá rever, retransmitir, imprimir, copiar, usar ou distribuir esta mensagem ou quaisquer anexos. Caso você tenha recebido esta mensagem por engano, por favor, contate o remetente imediatamente e em seguida apague esta mensagem.";

        $maxIdEmailSistemaUnidades = $this->retornarMaxIdEmailSistema();

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
            (" . $maxIdEmailSistemaUnidades . ",
              'Peticionamento Eletrônico - Alerta às Unidades',
              '@sigla_sistema@ <@email_sistema@>',
              '@emails_unidade@',
              'SEI Peticionamento Eletrônico - Processo nº @processo@',
              '" . $conteudo1 . "',
              'S',
            'MD_PET_ALERTA_PETICIONAMENTO_UNIDADES'
            )";
        BancoSEI::getInstance()->executarSql($insert1);

        //Parametrizar Email de Confirmação ao Usuario Externo
        $conteudo2 = "      :: Este é um e-mail automático ::

Prezado(a) @nome_usuario_externo@,

Este e-mail confirma a realização do Peticionamento Eletrônico do tipo @tipo_peticionamento@ no SEI-@sigla_orgao@, no âmbito do processo nº @processo@, conforme disposto no Recibo Eletrônico de Protocolo SEI nº @documento_recibo_eletronico_de_protocolo@.

Caso no futuro precise realizar novo peticionamento, sempre acesse a área destinada aos Usuários Externos no SEI-@sigla_orgao@ destacada em seu Portal na Internet ou acesse diretamente o link a seguir: @link_login_usuario_externo@


@sigla_orgao@
@descricao_orgao@
@sitio_internet_orgao@

ATENÇÃO: As informações contidas neste e-mail, incluindo seus anexos, podem ser restritas apenas à pessoa ou entidade para a qual foi endereçada. Se você não é o destinatário ou a pessoa responsável por encaminhar esta mensagem ao destinatário, você está, por meio desta, notificado que não deverá rever, retransmitir, imprimir, copiar, usar ou distribuir esta mensagem ou quaisquer anexos. Caso você tenha recebido esta mensagem por engano, por favor, contate o remetente imediatamente e em seguida apague esta mensagem.";

        $maxIdEmailSistemaUsuario = $this->retornarMaxIdEmailSistema();

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
        (" . $maxIdEmailSistemaUsuario . ",
              'Peticionamento Eletrônico - Confirmação ao Usuário Externo',
              '@sigla_sistema@ <@email_sistema@>',
              '@email_usuario_externo@',
              'SEI - Confirmação de Peticionamento Eletrônico (Processo nº @processo@)',
              '" . $conteudo2 . "',
              'S',
            'MD_PET_CONFIRMACAO_PETICIONAMENTO_USUARIO_EXTERNO'
        )";

        BancoSEI::getInstance()->executarSql($insert2);


        //Tabelas relacionais com Tipos de Contatos permitidos para Cadastro e para Seleção
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


        $this->logar('ATUALIZANDO PARÂMETRO '.$this->nomeParametroModulo.' NA TABELA infra_parametro PARA CONTROLAR A VERSÃO DO MÓDULO');
        BancoSEI::getInstance()->executarSql('UPDATE infra_parametro SET valor = \'0.0.2\' WHERE nome = \'' . $this->nomeParametroModulo . '\' ');

    }

    //Contem atualizações da versao 1.0.0
    protected function instalarv100(){

            $objInfraMetaBD = new InfraMetaBD(BancoSEI::getInstance());


            $this->logar('EXECUTANDO A INSTALAÇÃO/ATUALIZAÇÃO DA VERSÃO 1.0.3 DO '.$this->nomeDesteModulo.' NA BASE DO SEI');


            $this->logar('CRIANDO A TABELA md_pet_hipotese_legal');

            BancoSEI::getInstance()->executarSql('CREATE TABLE md_pet_hipotese_legal (
                id_md_pet_hipotese_legal ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL 
            )');

            $objInfraMetaBD->adicionarChavePrimaria('md_pet_hipotese_legal', 'pk_md_pet_hipotese_legal', array('id_md_pet_hipotese_legal'));
            $objInfraMetaBD->adicionarChaveEstrangeira('fk_id_md_pet_hip_legal1', 'md_pet_hipotese_legal', array('id_md_pet_hipotese_legal'), 'hipotese_legal', array('id_hipotese_legal'));


            $this->logar('DROP DA COLUNA  id_unidade (Não é mais unidade única. Agora terá opção para Peticionamento de Processo Novo para Múltiplas Unidades)');

            if (BancoSEI::getInstance() instanceof InfraMySql) {
                $objInfraMetaBD->excluirChaveEstrangeira('md_pet_tipo_processo', 'fk_pet_tp_proc_unidade_02');
                $objInfraMetaBD->excluirIndice('md_pet_tipo_processo', 'fk_pet_tp_proc_unidade_02');
            } else {
                $objInfraMetaBD->excluirChaveEstrangeira('md_pet_tipo_processo', 'fk_pet_tp_proc_unidade_02');
            }

            BancoSEI::getInstance()->executarSql('ALTER TABLE md_pet_tipo_processo DROP COLUMN id_unidade');


            $this->logar('CRIANDO A TABELA md_pet_rel_tp_processo_unid (para permitir multiplas unidades)');

            BancoSEI::getInstance()->executarSql('CREATE TABLE md_pet_rel_tp_processo_unid ( 
                id_md_pet_tipo_processo ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL,
                id_unidade ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL,
                sta_tp_unidade ' . $objInfraMetaBD->tipoTextoFixo(1) . ' NOT NULL
                )'
            );


            //Tabelas Abaixo é o problema da modificação da PK (Pk deixou de ser composta e passou a ter SEQ)
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


            //CRIANDO NOVO TIPO DE DOCUMENTO "Recibo Eletrônico de Protocolo"
            $this->logar('CRIANDO MODELO "Modulo_Peticionamento_Recibo_Eletronico_Protocolo"');
            $modeloRN = new ModeloRN();
            $modeloDTO = new ModeloDTO();
            $modeloDTO->setNumIdModelo(null);
            $modeloDTO->retTodos();
            $modeloDTO->setStrNome('Modulo_Peticionamento_Recibo_Eletronico_Protocolo');
            $modeloDTO->setStrSinAtivo('S');
            $modeloDTO = $modeloRN->cadastrar($modeloDTO);


            //adicionando as seções do modelo: Corpo de Texto e Rodapé
            $this->logar('CRIANDO SEÇAO DO MODELO - Corpo do Texto');
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


            //secao do rodapé
            $this->logar('CRIANDO SEÇAO DO MODELO - Rodapé');
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

            $secaoModeloRodapeDTO = $secaoModeloRN->cadastrar($secaoModeloRodapeDTO);

            //Criar o Grupo de Tipo de Documento "Internos do Sistema".
            $grupoSerieRN = new GrupoSerieRN();

            if (BancoSEI::getInstance() instanceof InfraMySql) {

                //verificando antes a situaçao da tabela seq_grupo_serie
                $arrDados = BancoSEI::getInstance()->consultarSql('SELECT * FROM seq_grupo_serie ORDER BY id DESC LIMIT 1 ');

                $grupoSerieDTOLista = new GrupoSerieDTO();
                $grupoSerieDTOLista->retTodos();
                $grupoSerieDTOLista->setOrd("IdGrupoSerie", InfraDTO::$TIPO_ORDENACAO_DESC);
                $grupoSerieDTOLista->setNumMaxRegistrosRetorno(1);

                $arrListaGrupoSerie = $grupoSerieRN->listarRN0778($grupoSerieDTOLista);

                //ja tem registro na SEQ, insere apenas se ID da SEQ estiver incorreto
                if ($arrDados != null && count($arrDados) > 0) {

                    if ($arrDados[0]['id'] < $arrListaGrupoSerie[0]->getNumIdGrupoSerie()) {

                        //INSERT para garantir a SEQ na posiçao correta
                        BancoSEI::getInstance()->executarSql('INSERT INTO seq_grupo_serie ( id ) VALUES ( ' . $arrListaGrupoSerie[0]->getNumIdGrupoSerie() . ') ');
                    }

                } //nao tem registro na SEQ ainda, colocar o ID do grupo_serie mais atual
                else {

                    //INSERT para garantir a SEQ na posiçao correta
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


            //Criar o Tipo de Documento "Recibo Eletrônico de Protocolo"
            $this->logar('CRIANDO TIPO DE DOCUMENTO Recibo Eletrônico de Protocolo');
            $serieDTO = new SerieDTO();
            $serieDTO->retTodos();
            $serieRN = new SerieRN();

            $serieDTO->setNumIdSerie(null);
            $serieDTO->setNumIdGrupoSerie($grupoSerieDTO->getNumIdGrupoSerie());
            $serieDTO->setStrStaNumeracao(SerieRN::$TN_SEM_NUMERACAO);
            $serieDTO->setStrStaAplicabilidade(SerieRN::$TA_INTERNO);
            $serieDTO->setNumIdModeloEdoc(null);
            $serieDTO->setNumIdModelo($modeloDTO->getNumIdModelo());
            $serieDTO->setStrNome('Recibo Eletrônico de Protocolo');
            $serieDTO->setStrDescricao('Utilizado para a geração automática do Recibo Eletrônico de Protocolo nos Peticionamentos Eletrônicos realizados por Usuário Externo diretamente no Acesso Externo do SEI.');
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


            $this->logar('ATUALIZANDO INFRA_PARAMETRO (' . MdPetAtualizadorSeiRN::$MD_PET_ID_SERIE_RECIBO . ')');
            $nomeParamIdSerie = MdPetAtualizadorSeiRN::$MD_PET_ID_SERIE_RECIBO;

            BancoSEI::getInstance()->executarSql('INSERT INTO infra_parametro ( valor, nome )  VALUES (\'' . $serieDTO->getNumIdSerie() . '\' , \'' . $nomeParamIdSerie . '\' ) ');


            $this->logar('ATUALIZANDO PARÂMETRO '.$this->nomeParametroModulo.' NA TABELA infra_parametro PARA CONTROLAR A VERSÃO DO MÓDULO');
            BancoSEI::getInstance()->executarSql('UPDATE infra_parametro SET valor = \'1.0.3\' WHERE nome = \'' . $this->nomeParametroModulo . '\' ');

    }

    //Contem atualizações da versao 1.0.4
    protected function instalarv104(){

            $objInfraMetaBD = new InfraMetaBD(BancoSEI::getInstance());


            $this->logar('EXECUTANDO A INSTALAÇÃO/ATUALIZAÇÃO DA VERSÃO 1.0.4 DO '.$this->nomeDesteModulo.' NA BASE DO SEI');

            //Caso exista a coluna na tabela a instalação é nova, então não é necessario executar a migração de dados
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
            $objTarjaAssinaturaDTO->setStrStaTarjaAssinatura( MdPetAssinaturaRN::$TT_ASSINATURA_SENHA_PETICIONAMENTO );
            $objTarjaAssinaturaDTO->setStrTexto('<hr style="margin: 0 0 4px 0;" />  <table>    <tr>      <td>  @logo_assinatura@      </td>      <td>  <p style="margin:0;text-align: left; font-size:11pt;font-family: Calibri;">Documento assinado eletronicamente por <b>@nome_assinante@</b>, <b>@tratamento_assinante@</b>, em @data_assinatura@, às @hora_assinatura@, conforme horário oficial de Brasília, com fundamento no art. 6º, § 1º, do <a title="Acesse o Decreto" href="http://www.planalto.gov.br/ccivil_03/_Ato2015-2018/2015/Decreto/D8539.htm" target="_blank">Decreto nº 8.539, de 8 de outubro de 2015</a>.</p>      </td>    </tr>  </table>');
            $objTarjaAssinaturaDTO->setStrLogo('iVBORw0KGgoAAAANSUhEUgAAAFkAAAA8CAMAAAA67OZ0AAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAADTtpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+Cjx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDQuMi4yLWMwNjMgNTMuMzUyNjI0LCAyMDA4LzA3LzMwLTE4OjEyOjE4ICAgICAgICAiPgogPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4KICA8cmRmOkRlc2NyaXB0aW9uIHJkZjphYm91dD0iIgogICAgeG1sbnM6ZGM9Imh0dHA6Ly9wdXJsLm9yZy9kYy9lbGVtZW50cy8xLjEvIgogICAgeG1sbnM6eG1wUmlnaHRzPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvcmlnaHRzLyIKICAgIHhtbG5zOnBob3Rvc2hvcD0iaHR0cDovL25zLmFkb2JlLmNvbS9waG90b3Nob3AvMS4wLyIKICAgIHhtbG5zOklwdGM0eG1wQ29yZT0iaHR0cDovL2lwdGMub3JnL3N0ZC9JcHRjNHhtcENvcmUvMS4wL3htbG5zLyIKICAgeG1wUmlnaHRzOldlYlN0YXRlbWVudD0iIgogICBwaG90b3Nob3A6QXV0aG9yc1Bvc2l0aW9uPSIiPgogICA8ZGM6cmlnaHRzPgogICAgPHJkZjpBbHQ+CiAgICAgPHJkZjpsaSB4bWw6bGFuZz0ieC1kZWZhdWx0Ii8+CiAgICA8L3JkZjpBbHQ+CiAgIDwvZGM6cmlnaHRzPgogICA8ZGM6Y3JlYXRvcj4KICAgIDxyZGY6U2VxPgogICAgIDxyZGY6bGk+QWxiZXJ0byBCaWdhdHRpPC9yZGY6bGk+CiAgICA8L3JkZjpTZXE+CiAgIDwvZGM6Y3JlYXRvcj4KICAgPGRjOnRpdGxlPgogICAgPHJkZjpBbHQ+CiAgICAgPHJkZjpsaSB4bWw6bGFuZz0ieC1kZWZhdWx0Ii8+CiAgICA8L3JkZjpBbHQ+CiAgIDwvZGM6dGl0bGU+CiAgIDx4bXBSaWdodHM6VXNhZ2VUZXJtcz4KICAgIDxyZGY6QWx0PgogICAgIDxyZGY6bGkgeG1sOmxhbmc9IngtZGVmYXVsdCIvPgogICAgPC9yZGY6QWx0PgogICA8L3htcFJpZ2h0czpVc2FnZVRlcm1zPgogICA8SXB0YzR4bXBDb3JlOkNyZWF0b3JDb250YWN0SW5mbwogICAgSXB0YzR4bXBDb3JlOkNpQWRyRXh0YWRyPSIiCiAgICBJcHRjNHhtcENvcmU6Q2lBZHJDaXR5PSIiCiAgICBJcHRjNHhtcENvcmU6Q2lBZHJSZWdpb249IiIKICAgIElwdGM0eG1wQ29yZTpDaUFkclBjb2RlPSIiCiAgICBJcHRjNHhtcENvcmU6Q2lBZHJDdHJ5PSIiCiAgICBJcHRjNHhtcENvcmU6Q2lUZWxXb3JrPSIiCiAgICBJcHRjNHhtcENvcmU6Q2lFbWFpbFdvcms9IiIKICAgIElwdGM0eG1wQ29yZTpDaVVybFdvcms9IiIvPgogIDwvcmRmOkRlc2NyaXB0aW9uPgogPC9yZGY6UkRGPgo8L3g6eG1wbWV0YT4KICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAg
ICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgIAo8P3hwYWNrZXQgZW5kPSJ3Ij8+RO84nQAAAwBQTFRFamts+fn5mp6hc3Nz9fX1U1NTS0tKnaGk6unqzM3P7e3u8fHxuLm7/Pz8lZmc2dnZxcXGWlpavr29wsLCp6eniYmKhYaGZWZmkpaZ0dHS5eXlkZGSrq2utbW2XV1d4uHhfX1+sbGy1dXW3d3dqampgYGCjY2OyMnKYWJihYaIjY6RnZ2ejpGSra+xeHl7lZWVmJiYgoKFpKaptre5vb7Aurq8oaGikpSWmJufh4iKkZKVysrMtrq7ioyOdXZ4fn+ArrGywcLEzc7QiYqMt7W1/v/8mZqcxsbIpqqrZGFhztDSeXp7iIWGnJqalJKSf4CCg4B/amZmoaSm5+fmvLy6ys3OzMzL2tze3dzaa2hny8nH0M7NiYiGbG5v19jYWFVVcG5s2drcxMTD0dPUx8jJ/P79sbO1j46OmZWU1dfXhIKC1NLTd3h68fL0wsTGb3By+vf3YV1d2NjW7u7u6Ojpe3x9fHp54eLkxMLAvLq5/f39+vr63t7fXFtamZiW6urqzMnKwL+98PHvrKytq6qq7evpr62toKKkvr/BOzk42dvad3V06OjmpaSj5efnnZyblpWT/fz6ZWZo9/f3jYyKqquteXd47u3rhYSC5eTisbCueXh2qaimWlhXjImIY2Bfc3Bw////UFBP/v7+/v////7///3+g4SHaGlpYmNj8vPzZ2dn/vz9WFhYtbO0ztDPWltbbW9u/v7/xcPEiouLrayq4+Tms7S2VldX7/DyqKel+/z++Pj4+ff4cXBuuru7u7y+7+/vx8fH8/HysK+wXFxc/fv8s7OztrWzZWRio6Ohl5eZ1NTUZGRkraus2NbX4N/d0dDP3dzc9ff14ODg9/n4oaCg4eHf+/v76+vrQD4+7Ozs/f3/7evsRUJCvLy87vDtysvLXl9fzczNwsPDYGBgw7+/ysjJgH19gH9/29rbwMC/Tk1MlJCPoaCeX1tb6ufo4uPjx8fF5OPht7e3X15cuLe4tLKzn56f09TW1dXTYWJkh4eHZGJj3+Diq6urXLJJJAAAC8BJREFUeNqsmAtYE1cWgAcmJLwSwjMJAYxiQhIeITyEgCGiAioCaiqWaoCiFQVKtgWsJFRapEpFatuodetKHYaQkIiipZVWqqBQ64OqrduGuquVR1sDu62u69JdW/fOZCCJovjttyffl9yZ3PvfM2fOOffcC6UgJ1a5R1GeJI6OjvHx8TQgTCYzLiEsTCgU8qRSQcaN4VNsWWpsndep7u7u2NhY9+7UkpKSJFnqkApBIOTrufFgJDb2MUIQ4xLYAMnjSRf4+koEAoGupLcMdQtVRBs0JA3JImovpVKpUED6SAMCnZhLo1Dmrlzp8hhJxCQkJGRdGhA6nV5aWjrs7T08nJw8Ono6hD7aXZd2ml5ALygoGAb33QPvBs68ACsZIjXkAcBLmpH/RVC7H7xlaZ86qmTcgY47UsKbEW3LU4Mmx9tTJwWYGJFAeh4URXGc2/yUCqJTaGrLRlFi3khIAUMUCxl9Kjj4qFQo1WYeC27ie6KjSK+AMHIsuDu92qpq8wCK+P+6cdasGvRRM6G21yI9hJPdn+Z1vTCfJvZlNccIgQt6IIj2iZ0zjY+Q0SnfGvZ921EiMC645kKjxNOen06NTMaTdH5oklwhl8OHdyyhUWgJudOS+yG9HRl9RGWrzm/FKfRNHYZEWnyCdON0ZHa/Xv8kO9u9FJSlY3DNzclMmtD34rTkVr1xajKKpFgaVIcu9URkkKq7EFW3MEEiZk1L5hsfJqtfrP74lXK3LhTDqQy/r+uOTX7egIUVKbhKvmOGQ7dEKpaxpvN/Np/BsLdzWeJWkDMpi+reAv5NNftIsjjpEekXLgJ0bgUDapf2JIsFnIgj0+o8YkMGuQMtX8SkgbTpyGTSEcTkIuX6CsTcLJkyAlzmRvD1nR1lXhXcJNjl4fTxsBSO9Pfb6IwaFjG3UxxXrKDQHF9B0F+lAp5AOH5BnM5RyF5Gnk9vVbR3lMUmVcBHb05lDXwm4nbhYH/rJBmY1QWAKe65q+avX09CB1LFPMF4VZchWQxH6MdR834+1OZbFg0nKfQhdo5Dch0YcHYu7zFZ/Yk3yG+10blrHo3iGK4G/1JdUWoal6eLm4Hli25FEsSZcTVp0Nh5v+w4BBtbT9u4peFITF1dTMyN7ple8kkD8YL4fCv5mGZRPIWynhjRM0cs0bljHY9VySDo6OmP69sZTvfLZr6raA2iW5+/pjSKsvb34FWrqrZXsM0TobY7iD9iq3N4PLDyuhfxQTMWSHSSdSiJZHCokjIUrXdvw56tTX6uvXx9X9vwpM7Hopes2h7uHh14/LhIEiF0Jf7Y3TcyaGNndSITXDAD1oL/UVaWRCcIDZ8d1eATWgFBg1uD4c4RcpHrg3Z+Z97w5Bv7mFI3b3ag+73AwMAGXwFcSrWQO9oHrWTQ75M9NEdHmlAYdaRLlVYh0GUlgVXY2M+Ajur7onJhp0FA9ukMcsLJ+HM3r3WUht0mgixUnBTVRZA9bcmgc3k4M4FJCxNIujXrSnRiTokSLA16Bn8waGzcA27qI+9znUNuc3LyBp0t4b8yXrjiE2L4VhkcqrE0fduCgmysAeQT+oowaUKYQJecXcLlyETbx0NDIyNFIrZvmhkCZL9rqdedxsijk2QXmnROGUHew1FSSBPkwT47ncHK4UwPFUil4oQbHE4JJw3RdHVpcEGK9WN9ZG519vjs83OCJ1VxuSChlFmax/ZUKLdP6NzZ5/lIrnvh9rhOIpb0LigpgWfa+G0xoymILCt/KO7qhIK4UtYQVuzMT4AhHuEckjxPTxtrEM5IXVKhyxK4z1FEKGWzrOVAsbGpncypPrG2O61nYj6VSxxPKJX4+XFlsor0iJIkRUbPo2SAHPDH0qU6OV3HEbMS34WVUBa9vMvk0ONxcwC5aAR25pYvYQqSomoIdHXc9vmzWNnZiUNHbp6mh4TcPB9UgPvdfSc7skN0agzL7FEnzBKXSNxqeIPw0X6935ZQkS/EGEZYmM5+ueESiQJiEY/isSARxZ8UdbCULLf7A9TYtZ892ZCqE0jZPLFMXAIHHkNyZUFGqLU9z8mpiUz2QS7qgZ0lG1ekVwwGzSfywyrpOrwhj5L0GrCGf384npcIcny05dleEesEYhmHE6FMegC8R2Vm97e1tXViYPIu5Erbd+Q395bHQJ1kdg9R+ezwpWP2+0sql62IVYPprvID1FayI0FGetzHpTpAFqSmGfBnqykY58IKCL7FPvsVMkPkx/ZrMJBOZdZWEzlNtUNQipEN6RdmKSOBMujVwQdWMohnQmeE6hzMCkk8Eoy7vhYb3SU35+Z+Jce81ERyc6shqRCVxpqHPcSlKqwRKhNCoyYsjwXZkwMfrYhQrdam4kBtVyfU2jtXh+mMojWi/4Tj0VfVNwV5wp/BF6CabhSqrfUm+tln9lMT9Fxusgq/2Ws047/BbbU25HjacaK/CWO3oGhKi4n64zcqAnZIiw5EHp7QFEsXVCoB
3wjiH7ea+0l/vK+8rcFhkhwfz7SsI2UiTuOlzxcWRbpd2VcYXDx+5nDGT2zDQObezKob3x34MGSraX7tzoLdmffG6wu/smi9sWS9BqWaTIj/SoMJ+50/5mOa9Od4moWM9Cz02r9JPpZhvpoPm3cG5LgeXJzh+aXmVOXBwtU/wzPG8x1q859dQ/7mtTs/LM50sEQAO4nH5nV0SDo6/Li3blVwRposRQ5OTqXFncW7/Xlh5smcr/curjS8nfcnUu1yZ/jtmk085HDm4qVvbArVhsLUXtjMLULdvsjIW2qw2OZqQ0eH732/fUXcW6Dk2Qune1mmtCNTh/NW716c0rOtafM7r3+w695y5/pxTdHu0Zw7t5a9AW/R7jK+tyUneFkm4nPyuYNFZyYqgoGBakxAVVBeLpdfI14HTqbR4nBrqH68viY/p3rpTwfunN/00vszR+T5W7r276aP7ftg2R8av/sh22nxq3Dwpkbko7w1efvcpq7iJ27h5AvMhHmW6V9beKRYQ194STMUkK3xH3JgVakuehxaXfmcBzJj5iztjwuHzGcumRFSQWVBlRqx2wXZxYKVHEYk+BbcFVuaX9CasLSAZ4bmQ+oW0L25GbW6MVX1GE2tgpNFcWHzrNO5iR5YulJVzRjboXd5LbEJHe2oslHv2BRA1J4cFxcWbg2sayd5WLPlzDe7QEy0IN9v/sKbZFG/+MtyEJ1EtKOP6os+rPMEGVF/eHDT6jP1mSnPHFz2cvb1po8ub2k8//Xfzq35x19rRQc3vDOU8d7Oxg+e8WjMKfRHp96IoXZ2jgsThuO9nv353vv/lHM2fPuS16fL/52zfEfBdU7Blpy6+qWXc/K3BHlXnnyZnV97h5V959zfU560H8QiBVsHE9jScGwuauX1xv2d5qK3R683wucuFxaleB0I/jZnA7ItZ3P9pzvza73g1+HzKSnv1S4dy6BOs43G10FA3ooZjup1/crOPzrvFXmTL/3yS/WyZSleL8nlOY0p53Oy92/7Hv7Iq35zfkbKO0s3FednTkO2WCNMKN2Kvxb5b78tTehRFrr+zCjaRY18s+HGgatow1iO57bL/bU9xk8rzz3bQH61IXPxMvIG6jRnCvcJ8h7LPed7hz3QWVVa/38trEJcn2H1DGkQUvb7qxFSsVx90f8ai6ShH/Ynfeh95bZqmvMK3M5Coe8eyyvVfq5WYYs8SlXjDo2AK0SlPgS8D7QRVIVlZrSZapr+xMLiG1LJnscnAIsrt9itUehjDmNsROLUxod8BJJQ1HYQShx1aK1orR1IO/2RRX2nUwW0VrxAQkf+vxLQ6Tl2AzoxO0si8ekG26OYmG7sQK/S3f3evbt3o6MDwebj7NmzMzHpBRIQELAVyIPa2trZPk+SfZ6eZD8HCCHNlnFBLSnjVIByEtSTQGAYVlqO9EDJrzcaGYz+Vj6fPzIY1Nfe7gnqpk5Qkz1WmpyamvxqECgFURX78HQ6MdgHZ+F8vF618MEER5VHIWwCI5igH5tgEEhfu+cTpN/PGzj8fwUYAEHf/4ET3ikCAAAAAElFTkSuQmCC');
            $objTarjaAssinaturaDTO->setStrSinAtivo('S');

            $objTarjaAssinaturaBD = new TarjaAssinaturaBD($this->getObjInfraIBanco());
            $objTarjaAssinaturaDTO = $objTarjaAssinaturaBD->cadastrar( $objTarjaAssinaturaDTO );


            $this->logar('ATUALIZANDO PARÂMETRO '.$this->nomeParametroModulo.' NA TABELA infra_parametro PARA CONTROLAR A VERSÃO DO MÓDULO');
            BancoSEI::getInstance()->executarSql('UPDATE infra_parametro SET valor = \'1.0.4\' WHERE nome = \'' . $this->nomeParametroModulo . '\' ');

    }

    //Contem atualizações da versao 1.1.0 (Intercorrente)
    protected function instalarv110(){

            $objInfraMetaBD = new InfraMetaBD(BancoSEI::getInstance());


            $this->logar('EXECUTANDO A INSTALAÇÃO/ATUALIZAÇÃO DA VERSÃO 1.1.0 DO '.$this->nomeDesteModulo.' NA BASE DO SEI');


            $this->logar('CREATE TABLE md_pet_criterio');

            BancoSEI::getInstance()->executarSql('CREATE TABLE md_pet_criterio (
                id_md_pet_criterio ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL,
                sin_ativo ' . $objInfraMetaBD->tipoTextoFixo(1) . ' NOT NULL,
                sin_criterio_padrao ' . $objInfraMetaBD->tipoTextoFixo(1) . ' NOT NULL,
                sta_nivel_acesso ' . $objInfraMetaBD->tipoTextoFixo(1) . '  NULL,
                sta_tipo_nivel_acesso ' . $objInfraMetaBD->tipoTextoFixo(1) . '  NULL,
                id_hipotese_legal ' . $objInfraMetaBD->tipoNumero() . '  NULL,
                id_tipo_procedimento ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL
                )');

            $objInfraMetaBD->adicionarChavePrimaria('md_pet_criterio','pk_md_pet_criterio',array('id_md_pet_criterio'));
            $objInfraMetaBD->adicionarChaveEstrangeira('fk1_md_pet_criterio', 'md_pet_criterio', array('id_hipotese_legal'), 'hipotese_legal', array('id_hipotese_legal'));
            $objInfraMetaBD->adicionarChaveEstrangeira('fk2_md_pet_criterio', 'md_pet_criterio', array('id_tipo_procedimento'), 'tipo_procedimento', array('id_tipo_procedimento'));


            $this->logar('CREATE SEQUENCE seq_md_pet_criterio');
            BancoSEI::getInstance()->criarSequencialNativa('seq_md_pet_criterio', 1);


            //Criando campo "md_pet_rel_recibo_protoc.id_protocolo_relacionado caso" ainda nao exista
            $coluna = $objInfraMetaBD->obterColunasTabela('md_pet_rel_recibo_protoc', 'id_protocolo_relacionado');

            if( $coluna == null || !is_array( $coluna ) ){
                $this->logar('CREATE CAMPO id_protocolo_relacionado');
 
                $objInfraMetaBD->adicionarColuna('md_pet_rel_recibo_protoc', 'id_protocolo_relacionado', '' . $objInfraMetaBD->tipoNumeroGrande() , 'NULL');
 
                $objInfraMetaBD->adicionarChaveEstrangeira('fk5_md_pet_rel_recibo_protoc', 'md_pet_rel_recibo_protoc', array('id_protocolo_relacionado'), 'protocolo', array('id_protocolo'));
            }


            //coluna id_documento na tabela de recibo
            $this->logar('CREATE CAMPO md_pet_rel_recibo_protoc.id_documento');

            $objInfraMetaBD->adicionarColuna('md_pet_rel_recibo_protoc', 'id_documento', '' . $objInfraMetaBD->tipoNumeroGrande() , 'NULL');
            $objInfraMetaBD->adicionarChaveEstrangeira('fk6_md_pet_rel_recibo_protoc', 'md_pet_rel_recibo_protoc', array('id_documento'), 'documento', array('id_documento'));

            //Atualizando dados da tabela
            $objInfraParametro = new InfraParametro(BancoSEI::getInstance());
            $ret = $objInfraParametro->listarValores(array(MdPetAtualizadorSeiRN::$MD_PET_ID_SERIE_RECIBO), false);

            $arrObjInfraParametroDTO = NULL;
            $idSeriePet = array_key_exists(MdPetAtualizadorSeiRN::$MD_PET_ID_SERIE_RECIBO , $ret) ? $ret[MdPetAtualizadorSeiRN::$MD_PET_ID_SERIE_RECIBO] : null;

            if($idSeriePet){
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

                        foreach ($arrObjMdPetReciboDTO as $objDTO){
                            $objMdPetReciboRN->alterar($objDTO);
                        }
                    }
                }

            }


            $this->logar('ATUALIZANDO PARÂMETRO '.$this->nomeParametroModulo.' NA TABELA infra_parametro PARA CONTROLAR A VERSÃO DO MÓDULO');
            BancoSEI::getInstance()->executarSql('UPDATE infra_parametro SET valor = \'1.1.0\' WHERE nome = \'' . $this->nomeParametroModulo . '\' ');

    }

    //Contem atualizações da versao 2.0.0
    protected function instalarv200(){

            $objInfraMetaBD = new InfraMetaBD(BancoSEI::getInstance());


            $this->logar('EXECUTANDO A INSTALAÇÃO/ATUALIZAÇÃO DA VERSÃO 2.0.0 DO '.$this->nomeDesteModulo.' NA BASE DO SEI');

            $this->logar('INSERINDO EMAIL MD_PET_INTIMACAO_APENAS_RESPOSTAS_FACULTATIVAS NA TABELA email_sistema');

            //Parametrizar Email de Alerta às Unidades
            $conteudoRespostaFacultativa = "      :: Este é um e-mail automático ::

Prezado(a) @nome_usuario_externo@,

No SEI-@sigla_orgao@ foi expedida Intimação Eletrônica referente a @tipo_intimacao@, no âmbito do processo nº @processo@, conforme documento principal de protocolo nº @documento_principal_intimacao@ (@tipo_documento_principal_intimacao@).

Para visualizar o documento principal da Intimação Eletrônica e possíveis anexos, acesse a área destinada aos Usuários Externos no SEI-@sigla_orgao@ destacada em nosso Portal na Internet ou acesse diretamente o link a seguir: @link_login_usuario_externo@

Caso tenha interesse, a resposta à Intimação Eletrônica deve ser realizada na área destinada aos Usuários Externos indicada acima. Com o processo aberto, acesse o botão de Ação Responder Intimação Eletrônica.

Lembramos que, independente de e-mail de alerta, é de responsabilidade exclusiva do Usuário Externo a consulta periódica ao SEI a fim de verificar o recebimento de Intimações, considerando-se realizadas na data em que efetuar sua consulta no sistema ou, não efetuada a consulta, em @prazo_intimacao_tacita@ dias após a data de sua expedição.

Dessa forma, como a presente Intimação foi expedida em @data_expedicao_intimacao@ e em conformidade com as regras de contagem de prazo dispostas no art. 66 da Lei nº 9.784/1999, mesmo se não ocorrer a consulta direta no sistema aos documentos correspondentes, a Intimação será considerada cumprida por decurso do prazo tácito ao final do dia @data_final_prazo_intimacao_tacita@.


@sigla_orgao@
@descricao_orgao@
@sitio_internet_orgao@

ATENÇÃO: As informações contidas neste e-mail, incluindo seus anexos, podem ser restritas apenas à pessoa ou entidade para a qual foi endereçada. Se você não é o destinatário ou a pessoa responsável por encaminhar esta mensagem ao destinatário, você está, por meio desta, notificado que não deverá rever, retransmitir, imprimir, copiar, usar ou distribuir esta mensagem ou quaisquer anexos. Caso você tenha recebido esta mensagem por engano, por favor, contate o remetente imediatamente e em seguida apague esta mensagem.";

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
              'Peticionamento Eletrônico - Intimação Eletrônica apenas com Respostas Facultativas',
              '@sigla_sistema@ <@email_sistema@>',
              '@email_usuario_externo@',
              'SEI - Intimação Eletrônica Gerada no Processo nº @processo@',
              '" . $conteudoRespostaFacultativa . "',
              'S',
            'MD_PET_INTIMACAO_APENAS_RESPOSTAS_FACULTATIVAS'
            )";

            BancoSEI::getInstance()->executarSql($insertRespostaFacultativa);


            $this->logar('INSERINDO EMAIL MD_PET_INTIMACAO_QUE_EXIGE_RESPOSTA NA TABELA email_sistema');

            $conteudoExigeResposta = "      :: Este é um e-mail automático ::

Prezado(a) @nome_usuario_externo@,

No SEI-@sigla_orgao@ foi expedida Intimação Eletrônica referente a @tipo_intimacao@, no âmbito do processo nº @processo@, conforme documento principal de protocolo nº @documento_principal_intimacao@ (@tipo_documento_principal_intimacao@).

A mencionada Intimação exige resposta para @tipo_resposta@, no prazo de @prazo_externo_tipo_resposta@, contados a partir do dia útil seguinte ao da data de cumprimento da presente Intimação.

Para visualizar o documento principal da Intimação Eletrônica e possíveis anexos, acesse a área destinada aos Usuários Externos no SEI-@sigla_orgao@ destacada em nosso Portal na Internet ou acesse diretamente o link a seguir: @link_login_usuario_externo@

A resposta à Intimação Eletrônica que é exigida deve ser realizada na área destinada aos Usuários Externos indicada acima. Com o processo aberto, acesse o botão de Ação Responder Intimação Eletrônica.

Lembramos que, independente de e-mail de alerta, é de responsabilidade exclusiva do Usuário Externo a consulta periódica ao SEI a fim de verificar o recebimento de Intimações, considerando-se realizadas na data em que efetuar sua consulta no sistema ou, não efetuada a consulta, em @prazo_intimacao_tacita@ dias após a data de sua expedição.

Dessa forma, como a presente Intimação foi expedida em @data_expedicao_intimacao@ e em conformidade com as regras de contagem de prazo dispostas no art. 66 da Lei nº 9.784/1999, mesmo se não ocorrer a consulta direta no sistema aos documentos correspondentes, a Intimação será considerada cumprida por decurso do prazo tácito ao final do dia @data_final_prazo_intimacao_tacita@.


@sigla_orgao@
@descricao_orgao@
@sitio_internet_orgao@

ATENÇÃO: As informações contidas neste e-mail, incluindo seus anexos, podem ser restritas apenas à pessoa ou entidade para a qual foi endereçada. Se você não é o destinatário ou a pessoa responsável por encaminhar esta mensagem ao destinatário, você está, por meio desta, notificado que não deverá rever, retransmitir, imprimir, copiar, usar ou distribuir esta mensagem ou quaisquer anexos. Caso você tenha recebido esta mensagem por engano, por favor, contate o remetente imediatamente e em seguida apague esta mensagem.";

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
                'Peticionamento Eletrônico - Intimação Eletrônica que Exige Resposta',
                '@sigla_sistema@ <@email_sistema@>',
                '@email_usuario_externo@',
                'SEI - Intimação Eletrônica que Exige Resposta no Processo nº @processo@',
                '" . $conteudoExigeResposta . "',
                'S',
                'MD_PET_INTIMACAO_QUE_EXIGE_RESPOSTA'
            )";
            BancoSEI::getInstance()->executarSql($insertExigeResposta);


            $this->logar('INSERINDO EMAIL MD_PET_INTIMACAO_SEM_RESPOSTA NA TABELA email_sistema');

            $conteudoSemResposta = "      :: Este é um e-mail automático ::

Prezado(a) @nome_usuario_externo@,

No SEI-@sigla_orgao@ foi expedida Intimação Eletrônica referente a @tipo_intimacao@, no âmbito do processo nº @processo@, conforme documento principal de protocolo nº @documento_principal_intimacao@ (@tipo_documento_principal_intimacao@).

ATENÇÃO: A presente intimação não demanda qualquer tipo de resposta, por geralmente encaminhar documento para mero conhecimento, o que não dispensa a necessidade de acesso aos documentos para ciência de seu teor. Após o cumprimento da intimação, observar que neste caso não será disponibilizada a funcionalidade para Peticionamento de Resposta a Intimação Eletrônica, sem que isso impeça o uso do Peticionamento Intercorrente, caso ainda seja necessário protocolizar documento no processo acima indicado.

Para visualizar o documento principal da Intimação Eletrônica e possíveis anexos, acesse a área destinada aos Usuários Externos no SEI-@sigla_orgao@ destacada em nosso Portal na Internet ou acesse diretamente o link a seguir: @link_login_usuario_externo@

Lembramos que, independente de e-mail de alerta, é de responsabilidade exclusiva do Usuário Externo a consulta periódica ao SEI a fim de verificar o recebimento de Intimações, considerando-se realizadas na data em que efetuar sua consulta no sistema ou, não efetuada a consulta, em @prazo_intimacao_tacita@ dias após a data de sua expedição.

Dessa forma, como a presente Intimação foi expedida em @data_expedicao_intimacao@ e em conformidade com as regras de contagem de prazo dispostas no art. 66 da Lei nº 9.784/1999, mesmo se não ocorrer a consulta direta no sistema aos documentos correspondentes, a Intimação será considerada cumprida por decurso do prazo tácito ao final do dia @data_final_prazo_intimacao_tacita@.


@sigla_orgao@
@descricao_orgao@
@sitio_internet_orgao@

ATENÇÃO: As informações contidas neste e-mail, incluindo seus anexos, podem ser restritas apenas à pessoa ou entidade para a qual foi endereçada. Se você não é o destinatário ou a pessoa responsável por encaminhar esta mensagem ao destinatário, você está, por meio desta, notificado que não deverá rever, retransmitir, imprimir, copiar, usar ou distribuir esta mensagem ou quaisquer anexos. Caso você tenha recebido esta mensagem por engano, por favor, contate o remetente imediatamente e em seguida apague esta mensagem.";

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
            'Peticionamento Eletrônico - Intimação Eletrônica Sem Resposta',
            '@sigla_sistema@ <@email_sistema@>',
            '@email_usuario_externo@',
            'SEI - Intimação Eletrônica Gerada no Processo nº @processo@',
            '" . $conteudoSemResposta . "',
            'S',
            'MD_PET_INTIMACAO_SEM_RESPOSTA'
            )";

            BancoSEI::getInstance()->executarSql($insertSemResposta);


            $this->logar('INSERINDO EMAIL MD_PET_REITERACAO_INTIMACAO_QUE_EXIGE_RESPOSTA NA TABELA email_sistema');

            $conteudoReiteracaoExigeResposta = "      :: Este é um e-mail automático ::

Prezado(a) @nome_usuario_externo@,

Reiteramos a necessidade de Resposta à Intimação Eletrônica expedida no SEI-@sigla_orgao@ referente a @tipo_intimacao@, no âmbito do processo nº @processo@, conforme documento principal de protocolo nº @documento_principal_intimacao@ (@tipo_documento_principal_intimacao@).

A mencionada Intimação exige resposta para @tipo_resposta@, no prazo de @prazo_externo_tipo_resposta@, contados a partir do dia útil seguinte ao da data de cumprimento da Intimação, que ocorreu em @data_cumprimento_intimacao@.

Para visualizar o documento principal da Intimação Eletrônica e possíveis anexos, acesse a área destinada aos Usuários Externos no SEI-@sigla_orgao@ destacada em nosso Portal na Internet ou acesse diretamente o link a seguir: @link_login_usuario_externo@

A resposta à Intimação Eletrônica que é exigida deve ser realizada na área destinada aos Usuários Externos indicada acima. Com o processo aberto, acesse o botão de Ação Responder Intimação Eletrônica.

OBSERVAÇÃO: A presente reiteração ocorre quando a resposta ainda não tenha sido efetivada pelo Destinatário da Intimação, em 5 dias e 1 dia antes da Data Limite para Resposta. Caso a Intimação já tenha sido respondida, por favor, ignorar esta reiteração.


@sigla_orgao@
@descricao_orgao@
@sitio_internet_orgao@

ATENÇÃO: As informações contidas neste e-mail, incluindo seus anexos, podem ser restritas apenas à pessoa ou entidade para a qual foi endereçada. Se você não é o destinatário ou a pessoa responsável por encaminhar esta mensagem ao destinatário, você está, por meio desta, notificado que não deverá rever, retransmitir, imprimir, copiar, usar ou distribuir esta mensagem ou quaisquer anexos. Caso você tenha recebido esta mensagem por engano, por favor, contate o remetente imediatamente e em seguida apague esta mensagem.";

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
                'Peticionamento Eletrônico - Reiteração de Intimação Eletrônica que Exige Resposta',
                '@sigla_sistema@ <@email_sistema@>',
                '@email_usuario_externo@',
                'SEI - Reiteração de Intimação Eletrônica que Exige Resposta no Processo nº @processo@',
                '" . $conteudoReiteracaoExigeResposta . "',
                'S',
                'MD_PET_REITERACAO_INTIMACAO_QUE_EXIGE_RESPOSTA'
            )";

            BancoSEI::getInstance()->executarSql($insertReiteracaoExigeResposta);


            $this->logar('CRIANDO USUÁRIO do Módulo de Peticionamento');

            $objRN = new MdPetIntUsuarioRN();
            $objRN->realizarInsercoesUsuarioModuloPet();


            $this->logar('CRIANDO A TABELA md_pet_int_prazo_tacita');

            BancoSEI::getInstance()->executarSql('CREATE TABLE md_pet_int_prazo_tacita (
                id_md_pet_int_prazo_tacita ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL,
                num_prazo ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL) '
                    );
            $objInfraMetaBD->adicionarChavePrimaria('md_pet_int_prazo_tacita','pk_md_pet_int_prazo_tacita',array('id_md_pet_int_prazo_tacita'));


            $this->logar('CRIANDO A SEQUENCE seq_md_pet_int_prazo_tacita');
            BancoSEI::getInstance()->criarSequencialNativa('seq_md_pet_int_prazo_tacita', 1);


            $this->logar('INSERINDO Prazo Tácito com valor default 15');

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
            $objInfraMetaBD->adicionarChavePrimaria('md_pet_int_tipo_intimacao','pk_md_pet_int_tipo_intimacao',array('id_md_pet_int_tipo_intimacao'));


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
            $objInfraMetaBD->adicionarChavePrimaria('md_pet_int_tipo_resp','pk_md_pet_int_tipo_resp',array('id_md_pet_int_tipo_resp'));


            $this->logar('CRIANDO A SEQUENCE seq_md_pet_int_tipo_resp');
            BancoSEI::getInstance()->criarSequencialNativa('seq_md_pet_int_tipo_resp', 1);


            $this->logar('CRIANDO A TABELA md_pet_int_rel_intim_resp');
            BancoSEI::getInstance()->executarSql('CREATE TABLE md_pet_int_rel_intim_resp (
                id_md_pet_int_tipo_intimacao ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL,
                id_md_pet_int_tipo_resp ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL,
                sin_ativo ' . $objInfraMetaBD->tipoTextoFixo(1) . ' NOT NULL )');

            $objInfraMetaBD->adicionarChavePrimaria('md_pet_int_rel_intim_resp','pk_md_pet_int_rel_intim_resp',array('id_md_pet_int_tipo_intimacao','id_md_pet_int_tipo_resp'));
            $objInfraMetaBD->adicionarChaveEstrangeira('fk1_md_pet_int_rel_intim_resp', 'md_pet_int_rel_intim_resp', array('id_md_pet_int_tipo_intimacao'), 'md_pet_int_tipo_intimacao', array('id_md_pet_int_tipo_intimacao'));
            $objInfraMetaBD->adicionarChaveEstrangeira('fk2_md_pet_int_rel_intim_resp', 'md_pet_int_rel_intim_resp', array('id_md_pet_int_tipo_resp'), 'md_pet_int_tipo_resp', array('id_md_pet_int_tipo_resp'));


            $this->logar('CRIANDO A TABELA md_pet_int_serie');

            BancoSEI::getInstance()->executarSql('CREATE TABLE md_pet_int_serie (
                id_serie ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL )');

            $objInfraMetaBD->adicionarChavePrimaria('md_pet_int_serie','pk_md_pet_int_serie',array('id_serie'));
            $objInfraMetaBD->adicionarChaveEstrangeira('fk1_md_pet_int_serie', 'md_pet_int_serie', array('id_serie'), 'serie', array('id_serie'));


            $this->logar('CRIANDO A TABELA md_pet_acesso_externo');
            BancoSEI::getInstance()->executarSql('CREATE TABLE md_pet_acesso_externo (
                id_acesso_externo ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL,
                sin_proc_intercorrente ' . $objInfraMetaBD->tipoTextoFixo(1) . ' NOT NULL ,
                sin_proc_novo ' . $objInfraMetaBD->tipoTextoFixo(1) . ' NOT NULL ,
                sin_intimacao ' . $objInfraMetaBD->tipoTextoFixo(1) . ' NOT NULL ,
                sin_ativo ' . $objInfraMetaBD->tipoTextoFixo(1) . ' NOT NULL )'
                    );

            $objInfraMetaBD->adicionarChavePrimaria('md_pet_acesso_externo','fk_pet_acesso_externo_01',array('id_acesso_externo'));

            $objInfraMetaBD->adicionarChaveEstrangeira('fk1_pet_acesso_externo', 'md_pet_acesso_externo', array('id_acesso_externo'), 'acesso_externo', array('id_acesso_externo'));


            $this->logar('CRIAÇÃO DE HISTÓRICOS E GERAÇÃO DE ANDAMENTOS NO PROCESSO DA INTIMAÇÃO ELETRÔNICA');

            $texto1 = " Intimação Eletrônica expedida em @DATA_EXPEDICAO_INTIMACAO@, sobre o Documento Principal @DOCUMENTO@, para @USUARIO_EXTERNO_NOME@";

            $texto2 = "Intimação cumprida em @DATA_CUMPRIMENTO_INTIMACAO@, conforme Certidão @DOC_CERTIDAO_INTIMACAO@, por @TIPO_CUMPRIMENTO_INTIMACAO@, sobre a Intimação expedida em @DATA_EXPEDICAO_INTIMACAO@ e Documento Principal @DOCUMENTO@ para @USUARIO_EXTERNO_NOME@";

            $texto3 = "O Usuário Externo @USUARIO_EXTERNO_NOME@ efetivou Peticionamento @TIPO_PETICIONAMENTO@, tendo gerado o recibo @DOCUMENTO@";

            $texto4 = "Prorrogação Automática do Prazo Externo de possível Resposta a Intimação, relativa à Intimação expedida em @DATA_EXPEDICAO_INTIMACAO@ e ao Documento Principal @DOCUMENTO@, para @DATA_LIMITE_RESPOSTAS@";

            //@todo incrementar a seq de um jeito diferente para cada modelo de SGBD (ver pagina 8 do manual)
            $numIdTarefaMax = BancoSEI::getInstance()->getValorSequencia('seq_tarefa');

            if( $numIdTarefaMax < 1000) {
                $numIdTarefaMax = 1000;
            }

            if (BancoSEI::getInstance() instanceof InfraMySql) {
                BancoSEI::getInstance()->executarSql( " alter table seq_tarefa AUTO_INCREMENT = " . $numIdTarefaMax ."; ");
            } else if (BancoSEI::getInstance() instanceof InfraSqlServer) {
                BancoSEI::getInstance()->executarSql( "DBCC CHECKIDENT ('seq_tarefa', RESEED, " . $numIdTarefaMax . ");");
            } else if (BancoSEI::getInstance() instanceof InfraOracle) {
                BancoSEI::getInstance()->executarSql( "DROP SEQUENCE seq_tarefa");
                BancoSEI::getInstance()->criarSequencialNativa('seq_tarefa', $numIdTarefaMax);
            }

            //campo setStrSinFecharAndamentosAbertos de N para S por estar lançando andamento em processo que estara aberto na unidade (seguindo recomendação do manual do SEI)
            $tarefaDTO1 = new TarefaDTO();
            $tarefaDTO1->setNumIdTarefa( $numIdTarefaMax );
            $tarefaDTO1->setStrIdTarefaModulo('MD_PET_INTIMACAO_EXPEDIDA');
            $tarefaDTO1->setStrNome( $texto1 );
            $tarefaDTO1->setStrSinHistoricoResumido('S');
            $tarefaDTO1->setStrSinHistoricoCompleto('S');
            $tarefaDTO1->setStrSinFecharAndamentosAbertos('S');
            $tarefaDTO1->setStrSinLancarAndamentoFechado('N');
            $tarefaDTO1->setStrSinPermiteProcessoFechado('N');

            $numero = $numIdTarefaMax+1;

            if (BancoSEI::getInstance() instanceof InfraMySql) {
                BancoSEI::getInstance()->executarSql( " alter table seq_tarefa AUTO_INCREMENT = " . $numero."; ");
            } else if (BancoSEI::getInstance() instanceof InfraSqlServer) {
                BancoSEI::getInstance()->executarSql( "DBCC CHECKIDENT ('seq_tarefa', RESEED, " . $numero. ");");
            } else if (BancoSEI::getInstance() instanceof InfraOracle) {
                BancoSEI::getInstance()->executarSql( "DROP SEQUENCE seq_tarefa");
                BancoSEI::getInstance()->criarSequencialNativa('seq_tarefa', $numero);
            }

            $tarefaDTO2 = new TarefaDTO();
            $tarefaDTO2->setNumIdTarefa( $numIdTarefaMax+1 );
            $tarefaDTO2->setStrIdTarefaModulo('MD_PET_INTIMACAO_CUMPRIDA');
            $tarefaDTO2->setStrNome( $texto2 );
            $tarefaDTO2->setStrSinHistoricoResumido('S');
            $tarefaDTO2->setStrSinHistoricoCompleto('S');
            $tarefaDTO2->setStrSinFecharAndamentosAbertos('S');
            $tarefaDTO2->setStrSinLancarAndamentoFechado('N');
            $tarefaDTO2->setStrSinPermiteProcessoFechado('N');

            $numero = $numIdTarefaMax+2;

            if (BancoSEI::getInstance() instanceof InfraMySql) {
                BancoSEI::getInstance()->executarSql( " alter table seq_tarefa AUTO_INCREMENT = " . $numero."; ");
            } else if (BancoSEI::getInstance() instanceof InfraSqlServer) {
                BancoSEI::getInstance()->executarSql( "DBCC CHECKIDENT ('seq_tarefa', RESEED, " . $numero. ");");
            } else if (BancoSEI::getInstance() instanceof InfraOracle) {
                BancoSEI::getInstance()->executarSql( "DROP SEQUENCE seq_tarefa");
                BancoSEI::getInstance()->criarSequencialNativa('seq_tarefa', $numero);
            }

            $tarefaDTO3 = new TarefaDTO();
            $tarefaDTO3->setNumIdTarefa( $numIdTarefaMax+2);
            $tarefaDTO3->setStrIdTarefaModulo('MD_PET_PETICIONAMENTO_EFETIVADO');
            $tarefaDTO3->setStrNome( $texto3 );
            $tarefaDTO3->setStrSinHistoricoResumido('S');
            $tarefaDTO3->setStrSinHistoricoCompleto('S');
            $tarefaDTO3->setStrSinFecharAndamentosAbertos('S');
            $tarefaDTO3->setStrSinLancarAndamentoFechado('N');
            $tarefaDTO3->setStrSinPermiteProcessoFechado('N');

            $numero = $numIdTarefaMax+3;

            if (BancoSEI::getInstance() instanceof InfraMySql) {
                BancoSEI::getInstance()->executarSql( " alter table seq_tarefa AUTO_INCREMENT = " . $numero."; ");
            } else if (BancoSEI::getInstance() instanceof InfraSqlServer) {
                BancoSEI::getInstance()->executarSql( "DBCC CHECKIDENT ('seq_tarefa', RESEED, " . $numero. ");");
            } else if (BancoSEI::getInstance() instanceof InfraOracle) {
                BancoSEI::getInstance()->executarSql( "DROP SEQUENCE seq_tarefa");
                BancoSEI::getInstance()->criarSequencialNativa('seq_tarefa', $numero);
            }

            $tarefaDTO4 = new TarefaDTO();
            $tarefaDTO4->setNumIdTarefa( $numIdTarefaMax+3);
            $tarefaDTO4->setStrIdTarefaModulo('MD_PET_INTIMACAO_PRORROGACAO_AUTOMATICA_PRAZO_EXT');
            $tarefaDTO4->setStrNome( $texto4 );
            $tarefaDTO4->setStrSinHistoricoResumido('S');
            $tarefaDTO4->setStrSinHistoricoCompleto('S');
            $tarefaDTO4->setStrSinFecharAndamentosAbertos('S');
            $tarefaDTO4->setStrSinLancarAndamentoFechado('N');
            $tarefaDTO4->setStrSinPermiteProcessoFechado('S');

            $tarefaRN = new TarefaRN();
            $tarefaRN->cadastrar( $tarefaDTO1 );
            $tarefaRN->cadastrar( $tarefaDTO2 );
            $tarefaRN->cadastrar( $tarefaDTO3 );
            $tarefaRN->cadastrar( $tarefaDTO4 );

            //CRIANDO NOVO TIPO DE DOCUMENTO "Certidão"
            $this->logar('CRIANDO MODELO "Modulo_Peticionamento_Certidao"');
            $modeloRN = new ModeloRN();
            $modeloDTO = new ModeloDTO();
            $modeloDTO->setNumIdModelo(null);
            $modeloDTO->retTodos();
            $modeloDTO->setStrNome('Modulo_Peticionamento_Certidao');
            $modeloDTO->setStrSinAtivo('S');
            $modeloDTO = $modeloRN->cadastrar($modeloDTO);

            //adicionando as seções do modelo: Corpo de Texto e Rodapé
            $this->logar('CRIANDO SEÇAO DO MODELO - Corpo do Texto');
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

            //secao do rodapé
            $this->logar('CRIANDO SEÇAO DO MODELO - Rodapé');
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

            $secaoModeloRodapeDTO = $secaoModeloRN->cadastrar($secaoModeloRodapeDTO);

            //Obter o Grupo de Tipo de Documento "Internos do Sistema".
            $grupoSerieRN = new GrupoSerieRN();

            $this->logar('OBTER GRUPO DE TIPO DE DOCUMENTO "Internos do Sistema"');
            $grupoSerieDTO = new GrupoSerieDTO();
            $grupoSerieDTO->retTodos();
            $grupoSerieDTO->setStrNome('Internos do Sistema');
            $grupoSerieDTO->setStrSinAtivo('S');
            $grupoSerieDTO = $grupoSerieRN->consultarRN0777($grupoSerieDTO);

            //Criar o Tipo de Documento "Recibo Eletrônico de Protocolo"
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
            $serieDTO->setStrNome('Certidão de Intimação Cumprida');
            $serieDTO->setStrDescricao('Utilizado para a geração automática da Certidao em Intimações feitas pelo Peticionamentos Eletrônicos realizados por Usuário Externo.');
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

            $objInfraMetaBD->adicionarColuna('md_pet_rel_recibo_protoc', 'txt_doc_principal_intimacao',  $objInfraMetaBD->tipoTextoVariavel(250) , 'NULL');


            $this->logar('CRIANDO A TABELA md_pet_intimacao');

            BancoSEI::getInstance()->executarSql('CREATE TABLE md_pet_intimacao (
                id_md_pet_intimacao ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL ,
                id_md_pet_int_tipo_intimacao ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL ,
                sin_tipo_acesso_processo ' . $objInfraMetaBD->tipoTextoFixo(1) . ' NOT NULL )'
            );

            $objInfraMetaBD->adicionarChavePrimaria('md_pet_intimacao', 'pk_md_pet_intimacao', array('id_md_pet_intimacao'));
            $objInfraMetaBD->adicionarChaveEstrangeira('fk1_md_pet_intimacao', 'md_pet_intimacao', array('id_md_pet_int_tipo_intimacao'), 'md_pet_int_tipo_intimacao', array('id_md_pet_int_tipo_intimacao'));


            $this->logar('CRIAÇÃO DA SEQUENCE seq_md_pet_intimacao');
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


            $this->logar('CRIAÇÃO DA SEQUENCE seq_md_pet_int_rel_tipo_resp');
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


            $this->logar('CRIAÇÃO DA SEQUENCE seq_md_pet_int_rel_dest');
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


            $this->logar('CRIAÇÃO DA SEQUENCE seq_md_pet_int_protocolo');
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


            $this->logar('CRIAÇÃO DA SEQUENCE seq_md_pet_int_dest_resposta');
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


            $this->logar('CRIAÇÃO DA SEQUENCE seq_md_pet_int_rel_tpo_res_des');
            BancoSEI::getInstance()->criarSequencialNativa('seq_md_pet_int_rel_tpo_res_des', 1);


            $this->logar('CRIAÇÃO DA SEQUENCE seq_md_pet_int_resp_documento');
            BancoSEI::getInstance()->criarSequencialNativa('seq_md_pet_int_resp_documento', 1);


            $this->logar('CRIAÇÃO DOS AGENDAMENTOS AUTOMÁTICOS DO MÓDULO');

            $infraAgendamentoDTO = new InfraAgendamentoTarefaDTO();
            $infraAgendamentoDTO->retTodos();
            $infraAgendamentoDTO->setStrDescricao('Script para cumprimento automático de intimação por decurso de prazo');

            $infraAgendamentoDTO->setStrComando('MdPetAgendamentoAutomaticoRN::CumprirPorDecursoPrazoTacito');

            $infraAgendamentoDTO->setStrSinAtivo('S');
            $infraAgendamentoDTO->setStrStaPeriodicidadeExecucao( InfraAgendamentoTarefaRN::$PERIODICIDADE_EXECUCAO_HORA );
            $infraAgendamentoDTO->setStrPeriodicidadeComplemento( 23 );
            $infraAgendamentoDTO->setStrParametro( null );
            $infraAgendamentoDTO->setDthUltimaExecucao( null );
            $infraAgendamentoDTO->setDthUltimaConclusao( null );
            $infraAgendamentoDTO->setStrSinSucesso( 'S' );
            $infraAgendamentoDTO->setStrEmailErro( null );

            $infraAgendamentoRN = new InfraAgendamentoTarefaRN();
            $infraAgendamentoDTO = $infraAgendamentoRN->cadastrar( $infraAgendamentoDTO );

            $infraAgendamentoDTO = new InfraAgendamentoTarefaDTO();
            $infraAgendamentoDTO->retTodos();
            $infraAgendamentoDTO->setStrDescricao('Script para atualizar os estados das Intimações com Prazo Externo Vencido');

            $infraAgendamentoDTO->setStrComando('MdPetAgendamentoAutomaticoRN::atualizarEstadoIntimacoesPrazoExternoVencido');

            $infraAgendamentoDTO->setStrSinAtivo('S');
            $infraAgendamentoDTO->setStrStaPeriodicidadeExecucao( InfraAgendamentoTarefaRN::$PERIODICIDADE_EXECUCAO_HORA );
            $infraAgendamentoDTO->setStrPeriodicidadeComplemento( 0 );
            $infraAgendamentoDTO->setStrParametro( null );
            $infraAgendamentoDTO->setDthUltimaExecucao( null );
            $infraAgendamentoDTO->setDthUltimaConclusao( null );
            $infraAgendamentoDTO->setStrSinSucesso( 'S' );
            $infraAgendamentoDTO->setStrEmailErro( null );

            $infraAgendamentoRN = new InfraAgendamentoTarefaRN();
            $infraAgendamentoDTO = $infraAgendamentoRN->cadastrar( $infraAgendamentoDTO );

            $infraAgendamentoDTO = new InfraAgendamentoTarefaDTO();
            $infraAgendamentoDTO->retTodos();
            $infraAgendamentoDTO->setStrDescricao('Dispara E-mails do Sistema do Módulo de Peticionamento e Intimação Eletrônicos de Reiteração de Intimação Eletrônica que Exige Resposta pendentes de Resposta pelo Usuário Externo');

            $infraAgendamentoDTO->setStrComando('MdPetAgendamentoAutomaticoRN::ReiterarIntimacaoExigeResposta');

            $infraAgendamentoDTO->setStrSinAtivo('S');
            $infraAgendamentoDTO->setStrStaPeriodicidadeExecucao( InfraAgendamentoTarefaRN::$PERIODICIDADE_EXECUCAO_HORA );
            $infraAgendamentoDTO->setStrPeriodicidadeComplemento( 7 );
            $infraAgendamentoDTO->setStrParametro( null );
            $infraAgendamentoDTO->setDthUltimaExecucao( null );
            $infraAgendamentoDTO->setDthUltimaConclusao( null );
            $infraAgendamentoDTO->setStrSinSucesso( 'S' );
            $infraAgendamentoDTO->setStrEmailErro( null );

            $infraAgendamentoRN = new InfraAgendamentoTarefaRN();
            $infraAgendamentoDTO = $infraAgendamentoRN->cadastrar( $infraAgendamentoDTO );

            //checar se precisa atualizar infra_parametro ID_SERIE_RECIBO_MODULO_PETICIONAMENTO
            $idParamAntigo = 'ID_SERIE_RECIBO_MODULO_PETICIONAMENTO';
            $objInfraParamRN = new InfraParametroRN();
            $objInfraParamDTO = new InfraParametroDTO();
            $objInfraParamDTO->retTodos();
            $objInfraParamDTO->setStrNome( $idParamAntigo );

            $arrObjInfraParamDTO = $objInfraParamRN->listar( $objInfraParamDTO );

            if( is_array( $arrObjInfraParamDTO ) && count( $arrObjInfraParamDTO ) > 0){
                BancoSEI::getInstance()->executarSql("UPDATE infra_parametro SET nome ='" . MdPetAtualizadorSeiRN::$MD_PET_ID_SERIE_RECIBO. "'  WHERE nome = '" . $idParamAntigo . "'");
            }

            //Alteração na tarefa "Cancelada disponibilização de acesso externo", passando a permitir em PROCESSO FECHADO
            $tarefaDTO = new TarefaDTO();
            $tarefaDTO->setNumIdTarefa(90);
            $tarefaDTO->setStrSinPermiteProcessoFechado('S');

            $tarefaRN = new TarefaRN();
            $tarefaRN->alterar( $tarefaDTO );


            $this->logar('ATUALIZANDO PARÂMETRO '.$this->nomeParametroModulo.' NA TABELA infra_parametro PARA CONTROLAR A VERSÃO DO MÓDULO');
            BancoSEI::getInstance()->executarSql('UPDATE infra_parametro SET valor = \'2.0.0\' WHERE nome = \'' . $this->nomeParametroModulo . '\' ');

    }

    //Contem atualizações da versao 2.0.1
    protected function instalarv201(){

        $this->logar('EXECUTANDO A INSTALAÇÃO/ATUALIZAÇÃO DA VERSÃO 2.0.1 DO '.$this->nomeDesteModulo.' NA BASE DO SEI');

        $this->logar('ATUALIZANDO PARÂMETRO '.$this->nomeParametroModulo.' NA TABELA infra_parametro PARA CONTROLAR A VERSÃO DO MÓDULO');
        BancoSEI::getInstance()->executarSql('UPDATE infra_parametro SET valor = \'2.0.1\' WHERE nome = \'' . $this->nomeParametroModulo . '\' ');

    }

    //Contem atualizações da versao 2.0.2
    protected function instalarv202(){

        $this->logar('EXECUTANDO A INSTALAÇÃO/ATUALIZAÇÃO DA VERSÃO 2.0.2 DO '.$this->nomeDesteModulo.' NA BASE DO SEI');

        //checando permissoes na base de dados
        $objInfraMetaBD = new InfraMetaBD(BancoSEI::getInstance());

        if (count($objInfraMetaBD->obterTabelas('md_pet_indisp_anexo')) > 0) {
            $this->logar('DELETANDO A TABELA md_pet_indisp_anexo');
            BancoSEI::getInstance()->executarSql('DROP TABLE md_pet_indisp_anexo');

            $this->logar('DELETANDO A SEQUENCE seq_md_pet_indisp_anexo');
            if ( (BancoSEI::getInstance() instanceof InfraMySql) OR (BancoSEI::getInstance() instanceof InfraSqlServer) ) {
                BancoSEI::getInstance()->executarSql( "DROP TABLE seq_md_pet_indisp_anexo");
            } else if (BancoSEI::getInstance() instanceof InfraOracle) {
                BancoSEI::getInstance()->executarSql( "DROP SEQUENCE seq_md_pet_indisp_anexo");
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
        if (count($colunasTabela) == 1 || $colunasTabela[0]['column_name'] == 'nome_tipo_intimacao') {
            $this->logar('DELETANDO A COLUNA md_pet_rel_recibo_protoc.nome_tipo_intimacao');
            $objInfraMetaBD->excluirColuna('md_pet_rel_recibo_protoc', 'nome_tipo_intimacao');
        }

        $colunasTabela = $objInfraMetaBD->obterColunasTabela('md_pet_rel_recibo_protoc', 'nome_tipo_resposta');
        if (count($colunasTabela) == 1 || $colunasTabela[0]['column_name'] == 'nome_tipo_resposta') {
            $this->logar('DELETANDO A COLUNA md_pet_rel_recibo_protoc.nome_tipo_resposta');
            $objInfraMetaBD->excluirColuna('md_pet_rel_recibo_protoc', 'nome_tipo_resposta');
        }

        if (count($objInfraMetaBD->obterTabelas('md_pet_usu_ext_processo')) == 1) {
            $this->logar('DELETANDO A TABELA md_pet_usu_ext_processo');
            BancoSEI::getInstance()->executarSql('DROP TABLE md_pet_usu_ext_processo');
        }

        $this->logar('ATUALIZANDO PARÂMETRO '.$this->nomeParametroModulo.' NA TABELA infra_parametro PARA CONTROLAR A VERSÃO DO MÓDULO');
        BancoSEI::getInstance()->executarSql('UPDATE infra_parametro SET valor = \'2.0.2\' WHERE nome = \'' . $this->nomeParametroModulo . '\' ');

    }

    private function existeIdEmailSistemaPecitionamento(){
        $this->logar('VERIFICANDO A EXISTENCIA DE MODELOS DE EMAIL PARA PETICIONAMENTO');
        $sql = "select 
            id_email_sistema 
            from email_sistema 
            where 
                id_email_sistema in (3001,3002)";
        $rs = BancoSEI::getInstance()->consultarSql($sql);
        return (count($rs) > 0) ? true : false;
    }

    private function atualizarIdEmailSistemaAlertaPecitionamento(){
        $this->logar('ATUALIZANDO O IDENTIFICADOR DO MODELO DE EMAIL PARA PETICIONAMENTO DA CONSTANTE MD_PET_ALERTA_PETICIONAMENTO_UNIDADES');
        $idEmailSistema = $this->retornarMaxIdEmailSistema();
        BancoSEI::getInstance()->executarSql('update email_sistema SET id_email_sistema = ' . $idEmailSistema . ', id_email_sistema_modulo = \'MD_PET_ALERTA_PETICIONAMENTO_UNIDADES\' WHERE id_email_sistema = 3002');
    }

    private function atualizarIdEmailSistemaConfirmacaoPeticionamento(){
        $this->logar('ATUALIZANDO O IDENTIFICADOR DO MODELO DE EMAIL PARA PETICIONAMENTO DA CONSTANTE MD_PET_CONFIRMACAO_PETICIONAMENTO_USUARIO_EXTERNO');
        $idEmailSistema = $this->retornarMaxIdEmailSistema();
        BancoSEI::getInstance()->executarSql('update email_sistema SET id_email_sistema = ' . $idEmailSistema . ', id_email_sistema_modulo = \'MD_PET_CONFIRMACAO_PETICIONAMENTO_USUARIO_EXTERNO\' WHERE  id_email_sistema = 3001');
    }

    private function retornarMaxIdEmailSistema(){
        $this->logar('BUSCANDO O PROXIMO ID DISPONIVEL NA TABELA EMAIL_SISTEMA ');
        $sql = "select id_email_sistema from email_sistema where id_email_sistema > 999";
        $rs = BancoSEI::getInstance()->consultarSql($sql);

        $maxIdEmailSistema = (1000 + count($rs));
        $indiceAnterior = 0;
        foreach ($rs as $i => $r) {
            if ($i == 0 && $r['id_email_sistema'] > 1000) {
                $maxIdEmailSistema = 1000;
                break;
            }

            if (($r['id_email_sistema'] - $rs[$indiceAnterior]['id_email_sistema']) > 1) {
                $maxIdEmailSistema = $rs[$indiceAnterior]['id_email_sistema'] + 1;
                break;
            }
            $indiceAnterior = $i;
        }
        return $maxIdEmailSistema;
    }

}
?>