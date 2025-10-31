/**
 * Gestion des notifications toast
 */
class ToastManager {
    static getContainer() {
        let container = document.querySelector('.toast-container');
        if (!container) {
            container = document.createElement('div');
            container.className = 'toast-container position-fixed bottom-0 end-0 p-3';
            document.body.appendChild(container);
        }
        return container;
    }

    static show(type = 'info', message = '') {
        const typeMap = {
            success: 'text-bg-success',
            danger: 'text-bg-danger',
            error: 'text-bg-danger',
            warning: 'text-bg-warning',
            info: 'text-bg-info'
        };

        const container = this.getContainer();
        const toast = document.createElement('div');
        toast.className = `toast ${typeMap[type] || typeMap.info} border-0`;
        toast.setAttribute('role', 'alert');
        toast.setAttribute('aria-live', 'assertive');
        toast.setAttribute('aria-atomic', 'true');
        toast.dataset.bsDelay = '4000';
        
        toast.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">${message}</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" 
                        data-bs-dismiss="toast" aria-label="Fermer"></button>
            </div>`;

        container.appendChild(toast);

        // Utiliser Bootstrap Toast si disponible, sinon fallback simple
        if (window.bootstrap?.Toast) {
            new bootstrap.Toast(toast).show();
        } else {
            toast.classList.add('show');
            toast.style.display = 'block';
            setTimeout(() => toast.remove(), 4000);
        }
    }
}

/**
 * Gestion des formulaires d'enchères
 */
class BidFormManager {
    static init() {
        const bidForms = document.querySelectorAll('form[action*="/bid"][method="post"]');
        bidForms.forEach(form => new this(form));
    }

    constructor(form) {
        this.form = form;
        this.submitBtn = form.querySelector('button[type="submit"]');
        this.originalBtnText = this.submitBtn?.textContent || 'Envoyer';
        this.init();
    }

    init() {
        this.form.addEventListener('submit', this.handleSubmit.bind(this));
    }

    async handleSubmit(event) {
        event.preventDefault();
        
        try {
            this.setLoading(true);
            
            const formData = new FormData(this.form);
            const response = await fetch(this.form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const result = await response.json();
            
            if (response.ok) {
                this.handleSuccess(result);
            } else {
                this.handleError(result);
            }
        } catch (error) {
            console.error('Erreur lors de l\'envoi de l\'enchère :', error);
            ToastManager.show('error', 'Une erreur est survenue lors de l\'envoi de votre enchère.');
        } finally {
            this.setLoading(false);
        }
    }

    setLoading(isLoading) {
        if (this.submitBtn) {
            this.submitBtn.disabled = isLoading;
            this.submitBtn.textContent = isLoading ? 'Envoi en cours...' : this.originalBtnText;
        }
    }

    handleSuccess(data) {
        ToastManager.show('success', data.message || 'Votre enchère a été enregistrée avec succès !');
        
        // Mise à jour des éléments de prix
        const priceElements = [
            document.querySelector('.current-price'),
            document.querySelector('[data-role="current-price"]')
        ];
        
        const nextElements = [
            document.querySelector('[data-role="next-min"]')
        ];

        // Mettre à jour les prix actuels
        priceElements.forEach(el => {
            if (el && (data.newPrice || data.price)) {
                const price = data.newPrice || data.price;
                el.textContent = el.hasAttribute('data-role') ? `${price} €` : price;
            }
        });

        // Mettre à jour les prochains prix minimums
        nextElements.forEach(el => {
            if (el && data.nextMin) {
                el.textContent = `${data.nextMin} €`;
            }
        });

        // Si aucun élément de prix n'est trouvé, recharger la page
        if (!priceElements.some(el => el)) {
            window.location.reload();
        }
    }

    handleError(error) {
        const message = error.message || 'Une erreur est survenue lors de l\'envoi de votre enchère.';
        ToastManager.show('error', message);
    }
}

// Initialisation des gestionnaires au chargement du DOM
document.addEventListener('DOMContentLoaded', () => {
    // Initialiser la gestion des formulaires d'enchères
    BidFormManager.init();

    // Initialisation des tooltips Bootstrap
    if (window.bootstrap?.Tooltip) {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.forEach(tooltipTriggerEl => {
            new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }

    // Initialisation des popovers Bootstrap
    if (window.bootstrap?.Popover) {
        const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
        popoverTriggerList.forEach(popoverTriggerEl => {
            new bootstrap.Popover(popoverTriggerEl);
        });
    }
});
