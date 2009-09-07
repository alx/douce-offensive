=== ThickBox ===
Contributors: chschenk
Donate link: http://www.christianschenk.org/donation/
Tags: thickbox, smoothbox, gallery, pictures, images
Requires at least: 2.0
Tested up to: 2.8
Stable tag: 1.4

Embed ThickBox into your posts and pages.

== Description ==

If you'd like to embed ThickBox into your blog just install this plugin,
insert ThickBox compliant markup whereever you want and you're all set.

Also contains SmoothBox.

== Installation ==

1. Unzip the plugin into your wp-content/plugins directory and activate it
2. [Integrate](http://www.christianschenk.org/projects/wordpress-thickbox-plugin/#howto) it into a post or page.

== Screenshots ==

1. Some pictures in a gallery.
2. If you click on a picture you'll see the larger image.

== Changelog ==

1.4

* New admin interface with various options
* Added german translation for ThickBox and SmoothBox
* You may now add variants of ThickBox and SmoothBox as you see fit
* Plugin supports i18n now

1.3.1

* Small bugfix regarding jQuery.

1.3

* Updated ThickBox (3.1) and SmoothBox (v20080623) to latest version.

1.0 - 1.2.x

* First drafts

== Expert Mode ==

If you don't want to include the CSS and JavaScript in each and every
page do this:

* open "thickbox.php" and set "INCLUDE_JS_AND_CSS_EVERYWHERE" to _false_
* add a custom field "thickbox" to those posts/pages that actually use ThickBox

This way the CSS/JS will only be included if the custom field is
present.

== Using Smoothbox instead of ThickBox ==

If you want _valid_ CSS and JavaScript, you might want to use
[Smoothbox](http://gueschla.com/labs/smoothbox/) instead of the original
ThickBox. To do this, open "thickbox.php" and change the value of
"INCLUDE_SMOOTHBOX" to "true". Once you've done that the CSS and
JavaScript for Smoothbox will be included.

== Licence ==

This plugin is released under the GPL.
