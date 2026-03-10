<?php
session_start();

if (!isset($_SESSION['admin_user_id'])) {
    header('Location: ../admin-login.php');
    exit();
}

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../classes/db.php';
require_once __DIR__ . '/../classes/models/our_project.mod.php';

$pdo = DB::getConnection();
$project = new OurProject($pdo);

$message = '';
$error = '';
$action = $_GET['action'] ?? 'list';
$projectId = $_GET['id'] ?? null;

// Helper function to handle file uploads: save to disk and return path
function handleFileUpload($fieldName) {
    if (!isset($_FILES[$fieldName]) || $_FILES[$fieldName]['error'] === UPLOAD_ERR_NO_FILE) {
        return null;
    }
    $file = $_FILES[$fieldName];
    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowed)) {
        throw new Exception('Invalid file type. Only images allowed.');
    }
    if ($file['size'] > 5242880) { // 5MB limit
        throw new Exception('File size exceeds 5MB limit.');
    }
    $uploadDir = __DIR__ . '/../uploads/projects';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    $filename = uniqid() . '.' . $ext;
    $destination = $uploadDir . '/' . $filename;
    if (!move_uploaded_file($file['tmp_name'], $destination)) {
        throw new Exception('Failed to move uploaded file.');
    }
    return '/uploads/projects/' . $filename;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'create') {
            $title = $_POST['title'] ?? '';
            $description = $_POST['description'] ?? '';
            
            try {
                $image = handleFileUpload('image');
            } catch (Exception $e) {
                $error = $e->getMessage();
                $image = null;
            }

            if (!empty($title) && empty($error)) {
                if ($project->create($title, $description, $image)) {
                    $message = 'Project created successfully!';
                    $action = 'list';
                } else {
                    $error = 'Failed to create project';
                }
            } else {
                $error = 'Title is required';
            }
        } elseif ($_POST['action'] === 'update') {
            $id = $_POST['id'] ?? null;
            $title = $_POST['title'] ?? '';
            $description = $_POST['description'] ?? '';
            
            try {
                $image = handleFileUpload('image');
            } catch (Exception $e) {
                $error = $e->getMessage();
                $image = null;
            }
            
            // If no new file uploaded, keep the existing image
            if ($image === null && $id) {
                $currentProject = $project->find($id);
                $image = $currentProject['photo'] ?? $currentProject['image'] ?? null;
            }

            if ($id && empty($error)) {
                if ($project->update($id, $title, $description, $image)) {
                    $message = 'Project updated successfully!';
                } else {
                    $error = 'Failed to update project';
                }
            }
            $action = 'list';
        } elseif ($_POST['action'] === 'delete') {
            $id = $_POST['id'] ?? null;
            if ($id) {
                if ($project->delete($id)) {
                    $message = 'Project deleted successfully!';
                } else {
                    $error = 'Failed to delete project';
                }
            }
            $action = 'list';
        }
    }
}

$allProjects = $project->getAll();
$currentProject = null;
if ($action === 'edit' && $projectId) {
    $currentProject = $project->find($projectId);
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Projects - Admin Portal</title>
    <link rel="stylesheet" href="../assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary: #667eea;
            --secondary: #764ba2;
            --success: #48bb78;
            --danger: #f56565;
        }
        
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .admin-container {
            display: flex;
            min-height: 100vh;
        }
        
        .sidebar {
            width: 250px;
            background: linear-gradient(180deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
        }
        
        .sidebar-header {
            display: flex;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .sidebar-header i, .sidebar-header h3 {
            margin: 0;
        }
        
        .sidebar-header i {
            font-size: 24px;
            margin-right: 10px;
        }
        
        .sidebar-menu {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .sidebar-menu li {
            margin-bottom: 10px;
        }
        
        .sidebar-menu a {
            display: flex;
            align-items: center;
            padding: 15px;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: all 0.3s;
        }
        
        .sidebar-menu a:hover,
        .sidebar-menu a.active {
            background-color: rgba(255, 255, 255, 0.2);
        }
        
        .sidebar-menu i {
            margin-right: 12px;
            width: 20px;
        }
        
        .main-content {
            margin-left: 250px;
            flex: 1;
            padding: 30px;
        }
        
        .top-navbar {
            background: white;
            padding: 15px 30px;
            border-radius: 10px;
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }
        
        .logout-btn {
            padding: 8px 15px;
            background-color: #f56565;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            font-size: 14px;
        }
        
        .content-card {
            background: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }
        
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
        
        .btn {
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s;
            border: none;
            cursor: pointer;
            font-size: 14px;
        }
        
        .btn-primary {
            background-color: var(--primary);
            color: white;
        }
        
        .btn-danger {
            background-color: var(--danger);
            color: white;
        }
        
        .btn-secondary {
            background-color: #e2e8f0;
            color: #4a5568;
        }
        
        .btn-sm {
            padding: 6px 12px;
            font-size: 12px;
        }
        
        .alert {
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        
        .alert-success {
            background-color: #c6f6d5;
            color: #22543d;
        }
        
        .alert-danger {
            background-color: #fed7d7;
            color: #742a2a;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        thead {
            background-color: #f7fafc;
            border-bottom: 2px solid #e2e8f0;
        }
        
        th {
            padding: 15px;
            text-align: left;
            font-weight: 600;
            color: #4a5568;
        }
        
        td {
            padding: 15px;
            border-bottom: 1px solid #e2e8f0;
        }
        
        tr:hover {
            background-color: #f7fafc;
        }
        
        .action-buttons {
            display: flex;
            gap: 8px;
        }
        
        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
            }
            
            .main-content {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <aside class="sidebar">
            <div class="sidebar-header">
                <i class="fas fa-sliders-h"></i>
                <h3>IoT Admin</h3>
            </div>
            
            <ul class="sidebar-menu">
                <li><a href="index.php"><i class="fas fa-home"></i> Dashboard</a></li>
                <li><a href="users.php"><i class="fas fa-users"></i> Users</a></li>
                <li><a href="team.php"><i class="fas fa-users-circle"></i> Team Members</a></li>
                <li><a href="works.php"><i class="fas fa-briefcase"></i> Our Works</a></li>
                <li><a href="projects.php" class="active"><i class="fas fa-project-diagram"></i> Projects</a></li>
                <li><a href="partners.php"><i class="fas fa-handshake"></i> Partners</a></li>
                <li><a href="roles.php"><i class="fas fa-user-tag"></i> Roles</a></li>
                <li><a href="privileges.php"><i class="fas fa-shield-alt"></i> Privileges</a></li>
                <li><a href="statistics.php"><i class="fas fa-chart-bar"></i> Statistics</a></li>
                <li style="margin-top: 30px; border-top: 1px solid rgba(255,255,255,0.2); padding-top: 20px;">
                    <a href="../admin-logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                </li>
            </ul>
        </aside>
        
        <div class="main-content">
            <div class="top-navbar">
                <h2 style="margin: 0; color: #333;">Projects</h2>
                <a href="../admin-logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
            
            <?php if ($message): ?>
                <div class="alert alert-success"><i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <div class="content-card">
                <?php if ($action === 'list'): ?>
                    <div class="page-header">
                        <h2><i class="fas fa-project-diagram"></i> Manage Projects</h2>
                        <a href="projects.php?action=create" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Add Project
                        </a>
                    </div>
                    
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Title</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($allProjects as $p): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($p['id'] ?? ''); ?></td>
                                    <td><?php echo htmlspecialchars($p['project_name'] ?? $p['title'] ?? ''); ?></td>
                                    <td>
                                        <div class="action-buttons">
                                            <a href="projects.php?action=edit&id=<?php echo $p['id']; ?>" class="btn btn-secondary btn-sm">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                            <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure?');">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="id" value="<?php echo $p['id']; ?>">
                                                <button type="submit" class="btn btn-danger btn-sm">
                                                    <i class="fas fa-trash"></i> Delete
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php elseif ($action === 'create' || $action === 'edit'): ?>
                    <div class="page-header">
                        <h2><i class="fas fa-project-diagram"></i> <?php echo $action === 'create' ? 'Add Project' : 'Edit Project'; ?></h2>
                    </div>
                    <form method="POST" enctype="multipart/form-data" class="mb-4">
                        <?php if ($action === 'edit'): ?>
                            <input type="hidden" name="action" value="update">
                            <input type="hidden" name="id" value="<?php echo htmlspecialchars($currentProject['id'] ?? ''); ?>">
                        <?php else: ?>
                            <input type="hidden" name="action" value="create">
                        <?php endif; ?>

                        <div class="form-group">
                            <label for="title">Title</label>
                            <input type="text" id="title" name="title" class="form-control" value="<?php echo htmlspecialchars($currentProject['project_name'] ?? ''); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea id="description" name="description" class="form-control"><?php echo htmlspecialchars($currentProject['project_description'] ?? ''); ?></textarea>
                        </div>
                        <div class="form-group">
                            <label for="image">Image</label>
                            <?php if (!empty($currentProject['photo'] ?? '')): ?>
                                <div style="margin-bottom: 10px;">
                                    <img src="<?php echo htmlspecialchars($currentProject['photo']); ?>" alt="Current image" style="max-width: 100px; height: auto; border-radius: 5px;">
                                    <p style="font-size: 12px; color: #666;">Current image</p>
                                </div>
                            <?php endif; ?>
                            <input type="file" id="image" name="image" class="form-control" accept="image/*">
                            <?php if ($action === 'edit'): ?>
                                <p style="font-size: 12px; color: #666;">Leave empty to keep current image</p>
                            <?php endif; ?>
                        </div>
                        <button type="submit" class="btn btn-primary"><?php echo $action === 'create' ? 'Create Project' : 'Update Project'; ?></button>
                        <a href="projects.php" class="btn btn-secondary">Cancel</a>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
