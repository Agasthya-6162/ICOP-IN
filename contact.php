<?php
$pageTitle = "Contact Us | Indira College of Pharmacy";
$metaDescription = "Contact Indira College of Pharmacy - Get in touch with us for admissions, inquiries, and more.";
include 'includes/header.php';
?>

<!-- Hero Section -->
<div class="contact-hero">
    <div class="container">
        <div class="hero-icon">
            <i class="fas fa-phone-alt"></i>
        </div>
        <h1>Contact Us</h1>
        <p>We're here to help - Get in touch with us today</p>
    </div>
</div>

<!-- Main Content -->
<main id="main-content">
    <section class="contact-content">
        <div class="container">
            <div class="contact-grid">
                <!-- Contact Information -->
                <div class="contact-info-section">
                    <h2>Contact Information</h2>

                    <div class="contact-detail">
                        <div class="contact-icon">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div class="contact-text">
                            <h3>Address</h3>
                            <p>
                                Indira College of Pharmacy<br>
                                Near Rajiv Gandhi Infotech Park,<br>
                                Hinjewadi, Pune - 411057,<br>
                                Maharashtra, India
                            </p>
                        </div>
                    </div>

                    <div class="contact-detail">
                        <div class="contact-icon">
                            <i class="fas fa-phone"></i>
                        </div>
                        <div class="contact-text">
                            <h3>Phone</h3>
                            <p>
                                Main Office: <a href="tel:+912012345678">+91-20-1234-5678</a><br>
                                Admissions: <a href="tel:+912012345679">+91-20-1234-5679</a><br>
                                Accounts: <a href="tel:+912012345680">+91-20-1234-5680</a>
                            </p>
                        </div>
                    </div>

                    <div class="contact-detail">
                        <div class="contact-icon">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div class="contact-text">
                            <h3>Email</h3>
                            <p>
                                General: <a href="mailto:info@icop.edu.in">info@icop.edu.in</a><br>
                                Admissions: <a href="mailto:admissions@icop.edu.in">admissions@icop.edu.in</a><br>
                                Principal: <a href="mailto:principal@icop.edu.in">principal@icop.edu.in</a>
                            </p>
                        </div>
                    </div>

                    <div class="contact-detail">
                        <div class="contact-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="contact-text">
                            <h3>Office Hours</h3>
                            <p>
                                Monday - Friday: 9:00 AM - 5:00 PM<br>
                                Saturday: 9:00 AM - 1:00 PM<br>
                                Sunday & Holidays: Closed
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Contact Form -->
                <div class="contact-form-section">
                    <h2>Send Us a Message</h2>

                    <form id="contactForm" method="POST">
                        <div class="form-group">
                            <label for="name">Full Name *</label>
                            <input type="text" id="name" name="name" required>
                        </div>

                        <div class="form-group">
                            <label for="email">Email Address *</label>
                            <input type="email" id="email" name="email" required>
                        </div>

                        <div class="form-group">
                            <label for="phone">Phone Number</label>
                            <input type="tel" id="phone" name="phone">
                        </div>

                        <div class="form-group">
                            <label for="inquiry-type">Inquiry Type *</label>
                            <select id="inquiry-type" name="inquiryType" required>
                                <option value="">-- Select Type --</option>
                                <option value="admissions">Admissions</option>
                                <option value="academic">Academic</option>
                                <option value="research">Research Collaboration</option>
                                <option value="general">General Inquiry</option>
                                <option value="other">Other</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="message">Message *</label>
                            <textarea id="message" name="message" required></textarea>
                        </div>

                        <button type="submit" class="submit-btn">
                            <i class="fas fa-paper-plane"></i> Send Message
                        </button>
                    </form>
                </div>
            </div>

            <!-- Department Contacts -->
            <div class="department-contacts">
                <h2>Department Contacts</h2>
                <div class="dept-grid">
                    <div class="dept-card">
                        <h3>Admissions Office</h3>
                        <p><i class="fas fa-phone"></i> +91-20-1234-5679</p>
                        <p><i class="fas fa-envelope"></i> admissions@icop.edu.in</p>
                    </div>

                    <div class="dept-card">
                        <h3>Examination Cell</h3>
                        <p><i class="fas fa-phone"></i> +91-20-1234-5681</p>
                        <p><i class="fas fa-envelope"></i> exams@icop.edu.in</p>
                    </div>

                    <div class="dept-card">
                        <h3>Library</h3>
                        <p><i class="fas fa-phone"></i> +91-20-1234-5682</p>
                        <p><i class="fas fa-envelope"></i> library@icop.edu.in</p>
                    </div>

                    <div class="dept-card">
                        <h3>Accounts Office</h3>
                        <p><i class="fas fa-phone"></i> +91-20-1234-5680</p>
                        <p><i class="fas fa-envelope"></i> accounts@icop.edu.in</p>
                    </div>

                    <div class="dept-card">
                        <h3>Research & Development</h3>
                        <p><i class="fas fa-phone"></i> +91-20-1234-5683</p>
                        <p><i class="fas fa-envelope"></i> research@icop.edu.in</p>
                    </div>

                    <div class="dept-card">
                        <h3>Placement Cell</h3>
                        <p><i class="fas fa-phone"></i> +91-20-1234-5684</p>
                        <p><i class="fas fa-envelope"></i> placements@icop.edu.in</p>
                    </div>
                </div>
            </div>

            <!-- Map Section -->
            <div class="map-section">
                <h2>Location Map</h2>
                <div class="map-container">
                    <iframe
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3768.455209355682!2d77.29447337507925!3d19.175338982049615!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3bd1d66bb9d31721%3A0x4386266050b4356e!2sIndira%20College%20of%20Pharmacy%2C%20Nanded!5e0!3m2!1sen!2sin!4v1706606000000!5m2!1sen!2sin"
                        width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy">
                    </iframe>
                </div>
            </div>
        </div>
    </section>
</main>

<style>
    .contact-hero {
        background: linear-gradient(135deg, var(--primary-blue), var(--secondary-blue));
        color: var(--white);
        padding: 60px 0 40px;
        text-align: center;
    }

    .hero-icon {
        font-size: 3.5rem;
        color: var(--orange-accent);
        /* Changed to accent color for better visibility/contrast like cards */
        margin-bottom: 20px;
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);
    }

    .contact-hero h1 {
        font-size: 2.5rem;
        margin-bottom: 15px;
        color: var(--white) !important;
        font-weight: 700;
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
    }

    .contact-hero p {
        color: var(--white) !important;
        font-size: 1.2rem;
        opacity: 0.95;
    }

    .contact-content {
        padding: var(--spacing-xl) 0;
        background: var(--light-gray);
    }

    .contact-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: var(--spacing-lg);
    }

    .contact-info-section {
        background: var(--white);
        padding: var(--spacing-lg);
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .contact-info-section h2 {
        font-size: 1.8rem;
        color: var(--primary-blue);
        margin-bottom: var(--spacing-md);
        border-bottom: 3px solid var(--orange-accent);
        padding-bottom: 10px;
    }

    .contact-detail {
        display: flex;
        align-items: start;
        gap: 20px;
        margin-bottom: 25px;
        padding: 15px;
        background: var(--light-blue);
        border-radius: 6px;
    }

    .contact-icon {
        font-size: 2rem;
        color: var(--accent-blue);
        min-width: 50px;
        text-align: center;
    }

    .contact-text h3 {
        color: var(--primary-blue);
        font-size: 1.2rem;
        margin-bottom: 8px;
    }

    .contact-text p {
        color: var(--text-primary);
        line-height: 1.6;
    }

    .contact-text a {
        color: var(--accent-blue);
        font-weight: 600;
    }

    .contact-text a:hover {
        text-decoration: underline;
    }

    .contact-form-section {
        background: var(--white);
        padding: var(--spacing-lg);
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .contact-form-section h2 {
        font-size: 1.8rem;
        color: var(--primary-blue);
        margin-bottom: var(--spacing-md);
        border-bottom: 3px solid var(--orange-accent);
        padding-bottom: 10px;
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

    .map-section {
        margin-top: var(--spacing-lg);
        background: var(--white);
        padding: var(--spacing-lg);
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .map-section h2 {
        font-size: 1.8rem;
        color: var(--primary-blue);
        margin-bottom: var(--spacing-md);
        border-bottom: 3px solid var(--orange-accent);
        padding-bottom: 10px;
    }

    .map-container {
        width: 100%;
        height: 400px;
        border-radius: 8px;
        overflow: hidden;
    }

    .department-contacts {
        margin-top: var(--spacing-lg);
        background: var(--white);
        padding: var(--spacing-lg);
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .department-contacts h2 {
        font-size: 1.8rem;
        color: var(--primary-blue);
        margin-bottom: var(--spacing-md);
        border-bottom: 3px solid var(--orange-accent);
        padding-bottom: 10px;
    }

    .dept-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 20px;
    }

    .dept-card {
        padding: 20px;
        background: var(--light-blue);
        border-radius: 6px;
        border-left: 4px solid var(--accent-blue);
    }

    .dept-card h3 {
        color: var(--primary-blue);
        font-size: 1.2rem;
        margin-bottom: 10px;
    }

    .dept-card p {
        color: var(--text-primary);
        margin-bottom: 8px;
    }

    .dept-card i {
        color: var(--orange-accent);
        margin-right: 8px;
    }

    @media (max-width: 768px) {
        .contact-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<script>
    document.getElementById('contactForm').addEventListener('submit', function (e) {
        e.preventDefault();

        const form = this;
        const submitBtn = form.querySelector('.submit-btn');
        const originalBtnText = submitBtn.innerHTML;

        // Disable button and show loading state
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';

        const formData = new FormData(form);

        fetch('backend/api/submit_contact.php', {
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