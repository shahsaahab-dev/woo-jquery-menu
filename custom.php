<?php

/**
 * Plugin Name: Custom Plugin
 * Author: Devsyed
 * Description: Extending WooFood Functionality
 * Text-Domain: custom-plugin
 */
// $currency_symbol = get_woocommerce_currency_symbol();
function custom_scripts() {
	wp_enqueue_script( 'custom', plugin_dir_url( __FILE__ ) . '/custom.js', array( 'jquery' ), '1.0', true );
	wp_localize_script(
		'custom',
		'js_data',
		array(
			'ajaxurl'  => admin_url( 'admin-ajax.php' ),
			'site_url' => home_url(),
			'currency_symbol' => $currency_symbol,
		)
	);

}
add_action( 'wp_enqueue_scripts', 'custom_scripts' );


// Shortcodes
add_shortcode( 'food_categories_accordion', 'fca_func' );
function fca_func() {
		ob_start();
		$args               = array(
			'taxonomy' => 'product_cat',
		);
		$product_categories = get_terms( $args );?>
		<style>
			ul.product-cats {list-style: none; margin: 0px;padding: 0; position: absolute; top: 20px;}
			.cart-parent { position: relative; top: 40px;}
		</style>
		<ul class="product-cats">
		<?php
		foreach ( $product_categories as $cat ) {
			echo '<li><a data-cat-name="' . $cat->name . '" href="#' . $cat->name . '">' . $cat->name . '</a></li>';
		}
		return ob_get_clean();
		?>
		</ul>
		<?php
}
 add_shortcode( 'cart_widget', 'cart_widget_func' );


function cart_widget_func() {
	ob_start();
	global $woocommerce;
	?>
	<style>
		body {min-height: 2000px;}
		footer#colophon{position:fixed; bottom:0;left:0;right:0;}
		.cart-widget {border: 2px solid #000;text-align: center;padding: 40px 10px;}
		h2{font-size:20px;text-transform:uppercase;	}
		.buttons button {border-radius: 100%;width: 22px; font-size: 12px;margin-right: 2px;}
		ul.cart-item {margin: 0px;padding: 0px;list-style: none;width: 100%; border-bottom: 1px solid; padding-bottom: 20px;}
		ul.cart-item li {display: block;margin-top: 14px;}
		.cart-parent {position: relative!important;width: 260px;}
		.buttons {display: inline-flex;float: left;margin-left: 10px;margin-bottom: 8px;}
		.item-details {display: inline-flex;width: 95%;}
		.food-item {margin-left: 14px;font-weight: bold; width: 60%;text-align: left; margin:0 auto;font-size: 13px;}
		.price {float: right; text-align: right; font-size: 14px;}
		.calcs {margin-top: 20px;text-align: left; border-bottom:1px solid #000;margin-bottom: 30px;}
		.calcs p span {float: right;}
		.calcs p {font-size: 13px;}
		.btn.checkout {padding: 15px 40px;margin-top: 25px;background: #222;color: #fff;}
		.min-order {margin-top: 25px; font-size: 12px;}

		.cart-widget { position: absolute; width: 260px; transition: all .5s ease-in;}
		.sticky{position:fixed!important;top:20px!important; transition:all .5s ease-in;}

		/* Accordions Style  */
		.panel-heading.panel-heading-title {background: none;border-bottom: 1px solid #222; padding: 0px;margin: 0px;}
		.woofood-accordion .panel-heading .panel-title { padding: 0px!important; margin: 0px;font-size: 16px;font-weight: 600;}
		.woofood-products .woofood-product-loop {max-width: 100%; width: 100%!important;flex: 0 auto;}
		span.woocommerce-Price-amount.amount { float: right; margin-right: 10px; font-weight: 600;}
		.woofood-product-loop .product-button .button {padding: 5px 10px;color: #fff!important;border-radius: 0; background: #222; border: none; margin-left:10px}
		.woofood-product-loop .product-button .button:hover {background: #000!important;border: none;color: #fff!important;}

		/* WooStyles */
		span.woocommerce-Price-amount.amount {padding: 0px;margin: 0px;}
		span.woocommerce-Price-currencySymbol {float: left!important;}
		a.added_to_cart.wc-forward {display: none;}


		@media screen and (max-width:768px){
    .cart-parent{
        width:100%
    }
    .cart-widget{
        position:unset!Important;
        margin:0 auto!important;
    }
    .product-cats{
        position:unset!important
    }
}
/* End woostyles */
</style>
	<div class="cart-parent" id="cart-parent-ref">
	<div class="cart-widget">
	<h2><?php echo __( 'JOUW BESTELLING', 'custom-plugin' ); ?></h2>
	<ul class="cart-item" id="cart-box">
	<?php
	$items = $woocommerce->cart->get_cart();
	foreach ( $items as $item => $values ) {
		$_product = wc_get_product( $values['data']->get_id() );
		$price    = get_post_meta( $values['product_id'], '_price', true );
		echo '
        <li>
        <div class="buttons">
			<button class="decrease">-</button>
				<button class="increase">+</button>
				
        </div>
        <div class="item-details">
        <div class="quantity" data-product-cart-hash="' . $values['key'] . '">' . $values['quantity'] . '<span>x</span></div>
        <div class="food-item" data-product-id = ' . $values['product_id'] . '>' . $_product->get_title() . '</div>
        <div class="price" data-product-price="' . $price . '">' . $price . '</div>
        </div>
    </li>';
	}

	?>
	</ul>
	<div class="calcs" id="ref-calc">
	
		<p id="subtotal">Subtotaal<span>
		<?php
		print_r(
			$woocommerce->cart->get_cart_subtotal()
		);
		?>
		</span></p>
		<?php
		$without_shipping = $woocommerce->cart->get_cart_subtotal();
		$shipping_cost = $woocommerce->cart->get_cart_shipping_total(); 
		?>
		<p id=>Bezorgkosten <?php echo get_woocommerce_currency_symbol();?><span>
		<?php print_r($woocommerce->cart->get_cart_shipping_total()) ?></span></p>
		<p id="total">Totaal<span>
		<?php
		$formatted_wc = ltrim($without_shipping,"£");
		$formatted_sc = ltrim($shipping_cost,"£");
		echo $formatted_sc . $formatted_wc;
		?>
 </span></p>
	</div>
	<a href="/checkout" class="btn checkout">Bestellen</a>
	<div class="min-order">
	<p>Je hebt het minimum bestelbedrag van € 9,99 bereikt en kunt afrekenen.</p></div>
</div>
</div>
	<?php
	return ob_get_clean();
}

add_action( 'wp_ajax_add_to_cart_increase_button', 'add_to_cart_increase_button' );
add_action( 'wp_ajax_nopriv_add_to_cart_increase_button', 'add_to_cart_increase_button' );
function add_to_cart_increase_button() {
	$product_id = $_POST['productId'];
	global $woocommerce;
	$add_to_cart = $woocommerce->cart->add_to_cart( $product_id );
	if ( $add_to_cart ) {
		wp_send_json( 'Product is Updated' );
	}
	wp_die();
}

add_action( 'wp_ajax_add_to_cart_decrease_button', 'add_to_cart_decrease_button' );
add_action( 'wp_ajax_nopriv_add_to_cart_decrease_button', 'add_to_cart_decrease_button' );
function add_to_cart_decrease_button() {
	// public function set_quantity( $cart_item_key, $quantity = 1, $refresh_totals = true ) {
	$cart_hash = $_POST['cartHash'];
	$quantity  = $_POST['quantity'];
	$delete_from_cart = WC()->cart->set_quantity( $cart_hash, $quantity, true );
	if ( $delete_from_cart ) {
		wp_send_json( 'Item Decreased by One' );
	} else {
		wp_send_json( 'Item Couldnt be decreased' );
	}
	wp_die();
}
add_action( 'wp_ajax_increase_quantity', 'increase_quantity' );
add_action( 'wp_ajax_nopriv_increase_quantity', 'increase_quantity' );
function increase_quantity() {
	$product_id = $_POST['productId'];
	global $woocommerce;
	$add_to_cart = $woocommerce->cart->add_to_cart( $product_id );
	if ( $add_to_cart ) {
		wp_send_json( 'Product is Added Again' );
	}
	wp_die();
}


add_action( 'wp_ajax_remove_product_generate_key', 'remove_product_generate_key' );
add_action( 'wp_ajax_nopriv_remove_product_generate_key', 'remove_product_generate_key' );
function remove_product_generate_key() {
	global $woocommerce;
	$product_id = $_POST['productId'];
	$cart_items = json_decode(json_encode(WC()->cart->get_cart()));
	foreach($cart_items as $cart_item){
		if($cart_item->product_id == $product_id){
			$remove_cart_item = WC()->cart->remove_cart_item($cart_item->key);
			if($remove_cart_item){
				wp_send_json("Product Removed");
			}else{
				wp_send_json("Product Not Removed");
			}
		}

	}
	
}
