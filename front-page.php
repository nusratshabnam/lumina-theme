<?php get_header(); ?>

<main id="primary">

<!-- ── HERO ── -->
<section class="hero">
    <div class="hero__content">
        <p class="hero__overline"><?php esc_html_e( 'New Collection', 'lumina' ); ?></p>
        <h1 class="hero__heading">
            <?php echo wp_kses_post( lumina_option( 'tagline', 'Refined goods for <em>modern living.</em>' ) ); ?>
        </h1>
        <p class="hero__subtext"><?php echo esc_html( lumina_option( 'sub_tagline', 'Curated collections. Thoughtful design. Sustainable materials.' ) ); ?></p>
        <div class="hero__actions">
            <a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>" class="btn btn--primary btn--lg">
                <?php esc_html_e( 'Shop Now', 'lumina' ); ?>
            </a>
            <a href="<?php echo esc_url( home_url( '/about' ) ); ?>" class="btn btn--outline btn--lg">
                <?php esc_html_e( 'Our Story', 'lumina' ); ?>
            </a>
        </div>
    </div>
    <div class="hero__media">
        <?php
        $hero_img = get_theme_mod( 'lumina_hero_image', 0 );
        if ( $hero_img ) {
            echo wp_get_attachment_image( $hero_img, 'lumina-hero', false, [ 'alt' => get_bloginfo( 'name' ), 'loading' => 'eager' ] );
        } else {
            echo '<div style="width:100%;height:100%;min-height:500px;background:var(--stone-100);display:flex;align-items:center;justify-content:center;"><span style="font-size:4rem;">◯</span></div>';
        }
        ?>
        <div class="hero__badge">
            <div class="hero__badge-label"><?php esc_html_e( 'Free shipping', 'lumina' ); ?></div>
            <div class="hero__badge-value"><?php esc_html_e( 'Over $75', 'lumina' ); ?></div>
        </div>
    </div>
</section>

<?php if ( lumina_option( 'show_categories', true ) ) : ?>
<!-- ── CATEGORY STRIP ── -->
<?php
$categories = get_terms( [ 'taxonomy' => 'product_cat', 'hide_empty' => true, 'parent' => 0, 'number' => 6 ] );
if ( ! empty( $categories ) && ! is_wp_error( $categories ) ) :
$cat_icons = [ '◈', '◉', '◎', '◆', '◇', '●' ];
?>
<section class="category-strip section--sm">
    <div class="container">
        <div class="category-strip__grid">
            <?php foreach ( $categories as $i => $cat ) :
                $cat_link = get_term_link( $cat );
                $cat_thumb_id = get_woocommerce_term_meta( $cat->term_id, 'thumbnail_id', true );
            ?>
            <a href="<?php echo esc_url( $cat_link ); ?>" class="category-card">
                <div class="category-card__icon">
                    <?php if ( $cat_thumb_id ) {
                        echo wp_get_attachment_image( $cat_thumb_id, [ 32, 32 ] );
                    } else {
                        echo esc_html( $cat_icons[ $i % count( $cat_icons ) ] );
                    } ?>
                </div>
                <div class="category-card__name"><?php echo esc_html( $cat->name ); ?></div>
                <div class="category-card__count"><?php printf( _n( '%d item', '%d items', $cat->count, 'lumina' ), $cat->count ); ?></div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; endif; ?>

<?php if ( lumina_option( 'show_featured', true ) ) : ?>
<!-- ── FEATURED PRODUCTS ── -->
<section class="section">
    <div class="container">
        <div style="display:flex;align-items:flex-end;justify-content:space-between;gap:1rem;margin-bottom:2.5rem;">
            <div>
                <span class="eyebrow"><?php esc_html_e( 'Handpicked', 'lumina' ); ?></span>
                <h2 style="margin:0;"><?php esc_html_e( 'Featured Products', 'lumina' ); ?></h2>
            </div>
            <a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>" class="btn btn--ghost">
                <?php esc_html_e( 'View All →', 'lumina' ); ?>
            </a>
        </div>

        <?php
        $featured_args = [
            'post_type'      => 'product',
            'posts_per_page' => 8,
            'post_status'    => 'publish',
            'tax_query'      => [ [ 'taxonomy' => 'product_visibility', 'field' => 'name', 'terms' => 'featured' ] ],
        ];
        $featured_cat = lumina_option( 'featured_category', '' );
        if ( $featured_cat ) {
            $featured_args['tax_query'] = [ [ 'taxonomy' => 'product_cat', 'field' => 'slug', 'terms' => $featured_cat ] ];
        }
        $featured_query = new WP_Query( $featured_args );
        if ( ! $featured_query->have_posts() ) {
            $featured_query = new WP_Query( [ 'post_type' => 'product', 'posts_per_page' => 8, 'post_status' => 'publish' ] );
        }
        ?>
        <div class="grid-4">
            <?php while ( $featured_query->have_posts() ) : $featured_query->the_post();
                global $product;
                if ( ! $product ) $product = wc_get_product( get_the_ID() );
                include LUMINA_DIR . '/template-parts/product-card.php';
            endwhile; wp_reset_postdata(); ?>
        </div>
    </div>
</section>
<?php endif; ?>

<?php if ( lumina_option( 'show_banner', true ) ) : ?>
<!-- ── MID BANNER ── -->
<section class="section--sm" style="background:var(--stone-50);border-top:1px solid var(--stone-100);border-bottom:1px solid var(--stone-100);">
    <div class="container">
        <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:1px;background:var(--stone-200);">
            <?php
            $trust = [
                [ 'icon' => 'truck',   'title' => __( 'Free Shipping',     'lumina' ), 'desc' => __( 'On all orders over $75',        'lumina' ) ],
                [ 'icon' => 'refresh', 'title' => __( 'Free Returns',      'lumina' ), 'desc' => __( '30-day hassle-free returns',     'lumina' ) ],
                [ 'icon' => 'shield',  'title' => __( 'Secure Checkout',   'lumina' ), 'desc' => __( 'SSL encrypted & safe payments',  'lumina' ) ],
            ];
            foreach ( $trust as $item ) : ?>
            <div style="background:var(--stone-50);padding:1.75rem 2rem;display:flex;align-items:center;gap:1.25rem;">
                <div style="width:40px;height:40px;background:var(--white);border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;border:1px solid var(--stone-200);">
                    <?php echo lumina_svg_icon( $item['icon'] ); ?>
                </div>
                <div>
                    <div style="font-weight:600;font-size:0.875rem;color:var(--ink);margin-bottom:0.15rem;"><?php echo esc_html( $item['title'] ); ?></div>
                    <div style="font-size:0.8125rem;color:var(--stone-400);"><?php echo esc_html( $item['desc'] ); ?></div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<?php if ( lumina_option( 'show_new', true ) ) : ?>
<!-- ── NEW ARRIVALS ── -->
<section class="section">
    <div class="container">
        <div style="display:flex;align-items:flex-end;justify-content:space-between;gap:1rem;margin-bottom:2.5rem;">
            <div>
                <span class="eyebrow"><?php esc_html_e( 'Just Dropped', 'lumina' ); ?></span>
                <h2 style="margin:0;"><?php esc_html_e( 'New Arrivals', 'lumina' ); ?></h2>
            </div>
            <a href="<?php echo esc_url( add_query_arg( 'orderby', 'date', wc_get_page_permalink( 'shop' ) ) ); ?>" class="btn btn--ghost">
                <?php esc_html_e( 'See All →', 'lumina' ); ?>
            </a>
        </div>
        <div class="grid-4">
            <?php
            $new_query = new WP_Query( [
                'post_type'      => 'product',
                'posts_per_page' => 4,
                'post_status'    => 'publish',
                'orderby'        => 'date',
                'order'          => 'DESC',
            ] );
            while ( $new_query->have_posts() ) : $new_query->the_post();
                global $product;
                if ( ! $product ) $product = wc_get_product( get_the_ID() );
                include LUMINA_DIR . '/template-parts/product-card.php';
            endwhile; wp_reset_postdata();
            ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ── NEWSLETTER ── -->
<section style="background:var(--ink);padding:clamp(3rem,6vw,5rem) 0;">
    <div class="container">
        <div style="max-width:520px;margin:0 auto;text-align:center;">
            <span class="eyebrow" style="color:rgba(255,255,255,0.4);"><?php esc_html_e( 'Stay Updated', 'lumina' ); ?></span>
            <h2 style="color:var(--white);margin:0.5rem 0 1rem;"><?php esc_html_e( 'Get 10% Off Your First Order', 'lumina' ); ?></h2>
            <p style="color:rgba(255,255,255,0.5);font-size:0.9375rem;margin-bottom:1.75rem;"><?php esc_html_e( 'Subscribe for early access to new drops, exclusive offers, and style inspiration.', 'lumina' ); ?></p>
            <div class="footer-newsletter-form" style="max-width:420px;margin:0 auto;">
                <input type="email" class="footer-newsletter-input" id="heroNewsletterEmail" placeholder="<?php esc_attr_e( 'Enter your email', 'lumina' ); ?>">
                <button class="footer-newsletter-btn" id="heroNewsletterBtn"><?php esc_html_e( 'Subscribe', 'lumina' ); ?></button>
            </div>
            <p style="font-size:0.6875rem;color:rgba(255,255,255,0.25);margin-top:0.75rem;font-family:var(--font-mono);">
                <?php esc_html_e( 'No spam. Unsubscribe anytime.', 'lumina' ); ?>
            </p>
        </div>
    </div>
</section>

</main>

<?php get_footer(); ?>
