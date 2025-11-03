@echo off
echo ==========================================================
echo  Mise à jour automatique des catégories
echo ==========================================================
echo Ce script uniformise les catégories du tableau LOT
pause

REM  Normalisation des libellés
php bin/console doctrine:query:sql "UPDATE lot SET Categorie='Mobilier' WHERE Categorie IS NULL OR TRIM(Categorie)='';"
php bin/console doctrine:query:sql "UPDATE lot SET Categorie='Sièges & Fauteuils' WHERE Categorie IN ('Sieges','Fauteuils','Sièges','Sieges & Fauteuils');"
php bin/console doctrine:query:sql "UPDATE lot SET Categorie='Tables & Bureaux' WHERE Categorie IN ('Tables','Bureaux','Tables et Bureaux');"
php bin/console doctrine:query:sql "UPDATE lot SET Categorie='Armoires & Commodes' WHERE Categorie IN ('Armoires','Commodes','Armoires et Commodes');"
php bin/console doctrine:query:sql "UPDATE lot SET Categorie='Peintures anciennes' WHERE Categorie IN ('Peinture','Peintures','Peinture ancienne');"
php bin/console doctrine:query:sql "UPDATE lot SET Categorie='Gravures & Dessins' WHERE Categorie IN ('Gravures','Dessins','Gravures et Dessins');"
php bin/console doctrine:query:sql "UPDATE lot SET Categorie='Affiches anciennes' WHERE Categorie IN ('Affiches','Affiche ancienne');"
php bin/console doctrine:query:sql "UPDATE lot SET Categorie='Bijoux anciens' WHERE Categorie IN ('Bijoux','Bijoux ancien');"
php bin/console doctrine:query:sql "UPDATE lot SET Categorie='Montres de collection' WHERE Categorie IN ('Montres','Montre');"
php bin/console doctrine:query:sql "UPDATE lot SET Categorie='Objets précieux' WHERE Categorie IN ('Objets precieux','Precieux','Précieux');"
php bin/console doctrine:query:sql "UPDATE lot SET Categorie='Monnaies & Médailles' WHERE Categorie IN ('Monnaies','Medailles','Monnaies et Médailles');"
php bin/console doctrine:query:sql "UPDATE lot SET Categorie='Objets asiatiques' WHERE Categorie IN ('Asie','Asiatique');"
php bin/console doctrine:query:sql "UPDATE lot SET Categorie='Objets africains' WHERE Categorie IN ('Afrique','Africain');"
php bin/console doctrine:query:sql "UPDATE lot SET Categorie='Objets amérindiens' WHERE Categorie IN ('Amerindien','Amérindien');"

echo ==========================================================
echo  Catégories uniformisées avec succès !
echo ==========================================================
pause
