<?php require 'layouts/header.php'; ?>

<div class="container">
    <h2>Analysis History</h2>

    <div class="scroll-box">
        <?php if (!empty($data['history'])): ?>
            <?php foreach ($data['history'] as $row): ?>
                <div class="comment-card">
                    <div class="comment-content">
                        <h4><?= htmlspecialchars($row['video_title']) ?></h4>
                        
                        <p style="margin-bottom: 5px;">
                            <a href="https://www.youtube.com/watch?v=<?= $row['video_id'] ?>" target="_blank" style="color: #8bb4f5; text-decoration: none;">
                                https://www.youtube.com/watch?v=<?= $row['video_id'] ?>
                            </a>
                        </p>
                        
                        <p>
                            Total Comments: <?= $row['total_comments'] ?><br>
                            Deleted: <?= $row['deleted_comments'] ?><br>
                            Analyzed on: <?= $row['created_at'] ?>
                        </p>
                    </div>
                    
                    <!-- OPTIONAL: nanti bisa dipakai -->
                    <a href="#" class="delete-text">Delete</a>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p style="text-align: center;">No history yet.</p>
        <?php endif; ?>
    </div>
</div>

<?php require 'layouts/footer.php'; ?>