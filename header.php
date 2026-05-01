<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="sr-only" href="#primary"><?php esc_html_e( 'Skip to content', 'lumina' ); ?></a>

<?php if ( lumina_option( 'promo_show', '1' ) && lumina_option( 'promo_text' ) ) : ?>
<div class="promo-bar" role="banner">
    <?php echo esc_html( lumina_option( 'promo_text', 'Free shipping on orders over $75' ) ); ?>
</div>
<?php endif; ?>

<header id="masthead" class="site-header" role="banner">
    <div class="container">
        <div class="header-inner">

            <!-- Mobile Toggle -->
            <button class="mobile-nav-toggle" id="mobileNavToggle" aria-expanded="false" aria-controls="mobileNav" aria-label="<?php esc_attr_e( 'Open menu', 'lumina' ); ?>">
                <?php echo lumina_svg_icon( 'menu' ); ?>
            </button>

            <!-- Logo -->
            <?php lumina_logo(); ?>

            <!-- Primary Nav -->
            <nav class="primary-nav" role="navigation" aria-label="<?php esc_attr_e( 'Primary', 'lumina' ); ?>">
                <?php wp_nav_menu( [
                    'theme_location' => 'primary',
                    'container'      => false,
                    'menu_id'        => 'primary-menu',
                    'fallback_cb'    => function() {
                        echo '<ul>';
                        $items = [
                            __( 'Shop', 'lumina' )     => wc_get_page_permalink( 'shop' ),
                            __( 'New In', 'lumina' )   => wc_get_page_permalink( 'shop' ) . '?orderby=date',
                            __( 'About', 'lumina' )    => home_url( '/about' ),
                            __( 'Contact', 'lumina' )  => home_url( '/contact' ),
                        ];
                        foreach ( $items as $label => $url ) {
                            echo '<li><a href="' . esc_url( $url ) . '">' . esc_html( $label ) . '</a></li>';
                        }
                        echo '</ul>';
                    },
                ] ); ?>
            </nav>

            <!-- Header Actions -->
            <div class="header-actions">
                <button class="header-icon-btn" id="searchToggle" aria-label="<?php esc_attr_e( 'Search', 'lumina' ); ?>">
                    <?php echo lumina_svg_icon( 'search' ); ?>
                </button>

                <?php if ( is_user_logged_in() ) : ?>
                <a href="<?php echo esc_url( wc_get_account_endpoint_url( 'dashboard' ) ); ?>" class="header-icon-btn" aria-label="<?php esc_attr_e( 'My Account', 'lumina' ); ?>">
                    <?php echo lumina_svg_icon( 'user' ); ?>
                </a>
                <?php else : ?>
                <a href="<?php echo esc_url( wc_get_account_permalink() ); ?>" class="header-icon-btn" aria-label="<?php esc_attr_e( 'Login', 'lumina' ); ?>">
                    <?php echo lumina_svg_icon( 'user' ); ?>
                </a>
                <?php endif; ?>

                <button class="header-icon-btn" id="cartDrawerToggle" aria-label="<?php esc_attr_e( 'Cart', 'lumina' ); ?>" aria-controls="cartDrawer">
                    <?php echo lumina_svg_icon( 'cart' ); ?>
                    <span class="cart-count"><?php echo WC()->cart ? WC()->cart->get_cart_contents_count() ?: '' : ''; ?></span>
                </button>
            </div>

        </div>
    </div>
</header>

<!-- Mobile Nav -->
<nav class="mobile-nav" id="mobileNav" aria-label="<?php esc_attr_e( 'Mobile Navigation', 'lumina' ); ?>">
    <button class="mobile-nav-close" id="mobileNavClose" aria-label="<?php esc_attr_e( 'Close menu', 'lumina' ); ?>">
        <?php echo lumina_svg_icon( 'close' ); ?>
    </button>
    <?php wp_nav_menu( [
        'theme_location' => 'primary',
        'container'      => false,
        'fallback_cb'    => function() {
            echo '<ul>';
            $items = [ __( 'Shop', 'lumina' ) => wc_get_page_permalink( 'shop' ), __( 'New In', 'lumina' ) => home_url( '/?orderby=date' ), __( 'About', 'lumina' ) => home_url( '/about' ), __( 'Contact', 'lumina' ) => home_url( '/contact' ) ];
            foreach ( $items as $label => $url ) echo '<li><a href="' . esc_url( $url ) . '">' . esc_html( $label ) . '</a></li>';
            echo '</ul>';
        },
    ] ); ?>
</nav>

<!-- Search Overlay -->
<div class="search-overlay" id="searchOverlay" role="search">
    <button class="search-overlay__close" id="searchClose" aria-label="<?php esc_attr_e( 'Close search', 'lumina' ); ?>">&#x2715;</button>
    <div class="search-overlay__inner">
        <?php get_search_form(); ?>
    </div>
</div>

<!-- Cart Drawer -->
<div class="cart-drawer-overlay" id="cartOverlay"></div>
<aside class="cart-drawer" id="cartDrawer" aria-label="<?php esc_attr_e( 'Shopping cart', 'lumina' ); ?>">
    <div class="cart-drawer__head">
        <h2 class="cart-drawer__title">
            <?php esc_html_e( 'Your Cart', 'lumina' ); ?>
            <?php if ( WC()->cart ) : ?>
            <span style="font-family:var(--font-mono);font-size:0.7rem;color:var(--stone-400);font-weight:400;margin-left:0.5rem;">(<?php echo WC()->cart->get_cart_contents_count(); ?>)</span>
            <?php endif; ?>
        </h2>
        <button class="cart-drawer__close" id="cartDrawerClose" aria-label="<?php esc_attr_e( 'Close cart', 'lumina' ); ?>">
            <?php echo lumina_svg_icon( 'close' ); ?>
        </button>
    </div>
    <div class="cart-drawer__items" id="cartDrawerItems">
        <?php woocommerce_mini_cart(); ?>
    </div>
    <div class="cart-drawer__foot">
        <?php if ( WC()->cart && ! WC()->cart->is_empty() ) : ?>
        <div class="cart-subtotal">
            <span class="label"><?php esc_html_e( 'Subtotal', 'lumina' ); ?></span>
            <span class="value"><?php echo WC()->cart->get_cart_subtotal(); ?></span>
        </div>
        <?php
        $free_shipping_min = 75;
        $remaining = $free_shipping_min - WC()->cart->get_subtotal();
        if ( $remaining > 0 ) : ?>
        <div class="cart-free-shipping">
            <?php echo lumina_svg_icon( 'truck' ); ?>
            <?php printf( esc_html__( 'Add %s more for free shipping', 'lumina' ), wc_price( $remaining ) ); ?>
        </div>
        <?php else : ?>
        <div class="cart-free-shipping">
            <?php echo lumina_svg_icon( 'check' ); ?>
            <?php esc_html_e( 'You qualify for free shipping!', 'lumina' ); ?>
        </div>
        <?php endif; ?>
        <a href="<?php echo esc_url( wc_get_cart_url() ); ?>" class="btn btn--outline btn--full" style="margin-bottom:0.5rem;">
            <?php esc_html_e( 'View Cart', 'lumina' ); ?>
        </a>
        <a href="<?php echo esc_url( wc_get_checkout_url() ); ?>" class="btn btn--primary btn--full">
            <?php esc_html_e( 'Checkout', 'lumina' ); ?>
        </a>
        <?php else : ?>
        <p style="text-align:center;color:var(--stone-400);font-size:0.9rem;padding:2rem 0;">
            <?php esc_html_e( 'Your cart is empty.', 'lumina' ); ?>
        </p>
        <a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>" class="btn btn--primary btn--full">
            <?php esc_html_e( 'Start Shopping', 'lumina' ); ?>
        </a>
        <?php endif; ?>
    </div>
</aside>

<!-- Toast notification -->
<div class="toast" id="toast" role="status" aria-live="polite"></div>
