<?php

class TeamView
{
    public function renderList(array $teams): string
    {
        ob_start();
        include 'components/header.php';
        include 'components/nav.php';
        ?>
        <div class="container">
            <h2>Team</h2>
            <ul>
                <?php foreach ($teams as $team): ?>
                    <li>ID: <?= $team['id'] ?>, Name: <?= $team['name'] ?>, Position: <?= $team['position'] ?></li>
                <?php endforeach; ?>
            </ul>
            <a href="?action=create">Add New Team Member</a>
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
            <h2>Add Team Member</h2>
            <form method="post" action="" enctype="multipart/form-data">
                <label>Name: <input type="text" name="name" required></label><br>
                <label>Position: <input type="text" name="position" required></label><br>
                <label>Photo: <input type="file" name="photo"></label><br>
                <label>Facebook: <input type="url" name="facebook"></label><br>
                <label>Instagram: <input type="url" name="instagram"></label><br>
                <label>Twitter: <input type="url" name="twitter"></label><br>
                <label>LinkedIn: <input type="url" name="linkedin"></label><br>
                <button type="submit">Add Team Member</button>
            </form>
            <a href="?action=index">Back to List</a>
        </div>
        <?php
        include 'components/footer.php';
        return ob_get_clean();
    }
}