<?php
session_start();
include 'includes/header.php';
?>

<div class="container" style="margin-top: 2rem; margin-bottom: 2rem;">
    <h1 style="text-align: center; margin-bottom: 2rem; color: var(--primary-color);">Contact Us</h1>

    <div class="contact-container" style="display: flex; flex-wrap: wrap; gap: 2rem; max-width: 1000px; margin: 0 auto;">
        
        <!-- Contact Information -->
        <div class="contact-info" style="flex: 1; min-width: 300px; background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
            <h2 style="color: var(--secondary-color); margin-bottom: 1.5rem;">Get In Touch</h2>
            <p style="margin-bottom: 2rem; line-height: 1.6;">Have a question about an order, a product, or just want to say hi? We'd love to hear from you!</p>
            
            <div class="info-item" style="margin-bottom: 1.5rem; display: flex; align-items: flex-start;">
                <i class="fas fa-map-marker-alt" style="color: var(--accent-color); font-size: 1.2rem; margin-right: 15px; margin-top: 5px;"></i>
                <div>
                    <h3 style="font-size: 1.1rem; margin-bottom: 0.5rem;">Address</h3>
                    <p>Gidan Kwano,<br>Minna, Niger State,<br>Nigeria</p>
                </div>
            </div>
            
            <div class="info-item" style="margin-bottom: 1.5rem; display: flex; align-items: flex-start;">
                <i class="fas fa-phone" style="color: var(--accent-color); font-size: 1.2rem; margin-right: 15px; margin-top: 5px;"></i>
                <div>
                    <h3 style="font-size: 1.1rem; margin-bottom: 0.5rem;">Phone</h3>
                    <p>+234 903 151 8307</p>
                </div>
            </div>
            
            <div class="info-item" style="margin-bottom: 1.5rem; display: flex; align-items: flex-start;">
                <i class="fas fa-envelope" style="color: var(--accent-color); font-size: 1.2rem; margin-right: 15px; margin-top: 5px;"></i>
                <div>
                    <h3 style="font-size: 1.1rem; margin-bottom: 0.5rem;">Email</h3>
                    <p>info@donkams.com</p>
                </div>
            </div>

            <div class="social-links" style="margin-top: 2rem;">
                <h3 style="font-size: 1.1rem; margin-bottom: 1rem;">Follow Us</h3>
                <div style="display: flex; gap: 1rem;">
                    <a href="#" style="color: var(--primary-color); font-size: 1.5rem;"><i class="fab fa-facebook"></i></a>
                    <a href="#" style="color: var(--primary-color); font-size: 1.5rem;"><i class="fab fa-twitter"></i></a>
                    <a href="#" style="color: var(--primary-color); font-size: 1.5rem;"><i class="fab fa-instagram"></i></a>
                    <a href="#" style="color: var(--primary-color); font-size: 1.5rem;"><i class="fab fa-linkedin"></i></a>
                </div>
            </div>
        </div>

        <!-- Contact Form -->
        <div class="contact-form" style="flex: 1.5; min-width: 300px; background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
            <h2 style="color: var(--secondary-color); margin-bottom: 1.5rem;">Send a Message</h2>
            <form action="#" method="POST">
                <div class="form-group" style="margin-bottom: 1.5rem;">
                    <label for="name" style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Your Name</label>
                    <input type="text" id="name" name="name" required style="width: 100%; padding: 0.8rem; border: 1px solid #ddd; border-radius: 4px;">
                </div>
                
                <div class="form-group" style="margin-bottom: 1.5rem;">
                    <label for="email" style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Your Email</label>
                    <input type="email" id="email" name="email" required style="width: 100%; padding: 0.8rem; border: 1px solid #ddd; border-radius: 4px;">
                </div>
                
                <div class="form-group" style="margin-bottom: 1.5rem;">
                    <label for="subject" style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Subject</label>
                    <input type="text" id="subject" name="subject" required style="width: 100%; padding: 0.8rem; border: 1px solid #ddd; border-radius: 4px;">
                </div>
                
                <div class="form-group" style="margin-bottom: 1.5rem;">
                    <label for="message" style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Message</label>
                    <textarea id="message" name="message" rows="5" required style="width: 100%; padding: 0.8rem; border: 1px solid #ddd; border-radius: 4px; resize: vertical;"></textarea>
                </div>
                
                <button type="submit" class="btn" style="width: 100%; background: var(--primary-color); color: white; padding: 1rem; border: none; border-radius: 4px; font-weight: 600; cursor: pointer; transition: background 0.3s;">Send Message</button>
            </form>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
