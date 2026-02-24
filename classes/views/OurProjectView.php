<?php

class OurProjectView
{
    public function renderList(array $projects): string
    {
        ob_start();
        include 'components/header.php';
        include 'components/nav.php';
        ?>
        <div class="container">
            <h2>Our Projects</h2>
            <ul>
                <?php foreach ($projects as $project): ?>
                    <li>ID: <?= $project['id'] ?>, Name: <?= $project['project_name'] ?>, Description: <?= $project['project_description'] ?? '' ?></li>
                <?php endforeach; ?>
            </ul>
            <a href="?action=create">Add New Project</a>
        </div>
        <?php
        include 'components/footer.php';
        return ob_get_clean();
    }

    public function renderForm(): string
    {
        ob_start();
        include 'components/header.php';
        include 'components/nav.php';
        ?>
        <div class="container">
            <h2>Add Project</h2>
            <form method="post" action="" enctype="multipart/form-data">
                <label>Name: <input type="text" name="name" required></label><br>
                <label>Description: <textarea name="description"></textarea></label><br>
                <label>Photo: <input type="file" name="photo"></label><br>
                <button type="submit">Add Project</button>
            </form>
            <a href="?action=index">Back to List</a>
        </div>
        <?php
        include 'components/footer.php';
        return ob_get_clean();
    }
}