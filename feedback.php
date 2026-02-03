<?php
$pageTitle = "Student Feedback | Indira College of Pharmacy";
include 'includes/header.php';
?>

    <!-- Breadcrumb -->
    <div class="breadcrumb">
        <div class="container">
            <nav class="breadcrumb-nav" aria-label="Breadcrumb">
                <a href="index.php"><i class="fas fa-home"></i> Home</a>
                <span class="separator">/</span>
                <span class="current">Feedback</span>
            </nav>
        </div>
    </div>

    <main id="main-content" style="padding: 60px 0; background-color: #f8f9fa;">
        <div class="container">
            <div class="row" style="display: flex; justify-content: center;">
                <div class="col-md-8" style="max-width: 800px; width: 100%;">
                    <div class="card" style="background: white; padding: 40px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
                        <h1 style="color: var(--primary-blue); margin-bottom: 20px; text-align: center;">Feedback & Suggestions</h1>
                        <p style="text-align: center; color: #666; margin-bottom: 30px;">
                            We value your feedback. Your suggestions help us improve the educational experience at Indira College of Pharmacy.
                            You can choose to remain anonymous.
                        </p>

                        <form id="feedbackForm">
                            <div class="form-group" style="margin-bottom: 20px;">
                                <label for="category" style="display: block; font-weight: 600; margin-bottom: 8px;">Category</label>
                                <select id="category" name="category" required style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px;">
                                    <option value="General">General Suggestion</option>
                                    <option value="Academic">Academic / Curriculum</option>
                                    <option value="Infrastructure">Infrastructure / Facilities</option>
                                    <option value="Library">Library</option>
                                    <option value="Canteen">Canteen</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>

                            <div class="form-group" style="margin-bottom: 20px;">
                                <label for="subject" style="display: block; font-weight: 600; margin-bottom: 8px;">Subject</label>
                                <input type="text" id="subject" name="subject" placeholder="Brief subject of your feedback" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px;">
                            </div>

                            <div class="form-group" style="margin-bottom: 20px;">
                                <label for="message" style="display: block; font-weight: 600; margin-bottom: 8px;">Your Feedback / Message *</label>
                                <textarea id="message" name="message" rows="6" required placeholder="Please describe your feedback or suggestion in detail..." style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px; resize: vertical;"></textarea>
                            </div>

                            <div class="form-group" style="margin-bottom: 20px;">
                                <label for="contactInfo" style="display: block; font-weight: 600; margin-bottom: 8px;">Contact Information (Optional)</label>
                                <input type="text" id="contactInfo" name="contactInfo" placeholder="Email or Phone (Leave blank to remain anonymous)" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px;">
                                <small style="color: #888; display: block; margin-top: 5px;">If you leave this blank, your feedback will be submitted anonymously.</small>
                            </div>

                            <div class="form-group" style="text-align: center; margin-top: 30px;">
                                <button type="submit" class="submit-btn" style="background: var(--primary-blue); color: white; padding: 12px 40px; border: none; border-radius: 4px; font-size: 1.1rem; cursor: pointer; transition: background 0.3s;">
                                    Submit Feedback
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>
        document.getElementById('feedbackForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const form = this;
            const submitBtn = form.querySelector('.submit-btn');
            const originalBtnText = submitBtn.innerHTML;
            
            // Disable button and show loading state
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Submitting...';
            
            const formData = new FormData(form);
            
            fetch('backend/api/submit_feedback.php', {
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
