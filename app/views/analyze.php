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

                            <?php if (!empty($c['is_toxic'])): ?>
                                <span style="color:red;">
                                    ⚠ 
                                    <?= $c['category'] ?? 'Toxic'; ?>
                                    <?php if (!empty($c['matched_keyword'])): ?>
                                        (<?= $c['matched_keyword']; ?>)
                                    <?php endif; ?>
                                </span>
                            <?php endif; ?>
                        </p>
                    </div>

                    <?php if (!empty($c['is_toxic'])): ?>

                        <?php if ($data['isLoggedIn'] && $data['isOwner']): ?>
                            <a href="<?= BASEURL ?>/analyze/delete/<?= $c['id']; ?>" class="delete-text">
                                Delete
                            </a>

                        <?php elseif (!$data['isLoggedIn']): ?>
                            <a href="<?= BASEURL ?>/auth" class="delete-text">
                                Login to delete
                            </a>

                        <?php else: ?>
                            <span style="color:gray;">Not Owner</span>
                        <?php endif; ?>

                    <?php endif; ?>

                </div>
            <?php endforeach; ?>

        </div>

        <?php if ($data['isLoggedIn'] && $data['isOwner']): ?>
            <form method="POST" action="<?= BASEURL ?>/analyze/deleteAll">
                <button type="submit" class="btn-danger">
                    Delete all sensitive comments
                </button>
            </form>
        <?php endif; ?>

    <?php else: ?>
        <p>No comments found or invalid video.</p>
    <?php endif; ?>

    <?php if (!empty($data['message'])): ?>
        <p><?= $data['message']; ?></p>
    <?php endif; ?>

</div>