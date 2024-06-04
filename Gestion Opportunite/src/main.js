document.addEventListener('DOMContentLoaded', function() {
    // Récupérer la liste des opportunités
    function getOpportunites() {
        fetch('get.php?action=get_opportunites')
            .then(response => response.json())
            .then(data => {
                // Vider la liste des opportunités
                const opportunitesList = document.getElementById('opportunites-list');
                opportunitesList.innerHTML = '';
                // Ajouter chaque opportunité à la liste
                data.forEach(opportunite => {
                    const item = document.createElement('li');
                    // Récupérer le nom de l'étape
                    fetch(`get.php?action=get_nometapes_opportunite&id=${opportunite.idOpportunité}`)
                        .then(response => response.json())
                        .then(etape => {
                            const etapeNom = etape.length > 0 ? etape[0].nomEtape : 'Étape inconnue';
                            item.textContent = `${opportunite.nomOpportunité} (${etapeNom})`;
                            const viewHistorique = document.createElement('button');
                            viewHistorique.textContent = 'Voir historique';
                            viewHistorique.dataset.id = opportunite.idOpportunité;
                            viewHistorique.addEventListener('click', viewHistoriqueOpportunite);
                            const deleteOpportunite = document.createElement('button');
                            deleteOpportunite.textContent = 'Supprimer';
                            deleteOpportunite.dataset.id = opportunite.idOpportunité;
                            deleteOpportunite.addEventListener('click', deleteOpportuniteHandler);
                            const incrEtapeOpportunite = document.createElement('button');
                            incrEtapeOpportunite.textContent = 'Incrémenter étape';
                            incrEtapeOpportunite.dataset.id = opportunite.idOpportunité;
                            incrEtapeOpportunite.addEventListener('click', incrEtapeOpportuniteHandler);
                            const addEvenementBtn = document.createElement('button');
                            addEvenementBtn.textContent = 'Ajouter événement';
                            addEvenementBtn.dataset.id = opportunite.idOpportunité;
                            addEvenementBtn.addEventListener('click', showAddEvenementModal);
                            item.appendChild(viewHistorique);
                            item.appendChild(deleteOpportunite);
                            item.appendChild(incrEtapeOpportunite);
                            item.appendChild(addEvenementBtn);
                            opportunitesList.appendChild(item);
                        })
                        .catch(error => console.error(error));
                });
            })
            .catch(error => console.error(error));
    }

    // Afficher l'historique d'une opportunité
    function viewHistoriqueOpportunite(event) {
        const idOpportunite = event.target.dataset.id;
        fetch(`get.php?action=get_historique_opportunite&id=${idOpportunite}`)
            .then(response => response.json())
            .then(data => {
                // Vider la liste de l'historique
                const historiqueList = document.getElementById('historique-list');
                historiqueList.innerHTML = '';

                // Ajouter chaque événement à la liste de l'historique
                if (Array.isArray(data) && data.length > 0) {
                    data.forEach(evenement => {
                        const item = document.createElement('li');
                        const eventText = document.createElement('span');
                        eventText.textContent = `${evenement.nomEtape} : ${evenement.nomevenement} (${evenement.dateevenement})`;

                        const deleteEvenementBtn = document.createElement('button');
                        deleteEvenementBtn.textContent = 'Supprimer';
                        deleteEvenementBtn.dataset.idEvenement = evenement.idEvenement;
                        deleteEvenementBtn.addEventListener('click', deleteEvenementHandler);

                        const buttonContainer = document.createElement('div');
                        buttonContainer.classList.add('button-container');
                        buttonContainer.appendChild(deleteEvenementBtn);

                        item.appendChild(eventText);
                        item.appendChild(buttonContainer);
                        historiqueList.appendChild(item);
                    });
                } else {
                    console.log('Aucun événement trouvé pour cette opportunité');
                }

                // Afficher la modale de l'historique
                const historiqueModal = document.getElementById('historique-modal');
                historiqueModal.style.display = 'block';
            })
            .catch(error => console.error(error));
    }

    // Supprimer une opportunité
    function deleteOpportuniteHandler(event) {
        const idOpportunite = event.target.dataset.id;
        if (confirm(`Êtes-vous sûr de vouloir supprimer l'opportunité ${idOpportunite} ?`)) {
            fetch('set.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: `action=delete_opportunite&idOpportunité=${idOpportunite}`
            })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        getOpportunites(); // Rafraîchir la liste des opportunités
                    } else {
                        console.error(data.message);
                    }
                })
                .catch(error => console.error(error));
        }
    }

    // Incrémente l'étape d'une opportunité
    function incrEtapeOpportuniteHandler(event) {
        const idOpportunite = event.target.dataset.id;
        if (confirm(`Êtes-vous sûr de vouloir incrémenter l'étape de l'opportunité ${idOpportunite} ?`)) {
            fetch('set.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: `action=incr_opportunite&idOpportunité=${idOpportunite}`
            })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        alert(data.message);
                        getOpportunites(); // Rafraîchir la liste des opportunités
                    } else if (data.status === 'error') {
                        alert(data.message);
                    } else {
                        console.error(data.message);
                    }
                })
                .catch(error => console.error(error));
        }
    }

    // Ajouter une nouvelle opportunité
    const addOpportuniteForm = document.getElementById('add-opportunite-form');
    addOpportuniteForm.addEventListener('submit', function(event) {
        event.preventDefault(); // Empêcher le rechargement de la page
        const nom = document.getElementById('nom-opportunite').value;
        if (nom) {
            fetch('set.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: `action=add_opportunite&nomOpportunite=${encodeURIComponent(nom)}`
            })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        // Opportunité ajoutée avec succès
                        document.getElementById('nom-opportunite').value = '';
                        getOpportunites(); // Rafraîchir la liste des opportunités
                    } else {
                        console.error(data.message);
                    }
                })
                .catch(error => console.error(error));
        }
    });

    // Ajouter un nouveau type d'événement
    const addTypeEvenementForm = document.getElementById('add-type-evenement-form');
    addTypeEvenementForm.addEventListener('submit', function(event) {
        event.preventDefault(); // Empêcher le rechargement de la page
        const nom = document.getElementById('nom-type-evenement').value;
        if (nom) {
            fetch('set.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: `action=add_type_evenement&nomEvenement=${encodeURIComponent(nom)}`
            })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        // Type d'événement ajouté avec succès
                        document.getElementById('nom-type-evenement').value = '';
                        alert(data.message);
                    } else {
                        console.error(data.message);
                    }
                })
                .catch(error => console.error(error));
        }
    });

    // Remplir le menu déroulant des types d'événement
    function remplirTypeEvenementSelect() {
        fetch('get.php?action=get_type_evenements')
            .then(response => response.json())
            .then(data => {
                const typeEvenementSelect = document.getElementById('type-evenement');
                typeEvenementSelect.innerHTML = '';
                data.forEach(typeEvenement => {
                    const option = document.createElement('option');
                    option.value = typeEvenement.idTypeEvenement;
                    option.textContent = typeEvenement.nomEvenement;
                    typeEvenementSelect.appendChild(option);
                });
            })
            .catch(error => console.error(error));
    }

    function showAddEvenementModal(event) {
        const idOpportunite = event.target.dataset.id;
        const addEvenementModal = document.getElementById('add-evenement-modal');
        addEvenementModal.dataset.idOpportunite = idOpportunite;
        remplirTypeEvenementSelect();
        addEvenementModal.style.display = 'block';
    }

    const addEvenementForm = document.getElementById('add-evenement-form');
    addEvenementForm.addEventListener('submit', function(event) {
        event.preventDefault();
        const idOpportunite = document.getElementById('add-evenement-modal').dataset.idOpportunite;
        const idTypeEvenement = document.getElementById('type-evenement').value;
        const dateEvenement = document.getElementById('date-evenement').value;
        // Récupérer l'étape actuelle de l'opportunité
        fetch(`get.php?action=get_idetapes_opportunite&id=${idOpportunite}`)
            .then(response => response.json())
            .then(etape => {
                const idEtape = etape.length > 0 ? etape[0].idEtape : 1; // Étape par défaut si non trouvée
                fetch('set.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: `action=add_evenement&idOpportunité=${idOpportunite}&idEtape=${idEtape}&idTypeEvenement=${idTypeEvenement}&dateEvenement=${dateEvenement}`
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            alert(data.message);
                            document.getElementById('add-evenement-modal').style.display = 'none';
                            getOpportunites(); // Rafraîchir la liste des opportunités
                        } else {
                            console.error(data.message);
                        }
                    })
                    .catch(error => console.error(error));
            })
            .catch(error => console.error(error));
    });

    function deleteEvenementHandler(event) {
        const idEvenement = event.target.dataset.idEvenement;
        if (confirm(`Êtes-vous sûr de vouloir supprimer l'événement ${idEvenement} ?`)) {
            fetch('set.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: `action=delete_evenement&idEvenement=${idEvenement}`
            })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        alert(data.message);
                        const historiqueModal = document.getElementById('historique-modal');
                        historiqueModal.style.display = 'none'; // Fermer la fenêtre modale
                    } else {
                        console.error(data.message);
                    }
                })
                .catch(error => console.error(error));
        }
    }


    // Initialiser l'application
    getOpportunites();
});
