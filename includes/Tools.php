<?php

namespace WidgetsForAmazon;

class Tools
{
    public static function log_function($function)
    {
        self::log_debug($function);

        self::log_debug('  is_404: ' . is_404());
        self::log_debug('  is_active_widget: ' . is_active_widget());
        self::log_debug('  is_archive: ' . is_archive());
        self::log_debug('  is_category: ' . is_category());
        self::log_debug('  is_home: ' . is_home());
        self::log_debug('  is_page: ' . is_page());
        self::log_debug('  is_post_type_archive: ' . is_post_type_archive());
        self::log_debug('  is_search: ' . is_search());
        self::log_debug('  is_single: ' . is_single());
        self::log_debug('  is_singular: ' . is_singular());

        self::log_debug('  get_permalink: ' . get_permalink());
        self::log_debug('  get_post_type: ' . get_post_type());
        self::log_debug('  get_search_query: ' . get_search_query());

        /*
        global $wp_query;
        if ($wp_query)
        {
            if ($wp_query->query_vars)
            {
                self::log_debug('  $wp_query->query_vars["error"]: ' . $wp_query->query_vars['error']);
            }
        }
        */

        //self::log_debug('  $wp_query->query_vars: ' . self::dump_to_string($wp_query->query_vars));

        /*
        global $wp;
        self::log_debug('  $wp->request: ' . self::dump_to_string($wp->request));


        self::log_debug('  home_url($wp->query_vars): ' . home_url($wp->query_vars));
        self::log_debug('  home_url($wp->request): ' . home_url($wp->request));
        $current_url = add_query_arg(array(), $wp->request);
        self::log_debug('  home_url($wp->request): ' . $current_url);
        */

        //self::log_debug('  $_SERVER["REQUEST_URI"]: ' . $_SERVER['REQUEST_URI']);
        //self::log_debug('  $_SERVER: ' . self::dump_to_string($_SERVER));
        //self::log_debug('  site_url: ' . site_url());
        //self::log_debug('  home_url: ' . home_url());
    }

    public static function log_debug($message)
    {
        self::log_with_level('Debug', $message);
    }

    public static function log_info($message)
    {
        self::log_with_level('Info ', $message);
    }

    public static function log_warning($message)
    {
        self::log_with_level('Warn ', $message);
    }

    public static function log_error($message)
    {
        self::log_with_level('Error', $message);
    }

    static function log_with_level($level, $message)
    {
        $filename = plugin_dir_path(__FILE__) . '../debug.log';
        error_log((new \DateTime())->format('Y-m-d H:i:s') . ' [' . $level . '] ' . $message . PHP_EOL, 3, $filename);
    }

    public static function dump_debug($object)
    {
        $s = self::dump_to_string($object);
        self::log_debug($s);;
    }

    public static function dump_to_string($object)
    {
        return var_export($object, true);
    }

    public static function array_to_text($array): string
    {
        $array_as_text = '';
        foreach ($array as $result)
        {
            if (strlen($array_as_text) > 0)
                $array_as_text .= ',';

            if (is_array($result))
                $array_as_text .= '[' . self::array_to_text($result) . ']';
            else
                $array_as_text .= $result;
        }

        return $array_as_text;
    }

    public static function dictionary_to_text($dictionary): string
    {
        $dictionary_as_text = '';
        foreach (array_keys($dictionary) as $key)
        {
            if (strlen($dictionary_as_text) > 0)
                $dictionary_as_text .= ',';

            $value = $dictionary[$key];
            if (is_array($value))
                $dictionary_as_text .= $key . '=[' . self::array_to_text($value) . ']';
            else
                $dictionary_as_text .= $key . '=' . $value;
        }

        return $dictionary_as_text;
    }

    public static function startsWith($haystack, $needle): bool
    {
        return substr($haystack, 0, strlen($needle)) === $needle;
    }

    public static function endsWith($haystack, $needle): bool
    {
        $length = strlen($needle);
        if (!$length)
            return true;

        return substr($haystack, -$length) === $needle;
    }
}
