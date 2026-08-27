<?php
/**
 * Contenido editable desde el escritorio: Equipos, Técnicas y Protocolos.
 *
 * Antes estas tres secciones estaban escritas dentro de las plantillas del
 * tema, así que ampliarlas exigía tocar código. Ahora cada una es un tipo de
 * contenido propio: el profesor añade, edita, reordena y borra fichas desde
 * el escritorio, con su foto (o vídeo) y sus campos técnicos.
 *
 * El contenido que ya existía se siembra como entradas la primera vez, de
 * forma que nada se pierde y todo queda editable.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class RV_Content {

	const EQUIPO    = 'rv_equipo';
	const TECNICA   = 'rv_tecnica';
	const PROTOCOLO = 'rv_protocolo';

	public static function register() {
		add_action( 'init', array( __CLASS__, 'register_post_types' ) );
		add_action( 'add_meta_boxes', array( __CLASS__, 'add_meta_boxes' ) );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'admin_assets' ) );

		/*
		 * Se usan las constantes y no types(): ese metodo lleva cadenas
		 * traducibles y register() corre en plugins_loaded, antes de init.
		 * Llamarlo aqui dispararia el aviso de traduccion prematura de WP 6.7.
		 */
		foreach ( array( self::EQUIPO, self::TECNICA, self::PROTOCOLO ) as $type ) {
			add_action( 'save_post_' . $type, array( __CLASS__, 'save_meta' ), 10, 2 );
		}
	}

	/**
	 * Definición de los tres tipos y de sus campos.
	 *
	 * @return array<string,array<string,mixed>>
	 */
	public static function types() {
		return array(
			self::EQUIPO => array(
				'plural'   => __( 'Equipos', 'rescate-vertical-tools' ),
				'singular' => __( 'Equipo', 'rescate-vertical-tools' ),
				'icon'     => 'dashicons-shield-alt',
				'intro'    => __( 'Cada ficha es una pieza del equipo. La foto se pone como Imagen destacada.', 'rescate-vertical-tools' ),
				'fields'   => array(
					'rv_norma'      => array( 'label' => __( 'Norma', 'rescate-vertical-tools' ), 'type' => 'text', 'desc' => __( 'Por ejemplo: EN 892.', 'rescate-vertical-tools' ) ),
					'rv_dato'       => array( 'label' => __( 'Dato técnico', 'rescate-vertical-tools' ), 'type' => 'text', 'desc' => __( 'Por ejemplo: 22 kN de resistencia mínima.', 'rescate-vertical-tools' ) ),
					'rv_dato_label' => array( 'label' => __( 'Nombre del dato', 'rescate-vertical-tools' ), 'type' => 'text', 'desc' => __( 'Cómo se titula ese dato. Por ejemplo: Resistencia mín.', 'rescate-vertical-tools' ) ),
					'rv_cuando_si'  => array( 'label' => __( 'Cuándo se usa', 'rescate-vertical-tools' ), 'type' => 'textarea', 'desc' => __( 'Una situación por línea, empezando con guion.', 'rescate-vertical-tools' ) ),
					'rv_cuando_no'  => array( 'label' => __( 'Cuándo NO se usa', 'rescate-vertical-tools' ), 'type' => 'textarea', 'desc' => __( 'Una situación por línea, empezando con guion. Aquí van los errores frecuentes y las contraindicaciones.', 'rescate-vertical-tools' ) ),
					'rv_galeria'    => array( 'label' => __( 'Imágenes adicionales', 'rescate-vertical-tools' ), 'type' => 'gallery', 'desc' => __( 'Fotos extra que se muestran debajo de la ficha.', 'rescate-vertical-tools' ) ),
				),
			),
			self::TECNICA => array(
				'plural'   => __( 'Técnicas y nudos', 'rescate-vertical-tools' ),
				'singular' => __( 'Técnica', 'rescate-vertical-tools' ),
				'icon'     => 'dashicons-share-alt',
				'intro'    => __( 'Cada ficha es una técnica o un nudo. Puede llevar foto (Imagen destacada) y además un vídeo.', 'rescate-vertical-tools' ),
				'fields'   => array(
					'rv_etiqueta' => array( 'label' => __( 'Etiqueta de uso', 'rescate-vertical-tools' ), 'type' => 'text', 'desc' => __( 'Aparece sobre la foto. Por ejemplo: Anclaje, Autoseguro, Encordamiento.', 'rescate-vertical-tools' ) ),
					'rv_galeria'  => array( 'label' => __( 'Imágenes adicionales', 'rescate-vertical-tools' ), 'type' => 'gallery', 'desc' => __( 'Fotos extra: pasos del nudo, detalles del montaje…', 'rescate-vertical-tools' ) ),
					'rv_video'    => array( 'label' => __( 'Vídeo', 'rescate-vertical-tools' ), 'type' => 'url', 'desc' => __( 'Pega la dirección de un vídeo de YouTube o Vimeo. Se incrusta solo debajo de la ficha. Déjalo vacío si no hay vídeo.', 'rescate-vertical-tools' ) ),
				),
			),
			self::PROTOCOLO => array(
				'plural'   => __( 'Protocolos', 'rescate-vertical-tools' ),
				'singular' => __( 'Protocolo', 'rescate-vertical-tools' ),
				'icon'     => 'dashicons-heart',
				'intro'    => __( 'Cada ficha es un protocolo (PHTLS, TECC, TCCC…). La infografía se sube como Imagen destacada y se muestra a tamaño completo.', 'rescate-vertical-tools' ),
				'fields'   => array(
					'rv_sigla' => array( 'label' => __( 'Secuencia', 'rescate-vertical-tools' ), 'type' => 'text', 'desc' => __( 'Por ejemplo: XABCDE, o MARCH PAWS.', 'rescate-vertical-tools' ) ),
					'rv_pasos' => array( 'label' => __( 'Pasos del protocolo', 'rescate-vertical-tools' ), 'type' => 'textarea', 'desc' => __( 'Un paso por línea, empezando con guion. Por ejemplo: - X — Hemorragia exanguinante: control inmediato.', 'rescate-vertical-tools' ) ),
					'rv_galeria' => array( 'label' => __( 'Infografías adicionales', 'rescate-vertical-tools' ), 'type' => 'gallery', 'desc' => __( 'Además de la Imagen destacada puedes subir aquí todas las que quieras. Se muestran una debajo de otra y se abren a tamaño completo al pulsarlas.', 'rescate-vertical-tools' ) ),
				),
			),
		);
	}

	/* ------------------------------------------------------------------
	 * Registro
	 * ------------------------------------------------------------------ */

	public static function register_post_types() {
		foreach ( self::types() as $type => $def ) {
			register_post_type(
				$type,
				array(
					'labels'          => array(
						'name'          => $def['plural'],
						'singular_name' => $def['singular'],
						'menu_name'     => $def['plural'],
						/* translators: %s: nombre en singular del tipo. */
						'add_new_item'  => sprintf( __( 'Añadir %s', 'rescate-vertical-tools' ), $def['singular'] ),
						/* translators: %s: nombre en singular del tipo. */
						'edit_item'     => sprintf( __( 'Editar %s', 'rescate-vertical-tools' ), $def['singular'] ),
						'add_new'       => __( 'Añadir nuevo', 'rescate-vertical-tools' ),
						'not_found'     => __( 'Todavía no hay fichas.', 'rescate-vertical-tools' ),
					),
					'public'          => false,
					'show_ui'         => true,
					'show_in_menu'    => RV_TOOLS_MENU_SLUG,
					'capability_type' => 'post',
					'map_meta_cap'    => true,
					'supports'        => array( 'title', 'editor', 'thumbnail', 'page-attributes' ),
					'has_archive'     => false,
					'rewrite'         => false,
					'show_in_rest'    => false,
					'menu_icon'       => $def['icon'],
				)
			);
		}
	}

	public static function add_meta_boxes() {
		foreach ( self::types() as $type => $def ) {
			add_meta_box(
				'rv_campos_' . $type,
				__( 'Datos de la ficha', 'rescate-vertical-tools' ),
				array( __CLASS__, 'render_meta_box' ),
				$type,
				'normal',
				'high'
			);
		}
	}

	public static function render_meta_box( $post ) {
		$types = self::types();
		$def   = isset( $types[ $post->post_type ] ) ? $types[ $post->post_type ] : null;
		if ( ! $def ) {
			return;
		}
		wp_nonce_field( 'rv_content_meta', 'rv_content_nonce' );
		?>
		<p><?php echo esc_html( $def['intro'] ); ?></p>
		<table class="form-table" role="presentation">
			<?php foreach ( $def['fields'] as $key => $field ) : ?>
				<tr>
					<th scope="row"><label for="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $field['label'] ); ?></label></th>
					<td>
						<?php
						$value = get_post_meta( $post->ID, $key, true );
						if ( 'gallery' === $field['type'] ) :
							self::render_gallery_field( $key, $value );
						elseif ( 'textarea' === $field['type'] ) :
							?>
							<textarea id="<?php echo esc_attr( $key ); ?>" name="<?php echo esc_attr( $key ); ?>" rows="5" class="large-text"><?php echo esc_textarea( $value ); ?></textarea>
						<?php else : ?>
							<input type="<?php echo 'url' === $field['type'] ? 'url' : 'text'; ?>"
								id="<?php echo esc_attr( $key ); ?>" name="<?php echo esc_attr( $key ); ?>"
								value="<?php echo esc_attr( $value ); ?>" class="regular-text">
						<?php endif; ?>
						<?php if ( ! empty( $field['desc'] ) ) : ?>
							<p class="description"><?php echo esc_html( $field['desc'] ); ?></p>
						<?php endif; ?>
					</td>
				</tr>
			<?php endforeach; ?>
		</table>
		<p class="description">
			<?php esc_html_e( 'El orden en la página se controla con el campo "Orden" del panel Atributos (números más bajos salen antes).', 'rescate-vertical-tools' ); ?>
		</p>
		<?php
	}

	/**
	 * Guarda los campos de la ficha.
	 *
	 * @param int     $post_id ID de la ficha.
	 * @param WP_Post $post    Objeto de la ficha.
	 */
	public static function save_meta( $post_id, $post ) {
		if ( ! isset( $_POST['rv_content_nonce'] )
			|| ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['rv_content_nonce'] ) ), 'rv_content_meta' )
		) {
			return;
		}
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		$types = self::types();
		if ( ! isset( $types[ $post->post_type ] ) ) {
			return;
		}

		foreach ( $types[ $post->post_type ]['fields'] as $key => $field ) {
			if ( ! isset( $_POST[ $key ] ) ) {
				continue;
			}
			$raw = wp_unslash( $_POST[ $key ] );

			if ( 'gallery' === $field['type'] ) {
				$ids   = array_filter( array_map( 'absint', explode( ',', (string) $raw ) ) );
				$value = implode( ',', $ids );
			} elseif ( 'textarea' === $field['type'] ) {
				$value = sanitize_textarea_field( $raw );
			} elseif ( 'url' === $field['type'] ) {
				$value = esc_url_raw( $raw );
			} else {
				$value = sanitize_text_field( $raw );
			}
			update_post_meta( $post_id, $key, $value );
		}
	}


	/**
	 * Carga la biblioteca de medios en las pantallas de edición de estos tipos.
	 *
	 * @param string $hook Pantalla actual del escritorio.
	 */
	public static function admin_assets( $hook ) {
		if ( ! in_array( $hook, array( 'post.php', 'post-new.php' ), true ) ) {
			return;
		}
		$screen = get_current_screen();
		if ( ! $screen || ! in_array( $screen->post_type, array( self::EQUIPO, self::TECNICA, self::PROTOCOLO ), true ) ) {
			return;
		}

		wp_enqueue_media();
		wp_enqueue_style( 'rv-admin-gallery', RV_TOOLS_URL . 'assets/css/rv-admin.css', array(), RV_TOOLS_VERSION );
		wp_enqueue_script( 'rv-admin-gallery', RV_TOOLS_URL . 'assets/js/rv-admin.js', array( 'jquery' ), RV_TOOLS_VERSION, true );
		wp_localize_script(
			'rv-admin-gallery',
			'rvAdminGallery',
			array(
				'title'  => __( 'Elegir imágenes', 'rescate-vertical-tools' ),
				'button' => __( 'Usar estas imágenes', 'rescate-vertical-tools' ),
				'remove' => __( 'Quitar', 'rescate-vertical-tools' ),
			)
		);
	}

	/**
	 * Pinta el selector de galería: miniaturas + IDs en un campo oculto.
	 *
	 * @param string $key   Nombre del campo.
	 * @param string $value IDs separados por coma.
	 */
	private static function render_gallery_field( $key, $value ) {
		$ids = array_filter( array_map( 'absint', explode( ',', (string) $value ) ) );
		?>
		<div class="rv-gal" data-field="<?php echo esc_attr( $key ); ?>">
			<div class="rv-gal-items">
				<?php foreach ( $ids as $id ) : ?>
					<?php $src = wp_get_attachment_image_url( $id, 'thumbnail' ); ?>
					<?php if ( $src ) : ?>
						<div class="rv-gal-item" data-id="<?php echo esc_attr( $id ); ?>">
							<img src="<?php echo esc_url( $src ); ?>" alt="">
							<button type="button" class="rv-gal-quitar" aria-label="<?php esc_attr_e( 'Quitar imagen', 'rescate-vertical-tools' ); ?>">&times;</button>
						</div>
					<?php endif; ?>
				<?php endforeach; ?>
			</div>
			<p>
				<button type="button" class="button rv-gal-add"><?php esc_html_e( 'Añadir imágenes', 'rescate-vertical-tools' ); ?></button>
			</p>
			<input type="hidden" name="<?php echo esc_attr( $key ); ?>" class="rv-gal-input" value="<?php echo esc_attr( implode( ',', $ids ) ); ?>">
		</div>
		<?php
	}

	/**
	 * IDs de la galería de una ficha.
	 *
	 * @param int $post_id ID de la ficha.
	 * @return int[]
	 */
	public static function gallery_ids( $post_id ) {
		$raw = get_post_meta( $post_id, 'rv_galeria', true );
		return array_values( array_filter( array_map( 'absint', explode( ',', (string) $raw ) ) ) );
	}
	/* ------------------------------------------------------------------
	 * Lectura desde las plantillas
	 * ------------------------------------------------------------------ */

	/**
	 * Fichas publicadas de un tipo, en su orden.
	 *
	 * @param string $type Tipo de contenido.
	 * @return WP_Post[]
	 */
	public static function items( $type ) {
		return get_posts(
			array(
				'post_type'      => $type,
				'post_status'    => 'publish',
				'numberposts'    => -1,
				'orderby'        => array( 'menu_order' => 'ASC', 'date' => 'ASC' ),
				'no_found_rows'  => true,
			)
		);
	}

	/**
	 * Convierte un campo de líneas con guion en una lista <ul> segura.
	 *
	 * @param string $raw Texto crudo.
	 * @return string HTML, o cadena vacía.
	 */
	public static function lines_to_list( $raw ) {
		$raw = trim( (string) $raw );
		if ( '' === $raw ) {
			return '';
		}
		$out = '';
		foreach ( preg_split( '/\r\n|\r|\n/', $raw ) as $line ) {
			$line = trim( ltrim( trim( $line ), '-*•' ) );
			if ( '' === $line ) {
				continue;
			}
			$out .= '<li>' . esc_html( $line ) . '</li>';
		}
		return '' === $out ? '' : '<ul class="rv-lista">' . $out . '</ul>';
	}

	/**
	 * Incrusta un vídeo de YouTube o Vimeo.
	 *
	 * @param string $url Dirección del vídeo.
	 * @return string HTML del reproductor, o cadena vacía.
	 */
	public static function video_embed( $url ) {
		$url = trim( (string) $url );
		if ( '' === $url ) {
			return '';
		}
		$embed = wp_oembed_get( $url, array( 'width' => 900 ) );
		if ( ! $embed ) {
			return '';
		}
		return '<div class="rv-video">' . $embed . '</div>';
	}
}
