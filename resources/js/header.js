import q, { listen } from './dom';

listen('header-user-info', 'click', handleUserMenuOpen);

function handleUserMenuOpen() {
  q('header-user-menu').classList.toggle('header__user-menu--open');
}
