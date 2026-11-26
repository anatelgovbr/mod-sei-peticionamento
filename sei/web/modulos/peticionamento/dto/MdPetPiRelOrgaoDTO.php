<?php
/**
 * ANATEL
 *
 * Disponibilizacao de um Tipo de Processo do Peticionamento Interno por Orgao.
 * Ausencia de registros significa disponivel para todos.
 *
 * 08/09/2026 - criado por emerson_b.goncalves - ANATEL
 */

require_once dirname(__FILE__) . '/../../../SEI.php';

class MdPetPiRelOrgaoDTO extends InfraDTO
{
    public function getStrNomeTabela()
    {
        return 'md_pet_pi_rel_orgao';
    }

    public function montar()
    {
        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM,
            'IdMdPetPiTpProcesso',
            'id_md_pet_pi_tp_processo');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM,
            'IdOrgao',
            'id_orgao');

        $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR, 'SiglaOrgao', 'o.sigla', 'orgao o');

        $this->configurarPK('IdMdPetPiTpProcesso', InfraDTO::$TIPO_PK_INFORMADO);
        $this->configurarPK('IdOrgao', InfraDTO::$TIPO_PK_INFORMADO);

        $this->configurarFK('IdMdPetPiTpProcesso', 'md_pet_pi_tp_processo tp', 'tp.id_md_pet_pi_tp_processo');
        $this->configurarFK('IdOrgao', 'orgao o', 'o.id_orgao');
    }
}
