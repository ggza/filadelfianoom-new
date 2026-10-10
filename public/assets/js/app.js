/* Laravel-kiegészítések a site.js mellé */
(function () {
  'use strict';
  // jelentkezés után (siker vagy hiba) a Jelentkezés ablak az űrlap fülön nyílik meg
  var dlg = document.getElementById('dlg-apply');
  if (dlg && dlg.hasAttribute('data-autoopen')) {
    var opener = document.querySelector('[data-open="dlg-apply"]');
    if (opener) {
      opener.click();
      var tab = document.getElementById('tab-form');
      if (tab) tab.click();
    }
  }
  // üzenetküldés után (siker vagy hiba) a kapcsolatfelvételi ablak újra megnyílik
  var dc = document.getElementById('dlg-contact');
  if (dc && dc.hasAttribute('data-autoopen')) {
    var oc = document.querySelector('[data-open="dlg-contact"]');
    if (oc) oc.click();
  }
})();
