<?php
session_start();

if (!isset($_SESSION['admin_user_id'])) {
    header('Location: ../admin-login.php');
    exit();
}

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../classes/db.php';
require_once __DIR__ . '/../classes/models/testimonies.mod.php';
require_once __DIR__ . '/admin-layout.php';

$pdo = DB::getConnection();
$testimony = new Testimony($pdo);
$message = '';
$error = '';
$action = $_GET['action'] ?? 'list';
$testimonyId = $_GET['id'] ?? null;

// Helper function to handle file uploads: encode image to base64
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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'create') {
            $messageText = $_POST['message'] ?? '';
            $name = $_POST['name'] ?? '';
            $position = $_POST['position'] ?? '';
            
            try {
                $picture = handleFileUpload('picture');
                $avatar = handleFileUpload('avatar');
            } catch (Exception $e) {
                $error = $e->getMessage();
                $picture = null;
                $avatar = null;
            }
            
            if (!empty($messageText) && !empty($name) && empty($error)) {
                if ($testimony->create($picture, $messageText, $name, $position, $avatar)) {
                    $message = 'Testimony created successfully!';
                    $action = 'list';
                } else {
                    $error = 'Failed to create testimony';
                }
            } else {
                $error = 'Message and name are required';
            }
        } elseif ($_POST['action'] === 'update') {
            $id = $_POST['id'] ?? null;
            $messageText = $_POST['message'] ?? '';
            $name = $_POST['name'] ?? '';
            $position = $_POST['position'] ?? '';
            
            try {
                $picture = handleFileUpload('picture');
                $avatar = handleFileUpload('avatar');
            } catch (Exception $e) {
                $error = $e->getMessage();
                $picture = null;
                $avatar = null;
            }
            
            // If no new file uploaded, keep the existing files
            if ($id && ($picture === null || $avatar === null)) {
                $currentTestimony = $testimony->find($id);
                if ($picture === null) {
                    $picture = $currentTestimony['picture'] ?? null;
                }
                if ($avatar === null) {
                    $avatar = $currentTestimony['avatar'] ?? null;
                }
            }
            
            if ($id && empty($error)) {
                if ($testimony->update($id, $picture, $messageText, $name, $position, $avatar)) {
                    $message = 'Testimony updated successfully!';
                } else {
                    $error = 'Failed to update testimony';
                }
            }
            $action = 'list';
        } elseif ($_POST['action'] === 'delete') {
            $id = $_POST['id'] ?? null;
            if ($id) {
                if ($testimony->delete($id)) {
                    $message = 'Testimony deleted successfully!';
                } else {
                    $error = 'Failed to delete testimony';
                }
            }
            $action = 'list';
        }
    }
}

$allTestimonies = $testimony->getAll();
$currentTestimony = null;
if ($action === 'edit' && $testimonyId) {
    $currentTestimony = $testimony->find($testimonyId);
}

adminHeader('Testimonies', 'testimonies');
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
            <h2><i class="fas fa-comment-dots"></i> Manage Testimonies</h2>
            <a href="testimonies.php?action=create" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add Testimony
            </a>
        </div>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Picture</th>
                    <th>Message</th>
                    <th>Name</th>
                    <th>Position</th>
                    <th>Avatar</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($allTestimonies as $t): ?>
                <tr>
                    <td><?php echo htmlspecialchars($t['id']); ?></td>
                    <td><img src="<?php echo htmlspecialchars($t['picture']); ?>" alt="Picture" style="width:30px;height:30px;border-radius:50%;object-fit:cover;"></td>
                    <td><?php echo htmlspecialchars($t['message']); ?></td>
                    <td><?php echo htmlspecialchars($t['name']); ?></td>
                    <td><?php echo htmlspecialchars($t['position']); ?></td>
                    <td><img src="<?php echo htmlspecialchars($t['avatar']); ?>" alt="Avatar" style="width:32px;height:32px;border-radius:50%;object-fit:cover;"></td>
                    <td>
                        <div class="action-buttons">
                            <a href="testimonies.php?action=edit&id=<?php echo $t['id']; ?>" class="btn btn-secondary btn-sm"><i class="fas fa-edit"></i> Edit</a>
                            <form method="POST" style="display:inline;" onsubmit="return confirm('Are you sure?');">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?php echo $t['id']; ?>">
                                <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i> Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($allTestimonies)): ?>
                <tr><td colspan="7" style="text-align:center;padding:40px;">No testimonies found</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    <?php elseif ($action === 'create'): ?>
        <a href="testimonies.php" class="back-btn"><i class="fas fa-arrow-left"></i> Back to Testimonies</a>
        <h2><i class="fas fa-plus"></i> Add New Testimony</h2>
        <form method="POST" enctype="multipart/form-data" style="max-width:500px;margin-top:20px;">
            <input type="hidden" name="action" value="create">
            <div class="form-group">
                <label for="picture" >Picture</label>
                <input type="file" id="picture" name="picture" accept="image/*">
            </div>
            <div class="form-group">
                <label for="message">Message <span style="color:var(--color-rose);">*</span></label>
                <textarea id="message" name="message" rows="3" required></textarea>
            </div>
            <div class="form-group">
                <label for="name">Name <span style="color:var(--color-rose);">*</span></label>
                <input type="text" id="name" name="name" required>
            </div>
            <div class="form-group">
                <label for="position">Position</label>
                <input type="text" id="position" name="position">
            </div>
            <div class="form-group">
                <label for="avatar">Avatar</label>
                <input type="file" id="avatar" name="avatar" accept="image/*">
            </div>
            <div style="display:flex;gap:10px;">
                <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Create</button>
                <a href="testimonies.php" class="btn btn-secondary"><i class="fas fa-times"></i> Cancel</a>
            </div>
        </form>
    <?php elseif ($action === 'edit' && $currentTestimony): ?>
        <a href="testimonies.php" class="back-btn"><i class="fas fa-arrow-left"></i> Back to Testimonies</a>
        <h2><i class="fas fa-edit"></i> Edit Testimony</h2>
        <form method="POST" enctype="multipart/form-data" style="max-width:500px;margin-top:20px;">
            <input type="hidden" name="action" value="update">
            <input type="hidden" name="id" value="<?php echo $currentTestimony['id']; ?>">
            <div class="form-group">
                <label for="picture">Picture</label>
                <?php if (!empty($currentTestimony['picture'])): ?>
                    <div style="margin-bottom: 10px;">
                        <img src="<?php echo htmlspecialchars($currentTestimony['picture']); ?>" alt="Current picture" style="max-width: 100px; height: auto; border-radius: 5px;">
                        <p style="font-size: 12px; color: #666;">Current picture</p>
                    </div>
                <?php endif; ?>
                <input type="file" id="picture" name="picture" accept="image/*">
                <p style="font-size: 12px; color: #666;">Leave empty to keep current picture</p>
            </div>
            <div class="form-group">
                <label for="message">Message <span style="color:var(--color-rose);">*</span></label>
                <textarea id="message" name="message" rows="3" required><?php echo htmlspecialchars($currentTestimony['message']); ?></textarea>
            </div>
            <div class="form-group">
                <label for="name">Name <span style="color:var(--color-rose);">*</span></label>
                <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($currentTestimony['name']); ?>" required>
            </div>
            <div class="form-group">
                <label for="position">Position</label>
                <input type="text" id="position" name="position" value="<?php echo htmlspecialchars($currentTestimony['position']); ?>">
            </div>
            <div class="form-group">
                <label for="avatar">Avatar</label>
                <?php if (!empty($currentTestimony['avatar'])): ?>
                    <div style="margin-bottom: 10px;">
                        <img src="<?php echo htmlspecialchars($currentTestimony['avatar']); ?>" alt="Current avatar" style="max-width: 100px; height: 100px; border-radius: 50%; object-fit: cover;">
                        <p style="font-size: 12px; color: #666;">Current avatar</p>
                    </div>
                <?php endif; ?>
                <input type="file" id="avatar" name="avatar" accept="image/*">
                <p style="font-size: 12px; color: #666;">Leave empty to keep current avatar</p>
            </div>
            <div style="display:flex;gap:10px;">
                <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Update</button>
                <a href="testimonies.php" class="btn btn-secondary"><i class="fas fa-times"></i> Cancel</a>
            </div>
        </form>
    <?php endif; ?>
</div>