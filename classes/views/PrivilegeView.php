<?php

class PrivilegeView
{
    public function renderList(array $privileges): string
    {
        ob_start();
        include 'components/header.php';
        include 'components/nav.php';
        ?>
        <div class="container">
            <h2>Privileges</h2>
            <ul>
                <?php foreach ($privileges as $privilege): ?>
                    <li>ID: <?= $privilege['privilege_id'] ?>, Name: <?= $privilege['privilege_name'] ?></li>
                <?php endforeach; ?>
            </ul>
            <a href="?action=create">Add New Privilege</a>
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
            <h2>Add Privilege</h2>
            <form method="post" action="">
                <label>Name: <input type="text" name="name" required></label><br>
                <label>Description: <textarea name="description"></textarea></label><br>
                <label>Module: <input type="text" name="module"></label><br>
                <button type="submit">Add Privilege</button>
            </form>
            <a href="?action=index">Back to List</a>
        </div>
        <?php
        include 'components/footer.php';
        return ob_get_clean();
    }
}