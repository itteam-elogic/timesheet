var datatable;
		$(document).ready(function () {        
        //datatables
			
        datatable = $('#datatable1').DataTable({
            "processing": true, //Feature control the processing indicator.
            "serverSide": true, //Feature control DataTables' server-side processing mode.
            "order": [], //Initial no order.
            "pageLength": 50, // Set Page Length
          //"lengthMenu":[[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
             "lengthMenu":[[10, 25, 50, 100], [10, 25, 50, 100]],
            // Load data for the table's content from an Ajax source
            "ajax": {
                "url": "empreports/getdata",
                "type": "POST",
                //Custom Post
                //"data": {"YOUR CUSTOM POST NAME": "YOUR VALUE"}
                
            },
            
            //Set column definition initialisation properties.
            "columnDefs": [
                {
                    "targets": [0,0,8], //first, Fourth, seventh column
                    "orderable": false //set not orderable
                }
            ],
           "fnInitComplete": function (oSettings, response) {
            
            		$("#countData").text(response.recordsTotal);
          },
			
		"fnCreatedRow": function( nRow, aData, iDataIndex ) {
					$(nRow).attr('id', 'delRecordsRow'+aData[0]);
		},
		"footerCallback": function ( row, data, start, end, display ) {
            var api = this.api(), data;
           
            // Remove the formatting to get integer data for summation
            var intVal = function ( i ) {
                return typeof i === 'string' ?
                    i.replace(/[\$,]/g, '')*1 :
                    typeof i === 'number' ?
                        i : 0;
            };
 
            // Total over all pages
            total = api
                .column( 4 )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
 
            // Total over this page
            pageTotal = api
                .column( 4, { page: 'current'} )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
 
            // Update footer
            $( api.column( 4 ).footer() ).html(
                //''+pageTotal +' ( '+ total +' total)'
				
				pageTotal 
            );
        }
            
        });
 });