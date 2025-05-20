-- Create table for departments
CREATE TABLE departments (
    department_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    contact_person_name VARCHAR(255) NOT NULL,
    contact_person_email VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_department (name)
);

-- Create table for staff members
CREATE TABLE staff (
    staff_id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(255) NOT NULL,
    surname VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    gender CHAR(1) NOT NULL,
    department_id INT NOT NULL,
    batch_id INT NOT NULL,
    is_current BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (department_id) REFERENCES departments(department_id),
    UNIQUE KEY unique_email_batch (email, batch_id)
);

-- Create table for batches to track submissions
CREATE TABLE batches (
    batch_id INT AUTO_INCREMENT PRIMARY KEY,
    submission_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_current BOOLEAN DEFAULT TRUE
);

-- Create table for archived staff data
CREATE TABLE staff_archive (
    archive_id INT AUTO_INCREMENT PRIMARY KEY,
    staff_id INT NOT NULL,
    first_name VARCHAR(255) NOT NULL,
    surname VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    gender CHAR(1) NOT NULL,
    department_id INT NOT NULL,
    batch_id INT NOT NULL,
    archived_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
