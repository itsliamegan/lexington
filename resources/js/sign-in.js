import { changeAttribute, callMethod } from './dom';

window.gapi.load('auth2', () => {
  window.gapi.auth2
    .init({
      client_id:
        '96510064562-868mpinnin5a05ud7alenl3gmt1s6c5l.apps.googleusercontent.com',
    })
    .then(auth2 => {
      auth2.attachClickHandler(
        'sign-in-button',
        { prompt: 'select_account' },
        handleSignin
      );
    });
});

function handleSignin(googleUser) {
  const token = googleUser.getAuthResponse().id_token;

  changeAttribute('sign-in', 'textContent', 'Signing In...');
  changeAttribute('token', 'value', token);

  callMethod('sign-in-form', 'submit');
}
