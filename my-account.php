<?php
/**
 * Lumina – My Account Page
 */
defined( 'ABSPATH' ) || exit;
?>
<div class="container section">

    <?php lumina_breadcrumb(); ?>
    <h1 style="margin-bottom:2.5rem;">
        <?php
        $greeting = is_user_logged_in()
            ? sprintf( __( 'Hello, %s', 'lumina' ), esc_html( wp_get_current_user()->display_name ) )
            : __( 'My Account', 'lumina' );
        echo $greeting;
        ?>
    </h1>

    <div class="account-layout">

        <!-- Sidebar Nav -->
        <nav class="account-nav" aria-label="<?php esc_attr_e( 'Account navigation', 'lumina' ); ?>">
            <?php
            $endpoints = wc_get_account_menu_items();
            $nav_icons = [
                'dashboard'       => 'grid',
                'orders'          => 'cart',
                'downloads'       => 'star',
                'edit-address'    => 'truck',
                'payment-methods' => 'shield',
                'edit-account'    => 'user',
                'customer-logout' => 'close',
            ];
            foreach ( $endpoints as $endpoint => $label ) :
                $url    = wc_get_account_endpoint_url( $endpoint );
                $active = WC()->query->get_current_endpoint() === $endpoint ? ' active' : '';
                $icon   = $nav_icons[ $endpoint ] ?? 'check';
            ?>
            <a href="<?php echo esc_url( $url ); ?>" class="account-nav-item<?php echo $active; ?>">
                <?php echo lumina_svg_icon( $icon ); ?>
                <?php echo esc_html( $label ); ?>
            </a>
            <?php endforeach; ?>
        </nav>

        <!-- Main Content -->
        <main id="account-content">
            <?php
            /**
             * My Account content.
             * Renders the currently active WooCommerce account endpoint.
             */
            do_action( 'woocommerce_account_content' );
            ?>
        </main>

    </div>
</div>
