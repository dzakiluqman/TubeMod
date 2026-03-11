<?php require 'layouts/header.php'; ?>

<section class="hero">
    <h1>One tool to manage toxic comments<br>in your YouTube channels.</h1>
    <p>Automatically analyze spam comments, online gambling, and hate speech.</p>

    <form action="<?= BASEURL ?>/analyze" method="POST" class="search-form">
        <input type="text" name="youtube_url" placeholder="Insert a YouTube video URL" required>
        <button type="submit">Start Analyze</button>
    </form>

    <div class="preview-images">
    <div class="card">
        <img src="<?= BASEURL ?>/assets/image 2.png" alt="Analyze">
    </div>
    <div class="card">
        <img src="<?= BASEURL ?>/assets/image 3.png" alt="Toxic Detection">
    </div>
    <div class="card">
        <img src="<?= BASEURL ?>/assets/image 4.png" alt="Moderation">
    </div>
</div>
</section>

<section class="trusted">
    <p>Trusted by more than 10+ creators</p>

    <div class="logos">
        <span>YouTube</span>
        <span>Google</span>
        <span>YouTube</span>
        <span>Google</span>
        <span>YouTube</span>
    </div>

    <div class="testimonial">
        <h3>
        “A single video could have thousands of comments, and filtering out spam and online gambling comments was incredibly tedious. With this system, I simply enter the video link and within seconds I receive a list of comments to review.”
        </h3>
        <div class="profile">
            <div class="avatar"></div>
            <div>
                <strong>Salwa Fijri</strong><br>
                <span>Content Creator (1M+ Subscribers)</span>
            </div>
        </div>
    </div>

    <div class="stats">
        <div>
            <h2>2026</h2>
            <p>TubeMod Founded</p>
        </div>
        <div>
            <h2>1k+</h2>
            <p>Active Users</p>
        </div>
        <div>
            <h2>10+</h2>
            <p>Creators trust</p>
        </div>
    </div>
</section>

<section class="cta">
    <h2>Discover the full scale of<br>TubeMod capabilities</h2>
    <a href="<?= BASEURL ?>/auth/login">Start now</a>
</section>

<?php require 'layouts/footer.php'; ?>