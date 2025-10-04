// CRUD Functions per la gestione delle Sale
$(document).ready(function() {
    // Carica le sale all'avvio
    loadSale();
    
    // Gestione invio form di ricerca
    $('#filtro-form').on('submit', function(e) {
        e.preventDefault();
        loadSale();
    });
    
    // Auto-generate codice sala
    $('#generate-codice').on('click', function() {
        generateCodiceSala();
    });
});

/* ----------------- CRUD FUNCTIONS ----------------- */

// CREATE
function addSala() {
    const formData = {
        action: 'create',
        codice: $('#add-codice').val().trim(),
        nome: $('#add-nome').val().trim(),
        tema: $('#add-tema').val(),
        mq: $('#add-mq').val()
    };
    
    // Validazione frontend
    if (!validateSalaForm(formData, 'add')) {
        return;
    }
    
    $.ajax({
        type: 'POST',
        url: 'ajax/ajax_crud.php',
        data: formData,
        dataType: 'json',
        beforeSend: function() {
            $('#add-sala-btn').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Salvando...');
        },
        success: function(response) {
            $('#add-sala-btn').prop('disabled', false).html('Aggiungi Sala');
            
            if (response.success) {
                $('#addSalaModal').modal('hide');
                $('#add-sala-form')[0].reset();
                loadSale();
                showMessage('success', response.message);
            } else {
                showMessage('error', response.message);
            }
        },
        error: function() {
            $('#add-sala-btn').prop('disabled', false).html('Aggiungi Sala');
            showMessage('error', 'Errore di connessione al server');
        }
    });
}

// READ: Funzione per caricare le sale con filtri e i pulsanti
function loadSale() {
    const formData = {
        action: 'read',
        codice: $('#Codice').val(),
        nome: $('#Nome').val(),
        tema: $('#Tema').val(),
        mq_min: $('#Mq_min').val(),
        mq_max: $('#Mq_max').val(),
        orderBy: currentOrderBy,
        orderDir: currentOrderDir
    };
    
    $.ajax({
        type: 'POST',
        url: 'ajax/ajax_crud.php',
        data: formData,
        dataType: 'json',
        beforeSend: function() {
            $('#risultati-tabella-sale').html('<tr><td colspan="6"><i class="fa fa-spinner fa-spin"></i> Caricamento...</td></tr>');
        },
        success: function(response) {
            if (response.success) {
                displaySale(response.data);
                showMessage('success', response.message);
            } else {
                $('#risultati-tabella-sale').html('<tr><td colspan="6">Nessuna sala trovata</td></tr>');
                showMessage('error', response.message);
            }
        },
        error: function() {
            console.log("errore in crud funct nel load");
            $('#risultati-tabella-sale').html('<tr><td colspan="5">Errore nel caricamento dei dati</td></tr>');
            showMessage('error', 'Errore di connessione al server');
        }
    });
}

// READ: Funzione per visualizzare le sale nella tabella
function displaySale(sale) {
    let html = '';
    if (sale.length === 0) {
        html = '<tr><td colspan="5" style="text-align:center;">Nessuna sala trovata.</td></tr>';
    } else {
        sale.forEach(function(s) {
            html += `<tr>
                <td>${escapeHtml(s.codice)}</td>
                <td>${escapeHtml(s.nome)}</td>
                <td>${escapeHtml(s.tema)}</td>
                <td>${s.mq}</td>
                <td><a href="fasce_orarie.php?sala=${s.codice}">${s.nFasceOrarie}</a></td>
                <td><a href="prenotazioni.php?sala=${s.codice}">${s.nPrenotazioni}</a></td>
                <td>
                    <button class="btn-modifica" onclick="editSala('${escapeHtml(s.codice)}');"><i class="fa fa-edit"></i> Modifica</button>
                    <button class="btn-elimina" onclick="deleteSala('${escapeHtml(s.codice)}','${escapeHtml(s.nome)}');"><i class="fa fa-trash-alt"></i> Elimina</button>
                </td>
            </tr>`;
        });
    }
    $('#risultati-tabella-sale').html(html);
}

// UPDATE: apre modale con dati precaricati
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

// DELETE: 
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

// Funzione per generare automaticamente il codice sala durante la CREATE
function generateCodiceSala() {
    $.ajax({
        type: 'POST',
        url: 'ajax/ajax_crud.php',
        data: { action: 'read' },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                const sale = response.data;
                let maxNumber = 0;
                
                // Trova il numero più alto
                sale.forEach(function(sala) {
                    const match = sala.codice.match(/^S(\d{3})$/);
                    if (match) {
                        const number = parseInt(match[1]);
                        if (number > maxNumber) {
                            maxNumber = number;
                        }
                    }
                });
                
                // Genera il prossimo codice
                const nextNumber = maxNumber + 1;
                const nextCode = 'S' + nextNumber.toString().padStart(3, '0');
                $('#add-codice').val(nextCode);
                
                showMessage('info', `Codice suggerito: ${nextCode}`);
            }
        }
    });
}

// Funzione di validazione frontend
function validateSalaForm(data, prefix) {
    let isValid = true;
    
    // Reset errori precedenti
    $(`.${prefix}-error`).remove();
    
    // Validazione codice
    if (!data.codice) {
        showFieldError(`#${prefix}-codice`, 'Il codice è obbligatorio');
        isValid = false;
    } else if (!/^S\d{3}$/.test(data.codice)) {
        showFieldError(`#${prefix}-codice`, 'Il codice deve essere nel formato S001, S002, ecc.');
        isValid = false;
    }
    
    // Validazione nome
    if (!data.nome) {
        showFieldError(`#${prefix}-nome`, 'Il nome è obbligatorio');
        isValid = false;
    } else if (data.nome.length > 100) {
        showFieldError(`#${prefix}-nome`, 'Il nome non può superare i 100 caratteri');
        isValid = false;
    }
    
    // Validazione tema
    if (!data.tema) {
        showFieldError(`#${prefix}-tema`, 'Il tema è obbligatorio');
        isValid = false;
    }
    
    // Validazione metri quadrati
    if (!data.mq || isNaN(data.mq) || data.mq <= 0 || data.mq > 1000) {
        showFieldError(`#${prefix}-mq`, 'I metri quadrati devono essere un numero tra 1 e 1000');
        isValid = false;
    }
    
    return isValid;
}

/* ------ Funzioni utility ------ */ 
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function getTemaClass(tema) {
    const classes = {
        'Cardio': 'badge-danger',
        'Pesi liberi': 'badge-warning',
        'Macchine': 'badge-primary',
        'Corpo libero': 'badge-success',
        'Sauna': 'badge-info'
    };
    return classes[tema] || 'badge-secondary';
}

function showMessage(type, message) {
    const alertClass = {
        'success': 'alert-success',
        'error': 'alert-danger',
        'warning': 'alert-warning',
        'info': 'alert-info'
    };
    
    const alert = `
        <div class="alert ${alertClass[type]} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    `;
    
    // Rimuovi alert precedenti
    $('.alert').fadeOut(300, function() { $(this).remove(); });
    
    // Aggiungi nuovo alert
    $('#risultati').prepend(alert);
    
    // Auto-hide dopo 5 secondi
    setTimeout(function() {
        $('.alert').fadeOut(300, function() { $(this).remove(); });
    }, 5000);
}

function showFieldError(fieldSelector, message) {
    const errorHtml = `<small class="${fieldSelector.substring(1)}-error text-danger d-block mt-1">${message}</small>`;
    $(fieldSelector).addClass('is-invalid').after(errorHtml);
}

// Reset form validation
function resetFormValidation(formSelector) {
    $(formSelector + ' .is-invalid').removeClass('is-invalid');
    $(formSelector + ' .text-danger').remove();
}