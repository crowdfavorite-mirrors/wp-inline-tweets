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
// Core Class: LatestTweetsSnippet
// =============================================================================

namespace InlineTweets;

/**
 * Represents a snippet that renders latest tweets
 *
 * @package InlineTweets
 */
class LatestTweetsSnippet extends Snippet
{
    // defaults
    const COUNT_DEFAULT = 5;
    const COUNT_MAX = 25;

    /**
     * Forms the key used for caching
     *
     * Format: latest_userName_count
     * Example: latest_milos_djekic_5
     *
     * @return string
     */
    protected function cacheKey()
    {
        // form via author name and count
        return 'latest_' . (isset($this->params['user']) ? $this->params['user'] : 'invalid') . '_' . (isset($this->params['count']) ? $this->params['count'] : 'invalid');
    }

    /**
     * Renders latest tweets
     *
     * @param stdClass|bool $from_cache
     *
     * @return string
     * @throws \RuntimeException
     */
    protected function renderSpecific($from_cache = false)
    {
        // check if cache loaded
        if(false !== $from_cache) $tweets = $from_cache;
        else
        {
            // make sure user is set
            if(!isset($this->params['user'])) throw new \RuntimeException('Missing user.');

            // make sure user name is valid
            if(!$this->validateTwitterUser($this->params['user'])) throw new \RuntimeException('Invalid Twitter username.');

            // determine count
            $count = (isset($this->params['count']) && $this->params['count'] <= self::COUNT_MAX) ? intval($this->params['count']) : self::COUNT_DEFAULT;

            // get tweets
            $tweets = $this->twitter->get('statuses/user_timeline',array('screen_name' => $this->params['user'], 'count' => $count));

            // check for errors
            if(isset($tweets->errors))
                throw new \RuntimeException('Twitter error: ' . $tweets->errors[0]->message);

            // save to cache
            $this->writeToCache($tweets);
        }

        // render list
        return $this->renderTweets($tweets);
    }

}