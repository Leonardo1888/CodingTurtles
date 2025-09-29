import random
from datetime import datetime, timedelta, time

# Dati di esempio per le tabelle
sala_codici = [f"S{i:03d}" for i in range(1, 51)]  # 50 sale da S001 a S050
nomi = ['Mario', 'Luigi', 'Anna', 'Giovanni', 'Sofia', 'Francesco', 'Alessia', 'Luca', 'Martina', 'Andrea']
cognomi = ['Rossi', 'Verdi', 'Bianchi', 'Neri', 'Gialli', 'Marroni', 'Arancioni', 'Rosa', 'Viola', 'Blu']
temi = ['Cardio', 'Pesi liberi', 'Macchine', 'Corpo libero', 'Sauna']
sale_Cardio = ['Tapis roulant', 'Cyclette', 'Ellittica']
sale_PesiLiberi = ['Manubri', 'Bilancieri', 'Kettlebell']
sale_Macchine = ['Lat machine', 'Leg press', 'Chest press']
sale_CorpoLibero = ['Tappetini', 'Bande elastiche', 'Sbarre per trazioni']
sale_Sauna = ['Sauna finlandese', 'Sauna a infrarossi', 'Bagno turco']
mq_values = [50, 100, 150, 200, 250]
possible_prices = [50, 100, 150, 200]

# Generazione di date casuali (Giugno 2025)
def random_date(start, end):
    return start + timedelta(days=random.randint(0, (end - start).days))

# Periodi per le date
start_date = datetime(2025, 6, 1)
end_date = datetime(2025, 6, 30)
start_date_abbonamento = datetime(2024, 1, 1)
end_date_abbonamento = datetime(2025, 6, 30)

# Generazione di orari casuali
def random_time():
    return f"{random.randint(8, 19)}:00:00"

# Creazione di query SQL
sql_queries = []

# Svuotamento delle tabelle
sql_queries.append("SET FOREIGN_KEY_CHECKS = 0;")
sql_queries.append("TRUNCATE TABLE SubAbbonamento;")
sql_queries.append("TRUNCATE TABLE Prenotazione;")
sql_queries.append("TRUNCATE TABLE Abbonamento;")
sql_queries.append("TRUNCATE TABLE Cliente;")
sql_queries.append("TRUNCATE TABLE Posto;")
sql_queries.append("TRUNCATE TABLE FasciaOraria;")
sql_queries.append("TRUNCATE TABLE Sala;")
sql_queries.append("SET FOREIGN_KEY_CHECKS = 1;")

# Mappatura tema -> lista sottocategorie
sottocategorie_per_tema = {
    'Cardio': sale_Cardio,
    'Pesi liberi': sale_PesiLiberi,
    'Macchine': sale_Macchine,
    'Corpo libero': sale_CorpoLibero,
    'Sauna': sale_Sauna
}

for i, codice in enumerate(sala_codici):
    tema = temi[i % len(temi)]
    sottocategoria_list = sottocategorie_per_tema[tema]
    sottocategoria = sottocategoria_list[i % len(sottocategoria_list)]

    nome = f"Sala {sottocategoria}"
    mq = random.choice(mq_values)
    sql_queries.append(f"INSERT INTO Sala (codice, nome, tema, mq) VALUES ('{codice}', '{nome}', '{tema}', {mq});")


# Generazione fasce orarie per ogni sala
def generate_fasce_orarie(codice, start_date, max_giorni=7):
    current_date = start_date
    fasce_orarie = []

    for _ in range(max_giorni):  # Limita a 7 giorni
        start_hour = 8  # Inizio giornata alle 8
        while start_hour < 20:
            durata = random.randint(1, 3)  # Durata random da 1 a 3 ore
            ora = time(start_hour, 0).strftime('%H:%M:%S')
            
            fasce_orarie.append(
                f"INSERT INTO FasciaOraria (sala, data, ora, durata) VALUES ('{codice}', '{current_date.strftime('%Y-%m-%d')}', '{ora}', {durata});"
            )
            start_hour += durata  # Incrementa ora inizio per la prossima fascia
        current_date += timedelta(days=1)  # Passa al giorno successivo
    return fasce_orarie

for codice in sala_codici:
    sql_queries.extend(generate_fasce_orarie(codice, start_date))


# Popolamento della tabella Posto
posto_ids = set()
for codice in sala_codici:
    for _ in range(20):
        data = random_date(start_date, end_date).strftime('%Y-%m-%d')
        ora = random_time()
        n_prog = random.randint(1, 100)
        id_posto = f"{codice}-{data}-{ora}-{n_prog}"
        if id_posto not in posto_ids:
            posto_ids.add(id_posto)
            sql_queries.append(f"INSERT INTO Posto (sala, data, ora, nProg) VALUES ('{codice}', '{data}', '{ora}', {n_prog});")

# Popolamento della tabella Cliente
clienti_codici = set()
for i in range(1, 501):
    codice = f"C{i:03d}"
    nome = random.choice(nomi)
    cognome = random.choice(cognomi)
    cf = ''.join(random.choices('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789', k=16))
    data_nas = random_date(datetime(1970, 1, 1), datetime(2000, 12, 31)).strftime('%Y-%m-%d')
    indirizzo = f"{random.randint(1, 100)} Via {random.choice(['Roma', 'Milano', 'Napoli', 'Torino', 'Palermo'])}"
    tel = ''.join(random.choices('0123456789', k=10))
    random_digits = ''.join(random.choices('0123456789', k=4))
    email = f"{nome.lower()}.{cognome.lower()}{random_digits}@example.com"
    if codice not in clienti_codici:
        clienti_codici.add(codice)
        sql_queries.append(f"INSERT INTO Cliente (codice, nome, cognome, cf, dataNas, indirizzo, tel, email) VALUES ('{codice}', '{nome}', '{cognome}', '{cf}', '{data_nas}', '{indirizzo}', '{tel}', '{email}');")

# Popolamento della tabella Abbonamento
abb_ids = set()
for i in range(1, 501):
    codice_cliente = f"C{random.randint(1, 500):03d}"
    inizio = random_date(start_date_abbonamento, end_date_abbonamento).strftime('%Y-%m-%d')
    fine = (random_date(datetime.strptime(inizio, '%Y-%m-%d'), end_date_abbonamento) + timedelta(days=365)).strftime('%Y-%m-%d')
    prezzo = random.choice(possible_prices)
    n_abb = f"A{i:03d}"
    if n_abb not in abb_ids:
        abb_ids.add(n_abb)
        sql_queries.append(f"INSERT INTO Abbonamento (nAbb, cliente, inizio, fine, prezzo) VALUES ('{n_abb}', '{codice_cliente}', '{inizio}', '{fine}', {prezzo});")

# Popolamento della tabella Prenotazione
prenotazione_nProg = set()
for codice in sala_codici:
    for _ in range(20):
        n_prog = random.randint(1, 1000)
        if n_prog not in prenotazione_nProg:
            prenotazione_nProg.add(n_prog)
            data = random_date(start_date, end_date).strftime('%Y-%m-%d')
            ora = random_time()
            codice_cliente = f"C{random.randint(1, 500):03d}"
            sql_queries.append(f"INSERT INTO Prenotazione (nProg, cliente, sala, data, ora, posto) VALUES ({n_prog}, '{codice_cliente}', '{codice}', '{data}', '{ora}', {random.randint(1, 100)});")

# Popolamento della tabella SubAbbonamento
sub_abb_ids = set()
prenotazioni = list(prenotazione_nProg)
abbonamenti = list(abb_ids)

for n_prog in prenotazioni:
    n_abb = random.choice(abbonamenti)
    id_sub_abb = f"{n_prog}-{n_abb}"
    if id_sub_abb not in sub_abb_ids:
        sub_abb_ids.add(id_sub_abb)
        sql_queries.append(f"INSERT INTO SubAbbonamento (prenotazione, abbonamento) VALUES ({n_prog}, '{n_abb}');")

# Scrittura delle query SQL in un file di testo
with open('popola_db.txt', 'w') as f:
    f.write('\n'.join(sql_queries))
