$(document).ready(function() {

  // Funzione generica per la ricerca tramite AJAX
  function eseguiRicerca(formId, tabellaId, url) {
      const formData = $(formId).serialize();

      $.ajax({
          url: url,
          type: 'POST',
          data: formData,
          beforeSend: function() {
              $(tabellaId).html('<tr><td colspan="100%" style="text-align: center;">Caricamento...</td></tr>');
          },
          success: function(response) {
              $(tabellaId).html(response);
          },
          error: function() {
              $(tabellaId).html('<tr><td colspan="100%">Si è verificato un errore di connessione.</td></tr>');
          }
      });
  }

  // Gestione della ricerca per le sale
  if ($('#filtro-form').length) {
      $('#filtro-form').on('submit', function(e) {
          e.preventDefault();
          eseguiRicerca('#filtro-form', '#risultati-tabella-sale', 'ajax/ricerca_sale.php');
      });
      // Esegue la ricerca iniziale
      eseguiRicerca('#filtro-form', '#risultati-tabella-sale', 'ajax/ricerca_sale.php');
  }

  // Gestione della ricerca per i clienti
  if ($('#filtro-clienti-form').length) {
      $('#filtro-clienti-form').on('submit', function(e) {
          e.preventDefault();
          eseguiRicerca('#filtro-clienti-form', '#risultati-tabella-clienti', 'ajax/ricerca_clienti.php');
      });
      // Esegue la ricerca iniziale
      eseguiRicerca('#filtro-clienti-form', '#risultati-tabella-clienti', 'ajax/ricerca_clienti.php');
  }
  
  // Gestione della ricerca per gli abbonamenti
  if ($('#filtro-abbonamenti-form').length) {
      $('#filtro-abbonamenti-form').on('submit', function(e) {
          e.preventDefault();
          eseguiRicerca('#filtro-abbonamenti-form', '#risultati-tabella-abbonamenti', 'ajax/ricerca_abbonamenti.php');
      });
      // Esegue la ricerca iniziale
      eseguiRicerca('#filtro-abbonamenti-form', '#risultati-tabella-abbonamenti', 'ajax/ricerca_abbonamenti.php');
  }

  // Gestione della ricerca per le fasce orarie
  if ($('#filtro-fasce_orarie-form').length) {
      $('#filtro-fasce_orarie-form').on('submit', function(e) {
          e.preventDefault();
          eseguiRicerca('#filtro-fasce_orarie-form', '#risultati-tabella-fasce_orarie', 'ajax/ricerca_fasce-orarie.php');
      });
      // Esegue la ricerca iniziale
      eseguiRicerca('#filtro-fasce_orarie-form', '#risultati-tabella-fasce_orarie', 'ajax/ricerca_fasce-orarie.php');
  }

});