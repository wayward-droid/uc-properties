<?php
require dirname(__DIR__) . '/includes/bootstrap.php';
require_admin();
$title = 'Enquiries & inspections';
$statuses = ['New', 'Contacted', 'Scheduled', 'Completed', 'Cancelled'];
$error = '';
$id = (int) input('id');
if ($id) {
    $request = row(
        'SELECT r.*,e.name AS estate_name,o.name AS option_name,p.name AS prototype_name FROM requests r LEFT JOIN estates e ON e.id=r.estate_id LEFT JOIN property_options o ON o.id=r.option_id LEFT JOIN prototypes p ON p.id=r.prototype_id WHERE r.id=?',
        [$id],
    );
    if (!$request) {
        not_found();
    }
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        verify_csrf();
        if (
            !in_array(input('status'), $statuses, true) ||
            strlen(input('internal_notes')) > 20000
        ) {
            $error = 'Choose a valid status and keep internal notes under 20,000 characters.';
        } else {
            run('UPDATE requests SET status=?,internal_notes=? WHERE id=?', [
                input('status'),
                input('internal_notes'),
                $id,
            ]);
            flash('Request updated. Internal notes are visible only to staff.');
            go('admin/requests.php?id=' . $id);
        }
    }
} else {
    $where = ['1=1'];
    $params = [];
    if (in_array(input('status'), $statuses, true)) {
        $where[] = 'r.status=?';
        $params[] = input('status');
    }
    if (in_array(input('type'), ['enquiry', 'inspection'], true)) {
        $where[] = 'r.type=?';
        $params[] = input('type');
    }
    $where[] = 'r.is_demo=?';
    $params[] = input('demo') === '1' ? 1 : 0;
    $sql = implode(' AND ', $where);
    $total = (int) row('SELECT COUNT(*) AS n FROM requests r WHERE ' . $sql, $params)['n'];
    $page = max(1, min((int) input('page', '1'), max(1, (int) ceil($total / 25))));
    $requests = rows(
        'SELECT r.*,e.name AS estate_name FROM requests r LEFT JOIN estates e ON e.id=r.estate_id WHERE ' .
            $sql .
            ' ORDER BY r.created_at DESC LIMIT 25 OFFSET ' .
            ($page - 1) * 25,
        $params,
    );
}
include __DIR__ . '/header.php';
?>
<?php
if ($id): ?>
<a class="text-link mb-4" href="<?= e(url('admin/requests.php')) ?>">Back to all requests</a>
<div class="form-card">
<p class="eyebrow"><?= e($request['reference']), $request['is_demo'] ? ' · DEMONSTRATION RECORD' : '' ?></p>
<h2><?= e($request['name']) ?></h2>
<div class="request-details">
<dl>
<dt>Request</dt>
<dd><?= e( tag($request['type']), ) ?></dd>
<dt>Phone</dt>
<dd>
<a href="tel:<?= e( preg_replace('/[^+\d]/', '', $request['phone']), ) ?>"><?= e($request['phone']) ?></a>
</dd>
<dt>Email</dt>
<dd><?= e( $request['email'] ?: 'Not supplied', ) ?></dd>
<dt>Estate / option</dt>
<dd><?= e( ($request['estate_name'] ?? 'General enquiry') . ($request['option_name'] ? ' · ' . $request['option_name'] : ''), ) ?></dd>
<dt>Building design</dt>
<dd><?= e( $request['prototype_name'] ?? 'Not selected', ) ?></dd>
<dt>Preferred visit</dt>
<dd><?= e( $request['preferred_date'] ?: 'Not applicable', ) ?> · <?= e(tag($request['visit_preference'] ?? '')) ?></dd>
<dt>Received</dt>
<dd><?= e( $request['created_at'], ) ?></dd>
<dt>Contact consent</dt>
<dd><?= e( $request['consent_at'], ) ?></dd>
<dt>Email notification</dt>
<dd><?= $request['notification_status'] === 'sent' ? 'Accepted by configured mail transport' : e( tag($request['notification_status']), ) ?></dd>
</dl>
</div>
<h3>Customer message</h3>
<p><?= paragraphs( $request['message'] ?: 'No message supplied.', ) ?></p>
<?php if ($error): ?><div class="notice error" role="alert"><?= e($error) ?></div><?php endif; ?>
<form method="post"><?= csrf() ?><div class="form-grid">
<div class="field">
<label for="status">Request status</label>
<select id="status" name="status"><?php foreach (
    $statuses
    as $status
): ?><option<?= selected($request['status'], $status) ?>><?= e( $status, ) ?></option><?php endforeach; ?></select>
</div>
<div class="field full">
<label for="notes">Private internal notes</label>
<textarea id="notes" name="internal_notes" maxlength="20000"><?= e( $request['internal_notes'], ) ?></textarea>
<small>Record agreed appointments and follow-up details here. These notes are never displayed on the public website.</small>
</div>
<div class="full">
<button class="btn" type="submit">Save request <?= icon( 'check', ) ?></button>
</div>
</div>
</form>
</div>
<?php else: ?>
<form class="filters" method="get">
<div>
<label for="type">Request type</label>
<select id="type" name="type">
<option value="">All requests</option>
<option value="enquiry"<?= selected( input('type'), 'enquiry', ) ?>>Enquiries</option>
<option value="inspection"<?= selected( input('type'), 'inspection', ) ?>>Inspections</option>
</select>
</div>
<div>
<label for="status">Status</label>
<select id="status" name="status">
<option value="">All statuses</option><?php foreach (
    $statuses
    as $status
): ?><option<?= selected(input('status'), $status) ?>><?= e( $status, ) ?></option><?php endforeach; ?></select>
</div>
<div>
<label for="demo">Data</label>
<select id="demo" name="demo">
<option value="0">Real requests</option>
<option value="1"<?= selected( input('demo'), '1', ) ?>>Demonstration only</option>
</select>
</div>
<button type="submit" class="btn btn-small">Filter requests</button>
</form>
<p><?= $total ?> request<?= $total === 1 ? '' : 's' ?></p>
<?php if (
    $requests
): ?><div class="table-wrap">
<table>
<thead>
<tr>
<th>Reference / received</th>
<th>Customer</th>
<th>Request</th>
<th>Estate</th>
<th>Status</th>
</tr>
</thead>
<tbody><?php foreach (
    $requests
    as $r
): ?><tr>
<td>
<a href="<?= e(url('admin/requests.php?id=' . $r['id'])) ?>"><?= e( $r['reference'], ) ?></a>
<br>
<small><?= e($r['created_at']) ?></small>
</td>
<td><?= e($r['name']) ?><br>
<small><?= e( $r['phone'], ) ?></small>
</td>
<td><?= e(tag($r['type'])) ?></td>
<td><?= e( $r['estate_name'] ?? 'General enquiry', ) ?></td>
<td>
<span class="status-tag"><?= e( $r['status'], ) ?></span>
</td>
</tr><?php endforeach; ?></tbody>
</table>
</div><?php else: ?><div class="empty-state">
<h2>No requests here yet.</h2>
<p>Try different filters, or check back after a customer gets in touch.</p>
</div><?php endif; ?>
<?php if ($total > 25): ?><nav class="pagination" aria-label="Request pages"><?php for (
    $p = 1;
    $p <= ceil($total / 25);
    $p++
): ?><a href="<?= e( url('admin/requests.php?' . http_build_query(array_merge($_GET, ['page' => $p]))), ) ?>"<?= $p === $page ? ' aria-current="page"' : '' ?>><?= $p ?></a><?php endfor; ?></nav><?php endif; ?>
<?php endif;
include __DIR__ . '/footer.php';
 ?>
