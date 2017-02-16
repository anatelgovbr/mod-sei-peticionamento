<?
/**
 * ANATEL
 *
 * 21/05/2016 - criado por marcelo.bezerra - CAST
 *
 */

require_once dirname(__FILE__) . '/../../../SEI.php';

class AtualizadorModuloPeticionamentoRN extends InfraRN
{

    private $numSeg = 0;
    private $versaoAtualDesteModulo = '1.1.0';
    private $nomeDesteModulo = 'PETICIONAMENTO E INTIMAÇÃO ELETRÔNICOS';
    private $nomeParametroModulo = 'VERSAO_MODULO_PETICIONAMENTO';
    private $historicoVersoes = array('0.0.1', '0.0.2', '1.0.3', '1.0.4', '1.1.0');

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

    private function logar($strMsg)
    {
        InfraDebug::getInstance()->gravar($strMsg);
        flush();
    }

    private function finalizar($strMsg = null, $bolErro)
    {

        if (!$bolErro) {
            $this->numSeg = InfraUtil::verificarTempoProcessamento($this->numSeg);
            $this->logar('TEMPO TOTAL DE EXECUÇÃO: ' . $this->numSeg . ' s');
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

    //Contem atualizações da versao 1.1.0
    protected function instalarv110()
    {
        try {

            $objInfraMetaBD = new InfraMetaBD(BancoSEI::getInstance());
            $this->logar('EXECUTANDO A INSTALACAO DA VERSAO 1.1.0 DO MODULO ' . $this->nomeDesteModulo . ' NA BASE DO SEI');

            // INICIO 7048
            //Cria a tabela de tipo de resposta
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

            if (BancoSEI::getInstance() instanceof InfraMySql) {
                BancoSEI::getInstance()->executarSql('create table seq_md_pet_criterio (id bigint not null primary key AUTO_INCREMENT, campo char(1) null) AUTO_INCREMENT = 1');
            } else if (BancoSEI::getInstance() instanceof InfraSqlServer) {
                BancoSEI::getInstance()->executarSql('create table seq_md_pet_criterio (id bigint identity(1,1), campo char(1) null)');
            } else if (BancoSEI::getInstance() instanceof InfraOracle) {
                BancoSEI::getInstance()->criarSequencialNativa('seq_md_pet_criterio', 1);
            }
            // FIM 7048
            
            //Criando campo md_pet_rel_recibo_protoc.id_protocolo_relacionado caso ainda nao exista
            $coluna = $objInfraMetaBD->obterColunasTabela('md_pet_rel_recibo_protoc', 'id_protocolo_relacionado');
            
            if( $coluna == null || !is_array( $coluna ) ){
            	
            	$objInfraMetaBD->adicionarColuna('md_pet_rel_recibo_protoc', 'id_protocolo_relacionado', '' . $objInfraMetaBD->tipoNumeroGrande() , 'NULL');
            	
            	$objInfraMetaBD->adicionarChaveEstrangeira('fk5_md_pet_rel_recibo_protoc', 'md_pet_rel_recibo_protoc', array('id_protocolo_relacionado'), 'protocolo', array('id_protocolo'));
            	
            }

            //Atualizando parametro para controlar versao do modulo
            $this->logar('ATUALIZANDO PARAMETRO ' . $this->nomeParametroModulo . ' NA TABELA infra_parametro PARA CONTROLAR A VERSÃO DO MÓDULO');
            BancoSEI::getInstance()->executarSql('UPDATE infra_parametro SET valor = \'1.1.0\' WHERE nome = \'' . $this->nomeParametroModulo . '\' ');

        } catch (Exception $e) {
            $this->logar($e->getTraceAsString());
            print_r($e);
            die();
        }
    }

//Contem atualizações da versao 1.0.4
    protected function instalarv104()
    {
        try {
        	
            $objInfraMetaBD = new InfraMetaBD(BancoSEI::getInstance());
            $this->logar('EXECUTANDO A INSTALACAO DA VERSAO 1.0.4 DO MODULO ' . $this->nomeDesteModulo . ' NA BASE DO SEI');

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
            $objTarjaAssinaturaDTO->setStrStaTarjaAssinatura( AssinaturaPeticionamentoRN::$TT_ASSINATURA_SENHA_PETICIONAMENTO );
            
            $objTarjaAssinaturaDTO->setStrTexto('<hr style="margin: 0 0 4px 0;" />  <table>    <tr>      <td>  @logo_assinatura@      </td>      <td>  <p style="margin:0;text-align: left; font-size:11pt;font-family: Calibri;">Documento assinado eletronicamente por <b>@nome_assinante@</b>, <b>@tratamento_assinante@</b>, em @data_assinatura@, às @hora_assinatura@, conforme horário oficial de Brasília, com fundamento no art. 6º, § 1º, do <a title="Acesse o Decreto" href="http://www.planalto.gov.br/ccivil_03/_Ato2015-2018/2015/Decreto/D8539.htm" target="_blank">Decreto nº 8.539, de 8 de outubro de 2015</a>.</p>      </td>    </tr>  </table>');
            
            $objTarjaAssinaturaDTO->setStrLogo('iVBORw0KGgoAAAANSUhEUgAAAFkAAAA8CAMAAAA67OZ0AAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAADTtpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+Cjx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDQuMi4yLWMwNjMgNTMuMzUyNjI0LCAyMDA4LzA3LzMwLTE4OjEyOjE4ICAgICAgICAiPgogPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4KICA8cmRmOkRlc2NyaXB0aW9uIHJkZjphYm91dD0iIgogICAgeG1sbnM6ZGM9Imh0dHA6Ly9wdXJsLm9yZy9kYy9lbGVtZW50cy8xLjEvIgogICAgeG1sbnM6eG1wUmlnaHRzPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvcmlnaHRzLyIKICAgIHhtbG5zOnBob3Rvc2hvcD0iaHR0cDovL25zLmFkb2JlLmNvbS9waG90b3Nob3AvMS4wLyIKICAgIHhtbG5zOklwdGM0eG1wQ29yZT0iaHR0cDovL2lwdGMub3JnL3N0ZC9JcHRjNHhtcENvcmUvMS4wL3htbG5zLyIKICAgeG1wUmlnaHRzOldlYlN0YXRlbWVudD0iIgogICBwaG90b3Nob3A6QXV0aG9yc1Bvc2l0aW9uPSIiPgogICA8ZGM6cmlnaHRzPgogICAgPHJkZjpBbHQ+CiAgICAgPHJkZjpsaSB4bWw6bGFuZz0ieC1kZWZhdWx0Ii8+CiAgICA8L3JkZjpBbHQ+CiAgIDwvZGM6cmlnaHRzPgogICA8ZGM6Y3JlYXRvcj4KICAgIDxyZGY6U2VxPgogICAgIDxyZGY6bGk+QWxiZXJ0byBCaWdhdHRpPC9yZGY6bGk+CiAgICA8L3JkZjpTZXE+CiAgIDwvZGM6Y3JlYXRvcj4KICAgPGRjOnRpdGxlPgogICAgPHJkZjpBbHQ+CiAgICAgPHJkZjpsaSB4bWw6bGFuZz0ieC1kZWZhdWx0Ii8+CiAgICA8L3JkZjpBbHQ+CiAgIDwvZGM6dGl0bGU+CiAgIDx4bXBSaWdodHM6VXNhZ2VUZXJtcz4KICAgIDxyZGY6QWx0PgogICAgIDxyZGY6bGkgeG1sOmxhbmc9IngtZGVmYXVsdCIvPgogICAgPC9yZGY6QWx0PgogICA8L3htcFJpZ2h0czpVc2FnZVRlcm1zPgogICA8SXB0YzR4bXBDb3JlOkNyZWF0b3JDb250YWN0SW5mbwogICAgSXB0YzR4bXBDb3JlOkNpQWRyRXh0YWRyPSIiCiAgICBJcHRjNHhtcENvcmU6Q2lBZHJDaXR5PSIiCiAgICBJcHRjNHhtcENvcmU6Q2lBZHJSZWdpb249IiIKICAgIElwdGM0eG1wQ29yZTpDaUFkclBjb2RlPSIiCiAgICBJcHRjNHhtcENvcmU6Q2lBZHJDdHJ5PSIiCiAgICBJcHRjNHhtcENvcmU6Q2lUZWxXb3JrPSIiCiAgICBJcHRjNHhtcENvcmU6Q2lFbWFpbFdvcms9IiIKICAgIElwdGM0eG1wQ29yZTpDaVVybFdvcms9IiIvPgogIDwvcmRmOkRlc2NyaXB0aW9uPgogPC9yZGY6UkRGPgo8L3g6eG1wbWV0YT4KICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgIAo8P3hwYWNrZXQgZW5kPSJ3Ij8+RO84nQAAAwBQTFRFamts+fn5mp6hc3Nz9fX1U1NTS0tKnaGk6unqzM3P7e3u8fHxuLm7/Pz8lZmc2dnZxcXGWlpavr29wsLCp6eniYmKhYaGZWZmkpaZ0dHS5eXlkZGSrq2utbW2XV1d4uHhfX1+sbGy1dXW3d3dqampgYGCjY2OyMnKYWJi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');
            
            $objTarjaAssinaturaDTO->setStrSinAtivo('S');
            
            $objTarjaAssinaturaBD = new TarjaAssinaturaBD($this->getObjInfraIBanco());
            $objTarjaAssinaturaDTO = $objTarjaAssinaturaBD->cadastrar( $objTarjaAssinaturaDTO );
            
            //Atualizando parametro para controlar versao do modulo
            $this->logar('ATUALIZANDO PARAMETRO ' . $this->nomeParametroModulo . ' NA TABELA infra_parametro PARA CONTROLAR A VERSÃO DO MÓDULO');
            BancoSEI::getInstance()->executarSql('UPDATE infra_parametro SET valor = \'1.0.4\' WHERE nome = \'' . $this->nomeParametroModulo . '\' ');
            
        } catch (Exception $e) {
            $this->logar($e->getTraceAsString());
            print_r($e);
            die();
        }
    }

    //Contem atualizações da versao 1.0.0
    protected function instalarv100()
    {

        try {

            $objInfraMetaBD = new InfraMetaBD(BancoSEI::getInstance());

            $this->logar('EXECUTANDO A INSTALACAO DA VERSAO 1.0.3 DO MODULO ' . $this->nomeDesteModulo . ' NA BASE DO SEI');
            $this->logar('CRIANDO A TABELA md_pet_hipotese_legal');

            BancoSEI::getInstance()->executarSql('CREATE TABLE md_pet_hipotese_legal (
			id_md_pet_hipotese_legal ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL )');

            $objInfraMetaBD->adicionarChavePrimaria('md_pet_hipotese_legal', 'pk_md_pet_hipotese_legal', array('id_md_pet_hipotese_legal'));

            $objInfraMetaBD->adicionarChaveEstrangeira('fk_id_md_pet_hip_legal1', 'md_pet_hipotese_legal',
                array('id_md_pet_hipotese_legal'), 'hipotese_legal', array('id_hipotese_legal'));

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
	)');

            //Tabelas Abaixo é o problema da modificação da PK (Pk deixou de ser composta e passou a ter SEQ)
            $this->logar('RECRIANDO tabela md_pet_rel_tp_processo_serie (renomeada para md_pet_rel_tp_proc_serie)');
            BancoSEI::getInstance()->executarSql('DROP TABLE md_pet_rel_tp_processo_serie');

            BancoSEI::getInstance()->executarSql('CREATE TABLE md_pet_rel_tp_proc_serie (
			id_md_pet_rel_tipo_proc ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL,
			id_md_pet_tipo_processo ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL,
			id_serie ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL,
			sta_tp_doc ' . $objInfraMetaBD->tipoTextoFixo(1) . ' NOT NULL ) ');

            //tabela SEQ
            $objInfraMetaBD->adicionarChavePrimaria('md_pet_rel_tp_proc_serie', 'pk_id_md_pet_rel_tipo_proc', array('id_md_pet_rel_tipo_proc'));

            $objInfraMetaBD->adicionarChaveEstrangeira('fk_md_pet_rel_tp_proc_serie1', 'md_pet_rel_tp_proc_serie',
                array('id_md_pet_tipo_processo'), 'md_pet_tipo_processo', array('id_md_pet_tipo_processo'));

            $objInfraMetaBD->adicionarChaveEstrangeira('fk_md_pet_rel_tp_proc_serie2', 'md_pet_rel_tp_proc_serie',
                array('id_serie'), 'serie', array('id_serie'));

            if (BancoSEI::getInstance() instanceof InfraMySql) {
                BancoSEI::getInstance()->executarSql('create table seq_md_pet_rel_tp_proc_serie (id bigint not null primary key AUTO_INCREMENT, campo char(1) null) AUTO_INCREMENT = 1');
            } else if (BancoSEI::getInstance() instanceof InfraSqlServer) {
                BancoSEI::getInstance()->executarSql('create table seq_md_pet_rel_tp_proc_serie (id bigint identity(1,1), campo char(1) null)');
            } else if (BancoSEI::getInstance() instanceof InfraOracle) {
                BancoSEI::getInstance()->criarSequencialNativa('seq_md_pet_rel_tp_proc_serie', 1);
            }

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

            //Criar o Grupo de Tipo de Documento “Internos do Sistema”.
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

            //Criar o Tipo de Documento “Recibo Eletrônico de Protocolo”
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

            //adicoes SEIv3
            $serieDTO->setNumIdTipoFormulario(null);
            $serieDTO->setArrObjSerieRestricaoDTO(array());

            $serieDTO = $serieRN->cadastrarRN0642($serieDTO);

            $this->logar('ATUALIZANDO INFRA_PARAMETRO (ID_SERIE_RECIBO_MODULO_PETICIONAMENTO)');
            $nomeParamIdSerie = 'ID_SERIE_RECIBO_MODULO_PETICIONAMENTO';
            BancoSEI::getInstance()->executarSql('INSERT INTO infra_parametro ( valor, nome )  VALUES (\'' . $serieDTO->getNumIdSerie() . '\' , \'' . $nomeParamIdSerie . '\' ) ');

            //Atualizando parametro para controlar versao do modulo
            $this->logar('ATUALIZANDO PARAMETRO ' . $this->nomeParametroModulo . ' NA TABELA infra_parametro PARA CONTROLAR A VERSÃO DO MÓDULO');
            BancoSEI::getInstance()->executarSql('UPDATE infra_parametro SET valor = \'1.0.3\' WHERE nome = \'' . $this->nomeParametroModulo . '\' ');

        } catch (Exception $e) {
            $this->logar($e->getTraceAsString());
            print_r($e);
            die();
        }

    }

    //Contem atualizações da versao 0.0.2
    protected function instalarv002()
    {

        $objInfraMetaBD = new InfraMetaBD(BancoSEI::getInstance());
        $this->logar('EXECUTANDO A INSTALACAO DA VERSAO 0.0.2 DO MODULO ' . $this->nomeDesteModulo . ' NA BASE DO SEI');
        $this->logar('CRIANDO A TABELA md_pet_usu_externo_menu E SUA sequence');

        BancoSEI::getInstance()->executarSql('CREATE TABLE md_pet_usu_externo_menu( id_md_pet_usu_externo_menu ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL,
		id_conjunto_estilos ' . $objInfraMetaBD->tipoNumero() . ' NULL ,
		nome ' . $objInfraMetaBD->tipoTextoVariavel(30) . ' NOT NULL ,
		tipo ' . $objInfraMetaBD->tipoTextoFixo(1) . ' NOT NULL ,
		url ' . $objInfraMetaBD->tipoTextoVariavel(2083) . ' NULL ,
		conteudo_html ' . $objInfraMetaBD->tipoTextoGrande() . ' NULL,
		sin_ativo  ' . $objInfraMetaBD->tipoTextoFixo(1) . ' NOT NULL ) ');

        $objInfraMetaBD->adicionarChavePrimaria('md_pet_usu_externo_menu', 'pk_md_pet_usu_externo_menu', array('id_md_pet_usu_externo_menu'));

        $objInfraMetaBD->adicionarChaveEstrangeira('fk_md_pet_menu_cj_est_01',
            'md_pet_usu_externo_menu',
            array('id_conjunto_estilos'),
            'conjunto_estilos', array('id_conjunto_estilos'));

        if (BancoSEI::getInstance() instanceof InfraMySql) {
            BancoSEI::getInstance()->executarSql('create table seq_md_pet_usu_externo_menu (id bigint not null primary key AUTO_INCREMENT, campo char(1) null) AUTO_INCREMENT = 1');
        } else if (BancoSEI::getInstance() instanceof InfraSqlServer) {
            BancoSEI::getInstance()->executarSql('create table seq_md_pet_usu_externo_menu (id bigint identity(1,1), campo char(1) null)');
        } else if (BancoSEI::getInstance() instanceof InfraOracle) {
            BancoSEI::getInstance()->criarSequencialNativa('seq_md_pet_usu_externo_menu', 1);
        }

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

        $this->logar('CRIANDO A TABELA md_pet_usu_ext_processo E SUA sequence');

        //Inserindo tabelas referentes ao Recibo Eletronico de Protocolo
        BancoSEI::getInstance()->executarSql('CREATE TABLE md_pet_usu_ext_processo (
		id_md_pet_usu_externo_processo ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL,
		id_md_pet_tipo_processo ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL,
		especificacao ' . $objInfraMetaBD->tipoTextoVariavel(50) . ' NULL,
		tipo_interessado ' . $objInfraMetaBD->tipoTextoFixo(1) . ' NULL,
		id_usuario_externo ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL,
		data_hora_recebimento ' . $objInfraMetaBD->tipoDataHora() . ' NULL,
		ip_usuario ' . $objInfraMetaBD->tipoTextoVariavel(60) . ' NULL,
		numero_processo ' . $objInfraMetaBD->tipoTextoVariavel(40) . ' NULL,
		sin_ativo  ' . $objInfraMetaBD->tipoTextoFixo(1) . ' NOT NULL ) ');

        $objInfraMetaBD->adicionarChavePrimaria('md_pet_usu_ext_processo', 'pk_md_pet_usu_externo_processo', array('id_md_pet_usu_externo_processo'));

        if (BancoSEI::getInstance() instanceof InfraMySql) {
            BancoSEI::getInstance()->executarSql('create table seq_md_pet_usu_ext_processo (id bigint not null primary key AUTO_INCREMENT, campo char(1) null) AUTO_INCREMENT = 1');
        } else if (BancoSEI::getInstance() instanceof InfraSqlServer) {
            BancoSEI::getInstance()->executarSql('create table seq_md_pet_usu_ext_processo (id bigint identity(1,1), campo char(1) null)');
        } else if (BancoSEI::getInstance() instanceof InfraOracle) {
            BancoSEI::getInstance()->criarSequencialNativa('seq_md_pet_usu_ext_processo', 1);
        }

        //Tabelas relacionais com Tipos de Contatos permitidos para Cadastro e para Seleção
        $this->logar('CRIANDO A TABELA md_pet_rel_tp_ctx_contato');

        //veraao SEIv2.6  id_tipo_contexto_contato ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL,
        //versao SEIv3.0
        BancoSEI::getInstance()->executarSql('CREATE TABLE md_pet_rel_tp_ctx_contato (
		id_tipo_contato ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL,
		sin_cadastro_interessado ' . $objInfraMetaBD->tipoTextoFixo(1) . ' NOT NULL,
		sin_selecao_interessado ' . $objInfraMetaBD->tipoTextoFixo(1) . ' NOT NULL,
		id_md_pet_rel_tp_ctx_contato ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL
		) ');

        $objInfraMetaBD->adicionarChavePrimaria('md_pet_rel_tp_ctx_contato', 'pk1_md_pet_rel_tp_ctx_cont', array('id_md_pet_rel_tp_ctx_contato'));

        //versao SEIv2.6
        //$objInfraMetaBD->adicionarChaveEstrangeira('fk_md_pet_rel_tp_ctx_cont_1','md_pet_rel_tp_ctx_contato',
        //array('id_tipo_contexto_contato'),
        //'tipo_contexto_contato',array('id_tipo_contexto_contato'));

        //versao SEIv3.0
        $objInfraMetaBD->adicionarChaveEstrangeira('fk_md_pet_rel_tp_ctx_cont_1', 'md_pet_rel_tp_ctx_contato',
            array('id_tipo_contato'),
            'tipo_contato', array('id_tipo_contato'));

        if (BancoSEI::getInstance() instanceof InfraMySql) {
            BancoSEI::getInstance()->executarSql('create table seq_md_pet_rel_tp_ctx_contato (id bigint not null primary key AUTO_INCREMENT, campo char(1) null) AUTO_INCREMENT = 1');
        } else if (BancoSEI::getInstance() instanceof InfraSqlServer) {
            BancoSEI::getInstance()->executarSql('create table seq_md_pet_rel_tp_ctx_contato (id bigint identity(1,1), campo char(1) null)');
        } else if (BancoSEI::getInstance() instanceof InfraOracle) {
            BancoSEI::getInstance()->criarSequencialNativa('seq_md_pet_rel_tp_ctx_contato', 1);
        }

        //Tabelas referentes ao Recibo Eletronico de Protocolo
        $this->logar('CRIANDO A TABELA md_pet_rel_recibo_protoc E SUA sequence');

        BancoSEI::getInstance()->executarSql('CREATE TABLE md_pet_rel_recibo_protoc (
		id_md_pet_rel_recibo_protoc ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL,
		id_protocolo ' . $objInfraMetaBD->tipoNumeroGrande() . ' NOT NULL,
		id_protocolo_relacionado ' . $objInfraMetaBD->tipoNumeroGrande() . ' NULL,
		id_usuario ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL,
		ip_usuario ' . $objInfraMetaBD->tipoTextoVariavel(500) . ' NOT NULL,
		data_hora_recebimento_final ' . $objInfraMetaBD->tipoDataHora() . ' NULL,
		sin_ativo ' . $objInfraMetaBD->tipoTextoFixo(1) . ' NOT NULL,
		sta_tipo_peticionamento ' . $objInfraMetaBD->tipoTextoVariavel(1) . ' NULL )');

        $objInfraMetaBD->adicionarChavePrimaria('md_pet_rel_recibo_protoc', 'pk1_md_pet_rel_recibo_protoc', array('id_md_pet_rel_recibo_protoc'));

        $objInfraMetaBD->adicionarChaveEstrangeira('fk1_md_pet_rel_recibo_protoc', 'md_pet_rel_recibo_protoc', array('id_protocolo'), 'protocolo', array('id_protocolo'));
        $objInfraMetaBD->adicionarChaveEstrangeira('fk2_md_pet_rel_recibo_protoc', 'md_pet_rel_recibo_protoc', array('id_usuario'), 'usuario', array('id_usuario'));
        $objInfraMetaBD->adicionarChaveEstrangeira('fk3_md_pet_rel_recibo_protoc', 'md_pet_rel_recibo_protoc', array('id_protocolo_relacionado'), 'protocolo', array('id_protocolo'));

        if (BancoSEI::getInstance() instanceof InfraMySql) {
            BancoSEI::getInstance()->executarSql('create table seq_md_pet_rel_recibo_protoc (id bigint not null primary key AUTO_INCREMENT, campo char(1) null) AUTO_INCREMENT = 1');
        } else if (BancoSEI::getInstance() instanceof InfraSqlServer) {
            BancoSEI::getInstance()->executarSql('create table seq_md_pet_rel_recibo_protoc (id bigint identity(1,1), campo char(1) null)');
        } else if (BancoSEI::getInstance() instanceof InfraOracle) {
            BancoSEI::getInstance()->criarSequencialNativa('seq_md_pet_rel_recibo_protoc', 1);
        }

        //Tabelas de recibo X documentos
        $this->logar('CRIANDO A TABELA md_pet_rel_recibo_docanexo E SUA sequence');

        BancoSEI::getInstance()->executarSql('CREATE TABLE md_pet_rel_recibo_docanexo (
		id_md_pet_rel_recibo_docanexo ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL,
		id_md_pet_rel_recibo_protoc ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL,
		formato_documento ' . $objInfraMetaBD->tipoTextoFixo(1) . ' NULL,
		id_documento ' . $objInfraMetaBD->tipoNumeroGrande() . ' NULL,
		id_anexo ' . $objInfraMetaBD->tipoNumero() . ' NULL,
		classificacao_documento ' . $objInfraMetaBD->tipoTextoFixo(1) . ' NOT NULL )');

        $objInfraMetaBD->adicionarChavePrimaria('md_pet_rel_recibo_docanexo', 'pk1_md_pet_rel_recibo_docanexo', array('id_md_pet_rel_recibo_docanexo'));

        $objInfraMetaBD->adicionarChaveEstrangeira('fk_pet_rel_recibo_docanexo_01', 'md_pet_rel_recibo_docanexo', array('id_md_pet_rel_recibo_protoc'), 'md_pet_rel_recibo_protoc', array('id_md_pet_rel_recibo_protoc'));
        $objInfraMetaBD->adicionarChaveEstrangeira('fk_pet_rel_recibo_docanexo_02', 'md_pet_rel_recibo_docanexo', array('id_documento'), 'documento', array('id_documento'));
        $objInfraMetaBD->adicionarChaveEstrangeira('fk_pet_rel_recibo_docanexo_03', 'md_pet_rel_recibo_docanexo', array('id_anexo'), 'anexo', array('id_anexo'));

        if (BancoSEI::getInstance() instanceof InfraMySql) {
            BancoSEI::getInstance()->executarSql('create table seq_md_pet_rel_recibo_docanexo (id bigint not null primary key AUTO_INCREMENT, campo char(1) null) AUTO_INCREMENT = 1');
        } else if (BancoSEI::getInstance() instanceof InfraSqlServer) {
            BancoSEI::getInstance()->executarSql('create table seq_md_pet_rel_recibo_docanexo (id bigint identity(1,1), campo char(1) null)');
        } else if (BancoSEI::getInstance() instanceof InfraOracle) {
            BancoSEI::getInstance()->criarSequencialNativa('seq_md_pet_rel_recibo_docanexo', 1);
        }

        //Atualizando parametro para controlar versao do modulo
        $this->logar('ATUALIZANDO PARAMETRO ' . $this->nomeParametroModulo . ' NA TABELA infra_parametro PARA CONTROLAR A VERSÃO DO MÓDULO');
        BancoSEI::getInstance()->executarSql('UPDATE infra_parametro SET valor = \'0.0.2\' WHERE nome = \'' . $this->nomeParametroModulo . '\' ');

    }

    //Contem atualizações da versao 0.0.1
    protected function instalarv001()
    {

        $objInfraMetaBD = new InfraMetaBD(BancoSEI::getInstance());

        $this->logar('EXECUTANDO A INSTALACAO DA VERSAO 0.0.1 DO MODULO ' . $this->nomeDesteModulo . ' NA BASE DO SEI');
        $this->logar('CRIANDO A TABELA md_pet_tipo_processo E SUA sequence');

        BancoSEI::getInstance()->executarSql('CREATE TABLE md_pet_tipo_processo( id_md_pet_tipo_processo ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL,
		id_tipo_procedimento ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL ,
		id_unidade ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL ,
		id_serie ' . $objInfraMetaBD->tipoNumero() . ' NULL , '

            . 'id_hipotese_legal ' . $objInfraMetaBD->tipoNumero() . ' NULL ,
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
		sin_ativo ' . $objInfraMetaBD->tipoTextoFixo(1) . ' NOT NULL ) ');

        $objInfraMetaBD->adicionarChavePrimaria('md_pet_tipo_processo', 'pk_md_pet_tipo_processo', array('id_md_pet_tipo_processo'));
        $objInfraMetaBD->adicionarChaveEstrangeira('fk_pet_tp_proc_tipo_proc_01', 'md_pet_tipo_processo', array('id_tipo_procedimento'), 'tipo_procedimento', array('id_tipo_procedimento'));
        $objInfraMetaBD->adicionarChaveEstrangeira('fk_pet_tp_proc_unidade_02', 'md_pet_tipo_processo', array('id_unidade'), 'unidade', array('id_unidade'));
        $objInfraMetaBD->adicionarChaveEstrangeira('fk_pet_tp_proc_serie_03', 'md_pet_tipo_processo', array('id_serie'), 'serie', array('id_serie'));
        $objInfraMetaBD->adicionarChaveEstrangeira('fk_pet_tp_proc_hip_legal_04', 'md_pet_tipo_processo', array('id_hipotese_legal'), 'hipotese_legal', array('id_hipotese_legal'));

        if (BancoSEI::getInstance() instanceof InfraMySql) {
            BancoSEI::getInstance()->executarSql('create table seq_md_pet_tipo_processo (id bigint not null primary key AUTO_INCREMENT, campo char(1) null) AUTO_INCREMENT = 1');
        } else if (BancoSEI::getInstance() instanceof InfraSqlServer) {
            BancoSEI::getInstance()->executarSql('create table seq_md_pet_tipo_processo (id bigint identity(1,1), campo char(1) null)');
        } else if (BancoSEI::getInstance() instanceof InfraOracle) {
            BancoSEI::getInstance()->criarSequencialNativa('seq_md_pet_tipo_processo', 1);
        }

        $this->logar('CRIANDO A TABELA md_pet_rel_tp_processo_serie');

        BancoSEI::getInstance()->executarSql('CREATE TABLE md_pet_rel_tp_processo_serie (
		id_md_pet_tipo_processo ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL,
		id_serie ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL)');

        $objInfraMetaBD->adicionarChavePrimaria('md_pet_rel_tp_processo_serie', 'pk1_md_pet_rel_tp_proc_serie', array('id_md_pet_tipo_processo', 'id_serie'));
        $objInfraMetaBD->adicionarChaveEstrangeira('fk1_md_pet_rel_tp_proc_serie', 'md_pet_rel_tp_processo_serie', array('id_md_pet_tipo_processo'), 'md_pet_tipo_processo', array('id_md_pet_tipo_processo'));
        $objInfraMetaBD->adicionarChaveEstrangeira('fk2_md_pet_rel_tp_proc_serie', 'md_pet_rel_tp_processo_serie', array('id_serie'), 'serie', array('id_serie'));

        $this->logar('CRIANDO A TABELA md_pet_tp_processo_orientacoes');

        BancoSEI::getInstance()->executarSql('CREATE TABLE md_pet_tp_processo_orientacoes (
		id_md_pet_tp_proc_orientacoes ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL,
		id_conjunto_estilos ' . $objInfraMetaBD->tipoNumero() . ' NULL,
		orientacoes_gerais ' . $objInfraMetaBD->tipoTextoGrande() . ' NOT NULL )');

        $objInfraMetaBD->adicionarChavePrimaria('md_pet_tp_processo_orientacoes', 'pk_md_pet_tp_proc_orient', array('id_md_pet_tp_proc_orientacoes'));
        $objInfraMetaBD->adicionarChaveEstrangeira('fk_md_pet_tp_proc_or_cj_est', 'md_pet_tp_processo_orientacoes', array('id_conjunto_estilos'), 'conjunto_estilos', array('id_conjunto_estilos'));

        $this->logar('CRIANDO A TABELA md_pet_ext_arquivo_perm E SUA sequence');

        BancoSEI::getInstance()->executarSql('CREATE TABLE md_pet_ext_arquivo_perm (
		id_md_pet_ext_arquivo_perm ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL,
		id_arquivo_extensao ' . $objInfraMetaBD->tipoNumero() . ' NULL ,
		sin_principal ' . $objInfraMetaBD->tipoTextoFixo(1) . ' NOT NULL,
		sin_ativo ' . $objInfraMetaBD->tipoTextoFixo(1) . ' NOT NULL )');

        $objInfraMetaBD->adicionarChavePrimaria('md_pet_ext_arquivo_perm', 'pk_md_pet_ext_arquivo_perm', array('id_md_pet_ext_arquivo_perm'));
        $objInfraMetaBD->adicionarChaveEstrangeira('fk1_md_pet_ext_arquivo_perm', 'md_pet_ext_arquivo_perm', array('id_arquivo_extensao'), 'arquivo_extensao', array('id_arquivo_extensao'));

        if (BancoSEI::getInstance() instanceof InfraMySql) {
            BancoSEI::getInstance()->executarSql('create table seq_md_pet_ext_arquivo_perm (id bigint not null primary key AUTO_INCREMENT, campo char(1) null) AUTO_INCREMENT = 1');
        } else if (BancoSEI::getInstance() instanceof InfraSqlServer) {
            BancoSEI::getInstance()->executarSql('create table seq_md_pet_ext_arquivo_perm (id bigint identity(1,1), campo char(1) null)');
        } else if (BancoSEI::getInstance() instanceof InfraOracle) {
            BancoSEI::getInstance()->criarSequencialNativa('seq_md_pet_ext_arquivo_perm', 1);
        }

        $this->logar('CRIANDO A TABELA md_pet_tamanho_arquivo');

        BancoSEI::getInstance()->executarSql('CREATE TABLE md_pet_tamanho_arquivo (
		id_md_pet_tamanho_arquivo ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL,
		valor_doc_principal ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL,
		valor_doc_complementar ' . $objInfraMetaBD->tipoNumero() . '  NULL,
		sin_ativo ' . $objInfraMetaBD->tipoTextoFixo(1) . ' NOT NULL ) ');

        $objInfraMetaBD->adicionarChavePrimaria('md_pet_tamanho_arquivo', 'pk_md_pet_tamanho_arquivo', array('id_md_pet_tamanho_arquivo'));

        $objTamanhoArquivoDTO = new TamanhoArquivoPermitidoPeticionamentoDTO();
        $objTamanhoArquivoRN = new TamanhoArquivoPermitidoPeticionamentoRN();
        $objTamanhoArquivoDTO->retTodos();
        $objTamanhoArquivoDTO->setNumValorDocPrincipal('5');
        $objTamanhoArquivoDTO->setNumValorDocComplementar('10');
        $objTamanhoArquivoDTO->setNumIdTamanhoArquivo(TamanhoArquivoPermitidoPeticionamentoRN::$ID_FIXO_TAMANHO_ARQUIVO);
        $objTamanhoArquivoDTO->setStrSinAtivo('S');
        $objTamanhoArquivoRN->cadastrar($objTamanhoArquivoDTO);

        $this->logar('CRIANDO A TABELA md_pet_indisponibilidade E SUA sequence');

        BancoSEI::getInstance()->executarSql('CREATE TABLE md_pet_indisponibilidade (
		id_md_pet_indisponibilidade ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL,
		dth_inicio ' . $objInfraMetaBD->tipoDataHora() . ' NOT NULL,
		dth_fim ' . $objInfraMetaBD->tipoDataHora() . ' NOT NULL,
		resumo_indisponibilidade ' . $objInfraMetaBD->tipoTextoVariavel(500) . ' NULL,
		sin_prorrogacao ' . $objInfraMetaBD->tipoTextoFixo(1) . ' NOT NULL,
		sin_ativo ' . $objInfraMetaBD->tipoTextoFixo(1) . ' NOT NULL ) ');

        $objInfraMetaBD->adicionarChavePrimaria('md_pet_indisponibilidade', 'pk_md_pet_indisponibilidade', array('id_md_pet_indisponibilidade'));

        if (BancoSEI::getInstance() instanceof InfraMySql) {
            BancoSEI::getInstance()->executarSql('create table seq_md_pet_indisponibilidade (id bigint not null primary key AUTO_INCREMENT, campo char(1) null) AUTO_INCREMENT = 1');
        } else if (BancoSEI::getInstance() instanceof InfraSqlServer) {
            BancoSEI::getInstance()->executarSql('create table seq_md_pet_indisponibilidade (id bigint identity(1,1), campo char(1) null)');
        } else if (BancoSEI::getInstance() instanceof InfraOracle) {
            BancoSEI::getInstance()->criarSequencialNativa('seq_md_pet_indisponibilidade', 1);
        }

        $this->logar('CRIANDO A TABELA md_pet_indisp_anexo E SUA sequence');

        BancoSEI::getInstance()->executarSql('CREATE TABLE md_pet_indisp_anexo (
		id_md_pet_anexo ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL,
		id_md_pet_indisponibilidade ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL,
		id_unidade ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL,
		id_usuario ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL,
		dth_inclusao ' . $objInfraMetaBD->tipoDataHora() . ' NOT NULL,
		nome ' . $objInfraMetaBD->tipoTextoVariavel(255) . ' NOT NULL,
		tamanho  ' . $objInfraMetaBD->tipoNumero() . ' NOT NULL,
		sin_ativo ' . $objInfraMetaBD->tipoTextoFixo(1) . ' NOT NULL,
		hash ' . $objInfraMetaBD->tipoTextoFixo(32) . ' NOT NULL ) ');

        $objInfraMetaBD->adicionarChavePrimaria('md_pet_indisp_anexo', 'pk_pet_indisponibilidade_anexo', array('id_md_pet_anexo'));
        $objInfraMetaBD->adicionarChaveEstrangeira('fk_md_pet_indisp_anexo_01', 'md_pet_indisp_anexo', array('id_md_pet_indisponibilidade'), 'md_pet_indisponibilidade', array('id_md_pet_indisponibilidade'));
        $objInfraMetaBD->adicionarChaveEstrangeira('fk_md_pet_indisp_anexo_02', 'md_pet_indisp_anexo', array('id_unidade'), 'unidade', array('id_unidade'));
        $objInfraMetaBD->adicionarChaveEstrangeira('fk_md_pet_indisp_anexo_03', 'md_pet_indisp_anexo', array('id_usuario'), 'usuario', array('id_usuario'));

        if (BancoSEI::getInstance() instanceof InfraMySql) {
            BancoSEI::getInstance()->executarSql('create table seq_md_pet_indisp_anexo (id bigint not null primary key AUTO_INCREMENT, campo char(1) null) AUTO_INCREMENT = 1');
        } else if (BancoSEI::getInstance() instanceof InfraSqlServer) {
            BancoSEI::getInstance()->executarSql('create table seq_md_pet_indisp_anexo (id bigint identity(1,1), campo char(1) null)');
        } else if (BancoSEI::getInstance() instanceof InfraOracle) {
            BancoSEI::getInstance()->criarSequencialNativa('seq_md_pet_indisp_anexo', 1);
        }

        //Adicionando parametro para controlar versao do modulo
        $this->logar('ADICIONANDO PARAMETRO ' . $this->nomeParametroModulo . ' NA TABELA infra_parametro PARA CONTROLAR A VERSÃO DO MÓDULO');
        BancoSEI::getInstance()->executarSql('INSERT INTO infra_parametro (valor, nome ) VALUES( \'0.0.1\',  \'' . $this->nomeParametroModulo . '\' )');
    }

    protected function atualizarVersaoConectado()
    {

        try {
            $this->inicializar('INICIANDO ATUALIZACAO DO MODULO ' . $this->nomeDesteModulo . ' NO SEI VERSAO ' . SEI_VERSAO);

            //checando versao do framework
            $numVersaoInfraRequerida = '1.376';
            if (VERSAO_INFRA != $numVersaoInfraRequerida) {
                $this->finalizar('VERSAO DO FRAMEWORK PHP INCOMPATIVEL (VERSAO ATUAL ' . VERSAO_INFRA . ', VERSAO REQUERIDA ' . $numVersaoInfraRequerida . ')', true);
            }

            //checando BDs suportados
            if (!(BancoSEI::getInstance() instanceof InfraMySql) &&
                !(BancoSEI::getInstance() instanceof InfraSqlServer) &&
                !(BancoSEI::getInstance() instanceof InfraOracle)
            ) {
                $this->finalizar('BANCO DE DADOS NAO SUPORTADO: ' . get_parent_class(BancoSEI::getInstance()), true);
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
            //nao tem nenhuma versao ainda, instalar todas
            if (InfraString::isBolVazia($strVersaoModuloPeticionamento)) {
                $this->instalarv001();
                $this->instalarv002();
                $this->instalarv100();
                $this->instalarv104();
                $this->instalarv110();
                $this->logar('ATUALIZAÇÔES DA VERSÃO ' . $this->versaoAtualDesteModulo . ' DO MODULO ' . $this->nomeDesteModulo . ' INSTALADAS COM SUCESSO NA BASE DO SEI');
                $this->finalizar('FIM', false);
            } //se ja tem 001 instala apenas 002, 100, 104 e 110
            else if ($strVersaoModuloPeticionamento == '0.0.1') {
                $this->instalarv002();
                $this->instalarv100();
                $this->instalarv104();
                $this->instalarv110();
                $this->logar('ATUALIZAÇÔES DA VERSÃO ' . $this->versaoAtualDesteModulo . ' DO MÓDULO ' . $this->nomeDesteModulo . ' INSTALADAS COM SUCESSO NA BASE DO SEI');
                $this->finalizar('FIM', false);
            } //se ja tem 002 instala apenas 100, 104, 110
            else if ($strVersaoModuloPeticionamento == '0.0.2') {
                $this->instalarv100();
                $this->instalarv104();
                $this->instalarv110();
                $this->logar('ATUALIZAÇÔES DA VERSÃO ' . $this->versaoAtualDesteModulo . ' DO MÓDULO ' . $this->nomeDesteModulo . ' INSTALADAS COM SUCESSO NA BASE DO SEI');
                $this->finalizar('FIM', false);
            } //se ja tem 100 ou 103 instala apenas a 104 e 110
            else if (in_array($strVersaoModuloPeticionamento, array('1.0.0', '1.0.3'))) {
                $this->instalarv104();
                $this->instalarv110();
                $this->logar('ATUALIZAÇÔES DA VERSÃO ' . $this->versaoAtualDesteModulo . ' DO MÓDULO ' . $this->nomeDesteModulo . ' INSTALADAS COM SUCESSO NA BASE DO SEI');
                $this->finalizar('FIM', false);
            } //se ja tem 104 apenas a 110
            else if ($strVersaoModuloPeticionamento == '1.0.4') {
                $this->instalarv110();
                $this->logar('ATUALIZAÇÔES DA VERSÃO ' . $this->versaoAtualDesteModulo . ' DO MÓDULO ' . $this->nomeDesteModulo . ' INSTALADAS COM SUCESSO NA BASE DO SEI');
                $this->finalizar('FIM', false);
            }
            else if ($strVersaoModuloPeticionamento == '1.1.0') {
                $this->logar('A VERSAO MAIS ATUAL DO MODULO ' . $this->nomeDesteModulo . ' (v ' . $this->versaoAtualDesteModulo . ') JA ESTA INSTALADA.');
                $this->finalizar('FIM', false);
            }

            InfraDebug::getInstance()->setBolLigado(false);
            InfraDebug::getInstance()->setBolDebugInfra(false);
            InfraDebug::getInstance()->setBolEcho(false);

        } catch (Exception $e) {
            InfraDebug::getInstance()->setBolLigado(true);
            InfraDebug::getInstance()->setBolDebugInfra(true);
            InfraDebug::getInstance()->setBolEcho(true);
            $this->logar($e->getTraceAsString());
            $this->finalizar('FIM', true);
            print_r($e);
            die;
            throw new InfraException('Erro atualizando versão.', $e);
        }

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