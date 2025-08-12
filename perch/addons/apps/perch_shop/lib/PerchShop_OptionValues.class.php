<?php

class PerchShop_OptionValues extends PerchShop_Factory
{
	public $singular_classname     = 'PerchShop_OptionValue';
	public $static_fields          = ['valueTitle', 'optionID', 'valueCreated'];
	
	protected $table               = 'shop_option_values';
	protected $pk                  = 'valueID';
	protected $index_table         = 'shop_index';
	protected $master_template	   = 'shop/option.html';
	
	protected $default_sort_column = 'valueTitle';
	protected $created_date_column = 'valueCreated';

	protected $event_prefix = 'shop.option.value';
	

	public $productID = false;
	public $optionID = false;

	private $stopwords = ["a", "about", "above", "above", "across", "after", "afterwards", "again", "against", "all", "almost", "alone", "along", "already", "also","although","always","am","among", "amongst", "amoungst", "amount",  "an", "and", "another", "any","anyhow","anyone","anything","anyway", "anywhere", "are", "around", "as",  "at", "back","be","became", "because","become","becomes", "becoming", "been", "before", "beforehand", "behind", "being", "below", "beside", "besides", "between", "beyond", "bill", "both", "bottom","but", "by", "call", "can", "cannot", "cant", "co", "con", "could", "couldnt", "cry", "de", "describe", "detail", "do", "done", "down", "due", "during", "each", "eg", "eight", "either", "eleven","else", "elsewhere", "empty", "enough", "etc", "even", "ever", "every", "everyone", "everything", "everywhere", "except", "few", "fifteen", "fify", "fill", "find", "fire", "first", "five", "for", "former", "formerly", "forty", "found", "four", "from", "front", "full", "further", "get", "give", "go", "had", "has", "hasnt", "have", "he", "hence", "her", "here", "hereafter", "hereby", "herein", "hereupon", "hers", "herself", "him", "himself", "his", "how", "however", "hundred", "ie", "if", "in", "inc", "indeed", "interest", "into", "is", "it", "its", "itself", "keep", "last", "latter", "latterly", "least", "less", "ltd", "made", "many", "may", "me", "meanwhile", "might", "mill", "mine", "more", "moreover", "most", "mostly", "move", "much", "must", "my", "myself", "name", "namely", "neither", "never", "nevertheless", "next", "nine", "no", "nobody", "none", "noone", "nor", "not", "nothing", "now", "nowhere", "of", "off", "often", "on", "once", "one", "only", "onto", "or", "other", "others", "otherwise", "our", "ours", "ourselves", "out", "over", "own","part", "per", "perhaps", "please", "put", "rather", "re", "same", "see", "seem", "seemed", "seeming", "seems", "serious", "several", "she", "should", "show", "side", "since", "sincere", "six", "sixty", "so", "some", "somehow", "someone", "something", "sometime", "sometimes", "somewhere", "still", "such", "system", "take", "ten", "than", "that", "the", "their", "them", "themselves", "then", "thence", "there", "thereafter", "thereby", "therefore", "therein", "thereupon", "these", "they", "thickv", "thin", "third", "this", "those", "though", "three", "through", "throughout", "thru", "thus", "to", "together", "too", "top", "toward", "towards", "twelve", "twenty", "two", "un", "under", "until", "up", "upon", "us", "very", "via", "was", "we", "well", "were", "what", "whatever", "when", "whence", "whenever", "where", "whereafter", "whereas", "whereby", "wherein", "whereupon", "wherever", "whether", "which", "while", "whither", "who", "whoever", "whole", "whom", "whose", "why", "will", "with", "within", "without", "would", "yet", "you", "your", "yours", "yourself", "yourselves", "the"];


	public function get_edit_values($optionID)
	{
		$sql = 'SELECT valueID AS id, valueTitle AS title, valueSKUCode AS skucode
				FROM '.$this->table.' WHERE optionID='.$this->db->pdb((int)$optionID).' AND valueDeleted IS NULL 
				ORDER BY valueOrder ASC';
		return $this->db->get_rows($sql);
	}

	public function get_edit_ids($optionID)
	{
		$sql = 'SELECT valueID FROM '.$this->table.' WHERE optionID='.$this->db->pdb((int)$optionID) .' AND valueDeleted IS NULL';
		return $this->db->get_rows_flat($sql);
	}

	public function get_checkbox_values($optionID, $productID)
	{
		$sql = 'SELECT valueID FROM '.PERCH_DB_PREFIX.'shop_product_option_values 
				WHERE productID='.$this->db->pdb((int)$productID).' AND optionID='.$this->db->pdb((int)$optionID);
		return $this->db->get_rows_flat($sql);
	}

	public function get_checkbox_options($optionID)
	{
		$sql = 'SELECT valueID, valueTitle FROM '.$this->table.' 
				WHERE optionID='.$this->db->pdb((int)$optionID).' AND valueDeleted IS NULL ORDER BY valueOrder ASC';
		$rows = $this->db->get_rows($sql);


		if (PerchUtil::count($rows)) {
			$opts = [];
			foreach($rows as $option) {
				$opts[] = [
					'value' => $option['valueID'],
					'label' => $option['valueTitle'],
				];
			}

			return $opts;

		}

		return false;
	}

	public function get_for_product($optionID, $productID)
	{
		$sql = 'SELECT pov.prodoptID, pov.optionID, pov.valueID, o.optionTitle, ov.*
					FROM '.PERCH_DB_PREFIX.'shop_product_option_values pov, '.PERCH_DB_PREFIX.'shop_options o, '.PERCH_DB_PREFIX.'shop_option_values ov
					WHERE pov.productID='.$this->db->pdb($productID).' AND pov.optionID=o.optionID AND pov.valueID=ov.valueID AND o.optionDeleted IS NULL AND ov.valueDeleted IS NULL
						AND o.optionID='.$this->db->pdb($optionID).'
					ORDER BY o.optionPrecendence ASC, ov.valueOrder ASC';
		return $this->return_instances($this->db->get_rows($sql));

	}

	public function get_unique_sku_code($value, $id, $codes_in_use_on_page)
	{
		$existing_codes = $this->db->get_rows_flat('SELECT DISTINCT valueSKUCode FROM '.$this->table.' WHERE valueID!='.$this->db->pdb((int)$id));
		if (!is_array($existing_codes)) $existing_codes = [];
		$existing_codes = array_merge($existing_codes, $codes_in_use_on_page);
		$code = $this->generate_sku_code($value, $existing_codes);

		return $code;
	}

	public function generate_sku_code($value, $existing_values=array())
	{
		$limit = 4;

		$value = trim($value);
		$value = PerchUtil::urlify($value);
		$value = strtoupper($value);

		$original_value = $value;

		// Multiple words
		if (strpos($value, '-')) {
			$words = explode('-', $value);

			if ((!in_array(strtolower($words[0]), $this->stopwords)) && strlen($words[0]) < $limit) {
				if (!in_array($words[0], $existing_values)) return $words[0];
			}

			$value = '';
			foreach($words as $word) {
				if (!in_array(strtolower($word), $this->stopwords)) {
					$value.=$word[0];
				}
			}

			if (strlen($value)==0) {
				$value = str_replace('-', '', $original_value);
			}

			$original_value = str_replace('-', '', $value);
		}else{

			if (strlen($value) < $limit) {
				if (!in_array($value, $existing_values)) return $value;
			}

			$first_letter = substr($value, 0, 1);
			$rest         = substr($value, 1);
			$value        = $first_letter.str_replace(['A', 'E', 'I', 'O', 'U'], '', $rest);
		}

		$original_value = $value;

		if (strlen($value) < $limit) {
			if (!in_array($value, $existing_values)) return $value;
		}

		$value = substr($value, 0, $limit-1);

		if (!in_array($value, $existing_values)) return $value;

		return $this->generate_random_sku_code($original_value, $existing_values);
	}

	public function generate_random_sku_code($original_value, $existing_values)
	{
		$limit = 4;
		$out = substr($original_value, 0, 2);

		if (!in_array($out.'X', $existing_values)) return $out.'X';

		$letters = str_split('ABCDEFGHJKLMNPQRSTUVWXYZ');

		while (strlen($out)<$limit) {
			if (!in_array($out, $existing_values)) break;
			$out .= $letters[rand(0, count($letters)-1)];
		}

		if (!in_array($out, $existing_values)) return $out;

		$existing_values[] = $out;

		return $this->generate_random_sku_code($original_value, $existing_values);
	}


}