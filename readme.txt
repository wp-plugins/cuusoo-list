=== CUUSOO List ===
Contributors: legendarydrew
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=89QB8KQSAQ3RE
Tags: cuusoo, lego, lego ideas, list, widget
Requires at least: 3.5
Tested up to: 3.9
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Maintains a list of LEGO Ideas (formerly CUUSOO) projects to display in a widget.

== Description ==

Maintains a list of LEGO Ideas (formerly CUUSOO) projects to display in a widget.

This plugin will allow the user to maintain and display a list of specific LEGO Ideas projects on their WordPress site,
by way of sidebar widgets. The templates used for displaying the widgets on your site can be easily customised, to
display as little or as much information as you want, and how you want it, without having to dig through cryptic code.

A dashboard widget that displays your list of projects is also provided, which highlights any change in the number of
each project's supporters. As with other dashboard widgets it can be turned off via the 'Screen Options' menu.

I originally developed this plugin for [SilentMode.tv](http://silentmode.tv) to promote my own CUUSOO projects, but
though they were unsuccessful, the plugin can still be used to help others.

If you have any suggestions for improvements to this plugin, contact me through the form at
http://silentmode.tv/contact/.

== Installation ==

1. Extract and copy the 'cuusoo-list' folder to your '/wp-content/plugins/' directory.
1. Activate the 'CUUSOO List' plugin through the 'Plugins' menu in WordPress.
1. Add the LEGO Ideas projects you want to track via the 'Settings > CUUSOO List' menu.
1. Add one of the provided 'CUUSOO List' widgets to a widget placeholder via the 'Appearance > Widgets' menu.

CUUSOO List comes with three widgets:

* **List:** displays a list of selected projects; by default it will show all listed projects.
* **Random:** displays a randomly chosen project from the ones selected; by default it will choose from all listed projects.
* **Single:** displays a single chosen project.

The default template for all three widgets is 'widget-cuusoolist.php' in the plugin folder, which displays the projects
in a very basic ordered list. This template can be overridden by creating a 'widget-cuusoolist.php' template file in
your theme folder.
You can customise further by creating widget-cuusoolist-list.php, widget-cuusoolist-random.php and
widget-cuusoolist-single.php to target list, random and single widgets respectively.

If you'd prefer to do your own coding, you can call CUUSOOList::get() to obtain the list of projects (indexed by their
project ID), and CUUSOOList::last_update() to obtain the date of the last project data fetch.

== Data fetching ==

Data is fetched for a project as soon as it's added, and then once a day thereafter.

As the LEGO Ideas site doesn't have an API, data is obtained via dreaded page scraping: this means that extra, unwanted
page views will be generated every time data is fetched for each project. Please be respectful of this when you use the
plugin.

== Screenshots ==

1. This is the default interface: the API data fetching method is selected by default.
2. This is the interface when the page scrape method is selected: note the presence of extra columns in the list.
3. A demonstration of the default CUUSOO List widget.

== Frequently Asked Questions ==

None yet...

== Upgrade Notice ==

None yet...

== Changelog ==

= 2.1 =
* All three widgets now use the widget_cuusoolist class name.
* Corrected use of widget-cuusoolist-* theme templates.

= 2.0 =
* LEGO CUUSOO has become LEGO Ideas and has completely changed.
* CUUSOO List has an updated (and nicer) interface, along with a menu icon.
* Projects can now only be added or deleted; there's no need to edit them.
* Fetching method is no longer a setting.
* More information about each project is available: see widget-cuusoolist.php.
* Three widgets instead of one have been provided: list, random and single.
* Replaced references to LEGO CUUSOO with LEGO Ideas.

= 1.4 =
* I no longer have my own CUUSOO projects.
* CUUSOO List is now a top-level menu page.
* Form data is now POSTed instead of using GET.
* Code was generally cleaned up, including using WordPress' validation functions.

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
Neither of these companies has anything to do with this plugin.
