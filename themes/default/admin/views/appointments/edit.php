<?php defined('BASEPATH') OR exit('No direct script access allowed');
$inv = $appointment;
?>
<script type="text/javascript">
    $(document).ready(function () {
        // Preferred date: same as communication - single datetime input, js_ldate
        $("#apdate").datetimepicker({
            format: site.dateFormats.js_ldate,
            fontAwesome: true,
            language: 'sma',
            weekStart: 1,
            todayBtn: 1,
            autoclose: 1,
            todayHighlight: 1,
            startView: 2,
            forceParse: 0
        });
        // Confirmed date: date only (optional)
        $('#confirmed_date_display').datetimepicker({
            format: site.dateFormats.js_sdate,
            fontAwesome: true,
            language: 'sma',
            todayBtn: 1,
            autoclose: 1,
            minView: 2
        }).on('change dp.change', function(e) {
            var d = (e && e.date) ? e.date : $(this).datetimepicker('getDate');
            if (d) {
                var ymd = (typeof d.format === 'function') ? d.format('YYYY-MM-DD') : (d.getFullYear() + '-' + ('0' + (d.getMonth() + 1)).slice(-2) + '-' + ('0' + d.getDate()).slice(-2));
                $('#confirmed_date').val(ymd);
            } else {
                $('#confirmed_date').val('');
            }
        });
        function syncConfirmedDate() {
            var v = $.trim($('#confirmed_date_display').val());
            if (v) {
                var parts = v.split(/[-\/\.]/);
                if (parts.length >= 3) {
                    var fmt = (site.dateFormats.js_sdate || 'dd-mm-yyyy').toLowerCase();
                    var y, m, d;
                    if (parts[0].length === 4) {
                        y = parts[0]; m = parts[1]; d = parts[2];
                    } else {
                        y = parts[2];
                        if (fmt.indexOf('dd') < fmt.indexOf('mm') || fmt.indexOf('dd') === 0) {
                            d = parts[0]; m = parts[1];
                        } else {
                            m = parts[0]; d = parts[1];
                        }
                    }
                    m = ('0' + m).slice(-2);
                    d = ('0' + d).slice(-2);
                    if (y && m && d) {
                        $('#confirmed_date').val(y + '-' + m + '-' + d);
                    }
                }
            }
        }
        $('#confirmed_date_display').closest('form').on('submit', syncConfirmedDate);
        $(document).on('click', 'input[type="submit"][name="edit_appointment"]', syncConfirmedDate);
    });
</script>
<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-calendar"></i><?= lang('edit'); ?> - <?= htmlspecialchars($inv->appointment_code); ?></h2>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">
                <?php
                $attrib = array('data-toggle' => 'validator', 'role' => 'form');
                echo admin_form_open_multipart('appointments/edit/' . $inv->id, $attrib);
                ?>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <?= lang('customer', 'customer'); ?>
                            <?= form_input('customer', $customer ? ($customer->name . ' ' . $customer->last_name . ' - ' . $customer->email) : $inv->customer_id, 'class="form-control" readonly'); ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <?= lang('reference_no', 'code'); ?>
                            <?= form_input('code', $inv->appointment_code, 'class="form-control" readonly'); ?>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <?= lang('appointment_type', 'appointment_type'); ?>
                            <?php echo form_dropdown('appointment_type', $appointment_types, set_value('appointment_type', $inv->appointment_type), 'id="appointment_type" class="form-control select" required="required" style="width:100%;"'); ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <?= lang('status', 'status'); ?>
                            <?php echo form_dropdown('status', $status_options, set_value('status', $inv->status), 'id="status" class="form-control select" required="required" style="width:100%;"'); ?>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <?= lang('subject', 'subject'); ?>
                            <?= form_input('subject', set_value('subject', $inv->subject), 'class="form-control" id="subject" required="required"'); ?>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <?= lang('description', 'description'); ?>
                            <?= form_textarea('description', set_value('description', $inv->description), 'class="form-control" id="description" rows="3"'); ?>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <?= lang('date', 'apdate'); ?>
                            <?php
                            $preferred_dt = ($inv->preferred_date && $inv->preferred_time) ? $inv->preferred_date . ' ' . $inv->preferred_time : '';
                            $preferred_dt_display = $preferred_dt ? $this->sma->hrld($preferred_dt) : '';
                            echo form_input('preferred_datetime', set_value('preferred_datetime', $preferred_dt_display), 'class="form-control input-tip datetime" id="apdate" required="required"');
                            ?>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <?= lang('duration', 'duration_minutes'); ?>
                            <?php echo form_dropdown('duration_minutes', $duration_options, set_value('duration_minutes', $inv->duration_minutes), 'id="duration_minutes" class="form-control select" style="width:100%;"'); ?>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <?= lang('confirmed_date', 'confirmed_date'); ?>
                            <input type="hidden" name="confirmed_date" id="confirmed_date" value="<?= set_value('confirmed_date', $inv->confirmed_date) ?>" />
                            <?= form_input('confirmed_date_display', set_value('confirmed_date', $inv->confirmed_date ? $this->sma->hrsd($inv->confirmed_date) : ''), 'class="form-control" id="confirmed_date_display"'); ?>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <?= lang('confirmed_time', 'confirmed_time'); ?>
                            <?= form_input('confirmed_time', set_value('confirmed_time', $inv->confirmed_time), 'type="time" class="form-control" id="confirmed_time"'); ?>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <?= form_submit('edit_appointment', lang('submit'), 'class="btn btn-primary"'); ?>
                    <a href="<?= admin_url('appointments'); ?>" class="btn btn-default"><?= lang('back'); ?></a>
                </div>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
</div>
