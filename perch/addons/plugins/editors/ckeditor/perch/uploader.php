<?php
    # include the API
    include('../../../../../core/inc/api.php');

    $file       = $_FILES['upload']['name'];
    $filesize   = $_FILES['upload']['size'];
    $funcNum    = $_GET['CKEditorFuncNum'];


    /* -------- GET THE RESOURCE BUCKET TO USE ---------- */
    $bucket_name  = 'default';

    if (isset($_GET['bucket'])) {
        $bucket_name = $_GET['bucket'];
    }

    $Perch = Perch::fetch();
    $Bucket = PerchResourceBuckets::get($bucket_name);

    if ($Bucket) $Bucket->initialise();


    //if the file is greater than 0, process it into resources
    if($filesize > 0) {
    	
    	if ($Bucket->ready_to_write() && isset($file)) {

            $target = $Bucket->write_file($_FILES['upload']['tmp_name'], $_FILES['upload']['name']);

            $urlpath = $Bucket->get_web_path().'/'.$target['name'];

            if(isset($_GET['filetype']) && $_GET['filetype'] == 'image') {

                $width   = 640;
                $height  = false;
                $crop    = false;
                $quality = false;
                $density = false;
                $sharpen = false;


            	if (isset($_GET['width']) && is_numeric($_GET['width'])) {
                    $width = (int) $_GET['width'];
                }              

                if (isset($_GET['height']) && is_numeric($_GET['height'])) {
                    $height = (int) $_GET['height'];
                }

                if (isset($_GET['quality']) && is_numeric($_GET['quality'])) {
                    $quality = (int) $_GET['quality'];
                }

                if (isset($_GET['density']) && is_numeric($_GET['density'])) {
                    $density = (int) $_GET['density'];
                }

                if (isset($_GET['sharpen']) && is_numeric($_GET['sharpen'])) {
                    $sharpen = (int) $_GET['sharpen'];
                }

                if (isset($_GET['crop']) && $_GET['crop']=='true') {
                    $crop = true;
                }
            	
            	$PerchImage = new PerchImage();

                if ($quality) $PerchImage->set_quality($quality);
                if ($sharpen) $PerchImage->set_sharpening($sharpen);
                if ($density) $PerchImage->set_density($density);

            	$result = $PerchImage->resize_image($target['path'], $width, $height, $crop);

                if (is_array($result)) {
                    
                    if (isset($result['web_path'])) {
                        $urlpath = $Bucket->get_web_path().'/'.$result['file_name'];
                    }
                }          	
            }

            echo '<script type="text/javascript">window.parent.CKEDITOR.tools.callFunction('.$funcNum.',"'.$urlpath.'");</script>';
                		
    	} else {
    		echo '<p class="message">Upload failed. Is the directory writable?</p>';
    	}
    } else {
    	echo '<p class="message">Upload failed.</p>';
    }

