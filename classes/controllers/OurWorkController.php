<?php

require_once 'classes/models/our_work.mod.php';

class OurWorkController
{
    private $model;
    private $view;

    public function __construct(\PDO $pdo)
    {
        $this->model = new OurWork($pdo);
        $this->view = new OurWorkView();
    }

    public function index()
    {
        $works = $this->model->getAll();
        echo $this->view->renderList($works);
    }

    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = $_POST['title'];
            $photo = null;
            if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = 'uploads/works/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                $photo = $uploadDir . basename($_FILES['photo']['name']);
                move_uploaded_file($_FILES['photo']['tmp_name'], $photo);
            }
            if ($this->model->create($title, $photo)) {
                header('Location: /ourwork');
            } else {
                echo 'Error adding work';
            }
        } else {
            echo $this->view->renderForm();
        }
    }

    public function show($id)
    {
        $work = $this->model->find($id);
        if ($work) {
            echo '<h2>Work Details</h2><p>ID: ' . $work['id'] . ', Title: ' . $work['work_title'] . '</p>';
        } else {
            echo 'Work not found';
        }
    }

    public function update($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = $_POST['title'];
            $photo = $_POST['existing_photo'] ?? null;
            if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = 'uploads/works/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                $photo = $uploadDir . basename($_FILES['photo']['name']);
                move_uploaded_file($_FILES['photo']['tmp_name'], $photo);
            }
            if ($this->model->update($id, $title, $photo)) {
                header('Location: /ourwork');
            } else {
                echo 'Error updating work';
            }
        } else {
            $work = $this->model->find($id);
            if ($work) {
                echo '<form method="post" action="" enctype="multipart/form-data">
                    <input type="hidden" name="existing_photo" value="' . ($work['photo'] ?? '') . '">
                    <label>Title: <input type="text" name="title" value="' . $work['work_title'] . '" required></label><br>
                    <label>Photo: <input type="file" name="photo"></label><br>
                    <button type="submit">Update Work</button>
                </form>';
            } else {
                echo 'Work not found';
            }
        }
    }

    public function delete($id)
    {
        if ($this->model->delete($id)) {
            header('Location: /ourwork');
        } else {
            echo 'Error deleting work';
        }
    }
}