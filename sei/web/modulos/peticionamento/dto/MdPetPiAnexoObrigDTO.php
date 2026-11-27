<?php
/**
 * ANATEL
 *
 * Anexo de Arquivo Obrigatorio parametrizado para um Tipo de Processo do Peticionamento Interno.
 * Sempre de aplicabilidade Externa, por isso nao tem aplicabilidade nem modelo por protocolo.
 *
 * 08/09/2026 - criado por emerson_b.goncalves - ANATEL
 */

require_once dirname(__FILE__) . '/../../../SEI.php';

class MdPetPiAnexoObrigDTO extends InfraDTO
{
    public function getStrNomeTabela()
    {
        return 'md_pet_pi_anexo_obrig';
    }

    public function montar()
    {
        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM,
            'IdMdPetPiAnexoObrig',
            'id_md_pet_pi_anexo_obrig');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM,
            'IdMdPetPiTpProcesso',
            'id_md_pet_pi_tp_processo');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM,
            'IdSerie',
            'id_serie');

        // U = Usuario indica diretamente, P = Padrao pre-definido
        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR,
            'StaTipoNivelAcesso',
            'sta_tipo_nivel_acesso');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR,
            'StaNivelAcesso',
            'sta_nivel_acesso');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM,
            'IdHipoteseLegal',
            'id_hipotese_legal');

        $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR, 'NomeSerie', 's.nome', 'serie s');
        $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR, 'NomeHipoteseLegal', 'hl.nome', 'hipotese_legal hl');

        $this->configurarPK('IdMdPetPiAnexoObrig', InfraDTO::$TIPO_PK_NATIVA);

        $this->configurarFK('IdMdPetPiTpProcesso', 'md_pet_pi_tp_processo tp', 'tp.id_md_pet_pi_tp_processo');
        $this->configurarFK('IdSerie', 'serie s', 's.id_serie');
        $this->configurarFK('IdHipoteseLegal', 'hipotese_legal hl', 'hl.id_hipotese_legal', InfraDTO::$TIPO_FK_OPCIONAL);
    }
}
