<?php

class PerchListings_Util extends PerchAPI_Factory
{
	protected $table     = 'listings_listings';
	protected $pk        = 'listingID';
	protected $singular_classname = 'PerchListings_Listing';

	protected $resource_bucket = 'default';
	protected $import_folder = false;

	public function find_importable_files()
	{
		return PerchUtil::get_dir_contents(PerchUtil::file_path(PERCH_PATH.'/addons/apps/'.$this->api->app_id.'/import_data/'), true);
	}

	public function find_templates()
	{
		$app_templates = $this->get_dir_contents(__DIR__.'/templates/listings');
        $local_templates = $this->get_dir_contents(PERCH_TEMPLATE_PATH.'/listings');

        $templates = array_merge($app_templates, $local_templates);
        sort($templates);
        return $templates;
	}

	public function get_dir_contents($dir, $prefix='')
    {
        $Perch = Perch::fetch();

        $a = array();
        if (is_dir($dir)) {
            if ($dh = opendir($dir)) {
                while (($file = readdir($dh)) !== false) {
                    if(substr($file, 0, 1) != '.' && !preg_match($Perch->ignore_pattern, $file)) {
                        if (is_dir($dir.DIRECTORY_SEPARATOR.$file)) {
                        	$sub = $this->get_dir_contents($dir.DIRECTORY_SEPARATOR.$file, $file.DIRECTORY_SEPARATOR);
                        	if (PerchUtil::count($sub)) {
                        	 	$a = array_merge($a, $sub);
                        	}
                        }else{
                        	if (PerchUtil::file_extension($file)=='html') {
                        		$a[] = $prefix.$file;
                        	}
                        }
                    }
                }
                closedir($dh);
            }
            sort($a);
        }
        return $a;
    }

	public function import_from_posterous($folder, $format='html', $bucket='default', $sectionID=1)
	{
		$folder_path = PerchUtil::file_path(PERCH_PATH.'/addons/apps/'.$this->api->app_id.'/import_data/'.$folder);
		$this->import_folder = $folder_path;

		if (is_dir($folder_path)) {
			$wordpress_file = PerchUtil::file_path($folder.'/wordpress_export_1.xml');
			if (file_exists(PerchUtil::file_path(PERCH_PATH.'/addons/apps/'.$this->api->app_id.'/import_data/'.$wordpress_file))) {

				$this->resource_bucket = $bucket;

				return $this->import_from_wp($wordpress_file, 'html', array($this, 'posterous_process_images'), $sectionID);
			}
		}else{

		}
	}


	public function posterous_process_images($post, $Template)
	{
		$html = $post['listingDescHTML'];

		// find posterous URLs
		// <img alt="Img_8371" height="333" src="http://getfile0.posterous.com/getfile/files.posterous.com/temp-2012-02-04/ybzoAslvztsefCumHsmxEuFjiEutyFpnhGanxcfyunylvDaoAhgpAxChyrnp/IMG_8371.jpg.scaled500.jpg" width="500"/>
		// <a href="http://getfile0.posterous.com/getfile/files.posterous.com/temp-2012-02-04/ybzoAslvztsefCumHsmxEuFjiEutyFpnhGanxcfyunylvDaoAhgpAxChyrnp/IMG_8371.jpg.scaled1000.jpg">

		$s = '/<img[^>]*src="[^"]*posterous\.com[^"]*"[^>]*>/';
		$count	= preg_match_all($s, $html, $matches);

		$PerchImage = $this->api->get('Image');
		$image_folder = $this->import_folder.'/image/';

		$Perch = Perch::fetch();
		$bucket = $Perch->get_resource_bucket($this->resource_bucket);

		if ($count) {
			foreach($matches as $match) {
				$Tag = new PerchXMLTag($match[0]);

				// Find the file name
				$parts = explode('/', $Tag->src());
				$filename = array_pop($parts);
				$linkpath = str_replace($filename, '', $Tag->src());
				$fileparts = explode('.scaled', $filename);
				$filename = array_shift($fileparts);
				$linkpath .= $filename;

				// Find the temp-YYYY-MM-DD part of the path to find the image folder
				$s = '/\/temp-([0-9]{4})-([0-9]{2})-[0-9]{2}\//';
				$count = preg_match($s, $Tag->src(), $path_matches);

				if ($count) {

					$folder = PerchUtil::file_path($image_folder.$path_matches[1].'/'.$path_matches[2].'/');
					$files = PerchUtil::get_dir_contents($folder, false);

					if (PerchUtil::count($files)) {

						$l = strlen($filename);

						$image_file = false;

						foreach($files as $file) {
							PerchUtil::debug(substr($file, -$l));
							if (substr($file, -$l)==$filename) {
								$image_file = PerchUtil::file_path($folder.$file);
								break;
							}
						}

						if ($image_file) {
							$new_image_file = PerchUtil::file_path($bucket['file_path'].'/'.$file);
							copy($image_file, $new_image_file);

							$new_image = $PerchImage->resize_image($new_image_file, (int)$Tag->width(), (int)$Tag->height());

							$img_html = '<img src="'.$new_image['web_path'].'" width="'.$new_image['w'].'" height="'.$new_image['h'].'" alt="'.PerchUtil::html($Tag->alt()).'" />' ;

							if (defined('PERCH_XHTML_MARKUP') && PERCH_XHTML_MARKUP==false) {
		    					$img_html = str_replace(' />', '>', $img_html);
							}

							$html = str_replace($match[0], $img_html, $html);

							// find links to the bigger version
							$s = '/<a[^>]*href="'.preg_quote($linkpath, '/').'[^"]*"[^>]*>/';
							$s = preg_replace('#getfile[0-9]{1,2}#', 'getfile[0-9]{1,2}', $s);
							$count	= preg_match_all($s, $html, $link_matches);

							if ($count) {
								$big_image = $PerchImage->resize_image($new_image_file, (int)$Tag->width()*2, (int)$Tag->height()*2);
								$link_html = '<a href="'.$big_image['web_path'].'">';

								foreach($link_matches as $link_match) {
									$html = str_replace($link_match[0], $link_html, $html);
								}
							}else{
								PerchUtil::debug('FAIL', 'error');
								PerchUtil::debug($new_image);
								PerchUtil::debug($s);
								PerchUtil::debug($link_matches);
							}
						}

					}

				}


			}
		}


		$post['listingDescHTML'] = $html;
		$post['listingDescRaw'] = $html;

		return $post;
	}




}


