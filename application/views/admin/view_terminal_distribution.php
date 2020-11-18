<link href="<?php echo base_url('assets/plugins/custombox/css/custombox.css'); ?>" rel="stylesheet">
<script src="<?php echo base_url('assets/plugins/custombox/js/custombox.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/plugins/custombox/js/legacy.min.js'); ?>"></script>

<!-- ============================================================== -->
<!-- Start right Content here -->
<!-- ============================================================== -->
<div class="content-page">
    <!-- Start content -->
    <div class="content">
        <div class="container">
            <!-- Page-Title -->
            <div class="row">
                <div class="col-sm-6">
                    <h4 class="page-title">Terminal Distribution</h4>
                    <ol class="breadcrumb"> </ol>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-12">
                    <div class="card-box table-responsive">
                        <table id="table1" class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>Staff</th>
                                    <th>Agent</th>
                                    <th>Terminal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>TSN1</td>
                                    <td>test</td>
                                    <td>1234</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>



        </div> <!-- container -->
    </div> <!-- content -->


    <!-- <script src="<?php echo base_url('assets/plugins/switchery/js/switchery.min.js'); ?>"></script> -->
    <script type="text/javascript">
        function initTable(tagId, cols, dataUrl) {
            var tblObj = $(tagId).DataTable({
                dom: "lfBrtip",
                buttons: [{
                    extend: "copy",
                    className: "btn-sm"
                }, {
                    extend: "csv",
                    className: "btn-sm"
                }, {
                    extend: "excel",
                    className: "btn-sm"
                }, {
                    extend: "pdf",
                    className: "btn-sm"
                }, {
                    extend: "print",
                    className: "btn-sm"
                }],
                responsive: !0,
                processing: true,
                serverSide: false,
                sPaginationType: "full_numbers",
                language: {
                    paginate: {
                        next: '<i class="fa fa-angle-right"></i>',
                        previous: '<i class="fa fa-angle-left"></i>',
                        first: '<i class="fa fa-angle-double-left"></i>',
                        last: '<i class="fa fa-angle-double-right"></i>'
                    }
                },
                //Set column definition initialisation properties.
                columnDefs: cols,
                ajax: {
                    url: dataUrl,
                    type: "POST",
                },
            });
            return tblObj;
        }


        var tableName = "<?php echo $table; ?>";
        var tbl;

        tbl = initTable("#table1",
            [{
                    targets: [0], //first column 
                    orderable: true, //set not orderable
                    className: "dt-center"
                },
                {
                    targets: [1], //first column 
                    orderable: false, //set not orderable
                    className: "dt-center"
                },
                {
                    targets: [2], //last column
                    orderable: false, //set not orderable
                    className: "actions dt-center"
                }
            ], "<?php echo site_url('Cms_api/get_terminal_distributions') ?>"
        );


        function MergeCommonRows(table) {
            var firstColumnBrakes = [];
            // iterate through the columns instead of passing each column as function parameter:
            for (var i = 1; i <= table.find('th').length; i++) {
                var previous = null,
                    cellToExtend = null,
                    rowspan = 1;
                table.find("td:nth-child(" + i + ")").each(function(index, e) {
                    var jthis = $(this),
                        content = jthis.text();
                    // check if current row "break" exist in the array. If not, then extend rowspan:
                    if (previous == content && content !== "" && $.inArray(index, firstColumnBrakes) === -1) {
                        // hide the row instead of remove(), so the DOM index won't "move" inside loop.
                        jthis.addClass('hidden');
                        cellToExtend.attr("rowspan", (rowspan = rowspan + 1));
                    } else {
                        // store row breaks only for the first column:
                        if (i === 1) firstColumnBrakes.push(index);
                        rowspan = 1;
                        previous = content;
                        cellToExtend = jthis;
                    }
                });
            }
            // now remove hidden td's (or leave them hidden if you wish):
            $('td.hidden').remove();
        }

        $('#table1').on('draw.dt', function() {
            MergeCommonRows($('#table1'));
        });
    </script>