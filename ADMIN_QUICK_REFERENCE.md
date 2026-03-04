# Admin Portal Quick Reference

## Login & Authentication
| Page | URL | Description |
|------|-----|-------------|
| Login | `/admin-login.php` | Admin portal login |
| Logout | `/admin-logout.php` | Logout and destroy session |

## Admin Dashboard & Management
| Page | URL | Features |
|------|-----|----------|
| Dashboard | `/admin/index.php` | Overview, statistics, quick actions |
| Users | `/admin/users.php` | Create, edit, delete users |
| Team | `/admin/team.php` | Manage team members with social links |
| Works | `/admin/works.php` | Manage portfolio works |
| Projects | `/admin/projects.php` | Manage projects |
| Partners | `/admin/partners.php` | Manage partners |
| Roles | `/admin/roles.php` | View/manage user roles |
| Privileges | `/admin/privileges.php` | View/manage system privileges |
| Statistics | `/admin/statistics.php` | System analytics & insights |

## Core Features by Module

### User Management (`/admin/users.php`)
**Actions:**
- Create new user: `?action=create`
- List all users: `?action=list` (default)
- Edit user: `?action=edit&id=X`

**Fields:**
- Username (required, unique)
- Password (required, bcrypt hashed)
- Role ID (optional)

**Sample Create Form:**
```php
POST /admin/users.php
action: create
username: john_doe
password: secure_password
role_id: 2
```

### Team Members (`/admin/team.php`)
**Actions:**
- Create: `?action=create`
- List: `?action=list`
- Edit: `?action=edit&id=X`

**Fields:**
- Name (required)
- Position (required)
- Photo URL (optional)
- Facebook/Instagram/Twitter/LinkedIn URLs (optional)

**Sample Entry:**
```
Name: John Smith
Position: Lead Developer
Photo: https://example.com/photo.jpg
Facebook: https://facebook.com/johnsmith
```

### Portfolio Works (`/admin/works.php`)
**Actions:**
- Create: `?action=create`
- List: `?action=list`
- Edit: `?action=edit&id=X`

**Fields:**
- Title (required)
- Photo URL (optional)

**Example:**
```
Title: E-Commerce Platform
Photo: https://example.com/work1.jpg
```

### Projects (`/admin/projects.php`)
**Fields:**
- ID
- Title
- Description (optional)

### Partners (`/admin/partners.php`)
**Fields:**
- Name
- Logo URL (optional)
- Website URL (optional)

### Roles (`/admin/roles.php`)
**Fields:**
- Role ID
- Role Name
- Description (optional)

**Default Roles:**
- 1 = Administrator
- 2 = Editor
- 3 = Viewer

### Privileges (`/admin/privileges.php`)
**Fields:**
- Privilege ID
- Privilege Name
- Description (optional)

### Statistics (`/admin/statistics.php`)
**Displays:**
- User count
- Team members count
- Works count
- Projects count
- Partners count
- Portal statistics (projects, happy clients, solutions, experts)

## Form Actions Reference

### POST Parameters Format

**Create Operation:**
```
action: create
[field1]: value1
[field2]: value2
```

**Update Operation:**
```
action: update
id: record_id
[field1]: new_value
[field2]: new_value
```

**Delete Operation:**
```
action: delete
id: record_id
```

## Database Tables Quick Reference

| Table | ID Field | Key Fields |
|-------|----------|-----------|
| USERS | user_id | user_name, role_id |
| TEAM | id | name, position |
| OUR_WORK | id | work_title, photo |
| OUR_PROJECT | id | title, description |
| PARTNER | id | name |
| ROLES | role_id | role_name |
| PRIVILEGES | privilege_id | privilege_name |
| COUNT | id | projects, happy_clients, complete_solutions, team_experts |

## Common Operations

### Add User
1. Go to `/admin/users.php`
2. Click "Add User"
3. Fill in username, password, select role
4. Submit form

### Add Team Member
1. Go to `/admin/team.php`
2. Click "Add Team Member"
3. Fill in name, position, add social links
4. Submit form

### Edit Content
1. Click "Edit" button on any list
2. Make changes
3. Click "Update"
4. Confirm success message

### Delete Content
1. Click "Delete" button
2. Confirm in dialog
3. Item removed permanently

## Session Variables
```php
$_SESSION['admin_user_id']    // Current user's ID
$_SESSION['admin_username']   // Current user's username
$_SESSION['admin_role_id']    // Current user's role ID
```

## Navigation Elements

**Sidebar Menu Items:**
- Dashboard
- Users
- Team Members
- Our Works
- Projects
- Partners
- Roles
- Privileges
- Statistics
- Logout

**Top Navbar Shows:**
- Current page title
- Current username
- Logout button
- User avatar (first letter)

## Color Scheme
- Primary: #667eea (Purple)
- Secondary: #764ba2 (Dark Purple)
- Success: #48bb78 (Green)
- Danger: #f56565 (Red)
- Warning: #ed8936 (Orange)
- Info: #4299e1 (Blue)

## Icons Used
- Dashboard: `fas fa-home`
- Users: `fas fa-users`
- Team: `fas fa-users-circle`
- Works: `fas fa-briefcase`
- Projects: `fas fa-project-diagram`
- Partners: `fas fa-handshake`
- Roles: `fas fa-user-tag`
- Privileges: `fas fa-shield-alt`
- Statistics: `fas fa-chart-bar`

## Error Handling

**Common Errors:**
- "Username and password required" - Empty fields
- "Invalid username or password" - Wrong credentials
- "Failed to create user" - Database error
- "Cannot delete current user" - Trying to delete self

**Solutions:**
1. Check error message on page
2. Verify input data
3. Check database connection
4. Review database integrity

## Performance Metrics

| Operation | Time | Notes |
|-----------|------|-------|
| Login | < 1s | Database query + session |
| Load dashboard | < 2s | Multiple queries |
| Load list page | < 2s | Table data query |
| Create item | < 1s | Insert + redirect |
| Edit item | < 1s | Update + redirect |
| Delete item | < 1s | Delete + redirect |

## Best Practices

1. **Always confirm before delete**
2. **Use strong passwords** (8+ chars, mixed case, numbers)
3. **Regular backups** before major changes
4. **Test new features** before production
5. **Monitor activity** through statistics
6. **Update content** regularly
7. **Review user access** quarterly

## Keyboard Shortcuts
- Tab - Navigate form fields
- Enter - Submit form
- Shift+Tab - Navigate backwards

## Mobile Responsive
- Sidebar collapses on mobile
- Tables scroll horizontally
- Touch-friendly buttons
- Breakpoint: 768px

## Browser Compatibility
- Chrome/Edge: Full support
- Firefox: Full support
- Safari: Full support
- IE11: Not supported

## File Locations
```
/admin-login.php              ← Start here
/admin/index.php              ← Dashboard
/admin/*.php                  ← Other admin pages
/config/app.php               ← Configuration
/classes/db.php               ← Database connection
/classes/models/*.mod.php     ← Data models
```

## Quick URL List
```
http://localhost/iot/admin-login.php        Login
http://localhost/iot/admin/                 Dashboard
http://localhost/iot/admin/users.php        Users
http://localhost/iot/admin/team.php         Team
http://localhost/iot/admin/works.php        Works
http://localhost/iot/admin/projects.php     Projects
http://localhost/iot/admin/partners.php     Partners
http://localhost/iot/admin/roles.php        Roles
http://localhost/iot/admin/privileges.php   Privileges
http://localhost/iot/admin/statistics.php   Statistics
http://localhost/iot/admin-logout.php       Logout
```

## Getting Help
1. Check README.md for detailed docs
2. Review ADMIN_SETUP.md for setup
3. Check browser console for JavaScript errors
4. Review PHP error logs
5. Verify database connection
