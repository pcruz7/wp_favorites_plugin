=== Favorite Posts ===
Contributors: pcruz, log_oscon
Tags: posts
Requires at least: 3.0.1
Tested up to: 3.9.1
Stable tag: 1.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Use this plugin to favourite your posts, whether you are a registered user or not.

== Description ==

The plugin will bookmark your favourite posts for later read. This will work with a non registered user as well.
If the user is registered, but not logged in, the posts will be saved within the cookies and then merged
(and erased from the cookies) into the database upon log-in.

There are two shortcodes available:

1. [favorite_button] - allows the insertion of the button within the post/page enabling its favoriting;
2. [favorite_list] - display the user's favourite list. A few parameters can be passed:
  - posts_per_page - how many posts each favorite page will show;
  - order - ASCending or DESCending;

Two administrative panels are also available (but only for users able to edit posts and pages). These panels allow
to easily add/remove/toggle the previously mentioned shortcodes to pages and posts.

Lastly, a widget is also available which allows the configuration of the number of posts the list can display.

== Installation ==

1. Upload the corresponding .zip;
2. Activate it through the 'Plugins' menu in WordPress.

== Usage ==

Simply add the shortcode [favorite_button] to the posts or pages that you'll want to enable favouriting or
just use one of the available panels.
