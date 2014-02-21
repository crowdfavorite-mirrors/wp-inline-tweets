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
// Handler Script
// =============================================================================

// On Load
jQuery(function(){

    // handle all placeholders
    jQuery('div.inline_tweets_placeholder').each(function(){
        // get placeholder
        var placeholder = jQuery(this);

        // load tweet(s)
        jQuery.ajax({
            type: 'POST',
            url: INLINE_TWEETS_BASE_URL + '/index.php',
            data: { inline_tweets_action: 'handleSnippet', snippet: placeholder.attr('snippet') },
            dataType: "json",
            success: function(response){
                // display HTML content
                placeholder.replaceWith(response.html);
            }
        });
    });

});