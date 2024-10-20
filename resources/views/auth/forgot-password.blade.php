@include('header')
@include('navbar')

<div class="d-flex flex-grow-1 justify-content-center align-items-center">
    <div class="greeting d-flex align-items-center">
        <div class="row">
            <div class="col-3">
            </div>
            <div class="col-6">
                <table class="table table-borderless text-center mx-auto w-auto" style="borders: false;">
                    <thead>
                        <tr>
                            <th>
                                <h4>Reset your PartHub account password</h4>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td> Forgot your password? No problem. <br><br>Just let us know your email address and we will
                                email
                                you a password reset link that will allow you to choose a new one.
                            </td>
                        <tr>
                            <td style='text-align:left'>
                                <form method="POST" action="{{ route('password.email') }}">
                                    @csrf
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control form-control-sm" id="email" name="email"
                                        :value="old('email')" data-toggle="password" data-size="sm" required autofocus>
                                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <button type="submit" class="btn btn-primary">Email Password Reset Link</button>
                                </form>
                            </td>
                        </tr>
                        <tr>
                            <td>Suddenly remembered your password? Log in <a href="{{ route('login') }}">here</a>!</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div class="col-3">
    </div>
</div>
</div>
</div>
