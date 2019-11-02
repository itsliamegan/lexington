import { listen, changeAttribute } from './dom';

listen('save-device', 'click', handleDeviceSave);

function handleDeviceSave() {
  changeAttribute('save-device', 'textContent', 'Saving...');
}
