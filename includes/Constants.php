<?php

namespace WidgetsForAmazon;

class Constants
{
    const PLUGIN_FILE_NAME = 'widgets-for-amazon.php';
    const PLUGIN_NAME = 'Widgets for Amazon';
    const PLUGIN_SLUG = 'widgets_for_amazon';

    const OPTION_GROUP = 'eggnstone_widgets_for_amazon_option_group';

    const OPTION_NAME_PREFIX = 'eggnstone_widgets_for_amazon_';

    const OPTION_DOMAIN_CODE_NAME = self::OPTION_NAME_PREFIX . 'domain_code';
    const OPTION_DOMAIN_CODE_DEFAULT_VALUE = 'com';

    const OPTION_LANGUAGE_NAME = self::OPTION_NAME_PREFIX . 'language';
    const OPTION_LANGUAGE_DEFAULT_VALUE = 'en';

    const OPTION_AFFILIATE_TAG_NAME = self::OPTION_NAME_PREFIX . 'affiliate_tag';
    const OPTION_AFFILIATE_TAG_DEFAULT_VALUE = 'your-amazon-affiliate-tag';

    const OPTION_SHOW_ON_404_PAGE_NAME = self::OPTION_NAME_PREFIX . 'show_on_404_page';
    const OPTION_SHOW_ON_404_PAGE_DEFAULT_VALUE = False;

    const OPTION_404_PAGE_CATEGORY_NAME = self::OPTION_NAME_PREFIX . '404_page_category';
    const OPTION_404_PAGE_CATEGORY_DEFAULT_VALUE = '';

    const OPTION_SHOW_ON_NO_RESULTS_PAGE_NAME = self::OPTION_NAME_PREFIX . 'show_on_no_results_page';
    const OPTION_SHOW_ON_NO_RESULTS_PAGE_DEFAULT_VALUE = False;

    const OPTION_NO_RESULTS_PAGE_CATEGORY_NAME = self::OPTION_NAME_PREFIX . 'no_results_page_category';
    const OPTION_NO_RESULTS_PAGE_CATEGORY_DEFAULT_VALUE = '';
}
