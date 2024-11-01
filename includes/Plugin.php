<?php

namespace WidgetsForAmazon;

class Plugin
{
    static $search_form_search_box_index = 0;

    public static function filter_get_product_search_form($form): string
    {
        return self::filter_search_form($form);
    }

    public static function filter_get_search_form($form): string
    {
        return self::filter_search_form($form);
    }

    public static function filter_plugin_action_links($links, $plugin_file_name)
    {
        //Tools::log_debug('filter_plugin_action_links');

        if (strpos($plugin_file_name, Constants::PLUGIN_FILE_NAME))
        {
            $settings_link = '<a href="' . esc_url(get_admin_url(null, 'tools.php?page=' . Constants::PLUGIN_SLUG)) . '">Settings</a>';
            array_unshift($links, $settings_link);
        }

        return $links;
    }

    public static function filter_the_content($input): string
    {
        //Tools::log_debug('filter_the_content: ' . str_replace("\n", '', substr($input, 0, 50)));

        if (!is_single() && !is_page())
            return preg_replace('/\[amazon-search.*?]/i', '', $input);

        $match_count = preg_match_all('/\[amazon-search.*?]/i', $input, $matches, PREG_OFFSET_CAPTURE);
        if (!$match_count)
            return $input;

        self::enqueue_common_styles_and_scripts();

        // Determine plugin version for JSON request.
        if (!function_exists('get_plugin_data'))
            require_once(ABSPATH . 'wp-admin/includes/plugin.php');
        $plugin_data = get_plugin_data(plugin_dir_path(__DIR__) . Constants::PLUGIN_FILE_NAME);
        $plugin_version = $plugin_data['Version'];

        $domainCode = get_option(Constants::OPTION_DOMAIN_CODE_NAME, Constants::OPTION_DOMAIN_CODE_DEFAULT_VALUE);
        $language = get_option(Constants::OPTION_LANGUAGE_NAME, Constants::OPTION_LANGUAGE_DEFAULT_VALUE);
        $affiliateTag = get_option(Constants::OPTION_AFFILIATE_TAG_NAME, Constants::OPTION_AFFILIATE_TAG_DEFAULT_VALUE);

        // Add start of page
        $output = substr($input, 0, $matches[0][0][1]);

        $last_pos = 0;
        for ($i = 0; $i < $match_count; $i++)
        {
            $match_text = $matches[0][$i][0];
            $match_pos = $matches[0][$i][1];

            if ($i != 0)
                $output .= substr($input, $last_pos, $match_pos - $last_pos);

            $html = self::create_search_box_html($i);
            $script = self::create_search_box_script($i, $match_text, $domainCode, $language, $affiliateTag, $plugin_version);

            $output .= $html;

            $label = 'WidgetsForAmazonSB-' . $i;
            wp_register_script($label, '');
            wp_enqueue_script($label);
            wp_add_inline_script($label, $script);

            $last_pos = $match_pos + strlen($match_text);
        }

        // Add rest of page
        $output .= substr($input, $last_pos);

        return $output;
    }

    static function filter_search_form($form): string
    {
        if (is_404())
        {
            $showOn404Page = get_option(Constants::OPTION_SHOW_ON_404_PAGE_NAME, Constants::OPTION_SHOW_ON_404_PAGE_DEFAULT_VALUE);
            if (!$showOn404Page)
                return $form;
        }
        else
        {
            if (!get_search_query())
                return $form;

            $showOnNoResultsPage = get_option(Constants::OPTION_SHOW_ON_NO_RESULTS_PAGE_NAME, Constants::OPTION_SHOW_ON_NO_RESULTS_PAGE_DEFAULT_VALUE);
            if (!$showOnNoResultsPage)
                return $form;
        }

        self::enqueue_common_styles_and_scripts();

        // Determine plugin version for JSON request.
        if (!function_exists('get_plugin_data'))
            require_once(ABSPATH . 'wp-admin/includes/plugin.php');
        $plugin_data = get_plugin_data(plugin_dir_path(__DIR__) . Constants::PLUGIN_FILE_NAME);
        $plugin_version = $plugin_data['Version'];

        $domainCode = get_option(Constants::OPTION_DOMAIN_CODE_NAME, Constants::OPTION_DOMAIN_CODE_DEFAULT_VALUE);
        $language = get_option(Constants::OPTION_LANGUAGE_NAME, Constants::OPTION_LANGUAGE_DEFAULT_VALUE);
        $affiliateTag = get_option(Constants::OPTION_AFFILIATE_TAG_NAME, Constants::OPTION_AFFILIATE_TAG_DEFAULT_VALUE);
        $noResultsPageCategory = get_option(Constants::OPTION_NO_RESULTS_PAGE_CATEGORY_NAME, Constants::OPTION_NO_RESULTS_PAGE_CATEGORY_DEFAULT_VALUE);

        $html = self::create_search_box_html('sf-' . self::$search_form_search_box_index);

        if (is_404())
        {
            $query = $_SERVER['REQUEST_URI'];
            //Tools::log_debug('REQUEST_URI: ' . $query);

            $questionPos = strpos($query, '?');
            if ($questionPos && $questionPos >= 0)
            {
                $query = substr($query, 0, $questionPos);
                //Tools::log_debug('  ? removed: ' . $query);
            }

            if (Tools::endsWith($query, '/'))
            {
                $query = substr($query, 0, strlen($query) - 1);
                //Tools::log_debug('  Last / removed: ' . $query);
            }

            $slashPos = strrpos($query, '/');
            if ($slashPos && $slashPos >= 0)
            {
                $query = substr($query, $slashPos + 1);
                //Tools::log_debug('  Everything before / removed: ' . $query);
            }
        }
        else
        {
            $query = get_search_query();
        }

        $query = str_replace('"', ' ', $query);
        $query = str_replace('?', ' ', $query);
        $query = str_replace('&', ' ', $query);
        $query = str_replace('/', ' ', $query);
        $query = str_replace('\\', ' ', $query);
        $query = str_replace('<', ' ', $query);
        $query = str_replace('>', ' ', $query);
        $query = trim($query);

        $inputForScript = 'category="' . $noResultsPageCategory . '" keywords="' . $query . '"';

        $script = self::create_search_box_script('sf-' . self::$search_form_search_box_index, $inputForScript, $domainCode, $language, $affiliateTag, $plugin_version);
        $script = str_replace('eggnstone_widgets_fill_amazon_search_box(', 'eggnstone_widgets_fill_amazon_search_box_for_search_forms(', $script);

        $label = 'WidgetsForAmazonSB-SF-' . self::$search_form_search_box_index;
        wp_register_script($label, '');
        wp_enqueue_script($label);
        wp_add_inline_script($label, $script);

        self::$search_form_search_box_index++;

        return $form . $html;
    }

    static function create_search_box_html($index): string
    {
        return '<div class="amazon-search" id="amazon-search-' . $index . '"></div>';
    }

    static function create_search_box_script($index, $input, $defaultDomainCode, $defaultLanguage, $defaultAffiliateTag, $plugin_version): string
    {
        $input = str_replace('&#8220;', '"', $input); // &ldquo;
        $input = str_replace('&#8221;', '"', $input); // &rdquo;
        $input = str_replace('&#8222;', '"', $input); // &bdquo;
        $input = str_replace('&#8243;', '"', $input); // &Prime;

        $domain_code = self::extract_param($input, 'domain-code="', '"');
        $language = self::extract_param($input, 'language="', '"');
        $category = self::extract_param($input, 'category="', '"');
        $keywords = self::extract_param($input, 'keywords="', '"');
        $affiliateTag = self::extract_param($input, 'tag="', '"');

        if (!$domain_code)
            $domain_code = $defaultDomainCode;

        if (!$language)
            $language = $defaultLanguage;

        if (!$keywords)
            return 'console.log("Keywords missing => Cannot create search box widget for \"amazon-search-' . $index . '\".");';

        if (!$affiliateTag)
            $affiliateTag = $defaultAffiliateTag;

        $base_url_europe_west_1 = 'https://europe-west1-e-widgets-europe-west3-prod.cloudfunctions.net/SearchBoxJsonEuropeWest1';
        $base_url_us_central_1 = 'https://us-central1-e-widgets-europe-west3-prod.cloudfunctions.net/SearchBoxJsonUsCentral1';
        $base_url = $base_url_us_central_1;
        if ($domain_code == 'co.uk' || $domain_code == 'de' || $domain_code == 'fr' || $domain_code == 'it')
            $base_url = $base_url_europe_west_1;

        $url = $base_url . '?Source=WP&Version=' . $plugin_version . '&Tag=' . $affiliateTag;
        if ($domain_code)
            $url .= '&DomainCode=' . $domain_code;
        if ($language)
            $url .= '&Language=' . $language;
        if ($category)
            $url .= '&Category=' . $category;

        /** @noinspection SpellCheckingInspection */
        $footerMessage = $language == 'de'
            ? 'Als Amazon-Partner verdienen wir an qualifizierten Verkäufen.'
            : 'As an Amazon Associate we earn from qualifying purchases.';

        $output = 'document.addEventListener("DOMContentLoaded", function(event) {'
            . 'eggnstone_widgets_fill_amazon_search_box("__INDEX__", "__URL__", "__KEYWORDS__", "' . $footerMessage . '");'
            . '});';

        $output = str_replace('__URL__', $url, $output);

        $output = str_replace('__INDEX__', $index, $output);

        /** @noinspection PhpUnnecessaryLocalVariableInspection */
        $output = str_replace('__KEYWORDS__', $keywords, $output);

        return $output;
    }

    static function extract_param($input, $pattern_start, $pattern_end)
    {
        $match_count = preg_match_all('/' . $pattern_start . '.*?' . $pattern_end . '/i', $input, $matches, PREG_OFFSET_CAPTURE);
        if ($match_count != 1)
            return null;

        $match_text = $matches[0][0][0];

        /** @noinspection PhpUnnecessaryLocalVariableInspection */
        $param = substr($match_text, strlen($pattern_start), strlen($match_text) - strlen($pattern_start) - strlen($pattern_end));

        return $param;
    }

    static function enqueue_common_styles_and_scripts()
    {
        // Add common CSS
        wp_enqueue_style('WidgetsForAmazonSB',
            plugin_dir_url(__DIR__) . 'css/amazon-search.css',
            [],
            filemtime(plugin_dir_path(__DIR__ . '/../css/amazon-search.css'))
        );

        // Add common script
        wp_enqueue_script('WidgetsForAmazonSB',
            plugin_dir_url(__DIR__) . 'js/amazon-search.js',
            [],
            filemtime(plugin_dir_path(__DIR__ . '/../js/amazon-search.js'))
        );
    }
}
