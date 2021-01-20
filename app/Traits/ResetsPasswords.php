<?php

    namespace App\Traits;
    use Illuminate\Http\Request;
    use Illuminate\Mail\Message;
    use Illuminate\Support\Facades\Password;
    use App\Helpers\AppHelper;
    use App\Models\User;

    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\Hash;

    
    trait ResetsPasswords
    {
        /**
         * Send a reset link to the given user.
         *
         * @param  \Illuminate\Http\Request  $request
         * @return \Illuminate\Http\Response
         */
        public function postEmail(Request $request)
        {
            return $this->sendResetLinkEmail($request);
        }

        /**
         * Send a reset link to the given user.
         *
         * @param  \Illuminate\Http\Request  $request
         * @return \Illuminate\Http\Response
         */
        public function sendResetLinkEmail(Request $request)
        {
            $this->validate($request, ['email' => 'required|email']);

            $broker = $this->getBroker();

            $response = Password::broker($broker)->sendResetLink($request->only('email'), function (Message $message) {
                $message->subject($this->getEmailSubject());
            });

            switch ($response) {
                case Password::RESET_LINK_SENT:
                    return $this->getSendResetLinkEmailSuccessResponse($response);

                case Password::INVALID_USER:
                default:
                    return $this->getSendResetLinkEmailFailureResponse($response);
            }
        }

        /**
         * Get the e-mail subject line to be used for the reset link email.
         *
         * @return string
         */
        protected function getEmailSubject()
        {
            return property_exists($this, 'subject') ? $this->subject : 'Your Password Reset Link';
        }

        /**
         * Get the response for after the reset link has been successfully sent.
         *
         * @param  string  $response
         * @return \Symfony\Component\HttpFoundation\Response
         */
        protected function getSendResetLinkEmailSuccessResponse($response)
        {
            return response()->json(['code' => 200, 'message' => 'Ссылка для восстановления пароля отправлена на указанный адрес'], 200);
        }

        /**
         * Get the response for after the reset link could not be sent.
         *
         * @param  string  $response
         * @return \Symfony\Component\HttpFoundation\Response
         */
        protected function getSendResetLinkEmailFailureResponse($response)
        {
            $result = ['code' => 400, 'message' => 'Не удалось отправить письмо.'];

            $response !== Password::INVALID_USER || $result = ['code' => 400, 'message' => 'Указанный адрес не найден среди зарегистрированных.'];

            return response()->json($result, 400);
        }


        /**
         * Reset the given user's password.
         *
         * @param  \Illuminate\Http\Request  $request
         * @return \Illuminate\Http\Response
         */
        public function postReset(Request $request)
        {
            return $this->reset($request);
        }

        /**
         * Reset the given user's password.
         *
         * @param  \Illuminate\Http\Request  $request
         * @return \Illuminate\Http\Response
         */
// Поскольку у нас используется JWT нельзя пользоваться рецептами Laravel 
// при изменении пароля!        
        public function reset(Request $request)
        {
            $this->validate($request, $this->getResetValidationRules());

            $credentials = $request->only(
                'email', 'password'
            );
            $user = auth()->user();
            if( $user) {
                $user->password = Hash::make($credentials['password']);
                $user->save();
                return response()->json(['message' => 'Successfully change password!']);
            } 
            else {
                return response()->json(['message' => 'No user autorization']);
                
            }
 
        }

        /**
         * Get the password reset validation rules.
         *
         * @return array
         */
        protected function getResetValidationRules()
        {
            return [
    //            'token' => 'required',
                'email' => 'required|email',
                'password' => 'required|min:6',
            ];
        }

        /**
         * Reset the given user's password.
         *
         * @param  \Illuminate\Contracts\Auth\CanResetPassword  $user
         * @param  string  $password
         * @return void
         */
        protected function resetPassword($user, $password)
        {
            $user->password = AppHelper::bcrypt($password);

            $user->save();

            return response()->json(['code' => 200, 'message' => 'Пароль изменен'], 200);
        }

        /**
         * Get the response for after a successful password reset.
         *
         * @param  string  $response
         * @return \Symfony\Component\HttpFoundation\Response
         */
        protected function getResetSuccessResponse($response, $user)
        {
            $token = auth()->login($user);
            $profile = $user->profile;
            $roles = $user->getAllRoles();

            return response()->json([
                'code' => 200,
//                'message' => 'Пароль изменен',
                'message' => 'Password chanhed',
                'user' => [
                    'profile' => $profile ?: null,
                    'roles' => $roles,
                    'token' => $token
                ]
            ], 200);
        }

        /**
         * Get the response for after a failing password reset.
         *
         * @param  Request  $request
         * @param  string  $response
         * @return \Symfony\Component\HttpFoundation\Response
         */
        protected function getResetFailureResponse(Request $request, $response)
        {
//            $result = ['code' => 400, 'message' => 'Что-то пошло не так'];
            $result = ['code' => 400, 'message' => 'Unknown error'];

//            $response !== Password::INVALID_TOKEN || $result = ['code' => 400, 'message' => 'Неверный токен.'];
            $response !== Password::INVALID_TOKEN || $result = ['code' => 400, 'message' => 'Error token'];

            return response()->json($result, 400);
        }

        /**
         * Get the broker to be used during password reset.
         *
         * @return string|null
         */
        public function getBroker()
        {
            return property_exists($this, 'broker') ? $this->broker : null;
        }
    }
