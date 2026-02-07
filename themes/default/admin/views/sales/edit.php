<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php /* Set localStorage and edit-sale customer data immediately so sales.js sees them when it runs */ ?>
<script type="text/javascript">
<?php if (isset($inv) && $inv) { ?>
localStorage.setItem('sldate', '<?= $this->sma->hrld($inv->date) ?>');
localStorage.setItem('slcustomer', '<?= (int)$inv->customer_id ?>');
localStorage.setItem('slbiller', '<?= $inv->biller_id ?>');
localStorage.setItem('slref', '<?= addslashes($inv->reference_no); ?>');
localStorage.setItem('slwarehouse', '<?= $inv->warehouse_id ?>');
localStorage.setItem('slsale_status', '<?= addslashes($inv->sale_status); ?>');
localStorage.setItem('slpayment_status', '<?= addslashes($inv->payment_status); ?>');
localStorage.setItem('slpayment_term', '<?= addslashes($inv->payment_term); ?>');
localStorage.setItem('slnote', <?= json_encode($this->sma->decode_html($inv->note)); ?>);
localStorage.setItem('slinnote', <?= json_encode($this->sma->decode_html($inv->staff_note)); ?>);
localStorage.setItem('sldiscount', '<?= $inv->order_discount_id ?>');
localStorage.setItem('sltax2', '<?= $inv->order_tax_id ?>');
localStorage.setItem('slshipping', '<?= $inv->shipping ?>');
(function() {
    try {
        var raw = <?= isset($inv_items) ? $inv_items : '""'; ?>;
        localStorage.setItem('slitems', typeof raw === 'object' && raw !== null ? JSON.stringify(raw) : (typeof raw === 'string' ? raw : '{}'));
    } catch (e) {
        console.error('Edit sale: failed to set slitems', e);
    }
})();
<?php if (!empty($inv->customer_id) && trim($inv->customer) !== '') { ?>
window.__editSaleCustomer = { id: '<?= (int)$inv->customer_id ?>', text: <?= json_encode(trim($inv->customer)); ?> };
<?php } ?>
<?php } ?>
</script>
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
    allow_discount =
    <?= ($Owner || $Admin || $this->session->userdata('allow_discount') || $inv->order_discount_id) ? 1 : 0; ?>,
    tax_rates = <?php echo json_encode($tax_rates); ?>;
//var audio_success = new Audio('<?=$assets?>sounds/sound2.mp3');
//var audio_error = new Audio('<?=$assets?>sounds/sound3.mp3');
$(document).ready(function() {
    var slitemsVal = localStorage.getItem('slitems');
    if (slitemsVal && typeof loadItems === 'function') {
        try { loadItems(); } catch (e) { console.error('loadItems', e); }
    }
    setTimeout(function() {
        if (localStorage.getItem('slitems') && typeof loadItems === 'function' && $('#slTable tbody').length && !$('#slTable tbody').children().length) {
            try { loadItems(); } catch (e) { console.error('loadItems retry', e); }
        }
    }, 350);
    <?php if ($Owner || $Admin) { ?>
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
    ItemnTotals();
    $("#add_item").autocomplete({
        source: function(request, response) {
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

});
</script>


<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-plus"></i><?= lang('edit_sale'); ?></h2>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">

                <p class="introtext"><?php echo lang('enter_info'); ?></p>
                <?php
                $attrib = array('data-toggle' => 'validator', 'role' => 'form', 'class' => 'edit-so-form');
                echo admin_form_open_multipart("sales/edit/" . $inv->id, $attrib)
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
                            // Use invoice biller
                            echo form_hidden('biller', $inv->biller_id);
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
                            // Use invoice warehouse
                            echo form_hidden('warehouse', $inv->warehouse_id);
                        }
                        ?>

                        <?php if ($Owner || $Admin) { ?>
                        <div class="col-md-4">
                            <div class="form-group">
                                <?= lang("date", "sldate"); ?>
                                <?php echo form_input('date', (isset($_POST['date']) ? $_POST['date'] : $this->sma->hrld($inv->date)), 'class="form-control input-tip datetime" id="sldate" required="required"'); ?>
                            </div>
                        </div>
                        <?php } ?>

                        <div class="col-md-4">
                            <div class="form-group">
                                <?= lang("reference_no", "slref"); ?>
                                <?php echo form_input('reference_no', (isset($_POST['reference_no']) ? $_POST['reference_no'] : $inv->reference_no), 'class="form-control input-tip" id="slref"'); ?>
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
                                                echo form_input('customer', (isset($_POST['customer']) ? $_POST['customer'] : $inv->customer_id), 'id="slcustomer" data-placeholder="' . lang("select") . ' ' . lang("customer") . '" required="required" class="form-control input-tip" style="width:100%;"');
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
                                        <?php if ($Owner || $Admin || $GP['products-add']) { ?>
                                        <div class="input-group-addon" style="padding-left: 10px; padding-right: 10px;">
                                            <a href="#" id="addManually">
                                                <i class="fa fa-2x fa-plus-circle addIcon" id="addIcon"></i>
                                            </a>
                                        </div>
                                        <?php } ?>
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
                                                <?php
                                            if (true && $Settings->tax1) {
                                                echo '<th class="col-md-1">' . lang("product_tax") . '</th>';
                                            }
                                            ?>
                                                <th><?= lang("subtotal"); ?> (<span
                                                        class="currency"><?= $default_currency->code ?></span>)
                                                </th>
                                                <th style="width: 30px !important; text-align: center;"><i
                                                        class="fa fa-trash-o"
                                                        style="opacity:0.5; filter:alpha(opacity=50);"></i></th>
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
                        echo form_hidden('order_discount', $inv->order_discount_id ? $inv->order_discount_id : '');
                        echo form_hidden('shipping', $inv->shipping ? $inv->shipping : '0');
                        echo form_hidden('sale_status', $inv->sale_status ? $inv->sale_status : 'pending');
                        echo form_hidden('payment_term', $inv->payment_term ? $inv->payment_term : '');
                        echo form_hidden('note', $inv->note ? $inv->note : '');
                        echo form_hidden('staff_note', $inv->staff_note ? $inv->staff_note : '');
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
                                            for="assign_marketing_officers"><?= lang('sales_person', 'sales person'); ?>
                                        </label>
                                        <?php
                                    $so[''] = lang('select').' sales person';
                                    foreach ($assign_marketing_officer as $officer) {
                                        $so[$officer->id] = $officer->first_name. ' '.$officer->last_name;
                                    }
                                    $marketing_officer_value = isset($inv->assign_marketing_officers) ? $inv->assign_marketing_officers : '';
                                    echo form_dropdown('assign_marketing_officers', $so, $marketing_officer_value, 'class="form-control input-tip select" id="assign_marketing_officers" style="width:100%;" required="required"');
                                    ?>
                                    </div>
                                </div>
                                <?php }?>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label
                                            for="support_duration"><?= lang('Support_duration', 'Support Duration'); ?>
                                            *
                                            (<?= lang('days', 'days'); ?>)</label>
                                        <input type="number" name="support_duration" id="support_duration" min="0"
                                            step="1" class="form-control input-tip" required="required"
                                            value="<?= isset($_POST['support_duration']) ? $_POST['support_duration'] : (isset($inv->support_duration) ? $inv->support_duration : '') ?>" />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php }?>

                        <div class="clearfix"></div>
                        <?php echo form_hidden('payment_status', $inv->payment_status ? $inv->payment_status : 'pending'); ?>

                        <input type="hidden" name="total_items" value="" id="total_items" required="required" />
                        <div class="col-md-12">
                            <div class="fprom-group">
                                <?php echo form_submit('edit_sale', lang("submit"), 'id="edit_sale" class="btn btn-primary" style="padding: 6px 15px; margin:15px 0;"'); ?>
                                <button type="button" class="btn btn-danger" id="reset"><?= lang('reset') ?></button>
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
                            class="fa fa-2x">&times;</i></span><span class="sr-only">Close</span></button>
                <h4 class="modal-title" id="prModalLabel"></h4>
            </div>
            <div class="modal-body" id="pr_popover_content">
                <form class="form-horizontal" role="form">
                    <?php if (false && $Settings->tax1) { ?>
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
                    <?php if ($Settings->product_discount) { ?>
                    <div class="form-group">
                        <label for="pdiscount" class="col-sm-4 control-label"><?= lang('product_discount') ?></label>

                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="pdiscount"
                                <?= $allow_discount ? '' : 'readonly="true"'; ?>>
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
                            class="fa fa-2x">&times;</i></span><span class="sr-only">Close</span></button>
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
                    <?php if (false && $Settings->tax1) { ?>
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
                    <?php if ($Settings->product_serial) { ?>
                    <div class="form-group">
                        <label for="mserial" class="col-sm-4 control-label"><?= lang('product_serial') ?></label>

                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="mserial">
                        </div>
                    </div>
                    <?php } ?>
                    <?php if ($Settings->product_discount) { ?>
                    <div class="form-group">
                        <label for="mdiscount" class="col-sm-4 control-label">
                            <?= lang('product_discount') ?>
                        </label>

                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="mdiscount"
                                <?= $allow_discount ? '' : 'readonly="true"'; ?>>
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