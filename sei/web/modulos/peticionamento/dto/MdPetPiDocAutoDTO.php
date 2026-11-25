<?php
/**
 * ANATEL
 *
 * Documento Adicional Gerado Automatico parametrizado para um Tipo de Processo do
 * Peticionamento Interno. Sempre de aplicabilidade Gerada, por isso nao tem aplicabilidade.
 *
 * 08/09/2026 - criado por emerson_b.goncalves - ANATEL
 */

require_once dirname(__FILE__) . '/../../../SEI.php';

class MdPetPiDocAutoDTO extends InfraDTO
{
    public function getStrNomeTabela()
    {
        return 'md_pet_pi_doc_auto';
    }

    public function montar()
    {
        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM,
            'IdMdPetPiDocAuto',
            'id_md_pet_pi_doc_auto');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM,
            'IdMdPetPiTpProcesso',
            'id_md_pet_pi_tp_processo');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM,
            'IdSerie',
            'id_serie');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_DBL,
            'IdProtocoloModelo',
            'id_protocolo_modelo');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR,
            'StaNivelAcesso',
            'sta_nivel_acesso');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM,
            'IdHipoteseLegal',
            'id_hipotese_legal');

        $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR, 'NomeSerie', 's.nome', 'serie s');
        $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR, 'NomeHipoteseLegal', 'hl.nome', 'hipotese_legal hl');
        $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR, 'ProtocoloFormatadoModelo', 'p.protocolo_formatado', 'protocolo p');

        $this->configurarPK('IdMdPetPiDocAuto', InfraDTO::$TIPO_PK_NATIVA);

        $this->configurarFK('IdMdPetPiTpProcesso', 'md_pet_pi_tp_processo tp', 'tp.id_md_pet_pi_tp_processo');
        $this->configurarFK('IdSerie', 'serie s', 's.id_serie');
        $this->configurarFK('IdHipoteseLegal', 'hipotese_legal hl', 'hl.id_hipotese_legal', InfraDTO::$TIPO_FK_OPCIONAL);
        $this->configurarFK('IdProtocoloModelo', 'protocolo p', 'p.id_protocolo', InfraDTO::$TIPO_FK_OPCIONAL);
    }
}
