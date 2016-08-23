# RapidFire #
Contributors: patilswapnilv, shefalishirodkar20
Plugin Name: RapidFire
Plugin URI: https://github.com/patilswapnilv/rapidfire
Tags: quiz, test, jquery, javascript, education, elearning, generator, manager, question, answer, score, rank
Author URI: http://swapnilpatil.in/
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/gpl.html
Requires at least: 3.0
Tested up to: 4.6
Stable tag: 1.0

*RapidFire is a plugin for displaying and managing pretty, dynamic quizzes. It uses the RapidFire jQuery plugin.*


## Description ##

**Create and manage pretty, dynamic quizzes** using the RapidFire jQuery plugin.**

Managing and creating new quizzes is simple and intuitive.

* **Unlimited** questions, unlimited answers.
* **Save** user scores (must be enabled in the options).
* **Share** results via Twitter and Facebook sharing buttons.

More Features:

* Questions can have single or multiple correct responses.
* Answers have correct and incorrect response messaging.
* Show correct / incorrect response message after each question and / or at the end of the quiz.
* End results include a score (8/10) and customizable ranking (ex. Super Genius).
* Quiz changes can be saved to a draft.
* Randomly sort questions and answers.
* Customize button text, as well as score and ranking text.
* Customize error messages for removed or unpublished quizzes.
* Load a set number of questions from a larger group.
* Prevent submitting questions without answers.
* Allows multiple quizzes on the same page.
* Save user emails along with quiz scores.


## Installation ##

1. Install the RapidFire plugin directly from the WordPress plugin directory. Or upload the RapidFire plugin to your `/wp-content/plugins/` directory.
1. Activate the plugin through the "Plugins" menu in WordPress.
1. Create / publish quizzes via the "RapidFirezes" option in the WordPress sidebar.
1. To add a quiz to a post or page, place `[rapidfire id=X]` the content area, where `X` is the ID of the quiz you created. The ID can be found in the main RapidFire listing.

# Dynamic URL Shortcode Setup #

You may also dynamically render a quiz by setting the shortcode to `[rapidfire id=url]`.  This will tell the plugin to look for an ID at **the end of the page URL** and select the quiz with that ID. Note: additional query string parameters will not interfere.

# Text Widget Setup #

To use the `[rapidfire id=X]` shortcode in the sidebar Text widget, add the following to yours theme's `functions.php` file.

`add_filter( 'widget_text', 'do_shortcode' )`

# Developer Hooks #

There are currently three filter actions that you may hook into:

`rapidfire_admin_options` This allows you to add custom admin options.

`rapidfire_after_options` This allows you to add custom markup to the bottom of RapidFire Options form (you would likely add data to your custom `rapidfire_admin_options` here).

`rapidfire_after_result` This allows you to add custom markup to the bottom of the quiz results area at the end of the quiz (you would likely output data from your custom `rapidfire_admin_options` here).

For an example of how to utilize these hooks, see this
[gist](https://gist.github.com/patilswapnilv/f2a7c9721f8aa1060afa2c3321559e71).

# Saving Additional Quiz Data #

It is possible to store extra data along with the main quiz JSON object. This would be useful if you're extending the quiz or integrating it with another plugin.

There is currently one JavaScript callback that you may use to add data when a quiz is saved to a draft or published. It will save extra data to an attribute named "extra" in the quiz JSON object. You would call it like below:

`jQuery(document).ready(function($) {
  $.fn.setPreSaveQuiz(function () {
    // Append some "extra" data to the RapidFire POST.
    return { some: 'data', another: 'piece of data' };
  });
});`

There is also a `rapidfire_save_quiz` WordPress action you can use to grab the quiz and extract the "extra" data when the quiz is saved to a draft or published. You might set up something like this:

`class YourCustomClass {
  function __construct()
  {
    add_action('rapidfire_save_quiz', array( &$this, 'custom_quiz_data_action' ));
  }

  function custom_quiz_data_action( $quiz, $mode = 'create_draft' )
  {
      $data  = json_decode( $_POST['json'] );
      $extra = $data->extra;
      // Do custom stuff
  }
}`

Note, the `$mode` option will return one of the following values: 'create_draft', 'create_published', 'update_draft', 'update_published'



## Frequently Asked Questions ##

# Why can't I see the quiz Preview? #

The quiz Preview opens in a popup window. You may need to allow popups for the domain in your browser. Check the URL bar for a popup-blocked indicator and click it for more information.

# Why isn't the quiz showing up on mypage? OR why is the quiz broken? #

There are a lot of reasons this could be happening - usually there is an issue with the theme or a conflict with another plugin. Here are a few things to look for:

* Make sure your theme's `footer.php` template contains the following code snippet - this ensures that plugins are able to add necessary code to the page.

`<?php wp_footer(); ?>`

* Check your browser's Development Console for errors. Click [here](http://webmasters.stackexchange.com/questions/8525/how-to-open-the-javascript-console-in-different-browsers) for instructions on how to find to this panel in your browser (Note: if you're on a Mac, use the `Command ⌘` key in place of `CTRL`). Once you've got it open, look for anything in red - it will all look pretty foreign, but if you see anything in red, scan the text for keywords that might indicate the plugin the error is coming from. Try disabling those plugins and then see if the quiz loads.
* Still having trouble? Create a new [support ticket](http://wordpress.org/support/plugin/rapidfire/) with the details of your issue.

# Why does my quiz have weird spacing or strange colors? #

You may have pasted the shortcode into the "Visual" mode of the page / post text editor, which may have wrapped the shortcode in extra, unnecessary `code` tags.

To check for this, go to the edit view of the post or page where you entered the shortcode and toggle the editor to "Text" mode (this option is the 2nd tab in the upper right of the text editor). Now, look for your shortcode and remove any `<code>` tags that may be surrounding it.

If you do not find any `code` tags, there may be other CSS conflicts with your theme to address. Please create a new [support ticket](http://wordpress.org/support/plugin/rapidfire/) with the details of your issue.

# Can I add pictures / videos / media / HTML to my questions / answers / responses? #

Yes, you can place any HTML tags that you like within any of the content fields. For images, get the URL of the image you want to add, and use something like the following to add the image to a content area:

`<img src="/uploads/2014/02/my_photo.jpg">`

# Is there a way to email user scores and answers? #

Yes, if you "Enable sharing buttons" from the RapidFire Options page, your users will have a button to email their results at the end of the quiz.  Currently, this feature does not capture all answers - only the user's overall score and ranking.

# How do I make sure Facebook share includes my quiz image? #

To customize Facebook share content, it's best to use a plugin like: [Add Meta Tags](http://wordpress.org/plugins/add-meta-tags/) to set [Facebook's Open Graph meta](https://developers.facebook.com/docs/plugins/checklist/) data on a page by page basis.

Each Facebook Open Graph meta data tag that you enter should look something like this:

`<meta property="og:image" content="http://www.xxx.com/images/xxx.jpg" />`

# Can I put the same quiz on the same page multiple times? #

Nope, things will break.  This might happen if you place the same quiz within multiple blog posts and more than one of those posts is displayed on the page.

# I've got an idea for a feature or have a bug to report, what should I do? #

Checkout the [RapidFire WordPress Support forum](http://wordpress.org/support/plugin/rapidfire/) to see if someone else has experienced your issue, the answer might already be there; if not - please create a new support ticket!

Also, see the [RapidFire Issues](https://github.com/patilswapnilv/rapidfire/issues) page on Github for a complete list of upcoming features and bug fixes, and feel free to add your own ideas!


## Screenshots ##

1. The quiz management / listing interface.
2. Creating a quiz.
3. Adding quiz questions.
4. A quiz embedded in a post - your styles will vary depending on your theme and preferences.
5. The plugin options allow you to alter messages and quiz features.
6. When user score saving is enabled, the user will be prompted for their name before starting the quiz, unless they're already logged in.
7. The listing of user scores when saving is enabled.

## Usage ##

1. Once the plugin is activated you should check Settings > General, to find options to add your URL's.
2. Insert URL for redirect on login.
3. Insert URL for redirect on logout.
4. Save!
  
## Before you deactive OR uninstall ##

I strong suggest you delete the redirect URL's from Setting  => General and save the settings before you deactive or delete the plugin.
Just the make sure the values are replaces with the default once.


## Changelog ##

= 1.0 =
This is the initial setup of the plugin.


## Upgrade Notice ##

= 1.0 =
This is the first version of the plugin
