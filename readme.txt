=== Q ===
Contributors: qlstudio
Tags: Q, theme, framework, developers 
Requires at least: 3.6
Tested up to: 3.8.2
Stable tag: 2.1.5
License: GPL2

NOTE: Q WordPress Development Framework

== Description ==

Q WordPress Development Framework for plugins and themes.

For feature request and bug reports, [please use the WP Support Website](http://www.wp-support.co/view/categories/q).

Please do not use the Wordpress.org forum to report bugs, as we no longer monitor or respond to questions there.

== Installation ==

1. Upload the plugin to your plugins directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Enjoy :)

== Screenshots ==

No applicable screenshots

== Frequently Asked Questions ==

= Where can I find out more about how this plugin works? =

Currently, there is scant documentation, but over the coming months as this plugin matures, we'll be adding more details at qstudio.us/plugins

== Changelog ==

= 2.0.0 =

* Move to github hosted plugin

= 1.6.1 =

* Simplified Q Settings page - removed Q_Key requirements.

= 1.6.0 =

* Added free-wall JS mosaic script
* Added slideJS jQuery slideshow
* Added organictabs JS tabs script
* Remove Q dashboard widget - it was a vanity item :)

= 1.5.3 =

* moved generic wordpress template functions from Q_Control to pluggable/wordpress.php

= 1.5.2 =

* added trim_cache function to pluggable/wordpress.php - for trimming WP_Post objects before caching

= 1.5.1 =

* add pluggable/deprecated.php and moved many old fig functions in there - to be dropped in the future

= 1.5.0 = 

* made all __construct methods private
* corrected several code issues to allow for better operation without a parent theme
* moved all template assets to a "theme-only" status - meaning that they are only loaded if Q finds them in the active theme library

= 1.4.3 = 

* hooked q_wp_title to after_setup_theme, as was not firing on wp_head hook
* allowed ( and removed default ) tags in q_excerpt_from_id function in pluggable/wordpress.php 

= 1.3.3 =

* removed filter in Q_Hook_WP_Head.class.php to hide version number on assets - this allows for easier cache busting.
* renamed language file to q.pot in line with WP plugin conventions.

= 1.3.2 =

* various fixes to assets locations
* added "page-slug" to body_classes
* removed transient caching on q_get_option - as WP already caches get_option calls and this allows for live device switching
* Added facebook share widget

= 1.3.1 =

* 3.8.1 Testing
* Forum link

= 1.3.0 =

* Initial working version

== Upgrade Notice ==

= 1.3.0 =

* Initial working version