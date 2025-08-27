# 🚀 Guia de Instalação no XAMPP - DailyHealthy

Este guia fornece instruções passo a passo para instalar e configurar o DailyHealthy no XAMPP.

## 📋 Pré-requisitos

### 1. XAMPP Instalado
- **Windows:** XAMPP 7.4+ ou 8.0+
- **Linux:** XAMPP for Linux 7.4+
- **macOS:** XAMPP for OS X 7.4+

### 2. Serviços Necessários
- ✅ Apache (com mod_rewrite)
- ✅ MySQL ou MariaDB
- ✅ PHP 7.4+ com extensões PDO e PDO_MySQL

## 🔧 Passo a Passo da Instalação

### Passo 1: Preparar o XAMPP

1. **Baixar e Instalar XAMPP:**
   - Acesse: https://www.apachefriends.org/
   - Baixe a versão adequada para seu sistema
   - Execute a instalação seguindo as instruções

2. **Iniciar Serviços:**
   - Abra o painel de controle do XAMPP
   - Clique em "Start" para **Apache**
   - Clique em "Start" para **MySQL**
   - Verifique se ambos mostram status verde

3. **Testar Instalação:**
   - Abra o navegador
   - Acesse: `http://localhost/`
   - Deve aparecer a página de boas-vindas do XAMPP

### Passo 2: Copiar Arquivos da Aplicação

1. **Localizar Pasta htdocs:**
   - **Windows:** `C:\xampp\htdocs\`
   - **Linux:** `/opt/lampp/htdocs/`
   - **macOS:** `/Applications/XAMPP/htdocs/`

2. **Criar Pasta do Projeto:**
   ```bash
   # Navegue até htdocs e crie a pasta
   mkdir dailyhealthy
   ```

3. **Copiar Todos os Arquivos:**
   - Copie todo o conteúdo da pasta `DailyHealthyXAMPP` para `htdocs/dailyhealthy/`
   - Certifique-se de que todos os arquivos e pastas foram copiados:
     ```
     dailyhealthy/
     ├── app/
     ├── api/
     ├── assets/
     ├── config/
     ├── migrations/
     ├── .htaccess
     ├── index.php
     ├── dashboard.php
     ├── ranking.php
     ├── setup.php
     └── README.md
     ```

### Passo 3: Configurar Permissões (Linux/macOS)

```bash
# Definir permissões corretas
sudo chmod -R 755 /opt/lampp/htdocs/dailyhealthy/
sudo chown -R daemon:daemon /opt/lampp/htdocs/dailyhealthy/

# Ou para usuário atual
sudo chown -R $USER:$USER /opt/lampp/htdocs/dailyhealthy/
```

### Passo 4: Verificar Configurações PHP

1. **Verificar Extensões:**
   - Acesse: `http://localhost/dashboard/phpinfo.php`
   - Ou crie um arquivo temporário:
   ```php
   <?php phpinfo(); ?>
   ```
   - Verifique se estão habilitadas:
     - ✅ PDO
     - ✅ pdo_mysql
     - ✅ mbstring
     - ✅ json

2. **Configurações Recomendadas:**
   ```ini
   ; No arquivo php.ini
   upload_max_filesize = 10M
   post_max_size = 10M
   max_execution_time = 300
   memory_limit = 256M
   ```

### Passo 5: Configurar Banco de Dados

1. **Acessar phpMyAdmin:**
   - URL: `http://localhost/phpmyadmin/`
   - Usuário: `root`
   - Senha: (deixe em branco por padrão)

2. **Verificar Configurações (Opcional):**
   - Edite `config/config.php` se necessário:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'dailyhealthy');
   define('DB_USER', 'root');
   define('DB_PASS', ''); // Vazio no XAMPP padrão
   ```

### Passo 6: Executar Configuração Inicial

1. **Acessar Script de Setup:**
   - URL: `http://localhost/dailyhealthy/setup.php`
   - Aguarde o carregamento completo

2. **Verificar Execução:**
   - ✅ Conexão com MySQL estabelecida
   - ✅ Banco de dados criado
   - ✅ Migrations executadas
   - ✅ Dados de exemplo criados

3. **Resultado Esperado:**
   ```
   ✅ Configuração concluída com sucesso!
   
   Usuários criados: 9
   Hábitos criados: 21
   Badges criados: 13
   ```

### Passo 7: Testar a Aplicação

1. **Acessar Página Inicial:**
   - URL: `http://localhost/dailyhealthy/`
   - Deve aparecer a tela de login

2. **Fazer Login de Teste:**
   - **Email:** `admin@dailyhealthy.com`
   - **Senha:** `admin123`

3. **Verificar Funcionalidades:**
   - ✅ Dashboard carrega com estatísticas
   - ✅ Lista de hábitos aparece
   - ✅ Possível marcar/desmarcar hábitos
   - ✅ Página de ranking funciona
   - ✅ Criação de novos hábitos

## 🔧 Configurações Avançadas

### Habilitar mod_rewrite (se necessário)

**Windows:**
1. Edite `xampp/apache/conf/httpd.conf`
2. Descomente a linha:
   ```apache
   LoadModule rewrite_module modules/mod_rewrite.so
   ```
3. Reinicie o Apache

**Linux:**
```bash
sudo a2enmod rewrite
sudo service apache2 restart
```

### Configurar Virtual Host (Opcional)

1. **Editar hosts do sistema:**
   ```
   # Windows: C:\Windows\System32\drivers\etc\hosts
   # Linux/macOS: /etc/hosts
   127.0.0.1 dailyhealthy.local
   ```

2. **Configurar Virtual Host:**
   ```apache
   # Em xampp/apache/conf/extra/httpd-vhosts.conf
   <VirtualHost *:80>
       DocumentRoot "C:/xampp/htdocs/dailyhealthy"
       ServerName dailyhealthy.local
       <Directory "C:/xampp/htdocs/dailyhealthy">
           AllowOverride All
           Require all granted
       </Directory>
   </VirtualHost>
   ```

3. **Acessar via:** `http://dailyhealthy.local/`

## 🚨 Solução de Problemas Comuns

### Erro: "Apache não inicia"
```
Port 80 in use by "Unable to open process" with PID 4!
```
**Solução:**
1. Mude a porta do Apache para 8080
2. Ou pare o serviço que usa a porta 80 (IIS, Skype)

### Erro: "MySQL não inicia"
```
Port 3306 in use
```
**Solução:**
1. Mude a porta do MySQL para 3307
2. Ou pare outros serviços MySQL

### Erro: "Página não encontrada"
```
Object not found! Error 404
```
**Solução:**
1. Verifique se mod_rewrite está habilitado
2. Confirme se .htaccess está presente
3. Teste com: `http://localhost/dailyhealthy/index.php`

### Erro: "Conexão com banco falhou"
```
SQLSTATE[HY000] [1049] Unknown database
```
**Solução:**
1. Execute `setup.php` novamente
2. Verifique se MySQL está rodando
3. Confirme configurações em `config/config.php`

### Erro: "Permissão negada" (Linux/macOS)
```
Permission denied
```
**Solução:**
```bash
sudo chmod -R 755 /opt/lampp/htdocs/dailyhealthy/
sudo chown -R daemon:daemon /opt/lampp/htdocs/dailyhealthy/
```

## 📊 Verificação Final

### Checklist de Funcionamento

- [ ] XAMPP Apache e MySQL rodando
- [ ] Acesso a `http://localhost/dailyhealthy/` funciona
- [ ] Login com admin@dailyhealthy.com/admin123 funciona
- [ ] Dashboard carrega com estatísticas
- [ ] Possível marcar/desmarcar hábitos
- [ ] Página de ranking acessível
- [ ] Criação de novos hábitos funciona
- [ ] URLs amigáveis funcionam (sem .php)

### URLs de Teste

- **Página inicial:** `http://localhost/dailyhealthy/`
- **Dashboard:** `http://localhost/dailyhealthy/dashboard`
- **Ranking:** `http://localhost/dailyhealthy/ranking`
- **API Auth:** `http://localhost/dailyhealthy/api/auth`
- **phpMyAdmin:** `http://localhost/phpmyadmin/`

## 🎉 Próximos Passos

1. **Explore a aplicação** com a conta admin
2. **Crie novos usuários** para testar
3. **Adicione hábitos personalizados**
4. **Monitore o ranking** conforme usa
5. **Personalize** cores e categorias conforme necessário

## 📞 Suporte

Se encontrar problemas:
1. Verifique os logs do Apache: `xampp/apache/logs/error.log`
2. Verifique os logs do MySQL: `xampp/mysql/data/*.err`
3. Consulte a documentação completa no `README.md`

---

**🎯 DailyHealthy está pronto para transformar seus hábitos!**

