<?php
session_start();

if (!isset($_SESSION['admin_user_id'])) {
    header('Location: ../admin-login.php');
    exit();
}

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../classes/db.php';
require_once __DIR__ . '/../classes/models/our_work.mod.php';

$pdo = DB::getConnection();
$work = new OurWork($pdo);

$message = '';
$error = '';
$action = $_GET['action'] ?? 'list';
$workId = $_GET['id'] ?? null;

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
    $uploadDir = __DIR__ . '/../uploads/works';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    $filename = uniqid() . '.' . $ext;
    $destination = $uploadDir . '/' . $filename;
    if (!move_uploaded_file($file['tmp_name'], $destination)) {
        throw new Exception('Failed to move uploaded file.');
    }
    return '/iot/uploads/works/' . $filename;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'create') {
            $title = $_POST['title'] ?? '';
            
            try {
                $photo = handleFileUpload('photo');
            } catch (Exception $e) {
                $error = $e->getMessage();
                $photo = null;
            }

            if (!empty($title) && empty($error)) {
                $result = $work->create($title, $photo);
                if ($result) {
                    $message = 'Work created successfully!';
                    $action = 'list';
                } else {
                    $error = 'Failed to create work';
                }
            } else {
                $error = 'Title is required';
            }
        } elseif ($_POST['action'] === 'update') {
            $id = $_POST['id'] ?? null;
            $title = $_POST['title'] ?? '';
            
            try {
                $photo = handleFileUpload('photo');
            } catch (Exception $e) {
                $error = $e->getMessage();
                $photo = null;
            }
            
            // If no new file uploaded, keep the existing photo
            if ($photo === null && $id) {
                $currentWork = $work->find($id);
                $photo = $currentWork['photo'] ?? null;
            }

            if ($id && empty($error)) {
                $result = $work->update($id, $title, $photo);
                if ($result) {
                    $message = 'Work updated successfully!';
                } else {
                    $error = 'Failed to update work';
                }
                $action = 'list';
            }
        } elseif ($_POST['action'] === 'delete') {
            $id = $_POST['id'] ?? null;
            if ($id) {
                if ($work->delete($id)) {
                    $message = 'Work deleted successfully!';
                } else {
                    $error = 'Failed to delete work';
                }
            }
            $action = 'list';
        }
    }
}

$allWorks = $work->getAll();
$currentWork = null;

if ($action === 'edit' && $workId) {
    $currentWork = $work->find($workId);
}

// Common CSS/HTML structure
include 'admin-layout.php';
adminHeader('Works', 'works');
?>

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
                        <h2><i class="fas fa-briefcase"></i> Manage Our Works</h2>
                        <a href="works.php?action=create" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Add Work
                        </a>
                    </div>
                    
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Title</th>
                                <th>Photo</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($allWorks as $w): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($w['id']); ?></td>
                                    <td><?php echo htmlspecialchars($w['work_title']); ?></td>
                                    <td>
                                        <?php if (!empty($w['photo'])): ?>
                                            <img src="<?php echo htmlspecialchars($w['photo'], ENT_QUOTES); ?>" alt="<?php echo htmlspecialchars($w['work_title'], ENT_QUOTES); ?>" style="max-width: 80px; height: auto;">
                                        <?php else: ?>
                                            <span style="color: #999;">No image</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <a href="works.php?action=edit&id=<?php echo $w['id']; ?>" class="btn btn-secondary btn-sm">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                            <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure?');">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="id" value="<?php echo $w['id']; ?>">
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
                    <a href="works.php" class="back-btn"><i class="fas fa-arrow-left"></i> Back to Works</a>
                    <h2><i class="fas fa-plus"></i> Add New Work</h2>
                    
                    <form method="POST" enctype="multipart/form-data" style="max-width: 500px; margin-top: 20px;">
                        <input type="hidden" name="action" value="create">
                        
                        <div class="form-group">
                            <label for="title">Title <span style="color: red;">*</span></label>
                            <input type="text" id="title" name="title" required="required">
                        </div>
                        
                        <div class="form-group">
                            <label for="photo">Photo</label>
                            <input type="file" id="photo" name="photo" accept="image/*">
                        </div>
                        
                        <div style="display: flex; gap: 10px;">
                            <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Create</button>
                            <a href="works.php" class="btn btn-secondary"><i class="fas fa-times"></i> Cancel</a>
                        </div>
                    </form>
                
                <?php elseif ($action === 'edit' && $currentWork): ?>
                    <a href="works.php" class="back-btn"><i class="fas fa-arrow-left"></i> Back to Works</a>
                    <h2><i class="fas fa-edit"></i> Edit Work</h2>
                    
                    <form method="POST" enctype="multipart/form-data" style="max-width: 500px; margin-top: 20px;">
                        <input type="hidden" name="action" value="update">
                        <input type="hidden" name="id" value="<?php echo $currentWork['id']; ?>">
                        
                        <div class="form-group">
                            <label for="title">Title</label>
                            <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($currentWork['work_title'] ?? '', ENT_QUOTES); ?>" required="required">
                        </div>
                        
                        <div class="form-group">
                            <label for="photo">Photo</label>
                            <?php if (!empty($currentWork['photo'])): ?>
                                <div style="margin-bottom: 10px;">
                                    <img src="<?php echo htmlspecialchars($currentWork['photo']); ?>" alt="Current photo" style="max-width: 100px; height: auto; border-radius: 5px;">
                                    <p style="font-size: 12px; color: #666;">Current photo</p>
                                </div>
                            <?php endif; ?>
                            <input type="file" id="photo" name="photo" accept="image/*">
                            <p style="font-size: 12px; color: #666;">Leave empty to keep current photo</p>
                        </div>
                        
                        <div style="display: flex; gap: 10px;">
                            <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Update</button>
                            <a href="works.php" class="btn btn-secondary"><i class="fas fa-times"></i> Cancel</a>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
