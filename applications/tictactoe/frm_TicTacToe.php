<?php
	
	class Tfrm_TicTacToe extends TForm
	{
		var $grid_w = 3;
		var $grid_h = 3;
		var $grid = Array();
		var $GameOver;
		var $RecursiveScore;
		var $min_score;
				
		function OnLoad()
		{
			for ($j = 0; $j < $this->grid_h; $j++)
			{
				for($i = 0; $i < $this->grid_w; $i++)
				{
					$btn = new TButton();
					$btn->Name = 'btn_'.$i.'_'.$j;
					$btn->Left = 112 + $i * 64;
					$btn->Top = 16 + $j * 64;
					$btn->Width = 64;
					$btn->Height = 64;
					$btn->OnClick = 'TicTacClick';
					$btn->Parent = $this;
					
					$shp = new TShape();
					$shp->Name = 'shp_'.$i.'_'.$j;
					$shp->Left = 113 + $i * 64;
					$shp->Top = 17 + $j * 64;
					$shp->Width = 59;
					$shp->Height = 60;
					$shp->PenColor = 'gray';
					$shp->BrushColor = 'silver';
					$shp->Shape = 'stRoundRect';
					$shp->Parent = $this;
					$shp->Style = 'font-weight: bold; font-size: 54px; max-height: 64px; ';
					
					$this->Controls[$shp->Name] = $shp;
					$this->Controls[$btn->Name] = $btn;
				}
			}
			$this->newGame();
		}
		
		function btn_New_onclick()
		{
			$this->NewGame();
		}
		
		function newGame()
		{
			//  initiate grid
			$this->grid = Array();
			for ($j = 0; $j < $this->grid_h; $j++)
			{
				$a = Array();
				for ($i = 0; $i < $this->grid_w; $i++)
				{
					$a[] = '';
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
		}
		
		function TicTacClick($sender)
		{
			if (!isset($sender)) return;
			$id = $sender;
			/*			
			$a = split('\.', $sender);
			if(count($a) < 2) return;
			
			$parent = $a[0];
			$id = $a[1];
			*/
			
			$a = split('\_', $id);
			if(count($a) < 2) return;
			
			$col = $a[1];
			$row = $a[2];
					
			$id_btn = 'btn_'.$col.'_'.$row;
			$id_shpX = 'shp_'.$col.'_'.$row;
			
			//place Xs
			$this->Controls[$id_btn]->Visible = false;
			$this->Controls[$id_shpX]->Caption = 'X';
			$this->Controls[$id_shpX]->FontColor = 'black';
			$this->grid[$row][$col] = 'x';
			
			if($this->hasWon('x'))
			{
				TQuark::instance()->addAjaxStack('', 'alert', 'X has Won!');
				return;
			}
			
			if($this->noMoreMoves()) return;
			
			//place 0s
			/*$i = rand(0, $this->grid_w - 1);
			$j = rand(0, $this->grid_h - 1);
			
			while($this->grid[$j][$i] != '')
			{
				$i = rand(0, $this->grid_w - 1);
				$j = rand(0, $this->grid_h - 1);	
			}*/
			
			$best_score = 100000;
			$best_i = -1;
			$best_j = -1;
			
			for ($j = 0; $j < $this->grid_h; $j++)
			{
				for ($i = 0; $i < $this->grid_w; $i++)
				{
					if ($this->grid[$j][$i] == '')
					{
						$this->grid[$j][$i] = '0';
						$x = $this->score('x');
						$o = $this->score('0');
						$score = $x - $o;
						
						if ($score < $best_score)
						{
							$best_score = $score;
							$best_i = $i;
							$best_j = $j;							
						}
						$this->grid[$j][$i] = '';
					}
				}
			}
			
			//place 0s
			if ($best_i >= 0 && $best_j >= 0)
			{
				$this->grid[$best_j][$best_i] = '0';
				
						    
				$id_btn0 = 'btn_'.$best_i.'_'.$best_j;
				$id_shp0 = 'shp_'.$best_i.'_'.$best_j;
				
				$this->Controls[$id_btn0]->Visible = false;
				$this->Controls[$id_shp0]->Caption = '0';
				$this->Controls[$id_shp0]->FontColor = 'red';
			    }
				
				if ($this->hasWon('0'))
					TQuark::instance()->addAjaxStack('', 'alert', 'You LOST!!!!');
			}
		
		
		function minimax()
			{
				
			} 
		
		function hasWon($s)
		{
			if($this->grid[0][0] == $s && $this->grid[0][1] == $s && $this->grid[0][2] == $s) return true;
			if($this->grid[1][0] == $s && $this->grid[1][1] == $s && $this->grid[1][2] == $s) return true;
			if($this->grid[2][0] == $s && $this->grid[2][1] == $s && $this->grid[2][2] == $s) return true;
			
			if($this->grid[0][0] == $s && $this->grid[1][0] == $s && $this->grid[2][0] == $s) return true;
			if($this->grid[0][1] == $s && $this->grid[1][1] == $s && $this->grid[2][1] == $s) return true;
			if($this->grid[0][2] == $s && $this->grid[1][2] == $s && $this->grid[2][2] == $s) return true;
			
			if($this->grid[0][0] == $s && $this->grid[1][1] == $s && $this->grid[2][2] == $s) return true;
			if($this->grid[2][0] == $s && $this->grid[1][1] == $s && $this->grid[0][2] == $s) return true;

			return false;
		}
		
		function score($val)
		{
			$value = 0;
			
			if($this->grid[1][1] == 'x')
			{
				if($this->grid[0][0] == $val) $value += 1;
				if($this->grid[0][2] == $val) $value += 1;
				if($this->grid[2][0] == $val) $value += 1;
				if($this->grid[2][2] == $val) $value += 1;
			}
						
			
		    if($this->grid[1][0] == $val) $value +=1;
			
		
			if($this->grid[2][1] == $val) $value +=1;
			 
		
			if($this->grid[1][2] == $val) $value +=1;
			
	
			if($this->grid[0][1] == $val) $value +=1;	
			
			if($this->grid[1][1] == $val && $val == 'x') $value += -1;
			else if($this->grid[1][1] == $val) $value += 3;
			
			$count_doubles = 0;
			for($k = 0; $k < 3; $k++)
			{	
				$count_doubles += $this->checkV($k, $val);
				$count_doubles += $this->checkH($k, $val);
			}
			
			$count_doubles += $this->checkD($val);
			$count_doubles += $this->checkS($val);
				
			if ($count_doubles == 1 ) $value += 10;
			else if (($count_doubles > 1) ) $value += 2 * ($count_doubles * 10);
			
			if ($this->hasWon($val)) $value += 100;
			
			return $value;
		}
		
		function checkV($i, $val)
		{
			$count_val = 0;
			$count_nval = 0;
			
			for($j = 0; $j < $this->grid_h; $j++)
			{
				if ($this->grid[$j][$i] != '') 
					if ($this->grid[$j][$i] == $val) $count_val++; 
					else $count_nval++;					
			}
		
			if ($count_val == 2 && $count_nval == 0) return 1;
			else if ($count_val == 1 && $count_nval == 2) return 2;
			else return 0;
		}
		
		function checkH($j, $val)
		{
			$count_val = 0;
			$count_nval = 0;
			
			for($i = 0; $i < $this->grid_h; $i++)
			{
				if ($this->grid[$j][$i] != '') 
					if ($this->grid[$j][$i] == $val) $count_val++; 
					else $count_nval++;					
			}
		
			if ($count_val == 2 && $count_nval == 0) return 1;
			else if($count_val == 1 && $count_nval == 2) return 2;
			else return 0;		
		}
		
		function checkD($val)
		{
			$count_val = 0;
			$count_nval = 0;
				
			for($i = 0; $i < $this->grid_h; $i++)
			{
				if ($this->grid[$i][$i] != '')
					if ($this->grid[$i][$i] == $val) $count_val++;
					else $count_nval++;
			}
			
			if ($count_val == 2 && $count_nval == 0) return 1;
			else if($count_val == 1 && $count_nval == 2) return 2;
			else return 0;
		}
		
		function checkS($val)
		{
			$count_val = 0;
			$count_nval = 0;
		
			for($i = 0; $i < $this->grid_h; $i++)
			{
				if ($this->grid[2 - $i][$i] != '')
					if ($this->grid[2 - $i][$i] == $val) $count_val++;
					else $count_nval++;
			}
				
			if ($count_val == 2 && $count_nval == 0) return 1;
			else if($count_val == 1 && $count_nval == 2) return 2;
			else return 0;
		}
		
		function noMoreMoves()
		{
			for($j = 0; $j < $this->grid_h; $j++)
			{
				for($i = 0; $i < $this->grid_w; $i++)
				{
					if($this->grid[$j][$i] == '') return false;					
				}
			}
			TQuark::instance()->addAjaxStack('', 'alert', 'Tie!');
			return true;
		}

		
			
		function isTerminalNode()
		{
			
		}	
		
		function btn_Close_onclick()
		{
			$this->close();
		}
		
	}

?>