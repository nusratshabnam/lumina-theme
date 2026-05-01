<?php
/**
 * Lumina – Single Product Page
 */
defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
    the_post();
    global $product;
    $product_id   = $product->get_id();
    $gallery_ids  = $product->get_gallery_image_ids();
    $main_img_id  = $product->get_image_id();
    $all_imgs     = array_merge( $main_img_id ? [ $main_img_id ] : [], $gallery_ids );
    $rating       = $product->get_average_rating();
    $rating_count = $product->get_rating_count();
    $categories   = get_the_terms( $product_id, 'product_cat' );
    $tags         = get_the_terms( $product_id, 'product_tag' );
    $sku          = $product->get_sku();
    $is_on_sale   = $product->is_on_sale();
    $regular      = $product->get_regular_price();
    $sale         = $product->get_sale_price();
?>
<main id="primary">
<div class="container section">

    <?php lumina_breadcrumb(); ?>

    <div class="product-single">

        <!-- GALLERY -->
        <div class="product-gallery">
            <div class="product-gallery__main" id="mainGalleryImg">
                <?php if ( $main_img_id ) : ?>
                    <?php echo wp_get_attachment_image( $main_img_id, 'lumina-product', false, [ 'id' => 'mainGalleryImgEl', 'loading' => 'eager' ] ); ?>
                <?php else : ?>
                    <?php echo wc_placeholder_img( 'lumina-product' ); ?>
                <?php endif; ?>
            </div>

            <?php if ( count( $all_imgs ) > 1 ) : ?>
            <div class="product-gallery__thumbs">
                <?php foreach ( $all_imgs as $idx => $img_id ) : ?>
                <button class="product-gallery__thumb<?php echo $idx === 0 ? ' active' : ''; ?>"
                        data-full="<?php echo esc_url( wp_get_attachment_image_url( $img_id, 'lumina-product' ) ); ?>"
                        aria-label="<?php printf( esc_attr__( 'View image %d', 'lumina' ), $idx + 1 ); ?>">
                    <?php echo wp_get_attachment_image( $img_id, 'lumina-thumb' ); ?>
                </button>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>

        <!-- SUMMARY -->
        <div class="product-summary">

            <?php if ( $categories && ! is_wp_error( $categories ) ) : ?>
            <div class="product-summary__brand">
                <a href="<?php echo esc_url( get_term_link( $categories[0] ) ); ?>"><?php echo esc_html( $categories[0]->name ); ?></a>
            </div>
            <?php endif; ?>

            <h1 class="product-summary__title"><?php the_title(); ?></h1>

            <?php if ( $rating > 0 ) : ?>
            <div class="product-summary__rating">
                <span class="product-summary__stars"><?php echo str_repeat( '★', (int) round( $rating ) ) . str_repeat( '☆', 5 - (int) round( $rating ) ); ?></span>
                <a href="#reviews" class="product-summary__rating-link">
                    <?php printf( _n( '%s review', '%s reviews', $rating_count, 'lumina' ), number_format_i18n( $rating_count ) ); ?>
                </a>
            </div>
            <?php endif; ?>

            <div class="product-summary__price">
                <?php if ( $is_on_sale ) : ?>
                    <span class="price" style="color:var(--error);"><?php echo wc_price( $sale ); ?></span>
                    <span class="was"><?php echo wc_price( $regular ); ?></span>
                    <?php $save_pct = round( ( ( $regular - $sale ) / $regular ) * 100 ); ?>
                    <span class="save"><?php printf( __( 'Save %d%%', 'lumina' ), $save_pct ); ?></span>
                <?php else : ?>
                    <span class="price"><?php echo $product->get_price_html(); ?></span>
                <?php endif; ?>
            </div>

            <div class="product-summary__divider"></div>

            <div class="product-summary__short-desc">
                <?php echo wp_kses_post( $product->get_short_description() ?: $product->get_description() ); ?>
            </div>

            <?php if ( $product->is_type( 'variable' ) ) : ?>
                <!-- Variable product form -->
                <?php woocommerce_variable_add_to_cart(); ?>
            <?php elseif ( $product->is_type( 'simple' ) ) : ?>
                <!-- Simple product ATC -->
                <form class="cart" action="<?php echo esc_url( $product->get_permalink() ); ?>" method="post" enctype='multipart/form-data'>
                    <div class="product-atc">
                        <div class="quantity-input">
                            <button type="button" class="qty-btn" data-action="decrease" aria-label="<?php esc_attr_e( 'Decrease quantity', 'lumina' ); ?>">−</button>
                            <input type="number" name="quantity" class="qty-field" value="1" min="1" max="<?php echo esc_attr( $product->get_stock_quantity() ?: 999 ); ?>" step="1" aria-label="<?php esc_attr_e( 'Quantity', 'lumina' ); ?>">
                            <button type="button" class="qty-btn" data-action="increase" aria-label="<?php esc_attr_e( 'Increase quantity', 'lumina' ); ?>">+</button>
                        </div>
                        <?php if ( $product->is_in_stock() ) : ?>
                        <button type="submit" name="add-to-cart" value="<?php echo esc_attr( $product_id ); ?>"
                                class="btn btn--primary btn--lg atc-btn single_add_to_cart_button">
                            <?php esc_html_e( 'Add to Cart', 'lumina' ); ?>
                        </button>
                        <?php else : ?>
                        <button type="button" class="btn btn--outline btn--lg atc-btn" disabled>
                            <?php esc_html_e( 'Out of Stock', 'lumina' ); ?>
                        </button>
                        <?php endif; ?>
                    </div>
                    <?php woocommerce_quantity_input(); ?>
                </form>
            <?php else : ?>
                <?php do_action( 'woocommerce_' . $product->get_type() . '_add_to_cart' ); ?>
            <?php endif; ?>

            <!-- Meta -->
            <div style="margin-top:1.25rem;">
                <?php if ( $sku ) : ?>
                <p class="product-meta-row"><strong><?php esc_html_e( 'SKU:', 'lumina' ); ?></strong> <?php echo esc_html( $sku ); ?></p>
                <?php endif; ?>
                <?php if ( $categories && ! is_wp_error( $categories ) ) : ?>
                <p class="product-meta-row"><strong><?php esc_html_e( 'Category:', 'lumina' ); ?></strong>
                    <?php echo implode( ', ', array_map( function( $cat ) {
                        return '<a href="' . esc_url( get_term_link( $cat ) ) . '">' . esc_html( $cat->name ) . '</a>';
                    }, $categories ) ); ?>
                </p>
                <?php endif; ?>
                <?php if ( $tags && ! is_wp_error( $tags ) ) : ?>
                <p class="product-meta-row"><strong><?php esc_html_e( 'Tags:', 'lumina' ); ?></strong>
                    <?php echo implode( ', ', array_map( function( $tag ) {
                        return '<a href="' . esc_url( get_term_link( $tag ) ) . '">' . esc_html( $tag->name ) . '</a>';
                    }, $tags ) ); ?>
                </p>
                <?php endif; ?>
            </div>

            <!-- Trust badges -->
            <div class="product-trust">
                <?php
                $trust_items = [
                    [ 'icon' => 'truck',   'text' => __( 'Free shipping over $75', 'lumina' ) ],
                    [ 'icon' => 'refresh', 'text' => __( '30-day returns', 'lumina' ) ],
                    [ 'icon' => 'shield',  'text' => __( 'Secure payment', 'lumina' ) ],
                ];
                foreach ( $trust_items as $item ) : ?>
                <div class="product-trust-item">
                    <?php echo lumina_svg_icon( $item['icon'] ); ?>
                    <span><?php echo esc_html( $item['text'] ); ?></span>
                </div>
                <?php endforeach; ?>
            </div>

        </div><!-- .product-summary -->
    </div><!-- .product-single -->

    <!-- TABS -->
    <div class="product-tabs" id="reviews">
        <div class="tabs-nav" role="tablist">
            <button class="tab-btn active" role="tab" aria-selected="true" data-tab="description"><?php esc_html_e( 'Description', 'lumina' ); ?></button>
            <button class="tab-btn" role="tab" aria-selected="false" data-tab="details"><?php esc_html_e( 'Product Details', 'lumina' ); ?></button>
            <button class="tab-btn" role="tab" aria-selected="false" data-tab="reviews">
                <?php esc_html_e( 'Reviews', 'lumina' ); ?>
                <?php if ( $rating_count ) echo ' (' . absint( $rating_count ) . ')'; ?>
            </button>
            <button class="tab-btn" role="tab" aria-selected="false" data-tab="shipping"><?php esc_html_e( 'Shipping', 'lumina' ); ?></button>
        </div>

        <div class="tab-panel active" id="tab-description" role="tabpanel">
            <?php if ( $product->get_description() ) : ?>
                <div class="container--narrow" style="padding:0;">
                    <?php echo wp_kses_post( $product->get_description() ); ?>
                </div>
            <?php else : ?>
                <p><?php esc_html_e( 'No description available for this product.', 'lumina' ); ?></p>
            <?php endif; ?>
        </div>

        <div class="tab-panel" id="tab-details" role="tabpanel">
            <?php
            $attributes = $product->get_attributes();
            if ( $attributes ) : ?>
            <table style="max-width:600px;">
                <tbody>
                    <?php foreach ( $attributes as $attr ) :
                        $name   = wc_attribute_label( $attr->get_name() );
                        $values = implode( ', ', $attr->get_terms() ? wp_list_pluck( $attr->get_terms(), 'name' ) : $attr->get_options() );
                        if ( ! $values ) continue;
                    ?>
                    <tr>
                        <td style="font-weight:600;font-size:0.8125rem;padding:0.6rem 1rem 0.6rem 0;color:var(--stone-800);white-space:nowrap;width:30%;border-bottom:1px solid var(--stone-100);"><?php echo esc_html( $name ); ?></td>
                        <td style="font-size:0.875rem;padding:0.6rem 0;color:var(--stone-600);border-bottom:1px solid var(--stone-100);"><?php echo esc_html( $values ); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php else : ?>
                <p><?php esc_html_e( 'No additional details specified.', 'lumina' ); ?></p>
            <?php endif; ?>
        </div>

        <div class="tab-panel" id="tab-reviews" role="tabpanel">
            <?php comments_template(); ?>
        </div>

        <div class="tab-panel" id="tab-shipping" role="tabpanel">
            <div class="container--narrow" style="padding:0;">
                <h4><?php esc_html_e( 'Shipping Information', 'lumina' ); ?></h4>
                <p><?php esc_html_e( 'Standard shipping (3–5 business days): Free on orders over $75, otherwise $5.99.', 'lumina' ); ?></p>
                <p><?php esc_html_e( 'Express shipping (1–2 business days): $14.99.', 'lumina' ); ?></p>
                <h4 style="margin-top:1.5rem;"><?php esc_html_e( 'Returns', 'lumina' ); ?></h4>
                <p><?php esc_html_e( 'Free returns within 30 days of delivery. Items must be unused and in original packaging.', 'lumina' ); ?></p>
            </div>
        </div>
    </div>

    <!-- RELATED PRODUCTS -->
    <?php
    $related_ids = wc_get_related_products( $product_id, 4 );
    if ( $related_ids ) :
    ?>
    <div style="margin-top:5rem;">
        <h2 style="margin-bottom:2rem;"><?php esc_html_e( 'You May Also Like', 'lumina' ); ?></h2>
        <div class="grid-4">
            <?php foreach ( $related_ids as $related_id ) :
                $product = wc_get_product( $related_id );
                if ( $product ) include get_template_directory() . '/template-parts/product-card.php';
            endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Reset $product -->
    <?php $product = wc_get_product( $product_id ); ?>

</div><!-- .container -->
</main>

<!-- Sticky ATC -->
<div class="sticky-atc" id="stickyAtc">
    <div class="sticky-atc__info">
        <div class="sticky-atc__name"><?php the_title(); ?></div>
        <div class="sticky-atc__price"><?php echo $product->get_price_html(); ?></div>
    </div>
    <?php if ( $product->is_in_stock() && $product->is_type( 'simple' ) ) : ?>
    <button class="btn btn--primary single_add_to_cart_button" data-product-id="<?php echo esc_attr( $product_id ); ?>">
        <?php esc_html_e( 'Add to Cart', 'lumina' ); ?>
    </button>
    <?php else : ?>
    <a href="#" class="btn btn--outline"><?php esc_html_e( 'Select Options', 'lumina' ); ?></a>
    <?php endif; ?>
</div>

<?php endwhile; ?>

<?php get_footer(); ?>
