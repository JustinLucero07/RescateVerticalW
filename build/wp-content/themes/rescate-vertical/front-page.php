<?php
/**
 * Front page — Inicio: hero con fotografía, tira de estadísticas y accesos a las secciones.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<section class="rv-hero">
	<div class="rv-hero-media">
		<img src="<?php echo esc_url( rv_img( 'hero.jpg' ) ); ?>"
			alt="<?php esc_attr_e( 'Rescatista suspendido con cuerdas sobre una quebrada durante una maniobra de rescate vertical', 'rescate-vertical' ); ?>"
			fetchpriority="high" decoding="async">
	</div>
	<div class="rv-hero-scrim"></div>

	<div class="rv-hero-inner">
		<div class="rv-hero-copy">
			<div class="rv-eyebrow rv-reveal">
				<span class="rv-eyebrow-dot" aria-hidden="true"></span>
				<span><?php esc_html_e( 'Guía técnica · Emergencias médicas', 'rescate-vertical' ); ?></span>
			</div>

			<h1 class="rv-reveal d1">
				<?php
				printf(
					/* translators: %s: the highlighted word "evacuar". */
					esc_html__( 'Acceder, asegurar y %s donde nadie más llega.', 'rescate-vertical' ),
					'<em>' . esc_html__( 'evacuar', 'rescate-vertical' ) . '</em>'
				);
				?>
			</h1>

			<p class="rv-hero-lead rv-reveal d2">
				<?php esc_html_e( 'El rescate vertical es la disciplina que permite intervenir en quebradas, alturas, pozos y estructuras de difícil acceso, cuando cada minuto de exposición cuenta tanto como cada nudo bien hecho.', 'rescate-vertical' ); ?>
			</p>

			<div class="rv-hero-actions rv-reveal d3">
				<a class="rv-btn rv-btn-primary" href="<?php echo esc_url( rv_section_url( 'fisica' ) ); ?>">
					<?php esc_html_e( 'Probar el cálculo de riesgo', 'rescate-vertical' ); ?>
				</a>
				<a class="rv-btn rv-btn-ghost" href="<?php echo esc_url( rv_section_url( 'tecnicas' ) ); ?>">
					<?php esc_html_e( 'Ver técnicas', 'rescate-vertical' ); ?>
				</a>
			</div>

			<div class="rv-hero-note rv-reveal d4">
				<svg width="17" height="17" viewBox="0 0 24 24" fill="none" aria-hidden="true" focusable="false">
					<path d="M4 12.5l5 5L20 6.5" stroke="#1B7FC4" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"/>
				</svg>
				<span>
					<?php
					printf(
						/* translators: 1: PHTLS, 2: TECC, 3: TCCC */
						esc_html__( 'Alineado a protocolos %1$s, %2$s y %3$s', 'rescate-vertical' ),
						'<strong>PHTLS</strong>',
						'<strong>TECC</strong>',
						'<strong>TCCC</strong>'
					);
					?>
				</span>
			</div>
		</div>
	</div>
</section>

<section class="rv-stats">
	<div class="rv-stats-grid">
		<div class="rv-stat rv-reveal">
			<div class="rv-stat-num">8 836</div>
			<div class="rv-stat-label"><?php esc_html_e( 'emergencias promedio por día a nivel nacional', 'rescate-vertical' ); ?></div>
		</div>
		<div class="rv-stat rv-reveal d1">
			<div class="rv-stat-num">12 kN</div>
			<div class="rv-stat-label"><?php esc_html_e( 'límite de impacto admitido por un sistema de aseguramiento', 'rescate-vertical' ); ?></div>
		</div>
		<div class="rv-stat rv-reveal d2">
			<div class="rv-stat-num">6 °C</div>
			<div class="rv-stat-label"><?php esc_html_e( 'de pérdida de calor corporal por cada 1000 m de altitud', 'rescate-vertical' ); ?></div>
		</div>
	</div>
</section>

<section class="rv-section">
	<div class="rv-shell">
		<div class="rv-split">
			<div class="rv-prose rv-reveal">
				<span class="rv-kicker rv-kicker--med"><?php esc_html_e( 'La disciplina', 'rescate-vertical' ); ?></span>
				<h2><?php esc_html_e( 'Cuando el paciente no puede caminar hacia la ambulancia', 'rescate-vertical' ); ?></h2>
				<p><?php esc_html_e( 'Es el conjunto de procedimientos con cuerdas, anclajes y equipo certificado usado para acceder, estabilizar y evacuar a una persona cuando el terreno no permite un rescate convencional: laderas escarpadas, quebradas, pozos, torres o edificios en altura.', 'rescate-vertical' ); ?></p>
				<p><?php esc_html_e( 'El equipo tiene que ir hacia él, estabilizarlo suspendido en el aire, y traerlo de vuelta sin agravar sus lesiones. Eso exige dominar tres lenguajes a la vez: la física de las cargas, la técnica de cuerdas y el protocolo clínico del paciente politraumatizado.', 'rescate-vertical' ); ?></p>
				<a class="rv-btn rv-btn-ghost" href="<?php echo esc_url( rv_section_url( 'que-es' ) ); ?>" style="margin-top:8px;">
					<?php esc_html_e( 'Leer la sección completa', 'rescate-vertical' ); ?>
				</a>
			</div>
			<div class="rv-split-media rv-reveal d2">
				<img src="<?php echo esc_url( rv_img( 'que-es.jpg' ) ); ?>"
					alt="<?php esc_attr_e( 'Quebrada profunda con paredes rocosas verticales y un río al fondo', 'rescate-vertical' ); ?>"
					loading="lazy" decoding="async" sizes="(max-width: 860px) 100vw, 45vw">
			</div>
		</div>
	</div>
</section>

<section class="rv-band">
	<div class="rv-band-media">
		<img src="<?php echo esc_url( rv_img( 'tecnicas.jpg' ) ); ?>"
			alt="<?php esc_attr_e( 'Equipo de rescate preparando sistemas de cuerdas y mosquetones antes de una maniobra', 'rescate-vertical' ); ?>"
			loading="lazy" decoding="async" sizes="100vw">
	</div>
	<div class="rv-band-inner">
		<span class="rv-kicker rv-reveal"><?php esc_html_e( 'Manual operativo', 'rescate-vertical' ); ?></span>
		<h2 class="rv-reveal d1">
			<?php
			printf(
				/* translators: %s: highlighted word "intervención". */
				esc_html__( 'Técnicas de %s', 'rescate-vertical' ),
				'<em>' . esc_html__( 'intervención', 'rescate-vertical' ) . '</em>'
			);
			?>
		</h2>
		<p class="rv-reveal d2"><?php esc_html_e( 'Procedimientos estandarizados para operaciones en entornos verticales de alto riesgo: precisión técnica y redundancia operativa en equipos de rescate y medicina prehospitalaria.', 'rescate-vertical' ); ?></p>
		<div class="rv-reveal d3" style="margin-top:26px;">
			<a class="rv-btn rv-btn-primary" href="<?php echo esc_url( rv_section_url( 'tecnicas' ) ); ?>">
				<?php esc_html_e( 'Ver los procedimientos', 'rescate-vertical' ); ?>
			</a>
		</div>
	</div>
</section>

<section class="rv-section rv-section--soft">
	<div class="rv-shell">
		<div class="rv-section-head rv-reveal">
			<span class="rv-kicker"><?php esc_html_e( 'Recorrido', 'rescate-vertical' ); ?></span>
			<h2><?php esc_html_e( 'Todo el contenido, por bloques', 'rescate-vertical' ); ?></h2>
			<p class="rv-section-intro"><?php esc_html_e( 'Cada sección combina la teoría con una herramienta práctica: un simulador, una ficha técnica o un caso para resolver.', 'rescate-vertical' ); ?></p>
		</div>

		<div class="rv-cards">
			<a class="rv-card rv-reveal" href="<?php echo esc_url( rv_section_url( 'fisica' ) ); ?>" style="text-decoration:none;">
				<span class="rv-card-figure"><?php rv_technique_diagram( 'rapel' ); ?></span>
				<span class="rv-card-body">
					<span class="rv-card-top">
						<h3><?php esc_html_e( 'Física del rescate', 'rescate-vertical' ); ?></h3>
						<span class="rv-card-num">02</span>
					</span>
					<p><?php esc_html_e( 'El factor de caída explicado con un simulador interactivo: ajusta distancia, cuerda y ángulo entre anclajes y observa cómo cambia el riesgo en tiempo real.', 'rescate-vertical' ); ?></p>
					<span class="rv-card-foot">
						<?php esc_html_e( 'Abrir simulador', 'rescate-vertical' ); ?>
						<svg width="14" height="14" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"/></svg>
					</span>
				</span>
			</a>

			<a class="rv-card rv-reveal d1" href="<?php echo esc_url( rv_section_url( 'tecnicas' ) ); ?>" style="text-decoration:none;">
				<span class="rv-card-figure"><?php rv_technique_diagram( 'anclaje' ); ?></span>
				<span class="rv-card-body">
					<span class="rv-card-top">
						<h3><?php esc_html_e( 'Técnicas y anclajes', 'rescate-vertical' ); ?></h3>
						<span class="rv-card-num">03</span>
					</span>
					<p><?php esc_html_e( 'Nudos, niveles de competencia certificados y el factor de seguridad que debe resistir todo punto de anclaje antes de cargar el sistema.', 'rescate-vertical' ); ?></p>
					<span class="rv-card-foot">
						<?php esc_html_e( 'Ver procedimiento', 'rescate-vertical' ); ?>
						<svg width="14" height="14" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"/></svg>
					</span>
				</span>
			</a>

			<a class="rv-card rv-reveal d2" href="<?php echo esc_url( rv_section_url( 'equipos' ) ); ?>" style="text-decoration:none;">
				<span class="rv-card-figure"><?php rv_technique_diagram( 'polipasto' ); ?></span>
				<span class="rv-card-body">
					<span class="rv-card-top">
						<h3><?php esc_html_e( 'Equipos certificados', 'rescate-vertical' ); ?></h3>
						<span class="rv-card-num">04</span>
					</span>
					<p><?php esc_html_e( 'Cuerdas, arneses, mosquetones y descensores con su norma EN y su resistencia mínima: la ficha técnica de cada pieza del sistema.', 'rescate-vertical' ); ?></p>
					<span class="rv-card-foot">
						<?php esc_html_e( 'Ver fichas', 'rescate-vertical' ); ?>
						<svg width="14" height="14" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"/></svg>
					</span>
				</span>
			</a>

			<a class="rv-card rv-reveal d3" href="<?php echo esc_url( rv_section_url( 'protocolos' ) ); ?>" style="text-decoration:none;">
				<span class="rv-card-figure"><?php rv_technique_diagram( 'camilla' ); ?></span>
				<span class="rv-card-body">
					<span class="rv-card-top">
						<h3><?php esc_html_e( 'Protocolos y normativas', 'rescate-vertical' ); ?></h3>
						<span class="rv-card-num">05</span>
					</span>
					<p><?php esc_html_e( 'PHTLS, TECC y TCCC aplicados durante la extracción, más el marco normativo NFPA, OSHA, ANSI, IRATA y SPRAT que respalda cada maniobra.', 'rescate-vertical' ); ?></p>
					<span class="rv-card-foot">
						<?php esc_html_e( 'Ver protocolos', 'rescate-vertical' ); ?>
						<svg width="14" height="14" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"/></svg>
					</span>
				</span>
			</a>
		</div>
	</div>
</section>

<section class="rv-section">
	<div class="rv-shell">
		<div class="rv-split">
			<div class="rv-split-media rv-reveal">
				<img src="<?php echo esc_url( rv_img( 'practicar.jpg' ) ); ?>"
					alt="<?php esc_attr_e( 'Entrenamiento de rescate con cuerdas en una estructura de prácticas', 'rescate-vertical' ); ?>"
					loading="lazy" decoding="async" sizes="(max-width: 860px) 100vw, 45vw">
			</div>
			<div class="rv-prose rv-reveal d2">
				<span class="rv-kicker"><?php esc_html_e( 'Práctica digital', 'rescate-vertical' ); ?></span>
				<h2><?php esc_html_e( 'El primer error no debería cometerse a 30 metros', 'rescate-vertical' ); ?></h2>
				<p><?php esc_html_e( 'Un entorno de simulación permite repetir un procedimiento —armar un anclaje, calcular un factor de caída, elegir un protocolo— tantas veces como haga falta, sin riesgo real para quien está aprendiendo.', 'rescate-vertical' ); ?></p>
				<p><?php esc_html_e( 'Presenta un caso propio y recibe retroalimentación inmediata sobre los riesgos técnicos y las consideraciones médicas antes de que ese razonamiento se ponga a prueba en el terreno.', 'rescate-vertical' ); ?></p>
				<a class="rv-btn rv-btn-primary" href="<?php echo esc_url( rv_section_url( 'practicar' ) ); ?>" style="margin-top:8px;">
					<?php esc_html_e( 'Presentar un caso', 'rescate-vertical' ); ?>
				</a>
			</div>
		</div>
	</div>
</section>

<?php
/*
 * Extra content only when the front page is a static page the author edits.
 * When the front page is set to "Your latest posts" the main query holds blog
 * posts, which must not be dumped under the landing sections.
 */
if ( is_page() && have_posts() ) {
	while ( have_posts() ) {
		the_post();
		if ( '' !== trim( get_the_content() ) ) {
			?>
			<section class="rv-page-content">
				<div class="rv-shell"><div class="rv-editor-content rv-reveal"><?php the_content(); ?></div></div>
			</section>
			<?php
		}
	}
}

get_footer();
