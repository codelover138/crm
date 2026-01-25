<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                <i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel">
                <?= $customer->company && $customer->company != '-' ? $customer->company : $customer->name; ?></h4>
        </div>
        <div class="modal-body">
            <div class="table-responsive">
                <table class="table table-striped table-bordered" style="margin-bottom:0;">
                    <tbody>


                        <tr>
                            <td><strong><?= lang(line: "Code"); ?></strong></td>
                            <td><?= $customer->vat_no; ?></strong></td>
                        </tr>
                        <tr>
                            <td><strong><?= lang("Name"); ?></strong></td>
                            <td><?= $customer->name; ?></strong></td>
                        </tr>

                        <tr>
                            <td><strong><?= lang("email"); ?></strong></td>
                            <td><?= $customer->email; ?></strong></td>
                        </tr>
                        <tr>
                            <td><strong><?= lang("phone"); ?></strong></td>
                            <td><?= $customer->phone; ?></strong></td>
                        </tr>
                        <tr>
                            <td><strong><?= lang("address"); ?></strong></td>
                            <td><?= $customer->address; ?></strong></td>
                        </tr>

                    </tbody>
                </table>
            </div>
            <div class="modal-footer no-print">
                <button type="button" class="btn btn-default pull-left"
                    data-dismiss="modal"><?= lang('close'); ?></button>
                <?php if ($Owner || $Admin || $GP['customers-edit']) { ?>
                <a href="<?=admin_url('customers/edit/'.$customer->id);?>" data-toggle="modal" data-target="#myModal2"
                    class="btn btn-primary"><?= lang('edit_customer'); ?></a>
                <?php } ?>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
</div>