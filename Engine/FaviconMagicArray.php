<?php  

# array of different favicon names, sizes and settings
$faviconsArray = array(
					   
			 array(
                'name'         => 'favicon',
                'incHead'      => true,
                'rel'          => 'icon',
                'ext'          => 'svg',
                'fitCanvas'    => true,
                'purpose'      => 'any',
				'color'        => '',
                'canvasWidth'  => '',
				'canvasHeight' => '',
				'realWidth'    => '',
				'realHeight'   => ''
            ),
	
			array(
                'name'         => 'apple-touch-icon',
                'incHead'      => true,
                'rel'          => 'apple-touch-icon',
                'ext'          => 'png',
                'fitCanvas'    => false,
                'purpose'      => '',
				'color'        => ( $this->appleTouchColor ) ? $this->appleTouchColor : $this->themeColor ,
				'canvasWidth'  => '180',
				'canvasHeight' => '',
				'realWidth'    => '',
				'realHeight'   => ''
            ),			
			
            array(
                'name'         => 'favicon-32x32',
                'incHead'      => true,
                'rel'          => 'icon',
                'ext'          => 'png',
                'fitCanvas'    => true,
                'purpose'      => '',
				'color'        => '',
				'canvasWidth'  => '32',
				'canvasHeight' => '',
				'realWidth'    => '',
				'realHeight'   => ''
            ),
		
			array(
                'name'         => 'favicon-16x16',
                'incHead'      => true,
                'rel'          => 'icon',
                'ext'          => 'png',
                'fitCanvas'    => true,
                'purpose'      => '',
				'color'        => '',
                'canvasWidth'  => '16',
				'canvasHeight' => '',
				'realWidth'    => '',
				'realHeight'   => ''
            ),
			
			array(
                'name'         => 'favicon-48x48',
                'incHead'      => false,
                'rel'          => 'icon',
                'ext'          => 'png',
                'fitCanvas'    => true,
                'purpose'      => 'any',
				'color'        => '',
                'canvasWidth'  => '48',
				'canvasHeight' => '',
				'realWidth'    => '',
				'realHeight'   => ''
             ),

            array(
                'name'         => 'favicon-192x192',
                'incHead'      => false,
                'rel'          => 'icon',
                'ext'          => 'png',
                'fitCanvas'    => true,
                'purpose'      => 'any',
				'color'        => '',
                'canvasWidth'  => '192',
				'canvasHeight' => '',
				'realWidth'    => '',
				'realHeight'   => ''
            ),
			
			array(
                'name'         => 'favicon-512x512',
                'incHead'      => false,
                'rel'          => 'icon',
                'ext'          => 'png',
                'fitCanvas'    => true,
                'purpose'      => 'any',
				'color'        => '',
                'canvasWidth'  => '512',
				'canvasHeight' => '',
				'realWidth'    => '',
				'realHeight'   => ''
            ),
			
			array(
                'name'         => 'maskable-512x512',
                'incHead'      => false,
                'rel'          => 'icon',
                'ext'          => 'png',
                'fitCanvas'    => false,
                'purpose'      => 'maskable',
				'color'        => $this->themeColor,
                'canvasWidth'  => '512',
				'canvasHeight' => '',
				'realWidth'    => '',
				'realHeight'   => ''
            ),
			
			array(
                'name'         => 'safari-pinned-tab',
                'incHead'      => true,
                'rel'          => 'mask-icon',
                'ext'          => 'svg',
                'fitCanvas'    => false,
                'purpose'      => '',
				'color'        => ( $this->safariPinnedTab ) ? $this->safariPinnedTab : $this->themeColor ,
                'canvasWidth'  => '',
				'canvasHeight' => '',
				'realWidth'    => '',
				'realHeight'   => ''
            ),
			
			array(
                'name'         => 'mstile-128x128',
                'incHead'      => false,
                'rel'          => '',
                'ext'          => 'png',
                'fitCanvas'    => false,
                'purpose'      => 'square70x70logo',
				'color'        => '',
                'canvasWidth'  => '128',
				'canvasHeight' => '',
				'realWidth'    => '58',
				'realHeight'   => ''
            ),
			
			array(
                'name'         => 'mstile-270x270',
                'incHead'      => false,
                'rel'          => '',
                'ext'          => 'png',
                'fitCanvas'    => false,
                'purpose'      => 'square150x150logo',
				'color'        => '',
                'canvasWidth'  => '270',
				'canvasHeight' => '',
				'realWidth'    => '108',
				'realHeight'   => ''
            ),
			
			array(
                'name'         => 'mstile-558x558',
                'incHead'      => false,
                'rel'          => '',
                'ext'          => 'png',
                'fitCanvas'    => false,
                'purpose'      => 'square310x310logo',
				'color'        => '',
                'canvasWidth'  => '558',
				'canvasHeight' => '',
				'realWidth'    => '234',
				'realHeight'   => ''
            ),
			
			array(
                'name'         => 'mstile-558x270',
                'incHead'      => false,
                'rel'          => '',
                'ext'          => 'png',
                'fitCanvas'    => false,
                'purpose'      => 'wide310x150logo',
				'color'        => '',
                'canvasWidth'  => '558',
				'canvasHeight' => '270',
				'realWidth'    => '108',
				'realHeight'   => '108'
            ),
			
);