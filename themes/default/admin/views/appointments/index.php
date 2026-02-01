<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<script type="text/javascript">
    var csrf_token_name = '<?= $this->security->get_csrf_token_name(); ?>';
    var csrf_hash = '<?= $this->security->get_csrf_hash(); ?>';
</script>
<script>
    $(document).ready(function () {
        oTable = $('#SLData').dataTable({
            "aaSorting": [[4, "desc"], [5, "desc"]],
            "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?= lang('all') ?>"]],
            "iDisplayLength": <?= $Settings->rows_per_page ?>,
            'bProcessing': true, 'bServerSide': true,
            'sAjaxSource': '<?= admin_url('appointments/getAppointments'); ?>',
            'fnServerData': function (sSource, aoData, fnCallback) {
                aoData.push({"name": "<?= $this->security->get_csrf_token_name() ?>", "value": "<?= $this->security->get_csrf_hash() ?>"});
                $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
            },
            'fnRowCallback': function (nRow, aData, iDisplayIndex) {
                nRow.id = aData[0];
                nRow.className = "appointment_row";
                return nRow;
            },
            "aoColumns": [{"bVisible": false}, null, null, null, {"mRender": fsd}, null, null, null, {"bSortable": false}],
            "fnFooterCallback": function (nRow, aaData, iStart, iEnd, aiDisplay) {}
        }).fnSetFilteringDelay().dtFilter([
            {column_number: 1, filter_default_label: "[<?= lang('reference_no'); ?>]", filter_type: "text", data: []},
            {column_number: 2, filter_default_label: "[<?= lang('appointment_type'); ?>]", filter_type: "text", data: []},
            {column_number: 3, filter_default_label: "[<?= lang('subject'); ?>]", filter_type: "text", data: []},
            {column_number: 4, filter_default_label: "[<?= lang('date'); ?>]", filter_type: "text", data: []},
            {column_number: 5, filter_default_label: "[<?= lang('time'); ?>]", filter_type: "text", data: []},
            {column_number: 6, filter_default_label: "[<?= lang('status'); ?>]", filter_type: "text", data: []},
            {column_number: 7, filter_default_label: "[<?= lang('customer'); ?>]", filter_type: "text", data: []}
        ], "footer");
    });
</script>

<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-calendar-check-o"></i><?= lang('appointments'); ?></h2>
        <div class="box-icon">
            <ul class="btn-tasks">
                <?php if ($this->sma->actionPermissions('add', 'appointments')) { ?>
                <li>
                    <a href="<?= admin_url('appointments/add') ?>">
                        <i class="icon fa fa-plus tip" data-placement="left" title="<?= lang('add_appointment') ?>"></i>
                    </a>
                </li>
                <?php } ?>
            </ul>
        </div>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="table-responsive">
                <table id="SLData" class="table table-bordered table-hover table-striped" cellpadding="0" cellspacing="0" border="0">
                    <thead>
                    <tr>
                        <th style="width:30px; text-align: center;">#</th>
                        <th class="col-xs-1"><?= lang('reference_no'); ?></th>
                        <th class="col-xs-1"><?= lang('appointment_type'); ?></th>
                        <th class="col-xs-2"><?= lang('subject'); ?></th>
                        <th class="col-xs-1"><?= lang('date'); ?></th>
                        <th class="col-xs-1"><?= lang('time'); ?></th>
                        <th class="col-xs-1"><?= lang('status'); ?></th>
                        <th class="col-xs-2"><?= lang('customer'); ?></th>
                        <th style="width:100px; text-align:center;"><?= lang('actions'); ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td colspan="9" class="dataTables_empty"><?= lang('loading_data'); ?></td>
                    </tr>
                    </tbody>
                    <tfoot class="dtFilter">
                    <tr class="active">
                        <th style="width:30px; text-align: center;"></th>
                        <th></th><th></th><th></th><th></th><th></th><th></th><th></th>
                        <th style="width:100px; text-align:center;"><?= lang('actions'); ?></th>
                    </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
