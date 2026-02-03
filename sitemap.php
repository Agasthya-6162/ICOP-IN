<?php
$pageTitle = "Sitemap | Indira College of Pharmacy";
$metaDescription = "Sitemap of Indira College of Pharmacy website - Easily navigate to all pages including admissions, courses, faculty, and contact details.";
include 'includes/header.php';
?>

<style>
    .page-hero {
        background: linear-gradient(135deg, #003366, #00539C);
        color: white;
        padding: 60px 0 40px;
        text-align: center;
    }

    .page-hero h1 {
        font-size: 2.5rem;
        margin-bottom: 15px;
        color: #FFFFFF !important;
        font-weight: 700;
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
    }

    .page-hero p {
        color: #FFFFFF !important;
        font-size: 1.2rem;
        opacity: 0.95;
    }

    .sitemap-section {
        padding: 60px 0;
        background: #fff;
    }

    .sitemap-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 30px;
    }

    .sitemap-column h3 {
        color: #003366;
        border-bottom: 2px solid #FF6B35;
        padding-bottom: 10px;
        margin-bottom: 20px;
        font-size: 1.4rem;
    }

    .sitemap-list {
        list-style: none;
        padding: 0;
    }

    .sitemap-list li {
        margin-bottom: 12px;
    }

    .sitemap-list li a {
        color: #444;
        text-decoration: none;
        transition: color 0.3s;
        display: flex;
        align-items: center;
    }

    .sitemap-list li a:hover {
        color: #FF6B35;
        padding-left: 5px;
    }

    .sitemap-list li a i {
        margin-right: 10px;
        color: #00539C;
        font-size: 0.8rem;
    }
</style>

<div class="page-hero">
    <div class="container">
        <h1><i class="fas fa-sitemap"></i> Sitemap</h1>
        <p>Overview of website structure</p>
    </div>
</div>

<main id="main-content">
    <section class="sitemap-section">
        <div class="container">
            <div class="sitemap-grid">
                <!-- Main Pages -->
                <div class="sitemap-column">
                    <h3>Main Menu</h3>
                    <ul class="sitemap-list">
                        <li><a href="index.php"><i class="fas fa-chevron-right"></i> Home</a></li>
                        <li><a href="about.php"><i class="fas fa-chevron-right"></i> About Us</a></li>
                        <li><a href="vision-mission.php"><i class="fas fa-chevron-right"></i> Vision & Mission</a></li>
                        <li><a href="principal-message.php"><i class="fas fa-chevron-right"></i> Principal's Message</a>
                        </li>
                        <li><a href="governing-body.php"><i class="fas fa-chevron-right"></i> Governing Body</a></li>
                    </ul>
                </div>

                <!-- Academics -->
                <div class="sitemap-column">
                    <h3>Academics</h3>
                    <ul class="sitemap-list">
                        <li><a href="courses.php"><i class="fas fa-chevron-right"></i> Courses Offered</a></li>
                        <li><a href="departments.php"><i class="fas fa-chevron-right"></i> Departments</a></li>
                        <li><a href="faculty.php"><i class="fas fa-chevron-right"></i> Faculty</a></li>
                        <li><a href="syllabus.php"><i class="fas fa-chevron-right"></i> Syllabus</a></li>
                        <li><a href="academic-calendar.php"><i class="fas fa-chevron-right"></i> Academic Calendar</a>
                        </li>
                        <li><a href="results.php"><i class="fas fa-chevron-right"></i> Results</a></li>
                    </ul>
                </div>

                <!-- Admissions & Students -->
                <div class="sitemap-column">
                    <h3>Admissions & Student Corner</h3>
                    <ul class="sitemap-list">
                        <li><a href="admissions.php"><i class="fas fa-chevron-right"></i> Admission Procedure</a></li>
                        <li><a href="apply-online.php"><i class="fas fa-chevron-right"></i> Apply Online</a></li>
                        <li><a href="student-corner.php"><i class="fas fa-chevron-right"></i> Student Corner</a></li>
                        <li><a href="anti-ragging.php"><i class="fas fa-chevron-right"></i> Anti-Ragging</a></li>
                        <li><a href="grievance.php"><i class="fas fa-chevron-right"></i> Grievance Redressal</a></li>
                        <li><a href="downloads.php"><i class="fas fa-chevron-right"></i> Downloads</a></li>
                    </ul>
                </div>

                <!-- Information & Contact -->
                <div class="sitemap-column">
                    <h3>Information & Contact</h3>
                    <ul class="sitemap-list">
                        <li><a href="notices.php"><i class="fas fa-chevron-right"></i> Notices & News</a></li>
                        <li><a href="gallery.php"><i class="fas fa-chevron-right"></i> Photo Gallery</a></li>
                        <li><a href="contact.php"><i class="fas fa-chevron-right"></i> Contact Us</a></li>
                        <li><a href="feedback.php"><i class="fas fa-chevron-right"></i> Feedback</a></li>
                        <li><a href="rti.php"><i class="fas fa-chevron-right"></i> RTI</a></li>
                        <li><a href="admin/"><i class="fas fa-chevron-right"></i> Admin Login</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </section>
</main>

<?php include 'includes/footer.php'; ?>