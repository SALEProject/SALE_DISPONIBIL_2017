var vis = (function(){
    var stateKey;
	var eventKey;
    var keys = {
		hidden: "visibilitychange",
		webkitHidden: "webkitvisibilitychange",
		mozHidden: "mozvisibilitychange",
		msHidden: "msvisibilitychange"
	};
    for (stateKey in keys) {
        if (stateKey in document) {
            eventKey = keys[stateKey];
            break;
        }
    }
    return function(c) {
        if (c) document.addEventListener(eventKey, c);
        return !document[stateKey];
    }
})();


var notIE = (document.documentMode === undefined),
    isChromium = window.chrome;

if (notIE && !isChromium) {
    // checks for Firefox and other  NON IE Chrome versions
    $(window).on("focusin", function () {
        setTimeout(function(){
			live = true;
			$.event.trigger('wakeUp');
        },300);
    }).on("focusout", function () {
        live = false;
    });
}
else {
    // checks for IE and Chromium versions
    if (window.addEventListener) {
        window.addEventListener("focus", function (event) {
            setTimeout(function(){
				live = true;
				$.event.trigger('wakeUp');
            },300);
        }, false);
        window.addEventListener("blur", function (event) {
            live = false;
        }, false);
    }
	else {
        window.attachEvent("focus", function (event) {
            setTimeout(function(){
                live = true;
				$.event.trigger('wakeUp');
            },300);
        });
        window.attachEvent("blur", function (event) {
            live = false;
        });
    }
};
