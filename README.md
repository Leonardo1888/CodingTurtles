# CodingTurtles

Gestionale web per una palestra ad accesso contingentato: sale, fasce orarie, posti, clienti, abbonamenti e prenotazioni. Si entra solo su prenotazione e ogni fascia oraria ha un numero limitato di posti, in base alla metratura della sala (lo scenario è quello delle regole anti-COVID).

Progetto del corso di **Programmazione Web** all'Università degli Studi di Bergamo, a.a. 2024/25 (DB12 – Palestra).

<!-- Link al sito su Altervista: aggiungilo qui -->

## Il database

Lo schema logico ci è stato fornito dal corso, noi l'abbiamo creato e popolato:

```
Sala            (codice, nome, tema, mq)
FasciaOraria    (sala, data, ora, durata)
Posto           (sala, data, ora, nProg)
Prenotazione    (nProg, cliente, sala, data, ora, posto)
Cliente         (codice, nome, cognome, cf, dataNas, indirizzo, tel, email)
Abbonamento     (nAbb, cliente, inizio, fine, prezzo)
SubAbbonamento  (prenotazione, abbonamento)
```

Una prenotazione è "semplice" oppure "sub abbonamento": nel secondo caso finisce anche in `SubAbbonamento` con il riferimento all'abbonamento usato. Su `Prenotazione` c'è un indice univoco su `(sala, data, ora, posto)`, così lo stesso posto non può essere prenotato due volte.

### Come l'abbiamo popolato

I dati sono sintetici e vengono generati da uno script Python (`database/popola_db.py`), che scrive tutte le INSERT in `database/popola_db.txt`. Abbiamo cercato di tenerli il più verosimili possibile:

- **Clienti**: 200, con anagrafica casuale. L'email è costruita a partire da nome e cognome, il codice fiscale è simulato.
- **Sale**: tema e sottocategoria assegnati a rotazione, metri quadri presi da una lista di valori realistici.
- **Fasce orarie**: per ogni sala e per ogni giorno coprono l'orario 8:00–20:00 senza buchi, con durate casuali da 1 a 3 ore (l'ultima viene tagliata per non sforare la chiusura).
- **Posti**: `mq / 10` per ogni fascia, per simulare il distanziamento.
- **Abbonamenti**: circa l'80% dei clienti ne ha uno. La durata non è casuale ma dipende dal prezzo (50 € → 30 giorni, 200 € → 365 giorni, ecc.).
- **Prenotazioni**: circa il 70% dei posti risulta prenotato da un cliente a caso. Se quel cliente ha un abbonamento valido nella data della prenotazione, la prenotazione viene registrata anche come `SubAbbonamento`.

## Cosa fa l'applicazione

Ogni tabella principale ha la sua pagina di ricerca, con filtri e ordinamento cliccando sulle intestazioni delle colonne: `sale.php`, `fasce_orarie.php`, `prenotazioni.php`, `clienti.php`, `abbonamenti.php`.

Dove i dati sono collegati tra loro ci sono i link: per esempio dalla lista delle sale il numero di fasce orarie e il numero di prenotazioni portano direttamente a `fasce_orarie.php` e `prenotazioni.php` già filtrate su quella sala (i parametri passano in GET).

### CRUD sulle sale

La tabella su cui dovevamo implementare il CRUD completo è `Sala`.

- **Create** – si inseriscono nome, tema (da un menu a tendina, i temi sono prefissati) e metri quadri. Il codice è facoltativo: se non lo metti viene assegnato il primo libero partendo da `S001`, e se lo metti non può essere già usato da un'altra sala.
- **Read** – codice, nome, tema, mq, numero di fasce orarie e numero di prenotazioni per ogni sala.
- **Update** – nome, tema e mq sono modificabili. Il codice no, perché è la chiave.
- **Delete** – cancellando una sala vengono eliminate a cascata anche le sue fasce orarie, i posti di quelle fasce e le prenotazioni collegate.

Tutte le operazioni passano da un unico endpoint, `ajax/ajax_crud.php`, che capisce cosa fare dal parametro `action` (`read`, `create`, `get_single`, `update`, `delete`). La pagina carica i dati via AJAX al `document ready` e ogni volta che si inviano i filtri; inserimento e modifica usano una finestra modale.

## Stack

HTML, CSS, JavaScript, jQuery, AJAX e PHP, con database MySQL. Erano le tecnologie ammesse dal corso, quindi niente framework.

## Struttura del progetto

```
CodingTurtles/
├── index.php            homepage
├── sale.php             ricerca + CRUD sale
├── fasce_orarie.php
├── prenotazioni.php
├── clienti.php
├── abbonamenti.php
├── ajax/                endpoint AJAX (ajax_crud.php)
├── includes/            parti comuni (header, nav, footer, connessione al db)
├── database/            script di popolamento e query generate
├── js/
├── css/
└── images/
```

## Lavorarci in locale

Il sito gira su Altervista. Per modificarlo comodamente da VS Code:

1. Clonate il repo e apritelo in VS Code.
2. Installate l'estensione **SFTP** di Natizyskunk.
3. La cartella `.vscode` contiene già il `sftp.json` configurato.
4. Da quel momento ogni salvataggio viene caricato in automatico su Altervista.

Conviene modificare i file dall'Explorer normale di VS Code, così ogni salvataggio aggiorna anche il sito, e ogni tanto fare commit e push dalla sezione Source Control.

## Seconda versione: Django + Bootstrap

Per il secondo progetto del corso abbiamo riscritto la stessa applicazione con Django e Bootstrap, mantenendo database e funzionalità. Il progetto è diviso in app (`homepage`, `clienti`, `abbonamenti`, `sale`, con `prenotazioni` e `fasce_orarie` come sottopagine di `sale`), il CRUD delle sale è gestito in `sale/views.py` e il popolamento è diventato un management command.

Per avviarla servono Python 3.10+ e Django 5.2: basta lanciare `Avvia_CodingTurtles.bat`, oppure

```
cd CodingTurtles
python manage.py runserver 8000
```

e aprire `http://localhost:8000`.

## Autori

- Leonardo Rota
- Pietro Bonomelli
