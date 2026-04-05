<x-mail::message>
# Bienvenue chez WashPro, {{ $user->prenom }} !

Nous sommes ravis de vous compter parmi nos clients.

WashPro est votre solution simple et efficace pour tous vos besoins de nettoyage. Vous pouvez dès à présent vous connecter à votre espace client pour suivre vos dépôts et opérations en toute transparence.

<x-mail::button :url="url('/login')">
Accéder à mon espace
</x-mail::button>

À très bientôt,<br>
L'équipe {{ config('app.name') }}
</x-mail::message>
