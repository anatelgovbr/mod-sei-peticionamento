<?php
/**
 * ANATEL
 *
 * Documento do SEI incluído pelo Peticionamento Interno, com o papel que teve no peticionamento.
 *
 * 17/09/2026 - criado por emerson_b.goncalves - ANATEL
 */

require_once dirname(__FILE__) . '/../../../SEI.php';

class MdPetPiRelPeticaoDocDTO extends InfraDTO
{
    public function getStrNomeTabela()
    {
        return 'md_pet_pi_rel_peticao_doc';
    }

    public function montar()
    {
        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM,
            'IdMdPetPiPeticao',
            'id_md_pet_pi_peticao');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_DBL,
            'IdDocumento',
            'id_documento');

        // P = Principal, O = Anexo Obrigatorio, M = Mais Anexos, A = Adicional Gerado Automatico
        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR,
            'StaPapel',
            'sta_papel');

        $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_DBL,
            'IdProcedimentoPeticao',
            'pp.id_procedimento',
            'md_pet_pi_peticao pp');

        $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_NUM,
            'IdUsuarioPeticao',
            'pp.id_usuario',
            'md_pet_pi_peticao pp');

        $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_NUM,
            'IdUnidadePeticao',
            'pp.id_unidade',
            'md_pet_pi_peticao pp');

        $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR,
            'StaIndicacaoInteressadoPeticao',
            'pp.sta_indicacao_interessado',
            'md_pet_pi_peticao pp');

        $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR,
            'StaNivelAcessoLocalProtocolo',
            'pd.sta_nivel_acesso_local',
            'protocolo pd');

        // Atributos sem coluna, usados so no caminho da conclusao do peticionamento
        $this->adicionarAtributo(InfraDTO::$PREFIXO_NUM, 'IdSerie');
        // $this->adicionarAtributo(InfraDTO::$PREFIXO_NUM, 'IdTipoFormulario');
        $this->adicionarAtributo(InfraDTO::$PREFIXO_DBL, 'IdProtocoloModelo');
        $this->adicionarAtributo(InfraDTO::$PREFIXO_STR, 'Complemento');
        $this->adicionarAtributo(InfraDTO::$PREFIXO_STR, 'StaNivelAcesso');
        $this->adicionarAtributo(InfraDTO::$PREFIXO_NUM, 'IdHipoteseLegal');
        $this->adicionarAtributo(InfraDTO::$PREFIXO_NUM, 'IdTipoConferencia');
        $this->adicionarAtributo(InfraDTO::$PREFIXO_STR, 'StaFormato');
        $this->adicionarAtributo(InfraDTO::$PREFIXO_STR, 'NomeArquivoUpload');
        $this->adicionarAtributo(InfraDTO::$PREFIXO_STR, 'NomeArquivo');
        $this->adicionarAtributo(InfraDTO::$PREFIXO_STR, 'Conteudo');
        $this->adicionarAtributo(InfraDTO::$PREFIXO_ARR, 'CamposFormulario');
        $this->adicionarAtributo(InfraDTO::$PREFIXO_ARR, 'ConteudoSecoes');

        $this->configurarPK('IdMdPetPiPeticao', InfraDTO::$TIPO_PK_INFORMADO);
        $this->configurarPK('IdDocumento', InfraDTO::$TIPO_PK_INFORMADO);

        $this->configurarFK('IdMdPetPiPeticao', 'md_pet_pi_peticao pp', 'pp.id_md_pet_pi_peticao');
        $this->configurarFK('IdDocumento', 'documento d', 'd.id_documento');
        $this->configurarFK('IdDocumento', 'protocolo pd', 'pd.id_protocolo');
    }
}
