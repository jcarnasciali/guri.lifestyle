<?php

/**
* The plugin class 
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'WCProductZoom' ) ) :


final class WCProductZoom {

    /**
    *   The single instance of the class.
    *
    *   @static
    *   @var WCProductZoom
    */
	protected static $_instance = null;

    /** 
    *   The array of default values for JS. 
    * 
    *   @static  
    *   @var Array 
    */ 
    public static $default_settings = array( 
        'wcpz-zoom-factor' => 3.0, 
        'wcpz-zoom-width' => 1.0, 
        'wcpz-zoom-height' => 1.0, 
        'wcpz-zoom-position' => 'right', 
        'wcpz-zoom-border-width' => '1', 
        'wcpz-zoom-border-type' => 'solid', 
        'wcpz-zoom-border-color' => '#cccccc', 
        'wcpz-zoom-border' => '1px solid #cccccc', 
        'wcpz-lens-border-width' => '1', 
        'wcpz-lens-border-type' => 'solid', 
        'wcpz-lens-border-color' => '#cccccc', 
        'wcpz-lens-border' => '1px solid #cccccc',
        'wcpz-max-images-per-row' => '4',
        'wcpz-plugin-templates-priority' => 'yes',
        'wcpz-mobile-on' => 'yes',
        'wcpz-version' => '1.0.7'
    ); 

    /**
	*   Main WCProductZoom Instance.
    *
    *   Ensures only one instance of WCProductZoom is loaded or can be loaded.
    *
    *   @static
    *   @see WCPZ()
    *   @return WCProductZoom
    */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

    /**
    *   Constructor method. 
    */
    public function __construct() {
        $this->init_hooks();
        $this->detect = new Mobile_Detect_3893();
    }

    /**
    *   Register hooks, filters etc. 
    */
    private function init_hooks() {
        #do this always
        add_action( 'wp_enqueue_scripts', array( $this, 'init_js_and_css_import' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'init_js_and_css_import' ) );
        add_action( 'admin_init', array( $this, 'wcpz_version_control' ) );
        add_action( 'wp_ajax_wcpz_get_wc_variations', array( $this, 'wcpz_get_wc_variations' ) );
        add_action( 'wp_ajax_nopriv_wcpz_get_wc_variations', array( $this, 'wcpz_get_wc_variations' ) );
        add_filter( 'woocommerce_locate_template', array( $this, 'override_wc_template' ), 1, 3 );
        #do this only in admin
        if( is_admin() ) {
            add_action( "woocommerce_settings_tabs_wcpz", array( $this, "wcpz_generate_css" ) );
            add_filter( 'woocommerce_settings_tabs_array', array( $this, 'wcpz_add_woocommerce_settings_tab' ), 50 );
            add_action( 'woocommerce_settings_tabs_wcpz', array( $this, 'wcpz_settings_tab' ) );
            add_action( 'woocommerce_update_options_wcpz', array( $this, 'wcpz_update_settings' ) );
        } else {
        #and this not in admin
            add_action( "wp_footer", array( $this, "add_gallery_script_into_footer" ) );
        }
    }

    /**
    *   Include all js and css files. Depends on user device
    */
    public function init_js_and_css_import() {
        $is_secure = false;
        if ( isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' ) {
            $is_secure = true;
        }
        elseif ( !empty( $_SERVER['HTTP_X_FORWARDED_PROTO'] ) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https' || !empty( $_SERVER['HTTP_X_FORWARDED_SSL'] ) && $_SERVER['HTTP_X_FORWARDED_SSL'] == 'on' ) {
            $is_secure = true;
        }
        $REQUEST_PROTOCOL = $is_secure ? 'https' : 'http';

        /*$isMobile = preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iPad|Tablet|iris|kindle|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i',$useragent)||preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i',substr($useragent,0,4));
*/
        if( ( $this->detect->isMobile() || $this->detect->isTablet() ) && get_option( "wcpz-mobile-on" ) === "yes" ) {
            $js = array(
                "wc-product-zoom-mobile" => array( "jquery" ) 
            );
            $css_front = array(
                "wcpz-mobile"
            );
        } else {
            $js = array(
                "wc-product-zoom" => array( "jquery" ) 
            );
            $css_front = array(
                "wcpz"
            );
        }
        $js_cdn = array(
            "$REQUEST_PROTOCOL://code.jquery.com/ui/1.12.1/jquery-ui.min.js" => array( "jquery" )
        );
        $js_admin = array(
            "wcpz-admin" => array( "jquery" )
        );
        $css_admin = array(
            "wcpz-admin"
        );

        if( is_admin() ) {
            foreach( $js_admin as $key => $value ) {
                wp_enqueue_script( $key, untrailingslashit( plugin_dir_url( __FILE__ ) ) . "/inc/js/{$key}.js", $value, 0.1 );
            }
            foreach( $css_admin as $css ) {
                wp_enqueue_style( $css, untrailingslashit( plugin_dir_url( __FILE__ ) ) . "/inc/css/{$css}.css", 0.1 );
            }
        } else {
            foreach( $js as $key => $value ) {
                wp_enqueue_script( $key, untrailingslashit( plugin_dir_url( __FILE__ ) ) . "/inc/js/{$key}.js", $value, 0.1 );
                wp_localize_script( $key, 'wcpz_ajax',
                array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
            }
            foreach( $js_cdn as $key => $value ) {
                wp_enqueue_script( "", $key, $value );
            }
            foreach( $css_front as $css ) {
                wp_enqueue_style( $css, untrailingslashit( plugin_dir_url( __FILE__ ) ) . "/inc/css/{$css}.css", 0.1 );
            }
        }
    }

    /**
    *   Override the standard WC single-product templates.
    *
    *   @param  string $template 
    *           string $template_name
    *           string $template_path
    *   @return string - Template file name
    */
    public function override_wc_template( $template, $template_name, $template_path ) {
        if( ( $this->detect->isMobile() || $this->detect->isTablet() ) && get_option( "wcpz-mobile-on" ) === "no" ) {
            return $template;
        }
        #run this only for product-image and product-thumbnails
        if($template_name === "single-product/product-image.php" || $template_name === "single-product/product-thumbnails.php") {
            global $woocommerce;
            $plugin_priority = get_option( "wcpz-plugin-templates-priority" );

            $_template = $template;
            if ( ! $template_path ) 
                $template_path = $woocommerce->template_url;
        
            $plugin_path  = untrailingslashit( plugin_dir_path( __FILE__ ) )  . "/templates/";
        
            #depending on priority first look to plugin or to theme
            if( $plugin_priority === 'yes' ) {
                $template = $plugin_path . $template_name;  
                
                if( ! $template && file_exists( $template_path . $template_name ) ) {
                    $template = locate_template(
                    array(
                        $template_path . $template_name,
                        $template_name
                        )
                    );
                }
            } else {
                $template = locate_template(
                array(
                    $template_path . $template_name,
                    $template_name
                    )
                );
            
                if( ! $template && file_exists( $plugin_path . $template_name ) ) {
                    $template = $plugin_path . $template_name;
                }
            }
            
            if ( ! $template )
                $template = $_template;
        }

        return $template;
    }

    /**
    *   Add new tab to WooCommerce Settings 
    */
    public function wcpz_add_woocommerce_settings_tab( $settings_tabs ) {
        $settings_tabs['wcpz'] = __( 'WooCommerce Product Zoom', 'woocommerce-product-zoom' );
        return $settings_tabs;
    }

    /**
    *   WC show settings
    */
    public function wcpz_settings_tab() {
        woocommerce_admin_fields( $this->wcpz_get_settings() );
    }

    /**
    *   WC update settings 
    */
    public function wcpz_update_settings() {
        woocommerce_update_options( $this->wcpz_get_settings() );
    }

    /**
    *   Get settings of WooCommerce Settings WCPZ tab 
    */
    public function wcpz_get_settings() {
        $settings = array(
            'wcpz_section_title' => array(
                'name'      => __( 'General Settings', 'woocommerce-product-zoom' ),
                'type'      => 'title',
                'desc'      => '<a id="wcpz-help-link" href="http://the-croc.ru/live-documentation-woocommerce-product-zoom/" target="_blank">Need Help?</a>',
                'id'        => 'wcpz-general-section-title'
            ),
            'wcpz_zoom_factor' => array(
                'name'      => __( 'Zoom Factor', 'woocommerce-product-zoom' ),
                'type'      => 'number',
                'desc'      => '',
                'default'   => '3',
                'id'        => 'wcpz-zoom-factor'
            ),
            'wcpz_zoom_width' => array(
                'name'      => __( 'Zoom Window Width', 'woocommerce-product-zoom' ),
                'type'      => 'number',
                'desc'      => 'Multiplied by the main image width',
                'default'   => '1',
                'id'        => 'wcpz-zoom-width'
            ),
            'wcpz_zoom_height' => array(
                'name'      => __( 'Zoom Window Height', 'woocommerce-product-zoom' ),
                'type'      => 'number',
                'desc'      => 'Multiplied by the main image height',
                'default'   => '1',
                'id'        => 'wcpz-zoom-height'
            ),
            'wcpz_zoom_position' => array(
                'name'      => __( 'Zoom Window Position', 'woocommerce-product-zoom' ),
                'type'      => 'select',
                'desc'      => 'Relatively to the main image position',
                'default'   => 'right',
                'options'   => array(
                    'right'     => 'right',
                    'left'      => 'left',
                    'top'       => 'top',
                    'bottom'    => 'bottom',
                    'overlay'   => 'overlay'
                ),
                'id'        => 'wcpz-zoom-position'
            ),
            'wcpz_max_images_per_row' => array(
                'name'      => __( 'Max Images per Row', 'woocommerce-product-zoom' ),
                'type'      => 'number',
                'desc'      => '',
                'default'   => '4',
                'id'        => 'wcpz-max-images-per-row'
            ),
            'wcpz_plugin_templates_priopity' => array(
                'name'      => __( 'Plugin Templates Priority', 'woocommerce-product-zoom' ),
                'type'      => 'checkbox',
                'desc'      => 'Protect the plugin from overriding necessary templates by your theme.',
                'default'   => 'yes',
                'id'        => 'wcpz-plugin-templates-priority'
            ),
            'wcpz_mobile_on' => array(
                'name'      => __( 'Mobile Version Enabled', 'woocommerce-product-zoom' ),
                'type'      => 'checkbox',
                'desc'      => 'Enable the plugin on mobiles and tablets.',
                'default'   => 'yes',
                'id'        => 'wcpz-mobile-on'
            ),
            'wcpz_general_section_end' => array(
                 'type' => 'sectionend',
                 'id' => 'wcpz-general-section-end'
            ),
            #zoom border
            'wcpz_zoom_border_title' => array(
                'name'      => __( 'Zoom Window Border', 'woocommerce-product-zoom' ),
                'type'      => 'title',
                'desc'      => '',
                'id'        => 'wcpz-zoom-border-title'
            ),
            'wcpz_zoom_border_width' => array(
                'name'      => __( 'Border width', 'woocommerce-product-zoom' ),
                'type'      => 'number',
                'desc'      => '',
                'default'   => '3',
                'id'        => 'wcpz-zoom-border-width'
            ),
            'wcpz_zoom_border_type' => array(
                'name'      => __( 'Border type', 'woocommerce-product-zoom' ),
                'type'      => 'select',
                'desc'      => '',
                'default'   => 'solid',
                'options'   => array(
                    'none'      => 'none',
                    'hidden'    => 'hidden',
                    'dotted'    => 'dotted',
                    'dashed'    => 'dashed',
                    'solid'     => 'solid',
                    'double'    => 'double',
                    'groove'    => 'groove',
                    'ridge'     => 'ridge',
                    'inset'     => 'inset',
                    'outset'    => 'outset'
                ),
                'id'        => 'wcpz-zoom-border-type'
            ),
            'wcpz_zoom_border_color' => array(
                'name'      => __( 'Border color', 'woocommerce-product-zoom' ),
                'type'      => 'color',
                'desc'      => '',
                'default'   => '#cccccc',
                'id'        => 'wcpz-zoom-border-color'
            ),
            'wcpz_zoom_border_hidden' => array(
                'name'      => '',
                'type'      => 'text',
                'desc'      => '',
                'default'   => '3px solid #cccccc',
                'id'        => 'wcpz-zoom-border'
            ),
            'wcpz_zoom_border_section_end' => array(
                 'type' => 'sectionend',
                 'id' => 'wcpz-zoom-border-section-end'
            ),
            #lens border
            'wcpz_lens_border_title' => array(
                'name'      => __( 'Zoom Lens Border', 'woocommerce-product-zoom' ),
                'type'      => 'title',
                'desc'      => '',
                'id'        => 'wcpz-lens-border-title'
            ),
            'wcpz_lens_border_width' => array(
                'name'      => __( 'Border width', 'woocommerce-product-zoom' ),
                'type'      => 'number',
                'desc'      => '',
                'default'   => '1',
                'id'        => 'wcpz-lens-border-width'
            ),
            'wcpz_lens_border_type' => array(
                'name'      => __( 'Border type', 'woocommerce-product-zoom' ),
                'type'      => 'select',
                'desc'      => '',
                'default'   => 'solid',
                'options'   => array(
                    'none'      => 'none',
                    'hidden'    => 'hidden',
                    'dotted'    => 'dotted',
                    'dashed'    => 'dashed',
                    'solid'     => 'solid',
                    'double'    => 'double',
                    'groove'    => 'groove',
                    'ridge'     => 'ridge',
                    'inset'     => 'inset',
                    'outset'    => 'outset'
                ),
                'id'        => 'wcpz-lens-border-type'
            ),
            'wcpz_lens_border_color' => array(
                'name'      => __( 'Border color', 'woocommerce-product-zoom' ),
                'type'      => 'color',
                'desc'      => '',
                'default'   => '#cccccc',
                'id'        => 'wcpz-lens-border-color'
            ),
            'wcpz_lens_border_hidden' => array(
                'name'      => '',
                'type'      => 'text',
                'desc'      => '',
                'default'   => '1px solid #cccccc',
                'id'        => 'wcpz-lens-border'
            ),
            'wcpz_lens_border_section_end' => array(
                 'type' => 'sectionend',
                 'id' => 'wcpz-lens-border-section-end'
            ),
        );

        return $settings;
    }
    
    /**
    *   Register all plugin options. 
    */
    public static function wcpz_register_options() {
        foreach( self::$default_settings as $key => $value ) {
            if( false === get_option($key) )
                self::add_or_update_option( $key, $value );
        }
    }

    /**
    *   Register all plugin settings. 
    */
    public function wcpz_register_settings() {
        foreach( self::$default_settings as $key => $value ) {
            register_setting( 'wcpz-settings', $key );
        }
    }

    /**
    *   Add the 'manage_wcpz' capabilities for default roles. 
    */
    public static function add_wcpz_cap() {
        global $wp_roles;
        $roles = $wp_roles->get_names();
        foreach( $roles as $name => $value )  {
            $wp_roles->add_cap( $name, 'manage_wcpz' );
        }
    }

    /**
    *   Funciton that fires on plugin activation
    */
    public static function wcpz_activation() {
        self::wcpz_register_options();
        self::add_wcpz_cap();
    }

    /**
    *   Add or update option - the union of add_option() and get_option() for convenience
    */
    public static function add_or_update_option( $name, $value ) {
        $success = add_option( $name, $value, '', 'no' );

        if ( !$success ) {
            $success = update_option( $name, $value );
        }

        return $success;
    }

    /**
    *   Version control of plugin. Fires every time in admin panel.
    */
    public function wcpz_version_control() {
        if( false === get_option("wcpz-version") || get_option("wcpz-version") !== self::$default_settings["wcpz-version"] )
            foreach( self::$default_settings as $key => $value ) {
                if( false === get_option($key) ) {
                    self::add_or_update_option( $key, $value );
                    register_setting( 'wcpz-settings', $key );
                }
            }
        self::add_or_update_option( "wcpz-version", self::$default_settings["wcpz-version"] );
    }

    /**
    *   Add an ajax handler to get variations' full-size images.
    */
    public function wcpz_get_wc_variations() {
        $product_id = esc_sql( $_POST['id'] );
        $product = wc_get_product( $product_id );
        $sizes = array();
        $variations = $product->get_available_variations();
        foreach($variations as $variation) {
            $sizes[$variation["attributes"]["attribute_pa_color"]]["full"] = wp_get_attachment_image_src( get_post_thumbnail_id( $variation["variation_id"] ), "full" )[0];
            $sizes[$variation["attributes"]["attribute_pa_color"]]["large"] = wp_get_attachment_image_src( get_post_thumbnail_id( $variation["variation_id"] ), "large" )[0];
        }
        echo json_encode( $sizes );
        wp_die();
    }

    /**
    *   Add the script with gallery params into the footer.
    */
    public function add_gallery_script_into_footer() {
        global $product, $post;

        $zoom_factor = get_option( 'wcpz-zoom-factor' );
        $zoom_width = get_option( 'wcpz-zoom-width' );
        $zoom_height = get_option( 'wcpz-zoom-height' );
        $zoom_position = get_option( 'wcpz-zoom-position' );
        $zoom_border = get_option( 'wcpz-zoom-border' );
        $lens_border = get_option( 'wcpz-lens-border' );

        # a number of images per row can't be greater than images quantity
        $max_images_per_row = get_option( 'wcpz-max-images-per-row' );
        if(!is_null($product) && !is_null($post)) {
            $images_quantity = count( $product->get_gallery_attachment_ids() );
            if( has_post_thumbnail($post->ID) )
                $images_quantity ++;
            if($max_images_per_row > $images_quantity)
                $max_images_per_row = $images_quantity;
        }

        echo "
        <script>
            jQuery(document).ready(function($) {
                $('#wcpz-main').gallery({
                    thumbs: 'wcpz-thumbs',
                    zoomWidth: {$zoom_width},
                    zoomHeight: {$zoom_height},
                    zoomFactor: {$zoom_factor},
                    zoomPosition: '{$zoom_position}',
                    zoomBorder: '{$zoom_border}',
                    lensBorder: '{$lens_border}',
                    maxImagesPerRow: '{$max_images_per_row}'
                });
            });
        </script>
        ";
    }

    /**
    *   The replacing function to be compatible with earlier WC
    */
    public static function wcpz_get_product_attachment_props( $attachment_id, $product = false ) {
        $props = array(
            'title'   => '',
            'caption' => '',
            'url'     => '',
            'alt'     => '',
        );
        if ( $attachment_id ) {
            $attachment       = get_post( $attachment_id );
            $props['title']   = trim( strip_tags( $attachment->post_title ) );
            $props['caption'] = trim( strip_tags( $attachment->post_excerpt ) );
            $props['url']     = wp_get_attachment_url( $attachment_id );
            $props['alt']     = trim( strip_tags( get_post_meta( $attachment_id, '_wp_attachment_image_alt', true ) ) );

            // Alt text fallbacks
            $props['alt']     = empty( $props['alt'] ) ? $props['caption'] : $props['alt'];
            $props['alt']     = empty( $props['alt'] ) ? trim( strip_tags( $attachment->post_title ) ) : $props['alt'];
            $props['alt']     = empty( $props['alt'] ) && $product ? trim( strip_tags( get_the_title( $product->ID ) ) ) : $props['alt'];
        }
        return $props;
    }

    /**
    *   Generate css file for frontend depending on saved settings 
    *   @return css files 
    */
    public function wcpz_generate_css() {
        global $product, $post;
        $css_transition = 120;

        $zoom_factor = get_option( 'wcpz-zoom-factor' );
        $zoom_width = get_option( 'wcpz-zoom-width' );
        $zoom_height = get_option( 'wcpz-zoom-height' );
        $zoom_position = get_option( 'wcpz-zoom-position' );
        $zoom_border = get_option( 'wcpz-zoom-border' );
        $lens_border = get_option( 'wcpz-lens-border' );

        $max_images_per_row = get_option( 'wcpz-max-images-per-row' );
        if(!is_null($product) && !is_null($post)) {
            $images_quantity = count( $product->get_gallery_attachment_ids() );
            if( has_post_thumbnail($post->ID) )
                $images_quantity ++;
            if($max_images_per_row > $images_quantity)
                $max_images_per_row = $images_quantity;
        }

        #generate for desktop
        $desktop_css = "
/**
*   This css is automatically generated for desktop.
*/

.woocommerce span.onsale {
    z-index: 2;
}

p#zoom-movement-tip-fixed:before {
    content: '!';
    font-size: 44px;
    position: absolute;
    top: -2px;
    left: 0;
    line-height: 44px;
}

#zoom-tip:before {
    content: '';
    position: absolute;
    background: url(../img/product-zoom.png);
    width: 18px;
    height: 18px;
    left:-22px;
    top:0;
}

#zoom-window {
    width:0px;
    height:0px;
    position:absolute;
    z-index:9999998;
    border:{$zoom_border};
    opacity: 0;
        ";
        if($zoom_position !== "overlay") {
            $desktop_css .= "
    -webkit-box-shadow: 7px 7px 15px 1px rgba(0,0,0,0.73);
    -moz-box-shadow: 7px 7px 15px 1px rgba(0,0,0,0.73);
    box-shadow: 7px 7px 15px 1px rgba(0,0,0,0.73);
            ";
            }
                
        $desktop_css .= "
            }
        ";

        $desktop_css .= "

#zoom-tip {
    z-index:9999999;
    position:absolute;
    font-size: 12px;
    background: rgba(0, 0, 0, 0.498039);
    color:#fff;
    border-radius:3px;;
    padding:0 5px;
    display:none;
    margin:0 15px;
    cursor:none;
    -webkit-box-shadow: 4px 4px 9px -2px rgba(0,0,0,0.75);
    -moz-box-shadow: 4px 4px 9px -2px rgba(0,0,0,0.75);
    box-shadow: 4px 4px 9px -2px rgba(0,0,0,0.75);
}

#zoom-lens {
    cursor:none;
    position:absolute;
    z-index:9999999;
    background-color:rgba(256, 256, 256, 0.2);
    border:{$lens_border};
    -webkit-box-shadow: 4px 4px 9px -2px rgba(0,0,0,0.75);
    -moz-box-shadow: 4px 4px 9px -2px rgba(0,0,0,0.75);
    box-shadow: 4px 4px 9px -2px rgba(0,0,0,0.75);
    background-image: url(../img/product-zoom-minus.png);
    background-repeat: no-repeat;
    background-position: center center;
}

#wcpz-thumbs {
    text-align: center;
    padding: 0 2px;
}

#wcpz-thumbs section {
    max-width: 100%;
    margin: 0 auto;
}

#wcpz-thumbs div {
    display: inline-block
    cursor: pointer;
    transition-property: all;
    transition-timing-function: ease-in-out;
    max-height: 0px;
    max-width: 0px;
    vertical-align: middle;
    float: left;
}

#wcpz-thumbs div img {
    display: inline-block;
    transition-property: all;
    transition-timing-function: ease-in-out;
    transition-duration: {$css_transition}ms;
    max-height: calc(100% - 3px);
    max-width: calc(100% - 3px);
    vertical-align: middle;
}

#wcpz-thumbs div span {            
    display: inline-block;
    height: 100%;
    width: 0;
    vertical-align: middle;
}

#wcpz-thumbs div.wcpz-thumb-active img {
    -webkit-box-shadow: 1px 1px 2px 1px rgba(0,0,0,0.44);
    -moz-box-shadow: 1px 1px 2px 1px rgba(0,0,0,0.44);
    box-shadow: 1px 1px 2px 1px rgba(0,0,0,0.44);
}

#wcpz-thumbs div:hover {
    filter: contrast(85%);
    -o-filter: contrast(85%);
    -moz-filter: contrast(85%);
    -webkit-filter: contrast(85%);
}

#wcpz-main {
    margin-bottom: 10px;
}

#wcpz-main > img {
    margin: 0 auto;
    cursor: none;
    position: relative;
}
        ";

        #generate for mobile
        $mobile_css = "
/**
*   This css is automatically generated for mobile.
*/

.woocommerce span.onsale {
    z-index: 2;
}

p#zoom-movement-tip-fixed:before {
    content: '!';
    font-size: 40px;
    position: absolute;
    top: -2px;
    left: 0;
    line-height: 40px;
}

#zoom-window {
    width:0px;
    height:0px;
    position:fixed;
    z-index:9999998;
    border:{$zoom_border};
    opacity: 0;                
    transition-property: background-position;
    transition-timing-function: ease-in-out;
    transition-duration: {$css_transition}ms;
}

#zoom-tip {
    z-index:99999999;
    position:absolute;
    font-size: 12px;
    background: rgba(0, 0, 0, 0.498039);
    color:#fff;
    border-radius:3px;
    padding:0 5px;
    white-space: nowrap;
}

#zoom-tip-fixed {
    z-index:99999999;
    position:fixed;
    font-size: 12px;
    background: rgba(0, 0, 0, 0.498039);
    color:#fff;
    border-radius:3px;;
    padding:10px 5px;
    white-space: nowrap;
    margin: 0;
    display: none;
}

#zoom-movement-tip-fixed {
    z-index:99999999;
    position:fixed;
    font-size: 12px;
    background: rgba(103, 0, 115, 0.498039);
    color:#fff;
    border-radius:3px;
    padding:10px 5px 10px 20px;
    white-space: nowrap;
    margin: 0;
    display: none;
}

#wcpz-thumbs {
    text-align: center;
    padding: 0 2px;
}

#wcpz-thumbs section {
    max-width: 100%;
    margin: 0 auto;
}

#wcpz-thumbs div {
    display: inline-block
    cursor: pointer;
    transition-property: all;
    transition-timing-function: ease-in-out;
    max-height: 0px;
    max-width: 0px;
    vertical-align: middle;
    float: left;
}

#wcpz-thumbs div img {
    display: inline-block;
    transition-property: all;
    transition-timing-function: ease-in-out;
    transition-duration: {$css_transition}ms;
    max-height: calc(100% - 3px);
    max-width: calc(100% - 3px);
    vertical-align: middle;
}

#wcpz-thumbs div span {            
    display: inline-block;
    height: 100%;
    width: 0;
    vertical-align: middle;
}

#wcpz-thumbs div.wcpz-thumb-active img {
    -webkit-box-shadow: 1px 1px 2px 1px rgba(0,0,0,0.44);
    -moz-box-shadow: 1px 1px 2px 1px rgba(0,0,0,0.44);
    box-shadow: 1px 1px 2px 1px rgba(0,0,0,0.44);
}

#wcpz-thumbs div:hover {
    filter: contrast(85%);
    -o-filter: contrast(85%);
    -moz-filter: contrast(85%);
    -webkit-filter: contrast(85%);
}

#wcpz-main {
    margin-bottom: 10px;
}

#wcpz-main > img {
    margin: 0 auto;
    cursor: none;
    position: relative;
}
        ";

        $dc = file_put_contents( plugin_dir_path( __FILE__ ) . "inc/css/wcpz.css", $desktop_css, LOCK_EX );
        $mc = file_put_contents( plugin_dir_path( __FILE__ ) . "inc/css/wcpz-mobile.css", $mobile_css, LOCK_EX );
    }

}

endif;