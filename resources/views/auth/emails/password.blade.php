<h3>Bonjour {{Auth::user()->name}},</h3>


Cliquez sur le lien pour réinitialiser votre mot de passe : <a href="{{ $link = url('password/reset', $token).'?email='.urlencode($user->getEmailForPasswordReset()) }}"> {{ $link }} </a>
