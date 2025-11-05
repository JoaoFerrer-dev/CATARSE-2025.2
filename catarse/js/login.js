const toggleTheme = document.getElementById('toggle-theme');
const body = document.body;

toggleTheme.addEventListener('click', () => {
    body.classList.toggle('dark-mode');
    localStorage.setItem('theme', body.classList.contains('dark-mode') ? 'dark' : 'light');
});


if (localStorage.getItem('theme') === 'dark') {
    body.classList.add('dark-mode');
}


const increaseFont = document.getElementById('increase-font');
const decreaseFont = document.getElementById('decrease-font');
const resetFont = document.getElementById('reset-font');

increaseFont.addEventListener('click', () => {
    changeFontSize(1);
});

decreaseFont.addEventListener('click', () => {
    changeFontSize(-1);
});

resetFont.addEventListener('click', () => {
    document.documentElement.style.fontSize = '16px';
    localStorage.removeItem('fontSize');
});

function changeFontSize(step) {
    const currentSize = parseFloat(getComputedStyle(document.documentElement).fontSize);
    const newSize = currentSize + (step * 2);
    document.documentElement.style.fontSize = `${newSize}px`;
    localStorage.setItem('fontSize', newSize);
}


const savedSize = localStorage.getItem('fontSize');
if (savedSize) {
    document.documentElement.style.fontSize = `${savedSize}px`;
    
}

document.addEventListener('DOMContentLoaded', function() {
const carrossel = document.querySelector('.carrossel-container');
const slides = document.querySelectorAll('.carrossel-slide');
const prevBtn = document.querySelector('.prev');
const nextBtn = document.querySelector('.next');
let currentIndex = 0;
const totalSlides = slides.length;

function updateCarrossel() {
    carrossel.style.transform = `translateX(-${currentIndex * 100}%)`;
    
    // Atualiza indicadores (se estiver usando)
    document.querySelectorAll('.carrossel-indicador').forEach((indicador, index) => {
        indicador.classList.toggle('active', index === currentIndex);
    });
}

function nextSlide() {
    currentIndex = (currentIndex + 1) % totalSlides;
    updateCarrossel();
}

function prevSlide() {
    currentIndex = (currentIndex - 1 + totalSlides) % totalSlides;
    updateCarrossel();
}

// Event listeners
nextBtn.addEventListener('click', nextSlide);
prevBtn.addEventListener('click', prevSlide);

// Opcional: Autoplay
let autoplay = setInterval(nextSlide, 5000);

// Pausa autoplay quando o mouse está sobre o carrossel
document.querySelector('.carrossel').addEventListener('mouseenter', () => {
    clearInterval(autoplay);
});

document.querySelector('.carrossel').addEventListener('mouseleave', () => {
    autoplay = setInterval(nextSlide, 5000);
});

// Opcional: Toque para mobile
let touchStartX = 0;
let touchEndX = 0;

document.querySelector('.carrossel').addEventListener('touchstart', (e) => {
    touchStartX = e.changedTouches[0].screenX;
}, false);

document.querySelector('.carrossel').addEventListener('touchend', (e) => {
    touchEndX = e.changedTouches[0].screenX;
    handleSwipe();
}, false);

function handleSwipe() {
    if (touchEndX < touchStartX - 50) {
        nextSlide();
    }
    if (touchEndX > touchStartX + 50) {
        prevSlide();
    }
}
});

// Menu hamburguer
const hamburger = document.querySelector('.hamburger-menu');
const navList = document.querySelector('.nav-list');

hamburger.addEventListener('click', () => {
hamburger.classList.toggle('active');
navList.classList.toggle('active');

// Impede a rolagem da página quando o menu está aberto
if (navList.classList.contains('active')) {
document.body.style.overflow = 'hidden';
} else {
document.body.style.overflow = 'auto';
}
});

// Fechar menu ao clicar em um link
document.querySelectorAll('.nav-list a').forEach(link => {
link.addEventListener('click', () => {
hamburger.classList.remove('active');
navList.classList.remove('active');
document.body.style.overflow = 'auto';
});
});
// =============================================
// ACESSIBILIDADE - VERSÃO MELHORADA (MOBILE FRIENDLY)
// =============================================

const accessibilityContainer = document.getElementById('accessibility-container');
const accessibilityBtn = document.getElementById('accessibility-main-btn');
const accessibilityOptions = document.getElementById('accessibility-options');

// Variáveis para controle do arraste
let isDragging = false;
let offsetX, offsetY;

// Função para iniciar o arrasto
function startDrag(e) {
e.preventDefault();
isDragging = true;

const clientX = e.clientX || e.touches[0].clientX;
const clientY = e.clientY || e.touches[0].clientY;

const rect = accessibilityContainer.getBoundingClientRect();
offsetX = clientX - rect.left;
offsetY = clientY - rect.top;

accessibilityBtn.style.cursor = 'grabbing';
accessibilityContainer.style.transition = 'none';
}

// Função para mover
function moveDrag(e) {
if (!isDragging) return;
e.preventDefault();

const clientX = e.clientX || (e.touches && e.touches[0].clientX);
const clientY = e.clientY || (e.touches && e.touches[0].clientY);

if (clientX === undefined || clientY === undefined) return;

const x = clientX - offsetX;
const y = clientY - offsetY;

accessibilityContainer.style.left = `${x}px`;
accessibilityContainer.style.top = `${y}px`;
accessibilityContainer.style.right = 'auto';
}

// Função para finalizar o arrasto
function endDrag() {
if (!isDragging) return;
isDragging = false;
accessibilityBtn.style.cursor = 'grab';
accessibilityContainer.style.transition = 'left 0.2s, top 0.2s';
keepInWindow();
}

// Eventos para mouse
accessibilityBtn.addEventListener('mousedown', startDrag);
document.addEventListener('mousemove', moveDrag);
document.addEventListener('mouseup', endDrag);

// Eventos para touch
accessibilityBtn.addEventListener('touchstart', startDrag, {passive: false});
document.addEventListener('touchmove', moveDrag, {passive: false});
document.addEventListener('touchend', endDrag);

// Função para manter dentro da janela
function keepInWindow() {
const container = accessibilityContainer.getBoundingClientRect();
const windowWidth = window.innerWidth;
const windowHeight = window.innerHeight;

let left = parseFloat(accessibilityContainer.style.left) || windowWidth - container.width - 20;
let top = parseFloat(accessibilityContainer.style.top) || 20;

left = Math.max(0, Math.min(left, windowWidth - container.width));
top = Math.max(0, Math.min(top, windowHeight - container.height));

accessibilityContainer.style.left = `${left}px`;
accessibilityContainer.style.top = `${top}px`;
}

// Toggle das opções
accessibilityBtn.addEventListener('click', (e) => {
e.stopPropagation();
accessibilityContainer.classList.toggle('active');
});

// Fechar ao clicar fora
document.addEventListener('click', (e) => {
if (!accessibilityContainer.contains(e.target)) {
accessibilityContainer.classList.remove('active');
}
});

// Ajustar na redimensionamento
window.addEventListener('resize', keepInWindow);
// Validação de login
document.getElementById('form-login').addEventListener('submit', function(e) {
    e.preventDefault();

    const login = document.getElementById('login').value;
    const senha = document.getElementById('senha').value;

    const savedLogin = localStorage.getItem('user_login');
    const savedSenha = localStorage.getItem('user_senha');

    if (login === savedLogin && senha === savedSenha) {
        alert('Login realizado com sucesso!');
        localStorage.setItem('isLoggedIn', 'true'); // <-- Adicione esta linha
        window.location.href = '/index.html';
    } else {
        alert('Login ou senha incorretos!');
    }
});

window.addEventListener('DOMContentLoaded', function() {
    // Ícone do usuário e menu de logout
    const userIcon = document.getElementById('user-icon');
    const logoutMenu = document.getElementById('logout-menu');
    const logoutBtn = document.getElementById('logout-btn');

    if (localStorage.getItem('isLoggedIn') === 'true') {
        const login = localStorage.getItem('user_login') || '';
        if (login.length >= 2 && userIcon && logoutMenu) {
            // Remove texto antigo das iniciais, se houver
            Array.from(userIcon.childNodes).forEach(node => {
                if (node.nodeType === Node.TEXT_NODE) userIcon.removeChild(node);
            });
            // Adiciona as iniciais ANTES do menu de logout
            userIcon.insertBefore(document.createTextNode(login.substring(0, 2).toUpperCase()), logoutMenu);
            userIcon.style.display = 'flex';
        }

        // Mostra/oculta o menu ao clicar no ícone
        userIcon.onclick = function(e) {
            e.stopPropagation();
            if (logoutMenu.style.display === 'none' || logoutMenu.style.display === '') {
                logoutMenu.style.display = 'block';
            } else {
                logoutMenu.style.display = 'none';
            }
        };

        // Fecha o menu se clicar fora
        document.addEventListener('click', function(e) {
            if (logoutMenu && !userIcon.contains(e.target)) {
                logoutMenu.style.display = 'none';
            }
        });

        // Logout (agora pede ao servidor para destruir a sessão)
        if (logoutBtn) {
            logoutBtn.onclick = function(e) {
                e.stopPropagation();
                // Chama endpoint de logout no servidor
                // usar caminho relativo, compatível com páginas dentro de /paginas/
                fetch('../php/logout.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: ''
                }).then(resp => resp.json()).then(data => {
                    // Limpa estado local e recarrega
                    localStorage.removeItem('isLoggedIn');
                    localStorage.removeItem('user_login');
                    localStorage.removeItem('user_senha');
                    window.location.reload();
                }).catch(err => {
                    // Mesmo em caso de erro, limpa local e recarrega para garantir logout do cliente
                    console.error('Erro no logout:', err);
                    localStorage.removeItem('isLoggedIn');
                    localStorage.removeItem('user_login');
                    localStorage.removeItem('user_senha');
                    window.location.reload();
                });
            };
        }
    } else {
        if (userIcon) userIcon.style.display = 'none';
    }
});
function showToast(message, type = '') {
    const toast = document.getElementById('toast');
    toast.textContent = message;
    toast.className = 'toast show' + (type ? ' ' + type : '');
    setTimeout(() => {
        toast.className = 'toast';
    }, 2500);
}

// Limitador de letras para o campo login (apenas 6 letras)
const loginInput = document.getElementById('login');
loginInput.addEventListener('input', function(e) {
    let value = this.value.replace(/[^A-Za-z]/g, '');
    if (value.length > 6) value = value.slice(0, 6);
    this.value = value;
});

// Validação de login e senha (senha: 6 a 12 números)
document.getElementById('form-login').addEventListener('submit', function(e) {
    e.preventDefault();
    const login = document.getElementById('login').value;
    const senha = document.getElementById('senha').value;
    const savedLogin = localStorage.getItem('user_login');
    const savedSenha = localStorage.getItem('user_senha');
    if (login === savedLogin && /^[0-9]{6,12}$/.test(senha) && senha === savedSenha) {
        alert('Login realizado com sucesso!');
        localStorage.setItem('isLoggedIn', 'true');
        window.location.href = 'index.html';
    } else {
        alert('Login ou senha inválidos!');
    }
});

document.addEventListener('DOMContentLoaded', function() {
    // ====== ACESSIBILIDADE ======
    const accessibilityContainer = document.getElementById('accessibility-container');
    if (accessibilityContainer) {
        accessibilityContainer.style.top = '70vh'; // 70% da altura da tela
        accessibilityContainer.style.left = '';
        accessibilityContainer.style.right = '20px';
    }
});
