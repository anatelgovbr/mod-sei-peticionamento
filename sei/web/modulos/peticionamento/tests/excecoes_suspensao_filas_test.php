<?php
/**
 * CLI regression of real queue/whitelist/link RNs with isolated persistence.
 * Run: php excecoes_suspensao_filas_test.php
 */
if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

require_once dirname(__DIR__, 3) . '/SEI.php';
require_once dirname(__DIR__) . '/rn/MdPetAgendamentoAutomaticoRN.php';
require_once dirname(__DIR__) . '/rn/MdPetWhitelistRN.php';
require_once dirname(__DIR__) . '/rn/MdPetVinculoRN.php';

function check(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

class SessaoSEI
{
    public static array $permissions = [];

    public static function getInstance(): self
    {
        static $instance;
        return $instance ??= new self();
    }

    public function validarPermissao(string $resource): void
    {
        self::$permissions[] = $resource;
        if ($resource !== 'md_pet_vinculacao_listar') {
            throw new InfraException('Administrative resource denied');
        }
    }

    public function validarAuditarPermissao(string $resource, string $method, InfraDTO $dto): void
    {
        $this->validarPermissao($resource);
    }
}

class ContatoRN
{
    public static $TN_PESSOA_FISICA = 'F';
    public static $TN_PESSOA_JURIDICA = 'J';
}

class QueueTestBank extends BancoSEI
{
    public function getIdBanco() { return 'suspension-queue-unit-test'; }
    public function getIdConexao() { return $this->getIdBanco(); }
    public function isBolProcessandoTransacao() { return false; }
    public function abrirConexao() {}
    public function fecharConexao() {}
}

class MdPetWhitelistBD
{
    public static array $rows = [];
    public static array $queries = [];
    public static bool $fail = false;

    public function __construct(InfraIBanco $bank)
    {
        check($bank instanceof QueueTestBank, 'Whitelist bank is not isolated');
    }

    public function listar(MdPetWhitelistDTO $filter): array
    {
        self::$queries[] = clone $filter;
        check($filter->getStrStaFinalidade() === 'S' && $filter->getStrStaTipoObjeto() === 'C',
            'Whitelist context widened');
        if (self::$fail) {
            throw new InfraException('Injected whitelist read failure');
        }
        return array_map(static fn(MdPetWhitelistDTO $row): MdPetWhitelistDTO => clone $row,
            array_values(array_filter(self::$rows, static fn(MdPetWhitelistDTO $row): bool =>
                $row->getStrStaFinalidade() === 'S' && $row->getStrStaTipoObjeto() === 'C')));
    }
}

class MdPetContatoRN
{
    public static array $contacts = [];
    public static array $queries = [];
    public static bool $fail = false;

    public function listarRN0325(ContatoDTO $filter): array
    {
        self::$queries[] = clone $filter;
        check(!$filter->isBolExclusaoLogica(), 'Inactive contacts excluded');
        check($filter->getOperador('IdContato') === InfraDTO::$OPER_IN, 'Contacts not loaded in batch');
        if (self::$fail) {
            throw new InfraException('Injected contact read failure');
        }
        return array_values(array_filter(self::$contacts, static fn(ContatoDTO $row): bool =>
            in_array($row->getNumIdContato(), $filter->getNumIdContato(), true)));
    }
}

class MdPetVinculoBD
{
    public static array $rows = [];
    public static array $queries = [];
    public static bool $fail = false;

    public function __construct(InfraIBanco $bank)
    {
        check($bank instanceof QueueTestBank, 'Link bank is not isolated');
    }

    public function listar(MdPetVinculoDTO $filter): array
    {
        self::$queries[] = clone $filter;
        if (self::$fail) {
            throw new InfraException('Injected queue read failure');
        }
        $physical = $filter->getStrTpVinculo() === 'F';
        $attribute = $physical ? 'CpfContatoRepresentante' : 'CNPJ';
        $getter = 'getStr' . $attribute;
        $isSet = 'isSetStr' . $attribute;
        check($filter->getOrdDthDataUltimaConsultaRFB() === InfraDTO::$TIPO_ORDENACAO_ASC, 'Queue order changed');
        check(!$physical || ($filter->getDblIdProtocolo() === null
            && $filter->getOperador('IdProtocolo') === InfraDTO::$OPER_DIFERENTE), 'CPF process filter changed');
        check($physical || !$filter->isSetDblIdProtocolo(), 'Unexpected CNPJ process filter');
        check($physical || ($filter->getStrStaEstado() === 'A' && $filter->getDistinct()), 'CNPJ active representative filter changed');
        $excluded = $filter->$isSet() ? $filter->$getter() : [];
        if ($filter->$isSet()) {
            check($filter->getOperador($attribute) === InfraDTO::$OPER_NOT_IN
                && is_array($excluded) && count($excluded) > 0, 'Invalid document exclusion');
        }
        $rows = array_values(array_filter(self::$rows, static function (MdPetVinculoDTO $row) use (
            $filter, $physical, $getter, $excluded
        ): bool {
            if ($row->getStrTpVinculo() !== $filter->getStrTpVinculo()
                || ($physical && $row->getDblIdProtocolo() === null)) {
                return false;
            }
            $document = $row->$getter();
            foreach ($excluded as $blocked) {
                if ($physical ? (string) (int) $document === (string) (int) $blocked : $document === $blocked) {
                    return false;
                }
            }
            return $excluded === [] || $document !== null;
        }));
        usort($rows, static fn(MdPetVinculoDTO $a, MdPetVinculoDTO $b): int =>
            strcmp($a->getDthDataUltimaConsultaRFB(), $b->getDthDataUltimaConsultaRFB()));
        return array_slice($rows, 0, $filter->getNumMaxRegistrosRetorno());
    }
}

function association(int $object, string $purpose = 'S', string $type = 'C'): MdPetWhitelistDTO
{
    $dto = new MdPetWhitelistDTO();
    $dto->setNumIdMdPetWhitelist(count(MdPetWhitelistBD::$rows) + 1);
    $dto->setDblIdObjeto($object);
    $dto->setStrStaFinalidade($purpose);
    $dto->setStrStaTipoObjeto($type);
    return $dto;
}

function resetFixtures(): void
{
    MdPetWhitelistBD::$rows = MdPetWhitelistBD::$queries = [];
    MdPetContatoRN::$contacts = MdPetContatoRN::$queries = [];
    MdPetVinculoBD::$rows = MdPetVinculoBD::$queries = [];
    SessaoSEI::$permissions = [];
    MdPetWhitelistBD::$fail = MdPetContatoRN::$fail = MdPetVinculoBD::$fail = false;
    foreach ([[10, 'F', '52998224725', 'S'], [11, 'F', '52998224725', 'N'],
        [12, 'J', '11222333000181', 'S'], [13, 'J', '12abc34501de35', 'N'],
        [14, 'F', '1234567890', 'S'], [15, 'J', '00111222000191', 'S'],
        [18, 'F', '11144477735', 'S'], [19, 'F', '12345678909', 'S'],
        [20, 'J', '33444555000166', 'S']] as [$id, $nature, $document, $active]) {
        $dto = new ContatoDTO();
        $dto->setNumIdContato($id);
        $dto->setStrStaNatureza($nature);
        $dto->setDblCpf($nature === 'F' ? $document : '52998224725');
        $dto->setStrCnpj($nature === 'J' ? $document : '11222333000181');
        $dto->setStrSinAtivo($active);
        $dto->setStrNome('Synthetic contact ' . $id);
        $dto->setStrNomeTipoContato('Synthetic type');
        MdPetContatoRN::$contacts[] = $dto;
    }
    foreach (['F' => ['52998224725', '52998224725', '01234567890', '11144477735', '12345678909', null],
        'J' => [null, '33444555000166', '00111222000191', '12ABC34501DE35', '11222333000181', '11222333000181']]
        as $nature => $documents) {
        foreach ($documents as $position => $document) {
            $dto = new MdPetVinculoDTO();
            $dto->setNumIdMdPetVinculo(count(MdPetVinculoBD::$rows) + 1);
            $dto->setNumIdContato(500 + $position);
            $dto->setNumIdContatoRepresentante(600 + $position);
            $dto->setStrTpVinculo($nature);
            $dto->setDblIdProtocolo(1000 + $position);
            $dto->setDthDataUltimaConsultaRFB(sprintf('2026-01-%02d 00:00:00', $position + 1));
            $setter = $nature === 'F' ? 'setStrCpfContatoRepresentante' : 'setStrCNPJ';
            $dto->$setter($document);
            MdPetVinculoBD::$rows[] = $dto;
        }
    }
    $withoutProcess = clone MdPetVinculoBD::$rows[0];
    $withoutProcess->setDblIdProtocolo(null);
    $withoutProcess->setDthDataUltimaConsultaRFB('2025-01-01 00:00:00');
    $withoutProcess->setStrCpfContatoRepresentante('99999999999');
    MdPetVinculoBD::$rows[] = $withoutProcess;
}

function queue(string $nature, int $limit = 2): array
{
    $method = new ReflectionMethod(MdPetAgendamentoAutomaticoRN::class,
        $nature === 'F' ? 'recuperarFilaConsultaCpf' : 'recuperarFilaConsultaCnpj');
    $method->setAccessible(true);
    return $method->invoke(new MdPetAgendamentoAutomaticoRN(), $limit);
}

function rejected(callable $operation): void
{
    try {
        $operation();
    } catch (InfraException $e) {
        return;
    }
    throw new RuntimeException('Expected read failure or authorization refusal');
}

foreach (['SessaoSEI', 'ContatoRN', 'MdPetContatoRN', 'MdPetWhitelistBD', 'MdPetVinculoBD'] as $class) {
    check((new ReflectionClass($class))->getFileName() === __FILE__, 'Double not loaded: ' . $class);
}
$bank = new QueueTestBank();
$instance = new ReflectionProperty(BancoSEI::class, 'instance');
$instance->setAccessible(true);
$instance->setValue(null, $bank);
check(BancoSEI::getInstance() === $bank, 'Singleton bank is not isolated');

$cases = [
    'exclude-f' => [[10], 'F', ['01234567890', '11144477735']],
    'exclude-j' => [[12], 'J', ['33444555000166', '00111222000191']],
    'baseline-f' => [[], 'F', ['52998224725', '52998224725']],
    'baseline-j' => [[], 'J', ['33444555000166']],
    'inactive-f' => [[11], 'F', ['01234567890', '11144477735']],
    'inactive-j' => [[13], 'J', ['33444555000166', '00111222000191', '11222333000181'], 3],
    'leading-zero-f' => [[14], 'F', ['52998224725', '52998224725', '11144477735'], 3],
    'leading-zero-j' => [[15], 'J', ['33444555000166', '12ABC34501DE35', '11222333000181'], 4],
    'duplicates-f' => [[10, 11], 'F', ['01234567890', '11144477735']],
    'other-nature-f' => [[12], 'F', ['52998224725', '52998224725']],
    'other-nature-j' => [[10], 'J', ['33444555000166']],
    'mixed-f' => [[10, 12, 13, 14], 'F', ['11144477735', '12345678909']],
    'mixed-j' => [[10, 12, 13, 14], 'J', ['33444555000166', '00111222000191']],
    'all-f' => [[10, 14, 18, 19], 'F', []],
    'all-j' => [[12, 13, 15, 20], 'J', []],
    'null-f' => [[], 'F', ['52998224725', '52998224725', '01234567890', '11144477735', '12345678909'], 6],
    'null-j' => [[], 'J', ['33444555000166', '00111222000191', '12ABC34501DE35', '11222333000181'], 6],
];
foreach ($cases as $name => $case) {
    resetFixtures();
    foreach ($case[0] as $id) {
        MdPetWhitelistBD::$rows[] = association($id);
    }
    check(queue($case[1], $case[3] ?? 2) === $case[2], 'Wrong queue: ' . $name);
    check(count(MdPetWhitelistBD::$queries) === 1, 'Expected one whitelist query: ' . $name);
    check(count(MdPetContatoRN::$queries) === (int) ($case[0] !== []), 'Unexpected contact query count: ' . $name);
    check(SessaoSEI::$permissions === ['md_pet_vinculacao_listar'], 'Unexpected administrative authorization');
    if ($name === 'duplicates-f') {
        check(MdPetVinculoBD::$queries[0]->getStrCpfContatoRepresentante() === ['52998224725'],
            'Document exclusions not deduplicated');
    }
    echo 'PASS queue ' . $name . "\n";
}

resetFixtures();
MdPetWhitelistBD::$rows = [association(10, 'I', 'P'), association(12, 'V', 'P'), association(10, 'S', 'P')];
check(queue('F') === $cases['baseline-f'][2] && queue('J') === $cases['baseline-j'][2], 'Other context affected queues');
check(MdPetContatoRN::$queries === [], 'Other context resolved contacts');
echo "PASS queue context-isolation\n";

resetFixtures();
MdPetWhitelistBD::$rows = [association(10), association(11)];
check(queue('F') === $cases['exclude-f'][2], 'Initial exception ignored');
array_shift(MdPetWhitelistBD::$rows);
check(queue('F') === $cases['exclude-f'][2], 'Remaining shared-document exception ignored');
MdPetWhitelistBD::$rows = [];
check(queue('F') === $cases['baseline-f'][2], 'Removed exception cached');
echo "PASS queue removal-refresh\n";

foreach (['MdPetWhitelistBD', 'MdPetContatoRN', 'MdPetVinculoBD'] as $class) {
    resetFixtures();
    MdPetWhitelistBD::$rows = [association(10)];
    $class::$fail = true;
    rejected(static fn(): array => queue('F'));
    check(count(MdPetVinculoBD::$queries) === (int) ($class === 'MdPetVinculoBD'), 'Queue continued after read failure');
    echo 'PASS queue failure-' . $class . "\n";
}

resetFixtures();
rejected(static fn(): array => (new MdPetWhitelistRN())->listarExcecoesSuspensao(new MdPetWhitelistDTO()));
check(MdPetWhitelistBD::$queries === [], 'Administrative read performed before authorization');
echo "PASS queue administrative-permission-preserved\n";

resetFixtures();
$invalid = association(10, 'V', 'P');
rejected(static fn(): array => (new MdPetWhitelistRN())->recuperarExcecoesSuspensaoInterno($invalid));
check(MdPetWhitelistBD::$queries === [], 'Invalid internal context read');
echo "PASS queue invalid-internal-context\n";
echo 'PASS ' . (count($cases) + 7) . " queue scenarios\n";
