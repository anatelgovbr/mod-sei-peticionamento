<?
/**
 * ANATEL
*
* 28/06/2016 - criado por marcelo.bezerra - CAST
*
*/

require_once dirname(__FILE__).'/../../../SEI.php';

class MdPetAcessoExternoRN extends InfraRN {

	public static $TA_INTERESSADO = 'I';
	public static $TA_USUARIO_EXTERNO = 'E';
	public static $TA_DESTINATARIO_ISOLADO = 'D';
	public static $TA_SISTEMA = 'S';
	public static $TA_ASSINATURA_EXTERNA = 'A';

	//Vars para controle de Tipo de Peticionamento
	public static $MD_PET_PROCESSO_NOVO = '1';
	public static $MD_PET_PROCESSO_INTERCORRENTE = '2';
	public static $MD_PET_INTIMACAO = '3';
	public static $MD_PET_CORRECAO_CANCELAMENTO = '4';

	//Tipo Acesso
	public static $ACESSO_PARCIAL    = 'P';
	public static $ACESSO_INTEGRAL   = 'I';
	public static $NAO_POSSUI_ACESSO = 'N';

	public function __construct()
	{
		parent::__construct();
	}

	protected function inicializarObjInfraIBanco()
	{
		return BancoSEI::getInstance();
	}

	private function validarNumIdAtividade($objAcessoExternoDTO, InfraException $objInfraException)
	{
		if (InfraString::isBolVazia($objAcessoExternoDTO->getNumIdAtividade())) {
			$objInfraException->adicionarValidacao('Atividade não informado.');
		}
	}

	private function validarNumIdParticipante($objAcessoExternoDTO, InfraException $objInfraException)
	{
		if (InfraString::isBolVazia($objAcessoExternoDTO->getNumIdParticipante())) {
			$objInfraException->adicionarValidacao('Interessado não informado.');
		}
	}

	private function validarNumIdUsuarioExterno($objAcessoExternoDTO, InfraException $objInfraException)
	{
		if (InfraString::isBolVazia($objAcessoExternoDTO->getNumIdUsuarioExterno())) {
			$objInfraException->adicionarValidacao('Usuário Externo não informado.');
		}
	}

	private function validarDblIdProtocoloAtividade($objAcessoExternoDTO, InfraException $objInfraException)
	{
		if (InfraString::isBolVazia($objAcessoExternoDTO->getDblIdProtocoloAtividade())) {
			$objInfraException->adicionarValidacao('Processo não informado.');
		}
	}

	private function validarNumIdContatoParticipante($objAcessoExternoDTO, InfraException $objInfraException)
	{
		if (InfraString::isBolVazia($objAcessoExternoDTO->getNumIdContatoParticipante())) {
			$objInfraException->adicionarValidacao('Contato não informado.');
		}
	}


	private function validarDblIdDocumento($objAcessoExternoDTO, InfraException $objInfraException)
	{
		if (InfraString::isBolVazia($objAcessoExternoDTO->getDblIdDocumento())) {
			$objInfraException->adicionarValidacao('Documento não informado.');
		}
	}

	private function validarNumDias($objAcessoExternoDTO, InfraException $objInfraException)
	{
		if (InfraString::isBolVazia($objAcessoExternoDTO->getNumDias())) {
			$objInfraException->adicionarValidacao('Validade do acesso não informada..');
		} else {
			if ($objAcessoExternoDTO->getNumDias() <= 0) {
				$objInfraException->adicionarValidacao('Validade do acesso deve ser de pelo menos um dia.');
			}
		}
	}

	private function validarDtaValidade($objAcessoExternoDTO, InfraException $objInfraException)
	{
		if (InfraString::isBolVazia($objAcessoExternoDTO->getDtaValidade())) {
			$objInfraException->adicionarValidacao('Data de Validade não informada.');
		} else {
			if (!InfraData::validarData($objAcessoExternoDTO->getDtaValidade())) {
				$objInfraException->adicionarValidacao('Data de Validade inválida.');
			}
		}
	}

	private function validarStrEmailUnidade($objAcessoExternoDTO, InfraException $objInfraException)
	{
	}

	private function validarStrEmailDestinatario($objAcessoExternoDTO, InfraException $objInfraException)
	{
		if (!InfraString::isBolVazia($objAcessoExternoDTO->getStrEmailDestinatario())) {
			$objAcessoExternoDTO->setStrEmailDestinatario(trim($objAcessoExternoDTO->getStrEmailDestinatario()));
		}
	}

	private function validarStrHashInterno($objAcessoExternoDTO, InfraException $objInfraException)
	{
		if (InfraString::isBolVazia($objAcessoExternoDTO->getStrHashInterno())) {
			$objInfraException->adicionarValidacao('HASH Interno não informado.');
		} else {
			$objAcessoExternoDTO->setStrHashInterno(trim($objAcessoExternoDTO->getStrHashInterno()));

			if (strlen($objAcessoExternoDTO->getStrHashInterno()) > 32) {
				$objInfraException->adicionarValidacao('HASH Interno possui tamanho superior a 32 caracteres.');
			}
		}
	}

	private function validarStrStaTipo($objAcessoExternoDTO, InfraException $objInfraException)
	{
		if (InfraString::isBolVazia($objAcessoExternoDTO->getStrStaTipo())) {
			$objInfraException->adicionarValidacao('Tipo não informado.');
		} else {
			if (!in_array($objAcessoExternoDTO->getStrStaTipo(), InfraArray::converterArrInfraDTO($this->listarValoresTipoAcessoExterno(), 'StaTipo'))) {
				$objInfraException->adicionarValidacao('Tipo inválido.');
			}
		}
	}

	private function validarStrSenha($objAcessoExternoDTO, InfraException $objInfraException)
	{
		if (InfraString::isBolVazia($objAcessoExternoDTO->getStrSenha())) {
			$objInfraException->adicionarValidacao('Senha não informada.');
		}
	}

	private function validarStrMotivo($objAcessoExternoDTO, InfraException $objInfraException)
	{
		if (InfraString::isBolVazia($objAcessoExternoDTO->getStrMotivo())) {
			$objInfraException->adicionarValidacao('Motivo não informado.');
		}
	}

	private function validarStrSinProcesso($objAcessoExternoDTO, InfraException $objInfraException)
	{

		if (InfraString::isBolVazia($objAcessoExternoDTO->getStrSinProcesso())) {
			$objInfraException->adicionarValidacao('Sinalizador de acesso ao processo não informado.');
		} else {
			if (!InfraUtil::isBolSinalizadorValido($objAcessoExternoDTO->getStrSinProcesso())) {
				$objInfraException->adicionarValidacao('Sinalizador de acesso ao processo inválido.');
			}
		}
	}

	protected function cadastrarAcessoExternoCoreControlado(AcessoExternoDTO $objAcessoExternoDTO)
	{
		try {
				
			//Regras de Negocio
			$objInfraException = new InfraException();

			$this->validarStrStaTipo($objAcessoExternoDTO, $objInfraException);
				
			$objInfraException->lancarValidacoes();

			if ($objAcessoExternoDTO->getStrStaTipo() == self::$TA_INTERESSADO ||
					$objAcessoExternoDTO->getStrStaTipo() == self::$TA_USUARIO_EXTERNO ||
					$objAcessoExternoDTO->getStrStaTipo() == self::$TA_DESTINATARIO_ISOLADO
					) {

						$this->validarStrEmailUnidade($objAcessoExternoDTO, $objInfraException);

						if ($objAcessoExternoDTO->getStrStaTipo() == self::$TA_INTERESSADO) {
							$this->validarNumIdParticipante($objAcessoExternoDTO, $objInfraException);
						} else if ($objAcessoExternoDTO->getStrStaTipo() == self::$TA_USUARIO_EXTERNO) {
							$this->validarNumIdUsuarioExterno($objAcessoExternoDTO, $objInfraException);
							$this->validarDblIdProtocoloAtividade($objAcessoExternoDTO, $objInfraException);
						} else {

							if (InfraString::isBolVazia($objAcessoExternoDTO->getNumIdContatoParticipante())) {
								if (InfraString::isBolVazia($objAcessoExternoDTO->getStrNomeContato())) {
									$objInfraException->adicionarValidacao('Destinatário não informado.');
								} else {
									$objContatoDTO = new ContatoDTO();
									$objContatoDTO->setStrNome($objAcessoExternoDTO->getStrNomeContato());
									$objContatoRN = new ContatoRN();
									$objContatoDTO = $objContatoRN->cadastrarContextoTemporario($objContatoDTO);
									$objAcessoExternoDTO->setNumIdContatoParticipante($objContatoDTO->getNumIdContato());
								}
							}
						}

						$this->validarStrSenha($objAcessoExternoDTO, $objInfraException);
						$this->validarStrMotivo($objAcessoExternoDTO, $objInfraException);
						$this->validarNumDias($objAcessoExternoDTO, $objInfraException);

						$objInfraException->lancarValidacoes();

						$objAcessoExternoDTO->setDblIdDocumento(null);
						$objAcessoExternoDTO->setStrSinProcesso('S');

						$objInfraParametro = new InfraParametro(BancoSEI::getInstance());

						$objInfraSip = new InfraSip(SessaoSEI::getInstance());
						$objInfraSip->autenticar(SessaoSEI::getInstance()->getNumIdOrgaoUsuario(),
								SessaoSEI::getInstance()->getNumIdContextoUsuario(),
								SessaoSEI::getInstance()->getStrSiglaUsuario(),
								$objAcessoExternoDTO->getStrSenha());

						$objAcessoExternoDTO->setDtaValidade(InfraData::calcularData($objAcessoExternoDTO->getNumDias(), InfraData::$UNIDADE_DIAS, InfraData::$SENTIDO_ADIANTE));

						$objParticipanteRN = new ParticipanteRN();

						if ($objAcessoExternoDTO->getStrStaTipo() == self::$TA_USUARIO_EXTERNO) {

							$objUsuarioDTO = new UsuarioDTO();
							$objUsuarioDTO->retNumIdUsuario();
							$objUsuarioDTO->retNumIdContato();
							$objUsuarioDTO->retStrSigla();
							$objUsuarioDTO->retStrNome();
							$objUsuarioDTO->setNumIdUsuario($objAcessoExternoDTO->getNumIdUsuarioExterno());
							$objUsuarioDTO->setStrStaTipo(UsuarioRN::$TU_EXTERNO);

							$objUsuarioRN = new UsuarioRN();
							$objUsuarioDTO = $objUsuarioRN->consultarRN0489($objUsuarioDTO);


							$objParticipanteDTO = new ParticipanteDTO();
							$objParticipanteDTO->retNumIdParticipante();
							$objParticipanteDTO->setDblIdProtocolo($objAcessoExternoDTO->getDblIdProtocoloAtividade());
							$objParticipanteDTO->setNumIdContato($objUsuarioDTO->getNumIdContato());
							$objParticipanteDTO->setStrStaParticipacao(ParticipanteRN::$TP_ACESSO_EXTERNO);

							$objParticipanteDTO = $objParticipanteRN->consultarRN1008($objParticipanteDTO);

							if ($objParticipanteDTO == null) {
								$objParticipanteDTO = new ParticipanteDTO();
								$objParticipanteDTO->setDblIdProtocolo($objAcessoExternoDTO->getDblIdProtocoloAtividade());
								$objParticipanteDTO->setNumIdContato($objUsuarioDTO->getNumIdContato());
								$objParticipanteDTO->setStrStaParticipacao(ParticipanteRN::$TP_ACESSO_EXTERNO);
								$objParticipanteDTO->setNumIdUnidade(SessaoSEI::getInstance()->getNumIdUnidadeAtual());
								$objParticipanteDTO->setNumSequencia(0);
								$objParticipanteDTO = $objParticipanteRN->cadastrarRN0170($objParticipanteDTO);
							}

							$objAcessoExternoDTO->setNumIdParticipante($objParticipanteDTO->getNumIdParticipante());
							$objAcessoExternoDTO->setStrEmailDestinatario($objUsuarioDTO->getStrSigla());

						} else if ($objAcessoExternoDTO->getStrStaTipo() == self::$TA_DESTINATARIO_ISOLADO) {

							$objContatoDTO = new ContatoDTO();
							$objContatoDTO->retNumIdContato();
							$objContatoDTO->retStrNome();
							$objContatoDTO->setNumIdContato($objAcessoExternoDTO->getNumIdContatoParticipante());

							$objContatoRN = new ContatoRN();
							$objContatoDTO = $objContatoRN->consultarRN0324($objContatoDTO);

							$objParticipanteDTO = new ParticipanteDTO();
							$objParticipanteDTO->retNumIdParticipante();
							$objParticipanteDTO->setDblIdProtocolo($objAcessoExternoDTO->getDblIdProtocoloAtividade());
							$objParticipanteDTO->setNumIdContato($objContatoDTO->getNumIdContato());
							$objParticipanteDTO->setStrStaParticipacao(ParticipanteRN::$TP_ACESSO_EXTERNO);

							$objParticipanteDTO = $objParticipanteRN->consultarRN1008($objParticipanteDTO);

							if ($objParticipanteDTO == null) {
								$objParticipanteDTO = new ParticipanteDTO();
								$objParticipanteDTO->setDblIdProtocolo($objAcessoExternoDTO->getDblIdProtocoloAtividade());
								$objParticipanteDTO->setNumIdContato($objContatoDTO->getNumIdContato());
								$objParticipanteDTO->setStrStaParticipacao(ParticipanteRN::$TP_ACESSO_EXTERNO);
								$objParticipanteDTO->setNumIdUnidade(SessaoSEI::getInstance()->getNumIdUnidadeAtual());
								$objParticipanteDTO->setNumSequencia(0);
								$objParticipanteDTO = $objParticipanteRN->cadastrarRN0170($objParticipanteDTO);
							}

							$objAcessoExternoDTO->setNumIdParticipante($objParticipanteDTO->getNumIdParticipante());
						}

						$objParticipanteDTO = new ParticipanteDTO();
						$objParticipanteDTO->retNumIdParticipante();
						$objParticipanteDTO->retDblIdProtocolo();
						$objParticipanteDTO->retStrNomeContato();
						$objParticipanteDTO->setNumIdParticipante($objAcessoExternoDTO->getNumIdParticipante());
						$objParticipanteRN = new ParticipanteRN();
						$objParticipanteDTO = $objParticipanteRN->consultarRN1008($objParticipanteDTO);

						$arrObjAtributoAndamentoDTO = array();
						$objAtributoAndamentoDTO = new AtributoAndamentoDTO();
						$objAtributoAndamentoDTO->setStrNome('DESTINATARIO_NOME');
						$objAtributoAndamentoDTO->setStrValor($objParticipanteDTO->getStrNomeContato());
						$objAtributoAndamentoDTO->setStrIdOrigem($objParticipanteDTO->getNumIdParticipante());
						$arrObjAtributoAndamentoDTO[] = $objAtributoAndamentoDTO;

						$objAtributoAndamentoDTO = new AtributoAndamentoDTO();
						$objAtributoAndamentoDTO->setStrNome('DESTINATARIO_EMAIL');
						$objAtributoAndamentoDTO->setStrValor($objAcessoExternoDTO->getStrEmailDestinatario());
						$objAtributoAndamentoDTO->setStrIdOrigem($objParticipanteDTO->getNumIdParticipante());
						$arrObjAtributoAndamentoDTO[] = $objAtributoAndamentoDTO;

						$objAtributoAndamentoDTO = new AtributoAndamentoDTO();
						$objAtributoAndamentoDTO->setStrNome('MOTIVO');
						$objAtributoAndamentoDTO->setStrValor($objAcessoExternoDTO->getStrMotivo());
						$objAtributoAndamentoDTO->setStrIdOrigem($objAcessoExternoDTO->getNumIdParticipante());
						$arrObjAtributoAndamentoDTO[] = $objAtributoAndamentoDTO;

						$objAtributoAndamentoDTO = new AtributoAndamentoDTO();
						$objAtributoAndamentoDTO->setStrNome('DATA_VALIDADE');
						$objAtributoAndamentoDTO->setStrValor($objAcessoExternoDTO->getDtaValidade());
						$objAtributoAndamentoDTO->setStrIdOrigem(null);
						$arrObjAtributoAndamentoDTO[] = $objAtributoAndamentoDTO;

						$objAtributoAndamentoDTO = new AtributoAndamentoDTO();
						$objAtributoAndamentoDTO->setStrNome('DIAS_VALIDADE');
						$objAtributoAndamentoDTO->setStrValor($objAcessoExternoDTO->getNumDias() . ' ' . ($objAcessoExternoDTO->getNumDias() == 1 ? 'dia' : 'dias'));
						$objAtributoAndamentoDTO->setStrIdOrigem(null);
						$arrObjAtributoAndamentoDTO[] = $objAtributoAndamentoDTO;

						$objAtividadeDTO = new AtividadeDTO();
						$objAtividadeDTO->setDblIdProtocolo($objParticipanteDTO->getDblIdProtocolo());
						$objAtividadeDTO->setNumIdUnidade(SessaoSEI::getInstance()->getNumIdUnidadeAtual());

						$objAtividadeDTO->setNumIdTarefa(TarefaRN::$TI_LIBERACAO_ACESSO_EXTERNO);

						$objAtividadeDTO->setArrObjAtributoAndamentoDTO($arrObjAtributoAndamentoDTO);

						$objAtividadeRN = new AtividadeRN();
						$objAtividadeDTO = $objAtividadeRN->gerarInternaRN0727($objAtividadeDTO);

						$objAcessoExternoDTO->setNumIdAtividade($objAtividadeDTO->getNumIdAtividade());


						$objAcessoExternoDTO->setStrHashInterno(md5(time()));


					} else if ($objAcessoExternoDTO->getStrStaTipo() == self::$TA_ASSINATURA_EXTERNA) {

						$this->validarDblIdDocumento($objAcessoExternoDTO, $objInfraException);
						$this->validarStrSinProcesso($objAcessoExternoDTO, $objInfraException);

						$objInfraException->lancarValidacoes();

						$objAcessoExternoDTO->setDtaValidade(null);
						$objAcessoExternoDTO->setStrMotivo(null);

						//busca processo
						$objDocumentoDTO = new DocumentoDTO();
						$objDocumentoDTO->retDblIdDocumento();
						$objDocumentoDTO->retDblIdProcedimento();
						$objDocumentoDTO->retStrProtocoloProcedimentoFormatado();
						$objDocumentoDTO->retStrProtocoloDocumentoFormatado();
						$objDocumentoDTO->retStrNomeSerie();
						$objDocumentoDTO->setDblIdDocumento($objAcessoExternoDTO->getDblIdDocumento());

						$objDocumentoRN = new DocumentoRN();
						$objDocumentoDTO = $objDocumentoRN->consultarRN0005($objDocumentoDTO);

						//busca contato
						$objUsuarioDTO = new UsuarioDTO();
						$objUsuarioDTO->retNumIdUsuario();
						$objUsuarioDTO->retStrSigla();
						$objUsuarioDTO->retStrNome();
						$objUsuarioDTO->retStrStaTipo();
						$objUsuarioDTO->retNumIdContato();
						$objUsuarioDTO->setNumIdUsuario($objAcessoExternoDTO->getNumIdUsuarioExterno());

						$objUsuarioRN = new UsuarioRN();
						$objUsuarioDTO = $objUsuarioRN->consultarRN0489($objUsuarioDTO);

						if ($objUsuarioDTO->getStrStaTipo() == UsuarioRN::$TU_EXTERNO_PENDENTE) {
							$objInfraException->lancarValidacao('Usuário externo "' . $objUsuarioDTO->getStrSigla() . '" ainda não foi liberado.');
						}

						if ($objUsuarioDTO->getStrStaTipo() != UsuarioRN::$TU_EXTERNO) {
							$objInfraException->lancarValidacao('Usuário "' . $objUsuarioDTO->getStrSigla() . '" não é um usuário externo.');
						}

						//verifica se o contato já é participante do processo
						$objParticipanteDTO = new ParticipanteDTO();

						$objParticipanteDTO->retNumIdParticipante();
						$objParticipanteDTO->retDblIdProtocolo();
						$objParticipanteDTO->retNumIdContato();
						$objParticipanteDTO->retStrStaParticipacao();

						$objParticipanteDTO->setDblIdProtocolo($objDocumentoDTO->getDblIdProcedimento());
						$objParticipanteDTO->setNumIdContato($objUsuarioDTO->getNumIdContato());
						$objParticipanteDTO->setStrStaParticipacao(ParticipanteRN::$TP_ACESSO_EXTERNO);

						$objParticipanteRN = new ParticipanteRN();
						$objParticipanteDTO = $objParticipanteRN->consultarRN1008($objParticipanteDTO);

						if ($objParticipanteDTO == null) {

							$objParticipanteDTO = new ParticipanteDTO();
							$objParticipanteDTO->setDblIdProtocolo($objDocumentoDTO->getDblIdProcedimento());
							$objParticipanteDTO->setNumIdContato($objUsuarioDTO->getNumIdContato());
							$objParticipanteDTO->setNumIdUnidade(SessaoSEI::getInstance()->getNumIdUnidadeAtual());
							$objParticipanteDTO->setStrStaParticipacao(ParticipanteRN::$TP_ACESSO_EXTERNO);
							$objParticipanteDTO->setNumSequencia(0);

							$objParticipanteDTO = $objParticipanteRN->cadastrarRN0170($objParticipanteDTO);
						} else {
							$dto = new AcessoExternoDTO();
							$dto->retStrSiglaContato();
							$dto->retDthAberturaAtividade();
							$dto->setDblIdDocumento($objDocumentoDTO->getDblIdDocumento());
							$dto->setNumIdParticipante($objParticipanteDTO->getNumIdParticipante());
							$dto->setStrStaTipo(AcessoExternoRN::$TA_ASSINATURA_EXTERNA);

							$objAcessoExternoRN = new AcessoExternoRN();
							$dto = $objAcessoExternoRN->consultar($dto);

							if ($dto != null) {
								$objInfraException->lancarValidacao('Usuário externo ' . $dto->getStrSiglaContato() . ' já recebeu liberação para assinatura externa no documento ' . $objDocumentoDTO->getStrProtocoloDocumentoFormatado() . ' em ' . substr($dto->getDthAberturaAtividade(), 0, 16) . '.');
							}
						}

						$objAcessoExternoDTO->setNumIdParticipante($objParticipanteDTO->getNumIdParticipante());

						$arrObjAtributoAndamentoDTO = array();
						$objAtributoAndamentoDTO = new AtributoAndamentoDTO();
						$objAtributoAndamentoDTO->setStrNome('USUARIO_EXTERNO_SIGLA');
						$objAtributoAndamentoDTO->setStrValor($objUsuarioDTO->getStrSigla());
						$objAtributoAndamentoDTO->setStrIdOrigem($objUsuarioDTO->getNumIdUsuario());
						$arrObjAtributoAndamentoDTO[] = $objAtributoAndamentoDTO;

						$objAtributoAndamentoDTO = new AtributoAndamentoDTO();
						$objAtributoAndamentoDTO->setStrNome('USUARIO_EXTERNO_NOME');
						$objAtributoAndamentoDTO->setStrValor($objUsuarioDTO->getStrNome());
						$objAtributoAndamentoDTO->setStrIdOrigem($objUsuarioDTO->getNumIdUsuario());
						$arrObjAtributoAndamentoDTO[] = $objAtributoAndamentoDTO;

						$objAtributoAndamentoDTO = new AtributoAndamentoDTO();
						$objAtributoAndamentoDTO->setStrNome('DOCUMENTO');
						$objAtributoAndamentoDTO->setStrValor($objDocumentoDTO->getStrProtocoloDocumentoFormatado());
						$objAtributoAndamentoDTO->setStrIdOrigem($objDocumentoDTO->getDblIdDocumento());
						$arrObjAtributoAndamentoDTO[] = $objAtributoAndamentoDTO;

						$objAtividadeDTO = new AtividadeDTO();
						$objAtividadeDTO->setDblIdProtocolo($objParticipanteDTO->getDblIdProtocolo());
						$objAtividadeDTO->setNumIdUnidade(SessaoSEI::getInstance()->getNumIdUnidadeAtual());
						$objAtividadeDTO->setNumIdTarefa(TarefaRN::$TI_ACESSO_EXTERNO_SISTEMA);
						$objAtividadeDTO->setArrObjAtributoAndamentoDTO($arrObjAtributoAndamentoDTO);

						$objAtividadeRN = new AtividadeRN();
						$objAtividadeDTO = $objAtividadeRN->gerarInternaRN0727($objAtividadeDTO);

						$objAtividadeRN = new AtividadeRN();
						$objAtividadeDTO = $objAtividadeRN->gerarInternaRN0727($objAtividadeDTO);
						$objAcessoExternoDTO->setNumIdAtividade($objAtividadeDTO->getNumIdAtividade());

						$objAcessoExternoDTO->setStrHashInterno(md5(time()));

					} else if ($objAcessoExternoDTO->getStrStaTipo() == self::$TA_SISTEMA) {

						$this->validarNumIdParticipante($objAcessoExternoDTO, $objInfraException);

						$objInfraException->lancarValidacoes();

						$objAcessoExternoDTO->setDblIdDocumento(null);
						$objAcessoExternoDTO->setStrSinProcesso('S');


						$objAcessoExternoDTO->setStrEmailUnidade(null);
						$objAcessoExternoDTO->setStrEmailDestinatario(null);
						$objAcessoExternoDTO->setDtaValidade(null);

						$objParticipanteDTO = new ParticipanteDTO();
						$objParticipanteDTO->retStrSiglaContato();
						$objParticipanteDTO->retStrNomeContato();
						$objParticipanteDTO->retDblIdProtocolo();
						$objParticipanteDTO->setNumIdParticipante($objAcessoExternoDTO->getNumIdParticipante());

						$objParticipanteRN = new ParticipanteRN();
						$objParticipanteDTO = $objParticipanteRN->consultarRN1008($objParticipanteDTO);

						$arrObjAtributoAndamentoDTO = array();
						$objAtributoAndamentoDTO = new AtributoAndamentoDTO();
						$objAtributoAndamentoDTO->setStrNome('INTERESSADO');
						$objAtributoAndamentoDTO->setStrValor($objParticipanteDTO->getStrSiglaContato() . '¥' . $objParticipanteDTO->getStrNomeContato());
						$objAtributoAndamentoDTO->setStrIdOrigem($objAcessoExternoDTO->getNumIdParticipante());
						$arrObjAtributoAndamentoDTO[] = $objAtributoAndamentoDTO;

						$objAtividadeDTO = new AtividadeDTO();
						$objAtividadeDTO->setDblIdProtocolo($objParticipanteDTO->getDblIdProtocolo());
						$objAtividadeDTO->setNumIdUnidade(SessaoSEI::getInstance()->getNumIdUnidadeAtual());
						$objAtividadeDTO->setNumIdTarefa(TarefaRN::$TI_ACESSO_EXTERNO_SISTEMA);
						$objAtividadeDTO->setArrObjAtributoAndamentoDTO($arrObjAtributoAndamentoDTO);

						$objAtividadeRN = new AtividadeRN();
						$objAtividadeDTO = $objAtividadeRN->gerarInternaRN0727($objAtividadeDTO);

						$objAcessoExternoDTO->setNumIdAtividade($objAtividadeDTO->getNumIdAtividade());

					}

					//gera da mesma forma independente do tipo
					$objAcessoExternoDTO->setStrHashInterno(md5(time()));
					$objAcessoExternoDTO->setStrSinAtivo('S');

					$objAcessoExternoBD = new AcessoExternoBD($this->getObjInfraIBanco());
					$ret = $objAcessoExternoBD->cadastrar($objAcessoExternoDTO);

					//ENVIAR EMAIL
					if ($objAcessoExternoDTO->getStrStaTipo() == self::$TA_INTERESSADO || $objAcessoExternoDTO->getStrStaTipo() == self::$TA_DESTINATARIO_ISOLADO) {

						$objEmailSistemaDTO = new EmailSistemaDTO();
						$objEmailSistemaDTO->retStrDe();
						$objEmailSistemaDTO->retStrPara();
						$objEmailSistemaDTO->retStrAssunto();
						$objEmailSistemaDTO->retStrConteudo();
						$objEmailSistemaDTO->setNumIdEmailSistema(EmailSistemaRN::$ES_DISPONIBILIZACAO_ACESSO_EXTERNO);

						$objEmailSistemaRN = new EmailSistemaRN();
						$objEmailSistemaDTO = $objEmailSistemaRN->consultar($objEmailSistemaDTO);

						if ($objEmailSistemaDTO!=null){

							$objProtocoloDTO = new ProtocoloDTO();
							$objProtocoloDTO->retStrProtocoloFormatado();
							$objProtocoloDTO->setDblIdProtocolo($objParticipanteDTO->getDblIdProtocolo());

							$objProtocoloRN = new ProtocoloRN();
							$objProtocoloDTO = $objProtocoloRN->consultarRN0186($objProtocoloDTO);

							$objUnidadeDTO = new UnidadeDTO();
							$objUnidadeDTO->retStrSigla();
							$objUnidadeDTO->retStrDescricao();
							$objUnidadeDTO->retStrSiglaOrgao();
							$objUnidadeDTO->retStrDescricaoOrgao();
							$objUnidadeDTO->retStrSitioInternetOrgao();
							$objUnidadeDTO->setNumIdUnidade(SessaoSEI::getInstance()->getNumIdUnidadeAtual());

							$objUnidadeRN = new UnidadeRN();
							$objUnidadeDTO = $objUnidadeRN->consultarRN0125($objUnidadeDTO);

							$strDe = $objEmailSistemaDTO->getStrDe();
							$strDe = str_replace('@email_unidade@', $objAcessoExternoDTO->getStrEmailUnidade(), $strDe);

							$strPara = $objEmailSistemaDTO->getStrPara();
							$strPara = str_replace('@email_destinatario@', $objAcessoExternoDTO->getStrEmailDestinatario(), $strPara);

							$strAssunto = $objEmailSistemaDTO->getStrAssunto();
							$strAssunto = str_replace('@processo@', $objProtocoloDTO->getStrProtocoloFormatado(), $strAssunto);

							$strConteudo = $objEmailSistemaDTO->getStrConteudo();
							$strConteudo = str_replace('@processo@', $objProtocoloDTO->getStrProtocoloFormatado(), $strConteudo);
							$strConteudo = str_replace('@nome_destinatario@', $objParticipanteDTO->getStrNomeContato(), $strConteudo);
							$strConteudo = str_replace('@data_validade@', $objAcessoExternoDTO->getDtaValidade(), $strConteudo);
							$strConteudo = str_replace('@link_acesso_externo@', SessaoSEIExterna::getInstance($ret->getNumIdAcessoExterno())->assinarLink(ConfiguracaoSEI::getInstance()->getValor('SEI', 'URL') . '/processo_acesso_externo_consulta.php?id_acesso_externo=' . $ret->getNumIdAcessoExterno()), $strConteudo);
							$strConteudo = str_replace('@sigla_unidade@', $objUnidadeDTO->getStrSigla(), $strConteudo);
							$strConteudo = str_replace('@descricao_unidade@', $objUnidadeDTO->getStrDescricao(), $strConteudo);
							$strConteudo = str_replace('@sigla_orgao@', $objUnidadeDTO->getStrSiglaOrgao(), $strConteudo);
							$strConteudo = str_replace('@descricao_orgao@', $objUnidadeDTO->getStrDescricaoOrgao(), $strConteudo);
							$strConteudo = str_replace('@sitio_internet_orgao@', $objUnidadeDTO->getStrSitioInternetOrgao(), $strConteudo);

						}
					} else if ($objAcessoExternoDTO->getStrStaTipo() == self::$TA_USUARIO_EXTERNO) {

						$objEmailSistemaDTO = new EmailSistemaDTO();
						$objEmailSistemaDTO->retStrDe();
						$objEmailSistemaDTO->retStrPara();
						$objEmailSistemaDTO->retStrAssunto();
						$objEmailSistemaDTO->retStrConteudo();
						$objEmailSistemaDTO->setNumIdEmailSistema(EmailSistemaRN::$ES_DISPONIBILIZACAO_ACESSO_EXTERNO_USUARIO_EXTERNO);

						$objEmailSistemaRN = new EmailSistemaRN();
						$objEmailSistemaDTO = $objEmailSistemaRN->consultar($objEmailSistemaDTO);

						if ($objEmailSistemaDTO!=null){
							$objProtocoloDTO = new ProtocoloDTO();
							$objProtocoloDTO->retStrProtocoloFormatado();
							$objProtocoloDTO->setDblIdProtocolo($objParticipanteDTO->getDblIdProtocolo());

							$objProtocoloRN = new ProtocoloRN();
							$objProtocoloDTO = $objProtocoloRN->consultarRN0186($objProtocoloDTO);

							$objUnidadeDTO = new UnidadeDTO();
							$objUnidadeDTO->retNumIdOrgao();
							$objUnidadeDTO->retStrSigla();
							$objUnidadeDTO->retStrDescricao();
							$objUnidadeDTO->retStrSiglaOrgao();
							$objUnidadeDTO->retStrDescricaoOrgao();
							$objUnidadeDTO->retStrSitioInternetOrgao();
							$objUnidadeDTO->setNumIdUnidade(SessaoSEI::getInstance()->getNumIdUnidadeAtual());

							$objUnidadeRN = new UnidadeRN();
							$objUnidadeDTO = $objUnidadeRN->consultarRN0125($objUnidadeDTO);

							$strDe = $objEmailSistemaDTO->getStrDe();
							$strDe = str_replace('@email_unidade@', $objAcessoExternoDTO->getStrEmailUnidade(), $strDe);

							$strPara = $objEmailSistemaDTO->getStrPara();
							$strPara = str_replace('@email_usuario_externo@', $objUsuarioDTO->getStrSigla(), $strPara);

							$strAssunto = $objEmailSistemaDTO->getStrAssunto();
							$strAssunto = str_replace('@processo@', $objProtocoloDTO->getStrProtocoloFormatado(), $strAssunto);

							$strConteudo = $objEmailSistemaDTO->getStrConteudo();
							$strConteudo = str_replace('@processo@', $objProtocoloDTO->getStrProtocoloFormatado(), $strConteudo);
							$strConteudo = str_replace('@nome_usuario_externo@', $objUsuarioDTO->getStrNome(), $strConteudo);
							$strConteudo = str_replace('@email_usuario_externo@', $objUsuarioDTO->getStrSigla(), $strConteudo);
							$strConteudo = str_replace('@link_login_usuario_externo@', ConfiguracaoSEI::getInstance()->getValor('SEI', 'URL') . '/controlador_externo.php?acao=usuario_externo_logar&id_orgao_acesso_externo=' . $objUnidadeDTO->getNumIdOrgao(), $strConteudo);

							$strConteudo = str_replace('@sigla_unidade@', $objUnidadeDTO->getStrSigla(), $strConteudo);
							$strConteudo = str_replace('@descricao_unidade@', $objUnidadeDTO->getStrDescricao(), $strConteudo);
							$strConteudo = str_replace('@sigla_orgao@', $objUnidadeDTO->getStrSiglaOrgao(), $strConteudo);
							$strConteudo = str_replace('@descricao_orgao@', $objUnidadeDTO->getStrDescricaoOrgao(), $strConteudo);
							$strConteudo = str_replace('@sitio_internet_orgao@', $objUnidadeDTO->getStrSitioInternetOrgao(), $strConteudo);

						}
					} else if ($objAcessoExternoDTO->getStrStaTipo() == self::$TA_ASSINATURA_EXTERNA) {

						$objEmailSistemaDTO = new EmailSistemaDTO();
						$objEmailSistemaDTO->retStrDe();
						$objEmailSistemaDTO->retStrPara();
						$objEmailSistemaDTO->retStrAssunto();
						$objEmailSistemaDTO->retStrConteudo();
						$objEmailSistemaDTO->setNumIdEmailSistema(EmailSistemaRN::$ES_DISPONIBILIZACAO_ASSINATURA_EXTERNA_USUARIO_EXTERNO);

						$objEmailSistemaRN = new EmailSistemaRN();
						$objEmailSistemaDTO = $objEmailSistemaRN->consultar($objEmailSistemaDTO);

						if ($objEmailSistemaDTO!=null){

							$objUnidadeDTO = new UnidadeDTO();
							$objUnidadeDTO->retNumIdOrgao();
							$objUnidadeDTO->retStrSigla();
							$objUnidadeDTO->retStrDescricao();
							$objUnidadeDTO->retStrSiglaOrgao();
							$objUnidadeDTO->retStrDescricaoOrgao();
								
							//alteracoes seiv3
							$objUnidadeDTO->retStrSitioInternetOrgaoContato();

							$objUnidadeDTO->setNumIdUnidade(SessaoSEI::getInstance()->getNumIdUnidadeAtual());

							$objUnidadeRN = new UnidadeRN();
							$objUnidadeDTO = $objUnidadeRN->consultarRN0125($objUnidadeDTO);

							$strDe = $objEmailSistemaDTO->getStrDe();
							$strDe = str_replace('@email_unidade@', $objAcessoExternoDTO->getStrEmailUnidade(), $strDe);

							$strPara = $objEmailSistemaDTO->getStrPara();
							$strPara = str_replace('@email_usuario_externo@', $objUsuarioDTO->getStrSigla(), $strPara);

							$strAssunto = $objEmailSistemaDTO->getStrAssunto();
							$strAssunto = str_replace('@processo@', $objDocumentoDTO->getStrProtocoloProcedimentoFormatado(), $strAssunto);

							$strConteudo = $objEmailSistemaDTO->getStrConteudo();
							$strConteudo = str_replace('@processo@', $objDocumentoDTO->getStrProtocoloProcedimentoFormatado(), $strConteudo);
							$strConteudo = str_replace('@documento@', $objDocumentoDTO->getStrProtocoloDocumentoFormatado(), $strConteudo);
							$strConteudo = str_replace('@tipo_documento@', $objDocumentoDTO->getStrNomeSerie(), $strConteudo);
							$strConteudo = str_replace('@nome_usuario_externo@', $objUsuarioDTO->getStrNome(), $strConteudo);
							$strConteudo = str_replace('@email_usuario_externo@', $objUsuarioDTO->getStrSigla(), $strConteudo);
							$strConteudo = str_replace('@link_login_usuario_externo@', ConfiguracaoSEI::getInstance()->getValor('SEI', 'URL') . '/controlador_externo.php?acao=usuario_externo_logar&id_orgao_acesso_externo=' . $objUnidadeDTO->getNumIdOrgao(), $strConteudo);
							$strConteudo = str_replace('@sigla_unidade@', $objUnidadeDTO->getStrSigla(), $strConteudo);
							$strConteudo = str_replace('@descricao_unidade@', $objUnidadeDTO->getStrDescricao(), $strConteudo);
							$strConteudo = str_replace('@sigla_orgao@', $objUnidadeDTO->getStrSiglaOrgao(), $strConteudo);
							$strConteudo = str_replace('@descricao_orgao@', $objUnidadeDTO->getStrDescricaoOrgao(), $strConteudo);
							$strConteudo = str_replace('@sitio_internet_orgao@', $objUnidadeDTO->getStrSitioInternetOrgaoContato(), $strConteudo);

						}
					}

					return $ret;

					//Auditoria

		} catch (Exception $e) {
			throw new InfraException('Erro cadastrando Acesso Externo.', $e);
		}
	}

	public function listarValoresTipoAcessoExterno(){
		try {

			$arrObjTipoDTO = array();

			$objTipoDTO = new TipoDTO();
			$objTipoDTO->setStrStaTipo(self::$TA_INTERESSADO);
			$objTipoDTO->setStrDescricao('Interessado do Processo');
			$arrObjTipoDTO[] = $objTipoDTO;

			$objTipoDTO = new TipoDTO();
			$objTipoDTO->setStrStaTipo(self::$TA_USUARIO_EXTERNO);
			$objTipoDTO->setStrDescricao('Usuário Externo');
			$arrObjTipoDTO[] = $objTipoDTO;

			$objTipoDTO = new TipoDTO();
			$objTipoDTO->setStrStaTipo(self::$TA_DESTINATARIO_ISOLADO);
			$objTipoDTO->setStrDescricao('Destinatário Isolado');
			$arrObjTipoDTO[] = $objTipoDTO;

			$objTipoDTO = new TipoDTO();
			$objTipoDTO->setStrStaTipo(self::$TA_SISTEMA);
			$objTipoDTO->setStrDescricao('Sistema');
			$arrObjTipoDTO[] = $objTipoDTO;

			$objTipoDTO = new TipoDTO();
			$objTipoDTO->setStrStaTipo(self::$TA_ASSINATURA_EXTERNA);
			$objTipoDTO->setStrDescricao('Assinatura Externa de Documento');
			$arrObjTipoDTO[] = $objTipoDTO;

			return $arrObjTipoDTO;

		} catch (Exception $e) {
			throw new InfraException('Erro listando valores de Tipo de Acesso Externo.', $e);
		}
		
	}
	
	protected function consultarConectado(MdPetAcessoExternoDTO $objAcessoExternoDTO)
	{
		try {
	
			//Valida Permissao
			SessaoSEI::getInstance()->validarAuditarPermissao('acesso_externo_consultar', __METHOD__, $objAcessoExternoDTO);
	
			//Regras de Negocio
			//$objInfraException = new InfraException();
	
			//$objInfraException->lancarValidacoes();
	
			$objAcessoExternoBD = new AcessoExternoBD($this->getObjInfraIBanco());
			$ret = $objAcessoExternoBD->consultar($objAcessoExternoDTO);
	
			//Auditoria
	
			return $ret;
		} catch (Exception $e) {
			throw new InfraException('Erro consultando Acesso Externo.', $e);
		}
	}
	
	protected function listarConectado(MdPetAcessoExternoDTO $objAcessoExternoDTO)
	{
		try {
	
			//Valida Permissao
			SessaoSEI::getInstance()->validarAuditarPermissao('acesso_externo_listar', __METHOD__, $objAcessoExternoDTO);
	
			//Regras de Negocio
			//$objInfraException = new InfraException();
	
			//$objInfraException->lancarValidacoes();
	
			$objAcessoExternoBD = new AcessoExternoBD($this->getObjInfraIBanco());
			$ret = $objAcessoExternoBD->listar($objAcessoExternoDTO);

			//Auditoria
	
			return $ret;
	
		} catch (Exception $e) {
			throw new InfraException('Erro listando Acessos Externos.', $e);
		}
	}
	
	protected function contarConectado(MdPetAcessoExternoDTO $objAcessoExternoDTO)
	{
		try {
	
			//Valida Permissao
			SessaoSEI::getInstance()->validarAuditarPermissao('acesso_externo_listar', __METHOD__, $objAcessoExternoDTO);
	
			//Regras de Negocio
			//$objInfraException = new InfraException();
	
			//$objInfraException->lancarValidacoes();
	
			$objAcessoExternoBD = new AcessoExternoBD($this->getObjInfraIBanco());
			$ret = $objAcessoExternoBD->contar($objAcessoExternoDTO);
	
			//Auditoria
	
			return $ret;
		} catch (Exception $e) {
			throw new InfraException('Erro contando Acessos Externos.', $e);
		}
	}

	protected function cadastrarControlado(MdPetAcessoExternoDTO $objAcessoExternoDTO)
	{
		try {

			//Valida Permissao
			SessaoSEI::getInstance()->validarAuditarPermissao('acesso_externo_cadastrar', __METHOD__, $objAcessoExternoDTO);

			//Regras de Negocio
			//$objInfraException = new InfraException();

			//$objInfraException->lancarValidacoes();

			$objAcessoExternoBD = new AcessoExternoBD($this->getObjInfraIBanco());
			$ret = $objAcessoExternoBD->cadastrar($objAcessoExternoDTO);

			//Auditoria

			return $ret;
		} catch (Exception $e) {
			throw new InfraException('Erro cadastrando Acessos Externos.', $e);
		}
	}

	protected function alterarControlado(MdPetAcessoExternoDTO $objAcessoExternoDTO)
	{
		try {

			//Valida Permissao
			SessaoSEI::getInstance()->validarAuditarPermissao('acesso_externo_cadastrar', __METHOD__, $objAcessoExternoDTO);

			//Regras de Negocio
			//$objInfraException = new InfraException();

			//$objInfraException->lancarValidacoes();

			$objAcessoExternoBD = new AcessoExternoBD($this->getObjInfraIBanco());
			$ret = $objAcessoExternoBD->alterar($objAcessoExternoDTO);

			//Auditoria

			return $ret;
		} catch (Exception $e) {
			throw new InfraException('Erro cadastrando Acessos Externos.', $e);
		}
	}

	public function atualizarIdAcessoExternoModulo($idAcessoExt, $tipo){
		$objMdPetAcessoExternoDTO = new MdPetAcessoExternoDTO();
		$objMdPetAcessoExternoDTO->setNumIdAcessoExterno($idAcessoExt);
		$objDTO         = null;
		$existeCadastro = $this->contar($objMdPetAcessoExternoDTO) > 0;

		switch ($tipo){
			case static::$MD_PET_PROCESSO_NOVO:
				$objMdPetAcessoExternoDTO->setStrSinProcessoNovo('S');

				if(!$existeCadastro)
				{
					$objMdPetAcessoExternoDTO->setStrSinProcessoIntercorrente('N');
					$objMdPetAcessoExternoDTO->setStrSinIntimacao('N');
				}

				break;

			case static::$MD_PET_PROCESSO_INTERCORRENTE:
				$objMdPetAcessoExternoDTO->setStrSinProcessoIntercorrente('S');

				if(!$existeCadastro)
				{
					$objMdPetAcessoExternoDTO->setStrSinProcessoNovo('N');
					$objMdPetAcessoExternoDTO->setStrSinIntimacao('N');
				}

				break;

			case static::$MD_PET_INTIMACAO:
				$objMdPetAcessoExternoDTO->setStrSinIntimacao('S');

				if(!$existeCadastro)
				{
					$objMdPetAcessoExternoDTO->setStrSinProcessoNovo('N');
					$objMdPetAcessoExternoDTO->setStrSinProcessoIntercorrente('N');
				}

				break;
		}

		$objMdPetAcessoExternoDTO->setStrSinAtivo('S');

		if($existeCadastro){
		  $objDTO = $this->alterar($objMdPetAcessoExternoDTO);
		}else{
		  $objDTO = $this->cadastrar($objMdPetAcessoExternoDTO);
		}
		
		return $objDTO;
	}


	private function _getDadosContato($idContato)
	{

		$objContatoDTO = new ContatoDTO();
		$objContatoDTO->retNumIdUsuarioCadastro();
		$objContatoDTO->setNumIdContato($idContato);
		$objContatoDTO->retStrEmail();
		$objContatoDTO->retStrNome();
		$objContatoDTO->retNumIdContato();
		$objContatoDTO->setNumMaxRegistrosRetorno(1);
		$objContatoRN = new ContatoRN();

		$count = $objContatoRN->contarRN0327($objContatoDTO);

		if($count > 0){

			$objContatoDTO = $objContatoRN->consultarRN0324($objContatoDTO);
			$idUsuario     = $this->_getIdUsuarioPorIdContato($idContato);


			return array($objContatoDTO->getNumIdContato(),	$idUsuario, $objContatoDTO->getStrNome(), $objContatoDTO->getStrEmail());
		}

		return null;
	}

	private function _getIdUsuarioPorIdContato($idContato){
		$objRN = new UsuarioRN();
		$objUsuarioDTO = new UsuarioDTO();
		$objUsuarioDTO->setNumIdContato($idContato);
		$objUsuarioDTO->retNumIdUsuario();
		$objUsuarioDTO->setNumMaxRegistrosRetorno(1);

		$objUsuarioDTO = $objRN->consultarRN0489($objUsuarioDTO);
		$idUsuario = !is_null($objUsuarioDTO) ? $objUsuarioDTO->getNumIdUsuario() : null;

		return $idUsuario;
	}


	public function adicionarParticipanteProcessoAcessoExterno($arr){
		$idProced      = $arr[0];
		$idUnidade     = $arr[1];
		$idContato     = $arr[2];

		$objParticipanteDTO = new ParticipanteDTO();
		$objParticipanteDTO->setNumIdContato($idContato);
		$objParticipanteDTO->setDblIdProtocolo($idProced);
		$objParticipanteDTO->setStrStaParticipacao(ParticipanteRN::$TP_ACESSO_EXTERNO);
		$objParticipanteDTO->retNumIdParticipante();

		$objParticipanteRN  = new ParticipanteRN();
		$count = $objParticipanteRN->contarRN0461($objParticipanteDTO);

		if($count > 0){
			return $objParticipanteRN->consultarRN1008($objParticipanteDTO);
		}else{
			$objParticipanteDTO->setNumIdUnidade($idUnidade);
			$objParticipanteDTO->setNumSequencia(0);

			$objParticipanteRN  = new ParticipanteRN();
			$objParticipanteDTO = $objParticipanteRN->cadastrarRN0170($objParticipanteDTO);
			return $objParticipanteDTO;
		}

		return null;
	}

	private function _getIdParticipantePorContato($idContato, $idProcesso, $cadastrarNovo = true){
		$objParticipanteRN = new ParticipanteRN();

		$objParticipanteDTO = new ParticipanteDTO();
		$objParticipanteDTO->setNumIdContato($idContato);
		$objParticipanteDTO->setDblIdProtocolo($idProcesso);
		$objParticipanteDTO->setStrStaParticipacao(ParticipanteRN::$TP_ACESSO_EXTERNO);
		$objParticipanteDTO->retNumIdParticipante();
		$objParticipanteDTO = $objParticipanteRN->consultarRN1008($objParticipanteDTO);

		$idParticipante = !is_null($objParticipanteDTO) ? $objParticipanteDTO->getNumIdParticipante() : null;

		if(is_null($idParticipante) && $cadastrarNovo){
			$idUnidade          = SessaoSEI::getInstance()->getNumIdUnidadeAtual();
			$objParticipanteDTO = $this->adicionarParticipanteProcessoAcessoExterno(array($idProcesso, $idUnidade, $idContato));

			if(!is_null($objParticipanteDTO)){
				$idParticipante = $objParticipanteDTO->getNumIdParticipante();
			}
		}

		return $idParticipante;
	}

	private function _preencherArrDocDisponibilizadosIntimacao(){
		$isTpConcessao = array_key_exists('optParcial', $_POST)? static::$ACESSO_PARCIAL : static::$ACESSO_INTEGRAL;

		$arrAnexos   = $_POST['hdnIdsDocAnexo'] != '' ? json_decode($_POST['hdnIdsDocAnexo']) : array();
		$arrProtDisp = $_POST['hdnIdsDocDisponivel'] != '' ? json_decode($_POST['hdnIdsDocDisponivel']) : array();

		$arrDoc = ($isTpConcessao == static::$ACESSO_PARCIAL) ? array_merge($arrAnexos, $arrProtDisp) : $arrAnexos;

		array_push($arrDoc, $_POST['hdnIdDocumento']);


		return $arrDoc;
	}



	private function _preencherArrDocDisponibilizadosProcessoNovo($idProcedimento){

		$arrRetorno = array();
		if (!is_null($idProcedimento))
		{
			$objDocumentoRN = new DocumentoRN();
			$objDocumentoDTO = new DocumentoDTO();
			$objDocumentoDTO->setDblIdProcedimento($idProcedimento);
			$objDocumentoDTO->retDblIdDocumento();

			$arrDTO = $objDocumentoRN->listarRN0008($objDocumentoDTO);

			$arrRetorno = count($arrDTO) > 0 ? InfraArray::converterArrInfraDTO($arrDTO, 'IdDocumento') : array();
		}


		return $arrRetorno;
	}


	private function _preencherArrDocDisponibilizados($tipoPeticionamento, $idProcedimento = null, $arrIdsDoc = null)
	{
   	  $arrRetorno = array();

		switch ($tipoPeticionamento)
		{
			case static::$MD_PET_INTIMACAO:
				$arrRetorno =  $this->_preencherArrDocDisponibilizadosIntimacao();
				break;

			case static::$MD_PET_PROCESSO_NOVO:
				$arrRetorno =  $this->_preencherArrDocDisponibilizadosProcessoNovo($idProcedimento);
				break;

			case static::$MD_PET_PROCESSO_INTERCORRENTE || static::$MD_PET_CORRECAO_CANCELAMENTO:
				return $arrIdsDoc;
				break;


		}

	  return $arrRetorno;
	}



	private function _cadastrarAcessoExterno($idProcesso, $idContato, $tipoPeticionamento, $tpAcessoSolicitado, $nomeDoc = null, $arrIdsDoc = null)
		{
			$motivoAcessoEx       = $this->_getMotivoAcessoExterno($tipoPeticionamento, $nomeDoc);
			$dadosContato    	  = $this->_getDadosContato($idContato);
			$idParticipante  	  = $this->_getIdParticipantePorContato($idContato, $idProcesso, true);
			$arrDocAcessoExt 	  = $this->_preencherArrDocDisponibilizados($tipoPeticionamento, $idProcesso, $arrIdsDoc);
			$idTpConcessao  	  = $tpAcessoSolicitado == static::$ACESSO_INTEGRAL ? 'S' : 'N';

			$objMdPetIntAcExDocRN 		= new MdPetIntAcessoExternoDocumentoRN();
			$objMdPetIntAcessoExtDocDTO = new MdPetIntAcessoExternoDocumentoDTO();
			$objMdPetIntAcessoExtDocDTO->setNumIdUsuarioExterno($dadosContato[1]);
			$objMdPetIntAcessoExtDocDTO->setNumIdUnidade(SessaoSEI::getInstance()->getNumIdUnidadeAtual());
			$objMdPetIntAcessoExtDocDTO->setNumIdParticipante($idParticipante);
			$objMdPetIntAcessoExtDocDTO->setDblIdProtocoloProcesso($idProcesso);
			$objMdPetIntAcessoExtDocDTO->setArrIdDocumentos($arrDocAcessoExt);
			$objMdPetIntAcessoExtDocDTO->setStrNomeUsuarioExterno($dadosContato[2]);
			$objMdPetIntAcessoExtDocDTO->setStrEmailUsuarioExterno($dadosContato[3]);
			$objMdPetIntAcessoExtDocDTO->setStrStaConcessao(MdPetIntAcessoExternoDocumentoRN::$STA_INTERNO);
			$objMdPetIntAcessoExtDocDTO->setStrSinVisualizacaoIntegral($idTpConcessao);
			$objMdPetIntAcessoExtDocDTO->setStrMotivo($motivoAcessoEx);

			$retorno = $objMdPetIntAcExDocRN->concederAcessoExternoParaDocumentos($objMdPetIntAcessoExtDocDTO);;

			return $retorno;
	}

	protected function getUltimaConcAcessoExtModuloPorContatosConectado($arr){
		$arrDadosUsuarios = count($arr) > 0 ? current($arr) : null;
		$idProcedimento   = array_key_exists('1', $arr) ? $arr[1] : null;
		$arrIdsContato    = $this->_getArrIdsContato($arrDadosUsuarios);

		$arrRel           = array();

		$arrObjs = $this->_retornaArrAcessoExtPorProcedimentoPorContato($arrIdsContato, $idProcedimento, false, false);

		if (count($arrObjs) > 0) {
			$arrRel = $this->_geTipoAcessoExternoPorContatos($arrObjs, $arrIdsContato);

		}else{
			foreach($arrIdsContato as $idContato){
				$arrRetorno[$idContato] = static::$NAO_POSSUI_ACESSO;
			}
		}

		return  $arrRel;
	}


	private function _getArrIdsContato($arrDadosUsuarios){
		$arrIdsUsuario = array();

		if(count($arrDadosUsuarios)> 0) {
			foreach ($arrDadosUsuarios as $arrUsuario) {
				array_push($arrIdsUsuario, $arrUsuario[0]);
			}
		}

		return $arrIdsUsuario;
	}



	private function _getUltimaConcessaoAcessoExternoModulo($idProcedimento, $idContato, $returnId = false){
		$objMdPetAcessoExternoDTO = new MdPetAcessoExternoDTO();
		$objMdPetAcessoExternoDTO->setDblIdProtocoloAcessoExterno($idProcedimento);
		$objMdPetAcessoExternoDTO->setNumIdContatoAcessoExterno($idContato);
		$objMdPetAcessoExternoDTO->setStrSinAtivo('S');
		$objMdPetAcessoExternoDTO->setNumMaxRegistrosRetorno(1);
		$objMdPetAcessoExternoDTO->retTodos(true);

		$countDTO = $this->contar($objMdPetAcessoExternoDTO);

		if($countDTO == 0)
		{
			if($returnId) {
				return 0;
			}else{
				return MdPetIntAcessoExternoDocumentoRN::$NAO_POSSUI_ACESSO;
			}
		}

		$objDTO = current($this->listar($objMdPetAcessoExternoDTO));
		
		$idAcessoExterno = $objDTO->getNumIdAcessoExterno();

		 if($idAcessoExterno != ''){
			 if($returnId) {
				 return $idAcessoExterno;
			 }else{
				 return $this->getTipoConcessaoAcesso($idAcessoExterno);
			 }
		 }

		return 0;
	}

	public function getTipoConcessaoAcesso($idAcessoExt){
		$objRelProtAcessoExtRN  = new RelAcessoExtProtocoloRN();
		$objRelProtAcessoExtDTO = new RelAcessoExtProtocoloDTO();
		$objRelProtAcessoExtDTO->setNumIdAcessoExterno($idAcessoExt);
		$tpConcessao = $objRelProtAcessoExtRN->contar($objRelProtAcessoExtDTO) > 0 ? static::$ACESSO_PARCIAL : static::$ACESSO_INTEGRAL ;

		return $tpConcessao;
	}

	/*
	 * Função construída com o intuíto de evitar várias consultas.
	 */
	private function _geTipoAcessoExternoPorContatos($arrObjs, $arrIdsContato)
	{
		$arrAcessoExtTp    = array();
		$arrRetorno        = array();
		$idsAcessoExterno  = InfraArray::converterArrInfraDTO($arrObjs, 'IdAcessoExterno');
		$idsAcessoExterno  = count($idsAcessoExterno) > 0 ? array_unique($idsAcessoExterno) : array();

		$arrIdAcExtContato = $this->_retornaArrIdAcessoExternoContato($arrObjs);

		if (count($idsAcessoExterno) > 0) {
			$arrAcessoExtTp = $this->getTipoAcessoExternoPorAcessoExterno($idsAcessoExterno);

			foreach ($arrIdsContato as $idContato) {
				$idAcessoExterno = $arrIdAcExtContato[$idContato];
				$arrRetorno[$idContato] = array_key_exists($idAcessoExterno, $arrAcessoExtTp) ? $arrAcessoExtTp[$idAcessoExterno] : static::$NAO_POSSUI_ACESSO;
			}

			return $arrRetorno;
		}

		return null;
	}

	private function _retornaArrIdAcessoExternoContato($arrObjs){
		$arrRet = array();
		foreach($arrObjs as $obj){
			$arrRet[$obj->getNumIdContatoAcessoExterno()] = 	$obj->getNumIdAcessoExterno();
		}

		return $arrRet;
	}

	public function getTipoAcessoExternoPorAcessoExterno($idsAcessoExterno)
	{

		if (count($idsAcessoExterno) > 0) {
			$objRelAcessoExtProtocoloRN = new RelAcessoExtProtocoloRN();

			$objRelAcessoExtProtocoloDTO = new RelAcessoExtProtocoloDTO();
			$objRelAcessoExtProtocoloDTO->setNumIdAcessoExterno($idsAcessoExterno, InfraDTO::$OPER_IN);
			$objRelAcessoExtProtocoloDTO->retDblIdProtocolo();
			$objRelAcessoExtProtocoloDTO->retNumIdAcessoExterno();

			$arr = $objRelAcessoExtProtocoloRN->listar($objRelAcessoExtProtocoloDTO);

			if (count($arr) > 0) {
				foreach ($arr as $objRel) {
					$arrAcessoExtTp[$objRel->getNumIdAcessoExterno()] = static::$ACESSO_PARCIAL;
				}
			}

			foreach ($idsAcessoExterno as $idAcessoExterno) {
				if (count($arrAcessoExtTp) == 0 || !(array_key_exists($idAcessoExterno, $arrAcessoExtTp))) {
					$arrAcessoExtTp[$idAcessoExterno] = static::$ACESSO_INTEGRAL;
				}
			}

			return $arrAcessoExtTp;
		}
		return array();
	}


	private function _getMotivoAcessoExterno($tipoPeticionamento, $nomeDoc){
		$strMotivo = '';
		switch ($tipoPeticionamento){
			case static::$MD_PET_INTIMACAO:
				$strMotivo = 'Criado automaticamente por meio do módulo Peticionamento e Intimação Eletrônicos em razão de Intimação Eletrônica gerada.';
				break;

			case static::$MD_PET_PROCESSO_NOVO:
				$strMotivo = 'Criado automaticamente por meio do módulo Peticionamento e Intimação Eletrônicos em razão de Peticionamento Eletrônico realizado.';
				break;

			case static::$MD_PET_PROCESSO_INTERCORRENTE:
				$strMotivo = 'Criado automaticamente por meio do módulo Peticionamento e Intimação Eletrônicos em razão de Peticionamento Eletrônico realizado.';
				break;

			case static::$MD_PET_CORRECAO_CANCELAMENTO:
				$strMotivo = 'Criado automaticamente por meio do módulo Peticionamento e Intimação Eletrônicos devido a cancelamento de acesso integral para correta manutenção dos acessos externos necessários.';
				break;
		}

		return $strMotivo;
	}

	private function _retornaIdContatoUsuarioExterno()
	{
		$idUsuario         = SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno();
		$objMdPetUsuarioRN = new MdPetIntUsuarioRN();
		$objContatoDTO = $objMdPetUsuarioRN->retornaObjContatoPorIdUsuario(array($idUsuario));

		if(!is_null($objContatoDTO))
		{
			return $objContatoDTO->getNumIdContato();
		}

		return null;
	}


	public function aplicarRegrasGeraisAcessoExterno($idProcedimento, $tipoPeticionamento, $idContato = null, $tpAcessoSolicitado = null, $nomeDoc = null, $arrIdsDocIntercorrente = null)
	{
		$idAcessoExternoMain = null;

		if (is_null($idContato)) {
			$idContato = $this->_retornaIdContatoUsuarioExterno();
		}

		if (!is_null($idContato)) 
		{
			$arrParams = array($idProcedimento, $idContato);
			$tpAcessoAnterior = $this->_getUltimaConcessaoAcessoExternoModulo($idProcedimento, $idContato);

			$tpAcessoSolicitado = is_null($tpAcessoSolicitado) ? static::$ACESSO_PARCIAL : $tpAcessoSolicitado;

			//Se não existe acesso externo anterior criado, gerar um novo
			if ($tpAcessoAnterior == MdPetIntAcessoExternoDocumentoRN::$NAO_POSSUI_ACESSO) 
			{
				$arrDTO = $this->_cadastrarAcessoExterno($idProcedimento, $idContato, $tipoPeticionamento, $tpAcessoSolicitado, $nomeDoc, $arrIdsDocIntercorrente);
				$objAcessoExtDTO = count($arrDTO) > 0 ? current($arrDTO) : null;
				$idAcessoExt = $objAcessoExtDTO->getNumIdAcessoExterno();

				$idAcessoExternoMain =  $idAcessoExt;
			}

			//Se existir acesso Integral
			if ($tpAcessoAnterior == static::$ACESSO_INTEGRAL) 
			{
				//Se o solicitado for integral
				if ($tpAcessoSolicitado == static::$ACESSO_INTEGRAL) 
				{
					$arrObjs = $this->_retornaArrAcessoExtPorProcedimentoPorContato(array($idContato), $idProcedimento);
					$objDTO = count($arrObjs) > 0 ? current($arrObjs) : false;
					$idAcessoExt = $objDTO ? $objDTO->getNumIdAcessoExterno() : false;

					$idAcessoExternoMain = $idAcessoExt;
				}
			}

			//Se existir acesso Parcial
			if ($tpAcessoAnterior == static::$ACESSO_PARCIAL) 
			{
				//Se o solicitado for Integral
				if ($tpAcessoSolicitado == static::$ACESSO_INTEGRAL) 
				{
					$idAcessoExt = $this->_realizarProcessosCancelamentoCriacaoAcExterno($arrParams, $nomeDoc, $tpAcessoSolicitado, $tipoPeticionamento);
					$idAcessoExternoMain =  $idAcessoExt;
				}

				//Se o solicitado for Parcial
				if ($tpAcessoSolicitado == static::$ACESSO_PARCIAL) 
				{
					$idAcessoExt = $this->_realizarProcessosAdequacaoProcessoExternoParcial($idProcedimento, $idContato, $tipoPeticionamento, $arrIdsDocIntercorrente);
					$idAcessoExternoMain = $idAcessoExt;
				}
			}
		}

		if(!is_null($idAcessoExternoMain)){
			$this->atualizarIdAcessoExternoModulo($idAcessoExternoMain, $tipoPeticionamento);
			return $idAcessoExternoMain;
		}

		return null;
	}


	private function _cancelarAcessosExternosExistentes($arrParams, $nomeDocCompleto)
	{
		$objInfraParametro   = new InfraParametro(BancoSEI::getInstance());
		$idUsuarioModulo     = $objInfraParametro->getValor(MdPetContatoRN::$STR_INFRA_PARAMETRO_SIGLA_CONTATO, false);
		$idUsuarioLogado     = SessaoSEI::getInstance()->getNumIdUsuario();
		$idUnidadeLogada     = SessaoSEI::getInstance()->getNumIdUnidadeAtual();
		$idContato    		 = $arrParams[1];
		$idProcesso   		 = $arrParams[0];
		$objAcessoExternoRN  = new AcessoExternoRN();
		$motivoCancelamento  = 'Em razão de novo Acesso Externo Integral concedido no âmbito da Intimação Eletrônica afeta ao Documento Principal '.$nomeDocCompleto.'.';

		$arrObjAcessosExtDTO = $this->_retornaArrAcessoExtPorProcedimentoPorContato(array($idContato), $idProcesso, true);
		$objDTO = count($arrObjAcessosExtDTO) > 0 ? current($arrObjAcessosExtDTO) : null;

		if (!is_null($objDTO))
		{
			//Simula login para settar a unidade do acesso externo (validação de acesso de unidade logada = unidade do acesso externo cancelado no CORE)
			//Set usuário do módulo como responsável pelo cancelamento
			$idUnidadeAcessoExt = $objDTO->getNumIdUnidadeAcessoExterno();

			SessaoSEI::getInstance()->setBolHabilitada(false);
			SessaoSEI::getInstance()->simularLogin(null, null, $idUsuarioModulo, $idUnidadeAcessoExt);

			// SIGILOSO - conceder credencial
			$objProcedimentoDTO = MdPetIntAceiteRN::_retornaObjProcedimento($idProcesso);
			if ($objProcedimentoDTO->getStrStaNivelAcessoGlobalProtocolo() == ProtocoloRN::$NA_SIGILOSO
				|| $objProcedimentoDTO->getStrStaNivelAcessoLocalProtocolo() == ProtocoloRN::$NA_SIGILOSO){
				//if (is_numeric($idUsuarioModulo)){
					$objMdPetProcedimentoRN = new MdPetProcedimentoRN();
					$objConcederCredencial = $objMdPetProcedimentoRN->concederCredencial( array($objProcedimentoDTO, $idUnidadeAcessoExt, null, null, $idUsuarioModulo) );
				//}
			}
			// SIGILOSO - conceder credencial - FIM

			$objAcessoExternoDTO = new AcessoExternoDTO();
			$objAcessoExternoDTO->setNumIdAcessoExterno($objDTO->getNumIdAcessoExterno());
			$objAcessoExternoDTO->setStrMotivo($motivoCancelamento);
			$arrObjsCancelar[] = $objAcessoExternoDTO;
			$objAcessoExternoRN->cancelarDisponibilizacao($arrObjsCancelar);

			// SIGILOSO - cassarcredencial 
			if ($objProcedimentoDTO->getStrStaNivelAcessoGlobalProtocolo() == ProtocoloRN::$NA_SIGILOSO
				|| $objProcedimentoDTO->getStrStaNivelAcessoLocalProtocolo() == ProtocoloRN::$NA_SIGILOSO){
				//if (is_numeric($idUsuarioModulo)){
					$objMdPetProcedimentoRN = new MdPetProcedimentoRN();
					$objCassarCredencial = $objMdPetProcedimentoRN->cassarCredencial( $objConcederCredencial );
					$objMdPetProcedimentoRN->excluirAndamentoCredencial( $objConcederCredencial );
				//}
			}
			// SIGILOSO - cassarcredencial - FIM

			//Volta os valores defaults
			SessaoSEI::getInstance()->setBolHabilitada(true);
			SessaoSEI::getInstance()->simularLogin(null, null, $idUsuarioLogado, $idUnidadeLogada);
		}

		return $objDTO;

	}


	private function _atualizarTabelaModuloCancelamento($idAcessoExCancelado){

		$objMdPetAcessoExternoDTO = new MdPetAcessoExternoDTO();
		$objMdPetAcessoExternoDTO->setNumIdAcessoExterno($idAcessoExCancelado);
		$objMdPetAcessoExternoDTO->setStrSinAtivo('N');
		$objRN = new MdPetAcessoExternoRN();

		$objRN->alterar($objMdPetAcessoExternoDTO);
	}

	private function _realizarProcessosCancelamentoCriacaoAcExterno($arrParams, $nomeDoc, $tpAcessoSolicitado, $tipoPeticionamento)
	{

		try {
			$objDTOCancelado = $this->_cancelarAcessosExternosExistentes($arrParams, $nomeDoc);
			$idAcessoExCancelado = $objDTOCancelado->getNumIdAcessoExterno();
			$idProcedimento = $arrParams[0];
			$idContato = $arrParams[1];

			//Cadastrar Novo Acesso Externo para Intimação
			$arrObjAcessoExt = $this->_cadastrarAcessoExterno($arrParams[0], $arrParams[1], $tipoPeticionamento, $tpAcessoSolicitado, $nomeDoc);

			$idNovoAcessoExt = count($arrObjAcessoExt) > 0 ? $arrObjAcessoExt[0]->getNumIdAcessoExterno() : false;

			//Atualizar Antigos Relacionamentos do Acesso Externo
			$this->_updateIntimacaoAcessoExternoGerado($idNovoAcessoExt, $idProcedimento, $idContato);

			$this->_atualizarTabelaModuloCancelamento($idAcessoExCancelado);

			$arrRetorno = $this->_buscarTipoPeticionamentoAcessoEx($idAcessoExCancelado);

			$this->_cadastrarAcessoExternoModuloPorArray($arrRetorno, $idNovoAcessoExt);

			return $idNovoAcessoExt;
		} catch (Exception $e) {
			throw new InfraException('Erro cancelando acesso externo.', $e);
		}
	}

	private function _cadastrarAcessoExternoModuloPorArray($arrRetorno, $idAcessoEx){

		if(count($arrRetorno) > 0 && $idAcessoEx)
		{
			$objMdPetAcessoExternoDTO = new MdPetAcessoExternoDTO();


			$sinProcNovo = array_key_exists(static::$MD_PET_PROCESSO_INTERCORRENTE, $arrRetorno) && $arrRetorno[static::$MD_PET_PROCESSO_INTERCORRENTE] ? 'S' : 'N';
			$sinIntercorr = array_key_exists(static::$MD_PET_PROCESSO_NOVO, $arrRetorno) && $arrRetorno[static::$MD_PET_PROCESSO_NOVO] ? 'S' : 'N';
			$sinIntimacao = array_key_exists(static::$MD_PET_INTIMACAO, $arrRetorno) && $arrRetorno[static::$MD_PET_INTIMACAO] ? 'S' : 'N';

			$objMdPetAcessoExternoDTO->setNumIdAcessoExterno($idAcessoEx);
			$objMdPetAcessoExternoDTO->setStrSinProcessoNovo($sinProcNovo);
			$objMdPetAcessoExternoDTO->setStrSinProcessoIntercorrente($sinIntercorr);
			$objMdPetAcessoExternoDTO->setStrSinIntimacao($sinIntimacao);
			$objMdPetAcessoExternoDTO->setStrSinAtivo('S');

			$this->cadastrar($objMdPetAcessoExternoDTO);
		}
	}



	private function _retornaArrAcessoExtPorProcedimentoPorContato($arrIdsContato, $idProcedimento, $retUnidade = false, $retUm = true)
	{
		$objRN                    = new MdPetAcessoExternoRN();
		$objMdPetAcessoExternoDTO = new MdPetAcessoExternoDTO();
		$objMdPetAcessoExternoDTO->setStrSinAtivo('S');
		$objMdPetAcessoExternoDTO->setNumIdContatoAcessoExterno($arrIdsContato, InfraDTO::$OPER_IN);
		$objMdPetAcessoExternoDTO->setDblIdProtocoloAcessoExterno($idProcedimento);

		if($retUm) {
			$objMdPetAcessoExternoDTO->setNumMaxRegistrosRetorno(1);
		}

		if($retUnidade)
		{
			$objMdPetAcessoExternoDTO->retNumIdUnidadeAcessoExterno();
		}

		$objMdPetAcessoExternoDTO->retNumIdAcessoExterno();
		$objMdPetAcessoExternoDTO->retNumIdContatoAcessoExterno();

		$arrObjs = $objRN->listar($objMdPetAcessoExternoDTO);

		return $arrObjs;
	}

	private function _updateIntimacaoAcessoExternoGerado($idAcessoExt, $idProcedimento, $idContato){
		$objMdPetIntDestDTO = new MdPetIntRelDestinatarioDTO();
		$objMdPetIntDestRN  = new MdPetIntRelDestinatarioRN();

		$objMdPetIntDestDTO->setNumIdContato($idContato);
		$objMdPetIntDestDTO->setProcedimentoDocTIPOFK(InfraDTO::$TIPO_FK_OBRIGATORIA);
		$objMdPetIntDestDTO->setDblIdProtocoloProcedimento($idProcedimento);
		$objMdPetIntDestDTO->retTodos();

		$arrObjMdPetIntDestDTO = $objMdPetIntDestRN->listar($objMdPetIntDestDTO);

		if (count($arrObjMdPetIntDestDTO) > 0)
		{
			foreach ($arrObjMdPetIntDestDTO as $objMdPetIntDestDTO)
			{
				$objMdPetIntDestDTO->setNumIdAcessoExterno($idAcessoExt);
				$objMdPetIntDestDTO->setProcedimentoDocTIPOFK(InfraDTO::$TIPO_FK_OPCIONAL);
				$objMdPetIntDestRN->alterar($objMdPetIntDestDTO);
			}
		}
	}

	private function _realizarProcessosAdequacaoProcessoExternoParcial($idProcedimento, $idContato, $tipoPeticionamento, $arrIdsDocIntercorrente)
	{
		$arrObjs     	 = $this->_retornaArrAcessoExtPorProcedimentoPorContato(array($idContato), $idProcedimento);
		$objDTO      	 = count($arrObjs) > 0 ? current($arrObjs) : false;
		$idAcessoExt 	 = $objDTO ? $objDTO->getNumIdAcessoExterno() : false;
		$arrDocAcessoExt = $this->_preencherArrDocDisponibilizados($tipoPeticionamento, null, $arrIdsDocIntercorrente);

		$objProtAcExRN   = new RelAcessoExtProtocoloRN();

		foreach ($arrDocAcessoExt as $doc)
		{
			if($idAcessoExt)
			{
				$objProtAcExDTO = new RelAcessoExtProtocoloDTO();
				$objProtAcExDTO->setNumIdAcessoExterno($idAcessoExt);
				$objProtAcExDTO->setDblIdProtocolo($doc);
				$existe = 	$objProtAcExRN->contar( $objProtAcExDTO ) > 0;

				if(!$existe) {
					$objProtAcExRN->cadastrar($objProtAcExDTO);
				}
			}
		}

		return $idAcessoExt;
	}

	private function _verificarDocPossuiAcessoExternoAtivo($idProcedimentoRecibo, $idContato, $idDocumentoRecibo)
	{
		$idAcessoExterno      = null;
		$emailDestinatario    = SessaoSEIExterna::getInstance()->getStrSiglaUsuarioExterno();
		$objAcessoExternoRN   = new AcessoExternoRN();
		$objRelAcessExtProtRN = new RelAcessoExtProtocoloRN();
		$objAcessoExternoDTO  = new AcessoExternoDTO();

		$objAcessoExternoDTO->retTodos();
		$objAcessoExternoDTO->setOrd("IdAcessoExterno", InfraDTO::$TIPO_ORDENACAO_DESC);
		$objAcessoExternoDTO->retDblIdProtocoloAtividade();
		$objAcessoExternoDTO->retNumIdContatoParticipante();

		$objAcessoExternoDTO->setDblIdProtocoloAtividade($idProcedimentoRecibo);
		$objAcessoExternoDTO->setNumIdContatoParticipante($idContato);
		$objAcessoExternoDTO->setStrEmailDestinatario($emailDestinatario);
		$objAcessoExternoDTO->setStrStaTipo(AcessoExternoRN::$TA_USUARIO_EXTERNO);
		$objAcessoExternoDTO->setStrSinAtivo('S');
	    $objAcessoExternoDTO->setDtaValidade(InfraData::getStrDataHoraAtual(),InfraDTO::$OPER_MAIOR_IGUAL);

		$arrAcExternoDTO = $objAcessoExternoRN->listar($objAcessoExternoDTO);
		$count           = $objAcessoExternoRN->contar($objAcessoExternoDTO);


		if($count > 0)
		{
			$idsAcessoExterno = InfraArray::converterArrInfraDTO($arrAcExternoDTO, 'IdAcessoExterno');
			$arrTiposAcEx     = $this->getTipoAcessoExternoPorAcessoExterno($idsAcessoExterno);
			$possuiIntegral   = array_search(MdPetAcessoExternoRN::$ACESSO_INTEGRAL, $arrTiposAcEx);

			//Se for integral, retorna o Id do Integral, se não busca se os parciais ativos e válidos possuem acesso
			if($possuiIntegral){
				$idAcessoExterno = $possuiIntegral;
			}
			else
			{
				$objRelAcessoExtProtDTO = new RelAcessoExtProtocoloDTO();
				$objRelAcessoExtProtDTO->setDblIdProtocolo($idDocumentoRecibo);
				$objRelAcessoExtProtDTO->setNumIdAcessoExterno($idsAcessoExterno, InfraDTO::$OPER_IN);
				$objRelAcessoExtProtDTO->retNumIdAcessoExterno();
				
				$countIdAcEx = $objRelAcessExtProtRN->contar($objRelAcessoExtProtDTO);
				
				if($countIdAcEx > 0)
				{
					$objDTO  = current($objRelAcessExtProtRN->listar($objRelAcessoExtProtDTO));
					$idAcessoExterno = $objDTO->getNumIdAcessoExterno();
				}
			}
		}

		return $idAcessoExterno;
	}
	
	
	protected function getIdAcessoExternoReciboConectado($objMdPetReciboDTO)
	{
		$objMdPetUsuarioRN    = new MdPetIntUsuarioRN();
		$objMdPetAcessoExtRN  = new MdPetAcessoExternoRN();
		$idDocumentoRecibo    = $objMdPetReciboDTO->getDblIdDocumento();
		$idProcedimentoRecibo = $objMdPetReciboDTO->getNumIdProtocolo();
		$objContatoDTO        = $objMdPetUsuarioRN->retornaObjContatoPorIdUsuario(array(SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno()));
		$idAcessoExterno      = null;
		
		//Get Id Acesso Externo Associado ao Módulo
		$objMdPetAcessoExternoDTO = new MdPetAcessoExternoDTO();
		$objMdPetAcessoExternoDTO->setNumIdContatoAcessoExterno($objContatoDTO->getNumIdContato() );
		$objMdPetAcessoExternoDTO->setDblIdProtocoloAcessoExterno($idProcedimentoRecibo);
		$objMdPetAcessoExternoDTO->setStrSinAtivo('S');
		$objMdPetAcessoExternoDTO->retNumIdAcessoExterno();
		$objMdPetAcessoExternoDTO->setNumMaxRegistrosRetorno(1);
		$objMdPetAcessoExternoDTO->setOrd("IdAcessoExterno", InfraDTO::$TIPO_ORDENACAO_DESC);
		$objMdPetAcessoExternoDTO = $objMdPetAcessoExtRN->consultar($objMdPetAcessoExternoDTO);


		//Se o acesso externo do módulo está ativo, settar do módulo, se não, buscar se o mesmo possui algum acesso externo ativo
		if(!is_null($objMdPetAcessoExternoDTO)){
			$idAcessoExterno =  $objMdPetAcessoExternoDTO->getNumIdAcessoExterno();
		}
		else
		{
			$idAcessoExterno = $this->_verificarDocPossuiAcessoExternoAtivo($idProcedimentoRecibo, $objContatoDTO->getNumIdContato(), $idDocumentoRecibo);
		}
		
		return $idAcessoExterno;
	}
	
	protected function verificaIdAcessoExternoModuloConectado($idAcessoExterno){
		$objMdPetAcessoExternoDTO = new MdPetAcessoExternoDTO();
		$objMdPetAcessoExternoDTO->setNumIdAcessoExterno($idAcessoExterno);
		$objMdPetAcessoExternoDTO->setStrSinAtivo('S');
		$objMdPetAcessoExternoDTO->retNumIdAcessoExterno();
		$objMdPetAcessoExternoDTO->setNumMaxRegistrosRetorno(1);

		$objMdPetAcessoExternoRN = new MdPetAcessoExternoRN();
		$isModulo = $objMdPetAcessoExternoRN->contar($objMdPetAcessoExternoDTO) > 0;
		
		return $isModulo;

	}

	private function _getDadosAcessoExterno($idAcessoExt){
		$arrParams           = array();
		$objAcessoExternoRN  = new AcessoExternoRN();
		$objAcessoExternoDTO = new AcessoExternoDTO();
		$objAcessoExternoDTO->retDblIdProtocoloAtividade();
		$objAcessoExternoDTO->retNumIdContatoParticipante();
		$objAcessoExternoDTO->setNumIdAcessoExterno($idAcessoExt);
		$objAcessoExternoDTO->setNumMaxRegistrosRetorno(1);
		$objAcessoExternoDTO->setBolExclusaoLogica(false);

		$count = $objAcessoExternoRN->contar($objAcessoExternoDTO);

		if($count > 0)
		{
			$objAcessoExternoDTO         = $objAcessoExternoRN->consultar($objAcessoExternoDTO);
			$arrParams['idContato']      = $objAcessoExternoDTO->getNumIdContatoParticipante();
			$arrParams['idProcedimento'] = $objAcessoExternoDTO->getDblIdProtocoloAtividade();
		}

		return $arrParams;
	}

	private function _getIdsIntimacaoContato($arrParams){
		$arrIdIntDest            = array();

		$objMdPetIntRelDestRN    = new MdPetIntRelDestinatarioRN();
		//Get todas Intimações deste destinatário
		$objMdPetIntRelDestDTO = new MdPetIntRelDestinatarioDTO();
		$objMdPetIntRelDestDTO->retNumIdMdPetIntRelDestinatario();
		$objMdPetIntRelDestDTO->setDblIdProcedimento($arrParams['idProcedimento']);
		$objMdPetIntRelDestDTO->setNumIdContato($arrParams['idContato']);
		$objMdPetIntRelDestDTO->setProcedimentoDocTIPOFK(InfraDTO::$TIPO_FK_OBRIGATORIA);

		$count = $objMdPetIntRelDestRN->contar($objMdPetIntRelDestDTO);

		if($count > 0)
		{
			$arrIdIntDest = array_unique(InfraArray::converterArrInfraDTO($objMdPetIntRelDestRN->listar($objMdPetIntRelDestDTO), 'IdMdPetIntRelDestinatario'));
		}

		return $arrIdIntDest;
	}

	private function _buscarDocumentosResposta($arrParams){
		$arrRetorno              = array();
		$objMdPetIntDestRespRN   = new MdPetIntDestRespostaRN();
		$objMdPetIntDestResDocRN = new MdPetIntRelRespDocRN();

		//Busca todos os ids 'rel dest' id por intimacao e por contato
		$arrIdIntDest = $this->_getIdsIntimacaoContato($arrParams);

		if(count($arrIdIntDest) > 0) {
			$objMdPetIntDestRespDTO = new MdPetIntDestRespostaDTO();
			$objMdPetIntDestRespDTO->setNumIdMdPetIntRelDestinatario($arrIdIntDest, InfraDTO::$OPER_IN);
			$objMdPetIntDestRespDTO->retNumIdMdPetIntDestResposta();


			$count = $objMdPetIntDestRespRN->contar($objMdPetIntDestRespDTO);

			if ($count > 0) {
				$arrIntDestResp =  array_unique(InfraArray::converterArrInfraDTO($objMdPetIntDestRespRN->listar($objMdPetIntDestRespDTO), 'IdMdPetIntDestResposta'));

				if(count($arrIntDestResp) > 0){
					$objMdPetIntDestResDocDTO = new MdPetIntRelRespDocDTO();
					$objMdPetIntDestResDocDTO->setNumIdMdPetIntDestResposta($arrIntDestResp, InfraDTO::$OPER_IN);
					$objMdPetIntDestResDocDTO->retDblIdDocumento();

					$count = $objMdPetIntDestResDocRN->contar($objMdPetIntDestResDocDTO);

					if($count > 0){
						$arrRetorno =  array_unique(InfraArray::converterArrInfraDTO($objMdPetIntDestResDocRN->listar($objMdPetIntDestResDocDTO), 'IdDocumento'));

					}
				}
			}
		}

	return $arrRetorno;
	}


	private function _buscarDocumentosAcessoExterno($idAcessoExt, $arrParams){
		//Buscar documentos envolvidos nas Intimações desse processo
		$arrDocInt  = $this->_buscarDocumentosIntimacoesProcesso($idAcessoExt, $arrParams);

		//Buscar os documentos envolvidos nos peticionamentos
		$arrDocPet  = $this->_buscarDocumentosPeticionamentos($arrParams);

		// Buscar os documentos envolvidos nas respostas por esse usuário neste processo
		$arrDocResp = $this->_buscarDocumentosResposta($arrParams);

		// Juntar e unificar os ids do documento
		$arrDocs   = array_unique(array_merge($arrDocPet, $arrDocInt, $arrDocResp));

		return $arrDocs;
	}


	protected function corrigirDadosPosCancelamentoAcessoIntegralConectado($idAcessoExt)
	{
		//Buscar dados relacionados a esse acesso externo
		$arrParams  = $this->_getDadosAcessoExterno($idAcessoExt);

		//Settar obj main do módulo no acesso externo
		$this->_atualizarTabelaModuloCancelamento($idAcessoExt);

		//Busca todos os ids de acesso externo envolvidos com esse acesso externo
		$arrDocsPet = $this->_buscarDocumentosAcessoExterno($idAcessoExt, $arrParams);

		//Busca os tipos de peticionamento realizados para esse acesso externo
		$arrRetorno = $this->_buscarTipoPeticionamentoAcessoEx($idAcessoExt);

		//Gerar Novo Acesso Externo do Tipo Parcial
		$arrObjAcessoExt = $this->_cadastrarAcessoExterno($arrParams['idProcedimento'], $arrParams['idContato'], static::$MD_PET_CORRECAO_CANCELAMENTO, static::$ACESSO_PARCIAL, null, $arrDocsPet);
		$objAcessoExtDTO = count($arrObjAcessoExt) > 0 ? current($arrObjAcessoExt) : null;
		$idNovoAcessExt  = $objAcessoExtDTO->getNumIdAcessoExterno();

		//Atualizar o novo id parcial na tabela do módulo de acesso externo
		$this->_cadastrarAcessoExternoModuloPorArray($arrRetorno, $idNovoAcessExt);

		//Readequar as intimações com o novo id de acesso externo
		$this->_updateIntimacaoAcessoExternoGerado($idNovoAcessExt, $arrParams['idProcedimento'], $arrParams['idContato']);
	}

	private function _buscarTipoPeticionamentoAcessoEx($idAcessoExt)
	{
		$arrRetorno = array();

		$objMdPetAcessExtDTO = new MdPetAcessoExternoDTO();
		$objMdPetAcessExtDTO->setNumIdAcessoExterno($idAcessoExt);
		$objMdPetAcessExtDTO->retStrSinProcessoNovo();
		$objMdPetAcessExtDTO->retStrSinIntimacao();
		$objMdPetAcessExtDTO->retStrSinProcessoIntercorrente();

		$objRN = new MdPetAcessoExternoRN();
		$count = $objRN->contar($objMdPetAcessExtDTO);

		if($count > 0)
		{
			$objMdPetAcessExtDTO = $objRN->consultar($objMdPetAcessExtDTO);
			$arrRetorno[static::$MD_PET_PROCESSO_NOVO] = $objMdPetAcessExtDTO->getStrSinProcessoNovo() == 'S' ? true : false;
			$arrRetorno[static::$MD_PET_PROCESSO_INTERCORRENTE] = $objMdPetAcessExtDTO->getStrSinProcessoIntercorrente() == 'S' ? true : false;
			$arrRetorno[static::$MD_PET_INTIMACAO] = $objMdPetAcessExtDTO->getStrSinIntimacao() == 'S' ? true : false;
		}

		return $arrRetorno;
	}


	private function _buscarDocumentosPeticionamentos($arrParams){
		$idContato   = $arrParams['idContato'];
		$idProcesso  = $arrParams['idProcedimento'];
		$idUsuario   = $this->_getIdUsuarioPorIdContato($idContato);
		$arrIdsRecib = array();
		$arrRetorno  = array();
		$arrIdsDoc   = array();

		//get ids recibo
		$objMdPetReciboDTO = new MdPetReciboDTO();
		$objMdPetReciboDTO->setNumIdProtocolo($idProcesso);
		$objMdPetReciboDTO->setNumIdUsuario($idUsuario);
		$objMdPetReciboDTO->retDblIdDocumento();
		$objMdPetReciboDTO->retNumIdReciboPeticionamento();

		$objMdPetReciboRN = new MdPetReciboRN();
		$count = $objMdPetReciboRN->contar($objMdPetReciboDTO);

		if($count > 0){
			$arrRetorno = $objMdPetReciboRN->listar($objMdPetReciboDTO);
			$arrIdsRecib = InfraArray::converterArrInfraDTO($arrRetorno, 'IdDocumento');
			$arrIdsPkRecibo = InfraArray::converterArrInfraDTO($arrRetorno, 'IdReciboPeticionamento');

			$objMdPetReciboDocAnexoDTO = new MdPetRelReciboDocumentoAnexoDTO();
			$objMdPetReciboDocAnexoDTO->setNumIdReciboPeticionamento($arrIdsPkRecibo, InfraDTO::$OPER_IN);
			$objMdPetReciboDocAnexoDTO->retNumIdDocumento();

			$objMdPetReciboDocAnexoRN = new MdPetRelReciboDocumentoAnexoRN();
			$count = $objMdPetReciboDocAnexoRN->contar($objMdPetReciboDocAnexoDTO);

			if($count > 0){
				$arrIdsDoc = InfraArray::converterArrInfraDTO($objMdPetReciboDocAnexoRN->listar($objMdPetReciboDocAnexoDTO), 'IdDocumento');
			}
		}

		$arrRetorno = array_merge($arrIdsDoc, $arrIdsRecib);


		return $arrRetorno;
	}


	private function _buscarDocumentosIntimacoesProcesso($idAcessoExt, $arrParams)
	{
		//Busca ids de todas as intimações
		$arrRetorno            = array();
		$idsProtocolo          = array();
		$idsCertidao           = array();
		$objMdPetIntRelDestRN  = new MdPetIntRelDestinatarioRN();
		$objMdPetIntAceiteRN   = new MdPetIntAceiteRN();


		$objMdPetIntRelDestDTO = new MdPetIntRelDestinatarioDTO();
		$objMdPetIntRelDestDTO->setNumIdAcessoExterno($idAcessoExt);
		$objMdPetIntRelDestDTO->retDblIdProtocolo();

		$count   = $objMdPetIntRelDestRN->contar($objMdPetIntRelDestDTO);

		if($count > 0)
		{
			$arrObjs       = $objMdPetIntRelDestRN->listar($objMdPetIntRelDestDTO);
			$idsProtocolo =  InfraArray::converterArrInfraDTO($arrObjs, 'IdProtocolo');
		}

		//Busca todas as certidoes
		$idsRelDest = $this->_getIdsIntimacaoContato($arrParams);

		if(count($idsRelDest)> 0 ) {
			$objMdPetIntAceiteDTO = new MdPetIntAceiteDTO();
			$objMdPetIntAceiteDTO->setNumIdMdPetIntRelDestinatario($idsRelDest, InfraDTO::$OPER_IN);
			$objMdPetIntAceiteDTO->retDblIdDocumentoCertidao();

			$count      = $objMdPetIntAceiteRN->contar($objMdPetIntAceiteDTO);

			if($count > 0) {
				$idsCertidao = array_unique(InfraArray::converterArrInfraDTO($objMdPetIntAceiteRN->listar($objMdPetIntAceiteDTO), 'IdDocumentoCertidao'));

			}
		}

		$arrRetorno = array_merge($idsProtocolo, $idsCertidao);

		return $arrRetorno;
	}






}

?>