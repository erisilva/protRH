<p align="center"><img src="http://www.contagem.mg.gov.br/novoportal/wp-content/themes/pmc/images/logo-prefeitura-contagem.png"></p>

## Sobre

Sistema de gestão e controle e tramitação de protocolos, memorandos, ofícios e solicitações para SMS (Secretaria Municipal de Saúde) de Contagem-MG.

O ProtRH foi constuído com a framework [Laravel](https://laravel.com/), na versão 5.7 e usa como front-end [Bootstrap 4.3](https://getbootstrap.com/).

Faz uso também das seguintes bibliotecas:

- [simple-qrcode](https://github.com/SimpleSoftwareIO/simple-qrcode)
- [laravel-fpdf](https://github.com/codedge/laravel-fpdf)
- [typeahead](https://github.com/corejavascript/typeahead.js)
- [bootstrap-datepicker](https://github.com/uxsolutions/bootstrap-datepicker)

## Requisitos

Os requisitos para executar esse sistema pode ser encontrado na [documentação oficial do laravel](https://laravel.com/docs/5.7):

- PHP >= 7.1.3
- OpenSSL PHP Extension
- PDO PHP Extension
- Mbstring PHP Extension
- Tokenizer PHP Extension
- XML PHP Extension
- Ctype PHP Extension
- JSON PHP Extension
- BCMath PHP Extension

## Instalação

Executar a migração das tabelas com o comando seed:

php artisan migrate --seed

Serão criados 4 usuários de acesso ao sistema, cada um com um perfíl de acesso diferente.

Login: adm@mail.com senha:123456, acesso total.
Login: gerente@mail.com senha:123456, acesso restrito.
Login: operador@mail.com senha:123456, acesso restrito, não pode excluir registros.
Login: leitor@mail.com senha: 123456, somente consulta.

## Funcionalidades

- Funcionários
- Setores
- Protocolos
- Memorandos
- Ofícios
- Solicitações
- Tramitação dos documentos
- Anexação de arquivos

## Prefeitura Municipal de Contagem

[www.contagem.mg.gov.br](http://www.contagem.mg.gov.br/novoportal/)

## Contribuições

Caso queira contribuir com melhorias para esse sistema basta enviar um e-mail para erivelton.silva@contagem.mg.gov.br com suas solicitações, ficarei grato com sua ajuda.

## Licenças

O sistema de protocolos é código aberto licenciado sob a [licença MIT](https://opensource.org/licenses/MIT).
