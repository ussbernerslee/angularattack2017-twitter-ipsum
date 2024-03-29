<?php
use App\Profile;
use Illuminate\Support\Facades\Input;
use Illuminate\Database\Eloquent\ModelNotFoundException;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::get('/', function () {
	return view('welcome');
});
Route::get('/profile', function () {
	return view('welcome');
});
Route::get('/privacy', function () {
	return view('welcome');
});
Route::get('/terms', function () {
	return view('welcome');
});
Route::get('/feed', function () {
	return view('welcome');
});
Route::get('twitter/oauth', ['as' => 'twitter.login', function(){
	// your SIGN IN WITH TWITTER  button should point to this route
	$sign_in_twitter = true;
	$force_login = false;

	// Make sure we make this request w/o tokens, overwrite the default values in case of login.
	Twitter::reconfig(['token' => '', 'secret' => '']);
	$token = Twitter::getRequestToken(route('twitter.callback'));

	if (isset($token['oauth_token_secret']))
	{
		$url = Twitter::getAuthorizeURL($token, $sign_in_twitter, $force_login);

		Session::put('oauth_state', 'start');
		Session::put('oauth_request_token', $token['oauth_token']);
		Session::put('oauth_request_token_secret', $token['oauth_token_secret']);

		return Redirect::to($url);
	}

	return Redirect::route('twitter.error');
}]);

Route::get('twitter/success', ['as' => 'twitter.callback', function() {
	// You should set this route on your Twitter Application settings as the callback
	// https://apps.twitter.com/app/YOUR-APP-ID/settings
	if (Session::has('oauth_request_token'))
	{
		$request_token = [
			'token'  => Session::get('oauth_request_token'),
			'secret' => Session::get('oauth_request_token_secret'),
		];

		Twitter::reconfig($request_token);

		$oauth_verifier = false;

		if (Input::has('oauth_verifier'))
		{
			$oauth_verifier = Input::get('oauth_verifier');
		}

		// getAccessToken() will reset the token for you
		$token = Twitter::getAccessToken($oauth_verifier);

		if (!isset($token['oauth_token_secret']))
		{
			return Redirect::route('twitter.login')->with('flash_error', 'We could not log you in on Twitter.');
		}

		$credentials = Twitter::getCredentials(['include_email' => 'true']);

		if (is_object($credentials) && !isset($credentials->error))
		{
			// $credentials contains the Twitter user object with all the info about the user.
			// Add here your own user logic, store profiles, create new users on your tables...you name it!
			// Typically you'll want to store at least, user id, name and access tokens
			// if you want to be able to call the API on behalf of your users.

			// This is also the moment to log in your users if you're using Laravel's Auth class
			// Auth::login($user) should do the trick.

			Session::put('access_token', $token);
			$profile = null;
			try {
				$profile = Profile::where("profileAtHandle", $credentials->screen_name)->firstOrFail();
			} catch(ModelNotFoundException $modelNotFoundException) {
				Profile::create(["profileAccessToken" => $token["oauth_token"], "profileAtHandle" => $credentials->screen_name, "profileEmail" => $credentials->email, "profileImage" => $credentials->profile_image_url_https]);
			} finally {
				if($profile === null) {
					$profile = Profile::where("profileAtHandle", $credentials->screen_name)->first();
				}
			}

			session(["profile" => $profile]);
			return Redirect::to('/')->with('flash_notice', 'Congrats! You\'ve successfully signed in!');
		}

		return Redirect::route('twitter.error')->with('flash_error', 'Crab! Something went wrong while signing you up!');
	}
}]);

Route::get('twitter/error', ['as' => 'twitter.error', function(){
	// Something went wrong, add your own error handling here
}]);

Route::get('twitter/logout', ['as' => 'twitter.logout', function(){
	Session::forget('access_token');
	return Redirect::to('/')->with('flash_notice', 'You\'ve successfully logged out!');
}]);