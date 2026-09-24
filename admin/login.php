<?php
require dirname(__DIR__) . '/includes/bootstrap.php';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    if (!rate_limit('admin-login', 8, 900)) {
        $error = 'Too many attempts. Please try again in 15 minutes.';
    } else {
        $admin = row('SELECT * FROM admins WHERE email=? AND active=1', [
            strtolower(input('email')),
        ]);
        // The fallback hash keeps an unknown account on the same password-check path.
        $hash =
            $admin['password_hash'] ??
            '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.';
        if (
            password_verify(
                is_string($_POST['password'] ?? null) ? $_POST['password'] : '',
                $hash,
            ) &&
            $admin
        ) {
            session_regenerate_id(true);
            $_SESSION['admin_id'] = (int) $admin['id'];
            $_SESSION['admin_seen'] = time();
            $_SESSION['csrf'] = bin2hex(random_bytes(32));
            go('admin/');
        } else {
            $error = 'The email or password is incorrect.';
        }
    }
}
$title = 'Staff sign in';
include dirname(__DIR__) . '/includes/header.php';
?>
<main id="main" class="container section reading">
    <p class="eyebrow">UC PROPERTIES STAFF</p>
    <h1>Welcome back.</h1>
    <p>Sign in to manage estates and customer requests.</p>
    <?php if (
    $error
): ?>
    <div class="notice error" role="alert"><?= e( $error, ) ?></div>
    <?php endif; ?>
    <form method="post" class="form-card">
        <div class="form-grid">
            <?= csrf() ?>
            <div class="field full">
                <label for="email">Email address</label>
                <input
                    id="email"
                    name="email"
                    type="email"
                    autocomplete="username"
                    required
                    maxlength="190"
                    value="<?= e( input('email'), ) ?>"
                />
            </div>
            <div class="field full">
                <label for="password">Password</label>
                <input
                    id="password"
                    name="password"
                    type="password"
                    autocomplete="current-password"
                    required
                    maxlength="200"
                />
            </div>
            <div class="full">
                <button class="btn" type="submit">Sign in <?= icon( 'arrow', ) ?></button>
            </div>
        </div>
    </form>
    <p class="mt-4">
        <small>Staff access only. Contact your website administrator if you need an account.</small>
    </p>
</main>
<?php include dirname(
    __DIR__,
) . '/includes/footer.php'; ?>
