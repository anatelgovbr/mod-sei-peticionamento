<?php
/**
 * ANATEL
 *
 * Documento Principal parametrizado para um Tipo de Processo do Peticionamento Interno.
 * Um unico registro por Tipo de Processo, por isso a PK e a propria FK do pai.
 *
 * 08/09/2026 - criado por emerson_b.goncalves - ANATEL
 */

require_once dirname(__FILE__) . '/../../../SEI.php';

class MdPetPiDocPrincDTO extends InfraDTO
{
    public function getStrNomeTabela()
    {
        return 'md_pet_pi_doc_princ';
    }

    public function montar()
    {
        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM,
            'IdMdPetPiTpProcesso',
            'id_md_pet_pi_tp_processo');

        // G = Gerado, E = Externo, F = Formulario
        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR,
            'StaAplicabilidade',
            'sta_aplicabilidade');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM,
            'IdSerie',
            'id_serie');

        // $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM,
        //     'IdTipoFormulario',
        //     'id_tipo_formulario');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_DBL,
            'IdProtocoloModelo',
            'id_protocolo_modelo');

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
        // $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR, 'NomeTipoFormulario', 'f.nome', 'tipo_formulario f');
        $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR, 'NomeHipoteseLegal', 'hl.nome', 'hipotese_legal hl');
        $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR, 'ProtocoloFormatadoModelo', 'p.protocolo_formatado', 'protocolo p');

        $this->configurarPK('IdMdPetPiTpProcesso', InfraDTO::$TIPO_PK_INFORMADO);

        $this->configurarFK('IdMdPetPiTpProcesso', 'md_pet_pi_tp_processo tp', 'tp.id_md_pet_pi_tp_processo');
        $this->configurarFK('IdSerie', 'serie s', 's.id_serie', InfraDTO::$TIPO_FK_OPCIONAL);
        // $this->configurarFK('IdTipoFormulario', 'tipo_formulario f', 'f.id_tipo_formulario', InfraDTO::$TIPO_FK_OPCIONAL);
        $this->configurarFK('IdHipoteseLegal', 'hipotese_legal hl', 'hl.id_hipotese_legal', InfraDTO::$TIPO_FK_OPCIONAL);
        $this->configurarFK('IdProtocoloModelo', 'protocolo p', 'p.id_protocolo', InfraDTO::$TIPO_FK_OPCIONAL);
    }
}
