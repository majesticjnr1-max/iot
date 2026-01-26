-- ROLES table (for user permissions)
CREATE TABLE ROLES (
    role_id INT PRIMARY KEY AUTO_INCREMENT,
    role_name VARCHAR(50) NOT NULL,
    role_description TEXT
);

-- PRIVILEGES table (for granular permissions)
CREATE TABLE PRIVILEGES (
    privilege_id INT PRIMARY KEY AUTO_INCREMENT,
    privilege_name VARCHAR(100) NOT NULL,
    privilege_description TEXT,
    module VARCHAR(50) -- e.g., 'USERS', 'PROJECTS', 'TEAM'
);

-- USERS table
CREATE TABLE USERS (
    user_id INT PRIMARY KEY AUTO_INCREMENT,
    user_name VARCHAR(100) NOT NULL,
    password VARCHAR(255) NOT NULL,
    role_id INT,
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (role_id) REFERENCES ROLES(role_id)
);

-- OUR WORK table
CREATE TABLE OUR_WORK (
    id INT PRIMARY KEY AUTO_INCREMENT,
    work_title VARCHAR(255) NOT NULL,
    photo VARCHAR(500)
);

-- TEAM table
CREATE TABLE TEAM (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    position VARCHAR(150) NOT NULL,
    photo VARCHAR(500),
    facebook VARCHAR(500),
    instagram VARCHAR(500),
    twitter VARCHAR(500),
    linkedin VARCHAR(500)
);

-- OUR PROJECT table
CREATE TABLE OUR_PROJECT (
    id INT PRIMARY KEY AUTO_INCREMENT,
    project_name VARCHAR(255) NOT NULL,
    project_description TEXT,
    photo VARCHAR(500)
);

-- PARTNERS table
CREATE TABLE PARTNERS (
    id INT PRIMARY KEY AUTO_INCREMENT,
    partner_name VARCHAR(255) NOT NULL,
    partner_logo VARCHAR(500),
    partner_website VARCHAR(500)
);

-- COUNT table (for statistics/impact numbers)
CREATE TABLE COUNT (
    id INT PRIMARY KEY AUTO_INCREMENT,
    count_impact INT DEFAULT 0,
    count_project INT DEFAULT 0,
    count_member INT DEFAULT 0,
    count_trainees INT DEFAULT 0
);

-- Insert default roles
INSERT INTO ROLES (role_name, role_description) VALUES
('Admin', 'Full administrative access'),
('Editor', 'Can edit content'),
('Viewer', 'Read-only access');

-- Insert default privileges
INSERT INTO PRIVILEGES (privilege_name, privilege_description, module) VALUES
('view_dashboard', 'View dashboard', 'DASHBOARD'),
('manage_users', 'Manage user accounts', 'USERS'),
('manage_roles', 'Manage user roles', 'ROLES'),
('view_team', 'View team members', 'TEAM'),
('edit_team', 'Edit team members', 'TEAM'),
('view_projects', 'View projects', 'PROJECTS'),
('edit_projects', 'Edit projects', 'PROJECTS'),
('view_work', 'View our work', 'WORK'),
('edit_work', 'Edit our work', 'WORK'),
('view_partners', 'View partners', 'PARTNERS'),
('edit_partners', 'Edit partners', 'PARTNERS'),
('manage_counts', 'Manage count statistics', 'COUNTS');

-- Insert a default admin user (password: admin123 - you should hash this in production)
INSERT INTO USERS (user_name, password, role_id) VALUES
('admin', 'admin123', 1);

-- Insert sample count data
INSERT INTO COUNT (count_impact, count_project, count_member, count_trainees) VALUES
(0, 0, 0, 0);