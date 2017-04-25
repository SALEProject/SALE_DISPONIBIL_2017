(
	function (id)
	{
		this.htmlID = id;
		var htmlID = id;
		this.jsform = getJSform("%parent%");
		this.dateObject = new Date();
		this.Offset = 0; //  used to sync between server clock and client clock
		this.ClockType = "clClock";
		this.Duration = 60000;
		this.Started = false;
		
		this.start = function()
		{
			this.dateObject = new Date();
			this.Started = true;
			//alert("timer started");
		}
		
		this.stop = function()
		{
			this.dateObject = new Date();
			this.Started = false;
		}
		
		//  below value is given in milliseconds
		this.sync = function(value)
		{
			/*var now = new Date(); //  as a ref
			var srvtime = new Date(value);
			
			this.Offset = srvtime - now;
			this.dateObject = now;*/
			this.dateObject = new Date(value);
		}
		
		this.str_pad_left = function(string, pad, length) 
		{
		    return (new Array(length + 1).join(pad) + string).slice(-length);
		}

		this.onTime = function(self)
		{
			switch (self.ClockType.toLowerCase())
			{
				case "ctclock":
					self.dateObject.setSeconds(self.dateObject.getSeconds() + 1);
					$update(htmlID, self.dateObject.toTimeString().substring(0, 8));
					break;
				case "ctstopwatch":
					break;
				case "ctcountdown":
					var newDate = new Date();
					var diff = 0;
					if (self.Started) diff = newDate - self.dateObject;					
					if (diff > self.Duration) diff = self.Duration;					
					diff = Math.ceil((self.Duration - diff) / 1000);
					var minutes = Math.floor(diff / 60);
					var seconds = diff % 60;
					$update(htmlID, self.str_pad_left(minutes, '0', 2) + ':' + self.str_pad_left(seconds, '0' , 2));
					break;
			}

			setTimeout(arguments.callee, 1000, self);			
		}
		
		setTimeout(this.onTime, 1000, this);			
	}
)