<?
/**
 *
 * 22/08/2016 - criado por marcelo.bezerra - CAST
 * 26/08/2024 - Atualização por gabrielg.colab - SPASSU
 *
 */

require_once dirname(__FILE__) . '/../../../SEI.php';

class MdPetContatoINT extends ContatoINT
{

    public static function getContatoByCPFCNPJ($cpfcnpj)
    {

        $objContextoContatoDTO = new ContatoDTO();

        $objContextoContatoDTO->retStrNome();
        $objContextoContatoDTO->retNumIdContato();
        $objContextoContatoDTO->retStrSigla();
        $objContextoContatoDTO->retStrSinAtivo();
        $objContextoContatoDTO->retNumIdUsuarioCadastro();

        $objContextoContatoDTO->adicionarCriterio(array('Cpf', 'Cnpj', 'SinAtivo'),

            array(InfraDTO::$OPER_IGUAL, InfraDTO::$OPER_IGUAL, InfraDTO::$OPER_IGUAL),

            array($cpfcnpj, $cpfcnpj, 'S'),

            array(InfraDTO::$OPER_LOGICO_OR,
                InfraDTO::$OPER_LOGICO_AND)
        );

        $objContatoRN = new ContatoRN();
        $objContextoContatoDTO = $objContatoRN->consultarRN0324($objContextoContatoDTO);

        return $objContextoContatoDTO;
    }

    public static function getTotalContatoByCPFCNPJ($cpfcnpj)
    {
        //Contato
        $objContextoContatoDTO = new ContatoDTO();
        $objContextoContatoDTO->retStrNome();
        $objContextoContatoDTO->retNumIdContato();
        $objContextoContatoDTO->retNumIdUsuarioCadastro();
        $objContextoContatoDTO->retStrSigla();
        $objContextoContatoDTO->retStrSinAtivo();
        $objContextoContatoDTO->setDistinct(true);
        $objContextoContatoDTO->adicionarCriterio(array('Cpf', 'Cnpj'),
            array(InfraDTO::$OPER_IGUAL, InfraDTO::$OPER_IGUAL),
            array($cpfcnpj, $cpfcnpj),
            array(InfraDTO::$OPER_LOGICO_OR)
        );

        $objContatoRN = new ContatoRN();
        $arrObjContextoContatoDTO = $objContatoRN->listarRN0325($objContextoContatoDTO);

        if (count($arrObjContextoContatoDTO) == 0) {
            return null;
        } else if (count($arrObjContextoContatoDTO) > 1) {
            $arrObjContextoContato = InfraArray::converterArrInfraDTO($arrObjContextoContatoDTO, 'IdUsuarioCadastro');
            $arrObjContextoContato = array_filter($arrObjContextoContato);
            if (count($arrObjContextoContato) > 0) {
                //Usuário Externo
                $objUsuarioDTO = new UsuarioDTO();
                $objUsuarioDTO->retNumIdUsuario();
                $objUsuarioDTO->setStrStaTipo(UsuarioRN::$TU_EXTERNO);
                $objUsuarioDTO->setDistinct(true);
                $objUsuarioDTO->adicionarCriterio(
                    array('IdUsuario'),
                    array(InfraDTO::$OPER_IN),
                    array($arrObjContextoContato)
                );

                $objUsuarioRN = new UsuarioRN();
                $arrObjUsuarioDTO = $objUsuarioRN->listarRN0490($objUsuarioDTO);

                if (count($arrObjUsuarioDTO) > 0) {
                    $arrObjUsuario = InfraArray::converterArrInfraDTO($arrObjUsuarioDTO, 'IdUsuario');

                    //Contato Filtrado
                    $objContextoContatoDTO = new ContatoDTO();
                    $objContextoContatoDTO->retStrNome();
                    $objContextoContatoDTO->retNumIdContato();
                    $objContextoContatoDTO->retNumIdUsuarioCadastro();
                    $objContextoContatoDTO->retStrSigla();
                    $objContextoContatoDTO->retStrSinAtivo();
                    $objContextoContatoDTO->setDistinct(true);
                    $objContextoContatoDTO->setOrd('IdContato', InfraDTO::$TIPO_ORDENACAO_DESC);
                    $objContextoContatoDTO->adicionarCriterio(array('Cpf', 'Cnpj'),
                        array(InfraDTO::$OPER_IGUAL, InfraDTO::$OPER_IGUAL),
                        array($cpfcnpj, $cpfcnpj),
                        array(InfraDTO::$OPER_LOGICO_OR)
                    );
                    $objContextoContatoDTO->adicionarCriterio(array('IdUsuarioCadastro'),
                        array(InfraDTO::$OPER_DIFERENTE),
                        array(NULL)
                    );
                    $objContextoContatoDTO->adicionarCriterio(
                        array('IdUsuarioCadastro'),
                        array(InfraDTO::$OPER_IN),
                        array($arrObjUsuario)
                    );
                    $objContatoRN = new ContatoRN();
                    $arrObjContextoContatoDTO = $objContatoRN->listarRN0325($objContextoContatoDTO);

                } else {
                    return null;
                }
            } else {
                return null;
            }
        }
        return $arrObjContextoContatoDTO;
    }

    public static function getContatosNomeAutoComplete($strPalavrasPesquisa)
    {

        foreach ($strPalavrasPesquisa as $i => $usuarios) {

            $contatoIntimacao = MdPetContatoINT::getDadosContatos($usuarios->getNumIdContato(), $_GET['id_documento'], false);

            if ($contatoIntimacao['Intimacao'] == 0) {

                $objContextoContatoDTO = new ContatoDTO();
                $objContextoContatoDTO->retTodos();
                $objContextoContatoDTO->setNumIdContato($usuarios->getNumIdContato());

                $objContatoRN = new ContatoRN();
                $arrContextoContatoDTO[$i] = $objContatoRN->consultarRN0324($objContextoContatoDTO);
            }

        }
        return $arrContextoContatoDTO;
    }

    public static function getContatosNomeAutoCompletePF($strPalavrasPesquisa)
    {

        foreach ($strPalavrasPesquisa as $i => $usuarios) {

            // $contatoIntimacao = MdPetContatoINT::getDadosContatos($usuarios->getNumIdContato(), $_GET['id_documento'], false);

            // if ($contatoIntimacao['Intimacao'] == 0) {

                $objContextoContatoDTO = new ContatoDTO();
                $objContextoContatoDTO->retTodos();
                $objContextoContatoDTO->setNumIdContato($usuarios->getNumIdContato());

                $objContatoRN = new ContatoRN();
                $arrContextoContatoDTO[$i] = $objContatoRN->consultarRN0324($objContextoContatoDTO);
            // }

        }

        $xml = '';
        $xml .= '<itens>';
        if ($arrContextoContatoDTO !== null) {
            foreach ($arrContextoContatoDTO as $dto) {
                $xml .= '<item id="' . $dto->get('IdContato') . '"';
                $xml .= ' descricao="' . $dto->get('Nome') . '"';
                $xml .= ' complemento="' . $dto->get('Email') . ' - ' . InfraUtil::formatarCpf($dto->get('Cpf')) . '"';
                $xml .= '></item>';
            }
        }
        $xml .= '</itens>';

        return $xml;
    }

    public static function getDadosContatos($idContato, $idDocumento, $xml = true)
    {

        $arrSituacao = MdPetIntRelDestinatarioINT::getArraySituacaoRelatorio();
        $possuiIntimacao = 0;

        $objContextoContatoDTO = new ContatoDTO();
        $objContextoContatoDTO->retTodos();
        $objContextoContatoDTO->setNumIdContato($idContato);

        $objContatoRN = new ContatoRN();
        $arrContextoContatoDTO = $objContatoRN->consultarRN0324($objContextoContatoDTO);

        //BuscaDestinatrio
        $objDestinatarioDTO = new MdPetIntRelDestinatarioDTO();
        $objDestinatarioDTO->retTodos();
        $objDestinatarioDTO->setNumIdContato($idContato);
        
        $objDestinatarioRN = new MdPetIntRelDestinatarioRN();
        $arrDestinatarioDTO = $objDestinatarioRN->listar($objDestinatarioDTO);

        if (is_iterable($arrDestinatarioDTO) && !empty($arrContextoContatoDTO)) {
            //Busca Intimacao Documento Principal
            foreach ($arrDestinatarioDTO as $destinatario) {
                $objDocumentoIntimacaoDTO = new MdPetIntProtocoloDTO();
                $objDocumentoIntimacaoDTO->retTodos();
                $objDocumentoIntimacaoDTO->setNumIdMdPetIntimacao($destinatario->getNumIdMdPetIntimacao());
                $objDocumentoIntimacaoDTO->setDblIdProtocolo($idDocumento);
                $objDocumentoIntimacaoDTO->setStrSinPrincipal('S');

                $objDocumentoRN = new MdPetIntProtocoloRN();
                $arrDocumentoIntimacao = $objDocumentoRN->consultar($objDocumentoIntimacaoDTO);

                if (!is_null($arrDocumentoIntimacao)) {
                    $possuiIntimacao = $destinatario->getNumIdMdPetIntimacao();
                    $situacao = !is_null($destinatario->getStrStaSituacaoIntimacao()) && $destinatario->getStrStaSituacaoIntimacao() != 0 ? $arrSituacao[$destinatario->getStrStaSituacaoIntimacao()] : MdPetIntimacaoRN::$STR_SITUACAO_NAO_CADASTRADA;
                    $dataIntimacao = $destinatario->getDthDataCadastro() ? substr($destinatario->getDthDataCadastro(), 0, 10) : '';
                }
            }
            $objIntimacaoRN = new MdPetIntimacaoRN();
            $idIntimacao = $possuiIntimacao ? $possuiIntimacao : '';

            $montaLink = str_replace('&', '&amp;', SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_pet_intimacao_consulta&arvore=1&id_documento=' . $idDocumento . '&id_intimacao=' . $possuiIntimacao . '&id_contato=' . $idContato));
        } else {
            $situacao = 'Pendente';
            $possuiIntimacao = 0;
        }

        if ($xml) {

            //Validação
            $empresas = array();
            $contato = '';
            $total = null;

            $dtoMdPetVincReptDTO = new MdPetVincRepresentantDTO();
            $dtoMdPetVincReptDTO->retNumIdContatoVinc();
            $dtoMdPetVincReptDTO->retStrNomeProcurador();
            $dtoMdPetVincReptDTO->setNumIdContatoProcurador($idContato);
            $dtoMdPetVincReptDTO->retNumIdContatoVinc();
            $dtoMdPetVincReptDTO->retStrEmail();
            $dtoMdPetVincReptDTO->retNumIdMdPetVinculoRepresent();
            $dtoMdPetVincReptDTO->retStrTipoRepresentante();
            //$dtoMdPetVincReptDTO->setDistinct(true);
            $dtoMdPetVincReptDTO->retNumIdContatoProcurador();
            $dtoMdPetVincReptDTO->setStrStaEstado(MdPetVincRepresentantRN::$RP_ATIVO);
            //$dtoMdPetVincReptDTO->adicionarCriterio(array('StaEstado', 'StaEstado'), array(InfraDTO::$OPER_IGUAL, InfraDTO::$OPER_IGUAL), array(MdPetVincRepresentantRN::$RP_ATIVO, MdPetVincRepresentantRN::$RP_REVOGADA), InfraDTO::$OPER_LOGICO_OR);

            $rnMdPetVincRepRN = new MdPetVincRepresentantRN();
            $arrObjMdPetVincRepresentantDTO = $rnMdPetVincRepRN->listar($dtoMdPetVincReptDTO);

            foreach ($arrObjMdPetVincRepresentantDTO as $key => $value) {

                if ($value->getStrTipoRepresentante() == MdPetVincRepresentantRN::$PE_PROCURADOR_SIMPLES) {
                    $mdPetRelVincRepTpPoderRN = new MdPetRelVincRepTpPoderRN();
                    $objMdPetRelVincRepTpPoderDTO = new MdPetRelVincRepTpPoderDTO();
                    $objMdPetRelVincRepTpPoderDTO->retTodos();
                    $objMdPetRelVincRepTpPoderDTO->setNumIdVinculoRepresent($value->getNumIdMdPetVinculoRepresent());
                    $arrObjMdPetVincRepresentant = $mdPetRelVincRepTpPoderRN->listar($objMdPetRelVincRepTpPoderDTO);
                    if ($arrObjMdPetVincRepresentant) {
                        foreach ($arrObjMdPetVincRepresentant as $objMdPetVincRepresentant) {
                            if($objMdPetVincRepresentant->getNumIdTipoPoderLegal() == MdPetTipoPoderLegalRN::$PODER_LEGAL_CUMPRIMENTO) {
                                $empresas [] = $value->getNumIdContatoVinc();
                            }
                        }
                    }
                } else{
                    $empresas [] = $value->getNumIdContatoVinc();
                }
            }
                
            if (is_countable($empresas) && count($empresas) > 0) {

                $objDestinatarioDTO = new MdPetIntRelDestinatarioDTO();
                $objDestinatarioDTO->retTodos();
                $objDestinatarioDTO->setDblIdDocumento($idDocumento);
                $objDestinatarioDTO->setNumIdContato($empresas, InfraDTO::$OPER_IN);

                $objDestinatarioRN = new MdPetIntRelDestinatarioRN();
                $arrDestinatarioDTO = $objDestinatarioRN->listar($objDestinatarioDTO);
                $arrContatos = InfraArray::converterArrInfraDTO($arrDestinatarioDTO, 'IdContato');

                if (is_countable($arrContatos) && count($arrContatos) > 0) {
                    //Recuperando contato
                    $objContextoContatoDTO = new ContatoDTO();
                    $objContextoContatoDTO->retTodos();
                    $objContextoContatoDTO->setNumIdContato($arrContatos, InfraDTO::$OPER_IN);
                    $objContatoRN = new ContatoRN();
                    $arrContextoContatoJuridicoDTO = $objContatoRN->listarRN0325($objContextoContatoDTO);

                    //Concatenando cada uma das empresas
                    foreach ($arrContextoContatoJuridicoDTO as $nome) {
                        $contato .= "\n* ".infraUtil::formatarCnpj($nome->getStrCnpj()) . " - " . PaginaSEI::tratarHTML($nome->getStrNome());
                    }

                    $total = count($arrContextoContatoJuridicoDTO);
                }

                $arrDestinatarioDTO = (count($arrDestinatarioDTO) > 0) ? 1 : 0;

            } else {

                $arrDestinatarioDTO = 0;

            }

            //Fim validação
	        
	        if(!empty($arrContextoContatoDTO)){
		        $xml = '<Documento>';
		        $xml .= '<Id>' . $arrContextoContatoDTO->getNumIdContato() . '</Id>';
		        $xml .= '<Nome>' . $arrContextoContatoDTO->getStrNome() . '</Nome>';
		        $xml .= '<Email>' . $arrContextoContatoDTO->getStrEmail() . '</Email>';
		        $xml .= '<Cpf>' . $arrContextoContatoDTO->getDblCpf() . '</Cpf>';
		        $xml .= '<Data>' . substr($arrContextoContatoDTO->getDthCadastro(), 0, 10) . '</Data>';
		        $xml .= '<Situacao>' . $situacao . '</Situacao>';
		        $xml .= '<Intimacao>' . $possuiIntimacao . '</Intimacao>';
		        $xml .= '<Url>' . $montaLink . '</Url>';
		        $xml .= '<DataIntimacao>' . $dataIntimacao . '</DataIntimacao>';
		        $xml .= '<Cadastro>' . $arrDestinatarioDTO . '</Cadastro>';
		        $xml .= '<Vinculo>' . $contato . '</Vinculo>';
		        $xml .= '<Quantidade>' . $total . '</Quantidade>';
		        $xml .= '</Documento>';
	        }
        
        } else {
	
	        if(!empty($arrContextoContatoDTO)){
		        $xml['Id'] = $arrContextoContatoDTO->getNumIdContato();
		        $xml['Nome'] = $arrContextoContatoDTO->getStrNome();
		        $xml['Email'] = $arrContextoContatoDTO->getStrEmail();
		        $xml['Cpf'] = $arrContextoContatoDTO->getDblCpf();
		        $xml['Data'] = substr($arrContextoContatoDTO->getDthCadastro(), 0, 10);
		        $xml['Situacao'] = $situacao;
		        $xml['Intimacao'] = $possuiIntimacao;
		        $xml['Url'] = $montaLink;
		        $xml['DataIntimacao'] = $dataIntimacao;
	        }

        }

        return $xml;
    }

    public static function getDadosContatosLote($idContato, $idDocumento, $xml = true)
    {


        $arrSituacao = MdPetIntRelDestinatarioINT::getArraySituacaoRelatorio();
        $possuiIntimacao = 0;

        $objContextoContatoDTO = new ContatoDTO();
        $objContextoContatoDTO->retTodos();
        $objContextoContatoDTO->setNumIdContato($idContato);

        $objContatoRN = new ContatoRN();
        $arrContextoContatoDTO = $objContatoRN->consultarRN0324($objContextoContatoDTO);

        //BuscaDestinatrio
        $objDestinatarioDTO = new MdPetIntRelDestinatarioDTO();
        $objDestinatarioDTO->retTodos();
        $objDestinatarioDTO->setNumIdContato($idContato);

        $objDestinatarioRN = new MdPetIntRelDestinatarioRN();
        $arrDestinatarioDTO = $objDestinatarioRN->listar($objDestinatarioDTO);

        if (is_iterable($arrDestinatarioDTO)) {
            //Busca Intimacao Documento Principal
            foreach ($arrDestinatarioDTO as $destinatario) {
                $objDocumentoIntimacaoDTO = new MdPetIntProtocoloDTO();
                $objDocumentoIntimacaoDTO->retTodos();
                $objDocumentoIntimacaoDTO->setNumIdMdPetIntimacao($destinatario->getNumIdMdPetIntimacao());
                $objDocumentoIntimacaoDTO->setDblIdProtocolo($idDocumento);
                $objDocumentoIntimacaoDTO->setStrSinPrincipal('S');

                $objDocumentoRN = new MdPetIntProtocoloRN();
                $arrDocumentoIntimacao = $objDocumentoRN->consultar($objDocumentoIntimacaoDTO);

                if (!is_null($arrDocumentoIntimacao)) {
                    $possuiIntimacao = $destinatario->getNumIdMdPetIntimacao();
                    $situacao = !is_null($destinatario->getStrStaSituacaoIntimacao()) && $destinatario->getStrStaSituacaoIntimacao() != 0 ? $arrSituacao[$destinatario->getStrStaSituacaoIntimacao()] : MdPetIntimacaoRN::$STR_SITUACAO_NAO_CADASTRADA;
                    $dataIntimacao = $destinatario->getDthDataCadastro() ? substr($destinatario->getDthDataCadastro(), 0, 10) : '';
                }
            }
            $objIntimacaoRN = new MdPetIntimacaoRN();
            $idIntimacao = $possuiIntimacao ? $possuiIntimacao : '';

            $montaLink = str_replace('&', '&amp;', SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_pet_intimacao_consulta&arvore=1&id_documento=' . $idDocumento . '&id_intimacao=' . $possuiIntimacao . '&id_contato=' . $arrContextoContatoDTO->getNumIdContato()));
        } else {
            $situacao = 'Pendente';
            $possuiIntimacao = 0;
        }

        if ($xml) {

            //Validação
            $empresas = array();
            $contato = '';
            $total = null;

            $dtoMdPetVincReptDTO = new MdPetVincRepresentantDTO();
            $dtoMdPetVincReptDTO->retNumIdContatoVinc();
            $dtoMdPetVincReptDTO->retStrNomeProcurador();
            $dtoMdPetVincReptDTO->setNumIdContatoProcurador($idContato);
            $dtoMdPetVincReptDTO->retNumIdContatoVinc();
            $dtoMdPetVincReptDTO->retStrEmail();
            $dtoMdPetVincReptDTO->retNumIdMdPetVinculoRepresent();
            $dtoMdPetVincReptDTO->retStrTipoRepresentante();
            //$dtoMdPetVincReptDTO->setDistinct(true);
            $dtoMdPetVincReptDTO->retNumIdContatoProcurador();
            $dtoMdPetVincReptDTO->setStrStaEstado(MdPetVincRepresentantRN::$RP_ATIVO);
            //$dtoMdPetVincReptDTO->adicionarCriterio(array('StaEstado', 'StaEstado'), array(InfraDTO::$OPER_IGUAL, InfraDTO::$OPER_IGUAL), array(MdPetVincRepresentantRN::$RP_ATIVO, MdPetVincRepresentantRN::$RP_REVOGADA), InfraDTO::$OPER_LOGICO_OR);

            $rnMdPetVincRepRN = new MdPetVincRepresentantRN();
            $arrObjMdPetVincRepresentantDTO = $rnMdPetVincRepRN->listar($dtoMdPetVincReptDTO);

            foreach ($arrObjMdPetVincRepresentantDTO as $key => $value) {

                if ($value->getStrTipoRepresentante() == MdPetVincRepresentantRN::$PE_PROCURADOR_SIMPLES) {
                    $mdPetRelVincRepTpPoderRN = new MdPetRelVincRepTpPoderRN();
                    $objMdPetRelVincRepTpPoderDTO = new MdPetRelVincRepTpPoderDTO();
                    $objMdPetRelVincRepTpPoderDTO->retTodos();
                    $objMdPetRelVincRepTpPoderDTO->setNumIdVinculoRepresent($value->getNumIdMdPetVinculoRepresent());
                    $arrObjMdPetVincRepresentant = $mdPetRelVincRepTpPoderRN->listar($objMdPetRelVincRepTpPoderDTO);
                    if ($arrObjMdPetVincRepresentant) {
                        foreach ($arrObjMdPetVincRepresentant as $objMdPetVincRepresentant) {
                            if($objMdPetVincRepresentant->getNumIdTipoPoderLegal() == MdPetTipoPoderLegalRN::$PODER_LEGAL_CUMPRIMENTO) {
                                $empresas [] = $value->getNumIdContatoVinc();
                            }
                        }
                    }
                } else{
                    $empresas [] = $value->getNumIdContatoVinc();
                }
            }

            if (is_countable($empresas) && count($empresas) > 0) {

                $objDestinatarioDTO = new MdPetIntRelDestinatarioDTO();
                $objDestinatarioDTO->retTodos();
                $objDestinatarioDTO->setDblIdDocumento($idDocumento);
                $objDestinatarioDTO->setNumIdContato($empresas, InfraDTO::$OPER_IN);

                $objDestinatarioRN = new MdPetIntRelDestinatarioRN();
                $arrDestinatarioDTO = $objDestinatarioRN->listar($objDestinatarioDTO);
                $arrContatos = InfraArray::converterArrInfraDTO($arrDestinatarioDTO, 'IdContato');

                if (is_countable($arrContatos) && count($arrContatos) > 0) {
                    //Recuperando contato
                    $objContextoContatoDTO = new ContatoDTO();
                    $objContextoContatoDTO->retTodos();
                    $objContextoContatoDTO->setNumIdContato($arrContatos, InfraDTO::$OPER_IN);
                    $objContatoRN = new ContatoRN();
                    $arrContextoContatoJuridicoDTO = $objContatoRN->listarRN0325($objContextoContatoDTO);

                    //Concatenando cada uma das empresas
                    foreach ($arrContextoContatoJuridicoDTO as $nome) {
                        $contato .= "\n * ";
                        $contato .= infraUtil::formatarCnpj($nome->getStrCnpj()) . " - " . PaginaSEI::tratarHTML($nome->getStrNome());
                    }

                    $total = count($arrContextoContatoJuridicoDTO);
                }

                $arrDestinatarioDTO = (count($arrDestinatarioDTO) > 0) ? 1 : 0;

            } else {

                $arrDestinatarioDTO = 0;

            }

            //Fim validação

            $xml = '<Documento>';
            $xml .= '<Id>' . $arrContextoContatoDTO->getNumIdContato() . '</Id>';
            $xml .= '<Nome>' . $arrContextoContatoDTO->getStrNome() . '</Nome>';
            $xml .= '<Email>' . $arrContextoContatoDTO->getStrEmail() . '</Email>';
            $xml .= '<Cpf>' . $arrContextoContatoDTO->getDblCpf() . '</Cpf>';
            $xml .= '<Data>' . substr($arrContextoContatoDTO->getDthCadastro(), 0, 10) . '</Data>';
            $xml .= '<Situacao>' . $situacao . '</Situacao>';
            $xml .= '<Intimacao>' . $possuiIntimacao . '</Intimacao>';
            $xml .= '<Url>' . $montaLink . '</Url>';
            $xml .= '<DataIntimacao>' . $dataIntimacao . '</DataIntimacao>';
            $xml .= '<Cadastro>' . $arrDestinatarioDTO . '</Cadastro>';
            $xml .= '<Vinculo>' . $contato . '</Vinculo>';
            $xml .= '<Quantidade>' . $total . '</Quantidade>';
            $xml .= '</Documento>';

        } else {

            $xml['Id'] = $arrContextoContatoDTO->getNumIdContato();
            $xml['Nome'] = $arrContextoContatoDTO->getStrNome();
            $xml['Email'] = $arrContextoContatoDTO->getStrEmail();
            $xml['Cpf'] = $arrContextoContatoDTO->getDblCpf();
            $xml['Data'] = substr($arrContextoContatoDTO->getDthCadastro(), 0, 10);
            $xml['Situacao'] = $situacao;
            $xml['Intimacao'] = $possuiIntimacao;
            $xml['Url'] = $montaLink;
            $xml['DataIntimacao'] = $dataIntimacao;

        }

        return $xml;
    }


    //Juridico


    public static function getContatosNomeAutoCompleteJuridico($strPalavrasPesquisa)
    {

        foreach ($strPalavrasPesquisa as $i => $usuarios) {

            // $contatoIntimacao = MdPetContatoINT::getDadosContatosJuridico($usuarios->getNumIdContatoVinc(), $_GET['id_documento'], false);

            // if ($contatoIntimacao['Intimacao'] == 0) {

                $objContextoContatoDTO = new ContatoDTO();
                $objContextoContatoDTO->retTodos();
                $objContextoContatoDTO->setNumIdContato($usuarios->getNumIdContatoVinc());

                $objContatoRN = new ContatoRN();
                $arrContextoContatoDTO[$i] = $objContatoRN->consultarRN0324($objContextoContatoDTO);
            // }

        }

        $xml = '<itens>';
        if (is_iterable($arrContextoContatoDTO)) {
            foreach ($arrContextoContatoDTO as $dto) {
                $nome = str_replace("<", "lt;", $dto->get('Nome'));
                $xml .= '<item id="' . $dto->get('IdContato') . '"';
                $xml .= ' complemento=" ' . InfraUtil::formatarCnpj($dto->get('Cnpj')) . '"';
                $xml .= ' descricao="' . $nome . '"';
                $xml .= '></item>';
            }
        }
        $xml .= '</itens>';

        return $xml;
    }

    public static function getDadosContatosJuridico($idContato, $idDocumento, $xml = true)
    {

        $arrSituacao = MdPetIntRelDestinatarioINT::getArraySituacaoRelatorio();
        $possuiIntimacao = 0;


        //Juridic


        $dtoMdPetVincReptDTO = new MdPetVincRepresentantDTO();
        $dtoMdPetVincReptDTO->setNumIdContatoVinc($idContato);
        $dtoMdPetVincReptDTO->retNumIdContatoVinc();
        // $dtoMdPetVincReptDTO->setDistinct(true);
        $dtoMdPetVincReptDTO->retNumIdContatoProcurador();
        $dtoMdPetVincReptDTO->retStrRazaoSocialNomeVinc();
	    $dtoMdPetVincReptDTO->setStrStaEstado(MdPetVincRepresentantRN::$RP_ATIVO);
        $rnMdPetVincRepRN = new MdPetVincRepresentantRN();
        $arr = $rnMdPetVincRepRN->listar($dtoMdPetVincReptDTO);


        $objContextoContatoDTO = new ContatoDTO();
        $objContextoContatoDTO->retTodos();
        $objContextoContatoDTO->setNumIdContato($idContato);

        $objContatoRN = new ContatoRN();
        $arrContextoContatoDTO = $objContatoRN->consultarRN0324($objContextoContatoDTO);

        //BuscaDestinatrio
        $objDestinatarioDTO = new MdPetIntRelDestinatarioDTO();
        $objDestinatarioDTO->retTodos();
        $objDestinatarioDTO->setNumIdContato($idContato);

        $objDestinatarioRN = new MdPetIntRelDestinatarioRN();
        $arrDestinatarioDTO = $objDestinatarioRN->listar($objDestinatarioDTO);

        $arrContatos = InfraArray::converterArrInfraDTO($arrDestinatarioDTO, 'IdContato');
        if (is_countable($arrContatos) && count($arrContatos) > 0) {
            //Recuperando contato
            $objContextoContatoDTO = new ContatoDTO();
            $objContextoContatoDTO->retTodos();
            $objContextoContatoDTO->setNumIdContato($arrContatos, InfraDTO::$OPER_IN);
            $objContatoRN = new ContatoRN();
            $arrContextoContatoJuridicoDTO = $objContatoRN->listarRN0325($objContextoContatoDTO);

            //Concatenando cada uma das empresas
            foreach ($arrContextoContatoJuridicoDTO as $nome) {
                $contato .= "\n* ";
                $contato .= infraUtil::formatarCnpj($nome->getStrCnpj()) . " - " . PaginaSEI::tratarHTML($nome->getStrNome());
            }

            $total = count($arrContextoContatoJuridicoDTO);
        }

        if (is_iterable($arrDestinatarioDTO)) {
            //Busca Intimacao Documento Principal
            foreach ($arrDestinatarioDTO as $destinatario) {
                $objDocumentoIntimacaoDTO = new MdPetIntProtocoloDTO();
                $objDocumentoIntimacaoDTO->retTodos();
                $objDocumentoIntimacaoDTO->setNumIdMdPetIntimacao($destinatario->getNumIdMdPetIntimacao());
                $objDocumentoIntimacaoDTO->setDblIdProtocolo($idDocumento);
                $objDocumentoIntimacaoDTO->setStrSinPrincipal('S');

                $objDocumentoRN = new MdPetIntProtocoloRN();
                $arrDocumentoIntimacao = $objDocumentoRN->consultar($objDocumentoIntimacaoDTO);

                if (!is_null($arrDocumentoIntimacao)) {
                    $possuiIntimacao = $destinatario->getNumIdMdPetIntimacao();
                    $situacao = !is_null($destinatario->getStrStaSituacaoIntimacao()) && $destinatario->getStrStaSituacaoIntimacao() != 0 ? $arrSituacao[$destinatario->getStrStaSituacaoIntimacao()] : MdPetIntimacaoRN::$STR_SITUACAO_NAO_CADASTRADA;
                    $dataIntimacao = $destinatario->getDthDataCadastro() ? substr($destinatario->getDthDataCadastro(), 0, 10) : '';
                }
            }
            $objIntimacaoRN = new MdPetIntimacaoRN();
            $idIntimacao = $possuiIntimacao ? $possuiIntimacao : '';

            $montaLink = str_replace('&', '&amp;', SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_pet_intimacao_consulta&arvore=1&id_documento=' . $idDocumento . '&id_intimacao=' . $possuiIntimacao . '&id_contato=' . $arrContextoContatoDTO->getNumIdContato()));
        } else {
            $situacao = 'Pendente';
            $possuiIntimacao = 0;
        }

        $arrDestinatarioDTO = (count($arrDestinatarioDTO) > 0) ? 1 : 0;

        if ($xml) {
            $xml = '<Documento>';
            $xml .= '<Id>' . $arrContextoContatoDTO->getNumIdContato() . '</Id>';
            $xml .= '<Nome>' . PaginaSEI::tratarHTML($arrContextoContatoDTO->getStrNome()) . '</Nome>';
            $xml .= '<Cnpj>' . InfraUtil::formatarCpfCnpj($arrContextoContatoDTO->getStrCnpj()) . '</Cnpj>';
            $xml .= '<Data>' . substr($arrContextoContatoDTO->getDthCadastro(), 0, 10) . '</Data>';
            $xml .= '<Situacao>' . $situacao . '</Situacao>';
            $xml .= '<Intimacao>' . $possuiIntimacao . '</Intimacao>';
            $xml .= '<Url>' . $montaLink . '</Url>';
            $xml .= '<DataIntimacao>' . $dataIntimacao . '</DataIntimacao>';
            $xml .= '<Cadastro>' . $arrDestinatarioDTO . '</Cadastro>';
            $xml .= '<Vinculo>' . $contato . '</Vinculo>';
            $xml .= '<Quantidade>' . $total . '</Quantidade>';
            $xml .= '</Documento>';
        } else {
            $xml['Id'] = $arrContextoContatoDTO->getNumIdContato();
            $xml['Nome'] = PaginaSEI::tratarHTML($arr[0]->getStrRazaoSocialNomeVinc());
            $xml['Cnpj'] = InfraUtil::formatarCpfCnpj($arrContextoContatoDTO->getStrCnpj());
            $xml['Data'] = substr($arrContextoContatoDTO->getDthCadastro(), 0, 10);
            $xml['Situacao'] = $situacao;
            $xml['Intimacao'] = $possuiIntimacao;
            $xml['Url'] = $montaLink;
            $xml['DataIntimacao'] = $dataIntimacao;
        }

        return $xml;
    }

	public static function getDadosContatosJuridicoLote($idContato, $idDocumento, $xml = true)
	{

		$arrSituacao = MdPetIntRelDestinatarioINT::getArraySituacaoRelatorio();
		$dataIntimacao = $contato = $montaLink = '';
		$situacao = 'Pendente';
		$possuiIntimacao =  $totalDestinatarios = 0;

		// Busca o Contato da PJ
		$objContextoContatoDTO = new ContatoDTO();
		$objContextoContatoDTO->retTodos();
		$objContextoContatoDTO->setNumIdContato($idContato);
		$arrContextoContatoDTO = (new ContatoRN())->consultarRN0324($objContextoContatoDTO);

		// Verifica se o documento esta presente em alguma intimacao
		$objDocumentoIntimacaoDTO = new MdPetIntProtocoloDTO();
		$objDocumentoIntimacaoDTO->retTodos();
		$objDocumentoIntimacaoDTO->setDblIdProtocolo($idDocumento);
		$objDocumentoIntimacaoDTO->setStrSinPrincipal('S');
		$arrDocumentoPrincipalIntimacao = (new MdPetIntProtocoloRN())->listar($objDocumentoIntimacaoDTO);

		// Se a lista nao retornar vazia é porque existe intimação para o Documento
		if(!empty($arrDocumentoPrincipalIntimacao) && count($arrDocumentoPrincipalIntimacao) > 0){

			// Verifica se a PJ é um dos destinatários das Intimações
			$pbjMdPetIntRelDestinatarioDTO = new MdPetIntRelDestinatarioDTO();
			$pbjMdPetIntRelDestinatarioDTO->retTodos();
			$pbjMdPetIntRelDestinatarioDTO->setNumIdMdPetIntimacao(array_unique(InfraArray::converterArrInfraDTO($arrDocumentoPrincipalIntimacao, 'IdMdPetIntimacao')), InfraDTO::$OPER_IN);
			$pbjMdPetIntRelDestinatarioDTO->setNumIdContato($idContato);
			$pbjMdPetIntRelDestinatarioDTO->setNumMaxRegistrosRetorno(1);
			$pbjMdPetIntRelDestinatarioDTO->setOrdDthDataCadastro(InfraDTO::$TIPO_ORDENACAO_DESC);
			$objMdPetIntRelDestinatarioDTO = (new MdPetIntRelDestinatarioRN())->consultar($pbjMdPetIntRelDestinatarioDTO);

			// Se for um dos destinatários retorna na validação
			if(!empty($objMdPetIntRelDestinatarioDTO)){

				$contato 			= "\n* " . infraUtil::formatarCnpj($arrContextoContatoDTO->getStrCnpj()) . " - " . PaginaSEI::tratarHTML($arrContextoContatoDTO->getStrNome());
				$possuiIntimacao 	= $objMdPetIntRelDestinatarioDTO->getNumIdMdPetIntimacao();
				$situacao 			= !is_null($objMdPetIntRelDestinatarioDTO->getStrStaSituacaoIntimacao()) && $objMdPetIntRelDestinatarioDTO->getStrStaSituacaoIntimacao() != 0 ? $arrSituacao[$objMdPetIntRelDestinatarioDTO->getStrStaSituacaoIntimacao()] : MdPetIntimacaoRN::$STR_SITUACAO_NAO_CADASTRADA;
				$dataIntimacao 		= $objMdPetIntRelDestinatarioDTO->getDthDataCadastro() ? substr($objMdPetIntRelDestinatarioDTO->getDthDataCadastro(), 0, 10) : '';
				$montaLink 			= str_replace('&', '&amp;', SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_pet_intimacao_consulta&arvore=1&id_documento=' . $idDocumento . '&id_intimacao=' . $possuiIntimacao . '&id_contato=' . $arrContextoContatoDTO->getNumIdContato()));
				$totalDestinatarios = 1;

			}

		}

		if ($xml) {

			$xml = '<Documento>';
			$xml .= '<Id>' . $arrContextoContatoDTO->getNumIdContato() . '</Id>';
			$xml .= '<Nome>' . PaginaSEI::tratarHTML($arrContextoContatoDTO->getStrNome()) . '</Nome>';
			$xml .= '<Cnpj>' . InfraUtil::formatarCpfCnpj($arrContextoContatoDTO->getStrCnpj()) . '</Cnpj>';
			$xml .= '<Data>' . substr($arrContextoContatoDTO->getDthCadastro(), 0, 10) . '</Data>';
			$xml .= '<Situacao>' . $situacao . '</Situacao>';
			$xml .= '<Intimacao>' . $possuiIntimacao . '</Intimacao>';
			$xml .= '<Url>' . $montaLink . '</Url>';
			$xml .= '<DataIntimacao>' . $dataIntimacao . '</DataIntimacao>';
			$xml .= '<Cadastro>' . $totalDestinatarios . '</Cadastro>';
			$xml .= '<Vinculo>' . $contato . '</Vinculo>';
			$xml .= '<Quantidade>' . $totalDestinatarios . '</Quantidade>';
			$xml .= '</Documento>';

		} else {

			$xml['Id'] = $arrContextoContatoDTO->getNumIdContato();
			$xml['Nome'] = PaginaSEI::tratarHTML($arrContextoContatoDTO->getStrNome());
			$xml['Cnpj'] = InfraUtil::formatarCpfCnpj($arrContextoContatoDTO->getStrCnpj());
			$xml['Data'] = substr($arrContextoContatoDTO->getDthCadastro(), 0, 10);
			$xml['Situacao'] = $situacao;
			$xml['Intimacao'] = $possuiIntimacao;
			$xml['Url'] = $montaLink;
			$xml['DataIntimacao'] = $dataIntimacao;

		}

		return $xml;

	}

    public function getDadosContatosJuridicoRecuperar($idContato, $idDocumento, $xml = true)
    {

        $arrSituacao = MdPetIntRelDestinatarioINT::getArraySituacaoRelatorio();
        $possuiIntimacao = 0;


        $objContextoContatoDTO = new ContatoDTO();
        $objContextoContatoDTO->retTodos();
        $objContextoContatoDTO->setNumIdContato($idContato);

        $objContatoRN = new ContatoRN();
        $arrContextoContatoDTO = $objContatoRN->consultarRN0324($objContextoContatoDTO);

        //BuscaDestinatrio
        $objDestinatarioDTO = new MdPetIntRelDestinatarioDTO();
        $objDestinatarioDTO->retTodos();
        $objDestinatarioDTO->setNumIdContato($idContato);

        $objDestinatarioRN = new MdPetIntRelDestinatarioRN();
        $arrDestinatarioDTO = $objDestinatarioRN->listar($objDestinatarioDTO);

        if (is_iterable($arrDestinatarioDTO)) {
            //Busca Intimacao Documento Principal
            foreach ($arrDestinatarioDTO as $destinatario) {
                $objDocumentoIntimacaoDTO = new MdPetIntProtocoloDTO();
                $objDocumentoIntimacaoDTO->retTodos();
                $objDocumentoIntimacaoDTO->setNumIdMdPetIntimacao($destinatario->getNumIdMdPetIntimacao());
                $objDocumentoIntimacaoDTO->setDblIdProtocolo($idDocumento);
                $objDocumentoIntimacaoDTO->setStrSinPrincipal('S');

                $objDocumentoRN = new MdPetIntProtocoloRN();
                $arrDocumentoIntimacao = $objDocumentoRN->consultar($objDocumentoIntimacaoDTO);

                if (!is_null($arrDocumentoIntimacao)) {
                    $possuiIntimacao = $destinatario->getNumIdMdPetIntimacao();
                    $situacao = !is_null($destinatario->getStrStaSituacaoIntimacao()) && $destinatario->getStrStaSituacaoIntimacao() != 0 ? $arrSituacao[$destinatario->getStrStaSituacaoIntimacao()] : MdPetIntimacaoRN::$STR_SITUACAO_NAO_CADASTRADA;
                    $dataIntimacao = $destinatario->getDthDataCadastro() ? substr($destinatario->getDthDataCadastro(), 0, 10) : '';
                }
            }
            $objIntimacaoRN = new MdPetIntimacaoRN();
            $idIntimacao = $possuiIntimacao ? $possuiIntimacao : '';

            $montaLink = str_replace('&', '&amp;', SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_pet_intimacao_consulta&arvore=1&id_documento=' . $idDocumento . '&id_intimacao=' . $possuiIntimacao . '&id_contato=' . $arrContextoContatoDTO->getNumIdContato()));
        } else {
            $situacao = 'Pendente';
            $possuiIntimacao = 0;
        }

        if (!$xml) {
            $xml['Id'] = $arrContextoContatoDTO->getNumIdContato();
            $xml['Nome'] = PaginaSEI::tratarHTML($arrContextoContatoDTO->getStrNome());
            $xml['Cnpj'] = $arrContextoContatoDTO->getStrCnpj();
            $xml['Data'] = substr($arrContextoContatoDTO->getDthCadastro(), 0, 10);
            $xml['Situacao'] = $situacao;
            $xml['Intimacao'] = $possuiIntimacao;
            $xml['Url'] = $montaLink;
            $xml['DataIntimacao'] = $dataIntimacao;
        }

        return $xml;
    }

}
