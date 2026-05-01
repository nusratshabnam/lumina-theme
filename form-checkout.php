<?php
/**
 * Lumina – Checkout Form
 */
defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_checkout_form', $checkout );

if ( ! $checkout->is_registration_enabled() && $checkout->is_registration_required() && ! is_user_logged_in() ) {
    echo esc_html( apply_filters( 'woocommerce_checkout_must_be_logged_in_message', __( 'You must be logged in to checkout.', 'lumina' ) ) );
    return;
}
?>
<div class="container section">

    <?php lumina_breadcrumb(); ?>
    <h1 style="margin-bottom:2.5rem;"><?php esc_html_e( 'Checkout', 'lumina' ); ?></h1>

    <?php do_action( 'woocommerce_checkout_before_customer_details' ); ?>

    <form name="checkout" method="post" class="checkout-layout woocommerce-checkout" action="<?php echo esc_url( wc_get_checkout_url() ); ?>" enctype="multipart/form-data">

        <!-- Left: Customer Details -->
        <div>

            <?php if ( $checkout->get_checkout_fields() ) : ?>

            <!-- Contact -->
            <div class="checkout-section">
                <h2 class="checkout-section-title">
                    <span class="checkout-section-num">1</span>
                    <?php esc_html_e( 'Contact Information', 'lumina' ); ?>
                </h2>
                <?php woocommerce_form_field( 'billing_email', $checkout->get_checkout_fields()['billing']['billing_email'], $checkout->get_value( 'billing_email' ) ); ?>
                <?php woocommerce_form_field( 'billing_phone', $checkout->get_checkout_fields()['billing']['billing_phone'], $checkout->get_value( 'billing_phone' ) ); ?>
            </div>

            <!-- Shipping Address -->
            <?php if ( WC()->cart->needs_shipping() ) : ?>
            <div class="checkout-section">
                <h2 class="checkout-section-title">
                    <span class="checkout-section-num">2</span>
                    <?php esc_html_e( 'Shipping Address', 'lumina' ); ?>
                </h2>
                <div class="checkout-grid-2">
                    <?php woocommerce_form_field( 'billing_first_name', $checkout->get_checkout_fields()['billing']['billing_first_name'], $checkout->get_value( 'billing_first_name' ) ); ?>
                    <?php woocommerce_form_field( 'billing_last_name', $checkout->get_checkout_fields()['billing']['billing_last_name'], $checkout->get_value( 'billing_last_name' ) ); ?>
                </div>
                <?php foreach ( [ 'billing_address_1', 'billing_address_2', 'billing_city', 'billing_state', 'billing_postcode', 'billing_country' ] as $field ) :
                    if ( isset( $checkout->get_checkout_fields()['billing'][ $field ] ) ) :
                        woocommerce_form_field( $field, $checkout->get_checkout_fields()['billing'][ $field ], $checkout->get_value( $field ) );
                    endif;
                endforeach; ?>
            </div>
            <?php endif; ?>

            <!-- Shipping Method -->
            <?php if ( WC()->cart->needs_shipping() && WC()->cart->show_shipping() ) : ?>
            <div class="checkout-section">
                <h2 class="checkout-section-title">
                    <span class="checkout-section-num">3</span>
                    <?php esc_html_e( 'Shipping Method', 'lumina' ); ?>
                </h2>
                <?php wc_get_template( 'checkout/shipping-methods.php' ); ?>
            </div>
            <?php endif; ?>

            <!-- Payment -->
            <div class="checkout-section">
                <h2 class="checkout-section-title">
                    <span class="checkout-section-num"><?php echo WC()->cart->needs_shipping() ? '4' : '2'; ?></span>
                    <?php esc_html_e( 'Payment Method', 'lumina' ); ?>
                </h2>
                <?php woocommerce_checkout_payment(); ?>
            </div>

            <!-- Order Notes -->
            <?php if ( apply_filters( 'woocommerce_checkout_show_comments', ! is_user_logged_in() || WC()->cart->needs_shipping() ) ) : ?>
            <div class="checkout-section">
                <?php woocommerce_form_field( 'order_comments', $checkout->get_checkout_fields()['order']['order_comments'], $checkout->get_value( 'order_comments' ) ); ?>
            </div>
            <?php endif; ?>

            <?php endif; ?>

        </div>

        <!-- Right: Order Summary -->
        <div>
            <div class="order-summary" style="position:sticky;top:80px;">
                <h2 class="order-summary-title"><?php esc_html_e( 'Order Summary', 'lumina' ); ?></h2>

                <!-- Cart items review -->
                <?php foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) :
                    $_product = $cart_item['data'];
                    if ( ! $_product || ! $_product->exists() ) continue;
                    $img_id = $_product->get_image_id();
                ?>
                <div style="display:grid;grid-template-columns:52px 1fr auto;gap:0.75rem;align-items:center;padding:0.75rem 0;border-bottom:1px solid var(--stone-100);">
                    <div style="position:relative;">
                        <?php echo $img_id ? wp_get_attachment_image( $img_id, 'lumina-cart', false, [ 'style' => 'width:52px;height:65px;object-fit:cover;border-radius:var(--radius);' ] ) : wc_placeholder_img( [ 52, 65 ] ); ?>
                        <span style="position:absolute;top:-6px;right:-6px;background:var(--stone-400);color:white;width:18px;height:18px;border-radius:50%;font-size:0.625rem;display:flex;align-items:center;justify-content:center;font-family:var(--font-mono);"><?php echo $cart_item['quantity']; ?></span>
                    </div>
                    <div>
                        <div style="font-size:0.875rem;font-weight:500;"><?php echo esc_html( $_product->get_name() ); ?></div>
                        <?php foreach ( $cart_item['variation'] ?? [] as $k => $v ) : ?>
                            <div style="font-size:0.6875rem;color:var(--stone-400);font-family:var(--font-mono);"><?php echo esc_html( $v ); ?></div>
                        <?php endforeach; ?>
                    </div>
                    <div style="font-size:0.875rem;font-weight:600;"><?php echo WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ); ?></div>
                </div>
                <?php endforeach; ?>

                <div style="margin-top:1rem;">
                    <div class="order-summary-row">
                        <span class="label"><?php esc_html_e( 'Subtotal', 'lumina' ); ?></span>
                        <span><?php wc_cart_totals_subtotal_html(); ?></span>
                    </div>
                    <?php if ( WC()->cart->needs_shipping() ) : ?>
                    <div class="order-summary-row">
                        <span class="label"><?php esc_html_e( 'Shipping', 'lumina' ); ?></span>
                        <span><?php wc_cart_totals_shipping_html(); ?></span>
                    </div>
                    <?php endif; ?>
                    <div class="order-summary-row total">
                        <span><?php esc_html_e( 'Total', 'lumina' ); ?></span>
                        <span><?php wc_cart_totals_order_total_html(); ?></span>
                    </div>
                </div>
            </div>
        </div>

    </form>

    <?php do_action( 'woocommerce_after_checkout_form', $checkout ); ?>

</div>
