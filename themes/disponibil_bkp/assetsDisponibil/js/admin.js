$(document).ready(function(){
	$('body').on('click',".delete-confirm",function(e){
		e.preventDefault();
		var url = $(this).attr('href');
		fancyConfirm('Esti sigur ca vrei sa stergi inregistrarea?',function(r){
			if(r) window.location.href = url;
		});
	});
	var startDate = new Date();
	$('#prm_StartDate').datepicker({
		dateFormat:'yyyy-mm-dd',
		/*startDate: startDate,*/
		autoclose:true,
		orientation:'auto top'
	}).on('changeDate', function(selected){
		startDate = new Date(selected.date.valueOf());
		startDate.setDate(startDate.getDate(new Date(selected.date.valueOf())));
		$('#prm_EndDate').datepicker('setStartDate', startDate);
		$('#ExecutionDate').datepicker('setStartDate', startDate);
	});
	$('#prm_EndDate').datepicker({
		dateFormat:'yyyy-mm-dd',
		startDate: startDate,
		autoclose: true,
		orientation:'auto top'
	}).on('changeDate', function(selected){
		endDate = new Date(selected.date.valueOf());
		endDate.setDate(endDate.getDate(new Date(selected.date.valueOf())));
		$('#prm_StartDate').datepicker('setEndDate', endDate);
		//$('#ExecutionDate').datepicker('setEndDate', endDate);
	});
	$('#StartDate').datepicker({
		dateFormat:'yyyy-mm-dd',
		autoclose:true,
		orientation:'auto top'
	});
	$('#EndDate').datepicker({
		dateFormat:'yyyy-mm-dd',
		autoclose: true,
		orientation:'auto top'
	});
	$('#ExecutionDate').datepicker({
		dateFormat:'yyyy-mm-dd',
		autoclose: true,
		startDate: $('#prm_StartDate').val()!='' ? $('#prm_StartDate').val() : null,
		orientation:'auto top'
	});
	$(".list-filter").on('change',function(e){
		window.location.href = $(this).attr('data-url')+$(this).val();
	});
	var date = new Date();
	$('#order-Date').datepicker({
		dateFormat:'dd M yyyy',
		startDate:date,
		autoclose:true,
		orientation:'auto top'
	});
	$('.timepicker-24').timepicker({
		minuteStep: 1,
		showSeconds: true,
		showMeridian: false,
		defaultTime: false,
		template: false
	});
	$('.timepicker').timepicker({
		minuteStep: 1,
		showSeconds: false,
		showMeridian: false,
		defaultTime: false,
		template: false
		//disableFocus: true
	});
	$(".timepicker").keydown(function (e) {
		var key = e.charCode || e.keyCode || 0;
            // allow backspace, tab, delete, enter, arrows, numbers and keypad numbers ONLY
            // home, end, period, and numpad decimal
            return (
                key == 8 ||
                key == 9 ||
                key == 46 ||
                key == 110 ||
                (key >= 35 && key <= 40) ||
                (key >= 48 && key <= 57) ||
                (key >= 96 && key <= 105));
    });
	//$('.timepicker').on('focus',function(e){
		//$(this).timepicker('showWidget');
	//});
	if($("#order-Time").val()=='') {
		$("#order-Time").val('23:59:00');
	}

/*
	$("#isSpotContract").change(function(e){
		if($(this).is(":checked")) {
			$("#SpotQuotation").prop('disabled',false);
		}
		else {
			$("#SpotQuotation").prop('disabled',true);
		}
	});
	if($("#isSpotContract").is(":checked")) {
		$("#SpotQuotation").prop('disabled',false);
	}
	else {
		$("#SpotQuotation").prop('disabled',true);
	}
	*/
	$("#hasSchedule").change(function(e){
		if($(this).is(":checked")) {
			$("#ring-schedule").show();
		}
		else {
			$("#ring-schedule").hide();
		}
	});
	$("input[name='priceValidation']").change(function(e){
		if($(this).val()=='min-max') {
			$("#price-limits-box").show();
			$("#price-variation-box").hide();
			$("#MaxPriceVariation").val('');
		}
		else {
			$("#price-limits-box").hide();
			$("#price-variation-box").show();
			$("#MinPrice").val('');
			$("#MaxPrice").val('');
		}
	});
	$("#ID_Agency_none").change(function(e){
		if($(this).is(":checked")) {
			$("#internal_user").hide();
			$(".role.internal_user").show();
			$(".role").not(".internal_user").hide();
			$(".role").not(".internal_user").find('input[type="checkbox"]').prop('checked',false);
		}
		else {
			$("#internal_user").show();
			$(".role.internal_user").hide();
			$(".role.internal_user").find('input[type="checkbox"]').prop('checked',false);
			$(".role").not(".internal_user").show();
		}
	});
	$("#isBroker").change(function(e){
		if($(this).is(":checked")) {
			$("#toggleBroker").show();
		}
		else {
			$("#toggleBroker").hide();
		}
	});
	if($("#hasSchedule").is(":checked")) {
		$("#ring-schedule").show();
	}
	else {
		$("#ring-schedule").hide();
	}
	if($("#ID_Agency_none").is(":checked")) {
		$("#internal_user").hide();
		$(".role.internal_user").show();
		$(".role").not(".internal_user").hide();
	}
	else {
		$("#internal_user").show();
		$(".role.internal_user").hide();
		$(".role").not(".internal_user").show();
	}

	function resetDropdown($dropdown,values,attr,filter_value,prop) {
		console.log('reset dropdown');
		if($dropdown.hasClass('select2')) {
			$dropdown.select2('destroy');
		}
		$dropdown.html('');
		for(var i=0;i<values.length;i++) {
			if(values[i] && values[i][attr]==filter_value) {
				$dropdown.append('<option value="'+values[i].ID+'">'+getTranslation(values[i][prop])+'</option>');
			}
		}
		$dropdown.prop('selectedIndex',-1);
		if($dropdown.hasClass('select2')) {
			$dropdown.select2();
		}
	}

	var ring = $("#ID_Ring").val();
	if(ring && $("#ID_AssetType").length>0 && $("#ID_AssetType").attr('data-new')=='1') {
		resetDropdown($("#ID_AssetType"),assetTypes,'ID_Ring',ring,'Name');
	}
	if(ring && $("#ID_Asset").length>0 && $("#ID_Asset").attr('data-new')=='1') {
		resetDropdown($("#ID_Asset"),assets,'ID_Ring',ring,'Name');
	}

	if($("#ValueInRON").length>0) {
		updateValueInRon();
		$("#ExchangeRate").change(function(e){
			updateValueInRon();
		});
		$("#ValueInCurrency").change(function(e){
			updateValueInRon();
		});
	}

	function updateValueInRon() {
		if($("#ExchangeRate").val() && $("#ValueInCurrency").val()) {
			if(currentLang == 'EN') {
				var val = $("#ValueInCurrency").val().replace(/\,/g,'')*1
				var cur = $("#ExchangeRate").val().replace(/\,/g,'')*1
			}
			else {
				var val = $("#ValueInCurrency").val().replace(',','.')*1;
				var cur = $("#ExchangeRate").val().replace(',','.')*1;
			}

			$("#ValueInRON").val(val*cur);
		}
	}

	$("#ID_Ring").on('change',function(e){
		var val = $(this).val();
		$(this).parents('form').find('.error').remove();
		if(val && $("#ID_AssetType").length>0 && typeof assetTypes != 'undefined') {
			resetDropdown($("#ID_AssetType"),assetTypes,'ID_Ring',val,'Name');
		}
		if(val && $("#ID_Asset").length>0 && typeof assets != 'undefined') {
			resetDropdown($("#ID_Asset"),assets,'ID_Ring',val,'Name');
		}
		//var idx = -1;
		//var idx2 = -1;
		/*
		var options1 = assetTypeOptions.clone().filter(function(){
			if($(this).attr('data-ring')==val) {
				if(idx==-1) idx = $(this).index();
				return true;
			}
			else return false;
		});
		$('#ID_AsseetType').html(options1);
		*/
		/*
		$("#ID_AssetType option").each(function(elm){
			if($(this).attr('data-ring')==val) {
				if(idx==-1) idx = elm;
				$(this).show();
			}
			else $(this).hide();
		});
		$("#ID_Asset option").each(function(elm){
			if($(this).attr('data-ring')==val) {
				if(idx2==-1) idx2 = elm;
				$(this).show();
			}
			else $(this).hide();
		});
		$('#ID_AssetType').prop('selectedIndex',idx);
		$('#ID_Asset').prop('selectedIndex',idx2);
		*/
		//if($("#ID_AssetType").length>0 && $("#ID_AssetType").attr('data-inherit')=='1' && $("#ID_AssetType").val()!='' && $("#ID_Asset").val()=='') {
		if($("#ID_AssetType").length>0 && $("#ID_AssetType").val()!='' && $("#ID_Asset").val()=='') {
			var asset = getArrayItem(assetTypes,$("#ID_AssetType").val());
			if(asset) {
				$("#Name_EN").val(asset.Name_EN);
				$("#Name_RO").val(asset.Name_RO);
				$("#Name").val(asset.Name);
				//$("#Code").val(asset.Code);
				$("#ID_MeasuringUnit").val(asset.ID_MeasuringUnit).trigger("change");
				$("#ID_Currency").val(asset.ID_Currency).trigger("change");
				$("#ID_PaymentCurrency").val(asset.ID_Currency).trigger("change");
			}
		}
	});
	var agency = $("#ID_Agency").val();
	if(agency && $("#ID_Client").length>0 && $("#ID_Client").attr('data-new')=='1') {
		resetDropdown($("#ID_Client"),clients,'ID_Agency',agency,'ClientName');
	}
	/*
	$("#ID_Client option").each(function(elm){
		if($(this).attr('data-agency')==agency) {
			if(idx_client==-1) idx_client = elm;
			$(this).show();
		}
		else $(this).hide();
	});
	$('#ID_Client').prop('selectedIndex',idx_client);
	*/

	$("#ID_Agency").on('change',function(e){
		var val = $(this).val();
		if(val && $("#ID_Client").length>0) {
			resetDropdown($("#ID_Client"),clients,'ID_Agency',val,'ClientName');
		}
	});

	$("#ID_AssetType").on('change',function(e){
		$(this).parents('form').find('.error').remove();
		//if($(this).attr('data-inherit')=='1' && $(this).val()!='' && $("#ID_Asset").val()=='') {
		if($(this).val()!='' && $("#ID_Asset").val()=='') {
			var asset = assetTypes[searchIdInArray($("#ID_AssetType").val(),assetTypes)];
			if(asset) {
				$("#Name_EN").val(asset.Name_EN);
				$("#Name_RO").val(asset.Name_RO);
				//$("#Code").val(asset.Code);
				$("#ID_MeasuringUnit").val(asset.ID_MeasuringUnit).trigger("change");
				$("#ID_Currency").val(asset.ID_Currency).trigger("change");
				$("#ID_PaymentCurrency").val(asset.ID_Currency).trigger("change");
			}
		}
	});

	//if($("#ID_AssetType").length>0 && $("#ID_AssetType").attr('data-inherit')=='1' && $("#ID_AssetType").val()!='' && $("#ID_Asset").val()=='') {
	if($("#ID_AssetType").length>0 && $("#ID_AssetType").val()!='' && $("#ID_Asset").val()=='') {
		console.log('update asset from asset type');
		var asset = assetTypes[searchIdInArray($("#ID_AssetType").val(),assetTypes)];
		if(asset) {
			$("#Name_EN").val(asset.Name_EN);
			$("#Name_RO").val(asset.Name_RO);
			//$("#Code").val(asset.Code);
			$("#ID_MeasuringUnit").val(asset.ID_MeasuringUnit).trigger("change");
			$("#ID_Currency").val(asset.ID_Currency).trigger("change");
			$("#ID_PaymentCurrency").val(asset.ID_Currency).trigger("change");
		}
	}

	$("#AuctionType").on('change',function(e){
		if($(this).val()=='simple') {
			$("#step-initial-order").show();
			$("#spot-contract").hide();
		}
		else {
			$("#step-initial-order").hide();
			$("#spot-contract").show();
		}
		var x = 1;
		$(".wizard-steps li .step").each(function(elm){
			if($(this).closest('li').is(":visible")) $(this).html(x++);
		});
	});
	var x = 1;
	$(".wizard-steps li .step").each(function(elm){
		if($(this).closest('li').is(":visible")) $(this).html(x++);
	});

	$("#isInitial").on('change',function(e){
		if($(this).is(":checked")) $("#restrictions").show();
		else $("#restrictions").hide();
	});
	$("#DifferentialPriceAllowed").on('change',function(e){
		if($(this).is(":checked")) $("#DifferentialPriceText-holder").show();
		else $("#DifferentialPriceText-holder").hide();
	});
    $('.tabs-nav a').click(function (e) {
		e.preventDefault();
		$(this).tab('show');
	});
	$(".agency2ring").change(function(e){
		var $this = $(this);
		$this.parent().find('label').append('<i class="processing fa fa-spinner fa-spin"></i>');
		$.post('/admin/agencies/setAgency2Ring',{agency_id: $this.attr('agency-id'),ring_id:$this.attr('data-id'), checked:$this.is(':checked')},function(data,textStatus){
			if(!data.Success) {
				var msg = '';
				if(data.ResultType=='GeneralError' || data.ResultType=='String') {
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
	});

	$(document).on('change',".client2agency",function(e){
		var $this = $(this);
		$this.parent().find('label').append('<i class="processing fa fa-spinner fa-spin"></i>');
		$.post('/admin/agencies/setClient2Agency',{agency_id: $this.attr('agency-id'),client_id:$this.attr('data-id'), checked:$this.is(':checked')},function(data,textStatus){
			if(!data.Success) {
				var msg = '';
				if(data.ResultType=='GeneralError' || data.ResultType=='String') {
					msg = data.Result;
				}
				$this.parent().parent().find('.processing').remove();
				$this.parent().parent().append('<i class="processing processing-bad fa fa-exclamation-triangle"></i>');
				$this.prop('checked',false);
				fancyAlert(msg);
			}
			else if(data.Success) {
				$this.parent().parent().find('.processing').remove();
				$this.parent().parent().append('<i class="processing processing-ok fa fa-check"></i>');
				if($this.closest('table').attr('id')=='agency-clients-table') {
					var agencyClientsTable = $("#agency-clients-table").data('footable');
					$this.closest('tr').appendTo("#clients-table");
					agencyClientsTable.redraw();
				}
				else {
					var clientsTable = $("#clients-table").data('footable');
					var agencyClientsTable = $("#agency-clients-table").data('footable');
					//console.log(tr.index());
					//var row = tables["clients-table"].fnDeleteRow(tr.index(),null,true);
					//tr.remove();
					$this.closest('tr').appendTo("#agency-clients-table");
					//footable.removeRow(tr);
					clientsTable.redraw();
					agencyClientsTable.redraw();
					//tables["clients-table"].fnClearTable();
					//tables["clients-table"].fnDraw();
					//tr.remove();
					//console.log(row);
					//tables["clients-table"].fnDraw();
					//row.hide();
				}
			}
			$this.parent().parent().find('.processing').fadeOut(1000,function(){ $(this).remove(); });
		});
	});

	$(document).on('change',".client2ringCanBuy,.client2ringCanSell",function(e){
		var $this = $(this);
		var clientID = $(this).attr('data-id');
		var ringID = $(this).attr('data-ring-id');
		var clientCanSell = $("#canSell_" + clientID).is(':checked');
		var clientCanBuy = $("#canBuy_" + clientID).is(':checked');
		$this.parent().find('label').append('<i class="processing fa fa-spinner fa-spin"></i>');
		//$.post('/admin/rings/setClient2Ring',{client_id: $this.attr('data-clientID'),ring_id:$this.attr('data-ringID'),canBuy:$this.is(':checked'),canSell:$("#canSell_" + clientID).is(':checked')},function(data,textStatus){
		$.post('/admin/rings/setClient2Ring',{client_id: clientID,ring_id:ringID,canBuy:clientCanBuy,canSell:clientCanSell},function(data,textStatus){
			if(!data.Success) {
				var msg = '';
				if(data.ResultType=='GeneralError' || data.ResultType=='String') {
					msg = data.Result;
				}
				$this.parent().parent().find('.processing').remove();
				fancyAlert(msg);
			}
			else if(data.Success) {
				$this.parent().parent().find('.processing').remove();
				if($this.closest('table').attr('id')=='ring-clients-table') {
					if(!clientCanSell && !clientCanBuy) {
						var ringClientsTable = $("#ring-clients-table").data('footable');
						$this.closest('tr').appendTo("#clients-table");
						ringClientsTable.redraw();
					}
				}
				else {
					if(clientCanSell || clientCanBuy) {
						var clientsTable = $("#clients-table").data('footable');
						var ringClientsTable = $("#ring-clients-table").data('footable');
						$this.closest('tr').appendTo("#ring-clients-table");
						clientsTable.redraw();
						ringClientsTable.redraw();
					}
				}
			}
			$this.parent().parent().find('.processing').fadeOut(1000,function(){ $(this).remove(); });
		});
	});

	$(document).on('change',".client2assetCanBuy,.client2assetCanSell",function(e){
	console.log('hello');
		var $this = $(this);
		var clientID = $(this).attr('data-id');
		var assetID = $(this).attr('data-asset-id');
		var clientCanSell = $("#canSell_" + clientID).is(':checked');
		var clientCanBuy = $("#canBuy_" + clientID).is(':checked');
		$this.parent().find('label').append('<i class="processing fa fa-spinner fa-spin"></i>');
		$.post('/admin/rings/setClient2Asset',{client_id: clientID,asset_id:assetID,canBuy:clientCanBuy,canSell:clientCanSell},function(data,textStatus){
			console.log('bubu');
			if(!data.Success) {
				var msg = '';
				if(data.ResultType=='GeneralError' || data.ResultType=='String') {
					msg = data.Result;
				}
				$this.parent().parent().find('.processing').remove();
				fancyAlert(msg);
			}
			else if(data.Success) {
				$this.parent().parent().find('.processing').remove();
				if($this.closest('table').attr('id')=='asset-clients-table') {
					if(!clientCanSell && !clientCanBuy && !$this.hasClass('sticky')) {
						var assetClientsTable = $("#asset-clients-table").data('footable');
						$this.closest('tr').appendTo("#clients-table");
						assetClientsTable.redraw();
						$this.closest('tr').removeClass('row_selected');
					}
					else if(!clientCanSell && !clientCanBuy) {
						$this.closest('tr').addClass('row_selected');
					}
					else {
						$this.closest('tr').removeClass('row_selected');
					}
				}
				else {
					if(clientCanSell || clientCanBuy) {
						var clientsTable = $("#clients-table").data('footable');
						var assetClientsTable = $("#asset-clients-table").data('footable');
						$this.closest('tr').appendTo("#asset-clients-table");
						clientsTable.redraw();
						assetClientsTable.redraw();
					}
				}
			}
			$this.parent().parent().find('.processing').fadeOut(1000,function(){ $(this).remove(); });
		});
	});

	$(document).on('change',".setWarrantyType2Ring",function(e){
		var $this = $(this);
		var warrantyType = $(this).attr('data-id');
		var ringID = $(this).attr('data-ring-id');
		var isSet = $("#set_" + warrantyType).is(':checked');
		$this.parent().find('label').append('<i class="processing fa fa-spinner fa-spin"></i>');
		$.post('/admin/rings/setWarrantyType2Ring',{ring_id: ringID,warrantytype_id:warrantyType,isSet:isSet},function(data,textStatus){
			if(!data.Success) {
				var msg = '';
				if(data.ResultType=='GeneralError' || data.ResultType=='String') {
					msg = data.Result;
				}
				$this.parent().parent().find('.processing').remove();
				fancyAlert(msg);
			}
			else if(data.Success) {
				$this.parent().parent().find('.processing').remove();
				if($this.closest('table').attr('id')=='ring-warrantytypes-table') {
					console.log('ring-warrantytypes-table');
					//if(!clientCanSell && !clientCanBuy) {
						var ringWarrantyTypesTable = $("#ring-warrantytypes-table").data('footable');
						$this.closest('tr').appendTo("#warrantytypes-table");
						ringWarrantyTypesTable.redraw();
					//}
				}
				else {
					//if(clientCanSell || clientCanBuy) {
						console.log('warrantytypes-table');
						var warrantytypesTable = $("#warrantytypes-table").data('footable');
						var ringWarrantyTypesTable = $("#ring-warrantytypes-table").data('footable');
						$this.closest('tr').appendTo("#ring-warrantytypes-table");
						warrantytypesTable.redraw();
						ringWarrantyTypesTable.redraw();
					//}
				}
			}
			$this.parent().parent().find('.processing').fadeOut(1000,function(){ $(this).remove(); });
		});
	});

	$(".setWarrantyType2RingPriority").click(function(e){
		var rowCount = $('.warrantyTypesPriority tr').length;
		var $this = $(this);
		var ID_WarrantyType = $(this).attr('data-id_warrantytype');
		var ID_Ring = $(this).attr('data-id_ring');
		var Direction = $(this).attr('data-direction');
		var CurrentPriority = $(this).attr('data-currentpriority');

		if((CurrentPriority == 1) && (Direction == "up")) return;
		if((CurrentPriority == rowCount) && (Direction == "down")) return;

		var next = 0;
		var offset = 0;
		if(Direction == "up")
		{
			next = CurrentPriority - 1;
			offset = -1;
		}
		else if (Direction == "down")
		{
			next = CurrentPriority + 1;
			offset = 1;
		}
		var currentPosition = $('.warrantyTypesPriority tr').eq(CurrentPriority);
		var nextPosition = $('.warrantyTypesPriority tr').eq(next);



		console.log(CurrentPriority);
		$this.parent().find('label').append('<i class="processing fa fa-spinner fa-spin"></i>');
		$.post('/admin/rings/setWarrantyType2RingPriority',{ID_WarrantyType: ID_WarrantyType,ID_Ring:ID_Ring,Offset:offset},function(data,textStatus){
			if(!data.Success) {
				var msg = '';
				if(data.ResultType=='GeneralError' || data.ResultType=='String') {
					msg = data.Result;
				}
				$this.parent().parent().find('.processing').remove();
				fancyAlert(msg);
			}
			else if(data.Success) {
				console.log("currentPosition: " + CurrentPriority + " nextPosition:" + next);
				if(Direction == "up")
					currentPosition.after(nextPosition);
				else if(Direction == "down")
					nextPosition.after(currentPosition);
			}
			$this.parent().parent().find('.processing').fadeOut(1000,function(){ $(this).remove(); });
		});
	});

	$(document).on('change',".setWarrantyType2Asset",function(e){
		var $this = $(this);
		var warrantyType = $(this).attr('data-id');
		var assetID = $(this).attr('data-asset-id');
		var isSet = $("#set_" + warrantyType).is(':checked');
		$this.parent().find('label').append('<i class="processing fa fa-spinner fa-spin"></i>');
		$.post('/admin/rings/setWarrantyType2Asset',{asset_id: assetID,warrantytype_id:warrantyType,isSet:isSet},function(data,textStatus){
			if(!data.Success) {
				var msg = '';
				if(data.ResultType=='GeneralError' || data.ResultType=='String') {
					msg = data.Result;
				}
				$this.parent().parent().find('.processing').remove();
				fancyAlert(msg);
			}
			else if(data.Success) {
			}
			$this.parent().parent().find('.processing').fadeOut(1000,function(){ $(this).remove(); });
		});
	});

	$(".setWarrantyType2AssetPriority").click(function(e){
		var rowCount = $('.warrantyTypesPriority tr').length;
		var $this = $(this);
		var ID_WarrantyType = $(this).attr('data-id_warrantytype');
		var ID_Asset = $(this).attr('data-id_asset');
		var Direction = $(this).attr('data-direction');
		var CurrentPriority = $(this).attr('data-currentpriority');

		if((CurrentPriority == 1) && (Direction == "up")) return;
		if((CurrentPriority == rowCount) && (Direction == "down")) return;

		var next = 0;
		var offset = 0;
		if(Direction == "up")
		{
			next = CurrentPriority - 1;
			offset = -1;
		}
		else if (Direction == "down")
		{
			next = CurrentPriority + 1;
			offset = 1;
		}

		var currentPosition = $('.warrantyTypesPriority tr').eq(CurrentPriority);
		var nextPosition = $('.warrantyTypesPriority tr').eq(next);


		console.log(CurrentPriority);
		$this.parent().find('label').append('<i class="processing fa fa-spinner fa-spin"></i>');
		$.post('/admin/assets/setWarrantyType2AssetPriority',{ID_WarrantyType: ID_WarrantyType,ID_Asset:ID_Asset,Direction:Direction,Offset:offset},function(data,textStatus){
			if(!data.Success) {
				var msg = '';
				if(data.ResultType=='GeneralError' || data.ResultType=='String') {
					msg = data.Result;
				}
				$this.parent().parent().find('.processing').remove();
				fancyAlert(msg);
			}
			else if(data.Success) {
				console.log("currentPosition: " + CurrentPriority + " nextPosition:" + next);
				if(Direction == "up")
					currentPosition.after(nextPosition);
				else if(Direction == "down")
					nextPosition.after(currentPosition);
			}
			$this.parent().parent().find('.processing').fadeOut(1000,function(){ $(this).remove(); });
		});
	});

	$(".confirmTransaction").click(function(e){
		var $this = $(this);
		var ID_Transaction = $(this).attr('data-id_transaction');
		var ID_Buyer = $(this).attr('data-id_buyer');
		var ID_Seller = $(this).attr('data-id_seller');

		$this.parent().find('label').append('<i class="processing fa fa-spinner fa-spin"></i>');
		$.post('/admin/transactions/confirmTransaction',{ID_Transaction: ID_Transaction,ID_Buyer:ID_Buyer,ID_Seller:ID_Seller},function(data,textStatus){
			if(!data.Success) {
				var msg = '';
				if(data.ResultType=='GeneralError' || data.ResultType=='String') {
					msg = data.Result;
				}
				$this.parent().parent().find('.processing').remove();
				fancyAlert(msg);
			}
			else if(data.Success) {
				$this.removeClass('confirmTransaction');
				$this.removeClass('btn-primary');
				$this.addClass('btn-success');
			}
			$this.parent().parent().find('.processing').fadeOut(1000,function(){ $(this).remove(); });
		});
	});

	$('#clients-modal').on('shown.bs.modal', function (event) {
		var modal = $(this);
		var selected = [];
		$(".agency-clients").each(function(){
			selected.push($(this).attr('data-id'));;
		});
		modal.find('.client2agency').each(function(elm){
			if(selected.indexOf($(this).attr('data-id'))>-1) $(this).closest('tr').hide();
			else $(this).closest('tr').show();
		});
		var clientsTable = $("#clients-table").data('footable');
		clientsTable.redraw();

		//console.log()
		//tables["clients-table"].fnAdjustColumnSizing();
	});
	if(window.location.hash && window.location.hash.length>0) {
		$(".nav.nav-tabs li").removeClass('active');
		$('.nav.nav-tabs li a[href="'+window.location.hash+'"]').parent().addClass('active');
		$(".tab-content .tab-pane").removeClass('active');
		$(window.location.hash).addClass('active');
	}
	$(".nav.nav-tabs li a").on('shown.bs.tab', function(e) {
		window.location.hash = $(this).attr('href');
	});

	$(document).on('change',".user2ring",function(e){
		var $this = $(this);
		var userID = $(this).attr('data-id');
		var ringID = $(this).attr('data-ring-id');
		var isAllowed = $("#ring_user_" + userID).is(':checked');
		$this.parent().find('label').append('<i class="processing fa fa-spinner fa-spin"></i>');
		//$.post('/admin/rings/setClient2Ring',{client_id: $this.attr('data-clientID'),ring_id:$this.attr('data-ringID'),canBuy:$this.is(':checked'),canSell:$("#canSell_" + clientID).is(':checked')},function(data,textStatus){
		$.post('/admin/rings/setRingAdministrator',{ID_User: userID,ID_Ring:ringID,isAllowed:isAllowed},function(data,textStatus){
			if(!data.Success) {
				var msg = '';
				if(data.ResultType=='GeneralError' || data.ResultType=='String') {
					msg = data.Result;
				}
				$this.parent().parent().find('.processing').remove();
				fancyAlert(msg);
			}
			else if(data.Success) {
				$this.parent().parent().find('.processing').remove();
			}
			$this.parent().parent().find('.processing').fadeOut(1000,function(){ $(this).remove(); });
		});
	});

	// manual transactions
	$(document).on('change',".transactions_asset",function(e){
		var $this = $(this);
		var assetID = $this.val();
		$.get('/admin/asset_schedules/json',{asset: assetID},function(data){
			var params = data.Rows[0];

			var preOpeningTime = params.PreOpeningTime;
			var openingTime = params.OpeningTime;
			var preClosingTime = params.PreClosingTime;
			var closingTime = params.ClosingTime;
			var data = params.EndDate;

			$("#PreOpeningTime").val(preOpeningTime);
			$("#OpeningTime").val(openingTime);
			$("#PreClosingTime").val(preClosingTime);
			$("#ClosingTime").val(closingTime);

			$("#Data").val(data);
			$("#ClosingDate").val(data);
			var ringID = 0;
			$(".transactions_asset option").each(function(){
				var id = $(this).val();
				if (id == assetID){
					//console.log('idring ' + $(this).attr('idring'));
					ringID = $(this).attr('idring');
					$("#ID_Ring").val(ringID);
				}
			});
			if (ringID == 0) return;
			$.get('/admin/rings/asset_clients_json',{asset: assetID},function(data){
				console.log(data.Rows);
				var clients = data.Rows;
				setClients(clients);
				$.get('/order',{ID_Ring: ringID, ID_Asset: assetID},function(data){
					if(data.Success == true && data.ResultType == "Array")
					{
						var orders = data.Result;
						setClientOrders("IDOrderBuy", orders);
						setClientOrders("IDOrderSell", orders);
					}
				});
			});
		});
	});

	$("input[name=NewOrderBuy]").change(function(){
		if ($(this).val() == 1){
			$("#OrderBuyQuantity").show();
			$("#IDOrderBuy").hide();
		}else{
			$("#OrderBuyQuantity").hide();
			$("#IDOrderBuy").show();
		}
	});

	$("input[name=NewOrderSell]").change(function(){
		if ($(this).val() == 1){
			$("#OrderSellQuantity").show();
			$("#IDOrderSell").hide();
		}else{
			$("#OrderSellQuantity").hide();
			$("#IDOrderSell").show();
		}
	});

	$("#IDOrderBuy").change(1000, function(){
		var price = $('option:selected', this).attr('price');
		$("#OrderBuyPrice").val(price);

		var quantity = $('option:selected', this).attr('quantity');
		$("#IDOrderBuyQuantity").val(quantity);
	});

	$("#IDOrderSell").change(function(){
		var price = $('option:selected', this).attr('price');
		$("#OrderSellPrice").val(price);

		var quantity = $('option:selected', this).attr('quantity');
		$("#IDOrderSellQuantity").val(quantity);
	});

	$('#ID_BuyClient').change(function(){
		var id_agency = $('option:selected', this).attr('id_agency');
		$("#ID_AgencyBuy").val(id_agency);
	});

	$('#ID_SellClient').change(function(){
		var id_agency = $('option:selected', this).attr('id_agency');
		$("#ID_AgencySell").val(id_agency);
	});

	$('#ID_Asset').trigger('change');

	var date = new Date();
	$('.date').datepicker({
		dateFormat:'dd M yyyy',
		startDate:date,
		autoclose:true,
		orientation:'auto top'
	});
	/*
	$('.timepicker-24').timepicker({
		minuteStep: 1,
		showSeconds: true,
		showMeridian: false
	});
	$('.timepicker').timepicker({
		minuteStep: 1,
		showSeconds: false,
		showMeridian: false,
		defaultTime: false,
		disableFocus: true
	});
	$('.timepicker').on('focus',function(e){
		$(this).timepicker('showWidget');
	});
	*/
	if($(".timepicker-24").val()=='') {
		$(".timepicker-24").val('23:59:00');
	}
	// end manual transactions

});

function setClients(clients){
	$('#ID_BuyClient')[0].options.length = 0;
	$('#ID_SellClient')[0].options.length = 0;
	for(var i = 0; i < clients.length; i++){
		console.log(clients[i]);
		var canBuy = clients[i]['canBuy'];
		var canSell = clients[i]['canSell'];
		if(canBuy)
			$("#ID_BuyClient").append($('<option>', {
			    value: clients[i]["ID_Client"],
			    text: clients[i]["Name"],
			    'id_agency': clients[i]["ID_Agency"]
			}));

		if(canSell)
			$("#ID_SellClient").append($('<option>', {
			    value: clients[i]["ID_Client"],
			    text: clients[i]["Name"],
			    'id_agency': clients[i]["ID_Agency"]
			}));
	}
	$('#ID_BuyClient').trigger('change');
	$('#ID_SellClient').trigger('change');
}

function setClientOrders(selectorID, orders){
	$('#' + selectorID)[0].options.length = 0;

	var selectedClient = 0;
	switch(selectorID){
		case "IDOrderBuy": selectedClient = $("#ID_BuyClient").val(); break;
		case "IDOrderSell": selectedClient = $("#ID_SellClient").val(); break;
	}

	$("#" + selectorID).append($('<option>', {
		value: -1,
		text: ""
	}));

	for(var i = 0; i < orders.length; i++){
		var orderDirection = orders[i]["Direction"];
		var orderClient = orders[i]["ID_Client"];
		if(selectedClient == orderClient){
			$("#" + selectorID).append($('<option>', {
			    value: orders[i]["ID"],
			    text: orders[i]["Quantity"],
			    'price': orders[i]["Price"],
			    'quantity': orders[i]["Quantity"]
			}));
		}
	}
	$("#"+selectorID).change();
}

function addZero(i) {
    if (i < 10) {
        i = "0" + i;
    }
    return i;
}

$(function () {
	$('.footable').footable();
});
