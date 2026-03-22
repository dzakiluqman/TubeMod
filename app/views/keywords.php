<?php require 'layouts/header.php'; ?>

<div class="container">
    <h2>Keyword Management</h2>

    <form method="POST" action="<?= BASEURL ?>/keyword/add" class="search-form" style="width: 700px; padding: 5px;">
        <input type="text" name="word" placeholder="Keyword" required style="flex:1;">
        <input type="text" name="category" placeholder="Category (judol/hate)" required style="flex:1; border-left: 1px solid #ccc;">
        <button type="submit">Add</button>
    </form>

    <div class="scroll-box">
        <?php foreach ($data['keywords'] as $row): ?>
            <div class="comment-card">
                <form method="POST" action="<?= BASEURL ?>/keyword/update" style="display:flex; gap:10px; align-items:center; width: 100%;">
                    <input type="hidden" name="id" value="<?= $row['id'] ?>">
                    
                    <div class="comment-content" style="display:flex; gap: 10px; flex: 1;">
                        <input type="text" name="word" value="<?= htmlspecialchars($row['word']) ?>">
                        <input type="text" name="category" value="<?= htmlspecialchars($row['category']) ?>">
                    </div>

                    <button type="submit">Update</button>
                    <a href="<?= BASEURL ?>/keyword/delete/<?= $row['id'] ?>" class="delete-text">Delete</a>
                </form>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php require 'layouts/footer.php'; ?>