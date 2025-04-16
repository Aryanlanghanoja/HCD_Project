<?php
	
	if (session_status() === PHP_SESSION_NONE) {
		session_start();
	}
	
	if (empty($_SESSION["user"])) {
		header("Location: ./login.php");
		exit();
	}
	
	
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
	<link rel="stylesheet" href="../css//faculty_dashboard.css">
	<link rel="shortcut icon" href="../../assets/images/Fevicon.png" type="image/x-icon">
	<title>Faculty Dashboard</title>
</head>	
<body>
	
	<!-- SIDEBAR -->
	<section id="sidebar">
		<a href="#" class="brand">
			<img src="../../assets/images/Logo_Black_Text-removebg_resized.png" alt="Logo">
		</a>
		<ul class="side-menu">
			<li><a href="#" class="active"><i class='bx bxs-dashboard icon'></i> Dashboard</a></li>
			
			<li class="divider" data-text="main">Main</li>

			<li><a href="../html/compiler.html"><i class='bx bx-code-alt icon'></i> Compiler</a></li>

			<li>
				<a href="#"><i class='bx bx-question-mark icon'></i> Questions <i class='bx bx-chevron-right icon-right'></i></a>
				<ul class="side-dropdown">
					<li><a href="./question_upload.php"><i class='bx bx-plus-circle icon'></i> Add</a></li>
					<li><a href="./question_list.php"><i class='bx bx-show icon'></i> View</a></li>
				</ul>
			</li>

			<li>
				<a href="#"><i class='bx bx-edit icon'></i> Exam <i class='bx bx-chevron-right icon-right'></i></a>
				<ul class="side-dropdown">
					<li><a href="#"><i class='bx bx-pencil icon'></i> Create</a></li>
					<li><a href="#"><i class='bx bx-show icon'></i> View</a></li>
				</ul>
			</li>

			<li><a href="#"><i class='bx bx-list-ul icon'></i> Student List</a></li>

			<?php if ($_SESSION["user"] === "603"): ?>
	<li><a href="./add_admin.php"><i class='bx bx-user icon'></i> Add Faculty</a></li>
<?php endif; ?>


			<li><a href="#"><i class='bx bx-cog icon'></i> Settings</a></li>

			<li><a href="#"><i class='bx bx-log-out icon'></i> Logout</a></li>
		</ul>
	</section>
<!-- SIDEBAR -->

	<!-- SIDEBAR -->

	<!-- NAVBAR -->
	<section id="content">
		<!-- NAVBAR -->
		<nav>
			<i class='bx bx-menu toggle-sidebar'></i>
			<form action="#">
				<div class="form-group">
					<input type="text" placeholder="Search...">
					<i class='bx bx-search icon'></i>
				</div>
			</form>
			<!-- <a href="#" class="nav-link">
				<i class='bx bxs-bell icon'></i>
				<span class="badge">5</span>
			</a>
			<a href="#" class="nav-link">
				<i class='bx bxs-message-square-dots icon'></i>
				<span class="badge">8</span>
			</a> -->
			<span class="divider"></span>
			<div class="profile" id="profile">
				<img src="../../assets/images/Admin.jpg" alt="">
				<ul class="profile-link">
					<li><a href="#"><i class='bx bxs-user-circle icon'></i> Profile</a></li>
					<li><a href="#"><i class='bx bxs-cog'></i> Settings</a></li>
					<li><a href="./logout.php"><i class='bx bxs-log-out-circle'></i> Logout</a></li>
				</ul>
			</div>
		</nav>
		<!-- NAVBAR -->

		<!-- MAIN -->
		<main>
			<h1 class="title">Faculty Dashboard</h1>
			<ul class="breadcrumbs">
				<li><a href="#">Home</a></li>
				<li class="divider">/</li>
				<li><a href="#" class="active">Dashboard</a></li>
			</ul>
			<div class="info-data">
				<div class="card">
					<div class="head">
						<div>
							<h2>1500</h2>
							<p>Submissions</p>
						</div>
						<i class='bx bx-trending-up icon' ></i>
					</div>
					<span class="progress" data-value="40%"></span>
					<span class="label">40%</span>
				</div>
				<div class="card">
					<div class="head">
						<div>
							<h2>234</h2>
							<p>Participants</p>
						</div>
						<i class='bx bx-trending-up icon' ></i>
					</div>
					<span class="progress" data-value="60%"></span>
					<span class="label">60%</span>
				</div>
				<!-- <div class="card">
					<div class="head">
						<div>
							<h2>465</h2>
							<p>Problems</p>
						</div>
						<i class='bx bx-trending-up icon' ></i>
					</div>
					<span class="progress" data-value="30%"></span>
					<span class="label">30%</span>
				</div> -->
				<div class="card">
					<div class="head">
						<div>
							<h2>235</h2>
							<p>Submissions</p>
						</div>
						<i class='bx bx-trending-up icon' ></i>
					</div>
					<span class="progress" data-value="80%"></span>
					<span class="label">80%</span>
				</div>
			</div>
			<div class="data">
				<div class="content-data">
					<div class="head">
						<h3>Sales Report</h3>
						<div class="menu">
							<i class='bx bx-dots-horizontal-rounded icon'></i>
							<ul class="menu-link">
								<li><a href="#">Edit</a></li>
								<li><a href="#">Save</a></li>
								<li><a href="#">Remove</a></li>
							</ul>
						</div>
					</div>
					<div class="chart">
						<div id="chart"></div>
					</div>
				</div>
				<!-- <div class="content-data">
					<div class="head">
						<h3>Chatbox</h3>
						<div class="menu">
							<i class='bx bx-dots-horizontal-rounded icon'></i>
							<ul class="menu-link">
								<li><a href="#">Edit</a></li>
								<li><a href="#">Save</a></li>
								<li><a href="#">Remove</a></li>
							</ul>
						</div>
					</div>
					<div class="chat-box">
						<p class="day"><span>Today</span></p>
						<div class="msg">
							<img src="../../assests//images/Admin.jpg" alt="">
							<div class="chat">
								<div class="profile">
									<span class="username">Narendra Modi</span>
									<span class="time">18:30</span>
								</div>
								<p>Hello</p>
							</div>
						</div>
						<div class="msg me">
							<div class="chat">
								<div class="profile">
									<span class="time">18:30</span>
								</div>
								<p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Eaque voluptatum eos quam dolores eligendi exercitationem animi nobis reprehenderit laborum! Nulla.</p>
							</div>
						</div>
						<div class="msg me">
							<div class="chat">
								<div class="profile">
									<span class="time">18:30</span>
								</div>
								<p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Ipsam, architecto!</p>
							</div>
						</div>
						<div class="msg me">
							<div class="chat">
								<div class="profile">
									<span class="time">18:30</span>
								</div>
								<p>Lorem ipsum, dolor sit amet.</p>
							</div>
						</div>
					</div>
					<form action="#">
						<div class="form-group">
							<input type="text" placeholder="Type...">
							<button type="submit" class="btn-send"><i class='bx bxs-send' ></i></button>
						</div>
					</form>
				</div> -->
			</div>
		</main>
		<!-- MAIN -->
	</section>
	<!-- NAVBAR -->

	<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
	<script src="../js/faculty_dashboard.js"></script>
</body>
</html>