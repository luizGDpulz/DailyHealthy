<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - DailyHealthy</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="bg-white p-8 rounded-lg shadow-md w-96 transform transition-all duration-300 hover:scale-105">
        <h1 class="text-2xl font-bold text-center mb-6 text-gray-800">DailyHealthy</h1>
        
        <?php if ($flash = getFlashMessage()): ?>
            <div class="mb-4 p-3 rounded <?= $flash['type'] === 'error' ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' ?>">
                <?= $flash['message'] ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="<?= url('auth/login') ?>" class="space-y-4">
            <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
            
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" id="email" name="email" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Senha</label>
                <input type="password" id="password" name="password" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            
            <div class="flex items-center">
                <input type="checkbox" id="remember" name="remember"
                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                <label for="remember" class="ml-2 block text-sm text-gray-700">
                    Lembrar-me
                </label>
            </div>
            
            <button type="submit"
                    class="w-full bg-blue-500 text-white py-2 rounded-md hover:bg-blue-600 transition-colors">
                Entrar
            </button>
        </form>
        
        <p class="mt-4 text-center text-sm text-gray-600">
            Não tem uma conta? 
            <a href="<?= url('register') ?>" class="text-blue-500 hover:text-blue-600">
                Registre-se
            </a>
        </p>
    </div>

    <script src="/DailyHealthy/public/assets/js/app.js"></script>
</body>
</html>
