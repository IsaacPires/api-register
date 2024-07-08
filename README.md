# Instruções 

Esta é uma API dividida em dois pontos principais:

- User: Aqui você encontrará métodos para realizar o cadastro de usuários na API e gerar seus tokens de acesso;
- Client: Aqui estarão todos os endpoints para cadastrar seus clientes. Você pode realizar todas as operações propostas por um CRUD.

## Requisitos

- Rodar Migrations;
- Criar "env.testing" para conseguir realizar todos os testes;

## Primeiros passos

Esta é uma API de fácil utilização, porém, para a maioria das ações, é necessário autenticar-se utilizando um token.

user/register: Este endpoint permite criar seu acesso. Após o sucesso na criação do seu cadastro, você receberá imediatamente um token. Utilize-o para acessar qualquer endpoint de manipulação de clientes.

O token possui um prazo de validade, sendo necessário fazer login novamente através do endpoint user/login para obter um novo token.

## Documentação detalhada

https://documenter.getpostman.com/view/22555988/2sA3e2eUo5#ed095ae3-ab5e-4d72-b4a8-f65cf21ea042

## Infos adicionais

- PHP 7^;
- Laravel 8^.
