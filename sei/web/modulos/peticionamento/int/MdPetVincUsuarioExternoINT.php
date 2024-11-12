<?
/**
 * TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
 *
 * 08/02/2012 - criado por bcu
 * 26/08/2024 - Atualização por gabrielg.colab - SPASSU
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

	    if ( $dados['tipoProc'] == 'S' ) {
		
		    // DEFININDO AS VARIAVEIS DA NOVA PROCURACAO
		    $dados['validade'] = ($dados['validade'] != 'Indeterminada') ? $dados['validade'] . ' 23:59:59' : null;
		    $poderesNova = explode('-', $dados['poderes']);
		    $abrangenciaNova = [];
		    
		    if ( !empty($dados['processos']) ) {
			    $tpProcesso = PaginaSEIExterna::getInstance()->getArrItensTabelaDinamica($dados['processos']);
			    foreach ($tpProcesso as $value) {
				    $abrangenciaNova[] = $value[0];
			    }
		    }
		
		    $abrangenciaSimplesNova = !empty($abrangenciaNova) ? 'E' : 'Q';
		    
		    // BUSCA POR PROCURACOES ESPECIAIS EXISTENTES
            $objMdPetVincRepresentantDTO = new MdPetVincRepresentantDTO();
            $objMdPetVincRepresentantDTO->setStrStaEstado(MdPetVincRepresentantRN::$RP_ATIVO);
            $objMdPetVincRepresentantDTO->adicionarCriterio(
                array('IdContato', 'IdContatoVinc', 'TipoRepresentante'),
                array(InfraDTO::$OPER_IGUAL, InfraDTO::$OPER_IGUAL, InfraDTO::$OPER_IGUAL),
                array($dados['idOutorgado'], $dados['idOutorgante'], MdPetVincRepresentantRN::$PE_PROCURADOR_ESPECIAL),
                array(InfraDTO::$OPER_LOGICO_AND, InfraDTO::$OPER_LOGICO_AND));
            $objMdPetVincRepresentantDTO->retNumIdContato();
            $objMdPetVincRepresentantDTO->retNumIdMdPetVinculoRepresent();
            $arrObjMdPetVincRepresentantDTO = (new MdPetVincRepresentantRN())->listar($objMdPetVincRepresentantDTO);
            
            // SE JA EXISTIR PROCURACAO ESPECIAL MOSTRA MODAL DE CONFLITO
		    if( !empty($arrObjMdPetVincRepresentantDTO) ){
			    $arrIdVinculosEspeciaisExistente = implode('-', InfraArray::converterArrInfraDTO($arrObjMdPetVincRepresentantDTO, 'IdMdPetVinculoRepresent'));
			    return '<itens><item id="' . InfraString::formatarXML(SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=peticionamento_usuario_externo_vinc_validacao_procuracao&id_represent=' . $arrIdVinculosEspeciaisExistente)) . '"></item></itens>';
		    }
		    
		    $gerarProcuracao = true;
		    $arrIdMdPetVinculoRepresentSimplesFinal = [];
		    $arrPoderesConflitantesFinal = [];
		    $arrProcessosConflitantesFinal = [];
		    $conflito_processos_poderes = false;
		
		    // BUSCA POR PROCURACOES SIMPLES EXISTENTES
            $objMdPetVincRepresentantSimplesDTO = new MdPetVincRepresentantDTO();
		    $objMdPetVincRepresentantSimplesDTO->retStrStaEstado();
            $objMdPetVincRepresentantSimplesDTO->retDthDataCadastro();
            $objMdPetVincRepresentantSimplesDTO->retDthDataLimite();
            $objMdPetVincRepresentantSimplesDTO->retStrStaAbrangencia();
		    $objMdPetVincRepresentantSimplesDTO->retNumIdContato();
		    $objMdPetVincRepresentantSimplesDTO->retNumIdMdPetVinculoRepresent();
			// $objMdPetVincRepresentantSimplesDTO->retNumIdMdPetRelPoder();
		    $objMdPetVincRepresentantSimplesDTO->setStrStaEstado(MdPetVincRepresentantRN::$RP_ATIVO);
            $objMdPetVincRepresentantSimplesDTO->adicionarCriterio(
                array('IdContato', 'IdContatoVinc', 'TipoRepresentante'),
                array(InfraDTO::$OPER_IGUAL, InfraDTO::$OPER_IGUAL, InfraDTO::$OPER_IGUAL),
                array($dados['idOutorgado'], $dados['idOutorgante'], MdPetVincRepresentantRN::$PE_PROCURADOR_SIMPLES),
                array(InfraDTO::$OPER_LOGICO_AND, InfraDTO::$OPER_LOGICO_AND));
            $arrObjMdPetVincRepresentantSimplesDTO = (new MdPetVincRepresentantRN())->listar($objMdPetVincRepresentantSimplesDTO);
            
            if( !empty($arrObjMdPetVincRepresentantSimplesDTO) ){
	
	            foreach( $arrObjMdPetVincRepresentantSimplesDTO as $objMdPetVincRepresentantSimplesExistente ){
		
		            $abrangenciaSimplesExistente = $objMdPetVincRepresentantSimplesExistente->getStrStaAbrangencia();
		            $idMdPetVincRepresentantSimplesExistente = $objMdPetVincRepresentantSimplesExistente->getNumIdMdPetVinculoRepresent();
		            
		            if($abrangenciaSimplesExistente == 'E'){
			            // BUSCA PROCESSOS ESPECIFICOS CASO HAJAM
			            $objMdPetRelVincRepProtocDTO = new MdPetRelVincRepProtocDTO();
			            $objMdPetRelVincRepProtocDTO->retNumIdProtocolo();
			            $objMdPetRelVincRepProtocDTO->setNumIdVincRepresent($idMdPetVincRepresentantSimplesExistente);
			            $arrMdPetRelVincRepProtocDTO = (new MdPetRelVincRepProtocRN())->listar($objMdPetRelVincRepProtocDTO);
			            $arrIdProtocoloSimplesExistente = InfraArray::converterArrInfraDTO($arrMdPetRelVincRepProtocDTO, 'IdProtocolo');
		            }
		
		            // BUSCA OS PODERES LEGAIS DA EXISTENTE
		            $objMdPetRelVincRepTpPoderDTO = new MdPetRelVincRepTpPoderDTO();
		            $objMdPetRelVincRepTpPoderDTO->retNumIdTipoPoderLegal();
		            $objMdPetRelVincRepTpPoderDTO->setNumIdVinculoRepresent($idMdPetVincRepresentantSimplesExistente);
		            $arrObjMdPetRelVincRepTpPoderDTO = (new MdPetRelVincRepTpPoderRN())->listar($objMdPetRelVincRepTpPoderDTO);
		            $arrIdTipoPoderLegalSimplesExistente = InfraArray::converterArrInfraDTO($arrObjMdPetRelVincRepTpPoderDTO, 'IdTipoPoderLegal');
		            
					$possuiProcessoComum = array_intersect($abrangenciaNova, $arrIdProtocoloSimplesExistente);
		            $possuiPoderComum = array_intersect($poderesNova, $arrIdTipoPoderLegalSimplesExistente);
		
		            if( $abrangenciaSimplesExistente == 'E' && $abrangenciaSimplesNova == 'E' && !empty($possuiProcessoComum) && !empty($possuiPoderComum) ){
			            $gerarProcuracao = false;
			            $conflito_processos_poderes = true;
			            $arrProcessosConflitantesFinal = array_unique(array_merge($possuiProcessoComum, $arrProcessosConflitantesFinal));
		            }
		
		            if( $abrangenciaSimplesExistente == 'Q' && $abrangenciaSimplesNova == 'E' && !empty($possuiPoderComum) ){
			            $gerarProcuracao = false;
		            }
		
		            if( $abrangenciaSimplesExistente == 'Q' && $abrangenciaSimplesNova == 'Q' && !empty($possuiPoderComum) ){
			            $gerarProcuracao = false;
		            }
		
		            if( !$gerarProcuracao ) {
			            $arrIdMdPetVinculoRepresentSimplesFinal = array_unique(array_merge([$idMdPetVincRepresentantSimplesExistente], $arrIdMdPetVinculoRepresentSimplesFinal));
			            $arrPoderesConflitantesFinal = array_unique(array_merge($possuiPoderComum, $arrPoderesConflitantesFinal));
		            }
		
	            }
            	
            }
            
            if( !empty($arrIdMdPetVinculoRepresentSimplesFinal) ){
	
	            $conflitoRepresentacao  = implode('-', $arrIdMdPetVinculoRepresentSimplesFinal);
	            $conflitoPoderes    = implode('-', $arrPoderesConflitantesFinal);
	            $conflitoProcessos  = implode('-', $arrProcessosConflitantesFinal);
	            
	            $msg_conflito = $conflito_processos_poderes ? 'processos_e_poderes' : 'poderes';
	            
	            return '<itens><item id="' . InfraString::formatarXML(SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=peticionamento_usuario_externo_vinc_validacao_procuracao&id_represent=' . $conflitoRepresentacao . '&msg_conflito=' . $msg_conflito . '&conflitosPoderes=' . $conflitoPoderes . '&conflitoProcessos=' . $conflitoProcessos )) . '"></item></itens>';
            	
            }else{
	
	            return '<itens><item id="0"></item></itens>';
            	
            }
            
        }

	    if( $dados['tipoProc'] == 'E' ){

			$objMdPetVincRepresentantDTO = new MdPetVincRepresentantDTO();
			$objMdPetVincRepresentantDTO->setStrStaEstado(MdPetVincRepresentantRN::$RP_ATIVO);
			$objMdPetVincRepresentantDTO->adicionarCriterio(
				array('IdContato', 'IdContatoVinc', 'TipoRepresentante'),
				array(InfraDTO::$OPER_IN, InfraDTO::$OPER_IGUAL, InfraDTO::$OPER_IN),
				array(explode('#', $dados['idOutorgado']), $dados['idOutorgante'], [MdPetVincRepresentantRN::$PE_PROCURADOR_ESPECIAL, MdPetVincRepresentantRN::$PE_PROCURADOR, MdPetVincRepresentantRN::$PE_PROCURADOR_SIMPLES]),
				array(InfraDTO::$OPER_LOGICO_AND, InfraDTO::$OPER_LOGICO_AND));
			$objMdPetVincRepresentantDTO->retNumIdContato();
			$objMdPetVincRepresentantDTO->retNumIdMdPetVinculoRepresent();
			$objMdPetVincRepresentantDTO = (new MdPetVincRepresentantRN())->listar($objMdPetVincRepresentantDTO);

			if( !empty($objMdPetVincRepresentantDTO) ){

				$arrImplode = implode('-', InfraArray::converterArrInfraDTO($objMdPetVincRepresentantDTO, 'IdMdPetVinculoRepresent'));
				$msg_conflito = 'especial';
				
				return '<itens><item id="' . InfraString::formatarXML(SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=peticionamento_usuario_externo_vinc_validacao_procuracao&id_represent=' . $arrImplode . '&msg_conflito=' . $msg_conflito)) . '"></item></itens>';
				
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
        $objUsuarioDTO = (new UsuarioRN())->consultarRN0489($usuarioDTO);
        
        if(!empty($objUsuarioDTO)){
	        return '<dados><existe>'.($objUsuarioDTO->getStrStaTipo() == UsuarioRN::$TU_EXTERNO_PENDENTE ? 0 : 1).'</existe></dados>';
        }

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
        $objUsuarioDTO->setStrSinAtivo('S');
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
                if ($usuarioDTO->getStrStaTipo() == UsuarioRN::$TU_EXTERNO) {
                    $xml .= ' sucesso="1" ';
                    $xml .= ' id="' . $usuarioDTO->getNumIdContato() . '"';
                    $xml .= ' descricao="' . $usuarioDTO->getStrNome() . '"';
                    $xml .= ' complemento="' . $params['cpf'] . '"';
                } elseif ($usuarioDTO->getStrStaTipo() == UsuarioRN::$TU_EXTERNO_PENDENTE) {
                    $xml .= ' sucesso="false" ';
                    $xml .= ' mensagem="Usuário Externo com cadastro pendente de liberação. Faça contato com a administração do SEI do Órgão." ';
                }
                $xml .= '></contato>';
            }
        }

        $xml .= '</resultado>';

        return $xml;

    }

}
