<?php
/**
 * Lumina – Product in loop override
 */
defined( 'ABSPATH' ) || exit;

global $product;
if ( ! $product || ! $product->is_visible() ) return;

include get_template_directory() . '/template-parts/product-card.php';
