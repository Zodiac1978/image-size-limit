=== Image Size Limit ===
Contributors: zodiac1978, bootsz
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=LCH9UVV7RKDFY
Tags: images, media, size, uploads, limit
Requires at least: 5.8
Tested up to: 7.1
Requires PHP: 7.4
Stable tag: 1.1.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Adds a new setting under Settings -> Media where an admin can set a maximum upload file size for image files.

== Description ==

Many users do not compress or resize their images before uploading them into a post, and WordPress's maximum upload limit can still be too large to prevent photos that significantly slow down a website.

Image Size Limit allows an administrator to set a custom file size limit that is specific to image files and smaller than WordPress's general file size limit. It is a maintained continuation of the discontinued WP Image Size Limit plugin.

This is especially useful when you need to put tighter restriction on image uploads but want to preserve the ability to upload larger files of other formats (audio, video, etc.).

== Installation ==

1. Upload the plugin ZIP on the Plugins page or search for `Image Size Limit` and install it directly from the plugin directory.
1. Activate the plugin through the 'Plugins' page in WordPress.
1. Go to Settings -> Media and set up the limit for your images.

== Frequently Asked Questions ==

= I would like to report a bug. Where can I do this? =

Please open up an issue on [GitHub](https://github.com/Zodiac1978/image-size-limit/issues)!

== Screenshots ==

1. The limit can be set in the Media panel under Settings.
2. The media uploader will display both limits.

== Changelog ==

= 1.1.0 =
* Prevent duplicate hook registration.
* Use WordPress file type checks for reliable image detection.
* Correct and consistently format upload size calculations.
* Replace global uploader CSS with a scoped media-screen notice.
* Improve validation, escaping, translations, and metadata.
* Add automated tests and expanded continuous integration.

= 1.0.5 =
* Fixed coding standards.
* Made the plugin translatable.

= 1.0.4 =
* Latest version from Sean Butze.
