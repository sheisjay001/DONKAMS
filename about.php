<?php
session_start();
include 'includes/header.php';
?>

<div class="container" style="margin-top: 2rem; margin-bottom: 2rem;">
    <h1 style="text-align: center; margin-bottom: 2rem; color: var(--primary-color);">About Us</h1>

    <div class="about-content" style="max-width: 800px; margin: 0 auto; background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
        <div style="text-align: center; margin-bottom: 2rem;">
            <img src="images/logo_original.jpg" alt="DONKAMS Logo" style="max-width: 200px; border-radius: 50%;">
        </div>

        <h2 style="color: var(--secondary-color); margin-bottom: 1rem;">Who We Are</h2>
        <p style="margin-bottom: 1.5rem; line-height: 1.6;">
            Welcome to <strong>DONKAMS</strong>, your number one source for all things electronics and gaming. We're dedicated to giving you the very best of gadgets, with a focus on dependability, customer service, and uniqueness.
        </p>
        <p style="margin-bottom: 1.5rem; line-height: 1.6;">
            Founded in <?php echo date("Y"); ?>, DONKAMS has come a long way from its beginnings in Minna, Niger State. When we first started out, our passion for "One click, multiple solution" drove us to do intense research so that DONKAMS can offer you the world's most advanced electronics. We now serve customers all over Nigeria and are thrilled to be a part of the quirky, eco-friendly, fair trade wing of the electronics industry.
        </p>

        <h2 style="color: var(--secondary-color); margin-bottom: 1rem; margin-top: 2rem;">Our Mission</h2>
        <p style="margin-bottom: 1.5rem; line-height: 1.6;">
            Our mission is to provide high-quality electronic devices and gaming consoles at affordable prices while ensuring an exceptional shopping experience for our customers. We believe in technology's power to connect and entertain, and we strive to make it accessible to everyone.
        </p>

        <h2 style="color: var(--secondary-color); margin-bottom: 1rem; margin-top: 2rem;">Why Choose Us?</h2>
        <ul style="list-style-type: none; padding-left: 0;">
            <li style="margin-bottom: 0.5rem;"><i class="fas fa-check-circle" style="color: var(--accent-color); margin-right: 10px;"></i> Quality Assurance</li>
            <li style="margin-bottom: 0.5rem;"><i class="fas fa-check-circle" style="color: var(--accent-color); margin-right: 10px;"></i> Competitive Pricing</li>
            <li style="margin-bottom: 0.5rem;"><i class="fas fa-check-circle" style="color: var(--accent-color); margin-right: 10px;"></i> Fast & Secure Delivery</li>
            <li style="margin-bottom: 0.5rem;"><i class="fas fa-check-circle" style="color: var(--accent-color); margin-right: 10px;"></i> 24/7 Customer Support</li>
        </ul>

        <div style="text-align: center; margin-top: 3rem;">
            <p style="margin-bottom: 1rem;">We hope you enjoy our products as much as we enjoy offering them to you. If you have any questions or comments, please don't hesitate to contact us.</p>
            <p><strong>Sincerely,<br>The DONKAMS Team</strong></p>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
