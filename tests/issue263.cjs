// Run with: node tests/issue263.cjs
const assert = require('node:assert/strict');
const fs = require('node:fs');
const vm = require('node:vm');
const base = __dirname + '/../sei/web/modulos/peticionamento/';
const source = fs.readFileSync(base + 'md_pet_usu_ext_cadastro_js.php', 'latin1');
const rn = fs.readFileSync(base + 'rn/MdPetProcessoRN.php', 'latin1');
const limit = Number(rn.match(/const TAMANHO_MAXIMO_COMPLEMENTO_DOCUMENTO = (\d+);/)[1]);
const start = source.indexOf('    function validarTamanhoComplementoDocumento(');
const end = source.indexOf('    //campo de upload de documentos principais', start);
const helper = source.slice(start, end).replace(/<\?= MdPetProcessoRN::TAMANHO_MAXIMO_COMPLEMENTO_DOCUMENTO \?>/, String(limit));
const elements = new Map();
let focused;
function element() {
    return {
        attributes: {},
        setAttribute(key, value) { this.attributes[key] = value; },
        removeAttribute(key) { delete this.attributes[key]; },
        focus() { focused = this; },
        remove() { elements.delete(this.id); },
        parentNode: { appendChild(node) { elements.set(node.id, node); } }
    };
}
const context = vm.createContext({
    document: { getElementById: id => elements.get(id), createElement: element },
    // Model jQuery text().html() escaping without introducing a browser dependency.
    $: () => ({ text(value) { return { html: () => value.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;') }; } })
});
vm.runInContext(helper, context);
let checks = 0;
for (const kind of ['Principal', 'Essencial', 'Complementar']) {
    const id = 'complemento' + kind;
    const field = element();
    elements.set(id, field);
    for (const [value, valid] of [
        ['a'.repeat(39), true], ['a'.repeat(40), true], ['a'.repeat(41), false], ['a'.repeat(255), false],
        ['\u00e9'.repeat(40), true], ['\u00e9'.repeat(41), false],
        ['&'.repeat(8), true], ['&'.repeat(9), false],
        ['<'.repeat(10), true], ['<'.repeat(11), false],
        ['>'.repeat(10), true], ['>'.repeat(11), false], ['', true]
    ]) {
        field.value = value;
        assert.equal(context.validarTamanhoComplementoDocumento(id), valid);
        assert.equal(field.value, value, 'Must not truncate input');
        assert.equal(elements.has(id + 'ErroTamanho'), !valid);
        if (!valid) {
            assert.equal(focused, field);
            assert.equal(field.attributes['aria-invalid'], 'true');
            assert.equal(field.attributes['aria-describedby'], id + 'ErroTamanho');
        } else {
            assert.equal(field.attributes['aria-invalid'], undefined);
            assert.equal(field.attributes['aria-describedby'], undefined);
        }
        checks++;
    }
}
console.log(`OK: ${checks} form regression scenarios`);
