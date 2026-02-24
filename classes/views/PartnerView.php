<?php

class PartnerView
{
    public function renderList(array $partners): string
    {
        ob_start();
        include 'components/header.php';
        include 'components/nav.php';
        ?>
        <div class="container">
            <h2>Partners</h2>
            <ul>
                <?php foreach ($partners as $partner): ?>
                    <li>ID: <?= $partner['id'] ?>, Name: <?= $partner['partner_name'] ?>, Website: <?= $partner['partner_website'] ?? '' ?></li>
                <?php endforeach; ?>
            </ul>
            <a href="?action=create">Add New Partner</a>
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
            <h2>Add Partner</h2>
            <form method="post" action="">
                <label>Name: <input type="text" name="name" required></label><br>
                <label>Logo URL: <input type="text" name="logo"></label><br>
                <label>Website: <input type="url" name="website"></label><br>
                <button type="submit">Add Partner</button>
            </form>
            <a href="?action=index">Back to List</a>
        </div>
        <?php
        include 'components/footer.php';
        return ob_get_clean();
    }
}