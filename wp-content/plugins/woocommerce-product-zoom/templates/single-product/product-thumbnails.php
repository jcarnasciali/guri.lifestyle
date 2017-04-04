<?php
/**
 * Single Product Thumbnails
 *
 * This template was overridden by Ildar Akhmetov & Ruslan Askarov
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see 	    https://docs.woocommerce.com/document/template-structure/
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.6.3
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $post, $product, $woocommerce;

$attachment_ids = $product->get_gallery_attachment_ids();

$max_thumbs_per_row = intval( get_option( "wcpz-max-images-per-row" ) );
if($max_thumbs_per_row === 0)
	$max_thumbs_per_row = INF;

$thumbs_per_row_cnt = 1;

#thumbs will be displayed anyway

$loop 		= 0;
$columns 	= apply_filters( 'woocommerce_product_thumbnails_columns', 3 );
?>
<div id="wcpz-thumbs" class="">
	<section><?php

		$props            = WCProductZoom::wcpz_get_product_attachment_props( get_post_thumbnail_id(), $post );
		$image 		  	  = wp_get_attachment_image_src( get_post_thumbnail_id(), "large" );
		$image_full_url   = wp_get_attachment_image_src( get_post_thumbnail_id(), "full" )[0];

		#first, repeat product image as a first thumbnail
		echo "
			<div class='wcpz-thumb-active'>
				<img src='{$image[0]}' data-full='{$image_full_url}' alt='{$props['alt']}' data-width='{$image[1]}' data-height='{$image[2]}' />
			</div>
		";

		if($thumbs_per_row_cnt === $max_thumbs_per_row) {
			echo "</section><section>";
			$thumbs_per_row_cnt = 0;
		}
		#and go through all thumbnails
	foreach ( $attachment_ids as $attachment_id ) {

		$thumbs_per_row_cnt ++;

		$classes = array( 'zoom' );

		if ( $loop === 0 || $loop % $columns === 0 ) {
			$classes[] = 'first';
		}

		if ( ( $loop + 1 ) % $columns === 0 ) {
			$classes[] = 'last';
		}

		$image_class = implode( ' ', $classes );
		$props       = WCProductZoom::wcpz_get_product_attachment_props( $attachment_id, $post );

		if ( ! $props['url'] ) {
			continue;
		}
		
		$image 		  = wp_get_attachment_image_src( $attachment_id, "large" );
		$image_full   = wp_get_attachment_image_src( $attachment_id, "full" );
			
		echo "
			<div>
				<img src='{$image[0]}' data-full='{$image_full[0]}' title='{$props['title']}' alt='{$props['alt']}' data-width='{$image[1]}' data-height='{$image[2]}' />
			</div>
		";

		if($thumbs_per_row_cnt === $max_thumbs_per_row) {
			echo "</section><section>";
			$thumbs_per_row_cnt = 0;
		}

		$loop++;
	}

?></section></div>
<?php
