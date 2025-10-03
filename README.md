Come modificare i file su altervista comodamente da Visual Studio Code.

- Git clone.
- La apri in VSC
- Installi l'estensione SFTP su VSC, di Natizyskunk
- Nella cartella CODINGTURTLES ci va una .vsoce con il json già configurato
- a sinistra su VSC hai la sezione SFTP dove puoi modificare i file direttamente e si uplodano direttamente su altervista

Consiglio: 
- Modificare i file dall'Explorer normale di Vscode, ogni volta che salvi si aggiorna pure il sito di altervista
- Dopo che hai fatto un po' di modifiche vai nella sezione git (Source control) e fai il commit e push


DOCUMENTAZIONE PROGETTO: CODING TURTLES
Bonomelli Pietro 1087035 
Rota Leonardo 1086029

Com’è stato popolato il DataBase di CT:
●	Sala: Vengono create delle sale. A ogni sala viene assegnato un tema e una sottocategoria in modo ciclico. I metri quadri (mq) sono scelti casualmente da una lista predefinita.
●	Fascia oraria: Per ogni sala e per ogni giorno da popolare, vengono create fasce orarie che coprono la giornata dalle 8:00 alle 20:00. Le fasce sono contigue e hanno una durata casuale (da 1 a 3 ore), con un controllo che impedisce di superare l'orario di chiusura.
●	Posto: I posti vengono creati a partire dalle fasce orarie generate. Per ogni fascia oraria, viene creato un numero di posti proporzionale ai metri quadri della sala (mq / 10), simulando il distanziamento sociale.
●	Prenotazione e SubAbbonamento/Semplice: Viene selezionato circa il 70% dei posti totali disponibili per essere prenotato.
A ogni posto selezionato viene associato un cliente casuale, creando una Prenotazione.
Se il cliente che ha effettuato la prenotazione possiede un abbonamento valido in quella data (cioè la data della prenotazione è compresa tra l'inizio e la fine dell'abbonamento), allora la prenotazione viene anche registrata nella tabella SubAbbonamento.
●	Abbonamento: Viene creato un abbonamento per l'80% dei clienti totali. La durata dell'abbonamento non è casuale, ma è direttamente proporzionale al prezzo: un prezzo più alto corrisponde a una durata maggiore (es. 50€ per 30 giorni, 200€ per 365 giorni).
●	Cliente: Vengono generati 200 clienti con dati anagrafici casuali (nome, cognome, CF, etc.). L'email viene costruita in modo realistico partendo da nome e cognome e il CF è simulato.






 
CRUD:
●	CREATE:  L’utente può aggiungere una nuova Sala inserendo i campi: 
○	Codice Sala: l’utente non può inserire un codice che appartiene ad un’altra sala. Se non lo inserisce viene inserito automaticamente al primo codice libero disponibile, partendo da S001;
○	Nome: Nome della sala da creare;
○	Tema: Sceglie il tema della sala tramite un dropdown menu. (Ci immaginiamo che i temi sono prestabiliti e aggiunti nel db solo all’occorrenza in quanto sono una cosa che non va modificata frequentemente);
○	Metri Quadrati.
●	READ: Lettura di tutte le tuple della tabella sala. Vengono mostrati:
○	Il codice;
○	Nome;
○	Tema;
○	Metri quadrati;
○	Numero di fasce orarie per ogni sala;
○	Numero di prenotazioni per ogni sala;
○	Azioni disponibili (UPDATE / DELETE).
●	UPDATE: Per l’update i campi richiesti sono:
○	Il codice non è modificabile, in quanto è l’ID della Sala;
○	Nome: modificabile;
○	Tema: modificabile;
○	Metri quadrati: modificabile.
●	DELETE: Quando viene cancellata una sala è necessario cancellare:
○	Tutte le sue fasce orarie;
○	I posti relativi ad ogni fascia oraria;
○	Le prenotazioni associate a quella sala.


 
Panoramica Architetturale della Pagina sale.php
La pagina sale.php gestisce le sale della palestra, combinando HTML/CSS per la struttura, PHP per il rendering iniziale e l'accesso al DB, e JavaScript/jQuery/AJAX per l'interazione dinamica.
Funzionalità Chiave
Tutte le operazioni (lettura, creazione, modifica, eliminazione – CRUD) avvengono tramite un singolo endpoint backend: ajax/ajax_crud.php, utilizzando il parametro action per specificare l'operazione.
1.	Caricamento Dati (AJAX): Al caricamento della pagina (document ready) e al submit del form filtri, una chiamata AJAX POST con action='read' recupera i dati.
2.	Link tra i dati: I conteggi aggregati sono visualizzati come link che permettono la navigazione contestuale e filtrata verso le pagine fasce_orarie.php e prenotazioni.php passando i dati come get nell’URL .
3.	CRUD e Modale: Le azioni Aggiungi/Modifica usano un modale e inviano le richieste CRUD (create, get_single, update, delete) all'endpoint AJAX.
