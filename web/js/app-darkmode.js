/**
 * LOKALIZACJA: web/js/app-darkmode.js
 * SYSTEM NASION - DARK MODE I LOADING ANIMATIONS (POPRAWIONY)
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
        link.target === '_blank' ||                      // External links
        link.href.includes('/export') ||                // CSV export links
        link.href.includes('download') ||               // Download links
        link.href.includes('.pdf') ||                   // PDF links
        link.href.includes('.csv')                      // CSV links
    );
}

/**
 * Sprawdź czy formularz powinien mieć loading effect
 */
function shouldAddLoadingToForm(form) {
    // Pomiń loading dla:
    return !(
        form.action.includes('/export') ||               // CSV export
        form.method.toLowerCase() === 'get' ||           // GET forms (search, filters)
        form.hasAttribute('data-no-loading') ||          // Explicit no-loading
        form.querySelector('input[type="file"]') ||      // File upload forms
        form.action.includes('download')                 // Download forms
    );
}

/**
 * Inicjalizacja loading effects dla różnych elementów
 */
function initializeLoadingEffects() {
    console.log('Inicjalizacja loading effects...');
    
    // Loading dla linków nawigacyjnych
    document.querySelectorAll('a').forEach(link => {
        if (shouldAddLoadingToLink(link)) {
            link.addEventListener('click', function(e) {
                // Nie dodawaj loading jeśli link otwiera w nowym oknie
                if (this.target === '_blank') return;
                
                console.log('Loading dla linku:', this.href);
                showLoading(this, { duration: 500 });
            });
        }
    });
    
    // Loading dla formularzy (z wykluczeniem eksportów)
    document.querySelectorAll('form').forEach(form => {
        if (shouldAddLoadingToForm(form)) {
            form.addEventListener('submit', function(e) {
                console.log('Loading dla formularza:', this.action);
                
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
        }
    });
    
    // Loading dla przycisków akcji (z wykluczeniem niektórych)
    document.querySelectorAll('.btn:not(.dark-mode-toggle):not([data-bs-toggle])').forEach(btn => {
        btn.addEventListener('click', function(e) {
            // Pomiń jeśli to dropdown toggle, modal, lub download
            if (this.hasAttribute('data-bs-toggle') || 
                this.classList.contains('dropdown-toggle') ||
                this.classList.contains('dark-mode-toggle') ||
                this.href && (this.href.includes('/export') || 
                             this.href.includes('download') ||
                             this.href.includes('.csv') ||
                             this.href.includes('.pdf'))) {
                return;
            }
            
            // Pomiń jeśli to submit button (obsługiwany przez formularz)
            if (this.type === 'submit') return;
            
            console.log('Loading dla przycisku');
            showLoading(this, { duration: 500 });
        });
    });
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
    
    // Fetch API support
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
                    console.log('Fetch success - counter:', ajaxCounter);
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
        }
    });
}

// ===============================================
// TOAST NOTIFICATIONS
// ===============================================

/**
 * Pokaż toast notification
 */
function showToast(message, type = 'info', duration = 5000) {
    // Utwórz container jeśli nie istnieje
    let container = document.getElementById('toast-container');
    if (!container) {
        container = document.createElement('div');
        container.id = 'toast-container';
        container.className = 'position-fixed top-0 end-0 p-3';
        container.style.zIndex = '1050';
        document.body.appendChild(container);
    }
    
    // Utwórz toast
    const toast = document.createElement('div');
    toast.className = 'toast show';
    toast.setAttribute('role', 'alert');
    
    const typeColors = {
        success: 'bg-success',
        error: 'bg-danger', 
        warning: 'bg-warning',
        info: 'bg-info'
    };
    
    const typeIcons = {
        success: 'bi-check-circle',
        error: 'bi-exclamation-triangle',
        warning: 'bi-exclamation-triangle',
        info: 'bi-info-circle'
    };
    
    toast.innerHTML = `
        <div class="toast-header ${typeColors[type]} text-white">
            <i class="bi ${typeIcons[type]} me-2"></i>
            <strong class="me-auto">${type.charAt(0).toUpperCase() + type.slice(1)}</strong>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"></button>
        </div>
        <div class="toast-body">
            ${message}
        </div>
    `;
    
    container.appendChild(toast);
    
    // Auto remove
    setTimeout(() => {
        if (toast.parentNode) {
            toast.remove();
        }
    }, duration);
    
    // Manual close
    toast.querySelector('.btn-close').addEventListener('click', () => {
        toast.remove();
    });
}

// ===============================================
// DEMO FUNKCJE (TYLKO DEVELOPMENT)
// ===============================================

/**
 * Dodaj demo button dla testowania (tylko development)
 */
function addDemoButton() {
    if (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') {
        console.log('🛠️ Development mode - dodano demo funkcje');
        
        // Dodaj demo toast na Ctrl+Shift+T
        document.addEventListener('keydown', function(e) {
            if ((e.ctrlKey || e.metaKey) && e.shiftKey && e.key === 'T') {
                e.preventDefault();
                showToast('Demo toast notification!', 'success');
            }
        });
    }
}

// ===============================================
// SYSTEM INFO
// ===============================================

/**
 * Loguj informacje systemowe
 */
function logSystemInfo() {
    console.groupCollapsed('🌱 System Informations');
    console.log('App:', 'System Zarządzania Nasionami');
    console.log('Version:', '1.0.0');
    console.log('Dark Mode:', localStorage.getItem('darkMode') === 'true' ? 'ON' : 'OFF');
    console.log('Screen:', window.screen.width + 'x' + window.screen.height);
    console.log('Viewport:', window.innerWidth + 'x' + window.innerHeight);
    console.log('User Agent:', navigator.userAgent);
    console.log('Color Scheme:', window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
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

// Loading przy nawigacji (ale nie dla downloads)
window.addEventListener('beforeunload', function() {
    // Sprawdź czy to nie jest download
    if (!window.location.href.includes('/export') && 
        !window.location.href.includes('download')) {
        showPageLoading();
    }
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