<link href="<?php echo base_url('assets/plugins/bootstrap-datepicker/css/bootstrap-datepicker.min.css'); ?>" rel="stylesheet">
<link href="<?php echo base_url('assets/plugins/bootstrap-daterangepicker/daterangepicker.css'); ?>" rel="stylesheet">
<link href="<?php echo base_url('assets/plugins/datatables/dataTables.colVis.css'); ?>" rel="stylesheet" type="text/css" />
<script src="<?php echo base_url('assets/plugins/datatables/dataTables.colVis.js'); ?>"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.3.4/jspdf.min.js"></script>

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
                    <h4 class="page-title">Agent Sales</h4>
                    <ol class="breadcrumb"> </ol>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-12">
                    <div class="panel panel-color panel-primary">
                        <div class="panel-heading">
                            <div class="btn-group pull-right">
                                <button type="button" class="btn btn-danger btn-custom btn-rounded waves-effect waves-light" onclick="onSearch();">Search</button>
                            </div>
                            <h3 class="panel-title">Agent Sales</h3>
                        </div>
                        <div class="panel-body" style="background-color:lightblue;">
                            <form class="form-horizontal" role="form">
                                <div class="row">
                                    <div class="col-sm-3">
                                        <div class="form-group has-success">
                                            <label class="col-md-6 control-label">Week</label>
                                            <div class="col-md-6">
                                                <select class="selectpicker" name="week" id="week" data-style="btn-default btn-custom">
                                                    <?php foreach ($weeks as $week) { ?>
                                                        <option value="<?php echo $week->week_no; ?>" <?php if ($curWeekNo == $week->week_no) echo 'selected'; ?>><?php echo $week->week_no; ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group has-success">
                                            <label class="col-md-6 control-label">Staff</label>
                                            <div class="col-md-6">
                                                <select class="selectpicker" name="staff" id="staff" data-style="btn-default btn-custom">
                                                    <option value="0" selected>ALL</option>
                                                    <?php foreach ($staffs as $staff) { ?>
                                                        <option value="<?php echo $staff->Id; ?>"><?php echo $staff->user_id; ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group has-success">
                                            <label class="col-md-6 control-label">Agent</label>
                                            <div class="col-md-6">
                                                <select class="selectpicker" name="agent" id="agent" data-style="btn-default btn-custom">
                                                    <option value="0" selected>ALL</option>
                                                    <?php foreach ($agents as $agent) { ?>
                                                        <option value="<?php echo $agent->Id; ?>"><?php echo $agent->user_id; ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group has-success">
                                            <label class="col-md-6 control-label">Terminal</label>
                                            <div class="col-md-6">
                                                <select class="selectpicker" name="terminal" id="terminal" data-style="btn-default btn-custom">
                                                    <option value="0" selected>ALL</option>
                                                    <?php foreach ($terminals as $terminal) { ?>
                                                        <option value="<?php echo $terminal->Id; ?>"><?php echo $terminal->terminal_no; ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-12">
                    <div class="card-box table-responsive">
                        <table id="table1" class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>Sales</th>
                                    <th>Total Sale</th>
                                    <th>Total Payable to Agents</th>
                                    <th>Win</th>
                                    <th>Total Winning</th>
                                    <th>Bal Agents</th>
                                    <th>Bal Company</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-12">
                    <div class="card-box table-responsive">
                        <div class="btn-group pull-right">
                            <button type="button" class="btn btn-primary btn-custom btn-rounded waves-effect waves-light" onclick="demoFromHTML();">Print</button>
                        </div>                        
                        <!-- <table id="table2" class="table table-striped table-bordered"> -->
                        <table id="table2" class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Week</th>
                                    <th>Staff</th>
                                    <th>Agent</th>
                                    <th>Terminal NO</th>
                                    <th>Sales</th>
                                    <th>Payable</th>
                                    <th>Win</th>
                                    <th>Total Win</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>


        </div> <!-- container -->
    </div> <!-- content -->

    <!-- <script src="<?php echo base_url('assets/plugins/switchery/js/switchery.min.js'); ?>"></script> -->
    <script src="<?php echo base_url('assets/plugins/bootstrap-daterangepicker/daterangepicker.js'); ?>"></script>
    <script src="<?php echo base_url('assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js'); ?>"></script>
    <script type="text/javascript">
        jQuery('#date-range').datepicker({
            toggleActive: true
        });

        function initTable(tagId, cols, dataUrl) {
            var tblObj = $(tagId).DataTable({
                // dom: 'C<"clear">lfBrtip',
                // colVis: {
                //     buttonText: "Columns"
                // },
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
                    orientation: 'landscape',
                    pageSize: 'LEGAL',
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
                aLengthMenu: [
                    [10, 25, 50, -1],
                    [10, 25, 50, "All"]
                ],
                iDisplayLength: -1,
                ajax: {
                    url: dataUrl,
                    type: "POST",
                },
            });
            return tblObj;
        }
        var tableName = "<?php echo $table; ?>";
        var tblSummary, tblSummary1;

        tblSummary = initTable("#table1",
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
                    targets: [-1], //last column
                    orderable: false, //set not orderable
                    className: "actions dt-center"
                }
            ], "<?php echo site_url('Cms_api/get_result_summary0_by_agent') . '/' . $curWeekNo; ?>");
        tblSummary1 = initTable("#table2",
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
                    targets: [2], //first column 
                    orderable: false, //set not orderable
                    className: "dt-center"
                },
                {
                    targets: [3], //last column
                    orderable: false, //set not orderable
                    className: "actions dt-center"
                },
                {
                    targets: [4], //first column 
                    orderable: false, //set not orderable
                    className: "dt-center"
                },
                {
                    targets: [5], //first column 
                    orderable: false, //set not orderable
                    className: "dt-center"
                },
                {
                    targets: [6], //last column
                    orderable: false, //set not orderable
                    className: "actions dt-center"
                },
                {
                    targets: [7], //first column 
                    orderable: false, //set not orderable
                    className: "dt-center"
                },
                {
                    targets: [8], //first column 
                    orderable: false, //set not orderable
                    className: "dt-center"
                }
            ], "<?php echo site_url('Cms_api/get_result_summary_by_agent') . '/' . $curWeekNo; ?>");

        function onSearch() {
            week = document.getElementById('week').value;
            staff = document.getElementById('staff').value;
            agent = document.getElementById('agent').value;
            terminal = document.getElementById('terminal').value;

            let params = '/' + week + '/' + staff + '/' + agent + '/' + terminal;

            tblSummary.ajax.url("<?php echo site_url('Cms_api/get_result_summary0_by_agent') ?>" + params);
            tblSummary.ajax.reload();

            tblSummary1.ajax.url("<?php echo site_url('Cms_api/get_result_summary_by_agent') ?>" + params);
            tblSummary1.ajax.reload();
        }


        function MergeCommonRows(table, mergeRows) {
            var firstColumnBrakes = [];
            // iterate through the columns instead of passing each column as function parameter:
            for (var i = 1; i <= table.find('th').length; i++) {
                //if(!$.inArray(i, mergeRows)) continue;

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

        $('#table2').on('draw.dt', function() {
            MergeCommonRows($('#table2'));
        });

        function demoFromHTML() {
            html2canvas(document.getElementById('table2'), {
                onrendered: function (canvas) {
                    var data = canvas.toDataURL();
                    var docDefinition = {
                        content: [{
                            image: data,
                            width: 500
                        }]
                    };
                    pdfMake.createPdf(docDefinition).download("Table.pdf");
                }
            });
        }
    </script>