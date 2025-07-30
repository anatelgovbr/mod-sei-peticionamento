# Manual do Webservice do Módulo Peticionamento

 * Endereço do WSDL: http://[dominio_servidor]/sei/controlador_ws.php?servico=wspeticionamento 
 * Recomendado utilizar o software SOAP-UI para testes: http://sourceforge.net/projects/soapui/files/soapui/
 * Todas as operações abaixo somente funcionam se o Serviço correspondente do Sistema indicado possuir pelo menos a operação "Listar Constatos" no menu Administração > Sistemas.
 
| Observações Gerais |
| ---- |
| Os métodos abaixo documentados somente funcionarão se o Serviço correspondente do Sistema indicado possuir pelo menos a operação "Listar Constatos" no menu Administração > Sistemas. |

# Sumário das Operações Disponíveis
- **[Consultar Usuário Externo](#1-consultar-usuário-externo)**: Verifica se uma determinada pessoa física possui login ativo e liberado como Usuário Externo no SEI.
- **Listar Poderes Legais**: Lista os tipos de poderes legais que podem ser utilizados na emissão de Procurações Eletrônicas Simples geradas no SEI.
- **Listar Representação de Pessoa Física**: Lista os representantes de determinada pessoa física outorgante, se houver emitido representação no SEI.
- **Listar Representação de Pessoa Jurídica**: Lista os representantes de determinada pessoa jurídica outorgante, se houver emitido representação no SEI.
- **Listar Representados**: Lista todos os representados por determinada pessoa física, se alguém houver emissão de representação outorgando poderes para ela no SEI.
- **Listar Representantes**: Lista todos os representantes e representados que possuem alguma emissão de representação outorgando poderes no SEI.
- **Listar Situações de Representação**: Lista os tipos de situação que existem sobre as representações geradas no SEI (S=Suspensa, A=Ativa, C=Renunciada, R=Revogada, T=Substituída, V=Vencida, I=Inativa).
- **Listar Tipos de Representação**: Lista os tipos de representação que existem sobre as representações geradas no SEI (L=Responsável Legal, E=Procurador Especial, S=Procurador Simples, U=Autorrepresentação).
- **Listar Usuários Externos**: Lista todos os Usuários Externos cadastrados no SEI.

## 1. Consultar Usuário Externo
Verifica se uma determinada pessoa física possui login ativo e liberado como Usuário Externo no SEI.
### Método “consultarUsuarioExterno”:

| Parâmetros de Entrada |  |
| ---- | ---- |
| SiglaSistema | Valor informado no cadastro do Sistema cliente no SEI no menu Administração > Sistemas. |
| IdentificacaoServico | Valor informado no cadastro do Serviço ou a Chave de Acesso correspondente para o Sistema cliente no SEI no menu Administração > Sistemas. Próxima versão do SEI somente aceitará integrações por Chave de Acesso. |
| Email | **Opcional**. Endereço de e-mail indicado pelo Usuário Externo em seu cadastro no SEI para fins de autenticação. Caso seja informado, tem que corresponder ao e-mail vinculado ao CPF indicado (se não corresponder retornará erro indicando “*E-mail informado não corresponde ao registrado no cadastro do Usuário Externo no SEI*”). |
| Cpf | CPF, sem formatação e incluindo zeros à esquerda, para consulta se existe cadastro correspondente como Usuário Externo no SEI. |

| Parâmetros de Saída |  |
| ---- | ---- |
| UsuarioExterno | Uma lista de ocorrências da Estrutura de Dados UsuarioExterno. |

| Observações |
| ---- |
| O usuário será listado sempre que indicar um CPF que conste na lista de Usuários Externos do SEI. O sistema cliente deverá verificar dois parâmetros da estrutura de dados de retorno para confirmar que o Usuário Externo de fato está com cadastro regular, se a “SituacaoAtivo” está “S” (ativo) e se “LiberacaoCadastro” está “L” (liberado), pois se o cadastro estiver desativado (SituacaoAtivo=N) o usuário externo não conseguirá logar na tela de acesso externo mesmo que esteja liberado (LiberacaoCadastro=L). |

### Regras de Negócio:
 * Se a SiglaSistema e/ou IdentificacaoServico não forem válidos, o webservice retorna as mensagens padrão a respeito.
 * Se o CPF informado não tiver cadastro como Usuário Externo no SEI o webservice retorna a mensagem “*Não existe cadastro de Usuário Externo no SEI com o CPF informado*”.
 * Se o CPF informado não for válido, ou seja, não passar na validação de sua estrutura (dígito verificador inválido), o webservice retorna a mensagem “*Número de CPF inválido*”.
 * Se o E-mail informado não passar na validação de formato (não pode ter espaços e tem que ter @), o webservice retorna a mensagem “*E-mail inválido*”.
 * Se o CPF informado for de Usuário Externo com cadastro localizado, mas, mesmo sendo opcional, o e-mail indicado em conjunto no chamado não corresponder ao cadastrado no SEI (quando quiser fazer dupla validação), o webservice retorna a mensagem “*E-mail informado não corresponde ao registrado no cadastro do Usuário Externo no SEI*”.
 * Demais regras devem ser implementadas pelo sistema cliente da integração, combinando os dados retornados, especialmente referente aos dados de “SituacaoAtivo” e “LiberacaoCadastro” conforme estrutura de dados “UsuarioExterno” abaixo especificada.

### Estrutura de Dados "UsuarioExterno":

| Dado | Descrição |
| ---- | ---- |
| IdUsuario | Id interno de identificação do usuário no SEI. |
| Email | Endereço de e-mail utilizado pelo Usuário Externo para acesso à tela de Acesso Externo do SEI, indicado quando efetivou seu cadastro no SEI como Usuário Externo. |
| Nome | Nome do Usuário Externo. |
| Cpf | Número do CPF do Usuário Externo (sem formatação). |
| Rg | Número do RG. |
| OrgaoExpedidor | Órgão Expedidor do RG. |
| Telefone | Telefone. |
| Endereco | Endereço. |
| Bairro | Bairro. |
| SiglaUf | Sigla da Unidade da Federação. |
| NomeCidade | Nome da Cidade. |
| Cep | CEP do endereço. |
| DataCadastro | Data na qual o Usuário Externo efetivou o cadastro no SEI. |
| SituacaoAtivo | Estado do cadastro do Usuário Externo (S=Ativado e N=Desativado, sendo que este estado do cadastro é independente de sua liberação, ou seja, mesmo liberado, se o cadastro estiver desativado o usuário não consegue mais ter acesso externo ao SEI). |
| LiberacaoCadastro | Estado da aprovação do cadastro do Usuário Externo (L=Liberado e P=Pendente). |

## 2. Listar Poderes Legais
Lista os tipos de poderes legais que podem ser utilizados na emissão de Procurações Eletrônicas Simples geradas no SEI.
### Método “listarPoderesLegais”:

| Parâmetros de Entrada |  |
| ---- | ---- |
| SiglaSistema | Valor informado no cadastro do Sistema cliente no SEI no menu Administração > Sistemas. |
| IdentificacaoServico | Valor informado no cadastro do Serviço ou a Chave de Acesso correspondente para o Sistema cliente no SEI no menu Administração > Sistemas. Próxima versão do SEI somente aceitará integrações por Chave de Acesso. |

| Parâmetros de Saída |  |
| ---- | ---- |
| PoderesLegais | Uma lista de ocorrências da estrutura [PoderesLegais](#estrutura-de-dados-podereslegais). |

## 3. Listar Representação de Pessoa Física
Lista os representantes de determinada pessoa física outorgante, se houver emitido representação no SEI.
### Método “listarRepresentacaoPessoaFisica”:

| Parâmetros de Entrada |  |
| ---- | ---- |
| SiglaSistema | Valor informado no cadastro do Sistema cliente no SEI no menu Administração > Sistemas. |
| IdentificacaoServico | Valor informado no cadastro do Serviço ou a Chave de Acesso correspondente para o Sistema cliente no SEI no menu Administração > Sistemas. Próxima versão do SEI somente aceitará integrações por Chave de Acesso. |
| CpfOutorgante | CPF do Outorgante, sem formatação e incluindo zeros à esquerda, para consulta se existe cadastro correspondente como Usuário Externo no SEI. |
| StaSituacao | Estado da representação (A=Ativo, S=Suspenso, R=Revogado, C=Renunciado, V=Vencido, T=Substituído, I=Inativo) |

### Regras de Negócio:
 * Se a SiglaSistema e/ou IdentificacaoServico não forem válidos, o webservice retorna as mensagens padrão a respeito.
 * Se o CPF informado não tiver cadastro como Usuário Externo no SEI o webservice retorna a mensagem “*Não existe cadastro de Usuário Externo no SEI com o CPF informado*”.
 * Se o CPF informado não for válido, ou seja, não passar na validação de sua estrutura (dígito verificador inválido), o webservice retorna a mensagem “*Número de CPF inválido*”.
 * Demais regras devem ser implementadas pelo sistema cliente da integração, combinando os dados retornados, especialmente referente aos dados de “SituacaoAtivo” e “LiberacaoCadastro” conforme estrutura de dados “UsuarioExterno” abaixo especificada.

| Parâmetros de Saída |  |
| ---- | ---- |
| RepresentacaoPessoaFisica | Uma lista de ocorrências da Estrutura de Dados RepresentacaoPessoaFisica. |

### Estrutura de Dados "RepresentacaoPessoaFisica":

| Dado | Descrição |
| ---- | ---- |
| Cpf | Número do CPF do Usuário Externo (sem formatação). |
| Nome | Nome do Usuário Externo. |
| Email | Endereço de e-mail utilizado pelo Usuário Externo para acesso à tela de Acesso Externo do SEI, indicado quando efetivou seu cadastro no SEI como Usuário Externo.. |
| StaSituacao | Estado do cadastro do Usuário Externo (S=Ativado e N=Desativado, sendo que este estado do cadastro é independente de sua liberação, ou seja, mesmo liberado, se o cadastro estiver desativado o usuário não consegue mais ter acesso externo ao SEI). |
| StaTipoRepresentacao | Estado da aprovação do cadastro do Usuário Externo (L=Liberado e P=Pendente). |
| DataLimite | Data limite da representação. Retorna vazio caso seja representação por tempo indeterminado. |
| ProcessosAbrangencia | Estrutura de Dados [ProcessosAbrangencia](#estrutura-de-dados-processosabrangencia). |
| TipoPoderesLegais |  Uma lista de ocorrências da Estrutura de Dados [PoderesLegais](#estrutura-de-dados-podereslegais). |

## 4. Listar Representação de Pessoa Jurídica
Lista os representantes de determinada pessoa jurídica outorgante, se houver emitido representação no SEI.
### Método “listarRepresentacaoPessoaJuridica”:

| Parâmetros de Entrada |  |
| ---- | ---- |
| SiglaSistema | Valor informado no cadastro do Sistema cliente no SEI no menu Administração > Sistemas. |
| IdentificacaoServico | Valor informado no cadastro do Serviço ou a Chave de Acesso correspondente para o Sistema cliente no SEI no menu Administração > Sistemas. Próxima versão do SEI somente aceitará integrações por Chave de Acesso. |
| CnpjOutorgante | CNPJ do Outorgante, sem formatação e incluindo zeros à esquerda, para consulta se existe cadastro correspondente como Usuário Externo no SEI. |
| StaSituacao | Estado da representação (A=Ativo, S=Suspenso, R=Revogada, C=Renunciada, V=Vencida, T=Substituída, I=Inativo). |

### Regras de Negócio:
 * Se a SiglaSistema e/ou IdentificacaoServico não forem válidos, o webservice retorna as mensagens padrão a respeito.
 * Se o CNPJ informado não for válido, ou seja, não passar na validação de sua estrutura (dígito verificador inválido), o webservice retorna a mensagem “*Número de CNPJ inválido*”.
 * Demais regras devem ser implementadas pelo sistema cliente da integração, combinando os dados retornados, especialmente referente aos dados de “SituacaoAtivo” e “LiberacaoCadastro” conforme estrutura de dados “UsuarioExterno” abaixo especificada.

| Parâmetros de Saída |  |
| ---- | ---- |
| parametros | Uma lista de ocorrências da Estrutura de Dados RepresentacaoPessoaJuridica. |

### Estrutura de Dados "RepresentacaoPessoaJuridica":

| Dado | Descrição |
| ---- | ---- |
| Cpf | Número do CPF do Usuário Externo (sem formatação). |
| Nome | Nome do Usuário Externo. |
| Email | Endereço de e-mail utilizado pelo Usuário Externo para acesso à tela de Acesso Externo do SEI, indicado quando efetivou seu cadastro no SEI como Usuário Externo. |
| StaSituacao | Estado do cadastro do Usuário Externo (S=Ativado e N=Desativado, sendo que este estado do cadastro é independente de sua liberação, ou seja, mesmo liberado, se o cadastro estiver desativado o usuário não consegue mais ter acesso externo ao SEI). |
| StaTipoRepresentacao | Estado da aprovação do cadastro do Usuário Externo (L=Liberado e P=Pendente). |
| DataLimite | Data limite da representação. Retorna vazio caso seja representação por tempo indeterminado. |
| ProcessosAbrangencia | Estrutura de Dados [ProcessosAbrangencia](#estrutura-de-dados-processosabrangencia). |
| TipoPoderesLegais | Uma lista de ocorrências da Estrutura de Dados [PoderesLegais](#estrutura-de-dados-podereslegais). |

## 5. Listar Representados
Lista todos os representados por determinada pessoa física, se alguém houver emissão de representação outorgando poderes para ela no SEI.
### Método “listarRepresentados”:

| Parâmetros de Entrada |  |
| ---- | ---- |
| SiglaSistema | Valor informado no cadastro do Sistema cliente no SEI no menu Administração > Sistemas. |
| IdentificacaoServico | Valor informado no cadastro do Serviço ou a Chave de Acesso correspondente para o Sistema cliente no SEI no menu Administração > Sistemas. Próxima versão do SEI somente aceitará integrações por Chave de Acesso. |
| Cpf | CPF do Representante, sem formatação e incluindo zeros à esquerda, para consulta se existe cadastro correspondente como Usuário Externo no SEI. |
| StaSituacao | **Opcional**. Situação da Representação (A=Ativo, S=Suspenso, R=Revogada, C=Renunciada, V=Vencida, T=Substituída, I=Inativo). |

### Regras de Negócio:
 * Se a SiglaSistema e/ou IdentificacaoServico não forem válidos, o webservice retorna as mensagens padrão a respeito.
 * Se o CPF informado não tiver cadastro como Usuário Externo no SEI o webservice retorna a mensagem “*Não existe cadastro de Usuário Externo no SEI com o CPF informado*”.
 * Se o CPF informado não for válido, ou seja, não passar na validação de sua estrutura (dígito verificador inválido), o webservice retorna a mensagem “*Número de CPF inválido*”.
 
| Parâmetros de Saída |  |
| ---- | ---- |
| Representados | Uma lista de ocorrências da estrutura Representados. |

### Estrutura de Dados "Representados":

| Dado | Descrição |
| ---- | ---- |
| CnpjCpf | CPF ou CNPJ do Representado. |
| RazaoSocial | Razão Social do Representado. |
| DataLimite | Data limite da representação. Retorna vazio caso seja representação por tempo indeterminado. |
| Representante | Uma ocorrência da estrutura Representante. |

### Estrutura de Dados "Representante":

| Dado | Descrição |
| ---- | ---- |
| Nome | Nome do Representante. |
| Cpf | CPF do Representante. |
| Email | Endereço de e-mail utilizado pelo Usuário Externo para acesso à tela de Acesso Externo do SEI, indicado quando efetivou seu cadastro no SEI como Usuário Externo. |
| StaSituacao | Situação do Representante (A=Ativo, S=Suspenso, R=Revogado, C=Renunciado, V=Vencido, T=Substituído, I=Inativo). |
| StaTipoRepresentacao | Tipo da Representação (L=Responsável Legal, E=Procurador Especial, C=Procurador, S=Procurador Simples, U=Autorrepresentação) |
| ProcessosAbrangencia | Estrutura de Dados [ProcessosAbrangencia](#estrutura-de-dados-processosabrangencia). |
| TipoPoderesLegais | Uma lista de ocorrências da Estrutura de Dados [PoderesLegais](#estrutura-de-dados-podereslegais). |

## 6. Listar Representantes
Lista todos os representantes e representados que possuem alguma emissão de representação outorgando poderes no SEI.
### Método “listarRepresentantes”:

| Parâmetros de Entrada |  |
| ---- | ---- |
| SiglaSistema | Valor informado no cadastro do Sistema cliente no SEI no menu Administração > Sistemas. |
| IdentificacaoServico | Valor informado no cadastro do Serviço ou a Chave de Acesso correspondente para o Sistema cliente no SEI no menu Administração > Sistemas. Próxima versão do SEI somente aceitará integrações por Chave de Acesso. |
| StaSituacao | Situação da Representação (A=Ativo, S=Suspensa, R=Revogada, C=Renunciada, V=Vencida, T=Substituída, I=Inativa). |
| Pagina | **Opcional**. Número da página para paginação dos resultados. Caso suprimido valor para este parâmetro será mostrada a página 1. |

| Parâmetros de Saída |  |
| ---- | ---- |
| Representantes | Uma lista de ocorrências da estrutura RepresentantesItens. |

### Estrutura de Dados "RepresentantesItens":

| Dado | Descrição |
| ---- | ---- |
| TipoVinculo | Tipo da Natureza do Vínculo (J=Pessoa Jurídica, F=Pessoa Física) |
| CnpjRepresentado | CNPJ do Representado caso o Tipo de Vínculo seja de Pessoa Jurídica (com formatação). |
| RazaoSocialRepresentado | Razão Social do Representado caso o Tipo de Vínculo seja de Pessoa Jurídica. |
| CpfRepresentado | CPF do Representado caso o Tipo de Vínculo seja de Pessoa Física (com formatação). |
| NomeRepresentado | Nome do Representado caso o Tipo de Vínculo seja de Pessoa Física. |
| EmailRepresentante | Endereço de e-mail utilizado pelo Usuário Externo do Representante para acesso à tela de Acesso Externo do SEI, indicado quando efetivou seu cadastro no SEI como Usuário Externo. |
| StaSituacao | Situação da Representação (A=Ativo, S=Suspenso, R=Revogada, C=Renunciada, V=Vencida, T=Substituída, I=Inativo). |
| StaTipoRepresentacao | Tipo da Representação (L=Responsável Legal, E=Procurador Especial, C=Procurador, S=Procurador Simples, U=Autorrepresentação) |
| DataLimite | Data limite da vigência da Representação. |
| ProcessosAbrangencia | Estrutura de Dados [ProcessosAbrangencia](#estrutura-de-dados-processosabrangencia). |
| TipoPoderesLegais | Uma lista de ocorrências da Estrutura de Dados [PoderesLegais](#estrutura-de-dados-podereslegais). |

## 7. Listar Situações de Representação
Lista os tipos de situação que existem sobre as representações geradas no SEI (S=Suspensa, A=Ativa, C=Renunciada, R=Revogada, T=Substituída, V=Vencida, I=Inativa).
### Método “listarSituacoesRepresentacao”:

| Parâmetros de Entrada |  |
| ---- | ---- |
| SiglaSistema | Valor informado no cadastro do Sistema cliente no SEI no menu Administração > Sistemas. |
| IdentificacaoServico | Valor informado no cadastro do Serviço ou a Chave de Acesso correspondente para o Sistema cliente no SEI no menu Administração > Sistemas. Próxima versão do SEI somente aceitará integrações por Chave de Acesso. |

| Parâmetros de Saída |  |
| ---- | ---- |
| parametros | Uma lista de ocorrências da estrutura SituacoesRepresentacao. |

### Estrutura de Dados "SituacoesRepresentacao":

| Dado | Descrição |
| ---- | ---- |
| StaEstado | Identificador do Estado da Representação (S=Suspensa, A=Ativa, C=Renunciada, R=Revogada, T=Substituída, V=Vencida, I=Inativa). |
| Nome | Nome do Estado da Representação. |

## 8. Listar Tipos de Representação
Lista os tipos de representação que existem sobre as representações geradas no SEI (L=Responsável Legal, E=Procurador Especial, S=Procurador Simples, U=Autorrepresentação).
### Método “listarTiposRepresentacao”:

| Parâmetros de Entrada |  |
| ---- | ---- |
| SiglaSistema | Valor informado no cadastro do Sistema cliente no SEI no menu Administração > Sistemas. |
| IdentificacaoServico | Valor informado no cadastro do Serviço ou a Chave de Acesso correspondente para o Sistema cliente no SEI no menu Administração > Sistemas. Próxima versão do SEI somente aceitará integrações por Chave de Acesso. |

| Parâmetros de Saída |  |
| ---- | ---- |
| UsuarioExterno | Uma lista de ocorrências da Estrutura de Dados TiposRepresentacao. |

### Estrutura de Dados "TiposRepresentacao":

| Dado | Descrição |
| ---- | ---- |
| Nome | Nome do Tipo de Representação. |
| StrTipoRepresentacao | Identificador do Tipo de Representação (L=Responsável Legal, E=Procurador Especial, S=Procurador Simples, U=Autorrepresentação). |

## 9. Listar Usuários Externos
Lista todos os Usuários Externos cadastrados no SEI.
### Método “listarUsuariosExternos”:

| Parâmetros de Entrada |  |
| ---- | ---- |
| SiglaSistema | Valor informado no cadastro do Sistema cliente no SEI no menu Administração > Sistemas. |
| IdentificacaoServico | Valor informado no cadastro do Serviço ou a Chave de Acesso correspondente para o Sistema cliente no SEI no menu Administração > Sistemas. Próxima versão do SEI somente aceitará integrações por Chave de Acesso. |
| StaSituacao | Situação do cadastro do Usuário Externo (S=Ativado e N=Desativado, sendo que este estado do cadastro é independente de sua liberação, ou seja, mesmo liberado, se o cadastro estiver desativado o usuário não consegue mais ter acesso externo ao SEI). |
| LiberacaoCadastro | Estado da aprovação do cadastro do Usuário Externo (L=Liberado e P=Pendente). |
| Página | **Opcional**. Número da página para paginação dos resultados. Caso suprimido valor para este parâmetro será mostrada a página 1. |

| Parâmetros de Saída |  |
| ---- | ---- |
| UsuarioExterno | Uma lista de ocorrências da Estrutura de Dados UsuarioExterno. |

### Estrutura de Dados "UsuariosExternos":

| Dado | Descrição |
| ---- | ---- |
| IdUsuario | Id interno de identificação do usuário no SEI. |
| Nome | Nome do Usuário Externo. |
| Email | Endereço de e-mail utilizado pelo Usuário Externo para acesso à tela de Acesso Externo do SEI, indicado quando efetivou seu cadastro no SEI como Usuário Externo. |
| SituacaoAtivo | Estado do cadastro do Usuário Externo (S=Ativado e N=Desativado). |
| LiberacaoCadastro | Estado da aprovação do cadastro do Usuário Externo (L=Liberado e P=Pendente). |
| DataCadastro | Data na qual o Usuário Externo efetivou o cadastro no SEI. |

## Outras Estruturas de Dados:

### Estrutura de Dados "ProcessosAbrangencia":

| Dado | Descrição |
| ---- | ---- |
| ProtocoloFormatado | Número do Processo SEI formatado |

### Estrutura de Dados "PoderesLegais":

| Dado | Descrição |
| ---- | ---- |
| IdTipoPoderLegal | Id interno de identificação do Poder Legal no SEI |
| Nome | Nome do Poder Legal |
| SinAtivo | Estado do cadastro do Poder Legal (S=Ativado e N=Desativado). **Observação:** Pode estar presente ou não no retorno da consulta |
