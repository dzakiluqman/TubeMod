<?php require 'layouts/header.php'; ?>

<div class="container">
    <h2>Keyword Management</h2>

    <!-- Filter Checkbox + Apply Button -->
    <div class="filter-wrapper" style="width: 700px; max-width: 100%; margin-top: 20px; align-items: center; gap: 10px;">
        <form method="POST" action="<?= BASEURL ?>/keyword/save_filters" style="margin-top: 20px;">
            <label>
                <input type="checkbox" name="filter_non_original_fonts"
                    <?php if(!empty($_SESSION['filter_non_original_fonts'])) echo 'checked'; ?>>
                Filter non-original YouTube fonts?
            </label>
            <button type="submit" class="btn-primary btn-small">Apply</button>
        </form>
    </div>

    <!-- Add Keyword Form -->
    <form method="POST" action="<?= BASEURL ?>/keyword/add" class="search-form keywords" style="margin-top: 15px; width: 760px; padding: 5px;">
        <input type="text" name="word" placeholder="Keyword" required style="flex:1;">  
        <input type="text" name="category" placeholder="Category (judol/hate)" required style="flex:1; border-left: 1px solid #ccc;">
        <button type="submit" class="btn-primary">Add</button>
    </form>

    <!-- Scroll Box -->
    <div class="scroll-box">
        <?php foreach ($data['keywords'] as $row): ?>
            <div class="comment-card">
                <form method="POST" action="<?= BASEURL ?>/keyword/update" style="display:flex; gap:10px; align-items:center; width: 100%;">
                    <input type="hidden" name="id" value="<?= $row['id'] ?>">

                    <div class="comment-content" style="display:flex; gap: 10px; flex: 1;">
                        <input type="text" name="word" value="<?= htmlspecialchars($row['word']) ?>" class="glass-input">
                        <input type="text" name="category" value="<?= htmlspecialchars($row['category']) ?>" class="glass-input">
                    </div>

                    <button type="submit" class="btn-update">Update</button>
                    <a href="<?= BASEURL ?>/keyword/delete/<?= $row['id'] ?>" class="delete-text">Delete</a>
                </form>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php require 'layouts/footer.php'; ?>