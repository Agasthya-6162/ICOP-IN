<?php
$pageTitle = "Admission Application Form 2026-27 | Indira College of Pharmacy";
$metaDescription = "Apply online for B.Pharm, M.Pharm, and Pharm.D programs at Indira College of Pharmacy for the academic year 2026-27.";
include 'includes/header.php';
?>

    <!-- Page Hero -->
    <div class="page-hero">
        <div class="container">
            <h1>Admission Application Form 2026-27</h1>
            <p>Complete the three-step process to apply online</p>
        </div>
    </div>

    <!-- Main Content -->
    <main id="main-content" style="padding: 60px 0; background: #f8f9fa;">
        <div class="container">
            <!-- Application Form -->
            <div class="form-container" id="applicationForm">
                <!-- Step Indicators -->
                <div class="step-indicator">
                    <div class="step active" id="step1-indicator">1</div>
                    <div class="step" id="step2-indicator">2</div>
                    <div class="step" id="step3-indicator">3</div>
                    <div class="step-line"></div>
                </div>
                <div class="step-labels">
                    <div class="step-label">Personal Details</div>
                    <div class="step-label">Parent & Academic Info</div>
                    <div class="step-label">Document Upload</div>
                </div>

                <form id="admissionMainForm" onsubmit="event.preventDefault();">
                    <!-- Step 1: Personal Details -->
                    <div class="form-step active" id="step1">
                        <h3 class="section-title"><i class="fas fa-user"></i> Personal Information</h3>

                        <div class="form-row">
                            <div class="form-group">
                                <label>Course Applying For *</label>
                                <select id="course" required>
                                    <option value="">-- Select Course --</option>
                                    <option value="B.Pharm">B.Pharm (Bachelor of Pharmacy)</option>
                                    <option value="M.Pharm">M.Pharm (Master of Pharmacy)</option>
                                    <option value="D.Pharm">D.Pharm (Diploma in Pharmacy)</option>
                                    <option value="Pharm.D">Pharm.D (Doctor of Pharmacy)</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Application Number (Auto-generated)</label>
                                <input type="text" id="applicationNo" readonly style="background: #f5f5f5;">
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Full Name (As per SSC/HSC Mark Sheet) *</label>
                            <input type="text" id="fullName" required
                                placeholder="SURNAME FIRSTNAME MIDDLENAME (in capital letters)">
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label>Date of Birth *</label>
                                <input type="date" id="dob" required>
                            </div>
                            <div class="form-group">
                                <label>Gender *</label>
                                <select id="gender" required>
                                    <option value="">Select</option>
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label>Nationality *</label>
                                <input type="text" id="nationality" value="Indian" required>
                            </div>
                            <div class="form-group">
                                <label>Religion</label>
                                <input type="text" id="religion">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label>Category *</label>
                                <select id="category" required>
                                    <option value="OPEN">OPEN</option>
                                    <option value="OBC">OBC</option>
                                    <option value="SC">SC</option>
                                    <option value="ST">ST</option>
                                    <option value="VJ/NT">VJ/NT</option>
                                    <option value="EWS">EWS</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Blood Group</label>
                                <select id="bloodGroup">
                                    <option value="">Select</option>
                                    <option value="A+">A+</option>
                                    <option value="A-">A-</option>
                                    <option value="B+">B+</option>
                                    <option value="B-">B-</option>
                                    <option value="AB+">AB+</option>
                                    <option value="AB-">AB-</option>
                                    <option value="O+">O+</option>
                                    <option value="O-">O-</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label>Aadhaar Number *</label>
                                <input type="text" id="aadhaar" pattern="[0-9]{12}" required
                                    placeholder="12 Digit Aadhaar Number">
                            </div>
                            <div class="form-group">
                                <label>ABC ID (Academic Bank of Credits)</label>
                                <input type="text" id="abcId" placeholder="ABC ID (if available)">
                            </div>
                        </div>

                        <h3 class="section-title"><i class="fas fa-address-book"></i> Contact Information</h3>

                        <div class="form-row">
                            <div class="form-group">
                                <label>Mobile Number *</label>
                                <input type="tel" id="mobile" pattern="[0-9]{10}" required
                                    placeholder="10 Digit Mobile No">
                            </div>
                            <div class="form-group">
                                <label>Email Address *</label>
                                <input type="email" id="email" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Permanent Address *</label>
                            <textarea id="address" rows="3" required
                                placeholder="House No., Street, Village/Town"></textarea>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label>City *</label>
                                <input type="text" id="city" required>
                            </div>
                            <div class="form-group">
                                <label>District *</label>
                                <input type="text" id="district" required>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label>State *</label>
                                <input type="text" id="state" value="Maharashtra" required>
                            </div>
                            <div class="form-group">
                                <label>PIN Code *</label>
                                <input type="text" id="pincode" pattern="[0-9]{6}" required>
                            </div>
                        </div>

                        <div class="btn-group">
                            <div></div>
                            <button type="button" class="btn btn-next" onclick="nextStep(1)">
                                Save & Next <i class="fas fa-arrow-right"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Step 2: Parent & Academic Details -->
                    <div class="form-step" id="step2">
                        <h3 class="section-title"><i class="fas fa-user-friends"></i> Parent/Guardian Information</h3>

                        <div class="form-group">
                            <label>Father's Full Name *</label>
                            <input type="text" id="fatherName" required>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label>Father's Occupation *</label>
                                <input type="text" id="fatherOccupation" required>
                            </div>
                            <div class="form-group">
                                <label>Father's Annual Income *</label>
                                <select id="fatherIncome" required>
                                    <option value="">Select Range</option>
                                    <option value="Below 1 Lakh">Below ₹1 Lakh</option>
                                    <option value="1-3 Lakh">₹1-3 Lakh</option>
                                    <option value="3-5 Lakh">₹3-5 Lakh</option>
                                    <option value="5-8 Lakh">₹5-8 Lakh</option>
                                    <option value="Above 8 Lakh">Above ₹8 Lakh</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label>Father's Mobile Number *</label>
                                <input type="tel" id="fatherMobile" pattern="[0-9]{10}" required>
                            </div>
                            <div class="form-group">
                                <label>Father's Email</label>
                                <input type="email" id="fatherEmail">
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Mother's Full Name *</label>
                            <input type="text" id="motherName" required>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label>Mother's Occupation</label>
                                <input type="text" id="motherOccupation" placeholder="Homemaker/Working">
                            </div>
                            <div class="form-group">
                                <label>Mother's Mobile Number</label>
                                <input type="tel" id="motherMobile" pattern="[0-9]{10}">
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Guardian Name (If Different from Parents)</label>
                            <input type="text" id="guardianName">
                        </div>

                        <h3 class="section-title"><i class="fas fa-graduation-cap"></i> Academic Qualifications</h3>

                        <h4 style="color: #666; margin: 20px 0 15px;">SSC (10th Standard)</h4>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Board *</label>
                                <input type="text" id="sscBoard" required placeholder="e.g., Maharashtra State Board">
                            </div>
                            <div class="form-group">
                                <label>Year of Passing *</label>
                                <input type="number" id="sscYear" min="2000" max="2026" required>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label>Percentage/Grade *</label>
                                <input type="number" id="sscPercentage" step="0.01" min="0" max="100" required>
                            </div>
                            <div class="form-group">
                                <label>Seat Number</label>
                                <input type="text" id="sscSeatNo">
                            </div>
                        </div>

                        <h4 style="color: #666; margin: 20px 0 15px;">HSC/Diploma (12th Standard)</h4>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Stream/Board *</label>
                                <select id="hscStream" required>
                                    <option value="">Select</option>
                                    <option value="Science">HSC - Science</option>
                                    <option value="Commerce">HSC - Commerce</option>
                                    <option value="Arts">HSC - Arts</option>
                                    <option value="Diploma">Diploma</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Year of Passing *</label>
                                <input type="number" id="hscYear" min="2000" max="2026" required>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label>Percentage/Grade *</label>
                                <input type="number" id="hscPercentage" step="0.01" min="0" max="100" required>
                            </div>
                            <div class="form-group">
                                <label>Physics-Chemistry-Biology/Maths Marks</label>
                                <input type="number" id="pcbMarks" step="0.01">
                            </div>
                        </div>

                        <h4 style="color: #666; margin: 20px 0 15px;">Entrance Exam Details (If Applicable)</h4>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Exam Name</label>
                                <select id="entranceExam">
                                    <option value="">Select</option>
                                    <option value="MHT-CET">MHT-CET</option>
                                    <option value="NEET">NEET</option>
                                    <option value="JEE">JEE</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Score/Percentile</label>
                                <input type="number" id="entranceScore" step="0.01">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label>Roll Number</label>
                                <input type="text" id="entranceRollNo">
                            </div>
                            <div class="form-group">
                                <label>Rank (if applicable)</label>
                                <input type="number" id="entranceRank">
                            </div>
                        </div>

                        <div class="btn-group">
                            <button type="button" class="btn btn-prev" onclick="prevStep(2)">
                                <i class="fas fa-arrow-left"></i> Previous
                            </button>
                            <button type="button" class="btn btn-next" onclick="nextStep(2)">
                                Save & Next <i class="fas fa-arrow-right"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Step 3: Document Upload -->
                    <div class="form-step" id="step3">
                        <h3 class="section-title"><i class="fas fa-file-upload"></i> Upload Required Documents</h3>
                        <p style="color: #666; margin-bottom: 25px;">
                            <i class="fas fa-info-circle"></i> Please upload clear scanned copies in JPG/JPEG/PNG/PDF
                            format (Max 2MB each)
                        </p>

                        <h4 style="color: #666; margin: 20px 0 15px;">Photograph & Signature</h4>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Passport Size Photograph * <span style="color: #999;">(Recent,
                                        Color)</span></label>
                                <input type="file" id="photo" accept="image/*" required>
                                <small style="color: #666;">Size: 3.5cm x 4.5cm, White background</small>
                            </div>
                            <div class="form-group">
                                <label>Signature * <span style="color: #999;">(On white paper)</span></label>
                                <input type="file" id="signature" accept="image/*" required>
                                <small style="color: #666;">Sign on white paper and scan</small>
                            </div>
                        </div>

                        <h4 style="color: #666; margin: 25px 0 15px;">Identity Documents</h4>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Aadhaar Card * (Front & Back)</label>
                                <input type="file" id="aadhaarDoc" accept=".pdf,image/*" required>
                            </div>
                            <div class="form-group">
                                <label>PAN Card (If Available)</label>
                                <input type="file" id="panDoc" accept=".pdf,image/*">
                            </div>
                        </div>

                        <h4 style="color: #666; margin: 25px 0 15px;">Academic Documents</h4>
                        <div class="form-row">
                            <div class="form-group">
                                <label>SSC (10th) Marksheet *</label>
                                <input type="file" id="sscDoc" accept=".pdf,image/*" required>
                            </div>
                            <div class="form-group">
                                <label>HSC/Diploma Marksheet *</label>
                                <input type="file" id="hscDoc" accept=".pdf,image/*" required>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label>Leaving Certificate (LC/TC) *</label>
                                <input type="file" id="lcDoc" accept=".pdf,image/*" required>
                            </div>
                            <div class="form-group">
                                <label>Migration Certificate (If from Other State)</label>
                                <input type="file" id="migrationDoc" accept=".pdf,image/*">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label>Domicile Certificate</label>
                                <input type="file" id="domicileDoc" accept=".pdf,image/*">
                            </div>
                            <div class="form-group">
                                <label>Caste Certificate (If SC/ST/OBC/VJ-NT)</label>
                                <input type="file" id="casteDoc" accept=".pdf,image/*">
                            </div>
                        </div>

                        <h4 style="color: #666; margin: 25px 0 15px;">Entrance Exam (If Applicable)</h4>
                        <div class="form-row">
                            <div class="form-group">
                                <label>MHT-CET/NEET Scorecard</label>
                                <input type="file" id="entranceDoc" accept=".pdf,image/*">
                            </div>
                            <div class="form-group">
                                <label>Allotment Letter (CAP Round)</label>
                                <input type="file" id="allotmentDoc" accept=".pdf,image/*">
                            </div>
                        </div>

                        <div style="background: #fff3cd; padding: 15px; border-radius: 5px; margin: 20px 0;">
                            <label style="display: flex; align-items: flex-start; gap: 10px; cursor: pointer;">
                                <input type="checkbox" id="declaration" required style="margin-top: 4px;">
                                <span style="color: #856404; font-size: 0.95rem;">
                                    <strong>Declaration:</strong> I hereby declare that the information provided above
                                    is
                                    true and correct to the best of my knowledge. I understand that any false
                                    information
                                    may lead to cancellation of my admission.
                                </span>
                            </label>
                        </div>

                        <div class="btn-group">
                            <button type="button" class="btn btn-prev" onclick="prevStep(3)">
                                <i class="fas fa-arrow-left"></i> Previous
                            </button>
                            <button type="button" class="btn btn-submit" onclick="submitApplication()">
                                <i class="fas fa-check-circle"></i> Submit Application
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Success Banner -->
            <div class="success-banner" id="successBanner">
                <h2><i class="fas fa-check-circle" style="font-size: 3rem; color: #28a745;"></i></h2>
                <h2>Application Submitted Successfully!</h2>
                <p style="font-size: 1.1rem; margin: 15px 0;">
                    Your admission application has been received and is being processed.
                </p>

                <div class="reference-box">
                    <p style="margin-bottom: 10px; font-weight: 600;">Your Application Reference Number:</p>
                    <div class="reference-id" id="referenceId">ICOP2026-000001</div>
                    <p style="margin-top: 15px; font-size: 0.9rem; color: #666;">
                        <i class="fas fa-info-circle"></i> Please note this number for future reference
                    </p>
                </div>

                <div style="margin: 30px 0;">
                    <button class="btn btn-next" onclick="printApplication()" style="margin-right: 10px;">
                        <i class="fas fa-print"></i> Print Application Form
                    </button>
                    <button class="btn" onclick="location.href='index.php'" style="background: #6c757d; color: white;">
                        <i class="fas fa-home"></i> Back to Home
                    </button>
                </div>

                <p style="font-size: 0.9rem; color: #666; margin-top: 20px;">
                    A confirmation email has been sent to your registered email address.
                </p>
            </div>

            <!-- Printable Application Form -->
            <div class="print-application" id="printableForm">
                <div class="application-header"
                    style="display: flex; align-items: center; border-bottom: 3px double #333; padding-bottom: 20px; margin-bottom: 30px;">
                    <div style="width: 100px; margin-right: 20px;">
                        <img src="images/logo.png" alt="Logo" style="width: 100%;">
                    </div>
                    <div style="flex: 1; text-align: center;">
                        <h1
                            style="color: var(--primary-blue); margin-bottom: 5px; font-size: 24px; text-transform: uppercase;">
                            Indira College of Pharmacy</h1>
                        <h2 style="color: #666; font-size: 14px; margin-bottom: 5px; font-weight: normal;">Vishnupuri Nanded 431606</h2>
                        <p style="margin: 5px 0; font-size: 12px;">Affiliated to Swami Ramanand Teerth Marathwada University Nanded |
                            Approved by PCI & AICTE | NAAC Accredited</p>
                        <div
                            style="margin-top: 15px; border: 1px solid #333; display: inline-block; padding: 5px 15px; font-weight: bold; background: #eee;">
                            ADMISSION APPLICATION FORM 2026-27
                        </div>
                    </div>
                    <div style="width: 100px;">
                        <img src="images/sanstha-logo.png" alt="Sanstha" style="width: 100%;">
                    </div>
                </div>

                <div class="application-details">
                    <!-- Photo & Signature Box -->
                    <div class="photo-signature-box">
                        <div class="photo-box" id="photoPreview">
                            <span style="color: #999; font-size: 0.8rem;">Photo</span>
                        </div>
                        <div class="signature-box" id="signaturePreview">
                            <span style="color: #999; font-size: 0.8rem;">Signature</span>
                        </div>
                    </div>

                    <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
                        <tr style="background: #f0f0f0;">
                            <td style="padding: 10px; font-weight: 600; border: 1px solid #ddd;">Application No:</td>
                            <td style="padding: 10px; border: 1px solid #ddd;" id="print-appNo"></td>
                            <td style="padding: 10px; font-weight: 600; border: 1px solid #ddd;">Date:</td>
                            <td style="padding: 10px; border: 1px solid #ddd;" id="print-date"></td>
                        </tr>
                    </table>

                    <h3 style="background: var(--primary-blue); color: white; padding: 8px; margin: 20px 0 10px;">
                        1. PERSONAL DETAILS</h3>
                    <table style="width: 100%; border-collapse: collapse;">
                        <tr>
                            <td style="padding: 8px; font-weight: 600; width: 25%; border: 1px solid #ddd;">Course:</td>
                            <td style="padding: 8px; width: 75%; border: 1px solid #ddd;" colspan="3" id="print-course">
                            </td>
                        </tr>
                        <tr>
                            <td style="padding: 8px; font-weight: 600; border: 1px solid #ddd;">Full Name:</td>
                            <td style="padding: 8px; border: 1px solid #ddd;" colspan="3" id="print-name"></td>
                        </tr>
                        <tr>
                            <td style="padding: 8px; font-weight: 600; border: 1px solid #ddd;">Date of Birth:</td>
                            <td style="padding: 8px; border: 1px solid #ddd;" id="print-dob"></td>
                            <td style="padding: 8px; font-weight: 600; border: 1px solid #ddd;">Gender:</td>
                            <td style="padding: 8px; border: 1px solid #ddd;" id="print-gender"></td>
                        </tr>
                        <tr>
                            <td style="padding: 8px; font-weight: 600; border: 1px solid #ddd;">Nationality:</td>
                            <td style="padding: 8px; border: 1px solid #ddd;" id="print-nationality"></td>
                            <td style="padding: 8px; font-weight: 600; border: 1px solid #ddd;">Religion:</td>
                            <td style="padding: 8px; border: 1px solid #ddd;" id="print-religion"></td>
                        </tr>
                        <tr>
                            <td style="padding: 8px; font-weight: 600; border: 1px solid #ddd;">Category:</td>
                            <td style="padding: 8px; border: 1px solid #ddd;" id="print-category"></td>
                            <td style="padding: 8px; font-weight: 600; border: 1px solid #ddd;">Blood Group:</td>
                            <td style="padding: 8px; border: 1px solid #ddd;" id="print-blood"></td>
                        </tr>
                        <tr>
                            <td style="padding: 8px; font-weight: 600; border: 1px solid #ddd;">Aadhaar No:</td>
                            <td style="padding: 8px; border: 1px solid #ddd;" id="print-aadhaar"></td>
                            <td style="padding: 8px; font-weight: 600; border: 1px solid #ddd;">ABC ID:</td>
                            <td style="padding: 8px; border: 1px solid #ddd;" id="print-abc"></td>
                        </tr>
                        <tr>
                            <td style="padding: 8px; font-weight: 600; border: 1px solid #ddd;">Mobile:</td>
                            <td style="padding: 8px; border: 1px solid #ddd;" id="print-mobile"></td>
                            <td style="padding: 8px; font-weight: 600; border: 1px solid #ddd;">Email:</td>
                            <td style="padding: 8px; border: 1px solid #ddd;" id="print-email"></td>
                        </tr>
                        <tr>
                            <td style="padding: 8px; font-weight: 600; border: 1px solid #ddd; vertical-align: top;">
                                Address:</td>
                            <td style="padding: 8px; border: 1px solid #ddd;" colspan="3" id="print-address"></td>
                        </tr>
                        <tr>
                            <td style="padding: 8px; font-weight: 600; border: 1px solid #ddd;">City/District:</td>
                            <td style="padding: 8px; border: 1px solid #ddd;" id="print-city"></td>
                            <td style="padding: 8px; font-weight: 600; border: 1px solid #ddd;">State/PIN:</td>
                            <td style="padding: 8px; border: 1px solid #ddd;" id="print-state"></td>
                        </tr>
                    </table>

                    <h3 style="background: var(--primary-blue); color: white; padding: 8px; margin: 20px 0 10px;">
                        2. PARENT/GUARDIAN DETAILS</h3>
                    <table style="width: 100%; border-collapse: collapse;">
                        <tr>
                            <td style="padding: 8px; font-weight: 600; width: 25%; border: 1px solid #ddd;">Father's
                                Name:
                            </td>
                            <td style="padding: 8px; border: 1px solid #ddd;" colspan="3" id="print-father"></td>
                        </tr>
                        <tr>
                            <td style="padding: 8px; font-weight: 600; border: 1px solid #ddd;">Occupation:</td>
                            <td style="padding: 8px; border: 1px solid #ddd;" id="print-fatherOcc"></td>
                            <td style="padding: 8px; font-weight: 600; border: 1px solid #ddd;">Income:</td>
                            <td style="padding: 8px; border: 1px solid #ddd;" id="print-fatherInc"></td>
                        </tr>
                        <tr>
                            <td style="padding: 8px; font-weight: 600; border: 1px solid #ddd;">Father's Mobile:</td>
                            <td style="padding: 8px; border: 1px solid #ddd;" id="print-fatherMob"></td>
                            <td style="padding: 8px; font-weight: 600; border: 1px solid #ddd;">Father's Email:</td>
                            <td style="padding: 8px; border: 1px solid #ddd;" id="print-fatherEmail"></td>
                        </tr>
                        <tr>
                            <td style="padding: 8px; font-weight: 600; border: 1px solid #ddd;">Mother's Name:</td>
                            <td style="padding: 8px; border: 1px solid #ddd;" colspan="3" id="print-mother"></td>
                        </tr>
                        <tr>
                            <td style="padding: 8px; font-weight: 600; border: 1px solid #ddd;">Occupation:</td>
                            <td style="padding: 8px; border: 1px solid #ddd;" id="print-motherOcc"></td>
                            <td style="padding: 8px; font-weight: 600; border: 1px solid #ddd;">Mobile:</td>
                            <td style="padding: 8px; border: 1px solid #ddd;" id="print-motherMob"></td>
                        </tr>
                    </table>

                    <div class="page-break"></div>

                    <h3 style="background: var(--primary-blue); color: white; padding: 8px; margin: 20px 0 10px;">
                        3. ACADEMIC QUALIFICATIONS</h3>
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead style="background: #f0f0f0;">
                            <tr>
                                <th style="padding: 8px; border: 1px solid #ddd;">Examination</th>
                                <th style="padding: 8px; border: 1px solid #ddd;">Board/University</th>
                                <th style="padding: 8px; border: 1px solid #ddd;">Year</th>
                                <th style="padding: 8px; border: 1px solid #ddd;">Percentage</th>
                                <th style="padding: 8px; border: 1px solid #ddd;">Seat No.</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td style="padding: 8px; border: 1px solid #ddd;">SSC (10th)</td>
                                <td style="padding: 8px; border: 1px solid #ddd;" id="print-sscBoard"></td>
                                <td style="padding: 8px; border: 1px solid #ddd;" id="print-sscYear"></td>
                                <td style="padding: 8px; border: 1px solid #ddd;" id="print-sscPer"></td>
                                <td style="padding: 8px; border: 1px solid #ddd;" id="print-sscSeat"></td>
                            </tr>
                            <tr>
                                <td style="padding: 8px; border: 1px solid #ddd;">HSC/Diploma</td>
                                <td style="padding: 8px; border: 1px solid #ddd;" id="print-hscStream"></td>
                                <td style="padding: 8px; border: 1px solid #ddd;" id="print-hscYear"></td>
                                <td style="padding: 8px; border: 1px solid #ddd;" id="print-hscPer"></td>
                                <td style="padding: 8px; border: 1px solid #ddd;">-</td>
                            </tr>
                        </tbody>
                    </table>

                    <h3 style="background: var(--primary-blue); color: white; padding: 8px; margin: 20px 0 10px;">
                        4. ENTRANCE EXAM DETAILS</h3>
                    <table style="width: 100%; border-collapse: collapse;">
                        <tr>
                            <td style="padding: 8px; font-weight: 600; width: 25%; border: 1px solid #ddd;">Exam Name:
                            </td>
                            <td style="padding: 8px; border: 1px solid #ddd;" id="print-examName"></td>
                            <td style="padding: 8px; font-weight: 600; border: 1px solid #ddd;">Score:</td>
                            <td style="padding: 8px; border: 1px solid #ddd;" id="print-examScore"></td>
                        </tr>
                        <tr>
                            <td style="padding: 8px; font-weight: 600; border: 1px solid #ddd;">Roll Number:</td>
                            <td style="padding: 8px; border: 1px solid #ddd;" id="print-examRoll"></td>
                            <td style="padding: 8px; font-weight: 600; border: 1px solid #ddd;">Rank:</td>
                            <td style="padding: 8px; border: 1px solid #ddd;" id="print-examRank"></td>
                        </tr>
                    </table>

                    <div class="declaration-box" style="margin-top: 30px;">
                        <h4 style="margin-bottom: 10px;">DECLARATION</h4>
                        <p style="font-size: 0.95rem; line-height: 1.6;">
                            I hereby declare that the information provided above is true and correct to the best of my
                            knowledge and belief. I understand that any false information or suppression of facts may
                            lead
                            to cancellation of my admission and I shall be liable for legal action. I agree to abide by
                            the
                            rules and regulations of the college.
                        </p>
                    </div>

                    <div class="signature-section">
                        <div style="text-align: center;">
                            <div
                                style="border-top: 2px solid #333; width: 200px; margin: 50px auto 10px; padding-top: 5px;">
                                Student's Signature
                            </div>
                            <p style="font-size: 0.9rem; color: #666;">Date: <span id="print-signDate"></span></p>
                        </div>
                        <div style="text-align: center;">
                            <div
                                style="border-top: 2px solid #333; width: 200px; margin: 50px auto 10px; padding-top: 5px;">
                                Parent's/Guardian's Signature
                            </div>
                            <p style="font-size: 0.9rem; color: #666;">Date: <span id="print-parentSignDate"></span></p>
                        </div>
                    </div>

                    <div style="margin-top: 40px; padding: 20px; border: 2px solid #333;">
                        <h4 style="margin-bottom: 15px;">FOR OFFICE USE ONLY</h4>
                        <table style="width: 100%;">
                            <tr>
                                <td style="padding: 10px;">Documents Verified:
                                    _______________________</td>
                                <td style="padding: 10px;">Fee Paid:
                                    _______________________</td>
                            </tr>
                            <tr>
                                <td style="padding: 10px;">Admission Granted: Yes / No</td>
                                <td style="padding: 10px;">Date:
                                    _______________________</td>
                            </tr>
                            <tr>
                                <td colspan="2" style="padding: 10px;">
                                    <br><br>
                                    Authorized Signature & Stamp
                                </td>
                            </tr>
                        </table>
                    </div>

                    <div style="text-align: center; margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd;">
                        <p style="font-size: 0.85rem; color: #666;">
                            Indira College of Pharmacy | New Sanghvi, Pune - 411027 | Phone: +91-20-XXXX-XXXX |
                            Email: admissions@icop.edu.in
                        </p>
                        <p style="font-size: 0.8rem; color: #999; margin-top: 5px;">
                            This is a computer-generated application form. Please bring original documents for
                            verification
                            at the time of admission.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </main>

<?php include 'includes/footer.php'; ?>

    <script>
        // Generate application number on page load
        window.onload = function () {
            document.getElementById('applicationNo').value = "Generated upon submission";
        };

        let currentStep = 1;
        let formData = {};

        function nextStep(step) {
            // Validate current step
            const currentStepEl = document.getElementById(`step${step}`);
            const inputs = currentStepEl.querySelectorAll('input[required], select[required], textarea[required]');
            let isValid = true;

            inputs.forEach(input => {
                if (!input.value) {
                    input.style.borderColor = 'red';
                    isValid = false;
                } else {
                    input.style.borderColor = '#e0e0e0';
                }
            });

            if (!isValid) {
                alert('Please fill all required fields marked with *');
                return;
            }

            // Save current step data
            saveStepData(step);

            // Move to next step
            document.getElementById(`step${step}`).classList.remove('active');
            document.getElementById(`step${step + 1}`).classList.add('active');

            // Update indicators
            document.getElementById(`step${step}-indicator`).classList.remove('active');
            document.getElementById(`step${step}-indicator`).classList.add('completed');
            document.getElementById(`step${step + 1}-indicator`).classList.add('active');

            currentStep = step + 1;
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        function prevStep(step) {
            document.getElementById(`step${step}`).classList.remove('active');
            document.getElementById(`step${step - 1}`).classList.add('active');

            // Update indicators
            document.getElementById(`step${step}-indicator`).classList.remove('active');
            document.getElementById(`step${step - 1}-indicator`).classList.remove('completed');
            document.getElementById(`step${step - 1}-indicator`).classList.add('active');

            currentStep = step - 1;
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        function saveStepData(step) {
            const stepEl = document.getElementById(`step${step}`);
            const inputs = stepEl.querySelectorAll('input, select, textarea');

            inputs.forEach(input => {
                if (input.type === 'file') {
                    if (input.files.length > 0) {
                        formData[input.id] = input.files[0];
                    }
                } else {
                    formData[input.id] = input.value;
                }
            });
        }

        function submitApplication() {
            // Validate step 3
            const step3 = document.getElementById('step3');
            const requiredInputs = step3.querySelectorAll('input[required]');
            let isValid = true;

            requiredInputs.forEach(input => {
                if (input.type === 'file') {
                    if (input.files.length === 0) {
                        input.style.borderColor = 'red';
                        isValid = false;
                    } else {
                        input.style.borderColor = '#e0e0e0';
                    }
                } else if (!input.value && input.type === 'checkbox' && !input.checked) {
                    alert('Please accept the declaration');
                    isValid = false;
                }
            });

            if (!isValid) {
                alert('Please upload all required documents');
                return;
            }

            // Save step 3 data
            saveStepData(3);

            // Create FormData object
            const data = new FormData();
            for (const key in formData) {
                data.append(key, formData[key]);
            }

            // Show loading state
            const submitBtn = document.querySelector('.btn-submit');
            const originalBtnText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Submitting...';
            submitBtn.disabled = true;

            // Send to backend
            fetch('backend/api/submit_application.php', {
                method: 'POST',
                body: data
            })
                .then(response => response.json())
                .then(result => {
                    if (result.success) {
                        // Hide form, show success banner
                        document.getElementById('applicationForm').style.display = 'none';
                        document.getElementById('successBanner').style.display = 'block';

                        // Update reference ID
                        document.getElementById('referenceId').textContent = result.application_no;

                        // Populate printable form
                        populatePrintForm(result.application_no);

                        window.scrollTo({ top: 0, behavior: 'smooth' });
                    } else {
                        alert('Error: ' + result.message);
                        submitBtn.innerHTML = originalBtnText;
                        submitBtn.disabled = false;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while submitting the application. Please try again.');
                    submitBtn.innerHTML = originalBtnText;
                    submitBtn.disabled = false;
                });
        }

        function populatePrintForm(refId) {
            // Application Details
            document.getElementById('print-appNo').textContent = refId;
            document.getElementById('print-date').textContent = new Date().toLocaleDateString();
            document.getElementById('print-course').textContent = formData.course || '';
            document.getElementById('print-name').textContent = formData.fullName || '';
            document.getElementById('print-dob').textContent = formData.dob || '';
            document.getElementById('print-gender').textContent = formData.gender || '';
            document.getElementById('print-nationality').textContent = formData.nationality || '';
            document.getElementById('print-religion').textContent = formData.religion || '';
            document.getElementById('print-category').textContent = formData.category || '';
            document.getElementById('print-blood').textContent = formData.bloodGroup || '';
            document.getElementById('print-aadhaar').textContent = formData.aadhaar || '';
            document.getElementById('print-abc').textContent = formData.abcId || '';
            document.getElementById('print-mobile').textContent = formData.mobile || '';
            document.getElementById('print-email').textContent = formData.email || '';
            document.getElementById('print-address').textContent = formData.address || '';
            document.getElementById('print-city').textContent = (formData.city || '') + ' / ' + (formData.district || '');
            document.getElementById('print-state').textContent = (formData.state || '') + ' - ' + (formData.pincode || '');

            // Parent Details
            document.getElementById('print-father').textContent = formData.fatherName || '';
            document.getElementById('print-fatherOcc').textContent = formData.fatherOccupation || '';
            document.getElementById('print-fatherInc').textContent = formData.fatherIncome || '';
            document.getElementById('print-fatherMob').textContent = formData.fatherMobile || '';
            document.getElementById('print-fatherEmail').textContent = formData.fatherEmail || '';
            document.getElementById('print-mother').textContent = formData.motherName || '';
            document.getElementById('print-motherOcc').textContent = formData.motherOccupation || '';
            document.getElementById('print-motherMob').textContent = formData.motherMobile || '';

            // Academic Details
            document.getElementById('print-sscBoard').textContent = formData.sscBoard || '';
            document.getElementById('print-sscYear').textContent = formData.sscYear || '';
            document.getElementById('print-sscPer').textContent = formData.sscPercentage ? formData.sscPercentage + '%' : '';
            document.getElementById('print-sscSeat').textContent = formData.sscSeatNo || '';
            document.getElementById('print-hscStream').textContent = formData.hscStream || '';
            document.getElementById('print-hscYear').textContent = formData.hscYear || '';
            document.getElementById('print-hscPer').textContent = formData.hscPercentage ? formData.hscPercentage + '%' : '';

            // Entrance Exam
            document.getElementById('print-examName').textContent = formData.entranceExam || 'N/A';
            document.getElementById('print-examScore').textContent = formData.entranceScore || 'N/A';
            document.getElementById('print-examRoll').textContent = formData.entranceRollNo || 'N/A';
            document.getElementById('print-examRank').textContent = formData.entranceRank || 'N/A';

            // Signature Dates
            const today = new Date().toLocaleDateString();
            document.getElementById('print-signDate').textContent = today;
            document.getElementById('print-parentSignDate').textContent = today;

            // Photo and Signature Preview
            if (formData.photo) {
                const photoReader = new FileReader();
                photoReader.onload = function(e) {
                    document.getElementById('photoPreview').innerHTML = `<img src="${e.target.result}" style="width:100%; height:100%; object-fit:contain;">`;
                };
                photoReader.readAsDataURL(formData.photo);
            }
            if (formData.signature) {
                const sigReader = new FileReader();
                sigReader.onload = function(e) {
                    document.getElementById('signaturePreview').innerHTML = `<img src="${e.target.result}" style="width:100%; height:100%; object-fit:contain;">`;
                };
                sigReader.readAsDataURL(formData.signature);
            }
        }

        function printApplication() {
            window.print();
        }

        function downloadPDF() {
            alert('This feature will be available soon.');
            // window.print();
        }
    </script>
