<?php
namespace MdPetPageRedirectTest {
    function header(string $header, bool $replace = true, int $status = 0): void
    {
        \PaginaSEI::$redirects[] = [$header, $replace, $status];
        throw new \PageRequestRedirected();
    }

    function session_start(): bool
    {
        return \session_status() === PHP_SESSION_ACTIVE || \session_start();
    }
}

namespace {
/** Runs the actual page with isolated requests and render/session doubles. */
if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}
if (!isset($argv[1])) {
    $cases = [
        'list', 'include', 'exclude', 'get-write', 'invalid-hash', 'denied', 'partial', 'search-only',
        'include-denied', 'select-denied', 'array-document', 'array-nature', 'forged-ids', 'invalid-id',
        'unknown-action', 'array-action', 'invalid-cpf', 'invalid-cnpj', 'no-eligible', 'duplicate',
        'missing-exception', 'validation-html', 'technical-lookup', 'technical-include', 'wrapped-error',
        'expired-session', 'multiple-validations', 'invalid-nature', 'long-document', 'technical-list',
        'include-j', 'missing-nature', 'legacy-search', 'empty-list', 'include-highlight',
        'invalid-result', 'array-result', 'invalid-highlight-id', 'array-highlight-id',
    ];
    foreach ($cases as $case) {
        $process = proc_open([PHP_BINARY, __FILE__, $case], [0 => ['pipe', 'r'], 1 => ['pipe', 'w'], 2 => ['pipe', 'w']], $pipes);
        if (!is_resource($process)) {
            throw new RuntimeException('Cannot start page test');
        }
        fclose($pipes[0]);
        $output = stream_get_contents($pipes[1]);
        $error = stream_get_contents($pipes[2]);
        fclose($pipes[1]);
        fclose($pipes[2]);
        if (proc_close($process) !== 0) {
            throw new RuntimeException($output . $error);
        }
        echo $output;
    }
    echo 'PASS ' . count($cases) . " page scenarios\n";
    exit;
}

require_once dirname(__DIR__, 3) . '/SEI.php';
require_once dirname(__DIR__) . '/dto/MdPetWhitelistDTO.php';
$case = $argv[1];

class PageRequestRejected extends RuntimeException {}
class PageRequestRedirected extends Error {}
class PaginaSEI
{
    public static array $get = [];
    public static array $post = [];
    public static string $method = 'POST';
    public static array $redirects = [];
    public static function getInstance(): self
    {
        static $page;
        return $page ??= new self();
    }
    public static function GET($name) { return self::$get[$name] ?? null; }
    public static function POST($name) { return self::$post[$name] ?? null; }
    public static function SERVER($name) { return self::$method; }
    public static function tratarHTML($text) { return htmlspecialchars((string) $text, ENT_QUOTES, 'ISO-8859-1'); }
    public function formatarXHTML($text) { return htmlspecialchars((string) $text, ENT_QUOTES, 'ISO-8859-1'); }
    public function getStrNomeSistema() { return 'SEI'; }
    public function getDiretorioSvgGlobal(): string { return '/infra/infra_css/svg'; }
    public function processarExcecao(Exception $exception): void { throw new PageRequestRejected('Page refused request'); }
    public function __call($name, $arguments) {}
}
class SessaoSEI
{
    public static string $case = '';
    public static bool $linkValidated = false;
    public static array $permissions = [];
    public static function getInstance(): self
    {
        static $session;
        return $session ??= new self();
    }
    public function validarLink(): void
    {
        if (self::$case === 'invalid-hash') { throw new InfraException('Invalid link'); }
        if (self::$case === 'expired-session') { throw new InfraException('Session expired'); }
        self::$linkValidated = true;
    }
    public function validarPermissao($resource): void
    {
        if (!self::$linkValidated) { throw new RuntimeException('Permission checked before link'); }
        self::$permissions[] = $resource;
        if (self::$case === 'denied') { throw new InfraException('Permission denied'); }
        if ((self::$case === 'include-denied' && $resource === 'md_pet_exc_suspensao_auto_cadastrar')
            || (self::$case === 'select-denied' && $resource === 'md_pet_exc_suspensao_auto_selecionar')) {
            throw new InfraException('Permission denied');
        }
    }
    public function verificarPermissao($resource): bool
    {
        if (self::$case === 'partial') { return $resource === 'md_pet_exc_suspensao_auto_listar'; }
        if (self::$case === 'search-only') { return $resource !== 'md_pet_exc_suspensao_auto_cadastrar'; }
        return true;
    }
    public function assinarLink($url): string { return $url . '&hash=valid'; }
}
class MdPetWhitelistRN
{
    public static array $calls = [];
    public static array $excludedIds = [];
    public static array $includedCriteria = [];
    public function cadastrarExcecoesSuspensao($dto): int
    {
        self::$calls[] = 'include';
        SessaoSEI::getInstance()->validarPermissao('md_pet_exc_suspensao_auto_selecionar');
        if ($dto->isSetArrIdObjetos()) { throw new RuntimeException('Browser-selected IDs reached automatic inclusion'); }
        self::$includedCriteria[] = clone $dto;
        if (SessaoSEI::$case === 'technical-lookup') { throw new InfraException('Injected query failure'); }
        if (SessaoSEI::$case === 'technical-include') { throw new InfraException('Injected persistence failure'); }
        if (SessaoSEI::$case === 'wrapped-error') {
            $exception = new InfraException();
            $exception->lancarValidacao('Validation with technical cause', null, new RuntimeException('Underlying failure'));
        }
        if (SessaoSEI::$case === 'multiple-validations') {
            $exception = new InfraException();
            $exception->adicionarValidacao('First validation');
            $exception->adicionarValidacao('Second validation');
            $exception->lancarValidacoes();
        }
        $messages = [
            'invalid-cpf' => "CPF inv\xE1lido. Informe o documento completo.",
            'invalid-cnpj' => "CNPJ inv\xE1lido. Informe o documento completo.",
            'validation-html' => '<script>untrusted_validation()</script>',
            'no-eligible' => "Nenhum contato eleg\xEDvel localizado para a natureza e o documento informados.",
            'duplicate' => "O primeiro contato eleg\xEDvel j\xE1 possui esta exce\xE7\xE3o.",
        ];
        if (isset($messages[SessaoSEI::$case])) {
            (new InfraException())->lancarValidacao($messages[SessaoSEI::$case]);
        }
        $dto->setNumIdMdPetWhitelist(42);
        return 1;
    }
    public function excluirExcecaoSuspensao($dto): void
    {
        self::$calls[] = 'exclude';
        self::$excludedIds[] = $dto->getNumIdMdPetWhitelist();
        if (SessaoSEI::$case === 'missing-exception') { (new InfraException())->lancarValidacao('Exception not found'); }
    }
    public function listarExcecoesSuspensao($dto): array
    {
        self::$calls[] = 'list';
        if (SessaoSEI::$case === 'technical-list') { throw new InfraException('Injected list failure'); }
        if (SessaoSEI::$case === 'empty-list') { return []; }
        $row = new MdPetWhitelistDTO();
        $row->setNumIdMdPetWhitelist(42);
        $row->setDblIdObjeto(10);
        $row->setStrNaturezaContato('F');
        $row->setStrDocumentoContato('529.982.247-25');
        $row->setStrDescricaoObjeto('<script>untrusted_contact()</script>');
        $row->setStrTipoContato('Test');
        $row->setStrSituacaoContato('Ativo');
        if (SessaoSEI::$case === 'include-highlight') {
            $before = clone $row;
            $before->setNumIdMdPetWhitelist(43);
            $before->setDblIdObjeto(11);
            $after = clone $row;
            $after->setNumIdMdPetWhitelist(44);
            $after->setDblIdObjeto(12);
            return [$before, $row, $after];
        }
        return [$row];
    }
}

foreach (['PaginaSEI', 'SessaoSEI', 'MdPetWhitelistRN'] as $class) {
    if ((new ReflectionClass($class))->getFileName() !== __FILE__) {
        throw new RuntimeException('Double not loaded: ' . $class);
    }
}
SessaoSEI::$case = $case;
$includeCases = [
    'include', 'include-j', 'include-highlight', 'get-write', 'forged-ids', 'invalid-cpf', 'invalid-cnpj', 'validation-html',
    'technical-lookup', 'wrapped-error', 'multiple-validations', 'no-eligible', 'duplicate',
    'technical-include', 'missing-nature', 'include-denied', 'select-denied',
    'array-document', 'array-nature', 'invalid-nature', 'long-document',
];
$action = in_array($case, $includeCases, true) ? 'cadastrar'
    : (['exclude' => 'excluir', 'invalid-id' => 'excluir', 'missing-exception' => 'excluir', 'legacy-search' => 'selecionar'][$case] ?? 'listar');
PaginaSEI::$get = ['acao' => 'md_pet_exc_suspensao_auto_' . $action];
PaginaSEI::$post = ['selNatureza' => 'F', 'txtDocumento' => '52998224725', 'idExcecao' => '42'];
if ($case === 'get-write') { PaginaSEI::$method = 'GET'; }
if ($case === 'array-document') { PaginaSEI::$post['txtDocumento'] = []; }
if ($case === 'array-nature') { PaginaSEI::$post['selNatureza'] = []; }
if ($case === 'forged-ids') { PaginaSEI::$post['arrIdContatos'] = [['999']]; }
if ($case === 'invalid-id') { PaginaSEI::$post['idExcecao'] = '9223372036854775808'; }
if ($case === 'unknown-action') { PaginaSEI::$get['acao'] = 'md_pet_other_excluir'; }
if ($case === 'array-action') { PaginaSEI::$get['acao'] = []; }
if ($case === 'invalid-cpf') { PaginaSEI::$post['txtDocumento'] = '123'; }
if ($case === 'invalid-cnpj') { PaginaSEI::$post['selNatureza'] = 'J'; PaginaSEI::$post['txtDocumento'] = '123'; }
if ($case === 'include-j') {
    PaginaSEI::$post['selNatureza'] = 'J';
    PaginaSEI::$post['txtDocumento'] = '12abc34501de35';
}
if ($case === 'missing-nature') { unset(PaginaSEI::$post['selNatureza']); }
if ($case === 'invalid-nature') { PaginaSEI::$post['selNatureza'] = 'X'; }
if ($case === 'long-document') { PaginaSEI::$post['txtDocumento'] = str_repeat('1', 19); }
if (in_array($case, ['invalid-result', 'array-result', 'invalid-highlight-id', 'array-highlight-id'], true)) {
    PaginaSEI::$method = 'GET';
    PaginaSEI::$post = [];
    PaginaSEI::$get['resultado'] = ['invalid-result' => 'unknown', 'array-result' => []][$case] ?? 'incluido';
    PaginaSEI::$get['id_excecao_incluida'] = $case === 'array-highlight-id' ? [] : '9223372036854775808';
}
$expectedRejection = in_array($case, ['get-write', 'invalid-hash', 'denied', 'include-denied', 'select-denied', 'unknown-action', 'array-action', 'technical-lookup', 'technical-include', 'wrapped-error', 'expired-session', 'technical-list', 'legacy-search'], true);
$expectedValidation = in_array($case, ['array-document', 'array-nature', 'invalid-id', 'invalid-cpf', 'invalid-cnpj', 'no-eligible', 'duplicate', 'missing-exception', 'validation-html', 'multiple-validations', 'invalid-nature', 'long-document', 'missing-nature', 'invalid-result', 'array-result', 'invalid-highlight-id', 'array-highlight-id'], true);
$expectedRedirect = in_array($case, ['include', 'include-j', 'forged-ids', 'include-highlight', 'exclude'], true);
$pageFile = dirname(__DIR__) . '/md_pet_exc_suspensao_auto_lista.php';
$pageSource = file_get_contents($pageFile);
if ($pageSource === false) { throw new RuntimeException('Cannot read actual page'); }
// Keep the page unchanged while intercepting header() without terminating the test process.
$pageCode = 'namespace MdPetPageRedirectTest; use \Exception; use \InfraException; use \PaginaSEI; use \SessaoSEI; use \MdPetWhitelistRN; use \MdPetWhitelistDTO; use \Icone; '
    . str_replace('__FILE__', var_export($pageFile, true), substr($pageSource, 5));
$rejected = false;
$redirected = false;
ob_start();
try {
    eval($pageCode);
} catch (PageRequestRedirected $redirect) {
    $redirected = true;
    if (!$expectedRedirect || count(PaginaSEI::$redirects) !== 1
        || PaginaSEI::$redirects[0][2] !== 303 || !PaginaSEI::$redirects[0][1]
        || ob_get_length() !== 0 || MdPetWhitelistRN::$calls !== [$case === 'exclude' ? 'exclude' : 'include']) {
        throw new RuntimeException('Successful mutation must redirect with 303 before rendering or listing');
    }
    $location = substr(PaginaSEI::$redirects[0][0], strlen('Location: '));
    parse_str(parse_url($location, PHP_URL_QUERY), $query);
    if (!str_starts_with($location, 'controlador.php?') || $query['acao'] !== 'md_pet_exc_suspensao_auto_listar'
        || $query['hash'] !== 'valid' || $query['resultado'] !== ($case === 'exclude' ? 'excluido' : 'incluido')
        || ($case !== 'exclude' && ($query['id_excecao_incluida'] ?? null) !== '42')
        || str_contains($location, '529982') || str_contains($location, '12abc')) {
        throw new RuntimeException('Wrong signed redirect or document exposed in redirect URL');
    }
    PaginaSEI::$get = $query;
    PaginaSEI::$post = [];
    PaginaSEI::$method = 'GET';
    eval($pageCode);
} catch (PageRequestRejected $exception) {
    $rejected = true;
}
$html = ob_get_clean();
if ($redirected !== $expectedRedirect) { throw new RuntimeException('Wrong POST/redirect/GET result: ' . $case); }
if ($rejected !== $expectedRejection) { throw new RuntimeException('Wrong rejection result: ' . $case); }
if ($rejected) {
    $expectedCalls = ['technical-lookup' => ['include'], 'wrapped-error' => ['include'], 'technical-include' => ['include'], 'select-denied' => ['include'], 'technical-list' => ['list']][$case] ?? [];
    if (MdPetWhitelistRN::$calls !== $expectedCalls) { throw new RuntimeException('Wrong RN dispatch before technical rejection'); }
}
if (!$rejected) {
    $inputs = [];
    preg_match_all('/<input\b[^>]*>/s', $html, $inputTags);
    foreach ($inputTags[0] as $tag) {
        if (preg_match('/\bid="([^"]+)"/', $tag, $id)) {
            $inputs[$id[1]] = $tag;
        }
    }
    $physicalSelected = str_contains($inputs['optNaturezaFisica'] ?? '', ' checked');
    $legalSelected = str_contains($inputs['optNaturezaJuridica'] ?? '', ' checked');
    if (str_contains($html, '<script>untrusted_contact()')) { throw new RuntimeException('Unescaped contact name'); }
    if (!str_contains($html, 'method="post"') || !str_contains($html, 'hash=valid')) { throw new RuntimeException('Unsigned/non-POST form'); }
    if (str_contains($html, 'arrIdContatos') || str_contains($html, 'Contatos Localizados')
        || str_contains($html, 'Incluir selecionados') || str_contains($html, '>Pesquisar</button>')) {
        throw new RuntimeException('Intermediate selection still exposed');
    }
    $canInclude = !in_array($case, ['partial', 'search-only'], true);
    if (str_contains($html, 'id="btnAdicionarExcecao"') !== $canInclude) {
        throw new RuntimeException('Automatic inclusion UI has wrong permission');
    }
    if ($canInclude && (preg_match('/<button\b[^>]*id="btnAdicionarExcecao"[^>]*>(.*?)<\/button>/s', $html, $includeButton) !== 1
        || !str_contains($includeButton[0], 'type="submit"')
        || !str_contains($includeButton[0], 'class="infraButton"')
        || !str_contains($includeButton[0], 'formaction="controlador.php?acao=md_pet_exc_suspensao_auto_cadastrar&amp;hash=valid"')
        || trim(strip_tags($includeButton[1])) !== 'Adicionar'
        || !str_contains($html, 'method="post" action="controlador.php?acao=md_pet_exc_suspensao_auto_cadastrar&amp;hash=valid"'))) {
        throw new RuntimeException('Automatic inclusion must use one signed POST, including implicit Enter');
    }
    if (!$canInclude && str_contains($html, 'id="txtDocumento"')) { throw new RuntimeException('Read-only UI exposes inclusion fields'); }
    if ($case === 'partial' && (str_contains($html, 'name="idExcecao"') || str_contains($html, '/remover.svg'))) { throw new RuntimeException('Unauthorized removal UI exposed'); }
    if (!in_array($case, ['partial', 'empty-list'], true)) {
        if (preg_match('/<button\b[^>]*name="idExcecao"[^>]*value="42"[^>]*>.*?<\/button>/s', $html, $removeButton) !== 1
            || !str_contains($removeButton[0], 'type="submit"')
            || !str_contains($removeButton[0], 'class="border-0 bg-transparent p-0"')
            || !str_contains($removeButton[0], 'value="42"')
            || !str_contains($removeButton[0], 'formaction="controlador.php?acao=md_pet_exc_suspensao_auto_excluir&amp;hash=valid"')
            || !str_contains($removeButton[0], 'onclick="return confirm(')
            || !str_contains($removeButton[0], 'src="/infra/infra_css/svg/remover.svg?' . Icone::VERSAO . '"')
            || !str_contains($removeButton[0], 'class="infraImg"')
            || !str_contains($removeButton[0], 'alt="Remover" title="Remover"')
            || str_contains($removeButton[0], '>Remover</button>')) {
            throw new RuntimeException('Removal must use the standard icon and preserve confirmed signed POST');
        }
    }
    if ($case === 'exclude' && MdPetWhitelistRN::$excludedIds !== [42]) {
        throw new RuntimeException('Removal did not receive the selected exception ID');
    }
    if ($canInclude && (substr_count($html, 'type="radio"') !== 2
        || str_contains($html, '<select') || !str_contains($html, 'role="radiogroup"')
        || !str_contains($html, 'for="optNaturezaFisica"') || !str_contains($html, 'for="optNaturezaJuridica"'))) {
        throw new RuntimeException('Nature must use an accessible PF/PJ radio group');
    }
    $expectedHighlight = in_array($case, ['include', 'include-j', 'forged-ids', 'include-highlight'], true);
    preg_match_all('/<tr class="([^"]+)">(.*?)<\/tr>/s', $html, $tableRows, PREG_SET_ORDER);
    $highlighted = array_values(array_filter($tableRows, static fn(array $row): bool => str_contains($row[1], 'infraTrAcessada')));
    if (count($highlighted) !== ($expectedHighlight ? 1 : 0)
        || ($expectedHighlight && !str_contains($highlighted[0][2], 'value="42"'))) {
        throw new RuntimeException('Only the successfully inserted association must use the standard highlight');
    }
    if ($case === 'include-highlight' && (count($tableRows) !== 3
        || $tableRows[0][1] !== 'infraTrClara' || $tableRows[1][1] !== 'infraTrEscura infraTrAcessada'
        || $tableRows[2][1] !== 'infraTrClara')) {
        throw new RuntimeException('Highlight affected neighboring rows with the same document');
    }
    if ($expectedHighlight) {
        if (!str_contains($inputs['txtDocumento'] ?? '', 'value=""') || $physicalSelected
            || $legalSelected || preg_match('/<input\b[^>]*name="arrIdContatos\[\]"/s', $html)) {
            throw new RuntimeException('Successful inclusion did not clear criteria and results');
        }
        if (MdPetWhitelistRN::$calls !== ['include', 'list']) { throw new RuntimeException('Inclusion did not refresh existing exceptions'); }
        $criteria = MdPetWhitelistRN::$includedCriteria[0];
        if ($criteria->isSetArrIdObjetos() || $criteria->getStrNaturezaContato() !== ($case === 'include-j' ? 'J' : 'F')
            || $criteria->getStrDocumentoContato() !== ($case === 'include-j' ? '12abc34501de35' : '52998224725')) {
            throw new RuntimeException('Automatic inclusion did not receive document-only criteria');
        }
    }
    if (in_array($case, ['duplicate', 'no-eligible'], true)
        && (!$physicalSelected || !str_contains($inputs['txtDocumento'] ?? '', 'value="52998224725"'))) {
        throw new RuntimeException('Failed inclusion cleared criteria');
    }
    if (in_array($case, ['include', 'exclude'], true) && MdPetWhitelistRN::$calls !== [$case, 'list']) { throw new RuntimeException('Wrong page dispatch'); }
    if (str_contains($html, '<div class="alert alert-warning" role="alert">') !== $expectedValidation) { throw new RuntimeException('Wrong Bootstrap validation display'); }
    $expectedSuccess = in_array($case, ['include', 'include-j', 'forged-ids', 'include-highlight', 'exclude'], true);
    if (str_contains($html, '<div class="alert alert-success" role="status">') !== $expectedSuccess) {
        throw new RuntimeException('Wrong Bootstrap success display');
    }
    if ($expectedValidation) {
        if (!str_contains($html, 'role="alert"') || !str_contains($html, 'id="txtDocumento"')) { throw new RuntimeException('Validation removed the usable form'); }
        if (str_contains($html, '/opt/') || str_contains($html, 'Stack trace') || str_contains($html, 'contato(s)')) { throw new RuntimeException('Validation exposed technical details or success feedback'); }
        $expectedCalls = [
            'invalid-cpf' => ['include', 'list'], 'invalid-cnpj' => ['include', 'list'], 'validation-html' => ['include', 'list'], 'multiple-validations' => ['include', 'list'],
            'duplicate' => ['include', 'list'], 'no-eligible' => ['include', 'list'], 'missing-exception' => ['exclude', 'list'],
        ][$case] ?? ['list'];
        if (MdPetWhitelistRN::$calls !== $expectedCalls) { throw new RuntimeException('Validation continued the invalid operation'); }
        if (in_array($case, ['invalid-cpf', 'invalid-cnpj'], true)
            && (!str_contains($html, "inv\xE1lido. Informe o documento completo.") || !str_contains($html, 'value="123"'))) {
            throw new RuntimeException('Invalid document message/input not preserved');
        }
        if ($case === 'validation-html' && (str_contains($html, '<script>untrusted_validation()')
            || !str_contains($html, '&lt;script&gt;untrusted_validation()'))) { throw new RuntimeException('Unescaped validation message'); }
        if ($case === 'invalid-cnpj' && !$legalSelected) { throw new RuntimeException('Legal person selection lost'); }
        if ($case === 'missing-nature'
            && ($physicalSelected || $legalSelected)) {
            throw new RuntimeException('Missing radio selection silently defaulted to PF');
        }
        if ($case === 'multiple-validations' && (substr_count($html, 'alert alert-warning') !== 2
            || !str_contains($html, 'First validation') || !str_contains($html, 'Second validation'))) {
            throw new RuntimeException('Not all validation messages were displayed');
        }
    }
}
if (($argv[2] ?? null) === '--render' && !$rejected) {
    $target = DIR_SEI_TEMP . '/excecoes_suspensao_test_render.html';
    if (file_put_contents($target, '<meta charset="ISO-8859-1">' . $html) === false) {
        throw new RuntimeException('Cannot save isolated render');
    }
}
if ($redirected) {
    $beforeCalls = MdPetWhitelistRN::$calls;
    ob_start();
    eval($pageCode);
    ob_end_clean();
    if (MdPetWhitelistRN::$calls !== array_merge($beforeCalls, ['list'])
        || count(PaginaSEI::$redirects) !== 1 || PaginaSEI::$method !== 'GET') {
        throw new RuntimeException('Refreshing the redirected GET repeated a mutation');
    }
}
echo "PASS page $case\n";
}
