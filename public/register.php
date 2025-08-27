<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - DailyHealthy</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="bg-white p-8 rounded-lg shadow-md w-96 transform transition-all duration-300 hover:scale-105">
        <h1 class="text-2xl font-bold text-center mb-6 text-gray-800">Criar Conta</h1>
        
        <?php if ($flash = getFlashMessage()): ?>
            <div class="mb-4 p-3 rounded <?= $flash['type'] === 'error' ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' ?>">
                <?= $flash['message'] ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="<?= url('auth/register') ?>" class="space-y-4">
            <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
            
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nome</label>
                <input type="text" id="name" name="name" required minlength="2"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" id="email" name="email" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Senha</label>
                <input type="password" id="password" name="password" required minlength="8"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                <p class="mt-1 text-xs text-gray-500">
                    Mínimo 8 caracteres, incluindo maiúsculas, minúsculas e números
                </p>
            </div>
            
            <div>
                <label for="password_confirm" class="block text-sm font-medium text-gray-700 mb-1">Confirmar Senha</label>
                <input type="password" id="password_confirm" name="password_confirm" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            
            <button type="submit"
                    class="w-full bg-blue-500 text-white py-2 rounded-md hover:bg-blue-600 transition-colors">
                Registrar
            </button>
        </form>
        
        <p class="mt-4 text-center text-sm text-gray-600">
            Já tem uma conta? 
            <a href="<?= url('login') ?>" class="text-blue-500 hover:text-blue-600">
                Faça login
            </a>
        </p>
    </div>

    <script>
        document.getElementById('password_confirm').addEventListener('input', function() {
            const password = document.getElementById('password').value;
            if (this.value !== password) {
                this.setCustomValidity('As senhas não coincidem');
            } else {
                this.setCustomValidity('');
            }
        });
        
        document.getElementById('password').addEventListener('input', function() {
            const value = this.value;
            const hasUpperCase = /[A-Z]/.test(value);
            const hasLowerCase = /[a-z]/.test(value);
            const hasNumbers = /\d/.test(value);
            const isLongEnough = value.length >= 8;
            
            if (!(hasUpperCase && hasLowerCase && hasNumbers && isLongEnough)) {
                this.setCustomValidity('A senha deve conter pelo menos 8 caracteres, incluindo maiúsculas, minúsculas e números');
            } else {
                this.setCustomValidity('');
            }
        });
    </script>
    
    <script src="/DailyHealthy/public/assets/js/app.js"></script>
</body>
</html>
