<?php
$pageTitle = "Departments - Indira College of Pharmacy";
include 'includes/header.php';
?>

    <main id="main-content" style="padding: 60px 0;">
        <div class="container">
            <h1 style="color: var(--primary-blue); margin-bottom: 30px;">Our Departments</h1>
            <div class="info-grid"
                style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 30px;">
                <div class="info-card"
                    style="padding: 30px; background: #fff; border-top: 5px solid var(--accent-blue); box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
                    <i class="fas fa-flask"
                        style="font-size: 2.5rem; color: var(--accent-blue); margin-bottom: 20px;"></i>
                    <h3>Pharmaceutics</h3>
                    <p>Focuses on the formulation and manufacturing of dosage forms.</p>
                </div>
                <div class="info-card"
                    style="padding: 30px; background: #fff; border-top: 5px solid var(--accent-blue); box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
                    <i class="fas fa-vial"
                        style="font-size: 2.5rem; color: var(--accent-blue); margin-bottom: 20px;"></i>
                    <h3>Pharmaceutical Chemistry</h3>
                    <p>Deals with drug design, synthesis, and analytical testing.</p>
                </div>
                <div class="info-card"
                    style="padding: 30px; background: #fff; border-top: 5px solid var(--accent-blue); box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
                    <i class="fas fa-leaf"
                        style="font-size: 2.5rem; color: var(--accent-blue); margin-bottom: 20px;"></i>
                    <h3>Pharmacognosy</h3>
                    <p>Study of medicinal drugs derived from plants or other natural sources.</p>
                </div>
                <div class="info-card"
                    style="padding: 30px; background: #fff; border-top: 5px solid var(--accent-blue); box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
                    <i class="fas fa-heartbeat"
                        style="font-size: 2.5rem; color: var(--accent-blue); margin-bottom: 20px;"></i>
                    <h3>Pharmacology</h3>
                    <p>Study of drug action on biological systems.</p>
                </div>
            </div>
        </div>
    </main>

<?php include 'includes/footer.php'; ?>
