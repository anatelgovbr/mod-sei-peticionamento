<?php
/**
 * ANATEL
 *
 * Orientacoes Gerais exibidas na tela de selecao do Tipo de Processo do Peticionamento Interno.
 * Registro unico, identificado por MdPetPiOrientacaoRN::$ID_FIXO_ORIENTACOES.
 *
 * 03/09/2026 - criado por emerson_b.goncalves - ANATEL
 */

require_once dirname(__FILE__) . '/../../../SEI.php';

class MdPetPiOrientacaoDTO extends InfraDTO
{
    public function getStrNomeTabela()
    {
        return 'md_pet_pi_orientacao';
    }

    public function montar()
    {
        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM,
            'IdMdPetPiOrientacao',
            'id_md_pet_pi_orientacao');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM,
            'IdConjuntoEstilos',
            'id_conjunto_estilos');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR,
            'OrientacoesGerais',
            'orientacoes_gerais');

        $this->configurarPK('IdMdPetPiOrientacao', InfraDTO::$TIPO_PK_INFORMADO);

        $this->configurarFK('IdConjuntoEstilos', 'conjunto_estilos ce', 'ce.id_conjunto_estilos', InfraDTO::$TIPO_FK_OPCIONAL);
    }
}
