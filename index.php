<?php
// index.php - Interfaz principal
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scraper de Empresas Turísticas - Google Places API</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }

        h1 {
            color: #333;
            margin-bottom: 10px;
        }

        .subtitle {
            color: #666;
            margin-bottom: 30px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #333;
        }

        input[type="text"],
        input[type="number"] {
            width: 100%;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s;
        }

        input[type="text"]:focus,
        input[type="number"]:focus {
            outline: none;
            border-color: #667eea;
        }

        button {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            transition: transform 0.2s;
        }

        button:hover {
            transform: translateY(-2px);
        }

        .result-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .result-table th,
        .result-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .result-table th {
            background: #f8f9fa;
            font-weight: bold;
            color: #333;
        }

        .result-table tr:hover {
            background: #f8f9fa;
        }

        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
        }

        .badge-success {
            background: #d4edda;
            color: #155724;
        }

        .badge-warning {
            background: #fff3cd;
            color: #856404;
        }

        .badge-danger {
            background: #f8d7da;
            color: #721c24;
        }

        .progress {
            background: #f0f0f0;
            border-radius: 10px;
            height: 30px;
            overflow: hidden;
            margin: 20px 0;
        }

        .progress-bar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            height: 100%;
            line-height: 30px;
            color: white;
            text-align: center;
            transition: width 0.3s;
        }

        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .btn-download {
            background: #28a745;
            margin-left: 10px;
        }

        .stats {
            display: flex;
            gap: 20px;
            margin-top: 20px;
        }

        .stat-box {
            flex: 1;
            background: #f8f9fa;
            padding: 15px;
            border-radius: 10px;
            text-align: center;
        }

        .stat-number {
            font-size: 28px;
            font-weight: bold;
            color: #667eea;
        }

        .stat-label {
            color: #666;
            margin-top: 5px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="card">
            <h1>🏢 Scraper de Empresas Turísticas</h1>
            <p class="subtitle">Extrae información de empresas turísticas usando Google Places API</p>

            <form id="searchForm">
                <div class="form-group">
                    <label>🔍 Tipo de empresa:</label>
                    <input type="text" id="keyword" value="agencias de viajes" placeholder="Ej: agencias de viajes, operadores turísticos, hoteles">
                </div>

                <div class="form-group">
                    <label>📍 Ciudad / Región:</label>
                    <input type="text" id="location" value="Piura, Perú" placeholder="Ej: Piura, Perú">
                </div>

                <div class="form-group">
                    <label>📊 Cantidad de empresas (máximo 60):</label>
                    <input type="number" id="limit" value="20" min="1" max="60">
                </div>

                <button type="submit">🚀 Buscar empresas</button>
            </form>
        </div>

        <div id="results" style="display: none;">
            <div class="card">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <h2>📋 Resultados de la búsqueda</h2>
                    <div>
                        <button id="extractEmailsBtn" class="btn-download">✉️ Extraer emails</button>
                        <button id="downloadCsvBtn" class="btn-download">📥 Descargar CSV</button>
                    </div>
                </div>

                <div id="stats" class="stats"></div>
                <div id="progress" class="progress" style="display: none;">
                    <div class="progress-bar" id="progressBar">0%</div>
                </div>

                <div style="overflow-x: auto;">
                    <table class="result-table" id="resultsTable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Empresa</th>
                                <th>Sitio Web</th>
                                <th>Teléfono</th>
                                <th>Dirección</th>
                                <th>Rating</th>
                                <th>Emails</th>
                            </tr>
                        </thead>
                        <tbody id="resultsBody"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('searchForm').addEventListener('submit', async (e) => {
            e.preventDefault();

            const keyword = document.getElementById('keyword').value;
            const location = document.getElementById('location').value;
            const limit = document.getElementById('limit').value;

            // Mostrar loading
            document.getElementById('results').style.display = 'block';
            document.getElementById('resultsBody').innerHTML = '<tr><td colspan="7">🔍 Buscando empresas...</td></tr>';
            document.getElementById('extractEmailsBtn').disabled = true;

            try {
                const response = await fetch('buscar.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        keyword,
                        location,
                        limit
                    })
                });

                const data = await response.json();

                if (data.success) {
                    mostrarResultados(data.empresas);
                    document.getElementById('extractEmailsBtn').disabled = false;

                    // Guardar empresas en localStorage para usar después
                    localStorage.setItem('empresas', JSON.stringify(data.empresas));
                } else {
                    alert('Error: ' + data.error);
                }
            } catch (error) {
                alert('Error de conexión: ' + error.message);
            }
        });

        function mostrarResultados(empresas) {
            const tbody = document.getElementById('resultsBody');
            tbody.innerHTML = '';

            empresas.forEach((empresa, index) => {
                const row = tbody.insertRow();
                row.insertCell(0).textContent = index + 1;
                row.insertCell(1).textContent = empresa.nombre || 'N/A';
                row.insertCell(2).innerHTML = empresa.website ? `<a href="${empresa.website}" target="_blank">🌐 Ver</a>` : 'N/A';
                row.insertCell(3).textContent = empresa.telefono || 'N/A';
                row.insertCell(4).textContent = empresa.direccion || 'N/A';
                row.insertCell(5).textContent = empresa.rating || 'N/A';
                row.insertCell(6).innerHTML = empresa.emails ? `<span class="badge badge-success">${empresa.emails.length} emails</span>` : '<span class="badge badge-warning">Sin emails</span>';
            });

            // Actualizar estadísticas
            const conWeb = empresas.filter(e => e.website).length;
            const conTelefono = empresas.filter(e => e.telefono).length;
            const stats = `
                <div class="stat-box">
                    <div class="stat-number">${empresas.length}</div>
                    <div class="stat-label">Total empresas</div>
                </div>
                <div class="stat-box">
                    <div class="stat-number">${conWeb}</div>
                    <div class="stat-label">Con sitio web</div>
                </div>
                <div class="stat-box">
                    <div class="stat-number">${conTelefono}</div>
                    <div class="stat-label">Con teléfono</div>
                </div>
            `;
            document.getElementById('stats').innerHTML = stats;
        }

        document.getElementById('extractEmailsBtn').addEventListener('click', async () => {
            const empresas = JSON.parse(localStorage.getItem('empresas') || '[]');
            const empresasConWeb = empresas.filter(e => e.website);

            if (empresasConWeb.length === 0) {
                alert('No hay empresas con sitio web para extraer emails');
                return;
            }

            document.getElementById('progress').style.display = 'block';
            document.getElementById('extractEmailsBtn').disabled = true;

            for (let i = 0; i < empresas.length; i++) {
                if (empresas[i].website) {
                    const percent = Math.round(((i + 1) / empresas.length) * 100);
                    document.getElementById('progressBar').style.width = `${percent}%`;
                    document.getElementById('progressBar').textContent = `${percent}% - Procesando: ${empresas[i].nombre}`;

                    try {
                        const response = await fetch('extraer_emails.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({
                                url: empresas[i].website,
                                empresa: empresas[i].nombre
                            })
                        });

                        const data = await response.json();
                        if (data.success && data.emails.length > 0) {
                            empresas[i].emails = data.emails;
                            empresas[i].todos_emails = data.todos_emails;
                        } else {
                            empresas[i].emails = [];
                            empresas[i].todos_emails = [];
                        }

                        localStorage.setItem('empresas', JSON.stringify(empresas));
                        mostrarResultados(empresas);

                    } catch (error) {
                        console.error(`Error al extraer emails de ${empresas[i].nombre}:`, error);
                    }

                    // Pequeña pausa para no sobrecargar
                    await new Promise(resolve => setTimeout(resolve, 1000));
                }
            }

            document.getElementById('progress').style.display = 'none';
            document.getElementById('extractEmailsBtn').disabled = false;
            alert('✅ Extracción de emails completada!');
        });

        document.getElementById('downloadCsvBtn').addEventListener('click', () => {
            const empresas = JSON.parse(localStorage.getItem('empresas') || '[]');

            // Crear contenido CSV
            let csv = "Nombre,Website,Telefono,Direccion,Rating,Emails,TodosLosEmails\n";
            empresas.forEach(empresa => {
                const row = [
                    `"${(empresa.nombre || '').replace(/"/g, '""')}"`,
                    `"${(empresa.website || '').replace(/"/g, '""')}"`,
                    `"${(empresa.telefono || '').replace(/"/g, '""')}"`,
                    `"${(empresa.direccion || '').replace(/"/g, '""')}"`,
                    `"${(empresa.rating || '').replace(/"/g, '""')}"`,
                    `"${(empresa.emails || []).join(';')}"`,
                    `"${(empresa.todos_emails || []).join(';')}"`
                ];
                csv += row.join(',') + "\n";
            });

            // Descargar archivo
            const blob = new Blob(["\uFEFF" + csv], {
                type: 'text/csv;charset=utf-8;'
            });
            const link = document.createElement('a');
            const url = URL.createObjectURL(blob);
            link.setAttribute('href', url);
            link.setAttribute('download', 'empresas_turismo.csv');
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            URL.revokeObjectURL(url);
        });
    </script>
</body>

</html>