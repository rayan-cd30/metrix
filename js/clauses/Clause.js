export default class Clause {
  sendInsert(endpoint, data, btn) {
    const formData = new FormData();
    for (const [k, v] of Object.entries(data)) formData.append(k, v);

    const old = btn.textContent;
    const loadingText = btn.dataset.loadingText || 'En attente...';
    const successText = btn.dataset.successText || 'Valide';

    btn.textContent = loadingText;
    btn.disabled = true;

    return fetch(endpoint, { method: 'POST', body: formData })
      .then(r => r.json())
      .then(result => {
        if (result.success) {
          btn.textContent = successText;
          btn.style.color = '#16a34a';

          if (btn.dataset.resetForm === 'true') {
            document.querySelector(btn.dataset.form || '')?.reset();
          }

          setTimeout(() => {
            btn.style.color = '';
            btn.textContent = old;
            btn.disabled = false;
          }, 900);

          return result;
        }

        alert('Erreur: ' + result.message);
        btn.textContent = old;
        btn.disabled = false;
      })
      .catch(err => {
        alert('Erreur reseau: ' + err);
        btn.textContent = old;
        btn.disabled = false;
      });
  }
}
