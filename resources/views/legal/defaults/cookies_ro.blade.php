<p>Un cookie este un mic fișier text pe care site-ul îl stochează în browserul dumneavoastră. Site-ul Filadelfia Noom folosește <strong>doar cookie-urile strict necesare funcționării</strong>. Nu folosim cookie-uri de statistică, de publicitate sau de rețele sociale, de aceea nu solicităm consimțământ pentru acestea. Cookie-urile necesare nu pot fi dezactivate, deoarece fără ele site-ul (de ex. alegerea limbii, formularele și autentificarea) nu ar funcționa.</p>

<h2>Cookie-urile pe care le folosim</h2>
<table>
  <thead><tr><th>Nume</th><th>Scop</th><th>Valabilitate</th></tr></thead>
  <tbody>
    <tr><td>{{ $sessionCookie }}</td><td>Sesiune: reține limba aleasă, autentificarea și starea formularelor.</td><td>expiră după {{ $sessionMinutes }} minute de inactivitate</td></tr>
    <tr><td>XSRF-TOKEN</td><td>Token de securitate care împiedică trimiterea de formulare în numele dumneavoastră de pe alte site-uri.</td><td>{{ $sessionMinutes }} minute</td></tr>
    <tr><td>cookie_consent</td><td>Reține faptul că ați văzut informarea despre cookie-uri, ca aceasta să nu mai apară.</td><td>12 luni</td></tr>
    <tr><td>admin_trusted</td><td>Doar pentru administratori: dacă o solicită la autentificare, reține dispozitivul de încredere, pentru a nu mai fi nevoie de codul de securitate.</td><td>{{ $trustDays }} zile</td></tr>
  </tbody>
</table>

<h2>Servicii externe</h2>
<ul>
  <li><strong>Cloudflare Turnstile</strong> (doar la formulare): pentru filtrarea trimiterilor automate nedorite. Cloudflare prelucrează date tehnice pentru funcționarea propriului serviciu; detalii în politica de confidențialitate Cloudflare.</li>
</ul>

<h2>Gestionarea cookie-urilor</h2>
<p>Puteți șterge sau bloca oricând cookie-urile din setările browserului; în cazul blocării cookie-urilor necesare, însă, unele părți ale site-ului nu vor funcționa. Fereastra de informare despre cookie-uri o puteți redeschide oricând cu linkul „Setări cookie-uri” din partea de jos a paginii.</p>
<p>Informații detaliate despre prelucrarea datelor personale găsiți în <a href="privacy">Politica de confidențialitate</a>.</p>
