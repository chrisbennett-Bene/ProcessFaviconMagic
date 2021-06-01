<?php namespace ProcessWire;
	
define("FM_FAVICON_FOLDER_NOTES"   , file_get_contents($this->config->paths->siteModules.'ProcessFaviconMagic/MoreInfoTxtFiles/FAVICON_FOLDER_NOTES.txt'    ));
define("FM_FAVICON_ROOT_NOTES"     , file_get_contents($this->config->paths->siteModules.'ProcessFaviconMagic/MoreInfoTxtFiles/FAVICON_ROOT_NOTES.txt'      ));
define("FM_RELATIVE_ABSOLUTE_NOTES", file_get_contents($this->config->paths->siteModules.'ProcessFaviconMagic/MoreInfoTxtFiles/RELATIVE_ABSOLUTE_NOTES.txt' ));
define("FM_PNG8_NOTES"             , file_get_contents($this->config->paths->siteModules.'ProcessFaviconMagic/MoreInfoTxtFiles/PNG8_NOTES.txt'              ));
define("FM_MANIFEST_NOTES"         , file_get_contents($this->config->paths->siteModules.'ProcessFaviconMagic/MoreInfoTxtFiles/MANIFEST_NOTES.txt'          ));
define("FM_SILHOUETTE_NOTES"       , file_get_contents($this->config->paths->siteModules.'ProcessFaviconMagic/MoreInfoTxtFiles/SILHOUETTE_NOTES.txt'        ));
define("FM_FAVICON_SRC_NOTES"      , file_get_contents($this->config->paths->siteModules.'ProcessFaviconMagic/MoreInfoTxtFiles/FAVICON_SRC_NOTES.txt'       ));
define("FM_FAVICONMAGIC_BENEFITS"  , file_get_contents($this->config->paths->siteModules.'ProcessFaviconMagic/MoreInfoTxtFiles/FAVICONMAGIC_BENEFITS.txt'   ));

class ProcessFaviconMagicConfig extends ModuleConfig {	
		
    public function getDefaults() {

        return array(
	         'folderName'          => 'favicons',
			 'showAdvanced'        => 1,
			 'faviconRoot'         => 1,
			 'faviconFolder'       => 1,
			 'showMoreInfo'        => 1,
			 'relativePaths'       => 1,
			 'compressPNGs'        => 1,
			 'manifestName'        => 'manifest',
			 'manifestExtension'   => '.json',
	         'symlink'             => ($this->config->customFilesAlias) ? $this->config->customFilesAlias :'',			 
			 'generateNewFavicons' => '',
	         'themeColor'          => ($this->pages->get("/settings")->business_Color) ? $this->pages->get("/settings")->business_Color : '',
             'appleTouchColor'     => '',
			 'safariPinnedTab'     => '',
			 'businessName'        => ($this->pages->get("/settings")->business_Name)         ? $this->pages->get("/settings")->business_Name         : '',
			 'businessDesc'        => ($this->pages->get("/settings")->business_Blurb)        ? $this->pages->get("/settings")->business_Blurb        : '',
			 'androidAppName'      => ($this->pages->get("/settings")->business_Abbreviation) ? $this->pages->get("/settings")->business_Abbreviation : '',
             'appleAppName'        => '',
			 'msTileColor'         => '',
			 'mobilePreviewZoom'   => 62
			 
        );
    }

	const APPLETOUCH      = 'IOS touch icons require a solid fill. Specify a solid color if the App Theme color does not suit, to avoid it being filled with solid black';
	const SAFARIPINTAB    = 'Specify a solid color for the background of Safari Pinned Tabs. This color is also used as the fill color of your icon on focus';
	const MSTILES         = 'Specify a solid color for the background of Microsoft Tiles as used in the Metro Theme. These tiles are used by Windows 8 and 10';	

	public function getValueDB($returnedField) {
    	
		if(!$returnedField) return;
		
        $templateHasField = $this->fields->get($returnedField)->getFieldgroups()->implode('|', 'name');
        $pageHasTemplate  = $this->pages->find("template=$templateHasField, include=all")->implode('|', 'id');
        $result           = $this->pages->get($pageHasTemplate)->$returnedField;
		
        return $result;
	}
	
	public function addDBconfig($targetField, $targetName) {
		
		$targetValue          = 'db-'. $targetField;
		
		$wrapper              = new InputfieldWrapper();
		$wrapper->columnWidth = 50;
		$wrapper->class       ='dbConfig';
		
		$field                = $this->modules->get("InputfieldSelect");
	    $field->name          = $targetValue;
		$field->id            = $targetValue;
		$field->label         = 'Choose from db - ' . $targetName;
	    $field->icon          = 'cogs';
	    $field->collapsed     = Inputfield::collapsedNever;
		
	    foreach($this->fields as $option) {
			
			$conditions = $this->fields->getNumPages($option) == 1  && strpos($option->getFieldgroups(), 'repeater') === false && strpos($option->getFieldgroups(), 'user') === false;
			
			if( $conditions	&& $option->type == "FieldtypeTextarea" 
				||
                $conditions 
				&& $option->type == "FieldtypeText"	) {
   
                    $field->addOption($option->name);
			}
        }
	
		$wrapper->add($field);
		
	    $field              = $this->modules->get('InputfieldHidden');
		$field->id          = 'returned-'. $targetField;
	    $field->value       = $this->getValueDB($this->$targetValue);
		
		$wrapper->add($field);
		
		return $wrapper;
			
	}
	
    public function getInputfields() {

		$this->config->styles->add($this->config->urls->siteModules . ProcessFaviconMagic::MODULE_DIR . '/' . ProcessFaviconMagic::MODULE_NAME . '.css');
		$this->config->scripts->add($this->config->urls->siteModules . ProcessFaviconMagic::MODULE_DIR . '/' . ProcessFaviconMagic::MODULE_NAME . '.js' );
		$this->config->scripts->add($this->config->urls->siteModules . ProcessFaviconMagic::MODULE_DIR . '/Engine/' . ProcessFaviconMagic::MODULE_NAME . 'Config.js' );

        $inputfields = parent::getInputfields(); 
        $wrapper = new InputfieldWrapper();

        $wrapper->label     = 'What is FaviconMagic and how do I use it?';
        $wrapper->collapsed = Inputfield::collapsedYes;
        $wrapper->icon      = 'info-circle';

        // field show info what
	    $field              = $this->modules->get('InputfieldMarkup');
	    $field->label       = 'What is FaviconMagic?';
	    $field->icon        = 'info';
	    $field->value       = self::MODULE_INFO;
        $field->collapsed   = Inputfield::collapsedNever;
        $field->columnWidth = 50;

        $wrapper->add($field);

	    // field show info what
	    $field              = $this->modules->get('InputfieldMarkup');
	    $field->label       = 'How do I use it?';
	    $field->icon        = 'info';
        $field->value       = self::DIRECTIONS;
        $field->collapsed   = Inputfield::collapsedNever;
	    $field->columnWidth = 50;

        $wrapper->add($field);
        $inputfields->add($wrapper);
		
		$field              = $this->modules->get('InputfieldCheckbox');
	    $field->name        = 'showAdvanced';
	    $field->label       = 'Show Advanced Options for users';
        $field->attr('class', 'autoSaveOnChange');
	    $field->collapsed   = Inputfield::collapsedNever;
		$field->columnWidth = 50;
		
		$inputfields->add($field);
		
		$field              = $this->modules->get('InputfieldCheckbox');
	    $field->name        = 'faviconFolder';
	    $field->label       = 'Place icons in a folder away from site root';
        $field->attr('class', 'autoSaveOnChange');
	    $field->collapsed   = Inputfield::collapsedNever;
		$field->columnWidth = 50;
		
		$inputfields->add($field);
		
		$field              = $this->modules->get('InputfieldCheckbox');
	    $field->name        = 'faviconRoot';
	    $field->label       = 'Place favicon.ico in site root';
        $field->attr('class', 'autoSaveOnChange');
	    $field->collapsed   = Inputfield::collapsedNever;
		$field->columnWidth = 50;
		
		$inputfields->add($field);
		
        $field              = $this->modules->get('InputfieldCheckbox');
	    $field->name        = 'showMoreInfo';
	    $field->label       = 'Show More Info boxes where available';
        $field->attr('class', 'autoSaveOnChange');
	    $field->collapsed   = Inputfield::collapsedNever;
		$field->columnWidth = 50;
		
		$inputfields->add($field);
        
		$field              = $this->modules->get('InputfieldCheckbox');
	    $field->name        = 'relativePaths';
	    $field->label       = 'Use relative paths for links';
        $field->attr('class', 'autoSaveOnChange');
	    $field->collapsed   = Inputfield::collapsedNever;
		$field->columnWidth = 50;
		
		$inputfields->add($field);
		
		$field              = $this->modules->get('InputfieldCheckbox');
	    $field->name        = 'compressPNGs';
	    $field->label       = 'Use indexed png8 to compress png icons and .ico';
        $field->attr('class', 'autoSaveOnChange');
	    $field->collapsed   = Inputfield::collapsedNever;
		$field->columnWidth = 50;
		
		$inputfields->add($field);		
		
        // General Settings Wrapper
        $wrapper            = new InputfieldWrapper();
        $wrapper->collapsed = Inputfield::collapsedNever;
        $wrapper->class     = 'inline';

	    // field Default Favicon Folder
	    $field              = $this->modules->get('InputfieldText');
	    $field->name        = 'folderName';
	    $field->label       = 'Favicon Folder';
	    $field->icon        = 'folder';
	    $field->description = $this->config->urls->files ;
	    $field->placeholder = 'Destination folder for your favicons, webmanifest and browserconfig';
	    $field->collapsed   = Inputfield::collapsedNever;
		$field->columnWidth = 100;

        $wrapper->add($field);
		
		$field              = $this->modules->get('InputfieldText');
	    $field->name        = 'symlink';
	    $field->label       = 'Symlink for /files/';
	    $field->icon        = 'link';
	    $field->description   = str_replace('https://www.', '', rtrim($this->pages->get('/')->httpUrl , '/')) . '/';
	    $field->placeholder = 'Symlink (if set) for ' . $this->config->urls->files . ' or set config.php with: $config->customFilesAlias = \'symlinkName\';';
	    $field->collapsed   = Inputfield::collapsedNever;
		$field->columnWidth = 100;

        $wrapper->add($field);	
	    $inputfields->add($wrapper);
		
		 // field Manifest Name
        $wrapper            = new InputfieldWrapper();
        $wrapper->collapsed = Inputfield::collapsedNever;
        $wrapper->class     = 'inline'; 
		
	    $field              = $this->modules->get('InputfieldText');
	    $field->name        = 'manifestName';
	    $field->label       = 'Manifest name';
	    $field->icon        = 'cog';
	    $field->placeholder = 'Specify name of your Manifest file';
	    $field->collapsed   = Inputfield::collapsedNever;
		$field->columnWidth = 50;

        $wrapper->add($field);	
		
	    $field              = $this->modules->get('InputfieldSelect');
	    $field->name        = 'manifestExtension';
	    $field->label       = 'Manifest extension';
	    $field->icon        = 'cog';
		$field->options     = '.json
.webmanifest';
	    $field->placeholder = 'Choose the extension for your manifest';
	    $field->collapsed   = Inputfield::collapsedNever;
		$field->columnWidth = 50;
		
        $wrapper->add($field);	
	    $inputfields->add($wrapper);
		
		
		// Business Names Wrapper
        $wrapper            = new InputfieldWrapper();
		$wrapper->label     = 'Names';
		$wrapper->icon      = ProcessFaviconMagic::ICON;
        $wrapper->collapsed = Inputfield::collapsedNever;
		$wrapper->class     = 'configPickers';
		
		// Business Name
		$field              = $this->modules->get('InputfieldText');
	    $field->name        = 'businessName';
	    $field->label       = 'Business Name';
        $field->icon        = 'star';
	    $field->collapsed   = Inputfield::collapsedNever;
        $field->columnWidth = 50;
        
		$field->id          = $field->name;
		$wrapper->add($field);
		$wrapper->add( $this->addDBconfig($field->name, $field->label) );
        
		// Business Description
		$field              = $this->modules->get('InputfieldText');
	    $field->name        = 'businessDesc';
	    $field->label       = 'Business Description';
	    $field->icon        ='cogs';
	    $field->collapsed   = Inputfield::collapsedNever;
        $field->columnWidth = 50;

		$field->id          = $field->name;
		$wrapper->add($field);
		$wrapper->add( $this->addDBconfig($field->name, $field->label) );

        // Android App Name
		$field              = $this->modules->get('InputfieldText');
	    $field->name        = 'androidAppName';
	    $field->label       = 'Android Short Name';
	    $field->icon        = 'android';
        $field->placeholder = '10 chars should fit on one line on most devices';
		$field->maxlength   = 50;
		$field->size        = 50;
		$field->showCount   = 1;
	    $field->collapsed   = Inputfield::collapsedNever;
        $field->columnWidth = 50;

		$field->id          = $field->name;
		$wrapper->add($field);
		$wrapper->add( $this->addDBconfig($field->name, $field->label) );

        // Apple App Name
		$field              = $this->modules->get('InputfieldText');
	    $field->name        = 'appleAppName';
	    $field->label       = 'Apple App Name';
	    $field->icon        = 'apple';
        $field->placeholder = '10 chars should fit on one line on most devices';
		$field->maxlength   = 50;
		$field->size        = 50;
		$field->showCount   = 1;
	    $field->collapsed   = Inputfield::collapsedNever;
        $field->columnWidth = 50;

		$field->id          = $field->name;
		$wrapper->add($field);
		$wrapper->add( $this->addDBconfig($field->name, $field->label) );
		
	    $inputfields->add($wrapper);

        // Colors Wrapper
        $wrapper            = new InputfieldWrapper();
        $wrapper->class     = 'configPickers pickers';
		$wrapper->description  = 'Apple Touch Background and MS Tile Color will automatically be generated from the App Theme color if not otherwise specified';
		$wrapper->label     = 'Colors';
        $wrapper->collapsed = Inputfield::collapsedNever;
        $wrapper->icon      = 'paint-brush';

        // Default theme color
        $field              = wire('modules')->get('InputfieldText');
        $field->name        = 'themeColor';
        $field->label       = 'App Theme';
        $field->icon        = 'android';
        $field->placeholder = '#rrggbb or #rgb';
        $field->maxlength   = 7;
        $field->minlength   = 7;
        $field->columnWidth = 50;
        $field->class       = 'colorpicker';
        $field->collapsed = Inputfield::collapsedNever;
		
		$field->id          = $field->name;
		$wrapper->add($field);
		$wrapper->add( $this->addDBconfig($field->name, $field->label) );

        // apple touch color
	    $field              = wire('modules')->get('InputfieldText');
	    $field->name        = 'appleTouchColor';
	    $field->label       = 'OPTIONAL - Set Apple Touch Background';
        $field->description = self::APPLETOUCH;
	    $field->icon        = 'apple';
        $field->placeholder = '#rrggbb or #rgb';
	    $field->maxlength   = 7;
	    $field->minlength   = 7;
	    $field->columnWidth = 50;
        $field->class       = 'colorpicker';
	    $field->collapsed = Inputfield::collapsedNever;

		$field->id          = $field->name;
		$wrapper->add($field);
		$wrapper->add( $this->addDBconfig($field->name, $field->label) );
		
		// Safari Pinned Tab - mac Touch Bar / Focus Color 
	    $field              = wire('modules')->get('InputfieldText');
	    $field->name        = 'safariPinnedTab';
	    $field->label       = 'OPTIONAL - Set Safari Pinned Tab Color';
        $field->description = self::SAFARIPINTAB;
	    $field->icon        = 'apple';
        $field->placeholder = '#rrggbb or #rgb';
	    $field->maxlength   = 7;
	    $field->minlength   = 7;
	    $field->columnWidth = 50;
        $field->class       = 'colorpicker';
	    $field->collapsed = Inputfield::collapsedNever;

		$field->id          = $field->name;
		$wrapper->add($field);
		$wrapper->add( $this->addDBconfig($field->name, $field->label) );
		
		// ms tile color
	    $field              = wire('modules')->get('InputfieldText');
	    $field->name        = 'msTileColor';
	    $field->label       = 'OPTIONAL - Set MS Tile color';
        $field->description = self::MSTILES;
	    $field->icon        = 'windows';
        $field->placeholder = '#rrggbb or #rgb';
	    $field->maxlength   = 7;
	    $field->minlength   = 7;
	    $field->columnWidth = 50;
        $field->class       = 'colorpicker';
	    $field->collapsed = Inputfield::collapsedNever;

		$field->id          = $field->name;
		$wrapper->add($field);
		$wrapper->add( $this->addDBconfig($field->name, $field->label) );
	
	    $inputfields->add($wrapper);
		
        $wrapper            = new InputfieldWrapper();
        $wrapper->collapsed = Inputfield::collapsedNever;
        $wrapper->class     = 'inline';
		
		$field              = $this->modules->get('InputfieldInteger');
		$field->id          = 'defaultZoomValue';
	    $field->name        = 'mobilePreviewZoom';
	    $field->label       = 'Default zoom';
	    $field->description = 'Set default zoom percentage for Mobile preview';
	    $field->placeholder = 'Value between 50 and 100';
	    $field->icon        = 'search';
		$field->size        = 3;
		$field->attr("min", "50");
		$field->attr("max", "100");
	    $field->collapsed   = Inputfield::collapsedNever;
		
		$wrapper->add($field);	
		$inputfields->add($wrapper);
	
        return $inputfields;

    }

	const MODULE_INFO = 
	FM_FAVICONMAGIC_BENEFITS . '
	<h3>Smarter automagic choices</h3>
    <p>Wherever possible, choices that are likely to be the same for multiple devices are defined in one place.</p>	
    <p>For example, there is no good reason to paste the same hex code in multiple places to set the same color for Android, Apple and MS Tiles. So FaviconMagic won\'t make you do that.</p>
	<p>The same principle applies to things like app names and manifest short names &#8212; they\'re all roughly the same size, so FaviconMagic will automatically use one, with the ability to over-ride it if you need to.</p>
    <p>FaviconMagic makes logical assumptions to simplify workflow and remove complexity. Naturally, all these can be over-ridden in <strong>Advanced settings</strong> if you want to fine-tune performance for a specific device.</p>
	<p>In addition, FaviconMagic module config allows you to specify fields from your database to use for different inputs. If you habe a field like <strong>business_Name</strong>, pick the field and FaviconMagic will use that. </p>
	<p>You can take this a step further and make things easier over multiple sites. Assuming you use consistent naming patterns, you can add links to your database fields in the getDefaults() array of ProcessFaviconModuleConfig.php, using something along these lines:</p>
	
<pre><code>
\'symlink\'        => ($this->config->customFilesAlias) 
                   ? $this->config->customFilesAlias :\'\',			 
\'themeColor\'     => ($this->pages->get("/settings")->business_Color) 
                   ? $this->pages->get("/settings")->business_Color: \'\',
\'businessName\'   => ($this->pages->get("/settings")->business_Name) 
                   ? $this->pages->get("/settings")->business_Name: \'\',
\'businessDesc\'   => ($this->pages->get("/settings")->business_Blurb) 
                   ? $this->pages->get("/settings")->business_Blurb: \'\',
\'androidAppName\' => ($this->pages->get("/settings")->business_Abbreviation) 
                   ? $this->pages->get("/settings")->business_Abbreviation : \'\',
</code></pre>

    <h3>Automatic cropping of excess transparent backgrounds where appropriate</h3>
    <p>Smaller icons, such as 16x16 and 32x32, are automatically cropped and resized.</p>
    <p>Automatically removing empty background allows them to fit the available canvas.</p>
    <p>This means you can provide decent padding at large sizes while still maximising impact at small sizes.</p>
    <p>2 or 3 pixels off either side makes a very big difference when you only have 16px to begin with.</p>
    <p>This means you don\'t need to worry about padding and/or providing multiple versions of the same icon resized for a variety of different sizes.</p>

    <h3>Automatic inclusion of markup for svg icons</h3>
    <p>If your source image is svg, FaviconMagic will automatically generate Markup for your svg favicon. 

    <h3>Automatic generation and inclusion of markup for adaptive maskable icons</h3>
    <p>FaviconMagic will automatically generate an adaptive maskable icon and add markup for it to for your webmanifest.</p>
    <p>The adaptive maskable icon will be generated with a background color based on your theme color and safe space automatically calculated.</p>
    <p>No hassles, no worry, no need for special fussing unless you want to.</p>
    ';

    const DIRECTIONS   = '
    <h2>Using FaviconMagic</h2>
    <ol>
    <li>Install the module</li>
	<li>Choose your source file and an optional silhouette/mask svg</li>
	<li>Choose <strong>Generate New Icons</strong></li>
	<li>Include the favicon markup in the &#60;head&#62; of your template/s</li>
    </ol>
	
	<h3>Include the favicon markup in the &#60;head&#62; of your template/s</h3>
	<p>Ensure your markup always stays up to date by copying and pasting the code below in the &#60;head&#62; of the template/s where you want your favicons, web manifest and browserconfig to appear.</p>
	<p><code>&lt;?php include($this->config->paths->files . \'faviconMarkup/faviconMarkup.txt\') ?></code></p>
	<p>The markup this include links to is automatically generated and saved as a text file to the <strong>faviconMarkup</strong> folder to ensure your favicon markup keeps working even if this module is deleted.</p>
	<p>This is a one-time thing and is the last time you\'ll need to copy/paste anything to do with markup.</p>

	<p>Like a lot of things with FaviconMagic, you could choose to copy and paste the generated favicon markup code to the &#60;head&#62; of your document if you prefer.</p>
	<p>We don\'t recommend that. The include method is simply more robust and more flexible: if something important changes, it changes.</p>
	
	<h3>Advanced Settings</h3>
	<p>There are a range of advanced options, most of which you can ignore, or alter at your leisure.</p>
	<p>They are shown by default when the module is initially installed but can easily be hidden once you are happy with them. Naturally, they can be accessed and altered at any time.</p>
	
	<p>These include:</p>
	
	<ul class="tick">
	<li><strong>Place favicons in a separate folder</strong> or dump all favicons in site root instead</li>
	<li><strong>Place favicon.ico in site root</strong> or let browsers download a useless favicon.ico over and over again</li>
    <li><strong>Choose name of favicon folder</strong></li>
	<li><strong>Choose name and extension of your manifest</strong> defaults to manifest.json</li>
    <li><strong>Input custom name of symlink</strong> to /site/assets/files/ if it exists, to use in links</li>
    <li><strong>Use relative links</strong> or choose to use absolute instead if you prefer</li>
    <li><strong>Save PNG favicons as indexed PNG-8</strong> or use larger \'normal\' PNG instead</li>
    <li>Option to specify device-specific colors and names if you want</li>
	</ul>
	
	<h3><strong>More detailed information:</strong> options, recommendations and defaults</h3>
	' .	FM_FAVICON_FOLDER_NOTES 
	  .	FM_FAVICON_ROOT_NOTES 
	  . FM_RELATIVE_ABSOLUTE_NOTES
	  . FM_PNG8_NOTES
	  . FM_MANIFEST_NOTES
;

	
}
?>