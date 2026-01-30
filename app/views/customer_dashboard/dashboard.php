<?php defined('BASEPATH') OR exit('No direct script access allowed');
$currency_symbol = (!empty($currency) && isset($currency->symbol)) ? $currency->symbol : (!empty($Settings->default_currency) ? $Settings->default_currency : '€');
$format_amount = function($n) use ($currency_symbol) { return $currency_symbol . ' ' . number_format((float)$n, 2); };
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= html_escape($customer_name) ?> - Customer Dashboard | <?= html_escape($site_name) ?></title>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --bg: #0f0f14;
            --surface: #18181f;
            --surface2: #22222c;
            --text: #e8e8ed;
            --text-muted: #8b8b9a;
            --accent: #6366f1;
            --accent-light: #818cf8;
            --success: #22c55e;
            --warning: #f59e0b;
            --radius: 14px;
            --radius-sm: 8px;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'DM Sans', -apple-system, sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            line-height: 1.5;
        }
        .dashboard {
            max-width: 1100px;
            margin: 0 auto;
            padding: 2rem 1.5rem;
        }
        .header {
            text-align: center;
            margin-bottom: 2.5rem;
        }
        .header-badge {
            display: inline-block;
            background: linear-gradient(135deg, var(--accent), #8b5cf6);
            color: #fff;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            padding: 0.35rem 0.85rem;
            border-radius: 100px;
            margin-bottom: 0.75rem;
        }
        .header h1 {
            font-size: 1.75rem;
            font-weight: 600;
            margin-bottom: 0.25rem;
        }
        .header p {
            color: var(--text-muted);
            font-size: 0.95rem;
        }
        .cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }
        .card {
            background: var(--surface);
            border: 1px solid rgba(255,255,255,0.06);
            border-radius: var(--radius);
            padding: 1.25rem;
            transition: transform 0.2s, border-color 0.2s;
        }
        .card:hover {
            transform: translateY(-2px);
            border-color: rgba(99,102,241,0.3);
        }
        .card-label {
            font-size: 0.8rem;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 0.35rem;
        }
        .card-value {
            font-size: 1.5rem;
            font-weight: 700;
        }
        .card-value.green { color: var(--success); }
        .card-value.amber { color: var(--warning); }
        .card-value.accent { color: var(--accent-light); }
        .section {
            background: var(--surface);
            border: 1px solid rgba(255,255,255,0.06);
            border-radius: var(--radius);
            overflow: hidden;
            margin-bottom: 1.5rem;
        }
        .section-title {
            font-size: 1rem;
            font-weight: 600;
            padding: 1rem 1.25rem;
            border-bottom: 1px solid rgba(255,255,255,0.06);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .section-title i { color: var(--accent); }
        .table-wrap {
            overflow-x: auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 0.85rem 1.25rem;
            text-align: left;
        }
        th {
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--text-muted);
        }
        tr { border-bottom: 1px solid rgba(255,255,255,0.04); }
        tr:last-child { border-bottom: 0; }
        td { font-size: 0.9rem; }
        .status {
            display: inline-block;
            padding: 0.2rem 0.6rem;
            border-radius: 100px;
            font-size: 0.75rem;
            font-weight: 500;
        }
        .status.completed { background: rgba(34,197,94,0.2); color: var(--success); }
        .status.pending { background: rgba(245,158,11,0.2); color: var(--warning); }
        .status.draft { background: rgba(139,139,154,0.2); color: var(--text-muted); }
        .empty-state {
            padding: 2rem;
            text-align: center;
            color: var(--text-muted);
            font-size: 0.9rem;
        }
        .empty-state i { font-size: 2rem; margin-bottom: 0.5rem; opacity: 0.5; }
        .footer-note {
            text-align: center;
            color: var(--text-muted);
            font-size: 0.8rem;
            margin-top: 2rem;
            padding-top: 1rem;
            border-top: 1px solid rgba(255,255,255,0.06);
        }
    </style>
</head>
<body>
    <div class="dashboard">
        <header class="header">
            <span class="header-badge">Customer Portal</span>
            <h1>Welcome, <?= html_escape($customer_name) ?></h1>
            <p>Your invoices, quotes and balance at a glance</p>
        </header>

        <div class="cards">
            <div class="card">
                <div class="card-label">Total Invoiced</div>
                <div class="card-value accent"><?= $format_amount($total_amount) ?></div>
            </div>
            <div class="card">
                <div class="card-label">Total Paid</div>
                <div class="card-value green"><?= $format_amount($paid_amount) ?></div>
            </div>
            <div class="card">
                <div class="card-label">Balance Due</div>
                <div class="card-value amber"><?= $format_amount($balance) ?></div>
            </div>
            <div class="card">
                <div class="card-label">Invoices</div>
                <div class="card-value"><?= (int)$sales_count ?></div>
            </div>
            <div class="card">
                <div class="card-label">Quotes</div>
                <div class="card-value"><?= (int)$quotes_count ?></div>
            </div>
        </div>

        <section class="section">
            <h2 class="section-title"><i class="fas fa-file-invoice-dollar"></i> Recent Invoices</h2>
            <div class="table-wrap">
                <?php if (!empty($sales_list)): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Reference</th>
                            <th>Total</th>
                            <th>Paid</th>
                            <th>Balance</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($sales_list as $s): ?>
                        <tr>
                            <td><?= date($date_format, strtotime($s->date)) ?></td>
                            <td><?= html_escape($s->reference_no) ?></td>
                            <td><?= $format_amount($s->grand_total) ?></td>
                            <td><?= $format_amount($s->paid) ?></td>
                            <td><?= $format_amount(isset($s->balance) ? $s->balance : ($s->grand_total - $s->paid)) ?></td>
                            <td>
                                <span class="status <?= ($s->sale_status == 'completed' || $s->sale_status == 'paid') ? 'completed' : (($s->sale_status == 'returned') ? 'draft' : 'pending') ?>">
                                    <?= html_escape($s->sale_status ?? 'pending') ?>
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php else: ?>
                <div class="empty-state"><i class="fas fa-inbox"></i><br>No invoices yet.</div>
                <?php endif; ?>
            </div>
        </section>

        <section class="section">
            <h2 class="section-title"><i class="fas fa-quote-right"></i> Recent Quotes</h2>
            <div class="table-wrap">
                <?php if (!empty($quotes_list)): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Reference</th>
                            <th>Amount</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($quotes_list as $q): ?>
                        <tr>
                            <td><?= date($date_format, strtotime($q->date)) ?></td>
                            <td><?= html_escape($q->reference_no) ?></td>
                            <td><?= $format_amount($q->grand_total) ?></td>
                            <td><span class="status <?= ($q->status == 'sent' || $q->status == 'accepted') ? 'completed' : 'pending' ?>"><?= html_escape($q->status ?? 'draft') ?></span></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php else: ?>
                <div class="empty-state"><i class="fas fa-inbox"></i><br>No quotes yet.</div>
                <?php endif; ?>
            </div>
        </section>

        <p class="footer-note"><?= html_escape($site_name) ?> · Customer dashboard · Access by your code</p>
    </div>
</body>
</html>
