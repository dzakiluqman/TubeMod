<div class="container">
    <h2>Analysis Result</h2>

    <?php if (!empty($data['comments'])): ?>
        <div class="scroll-box">
            <?php foreach ($data['comments'] as $c): ?>
                <div class="comment-card">

                    <div class="comment-content">
                        <h4><?= htmlspecialchars($c['author']); ?></h4>

                        <p>
                            <?= htmlspecialchars($c['text']); ?><br>

                            <span style="color:red;">
                                ⚠ <?= $c['category']; ?> (<?= $c['matched_keyword']; ?>)
                            </span>
                        </p>
                    </div>

                    <?php if ($data['isOwner']): ?>
                        <a href="<?= BASEURL ?>/analyze/delete/<?= $c['id']; ?>" class="delete-text">
                            Delete
                        </a>
                    <?php else: ?>
                        <span style="color:gray;">Not Owner</span>
                    <?php endif; ?>

                </div>
            <?php endforeach; ?>
        </div>

        <?php if ($data['isOwner']): ?>
            <form method="POST" action="<?= BASEURL ?>/analyze/deleteAll">
                <button type="submit" class="btn-danger">
                    Delete all sensitive comments
                </button>
            </form>
        <?php endif; ?>

    <?php else: ?>
        <p>No comments found or invalid video.</p>
    <?php endif; ?>
</div>