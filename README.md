# Simple Click to Extract and Plot Data

### Hi Interviewers, this is the code challenge repository from candidate William Sun: "Simple Click to Extract and Plot Data."

| Feature | Details |
| :--- | :--- |
| **Technology Stack** | Laravel 7.3, Bootstrap, and Font Awesome. |
| **Frontend Note** | No dedicated JavaScript frontend framework (e.g., Vue/React) was used. |
| **Input** | Accepts a Wikipedia URL, defaults to an example URL on load. |

---

## 🏗️ Project Structure and Components

The application follows the **Service Pattern** within Laravel to separate concerns:

| Component | Location | Role | Test Coverage |
| :--- | :--- | :--- | :--- |
| **Controller** | `app/Http/Controllers/WikiController.php` | Handles HTTP requests, manages external HTTP calls (to Wikipedia), and orchestrates the service layer. | **None** |
| **Service** | `app/Services/WikiPlotService.php` | Contains all core business logic: HTML parsing, numeric extraction, column identification, and image generation. | **Full** |
| **Views** | `resources/views/...` | Handles the simple input form and the results page display. | N/A |
| **Test** | `app/tests/Unit/WikiPlotServiceTest.php` | Contains unit tests for the Service layer logic. | **None** |

---

## 🚀 Getting Started (Setup Instructions)

Follow these steps to get the application running locally:

1.  **Clone the repository.**

2.  Install PHP dependencies using **Composer**:
    ```bash
    composer install
    ```

3.  Generate the secure application key:
    ```bash
    php artisan key:generate
    ```

4.  **Database/Environment Note:** This challenge does not require a database. All environment and database configuration files (`.env`, migrations) can be ignored.

5.  As no containerization (Docker) or complex frontend build is required, start the local development server:
    ```bash
    php artisan serve
    ```

6.  Open your browser and navigate to the application:
    [http://127.0.0.1:8000](http://127.0.0.1:8000)

7.  The application consists of two views: one for URL input, and the next for data demonstration and image display.

---

## 📂 Dynamically Created Files

The application performs file system writes only upon a successful URL submission and plot generation:

| Created Item | Location | Purpose |
| :--- | :--- | :--- |
| **Directory** | `storage/app/public/plots/` | Stores all generated image files. |
| **Plot Image File** | `storage/app/public/plots/wiki_plot_[timestamp].png` | The PNG image created by the PHP GD extension, named with a unique timestamp. |

> **Note:** You may need to run `php artisan storage:link` if your web server requires the `public/storage` symlink to view the images.

---

## ✅ Running Unit Tests

Unit tests are provided to validate the core logic, ensuring stability and correctness.

### Execution

To execute the full test suite, run the following command from the project root:

```bash
php artisan test
