// Handles AJAX status toggles for offers
import axios from 'axios';

function initOfferToggles() {
    const table = document.querySelector('.my-offers-table');
    if (!table) return;

    table.addEventListener('submit', async (e) => {
        const form = e.target;
        if (!(form instanceof HTMLFormElement)) return;
        if (!form.dataset.ajax) return;
        e.preventDefault();

        const url = form.action;
        const method = (form.querySelector('input[name="_method"]')?.value || form.method || 'POST').toUpperCase();
        const row = form.closest('tr');
        const actionCell = row?.querySelector('[data-actions-cell]');
        const statusCell = row?.querySelector('[data-status-cell]');

        const submitBtn = form.querySelector('button[type="submit"]');
        if (submitBtn) submitBtn.disabled = true;

        try {
            let response;

            // If form uses method spoofing (PATCH/PUT) Laravel expects a POST with _method
            if (method === 'PATCH' || method === 'PUT') {
                const formData = new FormData(form);
                // ensure _method is present
                if (!formData.has('_method')) {
                    formData.append('_method', method);
                }

                // send as urlencoded so Laravel reads _method
                const body = new URLSearchParams();
                for (const pair of formData.entries()) {
                    body.append(pair[0], pair[1]);
                }

                response = await axios.post(url, body.toString(), {
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'Accept': 'application/json',
                    },
                });
            } else {
                response = await axios({
                    url,
                    method,
                    headers: { 'Accept': 'application/json' },
                });
            }

            if (row) {
                const isSold = response?.data?.status === 'sold' || response?.data?.sold_at !== null;
                row.dataset.sold = isSold ? '1' : '0';

                if (statusCell) {
                    if (isSold) {
                        statusCell.innerHTML = '<span class="badge text-bg-secondary">Verkocht</span>';
                    } else {
                        statusCell.innerHTML = '<span class="badge text-bg-success">Te koop</span>';
                    }
                }

                if (actionCell) {
                    const toggleForm = actionCell.querySelector('form[data-ajax="true"]');
                    const toggleButton = toggleForm?.querySelector('button[type="submit"]');

                    if (toggleForm && toggleButton) {
                        if (isSold) {
                            toggleForm.action = toggleForm.action.replace('/sold', '/activate');
                            toggleButton.textContent = 'Activeren';
                            toggleButton.className = 'btn btn-sm btn-outline-success action-btn';
                        } else {
                            toggleForm.action = toggleForm.action.replace('/activate', '/sold');
                            toggleButton.textContent = 'Markeer verkocht';
                            toggleButton.className = 'btn btn-sm btn-outline-secondary action-btn';
                        }
                    }
                }
            }

            // Update stats counters if present
            const activeEl = document.getElementById('stats-active');
            const soldEl = document.getElementById('stats-sold');
            if (activeEl && soldEl) {
                if (response?.data?.status === 'sold') {
                        activeEl.textContent = Math.max(0, parseInt(activeEl.textContent || '0') - 1);
                        soldEl.textContent = (parseInt(soldEl.textContent || '0') + 1).toString();
                } else if (response?.data?.status === 'active') {
                        activeEl.textContent = (parseInt(activeEl.textContent || '0') + 1).toString();
                        soldEl.textContent = Math.max(0, parseInt(soldEl.textContent || '0') - 1);
                }
            }
        } catch (err) {
            console.error('Toggle failed', err);
            if (err?.response) {
                const status = err.response.status;
                const msg = err.response.data?.message || err.response.statusText || 'Onbekende fout';
                alert(`Actie kon niet worden uitgevoerd (HTTP ${status}): ${msg}`);
            } else {
                alert('Actie kon niet worden uitgevoerd. Vernieuw de pagina en probeer opnieuw.');
            }
        } finally {
            if (submitBtn) submitBtn.disabled = false;
        }
    });
}

document.addEventListener('DOMContentLoaded', initOfferToggles);
