(() => {
    const base = document.body.dataset.base || '';
    const form = document.getElementById('searchForm');
    const results = document.getElementById('results');
    const resultsBody = document.getElementById('resultsBody');
    const extractBtn = document.getElementById('extractEmailsBtn');
    const downloadBtn = document.getElementById('downloadCsvBtn');
    const searchBtn = document.getElementById('searchBtn');
    const stats = document.getElementById('stats');
    const progress = document.getElementById('progress');
    const progressBar = document.getElementById('progressBar');
    const saveStatus = document.getElementById('saveStatus');

    form.addEventListener('submit', async (event) => {
        event.preventDefault();

        results.hidden = false;
        extractBtn.disabled = true;
        searchBtn.disabled = true;
        resultsBody.innerHTML = '<tr><td colspan="7">Buscando empresas…</td></tr>';

        try {
            const response = await fetch(`${base}/Controller/buscar.php`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    keyword: document.getElementById('keyword').value,
                    location: document.getElementById('location').value,
                    limit: document.getElementById('limit').value,
                }),
            });

            const data = await response.json();

            if (!data.success) {
                alert('Error: ' + data.error);
                return;
            }

            localStorage.setItem('empresas', JSON.stringify(data.empresas));
            mostrarResultados(data.empresas, data.saved, data.save_error);
            extractBtn.disabled = false;
        } catch (error) {
            alert('Error de conexión: ' + error.message);
        } finally {
            searchBtn.disabled = false;
        }
    });

    function mostrarResultados(empresas, saved, saveError) {
        resultsBody.innerHTML = '';

        empresas.forEach((empresa, index) => {
            const row = resultsBody.insertRow();
            const emails = empresa.emails || [];
            row.insertCell(0).textContent = String(index + 1);
            row.insertCell(1).textContent = empresa.nombre || 'N/A';
            row.insertCell(2).innerHTML = empresa.website
                ? `<a href="${empresa.website}" target="_blank" rel="noopener noreferrer">Ver sitio</a>`
                : 'N/A';
            row.insertCell(3).textContent = empresa.telefono || 'N/A';
            row.insertCell(4).textContent = empresa.direccion || 'N/A';
            row.insertCell(5).textContent = empresa.rating || 'N/A';
            row.insertCell(6).innerHTML = emails.length
                ? `<span class="badge badge-ok">${emails.length} emails</span>`
                : '<span class="badge badge-wait">Sin emails</span>';
        });

        const conWeb = empresas.filter((item) => item.website).length;
        const conTelefono = empresas.filter((item) => item.telefono && item.telefono !== 'N/A').length;

        stats.innerHTML = `
            <div><strong>${empresas.length}</strong><span>Empresas</span></div>
            <div><strong>${conWeb}</strong><span>Con sitio web</span></div>
            <div><strong>${conTelefono}</strong><span>Con teléfono</span></div>
        `;

        mostrarEstadoGuardado(saved, saveError);
    }

    function mostrarEstadoGuardado(saved, saveError) {
        if (saved === true) {
            saveStatus.hidden = false;
            saveStatus.className = 'banner banner-ok';
            saveStatus.textContent = 'Búsqueda guardada en la base de datos.';
            return;
        }

        if (saveError) {
            saveStatus.hidden = false;
            saveStatus.className = 'banner banner-error';
            saveStatus.textContent = 'No se pudo guardar en la base de datos: ' + saveError;
            return;
        }

        saveStatus.hidden = true;
        saveStatus.textContent = '';
    }

    extractBtn.addEventListener('click', async () => {
        const empresas = JSON.parse(localStorage.getItem('empresas') || '[]');
        const conWeb = empresas.filter((item) => item.website);

        if (conWeb.length === 0) {
            alert('No hay empresas con sitio web para extraer emails');
            return;
        }

        progress.hidden = false;
        extractBtn.disabled = true;

        for (let i = 0; i < empresas.length; i += 1) {
            if (!empresas[i].website) {
                continue;
            }

            const percent = Math.round(((i + 1) / empresas.length) * 100);
            progressBar.style.transform = `scaleX(${percent / 100})`;
            progressBar.textContent = `${percent}% · ${empresas[i].nombre}`;

            try {
                const response = await fetch(`${base}/Controller/extraer_emails.php`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        url: empresas[i].website,
                        empresa: empresas[i].nombre,
                        place_id: empresas[i].place_id,
                    }),
                });
                const data = await response.json();
                empresas[i].emails = data.success ? data.emails : [];
                empresas[i].todos_emails = data.success ? data.todos_emails : [];
                localStorage.setItem('empresas', JSON.stringify(empresas));
                mostrarResultados(empresas);
            } catch (error) {
                console.error(error);
            }

            await new Promise((resolve) => setTimeout(resolve, 1000));
        }

        progress.hidden = true;
        extractBtn.disabled = false;
        alert('Extracción de emails completada.');
    });

    downloadBtn.addEventListener('click', () => {
        const empresas = JSON.parse(localStorage.getItem('empresas') || '[]');
        let csv = 'Nombre,Website,Telefono,Direccion,Rating,Emails,TodosLosEmails\n';

        empresas.forEach((empresa) => {
            const row = [
                empresa.nombre,
                empresa.website,
                empresa.telefono,
                empresa.direccion,
                empresa.rating,
                (empresa.emails || []).join(';'),
                (empresa.todos_emails || []).join(';'),
            ].map((value) => `"${String(value || '').replace(/"/g, '""')}"`);
            csv += row.join(',') + '\n';
        });

        const blob = new Blob(['\uFEFF' + csv], { type: 'text/csv;charset=utf-8;' });
        const link = document.createElement('a');
        const url = URL.createObjectURL(blob);
        link.href = url;
        link.download = 'empresas.csv';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        URL.revokeObjectURL(url);
    });
})();
