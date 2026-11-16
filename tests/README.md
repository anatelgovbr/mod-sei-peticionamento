# Regressão da issue #263

Execute na raiz do repositório, sem dependências adicionais:

```sh
php tests/issue263.php
node tests/issue263.cjs
```

Os testes isolam as validações reais do módulo usando substitutos mínimos para o core do SEI, a tabela dinâmica, o DOM e o escape do jQuery. Não verificam a serialização real da Infra nem a persistência no banco.

Antes da liberação, homologar em SEI 5.0.5 com MySQL em modo estrito:

- Conferir o tamanho real de `documento.nome_arvore`, o charset da conexão e a regra de tamanho do core. O limite adotado pelo módulo permanece em 40 caracteres na representação persistida.
- Reproduzir separadamente o upload de arquivo com nome de 255 caracteres e o envio de complemento longo. Comparar o complemento informado, o resultado de `getArrItensTabelaDinamica()` e o valor atribuído ao DTO, inclusive para `&`, `<` e `>`.
- Em cada categoria de documento, verificar o aviso antes do upload, o foco no complemento e a possibilidade de corrigir e tentar novamente.
- Contornar a validação do navegador com POST alterado: o servidor deve apresentar validação, preservar os temporários e não criar processo, documentos, acessos ou assinaturas parciais.
- Concluir com dados válidos e conferir assinatura, bloqueio, recibo e download com o nome original do arquivo.
