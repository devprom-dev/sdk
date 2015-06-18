<?php

// -----------------------------------------------
// Cryptographp v1.4
// (c) 2006-2007 Sylvain BRISON 
//
// www.cryptographp.com 
// cryptographp@alphpa.com 
//
// Licence CeCILL modifiйe
// => Voir fichier Licence_CeCILL_V2-fr.txt)
// -----------------------------------------------


// -------------------------------------
// Configuration du fond du cryptogramme
// -------------------------------------

$cryptwidth  = 100;  // Largeur du cryptogramme (en pixels)
$cryptheight = 35;   // Hauteur du cryptogramme (en pixels)

$bgR  = 230;         // Couleur du fond au format RGB: Red (0->255)
$bgG  = 230;         // Couleur du fond au format RGB: Green (0->255)
$bgB  = 230;         // Couleur du fond au format RGB: Blue (0->255)

$bgclear = false;     // Fond transparent (true/false)
                     // Uniquement valable pour le format PNG

$bgimg = '';                 // Le fond du cryptogramme peut-кtre une image  
                             // PNG, GIF ou JPG. Indiquer le fichier image
                             // Exemple: $fondimage = 'photo.gif';
				                     // L'image sera redimensionnйe si nйcessaire
                             // pour tenir dans le cryptogramme.
                             // Si vous indiquez un rйpertoire plutфt qu'un 
                             // fichier l'image sera prise au hasard parmi 
                             // celles disponibles dans le rйpertoire

$bgframe = true;    // Ajoute un cadre de l'image (true/false)


// ----------------------------
// Configuration des caractиres
// ----------------------------

// Couleur de base des caractиres

$charR = 50;     // Couleur des caractиres au format RGB: Red (0->255)
$charG = 50;     // Couleur des caractиres au format RGB: Green (0->255)
$charB = 50;     // Couleur des caractиres au format RGB: Blue (0->255)

$charcolorrnd = false;      // Choix alйatoire de la couleur.
$charcolorrndlevel = 2;    // Niveau de clartй des caractиres si choix alйatoire (0->4)
                           // 0: Aucune sйlection
                           // 1: Couleurs trиs sombres (surtout pour les fonds clairs)
                           // 2: Couleurs sombres
                           // 3: Couleurs claires
                           // 4: Couleurs trиs claires (surtout pour fonds sombres)

$charclear = 0;   // Intensitй de la transparence des caractиres (0->127)
                  // 0=opaques; 127=invisibles
	                // interessant si vous utilisez une image $bgimg
	                // Uniquement si PHP >=3.2.1

// Polices de caractиres

//$tfont[] = 'Alanden_.ttf';       // Les polices seront alйatoirement utilisйes.
//$tfont[] = 'bsurp___.ttf';       // Vous devez copier les fichiers correspondants
//$tfont[] = 'ELECHA__.TTF';       // sur le serveur.
//$tfont[] = 'luggerbu.ttf';         // Ajoutez autant de lignes que vous voulez   
//$tfont[] = 'RASCAL__.TTF';       // Respectez la casse ! 
$tfont[] = 'SCRAWL.TTF';  
//$tfont[] = 'WAVY.TTF';   


// Caracteres autorisйs
// Attention, certaines polices ne distinguent pas (ou difficilement) les majuscules 
// et les minuscules. Certains caractиres sont faciles а confondre, il est donc
// conseillй de bien choisir les caractиres utilisйs.

$charel = 'ABCDEFGHKLMNPRTWXYZ234569';       // Caractиres autorisйs

$crypteasy = true;       // Crйation de cryptogrammes "faciles а lire" (true/false)
                         // composйs alternativement de consonnes et de voyelles.

$charelc = 'BCDFGKLMPRTVWXZ';   // Consonnes utilisйes si $crypteasy = true
$charelv = 'AEIOUY';              // Voyelles utilisйes si $crypteasy = true

$difuplow = false;          // Diffйrencie les Maj/Min lors de la saisie du code (true, false)

$charnbmin = 5;         // Nb minimum de caracteres dans le cryptogramme
$charnbmax = 5;         // Nb maximum de caracteres dans le cryptogramme

$charspace = 16;        // Espace entre les caracteres (en pixels)
$charsizemin = 16;      // Taille minimum des caractиres
$charsizemax = 18;      // Taille maximum des caractиres

$charanglemax  = 10;     // Angle maximum de rotation des caracteres (0-360)
$charup   = false;        // Dйplacement vertical alйatoire des caractиres (true/false)

// Effets supplйmentaires

$cryptgaussianblur = false; // Transforme l'image finale en brouillant: mйthode Gauss (true/false)
                            // uniquement si PHP >= 5.0.0
$cryptgrayscal = false;     // Transforme l'image finale en dйgradй de gris (true/false)
                            // uniquement si PHP >= 5.0.0

// ----------------------
// Configuration du bruit
// ----------------------

$noisepxmin = 0;      // Bruit: Nb minimum de pixels alйatoires
$noisepxmax = 0;      // Bruit: Nb maximum de pixels alйatoires

$noiselinemin = 0;     // Bruit: Nb minimum de lignes alйatoires
$noiselinemax = 0;     // Bruit: Nb maximum de lignes alйatoires

$nbcirclemin = 0;      // Bruit: Nb minimum de cercles alйatoires 
$nbcirclemax = 0;      // Bruit: Nb maximim de cercles alйatoires

$noisecolorchar  = 2;  // Bruit: Couleur d'ecriture des pixels, lignes, cercles: 
                       // 1: Couleur d'йcriture des caractиres
                       // 2: Couleur du fond
                       // 3: Couleur alйatoire
                       
$brushsize = 1;        // Taille d'ecriture du princeaiu (en pixels) 
                       // de 1 а 25 (les valeurs plus importantes peuvent provoquer un 
                       // Internal Server Error sur certaines versions de PHP/GD)
                       // Ne fonctionne pas sur les anciennes configurations PHP/GD

$noiseup = false;      // Le bruit est-il par dessus l'ecriture (true) ou en dessous (false) 

// --------------------------------
// Configuration systиme & sйcuritй
// --------------------------------

$cryptformat = "png";   // Format du fichier image gйnйrй "GIF", "PNG" ou "JPG"
				                // Si vous souhaitez un fond transparent, utilisez "PNG" (et non "GIF")
				                // Attention certaines versions de la bibliotheque GD ne gerent pas GIF !!!

$cryptsecure = "md5";    // Mйthode de crytpage utilisйe: "md5", "sha1" ou "" (aucune)
                         // "sha1" seulement si PHP>=4.2.0
                         // Si aucune mйthode n'est indiquйe, le code du cyptogramme est stockй 
                         // en clair dans la session.
                       
$cryptusetimer = 0;        // Temps (en seconde) avant d'avoir le droit de regйnйrer un cryptogramme

$cryptusertimererror = 3;  // Action а rйaliser si le temps minimum n'est pas respectй:
                           // 1: Ne rien faire, ne pas renvoyer d'image.
                           // 2: L'image renvoyйe est "images/erreur2.png" (vous pouvez la modifier)
                           // 3: Le script se met en pause le temps correspondant (attention au timeout
                           //    par dйfaut qui coupe les scripts PHP au bout de 30 secondes)
                           //    voir la variable "max_execution_time" de votre configuration PHP

$cryptusemax = 1000;  // Nb maximum de fois que l'utilisateur peut gйnйrer le cryptogramme
                      // Si dйpassement, l'image renvoyйe est "images/erreur1.png"
                      // PS: Par dйfaut, la durйe d'une session PHP est de 180 mn, sauf si 
                      // l'hebergeur ou le dйveloppeur du site en ont dйcidй autrement... 
                      // Cette limite est effective pour toute la durйe de la session. 
                      
$cryptoneuse = false;  // Si vous souhaitez que la page de verification ne valide qu'une seule 
                       // fois la saisie en cas de rechargement de la page indiquer "true".
                       // Sinon, le rechargement de la page confirmera toujours la saisie.                          
                      
?>
