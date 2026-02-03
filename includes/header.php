<?php
// Get current page filename for active menu highlighting
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description"
        content="<?php echo isset($metaDescription) ? $metaDescription : 'Indira College of Pharmacy - Excellence in Pharmaceutical Education'; ?>">
    <title><?php echo isset($pageTitle) ? $pageTitle : 'Indira College of Pharmacy'; ?></title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Lato:wght@300;400;600;700&family=Noto+Sans+Devanagari:wght@300;400;600;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/animations.css">
    <link rel="icon" type="image/png" href="images/logo.png">
</head>

<body>
    <!-- Accessibility Bar -->
    <div class="accessibility-bar">
        <div class="container">
            <div class="accessibility-left">
                <a href="#main-content" class="skip-link">Skip to main content</a>
                <a href="screen-reader.php">Screen Reader Access</a>
            </div>
            <div class="accessibility-right">
                <div class="font-size-controls">
                    <span>Font Size:</span>
                    <button id="font-decrease" aria-label="Decrease font size">A-</button>
                    <button id="font-normal" aria-label="Normal font size">A</button>
                    <button id="font-increase" aria-label="Increase font size">A+</button>
                </div>
                <div class="language-toggle">
                    <a href="#" class="active">English</a>
                    <a href="#">हिंदी</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Utility Navigation (Top Quick Links) -->
    <div class="utility-nav">
        <div class="container">
            <a href="admissions.php"><i class="fas fa-user-plus"></i> Admissions</a>
            <a href="notices.php"><i class="fas fa-bell"></i> Notices</a>
            <a href="results.php"><i class="fas fa-chart-line"></i> Results</a>
            <a href="downloads.php"><i class="fas fa-download"></i> Downloads</a>
            <a href="rti.php"><i class="fas fa-file-alt"></i> RTI</a>
            <a href="admin/"><i class="fas fa-lock"></i> Admin</a>
            <a href="feedback.php"><i class="fas fa-comments"></i> Feedback</a>
            <a href="contact.php"><i class="fas fa-phone"></i> Contact</a>
        </div>
    </div>

    <!-- Top Header -->
    <header class="top-header">
        <div class="container">
            <div class="header-content">
                <div class="logo-section">
                    <img src="images/logo.png" alt="Indira College of Pharmacy Logo" class="college-logo">
                    <div class="college-name">
                        <h1 style="font-family: 'Noto Sans Devanagari', sans-serif;">इंदिरा कॉलेज ऑफ फार्मेसी</h1>
                        <h2>Indira College of Pharmacy</h2>
                        <p class="tagline">Excellence in Pharmaceutical Education & Research</p>
                    </div>
                </div>
                <div class="header-badges">
                    <img src="images/sanstha-logo.png" alt="Sahayog Sevabhavi Sanstha" class="badge"
                        title="Sahayog Sevabhavi Sanstha">
                    <img src="images/naac-badge.png" alt="NAAC Accredited" class="badge" title="A Grade NAAC Accredited"
                        loading="lazy">
                </div>
            </div>
        </div>
    </header>

    <!-- Main Navigation -->
    <nav class="main-nav" id="mainNav">
        <div class="container">
            <ul class="nav-menu">
                <li><a href="index.php" class="<?php echo $current_page == 'index.php' ? 'active' : ''; ?>"><i
                            class="fas fa-home"></i> Home</a></li>
                <li class="dropdown">
                    <a href="about.php"
                        class="<?php echo ($current_page == 'about.php' || $current_page == 'vision-mission.php' || $current_page == 'principal-message.php' || $current_page == 'naac.php') ? 'active' : ''; ?>"><i
                            class="fas fa-info-circle"></i> About <i class="fas fa-chevron-down"></i></a>
                    <ul class="dropdown-menu">
                        <li><a href="about.php">About College</a></li>
                        <li><a href="about.php#naac">NAAC Accreditation</a></li>
                        <li><a href="vision-mission.php">Vision & Mission</a></li>
                        <li><a href="principal-message.php">Principal's Message</a></li>
                    </ul>
                </li>
                <li class="dropdown">
                    <a href="courses.php"
                        class="<?php echo ($current_page == 'courses.php' || $current_page == 'departments.php' || $current_page == 'faculty.php' || $current_page == 'syllabus.php' || $current_page == 'academic-calendar.php') ? 'active' : ''; ?>"><i
                            class="fas fa-graduation-cap"></i> Academics <i class="fas fa-chevron-down"></i></a>
                    <ul class="dropdown-menu">
                        <li><a href="courses.php">Courses Offered</a></li>
                        <li><a href="departments.php">Departments</a></li>
                        <li><a href="faculty.php">Faculty</a></li>
                        <li><a href="syllabus.php">Syllabus</a></li>
                        <li><a href="academic-calendar.php">Academic Calendar</a></li>
                    </ul>
                </li>
                <li><a href="admissions.php" class="<?php echo $current_page == 'admissions.php' ? 'active' : ''; ?>"><i
                            class="fas fa-user-plus"></i> Admissions</a></li>
                <li><a href="student-corner.php"
                        class="<?php echo $current_page == 'student-corner.php' ? 'active' : ''; ?>"><i
                            class="fas fa-users"></i> Students</a></li>
                <li><a href="gallery.php" class="<?php echo $current_page == 'gallery.php' ? 'active' : ''; ?>"><i
                            class="fas fa-images"></i> Gallery</a></li>
                <li><a href="contact.php" class="<?php echo $current_page == 'contact.php' ? 'active' : ''; ?>"><i
                            class="fas fa-phone"></i> Contact</a></li>
                <li><a href="apply-online.php" class="nav-apply-btn">Apply Now</a></li>
            </ul>
            
            <div class="search-box">
                <input type="search" id="search" name="search" placeholder="Search..." aria-label="Search">
                <button type="submit" aria-label="Submit search"><i class="fas fa-search"></i></button>
            </div>
            
            <button class="mobile-toggle" aria-label="Toggle navigation menu">
                <i class="fas fa-bars"></i>
            </button>
        </div>
    </nav>