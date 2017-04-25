<?php

	class Tfrm_Minesweeper extends TForm
	{
		var $grid_w = 9;
		var $grid_h = 9;
		var $grid = Array();
		var $countLeft;
		var $colors = Array('black', 'blue', 'green', 'red', 'navy', 'purple', 'teal', 'gray', 'maroon');		
		//var $colors = Array('#000000', '#0000ff', '#008000', '#ff0000', '#000080', '#800080', '#008080', '#808080', '#800000');
		
		function OnLoad()
		{
			for ($j = 0; $j < $this->grid_h; $j++)
			{
				for ($i = 0; $i < $this->grid_w; $i++)
				{
					$btn = new TButton();
					//$btn->Theme = $this->Theme;
					$btn->Name = 'btn_'.$i.'_'.$j;
					$btn->Left = 128 + $i * 32;
					$btn->Top = 16 + $j * 32;
					$btn->Width = 32;
					$btn->Height = 32;
					$btn->OnClick = 'MineButtonClick';
					$btn->Parent = $this;
					
					$shp = new TShape();
					$shp->Name = 'shp_'.$i.'_'.$j;
					$shp->Left = 128 + $i * 32;
					$shp->Top = 16 + $j * 32;
					$shp->Width = 32;
					$shp->Height = 32;
					$shp->BrushColor = 'silver';
					$shp->PenColor = 'gray';
					$shp->Shape = 'stRoundRect';
					$shp->Parent = $this;
					$shp->Style = 'font-weight: bold; font-size: 16px; padding-top: 8px; max-height: 24px;';
										
					$this->Controls[$shp->Name] = $shp;
					$this->Controls[$btn->Name] = $btn;
				}
			}
			
			$this->NewGame();
		}
		
		function getMinesCount($i, $j)
		{
			if ($this->grid[$j][$i] == -1) return -1;
			
			$res = 0;
			for ($v = $j - 1; $v < $j + 2; $v++)
				for ($u = $i - 1; $u < $i + 2; $u++)
					if ($u >= 0 && $u < $this->grid_w && $v >= 0 && $v < $this->grid_h)
						if ($this->grid[$v][$u] == -1) $res++;
			
			return $res;
		}
		
		function NewGame()
		{
			//  initiate grid
			$this->grid = Array();
			for ($j = 0; $j < $this->grid_h; $j++)
			{
				$a = Array();
				for ($i = 0; $i < $this->grid_w; $i++)
				{
					$a[] = 0;
					$id_btn = 'btn_'.$i.'_'.$j;
					$id_shp = 'shp_'.$i.'_'.$j;
					$this->Controls[$id_btn]->Visible = true;
					$this->Controls[$id_shp]->Caption = '';
					$this->Controls[$id_shp]->Shape = 'stRoundRect';
					$this->Controls[$id_shp]->BrushColor = 'silver';
					$this->Controls[$id_shp]->PenColor = 'gray';						
				}
				
				$this->grid[] = $a;
			}
			
			//  place mines
			$i = rand(0, $this->grid_w - 1);
			$j = rand(0, $this->grid_h - 1);
			for ($k = 0; $k < 10; $k++) 
			{
				while ($this->grid[$j][$i] < 0)
				{
					$i = rand(0, $this->grid_w - 1);
					$j = rand(0, $this->grid_h - 1);
				}
				$this->grid[$j][$i] = -1;
			}
			$this->countLeft = $this->grid_w * $this->grid_h;

			//  place markers
			for ($j = 0; $j < $this->grid_h; $j++)
				for ($i = 0; $i < $this->grid_w; $i++)
				{
					$k = $this->getMinesCount($i, $j);
					$this->grid[$j][$i] = $k;
				}
		}
		
		function btn_New_onclick()
		{
			$this->NewGame();
			//TQuark::instance()->addAjaxStack('', 'alert', 'Doesn\'t quite work yet. Close and reopen Minesweeper. Thx');
		}
		
		function ExposeMine($row, $col)
		{
			if ($row < 0 || $row >= $this->grid_h) return;
			if ($col < 0 || $col >= $this->grid_w) return;
				
			$id_btn = 'btn_'.$col.'_'.$row;
			$id_shp = 'shp_'.$col.'_'.$row;
			$value = $this->grid[$row][$col];
			
			$this->Controls[$id_btn]->Visible = false;
				
			switch ($value)
			{
				case -1:
					$this->Controls[$id_shp]->Caption = 'B';
					$this->Controls[$id_shp]->Shape = 'stCircle';
					$this->Controls[$id_shp]->BrushColor = 'black';
					$this->Controls[$id_shp]->PenColor = 'black';
					$this->Controls[$id_shp]->FontColor = 'red';
					break;
				case 0:
					break;
				default:
					$this->Controls[$id_shp]->Caption = $value;
					$this->Controls[$id_shp]->FontColor = $this->colors[$value];
					break;
			}			
		}
		
		function ExploreMineField($row, $col)
		{
			if ($row < 0 || $row >= $this->grid_h) return;
			if ($col < 0 || $col >= $this->grid_w) return;

			$value = $this->grid[$row][$col];
			if ($value == -2) return;
			
			$this->ExposeMine($row, $col);
			$this->countLeft--;
			$this->grid[$row][$col] = -2;
			if ($value == 0)
			{
				$this->ExploreMineField($row - 1, $col - 1);
				$this->ExploreMineField($row - 1, $col);
				$this->ExploreMineField($row - 1, $col + 1);
				$this->ExploreMineField($row, $col - 1);
				$this->ExploreMineField($row, $col + 1);
				$this->ExploreMineField($row + 1, $col - 1);
				$this->ExploreMineField($row + 1, $col);
				$this->ExploreMineField($row + 1, $col + 1);
			}
			
			return $value;
		}
		
		function MineButtonClick($sender)
		{
			if (!isset($sender)) return;
			$id = $sender;
			/*
			$a = split('\.', $sender);
			if (count($a) < 2) return;
			
			$parent = $a[0];
			$id = $a[1];			
			*/
			$a = split('_', $id);
			if (count($a) < 2) return;
			
			$col = $a[1];
			$row = $a[2];

			$value = $this->ExploreMineField($row, $col);	
			if ($value == -1) TQuark::instance()->addAjaxStack('', 'alert', 'You LOST!');
			else if ($this->countLeft <= 10) TQuark::instance()->addAjaxStack('', 'alert', 'You WON!');			
		}
		
		function btn_Close_onclick()
		{
			$this->close();
		}

	}

?>