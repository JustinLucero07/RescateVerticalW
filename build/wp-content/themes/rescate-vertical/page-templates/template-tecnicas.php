<?php
/**
 * Template Name: Sección · Técnicas
 * Template Post Type: page
 *
 * Todas las fichas se editan en Rescate Vertical → Técnicas y nudos. Cada una
 * elige en qué bloque sale (nudo o sistema) con el campo "Tipo de ficha", y
 * admite foto, esquema, material, paso a paso, errores, galería y vídeos.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

rv_page_hero(
	array(
		'kicker' => __( 'Sección 03 · Manual operativo', 'rescate-vertical' ),
		'title'  => sprintf(
			/* translators: %s: highlighted word. */
			esc_html__( 'Técnicas de %s', 'rescate-vertical' ),
			'<em>' . esc_html__( 'intervención', 'rescate-vertical' ) . '</em>'
		),
		'intro'  => __( 'Procedimientos estandarizados para operaciones en entornos verticales de alto riesgo: precisión técnica y redundancia operativa.', 'rescate-vertical' ),
		'image'  => 'tecnicas.jpg',
		'alt'    => __( 'Equipo de rescate preparando sistemas de cuerdas y mosquetones', 'rescate-vertical' ),
	)
);

$rv_tecnicas = class_exists( 'RV_Content' ) ? RV_Content::items( RV_Content::TECNICA ) : array();

/*
 * Se reparten en dos bloques según el campo "Tipo de ficha". Las fichas
 * antiguas, creadas antes de que existiera ese campo, se tratan como nudos.
 */
$rv_grupos = array(
	'sistema' => array(),
	'nudo'    => array(),
);
foreach ( $rv_tecnicas as $rv_t ) {
	$rv_g = get_post_meta( $rv_t->ID, 'rv_grupo', true );
	$rv_grupos[ 'sistema' === $rv_g ? 'sistema' : 'nudo' ][] = $rv_t;
}

if ( ! function_exists( 'rv_render_tecnica' ) ) :
	/**
	 * Pinta una ficha completa de técnica.
	 *
	 * @param WP_Post $tecnica Ficha a mostrar.
	 * @param int     $indice  Posición, solo para escalonar la animación.
	 */
	function rv_render_tecnica( $tecnica, $indice ) {
	$tag      = get_post_meta( $tecnica->ID, 'rv_etiqueta', true );
	$esquema  = get_post_meta( $tecnica->ID, 'rv_esquema', true );
	$material = RV_Content::lines_to_list( get_post_meta( $tecnica->ID, 'rv_material', true ) );
	$pasos    = RV_Content::lines_to_steps( get_post_meta( $tecnica->ID, 'rv_pasos', true ) );
	$errores  = RV_Content::lines_to_list( get_post_meta( $tecnica->ID, 'rv_errores', true ) );
	$galeria  = RV_Content::gallery_ids( $tecnica->ID );
	$videos   = RV_Content::videos_embed( get_post_meta( $tecnica->ID, 'rv_video', true ) );
	$delay    = ( $indice % 3 ) > 0 ? ' d' . ( $indice % 3 ) : '';
	// Sin foto ni esquema el texto ocupa todo el ancho, para no dejar un hueco.
	$con_media = has_post_thumbnail( $tecnica ) || $esquema;
	?>
	<article class="rv-tec rv-reveal<?php echo esc_attr( $delay ); ?>">
		<div class="rv-tec-head<?php echo $con_media ? '' : ' rv-tec-head--solo'; ?>">
			<?php if ( has_post_thumbnail( $tecnica ) ) : ?>
				<div class="rv-tec-media">
					<?php echo get_the_post_thumbnail( $tecnica, 'large', array( 'loading' => 'lazy', 'decoding' => 'async' ) ); ?>
				</div>
			<?php elseif ( $esquema ) : ?>
				<div class="rv-tec-media rv-tec-media--svg">
					<?php rv_technique_diagram( $esquema ); ?>
				</div>
			<?php endif; ?>

			<div class="rv-tec-intro">
				<?php if ( $tag ) : ?>
					<span class="rv-tec-tag"><?php echo esc_html( $tag ); ?></span>
				<?php endif; ?>
				<h3><?php echo esc_html( get_the_title( $tecnica ) ); ?></h3>
				<?php if ( trim( $tecnica->post_content ) !== '' ) : ?>
					<?php echo wp_kses_post( wpautop( $tecnica->post_content ) ); ?>
				<?php endif; ?>
			</div>
		</div>

		<?php if ( $material || $pasos ) : ?>
			<div class="rv-tec-cols">
				<?php if ( $material ) : ?>
					<div class="rv-tec-mat">
						<h4><?php esc_html_e( 'Material necesario', 'rescate-vertical' ); ?></h4>
						<?php echo wp_kses_post( $material ); ?>
					</div>
				<?php endif; ?>

				<?php if ( $pasos ) : ?>
					<div class="rv-tec-pasos">
						<h4><?php esc_html_e( 'Cómo se elabora, paso a paso', 'rescate-vertical' ); ?></h4>
						<?php echo wp_kses_post( $pasos ); ?>
					</div>
				<?php endif; ?>
			</div>
		<?php endif; ?>

		<?php if ( $errores ) : ?>
			<div class="rv-tec-errores">
				<h4><?php esc_html_e( 'Errores frecuentes', 'rescate-vertical' ); ?></h4>
				<?php echo wp_kses_post( $errores ); ?>
			</div>
		<?php endif; ?>

		<?php if ( $galeria ) : ?>
			<div class="rv-tec-fotos">
				<h4><?php esc_html_e( 'Paso a paso en imágenes', 'rescate-vertical' ); ?></h4>
				<div class="rv-galeria rv-galeria--mini">
					<?php
					$rv_n = 0;
					foreach ( $galeria as $rv_gid ) :
						$rv_n++;
						?>
						<a href="<?php echo esc_url( wp_get_attachment_image_url( $rv_gid, 'full' ) ); ?>" target="_blank" rel="noopener">
							<?php echo wp_get_attachment_image( $rv_gid, 'medium', false, array( 'loading' => 'lazy', 'decoding' => 'async' ) ); ?>
							<span class="rv-galeria-num"><?php echo esc_html( $rv_n ); ?></span>
						</a>
					<?php endforeach; ?>
				</div>
			</div>
		<?php endif; ?>

		<?php if ( $videos ) : ?>
			<div class="rv-tec-videos">
				<h4><?php esc_html_e( 'Vídeo de la maniobra', 'rescate-vertical' ); ?></h4>
				<?php echo wp_kses_post( $videos ); ?>
			</div>
		<?php endif; ?>
	</article>
		<?php
	}
endif;
?>

<section class="rv-section">
	<div class="rv-shell">
		<div class="rv-section-head rv-reveal">
			<span class="rv-kicker"><?php esc_html_e( 'Sistemas y maniobras', 'rescate-vertical' ); ?></span>
			<h2><?php esc_html_e( 'Cómo se monta cada sistema', 'rescate-vertical' ); ?></h2>
			<p class="rv-section-intro"><?php esc_html_e( 'Cada procedimiento se apoya en el anterior: sin un anclaje válido no hay descenso seguro, y sin un empaquetamiento correcto la extracción puede agravar la lesión. Sigue el orden de los pasos y comprueba cada punto antes de cargar el sistema.', 'rescate-vertical' ); ?></p>
		</div>

		<?php if ( $rv_grupos['sistema'] ) : ?>
			<div class="rv-tecs">
				<?php
				$rv_i = 0;
				foreach ( $rv_grupos['sistema'] as $rv_t ) {
					rv_render_tecnica( $rv_t, $rv_i );
					$rv_i++;
				}
				?>
			</div>
		<?php else : ?>
			<p class="rv-section-intro"><?php esc_html_e( 'Todavía no hay sistemas cargados. Añádelos en Rescate Vertical → Técnicas y nudos, marcando el tipo de ficha como "Sistema o maniobra".', 'rescate-vertical' ); ?></p>
		<?php endif; ?>
	</div>
</section>

<section class="rv-section rv-section--soft">
	<div class="rv-shell">
		<div class="rv-section-head rv-reveal">
			<span class="rv-kicker"><?php esc_html_e( 'Nudos', 'rescate-vertical' ); ?></span>
			<h2><?php esc_html_e( 'Nudos que sostienen una vida', 'rescate-vertical' ); ?></h2>
			<p class="rv-section-intro">
				<?php
				printf(
					/* translators: 1: concientización, 2: operaciones, 3: técnico */
					esc_html__( 'Antes de ejecutar cualquier maniobra real, un rescatista avanza por niveles certificados: %1$s, %2$s y %3$s. Casi 6 de cada 10 lesiones registradas en personal de rescate en montaña ocurren durante el entrenamiento, no en operaciones reales.', 'rescate-vertical' ),
					'<strong>' . esc_html__( 'concientización', 'rescate-vertical' ) . '</strong>',
					'<strong>' . esc_html__( 'operaciones', 'rescate-vertical' ) . '</strong>',
					'<strong>' . esc_html__( 'técnico', 'rescate-vertical' ) . '</strong>'
				);
				?>
			</p>
		</div>

		<?php if ( $rv_grupos['nudo'] ) : ?>
			<div class="rv-tecs">
				<?php
				$rv_i = 0;
				foreach ( $rv_grupos['nudo'] as $rv_t ) {
					rv_render_tecnica( $rv_t, $rv_i );
					$rv_i++;
				}
				?>
			</div>
		<?php else : ?>
			<p class="rv-section-intro"><?php esc_html_e( 'Todavía no hay nudos cargados. Añádelos en Rescate Vertical → Técnicas y nudos.', 'rescate-vertical' ); ?></p>
		<?php endif; ?>

		<div class="rv-callout rv-reveal" style="margin-top:32px;max-width:760px;">
			<p class="rv-callout-k"><?php esc_html_e( 'Factor de seguridad del anclaje', 'rescate-vertical' ); ?></p>
			<p>
				<?php
				printf(
					/* translators: 1: 10 kN estáticos, 2: 6 kN dinámicos, 3: 10:1 */
					esc_html__( 'Todo punto de anclaje debe resistir al menos %1$s o %2$s, con un factor de seguridad mínimo de %3$s frente a la carga máxima esperada.', 'rescate-vertical' ),
					'<strong>' . esc_html__( '10 kN estáticos', 'rescate-vertical' ) . '</strong>',
					'<strong>' . esc_html__( '6 kN dinámicos', 'rescate-vertical' ) . '</strong>',
					'<strong>10:1</strong>'
				);
				?>
			</p>
		</div>

		<?php rv_editor_content(); ?>
	</div>
</section>

<?php get_footer(); ?>
