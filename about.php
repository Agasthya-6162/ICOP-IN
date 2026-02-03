<?php
require_once 'backend/config.php';
$pageTitle = "About Us | Indira College of Pharmacy";
$metaDescription = "About Indira College of Pharmacy - Learn about our history, mission, vision, and commitment to pharmaceutical education excellence.";
include 'includes/header.php';
?>

<!-- Breadcrumb Navigation -->
<div class="breadcrumb">
    <div class="container">
        <nav class="breadcrumb-nav" aria-label="Breadcrumb">
            <a href="index.php"><i class="fas fa-home"></i> Home</a>
            <span class="separator">/</span>
            <span class="current">About Us</span>
        </nav>
    </div>
</div>

<main id="main-content" style="padding: 60px 0; background: #f5f5f5;">
    <div class="container">
        <div style="background: white; padding: 40px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
            <h1
                style="color: #003366; font-size: 2.5rem; margin-bottom: 20px; border-bottom: 4px solid #FF6B35; padding-bottom: 15px;">
                About Indira College of Pharmacy</h1>

            <div style="line-height: 1.8; color: #2C3E50;">
                <p style="font-size: 1.1rem; margin-bottom: 20px;">Indira College of Pharmacy (ICOP) stands as a
                    beacon of excellence in pharmaceutical education and research. Established with the vision of
                    producing competent pharmacy professionals who can contribute significantly to the healthcare
                    sector, our institution has been consistently delivering quality education for over two decades.
                </p>

                <h2 style="color: #003366; font-size: 1.8rem; margin-top: 40px; margin-bottom: 15px;">Our Legacy
                </h2>
                <p style="margin-bottom: 20px;">Founded in 2008, Indira College of Pharmacy has grown from a modest
                    institution to one of the most respected pharmacy colleges in Maharashtra. Our journey has been
                    marked by continuous innovation in teaching methodologies, research initiatives, and industry
                    collaborations.</p>

                <h2 style="color: #003366; font-size: 1.8rem; margin-top: 40px; margin-bottom: 15px;">Accreditations
                    & Recognitions</h2>

                <!-- NAAC Collapsible Toggle Section -->
                <div class="naac-toggle-section" id="naac">
                    <div class="naac-toggle-header" onclick="toggleNAAC()">
                        <div>
                            <h3 class="naac-toggle-title">
                                <i class="fas fa-award"></i>
                                NAAC Accreditation
                            </h3>
                            <p class="naac-toggle-summary">Grade A | CGPA 3.13 | Valid until August 2028</p>
                        </div>
                        <i class="fas fa-chevron-down naac-toggle-icon"></i>
                    </div>

                    <div class="naac-content">
                        <!-- Achievement Highlights -->
                        <div class="naac-highlights">
                            <div class="naac-highlight-card">
                                <i class="fas fa-medal"></i>
                                <h4>Grade A</h4>
                                <p>NAAC Accreditation</p>
                            </div>
                            <div class="naac-highlight-card">
                                <i class="fas fa-chart-line"></i>
                                <h4>CGPA 3.13</h4>
                                <p>On 4-Point Scale</p>
                            </div>
                            <div class="naac-highlight-card">
                                <i class="fas fa-calendar-check"></i>
                                <h4>August 2023</h4>
                                <p>Accreditation Date</p>
                            </div>
                            <div class="naac-highlight-card">
                                <i class="fas fa-hourglass-half"></i>
                                <h4>5 Years</h4>
                                <p>Valid Until Aug 2028</p>
                            </div>
                        </div>

                        <!-- Certificates -->
                        <div class="naac-certificates">
                            <h3><i class="fas fa-certificate"
                                    style="margin-right: 10px; color: #667eea;"></i>Accreditation Certificates</h3>
                            <div class="certificate-grid">
                                <div class="certificate-card" onclick="openCertificate('images/naac-certificate.jpg')">
                                    <img src="images/naac-certificate.jpg" alt="NAAC Certificate of Accreditation">
                                    <div class="certificate-label">
                                        <h4>Certificate of Accreditation</h4>
                                        <p>Click to view full size</p>
                                    </div>
                                </div>
                                <div class="certificate-card"
                                    onclick="openCertificate('images/naac-quality-profile.jpg')">
                                    <img src="images/naac-quality-profile.jpg" alt="NAAC Quality Profile">
                                    <div class="certificate-label">
                                        <h4>Quality Profile</h4>
                                        <p>Click to view full size</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Quality Criteria -->
                        <div class="naac-criteria">
                            <h3><i class="fas fa-tasks" style="margin-right: 10px; color: #667eea;"></i>Seven Quality
                                Criteria</h3>
                            <div class="criteria-grid">
                                <div class="criteria-item">
                                    <h4><i class="fas fa-book-open"></i> Curricular Aspects</h4>
                                    <p>Curriculum design, implementation, and feedback mechanisms</p>
                                </div>
                                <div class="criteria-item">
                                    <h4><i class="fas fa-chalkboard-teacher"></i> Teaching-Learning & Evaluation</h4>
                                    <p>Student enrollment, teaching methodology, and assessment processes</p>
                                </div>
                                <div class="criteria-item">
                                    <h4><i class="fas fa-flask"></i> Research, Innovations & Extension</h4>
                                    <p>Research promotion, publications, and community engagement</p>
                                </div>
                                <div class="criteria-item">
                                    <h4><i class="fas fa-building"></i> Infrastructure & Learning Resources</h4>
                                    <p>Physical facilities, library, IT infrastructure, and maintenance</p>
                                </div>
                                <div class="criteria-item">
                                    <h4><i class="fas fa-user-graduate"></i> Student Support & Progression</h4>
                                    <p>Student welfare, guidance, placement, and alumni engagement</p>
                                </div>
                                <div class="criteria-item">
                                    <h4><i class="fas fa-users-cog"></i> Governance, Leadership & Management</h4>
                                    <p>Institutional vision, leadership, and administrative processes</p>
                                </div>
                                <div class="criteria-item">
                                    <h4><i class="fas fa-heart"></i> Institutional Values & Best Practices</h4>
                                    <p>Gender equity, environmental consciousness, and ethical practices</p>
                                </div>
                            </div>
                        </div>

                        <!-- About NAAC -->
                        <div
                            style="margin-top: 30px; padding: 20px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 8px; color: white;">
                            <h3 style="color: white; margin-bottom: 15px;"><i class="fas fa-info-circle"
                                    style="margin-right: 10px;"></i>About NAAC</h3>
                            <p style="margin-bottom: 10px; line-height: 1.8;">
                                The National Assessment and Accreditation Council (NAAC) is an autonomous institution
                                established by the University Grants Commission (UGC) of India to assess and accredit
                                institutions of higher education in the country.
                            </p>
                            <p style="margin: 0; line-height: 1.8;">
                                NAAC accreditation is a quality assurance mechanism that evaluates institutions on
                                various parameters including curricular aspects, teaching-learning processes, research,
                                infrastructure, student support, governance, and institutional values.
                            </p>
                        </div>
                    </div>
                </div>

                <h2 style="color: #003366; font-size: 1.8rem; margin-top: 40px; margin-bottom: 15px;">Other Recognitions
                </h2>
                <ul style="margin-left: 30px; margin-bottom: 20px;">
                    <li style="margin-bottom: 10px;"><strong>NAAC A+ Grade</strong> - Accredited by National
                        Assessment and Accreditation Council</li>
                    <li style="margin-bottom: 10px;"><strong>PCI Approved</strong> - Recognized by Pharmacy Council
                        of India</li>
                    <li style="margin-bottom: 10px;"><strong>AICTE Approved</strong> - All India Council for
                        Technical Education</li>
                    <li style="margin-bottom: 10px;"><strong>University Affiliated</strong> - Savitribai Phule Pune
                        University</li>
                </ul>

                <h2 style="color: #003366; font-size: 1.8rem; margin-top: 40px; margin-bottom: 15px;">Why Choose
                    ICOP?</h2>
                <div
                    style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-top: 20px;">
                    <div
                        style="background: #E8F4F8; padding: 20px; border-radius: 6px; border-left: 4px solid #0066CC;">
                        <h3 style="color: #003366; margin-bottom: 10px;"><i class="fas fa-chalkboard-teacher"
                                style="color: #0066CC; margin-right: 10px;"></i>Expert Faculty</h3>
                        <p>Highly qualified professors with Ph.D. and industry experience</p>
                    </div>
                    <div
                        style="background: #E8F4F8; padding: 20px; border-radius: 6px; border-left: 4px solid #0066CC;">
                        <h3 style="color: #003366; margin-bottom: 10px;"><i class="fas fa-microscope"
                                style="color: #0066CC; margin-right: 10px;"></i>Modern Labs</h3>
                        <p>State-of-the-art laboratories with latest equipment</p>
                    </div>
                    <div
                        style="background: #E8F4F8; padding: 20px; border-radius: 6px; border-left: 4px solid #0066CC;">
                        <h3 style="color: #003366; margin-bottom: 10px;"><i class="fas fa-briefcase"
                                style="color: #0066CC; margin-right: 10px;"></i>100% Placements</h3>
                        <p>Strong industry connections ensuring career success</p>
                    </div>
                    <div
                        style="background: #E8F4F8; padding: 20px; border-radius: 6px; border-left: 4px solid #0066CC;">
                        <h3 style="color: #003366; margin-bottom: 10px;"><i class="fas fa-book-open"
                                style="color: #0066CC; margin-right: 10px;"></i>Research Focus</h3>
                        <p>Active research programs and publications</p>
                    </div>
                </div>

                <h2 style="color: #003366; font-size: 1.8rem; margin-top: 40px; margin-bottom: 15px;">Our Commitment
                </h2>
                <p style="margin-bottom: 20px;">At ICOP, we are committed to:</p>
                <ul style="margin-left: 30px; margin-bottom: 20px;">
                    <li style="margin-bottom: 10px;">Providing quality pharmaceutical education</li>
                    <li style="margin-bottom: 10px;">Fostering research and innovation</li>
                    <li style="margin-bottom: 10px;">Developing industry-ready professionals</li>
                    <li style="margin-bottom: 10px;">Contributing to healthcare advancement</li>
                    <li style="margin-bottom: 10px;">Promoting ethical pharmacy practice</li>
                </ul>

                <!-- Secretary Section with anchor -->
                <div id="secretary" style="scroll-margin-top: 100px;">
                    <h2 style="color: #003366; font-size: 1.8rem; margin-top: 40px; margin-bottom: 15px;">Secretary's
                        Message</h2>
                    <p style="margin-bottom: 20px;"><strong>Dr. Santukrao Hambarde</strong>, Secretary of Sahayog
                        Sevabhavi Sanstha</p>
                    <p style="margin-bottom: 20px;">It gives me immense pride to welcome you to Indira College of
                        Pharmacy, a flagship institution of Sahayog Sevabhavi Sanstha. Our vision has always been to
                        provide world-class education to students in Nanded and surrounding regions.</p>
                    <p style="margin-bottom: 20px;">We are committed to creating an environment that nurtures
                        intellectual growth and professional competence. By providing state-of-the-art infrastructure,
                        experienced faculty, and modern facilities, we aim to empower our students to become global
                        leaders in the pharmaceutical sector.</p>
                    <p style="margin-bottom: 20px;">I invite you to be a part of our journey towards academic excellence
                        and social contribution.</p>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Certificate Modal -->
<div class="certificate-modal" id="certificateModal" onclick="closeCertificate()">
    <div class="modal-content" onclick="event.stopPropagation()">
        <button class="modal-close" onclick="closeCertificate()">&times;</button>
        <img id="modalImage" src="" alt="Certificate">
    </div>
</div>

<script>
// Toggle NAAC Section
function toggleNAAC() {
    const section = document.querySelector('.naac-toggle-section');
    section.classList.toggle('active');
}

// Open Certificate in Modal
function openCertificate(imageSrc) {
    const modal = document.getElementById('certificateModal');
    const modalImg = document.getElementById('modalImage');
    modalImg.src = imageSrc;
    modal.classList.add('active');
    document.body.style.overflow = 'hidden'; // Prevent scrolling
}

// Close Certificate Modal
function closeCertificate() {
    const modal = document.getElementById('certificateModal');
    modal.classList.remove('active');
    document.body.style.overflow = ''; // Restore scrolling
}

// Close modal on Escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeCertificate();
    }
});

// Auto-expand NAAC section if URL has #naac
window.addEventListener('DOMContentLoaded', function() {
    if (window.location.hash === '#naac') {
        const section = document.querySelector('.naac-toggle-section');
        section.classList.add('active');
        
        // Smooth scroll to section
        setTimeout(function() {
            section.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }, 100);
    }
});
</script>

<?php include 'includes/footer.php'; ?>