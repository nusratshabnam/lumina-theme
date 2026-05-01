<footer id="colophon" class="site-footer" role="contentinfo">
    <div class="container">
        <div class="footer-top">

            <div class="footer-brand">
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="site-logo" style="display:inline-flex;">
                    <span class="site-logo__mark"><svg viewBox="0 0 14 14" fill="white"><circle cx="7" cy="7" r="5"/></svg></span>
                    <span class="site-logo__text"><?php bloginfo( 'name' ); ?></span>
                </a>
                <p><?php bloginfo( 'description' ); ?></p>

                <div class="footer-social" style="margin-top:1.5rem;">
                    <?php foreach ( [ 'instagram', 'facebook', 'twitter', 'pinterest' ] as $p ) :
                        $url = lumina_option( $p );
                        if ( $url ) : ?>
                        <a href="<?php echo esc_url( $url ); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html( ucfirst( $p ) ); ?></a>
                    <?php endif; endforeach; ?>
                </div>

                <div class="footer-newsletter" style="margin-top:1.5rem;">
                    <p style="font-size:0.75rem;font-family:var(--font-mono);letter-spacing:0.1em;text-transform:uppercase;color:rgba(255,255,255,0.5);margin-bottom:0.5rem;">
                        <?php esc_html_e( 'Join our newsletter', 'lumina' ); ?>
                    </p>
                    <div class="footer-newsletter-form">
                        <input type="email" class="footer-newsletter-input" placeholder="<?php esc_attr_e( 'your@email.com', 'lumina' ); ?>" id="footerEmail">
                        <button class="footer-newsletter-btn" id="footerNewsletterBtn"><?php esc_html_e( 'Subscribe', 'lumina' ); ?></button>
                    </div>
                </div>
            </div>

            <div class="footer-col">
                <h5><?php esc_html_e( 'Shop', 'lumina' ); ?></h5>
                <?php wp_nav_menu( [
                    'theme_location' => 'footer-1',
                    'container'      => false,
                    'depth'          => 1,
                    'fallback_cb'    => function() {
                        $links = [ __( 'New Arrivals', 'lumina' ) => '?orderby=date', __( 'Best Sellers', 'lumina' ) => '?orderby=popularity', __( 'Sale', 'lumina' ) => '?on_sale=1', __( 'All Products', 'lumina' ) => '' ];
                        echo '<ul>';
                        foreach ( $links as $l => $q ) echo '<li><a href="' . esc_url( wc_get_page_permalink( 'shop' ) . $q ) . '">' . esc_html( $l ) . '</a></li>';
                        echo '</ul>';
                    },
                ] ); ?>
            </div>

            <div class="footer-col">
                <h5><?php esc_html_e( 'About', 'lumina' ); ?></h5>
                <?php wp_nav_menu( [
                    'theme_location' => 'footer-2',
                    'container'      => false,
                    'depth'          => 1,
                    'fallback_cb'    => function() {
                        $links = [ __( 'Our Story', 'lumina' ), __( 'Sustainability', 'lumina' ), __( 'Press', 'lumina' ), __( 'Careers', 'lumina' ), __( 'Blog', 'lumina' ) ];
                        echo '<ul>';
                        foreach ( $links as $l ) echo '<li><a href="' . esc_url( home_url( '/' ) ) . '">' . esc_html( $l ) . '</a></li>';
                        echo '</ul>';
                    },
                ] ); ?>
            </div>

            <div class="footer-col">
                <h5><?php esc_html_e( 'Help', 'lumina' ); ?></h5>
                <?php wp_nav_menu( [
                    'theme_location' => 'footer-3',
                    'container'      => false,
                    'depth'          => 1,
                    'fallback_cb'    => function() {
                        $links = [ __( 'FAQ', 'lumina' ), __( 'Shipping & Returns', 'lumina' ), __( 'Size Guide', 'lumina' ), __( 'Contact Us', 'lumina' ), __( 'Track Order', 'lumina' ) ];
                        echo '<ul>';
                        foreach ( $links as $l ) echo '<li><a href="' . esc_url( home_url( '/' ) ) . '">' . esc_html( $l ) . '</a></li>';
                        echo '</ul>';
                    },
                ] ); ?>
            </div>

        </div><!-- .footer-top -->

        <div class="footer-bottom">
            <span style="color:rgba(255,255,255,0.3);">
                &copy; <?php echo date( 'Y' ); ?> <?php bloginfo( 'name' ); ?>. <?php esc_html_e( 'All rights reserved.', 'lumina' ); ?>
            </span>
            <div class="footer-payment-icons" aria-label="<?php esc_attr_e( 'Accepted payment methods', 'lumina' ); ?>">
                <?php foreach ( [ 'Visa', 'MC', 'Amex', 'PayPal', 'Apple' ] as $pm ) : ?>
                    <span class="footer-payment-icon"><?php echo esc_html( $pm ); ?></span>
                <?php endforeach; ?>
            </div>
            <div class="footer-legal">
                <a href="<?php echo esc_url( get_privacy_policy_url() ); ?>"><?php esc_html_e( 'Privacy', 'lumina' ); ?></a>
                <a href="<?php echo esc_url( home_url( '/terms' ) ); ?>"><?php esc_html_e( 'Terms', 'lumina' ); ?></a>
                <a href="<?php echo esc_url( home_url( '/cookies' ) ); ?>"><?php esc_html_e( 'Cookies', 'lumina' ); ?></a>
            </div>
        </div>

    </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
