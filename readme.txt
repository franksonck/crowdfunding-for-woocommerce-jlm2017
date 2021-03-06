=== Crowdfunding for WooCommerce ===
Contributors: anbinder
Donate link: http://algoritmika.com/donate/
Tags: woocommerce,crowdfunding
Requires at least: 4.1
Tested up to: 4.5
Stable tag: 2.2.1
License: GNU General Public License v3.0
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Crowdfunding for WooCommerce plugin adds full crowdfunding support to WooCommerce.

== Description ==

Crowdfunding products for WooCommerce.

When enabled plugin adds full crowdfunding support to WooCommerce.
When adding or editing a product, you will have the possibility to set for each product individually:

* Goal (i.e. pledged) amount.
* Start and end dates.
* Custom "Back This Project" (i.e. "Add to Cart") button labels.
* Enable "Open Pricing" (i.e. "Name Your Price") functionality.

Also you will be able to:

* Set custom HTML to show when project *not yet started* and/or *ended*.
* Modify and choose where to display crowdfunding info, that is: goal remaining, time remaining, already pledged etc.

= Shortcodes =

When displaying crowdfunding data for the product, you should use plugin's shortcodes:

= Backers & Money Shortcodes =
* product_crowdfunding_total_sum – total sum (i.e. funded to date) for current product (formatted as price).
* product_crowdfunding_total_backers – total number of orders (i.e. backers) for current product.
* product_crowdfunding_total_items – total number of ordered items for current product.
* product_crowdfunding_goal – end goal for current product (formatted as price).
* product_crowdfunding_goal_remaining – sum remaining to reach the end goal for current product (formatted as price).
* product_crowdfunding_goal_remaining_progress_bar – goal remaining as graphical progress bar.
* product_crowdfunding_goal_backers – end goal (backers) for current product.
* product_crowdfunding_goal_backers_remaining – backers remaining to reach the end goal for current product.
* product_crowdfunding_goal_backers_remaining_progress_bar – goal (backers) remaining as graphical progress bar.
* product_crowdfunding_goal_items – end goal (items) for current product.
* product_crowdfunding_goal_items_remaining – items remaining to reach the end goal for current product.
* product_crowdfunding_goal_items_remaining_progress_bar – goal (items) remaining as graphical progress bar.

= Time Shortcodes =
* product_crowdfunding_startdate – starting date for current product.
* product_crowdfunding_starttime – starting time for current product.
* product_crowdfunding_startdatetime – starting date and time for current product.
* product_crowdfunding_deadline – ending date for current product.
* product_crowdfunding_deadline_time – ending time for current product.
* product_crowdfunding_deadline_datetime – ending date and time for current product.
* product_crowdfunding_time_remaining – time remaining till deadline.
* product_crowdfunding_time_remaining_progress_bar – time remaining as graphical progress bar.

= More Shortcodes =
* product_crowdfunding_add_to_cart_form – backers (add to cart) HTML form.

= Feedback =
* We are open to your suggestions and feedback - Thank you for using or trying out one of our plugins!
* Drop us a line at [www.algoritmika.com](http://www.algoritmika.com)

= More =
* Visit the [Crowdfunding for WooCommerce plugin page](http://coder.fm/item/crowdfunding-for-woocommerce-plugin/)

== Installation ==

1. Upload the entire 'crowdfunding-for-woocommerce' folder to the '/wp-content/plugins/' directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Start by visiting plugin settings at WooCommerce > Settings > Crowdfunding. Then try adding or editing a product.

== Changelog ==

= 2.2.1 - 13/05/2016 =
* Fix - Titles in per product admin options table - caused PHP notice when saving product.
* Dev - Text domain added to the plugin header.
* Dev - `[product_crowdfunding_time_progress_bar]` renamed to `[product_crowdfunding_time_remaining_progress_bar]`.
* Dev - POT file updated.

= 2.2.0 - 10/05/2016 =
* Fix - `total_orders` in `get_product_orders_data`.
* Fix - Custom links fixed.
* Dev - "Open Pricing (Name Your Price)" functionality added.
* Dev - `starting_offset` shortcodes attribute added to `get_product_orders_data`.
* Dev - `show_if_zero` attribute added to `output_shortcode` function.
* Dev - `[product_crowdfunding_goal_backers]` shortcode added.
* Dev - `[product_crowdfunding_goal_items]` shortcode added.
* Dev - `[product_crowdfunding_goal_backers_remaining]` shortcode added.
* Dev - `[product_crowdfunding_goal_items_remaining]` shortcode added.
* Dev - `[product_crowdfunding_goal_backers_remaining_progress_bar]` shortcode added.
* Dev - `[product_crowdfunding_goal_items_remaining_progress_bar]` shortcode added.
* Dev - `[product_crowdfunding_goal_progress_bar]` renamed to `[product_crowdfunding_goal_remaining_progress_bar]`.
* Dev - `[product_total_orders_sum]` renamed to `[product_crowdfunding_total_sum]`.
* Dev - `[product_total_orders]` renamed to `[product_crowdfunding_total_backers]`.
* Dev - `[product_crowdfunding_total_items]` shortcode added.
* Dev - Formating date and time according to local format.
* Dev - `post__not_in` added to `save_meta_box`.
* Dev - POT file added.
* Tweak - Titles added in per product admin options table.

= 2.1.0 - 26/11/2015 =
* Dev - WooCommerce Grouped products support added.
* Dev - `product_id` attribute added in shortcodes.
* Dev - `order_status` attribute added in orders shortcodes: `product_crowdfunding_goal_progress_bar`, `product_crowdfunding_goal_remaining`, `product_total_orders`, `product_total_orders_sum`.
* Dev - "Crowdfunding" column added to admin products list.
* Fix - Counting fix.
* Fix - Additional check in `is_crowdfunding_product()`. Caused PHP notice.
* Fix - Global `product` reset in `get_product_orders_data()` added.

= 2.0.0 - 27/10/2015 =
* Dev - Crowdfunding type product removed - now any product type (e.g. simple, variable) can be used as crowdfunding product.
* Fix - Shortcodes - `[product_crowdfunding_time_remaining]` singular form bug fixed.

= 1.2.0 - 18/10/2015 =
* Dev - Product Info - *Custom Product Info - Category View* option added.
* Dev - `[product_crowdfunding_time_progress_bar]` shortcode added.
* Dev - `[product_crowdfunding_goal_progress_bar]` shortcode added.
* Dev - `[product_crowdfunding_add_to_cart_form]` shortcode added.

= 1.1.1 - 02/10/2015 =
* Fix - "Remove Last Variation" bug when saving on product's admin edit page, fixed.

= 1.1.0 - 30/09/2015 =
* Dev - `[product_crowdfunding_starttime]`, `[product_crowdfunding_startdatetime]`, `[product_crowdfunding_deadline_time]`, `[product_crowdfunding_deadline_datetime]` shortcodes added.
* Dev - Start/end time added.

= 1.0.1 - 21/08/2015 =
* Fix - Validation on frontend only affects `crowdfunding` type products now.

= 1.0.0 - 20/08/2015 =
* Initial Release.

== Upgrade Notice ==

= 1.0.0 =
This is the first release of the plugin.
