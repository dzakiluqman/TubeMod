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
                            Hidden: <?= $row['hidden_comments'] ?><br>
                            Analyzed on: <?= $row['created_at'] ?>
                        </p>
                    </div>
                    
                    <div class="card-actions">
                        <a onclick="openHistoryDetail('<?= $row['video_id'] ?>')" class="btn-update">Detail</a>
                        <a href="<?= BASEURL ?>/history/delete/<?= $row['id'] ?>" class="delete-text">Delete</a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p style="text-align: center;">No history yet.</p>
        <?php endif; ?>
    </div>
</div>

<!-- Modal Detail History -->
<div id="historyDetailModal" class="modal">
  <div class="modal-content">
    <span class="close">&times;</span>
    
    <!-- Ringkasan Video -->
    <div id="videoSummary">
        <h3 id="videoTitle"></h3>
        <p>Creator: <span id="videoCreator"></span></p>
        <p>
            Total Comments: <span id="totalComments"></span><br>
            Deleted: <span id="deletedComments"></span><br>
            Hidden: <span id="hiddenComments"></span><br>
            Analyzed On: <span id="analyzedOn"></span>
        </p>
    </div>

    <!-- Tabel Komentar Deleted/Hidden -->
    <table id="commentsTable">
        <thead>
            <tr>
                <th>Author</th>
                <th>Comment</th>
                <th>Category</th>
                <th>Status</th>
                <th>Deleted/Hidden At</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
  </div>
</div>

<script>
function openHistoryDetail(video_id) {
    fetch(`<?= BASEURL ?>/history/detail/${video_id}`)
        .then(res => res.json())
        .then(data => {
            document.getElementById('videoTitle').textContent = data.video.video_title;
            document.getElementById('videoCreator').textContent = data.video.creator ?? '';
            document.getElementById('totalComments').textContent = data.video.total_comments;
            document.getElementById('deletedComments').textContent = data.video.deleted_comments;
            document.getElementById('hiddenComments').textContent = data.video.hidden_comments;
            document.getElementById('analyzedOn').textContent = data.video.created_at;

            const tbody = document.querySelector('#commentsTable tbody');
            tbody.innerHTML = '';
            data.comments.forEach(c => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${c.author}</td>
                    <td>${c.comment_text}</td>
                    <td>${c.category}</td>
                    <td>${c.status}</td>
                    <td>${c.deleted_at}</td>
                `;
                tbody.appendChild(tr);
            });

            document.getElementById('historyDetailModal').style.display = 'block';
        });
}

// Close modal
document.querySelector('#historyDetailModal .close').onclick = function() {
    document.getElementById('historyDetailModal').style.display = 'none';
};
</script>

<?php require 'layouts/footer.php'; ?>