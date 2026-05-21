class Modal {
  header = document.getElementById('header');
  modal = null;

  constructor(modalId, eventEmitter) {
    if (eventEmitter) {
      this.eventEmitter = eventEmitter;
      this.eventEmitter.on('openModal', this.openModal);
      this.eventEmitter.on('closeModal', this.closeModal);
    }

    const closeButton = document.getElementById(`${modalId}_button_close`);
    const closeButtons = document.getElementsByClassName(`${modalId}_button_close`);
    this.modal = document.getElementById(modalId);
    if (closeButton) {
      closeButton.addEventListener('click', this.emitEventCloseModal);
      this.closeButton = closeButton;
    }

    if (closeButtons) {
      Array.from(closeButtons).forEach(button => {
        button.addEventListener('click', this.emitEventCloseModal);
      });
    }
  }

  emitEventCloseModal = () => {
    this.eventEmitter.emit('closeModal', this.closeModal);
  };

  openModal = () => {
    document.body.style.overflow = 'hidden';
    this.header.style.zIndex = 1;
    this.modal.classList.remove('closed');
  };

  closeModal = () => {
    document.body.style.overflow = 'auto';
    this.header.style.zIndex = 2000;
    this.modal.classList.add('closed');
  };
}
