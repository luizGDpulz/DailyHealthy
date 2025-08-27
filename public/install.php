<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instalação - DailyHealthy</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="bg-white p-8 rounded-lg shadow-md w-96">
        <h1 class="text-2xl font-bold text-center mb-6">Instalação DailyHealthy</h1>
        
        <div class="space-y-4">
            <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-md">
                <p class="text-yellow-700 text-sm">
                    ⚠️ Esta página deve ser acessada apenas durante a instalação inicial do sistema.
                </p>
            </div>
            
            <form method="POST" action="/DailyHealthy/public/install/run" class="space-y-4">
                <div>
                    <label for="install_key" class="block text-sm font-medium text-gray-700 mb-1">
                        Chave de Instalação
                    </label>
                    <input type="password" id="install_key" name="install_key" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                
                <button type="submit"
                        class="w-full bg-blue-500 text-white py-2 rounded-md hover:bg-blue-600 transition-colors"
                        onclick="this.disabled=true;this.innerHTML='Instalando...';">
                    Executar Instalação
                </button>
            </form>
        </div>
    </div>
</body>
</html>
