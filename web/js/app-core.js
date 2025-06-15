// Core Application JavaScript
class SeedApp {
    constructor() {
        this.init();
    }

    init() {
        this.initBootstrap();
        this.initAlerts();
        this.initSearch();
        this.initForms();
        this.bindGlobalEvents();
    }

    // Initialize Bootstrap components
    initBootstrap() {
        // Initialize tooltips
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        // Initialize popovers
        const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
        popoverTriggerList.map(function (popoverTriggerEl) {
            return new bootstrap.Popover(popoverTriggerEl);
        });
    }

    // Auto-hide success alerts
    initAlerts() {
        setTimeout(() => {
            const successAlerts = document.querySelectorAll('.alert-success');
            successAlerts.forEach(alert => {
                if (bootstrap.Alert) {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }
            });
        }, 5000);
    }

    // Initialize search functionality
    initSearch() {
        const searchInput = document.getElementById('seed-search');
        const searchResults = document.getElementById('search-results');
        
        if (searchInput && searchResults) {
            let searchTimeout;
            
            searchInput.addEventListener('input', (e) => {
                const query = e.target.value;
                
                clearTimeout(searchTimeout);
                
                if (query.length < 2) {
                    searchResults.innerHTML = '';
                    return;
                }
                
                searchTimeout = setTimeout(() => {
                    this.performSearch(query, searchResults);
                }, 300);
            });

            // Close search results when clicking outside
            document.addEventListener('click', (e) => {
                if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
                    searchResults.innerHTML = '';
                }
            });
        }
    }

    // Perform AJAX search
    async performSearch(query, resultsContainer) {
        try {
            const response = await fetch(`/seed/search?q=${encodeURIComponent(query)}`);
            const data = await response.json();
            
            this.renderSearchResults(data.results, resultsContainer);
        } catch (error) {
            console.error('Search error:', error);
            resultsContainer.innerHTML = '<div class="alert alert-danger m-2">Błąd podczas wyszukiwania</div>';
        }
    }

    // Render search results
    renderSearchResults(results, container) {
        if (results.length === 0) {
            container.innerHTML = '<div class="search-item text-muted">Brak wyników</div>';
            return;
        }

        const html = results.map(item => `
            <div class="search-item" onclick="window.location.href='${item.url}'">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="fw-bold">${this.escapeHtml(item.name)}</div>
                        <div class="small text-muted">${item.sowing_period}</div>
                    </div>
                    <div class="d-flex gap-1">
                        <span class="badge bg-${this.getTypeBadgeColor(item.type)}">${item.type}</span>
                        <span class="priority-badge priority-${this.getPriorityClass(item.priority)}">${item.priority}</span>
                    </div>
                </div>
            </div>
        `).join('');
        
        container.innerHTML = html;
    }

    // Initialize form handling
    initForms() {
        // AJAX forms
        const ajaxForms = document.querySelectorAll('form[data-ajax="true"]');
        ajaxForms.forEach(form => {
            form.addEventListener('submit', (e) => {
                e.preventDefault();
                this.submitForm(form);
            });
        });

        // Form validation
        const validationForms = document.querySelectorAll('.needs-validation');
        Array.from(validationForms).forEach(form => {
            form.addEventListener('submit', event => {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });
    }

    // Submit form via AJAX
    async submitForm(form) {
        const formData = new FormData(form);
        const submitBtn = form.querySelector('button[type="submit"]');
        
        if (submitBtn) {
            this.setButtonLoading(submitBtn, true);
        }

        try {
            const response = await fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (response.headers.get('content-type')?.includes('application/json')) {
                const result = await response.json();
                
                if (result.success) {
                    this.showAlert('success', result.message || 'Operacja wykonana pomyślnie');
                    if (result.redirect) {
                        setTimeout(() => {
                            window.location.href = result.redirect;
                        }, 1000);
                    }
                } else {
                    this.showAlert('danger', result.message || 'Wystąpił błąd');
                }
            } else {
                // Handle file download or redirect
                if (response.ok) {
                    const contentDisposition = response.headers.get('content-disposition');
                    if (contentDisposition && contentDisposition.includes('attachment')) {
                        // File download
                        const blob = await response.blob();
                        const url = window.URL.createObjectURL(blob);
                        const a = document.createElement('a');
                        a.href = url;
                        a.download = this.getFilenameFromDisposition(contentDisposition);
                        document.body.appendChild(a);
                        a.click();
                        document.body.removeChild(a);
                        window.URL.revokeObjectURL(url);
                        this.showAlert('success', 'Plik został wygenerowany');
                    } else {
                        // Redirect
                        window.location.reload();
                    }
                } else {
                    this.showAlert('danger', 'Wystąpił błąd serwera');
                }
            }
        } catch (error) {
            console.error('Form submission error:', error);
            this.showAlert('danger', 'Wystąpił błąd połączenia');
        } finally {
            if (submitBtn) {
                this.setButtonLoading(submitBtn, false);
            }
        }
    }

    // Set button loading state
    setButtonLoading(button, isLoading) {
        if (isLoading) {
            button.disabled = true;
            button.dataset.originalText = button.innerHTML;
            button.innerHTML = '<span class="spinner me-2"></span>Zapisywanie...';
        } else {
            button.disabled = false;
            button.innerHTML = button.dataset.originalText || 'Zapisz';
        }
    }

    // Show alert message
    showAlert(type, message) {
        const alertContainer = document.querySelector('.alert-container') || document.body;
        const alert = document.createElement('div');
        alert.className = `alert alert-${type} alert-dismissible fade show`;
        alert.innerHTML = `
            <i class="bi bi-${this.getAlertIcon(type)} me-2"></i>${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        alertContainer.insertBefore(alert, alertContainer.firstChild);
        
        // Auto-hide after 5 seconds
        setTimeout(() => {
            if (alert.parentNode) {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            }
        }, 5000);
    }

    // Bind global events
    bindGlobalEvents() {
        document.addEventListener('click', (e) => {
            // Confirm dialogs
            if (e.target.matches('[data-confirm]')) {
                if (!confirm(e.target.dataset.confirm)) {
                    e.preventDefault();
                    return false;
                }
            }

            // Select all checkboxes
            if (e.target.matches('.select-all')) {
                const target = e.target.dataset.target;
                const checkboxes = document.querySelectorAll(target);
                checkboxes.forEach(cb => cb.checked = e.target.checked);
            }
        });

        // Priority input changes
        document.addEventListener('change', (e) => {
            if (e.target.matches('.priority-input')) {
                this.updatePriorityBadge(e.target);
            }
        });

        // Handle navigation dropdown clicks
        document.addEventListener('click', (e) => {
            if (e.target.closest('.dropdown-item form')) {
                e.stopPropagation();
            }
        });
    }

    // Update priority badge
    updatePriorityBadge(input) {
        const value = parseInt(input.value) || 0;
        const badge = input.closest('.card-body')?.querySelector('.priority-badge') || 
                     input.nextElementSibling;
        
        if (badge && badge.classList.contains('priority-badge')) {
            badge.textContent = value;
            badge.className = 'priority-badge';
            badge.classList.add(`priority-${this.getPriorityClass(value)}`);
        }
    }

    // Utility methods
    getPriorityClass(priority) {
        if (priority >= 8) return 'high';
        if (priority >= 5) return 'medium';
        if (priority > 0) return 'low';
        return 'none';
    }

    getTypeBadgeColor(type) {
        const colors = {
            'vegetables': 'success',
            'flowers': 'primary',
            'herbs': 'info'
        };
        return colors[type] || 'secondary';
    }

    getAlertIcon(type) {
        const icons = {
            'success': 'check-circle',
            'danger': 'exclamation-triangle',
            'warning': 'exclamation-triangle',
            'info': 'info-circle'
        };
        return icons[type] || 'info-circle';
    }

    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    getFilenameFromDisposition(disposition) {
        const filenameRegex = /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/;
        const matches = filenameRegex.exec(disposition);
        if (matches != null && matches[1]) {
            return matches[1].replace(/['"]/g, '');
        }
        return 'download';
    }

    formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('pl-PL');
    }
}

// Initialize app when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    window.seedApp = new SeedApp();
});