<link href="<?php echo base_url('assets/plugins/custombox/css/custombox.css'); ?>" rel="stylesheet">
<script src="<?php echo base_url('assets/plugins/custombox/js/custombox.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/plugins/custombox/js/legacy.min.js'); ?>"></script>

<style>
.busy {
  border: 16px solid #f3f3f3;
  border-radius: 50%;
  border-top: 16px solid blue;
  border-bottom: 16px solid blue;
  width: 120px;
  height: 120px;
  -webkit-animation: spin 2s linear infinite;
  animation: spin 2s linear infinite;
}

@-webkit-keyframes spin {
  0% { -webkit-transform: rotate(0deg); }
  100% { -webkit-transform: rotate(360deg); }
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.check-selected{
    border: solid 1px #5d9cec;
    border-radius: 4px;
    font-size: large;
    padding: 4px;    
}

</style>

<!-- ============================================================== -->
<!-- Start right Content here -->
<!-- ============================================================== -->
<div class="content-page">
    <!-- Start content -->
    <div class="content">
        <div class="container">
            <!-- Page-Title -->
            <div class="row">
                <div class="col-sm-12">
                    <div class="panel panel-color panel-primary">
                        <div class="panel-heading">
                            <h3 class="panel-title">Results</h3>
                        </div>
                        <div class="panel-body" style="background-color:lightblue;">
                            <form class="form-horizontal" role="form">
                                <div class="row">
                                    <div class="col-sm-2">
                                        <div class="form-group has-success">
                                            <label class="col-md-6 control-label">Week</label>
                                            <div class="col-md-6">
                                                <select class="selectpicker" name="week" id="week" data-style="btn-default btn-custom" onchange="onChangeWeek();">
                                                    <!-- <option value="0" selected>ALL</option> -->
                                                    <?php foreach ($weeks as $week) { ?>
                                                        <option value="<?php echo $week->week_no; ?>" <?php if ($curWeekNo == $week->week_no) echo 'selected'; ?>><?php echo $week->week_no; ?></option>
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
                        <div class="row">
                            <div class="btn-group pull-right">
                                <div hidden class="busy" id="calcBusy"></div>
                                <div class="m-b-30">
                                    <button id="btnApply" class="btn btn-default waves-effect waves-light" onclick="onApply();"><i class="fa fa-check-square-o"></i> Apply</button>
                                </div>
                            </div>
                        </div>
                        <table id="table1" class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>Game No</th>
                                    <th>Home</th>
                                    <th>Away</th>
                                    <th>Home Score</th>
                                    <th>Away Score</th>
                                    <th>Week</th>
                                    <th>Prize</th>
                                    <th>Check</th>
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
                    orderable: true, //set not orderable
                    className: "dt-center"
                },
                {
                    targets: [2], //first column 
                    orderable: true, //set not orderable
                    className: "dt-center"
                },
                {
                    targets: [3], //first column 
                    orderable: true, //set not orderable
                    className: "dt-center"
                },
                {
                    targets: [4], //first column 
                    orderable: true, //set not orderable
                    className: "dt-center"
                },
                {
                    targets: [5], //first column 
                    orderable: true, //set not orderable
                    className: "dt-center"
                },
                {
                    targets: [6], //first column 
                    orderable: false, //set not orderable
                    className: "dt-center"
                }
            ], "<?php echo site_url('Cms_api/get_results').'/'.$curWeekNo ?>"
        );


        function onCheck(id, checked) {
            $.ajax({
                url: "<?php echo site_url('Cms_api/check_game') ?>",
                data: {
                    Id: id,
                    checked: checked
                },
                type: "POST",
                dataType: "JSON",
                success: function(data) {
                    tbl.ajax.reload();
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    swal("Error!", "", "error");
                }
            });
        }

        function onApply() {
            swal({
                title: "Are you sure?",
                text: "This process will takes long time!",
                type: "warning",
                showCancelButton: true,
                cancelButtonClass: 'btn-white btn-md waves-effect',
                confirmButtonClass: 'btn-danger btn-md waves-effect waves-light',
                confirmButtonText: 'Yes',
                closeOnConfirm: false
            }, function(isConfirm) {
               
                if (isConfirm) {
                    
                    document.getElementById("calcBusy").style.display = "block";
                    document.getElementById("btnApply").style.display = "none";
                    $.ajax({
                        url: "<?php echo site_url('Api/apply_game_result') ?>",
                        data: {},
                        type: "POST",
                        dataType: "JSON",
                        success: function(data) {
                            document.getElementById("calcBusy").style.display = "none";
                            document.getElementById("btnApply").style.display = "block";
                            if (data.status == 200) {
                                swal("Apply Success!", "", "success");
                            }
                        },
                        error: function(jqXHR, textStatus, errorThrown) {
                            document.getElementById("calcBusy").style.display = "none";
                            document.getElementById("btnApply").style.display = "block";
                            swal("Error!", "", "error");
                        }
                    });

                }
            });
        }

        function onChangeWeek() {
            week = document.getElementById('week').value;
            curWeek = "<?php echo $curWeekNo; ?>";
            if(week == curWeek)
            {
                document.getElementById("addToTable").disabled = false;                
            }
            else
            {
                document.getElementById("addToTable").disabled = true;
            }

            tbl.ajax.url("<?php echo site_url('Cms_api/get_results') ?>" + "/" + week);
            tbl.ajax.reload();
        }
    </script>