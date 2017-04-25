(
	function (id)
	{
		this.htmlID = id;
		var htmlID = id;
		this.jsform = getJSform("%parent%");
		var selfGrid = this;
		
		var current_cell = '';
		var current_cell_value = '';
		
		this.nextCell = function(id_cell)
		{
			var td_obj = $(id_cell);
			if (td_obj == null) return;
			
			return td_obj.nextSibling;			
		};
		
		this.nextRow = function(id_cell)
		{
			var td_obj = $(id_cell);
			if (td_obj == null) return null;			
			
			var tr_obj = td_obj.parentNode;
			if (tr_obj == null) return null;
			
			var idx = $idx(td_obj, tr_obj);

			var tr_next = tr_obj.parentNode.rows[tr_obj.rowIndex + 1];
			if (tr_next == null) return null;
			
			return tr_next.childNodes[idx];
		};
		
		this.enterCell = function(id_cell)
		{
			if (id_cell == current_cell) return;
			input_id = this.htmlID + '_input';
			
			//  put back current cell content
			if (current_cell != '')
			{
				//alert(input_id);
				value = $(input_id).value;

				//  put back the original content until validation
				$update(current_cell, current_cell_value);
				
				//  we should validate here
				this.jsform.callBack('%internalOnValidate%', undefined, this.htmlID, current_cell, value);
				$update(current_cell, value);
			}			
			
			//  save content of the current cell
			current_cell = id_cell;
			current_cell_value = $(id_cell).innerHTML;
			
			//  create the input element
			input = '<input id="' + input_id + '" type="text" style="width: 100%; height: 100%;" value="' + current_cell_value + '"></input>';
			$update(id_cell, input);
			$(input_id).addEventListener('keydown', this.keyHandler, false);
			$(input_id).focus();
		};
		
		this.keyHandler = function(e)
		{
			var VK_TAB = 9;
			var VK_RETURN = 13;
			var VK_LEFT = 37;
			var VK_UP = 38;
			var VK_RIGHT = 39;
			var VK_DOWN = 40;
			
			switch (e.keyCode)
			{
				case VK_TAB:
					var next_cell = selfGrid.nextCell(current_cell);
					if (next_cell != null) selfGrid.enterCell(next_cell.id);
					e.preventDefault();
					break;
				case VK_RETURN:
					var next_row = selfGrid.nextRow(current_cell);
					if (next_row != null) selfGrid.enterCell(next_row.id);
					e.preventDefault();
					break;
				/*case VK_LEFT:
					alert('left');
					e.preventDefault();
					break;
				case VK_UP:
					alert('up');
					e.preventDefault();
					break;
				case VK_RIGHT:
					alert('right');
					e.preventDefault();
					break;
				case VK_DOWN:
					alert('down');
					e.preventDefault();
					break;*/
			}
		};
		
	}
);