<?php

require_once 'classes/models/team.mod.php';

class TeamController
{
    private $model;
    private $view;

    public function __construct(\PDO $pdo)
    {
        $this->model = new Team($pdo);
        $this->view = new TeamView();
    }

    public function index()
    {
        $teams = $this->model->getAll();
        echo $this->view->renderList($teams);
    }

    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'];
            $position = $_POST['position'];
            $photo = null;
            // save uploaded image to disk and store its path
            if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
                $ext = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
                $allowed = ['jpg','jpeg','png','gif','webp'];
                if (in_array($ext, $allowed)) {
                    $uploadDir = __DIR__ . '/../../uploads/team';
                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0755, true);
                    }
                    $filename = uniqid() . '.' . $ext;
                    $dest = $uploadDir . '/' . $filename;
                    if (move_uploaded_file($_FILES['photo']['tmp_name'], $dest)) {
                        $photo = '/uploads/team/' . $filename;
                    }
                }
            }
            $facebook = $_POST['facebook'] ?? null;
            $instagram = $_POST['instagram'] ?? null;
            $twitter = $_POST['twitter'] ?? null;
            $linkedin = $_POST['linkedin'] ?? null;
            if ($this->model->create($name, $position, $photo, $facebook, $instagram, $twitter, $linkedin)) {
                header('Location: /team');
            } else {
                echo 'Error adding team member';
            }
        } else {
            echo $this->view->renderForm();
        }
    }

    public function show($id)
    {
        $team = $this->model->find($id);
        if ($team) {
            echo '<h2>Team Member Details</h2><p>ID: ' . $team['id'] . ', Name: ' . $team['name'] . '</p>';
        } else {
            echo 'Team member not found';
        }
    }

    public function update($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'];
            $position = $_POST['position'];
            $photo = $_POST['existing_photo'] ?? null;
            if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
                $ext = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
                $allowed = ['jpg','jpeg','png','gif','webp'];
                if (in_array($ext, $allowed)) {
                    $uploadDir = __DIR__ . '/../../uploads/team';
                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0755, true);
                    }
                    $filename = uniqid() . '.' . $ext;
                    $dest = $uploadDir . '/' . $filename;
                    if (move_uploaded_file($_FILES['photo']['tmp_name'], $dest)) {
                        $photo = '/uploads/team/' . $filename;
                    }
                }
            }
            $facebook = $_POST['facebook'] ?? null;
            $instagram = $_POST['instagram'] ?? null;
            $twitter = $_POST['twitter'] ?? null;
            $linkedin = $_POST['linkedin'] ?? null;
            if ($this->model->update($id, $name, $position, $photo, $facebook, $instagram, $twitter, $linkedin)) {
                header('Location: /team');
            } else {
                echo 'Error updating team member';
            }
        } else {
            $team = $this->model->find($id);
            if ($team) {
                echo '<form method="post" action="" enctype="multipart/form-data">
                    <input type="hidden" name="existing_photo" value="' . ($team['photo'] ?? '') . '">
                    <label>Name: <input type="text" name="name" value="' . $team['name'] . '" required></label><br>
                    <label>Position: <input type="text" name="position" value="' . $team['position'] . '" required></label><br>
                    <label>Photo: <input type="file" name="photo"></label><br>
                    <label>Facebook: <input type="url" name="facebook" value="' . ($team['facebook'] ?? '') . '"></label><br>
                    <label>Instagram: <input type="url" name="instagram" value="' . ($team['instagram'] ?? '') . '"></label><br>
                    <label>Twitter: <input type="url" name="twitter" value="' . ($team['twitter'] ?? '') . '"></label><br>
                    <label>LinkedIn: <input type="url" name="linkedin" value="' . ($team['linkedin'] ?? '') . '"></label><br>
                    <button type="submit">Update Team Member</button>
                </form>';
            } else {
                echo 'Team member not found';
            }
        }
    }

    public function delete($id)
    {
        if ($this->model->delete($id)) {
            header('Location: /team');
        } else {
            echo 'Error deleting team member';
        }
    }
}