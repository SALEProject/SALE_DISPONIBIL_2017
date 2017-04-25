$(window).load(function() {
	//stickyHeader();
});

$(document).ready(function() {
	calculateHeight();

	$(window).scroll(function() {
		//stickyHeader();
	});

	/*
	$('#my-task-list').popover({
		html:true
	})*/
	$('[data-toggle="popup"]').each(function(elm) {
		var $this = $(this);
		var $target = null;

		if(typeof $this.attr('data-trigger') == 'undefined' || $this.attr('data-trigger')=='click') var trigger = 'click';
		else if($this.attr('data-trigger') == 'hover') var trigger = 'hover';
		if(typeof $this.attr('data-target') != 'undefined' && $($this.attr('data-target')).length>0) $target = $($this.attr('data-target'));

		if($target) {
			if(trigger == 'click') {
				$this.on('click',function(e) {
					e.preventDefault();
					//if($target.is(':visible')) $target.fadeOut();
					//else $target.fadeIn();
					$target.toggle();
				});
			}
			else if(trigger == 'hover') {
				$this.on('mouseenter',function(e) {
					$target.show();
				}).on('mouseleave',function(e) {
					$target.hide();
				});
			}
		}
	});


function fancyBlacklist(callback) {
    var ret;
    var s;
    var modalFancybox = $.fancybox({
        modal : true,
        content : '<div style="margin:1px;width:270px;" class="text-center">'+
					'<p>Introduceti motivul refuzului colaborarii cu acest partener.</p>'+
					'<div id="blacklist-form">'+
						'<input type="text" id="blacklist-reason" />'+
					'</div>'+
					'<div class="p-t-20">'+
						'<button class="btn btn-primary btn-cons" id="fancyConfirm_ok">Salveaza</button>&nbsp;'+
						'<button class="btn btn-cancel btn-cons" id="fancyConfirm_cancel">Renunta</button>'+
					'</div>',
        afterShow : function() {
			s = '';
            $("#blacklist-reason").keypress(function(event) {
				if (event.keyCode == 13) {
					$("#blacklist-form").find('.error').remove();
					if($.trim($("#blacklist-reason").val())=='') {
						$("#blacklist-form").append('<div class="error">Campul este obligatoriu.</div>');
					}
					else {
						ret = true;
						s = $("#blacklist-reason").val();
						$.fancybox.close();
					}
				}
			});
            $("#fancyConfirm_cancel").click(function() {
                ret = false;
                $.fancybox.close();
            });
            $("#fancyConfirm_ok").click(function() {
				$("#blacklist-form").find('.error').remove();
				if($.trim($("#blacklist-reason").val())=='') {
					$("#blacklist-form").append('<div class="error">Campul este obligatoriu.</div>');
				}
				else {
					ret = true;
					s = $("#blacklist-reason").val();
					$.fancybox.close();
				}
            })
        },
        afterClose : function() {
            callback.call(this,ret,s);
        }
    });
}

	$(".whitelist-input").change(function(e){
		var $this = $(this);
		log($(".whitelist-input:checked").length);
		if($(".whitelist-input:checked").length<4) {
			fancyAlert('Pentru a putea tranzactiona este nevoie de minim 4 parteneri!');
			if(!$this.is(':checked')) $this.attr('checked',true);
			e.preventDefault();
			return;
		}
		$this.parent().find('label').append('<i class="processing fa fa-spinner fa-spin"></i>');
		if($this.is(':checked')) {
			$.post('/account/whitelistSave',{id:$this.attr('data-id'), checked:$this.is(':checked')},function(data,textStatus){
				if(!data.Success) {
					var msg = '';
					if(data.ResultType=='JSONKeyValuePairStruct') {
						for(var i in data.Result) {
							if(data.Result[i]!='') {
								msg += '<span class="error">'+data.Result[i]+'</span>';
							}
						}
					}
					else if(data.ResultType=='GeneralError' || data.ResultType=='String') {
						msg = data.Result;
					}
					$this.parent().parent().find('.processing').remove();
					$this.parent().parent().append('<i class="processing processing-bad fa fa-exclamation-triangle"></i>');
					fancyAlert(msg);
				}
				else if(data.Success) {
					$this.parent().parent().find('.processing').remove();
					$this.parent().parent().append('<i class="processing processing-ok fa fa-check"></i>');
					$this.parent().parent().find('.blacklist-reason').html('');
				}
				$this.parent().parent().find('.processing').fadeOut(1000,function(){ $(this).remove(); });
			});
		}
		else {
			fancyBlacklist(function(ret,s){
				if(ret) {
					$.post('/account/whitelistSave',{id:$this.attr('data-id'), checked:$this.is(':checked'), reason: s},function(data,textStatus){
						if(!data.Success) {
							var msg = '';
							if(data.ResultType=='JSONKeyValuePairStruct') {
								for(var i in data.Result) {
									if(data.Result[i]!='') {
										msg += '<span class="error">'+data.Result[i]+'</span>';
									}
								}
							}
							else if(data.ResultType=='GeneralError' || data.ResultType=='String') {
								msg = data.Result;
							}
							$this.parent().parent().find('.processing').remove();
							$this.parent().parent().append('<i class="processing processing-bad fa fa-exclamation-triangle"></i>');
							fancyAlert(msg);
						}
						else if(data.Success) {
							$this.parent().parent().find('.processing').remove();
							$this.parent().parent().append('<i class="processing processing-ok fa fa-check"></i>');
							$this.parent().parent().find('.blacklist-reason').html('('+s+')');
						}
						$this.parent().parent().find('.processing').fadeOut(1000,function(){ $(this).remove(); });
					});
				}
				else {
					$this.parent().find('.processing').remove();
					$this.attr('checked',true);
					e.preventDefault();
				}
			});
		}
	});

		$('#reports-form').submit(function(e){
			var ok = true;
			var $form = $(this);
			$form.find(".error").remove();
			$form.find(".required").not(".select2-container").each(function(elm){
				if($.trim($(this).val())=='') {
					$('<span class="error">Campul este obligatoriu.</span>').insertAfter($(this).parent());
					ok = false;
				}
			});
			if(ok) return true;
			e.preventDefault();
		});

	/*************** forms ****************/



	$(document).on('click','.tile .controller .collapse',function (e) {
		var $el = $(this).parent().parent().parent();
		if($el.hasClass("collapsed")) {
			$el.find(".tile-body").show();
			if($(this).attr('rel')!='') $($(this).attr('rel')).show();
			$el.removeClass("collapsed");
		}
		else {
			$el.find(".tile-body").hide();
			if($(this).attr('rel')!='') $($(this).attr('rel')).hide();
			$el.addClass("collapsed");
		}
		e.preventDefault();
	});

	/*$('.dropdown-toggle').click(function () {
		$("img").trigger("unveil");
	});*/

	$(".remove-widget").click(function() {
		$(this).parent().parent().parent().addClass('animated fadeOut');
		$(this).parent().parent().parent().attr('id', 'id_a');

		//$(this).parent().parent().parent().hide();
		 setTimeout( function(){
			$('#id_a').remove();
		 },400);
	return false;
	});

	$(".create-folder").click(function() {
		$('.folder-input').show();
		return false;
	});

	$(".folder-name").keypress(function (e) {
        if(e.which == 13) {
			 $('.folder-input').hide();
			 $( '<li><a href="#"><div class="status-icon green"></div>'+  $(this).val() +'</a> </li>' ).insertBefore( ".folder-input" );
			 $(this).val('');
		}
    });

	$("#menu-collapse").click(function() {
		if($('.page-sidebar').hasClass('mini')){
			$('.page-sidebar').removeClass('mini');
			$('.page-content').removeClass('condensed-layout');
			$('.footer-widget').show();
		}
		else{
			$('.page-sidebar').addClass('mini');
			$('.page-content').addClass('condensed-layout');
			$('.footer-widget').hide();
			calculateHeight();
		}
	});

	$(".inside").children('input').blur(function(){
		$(this).parent().children('.add-on').removeClass('input-focus');
	})

	$(".inside").children('input').focus(function(){
		$(this).parent().children('.add-on').addClass('input-focus');
	})

	$(".input-group.transparent").children('input').blur(function(){
		$(this).parent().children('.input-group-addon').removeClass('input-focus');
	})

	$(".input-group.transparent").children('input').focus(function(){
		$(this).parent().children('.input-group-addon').addClass('input-focus');
	})

	$(".bootstrap-tagsinput input").blur(function(){
		$(this).parent().removeClass('input-focus');
	})

	$(".bootstrap-tagsinput input").focus(function(){
		$(this).parent().addClass('input-focus');
	})

	$('#user-messages').click(function(e) {
		if($('#user-messages-list').hasClass('visible')) {
			$('#user-messages-list').removeClass('visible').hide();
		}
		else {
			$('#user-messages-list').addClass('visible').show();
			$(this).find('.badge').removeClass('badge-important').html('0');
		}
		e.preventDefault();
    });

	$(document).mouseup(function (e) {
		var container = $("#notification-list");
		if (!container.is(e.target) // if the target of the click isn't the container...
			&& container.has(e.target).length === 0) // ... nor a descendant of the container
		{
			container.removeClass('visible').hide();
		}
	});


	$(document).on('click','.first-alert',function(e) {
		if($('#alerts-popup').hasClass('visible')) {
			$('#alerts-popup').removeClass('visible').hide();
		}
		else {
			$('#alerts-popup').addClass('visible').show();
			//$(this).find('.badge').removeClass('badge-important').html('0');
		}
		e.preventDefault();
    });

	$(document).on('click','#alerts-popup.visible',function(e) {
		$('#alerts-popup').removeClass('visible').hide();
		e.preventDefault();
    });

	$(document).mouseup(function (e) {
		var container = $("#alerts-popup");
		if (!container.is(e.target) // if the target of the click isn't the container...
			&& container.has(e.target).length === 0) // ... nor a descendant of the container
		{
			container.removeClass('visible').hide();
		}
	});
	/*
	$('#user-options').click(function(){
		$('#my-task-list').popover('hide')
	})*/
//*********************************** BEGIN CHAT POPUP*****************************
	/* $('.chat-menu-toggle').sidr({
		name:'sidr',
		side: 'right',
		complete:function(){
		}
	});*/
	$(".simple-chat-popup").click(function(){
		$(this).addClass('hide');
		$('#chat-message-count').addClass('hide');
	});

	setTimeout( function(){
		$('#chat-message-count').removeClass('hide');
		$('#chat-message-count').addClass('animated bounceIn');
		$('.simple-chat-popup').removeClass('hide');
		$('.simple-chat-popup').addClass('animated fadeIn');
	},5000);
	setTimeout( function(){
		$('.simple-chat-popup').addClass('hide');
		$('.simple-chat-popup').removeClass('animated fadeIn');
		$('.simple-chat-popup').addClass('animated fadeOut');
	},8000);

//*********************************** END CHAT POPUP*****************************

//**********************************BEGIN MAIN MENU********************************
	jQuery('.page-sidebar li > a').on('click', function (e) {
            if ($(this).next().hasClass('sub-menu') == false) {
                return;
	}
     var parent = $(this).parent().parent();


            parent.children('li.open').children('a').children('.arrow').removeClass('open');
            parent.children('li.open').children('a').children('.arrow').removeClass('active');
            parent.children('li.open').children('.sub-menu').slideUp(200);
            parent.children('li').removeClass('open');
          //  parent.children('li').removeClass('active');

            var sub = jQuery(this).next();
            if (sub.is(":visible")) {
                jQuery('.arrow', jQuery(this)).removeClass("open");
                jQuery(this).parent().removeClass("active");
                sub.slideUp(200, function () {
                    handleSidenarAndContentHeight();
                });
            } else {
                jQuery('.arrow', jQuery(this)).addClass("open");
                jQuery(this).parent().addClass("open");
                sub.slideDown(200, function () {
                    handleSidenarAndContentHeight();
                });
            }

            e.preventDefault();
        });
	//Auto close open menus in Condensed menu
		if( $('.page-sidebar').hasClass('mini'))  {
			var elem = jQuery('.page-sidebar ul');
		    elem.children('li.open').children('a').children('.arrow').removeClass('open');
            elem.children('li.open').children('a').children('.arrow').removeClass('active');
            elem.children('li.open').children('.sub-menu').slideUp(200);
            elem.children('li').removeClass('open');
		}
//**********************************END MAIN MENU********************************
//**** Element Background and height ********************************************

	$('[data-height-adjust="true"]').each(function(){
		var h = $(this).attr('data-elem-height');
		$(this).css("min-height", h);
		$(this).css('background-image', 'url(' + $(this).attr("data-background-image") + ')');
		$(this).css('background-repeat', 'no-repeat');
		if($(this).attr('data-background-image')){

		}
	})
	function equalHeight(group) {
	   tallest = 0;
	   group.each(function() {
		  thisHeight = $(this).height();
		  if(thisHeight > tallest) {
			 tallest = thisHeight;
		  }
	   });
	   group.height(tallest);
	}

	$('[data-aspect-ratio="true"]').each(function(){
		$(this).height($(this).width());
	})

	$('[data-sync-height="true"]').each(function(){
		equalHeight($(this).children());
	});

	$( window ).resize(function() {
		$('[data-aspect-ratio="true"]').each(function(){
			$(this).height($(this).width());
		})
		$('[data-sync-height="true"]').each(function(){
			equalHeight($(this).children());
		});
	});

//***********************************BEGIN Fixed Menu*****************************
	/*var eleHeight =window.screen.height;
	eleHeight=eleHeight-(eleHeight*22.5/100);
	if( !(/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent))  ) {
		$('#main-menu-wrapper').slimScroll({
			color: '#a1b2bd',
			size: '4px',
			height: eleHeight,
			alwaysVisible: false
		});
	}
	*/
//***********************************BEGIN Lazyload images*****************************
 if ($.fn.lazyload){
	$("img.lazy").lazyload({
		effect : "fadeIn"
	});
}
//***********************************BEGIN Grids*****************************
		 $('.grid .tools a.remove').on('click', function () {
            var removable = jQuery(this).parents(".grid");
            if (removable.next().hasClass('grid') || removable.prev().hasClass('grid')) {
                jQuery(this).parents(".grid").remove();
            } else {
                jQuery(this).parents(".grid").parent().remove();
            }
        });

        $('.grid .tools a.reload').on('click', function () {
            var el =  jQuery(this).parents(".grid");
            blockUI(el);
			window.setTimeout(function () {
               unblockUI(el);
            }, 1000);
        });

		$('.grid .tools .collapse, .grid .tools .expand').on('click', function () {
            var el = jQuery(this).parents(".grid").children(".grid-body");
            if (jQuery(this).hasClass("collapse")) {
                jQuery(this).removeClass("collapse").addClass("expand");
                el.slideUp(200);
            } else {
                jQuery(this).removeClass("expand").addClass("collapse");
                el.slideDown(200);
            }
        });

		$('.user-info .collapse').on('click', function () {
            jQuery(this).parents(".user-info ").slideToggle();
		});
//***********************************END Grids*****************************
		var handleSidenarAndContentHeight = function () {
        var content = $('.page-content');
        var sidebar = $('.page-sidebar');

        if (!content.attr("data-height")) {
            content.attr("data-height", content.height());
        }

        if (sidebar.height() > content.height()) {
            content.css("min-height", sidebar.height() + 120);
        } else {
            content.css("min-height", content.attr("data-height"));
        }
    }
	$('.panel-group').on('hidden.bs.collapse', function (e) {
	  $(this).find('.panel-heading').not($(e.target)).addClass('collapsed');
	})

	$('.panel-group').on('shown.bs.collapse', function (e) {
	 // $(e.target).prev('.accordion-heading').find('.accordion-toggle').removeClass('collapsed');
	})

//***********************************BEGIN Layout Readjust *****************************

/*
	$(window).setBreakpoints({
		distinct: true,
		breakpoints: [
			320,
			480,
			768,
			1024
		]
	});
	//Break point entry
	$(window).bind('enterBreakpoint320',function() {
		$('#main-menu-toggle-wrapper').show();
		$('#portrait-chat-toggler').show();
		$('#header_inbox_bar').hide();
		$('#main-menu').removeClass('mini');
		$('.page-content').removeClass('condensed');
		rebuildSider();
	});

	$(window).bind('enterBreakpoint480',function() {
		$('#main-menu-toggle-wrapper').show();
		$('.header-seperation').show();
		$('#portrait-chat-toggler').show();
		$('#header_inbox_bar').hide();
		//Incase if condensed layout is applied
		$('#main-menu').removeClass('mini');
		$('.page-content').removeClass('condensed');
		rebuildSider();
	});

	$(window).bind('enterBreakpoint800',function() {
		$('#main-menu-toggle-wrapper').show();
		$('#portrait-chat-toggler').show();
		$('#header_inbox_bar').hide();
		if( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ) {
			$('#main-menu').removeClass('mini');
			$('.page-content').removeClass('condensed');
			rebuildSider();
		}

	});
	$(window).bind('enterBreakpoint1024',function() {
		if( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ) {
			var elem = jQuery('.page-sidebar ul');
		    elem.children('li.open').children('a').children('.arrow').removeClass('open');
            elem.children('li.open').children('a').children('.arrow').removeClass('active');
            elem.children('li.open').children('.sub-menu').slideUp(200);
            elem.children('li').removeClass('open');
		}
	});

	$(window).bind('exitBreakpoint320',function() {
		$('#main-menu-toggle-wrapper').hide();
		$('#portrait-chat-toggler').hide();
		$('#header_inbox_bar').show();
		closeAndRestSider();
	});

	$(window).bind('exitBreakpoint480',function() {
		$('#main-menu-toggle-wrapper').hide();
		$('#portrait-chat-toggler').hide();
		$('#header_inbox_bar').show();
		closeAndRestSider();
	});

	$(window).bind('exitBreakpoint768',function() {
		$('#main-menu-toggle-wrapper').hide();
		$('#portrait-chat-toggler').hide();
		$('#header_inbox_bar').show();
		closeAndRestSider();
	});
	*/
//***********************************END Layout Readjust *****************************

//***********************************BEGIN Function calls *****************************
	/*function closeAndRestSider(){
	  if($('#main-menu').attr('data-inner-menu')=='1'){
		$('#main-menu').addClass("mini");
		$.sidr('close', 'main-menu');
		$.sidr('close', 'sidr');
		$('#main-menu').removeClass("sidr");
		$('#main-menu').removeClass("left");
	  }
	  else{
		$.sidr('close', 'main-menu');
		$.sidr('close', 'sidr');
		$('#main-menu').removeClass("sidr");
		$('#main-menu').removeClass("left");
	}

	}
	function rebuildSider(){
		$('#main-menu-toggle').sidr({
				name : 'main-menu',
				side: 'left'
		});
	}
	*/

  $('#main-menu-toggle').on('touchstart click', function (e) {
    e.preventDefault();
    toggleMainMenu();
  });
//***********************************END Function calls *****************************

//***********************************BEGIN Main Menu Toggle *****************************
	$('#layout-condensed-toggle').click(function(){
        $.sidr('close', 'sidr');
        if($('#main-menu').attr('data-inner-menu')=='1'){
            //Do nothing
            //console.log("Menu is already condensed");
        }
        else{
            if($('#main-menu').hasClass('mini')){
				$('body').removeClass('grey');
                $('#main-menu').removeClass('mini');
                $('.page-content').removeClass('condensed');
                $('.scrollup').removeClass('to-edge');
                $('.header-seperation').show();
                //Bug fix - In high resolution screen it leaves a white margin
                $('.header-seperation').css('height','61px');
                $('.footer-widget').show();
            }
            else{
                $('body').addClass('grey');
                $('#main-menu').addClass('mini');
                $('.page-content').addClass('condensed');
                $('.scrollup').addClass('to-edge');
                $('.header-seperation').hide();
                $('.footer-widget').hide();
            }
        }
	});

//***********************************BEGIN Function calls *****************************

//***********************************BEGIN Function calls *****************************
    function blockUI(el) {
            $(el).block({
                message: '<div class="loading-animator"></div>',
                css: {
                    border: 'none',
                    padding: '2px',
                    backgroundColor: 'none'
                },
                overlayCSS: {
                    backgroundColor: '#fff',
                    opacity: 0.3,
                    cursor: 'wait'
                }
            });
     }

     // wrapper function to  un-block element(finish loading)
     function unblockUI(el) {
            $(el).unblock();
    }

	$(window).resize(function() {
			calculateHeight();
	});

	$(window).scroll(function(){
        if ($(this).scrollTop() > 100) {
            $('.scrollup').fadeIn();
        } else {
            $('.scrollup').fadeOut();
        }
    });
//***********************************BEGIN Function calls *****************************
	$('.scrollup').click(function(){
		$("html, body").animate({ scrollTop: 0 }, 700);
		return false;
    });
	 //$("img").unveil();
	});
	/*$( window ).resize(function() {
		  $.sidr('close', 'sidr');
	});
	*/
	function calculateHeight(){
			var contentHeight=parseInt($('.page-content').height());
			if(911 > contentHeight){
				log("Small");
			}
	}

/******* utility functions **************/
function log(msg) {
	if (typeof console !== 'undefined' && typeof showLogs !='undefined' && showLogs) {
		//console.log.apply(console, arguments);
		console.log(msg);
	}
}

function searchIdInArray(id,array) {
	var idx = -1;
	$.each(array,function(index,value){
		if(value.ID==id) {
			idx = index;
			return index;
		}
	});
	return idx;
}

function searchItemInArray(id,array,label) {
	var idx = -1;
	$.each(array,function(index,value){
		if(value[label]==id) {
			idx = index;
			return index;
		}
	});
	return idx;
}

function searchXinArray(x,array) {
	var idx = -1;
	$.each(array,function(index,value){
		if(value.x==x) {
			idx = index;
			return index;
		}
	});
	return idx;
}

function fancyAlert(msg,callback) {
    $.fancybox({
        modal : true,
        content : '<div style="margin:1px;width:270px;" class="text-center">'+msg+
					'<div class="p-t-20">'+
						'<button class="btn btn-danger btn-cons" id="fancyAlert_ok">Ok</button>&nbsp;'+
					'</div>',
        afterShow : function() {
            $("#fancyAlert_ok").click(function() {
				if(callback) callback();
                else $.fancybox.close();
            }).focus();
        }
    });
}

function fancySuccess(msg,callback) {
    $.fancybox({
        modal : true,
        content : '<div style="margin:1px;width:270px;" class="text-center">'+msg+
					'<div class="p-t-20">'+
						'<button class="btn btn-primary btn-cons" id="fancyAlert_ok">Ok</button>&nbsp;'+
					'</div>',
        afterShow : function() {
            $("#fancyAlert_ok").click(function() {
				if(callback) callback();
                else $.fancybox.close();
            }).focus();
        }
    });
}

function fancyConfirm(msg,callback) {
    var ret;
    var modalFancybox = $.fancybox({
        modal : true,
        content : '<div style="margin:1px;width:270px;" class="text-center">'+msg+
					'<div class="p-t-20">'+
						'<button class="btn btn-primary btn-cons" id="fancyConfirm_ok">' + getTranslation('Yes') + '</button>&nbsp;'+
						'<button class="btn btn-cancel btn-cons" id="fancyConfirm_cancel">' + getTranslation('No') + '</button>'+
					'</div>',
        afterShow : function() {
            $("#fancyConfirm_cancel").click(function() {
                ret = false;
                $.fancybox.close();
            });
            $("#fancyConfirm_ok").click(function() {
                ret = true;
                $.fancybox.close();
            })
        },
        afterClose : function() {
            callback.call(this,ret);
        }
    });
}

function getRingStatus(status) {
    switch(status) {
		case 'Closed':
			return 'inchisa';
			break;
		case 'Opened':
			return 'deschisa';
			break;
		case 'PreOpened':
			return 'in deschidere';
			break;
		case 'PreClosed':
			return 'in inchidere';
			break;
		case 'NONE':
			return 'inchisa';
			break;
	}
}

function stickyHeader() {
	if($(window).scrollTop() > ($(".header.navbar").height()/2)) {
		$("#sticky-header").show();
	}
	else $("#sticky-header").hide();
}

function objectChanged(newObject,oldObject) {
	for(var key in newObject) {
		if(newObject[key]!=oldObject[key]) {
			return true;
		}
	}
	return false;
}

function getPrototypeData(obj,prototypeData) {
	var html = prototypeData;
	for(var key in obj) {
		html = html.replace(new RegExp("__"+key.toUpperCase()+"__",'g'),getTranslation(obj[key]));
	}
	return html;
}

var modalShown = false;
function parseResponse(action,response) {
	if(response.ResultType == 'LoginError' && !modalShown) {
		modalShown = true;
		showRelogin(response.Result);
	}
	else {
		console.log(response);
		log('error ' + action + ' ('+response.Result+')');
	}
}

function showErrors(error) {
	fancyAlert(error);
}

function showRelogin(msg) {
	fancyAlert(msg, function() {
		window.location.href = '/login';
		modalShown = false;
	});
}

function getArrayItem(array,id) {
	var item = array[searchIdInArray(id,array)];
	if(typeof item != 'undefined') return item;
	return null;
}

Date.now = Date.now || function() { return +new Date; }

// From https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Object/keys
if (!Object.keys) {
  Object.keys = (function () {
    'use strict';
    var hasOwnProperty = Object.prototype.hasOwnProperty,
        hasDontEnumBug = !({toString: null}).propertyIsEnumerable('toString'),
        dontEnums = [
          'toString',
          'toLocaleString',
          'valueOf',
          'hasOwnProperty',
          'isPrototypeOf',
          'propertyIsEnumerable',
          'constructor'
        ],
        dontEnumsLength = dontEnums.length;

    return function (obj) {
      if (typeof obj !== 'object' && (typeof obj !== 'function' || obj === null)) {
        throw new TypeError('Object.keys called on non-object');
      }

      var result = [], prop, i;

      for (prop in obj) {
        if (hasOwnProperty.call(obj, prop)) {
          result.push(prop);
        }
      }

      if (hasDontEnumBug) {
        for (i = 0; i < dontEnumsLength; i++) {
          if (hasOwnProperty.call(obj, dontEnums[i])) {
            result.push(dontEnums[i]);
          }
        }
      }
      return result;
    };
  }());
}

function getTranslation(label) {
  if(typeof MyDictionary != 'undefined') {
    return MyDictionary.getValue(label);
  }
	if(typeof translations != 'undefined' && typeof translations[label] != 'undefined') {
		return decodeHtml(translations[label]);
	}
	return label;
}

var txtDecodeElm = document.createElement("textarea");

function decodeHtml(html) {
    txtDecodeElm.innerHTML = html;
    return txtDecodeElm.value;
}

function localeNumber(val,dec) {
	if( typeof $.number != 'undefined') {
		aDec = ',';
		aSep = '.';
		if(currentLang == 'EN') {
			aDec = '.';
			aSep = ',';
		}
		return $.number(val,typeof dec != 'undefined' ? dec : 0 , aDec, aSep);
	}
	else return val;
}

function toggleMainMenu(){
  var timer;
  if($('body').hasClass('open-menu-left')){
    $('body').removeClass('open-menu-left');
    timer= setTimeout(function(){
      $('.page-sidebar').removeClass('visible');
    }, 50);

  }
  else{
    clearTimeout(timer);
    $('.page-sidebar').addClass('visible');
    setTimeout(function(){
      $('body').addClass('open-menu-left');
    }, 50);

  }
}
