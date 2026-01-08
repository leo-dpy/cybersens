// ==========================================
// API CLIENT (Replaces LocalStorage DB)
// ==========================================
const API_URL = 'backend'; // Chemin relatif vers le dossier backend

// Fonction utilitaire pour convertir le niveau numérique en texte
function getLevelName(level) {
    const levels = {
        1: 'Novice',
        2: 'Initié',
        3: 'Confirmé',
        4: 'Expert',
        5: 'Maître',
        6: 'Élite',
        7: 'Légende'
    };
    return levels[level] || `Niveau ${level}`;
}

// Calculer le niveau à partir de l'XP
function calculateLevel(xp) {
    if (xp < 100) return 1;
    if (xp < 300) return 2;
    if (xp < 600) return 3;
    if (xp < 1000) return 4;
    if (xp < 1500) return 5;
    if (xp < 2500) return 6;
    return 7;
}

// XP requis pour le prochain niveau
function getXpForNextLevel(level) {
    const requirements = {
        1: 100,
        2: 300,
        3: 600,
        4: 1000,
        5: 1500,
        6: 2500,
        7: Infinity
    };
    return requirements[level] || Infinity;
}

class ApiClient {
    
    async login(email, password) {
        try {
            const response = await fetch(`${API_URL}/login.php`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ email, password })
            });
            return await response.json();
        } catch (e) {
            console.error("Erreur Login:", e);
            return { success: false, message: "Erreur de connexion au serveur" };
        }
    }

    async register(username, email, password) {
        try {
            const response = await fetch(`${API_URL}/register.php`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ username, email, password })
            });
            return await response.json();
        } catch (e) {
            console.error("Erreur Register:", e);
            return { success: false, message: "Erreur de connexion au serveur" };
        }
    }

    async getUsers() {
        try {
            const response = await fetch(`${API_URL}/users.php`);
            return await response.json();
        } catch (e) {
            console.error("Erreur GetUsers:", e);
            return [];
        }
    }

    async deleteUser(id) {
        try {
            await fetch(`${API_URL}/users.php`, {
                method: 'DELETE',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id })
            });
            return true;
        } catch (e) {
            return false;
        }
    }

    async updateUserGroup(id, groupName) {
        try {
            await fetch(`${API_URL}/users.php`, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id, group: groupName })
            });
            return true;
        } catch (e) {
            return false;
        }
    }

    async addXp(userId, xpAmount) {
        try {
            const response = await fetch(`${API_URL}/users.php`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'add_xp', user_id: userId, xp: xpAmount })
            });
            return await response.json();
        } catch (e) {
            console.error("Erreur AddXP:", e);
            return { success: false };
        }
    }

    async updateAvatar(userId, avatar) {
        try {
            const response = await fetch(`${API_URL}/users.php`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'update_avatar', user_id: userId, avatar: avatar })
            });
            return await response.json();
        } catch (e) {
            console.error("Erreur UpdateAvatar:", e);
            return { success: false };
        }
    }

    async getLeaderboard() {
        try {
            const response = await fetch(`${API_URL}/leaderboard.php`);
            return await response.json();
        } catch (e) {
            return [];
        }
    }

    // ==========================================
    // COURSES API
    // ==========================================
    async getCourses() {
        try {
            const response = await fetch(`${API_URL}/courses.php`);
            const data = await response.json();
            return data.success ? data.courses : [];
        } catch (e) {
            console.error("Erreur GetCourses:", e);
            return [];
        }
    }

    async getCourse(id) {
        try {
            const response = await fetch(`${API_URL}/courses.php?id=${id}`);
            const data = await response.json();
            return data.success ? data.course : null;
        } catch (e) {
            console.error("Erreur GetCourse:", e);
            return null;
        }
    }

    async createCourse(courseData) {
        try {
            const response = await fetch(`${API_URL}/courses.php`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(courseData)
            });
            return await response.json();
        } catch (e) {
            return { success: false, message: "Erreur serveur" };
        }
    }

    async updateCourse(courseData) {
        try {
            const response = await fetch(`${API_URL}/courses.php`, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(courseData)
            });
            return await response.json();
        } catch (e) {
            return { success: false, message: "Erreur serveur" };
        }
    }

    async deleteCourse(id) {
        try {
            const response = await fetch(`${API_URL}/courses.php`, {
                method: 'DELETE',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id })
            });
            return await response.json();
        } catch (e) {
            return { success: false, message: "Erreur serveur" };
        }
    }

    // ==========================================
    // QUESTIONS API
    // ==========================================
    async getQuestions(courseId = null) {
        try {
            let url = `${API_URL}/questions.php`;
            if (courseId) url += `?course_id=${courseId}`;
            const response = await fetch(url);
            const data = await response.json();
            return data.success ? data.questions : [];
        } catch (e) {
            console.error("Erreur GetQuestions:", e);
            return [];
        }
    }

    async createQuestion(questionData) {
        try {
            const response = await fetch(`${API_URL}/questions.php`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(questionData)
            });
            return await response.json();
        } catch (e) {
            return { success: false, message: "Erreur serveur" };
        }
    }

    async updateQuestion(questionData) {
        try {
            const response = await fetch(`${API_URL}/questions.php`, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(questionData)
            });
            return await response.json();
        } catch (e) {
            return { success: false, message: "Erreur serveur" };
        }
    }

    async deleteQuestion(id) {
        try {
            const response = await fetch(`${API_URL}/questions.php`, {
                method: 'DELETE',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id })
            });
            return await response.json();
        } catch (e) {
            return { success: false, message: "Erreur serveur" };
        }
    }

    // ==========================================
    // STATS API
    // ==========================================
    async getStats() {
        try {
            const response = await fetch(`${API_URL}/stats.php`);
            const data = await response.json();
            return data.success ? data.stats : null;
        } catch (e) {
            console.error("Erreur GetStats:", e);
            return null;
        }
    }

    // ==========================================
    // USER ADMIN API
    // ==========================================
    async toggleUserAdmin(id) {
        try {
            const response = await fetch(`${API_URL}/users.php`, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id, toggle_admin: true })
            });
            return await response.json();
        } catch (e) {
            return { success: false };
        }
    }

    // ==========================================
    // PROGRESSION API
    // ==========================================
    async saveProgression(userId, courseId, completed) {
        try {
            const response = await fetch(`${API_URL}/progression.php`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ user_id: userId, course_id: courseId, completed })
            });
            return await response.json();
        } catch (e) {
            return { success: false };
        }
    }

    async getProgression(userId) {
        try {
            const response = await fetch(`${API_URL}/progression.php?user_id=${userId}`);
            const data = await response.json();
            return data.success ? data.progression : [];
        } catch (e) {
            return [];
        }
    }

    // ==========================================
    // BADGES API
    // ==========================================
    async getBadges(userId) {
        try {
            const response = await fetch(`${API_URL}/badges.php?user_id=${userId}`);
            const data = await response.json();
            return data.success ? data : { badges: [], unlocked: [] };
        } catch (e) {
            return { badges: [], unlocked: [] };
        }
    }

    async checkBadges(userId) {
        try {
            const response = await fetch(`${API_URL}/badges.php`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ user_id: userId })
            });
            return await response.json();
        } catch (e) {
            return { success: false, new_badges: [] };
        }
    }

    // ==========================================
    // CERTIFICATES API
    // ==========================================
    async getCertificates(userId) {
        try {
            const response = await fetch(`${API_URL}/certificates.php?user_id=${userId}`);
            const data = await response.json();
            return data.success ? data.certificates : [];
        } catch (e) {
            return [];
        }
    }

    async generateCertificate(userId, courseId, score) {
        try {
            const response = await fetch(`${API_URL}/certificates.php`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ user_id: userId, course_id: courseId, score })
            });
            return await response.json();
        } catch (e) {
            return { success: false };
        }
    }

    async verifyCertificate(code) {
        try {
            const response = await fetch(`${API_URL}/certificates.php?code=${code}`);
            return await response.json();
        } catch (e) {
            return { success: false, valid: false };
        }
    }

    // ==========================================
    // PHISHING API
    // ==========================================
    async getPhishingScenarios(userId = null) {
        try {
            let url = `${API_URL}/phishing.php`;
            if (userId) url += `?user_id=${userId}`;
            const response = await fetch(url);
            const data = await response.json();
            return data.success ? data.scenarios : [];
        } catch (e) {
            return [];
        }
    }

    async getPhishingScenario(scenarioId) {
        try {
            const response = await fetch(`${API_URL}/phishing.php`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'get_scenario', scenario_id: scenarioId })
            });
            const data = await response.json();
            return data.success ? data.scenario : null;
        } catch (e) {
            return null;
        }
    }

    async submitPhishingAnswer(userId, scenarioId, isPhishing, timeTaken) {
        try {
            const response = await fetch(`${API_URL}/phishing.php`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ 
                    action: 'answer', 
                    user_id: userId, 
                    scenario_id: scenarioId, 
                    is_phishing: isPhishing,
                    time_taken: timeTaken
                })
            });
            return await response.json();
        } catch (e) {
            return { success: false };
        }
    }

    async getPhishingStats(userId) {
        try {
            const response = await fetch(`${API_URL}/phishing.php?action=stats&user_id=${userId}`);
            const data = await response.json();
            return data.success ? data : { stats: {}, completed_scenarios: [] };
        } catch (e) {
            return { stats: {}, completed_scenarios: [] };
        }
    }

    // ==========================================
    // RESOURCES API
    // ==========================================
    async getResources(category = null, difficulty = null) {
        try {
            let url = `${API_URL}/resources.php`;
            const params = [];
            if (category) params.push(`category=${category}`);
            if (difficulty) params.push(`difficulty=${difficulty}`);
            if (params.length) url += '?' + params.join('&');
            
            const response = await fetch(url);
            const data = await response.json();
            return data.success ? data : { resources: [], grouped: {} };
        } catch (e) {
            return { resources: [], grouped: {} };
        }
    }

    async getResource(id) {
        try {
            const response = await fetch(`${API_URL}/resources.php?id=${id}`);
            const data = await response.json();
            return data.success ? data.resource : null;
        } catch (e) {
            return null;
        }
    }

    // ==========================================
    // NOTIFICATIONS API
    // ==========================================
    async getNotifications(userId, unreadOnly = false) {
        try {
            let url = `${API_URL}/notifications.php?user_id=${userId}`;
            if (unreadOnly) url += '&unread=true';
            const response = await fetch(url);
            const data = await response.json();
            return data.success ? data : { notifications: [], unread_count: 0 };
        } catch (e) {
            return { notifications: [], unread_count: 0 };
        }
    }

    async markNotificationsRead(userId, notificationId = null) {
        try {
            const response = await fetch(`${API_URL}/notifications.php`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ 
                    action: 'mark_read', 
                    user_id: userId, 
                    notification_id: notificationId 
                })
            });
            return await response.json();
        } catch (e) {
            return { success: false };
        }
    }
}

const api = new ApiClient();

document.addEventListener('DOMContentLoaded', async () => {
    // Check if running on file:// protocol
    if (window.location.protocol === 'file:') {
        alert("⚠️ ATTENTION : Vous avez ouvert le fichier directement.\n\nPour que la base de données fonctionne, vous devez passer par votre serveur WAMP (http://localhost/Cybersens).");
    }

    // Initialize Lucide Icons
    lucide.createIcons();

    const contentArea = document.getElementById('content-area');
    const navItems = document.querySelectorAll('.nav-item');

    // ==========================================
    // NAVIGATION & TEMPLATE LOADING
    // ==========================================

    async function loadTemplate(viewId) {
        try {
            const response = await fetch(`templates/${viewId}.html`);
            if (!response.ok) throw new Error('Template not found');
            const html = await response.text();
            contentArea.innerHTML = html;
            
            // Re-initialize icons for new content
            lucide.createIcons();
            
            // Initialize specific view logic
            if (viewId === 'profil') initAuth();
            if (viewId === 'leaderboard') loadLeaderboards();
            if (viewId === 'home' || viewId === 'cours' || viewId === 'quiz') initTiltEffect();
            if (viewId === 'admin') initAdminPanel();
            if (viewId === 'cours') loadCoursesView();
            if (viewId === 'phishing') initPhishingView();
            if (viewId === 'ressources') initResourcesView();

            // Update Active State in Sidebar
            navItems.forEach(item => {
                if (item.dataset.view === viewId) item.classList.add('active');
                else item.classList.remove('active');
            });

        } catch (error) {
            console.error('Error loading template:', error);
            contentArea.innerHTML = '<h1>Erreur 404</h1><p>Impossible de charger le contenu.</p>';
        }
    }

    // Add Click Listeners
    navItems.forEach(item => {
        item.addEventListener('click', () => {
            const viewId = item.dataset.view;
            loadTemplate(viewId);
        });
    });

    // Check if user is already logged in and show admin nav if applicable
    const sessionUser = JSON.parse(sessionStorage.getItem('currentUser'));
    if (sessionUser && sessionUser.role === 'admin') {
        const adminNavItem = document.querySelector('.nav-item.admin-only');
        if (adminNavItem) {
            adminNavItem.style.display = 'flex';
        }
    }

    // Load Home by default
    loadTemplate('home');


    // ==========================================
    // UI EFFECTS
    // ==========================================

    function initTiltEffect() {
        const cards = document.querySelectorAll('.card');
        cards.forEach(card => {
            card.addEventListener('mousemove', (e) => {
                const rect = card.getBoundingClientRect();
                const x = e.clientX - rect.left;
                const y = e.clientY - rect.top;
                
                const centerX = rect.width / 2;
                const centerY = rect.height / 2;
                
                const rotateX = ((y - centerY) / centerY) * -5;
                const rotateY = ((x - centerX) / centerX) * 5;

                card.style.transform = `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) scale(1.02)`;
            });

            card.addEventListener('mouseleave', () => {
                card.style.transform = 'perspective(1000px) rotateX(0) rotateY(0) scale(1)';
            });
        });
    }

    // Modal Logic (Global)
    const modalOverlay = document.getElementById('modal-overlay');
    const modal = document.getElementById('modal-bp');
    const closeBtn = document.getElementById('close-modal-btn');
    const ackBtn = document.getElementById('ack-btn');
    const openBtn = document.getElementById('open-bp-btn');

    function openModal() {
        modalOverlay.classList.add('active');
        setTimeout(() => modal.classList.add('active'), 10);
    }

    function closeModal() {
        modal.classList.remove('active');
        setTimeout(() => modalOverlay.classList.remove('active'), 300);
    }

    if (closeBtn) closeBtn.addEventListener('click', closeModal);
    if (ackBtn) ackBtn.addEventListener('click', closeModal);
    if (modalOverlay) modalOverlay.addEventListener('click', closeModal);
    if (openBtn) openBtn.addEventListener('click', openModal);

    // Auto-open logic
    setTimeout(openModal, 500);


    // ==========================================
    // AUTHENTICATION LOGIC
    // ==========================================
    
    function initAuth() {
        const authContainer = document.getElementById('auth-container');
        const userDashboard = document.getElementById('user-dashboard');
        
        const loginView = document.getElementById('login-view');
        const signupView = document.getElementById('signup-view');
        const loginToggleContent = document.getElementById('login-toggle-content');
        const signupToggleContent = document.getElementById('signup-toggle-content');

        // Check Session
        const sessionUser = JSON.parse(sessionStorage.getItem('currentUser'));
        if (sessionUser) {
            updateUI(sessionUser);
        }

        // Toggle Forms
        document.getElementById('show-signup-btn')?.addEventListener('click', () => {
            loginView.style.display = 'none';
            signupView.style.display = 'block';
            loginToggleContent.style.display = 'none';
            signupToggleContent.style.display = 'block';
        });

        document.getElementById('show-login-btn')?.addEventListener('click', () => {
            signupView.style.display = 'none';
            loginView.style.display = 'block';
            signupToggleContent.style.display = 'none';
            loginToggleContent.style.display = 'block';
        });

        // Handle Login
        document.getElementById('login-form')?.addEventListener('submit', async (e) => {
            e.preventDefault();
            const email = document.getElementById('login-email').value;
            const password = document.getElementById('login-password').value;
            const errorMsg = document.getElementById('login-error');

            const result = await api.login(email, password);

            if (result.success) {
                loginUser(result.user);
                errorMsg.style.display = 'none';
            } else {
                errorMsg.textContent = result.message;
                errorMsg.style.display = 'block';
            }
        });

        // Handle Signup
        document.getElementById('signup-form')?.addEventListener('submit', async (e) => {
            e.preventDefault();
            const username = document.getElementById('signup-username').value;
            const email = document.getElementById('signup-email').value;
            const password = document.getElementById('signup-password').value;
            const errorMsg = document.getElementById('signup-error');

            const result = await api.register(username, email, password);

            if (result.success) {
                loginUser(result.user);
                errorMsg.style.display = 'none';
            } else {
                errorMsg.textContent = result.message;
                errorMsg.style.display = 'block';
            }
        });

        // Handle Logout
        document.getElementById('logout-btn')?.addEventListener('click', () => {
            sessionStorage.removeItem('currentUser');
            loadTemplate('profil'); // Reload profile view to reset
        });

        function loginUser(user) {
            sessionStorage.setItem('currentUser', JSON.stringify(user));
            updateUI(user);
        }

        function updateUI(user) {
            if (!authContainer) return;
            
            // Hide Auth, Show Dashboard
            authContainer.style.display = 'none';
            userDashboard.style.display = 'block';

            // Update User Info
            document.getElementById('user-name-display').textContent = user.username;
            // Convertir le niveau numérique en texte
            const levelNum = parseInt(user.level) || 1;
            const xp = parseInt(user.xp) || 0;
            document.getElementById('user-level-display').textContent = getLevelName(levelNum);
            document.getElementById('user-xp-display').textContent = xp;
            
            // Update XP Progress Bar
            updateXpProgressBar(xp, levelNum);

            // Load badges and certificates
            loadUserBadges(user.id);
            loadUserCertificates(user.id);

            // Admin Check - Show admin link in sidebar only
            if (user.role === 'admin') {
                const adminNavItem = document.querySelector('.nav-item.admin-only');
                if (adminNavItem) {
                    adminNavItem.style.display = 'flex';
                }
            }
        }
        
        function updateXpProgressBar(xp, level) {
            const xpCurrentEl = document.getElementById('xp-current-value');
            const xpNextEl = document.getElementById('xp-next-value');
            const progressFill = document.getElementById('xp-progress-fill');
            const currentBadge = document.getElementById('current-level-badge');
            const nextBadge = document.getElementById('next-level-badge');
            
            if (!xpCurrentEl || !progressFill) return;
            
            const xpForNext = getXpForNextLevel(level);
            const xpForCurrent = level > 1 ? getXpForNextLevel(level - 1) : 0;
            const xpInLevel = xp - xpForCurrent;
            const xpNeeded = xpForNext - xpForCurrent;
            const progress = Math.min((xpInLevel / xpNeeded) * 100, 100);
            
            // Animate XP counter
            let currentXp = 0;
            const targetXp = xp;
            const duration = 1000;
            const startTime = performance.now();
            
            function animateXp(currentTime) {
                const elapsed = currentTime - startTime;
                const percent = Math.min(elapsed / duration, 1);
                currentXp = Math.floor(percent * targetXp);
                xpCurrentEl.textContent = currentXp;
                
                if (percent < 1) {
                    requestAnimationFrame(animateXp);
                } else {
                    xpCurrentEl.textContent = targetXp;
                }
            }
            requestAnimationFrame(animateXp);
            
            xpNextEl.textContent = xpForNext === Infinity ? '∞' : xpForNext;
            
            // Animate progress bar
            setTimeout(() => {
                progressFill.style.width = `${progress}%`;
            }, 100);
            
            // Update level badges
            currentBadge.textContent = `Niv. ${level}`;
            nextBadge.textContent = level >= 7 ? 'MAX' : `Niv. ${level + 1}`;
            
            if (level >= 7) {
                nextBadge.style.background = 'linear-gradient(135deg, gold, orange)';
                nextBadge.style.color = '#000';
                nextBadge.style.border = 'none';
            }
        }

        async function loadUserBadges(userId) {
            const badgesData = await api.getBadges(userId);
            const badgesGrid = document.getElementById('user-badges-grid');
            const badgeCountEl = document.getElementById('user-badges-display');
            const coursesCountEl = document.getElementById('user-courses-display');
            
            if (badgeCountEl) {
                badgeCountEl.textContent = badgesData.unlocked?.length || 0;
            }
            
            // Get progression for courses count
            const progression = await api.getProgression(userId);
            if (coursesCountEl) {
                coursesCountEl.textContent = progression.length || 0;
            }
            
            if (!badgesGrid) return;
            
            if (!badgesData.badges || badgesData.badges.length === 0) {
                badgesGrid.innerHTML = '<div class="no-data"><i data-lucide="award"></i><p>Aucun badge disponible</p></div>';
                lucide.createIcons();
                return;
            }
            
            const unlockedIds = badgesData.unlocked?.map(b => b.badge_id) || [];
            
            badgesGrid.innerHTML = badgesData.badges.map(b => {
                const isUnlocked = unlockedIds.includes(b.id);
                return `
                    <div class="badge-item ${isUnlocked ? '' : 'locked'}" title="${b.description}" style="--badge-color: ${b.color || '#00f3ff'}">
                        <div class="badge-icon" style="color: ${isUnlocked ? (b.color || '#00f3ff') : '#666'}">
                            <i data-lucide="${b.icon || 'award'}"></i>
                        </div>
                        <div class="badge-name">${b.name}</div>
                    </div>
                `;
            }).join('');
            
            lucide.createIcons();
        }

        async function loadUserCertificates(userId) {
            const certificates = await api.getCertificates(userId);
            const certsGrid = document.getElementById('user-certificates-grid');
            const certCountEl = document.getElementById('user-certs-display');
            
            if (certCountEl) {
                certCountEl.textContent = certificates.length || 0;
            }
            
            if (!certsGrid) return;
            
            if (!certificates || certificates.length === 0) {
                certsGrid.innerHTML = '<div class="no-data"><i data-lucide="scroll"></i><p>Complétez des cours avec 70%+ pour obtenir des certificats</p></div>';
                lucide.createIcons();
                return;
            }
            
            certsGrid.innerHTML = certificates.map(c => `
                <div class="certificate-item">
                    <span class="cert-score">${c.score}%</span>
                    <h4>${c.course_title}</h4>
                    <div class="cert-code">${c.certificate_code}</div>
                    <div class="cert-date">Obtenu le ${new Date(c.issued_at).toLocaleDateString('fr-FR')}</div>
                </div>
            `).join('');
            
            lucide.createIcons();
        }
    }

    // ==========================================
    // LEADERBOARD LOGIC
    // ==========================================
    
    async function loadLeaderboards() {
        const data = await api.getLeaderboard();
        
        const groupTbody = document.getElementById('group-leaderboard-list');
        if (!groupTbody) return;
        
        // Mettre à jour les stats globales
        const totalUsersEl = document.getElementById('lb-total-users');
        const totalXpEl = document.getElementById('lb-total-xp');
        
        if (totalUsersEl && data.stats) {
            totalUsersEl.textContent = data.stats.total_users || 0;
        }
        if (totalXpEl && data.stats) {
            totalXpEl.textContent = (data.stats.total_xp || 0).toLocaleString();
        }
        
        groupTbody.innerHTML = '';
        
        const leaderboard = data.leaderboard || data || [];

        if (leaderboard.length === 0) {
            groupTbody.innerHTML = '<tr><td colspan="6" style="text-align:center; color:#666;">Aucun participant</td></tr>';
        } else {
            leaderboard.forEach((user, index) => {
                const rank = user.rank || index + 1;
                let rankBadge = rank;
                if (rank <= 3) rankBadge = `<div class="rank-badge rank-${rank}">${rank}</div>`;

                const tr = document.createElement('tr');
                // Mettre en évidence l'utilisateur connecté
                const currentUser = JSON.parse(sessionStorage.getItem('currentUser'));
                if (currentUser && currentUser.id === user.id) {
                    tr.style.background = 'rgba(0, 255, 136, 0.1)';
                }
                
                tr.innerHTML = `
                    <td>${rankBadge}</td>
                    <td style="color: var(--primary-color); font-weight: bold;">
                        ${user.avatar ? `<span style="margin-right: 0.5rem;">${user.avatar}</span>` : ''}
                        ${user.username}
                    </td>
                    <td>${getLevelName(user.level)}</td>
                    <td>${parseInt(user.xp).toLocaleString()}</td>
                    <td>${user.courses_completed || 0}</td>
                    <td>${user.badges_count || 0}</td>
                `;
                groupTbody.appendChild(tr);
            });
        }
    }

    // ==========================================
    // ADMIN PANEL LOGIC
    // ==========================================
    
    async function initAdminPanel() {
        // Check if user is admin
        const sessionUser = JSON.parse(sessionStorage.getItem('currentUser'));
        if (!sessionUser || sessionUser.role !== 'admin') {
            loadTemplate('home');
            return;
        }

        // Load stats
        const stats = await api.getStats();
        if (stats) {
            document.getElementById('stat-users').textContent = stats.users || 0;
            document.getElementById('stat-courses').textContent = stats.courses || 0;
            document.getElementById('stat-questions').textContent = stats.questions || 0;
            document.getElementById('stat-completions').textContent = stats.completions || 0;

            // Recent courses
            const recentCoursesList = document.getElementById('recent-courses-list');
            if (recentCoursesList && stats.recentCourses) {
                recentCoursesList.innerHTML = stats.recentCourses.map(c => `
                    <div style="padding: 0.5rem 0; border-bottom: 1px solid var(--glass-border);">
                        <span style="color: var(--primary-color);">${c.title}</span>
                        <small style="display: block; color: #666;">${c.difficulty}</small>
                    </div>
                `).join('') || '<p style="color:#666;">Aucun cours</p>';
            }

            // Recent users
            const recentUsersList = document.getElementById('recent-users-list');
            if (recentUsersList && stats.recentUsers) {
                recentUsersList.innerHTML = stats.recentUsers.map(u => `
                    <div style="padding: 0.5rem 0; border-bottom: 1px solid var(--glass-border);">
                        <span>${u.username}</span>
                        <small style="display: block; color: #666;">${u.email}</small>
                    </div>
                `).join('') || '<p style="color:#666;">Aucun utilisateur</p>';
            }
        }

        // Tab navigation
        document.querySelectorAll('.admin-tab').forEach(tab => {
            tab.addEventListener('click', () => {
                document.querySelectorAll('.admin-tab').forEach(t => {
                    t.classList.remove('active');
                    t.classList.remove('btn-primary');
                    t.classList.add('btn-outline');
                });
                tab.classList.add('active');
                tab.classList.remove('btn-outline');
                tab.classList.add('btn-primary');

                document.querySelectorAll('.admin-panel').forEach(p => p.style.display = 'none');
                document.getElementById(`admin-panel-${tab.dataset.tab}`).style.display = 'block';

                // Load data for specific tabs
                if (tab.dataset.tab === 'courses') loadAdminCourses();
                if (tab.dataset.tab === 'questions') loadAdminQuestions();
                if (tab.dataset.tab === 'users') loadAdminUsersTable();
            });
        });

        // Initialize icons
        lucide.createIcons();
    }

    async function loadAdminCourses() {
        const courses = await api.getCourses();
        const tbody = document.getElementById('courses-list');
        if (!tbody) return;

        tbody.innerHTML = courses.map(c => `
            <tr>
                <td>#${c.id}</td>
                <td>
                    <strong>${c.title}</strong>
                    <small style="display: block; color: #666;">${c.description?.substring(0, 50)}...</small>
                </td>
                <td>
                    <span class="difficulty-badge difficulty-${c.difficulty?.toLowerCase()}">${c.difficulty}</span>
                </td>
                <td>${c.nb_questions || 0}</td>
                <td>
                    <button class="btn btn-outline btn-sm" onclick="editCourse(${c.id})" style="margin-right: 0.5rem;">
                        <i data-lucide="edit-2" style="width: 14px; height: 14px;"></i>
                    </button>
                    <button class="btn btn-outline btn-sm" onclick="deleteCourse(${c.id})" style="color: var(--accent-color); border-color: var(--accent-color);">
                        <i data-lucide="trash-2" style="width: 14px; height: 14px;"></i>
                    </button>
                </td>
            </tr>
        `).join('') || '<tr><td colspan="5" style="text-align: center;">Aucun cours</td></tr>';

        lucide.createIcons();
    }

    async function loadAdminQuestions() {
        // Load courses for filter
        const courses = await api.getCourses();
        const filterSelect = document.getElementById('filter-course');
        const questionCourseSelect = document.getElementById('question-course');

        if (filterSelect) {
            filterSelect.innerHTML = '<option value="">-- Tous les cours --</option>' +
                courses.map(c => `<option value="${c.id}">${c.title}</option>`).join('');

            filterSelect.addEventListener('change', async () => {
                const courseId = filterSelect.value;
                const questions = await api.getQuestions(courseId || null);
                renderQuestions(questions);
            });
        }

        if (questionCourseSelect) {
            questionCourseSelect.innerHTML = '<option value="">-- Sélectionner un cours --</option>' +
                courses.map(c => `<option value="${c.id}">${c.title}</option>`).join('');
        }

        // Load all questions
        const questions = await api.getQuestions();
        renderQuestions(questions);
    }

    function renderQuestions(questions) {
        const tbody = document.getElementById('questions-list');
        if (!tbody) return;

        tbody.innerHTML = questions.map(q => `
            <tr>
                <td>#${q.id}</td>
                <td><span style="color: var(--secondary-color);">${q.course_title}</span></td>
                <td>${q.question_text?.substring(0, 60)}...</td>
                <td><span style="color: var(--primary-color); font-weight: bold;">${q.correct_option.toUpperCase()}</span></td>
                <td>
                    <button class="btn btn-outline btn-sm" onclick="editQuestion(${q.id})" style="margin-right: 0.5rem;">
                        <i data-lucide="edit-2" style="width: 14px; height: 14px;"></i>
                    </button>
                    <button class="btn btn-outline btn-sm" onclick="deleteQuestion(${q.id})" style="color: var(--accent-color); border-color: var(--accent-color);">
                        <i data-lucide="trash-2" style="width: 14px; height: 14px;"></i>
                    </button>
                </td>
            </tr>
        `).join('') || '<tr><td colspan="5" style="text-align: center;">Aucune question</td></tr>';

        lucide.createIcons();
    }

    async function loadAdminUsersTable() {
        const users = await api.getUsers();
        const tbody = document.getElementById('admin-users-list-full');
        if (!tbody) return;

        const sessionUser = JSON.parse(sessionStorage.getItem('currentUser'));

        tbody.innerHTML = users.map(u => `
            <tr>
                <td>#${u.id}</td>
                <td>
                    ${u.username}
                    ${u.id == sessionUser?.id ? '<span style="color: var(--primary-color);"> (Vous)</span>' : ''}
                </td>
                <td>${u.email}</td>
                <td>
                    <span style="color: ${u.is_admin ? 'var(--accent-color)' : 'inherit'};">
                        ${u.is_admin ? '🛡️ Admin' : 'Utilisateur'}
                    </span>
                </td>
                <td>
                    <span style="color: var(--primary-color);">${u.completed_courses || 0}</span> cours
                </td>
                <td>
                    ${u.id != sessionUser?.id ? `
                        <button class="btn btn-outline btn-sm" onclick="toggleAdmin(${u.id})" style="margin-right: 0.5rem;" title="${u.is_admin ? 'Retirer admin' : 'Promouvoir admin'}">
                            <i data-lucide="shield" style="width: 14px; height: 14px;"></i>
                        </button>
                        <button class="btn btn-outline btn-sm" onclick="deleteUserAdmin(${u.id})" style="color: var(--accent-color); border-color: var(--accent-color);" title="Supprimer">
                            <i data-lucide="trash-2" style="width: 14px; height: 14px;"></i>
                        </button>
                    ` : '-'}
                </td>
            </tr>
        `).join('') || '<tr><td colspan="6" style="text-align: center;">Aucun utilisateur</td></tr>';

        lucide.createIcons();
    }

    // ==========================================
    // COURSES VIEW (Public)
    // ==========================================
    async function loadCoursesView() {
        const courses = await api.getCourses();
        const contentArea = document.getElementById('content-area');
        
        if (courses.length === 0) return;

        // Replace static content with dynamic courses
        const bentoGrid = contentArea.querySelector('.bento-grid');
        if (bentoGrid) {
            bentoGrid.innerHTML = courses.map(c => `
                <div class="card" data-course-id="${c.id}">
                    <div class="card-icon"><i data-lucide="${getDifficultyIcon(c.difficulty)}"></i></div>
                    <h3>${c.title}</h3>
                    <p>${c.description}</p>
                    <span class="difficulty-badge difficulty-${c.difficulty?.toLowerCase()}">${c.difficulty}</span>
                    <a href="#" class="card-action" onclick="viewCourse(${c.id}); return false;">Commencer <i data-lucide="play" size="16"></i></a>
                </div>
            `).join('');
            
            lucide.createIcons();
            initTiltEffect();
        }
    }

    function getDifficultyIcon(difficulty) {
        switch(difficulty) {
            case 'Facile': return 'shield';
            case 'Intermédiaire': return 'shield-alert';
            case 'Difficile': return 'skull';
            default: return 'book-open';
        }
    }

    // Make functions globally accessible
    window.showAddCourseForm = function() {
        document.getElementById('course-modal-title').textContent = 'Nouveau Cours';
        document.getElementById('course-form').reset();
        document.getElementById('course-id').value = '';
        document.getElementById('course-modal').style.display = 'flex';
    };

    window.closeCourseModal = function() {
        document.getElementById('course-modal').style.display = 'none';
    };

    window.showAddQuestionForm = function() {
        document.getElementById('question-modal-title').textContent = 'Nouvelle Question';
        document.getElementById('question-form').reset();
        document.getElementById('question-id').value = '';
        document.getElementById('question-modal').style.display = 'flex';
        loadAdminQuestions(); // To populate course dropdown
    };

    window.closeQuestionModal = function() {
        document.getElementById('question-modal').style.display = 'none';
    };

    window.editCourse = async function(id) {
        const course = await api.getCourse(id);
        if (!course) return;

        document.getElementById('course-modal-title').textContent = 'Modifier le Cours';
        document.getElementById('course-id').value = course.id;
        document.getElementById('course-title').value = course.title;
        document.getElementById('course-description').value = course.description;
        document.getElementById('course-difficulty').value = course.difficulty;
        document.getElementById('course-content').value = course.content;
        document.getElementById('course-modal').style.display = 'flex';
    };

    window.deleteCourse = async function(id) {
        if (!confirm('Supprimer ce cours et toutes ses questions ?')) return;
        const result = await api.deleteCourse(id);
        if (result.success) {
            loadAdminCourses();
        } else {
            alert('Erreur: ' + result.message);
        }
    };

    window.editQuestion = async function(id) {
        const questions = await api.getQuestions();
        const question = questions.find(q => q.id == id);
        if (!question) return;

        document.getElementById('question-modal-title').textContent = 'Modifier la Question';
        document.getElementById('question-id').value = question.id;
        document.getElementById('question-course').value = question.course_id;
        document.getElementById('question-text').value = question.question || question.question_text;
        document.getElementById('option-a').value = question.option_a;
        document.getElementById('option-b').value = question.option_b;
        document.getElementById('option-c').value = question.option_c;
        document.getElementById('option-d').value = question.option_d || '';
        document.getElementById('correct-option').value = question.correct_answer || question.correct_option;
        document.getElementById('question-explanation').value = question.explanation || '';
        document.getElementById('question-modal').style.display = 'flex';
    };

    window.deleteQuestion = async function(id) {
        if (!confirm('Supprimer cette question ?')) return;
        const result = await api.deleteQuestion(id);
        if (result.success) {
            loadAdminQuestions();
        } else {
            alert('Erreur: ' + result.message);
        }
    };

    window.toggleAdmin = async function(id) {
        if (!confirm('Modifier les droits admin de cet utilisateur ?')) return;
        await api.toggleUserAdmin(id);
        loadAdminUsersTable();
    };

    window.deleteUserAdmin = async function(id) {
        if (!confirm('Supprimer cet utilisateur ?')) return;
        await api.deleteUser(id);
        loadAdminUsersTable();
    };

    window.viewCourse = async function(id) {
        const course = await api.getCourse(id);
        if (!course) return;

        const contentArea = document.getElementById('content-area');
        contentArea.innerHTML = `
            <div style="margin-bottom: 1rem;">
                <button class="btn btn-outline" onclick="loadTemplate('cours')">
                    <i data-lucide="arrow-left"></i> Retour aux cours
                </button>
            </div>
            <h1>${course.title}</h1>
            <span class="difficulty-badge difficulty-${course.difficulty?.toLowerCase()}">${course.difficulty}</span>
            <div class="card" style="margin-top: 2rem;">
                <div class="course-content">${course.content}</div>
            </div>
            <div style="margin-top: 2rem;">
                <button class="btn btn-primary" onclick="startQuiz(${course.id})">
                    <i data-lucide="brain-circuit"></i> Commencer le Quiz
                </button>
            </div>
        `;
        lucide.createIcons();
    };

    window.startQuiz = async function(courseId) {
        const questions = await api.getQuestions(courseId);
        if (questions.length === 0) {
            alert('Aucune question disponible pour ce cours.');
            return;
        }

        let currentQuestion = 0;
        let score = 0;

        function renderQuestion() {
            const q = questions[currentQuestion];
            const questionText = q.question || q.question_text;
            const correctAnswer = (q.correct_answer || q.correct_option || '').toUpperCase();
            const contentArea = document.getElementById('content-area');
            
            // Construire les options (A, B, C, et D si disponible)
            let optionsHtml = `
                <button class="btn btn-outline quiz-option" data-option="A" style="text-align: left; padding: 1rem;">
                    <strong>A.</strong> ${q.option_a}
                </button>
                <button class="btn btn-outline quiz-option" data-option="B" style="text-align: left; padding: 1rem;">
                    <strong>B.</strong> ${q.option_b}
                </button>
                <button class="btn btn-outline quiz-option" data-option="C" style="text-align: left; padding: 1rem;">
                    <strong>C.</strong> ${q.option_c}
                </button>
            `;
            if (q.option_d && q.option_d.trim()) {
                optionsHtml += `
                <button class="btn btn-outline quiz-option" data-option="D" style="text-align: left; padding: 1rem;">
                    <strong>D.</strong> ${q.option_d}
                </button>
                `;
            }
            
            contentArea.innerHTML = `
                <h1>Quiz <span class="text-gradient">${currentQuestion + 1}/${questions.length}</span></h1>
                <div class="card" style="max-width: 800px; margin: 2rem auto;">
                    <h3 style="margin-bottom: 2rem;">${questionText}</h3>
                    <div class="quiz-options" style="display: flex; flex-direction: column; gap: 1rem;">
                        ${optionsHtml}
                    </div>
                </div>
            `;

            document.querySelectorAll('.quiz-option').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    const selected = e.currentTarget.dataset.option.toUpperCase();
                    const correct = correctAnswer;

                    document.querySelectorAll('.quiz-option').forEach(b => {
                        b.disabled = true;
                        if (b.dataset.option.toUpperCase() === correct) {
                            b.style.borderColor = 'var(--success, #10b981)';
                            b.style.background = 'rgba(16, 185, 129, 0.2)';
                        }
                        if (b.dataset.option.toUpperCase() === selected && selected !== correct) {
                            b.style.borderColor = 'var(--danger, #ef4444)';
                            b.style.background = 'rgba(239, 68, 68, 0.2)';
                        }
                    });

                    if (selected === correct) score++;

                    setTimeout(() => {
                        currentQuestion++;
                        if (currentQuestion < questions.length) {
                            renderQuestion();
                        } else {
                            showQuizResults(courseId, score, questions.length);
                        }
                    }, 1500);
                });
            });
        }

        renderQuestion();
    };

    async function showQuizResults(courseId, score, total) {
        const percentage = Math.round((score / total) * 100);
        const contentArea = document.getElementById('content-area');
        
        // Sauvegarder la progression si l'utilisateur est connecté
        const sessionUser = JSON.parse(sessionStorage.getItem('currentUser'));
        let certificateCode = null;
        let xpGained = 0;
        let leveledUp = false;
        
        if (sessionUser) {
            // Calculer l'XP gagné (basé sur le score)
            xpGained = Math.round(score * 10 * (percentage / 100)); // 10 XP par bonne réponse × multiplicateur
            if (percentage >= 70) xpGained += 20; // Bonus de réussite
            if (percentage === 100) xpGained += 30; // Bonus parfait
            
            // Ajouter l'XP
            const xpResult = await api.addXp(sessionUser.id, xpGained);
            if (xpResult.success) {
                leveledUp = xpResult.leveled_up;
                // Mettre à jour la session
                sessionUser.xp = xpResult.new_xp;
                sessionUser.level = xpResult.new_level;
                sessionStorage.setItem('currentUser', JSON.stringify(sessionUser));
            }
            
            if (percentage >= 70) {
                await api.saveProgression(sessionUser.id, courseId, 1);
                
                // Générer un certificat
                const certResult = await api.generateCertificate(sessionUser.id, courseId, percentage);
                if (certResult.success) {
                    certificateCode = certResult.certificate_code;
                }
                
                // Vérifier les nouveaux badges
                await api.checkBadges(sessionUser.id);
            }
        }
        
        contentArea.innerHTML = `
            <div style="text-align: center; max-width: 600px; margin: 0 auto;">
                <h1>Quiz <span class="text-gradient">Terminé!</span></h1>
                <div class="card" style="margin-top: 2rem;">
                    <div style="font-size: 4rem; margin-bottom: 1rem;">
                        ${percentage >= 70 ? '🎉' : percentage >= 50 ? '👍' : '📚'}
                    </div>
                    <h2>${score} / ${total}</h2>
                    <p style="color: var(--primary-color); font-size: 1.5rem; margin: 1rem 0;">
                        ${percentage}%
                    </p>
                    <p style="color: #666;">
                        ${percentage >= 70 ? 'Excellent travail !' : percentage >= 50 ? 'Pas mal, continuez !' : 'Révisez et réessayez !'}
                    </p>
                    ${xpGained > 0 ? `
                        <div style="margin-top: 1rem; padding: 0.75rem; background: rgba(0, 255, 136, 0.1); border-radius: 8px;">
                            <p style="color: var(--accent-color); font-weight: 600;">
                                +${xpGained} XP ${leveledUp ? '🎊 Niveau supérieur!' : ''}
                            </p>
                        </div>
                    ` : ''}
                    ${certificateCode ? `
                        <div style="margin-top: 1.5rem; padding: 1rem; background: rgba(0, 243, 255, 0.1); border-radius: 8px; border: 1px solid var(--primary-color);">
                            <p style="color: var(--primary-color); font-weight: 600;">🏆 Certificat obtenu !</p>
                            <p style="font-family: 'JetBrains Mono', monospace; color: #999;">${certificateCode}</p>
                        </div>
                    ` : ''}
                </div>
                <div style="margin-top: 2rem; display: flex; gap: 1rem; justify-content: center;">
                    <button class="btn btn-primary" onclick="startQuiz(${courseId})">
                        <i data-lucide="refresh-cw"></i> Recommencer
                    </button>
                    <button class="btn btn-outline" onclick="loadTemplate('cours')">
                        <i data-lucide="book-open"></i> Autres cours
                    </button>
                </div>
            </div>
        `;
        lucide.createIcons();
    }

    // Handle course form submission
    document.addEventListener('submit', async (e) => {
        if (e.target.id === 'course-form') {
            e.preventDefault();
            const id = document.getElementById('course-id').value;
            const courseData = {
                title: document.getElementById('course-title').value,
                description: document.getElementById('course-description').value,
                difficulty: document.getElementById('course-difficulty').value,
                content: document.getElementById('course-content').value
            };

            let result;
            if (id) {
                courseData.id = parseInt(id);
                result = await api.updateCourse(courseData);
            } else {
                result = await api.createCourse(courseData);
            }

            if (result.success) {
                closeCourseModal();
                loadAdminCourses();
            } else {
                alert('Erreur: ' + result.message);
            }
        }

        if (e.target.id === 'question-form') {
            e.preventDefault();
            const id = document.getElementById('question-id').value;
            const questionData = {
                course_id: document.getElementById('question-course').value,
                question: document.getElementById('question-text').value,
                option_a: document.getElementById('option-a').value,
                option_b: document.getElementById('option-b').value,
                option_c: document.getElementById('option-c').value,
                option_d: document.getElementById('option-d')?.value || '',
                correct_answer: document.getElementById('correct-option').value.toUpperCase(),
                explanation: document.getElementById('question-explanation').value
            };

            let result;
            if (id) {
                questionData.id = parseInt(id);
                result = await api.updateQuestion(questionData);
            } else {
                result = await api.createQuestion(questionData);
            }

            if (result.success) {
                closeQuestionModal();
                loadAdminQuestions();
            } else {
                alert('Erreur: ' + result.message);
            }
        }
    });

    // Make loadTemplate globally accessible for onclick handlers
    window.loadTemplate = loadTemplate;

    // ==========================================
    // PHISHING SIMULATION VIEW
    // ==========================================
    async function initPhishingView() {
        const sessionUser = JSON.parse(sessionStorage.getItem('currentUser'));
        
        // Load scenarios
        const scenarios = await api.getPhishingScenarios(sessionUser?.id);
        renderPhishingScenarios(scenarios);
        
        // Load stats if logged in
        if (sessionUser) {
            const statsData = await api.getPhishingStats(sessionUser.id);
            updatePhishingStats(statsData.stats);
        }
    }

    function updatePhishingStats(stats) {
        if (!stats) return;
        const total = parseInt(stats.total) || 0;
        const correct = parseInt(stats.correct) || 0;
        const rate = total > 0 ? Math.round((correct / total) * 100) : 0;
        const avgTime = Math.round(parseFloat(stats.avg_time) || 0);
        
        document.getElementById('phishingTotal').textContent = total;
        document.getElementById('phishingCorrect').textContent = correct;
        document.getElementById('phishingRate').textContent = rate + '%';
        document.getElementById('phishingTime').textContent = avgTime + 's';
    }

    function renderPhishingScenarios(scenarios) {
        const grid = document.getElementById('scenariosGrid');
        if (!grid) return;

        grid.innerHTML = scenarios.map(s => {
            let statusClass = '';
            let statusIcon = '';
            if (s.completed !== undefined && s.completed) {
                statusClass = s.correct ? 'completed' : 'completed wrong';
                statusIcon = s.correct 
                    ? '<i data-lucide="check-circle"></i>' 
                    : '<i data-lucide="x-circle"></i>';
            }
            
            return `
                <div class="scenario-card ${statusClass}" data-scenario-id="${s.id}">
                    <span class="type-badge ${s.type}">
                        <i data-lucide="${s.type === 'email' ? 'mail' : s.type === 'sms' ? 'smartphone' : 'globe'}"></i>
                        ${s.type.toUpperCase()}
                    </span>
                    <h3>${s.title}</h3>
                    <span class="difficulty ${s.difficulty}">${s.difficulty}</span>
                    ${statusIcon ? `<div class="status-icon">${statusIcon}</div>` : ''}
                </div>
            `;
        }).join('') || '<p style="text-align: center; color: var(--text-muted);">Aucun scénario disponible</p>';

        // Add click listeners to scenario cards
        grid.querySelectorAll('.scenario-card').forEach(card => {
            card.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                const scenarioId = this.getAttribute('data-scenario-id');
                console.log('Scenario card clicked, ID:', scenarioId);
                if (scenarioId) {
                    openScenario(parseInt(scenarioId));
                }
            });
        });

        lucide.createIcons();
    }

    let currentScenarioStartTime = null;

    window.openScenario = async function(id) {
        console.log('openScenario called with id:', id);
        const scenario = await api.getPhishingScenario(id);
        console.log('Scenario fetched:', scenario);
        if (!scenario) return;

        currentScenarioStartTime = Date.now();
        
        const contentDiv = document.getElementById('scenarioContent');
        const resultDiv = document.getElementById('scenarioResult');
        
        contentDiv.style.display = 'block';
        resultDiv.style.display = 'none';
        
        let previewHtml = '';
        
        if (scenario.type === 'email') {
            previewHtml = `
                <div class="scenario-preview email">
                    <div class="email-header">
                        <div class="from"><span class="label">De:</span> <span class="value">${scenario.sender}</span></div>
                        <div class="subject"><span class="label">Objet:</span> <span class="value">${scenario.subject}</span></div>
                    </div>
                    <div class="email-body">${scenario.content.replace(/\n/g, '<br>')}</div>
                </div>
            `;
        } else if (scenario.type === 'sms') {
            previewHtml = `
                <div class="scenario-preview sms">
                    <div class="sms-sender">${scenario.sender}</div>
                    <div class="sms-body">${scenario.content}</div>
                </div>
            `;
        } else {
            previewHtml = `
                <div class="scenario-preview website">
                    <div class="website-url">${scenario.sender}</div>
                    <div class="website-body">${scenario.content}</div>
                </div>
            `;
        }
        
        contentDiv.innerHTML = `
            <h2 style="margin-bottom: 1.5rem; text-align: center;">${scenario.title}</h2>
            ${previewHtml}
            <div class="scenario-question">
                <h3>Ce message est-il une tentative de phishing ?</h3>
                <div class="answer-buttons">
                    <button class="answer-btn phishing" onclick="submitPhishingAnswer(${scenario.id}, true)">
                        <i data-lucide="alert-triangle"></i> Phishing
                    </button>
                    <button class="answer-btn legitimate" onclick="submitPhishingAnswer(${scenario.id}, false)">
                        <i data-lucide="check"></i> Légitime
                    </button>
                </div>
            </div>
        `;
        
        const modal = document.getElementById('scenarioModal');
        console.log('Modal element:', modal);
        if (modal) {
            modal.style.display = 'flex';
            console.log('Modal display set to flex');
        } else {
            console.error('Modal not found!');
        }
        lucide.createIcons();
    };

    window.submitPhishingAnswer = async function(scenarioId, isPhishing) {
        const sessionUser = JSON.parse(sessionStorage.getItem('currentUser'));
        if (!sessionUser) {
            alert('Connectez-vous pour sauvegarder vos résultats');
            return;
        }
        
        const timeTaken = Math.round((Date.now() - currentScenarioStartTime) / 1000);
        const result = await api.submitPhishingAnswer(sessionUser.id, scenarioId, isPhishing, timeTaken);
        
        const contentDiv = document.getElementById('scenarioContent');
        const resultDiv = document.getElementById('scenarioResult');
        
        contentDiv.style.display = 'none';
        resultDiv.style.display = 'block';
        
        const isCorrect = result.is_correct;
        const indicators = result.indicators ? result.indicators.split(',').map(i => `<li>${i.trim()}</li>`).join('') : '';
        
        resultDiv.innerHTML = `
            <div class="result-container ${isCorrect ? 'correct' : 'incorrect'}">
                <i data-lucide="${isCorrect ? 'check-circle' : 'x-circle'}" class="result-icon"></i>
                <h2>${isCorrect ? 'Bonne réponse !' : 'Mauvaise réponse'}</h2>
                <p>Ce message était ${result.correct_answer ? 'bien du phishing' : 'légitime'}.</p>
                ${result.xp_earned > 0 ? `<p class="xp-earned"><i data-lucide="zap"></i> +${result.xp_earned} XP</p>` : ''}
                
                <div class="result-explanation">
                    <h4><i data-lucide="info"></i> Explication</h4>
                    <p>${result.explanation || 'Pas d\'explication disponible.'}</p>
                    ${indicators ? `<ul class="indicators-list">${indicators}</ul>` : ''}
                </div>
                
                <div class="result-actions">
                    <button class="btn btn-primary" onclick="closeScenarioModal(); initPhishingView();">
                        Continuer
                    </button>
                </div>
            </div>
        `;
        
        lucide.createIcons();
        
        // Check for new badges
        await api.checkBadges(sessionUser.id);
    };

    window.closeScenarioModal = function() {
        document.getElementById('scenarioModal').style.display = 'none';
    };

    // ==========================================
    // RESOURCES VIEW
    // ==========================================
    async function initResourcesView() {
        await loadResources();
    }

    async function loadResources(category = null, difficulty = null) {
        const data = await api.getResources(category, difficulty);
        const contentDiv = document.getElementById('resourcesContent');
        if (!contentDiv) return;

        const categoryNames = {
            article: '📝 Articles',
            video: '🎬 Vidéos',
            tool: '🛠️ Outils',
            documentation: '📚 Documentation',
            external: '🔗 Liens externes'
        };

        let html = '';
        
        if (data.grouped && Object.keys(data.grouped).length > 0) {
            for (const [cat, resources] of Object.entries(data.grouped)) {
                html += `
                    <div class="resources-section">
                        <h2>${categoryNames[cat] || cat}</h2>
                        <div class="resources-grid">
                            ${resources.map(r => `
                                <div class="resource-card ${r.category}" data-resource-id="${r.id}">
                                    <div class="card-header">
                                        <div class="icon-wrapper">
                                            <i data-lucide="${r.icon || 'file-text'}"></i>
                                        </div>
                                        <div>
                                            <h3>${r.title}</h3>
                                            <span class="difficulty-badge ${r.difficulty}">${r.difficulty}</span>
                                        </div>
                                    </div>
                                    <p>${r.description}</p>
                                    <div class="card-footer">
                                        <span class="action-hint">
                                            ${r.url ? '<i data-lucide="external-link"></i> Ouvrir' : '<i data-lucide="book-open"></i> Lire'}
                                        </span>
                                    </div>
                                </div>
                            `).join('')}
                        </div>
                    </div>
                `;
            }
        } else {
            html = `
                <div class="no-resources">
                    <i data-lucide="folder-open"></i>
                    <p>Aucune ressource trouvée</p>
                </div>
            `;
        }

        contentDiv.innerHTML = html;
        
        // Add click listeners to resource cards
        contentDiv.querySelectorAll('.resource-card').forEach(card => {
            card.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                const resourceId = this.getAttribute('data-resource-id');
                console.log('Resource card clicked, ID:', resourceId);
                if (resourceId) {
                    openResource(parseInt(resourceId));
                }
            });
        });
        
        lucide.createIcons();
    }

    window.filterResources = function() {
        const category = document.getElementById('resourceCategoryFilter').value;
        const difficulty = document.getElementById('resourceDifficultyFilter').value;
        loadResources(category || null, difficulty || null);
    };

    window.closeResourceModal = function() {
        const modal = document.getElementById('resourceModal');
        if (modal) modal.style.display = 'none';
    };

    window.openResource = async function(id) {
        console.log('openResource called with id:', id);
        const resource = await api.getResource(id);
        console.log('Resource fetched:', resource);
        if (!resource) {
            console.error('Resource not found!');
            return;
        }

        const modalContent = document.getElementById('resourceModalContent');
        console.log('Modal content element:', modalContent);
        
        const categoryColors = {
            article: '#3b82f6',
            video: '#ef4444',
            tool: '#10b981',
            documentation: '#f59e0b',
            external: '#8b5cf6'
        };
        
        let contentHtml = '';
        
        if (resource.content) {
            // Convert markdown-like content to HTML
            contentHtml = resource.content
                .replace(/## (.*)/g, '<h2>$1</h2>')
                .replace(/### (.*)/g, '<h3>$1</h3>')
                .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
                .replace(/\n- (.*)/g, '\n<li>$1</li>')
                .replace(/(<li>.*<\/li>\n?)+/g, '<ul>$&</ul>')
                .replace(/\n\d+\. (.*)/g, '\n<li>$1</li>')
                .replace(/\n/g, '<br>');
        }
        
        modalContent.innerHTML = `
            <div class="resource-modal-header">
                <div class="icon-wrapper" style="background: ${categoryColors[resource.category]}20; color: ${categoryColors[resource.category]};">
                    <i data-lucide="${resource.icon || 'file-text'}"></i>
                </div>
                <div>
                    <h2>${resource.title}</h2>
                    <span class="difficulty-badge ${resource.difficulty}">${resource.difficulty}</span>
                </div>
            </div>
            <div class="resource-modal-content">
                ${contentHtml || `<p>${resource.description}</p>`}
            </div>
            ${resource.url ? `
                <a href="${resource.url}" target="_blank" class="external-link-btn">
                    <i data-lucide="external-link"></i> Ouvrir le lien
                </a>
            ` : ''}
        `;
        
        const resourceModal = document.getElementById('resourceModal');
        console.log('Resource modal element:', resourceModal);
        if (resourceModal) {
            resourceModal.style.display = 'flex';
            console.log('Resource modal display set to flex');
        } else {
            console.error('Resource modal not found!');
        }
        lucide.createIcons();
    };

});
