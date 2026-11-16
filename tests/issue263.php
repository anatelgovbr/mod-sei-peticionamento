<?php
// Run with: php tests/issue263.php
// Isolated regression checks: the SEI core is not distributed in this repository.
class InfraRN {}
class InfraException extends Exception {
    public function lancarValidacao($message) { throw new self($message); }
}
class PaginaSEI {
    public static function getInstance() { return new self(); }
    public function getArrItensTabelaDinamica($value) { return json_decode($value, true); }
}

// Load the actual class without its unavailable SEI.php bootstrap. No methods are replaced.
$source = file_get_contents(__DIR__ . '/../sei/web/modulos/peticionamento/rn/MdPetProcessoRN.php');
eval(substr($source, strpos($source, 'class MdPetProcessoRN extends InfraRN')));
$class = new ReflectionClass(MdPetProcessoRN::class);
$rn = $class->newInstanceWithoutConstructor();
$checks = 0;

function check($condition, $message) {
    global $checks;
    if (!$condition) { throw new RuntimeException($message); }
    $checks++;
}
function invokeValidation($method, $arguments, $expectedError = null) {
    global $class, $rn;
    try {
        $class->getMethod($method)->invokeArgs($rn, $arguments);
    } catch (InfraException $error) {
        check($expectedError !== null && strpos($error->getMessage(), $expectedError) !== false, 'Unexpected validation error');
        return;
    }
    check($expectedError === null, 'Expected rejection: ' . $method);
}
function attachment($complement) {
    $row = array_fill(0, 13, '');
    $row[0] = str_repeat('a', 251) . '.pdf';
    $row[10] = $complement;
    return $row;
}
function table($rows) {
    // JSON is only the test double transport, not a replacement for SEI serialization.
    return json_encode($rows);
}

foreach ([39, 40, 41, 255] as $length) {
    invokeValidation('validarComplementoDocumento', [str_repeat('a', $length)], $length > 40 ? '40' : null);
    // ISO-8859-1 accented characters must count the same as ASCII characters.
    invokeValidation('validarComplementoDocumento', [str_repeat(chr(233), $length)], $length > 40 ? '40' : null);
}
foreach ([null, '', str_repeat('&amp;', 8), str_repeat('&lt;', 10), str_repeat('&gt;', 10)] as $value) {
    invokeValidation('validarComplementoDocumento', [$value]);
}
foreach ([str_repeat('&amp;', 9), str_repeat('&lt;', 11), str_repeat('&gt;', 11), ['invalid']] as $value) {
    invokeValidation('validarComplementoDocumento', [$value], '40');
}
invokeValidation('validarComplementosDocumentos', [[]]);
foreach (['Principal' => 'principal', 'Essencial' => 'essencial', 'Complementar' => 'complementar'] as $field => $label) {
    $row = attachment(str_repeat('a', 40));
    invokeValidation('validarComplementosDocumentos', [['hdnDoc' . $field => table([$row])]]);
    check($row[10] === str_repeat('a', 40) && strlen($row[0]) === 255, 'Input must remain intact');
    $parameters = ['hdnDoc' . $field => table([$row, attachment(str_repeat('a', 41))])];
    invokeValidation('validarComplementosDocumentos', [$parameters], 'Documento ' . $label . ' 2');
    // Invalid POST must fail before reaching any SEI process creation dependency.
    invokeValidation('gerarProcedimentoConectado', [$parameters], 'Documento ' . $label . ' 2');
}

$document = new class {
    public function isSetStrNomeArvore() { return true; }
    public function getStrNomeArvore() { return str_repeat('a', 41); }
};
// Other callers must fail before reaching signature/access/database dependencies.
invokeValidation('assinarETravarDocumentoProcesso', [null, [], $document, null], '40');
echo "OK: $checks server regression checks\n";
