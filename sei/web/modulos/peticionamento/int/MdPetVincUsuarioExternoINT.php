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

//
    public static function validarExistenciaProcuracao($dados)
    {


        if ($dados['tipoProc'] == "S") {
            $poderes = explode('-', $dados['poderes']);
            //Validação para Procuração Especial
            $objMdPetVincRepresentantDTO = new MdPetVincRepresentantDTO();
            $objMdPetVincRepresentantRN = new MdPetVincRepresentantRN();
            $objMdPetVincRepresentantDTO->setStrStaEstado('A');
            //$objMdPetVincRepresentantDTO->retStrDataCadastro();
            $objMdPetVincRepresentantDTO->setStrSinAtivo('S');
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
            //DAta
            $objMdPetVincRepresentantSimplesDTO = new MdPetVincRepresentantDTO();
            $objMdPetVincRepresentantSimplesRN = new MdPetVincRepresentantRN();
            $objMdPetVincRepresentantSimplesDTO->setStrStaEstado('A');
            $objMdPetVincRepresentantSimplesDTO->retNumIdMdPetRelPoder();
            $objMdPetVincRepresentantSimplesDTO->setStrSinAtivo('S');
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
                //PRocessos

                if ($dados['processos'] != "") {
                    $processos = array();
                    $tpProcesso = PaginaSEIExterna::getInstance()->getArrItensTabelaDinamica($dados['processos']);
                    foreach ($tpProcesso as $value) {
                        $processos[] = $value[0];
                    }
                } else {
                    $processos = "";
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


            if (count($arrObjMdPetVincRepresentantSimplesFinal) > 0 || count($objMdPetVincRepresentantDTO) > 0) {
                $objMdPetVincRepresentantDTO = array_unique(array_merge($objMdPetVincRepresentantDTO, $arrObjMdPetVincRepresentantSimplesFinal));
                $arrImplode = implode("-", $objMdPetVincRepresentantDTO);
                $xml = '';
                $xml .= '<itens>';
                $xml .= '<item id="' . InfraString::formatarXML(SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=peticionamento_usuario_externo_vinc_validacao_procuracao&id_represent=' . $arrImplode)) . '"';
                $xml .= '></item>';
                $xml .= '</itens>';

                return $xml;
            } else {

                $xml = '';
                $xml .= '<itens>';
                $xml .= '<item id="0"';
                $xml .= '></item>';
                $xml .= '</itens>';
                return $xml;

            }
            //Caso seja Procuração Especial
        } else {

            $xml = '';
            $xml .= '<itens>';
            $xml .= '<item id="0"';
            $xml .= '></item>';
            $xml .= '</itens>';
            return $xml;

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
        if ($objUsuarioRN->getStrStaTipo() == "2") {
            $xml .= '<dados>';
            $xml .= '<existe>0</existe>';
            $xml .= '</dados>';
        } else {
            $xml .= '<dados>';
            $xml .= '<existe>1</existe>';
            $xml .= '</dados>';
        }

        return $xml;
    }

    public static function consultarDadosUsuario($dados)
    {
        $existeVinculo = false;
        $xml = '';

        if (isset($dados['hdnSelPessoaJuridica'])) {
            //verifica se o procurador indicado já possui uma procuração naquele mesmo vinculo
            $existeVinculo = self::_consultarExistenciaVinculo($dados);

            if ($existeVinculo) {
                $xml .= '<dados>';
                $xml .= '<sucesso>0</sucesso>';
                $xml .= '</dados>';
                return $xml;
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
        $existeVinculo = false;
        $xml = '';

        /* if(isset($dados['hdnSelPessoaJuridica'])) {
             //verifica se o procurador indicado já possui uma procuração naquele mesmo vinculo
             $existeVinculo = self::_consultarExistenciaVinculo($dados);

            if($existeVinculo) {
                 $xml .= '<dados>';
                 $xml .= '<sucesso>0</sucesso>';
                 $xml .= '</dados>';
                 return $xml;
            }
         }*/

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
        $existeVinculo = false;
        $xml = '';

        if (isset($dados['hdnSelPessoaJuridica'])) {
            //verifica se o procurador indicado já possui uma procuração naquele mesmo vinculo
            $objMdPetVincRepresentantDTO = new MdPetVincRepresentantDTO();
            $objMdPetVincRepresentantRN = new MdPetVincRepresentantRN();

            $objMdPetVincRepresentantDTO->setNumIdContato($dados['hdnIdUsuarioProcuracao']);
            $objMdPetVincRepresentantDTO->setNumIdContatoVinc($dados['hdnSelPessoaJuridica']);
            $objMdPetVincRepresentantDTO->setStrStaEstado('A');
            $objMdPetVincRepresentantDTO->setStrSinAtivo('S');
            $objMdPetVincRepresentantDTO->setStrTipoRepresentante('E');
            $objMdPetVincRepresentantDTO->retNumIdContato();

            $objMdPetVincRepresentantDTO = $objMdPetVincRepresentantRN->contar($objMdPetVincRepresentantDTO) > 0;

            if ($objMdPetVincRepresentantDTO) {
                $xml .= '<dados>';
                $xml .= '<sucesso>1</sucesso>';
                $xml .= '</dados>';
                return $xml;
            } else {
                $xml .= '<dados>';
                $xml .= '<sucesso>0</sucesso>';
                $xml .= '</dados>';
                return $xml;
            }
        }


    }


    private static function _consultarExistenciaVinculo($dados)
    {

        $objMdPetVincRepresentantDTO = new MdPetVincRepresentantDTO();
        $objMdPetVincRepresentantRN = new MdPetVincRepresentantRN();

        $objMdPetVincRepresentantDTO->setNumIdContato($dados['hdnIdUsuarioProcuracao']);
        $objMdPetVincRepresentantDTO->setNumIdContatoVinc($dados['hdnSelPessoaJuridica']);
        $objMdPetVincRepresentantDTO->setStrStaEstado('A');
        $objMdPetVincRepresentantDTO->setStrSinAtivo('S');
        $objMdPetVincRepresentantDTO->retNumIdContato();

        $objMdPetVincRepresentantDTO = $objMdPetVincRepresentantRN->contar($objMdPetVincRepresentantDTO) > 0;

        return $objMdPetVincRepresentantDTO;
    }


    public static function consultarUsuarioValido($params)
    {

        $xml = '<dados>';

        $cpf = array_key_exists('cpf', $params) ? $params['cpf'] : null;

        $objContatoDTO = new ContatoDTO();
        $objContatoDTO->retNumIdUsuarioCadastro();
        $objContatoDTO->retNumIdContato();
        $objContatoDTO->retStrNome();

        $objMdPetContatoRN = new MdPetContatoRN();
//    $objContatoDTO->setNumIdTipoContato($objMdPetContatoRN->getIdTipoContatoUsExt());
        $objContatoDTO->setDblCpf(InfraUtil::retirarFormatacao($cpf));
//    if($cpf) {        
//        if (is_numeric(SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno())){
//            $objUsuarioRN = new UsuarioRN();
//            $objUsuarioDTO = new UsuarioDTO();
//            $objUsuarioDTO->retNumIdContato();
//            $objUsuarioDTO->setDblCpfContato(InfraUtil::retirarFormatacao($cpf));
////            $objUsuarioDTO->setNumIdUsuario(SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno());
//
//            $arrObjUsuarioDTO = $objUsuarioRN->listarRN0490($objUsuarioDTO);
//
//            if (count($arrObjUsuarioDTO)>0){
//                $objContatoDTO->setDblCpf(0);
//            }else{
//                $objContatoDTO->setDblCpf(InfraUtil::retirarFormatacao($cpf));
//            }
//        }
//    }else{
//        $objContatoDTO->setDblCpf(0);
//    }


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
//        $objUsuarioDTO->setStrSinAtivo(array('N', 'S'), InfraDTO::$OPER_IN);
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
