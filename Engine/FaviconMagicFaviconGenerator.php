<?php

    # creates new destination folder if it doesn't already exist
	if ($faviconFolder && !is_dir(FOLDER_PATH) ) {
        mkdir(FOLDER_PATH);
    }
	
    function renderFavicon($favicon, $width, $height, $newname, $ext, $fitCanvas, $purpose, $bgcolor = 'transparent', $realWidth, $realHeight, $PATH, $URL, $png8, $msTileSilhouette) {
        
		$options        = array('quality' => 80, 'cropping' => false, 'upscaling' => false); // fallback for non-Imagick
		
		$fileSrc        = $favicon;		
		$fileExt        = pathinfo($fileSrc, PATHINFO_EXTENSION);

        $variation_name = $PATH . $newname . '.' . $ext;
        $variation_url  = $URL  . $newname . '.' . $ext;
		
		if ($bgcolor == null) {
            $bgcolor    = 'transparent';
		} // imagick experiences difficulties unless $bgcolor is *explicitly* set to transparent. Easiest solution is to do so.
		
		copy($fileSrc, $variation_name );
		
		if ($ext == 'svg' && $newname != "safari-pinned-tab"){
			ProcessWire\wire('files')->chmod( $variation_name );
			//file_put_contents($variation_name );
		}
		
		if (!IMAGICK_ON && $ext == 'png') {
			
				$sizer = new  \ProcessWire\ImageSizer( $variation_name );
                $sizer->setOptions($options);
                $sizer->resize($width, $height);
                \ProcessWire\wire('files')->chmod( $variation_name );
				
        } // ends non Imagick resizing. 
        
        if ($fitCanvas) {
            if ($width < 96) {
				
                $newWidth        = $width;
		        $newHeight       = $height;
				
	        } else {

				#add slight buffer from canvas edge for icons over 96px in size
                (int)$newWidth   = $width  - ($width  / 96);
		        (int)$newHeight  = $height - ($height / 96);   
		    }
		  
		} elseif ($realWidth && preg_match('/^mstile.*/', $newname) ) {
			
			# set logo widths for mstiles
			(int)$newWidth      = $realWidth;
            (int)$newHeight     = $realHeight;
				  
        } else {
			
			# add buffer for icons like apple-touch
            (int)$newWidth      =  (int)$width  * .8;
            (int)$newHeight     =  (int)$height * .8;
            
			# add buffer for maskable icons
            if ($purpose == "maskable") {	
                (int)$newWidth  =  (int)$width  / 1.3;
                (int)$newHeight =  (int)$height / 1.3;	
			} // ends maskable options
			
        } // ends fit canvas conditional widths

        if ($fileExt == "svg") {
			
		    $svgFile    = $fileSrc; 
			
			$svgContent = file_get_contents($svgFile);
			
			if ($newname != "safari-pinned-tab") {
		        $svgCrop    = new Imagick($svgFile);
	                          $svgCrop->trimImage(0);
			    $inner      = 0.05;	
		        $svgW       = $svgCrop->getImageWidth() + (2 * $inner); // 2 x inner gap to allow for sliver of whitespace
                $svgH       = $svgCrop->getImageHeight() + (2 * $inner);
		
		        $view_box   = implode(' ', [-1 * $inner, -1 * $inner, $svgW, $svgH]);
			} 
			
			$doc = new DOMDocument();
            $doc->preserveWhiteSpace = false;
            $doc->loadXML($svgContent);

		    $svgTag = $doc->getElementsByTagName('svg');
		    if ( $newname == "safari-pinned-tab" || preg_match('/^mstile.*/', $newname) && $msTileSilhouette ) {
			    $paths = $doc->getElementsByTagName('path');
                
				#change safari pinned tab color to black
                if ( $newname == "safari-pinned-tab") {
                    foreach($paths as $path) {
                        $path->setAttribute('style', 'fill: #000000');
                    }
				# save safari pinned tab color	
				file_put_contents ($variation_name, $doc->saveXML() );
				
				#change mstile color to white
				} else {
                    foreach($paths as $path) {
                        $path->setAttribute('style', 'fill: #FFFFFF');
                    }
				}
            } 
			
			if ( $newname != "safari-pinned-tab" ) {
						
			    foreach($svgTag as $tag) {
					
				    if ($fitCanvas && IMAGICK_ON) {
						
		                $tag->setAttribute("viewBox", $view_box );
		            }
			   
                    $tag->setAttribute('width',   $newWidth);
                    $tag->setAttribute('height', $newHeight);
		        }
			}
			
            $svgString = $doc->saveXML();
        } // ends svg manipulations
		
		elseif ($fitCanvas && IMAGICK_ON) {
			
		   $PNGfileSrc    = $fileSrc;

           $pngFile   = $PNGfileSrc;
           $pngCrop   = new Imagick($pngFile);
                        $pngCrop->trimImage(0);

           $w         = $pngCrop->getImageWidth();
           $h         = $pngCrop->getImageHeight();
	
          ( $w >= $h ) ? $side = $w + 4 : $side = $h + 4; // slight buffer to help maintain curves at edges as they resize down
			
          $top       = (($side-$h)/2) * -1;
          $left      = (($side-$w)/2) * -1;
			
          $pngCrop->setImageBackgroundColor("transparent");
          $pngCrop->extentImage( $side, $side, $left, $top );
		
		} else {
            $pngCrop = file_get_contents($fileSrc);
		} // ends png crop options
				
        if ($ext == 'png' && IMAGICK_ON) {

            $image = new Imagick();	
            $image->setBackgroundColor('transparent');
			        
            if ($fileExt == "svg") {
                $image->readImageBlob( $svgString );

            } else {
                $image->readImageBlob( $pngCrop ); 
                $image->scaleImage( $newWidth, $newHeight, true );
            }
				
            $image->setImageFormat("png");

            $top  = (($height - $newHeight) / 2) * -1;
            $left = (($width - $newWidth) / 2) * -1;
			
            $image->setImageBackgroundColor($bgcolor);
            $image->extentImage( $width, $height, $left, $top );
			
			if ($png8) {
				
			    $image->setOption('png:bit-depth', '8');
			} 
			
            $image->writeImage( $variation_name );
            $image->clear();
				
        } // ends png output

		return $variation_url; // returns url for generated favicon	

	} // ends renderFavicon function

if (is_dir(FOLDER_PATH) ) {
    # setup array for webmanifest
    $manifest = [
        'name'             => $businessName,
        'short_name'       => $shortName,
        'description'      => $businessBlurb,
        'start_url'        => '/',
        'display'          => 'standalone',
        'background_color' => $themeColor,
        'theme_color'      => $themeColor,
	    'icons'            => array()
    ];
	
	#create ico file to add images to as we go		
	if (IMAGICK_ON) {
        $ico = new Imagick();
        $ico->setFormat("ico");
	} else {$ico = false;}
	
    #setup browserconfig.xml to fill as we go
	$browserconfig = new DOMDocument('1.0', 'UTF-8');
    $browserconfig->formatOutput = true;
    $root             = $browserconfig->createElement('browserconfig');
    $msapplication    = $browserconfig->createElement('msapplication'); 
	$tile             = $browserconfig->createElement('tile'); 
	
	# begin loop to generate favicons if needed and markup required for webmanifest and browserconfig
   foreach ( $faviconsArray as $faviconImg => $fields ) {
	    $ext          = $fields['ext'];
	    $incHead      = $fields['incHead'];
	    $faviconName  = $fields['name'];
	    $faviconPath  = FOLDER_PATH . $fields['name'] . '.' . $ext;
		$faviconLink  = ($this->relativePaths) ? $fields['name'] . '.' . $ext : FOLDER_URL  . $fields['name'] . '.' . $ext ;
		$png8         = $this->compressPNGs;

		$width        = $fields['canvasWidth'];
		$height       = ($fields['canvasHeight']) ? $fields['canvasHeight'] : $width ;
		$realWidth    = $fields['realWidth'];
		$realHeight   = ($fields['realHeight'])   ? $fields['realHeight']   : $width ;
		
	    $rawsizes     = ($ext == 'svg') ? '48x48 72x72 96x96 128x128 150x150 256x256 512x512 1024x1024' : $width . 'x' . $height;
	    $fitCanvas    = $fields['fitCanvas'];
	    $purpose      = $fields['purpose'];
	    $bgcolor      = $fields['color'];

        if ($faviconFolder) {
	        $rootPath  = $this->config->paths->root . $fields['name'] . '.' . $ext;

		    if ( file_exists($rootPath) ) {
                 unlink($rootPath);
				 $this->message('Deleted ' . $fields['name'] . '.' . $ext . ' from site root');
			}
		}

	    if ( $ext == 'svg' ) {
		   $manifestType = 'image/svg+xml';
		} 
	    elseif ( $faviconName != 'apple-touch-icon' ) {
		   $manifestType = 'image/' . $ext;
	    } 
		
		$msTileSilhouette = ($silhouetteExists) ? true: false;
		
		# swap image sources for safari pinned tabs and mstiles if silhouette exists
		if ( $silhouetteExists && $faviconName == "safari-pinned-tab" || $silhouetteExists && preg_match('/^mstile.*/', $faviconName) && $msTileSilhouette && IMAGICK_ON) {

		    $source   = $silhouetteSVG;
		} elseif ($faviconName === "favicon" && $faviconSrcExt !=="svg") {
			$source = false;
			
		} elseif($faviconName !== "safari-pinned-tab") {
			
			$source   = $favicon;
		} else {
			$source = false;
		}
		

		
		# render favicons
		if (IMAGICK_ON && $source) {
	    renderFavicon( $source, $width, $height, $faviconName, $ext, $fitCanvas, $purpose, $bgcolor, $realWidth, $realHeight, FOLDER_PATH, FOLDER_URL, $png8, $msTileSilhouette );
		}
		if (!IMAGICK_ON && $source && $purpose !== "maskable" && !preg_match('/^mstile.*/', $faviconName) && $faviconName !== "favicon")  {
	    renderFavicon( $source, $width, $height, $faviconName, $ext, $fitCanvas, $purpose, $bgcolor, $realWidth, $realHeight, FOLDER_PATH, FOLDER_URL, $png8, $msTileSilhouette );
		}
		
		
		# if variations are needed for favicon.ico add them to $ico 
		if ($ico && $ext == "png") {
			
		    if ($width == 16 || $width == 32 || $width == 48) {
				$icoIMG = new Imagick($faviconPath);
			    if ($png8) {
			        $icoIMG->setImageType(\Imagick::IMGTYPE_PALETTEMATTE);
			    } 
                $ico->addImage($icoIMG);
				$icoIMG->clear();
            }
        }	

		if ($purpose == "any" && $manifestType == 'image/svg+xml') {

			$svg = array(
	            'src'     => $faviconLink,
                'sizes'   => $rawsizes,
                'type'    => $manifestType,
	            'purpose' => $purpose
            );
		} elseif ($purpose == "any" && $manifestType !== 'image/svg+xml' || $purpose == "maskable" || $purpose == "any, maskable" ) {

	        $icons = array(
	            'src'     => $faviconLink,
                'sizes'   => $rawsizes,
                'type'    => $manifestType,
	            'purpose' => $purpose
            );
			# add matching icons to manifest array
		    array_push($manifest['icons'], $icons);	

        }//ends manifest icon additions
		
		#append mstile to $browserconfig if they exist
		if ( preg_match('/^mstile.*/', $faviconName) && file_exists($faviconPath) && $purpose){
					 
			$tileName = $purpose;
			
            $tileName = $browserconfig->createElement($purpose);
            $tileName->setAttribute('src', $faviconLink );
            $tile->appendChild($tileName);			   
		}
	
	} //ends loop
	array_push($manifest['icons'], $svg); # add svg icon to end of manifest array
	
	# If it exists, write $ico to favicon.ico path
	if ($ico) {
	    $ico->writeImages($icoPath, true);
		$ico->clear();
	}
	# write contents of webmanifest array to manifest
    file_put_contents ( $manifestPath, json_encode( $manifest, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT ) );
    
	# close off browserconfig and save to file	
	$tilecolor = $browserconfig->createElement('TileColor', $themeColor);
    $tile->appendChild($tilecolor);			   
    $msapplication->appendChild($tile);
    $root->appendChild($msapplication);
    $browserconfig->appendChild($root);
    $browserconfig->save($browserConfigPath);	
	
	clearstatcache();
	$generationComplete = true;
	
	if ($faviconFolder) {

             $rootManifest      = $this->config->paths->root . $manifestFile;
			 $rootBrowserConfig = $this->config->paths->root . 'browserconfig.xml';

		    if ( file_exists($rootManifest) ) {
                 unlink($rootManifest);
				 $this->message('Deleted ' . $manifestFile . ' from site root');
			}
			if ( file_exists($rootBrowserConfig) ) {
                 unlink($rootBrowserConfig);
				 $this->message('Deleted browserconfig.xml from site root');
			}
 
   } 
}