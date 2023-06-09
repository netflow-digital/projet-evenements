<section>
    <?php

    $requete = "SELECT evenements.*, lieux.nom, lieux.adresse, lieux.codePostal, lieux.ville, utilisateurs.nom AS nomOrganisateur, CASE WHEN ue.id_utilisateurs IS NULL THEN 'Non inscrit' ELSE 'Inscrit' END AS inscription, (evenements.nbPersonnesMax - (SELECT COUNT(inscrire.id_utilisateurs) FROM inscrire WHERE inscrire.id_events = evenements.id_events)) AS nbPlacesRestantes FROM evenements LEFT JOIN inscrire ue ON evenements.id_events=ue.id_events" . (!empty($_SESSION['id_utilisateurs']) ? " AND ue.id_utilisateurs = :userId" : "") . " INNER JOIN lieux ON evenements.id_lieux=lieux.id_lieux" . (!empty($_POST['lieuxId']) ? " AND lieux.id_lieux=:lieuxId" : "") . " INNER JOIN utilisateurs ON utilisateurs.id_utilisateurs=evenements.id_utilisateurs" .  (!empty($_POST['dateEvt']) ? " WHERE evenements.date = DATE_FORMAT(:dateEvt,'%Y-%m-%d %H:%i:%s')" : "");
    // connexion bdd
    try {
        include DB_CONFIG;
        $stmt = $cnx->prepare($requete);
        if (!empty($_SESSION['id_utilisateurs'])) {
            $stmt->bindParam(':userId', $_SESSION['id_utilisateurs']);
        }
        if (!empty($_POST['lieuxId'])) {
            $stmt->bindParam(':lieuxId', $_POST['lieuxId']);
        }
        if (!empty($_POST['dateEvt'])) {
            $stmt->bindParam(':dateEvt', $_POST['dateEvt']);
        }
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    ?>
        <?php foreach ($result as $evt) : ?>
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
                        <p>Nombre de places restantes : <?= $evt['nbPlacesRestantes'] ?></p>
                        <?php if (!empty($_SESSION['id_utilisateurs']) && $_SESSION['role_utilisateurs'] == 'admin') :    ?>
                            <button class="augmentationPlace"><a href="augmentationPlace.php?id_evenement=<?= $evt['id_events']; ?> & nbPersonnesMax=<?= $evt['nbPersonnesMax']; ?>"> + </a></button>
                            <button class="diminutionPlace"><a href="diminutionPlace.php?id_evenement=<?= $evt['id_events']; ?> & nbPersonnesMax=<?= $evt['nbPersonnesMax']; ?>"> - </a></button>
                        <?php endif ?>
                    </div>
                    <?php if (!empty($_SESSION['id_utilisateurs']) && $evt['inscription'] == 'Inscrit') : ?>
                        <button class="desinscription" style="display: block;"><a href="desinscrireEvent.php?id_evenement=<?= $evt['id_events'] ?>">Se désinscrire </a></button>
                    <?php elseif (!empty($_SESSION['id_utilisateurs'])) : ?>
                        <button class="inscription" style="display: block;"><a href="inscrireEvent.php?id_evenement=<?= $evt['id_events'] ?>">S'inscrire </a></button>
                    <?php endif; ?>
                    <?php if (!empty($_SESSION['id_utilisateurs']) && $_SESSION['role_utilisateurs'] == 'admin') : ?>
                        <button class="supprimer"><a href="supprimerEvent.php?id_evenement=<?= $evt['id_events'] ?>"> Supprimer l'événement</a></button>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach ?>
</section>
<?php
    } catch (PDOException $e) {
        // Affichage d'un message d'erreur si la connexion à la base de données a échoué
        echo "Erreur de connexion à la base de données : " . $e->getMessage();
    } finally {
        $stmt->closeCursor();
    }
?>