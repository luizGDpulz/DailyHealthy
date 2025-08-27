# 🎯 DailyHealthy - Aplicativo de Hábitos Saudáveis

Um aplicativo web completo para gerenciamento de hábitos saudáveis, desenvolvido em PHP com MySQL e frontend em HTML/CSS/JavaScript vanilla. Projetado especificamente para rodar em servidores Apache (XAMPP).

## ✨ Funcionalidades

### 🔐 Sistema de Autenticação
- Login e registro de usuários
- Sessões seguras com tokens CSRF
- Validação de dados e sanitização

### 📊 Dashboard Interativo
- Estatísticas em tempo real (pontos, streak, hábitos)
- Lista de hábitos diários com checkbox interativo
- Sistema de pontuação automático
- Interface responsiva e moderna

### 🏆 Sistema de Ranking
- Ranking global de usuários por pontuação
- Destaque visual para top 3 (coroa, medalhas)
- Indicação da posição do usuário atual
- Estatísticas detalhadas de cada usuário

### 🎯 Gerenciamento de Hábitos
- Criar, editar e remover hábitos
- Categorização por tipo (saúde, exercício, alimentação, etc.)
- Sistema de pontuação personalizável (5-25 pontos)
- Cores personalizáveis para organização visual
- Histórico de execuções

### 🏅 Sistema de Badges/Conquistas
- Badges automáticos por pontuação e streak
- Badges especiais por comportamentos específicos
- Sistema de progresso para badges não conquistados
- Interface visual atrativa com emojis

### 📱 Design Responsivo
- Interface adaptável para desktop e mobile
- Componentes modernos com animações suaves
- Paleta de cores profissional
- Tipografia otimizada para legibilidade

## 🛠️ Tecnologias Utilizadas

### Backend
- **PHP 7.4+** - Linguagem principal
- **MySQL 5.7+** - Banco de dados
- **PDO** - Abstração de banco de dados
- **Sessions** - Gerenciamento de autenticação

### Frontend
- **HTML5** - Estrutura semântica
- **CSS3** - Estilos modernos com variáveis CSS
- **JavaScript ES6+** - Interatividade e AJAX
- **Fetch API** - Comunicação com backend

### Servidor
- **Apache 2.4+** - Servidor web
- **.htaccess** - Configurações e URLs amigáveis
- **mod_rewrite** - Roteamento de URLs

## 📋 Pré-requisitos

### Para XAMPP (Recomendado)
- XAMPP 7.4+ ou 8.0+
- Apache com mod_rewrite habilitado
- MySQL 5.7+ ou MariaDB 10.3+
- PHP 7.4+ com extensões:
  - PDO
  - PDO_MySQL
  - mbstring
  - json

### Para outros servidores
- Apache 2.4+ ou Nginx
- PHP 7.4+ com extensões listadas acima
- MySQL 5.7+ ou MariaDB 10.3+

## 🚀 Instalação no XAMPP

### 1. Preparar o Ambiente

1. **Baixe e instale o XAMPP:**
   - Acesse: https://www.apachefriends.org/
   - Baixe a versão para seu sistema operacional
   - Instale seguindo as instruções

2. **Inicie os serviços:**
   - Abra o painel de controle do XAMPP
   - Inicie o **Apache** e **MySQL**
   - Verifique se estão rodando (indicadores verdes)

### 2. Instalar a Aplicação

1. **Copie os arquivos:**
   ```bash
   # Copie toda a pasta DailyHealthyXAMPP para:
   # Windows: C:\xampp\htdocs\dailyhealthy\
   # Linux: /opt/lampp/htdocs/dailyhealthy/
   # macOS: /Applications/XAMPP/htdocs/dailyhealthy/
   ```

2. **Configure as permissões (Linux/macOS):**
   ```bash
   sudo chmod -R 755 /opt/lampp/htdocs/dailyhealthy/
   sudo chown -R daemon:daemon /opt/lampp/htdocs/dailyhealthy/
   ```

### 3. Configurar o Banco de Dados

1. **Edite as configurações (se necessário):**
   - Abra `config/config.php`
   - Verifique as configurações do banco:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'dailyhealthy');
   define('DB_USER', 'root');
   define('DB_PASS', ''); // Vazio por padrão no XAMPP
   ```

2. **Execute a configuração inicial:**
   - Acesse: `http://localhost/dailyhealthy/setup.php`
   - Aguarde a conclusão da configuração
   - Verifique se todas as etapas foram concluídas com sucesso

### 4. Testar a Instalação

1. **Acesse a aplicação:**
   - URL: `http://localhost/dailyhealthy/`
   - Você deve ver a tela de login

2. **Faça login com a conta demo:**
   - **Email:** `admin@dailyhealthy.com`
   - **Senha:** `admin123`

3. **Explore as funcionalidades:**
   - Dashboard com hábitos e estatísticas
   - Ranking de usuários
   - Sistema de criação de hábitos

## 📁 Estrutura do Projeto

```
DailyHealthyXAMPP/
├── 📁 app/                     # Classes principais
│   ├── Auth.php               # Sistema de autenticação
│   ├── Database.php           # Conexão e operações de banco
│   ├── Habit.php              # Gerenciamento de hábitos
│   └── User.php               # Gerenciamento de usuários
├── 📁 api/                     # APIs REST
│   ├── auth.php               # API de autenticação
│   ├── badges.php             # API de badges
│   ├── habits.php             # API de hábitos
│   └── ranking.php            # API de ranking
├── 📁 assets/                  # Recursos estáticos
│   ├── 📁 css/
│   │   └── style.css          # Estilos principais
│   └── 📁 js/
│       └── app.js             # JavaScript principal
├── 📁 config/                  # Configurações
│   └── config.php             # Configurações gerais
├── 📁 migrations/              # Migrações do banco
│   ├── migrate.php            # Sistema de migrations
│   ├── 001_create_users.php   # Tabela de usuários
│   ├── 002_create_habits.php  # Tabela de hábitos
│   ├── 003_create_habit_executions.php # Execuções
│   ├── 004_create_badges.php  # Badges/conquistas
│   └── 005_create_user_badges.php # Badges dos usuários
├── .htaccess                   # Configurações Apache
├── index.php                   # Página inicial/login
├── dashboard.php               # Dashboard principal
├── ranking.php                 # Página de ranking
├── login.php                   # Redirecionamento
├── logout.php                  # Logout
├── setup.php                   # Configuração inicial
└── README.md                   # Esta documentação
```

## 🔧 Configurações Avançadas

### URLs Amigáveis

O arquivo `.htaccess` já está configurado com:
- Remoção de `.php` das URLs
- Redirecionamentos automáticos
- Configurações de segurança
- Cache de arquivos estáticos
- Compressão GZIP

### Segurança

- Proteção CSRF em formulários
- Sanitização de dados de entrada
- Validação de sessões
- Headers de segurança
- Bloqueio de arquivos sensíveis

### Performance

- Cache de arquivos estáticos
- Compressão GZIP habilitada
- Otimização de consultas SQL
- Lazy loading de componentes

## 🎮 Como Usar

### 1. Primeiro Acesso
1. Acesse `http://localhost/dailyhealthy/`
2. Faça login com `admin@dailyhealthy.com` / `admin123`
3. Explore o dashboard e funcionalidades

### 2. Gerenciar Hábitos
1. No dashboard, clique em "➕ Novo Hábito"
2. Preencha título, descrição e configurações
3. Escolha pontuação (5-25 pontos) e categoria
4. Selecione uma cor para organização
5. Clique em "Criar Hábito"

### 3. Completar Hábitos
1. No dashboard, clique no círculo ao lado do hábito
2. O hábito será marcado como concluído
3. Pontos serão adicionados automaticamente
4. Streak será atualizado se aplicável

### 4. Acompanhar Progresso
1. Veja suas estatísticas no topo do dashboard
2. Acesse a página "Ranking" para ver sua posição
3. Badges serão conquistados automaticamente

### 5. Criar Novos Usuários
1. Na tela de login, clique em "Criar conta"
2. Preencha os dados solicitados
3. Faça login com a nova conta

## 🔍 Solução de Problemas

### Erro de Conexão com Banco
```
Erro: SQLSTATE[HY000] [1049] Unknown database 'dailyhealthy'
```
**Solução:**
1. Execute `setup.php` novamente
2. Verifique se MySQL está rodando no XAMPP
3. Confirme as configurações em `config/config.php`

### Erro 404 nas URLs
```
Not Found: The requested URL was not found
```
**Solução:**
1. Verifique se `mod_rewrite` está habilitado no Apache
2. Confirme se o arquivo `.htaccess` está presente
3. Teste acessando com `.php` na URL

### Erro de Permissões (Linux/macOS)
```
Permission denied
```
**Solução:**
```bash
sudo chmod -R 755 /opt/lampp/htdocs/dailyhealthy/
sudo chown -R daemon:daemon /opt/lampp/htdocs/dailyhealthy/
```

### Sessão Não Funciona
```
Usuário não autenticado
```
**Solução:**
1. Verifique se cookies estão habilitados
2. Confirme configurações de sessão no PHP
3. Limpe cache e cookies do navegador

## 🔄 Atualizações e Manutenção

### Backup do Banco
```sql
mysqldump -u root -p dailyhealthy > backup_dailyhealthy.sql
```

### Restaurar Backup
```sql
mysql -u root -p dailyhealthy < backup_dailyhealthy.sql
```

### Logs de Erro
- **Apache:** `xampp/apache/logs/error.log`
- **PHP:** `xampp/php/logs/php_error_log`
- **MySQL:** `xampp/mysql/data/*.err`

## 📊 Banco de Dados

### Tabelas Principais

1. **users** - Usuários do sistema
2. **habits** - Hábitos criados pelos usuários
3. **habit_executions** - Registro de execuções diárias
4. **badges** - Badges/conquistas disponíveis
5. **user_badges** - Badges conquistados pelos usuários

### Relacionamentos

- `users` 1:N `habits`
- `users` 1:N `habit_executions`
- `habits` 1:N `habit_executions`
- `users` N:M `badges` (através de `user_badges`)

## 🎨 Personalização

### Cores e Temas
Edite as variáveis CSS em `assets/css/style.css`:
```css
:root {
    --primary-color: #4CAF50;
    --secondary-color: #2196F3;
    --accent-color: #FF9800;
    /* ... outras variáveis */
}
```

### Adicionar Novas Categorias
Edite o select em `dashboard.php`:
```html
<option value="nova_categoria">🆕 Nova Categoria</option>
```

### Novos Badges
Adicione em `migrations/004_create_badges.php` e execute as migrations.

## 🤝 Contribuição

1. Faça um fork do projeto
2. Crie uma branch para sua feature
3. Commit suas mudanças
4. Push para a branch
5. Abra um Pull Request

## 📄 Licença

Este projeto está sob a licença MIT. Veja o arquivo LICENSE para mais detalhes.

## 🆘 Suporte

Para suporte e dúvidas:
1. Verifique a seção "Solução de Problemas"
2. Consulte os logs de erro
3. Abra uma issue no repositório

---

**Desenvolvido com ❤️ para promover hábitos saudáveis!**

🎯 **DailyHealthy** - Transforme sua rotina, um hábito por vez!

