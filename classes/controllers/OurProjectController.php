<?php

require_once 'classes/models/our_project.mod.php';

class OurProjectController
{
    private $model;
    private $view;

    public function __construct(\PDO $pdo)
    {
        $this->model = new OurProject($pdo);
        $this->view = new OurProjectView();
    }

    public function index()
    {
        $projects = $this->model->getAll();
        echo $this->view->renderList($projects);
    }

    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'];
            $description = $_POST['description'] ?? null;
            $photo = null;
            if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
                $ext = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
                $allowed = ['jpg','jpeg','png','gif','webp'];
                if (in_array($ext, $allowed)) {
                    $uploadDir = __DIR__ . '/../../uploads/projects';
                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0755, true);
                    }
                    $filename = uniqid() . '.' . $ext;
                    $dest = $uploadDir . '/' . $filename;
                    if (move_uploaded_file($_FILES['photo']['tmp_name'], $dest)) {
                        $photo = '/uploads/projects/' . $filename;
                    }
                }
            }
            if ($this->model->create($name, $description, $photo)) {
                header('Location: /ourproject');
            } else {
                echo 'Error adding project';
            }
        } else {
            echo $this->view->renderForm();
        }
    }

    public function show($id)
    {
        $project = $this->model->find($id);
        if ($project) {
            echo '<h2>Project Details</h2><p>ID: ' . $project['id'] . ', Name: ' . $project['project_name'] . '</p>';
        } else {
            echo 'Project not found';
        }
    }

    public function update($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'];
            $description = $_POST['description'] ?? null;
            $photo = $_POST['existing_photo'] ?? null;
            if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
                $ext = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
                $allowed = ['jpg','jpeg','png','gif','webp'];
                if (in_array($ext, $allowed)) {
                    $uploadDir = __DIR__ . '/../../uploads/projects';
                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0755, true);
                    }
                    $filename = uniqid() . '.' . $ext;
                    $dest = $uploadDir . '/' . $filename;
                    if (move_uploaded_file($_FILES['photo']['tmp_name'], $dest)) {
                        $photo = '/uploads/projects/' . $filename;
                    }
                }
            }
            if ($this->model->update($id, $name, $description, $photo)) {
                header('Location: /ourproject');
            } else {
                echo 'Error updating project';
            }
        } else {
            $project = $this->model->find($id);
            if ($project) {
                echo '<form method="post" action="" enctype="multipart/form-data">
                    <input type="hidden" name="existing_photo" value="' . ($project['photo'] ?? '') . '">
                    <label>Name: <input type="text" name="name" value="' . $project['project_name'] . '" required></label><br>
                    <label>Description: <textarea name="description">' . ($project['project_description'] ?? '') . '</textarea></label><br>
                    <label>Photo: <input type="file" name="photo"></label><br>
                    <button type="submit">Update Project</button>
                </form>';
            } else {
                echo 'Project not found';
            }
        }
    }

    public function delete($id)
    {
        if ($this->model->delete($id)) {
            header('Location: /ourproject');
        } else {
            echo 'Error deleting project';
        }
    }
}