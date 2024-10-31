=== WooCommerce Quickteller Business Payment Gateway ===
Contributors: fizo7switch, whoismuktar, fortuneudechukwu
Tags: payment, nigeria, woocommerce, interswitch, quickteller
Requires at least: 6.5
Tested up to: 6.6
Requires PHP: 7.4
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.html

This WooCommerce plugin enables merchants and businesses in **Nigeria, Uganda, and Kenya** to accept payments from multiple payment channels.

== Description ==
Quickteller Business is a platform provided by Interswitch that allows individuals and registered businesses to seamlessly receive payments online into their bank accounts via their websites, payment links, invoices, storefront, or via their mobile apps using our mobile SDK.
 
With the Quickteller Business WooCommerce plugin, you can accept payment via these channels:
 
- Card: Accept payments from Verve, Visa, Mastercard, Discover, and American Express.
- Quickteller Wallet: Secure payments directly from the Quickteller Wallet.
- Bank Transfer:  Facilitate seamless bank transfers in Nigeria.
- QR Payments: Provide customers with the convenience of QR-based payments.
- USSD: Accept payments via USSD in Nigeria.
- Pay with Credit: Allow customers to pay using their credit accounts.
- Mobile Money: Available in Uganda.
- Pay with Wallet: Accept payments from digital wallets like Opay, Palmpay, Pocket, and many more coming soon.
 
Why Choose Quickteller Business?
 
- Quick setup— Get started and begin accepting payments swiftly.
- Intuitive dashboard — Easily manage and track your transactions with an intuitive dashboard.
- Transparent pricing — Simple, clear pricing with no hidden fees.
- T+1 settlement — Instant payouts into your account the next business day. 
- Versatile payment options — Accept payments from a variety of channels, including cards, bank transfers, mobile money, and wallets.
- Seamless checkout — Provide your customers with a smooth and user-friendly payment experience through Quickteller.
- Robust security — Benefit from advanced security measures to protect your transactions.
- Prompt dedicated customer support — Access support from our team around the clock.
- Regular updates — Enjoy continuous enhancements and new features at no extra cost.
 
 
 Signup
 
To sign up, if you haven\'t already done so, you can click [here](https://business.quickteller.com/signup).
 
 License
 
[GNU](http://www.gnu.org/licenses/gpl-2.0.txt)
 


== Installation ==

 
 Requirements
 
### Quickteller Business Account
 
- You will need a Quickteller Business account. You can either use an existing account or create a new one here
 
### Active WooCommerce Installation
 
Ensure that you have a working WooCommerce installation on your WordPress site.
 
### Step 1: Set Up Your Quickteller Business Account
 
 Account Creation
 
1. Visit [business.quickteller.com](https://business.quickteller.com) to sign up for a merchant account.
2. Complete the onboarding process, which includes KYC (Know Your Customer) and compliance checks.
 
Payment Credentials
 
After your account is approved, [log in to your dashboard](https://docs.interswitchgroup.com/docs/getting-integration-credentials) and obtain your Payment credentials: 
- Merchant Code
- Payment Merchant ID
 
### Step 2: Installation of the Quickteller Business WooCommerce Plugin
 
 Plugin Automatic Installation
 
1. Log in to your WordPress admin dashboard.
2. Go to `Plugins > Add New`.
3. Search for \"Quickteller Business\" and click on \"Install Now.\"
4. Once installed, activate the plugin.
5. Navigate to `WooCommerce > Settings` in the left menu, then click on the “Checkout/Payment” tab.
6. Select the Quickteller Business Payment gateway option from the available checkout methods and configure the settings as needed.
 
 Plugin Manual Installation
 
1. Download the plugin zip file.
2. Log in to your WordPress Admin, and select `Plugins > Add New` from the left menu.
3. Click the “Upload” option, then select the downloaded zip file by clicking “Choose File.”
4. Click “OK” and then “Install Now” to proceed with the installation.
5. Activate the plugin.
6. Go to `WooCommerce > Settings` in the left menu, and click on the “Checkout/Payment” tab.
7. Select the Quickteller Business payment gateway from the available checkout methods and configure the settings accordingly.
 
### Step 3: Configuration of the Quickteller Business Woocommerce Plugin
 
To configure the plugin, go to `WooCommerce > Settings` from the left-hand menu, then click “Payment” from the top tab. You should see “Quickteller Business payment gateway” as an option at the top of the screen. Click on it to configure the payment gateway.
 
- Enable/Disable – Check the box to enable Quickteller Business/Interswitch Payment Gateway.
- Description – Controls the message that is shown under the Quickteller Business payment method on the checkout page. Here you can list the types of cards you accept.
- Payment Item ID – Enter your payment item ID here.
- Merchant Code – Enter your Merchant code here.
- Mode – Check this to enable LIVE or TEST mode.
 
Click on **Save Changes** for the changes you made to be effected.


== Frequently Asked Questions ==

= What Do I Need To Use The Plugin? =
 
A Quickteller Business merchant account — Use an existing account or create an account [here](https://business.quickteller.com).

You need to have the WooCommerce plugin installed and activated on your WordPress site.

= How do I obtain my Quickteller Business API credentials? =

After your Quickteller Business account is approved, log in to your dashboard. Navigate to the API settings section to find your API credentials, including the Merchant Code, Payment Merchant ID, Client ID, Client Secret, and API Key.

= Can I test the plugin before going live? =

Yes, you can use the plugin's TEST mode for testing transactions. In the plugin configuration settings under \"Mode,\" check the option for TEST mode to ensure everything works correctly before switching to LIVE mode.

= How often are payments settled into my settlement bank account? =

Payments are typically settled within T+1 (one business day). For more specific details on settlement times, please refer to your Quickteller Business account terms or contact the support team.




== Screenshots ==
1. Quickteller Business woocommerce plugin installation
2. Quickteller Business woocommerce payment gateway settings page
3. Quickteller Business Payment Gateway checkout page