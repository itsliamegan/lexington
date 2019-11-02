window.print();

window.addEventListener('afterprint', () => {
  window.close();
});
