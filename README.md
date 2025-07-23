<p align="center">
    <a href="https://laravel.com" target="_blank">
        <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo">
    </a>
</p>

<h2 align="center">Sistema de Processo Seletivo - UEAP</h2>

<p align="center">
    Aplicação web desenvolvida em Laravel para gerenciar os processos seletivos da Universidade do Estado do Amapá (UEAP), permitindo o cadastro de editais, inscrições de candidatos e acompanhamento das etapas do certame.
</p>

---

## ⚙️ Requisitos

- PHP 8.3.2 ou superior
- Composer
- Mysql 7
- Extensões PHP habilitadas:
  - `zip`

---

## 🚀 Instalação

Siga os passos abaixo para configurar o ambiente de desenvolvimento:

1. **Clone o repositório**
   ```bash
   git clone https://github.com/seu-usuario/nome-do-repositorio.git
   cd nome-do-repositorio

2. **Instale as dependências**
    ```bash
    composer install
    ```

    ### **Gere a chave da aplicação**

    ```bash
    php artisan key:generate
    ```

    ### **Link simbólico para o storage**

    ```bash
    php artisan storage:link
    ```

    ---

    ### **Crie o arquivo `.env`**

    Copie o arquivo de exemplo e edite conforme necessário:

    ```bash
    cp .env.example .env
    ```

    ---

    ### **Configure o banco de dados no `.env`**

    Edite os seguintes campos no arquivo `.env`:

    ```env
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=
    DB_USERNAME=
    DB_PASSWORD=
    ```

    ---

    ### **Rodar migrations**

    ```bash
    php artisan migrate
    ```

    ---

    ### **Inicie o servidor**

    ```bash
    php artisan serve
    ```