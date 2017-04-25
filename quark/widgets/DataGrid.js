(
	function(id)
	{
		this.htmlID = id;
		var htmlID = id;

		var ascStr, ascInt, ascBool, ascDbl ,ascByte, ascDate, ascTime;
		//var tbody, rows, rlen;
		this.filtered_rows = 0;
		this.arr_pos = new Array();
		var current_page, current, pages, limit;
		var assettypes = '';
		var ring = '';
		var date = '';
		var send = '';
		var base_url = '';
		var oldFilter = '';
		var newFilter = '';
		 
		//var page = '';
		current_page = 1;
		current = '';
		
		var current_event, current_parentId, current_onFilter, current_className, current_limit;
		
		var filter = $(this.htmlID + '_filter');
				
		this.filterGrid = function (event, parentID, onFilter, className, limit)
		{
			var input;
			
			current_event = event;
			current_parentId = parentID;
			current_onFilter = onFilter;
			this.current_className = className;
			current_limit = limit;
			
			input = event.target;
			newFilter = input.value.toLowerCase();
			filter.text = newFilter;
			filter.value = newFilter;
			
			
			if(newFilter.length != 1 && newFilter.length != 2)
			{
			    getJSform(parentID).callBack(onFilter, undefined, htmlID, input.id, newFilter);
			}
			
		};
		
		this.reDrawPagination = function (name, filtered_rows, limit, fixedRows, className)
		{   		   
			var end, filtered_pages,
				navigation_a_position, navigation_b_position,
				navigation_a_pages, navigation_b_pages;
			
			var table = $(this.htmlID + '_table');
			var tbody = table.tBodies[0];
			var rows = tbody.rows;
			var rlen = rows.length;
			
			this.current_className = className;
			
			navigation_a_position = document.getElementsByClassName(className + '_pageposition')[0];
			navigation_b_position = document.getElementsByClassName(className + '_pageposition')[1];
			
			navigation_a_pages = document.getElementsByClassName(className + '_pages')[0];
			navigation_b_pages = document.getElementsByClassName(className + '_pages')[1];
			
			navigation_a_position.innerHTML = '';
			navigation_b_position.innerHTML = '';
			
			navigation_a_pages.innerHTML = '';
			navigation_b_pages.innerHTML = '';
			
			if (newFilter != oldFilter) current_page = 1;
			oldFilter = newFilter;
			
			end = filtered_rows < limit ? filtered_rows : current_page * limit;
			filtered_pages = Math.ceil(filtered_rows / limit);
			
			if(current_page == 1) end += fixedRows;

			if(current_page == filtered_pages) end = filtered_rows;
			
			//if (current_page != filtered_pages) end = end + fixedRows;
			
			for(var i = fixedRows; i < filtered_rows; i++) rows[i].style.display = "none";				
			for(var i = (current_page - 1) * limit; i < end; i++) rows[i].style.display = "table-row";
			
			if(current_page == 1) end -= fixedRows;
			navigation_a_position.innerHTML += 'From ' + ((current_page - 1) * limit + 1) + ' to ' + end + ' of ' + filtered_rows;
			navigation_b_position.innerHTML += 'From ' + ((current_page - 1) * limit + 1) + ' to ' + end + ' of ' + filtered_rows;
			
			for(var page = 1; page <= filtered_pages; page++)
			{
				if(page == current_page) current = ' current';
				else current = '';
				
				navigation_a_pages.innerHTML += '<a class="' + className + '_page' + current + '" onclick="getJSform(\'%parent%\').' + name + '.navigate(' + limit + ', ' + fixedRows + ', ' + page + ', ' + filtered_pages + ', ' + filtered_rows + ');">'+ page +'</a>';
				navigation_b_pages.innerHTML += '<a class="' + className + '_page' + current + '" onclick="getJSform(\'%parent%\').' + name + '.navigate(' + limit + ', ' + fixedRows + ', ' + page + ', ' + filtered_pages + ', ' + filtered_rows + ');">'+ page +'</a>';
			}	
		}
		

		this.navigate = function (limit, fixedrows, page, pgs, filtered_rows)
		{
			var navigation_a_pages = document.getElementsByClassName(this.current_className + '_pages')[0];
			var navigation_b_pages = document.getElementsByClassName(this.current_className + '_pages')[1];
			
			var current_a_page_obj = navigation_a_pages.childNodes[page - 1];
			var current_b_page_obj = navigation_b_pages.childNodes[page - 1];

			var current = '';
			var i = 0;
			var obj = navigation_a_pages.firstChild;
			while (obj != null)
			{
				if (obj.nodeName == 'A')
				{
					i++;
					if (i == page) current = ' current'; else current = '';
					obj.className = this.current_className + '_page' + current;
				}
				
				obj = obj.nextElementSibling;
			}

			var current = '';
			var i = 0;
			var obj = navigation_b_pages.firstChild;
			while (obj != null)
			{
				if (obj.nodeName == 'A')
				{
					i++;
					if (i == page) current = ' current'; else current = '';
					obj.className = this.current_className + '_page' + current;
				}
				
				obj = obj.nextElementSibling;
			}
			
			//page = page;
			//alert(this.htmlID + '_table');
			//alert('name: ' + name + '\n lim: ' + lim + '\n page: ' + page + '\n pgs: ' + pgs + '\n filtered_rows: ' + filtered_rows);

			//name = name.slice(0, name.indexOf("-"));
			table = $(this.htmlID + '_table');
			tbody = table.tBodies[0];
			rows = tbody.rows;
			rlen = rows.length;
			
			//limit = lim;
			current_page = page;
			var end = 0;
			if(page == 1) 
			{
				end = page * limit + fixedrows;
				start = (page - 1) * limit + fixedrows;
			}
			else 
			{
				end = page * limit;
				start = (page - 1) * limit;
			}
				
			if(this.arr_pos.length > 0)
			{ 	
				if(page == pgs) end = this.arr_pos.length;				
				for(var i = fixedrows; i < rlen; i++) rows[i].style.display = "none";				
				for(var i = start; i < end; i++) rows[this.arr_pos[i]].style.display = "table-row";
				
				$(this.htmlID + '_pageposition').innerHTML = "From " + ((page - 1) * limit + 1) + " to " + end + " of " + this.arr_pos.length;
				//document.getElementsByClassName(name + "-per_page")[0].innerHTML = ((current_page - 1)*limit + 1) + " la " + end + " din " + arr_pos.length;
				//document.getElementsByClassName(name + "-per_page")[1].innerHTML = ((current_page - 1)*limit + 1) + " la " + end + " din " + arr_pos.length;
			}				
			else
			{
				if(page == pgs) end = rlen;				
				for(var i = fixedrows; i < rlen; i++) rows[i].style.display = "none";				
				for(var i = start; i < end; i++) rows[i].style.display = "table-row";
				//if (page != pgs) end = end - fixedrows;
				
				if(page == 1) end--;
				$obj = $(this.htmlID + '_pagination_filternav');
				$obj.children[0].innerHTML = "From " + ((page - 1) * limit + 1) + " to " + end + " of " + rlen;

				$obj = $(this.htmlID + '_pagination_bottomnav');
				$obj.children[0].innerHTML = "From " + ((page - 1) * limit + 1) + " to " + end + " of " + rlen;
				//document.getElementsByClassName(name +"-per_page")[0].innerHTML = ((current_page - 1)*limit + 1) + " la " + end + " din " + rlen;
				//document.getElementsByClassName(name + "-per_page")[1].innerHTML = ((current_page - 1)*limit + 1) + " la " + end + " din " + rlen;
			}
			
			for(i = 1; i <= pgs; i++)
			{
				//document.getElementsByName(name + "-page" + i)[0].className = "page" + i;
				//document.getElementsByName(name + "-page" + i)[1].className = "page" + i;
			}
		
			//document.getElementsByName(name + "-page" +page)[0].className = "page" + page + "-current";
			//document.getElementsByName(name + "-page" +page)[1].className = "page" + page + "-current";	
		}
		
		this.resetFilter = function (){
			filter.text = '';
			filter.value = '';
			newFilter = '';
			
			this.filterGrid(current_event, current_parentId, current_onFilter, this.current_className, current_limit);
		}

	}
)
