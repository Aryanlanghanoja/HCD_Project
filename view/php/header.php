<!-- header.php -->
<style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
    :root {
        --primary: #02959F;
        --secondary: #3f37c9;
        --success: #4cc9f0;
        --danger: #f72585;
        --warning: #f8961e;
        --info: #56cfe1;
        --dark: #2b2d42;
        --light: #f8f9fa;
        --gray: #6c757d;
        --gray-dark: #343a40;
        --gray-light: #e9ecef;
        --code-font: 'Consolas', 'Monaco', 'Courier New', monospace;
    }

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
        color: var(--primary);
        text-decoration: none;
        display: flex;
        align-items: flex-start;
    }

    /* .logo span {
        color: var(--dark);
    } */

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
        color: var(--dark);
        font-weight: 500;
        transition: color 0.3s ease;
        letter-spacing: 0.2px;
    }

    .nav-links a:hover,
    .nav-links a.active {
        color: var(--primary);
        font-weight: 600;
    }

    .user-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background-color: var(--primary);
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
