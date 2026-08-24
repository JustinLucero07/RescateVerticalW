<?php
/**
 * Footer template.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
</main>

<footer class="rv-footer">
	<div class="rv-shell">
		<div class="rv-footer-grid">
			<div class="rv-footer-brand">
				<?php if ( function_exists( 'has_custom_logo' ) && has_custom_logo() ) : ?>
					<div class="rv-brand rv-brand--logo">
						<?php the_custom_logo(); ?>
						<span class="rv-brand-name"><?php bloginfo( 'name' ); ?></span>
					</div>
				<?php else : ?>
					<div class="rv-brand">
						<svg width="22" height="22" viewBox="0 0 24 24" fill="none" aria-hidden="true" focusable="false">
							<path d="M12 1.8v5.2" stroke="#FFFFFF" stroke-width="2" stroke-linecap="round"/>
							<circle cx="12" cy="10" r="3" stroke="#D9502B" stroke-width="2"/>
							<circle cx="12" cy="15.6" r="3" stroke="#D9502B" stroke-width="2"/>
							<path d="M12 18.6v3.6" stroke="#FFFFFF" stroke-width="2" stroke-linecap="round"/>
						</svg>
						<span class="rv-brand-name"><?php bloginfo( 'name' ); ?></span>
					</div>
				<?php endif; ?>
				<p><?php esc_html_e( 'Recurso educativo para formación en emergencias médicas y operaciones de rescate en altura.', 'rescate-vertical' ); ?></p>
			</div>

			<div class="rv-footer-cols">
				<div class="rv-footer-col">
					<h4><?php esc_html_e( 'Contenido', 'rescate-vertical' ); ?></h4>
					<a href="<?php echo esc_url( rv_section_url( 'que-es' ) ); ?>"><?php esc_html_e( 'Qué es el rescate vertical', 'rescate-vertical' ); ?></a>
					<a href="<?php echo esc_url( rv_section_url( 'fisica' ) ); ?>"><?php esc_html_e( 'Física del rescate', 'rescate-vertical' ); ?></a>
					<a href="<?php echo esc_url( rv_section_url( 'tecnicas' ) ); ?>"><?php esc_html_e( 'Técnicas', 'rescate-vertical' ); ?></a>
				</div>
				<div class="rv-footer-col">
					<h4><?php esc_html_e( 'Referencia', 'rescate-vertical' ); ?></h4>
					<a href="<?php echo esc_url( rv_section_url( 'equipos' ) ); ?>"><?php esc_html_e( 'Equipos certificados', 'rescate-vertical' ); ?></a>
					<a href="<?php echo esc_url( rv_section_url( 'protocolos' ) ); ?>"><?php esc_html_e( 'Protocolos y normativas', 'rescate-vertical' ); ?></a>
					<a href="<?php echo esc_url( rv_section_url( 'practicar' ) ); ?>"><?php esc_html_e( 'Practicar en digital', 'rescate-vertical' ); ?></a>
				</div>
			</div>
		</div>

		<div class="rv-footer-base">
			<?php
			printf(
				/* translators: 1: año actual, 2: nombre del sitio. */
				esc_html__( '© %1$s %2$s — Recurso educativo para formación en emergencias médicas.', 'rescate-vertical' ),
				esc_html( gmdate( 'Y' ) ),
				esc_html( get_bloginfo( 'name' ) )
			);
			?>
		</div>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
