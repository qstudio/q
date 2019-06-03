# Q #
**Contributors:** qlstudio  
**Tags:** Q, theme, framework, developers   
**Requires at least:** 4.0.0 
**Tested up to:** 5.0.0  
**Stable tag:** 2.3.9
**License:** GPL2  

NOTE: Beta release of Q WordPress Development Framework

## Description ##

Q WordPress Development Framework for plugins and themes.

For feature request and bug reports, [please use the WP Support Website](http://www.wp-support.co/view/categories/q).

Please do not use the Wordpress.org forum to report bugs, as we no longer monitor or respond to questions there.

## Installation ##

1. Upload the plugin to your plugins directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Enjoy :)

## Screenshots ##

No applicable screenshots

## Frequently Asked Questions ##

### Where can I find out more about how this plugin works? ###

Currently, there is scant documentation, but over the coming months as this plugin matures, we'll be adding more details at qstudio.us/plugins

## Changelog ##

### 2.3.9 ###

* Moved Q GLobal JS and CSS to optional include, removed old libraries

### 2.3.8 ###

* Updates from Exchange integration
* Q global js / css can be included from q_theme with fallback check to Q
* deprecated webmasters function in hook/wp_head

### 2.3.7 ###

* Comment clean up

### 2.3.5 ###

* Google class reference fix

### 2.3.5 ###

* Updated and tidied class inclusion, tested Club & International

### 2.3.0 ###

* Moved options to ACF API

### 2.2.3 ###

* Testing sub modules with version bump

### 2.2.2 ###

* Facebook and Twitter script consent checks

### 2.2.1 ###

* Facebook and Twitter Open Graph meta tags added

### 2.2.0 ###

* preparation for sub module usage

### 1.3.3 ###

* removed filter in Q_Hook_WP_Head.class.php to hide version number on assets - this allows for easier cache busting.
* renamed language file to q.pot in line with WP plugin conventions.

### 1.3.2 ###

* various fixes to assets locations
* added "page-slug" to body_classes
* removed transient caching on q_get_option - as WP already caches get_option calls and this allows for live device switching
* Added facebook share widget

### 1.3.1 ###

* 3.8.1 Testing
* Forum link

### 1.3.0 ###

* Initial working version

## Upgrade Notice ##

### 1.3.0 ###

* Initial working version