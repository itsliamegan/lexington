import q, { listen } from './dom';

listen('open-actions-menu', 'click', handleActionsMenuOpen);

function handleActionsMenuOpen() {
  q('actions-menu').classList.toggle('list__actions-menu--open');
}
