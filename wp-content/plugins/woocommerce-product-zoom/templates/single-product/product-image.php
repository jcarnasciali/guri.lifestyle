<?php
/**
 * Single Product Image
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

global $post, $product;
?>
<div class="images">
	<div id="wcpz-main">
	<?php
		if ( has_post_thumbnail() ) {
			$image 		  = wp_get_attachment_image_src( get_post_thumbnail_id(), "large" );
			$image_full_url   = wp_get_attachment_image_src( get_post_thumbnail_id(), "full" )[0];
			
			echo "
				<img src='{$image[0]}' data-full='{$image_full_url}' data-width='{$image[1]}' data-height='{$image[2]}' />
			";
		} else {
			echo apply_filters( 'woocommerce_single_product_image_html', sprintf( '<img src="%s" alt="%s" />', wc_placeholder_img_src(), __( 'Placeholder', 'woocommerce' ) ), $post->ID );
		}
	?>
	</div>

		<?php do_action( 'woocommerce_product_thumbnails' ); ?>
</div>
