=== CUUSOO List ===
Contributors: legendarydrew
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=89QB8KQSAQ3RE
Tags: cuusoo, lego, list, widget
Requires at least: 3.5
Tested up to: 3.5
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Maintains a list of LEGO CUUSOO projects to display in a widget.

== Description ==

Maintains a list of LEGO CUUSOO projects to display in a widget.

This plugin will allow the user to maintain a list of specific projects on the LEGO CUUSOO web site, and display
them on their WordPress site using a widget. Data for each specified project is obtained from the LEGO CUUSOO web
site and made available for display. The widget's template can be customised to get the layout you want.

An dashboard widget that displays the list of projects, along with any changes in the number of supporters, is also
added. This will be displayed by default, but can be turned off via the 'Screen Options' menu.

I developed this plugin for [SilentMode.tv](http://silentmode.tv) to feature my own CUUSOO projects:

* CATAWOL Records modular building http://lego.cuusoo.com/ideas/view/1760
* Graduates and Gorillas: the game http://lego.cuusoo.com/ideas/view/15356
* CATAWOL Records Studio One http://lego.cuusoo.com/ideas/view/20284

If you have any suggestions for improvements to this plugin, feel free to contact me through
http://silentmode.tv/contact/.

== Installation ==

1. Extract and copy the 'cuusoo-list' folder to the '/wp-content/plugins/' directory.
1. Activate the 'CUUSOO List' plugin through the 'Plugins' menu in WordPress.
1. Add the LEGO CUUSOO projects you want to track via the 'Settings > CUUSOO List' menu.
1. Add the 'CUUSOO List' widget to a widget placeholder via the 'Appearance > Widgets' menu.

The default template for the widget is 'widget-cuusoolist.php' in the plugin folder, which displays a basic unordered
list. This can be overridden by creating a 'widget-cuusoolist.php' template file in your theme folder.

== Data fetching ==

Data for each project is fetched as soon as a project is added, and then once a day afterward.

There are two methods available for fetching project data from the LEGO CUUSOO web site:

* **API**: this fetches JSON data for the project. This is the fastest and least intrusive method, however only the
number of supporters and bookmarks for the project is made available. You can use the label field to give each project a
title.
* **Page scrape**: this extracts project data from the project's page. While more project data (title, thumbnail and
number of views) is made available, this method is likely to generate unwanted page views. If the extra data isn't
necessary, please stick with the API method.

The data fetching method can be set via the 'Settings > CUUSOO List' menu. Any changes will take effect on the next
fetch.


== Screenshots ==

1. This is the default interface: the API data fetching method is selected by default.
2. This is the interface when the page scrape method is selected: not the presence of extra columns in the list.
3. A demonstration of the default CUUSOO List widget.
4. An example of a custom CUUSOO List widget template, as used on [SilentMode.tv](http://silentmode.tv).

== Frequently Asked Questions ==

None yet...

== Upgrade Notice ==

None yet...

== Changelog ==

= 1.3.3 =
* Corrected some general schoolboy errors with the code, namely with GET parameters and function calls.
* Fetching data via API no loger "forgets" the manually defined label.

= 1.3.1 =
* Better plugin deactivation handling: only the events for fetching project data should be removed. Project data now
isn't removed unless the plugin is deleted (via the Plugins menu).

= 1.3 =
* Fixed a major issue with the fetching event, where more than one could be in effect at once. An absolute nightmare if
you happen to be page scraping.
* Labels were not being saved.
* Corrected messages!

= 1.2 =
* First publicly available version.

== Disclaimer ==

LEGO is trademark of The LEGO Group. CUUSOO is a trademark of CUUSOO SYSTEM Co., Ltd. and Elephant Design Co. Ltd.
None of these companies has anything to do with this plugin.
