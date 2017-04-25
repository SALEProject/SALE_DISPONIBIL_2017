<?php
	


	 


	class Tfrm_Calculator extends TForm
	{
		
		var $state ='state1';
		var $op = '';
		var $op2 = '';
		var $rez = '';
		var $op1 = '';
		var $event = '';
		var $prev = 'No previous calculation';
		var $prev1 ='No previous calculation';
		var $showprev = 'OFF';
		var $updateprev = 'ON';	
		

	
		function Calc($sender) 
		{
			if($this->showprev == 'ON' && $sender !=="equal")
			{
				$this->prev1 = $this->prev;
				$te = $this->DisplayPREV;
				$te->set_Text($this->prev1);
			}
			else if($this->updateprev == 'ON' && $sender !=="equal")
			{
				$this->prev1 = $this->prev;
			}
			
		
			switch ($this->state)
			{
				case 'state1':
					
					
					$this->MF1($sender);
					
					
					
					
					break;
				case 'state2':
					$this->MF2($sender);
					
					break;
				default :
					TQuark::instance()->browserAlert("No state");
			}
		}
		
		
		function MF1($sender)
		{
			
			
			switch ($sender)
			{
				case '.':
				case '0':
				case '1':
				case '2':
				case '3':
				case '4':
				case '5':
				case '6':
				case '7':
				case '8':
				case '9':
					if($this->rez == "")
					{
					
					$this->op1 .=$sender;

					$te = $this->DisplayOP1;
					$te->set_Text($this->op1);
					$te = $this->DisplayOP;
					$te->set_Text($this->op);
					$te = $this->DisplayOP2;
					$te->set_Text("");
					$te = $this->DisplayREZ;
					$te->set_Text("");
					$te = $this->DisplayEGAL;
					$te->set_Text("");
					$this->op = "";
					$this->op2 = "";
					$this->rez = "";
					
					}
					else
					{
					$this->op1 .=$sender;
					$te = $this->DisplayOP1;
					$te->set_Text($this->op1);
					$te = $this->DisplayOP;
					$te->set_Text($this->op);
					$te = $this->DisplayOP2;
					$te->set_Text("");
					$te = $this->DisplayREZ;
					$te->set_Text("");
					$te = $this->DisplayEGAL;
					$te->set_Text("");
					$this->op = "";
					$this->op2 = "";
					$this->rez = "";
					}
					
					break;
		
					case 'plus':
						if($this->rez == "")
						{
							$this->op =$sender;
							$this->state = 'state2';
							$te = $this->DisplayOP;
							$te->set_Text("+");
							
							
					
						}
						else
						{
								
							$this->op1 = $this->rez;
							$this->op =$sender;
							$te = $this->DisplayOP;
							$te->set_Text("+");
							$te = $this->DisplayOP1;
							$te->set_Text($this->op1);
							$te = $this->DisplayOP2;
							$te->set_Text("");
							$te = $this->DisplayREZ;
							$te->set_Text("");
							$te = $this->DisplayEGAL;
							$te->set_Text("");
							$this->op2 = "";
							$this->rez = "";
							$this->state = "state2";
								
						}
						break;
					case 'minus':
						if($this->rez == "")
						{
							$this->op =$sender;
							$this->state = 'state2';
							$te = $this->DisplayOP;
							$te->set_Text("-");
	
						}
						else
						{
								
							$this->op1 = $this->rez;
							$this->op =$sender;
							$te = $this->DisplayOP;
							$te->set_Text("-");
							$te = $this->DisplayOP1;
							$te->set_Text($this->op1);
							$te = $this->DisplayOP2;
							$te->set_Text("");
							$te = $this->DisplayREZ;
							$te->set_Text("");
							$te = $this->DisplayEGAL;
							$te->set_Text("");
							$this->op2 = "";
							$this->rez = "";
							$this->state = "state2";
								
						}
						break;
					case 'mult':
						if($this->rez == "")
						{
							$this->op =$sender;
							$this->state = 'state2';
							$te = $this->DisplayOP;
							$te->set_Text("*");
						}
						else
						{
								
							$this->op1 = $this->rez;
							$this->op =$sender;
							$te = $this->DisplayOP;
							$te->set_Text("*");
							$te = $this->DisplayOP1;
							$te->set_Text($this->op1);
							$te = $this->DisplayOP2;
							$te->set_Text("");
							$te = $this->DisplayREZ;
							$te->set_Text("");
							$te = $this->DisplayEGAL;
							$te->set_Text("");
							$this->op2 = "";
							$this->rez = "";
							$this->state = "state2";
								
						}
						break;
					case 'div':
						if($this->rez == "")
						{
							$this->op =$sender;
							$this->state = 'state2';
							$te = $this->DisplayOP;
							$te->set_Text("/");
							
	
						}
						else
						{
								
							$this->op1 = $this->rez;
							$this->op =$sender;
							$te = $this->DisplayOP;
							$te->set_Text("/");
							$te = $this->DisplayOP1;
							$te->set_Text($this->op1);
							$te = $this->DisplayOP2;
							$te->set_Text("");
							$te = $this->DisplayREZ;
							$te->set_Text("");
							$te = $this->DisplayEGAL;
							$te->set_Text("");
							$this->op2 = "";
							$this->rez = "";
							$this->state = "state2";
							
					}
					break;
				case 'dec':
					if ($this->op1 !== '' && strpos($this->op1, ".") === FALSE)
					{
							$this->op1 .='.';
							$te = $this->DisplayOP1;
							$te->set_Text($this->op1);
					}
						else if ($this->rez == '' && $this->op1 == '' && strpos($this->op1, ".") === FALSE)
						{
							$this->op1 .='0.';
							$te = $this->DisplayOP1;
							$te->set_Text($this->op1);
							$te = $this->DisplayOP;
							$te->set_Text("");
							$te = $this->DisplayOP2;
							$te->set_Text("");
							$te = $this->DisplayREZ;
							$te->set_Text("");
							$te = $this->DisplayEGAL;
							$te->set_Text("");
						}
						
						else if ($this->rez !== '' && $this->op1 == '' && strpos($this->rez, ".") === FALSE)
						{
							$this->op1 = $this->rez;
							$this->op1 .='.';
							$te = $this->DisplayOP;
							$te->set_Text("");
							$te = $this->DisplayOP1;
							$te->set_Text($this->op1);
							$te = $this->DisplayOP2;
							$te->set_Text("");
							$te = $this->DisplayREZ;
							$te->set_Text("");
							$te = $this->DisplayEGAL;
							$te->set_Text("");
							$this->op2 = "";
							$this->rez = "";
						}
						
						else if ($this->rez !== '' && $this->op1 == '' && strpos($this->rez, ".") !== FALSE)
						{
							$this->op1 = $this->rez;
							$te = $this->DisplayOP;
							$te->set_Text("");
							$te = $this->DisplayOP1;
							$te->set_Text($this->op1);
							$te = $this->DisplayOP2;
							$te->set_Text("");
							$te = $this->DisplayREZ;
							$te->set_Text("");
							$te = $this->DisplayEGAL;
							$te->set_Text("");
							$this->op2 = "";
							$this->rez = "";
							
						}
							
						break;
				case 'Del' :
					if($this->rez == '' && $this->rez !=="Err: Division by 0")
					{
					$this->op1 = substr($this->op1,0,-1);
					$te = $this->DisplayOP1;
					$te->set_Text($this->op1);
					}
					else if($this->rez !=="Err: Division by 0")
					{
					$this->op1 = $this->rez;
					$this->op1 = substr($this->op1,0,-1);
					$te = $this->DisplayOP1;
					$te->set_Text($this->op1);
					$this->op = '';
					$this->op2 = '';
					$this->rez = '';
					$te = $this->DisplayOP;
					$te->set_Text($this->op);
					$te = $this->DisplayOP2;
					$te->set_Text($this->op2);
					$te = $this->DisplayREZ;
					$te->set_Text($this->rez);
					$te = $this->DisplayEGAL;
					$te->set_Text('');
					}
				case 'equal' :
					break;
					Default:
						TQuark::instance()->browserAlert("no value");
			}

	}
	
	function MF2($sender)
	{
		
		switch ($sender)
		{
			case '.':
			case '0':
			case '1':
			case '2':
			case '3':
			case '4':
			case '5':
			case '6':
			case '7':
			case '8':
			case '9':
				
			
				
				$this->op2 .=$sender;
				$te = $this->DisplayOP2;
				$te->set_Text($this->op2);
				break;
	
			case 'Del' :
				if($this->op2 == '' && $this->op =='')
				{
					$this->op1 = substr($this->op1,0,-1);
					$te = $this->DisplayOP1;
					$te->set_Text($this->op1);
					$this->op = "";
					$te = $this->DisplayOP;
					$te->set_Text($this->op);
					$this->state = "state1";
				}
				
				else if($this->op2 == '' && $this->op !=='')
				{
					$this->op = "";
					$te = $this->DisplayOP;
					$te->set_Text($this->op);
					$this->state = "state1";
				}
				
				else if($this->rez !==" Err: Division by 0")
				{
					$this->op2 = substr($this->op2,0,-1);
					$te = $this->DisplayOP2;
					$te->set_Text($this->op2);
				}
				
				break;
				
		case 'dec':
					if ($this->op2 !== '' && strpos($this->op2, ".") === FALSE)
					{
							$this->op2 .='.';
							$te = $this->DisplayOP2;
							$te->set_Text($this->op2);
					}
						else if (strpos($this->op2, ".") === FALSE)
						{
							$this->op2 .='0.';
							$te = $this->DisplayOP2;
							$te->set_Text($this->op2);
						}
				break;
				
			
			case 'equal';
				$te = $this->DisplayEGAL;
				$te->set_Text("=");
				$this->state = "state1";
				
				
				
				if($this->op == 'plus')
			{
				$this->rez=($this->op1+$this->op2);
				$this->prev =($this->op1." + ".$this->op2." = ".$this->rez);
				$te = $this->DisplayREZ;
				$te->set_Text($this->rez);
				$this->op1 = "";
				$this->op2 = "";
				$this->op = "";
				
			}
	
			elseif ($this->op == 'minus')
			{
				$this->rez=($this->op1-$this->op2);
				$this->prev =($this->op1." - ".$this->op2." = ".$this->rez);
				$this->op1 = "";
				$this->op2 = "";
				$this->op = "";
				$this->state = "state1";
				$te = $this->DisplayREZ;
				$te->set_Text($this->rez);
			}
			elseif ($this->op == 'div' and $this->op2==0)
			{
			
				
				$te = $this->DisplayREZ;
				$te->set_Text(" Err: Division by 0");
				$this->rez = "";
				$this->op1 = "";
				$this->op2 = "";
				$this->op = "";
				$this->state = "state1";
	
			}
	
			elseif ($this->op == 'div' and $this->op!==0)
			{
				$this->rez=($this->op1/$this->op2);
				$this->prev =($this->op1." / ".$this->op2." = ".$this->rez);
				$te = $this->DisplayREZ;
				$te->set_Text($this->rez);
				$this->op1 = "";
				$this->op2 = "";
				$this->op = "";
				$this->state = "state1";
			}
	
			elseif ($this->op == 'mult')
			{
				$this->rez=($this->op1*$this->op2);
				$this->prev =($this->op1." * ".$this->op2." = ".$this->rez);
				$te = $this->DisplayREZ;
				$te->set_Text($this->rez);
				$this->op1 = "";
				$this->op2 = "";
				$this->op = "";
				$this->state = "state1";
			}
			break;
			
			case 'plus':
				if($this->op1 !=="" && $this->op2 !=="")
					{
					$this->rez =($this->op1+$this->op2);
					$this->prev =($this->op1." + ".$this->op2." = ".$this->rez);
					$this->op1 = $this->rez;
					$te = $this->DisplayOP1;
					$te->set_Text($this->op1);
					$te = $this->DisplayOP;
					$te->set_Text('+');
					$te = $this->DisplayOP2;
					$te->set_Text('');
					$te = $this->DisplayREZ;
					$te->set_Text('');
					$this->op2='';
					$this->rez ='';
					}
				
				break;
				
				case 'minus':
					if($this->op1 !=="" && $this->op2 !=="")
					{
						$this->rez =($this->op1-$this->op2);
						$this->prev =($this->op1." - ".$this->op2." = ".$this->rez);
						$this->op1 = $this->rez;
						$te = $this->DisplayOP1;
						$te->set_Text($this->op1);
						$te = $this->DisplayOP;
						$te->set_Text('-');
						$te = $this->DisplayOP2;
						$te->set_Text('');
						$te = $this->DisplayREZ;
						$te->set_Text('');
						$this->op2='';
						$this->rez ='';
					}
				
					break;
				
					case 'mult':
						if($this->op1 !=="" && $this->op2 !=="")
						{
							$this->rez =($this->op1*$this->op2);
							$this->prev =($this->op1." * ".$this->op2." = ".$this->rez);
							$this->op1 = $this->rez;
							$te = $this->DisplayOP1;
							$te->set_Text($this->op1);
							$te = $this->DisplayOP;
							$te->set_Text('*');
							$te = $this->DisplayOP2;
							$te->set_Text('');
							$te = $this->DisplayREZ;
							$te->set_Text('');
							$this->op2='';
							$this->rez ='';
						}
					
						break;
						
						case 'div':
							if($this->op1 !=="" && $this->op2 !=="" && $this->op2 !='0')
							{
								$this->rez =($this->op1/$this->op2);
								$this->prev =($this->op1." / ".$this->op2." = ".$this->rez);
								$this->op1 = $this->rez;
								$te = $this->DisplayOP1;
								$te->set_Text($this->op1);
								$te = $this->DisplayOP;
								$te->set_Text('/');
								$te = $this->DisplayOP2;
								$te->set_Text('');
								$te = $this->DisplayREZ;
								$te->set_Text('');
								$this->op2='';
								$this->rez ='';
								$this->op = '';
								$this->op1= '';
							}
							else if($this->op1 !=="" && $this->op2 !=="")
							{
								$this->op1 = "";
								$this->op2 = "";
								$this->op = "";
								$this->state = "state1";
								$te = $this->DisplayREZ;
								$te->set_Text("You cannot divide by zero.");
								$te = $this->DisplayOP1;
								$te->set_Text('');
								$te = $this->DisplayOP;
								$te->set_Text('');
								$te = $this->DisplayOP2;
								$te->set_Text('');
							}
						
							break;
		}
	}
	

	function PRE()
	{
		if($this->showprev=="OFF")
		{
		$this->showprev = "ON";
		$this->updateprev = "ON";
		$te = $this->DisplayPREV;
		$te->set_Text($this->prev1);
		
		}
		else if($this->showprev=="ON")
		{

		$te = $this->DisplayPREV;
		$te->set_Text('');
		$this->showprev = "OFF";
		$this->updateprev = "OFF";
		}
			
		
	}
	
	function RESET()
	{
		
		
	$this->op1 = '';
	$te = $this->DisplayOP1;
	$te->set_Text('');
	$this->op = '';
	$te = $this->DisplayOP;
	$te->set_Text('');
	$this->op2 = '';
	$te = $this->DisplayOP2;
	$te->set_Text('');
	$this->rez = '';
	$te = $this->DisplayREZ;
	$te->set_Text('');
	$te = $this->DisplayEGAL;
	$te->set_Text('');
	$this->state ='state1';
	$this->event = '';
	$this->prev = 'No previous calculation';
	$this->showprev = 'OFF';
	$this->prev1 ='No previous calculation';
	$te = $this->DisplayPREV;
	$te->set_Text('');
	$this->updateprev = 'ON';
	
		
		
	}
}
	
	
	
	
?>