<?php

require_once __DIR__ . '/../config/queries.php';

$resumen = dashboardResumen();
$sinHistorial = $resumen['ok'] && $resumen['busquedas'] === 0;
?>

<?php if (!$resumen['ok']): ?>
    <div class="banner banner-error" role="alert">
        <i class="fa-solid fa-triangle-exclamation" aria-hidden="true"></i>
        No se pudo leer la base de datos. Revisa config/database.php y que exista la base scrapersoftware.
        <?php if ($resumen['error']): ?>
            <span><?= e($resumen['error']) ?></span>
        <?php endif; ?>
    </div>
<?php endif; ?>

<section class="ledger" aria-labelledby="historial-title">
    <div class="ledger-head">
        <div>
            <h2 id="historial-title">Últimas búsquedas</h2>
            <p>El trabajo que ya quedó guardado. Sigue desde aquí o lanza otra consulta.</p>
        </div>
        <dl class="tally" aria-label="Resumen guardado">
            <div>
                <dt>Búsquedas</dt>
                <dd><?= (int) $resumen['busquedas'] ?></dd>
            </div>
            <div>
                <dt>Empresas</dt>
                <dd><?= (int) $resumen['empresas'] ?></dd>
            </div>
            <div>
                <dt>Con web</dt>
                <dd><?= (int) $resumen['con_web'] ?></dd>
            </div>
            <div>
                <dt>Con email</dt>
                <dd><?= (int) $resumen['con_email'] ?></dd>
            </div>
        </dl>
    </div>

    <?php if ($sinHistorial): ?>
        <div class="empty">
            <i class="fa-solid fa-folder-open" aria-hidden="true"></i>
            <h3>Todavía no hay búsquedas</h3>
            <p>Busca por rubro y ciudad. Los resultados se guardan solos en MySQL.</p>
            <a class="btn btn-primary" href="<?= e(app_url('buscar')) ?>">
                <i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i>
                Hacer la primera búsqueda
            </a>
        </div>
    <?php else: ?>
        <div class="table-wrap">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Consulta</th>
                        <th>Ciudad</th>
                        <th>Pedidas</th>
                        <th>Encontradas</th>
                        <th>Fecha</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($resumen['ultimas_busquedas'] as $busqueda): ?>
                        <tr>
                            <td><?= e($busqueda['keyword']) ?></td>
                            <td><?= e($busqueda['location']) ?></td>
                            <td><?= (int) $busqueda['limite'] ?></td>
                            <td><?= (int) $busqueda['total_encontradas'] ?></td>
                            <td><?= e(formatearFecha($busqueda['created_at'])) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</section>

<?php if ($resumen['ultimas_empresas'] !== []): ?>
    <section class="roster" aria-labelledby="empresas-title">
        <h2 id="empresas-title">Empresas recientes</h2>
        <ul class="roster-list">
            <?php foreach ($resumen['ultimas_empresas'] as $empresa): ?>
                <li>
                    <div>
                        <strong><?= e($empresa['nombre']) ?></strong>
                        <span><?= e($empresa['direccion'] ?: 'Sin dirección') ?></span>
                    </div>
                    <div class="roster-meta">
                        <?php if (!empty($empresa['rating'])): ?>
                            <span>
                                <i class="fa-solid fa-star" aria-hidden="true"></i>
                                <?= e(number_format((float) $empresa['rating'], 1)) ?>
                            </span>
                        <?php endif; ?>
                        <?php if (!empty($empresa['telefono'])): ?>
                            <span>
                                <i class="fa-solid fa-phone" aria-hidden="true"></i>
                                <?= e($empresa['telefono']) ?>
                            </span>
                        <?php endif; ?>
                        <?php if (!empty($empresa['website'])): ?>
                            <a href="<?= e($empresa['website']) ?>" target="_blank" rel="noopener noreferrer">
                                Sitio
                                <i class="fa-solid fa-arrow-up-right-from-square" aria-hidden="true"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    </section>
<?php endif; ?>
