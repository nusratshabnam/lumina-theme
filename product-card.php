<?php
/**
 * Product Card partial — expects global $product (WC_Product)
 */
if ( ! isset( $product ) || ! $product ) return;

$product_id  = $product->get_id();
$permalink   = get_permalink( $product_id );
$title       = $product->get_name();
$rating      = $product->get_average_rating();
$rating_count = $product->get_rating_count();
$price_html  = $product->get_price_html();
$badge_html  = lumina_get_product_badge( $product );
$img_id      = $product->get_image_id();
$gallery_ids = $product->get_gallery_image_ids();
$in_stock    = $product->is_in_stock();
?>
<article class="product-card reveal" data-product-id="<?php echo esc_attr( $product_id ); ?>">

    <div class="product-card__media">
        <a href="<?php echo esc_url( $permalink ); ?>" aria-label="<?php echo esc_attr( $title ); ?>" tabindex="-1">
            <?php if ( $img_id ) : ?>
                <?php echo wp_get_attachment_image( $img_id, 'lumina-product', false, [ 'class' => 'primary-img', 'loading' => 'lazy', 'alt' => esc_attr( $title ) ] ); ?>
                <?php if ( ! empty( $gallery_ids ) ) :
                    echo wp_get_attachment_image( $gallery_ids[0], 'lumina-product', false, [ 'class' => 'secondary-img', 'loading' => 'lazy', 'alt' => esc_attr( $title ) . ' alternate view' ] );
                endif; ?>
            <?php else : ?>
                <?php echo wc_placeholder_img( 'lumina-product' ); ?>
            <?php endif; ?>
        </a>

        <?php if ( $badge_html ) : ?>
        <div class="product-card__badges"><?php echo $badge_html; ?></div>
        <?php endif; ?>

        <button class="product-card__wishlist"
                data-product-id="<?php echo esc_attr( $product_id ); ?>"
                aria-label="<?php esc_attr_e( 'Add to wishlist', 'lumina' ); ?>">
            <?php echo lumina_svg_icon( 'heart' ); ?>
        </button>

        <div class="product-card__actions">
            <?php if ( $product->is_type( 'simple' ) && $in_stock ) : ?>
            <button class="btn btn--primary btn--sm btn--full single_add_to_cart_button"
                    data-product-id="<?php echo esc_attr( $product_id ); ?>"
                    style="flex:1;">
                <?php esc_html_e( 'Quick Add', 'lumina' ); ?>
            </button>
            <?php else : ?>
            <a href="<?php echo esc_url( $permalink ); ?>" class="btn btn--outline btn--sm btn--full" style="flex:1;">
                <?php $product->is_type( 'variable' ) ? esc_html_e( 'Select Options', 'lumina' ) : esc_html_e( 'View Product', 'lumina' ); ?>
            </a>
            <?php endif; ?>
        </div>
    </div>

    <div class="product-card__info">
        <?php
        $cats = get_the_terms( $product_id, 'product_cat' );
        if ( $cats && ! is_wp_error( $cats ) ) :
        ?>
        <div class="product-card__brand"><?php echo esc_html( $cats[0]->name ); ?></div>
        <?php endif; ?>

        <h3 class="product-card__name">
            <a href="<?php echo esc_url( $permalink ); ?>"><?php echo esc_html( $title ); ?></a>
        </h3>

        <?php if ( $rating > 0 ) : ?>
        <div class="product-card__rating">
            <?php echo lumina_stars( (int) round( $rating ) ); ?>
            <span class="product-card__rating-count">(<?php echo absint( $rating_count ); ?>)</span>
        </div>
        <?php endif; ?>

        <div class="product-card__price">
            <?php echo $price_html; ?>
        </div>
    </div>

</article>
