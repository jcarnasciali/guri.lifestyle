<?php
/**
 * The template for displaying product content in the single-product.php template
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-single-product.php.
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
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>

<div class="navglcontainer">

<div class="topbarlang">
	
		<div class="topbarinner">
	
			<ul>
			
				<li id="english">EN</li>
				<li id="french">FR</li>
				<li id="portuguese">PT</li>
			
			</ul>
			
		</div>
	
	</div>


	<div class="navgltopbar">

		<div class="navcol1">
		
			<div><img class="iconsearch" alt="Product Search Bar Icon" src="https://gurilifestyle.com/wp-content/uploads/2017/03/icon_search.png" />
                       <?php echo do_shortcode( '[aws_search_form]' ); ?></div>
		
		
		</div>
		
		<div class="navcol2">
		
			<a href="https://gurilifestyle.com/"><img class="mainlogoimg" src="https://gurilifestyle.com/wp-content/uploads/2017/03/logo_black.png" alt="Main Logo of Guri Lifestyle" /></a>
		
		</div>
		
		<div class="navcol3">
		
			<div><a href="https://gurilifestyle.com/cart/"> <img class="iconbag" src="https://gurilifestyle.com/wp-content/uploads/2017/03/icon_bag.png" alt="Shopping Bag" /> </a>
			<a href="https://gurilifestyle.com/my-account/"> <img class="iconaccount" src="https://gurilifestyle.com/wp-content/uploads/2017/03/icon_account.png" alt="My Account" /> </a></div>
		
		</div>
		
	</div>
	
	<div class="navglmenu">
	
		<ul>
		
			<li>
                        <a href="https://gurilifestyle.com/">

                             <p class="glenglish">Home</p>
			<p class="glfrench">Accueil</p>
			<p class="glportuguese">Home</p>


                        </a></li>
			
			<a href="https://gurilifestyle.com/store/"><li class="dropdowngl1">
			
				<p class="glenglish">Shop <img class="caretdwn" src="https://gurilifestyle.com/wp-content/uploads/2017/03/caret_down.png" alt="caret-down" /></p> 
				<p class="glfrench">Boutique <img class="caretdwn" src="https://gurilifestyle.com/wp-content/uploads/2017/03/caret_down.png" alt="caret-down" /></p> 
				<p class="glportuguese">Loja <img class="caretdwn" src="https://gurilifestyle.com/wp-content/uploads/2017/03/caret_down.png" alt="caret-down" /></p> 
				
				<div class="dropdown-contentgl1">
				
				  <a href="https://gurilifestyle.com/girl/" class="glenglish"><p>Girl</p></a>
				  <a href="https://gurilifestyle.com/girl/" class="glfrench"><p>Fille</p></a>
				  <a href="https://gurilifestyle.com/girl/" class="glportuguese"><p>Menina</p></a>
				  
				  
				  
				  <a href="https://gurilifestyle.com/boy/" class="glenglish"><p>Boy</p></a>
				  <a href="https://gurilifestyle.com/boy/" class="glfrench"><p>Garçon</p></a>
				  <a href="https://gurilifestyle.com/boy/" class="glportuguese"><p>Menino</p></a>
				  
				  
				  <a href="https://gurilifestyle.com/woman/" class="glenglish"><p>Woman</p></a>
				  <a href="https://gurilifestyle.com/woman/" class="glfrench"><p>Femme</p></a>
				  <a href="https://gurilifestyle.com/woman/" class="glportuguese"><p>Mulher</p></a>
				  
				</div>
				
			</li></a>
			
			<li class="dropdowngl2"> 
			
				<p class="glenglish">About <img class="caretdwn" src="https://gurilifestyle.com/wp-content/uploads/2017/03/caret_down.png" alt="caret-down" /></p>
				<p class="glfrench">&Agrave; prop&ocirc;s <img class="caretdwn" src="https://gurilifestyle.com/wp-content/uploads/2017/03/caret_down.png" alt="caret-down" /></p>
				<p class="glportuguese">Sobre <img class="caretdwn" src="https://gurilifestyle.com/wp-content/uploads/2017/03/caret_down.png" alt="caret-down" /></p>
				
				
				<div class="dropdown-contentgl2">
				
				  <a href="https://gurilifestyle.com/about-us" class="glenglish"><p>About us</p></a>
				  <a href="https://gurilifestyle.com/about-us" class="glfrench terms"><p>De nous</p></a>
				  <a href="https://gurilifestyle.com/about-us" class="glportuguese"><p>Sobre nós</p></a>
				  
				  
				  
				  
				  <a class="terms glenglish" href="https://gurilifestyle.com/faq/" ><p>FAQ</p></a>
				  <a class="terms glfrench" href="https://gurilifestyle.com/faq/" lang="fr"><p>Questions fréquentes</p></a>
				  <a class="terms glportuguese" href="https://gurilifestyle.com/faq/" lang="pt"><p>Questões Frequentes</p></a>
				  
				  
				  
				</div>
				
			</li>
			
			<li>
			
			<a href="https://gurilifestyle.com/contact">
			
			<p class="glenglish">Contact</p>
			<p class="glfrench">Contact</p>
			<p class="glportuguese">Contato</p>
			
			</a>
			
			</li>
		
		</ul>
	
	</div>

</div>

<?php
	/**
	 * woocommerce_before_single_product hook.
	 *
	 * @hooked wc_print_notices - 10
	 */
	 do_action( 'woocommerce_before_single_product' );

	 if ( post_password_required() ) {
	 	echo get_the_password_form();
	 	return;
	 }
?>

<div id="product-<?php the_ID(); ?>" <?php post_class(); ?>>

	<?php
		/**
		 * woocommerce_before_single_product_summary hook.
		 *
		 * @hooked woocommerce_show_product_sale_flash - 10
		 * @hooked woocommerce_show_product_images - 20
		 */
		do_action( 'woocommerce_before_single_product_summary' );
	?>

	<div class="summary entry-summary">

		<?php
			/**
			 * woocommerce_single_product_summary hook.
			 *
			 * @hooked woocommerce_template_single_title - 5
			 * @hooked woocommerce_template_single_rating - 10
			 * @hooked woocommerce_template_single_price - 10
			 * @hooked woocommerce_template_single_excerpt - 20
			 * @hooked woocommerce_template_single_add_to_cart - 30
			 * @hooked woocommerce_template_single_meta - 40
			 * @hooked woocommerce_template_single_sharing - 50
			 * @hooked WC_Structured_Data::generate_product_data() - 60
			 */
			do_action( 'woocommerce_single_product_summary' );
		?>

	</div><!-- .summary -->

	<?php
		/**
		 * woocommerce_after_single_product_summary hook.
		 *
		 * @hooked woocommerce_output_product_data_tabs - 10
		 * @hooked woocommerce_upsell_display - 15
		 * @hooked woocommerce_output_related_products - 20
		 */
		do_action( 'woocommerce_after_single_product_summary' );
	?>

</div><!-- #product-<?php the_ID(); ?> -->

<?php do_action( 'woocommerce_after_single_product' ); ?>
