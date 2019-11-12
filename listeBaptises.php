<?php
    session_start();

    if(isset($_SESSION['login'])){
        $idUser = $_SESSION['login'];
        $annee = $_SESSION['annee'];
        require_once('includes/connexionbd.php');
        require_once('includes/function_role.php');
    
        if(!has_Droit($idUser, "Lister baptises")){

            header('Location:index.php');

        }else{
    
            $bapteme = null;

            $nbeParPage=100;
                $selectNombreBaptise = "SELECT COUNT(idbapteme) AS nbretotalbaptise FROM bapteme, fidele WHERE bapteme.lisible=1 AND fidele.lisible = 1 AND fidele_idfidele = idfidele";
                $selectNombreBaptise=$db->query("$selectNombreBaptise");
                
                while($idselectNombre=$selectNombreBaptise->fetch(PDO::FETCH_ASSOC)){
                    $total = $idselectNombre['nbretotalbaptise'];
                }

             $nbDePage = ceil($total/$nbeParPage);

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


                   $fideles = $db->prepare("SELECT idpersonne, codefidele, nom, prenom, statut, datenaiss, idfidele, idbapteme, datebaptise, lieu_baptise from personne as pers, fidele as fil, bapteme where idpersonne=personne_idpersonne and pers.lisible=1 AND fil.lisible=1 and fil.idfidele = bapteme.fidele_idfidele and bapteme.lisible = 1 order by nom  LIMIT $premierElementDeLaPage, $nbeParPage");
            $fideles->execute();


    }

}else{
    header('Location:login.php');
}
?>

    <section class="wrapper">




   
        
        
    <div class="col-lg-12">
                <ol class="breadcrumb">
                    <li> <i class="material-icons text-primary">home</i><a href="index.php" class="col-blue"> Accueil</a></li>
                    <li> <i class="material-icons text-primary">grain</i><a href="#" class="col-blue"> Activité</a></li>
                    <li> <i class="material-icons text-primary">list</i><a href="#" class="col-blue">Liste de baptisé</a></li>
                </ol>
    </div>


    <div class="row clearfix">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="card">

                        <div class="header text-center h4">
                              
                        <?php echo $total; ?> Fidèles  baptisés
                             
                                
                        </div>

                                
                            <!-- Module de recherche -->
                        

                        <div id="result"> </div>

                        <div class="table-responsive" id="old_table">
                                <table class="table table-bordered table-striped table-hover js-basic-example dataTable tableau_dynamique">  
                                    <thead>
                                        <tr>
                                            <th><i class="material-icons iconposition">confirmation_number</i>Numéro</th>
                                            <th><i class="material-icons iconposition">people</i>Nom et Prénom</th>
                                            <th><i class="material-icons iconposition">event</i>Date de naissance</th>
                                            <th><i class="material-icons iconposition">ev_station</i>Statut</th>
                                            <th><i class="material-icons iconposition">event</i>Date de baptème</th>
                                            <th><i class="material-icons iconposition">description</i>Lieu de baptème</th>
                                            
                                            <th><i class="material-icons iconposition">settings</i>Action</th>
                                        </tr>

                                    </thead>
                                    <tfoot>
                                        <tr>
                                             <th><i class="material-icons iconposition">confirmation_number</i>Numéro</th>
                                            <th><i class="material-icons iconposition">people</i>Nom et Prénom</th>
                                            <th><i class="material-icons iconposition">event</i>Date de naissance</th>
                                            <th><i class="material-icons iconposition">ev_station</i>Statut</th>
                                            <th><i class="material-icons iconposition">event</i>Date de baptème</th>
                                            <th><i class="material-icons iconposition">description</i>Lieu de baptème</th>
                                            
                                            <th><i class="material-icons iconposition">settings</i>Action</th>
                                        </tr>
                                    </tfoot>
                                    <tbody>
                                    <?php
                                    $n = 0;
                                    while($fidele=$fideles->fetch(PDO::FETCH_OBJ)){
                                       
                                        ?>
                                        <tr>
                                            <td><?php echo ++$n; ?></td>
                                            <td><?php echo $fidele->nom.' '.$fidele->prenom; ?></td>
                                            <td><?php echo $fidele->datenaiss; ?></td>
                                            <td><?php echo $fidele->statut; ?></td>
                                            <td><?php echo $fidele->datebaptise; ?></td>
                                            <td><?php echo $fidele->lieu_baptise; ?></td>
                                            <td width="12%">
                                              
                                                <a class="col-blue afficher" href="afficherFidele.php?code=<?php echo $fidele->idpersonne; ?>" title="Visualiser" <?php if(!has_Droit($idUser, "Afficher fidele")){echo 'disabled';}else{echo "";} ?>><i class="material-icons">loupe</i></a>
                                                    <a class="col-green afficher" href="modifierBapteme.php?id=<?php echo $fidele->idbapteme; ?>" title="Modifier" <?php if(!has_Droit($idUser, "Modifier bapteme") || (date('Y') != $annee)){echo 'hidden';}else{echo "";} ?>><i class="material-icons">border_color</i></a>
                                                    <a class="col-red" href="supprimerBapteme.php?code=<?php echo $fidele->idbapteme; ?>" title="Supprimer" <?php if(!has_Droit($idUser, "Supprimer bapteme") || (date('Y') != $annee)){echo 'hidden';}else{echo "";} ?>><i class="material-icons">delete</i></a>
                                            
                                                    
                                            </td>                                      
                                           
                                           
                                            
                                        </tr>
                                    <?php
                                    }
                                    ?>
                                    </tbody>
                                </table>
                            </div>


                                    <br>
                            <div align="center">
                                <a class="btn btn-success" href="report/imprimer.php?file=liste_fideles_baptises" title="Imprimer la liste des fidèles baptisés" target="_blank"><i class="fa fa-print"></i> Imprimer</a>
                            </div>
                            <br>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </section>
<script type="text/javascript">

$('#chargement').hide();

    $('.item').on('click', function(i){

                            i.preventDefault();
                            $('#modifiertext').html('Chargement...');
                            var $i = $(this);                            
                            url = $i.attr('href');

                             $('#main-content').load(url);

                            $('#modifiertext').html('');
                        });

     $('.afficher').on('click', function(af){

                            af.preventDefault();
                            $('.loader').show();
                           var $b = $(this);
                            url = $b.attr('href');
                           $('#main-content').load(url, function(){
                                $('.loader').hide();
                           });
                        });
$('.col-red').on('click', function(e){

                            e.preventDefault();

                            var $a = $(this);
                            var url = $a.attr('href');
                            if(window.confirm('Voulez-vous supprimer ce baptisé?')){
                                $.ajax(url, {

                                    success: function(){
                                        $a.parents('tr').remove();
                                    },

                                    error: function(){

                                        alert("Une erreur est survenue lors de la suppresion du baptisé");
                                    }
                                });
                            }
                        });

 $('#recherche').keyup(function(){

                            var txt = $(this).val();

                           // alert(txt);
                            if(txt != ''){                                
                                $.ajax({
                                    
                                    url:"searchListeBapteme.php", 
                                    method:"get",
                                    data:{search:txt},
                                    dataType:"text",
                                    success:function(data)
                                    {
                                        $('#old_table').hide();
                                        $('#result').html(data);
                                      //alert(txt);
                                    }
                                });

                            }else{
                               // alert(txt);
                                $('#result').html(txt);
                                $('#old_table').show();
                            }

                        });
 $(".tableau_dynamique").DataTable();
						$('.loader').hide();
</script>
