=== Sk Multi Tag ===
Contributors: skipstorm
Tags: multi tag, tag, wordcloud, tagcloud, widget
Requires at least: 2.4
Tested up to: 2.7.1
Stable tag: trunk

This plugin adds a tag cloud widget where you can select multiple tags at once.

== Description ==

This plugin adds a tag cloud widget where you can select multiple tags at once.


== Installation ==

How to install the plugin and get it working.

1. Upload the plugin folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Place the widget in your sidebar and customize it from the 'Widget' menu in WordPress
4. You can change "single tag title('', false)" to "skmultitag tag title()" (change the space with underscore) in your template if you're displaying the "Posts Tagged ..." message. This will show all the selected tags instead of one.

= Customizing the CSS =
You can customize the look of the tagcloud by adding a stylesheet for skwr_removetags and skwr_addtags in your css file.

e.g.
this will make the tags in the romeve tag cloud red

 #skwr_removetags a{
    color:#ff0000;
}

== Screenshots ==

1. skmultitag.png