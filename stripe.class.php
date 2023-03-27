<?php

if (
    !class_exists("Ashwini_Stripe_Gateway")
) {
    class Ashwini_Stripe_Gateway extends WC_Payment_Gateway
    {
        public function __construct()
        {
            $this->id = "stripe_by_ashwini";
            $this->has_fields = false;
            $this->method_title = __(
                "Stripe Payment Gateway",
                "stripe-ashwini"
            );
            $this->method_description = __(
                "Stripe Payment gateway plugin for woocommerce.",
                "stripe-ashwini"
            );

            $this->title = $this->get_option("title", "Stripe Payment Gateway");
            $this->description = $this->get_option("description");
            $this->instructions = $this->get_option("instructions");


            $this->init_form_fields();
            $this->init_settings();


            add_action(
                "woocommerce_update_options_payment_gateways_" . $this->id,
                [$this, "process_admin_options"]
            );
        }

        public function init_form_fields()
        {
            $this->form_fields = apply_filters("ashwini-stripe-fields", [
                "enabled" => [
                    "title" => __("Enable/Disable", "stripe-ashwini"),
                    "type" => "checkbox",
                    "label" => __("Enable or Disable Stripe Payment Method"),
                    "default" => "no",
                ],

                "title" => [
                    "title" => __("Payment Method", "stripe-ashwini"),
                    "type" => "text",
                    "description" => __(
                        "Add a new title for the Stripe Payment Gateway.",
                        "stripe-ashwini"
                    ),
                    "default" => __(
                        "Stripe Payment Gateways",
                        "stripe-ashwini"
                    ),
                    "desc_tip" => true,
                ],
                "publishablekey" => [
                    "title" => __("Stripe Publishable Key", "stripe-ashwini"),
                    "type" => "text",
                    "default" => "Add a Publishable key",
                    "desc_tip" => true,
                    "description" => __(
                        "Add a Publishable Key from Stripe Payment Method.",
                        "stripe-ashwini"
                    ),
                ],

                "secretkey" => [
                    "title" => __("Stripe Secret Key", "stripe-ashwini"),
                    "type" => "text",
                    "default" => "Add a Secret key",
                    "desc_tip" => true,
                    "description" => __(
                        "Add a Secret Key from Stripe Payment Method.",
                        "stripe-ashwini"
                    ),
                ],

                "description" => [
                    "title" => __("Description", "stripe-ashwini"),
                    "type" => "textarea",
                    "default" => __(
                        "Please remit your payment to the shop to allow for the delivery to be made",
                        "stripe-ashwini"
                    ),
                    "desc_tip" => true,
                    "description" => __(
                        "Add a new title for the Sojitra Payment Gateway that customers will see when they are in the checkout page",
                        "stripe-ashwini"
                    ),
                ],

                "instructions" => [
                    "title" => __("Instructions", "stripe-ashwini"),
                    "type" => "textarea",
                    "default" => __("Default Instructions", "stripe-ashwini"),
                    "desc_tip" => true,
                    "description" => __(
                        "Instruction that will be added to the thank you page and order email",
                        "stripe-ashwini"
                    ),
                ],
            ]);
        }

        public function process_payment($order_id)
        {
            $order = wc_get_order($order_id);

            $secretKey = $this->get_option("secretkey");

            $stripe = new \Stripe\StripeClient($secretKey);

            \Stripe\Stripe::setVerifySslCerts(false);

            $checkoutSession = $stripe->checkout->sessions->create([
                "line_items" => [
                    [
                        "price_data" => [
                            "currency" => get_woocommerce_currency(),
                            "product_data" => ["name" => "Shopping by" . get_bloginfo()],
                            "unit_amount" => $this->get_order_total() * 100,
                        ],
                        "quantity" => 1,
                    ],
                ],
                "mode" => "payment",
                "success_url" => $this->get_return_url($order),
                "cancel_url" => get_site_url().'/basket',
            ]);

            return array ("result" => "success", 
            "redirect" => $checkoutSession->url);
        }

        public function clear_payment_with_api()
        {
        }
    }
}
