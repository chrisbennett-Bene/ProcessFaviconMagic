var get           = function (selector, scope) {
                    scope = scope ? scope : document; 
                    return scope.querySelector(selector);
                    };
var getById       = function (selector, scope) {
                    scope = scope ? scope : document; 
                    return scope.getElementById(selector);
                    };
var getAll        = function (selector, scope) {
                    scope = scope ? scope : document; 
                    return scope.querySelectorAll(selector);
                    };

function docReady(fn) {
    // see if DOM is already available
    if (document.readyState === "complete" || document.readyState === "interactive") {
        // call on next available tick
        setTimeout(fn, 1);
    } else {
        document.addEventListener("DOMContentLoaded", fn);
    }
}    
function hideEmptyWrapper() {
	
    var wrappers = getAll('.InputfieldStateShowIf.InputfieldCheckbox, .InputfieldStateShowIf.inline, .InputfieldStateShowIf.InputfieldMarkup, .InputfieldStateShowIf.pickers, .InputfieldStateShowIf.colorpicker');
	
    wrappers.forEach(function (inputfield, index) {
		
        if (inputfield.classList.contains('InputfieldStateHidden')) {	
		
		    inputfield.classList.add('hideWRAPPER')
		    if (!inputfield.classList.contains('InputfieldMarkup')) inputfield.closest('.InputfieldWrapper').classList.add('hideWRAPPER');
	   
        } else {
			
		    inputfield.classList.remove('hideWRAPPER')
			
			if (!inputfield.classList.contains('InputfieldMarkup')) inputfield.closest('.InputfieldWrapper').classList.remove("hideWRAPPER");

            Array.from(getAll('.showHide')).forEach(function(el) {el.classList.remove('showHide');});
		};
     });	

}
function characterWarning(targetInput,minChar,maxChar,recommendedChar ) {
		
    var targetInputChar = targetInput.value.length;
	targetInput.classList.add('charCount');	
   	
	if (targetInputChar > minChar) {
			
        if (targetInputChar <= maxChar && targetInputChar <= recommendedChar) {
            targetInput.classList.add('charValid');			
            targetInput.classList.remove('charWarning');
        } else {
            targetInput.classList.add('charWarning');			
            targetInput.classList.remove('charValid');
        }				
    } else {
		targetInput.classList.remove('charValid','charWarning');		
	}
}

function getMaxInput(maxCharInput){

          var maxCharSet      = maxCharInput.getAttribute('maxlength');
          var recommendChar   = maxCharInput.getAttribute('data-recommended');

          if(recommendChar !== null && recommendChar !== '') {
              var suggested = recommendChar;
          } else {
              var suggested = maxCharSet;	
          }
			
          characterWarning(maxCharInput,1,maxCharSet,suggested );	
    	
};


function getInitialHeight() {

	var targetContainer = get('#faviconStatus .InputfieldContent'); // get targetContent container
	if (targetContainer == null) return;
	
	heightsArray =[];
	targetContainer.style.display = 'block'; // allow accurate targetContent height, without flex stretch of container
	var paddingBottom   = parseInt(window.getComputedStyle(targetContainer).getPropertyValue('padding-bottom'), 10); // get relevant container padding
    
	var targetContent   = get('#faviconStatus .faviconInfo'); // get content size, minus height from flex stretch
	var targetHeight    = targetContent.offsetHeight + paddingBottom; //account for container padding
	
	targetContainer.style.removeProperty('display'); // remove temporary display:block from container
   
    heightsArray.push(targetHeight);
 
	Array.from(getAll('.mobile')).forEach(function(field,index){ 			

        var mobileHeight = field.getBoundingClientRect().height;
		heightsArray.push(mobileHeight);

	});
    initalPreviewHeight = heightsArray.reduce(function(a, b) {return Math.max(a, b);});
};

function zoomResize() {
	
    var zoomController = getById('mobilePreviewZoom');
	if (zoomController == null) return;
	
    var zoomValue      = zoomController.value; 
	
    Array.from(getAll('.zoomable')).forEach(function(el,index){ 
        el.setAttribute( 'style', 'transform:scale(' + zoomValue / 100 +  ');' );	
    });
	
    Array.from(getAll('.hideWrapperStyling .InputfieldContent')).forEach(function(field,index){ 
		
		var newHeight = Math.ceil(initalPreviewHeight);
					
		field.style.maxHeight  = newHeight + "px";
		field.style.minHeight  = newHeight + "px";
			
	});
};

function rangeProgress(range,value) {
		
		var rangeMin     = range.min;	
        var rangeMax     = range.max;
		var rangeValue   = (value) ? value : range.value;
		
        var rangePercent = (rangeValue-rangeMin) / (rangeMax-rangeMin)*100;
		
		range.style.setProperty('--slider-progress', rangePercent +'%');
		range.nextSibling.value = rangeValue;
		getInitialHeight();
		zoomResize();
}

function setRange () {
	var ranges = getAll('input[type="range"].rangeSlider');
	ranges.forEach(function (range, index) {
	
	rangeProgress(range);
							 
	});						 	
}
function updateSliderValue (input,setDelay) {
    var timeout = null;	
    var slider = input.previousSibling;
    clearTimeout(timeout);
	var delay = (setDelay) ? setDelay : 0;
	
	timeout = setTimeout(function () {
		 var newSliderValue = Math.min(slider.max, Math.max(slider.min, Math.floor(input.value)));
         slider.value = newSliderValue;
         rangeProgress(slider,newSliderValue);
    }, delay);
}

		

function styleFrame(targetFrame) {
	var iFrame = getById(targetFrame);
    var doc = iFrame.contentWindow.document.getElementsByTagName('html')[0];
    doc.setAttribute( 'style', 'overflow: hidden!important;');
	var frameBody = iFrame.contentWindow.document.getElementsByTagName('body')[0];
	frameBody.setAttribute( 'style', 'overflow: hidden!important;');
	frameBody.classList.remove('has-tracy-debugbar','pw-AdminThemeUikit'); /* can't go though whole page switching pw styles off. need better solution */
}

docReady(function() {
    setRange();
	getInitialHeight();
	zoomResize();
	hideEmptyWrapper();
	
	
});

window.addEventListener('load', function() {
		
        var color = document.querySelectorAll('.colorpicker');
        color.forEach(function (color, index) {
            if (color.type == "text" && color.value) {
                color.type = "color";
				color.setAttribute('placeholder', 'Right-click to copy, paste or remove hex code');
            } 
        });
		
		var characterMaxInputs = document.querySelectorAll('.InputfieldTextLength');
		characterMaxInputs.forEach(function (maxCharInput, index) {
		 
		    getMaxInput(maxCharInput);
        	
        });
		
	
    window.addEventListener('contextmenu', function (event) {
        if (event.target.classList.contains('colorpicker')){
            if (event.target.type == "color" && event.target.value) {event.target.type = "text";
						} 
        }
		}, false);	
		
    var initColorpicker =  function (event) {
        if (event.target.classList.contains('colorpicker')){
            if (event.target.type == "text" && event.target.value && event.target.value.length == 7) {
                event.target.type = "color";
				event.target.setAttribute('placeholder', 'Right-click to copy, paste or remove hex code');
            } else if (event.target.type == "text" && event.target.value.length == 4){
                var hex = event.target.value;
					hex = "#" + hex.charAt(1) + hex.charAt(1) + hex.charAt(2) + hex.charAt(2) + hex.charAt(3) + hex.charAt(3);
					event.target.value = hex;
			} else if (event.target.type == "text" && event.target.value.length == 0){
				event.target.setAttribute('placeholder', '#rrggbb or #rgb');
			}
         }
    };
	
    // Add our event listeners
    window.addEventListener('change', initColorpicker, false);
    window.addEventListener('mouseout', initColorpicker, false);
    
    // trigger sliderRange update from direct input display, setting ms delay to allow typing
    window.addEventListener('keyup', function (event) {
        var input = event.target;
		if (input.classList.contains('rangeSliderDisplay')) updateSliderValue(input,850);											   
											   
    }, false);
	
	window.addEventListener('click', function (event) {
        var input = event.target;
		var onOff = getById('onOff');
		var contentContainers = getAll('.contentContainer');		
        var containerLinks = getAll('.previewLink');
		
		if (input.closest('a') && input.closest('a').classList.contains('previewLink')) {
			
			var linkTarget = input.closest('a').getAttribute('href').replace('#','') ;

            contentContainers.forEach(function (contentContainer, index) {

			    if (contentContainer.id.replace('#','') == linkTarget ) {contentContainer.setAttribute( 'style', 'display:block');} 
                else                                                    {contentContainer.setAttribute( 'style', 'display:none' );}
            });

            containerLinks.forEach(function (containerLink, index) {
				
			    if (containerLink.getAttribute('href').replace('#','') == linkTarget ) {containerLink.classList.add('currentScreen')   ;} 
			    else                                                                   {containerLink.classList.remove('currentScreen');}
		});

        if (onOff.classList.contains('currentScreen') && onOff.getAttribute('href') != '#mobileHomeScreen') {
			onOff.setAttribute('href', '#mobileHomeScreen')
			} else {
			onOff.setAttribute('href', '#blank') 	
			}

		event.preventDefault();
		};
    }, false);
	
	// trigger sliderRange update from direct input display, no delay on mouseout
    window.addEventListener('mouseout', function (event) {
        var input = event.target;
		if (input.classList.contains('rangeSliderDisplay')) updateSliderValue(input,0);											   
											   
    }, false);
	
    window.addEventListener('input', function (event) {

        var input = event.target;
		
		if (input.classList.contains('rangeSlider')){
			rangeProgress(input);
        }
		
		if (input.classList.contains('InputfieldTextLength')){
			getMaxInput(input);
        }

		if (input.id == 'mobilePreviewZoom') { 
			getInitialHeight();
			zoomResize();
		}
		
        if (input.classList.contains('autoSaveOnChange')){
			
             var showAdvanced = getById('showAdvanced');
             var addedButton  = getById('deleteSelectedFolders');			
			 var generateCheckbox = getById('generateNewFavicons');
			 
			if( input.id == 'generateNewFavicons') {
			console.log('Got it');	
			    showAdvanced.checked = false;
				
			}
			
            if (input.classList.contains('autoGenerateNew')){
               
			    generateCheckbox.checked = true;
				showAdvanced.checked = false;
              
            };

            input.closest('.InputfieldContent').classList.add('saveLoading');
		    
			var saveSubmit   = getById('submit_save_copy') || getById('submit_save') || getById('Inputfield_submit_save_module');
            
			if (addedButton) saveSubmit.click(); // extra click if extra button to work around weird hijacking. Not ideal but better than alternative
			saveSubmit.click();

        }
		
		setTimeout(function(){
           hideEmptyWrapper();
		}, 5);
	
		if (input.id == 'selectDeselect') {
            var selectTargets = getAll('.fmToggleSelect');
			
            if (input.checked == true) {
               var labelContent    = 'Deselect All';
			   var selectAllStatus = true;
            } else {
			   var labelContent    = 'Select All';
			   var selectAllStatus = false;
            }
            input.closest('label').lastElementChild.textContent=labelContent;
            selectTargets.forEach(function (target, index) {
            target.checked = selectAllStatus;
            
            // update labels from select/deselect
            if (target.id != 'selectDeselect' ) {

                var inputCell = target.closest('td');
                var labelCell = inputCell.nextElementSibling.firstElementChild;
                var lastmCell = inputCell.nextElementSibling.nextElementSibling;
                var pathsCell = inputCell.nextElementSibling.nextElementSibling.nextElementSibling;
			 
                if (selectAllStatus) {
                    labelCell.classList.add('deleteTarget');
                    lastmCell.classList.add('deleteTarget');
                    pathsCell.classList.add('deleteTarget');
                } else {
                    labelCell.classList.remove('deleteTarget');
                    lastmCell.classList.remove('deleteTarget');
                    pathsCell.classList.remove('deleteTarget');
                }
			}
			 
		   });						     
		};	
		
        // update labels from individual selections
		if (input.classList.contains('fmToggleSelect')) {
			  
			var inputCell = input.closest('td');
			var labelCell = inputCell.nextElementSibling.firstElementChild;
			var lastmCell = inputCell.nextElementSibling.nextElementSibling;
            var pathsCell = inputCell.nextElementSibling.nextElementSibling.nextElementSibling;			
			
			if (input.checked == true) {
                labelCell.classList.add('deleteTarget');
                lastmCell.classList.add('deleteTarget');
                pathsCell.classList.add('deleteTarget');
            } else {
                labelCell.classList.remove('deleteTarget');
                lastmCell.classList.remove('deleteTarget');
                pathsCell.classList.remove('deleteTarget');
           }	   
        };
		
    }, false);

    var targetNode = getById('_tab-1');// Select the Page Button field to observe for mutations   																							
        var config = {attributes:true};
        var callback = function() {
		        if(targetNode.classList.contains('pw-active')) {
			       getInitialHeight();
			       zoomResize();
				   styleFrame('siteFrame');
		        };
        };
// Create an observer instance linked to the callback function and start observing the target node for configured mutations
    if (targetNode) {
        var observer = new MutationObserver(callback); 
		observer.observe(targetNode, config);
	}
}, false);