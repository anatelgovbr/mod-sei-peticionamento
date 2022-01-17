<?
/**
 * TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
 *
 * 25/01/2018 - criado por Usuário
 *
 * Versão do Gerador de Código: 1.41.0
 */
 
require_once dirname(__FILE__) . '/../../../SEI.php';

class MdPetIntegracaoDTO extends InfraDTO
{

    public function getStrNomeTabela()
    {
        return 'md_pet_adm_integracao';
    }

    public function montar()
    {

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM, 'IdMdPetIntegracao', 'id_md_pet_adm_integracao');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM, 'IdMdPetIntegFuncionalid', 'id_md_pet_adm_integ_funcion');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR, 'Nome', 'nome');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR, 'StaUtilizarWs', 'sta_utilizar_ws');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR, 'TpClienteWs', 'sta_tp_cliente_ws');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM, 'NuVersao', 'nu_versao');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR, 'EnderecoWsdl', 'endereco_wsdl');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR, 'OperacaoWsdl', 'operacao_wsdl');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR, 'SinCache', 'sin_cache');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR, 'SinTpLogradouro', 'sin_tp_lougradouro');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR, 'SinNuLogradouro', 'sin_nu_lougradouro');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR, 'SinCompLogradouro', 'sin_comp_lougradouro');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR, 'SinAtivo', 'sin_ativo');

        $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR, 'NomeMdPetIntegFuncionalid', 'nome', 'md_pet_adm_integ_funcion');

        $this->configurarPK('IdMdPetIntegracao', InfraDTO::$TIPO_PK_NATIVA);

        $this->configurarFK('IdMdPetIntegFuncionalid', 'md_pet_adm_integ_funcion', 'id_md_pet_adm_integ_funcion');
        $this->configurarExclusaoLogica('SinAtivo', 'N');

    }
}
