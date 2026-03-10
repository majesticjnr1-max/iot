<?php
session_start();

if (!isset($_SESSION['admin_user_id'])) {
    header('Location: ../admin-login.php');
    exit();
}

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../classes/db.php';
require_once __DIR__ . '/../classes/models/partners.mod.php';

$pdo = DB::getConnection();
$partner = new Partner($pdo);

$message = '';
$error = '';
$action = $_GET['action'] ?? 'list';
$partnerId = $_GET['id'] ?? null;

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
    $uploadDir = __DIR__ . '/../uploads/partners';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    $filename = uniqid() . '.' . $ext;
    $destination = $uploadDir . '/' . $filename;
    if (!move_uploaded_file($file['tmp_name'], $destination)) {
        throw new Exception('Failed to move uploaded file.');
    }
    return '/iot/uploads/partners/' . $filename;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'create') {
            $name = $_POST['name'] ?? '';
            $website = $_POST['website'] ?? null;
            
            try {
                $logo = handleFileUpload('logo');
            } catch (Exception $e) {
                $error = $e->getMessage();
                $logo = null;
            }

            if (!empty($name) && empty($error)) {
                if ($partner->create($name, $logo, $website)) {
                    $message = 'Partner created successfully!';
                    $action = 'list';
                } else {
                    $error = 'Failed to create partner';
                }
            } else {
                $error = 'Partner name is required';
            }
        } elseif ($_POST['action'] === 'update') {
            $id = $_POST['id'] ?? null;
            $name = $_POST['name'] ?? '';
            $website = $_POST['website'] ?? null;
            
            try {
                $logo = handleFileUpload('logo');
            } catch (Exception $e) {
                $error = $e->getMessage();
                $logo = null;
            }
            
            // If no new file uploaded, keep the existing logo
            if ($logo === null && $id) {
                $currentPartner = $partner->find($id);
                $logo = $currentPartner['partner_logo'] ?? $currentPartner['logo'] ?? null;
            }

            if ($id && empty($error)) {
                if ($partner->update($id, $name, $logo, $website)) {
                    $message = 'Partner updated successfully!';
                } else {
                    $error = 'Failed to update partner';
                }
            }
            $action = 'list';
        } elseif ($_POST['action'] === 'delete') {
            $id = $_POST['id'] ?? null;
            if ($id) {
                if ($partner->delete($id)) {
                    $message = 'Partner deleted successfully!';
                } else {
                    $error = 'Failed to delete partner';
                }
            }
            $action = 'list';
        }
    }
}

$allPartners = $partner->getAll();
$currentPartner = null;
if ($action === 'edit' && $partnerId) {
    $currentPartner = $partner->find($partnerId);
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Partners - Admin Portal</title>
    <link rel="stylesheet" href="../assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="shortcut icon" href="../assets/img/icons/IoT.png" type="image/x-icon">
    <style>
        :root {
            --primary: #667eea;
            --secondary: #764ba2;
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
                <li><a href="projects.php"><i class="fas fa-project-diagram"></i> Projects</a></li>
                <li><a href="partners.php" class="active"><i class="fas fa-handshake"></i> Partners</a></li>
                <li><a href="roles.php"><i class="fas fa-user-tag"></i> Roles</a></li>
                <li><a href="privileges.php"><i class="fas fa-shield-alt"></i> Privileges</a></li>
                <li><a href="statistics.php"><i class="fas fa-chart-bar"></i> Statistics</a></li>
                <li><a href="testimonies.php"><i class="fas fa-comment-dots"></i> Testimonies</a></li>
                <li style="margin-top: 30px; border-top: 1px solid rgba(255,255,255,0.2); padding-top: 20px;">
                    <a href="../admin-logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                </li>
            </ul>
        </aside>
        
        <div class="main-content">
            <div class="top-navbar">
                <h2 style="margin: 0; color: #333;">Partners</h2>
                <a href="../admin-logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
            
            <?php if ($message): ?>
                <div class="alert alert-success"><i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>
            
            <div class="content-card">
                <?php if ($action === 'list'): ?>
                    <div class="page-header">
                        <h2><i class="fas fa-handshake"></i> Manage Partners</h2>
                        <a href="partners.php?action=create" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Add Partner
                        </a>
                    </div>
                    
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($allPartners as $p): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($p['id'] ?? $p['partner_id'] ?? ''); ?></td>
                                    <td><?php echo htmlspecialchars($p['partner_name'] ?? $p['name'] ?? ''); ?></td>
                                    <td>
                                        <div class="action-buttons">
                                            <a href="partners.php?action=edit&id=<?php echo $p['id'] ?? $p['partner_id']; ?>" class="btn btn-secondary btn-sm">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                            <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure?');">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="id" value="<?php echo $p['id'] ?? $p['partner_id']; ?>">
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
                        <h2><i class="fas fa-handshake"></i> <?php echo $action === 'create' ? 'Add Partner' : 'Edit Partner'; ?></h2>
                    </div>
                    <form method="POST" enctype="multipart/form-data" class="mb-4">
                        <?php if ($action === 'edit'): ?>
                            <input type="hidden" name="action" value="update">
                            <input type="hidden" name="id" value="<?php echo htmlspecialchars($currentPartner['id'] ?? $currentPartner['partner_id'] ?? ''); ?>">
                        <?php else: ?>
                            <input type="hidden" name="action" value="create">
                        <?php endif; ?>

                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" id="name" name="name" class="form-control" value="<?php echo htmlspecialchars($currentPartner['partner_name'] ?? ''); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="logo">Logo</label>
                            <?php if (!empty($currentPartner['partner_logo'] ?? '')): ?>
                                <div style="margin-bottom: 10px;">
                                    <img src="<?php echo htmlspecialchars($currentPartner['partner_logo']); ?>" alt="Current logo" style="max-width: 100px; height: auto; border-radius: 5px;">
                                    <p style="font-size: 12px; color: #666;">Current logo</p>
                                </div>
                            <?php endif; ?>
                            <input type="file" id="logo" name="logo" class="form-control" accept="image/*">
                            <?php if ($action === 'edit'): ?>
                                <p style="font-size: 12px; color: #666;">Leave empty to keep current logo</p>
                            <?php endif; ?>
                        </div>
                        <div class="form-group">
                            <label for="website">Website</label>
                            <input type="text" id="website" name="website" class="form-control" value="<?php echo htmlspecialchars($currentPartner['partner_website'] ?? ''); ?>">
                        </div>
                        <button type="submit" class="btn btn-primary"><?php echo $action === 'create' ? 'Create Partner' : 'Update Partner'; ?></button>
                        <a href="partners.php" class="btn btn-secondary">Cancel</a>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
