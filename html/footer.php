<!-- footer.html -->
<footer class="bg-black py-8 px-6 flex flex-col md:flex-row items-center justify-between">
  <nav class="flex flex-wrap gap-4 md:gap-8 mb-4 md:mb-0">
    <a href="index" class="footer-gradient">hOmE</a>
    <a href="over" class="footer-gradient">over tOdAY</a>
    <a href="diensten" class="footer-gradient">dIENStEN</a>
    <a href="expertise" class="footer-gradient">EXPERtISE</a>
    <a href="artikel" class="footer-gradient">ArtikElEn</a>
    <a href="vacatures" class="footer-gradient">VAcAturES</a>
    <!-- <a href="zoek" class="footer-gradient">zOEkEn</a> -->
    <a href="contact" class="footer-gradient">CoNtACt</a>
  </nav>
  <div class="flex items-center gap-4">
    <span class="text-xs text-gray-400">&copy; 2025 TODAY Advies BV. Alle rechten voorbehouden.</span>
    <a href="https://www.linkedin.com/company/today-advies/" target="_blank" rel="noopener" class="ml-2">
      <img src="img/footer/linkedin.png" alt="LinkedIn" style="width: 28px; height: 28px; object-fit: contain;">
    </a>
  </div>
</footer>
<style>
.footer-gradient {
  background: linear-gradient(90deg,  #FBC8D4, #9795F0);
  -webkit-background-clip: text;
  background-clip: text;
  -webkit-text-fill-color: transparent;
  color: transparent;
  transition: background 0.3s, color 0.3s;
  letter-spacing: 0.05em;
  margin: 0 10px;
  text-decoration: none;
  font-size: 0.95rem;
}
.footer-gradient:hover, .footer-gradient:focus {
  background: none;
  -webkit-background-clip: unset;
  background-clip: unset;
  -webkit-text-fill-color: #FBC8D4;
  color: #FBC8D4;
  background-color: #222;
  border-radius: 4px;
}
</style>