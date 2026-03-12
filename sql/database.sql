-- Kamirex Specialist Hospital (HMS) Database Schema
-- Location: Lagos, Nigeria

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+01:00"; -- Lagos Time

-- 1. Departments Table
CREATE TABLE IF NOT EXISTS `departments` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `description` TEXT,
  `image` VARCHAR(255) DEFAULT 'dept_default.jpg',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. Users Table (Core Auth)
-- Roles: admin, doctor, nurse, receptionist, lab_tech, pharmacist, patient
CREATE TABLE IF NOT EXISTS `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `full_name` VARCHAR(255) NOT NULL,
  `email` VARCHAR(191) NOT NULL UNIQUE,
  `phone` VARCHAR(20),
  `password` VARCHAR(255) NOT NULL,
  `role` ENUM('admin', 'doctor', 'nurse', 'receptionist', 'lab_tech', 'pharmacist', 'patient') NOT NULL,
  `profile_pic` VARCHAR(255) DEFAULT 'default_user.jpg',
  `status` TINYINT(1) DEFAULT 1, -- 1-Active, 0-Suspended
  `last_login` DATETIME NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. Doctors Table (Linked to Users)
CREATE TABLE IF NOT EXISTS `doctors` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `dept_id` INT DEFAULT NULL,
  `specialization` VARCHAR(255),
  `biography` TEXT,
  `schedule` JSON NULL, -- { "Mon": ["08:00", "16:00"], ... }
  `consultation_fee` DECIMAL(10,2) DEFAULT 0.00,
  `availability_status` ENUM('Available', 'Busy', 'On Leave') DEFAULT 'Available',
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`dept_id`) REFERENCES `departments`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4. Patients Table (Linked to Users)
CREATE TABLE IF NOT EXISTS `patients` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `dob` DATE,
  `gender` ENUM('Male', 'Female', 'Other'),
  `address` TEXT,
  `blood_group` VARCHAR(10),
  `genotype` VARCHAR(10),
  `emergency_contact` VARCHAR(255),
  `nhis_id` VARCHAR(50) DEFAULT NULL, -- Nigeria Health Insurance Scheme
  `medical_history_notes` TEXT,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 5. Appointments Table
CREATE TABLE IF NOT EXISTS `appointments` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `patient_id` INT NOT NULL,
  `doctor_id` INT NOT NULL,
  `dept_id` INT NOT NULL,
  `appointment_date` DATETIME NOT NULL,
  `reason` TEXT,
  `status` ENUM('Pending', 'Approved', 'Rescheduled', 'Completed', 'Cancelled') DEFAULT 'Pending',
  `telemedicine_link` VARCHAR(255) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`patient_id`) REFERENCES `patients`(`id`),
  FOREIGN KEY (`doctor_id`) REFERENCES `doctors`(`id`),
  FOREIGN KEY (`dept_id`) REFERENCES `departments`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 6. Medical Records (EMR Core)
CREATE TABLE IF NOT EXISTS `medical_records` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `patient_id` INT NOT NULL,
  `doctor_id` INT NOT NULL,
  `visit_date` DATE NOT NULL,
  `symptoms` TEXT,
  `diagnosis` TEXT,
  `vitals` JSON NULL, -- { "BP": "120/80", "Temp": "37", "Pulse": "72" }
  `notes` TEXT,
  `attachments` JSON NULL, -- Array of file paths [ "scan1.jpg", "report1.pdf" ]
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`patient_id`) REFERENCES `patients`(`id`),
  FOREIGN KEY (`doctor_id`) REFERENCES `doctors`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 7. Prescriptions Table
CREATE TABLE IF NOT EXISTS `prescriptions` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `record_id` INT DEFAULT NULL, -- Linked to a specific medical record
  `patient_id` INT NOT NULL,
  `doctor_id` INT NOT NULL,
  `medicine_details` JSON NOT NULL, -- [ { "name": "Paracetamol", "dosage": "2x3", "duration": "5 days" } ]
  `instruction` TEXT,
  `status` ENUM('Pending', 'Dispensed') DEFAULT 'Pending',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`patient_id`) REFERENCES `patients`(`id`),
  FOREIGN KEY (`doctor_id`) REFERENCES `doctors`(`id`),
  FOREIGN KEY (`record_id`) REFERENCES `medical_records`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 8. Lab Tests Table
CREATE TABLE IF NOT EXISTS `lab_tests` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `patient_id` INT NOT NULL,
  `doctor_id` INT NOT NULL,
  `lab_tech_id` INT DEFAULT NULL,
  `test_name` VARCHAR(255) NOT NULL,
  `test_category` VARCHAR(100), -- Hematology, Biochemistry, etc.
  `status` ENUM('Requested', 'In Progress', 'Completed') DEFAULT 'Requested',
  `result` TEXT,
  `report_file` VARCHAR(255) DEFAULT NULL,
  `requested_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `completed_at` DATETIME NULL,
  FOREIGN KEY (`patient_id`) REFERENCES `patients`(`id`),
  FOREIGN KEY (`doctor_id`) REFERENCES `doctors`(`id`),
  FOREIGN KEY (`lab_tech_id`) REFERENCES `users`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 9. Invoices Table
CREATE TABLE IF NOT EXISTS `invoices` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `patient_id` INT NOT NULL,
  `total_amount` DECIMAL(10,2) NOT NULL,
  `discount` DECIMAL(10,2) DEFAULT 0.00,
  `billing_items` JSON NOT NULL, -- [ { "item": "Consultation", "price": 5000 }, ... ]
  `status` ENUM('Unpaid', 'Paid', 'Partial') DEFAULT 'Unpaid',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`patient_id`) REFERENCES `patients`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 10. Payments Table (Paystack Integration Support)
CREATE TABLE IF NOT EXISTS `payments` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `invoice_id` INT NOT NULL,
  `patient_id` INT NOT NULL,
  `amount` DECIMAL(10,2) NOT NULL,
  `payment_method` ENUM('Cash', 'Bank Transfer', 'Online (Paystack)') NOT NULL,
  `transaction_ref` VARCHAR(100), -- Paystack Ref
  `payment_status` VARCHAR(50), -- Success, Failed, etc.
  `payment_date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`invoice_id`) REFERENCES `invoices`(`id`),
  FOREIGN KEY (`patient_id`) REFERENCES `patients`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 11. Medicines Table (Inventory)
CREATE TABLE IF NOT EXISTS `medicines` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(255) NOT NULL,
  `category` VARCHAR(100),
  `stock_quantity` INT DEFAULT 0,
  `price` DECIMAL(10,2) NOT NULL,
  `expiry_date` DATE,
  `low_stock_threshold` INT DEFAULT 10,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 12. Chat & Telemedicine Sessions
CREATE TABLE IF NOT EXISTS `chat_messages` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `session_id` VARCHAR(100) NOT NULL,
  `sender_id` INT NOT NULL,
  `receiver_id` INT NOT NULL,
  `message` TEXT,
  `file_attached` VARCHAR(255) NULL,
  `sent_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `telemedicine_sessions` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `appointment_id` INT NOT NULL,
  `doctor_id` INT NOT NULL,
  `patient_id` INT NOT NULL,
  `session_token` VARCHAR(255) NOT NULL,
  `status` ENUM('Scheduled', 'Ongoing', 'Ended') DEFAULT 'Scheduled',
  `started_at` DATETIME NULL,
  `ended_at` DATETIME NULL,
  FOREIGN KEY (`appointment_id`) REFERENCES `appointments`(`id`),
  FOREIGN KEY (`doctor_id`) REFERENCES `doctors`(`id`),
  FOREIGN KEY (`patient_id`) REFERENCES `patients`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

COMMIT;
