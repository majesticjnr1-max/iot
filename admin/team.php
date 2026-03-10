<?php
session_start();

if (!isset($_SESSION['admin_user_id'])) {
    header('Location: ../admin-login.php');
    exit();
}

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../classes/db.php';
require_once __DIR__ . '/../classes/models/team.mod.php';

$pdo = DB::getConnection();
$team = new Team($pdo);

$message = '';
$error = '';
$action = $_GET['action'] ?? 'list';
$teamId = $_GET['id'] ?? null;

// Helper function to handle file uploads: convert to base64 and return data URI
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
    $tmp = $file['tmp_name'];
    $data = file_get_contents($tmp);
    $type = mime_content_type($tmp) ?: 'application/octet-stream';
    return 'data:' . $type . ';base64,' . base64_encode($data);
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'create') {
            $name = $_POST['name'] ?? '';
            $position = $_POST['position'] ?? '';
            $facebook = $_POST['facebook'] ?? null;
            $instagram = $_POST['instagram'] ?? null;
            $twitter = $_POST['twitter'] ?? null;
            $linkedin = $_POST['linkedin'] ?? null;
            
            try {
                $photo = handleFileUpload('photo');
            } catch (Exception $e) {
                $error = $e->getMessage();
                $photo = null;
            }

            if (!empty($name) && !empty($position) && empty($error)) {
                $result = $team->create($name, $position, $photo, $facebook, $instagram, $twitter, $linkedin);
                if ($result) {
                    $message = 'Team member created successfully!';
                    $action = 'list';
                } else {
                    $error = 'Failed to create team member';
                }
            } else {
                $error = 'Name and position are required';
            }
        } elseif ($_POST['action'] === 'update') {
            $memberId = $_POST['id'] ?? null;
            $name = $_POST['name'] ?? '';
            $position = $_POST['position'] ?? '';
            $facebook = $_POST['facebook'] ?? null;
            $instagram = $_POST['instagram'] ?? null;
            $twitter = $_POST['twitter'] ?? null;
            $linkedin = $_POST['linkedin'] ?? null;
            
            try {
                $photo = handleFileUpload('photo');
            } catch (Exception $e) {
                $error = $e->getMessage();
                $photo = null;
            }
            
            // If no new file uploaded, keep the existing photo
            if ($photo === null && $memberId) {
                $currentMember = $team->find($memberId);
                $photo = $currentMember['photo'] ?? null;
            }

            if ($memberId && !empty($name) && !empty($position) && empty($error)) {
                $result = $team->update($memberId, $name, $position, $photo, $facebook, $instagram, $twitter, $linkedin);
                if ($result) {
                    $message = 'Team member updated successfully!';
                } else {
                    $error = 'Failed to update team member';
                }
                $action = 'list';
            } else {
                $error = 'Name and position are required';
            }
        } elseif ($_POST['action'] === 'delete') {
            $memberId = $_POST['id'] ?? null;
            if ($memberId) {
                if ($team->delete($memberId)) {
                    $message = 'Team member deleted successfully!';
                } else {
                    $error = 'Failed to delete team member';
                }
            }
            $action = 'list';
        }
    }
}

$allTeam = $team->getAll();
$currentMember = null;

if ($action === 'edit' && $teamId) {
    $currentMember = $team->find($teamId);
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Team - Admin Portal</title>
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
        
        .logout-btn:hover {
            background-color: #e53e3e;
            text-decoration: none;
            color: white;
        }
        
        .content-card {
            background: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            margin-bottom: 20px;
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
        
        .btn-success {
            background-color: var(--success);
            color: white;
        }
        
        .btn-secondary {
            background-color: #edf2f7;
            color: #333;
        }
        
        .btn-sm {
            padding: 6px 12px;
            font-size: 12px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 500;
        }
        
        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }
        
        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
        .alert {
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        
        .alert-success {
            background-color: #c6f6d5;
            color: #22543d;
            border: 1px solid #9ae6b4;
        }
        
        .alert-danger {
            background-color: #fed7d7;
            color: #742a2a;
            border: 1px solid #fc8181;
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
        
        .back-btn {
            margin-bottom: 20px;
            display: inline-block;
            color: var(--primary);
            text-decoration: none;
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
            table {display:block;overflow-x:auto;}
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <i class="fas fa-sliders-h"></i>
                <h3>IoT Admin</h3>
            </div>
            
            <ul class="sidebar-menu">
                <li><a href="index.php"><i class="fas fa-home"></i> Dashboard</a></li>
                <li><a href="users.php"><i class="fas fa-users"></i> Users</a></li>
                <li><a href="team.php" class="active"><i class="fas fa-users-circle"></i> Team Members</a></li>
                <li><a href="works.php"><i class="fas fa-briefcase"></i> Our Works</a></li>
                <li><a href="projects.php"><i class="fas fa-project-diagram"></i> Projects</a></li>
                <li><a href="partners.php"><i class="fas fa-handshake"></i> Partners</a></li>
                <li><a href="roles.php"><i class="fas fa-user-tag"></i> Roles</a></li>
                <li><a href="privileges.php"><i class="fas fa-shield-alt"></i> Privileges</a></li>
                <li><a href="statistics.php"><i class="fas fa-chart-bar"></i> Statistics</a></li>
                <li style="margin-top: 30px; border-top: 1px solid rgba(255,255,255,0.2); padding-top: 20px;">
                    <a href="../admin-logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                </li>
            </ul>
        </aside>
        
        <!-- Main Content -->
        <div class="main-content">
            <!-- Top Navbar -->
            <div class="top-navbar">
                <h2 style="margin: 0; color: #333;">Team Members</h2>
                <a href="../admin-logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
            
            <!-- Messages -->
            <!-- Messages -->
            <?php if ($message): ?>
                <div class="alert alert-success"><i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <div class="content-card">
                <?php if ($action === 'list'): ?>
                    <div class="page-header">
                        <h2><i class="fas fa-users-circle"></i> Manage Team Members</h2>
                        <a href="team.php?action=create" class="btn btn-primary">
                            <i class="fas fa-user-plus"></i> Add Team Member
                        </a>
                    </div>
                    
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Position</th>
                                <th>Social Links</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($allTeam as $member): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($member['id']); ?></td>
                                    <td><?php echo htmlspecialchars($member['name']); ?></td>
                                    <td><?php echo htmlspecialchars($member['position']); ?></td>
                                    <td>
                                        <?php if (!empty($member['facebook'])): ?>
                                            <a href="<?php echo htmlspecialchars($member['facebook']); ?>" target="_blank" title="Facebook">
                                                <i class="fab fa-facebook"></i>
                                            </a>
                                        <?php endif; ?>
                                        <?php if (!empty($member['instagram'])): ?>
                                            <a href="<?php echo htmlspecialchars($member['instagram']); ?>" target="_blank" title="Instagram">
                                                <i class="fab fa-instagram"></i>
                                            </a>
                                        <?php endif; ?>
                                        <?php if (!empty($member['twitter'])): ?>
                                            <a href="<?php echo htmlspecialchars($member['twitter']); ?>" target="_blank" title="Twitter">
                                                <i class="fab fa-twitter"></i>
                                            </a>
                                        <?php endif; ?>
                                        <?php if (!empty($member['linkedin'])): ?>
                                            <a href="<?php echo htmlspecialchars($member['linkedin']); ?>" target="_blank" title="LinkedIn">
                                                <i class="fab fa-linkedin"></i>
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <a href="team.php?action=edit&id=<?php echo $member['id']; ?>" class="btn btn-secondary btn-sm">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                            <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure?');">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="id" value="<?php echo $member['id']; ?>">
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
                
                <?php elseif ($action === 'create'): ?>
                    <a href="team.php" class="back-btn"><i class="fas fa-arrow-left"></i> Back to Team</a>
                    <h2><i class="fas fa-user-plus"></i> Add Team Member</h2>
                    
                    <form method="POST" enctype="multipart/form-data" style="max-width: 500px; margin-top: 20px;">
                        <input type="hidden" name="action" value="create">
                        
                        <div class="form-group">
                            <label for="name">Name <span style="color: red;">*</span></label>
                            <input type="text" id="name" name="name" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="position">Position <span style="color: red;">*</span></label>
                            <input type="text" id="position" name="position" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="photo">Photo</label>
                            <input type="file" id="photo" name="photo" accept="image/*">
                        </div>
                        
                        <div class="form-group">
                            <label for="facebook">Facebook URL</label>
                            <input type="url" id="facebook" name="facebook">
                        </div>
                        
                        <div class="form-group">
                            <label for="instagram">Instagram URL</label>
                            <input type="url" id="instagram" name="instagram">
                        </div>
                        
                        <div class="form-group">
                            <label for="twitter">Twitter URL</label>
                            <input type="url" id="twitter" name="twitter">
                        </div>
                        
                        <div class="form-group">
                            <label for="linkedin">LinkedIn URL</label>
                            <input type="url" id="linkedin" name="linkedin">
                        </div>
                        
                        <div style="display: flex; gap: 10px;">
                            <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Create</button>
                            <a href="team.php" class="btn btn-secondary"><i class="fas fa-times"></i> Cancel</a>
                        </div>
                    </form>
                
                <?php elseif ($action === 'edit' && $currentMember): ?>
                    <a href="team.php" class="back-btn"><i class="fas fa-arrow-left"></i> Back to Team</a>
                    <h2><i class="fas fa-edit"></i> Edit Team Member</h2>
                    
                    <form method="POST" enctype="multipart/form-data" style="max-width: 600px; margin-top: 20px;">
                        <input type="hidden" name="action" value="update">
                        <input type="hidden" name="id" value="<?php echo $currentMember['id']; ?>">
                        
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($currentMember['name']); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="position">Position</label>
                            <input type="text" id="position" name="position" value="<?php echo htmlspecialchars($currentMember['position'] ?? ''); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="photo">Photo</label>
                            <?php if (!empty($currentMember['photo'])): ?>
                                <div style="margin-bottom: 10px;">
                                    <img src="<?php echo htmlspecialchars($currentMember['photo']); ?>" alt="Current photo" style="max-width: 100px; height: auto; border-radius: 5px;">
                                    <p style="font-size: 12px; color: #666;">Current photo</p>
                                </div>
                            <?php endif; ?>
                            <input type="file" id="photo" name="photo" accept="image/*">
                            <p style="font-size: 12px; color: #666;">Leave empty to keep current photo</p>
                        </div>
                        
                        <h4>Social Media Links</h4>
                        <div class="form-group">
                            <label for="facebook">Facebook</label>
                            <input type="url" id="facebook" name="facebook" value="<?php echo htmlspecialchars($currentMember['facebook'] ?? ''); ?>" placeholder="https://facebook.com/...">
                        </div>
                        
                        <div class="form-group">
                            <label for="instagram">Instagram</label>
                            <input type="url" id="instagram" name="instagram" value="<?php echo htmlspecialchars($currentMember['instagram'] ?? ''); ?>" placeholder="https://instagram.com/...">
                        </div>
                        
                        <div class="form-group">
                            <label for="twitter">Twitter</label>
                            <input type="url" id="twitter" name="twitter" value="<?php echo htmlspecialchars($currentMember['twitter'] ?? ''); ?>" placeholder="https://twitter.com/...">
                        </div>
                        
                        <div class="form-group">
                            <label for="linkedin">LinkedIn</label>
                            <input type="url" id="linkedin" name="linkedin" value="<?php echo htmlspecialchars($currentMember['linkedin'] ?? ''); ?>" placeholder="https://linkedin.com/...">
                        </div>
                        
                        <div style="display: flex; gap: 10px;">
                            <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Update</button>
                            <a href="team.php" class="btn btn-secondary"><i class="fas fa-times"></i> Cancel</a>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
