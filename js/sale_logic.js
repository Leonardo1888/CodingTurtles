// La logica che gestisce gli eventi sulla pagina (come click sui pulsanti statici e submit di form)
$(document).ready(function() {
    // Apertura modale aggiunta (e altri eventi statici)
    $('#openAddSala').click(function() {
        $('#modalTitle').text('Aggiungi Sala');
        $('#form-action').val('create');
        $('#form-codice').prop('readonly', false);
        $('#salaForm')[0].reset();
        $('#salaModal').show();
    });

    // Chiudi modale
    $('#closeModalBtn').click(function() {
        $('#salaModal').hide();
    });

    // Chiudi modale con ESC
    $(document).keyup(function(e) {
        if (e.key === "Escape") {
            $('#salaModal').hide();
        }
    });

    // Submit form aggiungi/modifica
    $('#salaForm').submit(function(e) {
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
            url: 'ajax/ajax_crud.php',
            data: postData,
            dataType: 'json',
            success: function(res) {
                if (res.success) {
                    alert(res.message);
                    $('#salaModal').hide();
                    loadSale(); // funzione che carica la tabella con AJAX dal file crud_functions.js
                } else {
                    alert('Errore: ' + res.message);
                }
            },
            error: function() {
                alert('Errore di comunicazione con il server');
            }
        });
    });

    // Chiamata iniziale per caricare le sale
    loadSale();
});

// Funzione per edit: apre modale con dati precaricati
function editSala(codice) {
    $.ajax({
        type: 'POST',
        url: 'ajax/ajax_crud.php',
        data: {
            action: 'get_single',
            codice: codice
        },
        dataType: 'json',
        success: function(res) {
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
        error: function() {
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
        url: 'ajax/ajax_crud.php',
        data: {
            action: 'delete',
            codice: codice
        },
        dataType: 'json',
        success: function(res) {
            if (res.success) {
                alert(res.message);
                loadSale();
            } else {
                alert('Errore: ' + res.message);
            }
        },
        error: function() {
            alert('Errore di comunicazione con il server');
        }
    });
}

// Funzione di escape HTML per sicurezza
function escapeHtml(text) {
    return $('<div>').text(text).html();
}