Gestionnaire de recettes (Application réalisée en Symfony 6.3.3)

- L'application doit permettre à un utilisateur de créer un compte afin de pouvoir partager ses recettes.
- L'utilisateur pourra créer des entitées ingredients qu'il pourra ensuite choisir pour créer ses entitées recettes.
- Les recettes pourront être rendues publiques ou gardées en privé. On pourra ajouté une image illustrant la recette.
- Les utilisateurs pourront noter sur 5 les recettes publiques des autres utilisateurs.

- Les visiteurs ne pourront que consulter les recettes rendues publiques.

- Les visiteurs et utilisateurs pourront remplir un formulaire de contact pour joindre l'administrateur du site.
- Le formulaire de contact devra envoyer un mail à l'administrateur du site. (création d'un service dédié)
- Le formulaire de contact sera protéger par recaptchaV3.

- L'administration permettra d'accéder au CRUD des utilisateurs et des messages de contacts

- Pour ajouter des comptes administrateurs, on créera une commande symfony personnalisée
-----------------------------------------------------------------------------------

- Fixtures : mises en place avec les librairies et bundle : Doctrine-fixtures-bundle et fakerPhp
- Recaptchav3 : avec le bundle karser-recaptcha3-bundle
- Envoi de mails : avecc vich/uploader-bundle
- administration avec EasyAdmin 4
- wysiwyg : avec ckeditor-bundle
- tests unitaires : en phpUnit
