<style>
    .footer {
        background: #1e1e2f;
        color: #ddd;
        padding: 40px 30px;
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        align-items: flex-start;
        border-top: 2px solid #ffcc00;
    }

    .footer-column {
        flex: 1 1 220px;
        margin: 10px 20px;
    }

    .footer h3 {
        color: #ffcc00;
        margin-bottom: 15px;
        font-size: 18px;
        border-bottom: 1px solid #ffcc00;
        padding-bottom: 5px;
    }

    .footer a {
        color: #ccc;
        display: block;
        margin: 6px 0;
        text-decoration: none;
        transition: 0.3s;
    }

    .footer a:hover {
        color: #fff;
        text-decoration: underline;
    }

    .footer .social-icons a {
        display: inline-block;
        margin-right: 12px;
        color: #ffcc00;
        font-size: 18px;
    }

    .footer-bottom {
        background: #141422;
        text-align: center;
        padding: 15px;
        color: #888;
        font-size: 14px;
    }

    @media screen and (max-width: 768px) {
        .footer {
            flex-direction: column;
            text-align: left;
        }
        .footer-column {
            margin: 15px 0;
        }
    }
</style>

<footer>
    <div class="footer">
        <div class="footer-column">
            <h3>CodeAlpha</h3>
            <p>India's emerging tech hub for OTT streaming & digital commerce. Delivering high-performance content & smooth user experience.</p>
        </div>
        <div class="footer-column">
            <h3>Quick Links</h3>
            <a href="index.php">Home</a>
            <a href="cart.php">Cart</a>
            <a href="myorder.php">My Orders</a>
            <a href="auth.php">Login / Register</a>
        </div>
        <div class="footer-column">
            <h3>Contact</h3>
            <a href="mailto:support@codealpha.com">support@codealpha.com</a>
            <a href="#">📍 Mumbai, India</a>
            <div class="social-icons">
                <a href="#"><i class="fab fa-facebook-f"></i></a>
                <a href="#"><i class="fab fa-twitter"></i></a>
                <a href="#"><i class="fab fa-instagram"></i></a>
            </div>
        </div>
    </div>
    <div class="footer-bottom">
        &copy; <?= date("Y") ?> CodeAlpha. All Rights Reserved.
    </div>
</footer>

<!-- Add FontAwesome (if not already) -->
<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
