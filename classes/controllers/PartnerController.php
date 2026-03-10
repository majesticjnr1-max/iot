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
                $tmp = $_FILES['logo']['tmp_name'];
                $data = file_get_contents($tmp);
                $type = mime_content_type($tmp) ?: 'application/octet-stream';
                $logo = 'data:' . $type . ';base64,' . base64_encode($data);
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
                $tmp = $_FILES['logo']['tmp_name'];
                $data = file_get_contents($tmp);
                $type = mime_content_type($tmp) ?: 'application/octet-stream';
                $logo = 'data:' . $type . ';base64,' . base64_encode($data);
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