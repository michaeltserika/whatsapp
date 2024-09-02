D:.
|   .env
|   .env.test
|   .gitignore
|   composer.json
|   composer.lock
|   importmap.php
|   package-lock.json
|   package.json
|   phpunit.xml.dist
|   postcss.config.js
|   symfony.lock
|   tailwind.config.js
|   webpack.config.js
|
+---assets
|   |   app.css
|   |   app.js
|   |   bootstrap.js
|   |   controllers.json
|   |
|   +---controllers
|   |       hello_controller.js
|   |
|   +---images
|   |       logo.png
|   |
|   +---react
|   |   \---controllers
|   |           Hello.jsx
|   |
|   +---styles
|   |       app.css
|   |
|   \---vendor
|       |   installed.php
|       |
|       +---@hotwired
|       |   +---stimulus
|       |   |       stimulus.index.js
|       |   |
|       |   \---turbo
|       |           turbo.index.js
|       |
|       +---react
|       |       react.index.js
|       |
|       +---react-dom
|       |       react-dom.index.js
|       |
|       \---scheduler
|               scheduler.index.js
|
+---bin
|       console
|       phpunit
|
+---config
|   |   bundles.php
|   |   preload.php
|   |   routes.yaml
|   |   services.yaml
|   |
|   +---packages
|   |       asset_mapper.yaml
|   |       cache.yaml
|   |       debug.yaml
|   |       doctrine.yaml
|   |       doctrine_migrations.yaml
|   |       framework.yaml
|   |       mailer.yaml
|   |       messenger.yaml
|   |       monolog.yaml
|   |       notifier.yaml
|   |       react.yaml
|   |       routing.yaml
|   |       security.yaml
|   |       translation.yaml
|   |       twig.yaml
|   |       validator.yaml
|   |       webpack_encore.yaml
|   |       web_profiler.yaml
|   |
|   \---routes
|           framework.yaml
|           security.yaml
|           web_profiler.yaml
|
+---htdocs
|       index.php
|       projet.tar.gz
|
+---migrations
|       .gitignore
|       Version202408171123.php
|       Version202408200818.php
|       Version202408211402.php
|
+---public
|   |   index.php
|   |
|   \---build
|           app.css
|           app.js
|           entrypoints.json
|           manifest.json
|           runtime.js
|           tailwind.css
|           vendors-node_modules_react_index_js.js
|
+---src
|   |   Kernel.php
|   |   Untitled-1.md
|   |
|   +---Command
|   |       CreateUserCommand.php
|   |
|   +---Controller
|   |   |   .gitignore
|   |   |   SendMessageController.php
|   |   |   WhatsAppController.php
|   |   |
|   |   +---Auth
|   |   |       LoginController.php
|   |   |       LogoutController.php
|   |   |       RegisterController.php
|   |   |
|   |   +---Compain
|   |   |       CompainController.php
|   |   |
|   |   +---Contact
|   |   |       ContactController.php
|   |   |
|   |   \---Message
|   |           MessageController.php
|   |           TemplateController.php
|   |
|   +---Entity
|   |       .gitignore
|   |       Campaign.php
|   |       Contact.php
|   |       User.php
|   |
|   +---Form
|   |       ContactType.php
|   |
|   +---Message
|   |       SmsNotification.php
|   |       WhatsappNotification.php
|   |
|   +---MessageHandler
|   |       SmsNotificationHandler.php
|   |       WhatsappNotificationHandler.php
|   |
|   +---Repository
|   |       .gitignore
|   |       CampaignRepository.php
|   |       ContactRepository.php
|   |       UserRepository.php
|   |
|   \---Services
|           WhatsappService.php
|
+---templates
|   |   admin.html.twig
|   |   base.html.twig
|   |   home.html.twig
|   |   name.html.twig
|   |   vite.html.twig
|   |   
|   +---auth
|   |   +---login
|   |   |       index.html.twig
|   |   |
|   |   \---register
|   |           index.html.twig
|   |
|   +---compain
|   |       add.html.twig
|   |       index.html.twig
|   |
|   +---contacts
|   |       add.html.twig
|   |       list.html.twig
|   |       one.html.twig
|   |
|   +---message
|   |       manually.html.twig
|   |       template.html.twig
|   |
|   \---send_message
|           index.html.twig
|
+---tests
|       bootstrap.php
|
\---translations
        .gitignore
        messages.fr.xlf
        messages.fr.yaml
        security.fr.xlf
        validators.fr.xlf Voici mon ariborisance de projet je veux implante une 1. Connexion Webhook avec WhatsApp

 * Implémentation de la Connexion Webhook : 
  - Mise en place d'un webhook pour établir une connexion stable et sécurisée avec l'API WhatsApp.

  - Configuration du webhook pour recevoir des notifications en temps réel sur les événements tels que les messages entrants, les statuts de livraison, et les confirmations de lecture.

 -  Test de la connexion pour s'assurer de la réception correcte des données depuis WhatsApp. et voici mon {% extends 'base.html.twig' %}

{% block title %}{% endblock %}

{% block body %}
    <div class="flex">
        <nav class="w-64 bg-gray-800 text-white h-screen fixed top-0 left-0 flex flex-col">
            <ul class="flex-1 space-y-2 p-4">
                <li class="relative group">
                    <a href="javascript:void(0)" class="block px-4 py-2 text-lg font-semibold hover:bg-gray-700">
                        <ion-icon name="rocket" class="mr-2"></ion-icon> Campagne marketing
                    </a>
                    <ul class="absolute left-0 w-full bg-gray-700 space-y-2 mt-2 group-hover:block">
                        <li>
                            <a href="{{ path('app_compain_compain') }}" class="block px-4 py-2 hover:bg-gray-600">
                                <ion-icon name="cube" class="mr-2"></ion-icon> Campagne
                            </a>
                        </li>
                        <li>
                            <a href="/message/send/manually" class="block px-4 py-2 hover:bg-gray-600">
                                <ion-icon name="mail" class="mr-2"></ion-icon> Envoyer message manuellement
                            </a>
                        </li>
                        <li>
                            <a href="/templates" class="block px-4 py-2 hover:bg-gray-600">
                                <ion-icon name="document-text" class="mr-2"></ion-icon> Templates
                            </a>
                        </li>
                        <li>
                            <a href="/contacts" class="block px-4 py-2 hover:bg-gray-600">
                                <ion-icon name="people" class="mr-2"></ion-icon> Tous mes contacts
                            </a>
                        </li>
                        <li>
                            <a href="/configuration/sms/whatsapp/api" class="block px-4 py-2 hover:bg-gray-600">
                                <ion-icon name="logo-whatsapp" class="mr-2"></ion-icon> SMS et Whatsapp API
                            </a>
                        </li>
                        <li>
                            <a href="/configuration/email/smtp" class="block px-4 py-2 hover:bg-gray-600">
                                <ion-icon name="mail-open" class="mr-2"></ion-icon> Détails SMTP Email
                            </a>
                        </li>
                        <li>
                            <a href="/contacts/unscribe" class="block px-4 py-2 hover:bg-gray-600">
                                <ion-icon name="close-circle" class="mr-2"></ion-icon> Désabonner de mes listes de contact
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
            <a href="{{ path('app_auth_logout') }}" class="block px-4 py-2 text-sm bg-gray-700 hover:bg-gray-600 text-center mt-auto">
                <ion-icon name="log-out" class="mr-2"></ion-icon> Déconnexion
            </a>
        </nav>
        <main class="ml-64 p-6">
            {% block content %}{% endblock %}
        </main>
    </div>
{% endblock %}
