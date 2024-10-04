=== Old Post Notice ===
Contributors: medavidallsop
Donate link: https://github.com/sponsors/medavidallsop
Tags: old, post, notice, old post, old post notice
Stable tag: 1.1.0
Tested up to: 6.6.2
License: GNU General Public License v3.0
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Display a notice on old posts.

== Description ==

**Simple plugin to display a notice on all posts that are older than a specified number of days.**

It is particularly beneficial for blogs with numerous older posts, as it allows you to alert your visitors that the information may be outdated.

= Features =

- Enable or disable the notice
- Set your own notice text
- Insert the post published/modified date in the notice
- Set how many days old a post needs to be for the notice to display
- Display based on the published or modified date of the post
- Position the notice above or below the post content
- Default styling included
- Color options for the background/text color
- Styling can be disabled to allow styling via custom CSS
- Dashboard widget displaying an overview of all posts that are displaying the old post notice

= Usage =

After installation you'll find the settings in **Settings > Old Post Notice**, once enabled the notice will appear as per the settings you have applied on old posts.

= Contribute =

Help contribute towards the development of this plugin via the [GitHub repository](https://github.com/medavidallsop/old-post-notice).

== Screenshots ==

1. Notice
2. Settings
2. Dashboard widget

== Frequently Asked Questions ==

= Can I see a list of all posts that are displaying the old post notice? =

Yes. Ensure the dashboard widget setting is enabled, you'll then find the widget in the WordPress dashboard.

= Can I set the colors? =

Yes. Ensure the styling setting is set to default, then use the color settings to choose your colors.

= Can I use my own custom CSS? =

Yes. It is best to set the styling setting to none, then use the `old-post-notice` class to apply your custom CSS rules.

== Installation ==

= Installation =

Please see [this documentation](https://wordpress.org/support/article/managing-plugins/#installing-plugins-1).

= Updates =

Please see [this documentation](https://wordpress.org/documentation/article/manage-plugins/#updating-plugins).

= Minimum Requirements =

* PHP 7.0.0
* WordPress 5.0.0

== Changelog ==

= x.x.x - xxxx-xx-xx =
* Update: Dashboard widget visibility to users with edit_posts capability

= 1.1.0 - 2024-10-04 =

* Add: Dashboard widget that displays an overview of all posts that are displaying the old post notice, disabled by default
* Update: Public CSS for old post notice is now only enqueued if the old post notice setting is enabled and the styling setting is not none

= 1.0.1 - 2024-10-02 =

* Add: Donate link in readme.txt
* Update: Conditions in uninstall.php
* Update: FAQs in readme.txt

= 1.0.0 - 2024-10-01 =

* New: Initial release