class AcceptPaymentPage {
  page = document.getElementById('accept-payment-page');
  invoiceAddressInput = document.getElementById('fromAddress');
  invoiceAddressInputJson = document.getElementById('fromAddressJson');
  invoiceAddressRadio = Array.from(document.getElementsByClassName('accept-payment-page__invoice-address-radio'));
  invoiceAddressRadioAnotherAddress = document.getElementById('accept-payment-page__invoice-another-address-radio');
  inputEmail = document.getElementById('accept-payment-page__input-email');
  inputPhone = document.getElementById('accept-payment-page__input-phone');
  acceptPaymentStatusContainer = document.getElementById('accept-payment__status-container');
  qrCodeImage = document.getElementById('accept-payment__qrcode-img');
  bankIDLink = document.getElementById('accept-payment__link');
  altBankIdSignButton = document.getElementById('accept-payment__submit-bankid-link');
  qrCodeBankIdSignButton = document.getElementById('accept-payment__submit-bankid-qrcode');
  successButton = document.getElementById('accept-payment__success-button');
  submitButton = document.getElementById('accept-payment-page__submit-button');
  tryAgainButton = document.getElementById('accept-payment__try-again-button');

  updateQrcodeTimerId = null;
  checkSignBankIdStatusTimerId = null;
  isCheckStatusFetching = false;

  constructor(eventEmitter) {
    Array.from(this.page.getElementsByTagName('input')).forEach(input =>
      input.addEventListener('focus', () => this._removeErrorFromUI([input]))
    );

    this.eventEmitter = eventEmitter;
    this.eventEmitter.on('closeModal', this.handleCloseSignByBankIdModal);
    this.successButton.addEventListener('click', () => {
      window.location.replace('/');
    });
    this.tryAgainButton.addEventListener('click', this.handleSubmitButton);
    this.altBankIdSignButton.addEventListener('click', this.handleSubmitAlternative);
    this.qrCodeBankIdSignButton.addEventListener('click', this.handleSubmitButton);
    this.inputPhone.addEventListener('input', this.autocompletePhone);
    this.submitButton.addEventListener('click', this.handleSubmitButton);
    this.invoiceAddressRadioAnotherAddress.addEventListener('click', this.enableInvoiceAddressInput);
    this.invoiceAddressRadio.forEach(radio => {
      radio.addEventListener('click', this.disableInvoiceAddressInput);
    });
  }

  _hideHTMLNode = nodes => {
    nodes.forEach(node => {
      node.classList.add('hidden');
    });
  };

  _showHTMLNode = nodes => {
    nodes.forEach(node => {
      node.classList.remove('hidden');
    });
  };

  _setStateForBankIdModal = state => {
    if (state === 'loading') {
      this.acceptPaymentStatusContainer.classList.remove('error');
      this.acceptPaymentStatusContainer.classList.remove('wrongSignerName');
      this.acceptPaymentStatusContainer.classList.remove('content');
      this.acceptPaymentStatusContainer.classList.remove('success');
      this.acceptPaymentStatusContainer.classList.remove('duplicateSigningTaskError');
      this.acceptPaymentStatusContainer.classList.add('loading');
    }

    if (state === 'error') {
      this.acceptPaymentStatusContainer.classList.remove('loading');
      this.acceptPaymentStatusContainer.classList.remove('wrongSignerName');
      this.acceptPaymentStatusContainer.classList.remove('content');
      this.acceptPaymentStatusContainer.classList.remove('success');
      this.acceptPaymentStatusContainer.classList.remove('duplicateSigningTaskError');
      this.acceptPaymentStatusContainer.classList.add('error');
    }

    if (state === 'content') {
      this.acceptPaymentStatusContainer.classList.remove('error');
      this.acceptPaymentStatusContainer.classList.remove('wrongSignerName');
      this.acceptPaymentStatusContainer.classList.remove('loading');
      this.acceptPaymentStatusContainer.classList.remove('success');
      this.acceptPaymentStatusContainer.classList.remove('duplicateSigningTaskError');
      this.acceptPaymentStatusContainer.classList.add('content');
    }

    if (state === 'success') {
      this.acceptPaymentStatusContainer.classList.remove('error');
      this.acceptPaymentStatusContainer.classList.remove('wrongSignerName');
      this.acceptPaymentStatusContainer.classList.remove('loading');
      this.acceptPaymentStatusContainer.classList.remove('content');
      this.acceptPaymentStatusContainer.classList.remove('duplicateSigningTaskError');
      this.acceptPaymentStatusContainer.classList.add('success');
    }

    if (state === 'wrongSignerName') {
      this.acceptPaymentStatusContainer.classList.remove('error');
      this.acceptPaymentStatusContainer.classList.remove('loading');
      this.acceptPaymentStatusContainer.classList.remove('content');
      this.acceptPaymentStatusContainer.classList.remove('success');
      this.acceptPaymentStatusContainer.classList.remove('duplicateSigningTaskError');
      this.acceptPaymentStatusContainer.classList.add('wrongSignerName');
    }

    if (state === 'duplicateSigningTaskError') {
      this.acceptPaymentStatusContainer.classList.remove('error');
      this.acceptPaymentStatusContainer.classList.remove('loading');
      this.acceptPaymentStatusContainer.classList.remove('content');
      this.acceptPaymentStatusContainer.classList.remove('success');
      this.acceptPaymentStatusContainer.classList.remove('wrongSignerName');
      this.acceptPaymentStatusContainer.classList.add('duplicateSigningTaskError');
    }
  };

  handleSubmitButton = () => {
    const data = this._getFormValue();

    if (data) {
      this.eventEmitter.emit('openModal');
      // Check screen width to determine default BankID method
      if (window.innerWidth < 768) {
        // Mobile: default to BankID app link
        this._getLink({taskKey: data.taskKey, device: 'SameDevice'});
      } else {
        // Desktop: default to QR code
        this._getQRCode({taskKey: data.taskKey, device: 'OtherDevice'});
      }
    }
  };

  handleSubmitAlternative = () => {
    const data = this._getFormValue();
    if (data) {
      this._getLink({taskKey: data.taskKey, device: 'SameDevice'});
    }
  };

  _getLink = ({taskKey, device, redirectUr}) => {
    const getLinkUrl = '/wp-json/fd-api/v1/task/sign';
    this.isCheckStatusFetching = false;
    this._setUpdateQrCodeTimer(null);
    this._setCheckSignBankIdStatusTimerId(null);
    this._setStateForBankIdModal('loading');

    const onSuccess = data => {
      this._setQrCode('');
      this._hideHTMLNode([this.qrCodeImage, this.altBankIdSignButton]);
      this._showHTMLNode([this.bankIDLink, this.qrCodeBankIdSignButton]);
      this._setBankIDLink(data.launchUrl);
      this._setStateForBankIdModal('content');

      const checkSignStatusTimerID = setInterval(() => {
        this._checkSignByBankIdStatus({taskKey, protectedRefId: data.protectedRefId});
      }, 2000);

      this._setCheckSignBankIdStatusTimerId(checkSignStatusTimerID);
    };

    const onError = error => {
      this._setStateForBankIdModal('error');
    };

    AjaxRequestService.sendPostRequest({taskKey, device}, onSuccess, onError, getLinkUrl);
  };

  _getQRCode = ({taskKey, device, redirectUrl}) => {
    const getQRCodeUrl = '/wp-json/fd-api/v1/task/sign';
    this._setUpdateQrCodeTimer(null);
    this._setCheckSignBankIdStatusTimerId(null);
    this.isCheckStatusFetching = false;
    this._setStateForBankIdModal('loading');

    const onSuccess = ({qrCodeAsBase64, protectedRefId}) => {
      this._showHTMLNode([this.qrCodeImage, this.altBankIdSignButton]);
      this._hideHTMLNode([this.bankIDLink, this.qrCodeBankIdSignButton]);
      this._setQrCode(qrCodeAsBase64);
      this._setStateForBankIdModal('content');

      const updateQrCodeTimerID = setInterval(() => {
        this._updateQRCode({taskKey, protectedRefId});
      }, 1000);

      const checkSignStatusTimerID = setInterval(() => {
        this._checkSignByBankIdStatus({taskKey, protectedRefId});
      }, 2000);

      this._setCheckSignBankIdStatusTimerId(checkSignStatusTimerID);
      this._setUpdateQrCodeTimer(updateQrCodeTimerID);
    };

    const onError = error => {
      this._setStateForBankIdModal('error');
    };

    AjaxRequestService.sendPostRequest({taskKey, device, redirectUrl}, onSuccess, onError, getQRCodeUrl);
  };

  _updateQRCode = ({taskKey, protectedRefId}) => {
    const updateQRCodeUrl = '/wp-json/fd-api/v1/task/update-sign-qr';

    const onSuccess = ({qrCodeAsBase64}) => {
      this._setQrCode(qrCodeAsBase64);
    };

    AjaxRequestService.sendPostRequest({taskKey, protectedRefId}, onSuccess, () => null, updateQRCodeUrl);
  };

  _checkSignByBankIdStatus = ({taskKey, protectedRefId}) => {
    const getSignByBankIdStatusUrl = '/wp-json/fd-api/v1/task/check-sign-status';

    const onSuccess = data => {
      if (data.status === 'Pending') {
        this._checkSignByBankIdStatus({taskKey, protectedRefId});
      }

      if (data.status === 'Completed') {
        this._setUpdateQrCodeTimer(null);
        this._setCheckSignBankIdStatusTimerId(null);
        this.acceptPayment();
      }

      if (data.status === 'WrongSignerName') {
        this._setUpdateQrCodeTimer(null);
        this._setCheckSignBankIdStatusTimerId(null);
        this.isCheckStatusFetching = false;
        this._setStateForBankIdModal('wrongSignerName');
      }

      this.isCheckStatusFetching = false;
    };

    const onError = error => {
      this._setUpdateQrCodeTimer(null);
      this._setCheckSignBankIdStatusTimerId(null);
      this.isCheckStatusFetching = false;
      this._setStateForBankIdModal('error');
      alert(error);
    };

    if (!this.isCheckStatusFetching) {
      const data = this._getFormValue();
      this.isCheckStatusFetching = true;
      AjaxRequestService.sendPostRequest({signerName: data.name, taskKey, protectedRefId}, onSuccess, onError, getSignByBankIdStatusUrl);
    }
  };

  _setQrCode = imageBase64 => {
    this.qrCodeImage.src = `data:image/png;base64,${imageBase64}`;
  };

  _setBankIDLink = launchUrl => {
    this.bankIDLink.href = launchUrl;
  };

  handleCloseSignByBankIdModal = () => {
    this._setUpdateQrCodeTimer(null);
    this._setCheckSignBankIdStatusTimerId(null);
  };

  acceptPayment = () => {
    const data = this._getFormValue();
    const acceptPaymentUrl = '/wp-json/fd-api/v1/task/accept-price';

    if (data) {
      this._setStateForBankIdModal('loading');

      const onSuccess = ({paymentUrl}) => {
        this._setStateForBankIdModal('success');
        // window.location.replace(paymentUrl.replace(/\\/g, ''));
      };

      const onError = error => {
        if (error.includes('Reason: Invalid action (AcceptPrice)')) {
          this._setStateForBankIdModal('duplicateSigningTaskError');
        } else {
          this._setStateForBankIdModal('error');
        }
      };

      AjaxRequestService.sendPostAjaxRequest(data, onSuccess, onError, acceptPaymentUrl);
    }
  };

  disableInvoiceAddressInput = () => {
    this.invoiceAddressInput.setAttribute('disabled', '');
    this.invoiceAddressInput.removeAttribute('required');
    this.invoiceAddressInputJson.removeAttribute('required');
  };

  enableInvoiceAddressInput = () => {
    this.invoiceAddressInput.removeAttribute('disabled');
    this.invoiceAddressInput.setAttribute('required', '');
    this.invoiceAddressInputJson.setAttribute('required', '');
  };

  _getFormValue = () => {
    const inputs = Array.from(this.page.getElementsByTagName('input'));

    const isValid = this._validateFields(inputs);

    if (isValid) {
      const inputValues = inputs.reduce((acc, curr) => {
        if (curr.type === 'checkbox') {
          return {...acc, [curr.name]: curr.checked ? [...(acc[curr.name] || []), curr.value] : [...(acc[curr.name] || [])]};
        }
        if (curr.type === 'radio') {
          return curr.checked ? {...acc, [curr.name]: curr.value} : {...acc};
        }
        return curr.value ? {...acc, [curr.name]: curr.value} : {...acc};
      }, {});

      const values = {...inputValues};

      const urlParams = new URLSearchParams(window.location.search);
      const taskKey = urlParams.get('taskKey');

      const responseData = {
        taskKey,
        name: values.name.trim(),
        email: values.email,
        phone: values.phone,
        invoiceAddress: values.invoiceAddress || values.fromAddress,
        offerPrice: values.offerPrice
      };

      return responseData;
    }
  };

  _validateFields = inputs => {
    const requiredInputs = inputs.filter(input => input.required);
    const isInputsFilled = requiredInputs.every(input => (input.type === 'checkbox' ? input.checked : !!input.value.trim()));

    if (!isInputsFilled) {
      this._setErrorInUI(requiredInputs.filter(input => (input.type === 'checkbox' ? !input.checked : !input.value.trim())));
    }

    const addressInputs = requiredInputs.filter(input => input.name === 'fromAddressJson' && input.required);
    let isAddressValid = true;
    if (isInputsFilled && !!addressInputs.length) {
      const address = {...JSON.parse(addressInputs[0].value)};
      const invalidFields = Object.keys(address).reduce((acc, curr) => {
        if (!!address[curr]) {
          return acc;
        }
        return [...acc, curr];
      }, []);

      isAddressValid = !invalidFields.length;

      if (!isAddressValid) {
        const nodes = Array.from(document.getElementsByClassName('request-form__field-error'));
        this._renderErrorText(nodes, `Please fill: ${invalidFields.join(', ')}`);
        this._setErrorInUI(requiredInputs.filter(input => input.name === 'fromAddress'));
      }
    }

    const isEmailValid = ValidationService.validateEmail(this.inputEmail.value);
    if (!isEmailValid) {
      this._setErrorInUI([this.inputEmail]);
      return false;
    }

    const isPhoneValid = ValidationService.validatePhoneNumber(this.inputPhone.value);
    if (!isPhoneValid) {
      this._setErrorInUI([this.inputPhone]);
      return false;
    }

    return isInputsFilled && isAddressValid;
  };

  _setErrorInUI = inputs => {
    inputs.forEach(input => {
      input.classList.add('error');
    });

    setTimeout(() => {
      inputs.forEach(input => {
        input.classList.remove('error');
      });
    }, 4000);
  };

  _removeErrorFromUI = inputs => {
    inputs.forEach(input => {
      input.classList.remove('error');
    });
  };

  _renderErrorText = (nodes, message) => {
    nodes.forEach(node => {
      node.innerHTML = message;
      node.classList.remove('invisible');
    });

    setTimeout(() => {
      nodes.forEach(node => {
        // node.innerHTML = '';
        node.classList.add('invisible');
      });
    }, 4000);
  };

  _setLoadingInButtonsUI = buttons => {
    buttons.forEach(button => {
      button.disabled = true;
      button.classList.add('loading');
    });
  };

  _removeLoadingInButtonsUI = buttons => {
    buttons.forEach(button => {
      button.disabled = false;
      button.classList.remove('loading');
    });
  };

  _showError(error) {
    alert(error);
  }

  _setUpdateQrCodeTimer(timer) {
    clearTimeout(this.updateQrcodeTimerId);
    this.updateQrcodeTimerId = timer;
  }

  _setCheckSignBankIdStatusTimerId(timer) {
    clearTimeout(this.checkSignBankIdStatusTimerId);
    this.checkSignBankIdStatusTimerId = timer;
  }

  autocompletePhone = event => {
    let inputValue;
    inputValue = event.target.value.replace(/\D/g, '');
    event.target.value = inputValue;
  };
}
