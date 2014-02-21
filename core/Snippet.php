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
// Core Class: Snippet
// =============================================================================

namespace InlineTweets;

/**
 * Represents an abstract snippet.
 *
 * @package InlineTweets
 */
abstract class Snippet
{
    /**
     * List of supported snippet types with snippet classes
     *
     * @var array
     */
    public static $supported_types = array(
        'latest' => 'LatestTweetsSnippet',
        'single' => 'SingleTweetSnippet',
    );

    /**
     * Creates and returns a snippet
     *
     * @param string $snippet_text
     * @param \TwitterOAuth $twitter
     * @param stdClass $options
     *
     * @return Snippet $snippet
     * @throws \RuntimeException
     */
    public static function create($snippet_text,$twitter,$options)
    {
        // get snippet parts
        $snippet_parts = explode('|',$snippet_text);

        // make sure type is supported
        if(!isset(self::$supported_types[$snippet_parts[0]])) throw new \RuntimeException('Unknown snippet type.');

        // get snippet class
        $snippet_class = '\\InlineTweets\\' . self::$supported_types[$snippet_parts[0]];

        // instantiate snippet
        $snippet = new $snippet_class($snippet_text,(count($snippet_parts) == 2) ? $snippet_parts[1] : array(),$twitter,$options);

        // return snippet
        return $snippet;
    }

    // default date format
    const DATE_FORMAT_DEFAULT = 'M d Y';

    /**
     * Snippet text
     *
     * @var string
     */
    private $snippet;

    /**
     * Snippet params
     *
     * @var array
     */
    protected $params;

    /**
     * Twitter library
     *
     * @var \TwitterOAuth
     */
    protected $twitter;

    /**
     * User options
     *
     * @var stdClass
     */
    protected $options;

    /**
     * Cache timeout in minutes
     *
     * @var int
     */
    protected $cacheTimeout = 5; // default is 5 min

    /**
     * Instantiates a snippet
     *
     * @param string $snippet_text
     * @param string $params_string
     * @param \TwitterOAuth $twitter
     * @param stdClass $options
     */
    protected function __construct($snippet_text,$params_string,$twitter,$options)
    {
        // set snippet
        $this->snippet = $snippet_text;

        // init params
        $params = array();

        // get all key-value pairs
        $pairs = explode(',',$params_string);

        // go trough all key-value pairs
        foreach($pairs as $pair)
        {
            // turn pair into an array
            $pair = explode('=',$pair);

            // save param
            $params[$pair[0]] = (count($pair) != 2) ? true : $pair[1];
        }

        // save params
        $this->params = $params;

        // save Twitter library
        $this->twitter = $twitter;

        // save options
        $this->options = $options;
    }

    /**
     * Determines the date format based on user settings
     *
     * @return string
     */
    private function determineDateFormat()
    {
        global $inline_tweets_config;

        // check if time format is set inline
        if(isset($this->params['time_format']))
        {
            // make sure time format is supported
            if(in_array($this->params['time_format'],$inline_tweets_config->time_formats)) return $this->params['time_format'];
        }

        // return time format
        return isset($this->options->time_format) ? $this->options->time_format : self::DATE_FORMAT_DEFAULT;
    }

    /**
     * Validates a twitter user string
     *
     * @param string $user
     * @return bool
     */
    protected function validateTwitterUser($user)
    {
        return preg_match('/^[A-Za-z0-9_]{1,15}$/', $user);
    }

    /**
     * Renders a single tweet in HTML markup
     *
     * @param stdClass $tweet
     * @return string
     */
    protected function renderTweet($tweet)
    {
        // get tweet content
        $content = $tweet->text;

        // append tweet author avatar
        if(!isset($this->params['hide_avatar']))
            $content = '<a class="inline_tweets_avatar" href="http://twitter.com/'.$tweet->user->screen_name.'" target="_blank"><img src="'.$tweet->user->profile_image_url.'" /></a> ' . $content;

        // append tweet author
        if(!isset($this->params['hide_author']))
        {
            // determine display name
            $display_name = isset($this->params['full_names']) ? $tweet->user->name : "@{$tweet->user->screen_name}";

            // add to content
            $content = '<a class="inline_tweets_author" href="http://twitter.com/'.$tweet->user->screen_name.'" target="_blank">'.$display_name.'</a> ' . $content;
        }

        // render tags
        foreach($tweet->entities->hashtags as $tag)
            $content = str_replace("#{$tag->text}",'<span class="inline_tweets_tag">'."#{$tag->text}".'</span>',$content);

        // render URLs
        foreach($tweet->entities->urls as $url)
            $content = str_replace($url->url,'<a class="inline_tweets_url" href="'.$url->expanded_url.'" target="_blank">'.$url->url.'</a>',$content);

        // render mentions
        foreach($tweet->entities->user_mentions as $mention)
            $content = str_replace("@{$mention->screen_name}",'<a class="inline_tweets_mention" href="http://twitter.com/'.$mention->screen_name.'" target="_blank">'."@{$mention->screen_name}".'</a>',$content);

        // add timestamp
        if(!isset($this->params['hide_timestamp']))
            $content .= ' <span class="inline_tweets_timestamp">' . date($this->determineDateFormat(),strtotime($tweet->created_at)) . '</span>';

        // return rendered tweet content
        return $content;
    }

    /**
     * Renders a list of tweets in HTML markup
     *
     * @param array $tweets
     * @return string
     */
    protected function renderTweets($tweets)
    {
        // init resulting HTML
        $html = '<ul class="inline_tweets_ul">';

        // go trough all tweets
        foreach($tweets as $tweet)
            // render tweet
            $html .= '<li class="inline_tweet">' . $this->renderTweet($tweet) . '</li>';

        // close ul in resulting HTML
        $html .= '</ul>';

        // return rendered tweets list
        return $html;
    }

    /**
     * Renders tweets in a specific way (intended to be overwritten in child classes)
     *
     * @param stdClass|bool $from_cache
     *
     * @return string
     * @throws \RuntimeException
     */
    protected abstract function renderSpecific($from_cache = false);

    /**
     * Forms the key used for caching
     *
     * @return string
     */
    protected abstract function cacheKey();

    /**
     * Loads data from cache
     *
     * @return bool|mixed
     */
    protected function loadFromCache()
    {
        // get cache entry
        $cache_entry = wp_cache_get($this->cacheKey());

        // check if nothing found
        if(false === $cache_entry) return false;

        // get time difference in minutes
        $difference = round(abs(time() - $cache_entry['timestamp']) / 60,2);

        // return cache data if cache is young enough
        return ($difference <= $this->cacheTimeout) ? $cache_entry['data'] : false;
    }

    /**
     * Writes to cache
     *
     * @param mixed $data
     */
    protected function writeToCache($data)
    {
        // save to cache
        wp_cache_set($this->cacheKey(),array('timestamp' => time(), 'data' => $data));
    }

    /**
     * Renders tweets
     *
     * @return string
     * @throws \RuntimeException
     */
    public function render()
    {
        // get cache data
        $cache_data = $this->loadFromCache();

        // check if cache data exists
        if($cache_data !== false)
            // call specific render with data from cache
            return $this->renderSpecific($cache_data);

        // get account information from Twitter library
        $account = $this->twitter->get('account/verify_credentials');

        // check if there were any errors
        if(isset($account->errors))
            throw new \RuntimeException('Twitter error: ' . $account->errors[0]->message);

        // call specific render without data
        return $this->renderSpecific();
    }

}

// load all supported snippet classes
foreach(Snippet::$supported_types as $type => $class) include(__DIR__ . '/' . $class . '.php');