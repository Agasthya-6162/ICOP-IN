<?php
$pageTitle = "Search Results - Indira College of Pharmacy";
include 'includes/header.php';
?>

    <main id="main-content" style="padding: 60px 0; min-height: 50vh;">
        <div class="container">
            <h1>Search Results</h1>
            <p id="search-query-text">Showing results for: <span id="query-val"
                    style="font-weight: bold; color: var(--orange-accent);"></span></p>

            <div id="results-container" style="margin-top: 30px;">
                <div class="info-card"
                    style="padding: 20px; background: #fff; border-left: 5px solid var(--primary-blue);">
                    <p>Sorry, the live search index is currently being updated. Please try a different query or contact
                        us for specific information.</p>
                </div>
            </div>
        </div>
    </main>

    <script>
        const urlParams = new URLSearchParams(window.location.search);
        const query = urlParams.get('q');
        if (query) {
            document.getElementById('query-val').innerText = query;
        } else {
            document.getElementById('search-query-text').innerText = 'Please enter a search query.';
        }
    </script>

<?php include 'includes/footer.php'; ?>
