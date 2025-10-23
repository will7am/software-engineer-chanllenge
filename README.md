<h3>Hi Interviewers, this is the code challenge repository from candidate William Sun: "Simple Click to Extract and Plot Data."</h3>
<br>
<ul>
    <li>Technology Stack: Laravel 7.3, Bootstrap, and Font Awesome. No dedicated JavaScript frontend framework (e.g., Vue/React) was used.</li>
    <li>Input: Please use the provided field to enter a Wikipedia URL to start the process. The application defaults to an example URL upon load.</li>
</ul>

<hr>

<h3> Getting Started (Setup Instructions)</h3>
<ol>
    <li>To begin, please **clone the repository**.</li>
    <li>Install PHP dependencies using **Composer**:
        <pre><code>composer install</code></pre>
    </li>
    <li>Generate the secure application key:
        <pre><code>php artisan key:generate</code></pre>
    </li>
    <li>**Database/Storage Note:** Since this challenge uses a single-page flow and **does not require a database**, no migrations or environment file configuration are necessary. All generated plot images are saved to the local storage path.</li>
    <li>As no frontend framework or containerization (Docker) is used, start the local development server:
        <pre><code>php artisan serve</code></pre>
    </li>
    <li>Open your browser and navigate to:
        <a href="http://127.0.0.1:8000">http://127.0.0.1:8000</a>
    </li>
    <li>The application consists of two views: one for URL input, and the next for data demonstration and image display.</li>
</ol>
