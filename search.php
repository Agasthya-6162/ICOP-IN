<?php
$pageTitle = "Search Results | Indira College of Pharmacy";
include 'includes/header.php';

// Sanitize query
$query = isset($_GET['q']) ? trim(strip_tags($_GET['q'])) : '';
?>

<main id="main-content" style="padding: 60px 0; min-height: 50vh;">
    <div class="container">
        <h1>Search Results</h1>
        <p id="search-query-text">
            <?php if ($query): ?>
                Showing results for: <span
                    style="font-weight: bold; color: var(--orange-accent);"><?php echo htmlspecialchars($query); ?></span>
            <?php else: ?>
                Please enter a search term above.
            <?php endif; ?>
        </p>

        <div id="results-container" style="margin-top: 30px;">
            <?php
            if ($query) {
                // List of pages to search
                $pages = [
                    'index.php' => 'Home',
                    'about.php' => 'About Us',
                    'admissions.php' => 'Admissions',
                    'courses.php' => 'Courses',
                    'contact.php' => 'Contact Us',
                    'vision-mission.php' => 'Vision & Mission',
                    'principal-message.php' => 'Principal\'s Message',
                    'departments.php' => 'Departments',
                    'faculty.php' => 'Faculty',
                    'gallery.php' => 'Gallery',
                    'student-corner.php' => 'Student Corner',
                    'notices.php' => 'Notices',
                    'results.php' => 'Results',
                    'downloads.php' => 'Downloads',
                    'rti.php' => 'RTI',
                    'feedback.php' => 'Feedback'
                ];

                $resultsFound = false;
                $resultsCount = 0;

                foreach ($pages as $file => $title) {
                    if (file_exists($file)) {
                        $content = file_get_contents($file);

                        // Strip PHP tags and HTML tags to search plain text
                        $text_content = strip_tags($content);

                        // Simple case-insensitive search
                        if (stripos($text_content, $query) !== false) {
                            $resultsFound = true;
                            $resultsCount++;

                            // Get a snippet
                            $pos = stripos($text_content, $query);
                            $start = max(0, $pos - 100);
                            $length = 200;
                            $snippet = substr($text_content, $start, $length);
                            $snippet = preg_replace('/' . preg_quote($query, '/') . '/i', '<mark>$0</mark>', $snippet);

                            echo '<div class="search-result" style="margin-bottom: 25px; padding-bottom: 25px; border-bottom: 1px solid #eee;">';
                            echo '<h3><a href="' . $file . '" style="color: var(--primary-blue); text-decoration: none;">' . $title . '</a></h3>';
                            echo '<p style="color: #666; font-size: 0.9em;">...' . $snippet . '...</p>';
                            echo '<a href="' . $file . '" style="color: var(--accent-blue); font-size: 0.9em; font-weight: 600;">Read More &rarr;</a>';
                            echo '</div>';
                        }
                    }
                }

                if (!$resultsFound) {
                    echo '<div class="info-card" style="padding: 20px; background: #fff; border-left: 5px solid var(--orange-accent);">';
                    echo '<p>No results found for "' . htmlspecialchars($query) . '". Please try a different keyword.</p>';
                    echo '</div>';
                }
            }
            ?>
        </div>
    </div>
</main>

<style>
    .search-result h3 a:hover {
        color: var(--accent-blue) !important;
        text-decoration: underline !important;
    }

    mark {
        background-color: #fff3cd;
        padding: 0 2px;
    }
</style>

<?php include 'includes/footer.php'; ?>