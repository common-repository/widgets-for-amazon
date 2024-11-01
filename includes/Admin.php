<?php

namespace WidgetsForAmazon;

class Admin
{
    const NONCE_ACTION = 'eggnstone-widgets-for-amazon-update-settings';
    const NONCE_NAME = self::NONCE_ACTION . '-nonce';

    /*
    public static function admin_activate_plugin()
    {
        //Tools::log_debug('admin_activate_plugin');
    }

    public static function admin_deactivate_plugin()
    {
        //Tools::log_debug('admin_deactivate_plugin');
    }

    public static function admin_uninstall_plugin()
    {
        //Tools::log_debug('admin_uninstall_plugin');
    }
    */

    public static function admin_init()
    {
        //Tools::log_debug('admin_init');

        register_setting(
            Constants::OPTION_GROUP,
            Constants::OPTION_DOMAIN_CODE_NAME,
            __NAMESPACE__ . '\Admin::sanitize_string');

        register_setting(
            Constants::OPTION_GROUP,
            Constants::OPTION_LANGUAGE_NAME,
            __NAMESPACE__ . '\Admin::sanitize_string');

        register_setting(
            Constants::OPTION_GROUP,
            Constants::OPTION_AFFILIATE_TAG_NAME,
            __NAMESPACE__ . '\Admin::sanitize_string');

        register_setting(
            Constants::OPTION_GROUP,
            Constants::OPTION_SHOW_ON_404_PAGE_NAME,
            __NAMESPACE__ . '\Admin::sanitize_boolean');

        register_setting(
            Constants::OPTION_GROUP,
            Constants::OPTION_404_PAGE_CATEGORY_NAME,
            __NAMESPACE__ . '\Admin::sanitize_string');

        register_setting(
            Constants::OPTION_GROUP,
            Constants::OPTION_SHOW_ON_NO_RESULTS_PAGE_NAME,
            __NAMESPACE__ . '\Admin::sanitize_boolean');

        register_setting(
            Constants::OPTION_GROUP,
            Constants::OPTION_NO_RESULTS_PAGE_CATEGORY_NAME,
            __NAMESPACE__ . '\Admin::sanitize_string');
    }

    public static function admin_menu()
    {
        //Tools::log_debug('admin_menu');

        add_submenu_page(
            'tools.php',
            Constants::PLUGIN_NAME,
            Constants::PLUGIN_NAME,
            'manage_options', //'activate_plugins',
            Constants::PLUGIN_SLUG,
            __NAMESPACE__ . '\Admin::add_submenu_page');
    }

    static function add_submenu_page()
    {
        //Tools::log_debug('admin_add_submenu_page()');

        if (!current_user_can('manage_options'))
            return;

        if (isset($_POST['option_page']) && $_POST['option_page'] == Constants::OPTION_GROUP && $_POST['action'] == 'update')
        {
            //Tools::dump_debug($_POST);

            if (isset($_POST[self::NONCE_NAME]) && wp_verify_nonce($_POST[self::NONCE_NAME], self::NONCE_ACTION))
            {
                update_option(Constants::OPTION_DOMAIN_CODE_NAME, $_POST[Constants::OPTION_DOMAIN_CODE_NAME]);
                update_option(Constants::OPTION_LANGUAGE_NAME, $_POST[Constants::OPTION_LANGUAGE_NAME]);
                update_option(Constants::OPTION_AFFILIATE_TAG_NAME, $_POST[Constants::OPTION_AFFILIATE_TAG_NAME]);
                update_option(Constants::OPTION_404_PAGE_CATEGORY_NAME, $_POST[Constants::OPTION_404_PAGE_CATEGORY_NAME]);
                update_option(Constants::OPTION_NO_RESULTS_PAGE_CATEGORY_NAME, $_POST[Constants::OPTION_NO_RESULTS_PAGE_CATEGORY_NAME]);

                if (isset($_POST[Constants::OPTION_SHOW_ON_404_PAGE_NAME]))
                    update_option(Constants::OPTION_SHOW_ON_404_PAGE_NAME, $_POST[Constants::OPTION_SHOW_ON_404_PAGE_NAME] == 'on');
                else
                    update_option(Constants::OPTION_SHOW_ON_404_PAGE_NAME, 0);

                if (isset($_POST[Constants::OPTION_SHOW_ON_NO_RESULTS_PAGE_NAME]))
                    update_option(Constants::OPTION_SHOW_ON_NO_RESULTS_PAGE_NAME, $_POST[Constants::OPTION_SHOW_ON_NO_RESULTS_PAGE_NAME] == 'on');
                else
                    update_option(Constants::OPTION_SHOW_ON_NO_RESULTS_PAGE_NAME, 0);
            }
            else
            {
                print("Nonce failed!");
            }
        }

        $domainCode = get_option(Constants::OPTION_DOMAIN_CODE_NAME, Constants::OPTION_DOMAIN_CODE_DEFAULT_VALUE);
        $language = get_option(Constants::OPTION_LANGUAGE_NAME, Constants::OPTION_LANGUAGE_DEFAULT_VALUE);
        $affiliateTag = get_option(Constants::OPTION_AFFILIATE_TAG_NAME, Constants::OPTION_AFFILIATE_TAG_DEFAULT_VALUE);
        $showOn404Page = get_option(Constants::OPTION_SHOW_ON_404_PAGE_NAME, Constants::OPTION_SHOW_ON_404_PAGE_DEFAULT_VALUE);
        $categoryFor404Page = get_option(Constants::OPTION_404_PAGE_CATEGORY_NAME, Constants::OPTION_404_PAGE_CATEGORY_DEFAULT_VALUE);
        $showOnNoResultsPage = get_option(Constants::OPTION_SHOW_ON_NO_RESULTS_PAGE_NAME, Constants::OPTION_SHOW_ON_NO_RESULTS_PAGE_DEFAULT_VALUE);
        $categoryForNoResultsPage = get_option(Constants::OPTION_NO_RESULTS_PAGE_CATEGORY_NAME, Constants::OPTION_NO_RESULTS_PAGE_CATEGORY_DEFAULT_VALUE);

        ?>
        <script>
            function eggnstone_widgets_copy_to_clipboard(id)
            {
                navigator.clipboard.writeText(document.getElementById(id).value);
            }
        </script>
        <div class="wrap">
            <h2><?php print(Constants::PLUGIN_NAME); ?></h2>
            <form method="post" action="<?php print(esc_url($_SERVER["REQUEST_URI"])); ?>">
                <?php
                wp_nonce_field(self::NONCE_ACTION, self::NONCE_NAME, false);
                settings_fields(Constants::OPTION_GROUP);
                ?>
                <table class="form-table">
                    <tr>
                        <td colspan="3" style="padding: 8px; border: 1px solid black;">
                            <span style="font-weight: bold; font-size: large;">Global Settings</span><br/>
                            <table class="form-table">
                                <tr>
                                    <th>Amazon Site</th>
                                    <td>
                                        <select name="<?php print(Constants::OPTION_DOMAIN_CODE_NAME); ?>">
                                            <option <?php if ($domainCode == "ca") print("selected"); ?> value="ca">Canada - www.amazon.ca</option>
                                            <option <?php if ($domainCode == "fr") print("selected"); ?> value="fr">France - www.amazon.fr</option>
                                            <option <?php if ($domainCode == "de") print("selected"); ?> value="de">Germany - www.amazon.de</option>
                                            <option <?php if ($domainCode == "it") print("selected"); ?> value="it">Italy - www.amazon.it</option>
                                            <option <?php if ($domainCode == "co.jo") print("selected"); ?> value="co.jp">Japan - www.amazon.co.jp</option>
                                            <option <?php if ($domainCode == "co.uk") print("selected"); ?> value="co.uk">UK - www.amazon.co.uk</option>
                                            <option <?php if ($domainCode == "com") print("selected"); ?> value="com">USA - www.amazon.com</option>
                                        </select>
                                    </td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <th>Language</th>
                                    <td>
                                        <select name="<?php print(Constants::OPTION_LANGUAGE_NAME); ?>">
                                            <option <?php if ($language == "en") print("selected"); ?> value="en">English (en)</option>
                                            <option <?php if ($language == "de") print("selected"); ?> value="de">German (de)</option>
                                        </select>
                                    </td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <th>Affiliate Tag</th>
                                    <td><input type="text" name="<?php print(Constants::OPTION_AFFILIATE_TAG_NAME); ?>" value="<?php print($affiliateTag); ?>"/></td>
                                    <td></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td></td>
                    </tr>
                    <tr>
                        <td colspan="3" style="padding: 8px; border: 1px solid black;">
                            <span style="font-weight: bold; font-size: large;">"404" page</span><br/>
                            The "404" page is displayed when a request page is not found.
                            (<a target="_blank" href="<?php print(get_site_url() . '/404'); ?>">Example</a>)
                            <table class="form-table">
                                <tr>
                                    <th>Display Amazon search box<br/>underneath normal search box.</th>
                                    <td><input type="checkbox" name="<?php print(Constants::OPTION_SHOW_ON_404_PAGE_NAME); ?>" <?php checked('1', $showOn404Page); ?>/></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <th>Category*</th>
                                    <td><input type="text" name="<?php print(Constants::OPTION_404_PAGE_CATEGORY_NAME); ?>" value="<?php print($categoryFor404Page); ?>"/></td>
                                    <td></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td></td>
                    </tr>
                    <tr>
                        <td colspan="3" style="padding: 8px; border: 1px solid black;">
                            <span style="font-weight: bold; font-size: large;">"No Results" page</span><br/>
                            The "No Results" page is displayed when a site search does not produce any hits.
                            (<a target="_blank" href="<?php print(get_site_url() . '/?s=IMPOSSIBLE_KEYWORDS'); ?>">Example</a>)
                            <table class="form-table">
                                <tr>
                                    <th>Display Amazon search box<br/>underneath normal search box</th>
                                    <td><input type="checkbox" name="<?php print(Constants::OPTION_SHOW_ON_NO_RESULTS_PAGE_NAME); ?>" <?php checked('1', $showOnNoResultsPage); ?>/></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <th>Category*</th>
                                    <td><input type="text" name="<?php print(Constants::OPTION_NO_RESULTS_PAGE_CATEGORY_NAME); ?>" value="<?php print($categoryForNoResultsPage); ?>"/></td>
                                    <td></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
                <p><input type="submit" class="button-primary" value="Save all Changes"/></p>
            </form>
            <br/>
            Your can add Amazon search widgets pn pages and in posts by using the following shortcodes:<br/>
            <br/>
            <input id="example0" readonly style="width: 80%;" value="[amazon-search category=&quot;CATEGORY&quot; keywords=&quot;KEYWORDS&quot;]"/>
            <button onclick="eggnstone_widgets_copy_to_clipboard('example0');">Copy</button>
            <br/>
            <br/>
            <b>keywords</b> (required) the keywords to search for, e.g. &quot;Balls&quot;<br/>
            <b>category:</b> (optional) the category* to search in, e.g. &quot;Sporting&quot; (empty =&gt; all categories)<br/>
            <br/>
            e.g.<br/>
            <br/>
            <input id="example1" readonly style="width: 80%;" value="[amazon-search keywords=&quot;Toys&quot;]"/>
            <button onclick="eggnstone_widgets_copy_to_clipboard('example1');">Copy</button>
            <br/>
            <br/>
            <input id="example2" readonly style="width: 80%;" value="[amazon-search category=&quot;Sporting&quot; keywords=&quot;Balls&quot;]"/>
            <button onclick="eggnstone_widgets_copy_to_clipboard('example2');">Copy</button>
            <br/>
            <br/>
            <b>Category</b>:<br/>
            If you provide a category it needs to be the category code amazon uses, e.g. if you want "Apps & Games" then you need to supply "mobile-apps".<br/>
            See below for the appropriate codes for the different Amazon sites:<br/>
            <?php print(self::getCategories()); ?>
        </div>
        <?php
    }

    static function sanitize_string($input): string
    {
        //Tools::log_debug('Admin::sanitize_string("' . $input . '")');

        $sanitizedValue = wp_filter_nohtml_kses($input);
        if ($input != $sanitizedValue)
            Tools::log_debug('Admin::sanitize_string("' . $input . '") sanitized to "' . $sanitizedValue . '"');

        return $sanitizedValue;
    }

    static function sanitize_boolean($input): string
    {
        //Tools::log_debug('Admin::sanitize_boolean("' . $input . '")');

        // Force either 0 or 1
        $sanitizedValue = ($input == 1 ? 1 : 0);
        if ($input != $sanitizedValue)
            Tools::log_debug('Admin::sanitize_boolean("' . $input . '") sanitized to "' . $sanitizedValue . '"');

        return $sanitizedValue;
    }

    static function getCategories(): string
    {
        /** @noinspection SpellCheckingInspection */
        $categoriesDe =
            "<b id='categories-de'>Germany</b><br/>" .
            "alexa-skills: Alexa Skills<br/>" .
            "amazon-devices: Amazon Geräte<br/>" .
            "amazon-global-store: Amazon Global Store<br/>" .
            "warehouse-deals: Amazon Warehouse<br/>" .
            "mobile-apps: Apps & Spiele<br/>" .
            "audible: Audible Hörbücher<br/>" .
            "automotive: Auto & Motorrad<br/>" .
            "baby: Baby<br/>" .
            "diy: Baumarkt<br/>" .
            "beauty: Beauty<br/>" .
            "clothing: Bekleidung<br/>" .
            "lighting: Beleuchtung<br/>" .
            "stripbooks: Bücher<br/>" .
            "office-products: Bürobedarf & Schreibwaren<br/>" .
            "computers: Computer & Zubehör<br/>" .
            "drugstore: Drogerie & Körperpflege<br/>" .
            "dvd: DVD & Blu-ray<br/>" .
            "appliances: Elektro-Großgeräte<br/>" .
            "electronics: Elektronik & Foto<br/>" .
            "fashion: Fashion<br/>" .
            "videogames: Games<br/>" .
            "outdoor: Garten<br/>" .
            "gift-cards: Geschenkgutscheine<br/>" .
            "industrial: Gewerbe, Industrie & Wissenschaft<br/>" .
            "handmade: Handmade<br/>" .
            "pets: Haustier<br/>" .
            "local-services: Home & Business Services<br/>" .
            "photo: Kamera & Foto<br/>" .
            "digital-text: Kindle-Shop<br/>" .
            "classical: Klassik<br/>" .
            "luggage: Koffer, Rucksäcke & Taschen<br/>" .
            "kitchen: Küche, Haushalt & Wohnen<br/>" .
            "grocery: Lebensmittel & Getränke<br/>" .
            "popular: Musik-CDs & Vinyl<br/>" .
            "digital-music: Musik-Downloads<br/>" .
            "mi: Musikinstrumente & DJ-Equipment<br/>" .
            "luxury-beauty: Premium Beauty<br/>" .
            "instant-video: Prime Video<br/>" .
            "jewelry: Schmuck<br/>" .
            "shoes: Schuhe & Handtaschen<br/>" .
            "software: Software<br/>" .
            "specialty-aps-sns: Spar-Abo<br/>" .
            "toys: Spielzeug<br/>" .
            "sports: Sport & Freizeit<br/>" .
            "watches: Uhren<br/>" .
            "magazines: Zeitschriften<br/>";

        /** @noinspection SpellCheckingInspection */
        $categoriesUk =
            "<b id='categories-uk'>UK</b><br/>" .
            "alexa-skills: Alexa Skills<br/>" .
            "amazon-devices: Amazon Devices<br/>" .
            "amazon-global-store: Amazon Global Store<br/>" .
            "warehouse-deals: Amazon Warehouse<br/>" .
            "mobile-apps: Apps & Games<br/>" .
            "audible: Audible Audiobooks<br/>" .
            "baby: Baby<br/>" .
            "beauty: Beauty<br/>" .
            "stripbooks: Books<br/>" .
            "automotive: Car & Motorbike<br/>" .
            "popular: CDs & Vinyl<br/>" .
            "classical: Classical Music<br/>" .
            "clothing: Clothing<br/>" .
            "computers: Computers & Accessories<br/>" .
            "digital-music: Digital Music<br/>" .
            "diy: DIY & Tools<br/>" .
            "dvd: DVD & Blu-ray<br/>" .
            "electronics: Electronics & Photo<br/>" .
            "fashion: Fashion<br/>" .
            "outdoor: Garden & Outdoors<br/>" .
            "gift-cards: Gift Cards<br/>" .
            "grocery: Grocery<br/>" .
            "handmade: Handmade<br/>" .
            "drugstore: Health & Personal Care<br/>" .
            "local-services: Home & Business Services<br/>" .
            "kitchen: Home & Kitchen<br/>" .
            "industrial: Industrial & Scientific<br/>" .
            "jewelry: Jewellery<br/>" .
            "digital-text: Kindle Store<br/>" .
            "appliances: Large Appliances<br/>" .
            "lighting: Lighting<br/>" .
            "luggage: Luggage<br/>" .
            "mi: Musical Instruments & DJ Equipment<br/>" .
            "videogames: PC & Video Games<br/>" .
            "pets: Pet Supplies<br/>" .
            "luxury-beauty: Premium Beauty<br/>" .
            "instant-video: Prime Video<br/>" .
            "shoes: Shoes & Bags<br/>" .
            "software: Software<br/>" .
            "sports: Sports & Outdoors<br/>" .
            "office-products: Stationery & Office Supplies<br/>" .
            "specialty-aps-sns: Subscribe & Save<br/>" .
            "toys: Toys & Games<br/>" .
            "watches: Watches<br/>";

        /** @noinspection SpellCheckingInspection */
        $categoriesUs =
            "<b id='categories-us'>USA</b><br/>" .
            "alexa-skills: Alexa Skills<br/>" .
            "amazon-devices: Amazon Devices<br/>" .
            "live-explorations: Amazon Explore<br/>" .
            "amazonfresh: Amazon Fresh<br/>" .
            "amazon-pharmacy: Amazon Pharmacy<br/>" .
            "warehouse-deals: Amazon Warehouse<br/>" .
            "appliances: Appliances<br/>" .
            "mobile-apps: Apps & Games<br/>" .
            "arts-crafts: Arts, Crafts & Sewing<br/>" .
            "audible: Audible Books & Originals<br/>" .
            "automotive: Automotive Parts & Accessories<br/>" .
            "courses: AWS Courses<br/>" .
            "baby-products: Baby<br/>" .
            "beauty: Beauty & Personal Care<br/>" .
            "stripbooks: Books<br/>" .
            "popular: CDs & Vinyl<br/>" .
            "mobile: Cell Phones & Accessories<br/>" .
            "fashion: Clothing, Shoes & Jewelry<br/>" .
            "fashion-womens: Women<br/>" .
            "fashion-mens: Men<br/>" .
            "fashion-girls: Girls<br/>" .
            "fashion-boys: Boys<br/>" .
            "fashion-baby: Baby<br/>" .
            "collectibles: Collectibles & Fine Art<br/>" .
            "computers: Computers<br/>" .
            "financial: Credit and Payment Cards<br/>" .
            "edu-alt-content: Digital Educational Resources<br/>" .
            "digital-music: Digital Music<br/>" .
            "electronics: Electronics<br/>" .
            "lawngarden: Garden & Outdoor<br/>" .
            "gift-cards: Gift Cards<br/>" .
            "grocery: Grocery & Gourmet Food<br/>" .
            "handmade: Handmade<br/>" .
            "hpc: Health, Household & Baby Care<br/>" .
            "local-services: Home & Business Services<br/>" .
            "garden: Home & Kitchen<br/>" .
            "industrial: Industrial & Scientific<br/>" .
            "prime-exclusive: Just for Prime<br/>" .
            "digital-text: Kindle Store<br/>" .
            "fashion-luggage: Luggage & Travel Gear<br/>" .
            "luxury: Luxury Stores<br/>" .
            "magazines: Magazine Subscriptions<br/>" .
            "movies-tv: Movies & TV<br/>" .
            "mi: Musical Instruments<br/>" .
            "office-products: Office Products<br/>" .
            "pets: Pet Supplies<br/>" .
            "luxury-beauty: Premium Beauty<br/>" .
            "instant-video: Prime Video<br/>" .
            "smart-home: Smart Home<br/>" .
            "software: Software<br/>" .
            "sporting: Sports & Outdoors<br/>" .
            "specialty-aps-sns: Subscribe & Save<br/>" .
            "subscribe-with-amazon: Subscription Boxes<br/>" .
            "tools: Tools & Home Improvement<br/>" .
            "toys-and-games: Toys & Games<br/>" .
            "under-ten-dollars: Under $10<br/>" .
            "videogames: Video Games<br/>" .
            "wholefoods: Whole Foods Market<br/>";

        /** @noinspection SpellCheckingInspection */
        $categoriesUsIntl =
            "<b id='categories-us-intl'>USA (international shipping)</b><br/>" .
            "arts-crafts-intl-ship: Arts & Crafts<br/>" .
            "automotive-intl-ship: Automotive<br/>" .
            "baby-products-intl-ship: Baby<br/>" .
            "beauty-intl-ship: Beauty & Personal Care<br/>" .
            "stripbooks-intl-ship: Books<br/>" .
            "fashion-boys-intl-ship: Boys' Fashion<br/>" .
            "computers-intl-ship: Computers<br/>" .
            "deals-intl-ship: Deals<br/>" .
            "digital-music: Digital Music<br/>" .
            "electronics-intl-ship: Electronics<br/>" .
            "fashion-girls-intl-ship: Girls' Fashion<br/>" .
            "hpc-intl-ship: Health & Household<br/>" .
            "kitchen-intl-ship: Home & Kitchen<br/>" .
            "industrial-intl-ship: Industrial & Scientific<br/>" .
            "digital-text: Kindle Store<br/>" .
            "luggage-intl-ship: Luggage<br/>" .
            "fashion-mens-intl-ship: Men's Fashion<br/>" .
            "movies-tv-intl-ship: Movies & TV<br/>" .
            "music-intl-ship: Music, CDs & Vinyl<br/>" .
            "pets-intl-ship: Pet Supplies<br/>" .
            "instant-video: Prime Video<br/>" .
            "software-intl-ship: Software<br/>" .
            "sporting-intl-ship: Sports & Outdoors<br/>" .
            "tools-intl-ship: Tools & Home Improvement<br/>" .
            "toys-and-games-intl-ship: Toys & Games<br/>" .
            "videogames-intl-ship: Video Games<br/>" .
            "fashion-womens-intl-ship: Women's Fashion<br/>";

        return
            "<a href='#categories-de'>Germany</a><br/>" .
            "<a href='#categories-uk'>UK</a><br/>" .
            "<a href='#categories-us'>USA</a><br/>" .
            "<a href='#categories-us-intl'>USA (international shipping)</a><br/>" .
            "<br/>" .
            $categoriesDe . "<br/>" .
            $categoriesUk . "<br/>" .
            $categoriesUs . "<br/>" .
            $categoriesUsIntl;
    }
}
