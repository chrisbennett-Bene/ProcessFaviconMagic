<?php namespace ProcessWire;

include('FaviconMagicArray.php');
$faviconFolder= $this->faviconFolder;
$faviconRoot  = $this->faviconRoot;
$manifestFile = ($this->manifestName && $this->manifestExtension) ? trim($this->manifestName," ").trim($this->manifestExtension," ") : 'manifest.json';
$grandtotal   = 0;
$subtotal     = 0;

function formatFileSize($bytes)  {
        if ($bytes >= 1073741824) { $bytes = number_format($bytes / 1073741824, 2) . ' GB'; }
        elseif ($bytes >= 1048576){ $bytes = number_format($bytes / 1048576, 2) . ' MB';    }
        elseif ($bytes >= 1024)   { $bytes = number_format($bytes / 1024, 2) . ' kB';       }
        elseif ($bytes > 1)       { $bytes = $bytes . ' bytes';                             }
        elseif ($bytes == 1)      { $bytes = $bytes . ' byte';                              }
        else                      { $bytes = 'Favicon not yet generated';                   }

        return $bytes;
}

function themeContrastSwitch($hexcolor){
    if ( strlen( $hexcolor ) == 0 ) {
        $hexcolor = 'FFFFFF';	 
    } elseif ($hexcolor[0] == '#' ) {
        $hexcolor = substr( $hexcolor, 1 );
    }
    if ( strlen( $hexcolor ) == 6 ) {
        list( $r, $g, $b ) = array( $hexcolor[0] . $hexcolor[1], $hexcolor[2] . $hexcolor[3], $hexcolor[4] . $hexcolor[5] );
    } elseif ( strlen( $hexcolor ) == 3 ) {
        list( $r, $g, $b ) = array( $hexcolor[0] . $hexcolor[0], $hexcolor[1] . $hexcolor[1], $hexcolor[2] . $hexcolor[2] );
    } else {
        return false;
    }
    $light_bckgnd = '#000000';
    $dark_bckgnd  = '#FFFFFF';
 
    $r = hexdec( $r );
    $g = hexdec( $g );
    $b = hexdec( $b );
    $yiq = (($r*299)+($g*587)+($b*114))/1000;
    return ($yiq >= 155) ? $light_bckgnd : $dark_bckgnd;
}

# names of additional files that will be generated automatically
$fileNamesArray = array(
				
                  'ico'           => 'favicon.ico',
                  'manifest'      => $manifestFile,
                  'browserConfig' => 'browserconfig.xml',

);

#generate filepaths and URLs for items defined in $filesNameArray
foreach ( $fileNamesArray as $fileType => $fileName ) {
	
    ${$fileType . 'Path'} = FOLDER_PATH . $fileName;
    ${$fileType . 'URL'}  = FOLDER_URL  . $fileName;
	
}
# get favicons
$faviconExists     = $page->fmFavicon->count();
$silhouetteExists  = $page->fmFaviconSilhouette->count();
if ($faviconExists) {
    $favicon       = $page->fmFavicon->first->filename();
	$faviconSrcExt = $page->fmFavicon->first->ext;
   }
if ($silhouetteExists) {
    $silhouetteSVG = $page->fmFaviconSilhouette->first->filename();
}
# get business details etc
$businessName  = $this->modules->getConfig('ProcessFaviconMagic', 'businessName');
$businessBlurb = $this->modules->getConfig('ProcessFaviconMagic', 'businessDesc');
$appleName     = $this->modules->getConfig('ProcessFaviconMagic', 'appleAppName');
$androidName   = $this->modules->getConfig('ProcessFaviconMagic', 'androidAppName');
$shortName     = $appleName ? $appleName : $androidName ;
$themeColor    = $this->modules->getConfig('ProcessFaviconMagic', 'themeColor');
$touchIconBG   = ( $this->modules->getConfig('ProcessFaviconMagic','appleTouchColor') ) 
                   ? $this->modules->getConfig('ProcessFaviconMagic', 'appleTouchColor')
                   : $themeColor;
$safariPinTab  = ( $this->modules->getConfig('ProcessFaviconMagic','safariPinnedTab') ) 
                   ? $this->modules->getConfig('ProcessFaviconMagic', 'safariPinnedTab') 
                   : $themeColor;
$msTileColor   = ( $this->modules->getConfig('ProcessFaviconMagic','msTileColor') ) 
                   ? $this->modules->getConfig('ProcessFaviconMagic', 'msTileColor')
                   : $themeColor;
# Settings
$generateNew   = $this->modules->get('ProcessFaviconMagic')->generateNewFavicons; // forces users to actively click Generate New Favicons prior to save

# include array of favicons
if ($faviconExists): include('FaviconMagicArray.php'); endif;
# include generator engine to render favicons if $generateNew
if ($faviconExists && $generateNew) : include('FaviconMagicFaviconGenerator.php'); endif;

$folderExists  = is_dir( FOLDER_PATH ); // define after possible new generation

$imagickStatus = (IMAGICK_ON)
                  ? '<p class="success">imagick enabled</p>'
                  : '<p class="warning">imagick is not enabled on this server. Please enable imagick for best results</p>';

$faviconStatus = ($generateNew) 
                  ? '<p class="success">New favicons generated</p>' 
                  : '<p class="informational">No favicons generated since last save. Displaying current favicons.</p>';

$folderStatus  = ($folderExists) 
                  ? '<p class="success">Valid directory: <span>' . FOLDER_URL . '</span></p>'
                  : '<p class="informational">Destination Folder has not yet been created</p>';
$noFolder      = (!$faviconFolder) ? '<p class="informational">Favicons are located in site root</p>' : '';
$pngStatus     = ($this->compressPNGs) 
                  ? '<p class="success">PNG favicons &#x25ba <span>indexed PNG-8</span></p>'
                  : '<p class="informational">PNG favicons not generated as PNG-8</p>';	
				 
$filesStatus   = (SYMLINK_ACTIVE) 
                  ? '<p class="success">Valid symlink: <span> ' . SYMLINK_FILES . '<b> &#x25ba </b>'  . $this->config->urls->files . '</span></p>'
                  : '<p class="warning">Warning! <span>No valid symlink detected for '  . $this->config->urls->files . '</span></p>';

$outputFaviconMagicPreview = '<div class="faviconsPreview">';

$outputFaviconMagicPreview .= (!$folderExists) ? '<p>FaviconMagic has not yet generated your files and folders</p>' : '' ;
$outputRoot  = ($faviconRoot) ? '<h2>Favicons in site root</h2>' : '';

// favicon.ico setup dependent on location
$icoLocation = 'Document Head';

$icoPath     = FOLDER_PATH . '/favicon.ico';
$icoLink     = ($folderExists) ? FOLDER_URL.'favicon.ico' : '' ;

if ($faviconRoot) {

    $icoLocation = 'site root';
	if (file_exists($icoPath)) {
        copy($icoPath, $_SERVER['DOCUMENT_ROOT'].'/favicon.ico' );
	}
	$icoPath = $_SERVER['DOCUMENT_ROOT'].'/favicon.ico';
	$icoLink     = '/favicon.ico';

}

$icobytes    = ( file_exists($icoPath) ) ? filesize($icoPath) : 0;
$icosize     = ($icobytes) ? formatFileSize($icobytes): 'Favicon not yet generated';
$icoExists   = '
		<span class="iconPreview">
		<a href="' . $icoLink . '" title="open full size: favicon.ico" target="_blank">
		<img src="'. $icoLink . '?v='. mt_rand( 1000, 9999 ) . '"></a>
		
		<a href="' . $icoLink . '" title="open full size: favicon.ico" target="_blank">
		<strong>favicon.ico</strong>' . $icosize . '</a>
		</span>';
$icoBlank   = '
	    <span class="iconPreview">
		<strong>favicon.ico</strong>' . $icosize . '
	    </span>';

if ($faviconRoot) {
    if ( file_exists($icoPath) ) {	
		clearstatcache(true, $icoPath); 
        $outputRoot .= $icoExists;	
	} else {
	    $outputRoot .= $icoBlank;
    }

		 $subtotal += $icobytes;
         clearstatcache(true, $icoPath);
		// add in filesize for favicon as well, unless favicon is located at document root
}

$totalRoot = (file_exists($icoPath)) 
              ? '<p class="downloadSize"><span>Favicons in <strong>' . $icoLocation. '</a></strong></span> ' . formatFileSize($subtotal) .'</p>'
              : '<p class="informational">favicon.ico not yet generated</p>';
$grandtotal += $subtotal;

$icoLocation = 'document head';
$outputDocumentHead = '<h2>Favicons declared in <strong>' . $icoLocation . '</strong></h2>';
foreach ( $faviconsArray as $faviconLinks => $fields ) {

    $ext          = $fields['ext'];
	$incHead      = $fields['incHead'];
	$faviconName  = $fields['name'];
    $faviconLink  = FOLDER_URL.$faviconName . '.' . $ext ;
	$purpose      = $fields['purpose'];		
    $width        = ($fields['canvasWidth'])  ? $fields['canvasWidth']  : 150 ;
	$height       = ($fields['canvasHeight']) ? $fields['canvasHeight'] : $width ;

    $filename = FOLDER_PATH . $faviconName . '.' . $ext;
    $bytes    = ( file_exists($filename) ) ? filesize($filename) : 0;
    $filesize = ( $bytes ) ? formatFileSize($bytes): 'Favicon not yet generated';

    if ( $incHead ) {
			  		  
        if ( file_exists($filename) ) { // Display markup for image if the file is either about to be created or already exists 	
            clearstatcache(true, $filename );  
            $outputDocumentHead .= '
	        <span class="iconPreview">
		    <a href="' . $faviconLink . '" title="open full size: ' . $faviconName  . '.' .  $ext . '" target="_blank">
		    <img width="' . $width . '" height="' . $height . '" src="' . $faviconLink . '?v='. mt_rand( 1000, 9999 ) . '"></a> 
		
		    <a href="' . $faviconLink . '" title="open full size: ' . $faviconName  . '.' .  $ext . '" target="_blank">
		    <strong>' . $faviconName  . '.' .  $ext . '</strong>' . $filesize . '</a>
		    </span>';
    
	    } else {
	        $outputDocumentHead .= '
	        <span class="iconPreview">
		    <strong>' . $faviconName  . '.' .  $ext . '</strong>' . $filesize . '
	        </span>';
        }
    
   
       $subtotal += $bytes;
       clearstatcache(true, $filename );
   }
}

if ( !$faviconRoot) {
    if ( file_exists($icoPath) ) {	
		clearstatcache(true, $icoPath); 
        $outputDocumentHead .= $icoExists;	
	} else {
	    $outputDocumentHead .= $icoBlank;
    }

		 $subtotal += $icobytes;
         clearstatcache(true, $icoPath );
		// add in filesize for favicon as well, unless favicon is located at document root
} else {
	
	$subtotal -= $icobytes;
         clearstatcache(true, $icoPath );
	
	}

$totalDocumentHead = ($folderExists) 
                      ? '<p class="downloadSize"><span>Favicons in <strong>' . $icoLocation . '</a></strong></span> ' 
                         . formatFileSize($subtotal) .'</p>'
                      : '<p class="informational">Favicons not yet generated</p>';
$grandtotal += $subtotal;

$icoLocation = $manifestFile;
$outputManifest = '<h2>Favicons declared only in <strong><a href="'.$manifestURL.'" target="_blank">'.$icoLocation.'</a></strong></h2>';
$subtotal = 0;
foreach ( $faviconsArray as $faviconLinks => $fields ) {

    $ext          = $fields['ext'];
	$incHead      = $fields['incHead'];
	$faviconName  = $fields['name'] ;
    $faviconLink  = FOLDER_URL . $faviconName . '.' . $ext ;
	$purpose      = $fields['purpose'];		
    $width        = ($fields['canvasWidth'])  ? $fields['canvasWidth']  : 150 ;
	$height       = ($fields['canvasHeight']) ? $fields['canvasHeight'] : $width ;

    $filename = FOLDER_PATH . $faviconName . '.' . $ext;
    $bytes    = ( file_exists($filename) ) ? filesize($filename) : 0;
    $filesize = ( $bytes ) ? formatFileSize($bytes): 'Favicon not yet generated';

    if ( $purpose && !preg_match('/^mstile.*/', $faviconName) && !$incHead ) {
			  
        if ( file_exists($filename) ) {	
		    clearstatcache(true, $filename );  
            $outputManifest .= '
	        <span class="iconPreview">
		    <a href="' . $faviconLink . '" title="open full size: ' . $faviconName  . '.' .  $ext . '" target="_blank">
		    <img width="' . $width . '" height="' . $height . '" src="' . $faviconLink . '?v='. mt_rand( 1000, 9999 ) . '"></a> 
		
		    <a href="' . $faviconLink . '" title="open full size: ' . $faviconName  . '.' .  $ext . '" target="_blank">
		    <strong>' . $faviconName  . '.' .  $ext . '</strong>' . $filesize . '</a>
		    </span>';
    
	    } else {
	        $outputManifest .= '
	        <span class="iconPreview">
		    <strong>' . $faviconName  . '.' .  $ext . '</strong>' . $filesize . '
	        </span>';
        }
    
   $subtotal += $bytes;
   clearstatcache(true, $filename );
   }
}

$totalManifest = ($folderExists)
                  ? '<p class="downloadSize"><span>Favicons in <strong><a href="'.$manifestURL.'" target="_blank">' 
                     . $icoLocation . '</a></strong></span> '
                     . formatFileSize($subtotal) .'</p>'
                  : '<p class="informational">Favicons not yet generated';

$grandtotal += $subtotal;

$icoLocation = 'browserconfig.xml';
$outputBrowserconfig = '<h2>Favicons declared only in <strong><a href="'. $browserConfigURL . '" target="_blank">' . $icoLocation .  '</a></strong></h2>
<p><strong>Please note:</strong> MS Tile icons are transparent, but displayed here using the background tile color chosen for efficient review.</p>';

$subtotal = 0;
foreach ( $faviconsArray as $faviconLinks => $fields ) {

    $ext          = $fields['ext'];
	$incHead      = $fields['incHead'];
	$faviconName  = $fields['name'];
    $faviconLink  = FOLDER_URL  . $faviconName . '.' . $ext ;
	$purpose      = $fields['purpose'];		
    $width        = ($fields['canvasWidth'])  ? $fields['canvasWidth']  : 150 ;
	$height       = ($fields['canvasHeight']) ? $fields['canvasHeight'] : $width ;

    $filename = FOLDER_PATH . $faviconName . '.' . $ext;
    $bytes    = ( file_exists($filename) ) ? filesize($filename) : 0;
    $filesize = ( $bytes ) ? formatFileSize($bytes): 'Favicon not yet generated';
	
    if (preg_match('/^mstile.*/', $faviconName) ) {
			  
        if ( file_exists($filename) ) {	
		    clearstatcache(true, $filename );
            $outputBrowserconfig .= '
	        <span class="iconPreview">
		    <a href="'.$faviconLink.'" title="open full size: '.$faviconName.'.'.$ext.'" target="_blank">
		    <img width="'.$width.'" height="'.$height.'" src="'.$faviconLink.'?v='.mt_rand( 1000, 9999 ).'" style="background: '.$msTileColor.'"></a> 
		
		    <a href="'.$faviconLink.'" title="open full size: '.$faviconName.'.'.$ext.'" target="_blank">
		    <strong>' .$faviconName.'.'.$ext.'</strong> '.$filesize.'</a>
		    </span>';
    
	    } else {
	        $outputBrowserconfig .= '
	        <span class="iconPreview">
		    <strong>'.$faviconName.'.'.$ext.'</strong> '.$filesize.'
	        </span>';
        }
       
   $subtotal += $bytes;
   clearstatcache(true, $filename );
   }
}

$themeContrastColor = themeContrastSwitch($themeColor);
$mobileInfoFilter   = ($themeContrastColor == '#000000') ? ' style="filter:invert(1);opacity:.7;"' : '';

if     (file_exists(FOLDER_PATH  . 'favicon.svg')        ) {$mobileAppIconLink =' <img src="'.FOLDER_URL.'favicon.svg?v='        . mt_rand(1000,9999) . '">' ;} 
elseif (file_exists(FOLDER_PATH  . 'favicon-192x192.png')) {$mobileAppIconLink =' <img src="'.FOLDER_URL.'favicon-192x192.png?v='. mt_rand(1000,9999) . '">' ;}
else                                                       {$mobileAppIconLink ='';}
				  
$mobileHomeAppIcon = file_exists(FOLDER_PATH.'maskable-512x512.png') 
                                 ? ' style="background-image: url(' . FOLDER_URL  . 'maskable-512x512.png?v='. mt_rand( 1000, 9999 ) . ');"' 
								 : '';				  
					 
$mobileAppTxt       = ($themeContrastColor == '#000000') ? ' style="color:#000000; opacity:.85;"' : '';
$totalBrowserconfig = ($folderExists)
                       ? '<p class="downloadSize">
                          <span>Favicons in <strong><a href="'.$browserConfigURL.'" target="_blank">'.$icoLocation.'</a></strong></span>
						  ' . formatFileSize($subtotal) .'</p>'
                       : '<p class="informational">Favicons not yet generated';
$grandtotal += $subtotal;

$totalAll           = ($folderExists)
                       ? '<hr><p class="downloadSize">
                          <span>Total size of <strong>all</strong> generated favicons</span> '
                          . formatFileSize($grandtotal) . '</p>' 
                       : '';

$outputResultsOverview      = '<div class="faviconInfo">';						   
$outputResultsOverview     .= '<div class="iconStatus"><h2>Status</h2>';
$outputResultsOverview     .= ($folderExists) ? $faviconStatus : '';
$outputResultsOverview     .= $imagickStatus;
if ($faviconFolder) {
    if (!empty($this->symlink)) {$outputResultsOverview     .= $filesStatus;}
                                 $outputResultsOverview     .= $folderStatus;
} else {                         $outputResultsOverview     .= $noFolder;}
$outputResultsOverview     .= ($folderExists) ? $pngStatus : '';

$icoSizeStatus      = ($icobytes < 10000)
                       ? '<p class="success">Success! <span>Raw size of favicon.ico ( <b>'.formatFileSize($icobytes).'</b> ) is < 10 kB</span></p>'
                       : '<p class="warning">Warning! <span>Raw size of favicon.ico ( <b>'.formatFileSize($icobytes).'</b> ) is > 10 kB</span></p>';

$outputResultsOverview     .= ($folderExists) ? $icoSizeStatus : '';

$outputResultsOverview     .= '</div>';                                                                    // ENDS faviconInfo

$outputResultsOverview     .= '<div class="iconTotals"><h2>Favicon file size totals</h2>';
$outputResultsOverview     .= ($faviconRoot && $folderExists) ? $totalRoot : '';
$outputResultsOverview     .= $totalDocumentHead;
$outputResultsOverview     .= ($folderExists) ? $totalManifest : '';
$outputResultsOverview     .= ($folderExists) ? $totalBrowserconfig : '';
$outputResultsOverview     .= $totalAll;
$outputResultsOverview     .= ($folderExists) ? '<p class ="informational">No browser or device uses all variations.</p>' : '';
$outputResultsOverview     .= '</div>';                                                                    // ENDS iconTotals
$outputResultsOverview     .= ($folderExists) ? '<div class="faviconsRoot">' . $outputRoot . '</div> ':''; // ENDS faviconsRoot
$outputResultsOverview     .= '</div>';                                                                    // ENDS faviconInfo

$outputMobilePreview        = '<div class="mobile zoomable"><div id="mobileContentContainer">';

$outputMobilePreview       .= '<div id="blank" class="contentContainer">';
$outputMobilePreview       .= '<div class="mobilePreview">';
$outputMobilePreview       .= '</div>';                                                                    // ENDS mobile Preview (body) container
$outputMobilePreview       .= '</div>';                                                                    // ENDS #mobileHomeScreen

$outputMobilePreview       .= '<div id="mobileHomeScreen" class="contentContainer">';
$outputMobilePreview       .= '<div class="mobilePreview">';
$outputMobilePreview       .= '<div class="mobileInfo"></div>';                                            // ENDS switchable phone info top bar

              $appShortName = ($shortName) ? $shortName : $businessName ; 
$outputMobilePreview       .= ($mobileHomeAppIcon) 
                               ? '<div class="appIcon"><a href="#mobileAppScreen" class="previewLink" title="View app Start Screen">
                               <span class="imgContainer" ' . $mobileHomeAppIcon . '></span>
							   <span class="txtContainer">' . $appShortName . '</span></a></div>'
							   : '';
$outputMobilePreview       .= '</div>';                                                                    // ENDS mobile Preview (body) container
$outputMobilePreview       .= '<div class="mobileBottomIcons"></div>';                                     // ENDS mobile bottom icons
$outputMobilePreview       .= '</div>';                                                                    // ENDS #mobileHomeScreen

$outputMobilePreview       .= '<div id="mobileAppScreen" class="contentContainer">';
$outputMobilePreview       .= '<div class="mobilePreview" style="background-color: ' .$themeColor . ';">';
$outputMobilePreview       .= '<div class="mobileInfo"' .$mobileInfoFilter . '></div>';                    // ENDS switchable phone info top bar
$outputMobilePreview       .= '<div class="appStartContainer">' . $mobileAppIconLink . '</div>';           // ENDS mobile App start icon
$outputMobilePreview       .= '<div class="appStartTxt"' . $mobileAppTxt  . '>' . $businessName . '</div>';// ENDS mobile App start text
$outputMobilePreview       .= '</div>';                                                                    // ENDS mobile Preview (body) container
$outputMobilePreview       .= '<div class="mobileBottomIcons">
                               <a href="#mobileHomeScreen" class="previewLink" title="Return to home screen"></a>
							   </div>';                                                                    // ENDS mobile bottom icons
$outputMobilePreview       .= '</div>';                                                                    // ENDS #mobileAppScreen

$outputMobilePreview       .= '<div id="mobileWebScreen" class="contentContainer">';
$outputMobilePreview       .= '<div class="mobilePreview" style="background-color: ' .$themeColor . ';">';
$outputMobilePreview       .= '<div class="mobileInfo"' .$mobileInfoFilter . '></div>';                    // ENDS switchable phone info top bar
$outputMobilePreview       .= '<iframe id="siteFrame" sandbox="allow-same-origin" src="https://www.alfresco-bar-bistro.com.au/"></iframe>';  
 
$outputMobilePreview       .= '</div>';                                                                    // ENDS mobile Preview (body) container
$outputMobilePreview       .= '<div class="mobileBottomIcons">
                               <a href="#mobileHomeScreen" class="previewLink" title="Return to home screen"></a>
							   </div>';                                                                    // ENDS mobile bottom icons
$outputMobilePreview       .= '</div>';   


$outputMobilePreview       .= '</div>
							   <a href="#blank" id="onOff" class="previewLink"></a>
                               <ul class="mobileContainerLinks">
							   <li><a href="#mobileHomeScreen" class="previewLink currentScreen">Home</a></li>';
$outputMobilePreview       .= (file_exists($manifestPath)) ? 
                              '<li><a href="#mobileAppScreen" class="previewLink">App Start</a></li>
							   <li><a href="#mobileWebScreen" class="previewLink">App</a></li>' : '';
$outputMobilePreview       .= '</ul>
							   </div>';                                                                   // ENDS mobile zoomable container


$outputFaviconMagicPreview .= $outputDocumentHead;	
$outputFaviconMagicPreview .= $outputManifest;
$outputFaviconMagicPreview .= $outputBrowserconfig;


// Markup Generation section

$headMarkup = '';
# Markup generation independent of $generateNew
foreach ( $faviconsArray as $faviconLinks => $fields ) {
	    $rel          = $fields['rel'];
	    $ext          = $fields['ext'];
	    $incHead      = $fields['incHead'];
	    $faviconName  = $fields['name'];
        $width        = $fields['canvasWidth'];
		$height       = ($fields['canvasHeight']) ? $fields['canvasHeight'] : $width ;
		
	    $faviconPath  = FOLDER_PATH . $fields['name'] . '.' . $ext;
        $faviconLink  = FOLDER_URL . $fields['name'] . '.' . $ext;
		
	    $sizes        = ($width && $faviconName != 'apple-touch-icon' ) ? ' sizes="' . $width . 'x' . $height . '"': "";
		
		if ($faviconName == "safari-pinned-tab" ) {
		$color        = ' color="'. $fields["color"] . '"';
		} else {
		$color        = '';
        }
	    if ( $ext == "svg" && $faviconName != "safari-pinned-tab" ) {
	        $type    = 'type="image/svg+xml" ';
	    } elseif ( $ext == 'png' && $faviconName != 'apple-touch-icon' ) {
	        $type    = 'type="image/' . $ext .'" ';
	    } else {
            $type    = '';
	    }
		
        if ( file_exists($faviconPath) && $incHead ) {

            //echo '<link rel="' . $rel . '" ' . $type . 'href="' . $faviconLink . '"' . $sizes . $color . '>';
			$headMarkup .= '<link rel="' . $rel . '" ' . $type . 'href="' . $faviconLink . '"' . $sizes . $color . '>' . PHP_EOL;
        }
}

if(file_exists($manifestPath))      : $headMarkup .= '<link rel="manifest" href="'                       . $manifestURL      . '">' . PHP_EOL; endif;
if(!$faviconRoot && 
   file_exists($icoPath))           : $headMarkup .= '<link rel="shortcut icon" href="'                  . $icoURL           . '">' . PHP_EOL; endif;
if($appleName)                      : $headMarkup .= '<meta name="apple-mobile-web-app-title" content="' . $appleName        . '">' . PHP_EOL; endif;
if(!$appleName && $androidName)     : $headMarkup .= '<meta name="apple-mobile-web-app-title" content="' . $androidName      . '">' . PHP_EOL; endif;
if($androidName)                    : $headMarkup .= '<meta name="application-name" content="'           . $androidName      . '">' . PHP_EOL; endif;
if(!$androidName && $appleName)     : $headMarkup .= '<meta name="application-name" content="'           . $appleName        . '">' . PHP_EOL; endif;
if($msTileColor)                    : $headMarkup .= '<meta name="msapplication-TileColor" content="'    . $msTileColor      . '">' . PHP_EOL; endif;
if(file_exists($browserConfigPath)) : $headMarkup .= '<meta name="msapplication-config" content="'       . $browserConfigURL . '">' . PHP_EOL; endif;
if($themeColor)                     : $headMarkup .= '<meta name="theme-color" content="'                . $themeColor       . '">' . PHP_EOL; endif;


// define path for faviconMarkup folder (single location for markup and to save list of created favicon folders)
$faviconMarkupDir   = $this->config->paths->files. 'faviconMarkup';

## PREVIOUS FOLDER TRACKING
// grab previousFolders (if they exist) to add to array
$previousFolderFile = $faviconMarkupDir . '/previousFolders.php';
if ( file_exists($previousFolderFile) ) {
   include($previousFolderFile);
}

// add new Folder to the previousFolder list if the dir exists
if ($folderExists && isset($this->folderName) && FOLDER_PATH != $this->config->paths->root . '/' ) {
	$previousFolders[FOLDER_PATH] = $this->folderName; 
}
// write new contents to previousFolders.php 
file_put_contents($previousFolderFile, "<?php\n\$previousFolders = ".var_export($previousFolders, true).";\n?>"); // save array back to original location
// grab elibible previous Foldes that a) still exist and b) are not the current folder
$canDelete = array(); 
foreach ( $previousFolders as $key => $value ) { 
    if ( file_exists($key) 
         && $key != FOLDER_PATH 
         && $key != $this->config->paths->root . '/' ) $canDelete[$key] =  $value; 
}
##---

if ( $generateNew ) {
    # creates new destination folder if it doesn't already exist
	if (!is_dir($faviconMarkupDir) ) {
        mkdir($faviconMarkupDir);
    }

    $faviconMarkupPath = $faviconMarkupDir . '/faviconMarkup.txt';
    file_put_contents ( $faviconMarkupPath, $headMarkup );	
	
}

$outputFaviconMagicPreview .= '</div>';

?>