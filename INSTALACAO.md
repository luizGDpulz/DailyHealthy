# Instalação DailyHealthy - Raiz do Localhost

## Instalação Rápida

### 1. Extrair Arquivos
- Extraia o ZIP `dailyhealthy-raiz.zip`
- Copie a pasta `dailyhealthy` para `htdocs` (XAMPP) ou `www` (Laragon)

### 2. Configurar Banco
- Inicie Apache e MySQL
- Acesse phpMyAdmin: `http://localhost/phpmyadmin`
- Crie banco: `dailyhealthy_db`
- Importe: `dailyhealthy/database/banco.sql`

### 3. Acessar App
- URL: `http://localhost/dailyhealthy/`
- Login simulado: qualquer email/senha
- Backend: `http://localhost/dailyhealthy/backend/`

## Estrutura Final
```
htdocs/
└── dailyhealthy/
    ├── index.html          # Frontend principal
    ├── css/               # Estilos
    ├── js/                # JavaScript
    ├── backend/           # API PHP
    ├── database/          # SQL
    └── .htaccess          # Configuração Apache
```

## URLs
- **App**: http://localhost/dailyhealthy/
- **API**: http://localhost/dailyhealthy/backend/
- **Banco**: phpMyAdmin

Pronto para usar! 🚀

