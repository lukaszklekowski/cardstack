<?php
/**
 * Show error messages
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/notices/error.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see 	    https://docs.woocommerce.com/document/template-structure/
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     3.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

foreach ($messages as $messageKey => $message) {
    if (strpos($message, 'do koszyka.') !== false) {
        unset($messages[$messageKey]);
    } else if (strpos($message, 'z koszyka ponieważ nie') !== false) {
        unset($messages[$messageKey]);
    }
}

if ( ! $messages ) {
	return;
}

?>
<ul class="woocommerce-error" role="alert">
	<?php foreach ( $messages as $message ) : ?>
		<li><?php echo wp_kses_post( $message ); ?></li>
	<?php endforeach; ?>
</ul>
