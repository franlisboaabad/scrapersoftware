# Product

<!-- impeccable:product-schema 1 -->

## Platform

web

## Users

Operador que busca empresas de cualquier rubro por ciudad y guarda contactos.

## Product Purpose

Consultar Google Places por rubro y ciudad, persistir empresas y extraer emails de sus sitios. El éxito es un historial reutilizable en MySQL, no solo un CSV de la sesión.

## Positioning

Búsqueda + guardado automático + extracción de emails en el mismo flujo local (Laragon), anclado al `place_id` de Google.

## Operating Context

Uso local en escritorio (Laragon/Apache + MySQL). Flujo: dashboard → buscar → extraer emails → descargar CSV. Datos reales de Places, no demos inventados.

## Capabilities and Constraints

- Búsqueda por tipo, ciudad y límite (máx. 60).
- Persistencia en `busquedas`, `empresas`, `busqueda_empresa`, `empresa_emails`.
- Extracción de emails por sitio web.
- PHP plano, sin framework.

## Brand Commitments

- Carpeta de vistas: `View`.
- Iconos: Font Awesome.
- Fuente: Open Sans.
- Dashboard básico como entrada del producto.
- Los archivos `.md` viven en `docs/`.

## Evidence on Hand

Empresas y búsquedas reales en MySQL. No hay testimonios, logo oficial ni marca comercial.

## Product Principles

- El historial de trabajo es el producto, no solo la última búsqueda.
- No inventar métricas, clientes ni resultados.
- Una acción primaria por pantalla.
- Iconos dibujados, no emoji.
