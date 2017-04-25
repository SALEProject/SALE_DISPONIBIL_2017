<?php

if (!defined('Q_DRAGNDROPUPLOADBOX_Q'))
{
	define('Q_DRAGNDROPUPLOADBOX_Q', true);
	
	if(isset($_FILES["file"]["type"]))
	{
		$validextensions = array("jpeg", "jpg", "png");
		$temporary = explode(".", $_FILES["file"]["name"]);
		$file_extension = end($temporary);
		//if ((($_FILES["file"]["type"] == "image/png") || ($_FILES["file"]["type"] == "image/jpg") || ($_FILES["file"]["type"] == "image/jpeg")
		//) && ($_FILES["file"]["size"] < 500000)//Approx. 500kb files can be uploaded.
		//&& in_array($file_extension, $validextensions)) {
		if ($_FILES["file"]["error"] > 0)
		{
			echo "Return Code: " . $_FILES["file"]["error"] . "<br/><br/>";
		}
		else
		{
			if (file_exists("../uploads/" . $_FILES["file"]["name"])) {
				echo $_FILES["file"]["name"] . " <span id='invalid'><b>already exists.</b></span> ";
			}
			else
			{
				if(file_exists($_FILES['file']['tmp_name']))
					$sourcePath = $_FILES['file']['tmp_name']; // Storing source path of the file in a variable
				
				if(file_exists("../uploads"))
					$targetPath = "../uploads/".$_FILES['file']['name']; // Target path where file is to be stored
				
				$b = move_uploaded_file($sourcePath,$targetPath) ; // Moving Uploaded file
			}
		}
	/*}
	else
	{
		echo "<span id='invalid'>***Invalid file Size or Type***<span>";
	}*/
	}
	
	
	class TDragnDropUploadBox extends TWidget
	{
		var $className = 'TDragnDropUploadBox';
		var $Left = 0;
		var $Top = 0;
		var $Width = 256;
		var $Height = 184;
		var $id, $class;
		
		function setProperty($name, $value)
		{
			switch (strtolower($name))
			{
				case 'top':
					$this->Top = $value;
					break;
				case 'left':
					$this->Left = $value;
					break;
				case 'width':
					$this->Width = $value;
					break;
				case 'height':
					$this->Height = $value;
					break;
				default:
					$this->__set($name, $value);
					break;
			}
		}
		
		function generateHTML()
		{
			$this->class = '%parent%.'.$this->Name;
			if ($this->Theme != '') $class = $this->Theme.'_'.$this->ClassName;
			
			$this->id = '%parent%.'.$this->Name;
			
			$style = $this->generateStyle();
			$style.='margin: 10px;'.
					'padding: 10px;'.
					'border: 1px solid #f0f0f0;'.
					'display:none;';
			
			$dir = 'uploads/';
			if(file_exists($dir)){
				$f_paths = glob($dir . "*.*");
			}
			
			$body = '';
			if(!empty($f_paths))
			{
				$i = 0;
				$files = [];
				foreach($f_paths as $path)
				{
					if($i % 4 == 0) $body .= '<tr>';
					$body .= '<td style="width: 116px; padding: 8px;">';
					$body .= '<img src="'.$path.'" style="max-width: 100px; margin-bottom: 8px; float: left;"></img>';
					$body .= '<span style="position: relative; top: 0px; color: rgb(255, 255, 255); font-size: 13px;">'.substr($path, 8).'</span>';
					$body .= '</td>';
					$i++;
					if($i % 4 == 0) $body .= '</tr>';
				}
			}
			//else $body = "<tr></tr>";
			
			$html  = '<div id="status" style="display:none; position: absolute;top: 14px;left: -305px;" >Drag the files from a folder to the selected area ...</div>';
			$html .= '<div id="" class="'.$this->class.'" ondrop="dropFile(event)" style="'.$style.'">Drop files here.</div>'."\n";
			$html .= '<div id="list" style="display:none; position: absolute;top: 60px;left: -305px;"></div><div id="count"></div>';
			$html .= '<table id="table" style="float: right; position: absolute; top: 20px; left: 360px;"><tbody>'.$body.'</tbody></table>';
			
			return $html;
		}
		
		function generateJS()
		{
			$js = 	'	    var status = document.getElementById("status");'."\n".
		    		'		var drop = new Array();'."\n".  
		    		'		drop = document.getElementsByClassName("'.$this->class.'");'."\n".
		    		'		var list   = document.getElementById("list");'."\n".
		    		'		var count  = document.getElementById("count"); //count.innerHTML = 0;'."\n".
		    		'	    var tbody  = document.getElementById("table").tBodies[0];'."\n".
		    		'		var tr = ""; var tds_previous = 0;'."\n".
		    		'		var tds_initial = tbody.getElementsByTagName("td").length;'."\n".
		    		 
		    		
		    		'if(window.FileReader){'."\n".
		    		'	for(var i = 0; i < drop.length; i++){'."\n".
		    		'   	drop[i].addEventListener("drop", function (e) {'."\n".
		    		'			if(!e.dataTransfer.getData("text/html")){'."\n".
		    		'				status.style.display = "block";'."\n". 
		    		'				list.style.display = "block";'.
		    		'				if(tds_initial == 0) tds_previous = tbody.getElementsByTagName("td").length;'."\n".
		    		'				else tds_previous = tbody.getElementsByTagName("td").length - tds_initial;'."\n".
		    		' 			}'."\n".
		    	  	
		    	  	'			e = e || window.event; '."\n".   
		    	  	'			if (e.preventDefault) { e.preventDefault(); }'."\n".
		    	  	
		    	  	'			var dt    = e.dataTransfer;'."\n".
		    	  	'			var files = dt.files;'."\n".
		    	  	'			var flen = files.length;'."\n".
		    	  	'			var arr = new Array(); var formData = new FormData();'."\n".
		    	  	 
		    	  	'    		for (var i=0; i<files.length; i++) {'."\n".
		    	    '				var file = files[i];'."\n".
		    	    '				var bin = ""; '."\n".	    
		    	    '				var reader = new FileReader(); '."\n".
		    	    
		    	    '				reader.addEventListener("loadend", function(e, file) {'."\n".
		    	    '				    bin           = this.result; '."\n".
		    	    '					var newFile       = document.createElement("div");'."\n".
		    	    '					newFile.innerHTML = "Uploaded : "+file.name+" size "+file.size+" B";'."\n".
		    	    '	   				list.appendChild(newFile);'."\n".	    	    
	
		    	    '					var fileNumber = list.getElementsByTagName("div").length;'."\n".
		    	    '				    var inv = new Array(); for(var i = 1; i <= flen + tds_previous; i++) inv[i] = flen + tds_previous - i + 1;'."\n".
		    	    
		    	    '					status.innerHTML = fileNumber < files.length'."\n".
		    	    '                   ? "Loaded 100% of file "+fileNumber+" of "+files.length+"..."'."\n". 
		    	    '                   : "Done uploading. processed "+fileNumber+" files.";'."\n".
		    	    
		    	    '					var td = document.createElement("td");'."\n".
					'					var span = document.createElement("span");'."\n".	    	    
					'					var img = document.createElement("img");'."\n".
					'					tr.style = ""'."\n".
					'					td.style = "width: 116px;padding: 8px;"'."\n".
					'					img.style = "max-width: 100px; margin-bottom: 8px; float:left;"'."\n".
	 				'					span.style = "color: #fff;"; span.innerHTML = file.name;'."\n".		    	        
	 				
			    	'					if(file.name.toLowerCase().match(/\.(jpg|jpeg|png|gif)$/)){'."\n".
			    	'				   		img.src = bin;'."\n".
			    	'						img.file = file;'."\n".
					'					}else'."\n".
			    	'						img.src = "images/code.png";'."\n".
			    	
			    	'					var rows = null; var last_tr = null; '."\n".
			    	 
			    	'					//if(rows.length == 0) { alert("first row"); tr = document.createElement("tr"); tbody.appendChild(tr); }'."\n".
			    	'					var total = tds_initial + tds_previous + inv[fileNumber] - 1; //alert("inv["+fileNumber+"]:" + inv[fileNumber]  + " total:" + total);'."\n".
			    	
			    	'					if((total)%4 == 0) {'."\n".
			    	'						new_tr = document.createElement("tr");'."\n".
			    	'    					tbody.appendChild(new_tr);'."\n".
			    	'				    	tbody.appendChild(new_tr).appendChild(td).appendChild(img);'."\n".
			    	'						tbody.appendChild(new_tr).appendChild(td).appendChild(span); //alert("new row"); '."\n".
			    	' 					}'."\n".
			    	'					else{'."\n".
			    	'				    	rows = tbody.getElementsByTagName("tr"); '."\n".
			    	'						last_tr = rows[rows.length - 1];'."\n".
			    	'    					tbody.appendChild(last_tr);'."\n".
		    	    '				    	tbody.appendChild(last_tr).appendChild(td).appendChild(img);'."\n".
		    	    '						tbody.appendChild(last_tr).appendChild(td).appendChild(span); /*alert("old row");*/ }'."\n".
		    	    '					//count.innerHTML = tds_previous + inv[fileNumber];'."\n".
		    	    '				   // alert("initial:" + tds_initial + " previous:" + tds_previous + " flen:" + flen + " total:" + total + " inv["+fileNumber+"]:" + inv[fileNumber] ); '."\n".
		    	   
		    	    '				}.bindToEventHandler(file));'."\n".
		    	    				
		    	    '				reader.readAsDataURL(file);'."\n".
		    	    
		    	    '			var xhr = new XMLHttpRequest();'."\n".
		    	    '			xhr.onload = function () {'."\n".
		    	    '				if (xhr.status === 200) {'."\n".
		    	    '					console.log("all done: " + xhr.status);'."\n".
		    	    '				} else {'."\n".
		    	    '					//console.log("Something went terribly wrong...");'."\n".
		    	    '				}'."\n".
		    	    '			}; '."\n".
		    	    '			xhr.open("POST", "widgets/DragnDropUploadBox.php");'."\n".
		    	    '			xhr.setRequestHeader("X-Requested-With","XMLHttpRequest");'."\n".
		    	     
		    	    '			var f=new FormData(); f.append("file", file); xhr.send(f);'."\n".
		    	    
		    	    '				 //formData.append("upload", file); //alert("appended");'."\n".
		    	    '			arr.push(reader.result);'."\n".
		    	    
		    	  	'			}'."\n".
		    	  	'		    // do { callBack("SaveImgs", "frm_Products","","pictures", arr); } while (arr.length < 1); '."\n".
		    	  	'		   //callBack("SaveImgs", "frm_Products","","pictures", JSON.stringify(formData)); '."\n".
		    	  	'			return false;'."\n".
		    		'		});'."\n".
		    		
				    'Function.prototype.bindToEventHandler = function bindToEventHandler() {'."\n".
		    	  	'		var handler = this;'."\n".
		    	  	'		var boundParameters = Array.prototype.slice.call(arguments);'."\n".
		    	  	'		return function(e) {'."\n".
		    	    '			e = e || window.event;'."\n".  
		    	    ' 			boundParameters.unshift(e);'."\n".
		    	    '			handler.apply(this, boundParameters);'."\n".
		    	  	'		};'."\n".
		    		'	};'."\n".
		    		
		    		'	cancel = function(e) {'."\n".
		    		'		if (e.preventDefault) { e.preventDefault(); }'."\n".
		      		'		return false;'."\n".
		    		'	}'."\n".
		    		
		    		'	drop[i].addEventListener("dragover", cancel);'."\n".
		    		'	drop[i].addEventListener("dragenter", cancel);'."\n".
					'} } else {'."\n".
		  			'	document.getElementById("status").innerHTML = "Your browser does not support the HTML5 FileReader.";'."\n".
					'}'."\n".
	
					'addEventHandler = function(obj, evt, handler) {'."\n".
	    			'	if(obj.addEventListener) {'."\n".
	        		'		obj.addEventListener(evt, handler, false);'."\n".
	    			'	} else if(obj.attachEvent) {'."\n".
	        	   	' 		obj.attachEvent("on"+evt, handler);'."\n".
	    			'	} else {'."\n".
	          		' 		obj["on"+evt] = handler;'."\n".
	    			'	}'."\n".
					'}'."\n";
			return $js;
		}
	}
	
	//registerWidget('TDragnDropUploadBox', 'TDragnDropUploadBox');

}

?>