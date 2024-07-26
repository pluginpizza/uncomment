=== Uncomment — Disable Comments ===
Contributors: pluginpizza, barryceelen, functionsfile
Tags: comments, disable comments, spam comments, disable, remove comments
Requires at least: 4.6
Tested up to: 6.6
Requires PHP: 5.3
Stable tag: 1.2.1
License: GPLv3+
License URI: https://www.gnu.org/licenses/gpl-3.0.html

Your one-stop shop to completely remove all comment functionality from your theme and administration screens.

== Description ==

Your one-stop shop to completely disable comments and remove all comment functionality and interface elements from your theme and administration screens.

While WordPress allows you to disable comments site-wide via the comments setting screen, the Uncomment plugin goes one step further and disables or hides all interface elements and other functionality related to comments on your WordPress site.

Starting with Uncomment consists of just two steps: installing and activating the plugin. Uncomment is designed to disable or hide commenting functionality on your WordPress site without any additional settings. Uncomment also works great as a must-use plugin.

### Bug Reports

Do you want to report a bug or suggest a feature for Uncomment? Best to do so in the [Uncomment repository on GitHub](https://github.com/pluginpizza/uncomment/).

== Installation ==

Starting with Uncomment consists of just two steps: installing and activating the plugin. Uncomment is designed to disable or hide commenting functionality on your WordPress site without any additional settings. Uncomment also works great as a must-use plugin.

### Install Uncomment from within WordPress

1. Visit the Plugins page within your dashboard and select ‘Add New’
1. Search for ‘Uncomment’
1. Activate Uncomment from your Plugins page

### Install Uncomment manually

1. Upload the ‘uncomment’ folder to the /wp-content/plugins/ directory
1. Activate the Uncomment plugin through the ‘Plugins’ menu in WordPress

Uncomment contains a `GitHub Plugin URI` plugin header to support updates via the [Git Updater](https://git-updater.com/) plugin.

== Frequently Asked Questions ==

= Does this plugin delete existing comments from the database? =

Uncomment does not delete any existing comments from the database.

= Does this plugin delete existing comment blocks from the post content? =

Uncomment does not delete any existing comment blocks from the post content. It does prevent rendering comment blocks on your site by replacing their output with an empty string.

= Can I enable comments for a specific post or post type? =

Uncomment is designed to completely disable or hide all comments and commenting functionality on your WordPress site. It does not provide any settings to enable comments for specific posts or post types.

= I'm still seeing comments on my site? =

Uncomment uses WordPress core actions, filters and functions to hide or disable comments and comment-related elements. It is possible that your theme or another plugin uses its own custom comment elements or functions. In that case, the Uncomment plugin is unable to remove those elements.

If you spot any WordPress core comment functionality that should have been removed by the Uncomment plugin, please let us know by opening an [issue](https://github.com/pluginpizza/uncomment/issues/) or [pull request](https://github.com/pluginpizza/uncomment/pulls/) on Github.

== Changelog ==

= 1.2.1 =
Release Date: December 19th, 2023

- Fix missing exclusion for the core/comments block.
- Fix deprecated ${var} in strings. Props @claytoncollie

= 1.2.0 =
Release Date: November 13th, 2023

- Update tested up to version.
- Update cloned comment_feed_links() function for WP version < 6.1.0

= 1.1.0 =
Release Date: January 17th, 2023

- Add missing i18n namespace
- Use filters to remove comment feed links for WP version => 6.1.0

WordPress 6.1.0 [introduces filters](https://core.trac.wordpress.org/changeset/54161) that allow us to specify whether to display the post comments feed link. For versions lower than 6.1.0 we'll still replace the core [feed_links_extra()](https://developer.wordpress.org/reference/functions/feed_links_extra/) function with [our own near-identical one](https://github.com/pluginpizza/uncomment/blob/main/includes/feeds.php#L41).

= 1.0.4 =
Release Date: September 14th, 2022

- Remove Akismet plugin notice. Useful on multisite installs where Akismet is network activated or on VIP Go environments.

= 1.0.3 =
Release Date: May 16th, 2022

- Prevent inserting the new comment-related blocks included with WordPress 6.0 into a post.
- Disable showing comment-related blocks on the frontend.

Note that Uncomment aims to not touch any existing content. Comment-related blocks that have already been added to a post will still be visible in the editor. They will not be rendered when a site visitor views the post.

= 1.0.2 =
Release Date: January 27th, 2022

- Update tested up to version.

= 1.0.1 =
Release Date: January 27th, 2022

- Fix removing comment items from the network admin bar.
- Remove Akismet plugin "At a Glance" section.

= 1.0 =
Release Date: November 25th, 2021

- Initial release.
