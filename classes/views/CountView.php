<?php

class CountView
{
    public function renderList(array $counts): string
    {
        ob_start();
        include 'components/header.php';
        include 'components/nav.php';
        ?>
        <div class="container">
            <h2>Counts</h2>
            <ul>
                <?php foreach ($counts as $count): ?>
                    <li>ID: <?= $count['id'] ?>, Impact: <?= $count['count_impact'] ?>, Project: <?= $count['count_project'] ?>, Member: <?= $count['count_member'] ?>, Trainees: <?= $count['count_trainees'] ?></li>
                <?php endforeach; ?>
            </ul>
            <a href="?action=create">Add New Count</a>
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
            <h2>Add Count</h2>
            <form method="post" action="">
                <label>Impact: <input type="number" name="impact"></label><br>
                <label>Project: <input type="number" name="project"></label><br>
                <label>Member: <input type="number" name="member"></label><br>
                <label>Trainees: <input type="number" name="trainees"></label><br>
                <button type="submit">Add Count</button>
            </form>
            <a href="?action=index">Back to List</a>
        </div>
        <?php
        include 'components/footer.php';
        return ob_get_clean();
    }
}