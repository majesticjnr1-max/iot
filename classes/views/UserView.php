<?php

class UserView
{
    public function renderList(array $users): string
    {
        ob_start();
        include 'components/header.php';
        include 'components/nav.php';
        ?>
        <div class="container">
            <h2>Users</h2>
            <ul>
                <?php foreach ($users as $user): ?>
                    <li>ID: <?= $user['user_id'] ?>, Username: <?= $user['user_name'] ?></li>
                <?php endforeach; ?>
            </ul>
            <a href="?action=create">Add New User</a>
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
            <h2>Add User</h2>
            <form method="post" action="">
                <label>Username: <input type="text" name="username" required></label><br>
                <label>Password: <input type="password" name="password" required></label><br>
                <label>Role ID: <input type="number" name="roleId"></label><br>
                <button type="submit">Add User</button>
            </form>
            <a href="?action=index">Back to List</a>
        </div>
        <?php
        include 'components/footer.php';
        return ob_get_clean();
    }
}