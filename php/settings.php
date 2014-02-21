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
// Settings Page
// =============================================================================

?>

<div id="inline_tweets_settings">

    <h2 style="text-transform: uppercase">I n l i n e <span style="visibility: hidden">s</span> T w e e t s</h2>

    <br />
    <h3>Important - Caching</h3>
    <hr />
    <p>
        Caching will improve performance when fetching tweets and <b>ensure the plugin operates within Twitter API limits</b>.
        If it is disabled, the plugin will quickly reach the limit set by Twitter and your tweets will not be displayed.
    </p>
    <p>
        To enable caching, please install <a href="http://wordpress.org/extend/plugins/w3-total-cache/">W3 Total Cache</a>
        plugin and <b>enable 'Object Cache' in plugin settings</b>.
    </p>

    <br />
    <h3>Twitter Params</h3>
    <hr />
    <table class="form-table">
        <tbody>

        <!-- Consumer Key -->
        <tr valign="top">
            <th scope="row"><label for="consumer_key">Consumer Key</label></th>
            <td><input name="consumer_key" type="text" id="consumer_key" value="<?php echo isset($options->consumer_key) ? $options->consumer_key : ''; ?>" class="regular-text"></td>
        </tr>

        <!-- Consumer Secret -->
        <tr valign="top">
            <th scope="row"><label for="consumer_secret">Consumer Secret</label></th>
            <td><input name="consumer_secret" type="text" id="consumer_secret" value="<?php echo isset($options->consumer_secret) ? $options->consumer_secret : ''; ?>" class="regular-text"></td>
        </tr>

        <!-- oAuth Token -->
        <tr valign="top">
            <th scope="row"><label for="oauth_token">oAuth Token</label></th>
            <td><input name="oauth_token" type="text" id="oauth_token" value="<?php echo isset($options->oauth_token) ? $options->oauth_token : ''; ?>" class="regular-text"></td>
        </tr>

        <!-- oAuth Token Secret -->
        <tr valign="top">
            <th scope="row"><label for="oauth_token_secret">oAuth Token Secret</label></th>
            <td>
                <input name="oauth_token_secret" type="text" id="oauth_token_secret" value="<?php echo isset($options->oauth_token_secret) ? $options->oauth_token_secret : ''; ?>" class="regular-text">
                <p class="description">You don't know what these are? Visit <a href="https://dev.twitter.com/apps/" target="_blank">Twitter Apps</a>, create an app and obtain keys and tokens.</p>
                <p class="description">(In the middle of 2013 Twitter abandoned their API version 1.0. and this is required in version 1.1)</p>
            </td>
        </tr>

        </tbody>
    </table>

    <br />
    <h3>Other Settings</h3>
    <hr />
    <table class="form-table">
        <tbody>

        <!-- Time Format -->
        <tr valign="top">
            <th scope="row"><label for="time_format">Default Time Format</label></th>
            <td>
                <select name="time_format" id="time_format">
                    <?php foreach($inline_tweets_config->time_formats as $format) { ?>
                        <option value="<?php echo $format; ?>" <?php if(isset($options->time_format) && $options->time_format == $format) echo 'selected="selected"'; ?>><?php echo date($format); ?></option>
                    <?php } ?>
                </select>
                <p class="description">Default format of tweet timestamps (you can define format in the snippet that will override the defaults)</p>
            </td>
        </tr>

        <!-- Style -->
        <tr valign="top">
            <th scope="row"><label for="time_format">Default Styles</label></th>
            <td>
                <select name="style" id="style">
                    <option value="none">none</option>
                    <?php foreach($inline_tweets_config->styles as $style) { ?>
                        <option value="<?php echo $style; ?>" <?php if(isset($options->style) && $options->style == $style) echo 'selected="selected"'; ?>><?php echo $style; ?></option>
                    <?php } ?>
                </select>
                <p class="description">Default tweet styles (if you select "none" no styles will be loaded)</p>
            </td>
        </tr>

        </tbody>
    </table>

    <p class="submit">
        <input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes" onclick="saveSettings(); return false;">
    </p>

</div>