# Cómo está el proyecto

Scraper de empresas por rubro. Consulta Google Places, guarda los resultados en MySQL y puede extraer emails de los sitios web. No está limitado al turismo: sirve para cualquier giro (restaurantes, clínicas, ferreterías, etc.).

Entrada del sitio: `index.php`  
Ejemplo local: `http://localhost/PHP%20Projects/scrapersoftware/`

Autor: Frank Lisboa Abad · Piura · Perú

---

## Carpetas

```text
scrapersoftware/
├── index.php              Entrada. Enruta las pantallas.
├── .env                   Credenciales (no se sube a GitHub)
├── .env.example           Plantilla del .env
├── .gitignore             Ignora vendor/ y .env
├── .htaccess              Bloquea config/, docs/, vendor/ y .env
├── composer.json
│
├── Controller/            Acciones PHP (JSON)
│   ├── buscar.php         Busca en Google Places y guarda
│   └── extraer_emails.php Extrae correos y los guarda
│
├── View/                  Pantallas
│   ├── layout.php         Cascara: menú + cabecera
│   ├── dashboard.php      Panel con historial
│   ├── buscar.php         Formulario de búsqueda
│   ├── informacion.php    Datos del proyecto y autor
│   └── partials/
│       └── sidebar.php    Menú: Panel, Buscar, Información
│
├── config/                Conexión y persistencia (no es pública)
│   ├── env.php            Carga el .env
│   ├── database.php       Lee host, usuario y clave
│   ├── connection.php     PDO (función db())
│   ├── persist.php        Guarda búsquedas, empresas y emails
│   └── queries.php        Lecturas del panel
│
├── assets/
│   ├── css/app.css
│   └── js/
│       ├── app.js         Menú móvil
│       └── buscar.js      Llama a los controllers
│
└── docs/                  Documentación y SQL
    ├── proyecto.md        Este archivo
    ├── PRODUCT.md
    ├── README.md
    └── database.sql
```

`View/` son pantallas. `Controller/` son endpoints. `config/` no se abre desde el navegador.

---

## Pantallas

| Ruta | Qué muestra |
|---|---|
| `index.php` o `?page=dashboard` | Últimas búsquedas y empresas guardadas |
| `index.php?page=buscar` | Formulario: rubro, ciudad, cantidad |
| `index.php?page=informacion` | Explicación del proyecto y datos del autor |

---

## Flujo

1. El usuario entra al **Panel** o a **Buscar**.
2. En Buscar indica rubro, ciudad y límite (máx. 60).
3. `assets/js/buscar.js` llama a `Controller/buscar.php`.
4. El controller consulta Google Places, pide detalle (web, teléfono, rating) y guarda en MySQL.
5. Si pulsa **Extraer emails**, `Controller/extraer_emails.php` recorre el sitio, filtra correos genéricos y los guarda.
6. **Descargar CSV** arma el archivo en el navegador (`empresas.csv`).

La empresa se identifica por `place_id` de Google: si vuelves a buscarla, se actualiza; no se duplica.

---

## Base de datos

Script: `docs/database.sql`  
Base: `scrapersoftware`

| Tabla | Para qué |
|---|---|
| `busquedas` | Cada consulta (rubro, ciudad, límite, fecha) |
| `empresas` | Ficha de la empresa (`place_id` único) |
| `busqueda_empresa` | Qué empresas salieron en qué búsqueda |
| `empresa_emails` | Correos; `es_filtrado` = 1 si pasó el filtro |

Importar en phpMyAdmin o:

```bash
mysql -u root < docs/database.sql
```

---

## Credenciales

Van en `.env` (cópialo desde `.env.example`):

- `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`
- `GOOGLE_PLACES_API_KEY`

Nunca subas `.env` a GitHub. Si otro equipo clona el repo: `composer install`, copiar `.env.example` a `.env` y completar valores.

---

## Cómo arrancar en Laragon

1. Apache y MySQL encendidos.
2. Importar `docs/database.sql`.
3. Tener `.env` con la base y la API key de Google Places.
4. `composer install` (si no existe `vendor/`).
5. Abrir `index.php` en el navegador.

Dependencias: PHP, MySQL, Composer, `vlucas/phpdotenv`, `peterujah/email-crawl`, Guzzle. Fuente Open Sans e iconos Font Awesome (CDN).
