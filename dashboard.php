<?php
/**
 * DailyHealthy - Dashboard Principal
 */

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/app/Auth.php';
require_once __DIR__ . '/app/User.php';

// Verificar autenticação
Auth::requireAuth();

$currentUser = Auth::getCurrentUser();
$userStats = User::getUserStats($currentUser['id']);

$pageTitle = 'Dashboard - ' . SITE_NAME;
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
                    <a href="dashboard" class="nav-link active">Dashboard</a>
                    <a href="ranking" class="nav-link">Ranking</a>
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
        <!-- Stats Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon primary">🏆</div>
                <div>
                    <div class="stat-value" id="total-points"><?php echo $userStats['user']['points']; ?></div>
                    <div class="stat-label">Pontos Totais</div>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon accent">🔥</div>
                <div>
                    <div class="stat-value" id="current-streak"><?php echo $userStats['user']['streak']; ?></div>
                    <div class="stat-label">Dias Consecutivos</div>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon secondary">🎯</div>
                <div>
                    <div class="stat-value" id="habits-today"><?php echo $userStats['habits_completed_today']; ?>/<?php echo $userStats['habits_total']; ?></div>
                    <div class="stat-label">Hábitos Hoje</div>
                </div>
            </div>
        </div>

        <!-- Habits Section -->
        <div class="card">
            <div class="card-header">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <h2 class="card-title">Meus Hábitos de Hoje</h2>
                        <p class="card-description">Complete seus hábitos diários e ganhe pontos</p>
                    </div>
                    <button class="btn btn-primary" id="new-habit-btn">
                        ➕ Novo Hábito
                    </button>
                </div>
            </div>
            
            <div class="card-content">
                <div id="habits-list" class="habits-list">
                    <!-- Hábitos serão carregados via JavaScript -->
                    <div class="text-center" style="padding: 2rem;">
                        <div class="loading" style="margin: 0 auto var(--spacing-md);"></div>
                        <p style="color: var(--gray-600);">Carregando hábitos...</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="grid grid-cols-2" style="margin-top: var(--spacing-xl);">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Estatísticas Rápidas</h3>
                </div>
                <div class="card-content">
                    <div style="display: flex; flex-direction: column; gap: var(--spacing-md);">
                        <div style="display: flex; justify-content: space-between;">
                            <span>Total de Execuções:</span>
                            <strong><?php echo $userStats['total_executions']; ?></strong>
                        </div>
                        <div style="display: flex; justify-content: space-between;">
                            <span>Badges Conquistadas:</span>
                            <strong><?php echo $userStats['badges_count']; ?></strong>
                        </div>
                        <div style="display: flex; justify-content: space-between;">
                            <span>Posição no Ranking:</span>
                            <strong>#<?php echo $userStats['rank_position']; ?></strong>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Progresso Semanal</h3>
                </div>
                <div class="card-content">
                    <div style="text-align: center; padding: var(--spacing-lg);">
                        <div style="font-size: 3rem; margin-bottom: var(--spacing-md);">📈</div>
                        <p style="color: var(--gray-600); margin: 0;">
                            Continue assim! Você está no caminho certo para formar hábitos saudáveis.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Modal de Novo Hábito -->
    <div id="habit-modal" class="modal-overlay">
        <div class="modal">
            <div class="modal-header">
                <h3 class="modal-title">Criar Novo Hábito</h3>
                <button type="button" class="modal-close">&times;</button>
            </div>
            <div class="modal-body">
                <form id="habit-form">
                    <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                    
                    <div class="form-group">
                        <label for="habit-title" class="form-label">Título do Hábito</label>
                        <input 
                            type="text" 
                            id="habit-title" 
                            name="title" 
                            class="form-input" 
                            placeholder="Ex: Beber 2L de água"
                            required
                            maxlength="150"
                        >
                    </div>
                    
                    <div class="form-group">
                        <label for="habit-description" class="form-label">Descrição (opcional)</label>
                        <textarea 
                            id="habit-description" 
                            name="description" 
                            class="form-input" 
                            placeholder="Descreva seu hábito..."
                            rows="3"
                            maxlength="500"
                        ></textarea>
                    </div>
                    
                    <div class="grid grid-cols-2">
                        <div class="form-group">
                            <label for="habit-points" class="form-label">Pontos</label>
                            <select id="habit-points" name="points_base" class="form-input">
                                <option value="5">5 pontos (Fácil)</option>
                                <option value="10" selected>10 pontos (Normal)</option>
                                <option value="15">15 pontos (Médio)</option>
                                <option value="20">20 pontos (Difícil)</option>
                                <option value="25">25 pontos (Muito Difícil)</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="habit-category" class="form-label">Categoria</label>
                            <select id="habit-category" name="category" class="form-input">
                                <option value="saude">🏥 Saúde</option>
                                <option value="exercicio">💪 Exercício</option>
                                <option value="alimentacao">🥗 Alimentação</option>
                                <option value="mental">🧠 Mental</option>
                                <option value="educacao">📚 Educação</option>
                                <option value="trabalho">💼 Trabalho</option>
                                <option value="social">👥 Social</option>
                                <option value="geral" selected>📋 Geral</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="habit-color" class="form-label">Cor do Hábito</label>
                        <div style="display: flex; gap: var(--spacing-sm); flex-wrap: wrap;">
                            <input type="radio" name="color" value="#4CAF50" id="color-green" checked style="display: none;">
                            <label for="color-green" style="width: 40px; height: 40px; background: #4CAF50; border-radius: 50%; cursor: pointer; border: 3px solid transparent;" onclick="selectColor('#4CAF50', this)"></label>
                            
                            <input type="radio" name="color" value="#2196F3" id="color-blue" style="display: none;">
                            <label for="color-blue" style="width: 40px; height: 40px; background: #2196F3; border-radius: 50%; cursor: pointer; border: 3px solid transparent;" onclick="selectColor('#2196F3', this)"></label>
                            
                            <input type="radio" name="color" value="#FF9800" id="color-orange" style="display: none;">
                            <label for="color-orange" style="width: 40px; height: 40px; background: #FF9800; border-radius: 50%; cursor: pointer; border: 3px solid transparent;" onclick="selectColor('#FF9800', this)"></label>
                            
                            <input type="radio" name="color" value="#9C27B0" id="color-purple" style="display: none;">
                            <label for="color-purple" style="width: 40px; height: 40px; background: #9C27B0; border-radius: 50%; cursor: pointer; border: 3px solid transparent;" onclick="selectColor('#9C27B0', this)"></label>
                            
                            <input type="radio" name="color" value="#F44336" id="color-red" style="display: none;">
                            <label for="color-red" style="width: 40px; height: 40px; background: #F44336; border-radius: 50%; cursor: pointer; border: 3px solid transparent;" onclick="selectColor('#F44336', this)"></label>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-ghost" id="cancel-habit">Cancelar</button>
                <button type="submit" form="habit-form" class="btn btn-primary">Criar Hábito</button>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script src="assets/js/app.js"></script>
    
    <script>
        // Função para selecionar cor do hábito
        function selectColor(color, element) {
            // Remover seleção anterior
            document.querySelectorAll('label[for^="color-"]').forEach(label => {
                label.style.border = '3px solid transparent';
            });
            
            // Selecionar nova cor
            element.style.border = '3px solid var(--gray-800)';
            document.querySelector(`input[value="${color}"]`).checked = true;
        }
        
        // Inicializar seleção de cor padrão
        document.addEventListener('DOMContentLoaded', function() {
            const defaultColorLabel = document.querySelector('label[for="color-green"]');
            if (defaultColorLabel) {
                defaultColorLabel.style.border = '3px solid var(--gray-800)';
            }
        });
    </script>
    
    <style>
        /* Estilos específicos do dashboard */
        textarea.form-input {
            resize: vertical;
            min-height: 80px;
        }
        
        .color-selector {
            display: flex;
            gap: var(--spacing-sm);
            flex-wrap: wrap;
        }
        
        .color-option {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            cursor: pointer;
            border: 3px solid transparent;
            transition: all var(--transition-fast);
        }
        
        .color-option:hover {
            transform: scale(1.1);
        }
        
        .color-option.selected {
            border-color: var(--gray-800);
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
        }
    </style>
</body>
</html>

