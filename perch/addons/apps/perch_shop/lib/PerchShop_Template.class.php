<?php

class PerchShop_Template extends PerchAPI_TemplateHandler
{
	public $tag_mask = 'cartitems|cartitem|orderitems|orderitem|addresses|address|productopts|productopt|productvalues|productvalue|taxrates|taxrate|zones|zone|variants|variant';

	public function render($vars, $contents, $Template)
	{
		if (strpos($contents, 'perch:cartitems')!==false) {
			$contents       = $this->parse_paired_tags('cartitems', true, $contents, $vars, 0, 'render_cart_items');
        }

        if (strpos($contents, 'perch:orderitems')!==false) {
			$contents       = $this->parse_paired_tags('orderitems', true, $contents, $vars, 0, 'render_order_items');
        }

        if (strpos($contents, 'perch:addresses')!==false) {
			$contents       = $this->parse_paired_tags('addresses', true, $contents, $vars, 0, 'render_addresses');
        }

        if (strpos($contents, 'perch:productopts')!==false) {
			$contents       = $this->parse_paired_tags('productopts', true, $contents, $vars, 0, 'render_productopts');
        }

        if (strpos($contents, 'perch:productvalues')!==false) {
			$contents       = $this->parse_paired_tags('productvalues', true, $contents, $vars, 0, 'render_productvalues');
        }

        if (strpos($contents, 'perch:taxrates')!==false) {
			$contents       = $this->parse_paired_tags('taxrates', true, $contents, $vars, 0, 'render_taxrates');
        }

        if (strpos($contents, 'perch:zones')!==false) {
			$contents       = $this->parse_paired_tags('zones', true, $contents, $vars, 0, 'render_zones');
        }

        if (strpos($contents, 'perch:variants')!==false) {
			$contents       = $this->parse_paired_tags('variants', true, $contents, $vars, 0, 'render_variants');
        }

		return $contents;
	}

	protected function render_cart_items($type, $opening_tag, $condition_contents, $exact_match, $template_contents, $content_vars, $index_in_group=false)
	{
		$Tag = new PerchXMLTag($opening_tag);
		$out = '';

		if (isset($content_vars['items']) && PerchUtil::count($content_vars['items'])) {
			$ItemTemplate = new PerchTemplate(false, 'cartitem');
			$ItemTemplate->load($condition_contents);
			$out = $ItemTemplate->render_group($content_vars['items'], true);
		}else{
			if (strpos($condition_contents, 'perch:noresults')) {
		        $s = '/<perch:noresults[^>]*>(.*?)<\/perch:noresults>/s';
		        $count	= preg_match_all($s, $condition_contents, $matches, PREG_SET_ORDER);

				if ($count > 0) {
					foreach($matches as $match) {
					    $out .= $match[1];
					}
				}
			}
		}
		return str_replace($exact_match, $out, $template_contents);
	}

	protected function render_order_items($type, $opening_tag, $condition_contents, $exact_match, $template_contents, $content_vars, $index_in_group=false)
	{
		$Tag = new PerchXMLTag($opening_tag);
		$out = '';

		if (isset($content_vars['items']) && PerchUtil::count($content_vars['items']))  {
			$ItemTemplate = new PerchTemplate(false, 'orderitem');
			$ItemTemplate->load($condition_contents);
			$out = $ItemTemplate->render_group($content_vars['items'], true);
		}else{
			if (strpos($condition_contents, 'perch:noresults')) {
		        $s = '/<perch:noresults[^>]*>(.*?)<\/perch:noresults>/s';
		        $count	= preg_match_all($s, $condition_contents, $matches, PREG_SET_ORDER);

				if ($count > 0) {
					foreach($matches as $match) {
					    $out .= $match[1];
					}
				}
			}
		}
		return str_replace($exact_match, $out, $template_contents);
	}

	protected function render_addresses($type, $opening_tag, $condition_contents, $exact_match, $template_contents, $content_vars, $index_in_group=false)
	{
		$Tag = new PerchXMLTag($opening_tag);
		$out = '';

		#PerchUtil::debug($content_vars);

		if (isset($content_vars['items']) && PerchUtil::count($content_vars['items']))  {
			$ItemTemplate = new PerchTemplate(false, 'address');
			$ItemTemplate->load($condition_contents);
			$out = $ItemTemplate->render_group($content_vars['items'], true);
		}else{
			if (strpos($condition_contents, 'perch:noresults')) {
		        $s = '/<perch:noresults[^>]*>(.*?)<\/perch:noresults>/s';
		        $count	= preg_match_all($s, $condition_contents, $matches, PREG_SET_ORDER);

				if ($count > 0) {
					foreach($matches as $match) {
					    $out .= $match[1];
					}
				}
			}
		}
		return str_replace($exact_match, $out, $template_contents);
	}

	protected function render_taxrates($type, $opening_tag, $condition_contents, $exact_match, $template_contents, $content_vars, $index_in_group=false)
	{
		$Tag = new PerchXMLTag($opening_tag);
		$out = '';

		if (isset($content_vars['tax_rate_totals']) && PerchUtil::count($content_vars['tax_rate_totals']))  {
			$ItemTemplate = new PerchTemplate(false, 'taxrate');
			$ItemTemplate->load($condition_contents);
			$out = $ItemTemplate->render_group($content_vars['tax_rate_totals'], true);
		}else{
			if (strpos($condition_contents, 'perch:noresults')) {
		        $s = '/<perch:noresults[^>]*>(.*?)<\/perch:noresults>/s';
		        $count	= preg_match_all($s, $condition_contents, $matches, PREG_SET_ORDER);

				if ($count > 0) {
					foreach($matches as $match) {
					    $out .= $match[1];
					}
				}
			}
		}
		return str_replace($exact_match, $out, $template_contents);
	}

	protected function render_zones($type, $opening_tag, $condition_contents, $exact_match, $template_contents, $content_vars, $index_in_group=false)
	{
		$Tag = new PerchXMLTag($opening_tag);
		$out = '';

		if (isset($content_vars['zones']) && PerchUtil::count($content_vars['zones']))  {
			$ItemTemplate = new PerchTemplate(false, 'zone');
			$ItemTemplate->load($condition_contents);
			$out = $ItemTemplate->render_group($content_vars['zones'], true);
		}else{
			if (strpos($condition_contents, 'perch:noresults')) {
		        $s = '/<perch:noresults[^>]*>(.*?)<\/perch:noresults>/s';
		        $count	= preg_match_all($s, $condition_contents, $matches, PREG_SET_ORDER);

				if ($count > 0) {
					foreach($matches as $match) {
					    $out .= $match[1];
					}
				}
			}
		}
		return str_replace($exact_match, $out, $template_contents);
	}

	protected function render_productopts($type, $opening_tag, $condition_contents, $exact_match, $template_contents, $content_vars, $index_in_group=false)
	{
		$Tag = new PerchXMLTag($opening_tag);
		$out = '';

		if (isset($content_vars['options']) && PerchUtil::count($content_vars['options'])) {
			$ItemTemplate = new PerchTemplate(false, 'productopt');
			$ItemTemplate->load($condition_contents);
			$out = $ItemTemplate->render_group($content_vars['options'], true);
		}else{
			if (strpos($condition_contents, 'perch:noresults')) {
		        $s = '/<perch:noresults[^>]*>(.*?)<\/perch:noresults>/s';
		        $count	= preg_match_all($s, $condition_contents, $matches, PREG_SET_ORDER);

				if ($count > 0) {
					foreach($matches as $match) {
					    $out .= $match[1];
					}
				}
			}
		}
		return str_replace($exact_match, $out, $template_contents);
	}

	protected function render_productvalues($type, $opening_tag, $condition_contents, $exact_match, $template_contents, $content_vars, $index_in_group=false)
	{
		$Tag = new PerchXMLTag($opening_tag);
		$out = '';

		#PerchUtil::debug($content_vars);

		if (isset($content_vars['productvalues']) && PerchUtil::count($content_vars['productvalues'])) {
			$ItemTemplate = new PerchTemplate(false, 'productvalue');
			$ItemTemplate->load($condition_contents);
			$out = $ItemTemplate->render_group($content_vars['productvalues'], true);
		}else{
			if (strpos($condition_contents, 'perch:noresults')) {
		        $s = '/<perch:noresults[^>]*>(.*?)<\/perch:noresults>/s';
		        $count	= preg_match_all($s, $condition_contents, $matches, PREG_SET_ORDER);

				if ($count > 0) {
					foreach($matches as $match) {
					    $out .= $match[1];
					}
				}
			}
		}
		return str_replace($exact_match, $out, $template_contents);
	}

	protected function render_variants($type, $opening_tag, $condition_contents, $exact_match, $template_contents, $content_vars, $index_in_group=false)
	{
		$Tag = new PerchXMLTag($opening_tag);
		$out = '';

		if (isset($content_vars['variants']) && PerchUtil::count($content_vars['variants'])) {
			$ItemTemplate = new PerchTemplate(false, 'variant');
			$ItemTemplate->load($condition_contents);
			$out = $ItemTemplate->render_group($content_vars['variants'], true);
		}else{
			if (strpos($condition_contents, 'perch:noresults')) {
		        $s = '/<perch:noresults[^>]*>(.*?)<\/perch:noresults>/s';
		        $count	= preg_match_all($s, $condition_contents, $matches, PREG_SET_ORDER);

				if ($count > 0) {
					foreach($matches as $match) {
					    $out .= $match[1];
					}
				}
			}
		}
		return str_replace($exact_match, $out, $template_contents);
	}

    private function parse_paired_tags($type='', $empty_opener=false, $contents='', $content_vars=[], $index_in_group=false, $callback='parse_conditional')
    {

		$close_tag     = '</perch:'.$type.'>';
		$close_tag_len = mb_strlen($close_tag);
		$open_tag      = '<perch:'.$type.($empty_opener ? '' : ' ');

		// escape hatch
		$i = 0;
		$max_loops = 1000;

		// loop through while we have closing tags
    	while($close_pos = mb_strpos($contents, $close_tag)) {

    		// we always have to go from the start, as the string length changes,
    		// but stop at the closing tag
    		$chunk = mb_substr($contents, 0, $close_pos);

    		// search from the back of the chunk for the opening tag
    		$open_pos = mb_strrpos($chunk, $open_tag);

    		// get the pair html chunk
    		$len = ($close_pos+$close_tag_len)-$open_pos;
    		$pair_html = mb_substr($contents, $open_pos, $len);

    		// find the opening tag - it's right at the start
    		$opening_tag_end_pos = mb_strpos($pair_html, '>')+1;
    		$opening_tag = mb_substr($pair_html, 0, $opening_tag_end_pos);

    		// condition contents
    		$condition_contents = mb_substr($pair_html, $opening_tag_end_pos, 0-$close_tag_len);

    		// Do the business
    		$contents = $this->$callback($type, $opening_tag, $condition_contents, $pair_html, $contents, $content_vars, $index_in_group);

    		// escape hatch counter
    		$i++;
    		if ($i > $max_loops) return $contents;
    	}

    	return $contents;
    }
}
