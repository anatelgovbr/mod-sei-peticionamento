<?php
/**
 * ANATEL
 *
 * Tipo de Processo parametrizado para o Peticionamento Interno.
 *
 * 08/09/2026 - criado por emerson_b.goncalves - ANATEL
 */

require_once dirname(__FILE__) . '/../../../SEI.php';

class MdPetPiTpProcessoDTO extends InfraDTO
{
    public function getStrNomeTabela()
    {
        return 'md_pet_pi_tp_processo';
    }

    public function montar()
    {
        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM,
            'IdMdPetPiTpProcesso',
            'id_md_pet_pi_tp_processo');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM,
            'IdTipoProcedimento',
            'id_tipo_procedimento');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM,
            'IdConjuntoEstilos',
            'id_conjunto_estilos');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR,
            'Orientacoes',
            'orientacoes');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR,
            'OrientacoesPosPet',
            'orientacoes_pos_pet');

        // P = Interesse Pessoal do Usuario, U = Interesse da Unidade do Usuario
        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR,
            'StaIndicacaoInteressado',
            'sta_indicacao_interessado');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR,
            'SinAtivo',
            'sin_ativo');

        $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR, 'NomeTipoProcedimento', 'tp.nome', 'tipo_procedimento tp');

        // Documento Principal, para a coluna e o filtro da listagem. Relacao 1 para 1, nao duplica linha.
        $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR, 'StaAplicabilidadeDocPrinc', 'dp.sta_aplicabilidade', 'md_pet_pi_doc_princ dp');
        $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_NUM, 'IdSerieDocPrinc', 'dp.id_serie', 'md_pet_pi_doc_princ dp');
        // $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_NUM, 'IdTipoFormularioDocPrinc', 'dp.id_tipo_formulario', 'md_pet_pi_doc_princ dp');
        $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR, 'NomeSerieDocPrinc', 'sdp.nome', 'serie sdp');

        $this->configurarPK('IdMdPetPiTpProcesso', InfraDTO::$TIPO_PK_NATIVA);

        $this->configurarFK('IdTipoProcedimento', 'tipo_procedimento tp', 'tp.id_tipo_procedimento');
        $this->configurarFK('IdConjuntoEstilos', 'conjunto_estilos ce', 'ce.id_conjunto_estilos', InfraDTO::$TIPO_FK_OPCIONAL);
        $this->configurarFK('IdMdPetPiTpProcesso', 'md_pet_pi_doc_princ dp', 'dp.id_md_pet_pi_tp_processo');
        $this->configurarFK('IdSerieDocPrinc', 'serie sdp', 'sdp.id_serie', InfraDTO::$TIPO_FK_OPCIONAL);

        $this->adicionarAtributo(InfraDTO::$PREFIXO_OBJ, 'ObjMdPetPiDocPrincDTO');
        $this->adicionarAtributo(InfraDTO::$PREFIXO_ARR, 'ObjMdPetPiAnexoObrigDTO');
        $this->adicionarAtributo(InfraDTO::$PREFIXO_ARR, 'ObjMdPetPiDocAutoDTO');
        $this->adicionarAtributo(InfraDTO::$PREFIXO_ARR, 'ObjMdPetPiRelOrgaoDTO');
        $this->adicionarAtributo(InfraDTO::$PREFIXO_ARR, 'ObjMdPetPiRelUnidadeDTO');
    }
}
