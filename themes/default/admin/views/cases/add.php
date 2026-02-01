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
    });
</script>
<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-folder-open-o"></i><?= lang('add_case'); ?></h2>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">
                <p class="introtext"><?= lang('enter_info'); ?></p>
                <?php
                $attrib = array('data-toggle' => 'validator', 'role' => 'form');
                echo admin_form_open_multipart('cases/add', $attrib);
                ?>
                <div class="row">
                    <div class="col-md-12">
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
                    <div class="col-md-12">
                        <div class="form-group">
                            <?= lang('details', 'details'); ?>
                            <?= form_textarea('details', set_value('details'), 'class="form-control" id="details" rows="5" required="required"'); ?>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <?= form_submit('add_case', lang('submit'), 'class="btn btn-primary"'); ?>
                </div>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
</div>
