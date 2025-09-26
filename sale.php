<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Coding Turtles - Palestra</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="css/style.css" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>

    <?php include("includes/header.php"); ?>
    <?php include("includes/navbar.php"); ?>

    <div class="main-container">
        <div class="contenitore-centrale">

            <aside class="colonna-filtro">
                <h3>Filtra le sale</h3>
                <form id="filtro-form">
                    <label for="Codice">Codice Sala:</label>
                    <input type="text" id="Codice" name="Codice" /><br />

                    <label for="Nome">Nome:</label>
                    <input type="text" id="Nome" name="Nome" /><br />

                    <label for="Tema">Tema:</label>
                    <select id="Tema" name="Tema">
                        <option value="">Tutti</option>
                        <?php
                        require_once 'database/db.php';
                        $sql = "SELECT DISTINCT tema FROM Sala";
                        $result = $conn->query($sql);
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo "<option value='" . htmlspecialchars($row['tema']) . "'>" . htmlspecialchars($row['tema']) . "</option>";
                            }
                        }
                        $conn->close();
                        ?>
                    </select><br />

                    <label>Metri quadrati:</label>
                    <div class="input-range">
                        da <input type="number" id="Mq_min" name="Mq_min" placeholder="min" />
                        <span>a</span>
                        <input type="number" id="Mq_max" name="Mq_max" placeholder="max" />
                    </div><br />

                    <button type="submit">Cerca</button>
                </form>
            </aside>

            <main class="colonna-risultati" id="risultati">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:15px;">
                    <h2>SALE</h2>
                    <button id="openAddSala" style="padding:8px 16px; cursor:pointer;">
                        <i class="fa fa-plus"></i> Aggiungi Sala
                    </button>
                </div>
                <p>Scopri le sale della palestra Coding Turtles filtrando a sinistra!</p>

                <div class="table-responsive">
                    <table cellspacing="0" cellpadding="6" style="width:100%; border-collapse:collapse;">
                        <thead>
                            <tr>
                                <th>Codice Sala</th>
                                <th>Nome</th>
                                <th>Tema</th>
                                <th>Metri Quadrati</th>
                                <th>Azioni</th>
                            </tr>
                        </thead>
                        <tbody id="risultati-tabella-sale">
                            <!-- Dati caricati via AJAX -->
                        </tbody>
                    </table>
                </div>
            </main>
        </div>
    </div>

    <!-- Modale personalizzato per aggiungi/modifica sala -->
    <div id="salaModal" class="modal-sfondo" style="display:none;">
        <div class="modal-contenuto">
            <form id="salaForm">
                <h3 id="modalTitle">Aggiungi Sala</h3>
                <input type="hidden" id="form-action" value="add" />
                <label>
                    Codice Sala:
                    <input type="text" id="form-codice" name="codice" required pattern="^S\d{3}$" />
                </label>
                <br />
                <label>
                    Nome:
                    <input type="text" id="form-nome" name="nome" required maxlength="100" />
                </label>
                <br />
                <label>
                    Tema:
                    <select id="form-tema" name="tema" required>
                        <option value="">Scegli tema</option>
                        <option>Cardio</option>
                        <option>Pesi liberi</option>
                        <option>Macchine</option>
                        <option>Corpo libero</option>
                        <option>Sauna</option>
                    </select>
                </label>
                <br />
                <label>
                    Metri Quadrati:
                    <input type="number" id="form-mq" name="mq" required min="1" max="1000" />
                </label>
                <br />
                <div style="margin-top:10px;">
                    <button type="submit" id="form-submit-btn">Salva</button>
                    <button type="button" id="closeModalBtn">Annulla</button>
                </div>
            </form>
        </div>
    </div>

    <style>
        .modal-sfondo {
            position: fixed;
            left: 0;
            top: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(0, 0, 0, 0.35);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
        }

        .modal-contenuto {
            background: #fff;
            padding: 2.2em 2em;
            border-radius: 12px;
            box-shadow: 0 8px 36px #2223;
            min-width: 250px;
            max-width: 95vw;
        }

        .modal-contenuto label {
            display: block;
            margin: 9px 0;
        }

        .modal-contenuto input,
        .modal-contenuto select {
            padding: 5px;
            margin-top: 4px;
            width: 92%;
        }

        .modal-contenuto button {
            padding: 6px 16px;
            margin-right: 12px;
            margin-top: 10px;
        }

        .modal-contenuto h3 {
            margin-bottom: 12px;
        }
    </style>

    <?php include("includes/footer.php"); ?>

    <script src="crud_functions.js"></script>
    <script>
        // Apertura modale aggiunta
        $('#openAddSala').click(function () {
            $('#modalTitle').text('Aggiungi Sala');
            $('#form-action').val('create');
            $('#form-codice').prop('readonly', false);
            $('#salaForm')[0].reset();
            $('#salaModal').show();
        });

        // Chiudi modale
        $('#closeModalBtn').click(function () {
            $('#salaModal').hide();
        });

        // Chiudi modale con ESC
        $(document).keyup(function (e) {
            if (e.key === "Escape") {
                $('#salaModal').hide();
            }
        });

        // Submit form aggiungi/modifica
        $('#salaForm').submit(function (e) {
            e.preventDefault();

            var action = $('#form-action').val();
            var postData = {
                action: action,
                codice: $('#form-codice').val().trim(),
                nome: $('#form-nome').val().trim(),
                tema: $('#form-tema').val(),
                mq: $('#form-mq').val()
            };

            $.ajax({
                type: 'POST',
                url: 'ajax_crud.php',
                data: postData,
                dataType: 'json',
                success: function (res) {
                    if (res.success) {
                        alert(res.message);
                        $('#salaModal').hide();
                        loadSale(); // funzione che carica la tabella con AJAX dal file crud_functions.js
                    } else {
                        alert('Errore: ' + res.message);
                    }
                },
                error: function () {
                    alert('Errore di comunicazione con il server');
                }
            });
        });

        // Funzione per edit: apre modale con dati precaricati
        function editSala(codice) {
            $.ajax({
                type: 'POST',
                url: 'ajax_crud.php',
                data: { action: 'get_single', codice: codice },
                dataType: 'json',
                success: function (res) {
                    if (res.success) {
                        $('#modalTitle').text('Modifica Sala');
                        $('#form-action').val('update');
                        $('#form-codice').val(res.data.codice).prop('readonly', true);
                        $('#form-nome').val(res.data.nome);
                        $('#form-tema').val(res.data.tema);
                        $('#form-mq').val(res.data.mq);
                        $('#salaModal').show();
                    } else {
                        alert('Errore: ' + res.message);
                    }
                },
                error: function () {
                    alert('Errore di comunicazione con il server');
                }
            });
        }

        // Funzione per elimina sala
        function deleteSala(codice, nome) {
            if (!confirm(`Eliminare la sala "${nome}" (${codice})?`)) {
                return;
            }
            $.ajax({
                type: 'POST',
                url: 'ajax_crud.php',
                data: { action: 'delete', codice: codice },
                dataType: 'json',
                success: function (res) {
                    if (res.success) {
                        alert(res.message);
                        loadSale();
                    } else {
                        alert('Errore: ' + res.message);
                    }
                },
                error: function () {
                    alert('Errore di comunicazione con il server');
                }
            });
        }

        // Sovrascrivo displaySale per includere le azioni edit/delete nella tabella
        const originalDisplaySale = displaySale;
        displaySale = function (sale) {
            let html = '';
            if (sale.length === 0) {
                html = '<tr><td colspan="5" style="text-align:center;">Nessuna sala trovata.</td></tr>';
            } else {
                sale.forEach(function (s) {
                    html += `<tr>
                        <td>${escapeHtml(s.codice)}</td>
                        <td>${escapeHtml(s.nome)}</td>
                        <td>${escapeHtml(s.tema)}</td>
                        <td>${s.mq}</td>
                        <td>
                            <button onclick="editSala('${escapeHtml(s.codice)}');" style="margin-right:6px;">Modifica</button>
                            <button onclick="deleteSala('${escapeHtml(s.codice)}','${escapeHtml(s.nome)}');" style="background:#d9534f; color:#fff;">Elimina</button>
                        </td>
                    </tr>`;
                });
            }
            $('#risultati-tabella-sale').html(html);
            updateStats(sale);
        };

        // Funzione di escape HTML per sicurezza
        function escapeHtml(text) {
            return $('<div>').text(text).html();
        }

        // Chiamata iniziale per caricare le sale
        $(document).ready(function () {
            loadSale();
        });

        // La funzione loadSale è definita in crud_functions.js e gestisce il caricamento AJAX
    </script>
</body>

</html>
