if (!Array.prototype.indexOf) 
{
	Array.prototype.indexOf = function(obj, start) 
	{
	     for (var i = (start || 0), j = this.length; i < j; i++) 
	     {
	         if (this[i] === obj) { return i; }
	     }
	     
	     return -1;
	};
}

function $(id)
{
	if (id.trim().startsWith('[')) return document.querySelectorAll(id);
	else return document.getElementById(id);
}

function $idx(element, parent)
{
	return Array.prototype.indexOf.call(parent.childNodes, element);
	/*for (var i = 0; i < parent.childNodes.length; i++)
		if (parent.childNodes[i] == element) return i;
	
	return -1;*/
}

function $addClass(id, classAttribute)
{
	var cls = ' ' + classAttribute;
	var el = $(id);
	if (el == null) return;
	el.className = el.className.replace(cls, "");
	el.className = el.className + cls;
}

function $removeClass(id, classAttribute)
{
	var cls = ' ' + classAttribute;
	var el = $(id);
	if (el == null) return;
	el.className = el.className.replace(cls, "");
}

function $setStyle(id, content)
{
	var obj = $(id);
	
	if (obj != null)
	{
		var cssattributes = content.split(';');
		for (var j = 0; j < cssattributes.length; j++)
		{
			var attribute = cssattributes[j].split(':');
			if (attribute.length == 2)
			{
				var name = attribute[0];
				var value = attribute[1];
				
				s = "obj.style." + name + " = '" + value.trim() + "'; ";
				//alert(s);
				eval(s);
			}
		}
	}
}

function $append(id, content)
{
	var obj = null;
	
	if (id == '') obj = document.body; else obj = $(id);
	if (obj == null) return false;
	
	obj.innerHTML = obj.innerHTML + content;
	return true;
}

function $delete(id)
{
	var obj = $(id);
	if (obj != null)
	{
		obj.parentNode.removeChild(obj);
		return true;
	}
	
	return false;
}

function $update(id, content)
{
	var obj = $(id);
	if (obj != null)
	{
		obj.innerHTML = content;
	}
}

function $replace(id, content)
{
	var obj = $(id); //any element to be fully replaced
	
	if(obj.outerHTML) //if outerHTML is supported
	{ 
		obj.outerHTML = content; ///it's simple replacement of whole element with contents of str var
	}
	else
	{ 
		//  if outerHTML is not supported, there is a weird but crossbrowsered trick									
		var tmp = document.createElement('div');
		tmp.innerHTML = '<!--THIS DATA SHOULD BE REPLACED-->';
	
		parent = obj.parentNode; //Okey, element should be parented
		parent.replaceChild(tmp, obj); //here we placing our temporary data instead of our target, so we can find it then and replace it into whatever we want to replace to
		parent.innerHTML = parent.innerHTML.replace('<div><!--THIS DATA SHOULD BE REPLACED--></div>', content);
	}
}

function $setStyleSheet(id, content)
{
	var style = $(id);
	if (style == null)
	{
		document.createElement('style');
		style.id = id;
		style.type = 'text/css';

		document.head.appendChild(style);
	}
	
	style.innerHTML = content;
}

function JSclientScreen()
{
	this.getScreenOrientation = function()
	{
		if (window.orientation == null) return "landscape";
		return Math.abs(window.orientation) - 90 == 0 ? "landscape" : "portrait";
	};
	
	this.getScreenWidth = function()
	{
		return this.getScreenOrientation() == "landscape" ? screen.width : screen.height;
	};

	this.getScreenHeight = function()
	{
		return this.getScreenOrientation() == "landscape" ? screen.height: screen.width;
	};

	this.getAvailWidth = function()
	{
		return this.getScreenOrientation() == "landscape" ? screen.availWidth : screen.availHeight;
	};

	this.getAvailHeight = function()
	{
		return this.getScreenOrientation() == "landscape" ? screen.availHeight : screen.availWidth;
	};

	this.detectTouch = function()
	{
		if (('ontouchstart' in window) || (navigator.maxTouchPoints > 0) || (navigator.msMaxTouchPoints > 0)) return true;
		else return false;
	};

	this.Orientation = this.getScreenOrientation();
	this.Width = this.getScreenWidth();
	this.Height = this.getScreenHeight();
	this.AvailWidth = this.getAvailWidth();
	this.AvailHeight = this.getAvailHeight();
	this.TouchCapable = this.detectTouch();	
}

var clientScreen = new JSclientScreen();
callBack('setClientScreen', '', undefined, undefined, undefined, clientScreen);

/* repeatString() returns a string which has been repeated a set number of times */ 
function repeatString(str, num) {
    out = '';
    for (var i = 0; i < num; i++) {
        out += str; 
    }
    return out;
}

/*
dump() displays the contents of a variable like var_dump() does in PHP. dump() is
better than typeof, because it can distinguish between array, null and object.  
Parameters:
  v:              The variable
  howDisplay:     "none", "body", "alert" (default)
  recursionLevel: Number of times the function has recursed when entering nested
                  objects or arrays. Each level of recursion adds extra space to the 
                  output to indicate level. Set to 0 by default.
Return Value:
  A string of the variable's contents 
Limitations:
  Can't pass an undefined variable to dump(). 
  dump() can't distinguish between int and float.
  dump() can't tell the original variable type of a member variable of an object.
  These limitations can't be fixed because these are *features* of JS. However, dump()
*/
var max_recursionLevel = 2;
function dump(v, howDisplay, recursionLevel) 
{
    howDisplay = (typeof howDisplay === 'undefined') ? "alert" : howDisplay;
    recursionLevel = (typeof recursionLevel !== 'number') ? 0 : recursionLevel;

    if (recursionLevel > max_recursionLevel) return "";

    var vType = typeof v;
    var out = vType;

    switch (vType) {
        case "number":
            /* there is absolutely no way in JS to distinguish 2 from 2.0
            so 'number' is the best that you can do. The following doesn't work:
            var er = /^[0-9]+$/;
            if (!isNaN(v) && v % 1 === 0 && er.test(3.0))
                out = 'int';*/
        case "boolean":
            out += ": " + v;
            break;
        case "string":
            out += "(" + v.length + '): "' + v + '"';
            break;
        case "object":
            //check if null
            if (v === null) {
                out = "null";

            }
            //If using jQuery: if ($.isArray(v))
            //If using IE: if (isArray(v))
            //this should work for all browsers according to the ECMAScript standard:
            else if (Object.prototype.toString.call(v) === '[object Array]') {  
                out = 'array(' + v.length + '): {\n';
                for (var i = 0; i < v.length; i++) 
                {
                	var s = "";
                	try
                	{
                		s = dump(v[i], "none", recursionLevel + 1);
                	}
                	catch (exc)
                	{
                		s = exc.message;
                	}
                	
                    out += repeatString('   ', recursionLevel) + "   [" + i + "]:  " + s + "\n";
                }
                out += repeatString('   ', recursionLevel) + "}";
            }
            else { //if object    
                sContents = "{\n";
                cnt = 0;
                for (var member in v) 
                {
                    //No way to know the original data type of member, since JS
                    //always converts it to a string and no other way to parse objects.
                	var s = "";
                	try
                	{
                		s = dump(v[member], "none", recursionLevel + 1);
                	}
                	catch (exc)
                	{
                		s = exc.message;
                	}
                	
                    sContents += repeatString('   ', recursionLevel) + "   " + member + ":  " + s + "\n";
                    cnt++;
                }
                
                sContents += repeatString('   ', recursionLevel) + "}";
                out += "(" + cnt + "): " + sContents;
            }
            break;
    }

    return out;
}

var debugJS = false;
var forms = [];
var _catchMouse = false;
var _catchFormID = '';
var _catchMouseMoveCall = '';
var _catchMouseUpCall = '';

function registerMouseCatch(formID, mouseMoveCall, mouseUpCall)
{
	_catchMouse = true;
	_catchFormID = formID;
	_catchMouseMoveCall = mouseMoveCall;
	_catchMouseUpCall = mouseUpCall;
}

function clearMouseCatch()
{
	_catchMouse = false;
	_catchFormID = '';
	_catchMouseMoveCall = '';
	_catchMouseUpCall = '';
}

if (clientScreen.TouchCapable)
{
	document.ontouchmove = function(event)
	{
		if (_catchMouse)
		{
			event.preventDefault();
			var frm = getJSform(_catchFormID);
			if (frm != null)
			{
				var s = 'getJSform(_catchFormID).' + _catchMouseMoveCall + ';';
				eval(s);
			}
		}
	};

	document.ontouchend = function(event)
	{
		if (_catchMouse)
		{
			//event.preventDefault();
			var frm = getJSform(_catchFormID);
			if (frm != null)
			{
				var s = 'getJSform(_catchFormID).' + _catchMouseUpCall + ';';
				eval(s);
			}
		}
	};
}
else
{
	document.onmousemove = function(event)
	{
		if (_catchMouse)
		{
			var frm = getJSform(_catchFormID);
			if (frm != null)
			{
				var s = 'getJSform(_catchFormID).' + _catchMouseMoveCall + ';';
				eval(s);
			}
		}
	};

	document.onmouseup = function(event)
	{
		if (_catchMouse/*_moveForm || _e_resizeForm || _se_resizeForm || _s_resizeForm*/)
		{
			var frm = getJSform(_catchFormID);
			if (frm != null)
			{
				var s = 'getJSform(_catchFormID).' + _catchMouseUpCall + ';';
				eval(s);
			}
		}
	};

}

function JSform(id)
{
	this.htmlID = id;
	this._mousedown = false;
	this._mouseX = 0;
	this._mouseY = 0;
	this.Left = 0;
	this.Top = 0;
	this.Width = 0;
	this.Height = 0;
	this._x = 0;
	this._y = 0;
	this._width = 0;
	this.height = 0;
	
	this.focus = function()
	{
		var obj = $(this.htmlID);
		document.body.appendChild(obj);
	};
	
	this.mouseDown = function(event)
	{
		if (clientScreen.TouchCapable) event.preventDefault();
		
		this._mousedown = true;

		var clientX = 0;
		var clientY = 0;
		if (clientScreen.TouchCapable)
		{
			clientX = event.touches[0].clientX;
			clientY = event.touches[0].clientY;
		}
		else
		{
			clientX = event.clientX;
			clientY = event.clientY;
		}
		
		this._mouseX = clientX;
		this._mouseY = clientY;
		
		registerMouseCatch(this.htmlID, 'mouseMove(event)', 'mouseUp(event)');
		
		var obj = $(this.htmlID + "_wrapper");
		if (obj != null)
		{
			$addClass(this.htmlID + '_wrapper', 'morph');
			this.Left = obj.offsetLeft;//obj.style.left;
			this.Top = obj.offsetTop;//obj.style.top;
		}
		
		if (debugJS) writeDebug();
	};
	
	this.mouseMove = function(event)
	{
		if (this._mousedown) 
		{
			if (clientScreen.TouchCapable) event.preventDefault();

			this.focus();

			var clientX = 0;
			var clientY = 0;
			if (clientScreen.TouchCapable)
			{
				clientX = event.touches[0].clientX;
				clientY = event.touches[0].clientY;
			}
			else
			{
				clientX = event.clientX;
				clientY = event.clientY;
			}
						
			this._x = this.Left + clientX - this._mouseX;
			this._y = this.Top + clientY - this._mouseY;
			
			var obj = $(this.htmlID + "_wrapper");
			if (obj != null)
			{
				obj.style.left = this._x + "px";
				obj.style.top = this._y + "px";
			}
		}
		
		if (debugJS) writeDebug();
	};
	
	this.mouseExit = function(event)
	{
		if (this._mousedown) 
		{
			//if (clientScreen.TouchCapable) event.preventDefault();

			var clientX = 0;
			var clientY = 0;
			if (clientScreen.TouchCapable)
			{
				clientX = event.touches[0].clientX;
				clientY = event.touches[0].clientY;
			}
			else
			{
				clientX = event.clientX;
				clientY = event.clientY;
			}
			
			this._x = this.Left + clientX - this._mouseX;
			this._y = this.Top + clientY - this._mouseY;
			
			var obj = $(this.htmlID + "_wrapper");
			if (obj != null)
			{
				obj.style.left = this._x + "px";
				obj.style.top = this._y + "px";
			}
		}
		
		if (debugJS) writeDebug();
	};
	
	this.mouseUp = function(event)
	{
		if (this._mousedown)
		{
			//if (clientScreen.TouchCapable) event.preventDefault();

			var obj = $(this.htmlID + "_wrapper");
			if (obj != null)
			{
				$removeClass(this.htmlID + '_wrapper', 'morph');

				obj.style.left = this._x + "px";
				obj.style.top = this._y + "px";

				this.Left = this._x;
				this.Top = this._y;
				
				callBack('setProperty', this.htmlID, undefined, 'Left', this._x);
				callBack('setProperty', this.htmlID, undefined, 'Top', this._y);
			}

			this._mousedown = false;
			clearMouseCatch();
		}
		
		if (debugJS) writeDebug();
	};
	
	//  form resize functions
	this._resizeForm = false;
	this.resize_mouseDown = function(event, direction)
	{
		if (clientScreen.TouchCapable) event.preventDefault();
		
		this._resizeForm = true;
		if (clientScreen.TouchCapable)
		{
			this._mouseX = event.touches[0].clientX;
			this._mouseY = event.touches[0].clientY;
		}
		else
		{
			this._mouseX = event.clientX;
			this._mouseY = event.clientY;
		}
		
		registerMouseCatch(this.htmlID, 'resize_mouseMove(event, "' + direction + '")', 'resize_mouseUp(event, "' + direction + '")');
		var obj = $(this.htmlID + "_wrapper");
		if (obj != null) 
		{
			$addClass(this.htmlID + '_wrapper', 'morph');
			this.Width = obj.offsetWidth;
			this.Height = obj.offsetHeight;
		}
		
		if (debugJS) writeDebug();				
	};
	
	this.resize_mouseMove = function(event, direction)
	{
		if (this._resizeForm) 
		{
			if (clientScreen.TouchCapable) event.preventDefault();
			this.focus();
			var clientX = 0; 
			var clientY = 0;
			if (clientScreen.TouchCapable)
			{
				clientX = event.touches[0].clientX; 
				clientY = event.touches[0].clientY;
			}
			else
			{
				clientX = event.clientX; 
				clientY = event.clientY;
			}
						
			var obj = $(this.htmlID + "_wrapper");
			if (obj != null)
			{
				switch(direction)
				{
					case 'e':
						this._width = this.Width + clientX - this._mouseX;
						obj.style.width = this._width + "px";
						break;
					case 'se':
						this._width = this.Width + clientX - this._mouseX;
						this._height = this.Height + clientY - this._mouseY;
						obj.style.width = this._width + "px";
						obj.style.height = this._height + "px";
						break;
					case 's':
						this._height = this.Height + clientY - this._mouseY;
						obj.style.height = this._height + "px";
						break;
				}			
			}
		}
		
		if (debugJS) writeDebug();
	};
	
	this.resize_mouseUp = function(event, direction)
	{
		if (this._resizeForm)
		{			
			var obj = $(this.htmlID + "_wrapper");
			if (obj != null)
			{
				$removeClass(this.htmlID + '_wrapper', 'morph');

				switch (direction)
				{
					case 'e':
						obj.style.width = this._width + "px";
						this.Width = this._width;
						callBack('setProperty', this.htmlID, undefined, 'Width', this._width);
						break;
					case 'se':
						obj.style.width = this._width + "px";
						obj.style.height = this._height + "px";
						this.Width = this._width;
						this.Height = this._height;
						callBack('setProperty', this.htmlID, undefined, 'Width', this._width);
						callBack('setProperty', this.htmlID, undefined, 'Width', this._width);
						break;
					case 's':
						obj.style.height = this._height + "px";
						this.Height = this._height;
						callBack('setProperty', this.htmlID, undefined, 'Width', this._width);
						break;
				}				
			}

			clearMouseCatch();
			this._resizeForm = false;
		}
		
		if (debugJS) writeDebug();
	};	
}

function indexOfForm(id)
{
	var b = false;
	var idx = -1;
	while (!b && idx < forms.length - 1)
	{
		idx++;
		if (forms[idx].htmlID == id) b = true;
	}
	
	if (b) return idx; else return -1;
}

function getJSform(id)
{
	var idx = indexOfForm(id);
	if (idx > -1) return forms[idx].frm; else return null;
}

function addFormID(id, callRenderer = false)
{
	//alert(id);
	var v = {htmlID: id, frm: new JSform(id)};
	forms.push(v);
	//callBack('renderHTML', id);
	//callBack('loadJS', id);
	
	if (callRenderer == true) callBack('render', id);
	if (debugJS) writeDebug();
}

function removeFormID(id)
{
	var idx = indexOfForm(id);
	if (idx > -1) forms.splice(idx, 1);
	if (debugJS) writeDebug();
}

function writeDebug()
{
	var pre = $("pre");
	if (pre == null)
	{
		pre = document.createElement("pre");
		pre.id = 'pre';

		var pre_div = $("frm_DebugForm_pre_div");
		if (pre_div == null)
			document.body.appendChild(pre);
		else 
			pre_div.appendChild(pre);
	}

	pre.innerHTML = "";

	var ss = "clientScreen: " + dump(clientScreen) + "\n\n";
	pre.innerHTML = pre.innerHTML + ss;

	/*
	ss = "window.orientation: " + dump(window.orientation) + "\n\n";	
	pre.innerHTML = pre.innerHTML + ss;
	
	ss = "window.screen: " + dump(window.screen) + "\n\n";	
	pre.innerHTML = pre.innerHTML + ss;
	*/
	
	for (var i = 0; i < forms.length; i++)
		if (forms[i].htmlID != "frm_DebugForm")
		{
			var s = 'JS' + forms[i].htmlID + ': ' + dump(forms[i].frm, 'alert') + "\n\n";
			
			//alert(s);
			pre.innerHTML = pre.innerHTML + s;		
		}
}

function beginCallbackDebug()
{
}

function endCallbackDebug()
{
}

function systemTimerDebug()
{
	return true;
}

function writeCallStackDebug(callstack)
{
	var div = $("frm_DebugForm.pg_callstack");
	if (div != null) div.innerHTML = callstack;
}

function serializeForm(id)
{
	var form = $(id); 
	if (form == null) return '';
	
	var elements = form.elements;
	var str = '';
	var serialized = [];
	
	for (var i = 0; i < elements.length; i++)
	{
		var element = elements[i];
		var type = element.type;
		var name = element.name;
		var value = element.value;
		
		switch (type)
		{
			case 'text':
			case 'password':
			case 'textarea':
			case 'select-one':    
				str = name + '=' + value;    
				serialized.push(str);    
				break;
 			case 'checkbox':
			case 'radio':
 				str = name + '=' + element.checked;
 				serialized.push(str);
 				break;
			default:        
				break;
		}
	}
	
	str = serialized.join('&');
	return str;
}

function createXmlHttpRequest()
{ 
	var result = null;
	
	if (window.XMLHttpRequest)
	{
		result = new XMLHttpRequest();
	}
	else if (window.ActiveXObject)
	{
		result = new ActiveXObject("Microsoft.XMLHTTP");
	}
	
	return result;
}


function noop() {}

function callBack(event, formid, senderid, varName, varValue, varObject)
{ 
	beginCallbackDebug();
	
	//alert("event: " + event + "; formid: " + formid + "; senderid: " + senderid);
    formid = (typeof formid === 'undefined') ? this.htmlID : formid;
    
	var xmlHttp = createXmlHttpRequest();
	if (xmlHttp == null)
	{
		alert ("Your browser does not support Ajax");
		return false;
	}

    var url = "index.php?callBack=true&form=" + formid + "&event=" + event;
	if (!(typeof senderid === 'undefined')) url = url + "&sender=" + senderid;
	if (!(typeof varName === 'undefined') && !(typeof varValue === 'undefined'))
		url = url + '&varName=' + varName + '&&varValue=' + varValue;
	
	xmlHttp.onreadystatechange = stateChanged;
	xmlHttp.onerror = stateError;
	xmlHttp.open("POST", url, true);

	var data = undefined;
	if (!(typeof varObject === 'undefined')) 
	{
		data = JSON.stringify(varObject);
		xmlHttp.setRequestHeader("Content-type", "application/json");
	}
	else 
	{
		if (formid != "") data = serializeForm(formid);
		xmlHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	}
	//alert(data);

	//xmlHttp.setRequestHeader("Content-length", data.length);
	//xmlHttp.setRequestHeader("Connection", "close");
    xmlHttp.send(data);	 
	    
    function stateError()
    {
    	callBackWait = false;
    }

	function stateChanged() 
	{ 
		if (xmlHttp.readyState == 4 || xmlHttp.readyState == "complete")
		{
			if (xmlHttp.responseText != "")
			{
				//alert(xmlHttp.responseText);
				var records = eval(xmlHttp.responseText);
				
				for (var i = 0; i < records.length; i++)
				{
					var rec = records[i];
					var str_content = B64.decode(rec.content);
					switch (rec.action)
					{
						case 'append':
							$append(rec.target, str_content);
							if (debugJS) writeDebug();
							break;
						case 'delete':
							//alert('delete: ' + rec.target);
							if ($delete(rec.target) && debugJS) writeDebug();
							break;
						case 'update':
							$update(rec.target, str_content);
							break;
						case 'replace':
							$replace(rec.target, str_content);
							break;
						case 'addClass':
							$addClass(rec.target, str_content);
							break;
						case 'removeClass':
							$removeClass(rec.target, str_content);
							break;
						case 'setStyle':
							//alert(rec);
							$setStyle(rec.target, str_content);
							break;
						case 'setStyleSheet':					
							$setStyleSheet(rec.target, str_content);
							break;
						case 'script':		
							//alert(str_content);
							var retryCount = 0;
							var script_success = false;
							var error = '';
							while (!script_success && retryCount < 5)
							{
								try
								{
									eval(str_content);
									script_success = true;
								}
								catch (exc)
								{
									retryCount++;
									error = exc;
								}
							}
							
							if (!script_success) alert('A script failed ' + retryCount + ' times. Please reload page. \n Error "' + error + '" encountered in: \n ' + str_content);
							
							if (debugJS) writeDebug();
							break;
						case 'download':
							//alert(str_content);
							window.location = str_content;
							break;
						case 'alert':
							alert(str_content);
							break;
						case 'debugStack':
							writeCallStackDebug(str_content);
							break;
						case 'reset':
							alert('The system or an application triggered a reset event. Press Ok to reload and resume operation.');
							location.reload(true);
							break;
						default:
							break;
					}
				}
			}
		} 		
	} 
	
}

//  system timer
var first_tick = true;
(function()
{
	if (!first_tick && systemTimerDebug()) callBack('systemTimer', '');
	setTimeout(arguments.callee, 1000);
	first_tick = false;
}());


