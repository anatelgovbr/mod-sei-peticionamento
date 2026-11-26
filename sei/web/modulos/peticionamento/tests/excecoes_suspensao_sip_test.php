<?php
/** Exercises the real 4.7.0 SIP seed and its helpers without a database. */
if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}
require_once dirname(__DIR__, 5) . '/sip/web/Sip.php';

class BancoSip extends InfraMySqli
{
    public static function getInstance()
    {
        static $bank;
        return $bank ??= new self();
    }
    public function getServidor() { throw new RuntimeException('No database allowed'); }
    public function getPorta() { throw new RuntimeException('No database allowed'); }
    public function getBanco() { throw new RuntimeException('No database allowed'); }
    public function getUsuario() { throw new RuntimeException('No database allowed'); }
    public function getSenha() { throw new RuntimeException('No database allowed'); }
    public function abrirConexao() { throw new RuntimeException('No database allowed'); }
}

class SipSeedDouble
{
    public static array $tables = [];
    public static array $replications = [];

    public function listar(InfraDTO $filter): array
    {
        return array_values(array_filter(self::$tables[$filter->getStrNomeTabela()] ?? [], static function (InfraDTO $row) use ($filter): bool {
            $values = $row->getArrAtributos();
            foreach ($filter->getArrAtributos() ?? [] as $name => $attribute) {
                if (($attribute[InfraDTO::$POS_ATRIBUTO_FLAGS] & InfraDTO::$FLAG_SET) !== 0
                    && (!isset($values[$name])
                        || $values[$name][InfraDTO::$POS_ATRIBUTO_VALOR] !== $attribute[InfraDTO::$POS_ATRIBUTO_VALOR])) {
                    return false;
                }
            }
            return true;
        }));
    }
    public function consultar(InfraDTO $filter): ?InfraDTO
    {
        $rows = $this->listar($filter);
        if (count($rows) > 1) {
            throw new RuntimeException('Ambiguous lookup');
        }
        return isset($rows[0]) ? clone $rows[0] : null;
    }
    public function contar(InfraDTO $filter): int
    {
        return count($this->listar($filter));
    }
    public function cadastrar(InfraDTO $dto): InfraDTO
    {
        $keys = [
            'recurso' => 'NumIdRecurso',
            'item_menu' => 'NumIdItemMenu',
            'regra_auditoria' => 'NumIdRegraAuditoria',
        ];
        $table = $dto->getStrNomeTabela();
        if (isset($keys[$table])) {
            $setter = 'set' . $keys[$table];
            $dto->$setter(2000 + count(self::$tables[$table] ?? []));
        }
        self::$tables[$table][] = clone $dto;
        return $dto;
    }
}

class SistemaRN extends SipSeedDouble
{
    public function replicarRegraAuditoria(ReplicacaoRegraAuditoriaDTO $dto): void
    {
        self::$replications[] = clone $dto;
    }
}
class PerfilRN extends SipSeedDouble {}
class MenuRN extends SipSeedDouble {}
class ItemMenuRN extends SipSeedDouble
{
    public static int $alterations = 0;

    public function alterar(ItemMenuDTO $dto): void
    {
        verify(!$dto->isSetNumIdSistema() && !$dto->isSetNumIdRecurso()
            && !$dto->isSetNumIdMenuPai() && !$dto->isSetNumIdItemMenuPai()
            && !$dto->isSetNumSequencia() && !$dto->isSetStrSinAtivo()
            && !$dto->isSetStrDescricao() && !$dto->isSetStrIcone()
            && !$dto->isSetStrSinNovaJanela(), 'Rename must only set keys and label');
        foreach (self::$tables['item_menu'] as $index => $row) {
            if ($row->getNumIdMenu() === $dto->getNumIdMenu()
                && $row->getNumIdItemMenu() === $dto->getNumIdItemMenu()) {
                $updated = clone $row;
                $updated->setStrRotulo($dto->getStrRotulo());
                self::$tables['item_menu'][$index] = $updated;
                self::$alterations++;
                return;
            }
        }
        throw new RuntimeException('Menu item missing for rename');
    }
}
class RecursoRN extends SipSeedDouble {}
class RelPerfilRecursoRN extends SipSeedDouble {}
class RelPerfilItemMenuRN extends SipSeedDouble {}
class RegraAuditoriaRN extends SipSeedDouble {}
class RelRegraAuditoriaRecursoRN extends SipSeedDouble {}
class InfraParametroBD extends SipSeedDouble
{
    public function __construct(InfraIBanco $bank) {}
    public function alterar(InfraParametroDTO $dto): void
    {
        foreach (self::$tables['infra_parametro'] as $index => $row) {
            if ($row->getStrNome() === $dto->getStrNome()) {
                self::$tables['infra_parametro'][$index] = clone $dto;
                return;
            }
        }
        throw new RuntimeException('Version parameter missing');
    }
}

function verify(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

function seed(string $class, array $attributes): InfraDTO
{
    $dto = new $class();
    foreach ($attributes as $name => $value) {
        $setter = 'set' . $name;
        $dto->$setter($value);
    }
    SipSeedDouble::$tables[$dto->getStrNomeTabela()][] = $dto;
    return $dto;
}

foreach (['BancoSip', 'SistemaRN', 'PerfilRN', 'MenuRN', 'ItemMenuRN', 'RecursoRN', 'RegraAuditoriaRN', 'InfraParametroBD'] as $class) {
    verify((new ReflectionClass($class))->getFileName() === __FILE__, 'Double not loaded: ' . $class);
}
seed('SistemaDTO', ['NumIdSistema' => 1, 'StrSigla' => 'SEI']);
seed('PerfilDTO', ['NumIdSistema' => 1, 'NumIdPerfil' => 2, 'StrNome' => 'Administrador']);
seed('PerfilDTO', ['NumIdSistema' => 9, 'NumIdPerfil' => 9, 'StrNome' => 'Administrador']);
seed('MenuDTO', ['NumIdSistema' => 1, 'NumIdMenu' => 3, 'StrNome' => 'Principal']);
seed('MenuDTO', ['NumIdSistema' => 9, 'NumIdMenu' => 9, 'StrNome' => 'Principal']);
seed('RecursoDTO', [
    'NumIdSistema' => 1, 'NumIdRecurso' => 99, 'StrNome' => 'md_pet_adm_vinc_listar',
    'StrCaminho' => 'controlador.php?acao=md_pet_adm_vinc_listar', 'StrSinAtivo' => 'S',
]);
seed('RelPerfilRecursoDTO', ['NumIdSistema' => 1, 'NumIdPerfil' => 2, 'NumIdRecurso' => 99]);
seed('RecursoDTO', [
    'NumIdSistema' => 1, 'NumIdRecurso' => 100, 'StrNome' => 'md_pet_integracao_listar',
    'StrCaminho' => 'controlador.php?acao=md_pet_integracao_listar', 'StrSinAtivo' => 'S',
]);
seed('RelPerfilRecursoDTO', ['NumIdSistema' => 1, 'NumIdPerfil' => 2, 'NumIdRecurso' => 100]);
$labels = ["Administra\xE7\xE3o", "Peticionamento Eletr\xF4nico", "Vincula\xE7\xF5es e Procura\xE7\xF5es Eletr\xF4nicas"];
foreach ([
    [100, null, $labels[0]],
    [101, 100, $labels[1]],
    [102, 101, $labels[2]],
    [200, null, "Relat\xF3rios"],
    [201, 200, $labels[1]],
    [202, 201, $labels[2]],
] as [$id, $parent, $label]) {
    seed('ItemMenuDTO', [
        'NumIdSistema' => 1, 'NumIdMenu' => 3, 'NumIdItemMenu' => $id,
        'NumIdMenuPai' => $parent === null ? null : 3, 'NumIdItemMenuPai' => $parent,
        'NumIdRecurso' => $id === 102 ? 99 : null, 'StrRotulo' => $label,
    ]);
}
foreach ([[103, 101, 3, 1], [203, 201, 3, 1], [103, 101, 9, 9]] as [$id, $parent, $menu, $system]) {
    seed('ItemMenuDTO', [
        'NumIdSistema' => $system, 'NumIdMenu' => $menu, 'NumIdItemMenu' => $id,
        'NumIdMenuPai' => $menu, 'NumIdItemMenuPai' => $parent,
        'NumIdRecurso' => 100, 'StrNomeRecurso' => 'md_pet_integracao_listar',
        'StrRotulo' => "Integra\xE7\xF5es", 'NumSequencia' => 90,
        'StrDescricao' => 'Existing integration menu', 'StrIcone' => 'integration.svg',
        'StrSinNovaJanela' => 'N', 'StrSinAtivo' => 'S',
    ]);
}
$originalIntegration = clone SipSeedDouble::$tables['item_menu'][6];
seed('RelPerfilItemMenuDTO', [
    'NumIdSistema' => 1, 'NumIdPerfil' => 2, 'NumIdRecurso' => 100,
    'NumIdMenu' => 3, 'NumIdItemMenu' => 103,
]);
$originalIntegrationLink = clone SipSeedDouble::$tables['rel_perfil_item_menu'][0];
seed('RegraAuditoriaDTO', [
    'NumIdSistema' => 1, 'NumIdRegraAuditoria' => 400,
    'StrDescricao' => 'Modulo_Peticionamento_Eletronico', 'StrSinAtivo' => 'S',
]);
seed('RelRegraAuditoriaRecursoDTO', ['NumIdSistema' => 1, 'NumIdRegraAuditoria' => 400, 'NumIdRecurso' => 999]);
seed('InfraParametroDTO', ['StrNome' => 'VERSAO_MODULO_PETICIONAMENTO', 'StrValor' => '4.6.0']);

$source = file_get_contents(dirname(__DIR__, 5) . '/sip/scripts/sip_atualizar_versao_modulo_peticionamento.php');
$start = strpos($source, 'class MdPetAtualizadorSipRN');
$end = strrpos($source, "\ntry {");
verify($start !== false && $end !== false && $end > $start, 'Cannot isolate installer class');
eval(substr($source, $start, $end - $start));
$installer = new MdPetAtualizadorSipRN();
$method = new ReflectionMethod($installer, 'instalarv470');
$method->invoke($installer);
$sizes = array_map('count', SipSeedDouble::$tables);
verify(ItemMenuRN::$alterations === 1, 'Integration menu must be renamed in place');
$method->invoke($installer);
verify($sizes === array_map('count', SipSeedDouble::$tables), 'Seed not idempotent');
verify(ItemMenuRN::$alterations === 1, 'Already renamed menu must not be updated again');
$expectedIntegration = clone $originalIntegration;
$expectedIntegration->setStrRotulo("Mapeamento das Integra\xE7\xF5es");
verify(SipSeedDouble::$tables['item_menu'][6]->getArrAtributos() === $expectedIntegration->getArrAtributos(), 'Rename changed more than the label');
verify(SipSeedDouble::$tables['rel_perfil_item_menu'][0]->getArrAtributos() === $originalIntegrationLink->getArrAtributos(), 'Original integration profile link changed');
verify(SipSeedDouble::$tables['item_menu'][7]->getStrRotulo() === "Integra\xE7\xF5es", 'Integration under Reports was renamed');
verify(SipSeedDouble::$tables['item_menu'][8]->getStrRotulo() === "Integra\xE7\xF5es", 'Integration from another system/menu was renamed');
verify(SipSeedDouble::$tables['recurso'][1]->getStrCaminho() === 'controlador.php?acao=md_pet_integracao_listar', 'Integration resource path changed');
verify(count(SipSeedDouble::$tables['recurso']) === 6, 'Original resource duplicated or exception resource missing');
verify(count(SipSeedDouble::$tables['rel_perfil_recurso']) === 6, 'Wrong profile/resource count');
verify(count(SipSeedDouble::$tables['rel_perfil_item_menu']) === 3, 'Wrong profile/menu count');
$items = array_values(array_filter(SipSeedDouble::$tables['item_menu'], static fn(InfraDTO $dto): bool => $dto->getNumIdItemMenuPai() === 102));
verify(count($items) === 2, 'Administrative parent must have both children');
usort($items, static fn(InfraDTO $left, InfraDTO $right): int => $left->getNumSequencia() <=> $right->getNumSequencia());
verify($items[0]->getStrRotulo() === 'Administrar ' . $labels[2], 'Original administration must be the first child');
verify($items[0]->getNumSequencia() === 5 && $items[0]->getNumIdRecurso() === 99, 'First child must reuse the original action');
verify($items[1]->getStrRotulo() === "Exce\xE7\xF5es de Suspens\xE3o Autom\xE1tica", 'Exception child lost');
verify($items[1]->getNumSequencia() === 10, 'Exception child order changed');
$resource = SipSeedDouble::$tables['recurso'][0];
verify($resource->getStrCaminho() === 'controlador.php?acao=md_pet_adm_vinc_listar', 'Original listing path changed');
foreach ($items as $item) {
    verify($item->getNumIdMenu() === 3 && $item->getNumIdSistema() === 1, 'Wrong system/menu');
    verify($item->getStrSinAtivo() === 'S', 'Child menu inactive');
    $links = array_values(array_filter(SipSeedDouble::$tables['rel_perfil_item_menu'], static fn(InfraDTO $dto): bool => $dto->getNumIdItemMenu() === $item->getNumIdItemMenu()));
    verify(count($links) === 1 && $links[0]->getNumIdPerfil() === 2 && $links[0]->getNumIdRecurso() === $item->getNumIdRecurso(), 'Child not linked to Administrator with its resource');
}
$exceptionResources = array_values(array_filter(SipSeedDouble::$tables['recurso'], static fn(InfraDTO $dto): bool => $dto->getStrNome() === 'md_pet_exc_suspensao_auto_listar'));
verify(count($exceptionResources) === 1 && $items[1]->getNumIdRecurso() === $exceptionResources[0]->getNumIdRecurso(), 'Exception child action changed');
verify(SipSeedDouble::$tables['item_menu'][2]->getNumIdRecurso() === 99, 'Original parent action lost');
$relations = SipSeedDouble::$tables['rel_regra_auditoria_recurso'];
verify(count($relations) === 3 && $relations[0]->getNumIdRecurso() === 999, 'Previous audit resources lost');
$auditedIds = [$relations[1]->getNumIdRecurso(), $relations[2]->getNumIdRecurso()];
foreach (SipSeedDouble::$tables['recurso'] as $resource) {
    $write = preg_match('/_(cadastrar|excluir)$/D', $resource->getStrNome()) === 1;
    verify(in_array($resource->getNumIdRecurso(), $auditedIds, true) === $write, 'Wrong audit scope');
}
verify(count(SipSeedDouble::$replications) === 2, 'Audit not replicated');
verify(end(SipSeedDouble::$replications)->getStrStaOperacao() === 'A', 'Wrong audit replication operation');
verify(SipSeedDouble::$tables['infra_parametro'][0]->getStrValor() === '4.7.0', 'Version not updated');
echo "PASS SIP resources, both administrative children, original listing first, audit scope, replication and idempotence\n";
echo "PASS SIP integration label renamed in place, metadata/profile links preserved, other hierarchies untouched and reexecution is a no-op\n";

$tables = SipSeedDouble::$tables;
SipSeedDouble::$tables['item_menu'] = array_filter($tables['item_menu'], static fn(InfraDTO $dto): bool => $dto->getNumIdItemMenu() !== 102);
try {
    $method->invoke($installer);
    throw new RuntimeException('Missing parent accepted');
} catch (InfraException $e) {
    verify(count(SipSeedDouble::$tables['recurso']) === 6, 'Created resources before resolving hierarchy');
}
echo "PASS SIP missing administrative parent fails explicitly\n";

SipSeedDouble::$tables = $tables;
SipSeedDouble::$tables['regra_auditoria'] = [];
SipSeedDouble::$tables['rel_regra_auditoria_recurso'] = [];
$method->invoke($installer);
verify(count(SipSeedDouble::$tables['regra_auditoria']) === 1, 'Missing rule not created');
verify(SipSeedDouble::$tables['regra_auditoria'][0]->getStrSinAtivo() === 'S', 'New rule inactive');
verify(count(SipSeedDouble::$tables['rel_regra_auditoria_recurso']) === 2, 'New rule missing write resources');
$sizes = array_map('count', SipSeedDouble::$tables);
$method->invoke($installer);
verify($sizes === array_map('count', SipSeedDouble::$tables), 'New rule not idempotent');
verify(end(SipSeedDouble::$replications)->getNumIdRegraAuditoria() === SipSeedDouble::$tables['regra_auditoria'][0]->getNumIdRegraAuditoria(), 'New rule not replicated');
echo "PASS SIP absent audit rule is created, populated, replicated and remains idempotent\n";

SipSeedDouble::$tables = $tables;
SipSeedDouble::$tables['item_menu'][6] = clone $originalIntegration;
SipSeedDouble::$tables['item_menu'][6]->setStrRotulo("Integra\xE7\xE3o");
$method->invoke($installer);
verify(SipSeedDouble::$tables['item_menu'][6]->getStrRotulo() === "Mapeamento das Integra\xE7\xF5es", 'Singular legacy label not renamed');
echo "PASS SIP integration lookup accepts singular legacy label through the unchanged resource\n";

SipSeedDouble::$tables = $tables;
SipSeedDouble::$tables['item_menu'] = array_filter($tables['item_menu'], static fn(InfraDTO $dto): bool => !($dto->getNumIdMenu() === 3 && $dto->getNumIdItemMenu() === 103));
SipSeedDouble::$tables['infra_parametro'][0] = clone $tables['infra_parametro'][0];
SipSeedDouble::$tables['infra_parametro'][0]->setStrValor('4.6.0');
$sizes = array_map('count', SipSeedDouble::$tables);
try {
    $method->invoke($installer);
    throw new RuntimeException('Missing integration menu accepted');
} catch (InfraException $e) {
    verify($sizes === array_map('count', SipSeedDouble::$tables), 'Missing integration menu must fail without creating a replacement');
    verify(SipSeedDouble::$tables['infra_parametro'][0]->getStrValor() === '4.6.0', 'Version advanced despite missing integration menu');
}
echo "PASS SIP missing administrative integration menu fails explicitly without using homonyms\n";
