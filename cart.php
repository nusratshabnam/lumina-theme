<?php
/**
 * Lumina – Cart Page
 */
defined( 'ABSPATH' ) || exit;
?>
<div class="container section">

    <?php lumina_breadcrumb(); ?>
    <h1 style="margin-bottom:2.5rem;"><?php esc_html_e( 'Your Cart', 'lumina' ); ?></h1>

    <?php do_action( 'woocommerce_before_cart' ); ?>

    <form class="woocommerce-cart-form cart-page" action="<?php echo esc_url( wc_get_cart_url() ); ?>" method="post">

        <!-- Cart Items -->
        <div>
            <?php do_action( 'woocommerce_before_cart_table' ); ?>
            <table class="cart-table" cellspacing="0">
                <thead>
                    <tr>
                        <th><?php esc_html_e( 'Product', 'lumina' ); ?></th>
                        <th><?php esc_html_e( 'Price', 'lumina' ); ?></th>
                        <th><?php esc_html_e( 'Quantity', 'lumina' ); ?></th>
                        <th><?php esc_html_e( 'Subtotal', 'lumina' ); ?></th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php do_action( 'woocommerce_before_cart_contents' ); ?>
                    <?php foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) :
                        $_product   = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
                        $product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );

                        if ( ! $_product || ! $_product->exists() || $cart_item['quantity'] === 0 ) continue;

                        $product_permalink = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key );
                        $thumbnail         = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image( 'lumina-cart' ), $cart_item, $cart_item_key );
                        $product_price     = apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key );
                        $product_subtotal  = apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key );
                    ?>
                    <tr class="woocommerce-cart-form__cart-item <?php echo esc_attr( apply_filters( 'woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key ) ); ?>">
                        <td>
                            <div class="product-col">
                                <?php if ( $product_permalink ) : ?>
                                    <a href="<?php echo esc_url( $product_permalink ); ?>"><?php echo $thumbnail; ?></a>
                                <?php else : ?>
                                    <?php echo $thumbnail; ?>
                                <?php endif; ?>
                                <div>
                                    <div class="product-name">
                                        <?php echo $product_permalink ? '<a href="' . esc_url( $product_permalink ) . '">' . $_product->get_name() . '</a>' : $_product->get_name(); ?>
                                    </div>
                                    <?php foreach ( $cart_item['variation'] ?? [] as $key => $val ) : ?>
                                    <div class="product-variant"><?php echo esc_html( wc_attribute_label( str_replace( 'attribute_', '', $key ) ) ); ?>: <?php echo esc_html( $val ); ?></div>
                                    <?php endforeach; ?>
                                    <?php do_action( 'woocommerce_after_cart_item_name', $cart_item, $cart_item_key ); ?>
                                </div>
                            </div>
                        </td>
                        <td><?php echo $product_price; ?></td>
                        <td>
                            <div class="quantity-input" style="display:inline-flex;">
                                <button type="button" class="qty-btn" data-action="decrease" data-key="<?php echo esc_attr( $cart_item_key ); ?>">−</button>
                                <?php
                                if ( $_product->is_sold_individually() ) {
                                    echo '<input type="number" name="cart[' . esc_attr( $cart_item_key ) . '][qty]" class="qty-field" value="1" min="0" max="1" step="1">';
                                } else {
                                    woocommerce_quantity_input( [
                                        'input_name'   => "cart[{$cart_item_key}][qty]",
                                        'input_value'  => $cart_item['quantity'],
                                        'max_value'    => $_product->get_max_purchase_quantity(),
                                        'min_value'    => '0',
                                        'product_name' => $_product->get_name(),
                                        'classes'      => [ 'qty-field' ],
                                    ], $_product );
                                }
                                ?>
                                <button type="button" class="qty-btn" data-action="increase">+</button>
                            </div>
                        </td>
                        <td style="font-weight:600;"><?php echo $product_subtotal; ?></td>
                        <td>
                            <?php echo apply_filters( 'woocommerce_cart_item_remove_link',
                                sprintf(
                                    '<button type="button" class="remove-btn" aria-label="%s" data-product-id="%s" data-cart-item-key="%s">&#x2715;</button>',
                                    esc_attr( sprintf( __( 'Remove %s from cart', 'lumina' ), $_product->get_name() ) ),
                                    esc_attr( $product_id ),
                                    esc_attr( $cart_item_key )
                                ),
                                $cart_item_key
                            ); ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php do_action( 'woocommerce_cart_contents' ); ?>
                </tbody>
            </table>

            <?php do_action( 'woocommerce_after_cart_table' ); ?>

            <div style="display:flex;justify-content:space-between;align-items:center;margin-top:1.5rem;flex-wrap:wrap;gap:1rem;">
                <div style="display:flex;gap:0.75rem;align-items:center;">
                    <input type="text" name="coupon_code" class="form-input" style="max-width:200px;" placeholder="<?php esc_attr_e( 'Coupon code', 'lumina' ); ?>">
                    <button type="submit" name="apply_coupon" value="<?php esc_attr_e( 'Apply', 'lumina' ); ?>" class="btn btn--outline">
                        <?php esc_html_e( 'Apply', 'lumina' ); ?>
                    </button>
                </div>
                <button type="submit" name="update_cart" class="btn btn--outline" value="<?php esc_attr_e( 'Update cart', 'lumina' ); ?>">
                    <?php esc_html_e( 'Update Cart', 'lumina' ); ?>
                </button>
            </div>

            <?php do_action( 'woocommerce_cart_actions' ); ?>
            <?php wp_nonce_field( 'woocommerce-cart', 'woocommerce-cart-nonce' ); ?>
        </div>

        <!-- Order Summary -->
        <div>
            <div class="order-summary">
                <h2 class="order-summary-title"><?php esc_html_e( 'Order Summary', 'lumina' ); ?></h2>

                <?php do_action( 'woocommerce_before_cart_collaterals' ); ?>

                <div class="order-summary-row">
                    <span class="label"><?php esc_html_e( 'Subtotal', 'lumina' ); ?></span>
                    <span><?php wc_cart_totals_subtotal_html(); ?></span>
                </div>

                <?php foreach ( WC()->cart->get_coupons() as $code => $coupon ) : ?>
                <div class="order-summary-row" style="color:var(--success);">
                    <span class="label"><?php printf( __( 'Coupon: %s', 'lumina' ), esc_html( $code ) ); ?></span>
                    <span>−<?php wc_cart_totals_coupon_html( $coupon ); ?></span>
                </div>
                <?php endforeach; ?>

                <?php foreach ( WC()->cart->get_fees() as $fee ) : ?>
                <div class="order-summary-row">
                    <span class="label"><?php echo esc_html( $fee->name ); ?></span>
                    <span><?php wc_cart_totals_fee_html( $fee ); ?></span>
                </div>
                <?php endforeach; ?>

                <?php if ( WC()->cart->needs_shipping() && WC()->cart->show_shipping() ) : ?>
                <div class="order-summary-row">
                    <span class="label"><?php esc_html_e( 'Shipping', 'lumina' ); ?></span>
                    <span><?php wc_cart_totals_shipping_html(); ?></span>
                </div>
                <?php endif; ?>

                <?php wc_get_template( 'cart/cart-tax.php' ); ?>

                <div class="order-summary-row total">
                    <span><?php esc_html_e( 'Total', 'lumina' ); ?></span>
                    <span><?php wc_cart_totals_order_total_html(); ?></span>
                </div>

                <a href="<?php echo esc_url( wc_get_checkout_url() ); ?>" class="btn btn--primary btn--full btn--lg" style="margin-top:1.25rem;">
                    <?php esc_html_e( 'Proceed to Checkout', 'lumina' ); ?>
                </a>

                <div style="display:flex;justify-content:center;gap:0.5rem;margin-top:1rem;flex-wrap:wrap;">
                    <?php foreach ( [ 'Visa', 'MC', 'Amex', 'PayPal', 'Apple Pay' ] as $pm ) : ?>
                        <span class="footer-payment-icon" style="background:var(--stone-200);color:var(--stone-600);"><?php echo esc_html( $pm ); ?></span>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

    </form>

    <?php do_action( 'woocommerce_after_cart' ); ?>
</div>
