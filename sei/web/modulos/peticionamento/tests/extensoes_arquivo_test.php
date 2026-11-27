<?php
if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

require_once dirname(__DIR__, 3) . '/SEI.php';
require_once dirname(__DIR__) . '/dto/MdPetExtensoesArquivoDTO.php';

class ExtensoesFixture
{
    public static array $rows = [];
    public static array $core = [1, 2];
    public static array $writes = [];
    public static array $messages = [];
    public static array $permissions = [];
    public static int $queries = 0;
    public static string $failure = '';
}

class PaginaSEI
{
    public static $TIPO_MSG_ERRO = 1;
    public static $TIPO_MSG_AVISO = 2;
    public static function getInstance(): self { static $page; return $page ??= new self(); }
    public static function GET($key): mixed { return $_GET[$key] ?? null; }
    public static function POST($key): mixed { return $_POST[$key] ?? null; }
    public static function SERVER($key): mixed { return $_SERVER[$key] ?? null; }
    public function getArrValuesSelect($value): array { return (new ExtensoesSelectParser())->getArrValuesSelect($value); }
    public function processarExcecao($exception): never { throw $exception; }
    public function adicionarMensagem($message, $type): void { ExtensoesFixture::$messages[] = $message; }
}

class SessaoSEI
{
    public static function getInstance(): self { static $session; return $session ??= new self(); }
    public function validarLink(): void
    {
        if (in_array(ExtensoesFixture::$failure, ['link', 'session'], true)) {
            throw new InfraException('Request rejected');
        }
    }
    public function validarPermissao($resource): void
    {
        ExtensoesFixture::$permissions[] = $resource;
        if (ExtensoesFixture::$failure === 'permission') {
            throw new InfraException('Permission denied');
        }
    }
}

class ArquivoExtensaoRN
{
    public function listar(ArquivoExtensaoDTO $dto): array
    {
        ExtensoesFixture::$queries++;
        if (ExtensoesFixture::$failure === 'core') {
            throw new RuntimeException('Core read failed');
        }
        if ($dto->getStrSinAtivo() !== 'S' || !$dto->isBolExclusaoLogica()) {
            throw new RuntimeException('Active core filter missing');
        }
        return array_map(static function ($id): ArquivoExtensaoDTO {
            $item = new ArquivoExtensaoDTO();
            $item->setNumIdArquivoExtensao($id);
            return $item;
        }, ExtensoesFixture::$core);
    }
}

class MdPetExtensoesArquivoRN
{
    public function listar(MdPetExtensoesArquivoDTO $dto): array
    {
        $attributes = $dto->getArrAtributos();
        $result = [];
        foreach (ExtensoesFixture::$rows as $row) {
            $include = true;
            foreach (['IdArquivoExtensao' => 'id', 'SinPrincipal' => 'group', 'SinAtivo' => 'active'] as $attribute => $key) {
                if (!isset($attributes[$attribute]) || !($attributes[$attribute][InfraDTO::$POS_ATRIBUTO_FLAGS] & InfraDTO::$FLAG_SET)) {
                    continue;
                }
                $value = $attributes[$attribute][InfraDTO::$POS_ATRIBUTO_VALOR];
                $include = $include && match ($dto->getOperador($attributes[$attribute])) {
                    'IN' => in_array($row[$key], $value),
                    'NOT IN' => !in_array($row[$key], $value),
                    '=' => $row[$key] == $value,
                    default => throw new RuntimeException('Unexpected criterion'),
                };
            }
            if ($include) {
                $item = new MdPetExtensoesArquivoDTO();
                $item->setNumIdExtensaoArquivoPerm($row['pk']);
                $item->setNumIdArquivoExtensao($row['id']);
                $item->setStrSinPrincipal($row['group']);
                $item->setStrSinAtivo($row['active']);
                $item->setStrExtensao([1 => 'PDF', 2 => 'XML', 9 => 'ZIP'][$row['id']]);
                $result[] = $item;
            }
        }
        usort($result, static fn($a, $b) => strcmp($a->getStrExtensao(), $b->getStrExtensao()));
        return $result;
    }
    public function excluir(array $items): void
    {
        foreach ($items as $item) {
            $id = $item->getNumIdExtensaoArquivoPerm();
            ExtensoesFixture::$writes[] = ['delete', $id];
            ExtensoesFixture::$rows = array_values(array_filter(ExtensoesFixture::$rows, static fn($row) => $row['pk'] !== $id));
        }
    }
    public function cadastrar(array $items): array
    {
        foreach ($items as $item) {
            $id = $item->getNumIdArquivoExtensao();
            if (!is_int($id)) {
                throw new RuntimeException('Expected individual integer ID');
            }
            ExtensoesFixture::$writes[] = ['insert', $id];
            ExtensoesFixture::$rows[] = ['pk' => 1000 + count(ExtensoesFixture::$rows), 'id' => $id, 'group' => $item->getStrSinPrincipal(), 'active' => 'S'];
        }
        return $items;
    }
}

foreach (['PaginaSEI', 'SessaoSEI', 'ArquivoExtensaoRN', 'MdPetExtensoesArquivoRN'] as $class) {
    if ((new ReflectionClass($class))->getFileName() !== __FILE__) {
        throw new RuntimeException('Dependency is not isolated: ' . $class);
    }
}

$parser = '';
$delimiters = [];
foreach (['getArrOptionsSelect', 'getArrValuesSelect'] as $method) {
    $reflection = new ReflectionMethod(InfraPagina::class, $method);
    $lines = file($reflection->getFileName());
    $source = implode('', array_slice($lines, $reflection->getStartLine() - 1, $reflection->getEndLine() - $reflection->getStartLine() + 1));
    $parser .= $source;
    if ($method === 'getArrOptionsSelect') {
        preg_match_all("/explode\\('([^']+)'/", $source, $matches);
        $delimiters = $matches[1];
    }
}
if (count($delimiters) !== 2) {
    throw new RuntimeException('Unexpected select protocol');
}
eval('class ExtensoesSelectParser {' . $parser . '}');
$encode = static fn($ids) => implode($delimiters[0], array_map(static fn($id) => (string)$id . $delimiters[1] . 'extension', $ids));
$source = file_get_contents(dirname(__DIR__) . '/md_pet_extensoes_arquivo_cadastro.php');
$source = preg_replace("/require_once dirname\\(__FILE__\\)[^;]+;/", '', $source, -1, $count);
if ($count !== 1 || substr_count($source, '$strTitulo =') !== 1 || substr_count($source, 'session_start();') !== 1) {
    throw new RuntimeException('Unexpected page boundaries');
}
$source = str_replace('session_start();', '', explode('$strTitulo =', $source, 2)[0]);
require_once dirname(__DIR__) . '/int/MdPetExtensoesArquivoINT.php';
set_error_handler(static function ($severity, $message, $file, $line): never {
    throw new ErrorException($message, 0, $severity, $file, $line);
});

$rows = [
    ['pk' => 101, 'id' => 1, 'group' => 'S', 'active' => 'S'],
    ['pk' => 102, 'id' => 2, 'group' => 'S', 'active' => 'S'],
    ['pk' => 103, 'id' => 2, 'group' => 'N', 'active' => 'S'],
    ['pk' => 104, 'id' => 9, 'group' => 'N', 'active' => 'S'],
];
$cases = [
    'get' => ['method' => 'GET'],
    'get-empty-core' => ['method' => 'GET', 'core' => []],
    'no-insert' => ['rows' => array_slice($rows, 0, 3)],
    'insert-principal' => ['rows' => array_slice($rows, 1)],
    'insert-complementar' => ['rows' => array_slice($rows, 0, 2)],
    'remove-principal' => ['principal' => [1]],
    'remove-complementar' => [],
    'swap-groups' => ['principal' => [2], 'complementar' => [1]],
    'mixed-ids' => ['principal' => [1, 9, 999], 'complementar' => [2, 9]],
    'duplicate-ids' => ['rows' => [], 'principal' => [1, 1], 'complementar' => [2, 2]],
    'invalid-principal' => ['principal' => [9], 'validation' => true],
    'invalid-complementar' => ['principal' => [1], 'complementar' => [9], 'validation' => true],
    'empty-core-post' => ['core' => [], 'validation' => true],
    'empty-principal' => ['principal' => [], 'validation' => true],
    'empty-complementar' => ['complementar' => [], 'validation' => true],
    'array-field' => ['raw' => [], 'validation' => true],
    'missing-fields' => ['missing' => true, 'validation' => true],
    'malformed-id' => ['principal' => [1, 'abc'], 'validation' => true],
    'zero-id' => ['principal' => [0], 'validation' => true],
    'negative-id' => ['principal' => [-1], 'validation' => true],
    'overflow-id' => ['principal' => [PHP_INT_MAX . '0'], 'validation' => true],
    'array-action' => ['action' => [], 'rejected' => true],
    'unknown-action' => ['action' => 'other', 'rejected' => true],
    'invalid-link' => ['failure' => 'link', 'rejected' => true],
    'expired-session' => ['failure' => 'session', 'rejected' => true],
    'permission-denied' => ['failure' => 'permission', 'rejected' => true],
    'core-read-failure' => ['failure' => 'core', 'technical' => true],
    'strings' => ['mode' => 'strings'],
    'empty-strings' => ['mode' => 'strings', 'core' => []],
    'select-placeholder' => ['mode' => 'placeholder', 'core' => []],
];
$failed = 0;
foreach ($cases as $name => $options) {
    ExtensoesFixture::$rows = $options['rows'] ?? $rows;
    ExtensoesFixture::$core = $options['core'] ?? [1, 2];
    ExtensoesFixture::$writes = ExtensoesFixture::$messages = ExtensoesFixture::$permissions = [];
    ExtensoesFixture::$queries = 0;
    ExtensoesFixture::$failure = $options['failure'] ?? '';
    $_GET = ['acao' => $options['action'] ?? 'md_pet_extensoes_arquivo_cadastrar'];
    $_SERVER['REQUEST_METHOD'] = $options['method'] ?? 'POST';
    $_POST = $_SERVER['REQUEST_METHOD'] === 'GET' || isset($options['missing']) ? [] : [
        'hdnPrincipal' => $encode($options['principal'] ?? [1, 2]),
        'hdnComplementar' => $encode($options['complementar'] ?? [2]),
    ];
    if (array_key_exists('raw', $options)) {
        $_POST['hdnPrincipal'] = $options['raw'];
    }
    $initial = ExtensoesFixture::$rows;
    $caught = null;
    $result = '';
    try {
        if (($options['mode'] ?? '') === 'strings') {
            $result = MdPetExtensoesArquivoINT::recuperaExtensoes(null, null, null, 'S');
        } elseif (($options['mode'] ?? '') === 'placeholder') {
            $result = MdPetExtensoesArquivoINT::montarSelectExtensoes('0', 'None', null, 'S');
        } else {
            $result = (static function ($code): array {
                eval('?>' . $code);
                return [$strSelExtensoesPrin, $strSelExtensoesComp];
            })($source);
        }
    } catch (Throwable $exception) {
        $caught = $exception;
    }
    $ok = true;
    if (isset($options['validation'])) {
        $ok = $caught instanceof InfraException && $caught->contemValidacoes() && ExtensoesFixture::$rows === $initial && ExtensoesFixture::$writes === [] && ExtensoesFixture::$messages === [];
    } elseif (isset($options['rejected'])) {
        $ok = $caught instanceof InfraException && ExtensoesFixture::$queries === 0 && ExtensoesFixture::$writes === [];
    } elseif (isset($options['technical'])) {
        $ok = $caught instanceof RuntimeException && ExtensoesFixture::$writes === [];
    } else {
        $ok = $caught === null && ExtensoesFixture::$queries === 1;
        if (($options['mode'] ?? '') === 'strings') {
            $ok = $ok && $result === (ExtensoesFixture::$core === [] ? '' : "'pdf','xml'");
        } elseif (($options['mode'] ?? '') === 'placeholder') {
            $ok = $ok && str_contains($result, '>None</option>');
        } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $actual = array_map(static fn($row) => $row['group'] . ':' . $row['id'], ExtensoesFixture::$rows);
            $expected = [];
            foreach (['S' => $options['principal'] ?? [1, 2], 'N' => $options['complementar'] ?? [2]] as $group => $ids) {
                foreach (array_unique(array_intersect($ids, ExtensoesFixture::$core)) as $id) {
                    $expected[] = $group . ':' . $id;
                }
            }
            sort($actual);
            sort($expected);
            $ok = $ok && $actual === $expected && count(ExtensoesFixture::$messages) === 1;
        } else {
            $ok = $ok && ExtensoesFixture::$writes === [] && ExtensoesFixture::$messages === []
                && !str_contains($result[1], 'value="9"')
                && (ExtensoesFixture::$core !== [] || $result === ['', '']);
        }
    }
    echo ($ok ? 'PASS ' : 'FAIL ') . $name . ($caught === null ? '' : ' [' . get_class($caught) . ']') . PHP_EOL;
    $failed += $ok ? 0 : 1;
}
restore_error_handler();
echo count($cases) . ' scenarios, ' . $failed . ' failures' . PHP_EOL;
exit($failed === 0 ? 0 : 1);
