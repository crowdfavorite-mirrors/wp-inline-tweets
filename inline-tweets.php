<?php

// =============================================================================
// Inline Tweets
//
// Released under the GNU General Public Licence v2
// http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
//
// Please refer all questions/requests to: mdjekic@gmail.com
//
// This is an add-on for WordPress
// http://wordpress.org/
// =============================================================================

// =============================================================================
// This piece of software is distributed in the hope that it will be useful, but
// WITHOUT ANY WARRANTY, without even the implied warranty of MERCHANTABILITY or
// FITNESS FOR A PARTICULAR PURPOSE.
// =============================================================================

// =============================================================================
// Plugin Main File
// =============================================================================

/*
  Plugin Name: Inline Tweets
  Plugin URI: http://milos.djekic.net/my-software/inline-tweets
  Description: Embed latest tweet(s) or a specific tweet in any page, post or text widget and apply your custom styles.
  Version: 2.0
  Author: Miloš Đekić
  Author URI: http://milos.djekic.net
 */

// check if direct access attempted
if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
{
    // forbid access
    header('HTTP/1.1 403 Forbidden');
    exit('Direct access not allowed.');
}

// define inline tweets version
define('INLINE_TWEETS','2.0');

// load configuration
$inline_tweets_config = json_decode(file_get_contents(__DIR__ . '/config.json'));

// ---------------------------------------------------------------------------------------------------------------------
// Functions
// ---------------------------------------------------------------------------------------------------------------------

/**
 * Loads options
 *
 * @return stdClass
 */
function inline_tweets_loadOptions()
{
    // load options from database
    $options = get_option('inline_tweets_options');

    // render json
    $json = @json_decode($options);

    // return options
    return ($json === false || empty($json)) ? new stdClass() : $json;
}

/**
 * Saves options
 *
 * @param stdClass $options
 */
function inline_tweets_saveOptions($options)
{
    // update options in the database
    update_option('inline_tweets_options',json_encode($options));
}

/**
 * Returns the error HTML entity
 *
 * @param string $error text to be displayed as error
 * @return string HTML
 */
function inline_tweets_inline_error($error = "Error")
{
    return '<div class="inline_tweets_error">'.$error.'</div>';
}

/**
 * Checks if Twitter is configured
 *
 * @param stdClass $options
 * @return bool
 */
function inline_tweets_twitterConfigured($options)
{
    // go trough all configuration items
    foreach(array('consumer_key','consumer_secret','oauth_token','oauth_token_secret') as $item)
    {
        // make sure item is present
        if(!isset($options->{$item})) return false;

        // make sure item is not blank
        if(empty($options->{$item})) return false;
    }

    // all configuration items are present
    return true;
}

/**
 * Handles an inline tweet snippet
 *
 * @return stdClass result
 */
function inline_tweets_action_handleSnippet()
{
    // get snippet text
    $snippet_text = isset($_REQUEST['snippet']) ? $_REQUEST['snippet'] : '';

    // init result
    $result = new stdClass();

    // load options
    $options = inline_tweets_loadOptions();

    // make sure Twitter is configured
    if(!inline_tweets_twitterConfigured($options))
    {
        // set error text
        $result->html = inline_tweets_inline_error('Inline Tweets not configured!');

        // stop execution and return result
        return $result;
    }

    // include dependencies
    include(__DIR__ . '/lib/twitteroauth/OAuth.php');
    include(__DIR__ . '/lib/twitteroauth/twitteroauth.php');
    include(__DIR__ . '/core/Snippet.php');

    // init Twitter library
    $twitter = new TwitterOAuth($options->consumer_key,$options->consumer_secret,$options->oauth_token,$options->oauth_token_secret);

    try
    {
        // create a snippet
        $snippet = \InlineTweets\Snippet::create($snippet_text,$twitter,$options);

        // render the snippet
        $result->html = $snippet->render();
    }
    catch (RuntimeException $e)
    {
        // set error text
        $result->html = inline_tweets_inline_error($e->getMessage());

        // stop execution and return result
        return $result;
    }

    // return result
    return $result;
}

/**
 * Saves settings for inline tweets
 *
 * @return stdClass result
 */
function inline_tweets_action_saveSettings()
{
    // load current settings
    $settings = inline_tweets_loadOptions();

    // override from request
    foreach($_REQUEST['settings'] as $key => $value) $settings->{$key} = $value;

    // save settings
    inline_tweets_saveOptions($settings);

    // respond
    return $settings;
}

// ---------------------------------------------------------------------------------------------------------------------
// Behaviour
// ---------------------------------------------------------------------------------------------------------------------

/**
 * Intercepts all requests made to index.php and checks if an action should be performed
 * (when 'inline_tweets_snippet' parameter exists in the request array)
 */
function inline_tweets_ajaxAction()
{
    // make sure action is requested
    if(empty($_REQUEST['inline_tweets_action'])) return;

    // define action
    $action = 'inline_tweets_action_' . $_REQUEST['inline_tweets_action'];

    // make sure action exists
    if(!function_exists($action)) return;

    // perform action
    $result = $action();

    // respond
    header('Content-type: application/json');
    echo json_encode($result);

    // stop execution
    die;
}

/**
 * Renders posts/pages content by changing placeholders with tweets
 *
 * @param $content content to be rendered
 * @return String rendered content
 */
function inline_tweets_render($content)
{
    // define regex for searching for inline tweets
    $regex = '/\[tweet\](.*?)\[\/tweet\]/ism';

    // search for inline tweets
    $count = preg_match_all($regex,$content,$inline_tweets);

    // do nothing if there were no matches
    if($count == 0) return $content;

    // handle all matches
    if(isset($inline_tweets[0])) foreach($inline_tweets[0] as $snippet)
    {
        // remove tags from the snippet
        $cleared_snippet = str_replace('[tweet]','',$snippet);
        $cleared_snippet = str_replace('[/tweet]','',$cleared_snippet);

        // wrap inline tweet in placeholder div with a loader
        $content = str_replace($snippet,'<div class="inline_tweets_placeholder" snippet="'.$cleared_snippet.'"><img src="'. plugins_url('/img/loader.gif',__FILE__) . '" /></div>',$content);
    }

    // return content
    return $content;
}

/**
 * Loads Inline Tweets front-end dependencies in header
 */
function inline_tweets_loadHeader()
{
    global $inline_tweets_config;

    // load options
    $options = inline_tweets_loadOptions();

    // define styles
    $styles = array_merge(array('shared'),$inline_tweets_config->styles);

    // register styles
    foreach($styles as $style)
        wp_register_style("inline_tweets_{$style}", plugins_url("/css/{$style}.css", __FILE__ ), array(), INLINE_TWEETS, 'all');

    // set inline tweets url base
    echo '<script type="text/javascript">var INLINE_TWEETS_BASE_URL = "' . get_bloginfo('siteurl') . '";</script>';

    // load shared style
    wp_enqueue_style('inline_tweets_shared');

    // load style set in options (if not "none")
    if(isset($options->style) && $options->style != 'none' && in_array($options->style,$inline_tweets_config->styles))
        wp_enqueue_style("inline_tweets_{$options->style}");

    // load the handling script
    wp_enqueue_script('inline_tweets', plugins_url('/js/handler.js',__FILE__));
}

/**
 * Adds a menu item for accessing the settings page
 */
function inline_tweets_menuItem()
{
    add_submenu_page(
        'options-general.php', // parent
        'Inline Tweets Settings', // page title
        'Inline Tweets', // menu item title
        'administrator', // permission
        'inline-tweets-settings', // unique page name
        'inline_tweets_renderSettingsPage' // rendering function
    );
}

/**
 * Renders the settings page
 */
function inline_tweets_renderSettingsPage()
{
    global $inline_tweets_config;

    // load options
    $options = inline_tweets_loadOptions();

    // set inline tweets url base
    echo '<script type="text/javascript">var INLINE_TWEETS_BASE_URL = "' . get_bloginfo('siteurl') . '";</script>';

    // load the settings script
    wp_enqueue_script('inline_tweets', plugins_url('/js/settings.js',__FILE__));

    // render the page
    include(__DIR__ . '/php/settings.php');
}

/**
 * Adds a plugin action for accessing the settings page
 *
 * @param $links
 * @return $links
 */
function inlineTweets_addPluginActionLink($links) {
    // form the link
    $link = '<a href="' . get_bloginfo('wpurl') . '/wp-admin/options-general.php?page=inline-tweets-settings">Settings</a>';

    // append the link
    array_unshift($links, $link);

    // return the action links
    return $links;
}

// ---------------------------------------------------------------------------------------------------------------------
// Commands
// ---------------------------------------------------------------------------------------------------------------------

// register options
add_option('inline_tweets_options','');

// register request handler for ajax calls
add_action('init', 'inline_tweets_ajaxAction', 9999);

// add rendering filter for posts/pages
add_filter('the_content','inline_tweets_render');

// add rendering filter for text widgets content
add_filter('widget_text','inline_tweets_render');

// add head action to include inline-tweets script
add_action('wp_head', 'inline_tweets_loadHeader');

// register menu item for settings
add_action('admin_menu', 'inline_tweets_menuItem');

// add plugin action for accessing the settings page
add_action('plugin_action_links_'.plugin_basename(__FILE__),'inlineTweets_addPluginActionLink');