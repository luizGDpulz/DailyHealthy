<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - DailyHealthy</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <!-- Header -->
    <header class="bg-white shadow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex justify-between items-center">
                <div class="flex items-center">
                    <h1 class="text-2xl font-bold text-gray-800">DailyHealthy</h1>
                    <nav class="ml-8 space-x-4">
                        <a href="<?= url('') ?>" class="text-blue-500">Dashboard</a>
                        <a href="<?= url('habits') ?>" class="text-gray-600 hover:text-gray-800">Hábitos</a>
                        <a href="<?= url('ranking') ?>" class="text-gray-600 hover:text-gray-800">Ranking</a>
                    </nav>
                </div>
                
                <div class="flex items-center space-x-4">
                    <div class="flex items-center space-x-2">
                        <span class="text-sm text-gray-600">Nível:</span>
                        <span class="font-medium <?= getLevelColorClass($_SESSION['user_level']) ?>">
                            <?= ucfirst($_SESSION['user_level']) ?>
                        </span>
                    </div>
                    
                    <div class="flex items-center space-x-2">
                        <span class="text-sm text-gray-600">Pontos:</span>
                        <span id="userPoints" class="font-medium text-blue-600" data-points="<?= $stats['total_points'] ?>">
                            <?= formatPoints($stats['total_points']) ?>
                        </span>
                    </div>
                    
                    <div class="relative">
                        <button id="userMenuBtn" class="flex items-center space-x-2">
                            <img src="/DailyHealthy/public/assets/images/avatars/<?= $_SESSION['user_avatar'] ?? 'default.jpg' ?>"
                                 alt="Avatar" class="w-8 h-8 rounded-full">
                            <span class="text-gray-700"><?= $_SESSION['user_name'] ?></span>
                        </button>
                        
                        <div id="userMenu" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1">
                            <a href="<?= url('profile') ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                Perfil
                            </a>
                            <a href="<?= url('auth/logout') ?>" class="block px-4 py-2 text-sm text-red-600 hover:bg-gray-100">
                                Sair
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Main Stats -->
            <div class="col-span-2 space-y-6">
                <!-- Stats Cards -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-medium text-gray-900">Hábitos Ativos</h3>
                        <p class="mt-2 text-3xl font-bold text-blue-600"><?= count($habits) ?></p>
                    </div>
                    
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-medium text-gray-900">Streak Atual</h3>
                        <div class="mt-2 flex items-baseline">
                            <p class="text-3xl font-bold text-green-600"><?= $stats['current_streak'] ?? 0 ?></p>
                            <p class="ml-2 text-sm text-gray-500">dias</p>
                        </div>
                    </div>
                    
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-medium text-gray-900">Melhor Streak</h3>
                        <div class="mt-2 flex items-baseline">
                            <p class="text-3xl font-bold text-purple-600"><?= $stats['best_streak'] ?? 0 ?></p>
                            <p class="ml-2 text-sm text-gray-500">dias</p>
                        </div>
                    </div>
                </div>
                
                <!-- Habits List -->
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h2 class="text-lg font-medium text-gray-900">Hábitos de Hoje</h2>
                            <a href="/DailyHealthy/public/habits/create" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 transition-colors">
                                Novo Hábito
                            </a>
                        </div>
                        
                        <?php if (empty($habits)): ?>
                            <p class="text-gray-500 text-center py-4">
                                Você ainda não tem hábitos cadastrados.
                            </p>
                        <?php else: ?>
                            <div class="space-y-4">
                                <?php foreach ($habits as $habit): ?>
                                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                        <div>
                                            <h3 class="font-medium text-gray-900"><?= $habit['title'] ?></h3>
                                            <p class="text-sm text-gray-500"><?= $habit['description'] ?></p>
                                        </div>
                                        
                                        <button onclick="executeHabit(<?= $habit['id'] ?>)"
                                                data-habit-id="<?= $habit['id'] ?>"
                                                class="<?= $habit['executed_today'] ? 'bg-green-500 cursor-default' : 'bg-blue-500 hover:bg-blue-600' ?> text-white px-4 py-2 rounded-md transition-colors"
                                                <?= $habit['executed_today'] ? 'disabled' : '' ?>>
                                            <?= $habit['executed_today'] ? 'Concluído' : 'Concluir' ?>
                                        </button>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Calendar -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-lg font-medium text-gray-900 mb-4">Calendário</h2>
                    <div id="calendar"></div>
                </div>
                
                <!-- Top Users -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-lg font-medium text-gray-900 mb-4">Top 5 Usuários</h2>
                    <div class="space-y-3">
                        <?php foreach ($topUsers as $index => $user): ?>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <span class="text-lg font-bold <?= $index === 0 ? 'text-yellow-500' : 'text-gray-400' ?>">
                                        #<?= $index + 1 ?>
                                    </span>
                                    <span class="text-gray-700"><?= $user['name'] ?></span>
                                </div>
                                <span class="font-medium text-blue-600">
                                    <?= formatPoints($user['points']) ?>
                                </span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <!-- Recent Badges -->
                <?php if (!empty($badges)): ?>
                    <div class="bg-white rounded-lg shadow p-6">
                        <h2 class="text-lg font-medium text-gray-900 mb-4">Conquistas Recentes</h2>
                        <div class="grid grid-cols-3 gap-4">
                            <?php foreach (array_slice($badges, 0, 6) as $badge): ?>
                                <div class="text-center" data-tooltip="<?= $badge['name'] ?>">
                                    <img src="/DailyHealthy/public/assets/images/<?= $badge['icon_path'] ?>"
                                         alt="<?= $badge['name'] ?>"
                                         class="w-12 h-12 mx-auto">
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <script src="/DailyHealthy/public/assets/js/app.js"></script>
    <script>
        // User menu toggle
        const userMenuBtn = document.getElementById('userMenuBtn');
        const userMenu = document.getElementById('userMenu');
        
        userMenuBtn.addEventListener('click', () => {
            userMenu.classList.toggle('hidden');
        });
        
        document.addEventListener('click', (e) => {
            if (!userMenuBtn.contains(e.target) && !userMenu.contains(e.target)) {
                userMenu.classList.add('hidden');
            }
        });
    </script>
</body>
</html>
