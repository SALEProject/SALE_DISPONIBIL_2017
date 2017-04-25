(
	function (id)
	{
		var element = $(id),
			parrent = $(id + '-wrapper'),
			datePicker;
		
		if(element.nodeName){
		    datePicker = new datePicker();
		
			datePicker.init();
				
			return datePicker;
		}
		
			
		function datePicker () {
			
			var self = this,
				//calendarContainer = document.createElement('div'),
				calendarContainer,
				//navigationCurrentMonth = document.createElement('span'),
				
				/*calendar = document.createElement('table'),
		        calendarBody = document.createElement('tbody'),*/
				calendar,
		        calendarBody,
				currentDate = new Date(),
				wrapperElement,
				date = {
			    	year: currentDate.getFullYear(),
			    	month: currentDate.getMonth(),
			    	day: currentDate.getDate()
			    },
				weekdays = {
				    		shorthand: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
				    	    longhand: ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday']
				          },
				months = {
				    	 shorthand: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
				    	 longhand: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December']
				        },
		        daysInMonth = [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31],
		        firstDayOfWeek =  0;	
				
			//calendarContainer.className = 'datepicker-calendar';
		    //navigationCurrentMonth.className = 'datepicker-current-month';

		    self.updateNavigationCurrentMonth = function () {
		    	//navigationCurrentMonth.innerHTML = months.longhand[self.currentMonthView] + ' ' + self.currentYearView;
		    	$('datepicker-current-month-' + id).innerHTML = months.longhand[self.currentMonthView] + ' ' + self.currentYearView;
		    }
		    
		    self.handleYearChange = function () {
		        if (self.currentMonthView < 0) {
		            self.currentYearView--;
		            self.currentMonthView = 11;
		        }
	
		        if (self.currentMonthView > 11) {
		            self.currentYearView++;
		            self.currentMonthView = 0;
		        }
		    };
			
			self.buildMonthNavigation = function () {
				var months = document.createElement('div'),
		            monthNavigation;
				
		        //monthNavigation  = '<span class="datepicker-prev-month">&lt;</span>';
		        //monthNavigation += '<span class="datepicker-next-month">&gt;</span>';
		        
		        //months.className = 'datepicker-months';
		        //months.innerHTML = monthNavigation;
		        
		        //months.appendChild(navigationCurrentMonth);
		        self.updateNavigationCurrentMonth();
		        //calendarContainer.appendChild(months);
			}
			
			self.buildWeekdays = function () {
				var weekDayContainer = document.createElement('thead');
				
				weekDayContainer.innerHTML = '<tr><th>' + weekdays.shorthand.join('</th><th>') + '</th></tr>';
				calendar.appendChild(weekDayContainer);
			}
			
			self.isSpecificDay = function (day, month, year, comparison) {
		        return day === comparison && self.currentMonthView === month && self.currentYearView === year;
		    };
			
			self.buildDays = function () {
				var firstOfMonth = new Date(self.currentYearView, self.currentMonthView, 1).getDay(),
					//numDays = daysInMonth[self.currentMonthView],
					numDays = new Date(self.currentYearView, self.currentMonthView + 1, 0).getDate(),
					
					calendarFragment = document.createDocumentFragment(),
					row = document.createElement('tr'),
					dayCount,
					dayNumber,
					today = '',
					selected = '',
					disabled = '',
					currentTimestamp;

				// Offset the first day by the specified amount
		        firstOfMonth -= firstDayOfWeek;
		        if (firstOfMonth < 0) {
		            firstOfMonth += 7;
		        }
				
		        dayCount = firstOfMonth;
		        
		        calendarBody = $('datepicker-table-' + id).tBodies[0];
		        calendarBody.innerHTML = '';
		        
		        // Add spacer to line up the first day of the month correctly
		        if (firstOfMonth > 0) {
		            row.innerHTML += '<td colspan="' + firstOfMonth + '">&nbsp;</td>';
		        }
		        
		        // Start at 1 since there is no 0th day
		        for (dayNumber = 1; dayNumber <= numDays; dayNumber++) {
		            // if we have reached the end of a week, wrap to the next line
		            if (dayCount === 7) {
		                calendarFragment.appendChild(row);
		                row = document.createElement('tr');
		                dayCount = 0;
		            }
		            
		            today = self.isSpecificDay(date.day, date.month, date.year, dayNumber) ? ' today' : '';
		            
		            if (self.selectedDate) {
		                selected = self.isSpecificDay(self.selectedDate.day, self.selectedDate.month, self.selectedDate.year, dayNumber) ? ' selected' : '';
		            }
		            
		            row.innerHTML += '<td class="' + today + selected + '"><span class="datepicker-day">' + dayNumber + '</span></td>';
		            dayCount++;
		        }
				
				calendarFragment.appendChild(row);
		        calendarBody.appendChild(calendarFragment);
			}
			
			/*self.wrap = function () {
				wrapperElement = document.createElement('div');
		        wrapperElement.className = 'datepicker-wrapper';
	
		        //inserting the wrapper before the input element and appending it 
		        self.element.parentNode.insertBefore(wrapperElement, self.element);
		        wrapperElement.appendChild(self.element);
		    };*/
			
			self.wrap = function () {
				wrapperElement = self.element.parentNode;
				calendarContainer = wrapperElement.getElementsByClassName('datepicker-calendar')[0];
				//navigationCurrentMonth = calendarContainer.getElementsByClassName('datepicker-current-month')[0];
				navigationCurrentMonth = $('datepicker-current-month-' + id);
				calendar = $('datepicker-table-' + id);
		    };
		    
			self.buildCalendar = function () {
				
				self.buildMonthNavigation();
		        self.buildWeekdays();
		        self.buildDays();
	
		        calendar.appendChild(calendarBody);
		        calendarContainer.appendChild(calendar);
		        		        
		        wrapperElement.appendChild(calendarContainer);
		    };
		    
		    self.bind = function () {
		        var openEvent = 'click';
		        
		        if (self.element.nodeName === 'INPUT') {
		            openEvent = 'focus';
		            //self.element.addEventListener('blur', self.close);
		        }
		        
		        if(document.readyState == 'complete')
		        {     
			        //$(self.element.id).addEventListener(openEvent, self.open);
			        //calendarContainer.addEventListener('mousedown', self.calendarClick);
		        }
		    };
		    
		    self.calendarClick = function (event) {
		        var target = event.target,
		            targetClass = target.className,
		            currentTimestamp;
		        		        
		        if (targetClass) {
		            if (targetClass === 'datepicker-prev-month' || targetClass === 'datepicker-next-month') {
		                if (targetClass === 'datepicker-prev-month') {
		                    self.currentMonthView--;
		                } else {
		                	self.currentMonthView++;
		                }
		                
		                self.handleYearChange();
		                self.updateNavigationCurrentMonth();
		                self.buildDays();
		            } else if (targetClass === 'datepicker-day') {
		                self.selectedDate = {
		                    day: parseInt(target.innerHTML, 10),
		                    month: self.currentMonthView,
		                    year: self.currentYearView
		                };
	
		                currentTimestamp = new Date(self.currentYearView, self.currentMonthView, self.selectedDate.day);
		                
		                //self.element.value = self.formatDate(element.pattern, currentTimestamp);          
		                $(self.element.id).value = self.formatDate(element.pattern, currentTimestamp);
		                
		                $removeClass(self.element.id, 'hint');
		                $addClass(self.element.id, 'verified');
		                
		                self.close();
		                self.buildDays();
		            }
		        }
		    };
		    
		    self.documentClick = function (event) {
		        var parent,
		        	targetClass = event.target.className;
	        	
		        if (event.target.id !== self.element.id && event.target !== wrapperElement) {
		        	parent = event.target.parentNode;
			        if (parent !== wrapperElement) {
			        	while (parent.id !== wrapperElement.id) {
			        		parent = parent.parentNode;
			                if (parent === null) {
			                	self.close();
			                    break;
			                }
			            }
			        }
		         }
		    };
		    
		    self.open = function () {
		        //wrapperElement.classList.add('open');
		    	$addClass(wrapperElement.id, 'open');

		        document.addEventListener('click', self.documentClick);
		    };
		    
		    self.close = function () {
		        document.removeEventListener('click', self.documentClick);
		        //wrapperElement.classList.remove('open');
		        $removeClass(wrapperElement.id, 'open');
		       
		        //$removeClass(self.element.id, 'verified');
		    };
		    
		    /*self.destroy = function () {
		        var parent,
		            element;
	
		        document.removeEventListener('click', documentClick);
		        self.element.removeEventListener('focus', open);
		        self.element.removeEventListener('blur', close);
		        self.element.removeEventListener('click', open);
	
		        parent = self.element.parentNode;
		        parent.removeChild(calendarContainer);
		        element = parent.removeChild(self.element);
		        parent.parentNode.replaceChild(element, parent);
		    };*/
	
		    self.formatDate = function (dateFormat, milliseconds) {
		    	var dateObj = new Date(milliseconds),
		    		formattedDate = '',
		    		formats = {
		                d: function () {
		                    var day = formats.j();
		                    return (day < 10) ? '0' + day : day;
		                },
		                D: function () {
		                    return weekdays.shorthand[formats.w()];
		                },
		                j: function () {
		                    return dateObj.getDate();
		                },
		                l: function () {
		                    return weekdays.longhand[formats.w()];
		                },
		                w: function () {
		                    return dateObj.getDay();
		                },
		                F: function () {
		                    return self.monthToStr(formats.n() - 1, false);
		                },
		                m: function () {
		                    var month = formats.n();
		                    return (month < 10) ? '0' + month : month;
		                },
		                M: function () {
		                    return self.monthToStr(formats.n() - 1, true);
		                },
		                n: function () {
		                    return dateObj.getMonth() + 1;
		                },
		                U: function () {
		                    return dateObj.getTime() / 1000;
		                },
		                y: function () {
		                    return String(formats.Y()).substring(2);
		                },
		                Y: function () {
		                    return dateObj.getFullYear();
		                }
		            },
		    		formatPieces = dateFormat.split('');
		    	
		    	[].forEach.call(formatPieces, function(formatPiece, index){
		    		if(formats[formatPiece]) {
		    			formattedDate += formats[formatPiece]();
		    		}
		    		else{
		    			formattedDate += formatPiece;
		    		}
		    	});
		    	
		    	return formattedDate;
		    };
		    
		    self.monthToStr = function (date, shorthand) {
		        if (shorthand === true) {
		            return months.shorthand[date];
		        }
		        
		        return longhand[date];
		    };
		    
			self.init = function() {
				var parsedDate;
				
				self.element = element;
				
				if (self.element.value) {
		            parsedDate = Date.parse(self.element.value);
		        }
				
				if (parsedDate && !isNaN(parsedDate)) {
		            parsedDate = new Date(parsedDate);
		            self.selectedDate = {
		                day: parsedDate.getDate(),
		                month: parsedDate.getMonth(),
		                year: parsedDate.getFullYear()
		            };
		            self.currentYearView = self.selectedDate.year;
		            self.currentMonthView = self.selectedDate.month;
		            self.currentDayView = self.selectedDate.day;
		        } else {
		            self.selectedDate = null;
		            self.currentYearView = date.year;
		            self.currentMonthView = date.month;
		            self.currentDayView = date.day;
		        }
								
				self.wrap();
				self.buildCalendar();
				//self.bind();
			};	
	
	};
		
})
