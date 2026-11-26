<?php
/**
 * CLI regression of real scheduling, document and Litigioso code without DB/RFB.
 * Run: php suspensao_identidade_test.php
 */
if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

require_once dirname(__DIR__, 3) . '/SEI.php';
require_once dirname(__DIR__) . '/rn/MdPetIntUsuarioRN.php';
require_once dirname(__DIR__) . '/rn/MdPetAgendamentoAutomaticoRN.php';
require_once dirname(__DIR__) . '/rn/MdPetVincRepresentantRN.php';
require_once dirname(__DIR__, 2) . '/litigioso/LitigiosoIntegracao.php';
require_once dirname(__DIR__, 2) . '/litigioso/dto/MdLitDadoInteressadoDTO.php';
require_once dirname(__DIR__, 2) . '/litigioso/dto/MdLitControleDTO.php';
require_once dirname(__DIR__, 2) . '/litigioso/dto/MdLitProcessoSituacaoDTO.php';

function check(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

class SessaoSEI
{
    public static $UNIDADE_TESTE = 'TESTE';
    public const FIELDS = [
        'NumIdOrgaoSistema', 'StrDescricaoOrgaoSistema', 'NumIdSistema',
        'StrSiglaOrgaoUsuario', 'StrDescricaoOrgaoUsuario', 'NumIdOrgaoUsuario',
        'NumIdUsuario', 'StrIdOrigemUsuario', 'StrSiglaUsuario', 'StrNomeUsuario',
        'NumIdUnidadeAtual', 'StrIdOrigemUnidadeAtual', 'StrSiglaUnidadeAtual',
        'NumIdOrgaoUnidadeAtual', 'StrSiglaOrgaoUnidadeAtual', 'StrDescricaoOrgaoUnidadeAtual',
    ];
    public bool $enabled = true;
    public array $normal = [];
    public array $simulated = [];
    public array $logins = [];
    public bool $failLogin = false;
    public int $failLoginAt = 0;
    public bool $permission = true;

    public static function getInstance($enabled = true): self
    {
        static $instance;
        return $instance ??= new self();
    }

    public function __call(string $method, array $args)
    {
        $field = substr($method, 3);
        check(in_array($field, self::FIELDS, true), 'Unexpected session field: ' . $method);
        if (str_starts_with($method, 'set')) {
            $this->simulated[$field] = $args[0];
            return;
        }
        check(str_starts_with($method, 'get'), 'Unexpected session method: ' . $method);
        return ($this->enabled ? $this->normal : $this->simulated)[$field];
    }

    public function isBolHabilitada(): bool { return $this->enabled; }
    public function setBolHabilitada($value): void { $this->enabled = $value; }
    public function verificarPermissao($resource): bool { return $this->permission; }

    public function simularLogin($sigla, $unitName, $userId, $unitId): void
    {
        check(!$this->enabled, 'Login without disabling normal session');
        check((string) $userId === '400', 'Unexpected technical user');
        check(($unitName === self::$UNIDADE_TESTE && $unitId === null)
            || ($unitName === null && $unitId === 88), 'Wrong document/scheduling unit');
        $this->logins[] = [$userId, $unitId];
        foreach (self::FIELDS as $field) {
            $this->simulated[$field] = 'technical-' . $field;
        }
        $this->simulated['NumIdUsuario'] = $userId;
        $this->simulated['NumIdUnidadeAtual'] = $unitId ?? 77;
        if ($this->failLogin || count($this->logins) === $this->failLoginAt) {
            throw new InfraException('Injected login failure');
        }
    }
}

class SessaoSEIExterna
{
    public static function getInstance(): self { return new self(); }
    public function getNumIdUsuarioExterno() { return null; }
}

class IdentityTestConfiguration extends ConfiguracaoSEI
{
    public static bool $petEnabled = true;
    public function getArrConfiguracoes(): array
    {
        return ['SEI' => ['Modulos' => self::$petEnabled ? ['PeticionamentoIntegracao' => 'peticionamento'] : []]];
    }
}

class InfraParametro
{
    public static $userId = 400;
    public static int $reads = 0;
    public function __construct($bank) { check($bank instanceof IdentityTestBank, 'Unisolated parameters'); }
    public function getValor($name, $required = true)
    {
        check($name === 'MODULO_PETICIONAMENTO_ID_USUARIO_SISTEMA', 'Unexpected parameter read');
        self::$reads++;
        return self::$userId;
    }
}

class MdPetContatoRN
{
    public static $STR_INFRA_PARAMETRO_SIGLA_CONTATO = 'MODULO_PETICIONAMENTO_ID_USUARIO_SISTEMA';
}

class UsuarioRN
{
    public static $TU_SISTEMA = '1';
    public static ?UsuarioDTO $user = null;
    public static bool $fail = false;
    public static array $deactivated = [];
    public function consultarRN0489(UsuarioDTO $filter): ?UsuarioDTO
    {
        if ($filter->isSetNumIdContato()) {
            assertActor('external-user-read');
            $dto = new UsuarioDTO();
            $dto->setNumIdUsuario(401);
            return $dto;
        }
        if (self::$fail) {
            throw new InfraException('Injected user read failure');
        }
        return self::$user === null ? null : clone self::$user;
    }
    public function desativarRN0695(array $users): void
    {
        assertActor('external-user-deactivation');
        self::$deactivated = $users;
    }
}

class ContatoRN
{
    public static $TN_PESSOA_FISICA = 'F';
    public static $TN_PESSOA_JURIDICA = 'J';
    public static ?ContatoDTO $updated = null;
    public function consultarRN0324(ContatoDTO $filter): ContatoDTO
    {
        assertActor('contact-read');
        $dto = new ContatoDTO();
        $dto->setNumIdContato(999);
        $dto->setStrObservacao('previous-observation');
        return $dto;
    }
    public function alterarRN0323(ContatoDTO $dto): void
    {
        assertActor('contact-update');
        $api = new ContatoAPI();
        $api->setIdContato($dto->getNumIdContato());
        $api->setObservacao($dto->getStrObservacao());
        (new IdentityTestLitigioso())->alterarContato($api);
        self::$updated = clone $dto;
    }
}

class IdentityTestRepresentation extends MdPetVincRepresentantRN
{
    public static ?MdPetVincRepresentantDTO $updated = null;
    public function alterar(MdPetVincRepresentantDTO $dto): void
    {
        assertActor('link-suspension');
        self::$updated = clone $dto;
    }
}

class LogSEI
{
    public static int $writes = 0;
    public static function getInstance(): self { return new self(); }
    public function gravar($message, $level): void
    {
        assertActor('real-log');
        check(str_contains($message, 'FIM'), 'Missing completion log');
        if (IdentityTestScheduling::$failAt === 'log') {
            throw new InfraException('Injected log failure');
        }
        self::$writes++;
    }
}

class IdentityTestBank extends BancoSEI
{
    public function getIdBanco() { return 'suspension-identity-test'; }
    public function getIdConexao() { return $this->getIdBanco(); }
    public function isBolProcessandoTransacao() { return false; }
    public function abrirConexao() {}
    public function fecharConexao() {}
}

class MdLitDadoInteressadoRN
{
    public static int $reads = 0;
    public function listar($filter): array
    {
        self::$reads++;
        $dto = new MdLitDadoInteressadoDTO();
        $dto->setNumIdMdLitControle(1);
        return [$dto];
    }
}

class MdLitControleRN
{
    public function consultar($filter): MdLitControleDTO
    {
        $dto = new MdLitControleDTO();
        $dto->setDblIdProcedimento(1);
        return $dto;
    }
}

class MdLitProcessoSituacaoRN
{
    public function getDadosPrimeiraSituacaoCadastrada($id): MdLitProcessoSituacaoDTO
    {
        $dto = new MdLitProcessoSituacaoDTO();
        $dto->setNumIdUnidade(91);
        $dto->setStrSiglaUnidade('SYNTHETIC');
        return $dto;
    }
}

class ProtocoloRN
{
    public static $TP_DOCUMENTO_GERADO = 'G';
    public static $NA_PUBLICO = '0';
    public function consultarRN0186($filter): ProtocoloDTO
    {
        $dto = new ProtocoloDTO();
        $dto->setStrProtocoloFormatado('synthetic-process');
        return $dto;
    }
}

class IdentityTestLitigioso extends LitigiosoIntegracao
{
    protected function verificarUnidadePermitidaViaParametro() { return true; }
}

class SeiRN
{
    public static bool $fail = false;
    public function incluirDocumento(DocumentoAPI $document): SaidaIncluirDocumentoAPI
    {
        assertActor('document');
        check(SessaoSEI::getInstance()->getNumIdUnidadeAtual() === 88, 'Wrong generating unit');
        check($document->getSinAssinado() === 'N', 'Automatic document signed');
        if (self::$fail) {
            throw new InfraException('Injected document failure');
        }
        $result = new SaidaIncluirDocumentoAPI();
        $result->setIdDocumento('900');
        return $result;
    }
}

class DocumentoRN
{
    public static $TD_FORMULARIO_AUTOMATICO = 'A';
    public function consultarRN0005($filter): DocumentoDTO
    {
        assertActor('document-read');
        $dto = new DocumentoDTO();
        $dto->setDblIdDocumento(900);
        return $dto;
    }
}

class DocumentoBD
{
    public static bool $fail = false;
    public static int $writes = 0;
    public function __construct($bank) { check($bank instanceof IdentityTestBank, 'Unisolated document'); }
    public function alterar(DocumentoDTO $dto): void
    {
        assertActor('document-update');
        check($dto->getStrStaDocumento() === DocumentoRN::$TD_FORMULARIO_AUTOMATICO, 'Document type changed');
        if (self::$fail) {
            throw new InfraException('Injected document update failure');
        }
        self::$writes++;
    }
}

class IdentityTestScheduling extends MdPetAgendamentoAutomaticoRN
{
    public static array $events = [];
    public static string $failAt = '';
    public static int $quantity = 2;
    protected function recuperarQuantidadeParametrizadaPessoaFisica() { return self::$quantity; }
    protected function recuperarQuantidadeParametrizadaPessoaJuridica() { return self::$quantity; }
    protected function recuperarFilaConsultaCpf($limit): array { return $this->queue('CPF'); }
    protected function recuperarFilaConsultaCnpj($limit): array { return $this->queue('CNPJ'); }
    private function queue(string $nature): array
    {
        assertActor('first-' . $nature . '-queue');
        check(MdPetIntUsuarioRN::isExecutandoSuspensaoAutomatica(), 'Missing scheduling scope');
        self::$events[] = 'queue';
        if (self::$failAt === 'queue') {
            throw new InfraException('Injected queue failure');
        }
        return ['synthetic-document'];
    }
    protected function consultarAtualizarVinculoPF($queue, $count, $limit) { return $this->process(); }
    protected function consultarAtualizarVinculoPJ(array $queue): array { $this->process(); return []; }
    private function process(): array
    {
        assertActor('RFB/suspension');
        self::$events[] = 'process';
        $contact = new ContatoAPI();
        $contact->setIdContato('999');
        $contact->setObservacao('synthetic-RFB-observation');
        check((new IdentityTestLitigioso())->alterarContato($contact) === null, 'Unexpected hook result');
        check(MdLitDadoInteressadoRN::$reads === 0, 'Litigioso block executed inside authorized scope');
        if (self::$failAt === 'process') {
            throw new InfraException('Injected RFB/suspension failure');
        }
        generateDocument();
        assertActor('after-document');
        check(SessaoSEI::getInstance()->getNumIdUnidadeAtual() === 77, 'Document leaked its unit');
        check(MdPetIntUsuarioRN::isExecutandoSuspensaoAutomatica(), 'Document lost scheduling scope');
        return ['arrObjMdPetVincRepresentantSuspensos' => []];
    }
    protected function logarSuspensaoVinculacoesEProcuracoes($rows, $seconds, $title, $nature, $limit)
    {
        assertActor('log');
        self::$events[] = 'log';
        parent::logarSuspensaoVinculacoesEProcuracoes($rows, $seconds, $title, $nature, $limit);
    }
    protected function registrarResultadoConsultaCnpj(array $rows, $seconds, int $limit): void
    {
        assertActor('log');
        self::$events[] = 'log';
        parent::registrarResultadoConsultaCnpj($rows, $seconds, $limit);
    }
}

function assertActor(string $phase): void
{
    $session = SessaoSEI::getInstance();
    check(!$session->enabled && (string) $session->getNumIdUsuario() === '400',
        'Wrong actor on ' . $phase);
}

function generateDocument(): SaidaIncluirDocumentoAPI
{
    $procedure = new ProcedimentoDTO();
    $procedure->setNumIdUnidadeGeradoraProtocolo(88);
    $link = new MdPetVinculoDTO();
    $link->setDblIdProtocolo(1);
    $method = new ReflectionMethod(MdPetVincRepresentantRN::class, '_gerarDocumentoAgendamento');
    $method->setAccessible(true);
    return $method->invoke(new MdPetVincRepresentantRN(), [
        'procedimento' => $procedure, 'objVinculo' => $link, 'idSerie' => '1',
        'agendamento' => true, 'corpoDocumentoHTML' => '<p>synthetic</p>',
    ]);
}

function resetFixtures(bool $enabled = true): array
{
    $session = SessaoSEI::getInstance();
    $session->normal = $session->simulated = [];
    foreach (SessaoSEI::FIELDS as $field) {
        $session->normal[$field] = 'normal-' . $field;
        $session->simulated[$field] = 'simulated-' . $field;
    }
    $session->normal['NumIdUsuario'] = 7;
    $session->normal['NumIdUnidadeAtual'] = 42;
    $session->simulated['NumIdUsuario'] = 8;
    $session->simulated['NumIdUnidadeAtual'] = 43;
    $session->enabled = $enabled;
    $session->logins = [];
    $session->failLogin = false;
    $session->failLoginAt = 0;
    $session->permission = true;
    IdentityTestConfiguration::$petEnabled = true;
    InfraParametro::$userId = 400;
    InfraParametro::$reads = 0;
    UsuarioRN::$user = new UsuarioDTO();
    UsuarioRN::$user->setNumIdUsuario(400);
    UsuarioRN::$user->setStrSinAtivo('S');
    UsuarioRN::$user->setStrStaTipo(UsuarioRN::$TU_SISTEMA);
    UsuarioRN::$fail = SeiRN::$fail = DocumentoBD::$fail = false;
    UsuarioRN::$deactivated = [];
    ContatoRN::$updated = IdentityTestRepresentation::$updated = null;
    DocumentoBD::$writes = MdLitDadoInteressadoRN::$reads = 0;
    LogSEI::$writes = 0;
    IdentityTestScheduling::$events = [];
    IdentityTestScheduling::$failAt = '';
    IdentityTestScheduling::$quantity = 2;
    InfraDebug::getInstance()->setBolLigado(false);
    return [$session->enabled, $session->normal, $session->simulated];
}

function assertRestored(array $before): void
{
    $session = SessaoSEI::getInstance();
    check([$session->enabled, $session->normal, $session->simulated] === $before, 'Session not fully restored');
    check(!MdPetIntUsuarioRN::isExecutandoSuspensaoAutomatica(), 'Scheduling scope leaked');
}

function rejected(callable $operation): void
{
    try {
        $operation();
    } catch (InfraException $e) {
        return;
    }
    throw new RuntimeException('Expected refusal/failure');
}

function runJob(string $nature): void
{
    $method = new ReflectionMethod(MdPetAgendamentoAutomaticoRN::class,
        'ConsultarSituacaoReceita' . ($nature === 'CPF' ? 'Cpf' : 'Cnpj') . 'Conectado');
    $method->setAccessible(true);
    $method->invoke(new IdentityTestScheduling());
}

foreach (['SessaoSEI', 'SessaoSEIExterna', 'IdentityTestConfiguration', 'InfraParametro', 'UsuarioRN',
    'MdPetContatoRN', 'MdLitDadoInteressadoRN', 'MdLitControleRN', 'MdLitProcessoSituacaoRN',
    'ProtocoloRN', 'SeiRN', 'DocumentoRN', 'DocumentoBD', 'ContatoRN', 'LogSEI'] as $class) {
    check((new ReflectionClass($class))->getFileName() === __FILE__, 'Double not loaded: ' . $class);
}
$bank = new IdentityTestBank();
$instance = new ReflectionProperty(BancoSEI::class, 'instance');
$instance->setAccessible(true);
$instance->setValue(null, $bank);
check(BancoSEI::getInstance() === $bank, 'Singleton bank not isolated');
$configuration = new IdentityTestConfiguration();
$instance = new ReflectionProperty(ConfiguracaoSEI::class, 'instance');
$instance->setAccessible(true);
$instance->setValue(null, $configuration);
check(ConfiguracaoSEI::getInstance() === $configuration, 'Configuration not isolated');
$userRN = new MdPetIntUsuarioRN();
$cases = 0;

foreach ([true, false] as $enabled) {
    foreach (['CPF', 'CNPJ'] as $nature) {
        $before = resetFixtures($enabled);
        runJob($nature);
        assertRestored($before);
        check(IdentityTestScheduling::$events === ['queue', 'process', 'log'], 'Scheduling flow changed');
        check(InfraParametro::$reads === 1 && DocumentoBD::$writes === 1 && LogSEI::$writes === 1,
            'Repeated resolution/missing document/log');
        echo 'PASS job-' . $nature . '-' . (int) $enabled . "\n";
        $cases++;
        foreach (['queue', 'process', 'document', 'document-update', 'log', 'login', 'document-login'] as $phase) {
            $before = resetFixtures($enabled);
            IdentityTestScheduling::$failAt = $phase;
            SeiRN::$fail = $phase === 'document';
            DocumentoBD::$fail = $phase === 'document-update';
            SessaoSEI::getInstance()->failLogin = $phase === 'login';
            SessaoSEI::getInstance()->failLoginAt = $phase === 'document-login' ? 2 : 0;
            rejected(static fn() => runJob($nature));
            assertRestored($before);
            check(!in_array('log', IdentityTestScheduling::$events, true) || $phase === 'log',
                'Failure treated as successful suspension');
            echo 'PASS restore-' . $nature . '-' . $phase . '-' . (int) $enabled . "\n";
            $cases++;
        }
    }
}

foreach (['CPF', 'CNPJ'] as $nature) {
    $before = resetFixtures();
    IdentityTestScheduling::$quantity = 0;
    InfraParametro::$userId = null;
    runJob($nature);
    assertRestored($before);
    check(IdentityTestScheduling::$events === [] && InfraParametro::$reads === 0, 'Disabled job resolved identity');
    echo 'PASS disabled-job-' . $nature . "\n";
    $cases++;
}

foreach (['missing-parameter', 'missing-user', 'zero-id', 'negative-id', 'human', 'inactive', 'user-read'] as $invalid) {
    $before = resetFixtures();
    if ($invalid === 'missing-parameter') { InfraParametro::$userId = null; }
    if ($invalid === 'missing-user') { UsuarioRN::$user = null; }
    if ($invalid === 'zero-id') { UsuarioRN::$user->setNumIdUsuario(0); }
    if ($invalid === 'negative-id') { UsuarioRN::$user->setNumIdUsuario(-1); }
    if ($invalid === 'human') { UsuarioRN::$user->setStrStaTipo('0'); }
    if ($invalid === 'inactive') { UsuarioRN::$user->setStrSinAtivo('N'); }
    UsuarioRN::$fail = $invalid === 'user-read';
    rejected(static fn() => runJob('CPF'));
    assertRestored($before);
    check(IdentityTestScheduling::$events === [] && SessaoSEI::getInstance()->logins === [],
        'Invalid identity reached processing');
    echo 'PASS invalid-' . $invalid . "\n";
    $cases++;
}

$contact = new ContatoAPI();
$contact->setIdContato('999');
foreach (['human', 'technical', 'normal-technical', 'disabled-module', 'different-actor',
    'normal-mode-actor', 'generic-context'] as $scenario) {
    $before = resetFixtures();
    $blocked = static fn() => (new IdentityTestLitigioso())->alterarContato($contact);
    if ($scenario === 'human') {
        rejected($blocked);
    } elseif ($scenario === 'technical') {
        $session = SessaoSEI::getInstance();
        $session->enabled = false;
        $session->setNumIdUsuario(400);
        rejected($blocked);
        $session->enabled = $before[0];
        $session->simulated = $before[2];
    } elseif ($scenario === 'normal-technical') {
        SessaoSEI::getInstance()->normal['NumIdUsuario'] = 400;
        rejected($blocked);
        SessaoSEI::getInstance()->normal = $before[1];
    } elseif ($scenario === 'generic-context') {
        $userRN->executarComoUsuarioPeticionamento(static function () use ($blocked): void {
            check(!MdPetIntUsuarioRN::isExecutandoSuspensaoAutomatica(), 'Generic context authorizes hook');
            rejected($blocked);
        });
    } else {
        $userRN->executarSuspensaoAutomatica(static function () use ($scenario, $blocked): void {
            if ($scenario === 'disabled-module') {
                IdentityTestConfiguration::$petEnabled = false;
            } elseif ($scenario === 'normal-mode-actor') {
                SessaoSEI::getInstance()->setBolHabilitada(true);
            } else {
                SessaoSEI::getInstance()->setNumIdUsuario(401);
            }
            rejected($blocked);
        });
    }
    check(MdLitDadoInteressadoRN::$reads === 1, 'Existing Litigioso checks bypassed: ' . $scenario);
    assertRestored($before);
    echo 'PASS blocked-' . $scenario . "\n";
    $cases++;
}

foreach (['same-unit', 'no-lit-resource'] as $scenario) {
    resetFixtures();
    if ($scenario === 'same-unit') {
        SessaoSEI::getInstance()->normal['NumIdUnidadeAtual'] = 91;
    } else {
        SessaoSEI::getInstance()->permission = false;
    }
    check((new IdentityTestLitigioso())->alterarContato($contact) === null, 'Existing exception changed');
    echo 'PASS existing-rule-' . $scenario . "\n";
    $cases++;
}

foreach ([true, false] as $enabled) {
    $before = resetFixtures($enabled);
    check(generateDocument()->getIdDocumento() === '900', 'Document result changed');
    assertRestored($before);
    echo 'PASS standalone-document-' . (int) $enabled . "\n";
    $cases++;
}

$before = resetFixtures();
$result = $userRN->executarSuspensaoAutomatica(function () use ($userRN): string {
    $userRN->executarSuspensaoAutomatica(static function (): void {
        assertActor('nested-job');
        check(MdPetIntUsuarioRN::isExecutandoSuspensaoAutomatica(), 'Nested scope inactive');
    });
    check(MdPetIntUsuarioRN::isExecutandoSuspensaoAutomatica(), 'Nested scope erased outer scope');
    return 'preserved-result';
});
check($result === 'preserved-result' && InfraParametro::$reads === 1, 'Nested execution lost return/cache');
assertRestored($before);
echo "PASS nested-context\n";
$cases++;

$before = resetFixtures();
$caught = false;
try {
    $userRN->executarSuspensaoAutomatica(static function (): void {
        throw new TypeError('Injected PHP error');
    });
} catch (TypeError $e) {
    $caught = true;
}
check($caught, 'PHP error swallowed');
assertRestored($before);
echo "PASS php-error-restoration\n";
$cases++;

$before = resetFixtures();
$userRN->executarSuspensaoAutomatica(static function (): void {
    $link = new MdPetVincRepresentantDTO();
    $link->setNumIdContato(999);
    $method = new ReflectionMethod(MdPetVincRepresentantRN::class, '_suspenderAutoRepresentacao');
    $method->setAccessible(true);
    $method->invoke(new IdentityTestRepresentation(), $link);
    check(IdentityTestRepresentation::$updated->getStrStaEstado() === MdPetVincRepresentantRN::$RP_SUSPENSO,
        'Link not suspended');
    check(ContatoRN::$updated !== null && !ContatoRN::$updated->isSetStrSinAtivo(), 'Contact inactivated');
    check(str_contains(ContatoRN::$updated->getStrObservacao(), 'previous-observation'),
        'Previous observation lost');
    check(count(UsuarioRN::$deactivated) === 1 && UsuarioRN::$deactivated[0]->getNumIdUsuario() === 401,
        'External user not deactivated');
    check(MdLitDadoInteressadoRN::$reads === 0, 'Litigioso blocked the real suspension method');
});
assertRestored($before);
echo "PASS real-authorrepresentation-observation-external-user\n";
$cases++;

echo 'PASS ' . $cases . " identity scenarios\n";
