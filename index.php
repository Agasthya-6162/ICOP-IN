<?php
require_once 'backend/config.php';

// Fetch Banners
try {
    $db = Database::getInstance()->getConnection();
    $stmt = $db->query("SELECT * FROM banners WHERE is_active = 1 ORDER BY display_order ASC");
    $banners = $stmt->fetchAll();
} catch (Exception $e) {
    $banners = [];
    error_log("Error fetching banners: " . $e->getMessage());
}

// Fetch Latest Updates
try {
    $db = Database::getInstance()->getConnection();
    $stmt = $db->query("SELECT * FROM latest_updates WHERE is_active = 1 ORDER BY display_order ASC, created_at DESC");
    $tickerUpdates = $stmt->fetchAll();
} catch (Exception $e) {
    $tickerUpdates = [];
}

// Fetch Latest News
try {
    $db = Database::getInstance()->getConnection();
    $stmt = $db->query("SELECT * FROM notices WHERE is_active = 1 AND category != 'Event' ORDER BY publish_date DESC LIMIT 4");
    $latestNews = $stmt->fetchAll();
} catch (Exception $e) {
    $latestNews = [];
}

// Fetch Upcoming Events
try {
    $db = Database::getInstance()->getConnection();
    $stmt = $db->query("SELECT * FROM notices WHERE is_active = 1 AND category = 'Event' AND (expiry_date IS NULL OR expiry_date >= CURDATE()) ORDER BY publish_date ASC LIMIT 3");
    $upcomingEvents = $stmt->fetchAll();
} catch (Exception $e) {
    $upcomingEvents = [];
}

$pageTitle = "Home | Indira College of Pharmacy";
$metaDescription = "Indira College of Pharmacy - Premier pharmaceutical education and research institution offering quality education in pharmacy and pharmaceutical sciences.";
include 'includes/header.php';
?>

<!-- Breadcrumb Navigation -->
<div class="breadcrumb">
    <div class="container">
        <nav class="breadcrumb-nav" aria-label="Breadcrumb">
            <a href="index.php"><i class="fas fa-home"></i> Home</a>
            <span class="separator">/</span>
            <span class="current">Homepage</span>
        </nav>
    </div>
</div>

<!-- Announcement Ticker -->
<div class="announcement-ticker">
    <div class="container">
        <span class="ticker-label">Latest Updates:</span>
        <div class="ticker-content">
            <marquee behavior="scroll" direction="left" scrollamount="5">
                <?php if (count($tickerUpdates) > 0): ?>
                    <?php foreach ($tickerUpdates as $update): ?>
                        <span class="ticker-item">
                            <span class="new-badge">NEW</span>
                            <?php if (!empty($update['link_url'])): ?>
                                <a href="<?php echo htmlspecialchars($update['link_url']); ?>"
                                    style="color: inherit; text-decoration: none;">
                                    <?php echo htmlspecialchars($update['content']); ?>
                                </a>
                            <?php else: ?>
                                <?php echo htmlspecialchars($update['content']); ?>
                            <?php endif; ?>
                        </span>
                        <span class="ticker-separator">|</span>
                    <?php endforeach; ?>
                <?php else: ?>
                    <span class="ticker-item"><span class="new-badge">NEW</span> Admissions Open for Academic Year 2026-27 -
                        Apply Now!</span>
                    <span class="ticker-separator">|</span>
                    <span class="ticker-item">Welcome to Indira College of Pharmacy</span>
                <?php endif; ?>
            </marquee>
        </div>
    </div>
</div>

<!-- Main Content -->
<main id="main-content">
    <!-- Hero Slider -->
    <section class="hero-slider">
        <div class="slider-container">
            <?php if (count($banners) > 0): ?>
                <?php foreach ($banners as $index => $banner): ?>
                    <div class="slide <?php echo $index === 0 ? 'active' : ''; ?>">
                        <img src="<?php echo htmlspecialchars(SITE_URL . '/' . $banner['image_path']); ?>"
                            alt="<?php echo htmlspecialchars($banner['title']); ?>">
                        <div class="slide-caption">
                            <h3><?php echo htmlspecialchars($banner['title']); ?></h3>
                            <p><?php echo htmlspecialchars($banner['description']); ?></p>
                            <?php if (!empty($banner['link_url'])): ?>
                                <a href="<?php echo htmlspecialchars($banner['link_url']); ?>" class="read-more-btn"
                                    style="background: var(--orange-accent); margin-top: 15px;">Read More</a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <!-- Fallback Static Banners -->
                <div class="slide active">
                    <img src="images/building-entrance.jpg" alt="College Entrance Gate">
                    <div class="slide-caption">
                        <h3>Welcome to ICOP</h3>
                        <p>Gateway to Pharmaceutical Excellence</p>
                        <a href="apply-online.php" class="read-more-btn"
                            style="background: var(--orange-accent); margin-top: 15px;">Apply Now 2026-27</a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        <button class="slider-btn prev" aria-label="Previous slide"><i class="fas fa-chevron-left"></i></button>
        <button class="slider-btn next" aria-label="Next slide"><i class="fas fa-chevron-right"></i></button>
        <div class="slider-dots"></div>
    </section>

    <!-- Secretary's Message -->
    <section class="principal-message" data-animate="fade-up">
        <div class="container">
            <div class="section-header">
                <h2>Secretary's Message</h2>
            </div>
            <div class="message-content">
                <div class="principal-photo">
                    <img src="images/secretary.jpg" alt="Dr. Santukrao Hambarde">
                    <h3>Dr. Santukrao Hambarde</h3>
                    <p class="designation">Secretary</p>
                    <p class="designation-sub">Sahayog Sevabhavi Sanstha</p>
                </div>
                <div class="message-text">
                    <p class="welcome-text">Building a Legacy of Excellence</p>
                    <p>It gives me immense pride to welcome you to Indira College of Pharmacy, a flagship
                        institution of Sahayog Sevabhavi Sanstha. Our vision has always been to provide world-class
                        education to students in Nanded and surrounding regions.</p>
                    <p>We are committed to creating an environment that nurtures intellectual growth and
                        professional competence. By providing state-of-the-art infrastructure, experienced faculty,
                        and modern facilities, we aim to empower our students to become global leaders in the
                        pharmaceutical sector.</p>
                    <p>I invite you to be a part of our journey towards academic excellence and social contribution.
                    </p>
                    <a href="about.php#secretary" class="read-more-btn">Read Full Message <i
                            class="fas fa-arrow-right"></i></a>
                </div>
            </div>
        </div>
    </section>

    <!-- Principal's Message -->
    <section class="principal-message" style="background-color: var(--light-gray);">
        <div class="container">
            <div class="section-header">
                <h2>Principal's Message</h2>
            </div>
            <!-- Inline style to reverse layout for variety -->
            <div class="message-content" style="flex-direction: row-reverse;">
                <div class="principal-photo" style="margin-right: 0; margin-left: 40px;">
                    <img src="images/principal-new.jpg" alt="Principal">
                    <h3 style="margin-top: 15px;">Dr. Vijay V. Navghare</h3>
                    <p class="designation">Principal</p>
                </div>
                <div class="message-text">
                    <p class="welcome-text">Inspiring Innovation & Excellence</p>
                    <p>Welcome to Indira College of Pharmacy. Our mission is to bridge the gap between academic
                        learning and industry requirements. We believe in fostering a culture of research,
                        innovation, and ethical practice among our students.</p>
                    <p>With a dedicated team of faculty and modern infrastructure, we ensure that every student
                        receives personalized attention and guidance. Our focus is not just on producing graduates,
                        but on shaping future leaders of the pharmaceutical world.</p>
                    <p>We look forward to guiding you on this exciting journey of learning and professional
                        development.</p>
                    <a href="principal-message.php" class="read-more-btn">Read Full Message <i
                            class="fas fa-arrow-right"></i></a>
                </div>
            </div>
        </div>
    </section>

    <!-- Quick Links Panel -->
    <section class="quick-links" data-animate="fade-up">
        <div class="container">
            <div class="quick-links-grid">
                <a href="admissions.php" class="quick-link-card">
                    <i class="fas fa-user-graduate"></i>
                    <h3>Admissions</h3>
                    <p>Join our pharmacy programs</p>
                </a>
                <a href="courses.php" class="quick-link-card">
                    <i class="fas fa-book-reader"></i>
                    <h3>Courses</h3>
                    <p>Explore our academic offerings</p>
                </a>
                <a href="results.php" class="quick-link-card">
                    <i class="fas fa-trophy"></i>
                    <h3>Results</h3>
                    <p>Check examination results</p>
                </a>
                <a href="notices.php" class="quick-link-card">
                    <i class="fas fa-bullhorn"></i>
                    <h3>Notices</h3>
                    <p>Latest updates & news</p>
                </a>
            </div>
        </div>
    </section>

    <!-- Latest News & Events -->
    <section class="news-events" data-animate="fade-up">
        <div class="container">
            <div class="news-events-grid">
                <div class="news-section">
                    <div class="section-header">
                        <h2>Latest News</h2>
                        <a href="notices.php" class="view-all">View All <i class="fas fa-arrow-right"></i></a>
                    </div>
                    <div class="news-list">
                        <?php if (count($latestNews) > 0): ?>
                            <?php foreach ($latestNews as $news): ?>
                                <div class="news-item">
                                    <div class="news-date">
                                        <span class="day"><?php echo date('d', strtotime($news['publish_date'])); ?></span>
                                        <span class="month"><?php echo date('M', strtotime($news['publish_date'])); ?></span>
                                    </div>
                                    <div class="news-content">
                                        <h4><a
                                                href="notices.php?id=<?php echo $news['id']; ?>"><?php echo htmlspecialchars($news['title']); ?></a>
                                        </h4>
                                        <p><?php echo substr(strip_tags($news['content']), 0, 100) . '...'; ?></p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p>No latest news at the moment.</p>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="events-section">
                    <div class="section-header">
                        <h2>Upcoming Events</h2>
                        <a href="notices.php?category=Event" class="view-all">View All <i
                                class="fas fa-arrow-right"></i></a>
                    </div>
                    <div class="events-list">
                        <?php if (count($upcomingEvents) > 0): ?>
                            <?php foreach ($upcomingEvents as $event): ?>
                                <div class="event-card">
                                    <div class="event-date">
                                        <i class="far fa-calendar-alt"></i>
                                        <?php echo date('d M Y', strtotime($event['publish_date'])); ?>
                                    </div>
                                    <h3><a
                                            href="notices.php?id=<?php echo $event['id']; ?>"><?php echo htmlspecialchars($event['title']); ?></a>
                                    </h3>
                                    <a href="notices.php?id=<?php echo $event['id']; ?>" class="event-link">Details <i
                                            class="fas fa-chevron-right"></i></a>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p>No upcoming events scheduled.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<?php include 'includes/footer.php'; ?>