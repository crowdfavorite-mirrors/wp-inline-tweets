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
// Core Class: SingleTweetSnippet
// =============================================================================

namespace InlineTweets;

/**
 * Represents a snippet that renders a single tweet
 *
 * @package InlineTweets
 */
class SingleTweetSnippet extends Snippet
{
    /**
     * Instantiates the snippet
     *
     * @param string $snippet_text
     * @param string $params_string
     * @param \TwitterOAuth $twitter
     * @param stdClass $options
     */
    protected function __construct($snippet_text,$params_string,$twitter,$options)
    {
        // call parent constructor
        parent::__construct($snippet_text,$params_string,$twitter,$options);

        // set cache to 1 day
        $this->cacheTimeout = 24 * 60;
    }

    /**
     * Forms the key used for caching
     *
     * Format: single_tweetId
     * Example: single_419253253428506625
     *
     * @return string
     */
    protected function cacheKey()
    {
        // form via tweet id
        return 'single_' . (isset($this->params['id']) ? $this->params['id'] : 'invalid');
    }

    /**
     * Renders the single tweet
     *
     * @param stdClass|bool $from_cache
     *
     * @return string
     * @throws \RuntimeException
     */
    protected function renderSpecific($from_cache = false)
    {
        // check if cache loaded
        if(false !== $from_cache) $tweet = $from_cache;
        else
        {
            // make sure Tweet id is set
            if(!isset($this->params['id'])) throw new \RuntimeException('Missing tweet ID.');

            // get the tweet
            $tweet = $this->twitter->get('statuses/show/' . $this->params['id']);

            // check for errors
            if(isset($tweet->errors))
                throw new \RuntimeException('Twitter error: ' . $tweet->errors[0]->message);

            // save to cache
            $this->writeToCache($tweet);
        }

        // render the tweet
        return '<div class="inline_tweet">' . $this->renderTweet($tweet) . '</div>';
    }

}