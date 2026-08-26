<?php
/**
 * Siembra el contenido que hasta ahora vivía dentro de las plantillas del
 * tema, para que quede editable desde el escritorio sin perder nada.
 *
 * Solo se ejecuta si el tipo está vacío: si el profesor ya creó o borró
 * fichas, no se vuelve a tocar.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class RV_Content_Seed {

	public static function run() {
		self::seed( RV_Content::EQUIPO, self::equipos() );
		self::seed( RV_Content::TECNICA, self::tecnicas() );
		self::seed( RV_Content::PROTOCOLO, self::protocolos() );
	}

	/**
	 * @param string $type  Tipo de contenido.
	 * @param array  $items Fichas a crear.
	 */
	private static function seed( $type, $items ) {
		$existing = get_posts(
			array(
				'post_type'      => $type,
				'post_status'    => array( 'publish', 'draft', 'trash' ),
				'posts_per_page' => 1,
				'fields'         => 'ids',
				'no_found_rows'  => true,
			)
		);
		if ( $existing ) {
			return;
		}

		$order = 0;
		foreach ( $items as $item ) {
			$order += 10;
			$id     = wp_insert_post(
				array(
					'post_type'    => $type,
					'post_status'  => 'publish',
					'post_title'   => $item['titulo'],
					'post_content' => isset( $item['texto'] ) ? $item['texto'] : '',
					'menu_order'   => $order,
				),
				true
			);
			if ( is_wp_error( $id ) ) {
				continue;
			}
			foreach ( $item as $k => $v ) {
				if ( 0 === strpos( $k, 'rv_' ) ) {
					update_post_meta( $id, $k, $v );
				}
			}
		}
	}

	/* ------------------------------------------------------------------ */

	private static function equipos() {
		return array(
			array(
				'titulo'        => 'Cuerda dinámica',
				'texto'         => 'Absorbe energía de caída mediante elongación controlada.',
				'rv_norma'      => 'EN 892',
				'rv_dato_label' => 'Resistencia mín.',
				'rv_dato'       => '22 kN',
				'rv_cuando_si'  => "- Progresión y aseguramiento donde puede haber caída con factor mayor que cero.\n- Escalada de aproximación al punto del paciente.\n- Cualquier tramo donde el rescatista quede por encima de su anclaje.",
				'rv_cuando_no'  => "- Izado y descenso de cargas: la elongación hace el sistema esponjoso e impreciso. Ahí va cuerda estática o semiestática.\n- Líneas de vida horizontales tensadas.\n- Después de haber frenado una caída dura: se retira hasta revisarla.",
			),
			array(
				'titulo'        => 'Cuerda estática / semiestática',
				'texto'         => 'Elongación mínima: mantiene la posición y la precisión bajo carga.',
				'rv_norma'      => 'EN 1891',
				'rv_dato_label' => 'Elongación',
				'rv_dato'       => 'menos del 5 %',
				'rv_cuando_si'  => "- Descenso y izado de paciente y camilla.\n- Líneas de trabajo y de seguridad en acceso con cuerda.\n- Sistemas de polipasto, donde la elongación restaría eficiencia.",
				'rv_cuando_no'  => "- Situaciones con riesgo de caída por encima del anclaje: no absorbe energía y la fuerza de choque se dispara.\n- Como sustituto de cuerda dinámica en progresión de escalada.",
			),
			array(
				'titulo'        => 'Arnés integral',
				'texto'         => 'Distribuye la carga entre tronco, hombros y muslos.',
				'rv_norma'      => 'EN 361',
				'rv_dato_label' => 'Reduce',
				'rv_dato'       => 'síndrome de suspensión',
				'rv_cuando_si'  => "- Suspensión prolongada del paciente o del rescatista.\n- Trabajos con riesgo de caída libre, donde hace falta punto dorsal.\n- Evacuación de personas inconscientes.",
				'rv_cuando_no'  => "- No sustituye a la camilla en pacientes con sospecha de lesión espinal.\n- No se usa si presenta cortes, quemaduras o costuras dañadas: se retira del servicio.",
			),
			array(
				'titulo'        => 'Mosquetón de seguro',
				'texto'         => 'Cierre de seguro automático o de rosca en anclajes críticos.',
				'rv_norma'      => 'EN 362',
				'rv_dato_label' => 'Eje mayor',
				'rv_dato'       => '20 kN',
				'rv_cuando_si'  => "- Todo punto crítico del sistema: anclajes, punto maestro, conexión al arnés.\n- Donde la conexión vaya a quedar sin vigilancia durante la maniobra.\n- Con carga siempre en el eje mayor y el gatillo cerrado.",
				'rv_cuando_no'  => "- Nunca cargado en el eje menor ni sobre el gatillo: ahí resiste apenas 7 kN.\n- No se usa un mosquetón sin seguro en puntos vitales.\n- Se retira tras una caída fuerte o un golpe seco contra roca.",
			),
			array(
				'titulo'        => 'Descensor',
				'texto'         => 'Fricciona la cuerda para controlar la velocidad de bajada.',
				'rv_norma'      => 'EN 341',
				'rv_dato_label' => 'Tipo',
				'rv_dato'       => 'ID / RIG / ocho',
				'rv_cuando_si'  => "- Descenso controlado de rescatista o de camilla.\n- Con dispositivos autoblocantes cuando haya que soltar las manos.\n- Siempre con la mano de freno en la cuerda.",
				'rv_cuando_no'  => "- El descensor en ocho retuerce la cuerda: se evita en descensos largos con carga.\n- No se usa con diámetro de cuerda fuera del rango que marca el fabricante.",
			),
			array(
				'titulo'        => 'Casco',
				'texto'         => 'Protege contra impacto de objetos en caída durante la maniobra.',
				'rv_norma'      => 'EN 397',
				'rv_dato_label' => 'Uso',
				'rv_dato'       => 'obligatorio',
				'rv_cuando_si'  => "- Siempre, desde que se entra a la zona de trabajo.\n- También sobre el paciente durante el izado o descenso.",
				'rv_cuando_no'  => "- Nunca sin barboquejo abrochado: en una caída se sale.\n- Se retira del servicio tras cualquier impacto fuerte, aunque no se vea fisura.",
			),
			array(
				'titulo'        => 'Camilla de rescate (tipo Stokes)',
				'texto'         => 'Inmoviliza al paciente en posición horizontal o inclinada.',
				'rv_norma'      => 'Enganches certificados',
				'rv_dato_label' => 'Prioriza',
				'rv_dato'       => 'restricción de movimiento',
				'rv_cuando_si'  => "- Sospecha de lesión espinal o pélvica.\n- Paciente inconsciente o con lesiones múltiples.\n- Extracción larga donde la suspensión en arnés sería intolerable.",
				'rv_cuando_no'  => "- En pozos muy estrechos donde no pueda izarse en vertical sin quedar atrapada: hay que valorar otro sistema.\n- Nunca se iza sin bridas de suspensión certificadas y con el paciente bien fijado.",
			),
		);
	}

	private static function tecnicas() {
		return array(
			array(
				'titulo'      => 'Nudo ocho',
				'texto'       => 'Terminal de alta resistencia, fácil de inspeccionar a simple vista.',
				'rv_etiqueta' => 'Encordamiento',
			),
			array(
				'titulo'      => 'Ballestrinque',
				'texto'       => 'Fijación rápida a estructuras tubulares, ajustable bajo carga.',
				'rv_etiqueta' => 'Anclaje',
			),
			array(
				'titulo'      => 'Prusik',
				'texto'       => 'Bloqueo por fricción para ascenso controlado o seguro de emergencia.',
				'rv_etiqueta' => 'Autoseguro',
			),
			array(
				'titulo'      => 'Mariposa',
				'texto'       => 'Crea un anclaje intermedio sin usar los extremos de la cuerda.',
				'rv_etiqueta' => 'Punto intermedio',
			),
		);
	}

	private static function protocolos() {
		return array(
			array(
				'titulo'   => 'PHTLS',
				'texto'    => 'Evaluación XABCDE del paciente politraumatizado: la X de hemorragia exanguinante se atiende antes que la vía aérea. Se ejecuta mientras avanza la extracción técnica, no después de ella.',
				'rv_sigla' => 'XABCDE',
				'rv_pasos' => "- X — Exanguinante: control inmediato de hemorragias masivas.\n- A — Vía aérea con control cervical.\n- B — Ventilación y oxigenación.\n- C — Circulación y control de hemorragias no visibles.\n- D — Discapacidad neurológica (AVPU / GCS).\n- E — Exposición y control ambiental, previniendo hipotermia.",
			),
			array(
				'titulo'   => 'TECC',
				'texto'    => 'Control de hemorragias y vía aérea cuando el entorno restringe el acceso normal del equipo médico.',
				'rv_sigla' => 'MARCH PAWS',
				'rv_pasos' => "- M — Hemorragia masiva.\n- A — Vía aérea.\n- R — Respiración.\n- C — Circulación.\n- H — Hipotermia y lesión craneal.\n- P — Dolor.\n- A — Antibióticos.\n- W — Heridas.\n- S — Inmovilización.",
			),
			array(
				'titulo'   => 'TCCC',
				'texto'    => 'Equilibra la atención clínica con la seguridad operativa cuando la evacuación inmediata no es posible.',
				'rv_sigla' => 'MARCH PAWS',
				'rv_pasos' => "- Tratar primero lo que mata primero.\n- Evacuar lo antes posible.\n- Comunicar todo al equipo.\n- Mantener al paciente abrigado.\n- Reevaluar constantemente.",
			),
			array(
				'titulo'   => 'Síndrome compartimental y aplastamiento',
				'texto'    => 'Dos complicaciones propias del rescate vertical: la presión dentro de un compartimento muscular cerrado, y el daño por compresión prolongada de tejidos.',
				'rv_sigla' => 'Las 6 P',
				'rv_pasos' => "- Dolor desproporcionado que aumenta al estirar el músculo.\n- Presión: tensión profunda en el compartimento.\n- Parestesias: hormigueo o adormecimiento.\n- Palidez de la piel.\n- Parálisis o debilidad de la extremidad.\n- Pulsos distales débiles o ausentes (signo tardío).",
			),
			array(
				'titulo'   => 'Integración con el equipo técnico',
				'texto'    => 'El equipo técnico y el médico actúan en simultáneo, no en secuencia: reduce el tiempo total de exposición al riesgo.',
				'rv_sigla' => '',
				'rv_pasos' => "- Asegurar al paciente antes de cualquier intervención clínica.\n- Reevaluar tras cada cambio de posición y cada fase de izado o descenso.\n- Comunicación constante entre el equipo de cuerdas y el sanitario.",
			),
		);
	}
}
