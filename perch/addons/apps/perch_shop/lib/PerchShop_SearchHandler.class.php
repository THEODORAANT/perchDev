<?php

class PerchShop_SearchHandler implements PerchAPI_SearchHandler
{

    private static $tmp_url_vars = false;

    public static function get_admin_search_sql($key)
    {
        $API = new PerchAPI(1.0, 'perch_shop');
        $db = $API->get('DB');

        $sql = 'SELECT \''.__CLASS__.'\' AS source, \'Shop\' AS display_source, MATCH(s.searchBody) AGAINST('.$db->pdb($key).') AS score, p.title, p.productSlug, p.productDynamicFields, p.productTemplate, productID, "", "", ""
                FROM '.PERCH_DB_PREFIX.'shop_search s, '.PERCH_DB_PREFIX.'shop_products p
                WHERE p.productDeleted IS NULL
                    AND p.productID=s.itemKey AND s.itemType=\'product\'
                    AND MATCH(s.searchBody) AGAINST('.$db->pdb($key).')';

        return $sql;
    }

    public static function get_search_sql($key)
    {
        $API = new PerchAPI(1.0, 'perch_shop');
        $db = $API->get('DB');

        $sql = 'SELECT \''.__CLASS__.'\' AS source, MATCH(s.searchBody) AGAINST('.$db->pdb($key).') AS score, p.title, p.productSlug, p.productDynamicFields, p.productTemplate, productID, "", "", ""
	            FROM '.PERCH_DB_PREFIX.'shop_search s, '.PERCH_DB_PREFIX.'shop_products p
	            WHERE p.productDeleted IS NULL
                    AND p.productID=s.itemKey AND s.itemType=\'product\'
	                AND MATCH(s.searchBody) AGAINST('.$db->pdb($key).')
                    AND p.productID IN (
                        SELECT itemID FROM '.PERCH_DB_PREFIX.'shop_index WHERE itemKey=\'productID\' AND itemID=p.productID AND indexKey=\'status\' AND indexValue=1
                    )';

	    return $sql;
    }

    public static function get_backup_search_sql($key)
    {
        $API = new PerchAPI(1.0, 'perch_shop');
        $db = $API->get('DB');

        $sql = 'SELECT \''.__CLASS__.'\' AS source, p.stock_level AS score, p.title, p.productSlug, p.productDynamicFields, p.productTemplate, p.productID, "", "", ""
	            FROM '.PERCH_DB_PREFIX.'shop_search s, '.PERCH_DB_PREFIX.'shop_products p
                WHERE p.productDeleted IS NULL
	                AND p.productID=s.itemKey AND s.itemType=\'product\'
	                AND (
	                    concat("  ", s.searchBody, "  ") REGEXP '.$db->pdb('[[:<:]]'.$key.'[[:>:]]').'
	                    )
                    AND p.productID IN (
                        SELECT itemID FROM '.PERCH_DB_PREFIX.'shop_index WHERE itemKey=\'productID\' AND itemID=p.productID AND indexKey=\'status\' AND indexValue=1
                    )';

	    return $sql;
    }

    public static function format_result($key, $options, $result)
    {
        $result['title']    = $result['col1'];
        $result['slug']     = $result['col2'];
        $result['template'] = $result['col4'];
        $result['_id']      = $result['col5'];
    
        $Settings   = PerchSettings::fetch();

        $fields = PerchUtil::json_safe_decode($result['col3'], true);

        $description = $fields['description']['processed'];

        $html = PerchUtil::excerpt_char($description, $options['excerpt-chars'], true);
        // keyword highlight
        $html = preg_replace('/('.$key.')/i', '<em class="keyword">$1</em>', $html);

        $match = array();

        $match['url']     = $Settings->get('perch_shop_product_url')->settingValue();
        self::$tmp_url_vars = $result;
        $match['url'] = preg_replace_callback('/{([A-Za-z0-9_\-]+)}/', array('self', "substitute_url_vars"), $match['url']);
        self::$tmp_url_vars = false;

        $match['title']   = $result['title'];
        $match['excerpt'] = $html;
        $match['key']     = $key;

        if (PerchUtil::count($fields)) {
            foreach($fields as $key=>$val) {
                $match[$key] = $val;
            }
        }

        $match['productID'] = $result['_id'];

        return $match;
    }

    private static function substitute_url_vars($matches)
	{
	    $url_vars = self::$tmp_url_vars;
    	if (isset($url_vars[$matches[1]])){
    		return $url_vars[$matches[1]];
    	}
	}

    public static function format_admin_result($key, $options, $result)
    {
        $result['productID']       = $result['col5'];

        $self = __CLASS__;

        $out = $self::format_result($key, $options, $result);

        $out['url'] = PERCH_LOGINPATH.'/addons/apps/perch_shop_products/product/edit/?id='.$result['productID'];

        return $out;
    }

}
