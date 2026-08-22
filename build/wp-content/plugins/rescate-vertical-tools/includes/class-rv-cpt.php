<?php
/**
 * Custom Post Type "Validaciones" — stores expert validation form entries.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class RV_CPT {

	public static function register() {
		add_action( 'init', array( __CLASS__, 'register_post_type' ) );
		add_filter( 'manage_' . RV_TOOLS_CPT . '_posts_columns', array( __CLASS__, 'columns' ) );
		add_action( 'manage_' . RV_TOOLS_CPT . '_posts_custom_column', array( __CLASS__, 'render_column' ), 10, 2 );
	}

	public static function register_post_type() {
		$labels = array(
			'name'               => __( 'Validaciones', 'rescate-vertical-tools' ),
			'singular_name'      => __( 'Validación', 'rescate-vertical-tools' ),
			'menu_name'          => __( 'Validaciones', 'rescate-vertical-tools' ),
			'all_items'          => __( 'Todas las validaciones', 'rescate-vertical-tools' ),
			'view_item'          => __( 'Ver validación', 'rescate-vertical-tools' ),
			'search_items'       => __( 'Buscar validaciones', 'rescate-vertical-tools' ),
			'not_found'          => __( 'No se encontraron validaciones.', 'rescate-vertical-tools' ),
			'not_found_in_trash' => __( 'No hay validaciones en la papelera.', 'rescate-vertical-tools' ),
		);

		register_post_type(
			RV_TOOLS_CPT,
			array(
				'labels'          => $labels,
				'public'          => false,
				'show_ui'         => true,
				'show_in_menu'    => RV_TOOLS_MENU_SLUG,
				'capability_type' => 'post',
				'map_meta_cap'    => true,
				'supports'        => array( 'title' ),
				'has_archive'     => false,
				'rewrite'         => false,
				'show_in_rest'    => false,
				'menu_icon'       => 'dashicons-clipboard',
			)
		);
	}

	/**
	 * Custom admin list table columns.
	 *
	 * @param array $columns Existing columns.
	 * @return array
	 */
	public static function columns( $columns ) {
		$new = array();
		foreach ( $columns as $key => $label ) {
			$new[ $key ] = $label;
			if ( 'title' === $key ) {
				$new['rv_experto']       = __( 'Experto', 'rescate-vertical-tools' );
				$new['rv_claridad']      = __( 'Claridad', 'rescate-vertical-tools' );
				$new['rv_utilidad']      = __( 'Utilidad', 'rescate-vertical-tools' );
				$new['rv_diseno']        = __( 'Diseño', 'rescate-vertical-tools' );
			}
		}
		return $new;
	}

	/**
	 * Renders custom column content, escaped for output.
	 *
	 * @param string $column  Column key.
	 * @param int    $post_id Post ID.
	 */
	public static function render_column( $column, $post_id ) {
		if ( ! in_array( $column, array( 'rv_experto', 'rv_claridad', 'rv_utilidad', 'rv_diseno' ), true ) ) {
			return;
		}
		$value = get_post_meta( $post_id, $column, true );
		echo esc_html( $value );
	}
}
