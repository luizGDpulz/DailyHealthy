// Utility Functions
const formatPoints = (points) => {
    if (points >= 1000000) {
        return (points / 1000000).toFixed(1) + 'M';
    } else if (points >= 1000) {
        return (points / 1000).toFixed(1) + 'K';
    }
    return points;
};

// Toast Notifications
const showToast = (message, type = 'success') => {
    const toast = document.createElement('div');
    toast.className = `fixed bottom-4 right-4 px-6 py-3 rounded-lg text-white transform transition-all duration-500 ease-in-out ${
        type === 'success' ? 'bg-green-500' : 'bg-red-500'
    }`;
    toast.textContent = message;
    document.body.appendChild(toast);
    
    // Animate in
    setTimeout(() => toast.classList.add('translate-y-0', 'opacity-100'), 100);
    
    // Remove after 3 seconds
    setTimeout(() => {
        toast.classList.add('opacity-0', 'translate-y-2');
        setTimeout(() => toast.remove(), 500);
    }, 3000);
};

// Confetti Animation for Achievements
const triggerConfetti = () => {
    const colors = ['#FFD700', '#FFA500', '#FF6347', '#87CEEB', '#98FB98'];
    const confettiCount = 100;
    
    for (let i = 0; i < confettiCount; i++) {
        const confetti = document.createElement('div');
        confetti.className = 'absolute w-2 h-2 rounded-full';
        confetti.style.backgroundColor = colors[Math.floor(Math.random() * colors.length)];
        confetti.style.left = Math.random() * 100 + 'vw';
        confetti.style.top = -10 + 'px';
        confetti.style.transform = `rotate(${Math.random() * 360}deg)`;
        document.body.appendChild(confetti);
        
        const animation = confetti.animate([
            { transform: 'translate(0, 0) rotate(0)', opacity: 1 },
            { transform: `translate(${Math.random() * 200 - 100}px, ${window.innerHeight}px) rotate(${Math.random() * 360}deg)`, opacity: 0 }
        ], {
            duration: Math.random() * 1000 + 1000,
            easing: 'cubic-bezier(.25,.46,.45,.94)'
        });
        
        animation.onfinish = () => confetti.remove();
    }
};

// Habit Execution
const executeHabit = async (habitId) => {
    try {
        const response = await fetch('/dailyhealthy/public/habits/execute', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: `habit_id=${habitId}`
        });
        
        const data = await response.json();
        
        if (data.success) {
            // Update UI
            const pointsElement = document.getElementById('userPoints');
            const currentPoints = parseInt(pointsElement.dataset.points) + data.points;
            pointsElement.dataset.points = currentPoints;
            pointsElement.textContent = formatPoints(currentPoints);
            
            // Mark habit as completed
            const habitButton = document.querySelector(`[data-habit-id="${habitId}"]`);
            habitButton.classList.remove('bg-blue-500', 'hover:bg-blue-600');
            habitButton.classList.add('bg-green-500', 'cursor-default');
            habitButton.disabled = true;
            
            showToast(data.message, 'success');
            
            // If new badges were earned, show achievement modal
            if (data.newBadges && data.newBadges.length > 0) {
                showBadgeModal(data.newBadges[0]);
            }
            
        } else {
            showToast(data.message, 'error');
        }
    } catch (error) {
        showToast('Error executing habit', 'error');
        console.error(error);
    }
};

// Badge Achievement Modal
const showBadgeModal = (badge) => {
    const modal = document.createElement('div');
    modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
    modal.innerHTML = `
        <div class="bg-white rounded-lg p-8 max-w-md w-full mx-4 transform transition-all duration-300 scale-0">
            <div class="text-center">
                <img src="/DailyHealthy/public/assets/images/${badge.icon_path}" 
                     alt="${badge.name}" 
                     class="w-32 h-32 mx-auto mb-4">
                <h3 class="text-2xl font-bold mb-2">New Achievement Unlocked!</h3>
                <p class="text-xl text-gray-700 mb-4">${badge.name}</p>
                <p class="text-gray-600 mb-6">${badge.description}</p>
                <button class="bg-blue-500 text-white px-6 py-2 rounded-lg hover:bg-blue-600 transition-colors">
                    Awesome!
                </button>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    triggerConfetti();
    
    // Animate in
    setTimeout(() => {
        modal.querySelector('div > div').classList.remove('scale-0');
    }, 100);
    
    // Close on button click or outside click
    modal.addEventListener('click', (e) => {
        if (e.target === modal || e.target.tagName === 'BUTTON') {
            modal.querySelector('div > div').classList.add('scale-0');
            setTimeout(() => modal.remove(), 300);
        }
    });
};

// Calendar View
const initializeCalendar = () => {
    const calendarEl = document.getElementById('calendar');
    if (!calendarEl) return;
    
    const date = new Date();
    const currentMonth = date.getMonth();
    const currentYear = date.getFullYear();
    const firstDay = new Date(currentYear, currentMonth, 1).getDay();
    const daysInMonth = new Date(currentYear, currentMonth + 1, 0).getDate();
    
    let calendarHTML = `
        <div class="grid grid-cols-7 gap-1">
            ${['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat']
                .map(day => `<div class="text-center text-gray-600 text-sm py-1">${day}</div>`)
                .join('')}
    `;
    
    // Empty cells for days before the first of the month
    for (let i = 0; i < firstDay; i++) {
        calendarHTML += '<div class="p-2"></div>';
    }
    
    // Days of the month
    for (let day = 1; day <= daysInMonth; day++) {
        const isToday = day === date.getDate();
        calendarHTML += `
            <div class="p-2 text-center ${isToday ? 'bg-blue-100 rounded-lg' : ''}">
                <span class="text-sm ${isToday ? 'font-bold' : ''}">${day}</span>
                <div class="habit-dots flex justify-center gap-1 mt-1"></div>
            </div>
        `;
    }
    
    calendarHTML += '</div>';
    calendarEl.innerHTML = calendarHTML;
};

// Initialize on page load
document.addEventListener('DOMContentLoaded', () => {
    initializeCalendar();
    
    // Initialize tooltips
    const tooltips = document.querySelectorAll('[data-tooltip]');
    tooltips.forEach(element => {
        element.addEventListener('mouseenter', (e) => {
            const tooltip = document.createElement('div');
            tooltip.className = 'absolute bg-gray-800 text-white text-sm px-2 py-1 rounded -mt-8 transform -translate-x-1/2 left-1/2';
            tooltip.textContent = element.dataset.tooltip;
            element.appendChild(tooltip);
        });
        
        element.addEventListener('mouseleave', () => {
            const tooltip = element.querySelector('div');
            if (tooltip) tooltip.remove();
        });
    });
});
