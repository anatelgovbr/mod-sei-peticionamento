
# Módulo Peticionamento e Intimação Eletrônicos

## Requisitos
- Requisito Mínimo é o SEI 5.0.5 instalado/atualizado - Não é compatível com versões anteriores e em versões mais recentes é necessário conferir antes se possui compatibilidade.
   - Verificar valor da constante de versão no arquivo /sei/web/SEI.php ou, após logado no sistema, parando o mouse sobre a logo do SEI no canto superior esquerdo.
- Antes de executar os scripts de instalação/atualização, o usuário de acesso aos bancos de dados do SEI e do SIP, constante nos arquivos ConfiguracaoSEI.php e ConfiguracaoSip.php, deverá ter permissão de acesso total ao banco de dados, permitindo, por exemplo, criação e exclusão de tabelas.
- Os códigos-fonte do Módulo podem ser baixados a partir do link a seguir, devendo sempre utilizar a versão mais recente: [https://github.com/anatelgovbr/mod-sei-peticionamento/releases](https://github.com/anatelgovbr/mod-sei-peticionamento/releases "Clique e acesse")
- Se já tiver instalado versão principal com a execução dos scripts de banco do módulo no SEI e no SIP, **em versões intermediárias basta sobrescrever os códigos** e não precisa executar os scripts de banco novamente.
   - Atualizações de versões intermediárias são melhorias apenas de código e são identificadas com o incremento somente do terceiro dígito da versão (p. ex. v4.1.1, v4.1.2) e não envolve execução de scripts de banco.

## Procedimentos para Instalação
1. Fazer backup dos bancos de dados do SEI e do SIP.
2. Carregar no servidor os arquivos do módulo nas pastas correspondentes nos servidores do SEI e do SIP.
   - **Caso se trate de atualização de versão anterior do Módulo**, antes de copiar os códigos-fontes para a pasta "/sei/web/modulos/peticionamento", é necessário excluir os arquivos anteriores pré existentes na mencionada pasta, para não manter arquivos de códigos que foram renomeados ou descontinuados.
3. Editar o arquivo "/sei/config/ConfiguracaoSEI.php", tomando o cuidado de usar editor que não altere o charset do arquivo, para adicionar a referência à classe de integração do módulo e seu caminho relativo dentro da pasta "/sei/web/modulos" na array 'Modulos' da chave 'SEI':

		'SEI' => array(
			...
			'Modulos' => array(
				'PeticionamentoIntegracao' => 'peticionamento',
				),
			),

4. Antes de seguir para os próximos passos, é importante conferir se o Módulo foi corretamente declarado no arquivo "/sei/config/ConfiguracaoSEI.php". Acesse o menu **Infra > Módulos** e confira se consta a linha correspondente ao Módulo, pois, realizando os passos anteriores da forma correta, independente da execução do script de banco, o Módulo já deve ser reconhecido na tela aberta pelo menu indicado.
5. Rodar o script de banco "/sip/scripts/sip_atualizar_versao_modulo_peticionamento.php" em linha de comando no servidor do SIP, verificando se não houve erro em sua execução, em que ao final do log deverá ser informado "FIM". Exemplo de comando de execução:

		/usr/bin/php -c /etc/php.ini /opt/sip/scripts/sip_atualizar_versao_modulo_peticionamento.php 2>&1 > atualizacao_peticionamento_sip.log

6. Rodar o script de banco "/sei/scripts/sei_atualizar_versao_modulo_peticionamento.php" em linha de comando no servidor do SEI, verificando se não houve erro em sua execução, em que ao final do log deverá ser informado "FIM". Exemplo de comando de execução:

		/usr/bin/php -c /etc/php.ini /opt/sei/scripts/sei_atualizar_versao_modulo_peticionamento.php 2>&1 > atualizacao_peticionamento_sei.log

7. **IMPORTANTE**: Na execução dos dois scripts de banco acima, ao final deve constar o termo "FIM", o "TEMPO TOTAL DE EXECUÇÃO" e a informação de que a instalação/atualização foi realizada com sucesso na base de dados correspondente (SEM ERROS). Do contrário, o script não foi executado até o final e algum dado não foi inserido/atualizado no respectivo banco de dados, devendo recuperar o backup do banco e repetir o procedimento.
   - Constando ao final da execução do script as informações indicadas, pode logar no SEI e SIP e verificar no menu **Infra > Parâmetros** dos dois sistemas se consta o parâmetro "VERSAO_MODULO_PETICIONAMENTO" com o valor da última versão do módulo.
8. Em caso de erro durante a execução do script, verificar (lendo as mensagens de erro e no menu Infra > Log do SEI e do SIP) se a causa é algum problema na infraestrutura local ou ajustes indevidos na estrutura de banco do core do sistema. Neste caso, após a correção, deve recuperar o backup do banco pertinente e repetir o procedimento, especialmente a execução dos scripts de banco indicados acima.
9. Após a execução com sucesso, com um usuário com permissão de Administrador no SEI, seguir os passos dispostos no tópico "Orientações Negociais" mais abaixo.

## Orientações Negociais
1. Imediatamente após a instalação com sucesso, com usuário com permissão de "Administrador" do SEI, acessar os menus de administração do Módulo pelo seguinte caminho: Administração > Peticionamento Eletrônico. Somente com tudo parametrizado adequadamente será possível o uso do módulo pelos Usuários Externos por meio da tela de Acesso Externo do SEI:

		http://[Servidor_PHP]/sei/controlador_externo.php?acao=usuario_externo_logar&id_orgao_acesso_externo=idOrgao

2. O script de banco do SIP já cria todos os Recursos e Menus e os associam automaticamente ao Perfil "Básico" ou ao Perfil "Administrador".
	- Independente da criação de outros Perfis, os recursos indicados para o Perfil "Básico" ou "Administrador" devem manter correspondência com os Perfis dos Usuários internos que utilizarão o Módulo e dos Usuários Administradores do Módulo.
	- O SIP não controla Perfil próprio para os Usuários Externos, cabendo diretamente ao código do Módulo o controle devido junto aos Recursos e Menus criados pelo Módulo para os Usuários Externos.
	- Tão quanto ocorre com as atualizações do SEI, versões futuras deste Módulo continuarão a atualizar e criar Recursos e associá-los apenas aos Perfis "Básico" e "Administrador".
	- Todos os recursos do Módulo iniciam pelo sufix **"md_pet_"**.
3. Acesse o [Manual do Webservice do Módulo Peticionamento](https://github.com/anatelgovbr/mod-sei-peticionamento/blob/master/sei/web/modulos/peticionamento/ws/manual_ws_peticionamento.md).
4. Acesse o [Manual de Administração](https://docs.google.com/document/d/e/2PACX-1vRmsyc-Z35FHvrRuAeEYX6HsHJZKf0lEWwara8qJLrpJgL1bc6pOMSdP2wxgE6VCyHrgkotO3HqVnE4/pub).
5. Acesse o [Manual do Usuário Interno](https://docs.google.com/document/d/e/2PACX-1vSFScFD8PYCPDiqi6Sg5AZrzQekFtUp4j0iFyiDONkrtvMxM7S29LnWxZ1KbfpIxy6QFBQAw0QW-3zo/pub).
6. Acesse o [Manual do Usuário Externo](https://docs.google.com/document/d/1tBRrH1E4s25Q2ZBe6sW0qp75HsnIvKWfqmeoVU8MQLo/pub).
	- Não foi possível fazer um Manual do Usuário Externo genérico para qualquer órgão, em razão das especificidades de cada órgão quanto aos procedimentos de credenciamento dos Usuários Externos e até mesmo de parametrização do Módulo. De qualquer forma, o Manual do Usuário Externo do SEI elaborado pela Anatel pode ser quase que completamente aproveitado.

## Extensões de Arquivos Permitidas

A [página de cadastro](md_pet_extensoes_arquivo_cadastro.php) mantém as seleções de documento principal e dos demais documentos restritas às extensões ativas no core. A ação continua exigindo link assinado e o recurso `md_pet_extensoes_arquivo_cadastrar`.

Os campos enviados usam `PaginaSEI::POST`, com validação do formato e conversão dos identificadores para inteiros positivos. Identificadores repetidos são removidos, e extensões que não pertencem à lista ativa do core são desconsideradas. As duas seleções são filtradas e validadas antes de qualquer exclusão ou inclusão; cada conjunto deve conservar pelo menos uma extensão permitida. Uma seleção inválida não altera associações nem apresenta confirmação de sucesso.

A lista do core é consultada uma única vez por abertura ou salvamento e reutilizada pelos dois selects. Sem extensões ativas, a tela monta listas vazias sem configurar critérios `IN` vazios. A geração da string de extensões também retorna uma string vazia nesse caso. O ajuste não cria tabelas, recursos SIP ou versões e não altera as fronteiras transacionais legadas das RNs.

Execute a regressão isolada:

```bash
php /opt/sei/web/modulos/peticionamento/tests/extensoes_arquivo_test.php
```

O [teste de extensões](tests/extensoes_arquivo_test.php) executa o processamento real da página e a INT, com DTOs, parser de seleção e montagem de options do InfraPHP. Sessão e persistência usam substitutos conferidos por reflexão, sem acesso ao banco configurado. Os cenários cobrem inclusão, remoção, troca de grupos, ausência de inclusões, consultas repetidas, conjuntos vazios, tipos e IDs inválidos, duplicidade, link, permissão, sessão e falha de leitura do core. A homologação da interface em sessão autenticada permanece necessária.

A regressão passou nos 30 cenários, sem falhas; antes da correção, 23 cenários falhavam. `php -l` passou na página, na INT e no teste. Latin-1, abertura completa, ausência de BOM, preservação das quebras de linha e `git diff --check` foram verificados. O auditor da página existente retornou zero erros e um aviso P7 preexistente, sobre comandos sem verificação condicional individual; a permissão da página continua obrigatória na entrada, e o teste confirma a recusa sem permissão. Não foram executadas operações no banco configurado nem a homologação autenticada.

## Exceções de Suspensão Automática (4.7.0)

Na atualização SIP 4.7.0, o item **Administração > Peticionamento Eletrônico > Integrações** passa a se chamar **Mapeamento das Integrações**. A alteração preserva o identificador do item de menu, o recurso `md_pet_integracao_listar`, o destino, a posição e os vínculos de perfil. O item é localizado pela hierarquia administrativa e pelo recurso, independentemente de o rótulo anterior estar no singular ou no plural. A reexecução não cria outro item nem altera novamente o rótulo já atualizado.

O menu **Administração > Peticionamento Eletrônico > Vinculações e Procurações Eletrônicas** contém dois submenus, nesta ordem:

1. **Administrar Vinculações e Procurações Eletrônicas**: abre a listagem e pesquisa anteriores, com o recurso existente `md_pet_adm_vinc_listar`.
2. **Exceções de Suspensão Automática**: abre o gerenciamento das exceções.

Acesse **Administração > Peticionamento Eletrônico > Vinculações e Procurações Eletrônicas > Exceções de Suspensão Automática**. Escolha a natureza (Pessoa Física ou Pessoa Jurídica), informe o CPF/CNPJ completo e acione **Adicionar**. Em um único envio por POST, o servidor localiza e inclui o primeiro contato elegível, na ordem de nome e depois ID crescente, e atualiza as exceções cadastradas. Não há tabela intermediária de resultados, checkbox ou botão de inclusão de selecionados. Contatos ativos e desativados continuam elegíveis, e CNPJ alfanumérico é aceito conforme a validação do SEI.

CPF e CNPJ admitem somente contatos principais presentes em `md_pet_vinculo.id_contato`. Para CPF, o tipo do contato também deve corresponder ao parâmetro `<SIGLA>_ID_TIPO_CONTATO_USUARIOS_EXTERNOS`, usando a sigla do órgão do sistema, não a do usuário autenticado. CNPJ não recebe esse filtro de tipo. Um contato presente somente como representante não é elegível. A consulta de vínculos acontece em lote, sem exigir o recurso legado `md_pet_vinculacao_listar`. Parâmetro ausente ou inválido gera erro técnico explícito, sem ampliar a pesquisa.

A RN resolve o contato pelos filtros atuais de documento, natureza, tipo quando CPF e presença do vínculo, dentro da transação de inclusão. A página envia somente natureza e documento, sem IDs de contatos escolhidos pelo navegador; a RN recusa uma seleção externa de IDs nessa operação. A escolha ocorre depois de filtrar os vínculos, sem limitar a consulta ao primeiro candidato antes de verificar sua elegibilidade. Nenhum contato elegível gera validação, sem gravar uma exceção. A listagem e a remoção de exceções já cadastradas continuam disponíveis mesmo que o contato deixe de atender aos critérios da inclusão.

A inclusão grava uma única associação por envio e não altera o cadastro do contato. Se o primeiro contato elegível já possuir a exceção, a operação apresenta validação e não tenta incluir outro contato com o mesmo documento. Associações anteriores de outros contatos são preservadas. A remoção exclui somente a associação da lista. Não há edição de contato nem substituição integral da lista. Os agendamentos continuam recuperando o CPF/CNPJ do contato e excluindo esse documento das filas, inclusive registros de outros contatos que o compartilhem, sem alterar a implementação da consulta ou da suspensão.

Após inclusão bem-sucedida, somente a nova associação recebe o destaque amarelo padrão do SEI, pela classe `infraTrAcessada`, preservando a alternância das demais linhas. A RN devolve o identificador gerado no DTO de entrada, sem alterar o retorno da quantidade incluída. A página usa esse identificador, não o documento do contato, para aplicar o destaque. Validações, duplicidades, remoção e abertura normal da listagem não destacam linhas. O destaque acompanha a URL assinada de retorno da inclusão e suas recargas, sem persistência em sessão ou banco e sem CSS próprio. Abrir normalmente a listagem, sem os parâmetros de retorno, remove o destaque e a confirmação anterior.

A verificação do destaque passou em 19 cenários de RN e 35 de página. O navegador isolado, com o CSS real do SEI, confirmou fundo amarelo `rgb(255, 255, 153)` em todas as células somente da nova associação, inclusive entre duas linhas com o mesmo documento. Listagem normal, duplicidade e remoção não receberam destaque. `php -l` passou nos quatro PHP alterados; o gate da página retornou PASS e a comparação da RN não encontrou novos apontamentos. Permanecem os dois A3 e o aviso T5 anteriores, com gate global BLOCK. Não houve acesso ao banco configurado; a homologação autenticada permanece pendente.

A ação **Remover** usa a imagem padrão `remover.svg`, obtida pelo diretório global de SVG do SEI, com a versão de `Icone::VERSAO` e a classe `infraImg`. O botão sem borda ou fundo mantém a confirmação e envia por POST o identificador da exceção ao link assinado de exclusão. A ação continua disponível somente para quem possui permissão de excluir.

O ajuste do ícone passou nos 33 cenários de `php tests/excecoes_suspensao_pagina_test.php`, incluindo o identificador recebido pela remoção, o link assinado e a ausência da ação sem permissão. `php -l` passou na página e no teste; o auditor da página retornou PASS, sem erros ou avisos. O navegador isolado, com Bootstrap 5.3.1 e o SVG real, confirmou imagem carregada, ausência de borda e fundo, cancelamento sem envio e POST com o identificador correto por clique e teclado. Os testes não acessaram o banco; a remoção em sessão autenticada permanece pendente de homologação.

A natureza é escolhida por dois botões de opção: **Pessoa Física** e **Pessoa Jurídica**. O campo aplica a máscara de CPF ou CNPJ durante a digitação e a colagem, atualiza rótulo, limite e teclado conforme a natureza e preserva CNPJ alfanumérico em maiúsculas. Documentos parciais não recebem zeros automaticamente. Após inclusão bem-sucedida, a página limpa documento e natureza, mantendo a mensagem de sucesso e as exceções cadastradas. Em caso de validação, conserva os critérios para correção; incluir sem natureza selecionada gera validação Bootstrap.

Documentos e identificadores inválidos, ausência de contato elegível, duplicidades e associações não encontradas são validações do InfraPHP. A tela exibe suas mensagens em alertas Bootstrap `alert-warning`, sem rastreamento técnico, mantém o formulário disponível para correção e trata o texto como HTML escapado. Falhas técnicas, de permissão, de assinatura do link ou de sessão continuam no tratamento padrão de exceções, sem serem convertidas em validações.

As confirmações de inclusão e remoção são exibidas em alerta Bootstrap verde `alert alert-success`, com `role="status"` e texto tratado por `PaginaSEI::tratarHTML`. O alerta aparece somente após uma operação bem-sucedida, sem substituir as validações `alert-warning`.

Após inclusão ou remoção bem-sucedida, a página responde com HTTP **303 See Other** e redireciona para a listagem assinada por GET. Recarregar essa listagem não reenvia o formulário nem repete a operação. A URL transporta somente um código de resultado validado e, na inclusão, o identificador positivo da associação, sem CPF/CNPJ ou mensagem livre. A confirmação e o destaque são reconstruídos no GET de retorno. Validações continuam na resposta POST para preservar os dados digitados e permitir sua correção.

A correção do recarregamento passou nos 39 cenários de `php tests/excecoes_suspensao_pagina_test.php`, incluindo status 303, URL assinada sem documento, confirmação, destaque, recarga sem nova operação e parâmetros de retorno inválidos. Uma verificação HTTP real e temporária, com substitutos de sessão e RN, confirmou `POST → 303 → GET` na inclusão e na remoção, duas recargas adicionais de cada destino e uma única chamada de escrita por operação. GET de escrita foi recusado e a abertura normal da listagem não manteve confirmação ou destaque. `php -l` passou na página e no teste; Latin-1, abertura `<?php`, ausência de BOM e `git diff --check` foram verificados. O gate `sei-verificacao-pagina`, executado sobre a página existente, retornou PASS, sem erros ou avisos. Não houve acesso ao banco configurado ou à RFB, nem alterações na RN, nos agendamentos ou na release. A homologação em sessão autenticada permanece pendente.

O script SIP concede inicialmente ao perfil **Administrador** os recursos `md_pet_exc_suspensao_auto_listar`, `md_pet_exc_suspensao_auto_selecionar`, `md_pet_exc_suspensao_auto_cadastrar` e `md_pet_exc_suspensao_auto_excluir`. Apenas cadastrar e excluir entram na regra de auditoria `Modulo_Peticionamento_Eletronico`, que é replicada após o registro. A auditoria dessas operações contém contexto e identificadores, não nome ou CPF/CNPJ.

O recurso `_selecionar` continua exigido pelo filtro interno de contatos vinculados, mas não abre mais uma etapa separada de pesquisa nesta página. A inclusão direta exige os recursos `_listar`, `_selecionar` e `_cadastrar`. Usuários sem permissão de inclusão visualizam somente as exceções e as ações permitidas. Este ajuste reutiliza recursos existentes, sem mudar esquema, scripts de release, menus ou versões.

A inclusão direta passou em 19 cenários de `php tests/excecoes_suspensao_test.php`, 34 de `php tests/excecoes_suspensao_pagina_test.php` e 24 de `php tests/excecoes_suspensao_filas_test.php`, sem acessar o banco configurado ou a RFB. Os testes verificam a escolha pelo nome e desempate por ID, o tipo do órgão do sistema para CPF, vínculos atuais, documentos sem elegíveis, duplicidade sem tentar o próximo contato, auditoria sem documento, rollback após escrita, preservação das associações anteriores e consumo dos documentos pelas filas. `php -l` passou na página, na RN e nos dois testes alterados; os fontes permaneceram em Latin-1. O gate da página retornou PASS, sem erros ou avisos. O gate da RN continua BLOCK pelos dois apontamentos A3 e pelo aviso T5 anteriores, sem novos apontamentos. O navegador isolado confirmou uma única ação **Adicionar**, POST assinado por clique e Enter, máscaras de CPF com zero inicial e CNPJ alfanumérico, limpeza após sucesso, retenção após validação, interface de leitura sem controles de inclusão e preservação da remoção por SVG com confirmação. A homologação autenticada e a aprovação integral do gate da RN permanecem pendentes.

A release 4.7.0 exige executar os dois scripts, na ordem SIP e SEI descrita acima. O SEI cria `md_pet_adm_whitelist`, migra os processos da lista antiga para o contexto V/P, preserva o menor identificador por processo e posiciona o sequencial acima do maior identificador antigo. O índice único `i01_md_pet_adm_whitelist` impede repetição de finalidade, tipo de objeto e objeto. A estrutura antiga é removida somente após a migração e a criação do índice. As exceções desta tela usam exclusivamente S/C.

### Consumo pelos agendamentos de CPF/CNPJ

`MdPetAgendamentoAutomaticoRN::recuperarFilaConsultaCpf` exclui os CPFs dos contatos Pessoa Física da lista; `recuperarFilaConsultaCnpj` exclui os CNPJs dos contatos Pessoa Jurídica. A associação permanece por ID de contato, mas a exclusão da fila alcança todo o documento, inclusive outros contatos que compartilhem o CPF/CNPJ. Remover uma associação não libera o documento enquanto outro contato com esse documento continuar na lista.

A leitura interna de `MdPetWhitelistRN::recuperarExcecoesSuspensaoInterno` fixa S/C, carrega os contatos em lote, incluindo desativados, e não exige os recursos da tela administrativa. A listagem administrativa continua exigindo sua permissão e reutiliza essa leitura. Não são reaplicados os critérios de elegibilidade da inclusão, nem criados recursos SIP ou alterações de esquema.

As exclusões são aplicadas por DTO com `OPER_NOT_IN`, antes do limite do lote. CPF mantém o filtro de processo e a ordem crescente da última consulta. CNPJ usa a mesma ordem crescente, só com os vínculos que têm representante ativo, e consulta cada CNPJ uma vez por execução. A fila de CPF usa o documento do representante, enquanto a de CNPJ usa o documento do contato principal. Zeros iniciais e letras do CNPJ são preservados na normalização das exceções. Falhas de leitura são propagadas, sem prosseguir como se a lista estivesse vazia. Alterações da lista são refletidas na próxima recuperação da fila, sem cache entre execuções.

Os documentos excepcionados não passam pela consulta cadastral nem pela suspensão automática por essas filas. Suspensões manuais e outros fluxos permanecem fora deste ajuste. A estrutura S/C da release 4.7.0 deve estar instalada antes de executar os agendamentos com este código; tabela ausente é falha técnica, não ausência de exceções.

### Identidade técnica e integração com o Litigioso

Os agendamentos `ConsultarSituacaoReceitaCpf` e `ConsultarSituacaoReceitaCnpj` executam a recuperação da fila, o processamento RFB, a suspensão e o registro final sob o usuário definido por `MODULO_PETICIONAMENTO_ID_USUARIO_SISTEMA`. A [RN de usuário](rn/MdPetIntUsuarioRN.php) exige um usuário existente, ativo, do tipo sistema e com identificador positivo antes de recuperar a fila. Configuração inválida interrompe o agendamento com erro técnico, sem usar o usuário anterior como alternativa. Agendamentos com quantidade zero continuam sem processamento.

A execução mantém um contexto interno de suspensão automática. O [hook do Litigioso](../litigioso/LitigiosoIntegracao.php) dispensa seu bloqueio de alteração de contato somente quando o Peticionamento está habilitado, esse contexto está ativo e o identificador efetivo da sessão corresponde ao usuário técnico validado. A identificação não depende de sigla, nome, perfil, unidade dispensada ou parâmetro HTTP. Fora desse contexto, inclusive em suspensões manuais e em outras ações do próprio usuário técnico, permanecem as regras anteriores do Litigioso.

Na autorrepresentação, a suspensão continua alterando a **observação do contato** e desativando o **usuário externo**. O contato não é inativado. A liberação do hook não altera esse comportamento, os critérios da RFB ou a lista de exceções.

A geração de documentos usa a simulação oficial de login na unidade geradora do processo. Essa API recebe uma unidade por chamada e consulta seus metadados; não oferece troca de unidade em lote. A resolução e a validação do usuário técnico são reutilizadas dentro da execução. A geração de documento, sozinha, não ativa a dispensa do Litigioso. Os campos simulados da sessão, o estado habilitado e o contexto anterior são restaurados em `finally`, tanto no sucesso quanto em exceções ou erros do PHP, incluindo chamadas aninhadas.

Publique conjuntamente as três RNs alteradas do Peticionamento e a integração do Litigioso. Confirme ambos os módulos em **Infra > Módulos** e a validade do parâmetro do usuário técnico antes da homologação. Este ajuste não cria tabelas, recursos SIP, parâmetros ou versões. Ele não elimina a necessidade de instalar a estrutura da release 4.7.0 nem resolve a pendência do instalador SIP registrada abaixo.

### Agendamento de consulta de CNPJ

O agendamento `MdPetAgendamentoAutomaticoRN::ConsultarSituacaoReceitaCnpj` processa cada CNPJ da fila nesta ordem:

1. Valida o CNPJ. O inválido não vai à Receita Federal e recebe a situação `cnpj_invalido`.
2. Consulta a Receita Federal pela integração "Consultar Dados CNPJ Receita Federal". O CNPJ vai sem formatação e com as letras do CNPJ alfanumérico. Sem usuário externo logado, o solicitante enviado em `cpfUsuario` é o CPF fixo da constante `CPF_SOLICITANTE_SEM_USUARIO_EXTERNO`, porque o WS recusa a consulta sem CPF e o usuário técnico não tem CPF. O WSDL é baixado uma vez por execução.
3. Grava no contato da pessoa jurídica com vinculação o nome, o endereço e a cidade devolvidos, sem escape de HTML, e refaz o índice de pesquisa do contato.
4. Suspende conforme a RN-PET-004 (situação cadastral de suspensão: responsável legal e procurações) ou a RN-PET-005 (responsável legal divergente: só o responsável legal). A suspensão de cada CNPJ, os documentos e o andamento rodam em uma transação. O documento do responsável legal é do tipo de `MODULO_PETICIONAMENTO_ID_SERIE_VINC_SUSPENSAO_AUTOMATICA`, sem assinatura. Os documentos são indexados e os e-mails saem depois do commit.
5. Classifica o resultado do CNPJ: `cnpj_invalido`, `regular`, `suspenso`, `no_vinc_ativo`, `resp_diver_susp`, `erro_ws` ou `erro_suspensao`. A falha do WS (`erro_ws`) é consultada mais uma vez no fim do lote. Depois da última tentativa, o CNPJ recebe a data da consulta e vai para o fim da fila.

No fim, o resumo da execução vai para o log do SEI e, quando a fila não está vazia, por e-mail ao endereço de `SEI_EMAIL_ADMINISTRADOR`. O resumo não traz nome, CPF ou e-mail de pessoas e tem cinco partes: a quantidade de CNPJs por resultado, com rótulo descritivo; os totais de responsáveis legais suspensos, de procurações eletrônicas suspensas, de procurações mantidas ativas nos casos de responsável legal divergente, de documentos gerados e de e-mails de notificação não enviados; a explicação dos dois motivos de suspensão; uma linha por CNPJ com suspensão, falha ou aviso, com a situação cadastral devolvida pela Receita, o motivo, o que foi suspenso, os documentos e a causa da falha, limitada a 300 caracteres; e o tempo de execução.

O agendamento não grava a tabela `md_pet_fila_consulta_rfb`, criada fora do script de release só para acompanhar a execução no banco. O resultado de cada CNPJ fica no resumo. Nenhum código do módulo usa essa tabela, que pode ser removida dos ambientes.

Os testes unitários estão em `tests/unit/sei/web/modulos/peticionamento/rn/MdPetAgendamentoAutomaticoRNTest.php` e `MdPetIntegracaoRNTest.php`. O teste de integração, em `tests/integracao/sei/web/modulos/peticionamento/rn/MdPetAgendamentoAutomaticoRNTest.php`, troca só a Receita pelo retorno do cenário e confere, com banco real, a suspensão, o documento da série de suspensão automática, o andamento, os e-mails depois do commit, o desfazimento na falha e a gravação do contato.

### Validação isolada

Execute o teste de regressão sem banco:

```bash
php /opt/sei/web/modulos/peticionamento/tests/excecoes_suspensao_test.php
php /opt/sei/web/modulos/peticionamento/tests/excecoes_suspensao_pagina_test.php
php /opt/sei/web/modulos/peticionamento/tests/excecoes_suspensao_filas_test.php
php /opt/sei/web/modulos/peticionamento/tests/excecoes_suspensao_sip_test.php
php /opt/sei/web/modulos/peticionamento/tests/suspensao_identidade_test.php
```

Os testes usam os DTOs e o dispatcher transacional reais do InfraPHP com persistência isolada. Cobrem validação de documentos, pesquisa exata, contatos desativados, permissões, seleção adulterada, duplicidade, isolamento das finalidades, auditoria sem documentos, consulta em lote, rollback, dispatch da página, saída HTML e registro SIP idempotente. O lint dos nove PHP e os testes passaram; os auditores não registraram novos achados sobre as cópias de referência. A migração, o índice e as operações foram testados em MySQL e PostgreSQL descartáveis. Esses resultados não substituem a homologação do submenu, dos links assinados, da sessão e da interface em ambiente autorizado; Oracle, SQL Server e a instalação integral permanecem pendentes.

A correção das mensagens de validação foi verificada com sete cenários de RN e 29 cenários de página, incluindo apresentação Bootstrap, mensagens múltiplas, CPF/CNPJ inválidos, saída escapada e distinção entre validações e falhas técnicas. O navegador isolado, com Bootstrap 5.3.1, confirmou o alerta de CPF inválido, o campo editável com o valor digitado e a ausência de rastreamento técnico. O lint dos quatro PHP alterados passou. O auditor da página retornou zero erros e avisos; os dois apontamentos A3 e o aviso T5 preexistentes da RN permanecem sem acréscimos. A apresentação em sessão autenticada ainda exige homologação.

A restrição aos contatos vinculados passou em 15 cenários de RN, com as duas RNs reais, DTOs reais e persistência isolada. O teste reproduziu o retorno indevido antes da correção e passou depois. Inclui documentos compartilhados entre tipos e órgãos, contatos sem vínculo ou apenas representantes, vínculo repetido sem duplicação do resultado, ativos/desativados, revalidação da inclusão, parâmetro ausente/inválido, falha técnica de consulta e manutenção de exceções anteriores. Não houve mudança de esquema, recursos SIP, migração V/P ou versão 4.7.0.

Na verificação dos filtros, `php -l` passou nos três PHP alterados e os 29 cenários da página passaram. A comparação do gate das duas RNs manteve os oito erros e cinco avisos anteriores, sem novos apontamentos. O teste SIP falhou na reexecução da etapa 4.7.0: a consulta do item de integrações exige o rótulo antigo `Integrações`, que já foi renomeado para `Mapeamento das Integrações`. O instalador não foi alterado neste ajuste de pesquisa. Essa falha precisa ser resolvida antes de considerar a release pronta para instalação; homologação autenticada permanece pendente.

O ajuste dos botões de opção, máscaras e limpeza passou em 33 cenários de página e 15 cenários de RN. Os testes de RN foram alinhados à decisão confirmada de manter o **órgão do sistema**, sem alterar a RN. O navegador isolado confirmou digitação e colagem de CPF/CNPJ, letras do CNPJ, zero inicial, documento parcial, troca de natureza, navegação pelo teclado, edição no meio com cursor preservado, limpeza após sucesso e retenção após validação. O gate da página retornou PASS, zero erros e avisos. Não houve acesso ao banco configurado; a homologação autenticada permanece pendente.

O consumo das exceções pelos agendamentos passou em 24 cenários de filas, 15 de RN e 33 de página, sem banco configurado ou chamadas à Receita Federal. Os testes das filas usam as RNs reais de agendamento, whitelist e vínculos, DTOs reais e doubles de persistência, contatos e sessão conferidos por reflexão. Cobrem a exclusão por documento compartilhado, natureza, desativados, normalização, reposição do lote, isolamento S/C, atualização após remoção, recusa administrativa e propagação de falhas. `php -l` passou nas duas RNs alteradas e no novo teste. O gate das duas RNs manteve cinco erros e um aviso preexistentes, sem novos apontamentos. Homologação dos agendamentos em ambiente autorizado permanece pendente; a falha SIP anteriormente registrada não foi corrigida neste ajuste.

O [teste de identidade](tests/suspensao_identidade_test.php) passou em 55 cenários isolados. Usa o código real das entradas dos agendamentos, da geração de documentos, do registro final, da autorrepresentação e do hook do Litigioso, com DTOs reais e substitutos de sessão, configuração, parâmetros, banco e serviços conferidos por reflexão. Verifica identidade desde a primeira fila, configuração inválida antes do processamento, agendamento desabilitado, contexto aninhado, bloqueio fora do escopo, módulo desabilitado, troca de usuário efetivo, preservação das regras anteriores e restauração após falhas de fila, RFB/suspensão, login, documento, persistência, log e erro do PHP. A observação anterior é preservada e somente o usuário externo é desativado. As regressões de filas, RN e página também passaram, respectivamente em 24, 15 e 33 cenários. `php -l` passou nos quatro fontes alterados e no teste; Latin-1 e os bytes externos às alterações foram preservados. A comparação dos gates das três RNs não encontrou novos apontamentos: permanecem 19 erros e cinco avisos preexistentes, portanto o gate global continua BLOCK. A RN de usuário retornou PASS; seu aviso heurístico T5 deixou de aparecer. Não foram executados agendamentos reais, instaladores ou consultas RFB.

Na homologação autorizada, confirme CPF e CNPJ com contatos utilizados pelo Litigioso, a identificação técnica no registro/auditoria, a atualização da observação e a desativação somente do usuário externo. Confirme também que alterações fora dos agendamentos continuam sujeitas ao bloqueio anterior e que a geração de documentos não deixa a sessão em outra unidade ou identidade após sucesso ou falha. Essa homologação e a aprovação integral dos gates permanecem pendentes.

## Formulário do Documento Principal do Peticionamento Interno

A [janela do formulário](md_pet_pi_peticao_formulario.php) resolve o tipo de formulário a partir do `id_serie` configurado no Documento Principal. O identificador retornado pela consulta à série é usado tanto para montar os campos e o JavaScript de validação quanto para processar o salvamento. A página não depende de `id_tipo_formulario` no cadastro administrativo do Documento Principal. Série não encontrada ou sem formulário associado interrompe a operação com erro explícito.

Ao adicionar um formulário ou documento gerado à tabela de documentos na [tela de abertura do Peticionamento Interno](md_pet_pi_peticao_cadastro.php), a coluna **Nome do Arquivo** exibe o nome da série configurada para o Documento Principal. Documentos externos mantêm o nome do arquivo enviado. A estrutura da tabela e os demais dados da linha permanecem inalterados.

A regressão isolada em navegador, com o JavaScript e a montagem da tabela reais, passou em 14 cenários de documentos gerados, formulários e uploads, incluindo nomes com acento e apóstrofo, recarregamento, duplicidade e recusa sem conteúdo ou nível de acesso. A falha do nome gerado foi reproduzida antes da correção. As sete colunas visíveis e os 14 campos serializados foram preservados. O lint PHP e o gate da página passaram, sem erros ou avisos; a homologação em sessão autenticada permanece pendente.

## Verificação XSS antes da conclusão do Peticionamento Externo

No Peticionamento de Processo Novo, no Peticionamento Intercorrente e na Vinculação de Responsável Legal, o conteúdo passa pelo filtro XSS do core (`SeiINT::validarXss`) no momento em que o usuário externo o envia, antes da assinatura que conclui o peticionamento. O filtro usa o nível e as listas do bloco `XSS` da `ConfiguracaoSEI.php`; no nível `N`, nada é verificado. A verificação do core na conclusão continua ativa.

| Momento | Ação | Resultado quando o filtro acusa o conteúdo |
|---|---|---|
| Salvar no editor do Documento Principal (Processo Novo) | `md_pet_editor_montar` | Alerta "O documento possui conteúdo não permitido por restrição de segurança. As alterações não foram salvas." O texto permanece no editor para correção e a sessão mantém a última versão salva. |
| Upload de Documento Principal, Essencial e Complementar (Processo Novo) | `md_pet_usu_ext_upload_doc_principal`, `md_pet_usu_ext_upload_doc_essencial`, `md_pet_usu_ext_upload_doc_complementar` | Alerta "O arquivo possui conteúdo não permitido por restrição de segurança e não foi adicionado." O arquivo é apagado da área temporária e não entra na tabela de documentos. |
| Upload de documento (Intercorrente) | `md_pet_usu_ext_upload_arquivo` | Mesmo alerta e mesmo tratamento do upload acima. |
| Upload de documento (Vinculação de Responsável Legal) | `md_pet_vinc_usu_ext_upload_anexo` | Mesmo alerta e mesmo tratamento do upload acima. |

No upload, o arquivo que o servidor identifica como `text/html` passa pelo filtro, como na conclusão (`AnexoRN::validarConteudo`). O arquivo identificado como `application/pdf` segue a [ADR-PET-001](../../../../../../../../../docs/architecture/adr/peticionamento/ADR-PET-001-verificacao-de-conteudo-em-pdf.md): é lido pelo `Smalot\PdfParser` da InfraPHP e recusado, com o mesmo alerta, quando contém JavaScript (`/JavaScript`, `/JS`) ou as ações `/Launch`, `/SubmitForm` ou `/ImportData`, ou quando o filtro XSS acusa o texto extraído. No nível `N`, o PDF não é verificado. O PDF que o parser não consegue ler, por estar protegido ou fora do padrão, é aceito sem verificação enquanto a flag `PeticionamentoIntegracao::$bolAceitarPdfNaoLido` for `true`; com `false`, é recusado. A verificação do core na conclusão continua sem analisar PDF. Arquivos TXT e de outros tipos seguem sem verificação. A mensagem não mostra nem registra o trecho acusado. Erro do upload do core, como extensão não permitida, chega ao usuário sem alteração, e outro erro da verificação é repassado.

A implementação fica em `PeticionamentoIntegracao::validarXssEditor`, `processarUploadExterno` e `validarXssUpload`. O teste [PeticionamentoIntegracaoTest](../../../../../../../../../tests/unit/sei/web/modulos/peticionamento/PeticionamentoIntegracaoTest.php) cobre 35 cenários nos níveis `N`, `B` e `A`: texto acusado e aceito no editor, HTML acusado e apagado, HTML e TXT aceitos, erro do core repassado, nível inválido e, para PDF, texto acusado e aceito, cada ação recusada, ação em objeto separado, embutida e compactada, nomes codificados com `#xx`, nível `N` e PDF protegido ou fora do padrão conforme a flag. Cada cenário foi visto falhando com cópias quebradas do integrador e passou com o código real. A homologação nos três fluxos, com sessão de usuário externo autenticada, permanece pendente.

### Transação por etapa e conferência na conclusão

Conforme a [ADR-PET-002](../../../../../../../../../docs/architecture/adr/peticionamento/ADR-PET-002-transacoes-por-etapa.md), a conclusão do Processo Novo, do Intercorrente e da Vinculação de Responsável Legal usa uma transação por etapa. No Processo Novo, `MdPetProcessoRN::gerarProcedimento` abre o processo em `gerarProcedimentoInterno` (etapa 1) e inclui os documentos em `incluirDocumentosNoProcedimento` (etapa 2), cada um com sua transação. Assim, a sequência da numeração do processo em `infra_sequencia` fica travada só durante a etapa 1. Se uma etapa posterior falha, `reverterProcedimentoPeticionamento` exclui o processo; se essa exclusão falhar, o processo fica vazio e o erro é registrado em log.

Antes da etapa 1, `PeticionamentoIntegracao::validarXssConclusao` confere o texto do editor do Documento Principal e os arquivos temporários citados no pedido com as mesmas regras do salvar no editor e do upload. O conteúdo acusado é recusado com mensagem de validação, sem abrir processo e sem apagar os arquivos, como "O arquivo "nome" possui conteúdo não permitido por restrição de segurança.". Arquivo inexistente ou fora da pasta temporária fica para as validações do core. A conferência roda em `MdPetProcessoRN::gerarProcedimento`, depois das validações de tags críticas e de localização dos arquivos, em `MdPetVinculoUsuExtRN::gerarProcedimentoVinculo` e em `MdPetVincRepresentantRN::realizarProcessosAlteracaoResponsavelLegal`, antes de encerrar o vínculo anterior. Os campos do documento principal do tipo formulário não passam pela conferência, porque o core não os submete ao filtro XSS na inclusão.

Os testes [PeticionamentoIntegracaoTest](../../../../../../../../../tests/unit/sei/web/modulos/peticionamento/PeticionamentoIntegracaoTest.php), [MdPetProcessoRNTest](../../../../../../../../../tests/unit/sei/web/modulos/peticionamento/rn/MdPetProcessoRNTest.php) e [MdPetVinculoUsuExtRNTest](../../../../../../../../../tests/unit/sei/web/modulos/peticionamento/rn/MdPetVinculoUsuExtRNTest.php) cobrem 14 cenários da conferência: editor e arquivos recusados e aceitos, arquivos mantidos após a recusa, arquivo inexistente e fora da pasta, recusa no Processo Novo sem comando no banco nem transação aberta e leitura da tabela de documentos da Vinculação. Cada cenário foi visto falhando com cópias quebradas e passou com o código real. A fronteira das transações e o rollback compensatório dependem da homologação com usuário externo autenticado, ainda pendente.

## Conversão de exceções conhecidas do core

Conforme a [ADR-PET-003](../../../../../../../../../docs/architecture/adr/peticionamento/ADR-PET-003-conversao-de-excecoes-do-core.md), as páginas de conclusão do Peticionamento Externo convertem em mensagem de validação as exceções do core reconhecidas por `MdPetExcecaoINT`. Sem a conversão, `PaginaSEIExterna::processarExcecao` mostra a página de erro para toda exceção sem validação, como a recusa do filtro XSS do core. A conversão ocorre no `catch` final da página, depois do rollback, com `processarExcecao(MdPetExcecaoINT::converter($e))`. A exceção original fica anexada à validação, e o log registra a mensagem e os detalhes do core.

| Mensagem do core | Mensagem ao usuário externo |
|---|---|
| `SeiINT::$MSG_ERRO_XSS` ("Documento possui conteúdo não permitido") | "O documento possui conteúdo não permitido por restrição de segurança. Revise o conteúdo e tente novamente." |

O conversor procura o trecho na mensagem da exceção e das exceções encadeadas em `getPrevious()`. Validação e exceção fora da lista seguem sem mudança. A lista deve ser revisada a cada atualização do SEI.

| Página | Fluxo |
|---|---|
| `md_pet_usu_ext_concluir.php` | Peticionamento de Processo Novo |
| `md_pet_intercorrente_usu_ext_concluir.php` | Peticionamento Intercorrente |
| `md_pet_vinc_usu_externo_concluir.php` | Vinculação de Responsável Legal |
| `md_pet_vinc_pe_usu_externo_concluir.php` | Emissão de procuração eletrônica |
| `md_pet_vinc_pe_desvinculo_concluir.php` | Revogação ou renúncia de procuração eletrônica |
| `md_pet_procuracao_especial_cadastro.php` | Nova procuração eletrônica; ganhou `try`/`catch` em volta de `gerarProcedimentoVinculoProcuracao` |

Quando `EditorRN::adicionarVersao` falha, `MdPetVinculoUsuExtRN::_atualizarConteudoFormulario` e os métodos `_gerarModeloDesvinculoProcuracao`, `gerarFormularioProcuracaoSimplesControlado` e `gerarFormularioProcuracaoControlado` de `MdPetVinUsuExtProcRN` não encerram mais o script com `die` ou `processarExcecao`. A falha sobe para a transação e, na Vinculação de Responsável Legal, para o rollback compensatório da etapa, que desfaz processo e vínculo antes da mensagem. `MdPetIntCertidaoRN::gerarCertidaoExterna` passou a lançar `InfraException` em vez de chamar `processarExcecao`. No aceite da intimação, uma validação na geração da certidão deixava o aceite seguir sem certidão; agora a falha desfaz o aceite. As rotinas de suspensão e restabelecimento executadas pela administração ficaram fora do escopo, conforme a ADR.

O teste [MdPetExcecaoINTTest](../../../../../../../../../tests/unit/sei/web/modulos/peticionamento/int/MdPetExcecaoINTTest.php) cobre 8 cenários: a recusa XSS do core, sozinha ou encadeada na falha da certidão ou em exceção do PHP, vira validação com a original anexada; a validação do módulo, a validação que repete o texto do core, a falha de banco, a exceção do PHP e o erro do PHP seguem sem mudança. Cada cenário foi visto falhando com cópias quebradas do conversor e passou com o código real. O alerta nas páginas e o rollback dependem da homologação com usuário externo autenticado, ainda pendente.

## Verificação XSS antecipada no Peticionamento Interno

Conforme a [ADR-PET-004](../../../../../../../../../docs/architecture/adr/peticionamento/ADR-PET-004-verificacao-xss-antecipada-no-peticionamento-interno.md), o Peticionamento Interno aplica as regras da verificação XSS do Peticionamento Externo antes da assinatura que conclui o peticionamento: o filtro XSS do core no nível e com as listas do bloco `XSS` da `ConfiguracaoSEI.php` e, para PDF, a verificação da [ADR-PET-001](../../../../../../../../../docs/architecture/adr/peticionamento/ADR-PET-001-verificacao-de-conteudo-em-pdf.md), com a mesma flag `PeticionamentoIntegracao::$bolAceitarPdfNaoLido`.

| Momento | Ponto | Resultado quando o conteúdo é recusado |
|---|---|---|
| Salvar no editor completo (CKEditor 5) | `md_pet_pi_peticao_editor_salvar`, em `MdPetPiPeticaoINT::salvarEditorCompletoDocPrincipal`, por seção | Alerta "O documento possui conteúdo não permitido por restrição de segurança. As alterações não foram salvas." A sessão mantém a última versão salva. |
| Salvar no editor antigo (CKEditor 4) | `md_pet_pi_peticao_editor_montar`, em `md_pet_pi_peticao_editor.php` | Mesmo alerta. O texto permanece no editor para correção e a sessão mantém a última versão salva. |
| Upload do Documento Principal externo e dos anexos | `md_pet_pi_peticao_upload_principal` e `md_pet_pi_peticao_upload_anexo`, em `MdPetPiPeticaoINT::processarUploadArquivo` | Alerta "O arquivo possui conteúdo não permitido por restrição de segurança e não foi adicionado." O arquivo é apagado da área temporária e não é registrado na sessão. |
| Conclusão, antes de abrir o processo | `MdPetPiPeticaoRN::validarXssDocumentos`, chamado em `cadastrarControlado` depois de `validarPeticionamento` | Validação com o nome do arquivo ou do Documento Principal, sem abrir processo. |

Os campos do Documento Principal do tipo formulário e os documentos automáticos configurados pela administração não passam pela verificação. A conversão de exceções conhecidas do core não se aplica ao Peticionamento Interno: a recusa do core que escapar da verificação antecipada chega como página de erro. A conclusão mantém a transação única.

Os testes [MdPetPiPeticaoINTTest](../../../../../../../../../tests/unit/sei/web/modulos/peticionamento/int/MdPetPiPeticaoINTTest.php) e [MdPetPiPeticaoRNTest](../../../../../../../../../tests/unit/sei/web/modulos/peticionamento/rn/MdPetPiPeticaoRNTest.php) cobrem 11 cenários: seção do editor completo recusada nos níveis `B` e `A` sem gravar na sessão, seção comum e nível `N` gravados, seções e texto do editor antigo recusados, anexo e Documento Principal externo HTML recusados, formulário, conteúdo comum e anexo TXT aceitos. Cada cenário foi visto falhando com cópias quebradas e passou com o código real. O salvar no editor antigo e o upload dependem da homologação com usuário interno autenticado, ainda pendente.

## Erros ou Sugestões
1. [Abrir Issue](https://github.com/anatelgovbr/mod-sei-peticionamento/issues) no repositório do GitHub do módulo se ocorrer erro na execução dos scripts de banco do módulo no SEI ou no SIP acima.
2. [Abrir Issue](https://github.com/anatelgovbr/mod-sei-peticionamento/issues) no repositório do GitHub do módulo se ocorrer erro na operação do módulo.
3. Na abertura da Issue utilizar o modelo **"1 - Reportar Erro"**.
