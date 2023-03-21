<?php
/*
 * Plugin Name:       Ashwini Stripe Payment Gateway Plugin
 * Description:       Stripe Payment Gateway Integration by Ashwini
 * Version:           1.0.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Ashwini Sojitra
 */

require __DIR__ . '/init.php';
require __DIR__ .'/stripe.class.php';



add_filter ('woocommerce_payment_gateways', 'add_to_ashwini_stripe_payment_gateway');

function add_to_ashwini_stripe_payment_gateway ($gateways)
{
    $gateways[] = 'Ashwini_Stripe_Gateway';
    return $gateways;

}
?> 