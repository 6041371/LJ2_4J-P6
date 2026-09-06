<header class="bg-white shadow-md py-4 px-6 flex items-center">

    <!-- Logo -->
    <a href="home">
        <img src="img/header/logo.png" alt="Logo" style="width: 120px;" class="mr-8">
    </a>

    <!-- Navigatie -->
    <nav id="main-nav" class="flex items-center gap-8 ml-auto mr-8 text-lg">

        <div class="relative">
            <a href="over" class="nav-gradient dropdown-toggle">over tOdAY</a>
            <div class="dropdown-menu">
                <a href="professionals" class="nav-gradient-item">Professionals</a>
                <a href="community" class="nav-gradient-item">Community</a>
                <a href="contact" class="nav-gradient-item">Contact</a>
            </div>
        </div>

        <a href="kennisbank" class="nav-gradient">KENNisbANk</a>

        <div class="relative">
            <a href="diensten" class="nav-gradient dropdown-toggle">dIENStEN</a>
            <div class="dropdown-menu">
                <a href="onderwijsadvies" class="nav-gradient-item">Onderwijsadvies</a>
                <a href="interim" class="nav-gradient-item">Interim-management</a>
                <a href="coaching" class="nav-gradient-item">Coaching</a>
                <a href="audits" class="nav-gradient-item">Audits</a>
                <a href="professionalisering" class="nav-gradient-item">professionalisering</a>
                <a href="werving-en-selectie" class="nav-gradient-item">Werving en selectie</a>
            </div>
        </div>

        <div class="relative">
            <a href="expertise" class="nav-gradient dropdown-toggle">EXPERtISE</a>
            <div class="dropdown-menu">
                <a href="onderwijskwaliteit" class="nav-gradient-item">Onderwijskwaliteit</a>
                <a href="onderwijsresultaten" class="nav-gradient-item">Onderwijsresultaten</a>
                <a href="basisvaardigheden" class="nav-gradient-item">Basisvaardigheden</a>
                <a href="visieontwikkeling" class="nav-gradient-item">Visieontwikkeling</a>
                <a href="open-leermaterialen" class="nav-gradient-item">Open leermateriaal</a>
                <a href="kwaliteitszorg" class="nav-gradient-item">Kwaliteitszorg</a>
            </div>
        </div>

        <a href="artikel" class="nav-gradient">ArtikElEn</a>
        <a href="vacatures" class="nav-gradient">VAcAturES</a>
        <a href="zoek" class="search-link" aria-label="Zoeken op de website">
            <svg class="search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <circle cx="11" cy="11" r="7"></circle>
                <path d="M20 20l-3.5-3.5"></path>
            </svg>
            <span>zoeken</span>
        </a>

    </nav>

    <a href="/login" class="secret-login" aria-hidden="true">.</a>

    <button id="menu-toggle" class="hamburger ml-4" aria-label="Menu">
        <span></span>
        <span></span>
        <span></span>
    </button>


<script>
const toggle = document.getElementById('menu-toggle');
const nav = document.getElementById('main-nav');

toggle.addEventListener('click', () => {
    nav.style.display = nav.style.display === 'flex' ? 'none' : 'flex';
});
</script>
    
</header>
<style>
    .nav-gradient {
    background: linear-gradient(90deg, #FBC8D4, #9795F0);
        background-clip: text;
    -webkit-background-clip: text;
    background-clip: text;
    -webkit-text-fill-color: transparent;
    color: transparent;
    transition: 0.3s;
    letter-spacing: 0.05em;
    text-decoration: none;
}


.nav-gradient:hover {
    -webkit-text-fill-color: #343a40;
    color: #343a40;
}

/* Layout fix */
#main-nav a {
    display: flex;
    align-items: center;
}

/* Dropdown basis */
.relative {
    position: relative;
}

.dropdown-toggle {
    padding-right: 1.2em;
}

.dropdown-toggle::after {
    content: '▼';
    font-size: 0.7em;
    margin-left: 0.4em;
    transition: 0.2s;
}

/* Dropdown menu */
.dropdown-menu {
    display: none;
    position: absolute;
    top: 100%;
    left: 0;
    background: white;
    border-radius: 6px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    padding: 0.5rem 0;
    min-width: 240px;
    z-index: 1000;
}

/* Show dropdown */
.relative:hover .dropdown-menu {
    display: block;
}

.relative:hover .dropdown-toggle::after {
    transform: rotate(180deg);
}

/* Dropdown items */
.nav-gradient-item {
    display: block;
    padding: 0.6rem 1rem;
    text-decoration: none;
    background: linear-gradient(90deg, #FBC8D4, #9795F0);
    background-clip: text;
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

.nav-gradient-item:hover {
    background: #fbc8d4;
    -webkit-text-fill-color: #343a40;
}

/* Submenu */
.submenu {
    position: relative;
}

.submenu-toggle::after {
    content: '▶';
    font-size: 0.7em;
    margin-left: 0.5em;
}

.submenu-menu {
    display: none;
    position: absolute;
    top: 0;
    left: 100%;
    background: white;
    border-radius: 6px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    padding: 0.5rem 0;
    min-width: 180px;
}

.submenu:hover .submenu-menu {
    display: block;
}

.search-link {
    display: inline-flex !important;
    align-items: center;
    gap: 0.4rem;
    padding: 0.45rem 0.8rem;
    border-radius: 999px;
    border: 1px solid #d7d4ff;
    color: #5f5cd6;
    background: #f8f7ff;
    text-decoration: none;
    transition: 0.2s ease;
}

.search-link:hover {
    background: #ece9ff;
    color: #4f4bbd;
}

.search-icon {
    width: 16px;
    height: 16px;
}

@media (max-width: 960px) {
    .search-link span {
        display: none;
    }

    .search-link {
        padding: 0.45rem;
    }
}
</style>