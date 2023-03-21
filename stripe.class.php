<?php

if (
    class_exists("WC_Payment_Gateway") &&
    !class_exists("WC_as_stripe_gateway")
) {
    class Ashwini_Stripe_Gateway extends WC_Payment_Gateway
    {
        public function __construct()
        {
            $this->id = "ashwini_stripe";
            $this->has_fields = false;
            $this->method_title = __(
                "Ashwini Stripe Payment",
                "ashwini-stripe"
            );
            $this->method_description = __(
                "Ashwini Local Strive payment systems.",
                "ashwini-stripe"
            );

            $this->title = $this->get_option("title", "Ashwini Stripe Payment");
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
                    "title" => __("Enable/Disable", "ashwini-stripe"),
                    "type" => "checkbox",
                    "label" => __("Enable or Disable Ashwini Payment Method"),
                    "default" => "no",
                ],

                "title" => [
                    "title" => __("Payment Method", "ashwini-stripe"),
                    "type" => "text",
                    "description" => __(
                        "Add a new title for the Ashwini Stripe Payment Gateway.",
                        "ashwini-stripe"
                    ),
                    "default" => __(
                        "Ashwini Stripe Payment Gateways",
                        "ashwini-stripe"
                    ),
                    "desc_tip" => true,
                ],
                "publishablekey" => [
                    "title" => __("Stripe Publishable Key", "ashwini-stripe"),
                    "type" => "text",
                    "default" => "Add a Publishable key",
                    "desc_tip" => true,
                    "description" => __(
                        "Add a Publishable Key from Stripe Payment Method.",
                        "ashwini-stripe"
                    ),
                ],

                "secretkey" => [
                    "title" => __("Stripe Secret Key", "ashwini-stripe"),
                    "type" => "text",
                    "default" => "Add a Secret key",
                    "desc_tip" => true,
                    "description" => __(
                        "Add a Secret Key from Stripe Payment Method.",
                        "ashwini-stripe"
                    ),
                ],

                "description" => [
                    "title" => __("Description", "ashwini-stripe"),
                    "type" => "textarea",
                    "default" => __(
                        "Please remit your payment to the shop to allow for the delivery to be made",
                        "ashwini-stripe"
                    ),
                    "desc_tip" => true,
                    "description" => __(
                        "Add a new title for the Sojitra Payment Gateway that customers will see when they are in the checkout page",
                        "ashwini-stripe"
                    ),
                ],

                "instructions" => [
                    "title" => __("Instructions", "ashwini-stripe"),
                    "type" => "textarea",
                    "default" => __("Default Instructions", "ashwini-stripe"),
                    "desc_tip" => true,
                    "description" => __(
                        "Instruction that will be added to the thank you page and order email",
                        "ashwini-stripe"
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
                            "currency" => "gbp",
                            "product_data" => ["name" => "Shopping Items"],
                            "unit_amount" => $this->get_order_total() * 100,
                        ],
                        "quantity" => 1,
                    ],
                ],
                "mode" => "payment",
                "success_url" => get_site_url() . "/stripe-return",
                "cancel_url" => get_site_url() . "/stripe-return",
            ]);

            return ["result" => "success", "redirect" => $checkoutSession->url];
        }

        public function clear_payment_with_api()
        {
        }
    }
}
