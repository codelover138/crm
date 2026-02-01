<?php defined('BASEPATH') OR exit('No direct script access allowed');
$inv = $case;
?>
<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-folder-open"></i><?= lang('edit'); ?> - <?= htmlspecialchars($inv->case_code); ?></h2>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">
                <?php
                $attrib = array('data-toggle' => 'validator', 'role' => 'form');
                echo admin_form_open_multipart('cases/edit/' . $inv->id, $attrib);
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
                            <?= form_input('code', $inv->case_code, 'class="form-control" readonly'); ?>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <?= lang('status', 'status'); ?>
                            <?php echo form_dropdown('status', $status_options, set_value('status', $inv->status), 'id="status" class="form-control select" required="required" style="width:100%;"'); ?>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <?= lang('details', 'details'); ?>
                            <?= form_textarea('details', set_value('details', $inv->details), 'class="form-control" id="details" rows="5" required="required"'); ?>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <?= form_submit('edit_case', lang('submit'), 'class="btn btn-primary"'); ?>
                    <a href="<?= admin_url('cases'); ?>" class="btn btn-default"><?= lang('back'); ?></a>
                </div>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
</div>
