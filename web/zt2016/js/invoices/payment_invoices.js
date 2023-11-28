$(function(){
    //$('.datepicker').datepicker()
    $('#table_loading_message').hide() ;
    

    $.fn.dataTable.moment( 'D-MMM-YYYY' );

    $('#invoices_table').css({ "display": "table", "width": "100%"}).dataTable( {
    "dom":' <"col-sm-6"f><"col-sm-6"i>t<"bottom10"p><"bottom20"l>r<"clear">',
    //"dom":'difrtp',
    "order": [[ 2, "desc" ]],
    "scrollY":        "20em",
    "scrollCollapse": true,
    "paging":         true,
    "info":           true,
    // "processing": true,
    "orderClasses": false,
    "lengthMenu": [[10, 25, 50, 100, 250, -1], [10, 25, 50, 100, 250, "All"]],
    columnDefs: [ {
        targets: [ 3 ],
        orderData: [ 0, 1 ]
    }, {
        targets: [ 1 ],
        orderData: [ 1, 0 ]
    } ],
    "processing": true,
	"deferRender": true,
    // "ajax": "zt2016_existing_invoices_ajax",
           

       
    /*BELOWFROM HERE:
     * https://datatables.net/examples/api/multi_filter_select.html*/
    initComplete: function () {
        $('#invoices_table_wrapper').css({ "display": "block"}).show();
        var icount=0
        // Create a container for filter buttons above the table
        var filterContainer = $('<div class=""></div>');
    
        $('.header-hwe').append(filterContainer);
        

        this.api().columns(7).every(function () {
            var column = this;
            var container = $(column.footer()).empty();

            var flexContainer = $('<div class="row p-3"></div>');
            filterContainer.append(flexContainer);

            var allButton = $('<button class="btn btn-filter btn-success col-md-2 ms-2 mb-3">All</button>');
            allButton.on('click', function () {
                column.search('').draw();
                filterContainer.find('button').removeClass('btn-success');
                filterContainer.find('button').addClass('btn-default');
                $(this).removeClass('btn-default');

                $(this).addClass('btn-success');
            });
            flexContainer.append(allButton);
            // var Partially = $('<button class="btn btn-filter btn-default ms-2">Partially paid</button>');
            // Partially.on('click', function () {
            //     column.search('').draw();
            //     filterContainer.find('button').removeClass('btn-success');
            //     filterContainer.find('button').addClass('btn-default');
            //     $(this).removeClass('btn-default');

            //     $(this).addClass('btn-success');
            // });
            // flexContainer.append(Partially);

            column.data().unique().sort().each(function (d, j) {
                if(d== 'BILLED' || d== 'OVERDUE' || d=='PAID' || d=='Partially Paid'){
                    var button = $('<button type="button" class="btn btn-filter btn-default ms-2 col-md-2 mb-3">' + d + '</button>');
                    button.on('click', function () {
                        var val = d;
                        column.search(val ? val : '', true, false).draw();
                        filterContainer.find('button').removeClass('btn-success');
                        filterContainer.find('button').addClass('btn-default');
                        $(this).removeClass('btn-default');
                        $(this).addClass('btn-success');
                    });
                }
                flexContainer.append(button);
            });
        });
        // var filterContainer = $('<div style="display: flex; flex-direction: row; align-items: center;"></div>');
        // $(column.header()).append(filterContainer);

        this.api().columns(2).every(function () {
            var column = this;
            var container = $(column.footer()).empty();
        
            // Get the current year
            var currentYear = new Date().getFullYear();
        
            // Create a container for filter buttons below the search box
            var filterContainer1 = $('<div class="mb-5 row p-3"></div>');
            $('.date-filter').append(filterContainer1);
        
            var createButton = function (text, regex) {
                let class12 = '';
                if(text == 'All'){
                    class12 = 'btn-success';
                }else{
                    class12 =  'btn-default';
                }
                var button = $('<button class="btn btn-filter '+class12+' ms-2 col-md-2 mb-3">' + text + '</button>');
                button.on('click', function () {
                    column.search(regex, true, false).draw();
                    // Add the btn-success class to the clicked button and remove it from others
                    filterContainer1.find('button').removeClass('btn-success');
                    filterContainer1.find('button').addClass('btn-default');
                    $(this).removeClass('btn-default');
                    $(this).addClass('btn-success');
                });
                return button;
            };
        
            // Create filter buttons for different date ranges
            filterContainer1.append(createButton("All", ''));
            filterContainer1.append(createButton("Current Year", '\\d{2}-[A-Za-z]{3}-' + currentYear));
            filterContainer1.append(createButton("Past 3 Months", getPastThreeMonthsRegex()));
            filterContainer1.append(createButton("Past 2 Years", getPastTwoYearsRegex(currentYear)));
             // Empty regex to clear the filter

            function getPastThreeMonthsRegex() {
                var currentDate = new Date();
                var threeMonthsAgo = new Date(currentDate);
                threeMonthsAgo.setMonth(threeMonthsAgo.getMonth() - 3);
                var regexString = generateDateRegex(threeMonthsAgo, currentDate);
                return regexString;
            }
            function getPastTwoYearsRegex(currentYear) {
              
                var twoYearsAgo = currentYear - 2 ;
                var regexString = '\\d{2}-[A-Za-z]{3}-' + twoYearsAgo;
                return regexString;
            }
            function generateDateRegex(startDate, endDate) {
                var startYear = startDate.getFullYear();
                var startMonth = startDate.toLocaleString('default', { month: 'short' }).toUpperCase();
                var endYear = endDate.getFullYear();
                var endMonth = endDate.toLocaleString('default', { month: 'short' }).toUpperCase();
                var regexString = '\\d{2}-' + startMonth + '-' + startYear + '|\\d{2}-' + endMonth + '-' + endYear;
                return regexString;
            }
            
        
        });

        
        
   }, //initComplete: 

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
        /*total = api
            .column( 4 )
            .data()
            .reduce( function (a, b) {
                return intVal(a) + intVal(b);
            }, 0 )
         */
        // Total over this page
        pageTotal = api
            .column( 5, { page: 'current'} )
            .data()
            .reduce( function (a, b) {
                return intVal(a) + intVal(b);
            }, 0 );

        // Update footer
        $( api.column( 5 ).footer() ).html(
            pageTotal.formatMoney(2)  //+ ' ('+ total.formatMoney(2) +')'
        );
    }
    
    
}); //$('#invoices_table').dataTable

$('.paidAmount').on('input', function() {
    const paidAmount = $(this).val();
    const PaidDate = $(this).data('date');
    const invoice = $(this).data('invoice');
    // console.log(PaidDate);
    
    $.ajax({
        type: 'POST',
        url: BaseUrl + 'payment/zt2016_paidamount',
        data: { paidAmount: paidAmount, PaidDate: PaidDate ,invoice:invoice},
        success: function (data) {
            // Handle the response, e.g., update the result div
            console.log(data);
        },
        error: function () {
            console.error('Failed to update status');
        }
    });
});

});   //$(function()     


Number.prototype.formatMoney = function(c, d, t){
var n = this, 
c = isNaN(c = Math.abs(c)) ? 2 : c, 
d = d == undefined ? "." : d, 
t = t == undefined ? "," : t, 
s = n < 0 ? "-" : "", 
i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "", 
j = (j = i.length) > 3 ? j % 3 : 0;
return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
};