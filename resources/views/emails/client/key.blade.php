@component('mail::message')
# Confirmation d'achat

L'équipe de QR Québec vous remercie pour votre soutien ! Votre achat nous permet de poursuivre le développement de ce projet.

Votre achat est déjà actif dans l'application QR Québec, vous pouvez en profiter dès maintenant.

Si dans le futur vous devez réinstaller l'application, ou pour activer cet achat sur un autre appareil qui vous appartient,
dirigez-vous dans la section **Aide** de l'application et utilisez la fonction **Récupérer mes achats** et utilisez les
informations suivantes:

@component('mail::panel')
Votre adressse courriel: **{{ $client->email }}**

Votre code de récupération: **{{ $client->key }}**
@endcomponent

Veuillez ne pas partager ce code avec personne. S'il est utilisé sur des appareils qui ne vous appartiennent
pas, vous pourriez voir votre licence désactivée.

&mdash; L'équipe de QR Québec
@endcomponent
