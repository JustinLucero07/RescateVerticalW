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

## 4. Cambiar CUALQUIER imagen desde el escritorio

**Todas** las fotografías del sitio se cambian sin tocar archivos ni FTP:

**Apariencia → Personalizar → Imágenes del sitio**

Ahí están agrupadas en tres bloques:

- **Portada y cabeceras** (6): la imagen principal de la portada y la
  cabecera de cada sección.
- **Fotos de nudos** (4): nudo ocho, ballestrinque, prusik y mariposa.
- **Fotos de equipos** (6): cuerda, arnés, mosquetón, descensor, casco y
  camilla.

En cada una pulsas **Cambiar imagen**, la subes desde tu ordenador y le das
a **Publicar**. Para volver a la que trae el tema, pulsa **Eliminar**.

También tienes un resumen con vista previa en **Apariencia → Imágenes del
sitio**, que te dice cuáles has personalizado y cuáles siguen por defecto.

> Para la portada usa una foto horizontal de al menos 1500 px de ancho.
> Para nudos y equipos, horizontal 4:3 de unos 900 px.

Alternativa por página: si asignas una **Imagen destacada** a las páginas
"Qué es" o "Practicar", esa manda sobre la del tema en esa sección.

---

## 4b. Poner tu logo

**Apariencia → Personalizar → Identidad del sitio → Logo**

Sube el logo de ANCLAVIA (PNG con fondo transparente funciona mejor) y
publica. Sustituye automáticamente al icono del nudo ocho en la cabecera.
Se escala solo: hasta 76 px de alto en escritorio y 44 px en móvil.

**El nombre aparece al lado del logo**, en tipografía Chakra Petch, y el
logo también sale en el pie de página.
Ese texto sale de **Ajustes → Generales → Título del sitio**: ponlo en
`ANCLAVIA` para que se lea así en la cabecera (y también en la pestaña del
navegador y en Google).

Si no subes ninguno, la cabecera sigue mostrando el icono del nudo ocho
junto al nombre del sitio.

El **nombre del sitio** (el texto que acompaña al logo y el título en
Google) se cambia en **Ajustes → Generales → Título del sitio**.

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
- `[rv_simulador_anclaje]` — simulador de carga en anclajes según el ángulo.
- `[rv_caso_practico]` — ejercicio con caso aleatorio y corrección.
- `[rv_presenta_caso]` — formulario de caso con respuesta de IA.
- `[rv_formulario_validacion]` — formulario de validación por expertos.

---

## Notas técnicas

- Sin Elementor ni ningún constructor: todo el diseño va en PHP y CSS dentro
  del tema.
- Paleta tomada del logo ANCLAVIA: azul médico #1B7FC4, naranja #D9502B,
  dorado #C0982F y grafito #101820. Se cambian en un solo sitio, al principio
  de `style.css`.
- Tipografías Archivo y Source Sans 3 vía Google Fonts con `wp_enqueue_style`.
 - Las cifras usan Archivo con numeración tabular; no se usa ninguna fuente
  monoespaciada.
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

---

## 9. Los dos simuladores

### Factor de caída — `[rv_simulador_caso]`

Responde a: *¿qué tan violento es el impacto si alguien cae?*

`Factor de caída = distancia de caída ÷ cuerda disponible`

La cuerda es lo que absorbe la energía estirándose. Cuanta más cuerda hay
en juego para una misma caída, más suave es la frenada. Por eso el número
que importa no son los metros de caída sueltos, sino esa **relación**.

- **FC 0** — el sistema está en tensión, no hay caída libre.
- **FC 1** — caes tanto como cuerda tienes: ya toca revisar el montaje.
- **FC 2** — el máximo posible. Toda la cuerda, ninguna absorción útil.

Caer 4 m con 8 m de cuerda (FC 0,5) es mucho menos grave que caer 4 m con
2 m de cuerda (FC 2), aunque la altura sea la misma. El simulador cambia de
color en los umbrales configurados en Ajustes.

### Carga en anclajes — `[rv_simulador_anclaje]`

Responde a: *¿cuánto aguanta cada anclaje según cómo de abiertas estén las
ramas?*

`Fuerza por anclaje = carga total ÷ (2 × coseno(ángulo ÷ 2))`

Es el complemento del anterior: uno mide la energía del golpe, este mide
cómo se reparte esa carga. Lo contraintuitivo es que **abrir el ángulo no
reparte mejor, reparte peor**:

| Ángulo | Cada anclaje soporta |
|--------|----------------------|
| 0°     | 50 % de la carga     |
| 60°    | 58 %                 |
| 120°   | 100 % (¡la carga entera, cada uno!) |
| 150°   | 193 %                |

Por eso la regla de campo es mantenerse por debajo de 60°. El simulador
muestra la fuerza en kN, el porcentaje respecto a la carga, el margen que
queda frente a la resistencia del anclaje, y un esquema que se abre y se
cierra con el ángulo.

Los umbrales (ángulo de advertencia y crítico) y la resistencia del anclaje
se editan en **Rescate Vertical → Ajustes**.

---

## 10. Casos prácticos con corrección

En **Practicar en digital** el alumno recibe un **caso distinto cada vez**,
lo resuelve y pulsa "Ver corrección".

Qué recibe:

1. **El factor de caída corregido automáticamente.** Se compara su número
   con el real (tolerancia ±0,1) y se le muestra el cálculo. Esto no
   depende de la IA: es matemática exacta y funciona siempre.
2. **La respuesta modelo**, o sea lo que debería haberse hecho.
3. **Una evaluación de sus respuestas abiertas**, solo si has configurado
   la clave de Gemini. Sin clave, los puntos 1 y 2 siguen funcionando.

### Añadir o editar casos

**Rescate Vertical → Casos prácticos**. El plugin trae 4 casos de ejemplo
ya cargados. Para crear uno nuevo:

- **Título**: nombre corto del caso.
- **Editor**: el enunciado que lee el alumno.
- **Distancia de caída** y **Cuerda disponible**: con estos dos datos el
  factor de caída se corrige solo. Si los dejas vacíos, esa pregunta no
  se le muestra al alumno.
- **Respuesta modelo**: lo que debería haber contestado. Admite títulos
  con `##` y viñetas con `-`.

Los casos de ejemplo solo se crean la primera vez: si los borras o los
editas, el plugin no los vuelve a añadir.


---

## 11. Ampliar el contenido desde el escritorio

Equipos, Técnicas y Protocolos ya **no están escritos en el código**: son
fichas que se editan en el menú **Rescate Vertical**. El contenido que ya
existía se convirtió en fichas, así que no se perdió nada y todo es editable.

En las tres puedes **añadir, editar, reordenar y borrar** fichas. El orden en
la página lo controla el campo **Orden** del panel *Atributos* (los números
más bajos salen antes).

### Rescate Vertical → Equipos

Cada ficha lleva: foto (Imagen destacada), descripción (editor), **Norma**,
un **dato técnico** con su etiqueta, y dos listas clave:

- **Cuándo se usa** — sale en verde.
- **Cuándo NO se usa** — sale en rojo. Aquí van las contraindicaciones y los
  errores frecuentes.

Escribe una situación por línea, empezando con guion. Ya vienen cargadas 7
fichas, incluidas cuerda dinámica y estática con la diferencia de cuándo usar
cada una, y el mosquetón con el aviso del eje menor.

### Rescate Vertical → Técnicas y nudos

Además de foto y descripción, cada técnica admite un **vídeo**: pega la
dirección de YouTube o Vimeo en el campo *Vídeo* y se incrusta solo debajo de
la ficha. Déjalo vacío si esa técnica no tiene vídeo.

### Rescate Vertical → Protocolos

Cada protocolo lleva:

- **Imagen destacada**: aquí subes la infografía. Se muestra a lo ancho y al
  pulsarla se abre a tamaño completo.
- **Secuencia**: la sigla, por ejemplo `XABCDE` o `MARCH PAWS`.
- **Pasos del protocolo**: uno por línea con guion.

Vienen cargados PHTLS, TECC, TCCC, síndrome compartimental/aplastamiento e
integración con el equipo técnico, cada uno con su secuencia.

### Añadir texto suelto a cualquier sección

Todas las secciones muestran al final lo que escribas en el **editor de la
página** correspondiente. Sirve para ampliar apartados como *Cómo se lee* en
Física sin tocar código: edita la página, escribe, y aparece debajo.
