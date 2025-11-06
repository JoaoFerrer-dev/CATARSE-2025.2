// Esconde o preloader quando a página carregar; adiciona fallback para evitar que o overlay fique preso
(function(){
    const preloader = document.getElementById('preloader');
    function hidePreloader(delay = 500) {
        if (!preloader) return;
        try {
            preloader.style.opacity = '0';
            setTimeout(() => {
                preloader.style.display = 'none';
            }, delay);
        } catch (e) {
            // se alguma coisa falhar, remove rapidamente
            preloader.style.display = 'none';
        }
    }

    // Ao carregar:
    window.addEventListener('load', function() {
        setTimeout(() => hidePreloader(500), 1000);
    });

    // Fallback: garante que o preloader será escondido mesmo que 'load' não seja disparado ou haja erro
    setTimeout(() => hidePreloader(300), 4000);
})();

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

window.addEventListener('DOMContentLoaded', function() {
    // ====== CARROSSEL ======
    const carrossel = document.querySelector('.carrossel-container');
    if (carrossel) {
        const slides = document.querySelectorAll('.carrossel-slide');
        const prevBtn = document.querySelector('.prev');
        const nextBtn = document.querySelector('.next');
        let currentIndex = 0;
        const totalSlides = slides.length;

        function updateCarrossel() {
            carrossel.style.transform = `translateX(-${currentIndex * 100}%)`;
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

        if (nextBtn && prevBtn) {
            nextBtn.addEventListener('click', nextSlide);
            prevBtn.addEventListener('click', prevSlide);
        }

        let autoplay = setInterval(nextSlide, 5000);

        const carrosselEl = document.querySelector('.carrossel');
        if (carrosselEl) {
            carrosselEl.addEventListener('mouseenter', () => clearInterval(autoplay));
            carrosselEl.addEventListener('mouseleave', () => autoplay = setInterval(nextSlide, 5000));
            let touchStartX = 0;
            let touchEndX = 0;
            carrosselEl.addEventListener('touchstart', (e) => {
                touchStartX = e.changedTouches[0].screenX;
            }, false);
            carrosselEl.addEventListener('touchend', (e) => {
                touchEndX = e.changedTouches[0].screenX;
                if (touchEndX < touchStartX - 50) nextSlide();
                if (touchEndX > touchStartX + 50) prevSlide();
            }, false);
        }
    }

    // ====== MENU HAMBURGUER ======
    const hamburger = document.querySelector('.hamburger-menu');
    const navList = document.querySelector('.nav-list');
    if (hamburger && navList) {
        hamburger.addEventListener('click', () => {
            hamburger.classList.toggle('active');
            navList.classList.toggle('active');
            document.body.style.overflow = navList.classList.contains('active') ? 'hidden' : 'auto';
        });
        document.querySelectorAll('.nav-list a').forEach(link => {
            link.addEventListener('click', () => {
                hamburger.classList.remove('active');
                navList.classList.remove('active');
                document.body.style.overflow = 'auto';
            });
        });
    }

    // ====== ACESSIBILIDADE ======
    const accessibilityContainer = document.getElementById('accessibility-container');
    const accessibilityBtn = document.getElementById('accessibility-main-btn');
    const accessibilityOptions = document.getElementById('accessibility-options');
    let isDragging = false, offsetX, offsetY;

    // Define posição inicial mais abaixo na direita ao carregar
    if (accessibilityContainer) {
        accessibilityContainer.style.top = '70vh'; // 70% da altura da tela
        accessibilityContainer.style.left = '';
        accessibilityContainer.style.right = '20px';
    }

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
    function endDrag() {
        if (!isDragging) return;
        isDragging = false;
        accessibilityBtn.style.cursor = 'grab';
        accessibilityContainer.style.transition = 'left 0.2s, top 0.2s';
        keepInWindow();
    }
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
    if (accessibilityBtn && accessibilityContainer) {
        let touchTimeout;
        let isTouchDragging = false;
        // Clique desktop: abre/fecha menu normalmente
        accessibilityBtn.addEventListener('click', function(e) {
            // Evita conflito com drag
            if (!isTouchDragging) {
                e.stopPropagation();
                accessibilityContainer.classList.toggle('active');
            }
        });
        accessibilityBtn.addEventListener('mousedown', startDrag);
        document.addEventListener('mousemove', moveDrag);
        document.addEventListener('mouseup', endDrag);
        // Mobile: long press arrasta, toque rápido abre menu
        accessibilityBtn.addEventListener('touchstart', function(e) {
            if (e.touches.length > 1) return;
            isTouchDragging = false;
            touchTimeout = setTimeout(() => {
                isTouchDragging = true;
                startDrag(e);
            }, 300); // 300ms para distinguir long press
        }, {passive: false});
        accessibilityBtn.addEventListener('touchend', function(e) {
            clearTimeout(touchTimeout);
            if (isTouchDragging) {
                endDrag();
                isTouchDragging = false;
            } else {
                // Toque rápido abre/fecha menu
                e.stopPropagation();
                accessibilityContainer.classList.toggle('active');
            }
        });
        document.addEventListener('touchmove', function(e) {
            if (isTouchDragging) moveDrag(e);
        }, {passive: false});
        document.addEventListener('click', (e) => {
            if (!accessibilityContainer.contains(e.target)) {
                accessibilityContainer.classList.remove('active');
            }
        });
        window.addEventListener('resize', keepInWindow);
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

// Menu de usuário
document.addEventListener('DOMContentLoaded', function() {
    const userIcon = document.getElementById('user-icon');
    const userMenu = document.getElementById('user-menu');
    
    if (userIcon && userMenu) {
        userIcon.addEventListener('click', function(e) {
            e.stopPropagation();
            userMenu.style.display = userMenu.style.display === 'none' ? 'block' : 'none';
        });
        
        // Fechar menu quando clicar fora
        document.addEventListener('click', function() {
            userMenu.style.display = 'none';
        });
        
        // Prevenir fechamento quando clicar no menu
        userMenu.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    }
});
