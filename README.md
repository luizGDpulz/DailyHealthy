# DailyHealthy - App Gamificado para Hábitos Saudáveis

## Resumo

O **DailyHealthy** é um sistema CRUD gamificado para acompanhamento de hábitos saudáveis. Os usuários podem criar hábitos, marcar execuções diárias, ganhar pontos, conquistar badges e competir em rankings.

## Tecnologias Utilizadas

### Frontend
- **HTML5** com estrutura semântica
- **Tailwind CSS** para estilização responsiva
- **JavaScript Vanilla** para interatividade
- **CSS3** com animações e gradientes

### Backend
- **PHP 8.x** com PDO
- **MySQL/MariaDB** para banco de dados
- **Arquitetura MVC** simples
- **API RESTful** com endpoints documentados

## Estrutura do Projeto

```
dailyhealthy/
├── backend/
│   ├── .htaccess               # Configuração Apache
│   ├── config.php              # Configuração do banco
│   ├── index.php               # Roteador principal da API
│   └── src/
│       ├── controllers/        # Controladores da API
│       │   ├── AuthController.php
│       │   └── HabitController.php
│       └── models/             # Modelos de dados
│           ├── User.php
│           ├── Habit.php
│           └── HabitExecution.php
├── frontend/
│   ├── .htaccess               # Configuração Apache
│   ├── index.html              # Página principal
│   ├── css/
│   │   └── style.css           # Estilos customizados
│   └── js/
│       └── script.js           # Lógica da aplicação
├── database/
│   └── banco.sql               # Script de criação do banco
└── README.md
```

## Instalação e Configuração

### Pré-requisitos
- **XAMPP** ou **Laragon** (Apache + MySQL + PHP)
- **Navegador moderno** com suporte a ES6+

### Passo 1: Configurar o Banco de Dados

1. Inicie o Apache e MySQL no XAMPP/Laragon
2. Acesse o phpMyAdmin (http://localhost/phpmyadmin)
3. Crie um banco de dados chamado `dailyhealthy_db`
4. Importe o arquivo `database/banco.sql`

### Passo 2: Configurar o Backend

1. Copie a pasta `backend` para o diretório do servidor web (htdocs no XAMPP)
2. Edite o arquivo `backend/config.php` se necessário:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'dailyhealthy_db');
   define('DB_USER', 'root');
   define('DB_PASS', ''); // Senha do MySQL (geralmente vazia no XAMPP)
   ```

### Passo 3: Configurar o Frontend

1. Copie a pasta `frontend` para o diretório do servidor web
2. Acesse http://localhost/frontend/
3. O aplicativo estará funcionando!

## API Endpoints

### Base URL
```
http://localhost/backend/api/v1
```

### Autenticação

#### POST /auth/register
Registra um novo usuário.

**Body:**
```json
{
  "name": "João Silva",
  "email": "joao@example.com",
  "password": "senha123"
}
```

#### POST /auth/login
Realiza login do usuário.

**Body:**
```json
{
  "email": "joao@example.com",
  "password": "senha123"
}
```

### Hábitos

#### GET /habits
Lista todos os hábitos.

#### POST /habits
Cria um novo hábito.

#### POST /habits/{id}/execute
Marca a execução de um hábito.

## Sistema de Pontuação

### Pontos Base
- Cada hábito possui um valor base de pontos (`points_base`)
- Ao executar um hábito, o usuário recebe os pontos base

### Bônus de Streak
- **3 dias consecutivos**: +5 pontos extras
- **7 dias consecutivos**: +20 pontos extras
- **14 dias consecutivos**: +50 pontos extras

### Regras
- Apenas uma execução por hábito por dia
- Streaks são calculados automaticamente
- Pontos são atualizados em tempo real

## Funcionalidades

### ✅ Implementado
- [x] Interface responsiva com Tailwind CSS
- [x] Autenticação (registro/login)
- [x] Sistema de hábitos com dados mock
- [x] Cálculo de pontos em tempo real
- [x] Notificações visuais animadas
- [x] Persistência de sessão no localStorage
- [x] Backend PHP com API RESTful
- [x] Arquivos .htaccess configurados

### 🚧 Para Implementar
- [ ] Integração completa frontend-backend
- [ ] Sistema de badges
- [ ] Ranking de usuários
- [ ] Histórico detalhado

## Deploy

### Servidor Local (XAMPP/Laragon)
1. Copie as pastas `backend` e `frontend` para `htdocs`
2. Configure o banco de dados
3. Acesse http://localhost/frontend/

### Servidor Web
1. Faça upload dos arquivos via FTP
2. Configure o banco de dados no hosting
3. Ajuste as URLs da API no `script.js`

## Arquivos .htaccess

### Backend (.htaccess)
- Configuração de CORS
- Redirecionamento para index.php
- Headers de API

### Frontend (.htaccess)
- Roteamento client-side
- Compressão de arquivos
- Cache headers

## Troubleshooting

### Erro de CORS
- Verifique se o arquivo `.htaccess` está no backend
- Confirme se o mod_rewrite está habilitado no Apache

### Banco não conecta
- Verifique as credenciais em `config.php`
- Confirme se o MySQL está rodando
- Certifique-se que o banco `dailyhealthy_db` existe

### Frontend não carrega
- Verifique se o Tailwind CSS está carregando
- Confirme se o JavaScript não tem erros no console
- Teste se o Apache está servindo arquivos estáticos

## Licença

Este projeto está sob a licença MIT.

## Créditos

- **Desenvolvido por**: Manus AI
- **Framework CSS**: Tailwind CSS
- **Tecnologias**: HTML5, CSS3, JavaScript, PHP, MySQL

