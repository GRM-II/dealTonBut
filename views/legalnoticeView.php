<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<main>
    <div class="content legal-notice-content">
        <div class="nav-buttons default-nav-buttons">
            <a href="?controller=marketpage&action=index" class="button nav nav-btn-market" title="Marché">
                <img src="/public/assets/img/market.svg" alt="Marché" class="nav-icon">
            </a>
            <a href="?controller=tradeplace&action=index" class="button nav nav-btn-trade" title="Trading">
                <img src="/public/assets/img/trading.svg" alt="Trading" class="nav-icon">
            </a>
            <a href="?controller=sitemap&action=index" class="button nav nav-btn-maps" title="Plan du site">
                <img src="/public/assets/img/sitemap-icon.svg" alt="Plan du site" class="nav-icon">
            </a>
            <button id="scroll-to-top-btn" class="button nav scroll-to-top-btn" title="Remonter en haut">
                <img id="scroll-icon" src="/public/assets/img/Blue_Arrow.svg" alt="Remonter" class="nav-icon">
            </button>
        </div>

        <div class="legal-notice-wrapper">
            <div class="login-rectangle">
            <h1 class="title">Mentions légales</h1>

            <?php if (isset($A_view['flash'])): ?>
                <div class="flash-message <?php echo $A_view['flash']['success'] ? 'flash-success' : 'flash-error'; ?>">
                    <?php echo htmlspecialchars($A_view['flash']['message'], ENT_QUOTES, 'UTF-8'); ?>
                </div>
            <?php endif; ?>

            <div class="input-rectangles">
                <section>
                    <p>
                        Le site web Deal Ton BUT, accessible à l'adresse électronique dealtonbut.feyli.dev, constitue une plateforme digitale dédiée aux échanges et transactions entre étudiants et professeurs. Cette plateforme a été conçue, développée et mise en ligne dans le cadre d'un projet pédagogique et académique visant à promouvoir la danse du ventre au sein de la communauté estudiantine. Le responsable éditorial, identifié sous le pseudonyme Kiko, assume l'entière responsabilité du contenu publié sur la plateforme ainsi que de la gestion quotidienne des publications, modifications et suppressions de contenus. En tant que responsable de publication, Kiko s'engage à respecter l'ensemble des législations en vigueur concernant la publication en ligne, la protection des données personnelles, la propriété intellectuelle et les droits d'auteur. Toute correspondance, demande d'information, réclamation ou suggestion peut être adressée directement au responsable de publication via l'adresse électronique kiko.gamingxd@gmail.com. Les demandes seront traitées dans les meilleurs délais, généralement sous 72 heures ouvrées, sauf en période de forte affluence ou de circonstances exceptionnelles, comme lors de recel de cartes Pokémon ou juste qu'il conduit sa Mercedes CLK ce petit chenapan.
                    </p>

                </section>

                <section>

                    <p>
                        L'infrastructure technique et l'hébergement du site Deal Ton BUT sont assurés par Mr. Feyli, prestataire d'hébergement web opérant depuis le Pays des Furrys. Cette entité garantit la disponibilité, la sécurité et la maintenance des serveurs sur lesquels repose l'intégralité de notre plateforme. Les serveurs utilisés bénéficient d'une architecture moderne et sécurisée, comprenant des systèmes de sauvegarde automatique quotidiens, des pare-feu de dernière génération, des systèmes de détection et prévention d'intrusions (IDS/IPS), ainsi que des protocoles de chiffrement SSL/TLS pour garantir la confidentialité des communications. L'hébergeur s'engage à maintenir un taux de disponibilité optimal et à intervenir rapidement en cas d'incident technique. Pour toute question relative à l'infrastructure d'hébergement, aux performances du serveur, aux problèmes de connexion ou aux aspects techniques de la disponibilité du site, vous pouvez contacter directement l'hébergeur au numéro de téléphone 3630. Ce service d'assistance technique est disponible et opérationnel pour répondre à l'ensemble de vos interrogations concernant les aspects matériels et logiciels de l'hébergement de la plateforme.
                    </p>

                </section>

                <section>

                    <p>
                        La totalité des éléments constitutifs du site web Deal Ton BUT, incluant mais ne se limitant pas aux textes rédactionnels, articles de blog, descriptions de produits, tutoriels, guides d'utilisation, photographies originales et modifiées, illustrations graphiques, logos et éléments de charte graphique, animations, vidéos promotionnelles et éducatives, captures d'écran, infographies, schémas explicatifs, interfaces utilisateur, codes sources et scripts, bases de données et leur structure, ainsi que tous autres contenus multimédias présents ou futurs, sont placés sous le régime de protection de la propriété intellectuelle et des droits d'auteur défini par le Code de la propriété intellectuelle français, les conventions internationales dont la Convention de Berne pour la protection des œuvres littéraires et artistiques, ainsi que l'ensemble des traités et accords internationaux relatifs à la protection de la propriété intellectuelle. Ces contenus demeurent la propriété exclusive et inaliénable de amoU, entité juridique détentrice des droits patrimoniaux, ainsi que de l'ensemble de ses contributeurs actifs et reconnus, à savoir GRM, Kiko, Akcyl, mimojeej, Louloute et Olivier G., qui ont participé de manière significative à la conception, au développement, à l'enrichissement et à la maintenance du site et de ses contenus. Toute forme de reproduction partielle ou intégrale, de représentation, de distribution publique ou privée, de modification, d'adaptation, de traduction, de transformation, d'arrangement, de communication au public par voie électronique ou par tout autre moyen, d'exploitation commerciale ou non commerciale, de mise à disposition sous quelque forme que ce soit, de copie, de téléchargement, d'extraction, de réutilisation, de citation extensive au-delà des limites du droit de courte citation, de diffusion, de publication, de rediffusion ou d'utilisation de tout ou partie des contenus du site, réalisée sans l'obtention préalable d'une autorisation expresse, écrite et nominative de la part des détenteurs des droits, est formellement et strictement interdite sous peine de poursuites judiciaires et constitue une contrefaçon sanctionnée par les articles L.335-2 et suivants du Code de la propriété intellectuelle français, passible de sanctions pénales pouvant aller jusqu'à trois ans d'emprisonnement et 300 000 euros d'amende, ainsi que de dommages et intérêts civils dont le montant sera déterminé en fonction du préjudice subi.
                    </p>

                </section>

                <section>

                    <p>
                        En stricte conformité avec les dispositions du Règlement Général sur la Protection des Données (RGPD), règlement européen n°2016/679 du 27 avril 2016 entré en vigueur le 25 mai 2018, ainsi qu'avec la loi française n°78-17 du 6 janvier 1978 relative à l'informatique, aux fichiers et aux libertés, modifiée par la loi n°2018-493 du 20 juin 2018 et ses décrets d'application successifs, l'ensemble des utilisateurs, visiteurs, membres inscrits et personnes physiques dont les données à caractère personnel sont collectées, traitées, stockées ou utilisées par notre plateforme bénéficient de droits fondamentaux et inaliénables concernant leurs informations personnelles. Ces droits comprennent notamment et de manière non exhaustive le droit d'accès permettant d'obtenir la confirmation que des données vous concernant sont ou ne sont pas traitées ainsi que la communication d'une copie de ces données, le droit de rectification autorisant la correction immédiate de données inexactes ou incomplètes, le droit à l'effacement également appelé "droit à l'oubli" permettant la suppression définitive de vos données dans certaines conditions définies par l'article 17 du RGPD, le droit à la limitation du traitement permettant de geler temporairement l'utilisation de certaines données, le droit à la portabilité vous permettant de récupérer vos données dans un format structuré et lisible par machine pour les transférer à un autre responsable de traitement, le droit d'opposition vous autorisant à vous opposer à tout moment au traitement de vos données pour des raisons tenant à votre situation particulière, ainsi que le droit de définir des directives relatives au sort de vos données après votre décès. Les catégories de données personnelles susceptibles d'être collectées incluent les données d'identification (nom, prénom, pseudonyme), les données de contact (adresse électronique, numéro de téléphone), les données de connexion (adresse IP, logs de connexion, cookies de session), les données de navigation et d'utilisation du site, ainsi que toute autre information volontairement fournie lors de l'inscription ou de l'utilisation des services. Ces données sont collectées exclusivement dans le cadre de finalités légitimes et déterminées, principalement la gestion et l'administration des comptes utilisateurs, l'authentification et la sécurisation des accès, la fourniture et l'amélioration des services proposés, la communication avec les utilisateurs, la prévention de la fraude et des abus, ainsi que le respect de nos obligations légales et réglementaires. Les données collectées sur ce site sont destinées uniquement à un usage interne pour la gestion des comptes utilisateurs et l'administration de la plateforme, et ne sont en aucun cas, sous aucune circonstance et pour aucune raison, transmises, vendues, louées, échangées, partagées, cédées ou communiquées à des tiers, qu'il s'agisse de partenaires commerciaux, d'annonceurs, de sociétés affiliées, de prestataires externes non essentiels au fonctionnement du site, d'organismes de marketing ou de toute autre entité tierce, sauf dans les cas strictement prévus par la loi ou en cas d'obligation légale impérieuse. Les données sont conservées pendant une durée n'excédant pas celle nécessaire aux finalités pour lesquelles elles sont traitées, conformément aux principes de minimisation et de limitation de la conservation édictés par le RGPD. Pour exercer l'un quelconque de ces droits, formuler une demande d'accès à vos données, demander leur rectification ou leur suppression, limiter leur traitement, vous opposer à leur utilisation, exercer votre droit à la portabilité, ou pour toute question, réclamation, préoccupation ou demande d'information relative au traitement de vos données personnelles et à la protection de votre vie privée, vous pouvez nous contacter à tout moment en adressant votre demande par courrier électronique à l'adresse kiko.gamingxd@gmail.com, en veillant à joindre une copie d'une pièce d'identité en cours de validité pour des raisons de sécurité et d'authentification, et un chèque de 52€ à destination de la vie scolaire. Nous nous engageons à répondre à votre demande dans un délai maximum d'un mois à compter de sa réception, ce délai pouvant être prolongé de deux mois supplémentaires compte tenu de la complexité et du nombre de demandes, auquel cas vous serez informé de cette prolongation. Vous disposez également du droit d'introduire une réclamation auprès de la Commission Nationale de l'Informatique et des Libertés (CNIL), autorité de contrôle française compétente en matière de protection des données personnelles, si vous estimez que vos droits ne sont pas respectés.
                    </p>

                </section>

                <section>

                    <p>
                        Le présent site web Deal Ton BUT utilise de manière limitée et strictement encadrée des cookies, qui sont de petits fichiers texte stockés localement sur votre terminal (ordinateur, tablette, smartphone ou tout autre appareil connecté) lors de votre visite et navigation sur notre plateforme. Ces cookies sont exclusivement des cookies de session, également appelés cookies techniques ou cookies fonctionnels, qui sont strictement nécessaires au bon fonctionnement, à la sécurité et à l'utilisation optimale du site. Ces cookies de session permettent notamment de maintenir votre session de connexion active pendant la durée de votre visite, de conserver temporairement vos préférences de navigation, d'assurer la sécurité de votre authentification, de prévenir les attaques de type CSRF (Cross-Site Request Forgery), de gérer votre panier d'achats ou vos sélections temporaires, et de garantir la continuité de votre expérience utilisateur lorsque vous naviguez entre les différentes pages du site. Ces cookies de session sont automatiquement supprimés de votre terminal dès la fermeture de votre navigateur ou après une période d'inactivité prédéfinie, généralement fixée à quelques heures pour des raisons de sécurité. Il est expressément précisé et garanti qu'aucun cookie de tracking, de traçage publicitaire, d'analyse comportementale à des fins commerciales, de profilage utilisateur, de ciblage marketing, de géolocalisation non essentielle, de partage sur les réseaux sociaux, ou tout autre cookie tiers destiné à des finalités autres que le fonctionnement technique du site n'est utilisé, installé, déployé ou autorisé sur notre plateforme. Nous ne recourons à aucun service d'analyse d'audience externe tel que Google Analytics, Facebook Pixel, ou similaire, et ne partageons aucune information de navigation avec des tiers à des fins publicitaires ou commerciales. La durée de conservation de ces cookies de session est limitée à la stricte durée de votre visite sur le site et n'excède jamais 24 heures. Vous avez la possibilité de configurer votre navigateur internet pour refuser l'installation de ces cookies, supprimer les cookies existants ou être averti préalablement à l'enregistrement d'un cookie sur votre terminal, toutefois il convient de noter que le refus ou la suppression des cookies de session strictement nécessaires peut affecter significativement votre expérience utilisateur, compromettre le bon fonctionnement du site, empêcher l'accès à certaines fonctionnalités essentielles comme l'authentification ou la navigation sécurisée, et rendre impossible l'utilisation normale de certains services proposés sur la plateforme.
                    </p>

                </section>

                <section>

                    <p>
                        L'éditeur du site Deal Ton BUT, représenté par son responsable de publication Kiko, déploie tous les efforts raisonnables et proportionnés ainsi que tous les moyens humains, techniques et financiers nécessaires et disponibles pour s'assurer de l'exactitude, de la fiabilité, de la pertinence, de l'actualité et de la mise à jour régulière et systématique des informations, contenus, données, descriptions, prix, caractéristiques, spécifications techniques, illustrations, photographies et tout autre élément d'information diffusés, publiés, mis à disposition ou accessibles sur ce site web, cependant il ne peut, en raison de la nature évolutive et dynamique d'internet, de la complexité des technologies mises en œuvre, de la multiplicité des sources d'information, de la possibilité d'erreurs humaines dans la saisie ou la modification des données, ainsi que des contraintes techniques inhérentes à toute plateforme numérique, garantir de manière absolue, formelle et inconditionnelle l'exactitude parfaite, la précision totale, l'exhaustivité complète, la pertinence constante ou l'actualité permanente des informations mises à la disposition des utilisateurs. En conséquence, l'éditeur décline expressément toute responsabilité concernant d'éventuelles erreurs, omissions, inexactitudes, imprécisions, lacunes, informations obsolètes, données périmées ou incomplètes qui pourraient subsister malgré nos efforts de vérification et de mise à jour, et invite les utilisateurs à signaler toute anomalie constatée afin que nous puissions procéder aux corrections nécessaires dans les meilleurs délais. L'éditeur ne saurait en aucun cas, dans les limites autorisées par la législation en vigueur, être tenu responsable, redevable ou poursuivi pour quelque dommage que ce soit, qu'il soit direct ou indirect, matériel ou immatériel, prévisible ou imprévisible, certain ou incertain, actuel ou futur, corporel ou incorporel, patrimonial ou extrapatrimonial, résultant de l'accès au site, de l'utilisation du site ou de l'impossibilité d'accéder au site, de l'interprétation des informations contenues sur le site, de la confiance accordée aux informations publiées, des décisions prises sur la base de ces informations, de l'interruption temporaire ou permanente des services, des dysfonctionnements techniques, des bugs logiciels, des virus informatiques, des intrusions malveillantes, des attaques de type déni de service, des pertes de données, des corruptions de fichiers, des erreurs de transmission, de l'indisponibilité du serveur, de la lenteur de chargement des pages, des incompatibilités avec certains navigateurs ou systèmes d'exploitation, ou de tout autre inconvénient, désagrément, préjudice ou dommage de quelque nature que ce soit résultant directement ou indirectement de l'utilisation, de la mauvaise utilisation, de l'utilisation inappropriée ou de l'impossibilité d'utiliser le site et ses services. Cette limitation de responsabilité s'applique également aux dommages causés par des tiers, notamment par d'autres utilisateurs du site, ainsi qu'aux conséquences de tout manquement éventuel à nos obligations qui ne nous serait pas directement imputable. L'utilisateur reconnaît utiliser le site à ses propres risques et sous sa seule responsabilité, et s'engage à prendre toutes les précautions nécessaires pour protéger ses propres données, équipements et systèmes informatiques contre toute forme d'atteinte, notamment en utilisant des logiciels antivirus à jour et des systèmes de sécurité appropriés.
                    </p>
                </section>

                <section>

                    <p>
                        Le projet Deal Ton BUT, plateforme collaborative destinée à faciliter les échanges et transactions entre étudiants, a été conçu, imaginé, développé, programmé, testé, déployé et mis en production grâce aux efforts combinés, au travail acharné, à la créativité débordante, aux compétences techniques diversifiées, à l'investissement personnel considérable et à la collaboration étroite et harmonieuse d'une équipe pluridisciplinaire composée de six membres talentueux et dévoués, chacun apportant son expertise unique et sa contribution essentielle au succès du projet. Cette équipe exceptionnelle comprend Thomas.A, qui a assumé les responsabilités liées à l'architecture globale du projet et à la coordination technique, Akcyl.B, qui s'est distingué dans le développement des fonctionnalités backend et la gestion des bases de données, Dimitri.C, dont l'expertise en développement frontend et en interface utilisateur a permis de créer une expérience utilisateur fluide et intuitive, Lou.D, qui a brillamment géré les aspects de design graphique, d'identité visuelle et d'ergonomie, Olivier.G, qui a apporté ses compétences en matière de sécurité informatique, de tests et de débogage, ainsi que Francisco. S, qui a contribué de manière significative à la documentation, à la rédaction des contenus et à la communication du projet. Ensemble, ces six développeurs, designers et créateurs ont travaillé sans relâche, souvent tard dans la nuit et pendant leurs weekends, pour transformer une simple idée en une plateforme fonctionnelle et performante qui répond aux besoins réels de la communauté estudiantine. Le slogan du projet, "Là où la comédie commence, les points suivent", reflète parfaitement l'esprit ludique, convivial et décalé qui anime l'équipe tout en faisant référence au système de points utilisé sur la plateforme pour récompenser l'engagement et l'activité des utilisateurs. Ce slogan incarne la philosophie du projet qui vise à rendre l'expérience d'échange et de transaction non seulement utile et pratique, mais également agréable, divertissante et enrichissante sur le plan social. Nous tenons également à remercier chaleureusement tous les testeurs bénévoles, les premiers utilisateurs, les contributeurs occasionnels, les relecteurs, les conseillers techniques, les enseignants encadrants ainsi que toutes les personnes qui, de près ou de loin, ont participé, soutenu, encouragé ou contribué au développement et à l'amélioration continue de ce projet ambitieux.
                    </p>

                </section>

                <section>

                    <p>
                        Les présentes mentions légales, ainsi que l'ensemble des conditions générales d'utilisation du site Deal Ton BUT, les politiques de confidentialité, les règles de modération, les chartes d'utilisation et tous les documents juridiques, contractuels ou informatifs qui y sont associés, annexés ou référencés, sont intégralement et exclusivement régis, interprétés, appliqués et exécutés conformément aux dispositions du droit français, incluant notamment mais sans limitation le Code civil français, le Code de la consommation, le Code de commerce, le Code de la propriété intellectuelle, le Code pénal dans ses dispositions applicables aux infractions informatiques, la loi n°78-17 du 6 janvier 1978 relative à l'informatique, aux fichiers et aux libertés dans sa version actuellement en vigueur, le Règlement Général sur la Protection des Données (RGPD) tel qu'applicable en France, la loi n°2004-575 du 21 juin 2004 pour la confiance dans l'économie numérique (LCEN), ainsi que l'ensemble des textes législatifs, réglementaires, jurisprudentiels et doctrinaux français applicables aux services de la société de l'information, au commerce électronique, à la protection des données personnelles et aux droits et obligations des utilisateurs d'internet. En cas de survenance d'un différend, d'un désaccord, d'une contestation, d'un litige, d'une réclamation ou de toute divergence d'interprétation concernant la validité, l'interprétation, l'exécution, l'inexécution, la résiliation, les conséquences ou les effets des présentes mentions légales ou de l'utilisation du site, et dans l'hypothèse où aucune solution amiable ne pourrait être trouvée par la voie de la négociation directe, de la médiation ou de tout autre mode alternatif de résolution des conflits dans un délai raisonnable fixé à trente jours calendaires à compter de la notification du différend par la partie la plus diligente, les tribunaux de la République française, et plus spécifiquement les juridictions du ressort territorial compétent déterminé selon les règles de compétence territoriale établies par le Code de procédure civile français, seront seuls et exclusivement compétents pour connaître du litige, statuer sur les demandes, trancher le différend et rendre une décision exécutoire, à l'exclusion expresse de toute autre juridiction nationale ou internationale. Cette clause attributive de juridiction s'applique même dans les situations où plusieurs défendeurs seraient impliqués, où une garantie serait appelée, où une mesure d'urgence ou une procédure de référé serait nécessaire, ou en cas d'appel, de pourvoi en cassation ou de tout autre recours, sous réserve toutefois des dispositions d'ordre public du droit français qui ne peuvent être écartées par convention, notamment celles protégeant les consommateurs ou les parties faibles à un contrat. Les parties conviennent que cette clause de choix de loi et de juridiction a été librement négociée et acceptée en toute connaissance de cause.
                    </p>
                </section>

                <p>
                    <em>Dernière mise à jour : <?php echo date('d/m/Y'); ?></em>
                </p>
            </div>
            <div class="button-center-container">
                <?php
                $isLoggedIn = isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
                $redirectUrl = $isLoggedIn ? '?controller=profilepage&action=index' : '?controller=homepage&action=index';
                ?>
                <button onclick="window.location.href='<?php echo $redirectUrl; ?>'" class="button" type="button">
                    Retour à l'accueil
                </button>
            </div>
        </div>

        <div class="legal-notice-image-container">
            <video autoplay muted loop playsinline>
                <source src="/public/assets/vid/MinecraftGameplay.mp4" type="video/mp4">
                Votre navigateur ne supporte pas la balise vidéo.
            </video>

            <div class="legal-comments-section">
                <div class="legal-comments-header">
                    <h3>Commentaires</h3>
                    <span class="legal-comments-count">42 commentaires</span>
                </div>

                <div class="legal-comment-input">
                    <div class="legal-comment-avatar">
                        <img src="/public/assets/img/placeholder-meme.jpeg" alt="Votre avatar">
                    </div>
                    <input type="text" placeholder="Ajouter un commentaire..." class="legal-comment-field">
                </div>

                <div class="legal-comments-list">

                    <div class="legal-comment-item">
                        <div class="legal-comment-avatar">
                            <img src="/public/assets/img/feedbackImages/user1.jpg" alt="Avatar utilisateur 1">
                        </div>
                        <div class="legal-comment-content">
                            <div class="legal-comment-header">
                                <span class="legal-comment-author">@xXxArthurProxXx</span>
                                <span class="legal-comment-date">il y a 2 heures</span>
                            </div>
                            <p>
                                J'ADORE LE GAMEPLAY MINECRAFT, JE VEUX ÇA DE PARTOUT DANS MA VIE
                            </p>
                            <div class="legal-comment-actions">
                                <button class="legal-comment-like">👍 <span>67</span></button>
                                <button class="legal-comment-dislike">👎</button>
                                <button class="legal-comment-reply">Répondre</button>
                            </div>
                        </div>
                    </div>

                    <div class="legal-comment-item">
                        <div class="legal-comment-avatar">
                            <img src="/public/assets/img/feedbackImages/user2.jpg" alt="Avatar utilisateur 2">
                        </div>
                        <div class="legal-comment-content">
                            <div class="legal-comment-header">
                                <span class="legal-comment-author">@Louis_Parmentier</span>
                                <span class="legal-comment-date">il y a 5 heures</span>
                            </div>
                            <p>
                                J'avais envie de lire les mentions légales... mais la vidéo était trop intéressante..
                            </p>
                            <div class="legal-comment-actions">
                                <button class="legal-comment-like">👍 <span>2</span></button>
                                <button class="legal-comment-dislike">👎</button>
                                <button class="legal-comment-reply">Répondre</button>
                            </div>
                        </div>
                    </div>

                    <div class="legal-comment-item">
                        <div class="legal-comment-avatar">
                            <img src="/public/assets/img/feedbackImages/user3.jpg" alt="Avatar utilisateur 3">
                        </div>
                        <div class="legal-comment-content">
                            <div class="legal-comment-header">
                                <span class="legal-comment-author">@Fan1DealTonBUT</span>
                                <span class="legal-comment-date">il y a 1 jour</span>
                            </div>
                            <p>
                                Je voulais en apprendre plus sur les mentions légales, mais je suis resté 4 années entières à regarder cette vidéo de gameplay Minecraft captivant
                            </p>
                            <div class="legal-comment-actions">
                                <button class="legal-comment-like">👍 <span>156</span></button>
                                <button class="legal-comment-dislike">👎</button>
                                <button class="legal-comment-reply">Répondre</button>
                            </div>
                        </div>
                    </div>

                    <div class="legal-comment-item">
                        <div class="legal-comment-avatar">
                            <img src="/public/assets/img/feedbackImages/user4.jpg" alt="Avatar utilisateur 4">
                        </div>
                        <div class="legal-comment-content">
                            <div class="legal-comment-header">
                                <span class="legal-comment-author">@bob4anetdemie</span>
                                <span class="legal-comment-date">il y a 2 jours</span>
                            </div>
                            <p>
                                zqz mé cète pqje 2 mqnssion légql é tro bi1 fqilisssitqion
                            </p>
                            <div class="legal-comment-actions">
                                <button class="legal-comment-like">👍 <span>54</span></button>
                                <button class="legal-comment-dislike">👎</button>
                                <button class="legal-comment-reply">Répondre</button>
                            </div>
                        </div>
                    </div>

                    <div class="legal-comment-item">
                        <div class="legal-comment-avatar">
                            <img src="/public/assets/img/feedbackImages/user5.jpg" alt="Avatar utilisateur 5">
                        </div>
                        <div class="legal-comment-content">
                            <div class="legal-comment-header">
                                <span class="legal-comment-author">@AvocatDeLaDéfenseNationaleDuPatrimoineFrançais</span>
                                <span class="legal-comment-date">il y a 3 jours</span>
                            </div>
                            <p>
                                En tant qu'avocat, je dois dire que ces mentions légales sont d'une clarté et d'une précision exemplaires.
                                On peut ressentir la plume du professionnel ayant créé cette page, glisser le long de son clavier tel un virtuose de la langue française.
                                Cette page est un chef-d'œuvre juridique qui allie rigueur, exhaustivité et accessibilité.
                                La disposition des sections, la formulation des clauses et la structure globale témoignent d'une maîtrise parfaite du droit applicable aux sites internet.
                                La construction des phrases est fluide, les termes juridiques sont utilisés avec justesse, et chaque paragraphe est soigneusement rédigé pour éviter toute ambiguïté.
                                La personne ayant rédigé ces mentions légales mérite des éloges pour son professionnalisme et son souci du détail.
                                masterclass frr tié un lavabo
                            </p>
                            <div class="legal-comment-actions">
                                <button class="legal-comment-like">👍 <span>512</span></button>
                                <button class="legal-comment-dislike">👎</button>
                                <button class="legal-comment-reply">Répondre</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</main>