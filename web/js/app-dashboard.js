// Dashboard-specific JavaScript
class DashboardApp {
    constructor() {
        this.init();
    }

    init() {
        this.initLabelSelection();
        this.initStatsUpdates();
        this.bindDashboardEvents();
    }

    // Initialize label selection functionality
    initLabelSelection() {
        const labelCheckboxes = document.querySelectorAll('input[name="labels[]"]');
        const printLabelsBtn = document.getElementById('print-labels-btn');
        const selectedLabelsContainer = document.getElementById('selected-labels');

        if (labelCheckboxes.length > 0 && selectedLabelsContainer) {
            labelCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', () => {
                    this.updateSelectedLabels();
                });
            });
        }

        if (printLabelsBtn) {
            printLabelsBtn.addEventListener('click', (e) => {
                e.preventDefault();
                this.handleLabelPrint();
            });
        }
    }

    // Update selected labels for printing
    updateSelectedLabels() {
        const selectedLabelsContainer = document.getElementById('selected-labels');
        if (!selectedLabelsContainer) return;

        const selected = Array.from(document.querySelectorAll('input[name="labels[]"]:checked'))
            .map(checkbox => `<input type="hidden" name="seeds[]" value="${checkbox.value}">`)
            .join('');
        
        selectedLabelsContainer.innerHTML = selected;
    }

    // Handle label printing
    handleLabelPrint() {
        const selectedCheckboxes = document.querySelectorAll('input[name="labels[]"]:checked');
        
        if (selectedCheckboxes.length === 0) {
            this.showAlert('warning', 'Wybierz nasiona do wydruku etykiet');
            return;
        }

        this.updateSelectedLabels();
        
        const labelsForm = document.getElementById('labels-form');
        if (labelsForm) {
            labelsForm.submit();
        }
    }

    // Initialize real-time stats updates
    initStatsUpdates() {
        // Update stats every 30 seconds
        setInterval(() => {
            this.updateDashboardStats();
        }, 30000);
    }

    // Update dashboard statistics
    async updateDashboardStats() {
        try {
            const response = await fetch('/dashboard/get-stats');
            const data = await response.json();
            
            if (data.success) {
                this.updateStatsDisplay(data.stats);
            }
        } catch (error) {
            console.error('Error updating stats:', error);
        }
    }

    // Update stats display
    updateStatsDisplay(stats) {
        Object.keys(stats).forEach(key => {
            const element = document.querySelector(`[data-stat="${key}"]`);
            if (element && element.textContent !== stats[key].toString()) {
                this.animateCounterUpdate(element, parseInt(element.textContent) || 0, stats[key]);
            }
        });
    }

    // Animate counter updates
    animateCounterUpdate(element, fromValue, toValue) {
        const duration = 1000;
        const steps = 20;
        const stepValue = (toValue - fromValue) / steps;
        const stepDuration = duration / steps;
        
        let currentStep = 0;
        
        const timer = setInterval(() => {
            currentStep++;
            const currentValue = Math.round(fromValue + (stepValue * currentStep));
            element.textContent = currentValue;
            
            if (currentStep >= steps) {
                clearInterval(timer);
                element.textContent = toValue;
            }
        }, stepDuration);
    }

    // Bind dashboard-specific events
    bindDashboardEvents() {
        // Handle seed selection for PDF generation
        const seedForm = document.querySelector('form[action*="sowing-pdf"]');
        if (seedForm) {
            seedForm.addEventListener('submit', (e) => {
                const selectedSeeds = seedForm.querySelectorAll('input[name="seeds[]"]:checked');
                if (selectedSeeds.length === 0) {
                    e.preventDefault();
                    this.showAlert('warning', 'Wybierz nasiona do wysiewu');
                    return;
                }
                
                // Show loading state
                const submitBtn = seedForm.querySelector('button[type="submit"]');
                if (submitBtn) {
                    this.setButtonLoading(submitBtn, true);
                }
            });
        }

        // Handle germination status updates
        const germinationForm = document.querySelector('form[action*="update-germination"]');
        if (germinationForm) {
            germinationForm.addEventListener('submit', (e) => {
                e.preventDefault();
                this.handleGerminationUpdate(germinationForm);
            });
        }

        // Auto-refresh functionality
        const refreshBtn = document.querySelector('[onclick="location.reload()"]');
        if (refreshBtn) {
            refreshBtn.addEventListener('click', (e) => {
                e.preventDefault();
                this.refreshDashboard();
            });
        }
    }

    // Handle germination status updates
    async handleGerminationUpdate(form) {
        const formData = new FormData(form);
        const submitBtn = form.querySelector('button[type="submit"]');
        
        // Check if any changes were made
        const selects = form.querySelectorAll('select[name^="germination"]');
        let hasChanges = false;
        
        selects.forEach(select => {
            if (select.value !== select.defaultValue) {
                hasChanges = true;
            }
        });
        
        if (!hasChanges) {
            this.showAlert('info', 'Nie wprowadzono żadnych zmian');
            return;
        }

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

            const result = await response.json();
            
            if (result.success) {
                this.showAlert('success', result.message || 'Status kiełkowania został zaktualizowany');
                
                // Update default values to prevent re-submission
                selects.forEach(select => {
                    select.defaultValue = select.value;
                });
                
                // Update stats
                this.updateDashboardStats();
            } else {
                this.showAlert('danger', result.message || 'Wystąpił błąd podczas aktualizacji');
            }
        } catch (error) {
            console.error('Germination update error:', error);
            this.showAlert('danger', 'Wystąpił błąd połączenia');
        } finally {
            if (submitBtn) {
                this.setButtonLoading(submitBtn, false);
            }
        }
    }

    // Refresh dashboard data
    async refreshDashboard() {
        try {
            // Show loading indicator
            const refreshBtn = document.querySelector('[onclick="location.reload()"]');
            if (refreshBtn) {
                refreshBtn.innerHTML = '<i class="bi bi-arrow-clockwise me-1 rotating"></i>Odświeżanie...';
                refreshBtn.disabled = true;
            }

            // Load fresh data
            const response = await fetch('/dashboard/get-current-data');
            const data = await response.json();
            
            if (data.success) {
                this.updateCurrentSeeds(data.currentSeeds);
                this.updateSownSeeds(data.sownSeeds);
                this.updateStatsDisplay(data.stats);
                this.showAlert('success', 'Dashboard został odświeżony');
            } else {
                throw new Error(data.message || 'Błąd podczas odświeżania');
            }
        } catch (error) {
            console.error('Refresh error:', error);
            this.showAlert('danger', 'Wystąpił błąd podczas odświeżania');
        } finally {
            // Restore refresh button
            const refreshBtn = document.querySelector('[onclick="location.reload()"]');
            if (refreshBtn) {
                refreshBtn.innerHTML = '<i class="bi bi-arrow-clockwise me-1"></i>Odśwież';
                refreshBtn.disabled = false;
            }
        }
    }

    // Update current seeds table
    updateCurrentSeeds(seeds) {
        const container = document.getElementById('current-seeds-table');
        if (!container) return;

        if (seeds.length === 0) {
            container.innerHTML = `
                <tr>
                    <td colspan="4" class="text-center">
                        <div class="alert alert-info mb-0">
                            <h6><i class="bi bi-info-circle me-2"></i>Brak nasion do wysiewu</h6>
                            <p class="mb-0">W obecnym okresie nie ma nasion zaplanowanych do wysiewu.</p>
                        </div>
                    </td>
                </tr>
            `;
            return;
        }

        const html = seeds.map(seed => `
            <tr>
                <td>
                    <input type="checkbox" name="seeds[]" value="${seed.id}" class="form-check-input seed-checkbox">
                </td>
                <td>
                    <div class="fw-bold">${this.escapeHtml(seed.name)}</div>
                    <small class="text-muted">${seed.sowing_period}</small>
                </td>
                <td>
                    <span class="badge bg-${this.getTypeBadgeColor(seed.type)}">${seed.type_label}</span>
                </td>
                <td>
                    <span class="priority-badge priority-${this.getPriorityClass(seed.priority)}">${seed.priority}</span>
                </td>
            </tr>
        `).join('');

        container.innerHTML = html;
    }

    // Update sown seeds table
    updateSownSeeds(seeds) {
        const container = document.getElementById('sown-seeds-table');
        if (!container) return;

        if (seeds.length === 0) {
            container.innerHTML = `
                <tr>
                    <td colspan="4" class="text-center">
                        <div class="alert alert-info mb-0">
                            <h6><i class="bi bi-info-circle me-2"></i>Brak nasion do sprawdzenia</h6>
                            <p class="mb-0">Nie ma nasion oczekujących na sprawdzenie kiełkowania.</p>
                        </div>
                    </td>
                </tr>
            `;
            return;
        }

        const html = seeds.map(seed => `
            <tr class="${seed.should_germinate ? 'table-warning' : ''}">
                <td>
                    <div class="fw-bold small">${this.escapeHtml(seed.seed.name)}</div>
                    <small class="text-muted">${seed.formatted_date} (${seed.days_from_sowing} dni)</small>
                </td>
                <td>
                    <code class="small">${this.escapeHtml(seed.sowing_code)}</code>
                </td>
                <td>
                    <select name="germination[${seed.id}]" class="form-select form-select-sm">
                        <option value="sown" ${seed.status === 'sown' ? 'selected' : ''}>Wysiany</option>
                        <option value="germinated" ${seed.status === 'germinated' ? 'selected' : ''}>Wykiełkował</option>
                        <option value="not_germinated" ${seed.status === 'not_germinated' ? 'selected' : ''}>Nie wykiełkował</option>
                    </select>
                </td>
                <td class="text-center">
                    <input type="checkbox" name="labels[]" value="${seed.id}" class="form-check-input">
                </td>
            </tr>
        `).join('');

        container.innerHTML = html;
    }

    // Utility methods (same as core app)
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

    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

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

    showAlert(type, message) {
        if (window.seedApp && window.seedApp.showAlert) {
            window.seedApp.showAlert(type, message);
        } else {
            alert(message);
        }
    }
}

// Initialize dashboard app when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    if (document.querySelector('.dashboard-index')) {
        window.dashboardApp = new DashboardApp();
    }
});

// CSS for rotating icon
const style = document.createElement('style');
style.textContent = `
    .rotating {
        animation: spin 1s linear infinite;
    }
    
    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
`;
document.head.appendChild(style);