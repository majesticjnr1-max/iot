<?php

class RoleView
{
    public function renderList(array $roles): string
    {
        ob_start();
        include 'components/header.php';
        include 'components/nav.php';
        ?>
        <div class="container">
            <h2>Roles</h2>
            <ul>
                <?php foreach ($roles as $role): ?>
                    <li>ID: <?= $role['role_id'] ?>, Name: <?= $role['role_name'] ?></li>
                <?php endforeach; ?>
            </ul>
            <a href="?action=create">Add New Role</a>
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
            <h2>Add Role</h2>
            <form method="post" action="">
                <label>Name: <input type="text" name="name" required></label><br>
                <label>Description: <textarea name="description"></textarea></label><br>
                <button type="submit">Add Role</button>
            </form>
            <a href="?action=index">Back to List</a>
        </div>
        <?php
        include 'components/footer.php';
        return ob_get_clean();
    }
}