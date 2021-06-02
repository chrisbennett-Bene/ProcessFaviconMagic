# ProcessFaviconMagic
<h2>Easy-to-use favicons, web manifest and browserconfig for processwire</h2>
<h3>Designed just for processwire, FaviconMagic simplifies your workflow with custom markup and automatic generation of smarter options</h3>
<ul class="tick">
<li>Simple to use and easy to edit</li>
<li><strong>Support for svg favicons</strong>!</li>
<li>Review generated favicons, sizes, information and settings on the same page</li>
<li>Good defaults with a range of advanced settings (if you need them)</li>
<li>No third party scripts or sites</li>
<li>No need to ever paste in markup again once you set up FaviconMagic</li>
<li>Automatic generation of maskable icon and Lighthouse-friendly manifest, suitable for PWA</li>
</ul>
<p>FaviconMagic takes many of the best ideas and principles of realfavicongenerator (which has always rocked) and implements improvements on them in a seamless processwire environment.</p>
<p>Generating favicon variations is based around manipulating your svg source and outputting once resized, with fallbacks for png.</p>
<p>It's much more than a "clone" of realfavicongenerator: minor annoyances are eliminated, workflow is smoothed and smarter automatic options are implemented. The goal is to provide bang for buck: use the best favicons for modern browsers, while allowing crisp, low-bandwidth fallbacks for others.</p>
<p>This is achieved by threading the needle between what browsers "expect", and what we want to give them.</p>

<h3>A short example: "encouraging" browsers not to download favicons they won't use</h3>
<p>If we markup an old-school shortcut link to ye olde favicon.ico,  chromium-based browsers will obediently download it, via a no-cache request. They won't use it, but it will be downloaded repeatedly on every page unless prevented by your service worker or CDN.</p>
<p>If we place favicon.ico in the site root, the issue goes away, entirely.</p>
<p>Similarly, chromium browsers will try to download a 192x192px favicon from your web manifest, even when they already have a tiny svg which looks great no matter how big it is.</p>
<p>It's not unreasonable to want Chrome/Edge/Brave/Opera etc to see the web manifest and download the tiny favicon svg, instead of the larger 192x192 png it is used to requesting by default. Not a game-breaking bandwidth hog, but mildly annoying when every byte counts: around 15kb for a non-scalable png versus less than 2kb for an infinitely scalable and infinitely crisp svg.</p>
<p>The FaviconMagic web manifest answer to this:</p> 
<ol>
<li>Declare your svg last, after all your pngs &#8212; last one that is *suitable* size *must* get the nod.</li>
<li>Declare the following sizes for your svg &#8212; "sizes": "48x48 72x72 96x96 128x128 <strong>150x150</strong> 256x256 512x512 1024x1024",</li>
</ol>
<p>Setting 192x192 for your svg size doesn't achieve the desired result. It messes up app start pages by using the maskable favicon instead.</p> 
<p>Happily, the addition of the unusual 150x150 size instead works well:</p>
<ul>
<li>192x192 is downloaded only when needed and svg is used otherwise</li>
<li>maskable is used for app icons, rather than app start page, where the extra padding is not ideal for the available space</li>
</ul>
<p>With favicon.ico in the root, and the correct size definition on your svg, chromium browsers download the same tiny svg twice (because of their no-cache request), instead of downloading the svg, favicon.ico and the 192x192px favicon. It has no effect on what the browers can display, they have no need for the last two.</p>
<p>In the event someone wants to add your site as an app with a Chrome shortcut on their Windows 7 machine, Chrome will happily oblige and get favicon.ico when it is needed.</p>
<p>More relevant in the modern world, threading the needle with this size declaration for your svg results in Android app start screens looking fancy, with a nice large favicon instead of the sometimes unnecssarily small maskable option.</p> 
<p><strong>FaviconMagic takes care of this stuff for you, so you don't have to</strong>.</p>

<h2><strong>Benefits of FaviconMagic</strong></h2>
<p>By design, both the favicons that are generated and the separate folder containing a text file of the favicon markup are independent of the module. Even if you delete the module, your favicons and webmanifest will continue working until you make deliberate changes.</p>
<p><strong>You don't need to touch a zip file, copy/paste new markup or worry about missing files</strong>.</p>
<p>FaviconMagic takes care of all the favicon generation, creates a folder, web manifest and browesrconfig and generates the markup for you. Upload your image or images, add a couple of names and your theme color, check the results, click save and you're done.</p>

<h3>Smarter automagic choices</h3>
<p>Wherever possible, choices that are likely to be the same for multiple devices are defined in one place.</p>	
<p>There is no good reason to paste the same hex code in multiple places to set the same color for Android, Apple and MS Tiles. FaviconMagic won't make you do that.</p>
<p>The same principle applies to things like app names and manifest short names &#8212; they're all roughly the same size, so FaviconMagic will automatically use one, with the ability to over-ride it if you need to.</p>
<p>FaviconMagic makes logical assumptions to simplify workflow and remove complexity. Naturally, these can be over-ridden in <strong>Advanced settings</strong> if you want to fine-tune performance for a specific device.</p>
<p>In addition, FaviconMagic module config allows you to specify fields from your database to use for different inputs. If you have a field like <strong>business_Name</strong>, pick the field and FaviconMagic will use that. </p>
<p>You can take this a step further and make things easier over multiple sites. Assuming you use consistent naming patterns, you can add links to your database fields in the getDefaults() array of ProcessFaviconModuleConfig.php, using something along these lines:</p>
	
<pre><code>
'symlink'        => ($this->config->customFilesAlias) ? $this->config->customFilesAlias :'',			 
'themeColor'     => ($this->pages->get("/settings")->business_Color) ? $this->pages->get("/settings")->business_Color: '',
'businessName'   => ($this->pages->get("/settings")->business_Name)  ? $this->pages->get("/settings")->business_Name: '',
'businessDesc'   => ($this->pages->get("/settings")->business_Blurb) ? $this->pages->get("/settings")->business_Blurb: '',
'androidAppName' => ($this->pages->get("/settings")->business_Abbreviation) ? $this->pages->get("/settings")->business_Abbreviation : '',
</code></pre>

<h3>Automatic cropping of excess transparent backgrounds where appropriate</h3>
<p>2 or 3 pixels off either side makes a very big difference when you only have 16px to begin with. Smaller icons, such as 16x16 and 32x32, are automatically cropped and resized.</p>
<p>Automatically removing empty background allows them to fit the available canvas, so you can provide decent padding at large sizes while still maximising impact at small sizes.</p>
<p>This means you don't need to worry about padding and/or providing multiple resized versions for different sizes.</p>

<h3>Automatic inclusion of markup for svg icons</h3>
<p>If your source image is svg, FaviconMagic will automatically generate Markup for your svg favicon. 

<h3>Automatic generation and inclusion of markup for adaptive maskable icons</h3>
<p>FaviconMagic will automatically generate an adaptive maskable icon and add markup for it to for your webmanifest.</p>
<p>The adaptive maskable icon cannot be transparent. It is generated with a background color based on your theme color and safe space automatically calculated.</p>
<p>No hassles, no worry, no need for special fussing unless you want to.</p>
<h2>Using FaviconMagic</h2>
<ol>
<li>Install the module</li>
<li>Choose your source file and an optional silhouette/mask svg</li>
<li>Add a couple of names and your theme color</li>
<li>Click <strong>Generate New Icons</strong></li>
<li>Include the favicon markup in the &#60;head&#62; of your template/s</li>
</ol>
<h3>Include the favicon markup in the &#60;head&#62; of your template/s</h3>
<p>Ensure your markup always stays up to date by copying and pasting the code below in the &#60;head&#62; of the template/s where you want your favicons, web manifest and browserconfig to appear.</p>
<p><code>&lt;?php include($this->config->paths->files . 'faviconMarkup/faviconMarkup.txt') ?></code></p>
<p>The markup this include links to is automatically generated and saved as a text file to the <strong>faviconMarkup</strong> folder to ensure your favicon markup keeps working even if this module is deleted.</p>
<p>This is a one-time thing and is the last time you'll need to copy/paste anything to do with markup.</p>
<p>Like a lot of things with FaviconMagic, you could choose to copy and paste the generated favicon markup code to the &#60;head&#62; of your document if you prefer.</p>
<p>We don't recommend that. The include method is simply more robust and more flexible: if something important changes, it changes.</p>
	
<h3>Advanced Settings</h3>
<p>There are a range of advanced options, most of which you can ignore, or alter at your leisure.</p>
<p>They are shown by default when the module is initially installed but can easily be hidden once you are happy with them. Naturally, they can be accessed and altered at any time.</p>
	
<p>These include:</p>
	
<ul class="tick">
<li><strong>Place favicons in a separate folder</strong>  &#8212;  or dump all favicons in site root instead</li>
<li><strong>Place favicon.ico in site root</strong> &#8212; or force browsers to download a useless favicon.ico</li>
<li><strong>Choose name of favicon folder</strong> &#8212; defaults to 'favicons'</li>
<li><strong>Choose name and extension of your manifest</strong>  &#8212; defaults to manifest.json</li>
<li><strong>Input custom name of symlink</strong> to /site/assets/files/ if it exists, to use in links</li>
<li><strong>Use relative links</strong>  &#8212; or choose absolute if you prefer</li>
<li><strong>Save PNG favicons as indexed PNG-8</strong>   &#8212; or use larger 'normal' PNG</li>
<li>Option to specify device-specific colors and names</li>
</ul>
	
<h3><strong>More detailed information:</strong> options, recommendations and defaults</h3>
<details>
<summary><span><strong>More Info</strong>: Hide favicon stuff away in a folder</span> <em>Recommended</em></summary>
<h2>Place favicons, browswerconfig.xml and web manifest in a separate folder, instead of site root</h2>
<h3><strong>tldr</strong>: Use a separate folder for favicons</strong></h3>
<p>There is no real benefit to scattering all your favicon variations, browserconfig.xml and web manifest around the root of your site.</p>
<p>With the exception of favicon.ico, which we deal with separately because it acts very differently according to the arcane historical rules and mystical rituals of favicon.ico, there is no performance gain and no efficiency gain. It just makes your site root look unnecessarily cluttered.</p>
<p>While we enthusiastically recommend using a separate folder for your favicons, choosing to dump them all in site root instead is as easy as clicking off this option.</p>
<p>If you decide later that you're tired of looking at assorted favicon-related clutter in your site root, click it on again and we'll clean up the unnecessary files for you.</p>
</details>
<details>
<summary><span><strong>More Info</strong>: place favicon.ico at root</span> <em>Highly recommended</em></summary>
<h2>Place favicion.ico at site root</h2>
<h3><strong>tldr</strong>: avoid hassle, place <strong>favicion.ico at site root</strong></h3>
<p>Regardless of where other favicons, browserconfig and webmanifest are placed, it is <strong>much better</strong> to place favicon.ico at the root of your site.</p>
<p>The alternative is to direct browsers to your favicon through the old shortcut meta tag. Because of the ancient and weird ways of favicon.ico, this is not good.</p>
<p>If you include the link to favicon.ico, browsers will obediently download it &#8212; as instructed &#8212; even when they have <strong>no need for it</strong> and <strong>no intention of using it</strong>.</p>
<p>In contrast, if it is just sitting in the site root &#8212; where by the arcane rules of favicon.ico, browsers expect it to be &#8212; they will happily ignore it unless it is needed.</p>
<p>This is handy, especially when chromium-based browsers like Chrome, Edge, Brave, Opera and many more, consistently request a no-cache version of favicons (cache-control: no-cache).</p>
<p>This is probably not ideal, but it is what it is.</p>
<p>So, in a nutshell:</p>
<ol>
<li>Unless favicon.ico is in the root with no link, and </li>
<li>Unless you are using a service worker, and </li>
<li>Until the service worker kicks in,</li>
</ol>
<p>&#8212; then every different page will be downloading the same, <strong>unused</strong> favicon.ico, for no good reason.</p>
<p>Long story short: place favicon.ico in the site root and there's a decent chance it will never be requested or used, until and unless it is needed.</p>

</details><details>
<summary><span><strong>More Info</strong>: use relative links</span> <em>Recommended</em></summary>
<h2>Pros and cons of <strong>absolute vs relative</strong> links</h2>
<h3><strong>tldr</strong>: there's no real difference, so <strong>use the simple one: relative</strong></h3>
<p>Absolute links include the full domain name of your site with every link to a file. For example: <strong>https://www.domainname.com</strong>/folder/subfolder/filename.ext</p>
<p>Relative links show only the path to the file from the site root (domain). For example: /folder/subfolder/filename.ext</p>
<p>Given the browser knows where your root is (because you are there) absolute links are not necessary.</p>
<p>Manifest, browserconfig and links to favicons all work the same whether they are defined as relative or absolute links. The choice is largely a matter of preference and/or company policy.</p>
<p>Defaults to relative because the links are simpler and smaller, but changing that is as simple as toggling this option on or off, whichever you prefer.</p>
</details><details>
<summary><span><strong>More Info</strong>: Advantages of indexed PNG-8</span> <em>PNG-8 is highly recommended</em></summary>
<h2>Pros and cons of <strong>indexed PNG-8</strong></h2>
<h3><strong>tldr</strong>: unless you notice a problem, <strong>use PNG-8</strong></h3>
<p>PNG-8 is an 8-bit PNG. This means it can display up to 256 colors, rather than the 16,777,216 available to a 24-bit PNG-24.</p>
<p>In practice, 256 colors with dithering is usually more than enough to preserve quality at the size of favicons, even when gradients are featured.</p>
<p>Like PNG-24, PNG-8 uses lossless compression and supports alpha transparency and matte.<p>
<p>This option crunches png icons and the bitmaps used to create favicon.ico with <strong>very minimal</strong> quality loss. Technically, it removes unused colors and saves the image as an indexed, dithered PNG-8 while preserving alpha channels (transparency).</p>
<p>Results are usually excellent, with considerably <strong>smaller file sizes</strong> and little or no visible loss of <strong>image quality and clarity</strong>. It generally handles gradients well, especially at the size of favicons.</p>
<p>Even at 512px x 512 px, there are only 262,144 pixels in the entire image. So even if every pixel of the image was a different color, 16 million+ available colors could not be used.<p>
<p>For example, when using PNG-8 to create favicon.ico, the expected size is almost halved: well under 10 kB (even if uncompressed for server transfer) compared to the uncompressed average of around 14.5 kB for a favicon containing 16x16px, 32x32px and 48x48px variations.</p>
<p>This means that even if your server or CDN does not support compression for transfer of .ico files (they should, but some don't), the file size of favicon.ico will still be < 10kb.</p>
<p>Reducing unneccessary data and bandwidth use is always good. As a happy by-product, automated testing tools also won't nag you about your oversized favicon, even if your server doesn't allow the use of brotli/gzip for .ico</p>
<p>If the favicons generated with PNG-8 are not up to scratch, try it again without PNG-8 in a single click.</p>
</details><details>
<summary><span><strong>More Info</strong>: <strong>manifest.json</strong> is bulletproof</span> <em>Optional</em></summary>
<h2>Your web manifest name can be whatever you like: the choice between .json and .webmanifest can be more "interesting"</h2>
<h3><strong>tldr</strong>: <strong>.json</strong>, is hassle-free regardless of web host</h3>
<p>According to the specifications, the "official" extension for your web manifest "should" be .webmanifest. Regardless of whether it is .json or .webmanifest, browsers will treat both as JSON and will work properly.</p>
<p>Because the spec is relatively new, some web hosts will not automatically recognize .webmanifest.</p>
<p>Generally epeaking, it's not too complicated to add the .webmanifest info to your htaccess.</p>
<p>It's not hard to add types and encoding, it's just an added item to do.</p>
<p>You might end up with something like this in your htaccess:</p>
<pre><code>AddDefaultCharset UTF-8
AddCharset UTF-8 .webmanifest
DefaultLanguage en
AddType application/manifest+json .webmanifest
</code></pre>
<p>Unfortunately, some hosts &#8212; like Siteground, for example &#8212; no longer allow the option to add new file types with htaccess to their automatic default brotli/gzip transfer compression.</p>
<p>While the file is tiny either way, if warnings about your web manifest not being compressed and/or the added inconvenience of adding type and default character encoding for .webmanifest do not appeal, then .json is a very solid alternative, that works out of the box.</p>
<p>Of course, some online testing tools will flag your use of .json as an issue because it is not the newly "correct" extension for your web manifest.</p>
<p>Either way, browsers treat the info as JSON, so in a perfect world none of this would even be a thing worth considering.</p>
</details>
