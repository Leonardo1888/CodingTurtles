import random
from datetime import datetime, timedelta, time

# --- FUNZIONE PER GENERARE UN CODICE FISCALE REALISTICO ---
def genera_cf(nome, cognome, data_nas_str):
    """
    Genera un Codice Fiscale strutturato in modo realistico ma con
    codice comune e carattere di controllo casuali.
    """
    # Mappa per la conversione del mese
    mesi_map = {1: 'A', 2: 'B', 3: 'C', 4: 'D', 5: 'E', 6: 'H', 7: 'L', 8: 'M', 9: 'P', 10: 'R', 11: 'S', 12: 'T'}

    # 1. Cognome (3 lettere) - Semplificato prendendo le prime 3 lettere
    # e riempiendo con 'X' se più corto.
    cf_cognome = cognome.upper().replace(" ", "")[:3].ljust(3, 'X')

    # 2. Nome (3 lettere) - Semplificato
    cf_nome = nome.upper().replace(" ", "")[:3].ljust(3, 'X')

    data_dt = datetime.strptime(data_nas_str, '%Y-%m-%d')

    # 3. Anno di nascita (ultime 2 cifre)
    cf_anno = str(data_dt.year)[-2:]

    # 4. Mese di nascita (1 lettera dalla mappa)
    cf_mese = mesi_map[data_dt.month]

    # 5. Giorno di nascita e sesso (2 cifre)
    sesso = random.choice(['M', 'F'])
    giorno = data_dt.day
    if sesso == 'F':
        giorno += 40
    cf_giorno = f"{giorno:02d}"

    # 6. Comune di nascita (4 caratteri) - Simulato
    cf_comune = random.choice('ABCDEFGHIJKLMNOPQRSTUVWXYZ') + ''.join(random.choices('0123456789', k=3))

    # 7. Carattere di controllo (1 lettera) - Simulato
    cf_controllo = random.choice('ABCDEFGHIJKLMNOPQRSTUVWXYZ')

    return f"{cf_cognome}{cf_nome}{cf_anno}{cf_mese}{cf_giorno}{cf_comune}{cf_controllo}"


# --- CONFIGURAZIONE ---
NUM_SALE = 50
NUM_CLIENTI = 200
NUM_GIORNI_DA_POPOLARE = 7 # Popoliamo per una settimana
START_DATE_PRENOTAZIONI = datetime(2025, 6, 1)

# --- DATI DI ESEMPIO ---
nomi = ['Mario', 'Luigi', 'Anna', 'Giovanni', 'Sofia', 'Francesco', 'Alessia', 'Luca', 'Martina', 'Andrea', 'Antonio', 'Leonardo', 'Fabio', 'Riccardo', "Maurizio"]
cognomi = ['Rossi', 'Verdi', 'Bianchi', 'Neri', 'Gialli', 'Marroni', 'Arancioni', 'Rosa', 'Viola', 'Blu']
temi = ['Cardio', 'Pesi liberi', 'Macchine', 'Corpo libero', 'Sauna']
sottocategorie_per_tema = {
    'Cardio': ['Tapis roulant', 'Sala da ballo', 'Ellittica'],
    'Pesi liberi': ['Manubri', 'Bilancieri', 'Kettlebell'],
    'Macchine': ['Lat machine', 'Leg press', 'Chest press'],
    'Corpo libero': ['Tappetini', 'Bande elastiche', 'Sbarre per trazioni'],
    'Sauna': ['Sauna finlandese', 'Sauna a infrarossi', 'Bagno turco']
}
mq_values = [50, 80, 100, 150, 200]

# --- STRUTTURE DATI PER MEMORIZZARE I DATI GENERATI ---
generated_sale = []
generated_clienti = []
generated_abbonamenti = []
generated_fasce_orarie = []
generated_posti = []
generated_prenotazioni = []
generated_sub_abbonamenti = []

sql_queries = []

# --- 1. SVUOTAMENTO TABELLE ---
sql_queries.append("SET FOREIGN_KEY_CHECKS = 0;")
sql_queries.append("TRUNCATE TABLE SubAbbonamento;")
sql_queries.append("TRUNCATE TABLE Prenotazione;")
sql_queries.append("TRUNCATE TABLE Abbonamento;")
sql_queries.append("TRUNCATE TABLE Cliente;")
sql_queries.append("TRUNCATE TABLE Posto;")
sql_queries.append("TRUNCATE TABLE FasciaOraria;")
sql_queries.append("TRUNCATE TABLE Sala;")
sql_queries.append("SET FOREIGN_KEY_CHECKS = 1;")

# --- 2. POPOLAMENTO Sala ---
sala_codici = [f"S{i:03d}" for i in range(1, NUM_SALE + 1)]
for i, codice in enumerate(sala_codici):
    tema = temi[i % len(temi)]
    sottocategoria_list = sottocategorie_per_tema[tema]
    sottocategoria = sottocategoria_list[i % len(sottocategoria_list)]
    nome = f"Sala {sottocategoria}"
    mq = random.choice(mq_values)
    sala = {'codice': codice, 'nome': nome, 'tema': tema, 'mq': mq}
    generated_sale.append(sala)
    sql_queries.append(f"INSERT INTO Sala (codice, nome, tema, mq) VALUES ('{sala['codice']}', '{sala['nome']}', '{sala['tema']}', {sala['mq']});")

# --- 3. POPOLAMENTO Cliente ---
clienti_codici = [f"C{i:03d}" for i in range(1, NUM_CLIENTI + 1)]
for codice in clienti_codici:
    nome = random.choice(nomi)
    cognome = random.choice(cognomi)
    data_nas = (datetime(1970, 1, 1) + timedelta(days=random.randint(0, 11000))).strftime('%Y-%m-%d')
    
    # Generazione del CF in modo strutturato
    cf = genera_cf(nome, cognome, data_nas)
    
    indirizzo = f"Via {random.choice(['Roma', 'Milano', 'Napoli'])} {random.randint(1, 100)}"
    tel = ''.join(random.choices('0123456789', k=10))
    email = f"{nome.lower()}.{cognome.lower()}{random.randint(10,99)}@example.com"
    cliente = {'codice': codice, 'nome': nome, 'cognome': cognome, 'cf': cf, 'dataNas': data_nas, 'indirizzo': indirizzo, 'tel': tel, 'email': email}
    generated_clienti.append(cliente)
    sql_queries.append(f"INSERT INTO Cliente (codice, nome, cognome, cf, dataNas, indirizzo, tel, email) VALUES ('{cliente['codice']}', '{cliente['nome']}', '{cliente['cognome']}', '{cliente['cf']}', '{cliente['dataNas']}', '{cliente['indirizzo']}', '{cliente['tel']}', '{cliente['email']});")

# --- 4. POPOLAMENTO Abbonamento ---
prezzo_durata_map = {
    50: 30,   # Mensile
    100: 90,  # Trimestrale
    150: 180, # Semestrale
    200: 365  # Annuale
}
possible_prices = list(prezzo_durata_map.keys())

# Non tutti i clienti hanno un abbonamento, circa l'80%
clienti_con_abbonamento = random.sample(clienti_codici, int(len(clienti_codici) * 0.8))
for i, codice_cliente in enumerate(clienti_con_abbonamento):
    n_abb = f"A{i+1:03d}"
    
    # La data di inizio è casuale, ma la data di fine dipende dal prezzo
    inizio = (datetime(2025, 1, 1) + timedelta(days=random.randint(0, 150))).date()
    
    prezzo = random.choice(possible_prices)
    durata_giorni = prezzo_durata_map[prezzo]
    
    # Calcoliamo la data di fine
    fine = inizio + timedelta(days=durata_giorni)
    
    abbonamento = {'nAbb': n_abb, 'cliente': codice_cliente, 'inizio': inizio, 'fine': fine, 'prezzo': prezzo}
    generated_abbonamenti.append(abbonamento)
    sql_queries.append(f"INSERT INTO Abbonamento (nAbb, cliente, inizio, fine, prezzo) VALUES ('{abbonamento['nAbb']}', '{abbonamento['cliente']}', '{abbonamento['inizio']}', '{abbonamento['fine']}', {abbonamento['prezzo']});")


# --- 5. POPOLAMENTO FasciaOraria ---
for sala in generated_sale:
    for i in range(NUM_GIORNI_DA_POPOLARE):
        current_date = (START_DATE_PRENOTAZIONI + timedelta(days=i)).date()
        start_hour = 8
        while start_hour < 20:
            max_durata = min(3, 20 - start_hour)
            if max_durata < 1:
                break
            durata = random.randint(1, max_durata)
            ora = time(start_hour, 0).strftime('%H:%M:%S')
            
            fascia = {'sala': sala['codice'], 'data': current_date, 'ora': ora, 'durata': durata, 'mq': sala['mq']}
            generated_fasce_orarie.append(fascia)
            sql_queries.append(f"INSERT INTO FasciaOraria (sala, data, ora, durata) VALUES ('{fascia['sala']}', '{fascia['data']}', '{fascia['ora']}', {fascia['durata']});")
            
            start_hour += durata

# --- 6. POPOLAMENTO Posto ---
for fascia in generated_fasce_orarie:
    # Numero di posti in base ai mq della sala (es. 1 posto ogni 10mq)
    num_posti = max(1, fascia['mq'] // 10)
    for n_prog in range(1, num_posti + 1):
        posto = {'sala': fascia['sala'], 'data': fascia['data'], 'ora': fascia['ora'], 'nProg': n_prog}
        generated_posti.append(posto)
        sql_queries.append(f"INSERT INTO Posto (sala, data, ora, nProg) VALUES ('{posto['sala']}', '{posto['data']}', '{posto['ora']}', {posto['nProg']});")

# --- 7. POPOLAMENTO Prenotazione & SubAbbonamento ---
# Mappiamo i clienti ai loro abbonamenti per un accesso rapido
abbonamenti_per_cliente = {}
for abb in generated_abbonamenti:
    if abb['cliente'] not in abbonamenti_per_cliente:
        abbonamenti_per_cliente[abb['cliente']] = []
    abbonamenti_per_cliente[abb['cliente']].append(abb)
    
# Non tutti i posti vengono prenotati, diciamo il 70%
posti_da_prenotare = random.sample(generated_posti, int(len(generated_posti) * 0.7))
nProg_prenotazione = 1

for posto in posti_da_prenotare:
    cliente_casuale = random.choice(clienti_codici)
    
    prenotazione = {
        'nProg': nProg_prenotazione,
        'cliente': cliente_casuale,
        'sala': posto['sala'],
        'data': posto['data'],
        'ora': posto['ora'],
        'posto': posto['nProg']
    }
    generated_prenotazioni.append(prenotazione)
    sql_queries.append(f"INSERT INTO Prenotazione (nProg, cliente, sala, data, ora, posto) VALUES ({prenotazione['nProg']}, '{prenotazione['cliente']}', '{prenotazione['sala']}', '{prenotazione['data']}', '{prenotazione['ora']}', {prenotazione['posto']});")
    
    # Tentiamo di associare un SubAbbonamento
    if cliente_casuale in abbonamenti_per_cliente:
        # Controlla se il cliente ha un abbonamento valido per la data della prenotazione
        abbonamenti_validi = [
            abb for abb in abbonamenti_per_cliente[cliente_casuale]
            if abb['inizio'] <= prenotazione['data'] <= abb['fine']
        ]
        
        if abbonamenti_validi:
            # Associa la prenotazione a uno degli abbonamenti validi
            abbonamento_usato = random.choice(abbonamenti_validi)
            sub = {'prenotazione': prenotazione['nProg'], 'abbonamento': abbonamento_usato['nAbb']}
            generated_sub_abbonamenti.append(sub)
            sql_queries.append(f"INSERT INTO SubAbbonamento (prenotazione, abbonamento) VALUES ({sub['prenotazione']}, '{sub['abbonamento']});")

    nProg_prenotazione += 1

# --- 8. SCRITTURA FILE SQL ---
with open('popola_db.txt', 'w', encoding='utf-8') as f:
    f.write('\n'.join(sql_queries))

print(f"File 'popola_db.txt' generato con successo!")
print(f"- Sale: {len(generated_sale)}")
print(f"- Clienti: {len(generated_clienti)}")
print(f"- Abbonamenti: {len(generated_abbonamenti)}")
print(f"- Fasce Orarie: {len(generated_fasce_orarie)}")
print(f"- Posti: {len(generated_posti)}")
print(f"- Prenotazioni: {len(generated_prenotazioni)}")
print(f"- SubAbbonamenti: {len(generated_sub_abbonamenti)}")
