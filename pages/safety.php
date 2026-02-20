<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>

  <title>Safety Tips - Raksha</title>

  <!-- META TAGS -->
<meta name="title" content="Raksha - Women Safety & Emergency Protection System">
<meta name="description" content="Raksha is a smart women safety platform for SOS alerts, emergency support, live location sharing, and nearby police assistance. Stay safe, stay empowered.">

<meta name="keywords" content="women safety, SOS alert system, emergency help for women, Raksha safety app, women security platform">

<meta name="author" content="Raksha Team">
<meta name="robots" content="index, follow">

<meta property="og:type" content="website">
<meta property="og:title" content="Raksha - Women Safety & Emergency Protection System">
<meta property="og:description" content="Smart platform for women's safety with instant SOS alerts, live tracking, and police support.">

<meta name="theme-color" content="#e91e63">

  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">

  <link rel="stylesheet" href="../index.css" />
  <link rel="icon" href="../assets/favicon.jpg" type="image/x-icon" />
</head>
<body>

  <!-- Banner -->
  <div class="top-banner">
    <p>🚨 Emergency Helpline: 112 | Women Helpline: 181 | Need Help Urgently?</p>
    <a href="../user/non_reg_sos.php">
    <button>Get Help</button>
    </a>
  </div>

  <!-- Navbar -->
  <header>
    <div class="nav-container">

      <div class="logo">Raksha</div>

      <nav>
        <a href="../index.php">Home</a>
        <a href="safety.php">Safety Tips</a>
        <a href="police.php">Nearby Police</a>
        <a href="../auth/register.php">User</a>
        <a href="../admin/admin_login.php">Admin</a>
      </nav>
      <a href="../auth/register.php">
      <button class="start-btn">Start Protection</button>
      </a>

    </div>
  </header>

  <!-- Safety Tips Section -->
  <section class="hero">

  <div class="hero-left">

    <div class="badge">
      📘 Stay Alert • Stay Safe • Stay Strong
    </div>

    <h1>
      Essential <span>Safety Tips</span><br>
      for Women
    </h1>

    <p>
      Follow these simple safety guidelines to protect yourself
      and stay confident in every situation.
    </p>

    
      <button class="primary-btn" onclick="document.getElementById('tips').scrollIntoView({ behavior: 'smooth' });">Get Started ➞</button>
      
    <a href="police.php">
      <button class="secondary-btn">Nearby Police</button>
      </a>
      
    </div>

    <div class="hero-right">
      <img src="../assets/safety.png" alt="Safety Tips Illustration">
    </div>

  </section>


  <!-- Tips Cards -->
  <section class="tips-container" id="tips">

    <div class="tip-card">
      <h3>📱 Keep Emergency Contacts</h3>
      <p>
        Save important contacts on speed dial and share
        your location with trusted people.
      </p>
    </div>

    <div class="tip-card">
      <h3>🚶 Stay Aware of Surroundings</h3>
      <p>
        Avoid using headphones or phone excessively
        while walking in public places.
      </p>
    </div>

    <div class="tip-card">
      <h3>🌙 Avoid Isolated Areas</h3>
      <p>
        Choose well-lit routes and crowded places,
        especially at night.
      </p>
    </div>

    <div class="tip-card">
      <h3>🔐 Secure Your Online Presence</h3>
      <p>
        Do not share personal information
        on social media publicly.
      </p>
    </div>

    <div class="tip-card">
      <h3>🗣 Learn Self-Defense</h3>
      <p>
        Basic self-defense training helps build
        confidence and quick response.
      </p>
    </div>

    <div class="tip-card">
      <h3>🚨 Trust Your Instincts</h3>
      <p>
        If something feels wrong, leave immediately
        and seek help.
      </p>
    </div>

  </section>

  <!-- Footer -->
   <footer style="text-align:center; padding:15px; color:#666; background:white; margin-top:30px;">
  © <?php echo date("Y"); ?> Raksha - Women Safety System | Designed for Safety • Security • Empowerment for Women | All Rights Reserved.
</footer>

<script>
  const links = document.querySelectorAll("nav a");
  const currentPage = window.location.pathname.split("/").pop();

  links.forEach(link => {
    if (link.getAttribute("href") === currentPage) {
      link.classList.add("active");
    }
  });
</script>

</body>
</html>
