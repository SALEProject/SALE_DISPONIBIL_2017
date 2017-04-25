/* Set the defaults for DataTables initialisation */
$.extend( true, $.fn.dataTable.defaults, {
	"sDom": "<'row-fluid'<'span6'l><'span6'f>r>t<'row-fluid'<'span12'p i>>",
	"sPaginationType": "bootstrap",
	"oLanguage": {
		"sLengthMenu": "_MENU_",
        "sEmptyTable":     '<p class="text-center">Nici o inregistrare.</p>',
		"sInfo": "De la <b>_START_ la _END_</b> din _TOTAL_",
		"sSearch": "Filtreaza"
	}
} );


/* Default class modification */
$.extend( $.fn.dataTableExt.oStdClasses, {
	"sWrapper": "dataTables_wrapper form-inline"
} );


/* API method to get paging information */
$.fn.dataTableExt.oApi.fnPagingInfo = function ( oSettings )
{
	return {
		"iStart":         oSettings._iDisplayStart,
		"iEnd":           oSettings.fnDisplayEnd(),
		"iLength":        oSettings._iDisplayLength,
		"iTotal":         oSettings.fnRecordsTotal(),
		"iFilteredTotal": oSettings.fnRecordsDisplay(),
		"iPage":          oSettings._iDisplayLength === -1 ?
			0 : Math.ceil( oSettings._iDisplayStart / oSettings._iDisplayLength ),
		"iTotalPages":    oSettings._iDisplayLength === -1 ?
			0 : Math.ceil( oSettings.fnRecordsDisplay() / oSettings._iDisplayLength )
	};
};


/* Bootstrap style pagination control */
$.extend( $.fn.dataTableExt.oPagination, {
	"bootstrap": {
		"fnInit": function( oSettings, nPaging, fnDraw ) {
			var oLang = oSettings.oLanguage.oPaginate;
			var fnClickHandler = function ( e ) {
				e.preventDefault();
				if ( oSettings.oApi._fnPageChange(oSettings, e.data.action) ) {
					fnDraw( oSettings );
				}
			};

			$(nPaging).addClass('pagination').append(
				'<ul>'+
					'<li class="prev disabled"><a href="#"><i class="fa fa-chevron-left"></i></a></li>'+
					'<li class="next disabled"><a href="#"><i class="fa fa-chevron-right"></i></a></li>'+
				'</ul>'
			);
			var els = $('a', nPaging);
			$(els[0]).bind( 'click.DT', { action: "previous" }, fnClickHandler );
			$(els[1]).bind( 'click.DT', { action: "next" }, fnClickHandler );
		},

		"fnUpdate": function ( oSettings, fnDraw ) {
			var iListLength = 5;
			var oPaging = oSettings.oInstance.fnPagingInfo();
			var an = oSettings.aanFeatures.p;
			var i, ien, j, sClass, iStart, iEnd, iHalf=Math.floor(iListLength/2);

			if ( oPaging.iTotalPages < iListLength) {
				iStart = 1;
				iEnd = oPaging.iTotalPages;
			}
			else if ( oPaging.iPage <= iHalf ) {
				iStart = 1;
				iEnd = iListLength;
			} else if ( oPaging.iPage >= (oPaging.iTotalPages-iHalf) ) {
				iStart = oPaging.iTotalPages - iListLength + 1;
				iEnd = oPaging.iTotalPages;
			} else {
				iStart = oPaging.iPage - iHalf + 1;
				iEnd = iStart + iListLength - 1;
			}

			for ( i=0, ien=an.length ; i<ien ; i++ ) {
				// Remove the middle elements
				$('li:gt(0)', an[i]).filter(':not(:last)').remove();

				// Add the new list items and their event handlers
				for ( j=iStart ; j<=iEnd ; j++ ) {
					sClass = (j==oPaging.iPage+1) ? 'class="active"' : '';
					$('<li '+sClass+'><a href="#">'+j+'</a></li>')
						.insertBefore( $('li:last', an[i])[0] )
						.bind('click', function (e) {
							e.preventDefault();
							oSettings._iDisplayStart = (parseInt($('a', this).text(),10)-1) * oPaging.iLength;
							fnDraw( oSettings );
						} );
				}

				// Add / remove disabled classes from the static elements
				if ( oPaging.iPage === 0 ) {
					$('li:first', an[i]).addClass('disabled');
				} else {
					$('li:first', an[i]).removeClass('disabled');
				}

				if ( oPaging.iPage === oPaging.iTotalPages-1 || oPaging.iTotalPages === 0 ) {
					$('li:last', an[i]).addClass('disabled');
				} else {
					$('li:last', an[i]).removeClass('disabled');
				}
			}
		}
	}
} );


/*
 * TableTools Bootstrap compatibility
 * Required TableTools 2.1+
 */

	// Set the classes that TableTools uses to something suitable for Bootstrap
	$.extend( true, $.fn.DataTable.TableTools.classes, {
		"container": "DTTT pull-left",
		"buttons": {
			"normal": "btn btn-white",
			"disabled": "disabled"
		},
		"collection": {
			"container": "DTTT_dropdown dropdown-menu",
			"buttons": {
				"normal": "",
				"disabled": "disabled"
			}
		},
		"print": {
			"info": "DTTT_print_info modal"
		},
		"select": {
			"row": "active"
		}
	} );

	// Have the collection use a bootstrap compatible dropdown
	$.extend( true, $.fn.DataTable.TableTools.DEFAULTS.oTags, {
		"collection": {
			"container": "ul",
			"button": "li",
			"liner": "a"
		}
	} );

$(".select2-wrapper").select2({minimumResultsForSearch: -1});

var table_api = null;
var tables = {};
/* Table initialisation */
$(document).ready(function() {
    var responsiveHelper = undefined;
    var breakpointDefinition = {
        tablet: 1024,
        phone : 480
    };

	tableElement = $('#example-table');
	var column_defs = null;
	var column_sort = null;
	if(tableElement.hasClass('admin-table')) {
		column_sort = [[ 0, "asc" ]];
	}
	else {
		column_sort = [[ 0, "desc" ]];
	}

	var today = new Date();
    var dd = today.getDate();
    var mm = today.getMonth()+1; //January is 0!
    var hour = today.getHours();
    var minutes = today.getMinutes();
    var seconds = today.getSeconds();


    var yyyy = today.getFullYear();
    if(dd<10){
        dd='0'+dd
    }
    if(mm<10){
        mm='0'+mm
    }
    var today = dd+'-'+mm+'-'+yyyy + ' ' + hour + ':' + minutes + ':' + seconds;
	
	var tableArgs = {
		"sDom": "<'row'<'col-md-6'l T C><'col-md-6'f>r>t<'row'<'col-md-12'p i>>",
			"oTableTools": {
			"aButtons": [
				{
					"sExtends":    "collection",
					"sButtonText": "<i class='fa fa-cloud-download'></i>",
					"aButtons":    [ "csv", "xls", {
					                    "sExtends": "pdf",
					                    "sPdfMessage": "Raport generat in data de: " + today
					                }, "copy"],
					"mColumns": "visible"
				}
			]
		},
		"sPaginationType": "bootstrap",
		"aoColumnDefs": column_defs,
		//"sScrollX": "100%",
		"aaSorting": column_sort,
		"bProcessing": true,
		"oLanguage": {
			"sLengthMenu": "_MENU_ ",
			"sInfo": "De la <b>_START_ la _END_</b> din _TOTAL_"
		},
		 bAutoWidth     : false,
        fnPreDrawCallback: function () {
            // Initialize the responsive datatables helper once.
            if (!responsiveHelper) {
                responsiveHelper = new ResponsiveDatatablesHelper(tableElement, breakpointDefinition);
            }
        },
        fnRowCallback  : function (nRow) {
            responsiveHelper.createExpandIcon(nRow);
        },
        fnDrawCallback : function (oSettings) {
            responsiveHelper.respond();
        }
	};
	if(tableElement.hasClass('ajax-table')) {
		tableArgs.bServerSide = true;
		tableArgs.sAjaxSource = tableElement.data('source');
	}


    table_api = tableElement.DataTable( tableArgs );


	$('#example_wrapper .dataTables_filter input').addClass("input-medium "); // modify table search input
    $('#example_wrapper .dataTables_length select').addClass("select2-wrapper span12"); // modify table per page dropdown



	$('#example input').click( function() {
        $(this).parent().parent().parent().toggleClass('row_selected');
    });


	$('#quick-access .btn-cancel').click( function() {
		$("#quick-access").css("bottom","-115px");
    });
	$('#quick-access .btn-add').click( function() {
		fnClickAddRow();
		$("#quick-access").css("bottom","-115px");
    });

	$("div.toolbar").html('<div class="table-tools-actions"><button class="btn btn-primary" style="margin-left:12px" id="test2">Add</button></div>');


	function fnClickAddRow() {
    $('#example3').dataTable().fnAddData( [
        $("#val1 option:selected").text(),
        $("#val2 option:selected").text(),
        "Windows",
        "789.","A" ] );
	}

	$(".column-toggler").change(function(){
		var column = tableElement.column( $(this).attr('data-column') );
        column.visible($(this).is(":checked"));
	});


	/**** generic datatables ********/
	$(".datatables").each(function(elm){
		var tableElement = $(this);
		tables[$(this).attr('id')] = tableElement;
		var column_defs = null;
		var column_sort = null;
		if(tableElement.hasClass('admin-table')) {
			column_sort = [[ 1, "asc" ]];
		}
		else {
			column_sort = [[ 0, "desc" ]];
		}

		tableElement.DataTable( {
			"sDom": "<'row'<'col-md-6'l T C><'col-md-6'f>r>t<'row'<'col-md-12'p i>>",
				"oTableTools": {
				"aButtons": [
					{
						"sExtends":    "collection",
						"sButtonText": "<i class='fa fa-cloud-download'></i>",
						"aButtons":    [ "csv", "xls", "pdf", "copy"],
						"mColumns": "visible"
					}
				]
			},
			"sPaginationType": "bootstrap",
			"aoColumnDefs": column_defs,
			"sScrollX": "100%",
			"aaSorting": column_sort,
			"bProcessing": true,
			"oLanguage": {
				"sLengthMenu": "_MENU_ ",
				"sInfo": "De la <b>_START_ la _END_</b> din _TOTAL_"
			},
			 bAutoWidth     : false,
			fnPreDrawCallback: function () {
				// Initialize the responsive datatables helper once.
				if (!responsiveHelper) {
					responsiveHelper = new ResponsiveDatatablesHelper(tableElement, breakpointDefinition);
				}
			},
			fnRowCallback  : function (nRow) {
				responsiveHelper.createExpandIcon(nRow);
			},
			fnDrawCallback : function (oSettings) {
				responsiveHelper.respond();
			}
		});


		tableElement.find('.dataTables_filter input').addClass("input-medium "); // modify table search input
		tableElement.find('.dataTables_length select').addClass("select2-wrapper span12"); // modify table per page dropdown



		tableElement.find('input').click( function() {
			$(this).parent().parent().parent().toggleClass('row_selected');
		});
	});
});


/* Formating function for row details */
function fnFormatDetails ( oTable, nTr )
{
    var aData = oTable.fnGetData( nTr );
    var sOut = '<table cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;" class="inner-table">';
    sOut += '<tr><td>Rendering engine:</td><td>'+aData[1]+' '+aData[4]+'</td></tr>';
    sOut += '<tr><td>Link to source:</td><td>Could provide a link here</td></tr>';
    sOut += '<tr><td>Extra info:</td><td>And any further details here (images etc)</td></tr>';
    sOut += '</table>';

    return sOut;
}
