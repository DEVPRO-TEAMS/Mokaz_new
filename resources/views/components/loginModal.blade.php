<div class="modal fade" id="modalLogin">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="flat-account bg-surface">
                    <h3 class="title text-center">Connexion</h3>
                    <span class="close-modal icon-close2" data-bs-dismiss="modal"></span>
                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        <fieldset class="box-fieldset">
                            <label for="email">Votre adresse email<span>*</span>:</label>
                            <input id="email" type="email" class="form-contact style-1 @error('email') is-invalid @enderror"
                                name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>

                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </fieldset>
                        <fieldset class="box-fieldset">
                            <label for="pass">Votre mot de passe<span>*</span>:</label>
                            <div class="box-password">
                                <input type="password"
                                    class="form-contact style-1 password-field @error('password') is-invalid @enderror" name="password"
                                    placeholder="Password" required autocomplete="current-password">
                                <span class="show-pass">
                                    <i class="icon-pass icon-eye"></i>
                                    <i class="icon-pass icon-eye-off"></i>
                                </span>
                            </div>
                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </fieldset>
                        <div class="d-flex justify-content-between flex-wrap gap-12">
                            <fieldset class="d-flex align-items-center gap-6">
                                <input type="checkbox" class="tf-checkbox style-2" name="remember" id="remember"
                                    {{ old('remember') ? 'checked' : '' }}>
                                <label for="remember" class="caption-1 text-variant-1">Se souvenir de moi</label>
                            </fieldset>
                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" class="caption-1 text-primary">Mot de passe oublie ?</a>
                            @endif
                        </div>
                        <button type="submit" class="tf-btn primary w-100">Se connecter</button>
                    </form>
                </div>
            </div> 
        </div>
    </div>