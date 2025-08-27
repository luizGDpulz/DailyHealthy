<?php
/**
 * DailyHealthy - Página de Ranking
 */

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/app/Auth.php';
require_once __DIR__ . '/app/User.php';

// Verificar autenticação
Auth::requireAuth();

$currentUser = Auth::getCurrentUser();
$pageTitle = 'Ranking - ' . SITE_NAME;
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="assets/images/favicon.ico">
    
    <!-- CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <div class="logo-icon">🎯</div>
                    <span><?php echo SITE_NAME; ?></span>
                </div>
                
                <nav class="nav">
                    <a href="dashboard" class="nav-link">Dashboard</a>
                    <a href="ranking" class="nav-link active">Ranking</a>
                </nav>
                
                <div class="user-info">
                    <div class="user-stats">
                        <div class="user-name" id="user-name"><?php echo htmlspecialchars($currentUser['name']); ?></div>
                        <div class="user-points" id="user-points"><?php echo $currentUser['points']; ?> pontos</div>
                    </div>
                    <button class="btn btn-outline btn-sm" id="logout-btn">Sair</button>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="container" style="padding-top: var(--spacing-xl); padding-bottom: var(--spacing-xl);">
        <!-- Page Header -->
        <div class="text-center" style="margin-bottom: var(--spacing-2xl);">
            <h1 style="font-size: 3rem; margin-bottom: var(--spacing-md);">🏆</h1>
            <h2>Ranking de Usuários</h2>
            <p style="color: var(--gray-600); font-size: 1.125rem;">
                Veja como você se compara com outros usuários do DailyHealthy
            </p>
        </div>

        <!-- Ranking Stats -->
        <div class="stats-grid" style="margin-bottom: var(--spacing-2xl);">
            <div class="stat-card">
                <div class="stat-icon primary">👑</div>
                <div>
                    <div class="stat-value" id="user-position">#<?php echo User::getUserRankPosition($currentUser['id']); ?></div>
                    <div class="stat-label">Sua Posição</div>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon accent">🏆</div>
                <div>
                    <div class="stat-value" id="user-points-display"><?php echo $currentUser['points']; ?></div>
                    <div class="stat-label">Seus Pontos</div>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon secondary">🔥</div>
                <div>
                    <div class="stat-value" id="user-streak-display"><?php echo $currentUser['streak']; ?></div>
                    <div class="stat-label">Seu Streak</div>
                </div>
            </div>
        </div>

        <!-- Ranking List -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">🏅 Top Usuários</h3>
                <p class="card-description">Ranking baseado na pontuação total e streak consecutivo</p>
            </div>
            
            <div class="card-content">
                <div id="ranking-list" class="ranking-list">
                    <!-- Ranking será carregado via JavaScript -->
                    <div class="text-center" style="padding: 2rem;">
                        <div class="loading" style="margin: 0 auto var(--spacing-md);"></div>
                        <p style="color: var(--gray-600);">Carregando ranking...</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Ranking Info -->
        <div class="grid grid-cols-2" style="margin-top: var(--spacing-xl);">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">🎯 Como Funciona</h3>
                </div>
                <div class="card-content">
                    <div style="display: flex; flex-direction: column; gap: var(--spacing-md);">
                        <div>
                            <strong>Pontuação:</strong>
                            <p style="margin: var(--spacing-xs) 0 0; color: var(--gray-600); font-size: 0.875rem;">
                                Complete hábitos diariamente para ganhar pontos. Hábitos mais difíceis valem mais pontos.
                            </p>
                        </div>
                        <div>
                            <strong>Streak:</strong>
                            <p style="margin: var(--spacing-xs) 0 0; color: var(--gray-600); font-size: 0.875rem;">
                                Mantenha a consistência completando hábitos todos os dias para aumentar seu streak.
                            </p>
                        </div>
                        <div>
                            <strong>Ranking:</strong>
                            <p style="margin: var(--spacing-xs) 0 0; color: var(--gray-600); font-size: 0.875rem;">
                                O ranking é baseado primeiro na pontuação total, depois no streak como critério de desempate.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">🏆 Conquistas</h3>
                </div>
                <div class="card-content">
                    <div style="display: flex; flex-direction: column; gap: var(--spacing-md);">
                        <div style="display: flex; align-items: center; gap: var(--spacing-sm);">
                            <span style="font-size: 1.5rem;">👑</span>
                            <div>
                                <strong>1º Lugar</strong>
                                <p style="margin: 0; color: var(--gray-600); font-size: 0.875rem;">Coroa dourada</p>
                            </div>
                        </div>
                        <div style="display: flex; align-items: center; gap: var(--spacing-sm);">
                            <span style="font-size: 1.5rem;">🥈</span>
                            <div>
                                <strong>2º Lugar</strong>
                                <p style="margin: 0; color: var(--gray-600); font-size: 0.875rem;">Medalha de prata</p>
                            </div>
                        </div>
                        <div style="display: flex; align-items: center; gap: var(--spacing-sm);">
                            <span style="font-size: 1.5rem;">🥉</span>
                            <div>
                                <strong>3º Lugar</strong>
                                <p style="margin: 0; color: var(--gray-600); font-size: 0.875rem;">Medalha de bronze</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Call to Action -->
        <div class="text-center" style="margin-top: var(--spacing-2xl);">
            <div class="card" style="background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); color: white;">
                <div class="card-content" style="padding: var(--spacing-2xl);">
                    <h3 style="color: white; margin-bottom: var(--spacing-md);">
                        💪 Quer subir no ranking?
                    </h3>
                    <p style="color: rgba(255, 255, 255, 0.9); margin-bottom: var(--spacing-lg);">
                        Complete mais hábitos diariamente e mantenha sua consistência para escalar posições!
                    </p>
                    <a href="dashboard" class="btn" style="background: white; color: var(--primary-color); font-weight: 600;">
                        Ir para Dashboard
                    </a>
                </div>
            </div>
        </div>
    </main>

    <!-- JavaScript -->
    <script src="assets/js/app.js"></script>
    
    <script>
        // Event listener para logout
        document.getElementById('logout-btn').addEventListener('click', function(e) {
            e.preventDefault();
            DailyHealthy.Auth.logout();
        });
    </script>
    
    <style>
        /* Estilos específicos da página de ranking */
        .ranking-item.current-user {
            position: relative;
            overflow: hidden;
        }
        
        .ranking-item.current-user::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(76, 175, 80, 0.1), rgba(33, 150, 243, 0.1));
            pointer-events: none;
        }
        
        .ranking-position.gold {
            background: linear-gradient(135deg, #FFD700, #FFA000);
            box-shadow: 0 4px 15px rgba(255, 215, 0, 0.4);
        }
        
        .ranking-position.silver {
            background: linear-gradient(135deg, #C0C0C0, #9E9E9E);
            box-shadow: 0 4px 15px rgba(192, 192, 192, 0.4);
        }
        
        .ranking-position.bronze {
            background: linear-gradient(135deg, #CD7F32, #8D5524);
            box-shadow: 0 4px 15px rgba(205, 127, 50, 0.4);
        }
        
        .user-avatar {
            background: linear-gradient(135deg, var(--secondary-color), var(--accent-color));
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
        
        @media (max-width: 768px) {
            .header-content {
                flex-direction: column;
                gap: var(--spacing-md);
                height: auto;
                padding: var(--spacing-md);
            }
            
            .nav {
                order: -1;
            }
            
            .user-info {
                width: 100%;
                justify-content: space-between;
            }
            
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            .grid-cols-2 {
                grid-template-columns: 1fr;
            }
            
            .ranking-item {
                flex-direction: column;
                align-items: flex-start;
                gap: var(--spacing-md);
            }
            
            .ranking-left {
                width: 100%;
            }
            
            .ranking-stats {
                text-align: left;
                width: 100%;
            }
        }
    </style>
</body>
</html>

