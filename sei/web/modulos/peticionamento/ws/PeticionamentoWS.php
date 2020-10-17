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
                    $ret[] = (object)array(
                        'IdTipoPoderLegal' => $item->getNumIdTipoPoderLegal(),
                        'Nome' => $item->getStrNome(),
                        'SinAtivo' => $item->getStrSinAtivo(),
                    );
                }
            } else {
                throw new InfraException('Lista de poderes legais nуo encontrada.');
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
                    $ret[] = (object)array(
                        'StrTipoRepresentacao ' => $item->getStrTipoRepresentante(),
                        'Nome' => $nome,
                    );
                }
            } else {
                $infraException->lancarValidacao('Lista de tipos de representaчѕes nуo encontrada.');
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

            $mdPetVincRepresentantDTO->setStrSinAtivo('S');
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
                        $estado = "Substituэda";
                    }
                    $ret[] = (object)array(
                        'StaEstado' => $item->getStrStaEstado(),
                        'Nome' => $estado,
                    );
                }
            } else {
                $infraException->lancarValidacao('Lista de tipos de representaчѕes nуo encontrada.');
            }

            return $ret;

        } catch (Exception $e) {
            $this->processarExcecao($e);
        }
    }

    public function listarRepresentacaoPessoaJuridica($siglaSistema, $identificacaoServico, $cnpjOutorgante, $strSituacao, $idsTipoPoderLegal)
    {
        try {
            $infraException = new InfraException();

            if (strlen(trim($cnpjOutorgante)) > 0 && !InfraUtil::validarCnpj($cnpjOutorgante)) {
                $infraException->lancarValidacao('Nњmero de CNPJ invсlido.');
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

            if (!empty($strSituacao)) {
                $mdPetVincRepresentantDTO->setStrStaEstado($strSituacao);
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
                                        $i = '';
                                        $ret[] = (object)array(
                                            'Cpf' => InfraUtil::formatarCpf($arrContatoDTO->getDblCpf()),
                                            'Nome' => mb_strtoupper($arrContatoDTO->getStrNome(), 'ISO-8859-1'),
                                            'StrSituacao' => $item->getStrStaEstado(),
                                            'StaTipoRepresentacao' => $item->getStrTipoRepresentante(),
                                            'DataLimite' => $dataLimite,
                                            'Abrangencia' => array(
                                                $arrProtocolo
                                            ),
                                            'PoderesLegais' => array(
                                                $arrPoderLegal
                                            )
                                        );
                                        break;
                                    }
                                }
                            }
                        } else {
                            $i = '';
                            $ret[] = (object)array(
                                'Cpf' => InfraUtil::formatarCpf($arrContatoDTO->getDblCpf()),
                                'Nome' => mb_strtoupper($arrContatoDTO->getStrNome(), 'ISO-8859-1'),
                                'StrSituacao' => $item->getStrStaEstado(),
                                'StaTipoRepresentacao' => $item->getStrTipoRepresentante(),
                                'DataLimite' => $dataLimite,
                                'Abrangencia' => array(
                                    $arrProtocolo
                                ),
                                'PoderesLegais' => array(
                                    $arrPoderLegal
                                )
                            );
                        }
                    }
                }
                if (!$ret) {
                    $infraException->lancarValidacao('Nenhum Representante encontrato para o CNPJ informado com os filtros utilizados.');
                }
            } else {
                if (!empty($strSituacao) || !empty($idsTipoPoderLegal)) {
                    $infraException->lancarValidacao('Nenhum Representante encontrato para o CNPJ informado com os filtros utilizados.');
                }
                $infraException->lancarValidacao('O CNPJ informado nуo tem nenhum Representante formalizado pelo Acesso Externo do SEI.');
            }

            return $ret;

        } catch
        (Exception $e) {
            $this->processarExcecao($e);
        }
    }

    public function listarRepresentacaoPessoaFisica($siglaSistema, $identificacaoServico, $cpfOutorgante, $strSituacao, $idsTipoPoderLegal)
    {
        try {
            $infraException = new InfraException();

            if (strlen(trim($cpfOutorgante)) > 0 && !InfraUtil::validarCpf($cpfOutorgante)) {
                $infraException->lancarValidacao('Nњmero de CPF invсlido.');
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
            $mdPetVincRepresentantDTO->setStrSinAtivo('S');
            $mdPetVincRepresentantDTO->setStrCPF($cpfSemFormato);

            if (!empty($strSituacao)) {
                $mdPetVincRepresentantDTO->setStrStaEstado($strSituacao);
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
                                        $ret[] = (object)array(
                                            'Cpf' => InfraUtil::formatarCpf($arrContatoDTO->getDblCpf()),
                                            'Nome' => mb_strtoupper($arrContatoDTO->getStrNome(), 'ISO-8859-1'),
                                            'StrSituacao' => $item->getStrStaEstado(),
                                            'StaTipoRepresentacao' => $item->getStrTipoRepresentante(),
                                            'DataLimite' => $dataLimite,
                                            'Abrangencia' => array(
                                                $arrProtocolo
                                            ),
                                            'PoderesLegais' => array(
                                                $arrPoderLegal
                                            )
                                        );
                                        break;
                                    }
                                }
                            }
                        } else {
                            $ret[] = (object)array(
                                'Cpf' => InfraUtil::formatarCpf($arrContatoDTO->getDblCpf()),
                                'Nome' => mb_strtoupper($arrContatoDTO->getStrNome(), 'ISO-8859-1'),
                                'StrSituacao' => $item->getStrStaEstado(),
                                'StaTipoRepresentacao' => $item->getStrTipoRepresentante(),
                                'DataLimite' => $dataLimite,
                                'Abrangencia' => array(
                                    $arrProtocolo
                                ),
                                'PoderesLegais' => array(
                                    $arrPoderLegal
                                )
                            );
                        }
                    }
                }
                if (!$ret) {
                    $infraException->lancarValidacao('Nenhum Representante encontrato para o CPF informado com os filtros utilizados.');
                }
            } else {
                if (!empty($strSituacao) || !empty($idsTipoPoderLegal)) {
                    $infraException->lancarValidacao('Nenhum Representante encontrato para o CPF informado com os filtros utilizados.');
                }
                $infraException->lancarValidacao('O CPF informado nуo tem nenhum Representante formalizado pelo Acesso Externo do SEI.');
            }

            return $ret;

        } catch
        (Exception $e) {
            $this->processarExcecao($e);
        }
    }

    public function consultarUsuarioExterno($SiglaSistema, $IdentificacaoServico, $Email, $Cpf = "") {
        try {
            $InfraException = new InfraException();

            // Valida E-mail.
            if (! InfraUtil::validarEmail($Email)) {
                $InfraException->lancarValidacao('E-mail invсlido.');
            }

            // Valida CPF se informado.
            if (strlen(trim($Cpf)) > 0 && ! InfraUtil::validarCpf($Cpf)) {
                $InfraException->lancarValidacao('Nњmero de CPF invсlido.');
            }

            InfraDebug::getInstance()->setBolLigado(false);
            InfraDebug::getInstance()->setBolDebugInfra(false);
            InfraDebug::getInstance()->limpar();

            SessaoSEI::getInstance(false);

            $objServicoDTO = self::obterServico($SiglaSistema, $IdentificacaoServico);

            $this->validarAcessoAutorizado(explode(',', str_replace(' ', '', $objServicoDTO->getStrServidor())));

            $UsuarioExternoDTO = new MdPetWsUsuarioExternoDTO();
            $UsuarioExternoRN = new MdPetWsUsuarioExternoRN();

            $UsuarioExternoDTO = $UsuarioExternoRN->consultarExterno($Email);

            $contatoDTO = new ContatoDTO();
            $contatoDTO->retTodos( true );
            $contatoDTO->setNumIdContato( $UsuarioExternoDTO->getNumIdContato() );
            $contatoRN = new ContatoRN();
            $contatoDTO = $contatoRN->consultarRN0324( $contatoDTO );

            if (strlen(trim($Cpf)) > 0 && (InfraUtil::formatarCpf( $contatoDTO->getDblCpf() ) !== InfraUtil::formatarCpf($Cpf))) {
                $InfraException->lancarValidacao('CPF informado nуo corresponde ao registrado no cadastro do Usuсrio Externo no SEI.');
            }

            // Usuсrio Externo Liberado = L, Pendente = P
            switch ($UsuarioExternoDTO->getStrStaTipo()) {
                case UsuarioRN::$TU_EXTERNO_PENDENTE :
                    $UsuarioExternoDTO->setStrStaTipo('P');
                    break;

                case UsuarioRN::$TU_EXTERNO :
                    $UsuarioExternoDTO->setStrStaTipo('L');
                    break;

                default :
                    $InfraException->lancarValidacao('Erro ao consultar o cadastro do Usuсrio Externo no SEI.');
                    break;
            }


            $ret = array ();



            $ret[] = (object) array(
                'IdUsuario' => $UsuarioExternoDTO->getNumIdUsuario(),
                'E-mail' => $UsuarioExternoDTO->getStrSigla(),
                'Nome' => $UsuarioExternoDTO->getStrNome(),
                'Cpf' => InfraUtil::formatarCpf($contatoDTO->getDblCpf()),
                'SituacaoAtivo' => $UsuarioExternoDTO->getStrSinAtivo(),
                'LiberacaoCadastro' => $UsuarioExternoDTO->getStrStaTipo(),
                'Rg' => $UsuarioExternoDTO->getDblRgContato(),
                'OrgaoExpedidor' => $UsuarioExternoDTO->getStrOrgaoExpedidorContato(),
                'Telefone' => $UsuarioExternoDTO->getStrTelefoneFixo(),
                'Endereco' => $UsuarioExternoDTO->getStrEnderecoContato(),
                'Bairro' => $contatoDTO->getStrBairro(),
                'SiglaUf' => $contatoDTO->getStrSiglaUf(),
                'NomeCidade' => $contatoDTO->getStrNomeCidade(),
                'Cep' => $contatoDTO->getStrCep(),
                'DataCadastro' => $UsuarioExternoDTO->getDthDataCadastroContato());

            return $ret;

        } catch ( Exception $e ) {
            $this->processarExcecao ( $e );
        }
    }

    public function listarRepresentacaoUsuarioExterno($cpf) {
        try {
            $InfraException = new InfraException();

            if (empty($Cpf) || $Cpf == null) {
                throw new InfraException('CPF nуo informado.');
            }

            // Valida CPF se informado.
            if (strlen(trim($Cpf)) > 0 && ! InfraUtil::validarCpf($Cpf)) {
                $InfraException->lancarValidacao('Nњmero de CPF invсlido.');
            }

            InfraDebug::getInstance()->setBolLigado(false);
            InfraDebug::getInstance()->setBolDebugInfra(false);
            InfraDebug::getInstance()->limpar();

            SessaoSEI::getInstance(false);

//            $objServicoDTO = self::obterServico($SiglaSistema, $IdentificacaoServico);

//            $this->validarAcessoAutorizado(explode(',', str_replace(' ', '', $objServicoDTO->getStrServidor())));

            $UsuarioExternoDTO = new MdPetWsUsuarioExternoDTO();
            $UsuarioExternoRN = new MdPetWsUsuarioExternoRN();

            $UsuarioExternoDTO = $UsuarioExternoRN->consultarExterno($cpf);

            $contatoDTO = new ContatoDTO();
            $contatoDTO->retTodos( true );
            $contatoDTO->setNumIdContato( $UsuarioExternoDTO->getNumIdContato() );
            $contatoRN = new ContatoRN();
            $contatoDTO = $contatoRN->consultarRN0324( $contatoDTO );

            if (strlen(trim($Cpf)) > 0 && (InfraUtil::formatarCpf( $contatoDTO->getDblCpf() ) !== InfraUtil::formatarCpf($Cpf))) {
                $InfraException->lancarValidacao('CPF informado nуo corresponde ao registrado no cadastro do Usuсrio Externo no SEI.');
            }

            // Usuсrio Externo Liberado = L, Pendente = P
            switch ($UsuarioExternoDTO->getStrStaTipo()) {
                case UsuarioRN::$TU_EXTERNO_PENDENTE :
                    $UsuarioExternoDTO->setStrStaTipo('P');
                    break;

                case UsuarioRN::$TU_EXTERNO :
                    $UsuarioExternoDTO->setStrStaTipo('L');
                    break;

                default :
                    $InfraException->lancarValidacao('Erro ao consultar o cadastro do Usuсrio Externo no SEI.');
                    break;
            }


            $ret = array ();



            $ret[] = (object) array(
                'IdUsuario' => $UsuarioExternoDTO->getNumIdUsuario(),
                'E-mail' => $UsuarioExternoDTO->getStrSigla(),
                'Nome' => $UsuarioExternoDTO->getStrNome(),
                'Cpf' => InfraUtil::formatarCpf($contatoDTO->getDblCpf()),
                'SituacaoAtivo' => $UsuarioExternoDTO->getStrSinAtivo(),
                'LiberacaoCadastro' => $UsuarioExternoDTO->getStrStaTipo(),
                'Rg' => $UsuarioExternoDTO->getDblRgContato(),
                'OrgaoExpedidor' => $UsuarioExternoDTO->getStrOrgaoExpedidorContato(),
                'Telefone' => $UsuarioExternoDTO->getStrTelefoneFixo(),
                'Endereco' => $UsuarioExternoDTO->getStrEnderecoContato(),
                'Bairro' => $contatoDTO->getStrBairro(),
                'SiglaUf' => $contatoDTO->getStrSiglaUf(),
                'NomeCidade' => $contatoDTO->getStrNomeCidade(),
                'Cep' => $contatoDTO->getStrCep(),
                'DataCadastro' => $UsuarioExternoDTO->getDthDataCadastroContato());

            return $ret;

        } catch ( Exception $e ) {
            $this->processarExcecao ( $e );
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

        if ($objTpPoderLegal) {
            foreach ($objTpPoderLegal as $item) {
                $arrPoder[] = (object)array('IdTipoPoderLegal' => $item->getNumIdTipoPoderLegal());
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

                $arrProtocolo[] = (object)array('ProtocoloFormatadoProcesso' => $obRjelProtocoloProtocoloDTO[0]->getStrProtocoloFormatadoProtocolo1());

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
            $infraException->lancarValidacao('O CPF informado nуo tem nenhum Representante formalizado pelo Acesso Externo do SEI.');
        }

    }

    private function obterServico($siglaSistema, $identificacaoServico, $operacaoExigida = null)
    {

        if (empty($siglaSistema) || $siglaSistema == null) {
            throw new InfraException('Sistema nуo informado.');
        }
        if (empty($identificacaoServico) || $identificacaoServico == null) {
            throw new InfraException('Serviчo nуo informado.');
        }

        $objUsuarioDTO = new UsuarioDTO();
        $objUsuarioDTO->retNumIdUsuario();
        $objUsuarioDTO->setStrSigla($siglaSistema);
        $objUsuarioDTO->setStrStaTipo(UsuarioRN::$TU_SISTEMA);

        $objUsuarioRN = new UsuarioRN();
        $objUsuarioDTO = $objUsuarioRN->consultarRN0489($objUsuarioDTO);

        if ($objUsuarioDTO == null) {
            throw new InfraException('Sistema [' . $siglaSistema . '] nуo encontrado.');
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
            throw new InfraException('Serviчo [' . $identificacaoServico . '] do sistema [' . $siglaSistema . '] nуo foi encontrado.');
        } else {
            if ($operacaoExigida) {
                $operacaoServicoDTO = new OperacaoServicoDTO();
                $operacaoServicoRN = new OperacaoServicoRN();
                $operacaoServicoDTO->setNumStaOperacaoServico($operacaoExigida);
                $operacaoServicoDTO->setNumIdServico($objServicoDTO->getNumIdServico());
                $operacaoServicoDTO->retNumIdServico();
                $objOperacaoServicoDTO = $operacaoServicoRN->listar($operacaoServicoDTO);

                if (empty($objOperacaoServicoDTO)) {
                    throw new InfraException('Operaчуo nуo permitida.');
                }
            }
        }

        return $objServicoDTO;

    }
}

$servidorSoap = new SoapServer("wspeticionamento.wsdl", array('encoding' => 'ISO-8859-1'));

$servidorSoap->setClass("PeticionamentoWS");

//Sѓ processa se acessado via POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $servidorSoap->handle();
}

?>