=== Plugin Name ===

Contributors: StuckOn_dev, nickarkell
Tags: parent page, dynamic list, parent, classic editor
Requires at least: 3.5.1
Tested up to: 6.1.1
Stable tag: 2.1.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Dynaparent (Dynamic Parent) makes it much easier to choose a parent page on websites that feature a huge number of pages and subpages. 

== Description ==

This plugin is only neccessary when using Classic Editor because Gutenberg Editor has dynamic parent selection built-in.

Dynaparent – Dynamic Parent. This is ideal for websites that feature a huge number of pages and subpages. When adding a new page within WordPress, this plugin makes it much easier to choose a parent page. 

Dynparent replaces the standard dropdown list selection method for parent pages with a dynamic search box. As you begin to type the name of the parent page you want, a list appears of the parent pages matching that criteria. The list will update with each new character typed.

This is designed for sites with a large selection of parent pages.

== Installation ==

1. Upload 'dynaparent' folder to the '/wp-content/plugins/' directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Ensure you have parent page checked in the 'Screen Options' panel of your page edit screen.

== Frequently Asked Questions ==

== How do I select the parent page when I can see it in the list?

Simply click the parent page, it will become bold and will fill in the field with that value, then make sure you publish/update your post.

== Do I have to start with the first letters of the parent page’s name?

No, the plugin will search any part of the parent page’s name. For instance, to find ‘Steven’, you could start to type ‘even’ and it will appear in the list of potential matches.

== What if I have many pages and subpages with the same name?

The plugin was designed for this very reason, and is ideal for websites that feature multiple pages and subpages with similar or identical names. The parent pages are displayed in the list with their full path, so you can easily select the correct page from the displayed list.

== Screenshots ==

1. The Dynaparent box in the right sidebar of a page edit screen.

== Changelog ==

= 1.0 =

* n/a

= 2.0.0 =

- Plugin fault diagnosed and fixed using a different method to search through pages dynamically
- Interface overhaulled to look better and improve the UX
- Menu order re-added into Page Attributes box so it is not lost as an option when using the plugin
- Template selection reverted back to original WordPress dropdown box
- readme.txt updated to reflect changes
- screenshot-1.png updated to reflect changes
- Restricted the customised 'Page Attributes' box for this to only appear when not using Gutenberg Editor (because Gutenberg / Block Editor has this functionality built-in)

= 2.1.0 =

- Alter two lines of jQuery to replace $ with jQuery to prevent an error