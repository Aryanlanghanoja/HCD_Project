<!-- header.php -->
<style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
    header {
        background-color: white;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        position: sticky;
        top: 0;
        z-index: 100;
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: 'Poppins', sans-serif !important;
    }

    .nav-container {
        max-width: 1200px;
        margin: 0 auto;
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 15px 20px;
    }

    .logo {
        font-size: 24px;
        font-weight: 700;
        color: #02959F;;
        text-decoration: none;
        display: flex;
        align-items: flex-start;
    }

    .logo-icon {
        margin-right: 10px;
        font-size: 28px;
    }

    .nav-links {
        display: flex;
        list-style: none;
    }

    .nav-links li {
        margin-left: 30px;
    }

    .nav-links a {
        text-decoration: none;
        color: #2B2D42;
        font-weight: 500;
        transition: color 0.3s ease;
        letter-spacing: 0.2px;
    }

    .nav-links a:hover,
    .nav-links a.active {
        color: #02959F;;
        font-weight: 600;
    }

    .user-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background-color: #02959F;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 700;
        margin-left: 15px;
        cursor: pointer;
    }
</style>

<header>
    <div class="nav-container">
        <a href="#" class="logo">
            <span class="logo-icon"><img src="../../assets/images/Logo_Black_Text-removebg_resized.png" alt="" srcset=""></span>
            <!-- Code<span>Arena</span> -->
        </a>
        <ul class="nav-links">
            <li><a href="#" class="active">Problems</a></li>
            <li><a href="#">Exam</a></li>
            <li><a href="#">Discuss</a></li>
            <li><a href="#">Leaderboard</a></li>
            <li><a href="#">Preparation Resources</a></li>
        </ul>
        <div class="user-actions">
            <div class="user-avatar">JS</div>
        </div>
    </div>
</header>
