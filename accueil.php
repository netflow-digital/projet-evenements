<?php
include './config/config.php';

session_start();
$id_utilisateurs = isset($_SESSION['id_utilisateurs']) ? $_SESSION['id_utilisateurs'] : null;

try {
    $cnx = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';port=3306', DB_USER, DB_PASSWORD);
    $stmt = $cnx->prepare("CALL getEvenementsInscrits(:id_utilisateurs)");
    $stmt->bindParam(':id_utilisateurs', $id_utilisateurs, PDO::PARAM_INT);
    $stmt->execute();
    $evenements = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="<?= CSS ?>/style.css">
    <link rel="stylesheet" href="<?= CSS  ?>/header.css">
    <link rel="stylesheet" href="<?= CSS  ?>/responsive.css">

</head>

<body>
    <?php include TEMPLATE . '_header.php'; ?>
    <main id="container">
        <section>
            <?php foreach ($evenements as $evt) : ?>
                <div class="vignette">
                    <div><img src="<?= IMAGES ?><?= $evt['imageSrc'] ?>"></img></div>
                    <div class="descriptif">
                        <div class="disposition">
                            <h2><?= $evt['titre'] ?></h2>
                            <p>Date: <?= $evt['date'] ?></p>
                        </div>
                        <p> Organisé par : <?= $evt['nomOrganisateur'] ?></p>
                        <div class="disposition">
                            <button><a href="evenements.php?id= <?= $evt['id_events'] ?>"> Voir les détails</a></button>
                            <p>Nombre de places : <?= $evt['nbPersonnesMax'] ?></p>
                        </div>
                        <div class="disposition">
                            <p>Nombre de places restante : <?= $evt['nbPlacesRestantes'] ?></p>
                            <?php if ($id_utilisateurs !== null && $_SESSION['role_utilisateurs'] == 'admin') :    ?>
                                <button class="augmentationPlace"><a href="augmentationPlace.php?id_evenement=<?= $evt['id_events']; ?> & nbPersonnesMax=<?= $evt['nbPersonnesMax']; ?>"> + </a></button>
                                <button class="diminutionPlace"><a href="diminutionPlace.php?id_evenement=<?= $evt['id_events']; ?> & nbPersonnesMax=<?= $evt['nbPersonnesMax']; ?>"> - </a></button>
                            <?php endif ?>
                        </div>
                        <?php if ($id_utilisateurs !== null && $evt['inscription'] == 'Inscrit') : ?>
                            <button class="desinscription" style="display: block;"><a href="desinscrireEvent.php?id_evenement=<?= $evt['id_events'] ?>">Se désinscrire </a></button>
                        <?php elseif ($id_utilisateurs !== null) : ?>
                            <button class="inscription" style="display: block;"><a href="inscrireEvent.php?id_evenement=<?= $evt['id_events'] ?>">S'inscrire </a></button>
                        <?php endif; ?>
                        <?php if ($id_utilisateurs !== null && $_SESSION['role_utilisateurs'] == 'admin') : ?>
                            <button class="supprimer"><a href="supprimerEvent.php?id_evenement=<?= $evt['id_events'] ?>"> Supprimer l'événement</a></button>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach ?>
        </section>

        <script>
            // Attendre que la page soit chargée
            document.addEventListener('DOMContentLoaded', function() {
                // Sélectionner le bouton d'inscription
                const inscriptions = document.querySelectorAll('.inscription');

                // Ajouter un événement de clic pour chaque bouton d'inscription
                inscriptions.forEach(function(inscription) {
                    inscription.addEventListener('click', function() {
                        // Insertion de l'inscription dans la base de données
                        const xhr = new XMLHttpRequest();
                        xhr.open('POST', 'inscrireEvent.php');
                        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                        xhr.onload = function() {
                            if (xhr.status === 200) {
                                alert("Vous êtes maintenant inscrit à l'événement !");
                            }
                        };
                        xhr.send('user_id=' + user_id);


                    });
                });
            });

            // Attendre que la page soit chargée
            document.addEventListener('DOMContentLoaded', function() {
                // Sélectionner le bouton désinscription
                const desinscriptions = document.querySelectorAll('.desinscription');


                // Ajouter  un événement de clic pour le bouton de désinscription
                desinscriptions.forEach(function(desinscription) {
                    desinscription.addEventListener('click', function() {
                        // Récupération de l'ID de l'utilisateur à partir de sa session
                        const user_id = <?= $_SESSION['id_utilisateurs'] ?>;
                        //message en cas de desinscription à la bdd
                        const messageDesinscrit = new XMLHttpRequest();
                        messageDesinscrit.open('POST', 'desinscrireEvent.php');
                        messageDesinscrit.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                        messageDesinscrit.onload = function() {
                            if (messageDesinscrit.status === 200) {
                                alert("Vous êtes désinscrit de l'événement");
                            }
                        }
                        messageDesinscrit.send('user_id=' + user_id);
                    });

                });
            });

            // Attendre que la page soit chargée
            document.addEventListener('DOMContentLoaded', function() {
                // Sélectionner le bouton supprimerEvent
                const supprimer = document.querySelectorAll('.supprimer');


                // Ajouter  un événement de clic pour le bouton supprimer
                delate.forEach(function(supprimer) {
                    supprimer.addEventListener('click', function() {
                        // Récupération de l'ID de l'event à partir de sa session
                        const event_id = <?= $_SESSION['id_event'] ?>;
                        //message en cas de suppression à la bdd
                        const messageSuppression = new XMLHttpRequest();
                        messageSuppression.open('POST', 'supprimerEvent.php');
                        messageSuppression.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                        messageSuppression.onload = function() {
                            if (messageSuppression.status === 200) {
                                alert("Vous avez supprimer l'événement");
                            }
                        }
                        messageSuppression.send('user_id=' + user_id);
                    });

                });
            });


            // Attendre que la page soit chargée
            document.addEventListener('DOMContentLoaded', function() {
                // Sélectionner le bouton d'inscription
                const augmentationPlace = document.querySelectorAll('.augmentationPlace');

                // Ajouter un événement de clic pour chaque bouton d'inscription
                augmentationPlace.forEach(function(upgrade) {
                    upgrade.addEventListener('click', function() {
                        // Insertion de l'inscription dans la base de données
                        const xhr = new XMLHttpRequest();
                        xhr.open('POST', 'inscrireEvent.php');
                        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                        xhr.onload = function() {
                            if (xhr.status === 200) {
                                alert("Vous êtes maintenant inscrit à l'événement !");
                            }
                        };
                        xhr.send('user_id=' + user_id);


                    });
                });
            });
        </script>

    </main>
</body>

</html>