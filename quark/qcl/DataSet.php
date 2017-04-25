<?php

	class TDataSet extends TComponent
	{
		var $FieldDefs = Array();
		private $FRows = Array();
		
		private $FCurrentRow;
		private $FRowsCount = 0;
		
		private $FFilter = '';
		private $FSort = '';
		private $FIndex = Array();
		
		function __construct($AParent)
		{
			parent::__construct($AParent);
			$this->FieldDefs = Array();
			$this->FRows = Array();
			$this->FCurrentRow = 0;
			$this->FRowsCount = 0;
		}
		
		function get_Filter()
		{
			return $this->FFilter;
		}
		
		function set_Filter($value)
		{
			if ($this->FFilter != $value)
			{
				$this->FFilter = $value;
				$this->doFilter();
			} 
		}
		
		function get_Sort()
		{
			return $this->FSort;
		}
		
		function set_Sort($value)
		{
			if ($this->FSort != $value)
			{
				$this->FSort = $value;
				$this->doSort();
			}
		}
		
		function get_RowsCount()
		{
			return count($this->FIndex);
		}
		
		function get_Rows()
		{
			$rows = Array();
			foreach ($this->FIndex as $i) $rows[] = $this->FRows[$i];
			
			return $rows;
		}
		
		function doFilter()
		{
			if (trim($this->FFilter) == '') return;
			
			$filter_array = preg_split('/[\s,]+/', $this->FFilter);
			if (count($filter_array) < 3) return;
			
			$this->FIndex = Array();
			
			$operand1 = $filter_array[0];
			$operand2 = $filter_array[2];
			$operator = $filter_array[1];
			
			$idx_field1 = array_search($operand1, $this->FieldDefs);
			$idx_field2 = array_search($operand2, $this->FieldDefs);
			
			for ($i = 0; $i < count($this->FRows); $i++)
			{
				$value1 = $operand1;
				if ($idx_field1 != false) $value1 = $this->FRows[$i][$operand1];
				
				$value2 = $operand2;
				if ($idx_field2 != false) $value2 = $this->FRows[$i][$operand2];
							
				switch ($operator)
				{
					case '=':
						if ($value1 == $value2) $this->FIndex[] = $i;
						break;
					case '<>':
					case '!=':
						if ($value1 != $value2) $this->FIndex[] = $i;
						break;
					case '>':
						if ($value1 > $value2) $this->FIndex[] = $i;
						break;
					case '<':
						if ($value1 < $value2) $this->FIndex[] = $i;
						break;
				}
			}
			
			$this->doSort();
		}
		
		function doSort()
		{
			
		}
		
		function first()
		{
			$this->CurrentRow = 0;
		}
		
		function next()
		{
			if ($this->CurrentRow < $this->RowsCount - 1) $this->CurrentRow++;
		}
		
		function prev()
		{
			if ($this->CurrentRow > 0) $this->CurrentRow--;
		}
		
		function last()
		{
			if ($this-RowsCount > 0) $this->CurrentRow = $this->RowsCount - 1;
			else $this->CurrentRow = 0;
		}
		
		function addRow($row)
		{
			$this->FRows[] = $row;
			$this->FRowsCount++;				
			
			if ($this->FFilter == '') $this->FIndex[] = $this->FRowsCount - 1;
			else $this->doFilter();
		}
		
		function editRow($rowIndex, $row)
		{
			if ($rowIndex >= 0 && $rowIndex < count($this->FRows))
				$this->FRows[$rowIndex] = $row;
		}
		
		function deleteRow($rowIndex)
		{
			if ($rowIndex >= 0 && $rowIndex < count($this->FRows))
			{
				unset($this->FRows[$rowIndex]);
				$this->FRows = array_values($this->FRows);
			}
		}
		
		function loadXML($url)
		{
			if (!file_exists($url)) return;
			
			$this->FRowsCount = 0;
			$this->FRows = Array();
			$this->FIndex = Array();
			
			$xml = simplexml_load_file($url);
			foreach($xml->children() as $xml_node)
			{
				switch (trim(strtolower($xml_node->getName())))
				{
					case 'fielddefs':
						foreach ($xml_node->children() as $node_def)
						{
							$this->FieldDefs[] = $node_def->__toString();
						}							
						break;
					case 'rows':
						foreach ($xml_node->children() as $node_row)
						{
							$row = Array();
							foreach ($node_row->children() as $field)
							{
								$name = $field->getName();
								$value = $field->__toString();
								$row[$name] = $value;
							}
							
							$this->FRows[] = $row;
							$this->FRowsCount++;
							$this->FIndex[] = $this->FRowsCount - 1;
						}							
						break;
				}
			}
		}
		
		function saveXML($url)
		{
			
			$s =		'<ds_data>'."\n".
						'	<fielddefs>'."\n";
			foreach ($this->FieldDefs as $fielddef) 
				$s .=	'		<field>'.$fielddef.'</field>'."\n";
			$s .=		'	</fielddefs>'."\n";
			$s .=		'	<rows>'."\n";
			
			foreach($this->FRows as $row)
			{
				$s .=	'		<row> '."\n";
				foreach ($this->FieldDefs as $fielddef)
					$s.='			<'.$fielddef.'>'.$row[$fielddef].'</'.$fielddef.'>'."\n";
				$s .=	'		</row>'."\n";
			}
			$s .=		'	</rows>'."\n";
			$s .=		'</ds_data>'."\n";
			
			file_put_contents($url, $s);			
		}
	}

?>