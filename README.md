# Uncomment WordPress Plugin

Your one-stop shop to completely disable comments and remove all comment functionality from your theme and administration screens.

While WordPress allows you to disable comments site-wide via the comments setting screen, the Uncomment plugin goes one step further and disables or hides all interface elements and functionality related to comments on your WordPress site.

Starting with Uncomment consists of just two steps: installing and activating the plugin. Uncomment is designed to disable or hide commenting functionality on your WordPress site without any additional settings. Uncomment also works great as a must-use plugin.

## Installation

Uncomment is hosted on the [WordPress plugin directory](https://wordpress.org/plugins/uncomment/) and can be installed via the WordPress dashboard.

1. Visit the Plugins page within your WordPress dashboard and select ‘Add New’
1. Search for ‘Uncomment’ and install the plugin
1. Activate Uncomment from your Plugins page

### Composer

Uncomment can be added as a dependency to your project via the [wpackagist composer repository](https://wpackagist.org/search?q=uncomment).

## FAQ

### Does this plugin delete existing comments from the database?

Uncomment does not delete any existing comments from the database.

### Can I enable comments for a specific post or post type?

Uncomment is designed to completely disable or hide all comments and commenting functionality on your WordPress site. It does not provide any settings to enable comments for specific posts or post types.

### I'm still seeing comments on my site?

Uncomment uses WordPress core actions, filters and functions to hide or disable comments and comment-related elements. It is possible that your theme or another plugin uses its own custom comment elements or functions. In that case, the Uncomment plugin is unable to remove those elements.

If you spot any WordPress core comment functionality that should have been removed by the Uncomment plugin, please let us know by opening an [issue](https://github.com/functionsfile/uncomment/issues) or [pull request](https://github.com/functionsfile/uncomment/pulls).