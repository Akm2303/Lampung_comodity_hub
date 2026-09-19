<nav class="navbar">
    <div class="nav-container">
        <a href="/" class="nav-logo">
            <span class="logo-icon">🌾</span>
            <span>Lampung Agri Hub</span>
        </a>
        <button class="nav-toggle" onclick="document.querySelector('.nav-menu').classList.toggle('open')">☰</button>
        <ul class="nav-menu">
            <li><a href="/">HOME</a></li>
            <li><a href="/commodities.php">COMMODITIES</a></li>
            <li><a href="/farmers.php">FARMERS</a></li>
            <li><a href="/map.php">AGRICULTURE MAP</a></li>
            <li><a href="/forecast.php">HARVEST FORECAST</a></li>
            <li><a href="/logistics.php">LOGISTICS</a></li>
            <li><a href="/export.php">EXPORT</a></li>
            <li><a href="/dashboard.php">DASHBOARD</a></li>
            <?php if (Auth::check()): ?>
                <li><a href="/logout.php" class="btn-login">LOGOUT</a></li>
            <?php else: ?>
                <li><a href="/login.php" class="btn-login">LOGIN</a></li>
            <?php endif; ?>
        </ul>
    </div>
</nav>