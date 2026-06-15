-- Identity & Access
CREATE TABLE Users (
    user_id CHAR(36) PRIMARY KEY DEFAULT (UUID()),
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('student', 'instructor', 'admin') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
);


-- Exam Architecture
CREATE TABLE Exams (
    exam_id CHAR(36) PRIMARY KEY DEFAULT (UUID()),
    instructor_id CHAR(36) NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    start_time DATETIME,
    end_time DATETIME,
    duration_s INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT chk_exam_times CHECK (end_time > start_time),
    CONSTRAINT chk_duration CHECK (duration_s > 0),
    FOREIGN KEY (instructor_id) REFERENCES Users(user_id) ON DELETE CASCADE
);

CREATE TABLE Questions (
    question_id CHAR(36) PRIMARY KEY DEFAULT (UUID()),
    exam_id CHAR(36) NOT NULL,
    order_index INT NOT NULL,
    question_text TEXT NOT NULL,
    image_url VARCHAR(255) NULL, 
    type ENUM('multiple_choice', 'true_false', 'question_answer', 'essay') NOT NULL, 
    time_limit_s INT NULL, 
    marks DECIMAL(5,2) NOT NULL,
    CONSTRAINT chk_time_limit CHECK (time_limit_s > 0),
    CONSTRAINT chk_marks CHECK (marks >= 0),
    FOREIGN KEY (exam_id) REFERENCES Exams(exam_id) ON DELETE CASCADE,
    UNIQUE (exam_id, order_index)
);

CREATE TABLE Options (
    option_id CHAR(36) PRIMARY KEY DEFAULT (UUID()),
    question_id CHAR(36) NOT NULL,
    order_index INT NOT NULL,
    option_text TEXT NOT NULL,
    is_correct BOOLEAN NOT NULL DEFAULT FALSE,
    FOREIGN KEY (question_id) REFERENCES Questions(question_id) ON DELETE CASCADE,
    UNIQUE (question_id, order_index)
);

-- Evaluation & Tracking
CREATE TABLE Exam_Attempts (
    attempt_id CHAR(36) PRIMARY KEY DEFAULT (UUID()),
    exam_id CHAR(36) NOT NULL,
    student_id CHAR(36) NOT NULL,
    start_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    end_time DATETIME NULL,
    status ENUM('in_progress', 'submitted', 'graded') NOT NULL DEFAULT 'in_progress',
    CONSTRAINT chk_attempt_times CHECK (end_time >= start_time),
    FOREIGN KEY (exam_id) REFERENCES Exams(exam_id) ON DELETE CASCADE,
    FOREIGN KEY (student_id) REFERENCES Users(user_id) ON DELETE CASCADE
);

CREATE TABLE Student_Answers (
    answer_id CHAR(36) PRIMARY KEY DEFAULT (UUID()),
    attempt_id CHAR(36) NOT NULL,
    question_id CHAR(36) NOT NULL,
    selected_option CHAR(36) NULL,
    text_answer TEXT NULL,
    marks_awarded DECIMAL(5,2) NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT chk_marks_awarded CHECK (marks_awarded >= 0),
    FOREIGN KEY (attempt_id) REFERENCES Exam_Attempts(attempt_id) ON DELETE CASCADE,
    FOREIGN KEY (question_id) REFERENCES Questions(question_id) ON DELETE CASCADE,
    FOREIGN KEY (selected_option) REFERENCES Options(option_id) ON DELETE SET NULL,
    UNIQUE (attempt_id, question_id)
);