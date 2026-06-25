# SkillCheck — Online Examination System

SkillCheck is a modern, feature-rich online examination and assessment system built on the **Laravel** framework. It is designed to facilitate convenient exam creation and management for instructors, a seamless test-taking environment for students, and comprehensive administration capabilities for system moderators.

---

## 🚀 Features by Role

### 👨‍🎓 Student Portal
- **Available Exams Dashboard:** Browse active exams with details on duration, question count, and availability.
- **Dynamic Test-Taking Interface:**
  - Single-question pagination with instant answer saving.
  - Custom question ordering support (supports question randomization).
  - Time-limit tracker per question.
- **Attempt History & Review:**
  - View details of past attempts.
  - Review submitted answers, correct answers, and marks awarded (dependent on exam visibility settings).

### 👩‍🏫 Instructor Portal
- **Exam Management:** Create, read, update, and delete exams with customized start/end times and durations.
- **Question Bank & Customizations:**
  - Add questions of different types: Multiple Choice (MCQ), True/False, Short Answer, and Essay.
  - Reorder questions using a drag-and-drop or index reordering interface.
  - Optional image uploads or external image URLs for visual question context.
  - Prevent edits to active exams via lock states.

- **Grading & Evaluation:**
  - Immediate auto-grading of MCQs, True/False, and Short Answer questions.
  - Dedicated interface to review and manually grade student Essay submissions.
  - Finalize grading to publish results.

### 👑 Admin Dashboard
- **System Metrics:** High-level overview of total users, active exams, and exam attempts.
- **User Moderation:** Manage student and instructor accounts, including toggling suspension/ban status.
- **Content Moderation:** Monitor, manage, and delete exams across the platform to ensure policy compliance.

---

## 🛠️ Technology Stack
- **Core Backend:** PHP 8.3+, Laravel 13.x
- **Frontend / Templating:** Laravel Blade, Vite 8.x, TailwindCSS v4, Bootstrap v5
- **Database:** MariaDB (default config), compatible with MySQL, PostgreSQL, SQLite, etc.

---

## ⚡ Getting Started

Follow these steps to set up the project locally.

### Prerequisites
- **PHP** >= 8.3
- **Composer** (PHP Package Manager)
- **Node.js** & **npm**

### Installation Steps

1. **Clone the Repository** and navigate to the project directory:
   ```bash
   git clone <repository-url> skillcheck
   cd skillcheck
   ```

2. **Install Composer Dependencies:**
   ```bash
   composer install
   ```

3. **Install npm Dependencies:**
   ```bash
   npm install
   ```

4. **Environment Configuration:**
   Copy the example environment file and generate the application key:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Database Setup & Seeding:**
   Run migrations and seed the database with core accounts and mock exam data:
   ```bash
   php artisan migrate --seed
   ```
   *Note: Ensure your database server is running and database credentials match those in `.env` (configured for MariaDB).*

6. **Compile Frontend Assets & Run Development Server:**
   You can run the full environment using:
   ```bash
   composer dev
   ```
   Or run the services individually:
   - Laravel Development Server: `php artisan serve`
   - Vite Compilation: `npm run dev`

---

## 🔑 Default Seeded Accounts

The database seeder generates three default user roles with the password `password` for testing:

| Role | Email | Username | Password |
| :--- | :--- | :--- | :--- |
| **Admin** | `admin@skillcheck.com` | `admin` | `password` |
| **Instructor** | `instructor@skillcheck.com` | `instructor` | `password` |
| **Student** | `student@skillcheck.com` | `student` | `password` |

---

## 📁 Project Structure Highlights

- **`app/Http/Controllers/`**
  - `AuthController.php` — User authentication & profile settings (with profile photo upload).
  - `Admin/` — Admin controls for user suspension and exam moderation.
  - `Instructor/` — Exam creation, question reordering, and submission grading.
  - `Student/` — Exam list, attempts management, dynamic test-taking views, and submission handling.
- **`app/Models/`** — Core Eloquent models: `User`, `Exam`, `Question`, `Option`, `ExamAttempt`, `StudentAnswer`.
- **`database/migrations/`** — Schema definitions supporting question randomization, question-locking states, and suspends.
- **`resources/views/`** — Blade layouts and templates structured neatly by role modules.
- **`routes/web.php`** — Role-based routes protected by authentication and role middleware.

---

## 📄 License
This project is open-sourced under the [MIT license](LICENSE).
