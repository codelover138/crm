<?php defined('BASEPATH') OR exit('No direct script access allowed');
$company_name = $biller->company != '-' ? $biller->company : $biller->name;
$customer_name = ($customer->name && $customer->last_name) ? $customer->name . ' ' . $customer->last_name : $customer->company;
$customer_id_display = '#' . $inv->id . '/' . date('mdY', strtotime($inv->date)) . '/' . $inv->reference_no;
$balance_due = $inv->grand_total - $inv->paid;
$associate_name = $created_by ? ($created_by->first_name . ' ' . $created_by->last_name) : '';
$contact_phone = !empty($biller->phone) && $biller->phone != '-' ? $biller->phone : '';
$contact_email = !empty($biller->email) && $biller->email != '-' ? $biller->email : '';
$sale_date_formatted = $this->sma->hrld($inv->date);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= lang('sale') . ' ' . $inv->reference_no; ?></title>
    <style>
    * {
        box-sizing: border-box;
    }

    body {
        font-family: Arial, sans-serif;
        font-size: 14px;
        color: #333;
        background: #f0f0f0;
        margin: 0;
        padding: 24px;
    }

    .doc-card {
        max-width: 900px;
        margin: 0 auto;
        background: #fff;
        /* border: 1px solid #e0e0e0; */
        border-radius: 2px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.06);
        padding: 24px;
    }

    @media print {
        body {
            background: #fff;
            padding: 0;
        }

        .doc-card {
            border: none;
            box-shadow: none;
            padding: 0;
        }
    }

    .toolbar {
        display: flex;
        justify-content: flex-end;
        margin-bottom: 16px;
        max-width: 900px;
        margin-left: auto;
        margin-right: auto;
    }

    .btn-print {
        padding: 8px 20px;
        background: #2c5282;
        color: #fff;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 14px;
    }

    .btn-print:hover {
        background: #1a365d;
    }

    @media print {
        .toolbar {
            display: none !important;
        }

        body {
            padding: 0;
        }
    }

    .header {
        margin-bottom: 24px;
    }

    .header-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .header-brand {
        display: flex;
        align-items: center;
        gap: 14px;
    }

    .logo {
        max-height: 80px;
        width: auto;
        display: block;
    }

    .header .company-name {
        margin-top: 0;
        margin-bottom: 0;
    }

    .header-details {
        margin-top: 10px;
    }

    .customer-id-wrap {
        text-align: right;
    }

    .customer-id-label {
        font-size: 12px;
        color: #666;
    }

    .customer-id {
        font-size: 20px;
        font-weight: bold;
        color: #000;
    }

    .company-name {
        font-size: 22px;
        font-weight: bold;
        color: #1e3a5f;
        margin-bottom: 6px;
    }

    .company-info {
        font-size: 16px;
        color: #333;
        line-height: 1.4;
    }

    .support-phone {
        color: #c53030;
        font-weight: bold;
        font-size: 22px;
    }

    .invoice-meta {
        text-align: right;
        font-size: 16px;
    }

    .invoice-meta p {
        margin: 6px 0;
    }

    .two-col {
        font-size: 16px;
    }

    .bill-to {
        font-size: 16px;
        color: #666;
        margin-bottom: 6px;
    }

    .bill-to-name {
        font-weight: bold;
        font-size: 16px;
        margin-bottom: 6px;
    }

    .two-col .left .company-info {
        font-size: 16px;
        line-height: 1.25;
        margin: 0;
        padding: 0;
    }

    .section-divider {
        border: none;
        border-top: 1px solid #d1d5db;
        margin: 20px 0;
    }

    table.items {
        width: 100%;
        border-collapse: collapse;
        margin-top: 8px;
    }

    table.items th {
        text-align: left;
        padding: 12px 10px;
        font-size: 12px;
        color: #6b7280;
        font-weight: 600;
        background: #f3f4f6;
        border-bottom: 1px solid #e5e7eb;
    }

    table.items th.qty,
    table.items th.rate,
    table.items th.amount {
        text-align: right;
    }

    table.items td {
        padding: 12px 8px;
        border-bottom: 1px solid #f3f4f6;
        vertical-align: top;
    }

    table.items td.qty,
    table.items td.rate,
    table.items td.amount {
        text-align: right;
    }

    .item-name {
        font-weight: bold;
        color: #000;
    }

    .item-desc {
        font-size: 12px;
        color: #6b7280;
        margin-top: 4px;
    }

    .totals {
        margin-top: 20px;
        text-align: right;
    }

    .totals-row {
        margin: 6px 0;
        font-size: 14px;
    }

    .totals-row strong {
        display: inline-block;
        min-width: 120px;
        text-align: left;
    }

    .footer-auth {
        text-align: center;
        margin-top: 32px;
        padding-top: 16px;
        font-weight: bold;
        font-size: 13px;
    }

    .two-col {
        display: flex;
        justify-content: space-between;
        gap: 24px;
        margin-bottom: 20px;
    }

    .two-col .left {
        flex: 1;
    }

    .two-col .right {
        flex: 1;
        text-align: right;
    }

    .auth-doc {
        margin-top: 32px;
        padding-top: 24px;
        border-top: 1px solid #1e3a5f;
    }

    .auth-doc h2 {
        text-align: center;
        font-size: 18px;
        margin-bottom: 16px;
        color: #000;
    }

    .auth-doc .intro {
        margin-bottom: 20px;
        font-size: 13px;
        line-height: 1.5;
    }

    .auth-doc .section {
        margin-bottom: 18px;
    }

    .auth-doc .section-title {
        font-weight: bold;
        font-size: 14px;
        margin-bottom: 6px;
    }

    .auth-doc .section p {
        margin: 4px 0;
        font-size: 13px;
        line-height: 1.5;
    }

    .auth-doc .info-fields {
        margin: 12px 0;
        font-size: 13px;
    }

    .auth-doc .info-fields .field {
        margin: 8px 0;
    }

    .auth-doc .info-fields .field-label {
        font-weight: bold;
    }

    .auth-doc .info-fields .field-value {
        border-bottom: 1px solid #333;
        display: inline-block;
        min-width: 200px;
        padding: 2px 4px;
    }

    .auth-doc .proof-link {
        color: #2563eb;
    }

    .auth-doc .signature-block {
        margin-top: 24px;
    }

    .auth-doc .signature-block .signature-label {
        font-weight: bold;
        margin-right: 8px;
    }

    .auth-doc .signature-blank {
        display: inline-block;
        min-width: 280px;
        border-bottom: 1px solid #333;
        padding-bottom: 2px;
        vertical-align: bottom;
    }

    .auth-doc .closing {
        margin-top: 24px;
        font-size: 13px;
    }

    .auth-doc .closing .regards {
        margin-top: 16px;
    }
    </style>
</head>

<body>
    <div class="toolbar">
        <button type="button" class="btn-print"
            onclick="window.print();"><?= lang('print') ? lang('print') : 'Print'; ?></button>
    </div>

    <div class="doc-card">
        <div class="header">
            <div class="header-row">
                <div class="header-brand">
                    <?php if (!empty($biller->logo)) { ?>
                    <img src="<?= base_url('assets/uploads/logos/' . $biller->logo); ?>" alt="<?= $company_name; ?>"
                        class="logo">
                    <?php } ?>

                </div>
                <div class="customer-id-wrap">
                    <div class="customer-id-label">Customer ID</div>
                    <div class="customer-id"><?= $customer_id_display; ?></div>
                </div>
            </div>
            <hr class="section-divider">
            <div class="header-details">
                <?php if (!empty($biller->cf1) && $biller->cf1 != '-') { ?>
                <div class="company-info"><?= $biller->cf1; ?></div>
                <?php } ?>
                <?php if (!empty($Settings->site_name)) { ?>
                <div class="company-name"><?= strtoupper($company_name); ?></div>
                <div class="company-info">www.geekofstates.com</div>
                <?php } ?>

                <?php if (!empty($biller->email) && $biller->email != '-') { ?>
                <div class="company-info"><?= $biller->email; ?></div>
                <?php } ?>
                <?php if (!empty($biller->phone) && $biller->phone != '-') { ?>
                <div class="company-info"><?= lang('Customer Support'); ?>: <span
                        class="support-phone"><?= $biller->phone; ?></span>
                </div>
                <?php } ?>
            </div>
        </div>


        <div class="two-col">
            <div class="left">
                <div class="bill-to"><b>Bill To:</b></div>
                <div class="bill-to-name"><?= $customer_name; ?></div>
                <div class="company-info">
                    <?= preg_replace('/\s*[\r\n]+\s*/', ' ', trim($customer->address)); ?><br>
                    <?= $customer->city . ' ' . $customer->state . ' ' . $customer->postal_code; ?><br>
                    <?= $customer->country; ?>
                    <?= $customer->phone; ?><br> <?= $customer->email; ?>

                </div>
            </div>
            <div class="right">
                <div class="invoice-meta">
                    <p><strong><?= lang('date'); ?>:</strong> <?= $this->sma->hrld($inv->date); ?></p>
                    <p><strong>Invoice No:</strong> #<?= $inv->reference_no; ?></p>
                    <?php if ($associate_name) { ?>
                    <p><strong>Associate Person:</strong> <?= $associate_name; ?></p>
                    <?php } ?>
                    <p><strong>Balance Due:</strong> <?= $this->sma->formatMoney($balance_due); ?></p>
                </div>
            </div>
        </div>

        <hr class="section-divider">

        <table class="items">
            <thead>
                <tr>
                    <th>Item</th>
                    <th class="qty">Quantity</th>
                    <th class="rate">Rate</th>
                    <th class="amount">Amount</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($rows as $row): ?>
                <tr>
                    <td>
                        <span
                            class="item-name"><?= $row->product_name . ($row->variant ? ' (' . $row->variant . ')' : ''); ?></span>
                        <?php if ($row->details) { ?>
                        <div class="item-desc"><?= $row->details; ?></div>
                        <?php } ?>
                    </td>
                    <td class="qty">
                        <?= $this->sma->formatQuantity($row->unit_quantity) . ($row->product_unit_code ? ' ' . $row->product_unit_code : ''); ?>
                    </td>
                    <td class="rate"><?= $this->sma->formatMoney($row->unit_price); ?></td>
                    <td class="amount"><?= $this->sma->formatMoney($row->subtotal); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <hr class="section-divider">

        <div class="totals">
            <div class="totals-row"><strong>Total:</strong> <?= $this->sma->formatMoney($inv->grand_total); ?></div>
            <div class="totals-row"><strong>Amount Paid: </strong><?= $this->sma->formatMoney($inv->paid); ?></div>
        </div>

        <div class="auth-doc">
            <h2>Authorization for Computer Service Payment - <?= htmlspecialchars($company_name); ?></h2>
            <p class="intro">This document confirms the agreement between the undersigned client and
                <?= htmlspecialchars($company_name); ?> for computer service. By electronically signing this document,
                you acknowledge your consent to the terms stated below:</p>

            <div class="section">
                <div class="section-title">Payment Authorization:</div>
                <p>I hereby authorize <?= htmlspecialchars($company_name); ?> to charge my provided credit card for the
                    total cost of the computer service as outlined above. I understand that this payment will be
                    processed immediately upon my confirmation. The credit card details are as follows:</p>
            </div>

            <div class="section">
                <div class="section-title" style="font-size: 15px;">Please fill this information:</div>
                <div class="info-fields">
                    <div class="field"><span class="field-label">Cardholder Name (as shown on card):</span> <span
                            class="field-value"><?= htmlspecialchars($customer_name); ?></span></div>
                    <div class="field"><span class="field-label">Last 4 digit of Card Number:</span> <span
                            class="field-value"><?= isset($card_last4) ? htmlspecialchars($card_last4) : '&nbsp;'; ?></span></div>
                    <div class="field"><span class="field-label">Card Type:</span> <span
                            class="field-value"><?= isset($card_type) ? htmlspecialchars($card_type) : '&nbsp;'; ?></span></div>
                    <div class="field"><span class="field-label">Zip Code:</span> <span
                            class="field-value"><?= htmlspecialchars($customer->postal_code ? $customer->postal_code : ''); ?></span>
                    </div>
                </div>
            </div>

            <div class="section">
                <div class="section-title">Service Delivery Confirmation:</div>
                <p>I acknowledge that the computer service has been delivered by <?= strtoupper($company_name); ?> as of
                    <?= $sale_date_formatted; ?>. I understand that this service is non-refundable due to its
                    completion.</p>
            </div>

            <div class="section">
                <div class="section-title">Proof of Service Delivery:</div>
                <p class="proof-link">This document serves as proof of service delivery and can be presented as evidence
                    in case of any dispute or query related to the provided computer service.</p>
            </div>

            <div class="section">
                <div class="section-title">Credit Card Statement Information:</div>
                <p>The charge for this service will appear as <?= strtoupper($company_name); ?> on your credit card
                    statement.</p>
            </div>

            <div class="section">
                <div class="section-title">Contact Information:</div>
                <p>For any queries or concerns, please feel free to contact our customer service at
                    <?= $contact_phone ? htmlspecialchars($contact_phone) : '—'; ?> or email us at:
                    <?= $contact_email ? htmlspecialchars($contact_email) : '—'; ?></p>
            </div>

            <div class="section">
                <div class="section-title">Electronic Signature:</div>
                <p>By signing below, I confirm that I have read and understood the terms of this agreement, including
                    the payment authorization, service delivery confirmation, and contact information.</p>
                <div class="signature-block">
                    <span class="signature-label">Signature :</span><span class="signature-blank">&nbsp;</span>
                </div>
                <div class="signature-block">
                    <span class="signature-label">Date :</span><span class="signature-blank">&nbsp;</span>
                </div>
            </div>

            <div class="section" style="margin-top: 20px;">
                <div class="section-title">Agreement Confirmation:</div>
                <p>Upon receiving your electronically signed agreement, <?= strtoupper($company_name); ?> will consider
                    the service payment confirmed and delivered. A copy of this agreement will be sent to you via email
                    for your records.</p>
                <p>If you have any questions or concerns about this agreement, please contact us at
                    <?= $contact_phone ? htmlspecialchars($contact_phone) : '—'; ?> or email us at:
                    <?= $contact_email ? htmlspecialchars($contact_email) : '—'; ?></p>
                <p>By providing your electronic signature, you indicate your acceptance of the terms outlined above.</p>
                <div class="signature-block">
                    <span class="signature-label">Signature :</span><span class="signature-blank">&nbsp;</span>
                </div>
            </div>

            <div class="closing">
                <p>Thank you for choosing <?= strtoupper($company_name); ?> for your computer service needs. We are
                    committed to providing you with excellent service and support.</p>
                <p class="regards">Best regards,</p>
                <p><strong><?= strtoupper($company_name); ?></strong></p>
                <p>Email us at: <?= $contact_email ? htmlspecialchars($contact_email) : '—'; ?></p>
            </div>
        </div>
    </div>
</body>

</html>