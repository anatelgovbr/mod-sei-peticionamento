# Módulo Peticionamento e Intimação Eletrônicos do SEI
- Data de Criação: 30/09/2016
- Versão: 1.0.0

## Requisitos:
- SEI 2.6.0.A13 instalada (verificar valor da constante de versão do SEI no arquivo sei/SEI.php).
- ANTES de executar os scripts de instalação (itens 5 e 6 abaixo), o usuário de acesso aos bancos de dados do SEI e SIP, constante nos arquivos ConfiguracaoSEI.php e ConfiguracaoSip.php, deverá ter permissão de acesso total ao banco de dados, permitindo, por exemplo, criação e exclusão de tabelas.

## Procedimentos para Instalação:

1. Fazer backup dos banco de dados do SEI e SIP.
2. Carregar os arquivos do módulo localizados na pasta "/sei/institucional/peticionamento" e os scripts de instalação/atualização "/sei/sei_atualizar_versao_modulo_peticionamento.php" e "/sip/sip_atualizar_versao_modulo_peticionamento.php".
3. Editar o arquivo "sei/ConfiguracaoSEI.php", tomando o cuidado de usar editor que não altere o charset do arquivo, para adicionar a referência ao módulo Peticionamento na chave 'Modulos' abaixo da chave 'SEI':

		'SEI' => array(
			'URL' => 'http://[Servidor_PHP]sei',
			'Producao' => false,
			'RepositorioArquivos' => '/var/sei/arquivos',
			'Modulos' => array(),
			),

		==> Adicionar a referência ao módulo Peticionamento na array da chave 'Modulos' indicada acima:
			
			'Modulos' => array('Peticionamento' => dirname(__FILE__).'/institucional/peticionamento'),

4. Logar no SEI e no SIP com Usuário com perfil "Administrador".
	
5. Rodar via navegador ou por linha de comando no servidor o script:

        http://[Servidor_PHP]/sei/sei_atualizar_versao_modulo_peticionamento.php
	
6. Rodar via navegador ou por linha de comando no servidor o script:

        http://[Servidor_PHP]/sip/sip_atualizar_versao_modulo_peticionamento.php
    - IMPORTANTE 1: Na execução dos dois scripts acima, ao final deve constar o termo "FIM". Do contrário, o script não foi executado até o final e algum dado não foi inserido/atualizado no banco de dados correspondente, devendo recuperar o backup e repetir o procedimento.
	- IMPORTANTE 2: Depois da execução com sucesso, fazer logoff no SEI e fazer login novamente utilizando Usuário com Perfil "Administrador" e tentar acessar os menus de Administração do módulo: Administração > Peticionamento Eletrônico.
	- IMPORTANTE 3: Depois da execução com sucesso, ainda, verificar no SEI e no SIP no menu Infra > Parâmetros se consta o parâmetro "VERSAO_MODULO_PETICIONAMENTO" com o valor da última versão do módulo.

7. Recomenda-se que, após a instalação/atualização do módulo, os scripts "/sei/sei_atualizar_versao_modulo_peticionamento.php" e "/sip/sip_atualizar_versao_modulo_peticionamento.php" sejam removidos do servidor.
	
8. Imediatamente após a instalação é necessário realizar as parametrizações do módulo em Administração > Peticionamento Eletrônico, para que o módulo seja utilizado adequadamente pelos Usuários Externos na tela de Acesso Externo do SEI:

        http://[Servidor_PHP]/sei/controlador_externo.php?acao=usuario_externo_logar&id_orgao_acesso_externo=0

9. É imprescindível ativar no SIP para o Perfil "Administrador" do SEI os seguintes menus:
	- Para cadastrar "Cargos" que serão utilizados pelos Usuários Externos na seleção do "Cargo/Função" na assinatura de cada Peticionamento e na seleção de "Cargo" no cadastro de novos Interessados, ativar os menus:
		- "Contextos/Contatos / Cargos / Listar" (recurso "cargo_listar")
		- "Contextos/Contatos / Cargos / Reativar" (recurso "cargo_reativar")
	- Para cadastrar "Tratamentos" que serão utilizados pelos Usuários Externos na seleção de "Tratamento" no cadastro de novos Interessados, ativar os menus:
		- "Contextos/Contatos / Tratamentos / Listar" (recurso "tratamento_listar")
		- "Contextos/Contatos / Tratamentos / Reativar" (recurso "tratamento_reativar")
	- Para cadastrar "Vocativos" que serão utilizados pelos Usuários Externos na seleção de "Vocativo" no cadastro de novos Interessados, ativar os menus:
		- "Contextos/Contatos / Vocativos / Listar" (recurso "vocativo_listar")
		- "Contextos/Contatos / Vocativos / Reativar" (recurso "vocativo_reativar")
	- Para cadastrar "Tipos de Contexto" que serão utilizados na Administração > Peticionamento Eletrônico > Tipos de Contatos Permitidos e, consequentemente, pelos Usuários Externos no cadastro e seleção de Interessados, ativar os menus:
		- "Contextos/Contatos / Tipos de Contexto / Listar" (recurso "tipo_contexto_contato_listar")
		- "Contextos/Contatos / Tipos de Contexto / Reativar" (recurso "tipo_contexto_contato_reativar")

10. Não é aconselhável dar publicidade a registros de indisponibilidades do SEI até que o módulo possua funcionalidades afetas a Intimação Eletrônica, prevista para a versão 2.0. De qualquer forma, segue URL da página pública que lista os cadastrados realizados no menu Administração > Peticionamento Eletrônico > Indisponibilidades do SEI:  

        http://[Servidor_PHP]/sei/institucional/peticionamento/indisponibilidade_peticionamento_usuario_externo_lista.php?acao_externa=indisponibilidade_peticionamento_usuario_externo_listar&id_orgao_acesso_externo=0

Em caso de erro durante a execução do script verificar (lendo as mensagens de erro e no SEI em Infra > Log e no SIP em Infra > Log) se a causa é algum problema na infra-estrutura local. Neste caso, após a correção, restaurar o backup do banco de dados e executar novamente os scripts indicados nos itens 5 e 6 acima.

Caso não seja possível identificar a causa, entrar em contato com o desenvolvedor responsável por esta versão do módulo: Marcelo Bezerra - marcelo.bezerra@cast.com.br