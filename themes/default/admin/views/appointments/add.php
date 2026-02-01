<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<script type="text/javascript">
    $(document).ready(function () {
        $('#slcustomer').select2({
            minimumInputLength: 1,
            placeholder: "<?= lang('select') . ' ' . lang('customer'); ?>",
            ajax: {
                url: "<?= admin_url('customers/suggestions'); ?>",
                dataType: 'json',
                quietMillis: 250,
                data: function (term, page) {
                    return { term: term, limit: 15 };
                },
                results: function (data, page) {
                    if (data.results != null) {
                        return { results: data.results };
                    }
                    return { results: [{ id: '', text: '<?= lang('no_match_found'); ?>' }] };
                }
            }
            <?php if (set_value('customer_id')) { ?>
            ,initSelection: function (element, callback) {
                $.ajax({
                    type: "get",
                    url: "<?= admin_url('customers/getCustomer/'); ?>" + $(element).val(),
                    dataType: "json",
                    success: function (data) {
                        if (data && data[0]) {
                            callback(data[0]);
                        }
                    }
                });
            }
            <?php } ?>
        });
        // Preferred date: same as communication - single datetime input, js_ldate, localStorage
        if (!localStorage.getItem('apdate')) {
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
            }).datetimepicker('update', new Date());
        }
        $(document).on('change', '#apdate', function(e) {
            localStorage.setItem('apdate', $(this).val());
        });
        if (apdate = localStorage.getItem('apdate')) {
            $('#apdate').val(apdate);
        }
    });
</script>
<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-calendar-plus-o"></i><?= lang('add_appointment'); ?></h2>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">
                <p class="introtext"><?= lang('enter_info'); ?></p>
                <?php
                $attrib = array('data-toggle' => 'validator', 'role' => 'form');
                echo admin_form_open_multipart('appointments/add', $attrib);
                ?>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <?= lang('customer', 'slcustomer'); ?>
                            <div class="input-group">
                                <?php
                                echo form_input('customer_id', set_value('customer_id'), 'id="slcustomer" data-placeholder="' . lang("select") . ' ' . lang("customer") . '" required="required" class="form-control input-tip" style="width:100%;"');
                                ?>
                                <div class="input-group-addon no-print" style="padding: 2px 8px;">
                                    <a href="<?= admin_url('customers/add'); ?>" data-toggle="modal" data-target="#myModal" class="tip" title="<?= lang('add_customer'); ?>">
                                        <i class="fa fa-plus-circle" style="font-size: 1.2em;"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <?= lang('appointment_type', 'appointment_type'); ?>
                            <?php
                            $types = $appointment_types;
                            echo form_dropdown('appointment_type', $types, set_value('appointment_type'), 'id="appointment_type" class="form-control select" required="required" style="width:100%;"');
                            ?>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <?= lang('subject', 'subject'); ?>
                            <?= form_input('subject', set_value('subject'), 'class="form-control" id="subject" required="required"'); ?>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <?= lang('description', 'description'); ?>
                            <?= form_textarea('description', set_value('description'), 'class="form-control" id="description" rows="3"'); ?>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <?= lang('date', 'apdate'); ?>
                            <?php echo form_input('preferred_datetime', set_value('preferred_datetime'), 'class="form-control input-tip datetime" id="apdate" required="required"'); ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <?= lang('duration', 'duration_minutes'); ?>
                            <?php echo form_dropdown('duration_minutes', $duration_options, set_value('duration_minutes', 30), 'id="duration_minutes" class="form-control select" style="width:100%;"'); ?>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <?= form_submit('add_appointment', lang('submit'), 'class="btn btn-primary"'); ?>
                </div>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
</div>
