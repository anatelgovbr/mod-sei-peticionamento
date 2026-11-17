<?php

/**
 * ANATEL
 *
 * 10/05/2017 - criado por jaqueline.mendes - CAST
 * 26/08/2024 - Atualização por gabrielg.colab - SPASSU 
 *
 */

require_once dirname(__FILE__) . '/../../../SEI.php';

class MdPetIntUsuarioRN extends InfraRN {

    public static $SIM = 'S';
    public static $NAO = 'N';

    private static ?UsuarioDTO $objUsuarioEmExecucao = null;
    private static bool $bolSuspensaoAutomatica = false;

    /** Executa sob a identidade técnica e restaura a sessão, inclusive em falha. */
    public function executarComoUsuarioPeticionamento(callable $executar, ?int $numIdUnidade = null): mixed
    {
        $objSessao = SessaoSEI::getInstance();
        $bolHabilitadaAnterior = $objSessao->isBolHabilitada();
        $objUsuarioAnterior = self::$objUsuarioEmExecucao;
        $arrEstadoAnterior = [];

        // Os campos simulados são separados dos campos da sessão autenticada.
        $objSessao->setBolHabilitada(false);
        try {
            foreach ([
                'NumIdOrgaoSistema', 'StrDescricaoOrgaoSistema', 'NumIdSistema',
                'StrSiglaOrgaoUsuario', 'StrDescricaoOrgaoUsuario', 'NumIdOrgaoUsuario',
                'NumIdUsuario', 'StrIdOrigemUsuario', 'StrSiglaUsuario', 'StrNomeUsuario',
                'NumIdUnidadeAtual', 'StrIdOrigemUnidadeAtual', 'StrSiglaUnidadeAtual',
                'NumIdOrgaoUnidadeAtual', 'StrSiglaOrgaoUnidadeAtual', 'StrDescricaoOrgaoUnidadeAtual',
            ] as $strCampo) {
                $arrEstadoAnterior[$strCampo] = $objSessao->{'get' . $strCampo}();
            }

            if (self::$objUsuarioEmExecucao === null) {
                $objUsuarioDTO = $this->getObjUsuarioPeticionamento();
                if (!$objUsuarioDTO instanceof UsuarioDTO
                    || filter_var($objUsuarioDTO->getNumIdUsuario(), FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]) === false
                    || $objUsuarioDTO->getStrStaTipo() !== UsuarioRN::$TU_SISTEMA
                    || $objUsuarioDTO->getStrSinAtivo() !== self::$SIM) {
                    throw new InfraException('Usuário técnico inválido ou ausente em MODULO_PETICIONAMENTO_ID_USUARIO_SISTEMA.');
                }
                self::$objUsuarioEmExecucao = $objUsuarioDTO;
            }

            $objSessao->simularLogin(
                null,
                $numIdUnidade === null ? SessaoSEI::$UNIDADE_TESTE : null,
                self::$objUsuarioEmExecucao->getNumIdUsuario(),
                $numIdUnidade
            );

            return $executar();
        } finally {
            foreach ($arrEstadoAnterior as $strCampo => $valor) {
                $objSessao->{'set' . $strCampo}($valor);
            }
            self::$objUsuarioEmExecucao = $objUsuarioAnterior;
            $objSessao->setBolHabilitada($bolHabilitadaAnterior);
        }
    }

    /** Delimita a suspensão RFB dos agendamentos CPF/CNPJ. */
    public function executarSuspensaoAutomatica(callable $executar): mixed
    {
        $bolSuspensaoAnterior = self::$bolSuspensaoAutomatica;
        try {
            self::$bolSuspensaoAutomatica = true;
            return $this->executarComoUsuarioPeticionamento($executar);
        } finally {
            self::$bolSuspensaoAutomatica = $bolSuspensaoAnterior;
        }
    }

    /** Autoriza o hook apenas no escopo ativo com a identidade técnica validada. */
    public static function isExecutandoSuspensaoAutomatica(): bool
    {
        return self::$bolSuspensaoAutomatica
            && self::$objUsuarioEmExecucao !== null
            && (string) SessaoSEI::getInstance()->getNumIdUsuario()
                === (string) self::$objUsuarioEmExecucao->getNumIdUsuario();
    }

    public function __construct() {
        parent::__construct ();
    }

    protected function inicializarObjInfraIBanco() {
        return BancoSEI::getInstance ();
    }

    protected function realizarInsercoesUsuarioModuloPetConectado()
    {
	
	    $retorno = false;
	
	    $objContatoDTO = (new MdPetContatoRN())->inserirContatoModuloPet();
	
	    if(!is_null($objContatoDTO)){
		    $objUsuarioDTO = $this->_inserirUsuarioModuloPeticionamento($objContatoDTO);
		    if(!is_null($objUsuarioDTO)){
			    $objInfraParametroDTO = $this->_inserirNovoUsuarioInfraParametro($objUsuarioDTO);
			    if(!is_null($objInfraParametroDTO)){
				    $retorno = true;
			    }
		    }
	    }
	
	    if(!$retorno){
		    throw new InfraException('NÃO FOI POSSÍVEL INSERIR O USUÁRIO DO MÓDULO PETICIONAMENTO.');
	    }
	
	    return true;

    }

    private function _inserirNovoUsuarioInfraParametro($objUsuarioDTO){
        $objInfraParametroRN = new InfraParametroRN();

        $objInfraParametroDTO = new InfraParametroDTO();
        $objInfraParametroDTO->setStrNome(MdPetContatoRN::$STR_INFRA_PARAMETRO_SIGLA_CONTATO);
        $objInfraParametroDTO->setStrValor($objUsuarioDTO->getNumIdUsuario());
        $objInfraParametroDTO = $objInfraParametroRN->cadastrar($objInfraParametroDTO);

        return $objInfraParametroDTO;

    }
    
    private function _inserirUsuarioModuloPeticionamento($objContatoDTO){
        $objUsuarioDTO   = null;
        $objContatoPetRN = new MdPetContatoRN();
        $objUsuarioRN    = new UsuarioRN();

        $idOrgaoPrinc = $this->_getIdOrgaoPrincipal();

        if (!is_null($idOrgaoPrinc) && !is_null($objContatoDTO))
        {
            $idxUsuario = $objContatoPetRN->getIdxContatoUsuario();

            $objUsuarioDTO = new UsuarioDTO();
            $objUsuarioDTO->setNumIdUsuario(null);
            $objUsuarioDTO->setNumIdContato($objContatoDTO->getNumIdContato());
            $objUsuarioDTO->setStrIdOrigem(null);
            $objUsuarioDTO->setNumIdOrgao($idOrgaoPrinc);
            $objUsuarioDTO->setStrSigla(MdPetContatoRN::$STR_SIGLA_CONTATO_MODULO);
            $objUsuarioDTO->setStrNome(MdPetContatoRN::$STR_NOME_CONTATO_MODULO);
            $objUsuarioDTO->setStrIdxUsuario($idxUsuario);
            $objUsuarioDTO->setStrStaTipo(UsuarioRN::$TU_SISTEMA);
            $objUsuarioDTO->setStrSenha(null);
            $objUsuarioDTO->setStrSinAtivo(MdPetIntUsuarioRN::$SIM);

            $objUsuarioDTO = $objUsuarioRN->cadastrarRN0487($objUsuarioDTO);
        }

        return $objUsuarioDTO;
    }

    private function _getIdOrgaoPrincipal()
    {
        $idOrgao = null;
        $objInfraConfiguracao = ConfiguracaoSEI::getInstance();
        $sessaoSei = $objInfraConfiguracao->getValor('SessaoSEI');

        if (is_array($sessaoSei) && array_key_exists('SiglaOrgaoSistema', $sessaoSei)) {
            $sigla = $sessaoSei['SiglaOrgaoSistema'];

            if ($sigla != '')
            {
                $objOrgaoRN  = new OrgaoRN();

                $objOrgaoDTO = new OrgaoDTO();
                $objOrgaoDTO->setStrSigla($sigla);
                $objOrgaoDTO->retNumIdOrgao();
                $objOrgaoDTO = $objOrgaoRN->consultarRN1352($objOrgaoDTO);

                if($objOrgaoDTO){
                    $idOrgao =  $objOrgaoDTO->getNumIdOrgao();
                }

            }

        }

        return $idOrgao;
    }
    
    
    protected function getObjUsuarioPeticionamentoConectado($retId = false)
    {
        $objUsuarioDTO     = null;
        $objInfraParametro = new InfraParametro(BancoSEI::getInstance());

        $idUsuario = $objInfraParametro->getValor(MdPetContatoRN::$STR_INFRA_PARAMETRO_SIGLA_CONTATO, false);
        
        if($idUsuario != '' && !is_null($idUsuario)){
            $objUsuarioRN  = new UsuarioRN();

            $objUsuarioDTO = new UsuarioDTO();
            $objUsuarioDTO->setNumIdUsuario($idUsuario);
            if($retId){
                $objUsuarioDTO->retNumIdUsuario();
            }else{
                $objUsuarioDTO->retTodos();
            }

            $objUsuarioDTO = $objUsuarioRN->consultarRN0489($objUsuarioDTO);

            if($retId && $objUsuarioDTO)
            {
                return $objUsuarioDTO->getNumIdUsuario();
            }
        }

        return $objUsuarioDTO;
    }


    protected function retornaObjContatoPorIdUsuarioConectado($params){
        $objRetorno = null;
        $idUsuario  = $params[0];
        $retTodos   = isset($params[1]) ? $params[1] : false;

        $objUsuarioDTO = new UsuarioDTO();
        $objUsuarioDTO->setNumIdUsuario($idUsuario);
        $objUsuarioDTO->retNumIdContato();
        $objUsuarioRN = new UsuarioRN();
        $ret = $objUsuarioRN->listarRN0490($objUsuarioDTO);
        $objUsuarioDTO = count($ret) > 0 ? current($ret) : null;

        $idContato = !is_null($objUsuarioDTO) ? $objUsuarioDTO->getNumIdContato() : null;

        if($idContato){
            $objContatoDTO = new ContatoDTO();
            $objContatoDTO->setNumIdContato($idContato);

            if($retTodos) {
                $objContatoDTO->retTodos(true);
            }else{
                $objContatoDTO->retNumIdContato();
                $objContatoDTO->retStrNome();
                $objContatoDTO->retStrEmail();
            }

            $objContatoRN = new ContatoRN();
            $count = $objContatoRN->contarRN0327($objContatoDTO);

            if($count > 0){
                $objContatoDTO->setNumMaxRegistrosRetorno(1);
                $objRetorno = $objContatoRN->consultarRN0324($objContatoDTO);
            }
        }

        return $objRetorno;
    }






}