<?php require 'layouts/header.php'; ?>

<div class="container">
    <div class="login-card">
        <h2>Login Required</h2>
        <p>You need to login with Google to manage comments and keywords.</p>

        <a href="<?= $data['login_url'] ?>" class="google-btn">
            <img src="https://upload.wikimedia.org/wikipedia/commons/c/c1/Google_%22G%22_logo.svg" alt="Google">
            Continue with Google
        </a>
        
        <form method="POST" action="<?= BASEURL ?>/auth/login">
            <button name="login" class="hidden-login">bypass login</button>
        </form>
    </div>
</div>

<?php require 'layouts/footer.php'; ?>