/**
 * Enhanced Alert System for LAREA
 * Provides beautiful, customizable alerts with animations and optional sound
 */

class EnhancedAlerts {
    constructor(options = {}) {
        this.options = {
            autoClose: true,
            autoCloseDelay: 5000,
            soundEnabled: localStorage.getItem('alertSoundEnabled') !== 'false',
            position: 'top-right',
            maxAlerts: 5,
            ...options
        };
        
        this.alertContainer = null;
        this.alerts = [];
        this.sounds = {};
        
        this.init();
    }
    
    init() {
        this.createContainer();
        this.loadSounds();
        this.createSoundToggle();
        this.bindEvents();
    }
    
    createContainer() {
        this.alertContainer = document.createElement('div');
        this.alertContainer.className = 'alert-container';
        document.body.appendChild(this.alertContainer);
    }
    
    loadSounds() {
        // Load sound files for different alert types
        this.sounds = {
            success: this.createAudioElement('data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgodDbq2EcBj+a2/LDciUFLIHO8tiJNwgZaLvt559NEAxQp+PwtmMcBjiR1/LMeSwFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhCjWQzPLNeSsFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhCjWQzPLNeSsFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhCjWQzPLNeSsFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhCjWQzPLNeSsFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhCjWQzPLNeSsFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwAAAAA'),
            error: this.createAudioElement('data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgodDbq2EcBj+a2/LDciUFLIHO8tiJNwgZaLvt559NEAxQp+PwtmMcBjiR1/LMeSwFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhCjWJzPLNeSsFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhCjWJzPLNeSsFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhCjWJzPLNeSsFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhCjWJzPLNeSsFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhCjWJzPLNeSsFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwAAAAA'),
            warning: this.createAudioElement('data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgodDbq2EcBj+a2/LDciUFLIHO8tiJNwgZaLvt559NEAxQp+PwtmMcBjiR1/LMeSwFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhCjWQzPLNeSsFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhCjWQzPLNeSsFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhCjWQzPLNeSsFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhCjWQzPLNeSsFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhCjWQzPLNeSsFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwAAAAA'),
            info: this.createAudioElement('data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgodDbq2EcBj+a2/LDciUFLIHO8tiJNwgZaLvt559NEAxQp+PwtmMcBjiR1/LMeSwFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhCjWQzPLNeSsFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhCjWQzPLNeSsFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhCjWQzPLNeSsFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhCjWQzPLNeSsFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhCjWQzPLNeSsFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwAAAAA')
        };
    }
    
    createAudioElement(dataUri) {
        const audio = new Audio(dataUri);
        audio.volume = 0.3; // Set volume to 30%
        return audio;
    }
    
    createSoundToggle() {
        const soundToggle = document.createElement('button');
        soundToggle.className = 'alert-sound-toggle';
        soundToggle.innerHTML = this.options.soundEnabled ? '🔊' : '🔇';
        soundToggle.title = this.options.soundEnabled ? 'Disable sound notifications' : 'Enable sound notifications';
        
        if (!this.options.soundEnabled) {
            soundToggle.classList.add('muted');
        }
        
        soundToggle.addEventListener('click', () => {
            this.options.soundEnabled = !this.options.soundEnabled;
            localStorage.setItem('alertSoundEnabled', this.options.soundEnabled);
            
            soundToggle.innerHTML = this.options.soundEnabled ? '🔊' : '🔇';
            soundToggle.title = this.options.soundEnabled ? 'Disable sound notifications' : 'Enable sound notifications';
            soundToggle.classList.toggle('muted', !this.options.soundEnabled);
        });
        
        document.body.appendChild(soundToggle);
    }
    
    bindEvents() {
        // Close alerts when clicking outside
        document.addEventListener('click', (e) => {
            if (!e.target.closest('.alert') && !e.target.closest('.alert-sound-toggle')) {
                // Don't close alerts automatically on outside click
            }
        });
        
        // Keyboard shortcuts
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                this.closeAll();
            }
        });
    }
    
    show(type, message, title = '', options = {}) {
        const alertOptions = { ...this.options, ...options };
        
        // Remove oldest alert if we've reached the maximum
        if (this.alerts.length >= this.options.maxAlerts) {
            this.close(this.alerts[0].id);
        }
        
        const alertId = 'alert-' + Date.now() + '-' + Math.random().toString(36).substr(2, 9);
        
        const alertElement = this.createAlertElement(type, message, title, alertId, alertOptions);
        this.alertContainer.appendChild(alertElement);
        
        const alertData = {
            id: alertId,
            element: alertElement,
            type: type,
            autoClose: alertOptions.autoClose,
            autoCloseDelay: alertOptions.autoCloseDelay
        };
        
        this.alerts.push(alertData);
        
        // Play sound if enabled
        if (this.options.soundEnabled && this.sounds[type]) {
            this.sounds[type].play().catch(() => {
                // Ignore errors if sound can't be played
            });
        }
        
        // Show alert with animation
        setTimeout(() => {
            alertElement.classList.add('show');
        }, 10);
        
        // Auto close if enabled
        if (alertOptions.autoClose) {
            this.scheduleAutoClose(alertId, alertOptions.autoCloseDelay);
        }
        
        return alertId;
    }
    
    createAlertElement(type, message, title, alertId, options) {
        const alertElement = document.createElement('div');
        alertElement.className = `alert alert-${type}`;
        alertElement.id = alertId;
        
        const icon = this.getIcon(type);
        
        alertElement.innerHTML = `
            <div class="alert-content">
                <div class="alert-icon">${icon}</div>
                <div class="alert-text">
                    ${title ? `<div class="alert-title">${this.escapeHtml(title)}</div>` : ''}
                    <div class="alert-message">${this.escapeHtml(message)}</div>
                </div>
            </div>
            <button class="alert-close" aria-label="Close alert">&times;</button>
            ${options.autoClose ? `<div class="alert-progress" style="animation-duration: ${options.autoCloseDelay}ms;"></div>` : ''}
        `;
        
        // Add close button event
        const closeButton = alertElement.querySelector('.alert-close');
        closeButton.addEventListener('click', () => {
            this.close(alertId);
        });
        
        return alertElement;
    }
    
    getIcon(type) {
        const icons = {
            success: '✅',
            error: '❌',
            warning: '⚠️',
            info: 'ℹ️'
        };
        return icons[type] || icons.info;
    }
    
    scheduleAutoClose(alertId, delay) {
        setTimeout(() => {
            this.close(alertId);
        }, delay);
    }
    
    close(alertId) {
        const alertIndex = this.alerts.findIndex(alert => alert.id === alertId);
        if (alertIndex === -1) return;
        
        const alertData = this.alerts[alertIndex];
        const alertElement = alertData.element;
        
        alertElement.classList.add('hide');
        
        setTimeout(() => {
            if (alertElement.parentNode) {
                alertElement.parentNode.removeChild(alertElement);
            }
            this.alerts.splice(alertIndex, 1);
        }, 400);
    }
    
    closeAll() {
        this.alerts.forEach(alert => {
            this.close(alert.id);
        });
    }
    
    success(message, title = 'Success', options = {}) {
        return this.show('success', message, title, options);
    }
    
    error(message, title = 'Error', options = {}) {
        return this.show('error', message, title, options);
    }
    
    warning(message, title = 'Warning', options = {}) {
        return this.show('warning', message, title, options);
    }
    
    info(message, title = 'Information', options = {}) {
        return this.show('info', message, title, options);
    }
    
    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
}

// Global instance
let alerts;

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    alerts = new EnhancedAlerts();
    
    // Make it globally available
    window.showAlert = function(type, message, title, options) {
        return alerts.show(type, message, title, options);
    };
    
    window.showSuccess = function(message, title, options) {
        return alerts.success(message, title, options);
    };
    
    window.showError = function(message, title, options) {
        return alerts.error(message, title, options);
    };
    
    window.showWarning = function(message, title, options) {
        return alerts.warning(message, title, options);
    };
    
    window.showInfo = function(message, title, options) {
        return alerts.info(message, title, options);
    };
    
    window.closeAlert = function(alertId) {
        return alerts.close(alertId);
    };
    
    window.closeAllAlerts = function() {
        return alerts.closeAll();
    };
});

// Legacy support for existing alert functions
function closeAlert(alertId) {
    if (window.closeAlert) {
        window.closeAlert(alertId);
    }
}

// Export for module systems
if (typeof module !== 'undefined' && module.exports) {
    module.exports = EnhancedAlerts;
}