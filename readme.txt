=== Cision Modules ===
Contributors: cyclonecode
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=PBTHN3L67QA2S&source=url&lc=US&item_name=Cision+Modules
Tags: cision, modules, ticker
Requires at least: 4.0.0
Tested up to: 5.6
Requires PHP: 7.1
Stable tag: 1.0.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This plugin aims to add support for different cision modules.

== Description ==

This plugin can be used to display tickers from Cision.

= Shortcode =

The shortcode **[cision-ticker]** can either be used by adding it to the content field of any post or by using the **do_shortcode** function in one of your templates.
At the moment the shortcode will only render the associated label with price and currency for each ticker.

If there is other data that you need to display, please contact me and perhaps we can make a small addition for this.

== Frequently Asked Questions ==

== Support ==

If you run into any trouble, don’t hesitate to add a new topic under the support section:
[https://wordpress.org/support/plugin/cision-modules/](https://wordpress.org/support/plugin/cision-modules/)

You can also try contacting me on [slack](https://join.slack.com/t/cyclonecode/shared_invite/zt-6bdtbdab-n9QaMLM~exHP19zFDPN~AQ).

== Installation ==

1. Upload custom-post-field to the **/wp-content/plugins/** directory,
2. Activate the plugin through the **Plugins** menu in WordPress.
3. You can now configure the plugin by going to *wp-admin/tools.php?page=cision-modules*.
4. Add your API KEY and save the form.
5. Enable the tickers you would like to use and any label you would like to use.
6. Save the form.

== Frequently Asked Questions ==

== Upgrade Notice ==

== Screenshots ==

1. A page using data for two tickers fetched from Cision.
2. The plugin configuration form.

== Changelog ==

= 1.0.1
- Removed dependency from composer.json.
- Refactor for PHP 7.1.
- Add support to enable specific tickers.
