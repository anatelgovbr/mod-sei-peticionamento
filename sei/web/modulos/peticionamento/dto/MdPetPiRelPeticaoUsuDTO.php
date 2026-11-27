<?php
/**
 * ANATEL
 *
 * Usuário liberado pelo autor para acessar os documentos Restritos de um Peticionamento Interno de Interesse Pessoal.
 *
 * 25/09/2026 - criado por emerson_b.goncalves - ANATEL
 */

require_once dirname(__FILE__) . '/../../../SEI.php';

class MdPetPiRelPeticaoUsuDTO extends InfraDTO
{
    public function getStrNomeTabela()
    {
        return 'md_pet_pi_rel_peticao_usu';
    }

    public function montar()
    {
        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM,
            'IdMdPetPiPeticao',
            'id_md_pet_pi_peticao');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM,
            'IdUsuario',
            'id_usuario');

        $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR,
            'SiglaUsuario',
            'u.sigla',
            'usuario u');

        $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR,
            'NomeUsuario',
            'u.nome',
            'usuario u');

        $this->configurarPK('IdMdPetPiPeticao', InfraDTO::$TIPO_PK_INFORMADO);
        $this->configurarPK('IdUsuario', InfraDTO::$TIPO_PK_INFORMADO);

        $this->configurarFK('IdMdPetPiPeticao', 'md_pet_pi_peticao pp', 'pp.id_md_pet_pi_peticao');
        $this->configurarFK('IdUsuario', 'usuario u', 'u.id_usuario');
    }
}
