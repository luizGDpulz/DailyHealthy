<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../app/Database.php';
require_once __DIR__ . '/../app/SystemCheck.php';

$systemCheck = new SystemCheck();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Diagnóstico do Sistema - DailyHealthy</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen p-8">
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-lg shadow-md p-6">
            <h1 class="text-2xl font-bold mb-6">Diagnóstico do Sistema DailyHealthy</h1>
            
            <div class="mb-8">
                <h2 class="text-lg font-semibold mb-4">Status do Sistema: 
                    <span class="<?= $systemCheck->isSystemOK() ? 'text-green-500' : 'text-red-500' ?>">
                        <?= $systemCheck->isSystemOK() ? 'OK' : 'Problemas Encontrados' ?>
                    </span>
                </h2>
                
                <?php if (!empty($systemCheck->getChecks())): ?>
                    <div class="mb-6">
                        <h3 class="text-md font-medium mb-2 text-green-600">Verificações bem-sucedidas:</h3>
                        <ul class="list-disc pl-5 space-y-1">
                            <?php foreach ($systemCheck->getChecks() as $check): ?>
                                <li class="text-green-600"><?= htmlspecialchars($check) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($systemCheck->getErrors())): ?>
                    <div class="mb-6">
                        <h3 class="text-md font-medium mb-2 text-red-600">Problemas encontrados:</h3>
                        <ul class="list-disc pl-5 space-y-1">
                            <?php foreach ($systemCheck->getErrors() as $error): ?>
                                <li class="text-red-600"><?= htmlspecialchars($error) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="mt-6 space-y-4">
                <h3 class="text-lg font-semibold">Próximos passos:</h3>
                
                <?php if ($systemCheck->isSystemOK()): ?>
                    <p class="text-green-600">
                        ✅ O sistema está configurado corretamente. Você pode:
                    </p>
                    <ul class="list-disc pl-5 space-y-2">
                        <li><a href="<?= url('install') ?>" class="text-blue-500 hover:underline">Executar ou verificar as migrations</a></li>
                        <li><a href="<?= url('login') ?>" class="text-blue-500 hover:underline">Ir para a página de login</a></li>
                        <li><a href="<?= url('') ?>" class="text-blue-500 hover:underline">Acessar o dashboard</a></li>
                    </ul>
                <?php else: ?>
                    <p class="text-red-600">
                        ⚠️ Por favor, corrija os problemas acima antes de continuar.
                    </p>
                    <div class="bg-yellow-50 border border-yellow-200 rounded p-4 mt-4">
                        <h4 class="font-medium text-yellow-800 mb-2">Dicas de resolução:</h4>
                        <ul class="list-disc pl-5 space-y-2 text-yellow-700">
                            <li>Verifique as permissões dos diretórios</li>
                            <li>Configure o mod_rewrite no Apache</li>
                            <li>Verifique as configurações do banco de dados</li>
                            <li>Certifique-se que todas as extensões PHP necessárias estão instaladas</li>
                        </ul>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
