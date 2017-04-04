<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Product Factory Class
 *
 * The WooCommerce product factory creating the right product object.
 *
 * @class 		WC_Product_Factory
 * @version		2.7.0
 * @package		WooCommerce/Classes
 * @category	Class
 * @author 		WooThemes
 */
class WC_Product_Factory {

	/**
	 * Get a product.
	 *
	 * @param mixed $product_id (default: false)
	 * @param array $deprecated Previously used to pass arguments to the factory, e.g. to force a type.
	 * @return WC_Product|bool Product object or null if the product cannot be loaded.
	 */
	public function get_product( $product_id = false, $deprecated = array() ) {
		if ( ! $product_id = $this->get_product_id( $product_id ) ) {
			return false;
		}

		$product_type = $this->get_product_type( $product_id );

		// Backwards compatibility.
		if ( ! empty( $deprecated ) ) {
			wc_deprecated_argument( 'args', '2.7', 'Passing args to the product factory is deprecated. If you need to force a type, construct the product class directly.' );

			if ( isset( $deprecated['product_type'] ) ) {
				$product_type = $this->get_classname_from_product_type( $deprecated['product_type'] );
			}
		}

		$classname = $this->get_product_classname( $product_id, $product_type );

		try {
			// Try to get from cache, otherwise create a new object,
			$product = wp_cache_get( 'product-' . $product_id, 'products' );

			if ( ! is_a( $product, 'WC_Product' ) ) {
				$product = new $classname( $product_id, $deprecated );
				wp_cache_set( 'product-' . $product_id, $product, 'products' );
			}

			return $product;
		} catch ( Exception $e ) {
			return false;
		}
	}

	/**
	 * Gets a product classname and allows filtering. Returns WC_Product_Simple if the class does not exist.
	 *
	 * @since  2.7.0
	 * @param  int    $product_id
	 * @param  string $product_type
	 * @return string
	 */
	public static function get_product_classname( $product_id, $product_type ) {
		$classname = apply_filters( 'woocommerce_product_class', self::get_classname_from_product_type( $product_type ), $product_type, 'variation' === $product_type ? 'product_variation' : 'product', $product_id );

		if ( ! $classname || ! class_exists( $classname ) ) {
			$classname = 'WC_Product_Simple';
		}

		return $classname;
	}

	/**
	 * Get the product type for a product.
	 *
	 * @since 2.7.0
	 * @param  int $product_id
	 * @return string|false
	 */
	public static function get_product_type( $product_id ) {
		// Allow the overriding of the lookup in this function. Return the product type here.
		$override = apply_filters( 'woocommerce_product_type_query', false, $product_id );
		if ( ! $override ) {
			return WC_Data_Store::load( 'product' )->get_product_type( $product_id );
		} else {
			return $override;
		}
	}

	/**
	 * Create a WC coding standards compliant class name e.g. WC_Product_Type_Class instead of WC_Product_type-class.
	 *
	 * @param  string $product_type
	 * @return string|false
	 */
	public static function get_classname_from_product_type( $product_type ) {
		return $product_type ? 'WC_Product_' . implode( '_', array_map( 'ucfirst', explode( '-', $product_type ) ) ) : false;
	}

	/**
	 * Get the product ID depending on what was passed.
	 *
	 * @since 2.7.0
	 * @param  mixed $product
	 * @return int|bool false on failure
	 */
	private function get_product_id( $product ) {
		if ( false === $product && isset( $GLOBALS['post'], $GLOBALS['post']->ID ) && 'product' === get_post_type( $GLOBALS['post']->ID ) ) {
			return $GLOBALS['post']->ID;
		} elseif ( is_numeric( $product ) ) {
			return $product;
		} elseif ( $product instanceof WC_Product ) {
			return $product->get_id();
		} elseif ( ! empty( $product->ID ) ) {
			return $product->ID;
		} else {
			return false;
		}
	}
}