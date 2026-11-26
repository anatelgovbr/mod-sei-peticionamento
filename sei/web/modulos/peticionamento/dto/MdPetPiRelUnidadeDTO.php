<?php
/**
 * ANATEL
 *
 * Disponibilizacao de um Tipo de Processo do Peticionamento Interno por Unidade.
 * Ausencia de registros significa disponivel para todos.
 *
 * 08/09/2026 - criado por emerson_b.goncalves - ANATEL
 */

require_once dirname(__FILE__) . '/../../../SEI.php';

class MdPetPiRelUnidadeDTO extends InfraDTO
{
    public function getStrNomeTabela()
    {
        return 'md_pet_pi_rel_unidade';
    }

    public function montar()
    {
        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM,
            'IdMdPetPiTpProcesso',
            'id_md_pet_pi_tp_processo');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM,
            'IdUnidade',
            'id_unidade');

        $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR, 'SiglaUnidade', 'u.sigla', 'unidade u');
        $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR, 'DescricaoUnidade', 'u.descricao', 'unidade u');
        $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_NUM, 'IdOrgaoUnidade', 'u.id_orgao', 'unidade u');
        $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR, 'SiglaOrgaoUnidade', 'o.sigla', 'orgao o');

        $this->configurarPK('IdMdPetPiTpProcesso', InfraDTO::$TIPO_PK_INFORMADO);
        $this->configurarPK('IdUnidade', InfraDTO::$TIPO_PK_INFORMADO);

        $this->configurarFK('IdMdPetPiTpProcesso', 'md_pet_pi_tp_processo tp', 'tp.id_md_pet_pi_tp_processo');
        $this->configurarFK('IdUnidade', 'unidade u', 'u.id_unidade');
        $this->configurarFK('IdOrgaoUnidade', 'orgao o', 'o.id_orgao');
    }
}
