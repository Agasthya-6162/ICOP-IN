<?php
$pageTitle = "Student Corner | Indira College of Pharmacy";
$metaDescription = "Student Corner - Indira College of Pharmacy. Access exam notifications, syllabus downloads, and submit grievances anonymously.";
include 'includes/header.php';
?>

<style>
    .student-hero {
        background: linear-gradient(135deg, var(--primary-blue), var(--secondary-blue));
        color: var(--white);
        padding: 60px 0 40px;
        text-align: center;
    }

    .student-hero h1 {
        font-size: 2.5rem;
        margin-bottom: 15px;
        color: #FFFFFF !important;
        font-weight: 700;
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
    }

    .student-hero p {
        font-size: 1.2rem;
        opacity: 0.95;
        color: #FFFFFF !important;
    }

    .student-services {
        padding: var(--spacing-xl) 0;
    }

    .services-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: var(--spacing-lg);
        margin-bottom: var(--spacing-xl);
    }

    .service-card {
        background: var(--white);
        padding: var(--spacing-lg);
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        border-top: 4px solid var(--accent-blue);
    }

    .service-card i {
        font-size: 2.5rem;
        color: var(--accent-blue);
        margin-bottom: 15px;
    }

    .service-card h3 {
        font-size: 1.4rem;
        color: var(--primary-blue);
        margin-bottom: 15px;
    }

    .service-card ul {
        list-style: disc;
        margin-left: 20px;
    }

    .service-card ul li {
        margin-bottom: 10px;
        color: var(--text-secondary);
    }

    .service-card a {
        display: inline-block;
        margin-top: 15px;
        padding: 10px 20px;
        background: var(--accent-blue);
        color: var(--white);
        border-radius: 4px;
        font-weight: 600;
        transition: var(--transition-fast);
    }

    .service-card a:hover {
        background: var(--primary-blue);
        transform: translateY(-2px);
    }

    .grievance-section {
        background: var(--light-gray);
        padding: var(--spacing-xl) 0;
    }

    .grievance-form {
        max-width: 800px;
        margin: 0 auto;
        background: var(--white);
        padding: var(--spacing-xl);
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        display: block;
        font-weight: 600;
        margin-bottom: 8px;
        color: var(--primary-blue);
    }

    .form-group input,
    .form-group select,
    .form-group textarea {
        width: 100%;
        padding: 12px;
        border: 2px solid var(--border-gray);
        border-radius: 4px;
        font-family: var(--font-primary);
        font-size: 1rem;
        transition: var(--transition-fast);
    }

    .form-group input:focus,
    .form-group select:focus,
    .form-group textarea:focus {
        outline: none;
        border-color: var(--accent-blue);
    }

    .form-group textarea {
        resize: vertical;
        min-height: 120px;
    }

    .form-disclaimer {
        background: var(--light-blue);
        padding: 15px;
        border-left: 4px solid var(--accent-blue);
        margin-bottom: 20px;
        border-radius: 4px;
    }

    .form-disclaimer p {
        color: var(--text-primary);
        font-size: 0.95rem;
        line-height: 1.6;
    }

    .submit-btn {
        background: var(--success-green);
        color: var(--white);
        padding: 12px 40px;
        border: none;
        border-radius: 4px;
        font-size: 1.1rem;
        font-weight: 600;
        cursor: pointer;
        transition: var(--transition-fast);
    }

    .submit-btn:hover {
        background: #218838;
        transform: translateY(-2px);
    }
</style>

<!-- Hero Section -->
<div class="student-hero">
    <div class="container">
        <h1><i class="fas fa-users"></i> Student Corner</h1>
        <p>Your one-stop portal for academic resources and support</p>
    </div>
</div>

<!-- Main Content -->
<main id="main-content">
    <!-- Student Services -->
    <section class="student-services">
        <div class="container">
            <div class="section-header">
                <h2>Student Services</h2>
                <p>Access all academic resources and support services</p>
            </div>

            <div class="services-grid">
                <div class="service-card">
                    <i class="fas fa-calendar-check"></i>
                    <h3>Exam Notifications</h3>
                    <ul>
                        <li>End Semester Exams - Feb 2026</li>
                        <li>Practical Exam Schedule</li>
                        <li>Form Filling Dates</li>
                        <li>Revaluation Results</li>
                    </ul>
                    <a href="notices.php">View All Notifications <i class="fas fa-arrow-right"></i></a>
                </div>

                <div class="service-card">
                    <i class="fas fa-download"></i>
                    <h3>Syllabus Downloads</h3>
                    <ul>
                        <li>B.Pharm - All Semesters</li>
                        <li>M.Pharm - Pharmaceutics</li>
                        <li>M.Pharm - Pharmacology</li>
                        <li>D.Pharm - Year 1 & 2</li>
                    </ul>
                    <a href="syllabus.php">Download Syllabus <i class="fas fa-arrow-right"></i></a>
                </div>

                <div class="service-card">
                    <i class="fas fa-file-alt"></i>
                    <h3>Academic Resources</h3>
                    <ul>
                        <li>Previous Year Question Papers</li>
                        <li>Study Materials</li>
                        <li>Reference Books List</li>
                        <li>E-Learning Resources</li>
                    </ul>
                    <a href="downloads.php">Access Resources <i class="fas fa-arrow-right"></i></a>
                </div>

                <div class="service-card">
                    <i class="fas fa-chart-line"></i>
                    <h3>Results</h3>
                    <ul>
                        <li>Semester Results</li>
                        <li>Internal Assessment Marks</li>
                        <li>Grade Cards</li>
                        <li>Transcripts</li>
                    </ul>
                    <a href="results.php">Check Results <i class="fas fa-arrow-right"></i></a>
                </div>

                <div class="service-card">
                    <i class="fas fa-shield-alt"></i>
                    <h3>Anti-Ragging Cell</h3>
                    <ul>
                        <li>Anti-Ragging Committee</li>
                        <li>UGC Guidelines</li>
                        <li>Report Incident</li>
                        <li>Helpline Numbers</li>
                    </ul>
                    <a href="rti.php">Learn More <i class="fas fa-arrow-right"></i></a>
                </div>

                <div class="service-card">
                    <i class="fas fa-briefcase"></i>
                    <h3>Placements</h3>
                    <ul>
                        <li>Campus Recruitment Drive</li>
                        <li>Placement Statistics</li>
                        <li>Career Guidance</li>
                        <li>Industry Partnerships</li>
                    </ul>
                    <a href="#">View Details <i class="fas fa-arrow-right"></i></a>
                </div>
            </div>
        </div>
    </section>

    <!-- Grievance Submission Form -->
    <section class="grievance-section">
        <div class="container">
            <div class="section-header centered">
                <h2>Anonymous Grievance Submission</h2>
                <p>Your voice matters - Submit your concerns confidentially</p>
            </div>

            <div class="grievance-form">
                <div class="form-disclaimer">
                    <p><strong>Privacy Notice:</strong> This form allows you to submit complaints or feedback
                        anonymously. Your identity will remain completely confidential. However, providing contact
                        information (optional) helps us communicate updates regarding your concern.</p>
                </div>

                <form id="grievanceForm" method="POST">
                    <div class="form-group">
                        <label for="category">Category *</label>
                        <select id="category" name="category" required>
                            <option value="">-- Select Category --</option>
                            <option value="academic">Academic Issues</option>
                            <option value="infrastructure">Infrastructure & Facilities</option>
                            <option value="examination">Examination Related</option>
                            <option value="harassment">Harassment / Ragging</option>
                            <option value="administration">Administration</option>
                            <option value="other">Other</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="subject">Subject *</label>
                        <input type="text" id="subject" name="subject" placeholder="Brief subject of your concern"
                            required>
                    </div>

                    <div class="form-group">
                        <label for="message">Detailed Description *</label>
                        <textarea id="message" name="message" placeholder="Please describe your concern in detail..."
                            required></textarea>
                    </div>

                    <div class="form-group">
                        <label for="contact">Contact Information (Optional)</label>
                        <input type="text" id="contact" name="contact"
                            placeholder="Email or Phone (if you want a response)">
                    </div>

                    <button type="submit" class="submit-btn">
                        <i class="fas fa-paper-plane"></i> Submit Grievance
                    </button>
                </form>
            </div>
        </div>
    </section>
</main>

<script>
    document.getElementById('grievanceForm').addEventListener('submit', function (e) {
        e.preventDefault();

        const form = this;
        const submitBtn = form.querySelector('.submit-btn');
        const originalBtnText = submitBtn.innerHTML;

        // Disable button and show loading state
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Submitting...';

        const formData = new FormData(form);

        fetch('backend/api/submit_grievance.php', {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    form.reset();
                } else {
                    alert(data.message || 'An error occurred. Please try again.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
            })
            .finally(() => {
                // Restore button state
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnText;
            });
    });
</script>

<?php include 'includes/footer.php'; ?>