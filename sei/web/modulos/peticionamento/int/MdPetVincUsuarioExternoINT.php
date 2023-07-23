<?
/**
 * TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
 *
 * 08/02/2012 - criado por bcu
 *
 * Versão do Gerador de Código: 1.32.1
 *
 * Versão no CVS: $Id$
 */

require_once dirname(__FILE__) . '/../../../SEI.php';

class MdPetVincUsuarioExternoINT extends InfraINT
{

    public static function validarExistenciaProcuracao($dados)
    {

	    if ($dados['tipoProc'] == "S") {

            $poderes = explode('-', $dados['poderes']);

            //Validação para Procuração Especial
            $objMdPetVincRepresentantDTO = new MdPetVincRepresentantDTO();
            $objMdPetVincRepresentantRN = new MdPetVincRepresentantRN();
            $objMdPetVincRepresentantDTO->setStrStaEstado(MdPetVincRepresentantRN::$RP_ATIVO);
            $objMdPetVincRepresentantDTO->adicionarCriterio(
                array('IdContato', 'IdContatoVinc', 'TipoRepresentante'),
                array(InfraDTO::$OPER_IGUAL, InfraDTO::$OPER_IGUAL, InfraDTO::$OPER_IGUAL),
                array($dados['idOutorgado'], $dados['idOutorgante'], 'E'),
                array(InfraDTO::$OPER_LOGICO_AND, InfraDTO::$OPER_LOGICO_AND));
            $objMdPetVincRepresentantDTO->retNumIdContato();
            $objMdPetVincRepresentantDTO->retNumIdMdPetVinculoRepresent();
            $objMdPetVincRepresentantDTO = $objMdPetVincRepresentantRN->listar($objMdPetVincRepresentantDTO);
            $objMdPetVincRepresentantDTO = InfraArray::converterArrInfraDTO($objMdPetVincRepresentantDTO, 'IdMdPetVinculoRepresent');

            //Validação para Procuração Simples
            $objMdPetVincRepresentantSimplesDTO = new MdPetVincRepresentantDTO();
            $objMdPetVincRepresentantSimplesRN = new MdPetVincRepresentantRN();
            $objMdPetVincRepresentantSimplesDTO->setStrStaEstado(MdPetVincRepresentantRN::$RP_ATIVO);
            $objMdPetVincRepresentantSimplesDTO->retNumIdMdPetRelPoder();
            $objMdPetVincRepresentantSimplesDTO->retDthDataCadastro();
            $objMdPetVincRepresentantSimplesDTO->retDthDataLimite();
            $objMdPetVincRepresentantSimplesDTO->retStrStaAbrangencia();
            $objMdPetVincRepresentantSimplesDTO->adicionarCriterio(
                array('IdContato', 'IdContatoVinc', 'TipoRepresentante'),
                array(InfraDTO::$OPER_IGUAL, InfraDTO::$OPER_IGUAL, InfraDTO::$OPER_IGUAL),
                array($dados['idOutorgado'], $dados['idOutorgante'], MdPetVincRepresentantRN::$PE_PROCURADOR_SIMPLES),
                array(InfraDTO::$OPER_LOGICO_AND, InfraDTO::$OPER_LOGICO_AND));
            $objMdPetVincRepresentantSimplesDTO->retNumIdContato();
            $objMdPetVincRepresentantSimplesDTO->retNumIdMdPetVinculoRepresent();
            $arrObjMdPetVincRepresentantSimplesDTO = $objMdPetVincRepresentantSimplesRN->listar($objMdPetVincRepresentantSimplesDTO);
	        $arrObjMdPetVincRepresentantSimplesDTO = InfraArray::converterArrInfraDTO($arrObjMdPetVincRepresentantSimplesDTO, 'IdMdPetVinculoRepresent');

	        if($validarPoderes = false){

	            $arrObjMdPetVincRepresentantSimplesFinal = array();
	            foreach($arrObjMdPetVincRepresentantSimplesDTO as $objMdPetVincRepresentantSimples){
	                $gerarProcuracao = true;

	                $objMdPetRelVincRepTpPoderRN = new MdPetRelVincRepTpPoderRN();
	                $objMdPetRelVincRepTpPoderDTO = new MdPetRelVincRepTpPoderDTO();
	                $objMdPetRelVincRepTpPoderDTO->retNumIdTipoPoderLegal();
	                $objMdPetRelVincRepTpPoderDTO->setNumIdVinculoRepresent($objMdPetVincRepresentantSimples->getNumIdMdPetVinculoRepresent());

	                $arrObjMdPetRelVincRepTpPoderDTO = $objMdPetRelVincRepTpPoderRN->listar($objMdPetRelVincRepTpPoderDTO);
	                $arrObjMdPetRelVincRepTpPoderDTO = InfraArray::converterArrInfraDTO($arrObjMdPetRelVincRepTpPoderDTO, 'IdTipoPoderLegal');

	                $objMdPetRelVincRepProtocRN = new MdPetRelVincRepProtocRN();
	                $objMdPetRelVincRepProtocDTO = new MdPetRelVincRepProtocDTO();
	                $objMdPetRelVincRepProtocDTO->retNumIdProtocolo();
	                $objMdPetRelVincRepProtocDTO->setNumIdVincRepresent($objMdPetVincRepresentantSimples->getNumIdMdPetVinculoRepresent());

	                $arrMdPetRelVincRepProtocDTO = $objMdPetRelVincRepProtocRN->listar($objMdPetRelVincRepProtocDTO);
	                $arrMdPetRelVincRepProtocDTO = InfraArray::converterArrInfraDTO($arrMdPetRelVincRepProtocDTO, 'IdProtocolo');

	                if ($dados['validade'] != "Indeterminada") {
	                    $data = explode('/', $dados['validade']);
	                    $dados['validade'] = $data[0] . '/' . $data[1] . '/' . $data[2] . " 23:59:59";
	                } else {
	                    $dados['validade'] = null;
	                }

	                if ($dados['processos'] != "") {
	                    $processos = array();
	                    $tpProcesso = PaginaSEIExterna::getInstance()->getArrItensTabelaDinamica($dados['processos']);
	                    foreach ($tpProcesso as $value) {
	                        $processos[] = $value[0];
	                    }
	                } else {
	                    $processos = "Q";
	                }

	                $poderesIguais = array_intersect($poderes, $arrObjMdPetRelVincRepTpPoderDTO);

	                if($objMdPetVincRepresentantSimples->getStrStaAbrangencia() == 'Q'){
	                    if(is_array($processos) && $poderesIguais){
	                        $gerarProcuracao = false;
	                    } else {
	                        if($poderesIguais){
	                            $gerarProcuracao = false;
	                        }
	                    }
	                } else {
	                    if($processos == 'Q' && $objMdPetVincRepresentantSimples->getStrStaAbrangencia() == 'E'){
	                        if($poderesIguais){
	                            $gerarProcuracao = false;
	                        }
	                    } else {
	                        $processosIguais = array_intersect($processos, $arrMdPetRelVincRepProtocDTO);
	                        if ($processosIguais || $poderesIguais) {
	                            $gerarProcuracao = false;
	                        }
	                    }
	                }

	                if(!$gerarProcuracao) {
	                    $arrObjMdPetVincRepresentantSimplesFinal[] = $objMdPetVincRepresentantSimples->getNumIdMdPetVinculoRepresent();
	                }
	            }

	        }

            if (count($arrObjMdPetVincRepresentantSimplesDTO) > 0 || count($objMdPetVincRepresentantDTO) > 0) {

	            $objMdPetVincRepresentantDTO = array_unique(array_merge($objMdPetVincRepresentantDTO, $arrObjMdPetVincRepresentantSimplesDTO));
                $arrImplode = implode("-", $objMdPetVincRepresentantDTO);
                $xml = '<itens>';
                $xml .= '<item id="' . InfraString::formatarXML(SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=peticionamento_usuario_externo_vinc_validacao_procuracao&id_represent=' . $arrImplode)) . '"></item>';
                $xml .= '</itens>';

                return $xml;

            } else {

                return '<itens><item id="0"></item></itens>';

            }
        }

	    if($dados['tipoProc'] == 'E'){

			$objMdPetVincRepresentantDTO = new MdPetVincRepresentantDTO();
			$objMdPetVincRepresentantDTO->setStrStaEstado('A');
			$objMdPetVincRepresentantDTO->adicionarCriterio(
				array('IdContato', 'IdContatoVinc', 'TipoRepresentante'),
				array(InfraDTO::$OPER_IN, InfraDTO::$OPER_IGUAL, InfraDTO::$OPER_IGUAL),
				array(explode('#', $dados['idOutorgado']), $dados['idOutorgante'], 'E'),
				array(InfraDTO::$OPER_LOGICO_AND, InfraDTO::$OPER_LOGICO_AND));
			$objMdPetVincRepresentantDTO->retNumIdContato();
			$objMdPetVincRepresentantDTO->retNumIdMdPetVinculoRepresent();
			$objMdPetVincRepresentantDTO = (new MdPetVincRepresentantRN())->listar($objMdPetVincRepresentantDTO);
			$objMdPetVincRepresentantDTO = InfraArray::converterArrInfraDTO($objMdPetVincRepresentantDTO, 'IdMdPetVinculoRepresent');

			if(count($objMdPetVincRepresentantDTO) > 0){

				$arrImplode = implode("-", $objMdPetVincRepresentantDTO);
				$xml = '';
				$xml .= '<itens>';
				$xml .= '<item id="' . InfraString::formatarXML(SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=peticionamento_usuario_externo_vinc_validacao_procuracao&id_represent=' . $arrImplode)) . '"';
				$xml .= '></item>';
				$xml .= '</itens>';
				return $xml;

			}else{

				return '<itens><item id="0"></item></itens>';

			}

		}

    }

    public static function verificarUsuarioValido($dados)
    {

    	$usuarioDTO = new UsuarioDTO();
        $usuarioDTO->setNumIdContato($dados['idContato']);
        $usuarioDTO->retStrStaTipo();
        $usuarioRN = new UsuarioRN();
        $objUsuarioRN = $usuarioRN->consultarRN0489($usuarioDTO);

        // Igual a 2 é Pendente
	    return '<dados><existe>'.($objUsuarioRN->getStrStaTipo() == "2" ? 0 : 1).'</existe></dados>';

    }

    public static function consultarDadosUsuario($dados)
    {

    	$xml = '';

        if (isset($dados['hdnSelPessoaJuridica'])) {
            //verifica se o procurador indicado já possui uma procuração naquele mesmo vinculo
            if (self::_consultarExistenciaVinculo($dados)) {
	            return '<dados><sucesso>0</sucesso></dados>';
            }
        }

        $idContato = $dados['hdnIdUsuarioProcuracao'];

        $objUsuarioRN = new UsuarioRN();
        $objUsuarioDTO = new UsuarioDTO();
        $objUsuarioDTO->retNumIdContato();
        $objUsuarioDTO->retStrNomeContato();
        $objUsuarioDTO->retStrSigla();
        $objUsuarioDTO->retDblCpfContato();
        $objUsuarioDTO->setNumIdContato($idContato);

        $arrContato = $objUsuarioRN->consultarRN0489($objUsuarioDTO);

        if (count($arrContato) > 0) {
            $xml .= '<dados>';
            $xml .= '<no-usuario>' . $arrContato->getStrNomeContato() . '</no-usuario>';
            $xml .= '<ds-email>' . $arrContato->getStrSigla() . '</ds-email>';
            $xml .= '<nu-cpf>' . InfraUtil::formatarCpf($arrContato->getDblCpfContato()) . '</nu-cpf>';
            $xml .= '<sucesso>1</sucesso>';
            $xml .= '</dados>';
        }

        return $xml;

    }

    public static function consultarDadosUsuarioExternoProcuracao($dados)
    {

    	$xml = '';

        $idContato = $dados['hdnIdUsuarioProcuracao'];

        $objUsuarioRN = new UsuarioRN();
        $objUsuarioDTO = new UsuarioDTO();
        $objUsuarioDTO->retNumIdContato();
        $objUsuarioDTO->retStrNomeContato();
        $objUsuarioDTO->retStrSigla();
        $objUsuarioDTO->retDblCpfContato();
        $objUsuarioDTO->setNumIdContato($idContato);

        $arrContato = $objUsuarioRN->consultarRN0489($objUsuarioDTO);

        if (!is_null($arrContato)) {
            $xml .= '<dados>';
            $xml .= '<nu-id>' . $arrContato->getNumIdContato() . '</nu-id>';
            $xml .= '<nu-cpf>' . InfraUtil::formatarCpf($arrContato->getDblCpfContato()) . '</nu-cpf>';
            $xml .= '<no-usuario>' . $arrContato->getStrNomeContato() . '</no-usuario>';
            $xml .= '<sucesso>1</sucesso>';
            $xml .= '</dados>';
        }

        return $xml;

    }

    public static function consultarDadosUsuarioExterno($dados)
    {

    	if (isset($dados['hdnSelPessoaJuridica'])) {

    		$objMdPetVincRepresentantDTO = new MdPetVincRepresentantDTO();

            $objMdPetVincRepresentantDTO->setNumIdContato($dados['hdnIdUsuarioProcuracao']);
            $objMdPetVincRepresentantDTO->setNumIdContatoVinc($dados['hdnSelPessoaJuridica']);
            $objMdPetVincRepresentantDTO->setStrStaEstado(MdPetVincRepresentantRN::$RP_ATIVO);
            $objMdPetVincRepresentantDTO->retNumIdContato();

            $objMdPetVincRepresentantDTO = (new MdPetVincRepresentantRN())->contar($objMdPetVincRepresentantDTO) > 0;

		    return '<dados><sucesso>'.($objMdPetVincRepresentantDTO ? 1 : 0).'</sucesso></dados>';

        }

    }

    private static function _consultarExistenciaVinculo($dados)
    {

        $objMdPetVincRepresentantDTO = new MdPetVincRepresentantDTO();
        $objMdPetVincRepresentantDTO->setNumIdContato($dados['hdnIdUsuarioProcuracao']);
        $objMdPetVincRepresentantDTO->setNumIdContatoVinc($dados['hdnSelPessoaJuridica']);
        $objMdPetVincRepresentantDTO->setStrStaEstado(MdPetVincRepresentantRN::$RP_ATIVO);
        $objMdPetVincRepresentantDTO->retNumIdContato();
	    return (new MdPetVincRepresentantRN())->contar($objMdPetVincRepresentantDTO) > 0;

    }

    public static function consultarUsuarioValido($params)
    {

        $xml = '<dados>';

        $cpf = array_key_exists('cpf', $params) ? $params['cpf'] : null;

        $objContatoDTO = new ContatoDTO();
        $objContatoDTO->retNumIdUsuarioCadastro();
        $objContatoDTO->retNumIdContato();
        $objContatoDTO->retStrNome();
        $objContatoDTO->setDblCpf(InfraUtil::retirarFormatacao($cpf));
        $objContatoRN = new ContatoRN();
        $arrObjContatoDTO = $objContatoRN->listarRN0325($objContatoDTO);

        if (count($arrObjContatoDTO) > 0) {

            $arrIdContato = InfraArray::converterArrInfraDTO($arrObjContatoDTO, 'IdContato');

            $objUsuarioDTO = new UsuarioDTO();
            $objUsuarioDTO->setNumIdContato($arrIdContato, InfraDTO::$OPER_IN);
            $objUsuarioDTO->retStrStaTipo();
            $objUsuarioDTO->retStrNome();
            $objUsuarioDTO->retStrSinAtivo();
            $objUsuarioDTO->retStrSigla();
            $objUsuarioDTO->retNumIdContato();

            $objUsuarioRN = new UsuarioRN();
            $arrObjUsuarioDTO = $objUsuarioRN->listarRN0490($objUsuarioDTO);

            if (count($arrObjUsuarioDTO) > 0) {
                foreach ($arrObjUsuarioDTO as $objUsuarioDTO) {
                    $xml .= '<contato>';
                    if ($objUsuarioDTO->getStrStaTipo() == UsuarioRN::$TU_EXTERNO && $objUsuarioDTO->getStrSinAtivo() == 'S') {
                        $xml .= '<nu-cpf>' . $params['cpf'] . '</nu-cpf>';
                        $xml .= '<no-usuario>' . $objUsuarioDTO->getStrNome() . '</no-usuario>';
                        $xml .= '<nu-contato>' . $objUsuarioDTO->getNumIdContato() . '</nu-contato>';
                        $xml .= '<sg-contato>' . $objUsuarioDTO->getStrSigla() . '</sg-contato>';
                        $xml .= '<sucesso>1</sucesso>';
                    } elseif ($objUsuarioDTO->getStrStaTipo() == UsuarioRN::$TU_EXTERNO_PENDENTE) {
                        $xml .= '<nu-cpf>' . $params['cpf'] . '</nu-cpf>';
                        $xml .= '<no-usuario>' . $objUsuarioDTO->getStrNome() . '</no-usuario>';
                        $xml .= '<nu-contato>' . $objUsuarioDTO->getNumIdContato() . '</nu-contato>';
                        $xml .= '<sg-contato>' . $objUsuarioDTO->getStrSigla() . '</sg-contato>';
                        $xml .= '<mensagem>pendente</mensagem>';
                        $xml .= '<sucesso>1</sucesso>';
                    }
                    $xml .= '</contato>';
                }
            }
        }

        $xml .= '</dados>';

        return $xml;

    }

    public static function consultarUsuarioValidoProcuracao($params)
    {

        $xml = '<resultado>';
        $cpf = array_key_exists('cpf', $params) ? $params['cpf'] : null;

        $objUsuarioDTO = new UsuarioDTO();
        $objUsuarioDTO->setDblCpfContato(InfraUtil::retirarFormatacao($cpf));
        $objUsuarioDTO->setStrStaTipo(array(UsuarioRN::$TU_EXTERNO_PENDENTE, UsuarioRN::$TU_EXTERNO), InfraDTO::$OPER_IN);
        $objUsuarioDTO->setBolExclusaoLogica(false);
        $objUsuarioDTO->retStrNome();
        $objUsuarioDTO->retDblCpfContato();
        $objUsuarioDTO->retNumIdContato();
        $objUsuarioDTO->retStrStaTipo();
        $objUsuarioDTO->retStrSinAtivo();

        $objUsuarioRN = new UsuarioRN();
        $arrObjUsuarioDTO = $objUsuarioRN->listarRN0490($objUsuarioDTO);

        if (count($arrObjUsuarioDTO) > 0) {
            foreach ($arrObjUsuarioDTO as $usuarioDTO) {
                $xml .= '<contato';
                if ($usuarioDTO->getStrStaTipo() == UsuarioRN::$TU_EXTERNO && $usuarioDTO->getStrSinAtivo() == 'S') {
                    $xml .= ' sucesso="1" ';
                    $xml .= ' id="' . $usuarioDTO->getNumIdContato() . '"';
                    $xml .= ' descricao="' . $usuarioDTO->getStrNome() . '"';
                    $xml .= ' complemento="' . $params['cpf'] . '"';
                } elseif ($usuarioDTO->getStrStaTipo() == UsuarioRN::$TU_EXTERNO_PENDENTE) {
                    $xml .= ' sucesso="false" ';
                    $xml .= ' mensagem="Usuário Externo com pendência de liberação de cadastro" ';
                } elseif ($usuarioDTO->getStrSinAtivo() == 'N') {
                    $xml .= ' sucesso="false" ';
                    $xml .= ' mensagem="Usuário Externo está com o cadastro desativado" ';
                }
                $xml .= '></contato>';
            }
        }

        $xml .= '</resultado>';

        return $xml;

    }

}
