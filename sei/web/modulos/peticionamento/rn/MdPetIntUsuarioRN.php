<?php

/**
 * ANATEL
 *
 * 10/05/2017 - criado por jaqueline.mendes - CAST
 *
 */

require_once dirname(__FILE__) . '/../../../SEI.php';

class MdPetIntUsuarioRN extends InfraRN {

    public static $SIM = 'S';
    public static $NAO = 'N';

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