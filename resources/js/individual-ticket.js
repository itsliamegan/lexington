import debounce from 'lodash/debounce';
import {
  listen,
  exists,
  changeAttribute,
  removeChildren,
  getAttribute,
  callMethod,
  create,
} from './dom';

listen('ticket-form', 'submit', handleTicketSave);
listen('advance-status', 'click', handleAdvanceStatus);
listen('print-ticket', 'click', handlePrint);
listen(
  'device-name',
  'input',
  createNameChangeListener({
    url: '/lexington/devices/search.json',
    dataList: 'device-names',
  })
);
listen(
  'loaner-name',
  'input',
  createNameChangeListener({
    url: '/lexington/loaners/search.json',
    dataList: 'loaner-names',
  })
);

function handleTicketSave() {
  changeAttribute('save-ticket', 'textContent', 'Saving...');
}

function handleAdvanceStatus() {
  if (exists('current-status') && exists('next-status')) {
    changeAttribute('current-status', 'selected', false);
    changeAttribute('next-status', 'selected', true);

    callMethod('save-ticket', 'click');
  }
}

function handlePrint() {
  const ticketId = getAttribute('ticket-form', 'data-ticket-id');
  const windowFeatures = `height=${window.innerHeight},width=${window.innerWidth},menu,toolbar,allow-modals`;

  window.open(`/lexington/tickets/${ticketId}/print`, '_blank', windowFeatures);
}

function createNameChangeListener(options = {}) {
  const { url, dataList } = options;

  function handleNameChange(event) {
    const name = event.target.value;
    let previousName;

    if (name && name !== previousName) {
      previousName = name;

      fetch(`${url}?q=${name}`, { credentials: 'include' })
        .then(res => res.json())
        .then(devices => {
          removeChildren(dataList);

          devices.forEach(device => {
            const option = create('option', {
              value: device.name,
            });
            option.textContent = `(${device.assetTag} | ${device.serialNumber})`;
            callMethod(dataList, 'appendChild', option);
          });
        });
    }
  }

  return debounce(handleNameChange, 300);
}
