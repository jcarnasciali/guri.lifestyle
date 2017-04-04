<?php

// =============================================================================
// FUNCTIONS.PHP
// -----------------------------------------------------------------------------
// Overwrite or add your own custom functions to X in this file.
// =============================================================================

// =============================================================================
// TABLE OF CONTENTS
// -----------------------------------------------------------------------------
//   01. Enqueue Parent Stylesheet
//   02. Additional Functions
// =============================================================================

// Enqueue Parent Stylesheet
// =============================================================================

add_filter( 'x_enqueue_parent_stylesheet', '__return_true' );


// Additional Functions
// =============================================================================

function product_title_gl_divider() {
	
	echo '<img class="x-img gldivider x-img-none" src="https://gurilifestyle.com/wp-content/uploads/2017/03/divider.png">';
	
}

function headline_other_products() {
	
	echo '<h3 class="h-custom-headline cs-ta-center glheadlines h3" style="color:hsl(0, 0%, 3%);"><span><p class="glenglish" style="display: block;">You might also like:</p>
	<p class="glfrench" style="display: none;">Vous aimerez peut-être aussi:</p><p class="glportuguese" style="display: none;">Você também pode gostar:</p></span></h3>';
}

function headline_descrip_title() {
	
	echo '<h3 class="h-custom-headline cs-ta-center glheadlinesdescription h3" style="color:hsl(0, 0%, 3%);"><span><p class="glenglish" style="display: block;">Description</p>
	<p class="glfrench" style="display: none;">Description</p><p class="glportuguese" style="display: none;">Descrição</p></span></h3>';
}

function disclaimer_message() {
	
	echo '<span class="glenglish">*We recommend that you go up one size above your usual shoe size.</span><span class="glfrench">*Nous vous recommandons de choisir une taille au-dessus de votre taille habituelle.</span><span class="glportuguese">*Recomendamos que se escolha um número maior que o seu tamanho habitual.</span>';
	
}

remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );

add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 6 );

remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );

add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 7 );

add_action( 'woocommerce_single_product_summary', 'disclaimer_message', 19 );

remove_action( 'woocommerce_variable_add_to_cart', 'woocommerce_variable_add_to_cart', 30 );

add_action( 'woocommerce_single_product_summary', 'woocommerce_variable_add_to_cart', 9 );

remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10 );


function woocommerce_template_product_description() {
  woocommerce_get_template( 'single-product/tabs/description.php' );
}

add_action('woocommerce_single_product_summary', create_function( '$args', 'call_user_func(\'comments_template\');'), 22);

add_action( 'woocommerce_after_single_product', 'headline_other_products', 10 );

add_action( 'woocommerce_after_single_product', 'product_title_gl_divider', 12 );

remove_action( 'woocommerce_after_single_product_summary', 'x_woocommerce_output_upsells', 21 );

add_action( 'woocommerce_after_single_product', 'x_woocommerce_output_upsells', 21 );

add_action( 'woocommerce_single_product_summary', 'x_woocommerce_output_upsells', 8 );


add_action( 'woocommerce_after_single_product', 'headline_descrip_title', 23 );

add_action( 'woocommerce_after_single_product', 'product_title_gl_divider', 24 );

add_action( 'woocommerce_after_single_product', 'woocommerce_template_product_description', 25 );

add_filter( 'woocommerce_product_single_add_to_cart_text', 'woo_custom_cart_button_text' );    // 2.1 +
 
 
function woo_custom_cart_button_text() {
        echo '<span class="glenglish">Add to Bag</span><span class="glfrench">Ajouter au Panier</span><span class="glportuguese">Adicionar &agrave; Sacola</span>';
 
}


