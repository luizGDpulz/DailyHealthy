document.addEventListener("DOMContentLoaded", () => {
    const API_BASE_URL = "/backend";

    const authSection = document.getElementById("auth-section");
    const dashboardSection = document.getElementById("dashboard-section");
    const loginForm = document.getElementById("login-form");
    const registerForm = document.getElementById("register-form");
    const logoutBtn = document.getElementById("logout-btn");
    const welcomeMessage = document.getElementById("welcome-message");
    const userPoints = document.getElementById("user-points");
    const habitsList = document.getElementById("habits-list");
    const notificationToast = document.getElementById("notification-toast");

    let currentUser = null;

    const showNotification = (message, type = "success") => {
        notificationToast.textContent = message;
        notificationToast.className = `fixed top-5 right-5 px-6 py-3 rounded-lg shadow-lg text-white notification-toast ${type === "success" ? "bg-green-500" : "bg-red-500"}`;
        notificationToast.classList.remove("hidden");
        setTimeout(() => {
            notificationToast.classList.add("hidden");
        }, 3000);
    };

    const renderHabits = (habits) => {
        habitsList.innerHTML = "";
        if (habits.length === 0) {
            habitsList.innerHTML = "<p class=\"text-gray-600\">Nenhum hábito encontrado.</p>";
            return;
        }
        habits.forEach(habit => {
            const habitCard = document.createElement("div");
            habitCard.className = "bg-gradient-to-r from-blue-50 to-indigo-50 p-6 rounded-xl shadow-md border border-indigo-100 hover:shadow-lg transition-all duration-300";
            habitCard.innerHTML = `
                <h4 class="text-xl font-bold text-gray-800 mb-2">${habit.title}</h4>
                <p class="text-gray-600 mb-4">${habit.description}</p>
                <div class="flex justify-between items-center">
                    <span class="text-indigo-600 font-bold text-lg">+${habit.points_base} pontos</span>
                    <button data-id="${habit.id}" class="execute-btn bg-gradient-to-r from-green-500 to-green-600 text-white py-2 px-6 rounded-lg hover:from-green-600 hover:to-green-700 transition duration-300 transform hover:scale-105 font-semibold">
                        ✓ Concluir
                    </button>
                </div>
            `;
            habitsList.appendChild(habitCard);
        });

        document.querySelectorAll(".execute-btn").forEach(button => {
            button.addEventListener("click", async (e) => {
                const habitId = e.target.dataset.id;
                await executeHabit(habitId);
            });
        });
    };

    const mockHabits = [
        { id: 1, title: "Beber 2L de água", description: "Mantenha-se hidratado durante o dia", points_base: 10 },
        { id: 2, title: "Meditar 10 min", description: "Pratique mindfulness e relaxamento", points_base: 15 },
        { id: 3, title: "Caminhar 30 min", description: "Exercício cardiovascular leve", points_base: 20 },
        { id: 4, title: "Ler 20 páginas", description: "Desenvolva o hábito da leitura", points_base: 12 }
    ];

    const loadHabits = async () => {
        try {
            const response = await fetch(`${API_BASE_URL}/habits`);
            if (response.ok) {
                const data = await response.json();
                renderHabits(data);
            } else {
                renderHabits(mockHabits);
            }
        } catch (error) {
            renderHabits(mockHabits);
        }
    };

    const executeHabit = async (habitId) => {
        const habit = mockHabits.find(h => h.id == habitId);
        if (habit) {
            currentUser.points += habit.points_base;
            userPoints.textContent = `${currentUser.points} pontos`;
            showNotification(`+${habit.points_base} pontos! Parabéns! 🎉`);
            
            const button = document.querySelector(`.execute-btn[data-id="${habitId}"]`);
            button.disabled = true;
            button.textContent = "✓ Concluído!";
            button.className = "bg-gray-400 text-white py-2 px-6 rounded-lg font-semibold cursor-not-allowed";
        }
    };

    const updateUI = () => {
        if (currentUser) {
            authSection.classList.add("hidden");
            dashboardSection.classList.remove("hidden");
            dashboardSection.classList.add("flex");
            welcomeMessage.textContent = `Olá, ${currentUser.name}!`;
            userPoints.textContent = `${currentUser.points} pontos`;
            loadHabits();
        } else {
            authSection.classList.remove("hidden");
            dashboardSection.classList.add("hidden");
            dashboardSection.classList.remove("flex");
        }
    };

    const checkSession = () => {
        const storedUser = localStorage.getItem("currentUser");
        if (storedUser) {
            currentUser = JSON.parse(storedUser);
            updateUI();
        }
    };

    loginForm.addEventListener("submit", async (e) => {
        e.preventDefault();
        const email = document.getElementById("login-email").value;
        const password = document.getElementById("login-password").value;

        try {
            const response = await fetch(`${API_BASE_URL}/auth/login`, {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ email, password })
            });
            const data = await response.json();

            if (response.ok) {
                currentUser = { id: data.id, name: data.name, email: data.email, points: data.points };
                localStorage.setItem("currentUser", JSON.stringify(currentUser));
                showNotification("Login realizado com sucesso! 🎉");
                updateUI();
            } else {
                showNotification(data.message || "Erro no login.", "error");
            }
        } catch (error) {
            currentUser = { id: 1, name: email.split('@')[0], email: email, points: 50 };
            localStorage.setItem("currentUser", JSON.stringify(currentUser));
            showNotification("Login simulado realizado! 🎉");
            updateUI();
        }
    });

    registerForm.addEventListener("submit", async (e) => {
        e.preventDefault();
        const name = document.getElementById("register-name").value;
        const email = document.getElementById("register-email").value;
        const password = document.getElementById("register-password").value;

        try {
            const response = await fetch(`${API_BASE_URL}/auth/register`, {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ name, email, password })
            });
            const data = await response.json();

            if (response.ok) {
                showNotification("Registro realizado com sucesso! Faça login. 🎉");
                registerForm.reset();
            } else {
                showNotification(data.message || "Erro no registro.", "error");
            }
        } catch (error) {
            showNotification("Registro simulado realizado! Faça login. 🎉");
            registerForm.reset();
        }
    });

    logoutBtn.addEventListener("click", () => {
        currentUser = null;
        localStorage.removeItem("currentUser");
        showNotification("Logout realizado com sucesso! 👋");
        updateUI();
    });

    checkSession();
});


