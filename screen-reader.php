<?php
$pageTitle = "Screen Reader Access | Indira College of Pharmacy";
$metaDescription = "Information on screen reader access and accessibility features of the Indira College of Pharmacy website.";
include 'includes/header.php';
?>

    <!-- Breadcrumb -->
    <div class="breadcrumb">
        <div class="container">
            <nav class="breadcrumb-nav" aria-label="Breadcrumb">
                <a href="index.php"><i class="fas fa-home"></i> Home</a>
                <span class="separator">/</span>
                <span class="current">Screen Reader Access</span>
            </nav>
        </div>
    </div>

    <main id="main-content" style="padding: 60px 0; min-height: 60vh;">
        <div class="container">
            <h1>Screen Reader Access</h1>
            <p>Indira College of Pharmacy is committed to ensuring that our website is accessible to all users, regardless of device or ability. This website complies with World Wide Web Consortium (W3C) Web Content Accessibility Guidelines (WCAG) 2.0 level AA.</p>

            <section style="margin-top: 30px;">
                <h2>Screen Reader Compatibility</h2>
                <p>Our website is designed to be compatible with popular screen readers, including:</p>
                <ul>
                    <li><strong>NVDA (NonVisual Desktop Access):</strong> Free, open-source screen reader for Windows.</li>
                    <li><strong>JAWS (Job Access With Speech):</strong> Popular screen reader for Windows.</li>
                    <li><strong>VoiceOver:</strong> Built-in screen reader for macOS and iOS.</li>
                    <li><strong>TalkBack:</strong> Built-in screen reader for Android devices.</li>
                </ul>
            </section>

            <section style="margin-top: 30px;">
                <h2>Accessibility Features</h2>
                <ul>
                    <li><strong>Skip to Main Content:</strong> A link is provided at the top of each page to skip navigation and go directly to the main content area.</li>
                    <li><strong>Text Resize:</strong> Controls are provided in the accessibility bar to increase or decrease text size.</li>
                    <li><strong>High Contrast:</strong> The website uses colors with sufficient contrast for better readability.</li>
                    <li><strong>Descriptive Links:</strong> Links are written to make sense out of context.</li>
                    <li><strong>Alt Text:</strong> All images have alternative text descriptions.</li>
                </ul>
            </section>

            <section style="margin-top: 30px;">
                <h2>Keyboard Navigation</h2>
                <p>The website can be navigated using a keyboard:</p>
                <ul>
                    <li><strong>Tab:</strong> Move forward through interactive elements.</li>
                    <li><strong>Shift + Tab:</strong> Move backward through interactive elements.</li>
                    <li><strong>Enter:</strong> Activate links and buttons.</li>
                    <li><strong>Space:</strong> Activate buttons and checkboxes.</li>
                </ul>
            </section>
            
            <section style="margin-top: 30px;">
                <h2>Report an Issue</h2>
                <p>If you encounter any accessibility barriers on our website, please contact us at <a href="mailto:info@icop.edu.in">info@icop.edu.in</a> so we can improve the experience for everyone.</p>
            </section>
        </div>
    </main>

<?php include 'includes/footer.php'; ?>
