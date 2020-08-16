# Desafio Finnet

>Uma empresa precisa importar faturas por meio de um arquivo CSV e notificar os clientes que ela está disponível para ser paga.
Essa empresa necessita agrupar as faturas pelo CPF ou CNPJ do cliente.
Para cada fatura agrupada o cliente receberá um email informando que a consolidação dessas faturas está disponível.
A empresa pode posteriormente consultar essas faturas disponíveis mediante a autenticação.

### Solução
Com base nos requisitos deste projeto, observamos que:
 - Podemos utilizar o MailTrap para fazer o envio dos emails de notificação;
 - Podemos utilizar JWT para fazer a autenticação da empresa;
 - Não precisa ser uma arquitetura/framework robusto para resolver o problema.

#### Desenho da solução
<p align="center">
  <img src="https://i.imgur.com/KE1cozf.jpg" width="500" /><br>
  (Diagrama de Casos de Uso)
</p>

#### Projeto de Dados
As figuras abaixo mostram a modelagem dos dados, representada com o modelo lógico do banco de dados.

<p align="center">
  <img src="https://i.imgur.com/Mep6x5H.png" width="500" /><br>
  (Modelo Lógico do Banco de Dados)
</p>

#### Ferramentas e Tecnologias Utilizadas

Seguindo esses requisitos, iremos trabalhar com as seguintes ferramentas e tecnologias:
 - PHP 7.2
 - Slim 3 Framework
 - Banco de dados SQLite

Nossas dependências:
```json
"slim/slim": "3.*",
"doctrine/orm": "^2.7",
"oscarotero/psr7-middlewares": "^3.21",
"monolog/monolog": "^2.1",
"firebase/php-jwt": "^5.2",
"tuupola/slim-jwt-auth": "^3.4",
"phpmailer/phpmailer": "^6.1"
```
#### Instalação
Para instalar o sistema basta executar os seguintes comandos:
```bash
#Baixar o projeto
git clone https://github.com/leonammp/desafio-finnet/
cd desafio-finnet

#Instalar as dependências
composer install
```
