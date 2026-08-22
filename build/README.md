# Rescate Vertical — Tema y Plugin de WordPress

Sitio educativo sobre rescate vertical en emergencias médicas.
Tema propio (`rescate-vertical` v2.0) + plugin propio (`rescate-vertical-tools`).

## Contenido de esta entrega

```
rescate-vertical.zip           → tema
rescate-vertical-tools.zip     → plugin (simulador, casos con IA, validaciones)
README.md                      → este archivo
```

Ambos zips se suben tal cual desde el panel de WordPress — no hay que
descomprimirlos antes.

---

## 1. Instalar / actualizar

### Si ya tenías la versión anterior instalada

1. **Apariencia → Temas**: sube `rescate-vertical.zip` con **Añadir nuevo →
   Subir tema**. WordPress avisará de que el tema ya existe y ofrecerá
   **"Reemplazar el actual con el subido"** — acepta.
2. **Plugins**: igual, con **Añadir nuevo → Subir plugin** y
   `rescate-vertical-tools.zip`, y **Reemplazar**.
3. **Purga la caché** (paso 5). Sin esto seguirás viendo el diseño viejo.

Las páginas, el menú y las validaciones guardadas se conservan: los nombres
de las plantillas no cambiaron.

### Instalación desde cero

1. **hPanel → Sitios web → Administrar → WordPress → Panel de administrador**
   (o entra directo a `tudominio.com/wp-admin`).
2. **Apariencia → Temas → Añadir nuevo → Subir tema** →
   `rescate-vertical.zip` → **Instalar** → **Activar**.
3. **Plugins → Añadir nuevo → Subir plugin** → `rescate-vertical-tools.zip`
   → **Instalar** → **Activar**.

---

## 2. Crear las páginas y asignarles su plantilla

Cada sección tiene su propia plantilla con el diseño ya codificado. En
**Páginas → Añadir nueva**, crea una por sección y en
**Atributos de página → Plantilla** elige la que corresponde:

| Página                | Slug         | Plantilla                                |
|-----------------------|--------------|------------------------------------------|
| Qué es                | `que-es`     | Sección · Qué es el rescate vertical     |
| Física del rescate    | `fisica`     | Sección · Física del rescate             |
| Técnicas              | `tecnicas`   | Sección · Técnicas                       |
| Equipos               | `equipos`    | Sección · Equipos                        |
| Protocolos y normativas | `protocolos` | Sección · Protocolos y normativas      |
| Practicar en digital  | `practicar`  | Sección · Practicar en digital           |

> **Importante:** si la plantilla queda en "Predeterminada", la página sale
> casi vacía. Ese es el error más común.

**Inicio** no necesita página propia: el tema la muestra automáticamente en
la portada.

Puedes dejar el editor de contenido vacío — el copy ya viene en la plantilla.
Si escribes algo, aparecerá al final de la sección.

---

## 3. Menú de navegación

1. **Apariencia → Menús** → crea un menú (p. ej. "Menú principal").
2. Añade un enlace personalizado "Inicio" a la URL raíz, y luego las 6 páginas.
3. En **Ajustes de menú** marca la ubicación **"Menú principal"** y guarda.

**Usa etiquetas cortas.** Despliega cada elemento del menú y escribe en
**Etiqueta de navegación**: `Qué es`, `Física`, `Técnicas`, `Equipos`,
`Protocolos`, `Practicar`. Con los títulos largos completos el menú se parte
en dos líneas en pantallas medianas.

---

## 4. Cambiar las fotos por las tuyas

El tema trae 6 fotos incluidas (todas de dominio público o licencia libre
para uso comercial, sin necesidad de atribución). Para poner las tuyas
tienes dos caminos:

- **Por página (recomendado, sin FTP):** edita la página y asigna una
  **Imagen destacada**. Las secciones "Qué es" y "Practicar" la usan
  automáticamente en lugar de la foto incluida.
- **Reemplazando los archivos:** en el Administrador de archivos de hPanel,
  entra a `wp-content/themes/rescate-vertical/assets/images/` y sustituye
  los `.jpg` manteniendo **exactamente los mismos nombres**:

  | Archivo          | Dónde aparece                                  |
  |------------------|------------------------------------------------|
  | `hero.jpg`       | Portada (fondo del hero) y cabecera de Física  |
  | `que-es.jpg`     | Portada y cabecera de Qué es                   |
  | `tecnicas.jpg`   | Banda de la portada y cabecera de Técnicas     |
  | `equipos.jpg`    | Cabecera de Equipos                            |
  | `protocolos.jpg` | Cabecera de Protocolos                         |
  | `practicar.jpg`  | Portada y cabecera de Practicar                |

  Usa imágenes horizontales de ~1500 px de ancho y menos de ~400 KB.

---

## 5. Purgar la caché (Hostinger)

Hostinger usa LiteSpeed Cache: tras cualquier cambio de diseño o plantilla,
si sigues viendo lo viejo:

- En la barra superior de `wp-admin`, menú **LiteSpeed Cache → Purge All**, o
- **hPanel → Sitios web → Administrar → Rendimiento / Caché → Purgar caché**.

Después abre el sitio en una **ventana de incógnito** para descartar también
la caché de tu navegador.

---

## 6. Activar "Presenta tu caso" (IA con Gemini)

El estudiante describe un escenario y recibe retroalimentación educativa.
**No hay que tocar `functions.php` ni ningún archivo:**

1. Consigue una clave gratuita en https://aistudio.google.com/apikey
2. **Rescate Vertical → Ajustes**, sección
   **«Presenta tu caso» — retroalimentación con IA (Gemini)**.
3. Pega la clave en **Clave de la API de Gemini** y guarda. Queda en la base
   de datos, nunca en un archivo del tema o del plugin.
4. Opcional: el **Modelo** (por defecto `gemini-2.0-flash`) y las
   **Instrucciones para la IA** también se editan ahí.

Sin clave configurada el formulario se muestra igual, pero responde con un
aviso pidiendo configurarla — no rompe la página.

---

## 7. Ajustar el simulador de factor de caída

**Rescate Vertical → Ajustes** permite editar sin código:

- Umbrales del factor de caída (seguro / moderado / peligroso).
- Límite de kN del sistema de aseguramiento.
- Ángulo de advertencia y ángulo crítico entre anclajes.
- El texto de cada nivel de riesgo. Dentro del texto puedes usar los
  marcadores `{kn}`, `{angle_warn}` y `{angle_critical}`.

---

## 8. Validaciones de expertos

- **Rescate Vertical → Validaciones**: todas las respuestas del formulario.
- **Rescate Vertical → Exportar CSV**: descarga todas las respuestas
  (el CSV abre con tildes correctas en Excel).

---

## Shortcodes disponibles

Ya están incrustados en sus plantillas, pero puedes reutilizarlos en
cualquier página o entrada:

- `[rv_simulador_caso]` — simulador interactivo de factor de caída.
- `[rv_presenta_caso]` — formulario de caso con respuesta de IA.
- `[rv_formulario_validacion]` — formulario de validación por expertos.

---

## Notas técnicas

- Sin Elementor ni ningún constructor: todo el diseño va en PHP y CSS dentro
  del tema.
- Tipografías Archivo, Public Sans y JetBrains Mono vía Google Fonts con
  `wp_enqueue_style`.
- Animaciones de entrada con `IntersectionObserver`; se desactivan solas si
  el visitante tiene activado "reducir movimiento" en su sistema.
- Los diagramas técnicos (rapel, polipasto, anclajes, camilla) son SVG
  dibujados en el tema: escalan sin perder nitidez y no pesan nada.
- Responsive con menú desplegable por debajo de 1080 px y layouts de una
  columna por debajo de 860 px.
- Accesibilidad: enlace "saltar al contenido", foco visible en todos los
  controles, SVG decorativos marcados `aria-hidden`, y texto alternativo en
  todas las fotos.
- Todo el plugin usa el prefijo `rv_` / `RV_` en funciones, clases, hooks y
  opciones, con nonces, sanitización de entradas y escape de salidas.
