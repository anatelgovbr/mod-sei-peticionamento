<?php
try {
    require_once dirname(__FILE__) . '/../../SEI.php';
    session_start();
    PaginaSEI::getInstance()->setBolXHTML(false);
    SessaoSEI::getInstance()->validarLink();
    $strAcao = PaginaSEI::GET('acao');
    $arrAcoes = [
        'md_pet_exc_suspensao_auto_listar',
        'md_pet_exc_suspensao_auto_cadastrar',
        'md_pet_exc_suspensao_auto_excluir',
    ];
    if (!is_string($strAcao) || !in_array($strAcao, $arrAcoes, true)) {
        throw new InfraException('Ação não reconhecida.');
    }
    SessaoSEI::getInstance()->validarPermissao($strAcao);
    SessaoSEI::getInstance()->validarPermissao('md_pet_exc_suspensao_auto_listar');
    if ($strAcao !== 'md_pet_exc_suspensao_auto_listar'
        && PaginaSEI::SERVER('REQUEST_METHOD') !== 'POST') {
        throw new InfraException('Esta operação exige envio do formulário.');
    }

    $strTitulo = 'Exceções de Suspensão Automática';
    $strNatureza = 'F';
    $strDocumento = '';
    $arrValidacoes = [];
    $strMensagem = '';
    $strResultado = '';
    $numIdExcecaoIncluida = null;
    $objRN = new MdPetWhitelistRN();
    $objDTO = new MdPetWhitelistDTO();
    try {
        $mixNatureza = PaginaSEI::POST('selNatureza');
        $mixDocumento = PaginaSEI::POST('txtDocumento');
        if (($mixNatureza !== null && (!is_string($mixNatureza) || !in_array($mixNatureza, ['F', 'J'], true)))
            || ($mixDocumento !== null && (!is_string($mixDocumento) || strlen($mixDocumento) > 18))) {
            (new InfraException())->lancarValidacao('Natureza ou documento inválido.');
        }
        $strNatureza = $mixNatureza ?? ($strAcao === 'md_pet_exc_suspensao_auto_listar' ? 'F' : '');
        $strDocumento = trim($mixDocumento ?? '');
        if ($strAcao === 'md_pet_exc_suspensao_auto_cadastrar'
            && $strNatureza === '') {
            (new InfraException())->lancarValidacao('Selecione Pessoa Física ou Pessoa Jurídica.');
        }
        $objDTO->setStrNaturezaContato($strNatureza);
        $objDTO->setStrDocumentoContato($strDocumento);
        switch ($strAcao) {
            case 'md_pet_exc_suspensao_auto_listar':
                $mixResultado = PaginaSEI::GET('resultado');
                if ($mixResultado === null) {
                    break;
                }
                if (!is_string($mixResultado) || !in_array($mixResultado, ['incluido', 'excluido'], true)) {
                    (new InfraException())->lancarValidacao('Resultado da operação inválido.');
                }
                if ($mixResultado === 'incluido') {
                    $numIdExcecaoIncluida = filter_var(PaginaSEI::GET('id_excecao_incluida'), FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
                    if ($numIdExcecaoIncluida === false) {
                        (new InfraException())->lancarValidacao('Identificador de exceção inválido.');
                    }
                    $strNatureza = '';
                    $strMensagem = '1 contato(s) incluído(s) na lista de exceções.';
                } else {
                    $strMensagem = 'Exceção removida. O cadastro do contato não foi alterado.';
                }
                break;
            case 'md_pet_exc_suspensao_auto_cadastrar':
                $objRN->cadastrarExcecoesSuspensao($objDTO);
                $numIdExcecaoIncluida = (int) $objDTO->getNumIdMdPetWhitelist();
                $strResultado = 'incluido';
                break;
            case 'md_pet_exc_suspensao_auto_excluir':
                $strId = PaginaSEI::POST('idExcecao');
                if (!is_string($strId) || preg_match('/^[1-9][0-9]*$/D', $strId) !== 1
                    || strlen($strId) > strlen((string) PHP_INT_MAX)
                    || (strlen($strId) === strlen((string) PHP_INT_MAX)
                        && strcmp($strId, (string) PHP_INT_MAX) > 0)) {
                    (new InfraException())->lancarValidacao('Identificador de exceção inválido.');
                }
                $objExclusaoDTO = new MdPetWhitelistDTO();
                $objExclusaoDTO->setNumIdMdPetWhitelist((int) $strId);
                $objRN->excluirExcecaoSuspensao($objExclusaoDTO);
                $strResultado = 'excluido';
                break;
        }
    } catch (Exception $e) {
        if ($e instanceof InfraException && $e->contemValidacoes() && $e->getObjException() === null) {
            foreach ($e->getArrObjInfraValidacao() as $objValidacao) {
                $arrValidacoes[] = $objValidacao->getStrDescricao();
            }
        } else {
            PaginaSEI::getInstance()->processarExcecao($e);
        }
    }

    if ($strResultado !== '') {
        $strRetorno = 'controlador.php?acao=md_pet_exc_suspensao_auto_listar&resultado=' . $strResultado;
        if ($numIdExcecaoIncluida !== null) {
            $strRetorno .= '&id_excecao_incluida=' . $numIdExcecaoIncluida;
        }
        header('Location: ' . SessaoSEI::getInstance()->assinarLink($strRetorno), true, 303);
        exit;
    }

    $arrExcecoes = $objRN->listarExcecoesSuspensao(new MdPetWhitelistDTO());
    $bolCadastrar = SessaoSEI::getInstance()->verificarPermissao('md_pet_exc_suspensao_auto_selecionar')
        && SessaoSEI::getInstance()->verificarPermissao('md_pet_exc_suspensao_auto_cadastrar');
    $bolExcluir = SessaoSEI::getInstance()->verificarPermissao('md_pet_exc_suspensao_auto_excluir');
    $arrLinks = [];
    foreach ($arrAcoes as $strDestino) {
        $arrLinks[$strDestino] = PaginaSEI::getInstance()->formatarXHTML(
            SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . $strDestino)
        );
    }
    $strVoltar = PaginaSEI::getInstance()->formatarXHTML(
        SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_pet_adm_vinc_listar')
    );
} catch (Exception $e) {
    PaginaSEI::getInstance()->processarExcecao($e);
    exit;
}

PaginaSEI::getInstance()->montarDocType();
PaginaSEI::getInstance()->abrirHtml();
PaginaSEI::getInstance()->abrirHead();
PaginaSEI::getInstance()->montarMeta();
PaginaSEI::getInstance()->montarTitle(':: ' . PaginaSEI::getInstance()->getStrNomeSistema() . ' - ' . $strTitulo . ' ::');
PaginaSEI::getInstance()->montarStyle();
PaginaSEI::getInstance()->montarJavaScript();
?>
<script>
function atualizarMascaraDocumentoSuspensao() {
    var natureza = document.querySelector('input[name="selNatureza"]:checked');
    var campo = document.getElementById('txtDocumento');
    if (!campo) {
        return;
    }
    var pessoaFisica = natureza && natureza.value === 'F';
    document.getElementById('lblDocumentoSuspensao').textContent = natureza ? (pessoaFisica ? 'CPF:' : 'CNPJ:') : 'CPF/CNPJ:';
    campo.maxLength = pessoaFisica ? 14 : 18;
    campo.inputMode = pessoaFisica ? 'numeric' : 'text';
    campo.placeholder = natureza ? (pessoaFisica ? '000.000.000-00' : '00.000.000/0000-00') : '';
    if (!natureza) {
        return;
    }
    var caracteresInvalidos = pessoaFisica ? /[^0-9]/g : /[^A-Za-z0-9]/g;
    var caracteresAntesCursor = campo.value.substring(0, campo.selectionStart).replace(caracteresInvalidos, '').length;
    var valor = campo.value.replace(caracteresInvalidos, '').toUpperCase().substring(0, pessoaFisica ? 11 : 14);
    var formatado = '';
    if (valor.length === (pessoaFisica ? 11 : 14)) {
        formatado = pessoaFisica ? infraFormatarCpf(valor) : infraFormatarCnpj(valor);
    } else {
        // Os formatadores do core completam documentos parciais com zeros.
        var mascara = pessoaFisica ? '###.###.###-##' : 'HH.HHH.HHH/HHHH-##';
        var indice = 0;
        for (var i = 0; i < mascara.length && indice < valor.length; i++) {
            formatado += mascara[i] === '#' || mascara[i] === 'H' ? valor[indice++] : mascara[i];
        }
    }
    if (campo.value !== formatado) {
        campo.value = formatado;
        var posicao = 0;
        var caracteres = 0;
        while (posicao < formatado.length && caracteres < caracteresAntesCursor) {
            if (/[A-Z0-9]/.test(formatado[posicao])) {
                caracteres++;
            }
            posicao++;
        }
        campo.setSelectionRange(posicao, posicao);
    }
}
document.addEventListener('DOMContentLoaded', atualizarMascaraDocumentoSuspensao);

</script>
<?php
PaginaSEI::getInstance()->fecharHead();
PaginaSEI::getInstance()->abrirBody($strTitulo);
?>
<form id="frmExcecoesSuspensao" method="post" action="<?= $arrLinks[$bolCadastrar ? 'md_pet_exc_suspensao_auto_cadastrar' : 'md_pet_exc_suspensao_auto_listar'] ?>">
    <?php foreach ($arrValidacoes as $strValidacao) { ?>
        <div class="alert alert-warning" role="alert"><?= PaginaSEI::tratarHTML($strValidacao) ?></div>
    <?php } ?>
    <?php if ($strMensagem !== '') { ?>
        <div class="alert alert-success" role="status"><?= PaginaSEI::tratarHTML($strMensagem) ?></div>
    <?php } ?>
    <?php if ($bolCadastrar) { ?>
        <fieldset class="infraFieldset p-3 mb-3">
            <legend class="infraLegend">Adicionar Contato por CPF/CNPJ</legend>
            <div class="row mb-3">
                <div class="col-2">
                    <div role="radiogroup" aria-labelledby="lblNaturezaSuspensao">
                        <span id="lblNaturezaSuspensao" class="infraLabel d-block">Natureza do Contato:</span>
                        <div class="form-check d-inline-block pl-0">
                            <input type="radio" id="optNaturezaFisica" name="selNatureza" value="F" class="infraRadio form-check-input"<?= $strNatureza === 'F' ? ' checked' : '' ?>
                                onchange="atualizarMascaraDocumentoSuspensao();">
                            <label for="optNaturezaFisica" class="infraLabel form-check-label">Pessoa Física</label>
                        </div>
                        <div class="form-check d-inline-block">
                            <input type="radio" id="optNaturezaJuridica" name="selNatureza" value="J" class="infraRadio form-check-input"<?= $strNatureza === 'J' ? ' checked' : '' ?>
                                onchange="atualizarMascaraDocumentoSuspensao();">
                            <label for="optNaturezaJuridica" class="infraLabel form-check-label">Pessoa Jurídica</label>
                        </div>
                    </div>
                </div>
                <div class="col-3">
                    <label for="txtDocumento" id="lblDocumentoSuspensao" class="infraLabel d-block mb-0">CPF/CNPJ:</label>
                    <input type="text" id="txtDocumento" name="txtDocumento" class="infraText" maxlength="<?= $strNatureza === 'F' ? 14 : 18 ?>"
                        value="<?= PaginaSEI::getInstance()->formatarXHTML(PaginaSEI::tratarHTML($strDocumento)) ?>"
                        oninput="atualizarMascaraDocumentoSuspensao();" autocomplete="off">
                    <button type="submit" id="btnAdicionarExcecao" class="infraButton" accesskey="A" formaction="<?= $arrLinks['md_pet_exc_suspensao_auto_cadastrar'] ?>"><span class="infraTeclaAtalho">A</span>dicionar</button>
                </div>
            </div>
            
        </fieldset>
    <?php } ?>
    
    <?php if (empty($arrExcecoes)) { ?>
        <p class="alert alert-warning">Nenhuma exceção de suspensão automática cadastrada.</p>
    <?php } else { ?>
        <div class="infraAreaTabela">
            <table class="infraTable" style="width: 100%;">
                <caption class="infraCaption">Exceções cadastradas (<?= count($arrExcecoes) ?>)</caption>
                <thead><tr>
                    <th scope="col" class="infraTh text-left">CPF/CNPJ</th>
                    <th scope="col" class="infraTh">Natureza</th>
                    <th scope="col" class="infraTh text-center">Nome</th>
                    <th scope="col" class="infraTh">Tipo de contato</th>
                    <!-- <th scope="col" class="infraTh">Situação</th> -->
                    <?php if ($bolExcluir) { ?><th scope="col" class="infraTh text-center">Ações</th><?php } ?>
                </tr></thead>
                <tbody>
                <?php foreach ($arrExcecoes as $i => $objExcecao) { ?>
                    <tr class="<?= $i % 2 === 0 ? 'infraTrClara' : 'infraTrEscura' ?><?= (int) $objExcecao->getNumIdMdPetWhitelist() === $numIdExcecaoIncluida ? ' infraTrAcessada' : '' ?>">
                        <td class="text-left"><?= PaginaSEI::tratarHTML($objExcecao->getStrDocumentoContato()) ?></td>
                        <td class="text-center"><?= $objExcecao->getStrNaturezaContato() === 'F' ? 'Pessoa Física' : ($objExcecao->getStrNaturezaContato() === 'J' ? 'Pessoa Jurídica' : 'Não localizada') ?></td>
                        <td class="text-center"><?= PaginaSEI::tratarHTML($objExcecao->getStrDescricaoObjeto()) ?></td>
                        <td class="text-center"><?= PaginaSEI::tratarHTML($objExcecao->getStrTipoContato()) ?></td>
                        <!-- <td class="text-center"><?= PaginaSEI::tratarHTML($objExcecao->getStrSituacaoContato()) ?></td> -->
                        <?php if ($bolExcluir) { ?>
                            <td class="text-center"><button type="submit" class="border-0 bg-transparent p-0" style="cursor: pointer; outline: none;" name="idExcecao""
                                        value="<?= (int) $objExcecao->getNumIdMdPetWhitelist() ?>"
                                        formaction="<?= $arrLinks['md_pet_exc_suspensao_auto_excluir'] ?>"
                                        onclick="return confirm('Remover esta exceção de suspensão automática? O Usuário Externo do Contato poderá ser suspenso na próxima execução do agendamento de suspensão automática mediante consulta na RFB.');">
                                    <img src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/remover.svg?<?= Icone::VERSAO ?>"
                                         alt="Remover" title="Remover" class="infraImg">
                                </button></td>
                        <?php } ?>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    <?php } ?>
    <?php if (SessaoSEI::getInstance()->verificarPermissao('md_pet_adm_vinc_listar')) { ?>
        <!-- <a class="infraButton" href="<?= $strVoltar ?>">Voltar</a> -->
    <?php } ?>
</form>
<?php
PaginaSEI::getInstance()->fecharBody();
PaginaSEI::getInstance()->fecharHtml();
