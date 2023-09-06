=== Frontend Reset Password ===
Contributors: wpenhanced, rwebster85
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=VAYF6G99MCMHU
Author URI: https://wpenhanced.com
Requires at Least: 4.4
Tested up to: 6.2.2
Stable tag: trunk
License: GPLv2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Tags: password, reset password, lost password, login

Let your users reset their forgotten passwords from the frontend of your website.

== Description ==

**Frontend Reset Password** lets your site users reset their lost or forgotten passwords in the frontend of your site. No more default WordPress reset form! Users fill in their username or email address and a reset password link is emailed to them. When they click this link they'll be redirected to your site and asked for a new password. Everything is handled using default WordPress methods including security, so you don't have to worry.

**Frontend Reset Password** is perfect for sites that have disabled access to the WordPress dashboard, or if you want to include a lost/reset password form on one of your custom site pages. It also works great with **Easy Digital Downloads**!

Any error messages display right on the form, including whether the username or email address is invalid.

The plugin works by hooking into the ``lostpassword_url`` WordPress filter, meaning compatibility with other plugins can be better maintained.

**Frontend Reset Password** is also translation ready.

== Setup Guide ==

= Step 1 =
Include our shortcode ``[reset_password]`` in any page you want

= Step 2 =
Go to the plugin settings page and select which page your shortcode is on.

= Step 3 =
Customise! This is optional, the plugin works right out of the box, but you're able to change the text for the form elements.

== Customisation ==

The text in the lost/reset password forms can be customised. Very little CSS styling is used, so the forms should style with your website theme beautifully.

If you use a frontend login page you can set that in the plugin also. Users are told they can login and are shown the url when they successfully change their password.

You can also set the minimum number of characters required for a password. Default is 0.

== Support ==

Quick start guide included on the plugin settings page. For anything else post on the wordpress.org support forum.

== Installation ==

**Manually in WordPress**

1. Download the plugin ZIP file from WordPress.org
2. From the WordPress admin dashboard go to Plugins, Add New
3. Click Upload Plugin, locate the file, upload
4. In the WordPress dashboard go to Plugins, Installed Plugins, and activate **Frontend Reset Password**
5. Make sure to read the quick start guide! (it's really short)

**Manually using FTP**

1. Download the plugin ZIP file, extract it
2. FTP to your server and go to your root WordPress directory
3. Navigate to wp-content/plugins
4. Upload the parent directory *som-frontend-reset-password* - the folder that contains the file som-frontend-reset-password.php - to that location
5. In the WordPress dashboard go to Plugins, Installed Plugins, and activate **Frontend Reset Password**
6. Make sure to read the quick start guide! (it's really short)

You can customise **Frontend Reset Password** on the Plugins, Frontend Reset Password dashboard page.

== Frequently Asked Questions ==

= Error Messages =

**The e-mail could not be sent:** This happens when the wp_mail() function call fails. If you're testing the plugin on a localhost and don't use a local email server, this error will show.

== Screenshots ==

1. Reset Password Form (Twenty Seventeen Theme)
2. Enter New Password Form (Twenty Seventeen Theme)

== Changelog ==

= 1.2.2 - 1st August 2023 =
* MOD: Lost Password Form - Accessibility

= 1.2.1 - 8th November 2022 =
* MOD: Updated branding to match WP Enhanced
* MOD: Updated "tested up to" so its not out of date anymore

= 1.2 - 13th July 2020 =
* [New Feature] Setting to change the email subject.
* [Change] Additional `esc_html()` calls added to frontend facing text.
* [Change] All translatable strings using `__` have been converted to `esc_html__()`.
* [Change] Updated POT file included.

= 1.1.91 =
* New Feature: Custom templates. Template files can now be included in your child theme folder. Create a new folder inside your child theme directory called ``somfrp-templates``, and follow the same template folder/file structure as found in the plugin's ``templates`` folder.
* Change: Now uses ``add_query_arg()`` when creating a reset password link, to improve compatibility
* Change: Username no longer included in reset password link, switched to using user ID
* Change: Changes made to ``lost_password_form.php`` template file
* Change: Tested up to WordPress 5.4

= 1.1.9 =
* Fix: Changed the way error messages are displayed to improve security. As such the plugin template files have changed, meaning any custom ones you have made will need to be updated to reflect the new changes.
* Change: Removed redundant ``<i>`` element from ``lost_password_form.php``.

= 1.1.8 =
* New Feature: Custom templates. You can now override the form templates in your theme. Create a new folder inside your theme directory called ``somfrp-templates``, and follow the same template folder/file structure as found in the plugin's ``templates`` folder.

= 1.1.7 =
* Change: More HTML tags are now available to use in email messages, since the saved message is now included in the email raw, but still uses the ``wpautop()`` function to automatically add ``<p>`` tags

= 1.1.6 =
* Change: Replaced filter 'retrieve_password_title' with 'somfrp_retrieve_password_title' to prevent other plugins unintentionally overriding the email title
* Change: Replaced filter 'retrieve_password_message' with 'somfrp_retrieve_password_message' to prevent other plugins unintentionally overriding the email message
* Change: Set priority for lostpassword_url filter to 999
* Change: Moved plugin settings page from the Plugins section to the Settings section of the admin menu

= 1.1.5 =
* Change: Removed login check to allow resetting password when logged in
* Fix: Error output corrected for the invalid_key index

= 1.1.41 =
* Fix: Corrected bug with action displaying form

= 1.1.4 =
* Change: Changed to using "somfrp_action" parameter rather than "action" to avoid conflicts

= 1.1.3 =
* Change: Functions to override default lost password actions and filters have a higher priority number

= 1.1.2 =
* Change: Email sent confirmation text no longer shows the email address

= 1.1.1 =
* Fix: Custom text for the reset form now outputs HTML tags correctly

= 1.1 =
* New feature: Customise the name and email address that the reset password emails send from, rather than the default wordpress@yoursite.com
* New feature: Plugin now sends HTML formatted emails which can be fully customised in the settings
* New feature: Select custom pages to redirect to for the email sent successfully and password changed pages, rather than the reset password page handling everything

= 1.0.5 =
* Cleaned undefined index errors
* Change wp_mail() headers to better support some plugins/themes

= 1.0.4 =
* Fixed missing WP_Error object on password validation

= 1.0.3 =
* Plugin now translation ready

= 1.0.2 =
* Textdomain set for language file

= 1.0.1 =
* Textdomain fix

= 1.0 =
* Initial release