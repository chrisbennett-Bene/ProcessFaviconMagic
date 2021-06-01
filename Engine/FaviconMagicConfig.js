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

window.addEventListener('load', function() {
	    
    var targets = ['businessName', 'businessDesc', 'androidAppName', 'appleAppName', 'themeColor', 'appleTouchColor', 'msTileColor'];
	
    targets.forEach(function(item, index, array) {
	
	    var configInput   =  getById(item);
	    var databaseField =  getById('db-'       + item);
	    var returnedValue =  getById('returned-' + item);
  
        if (databaseField.value != "") {
		    configInput.value = returnedValue.value;
		}
    })
}, false);