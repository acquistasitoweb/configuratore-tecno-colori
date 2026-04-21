<?php

/**
 * Register content types and shared catalog rules.
 *
 * @link       https://acquistasitoweb.com
 * @since      1.0.0
 *
 * @package    Configuratore_Vernici
 * @subpackage Configuratore_Vernici/includes
 */

/**
 * Register content types and shared catalog rules.
 *
 * @since      1.0.0
 * @package    Configuratore_Vernici
 * @subpackage Configuratore_Vernici/includes
 * @author     Acquistasitoweb <info@acquistasitoweb.com>
 */
class Configuratore_Vernici_Content_Types {

	const PRODUCT_META_STATUS = '_configuratore_vernici_status';

	const QUOTE_META_STATUS = '_cv_status';

	/**
	 * Register custom post types and product taxonomies used by the configurator.
	 *
	 * @since    1.0.0
	 */
	public function register_content_types() {
		register_taxonomy(
			'cv_superficie',
			array( 'product' ),
			array(
				'labels'            => array(
					'name'                       => __( 'Superfici', 'configuratore-vernici' ),
					'singular_name'              => __( 'Superficie', 'configuratore-vernici' ),
					'search_items'               => __( 'Cerca superfici', 'configuratore-vernici' ),
					'popular_items'              => __( 'Superfici più usate', 'configuratore-vernici' ),
					'all_items'                  => __( 'Tutte le superfici', 'configuratore-vernici' ),
					'edit_item'                  => __( 'Modifica superficie', 'configuratore-vernici' ),
					'update_item'                => __( 'Aggiorna superficie', 'configuratore-vernici' ),
					'add_new_item'               => __( 'Aggiungi superficie', 'configuratore-vernici' ),
					'new_item_name'              => __( 'Nuova superficie', 'configuratore-vernici' ),
					'separate_items_with_commas' => __( 'Separa le superfici con virgole', 'configuratore-vernici' ),
					'add_or_remove_items'        => __( 'Aggiungi o rimuovi superfici', 'configuratore-vernici' ),
					'choose_from_most_used'      => __( 'Scegli tra le superfici più usate', 'configuratore-vernici' ),
					'not_found'                  => __( 'Nessuna superficie trovata', 'configuratore-vernici' ),
					'menu_name'                  => __( 'Superfici', 'configuratore-vernici' ),
				),
				'hierarchical'      => false,
				'public'            => false,
				'show_ui'           => true,
				'show_admin_column' => true,
				'show_in_rest'      => true,
				'rewrite'           => false,
			)
		);

		register_post_type(
			'cv_strumento',
			array(
				'labels'       => array(
					'name'               => __( 'Strumenti di applicazione', 'configuratore-vernici' ),
					'singular_name'      => __( 'Strumento di applicazione', 'configuratore-vernici' ),
					'add_new_item'       => __( 'Aggiungi strumento', 'configuratore-vernici' ),
					'edit_item'          => __( 'Modifica strumento', 'configuratore-vernici' ),
					'new_item'           => __( 'Nuovo strumento', 'configuratore-vernici' ),
					'view_item'          => __( 'Visualizza strumento', 'configuratore-vernici' ),
					'search_items'       => __( 'Cerca strumenti', 'configuratore-vernici' ),
					'not_found'          => __( 'Nessuno strumento trovato', 'configuratore-vernici' ),
					'menu_name'          => __( 'Strumenti', 'configuratore-vernici' ),
				),
				'public'       => false,
				'show_ui'      => true,
				'show_in_menu' => false,
				'supports'     => array( 'title', 'editor', 'excerpt' ),
				'capability_type' => 'post',
			)
		);

		register_post_type(
			'cv_preventivo',
			array(
				'labels'       => array(
					'name'          => __( 'Richieste preventivo', 'configuratore-vernici' ),
					'singular_name' => __( 'Richiesta preventivo', 'configuratore-vernici' ),
				),
				'public'       => false,
				'show_ui'      => false,
				'supports'     => array( 'title' ),
				'capability_type' => 'post',
			)
		);
	}

	/**
	 * Ensure the two parent WooCommerce product categories exist.
	 *
	 * @since    1.0.0
	 */
	public function ensure_product_parent_categories() {
		if ( ! taxonomy_exists( 'product_cat' ) ) {
			return;
		}

		foreach ( self::get_paint_parent_categories() as $category ) {
			if ( term_exists( $category['slug'], 'product_cat' ) ) {
				continue;
			}

			wp_insert_term(
				$category['name'],
				'product_cat',
				array(
					'slug' => $category['slug'],
				)
			);
		}
	}

	/**
	 * Get the configured WooCommerce parent categories.
	 *
	 * @since    1.0.0
	 * @return   array
	 */
	public static function get_paint_parent_categories() {
		return array(
			array(
				'name' => __( 'Vernici Industriali', 'configuratore-vernici' ),
				'slug' => 'vernici-industriali',
			),
			array(
				'name' => __( 'Vernici Alimentari', 'configuratore-vernici' ),
				'slug' => 'vernici-alimentari',
			),
			array(
				'name' => __( 'Vernici Murali Esterno', 'configuratore-vernici' ),
				'slug' => 'vernici-murali-esterno',
			),
			array(
				'name' => __( 'Vernici Murali Interni', 'configuratore-vernici' ),
				'slug' => 'vernici-murali-interni',
			),
		);
	}

	/**
	 * Get existing parent category IDs.
	 *
	 * @since    1.0.0
	 * @return   array
	 */
	public static function get_paint_parent_category_ids() {
		if ( ! taxonomy_exists( 'product_cat' ) ) {
			return array();
		}

		$term_ids = array();

		foreach ( self::get_paint_parent_categories() as $category ) {
			$term = get_term_by( 'slug', $category['slug'], 'product_cat' );

			if ( $term && ! is_wp_error( $term ) ) {
				$term_ids[] = (int) $term->term_id;
			}
		}

		return $term_ids;
	}

	/**
	 * Get allowed product inclusion statuses.
	 *
	 * @since    1.0.0
	 * @return   array
	 */
	public static function get_product_statuses() {
		return array(
			'auto'    => __( 'Automatico', 'configuratore-vernici' ),
			'include' => __( 'Includi sempre', 'configuratore-vernici' ),
			'exclude' => __( 'Escludi', 'configuratore-vernici' ),
		);
	}

	/**
	 * Get allowed CRM statuses for quote requests.
	 *
	 * @since    1.0.0
	 * @return   array
	 */
	public static function get_quote_statuses() {
		return array(
			'new'        => __( 'Nuovo', 'configuratore-vernici' ),
			'working'    => __( 'In lavorazione', 'configuratore-vernici' ),
			'quote_sent' => __( 'Preventivo inviato', 'configuratore-vernici' ),
			'closed'     => __( 'Chiuso', 'configuratore-vernici' ),
			'lost'       => __( 'Perso', 'configuratore-vernici' ),
		);
	}

	/**
	 * Get the CRM status for a quote request.
	 *
	 * @since    1.0.0
	 * @param    int $quote_id Quote request ID.
	 * @return   string
	 */
	public static function get_quote_status( $quote_id ) {
		$status = get_post_meta( $quote_id, self::QUOTE_META_STATUS, true );
		$valid  = array_keys( self::get_quote_statuses() );

		return in_array( $status, $valid, true ) ? $status : 'new';
	}

	/**
	 * Get the configurator status for a product.
	 *
	 * @since    1.0.0
	 * @param    int $product_id Product ID.
	 * @return   string
	 */
	public static function get_product_status( $product_id ) {
		$status = get_post_meta( $product_id, self::PRODUCT_META_STATUS, true );
		$valid  = array_keys( self::get_product_statuses() );

		return in_array( $status, $valid, true ) ? $status : 'auto';
	}

	/**
	 * Check if a product belongs to one of the configured parent categories.
	 *
	 * @since    1.0.0
	 * @param    int $product_id Product ID.
	 * @return   bool
	 */
	public static function product_is_in_configurator_category( $product_id ) {
		$parent_ids = self::get_paint_parent_category_ids();

		if ( empty( $parent_ids ) ) {
			return false;
		}

		foreach ( $parent_ids as $parent_id ) {
			if ( has_term( $parent_id, 'product_cat', $product_id ) ) {
				return true;
			}

			$children = get_term_children( $parent_id, 'product_cat' );

			if ( is_wp_error( $children ) || empty( $children ) ) {
				continue;
			}

			if ( has_term( array_map( 'intval', $children ), 'product_cat', $product_id ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Check if a product can appear in the configurator.
	 *
	 * @since    1.0.0
	 * @param    int $product_id Product ID.
	 * @return   bool
	 */
	public static function product_is_candidate( $product_id ) {
		$status = self::get_product_status( $product_id );
		$yield  = (float) str_replace( ',', '.', get_post_meta( $product_id, 'resa_litro', true ) );

		if ( $yield <= 0 || 'exclude' === $status ) {
			return false;
		}

		if ( 'include' === $status ) {
			return true;
		}

		return self::product_is_in_configurator_category( $product_id );
	}
}
