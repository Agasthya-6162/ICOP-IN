<?php
$pageTitle = "Courses Offered | Indira College of Pharmacy";
$metaDescription = "Pharmacy courses offered at Indira College of Pharmacy - B.Pharm, M.Pharm, D.Pharm with detailed curriculum and career prospects.";
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

    .content-section {
        padding: 60px 0;
        background: #f5f5f5;
    }

    .course-card {
        background: white;
        padding: 40px;
        border-radius: 8px;
        margin-bottom: 30px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        border-top: 6px solid #FF6B35;
    }

    .course-card h2 {
        color: #003366;
        font-size: 2.2rem;
        margin-bottom: 20px;
    }

    .course-header {
        background: #E8F4F8;
        padding: 20px;
        border-radius: 6px;
        margin-bottom: 25px;
    }

    .course-header p {
        margin: 8px 0;
        font-size: 1.05rem;
    }

    .course-header strong {
        color: #003366;
    }

    .semester-box {
        background: #f9f9f9;
        padding: 20px;
        border-left: 4px solid #0066CC;
        margin: 15px 0;
    }

    .semester-box h4 {
        color: #003366;
        margin-bottom: 10px;
    }

    .subject-list {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 10px;
        margin-top: 10px;
    }

    .subject-item {
        background: white;
        padding: 10px 15px;
        border-radius: 4px;
        border-left: 3px solid#FF6B35;
    }

    .career-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 15px;
        margin: 20px 0;
    }

    .career-item {
        background: #E8F4F8;
        padding: 15px;
        border-radius: 6px;
        text-align: center;
    }

    .career-item i {
        font-size: 2rem;
        color: #0066CC;
        margin-bottom: 10px;
    }
</style>

<div class="page-hero">
    <div class="container">
        <h1><i class="fas fa-book-open"></i> Courses Offered</h1>
        <p>Comprehensive pharmacy education programs</p>
    </div>
</div>

<main id="main-content">
    <section class="content-section">
        <div class="container">
            <!-- B.Pharm -->
            <div class="course-card">
                <h2><i class="fas fa-graduation-cap"></i> Bachelor of Pharmacy (B.Pharm)</h2>

                <div class="course-header">
                    <p><strong>Duration:</strong> 4 Years (8 Semesters)</p>
                    <p><strong>Intake:</strong> 100 Students</p>
                    <p><strong>Affiliation:</strong> Swami Ramanand Teerth Marathwada University Nanded</p>
                    <p><strong>Approval:</strong> PCI (Pharmacy Council of India)</p>
                </div>

                <h3 style="color: #003366; margin-top: 30px; margin-bottom: 15px;">Course Overview</h3>
                <p style="line-height: 1.8;">The B.Pharm program is designed to provide comprehensive knowledge in
                    pharmaceutical sciences. Students learn about drug formulation, manufacturing, quality control,
                    pharmacology, and patient care. The curriculum combines theoretical knowledge with practical
                    skills through extensive laboratory work and industrial training.</p>

                <h3 style="color: #003366; margin-top: 30px; margin-bottom: 15px;">Curriculum Highlights</h3>

                <div class="semester-box">
                    <h4>Semester I</h4>
                    <div class="subject-list">
                        <div class="subject-item">Human Anatomy and Physiology I</div>
                        <div class="subject-item">Pharmaceutical Analysis I</div>
                        <div class="subject-item">Pharmaceutics I</div>
                        <div class="subject-item">Pharmaceutical Inorganic Chemistry</div>
                        <div class="subject-item">Communication skills</div>
                        <div class="subject-item">Remedial Biology/Mathematics</div>
                    </div>
                </div>

                <div class="semester-box">
                    <h4>Semester II</h4>
                    <div class="subject-list">
                        <div class="subject-item">Human Anatomy and Physiology II</div>
                        <div class="subject-item">Pharmaceutical Organic Chemistry I</div>
                        <div class="subject-item">Biochemistry</div>
                        <div class="subject-item">Pathophysiology</div>
                        <div class="subject-item">Computer Applications in Pharmacy</div>
                        <div class="subject-item">Environmental sciences</div>
                    </div>
                </div>

                <div class="semester-box">
                    <h4>Semester III</h4>
                    <div class="subject-list">
                        <div class="subject-item">Pharmaceutical Organic Chemistry II</div>
                        <div class="subject-item">Physical Pharmaceutics I</div>
                        <div class="subject-item">Pharmaceutical Microbiology</div>
                        <div class="subject-item">Pharmaceutical Engineering</div>
                    </div>
                </div>

                <div class="semester-box">
                    <h4>Semester IV</h4>
                    <div class="subject-list">
                        <div class="subject-item">Pharmaceutical Organic Chemistry III</div>
                        <div class="subject-item">Medicinal Chemistry I</div>
                        <div class="subject-item">Physical Pharmaceutics II</div>
                        <div class="subject-item">Pharmacology I</div>
                        <div class="subject-item">Pharmacognosy and Phytochemistry I</div>
                    </div>
                </div>

                <div class="semester-box">
                    <h4>Semester V</h4>
                    <div class="subject-list">
                        <div class="subject-item">Medicinal Chemistry II</div>
                        <div class="subject-item">Industrial Pharmacy I</div>
                        <div class="subject-item">Pharmacology II</div>
                        <div class="subject-item">Pharmacognosy and Phytochemistry II</div>
                        <div class="subject-item">Pharmaceutical Jurisprudence</div>
                    </div>
                </div>

                <div class="semester-box">
                    <h4>Semester VI</h4>
                    <div class="subject-list">
                        <div class="subject-item">Medicinal Chemistry III</div>
                        <div class="subject-item">Pharmacology III</div>
                        <div class="subject-item">Herbal Drug Technology</div>
                        <div class="subject-item">Biopharmaceutics and Pharmacokinetics</div>
                        <div class="subject-item">Pharmaceutical Biotechnology</div>
                        <div class="subject-item">Quality Assurance</div>
                    </div>
                </div>

                <div class="semester-box">
                    <h4>Semester VII</h4>
                    <div class="subject-list">
                        <div class="subject-item">Instrumental Methods of Analysis</div>
                        <div class="subject-item">Industrial Pharmacy II</div>
                        <div class="subject-item">Pharmacy Practice</div>
                        <div class="subject-item">Novel Drug Delivery Systems</div>
                    </div>
                </div>

                <div class="semester-box">
                    <h4>Semester VIII</h4>
                    <div class="subject-list">
                        <div class="subject-item">Biostatistics and Research Methodology</div>
                        <div class="subject-item">Social and Preventive Pharmacy</div>
                        <div class="subject-item">Pharma Marketing Management</div>
                        <div class="subject-item">Pharmaceutical Regulatory Science</div>
                        <div class="subject-item">Pharmacovigilance</div>
                        <div class="subject-item">Project Work</div>
                    </div>
                </div>

                <h3 style="color: #003366; margin-top: 30px; margin-bottom: 15px;">Career Opportunities</h3>
                <div class="career-grid">
                    <div class="career-item">
                        <i class="fas fa-hospital"></i>
                        <p>Hospital Pharmacist</p>
                    </div>
                    <div class="career-item">
                        <i class="fas fa-industry"></i>
                        <p>Pharmaceutical Industry</p>
                    </div>
                    <div class="career-item">
                        <i class="fas fa-flask"></i>
                        <p>Quality Control</p>
                    </div>
                    <div class="career-item">
                        <i class="fas fa-microscope"></i>
                        <p>Research & Development</p>
                    </div>
                    <div class="career-item">
                        <i class="fas fa-briefcase"></i>
                        <p>Medical Representative</p>
                    </div>
                    <div class="career-item">
                        <i class="fas fa-pills"></i>
                        <p>Drug Regulatory Affairs</p>
                    </div>
                </div>
            </div>

            <!-- Pharm.D -->
            <div class="course-card">
                <h2><i class="fas fa-user-md"></i> Doctor of Pharmacy (Pharm.D)</h2>

                <div class="course-header">
                    <p><strong>Duration:</strong> 6 Years (5 Years Study + 1 Year Internship)</p>
                    <p><strong>Intake:</strong> 30 Students</p>
                    <p><strong>Affiliation:</strong> Swami Ramanand Teerth Marathwada University Nanded</p>
                    <p><strong>Approval:</strong> PCI (Pharmacy Council of India)</p>
                </div>

                <h3 style="color: #003366; margin-top: 30px; margin-bottom: 15px;">Course Overview</h3>
                <p style="line-height: 1.8;">Pharm.D is a professional doctoral degree in pharmacy. It is a clinical
                    oriented curriculum where students are trained to provide patient care. The course is designed to
                    prepare students for clinical practice and community pharmacy.</p>

                <h3 style="color: #003366; margin-top: 30px; margin-bottom: 15px;">Curriculum Highlights</h3>

                <div class="semester-box">
                    <h4>First Year</h4>
                    <div class="subject-list">
                        <div class="subject-item">Human Anatomy and Physiology</div>
                        <div class="subject-item">Pharmaceutics</div>
                        <div class="subject-item">Medicinal Biochemistry</div>
                        <div class="subject-item">Pharmaceutical Organic Chemistry</div>
                        <div class="subject-item">Pharmaceutical Inorganic Chemistry</div>
                        <div class="subject-item">Remedial Mathematics/Biology</div>
                    </div>
                </div>

                <div class="semester-box">
                    <h4>Second Year</h4>
                    <div class="subject-list">
                        <div class="subject-item">Pathophysiology</div>
                        <div class="subject-item">Pharmaceutical Microbiology</div>
                        <div class="subject-item">Pharmacognosy & Phytopharmaceuticals</div>
                        <div class="subject-item">Pharmacology-I</div>
                        <div class="subject-item">Community Pharmacy</div>
                        <div class="subject-item">Pharmacotherapeutics-I</div>
                    </div>
                </div>

                <div class="semester-box">
                    <h4>Third Year</h4>
                    <div class="subject-list">
                        <div class="subject-item">Pharmacology-II</div>
                        <div class="subject-item">Pharmaceutical Analysis</div>
                        <div class="subject-item">Pharmacotherapeutics-II</div>
                        <div class="subject-item">Pharmaceutical Jurisprudence</div>
                        <div class="subject-item">Medicinal Chemistry</div>
                        <div class="subject-item">Pharmaceutical Formulations</div>
                    </div>
                </div>

                <div class="semester-box">
                    <h4>Fourth Year</h4>
                    <div class="subject-list">
                        <div class="subject-item">Pharmacotherapeutics-III</div>
                        <div class="subject-item">Hospital Pharmacy</div>
                        <div class="subject-item">Clinical Pharmacy</div>
                        <div class="subject-item">Biostatistics & Research Methodology</div>
                        <div class="subject-item">Biopharmaceutics & Pharmacokinetics</div>
                        <div class="subject-item">Clinical Toxicology</div>
                    </div>
                </div>

                <div class="semester-box">
                    <h4>Fifth Year</h4>
                    <div class="subject-list">
                        <div class="subject-item">Clinical Research</div>
                        <div class="subject-item">Pharmacoepidemiology and Pharmacoeconomics</div>
                        <div class="subject-item">Clinical Pharmacokinetics & Pharmacotherapeutic Drug Monitoring</div>
                        <div class="subject-item">Clerkship</div>
                        <div class="subject-item">Project Work (Six Months)</div>
                    </div>
                </div>

                <div class="semester-box">
                    <h4>Sixth Year</h4>
                    <div class="subject-list">
                        <div class="subject-item">Internship or residency training including postings in speciality
                            units</div>
                    </div>
                </div>
            </div>

            <!-- M.Pharm -->
            <div class="course-card">
                <h2><i class="fas fa-user-graduate"></i> Master of Pharmacy (M.Pharm)</h2>

                <div class="course-header">
                    <p><strong>Duration:</strong> 2 Years (4 Semesters)</p>
                    <p><strong>Specializations:</strong> Pharmaceutics, Pharmaceutical Quality Assurance</p>
                    <p><strong>Affiliation:</strong> Swami Ramanand Teerth Marathwada University Nanded</p>
                    <p><strong>Approval:</strong> PCI (Pharmacy Council of India)</p>
                </div>

                <h3 style="color: #003366; margin-top: 30px; margin-bottom: 15px;">Program Structure</h3>
                <p style="line-height: 1.8;">The M.Pharm program offers advanced specialization in specific areas of
                    pharmacy. It involves in-depth theoretical study, advanced laboratory techniques, and a significant
                    research project component. Students are prepared for roles in R&D, academia, and regulatory bodies.
                </p>

                <div class="semester-box">
                    <h4>Semester I (Pharmaceutics)</h4>
                    <div class="subject-list">
                        <div class="subject-item">Modern Pharmaceutical Analytical Techniques</div>
                        <div class="subject-item">Drug Delivery System</div>
                        <div class="subject-item">Modern Pharmaceutics</div>
                        <div class="subject-item">Regulatory Affair</div>
                        <div class="subject-item">Pharmaceutics Practical I</div>
                        <div class="subject-item">Seminar/Assignment</div>
                    </div>
                </div>

                <div class="semester-box">
                    <h4>Semester II (Pharmaceutics)</h4>
                    <div class="subject-list">
                        <div class="subject-item">Molecular Pharmaceutics (Nano Tech and Targeted DDS)</div>
                        <div class="subject-item">Advanced Biopharmaceutics & Pharmacokinetics</div>
                        <div class="subject-item">Computer Aided Drug Delivery System</div>
                        <div class="subject-item">Cosmetic and Cosmeceuticals</div>
                        <div class="subject-item">Pharmaceutics Practical II</div>
                        <div class="subject-item">Seminar/Assignment</div>
                    </div>
                </div>

                <div class="semester-box">
                    <h4>Semester III</h4>
                    <div class="subject-list">
                        <div class="subject-item">Research Methodology and Biostatistics</div>
                        <div class="subject-item">Journal club</div>
                        <div class="subject-item">Discussion / Presentation (Proposal Presentation)</div>
                        <div class="subject-item">Research Work</div>
                    </div>
                </div>

                <div class="semester-box">
                    <h4>Semester IV</h4>
                    <div class="subject-list">
                        <div class="subject-item">Journal Club</div>
                        <div class="subject-item">Research Work</div>
                        <div class="subject-item">Discussion/Final Presentation</div>
                    </div>
                </div>
            </div>

            <!-- D.Pharm -->
            <div class="course-card">
                <h2><i class="fas fa-certificate"></i> Diploma in Pharmacy (D.Pharm)</h2>

                <div class="course-header">
                    <p><strong>Duration:</strong> 2 Years</p>
                    <p><strong>Intake:</strong> 60 Students</p>
                    <p><strong>Affiliation:</strong> MSBTE (Maharashtra State Board of Technical Education)</p>
                    <p><strong>Approval:</strong> PCI (Pharmacy Council of India)</p>
                </div>

                <h3 style="color: #003366; margin-top: 30px; margin-bottom: 15px;">Course Overview</h3>
                <p style="line-height: 1.8;">The D.Pharm program is the minimum qualification required to practice
                    pharmacy in India. It focuses on the basics of pharmacy, community pharmacy management, and
                    patient counseling. Ideal for those who wish to open their own pharmacy or work in hospital
                    pharmacies.</p>
            </div>
        </div>
    </section>
</main>

<?php include 'includes/footer.php'; ?>