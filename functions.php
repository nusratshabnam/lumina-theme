<?php
/**
 * Lumina Theme — Functions
 */

if ( ! defined( 'ABSPATH' ) ) exit;

define( 'LUMINA_VERSION', '1.0.0' );
define( 'LUMINA_DIR',     get_template_directory() );
define( 'LUMINA_URI',     get_template_directory_uri() );

/* =========================================================
   THEME SETUP
   ========================================================= */
function lumina_setup() {
    load_theme_textdomain( 'lumina', LUMINA_DIR . '/languages' );

    add_theme_support( 'automatic-feed-links' );
    add_theme_support( 'title-tag' );
    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'html5', [ 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ] );
    add_theme_support( 'customize-selective-refresh-widgets' );
    add_theme_support( 'responsive-embeds' );
    add_theme_support( 'custom-logo', [ 'height' => 60, 'width' => 180, 'flex-width' => true, 'flex-height' => true ] );

    // WooCommerce
    add_theme_support( 'woocommerce', [
        'thumbnail_image_width'         => 480,
        'single_image_width'            => 720,
        'product_grid'                  => [ 'default_rows' => 4, 'min_rows' => 1, 'default_columns' => 4, 'min_columns' => 2, 'max_columns' => 6 ],
    ] );
    add_theme_support( 'wc-product-gallery-zoom' );
    add_theme_support( 'wc-product-gallery-lightbox' );
    add_theme_support( 'wc-product-gallery-slider' );

    // Image sizes
    add_image_size( 'lumina-hero',    1600, 900,  true );
    add_image_size( 'lumina-product', 600,  750,  true );
    add_image_size( 'lumina-thumb',   300,  375,  true );
    add_image_size( 'lumina-square',  600,  600,  true );
    add_image_size( 'lumina-wide',    1200, 600,  true );
    add_image_size( 'lumina-cart',    120,  150,  true );

    register_nav_menus( [
        'primary'  => __( 'Primary Navigation', 'lumina' ),
        'footer-1' => __( 'Footer: Shop', 'lumina' ),
        'footer-2' => __( 'Footer: Info', 'lumina' ),
        'footer-3' => __( 'Footer: Help', 'lumina' ),
        'mobile'   => __( 'Mobile Navigation', 'lumina' ),
    ] );
}
add_action( 'after_setup_theme', 'lumina_setup' );

function lumina_content_width() { $GLOBALS['content_width'] = 1320; }
add_action( 'after_setup_theme', 'lumina_content_width', 0 );

/* =========================================================
   ENQUEUE
   ========================================================= */
function lumina_scripts() {
    wp_enqueue_style( 'lumina-style', get_stylesheet_uri(), [], LUMINA_VERSION );
    wp_enqueue_script( 'lumina-main', LUMINA_URI . '/assets/js/main.js', [], LUMINA_VERSION, true );

    wp_localize_script( 'lumina-main', 'LuminaData', [
        'ajaxUrl'     => admin_url( 'admin-ajax.php' ),
        'nonce'       => wp_create_nonce( 'lumina_nonce' ),
        'cartUrl'     => wc_get_cart_url(),
        'checkoutUrl' => wc_get_checkout_url(),
        'currency'    => get_woocommerce_currency_symbol(),
    ] );

    if ( is_singular() && comments_open() ) wp_enqueue_script( 'comment-reply' );
}
add_action( 'wp_enqueue_scripts', 'lumina_scripts' );

/* =========================================================
   WOOCOMMERCE: DECLARE COMPATIBILITY
   ========================================================= */
add_action( 'before_woocommerce_init', function() {
    if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
        \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
        \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'cart_checkout_blocks', __FILE__, true );
    }
} );

/* =========================================================
   WOOCOMMERCE: LAYOUT HOOKS
   ========================================================= */

// Remove default WC wrappers — we provide our own
remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
remove_action( 'woocommerce_after_main_content',  'woocommerce_output_content_wrapper_end', 10 );

add_action( 'woocommerce_before_main_content', 'lumina_wc_wrapper_start', 10 );
add_action( 'woocommerce_after_main_content',  'lumina_wc_wrapper_end',   10 );

function lumina_wc_wrapper_start() {
    echo '<main id="primary" class="woocommerce-main"><div class="container"><div class="section--sm">';
}
function lumina_wc_wrapper_end() {
    echo '</div></div></main>';
}

// Remove default sidebar
remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10 );

// Product loop columns
add_filter( 'loop_shop_columns', fn() => 4 );

// Products per page
add_filter( 'loop_shop_per_page', fn() => 16, 20 );

// Remove breadcrumb from default position (we render it ourselves)
remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20 );

// Replace default product image wrapper
add_filter( 'woocommerce_product_thumbnails_columns', fn() => 4 );

// Cart fragments (AJAX)
add_filter( 'woocommerce_add_to_cart_fragments', 'lumina_cart_fragments' );
function lumina_cart_fragments( $fragments ) {
    $count = WC()->cart->get_cart_contents_count();
    $fragments['.cart-count'] = '<span class="cart-count">' . ( $count ?: '' ) . '</span>';
    return $fragments;
}

// Add "Quick Add" button to loop
add_action( 'woocommerce_after_shop_loop_item', 'lumina_loop_atc_button', 5 );
function lumina_loop_atc_button() {
    // Handled in loop template override
}

// Wrap checkout fields
add_filter( 'woocommerce_checkout_fields', 'lumina_checkout_fields' );
function lumina_checkout_fields( $fields ) {
    // Ensure all fields have proper classes for our CSS
    foreach ( $fields as $section => &$section_fields ) {
        foreach ( $section_fields as $key => &$field ) {
            $field['class']         = array_merge( $field['class'] ?? [], [] );
            $field['input_class']   = array_merge( $field['input_class'] ?? [], [ 'form-input' ] );
            $field['label_class']   = array_merge( $field['label_class'] ?? [], [ 'form-label' ] );
        }
    }
    return $fields;
}

/* =========================================================
   REGISTER SIDEBARS / WIDGETS
   ========================================================= */
function lumina_widgets_init() {
    $shared = [
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h5 class="widget-title">',
        'after_title'   => '</h5>',
    ];

    register_sidebar( array_merge( $shared, [
        'name' => __( 'Shop Sidebar', 'lumina' ),
        'id'   => 'shop-sidebar',
    ] ) );

    register_sidebar( array_merge( $shared, [
        'name' => __( 'Blog Sidebar', 'lumina' ),
        'id'   => 'blog-sidebar',
    ] ) );

    register_sidebar( array_merge( $shared, [
        'name' => __( 'Footer Widget Area', 'lumina' ),
        'id'   => 'footer-widget',
    ] ) );
}
add_action( 'widgets_init', 'lumina_widgets_init' );

/* =========================================================
   CUSTOMIZER
   ========================================================= */
function lumina_customize_register( $wp_customize ) {

    // ── Store Info ──
    $wp_customize->add_section( 'lumina_store', [
        'title'    => __( 'Store Info', 'lumina' ),
        'priority' => 30,
    ] );

    $store_fields = [
        'tagline'      => [ 'label' => 'Hero Tagline',       'default' => 'Refined goods for modern living.' ],
        'sub_tagline'  => [ 'label' => 'Hero Sub-text',      'default' => 'Curated collections. Thoughtful design. Sustainable materials.' ],
        'promo_text'   => [ 'label' => 'Promo Bar Text',     'default' => 'Free shipping on orders over $75 — Use code WELCOME10 for 10% off' ],
        'promo_show'   => [ 'label' => 'Show Promo Bar',     'default' => '1', 'type' => 'checkbox' ],
        'trust_1'      => [ 'label' => 'Trust Badge 1',      'default' => 'Free Returns' ],
        'trust_2'      => [ 'label' => 'Trust Badge 2',      'default' => 'Secure Payment' ],
        'trust_3'      => [ 'label' => 'Trust Badge 3',      'default' => '2-Year Warranty' ],
    ];

    foreach ( $store_fields as $id => $args ) {
        $wp_customize->add_setting( "lumina_{$id}", [
            'default'           => $args['default'],
            'sanitize_callback' => $args['type'] ?? '' === 'checkbox' ? 'wp_validate_boolean' : 'sanitize_text_field',
        ] );
        $wp_customize->add_control( "lumina_{$id}", [
            'label'   => $args['label'],
            'section' => 'lumina_store',
            'type'    => $args['type'] ?? 'text',
        ] );
    }

    // ── Social Links ──
    $wp_customize->add_section( 'lumina_social', [
        'title'    => __( 'Social Links', 'lumina' ),
        'priority' => 35,
    ] );

    foreach ( [ 'instagram', 'facebook', 'twitter', 'pinterest', 'tiktok' ] as $platform ) {
        $wp_customize->add_setting( "lumina_{$platform}", [ 'default' => '', 'sanitize_callback' => 'esc_url_raw' ] );
        $wp_customize->add_control( "lumina_{$platform}", [
            'label'   => ucfirst( $platform ) . ' URL',
            'section' => 'lumina_social',
            'type'    => 'url',
        ] );
    }

    // ── Homepage ──
    $wp_customize->add_section( 'lumina_homepage', [
        'title'    => __( 'Homepage', 'lumina' ),
        'priority' => 40,
    ] );

    foreach ( [ 'show_categories' => [ 'label' => 'Show Category Strip', 'default' => true ], 'show_featured' => [ 'label' => 'Show Featured Products', 'default' => true ], 'show_banner' => [ 'label' => 'Show Mid Banner', 'default' => true ], 'show_new' => [ 'label' => 'Show New Arrivals', 'default' => true ] ] as $id => $args ) {
        $wp_customize->add_setting( "lumina_{$id}", [ 'default' => $args['default'], 'sanitize_callback' => 'wp_validate_boolean' ] );
        $wp_customize->add_control( "lumina_{$id}", [ 'label' => $args['label'], 'section' => 'lumina_homepage', 'type' => 'checkbox' ] );
    }

    $wp_customize->add_setting( 'lumina_featured_category', [ 'default' => '', 'sanitize_callback' => 'sanitize_text_field' ] );
    $wp_customize->add_control( 'lumina_featured_category', [
        'label'       => __( 'Featured Category Slug', 'lumina' ),
        'description' => __( 'Enter a product category slug to feature on homepage.', 'lumina' ),
        'section'     => 'lumina_homepage',
        'type'        => 'text',
    ] );
}
add_action( 'customize_register', 'lumina_customize_register' );

/* =========================================================
   HELPERS
   ========================================================= */
function lumina_option( $key, $fallback = '' ) {
    return get_theme_mod( "lumina_{$key}", $fallback );
}

function lumina_logo() {
    if ( has_custom_logo() ) {
        the_custom_logo();
    } else {
        echo '<a href="' . esc_url( home_url( '/' ) ) . '" class="site-logo" rel="home">';
        echo '<span class="site-logo__mark"><svg viewBox="0 0 14 14"><circle cx="7" cy="7" r="5"/></svg></span>';
        echo '<span class="site-logo__text">' . esc_html( get_bloginfo( 'name' ) ) . '</span>';
        echo '</a>';
    }
}

function lumina_breadcrumb() {
    if ( function_exists( 'woocommerce_breadcrumb' ) && is_woocommerce() ) {
        woocommerce_breadcrumb( [
            'wrap_before'  => '<nav class="breadcrumbs" aria-label="' . esc_attr__( 'Breadcrumb', 'lumina' ) . '">',
            'wrap_after'   => '</nav>',
            'before'       => '',
            'after'        => '',
            'delimiter'    => '<span class="sep">/</span>',
            'home'         => __( 'Home', 'lumina' ),
        ] );
        return;
    }
    echo '<nav class="breadcrumbs"><a href="' . esc_url( home_url( '/' ) ) . '">' . __( 'Home', 'lumina' ) . '</a>';
    if ( is_singular() ) echo ' <span class="sep">/</span> ' . esc_html( get_the_title() );
    echo '</nav>';
}

function lumina_stars( $rating, $max = 5 ) {
    $out = '';
    for ( $i = 1; $i <= $max; $i++ ) {
        $out .= $i <= $rating ? '★' : '☆';
    }
    return '<span class="product-card__stars" aria-label="' . esc_attr( $rating . ' out of ' . $max ) . '">' . $out . '</span>';
}

function lumina_get_product_badge( $product ) {
    if ( ! $product->is_in_stock() ) return '<span class="badge badge--out">' . __( 'Sold Out', 'lumina' ) . '</span>';
    if ( $product->is_on_sale() )    return '<span class="badge badge--sale">' . __( 'Sale', 'lumina' ) . '</span>';
    $created = get_the_date( 'U', $product->get_id() );
    if ( time() - $created < 30 * DAY_IN_SECONDS ) return '<span class="badge badge--new">' . __( 'New', 'lumina' ) . '</span>';
    return '';
}

function lumina_svg_icon( $icon ) {
    $icons = [
        'close'    => '<svg viewBox="0 0 24 24"><path d="M18 6L6 18M6 6l12 12"/></svg>',
        'search'   => '<svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>',
        'cart'     => '<svg viewBox="0 0 24 24"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 01-8 0"/></svg>',
        'user'     => '<svg viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>',
        'heart'    => '<svg viewBox="0 0 24 24"><path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z"/></svg>',
        'menu'     => '<svg viewBox="0 0 24 24"><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="18" x2="21" y2="18"/></svg>',
        'chevron'  => '<svg viewBox="0 0 24 24"><polyline points="6 9 12 15 18 9"/></svg>',
        'check'    => '<svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>',
        'truck'    => '<svg viewBox="0 0 24 24"><rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>',
        'shield'   => '<svg viewBox="0 0 24 24"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>',
        'refresh'  => '<svg viewBox="0 0 24 24"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 102.13-9.36L1 10"/></svg>',
        'grid'     => '<svg viewBox="0 0 16 16"><rect x="1" y="1" width="6" height="6" rx="1"/><rect x="9" y="1" width="6" height="6" rx="1"/><rect x="1" y="9" width="6" height="6" rx="1"/><rect x="9" y="9" width="6" height="6" rx="1"/></svg>',
        'list'     => '<svg viewBox="0 0 16 16"><rect x="1" y="2" width="14" height="4" rx="1"/><rect x="1" y="10" width="14" height="4" rx="1"/></svg>',
        'star'     => '<svg viewBox="0 0 24 24" fill="currentColor"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>',
    ];
    return isset( $icons[ $icon ] ) ? $icons[ $icon ] : '';
}

/* =========================================================
   AJAX: MINI CART
   ========================================================= */
function lumina_get_mini_cart() {
    check_ajax_referer( 'lumina_nonce', 'nonce' );
    ob_start();
    woocommerce_mini_cart();
    $cart = ob_get_clean();
    wp_send_json_success( [
        'cart'  => $cart,
        'count' => WC()->cart->get_cart_contents_count(),
        'total' => WC()->cart->get_cart_total(),
    ] );
}
add_action( 'wp_ajax_lumina_mini_cart',        'lumina_get_mini_cart' );
add_action( 'wp_ajax_nopriv_lumina_mini_cart', 'lumina_get_mini_cart' );

/* =========================================================
   AJAX: QUICK VIEW
   ========================================================= */
function lumina_quick_view() {
    check_ajax_referer( 'lumina_nonce', 'nonce' );
    $product_id = absint( $_POST['product_id'] ?? 0 );
    if ( ! $product_id ) wp_send_json_error();

    $product = wc_get_product( $product_id );
    if ( ! $product ) wp_send_json_error();

    ob_start();
    include LUMINA_DIR . '/template-parts/quick-view.php';
    wp_send_json_success( [ 'html' => ob_get_clean() ] );
}
add_action( 'wp_ajax_lumina_quick_view',        'lumina_quick_view' );
add_action( 'wp_ajax_nopriv_lumina_quick_view', 'lumina_quick_view' );

/* =========================================================
   AJAX: WISHLIST (simple session-based)
   ========================================================= */
function lumina_toggle_wishlist() {
    check_ajax_referer( 'lumina_nonce', 'nonce' );
    $product_id = absint( $_POST['product_id'] ?? 0 );
    if ( ! $product_id ) wp_send_json_error();

    if ( ! session_id() ) session_start();
    $wishlist = $_SESSION['lumina_wishlist'] ?? [];

    if ( in_array( $product_id, $wishlist ) ) {
        $wishlist = array_diff( $wishlist, [ $product_id ] );
        $action   = 'removed';
    } else {
        $wishlist[] = $product_id;
        $action = 'added';
    }

    $_SESSION['lumina_wishlist'] = array_values( $wishlist );
    wp_send_json_success( [ 'action' => $action, 'count' => count( $wishlist ) ] );
}
add_action( 'wp_ajax_lumina_wishlist',        'lumina_toggle_wishlist' );
add_action( 'wp_ajax_nopriv_lumina_wishlist', 'lumina_toggle_wishlist' );

/* =========================================================
   WOOCOMMERCE: PRODUCT LOOP TEMPLATE TAG
   ========================================================= */
function lumina_product_card( $product = null ) {
    if ( ! $product ) global $product;
    if ( ! $product ) return;
    include LUMINA_DIR . '/template-parts/product-card.php';
}

/* =========================================================
   EXCERPT
   ========================================================= */
add_filter( 'excerpt_length', fn() => 20, 999 );
add_filter( 'excerpt_more',   fn() => '&hellip;' );

/* =========================================================
   WORDPRESS CORE
   ========================================================= */
add_filter( 'wp_kses_allowed_html', function( $tags, $context ) {
    if ( $context === 'post' ) {
        $tags['svg']  = [ 'viewbox' => true, 'fill' => true, 'xmlns' => true, 'class' => true, 'aria-hidden' => true ];
        $tags['path'] = [ 'd' => true, 'stroke' => true, 'fill' => true, 'stroke-width' => true, 'stroke-linecap' => true, 'stroke-linejoin' => true ];
    }
    return $tags;
}, 10, 2 );

/* =========================================================
   WOOCOMMERCE: REMOVE UNNECESSARY ELEMENTS
   ========================================================= */
add_filter( 'woocommerce_show_page_title', '__return_false' );
remove_action( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 20 );
remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30 );
// We render our own toolbar in archive-product.php

/* =========================================================
   WOOCOMMERCE: CHECKOUT CUSTOMISATION
   ========================================================= */
add_filter( 'woocommerce_checkout_fields', function( $fields ) {
    $fields['billing']['billing_company']['priority'] = 15;
    return $fields;
} );

// Remove the "Returning customer?" login notice on checkout
add_filter( 'woocommerce_checkout_show_login_reminder', '__return_false' );
