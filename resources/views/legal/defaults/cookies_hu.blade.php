<p>A süti (cookie) egy kis szöveges fájl, amelyet a weboldal a böngészőjében tárol. A Filadelfia Noom weboldala <strong>csak a működéshez feltétlenül szükséges sütiket</strong> használja. Statisztikai, reklám- vagy közösségimédia-sütit nem használunk, ezért ezekhez nem kérünk hozzájárulást. A szükséges sütik nem kapcsolhatók ki, mert nélkülük az oldal (például a nyelvválasztás, az űrlapok és a belépés) nem működne.</p>

<h2>Az általunk használt sütik</h2>
<table>
  <thead><tr><th>Név</th><th>Cél</th><th>Érvényesség</th></tr></thead>
  <tbody>
    <tr><td>{{ $sessionCookie }}</td><td>Munkamenet: megjegyzi a választott nyelvet, a belépést és az űrlapok állapotát.</td><td>{{ $sessionMinutes }} perc tétlenség után lejár</td></tr>
    <tr><td>XSRF-TOKEN</td><td>Biztonsági token, amely megakadályozza, hogy más oldal az Ön nevében küldjön be űrlapot.</td><td>{{ $sessionMinutes }} perc</td></tr>
    <tr><td>cookie_consent</td><td>Megjegyzi, hogy a sütitájékoztatót már látta, hogy ne jelenjen meg újra.</td><td>12 hónap</td></tr>
    <tr><td>admin_trusted</td><td>Csak adminisztrátoroknak: ha a belépéskor kérik, megjegyzi a megbízható eszközt, hogy ne kelljen újra biztonsági kódot megadni.</td><td>{{ $trustDays }} nap</td></tr>
  </tbody>
</table>

<h2>Külső szolgáltatások</h2>
<ul>
  <li><strong>Cloudflare Turnstile</strong> (csak az űrlapokon): a kéretlen, automatizált beküldések kiszűrésére. A Cloudflare a saját szolgáltatásának működéséhez technikai adatokat kezel; részletek a Cloudflare adatvédelmi tájékoztatójában.</li>
</ul>

<h2>A sütik kezelése</h2>
<p>A sütiket a böngésző beállításaiban bármikor törölheti vagy letilthatja; a szükséges sütik letiltása esetén azonban az oldal egyes részei nem fognak működni. A sütitájékoztató ablakot az oldal alján a „Sütibeállítások” linkkel bármikor újra megnyithatja.</p>
<p>A személyes adatok kezeléséről az <a href="privacy">Adatvédelmi tájékoztató</a> ad részletes tájékoztatást.</p>
