<?
/**
 * ANATEL
 *
 * 22/04/2016 - criado por Ramon Veloso - ramon.onix@gmail.com
 *
 */

require_once dirname(__FILE__) . '/../../../SEI.php';


class PeticionamentoWS extends InfraWS
{

    public function getObjInfraLog()
    {
        return LogSEI::getInstance();
    }

    public function listarPoderesLegais($siglaSistema, $identificacaoServico)
    {
        try {
            $infraException = new InfraException();

            InfraDebug::getInstance()->setBolLigado(false);
            InfraDebug::getInstance()->setBolDebugInfra(false);
            InfraDebug::getInstance()->limpar();

            SessaoSEI::getInstance(false);

            $objServicoDTO = self::obterServico($siglaSistema, $identificacaoServico, OperacaoServicoRN::$TS_LISTAR_CONTATOS);

            $this->validarAcessoAutorizado(explode(',', str_replace(' ', '', $objServicoDTO->getStrServidor())));

            $mdPetTipoPoderLegalDTO = new MdPetTipoPoderLegalDTO();
            $mdPetTipoPoderLegalRN = new MdPetTipoPoderLegalRN();

            $mdPetTipoPoderLegalDTO->setBolExclusaoLogica(false);
            $mdPetTipoPoderLegalDTO->retStrSinAtivo();
            $mdPetTipoPoderLegalDTO->retNumIdTipoPoderLegal();
            $mdPetTipoPoderLegalDTO->retStrNome();
            $arrPoderLegal = $mdPetTipoPoderLegalRN->listar($mdPetTipoPoderLegalDTO);

            $ret = array();
            if ($arrPoderLegal) {
                foreach ($arrPoderLegal as $item) {
                    $objTipoPoderLegal = new MdPetTipoPoderLegalAPIWS();
                    $objTipoPoderLegal->setIdTipoPodeLegal($item->getNumIdTipoPoderLegal());
                    $objTipoPoderLegal->setNome($item->getStrNome());
                    $objTipoPoderLegal->setSinAtivo($item->getStrSinAtivo());
                    $ret[$item->getNumIdTipoPoderLegal()] = $objTipoPoderLegal;
                }

            } else {
                throw new InfraException('Lista de poderes legais n�o encontrada.');
            }

            return $ret;

        } catch (Exception $e) {
            $this->processarExcecao($e);
        }
    }

    public function listarTiposRepresentacao($siglaSistema, $identificacaoServico)
    {
        try {
            $infraException = new InfraException();

            InfraDebug::getInstance()->setBolLigado(false);
            InfraDebug::getInstance()->setBolDebugInfra(false);
            InfraDebug::getInstance()->limpar();

            SessaoSEI::getInstance(false);

            $objServicoDTO = self::obterServico($siglaSistema, $identificacaoServico, OperacaoServicoRN::$TS_LISTAR_CONTATOS);

            $this->validarAcessoAutorizado(explode(',', str_replace(' ', '', $objServicoDTO->getStrServidor())));

            $mdPetVincRepresentantDTO = new MdPetVincRepresentantDTO();
            $mdPetVincRepresentantRN = new MdPetVincRepresentantRN();

            $mdPetVincRepresentantDTO->setStrSinAtivo('S');
            $mdPetVincRepresentantDTO->retStrTipoRepresentante();
            $mdPetVincRepresentantDTO->setDistinct(true);
            $arrTipoRepres = $mdPetVincRepresentantRN->listar($mdPetVincRepresentantDTO);

            $ret = array();
            $nome = '';
            if ($arrTipoRepres) {
                foreach ($arrTipoRepres as $item) {
                    if ($item->getStrTipoRepresentante() == MdPetVincRepresentantRN::$PE_PROCURADOR_SIMPLES) {
                        $nome = MdPetVincRepresentantRN::$STR_PROCURADOR_SIMPLES;
                    }
                    if ($item->getStrTipoRepresentante() == MdPetVincRepresentantRN::$PE_PROCURADOR_ESPECIAL) {
                        $nome = MdPetVincRepresentantRN::$STR_PROCURADOR_ESPECIAL;
                    }
                    if ($item->getStrTipoRepresentante() == MdPetVincRepresentantRN::$PE_RESPONSAVEL_LEGAL) {
                        $nome = MdPetVincRepresentantRN::$STR_RESPONSAVEL_LEGAL;
                    }
                    $objMdPetTipoRepresentacao = new MdPetTipoRepresentacaoAPIWS();
                    $objMdPetTipoRepresentacao->setNome($nome);
                    $objMdPetTipoRepresentacao->setStrTipoRepresentacao($item->getStrTipoRepresentante());
                    $ret[] = $objMdPetTipoRepresentacao;
                }
            } else {
                $infraException->lancarValidacao('Lista de tipos de representa��es n�o encontrada.');
            }

            return $ret;

        } catch (Exception $e) {
            $this->processarExcecao($e);
        }
    }

    public function listarSituacoesRepresentacao($siglaSistema, $identificacaoServico)
    {
        try {
            $infraException = new InfraException();

            InfraDebug::getInstance()->setBolLigado(false);
            InfraDebug::getInstance()->setBolDebugInfra(false);
            InfraDebug::getInstance()->limpar();

            SessaoSEI::getInstance(false);

            $objServicoDTO = self::obterServico($siglaSistema, $identificacaoServico, OperacaoServicoRN::$TS_LISTAR_CONTATOS);

            $this->validarAcessoAutorizado(explode(',', str_replace(' ', '', $objServicoDTO->getStrServidor())));

            $mdPetVincRepresentantDTO = new MdPetVincRepresentantDTO();
            $mdPetVincRepresentantRN = new MdPetVincRepresentantRN();

            $mdPetVincRepresentantDTO->retStrStaEstado();
            $mdPetVincRepresentantDTO->setDistinct(true);
            $arrStaRepres = $mdPetVincRepresentantRN->listar($mdPetVincRepresentantDTO);

            $ret = array();
            $estado = '';
            if ($arrStaRepres) {
                foreach ($arrStaRepres as $item) {
                    if ($item->getStrStaEstado() == MdPetVincRepresentantRN::$RP_ATIVO) {
                        $estado = "Ativa";
                    }
                    if ($item->getStrStaEstado() == MdPetVincRepresentantRN::$RP_SUSPENSO) {
                        $estado = "Suspensa";
                    }
                    if ($item->getStrStaEstado() == MdPetVincRepresentantRN::$RP_REVOGADA) {
                        $estado = "Revogada";
                    }
                    if ($item->getStrStaEstado() == MdPetVincRepresentantRN::$RP_RENUNCIADA) {
                        $estado = "Renunciada";
                    }
                    if ($item->getStrStaEstado() == MdPetVincRepresentantRN::$RP_VENCIDA) {
                        $estado = "Vencida";
                    }
                    if ($item->getStrStaEstado() == MdPetVincRepresentantRN::$RP_SUBSTITUIDA) {
                        $estado = "Substitu�da";
                    }
                    $objMdPetSituacaoRepresentacao = new MdPetSituacaoRepresentacaoAPIWS();
                    $objMdPetSituacaoRepresentacao->setStaEstado($item->getStrStaEstado());
                    $objMdPetSituacaoRepresentacao->setNome($estado);
                    $ret[] = $objMdPetSituacaoRepresentacao;
                }
            } else {
                $infraException->lancarValidacao('Lista de tipos de representa��es n�o encontrada.');
            }
//            $ret = asort($ret);
            return $ret;

        } catch (Exception $e) {
            $this->processarExcecao($e);
        }
    }

    public function listarRepresentacaoPessoaJuridica($siglaSistema, $identificacaoServico, $cnpjOutorgante, $staSituacao, $idsTipoPoderLegal)
    {
        try {
            $infraException = new InfraException();

            if (strlen(trim($cnpjOutorgante)) > 0 && !InfraUtil::validarCnpj($cnpjOutorgante)) {
                $infraException->lancarValidacao('N�mero de CNPJ inv�lido.');
            }

            if (!empty($idsTipoPoderLegal)) {
                foreach ($idsTipoPoderLegal as $key => $idTipoPoderLegal) {
                    if ($idTipoPoderLegal->IdTipoPoderLegal === "") {
                        unset($idsTipoPoderLegal[$key]);
                    }
                }
                if (!empty($idsTipoPoderLegal)) {
                    $arrIdsTipoPoderLegal = $this->validarIdsTipoPoderLegal($idsTipoPoderLegal);
                }
            }

            InfraDebug::getInstance()->setBolLigado(false);
            InfraDebug::getInstance()->setBolDebugInfra(false);
            InfraDebug::getInstance()->limpar();

            SessaoSEI::getInstance(false);

            $objServicoDTO = self::obterServico($siglaSistema, $identificacaoServico, OperacaoServicoRN::$TS_LISTAR_CONTATOS);

            $this->validarAcessoAutorizado(explode(',', str_replace(' ', '', $objServicoDTO->getStrServidor())));

            $cnpjSemFormato = InfraUtil::retirarFormatacao($cnpjOutorgante);

            $mdPetVincRepresentantRN = new MdPetVincRepresentantRN();
            $mdPetVincRepresentantDTO = new MdPetVincRepresentantDTO();
            $mdPetVincRepresentantDTO->setStrSinAtivo('S');
            $mdPetVincRepresentantDTO->setStrCNPJ($cnpjSemFormato);

            if (!empty($staSituacao)) {
                $mdPetVincRepresentantDTO->setStrStaEstado($staSituacao);
            }

            $filtraPoderLegal = false;
            if (!empty($arrIdsTipoPoderLegal)) {
                $filtraPoderLegal = true;
            }

            $mdPetVincRepresentantDTO->setStrSinAtivo('S');
            $mdPetVincRepresentantDTO->retNumIdContatoVinc();
            $mdPetVincRepresentantDTO->retNumIdContato();
            $mdPetVincRepresentantDTO->retNumIdMdPetVinculoRepresent();
            $mdPetVincRepresentantDTO->retStrStaEstado();
            $mdPetVincRepresentantDTO->retStrRazaoSocialNomeVinc();
            $mdPetVincRepresentantDTO->retStrCNPJ();
            $mdPetVincRepresentantDTO->retStrCPF();
            $mdPetVincRepresentantDTO->retStrTipoRepresentante();
            $mdPetVincRepresentantDTO->retDthDataLimite();
            $mdPetVincRepresentantDTO->retStrStaAbrangencia();
            $mdPetVincRepresentantDTO->retStrStaEstado();
            $mdPetVincRepresentantDTO->retStrTipoRepresentante();
            $mdPetVincRepresentantDTO->retDblIdProcedimentoVinculo();

            $arrRepres = $mdPetVincRepresentantRN->listar($mdPetVincRepresentantDTO);

            $ret = array();
            if ($arrRepres) {
                foreach ($arrRepres as $item) {
                    $contatoRN = new ContatoRN();
                    $contatoDTO = new ContatoDTO();
                    $contatoDTO->setNumIdContato($item->getNumIdContato());
                    $contatoDTO->retStrNome();
                    $contatoDTO->retDblCpf();
                    $arrContatoDTO = $contatoRN->consultarRN0324($contatoDTO);

                    if ($arrContatoDTO) {
                        $dataLimite = (empty($item->getDthDataLimite()) ? '' : $item->getDthDataLimite());

                        $arrPoderLegal = $this->recuperarPoderLegal($item->getNumIdMdPetVinculoRepresent());
                        $arrProtocolo = $this->recuperarProtocoloFormatado($item->getNumIdMdPetVinculoRepresent());

                        if ($filtraPoderLegal) {
                            if ($arrPoderLegal) {
                                foreach ($arrIdsTipoPoderLegal as $idTipoPoderLegal) {
                                    $neededObject = array_filter(
                                        $arrPoderLegal,
                                        function ($e) use ($idTipoPoderLegal) {
                                            return $e->IdTipoPoderLegal == $idTipoPoderLegal;
                                        }
                                    );

                                    $temPoder = count($neededObject);
                                    if ($temPoder) {
                                        $objMdPetRepresentante = new MdPetRepresentanteAPIWS();
                                        $objMdPetRepresentante->setCpf(InfraUtil::formatarCpf($arrContatoDTO->getDblCpf()));
                                        $objMdPetRepresentante->setNome(mb_strtoupper($arrContatoDTO->getStrNome(), 'ISO-8859-1'));
                                        $objMdPetRepresentante->setStaSituacao($item->getStrStaEstado());
                                        $objMdPetRepresentante->setStaTipoRepresentacao($item->getStrTipoRepresentante());
                                        $objMdPetRepresentante->setDataLimite($dataLimite);
                                        $objMdPetRepresentante->setProcessosAbrangencia($arrProtocolo);
                                        $objMdPetRepresentante->setTipoPoderesLegais($arrPoderLegal);
                                        $ret[] = $objMdPetRepresentante;
                                        break;
                                    }
                                }
                            }
                        } else {
                            $objMdPetRepresentante = new MdPetRepresentanteAPIWS();
                            $objMdPetRepresentante->setCpf(InfraUtil::formatarCpf($arrContatoDTO->getDblCpf()));
                            $objMdPetRepresentante->setNome(mb_strtoupper($arrContatoDTO->getStrNome(), 'ISO-8859-1'));
                            $objMdPetRepresentante->setStaSituacao($item->getStrStaEstado());
                            $objMdPetRepresentante->setStaTipoRepresentacao($item->getStrTipoRepresentante());
                            $objMdPetRepresentante->setDataLimite($dataLimite);
                            $objMdPetRepresentante->setProcessosAbrangencia($arrProtocolo);
                            $objMdPetRepresentante->setTipoPoderesLegais($arrPoderLegal);
                            $ret[] = $objMdPetRepresentante;
                        }
                    }
                }
                if (!$ret) {
                    $infraException->lancarValidacao('Nenhum Representante encontrato para o CNPJ informado com os filtros utilizados.');
                }
            } else {
                if (!empty($staSituacao) || !empty($idsTipoPoderLegal)) {
                    $infraException->lancarValidacao('Nenhum Representante encontrato para o CNPJ informado com os filtros utilizados.');
                }
                $infraException->lancarValidacao('O CNPJ informado n�o tem nenhum Representante formalizado pelo Acesso Externo do SEI.');
            }

            return $ret;

        } catch
        (Exception $e) {
            $this->processarExcecao($e);
        }
    }

    public function listarRepresentacaoPessoaFisica($siglaSistema, $identificacaoServico, $cpfOutorgante, $staSituacao, $idsTipoPoderLegal)
    {
        try {
            $infraException = new InfraException();

            if (strlen(trim($cpfOutorgante)) > 0 && !InfraUtil::validarCpf($cpfOutorgante)) {
                $infraException->lancarValidacao('N�mero de CPF inv�lido.');
            }

            if (!empty($idsTipoPoderLegal)) {
                foreach ($idsTipoPoderLegal as $key => $idTipoPoderLegal) {
                    if ($idTipoPoderLegal->IdTipoPoderLegal === "") {
                        unset($idsTipoPoderLegal[$key]);
                    }
                }
                if (!empty($idsTipoPoderLegal)) {
                    $arrIdsTipoPoderLegal = $this->validarIdsTipoPoderLegal($idsTipoPoderLegal);
                }
            }

            InfraDebug::getInstance()->setBolLigado(false);
            InfraDebug::getInstance()->setBolDebugInfra(false);
            InfraDebug::getInstance()->limpar();

            SessaoSEI::getInstance(false);

            $objServicoDTO = self::obterServico($siglaSistema, $identificacaoServico, OperacaoServicoRN::$TS_LISTAR_CONTATOS);

            $this->validarAcessoAutorizado(explode(',', str_replace(' ', '', $objServicoDTO->getStrServidor())));

            $this->validarUsuarioExterno($cpfOutorgante);

            $cpfSemFormato = InfraUtil::retirarFormatacao($cpfOutorgante);

            $mdPetVincRepresentantRN = new MdPetVincRepresentantRN();
            $mdPetVincRepresentantDTO = new MdPetVincRepresentantDTO();
            $mdPetVincRepresentantDTO->setStrCPF($cpfSemFormato);

            if (!empty($staSituacao)) {
                $mdPetVincRepresentantDTO->setStrStaEstado($staSituacao);
            }

            $filtraPoderLegal = false;
            if (!empty($arrIdsTipoPoderLegal)) {
                $filtraPoderLegal = true;
            }

            $mdPetVincRepresentantDTO->setStrSinAtivo('S');
            $mdPetVincRepresentantDTO->retNumIdContatoVinc();
            $mdPetVincRepresentantDTO->retNumIdContato();
            $mdPetVincRepresentantDTO->retNumIdMdPetVinculoRepresent();
            $mdPetVincRepresentantDTO->retStrStaEstado();
            $mdPetVincRepresentantDTO->retStrRazaoSocialNomeVinc();
            $mdPetVincRepresentantDTO->retStrCNPJ();
            $mdPetVincRepresentantDTO->retStrCPF();
            $mdPetVincRepresentantDTO->retStrTipoRepresentante();
            $mdPetVincRepresentantDTO->retDthDataLimite();
            $mdPetVincRepresentantDTO->retStrStaAbrangencia();
            $mdPetVincRepresentantDTO->retStrStaEstado();
            $mdPetVincRepresentantDTO->retStrTipoRepresentante();
            $mdPetVincRepresentantDTO->retDblIdProcedimentoVinculo();

            $arrRepres = $mdPetVincRepresentantRN->listar($mdPetVincRepresentantDTO);

            $ret = array();
            if ($arrRepres) {
                foreach ($arrRepres as $item) {
                    $contatoRN = new ContatoRN();
                    $contatoDTO = new ContatoDTO();
                    $contatoDTO->setNumIdContato($item->getNumIdContato());
                    $contatoDTO->retStrNome();
                    $contatoDTO->retDblCpf();
                    $arrContatoDTO = $contatoRN->consultarRN0324($contatoDTO);

                    if ($arrContatoDTO) {
                        $dataLimite = (empty($item->getDthDataLimite()) ? '' : $item->getDthDataLimite());

                        $arrPoderLegal = $this->recuperarPoderLegal($item->getNumIdMdPetVinculoRepresent());
                        $arrProtocolo = $this->recuperarProtocoloFormatado($item->getNumIdMdPetVinculoRepresent());

                        if ($filtraPoderLegal) {
                            if ($arrPoderLegal) {
                                foreach ($arrIdsTipoPoderLegal as $idTipoPoderLegal) {
                                    $neededObject = array_filter(
                                        $arrPoderLegal,
                                        function ($e) use ($idTipoPoderLegal) {
                                            return $e->IdTipoPoderLegal == $idTipoPoderLegal;
                                        }
                                    );

                                    $temPoder = count($neededObject);
                                    if ($temPoder) {
                                        $objMdPetRepresentante = new MdPetRepresentanteAPIWS();
                                        $objMdPetRepresentante->setCpf(InfraUtil::formatarCpf($arrContatoDTO->getDblCpf()));
                                        $objMdPetRepresentante->setNome(mb_strtoupper($arrContatoDTO->getStrNome(), 'ISO-8859-1'));
                                        $objMdPetRepresentante->setStaSituacao($item->getStrStaEstado());
                                        $objMdPetRepresentante->setStaTipoRepresentacao($item->getStrTipoRepresentante());
                                        $objMdPetRepresentante->setDataLimite($dataLimite);
                                        $objMdPetRepresentante->setProcessosAbrangencia($arrProtocolo);
                                        $objMdPetRepresentante->setTipoPoderesLegais($arrPoderLegal);
                                        $ret[] = $objMdPetRepresentante;
                                        break;
                                    }
                                }
                            }
                        } else {
                            $objMdPetRepresentante = new MdPetRepresentanteAPIWS();
                            $objMdPetRepresentante->setCpf(InfraUtil::formatarCpf($arrContatoDTO->getDblCpf()));
                            $objMdPetRepresentante->setNome(mb_strtoupper($arrContatoDTO->getStrNome(), 'ISO-8859-1'));
                            $objMdPetRepresentante->setStaSituacao($item->getStrStaEstado());
                            $objMdPetRepresentante->setStaTipoRepresentacao($item->getStrTipoRepresentante());
                            $objMdPetRepresentante->setDataLimite($dataLimite);
                            $objMdPetRepresentante->setProcessosAbrangencia($arrProtocolo);
                            $objMdPetRepresentante->setTipoPoderesLegais($arrPoderLegal);
                            $ret[] = $objMdPetRepresentante;
                        }
                    }
                }
                if (!$ret) {
                    $infraException->lancarValidacao('Nenhum Representante encontrato para o CPF informado com os filtros utilizados.');
                }
            } else {
                if (!empty($staSituacao) || !empty($idsTipoPoderLegal)) {
                    $infraException->lancarValidacao('Nenhum Representante encontrato para o CPF informado com os filtros utilizados.');
                }
                $infraException->lancarValidacao('O CPF informado n�o tem nenhum Representante formalizado pelo Acesso Externo do SEI.');
            }

            return $ret;

        } catch (Exception $e) {
            $this->processarExcecao($e);
        }
    }

    public function consultarUsuarioExterno($SiglaSistema, $IdentificacaoServico, $Cpf, $Email = "")
    {
        try {
            $InfraException = new InfraException();

            // Valida E-mail.
            if($Email != "") {
                if (!InfraUtil::validarEmail($Email)) {
                    $InfraException->lancarValidacao('E-mail inv�lido.');
                }
            }

            // Valida CPF se informado.
            if (strlen(trim($Cpf)) > 0 && !InfraUtil::validarCpf($Cpf)) {
                $InfraException->lancarValidacao('N�mero de CPF inv�lido.');
            }

            InfraDebug::getInstance()->setBolLigado(false);
            InfraDebug::getInstance()->setBolDebugInfra(false);
            InfraDebug::getInstance()->limpar();

            SessaoSEI::getInstance(false);

            $objServicoDTO = self::obterServico($SiglaSistema, $IdentificacaoServico);

            $this->validarAcessoAutorizado(explode(',', str_replace(' ', '', $objServicoDTO->getStrServidor())));

            $UsuarioExternoDTO = new MdPetWsUsuarioExternoDTO();
            $UsuarioExternoRN = new MdPetWsUsuarioExternoRN();
            $Cpf = preg_replace('/[^0-9]/', '', $Cpf);
            $UsuarioExternoDTO = $UsuarioExternoRN->consultarExterno($Cpf, $Email);
            $ret = array();
            foreach ($UsuarioExternoDTO as $usuarioExterno) {
                $contatoDTO = new ContatoDTO();
                $contatoDTO->retTodos(true);
                $contatoDTO->setNumIdContato($usuarioExterno->getNumIdContato());
                $contatoRN = new ContatoRN();
                $contatoDTO = $contatoRN->consultarRN0324($contatoDTO);

                if (strlen(trim($Cpf)) > 0 && (InfraUtil::formatarCpf($contatoDTO->getDblCpf()) !== InfraUtil::formatarCpf($Cpf))) {
                    $InfraException->lancarValidacao('CPF informado n�o corresponde ao registrado no cadastro do Usu�rio Externo no SEI.');
                }

                // Usu�rio Externo Liberado = L, Pendente = P
                switch ($usuarioExterno->getStrStaTipo()) {
                    case UsuarioRN::$TU_EXTERNO_PENDENTE :
                        $usuarioExterno->setStrStaTipo('P');
                        break;

                    case UsuarioRN::$TU_EXTERNO :
                        $usuarioExterno->setStrStaTipo('L');
                        break;

                    default :
                        $InfraException->lancarValidacao('Erro ao consultar o cadastro do Usu�rio Externo no SEI.');
                        break;
                }




                $objMdPetUsuarioExternoAPIWS = new MdPetUsuarioExternoAPIWS();
                $objMdPetUsuarioExternoAPIWS->setIdUsuario($usuarioExterno->getNumIdUsuario());
                $objMdPetUsuarioExternoAPIWS->setEmail($usuarioExterno->getStrSigla());
                $objMdPetUsuarioExternoAPIWS->setNome($usuarioExterno->getStrNome());
                $objMdPetUsuarioExternoAPIWS->setCpf(InfraUtil::formatarCpf($contatoDTO->getDblCpf()));
                $objMdPetUsuarioExternoAPIWS->setSituacaoAtivo($usuarioExterno->getStrSinAtivo());
                $objMdPetUsuarioExternoAPIWS->setLiberacaoCadastro($usuarioExterno->getStrStaTipo());
                $objMdPetUsuarioExternoAPIWS->setRg($usuarioExterno->getDblRgContato());
                $objMdPetUsuarioExternoAPIWS->setOrgaoExpedidor($usuarioExterno->getStrOrgaoExpedidorContato());
                $objMdPetUsuarioExternoAPIWS->setTelefone($usuarioExterno->getStrTelefoneFixo());
                $objMdPetUsuarioExternoAPIWS->setEndereco($usuarioExterno->getStrEnderecoContato());
                $objMdPetUsuarioExternoAPIWS->setBairro($contatoDTO->getStrBairro());
                $objMdPetUsuarioExternoAPIWS->setSiglaUf($contatoDTO->getStrSiglaUf());
                $objMdPetUsuarioExternoAPIWS->setNomeCidade($contatoDTO->getStrNomeCidade());
                $objMdPetUsuarioExternoAPIWS->setCep($contatoDTO->getStrCep());
                $objMdPetUsuarioExternoAPIWS->setDataCadastro($usuarioExterno->getDthDataCadastroContato());
                $ret[] = $objMdPetUsuarioExternoAPIWS;
            }
            return $ret;

        } catch (Exception $e) {
            $this->processarExcecao($e);
        }
    }

    public function listarRepresentados($siglaSistema, $identificacaoServico, $cpfOutorgado, $StaSituacao = "")
    {
        try {
            $infraException = new InfraException();

            if (empty($cpfOutorgado) || $cpfOutorgado == null) {
                throw new InfraException('CPF n�o informado.');
            }

            // Valida CPF se informado.
            if (strlen(trim($cpfOutorgado)) > 0 && !InfraUtil::validarCpf($cpfOutorgado)) {
                $infraException->lancarValidacao('N�mero de CPF inv�lido.');
            }

            InfraDebug::getInstance()->setBolLigado(false);
            InfraDebug::getInstance()->setBolDebugInfra(false);
            InfraDebug::getInstance()->limpar();

            SessaoSEI::getInstance(false);

            $objServicoDTO = self::obterServico($siglaSistema, $identificacaoServico, OperacaoServicoRN::$TS_LISTAR_CONTATOS);

            $this->validarAcessoAutorizado(explode(',', str_replace(' ', '', $objServicoDTO->getStrServidor())));

            $this->validarUsuarioExterno($cpfOutorgado);

            $cpfSemFormato = InfraUtil::retirarFormatacao($cpfOutorgado);

            $mdPetVincRepresentantRN = new MdPetVincRepresentantRN();
            $mdPetVincRepresentantDTO = new MdPetVincRepresentantDTO();

            $mdPetVincRepresentantDTO->setStrCpfProcurador($cpfSemFormato);
            if($StaSituacao != "") {
                $mdPetVincRepresentantDTO->setStrStaEstado($StaSituacao);
            }
            $mdPetVincRepresentantDTO->retStrSinAtivo();
            $mdPetVincRepresentantDTO->retNumIdContatoVinc();
            $mdPetVincRepresentantDTO->retNumIdContato();
            $mdPetVincRepresentantDTO->retNumIdContatoOutorg();
            $mdPetVincRepresentantDTO->retNumIdMdPetVinculoRepresent();
            $mdPetVincRepresentantDTO->retStrStaEstado();
            $mdPetVincRepresentantDTO->retStrRazaoSocialNomeVinc();
            $mdPetVincRepresentantDTO->retStrCNPJ();
            $mdPetVincRepresentantDTO->retStrCPF();
            $mdPetVincRepresentantDTO->retStrCpfProcurador();
            $mdPetVincRepresentantDTO->retStrTipoRepresentante();
            $mdPetVincRepresentantDTO->retDthDataLimite();
            $mdPetVincRepresentantDTO->retStrStaAbrangencia();
            $mdPetVincRepresentantDTO->retStrStaEstado();
            $mdPetVincRepresentantDTO->retStrTipoRepresentante();
            $mdPetVincRepresentantDTO->retDblIdProcedimentoVinculo();

            $arrRepres = $mdPetVincRepresentantRN->listar($mdPetVincRepresentantDTO);

            $ret = array();
            if ($arrRepres) {
                foreach ($arrRepres as $item) {
                    $contatoRN = new ContatoRN();
                    $contatoDTO = new ContatoDTO();
                    $contatoDTO->setNumIdContato($item->getNumIdContatoVinc());
                    $contatoDTO->retStrSinAtivo();
                    $arrRepresentadoAtivo = $contatoRN->consultarRN0324($contatoDTO);

                    if ($arrRepresentadoAtivo) {
                        $contatoVincRN = new ContatoRN();
                        $contatoVincDTO = new ContatoDTO();
                        $contatoVincDTO->setNumIdContato($item->getNumIdContato());
                        $contatoVincDTO->retStrSinAtivo();
                        $contatoVincDTO->retStrNome();
                        $contatoVincDTO->retStrEmail();
                        $contatoVincDTO->retDblCpf();
                        $arrContatoVincDTO = $contatoVincRN->consultarRN0324($contatoVincDTO);

                        $cnpjCpf = is_null($item->getStrCNPJ()) ? InfraUtil::formatarCpf($item->getStrCPF()) : InfraUtil::formatarCnpj($item->getStrCNPJ());

                        $mdPetRelVincRepTpPoderRN = new MdPetRelVincRepTpPoderRN();
                        $objMdPetRelVincRepTpPoderDTO = new MdPetRelVincRepTpPoderDTO();
                        $objMdPetRelVincRepTpPoderDTO->retTodos();
                        $objMdPetRelVincRepTpPoderDTO->setNumIdVinculoRepresent($item->getNumIdMdPetVinculoRepresent());

                        $objMrrObjMdPetRelVinRepTpPoderDTO = $mdPetRelVincRepTpPoderRN->listar($objMdPetRelVincRepTpPoderDTO);

                        if($objMrrObjMdPetRelVinRepTpPoderDTO) {
                            $mdPetTipoPoderLegalRN = new MdPetTipoPoderLegalRN();
                            $objMdPetTipoPoderLegal = new MdPetTipoPoderLegalDTO();
                            $objMdPetTipoPoderLegal->retTodos();
                            $objMdPetTipoPoderLegal->retStrSinAtivo();
                            $objMdPetTipoPoderLegal->setNumIdTipoPoderLegal(InfraArray::converterArrInfraDTO($objMrrObjMdPetRelVinRepTpPoderDTO, "IdTipoPoderLegal"), InfraDTO::$OPER_IN);
                            $arrObjMdPetTipoPoderLegal = $mdPetTipoPoderLegalRN->listar($objMdPetTipoPoderLegal);

                            $arrTipoPoderesLegais = [];
                            if($arrObjMdPetTipoPoderLegal) {
                                foreach ($arrObjMdPetTipoPoderLegal as $objTipoPoderLegal) {
                                    $objMdPetTipoPoderLegalAPI = new MdPetTipoPoderLegalAPIWS();
                                    $objMdPetTipoPoderLegalAPI->setIdTipoPodeLegal($objTipoPoderLegal->getNumIdTipoPoderLegal());
                                    $objMdPetTipoPoderLegalAPI->setNome($objTipoPoderLegal->getStrNome());
                                    $objMdPetTipoPoderLegalAPI->setSinAtivo($objTipoPoderLegal->getStrSinAtivo());
                                    $arrTipoPoderesLegais[] = $objMdPetTipoPoderLegalAPI;
                                }
                            }
                        } else {
                            $arrTipoPoderesLegais = [];
                        }

                        $mdPetRelVincRepProtocDTO = new MdPetRelVincRepProtocRN();
                        $objMdPetRelVincRepProtocDTO = new MdPetRelVincRepProtocDTO();
                        $objMdPetRelVincRepProtocDTO->retTodos();
                        $objMdPetRelVincRepProtocDTO->setNumIdVincRepresent($item->getNumIdMdPetVinculoRepresent());
                        $arrObjMdPetRelVincRepProtocDTO = $mdPetRelVincRepProtocDTO->listar($objMdPetRelVincRepProtocDTO);

                        $arrProtocolos = [];
                        if($arrObjMdPetRelVincRepProtocDTO){
                            foreach($arrObjMdPetRelVincRepProtocDTO as $itemObjMdPetRelVincRepProtocDTO){
                                $objProtocolo = new ProtocoloDTO();
                                $protocoloRN = new ProtocoloRN();
                                $objProtocolo->retStrProtocoloFormatado();
                                $objProtocolo->setDblIdProtocolo($itemObjMdPetRelVincRepProtocDTO->getNumIdProtocolo());
                                $objProtocolo = $protocoloRN->consultarRN0186($objProtocolo);
                                $objMdPetProcessoAbrangencia = new MdPetProcessoAbrangenciaAPIWS();
                                $objMdPetProcessoAbrangencia->setProtocoloFormatado($objProtocolo->getStrProtocoloFormatado());
                                $arrProtocolos[] = $objMdPetProcessoAbrangencia;
                            }
                        }

                        $objMdPetRepresentante = new MdPetRepresentanteAPIWS();
                        $objMdPetRepresentante->setNome($arrContatoVincDTO->getStrNome());
                        $objMdPetRepresentante->setCpf(InfraUtil::formatarCpf($arrContatoVincDTO->getDblCpf()));
                        $objMdPetRepresentante->setEmail($arrContatoVincDTO->getStrEmail());
                        $objMdPetRepresentante->setStaSituacao($item->getStrStaEstado());
                        $objMdPetRepresentante->setStaTipoRepresentacao($item->getStrTipoRepresentante());
                        $objMdPetRepresentante->setTipoPoderesLegais($arrTipoPoderesLegais);
                        $objMdPetRepresentante->setProcessosAbrangencia($arrProtocolos);

                        $dataLimite = "";
                        if(!is_null($item->getDthDataLimite())){
                            $dataLimite = substr($item->getDthDataLimite(),0,10);
                        }

                        $objMdPetRepresentado = new MdPetRepresentadoAPIWS();
                        $objMdPetRepresentado->setCnpjCpf($cnpjCpf);
                        $objMdPetRepresentado->setRazaoSocial($item->getStrRazaoSocialNomeVinc());
                        $objMdPetRepresentado->setDataLimite($dataLimite);
                        $objMdPetRepresentado->setRepresentante($objMdPetRepresentante);

                        $ret[] = $objMdPetRepresentado;
                    }
                }
                if (!$ret) {
                    $infraException->lancarValidacao('Nenhum Representante encontrato para o CPF informado com os filtros utilizados.');
                }
            } else {
                $infraException->lancarValidacao('O CPF informado n�o tem nenhum Representante formalizado pelo Acesso Externo do SEI.');
            }

            return $ret;

        } catch (Exception $e) {
            $this->processarExcecao($e);
        }
    }

    private function validarIdsTipoPoderLegal($idsTipoPoderLegal)
    {
        $infraException = new InfraException();

        $arrIdsTipoPoderLegal = array();
        foreach ($idsTipoPoderLegal as $item) {
            if (!property_exists($item, 'IdTipoPoderLegal')) {
                $infraException->lancarValidacao('idsTipoPoderLegal esperava um array com chave IdTipoPoderLegal.');
            }
            $arrIdsTipoPoderLegal[] = $item->IdTipoPoderLegal;
        }

        return $arrIdsTipoPoderLegal;
    }

    private function recuperarPoderLegal($idMdPetVinculoRepresent)
    {
        $mdPetRelVincRepTpPoderDTO = new MdPetRelVincRepTpPoderDTO();
        $mdPetRelVincRepTpPoderDTO->retNumIdTipoPoderLegal();
        $mdPetRelVincRepTpPoderDTO->setNumIdVinculoRepresent($idMdPetVinculoRepresent);
        $mdPetRelVincRepTpPoderRN = new MdPetRelVincRepTpPoderRN();
        $objTpPoderLegal = $mdPetRelVincRepTpPoderRN->listar($mdPetRelVincRepTpPoderDTO);
        $arrPoder = [];
        if ($objTpPoderLegal) {
            foreach ($objTpPoderLegal as $item) {
                $objMdPetTipoPoderLegalDTO = new MdPetTipoPoderLegalDTO();
                $objMdPetTipoPoderLegalRN = new MdPetTipoPoderLegalRN();
                $objMdPetTipoPoderLegalDTO->retTodos();
                $objMdPetTipoPoderLegalDTO->setNumIdTipoPoderLegal($item->getNumIdTipoPoderLegal());
                $objMdPetTipoPoderLegalDTO = $objMdPetTipoPoderLegalRN->consultar($objMdPetTipoPoderLegalDTO);
                $objMdPetTipoPoderesLegais = new MdPetTipoPoderLegalAPIWS();
                $objMdPetTipoPoderesLegais->setIdTipoPodeLegal($objMdPetTipoPoderLegalDTO->getNumIdTipoPoderLegal());
                $objMdPetTipoPoderesLegais->setNome($objMdPetTipoPoderLegalDTO->getStrNome());
                $objMdPetTipoPoderesLegais->setSinAtivo($objMdPetTipoPoderLegalDTO->getStrSinAtivo());
                $arrPoder[] = $objMdPetTipoPoderesLegais;
            }
        }

        return $arrPoder;

    }

    private function recuperarProtocoloFormatado($idMdPetVinculoRepresent)
    {

        $mdPetRelVincRepProtocRN = new MdPetRelVincRepProtocRN();
        $mdPetRelVincRepProtocDTO = new MdPetRelVincRepProtocDTO();
        $mdPetRelVincRepProtocDTO->retNumIdVincRepresent();
        $mdPetRelVincRepProtocDTO->retNumIdProtocolo();
        $mdPetRelVincRepProtocDTO->setNumIdVincRepresent($idMdPetVinculoRepresent);
        $arrObjMdPetRelVincRepProtocDTO = $mdPetRelVincRepProtocRN->listar($mdPetRelVincRepProtocDTO);

        if ($arrObjMdPetRelVincRepProtocDTO) {
            foreach ($arrObjMdPetRelVincRepProtocDTO as $item) {
                $relProtocoloProtocoloRN = new RelProtocoloProtocoloRN();
                $relProtocoloProtocoloDTO = new RelProtocoloProtocoloDTO();
                $relProtocoloProtocoloDTO->setDblIdProtocolo1($item->getNumIdProtocolo());
                $relProtocoloProtocoloDTO->retDblIdProtocolo1();
                $relProtocoloProtocoloDTO->retStrProtocoloFormatadoProtocolo1();

                $obRjelProtocoloProtocoloDTO = $relProtocoloProtocoloRN->listarRN0187($relProtocoloProtocoloDTO);
                $objMdPetProcessoAbrangencia = new MdPetProcessoAbrangenciaAPIWS();
                $objMdPetProcessoAbrangencia->setProtocoloFormatado($obRjelProtocoloProtocoloDTO[0]->getStrProtocoloFormatadoProtocolo1());
                $arrProtocolo[] = $objMdPetProcessoAbrangencia;

            }

        }

        return $arrProtocolo;

    }

    private function validarUsuarioExterno($cpf)
    {
        $infraException = new InfraException();

        $objUsuarioDTO = new UsuarioDTO();
        $objUsuarioDTO->setDblCpfContato(InfraUtil::retirarFormatacao($cpf));
        $objUsuarioDTO->setStrStaTipo(array(UsuarioRN::$TU_EXTERNO_PENDENTE, UsuarioRN::$TU_EXTERNO), InfraDTO::$OPER_IN);
        $objUsuarioDTO->setBolExclusaoLogica(false);
        $objUsuarioDTO->retStrStaTipo();
        $objUsuarioDTO->retStrSinAtivo();

        $mdPetAcessoExternoRN = new UsuarioRN();
        $arrObjUsuarioDTO = $mdPetAcessoExternoRN->listarRN0490($objUsuarioDTO);

        if (!count($arrObjUsuarioDTO)) {
            $infraException->lancarValidacao('O CPF informado n�o tem nenhum Representante formalizado pelo Acesso Externo do SEI.');
        }

    }

    private function obterServico($siglaSistema, $identificacaoServico, $operacaoExigida = null)
    {

        if (empty($siglaSistema) || $siglaSistema == null) {
            throw new InfraException('Sistema n�o informado.');
        }
        if (empty($identificacaoServico) || $identificacaoServico == null) {
            throw new InfraException('Servi�o n�o informado.');
        }

        $objUsuarioDTO = new UsuarioDTO();
        $objUsuarioDTO->retNumIdUsuario();
        $objUsuarioDTO->setStrSigla($siglaSistema);
        $objUsuarioDTO->setStrStaTipo(UsuarioRN::$TU_SISTEMA);

        $objUsuarioRN = new UsuarioRN();
        $objUsuarioDTO = $objUsuarioRN->consultarRN0489($objUsuarioDTO);

        if ($objUsuarioDTO == null) {
            throw new InfraException('Sistema [' . $siglaSistema . '] n�o encontrado.');
        }

        $objServicoDTO = new ServicoDTO();
        $objServicoDTO->retNumIdServico();
        $objServicoDTO->retStrIdentificacao();
        $objServicoDTO->retStrSiglaUsuario();
        $objServicoDTO->retNumIdUsuario();
        $objServicoDTO->retStrServidor();
        $objServicoDTO->retStrSinLinkExterno();
        $objServicoDTO->retNumIdContatoUsuario();
        $objServicoDTO->setNumIdUsuario($objUsuarioDTO->getNumIdUsuario());
        $objServicoDTO->setStrIdentificacao($identificacaoServico);

        $objServicoRN = new ServicoRN();
        $objServicoDTO = $objServicoRN->consultar($objServicoDTO);

        if ($objServicoDTO == null) {
            throw new InfraException('Servi�o [' . $identificacaoServico . '] do sistema [' . $siglaSistema . '] n�o foi encontrado.');
        } else {
            if ($operacaoExigida) {
                $operacaoServicoDTO = new OperacaoServicoDTO();
                $operacaoServicoRN = new OperacaoServicoRN();
                $operacaoServicoDTO->setNumStaOperacaoServico($operacaoExigida);
                $operacaoServicoDTO->setNumIdServico($objServicoDTO->getNumIdServico());
                $operacaoServicoDTO->retNumIdServico();
                $objOperacaoServicoDTO = $operacaoServicoRN->listar($operacaoServicoDTO);

                if (empty($objOperacaoServicoDTO)) {
                    throw new InfraException('Opera��o n�o permitida.');
                }
            }
        }

        return $objServicoDTO;

    }
}

$servidorSoap = new SoapServer("wspeticionamento.wsdl", array('encoding' => 'ISO-8859-1'));

$servidorSoap->setClass("PeticionamentoWS");

//S� processa se acessado via POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $servidorSoap->handle();
}

?>