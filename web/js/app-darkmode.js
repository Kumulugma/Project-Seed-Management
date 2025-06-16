/**
 * SYSTEM NASION - DARK MODE I LOADING ANIMATIONS
 * Plik: web/js/app-darkmode.js
 */

'use strict';

// ===============================================
// DARK MODE FUNCTIONALITY
// ===============================================

/**
 * Przełącz tryb ciemny/jasny
 */
function toggleDarkMode() {
    const body = document.body;
    const icon = document.getElementById('dark-mode-icon');
    const isDark = body.classList.contains('dark-mode');
    
    // Natychmiastowe przełączanie
    if (isDark) {
        // Przełącz na light mode
        body.classList.remove('dark-mode');
        if (icon) {
            icon.classList.remove('bi-sun');
            icon.classList.add('bi-moon');
        }
        localStorage.setItem('darkMode', 'false');
        console.log('Przełączono na tryb jasny');
    } else {
        // Przełącz na dark mode
        body.classList.add('dark-mode');
        if (icon) {
            icon.classList.remove('bi-moon');
            icon.classList.add('bi-sun');
        }
        localStorage.setItem('darkMode', 'true');
        console.log('Przełączono na tryb ciemny');
    }
    
    // Dispatch custom event dla innych skryptów
    const event = new CustomEvent('darkModeToggled', {
        detail: { isDarkMode: !isDark }
    });
    document.dispatchEvent(event);
}

/**
 * Inicjalizacja dark mode przy ładowaniu strony
 */
function initializeDarkMode() {
    const savedMode = localStorage.getItem('darkMode');
    const icon = document.getElementById('dark-mode-icon');
    
    console.log('Inicjalizacja dark mode, zapisany tryb:', savedMode);
    
    // Zastosuj zapisany tryb bez animacji
    if (savedMode === 'true') {
        document.body.classList.add('dark-mode');
        if (icon) {
            icon.classList.remove('bi-moon');
            icon.classList.add('bi-sun');
        }
        console.log('Załadowano tryb ciemny');
    } else {
        // Upewnij się że body nie ma klasy dark-mode
        document.body.classList.remove('dark-mode');
        if (icon) {
            icon.classList.remove('bi-sun');
            icon.classList.add('bi-moon');
        }
        console.log('Załadowano tryb jasny');
    }
}

/**
 * Sprawdź preferencje systemowe użytkownika
 */
function checkSystemPreferences() {
    // Sprawdź czy użytkownik ma ustawiony dark mode w systemie
    if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
        // Tylko jeśli użytkownik nie ustawił własnej preferencji
        if (!localStorage.getItem('darkMode')) {
            localStorage.setItem('darkMode', 'true');
            initializeDarkMode();
        }
    }
    
    // Nasłuchuj zmian preferencji systemowych
    if (window.matchMedia) {
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', function(e) {
            // Tylko jeśli użytkownik nie ma własnej preferencji
            if (!localStorage.getItem('darkMode')) {
                if (e.matches) {
                    document.body.classList.add('dark-mode');
                } else {
                    document.body.classList.remove('dark-mode');
                }
            }
        });
    }
}

// ===============================================
// LOADING ANIMATIONS
// ===============================================

/**
 * Dodaj loading effect do elementu
 */
function showLoading(element, options = {}) {
    if (typeof element === 'string') {
        element = document.querySelector(element);
    }
    
    if (!element) return;
    
    const { 
        duration = 0, 
        bigLoader = false, 
        message = 'Ładowanie...' 
    } = options;
    
    element.classList.add('loading');
    if (bigLoader) {
        element.classList.add('big-loader');
    }
    
    // Dodaj aria-label dla screen readers
    element.setAttribute('aria-label', message);
    element.setAttribute('aria-busy', 'true');
    
    console.log('Pokazano loading dla:', element.tagName, element.className);
    
    // Automatyczne ukrycie po określonym czasie
    if (duration > 0) {
        setTimeout(() => {
            hideLoading(element);
        }, duration);
    }
    
    return element;
}

/**
 * Usuń loading effect z elementu
 */
function hideLoading(element) {
    if (typeof element === 'string') {
        element = document.querySelector(element);
    }
    
    if (!element) return;
    
    element.classList.remove('loading', 'big-loader');
    element.removeAttribute('aria-label');
    element.removeAttribute('aria-busy');
    
    console.log('Ukryto loading dla:', element.tagName, element.className);
    
    return element;
}

/**
 * Loading dla całej strony
 */
function showPageLoading() {
    document.body.classList.add('page-loading');
    console.log('Pokazano page loading');
}

function hidePageLoading() {
    document.body.classList.remove('page-loading');
    console.log('Ukryto page loading');
}

/**
 * Inicjalizacja loading effects dla różnych elementów
 */
function initializeLoadingEffects() {
    console.log('Inicjalizacja loading effects...');
    
    // Loading dla linków nawigacyjnych
    document.querySelectorAll('a').forEach(link => {
        // Pomiń dropdown toggles, anchors i javascript links
        if (shouldAddLoadingToLink(link)) {
            link.addEventListener('click', function(e) {
                // Nie dodawaj loading jeśli link otwiera w nowym oknie
                if (this.target === '_blank') return;
                
                console.log('Loading dla linku:', this.href);
                showLoading(this, { duration: 3000 });
            });
        }
    });
    
    // Loading dla formularzy
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', function(e) {
            console.log('Loading dla formularza');
            
            // Znajdź submit button
            const submitBtn = this.querySelector('button[type="submit"], input[type="submit"]');
            if (submitBtn) {
                showLoading(submitBtn, { 
                    bigLoader: true, 
                    message: 'Wysyłanie...' 
                });
                
                // Zablokuj przycisk
                submitBtn.disabled = true;
                const originalText = submitBtn.textContent || submitBtn.value;
                
                if (submitBtn.tagName === 'BUTTON') {
                    submitBtn.textContent = 'Wysyłanie...';
                } else {
                    submitBtn.value = 'Wysyłanie...';
                }
                
                // Przywróć po 10 sekundach (safety)
                setTimeout(() => {
                    hideLoading(submitBtn);
                    submitBtn.disabled = false;
                    if (submitBtn.tagName === 'BUTTON') {
                        submitBtn.textContent = originalText;
                    } else {
                        submitBtn.value = originalText;
                    }
                }, 10000);
            }
        });
    });
    
    // Loading dla przycisków akcji
    document.querySelectorAll('.btn:not(.dark-mode-toggle):not([data-bs-toggle])').forEach(btn => {
        btn.addEventListener('click', function(e) {
            // Pomiń jeśli to dropdown toggle lub modal
            if (this.hasAttribute('data-bs-toggle') || 
                this.classList.contains('dropdown-toggle') ||
                this.classList.contains('dark-mode-toggle')) {
                return;
            }
            
            // Pomiń jeśli to submit button (obsługiwany przez formularz)
            if (this.type === 'submit') return;
            
            console.log('Loading dla przycisku');
            showLoading(this, { duration: 2000 });
        });
    });
}

/**
 * Sprawdź czy link powinien mieć loading effect
 */
function shouldAddLoadingToLink(link) {
    // Pomiń jeśli:
    return !(
        link.hasAttribute('data-bs-toggle') ||           // Bootstrap toggle
        link.classList.contains('dropdown-toggle') ||    // Dropdown toggle
        link.classList.contains('dark-mode-toggle') ||   // Dark mode toggle
        link.href.startsWith('#') ||                     // Anchor links
        link.href.startsWith('javascript:') ||           // JavaScript links
        link.href.startsWith('mailto:') ||               // Email links
        link.href.startsWith('tel:') ||                  // Phone links
        link.href === '' ||                              // Empty href
        link.href === window.location.href ||           // Same page
        link.download ||                                 // Download links
        link.target === '_blank'                         // External links
    );
}

// ===============================================
// AJAX LOADING (jQuery i Fetch)
// ===============================================

/**
 * Inicjalizacja AJAX loading
 */
function initializeAjaxLoading() {
    let ajaxCounter = 0;
    
    // jQuery AJAX support
    if (typeof $ !== 'undefined') {
        $(document).ajaxStart(function() {
            ajaxCounter++;
            showPageLoading();
            console.log('AJAX start - counter:', ajaxCounter);
        });
        
        $(document).ajaxComplete(function() {
            ajaxCounter--;
            if (ajaxCounter <= 0) {
                ajaxCounter = 0;
                hidePageLoading();
                console.log('AJAX complete - counter:', ajaxCounter);
            }
        });
        
        $(document).ajaxError(function() {
            ajaxCounter--;
            if (ajaxCounter <= 0) {
                ajaxCounter = 0;
                hidePageLoading();
                console.log('AJAX error - counter:', ajaxCounter);
            }
        });
    }
    
    // Fetch API interceptor
    if (window.fetch) {
        const originalFetch = window.fetch;
        window.fetch = function(...args) {
            ajaxCounter++;
            showPageLoading();
            console.log('Fetch start - counter:', ajaxCounter);
            
            return originalFetch.apply(this, args)
                .then(response => {
                    ajaxCounter--;
                    if (ajaxCounter <= 0) {
                        ajaxCounter = 0;
                        hidePageLoading();
                        console.log('Fetch complete - counter:', ajaxCounter);
                    }
                    return response;
                })
                .catch(error => {
                    ajaxCounter--;
                    if (ajaxCounter <= 0) {
                        ajaxCounter = 0;
                        hidePageLoading();
                        console.log('Fetch error - counter:', ajaxCounter);
                    }
                    throw error;
                });
        };
    }
}

// ===============================================
// KEYBOARD SHORTCUTS
// ===============================================

/**
 * Inicjalizacja skrótów klawiszowych
 */
function initializeKeyboardShortcuts() {
    document.addEventListener('keydown', function(e) {
        // Ctrl/Cmd + Shift + D = toggle dark mode
        if ((e.ctrlKey || e.metaKey) && e.shiftKey && e.key === 'D') {
            e.preventDefault();
            toggleDarkMode();
            
            // Pokaż toast notification
            showToast('Tryb ciemny przełączony', 'info');
        }
    });
}

// ===============================================
// TOAST NOTIFICATIONS
// ===============================================

/**
 * Pokaż toast notification
 */
function showToast(message, type = 'info', duration = 3000) {
    // Usuń poprzednie toast-y
    document.querySelectorAll('.app-toast').forEach(toast => toast.remove());
    
    // Utwórz nowy toast
    const toast = document.createElement('div');
    toast.className = `app-toast toast-${type}`;
    toast.textContent = message;
    
    // Style
    toast.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 10001;
        padding: 12px 20px;
        border-radius: 6px;
        color: white;
        font-weight: 500;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        transform: translateX(100%);
        transition: transform 0.3s ease;
        max-width: 300px;
        word-wrap: break-word;
    `;
    
    // Kolory według typu
    const colors = {
        success: '#198754',
        error: '#dc3545',
        warning: '#ffc107',
        info: '#0dcaf0'
    };
    toast.style.backgroundColor = colors[type] || colors.info;
    
    document.body.appendChild(toast);
    
    // Animacja wejścia
    setTimeout(() => {
        toast.style.transform = 'translateX(0)';
    }, 10);
    
    // Automatyczne ukrycie
    setTimeout(() => {
        toast.style.transform = 'translateX(100%)';
        setTimeout(() => {
            if (toast.parentNode) {
                toast.parentNode.removeChild(toast);
            }
        }, 300);
    }, duration);
}

// ===============================================
// DEBUG I DEVELOPMENT
// ===============================================

/**
 * Dodaj przycisk demo (tylko localhost)
 */
function addDemoButton() {
    if (window.location.hostname === 'localhost' || 
        window.location.hostname === '127.0.0.1') {
        
        console.log('Tryb deweloperski - dodano przyciski demo');
        
        // Demo loading button
        const demoBtn = document.createElement('button');
        demoBtn.innerHTML = '🔄 Test Loading';
        demoBtn.className = 'btn btn-warning btn-sm position-fixed';
        demoBtn.style.cssText = `
            bottom: 20px; 
            right: 20px; 
            z-index: 1000;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        `;
        demoBtn.onclick = function() {
            console.log('Demo loading...');
            showLoading(this, { duration: 2000, bigLoader: true });
        };
        document.body.appendChild(demoBtn);
        
        // Demo page loading button
        const pageBtn = document.createElement('button');
        pageBtn.innerHTML = '📄 Page Loading';
        pageBtn.className = 'btn btn-info btn-sm position-fixed';
        pageBtn.style.cssText = `
            bottom: 70px; 
            right: 20px; 
            z-index: 1000;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        `;
        pageBtn.onclick = function() {
            showPageLoading();
            setTimeout(hidePageLoading, 2000);
        };
        document.body.appendChild(pageBtn);
        
        // Demo toast button
        const toastBtn = document.createElement('button');
        toastBtn.innerHTML = '💬 Toast';
        toastBtn.className = 'btn btn-secondary btn-sm position-fixed';
        toastBtn.style.cssText = `
            bottom: 120px; 
            right: 20px; 
            z-index: 1000;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        `;
        toastBtn.onclick = function() {
            const messages = [
                { text: 'Operacja zakończona sukcesem!', type: 'success' },
                { text: 'Wystąpił błąd!', type: 'error' },
                { text: 'Ostrzeżenie!', type: 'warning' },
                { text: 'Informacja', type: 'info' }
            ];
            const random = messages[Math.floor(Math.random() * messages.length)];
            showToast(random.text, random.type);
        };
        document.body.appendChild(toastBtn);
    }
}

/**
 * Loguj informacje systemowe
 */
function logSystemInfo() {
    console.group('🌱 System Nasion - Dark Mode & Loading');
    console.log('💡 Dark mode status:', localStorage.getItem('darkMode') || 'nie ustawiony');
    console.log('📱 Viewport:', window.innerWidth + 'x' + window.innerHeight);
    console.log('🎨 System theme:', window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
    console.log('⚡ JavaScript loaded:', new Date().toLocaleTimeString());
    console.groupEnd();
}

// ===============================================
// INICJALIZACJA GŁÓWNA
// ===============================================

/**
 * Główna funkcja inicjalizująca
 */
function initializeApp() {
    console.log('🚀 Inicjalizacja aplikacji...');
    
    // Sprawdź preferencje systemowe
    checkSystemPreferences();
    
    // Inicjalizuj dark mode
    initializeDarkMode();
    
    // Inicjalizuj loading effects
    initializeLoadingEffects();
    
    // Inicjalizuj AJAX loading
    initializeAjaxLoading();
    
    // Inicjalizuj skróty klawiszowe
    initializeKeyboardShortcuts();
    
    // Dodaj demo buttons (tylko development)
    addDemoButton();
    
    // Loguj informacje systemowe
    logSystemInfo();
    
    // Sprawdź czy dark mode button istnieje
    setTimeout(() => {
        const darkModeBtn = document.getElementById('dark-mode-icon');
        if (darkModeBtn) {
            console.log('✅ Dark mode button znaleziony');
        } else {
            console.warn('❌ Dark mode button nie znaleziony! Sprawdź czy jest w navbar.');
        }
    }, 1000);
    
    console.log('✅ Aplikacja zainicjalizowana');
}

// ===============================================
// EVENT LISTENERS
// ===============================================

// Inicjalizacja po załadowaniu DOM
document.addEventListener('DOMContentLoaded', initializeApp);

// Loading przy nawigacji
window.addEventListener('beforeunload', function() {
    showPageLoading();
});

window.addEventListener('load', function() {
    hidePageLoading();
});

// ===============================================
// GLOBALNE API
// ===============================================

// Udostępnij funkcje globalnie dla innych skryptów
window.SeedSystemDarkMode = {
    toggle: toggleDarkMode,
    init: initializeDarkMode,
    showLoading: showLoading,
    hideLoading: hideLoading,
    showPageLoading: showPageLoading,
    hidePageLoading: hidePageLoading,
    showToast: showToast
};

// Event dla innych skryptów
document.addEventListener('darkModeToggled', function(e) {
    console.log('Dark mode toggled:', e.detail.isDarkMode ? 'ON' : 'OFF');
});