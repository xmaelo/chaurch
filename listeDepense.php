

<?php
    session_start();
    if(isset($_SESSION['login'])){
       $idUser = $_SESSION['login'];
       $annee = $_SESSION['annee'];
        //require_once('layout.php');
       require_once('includes/connexionbd.php');
       require_once('includes/function_role.php');


        if(!has_Droit($idUser, "Liste des depenses")){
            header('Location:index.php');
        }else{

           
            //nombre de fidele a afficher par page
            $nbeParPage=100;
            //total de fideles enregistrés
            $total = 0;

            //selection du nombre de fideles enregistrés
            $selectNombreFidele = $db->prepare("SELECT COUNT(id) AS nbretotalfidele FROM depenses WHERE lisible=1");
            $selectNombreFidele->execute();
            
            while($idselectNombreFidele=$selectNombreFidele->fetch(PDO::FETCH_OBJ)){
                $total = $idselectNombreFidele->nbretotalfidele;
            }

            //calcul du nombre de pages
            $nbDePage = ceil($total/$nbeParPage);

            //navigation dans le paginator
            if(isset($_GET['p']) && !empty($_GET['p']) && ctype_digit($_GET['p'])==1){
                if($_GET['p'] > $nbDePage){
                    $pageCourante = $nbDePage;
                }else{
                    $pageCourante = $_GET['p'];
                }

            }else{
                $pageCourante = 1;
            }

            $premierElementDeLaPage = ($pageCourante - 1) * $nbeParPage;


            //selection des information sur les fideles
            $selectAllFidele= $db->prepare("SELECT *
                                            
                                        FROM
                                            depenses
                                        WHERE lisible = 1
                                        ORDER BY id DESC");
            $selectAllFidele->execute();

        }

    }else{
        header('Location:login.php');
    }
?>
 
    <section class="wrapper">
        
        <div class="row">
            <div class="col-lg-12">
                <ol class="breadcrumb">
                    <li class="col-blue"><i class="material-icons">home </i><a href="index.php" class="col-blue"> Accueil</a></li>
                    <li class="col-blue"><i class="material-icons">assistant </i> Sainte Cène</li>
                    <li class="col-blue"><i class="material-icons">assistant</i><a href="#" class="col-blue"> Liste des dépenses</a></li>

                    <li style="float: right;"> 
                        <a class="" href="#" title="Imprimer la lla liste des projets" target="_blank"><i class="material-icons">print</i> Imprimer</a>
                        <a class="" href="#" title="Imprimer la lla liste des projets" target="_blank"><i class="material-icons">print</i> CSV</a>
                    </li>
                   
                </ol>
            </div>
        </div>
        
            <div class="row clearfix card">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 card">
                   

                        <div class="header text-center h4">
                              
                                <?php echo $total; ?> Dépenses enregistrés
                                 
                        </div>

                        <div class="body">
                            <div class="table-responsive" id="old_table">
                                <table class="table table-bordered table-striped table-hover js-basic-example dataTable tableau_dynamique">  
                                    <thead>
                                        <tr>
                                            <th><i class="material-icons iconposition"></i>Numéro</th>
                                            <th><i class="material-icons iconposition"></i>Motif</th>
                                            <th><i class="material-icons iconposition"></i>Montant</th>
                                            <th><i class="material-icons iconposition"></i>Date Eregistrement</th>
                                            <th><i class="material-icons iconposition"></i>Date de dépense</th>
                                            <th><i class="material-icons iconposition"></i>Auteur</th>
                                            <th><i class="material-icons iconposition"></i>Projet</th>
                                            <th><i class="material-icons iconposition">settings</i>Action</th>
                                        </tr>
                                    </thead>
                                     <tbody>
                                        <?php 
                                            $n=0;
                                            $reside = "";
                                            $total = 0;                                        
                                            while($liste=$selectAllFidele->fetch(PDO::FETCH_OBJ)){
                                            ?>
                                        <tr>
                                            <td><?php echo ++$n; ?></td>
                                            <td><?php echo $liste->motif; ?></td>
                                            <td><?php echo $liste->montant; 
                                                        $total = $total + $liste->montant; ?></td>
                                            <td>
                                               <?php echo $liste->date; ?>
                                            </td> 
                                            <td>
                                               <?php echo $liste->date_d; ?>
                                            </td>
                                            <td>
                                                <?php
                                                echo $liste->utilisateur_idutilisateur;
                                             ?></td>
                                             <td>
                                               <?php echo $liste->traveau; ?>
                                            </td>
                                            <td width="15%">
                                            <i onclick="onDelete(<?php echo $liste->id; ?>)" class="material-icons">delete</i>     
                                            </td>

                                        </tr>
                                        <?php
                                        }
                                        ?>
                                        <tr>
                                            <td style="font-style: bold; font-size: 25px">Dépense total</td>
                                            <td colspan="5" style="text-align: center;
                                            font-style: bold; font-size: 25px"><?php echo $total
                                        ; ?></td>
                                        </tr>
                                    </tbody>
                                </table>
                                

                            </div>
                            <br>
                            
                        </div>
                </div>
            </div>                
                            

                       
                    

                    <script>
                        $(".tableau_dynamique").DataTable();

function onDelete(arg){
   if(confirm("Etes vous vraiment sure de vouloir l'éffacer ? vous ne pourrez plus faire chemin retour !"))
        {

            console.log('delDepense.php?id='+arg);
            $.ajax({
                url: 'delDepense.php?id='+arg,
                type: 'GET',
                contentType: false,
                processData: false,
                data: arg,
                success: function(response) {
                    $('#main-content').load("listeDepense.php");
                   
                }
        });
        }
}
                    
                     
                        $('.afficher').on('click', function(af){
                            $('.loader').show();
                            af.preventDefault();
                           var $b = $(this);
                            url = $b.attr('href');
                           $('#main-content').load(url, function(){
                                $('.loader').hide();
                           });
                        });

                        $('.item').on('click', function(i){

                            i.preventDefault();
                            $('#modifiertext').html('Chargement...');
                            var $i = $(this);                            
                            url = $i.attr('href');

                             $('#main-content').load(url);

                            $('#modifiertext').html('');
                        });

                        
                        $('.loader').hide();

                    </script>
        
    </section>
