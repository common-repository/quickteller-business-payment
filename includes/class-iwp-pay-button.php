<?php

if (!defined('ABSPATH')) {
    exit;
}

class Tbz_WC_IWP_Pay_Button_Gateway extends WC_Payment_Gateway_CC
{
    public $ref_code;
    public $merchant_code;
    public $mode;
    public $query_url;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->id = 'iwp-pay-button';
        $this->method_title = 'Quickteller Business Payment Gateway';
        $this->method_description = sprintf(__('The Interswitch Payment Gateway (IPG) gives you all the tools and features you need to receive and manage web payments from any channel and platform. <a href="%1$s" target="_blank">Sign up</a> for a Quickteller Business account, and <a href="%2$s" target="_blank">get your API keys</a>.', 'woo-paystack'), 'https://business.quickteller.com/signup', 'https://business.quickteller.com/developertools');
        $this->has_fields = false;

        // Load the form fields
        $this->init_form_fields();

        // Load the settings
        $this->init_settings();

        // Get setting values
        $this->title = 'Quickteller Business Payment Gateway';
        $this->description = $this->get_option('description');
        $this->enabled = $this->get_option('enabled');

        $this->ref_code = $this->get_option('iwp_pb_ref_code');
        $this->merchant_code = $this->get_option('iwp_merchant_code');
        $this->mode = $this->get_option('iwp_mode');

        // Set url according to Gateway Mode
        if (!$this->mode != 'LIVE') {
            $this->query_url = 'https://qa.interswitchng.com/collections/api/v1/gettransaction.json';
        } else {
            $this->query_url = 'https://webpay.interswitchng.com/collections/api/v1/gettransaction.json';
        }


        // Hooks
        add_action('wp_enqueue_scripts', array($this, 'payment_scripts'));

        add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));

        add_action('woocommerce_receipt_' . $this->id, array($this, 'receipt_page'));

        add_action('woocommerce_api_' . $this->id, array($this, 'verify_transaction'));

        // Payment listener/API hook
        // add_action('woocommerce_api_tbz_wc_iwp_pay_button_gateway', array($this, 'verify_transaction'));

        // Check if the gateway can be used
        if (!$this->is_valid_for_use()) {
            $this->enabled = false;
        }
    }


    /**
     * Check if this gateway is enabled and available in the user's country.
     */
    public function is_valid_for_use()
    {
        if (!in_array(get_woocommerce_currency(), apply_filters('woocommerce_iwp_pay_button_supported_currencies', array('NGN', 'USD')))) {
            $this->msg = 'Quickteller Business Payment Gateway does not support your store currency. Kindly set it to either NGN (&#8358) or USD <a href="' . admin_url('admin.php?page=wc-settings&tab=general') . '">here</a>';

            return false;
        }

        return true;
    }


    /**
     * Display Interswitch payment icon
     */
    public function get_icon()
    {
        $icon = '<img src="' . WC_HTTPS::force_https_url(plugins_url('assets/images/paymenticons.png', WC_IWP_PB_MAIN_FILE)) . '" alt="cards" />';

        return apply_filters('woocommerce_gateway_icon', $icon, $this->id);
    }


    /**
     * Check if this gateway is enabled
     */
    public function is_available()
    {
        if ($this->enabled == "yes") {
            if (!($this->ref_code && $this->merchant_code)) {
                return false;
            }

            return true;
        }

        return false;
    }


    /**
     * Admin Panel Options
     */
    public function admin_options()
    {
        ?>

        <h3>Quickteller Business Payment Gateway</h3>

        <?php

        if ($this->is_valid_for_use()) {
            echo '<table class="form-table">';
            $this->generate_settings_html();
            echo '</table>';
        } else { ?>
            <div class="inline error">
                <p><strong>Quickteller Business Payment Gateway Disabled</strong>: <?php echo $this->msg ?></p>
            </div>

        <?php }
    }


    /**
     * Initialise Gateway Settings Form Fields
     */
    public function init_form_fields()
    {
        $this->form_fields = array(
            'enabled' => array(
                'title' => 'Enable/Disable',
                'label' => 'Enable Quickteller Business Payment Gateway',
                'type' => 'checkbox',
                'description' => 'Enable Quickteller Business Payment Gateway as a payment option on the checkout page.',
                'default' => 'no',
                'desc_tip' => true
            ),
            'description' => array(
                'title' => 'Description',
                'type' => 'textarea',
                'description' => 'This controls the payment method description which the user sees during checkout.',
                'desc_tip' => true,
                'default' => 'Make payment using your debit and credit cards, bank transfer, USSD, mobile money, Pay with Opay, Kuda, Pocket, QR and Quickteller wallet.',
                'placeholder' => 'Enter your payment gateway description',
            ),
            'iwp_pb_ref_code' => array(
                'title' => 'Payment Item ID',
                'type' => 'text',
                'desc_tip' => 'This is a unique identifier for the product or service you are offering.  Enter the Payment Item ID associated with the product or service you are offering. This ID allows us to accurately track and process transactions for the item.',
                'description' => '<a href="https://business.quickteller.com/developertools" target="_blank">How to get Payment Item ID</a>',
                'default' => '',
                'placeholder' => '1324354',
            ),
            'iwp_merchant_code' => array(
                'title' => 'Merchant Code',
                'type' => 'text',
                'description' => '<a href="https://business.quickteller.com/developertools" target="_blank">How to get Merchant Code</a>',
                'desc_tip' => 'This is a unique code assigned to a merchant by the payment gateway provider.  Enter your Merchant Code here which is essential to identify your business and ensure that payments are routed to your account.',
                'placeholder' => 'MX123456',
                'default' => '',
            ),
            'iwp_mode' => array(
                'title' => 'Mode',
                'type' => 'text',
                'description' => 'Enter TEST or LIVE',
                'desc_tip' => true,
                'placeholder' => 'TEST or LIVE',
                'default' => '',
            )
        );
    }


    /**
     * Custom form fields on checkout page.
     */
    public function payment_fields()
    {
        if ($this->description) {
            echo wpautop(wp_kses_post($this->description));
        }

    }


    /**
     * Outputs scripts used for Interswitch payment
     */
    public function payment_scripts()
    {
        if (!is_checkout_pay_page()) {
            return;
        }

        $suffix = (defined('SCRIPT_DEBUG') && SCRIPT_DEBUG) ? '' : '.min';

        wp_enqueue_script('wc_iwp_pb', 'https://newwebpay.interswitchng.com/inline-checkout.js', array('jquery'), WC_IWP_PB_VERSION, false);




        // wp_enqueue_script('wc_iwp_pb', plugins_url('assets/js/iwp' . $suffix . '.js', WC_IWP_PB_MAIN_FILE), array('jquery'), WC_IWP_PB_VERSION, true);
    }


    /**
     * Process the payment
     */
    public function process_payment($order_id)
    {
        $order = wc_get_order($order_id);

        return array(
            'result' => 'success',
            'redirect' => $order->get_checkout_payment_url(true)
        );
    }


    /**
     * Displays the payment page
     */
    public function receipt_page($order_id)
    {
        $order = wc_get_order($order_id);

        $total = $order->get_total() * 100;

        $ref = $this->ref_code;

        $merchant_code = $this->merchant_code;

        $mode = $this->mode;

        $email = $order->get_data()['billing']['email'];

        $thank_you_url = $order->get_checkout_order_received_url();

        echo '<p>Thank you for your order, please click the pay button below to pay with debit/credit card using Interswitch.</p>';

        ob_start();

        $inline_endpoint = ($mode === 'LIVE') ? 'https://newwebpay.interswitchng.com/inline-checkout.js' : 'https://newwebpay.qa.interswitchng.com/inline-checkout.js';

        wp_enqueue_script('webpay', $inline_endpoint, array('jquery'), '1.0.0', false); ?>
        <script>
            function uniqid(prefix = "", random = false) {
                const sec = Date.now() * 1000 + Math.random() * 1000;
                const id = sec.toString(16).replace(/\./g, "").padEnd(14, "0");
                return `${prefix}${id}${random ? `.${Math.trunc(Math.random() * 100000000)}` : ""}`;
            };

            const payBtnEl = jQuery('#iwp-pay-btn');
            function paymentCallback(response) {
                const isSuccessfulPayment = response.resp == "00";

                if (!isSuccessfulPayment) {
                    alert("Your payment was not successful")

                    return
                };

                const thankYouPage = "<?php echo esc_url($thank_you_url); ?>";

                jQuery.ajax({
                    url: `${window.location.origin}/wc-api/iwp-pay-button`,
                    method: 'POST',
                    data: { verify_transaction: 'verify_transaction', ...response },
                    success: function (res) {
                        console.log({ response, res })


                        if (thankYouPage && isSuccessfulPayment) {
                            window.location.href = thankYouPage;
                        }
                    },
                    error: function (xhr, status, error) {
                        payBtnEl.prop('disabled', false);
                        console.error('Payment verification failed:', error);
                    }
                });
            }

            function submitHandler() {
                payBtnEl.prop('disabled', false);

                var transRef = `${uniqid()}|` + '<?php echo $order_id; ?>';
                var paymentRequest = {
                    merchant_code: '<?php echo $merchant_code; ?>',
                    pay_item_id: '<?php echo $ref; ?>',
                    txn_ref: transRef,
                    amount: '<?php echo $total; ?>',
                    currency: 566,
                    site_redirect_url: window.location.origin,
                    onComplete: paymentCallback,
                    mode: '<?php echo $mode; ?>',
                    cust_email: '<?php echo $email; ?>',
                    cust_id: '<?php echo $email; ?>'
                };
                window.webpayCheckout(paymentRequest);
            }
        </script>

        <div class="wc-iwp-payment-form" style="display: flex; align-items: center">
            <div class="form-input" style="margin-right: 10px">
                <button class="button" id="iwp-pay-btn" style="
                    border: 1px solid rgb(206, 206, 206);
                    height: 40px;
                    margin: 0;
                    box-shadow: rgb(226, 224, 224) 0px 1px 3px;
                    padding: 0 2em 0 0.8em;
                    font-weight: 700;
                    border-radius: 4px;
                    color: rgb(0, 66, 95);
                    font-size: 13px;
                    text-transform: uppercase;
                    background-color: #FFF;
                    background-image: url(https://paymentgateway.interswitchgroup.com/paymentgateway/public/images/isw_paynow_btnbg.png);
                    width: 150px;
                    display: inline-block;
                    box-sizing: border-box;
                    cursor: pointer;
                    font-family: 'proxima-nova', sans-serif, 'Helvetica';" onclick="submitHandler()">

                    <img style="float:left;" class="isw-pay-logo"
                        src="https://paymentgateway.interswitchgroup.com/paymentgateway/public/images/isw_paynow_btn.png">
                    <span style="margin-top: 10px;display: inline-block;margin-left: 8px;">
                        Pay Now
                    </span>
                </button>
            </div>
            <?php

            echo ob_get_clean();

            echo '<div id="iwp_paybutton_form"><form id="order_review" method="post" action="' . WC()->api_request_url('Tbz_WC_IWP_Pay_Button_Gateway') . '"></form>
			<a class="button cancel" href="' . esc_url($order->get_cancel_order_url()) . '">Cancel order &amp; restore cart</a></div>';

            echo '</div>';
    }


    /**
     * Verify Interswitch payment
     */
    public function verify_transaction()
    {
        @ob_clean();

        if (isset($_REQUEST['verify_transaction'])) {
            $pay_ref = sanitize_text_field($_REQUEST['txnref']);
            $pay_amount = sanitize_text_field($_REQUEST['amount']);

            $txn_details = $this->get_transaction_details($pay_ref, $pay_amount);

            if ('00' == $txn_details['ResponseCode']) {
                $order_details = explode('|', $txn_details['MerchantReference']);

                $order_id = (int) $order_details[1];

                $order = wc_get_order($order_id);

                if (in_array($order->get_status(), array('processing', 'completed', 'on-hold'))) {
                    wp_redirect($this->get_return_url($order));

                    exit;
                }

                $order_total = $order->get_total();

                $amount_paid = $txn_details['Amount'] / 100;

                $iwp_payment_ref = $txn_details['PaymentReference'];

                // Check if the amount paid matches the order total
                if ($amount_paid < $order_total) {
                    $order->update_status('on-hold', 'Amount paid is less than the total order amount.');

                    add_post_meta($order_id, '_transaction_id', $iwp_payment_ref, true);

                    $notice = 'Thank you for shopping with us.<br />Your payment transaction was successful, but the amount paid is not the same as the total order amount.<br />Your order is currently on-hold.<br />Kindly contact us for more information regarding your order and payment status.';
                    $notice_type = 'notice';

                    // Add Customer Order Note
                    $order->add_order_note($notice, 1);

                    // Add Admin Order Note
                    $order->add_order_note('<strong>Look into this order</strong><br />This order is currently on hold.<br />Reason: Amount paid is less than the total order amount.<br />Amount Paid was <strong>&#8358;' . $amount_paid . '</strong> while the total order amount is <strong>&#8358;' . $order_total . '</strong><br />Interswitch Transaction Reference: ' . $iwp_payment_ref);

                    $order->reduce_order_stock();

                    wc_add_notice($notice, $notice_type);
                    wp_redirect($this->get_return_url($order));  // Redirect to thank you page (or another page)
                    exit;
                } else {
                    $order->payment_complete($iwp_payment_ref);
                    $order->add_order_note(sprintf('Payment via Interswitch successful (Transaction Reference: %s)', $iwp_payment_ref));

                    $order->update_status('completed');
                    wc_empty_cart();  // Clear the cart


                    wp_redirect($this->get_return_url($order));

                    exit;
                }
            } else {
                $order_details = explode('|', sanitize_text_field($_REQUEST['txnref']));

                $order_id = (int) $order_details[1];

                $order = wc_get_order($order_id);

                $order->update_status('failed', 'Payment failed.');
            }

            wp_redirect($this->get_return_url($order));

            exit;
        }

        wp_redirect(wc_get_page_permalink('cart'));

        exit;
    }


    /**
     * Validate a payment
     */
    public function get_transaction_details($txn_ref, $amount)
    {
        $merchant_code = $this->merchant_code;
        $query_url = $this->query_url;

        $args = array(
            'merchantcode' => $merchant_code,
            'transactionreference' => $txn_ref,
            'amount' => $amount
        );

        $api_url = add_query_arg($args, $query_url);

        $args = array(
            'timeout' => 90
        );

        $request = wp_remote_get($api_url, $args);

        if (!is_wp_error($request) && 200 == wp_remote_retrieve_response_code($request)) {
            $response = json_decode(wp_remote_retrieve_body($request));
        } else {
            $response['ResponseCode'] = '400';
            $response['ResponseDescription'] = 'Can\'t verify payment. Contact us for more details about the order and payment status.';
        }

        return (array) $response;
    }
}