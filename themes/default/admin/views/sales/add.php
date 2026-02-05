<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<script type="text/javascript">
var count = 1,
    an = 1,
    product_variant = 0,
    DT = <?= $Settings->default_tax_rate ?>,
    product_tax = 0,
    invoice_tax = 0,
    product_discount = 0,
    order_discount = 0,
    total_discount = 0,
    total = 0,
    allow_discount = <?= ($Owner || $Admin || $this->session->userdata('allow_discount')) ? 1 : 0; ?>,
    tax_rates = <?php echo json_encode($tax_rates); ?>;
//var audio_success = new Audio('<?=$assets?>sounds/sound2.mp3');
//var audio_error = new Audio('<?=$assets?>sounds/sound3.mp3');
$(document).ready(function() {
    if (localStorage.getItem('remove_slls')) {
        if (localStorage.getItem('slitems')) {
            localStorage.removeItem('slitems');
        }
        if (localStorage.getItem('sldiscount')) {
            localStorage.removeItem('sldiscount');
        }
        if (localStorage.getItem('sltax2')) {
            localStorage.removeItem('sltax2');
        }
        if (localStorage.getItem('slref')) {
            localStorage.removeItem('slref');
        }
        if (localStorage.getItem('slshipping')) {
            localStorage.removeItem('slshipping');
        }
        if (localStorage.getItem('slwarehouse')) {
            localStorage.removeItem('slwarehouse');
        }
        if (localStorage.getItem('slnote')) {
            localStorage.removeItem('slnote');
        }
        if (localStorage.getItem('slinnote')) {
            localStorage.removeItem('slinnote');
        }
        if (localStorage.getItem('slcustomer')) {
            localStorage.removeItem('slcustomer');
        }
        if (localStorage.getItem('slbiller')) {
            localStorage.removeItem('slbiller');
        }
        if (localStorage.getItem('slcurrency')) {
            localStorage.removeItem('slcurrency');
        }
        if (localStorage.getItem('sldate')) {
            localStorage.removeItem('sldate');
        }
        if (localStorage.getItem('slsale_status')) {
            localStorage.removeItem('slsale_status');
        }
        if (localStorage.getItem('slpayment_status')) {
            localStorage.removeItem('slpayment_status');
        }
        if (localStorage.getItem('paid_by')) {
            localStorage.removeItem('paid_by');
        }
        if (localStorage.getItem('amount_1')) {
            localStorage.removeItem('amount_1');
        }
        if (localStorage.getItem('paid_by_1')) {
            localStorage.removeItem('paid_by_1');
        }
        if (localStorage.getItem('pcc_holder_1')) {
            localStorage.removeItem('pcc_holder_1');
        }
        if (localStorage.getItem('pcc_type_1')) {
            localStorage.removeItem('pcc_type_1');
        }
        if (localStorage.getItem('pcc_month_1')) {
            localStorage.removeItem('pcc_month_1');
        }
        if (localStorage.getItem('pcc_year_1')) {
            localStorage.removeItem('pcc_year_1');
        }
        if (localStorage.getItem('pcc_no_1')) {
            localStorage.removeItem('pcc_no_1');
        }
        if (localStorage.getItem('cheque_no_1')) {
            localStorage.removeItem('cheque_no_1');
        }
        if (localStorage.getItem('payment_note_1')) {
            localStorage.removeItem('payment_note_1');
        }
        if (localStorage.getItem('slpayment_term')) {
            localStorage.removeItem('slpayment_term');
        }
        localStorage.removeItem('remove_slls');
    }
    <?php if($quote_id) { ?>
    // localStorage.setItem('sldate', '<?= $this->sma->hrld($quote->date) ?>');
    localStorage.setItem('slcustomer', '<?= $quote->customer_id ?>');
    localStorage.setItem('slbiller', '<?= $quote->biller_id ?>');
    localStorage.setItem('slwarehouse', '<?= $quote->warehouse_id ?>');
    localStorage.setItem('slnote',
        '<?= str_replace(array("\r", "\n"), "", $this->sma->decode_html($quote->note)); ?>');
    localStorage.setItem('sldiscount', '<?= $quote->order_discount_id ?>');
    localStorage.setItem('sltax2', '<?= $quote->order_tax_id ?>');
    localStorage.setItem('slshipping', '<?= $quote->shipping ?>');
    localStorage.setItem('slitems', JSON.stringify(<?= $quote_items; ?>));
    <?php } ?>
    <?php if($this->input->get('customer')) { ?>
    if (!localStorage.getItem('slitems')) {
        localStorage.setItem('slcustomer', <?=$this->input->get('customer');?>);
    }
    <?php } ?>
    <?php if ($Owner || $Admin) { ?>
    if (!localStorage.getItem('sldate')) {
        $("#sldate").datetimepicker({
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
    $(document).on('change', '#sldate', function(e) {
        localStorage.setItem('sldate', $(this).val());
    });
    if (sldate = localStorage.getItem('sldate')) {
        $('#sldate').val(sldate);
    }
    <?php } ?>
    $(document).on('change', '#slbiller', function(e) {
        localStorage.setItem('slbiller', $(this).val());
    });
    if (slbiller = localStorage.getItem('slbiller')) {
        $('#slbiller').val(slbiller);
    }
    if (!localStorage.getItem('slref')) {
        localStorage.setItem('slref', '<?=$slnumber?>');
    }
    if (!localStorage.getItem('sltax2')) {
        localStorage.setItem('sltax2', <?=$Settings->default_tax_rate2;?>);
    }
    ItemnTotals();
    $('.bootbox').on('hidden.bs.modal', function(e) {
        $('#add_item').focus();
    });
    $("#add_item").autocomplete({
        source: function(request, response) {
            console.log($('#slcustomer').val());
            if (!$('#slcustomer').val()) {
                $('#add_item').val('').removeClass('ui-autocomplete-loading');
                bootbox.alert('<?=lang('select_above');?>');
                $('#add_item').focus();
                return false;
            }
            $.ajax({
                type: 'get',
                url: '<?= admin_url('sales/suggestions'); ?>',
                dataType: "json",
                data: {
                    term: request.term,
                    warehouse_id: $("#slwarehouse").val(),
                    customer_id: $("#slcustomer").val()
                },
                success: function(data) {
                    $(this).removeClass('ui-autocomplete-loading');
                    response(data);
                }
            });
        },
        minLength: 1,
        autoFocus: false,
        delay: 250,
        response: function(event, ui) {
            if ($(this).val().length >= 16 && ui.content[0].id == 0) {
                bootbox.alert('<?= lang('no_match_found') ?>', function() {
                    $('#add_item').focus();
                });
                $(this).removeClass('ui-autocomplete-loading');
                $(this).removeClass('ui-autocomplete-loading');
                $(this).val('');
            } else if (ui.content.length == 1 && ui.content[0].id != 0) {
                ui.item = ui.content[0];
                $(this).data('ui-autocomplete')._trigger('select', 'autocompleteselect', ui);
                $(this).autocomplete('close');
                $(this).removeClass('ui-autocomplete-loading');
            } else if (ui.content.length == 1 && ui.content[0].id == 0) {
                bootbox.alert('<?= lang('no_match_found') ?>', function() {
                    $('#add_item').focus();
                });
                $(this).removeClass('ui-autocomplete-loading');
                $(this).val('');
            }
        },
        select: function(event, ui) {
            event.preventDefault();
            if (ui.item.id !== 0) {
                var row = add_invoice_item(ui.item);
                if (row)
                    $(this).val('');
            } else {
                bootbox.alert('<?= lang('no_match_found') ?>');
            }
        }
    });

    $('#slpayment_status').change(function() {
        var ps = $(this).val();
        if (ps === 'partial' || ps === 'paid') {
            $('#payments').slideDown();
            if (ps === 'paid' && typeof gtotal !== 'undefined') {
                $('#amount_1').val(formatDecimal(gtotal));
            } else if (ps === 'paid' && $('#gtotal').length) {
                var gt = parseFloat($('#gtotal').text().replace(/[^0-9.-]/g, '')) || 0;
                $('#amount_1').val(formatDecimal(gt));
            }
            renderPaidBySection();
        } else {
            $('#payments').slideUp();
        }
    });
    var psInit = $('#slpayment_status').val();
    if (psInit === 'partial' || psInit === 'paid') {
        $('#payments').show();
        renderPaidBySection();
    }

    function renderPaidBySection() {
        var p_val = $('#paid_by_1').val();
        $('.pcc_1').hide();
        $('.pcheque_1').hide();
        if (p_val === 'CC') {
            $('.pcc_1').show();
            $('#pcc_no_1').focus();
        } else if (p_val === 'Cheque') {
            $('.pcheque_1').show();
            $('#cheque_no_1').focus();
        } else {
            $('#payment_note_1').focus();
        }
    }
    $('#paid_by_1').change(function() {
        renderPaidBySection();
    });
});
</script>

<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-plus"></i><?= lang('add_sale'); ?></h2>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">

                <p class="introtext"><?php echo lang('enter_info'); ?></p>
                <?php
                $attrib = array('data-toggle' => 'validator', 'role' => 'form');
                echo admin_form_open_multipart("sales/add", $attrib);
                if ($quote_id) {
                    echo form_hidden('quote_id', $quote_id);
                }
                ?>
                <div class="row">
                    <div class="col-lg-12">
                        <?php
                        // Hidden fields for biller and warehouse (if session values exist)
                        if ($this->session->userdata('biller_id')) {
                            $biller_input = array(
                                'type' => 'hidden',
                                'name' => 'biller',
                                'id' => 'slbiller',
                                'value' => $this->session->userdata('biller_id'),
                            );
                            echo form_input($biller_input);
                        } else {
                            // Use default biller
                            echo form_hidden('biller', $Settings->default_biller);
                        }
                        
                        if ($this->session->userdata('warehouse_id')) {
                            $warehouse_input = array(
                                'type' => 'hidden',
                                'name' => 'warehouse',
                                'id' => 'slwarehouse',
                                'value' => $this->session->userdata('warehouse_id'),
                            );
                            echo form_input($warehouse_input);
                        } else {
                            // Use default warehouse
                            echo form_hidden('warehouse', $Settings->default_warehouse);
                        }
                        ?>

                        <?php if ($Owner || $Admin) { ?>
                        <div class="col-md-4">
                            <div class="form-group">
                                <?= lang("date", "sldate"); ?>
                                <?php echo form_input('date', (isset($_POST['date']) ? $_POST['date'] : ""), 'class="form-control input-tip datetime" id="sldate" required="required"'); ?>
                            </div>
                        </div>
                        <?php } ?>

                        <div class="col-md-4">
                            <div class="form-group">
                                <?= lang("reference_no", "slref"); ?>
                                <?php echo form_input('reference_no', (isset($_POST['reference_no']) ? $_POST['reference_no'] : $slnumber), 'class="form-control input-tip" id="slref"'); ?>
                            </div>
                        </div>

                        <div class="clearfix"></div>
                        <div class="col-md-12">
                            <div class="panel panel-warning">
                                <div class="panel-heading"><?= lang('please_select_these_before_adding_product') ?>
                                </div>
                                <div class="panel-body" style="padding: 5px;">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <?= lang("customer", "slcustomer"); ?>
                                            <div class="input-group">
                                                <?php
                                                echo form_input('customer', (isset($_POST['customer']) ? $_POST['customer'] : ""), 'id="slcustomer" data-placeholder="' . lang("select") . ' ' . lang("customer") . '" required="required" class="form-control input-tip" style="width:100%;"');
                                                ?>
                                                <div class="input-group-addon no-print"
                                                    style="padding: 2px 8px; border-left: 0;">
                                                    <a href="#" id="toogle-customer-read-attr" class="external">
                                                        <i class="fa fa-pencil" id="addIcon"
                                                            style="font-size: 1.2em;"></i>
                                                    </a>
                                                </div>
                                                <div class="input-group-addon no-print"
                                                    style="padding: 2px 7px; border-left: 0;">
                                                    <a href="#" id="view-customer" class="external" data-toggle="modal"
                                                        data-target="#myModal">
                                                        <i class="fa fa-eye" id="addIcon" style="font-size: 1.2em;"></i>
                                                    </a>
                                                </div>
                                                <?php if ($Owner || $Admin || $GP['customers-add']) { ?>
                                                <div class="input-group-addon no-print"
                                                    style="padding: 2px 8px; border-left: 0;">
                                                    <a href="#" id="quick-add-customer" class="external"
                                                        data-toggle="modal" data-target="#quickCustomerModal"
                                                        title="<?= lang('quick_add_customer') ?>">
                                                        <i class="fa fa-plus-circle" id="addIcon"
                                                            style="font-size: 1.2em;"></i>
                                                    </a>
                                                </div>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="col-md-12" id="sticker">
                            <div class="well well-sm">
                                <div class="form-group" style="margin-bottom:0;">
                                    <div class="input-group wide-tip">
                                        <div class="input-group-addon" style="padding-left: 10px; padding-right: 10px;">
                                            <i class="fa fa-2x fa-barcode addIcon"></i></a>
                                        </div>
                                        <?php echo form_input('add_item', '', 'class="form-control input-lg" id="add_item" placeholder="' . lang("add_product_to_order") . '"'); ?>
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="control-group table-group">
                                <label class="table-label"><?= lang("order_items"); ?> *</label>

                                <div class="controls table-controls">
                                    <table id="slTable"
                                        class="table items table-striped table-bordered table-condensed table-hover sortable_table">
                                        <thead>
                                            <tr>
                                                <th class="col-md-4">
                                                    <?= lang('product') . ' (' . lang('code') .' - '.lang('name') . ')'; ?>
                                                </th>
                                                <?php
                                            if ($Settings->product_serial) {
                                                echo '<th class="col-md-2">' . lang("serial_no") . '</th>';
                                            }
                                            ?>
                                                <th class="col-md-1"><?= lang("net_unit_price"); ?></th>
                                                <th class="col-md-1"><?= lang("quantity"); ?></th>
                                                <?php
                                            if ($Settings->product_discount && ($Owner || $Admin || $this->session->userdata('allow_discount'))) {
                                                echo '<th class="col-md-1">' . lang("discount") . '</th>';
                                            }
                                            ?>
                                                <?php if (true && $Settings->tax1) {
                                                echo '<th class="col-md-1">' . lang("product_tax") . '</th>';
                                            } ?>
                                                <th>
                                                    <?= lang("subtotal"); ?>
                                                    (<span class="currency"><?= $default_currency->code ?></span>)
                                                </th>
                                                <th style="width: 30px !important; text-align: center;">
                                                    <i class="fa fa-trash-o"
                                                        style="opacity:0.5; filter:alpha(opacity=50);"></i>
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                        <tfoot></tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="clearfix"></div>


                        <?php
                        // Hidden fields for removed form fields (needed for backend compatibility)
                        echo form_hidden('order_tax', '');
                        echo form_hidden('order_discount', '');
                        echo form_hidden('shipping', '0');
                        echo form_hidden('sale_status', 'pending');
                        echo form_hidden('payment_term', '');
                        echo form_hidden('note', '');
                        echo form_hidden('staff_note', '');
                        ?>

                        <?php if(isset($assign_provider) || isset($assign_marketing_officer)) { ?>
                        <div class="clearfix"></div>
                        <div class="row">
                            <div class="col-lg-12">
                                <?php if(isset($assign_provider)){ ?>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="assign_service_provider"><?= lang('Tech_Person', 'Tech Person'); ?>
                                        </label>
                                        <?php
                                    $ts[''] = lang('select').' Tech Person';
                                    foreach ($assign_provider as $provider) {
                                        $ts[$provider->id] = $provider->first_name. ' '.$provider->last_name;
                                    }
                                    $service_provider_value = isset($inv->service_provider) ? $inv->service_provider : '';
                                    echo form_dropdown('assign_service_provider', $ts, $service_provider_value, 'class="form-control input-tip select" id="assign_service_provider" style="width:100%;" required="required"');
                                    ?>
                                    </div>
                                </div>
                                <?php }?>
                                <?php if(isset($assign_marketing_officer)){ ?>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label
                                            for="assign_marketing_officers"><?= lang('Sales_Person', 'Sales Person'); ?>
                                        </label>
                                        <?php
                                    $so[''] = lang('select').' Sales Person';
                                    foreach ($assign_marketing_officer as $officer) {
                                        $so[$officer->id] = $officer->first_name. ' '.$officer->last_name;
                                    }
                                    $marketing_officer_value = (isset($inv->assign_marketing_officers) && $inv->assign_marketing_officers) ? $inv->assign_marketing_officers : (isset($default_assign_marketing_officers) ? $default_assign_marketing_officers : '');
                                    echo form_dropdown('assign_marketing_officers', $so, $marketing_officer_value, 'class="form-control input-tip select" id="assign_marketing_officers" style="width:100%;" required="required"');
                                    ?>
                                    </div>
                                </div>
                                <?php }?>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label
                                            for="support_duration"><?= lang('Support_duration', 'Support Duration'); ?>

                                            (<?= lang('days', 'days'); ?>)</label>
                                        <input type="number" name="support_duration" id="support_duration" min="0"
                                            step="1" class="form-control input-tip" required="required"
                                            value="<?= isset($_POST['support_duration']) ? $_POST['support_duration'] : '' ?>" />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php }?>

                        <div class="clearfix"></div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="slpayment_status"><?= lang("payment_status"); ?></label>
                                <?php
                                $pst = array('pending' => lang('pending'), 'due' => lang('due'), 'partial' => lang('partial'), 'paid' => lang('paid'));
                                echo form_dropdown('payment_status', $pst, isset($_POST['payment_status']) ? $_POST['payment_status'] : 'pending', 'class="form-control input-tip" required="required" id="slpayment_status"');
                                ?>
                            </div>
                        </div>

                        <div id="payments" class="col-md-12" style="display: none;">
                            <div class="well well-sm">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label
                                                for="payment_reference_no"><?= lang("payment_reference_no"); ?></label>
                                            <?= form_input('payment_reference_no', isset($_POST['payment_reference_no']) ? $_POST['payment_reference_no'] : (isset($payment_ref) ? $payment_ref : ''), 'class="form-control" id="payment_reference_no"'); ?>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="amount_1"><?= lang("amount"); ?></label>
                                            <input name="amount-paid" type="text" id="amount_1"
                                                class="form-control kb-pad amount" />
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="paid_by_1"><?= lang("paying_by", "paid_by_1"); ?></label>
                                            <select name="paid_by" id="paid_by_1" class="form-control paid_by">
                                                <?= $this->sma->paid_opts(isset($_POST['paid_by']) ? $_POST['paid_by'] : null, false, true); ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                                <div class="pcc_1" style="display:none;">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <input name="pcc_no" type="text" id="pcc_no_1" class="form-control"
                                                    placeholder="<?= lang('cc_no'); ?>" />
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <input name="pcc_holder" type="text" id="pcc_holder_1"
                                                    class="form-control" placeholder="<?= lang('cc_holder'); ?>" />
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <select name="pcc_type" id="pcc_type_1" class="form-control">
                                                    <option value="Visa"><?= lang('Visa'); ?></option>
                                                    <option value="MasterCard"><?= lang('MasterCard'); ?></option>
                                                    <option value="Amex"><?= lang('Amex'); ?></option>
                                                    <option value="Discover"><?= lang('Discover'); ?></option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <input name="pcc_month" type="text" id="pcc_month_1"
                                                    class="form-control" placeholder="<?= lang('month'); ?>" />
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <input name="pcc_year" type="text" id="pcc_year_1" class="form-control"
                                                    placeholder="<?= lang('year'); ?>" />
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <input name="pcc_ccv" type="text" id="pcc_cvv2_1" class="form-control"
                                                    placeholder="<?= lang('cvv2'); ?>" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="pcheque_1" style="display:none;">
                                    <div class="form-group">
                                        <label for="cheque_no_1"><?= lang('cheque_no'); ?></label>
                                        <input name="cheque_no" type="text" id="cheque_no_1" class="form-control" />
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="payment_note_1"><?= lang('payment_note'); ?></label>
                                            <textarea name="payment_note" id="payment_note_1" class="form-control"
                                                rows="2"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <input type="hidden" name="total_items" value="" id="total_items" required="required" />
                        <div class="col-md-12">
                            <div class="fprom-group">
                                <?php echo form_submit('add_sale', lang("submit"), 'id="add_sale" class="btn btn-primary" style="padding: 6px 15px; margin:15px 0;"'); ?>
                                <button type="button" class="btn btn-danger" id="reset"><?= lang('reset') ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="bottom-total" class="well well-sm" style="margin-bottom: 0;">
                    <table class="table table-bordered table-condensed totals" style="margin-bottom:0;">
                        <tr class="warning">
                            <td><?= lang('items') ?> <span class="totals_val pull-right" id="titems">0</span></td>
                            <td><?= lang('total') ?> <span class="totals_val pull-right" id="total">0.00</span></td>
                            <td><?= lang('grand_total') ?> <span class="totals_val pull-right" id="gtotal">0.00</span>
                            </td>
                        </tr>
                    </table>
                </div>

                <?php echo form_close(); ?>

            </div>

        </div>
    </div>
</div>

<div class="modal" id="prModal" tabindex="-1" role="dialog" aria-labelledby="prModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true"><i
                            class="fa fa-2x">&times;</i></span><span class="sr-only"><?=lang('close');?></span></button>
                <h4 class="modal-title" id="prModalLabel"></h4>
            </div>
            <div class="modal-body" id="pr_popover_content">
                <form class="form-horizontal" role="form">
                    <?php if ($Settings->tax1) { ?>
                    <div class="form-group">
                        <label class="col-sm-4 control-label"><?= lang('product_tax') ?></label>
                        <div class="col-sm-8">
                            <?php
                                $tr[""] = "";
                                foreach ($tax_rates as $tax) {
                                    $tr[$tax->id] = $tax->name;
                                }
                                echo form_dropdown('ptax', $tr, "", 'id="ptax" class="form-control pos-input-tip" style="width:100%;"');
                                ?>
                        </div>
                    </div>
                    <?php } ?>
                    <?php if ($Settings->product_serial) { ?>
                    <div class="form-group">
                        <label for="pserial" class="col-sm-4 control-label"><?= lang('serial_no') ?></label>

                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="pserial">
                        </div>
                    </div>
                    <?php } ?>
                    <div class="form-group">
                        <label for="pquantity" class="col-sm-4 control-label"><?= lang('quantity') ?></label>

                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="pquantity">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="punit" class="col-sm-4 control-label"><?= lang('product_unit') ?></label>
                        <div class="col-sm-8">
                            <div id="punits-div"></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="poption" class="col-sm-4 control-label"><?= lang('product_option') ?></label>
                        <div class="col-sm-8">
                            <div id="poptions-div"></div>
                        </div>
                    </div>
                    <?php if ($Settings->product_discount && ($Owner || $Admin || $this->session->userdata('allow_discount'))) { ?>
                    <div class="form-group">
                        <label for="pdiscount" class="col-sm-4 control-label"><?= lang('product_discount') ?></label>

                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="pdiscount">
                        </div>
                    </div>
                    <?php } ?>
                    <div class="form-group">
                        <label for="pprice" class="col-sm-4 control-label"><?= lang('unit_price') ?></label>

                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="pprice"
                                <?= ($Owner || $Admin || $GP['edit_price']) ? '' : 'readonly'; ?>>
                        </div>
                    </div>
                    <table class="table table-bordered table-striped">
                        <tr>
                            <th style="width:25%;"><?= lang('net_unit_price'); ?></th>
                            <th style="width:25%;"><span id="net_price"></span></th>
                            <?php if (false) { ?><th style="width:25%;"><?= lang('product_tax'); ?></th>
                            <th style="width:25%;"><span id="pro_tax"></span></th><?php } ?>
                        </tr>
                    </table>
                    <input type="hidden" id="punit_price" value="" />
                    <input type="hidden" id="old_tax" value="" />
                    <input type="hidden" id="old_qty" value="" />
                    <input type="hidden" id="old_price" value="" />
                    <input type="hidden" id="row_id" value="" />
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="editItem"><?= lang('submit') ?></button>
            </div>
        </div>
    </div>
</div>

<div class="modal" id="productDetailsModal" tabindex="-1" role="dialog" aria-labelledby="productDetailsModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true"><i
                            class="fa fa-2x">&times;</i></span><span class="sr-only"><?=lang('close');?></span></button>
                <h4 class="modal-title" id="productDetailsModalLabel"></h4>
            </div>
            <div class="modal-body" id="pr_popover_content">
                <form class="form-horizontal" role="form">

                    <div class="form-group">

                        <div class="col-sm-10 margin05" id="productDetails">
                        </div>
                    </div>


                </form>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal"><?=lang('close');?></button>
            </div>
        </div>
    </div>
</div>

<div class="modal" id="mModal" tabindex="-1" role="dialog" aria-labelledby="mModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true"><i
                            class="fa fa-2x">&times;</i></span><span class="sr-only"><?=lang('close');?></span></button>
                <h4 class="modal-title" id="mModalLabel"><?= lang('add_product_manually') ?></h4>
            </div>
            <div class="modal-body" id="pr_popover_content">
                <form class="form-horizontal" role="form">
                    <div class="form-group">
                        <label for="mcode" class="col-sm-4 control-label"><?= lang('product_code') ?> *</label>

                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="mcode">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="mname" class="col-sm-4 control-label"><?= lang('product_name') ?> *</label>

                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="mname">
                        </div>
                    </div>
                    <?php if ($Settings->tax1) { ?>
                    <div class="form-group">
                        <label for="mtax" class="col-sm-4 control-label"><?= lang('product_tax') ?> *</label>

                        <div class="col-sm-8">
                            <?php
                                $tr[""] = "";
                                foreach ($tax_rates as $tax) {
                                    $tr[$tax->id] = $tax->name;
                                }
                                echo form_dropdown('mtax', $tr, "", 'id="mtax" class="form-control input-tip select" style="width:100%;"');
                                ?>
                        </div>
                    </div>
                    <?php } ?>
                    <div class="form-group">
                        <label for="mquantity" class="col-sm-4 control-label"><?= lang('quantity') ?> *</label>

                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="mquantity">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="munit" class="col-sm-4 control-label"><?= lang('unit') ?> *</label>

                        <div class="col-sm-8">
                            <?php
                            $uts[""] = "";
                            foreach ($units as $unit) {
                                $uts[$unit->id] = $unit->name;
                            }
                            echo form_dropdown('munit', $uts, "", 'id="munit" class="form-control input-tip select" style="width:100%;"');
                            ?>
                        </div>
                    </div>
                    <?php if ($Settings->product_discount && ($Owner || $Admin || $this->session->userdata('allow_discount'))) { ?>
                    <div class="form-group">
                        <label for="mdiscount" class="col-sm-4 control-label"><?= lang('product_discount') ?></label>

                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="mdiscount">
                        </div>
                    </div>
                    <?php } ?>
                    <div class="form-group">
                        <label for="mprice" class="col-sm-4 control-label"><?= lang('unit_price') ?> *</label>

                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="mprice">
                        </div>
                    </div>
                    <table class="table table-bordered table-striped">
                        <tr>
                            <th style="width:25%;"><?= lang('net_unit_price'); ?></th>
                            <th style="width:25%;"><span id="mnet_price"></span></th>
                            <?php if (false) { ?><th style="width:25%;"><?= lang('product_tax'); ?></th>
                            <th style="width:25%;"><span id="mpro_tax"></span></th><?php } ?>
                        </tr>
                    </table>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="addItemManually"><?= lang('submit') ?></button>
            </div>
        </div>
    </div>
</div>

<div class="modal" id="gcModal" tabindex="-1" role="dialog" aria-labelledby="mModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i
                        class="fa fa-2x">&times;</i></button>
                <h4 class="modal-title" id="myModalLabel"><?= lang('sell_gift_card'); ?></h4>
            </div>
            <div class="modal-body">
                <p><?= lang('enter_info'); ?></p>

                <div class="alert alert-danger gcerror-con" style="display: none;">
                    <button data-dismiss="alert" class="close" type="button">×</button>
                    <span id="gcerror"></span>
                </div>
                <div class="form-group">
                    <?= lang("card_no", "gccard_no"); ?> *
                    <div class="input-group">
                        <?php echo form_input('gccard_no', '', 'class="form-control" id="gccard_no"'); ?>
                        <div class="input-group-addon" style="padding-left: 10px; padding-right: 10px;"><a href="#"
                                id="genNo"><i class="fa fa-cogs"></i></a></div>
                    </div>
                </div>
                <input type="hidden" name="gcname" value="<?= lang('gift_card') ?>" id="gcname" />

                <div class="form-group">
                    <?= lang("value", "gcvalue"); ?> *
                    <?php echo form_input('gcvalue', '', 'class="form-control" id="gcvalue"'); ?>
                </div>
                <div class="form-group">
                    <?= lang("price", "gcprice"); ?> *
                    <?php echo form_input('gcprice', '', 'class="form-control" id="gcprice"'); ?>
                </div>
                <div class="form-group">
                    <?= lang("customer", "gccustomer"); ?>
                    <?php echo form_input('gccustomer', '', 'class="form-control" id="gccustomer"'); ?>
                </div>
                <div class="form-group">
                    <?= lang("expiry_date", "gcexpiry"); ?>
                    <?php echo form_input('gcexpiry', $this->sma->hrsd(date("Y-m-d", strtotime("+2 year"))), 'class="form-control date" id="gcexpiry"'); ?>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" id="addGiftCard" class="btn btn-primary"><?= lang('sell_gift_card') ?></button>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
$(document).ready(function() {
    $('#gccustomer').select2({
        minimumInputLength: 1,
        ajax: {
            url: site.base_url + "customers/suggestions",
            dataType: 'json',
            quietMillis: 15,
            data: function(term, page) {
                return {
                    term: term,
                    limit: 10
                };
            },
            results: function(data, page) {
                if (data.results != null) {
                    return {
                        results: data.results
                    };
                } else {
                    return {
                        results: [{
                            id: '',
                            text: 'No Match Found'
                        }]
                    };
                }
            }
        }
    });
    $('#genNo').click(function() {
        var no = generateCardNo();
        $(this).parent().parent('.input-group').children('input').val(no);
        return false;
    });
});
</script>

<!-- Quick Customer Creation Modal -->
<div class="modal" id="quickCustomerModal" tabindex="-1" role="dialog" aria-labelledby="quickCustomerModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i
                        class="fa fa-2x">&times;</i></button>
                <h4 class="modal-title" id="quickCustomerModalLabel"><?= lang('quick_add_customer'); ?></h4>
            </div>
            <div class="modal-body">
                <p><?= lang('enter_info'); ?></p>
                <div class="alert alert-danger qcerror-con" style="display: none;">
                    <button data-dismiss="alert" class="close" type="button">×</button>
                    <span id="qcerror"></span>
                </div>
                <form id="quick-customer-form" role="form">
                    <div class="form-group">
                        <?= lang("name", "qcname"); ?>
                        <?php echo form_input('name', '', 'class="form-control" id="qcname" required="required" placeholder="' . lang("name") . '"'); ?>
                    </div>
                    <div class="form-group">
                        <?= lang("email_address", "qcemail"); ?>
                        <input type="email" name="email" class="form-control" required="required" id="qcemail"
                            placeholder="<?= lang("email_address") ?>" />
                    </div>
                    <div class="form-group">
                        <?= lang("phone", "qcphone"); ?>
                        <input type="tel" name="phone" class="form-control" required="required" id="qcphone"
                            placeholder="<?= lang("phone") ?>" />
                    </div>
                    <div class="form-group">
                        <?= lang("address", "qcaddress"); ?>
                        <?php echo form_textarea('address', '', 'class="form-control" id="qcaddress" required="required" rows="3" placeholder="' . lang("address") . '"'); ?>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?= lang('close'); ?></button>
                <button type="button" id="saveQuickCustomer" class="btn btn-primary"><?= lang('save') ?></button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
$(document).ready(function() {
    $('#saveQuickCustomer').on('click', function() {
        var name = $('#qcname').val();
        var email = $('#qcemail').val();
        var phone = $('#qcphone').val();
        var address = $('#qcaddress').val();

        if (!name || !email || !phone || !address) {
            $('.qcerror-con').show();
            $('#qcerror').html('<?= lang("please_fill_all_fields") ?>');
            return false;
        }

        $('#saveQuickCustomer').prop('disabled', true).html('<?= lang("saving") ?>...');

        $.ajax({
            type: "POST",
            url: site.base_url + "customers/add_quick",
            dataType: "json",
            data: {
                name: name,
                email: email,
                phone: phone,
                address: address,
                <?= $this->security->get_csrf_token_name() ?>: '<?= $this->security->get_csrf_hash() ?>'
            },
            success: function(data) {
                $('#saveQuickCustomer').prop('disabled', false).html(
                    '<?= lang("save") ?>');
                if (data.error) {
                    $('.qcerror-con').show();
                    $('#qcerror').html(data.error);
                } else if (data.customer_id && data.customer_name) {
                    $('#quickCustomerModal').modal('hide');
                    // Clear any previous error
                    $('.qcerror-con').hide();

                    // Store customer ID in localStorage for persistence
                    localStorage.setItem('slcustomer', data.customer_id);

                    // Get the customer field
                    var $customerField = $('#slcustomer');

                    // Create new option with customer data
                    var newOption = new Option(data.customer_name, data.customer_id, true,
                        true);

                    // If Select2 is initialized, use Select2 API
                    if ($customerField.data('select2')) {
                        // Add option if it doesn't exist
                        if ($customerField.find('option[value="' + data.customer_id + '"]')
                            .length === 0) {
                            $customerField.append(newOption);
                        }
                        // Select the option using Select2
                        $customerField.val(data.customer_id).trigger('change.select2');
                    } else {
                        // Select2 not initialized yet, just append and set value
                        $customerField.append(newOption);
                        $customerField.val(data.customer_id);
                    }

                    // Reset form
                    $('#quick-customer-form')[0].reset();
                    // Show success message
                    bootbox.alert('<?= lang("customer_added") ?>');
                } else {
                    $('.qcerror-con').show();
                    $('#qcerror').html('<?= lang("error_occurred") ?>');
                }
            },
            error: function(xhr, status, error) {
                $('#saveQuickCustomer').prop('disabled', false).html(
                    '<?= lang("save") ?>');
                $('.qcerror-con').show();
                var errorMsg = '<?= lang("error_occurred") ?>';
                if (xhr.responseJSON && xhr.responseJSON.error) {
                    errorMsg = xhr.responseJSON.error;
                } else if (xhr.responseText) {
                    try {
                        var response = JSON.parse(xhr.responseText);
                        if (response.error) {
                            errorMsg = response.error;
                        }
                    } catch (e) {
                        errorMsg = xhr.responseText.substring(0, 200);
                    }
                }
                $('#qcerror').html(errorMsg);
            }
        });
    });

    $('#quickCustomerModal').on('hidden.bs.modal', function() {
        $('#quick-customer-form')[0].reset();
        $('.qcerror-con').hide();
        $('#qcerror').html('');
        $('#saveQuickCustomer').prop('disabled', false).html('<?= lang("save") ?>');
    });
});
</script>