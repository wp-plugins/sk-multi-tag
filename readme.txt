=== Sk Multi Tag ===
Contributors: skipstorm
Tags: multi tag, tag, wordcloud, tagcloud, widget
Requires at least: 3.0
Tested up to: 3.0
Stable tag: trunk

This plugin adds a tag cloud widget where you can select multiple tags at once.

== Description ==

This plugin adds a tag cloud widget where you can select multiple tags at once.

ATTENTION! This is a totally different plugin so if you're updating from the version 0.6.x you'll have to configure it again.
Visit the plugin homepage to download the old version if you want to downgrade.

= Update =

1.0.2
*	Bug fix for utf8 slugs thanks to Alexandros
*       Bug fix + template functions, check comments on plugin homepage

1.0.1
*	Bug fix about random order

1.0
*	Now is a multi instance widget
*	Supports modules
*	Added wp cumulus module (flash file courtesy of weefselkweekje and LukeMorton)
*	Any tag size has it's own css class allowing a better customization
*	Added several options

0.6.2
*	Removed bug causing some tags not to be displayed, thanks to Felipe from turismocoquimbo.com

0.6.1
*	This version removes the tags that will lead to no articles in the add tag list.
You might want to install wp-cache to avoid performance issues.

= System requirements =
I've only tested this plugin on wordpress 3.0 but it should work down to version 2.8
This plugin requires php version 5.0 or above

== Installation ==

How to install the plugin and get it working. This might be a little tricky so read carefully.

1. Upload the plugin folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to the plugin admin page in settings > sk multi tag and create your first cloud style
4. Place the widget in your sidebar and select the style created on step 3. Save.
5. Use function SKMultiTag::selectedTags to get the list (String) of selected tags if you want to add it on your template.

- Check admin panel for more informations

= Customizing the CSS =
You can customize the look of the tagcloud by editing your style.css file. Check plugin options

== Screenshots ==

1. The widget frontend

2. Option panel

3. Widget instance options

== Developers ==

This plugin supports modules, check out the files in modules folder if you want to create your custom cloud style.