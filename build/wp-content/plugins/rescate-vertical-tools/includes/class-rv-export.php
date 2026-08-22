<?php
/**
 * "Exportar CSV" admin page for the Validaciones CPT.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class RV_Export {

	const ACTION = 'rv_export_validaciones';

	public static function register() {
		add_action( 'admin_menu', array( __CLASS__, 'add_menu' ) );
		add_action( 'admin_init', array( __CLASS__, 'maybe_export' ) );
	}

	public static function add_menu() {
		add_submenu_page(
			RV_TOOLS_MENU_SLUG,
			__( 'Exportar validaciones', 'rescate-vertical-tools' ),
			__( 'Exportar CSV', 'rescate-vertical-tools' ),
			'manage_options',
			'rv-export-csv',
			array( __CLASS__, 'render_page' )
		);
	}

	public static function render_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		$count = wp_count_posts( RV_TOOLS_CPT );
		$total = isset( $count->publish ) ? (int) $count->publish : 0;
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'Exportar validaciones', 'rescate-vertical-tools' ); ?></h1>
			<p>
				<?php
				printf(
					/* translators: %d: number of stored validation entries */
					esc_html( _n( 'Hay %d validación registrada.', 'Hay %d validaciones registradas.', $total, 'rescate-vertical-tools' ) ),
					(int) $total
				);
				?>
			</p>
			<form method="post">
				<?php wp_nonce_field( self::ACTION, self::ACTION . '_nonce' ); ?>
				<input type="hidden" name="rv_do_export" value="1">
				<?php submit_button( __( 'Descargar CSV', 'rescate-vertical-tools' ) ); ?>
			</form>
		</div>
		<?php
	}

	public static function maybe_export() {
		if ( ! isset( $_POST['rv_do_export'] ) ) {
			return;
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'No tienes permiso para exportar estos datos.', 'rescate-vertical-tools' ) );
		}

		check_admin_referer( self::ACTION, self::ACTION . '_nonce' );

		$posts = get_posts(
			array(
				'post_type'      => RV_TOOLS_CPT,
				'post_status'    => 'publish',
				'numberposts'    => -1,
				'orderby'        => 'date',
				'order'          => 'DESC',
			)
		);

		nocache_headers();
		header( 'Content-Type: text/csv; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename=validaciones-rescate-vertical-' . gmdate( 'Y-m-d' ) . '.csv' );

		$out = fopen( 'php://output', 'w' );
		fwrite( $out, "\xEF\xBB\xBF" ); // UTF-8 BOM so Excel renders accented characters correctly.
		fputcsv( $out, array( 'ID', 'Fecha', 'Experto', 'Claridad', 'Utilidad', 'Diseño', 'Observaciones' ) );

		foreach ( $posts as $post ) {
			fputcsv(
				$out,
				array(
					$post->ID,
					get_the_date( 'Y-m-d H:i', $post ),
					get_post_meta( $post->ID, 'rv_experto', true ),
					get_post_meta( $post->ID, 'rv_claridad', true ),
					get_post_meta( $post->ID, 'rv_utilidad', true ),
					get_post_meta( $post->ID, 'rv_diseno', true ),
					get_post_meta( $post->ID, 'rv_observaciones', true ),
				)
			);
		}

		fclose( $out );
		exit;
	}
}
