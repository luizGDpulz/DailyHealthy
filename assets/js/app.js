/**
 * DailyHealthy - JavaScript Principal
 * Funcionalidades do frontend
 */

// ===== CONFIGURAÇÕES GLOBAIS =====
const API_BASE = window.location.origin + window.location.pathname.replace(/\/[^\/]*$/, '') + '/api';
const BASE_URL = window.location.origin + window.location.pathname.replace(/\/[^\/]*$/, '') + '/';

// ===== UTILITÁRIOS =====
class Utils {
    // Fazer requisição AJAX
    static async request(url, options = {}) {
        const defaultOptions = {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        };

        const config = { ...defaultOptions, ...options };
        
        try {
            const response = await fetch(url, config);
            const data = await response.json();
            return data;
        } catch (error) {
            console.error('Erro na requisição:', error);
            return { success: false, message: 'Erro de conexão' };
        }
    }

    // Mostrar notificação
    static showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `notification ${type}`;
        notification.textContent = message;
        
        document.body.appendChild(notification);
        
        // Mostrar notificação
        setTimeout(() => {
            notification.classList.add('show');
        }, 100);
        
        // Remover após 5 segundos
        setTimeout(() => {
            notification.classList.remove('show');
            setTimeout(() => {
                document.body.removeChild(notification);
            }, 300);
        }, 5000);
    }

    // Formatar data
    static formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('pt-BR');
    }

    // Formatar data/hora
    static formatDateTime(dateString) {
        const date = new Date(dateString);
        return date.toLocaleString('pt-BR');
    }

    // Obter iniciais do nome
    static getInitials(name) {
        return name.split(' ')
            .map(word => word.charAt(0))
            .join('')
            .toUpperCase()
            .substring(0, 2);
    }

    // Sanitizar HTML
    static escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // Debounce para otimizar eventos
    static debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }
}

// ===== GERENCIADOR DE AUTENTICAÇÃO =====
class Auth {
    static async login(email, password) {
        const response = await Utils.request(`${API_BASE}/auth`, {
            method: 'POST',
            body: JSON.stringify({ action: 'login', email, password })
        });

        if (response.success) {
            Utils.showNotification('Login realizado com sucesso!', 'success');
            setTimeout(() => {
                window.location.href = BASE_URL + 'dashboard';
            }, 1000);
        } else {
            Utils.showNotification(response.message || 'Erro ao fazer login', 'error');
        }

        return response;
    }

    static async logout() {
        const response = await Utils.request(`${API_BASE}/auth`, {
            method: 'DELETE'
        });

        if (response.success) {
            Utils.showNotification('Logout realizado com sucesso!', 'success');
            setTimeout(() => {
                window.location.href = BASE_URL + 'login';
            }, 1000);
        }

        return response;
    }

    static async register(name, email, password, confirmPassword) {
        const response = await Utils.request(`${API_BASE}/auth`, {
            method: 'POST',
            body: JSON.stringify({ 
                action: 'register', 
                name, 
                email, 
                password, 
                confirmPassword 
            })
        });

        if (response.success) {
            Utils.showNotification('Conta criada com sucesso!', 'success');
            setTimeout(() => {
                window.location.href = BASE_URL + 'dashboard';
            }, 1000);
        } else {
            Utils.showNotification(response.message || 'Erro ao criar conta', 'error');
        }

        return response;
    }
}

// ===== GERENCIADOR DE HÁBITOS =====
class Habits {
    static async getUserHabits() {
        return await Utils.request(`${API_BASE}/habits`);
    }

    static async toggleHabit(habitId) {
        const response = await Utils.request(`${API_BASE}/habits`, {
            method: 'PUT',
            body: JSON.stringify({ 
                action: 'toggle',
                habit_id: habitId 
            })
        });

        if (response.success) {
            const data = response.data;
            if (data.completed) {
                Utils.showNotification(
                    `Parabéns! +${data.points_earned} pontos ganhos!`, 
                    'success'
                );
                
                // Mostrar novos badges se houver
                if (data.new_badges && data.new_badges.length > 0) {
                    setTimeout(() => {
                        data.new_badges.forEach(badge => {
                            Utils.showNotification(
                                `🏆 Novo badge conquistado: ${badge}!`, 
                                'success'
                            );
                        });
                    }, 1000);
                }
            } else {
                Utils.showNotification('Hábito desmarcado', 'info');
            }
        } else {
            Utils.showNotification(response.message || 'Erro ao atualizar hábito', 'error');
        }

        return response;
    }

    static async createHabit(habitData) {
        const response = await Utils.request(`${API_BASE}/habits`, {
            method: 'POST',
            body: JSON.stringify(habitData)
        });

        if (response.success) {
            Utils.showNotification('Hábito criado com sucesso!', 'success');
        } else {
            Utils.showNotification(response.message || 'Erro ao criar hábito', 'error');
        }

        return response;
    }

    static renderHabits(habits, container) {
        if (!container) return;

        container.innerHTML = '';

        if (!habits || habits.length === 0) {
            container.innerHTML = `
                <div class="text-center" style="padding: 2rem;">
                    <p style="color: var(--gray-600);">Nenhum hábito encontrado.</p>
                    <button class="btn btn-primary" onclick="HabitModal.show()">
                        Criar Primeiro Hábito
                    </button>
                </div>
            `;
            return;
        }

        habits.forEach(habit => {
            const habitElement = document.createElement('div');
            habitElement.className = `habit-item ${habit.completed_today ? 'completed' : ''}`;
            habitElement.innerHTML = `
                <div class="habit-content">
                    <div class="habit-checkbox ${habit.completed_today ? 'checked' : ''}" 
                         onclick="Habits.toggleHabitUI(${habit.id})">
                        ${habit.completed_today ? '✓' : ''}
                    </div>
                    <div class="habit-info">
                        <h3 class="habit-title ${habit.completed_today ? 'completed' : ''}">
                            ${Utils.escapeHtml(habit.title)}
                        </h3>
                        <p class="habit-description">
                            ${Utils.escapeHtml(habit.description || '')}
                        </p>
                    </div>
                </div>
                <div class="habit-points">
                    +${habit.points_base} pts
                </div>
            `;
            container.appendChild(habitElement);
        });
    }

    static async toggleHabitUI(habitId) {
        const response = await this.toggleHabit(habitId);
        
        if (response.success) {
            // Recarregar hábitos
            await Dashboard.loadHabits();
            
            // Atualizar estatísticas
            await Dashboard.loadStats();
        }
    }
}

// ===== GERENCIADOR DE RANKING =====
class Ranking {
    static async getRanking() {
        return await Utils.request(`${API_BASE}/ranking`);
    }

    static renderRanking(users, container, currentUserId = null) {
        if (!container) return;

        container.innerHTML = '';

        if (!users || users.length === 0) {
            container.innerHTML = `
                <div class="text-center" style="padding: 2rem;">
                    <p style="color: var(--gray-600);">Nenhum usuário encontrado no ranking.</p>
                </div>
            `;
            return;
        }

        users.forEach((user, index) => {
            const position = index + 1;
            const isCurrentUser = currentUserId && user.id == currentUserId;
            
            let positionClass = 'other';
            let positionIcon = `#${position}`;
            
            if (position === 1) {
                positionClass = 'gold';
                positionIcon = '👑';
            } else if (position === 2) {
                positionClass = 'silver';
                positionIcon = '🥈';
            } else if (position === 3) {
                positionClass = 'bronze';
                positionIcon = '🥉';
            }

            const userElement = document.createElement('div');
            userElement.className = `ranking-item ${isCurrentUser ? 'current-user' : ''}`;
            userElement.innerHTML = `
                <div class="ranking-left">
                    <div class="ranking-position ${positionClass}">
                        ${positionIcon}
                    </div>
                    <div class="user-avatar">
                        ${Utils.getInitials(user.name)}
                    </div>
                    <div class="user-details">
                        <h3>${Utils.escapeHtml(user.name)}</h3>
                        <p class="user-email">${Utils.escapeHtml(user.email)}</p>
                    </div>
                </div>
                <div class="ranking-stats">
                    <div class="user-points-large">${user.points} pts</div>
                    <div class="user-streak">
                        🔥 ${user.streak} dias
                    </div>
                </div>
            `;
            container.appendChild(userElement);
        });
    }
}

// ===== GERENCIADOR DE BADGES =====
class Badges {
    static async getUserBadges() {
        return await Utils.request(`${API_BASE}/badges`);
    }

    static renderBadges(badges, container) {
        if (!container) return;

        container.innerHTML = '';

        if (!badges || badges.length === 0) {
            container.innerHTML = `
                <div class="text-center" style="padding: 2rem;">
                    <p style="color: var(--gray-600);">Nenhum badge conquistado ainda.</p>
                    <p style="color: var(--gray-500); font-size: 0.875rem;">
                        Complete hábitos para ganhar seus primeiros badges!
                    </p>
                </div>
            `;
            return;
        }

        badges.forEach(badge => {
            const badgeElement = document.createElement('div');
            badgeElement.className = 'badge-item';
            badgeElement.innerHTML = `
                <div class="badge-icon" style="background: ${badge.color || '#FFD700'}">
                    ${this.getBadgeIcon(badge.icon)}
                </div>
                <h4 class="badge-name">${Utils.escapeHtml(badge.name)}</h4>
                <p class="badge-description">${Utils.escapeHtml(badge.description || '')}</p>
                <div class="badge-date">
                    Conquistado em ${Utils.formatDate(badge.earned_at)}
                </div>
            `;
            container.appendChild(badgeElement);
        });
    }

    static getBadgeIcon(iconName) {
        const icons = {
            'award': '🏆',
            'star': '⭐',
            'medal': '🏅',
            'trophy': '🏆',
            'flame': '🔥',
            'zap': '⚡',
            'fire': '🔥',
            'heart': '❤️',
            'footprints': '👣',
            'sunrise': '🌅',
            'moon': '🌙',
            'list': '📋',
            'calendar': '📅'
        };
        
        return icons[iconName] || '🏆';
    }
}

// ===== DASHBOARD =====
class Dashboard {
    static async init() {
        await this.loadStats();
        await this.loadHabits();
        this.setupEventListeners();
    }

    static async loadStats() {
        const response = await Utils.request(`${API_BASE}/auth?action=user_stats`);
        
        if (response.success && response.data) {
            this.renderStats(response.data);
        }
    }

    static async loadHabits() {
        const response = await Habits.getUserHabits();
        
        if (response.success && response.data) {
            const container = document.getElementById('habits-list');
            Habits.renderHabits(response.data, container);
        }
    }

    static renderStats(stats) {
        // Atualizar pontos totais
        const pointsElement = document.getElementById('total-points');
        if (pointsElement) {
            pointsElement.textContent = stats.points || 0;
        }

        // Atualizar streak
        const streakElement = document.getElementById('current-streak');
        if (streakElement) {
            streakElement.textContent = stats.streak || 0;
        }

        // Atualizar hábitos de hoje
        const habitsElement = document.getElementById('habits-today');
        if (habitsElement) {
            habitsElement.textContent = `${stats.habits_completed_today || 0}/${stats.habits_total || 0}`;
        }

        // Atualizar informações do usuário no header
        const userNameElement = document.getElementById('user-name');
        const userPointsElement = document.getElementById('user-points');
        
        if (userNameElement && stats.name) {
            userNameElement.textContent = stats.name;
        }
        
        if (userPointsElement && stats.points !== undefined) {
            userPointsElement.textContent = `${stats.points} pontos`;
        }
    }

    static setupEventListeners() {
        // Botão de novo hábito
        const newHabitBtn = document.getElementById('new-habit-btn');
        if (newHabitBtn) {
            newHabitBtn.addEventListener('click', () => {
                HabitModal.show();
            });
        }

        // Botão de logout
        const logoutBtn = document.getElementById('logout-btn');
        if (logoutBtn) {
            logoutBtn.addEventListener('click', (e) => {
                e.preventDefault();
                Auth.logout();
            });
        }
    }
}

// ===== PÁGINA DE RANKING =====
class RankingPage {
    static async init() {
        await this.loadRanking();
    }

    static async loadRanking() {
        const response = await Ranking.getRanking();
        
        if (response.success && response.data) {
            const container = document.getElementById('ranking-list');
            const currentUserId = response.current_user_id;
            Ranking.renderRanking(response.data, container, currentUserId);
        }
    }
}

// ===== MODAL DE HÁBITO =====
class HabitModal {
    static show() {
        const modal = document.getElementById('habit-modal');
        if (modal) {
            modal.classList.add('active');
            document.body.style.overflow = 'hidden';
        }
    }

    static hide() {
        const modal = document.getElementById('habit-modal');
        if (modal) {
            modal.classList.remove('active');
            document.body.style.overflow = '';
            this.resetForm();
        }
    }

    static resetForm() {
        const form = document.getElementById('habit-form');
        if (form) {
            form.reset();
        }
    }

    static async submit(event) {
        event.preventDefault();
        
        const form = event.target;
        const formData = new FormData(form);
        
        const habitData = {
            title: formData.get('title'),
            description: formData.get('description'),
            points_base: parseInt(formData.get('points_base')) || 10,
            category: formData.get('category') || 'geral',
            color: formData.get('color') || '#4CAF50'
        };

        const response = await Habits.createHabit(habitData);
        
        if (response.success) {
            this.hide();
            await Dashboard.loadHabits();
            await Dashboard.loadStats();
        }
    }

    static init() {
        // Event listeners para o modal
        const modal = document.getElementById('habit-modal');
        const closeBtn = document.querySelector('.modal-close');
        const cancelBtn = document.getElementById('cancel-habit');
        const form = document.getElementById('habit-form');

        if (closeBtn) {
            closeBtn.addEventListener('click', () => this.hide());
        }

        if (cancelBtn) {
            cancelBtn.addEventListener('click', () => this.hide());
        }

        if (modal) {
            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    this.hide();
                }
            });
        }

        if (form) {
            form.addEventListener('submit', (e) => this.submit(e));
        }

        // Fechar modal com ESC
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                this.hide();
            }
        });
    }
}

// ===== FORMULÁRIO DE LOGIN =====
class LoginForm {
    static init() {
        const form = document.getElementById('login-form');
        if (form) {
            form.addEventListener('submit', this.handleSubmit.bind(this));
        }
    }

    static async handleSubmit(event) {
        event.preventDefault();
        
        const form = event.target;
        const formData = new FormData(form);
        
        const email = formData.get('email');
        const password = formData.get('password');

        // Validações básicas
        if (!email || !password) {
            Utils.showNotification('Por favor, preencha todos os campos', 'error');
            return;
        }

        // Desabilitar botão durante o envio
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.textContent;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="loading"></span> Entrando...';

        try {
            await Auth.login(email, password);
        } finally {
            submitBtn.disabled = false;
            submitBtn.textContent = originalText;
        }
    }
}

// ===== INICIALIZAÇÃO =====
document.addEventListener('DOMContentLoaded', () => {
    // Detectar página atual e inicializar componentes apropriados
    const path = window.location.pathname;
    const page = path.split('/').pop().replace('.php', '') || 'index';

    switch (page) {
        case 'login':
        case 'index':
            LoginForm.init();
            break;
            
        case 'dashboard':
            Dashboard.init();
            HabitModal.init();
            break;
            
        case 'ranking':
            RankingPage.init();
            break;
    }

    // Inicializar componentes globais
    initGlobalComponents();
});

// ===== COMPONENTES GLOBAIS =====
function initGlobalComponents() {
    // Smooth scroll para links internos
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth'
                });
            }
        });
    });

    // Auto-hide notifications ao clicar
    document.addEventListener('click', (e) => {
        if (e.target.classList.contains('notification')) {
            e.target.classList.remove('show');
            setTimeout(() => {
                if (e.target.parentNode) {
                    e.target.parentNode.removeChild(e.target);
                }
            }, 300);
        }
    });
}

// ===== EXPORTAR PARA ESCOPO GLOBAL =====
window.DailyHealthy = {
    Utils,
    Auth,
    Habits,
    Ranking,
    Badges,
    Dashboard,
    RankingPage,
    HabitModal,
    LoginForm
};

