// Gestion AJAX + navigation History API

function getToastContainer() {
    let cont = document.querySelector('.toast-container');
    if (!cont) {
        cont = document.createElement('div');
        cont.className = 'toast-container position-fixed bottom-0 end-0 p-3';
        document.body.appendChild(cont);
    }
    return cont;
}

function showToast(type = 'info', message = '') {
    const map = { success: 'text-bg-success', danger: 'text-bg-danger', error: 'text-bg-danger', warning: 'text-bg-warning', info: 'text-bg-info' };
    const cls = map[type] || map.info;
    const container = getToastContainer();
    const wrap = document.createElement('div');
    wrap.className = `toast ${cls} border-0`;
    wrap.setAttribute('role', 'alert');
    wrap.setAttribute('aria-live', 'assertive');
    wrap.setAttribute('aria-atomic', 'true');
    wrap.dataset.bsDelay = '4000';
    wrap.innerHTML = `<div class="d-flex"><div class="toast-body">${message}</div><button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button></div>`;
    container.appendChild(wrap);
    if (window.bootstrap && bootstrap.Toast) {
        new bootstrap.Toast(wrap).show();
    } else {
        // Fallback si Bootstrap JS n'est pas chargé: afficher quand même
        wrap.classList.add('show');
        wrap.style.display = 'block';
        setTimeout(() => {
            try { wrap.remove(); } catch(_) {}
        }, 4000);
    }
}

// Gestion des formulaires d'enchère (/lot/{id}/bid)
document.addEventListener('DOMContentLoaded', () => {
    const bidForms = document.querySelectorAll('form[action*="/bid"][method="post"]');

    bidForms.forEach((form) => {
        form.addEventListener('submit', async (e) => {
            e.preventDefault();

            const submitBtn = form.querySelector('button[type="submit"]');
            const restoreBtn = () => {
                if (submitBtn) {
                    submitBtn.disabled = false;
                    if (submitBtn.dataset && submitBtn.dataset.originalText) {
                        submitBtn.textContent = submitBtn.dataset.originalText;
                    }
                }
            };

            try {
                if (submitBtn) {
                    submitBtn.dataset.originalText = submitBtn.textContent || 'Envoyer';
                    submitBtn.textContent = 'Envoi…';
                    submitBtn.disabled = true;
                }

                const formData = new FormData(form);
                const res = await fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                let data = null;
                try {
                    data = await res.json();
                } catch (_) {
                    // Pas de JSON valide
                }

                if (!res.ok || !data || data.ok === false) {
                    const msg = (data && data.error) ? data.error : `Erreur ${res.status}`;
                    showToast('danger', `Échec de l'enchère: ${msg}`);
                    restoreBtn();
                    return;
                }

                // Mise à jour optimiste si des hooks existent, sinon on recharge
                const priceEl = document.querySelector('[data-role="current-price"]');
                const nextEl = document.querySelector('[data-role="next-min"]');

                if (priceEl && data.newPrice) {
                    priceEl.textContent = `${data.newPrice} €`;
                }
                if (nextEl && data.nextMin) {
                    nextEl.textContent = `${data.nextMin} €`;
                }

                if (!priceEl) {
                    // Pas d'élément dédié pour MAJ dynamique: recharger la page
                    window.location.reload();
                    return;
                }

                showToast('success', 'Enchère déposée avec succès.');
                restoreBtn();
            } catch (err) {
                showToast('danger', 'Erreur réseau ou serveur. Veuillez réessayer.');
                restoreBtn();
            }
        });
    });
});
