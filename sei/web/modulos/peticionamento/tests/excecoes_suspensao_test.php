<?php
/**
 * CLI regression tests with real DTOs/InfraRN and isolated persistence doubles.
 * Run: php excecoes_suspensao_test.php
 */
if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

require_once dirname(__DIR__, 3) . '/SEI.php';
require_once dirname(__DIR__) . '/dto/MdPetWhitelistDTO.php';
require_once dirname(__DIR__) . '/rn/MdPetWhitelistRN.php';
require_once dirname(__DIR__) . '/rn/MdPetVinculoRN.php';

class SessaoSEI
{
    public static array $denied = [];
    public static array $audits = [];
    public static string $unitOrganization = 'TESTE';
    public static string $systemOrganization = 'TESTE';

    public static function getInstance(): self
    {
        static $instance;
        return $instance ??= new self();
    }

    public function validarPermissao(string $resource): void
    {
        if (in_array($resource, self::$denied, true)) {
            throw new InfraException('Permission denied: ' . $resource);
        }
    }

    public function validarAuditarPermissao(string $resource, string $method, InfraDTO $dto): void
    {
        $this->validarPermissao($resource);
        self::$audits[] = [$resource, $method, clone $dto];
    }

    public function getStrSiglaOrgaoUnidadeAtual(): string
    {
        return self::$unitOrganization;
    }

    public function getStrSiglaOrgaoSistema(): string
    {
        return self::$systemOrganization;
    }

    public function getStrSiglaOrgaoUsuario(): string
    {
        return 'USUARIO';
    }
}

class InfraParametro
{
    public static array $values = [
        'TESTE_ID_TIPO_CONTATO_USUARIOS_EXTERNOS' => '77',
        'OUTRO_ID_TIPO_CONTATO_USUARIOS_EXTERNOS' => '99',
        'USUARIO_ID_TIPO_CONTATO_USUARIOS_EXTERNOS' => '88',
    ];
    public static array $queries = [];

    public function __construct(InfraIBanco $bank)
    {
        check($bank instanceof SuspensionTestBank, 'Parameter bank is not isolated');
    }

    public function getValor(string $name, bool $required = true, $default = null)
    {
        self::$queries[] = $name;
        if (!isset(self::$values[$name])) {
            check($required, 'Missing parameter silently ignored');
            throw new InfraException('Missing parameter: ' . $name);
        }
        return self::$values[$name];
    }
}

class ContatoRN
{
    public static $TN_PESSOA_FISICA = 'F';
    public static $TN_PESSOA_JURIDICA = 'J';
}

class MdPetContatoRN
{
    public static array $contacts = [];
    public static array $queries = [];

    public function listarRN0325(ContatoDTO $filter): array
    {
        self::$queries[] = clone $filter;
        $contacts = array_values(array_filter(self::$contacts, static function (ContatoDTO $contact) use ($filter): bool {
            if ($filter->isSetStrStaNatureza() && $filter->getStrStaNatureza() !== $contact->getStrStaNatureza()) {
                return false;
            }
            if ($filter->isSetDblCpf() && (string) $filter->getDblCpf() !== (string) $contact->getDblCpf()) {
                return false;
            }
            if ($filter->isSetStrCnpj() && $filter->getStrCnpj() !== $contact->getStrCnpj()) {
                return false;
            }
            if ($filter->isSetNumIdTipoContato() && $filter->getNumIdTipoContato() !== $contact->getNumIdTipoContato()) {
                return false;
            }
            return !$filter->isSetNumIdContato()
                || in_array($contact->getNumIdContato(), (array) $filter->getNumIdContato(), true);
        }));
        usort($contacts, static function (ContatoDTO $left, ContatoDTO $right) use ($filter): int {
            foreach ($filter->getArrOrdenacao() as [$attribute, $direction]) {
                check($direction === InfraDTO::$TIPO_ORDENACAO_ASC, 'Unexpected contact ordering');
                $comparison = $left->get($attribute) <=> $right->get($attribute);
                if ($comparison !== 0) {
                    return $comparison;
                }
            }
            return 0;
        });
        return $contacts;
    }
}

class MdPetVinculoBD
{
    public static array $rows = [];
    public static array $queries = [];
    public static bool $fail = false;

    public function __construct(InfraIBanco $bank)
    {
        check($bank instanceof SuspensionTestBank, 'Link bank is not isolated');
    }

    public function listar(MdPetVinculoDTO $filter): array
    {
        self::$queries[] = clone $filter;
        if (self::$fail) {
            throw new InfraException('Injected link query failure');
        }
        check($filter->isSetNumIdContato(), 'Unbounded link query');
        check($filter->getOperador('IdContato') === InfraDTO::$OPER_IN, 'Link query is not batched');
        check($filter->getDistinct(), 'Link query is not distinct');
        check(!$filter->isSetNumIdContatoRepresentante() && !$filter->isSetStrStaEstado()
            && !$filter->isSetStrSinValidado() && !$filter->isSetDblIdProtocolo(), 'Unexpected link restriction');
        return array_values(array_filter(self::$rows, static fn(MdPetVinculoDTO $row): bool =>
            in_array($row->getNumIdContato(), $filter->getNumIdContato(), true)));
    }
}

class MdPetWhitelistBD
{
    public static array $rows = [];
    public static int $failAt = 0;
    public static int $writes = 0;

    public function __construct(InfraIBanco $bank)
    {
    }

    public function listar(MdPetWhitelistDTO $filter): array
    {
        return array_values(array_filter(self::$rows, static function (MdPetWhitelistDTO $row) use ($filter): bool {
            return $row->getStrStaFinalidade() === $filter->getStrStaFinalidade()
                && $row->getStrStaTipoObjeto() === $filter->getStrStaTipoObjeto()
                && (!$filter->isSetDblIdObjeto()
                    || in_array($row->getDblIdObjeto(), (array) $filter->getDblIdObjeto(), true))
                && (!$filter->isSetNumIdMdPetWhitelist()
                    || $row->getNumIdMdPetWhitelist() === $filter->getNumIdMdPetWhitelist());
        }));
    }

    public function contar(MdPetWhitelistDTO $filter): int
    {
        return count($this->listar($filter));
    }

    public function consultar(MdPetWhitelistDTO $filter): ?MdPetWhitelistDTO
    {
        return $this->listar($filter)[0] ?? null;
    }

    public function cadastrar(MdPetWhitelistDTO $dto): MdPetWhitelistDTO
    {
        self::$writes++;
        $dto->setNumIdMdPetWhitelist(count(self::$rows) + 1);
        self::$rows[$dto->getNumIdMdPetWhitelist()] = clone $dto;
        if (self::$failAt === self::$writes) {
            throw new InfraException('Injected persistence failure after write');
        }
        return $dto;
    }

    public function excluir(MdPetWhitelistDTO $dto): void
    {
        unset(self::$rows[$dto->getNumIdMdPetWhitelist()]);
    }
}

class SuspensionTestBank extends BancoSEI
{
    public array $events = [];
    private array $snapshot = [];
    private bool $transaction = false;

    public function getIdBanco() { return 'suspension-unit-test'; }
    public function getIdConexao() { return $this->getIdBanco(); }
    public function isBolProcessandoTransacao() { return $this->transaction; }
    public function abrirConexao() { $this->events[] = 'connect'; }
    public function fecharConexao() { $this->events[] = 'close'; }
    public function abrirTransacao()
    {
        $this->snapshot = MdPetWhitelistBD::$rows;
        $this->transaction = true;
        $this->events[] = 'begin';
    }
    public function confirmarTransacao()
    {
        $this->transaction = false;
        $this->events[] = 'commit';
    }
    public function cancelarTransacao()
    {
        MdPetWhitelistBD::$rows = $this->snapshot;
        $this->transaction = false;
        $this->events[] = 'rollback';
    }
}

class SuspensionTestRN extends MdPetWhitelistRN
{
    public SuspensionTestBank $bank;

    public function __construct()
    {
        $this->bank = new SuspensionTestBank();
    }

    protected function inicializarObjInfraIBanco()
    {
        return $this->bank;
    }
}

function check(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

function rejected(callable $operation, string $message, ?bool $validation = null): void
{
    try {
        $operation();
    } catch (InfraException $e) {
        if ($validation !== null) {
            check($e->contemValidacoes() === $validation, 'Wrong exception classification: ' . $message . ': ' . $e);
            if ($validation) {
                check($e->getObjException() === null && $e->getStrTrace() === '', 'Validation contains technical details: ' . $message);
            }
        }
        return;
    }
    throw new RuntimeException('Not rejected: ' . $message);
}

function criteria(string $nature = 'F', string $document = '529.982.247-25', ?array $ids = null): MdPetWhitelistDTO
{
    $dto = new MdPetWhitelistDTO();
    $dto->setStrNaturezaContato($nature);
    $dto->setStrDocumentoContato($document);
    if ($ids !== null) {
        $dto->setArrIdObjetos($ids);
    }
    return $dto;
}

function association(int $id, string $purpose, string $type, int $object): MdPetWhitelistDTO
{
    $dto = new MdPetWhitelistDTO();
    $dto->setNumIdMdPetWhitelist($id);
    $dto->setStrStaFinalidade($purpose);
    $dto->setStrStaTipoObjeto($type);
    $dto->setDblIdObjeto($object);
    return $dto;
}

foreach (['SessaoSEI', 'ContatoRN', 'MdPetContatoRN', 'MdPetWhitelistBD', 'MdPetVinculoBD', 'InfraParametro'] as $class) {
    check((new ReflectionClass($class))->getFileName() === __FILE__, 'Persistence double not loaded: ' . $class);
}

$rn = new SuspensionTestRN();
$bankInstance = new ReflectionProperty(BancoSEI::class, 'instance');
$bankInstance->setAccessible(true);
$bankInstance->setValue(null, $rn->bank);
check(BancoSEI::getInstance() === $rn->bank, 'RN singleton bank is not isolated');
foreach ([[10, 'F', 'S'], [11, 'F', 'N'], [12, 'J', 'S'], [13, 'F', 'S'], [14, 'F', 'S'], [15, 'J', 'N'],
    [16, 'F', 'S'], [17, 'F', 'S'], [18, 'F', 'S'], [19, 'J', 'S'], [20, 'J', 'N'],
    [21, 'J', 'S'], [22, 'F', 'N']] as [$id, $nature, $active]) {
    $contact = new ContatoDTO();
    $contact->setNumIdContato($id);
    $contact->setStrStaNatureza($nature);
    $contact->setStrSinAtivo($active);
    $contact->setStrNome('Synthetic <contact> ' . $id);
    $contact->setDblCpf($id === 13 ? '11144477735' : ($id === 14 ? '01234567890' : '52998224725'));
    $contact->setStrCnpj($id === 15 ? '12ABC34501DE35' : '11222333000181');
    $contact->setStrNomeTipoContato('Test');
    $contact->setNumIdTipoContato($id === 17 ? 88 : ($id === 18 ? 99 : 77));
    MdPetContatoRN::$contacts[] = $contact;
}
foreach ([10, 10, 11, 12, 13, 14, 15, 17, 18, 20] as $index => $id) {
    $link = new MdPetVinculoDTO();
    $link->setNumIdMdPetVinculo($index + 1);
    $link->setNumIdContato($id);
    $link->setNumIdContatoRepresentante([16, 19, 21, 22][$index % 4]);
    MdPetVinculoBD::$rows[] = $link;
}

$count = 0;
$tests = [
    'only principal linked IDs and external contact type, in one batch' => static function () use ($rn): void {
        $beforeContacts = count(MdPetContatoRN::$queries);
        $beforeLinks = count(MdPetVinculoBD::$queries);
        $contacts = $rn->pesquisarContatosSuspensao(criteria());
        check(InfraArray::converterArrInfraDTO($contacts, 'IdContato') === [10, 11], 'CPF eligibility: wrong type, unlinked contact or representative included');
        check(MdPetContatoRN::$queries[$beforeContacts]->getArrOrdenacao() === [
            ['Nome', InfraDTO::$TIPO_ORDENACAO_ASC], ['IdContato', InfraDTO::$TIPO_ORDENACAO_ASC],
        ], 'Contact order must be name followed by ID');
        check(count(MdPetContatoRN::$queries) === $beforeContacts + 1, 'Contact N+1');
        check(count(MdPetVinculoBD::$queries) === $beforeLinks + 1, 'Link N+1');
        check(MdPetVinculoBD::$queries[$beforeLinks]->getNumIdContato() === [10, 11, 16, 22], 'Wrong CPF candidate IDs');
        $contacts = $rn->pesquisarContatosSuspensao(criteria('J', '11222333000181'));
        check(InfraArray::converterArrInfraDTO($contacts, 'IdContato') === [12, 20], 'CNPJ eligibility: unlinked contact or representative included');
        check($contacts[1]->getStrSinAtivo() === 'N', 'Inactive linked CNPJ excluded');
        check(count(MdPetVinculoBD::$queries) === $beforeLinks + 2, 'CNPJ link N+1');
    },
    'CPF uses system organization, not current unit or authenticated user organization' => static function () use ($rn): void {
        $rows = MdPetWhitelistBD::$rows;
        SessaoSEI::$systemOrganization = 'OUTRO';
        try {
            $contacts = $rn->pesquisarContatosSuspensao(criteria());
            check(InfraArray::converterArrInfraDTO($contacts, 'IdContato') === [18], 'Wrong organization contact type');
            check(end(InfraParametro::$queries) === 'OUTRO_ID_TIPO_CONTATO_USUARIOS_EXTERNOS', 'Wrong parameter key');
            check($rn->cadastrarExcecoesSuspensao(criteria()) === 1, 'Automatic insertion with system organization');
            check(MdPetWhitelistBD::$rows[1]->getDblIdObjeto() === 18, 'Automatic insertion used wrong organization contact type');
        } finally {
            SessaoSEI::$systemOrganization = 'TESTE';
            MdPetWhitelistBD::$rows = $rows;
        }
    },
    'missing or invalid external type never broadens CPF search' => static function () use ($rn): void {
        $before = count(MdPetContatoRN::$queries);
        foreach ([null, '', '0', '-1', 'abc', '77x', '1.5', '9223372036854775808'] as $value) {
            InfraParametro::$values['TESTE_ID_TIPO_CONTATO_USUARIOS_EXTERNOS'] = $value;
            rejected(static fn() => $rn->pesquisarContatosSuspensao(criteria()), 'Invalid external type', false);
            rejected(static fn() => $rn->cadastrarExcecoesSuspensao(criteria()), 'Invalid external type on automatic insertion', false);
        }
        InfraParametro::$values['TESTE_ID_TIPO_CONTATO_USUARIOS_EXTERNOS'] = '77';
        SessaoSEI::$systemOrganization = '';
        rejected(static fn() => $rn->pesquisarContatosSuspensao(criteria()), 'Missing organization', false);
        rejected(static fn() => $rn->cadastrarExcecoesSuspensao(criteria()), 'Missing organization on automatic insertion', false);
        SessaoSEI::$systemOrganization = 'TESTE';
        check(count(MdPetContatoRN::$queries) === $before, 'Invalid parameter reached contact query');
        $before = count(InfraParametro::$queries);
        $rn->pesquisarContatosSuspensao(criteria('J', '11222333000181'));
        check(count(InfraParametro::$queries) === $before, 'CNPJ depends on CPF type parameter');
    },
    'no candidates skips link query and no links yields no matches' => static function () use ($rn): void {
        $before = count(MdPetVinculoBD::$queries);
        check($rn->pesquisarContatosSuspensao(criteria('F', '12345678909')) === [], 'No candidates');
        rejected(static fn() => $rn->cadastrarExcecoesSuspensao(criteria('F', '12345678909')), 'No eligible CPF contact', true);
        check(count(MdPetVinculoBD::$queries) === $before, 'Empty IN link query');
        $links = MdPetVinculoBD::$rows;
        MdPetVinculoBD::$rows = [];
        try {
            check($rn->pesquisarContatosSuspensao(criteria()) === [], 'Unlinked CPF returned');
            check($rn->pesquisarContatosSuspensao(criteria('J', '11222333000181')) === [], 'Unlinked CNPJ returned');
            rejected(static fn() => $rn->cadastrarExcecoesSuspensao(criteria()), 'Unlinked CPF insertion', true);
            rejected(static fn() => $rn->cadastrarExcecoesSuspensao(criteria('J', '11222333000181')), 'Unlinked CNPJ insertion', true);
            check(MdPetWhitelistBD::$rows === [], 'Missing links persisted an exception');
        } finally {
            MdPetVinculoBD::$rows = $links;
        }
    },
    'link query failure is technical, not an empty result' => static function () use ($rn): void {
        MdPetVinculoBD::$fail = true;
        try {
            rejected(static fn() => $rn->pesquisarContatosSuspensao(criteria()), 'Link query failure', false);
            rejected(static fn() => $rn->cadastrarExcecoesSuspensao(criteria()), 'Link failure on insert', false);
            check(MdPetWhitelistBD::$rows === [], 'Link failure persisted');
        } finally {
            MdPetVinculoBD::$fail = false;
        }
    },
    'exact document, active/inactive and masks' => static function () use ($rn): void {
        check(count($rn->pesquisarContatosSuspensao(criteria())) === 2, 'CPF mask');
        check(count($rn->pesquisarContatosSuspensao(criteria('F', '52998224725'))) === 2, 'CPF digits');
        check(count($rn->pesquisarContatosSuspensao(criteria('J', '11.222.333/0001-81'))) === 2, 'CNPJ mask');
        check(count($rn->pesquisarContatosSuspensao(criteria('J', '11222333000181'))) === 2, 'CNPJ digits');
        check(count($rn->pesquisarContatosSuspensao(criteria('J', '12.abc.345/01de-35'))) === 1, 'Alphanumeric CNPJ');
        $leading = $rn->pesquisarContatosSuspensao(criteria('F', '012.345.678-90'));
        check(count($leading) === 1 && InfraUtil::formatarCpf($leading[0]->getDblCpf()) === '012.345.678-90', 'Leading zero lost');
        check(count($rn->pesquisarContatosSuspensao(criteria('F', '12345678909'))) === 0, 'Not found');
        check(!MdPetContatoRN::$queries[0]->isBolExclusaoLogica(), 'Inactive contacts excluded');
    },
    'invalid documents, nature and identifier shapes' => static function () use ($rn): void {
        foreach (['', '529982', '00000000000', '52998224724', '<52998224725>', '529 982 247 25'] as $doc) {
            rejected(static fn() => $rn->pesquisarContatosSuspensao(criteria('F', $doc)), 'Invalid CPF', true);
            rejected(static fn() => $rn->cadastrarExcecoesSuspensao(criteria('F', $doc)), 'Invalid CPF on automatic insertion', true);
        }
        foreach (['00000000000000', '11222333000180', '11222333', '!!11222333000181'] as $doc) {
            rejected(static fn() => $rn->pesquisarContatosSuspensao(criteria('J', $doc)), 'Invalid CNPJ', true);
            rejected(static fn() => $rn->cadastrarExcecoesSuspensao(criteria('J', $doc)), 'Invalid CNPJ on automatic insertion', true);
        }
        rejected(static fn() => $rn->pesquisarContatosSuspensao(criteria('X')), 'Invalid nature', true);
        rejected(static fn() => $rn->pesquisarContatosSuspensao(new MdPetWhitelistDTO()), 'Missing criteria', true);
        foreach (['NaturezaContato', 'DocumentoContato'] as $attribute) {
            rejected(static function () use ($rn, $attribute): void {
                $dto = criteria();
                $setter = 'setStr' . $attribute;
                $dto->$setter([]);
                $rn->pesquisarContatosSuspensao($dto);
            }, 'Array criterion', true);
        }
        foreach ([[], [0], [-1], ['1e1'], ['10x'], [[]], [true], ['9223372036854775808']] as $ids) {
            rejected(static fn() => $rn->cadastrarExcecoesSuspensao(criteria('F', '52998224725', $ids)), 'Invalid ID', true);
        }
        $dto = new MdPetWhitelistDTO();
        rejected(static fn() => $rn->cadastrarExcecoesSuspensao($dto), 'Missing document criteria', true);
        rejected(static fn() => $rn->excluirExcecaoSuspensao($dto), 'Missing exception ID', true);
    },
    'permissions are required independently at RN boundary' => static function () use ($rn): void {
        foreach (['listar', 'selecionar', 'cadastrar'] as $action) {
            SessaoSEI::$denied = ['md_pet_exc_suspensao_auto_' . $action];
            $before = count(MdPetContatoRN::$queries);
            rejected(static fn() => $rn->cadastrarExcecoesSuspensao(criteria()), $action, false);
            check(count(MdPetContatoRN::$queries) === $before, 'Denied insertion reached contact lookup');
        }
        SessaoSEI::$denied = ['md_pet_exc_suspensao_auto_listar'];
        rejected(static fn() => $rn->listarExcecoesSuspensao(new MdPetWhitelistDTO()), 'List permission');
        SessaoSEI::$denied = ['md_pet_exc_suspensao_auto_selecionar'];
        rejected(static fn() => $rn->pesquisarContatosSuspensao(criteria()), 'Search permission');
        SessaoSEI::$denied = [];
        check(MdPetWhitelistBD::$rows === [], 'Denied operation persisted');
    },
    'client-selected IDs and other contexts are rejected' => static function () use ($rn): void {
        foreach ([16, 17, 18, 22] as $id) {
            rejected(static fn() => $rn->cadastrarExcecoesSuspensao(criteria('F', '52998224725', [10, $id])), 'Ineligible CPF selection', true);
        }
        foreach ([19, 21] as $id) {
            rejected(static fn() => $rn->cadastrarExcecoesSuspensao(criteria('J', '11222333000181', [12, $id])), 'Ineligible CNPJ selection', true);
        }
        rejected(static fn() => $rn->cadastrarExcecoesSuspensao(criteria('F', '52998224725', [10, 13])), 'Wrong document ID', true);
        rejected(static fn() => $rn->cadastrarExcecoesSuspensao(criteria('F', '52998224725', [12])), 'Wrong nature ID', true);
        $dto = criteria('F', '52998224725', [10]);
        $dto->setStrStaFinalidade('V');
        rejected(static fn() => $rn->cadastrarExcecoesSuspensao($dto), 'Wrong purpose');
        $dto->setStrStaFinalidade('S');
        $dto->setStrStaTipoObjeto('P');
        rejected(static fn() => $rn->cadastrarExcecoesSuspensao($dto), 'Wrong type');
        $dto->setStrStaTipoObjeto('C');
        rejected(static fn() => $rn->sincronizarContexto($dto), 'Whole-set sync');
        rejected(static fn() => $rn->sincronizarContextoPersistencia($dto), 'Direct persistence sync');
        check(MdPetWhitelistBD::$rows === [], 'Invalid selection persisted');
    },
    'automatic insertion resolves current links instead of prior search results' => static function () use ($rn): void {
        check(count($rn->pesquisarContatosSuspensao(criteria())) === 2, 'Search before link removal');
        $links = MdPetVinculoBD::$rows;
        MdPetVinculoBD::$rows = array_filter($links, static fn(MdPetVinculoDTO $row): bool => !in_array($row->getNumIdContato(), [10, 11], true));
        try {
            rejected(static fn() => $rn->cadastrarExcecoesSuspensao(criteria()), 'Removed links', true);
            check(MdPetWhitelistBD::$rows === [], 'Stale search result persisted');
        } finally {
            MdPetVinculoBD::$rows = $links;
        }
    },
    'link lookup uses exception permissions without legacy linkage permission' => static function () use ($rn): void {
        SessaoSEI::$denied = ['md_pet_vinculacao_listar'];
        try {
            check(count($rn->pesquisarContatosSuspensao(criteria())) === 2, 'Legacy permission imposed');
            $filter = new MdPetVinculoDTO();
            $filter->setNumIdContato([10], InfraDTO::$OPER_IN);
            foreach (['listar', 'selecionar'] as $action) {
                SessaoSEI::$denied = ['md_pet_exc_suspensao_auto_' . $action];
                rejected(static fn() => (new MdPetVinculoRN())->filtrarContatosSuspensao($filter), 'Direct link helper permission', false);
            }
            SessaoSEI::$denied = [];
            rejected(static fn() => (new MdPetVinculoRN())->filtrarContatosSuspensao(new MdPetVinculoDTO()), 'Missing link filter', false);
            foreach ([[], [0], ['10x']] as $ids) {
                rejected(static function () use ($ids): void {
                    $filter = new MdPetVinculoDTO();
                    $filter->setNumIdContato($ids, InfraDTO::$OPER_IN);
                    (new MdPetVinculoRN())->filtrarContatosSuspensao($filter);
                }, 'Invalid link filter', false);
            }
        } finally {
            SessaoSEI::$denied = [];
        }
    },
    'automatic CPF insertion selects first eligible name, not lowest ID or first candidate' => static function () use ($rn): void {
        $contacts = MdPetContatoRN::$contacts;
        $rows = MdPetWhitelistBD::$rows;
        MdPetContatoRN::$contacts = array_reverse(array_map(static fn(ContatoDTO $contact): ContatoDTO => clone $contact, $contacts));
        foreach (MdPetContatoRN::$contacts as $contact) {
            $contact->setStrNome([10 => 'Z later', 11 => 'B eligible', 16 => 'A unlinked', 17 => 'A wrong type'][$contact->getNumIdContato()] ?? $contact->getStrNome());
        }
        $beforeContacts = count(MdPetContatoRN::$queries);
        $beforeLinks = count(MdPetVinculoBD::$queries);
        try {
            check($rn->cadastrarExcecoesSuspensao(criteria()) === 1, 'Automatic insertion count');
            check(count(MdPetWhitelistBD::$rows) === 1 && MdPetWhitelistBD::$rows[1]->getDblIdObjeto() === 11, 'Wrong first eligible CPF contact');
            check(count(MdPetContatoRN::$queries) === $beforeContacts + 1, 'Automatic insertion contact N+1');
            check(count(MdPetVinculoBD::$queries) === $beforeLinks + 1, 'Automatic insertion link N+1');
        } finally {
            MdPetContatoRN::$contacts = $contacts;
            MdPetWhitelistBD::$rows = $rows;
        }
    },
    'equal names use ascending ID regardless of candidate arrival order' => static function () use ($rn): void {
        $contacts = MdPetContatoRN::$contacts;
        $rows = MdPetWhitelistBD::$rows;
        MdPetContatoRN::$contacts = array_reverse(array_map(static fn(ContatoDTO $contact): ContatoDTO => clone $contact, $contacts));
        foreach (MdPetContatoRN::$contacts as $contact) {
            if (in_array($contact->getNumIdContato(), [10, 11], true)) {
                $contact->setStrNome('Same name');
            }
        }
        try {
            check($rn->cadastrarExcecoesSuspensao(criteria()) === 1, 'Equal-name insertion count');
            check(MdPetWhitelistBD::$rows[1]->getDblIdObjeto() === 10, 'Name tie did not use lowest ID');
        } finally {
            MdPetContatoRN::$contacts = $contacts;
            MdPetWhitelistBD::$rows = $rows;
        }
    },
    'automatic CNPJ insertion accepts first linked inactive contact without CPF type filter' => static function () use ($rn): void {
        $contacts = MdPetContatoRN::$contacts;
        $rows = MdPetWhitelistBD::$rows;
        MdPetContatoRN::$contacts = array_map(static fn(ContatoDTO $contact): ContatoDTO => clone $contact, $contacts);
        foreach (MdPetContatoRN::$contacts as $contact) {
            if ($contact->getNumIdContato() === 20) {
                $contact->setStrNome('A linked');
                $contact->setNumIdTipoContato(88);
            } elseif ($contact->getNumIdContato() === 19) {
                $contact->setStrNome('0 unlinked');
            }
        }
        unset(InfraParametro::$values['TESTE_ID_TIPO_CONTATO_USUARIOS_EXTERNOS']);
        try {
            check($rn->cadastrarExcecoesSuspensao(criteria('J', '11222333000181')) === 1, 'Automatic CNPJ count');
            check(MdPetWhitelistBD::$rows[1]->getDblIdObjeto() === 20, 'CNPJ applied CPF type or activity restriction');
        } finally {
            MdPetContatoRN::$contacts = $contacts;
            MdPetWhitelistBD::$rows = $rows;
            InfraParametro::$values['TESTE_ID_TIPO_CONTATO_USUARIOS_EXTERNOS'] = '77';
        }
    },
    'automatic inclusion preserves leading zero CPF and alphanumeric CNPJ' => static function () use ($rn): void {
        $rows = MdPetWhitelistBD::$rows;
        try {
            foreach ([['F', '012.345.678-90', 14], ['J', '12.abc.345/01de-35', 15]] as [$nature, $document, $id]) {
                check($rn->cadastrarExcecoesSuspensao(criteria($nature, $document)) === 1, 'Formatted document insertion');
                check(end(MdPetWhitelistBD::$rows)->getDblIdObjeto() === $id, 'Document resolved wrong contact');
            }
        } finally {
            MdPetWhitelistBD::$rows = $rows;
        }
    },
    'failure after single insertion rolls back via real InfraRN dispatcher' => static function () use ($rn): void {
        MdPetWhitelistBD::$writes = 0;
        MdPetWhitelistBD::$failAt = 1;
        $dto = criteria();
        rejected(static fn() => $rn->cadastrarExcecoesSuspensao($dto), 'Injected failure after write', false);
        check(!$dto->isSetNumIdMdPetWhitelist(), 'Failed insertion exposed an association ID');
        check(MdPetWhitelistBD::$writes === 1 && MdPetWhitelistBD::$rows === [], 'Insertion not rolled back');
        check(end($rn->bank->events) === 'rollback', 'Real dispatcher did not roll back');
        MdPetWhitelistBD::$failAt = 0;
    },
    'document-only insertion, first-contact duplicate, audit metadata and legacy batch read' => static function () use ($rn): void {
        SessaoSEI::$denied = ['md_pet_vinculacao_listar'];
        $dto = criteria();
        try {
            check($rn->cadastrarExcecoesSuspensao($dto) === 1, 'Insert count without legacy linkage permission');
            check($dto->getNumIdMdPetWhitelist() === 1, 'Insertion did not expose the generated association ID');
        } finally {
            SessaoSEI::$denied = [];
        }
        check(end($rn->bank->events) === 'commit', 'Missing commit');
        $writeAudits = array_values(array_filter(SessaoSEI::$audits, static fn(array $entry): bool =>
            $entry[0] === 'md_pet_exc_suspensao_auto_cadastrar'));
        $audit = end($writeAudits)[2];
        check($audit->getArrIdObjetos() === [10], 'Missing automatically resolved audited ID');
        check(!$audit->isSetStrDocumentoContato() && !$audit->isSetStrNaturezaContato(), 'Document copied into audit');
        rejected(static fn() => $rn->cadastrarExcecoesSuspensao(criteria()), 'Duplicate first eligible contact', true);
        check(count(MdPetWhitelistBD::$rows) === 1, 'Duplicate selected a later contact instead');
        MdPetWhitelistBD::$rows[2] = association(2, 'S', 'C', 11);
        MdPetWhitelistBD::$rows[20] = association(20, 'V', 'P', 10);
        MdPetWhitelistBD::$rows[21] = association(21, 'I', 'P', 11);
        MdPetWhitelistBD::$rows[22] = association(22, 'S', 'C', 999);
        $before = count(MdPetContatoRN::$queries);
        $rows = $rn->listarExcecoesSuspensao(new MdPetWhitelistDTO());
        check(count($rows) === 3, 'Cross-purpose listing');
        check(count(MdPetContatoRN::$queries) === $before + 1, 'N+1 query');
        check($rows[1]->getStrSituacaoContato() === 'Desativado', 'Inactive contact hidden');
        check($rows[2]->getStrSituacaoContato() === 'Contato nao localizado', 'Orphan not identified');
    },
    'deletion preserves contacts and all other purposes' => static function () use ($rn): void {
        $dto = new MdPetWhitelistDTO();
        $dto->setNumIdMdPetWhitelist(20);
        rejected(static fn() => $rn->excluirExcecaoSuspensao($dto), 'Cross-purpose deletion', true);
        $dto->setNumIdMdPetWhitelist(1);
        SessaoSEI::$denied = ['md_pet_exc_suspensao_auto_excluir'];
        rejected(static fn() => $rn->excluirExcecaoSuspensao($dto), 'Delete permission');
        SessaoSEI::$denied = [];
        $rn->excluirExcecaoSuspensao($dto);
        check(!isset(MdPetWhitelistBD::$rows[1]), 'Association not deleted');
        check(isset(MdPetWhitelistBD::$rows[2], MdPetWhitelistBD::$rows[20], MdPetWhitelistBD::$rows[21]), 'Other associations deleted');
        check(count(MdPetContatoRN::$contacts) === 13, 'Contact deleted');
        check(end(SessaoSEI::$audits)[2]->getNumIdMdPetWhitelist() === 1, 'Missing audited deletion ID');
        rejected(static fn() => $rn->excluirExcecaoSuspensao($dto), 'Repeated deletion', true);
    },
    'existing exceptions remain visible and removable without links or type parameter' => static function () use ($rn): void {
        $links = MdPetVinculoBD::$rows;
        MdPetVinculoBD::$rows = [];
        unset(InfraParametro::$values['TESTE_ID_TIPO_CONTATO_USUARIOS_EXTERNOS']);
        $before = count(MdPetVinculoBD::$queries);
        try {
            $rows = $rn->listarExcecoesSuspensao(new MdPetWhitelistDTO());
            check(count($rows) === 2 && $rows[0]->getDblIdObjeto() === 11, 'Existing exception hidden');
            $dto = new MdPetWhitelistDTO();
            $dto->setNumIdMdPetWhitelist(2);
            $rn->excluirExcecaoSuspensao($dto);
            check(!isset(MdPetWhitelistBD::$rows[2]), 'Old exception cannot be removed');
            check(count(MdPetVinculoBD::$queries) === $before, 'New eligibility applied to existing list');
        } finally {
            MdPetVinculoBD::$rows = $links;
            InfraParametro::$values['TESTE_ID_TIPO_CONTATO_USUARIOS_EXTERNOS'] = '77';
        }
    },
];

foreach ($tests as $name => $test) {
    $test();
    echo 'PASS ' . $name . PHP_EOL;
    $count++;
}
echo "PASS $count scenarios" . PHP_EOL;
