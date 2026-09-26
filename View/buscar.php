<section class="search-panel">
    <h2>Consulta en Google Places</h2>
    <p>Indica el rubro, la ciudad y cuántas empresas quieres. Sirve para cualquier giro.</p>

    <form id="searchForm" class="search-form">
        <div class="field">
            <label for="keyword">
                <i class="fa-solid fa-briefcase" aria-hidden="true"></i>
                Rubro
            </label>
            <input type="text" id="keyword" name="keyword" value="" placeholder="Restaurantes, clínicas, ferreterías, estudios contables" required>
        </div>
        <div class="field">
            <label for="location">
                <i class="fa-solid fa-location-dot" aria-hidden="true"></i>
                Ciudad / región
            </label>
            <input type="text" id="location" name="location" value="Piura, Perú" placeholder="Piura, Perú">
        </div>
        <div class="field field-narrow">
            <label for="limit">
                <i class="fa-solid fa-hashtag" aria-hidden="true"></i>
                Cantidad
            </label>
            <input type="number" id="limit" name="limit" value="20" min="1" max="60">
        </div>
        <div class="field field-action">
            <button type="submit" class="btn btn-primary" id="searchBtn">
                <i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i>
                Buscar empresas
            </button>
        </div>
    </form>
</section>

<section id="results" class="results-panel" hidden>
    <div class="results-head">
        <h2>Resultados</h2>
        <div class="results-actions">
            <button type="button" id="extractEmailsBtn" class="btn btn-secondary" disabled>
                <i class="fa-solid fa-envelope" aria-hidden="true"></i>
                Extraer emails
            </button>
            <button type="button" id="downloadCsvBtn" class="btn btn-ghost">
                <i class="fa-solid fa-file-csv" aria-hidden="true"></i>
                Descargar CSV
            </button>
        </div>
    </div>

    <div id="stats" class="run-stats"></div>
    <div id="saveStatus" class="banner" hidden></div>
    <div id="progress" class="progress" hidden>
        <div class="progress-bar" id="progressBar">0%</div>
    </div>

    <div class="table-wrap">
        <table class="data-table" id="resultsTable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Empresa</th>
                    <th>Sitio web</th>
                    <th>Teléfono</th>
                    <th>Dirección</th>
                    <th>Rating</th>
                    <th>Emails</th>
                </tr>
            </thead>
            <tbody id="resultsBody"></tbody>
        </table>
    </div>
</section>
