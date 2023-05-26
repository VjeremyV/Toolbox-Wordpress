# Toolbox-Wordpress
Langages et technos :
Back-End : 
- PHP : 8.1
- Symfony : 6.0.18

Front-end : 
- Twig
- CSS
- Bootstrap
- Javascript

-------------------------------------------------------------------------------------------------------------------------------------------------------------------------

La toolbox wordpress est un projet visant à compiler sur une même interface un ensemble d'utilitaires wordpress développés pour des cas clients spécifiques.
Actuellement, 3 fonctionnalités sont proposées : 

- Un traducteur/spinneur :
  en exportant les articles d'un site wordpress avec l'outil d'export intégré à wordpress, on utilise le fichier d'export xml pour traduire et la réécrire tous les         textes à la volée : 
  - Retourne un fichier .csv des textes traduits et réécris.
  - Utilise les API Deepl et WorldAi
  
- Un extracteur d'images :
   en exportant les articles d'un site wordpress avec l'outil d'export intégré à wordpress, on utilise le ficheir d'export xml pour récupérer toutes les images utilisées    dans les différents articles :
   - Retourne une archive .zip contenant les images rangées selon l'arborescence des dossiers du FTP du wordpress
   
- Un onglet plugins
  contient un ensemble de plugins Wordpress développés sur mesure : 
    - Auto-Featured-Imgs : plugin qui établie la liste des articles n'ayant pas d'images mise en avant et permet de faire une requête d'image par article à la banque         d'images pixabay.
    -	Clean-After-Import : Plugin permettant de nettoyer la base de données des anciennes URLs d’articles importés depuis un autre site.

- Un encodeur B64 : 
  transforme à la volée une URL dans son équivalent en base 64 avec un process d'intégration sur le site.

    
    
