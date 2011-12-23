=== In-Context Comment ===
Contributors: Wizag LLC
Donate link: http://wizag.com/incontext.php
Tags: Comments, Blog, Context
Requires at least: 2.8
Tested up to: 3.2.1
Stable tag: 0.8.2

"In-Context Comment" lets readers leave comments right next to the content being commented, instead of only at the bottom of the blog post

== Description ==

Comments are extremely critical for the success of a blog. All existing blog platform and commenting plugins only let readers add comments all the way at the bottom of a post, out of the context of the content in the post. When you write a long blog post with, say, 10 paragraphs, and readers are commenting on a particular statement or expression in paragraph 3, they have to scroll back and forth to read the context to figure out what the comments are about. The "In-Context Comment" plugin changes that: Now you can add an "In-Context Comment" icon using &lt;in-context-comment:here:tag&gt; (where "tag" is any word or words connected by hyphen ) at any place you want readers to comment on and they will be able to click and open a window to add comments right there, next to the context so other readers can see both the context and the comments in one glance. The comment window automatically closes when a reader clicks the cursor anywhere outside the comment window so it does not interfere with the reading.

This plugin can also help you grow your readership by posting the comments to the commenters' Facebook and Twitter status updates to bring in new readers to your blog.
You can also configure the "In-Context Comment" plugin to automatically add a comment icon at the end of each paragraph that is longer than a certain number of characters. This auto feature is enabled by default with a minimum character count of 360. Please go to the plugin's Settings page to change.

== Installation ==

You can either use the "Install new plugin" on Wordpress, or do the following:

Download the zip file (in-context-comments.zip)
Unpack the zip file. You should have a folder called 'in-context-comments', containing several PHP files
Upload the 'in-context-comments' folder to the 'wp-content/plugins' folder on your WordPress server. It is important you retain the 'in-context-comments' directory structure

Note: This plugin will only affect new posts you write after the installation.

== Frequently Asked Questions ==

= How to use In-Context Comment? =

First make sure you have activated this plugin.
You have complete control on where add "In-Context Comment" icons in each blog post using the following three commands:
Add &lt;in-context-comment:auto-on&gt; anywhere in a blog post (in HTML edit mode) to turn on the function to automatically add an In-Context Comment icon at the end of each paragraph that is longer than a certain number of characters (see command below). The auto mode is on by default for all posts. You can use &lt;in-context-comment:auto-off&gt; to turn this function off in a post you don't want in context comments.
&lt;in-context-comment:block-size:N&gt; where N is a positive integer, e.g., 350. When the auto mode is on, this command controls the minimum number of characters a paragraph must have for an In-Context Comment icon to be added at the end of the paragraph. This is to avoid adding icons to a very short paragraph.
&lt;in-context-comment:here:tag&gt; where "tag" is any word or words connected by hyphen, e.g., first-comment. You can add this command at any place inside a post where you want readers to leave comments. This command works regardless whether the auto mode is on or off.
When the auto mode is on, if you need to edit a post, in the HTML edit mode, you will see a command &lt;in-context-comment:autotag&gt; (where autotag is an auto generated tag for the icon) at the location of each In-Context Comment icon. You can cut and paste the command &lt;in-context-comment:autotag&gt; to move the comment to any place in the post.
In the plugin's configuration page, you can turn on/off the auto mode and change the minimum block-size globally for all posts. The configuration here will apply to all posts unless you use the commands above to change it for an individual post.

Note: This plugin will only affect new posts you write after the installation.

= Does this plugin requre users to log in to leave comments? =

Yes, a user needs to log in via OAuth using Facebook or Twitter. Their login credientials are with Facebook or Twitter. This plugin does not have access to users' password or other confidential information. The login is entirely handled by Facebook or Twitter. When a user grants the permission for this plugin to post to their Facebook or Twitter, this plugin submits the request to Facebook or Twitter, who verifies the permission and posts it to the user's wall or status updates.

== Documentation ==

Visit http://wizag.com/incontext.php for example and documentation

== Changelog ==

= 0.8.2 =
* This version fixes a function related bug.
* Another change.

= 0.8.1 =
* First public release

* Auto and manual add In-Context Comment icons
* Support login using Facebook, Twitter and Google, and publish comments in commenter's Facebook or Twitter status updates.