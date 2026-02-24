<?php

class OurWorkView
{
    public function renderList(array $works): string
    {
        ob_start();
        include 'components/header.php';
        include 'components/nav.php';
        ?>
        <div class="container">
            <h2>Our Works</h2>
            <ul>
                <?php foreach ($works as $work): ?>
                    <li>ID: <?= $work['id'] ?>, Title: <?= $work['work_title'] ?></li>
                <?php endforeach; ?>
            </ul>
            <a href="?action=create">Add New Work</a>
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
            <h2>Add Work</h2>
            <form method="post" action="" enctype="multipart/form-data">
                <label>Title: <input type="text" name="title" required></label><br>
                <label>Photo: <input type="file" name="photo"></label><br>
                <button type="submit">Add Work</button>
            </form>
            <a href="?action=index">Back to List</a>
        </div>
        <?php
        include 'components/footer.php';
        return ob_get_clean();
    }
}