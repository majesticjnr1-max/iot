<?php

require_once 'classes/models/partners.mod.php';

class PartnerController
{
    private $model;
    private $view;

    public function __construct(\PDO $pdo)
    {
        $this->model = new Partner($pdo);
        $this->view = new PartnerView();
    }

    public function index()
    {
        $partners = $this->model->getAll();
        echo $this->view->renderList($partners);
    }

    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'];
            $website = $_POST['website'] ?? null;
            $logo = null;
            if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
                $ext = strtolower(pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION));
                $allowed = ['jpg','jpeg','png','gif','webp'];
                if (in_array($ext, $allowed)) {
                    $uploadDir = __DIR__ . '/../../uploads/partners';
                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0755, true);
                    }
                    $filename = uniqid() . '.' . $ext;
                    $dest = $uploadDir . '/' . $filename;
                    if (move_uploaded_file($_FILES['logo']['tmp_name'], $dest)) {
                        $logo = '/uploads/partners/' . $filename;
                    }
                }
            }
            if ($this->model->create($name, $logo, $website)) {
                header('Location: /partner');
            } else {
                echo 'Error adding partner';
            }
        } else {
            echo $this->view->renderForm();
        }
    }

    public function show($id)
    {
        $partner = $this->model->find($id);
        if ($partner) {
            echo '<h2>Partner Details</h2><p>ID: ' . $partner['id'] . ', Name: ' . $partner['partner_name'] . '</p>';
        } else {
            echo 'Partner not found';
        }
    }

    public function update($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'];
            $website = $_POST['website'] ?? null;
            $logo = $_POST['existing_logo'] ?? null;
            if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
                $ext = strtolower(pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION));
                $allowed = ['jpg','jpeg','png','gif','webp'];
                if (in_array($ext, $allowed)) {
                    $uploadDir = __DIR__ . '/../../uploads/partners';
                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0755, true);
                    }
                    $filename = uniqid() . '.' . $ext;
                    $dest = $uploadDir . '/' . $filename;
                    if (move_uploaded_file($_FILES['logo']['tmp_name'], $dest)) {
                        $logo = '/uploads/partners/' . $filename;
                    }
                }
            }
            if ($this->model->update($id, $name, $logo, $website)) {
                header('Location: /partner');
            } else {
                echo 'Error updating partner';
            }
        } else {
            $partner = $this->model->find($id);
            if ($partner) {
                echo '<form method="post" action="" enctype="multipart/form-data">
                    <input type="hidden" name="existing_logo" value="' . ($partner['partner_logo'] ?? '') . '">
                    <label>Name: <input type="text" name="name" value="' . $partner['partner_name'] . '" required></label><br>
                    <label>Logo: <input type="file" name="logo"></label><br>
                    <label>Website: <input type="url" name="website" value="' . ($partner['partner_website'] ?? '') . '"></label><br>
                    <button type="submit">Update Partner</button>
                </form>';
            } else {
                echo 'Partner not found';
            }
        }
    }

    public function delete($id)
    {
        if ($this->model->delete($id)) {
            header('Location: /partner');
        } else {
            echo 'Error deleting partner';
        }
    }
}