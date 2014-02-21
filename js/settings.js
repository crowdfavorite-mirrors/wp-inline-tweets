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
// Settings Script
// =============================================================================

/**
 * Gets all settings
 *
 * @returns {Object}
 */
function getSettings()
{
    // init settings
    var settings = {};

    // go trough all settings inputs
    jQuery("#inline_tweets_settings").find('input,select,textarea').each(function(){
        // get setting
        var setting = jQuery(this);

        // save settings
        settings[setting.attr('name')] = setting.val();
    });

    // respond
    return settings;
}

/**
 * Attempts to save settings
 */
function saveSettings()
{
    // call service
    jQuery.ajax({
        type: 'POST',
        url: INLINE_TWEETS_BASE_URL + '/index.php',
        data: { inline_tweets_action: 'saveSettings', settings: getSettings() },
        dataType: "json",
        success: function() { window.location = window.location.href; }
    });
}