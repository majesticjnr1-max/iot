<?php
session_start();

if (!isset($_SESSION['admin_user_id'])) {
    header('Location: ../admin-login.php');
    exit();
}

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../classes/db.php';
require_once __DIR__ . '/../classes/models/users.mod.php';
require_once __DIR__ . '/../classes/models/roles.mod.php';

$pdo = DB::getConnection();
$user = new User($pdo);
$role = new Role($pdo);

$message = '';
$error = '';
$action = $_GET['action'] ?? 'list';
$userId = $_GET['id'] ?? null;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'create') {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';
            $roleId = isset($_POST['role_id']) && $_POST['role_id'] !== '' ? intval($_POST['role_id']) : null;

            if (!empty($username) && !empty($password)) {
                $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
                $result = $user->create($username, $hashedPassword, $roleId);
                if ($result) {
                    $message = 'User created successfully!';
                    $action = 'list';
                } else {
                    $error = 'Failed to create user';
                }
            } else {
                $error = 'Username and password are required';
            }
        } elseif ($_POST['action'] === 'update') {
            $userId = $_POST['user_id'] ?? null;
            $roleId = isset($_POST['role_id']) && $_POST['role_id'] !== '' ? intval($_POST['role_id']) : null;
            $newPassword = $_POST['password'] ?? '';

            if ($userId) {
                if (!empty($newPassword)) {
                    $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
                    $user->updatePassword($userId, $hashedPassword);
                }
                $user->updateRole($userId, $roleId);
                $message = 'User updated successfully!';
                $action = 'list';
            }
        } elseif ($_POST['action'] === 'delete') {
            $userId = $_POST['user_id'] ?? null;
            if ($userId && $userId != $_SESSION['admin_user_id']) {
                if ($user->delete($userId)) {
                    $message = 'User deleted successfully!';
                } else {
                    $error = 'Failed to delete user';
                }
            } else {
                $error = 'Cannot delete current user';
            }
            $action = 'list';
        }
    }
}

$allUsers = $user->getAll();
$allRoles = $role->getAll();
$currentUser = null;

if ($action === 'edit' && $userId) {
    $currentUser = $user->find($userId);
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - Admin Portal</title>
    <link rel="stylesheet" href="../assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="shortcut icon" href="../assets/img/icons/IoT.png" type="image/x-icon">
    <style>
        :root {
            --primary: #667eea;
            --secondary: #764ba2;
            --success: #48bb78;
            --danger: #f56565;
            --warning: #ed8936;
            --info: #4299e1;
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
        
        .sidebar-header h3 {
            margin: 0;
            font-size: 18px;
            font-weight: 600;
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
            margin-bottom: 20px;
        }
        
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
        
        .page-header h2 {
            margin: 0;
            color: #333;
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
        
        .btn-primary:hover {
            background-color: #5a67d8;
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
            background-color: #e2e8f0;
            color: #4a5568;
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
        
        .back-btn:hover {
            text-decoration: underline;
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
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <i class="fas fa-sliders-h"></i>
                <h3>IoT Admin</h3>
            </div>
            
            <ul class="sidebar-menu">
                <li><a href="index.php"><i class="fas fa-home"></i> Dashboard</a></li>
                <li><a href="users.php" class="active"><i class="fas fa-users"></i> Users</a></li>
                <li><a href="team.php"><i class="fas fa-users-circle"></i> Team Members</a></li>
                <li><a href="works.php"><i class="fas fa-briefcase"></i> Our Works</a></li>
                <li><a href="projects.php"><i class="fas fa-project-diagram"></i> Projects</a></li>
                <li><a href="partners.php"><i class="fas fa-handshake"></i> Partners</a></li>
                <li><a href="roles.php"><i class="fas fa-user-tag"></i> Roles</a></li>
                <li><a href="privileges.php"><i class="fas fa-shield-alt"></i> Privileges</a></li>
                <li><a href="statistics.php"><i class="fas fa-chart-bar"></i> Statistics</a></li>
                <li><a href="testimonies.php"><i class="fas fa-comment-dots"></i> Testimonies</a></li>
                <li style="margin-top: 30px; border-top: 1px solid rgba(255,255,255,0.2); padding-top: 20px;">
                    <a href="../admin-logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                </li>
            </ul>
        </aside>
        
        <!-- Main Content -->
        <div class="main-content">
            <!-- Top Navbar -->
            <div class="top-navbar">
                <h2 style="margin: 0; color: #333;">Users</h2>
                <a href="../admin-logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
            
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
                        <h2><i class="fas fa-users"></i> Manage Users</h2>
                        <a href="users.php?action=create" class="btn btn-primary">
                            <i class="fas fa-user-plus"></i> Add User
                        </a>
                    </div>
                    
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Username</th>
                                <th>Role</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($allUsers as $u): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($u['user_id']); ?></td>
                                    <td><?php echo htmlspecialchars($u['user_name']); ?></td>
                                    <td><?php echo htmlspecialchars($u['role_id'] ?? 'N/A'); ?></td>
                                    <td>
                                        <div class="action-buttons">
                                            <a href="users.php?action=edit&id=<?php echo $u['user_id']; ?>" class="btn btn-secondary btn-sm">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                            <?php if ($u['user_id'] != $_SESSION['admin_user_id']): ?>
                                                <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure?');">
                                                    <input type="hidden" name="action" value="delete">
                                                    <input type="hidden" name="user_id" value="<?php echo $u['user_id']; ?>">
                                                    <button type="submit" class="btn btn-danger btn-sm">
                                                        <i class="fas fa-trash"></i> Delete
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                
                <?php elseif ($action === 'create'): ?>
                    <a href="users.php" class="back-btn"><i class="fas fa-arrow-left"></i> Back to Users</a>
                    <h2><i class="fas fa-user-plus"></i> Create New User</h2>
                    
                    <form method="POST" style="max-width: 500px; margin-top: 20px;">
                        <input type="hidden" name="action" value="create">
                        
                        <div class="form-group">
                            <label for="username">Username <span style="color: red;">*</span></label>
                            <input type="text" id="username" name="username" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="password">Password <span style="color: red;">*</span></label>
                            <input type="password" id="password" name="password" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="role_id">Role</label>
                            <select id="role_id" name="role_id">
                                <option value="">-- Select Role --</option>
                                <?php foreach ($allRoles as $r): ?>
                                    <option value="<?php echo $r['role_id']; ?>">
                                        <?php echo htmlspecialchars($r['role_name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div style="display: flex; gap: 10px;">
                            <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Create User</button>
                            <a href="users.php" class="btn btn-secondary"><i class="fas fa-times"></i> Cancel</a>
                        </div>
                    </form>
                
                <?php elseif ($action === 'edit' && $currentUser): ?>
                    <a href="users.php" class="back-btn"><i class="fas fa-arrow-left"></i> Back to Users</a>
                    <h2><i class="fas fa-edit"></i> Edit User</h2>
                    
                    <form method="POST" style="max-width: 500px; margin-top: 20px;">
                        <input type="hidden" name="action" value="update">
                        <input type="hidden" name="user_id" value="<?php echo $currentUser['user_id']; ?>">
                        
                        <div class="form-group">
                            <label>Username</label>
                            <input type="text" value="<?php echo htmlspecialchars($currentUser['user_name']); ?>" disabled>
                        </div>
                        
                        <div class="form-group">
                            <label for="password">New Password (leave blank to keep current)</label>
                            <input type="password" id="password" name="password">
                        </div>
                        
                        <div class="form-group">
                            <label for="role_id">Role</label>
                            <select id="role_id" name="role_id">
                                <option value="">-- Select Role --</option>
                                <?php foreach ($allRoles as $r): ?>
                                    <option value="<?php echo $r['role_id']; ?>" 
                                        <?php echo ($r['role_id'] == $currentUser['role_id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($r['role_name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div style="display: flex; gap: 10px;">
                            <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Update User</button>
                            <a href="users.php" class="btn btn-secondary"><i class="fas fa-times"></i> Cancel</a>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
