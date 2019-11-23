                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" id="navbarDropdownLicencie" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Espace licencié
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdownLicencie">
                            <li><a class="dropdown-item dropdown-toggle">Mes Entrainements</a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="index.php?controller=entrainement&action=Afficher">Tableau</a></li>
                                </ul>
                            </li>
                            <li><a class="dropdown-item dropdown-toggle">Mes inscriptions</a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="index.php?controller=concours&action=Inscrire">S'inscrire</a></li>
                                    <li><a class="dropdown-item" href="index.php?controller=concours&action=Desinscrire">Se désincrire</a></li>
                                </ul>
                            </li>
                            <li><a class="dropdown-item dropdown-toggle">Mes résultats</a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="index.php?controller=resultat&action=Afficher">Concours</a></li>
                                    <li><a class="dropdown-item" href="check_resultat_db.php?cle=a14axQHk8JA6dfpjNmwPoRpCvcTQg5WG8pM6N6Tzmo75XcgIoy&recupererClassementsLicencie=<?php echo $_SESSION['licencie']?>&annee=2019">Mise à jour</a></li>
                                </ul>
                            </li>
                            <!-- <li><a class="dropdown-item dropdown-toggle">Mes Commandes</a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="index.php?controller=commande&action=Afficher">Formulaire</a></li>
                                </ul>
                            </li> -->
                            <li class="divider-vertical"></li>
                            <li><a class="dropdown-item dropdown-toggle">Mon Profil</a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="index.php?controller=user&action=Afficher">Mes données personnelles</a></li>
                                    <li class="divider-vertical"></li>
                                    <li><a class="dropdown-item" href="index.php?controller=user&action=Securite">Modifier mon mot de passe</a></li>
                                </ul>
                            </li>
                        </ul>
                    </li>
