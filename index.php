<?php 
session_start();
include('connexionDB.php');
  $bdd = new PDO('mysql:host=127.0.0.1;dbname=bd_grillage;charset=utf8', 'root', '');

  //------------------------- On insert dans la table commentaire
  if(isset($_POST['comment_envoie'])){
    $id_user = (int)($_SESSION['id_user']);
    $comment_com = htmlentities(trim($_POST['comment_com']));
    $DB->insert("INSERT INTO commentaire (id_user, comment_com) VALUES (?, ?)", array($id_user, $comment_com)); 
    header('Location: index.php');  
  }

	// ------------------------------------------ On récupère tous les commentaires ------------------------------
  $affiche_comment = $DB->query("SELECT * FROM users 
                              inner join commentaire
                              on users.id_user = commentaire.id_user 
                              ORDER BY date_com desc 
                              LIMIT 5
                              "); 
  $affiche_comment = $affiche_comment->fetchAll(); 

	// ------------------------------------------ On récupère tous les MAISONS ------------------------------
  $affiche_maisons = $DB->query("SELECT *, count(parents.id_maison) as membre
                              FROM maisons 
                              INNER JOIN parents 
                              ON maisons.id_maison = parents.id_maison 
                              GROUP BY parents.id_maison 
                              LIMIT 5
                              "); 
  $affiche_maisons = $affiche_maisons->fetchAll(); 

	// ------------------------------------------ On récupère tous les PARENTS ------------------------------
  $affiche_parent = $DB->query("SELECT *, sum(montant_vers) montant FROM parents 
                              INNER JOIN maisons 
                              INNER JOIN versements 
                              ON parents.id_maison = maisons.id_maison 
                              AND versements.id_par = parents.id_par 
                              GROUP BY versements.id_par 
                              LIMIT 10 
                              "); 
  $affiche_parent = $affiche_parent->fetchAll(); 
  
  // ------------------------------------------ On récupère tous les FEMMES ------------------------------
  $affiche_femme = $DB->query("SELECT * 
                              FROM femmes 
                              INNER JOIN users 
                              ON users.id_user = femmes.id_user 
                              LEFT JOIN parents 
                              ON parents.id_par = femmes.id_par 
                              LEFT JOIN maisons 
                              ON (maisons.id_maison = femmes.id_maison OR maisons.id_maison = parents.id_maison)
                              LIMIT 7 
                              "); 
  $affiche_femme = $affiche_femme->fetchAll(); 

	// ------------------------------------------ On récupère les derniers versements des parents ------------------------------
  	$affiche_vers = $DB->query("SELECT *
                              FROM parents 
                              INNER JOIN versements 
                              ON versements.id_par = parents.id_par 
                              INNER JOIN maisons 
                              ON maisons.id_maison = parents.id_maison 
                              WHERE montant_vers <> 0
                              ORDER BY id_vers DESC 
                              LIMIT 5 
                              ");  
  	$affiche_vers = $affiche_vers->fetchAll(); 

    // ------------------------------------------ On récupère les derniers versements des FEMMES ------------------------------
    $affiche_vers_fem = $DB->query("SELECT *
                              FROM femmes 
                              INNER JOIN versements
                              ON versements.id_fem = femmes.id_fem 
                              INNER JOIN users 
                              ON users.id_user = femmes.id_user 
                              LEFT JOIN parents 
                              ON parents.id_par = femmes.id_par 
                              LEFT JOIN maisons 
                              ON (maisons.id_maison = femmes.id_maison OR maisons.id_maison = parents.id_maison) 
                              WHERE montant_vers <> 0 
                              ORDER BY id_vers DESC 
                              LIMIT 5 
                              ");  
    $affiche_vers_fem = $affiche_vers_fem->fetchAll(); 
  
	// ------------------------------------------ On récupère les derniers versements des jeunes ------------------------------
    $affiche_vers_jeune = $DB->query("SELECT *
                              FROM jeunes 
                              INNER JOIN versements 
                              ON versements.id_jeune = jeunes.id_jeune
                              WHERE montant_vers <> 0 
                              ORDER BY id_vers DESC 
                              LIMIT 5 
                              ");   
    $affiche_vers_jeune = $affiche_vers_jeune->fetchAll();

	// ------------------------------------------ On récupère tous les JEUNES ------------------------------
  $affiche_jeune = $DB->query("SELECT * FROM jeunes LIMIT 10 "); 
  $affiche_jeune = $affiche_jeune->fetchAll(); 
  
	// ----------------------------------------- Les nombres de maisons ----------------------------------------
	$reponse_mais = $bdd->prepare("SELECT count(*) AS nombre_mais FROM maisons "); 
	$reponse_mais->execute(array()); 
	while ($donnees_mais = $reponse_mais->fetch()){ 

  // ----------------------------------------- Les nombres de parents ----------------------------------------
  $reponse_par = $bdd->prepare("SELECT count(*) AS nombre_par FROM parents "); 
  $reponse_par->execute(array()); 
  while ($donnees_par = $reponse_par->fetch()){ 

  // ----------------------------------------- Les nombres de jeunes ----------------------------------------
  $reponse_jeune = $bdd->prepare("SELECT count(*) AS nombre_jeune FROM jeunes "); 
  $reponse_jeune->execute(array()); 
  while ($donnees_jeune = $reponse_jeune->fetch()){  

  // ----------------------------------------- Les nombres de femmes ----------------------------------------
  $reponse_fem = $bdd->prepare("SELECT count(*) AS nombre_fem FROM femmes "); 
  $reponse_fem->execute(array()); 
  while ($donnees_fem = $reponse_fem->fetch()){ 

    // ----------------------------------------- Les nombres de parents -------------------------------------
    $reponse_count_par = $bdd->prepare("SELECT count(*) AS nombre_count_par 
                                        FROM parents 
                                        INNER JOIN versements
                                        ON parents.id_par = versements.id_par 
                                        WHERE montant_vers <> 0
                                      "); 
    $reponse_count_par->execute(array()); 
    while ($donnees_count_par = $reponse_count_par->fetch()){ 

    // ----------------------------------------- Les nombres de femmes --------------------------------------
    $reponse_count_fem = $bdd->prepare("SELECT count(*) AS nombre_count_fem 
                                        FROM femmes 
                                        INNER JOIN versements
                                        ON femmes.id_fem = versements.id_fem
                                        WHERE montant_vers <> 0 
                                      "); 
    $reponse_count_fem->execute(array()); 
    while ($donnees_count_fem = $reponse_count_fem->fetch()){ 

    // ----------------------------------------- Les nombres de jeunes --------------------------------------
    $reponse_count_jeune = $bdd->prepare("SELECT count(*) AS nombre_count_jeune 
                                        FROM jeunes 
                                        INNER JOIN versements
                                        ON jeunes.id_jeune = versements.id_jeune 
                                        WHERE montant_vers <> 0 
                                      "); 
    $reponse_count_jeune->execute(array()); 
    while ($donnees_count_jeune = $reponse_count_jeune->fetch()){ 

?> 

<!DOCTYPE html>
<html lang="en">
<head> 
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Index final</title>
  <meta content="" name="description">
  <meta content="" name="keywords">

  <!-- Favicons -->
  <link href="assets/img/favicon.png" rel="icon">
  <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,600;1,700&family=Montserrat:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&family=Raleway:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/vendor/aos/aos.css" rel="stylesheet">
  <link href="assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
  <link href="assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">

  <!-- Template Main CSS File -->
  <link href="assets/css/main.css" rel="stylesheet">

  <!-- =======================================================
  * Template Name: Impact - v1.1.0
  * Template URL: https://bootstrapmade.com/impact-bootstrap-business-website-template/
  * Author: BootstrapMade.com
  * License: https://bootstrapmade.com/license/
  ======================================================== -->
</head>

<body>
  <!-- ======= Header ======= -->
  <section id="topbar" class="topbar">
    <div class="container-fluid">
      <div class="row contact-info pt-2"> 
          <div class="col-8 d-flex justify-content-md-between"> 
            <div class="contact-info d-flex align-items-center px-2"> 
              <?php if(isset($_SESSION['id_user'])){ ?> 
                <a href='profil.php?number=<?= $_SESSION['id_user'] ?>' class=''> 
                  <i class="d-flex align-items-center"> 
                      <?php if(isset($_SESSION['id_user'])){ ?>
                        <img src="images_profil/<?php echo $_SESSION['image_user']; ?>" class="rounded" style='width: 20px;'/>
                        <span class='mx-2'><?= $_SESSION['prenom_user']; ?></span> 
                      <?php } ?> 
                  </i>  
                </a> 
                <div class="social-links d-none d-md-flex align-items-center">
                  <a href="#" class="phone"><i class="bi bi-phone px-2"></i><?php echo $_SESSION['tel_user']; ?></a>  
                </div>
              <?php } ?>    
            </div>  
          </div>  
         <div class="col-4">  
            <div class="social-links d-flex justify-content-end px-2"> 
              <?php if(!isset($_SESSION['id_user'])){ ?> 
                <a href="form_ajout_user2.php" class=""><img src="images/Add_User.png" class="img-fluid rounded" width='20px;'/></a>
                <a href="login.php" class=""><img src="images/log-in.png" class="img-fluid rounded" width='20px;'/></a> 
              <?php }else{ ?>    
              <a href="deconnexion.php" class="whatsapp"><img src="images/log-out.png" class="img-fluid rounded" width='20px;'/></a> 
              <?php } ?>    
            </div> 
         </div>  
      </div> 
    </div> 
  </section>
  <!-- End Top Bar --> 

  <header id="header" class="header d-flex align-items-center">

    <div class="container-fluid container-xl d-flex align-items-center justify-content-between">
      <a href="index.php" class="logo d-flex align-items-center">
        <!-- Uncomment the line below if you also wish to use an image logo -->
        <!-- <img src="assets/img/logo.png" alt=""> -->
        <h1>HORE-FELLO<span>.</span></h1> 
      </a>
      <nav id="navbar" class="navbar"> 
        <ul> 
          <li class=''><a href="#hero">Home</a></li>
          <li class=''><a href="#about">Maisons</a></li> 
          <li class=''><a href="#services">Parents</a></li> 
          <li class=''><a href="#portfolio">Femmes</a></li>
          <li class=''><a href="#team">Jeunes</a></li> 
          <li class=''><a href="#states">Versements</a></li>
          <li class="dropdown"><a href="#"><span>Plus</span> <i class="bi bi-chevron-down dropdown-indicator"></i></a>
            <ul> 
              <?php if(isset($_SESSION['id_user']) AND ($_SESSION['status_user'] != "Utilisateur")){ ?>
                <li><a href="vue_maisons.php">Maisons</a></li> 
                <li><a href="vue_parents.php">Parents</a></li> 
                <li><a href="vue_femmes.php">Femmes</a></li> 
                <li><a href="vue_jeunes.php">Jeunes</a></li>   
                <li><a href="vue_versements.php">Versements</a></li>  
                <li><a href="vue_comptabilite.php">Statistiques</a></li> 
              <?php } ?>          
            </ul>  
          </li> 
          <?php if(isset($_SESSION['id_user']) AND($_SESSION['status_user'] == "Grand Admin" OR $_SESSION['status_user'] == "Admin" )){ ?> 
            <li><div class='d-flex'><a href="administration.php"><img src="images/Services_48px.png" class="img-fluid rounded" width='20px;'/><span class='mx-2'>Parametre</span></a></div></li> 
          <?php } ?> 
        </ul> 
      </nav> 
      <!-- .navbar --> 

      <i class="mobile-nav-toggle mobile-nav-show bi bi-list"></i> 
      <i class="mobile-nav-toggle mobile-nav-hide d-none bi bi-x"></i>
 
    </div>
  </header><!-- End Header -->
  <!-- End Header -->

  <!-- ======= Hero Section ======= -->
  <section id="hero" class="hero">
    <div class="container position-relative">
      <div class="row gy-5" data-aos="fade-in">
        <div class="col-lg-6 order-2 order-lg-1 d-flex flex-column justify-content-center text-center text-lg-start">
          <h2>Bienvenue au <span>secteur HORE-FELLO</span></h2>
          <p>
            Le secteur Horé-fello est situé dans le district de Hackoudé-mitty et Kokoulo.
            Dans la sous-prefecture de Brouwal-Tappé, Prefecture de Pita, <br>République de Guinée. 
            <img src="images/drapeau.png" class="img-fluid rounded" width='40px;' alt="">
          </p>
          <?php if(isset($_SESSION['id_user'])){ ?> 
            <div class="d-flex justify-content-center justify-content-lg-start"> 
              <a href="detail_secteur.php" class="btn-get-started">En savoir plus ...</a>
            </div> 
          <?php } ?> 
        </div> 
        <div class="col-lg-6 order-1 order-lg-2">
          <img src="images/20220102_171822.jpg" class="img-fluid rounded" alt="" data-aos="zoom-out" data-aos-delay="100">
        </div>
      </div> 
    </div>

    <div class="icon-boxes position-relative">
      <div class="container position-relative">
        <div class="row gy-4 mt-5">

          <?php if(isset($_SESSION['id_user'])){ ?> 
          <div class="col-xl-3 col-md-6" data-aos="fade-up" data-aos-delay="100">
            <div class="icon-box">
              <div class="icon"><img src="images/cap_grill.png" class="img-fluid rounded" data-aos="zoom-out" data-aos-delay="100"/></div>
              <h4 class="title"><a href="detail_projet_cout.php" class="stretched-link">Coût total du Projet des Grillages.</a></h4>
            </div>
          </div>
          <?php } ?> 
          <!--End Icon Box -->

          <?php if(isset($_SESSION['id_user'])){ ?> 
          <div class="col-xl-3 col-md-6" data-aos="fade-up" data-aos-delay="200">
            <div class="icon-box">
              <div class="icon"><img src="images/cap_grill.png" class="img-fluid rounded" data-aos="zoom-out" data-aos-delay="100" /></div>
              <h4 class="title"><a href="detail_projet_mesure.php" class="stretched-link">Les mesures effectuées depuis, décembre 2020</a></h4>
            </div>
          </div> 
          <?php } ?> 
          <!--End Icon Box --> 

          <?php if(isset($_SESSION['id_user'])){ ?> 
          <div class="col-xl-3 col-md-6" data-aos="fade-up" data-aos-delay="300">
            <div class="icon-box"> 
              <div class="icon"><img src="images/cap_grill.png" class="img-fluid rounded" data-aos="zoom-out" data-aos-delay="100"/></div>
              <h4 class="title"><a href="detail_projet_plan.php" class="stretched-link">Le plan d'execution du projet aucours de l'année.</a></h4>
            </div>
          </div> 
          <?php } ?> 
          <!--End Icon Box -->

          <?php if(isset($_SESSION['id_user'])){ ?> 
          <div class="col-xl-3 col-md-6" data-aos="fade-up" data-aos-delay="500">
            <div class="icon-box">
              <div class="icon"><img src="images/cap_grill.png" class="img-fluid rounded" data-aos="zoom-out" data-aos-delay="100"/></div>
              <h4 class="title"><a href="detail_projet_modalité.php" class="stretched-link">Les modalités de versements pour les Grillages.</a></h4> 
            </div> 
          </div> 
          <?php } ?> 
          <!--End Icon Box --> 

        </div>
      </div>
    </div>

    </div>
  </section>
  <!-- End Hero Section -->

  <main id="main">
    <!-- ======= About Us Section ======= -->
    <section id="states" class="about"> 
      <div class="container" data-aos="fade-up">

        <div class="section-header">
          <h5><strong> Les derniers versements des PARENTS </strong></h5> 
          <p>Nous avons, plus de <strong><?= $donnees_count_par['nombre_count_par'] ?></strong> versements. Dont en voici les 5 derniers. </p>
        </div> 

        <div class="row gy-2"> 
          <table class="table table-striped px-0 mx-0"> 
            <?php foreach($affiche_vers as $ap){ ?> 
            <tr>
              <!-- <div class='card px-0 mx-0'> -->
                <div class='container card-body py-1 my-1 border rounded' style='background: #008374; color: white;'>  
                  <div class='card-body'> 
                    <div class='row mx-1'> 
                      <div class='col-12 col-md-5'>
                        <em class='h6'><span class=''><strong><?= $ap['prenom_par'].' '.$ap['nom_par'] ?></strong></em>
                      </div> 
                      <div class='col-5 col-md-3'> 
                        <img src="images/home.png" width='15px;' class='rounded '/> 
                        <em class='' style='font-size : 11px;'> <?= ' '.$ap['nom_maison'] ?></em> 
                      </div> 
                      <div class='col-4 col-md-2 border-white'>  
                        <em style='font-size : 11px;'>
                          <?= strftime('%d/%m/%Y', strtotime($ap['date_vers'])); ?> 
                        </em> 
                      </div> 
                      <div class='col-3 col-md-2 border-left'> 
                        <span class=""><em style='font-size : 11px;'><?= number_format($ap['montant_vers'], 0, " ", " "); ?> GNF</em></span> 
                      </div> 
                    </div> 
                  </div> 
                </div> 
              <!-- </div>  --> 
            </tr>
            <?php } ?> 
          </table>
        </div>

        <hr>

        <div class="section-header">
          <h5><strong> Les derniers versements des FEMMES </strong></h5> 
          <p>Nous avons, plus de <strong><?= $donnees_count_fem['nombre_count_fem'] ?></strong> versements. Dont en voici les 5 derniers. </p>
        </div> 

        <div class="row gy-2"> 
          <table class="table table-striped px-0 mx-0"> 
            <?php foreach($affiche_vers_fem as $ap){ ?> 
            <tr>
              <!-- <div class='card px-0 mx-0'> -->
                <div class='container card-body py-1 my-1 border rounded' style='background: #008374; color: white;'>  
                  <div class='card-body'> 
                    <div class='row mx-1'> 
                      <div class='col-12 col-md-5'>
                        <em class='h6'><span class=''><strong><?= $ap['prenom_fem'].' '.$ap['nom_fem'] ?></strong></em>
                      </div> 
                      <div class='col-5 col-md-3'> 
                        <img src="images/connect.png" width='15px;' class='rounded'/> 
                        <em class='' style='font-size : 11px;'> <?= ' '.$ap['prenom_par'].' '.$ap['nom_par'] ?></em> 
                      </div> 
                      <div class='col-4 col-md-2 border-white'>  
                        <em style='font-size : 11px;'>
                          <?= strftime('%d/%m/%Y', strtotime($ap['date_vers'])); ?> 
                        </em> 
                      </div> 
                      <div class='col-3 col-md-2 border-left'> 
                        <span class=""><em style='font-size : 11px;'><?= number_format($ap['montant_vers'], 0, " ", " "); ?> GNF</em></span> 
                      </div> 
                    </div> 
                  </div> 
                </div> 
              <!-- </div>  --> 
            </tr>
            <?php } ?> 
          </table>
        </div>

        <hr>

        <div class="section-header">
          <h5><strong> Les derniers versements des JEUNES </strong></h5> 
          <p>Nous avons, plus de <strong><?= $donnees_count_jeune['nombre_count_jeune'] ?></strong> versements. Dont en voici les 5 derniers. </p>
        </div> 

        <div class="row gy-2"> 
          <table class="table table-striped px-0 mx-0"> 
            <?php foreach($affiche_vers_jeune as $ap){ ?> 
            <tr>
              <!-- <div class='card px-0 mx-0'> -->
                <div class='container card-body py-1 my-1 border rounded' style='background: #008374; color: white;'>  
                  <div class='card-body'> 
                    <div class='row mx-1'> 
                      <div class='col-12 col-md-5'>
                        <em class='h6'><span class=''><strong><?= $ap['prenom_jeune'].' '.$ap['nom_jeune'] ?></strong></em>
                      </div> 
                      <div class='col-5 col-md-3'> 
                        <i>
                          <img src="images/localit5.jpg" width='18px;' class='rounded'/> 
                          <em class='' style='font-size : 11px;'> <?= $ap['pays_ville_jeune'] ?></em> 
                        </i>
                      </div> 
                      <div class='col-4 col-md-2 border-white'>  
                        <em style='font-size : 11px;'>
                          <?= strftime('%d/%m/%Y', strtotime($ap['date_vers'])); ?> 
                        </em> 
                      </div> 
                      <div class='col-3 col-md-2 border-left'> 
                        <span class=""><em style='font-size : 11px;'><?= number_format($ap['montant_vers'], 0, " ", " "); ?> GNF</em></span> 
                      </div> 
                    </div> 
                  </div> 
                </div> 
              <!-- </div>  --> 
            </tr>
            <?php } ?> 
          </table>
        </div>

        <!-- ======= Stats Counter Section ======= --> 
        <?php if(isset($_SESSION['id_user']) and ($_SESSION['status_user'] != 'Utilisateur' ) ){ ?> 
        <section id="stats-counter" class="stats-counter"> 
          <div class="container d-flex justify-content-center border border-success rounded" style='color: #008374;' data-aos="fade-up"> 
            <a href='vue_versements.php' class='text-'>voir tous les versements... </a> 
          </div> 
        </section><!-- End Stats Counter Section --> 
        <?php } ?> 
      </div> 
    </section>

    <!-- ======= About Us Section ======= -->
    <section id="about" class="about"> 
      <div class="container" data-aos="fade-up">

        <div class="section-header">
          <h2><img src="images/home.png" class="img-fluid rounded border rounded-circle" data-aos="zoom-out" data-aos-delay="100" width='50px;'/> LES MAISONS</h2>
          <p>Nous avons, plus de <strong><?= $donnees_mais['nombre_mais'] ?></strong> maisons. Dont en voici les 5 premières. </p>
        </div> 

        <div class="row gy-2"> 
          <table class="table table-striped px-0 mx-0">
            <?php foreach($affiche_maisons as $ap){ ?> 
            <tr>
              <!-- <div class='card px-0 mx-0'> -->
                <!-- <div class='container card-body bg- text-white py-1 my-1 border rounded' style='background-color: #008374;'>   -->
                <div class='container card-body bg-light py-1 my-1 border rounded' style='color: #008374;'>  
                  <span class='card-body'> 
                    <div class='row mx-1'> 
                      <div class='col-12 col-md-6'>
                        <em class='h6'>
                          <span class=''><?= $ap['id_maison'].' - ' ?>
                          <img src="images/home.png" width='15px;' class='rounded rounded-circle border'/> 
                          </span><strong><?= $ap['nom_maison'] ?></strong>
                        </em>
                      </div> 
                      <div class='col-6 col-md-3 '>  
                        <em style='font-size : 11px;'><i><img src="images/localit5.jpg" width='22px;' class=''/> <?= ' '.$ap['cloture_maison'] ?> </i></em> 
                      </div> 
                      <div class='col-6 col-md-3'> 
                        <em class='' style='font-size : 11px;'><i class='border rounded px-1 my-0 py-0 mx-1'> <?= ' '.$ap['membre'] ?> </i> membre(s)</em>
                      </div> 
                    </div> 
                  </span> 
                </div> 
              <!-- </div>  -->
            </tr>
            <?php } ?> 
          </table>
        </div>

        <!-- ======= Stats Counter Section ======= -->
        <?php if(isset($_SESSION['id_user']) and ($_SESSION['status_user'] != 'Utilisateur' ) ){ ?> 
        <section id="stats-counter" class="stats-counter">
          <div class="container d-flex justify-content-center rounded" style='background: #008374;' data-aos="fade-up"> 
            <a href='vue_maisons.php' class='text-white'>voir plus de MAISONS... </a>
          </div> 
        </section><!-- End Stats Counter Section -->
        <?php } ?> 
      </div>
    </section>

    <!-- ======= Our Services Section ======= -->
    <section id="services" class="services sections-bg">
      <div class="container" data-aos="fade-up">
        <div class="section-header"> 
          <h2><img src="images/Conference Call_50px.png" class="img-fluid border rounded mx-2" data-aos="zoom-out" data-aos-delay="100" width='50px;'/>LES PARENTS</h2> 
          <p>Nous avons, plus de <strong><?= $donnees_par['nombre_par'] ?></strong> parents. Dont en voici les 10 premiers.</p>
        </div>
        <div class="row gy-2"> 
          <table class="table table-striped px-0 mx-0"> 
            <?php foreach($affiche_parent as $ap){ ?> 
            <tr>
              <!-- <div class='card px-0 mx-0'> -->
                <div class='container card-body bg-white py-1 my-1 border rounded' style='color: #008374;'>  
                  <span class='card-body'> 
                    <div class='row mx-1'> 
                      <div class='col-12 col-md-6'>
                        <em class='h6'><span class=''><?= $ap['id_par'] ?></span><strong><?= ' - '.$ap['prenom_par'].' '.$ap['nom_par'] ?></strong></em>
                      </div> 
                      <!-- <div class=' col-md-3 d-none col-md-3 d-md-block'>   --> 
                      <div class='col-4 col-md-3'>  
                        <em style='font-size : 11px;'>
                          <i><img src="images/localit2.jpg" width='15px;' class=''/>
                            <?php 
                              if($ap['pays_ville_par'] == 'Senegal'){ 
                                echo 'au'; 
                              }elseif($ap['pays_ville_par'] == 'Sierra-leone' or $ap['pays_ville_par'] == 'Gambie'){
                                echo 'en'; 
                              }else{
                                echo 'à';   
                              } 
                            ?>  
                            <?= ' '.$ap['pays_ville_par'] ?> 
                          </i> 
                        </em> 
                      </div> 
                      <div class='col-8 col-md-3'> 
                        <img src="images/home.png" width='15px;' class='rounded border'/> 
                        <em class='' style='font-size : 11px;'> <?= ' '.$ap['nom_maison'] ?></em> 
                      </div> 
                    </div> 
                  </span> 
                </div> 
              <!-- </div>  --> 
            </tr>
            <?php } ?> 
          </table>
        </div>

        <!-- ======= Stats Counter Section ======= -->
        <?php if(isset($_SESSION['id_user']) and ($_SESSION['status_user'] != 'Utilisateur' ) ){ ?> 
        <section id="stats-counter" class="stats-counter">
          <div class="container d-flex justify-content-center rounded" style='background: #008374; color: white;' data-aos="fade-up"> 
            <a href='vue_parents.php' class='text-white'>voir plus de PARENTS... </a>
          </div> 
        </section><!-- End Stats Counter Section -->
        <?php } ?> 

      </div>
    </section><!-- End Our Services Section -->

    <!-- ======= Testimonials Section ======= --> 
    <section id="testimonials" class="testimonials">
      <div class="container" data-aos="fade-up">

        <div class="section-header">
          <h2>LE BUREAU INTERNATIONAL DE HORE-FELLO sur <i class="bi bi-whatsapp"></i></h2>
          <p>
            Ce Bureau represente tous les ressortissants du secteur.<br>
            Il y'a environ cinq (5) Antennes, dont nous avons :
            <div class='text-left'>
              <div class=''>
                <span class='border border-success rounded mx-1 px-2'><img src='images/antennne.jpg' style='width: 18px;'/> Pita</span><br><br>
                <span class='border border-success rounded mx-1 px-2'><img src='images/antennne.jpg' style='width: 18px;'/> Conakry</span> 
                <span class='border border-success rounded mx-1 px-2'><img src='images/antennne.jpg' style='width: 18px;'/> Gambie</span> <br><br>
                <span class='border border-success rounded mx-1 px-2'><img src='images/antennne.jpg' style='width: 18px;'/> Sénégal</span>
                <span class='border border-success rounded mx-1 px-2'><img src='images/antennne.jpg' style='width: 18px;'/> Sierra-léone</span> 
              </div>
            </div> 
          </p>
        </div>

        <div class="slides-3 swiper" data-aos="fade-up" data-aos-delay="100">
          <div class="swiper-wrapper">

            <div class="swiper-slide"> 
              <div class="testimonial-wrap"> 
                <div class="testimonial-item">
                  <div class="d-flex align-items-center">
                    <img src="assets/img/testimonials/Bah1.jpg" class="testimonial-img flex-shrink-0 w-50" alt="">
                    <div>
                      <h3>M. Mamadou Bhoye BAH</h3>
                      <h4>Chargé à l'info &amp; Secretaire</h4>
                      <h4><img src="images/localit2.jpg" width='12px;' class=''/> à Conakry</h4>
                      <div class="stars">
                        <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                      </div> 
                    </div>
                  </div>
                  <p>
                    <i class="bi bi-quote quote-icon-left"></i>
                      M. Mamadou Bhye, cumule deux fonctions en même temps. IL gère la communication du Groupe.<br> 
                      Et, il est l'unique sécretaire de la platforme, qui gère toute la documentation et les archives.
                    <i class="bi bi-quote quote-icon-right"></i>
                  </p>
                </div>
              </div>
            </div>
            <!-- End testimonial item -->

            <div class="swiper-slide">
              <div class="testimonial-wrap">
                <div class="testimonial-item">
                  <div class="d-flex align-items-center">
                    <img src="assets/img/testimonials/Bah4.jpg" class="testimonial-img flex-shrink-0 w-50" alt="">
                    <div>
                      <h3>M. Mamadou Lamarana BARRY</h3>
                      <h4>Vice-president</h4>
                      <h4><img src="images/localit2.jpg" width='12px;' class=''/> à Conakry</h4>
                      <div class="stars">
                        <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                      </div>
                    </div>
                  </div>
                  <p>
                    <i class="bi bi-quote quote-icon-left"></i>
                      C'est lui, l'adjoint direct du President. En cas d'absence, il preside la réunion dans le groupe. 
                      Il est aussi, le président des jeunes de Horé-fello à Conakry.
                    <i class="bi bi-quote quote-icon-right"></i>
                  </p>
                </div>
              </div>
            </div><!-- End testimonial item -->

            <div class="swiper-slide">
              <div class="testimonial-wrap">
                <div class="testimonial-item">
                  <div class="d-flex align-items-center">
                    <img src="assets/img/testimonials/presi.jpg" class="testimonial-img flex-shrink-0 w-50" alt="">
                    <div>
                      <h3>M. Ousmane BAH</h3> 
                      <h4>Le Président</h4>
                      <h4><img src="images/localit2.jpg" width='12px;' class=''/> à Conakry</h4>
                      <div class="stars">
                        <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                      </div>
                    </div>
                  </div>
                  <p>
                    <i class="bi bi-quote quote-icon-left"></i>
                      C'est lui, le coordinateur du Groupe. Il coordonne toutes les activités du groupe. 
                      M. Bah est aussi le Président de tous les ressortissants du secteur Horé-fello.
                      Et Vice-président du district de Hackoudé-mitty. 
                    <i class="bi bi-quote quote-icon-right"></i>
                  </p>
                </div>
              </div>
            </div><!-- End testimonial item -->

            <div class="swiper-slide">
              <div class="testimonial-wrap">
                <div class="testimonial-item">
                  <div class="d-flex align-items-center">
                    <img src="assets/img/testimonials/vice_info2.jpg" class="testimonial-img flex-shrink-0 w-50" alt="">
                    <div>
                      <h3>M. Mamadou Bailo BAH</h3>
                      <h4>Adjoint Chargé à l'info</h4>
                      <h4><img src="images/localit2.jpg" width='12px;' class=''/> à Conakry</h4>
                      <div class="stars">
                        <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                      </div>
                    </div>
                  </div>
                  <p> 
                    <i class="bi bi-quote quote-icon-left"></i>
                      M. Mamadou Bailo remplace le chargé à l'information en cas de maladie ou d'absance. 
                      En commun d'accord avec le titulaire, qui est la personne de M. Mamadou Bhoye.
                    <i class="bi bi-quote quote-icon-right"></i>
                  </p>
                </div>
              </div>
            </div><!-- End testimonial item -->

            <div class="swiper-slide">
              <div class="testimonial-wrap">
                <div class="testimonial-item">
                  <div class="d-flex align-items-center">
                    <img src="assets/img/testimonials/presi_femme.png" class="testimonial-img flex-shrink-0 w-50" alt="">
                    <div> 
                      <h3>Mme Hadjiratou BARRY</h3>
                      <h4>Présidente des Femmes</h4>
                      <h4><img src="images/localit2.jpg" width='12px;' class=''/> en Conakry</h4>
                      <div class="stars">
                        <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                      </div>
                    </div>
                  </div>
                  <p>
                    <i class="bi bi-quote quote-icon-left"></i>
                      Mme Hadjiratou dirige toutes les femmes du secteur Horé-fello où qu'elles soient.
                      Elle est la première femme qui doit être exemplaire dans toutes les bonnes actions contribuants au developpement de Horé-fello. 
                    <i class="bi bi-quote quote-icon-right"></i>
                  </p>
                </div>
              </div>
            </div><!-- End testimonial item -->
            
            <div class="swiper-slide">
              <div class="testimonial-wrap">
                <div class="testimonial-item">
                  <div class="d-flex align-items-center">
                    <img src="assets/img/testimonials/bella_sene.jpg" class="testimonial-img flex-shrink-0 w-50" width='' alt="">
                    <div>
                      <h3>M. Amadou Bela BAH</h3> 
                      <h4>Conseillé</h4> 
                      <h4><img src="images/localit2.jpg" width='12px;' class=''/> au Sénégal</h4>
                      <div class="stars"> 
                        <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                      </div>
                    </div> 
                  </div> 
                  <p> 
                    <i class="bi bi-quote quote-icon-left"></i>
                      M. Amadou Bela est l'un des conseillés du secteur dans le groupe. Il reside au Sénégal, 
                      et est aussi le President de tous les ressortissants de Hackodé-mitty au Sénégal.
                    <i class="bi bi-quote quote-icon-right"></i>
                  </p>
                </div> 
              </div>
            </div><!-- End testimonial item -->
            
            <div class="swiper-slide">
              <div class="testimonial-wrap">
                <div class="testimonial-item">
                  <div class="d-flex align-items-center">
                    <img src="assets/img/testimonials/Oncle Adou Bella.png" class="testimonial-img flex-shrink-0 w-50" alt="">
                    <div>
                      <h3>M. Amadou Bela BARRY</h3> 
                      <h4>Président de l'antenne du Sénégal</h4> 
                      <h4><img src="images/localit2.jpg" width='12px;' class=''/> au Sénégal</h4>
                      <div class="stars"> 
                        <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                      </div>
                    </div> 
                  </div> 
                  <p> 
                    <i class="bi bi-quote quote-icon-left"></i>
                      M. Amadou Bela Barry est le Présdent de l'antenne du seteur Horé-fello au Sénégal dans le groupe. 
                      Il reside actuellement au Sénégal.
                      Il est le premier responsable des ressortissants de Horé-fello au Sénégal. 
                    <i class="bi bi-quote quote-icon-right"></i>
                  </p>
                </div> 
              </div> 
            </div><!-- End testimonial item -->
            
            <div class="swiper-slide">
              <div class="testimonial-wrap">
                <div class="testimonial-item">
                  <div class="d-flex align-items-center">
                    <img src="assets/img/testimonials/Oury_sen.jpg" class="testimonial-img flex-shrink-0 w-50" alt="">
                    <div>
                      <h3>M. Mamadou Oury BAH</h3> 
                      <h4>Vice-président de l'antenne du Sénégal</h4> 
                      <h4><img src="images/localit2.jpg" width='12px;' class=''/> au Sénégal</h4>
                      <div class="stars"> 
                        <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                      </div>
                    </div> 
                  </div> 
                  <p> 
                    <i class="bi bi-quote quote-icon-left"></i>
                      M. Mamadou Oury est le Vice-Présdent de l'antenne du seteur Horé-fello au Sénégal dans le groupe, 
                      et est aussi le Président de l'antenne de Hackoudé-mitty au Sénégal sur WhatsApp.
                      Il remplace le président à son absence.
                    <i class="bi bi-quote quote-icon-right"></i>
                  </p> 
                </div> 
              </div>
            </div><!-- End testimonial item -->

            <div class="swiper-slide">
              <div class="testimonial-wrap">
                <div class="testimonial-item">
                  <div class="d-flex align-items-center">
                    <img src="assets/img/testimonials/kouyaté.jpg" class="testimonial-img flex-shrink-0 w-50" alt="">
                    <div>
                      <h3>Mme. Aissatou Kouyaté</h3> 
                      <h4>Présidente des femmes de l'antenne du Sénégal</h4> 
                      <h4><img src="images/localit2.jpg" width='12px;' class=''/> au Sénégal</h4>
                      <div class="stars"> 
                        <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                      </div> 
                    </div> 
                  </div> 
                  <p> 
                    <i class="bi bi-quote quote-icon-left"></i> 
                      Mme. Aissatou est la presidente des femmes de Horé-fello au Sénégal. 
                      Elle supervise toutes les femmes, pour les cotisations du secteur Horé-fello. 
                    <i class="bi bi-quote quote-icon-right"></i> 
                  </p> 
                </div> 
              </div>
            </div><!-- End testimonial item -->

            <div class="swiper-slide">
              <div class="testimonial-wrap">
                <div class="testimonial-item">
                  <div class="d-flex align-items-center">
                    <img src="assets/img/testimonials/gambien.jpg" class="testimonial-img flex-shrink-0 w-50" alt="">
                    <div> 
                      <h3>M. Boubacar BAH</h3> 
                      <h4>Président de l'antenne de la Gambie</h4> 
                      <h4><img src="images/localit2.jpg" width='12px;' class=''/> en Gambie</h4>
                      <div class="stars"> 
                        <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                      </div> 
                    </div> 
                  </div> 
                  <p> 
                    <i class="bi bi-quote quote-icon-left"></i> 
                      M. Boubacar Bah est le Présdent de l'antenne du seteur Horé-fello en Gambie dans le groupe. 
                      Il reside actuellement en Gambie.
                      Il est le premier responsable des ressortissants de Horé-fello en Gambie.
                    <i class="bi bi-quote quote-icon-right"></i> 
                  </p> 
                </div> 
              </div>    
            </div><!-- End testimonial item -->
            
            <div class="swiper-slide">
              <div class="testimonial-wrap">
                <div class="testimonial-item">
                  <div class="d-flex align-items-center">
                    <img src="assets/img/testimonials/Ismaila_gam.jpg" class="testimonial-img flex-shrink-0 w-50" alt="">
                    <div>
                      <h3>M. Ismaila BAH</h3> 
                      <h4>Vice-président de l'antenne en Gambie</h4> 
                      <h4><img src="images/localit2.jpg" width='12px;' class=''/> en Gambie</h4>
                      <div class="stars"> 
                        <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                      </div>
                    </div> 
                  </div> 
                  <p> 
                    <i class="bi bi-quote quote-icon-left"></i> 
                      M. Ismaila est le Vice-Présdent de l'antenne du seteur Horé-fello en Gambie dans le groupe. 
                      Il remplace le président à son absence. 
                    <i class="bi bi-quote quote-icon-right"></i>
                  </p> 
                </div> 
              </div>
            </div><!-- End testimonial item -->

            <div class="swiper-slide">
              <div class="testimonial-wrap">
                <div class="testimonial-item">
                  <div class="d-flex align-items-center">
                    <img src="assets/img/testimonials/farba.png" class="testimonial-img flex-shrink-0 w-50" alt="">
                    <div> 
                      <h3>M. Amadou BAH</h3> 
                      <h4>Conseillé</h4> 
                      <h4><img src="images/localit2.jpg" width='12px;' class=''/> en Sierra-leone</h4>
                      <div class="stars"> 
                        <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                      </div> 
                    </div> 
                  </div> 
                  <p> 
                    <i class="bi bi-quote quote-icon-left"></i> 
                      M. Amadou Bah est l'un des conseillé du secteur Horé-fello dans le Groupe sur WhatsApp. 
                      Il reside actuellement en Sierra-leone. 
                      Il est le premier responsable des ressortissants de Horé-fello en Sierra-leone. 
                    <i class="bi bi-quote quote-icon-right"></i> 
                  </p> 
                </div> 
              </div>    
            </div><!-- End testimonial item --> 
            
            <div class="swiper-slide">
              <div class="testimonial-wrap">
                <div class="testimonial-item">
                  <div class="d-flex align-items-center">
                    <img src="assets/img/testimonials/Soule.png" class="testimonial-img flex-shrink-0 w-50" alt="">
                    <div> 
                      <h3>M. Souleymane BAH</h3>
                      <h4>Président de l'antenne de la Sierra-leone</h4> 
                      <h4><img src="images/localit2.jpg" width='12px;' class=''/> en Sierra-leone</h4>
                      <div class="stars"> 
                        <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                      </div> 
                    </div> 
                  </div> 
                  <p> 
                    <i class="bi bi-quote quote-icon-left"></i> 
                      M. Souleymane Bah est le Présdent de l'antenne du seteur Horé-fello en Sierra-leone dans le groupe. 
                      Il reside actuellement en Sierra-leone. 
                      Il est le deuxième responsable des ressortissants de Horé-fello en Sierra-leone. 
                    <i class="bi bi-quote quote-icon-right"></i> 
                  </p> 
                </div> 
              </div>    
            </div><!-- End testimonial item -->

            <div class="swiper-slide">
              <div class="testimonial-wrap">
                <div class="testimonial-item">
                  <div class="d-flex align-items-center">
                    <img src="assets/img/testimonials/Madjou.png" class="testimonial-img flex-shrink-0 w-50" alt="">
                    <div>
                      <h3>M. Thierno Madjou BAH</h3> 
                      <h4>Vice-président de l'antenne en Sierra-leone</h4> 
                      <h4><img src="images/localit2.jpg" width='12px;' class=''/> en Sierra-leone</h4>
                      <div class="stars"> 
                        <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                      </div>
                    </div> 
                  </div> 
                  <p> 
                    <i class="bi bi-quote quote-icon-left"></i> 
                      M. Thierno Madjou est le Vice-Présdent de l'antenne du seteur Horé-fello en Sierra-leone dans le groupe. 
                      Il remplace le président à son absence. 
                      Il est le troisième responsable des ressortissants de Horé-fello en Sierra-leone.
                    <i class="bi bi-quote quote-icon-right"></i> 
                  </p> 
                </div> 
              </div>
            </div><!-- End testimonial item -->

            <div class="swiper-slide">
              <div class="testimonial-wrap">
                <div class="testimonial-item">
                  <div class="d-flex align-items-center">
                    <img src="assets/img/testimonials/Oustaz.png" class="testimonial-img flex-shrink-0 w-50" alt="">
                    <div>
                      <h3>Oustaz Sidy BAH</h3> 
                      <h4>Conseillé</h4>  
                      <h4><img src="images/localit2.jpg" width='12px;' class=''/> à Pita</h4>
                      <div class="stars"> 
                        <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                      </div>
                    </div> 
                  </div> 
                  <p> 
                    <i class="bi bi-quote quote-icon-left"></i> 
                      Oustaz Sidy est l'un des conseillé du secteur Horé-fello dans le Groupe sur WhatsApp. 
                      Il est l'actuel Chef secteur de Horé-fello. 
                      Il est le premier responsable de Horé-fello dans la prefecture de Pita. 
                    <i class="bi bi-quote quote-icon-right"></i> 
                  </p> 
                </div> 
              </div>
            </div><!-- End testimonial item -->
            
            <div class="swiper-slide">
              <div class="testimonial-wrap">
                <div class="testimonial-item">
                  <div class="d-flex align-items-center">
                    <img src="assets/img/testimonials/lirwane.jpg" class="testimonial-img flex-shrink-0 w-50" alt="">
                    <div>
                      <h3>M. Amadou Lirwane BAH</h3> 
                      <h4>President de l'antenne du village.</h4>  
                      <h4><img src="images/localit2.jpg" width='12px;' class=''/> à Pita</h4> 
                      <div class="stars"> 
                        <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                      </div> 
                    </div>   
                  </div>    
                  <p> 
                    <i class="bi bi-quote quote-icon-left"></i> 
                      M. Amadou Lirwane, est le president de l'antenne du village dans le Groupe sur WhatsApp. 
                      Il est aussi l'actuel Président des Jeunes du secteur de Horé-fello au village. 
                      Il est le deuxieme responsable de Horé-fello dans la prefecture de Pita. 
                    <i class="bi bi-quote quote-icon-right"></i> 
                  </p> 
                </div> 
              </div>
            </div><!-- End testimonial item -->
            
            <div class="swiper-slide">
              <div class="testimonial-wrap">
                <div class="testimonial-item">
                  <div class="d-flex align-items-center">
                    <img src="assets/img/testimonials/nen_hassan1.jpg" class="testimonial-img flex-shrink-0 w-50" alt="">
                    <div>
                      <h3>Mme. Hassanatou BAH</h3>  
                      <h4>Présidente des femmes de l'antenne du Village</h4> 
                      <h4><img src="images/localit2.jpg" width='12px;' class=''/> à Pita</h4> 
                      <div class="stars"> 
                        <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                      </div> 
                    </div> 
                  </div> 
                  <p> 
                    <i class="bi bi-quote quote-icon-left"></i> 
                      Mme. Hassanatou est la presidente des femmes de Horé-fello au Village. 
                      Elle supervise toutes les femmes, pour les cotisations du secteur Horé-fello. 
                    <i class="bi bi-quote quote-icon-right"></i> 
                  </p> 
                </div> 
              </div>
            </div><!-- End testimonial item -->

          </div>
          <div class="swiper-pagination"></div>
        </div>

      </div>
    </section><!-- End Testimonials Section -->

    <!-- ======= Portfolio Section ======= -->
    <section id="portfolio" class="portfolio sections-bg">
      <div class="container" data-aos="fade-up">

        <div class="section-header">
          <h2><img src="images/images (3).png" class="img-fluid border rounded mx-2 px-1" data-aos="zoom-out" data-aos-delay="100" width='60px;'/>LES FEMMES</h2>
          <p>Nous avons, plus de <strong><?= $donnees_fem['nombre_fem'] ?></strong> femmes. Dont en voici les 7 premières.</p>
        </div>

        <div class="row gy-2 "> 
          <table class="table table-striped px-0 mx-0"> 
            <?php foreach($affiche_femme as $ap){ ?> 
            <tr> 
              <!-- <div class='card px-0 mx-0'> -->
                <div class='container card-body bg-white py-1 my-1 border rounded' style='color: #008374;'>  
                  <span class='card-body'> 
                    <div class='row mx-1'> 
                      <div class='col-9 col-md-6'>
                        <em class='h6'><span class=''><?= $ap['id_fem'] ?></span><strong><?= ' - '.$ap['prenom_fem'].' '.$ap['nom_fem'] ?></strong></em>
                      </div> 
                      <!-- <div class=' col-md-3 d-none col-md-3 d-md-block'>   --> 
                      <div class='col-3 col-md-2'>  
                        <em style='font-size : 10px;'>
                          <i><img src="images/localit2.jpg" width='10px;' class=''/> 
                            <?php 
                              if($ap['pays_ville_fem'] == 'Senegal'){ 
                                echo 'au'; 
                              }elseif($ap['pays_ville_fem'] == 'Sierra-leone' or $ap['pays_ville_fem'] == 'Gambie'){
                                echo 'en'; 
                              }else{ 
                                echo 'à';
                              }
                            ?>  
                            <?= ' '.$ap['pays_ville_fem'] ?> 
                          </i> 
                        </em> 
                      </div> 
                      <div class='col-6 col-md-2'> 
                        <img src="images/Contacts_48px.png" width='14px;' class=''/>  
                        <em class='' style='font-size : 11px;'> <?= ' '.$ap['prenom_par'].' '.$ap['nom_par'] ?></em> 
                      </div> 
                      <div class='col-6 col-md-2'> 
                        <img src="images/home.png" width='14px;' class='rounded border'/> 
                        <em class='' style='font-size : 11px;'> <?= ' '.$ap['nom_maison'] ?></em> 
                      </div> 
                    </div> 
                  </span> 
                </div> 
              <!-- </div>  --> 
            </tr>
            <?php } ?> 
          </table>
        </div>

        <!-- ======= Stats Counter Section ======= -->
        <?php if(isset($_SESSION['id_user']) and ($_SESSION['status_user'] != 'Utilisateur' ) ){ ?> 
        <section id="stats-counter" class="stats-counter pb-4">
          <div class="container d-flex justify-content-center" style='background: #008374;' data-aos="fade-up"> 
            <a href='vue_femmes.php' class='text-white'>voir plus de FEMMES... </a>
          </div> 
        </section><!-- End Stats Counter Section -->
        <?php } ?> 

        <div class="portfolio-isotope" data-portfolio-filter="*" data-portfolio-layout="masonry" data-portfolio-sort="original-order" data-aos="fade-up" data-aos-delay="100">

          <div>
            <ul class="portfolio-flters">
              <li data-filter="*" class="filter-active">Toutes</li>
              <li data-filter=".filter-app">Grillage</li>
              <li data-filter=".filter-product">Forage</li> 
              <li data-filter=".filter-branding">Jeune</li> 
              <li data-filter=".filter-books">Ceremonie</li> 
            </ul><!-- End Portfolio Filters -->
          </div>

          <div class="row gy-4 portfolio-container">

            <div class="col-xl-4 col-md-6 portfolio-item filter-app">
              <div class="portfolio-wrap">
                <a href="assets/img/portfolio/grilag1.jpg" data-gallery="portfolio-gallery-app" class="glightbox"><img src="assets/img/portfolio/grilag1.jpg" class="img-fluid" alt=""></a>
                <div class="portfolio-info">
                  <a href="portfolio-details.php" title="More Details"><h4>Grillage</h4>
                  <p>Le model de grillage que nous voulons</p></a>
                </div>
              </div>
            </div><!-- End Portfolio Item -->

            <div class="col-xl-4 col-md-6 portfolio-item filter-product">
              <div class="portfolio-wrap">
                <a href="assets/img/portfolio/forage1.jpg" data-gallery="portfolio-gallery-app" class="glightbox"><img src="assets/img/portfolio/forage1.jpg" class="img-fluid" alt=""></a>
                <div class="portfolio-info">
                  <a href="portfolio-details.php" title="More Details"><h4>Forage</h4>
                  <p>Reneauver le 22/08/2019</p></a>
                </div>
              </div>
            </div><!-- End Portfolio Item -->

            <div class="col-xl-4 col-md-6 portfolio-item filter-branding">
              <div class="portfolio-wrap">
                <a href="assets/img/portfolio/jeune1.jpg" data-gallery="portfolio-gallery-app" class="glightbox"><img src="assets/img/portfolio/jeune1.jpg" class="img-fluid" alt=""></a>
                <div class="portfolio-info">
                  <a href="portfolio-details.php" title="More Details"><h4>Jeunes</h4>
                  <p>Le match de Gala du 02/01/2022.</p></a>
                </div>
              </div> 
            </div><!-- End Portfolio Item -->

            <div class="col-xl-4 col-md-6 portfolio-item filter-books">
              <div class="portfolio-wrap">
                <a href="assets/img/portfolio/ceremo1.jpg" data-gallery="portfolio-gallery-app" class="glightbox"><img src="assets/img/portfolio/ceremo1.jpg" class="img-fluid" alt=""></a>
                <div class="portfolio-info">
                  <a href="portfolio-details.php" title="More Details"><h4>Ceremonie</h4>
                  <p>Le mariage de Mamadou Saliou cimenterie le 17/07/2022</p></a>
                </div>
              </div>
            </div><!-- End Portfolio Item -->

            <div class="col-xl-4 col-md-6 portfolio-item filter-app">
              <div class="portfolio-wrap">
                <a href="assets/img/portfolio/grilag2.jpg" data-gallery="portfolio-gallery-app" class="glightbox"><img src="assets/img/portfolio/grilag2.jpg" class="img-fluid" alt=""></a>
                <div class="portfolio-info">
                  <a href="portfolio-details.php" title="More Details"><h4>Grillage</h4>
                  <p>Le model de grillage que nous voulons</p></a>
                </div> 
              </div>
            </div><!-- End Portfolio Item -->

            <div class="col-xl-4 col-md-6 portfolio-item filter-product">
              <div class="portfolio-wrap">
                <a href="assets/img/portfolio/forage2.jpg" data-gallery="portfolio-gallery-app" class="glightbox"><img src="assets/img/portfolio/forage2.jpg" class="img-fluid" alt=""></a>
                <div class="portfolio-info">
                  <a href="portfolio-details.php" title="More Details"><h4>Forage</h4>
                  <p>Renauver le 22/08/2019</p></a>
                </div>
              </div>
            </div><!-- End Portfolio Item -->

            <div class="col-xl-4 col-md-6 portfolio-item filter-branding">
              <div class="portfolio-wrap">
                <a href="assets/img/portfolio/jeune2.jpg" data-gallery="portfolio-gallery-app" class="glightbox"><img src="assets/img/portfolio/jeune2.jpg" class="img-fluid" alt=""></a>
                <div class="portfolio-info">
                  <a href="portfolio-details.php" title="More Details"><h4>Jeunes</h4>
                  <p>Le bapteme de Mr Ibrahima Sory en 2019</p></a>
                </div>
              </div>
            </div><!-- End Portfolio Item -->

            <div class="col-xl-4 col-md-6 portfolio-item filter-books">
              <div class="portfolio-wrap">
                <a href="assets/img/portfolio/ceremo11.jpg" data-gallery="portfolio-gallery-app" class="glightbox"><img src="assets/img/portfolio/ceremo11.jpg" class="img-fluid" alt=""></a>
                <div class="portfolio-info">
                  <a href="portfolio-details.php" title="More Details"><h4>Ceremonie</h4>
                  <p>Bapteme d'Aissatou Bah (Campagnes) le 01/01/2020</p></a>
                </div> 
              </div>
            </div><!-- End Portfolio Item -->

            <div class="col-xl-4 col-md-6 portfolio-item filter-app">
              <div class="portfolio-wrap">
                <a href="assets/img/portfolio/grilag3.jpg" data-gallery="portfolio-gallery-app" class="glightbox"><img src="assets/img/portfolio/grilag3.jpg" class="img-fluid" alt=""></a>
                <div class="portfolio-info">
                  <a href="portfolio-details.php" title="More Details"><h4>Grillage</h4>
                  <p>Le model de grillage que nous voulons</p></a>
                </div>
              </div>
            </div><!-- End Portfolio Item -->

            <div class="col-xl-4 col-md-6 portfolio-item filter-product">
              <div class="portfolio-wrap">
                <a href="assets/img/portfolio/forage3.jpg" data-gallery="portfolio-gallery-app" class="glightbox"><img src="assets/img/portfolio/forage3.jpg" class="img-fluid" alt=""></a>
                <div class="portfolio-info">
                  <a href="portfolio-details.php" title="More Details"><h4>Forage</h4>
                  <p>Renauver le 22/08/2019</p></a>
                </div>
              </div>
            </div><!-- End Portfolio Item -->

            <div class="col-xl-4 col-md-6 portfolio-item filter-branding">
              <div class="portfolio-wrap">  
                <a href="assets/img/portfolio/jeune14.jpg" data-gallery="portfolio-gallery-app" class="glightbox"><img src="assets/img/portfolio/jeune14.jpg" class="img-fluid" alt=""></a>
                <div class="portfolio-info">
                  <a href="portfolio-details.php" title="More Details"><h4>Jeunes</h4>
                  <p>Bapteme de Mdou Lamarana (les campagnes) en 02/02/2020</p></a>
                </div>
              </div>
            </div><!-- End Portfolio Item -->

            <div class="col-xl-4 col-md-6 portfolio-item filter-books">
              <div class="portfolio-wrap">
                <a href="assets/img/portfolio/ceremo3.jpg" data-gallery="portfolio-gallery-app" class="glightbox"><img src="assets/img/portfolio/ceremo3.jpg" class="img-fluid" alt=""></a>
                <div class="portfolio-info">
                  <a href="portfolio-details.php" title="More Details"><h4>Ceremonie</h4> 
                  <p>Bapteme de Oumou Salamata le 06/09/2022</p></a>
                </div>
              </div>
            </div><!-- End Portfolio Item -->

          </div><!-- End Portfolio Container -->

        </div>

      </div>
    </section><!-- End Portfolio Section -->

    <!-- ======= Our Team Section ======= -->
    <section id="team" class="team">
      <div class="container" data-aos="fade-up">

        <div class="section-header">
          <h2>LES JEUNES</h2> 
          <p>Nous avons, plus de <strong><?= $donnees_jeune['nombre_jeune'] ?></strong> maisons. Dont en voici les 10 premiers.</p>
        </div>

        <div class="row gy-4">
          <table class="table table-striped px-0 mx-0"> 
            <?php foreach($affiche_jeune as $ap){ ?> 
            <tr>
              <!-- <div class='card px-0 mx-0'> -->
                <div class='container card-body py-1 my-1 border rounded' style='color: #008374;'>  
                  <span class='card-body'> 
                    <div class='row mx-1'> 
                      <div class='col-12 col-md-5'> 
                        <em class='h6'><span class=''><?= $ap['id_jeune'] ?></span><strong><?= ' - '.$ap['prenom_jeune'].' '.$ap['nom_jeune'] ?></strong></em>
                      </div> 
                      <!-- <div class=' col-md-3 d-none col-md-3 d-md-block'>   --> 
                      <div class='col-3 col-md-2'>  
                        <em style='font-size : 11px;'>
                          <i><img src="images/localit2.jpg" width='11px;' class=''/> 
                            <?= ' '.$ap['pays_ville_jeune'] ?> 
                          </i> 
                        </em> 
                      </div> 
                      <div class='col-4 col-md-2'> 
                        <em class='' style='font-size : 11px;'> <?= ' '.$ap['situation_matri_jeune'] ?></em> 
                      </div> 
                      <div class='col-5 col-md-3'> 
                        <!-- <img src="images/home.png" width='15px;' class='rounded border'/>  -->
                        <em class='' style='font-size : 11px;'> <?= ' '.$ap['profession_jeune'] ?></em> 
                      </div> 
                    </div> 
                  </span> 
                </div> 
              <!-- </div>  --> 
            </tr>
            <?php } ?> 
          </table>

          <!-- ======= Stats Counter Section ======= -->
          <?php if(isset($_SESSION['id_user']) and ($_SESSION['status_user'] != 'Utilisateur' ) ){ ?> 
          <section id="stats-counter" class="stats-counter">
            <div class="container d-flex justify-content-center" style='background: #008374;' data-aos="fade-up"> 
              <a href='vue_jeunes.php' class='text-white'>voir plus de JEUNES... </a>
            </div> 
          </section><!-- End Stats Counter Section -->
          <?php } ?> 
        </div>

      </div>
    </section><!-- End Our Team Section -->

    <!-- ======= Frequently Asked Questions Section ======= -->
    <section id="faq" class="faq">
      <div class="container" data-aos="fade-up">

        <div class="row gy-4">

          <div class="col-lg-4">
            <div class="content px-xl-5">
              <h3>Des <strong>questions</strong> qu'on se pose souvent. </h3>
              <p>
                En voici, quelques principaux questions pré-selectionnées pour éclaircir quelques interrogations. 
              </p>
            </div>
          </div>

          <div class="col-lg-8">

            <div class="accordion accordion-flush" id="faqlist" data-aos="fade-up" data-aos-delay="100">

              <div class="accordion-item">
                <h3 class="accordion-header">
                  <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq-content-1">
                    <span class="num">1.</span>
                      Pourquoi ce site ?
                  </button>
                </h3>
                <div id="faq-content-1" class="accordion-collapse collapse" data-bs-parent="#faqlist">
                  <div class="accordion-body">
                    Ce site est déployé par le secteur HORE-FELLO, pour <strong class='text-success'>LA GESTION DES GRILLAGES</strong>.
                    L'objectif, c'est pour clôturer le secteur dans le cadre du développement du district de Hackoudé-mitty. 
                  </div>
                </div>
              </div><!-- # Faq item-->

              <div class="accordion-item">
                <h3 class="accordion-header">
                  <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq-content-2">
                    <span class="num">2.</span>
                      Pourquoi ces listes de Maisons, Parents, Femmes, et Jeunes ?
                  </button> 
                </h3> 
                <div id="faq-content-2" class="accordion-collapse collapse" data-bs-parent="#faqlist">
                  <div class="accordion-body">
                    Toutes ces listes, permettent de trier les differentes recensements effectués dans le secteur. 
                    Le tableau principal est celui <strong class='text-success'>des maisons</strong>. Parce que tous 
                    <strong class='text-success'>Parents</strong> 
                    est issue d'une maisons et toutes <strong class='text-success'>femmes</strong> a
                    potentiellement un mari (sauf s'elle est mariée à l'etranger c'est-à-dire hors Horé-fello).<br>
                    Par contre, <strong class='text-success'>les Jeunes</strong> sont recensés à part. 
                  </div> 
                </div> 
              </div><!-- # Faq item-->

              <div class="accordion-item">
                <h3 class="accordion-header">
                  <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq-content-3">
                    <span class="num">3.</span>
                      Qui a developper ce site ?
                  </button>
                </h3>
                <div id="faq-content-3" class="accordion-collapse collapse" data-bs-parent="#faqlist">
                  <div class="accordion-body">
                      <div class="contenair-fluid">
                        <div class="row">
                          <div class="col-12 col-md-4"> 
                            <img src="images/5..jpg" width='' class='img-fluid rounded'/> 
                          </div>
                          <div class="col-12 col-md-8">
                            Ce site est développé par le jeune <strong class='text-success'>MAMADOU BHOYE BAH </strong>. 
                            <strong class='text-success'>Ingérieur Information </strong> de profession.<br>
                            President des jeunes du secteur Horé-fello, de Mai <strong class='text-success'>2016</strong> jusqu'en Janvier <strong class='text-success'>2019</strong>. 
                          </div>
                        </div>
                      </div>
                  </div>
                </div>
              </div><!-- # Faq item-->

            </div>

          </div>
        </div>

      </div>
    </section><!-- End Frequently Asked Questions Section -->

    <!-- ======= Contact Section ======= -->
    <section id="contact" class="contact">
      <div class="container" data-aos="fade-up">

        <div class="section-header">
          <h2>Commentaire</h2>
          <p>Voulez-vous nous contacter où ajouter un commentaire.<br> Voici les cinq derniers </p>
        </div>

        <div class="row gy-4">
          <table class="table table-striped px-0 mx-0"> 
            <?php foreach($affiche_comment as $ap){ ?> 
            <tr>
              <div class='container card-body py-1 my-1 border rounded'>  
                <span class='card-body'> 
                  <div class='row mx-1'> 
                    <div class='col-2 col-md-1 py-0'>
                      <p> 
                        <?php if($ap['image_user']){ ?>
                            <img src="images_profil/<?php echo $ap['image_user']; ?>" width='30px;' class='rounded img-fluid' /> 
                        <?php } ?> 
                      </p> 
                    </div> 
                    <div class='col-7 col-md-3 py-0 text-muted'> 
                      <em class='h6'><?= $ap['prenom_user'].' '.$ap['nom_user'] ?></em>
                    </div> 
                    <div class='col-3 col-md-2 py-0 text-muted'> 
                      <em class='h6' style='font-size: 10px;'>
                        <img src="images/localit2.jpg" width='11px;' class='img-fluid' /> 
                        <?= $ap['pays_ville_user'] ?>
                      </em>
                    </div> 
                    <div class='col-3 col-md-2 py-0'> 
                      <em class='text-muted' style='font-size: 11px;'><?= strftime('%d/%m/%Y', strtotime($ap['date_com'])); ?></em> 
                    </div> 
                    <div class='col-9 col-md-4 py-0' style='color: #008374;'> 
                      <p> 
                        <i class="bi bi-quote quote-icon-left"></i> 
                        <em class=''> <?= ' '.$ap['comment_com'] ?></em> 
                        <i class="bi bi-quote quote-icon-right"></i> 
                      </p>
                    </div> 
                  </div> 
                </span> 
              </div> 
            </tr>
            <?php } ?> 
          </table>
          <!-- ======= Stats Counter Section ======= -->
        </div>


        <?php if(isset($_SESSION['id_user'])){ ?> 
        <div class="row gx-lg-0 gy-4">
          <form action="index.php" method="POST" role="form" class="">
            <div class='row'> 
              <div class="col-12 col-md-8">
                <div class="form-group">
                  <textarea class="form-control" name="comment_com" rows="3" placeholder="Votre commentaire !" required></textarea>
                </div>
              </div>
              <div class="col-12 col-md-4">
                <div class="text-center mt-4"><button class='btn text-white' style='background: #008374;' type="submit" name='comment_envoie'>Commenter</button></div>
              </div> <!-- End Contact Form -->
            </div>
          </form> 
        </div> 
        <?php } ?> 
      </div>
    </section><!-- End Contact Section -->

  </main><!-- End #main -->

  <!-- ======= Footer ======= -->
  <?php include("footer_final.php"); ?>
  <!-- End Footer -->

  <a href="#" class="scroll-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <div id="preloader"></div>

  <!-- Vendor JS Files -->
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/aos/aos.js"></script>
  <script src="assets/vendor/glightbox/js/glightbox.min.js"></script>
  <script src="assets/vendor/purecounter/purecounter_vanilla.js"></script>
  <script src="assets/vendor/swiper/swiper-bundle.min.js"></script>
  <script src="assets/vendor/isotope-layout/isotope.pkgd.min.js"></script>
  <script src="assets/vendor/php-email-form/validate.js"></script>

  <!-- Template Main JS File -->
  <script src="assets/js/main.js"></script>


                <?php } $reponse_count_jeune->closeCursor(); ?> 
              <?php } $reponse_count_fem->closeCursor(); ?>         
            <?php } $reponse_count_par->closeCursor(); ?> 
          <?php } $reponse_fem->closeCursor(); ?> 
        <?php } $reponse_jeune->closeCursor(); ?> 
      <?php } $reponse_jeune->closeCursor(); ?> 
    <?php } $reponse_mais->closeCursor(); ?> 
  <?php // } $reponse_user->closeCursor(); ?> 

</body>
</html>