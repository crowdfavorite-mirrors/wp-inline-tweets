=== Inline Tweets ===

Contributors: mdjekic
Donate link: http://milos.djekic.net/my-software/inline-tweets
Tags: tweeter,tweets,inline,embed,styles
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Requires at least: 2.7
Tested up to: 3.8
Stable tag: 2.0

Embed latest tweet(s) or a specific tweet in any page, post or text widget and apply your custom styles.




== Description ==

This simple plugin will enable you to embed tweets into your posts, pages and text widgets and apply YOUR CUSTOM STYLES. It comes down to adding a simple snippet into your post, page or text widget content. Here's an example:

`[tweet]latest|user=WordPress,count=5[/tweet]`

This snippet will display five last tweets made by @WordPress with an intuitive HTML markup to which you can easily apply your custom CSS:

`.inline_tweet { background-color: whitesmoke; border: 2px solid black; }`

Fetching tweets will be done in the background and will not slow down the displaying of your website or blog. You will see an unobtrusive transition placeholder before the tweets are loaded.

You like that? Take a look at the [How to use](http://wordpress.org/plugins/inline-tweets/other_notes/#How-to-use) and [Markup and Styling](http://wordpress.org/plugins/inline-tweets/other_notes/#Markup-and-Styling) to learn what you can do.

= Caching =

Caching will improve performance when fetching tweets and ensure that the plugin operates within Twitter API limits. To enable caching, please install [W3 Total Cache](http://wordpress.org/extend/plugins/w3-total-cache/) and enable 'Object Cache' in plugin settings.

= Configuration =

SinceTwitter introduced it's API version 1.1 you need to create a Twitter application to be able to use it and thus to use this plugin. It's not hard, you will spend a couple of minutes to create an app and obtain tokens and keys you will use for configuring Inline Tweets. You will have to do that only once, so no problem.

= Author =

[Miloš Đekić](http://milos.djekic.net) is a software solutions architect from Belgrade, Serbia. He loves to create useful software.

= Credits =

Credits should be given when credits are due. Inline tweets use a great Twitter API wrapper library created by [Abraham Williams](https://abrah.am/) to communicate with Twitter. You can find it [here](https://github.com/abraham/twitteroauth)




== Installation ==

Just follow this simple guidelines.

Installation steps

1. Upload 'inline-tweets' to the '/wp-content/plugins/' directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Visit plugin settings page to configure your Twitter keys and tokens
1. Include inline tweet snippets in your pages, posts or widgets
1. Customize inline tweets with your own CSS
1. That's it. Enjoy!




== How to use ==

The usage depends on what you want to display. Only a couple of ways of displaying tweets exists but the list of applications will be expanded in the future. Make sure you take a look at the "Snippet Options" and learn how to take control over what is displayed and how.

= Single Tweet =

This is useful when you want to embed a tweet in your blog article or page.  Just use this snippet:

`[tweet]single|id=tweet_id[/tweet]`

Replace "tweet_id" with the ID of the Twitter status you want to display. That's it.

= Latest Tweets =

This will probably be the most common application of the plugin. If you want to display a list of your (or someone elses) latest tweets in your sidebar and/or pages/articles, use this snippet

`[tweet]latest|user=twitter_user[/tweet]`

Replace "twitter_user" with the username of the account whose tweets you want to display. You can limit the number of tweets that will be fetched and displayed by adding a "count" param. Take a look at the following example:

`[tweet]latest|user=twitter_user,count=10[/tweet]`

Note: Default number of tweets that will be fetched is 5 and the maximum you can set is 25. If you set more that the maximum you will get the default 5 tweets.

= Display Options =

There are various options to help you customise what will be displayed by the plugin for a particular snippet. You can include a custom option by adding the option string after a comma sign.

Here's a list of all options with examples:

**time_format**

You can select the time format inline. Have in mind that if the format you set is not supported the time will be displayed in default format Here's an example:

`[tweet]latest|user=twitter_user,time_format=M Y[/tweet]`

**hide_author**

You can easily remove the author from display by simply adding the option:

`[tweet]single|id=tweet_id,hide_author[/tweet]`

**hide_avatar**

You can easily remove the avatar from display by simply adding the option:

`[tweet]single|id=tweet_id,hide_avatar[/tweet]`

**hide_timestamp**

You can easily remove the timestamp from display by simply adding the option:

`[tweet]single|id=tweet_id,hide_timestamp[/tweet]`

**full_names**

By default tweets are displayed with their authors Twitter names. If "full_names" param is set they will be displayed with their full names:

`[tweet]latest|user=twitter_user,count=15,full_names[/tweet]`




== Markup and Styling ==

Inline tweets are rendered with a recognizable HTML markup that you can use to display them in virtually every way possible using CSS. For starters, every tweet is wrapped in an element with class "inline_tweet". Then every displayed element has it's own class and rules.

= Tweet Content =

**Author**

Author's Twitter name or full name is displayed as a link to his/her Twitter page:

`<a class="inline_tweets_author" href="http://twitter.com/author" target="_blank">@author</a>`

You can style the link by referencing the "inline_tweets_author" class (e.g. different color for plugin author than other users mentions).

**Avatar**

If displayed, the avatar is rendered as a link to author's Twitter page also, with the addition of an image element:

`<a class="inline_tweets_avatar" href="http://twitter.com/author" target="_blank">
    <img src="http://pbs.twimg.com/profile_images/author_image.jpeg">
</a>`

You can style the link in terms of positioning and the image display by referencing the "inline_tweets_avatar" class.

**Mentions**

Mentions are also displayed as link to author Twitter pages and you cal easily style them by referencing the "inline_tweets_mention" class:

`<a class="inline_tweets_mention" href="http://twitter.com/mention" target="_blank">@mention</a`

**Hashtags**

Hashtags are wrapped in span elements enabling you to apply your custom styles (e.g. display in bold or italic) by referencing the "inline_tweets_tag" class:

`<span class="inline_tweets_tag">#tag</span>`

**Timestamp**

If you decide to display the timestamp, you can easily style it - in terms of positioning for starters - by referencing the "inline_tweets_timestamp" class. Here's how the timestamp is rendered:

`<span class="inline_tweets_timestamp">Jan 2014</span>`

= Single Tweet =

Here's an example of how a single tweet is displayed:

`<div class="inline_tweet">
    <a class="inline_tweets_author" href="http://twitter.com/ArchdukeM" target="_blank">@ArchdukeM</a>
    <a class="inline_tweets_avatar" href="http://twitter.com/ArchdukeM" target="_blank">
        <img src="http://pbs.twimg.com/profile_images/image.jpeg">
    </a>
    "She represented everything I loved about the English. She was totally deranged."
    <span class="inline_tweets_timestamp">Jan 2014</span>
</div>`

= Multiple Tweets =

When more that one tweet is displayed it is rendered as a list. To cut it short, here's an example markup:

`<ul class="inline_tweets_ul">
    <li class="inline_tweet">
        CONTENT
    </li>
</ul>`

Let's analyse the differences and the advantages. You have a ul element with a class "inline_tweets_ul" that you can use if you want to position the entire list, enable scrolling or whatever you want. Instead of a div element, there's a li element with the "inline_tweet" class for wrapping the tweet. Tweet content is rendered in the same way as single tweets.




== Screenshots ==

1. Entering the snippet in a text widget (syntax highlight done by Synchi plugin)
2. Latest tweets displayed in the sidebar




== Changelog ==

= 2.0 =
* A complete rewrite to support Twitter API 1.1
* User avatars in tweets
* Inline options in snippet (show/hide: author, avatar, timestamp; different timestamp formats, full author names)

= 1.1.2 =
* Fixed a small bug with some escaped characters appearing in blogs

= 1.1.1 =
* Fixed a bug reported by Mr. Michael Albers where not all inline tweets were shown in a single post

= 1.1 =
* Added Twitter username check
* Added tweets caching to improve performance and comply with Twitter API limits

= 1.0.2 =
* Applied a small fix to handle a reported issue with fetching tweets within pages

= 1.0.1 =
* Added a small fix to handle potential SSL problems when fetching tweets

= 1.0 =
* First version - inline tweets on.




== Upgrade Notice ==

Inline Tweets now work with Twitter API 1.1! Upgrade to continue using the plugin with a lot of additional options.