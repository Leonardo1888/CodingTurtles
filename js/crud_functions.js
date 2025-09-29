// CRUD Functions per la gestione delle Sale
$(document).ready(function() {
    // Carica le sale all'avvio
    loadSale();
    
    // Gestione invio form di ricerca
    $('#filtro-form').on('submit', function(e) {
        e.preventDefault();
        loadSale();
    });
    
    // Gestione form aggiungi sala
    $('#add-sala-form').on('submit', function(e) {
        e.preventDefault();
        addSala();
    });
    
    // Gestione form modifica sala
    $('#edit-sala-form').on('submit', function(e) {
        e.preventDefault();
        updateSala();
    });
    
    // Auto-generate codice sala
    $('#generate-codice').on('click', function() {
        generateCodiceSala();
    });
});

// Funzione per caricare le sale con filtri
function loadSale() {
    const formData = {
        action: 'read',
        codice: $('#Codice').val(),
        nome: $('#Nome').val(),
        tema: $('#Tema').val(),
        mq_min: $('#Mq_min').val(),
        mq_max: $('#Mq_max').val()
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

// Funzione per visualizzare le sale nella tabella
function displaySale(sale) {
    let html = '';
    if (sale.length === 0) {
        html = '<tr><td colspan="6">Nessuna sala trovata</td></tr>';
    } else {
        sale.forEach(function(sala) {
            html += `
                <tr>
                    <td>${escapeHtml(sala.codice)}</td>
                    <td>${escapeHtml(sala.nome)}</td>
                    <td>
                        <span class="badge ${getTemaClass(sala.tema)}">${escapeHtml(sala.tema)}</span>
                    </td>
                    <td>${sala.mq} m²</td>
                    <td><a href="fasce_orarie.php?sala=${sala.codice}">${sala.nFasceOrarie}</a></td>
                    <td>
                        <button class="btn btn-sm btn-primary me-1" onclick="editSala('${sala.codice}')" title="Modifica">
                            <i class="fa fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="deleteSala('${sala.codice}', '${escapeHtml(sala.nome)}')" title="Elimina">
                            <i class="fa fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `;
        });
    }
    
    $('#risultati-tabella-sale').html(html);
}

// Funzione per aggiungere una nuova sala
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

// Funzione per modificare una sala esistente
function editSala(codice) {
    $.ajax({
        type: 'POST',
        url: 'ajax/ajax_crud.php',
        data: { action: 'get_single', codice: codice },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                // Popola il form di modifica
                $('#edit-codice').val(response.data.codice);
                $('#edit-nome').val(response.data.nome);
                $('#edit-tema').val(response.data.tema);
                $('#edit-mq').val(response.data.mq);
                
                // Mostra il modal
                $('#editSalaModal').modal('show');
            } else {
                showMessage('error', response.message);
            }
        },
        error: function() {
            console.log("errore nel crud funct in edit sala");
            showMessage('error', 'Errore nel caricamento dei dati della sala');
        }
    });
}

// Funzione per aggiornare una sala
function updateSala() {
    const formData = {
        action: 'update',
        codice: $('#edit-codice').val().trim(),
        nome: $('#edit-nome').val().trim(),
        tema: $('#edit-tema').val(),
        mq: $('#edit-mq').val()
    };
    
    // Validazione frontend
    if (!validateSalaForm(formData, 'edit')) {
        return;
    }
    
    $.ajax({
        type: 'POST',
        url: 'ajax/ajax_crud.php',
        data: formData,
        dataType: 'json',
        beforeSend: function() {
            $('#edit-sala-btn').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Aggiornando...');
        },
        success: function(response) {
            $('#edit-sala-btn').prop('disabled', false).html('Aggiorna Sala');
            
            if (response.success) {
                $('#editSalaModal').modal('hide');
                loadSale();
                showMessage('success', response.message);
            } else {
                showMessage('error', response.message);
            }
        },
        error: function() {
            $('#edit-sala-btn').prop('disabled', false).html('Aggiorna Sala');
            showMessage('error', 'Errore di connessione al server');
        }
    });
}

// Funzione per eliminare una sala
function deleteSala(codice, nome) {
    // Conferma eliminazione
    if (!confirm(`Sei sicuro di voler eliminare la sala "${nome}" (${codice})?`)) {
        return;
    }
    
    $.ajax({
        type: 'POST',
        url: 'ajax/ajax_crud.php',
        data: { action: 'delete', codice: codice },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                loadSale();
                showMessage('success', response.message);
            } else {
                showMessage('error', response.message);
            }
        },
        error: function() {
            showMessage('error', 'Errore durante l\'eliminazione della sala');
        }
    });
}

// Funzione per generare automaticamente il codice sala
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

// Funzioni utility
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