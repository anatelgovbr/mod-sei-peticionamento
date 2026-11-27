<?php
/**
 * ANATEL
 *
 * Peticionamento Interno concluído: processo aberto, usuário que peticionou, unidade de abertura e indicação de interessado.
 *
 * 17/09/2026 - criado por emerson_b.goncalves - ANATEL
 */

require_once dirname(__FILE__) . '/../../../SEI.php';

class MdPetPiPeticaoDTO extends InfraDTO
{
    public function getStrNomeTabela()
    {
        return 'md_pet_pi_peticao';
    }

    public function montar()
    {
        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM,
            'IdMdPetPiPeticao',
            'id_md_pet_pi_peticao');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_DBL,
            'IdProcedimento',
            'id_procedimento');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM,
            'IdUsuario',
            'id_usuario');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM,
            'IdUnidade',
            'id_unidade');

        // P = Interesse Pessoal do Usuario, U = Interesse da Unidade do Usuario
        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR,
            'StaIndicacaoInteressado',
            'sta_indicacao_interessado');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_DTH,
            'Peticionamento',
            'dth_peticionamento');

        $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR,
            'ProtocoloFormatadoProcedimento',
            'p.protocolo_formatado',
            'protocolo p');

        $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR,
            'ProtocoloFormatadoPesquisaProcedimento',
            'p.protocolo_formatado_pesquisa',
            'protocolo p');

        $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR,
            'EspecificacaoProcedimento',
            'p.descricao',
            'protocolo p');

        $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_DTA,
            'GeracaoProcedimento',
            'p.dta_geracao',
            'protocolo p');

        $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_NUM,
            'IdTipoProcedimentoProcesso',
            'pr.id_tipo_procedimento',
            'procedimento pr');

        $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR,
            'NomeTipoProcedimentoProcesso',
            'tp.nome',
            'tipo_procedimento tp');

        // Atributos sem coluna, usados so no caminho da conclusao do peticionamento
        $this->adicionarAtributo(InfraDTO::$PREFIXO_NUM, 'IdMdPetPiTpProcesso');
        $this->adicionarAtributo(InfraDTO::$PREFIXO_STR, 'Especificacao');
        $this->adicionarAtributo(InfraDTO::$PREFIXO_NUM, 'IdTipoPrioridade');
        $this->adicionarAtributo(InfraDTO::$PREFIXO_ARR, 'ObjMdPetPiRelPeticaoDocDTO');
        $this->adicionarAtributo(InfraDTO::$PREFIXO_STR, 'CargoFuncaoAssinatura');
        $this->adicionarAtributo(InfraDTO::$PREFIXO_STR, 'SenhaAssinatura');
        $this->adicionarAtributo(InfraDTO::$PREFIXO_DBL, 'IdDocumentoAssinado');

        $this->configurarPK('IdMdPetPiPeticao', InfraDTO::$TIPO_PK_NATIVA);

        $this->configurarFK('IdProcedimento', 'protocolo p', 'p.id_protocolo');
        $this->configurarFK('IdProcedimento', 'procedimento pr', 'pr.id_procedimento');
        $this->configurarFK('IdTipoProcedimentoProcesso', 'tipo_procedimento tp', 'tp.id_tipo_procedimento');
        $this->configurarFK('IdUsuario', 'usuario u', 'u.id_usuario');
        $this->configurarFK('IdUnidade', 'unidade un', 'un.id_unidade');
    }
}
